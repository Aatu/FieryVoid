(function(Eg){typeof define=="function"&&define.amd?define(Eg):Eg()})(function(){"use strict";function Eg(o){return o&&o.__esModule&&Object.prototype.hasOwnProperty.call(o,"default")?o.default:o}var tx={exports:{}},ap={},nx={exports:{}},Lt={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var IS;function cD(){if(IS)return Lt;IS=1;var o=Symbol.for("react.element"),r=Symbol.for("react.portal"),s=Symbol.for("react.fragment"),d=Symbol.for("react.strict_mode"),g=Symbol.for("react.profiler"),b=Symbol.for("react.provider"),S=Symbol.for("react.context"),y=Symbol.for("react.forward_ref"),E=Symbol.for("react.suspense"),O=Symbol.for("react.memo"),$=Symbol.for("react.lazy"),P=Symbol.iterator;function j(B){return B===null||typeof B!="object"?null:(B=P&&B[P]||B["@@iterator"],typeof B=="function"?B:null)}var H={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},L=Object.assign,Q={};function xe(B,re,Ve){this.props=B,this.context=re,this.refs=Q,this.updater=Ve||H}xe.prototype.isReactComponent={},xe.prototype.setState=function(B,re){if(typeof B!="object"&&typeof B!="function"&&B!=null)throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,B,re,"setState")},xe.prototype.forceUpdate=function(B){this.updater.enqueueForceUpdate(this,B,"forceUpdate")};function ze(){}ze.prototype=xe.prototype;function fe(B,re,Ve){this.props=B,this.context=re,this.refs=Q,this.updater=Ve||H}var ae=fe.prototype=new ze;ae.constructor=fe,L(ae,xe.prototype),ae.isPureReactComponent=!0;var ce=Array.isArray,Ee=Object.prototype.hasOwnProperty,le={current:null},ue={key:!0,ref:!0,__self:!0,__source:!0};function $e(B,re,Ve){var et,it={},ht=null,Ot=null;if(re!=null)for(et in re.ref!==void 0&&(Ot=re.ref),re.key!==void 0&&(ht=""+re.key),re)Ee.call(re,et)&&!ue.hasOwnProperty(et)&&(it[et]=re[et]);var tt=arguments.length-2;if(tt===1)it.children=Ve;else if(1<tt){for(var vt=Array(tt),Ut=0;Ut<tt;Ut++)vt[Ut]=arguments[Ut+2];it.children=vt}if(B&&B.defaultProps)for(et in tt=B.defaultProps,tt)it[et]===void 0&&(it[et]=tt[et]);return{$$typeof:o,type:B,key:ht,ref:Ot,props:it,_owner:le.current}}function ft(B,re){return{$$typeof:o,type:B.type,key:re,ref:B.ref,props:B.props,_owner:B._owner}}function He(B){return typeof B=="object"&&B!==null&&B.$$typeof===o}function Tt(B){var re={"=":"=0",":":"=2"};return"$"+B.replace(/[=:]/g,function(Ve){return re[Ve]})}var bt=/\/+/g;function rt(B,re){return typeof B=="object"&&B!==null&&B.key!=null?Tt(""+B.key):re.toString(36)}function Be(B,re,Ve,et,it){var ht=typeof B;(ht==="undefined"||ht==="boolean")&&(B=null);var Ot=!1;if(B===null)Ot=!0;else switch(ht){case"string":case"number":Ot=!0;break;case"object":switch(B.$$typeof){case o:case r:Ot=!0}}if(Ot)return Ot=B,it=it(Ot),B=et===""?"."+rt(Ot,0):et,ce(it)?(Ve="",B!=null&&(Ve=B.replace(bt,"$&/")+"/"),Be(it,re,Ve,"",function(Ut){return Ut})):it!=null&&(He(it)&&(it=ft(it,Ve+(!it.key||Ot&&Ot.key===it.key?"":(""+it.key).replace(bt,"$&/")+"/")+B)),re.push(it)),1;if(Ot=0,et=et===""?".":et+":",ce(B))for(var tt=0;tt<B.length;tt++){ht=B[tt];var vt=et+rt(ht,tt);Ot+=Be(ht,re,Ve,vt,it)}else if(vt=j(B),typeof vt=="function")for(B=vt.call(B),tt=0;!(ht=B.next()).done;)ht=ht.value,vt=et+rt(ht,tt++),Ot+=Be(ht,re,Ve,vt,it);else if(ht==="object")throw re=String(B),Error("Objects are not valid as a React child (found: "+(re==="[object Object]"?"object with keys {"+Object.keys(B).join(", ")+"}":re)+"). If you meant to render a collection of children, use an array instead.");return Ot}function Ht(B,re,Ve){if(B==null)return B;var et=[],it=0;return Be(B,et,"","",function(ht){return re.call(Ve,ht,it++)}),et}function kt(B){if(B._status===-1){var re=B._result;re=re(),re.then(function(Ve){(B._status===0||B._status===-1)&&(B._status=1,B._result=Ve)},function(Ve){(B._status===0||B._status===-1)&&(B._status=2,B._result=Ve)}),B._status===-1&&(B._status=0,B._result=re)}if(B._status===1)return B._result.default;throw B._result}var pt={current:null},se={transition:null},ke={ReactCurrentDispatcher:pt,ReactCurrentBatchConfig:se,ReactCurrentOwner:le};function be(){throw Error("act(...) is not supported in production builds of React.")}return Lt.Children={map:Ht,forEach:function(B,re,Ve){Ht(B,function(){re.apply(this,arguments)},Ve)},count:function(B){var re=0;return Ht(B,function(){re++}),re},toArray:function(B){return Ht(B,function(re){return re})||[]},only:function(B){if(!He(B))throw Error("React.Children.only expected to receive a single React element child.");return B}},Lt.Component=xe,Lt.Fragment=s,Lt.Profiler=g,Lt.PureComponent=fe,Lt.StrictMode=d,Lt.Suspense=E,Lt.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=ke,Lt.act=be,Lt.cloneElement=function(B,re,Ve){if(B==null)throw Error("React.cloneElement(...): The argument must be a React element, but you passed "+B+".");var et=L({},B.props),it=B.key,ht=B.ref,Ot=B._owner;if(re!=null){if(re.ref!==void 0&&(ht=re.ref,Ot=le.current),re.key!==void 0&&(it=""+re.key),B.type&&B.type.defaultProps)var tt=B.type.defaultProps;for(vt in re)Ee.call(re,vt)&&!ue.hasOwnProperty(vt)&&(et[vt]=re[vt]===void 0&&tt!==void 0?tt[vt]:re[vt])}var vt=arguments.length-2;if(vt===1)et.children=Ve;else if(1<vt){tt=Array(vt);for(var Ut=0;Ut<vt;Ut++)tt[Ut]=arguments[Ut+2];et.children=tt}return{$$typeof:o,type:B.type,key:it,ref:ht,props:et,_owner:Ot}},Lt.createContext=function(B){return B={$$typeof:S,_currentValue:B,_currentValue2:B,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null},B.Provider={$$typeof:b,_context:B},B.Consumer=B},Lt.createElement=$e,Lt.createFactory=function(B){var re=$e.bind(null,B);return re.type=B,re},Lt.createRef=function(){return{current:null}},Lt.forwardRef=function(B){return{$$typeof:y,render:B}},Lt.isValidElement=He,Lt.lazy=function(B){return{$$typeof:$,_payload:{_status:-1,_result:B},_init:kt}},Lt.memo=function(B,re){return{$$typeof:O,type:B,compare:re===void 0?null:re}},Lt.startTransition=function(B){var re=se.transition;se.transition={};try{B()}finally{se.transition=re}},Lt.unstable_act=be,Lt.useCallback=function(B,re){return pt.current.useCallback(B,re)},Lt.useContext=function(B){return pt.current.useContext(B)},Lt.useDebugValue=function(){},Lt.useDeferredValue=function(B){return pt.current.useDeferredValue(B)},Lt.useEffect=function(B,re){return pt.current.useEffect(B,re)},Lt.useId=function(){return pt.current.useId()},Lt.useImperativeHandle=function(B,re,Ve){return pt.current.useImperativeHandle(B,re,Ve)},Lt.useInsertionEffect=function(B,re){return pt.current.useInsertionEffect(B,re)},Lt.useLayoutEffect=function(B,re){return pt.current.useLayoutEffect(B,re)},Lt.useMemo=function(B,re){return pt.current.useMemo(B,re)},Lt.useReducer=function(B,re,Ve){return pt.current.useReducer(B,re,Ve)},Lt.useRef=function(B){return pt.current.useRef(B)},Lt.useState=function(B){return pt.current.useState(B)},Lt.useSyncExternalStore=function(B,re,Ve){return pt.current.useSyncExternalStore(B,re,Ve)},Lt.useTransition=function(){return pt.current.useTransition()},Lt.version="18.3.1",Lt}var op={exports:{}};op.exports;var US;function dD(){return US||(US=1,function(o,r){var s={};/**
 * @license React
 * react.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */s.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var d="18.3.1",g=Symbol.for("react.element"),b=Symbol.for("react.portal"),S=Symbol.for("react.fragment"),y=Symbol.for("react.strict_mode"),E=Symbol.for("react.profiler"),O=Symbol.for("react.provider"),$=Symbol.for("react.context"),P=Symbol.for("react.forward_ref"),j=Symbol.for("react.suspense"),H=Symbol.for("react.suspense_list"),L=Symbol.for("react.memo"),Q=Symbol.for("react.lazy"),xe=Symbol.for("react.offscreen"),ze=Symbol.iterator,fe="@@iterator";function ae(T){if(T===null||typeof T!="object")return null;var _=ze&&T[ze]||T[fe];return typeof _=="function"?_:null}var ce={current:null},Ee={transition:null},le={current:null,isBatchingLegacy:!1,didScheduleLegacyUpdate:!1},ue={current:null},$e={},ft=null;function He(T){ft=T}$e.setExtraStackFrame=function(T){ft=T},$e.getCurrentStack=null,$e.getStackAddendum=function(){var T="";ft&&(T+=ft);var _=$e.getCurrentStack;return _&&(T+=_()||""),T};var Tt=!1,bt=!1,rt=!1,Be=!1,Ht=!1,kt={ReactCurrentDispatcher:ce,ReactCurrentBatchConfig:Ee,ReactCurrentOwner:ue};kt.ReactDebugCurrentFrame=$e,kt.ReactCurrentActQueue=le;function pt(T){{for(var _=arguments.length,q=new Array(_>1?_-1:0),ee=1;ee<_;ee++)q[ee-1]=arguments[ee];ke("warn",T,q)}}function se(T){{for(var _=arguments.length,q=new Array(_>1?_-1:0),ee=1;ee<_;ee++)q[ee-1]=arguments[ee];ke("error",T,q)}}function ke(T,_,q){{var ee=kt.ReactDebugCurrentFrame,ye=ee.getStackAddendum();ye!==""&&(_+="%s",q=q.concat([ye]));var Pe=q.map(function(Ae){return String(Ae)});Pe.unshift("Warning: "+_),Function.prototype.apply.call(console[T],console,Pe)}}var be={};function B(T,_){{var q=T.constructor,ee=q&&(q.displayName||q.name)||"ReactClass",ye=ee+"."+_;if(be[ye])return;se("Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",_,ee),be[ye]=!0}}var re={isMounted:function(T){return!1},enqueueForceUpdate:function(T,_,q){B(T,"forceUpdate")},enqueueReplaceState:function(T,_,q,ee){B(T,"replaceState")},enqueueSetState:function(T,_,q,ee){B(T,"setState")}},Ve=Object.assign,et={};Object.freeze(et);function it(T,_,q){this.props=T,this.context=_,this.refs=et,this.updater=q||re}it.prototype.isReactComponent={},it.prototype.setState=function(T,_){if(typeof T!="object"&&typeof T!="function"&&T!=null)throw new Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,T,_,"setState")},it.prototype.forceUpdate=function(T){this.updater.enqueueForceUpdate(this,T,"forceUpdate")};{var ht={isMounted:["isMounted","Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."],replaceState:["replaceState","Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."]},Ot=function(T,_){Object.defineProperty(it.prototype,T,{get:function(){pt("%s(...) is deprecated in plain JavaScript React classes. %s",_[0],_[1])}})};for(var tt in ht)ht.hasOwnProperty(tt)&&Ot(tt,ht[tt])}function vt(){}vt.prototype=it.prototype;function Ut(T,_,q){this.props=T,this.context=_,this.refs=et,this.updater=q||re}var pn=Ut.prototype=new vt;pn.constructor=Ut,Ve(pn,it.prototype),pn.isPureReactComponent=!0;function an(){var T={current:null};return Object.seal(T),T}var Pn=Array.isArray;function xn(T){return Pn(T)}function An(T){{var _=typeof Symbol=="function"&&Symbol.toStringTag,q=_&&T[Symbol.toStringTag]||T.constructor.name||"Object";return q}}function tr(T){try{return Gn(T),!1}catch{return!0}}function Gn(T){return""+T}function Ri(T){if(tr(T))return se("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",An(T)),Gn(T)}function ha(T,_,q){var ee=T.displayName;if(ee)return ee;var ye=_.displayName||_.name||"";return ye!==""?q+"("+ye+")":q}function Hr(T){return T.displayName||"Context"}function nr(T){if(T==null)return null;if(typeof T.tag=="number"&&se("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof T=="function")return T.displayName||T.name||null;if(typeof T=="string")return T;switch(T){case S:return"Fragment";case b:return"Portal";case E:return"Profiler";case y:return"StrictMode";case j:return"Suspense";case H:return"SuspenseList"}if(typeof T=="object")switch(T.$$typeof){case $:var _=T;return Hr(_)+".Consumer";case O:var q=T;return Hr(q._context)+".Provider";case P:return ha(T,T.render,"ForwardRef");case L:var ee=T.displayName||null;return ee!==null?ee:nr(T.type)||"Memo";case Q:{var ye=T,Pe=ye._payload,Ae=ye._init;try{return nr(Ae(Pe))}catch{return null}}}return null}var ur=Object.prototype.hasOwnProperty,cr={key:!0,ref:!0,__self:!0,__source:!0},_r,ga,Kn;Kn={};function xr(T){if(ur.call(T,"ref")){var _=Object.getOwnPropertyDescriptor(T,"ref").get;if(_&&_.isReactWarning)return!1}return T.ref!==void 0}function oi(T){if(ur.call(T,"key")){var _=Object.getOwnPropertyDescriptor(T,"key").get;if(_&&_.isReactWarning)return!1}return T.key!==void 0}function ro(T,_){var q=function(){_r||(_r=!0,se("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",_))};q.isReactWarning=!0,Object.defineProperty(T,"key",{get:q,configurable:!0})}function Di(T,_){var q=function(){ga||(ga=!0,se("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",_))};q.isReactWarning=!0,Object.defineProperty(T,"ref",{get:q,configurable:!0})}function we(T){if(typeof T.ref=="string"&&ue.current&&T.__self&&ue.current.stateNode!==T.__self){var _=nr(ue.current.type);Kn[_]||(se('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. This case cannot be automatically converted to an arrow function. We ask you to manually fix this case by using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',_,T.ref),Kn[_]=!0)}}var Qe=function(T,_,q,ee,ye,Pe,Ae){var at={$$typeof:g,type:T,key:_,ref:q,props:Ae,_owner:Pe};return at._store={},Object.defineProperty(at._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(at,"_self",{configurable:!1,enumerable:!1,writable:!1,value:ee}),Object.defineProperty(at,"_source",{configurable:!1,enumerable:!1,writable:!1,value:ye}),Object.freeze&&(Object.freeze(at.props),Object.freeze(at)),at};function wt(T,_,q){var ee,ye={},Pe=null,Ae=null,at=null,Ct=null;if(_!=null){xr(_)&&(Ae=_.ref,we(_)),oi(_)&&(Ri(_.key),Pe=""+_.key),at=_.__self===void 0?null:_.__self,Ct=_.__source===void 0?null:_.__source;for(ee in _)ur.call(_,ee)&&!cr.hasOwnProperty(ee)&&(ye[ee]=_[ee])}var qt=arguments.length-2;if(qt===1)ye.children=q;else if(qt>1){for(var sn=Array(qt),un=0;un<qt;un++)sn[un]=arguments[un+2];Object.freeze&&Object.freeze(sn),ye.children=sn}if(T&&T.defaultProps){var yt=T.defaultProps;for(ee in yt)ye[ee]===void 0&&(ye[ee]=yt[ee])}if(Pe||Ae){var hn=typeof T=="function"?T.displayName||T.name||"Unknown":T;Pe&&ro(ye,hn),Ae&&Di(ye,hn)}return Qe(T,Pe,Ae,at,Ct,ue.current,ye)}function Gt(T,_){var q=Qe(T.type,_,T.ref,T._self,T._source,T._owner,T.props);return q}function bn(T,_,q){if(T==null)throw new Error("React.cloneElement(...): The argument must be a React element, but you passed "+T+".");var ee,ye=Ve({},T.props),Pe=T.key,Ae=T.ref,at=T._self,Ct=T._source,qt=T._owner;if(_!=null){xr(_)&&(Ae=_.ref,qt=ue.current),oi(_)&&(Ri(_.key),Pe=""+_.key);var sn;T.type&&T.type.defaultProps&&(sn=T.type.defaultProps);for(ee in _)ur.call(_,ee)&&!cr.hasOwnProperty(ee)&&(_[ee]===void 0&&sn!==void 0?ye[ee]=sn[ee]:ye[ee]=_[ee])}var un=arguments.length-2;if(un===1)ye.children=q;else if(un>1){for(var yt=Array(un),hn=0;hn<un;hn++)yt[hn]=arguments[hn+2];ye.children=yt}return Qe(T.type,Pe,Ae,at,Ct,qt,ye)}function wn(T){return typeof T=="object"&&T!==null&&T.$$typeof===g}var Sn=".",dr=":";function vn(T){var _=/[=:]/g,q={"=":"=0",":":"=2"},ee=T.replace(_,function(ye){return q[ye]});return"$"+ee}var on=!1,Kt=/\/+/g;function Mi(T){return T.replace(Kt,"$&/")}function qi(T,_){return typeof T=="object"&&T!==null&&T.key!=null?(Ri(T.key),vn(""+T.key)):_.toString(36)}function Xi(T,_,q,ee,ye){var Pe=typeof T;(Pe==="undefined"||Pe==="boolean")&&(T=null);var Ae=!1;if(T===null)Ae=!0;else switch(Pe){case"string":case"number":Ae=!0;break;case"object":switch(T.$$typeof){case g:case b:Ae=!0}}if(Ae){var at=T,Ct=ye(at),qt=ee===""?Sn+qi(at,0):ee;if(xn(Ct)){var sn="";qt!=null&&(sn=Mi(qt)+"/"),Xi(Ct,_,sn,"",function(Tp){return Tp})}else Ct!=null&&(wn(Ct)&&(Ct.key&&(!at||at.key!==Ct.key)&&Ri(Ct.key),Ct=Gt(Ct,q+(Ct.key&&(!at||at.key!==Ct.key)?Mi(""+Ct.key)+"/":"")+qt)),_.push(Ct));return 1}var un,yt,hn=0,In=ee===""?Sn:ee+dr;if(xn(T))for(var Ml=0;Ml<T.length;Ml++)un=T[Ml],yt=In+qi(un,Ml),hn+=Xi(un,_,q,yt,ye);else{var Hu=ae(T);if(typeof Hu=="function"){var ho=T;Hu===ho.entries&&(on||pt("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),on=!0);for(var Ol=Hu.call(ho),Vu,Ep=0;!(Vu=Ol.next()).done;)un=Vu.value,yt=In+qi(un,Ep++),hn+=Xi(un,_,q,yt,ye)}else if(Pe==="object"){var xd=String(T);throw new Error("Objects are not valid as a React child (found: "+(xd==="[object Object]"?"object with keys {"+Object.keys(T).join(", ")+"}":xd)+"). If you meant to render a collection of children, use an array instead.")}}return hn}function io(T,_,q){if(T==null)return T;var ee=[],ye=0;return Xi(T,ee,"","",function(Pe){return _.call(q,Pe,ye++)}),ee}function wl(T){var _=0;return io(T,function(){_++}),_}function Sl(T,_,q){io(T,function(){_.apply(this,arguments)},q)}function ao(T){return io(T,function(_){return _})||[]}function Cl(T){if(!wn(T))throw new Error("React.Children.only expected to receive a single React element child.");return T}function ja(T){var _={$$typeof:$,_currentValue:T,_currentValue2:T,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null};_.Provider={$$typeof:O,_context:_};var q=!1,ee=!1,ye=!1;{var Pe={$$typeof:$,_context:_};Object.defineProperties(Pe,{Provider:{get:function(){return ee||(ee=!0,se("Rendering <Context.Consumer.Provider> is not supported and will be removed in a future major release. Did you mean to render <Context.Provider> instead?")),_.Provider},set:function(Ae){_.Provider=Ae}},_currentValue:{get:function(){return _._currentValue},set:function(Ae){_._currentValue=Ae}},_currentValue2:{get:function(){return _._currentValue2},set:function(Ae){_._currentValue2=Ae}},_threadCount:{get:function(){return _._threadCount},set:function(Ae){_._threadCount=Ae}},Consumer:{get:function(){return q||(q=!0,se("Rendering <Context.Consumer.Consumer> is not supported and will be removed in a future major release. Did you mean to render <Context.Consumer> instead?")),_.Consumer}},displayName:{get:function(){return _.displayName},set:function(Ae){ye||(pt("Setting `displayName` on Context.Consumer has no effect. You should set it directly on the context with Context.displayName = '%s'.",Ae),ye=!0)}}}),_.Consumer=Pe}return _._currentRenderer=null,_._currentRenderer2=null,_}var Oi=-1,br=0,$i=1,li=2;function _a(T){if(T._status===Oi){var _=T._result,q=_();if(q.then(function(Pe){if(T._status===br||T._status===Oi){var Ae=T;Ae._status=$i,Ae._result=Pe}},function(Pe){if(T._status===br||T._status===Oi){var Ae=T;Ae._status=li,Ae._result=Pe}}),T._status===Oi){var ee=T;ee._status=br,ee._result=q}}if(T._status===$i){var ye=T._result;return ye===void 0&&se(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))

Did you accidentally put curly braces around the import?`,ye),"default"in ye||se(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))`,ye),ye.default}else throw T._result}function La(T){var _={_status:Oi,_result:T},q={$$typeof:Q,_payload:_,_init:_a};{var ee,ye;Object.defineProperties(q,{defaultProps:{configurable:!0,get:function(){return ee},set:function(Pe){se("React.lazy(...): It is not supported to assign `defaultProps` to a lazy component import. Either specify them where the component is defined, or create a wrapping component around it."),ee=Pe,Object.defineProperty(q,"defaultProps",{enumerable:!0})}},propTypes:{configurable:!0,get:function(){return ye},set:function(Pe){se("React.lazy(...): It is not supported to assign `propTypes` to a lazy component import. Either specify them where the component is defined, or create a wrapping component around it."),ye=Pe,Object.defineProperty(q,"propTypes",{enumerable:!0})}}})}return q}function oo(T){T!=null&&T.$$typeof===L?se("forwardRef requires a render function but received a `memo` component. Instead of forwardRef(memo(...)), use memo(forwardRef(...))."):typeof T!="function"?se("forwardRef requires a render function but was given %s.",T===null?"null":typeof T):T.length!==0&&T.length!==2&&se("forwardRef render functions accept exactly two parameters: props and ref. %s",T.length===1?"Did you forget to use the ref parameter?":"Any additional parameter will be undefined."),T!=null&&(T.defaultProps!=null||T.propTypes!=null)&&se("forwardRef render functions do not support propTypes or defaultProps. Did you accidentally pass a React component?");var _={$$typeof:P,render:T};{var q;Object.defineProperty(_,"displayName",{enumerable:!1,configurable:!0,get:function(){return q},set:function(ee){q=ee,!T.name&&!T.displayName&&(T.displayName=ee)}})}return _}var z;z=Symbol.for("react.module.reference");function de(T){return!!(typeof T=="string"||typeof T=="function"||T===S||T===E||Ht||T===y||T===j||T===H||Be||T===xe||Tt||bt||rt||typeof T=="object"&&T!==null&&(T.$$typeof===Q||T.$$typeof===L||T.$$typeof===O||T.$$typeof===$||T.$$typeof===P||T.$$typeof===z||T.getModuleId!==void 0))}function Te(T,_){de(T)||se("memo: The first argument must be a component. Instead received: %s",T===null?"null":typeof T);var q={$$typeof:L,type:T,compare:_===void 0?null:_};{var ee;Object.defineProperty(q,"displayName",{enumerable:!1,configurable:!0,get:function(){return ee},set:function(ye){ee=ye,!T.name&&!T.displayName&&(T.displayName=ye)}})}return q}function De(){var T=ce.current;return T===null&&se(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://reactjs.org/link/invalid-hook-call for tips about how to debug and fix this problem.`),T}function Rt(T){var _=De();if(T._context!==void 0){var q=T._context;q.Consumer===T?se("Calling useContext(Context.Consumer) is not supported, may cause bugs, and will be removed in a future major release. Did you mean to call useContext(Context) instead?"):q.Provider===T&&se("Calling useContext(Context.Provider) is not supported. Did you mean to call useContext(Context) instead?")}return _.useContext(T)}function ut(T){var _=De();return _.useState(T)}function $t(T,_,q){var ee=De();return ee.useReducer(T,_,q)}function St(T){var _=De();return _.useRef(T)}function Fn(T,_){var q=De();return q.useEffect(T,_)}function yn(T,_){var q=De();return q.useInsertionEffect(T,_)}function Cn(T,_){var q=De();return q.useLayoutEffect(T,_)}function Lr(T,_){var q=De();return q.useCallback(T,_)}function ma(T,_){var q=De();return q.useMemo(T,_)}function Qt(T,_,q){var ee=De();return ee.useImperativeHandle(T,_,q)}function kn(T,_){{var q=De();return q.useDebugValue(T,_)}}function gt(){var T=De();return T.useTransition()}function za(T){var _=De();return _.useDeferredValue(T)}function lo(){var T=De();return T.useId()}function gd(T,_,q){var ee=De();return ee.useSyncExternalStore(T,_,q)}var so=0,_o,si,Nu,Vr,Pu,md,vd;function uo(){}uo.__reactDisabledLog=!0;function Lo(){{if(so===0){_o=console.log,si=console.info,Nu=console.warn,Vr=console.error,Pu=console.group,md=console.groupCollapsed,vd=console.groupEnd;var T={configurable:!0,enumerable:!0,value:uo,writable:!0};Object.defineProperties(console,{info:T,log:T,warn:T,error:T,group:T,groupCollapsed:T,groupEnd:T})}so++}}function ui(){{if(so--,so===0){var T={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:Ve({},T,{value:_o}),info:Ve({},T,{value:si}),warn:Ve({},T,{value:Nu}),error:Ve({},T,{value:Vr}),group:Ve({},T,{value:Pu}),groupCollapsed:Ve({},T,{value:md}),groupEnd:Ve({},T,{value:vd})})}so<0&&se("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var Na=kt.ReactCurrentDispatcher,zo;function Cs(T,_,q){{if(zo===void 0)try{throw Error()}catch(ye){var ee=ye.stack.trim().match(/\n( *(at )?)/);zo=ee&&ee[1]||""}return`
`+zo+T}}var co=!1,El;{var Tl=typeof WeakMap=="function"?WeakMap:Map;El=new Tl}function No(T,_){if(!T||co)return"";{var q=El.get(T);if(q!==void 0)return q}var ee;co=!0;var ye=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var Pe;Pe=Na.current,Na.current=null,Lo();try{if(_){var Ae=function(){throw Error()};if(Object.defineProperty(Ae.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(Ae,[])}catch(In){ee=In}Reflect.construct(T,[],Ae)}else{try{Ae.call()}catch(In){ee=In}T.call(Ae.prototype)}}else{try{throw Error()}catch(In){ee=In}T()}}catch(In){if(In&&ee&&typeof In.stack=="string"){for(var at=In.stack.split(`
`),Ct=ee.stack.split(`
`),qt=at.length-1,sn=Ct.length-1;qt>=1&&sn>=0&&at[qt]!==Ct[sn];)sn--;for(;qt>=1&&sn>=0;qt--,sn--)if(at[qt]!==Ct[sn]){if(qt!==1||sn!==1)do if(qt--,sn--,sn<0||at[qt]!==Ct[sn]){var un=`
`+at[qt].replace(" at new "," at ");return T.displayName&&un.includes("<anonymous>")&&(un=un.replace("<anonymous>",T.displayName)),typeof T=="function"&&El.set(T,un),un}while(qt>=1&&sn>=0);break}}}finally{co=!1,Na.current=Pe,ui(),Error.prepareStackTrace=ye}var yt=T?T.displayName||T.name:"",hn=yt?Cs(yt):"";return typeof T=="function"&&El.set(T,hn),hn}function Fu(T,_,q){return No(T,!1)}function Iu(T){var _=T.prototype;return!!(_&&_.isReactComponent)}function Nt(T,_,q){if(T==null)return"";if(typeof T=="function")return No(T,Iu(T));if(typeof T=="string")return Cs(T);switch(T){case j:return Cs("Suspense");case H:return Cs("SuspenseList")}if(typeof T=="object")switch(T.$$typeof){case P:return Fu(T.render);case L:return Nt(T.type,_,q);case Q:{var ee=T,ye=ee._payload,Pe=ee._init;try{return Nt(Pe(ye),_,q)}catch{}}}return""}var Uu={},Es=kt.ReactDebugCurrentFrame;function Pt(T){if(T){var _=T._owner,q=Nt(T.type,T._source,_?_.type:null);Es.setExtraStackFrame(q)}else Es.setExtraStackFrame(null)}function yd(T,_,q,ee,ye){{var Pe=Function.call.bind(ur);for(var Ae in T)if(Pe(T,Ae)){var at=void 0;try{if(typeof T[Ae]!="function"){var Ct=Error((ee||"React class")+": "+q+" type `"+Ae+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof T[Ae]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw Ct.name="Invariant Violation",Ct}at=T[Ae](_,Ae,ee,q,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(qt){at=qt}at&&!(at instanceof Error)&&(Pt(ye),se("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",ee||"React class",q,Ae,typeof at),Pt(null)),at instanceof Error&&!(at.message in Uu)&&(Uu[at.message]=!0,Pt(ye),se("Failed %s type: %s",q,at.message),Pt(null))}}}function Pa(T){if(T){var _=T._owner,q=Nt(T.type,T._source,_?_.type:null);He(q)}else He(null)}var lt;lt=!1;function kl(){if(ue.current){var T=nr(ue.current.type);if(T)return`

Check the render method of \``+T+"`."}return""}function fr(T){if(T!==void 0){var _=T.fileName.replace(/^.*[\\\/]/,""),q=T.lineNumber;return`

Check your code at `+_+":"+q+"."}return""}function ci(T){return T!=null?fr(T.__source):""}var Wr={};function Fa(T){var _=kl();if(!_){var q=typeof T=="string"?T:T.displayName||T.name;q&&(_=`

Check the top-level render call using <`+q+">.")}return _}function _n(T,_){if(!(!T._store||T._store.validated||T.key!=null)){T._store.validated=!0;var q=Fa(_);if(!Wr[q]){Wr[q]=!0;var ee="";T&&T._owner&&T._owner!==ue.current&&(ee=" It was passed a child from "+nr(T._owner.type)+"."),Pa(T),se('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',q,ee),Pa(null)}}}function ln(T,_){if(typeof T=="object"){if(xn(T))for(var q=0;q<T.length;q++){var ee=T[q];wn(ee)&&_n(ee,_)}else if(wn(T))T._store&&(T._store.validated=!0);else if(T){var ye=ae(T);if(typeof ye=="function"&&ye!==T.entries)for(var Pe=ye.call(T),Ae;!(Ae=Pe.next()).done;)wn(Ae.value)&&_n(Ae.value,_)}}}function va(T){{var _=T.type;if(_==null||typeof _=="string")return;var q;if(typeof _=="function")q=_.propTypes;else if(typeof _=="object"&&(_.$$typeof===P||_.$$typeof===L))q=_.propTypes;else return;if(q){var ee=nr(_);yd(q,T.props,"prop",ee,T)}else if(_.PropTypes!==void 0&&!lt){lt=!0;var ye=nr(_);se("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",ye||"Unknown")}typeof _.getDefaultProps=="function"&&!_.getDefaultProps.isReactClassApproved&&se("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function Ji(T){{for(var _=Object.keys(T.props),q=0;q<_.length;q++){var ee=_[q];if(ee!=="children"&&ee!=="key"){Pa(T),se("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",ee),Pa(null);break}}T.ref!==null&&(Pa(T),se("Invalid attribute `ref` supplied to `React.Fragment`."),Pa(null))}}function zr(T,_,q){var ee=de(T);if(!ee){var ye="";(T===void 0||typeof T=="object"&&T!==null&&Object.keys(T).length===0)&&(ye+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var Pe=ci(_);Pe?ye+=Pe:ye+=kl();var Ae;T===null?Ae="null":xn(T)?Ae="array":T!==void 0&&T.$$typeof===g?(Ae="<"+(nr(T.type)||"Unknown")+" />",ye=" Did you accidentally export a JSX literal instead of a component?"):Ae=typeof T,se("React.createElement: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",Ae,ye)}var at=wt.apply(this,arguments);if(at==null)return at;if(ee)for(var Ct=2;Ct<arguments.length;Ct++)ln(arguments[Ct],T);return T===S?Ji(at):va(at),at}var Yr=!1;function Cp(T){var _=zr.bind(null,T);return _.type=T,Yr||(Yr=!0,pt("React.createFactory() is deprecated and will be removed in a future major release. Consider using JSX or use React.createElement() directly instead.")),Object.defineProperty(_,"type",{enumerable:!1,get:function(){return pt("Factory.type is deprecated. Access the class directly before passing it to createFactory."),Object.defineProperty(this,"type",{value:T}),T}}),_}function Ts(T,_,q){for(var ee=bn.apply(this,arguments),ye=2;ye<arguments.length;ye++)ln(arguments[ye],ee.type);return va(ee),ee}function Rl(T,_){var q=Ee.transition;Ee.transition={};var ee=Ee.transition;Ee.transition._updatedFibers=new Set;try{T()}finally{if(Ee.transition=q,q===null&&ee._updatedFibers){var ye=ee._updatedFibers.size;ye>10&&pt("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."),ee._updatedFibers.clear()}}}var ks=!1,Rs=null;function Dl(T){if(Rs===null)try{var _=("require"+Math.random()).slice(0,7),q=o&&o[_];Rs=q.call(o,"timers").setImmediate}catch{Rs=function(ye){ks===!1&&(ks=!0,typeof MessageChannel>"u"&&se("This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."));var Pe=new MessageChannel;Pe.port1.onmessage=ye,Pe.port2.postMessage(void 0)}}return Rs(T)}var Zi=0,ea=!1;function Po(T){{var _=Zi;Zi++,le.current===null&&(le.current=[]);var q=le.isBatchingLegacy,ee;try{if(le.isBatchingLegacy=!0,ee=T(),!q&&le.didScheduleLegacyUpdate){var ye=le.current;ye!==null&&(le.didScheduleLegacyUpdate=!1,po(ye))}}catch(yt){throw fo(_),yt}finally{le.isBatchingLegacy=q}if(ee!==null&&typeof ee=="object"&&typeof ee.then=="function"){var Pe=ee,Ae=!1,at={then:function(yt,hn){Ae=!0,Pe.then(function(In){fo(_),Zi===0?Ds(In,yt,hn):yt(In)},function(In){fo(_),hn(In)})}};return!ea&&typeof Promise<"u"&&Promise.resolve().then(function(){}).then(function(){Ae||(ea=!0,se("You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"))}),at}else{var Ct=ee;if(fo(_),Zi===0){var qt=le.current;qt!==null&&(po(qt),le.current=null);var sn={then:function(yt,hn){le.current===null?(le.current=[],Ds(Ct,yt,hn)):yt(Ct)}};return sn}else{var un={then:function(yt,hn){yt(Ct)}};return un}}}}function fo(T){T!==Zi-1&&se("You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "),Zi=T}function Ds(T,_,q){{var ee=le.current;if(ee!==null)try{po(ee),Dl(function(){ee.length===0?(le.current=null,_(T)):Ds(T,_,q)})}catch(ye){q(ye)}else _(T)}}var Fo=!1;function po(T){if(!Fo){Fo=!0;var _=0;try{for(;_<T.length;_++){var q=T[_];do q=q(!0);while(q!==null)}T.length=0}catch(ee){throw T=T.slice(_+1),ee}finally{Fo=!1}}}var Ms=zr,Bu=Ts,ta=Cp,Os={map:io,forEach:Sl,count:wl,toArray:ao,only:Cl};r.Children=Os,r.Component=it,r.Fragment=S,r.Profiler=E,r.PureComponent=Ut,r.StrictMode=y,r.Suspense=j,r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=kt,r.act=Po,r.cloneElement=Bu,r.createContext=ja,r.createElement=Ms,r.createFactory=ta,r.createRef=an,r.forwardRef=oo,r.isValidElement=wn,r.lazy=La,r.memo=Te,r.startTransition=Rl,r.unstable_act=Po,r.useCallback=Lr,r.useContext=Rt,r.useDebugValue=kn,r.useDeferredValue=za,r.useEffect=Fn,r.useId=lo,r.useImperativeHandle=Qt,r.useInsertionEffect=yn,r.useLayoutEffect=Cn,r.useMemo=ma,r.useReducer=$t,r.useRef=St,r.useState=ut,r.useSyncExternalStore=gd,r.useTransition=gt,r.version=d,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}()}(op,op.exports)),op.exports}var fD={};fD.NODE_ENV==="production"?nx.exports=cD():nx.exports=dD();var Je=nx.exports;const On=Eg(Je);/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var BS;function pD(){if(BS)return ap;BS=1;var o=Je,r=Symbol.for("react.element"),s=Symbol.for("react.fragment"),d=Object.prototype.hasOwnProperty,g=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,b={key:!0,ref:!0,__self:!0,__source:!0};function S(y,E,O){var $,P={},j=null,H=null;O!==void 0&&(j=""+O),E.key!==void 0&&(j=""+E.key),E.ref!==void 0&&(H=E.ref);for($ in E)d.call(E,$)&&!b.hasOwnProperty($)&&(P[$]=E[$]);if(y&&y.defaultProps)for($ in E=y.defaultProps,E)P[$]===void 0&&(P[$]=E[$]);return{$$typeof:r,type:y,key:j,ref:H,props:P,_owner:g.current}}return ap.Fragment=s,ap.jsx=S,ap.jsxs=S,ap}var lp={},HS;function hD(){if(HS)return lp;HS=1;var o={};/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */return o.NODE_ENV!=="production"&&function(){var r=Je,s=Symbol.for("react.element"),d=Symbol.for("react.portal"),g=Symbol.for("react.fragment"),b=Symbol.for("react.strict_mode"),S=Symbol.for("react.profiler"),y=Symbol.for("react.provider"),E=Symbol.for("react.context"),O=Symbol.for("react.forward_ref"),$=Symbol.for("react.suspense"),P=Symbol.for("react.suspense_list"),j=Symbol.for("react.memo"),H=Symbol.for("react.lazy"),L=Symbol.for("react.offscreen"),Q=Symbol.iterator,xe="@@iterator";function ze(z){if(z===null||typeof z!="object")return null;var de=Q&&z[Q]||z[xe];return typeof de=="function"?de:null}var fe=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;function ae(z){{for(var de=arguments.length,Te=new Array(de>1?de-1:0),De=1;De<de;De++)Te[De-1]=arguments[De];ce("error",z,Te)}}function ce(z,de,Te){{var De=fe.ReactDebugCurrentFrame,Rt=De.getStackAddendum();Rt!==""&&(de+="%s",Te=Te.concat([Rt]));var ut=Te.map(function($t){return String($t)});ut.unshift("Warning: "+de),Function.prototype.apply.call(console[z],console,ut)}}var Ee=!1,le=!1,ue=!1,$e=!1,ft=!1,He;He=Symbol.for("react.module.reference");function Tt(z){return!!(typeof z=="string"||typeof z=="function"||z===g||z===S||ft||z===b||z===$||z===P||$e||z===L||Ee||le||ue||typeof z=="object"&&z!==null&&(z.$$typeof===H||z.$$typeof===j||z.$$typeof===y||z.$$typeof===E||z.$$typeof===O||z.$$typeof===He||z.getModuleId!==void 0))}function bt(z,de,Te){var De=z.displayName;if(De)return De;var Rt=de.displayName||de.name||"";return Rt!==""?Te+"("+Rt+")":Te}function rt(z){return z.displayName||"Context"}function Be(z){if(z==null)return null;if(typeof z.tag=="number"&&ae("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof z=="function")return z.displayName||z.name||null;if(typeof z=="string")return z;switch(z){case g:return"Fragment";case d:return"Portal";case S:return"Profiler";case b:return"StrictMode";case $:return"Suspense";case P:return"SuspenseList"}if(typeof z=="object")switch(z.$$typeof){case E:var de=z;return rt(de)+".Consumer";case y:var Te=z;return rt(Te._context)+".Provider";case O:return bt(z,z.render,"ForwardRef");case j:var De=z.displayName||null;return De!==null?De:Be(z.type)||"Memo";case H:{var Rt=z,ut=Rt._payload,$t=Rt._init;try{return Be($t(ut))}catch{return null}}}return null}var Ht=Object.assign,kt=0,pt,se,ke,be,B,re,Ve;function et(){}et.__reactDisabledLog=!0;function it(){{if(kt===0){pt=console.log,se=console.info,ke=console.warn,be=console.error,B=console.group,re=console.groupCollapsed,Ve=console.groupEnd;var z={configurable:!0,enumerable:!0,value:et,writable:!0};Object.defineProperties(console,{info:z,log:z,warn:z,error:z,group:z,groupCollapsed:z,groupEnd:z})}kt++}}function ht(){{if(kt--,kt===0){var z={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:Ht({},z,{value:pt}),info:Ht({},z,{value:se}),warn:Ht({},z,{value:ke}),error:Ht({},z,{value:be}),group:Ht({},z,{value:B}),groupCollapsed:Ht({},z,{value:re}),groupEnd:Ht({},z,{value:Ve})})}kt<0&&ae("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var Ot=fe.ReactCurrentDispatcher,tt;function vt(z,de,Te){{if(tt===void 0)try{throw Error()}catch(Rt){var De=Rt.stack.trim().match(/\n( *(at )?)/);tt=De&&De[1]||""}return`
`+tt+z}}var Ut=!1,pn;{var an=typeof WeakMap=="function"?WeakMap:Map;pn=new an}function Pn(z,de){if(!z||Ut)return"";{var Te=pn.get(z);if(Te!==void 0)return Te}var De;Ut=!0;var Rt=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var ut;ut=Ot.current,Ot.current=null,it();try{if(de){var $t=function(){throw Error()};if(Object.defineProperty($t.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct($t,[])}catch(kn){De=kn}Reflect.construct(z,[],$t)}else{try{$t.call()}catch(kn){De=kn}z.call($t.prototype)}}else{try{throw Error()}catch(kn){De=kn}z()}}catch(kn){if(kn&&De&&typeof kn.stack=="string"){for(var St=kn.stack.split(`
`),Fn=De.stack.split(`
`),yn=St.length-1,Cn=Fn.length-1;yn>=1&&Cn>=0&&St[yn]!==Fn[Cn];)Cn--;for(;yn>=1&&Cn>=0;yn--,Cn--)if(St[yn]!==Fn[Cn]){if(yn!==1||Cn!==1)do if(yn--,Cn--,Cn<0||St[yn]!==Fn[Cn]){var Lr=`
`+St[yn].replace(" at new "," at ");return z.displayName&&Lr.includes("<anonymous>")&&(Lr=Lr.replace("<anonymous>",z.displayName)),typeof z=="function"&&pn.set(z,Lr),Lr}while(yn>=1&&Cn>=0);break}}}finally{Ut=!1,Ot.current=ut,ht(),Error.prepareStackTrace=Rt}var ma=z?z.displayName||z.name:"",Qt=ma?vt(ma):"";return typeof z=="function"&&pn.set(z,Qt),Qt}function xn(z,de,Te){return Pn(z,!1)}function An(z){var de=z.prototype;return!!(de&&de.isReactComponent)}function tr(z,de,Te){if(z==null)return"";if(typeof z=="function")return Pn(z,An(z));if(typeof z=="string")return vt(z);switch(z){case $:return vt("Suspense");case P:return vt("SuspenseList")}if(typeof z=="object")switch(z.$$typeof){case O:return xn(z.render);case j:return tr(z.type,de,Te);case H:{var De=z,Rt=De._payload,ut=De._init;try{return tr(ut(Rt),de,Te)}catch{}}}return""}var Gn=Object.prototype.hasOwnProperty,Ri={},ha=fe.ReactDebugCurrentFrame;function Hr(z){if(z){var de=z._owner,Te=tr(z.type,z._source,de?de.type:null);ha.setExtraStackFrame(Te)}else ha.setExtraStackFrame(null)}function nr(z,de,Te,De,Rt){{var ut=Function.call.bind(Gn);for(var $t in z)if(ut(z,$t)){var St=void 0;try{if(typeof z[$t]!="function"){var Fn=Error((De||"React class")+": "+Te+" type `"+$t+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof z[$t]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw Fn.name="Invariant Violation",Fn}St=z[$t](de,$t,De,Te,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(yn){St=yn}St&&!(St instanceof Error)&&(Hr(Rt),ae("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",De||"React class",Te,$t,typeof St),Hr(null)),St instanceof Error&&!(St.message in Ri)&&(Ri[St.message]=!0,Hr(Rt),ae("Failed %s type: %s",Te,St.message),Hr(null))}}}var ur=Array.isArray;function cr(z){return ur(z)}function _r(z){{var de=typeof Symbol=="function"&&Symbol.toStringTag,Te=de&&z[Symbol.toStringTag]||z.constructor.name||"Object";return Te}}function ga(z){try{return Kn(z),!1}catch{return!0}}function Kn(z){return""+z}function xr(z){if(ga(z))return ae("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",_r(z)),Kn(z)}var oi=fe.ReactCurrentOwner,ro={key:!0,ref:!0,__self:!0,__source:!0},Di,we;function Qe(z){if(Gn.call(z,"ref")){var de=Object.getOwnPropertyDescriptor(z,"ref").get;if(de&&de.isReactWarning)return!1}return z.ref!==void 0}function wt(z){if(Gn.call(z,"key")){var de=Object.getOwnPropertyDescriptor(z,"key").get;if(de&&de.isReactWarning)return!1}return z.key!==void 0}function Gt(z,de){typeof z.ref=="string"&&oi.current}function bn(z,de){{var Te=function(){Di||(Di=!0,ae("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",de))};Te.isReactWarning=!0,Object.defineProperty(z,"key",{get:Te,configurable:!0})}}function wn(z,de){{var Te=function(){we||(we=!0,ae("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",de))};Te.isReactWarning=!0,Object.defineProperty(z,"ref",{get:Te,configurable:!0})}}var Sn=function(z,de,Te,De,Rt,ut,$t){var St={$$typeof:s,type:z,key:de,ref:Te,props:$t,_owner:ut};return St._store={},Object.defineProperty(St._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(St,"_self",{configurable:!1,enumerable:!1,writable:!1,value:De}),Object.defineProperty(St,"_source",{configurable:!1,enumerable:!1,writable:!1,value:Rt}),Object.freeze&&(Object.freeze(St.props),Object.freeze(St)),St};function dr(z,de,Te,De,Rt){{var ut,$t={},St=null,Fn=null;Te!==void 0&&(xr(Te),St=""+Te),wt(de)&&(xr(de.key),St=""+de.key),Qe(de)&&(Fn=de.ref,Gt(de,Rt));for(ut in de)Gn.call(de,ut)&&!ro.hasOwnProperty(ut)&&($t[ut]=de[ut]);if(z&&z.defaultProps){var yn=z.defaultProps;for(ut in yn)$t[ut]===void 0&&($t[ut]=yn[ut])}if(St||Fn){var Cn=typeof z=="function"?z.displayName||z.name||"Unknown":z;St&&bn($t,Cn),Fn&&wn($t,Cn)}return Sn(z,St,Fn,Rt,De,oi.current,$t)}}var vn=fe.ReactCurrentOwner,on=fe.ReactDebugCurrentFrame;function Kt(z){if(z){var de=z._owner,Te=tr(z.type,z._source,de?de.type:null);on.setExtraStackFrame(Te)}else on.setExtraStackFrame(null)}var Mi;Mi=!1;function qi(z){return typeof z=="object"&&z!==null&&z.$$typeof===s}function Xi(){{if(vn.current){var z=Be(vn.current.type);if(z)return`

Check the render method of \``+z+"`."}return""}}function io(z){return""}var wl={};function Sl(z){{var de=Xi();if(!de){var Te=typeof z=="string"?z:z.displayName||z.name;Te&&(de=`

Check the top-level render call using <`+Te+">.")}return de}}function ao(z,de){{if(!z._store||z._store.validated||z.key!=null)return;z._store.validated=!0;var Te=Sl(de);if(wl[Te])return;wl[Te]=!0;var De="";z&&z._owner&&z._owner!==vn.current&&(De=" It was passed a child from "+Be(z._owner.type)+"."),Kt(z),ae('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',Te,De),Kt(null)}}function Cl(z,de){{if(typeof z!="object")return;if(cr(z))for(var Te=0;Te<z.length;Te++){var De=z[Te];qi(De)&&ao(De,de)}else if(qi(z))z._store&&(z._store.validated=!0);else if(z){var Rt=ze(z);if(typeof Rt=="function"&&Rt!==z.entries)for(var ut=Rt.call(z),$t;!($t=ut.next()).done;)qi($t.value)&&ao($t.value,de)}}}function ja(z){{var de=z.type;if(de==null||typeof de=="string")return;var Te;if(typeof de=="function")Te=de.propTypes;else if(typeof de=="object"&&(de.$$typeof===O||de.$$typeof===j))Te=de.propTypes;else return;if(Te){var De=Be(de);nr(Te,z.props,"prop",De,z)}else if(de.PropTypes!==void 0&&!Mi){Mi=!0;var Rt=Be(de);ae("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",Rt||"Unknown")}typeof de.getDefaultProps=="function"&&!de.getDefaultProps.isReactClassApproved&&ae("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function Oi(z){{for(var de=Object.keys(z.props),Te=0;Te<de.length;Te++){var De=de[Te];if(De!=="children"&&De!=="key"){Kt(z),ae("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",De),Kt(null);break}}z.ref!==null&&(Kt(z),ae("Invalid attribute `ref` supplied to `React.Fragment`."),Kt(null))}}var br={};function $i(z,de,Te,De,Rt,ut){{var $t=Tt(z);if(!$t){var St="";(z===void 0||typeof z=="object"&&z!==null&&Object.keys(z).length===0)&&(St+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var Fn=io();Fn?St+=Fn:St+=Xi();var yn;z===null?yn="null":cr(z)?yn="array":z!==void 0&&z.$$typeof===s?(yn="<"+(Be(z.type)||"Unknown")+" />",St=" Did you accidentally export a JSX literal instead of a component?"):yn=typeof z,ae("React.jsx: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",yn,St)}var Cn=dr(z,de,Te,Rt,ut);if(Cn==null)return Cn;if($t){var Lr=de.children;if(Lr!==void 0)if(De)if(cr(Lr)){for(var ma=0;ma<Lr.length;ma++)Cl(Lr[ma],z);Object.freeze&&Object.freeze(Lr)}else ae("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");else Cl(Lr,z)}if(Gn.call(de,"key")){var Qt=Be(z),kn=Object.keys(de).filter(function(lo){return lo!=="key"}),gt=kn.length>0?"{key: someKey, "+kn.join(": ..., ")+": ...}":"{key: someKey}";if(!br[Qt+gt]){var za=kn.length>0?"{"+kn.join(": ..., ")+": ...}":"{}";ae(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,gt,Qt,za,Qt),br[Qt+gt]=!0}}return z===g?Oi(Cn):ja(Cn),Cn}}function li(z,de,Te){return $i(z,de,Te,!0)}function _a(z,de,Te){return $i(z,de,Te,!1)}var La=_a,oo=li;lp.Fragment=g,lp.jsx=La,lp.jsxs=oo}(),lp}var gD={};gD.NODE_ENV==="production"?tx.exports=pD():tx.exports=hD();var m=tx.exports,rx={exports:{}},Hi={},Tg={exports:{}},ix={};/**
 * @license React
 * scheduler.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var VS;function mD(){return VS||(VS=1,function(o){function r(se,ke){var be=se.length;se.push(ke);e:for(;0<be;){var B=be-1>>>1,re=se[B];if(0<g(re,ke))se[B]=ke,se[be]=re,be=B;else break e}}function s(se){return se.length===0?null:se[0]}function d(se){if(se.length===0)return null;var ke=se[0],be=se.pop();if(be!==ke){se[0]=be;e:for(var B=0,re=se.length,Ve=re>>>1;B<Ve;){var et=2*(B+1)-1,it=se[et],ht=et+1,Ot=se[ht];if(0>g(it,be))ht<re&&0>g(Ot,it)?(se[B]=Ot,se[ht]=be,B=ht):(se[B]=it,se[et]=be,B=et);else if(ht<re&&0>g(Ot,be))se[B]=Ot,se[ht]=be,B=ht;else break e}}return ke}function g(se,ke){var be=se.sortIndex-ke.sortIndex;return be!==0?be:se.id-ke.id}if(typeof performance=="object"&&typeof performance.now=="function"){var b=performance;o.unstable_now=function(){return b.now()}}else{var S=Date,y=S.now();o.unstable_now=function(){return S.now()-y}}var E=[],O=[],$=1,P=null,j=3,H=!1,L=!1,Q=!1,xe=typeof setTimeout=="function"?setTimeout:null,ze=typeof clearTimeout=="function"?clearTimeout:null,fe=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function ae(se){for(var ke=s(O);ke!==null;){if(ke.callback===null)d(O);else if(ke.startTime<=se)d(O),ke.sortIndex=ke.expirationTime,r(E,ke);else break;ke=s(O)}}function ce(se){if(Q=!1,ae(se),!L)if(s(E)!==null)L=!0,kt(Ee);else{var ke=s(O);ke!==null&&pt(ce,ke.startTime-se)}}function Ee(se,ke){L=!1,Q&&(Q=!1,ze($e),$e=-1),H=!0;var be=j;try{for(ae(ke),P=s(E);P!==null&&(!(P.expirationTime>ke)||se&&!Tt());){var B=P.callback;if(typeof B=="function"){P.callback=null,j=P.priorityLevel;var re=B(P.expirationTime<=ke);ke=o.unstable_now(),typeof re=="function"?P.callback=re:P===s(E)&&d(E),ae(ke)}else d(E);P=s(E)}if(P!==null)var Ve=!0;else{var et=s(O);et!==null&&pt(ce,et.startTime-ke),Ve=!1}return Ve}finally{P=null,j=be,H=!1}}var le=!1,ue=null,$e=-1,ft=5,He=-1;function Tt(){return!(o.unstable_now()-He<ft)}function bt(){if(ue!==null){var se=o.unstable_now();He=se;var ke=!0;try{ke=ue(!0,se)}finally{ke?rt():(le=!1,ue=null)}}else le=!1}var rt;if(typeof fe=="function")rt=function(){fe(bt)};else if(typeof MessageChannel<"u"){var Be=new MessageChannel,Ht=Be.port2;Be.port1.onmessage=bt,rt=function(){Ht.postMessage(null)}}else rt=function(){xe(bt,0)};function kt(se){ue=se,le||(le=!0,rt())}function pt(se,ke){$e=xe(function(){se(o.unstable_now())},ke)}o.unstable_IdlePriority=5,o.unstable_ImmediatePriority=1,o.unstable_LowPriority=4,o.unstable_NormalPriority=3,o.unstable_Profiling=null,o.unstable_UserBlockingPriority=2,o.unstable_cancelCallback=function(se){se.callback=null},o.unstable_continueExecution=function(){L||H||(L=!0,kt(Ee))},o.unstable_forceFrameRate=function(se){0>se||125<se?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):ft=0<se?Math.floor(1e3/se):5},o.unstable_getCurrentPriorityLevel=function(){return j},o.unstable_getFirstCallbackNode=function(){return s(E)},o.unstable_next=function(se){switch(j){case 1:case 2:case 3:var ke=3;break;default:ke=j}var be=j;j=ke;try{return se()}finally{j=be}},o.unstable_pauseExecution=function(){},o.unstable_requestPaint=function(){},o.unstable_runWithPriority=function(se,ke){switch(se){case 1:case 2:case 3:case 4:case 5:break;default:se=3}var be=j;j=se;try{return ke()}finally{j=be}},o.unstable_scheduleCallback=function(se,ke,be){var B=o.unstable_now();switch(typeof be=="object"&&be!==null?(be=be.delay,be=typeof be=="number"&&0<be?B+be:B):be=B,se){case 1:var re=-1;break;case 2:re=250;break;case 5:re=1073741823;break;case 4:re=1e4;break;default:re=5e3}return re=be+re,se={id:$++,callback:ke,priorityLevel:se,startTime:be,expirationTime:re,sortIndex:-1},be>B?(se.sortIndex=be,r(O,se),s(E)===null&&se===s(O)&&(Q?(ze($e),$e=-1):Q=!0,pt(ce,be-B))):(se.sortIndex=re,r(E,se),L||H||(L=!0,kt(Ee))),se},o.unstable_shouldYield=Tt,o.unstable_wrapCallback=function(se){var ke=j;return function(){var be=j;j=ke;try{return se.apply(this,arguments)}finally{j=be}}}}(ix)),ix}var ax={},WS;function vD(){return WS||(WS=1,function(o){var r={};/**
 * @license React
 * scheduler.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */r.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var s=!1,d=5;function g(we,Qe){var wt=we.length;we.push(Qe),y(we,Qe,wt)}function b(we){return we.length===0?null:we[0]}function S(we){if(we.length===0)return null;var Qe=we[0],wt=we.pop();return wt!==Qe&&(we[0]=wt,E(we,wt,0)),Qe}function y(we,Qe,wt){for(var Gt=wt;Gt>0;){var bn=Gt-1>>>1,wn=we[bn];if(O(wn,Qe)>0)we[bn]=Qe,we[Gt]=wn,Gt=bn;else return}}function E(we,Qe,wt){for(var Gt=wt,bn=we.length,wn=bn>>>1;Gt<wn;){var Sn=(Gt+1)*2-1,dr=we[Sn],vn=Sn+1,on=we[vn];if(O(dr,Qe)<0)vn<bn&&O(on,dr)<0?(we[Gt]=on,we[vn]=Qe,Gt=vn):(we[Gt]=dr,we[Sn]=Qe,Gt=Sn);else if(vn<bn&&O(on,Qe)<0)we[Gt]=on,we[vn]=Qe,Gt=vn;else return}}function O(we,Qe){var wt=we.sortIndex-Qe.sortIndex;return wt!==0?wt:we.id-Qe.id}var $=1,P=2,j=3,H=4,L=5;function Q(we,Qe){}var xe=typeof performance=="object"&&typeof performance.now=="function";if(xe){var ze=performance;o.unstable_now=function(){return ze.now()}}else{var fe=Date,ae=fe.now();o.unstable_now=function(){return fe.now()-ae}}var ce=1073741823,Ee=-1,le=250,ue=5e3,$e=1e4,ft=ce,He=[],Tt=[],bt=1,rt=null,Be=j,Ht=!1,kt=!1,pt=!1,se=typeof setTimeout=="function"?setTimeout:null,ke=typeof clearTimeout=="function"?clearTimeout:null,be=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function B(we){for(var Qe=b(Tt);Qe!==null;){if(Qe.callback===null)S(Tt);else if(Qe.startTime<=we)S(Tt),Qe.sortIndex=Qe.expirationTime,g(He,Qe);else return;Qe=b(Tt)}}function re(we){if(pt=!1,B(we),!kt)if(b(He)!==null)kt=!0,Kn(Ve);else{var Qe=b(Tt);Qe!==null&&xr(re,Qe.startTime-we)}}function Ve(we,Qe){kt=!1,pt&&(pt=!1,oi()),Ht=!0;var wt=Be;try{var Gt;if(!s)return et(we,Qe)}finally{rt=null,Be=wt,Ht=!1}}function et(we,Qe){var wt=Qe;for(B(wt),rt=b(He);rt!==null&&!(rt.expirationTime>wt&&(!we||ha()));){var Gt=rt.callback;if(typeof Gt=="function"){rt.callback=null,Be=rt.priorityLevel;var bn=rt.expirationTime<=wt,wn=Gt(bn);wt=o.unstable_now(),typeof wn=="function"?rt.callback=wn:rt===b(He)&&S(He),B(wt)}else S(He);rt=b(He)}if(rt!==null)return!0;var Sn=b(Tt);return Sn!==null&&xr(re,Sn.startTime-wt),!1}function it(we,Qe){switch(we){case $:case P:case j:case H:case L:break;default:we=j}var wt=Be;Be=we;try{return Qe()}finally{Be=wt}}function ht(we){var Qe;switch(Be){case $:case P:case j:Qe=j;break;default:Qe=Be;break}var wt=Be;Be=Qe;try{return we()}finally{Be=wt}}function Ot(we){var Qe=Be;return function(){var wt=Be;Be=Qe;try{return we.apply(this,arguments)}finally{Be=wt}}}function tt(we,Qe,wt){var Gt=o.unstable_now(),bn;if(typeof wt=="object"&&wt!==null){var wn=wt.delay;typeof wn=="number"&&wn>0?bn=Gt+wn:bn=Gt}else bn=Gt;var Sn;switch(we){case $:Sn=Ee;break;case P:Sn=le;break;case L:Sn=ft;break;case H:Sn=$e;break;case j:default:Sn=ue;break}var dr=bn+Sn,vn={id:bt++,callback:Qe,priorityLevel:we,startTime:bn,expirationTime:dr,sortIndex:-1};return bn>Gt?(vn.sortIndex=bn,g(Tt,vn),b(He)===null&&vn===b(Tt)&&(pt?oi():pt=!0,xr(re,bn-Gt))):(vn.sortIndex=dr,g(He,vn),!kt&&!Ht&&(kt=!0,Kn(Ve))),vn}function vt(){}function Ut(){!kt&&!Ht&&(kt=!0,Kn(Ve))}function pn(){return b(He)}function an(we){we.callback=null}function Pn(){return Be}var xn=!1,An=null,tr=-1,Gn=d,Ri=-1;function ha(){var we=o.unstable_now()-Ri;return!(we<Gn)}function Hr(){}function nr(we){if(we<0||we>125){console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported");return}we>0?Gn=Math.floor(1e3/we):Gn=d}var ur=function(){if(An!==null){var we=o.unstable_now();Ri=we;var Qe=!0,wt=!0;try{wt=An(Qe,we)}finally{wt?cr():(xn=!1,An=null)}}else xn=!1},cr;if(typeof be=="function")cr=function(){be(ur)};else if(typeof MessageChannel<"u"){var _r=new MessageChannel,ga=_r.port2;_r.port1.onmessage=ur,cr=function(){ga.postMessage(null)}}else cr=function(){se(ur,0)};function Kn(we){An=we,xn||(xn=!0,cr())}function xr(we,Qe){tr=se(function(){we(o.unstable_now())},Qe)}function oi(){ke(tr),tr=-1}var ro=Hr,Di=null;o.unstable_IdlePriority=L,o.unstable_ImmediatePriority=$,o.unstable_LowPriority=H,o.unstable_NormalPriority=j,o.unstable_Profiling=Di,o.unstable_UserBlockingPriority=P,o.unstable_cancelCallback=an,o.unstable_continueExecution=Ut,o.unstable_forceFrameRate=nr,o.unstable_getCurrentPriorityLevel=Pn,o.unstable_getFirstCallbackNode=pn,o.unstable_next=ht,o.unstable_pauseExecution=vt,o.unstable_requestPaint=ro,o.unstable_runWithPriority=it,o.unstable_scheduleCallback=tt,o.unstable_shouldYield=ha,o.unstable_wrapCallback=Ot,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}()}(ax)),ax}var YS;function GS(){if(YS)return Tg.exports;YS=1;var o={};return o.NODE_ENV==="production"?Tg.exports=mD():Tg.exports=vD(),Tg.exports}/**
 * @license React
 * react-dom.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var KS;function yD(){if(KS)return Hi;KS=1;var o=Je,r=GS();function s(n){for(var i="https://reactjs.org/docs/error-decoder.html?invariant="+n,u=1;u<arguments.length;u++)i+="&args[]="+encodeURIComponent(arguments[u]);return"Minified React error #"+n+"; visit "+i+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var d=new Set,g={};function b(n,i){S(n,i),S(n+"Capture",i)}function S(n,i){for(g[n]=i,n=0;n<i.length;n++)d.add(i[n])}var y=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),E=Object.prototype.hasOwnProperty,O=/^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,$={},P={};function j(n){return E.call(P,n)?!0:E.call($,n)?!1:O.test(n)?P[n]=!0:($[n]=!0,!1)}function H(n,i,u,f){if(u!==null&&u.type===0)return!1;switch(typeof i){case"function":case"symbol":return!0;case"boolean":return f?!1:u!==null?!u.acceptsBooleans:(n=n.toLowerCase().slice(0,5),n!=="data-"&&n!=="aria-");default:return!1}}function L(n,i,u,f){if(i===null||typeof i>"u"||H(n,i,u,f))return!0;if(f)return!1;if(u!==null)switch(u.type){case 3:return!i;case 4:return i===!1;case 5:return isNaN(i);case 6:return isNaN(i)||1>i}return!1}function Q(n,i,u,f,h,x,k){this.acceptsBooleans=i===2||i===3||i===4,this.attributeName=f,this.attributeNamespace=h,this.mustUseProperty=u,this.propertyName=n,this.type=i,this.sanitizeURL=x,this.removeEmptyString=k}var xe={};"children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(n){xe[n]=new Q(n,0,!1,n,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(n){var i=n[0];xe[i]=new Q(i,1,!1,n[1],null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(n){xe[n]=new Q(n,2,!1,n.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(n){xe[n]=new Q(n,2,!1,n,null,!1,!1)}),"allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(n){xe[n]=new Q(n,3,!1,n.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(n){xe[n]=new Q(n,3,!0,n,null,!1,!1)}),["capture","download"].forEach(function(n){xe[n]=new Q(n,4,!1,n,null,!1,!1)}),["cols","rows","size","span"].forEach(function(n){xe[n]=new Q(n,6,!1,n,null,!1,!1)}),["rowSpan","start"].forEach(function(n){xe[n]=new Q(n,5,!1,n.toLowerCase(),null,!1,!1)});var ze=/[\-:]([a-z])/g;function fe(n){return n[1].toUpperCase()}"accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(n){var i=n.replace(ze,fe);xe[i]=new Q(i,1,!1,n,null,!1,!1)}),"xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(n){var i=n.replace(ze,fe);xe[i]=new Q(i,1,!1,n,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(n){var i=n.replace(ze,fe);xe[i]=new Q(i,1,!1,n,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(n){xe[n]=new Q(n,1,!1,n.toLowerCase(),null,!1,!1)}),xe.xlinkHref=new Q("xlinkHref",1,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(n){xe[n]=new Q(n,1,!1,n.toLowerCase(),null,!0,!0)});function ae(n,i,u,f){var h=xe.hasOwnProperty(i)?xe[i]:null;(h!==null?h.type!==0:f||!(2<i.length)||i[0]!=="o"&&i[0]!=="O"||i[1]!=="n"&&i[1]!=="N")&&(L(i,u,h,f)&&(u=null),f||h===null?j(i)&&(u===null?n.removeAttribute(i):n.setAttribute(i,""+u)):h.mustUseProperty?n[h.propertyName]=u===null?h.type===3?!1:"":u:(i=h.attributeName,f=h.attributeNamespace,u===null?n.removeAttribute(i):(h=h.type,u=h===3||h===4&&u===!0?"":""+u,f?n.setAttributeNS(f,i,u):n.setAttribute(i,u))))}var ce=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,Ee=Symbol.for("react.element"),le=Symbol.for("react.portal"),ue=Symbol.for("react.fragment"),$e=Symbol.for("react.strict_mode"),ft=Symbol.for("react.profiler"),He=Symbol.for("react.provider"),Tt=Symbol.for("react.context"),bt=Symbol.for("react.forward_ref"),rt=Symbol.for("react.suspense"),Be=Symbol.for("react.suspense_list"),Ht=Symbol.for("react.memo"),kt=Symbol.for("react.lazy"),pt=Symbol.for("react.offscreen"),se=Symbol.iterator;function ke(n){return n===null||typeof n!="object"?null:(n=se&&n[se]||n["@@iterator"],typeof n=="function"?n:null)}var be=Object.assign,B;function re(n){if(B===void 0)try{throw Error()}catch(u){var i=u.stack.trim().match(/\n( *(at )?)/);B=i&&i[1]||""}return`
`+B+n}var Ve=!1;function et(n,i){if(!n||Ve)return"";Ve=!0;var u=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{if(i)if(i=function(){throw Error()},Object.defineProperty(i.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(i,[])}catch(J){var f=J}Reflect.construct(n,[],i)}else{try{i.call()}catch(J){f=J}n.call(i.prototype)}else{try{throw Error()}catch(J){f=J}n()}}catch(J){if(J&&f&&typeof J.stack=="string"){for(var h=J.stack.split(`
`),x=f.stack.split(`
`),k=h.length-1,A=x.length-1;1<=k&&0<=A&&h[k]!==x[A];)A--;for(;1<=k&&0<=A;k--,A--)if(h[k]!==x[A]){if(k!==1||A!==1)do if(k--,A--,0>A||h[k]!==x[A]){var N=`
`+h[k].replace(" at new "," at ");return n.displayName&&N.includes("<anonymous>")&&(N=N.replace("<anonymous>",n.displayName)),N}while(1<=k&&0<=A);break}}}finally{Ve=!1,Error.prepareStackTrace=u}return(n=n?n.displayName||n.name:"")?re(n):""}function it(n){switch(n.tag){case 5:return re(n.type);case 16:return re("Lazy");case 13:return re("Suspense");case 19:return re("SuspenseList");case 0:case 2:case 15:return n=et(n.type,!1),n;case 11:return n=et(n.type.render,!1),n;case 1:return n=et(n.type,!0),n;default:return""}}function ht(n){if(n==null)return null;if(typeof n=="function")return n.displayName||n.name||null;if(typeof n=="string")return n;switch(n){case ue:return"Fragment";case le:return"Portal";case ft:return"Profiler";case $e:return"StrictMode";case rt:return"Suspense";case Be:return"SuspenseList"}if(typeof n=="object")switch(n.$$typeof){case Tt:return(n.displayName||"Context")+".Consumer";case He:return(n._context.displayName||"Context")+".Provider";case bt:var i=n.render;return n=n.displayName,n||(n=i.displayName||i.name||"",n=n!==""?"ForwardRef("+n+")":"ForwardRef"),n;case Ht:return i=n.displayName||null,i!==null?i:ht(n.type)||"Memo";case kt:i=n._payload,n=n._init;try{return ht(n(i))}catch{}}return null}function Ot(n){var i=n.type;switch(n.tag){case 24:return"Cache";case 9:return(i.displayName||"Context")+".Consumer";case 10:return(i._context.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return n=i.render,n=n.displayName||n.name||"",i.displayName||(n!==""?"ForwardRef("+n+")":"ForwardRef");case 7:return"Fragment";case 5:return i;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return ht(i);case 8:return i===$e?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 17:case 2:case 14:case 15:if(typeof i=="function")return i.displayName||i.name||null;if(typeof i=="string")return i}return null}function tt(n){switch(typeof n){case"boolean":case"number":case"string":case"undefined":return n;case"object":return n;default:return""}}function vt(n){var i=n.type;return(n=n.nodeName)&&n.toLowerCase()==="input"&&(i==="checkbox"||i==="radio")}function Ut(n){var i=vt(n)?"checked":"value",u=Object.getOwnPropertyDescriptor(n.constructor.prototype,i),f=""+n[i];if(!n.hasOwnProperty(i)&&typeof u<"u"&&typeof u.get=="function"&&typeof u.set=="function"){var h=u.get,x=u.set;return Object.defineProperty(n,i,{configurable:!0,get:function(){return h.call(this)},set:function(k){f=""+k,x.call(this,k)}}),Object.defineProperty(n,i,{enumerable:u.enumerable}),{getValue:function(){return f},setValue:function(k){f=""+k},stopTracking:function(){n._valueTracker=null,delete n[i]}}}}function pn(n){n._valueTracker||(n._valueTracker=Ut(n))}function an(n){if(!n)return!1;var i=n._valueTracker;if(!i)return!0;var u=i.getValue(),f="";return n&&(f=vt(n)?n.checked?"true":"false":n.value),n=f,n!==u?(i.setValue(n),!0):!1}function Pn(n){if(n=n||(typeof document<"u"?document:void 0),typeof n>"u")return null;try{return n.activeElement||n.body}catch{return n.body}}function xn(n,i){var u=i.checked;return be({},i,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:u??n._wrapperState.initialChecked})}function An(n,i){var u=i.defaultValue==null?"":i.defaultValue,f=i.checked!=null?i.checked:i.defaultChecked;u=tt(i.value!=null?i.value:u),n._wrapperState={initialChecked:f,initialValue:u,controlled:i.type==="checkbox"||i.type==="radio"?i.checked!=null:i.value!=null}}function tr(n,i){i=i.checked,i!=null&&ae(n,"checked",i,!1)}function Gn(n,i){tr(n,i);var u=tt(i.value),f=i.type;if(u!=null)f==="number"?(u===0&&n.value===""||n.value!=u)&&(n.value=""+u):n.value!==""+u&&(n.value=""+u);else if(f==="submit"||f==="reset"){n.removeAttribute("value");return}i.hasOwnProperty("value")?ha(n,i.type,u):i.hasOwnProperty("defaultValue")&&ha(n,i.type,tt(i.defaultValue)),i.checked==null&&i.defaultChecked!=null&&(n.defaultChecked=!!i.defaultChecked)}function Ri(n,i,u){if(i.hasOwnProperty("value")||i.hasOwnProperty("defaultValue")){var f=i.type;if(!(f!=="submit"&&f!=="reset"||i.value!==void 0&&i.value!==null))return;i=""+n._wrapperState.initialValue,u||i===n.value||(n.value=i),n.defaultValue=i}u=n.name,u!==""&&(n.name=""),n.defaultChecked=!!n._wrapperState.initialChecked,u!==""&&(n.name=u)}function ha(n,i,u){(i!=="number"||Pn(n.ownerDocument)!==n)&&(u==null?n.defaultValue=""+n._wrapperState.initialValue:n.defaultValue!==""+u&&(n.defaultValue=""+u))}var Hr=Array.isArray;function nr(n,i,u,f){if(n=n.options,i){i={};for(var h=0;h<u.length;h++)i["$"+u[h]]=!0;for(u=0;u<n.length;u++)h=i.hasOwnProperty("$"+n[u].value),n[u].selected!==h&&(n[u].selected=h),h&&f&&(n[u].defaultSelected=!0)}else{for(u=""+tt(u),i=null,h=0;h<n.length;h++){if(n[h].value===u){n[h].selected=!0,f&&(n[h].defaultSelected=!0);return}i!==null||n[h].disabled||(i=n[h])}i!==null&&(i.selected=!0)}}function ur(n,i){if(i.dangerouslySetInnerHTML!=null)throw Error(s(91));return be({},i,{value:void 0,defaultValue:void 0,children:""+n._wrapperState.initialValue})}function cr(n,i){var u=i.value;if(u==null){if(u=i.children,i=i.defaultValue,u!=null){if(i!=null)throw Error(s(92));if(Hr(u)){if(1<u.length)throw Error(s(93));u=u[0]}i=u}i==null&&(i=""),u=i}n._wrapperState={initialValue:tt(u)}}function _r(n,i){var u=tt(i.value),f=tt(i.defaultValue);u!=null&&(u=""+u,u!==n.value&&(n.value=u),i.defaultValue==null&&n.defaultValue!==u&&(n.defaultValue=u)),f!=null&&(n.defaultValue=""+f)}function ga(n){var i=n.textContent;i===n._wrapperState.initialValue&&i!==""&&i!==null&&(n.value=i)}function Kn(n){switch(n){case"svg":return"http://www.w3.org/2000/svg";case"math":return"http://www.w3.org/1998/Math/MathML";default:return"http://www.w3.org/1999/xhtml"}}function xr(n,i){return n==null||n==="http://www.w3.org/1999/xhtml"?Kn(i):n==="http://www.w3.org/2000/svg"&&i==="foreignObject"?"http://www.w3.org/1999/xhtml":n}var oi,ro=function(n){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(i,u,f,h){MSApp.execUnsafeLocalFunction(function(){return n(i,u,f,h)})}:n}(function(n,i){if(n.namespaceURI!=="http://www.w3.org/2000/svg"||"innerHTML"in n)n.innerHTML=i;else{for(oi=oi||document.createElement("div"),oi.innerHTML="<svg>"+i.valueOf().toString()+"</svg>",i=oi.firstChild;n.firstChild;)n.removeChild(n.firstChild);for(;i.firstChild;)n.appendChild(i.firstChild)}});function Di(n,i){if(i){var u=n.firstChild;if(u&&u===n.lastChild&&u.nodeType===3){u.nodeValue=i;return}}n.textContent=i}var we={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0},Qe=["Webkit","ms","Moz","O"];Object.keys(we).forEach(function(n){Qe.forEach(function(i){i=i+n.charAt(0).toUpperCase()+n.substring(1),we[i]=we[n]})});function wt(n,i,u){return i==null||typeof i=="boolean"||i===""?"":u||typeof i!="number"||i===0||we.hasOwnProperty(n)&&we[n]?(""+i).trim():i+"px"}function Gt(n,i){n=n.style;for(var u in i)if(i.hasOwnProperty(u)){var f=u.indexOf("--")===0,h=wt(u,i[u],f);u==="float"&&(u="cssFloat"),f?n.setProperty(u,h):n[u]=h}}var bn=be({menuitem:!0},{area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0});function wn(n,i){if(i){if(bn[n]&&(i.children!=null||i.dangerouslySetInnerHTML!=null))throw Error(s(137,n));if(i.dangerouslySetInnerHTML!=null){if(i.children!=null)throw Error(s(60));if(typeof i.dangerouslySetInnerHTML!="object"||!("__html"in i.dangerouslySetInnerHTML))throw Error(s(61))}if(i.style!=null&&typeof i.style!="object")throw Error(s(62))}}function Sn(n,i){if(n.indexOf("-")===-1)return typeof i.is=="string";switch(n){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var dr=null;function vn(n){return n=n.target||n.srcElement||window,n.correspondingUseElement&&(n=n.correspondingUseElement),n.nodeType===3?n.parentNode:n}var on=null,Kt=null,Mi=null;function qi(n){if(n=ic(n)){if(typeof on!="function")throw Error(s(280));var i=n.stateNode;i&&(i=yo(i),on(n.stateNode,n.type,i))}}function Xi(n){Kt?Mi?Mi.push(n):Mi=[n]:Kt=n}function io(){if(Kt){var n=Kt,i=Mi;if(Mi=Kt=null,qi(n),i)for(n=0;n<i.length;n++)qi(i[n])}}function wl(n,i){return n(i)}function Sl(){}var ao=!1;function Cl(n,i,u){if(ao)return n(i,u);ao=!0;try{return wl(n,i,u)}finally{ao=!1,(Kt!==null||Mi!==null)&&(Sl(),io())}}function ja(n,i){var u=n.stateNode;if(u===null)return null;var f=yo(u);if(f===null)return null;u=f[i];e:switch(i){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(f=!f.disabled)||(n=n.type,f=!(n==="button"||n==="input"||n==="select"||n==="textarea")),n=!f;break e;default:n=!1}if(n)return null;if(u&&typeof u!="function")throw Error(s(231,i,typeof u));return u}var Oi=!1;if(y)try{var br={};Object.defineProperty(br,"passive",{get:function(){Oi=!0}}),window.addEventListener("test",br,br),window.removeEventListener("test",br,br)}catch{Oi=!1}function $i(n,i,u,f,h,x,k,A,N){var J=Array.prototype.slice.call(arguments,3);try{i.apply(u,J)}catch(he){this.onError(he)}}var li=!1,_a=null,La=!1,oo=null,z={onError:function(n){li=!0,_a=n}};function de(n,i,u,f,h,x,k,A,N){li=!1,_a=null,$i.apply(z,arguments)}function Te(n,i,u,f,h,x,k,A,N){if(de.apply(this,arguments),li){if(li){var J=_a;li=!1,_a=null}else throw Error(s(198));La||(La=!0,oo=J)}}function De(n){var i=n,u=n;if(n.alternate)for(;i.return;)i=i.return;else{n=i;do i=n,i.flags&4098&&(u=i.return),n=i.return;while(n)}return i.tag===3?u:null}function Rt(n){if(n.tag===13){var i=n.memoizedState;if(i===null&&(n=n.alternate,n!==null&&(i=n.memoizedState)),i!==null)return i.dehydrated}return null}function ut(n){if(De(n)!==n)throw Error(s(188))}function $t(n){var i=n.alternate;if(!i){if(i=De(n),i===null)throw Error(s(188));return i!==n?null:n}for(var u=n,f=i;;){var h=u.return;if(h===null)break;var x=h.alternate;if(x===null){if(f=h.return,f!==null){u=f;continue}break}if(h.child===x.child){for(x=h.child;x;){if(x===u)return ut(h),n;if(x===f)return ut(h),i;x=x.sibling}throw Error(s(188))}if(u.return!==f.return)u=h,f=x;else{for(var k=!1,A=h.child;A;){if(A===u){k=!0,u=h,f=x;break}if(A===f){k=!0,f=h,u=x;break}A=A.sibling}if(!k){for(A=x.child;A;){if(A===u){k=!0,u=x,f=h;break}if(A===f){k=!0,f=x,u=h;break}A=A.sibling}if(!k)throw Error(s(189))}}if(u.alternate!==f)throw Error(s(190))}if(u.tag!==3)throw Error(s(188));return u.stateNode.current===u?n:i}function St(n){return n=$t(n),n!==null?Fn(n):null}function Fn(n){if(n.tag===5||n.tag===6)return n;for(n=n.child;n!==null;){var i=Fn(n);if(i!==null)return i;n=n.sibling}return null}var yn=r.unstable_scheduleCallback,Cn=r.unstable_cancelCallback,Lr=r.unstable_shouldYield,ma=r.unstable_requestPaint,Qt=r.unstable_now,kn=r.unstable_getCurrentPriorityLevel,gt=r.unstable_ImmediatePriority,za=r.unstable_UserBlockingPriority,lo=r.unstable_NormalPriority,gd=r.unstable_LowPriority,so=r.unstable_IdlePriority,_o=null,si=null;function Nu(n){if(si&&typeof si.onCommitFiberRoot=="function")try{si.onCommitFiberRoot(_o,n,void 0,(n.current.flags&128)===128)}catch{}}var Vr=Math.clz32?Math.clz32:vd,Pu=Math.log,md=Math.LN2;function vd(n){return n>>>=0,n===0?32:31-(Pu(n)/md|0)|0}var uo=64,Lo=4194304;function ui(n){switch(n&-n){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return n&4194240;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return n&130023424;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 1073741824;default:return n}}function Na(n,i){var u=n.pendingLanes;if(u===0)return 0;var f=0,h=n.suspendedLanes,x=n.pingedLanes,k=u&268435455;if(k!==0){var A=k&~h;A!==0?f=ui(A):(x&=k,x!==0&&(f=ui(x)))}else k=u&~h,k!==0?f=ui(k):x!==0&&(f=ui(x));if(f===0)return 0;if(i!==0&&i!==f&&!(i&h)&&(h=f&-f,x=i&-i,h>=x||h===16&&(x&4194240)!==0))return i;if(f&4&&(f|=u&16),i=n.entangledLanes,i!==0)for(n=n.entanglements,i&=f;0<i;)u=31-Vr(i),h=1<<u,f|=n[u],i&=~h;return f}function zo(n,i){switch(n){case 1:case 2:case 4:return i+250;case 8:case 16:case 32:case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return i+5e3;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return-1;case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function Cs(n,i){for(var u=n.suspendedLanes,f=n.pingedLanes,h=n.expirationTimes,x=n.pendingLanes;0<x;){var k=31-Vr(x),A=1<<k,N=h[k];N===-1?(!(A&u)||A&f)&&(h[k]=zo(A,i)):N<=i&&(n.expiredLanes|=A),x&=~A}}function co(n){return n=n.pendingLanes&-1073741825,n!==0?n:n&1073741824?1073741824:0}function El(){var n=uo;return uo<<=1,!(uo&4194240)&&(uo=64),n}function Tl(n){for(var i=[],u=0;31>u;u++)i.push(n);return i}function No(n,i,u){n.pendingLanes|=i,i!==536870912&&(n.suspendedLanes=0,n.pingedLanes=0),n=n.eventTimes,i=31-Vr(i),n[i]=u}function Fu(n,i){var u=n.pendingLanes&~i;n.pendingLanes=i,n.suspendedLanes=0,n.pingedLanes=0,n.expiredLanes&=i,n.mutableReadLanes&=i,n.entangledLanes&=i,i=n.entanglements;var f=n.eventTimes;for(n=n.expirationTimes;0<u;){var h=31-Vr(u),x=1<<h;i[h]=0,f[h]=-1,n[h]=-1,u&=~x}}function Iu(n,i){var u=n.entangledLanes|=i;for(n=n.entanglements;u;){var f=31-Vr(u),h=1<<f;h&i|n[f]&i&&(n[f]|=i),u&=~h}}var Nt=0;function Uu(n){return n&=-n,1<n?4<n?n&268435455?16:536870912:4:1}var Es,Pt,yd,Pa,lt,kl=!1,fr=[],ci=null,Wr=null,Fa=null,_n=new Map,ln=new Map,va=[],Ji="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");function zr(n,i){switch(n){case"focusin":case"focusout":ci=null;break;case"dragenter":case"dragleave":Wr=null;break;case"mouseover":case"mouseout":Fa=null;break;case"pointerover":case"pointerout":_n.delete(i.pointerId);break;case"gotpointercapture":case"lostpointercapture":ln.delete(i.pointerId)}}function Yr(n,i,u,f,h,x){return n===null||n.nativeEvent!==x?(n={blockedOn:i,domEventName:u,eventSystemFlags:f,nativeEvent:x,targetContainers:[h]},i!==null&&(i=ic(i),i!==null&&Pt(i)),n):(n.eventSystemFlags|=f,i=n.targetContainers,h!==null&&i.indexOf(h)===-1&&i.push(h),n)}function Cp(n,i,u,f,h){switch(i){case"focusin":return ci=Yr(ci,n,i,u,f,h),!0;case"dragenter":return Wr=Yr(Wr,n,i,u,f,h),!0;case"mouseover":return Fa=Yr(Fa,n,i,u,f,h),!0;case"pointerover":var x=h.pointerId;return _n.set(x,Yr(_n.get(x)||null,n,i,u,f,h)),!0;case"gotpointercapture":return x=h.pointerId,ln.set(x,Yr(ln.get(x)||null,n,i,u,f,h)),!0}return!1}function Ts(n){var i=Ll(n.target);if(i!==null){var u=De(i);if(u!==null){if(i=u.tag,i===13){if(i=Rt(u),i!==null){n.blockedOn=i,lt(n.priority,function(){yd(u)});return}}else if(i===3&&u.stateNode.current.memoizedState.isDehydrated){n.blockedOn=u.tag===3?u.stateNode.containerInfo:null;return}}}n.blockedOn=null}function Rl(n){if(n.blockedOn!==null)return!1;for(var i=n.targetContainers;0<i.length;){var u=Ms(n.domEventName,n.eventSystemFlags,i[0],n.nativeEvent);if(u===null){u=n.nativeEvent;var f=new u.constructor(u.type,u);dr=f,u.target.dispatchEvent(f),dr=null}else return i=ic(u),i!==null&&Pt(i),n.blockedOn=u,!1;i.shift()}return!0}function ks(n,i,u){Rl(n)&&u.delete(i)}function Rs(){kl=!1,ci!==null&&Rl(ci)&&(ci=null),Wr!==null&&Rl(Wr)&&(Wr=null),Fa!==null&&Rl(Fa)&&(Fa=null),_n.forEach(ks),ln.forEach(ks)}function Dl(n,i){n.blockedOn===i&&(n.blockedOn=null,kl||(kl=!0,r.unstable_scheduleCallback(r.unstable_NormalPriority,Rs)))}function Zi(n){function i(h){return Dl(h,n)}if(0<fr.length){Dl(fr[0],n);for(var u=1;u<fr.length;u++){var f=fr[u];f.blockedOn===n&&(f.blockedOn=null)}}for(ci!==null&&Dl(ci,n),Wr!==null&&Dl(Wr,n),Fa!==null&&Dl(Fa,n),_n.forEach(i),ln.forEach(i),u=0;u<va.length;u++)f=va[u],f.blockedOn===n&&(f.blockedOn=null);for(;0<va.length&&(u=va[0],u.blockedOn===null);)Ts(u),u.blockedOn===null&&va.shift()}var ea=ce.ReactCurrentBatchConfig,Po=!0;function fo(n,i,u,f){var h=Nt,x=ea.transition;ea.transition=null;try{Nt=1,Fo(n,i,u,f)}finally{Nt=h,ea.transition=x}}function Ds(n,i,u,f){var h=Nt,x=ea.transition;ea.transition=null;try{Nt=4,Fo(n,i,u,f)}finally{Nt=h,ea.transition=x}}function Fo(n,i,u,f){if(Po){var h=Ms(n,i,u,f);if(h===null)zp(n,i,f,po,u),zr(n,f);else if(Cp(h,n,i,u,f))f.stopPropagation();else if(zr(n,f),i&4&&-1<Ji.indexOf(n)){for(;h!==null;){var x=ic(h);if(x!==null&&Es(x),x=Ms(n,i,u,f),x===null&&zp(n,i,f,po,u),x===h)break;h=x}h!==null&&f.stopPropagation()}else zp(n,i,f,null,u)}}var po=null;function Ms(n,i,u,f){if(po=null,n=vn(f),n=Ll(n),n!==null)if(i=De(n),i===null)n=null;else if(u=i.tag,u===13){if(n=Rt(i),n!==null)return n;n=null}else if(u===3){if(i.stateNode.current.memoizedState.isDehydrated)return i.tag===3?i.stateNode.containerInfo:null;n=null}else i!==n&&(n=null);return po=n,null}function Bu(n){switch(n){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return 1;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return 4;case"message":switch(kn()){case gt:return 1;case za:return 4;case lo:case gd:return 16;case so:return 536870912;default:return 16}default:return 16}}var ta=null,Os=null,T=null;function _(){if(T)return T;var n,i=Os,u=i.length,f,h="value"in ta?ta.value:ta.textContent,x=h.length;for(n=0;n<u&&i[n]===h[n];n++);var k=u-n;for(f=1;f<=k&&i[u-f]===h[x-f];f++);return T=h.slice(n,1<f?1-f:void 0)}function q(n){var i=n.keyCode;return"charCode"in n?(n=n.charCode,n===0&&i===13&&(n=13)):n=i,n===10&&(n=13),32<=n||n===13?n:0}function ee(){return!0}function ye(){return!1}function Pe(n){function i(u,f,h,x,k){this._reactName=u,this._targetInst=h,this.type=f,this.nativeEvent=x,this.target=k,this.currentTarget=null;for(var A in n)n.hasOwnProperty(A)&&(u=n[A],this[A]=u?u(x):x[A]);return this.isDefaultPrevented=(x.defaultPrevented!=null?x.defaultPrevented:x.returnValue===!1)?ee:ye,this.isPropagationStopped=ye,this}return be(i.prototype,{preventDefault:function(){this.defaultPrevented=!0;var u=this.nativeEvent;u&&(u.preventDefault?u.preventDefault():typeof u.returnValue!="unknown"&&(u.returnValue=!1),this.isDefaultPrevented=ee)},stopPropagation:function(){var u=this.nativeEvent;u&&(u.stopPropagation?u.stopPropagation():typeof u.cancelBubble!="unknown"&&(u.cancelBubble=!0),this.isPropagationStopped=ee)},persist:function(){},isPersistent:ee}),i}var Ae={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(n){return n.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},at=Pe(Ae),Ct=be({},Ae,{view:0,detail:0}),qt=Pe(Ct),sn,un,yt,hn=be({},Ct,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:ya,button:0,buttons:0,relatedTarget:function(n){return n.relatedTarget===void 0?n.fromElement===n.srcElement?n.toElement:n.fromElement:n.relatedTarget},movementX:function(n){return"movementX"in n?n.movementX:(n!==yt&&(yt&&n.type==="mousemove"?(sn=n.screenX-yt.screenX,un=n.screenY-yt.screenY):un=sn=0,yt=n),sn)},movementY:function(n){return"movementY"in n?n.movementY:un}}),In=Pe(hn),Ml=be({},hn,{dataTransfer:0}),Hu=Pe(Ml),ho=be({},Ct,{relatedTarget:0}),Ol=Pe(ho),Vu=be({},Ae,{animationName:0,elapsedTime:0,pseudoElement:0}),Ep=Pe(Vu),xd=be({},Ae,{clipboardData:function(n){return"clipboardData"in n?n.clipboardData:window.clipboardData}}),Tp=Pe(xd),cm=be({},Ae,{data:0}),bd=Pe(cm),dm={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},fm={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},pm={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function S0(n){var i=this.nativeEvent;return i.getModifierState?i.getModifierState(n):(n=pm[n])?!!i[n]:!1}function ya(){return S0}var C0=be({},Ct,{key:function(n){if(n.key){var i=dm[n.key]||n.key;if(i!=="Unidentified")return i}return n.type==="keypress"?(n=q(n),n===13?"Enter":String.fromCharCode(n)):n.type==="keydown"||n.type==="keyup"?fm[n.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:ya,charCode:function(n){return n.type==="keypress"?q(n):0},keyCode:function(n){return n.type==="keydown"||n.type==="keyup"?n.keyCode:0},which:function(n){return n.type==="keypress"?q(n):n.type==="keydown"||n.type==="keyup"?n.keyCode:0}}),kp=Pe(C0),Rp=be({},hn,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),wd=Pe(Rp),E0=be({},Ct,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:ya}),Sd=Pe(E0),hm=be({},Ae,{propertyName:0,elapsedTime:0,pseudoElement:0}),di=Pe(hm),go=be({},hn,{deltaX:function(n){return"deltaX"in n?n.deltaX:"wheelDeltaX"in n?-n.wheelDeltaX:0},deltaY:function(n){return"deltaY"in n?n.deltaY:"wheelDeltaY"in n?-n.wheelDeltaY:"wheelDelta"in n?-n.wheelDelta:0},deltaZ:0,deltaMode:0}),Qn=Pe(go),mo=[9,13,27,32],Wu=y&&"CompositionEvent"in window,Io=null;y&&"documentMode"in document&&(Io=document.documentMode);var T0=y&&"TextEvent"in window&&!Io,$s=y&&(!Wu||Io&&8<Io&&11>=Io),gm=" ",mm=!1;function Cd(n,i){switch(n){case"keyup":return mo.indexOf(i.keyCode)!==-1;case"keydown":return i.keyCode!==229;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function vm(n){return n=n.detail,typeof n=="object"&&"data"in n?n.data:null}var As=!1;function k0(n,i){switch(n){case"compositionend":return vm(i);case"keypress":return i.which!==32?null:(mm=!0,gm);case"textInput":return n=i.data,n===gm&&mm?null:n;default:return null}}function ym(n,i){if(As)return n==="compositionend"||!Wu&&Cd(n,i)?(n=_(),T=Os=ta=null,As=!1,n):null;switch(n){case"paste":return null;case"keypress":if(!(i.ctrlKey||i.altKey||i.metaKey)||i.ctrlKey&&i.altKey){if(i.char&&1<i.char.length)return i.char;if(i.which)return String.fromCharCode(i.which)}return null;case"compositionend":return $s&&i.locale!=="ko"?null:i.data;default:return null}}var R0={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function xm(n){var i=n&&n.nodeName&&n.nodeName.toLowerCase();return i==="input"?!!R0[n.type]:i==="textarea"}function bm(n,i,u,f){Xi(f),i=tc(i,"onChange"),0<i.length&&(u=new at("onChange","change",null,u,f),n.push({event:u,listeners:i}))}var js=null,Ia=null;function Dp(n){Rd(n,0)}function Yu(n){var i=Ye(n);if(an(i))return n}function wm(n,i){if(n==="change")return i}var Sm=!1;if(y){var Mp;if(y){var Op="oninput"in document;if(!Op){var Cm=document.createElement("div");Cm.setAttribute("oninput","return;"),Op=typeof Cm.oninput=="function"}Mp=Op}else Mp=!1;Sm=Mp&&(!document.documentMode||9<document.documentMode)}function Em(){js&&(js.detachEvent("onpropertychange",Tm),Ia=js=null)}function Tm(n){if(n.propertyName==="value"&&Yu(Ia)){var i=[];bm(i,Ia,n,vn(n)),Cl(Dp,i)}}function D0(n,i,u){n==="focusin"?(Em(),js=i,Ia=u,js.attachEvent("onpropertychange",Tm)):n==="focusout"&&Em()}function M0(n){if(n==="selectionchange"||n==="keyup"||n==="keydown")return Yu(Ia)}function km(n,i){if(n==="click")return Yu(i)}function O0(n,i){if(n==="input"||n==="change")return Yu(i)}function Rm(n,i){return n===i&&(n!==0||1/n===1/i)||n!==n&&i!==i}var xa=typeof Object.is=="function"?Object.is:Rm;function Gu(n,i){if(xa(n,i))return!0;if(typeof n!="object"||n===null||typeof i!="object"||i===null)return!1;var u=Object.keys(n),f=Object.keys(i);if(u.length!==f.length)return!1;for(f=0;f<u.length;f++){var h=u[f];if(!E.call(i,h)||!xa(n[h],i[h]))return!1}return!0}function Dm(n){for(;n&&n.firstChild;)n=n.firstChild;return n}function Mm(n,i){var u=Dm(n);n=0;for(var f;u;){if(u.nodeType===3){if(f=n+u.textContent.length,n<=i&&f>=i)return{node:u,offset:i-n};n=f}e:{for(;u;){if(u.nextSibling){u=u.nextSibling;break e}u=u.parentNode}u=void 0}u=Dm(u)}}function Ed(n,i){return n&&i?n===i?!0:n&&n.nodeType===3?!1:i&&i.nodeType===3?Ed(n,i.parentNode):"contains"in n?n.contains(i):n.compareDocumentPosition?!!(n.compareDocumentPosition(i)&16):!1:!1}function Uo(){for(var n=window,i=Pn();i instanceof n.HTMLIFrameElement;){try{var u=typeof i.contentWindow.location.href=="string"}catch{u=!1}if(u)n=i.contentWindow;else break;i=Pn(n.document)}return i}function _s(n){var i=n&&n.nodeName&&n.nodeName.toLowerCase();return i&&(i==="input"&&(n.type==="text"||n.type==="search"||n.type==="tel"||n.type==="url"||n.type==="password")||i==="textarea"||n.contentEditable==="true")}function Om(n){var i=Uo(),u=n.focusedElem,f=n.selectionRange;if(i!==u&&u&&u.ownerDocument&&Ed(u.ownerDocument.documentElement,u)){if(f!==null&&_s(u)){if(i=f.start,n=f.end,n===void 0&&(n=i),"selectionStart"in u)u.selectionStart=i,u.selectionEnd=Math.min(n,u.value.length);else if(n=(i=u.ownerDocument||document)&&i.defaultView||window,n.getSelection){n=n.getSelection();var h=u.textContent.length,x=Math.min(f.start,h);f=f.end===void 0?x:Math.min(f.end,h),!n.extend&&x>f&&(h=f,f=x,x=h),h=Mm(u,x);var k=Mm(u,f);h&&k&&(n.rangeCount!==1||n.anchorNode!==h.node||n.anchorOffset!==h.offset||n.focusNode!==k.node||n.focusOffset!==k.offset)&&(i=i.createRange(),i.setStart(h.node,h.offset),n.removeAllRanges(),x>f?(n.addRange(i),n.extend(k.node,k.offset)):(i.setEnd(k.node,k.offset),n.addRange(i)))}}for(i=[],n=u;n=n.parentNode;)n.nodeType===1&&i.push({element:n,left:n.scrollLeft,top:n.scrollTop});for(typeof u.focus=="function"&&u.focus(),u=0;u<i.length;u++)n=i[u],n.element.scrollLeft=n.left,n.element.scrollTop=n.top}}var Ls=y&&"documentMode"in document&&11>=document.documentMode,zs=null,$p=null,Ku=null,Ap=!1;function $m(n,i,u){var f=u.window===u?u.document:u.nodeType===9?u:u.ownerDocument;Ap||zs==null||zs!==Pn(f)||(f=zs,"selectionStart"in f&&_s(f)?f={start:f.selectionStart,end:f.selectionEnd}:(f=(f.ownerDocument&&f.ownerDocument.defaultView||window).getSelection(),f={anchorNode:f.anchorNode,anchorOffset:f.anchorOffset,focusNode:f.focusNode,focusOffset:f.focusOffset}),Ku&&Gu(Ku,f)||(Ku=f,f=tc($p,"onSelect"),0<f.length&&(i=new at("onSelect","select",null,i,u),n.push({event:i,listeners:f}),i.target=zs)))}function Qu(n,i){var u={};return u[n.toLowerCase()]=i.toLowerCase(),u["Webkit"+n]="webkit"+i,u["Moz"+n]="moz"+i,u}var Ns={animationend:Qu("Animation","AnimationEnd"),animationiteration:Qu("Animation","AnimationIteration"),animationstart:Qu("Animation","AnimationStart"),transitionend:Qu("Transition","TransitionEnd")},Td={},Nr={};y&&(Nr=document.createElement("div").style,"AnimationEvent"in window||(delete Ns.animationend.animation,delete Ns.animationiteration.animation,delete Ns.animationstart.animation),"TransitionEvent"in window||delete Ns.transitionend.transition);function qu(n){if(Td[n])return Td[n];if(!Ns[n])return n;var i=Ns[n],u;for(u in i)if(i.hasOwnProperty(u)&&u in Nr)return Td[n]=i[u];return n}var Am=qu("animationend"),jm=qu("animationiteration"),_m=qu("animationstart"),Lm=qu("transitionend"),zm=new Map,jp="abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");function Ua(n,i){zm.set(n,i),b(i,[n])}for(var $l=0;$l<jp.length;$l++){var _p=jp[$l],Xu=_p.toLowerCase(),$0=_p[0].toUpperCase()+_p.slice(1);Ua(Xu,"on"+$0)}Ua(Am,"onAnimationEnd"),Ua(jm,"onAnimationIteration"),Ua(_m,"onAnimationStart"),Ua("dblclick","onDoubleClick"),Ua("focusin","onFocus"),Ua("focusout","onBlur"),Ua(Lm,"onTransitionEnd"),S("onMouseEnter",["mouseout","mouseover"]),S("onMouseLeave",["mouseout","mouseover"]),S("onPointerEnter",["pointerout","pointerover"]),S("onPointerLeave",["pointerout","pointerover"]),b("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),b("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),b("onBeforeInput",["compositionend","keypress","textInput","paste"]),b("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),b("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),b("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var Ju="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),A0=new Set("cancel close invalid load scroll toggle".split(" ").concat(Ju));function kd(n,i,u){var f=n.type||"unknown-event";n.currentTarget=u,Te(f,i,void 0,n),n.currentTarget=null}function Rd(n,i){i=(i&4)!==0;for(var u=0;u<n.length;u++){var f=n[u],h=f.event;f=f.listeners;e:{var x=void 0;if(i)for(var k=f.length-1;0<=k;k--){var A=f[k],N=A.instance,J=A.currentTarget;if(A=A.listener,N!==x&&h.isPropagationStopped())break e;kd(h,A,J),x=N}else for(k=0;k<f.length;k++){if(A=f[k],N=A.instance,J=A.currentTarget,A=A.listener,N!==x&&h.isPropagationStopped())break e;kd(h,A,J),x=N}}}if(La)throw n=oo,La=!1,oo=null,n}function Xt(n,i){var u=i[Np];u===void 0&&(u=i[Np]=new Set);var f=n+"__bubble";u.has(f)||(Lp(i,n,2,!1),u.add(f))}function Bo(n,i,u){var f=0;i&&(f|=4),Lp(u,n,f,i)}var Zu="_reactListening"+Math.random().toString(36).slice(2);function ec(n){if(!n[Zu]){n[Zu]=!0,d.forEach(function(u){u!=="selectionchange"&&(A0.has(u)||Bo(u,!1,n),Bo(u,!0,n))});var i=n.nodeType===9?n:n.ownerDocument;i===null||i[Zu]||(i[Zu]=!0,Bo("selectionchange",!1,i))}}function Lp(n,i,u,f){switch(Bu(i)){case 1:var h=fo;break;case 4:h=Ds;break;default:h=Fo}u=h.bind(null,i,u,n),h=void 0,!Oi||i!=="touchstart"&&i!=="touchmove"&&i!=="wheel"||(h=!0),f?h!==void 0?n.addEventListener(i,u,{capture:!0,passive:h}):n.addEventListener(i,u,!0):h!==void 0?n.addEventListener(i,u,{passive:h}):n.addEventListener(i,u,!1)}function zp(n,i,u,f,h){var x=f;if(!(i&1)&&!(i&2)&&f!==null)e:for(;;){if(f===null)return;var k=f.tag;if(k===3||k===4){var A=f.stateNode.containerInfo;if(A===h||A.nodeType===8&&A.parentNode===h)break;if(k===4)for(k=f.return;k!==null;){var N=k.tag;if((N===3||N===4)&&(N=k.stateNode.containerInfo,N===h||N.nodeType===8&&N.parentNode===h))return;k=k.return}for(;A!==null;){if(k=Ll(A),k===null)return;if(N=k.tag,N===5||N===6){f=x=k;continue e}A=A.parentNode}}f=f.return}Cl(function(){var J=x,he=vn(u),ge=[];e:{var pe=zm.get(n);if(pe!==void 0){var je=at,Fe=n;switch(n){case"keypress":if(q(u)===0)break e;case"keydown":case"keyup":je=kp;break;case"focusin":Fe="focus",je=Ol;break;case"focusout":Fe="blur",je=Ol;break;case"beforeblur":case"afterblur":je=Ol;break;case"click":if(u.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":je=In;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":je=Hu;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":je=Sd;break;case Am:case jm:case _m:je=Ep;break;case Lm:je=di;break;case"scroll":je=qt;break;case"wheel":je=Qn;break;case"copy":case"cut":case"paste":je=Tp;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":je=wd}var Ue=(i&4)!==0,Vn=!Ue&&n==="scroll",W=Ue?pe!==null?pe+"Capture":null:pe;Ue=[];for(var I=J,K;I!==null;){K=I;var ve=K.stateNode;if(K.tag===5&&ve!==null&&(K=ve,W!==null&&(ve=ja(I,W),ve!=null&&Ue.push(Ps(I,ve,K)))),Vn)break;I=I.return}0<Ue.length&&(pe=new je(pe,Fe,null,u,he),ge.push({event:pe,listeners:Ue}))}}if(!(i&7)){e:{if(pe=n==="mouseover"||n==="pointerover",je=n==="mouseout"||n==="pointerout",pe&&u!==dr&&(Fe=u.relatedTarget||u.fromElement)&&(Ll(Fe)||Fe[vo]))break e;if((je||pe)&&(pe=he.window===he?he:(pe=he.ownerDocument)?pe.defaultView||pe.parentWindow:window,je?(Fe=u.relatedTarget||u.toElement,je=J,Fe=Fe?Ll(Fe):null,Fe!==null&&(Vn=De(Fe),Fe!==Vn||Fe.tag!==5&&Fe.tag!==6)&&(Fe=null)):(je=null,Fe=J),je!==Fe)){if(Ue=In,ve="onMouseLeave",W="onMouseEnter",I="mouse",(n==="pointerout"||n==="pointerover")&&(Ue=wd,ve="onPointerLeave",W="onPointerEnter",I="pointer"),Vn=je==null?pe:Ye(je),K=Fe==null?pe:Ye(Fe),pe=new Ue(ve,I+"leave",je,u,he),pe.target=Vn,pe.relatedTarget=K,ve=null,Ll(he)===J&&(Ue=new Ue(W,I+"enter",Fe,u,he),Ue.target=K,Ue.relatedTarget=Vn,ve=Ue),Vn=ve,je&&Fe)t:{for(Ue=je,W=Fe,I=0,K=Ue;K;K=Al(K))I++;for(K=0,ve=W;ve;ve=Al(ve))K++;for(;0<I-K;)Ue=Al(Ue),I--;for(;0<K-I;)W=Al(W),K--;for(;I--;){if(Ue===W||W!==null&&Ue===W.alternate)break t;Ue=Al(Ue),W=Al(W)}Ue=null}else Ue=null;je!==null&&Dd(ge,pe,je,Ue,!1),Fe!==null&&Vn!==null&&Dd(ge,Vn,Fe,Ue,!0)}}e:{if(pe=J?Ye(J):window,je=pe.nodeName&&pe.nodeName.toLowerCase(),je==="select"||je==="input"&&pe.type==="file")var Me=wm;else if(xm(pe))if(Sm)Me=O0;else{Me=M0;var Ke=D0}else(je=pe.nodeName)&&je.toLowerCase()==="input"&&(pe.type==="checkbox"||pe.type==="radio")&&(Me=km);if(Me&&(Me=Me(n,J))){bm(ge,Me,u,he);break e}Ke&&Ke(n,pe,J),n==="focusout"&&(Ke=pe._wrapperState)&&Ke.controlled&&pe.type==="number"&&ha(pe,"number",pe.value)}switch(Ke=J?Ye(J):window,n){case"focusin":(xm(Ke)||Ke.contentEditable==="true")&&(zs=Ke,$p=J,Ku=null);break;case"focusout":Ku=$p=zs=null;break;case"mousedown":Ap=!0;break;case"contextmenu":case"mouseup":case"dragend":Ap=!1,$m(ge,u,he);break;case"selectionchange":if(Ls)break;case"keydown":case"keyup":$m(ge,u,he)}var Ze;if(Wu)e:{switch(n){case"compositionstart":var st="onCompositionStart";break e;case"compositionend":st="onCompositionEnd";break e;case"compositionupdate":st="onCompositionUpdate";break e}st=void 0}else As?Cd(n,u)&&(st="onCompositionEnd"):n==="keydown"&&u.keyCode===229&&(st="onCompositionStart");st&&($s&&u.locale!=="ko"&&(As||st!=="onCompositionStart"?st==="onCompositionEnd"&&As&&(Ze=_()):(ta=he,Os="value"in ta?ta.value:ta.textContent,As=!0)),Ke=tc(J,st),0<Ke.length&&(st=new bd(st,n,null,u,he),ge.push({event:st,listeners:Ke}),Ze?st.data=Ze:(Ze=vm(u),Ze!==null&&(st.data=Ze)))),(Ze=T0?k0(n,u):ym(n,u))&&(J=tc(J,"onBeforeInput"),0<J.length&&(he=new bd("onBeforeInput","beforeinput",null,u,he),ge.push({event:he,listeners:J}),he.data=Ze))}Rd(ge,i)})}function Ps(n,i,u){return{instance:n,listener:i,currentTarget:u}}function tc(n,i){for(var u=i+"Capture",f=[];n!==null;){var h=n,x=h.stateNode;h.tag===5&&x!==null&&(h=x,x=ja(n,u),x!=null&&f.unshift(Ps(n,x,h)),x=ja(n,i),x!=null&&f.push(Ps(n,x,h))),n=n.return}return f}function Al(n){if(n===null)return null;do n=n.return;while(n&&n.tag!==5);return n||null}function Dd(n,i,u,f,h){for(var x=i._reactName,k=[];u!==null&&u!==f;){var A=u,N=A.alternate,J=A.stateNode;if(N!==null&&N===f)break;A.tag===5&&J!==null&&(A=J,h?(N=ja(u,x),N!=null&&k.unshift(Ps(u,N,A))):h||(N=ja(u,x),N!=null&&k.push(Ps(u,N,A)))),u=u.return}k.length!==0&&n.push({event:i,listeners:k})}var j0=/\r\n?/g,Nm=/\u0000|\uFFFD/g;function Pm(n){return(typeof n=="string"?n:""+n).replace(j0,`
`).replace(Nm,"")}function Md(n,i,u){if(i=Pm(i),Pm(n)!==i&&u)throw Error(s(425))}function Od(){}var jl=null,nc=null;function _l(n,i){return n==="textarea"||n==="noscript"||typeof i.children=="string"||typeof i.children=="number"||typeof i.dangerouslySetInnerHTML=="object"&&i.dangerouslySetInnerHTML!==null&&i.dangerouslySetInnerHTML.__html!=null}var $d=typeof setTimeout=="function"?setTimeout:void 0,Fm=typeof clearTimeout=="function"?clearTimeout:void 0,Ad=typeof Promise=="function"?Promise:void 0,_0=typeof queueMicrotask=="function"?queueMicrotask:typeof Ad<"u"?function(n){return Ad.resolve(null).then(n).catch(Fs)}:$d;function Fs(n){setTimeout(function(){throw n})}function Is(n,i){var u=i,f=0;do{var h=u.nextSibling;if(n.removeChild(u),h&&h.nodeType===8)if(u=h.data,u==="/$"){if(f===0){n.removeChild(h),Zi(i);return}f--}else u!=="$"&&u!=="$?"&&u!=="$!"||f++;u=h}while(u);Zi(i)}function ba(n){for(;n!=null;n=n.nextSibling){var i=n.nodeType;if(i===1||i===3)break;if(i===8){if(i=n.data,i==="$"||i==="$!"||i==="$?")break;if(i==="/$")return null}}return n}function jd(n){n=n.previousSibling;for(var i=0;n;){if(n.nodeType===8){var u=n.data;if(u==="$"||u==="$!"||u==="$?"){if(i===0)return n;i--}else u==="/$"&&i++}n=n.previousSibling}return null}var Us=Math.random().toString(36).slice(2),na="__reactFiber$"+Us,rc="__reactProps$"+Us,vo="__reactContainer$"+Us,Np="__reactEvents$"+Us,Pp="__reactListeners$"+Us,Bs="__reactHandles$"+Us;function Ll(n){var i=n[na];if(i)return i;for(var u=n.parentNode;u;){if(i=u[vo]||u[na]){if(u=i.alternate,i.child!==null||u!==null&&u.child!==null)for(n=jd(n);n!==null;){if(u=n[na])return u;n=jd(n)}return i}n=u,u=n.parentNode}return null}function ic(n){return n=n[na]||n[vo],!n||n.tag!==5&&n.tag!==6&&n.tag!==13&&n.tag!==3?null:n}function Ye(n){if(n.tag===5||n.tag===6)return n.stateNode;throw Error(s(33))}function yo(n){return n[rc]||null}var Ln=[],At=-1;function fi(n){return{current:n}}function tn(n){0>At||(n.current=Ln[At],Ln[At]=null,At--)}function gn(n,i){At++,Ln[At]=n.current,n.current=i}var Et={},Rn=fi(Et),qn=fi(!1),ra=Et;function Ai(n,i){var u=n.type.contextTypes;if(!u)return Et;var f=n.stateNode;if(f&&f.__reactInternalMemoizedUnmaskedChildContext===i)return f.__reactInternalMemoizedMaskedChildContext;var h={},x;for(x in u)h[x]=i[x];return f&&(n=n.stateNode,n.__reactInternalMemoizedUnmaskedChildContext=i,n.__reactInternalMemoizedMaskedChildContext=h),h}function zn(n){return n=n.childContextTypes,n!=null}function Ba(){tn(qn),tn(Rn)}function _d(n,i,u){if(Rn.current!==Et)throw Error(s(168));gn(Rn,i),gn(qn,u)}function Im(n,i,u){var f=n.stateNode;if(i=i.childContextTypes,typeof f.getChildContext!="function")return u;f=f.getChildContext();for(var h in f)if(!(h in i))throw Error(s(108,Ot(n)||"Unknown",h));return be({},u,f)}function zl(n){return n=(n=n.stateNode)&&n.__reactInternalMemoizedMergedChildContext||Et,ra=Rn.current,gn(Rn,n),gn(qn,qn.current),!0}function Pr(n,i,u){var f=n.stateNode;if(!f)throw Error(s(169));u?(n=Im(n,i,ra),f.__reactInternalMemoizedMergedChildContext=n,tn(qn),tn(Rn),gn(Rn,n)):tn(qn),gn(qn,u)}var wa=null,ac=!1,oc=!1;function Ho(n){wa===null?wa=[n]:wa.push(n)}function Fp(n){ac=!0,Ho(n)}function Gr(){if(!oc&&wa!==null){oc=!0;var n=0,i=Nt;try{var u=wa;for(Nt=1;n<u.length;n++){var f=u[n];do f=f(!0);while(f!==null)}wa=null,ac=!1}catch(h){throw wa!==null&&(wa=wa.slice(n+1)),yn(gt,Gr),h}finally{Nt=i,oc=!1}}return null}var Vo=[],Wo=0,Hs=null,Yo=0,wr=[],Xn=0,Nl=null,Kr=1,Ha="";function Go(n,i){Vo[Wo++]=Yo,Vo[Wo++]=Hs,Hs=n,Yo=i}function Um(n,i,u){wr[Xn++]=Kr,wr[Xn++]=Ha,wr[Xn++]=Nl,Nl=n;var f=Kr;n=Ha;var h=32-Vr(f)-1;f&=~(1<<h),u+=1;var x=32-Vr(i)+h;if(30<x){var k=h-h%5;x=(f&(1<<k)-1).toString(32),f>>=k,h-=k,Kr=1<<32-Vr(i)+h|u<<h|f,Ha=x+n}else Kr=1<<x|u<<h|f,Ha=n}function Ip(n){n.return!==null&&(Go(n,1),Um(n,1,0))}function Ld(n){for(;n===Hs;)Hs=Vo[--Wo],Vo[Wo]=null,Yo=Vo[--Wo],Vo[Wo]=null;for(;n===Nl;)Nl=wr[--Xn],wr[Xn]=null,Ha=wr[--Xn],wr[Xn]=null,Kr=wr[--Xn],wr[Xn]=null}var pi=null,hi=null,En=!1,Sa=null;function Up(n,i){var u=sa(5,null,null,0);u.elementType="DELETED",u.stateNode=i,u.return=n,i=n.deletions,i===null?(n.deletions=[u],n.flags|=16):i.push(u)}function Bp(n,i){switch(n.tag){case 5:var u=n.type;return i=i.nodeType!==1||u.toLowerCase()!==i.nodeName.toLowerCase()?null:i,i!==null?(n.stateNode=i,pi=n,hi=ba(i.firstChild),!0):!1;case 6:return i=n.pendingProps===""||i.nodeType!==3?null:i,i!==null?(n.stateNode=i,pi=n,hi=null,!0):!1;case 13:return i=i.nodeType!==8?null:i,i!==null?(u=Nl!==null?{id:Kr,overflow:Ha}:null,n.memoizedState={dehydrated:i,treeContext:u,retryLane:1073741824},u=sa(18,null,null,0),u.stateNode=i,u.return=n,n.child=u,pi=n,hi=null,!0):!1;default:return!1}}function Hp(n){return(n.mode&1)!==0&&(n.flags&128)===0}function Vp(n){if(En){var i=hi;if(i){var u=i;if(!Bp(n,i)){if(Hp(n))throw Error(s(418));i=ba(u.nextSibling);var f=pi;i&&Bp(n,i)?Up(f,u):(n.flags=n.flags&-4097|2,En=!1,pi=n)}}else{if(Hp(n))throw Error(s(418));n.flags=n.flags&-4097|2,En=!1,pi=n}}}function Bm(n){for(n=n.return;n!==null&&n.tag!==5&&n.tag!==3&&n.tag!==13;)n=n.return;pi=n}function Un(n){if(n!==pi)return!1;if(!En)return Bm(n),En=!0,!1;var i;if((i=n.tag!==3)&&!(i=n.tag!==5)&&(i=n.type,i=i!=="head"&&i!=="body"&&!_l(n.type,n.memoizedProps)),i&&(i=hi)){if(Hp(n))throw Hm(),Error(s(418));for(;i;)Up(n,i),i=ba(i.nextSibling)}if(Bm(n),n.tag===13){if(n=n.memoizedState,n=n!==null?n.dehydrated:null,!n)throw Error(s(317));e:{for(n=n.nextSibling,i=0;n;){if(n.nodeType===8){var u=n.data;if(u==="/$"){if(i===0){hi=ba(n.nextSibling);break e}i--}else u!=="$"&&u!=="$!"&&u!=="$?"||i++}n=n.nextSibling}hi=null}}else hi=pi?ba(n.stateNode.nextSibling):null;return!0}function Hm(){for(var n=hi;n;)n=ba(n.nextSibling)}function xo(){hi=pi=null,En=!1}function lc(n){Sa===null?Sa=[n]:Sa.push(n)}var Pl=ce.ReactCurrentBatchConfig;function sc(n,i,u){if(n=u.ref,n!==null&&typeof n!="function"&&typeof n!="object"){if(u._owner){if(u=u._owner,u){if(u.tag!==1)throw Error(s(309));var f=u.stateNode}if(!f)throw Error(s(147,n));var h=f,x=""+n;return i!==null&&i.ref!==null&&typeof i.ref=="function"&&i.ref._stringRef===x?i.ref:(i=function(k){var A=h.refs;k===null?delete A[x]:A[x]=k},i._stringRef=x,i)}if(typeof n!="string")throw Error(s(284));if(!u._owner)throw Error(s(290,n))}return n}function Vs(n,i){throw n=Object.prototype.toString.call(i),Error(s(31,n==="[object Object]"?"object with keys {"+Object.keys(i).join(", ")+"}":n))}function Vm(n){var i=n._init;return i(n._payload)}function Wm(n){function i(W,I){if(n){var K=W.deletions;K===null?(W.deletions=[I],W.flags|=16):K.push(I)}}function u(W,I){if(!n)return null;for(;I!==null;)i(W,I),I=I.sibling;return null}function f(W,I){for(W=new Map;I!==null;)I.key!==null?W.set(I.key,I):W.set(I.index,I),I=I.sibling;return W}function h(W,I){return W=il(W,I),W.index=0,W.sibling=null,W}function x(W,I,K){return W.index=K,n?(K=W.alternate,K!==null?(K=K.index,K<I?(W.flags|=2,I):K):(W.flags|=2,I)):(W.flags|=1048576,I)}function k(W){return n&&W.alternate===null&&(W.flags|=2),W}function A(W,I,K,ve){return I===null||I.tag!==6?(I=ts(K,W.mode,ve),I.return=W,I):(I=h(I,K),I.return=W,I)}function N(W,I,K,ve){var Me=K.type;return Me===ue?he(W,I,K.props.children,ve,K.key):I!==null&&(I.elementType===Me||typeof Me=="object"&&Me!==null&&Me.$$typeof===kt&&Vm(Me)===I.type)?(ve=h(I,K.props),ve.ref=sc(W,I,K),ve.return=W,ve):(ve=wf(K.type,K.key,K.props,null,W.mode,ve),ve.ref=sc(W,I,K),ve.return=W,ve)}function J(W,I,K,ve){return I===null||I.tag!==4||I.stateNode.containerInfo!==K.containerInfo||I.stateNode.implementation!==K.implementation?(I=wh(K,W.mode,ve),I.return=W,I):(I=h(I,K.children||[]),I.return=W,I)}function he(W,I,K,ve,Me){return I===null||I.tag!==7?(I=al(K,W.mode,ve,Me),I.return=W,I):(I=h(I,K),I.return=W,I)}function ge(W,I,K){if(typeof I=="string"&&I!==""||typeof I=="number")return I=ts(""+I,W.mode,K),I.return=W,I;if(typeof I=="object"&&I!==null){switch(I.$$typeof){case Ee:return K=wf(I.type,I.key,I.props,null,W.mode,K),K.ref=sc(W,null,I),K.return=W,K;case le:return I=wh(I,W.mode,K),I.return=W,I;case kt:var ve=I._init;return ge(W,ve(I._payload),K)}if(Hr(I)||ke(I))return I=al(I,W.mode,K,null),I.return=W,I;Vs(W,I)}return null}function pe(W,I,K,ve){var Me=I!==null?I.key:null;if(typeof K=="string"&&K!==""||typeof K=="number")return Me!==null?null:A(W,I,""+K,ve);if(typeof K=="object"&&K!==null){switch(K.$$typeof){case Ee:return K.key===Me?N(W,I,K,ve):null;case le:return K.key===Me?J(W,I,K,ve):null;case kt:return Me=K._init,pe(W,I,Me(K._payload),ve)}if(Hr(K)||ke(K))return Me!==null?null:he(W,I,K,ve,null);Vs(W,K)}return null}function je(W,I,K,ve,Me){if(typeof ve=="string"&&ve!==""||typeof ve=="number")return W=W.get(K)||null,A(I,W,""+ve,Me);if(typeof ve=="object"&&ve!==null){switch(ve.$$typeof){case Ee:return W=W.get(ve.key===null?K:ve.key)||null,N(I,W,ve,Me);case le:return W=W.get(ve.key===null?K:ve.key)||null,J(I,W,ve,Me);case kt:var Ke=ve._init;return je(W,I,K,Ke(ve._payload),Me)}if(Hr(ve)||ke(ve))return W=W.get(K)||null,he(I,W,ve,Me,null);Vs(I,ve)}return null}function Fe(W,I,K,ve){for(var Me=null,Ke=null,Ze=I,st=I=0,or=null;Ze!==null&&st<K.length;st++){Ze.index>st?(or=Ze,Ze=null):or=Ze.sibling;var Vt=pe(W,Ze,K[st],ve);if(Vt===null){Ze===null&&(Ze=or);break}n&&Ze&&Vt.alternate===null&&i(W,Ze),I=x(Vt,I,st),Ke===null?Me=Vt:Ke.sibling=Vt,Ke=Vt,Ze=or}if(st===K.length)return u(W,Ze),En&&Go(W,st),Me;if(Ze===null){for(;st<K.length;st++)Ze=ge(W,K[st],ve),Ze!==null&&(I=x(Ze,I,st),Ke===null?Me=Ze:Ke.sibling=Ze,Ke=Ze);return En&&Go(W,st),Me}for(Ze=f(W,Ze);st<K.length;st++)or=je(Ze,W,st,K[st],ve),or!==null&&(n&&or.alternate!==null&&Ze.delete(or.key===null?st:or.key),I=x(or,I,st),Ke===null?Me=or:Ke.sibling=or,Ke=or);return n&&Ze.forEach(function(ll){return i(W,ll)}),En&&Go(W,st),Me}function Ue(W,I,K,ve){var Me=ke(K);if(typeof Me!="function")throw Error(s(150));if(K=Me.call(K),K==null)throw Error(s(151));for(var Ke=Me=null,Ze=I,st=I=0,or=null,Vt=K.next();Ze!==null&&!Vt.done;st++,Vt=K.next()){Ze.index>st?(or=Ze,Ze=null):or=Ze.sibling;var ll=pe(W,Ze,Vt.value,ve);if(ll===null){Ze===null&&(Ze=or);break}n&&Ze&&ll.alternate===null&&i(W,Ze),I=x(ll,I,st),Ke===null?Me=ll:Ke.sibling=ll,Ke=ll,Ze=or}if(Vt.done)return u(W,Ze),En&&Go(W,st),Me;if(Ze===null){for(;!Vt.done;st++,Vt=K.next())Vt=ge(W,Vt.value,ve),Vt!==null&&(I=x(Vt,I,st),Ke===null?Me=Vt:Ke.sibling=Vt,Ke=Vt);return En&&Go(W,st),Me}for(Ze=f(W,Ze);!Vt.done;st++,Vt=K.next())Vt=je(Ze,W,st,Vt.value,ve),Vt!==null&&(n&&Vt.alternate!==null&&Ze.delete(Vt.key===null?st:Vt.key),I=x(Vt,I,st),Ke===null?Me=Vt:Ke.sibling=Vt,Ke=Vt);return n&&Ze.forEach(function(K0){return i(W,K0)}),En&&Go(W,st),Me}function Vn(W,I,K,ve){if(typeof K=="object"&&K!==null&&K.type===ue&&K.key===null&&(K=K.props.children),typeof K=="object"&&K!==null){switch(K.$$typeof){case Ee:e:{for(var Me=K.key,Ke=I;Ke!==null;){if(Ke.key===Me){if(Me=K.type,Me===ue){if(Ke.tag===7){u(W,Ke.sibling),I=h(Ke,K.props.children),I.return=W,W=I;break e}}else if(Ke.elementType===Me||typeof Me=="object"&&Me!==null&&Me.$$typeof===kt&&Vm(Me)===Ke.type){u(W,Ke.sibling),I=h(Ke,K.props),I.ref=sc(W,Ke,K),I.return=W,W=I;break e}u(W,Ke);break}else i(W,Ke);Ke=Ke.sibling}K.type===ue?(I=al(K.props.children,W.mode,ve,K.key),I.return=W,W=I):(ve=wf(K.type,K.key,K.props,null,W.mode,ve),ve.ref=sc(W,I,K),ve.return=W,W=ve)}return k(W);case le:e:{for(Ke=K.key;I!==null;){if(I.key===Ke)if(I.tag===4&&I.stateNode.containerInfo===K.containerInfo&&I.stateNode.implementation===K.implementation){u(W,I.sibling),I=h(I,K.children||[]),I.return=W,W=I;break e}else{u(W,I);break}else i(W,I);I=I.sibling}I=wh(K,W.mode,ve),I.return=W,W=I}return k(W);case kt:return Ke=K._init,Vn(W,I,Ke(K._payload),ve)}if(Hr(K))return Fe(W,I,K,ve);if(ke(K))return Ue(W,I,K,ve);Vs(W,K)}return typeof K=="string"&&K!==""||typeof K=="number"?(K=""+K,I!==null&&I.tag===6?(u(W,I.sibling),I=h(I,K),I.return=W,W=I):(u(W,I),I=ts(K,W.mode,ve),I.return=W,W=I),k(W)):u(W,I)}return Vn}var Ca=Wm(!0),Sr=Wm(!1),Ce=fi(null),ji=null,Fr=null,Wp=null;function Yp(){Wp=Fr=ji=null}function Gp(n){var i=Ce.current;tn(Ce),n._currentValue=i}function Kp(n,i,u){for(;n!==null;){var f=n.alternate;if((n.childLanes&i)!==i?(n.childLanes|=i,f!==null&&(f.childLanes|=i)):f!==null&&(f.childLanes&i)!==i&&(f.childLanes|=i),n===u)break;n=n.return}}function Ws(n,i){ji=n,Wp=Fr=null,n=n.dependencies,n!==null&&n.firstContext!==null&&(n.lanes&i&&(gr=!0),n.firstContext=null)}function nn(n){var i=n._currentValue;if(Wp!==n)if(n={context:n,memoizedValue:i,next:null},Fr===null){if(ji===null)throw Error(s(308));Fr=n,ji.dependencies={lanes:0,firstContext:n}}else Fr=Fr.next=n;return i}var Fl=null;function Qp(n){Fl===null?Fl=[n]:Fl.push(n)}function Ym(n,i,u,f){var h=i.interleaved;return h===null?(u.next=u,Qp(i)):(u.next=h.next,h.next=u),i.interleaved=u,Va(n,f)}function Va(n,i){n.lanes|=i;var u=n.alternate;for(u!==null&&(u.lanes|=i),u=n,n=n.return;n!==null;)n.childLanes|=i,u=n.alternate,u!==null&&(u.childLanes|=i),u=n,n=n.return;return u.tag===3?u.stateNode:null}var ia=!1;function Ko(n){n.updateQueue={baseState:n.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:0},effects:null}}function Gm(n,i){n=n.updateQueue,i.updateQueue===n&&(i.updateQueue={baseState:n.baseState,firstBaseUpdate:n.firstBaseUpdate,lastBaseUpdate:n.lastBaseUpdate,shared:n.shared,effects:n.effects})}function bo(n,i){return{eventTime:n,lane:i,tag:0,payload:null,callback:null,next:null}}function Qo(n,i,u){var f=n.updateQueue;if(f===null)return null;if(f=f.shared,jt&2){var h=f.pending;return h===null?i.next=i:(i.next=h.next,h.next=i),f.pending=i,Va(n,u)}return h=f.interleaved,h===null?(i.next=i,Qp(f)):(i.next=h.next,h.next=i),f.interleaved=i,Va(n,u)}function zd(n,i,u){if(i=i.updateQueue,i!==null&&(i=i.shared,(u&4194240)!==0)){var f=i.lanes;f&=n.pendingLanes,u|=f,i.lanes=u,Iu(n,u)}}function Km(n,i){var u=n.updateQueue,f=n.alternate;if(f!==null&&(f=f.updateQueue,u===f)){var h=null,x=null;if(u=u.firstBaseUpdate,u!==null){do{var k={eventTime:u.eventTime,lane:u.lane,tag:u.tag,payload:u.payload,callback:u.callback,next:null};x===null?h=x=k:x=x.next=k,u=u.next}while(u!==null);x===null?h=x=i:x=x.next=i}else h=x=i;u={baseState:f.baseState,firstBaseUpdate:h,lastBaseUpdate:x,shared:f.shared,effects:f.effects},n.updateQueue=u;return}n=u.lastBaseUpdate,n===null?u.firstBaseUpdate=i:n.next=i,u.lastBaseUpdate=i}function Nd(n,i,u,f){var h=n.updateQueue;ia=!1;var x=h.firstBaseUpdate,k=h.lastBaseUpdate,A=h.shared.pending;if(A!==null){h.shared.pending=null;var N=A,J=N.next;N.next=null,k===null?x=J:k.next=J,k=N;var he=n.alternate;he!==null&&(he=he.updateQueue,A=he.lastBaseUpdate,A!==k&&(A===null?he.firstBaseUpdate=J:A.next=J,he.lastBaseUpdate=N))}if(x!==null){var ge=h.baseState;k=0,he=J=N=null,A=x;do{var pe=A.lane,je=A.eventTime;if((f&pe)===pe){he!==null&&(he=he.next={eventTime:je,lane:0,tag:A.tag,payload:A.payload,callback:A.callback,next:null});e:{var Fe=n,Ue=A;switch(pe=i,je=u,Ue.tag){case 1:if(Fe=Ue.payload,typeof Fe=="function"){ge=Fe.call(je,ge,pe);break e}ge=Fe;break e;case 3:Fe.flags=Fe.flags&-65537|128;case 0:if(Fe=Ue.payload,pe=typeof Fe=="function"?Fe.call(je,ge,pe):Fe,pe==null)break e;ge=be({},ge,pe);break e;case 2:ia=!0}}A.callback!==null&&A.lane!==0&&(n.flags|=64,pe=h.effects,pe===null?h.effects=[A]:pe.push(A))}else je={eventTime:je,lane:pe,tag:A.tag,payload:A.payload,callback:A.callback,next:null},he===null?(J=he=je,N=ge):he=he.next=je,k|=pe;if(A=A.next,A===null){if(A=h.shared.pending,A===null)break;pe=A,A=pe.next,pe.next=null,h.lastBaseUpdate=pe,h.shared.pending=null}}while(!0);if(he===null&&(N=ge),h.baseState=N,h.firstBaseUpdate=J,h.lastBaseUpdate=he,i=h.shared.interleaved,i!==null){h=i;do k|=h.lane,h=h.next;while(h!==i)}else x===null&&(h.shared.lanes=0);Ql|=k,n.lanes=k,n.memoizedState=ge}}function qp(n,i,u){if(n=i.effects,i.effects=null,n!==null)for(i=0;i<n.length;i++){var f=n[i],h=f.callback;if(h!==null){if(f.callback=null,f=u,typeof h!="function")throw Error(s(191,h));h.call(f)}}}var Ys={},Wa=fi(Ys),uc=fi(Ys),cc=fi(Ys);function Il(n){if(n===Ys)throw Error(s(174));return n}function Xp(n,i){switch(gn(cc,i),gn(uc,n),gn(Wa,Ys),n=i.nodeType,n){case 9:case 11:i=(i=i.documentElement)?i.namespaceURI:xr(null,"");break;default:n=n===8?i.parentNode:i,i=n.namespaceURI||null,n=n.tagName,i=xr(i,n)}tn(Wa),gn(Wa,i)}function Gs(){tn(Wa),tn(uc),tn(cc)}function Jp(n){Il(cc.current);var i=Il(Wa.current),u=xr(i,n.type);i!==u&&(gn(uc,n),gn(Wa,u))}function Zp(n){uc.current===n&&(tn(Wa),tn(uc))}var Dn=fi(0);function Pd(n){for(var i=n;i!==null;){if(i.tag===13){var u=i.memoizedState;if(u!==null&&(u=u.dehydrated,u===null||u.data==="$?"||u.data==="$!"))return i}else if(i.tag===19&&i.memoizedProps.revealOrder!==void 0){if(i.flags&128)return i}else if(i.child!==null){i.child.return=i,i=i.child;continue}if(i===n)break;for(;i.sibling===null;){if(i.return===null||i.return===n)return null;i=i.return}i.sibling.return=i.return,i=i.sibling}return null}var eh=[];function dc(){for(var n=0;n<eh.length;n++)eh[n]._workInProgressVersionPrimary=null;eh.length=0}var Ge=ce.ReactCurrentDispatcher,Dt=ce.ReactCurrentBatchConfig,zt=0,mt=null,cn=null,rr=null,Fd=!1,fc=!1,pc=0,th=0;function ie(){throw Error(s(321))}function Jn(n,i){if(i===null)return!1;for(var u=0;u<i.length&&u<n.length;u++)if(!xa(n[u],i[u]))return!1;return!0}function nt(n,i,u,f,h,x){if(zt=x,mt=i,i.memoizedState=null,i.updateQueue=null,i.lanes=0,Ge.current=n===null||n.memoizedState===null?ef:tf,n=u(f,h),fc){x=0;do{if(fc=!1,pc=0,25<=x)throw Error(s(301));x+=1,rr=cn=null,i.updateQueue=null,Ge.current=yc,n=u(f,h)}while(fc)}if(Ge.current=rn,i=cn!==null&&cn.next!==null,zt=0,rr=cn=mt=null,Fd=!1,i)throw Error(s(300));return n}function qo(){var n=pc!==0;return pc=0,n}function pr(){var n={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return rr===null?mt.memoizedState=rr=n:rr=rr.next=n,rr}function hr(){if(cn===null){var n=mt.alternate;n=n!==null?n.memoizedState:null}else n=cn.next;var i=rr===null?mt.memoizedState:rr.next;if(i!==null)rr=i,cn=n;else{if(n===null)throw Error(s(310));cn=n,n={memoizedState:cn.memoizedState,baseState:cn.baseState,baseQueue:cn.baseQueue,queue:cn.queue,next:null},rr===null?mt.memoizedState=rr=n:rr=rr.next=n}return rr}function gi(n,i){return typeof i=="function"?i(n):i}function Ul(n){var i=hr(),u=i.queue;if(u===null)throw Error(s(311));u.lastRenderedReducer=n;var f=cn,h=f.baseQueue,x=u.pending;if(x!==null){if(h!==null){var k=h.next;h.next=x.next,x.next=k}f.baseQueue=h=x,u.pending=null}if(h!==null){x=h.next,f=f.baseState;var A=k=null,N=null,J=x;do{var he=J.lane;if((zt&he)===he)N!==null&&(N=N.next={lane:0,action:J.action,hasEagerState:J.hasEagerState,eagerState:J.eagerState,next:null}),f=J.hasEagerState?J.eagerState:n(f,J.action);else{var ge={lane:he,action:J.action,hasEagerState:J.hasEagerState,eagerState:J.eagerState,next:null};N===null?(A=N=ge,k=f):N=N.next=ge,mt.lanes|=he,Ql|=he}J=J.next}while(J!==null&&J!==x);N===null?k=f:N.next=A,xa(f,i.memoizedState)||(gr=!0),i.memoizedState=f,i.baseState=k,i.baseQueue=N,u.lastRenderedState=f}if(n=u.interleaved,n!==null){h=n;do x=h.lane,mt.lanes|=x,Ql|=x,h=h.next;while(h!==n)}else h===null&&(u.lanes=0);return[i.memoizedState,u.dispatch]}function Xo(n){var i=hr(),u=i.queue;if(u===null)throw Error(s(311));u.lastRenderedReducer=n;var f=u.dispatch,h=u.pending,x=i.memoizedState;if(h!==null){u.pending=null;var k=h=h.next;do x=n(x,k.action),k=k.next;while(k!==h);xa(x,i.memoizedState)||(gr=!0),i.memoizedState=x,i.baseQueue===null&&(i.baseState=x),u.lastRenderedState=x}return[x,f]}function Ks(){}function Id(n,i){var u=mt,f=hr(),h=i(),x=!xa(f.memoizedState,h);if(x&&(f.memoizedState=h,gr=!0),f=f.queue,hc(Hd.bind(null,u,f,n),[n]),f.getSnapshot!==i||x||rr!==null&&rr.memoizedState.tag&1){if(u.flags|=2048,Bl(9,Bd.bind(null,u,f,h,i),void 0,null),Zn===null)throw Error(s(349));zt&30||Ud(u,i,h)}return h}function Ud(n,i,u){n.flags|=16384,n={getSnapshot:i,value:u},i=mt.updateQueue,i===null?(i={lastEffect:null,stores:null},mt.updateQueue=i,i.stores=[n]):(u=i.stores,u===null?i.stores=[n]:u.push(n))}function Bd(n,i,u,f){i.value=u,i.getSnapshot=f,Vd(i)&&Wd(n)}function Hd(n,i,u){return u(function(){Vd(i)&&Wd(n)})}function Vd(n){var i=n.getSnapshot;n=n.value;try{var u=i();return!xa(n,u)}catch{return!0}}function Wd(n){var i=Va(n,1);i!==null&&Ni(i,n,1,-1)}function Yd(n){var i=pr();return typeof n=="function"&&(n=n()),i.memoizedState=i.baseState=n,n={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:gi,lastRenderedState:n},i.queue=n,n=n.dispatch=vc.bind(null,mt,n),[i.memoizedState,n]}function Bl(n,i,u,f){return n={tag:n,create:i,destroy:u,deps:f,next:null},i=mt.updateQueue,i===null?(i={lastEffect:null,stores:null},mt.updateQueue=i,i.lastEffect=n.next=n):(u=i.lastEffect,u===null?i.lastEffect=n.next=n:(f=u.next,u.next=n,n.next=f,i.lastEffect=n)),n}function Gd(){return hr().memoizedState}function Qs(n,i,u,f){var h=pr();mt.flags|=n,h.memoizedState=Bl(1|i,u,void 0,f===void 0?null:f)}function qs(n,i,u,f){var h=hr();f=f===void 0?null:f;var x=void 0;if(cn!==null){var k=cn.memoizedState;if(x=k.destroy,f!==null&&Jn(f,k.deps)){h.memoizedState=Bl(i,u,x,f);return}}mt.flags|=n,h.memoizedState=Bl(1|i,u,x,f)}function Kd(n,i){return Qs(8390656,8,n,i)}function hc(n,i){return qs(2048,8,n,i)}function Qd(n,i){return qs(4,2,n,i)}function qd(n,i){return qs(4,4,n,i)}function gc(n,i){if(typeof i=="function")return n=n(),i(n),function(){i(null)};if(i!=null)return n=n(),i.current=n,function(){i.current=null}}function Hl(n,i,u){return u=u!=null?u.concat([n]):null,qs(4,4,gc.bind(null,i,n),u)}function mc(){}function Xd(n,i){var u=hr();i=i===void 0?null:i;var f=u.memoizedState;return f!==null&&i!==null&&Jn(i,f[1])?f[0]:(u.memoizedState=[n,i],n)}function Jd(n,i){var u=hr();i=i===void 0?null:i;var f=u.memoizedState;return f!==null&&i!==null&&Jn(i,f[1])?f[0]:(n=n(),u.memoizedState=[n,i],n)}function Zd(n,i,u){return zt&21?(xa(u,i)||(u=El(),mt.lanes|=u,Ql|=u,n.baseState=!0),i):(n.baseState&&(n.baseState=!1,gr=!0),n.memoizedState=u)}function Qm(n,i){var u=Nt;Nt=u!==0&&4>u?u:4,n(!0);var f=Dt.transition;Dt.transition={};try{n(!1),i()}finally{Nt=u,Dt.transition=f}}function Xs(){return hr().memoizedState}function qm(n,i,u){var f=zi(n);if(u={lane:f,action:u,hasEagerState:!1,eagerState:null,next:null},Jo(n))mi(i,u);else if(u=Ym(n,i,u,f),u!==null){var h=mn();Ni(u,n,f,h),Xm(u,i,f)}}function vc(n,i,u){var f=zi(n),h={lane:f,action:u,hasEagerState:!1,eagerState:null,next:null};if(Jo(n))mi(i,h);else{var x=n.alternate;if(n.lanes===0&&(x===null||x.lanes===0)&&(x=i.lastRenderedReducer,x!==null))try{var k=i.lastRenderedState,A=x(k,u);if(h.hasEagerState=!0,h.eagerState=A,xa(A,k)){var N=i.interleaved;N===null?(h.next=h,Qp(i)):(h.next=N.next,N.next=h),i.interleaved=h;return}}catch{}finally{}u=Ym(n,i,h,f),u!==null&&(h=mn(),Ni(u,n,f,h),Xm(u,i,f))}}function Jo(n){var i=n.alternate;return n===mt||i!==null&&i===mt}function mi(n,i){fc=Fd=!0;var u=n.pending;u===null?i.next=i:(i.next=u.next,u.next=i),n.pending=i}function Xm(n,i,u){if(u&4194240){var f=i.lanes;f&=n.pendingLanes,u|=f,i.lanes=u,Iu(n,u)}}var rn={readContext:nn,useCallback:ie,useContext:ie,useEffect:ie,useImperativeHandle:ie,useInsertionEffect:ie,useLayoutEffect:ie,useMemo:ie,useReducer:ie,useRef:ie,useState:ie,useDebugValue:ie,useDeferredValue:ie,useTransition:ie,useMutableSource:ie,useSyncExternalStore:ie,useId:ie,unstable_isNewReconciler:!1},ef={readContext:nn,useCallback:function(n,i){return pr().memoizedState=[n,i===void 0?null:i],n},useContext:nn,useEffect:Kd,useImperativeHandle:function(n,i,u){return u=u!=null?u.concat([n]):null,Qs(4194308,4,gc.bind(null,i,n),u)},useLayoutEffect:function(n,i){return Qs(4194308,4,n,i)},useInsertionEffect:function(n,i){return Qs(4,2,n,i)},useMemo:function(n,i){var u=pr();return i=i===void 0?null:i,n=n(),u.memoizedState=[n,i],n},useReducer:function(n,i,u){var f=pr();return i=u!==void 0?u(i):i,f.memoizedState=f.baseState=i,n={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:n,lastRenderedState:i},f.queue=n,n=n.dispatch=qm.bind(null,mt,n),[f.memoizedState,n]},useRef:function(n){var i=pr();return n={current:n},i.memoizedState=n},useState:Yd,useDebugValue:mc,useDeferredValue:function(n){return pr().memoizedState=n},useTransition:function(){var n=Yd(!1),i=n[0];return n=Qm.bind(null,n[1]),pr().memoizedState=n,[i,n]},useMutableSource:function(){},useSyncExternalStore:function(n,i,u){var f=mt,h=pr();if(En){if(u===void 0)throw Error(s(407));u=u()}else{if(u=i(),Zn===null)throw Error(s(349));zt&30||Ud(f,i,u)}h.memoizedState=u;var x={value:u,getSnapshot:i};return h.queue=x,Kd(Hd.bind(null,f,x,n),[n]),f.flags|=2048,Bl(9,Bd.bind(null,f,x,u,i),void 0,null),u},useId:function(){var n=pr(),i=Zn.identifierPrefix;if(En){var u=Ha,f=Kr;u=(f&~(1<<32-Vr(f)-1)).toString(32)+u,i=":"+i+"R"+u,u=pc++,0<u&&(i+="H"+u.toString(32)),i+=":"}else u=th++,i=":"+i+"r"+u.toString(32)+":";return n.memoizedState=i},unstable_isNewReconciler:!1},tf={readContext:nn,useCallback:Xd,useContext:nn,useEffect:hc,useImperativeHandle:Hl,useInsertionEffect:Qd,useLayoutEffect:qd,useMemo:Jd,useReducer:Ul,useRef:Gd,useState:function(){return Ul(gi)},useDebugValue:mc,useDeferredValue:function(n){var i=hr();return Zd(i,cn.memoizedState,n)},useTransition:function(){var n=Ul(gi)[0],i=hr().memoizedState;return[n,i]},useMutableSource:Ks,useSyncExternalStore:Id,useId:Xs,unstable_isNewReconciler:!1},yc={readContext:nn,useCallback:Xd,useContext:nn,useEffect:hc,useImperativeHandle:Hl,useInsertionEffect:Qd,useLayoutEffect:qd,useMemo:Jd,useReducer:Xo,useRef:Gd,useState:function(){return Xo(gi)},useDebugValue:mc,useDeferredValue:function(n){var i=hr();return cn===null?i.memoizedState=n:Zd(i,cn.memoizedState,n)},useTransition:function(){var n=Xo(gi)[0],i=hr().memoizedState;return[n,i]},useMutableSource:Ks,useSyncExternalStore:Id,useId:Xs,unstable_isNewReconciler:!1};function vi(n,i){if(n&&n.defaultProps){i=be({},i),n=n.defaultProps;for(var u in n)i[u]===void 0&&(i[u]=n[u]);return i}return i}function nh(n,i,u,f){i=n.memoizedState,u=u(f,i),u=u==null?i:be({},i,u),n.memoizedState=u,n.lanes===0&&(n.updateQueue.baseState=u)}var nf={isMounted:function(n){return(n=n._reactInternals)?De(n)===n:!1},enqueueSetState:function(n,i,u){n=n._reactInternals;var f=mn(),h=zi(n),x=bo(f,h);x.payload=i,u!=null&&(x.callback=u),i=Qo(n,x,h),i!==null&&(Ni(i,n,h,f),zd(i,n,h))},enqueueReplaceState:function(n,i,u){n=n._reactInternals;var f=mn(),h=zi(n),x=bo(f,h);x.tag=1,x.payload=i,u!=null&&(x.callback=u),i=Qo(n,x,h),i!==null&&(Ni(i,n,h,f),zd(i,n,h))},enqueueForceUpdate:function(n,i){n=n._reactInternals;var u=mn(),f=zi(n),h=bo(u,f);h.tag=2,i!=null&&(h.callback=i),i=Qo(n,h,f),i!==null&&(Ni(i,n,f,u),zd(i,n,f))}};function Jm(n,i,u,f,h,x,k){return n=n.stateNode,typeof n.shouldComponentUpdate=="function"?n.shouldComponentUpdate(f,x,k):i.prototype&&i.prototype.isPureReactComponent?!Gu(u,f)||!Gu(h,x):!0}function Zm(n,i,u){var f=!1,h=Et,x=i.contextType;return typeof x=="object"&&x!==null?x=nn(x):(h=zn(i)?ra:Rn.current,f=i.contextTypes,x=(f=f!=null)?Ai(n,h):Et),i=new i(u,x),n.memoizedState=i.state!==null&&i.state!==void 0?i.state:null,i.updater=nf,n.stateNode=i,i._reactInternals=n,f&&(n=n.stateNode,n.__reactInternalMemoizedUnmaskedChildContext=h,n.__reactInternalMemoizedMaskedChildContext=x),i}function rf(n,i,u,f){n=i.state,typeof i.componentWillReceiveProps=="function"&&i.componentWillReceiveProps(u,f),typeof i.UNSAFE_componentWillReceiveProps=="function"&&i.UNSAFE_componentWillReceiveProps(u,f),i.state!==n&&nf.enqueueReplaceState(i,i.state,null)}function rh(n,i,u,f){var h=n.stateNode;h.props=u,h.state=n.memoizedState,h.refs={},Ko(n);var x=i.contextType;typeof x=="object"&&x!==null?h.context=nn(x):(x=zn(i)?ra:Rn.current,h.context=Ai(n,x)),h.state=n.memoizedState,x=i.getDerivedStateFromProps,typeof x=="function"&&(nh(n,i,x,u),h.state=n.memoizedState),typeof i.getDerivedStateFromProps=="function"||typeof h.getSnapshotBeforeUpdate=="function"||typeof h.UNSAFE_componentWillMount!="function"&&typeof h.componentWillMount!="function"||(i=h.state,typeof h.componentWillMount=="function"&&h.componentWillMount(),typeof h.UNSAFE_componentWillMount=="function"&&h.UNSAFE_componentWillMount(),i!==h.state&&nf.enqueueReplaceState(h,h.state,null),Nd(n,u,h,f),h.state=n.memoizedState),typeof h.componentDidMount=="function"&&(n.flags|=4194308)}function Zo(n,i){try{var u="",f=i;do u+=it(f),f=f.return;while(f);var h=u}catch(x){h=`
Error generating stack: `+x.message+`
`+x.stack}return{value:n,source:i,stack:h,digest:null}}function af(n,i,u){return{value:n,source:null,stack:u??null,digest:i??null}}function ih(n,i){try{console.error(i.value)}catch(u){setTimeout(function(){throw u})}}var L0=typeof WeakMap=="function"?WeakMap:Map;function xc(n,i,u){u=bo(-1,u),u.tag=3,u.payload={element:null};var f=i.value;return u.callback=function(){tl||(tl=!0,Rc=f),ih(n,i)},u}function ev(n,i,u){u=bo(-1,u),u.tag=3;var f=n.type.getDerivedStateFromError;if(typeof f=="function"){var h=i.value;u.payload=function(){return f(h)},u.callback=function(){ih(n,i)}}var x=n.stateNode;return x!==null&&typeof x.componentDidCatch=="function"&&(u.callback=function(){ih(n,i),typeof f!="function"&&(la===null?la=new Set([this]):la.add(this));var k=i.stack;this.componentDidCatch(i.value,{componentStack:k!==null?k:""})}),u}function ah(n,i,u){var f=n.pingCache;if(f===null){f=n.pingCache=new L0;var h=new Set;f.set(i,h)}else h=f.get(i),h===void 0&&(h=new Set,f.set(i,h));h.has(u)||(h.add(u),n=yh.bind(null,n,i,u),i.then(n,n))}function oh(n){do{var i;if((i=n.tag===13)&&(i=n.memoizedState,i=i!==null?i.dehydrated!==null:!0),i)return n;n=n.return}while(n!==null);return null}function tv(n,i,u,f,h){return n.mode&1?(n.flags|=65536,n.lanes=h,n):(n===i?n.flags|=65536:(n.flags|=128,u.flags|=131072,u.flags&=-52805,u.tag===1&&(u.alternate===null?u.tag=17:(i=bo(-1,1),i.tag=2,Qo(u,i,1))),u.lanes|=1),n)}var Vl=ce.ReactCurrentOwner,gr=!1;function Bn(n,i,u,f){i.child=n===null?Sr(i,null,u,f):Ca(i,n.child,u,f)}function of(n,i,u,f,h){u=u.render;var x=i.ref;return Ws(i,h),f=nt(n,i,u,f,x,h),u=qo(),n!==null&&!gr?(i.updateQueue=n.updateQueue,i.flags&=-2053,n.lanes&=~h,Cr(n,i,h)):(En&&u&&Ip(i),i.flags|=1,Bn(n,i,f,h),i.child)}function yi(n,i,u,f,h){if(n===null){var x=u.type;return typeof x=="function"&&!bh(x)&&x.defaultProps===void 0&&u.compare===null&&u.defaultProps===void 0?(i.tag=15,i.type=x,Wl(n,i,x,f,h)):(n=wf(u.type,null,f,i,i.mode,h),n.ref=i.ref,n.return=i,i.child=n)}if(x=n.child,!(n.lanes&h)){var k=x.memoizedProps;if(u=u.compare,u=u!==null?u:Gu,u(k,f)&&n.ref===i.ref)return Cr(n,i,h)}return i.flags|=1,n=il(x,f),n.ref=i.ref,n.return=i,i.child=n}function Wl(n,i,u,f,h){if(n!==null){var x=n.memoizedProps;if(Gu(x,f)&&n.ref===i.ref)if(gr=!1,i.pendingProps=f=x,(n.lanes&h)!==0)n.flags&131072&&(gr=!0);else return i.lanes=n.lanes,Cr(n,i,h)}return lf(n,i,u,f,h)}function xt(n,i,u){var f=i.pendingProps,h=f.children,x=n!==null?n.memoizedState:null;if(f.mode==="hidden")if(!(i.mode&1))i.memoizedState={baseLanes:0,cachePool:null,transitions:null},gn(tu,Li),Li|=u;else{if(!(u&1073741824))return n=x!==null?x.baseLanes|u:u,i.lanes=i.childLanes=1073741824,i.memoizedState={baseLanes:n,cachePool:null,transitions:null},i.updateQueue=null,gn(tu,Li),Li|=n,null;i.memoizedState={baseLanes:0,cachePool:null,transitions:null},f=x!==null?x.baseLanes:u,gn(tu,Li),Li|=f}else x!==null?(f=x.baseLanes|u,i.memoizedState=null):f=u,gn(tu,Li),Li|=f;return Bn(n,i,h,u),i.child}function bc(n,i){var u=i.ref;(n===null&&u!==null||n!==null&&n.ref!==u)&&(i.flags|=512,i.flags|=2097152)}function lf(n,i,u,f,h){var x=zn(u)?ra:Rn.current;return x=Ai(i,x),Ws(i,h),u=nt(n,i,u,f,x,h),f=qo(),n!==null&&!gr?(i.updateQueue=n.updateQueue,i.flags&=-2053,n.lanes&=~h,Cr(n,i,h)):(En&&f&&Ip(i),i.flags|=1,Bn(n,i,u,h),i.child)}function z0(n,i,u,f,h){if(zn(u)){var x=!0;zl(i)}else x=!1;if(Ws(i,h),i.stateNode===null)aa(n,i),Zm(i,u,f),rh(i,u,f,h),f=!0;else if(n===null){var k=i.stateNode,A=i.memoizedProps;k.props=A;var N=k.context,J=u.contextType;typeof J=="object"&&J!==null?J=nn(J):(J=zn(u)?ra:Rn.current,J=Ai(i,J));var he=u.getDerivedStateFromProps,ge=typeof he=="function"||typeof k.getSnapshotBeforeUpdate=="function";ge||typeof k.UNSAFE_componentWillReceiveProps!="function"&&typeof k.componentWillReceiveProps!="function"||(A!==f||N!==J)&&rf(i,k,f,J),ia=!1;var pe=i.memoizedState;k.state=pe,Nd(i,f,k,h),N=i.memoizedState,A!==f||pe!==N||qn.current||ia?(typeof he=="function"&&(nh(i,u,he,f),N=i.memoizedState),(A=ia||Jm(i,u,A,f,pe,N,J))?(ge||typeof k.UNSAFE_componentWillMount!="function"&&typeof k.componentWillMount!="function"||(typeof k.componentWillMount=="function"&&k.componentWillMount(),typeof k.UNSAFE_componentWillMount=="function"&&k.UNSAFE_componentWillMount()),typeof k.componentDidMount=="function"&&(i.flags|=4194308)):(typeof k.componentDidMount=="function"&&(i.flags|=4194308),i.memoizedProps=f,i.memoizedState=N),k.props=f,k.state=N,k.context=J,f=A):(typeof k.componentDidMount=="function"&&(i.flags|=4194308),f=!1)}else{k=i.stateNode,Gm(n,i),A=i.memoizedProps,J=i.type===i.elementType?A:vi(i.type,A),k.props=J,ge=i.pendingProps,pe=k.context,N=u.contextType,typeof N=="object"&&N!==null?N=nn(N):(N=zn(u)?ra:Rn.current,N=Ai(i,N));var je=u.getDerivedStateFromProps;(he=typeof je=="function"||typeof k.getSnapshotBeforeUpdate=="function")||typeof k.UNSAFE_componentWillReceiveProps!="function"&&typeof k.componentWillReceiveProps!="function"||(A!==ge||pe!==N)&&rf(i,k,f,N),ia=!1,pe=i.memoizedState,k.state=pe,Nd(i,f,k,h);var Fe=i.memoizedState;A!==ge||pe!==Fe||qn.current||ia?(typeof je=="function"&&(nh(i,u,je,f),Fe=i.memoizedState),(J=ia||Jm(i,u,J,f,pe,Fe,N)||!1)?(he||typeof k.UNSAFE_componentWillUpdate!="function"&&typeof k.componentWillUpdate!="function"||(typeof k.componentWillUpdate=="function"&&k.componentWillUpdate(f,Fe,N),typeof k.UNSAFE_componentWillUpdate=="function"&&k.UNSAFE_componentWillUpdate(f,Fe,N)),typeof k.componentDidUpdate=="function"&&(i.flags|=4),typeof k.getSnapshotBeforeUpdate=="function"&&(i.flags|=1024)):(typeof k.componentDidUpdate!="function"||A===n.memoizedProps&&pe===n.memoizedState||(i.flags|=4),typeof k.getSnapshotBeforeUpdate!="function"||A===n.memoizedProps&&pe===n.memoizedState||(i.flags|=1024),i.memoizedProps=f,i.memoizedState=Fe),k.props=f,k.state=Fe,k.context=N,f=J):(typeof k.componentDidUpdate!="function"||A===n.memoizedProps&&pe===n.memoizedState||(i.flags|=4),typeof k.getSnapshotBeforeUpdate!="function"||A===n.memoizedProps&&pe===n.memoizedState||(i.flags|=1024),f=!1)}return lh(n,i,u,f,x,h)}function lh(n,i,u,f,h,x){bc(n,i);var k=(i.flags&128)!==0;if(!f&&!k)return h&&Pr(i,u,!1),Cr(n,i,x);f=i.stateNode,Vl.current=i;var A=k&&typeof u.getDerivedStateFromError!="function"?null:f.render();return i.flags|=1,n!==null&&k?(i.child=Ca(i,n.child,null,x),i.child=Ca(i,null,A,x)):Bn(n,i,A,x),i.memoizedState=f.state,h&&Pr(i,u,!0),i.child}function sf(n){var i=n.stateNode;i.pendingContext?_d(n,i.pendingContext,i.pendingContext!==i.context):i.context&&_d(n,i.context,!1),Xp(n,i.containerInfo)}function Js(n,i,u,f,h){return xo(),lc(h),i.flags|=256,Bn(n,i,u,f),i.child}var sh={dehydrated:null,treeContext:null,retryLane:0};function uf(n){return{baseLanes:n,cachePool:null,transitions:null}}function nv(n,i,u){var f=i.pendingProps,h=Dn.current,x=!1,k=(i.flags&128)!==0,A;if((A=k)||(A=n!==null&&n.memoizedState===null?!1:(h&2)!==0),A?(x=!0,i.flags&=-129):(n===null||n.memoizedState!==null)&&(h|=1),gn(Dn,h&1),n===null)return Vp(i),n=i.memoizedState,n!==null&&(n=n.dehydrated,n!==null)?(i.mode&1?n.data==="$!"?i.lanes=8:i.lanes=1073741824:i.lanes=1,null):(k=f.children,n=f.fallback,x?(f=i.mode,x=i.child,k={mode:"hidden",children:k},!(f&1)&&x!==null?(x.childLanes=0,x.pendingProps=k):x=lu(k,f,0,null),n=al(n,f,u,null),x.return=i,n.return=i,x.sibling=n,i.child=x,i.child.memoizedState=uf(u),i.memoizedState=sh,n):wc(i,k));if(h=n.memoizedState,h!==null&&(A=h.dehydrated,A!==null))return rv(n,i,k,f,A,h,u);if(x){x=f.fallback,k=i.mode,h=n.child,A=h.sibling;var N={mode:"hidden",children:f.children};return!(k&1)&&i.child!==h?(f=i.child,f.childLanes=0,f.pendingProps=N,i.deletions=null):(f=il(h,N),f.subtreeFlags=h.subtreeFlags&14680064),A!==null?x=il(A,x):(x=al(x,k,u,null),x.flags|=2),x.return=i,f.return=i,f.sibling=x,i.child=f,f=x,x=i.child,k=n.child.memoizedState,k=k===null?uf(u):{baseLanes:k.baseLanes|u,cachePool:null,transitions:k.transitions},x.memoizedState=k,x.childLanes=n.childLanes&~u,i.memoizedState=sh,f}return x=n.child,n=x.sibling,f=il(x,{mode:"visible",children:f.children}),!(i.mode&1)&&(f.lanes=u),f.return=i,f.sibling=null,n!==null&&(u=i.deletions,u===null?(i.deletions=[n],i.flags|=16):u.push(n)),i.child=f,i.memoizedState=null,f}function wc(n,i){return i=lu({mode:"visible",children:i},n.mode,0,null),i.return=n,n.child=i}function cf(n,i,u,f){return f!==null&&lc(f),Ca(i,n.child,null,u),n=wc(i,i.pendingProps.children),n.flags|=2,i.memoizedState=null,n}function rv(n,i,u,f,h,x,k){if(u)return i.flags&256?(i.flags&=-257,f=af(Error(s(422))),cf(n,i,k,f)):i.memoizedState!==null?(i.child=n.child,i.flags|=128,null):(x=f.fallback,h=i.mode,f=lu({mode:"visible",children:f.children},h,0,null),x=al(x,h,k,null),x.flags|=2,f.return=i,x.return=i,f.sibling=x,i.child=f,i.mode&1&&Ca(i,n.child,null,k),i.child.memoizedState=uf(k),i.memoizedState=sh,x);if(!(i.mode&1))return cf(n,i,k,null);if(h.data==="$!"){if(f=h.nextSibling&&h.nextSibling.dataset,f)var A=f.dgst;return f=A,x=Error(s(419)),f=af(x,f,void 0),cf(n,i,k,f)}if(A=(k&n.childLanes)!==0,gr||A){if(f=Zn,f!==null){switch(k&-k){case 4:h=2;break;case 16:h=8;break;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:h=32;break;case 536870912:h=268435456;break;default:h=0}h=h&(f.suspendedLanes|k)?0:h,h!==0&&h!==x.retryLane&&(x.retryLane=h,Va(n,h),Ni(f,n,h,-1))}return mh(),f=af(Error(s(421))),cf(n,i,k,f)}return h.data==="$?"?(i.flags|=128,i.child=n.child,i=B0.bind(null,n),h._reactRetry=i,null):(n=x.treeContext,hi=ba(h.nextSibling),pi=i,En=!0,Sa=null,n!==null&&(wr[Xn++]=Kr,wr[Xn++]=Ha,wr[Xn++]=Nl,Kr=n.id,Ha=n.overflow,Nl=i),i=wc(i,f.children),i.flags|=4096,i)}function uh(n,i,u){n.lanes|=i;var f=n.alternate;f!==null&&(f.lanes|=i),Kp(n.return,i,u)}function df(n,i,u,f,h){var x=n.memoizedState;x===null?n.memoizedState={isBackwards:i,rendering:null,renderingStartTime:0,last:f,tail:u,tailMode:h}:(x.isBackwards=i,x.rendering=null,x.renderingStartTime=0,x.last=f,x.tail=u,x.tailMode=h)}function xi(n,i,u){var f=i.pendingProps,h=f.revealOrder,x=f.tail;if(Bn(n,i,f.children,u),f=Dn.current,f&2)f=f&1|2,i.flags|=128;else{if(n!==null&&n.flags&128)e:for(n=i.child;n!==null;){if(n.tag===13)n.memoizedState!==null&&uh(n,u,i);else if(n.tag===19)uh(n,u,i);else if(n.child!==null){n.child.return=n,n=n.child;continue}if(n===i)break e;for(;n.sibling===null;){if(n.return===null||n.return===i)break e;n=n.return}n.sibling.return=n.return,n=n.sibling}f&=1}if(gn(Dn,f),!(i.mode&1))i.memoizedState=null;else switch(h){case"forwards":for(u=i.child,h=null;u!==null;)n=u.alternate,n!==null&&Pd(n)===null&&(h=u),u=u.sibling;u=h,u===null?(h=i.child,i.child=null):(h=u.sibling,u.sibling=null),df(i,!1,h,u,x);break;case"backwards":for(u=null,h=i.child,i.child=null;h!==null;){if(n=h.alternate,n!==null&&Pd(n)===null){i.child=h;break}n=h.sibling,h.sibling=u,u=h,h=n}df(i,!0,u,null,x);break;case"together":df(i,!1,null,null,void 0);break;default:i.memoizedState=null}return i.child}function aa(n,i){!(i.mode&1)&&n!==null&&(n.alternate=null,i.alternate=null,i.flags|=2)}function Cr(n,i,u){if(n!==null&&(i.dependencies=n.dependencies),Ql|=i.lanes,!(u&i.childLanes))return null;if(n!==null&&i.child!==n.child)throw Error(s(153));if(i.child!==null){for(n=i.child,u=il(n,n.pendingProps),i.child=u,u.return=i;n.sibling!==null;)n=n.sibling,u=u.sibling=il(n,n.pendingProps),u.return=i;u.sibling=null}return i.child}function ff(n,i,u){switch(i.tag){case 3:sf(i),xo();break;case 5:Jp(i);break;case 1:zn(i.type)&&zl(i);break;case 4:Xp(i,i.stateNode.containerInfo);break;case 10:var f=i.type._context,h=i.memoizedProps.value;gn(Ce,f._currentValue),f._currentValue=h;break;case 13:if(f=i.memoizedState,f!==null)return f.dehydrated!==null?(gn(Dn,Dn.current&1),i.flags|=128,null):u&i.child.childLanes?nv(n,i,u):(gn(Dn,Dn.current&1),n=Cr(n,i,u),n!==null?n.sibling:null);gn(Dn,Dn.current&1);break;case 19:if(f=(u&i.childLanes)!==0,n.flags&128){if(f)return xi(n,i,u);i.flags|=128}if(h=i.memoizedState,h!==null&&(h.rendering=null,h.tail=null,h.lastEffect=null),gn(Dn,Dn.current),f)break;return null;case 22:case 23:return i.lanes=0,xt(n,i,u)}return Cr(n,i,u)}var Zs,_i,ir,iv;Zs=function(n,i){for(var u=i.child;u!==null;){if(u.tag===5||u.tag===6)n.appendChild(u.stateNode);else if(u.tag!==4&&u.child!==null){u.child.return=u,u=u.child;continue}if(u===i)break;for(;u.sibling===null;){if(u.return===null||u.return===i)return;u=u.return}u.sibling.return=u.return,u=u.sibling}},_i=function(){},ir=function(n,i,u,f){var h=n.memoizedProps;if(h!==f){n=i.stateNode,Il(Wa.current);var x=null;switch(u){case"input":h=xn(n,h),f=xn(n,f),x=[];break;case"select":h=be({},h,{value:void 0}),f=be({},f,{value:void 0}),x=[];break;case"textarea":h=ur(n,h),f=ur(n,f),x=[];break;default:typeof h.onClick!="function"&&typeof f.onClick=="function"&&(n.onclick=Od)}wn(u,f);var k;u=null;for(J in h)if(!f.hasOwnProperty(J)&&h.hasOwnProperty(J)&&h[J]!=null)if(J==="style"){var A=h[J];for(k in A)A.hasOwnProperty(k)&&(u||(u={}),u[k]="")}else J!=="dangerouslySetInnerHTML"&&J!=="children"&&J!=="suppressContentEditableWarning"&&J!=="suppressHydrationWarning"&&J!=="autoFocus"&&(g.hasOwnProperty(J)?x||(x=[]):(x=x||[]).push(J,null));for(J in f){var N=f[J];if(A=h!=null?h[J]:void 0,f.hasOwnProperty(J)&&N!==A&&(N!=null||A!=null))if(J==="style")if(A){for(k in A)!A.hasOwnProperty(k)||N&&N.hasOwnProperty(k)||(u||(u={}),u[k]="");for(k in N)N.hasOwnProperty(k)&&A[k]!==N[k]&&(u||(u={}),u[k]=N[k])}else u||(x||(x=[]),x.push(J,u)),u=N;else J==="dangerouslySetInnerHTML"?(N=N?N.__html:void 0,A=A?A.__html:void 0,N!=null&&A!==N&&(x=x||[]).push(J,N)):J==="children"?typeof N!="string"&&typeof N!="number"||(x=x||[]).push(J,""+N):J!=="suppressContentEditableWarning"&&J!=="suppressHydrationWarning"&&(g.hasOwnProperty(J)?(N!=null&&J==="onScroll"&&Xt("scroll",n),x||A===N||(x=[])):(x=x||[]).push(J,N))}u&&(x=x||[]).push("style",u);var J=x;(i.updateQueue=J)&&(i.flags|=4)}},iv=function(n,i,u,f){u!==f&&(i.flags|=4)};function Sc(n,i){if(!En)switch(n.tailMode){case"hidden":i=n.tail;for(var u=null;i!==null;)i.alternate!==null&&(u=i),i=i.sibling;u===null?n.tail=null:u.sibling=null;break;case"collapsed":u=n.tail;for(var f=null;u!==null;)u.alternate!==null&&(f=u),u=u.sibling;f===null?i||n.tail===null?n.tail=null:n.tail.sibling=null:f.sibling=null}}function Ir(n){var i=n.alternate!==null&&n.alternate.child===n.child,u=0,f=0;if(i)for(var h=n.child;h!==null;)u|=h.lanes|h.childLanes,f|=h.subtreeFlags&14680064,f|=h.flags&14680064,h.return=n,h=h.sibling;else for(h=n.child;h!==null;)u|=h.lanes|h.childLanes,f|=h.subtreeFlags,f|=h.flags,h.return=n,h=h.sibling;return n.subtreeFlags|=f,n.childLanes=u,i}function ch(n,i,u){var f=i.pendingProps;switch(Ld(i),i.tag){case 2:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return Ir(i),null;case 1:return zn(i.type)&&Ba(),Ir(i),null;case 3:return f=i.stateNode,Gs(),tn(qn),tn(Rn),dc(),f.pendingContext&&(f.context=f.pendingContext,f.pendingContext=null),(n===null||n.child===null)&&(Un(i)?i.flags|=4:n===null||n.memoizedState.isDehydrated&&!(i.flags&256)||(i.flags|=1024,Sa!==null&&($c(Sa),Sa=null))),_i(n,i),Ir(i),null;case 5:Zp(i);var h=Il(cc.current);if(u=i.type,n!==null&&i.stateNode!=null)ir(n,i,u,f,h),n.ref!==i.ref&&(i.flags|=512,i.flags|=2097152);else{if(!f){if(i.stateNode===null)throw Error(s(166));return Ir(i),null}if(n=Il(Wa.current),Un(i)){f=i.stateNode,u=i.type;var x=i.memoizedProps;switch(f[na]=i,f[rc]=x,n=(i.mode&1)!==0,u){case"dialog":Xt("cancel",f),Xt("close",f);break;case"iframe":case"object":case"embed":Xt("load",f);break;case"video":case"audio":for(h=0;h<Ju.length;h++)Xt(Ju[h],f);break;case"source":Xt("error",f);break;case"img":case"image":case"link":Xt("error",f),Xt("load",f);break;case"details":Xt("toggle",f);break;case"input":An(f,x),Xt("invalid",f);break;case"select":f._wrapperState={wasMultiple:!!x.multiple},Xt("invalid",f);break;case"textarea":cr(f,x),Xt("invalid",f)}wn(u,x),h=null;for(var k in x)if(x.hasOwnProperty(k)){var A=x[k];k==="children"?typeof A=="string"?f.textContent!==A&&(x.suppressHydrationWarning!==!0&&Md(f.textContent,A,n),h=["children",A]):typeof A=="number"&&f.textContent!==""+A&&(x.suppressHydrationWarning!==!0&&Md(f.textContent,A,n),h=["children",""+A]):g.hasOwnProperty(k)&&A!=null&&k==="onScroll"&&Xt("scroll",f)}switch(u){case"input":pn(f),Ri(f,x,!0);break;case"textarea":pn(f),ga(f);break;case"select":case"option":break;default:typeof x.onClick=="function"&&(f.onclick=Od)}f=h,i.updateQueue=f,f!==null&&(i.flags|=4)}else{k=h.nodeType===9?h:h.ownerDocument,n==="http://www.w3.org/1999/xhtml"&&(n=Kn(u)),n==="http://www.w3.org/1999/xhtml"?u==="script"?(n=k.createElement("div"),n.innerHTML="<script><\/script>",n=n.removeChild(n.firstChild)):typeof f.is=="string"?n=k.createElement(u,{is:f.is}):(n=k.createElement(u),u==="select"&&(k=n,f.multiple?k.multiple=!0:f.size&&(k.size=f.size))):n=k.createElementNS(n,u),n[na]=i,n[rc]=f,Zs(n,i,!1,!1),i.stateNode=n;e:{switch(k=Sn(u,f),u){case"dialog":Xt("cancel",n),Xt("close",n),h=f;break;case"iframe":case"object":case"embed":Xt("load",n),h=f;break;case"video":case"audio":for(h=0;h<Ju.length;h++)Xt(Ju[h],n);h=f;break;case"source":Xt("error",n),h=f;break;case"img":case"image":case"link":Xt("error",n),Xt("load",n),h=f;break;case"details":Xt("toggle",n),h=f;break;case"input":An(n,f),h=xn(n,f),Xt("invalid",n);break;case"option":h=f;break;case"select":n._wrapperState={wasMultiple:!!f.multiple},h=be({},f,{value:void 0}),Xt("invalid",n);break;case"textarea":cr(n,f),h=ur(n,f),Xt("invalid",n);break;default:h=f}wn(u,h),A=h;for(x in A)if(A.hasOwnProperty(x)){var N=A[x];x==="style"?Gt(n,N):x==="dangerouslySetInnerHTML"?(N=N?N.__html:void 0,N!=null&&ro(n,N)):x==="children"?typeof N=="string"?(u!=="textarea"||N!=="")&&Di(n,N):typeof N=="number"&&Di(n,""+N):x!=="suppressContentEditableWarning"&&x!=="suppressHydrationWarning"&&x!=="autoFocus"&&(g.hasOwnProperty(x)?N!=null&&x==="onScroll"&&Xt("scroll",n):N!=null&&ae(n,x,N,k))}switch(u){case"input":pn(n),Ri(n,f,!1);break;case"textarea":pn(n),ga(n);break;case"option":f.value!=null&&n.setAttribute("value",""+tt(f.value));break;case"select":n.multiple=!!f.multiple,x=f.value,x!=null?nr(n,!!f.multiple,x,!1):f.defaultValue!=null&&nr(n,!!f.multiple,f.defaultValue,!0);break;default:typeof h.onClick=="function"&&(n.onclick=Od)}switch(u){case"button":case"input":case"select":case"textarea":f=!!f.autoFocus;break e;case"img":f=!0;break e;default:f=!1}}f&&(i.flags|=4)}i.ref!==null&&(i.flags|=512,i.flags|=2097152)}return Ir(i),null;case 6:if(n&&i.stateNode!=null)iv(n,i,n.memoizedProps,f);else{if(typeof f!="string"&&i.stateNode===null)throw Error(s(166));if(u=Il(cc.current),Il(Wa.current),Un(i)){if(f=i.stateNode,u=i.memoizedProps,f[na]=i,(x=f.nodeValue!==u)&&(n=pi,n!==null))switch(n.tag){case 3:Md(f.nodeValue,u,(n.mode&1)!==0);break;case 5:n.memoizedProps.suppressHydrationWarning!==!0&&Md(f.nodeValue,u,(n.mode&1)!==0)}x&&(i.flags|=4)}else f=(u.nodeType===9?u:u.ownerDocument).createTextNode(f),f[na]=i,i.stateNode=f}return Ir(i),null;case 13:if(tn(Dn),f=i.memoizedState,n===null||n.memoizedState!==null&&n.memoizedState.dehydrated!==null){if(En&&hi!==null&&i.mode&1&&!(i.flags&128))Hm(),xo(),i.flags|=98560,x=!1;else if(x=Un(i),f!==null&&f.dehydrated!==null){if(n===null){if(!x)throw Error(s(318));if(x=i.memoizedState,x=x!==null?x.dehydrated:null,!x)throw Error(s(317));x[na]=i}else xo(),!(i.flags&128)&&(i.memoizedState=null),i.flags|=4;Ir(i),x=!1}else Sa!==null&&($c(Sa),Sa=null),x=!0;if(!x)return i.flags&65536?i:null}return i.flags&128?(i.lanes=u,i):(f=f!==null,f!==(n!==null&&n.memoizedState!==null)&&f&&(i.child.flags|=8192,i.mode&1&&(n===null||Dn.current&1?ar===0&&(ar=3):mh())),i.updateQueue!==null&&(i.flags|=4),Ir(i),null);case 4:return Gs(),_i(n,i),n===null&&ec(i.stateNode.containerInfo),Ir(i),null;case 10:return Gp(i.type._context),Ir(i),null;case 17:return zn(i.type)&&Ba(),Ir(i),null;case 19:if(tn(Dn),x=i.memoizedState,x===null)return Ir(i),null;if(f=(i.flags&128)!==0,k=x.rendering,k===null)if(f)Sc(x,!1);else{if(ar!==0||n!==null&&n.flags&128)for(n=i.child;n!==null;){if(k=Pd(n),k!==null){for(i.flags|=128,Sc(x,!1),f=k.updateQueue,f!==null&&(i.updateQueue=f,i.flags|=4),i.subtreeFlags=0,f=u,u=i.child;u!==null;)x=u,n=f,x.flags&=14680066,k=x.alternate,k===null?(x.childLanes=0,x.lanes=n,x.child=null,x.subtreeFlags=0,x.memoizedProps=null,x.memoizedState=null,x.updateQueue=null,x.dependencies=null,x.stateNode=null):(x.childLanes=k.childLanes,x.lanes=k.lanes,x.child=k.child,x.subtreeFlags=0,x.deletions=null,x.memoizedProps=k.memoizedProps,x.memoizedState=k.memoizedState,x.updateQueue=k.updateQueue,x.type=k.type,n=k.dependencies,x.dependencies=n===null?null:{lanes:n.lanes,firstContext:n.firstContext}),u=u.sibling;return gn(Dn,Dn.current&1|2),i.child}n=n.sibling}x.tail!==null&&Qt()>ru&&(i.flags|=128,f=!0,Sc(x,!1),i.lanes=4194304)}else{if(!f)if(n=Pd(k),n!==null){if(i.flags|=128,f=!0,u=n.updateQueue,u!==null&&(i.updateQueue=u,i.flags|=4),Sc(x,!0),x.tail===null&&x.tailMode==="hidden"&&!k.alternate&&!En)return Ir(i),null}else 2*Qt()-x.renderingStartTime>ru&&u!==1073741824&&(i.flags|=128,f=!0,Sc(x,!1),i.lanes=4194304);x.isBackwards?(k.sibling=i.child,i.child=k):(u=x.last,u!==null?u.sibling=k:i.child=k,x.last=k)}return x.tail!==null?(i=x.tail,x.rendering=i,x.tail=i.sibling,x.renderingStartTime=Qt(),i.sibling=null,u=Dn.current,gn(Dn,f?u&1|2:u&1),i):(Ir(i),null);case 22:case 23:return gh(),f=i.memoizedState!==null,n!==null&&n.memoizedState!==null!==f&&(i.flags|=8192),f&&i.mode&1?Li&1073741824&&(Ir(i),i.subtreeFlags&6&&(i.flags|=8192)):Ir(i),null;case 24:return null;case 25:return null}throw Error(s(156,i.tag))}function av(n,i){switch(Ld(i),i.tag){case 1:return zn(i.type)&&Ba(),n=i.flags,n&65536?(i.flags=n&-65537|128,i):null;case 3:return Gs(),tn(qn),tn(Rn),dc(),n=i.flags,n&65536&&!(n&128)?(i.flags=n&-65537|128,i):null;case 5:return Zp(i),null;case 13:if(tn(Dn),n=i.memoizedState,n!==null&&n.dehydrated!==null){if(i.alternate===null)throw Error(s(340));xo()}return n=i.flags,n&65536?(i.flags=n&-65537|128,i):null;case 19:return tn(Dn),null;case 4:return Gs(),null;case 10:return Gp(i.type._context),null;case 22:case 23:return gh(),null;case 24:return null;default:return null}}var Yl=!1,Er=!1,N0=typeof WeakSet=="function"?WeakSet:Set,Ne=null;function el(n,i){var u=n.ref;if(u!==null)if(typeof u=="function")try{u(null)}catch(f){Nn(n,i,f)}else u.current=null}function dh(n,i,u){try{u()}catch(f){Nn(n,i,f)}}var fh=!1;function P0(n,i){if(jl=Po,n=Uo(),_s(n)){if("selectionStart"in n)var u={start:n.selectionStart,end:n.selectionEnd};else e:{u=(u=n.ownerDocument)&&u.defaultView||window;var f=u.getSelection&&u.getSelection();if(f&&f.rangeCount!==0){u=f.anchorNode;var h=f.anchorOffset,x=f.focusNode;f=f.focusOffset;try{u.nodeType,x.nodeType}catch{u=null;break e}var k=0,A=-1,N=-1,J=0,he=0,ge=n,pe=null;t:for(;;){for(var je;ge!==u||h!==0&&ge.nodeType!==3||(A=k+h),ge!==x||f!==0&&ge.nodeType!==3||(N=k+f),ge.nodeType===3&&(k+=ge.nodeValue.length),(je=ge.firstChild)!==null;)pe=ge,ge=je;for(;;){if(ge===n)break t;if(pe===u&&++J===h&&(A=k),pe===x&&++he===f&&(N=k),(je=ge.nextSibling)!==null)break;ge=pe,pe=ge.parentNode}ge=je}u=A===-1||N===-1?null:{start:A,end:N}}else u=null}u=u||{start:0,end:0}}else u=null;for(nc={focusedElem:n,selectionRange:u},Po=!1,Ne=i;Ne!==null;)if(i=Ne,n=i.child,(i.subtreeFlags&1028)!==0&&n!==null)n.return=i,Ne=n;else for(;Ne!==null;){i=Ne;try{var Fe=i.alternate;if(i.flags&1024)switch(i.tag){case 0:case 11:case 15:break;case 1:if(Fe!==null){var Ue=Fe.memoizedProps,Vn=Fe.memoizedState,W=i.stateNode,I=W.getSnapshotBeforeUpdate(i.elementType===i.type?Ue:vi(i.type,Ue),Vn);W.__reactInternalSnapshotBeforeUpdate=I}break;case 3:var K=i.stateNode.containerInfo;K.nodeType===1?K.textContent="":K.nodeType===9&&K.documentElement&&K.removeChild(K.documentElement);break;case 5:case 6:case 4:case 17:break;default:throw Error(s(163))}}catch(ve){Nn(i,i.return,ve)}if(n=i.sibling,n!==null){n.return=i.return,Ne=n;break}Ne=i.return}return Fe=fh,fh=!1,Fe}function eu(n,i,u){var f=i.updateQueue;if(f=f!==null?f.lastEffect:null,f!==null){var h=f=f.next;do{if((h.tag&n)===n){var x=h.destroy;h.destroy=void 0,x!==void 0&&dh(i,u,x)}h=h.next}while(h!==f)}}function pf(n,i){if(i=i.updateQueue,i=i!==null?i.lastEffect:null,i!==null){var u=i=i.next;do{if((u.tag&n)===n){var f=u.create;u.destroy=f()}u=u.next}while(u!==i)}}function hf(n){var i=n.ref;if(i!==null){var u=n.stateNode;switch(n.tag){case 5:n=u;break;default:n=u}typeof i=="function"?i(n):i.current=n}}function ov(n){var i=n.alternate;i!==null&&(n.alternate=null,ov(i)),n.child=null,n.deletions=null,n.sibling=null,n.tag===5&&(i=n.stateNode,i!==null&&(delete i[na],delete i[rc],delete i[Np],delete i[Pp],delete i[Bs])),n.stateNode=null,n.return=null,n.dependencies=null,n.memoizedProps=null,n.memoizedState=null,n.pendingProps=null,n.stateNode=null,n.updateQueue=null}function gf(n){return n.tag===5||n.tag===3||n.tag===4}function Cc(n){e:for(;;){for(;n.sibling===null;){if(n.return===null||gf(n.return))return null;n=n.return}for(n.sibling.return=n.return,n=n.sibling;n.tag!==5&&n.tag!==6&&n.tag!==18;){if(n.flags&2||n.child===null||n.tag===4)continue e;n.child.return=n,n=n.child}if(!(n.flags&2))return n.stateNode}}function Ya(n,i,u){var f=n.tag;if(f===5||f===6)n=n.stateNode,i?u.nodeType===8?u.parentNode.insertBefore(n,i):u.insertBefore(n,i):(u.nodeType===8?(i=u.parentNode,i.insertBefore(n,u)):(i=u,i.appendChild(n)),u=u._reactRootContainer,u!=null||i.onclick!==null||(i.onclick=Od));else if(f!==4&&(n=n.child,n!==null))for(Ya(n,i,u),n=n.sibling;n!==null;)Ya(n,i,u),n=n.sibling}function Ga(n,i,u){var f=n.tag;if(f===5||f===6)n=n.stateNode,i?u.insertBefore(n,i):u.appendChild(n);else if(f!==4&&(n=n.child,n!==null))for(Ga(n,i,u),n=n.sibling;n!==null;)Ga(n,i,u),n=n.sibling}var Mn=null,Qr=!1;function oa(n,i,u){for(u=u.child;u!==null;)wo(n,i,u),u=u.sibling}function wo(n,i,u){if(si&&typeof si.onCommitFiberUnmount=="function")try{si.onCommitFiberUnmount(_o,u)}catch{}switch(u.tag){case 5:Er||el(u,i);case 6:var f=Mn,h=Qr;Mn=null,oa(n,i,u),Mn=f,Qr=h,Mn!==null&&(Qr?(n=Mn,u=u.stateNode,n.nodeType===8?n.parentNode.removeChild(u):n.removeChild(u)):Mn.removeChild(u.stateNode));break;case 18:Mn!==null&&(Qr?(n=Mn,u=u.stateNode,n.nodeType===8?Is(n.parentNode,u):n.nodeType===1&&Is(n,u),Zi(n)):Is(Mn,u.stateNode));break;case 4:f=Mn,h=Qr,Mn=u.stateNode.containerInfo,Qr=!0,oa(n,i,u),Mn=f,Qr=h;break;case 0:case 11:case 14:case 15:if(!Er&&(f=u.updateQueue,f!==null&&(f=f.lastEffect,f!==null))){h=f=f.next;do{var x=h,k=x.destroy;x=x.tag,k!==void 0&&(x&2||x&4)&&dh(u,i,k),h=h.next}while(h!==f)}oa(n,i,u);break;case 1:if(!Er&&(el(u,i),f=u.stateNode,typeof f.componentWillUnmount=="function"))try{f.props=u.memoizedProps,f.state=u.memoizedState,f.componentWillUnmount()}catch(A){Nn(u,i,A)}oa(n,i,u);break;case 21:oa(n,i,u);break;case 22:u.mode&1?(Er=(f=Er)||u.memoizedState!==null,oa(n,i,u),Er=f):oa(n,i,u);break;default:oa(n,i,u)}}function lv(n){var i=n.updateQueue;if(i!==null){n.updateQueue=null;var u=n.stateNode;u===null&&(u=n.stateNode=new N0),i.forEach(function(f){var h=H0.bind(null,n,f);u.has(f)||(u.add(f),f.then(h,h))})}}function Ea(n,i){var u=i.deletions;if(u!==null)for(var f=0;f<u.length;f++){var h=u[f];try{var x=n,k=i,A=k;e:for(;A!==null;){switch(A.tag){case 5:Mn=A.stateNode,Qr=!1;break e;case 3:Mn=A.stateNode.containerInfo,Qr=!0;break e;case 4:Mn=A.stateNode.containerInfo,Qr=!0;break e}A=A.return}if(Mn===null)throw Error(s(160));wo(x,k,h),Mn=null,Qr=!1;var N=h.alternate;N!==null&&(N.return=null),h.return=null}catch(J){Nn(h,i,J)}}if(i.subtreeFlags&12854)for(i=i.child;i!==null;)sv(i,n),i=i.sibling}function sv(n,i){var u=n.alternate,f=n.flags;switch(n.tag){case 0:case 11:case 14:case 15:if(Ea(i,n),Ta(n),f&4){try{eu(3,n,n.return),pf(3,n)}catch(Ue){Nn(n,n.return,Ue)}try{eu(5,n,n.return)}catch(Ue){Nn(n,n.return,Ue)}}break;case 1:Ea(i,n),Ta(n),f&512&&u!==null&&el(u,u.return);break;case 5:if(Ea(i,n),Ta(n),f&512&&u!==null&&el(u,u.return),n.flags&32){var h=n.stateNode;try{Di(h,"")}catch(Ue){Nn(n,n.return,Ue)}}if(f&4&&(h=n.stateNode,h!=null)){var x=n.memoizedProps,k=u!==null?u.memoizedProps:x,A=n.type,N=n.updateQueue;if(n.updateQueue=null,N!==null)try{A==="input"&&x.type==="radio"&&x.name!=null&&tr(h,x),Sn(A,k);var J=Sn(A,x);for(k=0;k<N.length;k+=2){var he=N[k],ge=N[k+1];he==="style"?Gt(h,ge):he==="dangerouslySetInnerHTML"?ro(h,ge):he==="children"?Di(h,ge):ae(h,he,ge,J)}switch(A){case"input":Gn(h,x);break;case"textarea":_r(h,x);break;case"select":var pe=h._wrapperState.wasMultiple;h._wrapperState.wasMultiple=!!x.multiple;var je=x.value;je!=null?nr(h,!!x.multiple,je,!1):pe!==!!x.multiple&&(x.defaultValue!=null?nr(h,!!x.multiple,x.defaultValue,!0):nr(h,!!x.multiple,x.multiple?[]:"",!1))}h[rc]=x}catch(Ue){Nn(n,n.return,Ue)}}break;case 6:if(Ea(i,n),Ta(n),f&4){if(n.stateNode===null)throw Error(s(162));h=n.stateNode,x=n.memoizedProps;try{h.nodeValue=x}catch(Ue){Nn(n,n.return,Ue)}}break;case 3:if(Ea(i,n),Ta(n),f&4&&u!==null&&u.memoizedState.isDehydrated)try{Zi(i.containerInfo)}catch(Ue){Nn(n,n.return,Ue)}break;case 4:Ea(i,n),Ta(n);break;case 13:Ea(i,n),Ta(n),h=n.child,h.flags&8192&&(x=h.memoizedState!==null,h.stateNode.isHidden=x,!x||h.alternate!==null&&h.alternate.memoizedState!==null||(hh=Qt())),f&4&&lv(n);break;case 22:if(he=u!==null&&u.memoizedState!==null,n.mode&1?(Er=(J=Er)||he,Ea(i,n),Er=J):Ea(i,n),Ta(n),f&8192){if(J=n.memoizedState!==null,(n.stateNode.isHidden=J)&&!he&&n.mode&1)for(Ne=n,he=n.child;he!==null;){for(ge=Ne=he;Ne!==null;){switch(pe=Ne,je=pe.child,pe.tag){case 0:case 11:case 14:case 15:eu(4,pe,pe.return);break;case 1:el(pe,pe.return);var Fe=pe.stateNode;if(typeof Fe.componentWillUnmount=="function"){f=pe,u=pe.return;try{i=f,Fe.props=i.memoizedProps,Fe.state=i.memoizedState,Fe.componentWillUnmount()}catch(Ue){Nn(f,u,Ue)}}break;case 5:el(pe,pe.return);break;case 22:if(pe.memoizedState!==null){cv(ge);continue}}je!==null?(je.return=pe,Ne=je):cv(ge)}he=he.sibling}e:for(he=null,ge=n;;){if(ge.tag===5){if(he===null){he=ge;try{h=ge.stateNode,J?(x=h.style,typeof x.setProperty=="function"?x.setProperty("display","none","important"):x.display="none"):(A=ge.stateNode,N=ge.memoizedProps.style,k=N!=null&&N.hasOwnProperty("display")?N.display:null,A.style.display=wt("display",k))}catch(Ue){Nn(n,n.return,Ue)}}}else if(ge.tag===6){if(he===null)try{ge.stateNode.nodeValue=J?"":ge.memoizedProps}catch(Ue){Nn(n,n.return,Ue)}}else if((ge.tag!==22&&ge.tag!==23||ge.memoizedState===null||ge===n)&&ge.child!==null){ge.child.return=ge,ge=ge.child;continue}if(ge===n)break e;for(;ge.sibling===null;){if(ge.return===null||ge.return===n)break e;he===ge&&(he=null),ge=ge.return}he===ge&&(he=null),ge.sibling.return=ge.return,ge=ge.sibling}}break;case 19:Ea(i,n),Ta(n),f&4&&lv(n);break;case 21:break;default:Ea(i,n),Ta(n)}}function Ta(n){var i=n.flags;if(i&2){try{e:{for(var u=n.return;u!==null;){if(gf(u)){var f=u;break e}u=u.return}throw Error(s(160))}switch(f.tag){case 5:var h=f.stateNode;f.flags&32&&(Di(h,""),f.flags&=-33);var x=Cc(n);Ga(n,x,h);break;case 3:case 4:var k=f.stateNode.containerInfo,A=Cc(n);Ya(n,A,k);break;default:throw Error(s(161))}}catch(N){Nn(n,n.return,N)}n.flags&=-3}i&4096&&(n.flags&=-4097)}function Ec(n,i,u){Ne=n,uv(n)}function uv(n,i,u){for(var f=(n.mode&1)!==0;Ne!==null;){var h=Ne,x=h.child;if(h.tag===22&&f){var k=h.memoizedState!==null||Yl;if(!k){var A=h.alternate,N=A!==null&&A.memoizedState!==null||Er;A=Yl;var J=Er;if(Yl=k,(Er=N)&&!J)for(Ne=h;Ne!==null;)k=Ne,N=k.child,k.tag===22&&k.memoizedState!==null?Tc(h):N!==null?(N.return=k,Ne=N):Tc(h);for(;x!==null;)Ne=x,uv(x),x=x.sibling;Ne=h,Yl=A,Er=J}ph(n)}else h.subtreeFlags&8772&&x!==null?(x.return=h,Ne=x):ph(n)}}function ph(n){for(;Ne!==null;){var i=Ne;if(i.flags&8772){var u=i.alternate;try{if(i.flags&8772)switch(i.tag){case 0:case 11:case 15:Er||pf(5,i);break;case 1:var f=i.stateNode;if(i.flags&4&&!Er)if(u===null)f.componentDidMount();else{var h=i.elementType===i.type?u.memoizedProps:vi(i.type,u.memoizedProps);f.componentDidUpdate(h,u.memoizedState,f.__reactInternalSnapshotBeforeUpdate)}var x=i.updateQueue;x!==null&&qp(i,x,f);break;case 3:var k=i.updateQueue;if(k!==null){if(u=null,i.child!==null)switch(i.child.tag){case 5:u=i.child.stateNode;break;case 1:u=i.child.stateNode}qp(i,k,u)}break;case 5:var A=i.stateNode;if(u===null&&i.flags&4){u=A;var N=i.memoizedProps;switch(i.type){case"button":case"input":case"select":case"textarea":N.autoFocus&&u.focus();break;case"img":N.src&&(u.src=N.src)}}break;case 6:break;case 4:break;case 12:break;case 13:if(i.memoizedState===null){var J=i.alternate;if(J!==null){var he=J.memoizedState;if(he!==null){var ge=he.dehydrated;ge!==null&&Zi(ge)}}}break;case 19:case 17:case 21:case 22:case 23:case 25:break;default:throw Error(s(163))}Er||i.flags&512&&hf(i)}catch(pe){Nn(i,i.return,pe)}}if(i===n){Ne=null;break}if(u=i.sibling,u!==null){u.return=i.return,Ne=u;break}Ne=i.return}}function cv(n){for(;Ne!==null;){var i=Ne;if(i===n){Ne=null;break}var u=i.sibling;if(u!==null){u.return=i.return,Ne=u;break}Ne=i.return}}function Tc(n){for(;Ne!==null;){var i=Ne;try{switch(i.tag){case 0:case 11:case 15:var u=i.return;try{pf(4,i)}catch(N){Nn(i,u,N)}break;case 1:var f=i.stateNode;if(typeof f.componentDidMount=="function"){var h=i.return;try{f.componentDidMount()}catch(N){Nn(i,h,N)}}var x=i.return;try{hf(i)}catch(N){Nn(i,x,N)}break;case 5:var k=i.return;try{hf(i)}catch(N){Nn(i,k,N)}}}catch(N){Nn(i,i.return,N)}if(i===n){Ne=null;break}var A=i.sibling;if(A!==null){A.return=i.return,Ne=A;break}Ne=i.return}}var dv=Math.ceil,mf=ce.ReactCurrentDispatcher,Gl=ce.ReactCurrentOwner,Ur=ce.ReactCurrentBatchConfig,jt=0,Zn=null,Hn=null,Tr=0,Li=0,tu=fi(0),ar=0,Kl=null,Ql=0,ql=0,kc=0,nu=null,bi=null,hh=0,ru=1/0,So=null,tl=!1,Rc=null,la=null,vf=!1,nl=null,Dc=0,iu=0,au=null,Xl=-1,Mc=0;function mn(){return jt&6?Qt():Xl!==-1?Xl:Xl=Qt()}function zi(n){return n.mode&1?jt&2&&Tr!==0?Tr&-Tr:Pl.transition!==null?(Mc===0&&(Mc=El()),Mc):(n=Nt,n!==0||(n=window.event,n=n===void 0?16:Bu(n.type)),n):1}function Ni(n,i,u,f){if(50<iu)throw iu=0,au=null,Error(s(185));No(n,u,f),(!(jt&2)||n!==Zn)&&(n===Zn&&(!(jt&2)&&(ql|=u),ar===4&&rl(n,Tr)),mr(n,f),u===1&&jt===0&&!(i.mode&1)&&(ru=Qt()+500,ac&&Gr()))}function mr(n,i){var u=n.callbackNode;Cs(n,i);var f=Na(n,n===Zn?Tr:0);if(f===0)u!==null&&Cn(u),n.callbackNode=null,n.callbackPriority=0;else if(i=f&-f,n.callbackPriority!==i){if(u!=null&&Cn(u),i===1)n.tag===0?Fp(jc.bind(null,n)):Ho(jc.bind(null,n)),_0(function(){!(jt&6)&&Gr()}),u=null;else{switch(Uu(f)){case 1:u=gt;break;case 4:u=za;break;case 16:u=lo;break;case 536870912:u=so;break;default:u=lo}u=vv(u,fv.bind(null,n))}n.callbackPriority=i,n.callbackNode=u}}function fv(n,i){if(Xl=-1,Mc=0,jt&6)throw Error(s(327));var u=n.callbackNode;if(ou()&&n.callbackNode!==u)return null;var f=Na(n,n===Zn?Tr:0);if(f===0)return null;if(f&30||f&n.expiredLanes||i)i=bf(n,f);else{i=f;var h=jt;jt|=2;var x=pv();(Zn!==n||Tr!==i)&&(So=null,ru=Qt()+500,Zl(n,i));do try{I0();break}catch(A){xf(n,A)}while(!0);Yp(),mf.current=x,jt=h,Hn!==null?i=0:(Zn=null,Tr=0,i=ar)}if(i!==0){if(i===2&&(h=co(n),h!==0&&(f=h,i=Oc(n,h))),i===1)throw u=Kl,Zl(n,0),rl(n,f),mr(n,Qt()),u;if(i===6)rl(n,f);else{if(h=n.current.alternate,!(f&30)&&!Ac(h)&&(i=bf(n,f),i===2&&(x=co(n),x!==0&&(f=x,i=Oc(n,x))),i===1))throw u=Kl,Zl(n,0),rl(n,f),mr(n,Qt()),u;switch(n.finishedWork=h,n.finishedLanes=f,i){case 0:case 1:throw Error(s(345));case 2:es(n,bi,So);break;case 3:if(rl(n,f),(f&130023424)===f&&(i=hh+500-Qt(),10<i)){if(Na(n,0)!==0)break;if(h=n.suspendedLanes,(h&f)!==f){mn(),n.pingedLanes|=n.suspendedLanes&h;break}n.timeoutHandle=$d(es.bind(null,n,bi,So),i);break}es(n,bi,So);break;case 4:if(rl(n,f),(f&4194240)===f)break;for(i=n.eventTimes,h=-1;0<f;){var k=31-Vr(f);x=1<<k,k=i[k],k>h&&(h=k),f&=~x}if(f=h,f=Qt()-f,f=(120>f?120:480>f?480:1080>f?1080:1920>f?1920:3e3>f?3e3:4320>f?4320:1960*dv(f/1960))-f,10<f){n.timeoutHandle=$d(es.bind(null,n,bi,So),f);break}es(n,bi,So);break;case 5:es(n,bi,So);break;default:throw Error(s(329))}}}return mr(n,Qt()),n.callbackNode===u?fv.bind(null,n):null}function Oc(n,i){var u=nu;return n.current.memoizedState.isDehydrated&&(Zl(n,i).flags|=256),n=bf(n,i),n!==2&&(i=bi,bi=u,i!==null&&$c(i)),n}function $c(n){bi===null?bi=n:bi.push.apply(bi,n)}function Ac(n){for(var i=n;;){if(i.flags&16384){var u=i.updateQueue;if(u!==null&&(u=u.stores,u!==null))for(var f=0;f<u.length;f++){var h=u[f],x=h.getSnapshot;h=h.value;try{if(!xa(x(),h))return!1}catch{return!1}}}if(u=i.child,i.subtreeFlags&16384&&u!==null)u.return=i,i=u;else{if(i===n)break;for(;i.sibling===null;){if(i.return===null||i.return===n)return!0;i=i.return}i.sibling.return=i.return,i=i.sibling}}return!0}function rl(n,i){for(i&=~kc,i&=~ql,n.suspendedLanes|=i,n.pingedLanes&=~i,n=n.expirationTimes;0<i;){var u=31-Vr(i),f=1<<u;n[u]=-1,i&=~f}}function jc(n){if(jt&6)throw Error(s(327));ou();var i=Na(n,0);if(!(i&1))return mr(n,Qt()),null;var u=bf(n,i);if(n.tag!==0&&u===2){var f=co(n);f!==0&&(i=f,u=Oc(n,f))}if(u===1)throw u=Kl,Zl(n,0),rl(n,i),mr(n,Qt()),u;if(u===6)throw Error(s(345));return n.finishedWork=n.current.alternate,n.finishedLanes=i,es(n,bi,So),mr(n,Qt()),null}function yf(n,i){var u=jt;jt|=1;try{return n(i)}finally{jt=u,jt===0&&(ru=Qt()+500,ac&&Gr())}}function Jl(n){nl!==null&&nl.tag===0&&!(jt&6)&&ou();var i=jt;jt|=1;var u=Ur.transition,f=Nt;try{if(Ur.transition=null,Nt=1,n)return n()}finally{Nt=f,Ur.transition=u,jt=i,!(jt&6)&&Gr()}}function gh(){Li=tu.current,tn(tu)}function Zl(n,i){n.finishedWork=null,n.finishedLanes=0;var u=n.timeoutHandle;if(u!==-1&&(n.timeoutHandle=-1,Fm(u)),Hn!==null)for(u=Hn.return;u!==null;){var f=u;switch(Ld(f),f.tag){case 1:f=f.type.childContextTypes,f!=null&&Ba();break;case 3:Gs(),tn(qn),tn(Rn),dc();break;case 5:Zp(f);break;case 4:Gs();break;case 13:tn(Dn);break;case 19:tn(Dn);break;case 10:Gp(f.type._context);break;case 22:case 23:gh()}u=u.return}if(Zn=n,Hn=n=il(n.current,null),Tr=Li=i,ar=0,Kl=null,kc=ql=Ql=0,bi=nu=null,Fl!==null){for(i=0;i<Fl.length;i++)if(u=Fl[i],f=u.interleaved,f!==null){u.interleaved=null;var h=f.next,x=u.pending;if(x!==null){var k=x.next;x.next=h,f.next=k}u.pending=f}Fl=null}return n}function xf(n,i){do{var u=Hn;try{if(Yp(),Ge.current=rn,Fd){for(var f=mt.memoizedState;f!==null;){var h=f.queue;h!==null&&(h.pending=null),f=f.next}Fd=!1}if(zt=0,rr=cn=mt=null,fc=!1,pc=0,Gl.current=null,u===null||u.return===null){ar=1,Kl=i,Hn=null;break}e:{var x=n,k=u.return,A=u,N=i;if(i=Tr,A.flags|=32768,N!==null&&typeof N=="object"&&typeof N.then=="function"){var J=N,he=A,ge=he.tag;if(!(he.mode&1)&&(ge===0||ge===11||ge===15)){var pe=he.alternate;pe?(he.updateQueue=pe.updateQueue,he.memoizedState=pe.memoizedState,he.lanes=pe.lanes):(he.updateQueue=null,he.memoizedState=null)}var je=oh(k);if(je!==null){je.flags&=-257,tv(je,k,A,x,i),je.mode&1&&ah(x,J,i),i=je,N=J;var Fe=i.updateQueue;if(Fe===null){var Ue=new Set;Ue.add(N),i.updateQueue=Ue}else Fe.add(N);break e}else{if(!(i&1)){ah(x,J,i),mh();break e}N=Error(s(426))}}else if(En&&A.mode&1){var Vn=oh(k);if(Vn!==null){!(Vn.flags&65536)&&(Vn.flags|=256),tv(Vn,k,A,x,i),lc(Zo(N,A));break e}}x=N=Zo(N,A),ar!==4&&(ar=2),nu===null?nu=[x]:nu.push(x),x=k;do{switch(x.tag){case 3:x.flags|=65536,i&=-i,x.lanes|=i;var W=xc(x,N,i);Km(x,W);break e;case 1:A=N;var I=x.type,K=x.stateNode;if(!(x.flags&128)&&(typeof I.getDerivedStateFromError=="function"||K!==null&&typeof K.componentDidCatch=="function"&&(la===null||!la.has(K)))){x.flags|=65536,i&=-i,x.lanes|=i;var ve=ev(x,A,i);Km(x,ve);break e}}x=x.return}while(x!==null)}hv(u)}catch(Me){i=Me,Hn===u&&u!==null&&(Hn=u=u.return);continue}break}while(!0)}function pv(){var n=mf.current;return mf.current=rn,n===null?rn:n}function mh(){(ar===0||ar===3||ar===2)&&(ar=4),Zn===null||!(Ql&268435455)&&!(ql&268435455)||rl(Zn,Tr)}function bf(n,i){var u=jt;jt|=2;var f=pv();(Zn!==n||Tr!==i)&&(So=null,Zl(n,i));do try{F0();break}catch(h){xf(n,h)}while(!0);if(Yp(),jt=u,mf.current=f,Hn!==null)throw Error(s(261));return Zn=null,Tr=0,ar}function F0(){for(;Hn!==null;)vh(Hn)}function I0(){for(;Hn!==null&&!Lr();)vh(Hn)}function vh(n){var i=xh(n.alternate,n,Li);n.memoizedProps=n.pendingProps,i===null?hv(n):Hn=i,Gl.current=null}function hv(n){var i=n;do{var u=i.alternate;if(n=i.return,i.flags&32768){if(u=av(u,i),u!==null){u.flags&=32767,Hn=u;return}if(n!==null)n.flags|=32768,n.subtreeFlags=0,n.deletions=null;else{ar=6,Hn=null;return}}else if(u=ch(u,i,Li),u!==null){Hn=u;return}if(i=i.sibling,i!==null){Hn=i;return}Hn=i=n}while(i!==null);ar===0&&(ar=5)}function es(n,i,u){var f=Nt,h=Ur.transition;try{Ur.transition=null,Nt=1,U0(n,i,u,f)}finally{Ur.transition=h,Nt=f}return null}function U0(n,i,u,f){do ou();while(nl!==null);if(jt&6)throw Error(s(327));u=n.finishedWork;var h=n.finishedLanes;if(u===null)return null;if(n.finishedWork=null,n.finishedLanes=0,u===n.current)throw Error(s(177));n.callbackNode=null,n.callbackPriority=0;var x=u.lanes|u.childLanes;if(Fu(n,x),n===Zn&&(Hn=Zn=null,Tr=0),!(u.subtreeFlags&2064)&&!(u.flags&2064)||vf||(vf=!0,vv(lo,function(){return ou(),null})),x=(u.flags&15990)!==0,u.subtreeFlags&15990||x){x=Ur.transition,Ur.transition=null;var k=Nt;Nt=1;var A=jt;jt|=4,Gl.current=null,P0(n,u),sv(u,n),Om(nc),Po=!!jl,nc=jl=null,n.current=u,Ec(u),ma(),jt=A,Nt=k,Ur.transition=x}else n.current=u;if(vf&&(vf=!1,nl=n,Dc=h),x=n.pendingLanes,x===0&&(la=null),Nu(u.stateNode),mr(n,Qt()),i!==null)for(f=n.onRecoverableError,u=0;u<i.length;u++)h=i[u],f(h.value,{componentStack:h.stack,digest:h.digest});if(tl)throw tl=!1,n=Rc,Rc=null,n;return Dc&1&&n.tag!==0&&ou(),x=n.pendingLanes,x&1?n===au?iu++:(iu=0,au=n):iu=0,Gr(),null}function ou(){if(nl!==null){var n=Uu(Dc),i=Ur.transition,u=Nt;try{if(Ur.transition=null,Nt=16>n?16:n,nl===null)var f=!1;else{if(n=nl,nl=null,Dc=0,jt&6)throw Error(s(331));var h=jt;for(jt|=4,Ne=n.current;Ne!==null;){var x=Ne,k=x.child;if(Ne.flags&16){var A=x.deletions;if(A!==null){for(var N=0;N<A.length;N++){var J=A[N];for(Ne=J;Ne!==null;){var he=Ne;switch(he.tag){case 0:case 11:case 15:eu(8,he,x)}var ge=he.child;if(ge!==null)ge.return=he,Ne=ge;else for(;Ne!==null;){he=Ne;var pe=he.sibling,je=he.return;if(ov(he),he===J){Ne=null;break}if(pe!==null){pe.return=je,Ne=pe;break}Ne=je}}}var Fe=x.alternate;if(Fe!==null){var Ue=Fe.child;if(Ue!==null){Fe.child=null;do{var Vn=Ue.sibling;Ue.sibling=null,Ue=Vn}while(Ue!==null)}}Ne=x}}if(x.subtreeFlags&2064&&k!==null)k.return=x,Ne=k;else e:for(;Ne!==null;){if(x=Ne,x.flags&2048)switch(x.tag){case 0:case 11:case 15:eu(9,x,x.return)}var W=x.sibling;if(W!==null){W.return=x.return,Ne=W;break e}Ne=x.return}}var I=n.current;for(Ne=I;Ne!==null;){k=Ne;var K=k.child;if(k.subtreeFlags&2064&&K!==null)K.return=k,Ne=K;else e:for(k=I;Ne!==null;){if(A=Ne,A.flags&2048)try{switch(A.tag){case 0:case 11:case 15:pf(9,A)}}catch(Me){Nn(A,A.return,Me)}if(A===k){Ne=null;break e}var ve=A.sibling;if(ve!==null){ve.return=A.return,Ne=ve;break e}Ne=A.return}}if(jt=h,Gr(),si&&typeof si.onPostCommitFiberRoot=="function")try{si.onPostCommitFiberRoot(_o,n)}catch{}f=!0}return f}finally{Nt=u,Ur.transition=i}}return!1}function gv(n,i,u){i=Zo(u,i),i=xc(n,i,1),n=Qo(n,i,1),i=mn(),n!==null&&(No(n,1,i),mr(n,i))}function Nn(n,i,u){if(n.tag===3)gv(n,n,u);else for(;i!==null;){if(i.tag===3){gv(i,n,u);break}else if(i.tag===1){var f=i.stateNode;if(typeof i.type.getDerivedStateFromError=="function"||typeof f.componentDidCatch=="function"&&(la===null||!la.has(f))){n=Zo(u,n),n=ev(i,n,1),i=Qo(i,n,1),n=mn(),i!==null&&(No(i,1,n),mr(i,n));break}}i=i.return}}function yh(n,i,u){var f=n.pingCache;f!==null&&f.delete(i),i=mn(),n.pingedLanes|=n.suspendedLanes&u,Zn===n&&(Tr&u)===u&&(ar===4||ar===3&&(Tr&130023424)===Tr&&500>Qt()-hh?Zl(n,0):kc|=u),mr(n,i)}function mv(n,i){i===0&&(n.mode&1?(i=Lo,Lo<<=1,!(Lo&130023424)&&(Lo=4194304)):i=1);var u=mn();n=Va(n,i),n!==null&&(No(n,i,u),mr(n,u))}function B0(n){var i=n.memoizedState,u=0;i!==null&&(u=i.retryLane),mv(n,u)}function H0(n,i){var u=0;switch(n.tag){case 13:var f=n.stateNode,h=n.memoizedState;h!==null&&(u=h.retryLane);break;case 19:f=n.stateNode;break;default:throw Error(s(314))}f!==null&&f.delete(i),mv(n,u)}var xh;xh=function(n,i,u){if(n!==null)if(n.memoizedProps!==i.pendingProps||qn.current)gr=!0;else{if(!(n.lanes&u)&&!(i.flags&128))return gr=!1,ff(n,i,u);gr=!!(n.flags&131072)}else gr=!1,En&&i.flags&1048576&&Um(i,Yo,i.index);switch(i.lanes=0,i.tag){case 2:var f=i.type;aa(n,i),n=i.pendingProps;var h=Ai(i,Rn.current);Ws(i,u),h=nt(null,i,f,n,h,u);var x=qo();return i.flags|=1,typeof h=="object"&&h!==null&&typeof h.render=="function"&&h.$$typeof===void 0?(i.tag=1,i.memoizedState=null,i.updateQueue=null,zn(f)?(x=!0,zl(i)):x=!1,i.memoizedState=h.state!==null&&h.state!==void 0?h.state:null,Ko(i),h.updater=nf,i.stateNode=h,h._reactInternals=i,rh(i,f,n,u),i=lh(null,i,f,!0,x,u)):(i.tag=0,En&&x&&Ip(i),Bn(null,i,h,u),i=i.child),i;case 16:f=i.elementType;e:{switch(aa(n,i),n=i.pendingProps,h=f._init,f=h(f._payload),i.type=f,h=i.tag=W0(f),n=vi(f,n),h){case 0:i=lf(null,i,f,n,u);break e;case 1:i=z0(null,i,f,n,u);break e;case 11:i=of(null,i,f,n,u);break e;case 14:i=yi(null,i,f,vi(f.type,n),u);break e}throw Error(s(306,f,""))}return i;case 0:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:vi(f,h),lf(n,i,f,h,u);case 1:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:vi(f,h),z0(n,i,f,h,u);case 3:e:{if(sf(i),n===null)throw Error(s(387));f=i.pendingProps,x=i.memoizedState,h=x.element,Gm(n,i),Nd(i,f,null,u);var k=i.memoizedState;if(f=k.element,x.isDehydrated)if(x={element:f,isDehydrated:!1,cache:k.cache,pendingSuspenseBoundaries:k.pendingSuspenseBoundaries,transitions:k.transitions},i.updateQueue.baseState=x,i.memoizedState=x,i.flags&256){h=Zo(Error(s(423)),i),i=Js(n,i,f,u,h);break e}else if(f!==h){h=Zo(Error(s(424)),i),i=Js(n,i,f,u,h);break e}else for(hi=ba(i.stateNode.containerInfo.firstChild),pi=i,En=!0,Sa=null,u=Sr(i,null,f,u),i.child=u;u;)u.flags=u.flags&-3|4096,u=u.sibling;else{if(xo(),f===h){i=Cr(n,i,u);break e}Bn(n,i,f,u)}i=i.child}return i;case 5:return Jp(i),n===null&&Vp(i),f=i.type,h=i.pendingProps,x=n!==null?n.memoizedProps:null,k=h.children,_l(f,h)?k=null:x!==null&&_l(f,x)&&(i.flags|=32),bc(n,i),Bn(n,i,k,u),i.child;case 6:return n===null&&Vp(i),null;case 13:return nv(n,i,u);case 4:return Xp(i,i.stateNode.containerInfo),f=i.pendingProps,n===null?i.child=Ca(i,null,f,u):Bn(n,i,f,u),i.child;case 11:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:vi(f,h),of(n,i,f,h,u);case 7:return Bn(n,i,i.pendingProps,u),i.child;case 8:return Bn(n,i,i.pendingProps.children,u),i.child;case 12:return Bn(n,i,i.pendingProps.children,u),i.child;case 10:e:{if(f=i.type._context,h=i.pendingProps,x=i.memoizedProps,k=h.value,gn(Ce,f._currentValue),f._currentValue=k,x!==null)if(xa(x.value,k)){if(x.children===h.children&&!qn.current){i=Cr(n,i,u);break e}}else for(x=i.child,x!==null&&(x.return=i);x!==null;){var A=x.dependencies;if(A!==null){k=x.child;for(var N=A.firstContext;N!==null;){if(N.context===f){if(x.tag===1){N=bo(-1,u&-u),N.tag=2;var J=x.updateQueue;if(J!==null){J=J.shared;var he=J.pending;he===null?N.next=N:(N.next=he.next,he.next=N),J.pending=N}}x.lanes|=u,N=x.alternate,N!==null&&(N.lanes|=u),Kp(x.return,u,i),A.lanes|=u;break}N=N.next}}else if(x.tag===10)k=x.type===i.type?null:x.child;else if(x.tag===18){if(k=x.return,k===null)throw Error(s(341));k.lanes|=u,A=k.alternate,A!==null&&(A.lanes|=u),Kp(k,u,i),k=x.sibling}else k=x.child;if(k!==null)k.return=x;else for(k=x;k!==null;){if(k===i){k=null;break}if(x=k.sibling,x!==null){x.return=k.return,k=x;break}k=k.return}x=k}Bn(n,i,h.children,u),i=i.child}return i;case 9:return h=i.type,f=i.pendingProps.children,Ws(i,u),h=nn(h),f=f(h),i.flags|=1,Bn(n,i,f,u),i.child;case 14:return f=i.type,h=vi(f,i.pendingProps),h=vi(f.type,h),yi(n,i,f,h,u);case 15:return Wl(n,i,i.type,i.pendingProps,u);case 17:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:vi(f,h),aa(n,i),i.tag=1,zn(f)?(n=!0,zl(i)):n=!1,Ws(i,u),Zm(i,f,h),rh(i,f,h,u),lh(null,i,f,!0,n,u);case 19:return xi(n,i,u);case 22:return xt(n,i,u)}throw Error(s(156,i.tag))};function vv(n,i){return yn(n,i)}function V0(n,i,u,f){this.tag=n,this.key=u,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.ref=null,this.pendingProps=i,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=f,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function sa(n,i,u,f){return new V0(n,i,u,f)}function bh(n){return n=n.prototype,!(!n||!n.isReactComponent)}function W0(n){if(typeof n=="function")return bh(n)?1:0;if(n!=null){if(n=n.$$typeof,n===bt)return 11;if(n===Ht)return 14}return 2}function il(n,i){var u=n.alternate;return u===null?(u=sa(n.tag,i,n.key,n.mode),u.elementType=n.elementType,u.type=n.type,u.stateNode=n.stateNode,u.alternate=n,n.alternate=u):(u.pendingProps=i,u.type=n.type,u.flags=0,u.subtreeFlags=0,u.deletions=null),u.flags=n.flags&14680064,u.childLanes=n.childLanes,u.lanes=n.lanes,u.child=n.child,u.memoizedProps=n.memoizedProps,u.memoizedState=n.memoizedState,u.updateQueue=n.updateQueue,i=n.dependencies,u.dependencies=i===null?null:{lanes:i.lanes,firstContext:i.firstContext},u.sibling=n.sibling,u.index=n.index,u.ref=n.ref,u}function wf(n,i,u,f,h,x){var k=2;if(f=n,typeof n=="function")bh(n)&&(k=1);else if(typeof n=="string")k=5;else e:switch(n){case ue:return al(u.children,h,x,i);case $e:k=8,h|=8;break;case ft:return n=sa(12,u,i,h|2),n.elementType=ft,n.lanes=x,n;case rt:return n=sa(13,u,i,h),n.elementType=rt,n.lanes=x,n;case Be:return n=sa(19,u,i,h),n.elementType=Be,n.lanes=x,n;case pt:return lu(u,h,x,i);default:if(typeof n=="object"&&n!==null)switch(n.$$typeof){case He:k=10;break e;case Tt:k=9;break e;case bt:k=11;break e;case Ht:k=14;break e;case kt:k=16,f=null;break e}throw Error(s(130,n==null?n:typeof n,""))}return i=sa(k,u,i,h),i.elementType=n,i.type=f,i.lanes=x,i}function al(n,i,u,f){return n=sa(7,n,f,i),n.lanes=u,n}function lu(n,i,u,f){return n=sa(22,n,f,i),n.elementType=pt,n.lanes=u,n.stateNode={isHidden:!1},n}function ts(n,i,u){return n=sa(6,n,null,i),n.lanes=u,n}function wh(n,i,u){return i=sa(4,n.children!==null?n.children:[],n.key,i),i.lanes=u,i.stateNode={containerInfo:n.containerInfo,pendingChildren:null,implementation:n.implementation},i}function yv(n,i,u,f,h){this.tag=i,this.containerInfo=n,this.finishedWork=this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.pendingContext=this.context=null,this.callbackPriority=0,this.eventTimes=Tl(0),this.expirationTimes=Tl(-1),this.entangledLanes=this.finishedLanes=this.mutableReadLanes=this.expiredLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=Tl(0),this.identifierPrefix=f,this.onRecoverableError=h,this.mutableSourceEagerHydrationData=null}function Sf(n,i,u,f,h,x,k,A,N){return n=new yv(n,i,u,A,N),i===1?(i=1,x===!0&&(i|=8)):i=0,x=sa(3,null,null,i),n.current=x,x.stateNode=n,x.memoizedState={element:f,isDehydrated:u,cache:null,transitions:null,pendingSuspenseBoundaries:null},Ko(x),n}function xv(n,i,u){var f=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:le,key:f==null?null:""+f,children:n,containerInfo:i,implementation:u}}function bv(n){if(!n)return Et;n=n._reactInternals;e:{if(De(n)!==n||n.tag!==1)throw Error(s(170));var i=n;do{switch(i.tag){case 3:i=i.stateNode.context;break e;case 1:if(zn(i.type)){i=i.stateNode.__reactInternalMemoizedMergedChildContext;break e}}i=i.return}while(i!==null);throw Error(s(171))}if(n.tag===1){var u=n.type;if(zn(u))return Im(n,u,i)}return i}function Sh(n,i,u,f,h,x,k,A,N){return n=Sf(u,f,!0,n,h,x,k,A,N),n.context=bv(null),u=n.current,f=mn(),h=zi(u),x=bo(f,h),x.callback=i??null,Qo(u,x,h),n.current.lanes=h,No(n,h,f),mr(n,f),n}function Cf(n,i,u,f){var h=i.current,x=mn(),k=zi(h);return u=bv(u),i.context===null?i.context=u:i.pendingContext=u,i=bo(x,k),i.payload={element:n},f=f===void 0?null:f,f!==null&&(i.callback=f),n=Qo(h,i,k),n!==null&&(Ni(n,h,k,x),zd(n,h,k)),k}function Ef(n){if(n=n.current,!n.child)return null;switch(n.child.tag){case 5:return n.child.stateNode;default:return n.child.stateNode}}function wv(n,i){if(n=n.memoizedState,n!==null&&n.dehydrated!==null){var u=n.retryLane;n.retryLane=u!==0&&u<i?u:i}}function Tf(n,i){wv(n,i),(n=n.alternate)&&wv(n,i)}function Sv(){return null}var Ch=typeof reportError=="function"?reportError:function(n){console.error(n)};function ol(n){this._internalRoot=n}kf.prototype.render=ol.prototype.render=function(n){var i=this._internalRoot;if(i===null)throw Error(s(409));Cf(n,i,null,null)},kf.prototype.unmount=ol.prototype.unmount=function(){var n=this._internalRoot;if(n!==null){this._internalRoot=null;var i=n.containerInfo;Jl(function(){Cf(null,n,null,null)}),i[vo]=null}};function kf(n){this._internalRoot=n}kf.prototype.unstable_scheduleHydration=function(n){if(n){var i=Pa();n={blockedOn:null,target:n,priority:i};for(var u=0;u<va.length&&i!==0&&i<va[u].priority;u++);va.splice(u,0,n),u===0&&Ts(n)}};function Eh(n){return!(!n||n.nodeType!==1&&n.nodeType!==9&&n.nodeType!==11)}function Rf(n){return!(!n||n.nodeType!==1&&n.nodeType!==9&&n.nodeType!==11&&(n.nodeType!==8||n.nodeValue!==" react-mount-point-unstable "))}function Cv(){}function Y0(n,i,u,f,h){if(h){if(typeof f=="function"){var x=f;f=function(){var J=Ef(k);x.call(J)}}var k=Sh(i,f,n,0,null,!1,!1,"",Cv);return n._reactRootContainer=k,n[vo]=k.current,ec(n.nodeType===8?n.parentNode:n),Jl(),k}for(;h=n.lastChild;)n.removeChild(h);if(typeof f=="function"){var A=f;f=function(){var J=Ef(N);A.call(J)}}var N=Sf(n,0,!1,null,null,!1,!1,"",Cv);return n._reactRootContainer=N,n[vo]=N.current,ec(n.nodeType===8?n.parentNode:n),Jl(function(){Cf(i,N,u,f)}),N}function Df(n,i,u,f,h){var x=u._reactRootContainer;if(x){var k=x;if(typeof h=="function"){var A=h;h=function(){var N=Ef(k);A.call(N)}}Cf(i,k,n,h)}else k=Y0(u,i,n,h,f);return Ef(k)}Es=function(n){switch(n.tag){case 3:var i=n.stateNode;if(i.current.memoizedState.isDehydrated){var u=ui(i.pendingLanes);u!==0&&(Iu(i,u|1),mr(i,Qt()),!(jt&6)&&(ru=Qt()+500,Gr()))}break;case 13:Jl(function(){var f=Va(n,1);if(f!==null){var h=mn();Ni(f,n,1,h)}}),Tf(n,1)}},Pt=function(n){if(n.tag===13){var i=Va(n,134217728);if(i!==null){var u=mn();Ni(i,n,134217728,u)}Tf(n,134217728)}},yd=function(n){if(n.tag===13){var i=zi(n),u=Va(n,i);if(u!==null){var f=mn();Ni(u,n,i,f)}Tf(n,i)}},Pa=function(){return Nt},lt=function(n,i){var u=Nt;try{return Nt=n,i()}finally{Nt=u}},on=function(n,i,u){switch(i){case"input":if(Gn(n,u),i=u.name,u.type==="radio"&&i!=null){for(u=n;u.parentNode;)u=u.parentNode;for(u=u.querySelectorAll("input[name="+JSON.stringify(""+i)+'][type="radio"]'),i=0;i<u.length;i++){var f=u[i];if(f!==n&&f.form===n.form){var h=yo(f);if(!h)throw Error(s(90));an(f),Gn(f,h)}}}break;case"textarea":_r(n,u);break;case"select":i=u.value,i!=null&&nr(n,!!u.multiple,i,!1)}},wl=yf,Sl=Jl;var Ev={usingClientEntryPoint:!1,Events:[ic,Ye,yo,Xi,io,yf]},_c={findFiberByHostInstance:Ll,bundleType:0,version:"18.3.1",rendererPackageName:"react-dom"},G0={bundleType:_c.bundleType,version:_c.version,rendererPackageName:_c.rendererPackageName,rendererConfig:_c.rendererConfig,overrideHookState:null,overrideHookStateDeletePath:null,overrideHookStateRenamePath:null,overrideProps:null,overridePropsDeletePath:null,overridePropsRenamePath:null,setErrorHandler:null,setSuspenseHandler:null,scheduleUpdate:null,currentDispatcherRef:ce.ReactCurrentDispatcher,findHostInstanceByFiber:function(n){return n=St(n),n===null?null:n.stateNode},findFiberByHostInstance:_c.findFiberByHostInstance||Sv,findHostInstancesForRefresh:null,scheduleRefresh:null,scheduleRoot:null,setRefreshHandler:null,getCurrentFiber:null,reconcilerVersion:"18.3.1-next-f1338f8080-20240426"};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"){var Lc=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!Lc.isDisabled&&Lc.supportsFiber)try{_o=Lc.inject(G0),si=Lc}catch{}}return Hi.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=Ev,Hi.createPortal=function(n,i){var u=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!Eh(i))throw Error(s(200));return xv(n,i,null,u)},Hi.createRoot=function(n,i){if(!Eh(n))throw Error(s(299));var u=!1,f="",h=Ch;return i!=null&&(i.unstable_strictMode===!0&&(u=!0),i.identifierPrefix!==void 0&&(f=i.identifierPrefix),i.onRecoverableError!==void 0&&(h=i.onRecoverableError)),i=Sf(n,1,!1,null,null,u,!1,f,h),n[vo]=i.current,ec(n.nodeType===8?n.parentNode:n),new ol(i)},Hi.findDOMNode=function(n){if(n==null)return null;if(n.nodeType===1)return n;var i=n._reactInternals;if(i===void 0)throw typeof n.render=="function"?Error(s(188)):(n=Object.keys(n).join(","),Error(s(268,n)));return n=St(i),n=n===null?null:n.stateNode,n},Hi.flushSync=function(n){return Jl(n)},Hi.hydrate=function(n,i,u){if(!Rf(i))throw Error(s(200));return Df(null,n,i,!0,u)},Hi.hydrateRoot=function(n,i,u){if(!Eh(n))throw Error(s(405));var f=u!=null&&u.hydratedSources||null,h=!1,x="",k=Ch;if(u!=null&&(u.unstable_strictMode===!0&&(h=!0),u.identifierPrefix!==void 0&&(x=u.identifierPrefix),u.onRecoverableError!==void 0&&(k=u.onRecoverableError)),i=Sh(i,null,n,1,u??null,h,!1,x,k),n[vo]=i.current,ec(n),f)for(n=0;n<f.length;n++)u=f[n],h=u._getVersion,h=h(u._source),i.mutableSourceEagerHydrationData==null?i.mutableSourceEagerHydrationData=[u,h]:i.mutableSourceEagerHydrationData.push(u,h);return new kf(i)},Hi.render=function(n,i,u){if(!Rf(i))throw Error(s(200));return Df(null,n,i,!1,u)},Hi.unmountComponentAtNode=function(n){if(!Rf(n))throw Error(s(40));return n._reactRootContainer?(Jl(function(){Df(null,null,n,!1,function(){n._reactRootContainer=null,n[vo]=null})}),!0):!1},Hi.unstable_batchedUpdates=yf,Hi.unstable_renderSubtreeIntoContainer=function(n,i,u,f){if(!Rf(u))throw Error(s(200));if(n==null||n._reactInternals===void 0)throw Error(s(38));return Df(n,i,u,!1,f)},Hi.version="18.3.1-next-f1338f8080-20240426",Hi}var Vi={},QS;function xD(){if(QS)return Vi;QS=1;var o={};/**
 * @license React
 * react-dom.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */return o.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var r=Je,s=GS(),d=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,g=!1;function b(e){g=e}function S(e){if(!g){for(var t=arguments.length,a=new Array(t>1?t-1:0),l=1;l<t;l++)a[l-1]=arguments[l];E("warn",e,a)}}function y(e){if(!g){for(var t=arguments.length,a=new Array(t>1?t-1:0),l=1;l<t;l++)a[l-1]=arguments[l];E("error",e,a)}}function E(e,t,a){{var l=d.ReactDebugCurrentFrame,c=l.getStackAddendum();c!==""&&(t+="%s",a=a.concat([c]));var p=a.map(function(v){return String(v)});p.unshift("Warning: "+t),Function.prototype.apply.call(console[e],console,p)}}var O=0,$=1,P=2,j=3,H=4,L=5,Q=6,xe=7,ze=8,fe=9,ae=10,ce=11,Ee=12,le=13,ue=14,$e=15,ft=16,He=17,Tt=18,bt=19,rt=21,Be=22,Ht=23,kt=24,pt=25,se=!0,ke=!1,be=!1,B=!1,re=!1,Ve=!0,et=!0,it=!0,ht=!0,Ot=new Set,tt={},vt={};function Ut(e,t){pn(e,t),pn(e+"Capture",t)}function pn(e,t){tt[e]&&y("EventRegistry: More than one plugin attempted to publish the same registration name, `%s`.",e),tt[e]=t;{var a=e.toLowerCase();vt[a]=e,e==="onDoubleClick"&&(vt.ondblclick=e)}for(var l=0;l<t.length;l++)Ot.add(t[l])}var an=typeof window<"u"&&typeof window.document<"u"&&typeof window.document.createElement<"u",Pn=Object.prototype.hasOwnProperty;function xn(e){{var t=typeof Symbol=="function"&&Symbol.toStringTag,a=t&&e[Symbol.toStringTag]||e.constructor.name||"Object";return a}}function An(e){try{return tr(e),!1}catch{return!0}}function tr(e){return""+e}function Gn(e,t){if(An(e))return y("The provided `%s` attribute is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),tr(e)}function Ri(e){if(An(e))return y("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",xn(e)),tr(e)}function ha(e,t){if(An(e))return y("The provided `%s` prop is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),tr(e)}function Hr(e,t){if(An(e))return y("The provided `%s` CSS property is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),tr(e)}function nr(e){if(An(e))return y("The provided HTML markup uses a value of unsupported type %s. This value must be coerced to a string before before using it here.",xn(e)),tr(e)}function ur(e){if(An(e))return y("Form field values (value, checked, defaultValue, or defaultChecked props) must be strings, not %s. This value must be coerced to a string before before using it here.",xn(e)),tr(e)}var cr=0,_r=1,ga=2,Kn=3,xr=4,oi=5,ro=6,Di=":A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD",we=Di+"\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040",Qe=new RegExp("^["+Di+"]["+we+"]*$"),wt={},Gt={};function bn(e){return Pn.call(Gt,e)?!0:Pn.call(wt,e)?!1:Qe.test(e)?(Gt[e]=!0,!0):(wt[e]=!0,y("Invalid attribute name: `%s`",e),!1)}function wn(e,t,a){return t!==null?t.type===cr:a?!1:e.length>2&&(e[0]==="o"||e[0]==="O")&&(e[1]==="n"||e[1]==="N")}function Sn(e,t,a,l){if(a!==null&&a.type===cr)return!1;switch(typeof t){case"function":case"symbol":return!0;case"boolean":{if(l)return!1;if(a!==null)return!a.acceptsBooleans;var c=e.toLowerCase().slice(0,5);return c!=="data-"&&c!=="aria-"}default:return!1}}function dr(e,t,a,l){if(t===null||typeof t>"u"||Sn(e,t,a,l))return!0;if(l)return!1;if(a!==null)switch(a.type){case Kn:return!t;case xr:return t===!1;case oi:return isNaN(t);case ro:return isNaN(t)||t<1}return!1}function vn(e){return Kt.hasOwnProperty(e)?Kt[e]:null}function on(e,t,a,l,c,p,v){this.acceptsBooleans=t===ga||t===Kn||t===xr,this.attributeName=l,this.attributeNamespace=c,this.mustUseProperty=a,this.propertyName=e,this.type=t,this.sanitizeURL=p,this.removeEmptyString=v}var Kt={},Mi=["children","dangerouslySetInnerHTML","defaultValue","defaultChecked","innerHTML","suppressContentEditableWarning","suppressHydrationWarning","style"];Mi.forEach(function(e){Kt[e]=new on(e,cr,!1,e,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(e){var t=e[0],a=e[1];Kt[t]=new on(t,_r,!1,a,null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(e){Kt[e]=new on(e,ga,!1,e.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(e){Kt[e]=new on(e,ga,!1,e,null,!1,!1)}),["allowFullScreen","async","autoFocus","autoPlay","controls","default","defer","disabled","disablePictureInPicture","disableRemotePlayback","formNoValidate","hidden","loop","noModule","noValidate","open","playsInline","readOnly","required","reversed","scoped","seamless","itemScope"].forEach(function(e){Kt[e]=new on(e,Kn,!1,e.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(e){Kt[e]=new on(e,Kn,!0,e,null,!1,!1)}),["capture","download"].forEach(function(e){Kt[e]=new on(e,xr,!1,e,null,!1,!1)}),["cols","rows","size","span"].forEach(function(e){Kt[e]=new on(e,ro,!1,e,null,!1,!1)}),["rowSpan","start"].forEach(function(e){Kt[e]=new on(e,oi,!1,e.toLowerCase(),null,!1,!1)});var qi=/[\-\:]([a-z])/g,Xi=function(e){return e[1].toUpperCase()};["accent-height","alignment-baseline","arabic-form","baseline-shift","cap-height","clip-path","clip-rule","color-interpolation","color-interpolation-filters","color-profile","color-rendering","dominant-baseline","enable-background","fill-opacity","fill-rule","flood-color","flood-opacity","font-family","font-size","font-size-adjust","font-stretch","font-style","font-variant","font-weight","glyph-name","glyph-orientation-horizontal","glyph-orientation-vertical","horiz-adv-x","horiz-origin-x","image-rendering","letter-spacing","lighting-color","marker-end","marker-mid","marker-start","overline-position","overline-thickness","paint-order","panose-1","pointer-events","rendering-intent","shape-rendering","stop-color","stop-opacity","strikethrough-position","strikethrough-thickness","stroke-dasharray","stroke-dashoffset","stroke-linecap","stroke-linejoin","stroke-miterlimit","stroke-opacity","stroke-width","text-anchor","text-decoration","text-rendering","underline-position","underline-thickness","unicode-bidi","unicode-range","units-per-em","v-alphabetic","v-hanging","v-ideographic","v-mathematical","vector-effect","vert-adv-y","vert-origin-x","vert-origin-y","word-spacing","writing-mode","xmlns:xlink","x-height"].forEach(function(e){var t=e.replace(qi,Xi);Kt[t]=new on(t,_r,!1,e,null,!1,!1)}),["xlink:actuate","xlink:arcrole","xlink:role","xlink:show","xlink:title","xlink:type"].forEach(function(e){var t=e.replace(qi,Xi);Kt[t]=new on(t,_r,!1,e,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(e){var t=e.replace(qi,Xi);Kt[t]=new on(t,_r,!1,e,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(e){Kt[e]=new on(e,_r,!1,e.toLowerCase(),null,!1,!1)});var io="xlinkHref";Kt[io]=new on("xlinkHref",_r,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(e){Kt[e]=new on(e,_r,!1,e.toLowerCase(),null,!0,!0)});var wl=/^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*\:/i,Sl=!1;function ao(e){!Sl&&wl.test(e)&&(Sl=!0,y("A future version of React will block javascript: URLs as a security precaution. Use event handlers instead if you can. If you need to generate unsafe HTML try using dangerouslySetInnerHTML instead. React was passed %s.",JSON.stringify(e)))}function Cl(e,t,a,l){if(l.mustUseProperty){var c=l.propertyName;return e[c]}else{Gn(a,t),l.sanitizeURL&&ao(""+a);var p=l.attributeName,v=null;if(l.type===xr){if(e.hasAttribute(p)){var w=e.getAttribute(p);return w===""?!0:dr(t,a,l,!1)?w:w===""+a?a:w}}else if(e.hasAttribute(p)){if(dr(t,a,l,!1))return e.getAttribute(p);if(l.type===Kn)return a;v=e.getAttribute(p)}return dr(t,a,l,!1)?v===null?a:v:v===""+a?a:v}}function ja(e,t,a,l){{if(!bn(t))return;if(!e.hasAttribute(t))return a===void 0?void 0:null;var c=e.getAttribute(t);return Gn(a,t),c===""+a?a:c}}function Oi(e,t,a,l){var c=vn(t);if(!wn(t,c,l)){if(dr(t,a,c,l)&&(a=null),l||c===null){if(bn(t)){var p=t;a===null?e.removeAttribute(p):(Gn(a,t),e.setAttribute(p,""+a))}return}var v=c.mustUseProperty;if(v){var w=c.propertyName;if(a===null){var C=c.type;e[w]=C===Kn?!1:""}else e[w]=a;return}var R=c.attributeName,M=c.attributeNamespace;if(a===null)e.removeAttribute(R);else{var U=c.type,F;U===Kn||U===xr&&a===!0?F="":(Gn(a,R),F=""+a,c.sanitizeURL&&ao(F.toString())),M?e.setAttributeNS(M,R,F):e.setAttribute(R,F)}}}var br=Symbol.for("react.element"),$i=Symbol.for("react.portal"),li=Symbol.for("react.fragment"),_a=Symbol.for("react.strict_mode"),La=Symbol.for("react.profiler"),oo=Symbol.for("react.provider"),z=Symbol.for("react.context"),de=Symbol.for("react.forward_ref"),Te=Symbol.for("react.suspense"),De=Symbol.for("react.suspense_list"),Rt=Symbol.for("react.memo"),ut=Symbol.for("react.lazy"),$t=Symbol.for("react.scope"),St=Symbol.for("react.debug_trace_mode"),Fn=Symbol.for("react.offscreen"),yn=Symbol.for("react.legacy_hidden"),Cn=Symbol.for("react.cache"),Lr=Symbol.for("react.tracing_marker"),ma=Symbol.iterator,Qt="@@iterator";function kn(e){if(e===null||typeof e!="object")return null;var t=ma&&e[ma]||e[Qt];return typeof t=="function"?t:null}var gt=Object.assign,za=0,lo,gd,so,_o,si,Nu,Vr;function Pu(){}Pu.__reactDisabledLog=!0;function md(){{if(za===0){lo=console.log,gd=console.info,so=console.warn,_o=console.error,si=console.group,Nu=console.groupCollapsed,Vr=console.groupEnd;var e={configurable:!0,enumerable:!0,value:Pu,writable:!0};Object.defineProperties(console,{info:e,log:e,warn:e,error:e,group:e,groupCollapsed:e,groupEnd:e})}za++}}function vd(){{if(za--,za===0){var e={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:gt({},e,{value:lo}),info:gt({},e,{value:gd}),warn:gt({},e,{value:so}),error:gt({},e,{value:_o}),group:gt({},e,{value:si}),groupCollapsed:gt({},e,{value:Nu}),groupEnd:gt({},e,{value:Vr})})}za<0&&y("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var uo=d.ReactCurrentDispatcher,Lo;function ui(e,t,a){{if(Lo===void 0)try{throw Error()}catch(c){var l=c.stack.trim().match(/\n( *(at )?)/);Lo=l&&l[1]||""}return`
`+Lo+e}}var Na=!1,zo;{var Cs=typeof WeakMap=="function"?WeakMap:Map;zo=new Cs}function co(e,t){if(!e||Na)return"";{var a=zo.get(e);if(a!==void 0)return a}var l;Na=!0;var c=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var p;p=uo.current,uo.current=null,md();try{if(t){var v=function(){throw Error()};if(Object.defineProperty(v.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(v,[])}catch(Z){l=Z}Reflect.construct(e,[],v)}else{try{v.call()}catch(Z){l=Z}e.call(v.prototype)}}else{try{throw Error()}catch(Z){l=Z}e()}}catch(Z){if(Z&&l&&typeof Z.stack=="string"){for(var w=Z.stack.split(`
`),C=l.stack.split(`
`),R=w.length-1,M=C.length-1;R>=1&&M>=0&&w[R]!==C[M];)M--;for(;R>=1&&M>=0;R--,M--)if(w[R]!==C[M]){if(R!==1||M!==1)do if(R--,M--,M<0||w[R]!==C[M]){var U=`
`+w[R].replace(" at new "," at ");return e.displayName&&U.includes("<anonymous>")&&(U=U.replace("<anonymous>",e.displayName)),typeof e=="function"&&zo.set(e,U),U}while(R>=1&&M>=0);break}}}finally{Na=!1,uo.current=p,vd(),Error.prepareStackTrace=c}var F=e?e.displayName||e.name:"",X=F?ui(F):"";return typeof e=="function"&&zo.set(e,X),X}function El(e,t,a){return co(e,!0)}function Tl(e,t,a){return co(e,!1)}function No(e){var t=e.prototype;return!!(t&&t.isReactComponent)}function Fu(e,t,a){if(e==null)return"";if(typeof e=="function")return co(e,No(e));if(typeof e=="string")return ui(e);switch(e){case Te:return ui("Suspense");case De:return ui("SuspenseList")}if(typeof e=="object")switch(e.$$typeof){case de:return Tl(e.render);case Rt:return Fu(e.type,t,a);case ut:{var l=e,c=l._payload,p=l._init;try{return Fu(p(c),t,a)}catch{}}}return""}function Iu(e){switch(e._debugOwner&&e._debugOwner.type,e._debugSource,e.tag){case L:return ui(e.type);case ft:return ui("Lazy");case le:return ui("Suspense");case bt:return ui("SuspenseList");case O:case P:case $e:return Tl(e.type);case ce:return Tl(e.type.render);case $:return El(e.type);default:return""}}function Nt(e){try{var t="",a=e;do t+=Iu(a),a=a.return;while(a);return t}catch(l){return`
Error generating stack: `+l.message+`
`+l.stack}}function Uu(e,t,a){var l=e.displayName;if(l)return l;var c=t.displayName||t.name||"";return c!==""?a+"("+c+")":a}function Es(e){return e.displayName||"Context"}function Pt(e){if(e==null)return null;if(typeof e.tag=="number"&&y("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof e=="function")return e.displayName||e.name||null;if(typeof e=="string")return e;switch(e){case li:return"Fragment";case $i:return"Portal";case La:return"Profiler";case _a:return"StrictMode";case Te:return"Suspense";case De:return"SuspenseList"}if(typeof e=="object")switch(e.$$typeof){case z:var t=e;return Es(t)+".Consumer";case oo:var a=e;return Es(a._context)+".Provider";case de:return Uu(e,e.render,"ForwardRef");case Rt:var l=e.displayName||null;return l!==null?l:Pt(e.type)||"Memo";case ut:{var c=e,p=c._payload,v=c._init;try{return Pt(v(p))}catch{return null}}}return null}function yd(e,t,a){var l=t.displayName||t.name||"";return e.displayName||(l!==""?a+"("+l+")":a)}function Pa(e){return e.displayName||"Context"}function lt(e){var t=e.tag,a=e.type;switch(t){case kt:return"Cache";case fe:var l=a;return Pa(l)+".Consumer";case ae:var c=a;return Pa(c._context)+".Provider";case Tt:return"DehydratedFragment";case ce:return yd(a,a.render,"ForwardRef");case xe:return"Fragment";case L:return a;case H:return"Portal";case j:return"Root";case Q:return"Text";case ft:return Pt(a);case ze:return a===_a?"StrictMode":"Mode";case Be:return"Offscreen";case Ee:return"Profiler";case rt:return"Scope";case le:return"Suspense";case bt:return"SuspenseList";case pt:return"TracingMarker";case $:case O:case He:case P:case ue:case $e:if(typeof a=="function")return a.displayName||a.name||null;if(typeof a=="string")return a;break}return null}var kl=d.ReactDebugCurrentFrame,fr=null,ci=!1;function Wr(){{if(fr===null)return null;var e=fr._debugOwner;if(e!==null&&typeof e<"u")return lt(e)}return null}function Fa(){return fr===null?"":Nt(fr)}function _n(){kl.getCurrentStack=null,fr=null,ci=!1}function ln(e){kl.getCurrentStack=e===null?null:Fa,fr=e,ci=!1}function va(){return fr}function Ji(e){ci=e}function zr(e){return""+e}function Yr(e){switch(typeof e){case"boolean":case"number":case"string":case"undefined":return e;case"object":return ur(e),e;default:return""}}var Cp={button:!0,checkbox:!0,image:!0,hidden:!0,radio:!0,reset:!0,submit:!0};function Ts(e,t){Cp[t.type]||t.onChange||t.onInput||t.readOnly||t.disabled||t.value==null||y("You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set either `onChange` or `readOnly`."),t.onChange||t.readOnly||t.disabled||t.checked==null||y("You provided a `checked` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultChecked`. Otherwise, set either `onChange` or `readOnly`.")}function Rl(e){var t=e.type,a=e.nodeName;return a&&a.toLowerCase()==="input"&&(t==="checkbox"||t==="radio")}function ks(e){return e._valueTracker}function Rs(e){e._valueTracker=null}function Dl(e){var t="";return e&&(Rl(e)?t=e.checked?"true":"false":t=e.value),t}function Zi(e){var t=Rl(e)?"checked":"value",a=Object.getOwnPropertyDescriptor(e.constructor.prototype,t);ur(e[t]);var l=""+e[t];if(!(e.hasOwnProperty(t)||typeof a>"u"||typeof a.get!="function"||typeof a.set!="function")){var c=a.get,p=a.set;Object.defineProperty(e,t,{configurable:!0,get:function(){return c.call(this)},set:function(w){ur(w),l=""+w,p.call(this,w)}}),Object.defineProperty(e,t,{enumerable:a.enumerable});var v={getValue:function(){return l},setValue:function(w){ur(w),l=""+w},stopTracking:function(){Rs(e),delete e[t]}};return v}}function ea(e){ks(e)||(e._valueTracker=Zi(e))}function Po(e){if(!e)return!1;var t=ks(e);if(!t)return!0;var a=t.getValue(),l=Dl(e);return l!==a?(t.setValue(l),!0):!1}function fo(e){if(e=e||(typeof document<"u"?document:void 0),typeof e>"u")return null;try{return e.activeElement||e.body}catch{return e.body}}var Ds=!1,Fo=!1,po=!1,Ms=!1;function Bu(e){var t=e.type==="checkbox"||e.type==="radio";return t?e.checked!=null:e.value!=null}function ta(e,t){var a=e,l=t.checked,c=gt({},t,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:l??a._wrapperState.initialChecked});return c}function Os(e,t){Ts("input",t),t.checked!==void 0&&t.defaultChecked!==void 0&&!Fo&&(y("%s contains an input of type %s with both checked and defaultChecked props. Input elements must be either controlled or uncontrolled (specify either the checked prop, or the defaultChecked prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://reactjs.org/link/controlled-components",Wr()||"A component",t.type),Fo=!0),t.value!==void 0&&t.defaultValue!==void 0&&!Ds&&(y("%s contains an input of type %s with both value and defaultValue props. Input elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://reactjs.org/link/controlled-components",Wr()||"A component",t.type),Ds=!0);var a=e,l=t.defaultValue==null?"":t.defaultValue;a._wrapperState={initialChecked:t.checked!=null?t.checked:t.defaultChecked,initialValue:Yr(t.value!=null?t.value:l),controlled:Bu(t)}}function T(e,t){var a=e,l=t.checked;l!=null&&Oi(a,"checked",l,!1)}function _(e,t){var a=e;{var l=Bu(t);!a._wrapperState.controlled&&l&&!Ms&&(y("A component is changing an uncontrolled input to be controlled. This is likely caused by the value changing from undefined to a defined value, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://reactjs.org/link/controlled-components"),Ms=!0),a._wrapperState.controlled&&!l&&!po&&(y("A component is changing a controlled input to be uncontrolled. This is likely caused by the value changing from a defined to undefined, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://reactjs.org/link/controlled-components"),po=!0)}T(e,t);var c=Yr(t.value),p=t.type;if(c!=null)p==="number"?(c===0&&a.value===""||a.value!=c)&&(a.value=zr(c)):a.value!==zr(c)&&(a.value=zr(c));else if(p==="submit"||p==="reset"){a.removeAttribute("value");return}t.hasOwnProperty("value")?Pe(a,t.type,c):t.hasOwnProperty("defaultValue")&&Pe(a,t.type,Yr(t.defaultValue)),t.checked==null&&t.defaultChecked!=null&&(a.defaultChecked=!!t.defaultChecked)}function q(e,t,a){var l=e;if(t.hasOwnProperty("value")||t.hasOwnProperty("defaultValue")){var c=t.type,p=c==="submit"||c==="reset";if(p&&(t.value===void 0||t.value===null))return;var v=zr(l._wrapperState.initialValue);a||v!==l.value&&(l.value=v),l.defaultValue=v}var w=l.name;w!==""&&(l.name=""),l.defaultChecked=!l.defaultChecked,l.defaultChecked=!!l._wrapperState.initialChecked,w!==""&&(l.name=w)}function ee(e,t){var a=e;_(a,t),ye(a,t)}function ye(e,t){var a=t.name;if(t.type==="radio"&&a!=null){for(var l=e;l.parentNode;)l=l.parentNode;Gn(a,"name");for(var c=l.querySelectorAll("input[name="+JSON.stringify(""+a)+'][type="radio"]'),p=0;p<c.length;p++){var v=c[p];if(!(v===e||v.form!==e.form)){var w=Uv(v);if(!w)throw new Error("ReactDOMInput: Mixing React and non-React radio inputs with the same `name` is not supported.");Po(v),_(v,w)}}}}function Pe(e,t,a){(t!=="number"||fo(e.ownerDocument)!==e)&&(a==null?e.defaultValue=zr(e._wrapperState.initialValue):e.defaultValue!==zr(a)&&(e.defaultValue=zr(a)))}var Ae=!1,at=!1,Ct=!1;function qt(e,t){t.value==null&&(typeof t.children=="object"&&t.children!==null?r.Children.forEach(t.children,function(a){a!=null&&(typeof a=="string"||typeof a=="number"||at||(at=!0,y("Cannot infer the option value of complex children. Pass a `value` prop or use a plain string as children to <option>.")))}):t.dangerouslySetInnerHTML!=null&&(Ct||(Ct=!0,y("Pass a `value` prop if you set dangerouslyInnerHTML so React knows which value should be selected.")))),t.selected!=null&&!Ae&&(y("Use the `defaultValue` or `value` props on <select> instead of setting `selected` on <option>."),Ae=!0)}function sn(e,t){t.value!=null&&e.setAttribute("value",zr(Yr(t.value)))}var un=Array.isArray;function yt(e){return un(e)}var hn;hn=!1;function In(){var e=Wr();return e?`

Check the render method of \``+e+"`.":""}var Ml=["value","defaultValue"];function Hu(e){{Ts("select",e);for(var t=0;t<Ml.length;t++){var a=Ml[t];if(e[a]!=null){var l=yt(e[a]);e.multiple&&!l?y("The `%s` prop supplied to <select> must be an array if `multiple` is true.%s",a,In()):!e.multiple&&l&&y("The `%s` prop supplied to <select> must be a scalar value if `multiple` is false.%s",a,In())}}}}function ho(e,t,a,l){var c=e.options;if(t){for(var p=a,v={},w=0;w<p.length;w++)v["$"+p[w]]=!0;for(var C=0;C<c.length;C++){var R=v.hasOwnProperty("$"+c[C].value);c[C].selected!==R&&(c[C].selected=R),R&&l&&(c[C].defaultSelected=!0)}}else{for(var M=zr(Yr(a)),U=null,F=0;F<c.length;F++){if(c[F].value===M){c[F].selected=!0,l&&(c[F].defaultSelected=!0);return}U===null&&!c[F].disabled&&(U=c[F])}U!==null&&(U.selected=!0)}}function Ol(e,t){return gt({},t,{value:void 0})}function Vu(e,t){var a=e;Hu(t),a._wrapperState={wasMultiple:!!t.multiple},t.value!==void 0&&t.defaultValue!==void 0&&!hn&&(y("Select elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled select element and remove one of these props. More info: https://reactjs.org/link/controlled-components"),hn=!0)}function Ep(e,t){var a=e;a.multiple=!!t.multiple;var l=t.value;l!=null?ho(a,!!t.multiple,l,!1):t.defaultValue!=null&&ho(a,!!t.multiple,t.defaultValue,!0)}function xd(e,t){var a=e,l=a._wrapperState.wasMultiple;a._wrapperState.wasMultiple=!!t.multiple;var c=t.value;c!=null?ho(a,!!t.multiple,c,!1):l!==!!t.multiple&&(t.defaultValue!=null?ho(a,!!t.multiple,t.defaultValue,!0):ho(a,!!t.multiple,t.multiple?[]:"",!1))}function Tp(e,t){var a=e,l=t.value;l!=null&&ho(a,!!t.multiple,l,!1)}var cm=!1;function bd(e,t){var a=e;if(t.dangerouslySetInnerHTML!=null)throw new Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");var l=gt({},t,{value:void 0,defaultValue:void 0,children:zr(a._wrapperState.initialValue)});return l}function dm(e,t){var a=e;Ts("textarea",t),t.value!==void 0&&t.defaultValue!==void 0&&!cm&&(y("%s contains a textarea with both value and defaultValue props. Textarea elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled textarea and remove one of these props. More info: https://reactjs.org/link/controlled-components",Wr()||"A component"),cm=!0);var l=t.value;if(l==null){var c=t.children,p=t.defaultValue;if(c!=null){y("Use the `defaultValue` or `value` props instead of setting children on <textarea>.");{if(p!=null)throw new Error("If you supply `defaultValue` on a <textarea>, do not pass children.");if(yt(c)){if(c.length>1)throw new Error("<textarea> can only have at most one child.");c=c[0]}p=c}}p==null&&(p=""),l=p}a._wrapperState={initialValue:Yr(l)}}function fm(e,t){var a=e,l=Yr(t.value),c=Yr(t.defaultValue);if(l!=null){var p=zr(l);p!==a.value&&(a.value=p),t.defaultValue==null&&a.defaultValue!==p&&(a.defaultValue=p)}c!=null&&(a.defaultValue=zr(c))}function pm(e,t){var a=e,l=a.textContent;l===a._wrapperState.initialValue&&l!==""&&l!==null&&(a.value=l)}function S0(e,t){fm(e,t)}var ya="http://www.w3.org/1999/xhtml",C0="http://www.w3.org/1998/Math/MathML",kp="http://www.w3.org/2000/svg";function Rp(e){switch(e){case"svg":return kp;case"math":return C0;default:return ya}}function wd(e,t){return e==null||e===ya?Rp(t):e===kp&&t==="foreignObject"?ya:e}var E0=function(e){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(t,a,l,c){MSApp.execUnsafeLocalFunction(function(){return e(t,a,l,c)})}:e},Sd,hm=E0(function(e,t){if(e.namespaceURI===kp&&!("innerHTML"in e)){Sd=Sd||document.createElement("div"),Sd.innerHTML="<svg>"+t.valueOf().toString()+"</svg>";for(var a=Sd.firstChild;e.firstChild;)e.removeChild(e.firstChild);for(;a.firstChild;)e.appendChild(a.firstChild);return}e.innerHTML=t}),di=1,go=3,Qn=8,mo=9,Wu=11,Io=function(e,t){if(t){var a=e.firstChild;if(a&&a===e.lastChild&&a.nodeType===go){a.nodeValue=t;return}}e.textContent=t},T0={animation:["animationDelay","animationDirection","animationDuration","animationFillMode","animationIterationCount","animationName","animationPlayState","animationTimingFunction"],background:["backgroundAttachment","backgroundClip","backgroundColor","backgroundImage","backgroundOrigin","backgroundPositionX","backgroundPositionY","backgroundRepeat","backgroundSize"],backgroundPosition:["backgroundPositionX","backgroundPositionY"],border:["borderBottomColor","borderBottomStyle","borderBottomWidth","borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth","borderLeftColor","borderLeftStyle","borderLeftWidth","borderRightColor","borderRightStyle","borderRightWidth","borderTopColor","borderTopStyle","borderTopWidth"],borderBlockEnd:["borderBlockEndColor","borderBlockEndStyle","borderBlockEndWidth"],borderBlockStart:["borderBlockStartColor","borderBlockStartStyle","borderBlockStartWidth"],borderBottom:["borderBottomColor","borderBottomStyle","borderBottomWidth"],borderColor:["borderBottomColor","borderLeftColor","borderRightColor","borderTopColor"],borderImage:["borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth"],borderInlineEnd:["borderInlineEndColor","borderInlineEndStyle","borderInlineEndWidth"],borderInlineStart:["borderInlineStartColor","borderInlineStartStyle","borderInlineStartWidth"],borderLeft:["borderLeftColor","borderLeftStyle","borderLeftWidth"],borderRadius:["borderBottomLeftRadius","borderBottomRightRadius","borderTopLeftRadius","borderTopRightRadius"],borderRight:["borderRightColor","borderRightStyle","borderRightWidth"],borderStyle:["borderBottomStyle","borderLeftStyle","borderRightStyle","borderTopStyle"],borderTop:["borderTopColor","borderTopStyle","borderTopWidth"],borderWidth:["borderBottomWidth","borderLeftWidth","borderRightWidth","borderTopWidth"],columnRule:["columnRuleColor","columnRuleStyle","columnRuleWidth"],columns:["columnCount","columnWidth"],flex:["flexBasis","flexGrow","flexShrink"],flexFlow:["flexDirection","flexWrap"],font:["fontFamily","fontFeatureSettings","fontKerning","fontLanguageOverride","fontSize","fontSizeAdjust","fontStretch","fontStyle","fontVariant","fontVariantAlternates","fontVariantCaps","fontVariantEastAsian","fontVariantLigatures","fontVariantNumeric","fontVariantPosition","fontWeight","lineHeight"],fontVariant:["fontVariantAlternates","fontVariantCaps","fontVariantEastAsian","fontVariantLigatures","fontVariantNumeric","fontVariantPosition"],gap:["columnGap","rowGap"],grid:["gridAutoColumns","gridAutoFlow","gridAutoRows","gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],gridArea:["gridColumnEnd","gridColumnStart","gridRowEnd","gridRowStart"],gridColumn:["gridColumnEnd","gridColumnStart"],gridColumnGap:["columnGap"],gridGap:["columnGap","rowGap"],gridRow:["gridRowEnd","gridRowStart"],gridRowGap:["rowGap"],gridTemplate:["gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],listStyle:["listStyleImage","listStylePosition","listStyleType"],margin:["marginBottom","marginLeft","marginRight","marginTop"],marker:["markerEnd","markerMid","markerStart"],mask:["maskClip","maskComposite","maskImage","maskMode","maskOrigin","maskPositionX","maskPositionY","maskRepeat","maskSize"],maskPosition:["maskPositionX","maskPositionY"],outline:["outlineColor","outlineStyle","outlineWidth"],overflow:["overflowX","overflowY"],padding:["paddingBottom","paddingLeft","paddingRight","paddingTop"],placeContent:["alignContent","justifyContent"],placeItems:["alignItems","justifyItems"],placeSelf:["alignSelf","justifySelf"],textDecoration:["textDecorationColor","textDecorationLine","textDecorationStyle"],textEmphasis:["textEmphasisColor","textEmphasisStyle"],transition:["transitionDelay","transitionDuration","transitionProperty","transitionTimingFunction"],wordWrap:["overflowWrap"]},$s={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0};function gm(e,t){return e+t.charAt(0).toUpperCase()+t.substring(1)}var mm=["Webkit","ms","Moz","O"];Object.keys($s).forEach(function(e){mm.forEach(function(t){$s[gm(t,e)]=$s[e]})});function Cd(e,t,a){var l=t==null||typeof t=="boolean"||t==="";return l?"":!a&&typeof t=="number"&&t!==0&&!($s.hasOwnProperty(e)&&$s[e])?t+"px":(Hr(t,e),(""+t).trim())}var vm=/([A-Z])/g,As=/^ms-/;function k0(e){return e.replace(vm,"-$1").toLowerCase().replace(As,"-ms-")}var ym=function(){};{var R0=/^(?:webkit|moz|o)[A-Z]/,xm=/^-ms-/,bm=/-(.)/g,js=/;\s*$/,Ia={},Dp={},Yu=!1,wm=!1,Sm=function(e){return e.replace(bm,function(t,a){return a.toUpperCase()})},Mp=function(e){Ia.hasOwnProperty(e)&&Ia[e]||(Ia[e]=!0,y("Unsupported style property %s. Did you mean %s?",e,Sm(e.replace(xm,"ms-"))))},Op=function(e){Ia.hasOwnProperty(e)&&Ia[e]||(Ia[e]=!0,y("Unsupported vendor-prefixed style property %s. Did you mean %s?",e,e.charAt(0).toUpperCase()+e.slice(1)))},Cm=function(e,t){Dp.hasOwnProperty(t)&&Dp[t]||(Dp[t]=!0,y(`Style property values shouldn't contain a semicolon. Try "%s: %s" instead.`,e,t.replace(js,"")))},Em=function(e,t){Yu||(Yu=!0,y("`NaN` is an invalid value for the `%s` css style property.",e))},Tm=function(e,t){wm||(wm=!0,y("`Infinity` is an invalid value for the `%s` css style property.",e))};ym=function(e,t){e.indexOf("-")>-1?Mp(e):R0.test(e)?Op(e):js.test(t)&&Cm(e,t),typeof t=="number"&&(isNaN(t)?Em(e,t):isFinite(t)||Tm(e,t))}}var D0=ym;function M0(e){{var t="",a="";for(var l in e)if(e.hasOwnProperty(l)){var c=e[l];if(c!=null){var p=l.indexOf("--")===0;t+=a+(p?l:k0(l))+":",t+=Cd(l,c,p),a=";"}}return t||null}}function km(e,t){var a=e.style;for(var l in t)if(t.hasOwnProperty(l)){var c=l.indexOf("--")===0;c||D0(l,t[l]);var p=Cd(l,t[l],c);l==="float"&&(l="cssFloat"),c?a.setProperty(l,p):a[l]=p}}function O0(e){return e==null||typeof e=="boolean"||e===""}function Rm(e){var t={};for(var a in e)for(var l=T0[a]||[a],c=0;c<l.length;c++)t[l[c]]=a;return t}function xa(e,t){{if(!t)return;var a=Rm(e),l=Rm(t),c={};for(var p in a){var v=a[p],w=l[p];if(w&&v!==w){var C=v+","+w;if(c[C])continue;c[C]=!0,y("%s a style property during rerender (%s) when a conflicting property is set (%s) can lead to styling bugs. To avoid this, don't mix shorthand and non-shorthand properties for the same value; instead, replace the shorthand with separate values.",O0(e[v])?"Removing":"Updating",v,w)}}}}var Gu={area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0},Dm=gt({menuitem:!0},Gu),Mm="__html";function Ed(e,t){if(t){if(Dm[e]&&(t.children!=null||t.dangerouslySetInnerHTML!=null))throw new Error(e+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");if(t.dangerouslySetInnerHTML!=null){if(t.children!=null)throw new Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");if(typeof t.dangerouslySetInnerHTML!="object"||!(Mm in t.dangerouslySetInnerHTML))throw new Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://reactjs.org/link/dangerously-set-inner-html for more information.")}if(!t.suppressContentEditableWarning&&t.contentEditable&&t.children!=null&&y("A component is `contentEditable` and contains `children` managed by React. It is now your responsibility to guarantee that none of those nodes are unexpectedly modified or duplicated. This is probably not intentional."),t.style!=null&&typeof t.style!="object")throw new Error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.")}}function Uo(e,t){if(e.indexOf("-")===-1)return typeof t.is=="string";switch(e){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var _s={accept:"accept",acceptcharset:"acceptCharset","accept-charset":"acceptCharset",accesskey:"accessKey",action:"action",allowfullscreen:"allowFullScreen",alt:"alt",as:"as",async:"async",autocapitalize:"autoCapitalize",autocomplete:"autoComplete",autocorrect:"autoCorrect",autofocus:"autoFocus",autoplay:"autoPlay",autosave:"autoSave",capture:"capture",cellpadding:"cellPadding",cellspacing:"cellSpacing",challenge:"challenge",charset:"charSet",checked:"checked",children:"children",cite:"cite",class:"className",classid:"classID",classname:"className",cols:"cols",colspan:"colSpan",content:"content",contenteditable:"contentEditable",contextmenu:"contextMenu",controls:"controls",controlslist:"controlsList",coords:"coords",crossorigin:"crossOrigin",dangerouslysetinnerhtml:"dangerouslySetInnerHTML",data:"data",datetime:"dateTime",default:"default",defaultchecked:"defaultChecked",defaultvalue:"defaultValue",defer:"defer",dir:"dir",disabled:"disabled",disablepictureinpicture:"disablePictureInPicture",disableremoteplayback:"disableRemotePlayback",download:"download",draggable:"draggable",enctype:"encType",enterkeyhint:"enterKeyHint",for:"htmlFor",form:"form",formmethod:"formMethod",formaction:"formAction",formenctype:"formEncType",formnovalidate:"formNoValidate",formtarget:"formTarget",frameborder:"frameBorder",headers:"headers",height:"height",hidden:"hidden",high:"high",href:"href",hreflang:"hrefLang",htmlfor:"htmlFor",httpequiv:"httpEquiv","http-equiv":"httpEquiv",icon:"icon",id:"id",imagesizes:"imageSizes",imagesrcset:"imageSrcSet",innerhtml:"innerHTML",inputmode:"inputMode",integrity:"integrity",is:"is",itemid:"itemID",itemprop:"itemProp",itemref:"itemRef",itemscope:"itemScope",itemtype:"itemType",keyparams:"keyParams",keytype:"keyType",kind:"kind",label:"label",lang:"lang",list:"list",loop:"loop",low:"low",manifest:"manifest",marginwidth:"marginWidth",marginheight:"marginHeight",max:"max",maxlength:"maxLength",media:"media",mediagroup:"mediaGroup",method:"method",min:"min",minlength:"minLength",multiple:"multiple",muted:"muted",name:"name",nomodule:"noModule",nonce:"nonce",novalidate:"noValidate",open:"open",optimum:"optimum",pattern:"pattern",placeholder:"placeholder",playsinline:"playsInline",poster:"poster",preload:"preload",profile:"profile",radiogroup:"radioGroup",readonly:"readOnly",referrerpolicy:"referrerPolicy",rel:"rel",required:"required",reversed:"reversed",role:"role",rows:"rows",rowspan:"rowSpan",sandbox:"sandbox",scope:"scope",scoped:"scoped",scrolling:"scrolling",seamless:"seamless",selected:"selected",shape:"shape",size:"size",sizes:"sizes",span:"span",spellcheck:"spellCheck",src:"src",srcdoc:"srcDoc",srclang:"srcLang",srcset:"srcSet",start:"start",step:"step",style:"style",summary:"summary",tabindex:"tabIndex",target:"target",title:"title",type:"type",usemap:"useMap",value:"value",width:"width",wmode:"wmode",wrap:"wrap",about:"about",accentheight:"accentHeight","accent-height":"accentHeight",accumulate:"accumulate",additive:"additive",alignmentbaseline:"alignmentBaseline","alignment-baseline":"alignmentBaseline",allowreorder:"allowReorder",alphabetic:"alphabetic",amplitude:"amplitude",arabicform:"arabicForm","arabic-form":"arabicForm",ascent:"ascent",attributename:"attributeName",attributetype:"attributeType",autoreverse:"autoReverse",azimuth:"azimuth",basefrequency:"baseFrequency",baselineshift:"baselineShift","baseline-shift":"baselineShift",baseprofile:"baseProfile",bbox:"bbox",begin:"begin",bias:"bias",by:"by",calcmode:"calcMode",capheight:"capHeight","cap-height":"capHeight",clip:"clip",clippath:"clipPath","clip-path":"clipPath",clippathunits:"clipPathUnits",cliprule:"clipRule","clip-rule":"clipRule",color:"color",colorinterpolation:"colorInterpolation","color-interpolation":"colorInterpolation",colorinterpolationfilters:"colorInterpolationFilters","color-interpolation-filters":"colorInterpolationFilters",colorprofile:"colorProfile","color-profile":"colorProfile",colorrendering:"colorRendering","color-rendering":"colorRendering",contentscripttype:"contentScriptType",contentstyletype:"contentStyleType",cursor:"cursor",cx:"cx",cy:"cy",d:"d",datatype:"datatype",decelerate:"decelerate",descent:"descent",diffuseconstant:"diffuseConstant",direction:"direction",display:"display",divisor:"divisor",dominantbaseline:"dominantBaseline","dominant-baseline":"dominantBaseline",dur:"dur",dx:"dx",dy:"dy",edgemode:"edgeMode",elevation:"elevation",enablebackground:"enableBackground","enable-background":"enableBackground",end:"end",exponent:"exponent",externalresourcesrequired:"externalResourcesRequired",fill:"fill",fillopacity:"fillOpacity","fill-opacity":"fillOpacity",fillrule:"fillRule","fill-rule":"fillRule",filter:"filter",filterres:"filterRes",filterunits:"filterUnits",floodopacity:"floodOpacity","flood-opacity":"floodOpacity",floodcolor:"floodColor","flood-color":"floodColor",focusable:"focusable",fontfamily:"fontFamily","font-family":"fontFamily",fontsize:"fontSize","font-size":"fontSize",fontsizeadjust:"fontSizeAdjust","font-size-adjust":"fontSizeAdjust",fontstretch:"fontStretch","font-stretch":"fontStretch",fontstyle:"fontStyle","font-style":"fontStyle",fontvariant:"fontVariant","font-variant":"fontVariant",fontweight:"fontWeight","font-weight":"fontWeight",format:"format",from:"from",fx:"fx",fy:"fy",g1:"g1",g2:"g2",glyphname:"glyphName","glyph-name":"glyphName",glyphorientationhorizontal:"glyphOrientationHorizontal","glyph-orientation-horizontal":"glyphOrientationHorizontal",glyphorientationvertical:"glyphOrientationVertical","glyph-orientation-vertical":"glyphOrientationVertical",glyphref:"glyphRef",gradienttransform:"gradientTransform",gradientunits:"gradientUnits",hanging:"hanging",horizadvx:"horizAdvX","horiz-adv-x":"horizAdvX",horizoriginx:"horizOriginX","horiz-origin-x":"horizOriginX",ideographic:"ideographic",imagerendering:"imageRendering","image-rendering":"imageRendering",in2:"in2",in:"in",inlist:"inlist",intercept:"intercept",k1:"k1",k2:"k2",k3:"k3",k4:"k4",k:"k",kernelmatrix:"kernelMatrix",kernelunitlength:"kernelUnitLength",kerning:"kerning",keypoints:"keyPoints",keysplines:"keySplines",keytimes:"keyTimes",lengthadjust:"lengthAdjust",letterspacing:"letterSpacing","letter-spacing":"letterSpacing",lightingcolor:"lightingColor","lighting-color":"lightingColor",limitingconeangle:"limitingConeAngle",local:"local",markerend:"markerEnd","marker-end":"markerEnd",markerheight:"markerHeight",markermid:"markerMid","marker-mid":"markerMid",markerstart:"markerStart","marker-start":"markerStart",markerunits:"markerUnits",markerwidth:"markerWidth",mask:"mask",maskcontentunits:"maskContentUnits",maskunits:"maskUnits",mathematical:"mathematical",mode:"mode",numoctaves:"numOctaves",offset:"offset",opacity:"opacity",operator:"operator",order:"order",orient:"orient",orientation:"orientation",origin:"origin",overflow:"overflow",overlineposition:"overlinePosition","overline-position":"overlinePosition",overlinethickness:"overlineThickness","overline-thickness":"overlineThickness",paintorder:"paintOrder","paint-order":"paintOrder",panose1:"panose1","panose-1":"panose1",pathlength:"pathLength",patterncontentunits:"patternContentUnits",patterntransform:"patternTransform",patternunits:"patternUnits",pointerevents:"pointerEvents","pointer-events":"pointerEvents",points:"points",pointsatx:"pointsAtX",pointsaty:"pointsAtY",pointsatz:"pointsAtZ",prefix:"prefix",preservealpha:"preserveAlpha",preserveaspectratio:"preserveAspectRatio",primitiveunits:"primitiveUnits",property:"property",r:"r",radius:"radius",refx:"refX",refy:"refY",renderingintent:"renderingIntent","rendering-intent":"renderingIntent",repeatcount:"repeatCount",repeatdur:"repeatDur",requiredextensions:"requiredExtensions",requiredfeatures:"requiredFeatures",resource:"resource",restart:"restart",result:"result",results:"results",rotate:"rotate",rx:"rx",ry:"ry",scale:"scale",security:"security",seed:"seed",shaperendering:"shapeRendering","shape-rendering":"shapeRendering",slope:"slope",spacing:"spacing",specularconstant:"specularConstant",specularexponent:"specularExponent",speed:"speed",spreadmethod:"spreadMethod",startoffset:"startOffset",stddeviation:"stdDeviation",stemh:"stemh",stemv:"stemv",stitchtiles:"stitchTiles",stopcolor:"stopColor","stop-color":"stopColor",stopopacity:"stopOpacity","stop-opacity":"stopOpacity",strikethroughposition:"strikethroughPosition","strikethrough-position":"strikethroughPosition",strikethroughthickness:"strikethroughThickness","strikethrough-thickness":"strikethroughThickness",string:"string",stroke:"stroke",strokedasharray:"strokeDasharray","stroke-dasharray":"strokeDasharray",strokedashoffset:"strokeDashoffset","stroke-dashoffset":"strokeDashoffset",strokelinecap:"strokeLinecap","stroke-linecap":"strokeLinecap",strokelinejoin:"strokeLinejoin","stroke-linejoin":"strokeLinejoin",strokemiterlimit:"strokeMiterlimit","stroke-miterlimit":"strokeMiterlimit",strokewidth:"strokeWidth","stroke-width":"strokeWidth",strokeopacity:"strokeOpacity","stroke-opacity":"strokeOpacity",suppresscontenteditablewarning:"suppressContentEditableWarning",suppresshydrationwarning:"suppressHydrationWarning",surfacescale:"surfaceScale",systemlanguage:"systemLanguage",tablevalues:"tableValues",targetx:"targetX",targety:"targetY",textanchor:"textAnchor","text-anchor":"textAnchor",textdecoration:"textDecoration","text-decoration":"textDecoration",textlength:"textLength",textrendering:"textRendering","text-rendering":"textRendering",to:"to",transform:"transform",typeof:"typeof",u1:"u1",u2:"u2",underlineposition:"underlinePosition","underline-position":"underlinePosition",underlinethickness:"underlineThickness","underline-thickness":"underlineThickness",unicode:"unicode",unicodebidi:"unicodeBidi","unicode-bidi":"unicodeBidi",unicoderange:"unicodeRange","unicode-range":"unicodeRange",unitsperem:"unitsPerEm","units-per-em":"unitsPerEm",unselectable:"unselectable",valphabetic:"vAlphabetic","v-alphabetic":"vAlphabetic",values:"values",vectoreffect:"vectorEffect","vector-effect":"vectorEffect",version:"version",vertadvy:"vertAdvY","vert-adv-y":"vertAdvY",vertoriginx:"vertOriginX","vert-origin-x":"vertOriginX",vertoriginy:"vertOriginY","vert-origin-y":"vertOriginY",vhanging:"vHanging","v-hanging":"vHanging",videographic:"vIdeographic","v-ideographic":"vIdeographic",viewbox:"viewBox",viewtarget:"viewTarget",visibility:"visibility",vmathematical:"vMathematical","v-mathematical":"vMathematical",vocab:"vocab",widths:"widths",wordspacing:"wordSpacing","word-spacing":"wordSpacing",writingmode:"writingMode","writing-mode":"writingMode",x1:"x1",x2:"x2",x:"x",xchannelselector:"xChannelSelector",xheight:"xHeight","x-height":"xHeight",xlinkactuate:"xlinkActuate","xlink:actuate":"xlinkActuate",xlinkarcrole:"xlinkArcrole","xlink:arcrole":"xlinkArcrole",xlinkhref:"xlinkHref","xlink:href":"xlinkHref",xlinkrole:"xlinkRole","xlink:role":"xlinkRole",xlinkshow:"xlinkShow","xlink:show":"xlinkShow",xlinktitle:"xlinkTitle","xlink:title":"xlinkTitle",xlinktype:"xlinkType","xlink:type":"xlinkType",xmlbase:"xmlBase","xml:base":"xmlBase",xmllang:"xmlLang","xml:lang":"xmlLang",xmlns:"xmlns","xml:space":"xmlSpace",xmlnsxlink:"xmlnsXlink","xmlns:xlink":"xmlnsXlink",xmlspace:"xmlSpace",y1:"y1",y2:"y2",y:"y",ychannelselector:"yChannelSelector",z:"z",zoomandpan:"zoomAndPan"},Om={"aria-current":0,"aria-description":0,"aria-details":0,"aria-disabled":0,"aria-hidden":0,"aria-invalid":0,"aria-keyshortcuts":0,"aria-label":0,"aria-roledescription":0,"aria-autocomplete":0,"aria-checked":0,"aria-expanded":0,"aria-haspopup":0,"aria-level":0,"aria-modal":0,"aria-multiline":0,"aria-multiselectable":0,"aria-orientation":0,"aria-placeholder":0,"aria-pressed":0,"aria-readonly":0,"aria-required":0,"aria-selected":0,"aria-sort":0,"aria-valuemax":0,"aria-valuemin":0,"aria-valuenow":0,"aria-valuetext":0,"aria-atomic":0,"aria-busy":0,"aria-live":0,"aria-relevant":0,"aria-dropeffect":0,"aria-grabbed":0,"aria-activedescendant":0,"aria-colcount":0,"aria-colindex":0,"aria-colspan":0,"aria-controls":0,"aria-describedby":0,"aria-errormessage":0,"aria-flowto":0,"aria-labelledby":0,"aria-owns":0,"aria-posinset":0,"aria-rowcount":0,"aria-rowindex":0,"aria-rowspan":0,"aria-setsize":0},Ls={},zs=new RegExp("^(aria)-["+we+"]*$"),$p=new RegExp("^(aria)[A-Z]["+we+"]*$");function Ku(e,t){{if(Pn.call(Ls,t)&&Ls[t])return!0;if($p.test(t)){var a="aria-"+t.slice(4).toLowerCase(),l=Om.hasOwnProperty(a)?a:null;if(l==null)return y("Invalid ARIA attribute `%s`. ARIA attributes follow the pattern aria-* and must be lowercase.",t),Ls[t]=!0,!0;if(t!==l)return y("Invalid ARIA attribute `%s`. Did you mean `%s`?",t,l),Ls[t]=!0,!0}if(zs.test(t)){var c=t.toLowerCase(),p=Om.hasOwnProperty(c)?c:null;if(p==null)return Ls[t]=!0,!1;if(t!==p)return y("Unknown ARIA attribute `%s`. Did you mean `%s`?",t,p),Ls[t]=!0,!0}}return!0}function Ap(e,t){{var a=[];for(var l in t){var c=Ku(e,l);c||a.push(l)}var p=a.map(function(v){return"`"+v+"`"}).join(", ");a.length===1?y("Invalid aria prop %s on <%s> tag. For details, see https://reactjs.org/link/invalid-aria-props",p,e):a.length>1&&y("Invalid aria props %s on <%s> tag. For details, see https://reactjs.org/link/invalid-aria-props",p,e)}}function $m(e,t){Uo(e,t)||Ap(e,t)}var Qu=!1;function Ns(e,t){{if(e!=="input"&&e!=="textarea"&&e!=="select")return;t!=null&&t.value===null&&!Qu&&(Qu=!0,e==="select"&&t.multiple?y("`value` prop on `%s` should not be null. Consider using an empty array when `multiple` is set to `true` to clear the component or `undefined` for uncontrolled components.",e):y("`value` prop on `%s` should not be null. Consider using an empty string to clear the component or `undefined` for uncontrolled components.",e))}}var Td=function(){};{var Nr={},qu=/^on./,Am=/^on[^A-Z]/,jm=new RegExp("^(aria)-["+we+"]*$"),_m=new RegExp("^(aria)[A-Z]["+we+"]*$");Td=function(e,t,a,l){if(Pn.call(Nr,t)&&Nr[t])return!0;var c=t.toLowerCase();if(c==="onfocusin"||c==="onfocusout")return y("React uses onFocus and onBlur instead of onFocusIn and onFocusOut. All React events are normalized to bubble, so onFocusIn and onFocusOut are not needed/supported by React."),Nr[t]=!0,!0;if(l!=null){var p=l.registrationNameDependencies,v=l.possibleRegistrationNames;if(p.hasOwnProperty(t))return!0;var w=v.hasOwnProperty(c)?v[c]:null;if(w!=null)return y("Invalid event handler property `%s`. Did you mean `%s`?",t,w),Nr[t]=!0,!0;if(qu.test(t))return y("Unknown event handler property `%s`. It will be ignored.",t),Nr[t]=!0,!0}else if(qu.test(t))return Am.test(t)&&y("Invalid event handler property `%s`. React events use the camelCase naming convention, for example `onClick`.",t),Nr[t]=!0,!0;if(jm.test(t)||_m.test(t))return!0;if(c==="innerhtml")return y("Directly setting property `innerHTML` is not permitted. For more information, lookup documentation on `dangerouslySetInnerHTML`."),Nr[t]=!0,!0;if(c==="aria")return y("The `aria` attribute is reserved for future use in React. Pass individual `aria-` attributes instead."),Nr[t]=!0,!0;if(c==="is"&&a!==null&&a!==void 0&&typeof a!="string")return y("Received a `%s` for a string attribute `is`. If this is expected, cast the value to a string.",typeof a),Nr[t]=!0,!0;if(typeof a=="number"&&isNaN(a))return y("Received NaN for the `%s` attribute. If this is expected, cast the value to a string.",t),Nr[t]=!0,!0;var C=vn(t),R=C!==null&&C.type===cr;if(_s.hasOwnProperty(c)){var M=_s[c];if(M!==t)return y("Invalid DOM property `%s`. Did you mean `%s`?",t,M),Nr[t]=!0,!0}else if(!R&&t!==c)return y("React does not recognize the `%s` prop on a DOM element. If you intentionally want it to appear in the DOM as a custom attribute, spell it as lowercase `%s` instead. If you accidentally passed it from a parent component, remove it from the DOM element.",t,c),Nr[t]=!0,!0;return typeof a=="boolean"&&Sn(t,a,C,!1)?(a?y('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.',a,t,t,a,t):y('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.',a,t,t,a,t,t,t),Nr[t]=!0,!0):R?!0:Sn(t,a,C,!1)?(Nr[t]=!0,!1):((a==="false"||a==="true")&&C!==null&&C.type===Kn&&(y("Received the string `%s` for the boolean attribute `%s`. %s Did you mean %s={%s}?",a,t,a==="false"?"The browser will interpret it as a truthy value.":'Although this works, it will not work as expected if you pass the string "false".',t,a),Nr[t]=!0),!0)}}var Lm=function(e,t,a){{var l=[];for(var c in t){var p=Td(e,c,t[c],a);p||l.push(c)}var v=l.map(function(w){return"`"+w+"`"}).join(", ");l.length===1?y("Invalid value for prop %s on <%s> tag. Either remove it from the element, or pass a string or number value to keep it in the DOM. For details, see https://reactjs.org/link/attribute-behavior ",v,e):l.length>1&&y("Invalid values for props %s on <%s> tag. Either remove them from the element, or pass a string or number value to keep them in the DOM. For details, see https://reactjs.org/link/attribute-behavior ",v,e)}};function zm(e,t,a){Uo(e,t)||Lm(e,t,a)}var jp=1,Ua=2,$l=4,_p=jp|Ua|$l,Xu=null;function $0(e){Xu!==null&&y("Expected currently replaying event to be null. This error is likely caused by a bug in React. Please file an issue."),Xu=e}function Ju(){Xu===null&&y("Expected currently replaying event to not be null. This error is likely caused by a bug in React. Please file an issue."),Xu=null}function A0(e){return e===Xu}function kd(e){var t=e.target||e.srcElement||window;return t.correspondingUseElement&&(t=t.correspondingUseElement),t.nodeType===go?t.parentNode:t}var Rd=null,Xt=null,Bo=null;function Zu(e){var t=cu(e);if(t){if(typeof Rd!="function")throw new Error("setRestoreImplementation() needs to be called to handle a target for controlled events. This error is likely caused by a bug in React. Please file an issue.");var a=t.stateNode;if(a){var l=Uv(a);Rd(t.stateNode,t.type,l)}}}function ec(e){Rd=e}function Lp(e){Xt?Bo?Bo.push(e):Bo=[e]:Xt=e}function zp(){return Xt!==null||Bo!==null}function Ps(){if(Xt){var e=Xt,t=Bo;if(Xt=null,Bo=null,Zu(e),t)for(var a=0;a<t.length;a++)Zu(t[a])}}var tc=function(e,t){return e(t)},Al=function(){},Dd=!1;function j0(){var e=zp();e&&(Al(),Ps())}function Nm(e,t,a){if(Dd)return e(t,a);Dd=!0;try{return tc(e,t,a)}finally{Dd=!1,j0()}}function Pm(e,t,a){tc=e,Al=a}function Md(e){return e==="button"||e==="input"||e==="select"||e==="textarea"}function Od(e,t,a){switch(e){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":return!!(a.disabled&&Md(t));default:return!1}}function jl(e,t){var a=e.stateNode;if(a===null)return null;var l=Uv(a);if(l===null)return null;var c=l[t];if(Od(t,e.type,l))return null;if(c&&typeof c!="function")throw new Error("Expected `"+t+"` listener to be a function, instead got a value of `"+typeof c+"` type.");return c}var nc=!1;if(an)try{var _l={};Object.defineProperty(_l,"passive",{get:function(){nc=!0}}),window.addEventListener("test",_l,_l),window.removeEventListener("test",_l,_l)}catch{nc=!1}function $d(e,t,a,l,c,p,v,w,C){var R=Array.prototype.slice.call(arguments,3);try{t.apply(a,R)}catch(M){this.onError(M)}}var Fm=$d;if(typeof window<"u"&&typeof window.dispatchEvent=="function"&&typeof document<"u"&&typeof document.createEvent=="function"){var Ad=document.createElement("react");Fm=function(t,a,l,c,p,v,w,C,R){if(typeof document>"u"||document===null)throw new Error("The `document` global was defined when React was initialized, but is not defined anymore. This can happen in a test environment if a component schedules an update from an asynchronous callback, but the test has already finished running. To solve this, you can either unmount the component at the end of your test (and ensure that any asynchronous operations get canceled in `componentWillUnmount`), or you can change the test itself to be asynchronous.");var M=document.createEvent("Event"),U=!1,F=!0,X=window.event,Z=Object.getOwnPropertyDescriptor(window,"event");function te(){Ad.removeEventListener(ne,Xe,!1),typeof window.event<"u"&&window.hasOwnProperty("event")&&(window.event=X)}var Re=Array.prototype.slice.call(arguments,3);function Xe(){U=!0,te(),a.apply(l,Re),F=!1}var We,It=!1,_t=!1;function Y(G){if(We=G.error,It=!0,We===null&&G.colno===0&&G.lineno===0&&(_t=!0),G.defaultPrevented&&We!=null&&typeof We=="object")try{We._suppressLogging=!0}catch{}}var ne="react-"+(t||"invokeguardedcallback");if(window.addEventListener("error",Y),Ad.addEventListener(ne,Xe,!1),M.initEvent(ne,!1,!1),Ad.dispatchEvent(M),Z&&Object.defineProperty(window,"event",Z),U&&F&&(It?_t&&(We=new Error("A cross-origin error was thrown. React doesn't have access to the actual error object in development. See https://reactjs.org/link/crossorigin-error for more information.")):We=new Error(`An error was thrown inside one of your components, but React doesn't know what it was. This is likely due to browser flakiness. React does its best to preserve the "Pause on exceptions" behavior of the DevTools, which requires some DEV-mode only tricks. It's possible that these don't work in your browser. Try triggering the error in production mode, or switching to a modern browser. If you suspect that this is actually an issue with React, please file an issue.`),this.onError(We)),window.removeEventListener("error",Y),!U)return te(),$d.apply(this,arguments)}}var _0=Fm,Fs=!1,Is=null,ba=!1,jd=null,Us={onError:function(e){Fs=!0,Is=e}};function na(e,t,a,l,c,p,v,w,C){Fs=!1,Is=null,_0.apply(Us,arguments)}function rc(e,t,a,l,c,p,v,w,C){if(na.apply(this,arguments),Fs){var R=Pp();ba||(ba=!0,jd=R)}}function vo(){if(ba){var e=jd;throw ba=!1,jd=null,e}}function Np(){return Fs}function Pp(){if(Fs){var e=Is;return Fs=!1,Is=null,e}else throw new Error("clearCaughtError was called but no error was captured. This error is likely caused by a bug in React. Please file an issue.")}function Bs(e){return e._reactInternals}function Ll(e){return e._reactInternals!==void 0}function ic(e,t){e._reactInternals=t}var Ye=0,yo=1,Ln=2,At=4,fi=16,tn=32,gn=64,Et=128,Rn=256,qn=512,ra=1024,Ai=2048,zn=4096,Ba=8192,_d=16384,Im=32767,zl=32768,Pr=65536,wa=131072,ac=1048576,oc=2097152,Ho=4194304,Fp=8388608,Gr=16777216,Vo=33554432,Wo=At|ra|0,Hs=Ln|At|fi|tn|qn|zn|Ba,Yo=At|gn|qn|Ba,wr=Ai|fi,Xn=Ho|Fp|oc,Nl=d.ReactCurrentOwner;function Kr(e){var t=e,a=e;if(e.alternate)for(;t.return;)t=t.return;else{var l=t;do t=l,(t.flags&(Ln|zn))!==Ye&&(a=t.return),l=t.return;while(l)}return t.tag===j?a:null}function Ha(e){if(e.tag===le){var t=e.memoizedState;if(t===null){var a=e.alternate;a!==null&&(t=a.memoizedState)}if(t!==null)return t.dehydrated}return null}function Go(e){return e.tag===j?e.stateNode.containerInfo:null}function Um(e){return Kr(e)===e}function Ip(e){{var t=Nl.current;if(t!==null&&t.tag===$){var a=t,l=a.stateNode;l._warnedAboutRefsInRender||y("%s is accessing isMounted inside its render() function. render() should be a pure function of props and state. It should never access something that requires stale data from the previous render, such as refs. Move this logic to componentDidMount and componentDidUpdate instead.",lt(a)||"A component"),l._warnedAboutRefsInRender=!0}}var c=Bs(e);return c?Kr(c)===c:!1}function Ld(e){if(Kr(e)!==e)throw new Error("Unable to find node on an unmounted component.")}function pi(e){var t=e.alternate;if(!t){var a=Kr(e);if(a===null)throw new Error("Unable to find node on an unmounted component.");return a!==e?null:e}for(var l=e,c=t;;){var p=l.return;if(p===null)break;var v=p.alternate;if(v===null){var w=p.return;if(w!==null){l=c=w;continue}break}if(p.child===v.child){for(var C=p.child;C;){if(C===l)return Ld(p),e;if(C===c)return Ld(p),t;C=C.sibling}throw new Error("Unable to find node on an unmounted component.")}if(l.return!==c.return)l=p,c=v;else{for(var R=!1,M=p.child;M;){if(M===l){R=!0,l=p,c=v;break}if(M===c){R=!0,c=p,l=v;break}M=M.sibling}if(!R){for(M=v.child;M;){if(M===l){R=!0,l=v,c=p;break}if(M===c){R=!0,c=v,l=p;break}M=M.sibling}if(!R)throw new Error("Child was not found in either parent set. This indicates a bug in React related to the return pointer. Please file an issue.")}}if(l.alternate!==c)throw new Error("Return fibers should always be each others' alternates. This error is likely caused by a bug in React. Please file an issue.")}if(l.tag!==j)throw new Error("Unable to find node on an unmounted component.");return l.stateNode.current===l?e:t}function hi(e){var t=pi(e);return t!==null?En(t):null}function En(e){if(e.tag===L||e.tag===Q)return e;for(var t=e.child;t!==null;){var a=En(t);if(a!==null)return a;t=t.sibling}return null}function Sa(e){var t=pi(e);return t!==null?Up(t):null}function Up(e){if(e.tag===L||e.tag===Q)return e;for(var t=e.child;t!==null;){if(t.tag!==H){var a=Up(t);if(a!==null)return a}t=t.sibling}return null}var Bp=s.unstable_scheduleCallback,Hp=s.unstable_cancelCallback,Vp=s.unstable_shouldYield,Bm=s.unstable_requestPaint,Un=s.unstable_now,Hm=s.unstable_getCurrentPriorityLevel,xo=s.unstable_ImmediatePriority,lc=s.unstable_UserBlockingPriority,Pl=s.unstable_NormalPriority,sc=s.unstable_LowPriority,Vs=s.unstable_IdlePriority,Vm=s.unstable_yieldValue,Wm=s.unstable_setDisableYieldValue,Ca=null,Sr=null,Ce=null,ji=!1,Fr=typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u";function Wp(e){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u")return!1;var t=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(t.isDisabled)return!0;if(!t.supportsFiber)return y("The installed version of React DevTools is too old and will not work with the current version of React. Please update React DevTools. https://reactjs.org/link/react-devtools"),!0;try{et&&(e=gt({},e,{getLaneLabelMap:Qp,injectProfilingHooks:Fl})),Ca=t.inject(e),Sr=t}catch(a){y("React instrumentation encountered an error: %s.",a)}return!!t.checkDCE}function Yp(e,t){if(Sr&&typeof Sr.onScheduleFiberRoot=="function")try{Sr.onScheduleFiberRoot(Ca,e,t)}catch(a){ji||(ji=!0,y("React instrumentation encountered an error: %s",a))}}function Gp(e,t){if(Sr&&typeof Sr.onCommitFiberRoot=="function")try{var a=(e.current.flags&Et)===Et;if(it){var l;switch(t){case xi:l=xo;break;case aa:l=lc;break;case Cr:l=Pl;break;case ff:l=Vs;break;default:l=Pl;break}Sr.onCommitFiberRoot(Ca,e,l,a)}}catch(c){ji||(ji=!0,y("React instrumentation encountered an error: %s",c))}}function Kp(e){if(Sr&&typeof Sr.onPostCommitFiberRoot=="function")try{Sr.onPostCommitFiberRoot(Ca,e)}catch(t){ji||(ji=!0,y("React instrumentation encountered an error: %s",t))}}function Ws(e){if(Sr&&typeof Sr.onCommitFiberUnmount=="function")try{Sr.onCommitFiberUnmount(Ca,e)}catch(t){ji||(ji=!0,y("React instrumentation encountered an error: %s",t))}}function nn(e){if(typeof Vm=="function"&&(Wm(e),b(e)),Sr&&typeof Sr.setStrictMode=="function")try{Sr.setStrictMode(Ca,e)}catch(t){ji||(ji=!0,y("React instrumentation encountered an error: %s",t))}}function Fl(e){Ce=e}function Qp(){{for(var e=new Map,t=1,a=0;a<th;a++){var l=Xm(t);e.set(t,l),t*=2}return e}}function Ym(e){Ce!==null&&typeof Ce.markCommitStarted=="function"&&Ce.markCommitStarted(e)}function Va(){Ce!==null&&typeof Ce.markCommitStopped=="function"&&Ce.markCommitStopped()}function ia(e){Ce!==null&&typeof Ce.markComponentRenderStarted=="function"&&Ce.markComponentRenderStarted(e)}function Ko(){Ce!==null&&typeof Ce.markComponentRenderStopped=="function"&&Ce.markComponentRenderStopped()}function Gm(e){Ce!==null&&typeof Ce.markComponentPassiveEffectMountStarted=="function"&&Ce.markComponentPassiveEffectMountStarted(e)}function bo(){Ce!==null&&typeof Ce.markComponentPassiveEffectMountStopped=="function"&&Ce.markComponentPassiveEffectMountStopped()}function Qo(e){Ce!==null&&typeof Ce.markComponentPassiveEffectUnmountStarted=="function"&&Ce.markComponentPassiveEffectUnmountStarted(e)}function zd(){Ce!==null&&typeof Ce.markComponentPassiveEffectUnmountStopped=="function"&&Ce.markComponentPassiveEffectUnmountStopped()}function Km(e){Ce!==null&&typeof Ce.markComponentLayoutEffectMountStarted=="function"&&Ce.markComponentLayoutEffectMountStarted(e)}function Nd(){Ce!==null&&typeof Ce.markComponentLayoutEffectMountStopped=="function"&&Ce.markComponentLayoutEffectMountStopped()}function qp(e){Ce!==null&&typeof Ce.markComponentLayoutEffectUnmountStarted=="function"&&Ce.markComponentLayoutEffectUnmountStarted(e)}function Ys(){Ce!==null&&typeof Ce.markComponentLayoutEffectUnmountStopped=="function"&&Ce.markComponentLayoutEffectUnmountStopped()}function Wa(e,t,a){Ce!==null&&typeof Ce.markComponentErrored=="function"&&Ce.markComponentErrored(e,t,a)}function uc(e,t,a){Ce!==null&&typeof Ce.markComponentSuspended=="function"&&Ce.markComponentSuspended(e,t,a)}function cc(e){Ce!==null&&typeof Ce.markLayoutEffectsStarted=="function"&&Ce.markLayoutEffectsStarted(e)}function Il(){Ce!==null&&typeof Ce.markLayoutEffectsStopped=="function"&&Ce.markLayoutEffectsStopped()}function Xp(e){Ce!==null&&typeof Ce.markPassiveEffectsStarted=="function"&&Ce.markPassiveEffectsStarted(e)}function Gs(){Ce!==null&&typeof Ce.markPassiveEffectsStopped=="function"&&Ce.markPassiveEffectsStopped()}function Jp(e){Ce!==null&&typeof Ce.markRenderStarted=="function"&&Ce.markRenderStarted(e)}function Zp(){Ce!==null&&typeof Ce.markRenderYielded=="function"&&Ce.markRenderYielded()}function Dn(){Ce!==null&&typeof Ce.markRenderStopped=="function"&&Ce.markRenderStopped()}function Pd(e){Ce!==null&&typeof Ce.markRenderScheduled=="function"&&Ce.markRenderScheduled(e)}function eh(e,t){Ce!==null&&typeof Ce.markForceUpdateScheduled=="function"&&Ce.markForceUpdateScheduled(e,t)}function dc(e,t){Ce!==null&&typeof Ce.markStateUpdateScheduled=="function"&&Ce.markStateUpdateScheduled(e,t)}var Ge=0,Dt=1,zt=2,mt=8,cn=16,rr=Math.clz32?Math.clz32:pc,Fd=Math.log,fc=Math.LN2;function pc(e){var t=e>>>0;return t===0?32:31-(Fd(t)/fc|0)|0}var th=31,ie=0,Jn=0,nt=1,qo=2,pr=4,hr=8,gi=16,Ul=32,Xo=4194240,Ks=64,Id=128,Ud=256,Bd=512,Hd=1024,Vd=2048,Wd=4096,Yd=8192,Bl=16384,Gd=32768,Qs=65536,qs=131072,Kd=262144,hc=524288,Qd=1048576,qd=2097152,gc=130023424,Hl=4194304,mc=8388608,Xd=16777216,Jd=33554432,Zd=67108864,Qm=Hl,Xs=134217728,qm=268435455,vc=268435456,Jo=536870912,mi=1073741824;function Xm(e){{if(e&nt)return"Sync";if(e&qo)return"InputContinuousHydration";if(e&pr)return"InputContinuous";if(e&hr)return"DefaultHydration";if(e&gi)return"Default";if(e&Ul)return"TransitionHydration";if(e&Xo)return"Transition";if(e&gc)return"Retry";if(e&Xs)return"SelectiveHydration";if(e&vc)return"IdleHydration";if(e&Jo)return"Idle";if(e&mi)return"Offscreen"}}var rn=-1,ef=Ks,tf=Hl;function yc(e){switch(Vl(e)){case nt:return nt;case qo:return qo;case pr:return pr;case hr:return hr;case gi:return gi;case Ul:return Ul;case Ks:case Id:case Ud:case Bd:case Hd:case Vd:case Wd:case Yd:case Bl:case Gd:case Qs:case qs:case Kd:case hc:case Qd:case qd:return e&Xo;case Hl:case mc:case Xd:case Jd:case Zd:return e&gc;case Xs:return Xs;case vc:return vc;case Jo:return Jo;case mi:return mi;default:return y("Should have found matching lanes. This is a bug in React."),e}}function vi(e,t){var a=e.pendingLanes;if(a===ie)return ie;var l=ie,c=e.suspendedLanes,p=e.pingedLanes,v=a&qm;if(v!==ie){var w=v&~c;if(w!==ie)l=yc(w);else{var C=v&p;C!==ie&&(l=yc(C))}}else{var R=a&~c;R!==ie?l=yc(R):p!==ie&&(l=yc(p))}if(l===ie)return ie;if(t!==ie&&t!==l&&(t&c)===ie){var M=Vl(l),U=Vl(t);if(M>=U||M===gi&&(U&Xo)!==ie)return t}(l&pr)!==ie&&(l|=a&gi);var F=e.entangledLanes;if(F!==ie)for(var X=e.entanglements,Z=l&F;Z>0;){var te=Bn(Z),Re=1<<te;l|=X[te],Z&=~Re}return l}function nh(e,t){for(var a=e.eventTimes,l=rn;t>0;){var c=Bn(t),p=1<<c,v=a[c];v>l&&(l=v),t&=~p}return l}function nf(e,t){switch(e){case nt:case qo:case pr:return t+250;case hr:case gi:case Ul:case Ks:case Id:case Ud:case Bd:case Hd:case Vd:case Wd:case Yd:case Bl:case Gd:case Qs:case qs:case Kd:case hc:case Qd:case qd:return t+5e3;case Hl:case mc:case Xd:case Jd:case Zd:return rn;case Xs:case vc:case Jo:case mi:return rn;default:return y("Should have found matching lanes. This is a bug in React."),rn}}function Jm(e,t){for(var a=e.pendingLanes,l=e.suspendedLanes,c=e.pingedLanes,p=e.expirationTimes,v=a;v>0;){var w=Bn(v),C=1<<w,R=p[w];R===rn?((C&l)===ie||(C&c)!==ie)&&(p[w]=nf(C,t)):R<=t&&(e.expiredLanes|=C),v&=~C}}function Zm(e){return yc(e.pendingLanes)}function rf(e){var t=e.pendingLanes&~mi;return t!==ie?t:t&mi?mi:ie}function rh(e){return(e&nt)!==ie}function Zo(e){return(e&qm)!==ie}function af(e){return(e&gc)===e}function ih(e){var t=nt|pr|gi;return(e&t)===ie}function L0(e){return(e&Xo)===e}function xc(e,t){var a=qo|pr|hr|gi;return(t&a)!==ie}function ev(e,t){return(t&e.expiredLanes)!==ie}function ah(e){return(e&Xo)!==ie}function oh(){var e=ef;return ef<<=1,(ef&Xo)===ie&&(ef=Ks),e}function tv(){var e=tf;return tf<<=1,(tf&gc)===ie&&(tf=Hl),e}function Vl(e){return e&-e}function gr(e){return Vl(e)}function Bn(e){return 31-rr(e)}function of(e){return Bn(e)}function yi(e,t){return(e&t)!==ie}function Wl(e,t){return(e&t)===t}function xt(e,t){return e|t}function bc(e,t){return e&~t}function lf(e,t){return e&t}function z0(e){return e}function lh(e,t){return e!==Jn&&e<t?e:t}function sf(e){for(var t=[],a=0;a<th;a++)t.push(e);return t}function Js(e,t,a){e.pendingLanes|=t,t!==Jo&&(e.suspendedLanes=ie,e.pingedLanes=ie);var l=e.eventTimes,c=of(t);l[c]=a}function sh(e,t){e.suspendedLanes|=t,e.pingedLanes&=~t;for(var a=e.expirationTimes,l=t;l>0;){var c=Bn(l),p=1<<c;a[c]=rn,l&=~p}}function uf(e,t,a){e.pingedLanes|=e.suspendedLanes&t}function nv(e,t){var a=e.pendingLanes&~t;e.pendingLanes=t,e.suspendedLanes=ie,e.pingedLanes=ie,e.expiredLanes&=t,e.mutableReadLanes&=t,e.entangledLanes&=t;for(var l=e.entanglements,c=e.eventTimes,p=e.expirationTimes,v=a;v>0;){var w=Bn(v),C=1<<w;l[w]=ie,c[w]=rn,p[w]=rn,v&=~C}}function wc(e,t){for(var a=e.entangledLanes|=t,l=e.entanglements,c=a;c;){var p=Bn(c),v=1<<p;v&t|l[p]&t&&(l[p]|=t),c&=~v}}function cf(e,t){var a=Vl(t),l;switch(a){case pr:l=qo;break;case gi:l=hr;break;case Ks:case Id:case Ud:case Bd:case Hd:case Vd:case Wd:case Yd:case Bl:case Gd:case Qs:case qs:case Kd:case hc:case Qd:case qd:case Hl:case mc:case Xd:case Jd:case Zd:l=Ul;break;case Jo:l=vc;break;default:l=Jn;break}return(l&(e.suspendedLanes|t))!==Jn?Jn:l}function rv(e,t,a){if(Fr)for(var l=e.pendingUpdatersLaneMap;a>0;){var c=of(a),p=1<<c,v=l[c];v.add(t),a&=~p}}function uh(e,t){if(Fr)for(var a=e.pendingUpdatersLaneMap,l=e.memoizedUpdaters;t>0;){var c=of(t),p=1<<c,v=a[c];v.size>0&&(v.forEach(function(w){var C=w.alternate;(C===null||!l.has(C))&&l.add(w)}),v.clear()),t&=~p}}function df(e,t){return null}var xi=nt,aa=pr,Cr=gi,ff=Jo,Zs=Jn;function _i(){return Zs}function ir(e){Zs=e}function iv(e,t){var a=Zs;try{return Zs=e,t()}finally{Zs=a}}function Sc(e,t){return e!==0&&e<t?e:t}function Ir(e,t){return e>t?e:t}function ch(e,t){return e!==0&&e<t}function av(e){var t=Vl(e);return ch(xi,t)?ch(aa,t)?Zo(t)?Cr:ff:aa:xi}function Yl(e){var t=e.current.memoizedState;return t.isDehydrated}var Er;function N0(e){Er=e}function Ne(e){Er(e)}var el;function dh(e){el=e}var fh;function P0(e){fh=e}var eu;function pf(e){eu=e}var hf;function ov(e){hf=e}var gf=!1,Cc=[],Ya=null,Ga=null,Mn=null,Qr=new Map,oa=new Map,wo=[],lv=["mousedown","mouseup","touchcancel","touchend","touchstart","auxclick","dblclick","pointercancel","pointerdown","pointerup","dragend","dragstart","drop","compositionend","compositionstart","keydown","keypress","keyup","input","textInput","copy","cut","paste","click","change","contextmenu","reset","submit"];function Ea(e){return lv.indexOf(e)>-1}function sv(e,t,a,l,c){return{blockedOn:e,domEventName:t,eventSystemFlags:a,nativeEvent:c,targetContainers:[l]}}function Ta(e,t){switch(e){case"focusin":case"focusout":Ya=null;break;case"dragenter":case"dragleave":Ga=null;break;case"mouseover":case"mouseout":Mn=null;break;case"pointerover":case"pointerout":{var a=t.pointerId;Qr.delete(a);break}case"gotpointercapture":case"lostpointercapture":{var l=t.pointerId;oa.delete(l);break}}}function Ec(e,t,a,l,c,p){if(e===null||e.nativeEvent!==p){var v=sv(t,a,l,c,p);if(t!==null){var w=cu(t);w!==null&&el(w)}return v}e.eventSystemFlags|=l;var C=e.targetContainers;return c!==null&&C.indexOf(c)===-1&&C.push(c),e}function uv(e,t,a,l,c){switch(t){case"focusin":{var p=c;return Ya=Ec(Ya,e,t,a,l,p),!0}case"dragenter":{var v=c;return Ga=Ec(Ga,e,t,a,l,v),!0}case"mouseover":{var w=c;return Mn=Ec(Mn,e,t,a,l,w),!0}case"pointerover":{var C=c,R=C.pointerId;return Qr.set(R,Ec(Qr.get(R)||null,e,t,a,l,C)),!0}case"gotpointercapture":{var M=c,U=M.pointerId;return oa.set(U,Ec(oa.get(U)||null,e,t,a,l,M)),!0}}return!1}function ph(e){var t=Pc(e.target);if(t!==null){var a=Kr(t);if(a!==null){var l=a.tag;if(l===le){var c=Ha(a);if(c!==null){e.blockedOn=c,hf(e.priority,function(){fh(a)});return}}else if(l===j){var p=a.stateNode;if(Yl(p)){e.blockedOn=Go(a);return}}}}e.blockedOn=null}function cv(e){for(var t=eu(),a={blockedOn:null,target:e,priority:t},l=0;l<wo.length&&ch(t,wo[l].priority);l++);wo.splice(l,0,a),l===0&&ph(a)}function Tc(e){if(e.blockedOn!==null)return!1;for(var t=e.targetContainers;t.length>0;){var a=t[0],l=kc(e.domEventName,e.eventSystemFlags,a,e.nativeEvent);if(l===null){var c=e.nativeEvent,p=new c.constructor(c.type,c);$0(p),c.target.dispatchEvent(p),Ju()}else{var v=cu(l);return v!==null&&el(v),e.blockedOn=l,!1}t.shift()}return!0}function dv(e,t,a){Tc(e)&&a.delete(t)}function mf(){gf=!1,Ya!==null&&Tc(Ya)&&(Ya=null),Ga!==null&&Tc(Ga)&&(Ga=null),Mn!==null&&Tc(Mn)&&(Mn=null),Qr.forEach(dv),oa.forEach(dv)}function Gl(e,t){e.blockedOn===t&&(e.blockedOn=null,gf||(gf=!0,s.unstable_scheduleCallback(s.unstable_NormalPriority,mf)))}function Ur(e){if(Cc.length>0){Gl(Cc[0],e);for(var t=1;t<Cc.length;t++){var a=Cc[t];a.blockedOn===e&&(a.blockedOn=null)}}Ya!==null&&Gl(Ya,e),Ga!==null&&Gl(Ga,e),Mn!==null&&Gl(Mn,e);var l=function(w){return Gl(w,e)};Qr.forEach(l),oa.forEach(l);for(var c=0;c<wo.length;c++){var p=wo[c];p.blockedOn===e&&(p.blockedOn=null)}for(;wo.length>0;){var v=wo[0];if(v.blockedOn!==null)break;ph(v),v.blockedOn===null&&wo.shift()}}var jt=d.ReactCurrentBatchConfig,Zn=!0;function Hn(e){Zn=!!e}function Tr(){return Zn}function Li(e,t,a){var l=nu(t),c;switch(l){case xi:c=tu;break;case aa:c=ar;break;case Cr:default:c=Kl;break}return c.bind(null,t,a,e)}function tu(e,t,a,l){var c=_i(),p=jt.transition;jt.transition=null;try{ir(xi),Kl(e,t,a,l)}finally{ir(c),jt.transition=p}}function ar(e,t,a,l){var c=_i(),p=jt.transition;jt.transition=null;try{ir(aa),Kl(e,t,a,l)}finally{ir(c),jt.transition=p}}function Kl(e,t,a,l){Zn&&Ql(e,t,a,l)}function Ql(e,t,a,l){var c=kc(e,t,a,l);if(c===null){tb(e,t,l,ql,a),Ta(e,l);return}if(uv(c,e,t,a,l)){l.stopPropagation();return}if(Ta(e,l),t&$l&&Ea(e)){for(;c!==null;){var p=cu(c);p!==null&&Ne(p);var v=kc(e,t,a,l);if(v===null&&tb(e,t,l,ql,a),v===c)break;c=v}c!==null&&l.stopPropagation();return}tb(e,t,l,null,a)}var ql=null;function kc(e,t,a,l){ql=null;var c=kd(l),p=Pc(c);if(p!==null){var v=Kr(p);if(v===null)p=null;else{var w=v.tag;if(w===le){var C=Ha(v);if(C!==null)return C;p=null}else if(w===j){var R=v.stateNode;if(Yl(R))return Go(v);p=null}else v!==p&&(p=null)}}return ql=p,null}function nu(e){switch(e){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return xi;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return aa;case"message":{var t=Hm();switch(t){case xo:return xi;case lc:return aa;case Pl:case sc:return Cr;case Vs:return ff;default:return Cr}}default:return Cr}}function bi(e,t,a){return e.addEventListener(t,a,!1),a}function hh(e,t,a){return e.addEventListener(t,a,!0),a}function ru(e,t,a,l){return e.addEventListener(t,a,{capture:!0,passive:l}),a}function So(e,t,a,l){return e.addEventListener(t,a,{passive:l}),a}var tl=null,Rc=null,la=null;function vf(e){return tl=e,Rc=iu(),!0}function nl(){tl=null,Rc=null,la=null}function Dc(){if(la)return la;var e,t=Rc,a=t.length,l,c=iu(),p=c.length;for(e=0;e<a&&t[e]===c[e];e++);var v=a-e;for(l=1;l<=v&&t[a-l]===c[p-l];l++);var w=l>1?1-l:void 0;return la=c.slice(e,w),la}function iu(){return"value"in tl?tl.value:tl.textContent}function au(e){var t,a=e.keyCode;return"charCode"in e?(t=e.charCode,t===0&&a===13&&(t=13)):t=a,t===10&&(t=13),t>=32||t===13?t:0}function Xl(){return!0}function Mc(){return!1}function mn(e){function t(a,l,c,p,v){this._reactName=a,this._targetInst=c,this.type=l,this.nativeEvent=p,this.target=v,this.currentTarget=null;for(var w in e)if(e.hasOwnProperty(w)){var C=e[w];C?this[w]=C(p):this[w]=p[w]}var R=p.defaultPrevented!=null?p.defaultPrevented:p.returnValue===!1;return R?this.isDefaultPrevented=Xl:this.isDefaultPrevented=Mc,this.isPropagationStopped=Mc,this}return gt(t.prototype,{preventDefault:function(){this.defaultPrevented=!0;var a=this.nativeEvent;a&&(a.preventDefault?a.preventDefault():typeof a.returnValue!="unknown"&&(a.returnValue=!1),this.isDefaultPrevented=Xl)},stopPropagation:function(){var a=this.nativeEvent;a&&(a.stopPropagation?a.stopPropagation():typeof a.cancelBubble!="unknown"&&(a.cancelBubble=!0),this.isPropagationStopped=Xl)},persist:function(){},isPersistent:Xl}),t}var zi={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(e){return e.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Ni=mn(zi),mr=gt({},zi,{view:0,detail:0}),fv=mn(mr),Oc,$c,Ac;function rl(e){e!==Ac&&(Ac&&e.type==="mousemove"?(Oc=e.screenX-Ac.screenX,$c=e.screenY-Ac.screenY):(Oc=0,$c=0),Ac=e)}var jc=gt({},mr,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:yh,button:0,buttons:0,relatedTarget:function(e){return e.relatedTarget===void 0?e.fromElement===e.srcElement?e.toElement:e.fromElement:e.relatedTarget},movementX:function(e){return"movementX"in e?e.movementX:(rl(e),Oc)},movementY:function(e){return"movementY"in e?e.movementY:$c}}),yf=mn(jc),Jl=gt({},jc,{dataTransfer:0}),gh=mn(Jl),Zl=gt({},mr,{relatedTarget:0}),xf=mn(Zl),pv=gt({},zi,{animationName:0,elapsedTime:0,pseudoElement:0}),mh=mn(pv),bf=gt({},zi,{clipboardData:function(e){return"clipboardData"in e?e.clipboardData:window.clipboardData}}),F0=mn(bf),I0=gt({},zi,{data:0}),vh=mn(I0),hv=vh,es={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},U0={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"};function ou(e){if(e.key){var t=es[e.key]||e.key;if(t!=="Unidentified")return t}if(e.type==="keypress"){var a=au(e);return a===13?"Enter":String.fromCharCode(a)}return e.type==="keydown"||e.type==="keyup"?U0[e.keyCode]||"Unidentified":""}var gv={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function Nn(e){var t=this,a=t.nativeEvent;if(a.getModifierState)return a.getModifierState(e);var l=gv[e];return l?!!a[l]:!1}function yh(e){return Nn}var mv=gt({},mr,{key:ou,code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:yh,charCode:function(e){return e.type==="keypress"?au(e):0},keyCode:function(e){return e.type==="keydown"||e.type==="keyup"?e.keyCode:0},which:function(e){return e.type==="keypress"?au(e):e.type==="keydown"||e.type==="keyup"?e.keyCode:0}}),B0=mn(mv),H0=gt({},jc,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),xh=mn(H0),vv=gt({},mr,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:yh}),V0=mn(vv),sa=gt({},zi,{propertyName:0,elapsedTime:0,pseudoElement:0}),bh=mn(sa),W0=gt({},jc,{deltaX:function(e){return"deltaX"in e?e.deltaX:"wheelDeltaX"in e?-e.wheelDeltaX:0},deltaY:function(e){return"deltaY"in e?e.deltaY:"wheelDeltaY"in e?-e.wheelDeltaY:"wheelDelta"in e?-e.wheelDelta:0},deltaZ:0,deltaMode:0}),il=mn(W0),wf=[9,13,27,32],al=229,lu=an&&"CompositionEvent"in window,ts=null;an&&"documentMode"in document&&(ts=document.documentMode);var wh=an&&"TextEvent"in window&&!ts,yv=an&&(!lu||ts&&ts>8&&ts<=11),Sf=32,xv=String.fromCharCode(Sf);function bv(){Ut("onBeforeInput",["compositionend","keypress","textInput","paste"]),Ut("onCompositionEnd",["compositionend","focusout","keydown","keypress","keyup","mousedown"]),Ut("onCompositionStart",["compositionstart","focusout","keydown","keypress","keyup","mousedown"]),Ut("onCompositionUpdate",["compositionupdate","focusout","keydown","keypress","keyup","mousedown"])}var Sh=!1;function Cf(e){return(e.ctrlKey||e.altKey||e.metaKey)&&!(e.ctrlKey&&e.altKey)}function Ef(e){switch(e){case"compositionstart":return"onCompositionStart";case"compositionend":return"onCompositionEnd";case"compositionupdate":return"onCompositionUpdate"}}function wv(e,t){return e==="keydown"&&t.keyCode===al}function Tf(e,t){switch(e){case"keyup":return wf.indexOf(t.keyCode)!==-1;case"keydown":return t.keyCode!==al;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function Sv(e){var t=e.detail;return typeof t=="object"&&"data"in t?t.data:null}function Ch(e){return e.locale==="ko"}var ol=!1;function kf(e,t,a,l,c){var p,v;if(lu?p=Ef(t):ol?Tf(t,l)&&(p="onCompositionEnd"):wv(t,l)&&(p="onCompositionStart"),!p)return null;yv&&!Ch(l)&&(!ol&&p==="onCompositionStart"?ol=vf(c):p==="onCompositionEnd"&&ol&&(v=Dc()));var w=Dv(a,p);if(w.length>0){var C=new vh(p,t,null,l,c);if(e.push({event:C,listeners:w}),v)C.data=v;else{var R=Sv(l);R!==null&&(C.data=R)}}}function Eh(e,t){switch(e){case"compositionend":return Sv(t);case"keypress":var a=t.which;return a!==Sf?null:(Sh=!0,xv);case"textInput":var l=t.data;return l===xv&&Sh?null:l;default:return null}}function Rf(e,t){if(ol){if(e==="compositionend"||!lu&&Tf(e,t)){var a=Dc();return nl(),ol=!1,a}return null}switch(e){case"paste":return null;case"keypress":if(!Cf(t)){if(t.char&&t.char.length>1)return t.char;if(t.which)return String.fromCharCode(t.which)}return null;case"compositionend":return yv&&!Ch(t)?null:t.data;default:return null}}function Cv(e,t,a,l,c){var p;if(wh?p=Eh(t,l):p=Rf(t,l),!p)return null;var v=Dv(a,"onBeforeInput");if(v.length>0){var w=new hv("onBeforeInput","beforeinput",null,l,c);e.push({event:w,listeners:v}),w.data=p}}function Y0(e,t,a,l,c,p,v){kf(e,t,a,l,c),Cv(e,t,a,l,c)}var Df={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function Ev(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t==="input"?!!Df[e.type]:t==="textarea"}/**
 * Checks if an event is supported in the current execution environment.
 *
 * NOTE: This will not work correctly for non-generic events such as `change`,
 * `reset`, `load`, `error`, and `select`.
 *
 * Borrows from Modernizr.
 *
 * @param {string} eventNameSuffix Event name, e.g. "click".
 * @return {boolean} True if the event is supported.
 * @internal
 * @license Modernizr 3.0.0pre (Custom Build) | MIT
 */function _c(e){if(!an)return!1;var t="on"+e,a=t in document;if(!a){var l=document.createElement("div");l.setAttribute(t,"return;"),a=typeof l[t]=="function"}return a}function G0(){Ut("onChange",["change","click","focusin","focusout","input","keydown","keyup","selectionchange"])}function Lc(e,t,a,l){Lp(l);var c=Dv(t,"onChange");if(c.length>0){var p=new Ni("onChange","change",null,a,l);e.push({event:p,listeners:c})}}var n=null,i=null;function u(e){var t=e.nodeName&&e.nodeName.toLowerCase();return t==="select"||t==="input"&&e.type==="file"}function f(e){var t=[];Lc(t,i,e,kd(e)),Nm(h,t)}function h(e){UE(e,0)}function x(e){var t=_f(e);if(Po(t))return e}function k(e,t){if(e==="change")return t}var A=!1;an&&(A=_c("input")&&(!document.documentMode||document.documentMode>9));function N(e,t){n=e,i=t,n.attachEvent("onpropertychange",he)}function J(){n&&(n.detachEvent("onpropertychange",he),n=null,i=null)}function he(e){e.propertyName==="value"&&x(i)&&f(e)}function ge(e,t,a){e==="focusin"?(J(),N(t,a)):e==="focusout"&&J()}function pe(e,t){if(e==="selectionchange"||e==="keyup"||e==="keydown")return x(i)}function je(e){var t=e.nodeName;return t&&t.toLowerCase()==="input"&&(e.type==="checkbox"||e.type==="radio")}function Fe(e,t){if(e==="click")return x(t)}function Ue(e,t){if(e==="input"||e==="change")return x(t)}function Vn(e){var t=e._wrapperState;!t||!t.controlled||e.type!=="number"||Pe(e,"number",e.value)}function W(e,t,a,l,c,p,v){var w=a?_f(a):window,C,R;if(u(w)?C=k:Ev(w)?A?C=Ue:(C=pe,R=ge):je(w)&&(C=Fe),C){var M=C(t,a);if(M){Lc(e,M,l,c);return}}R&&R(t,w,a),t==="focusout"&&Vn(w)}function I(){pn("onMouseEnter",["mouseout","mouseover"]),pn("onMouseLeave",["mouseout","mouseover"]),pn("onPointerEnter",["pointerout","pointerover"]),pn("onPointerLeave",["pointerout","pointerover"])}function K(e,t,a,l,c,p,v){var w=t==="mouseover"||t==="pointerover",C=t==="mouseout"||t==="pointerout";if(w&&!A0(l)){var R=l.relatedTarget||l.fromElement;if(R&&(Pc(R)||Ph(R)))return}if(!(!C&&!w)){var M;if(c.window===c)M=c;else{var U=c.ownerDocument;U?M=U.defaultView||U.parentWindow:M=window}var F,X;if(C){var Z=l.relatedTarget||l.toElement;if(F=a,X=Z?Pc(Z):null,X!==null){var te=Kr(X);(X!==te||X.tag!==L&&X.tag!==Q)&&(X=null)}}else F=null,X=a;if(F!==X){var Re=yf,Xe="onMouseLeave",We="onMouseEnter",It="mouse";(t==="pointerout"||t==="pointerover")&&(Re=xh,Xe="onPointerLeave",We="onPointerEnter",It="pointer");var _t=F==null?M:_f(F),Y=X==null?M:_f(X),ne=new Re(Xe,It+"leave",F,l,c);ne.target=_t,ne.relatedTarget=Y;var G=null,me=Pc(c);if(me===a){var Le=new Re(We,It+"enter",X,l,c);Le.target=Y,Le.relatedTarget=_t,G=Le}lz(e,ne,G,F,X)}}}function ve(e,t){return e===t&&(e!==0||1/e===1/t)||e!==e&&t!==t}var Me=typeof Object.is=="function"?Object.is:ve;function Ke(e,t){if(Me(e,t))return!0;if(typeof e!="object"||e===null||typeof t!="object"||t===null)return!1;var a=Object.keys(e),l=Object.keys(t);if(a.length!==l.length)return!1;for(var c=0;c<a.length;c++){var p=a[c];if(!Pn.call(t,p)||!Me(e[p],t[p]))return!1}return!0}function Ze(e){for(;e&&e.firstChild;)e=e.firstChild;return e}function st(e){for(;e;){if(e.nextSibling)return e.nextSibling;e=e.parentNode}}function or(e,t){for(var a=Ze(e),l=0,c=0;a;){if(a.nodeType===go){if(c=l+a.textContent.length,l<=t&&c>=t)return{node:a,offset:t-l};l=c}a=Ze(st(a))}}function Vt(e){var t=e.ownerDocument,a=t&&t.defaultView||window,l=a.getSelection&&a.getSelection();if(!l||l.rangeCount===0)return null;var c=l.anchorNode,p=l.anchorOffset,v=l.focusNode,w=l.focusOffset;try{c.nodeType,v.nodeType}catch{return null}return ll(e,c,p,v,w)}function ll(e,t,a,l,c){var p=0,v=-1,w=-1,C=0,R=0,M=e,U=null;e:for(;;){for(var F=null;M===t&&(a===0||M.nodeType===go)&&(v=p+a),M===l&&(c===0||M.nodeType===go)&&(w=p+c),M.nodeType===go&&(p+=M.nodeValue.length),(F=M.firstChild)!==null;)U=M,M=F;for(;;){if(M===e)break e;if(U===t&&++C===a&&(v=p),U===l&&++R===c&&(w=p),(F=M.nextSibling)!==null)break;M=U,U=M.parentNode}M=F}return v===-1||w===-1?null:{start:v,end:w}}function K0(e,t){var a=e.ownerDocument||document,l=a&&a.defaultView||window;if(l.getSelection){var c=l.getSelection(),p=e.textContent.length,v=Math.min(t.start,p),w=t.end===void 0?v:Math.min(t.end,p);if(!c.extend&&v>w){var C=w;w=v,v=C}var R=or(e,v),M=or(e,w);if(R&&M){if(c.rangeCount===1&&c.anchorNode===R.node&&c.anchorOffset===R.offset&&c.focusNode===M.node&&c.focusOffset===M.offset)return;var U=a.createRange();U.setStart(R.node,R.offset),c.removeAllRanges(),v>w?(c.addRange(U),c.extend(M.node,M.offset)):(U.setEnd(M.node,M.offset),c.addRange(U))}}}function ME(e){return e&&e.nodeType===go}function OE(e,t){return!e||!t?!1:e===t?!0:ME(e)?!1:ME(t)?OE(e,t.parentNode):"contains"in e?e.contains(t):e.compareDocumentPosition?!!(e.compareDocumentPosition(t)&16):!1}function HL(e){return e&&e.ownerDocument&&OE(e.ownerDocument.documentElement,e)}function VL(e){try{return typeof e.contentWindow.location.href=="string"}catch{return!1}}function $E(){for(var e=window,t=fo();t instanceof e.HTMLIFrameElement;){if(VL(t))e=t.contentWindow;else return t;t=fo(e.document)}return t}function Q0(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t&&(t==="input"&&(e.type==="text"||e.type==="search"||e.type==="tel"||e.type==="url"||e.type==="password")||t==="textarea"||e.contentEditable==="true")}function WL(){var e=$E();return{focusedElem:e,selectionRange:Q0(e)?GL(e):null}}function YL(e){var t=$E(),a=e.focusedElem,l=e.selectionRange;if(t!==a&&HL(a)){l!==null&&Q0(a)&&KL(a,l);for(var c=[],p=a;p=p.parentNode;)p.nodeType===di&&c.push({element:p,left:p.scrollLeft,top:p.scrollTop});typeof a.focus=="function"&&a.focus();for(var v=0;v<c.length;v++){var w=c[v];w.element.scrollLeft=w.left,w.element.scrollTop=w.top}}}function GL(e){var t;return"selectionStart"in e?t={start:e.selectionStart,end:e.selectionEnd}:t=Vt(e),t||{start:0,end:0}}function KL(e,t){var a=t.start,l=t.end;l===void 0&&(l=a),"selectionStart"in e?(e.selectionStart=a,e.selectionEnd=Math.min(l,e.value.length)):K0(e,t)}var QL=an&&"documentMode"in document&&document.documentMode<=11;function qL(){Ut("onSelect",["focusout","contextmenu","dragend","focusin","keydown","keyup","mousedown","mouseup","selectionchange"])}var Mf=null,q0=null,Th=null,X0=!1;function XL(e){if("selectionStart"in e&&Q0(e))return{start:e.selectionStart,end:e.selectionEnd};var t=e.ownerDocument&&e.ownerDocument.defaultView||window,a=t.getSelection();return{anchorNode:a.anchorNode,anchorOffset:a.anchorOffset,focusNode:a.focusNode,focusOffset:a.focusOffset}}function JL(e){return e.window===e?e.document:e.nodeType===mo?e:e.ownerDocument}function AE(e,t,a){var l=JL(a);if(!(X0||Mf==null||Mf!==fo(l))){var c=XL(Mf);if(!Th||!Ke(Th,c)){Th=c;var p=Dv(q0,"onSelect");if(p.length>0){var v=new Ni("onSelect","select",null,t,a);e.push({event:v,listeners:p}),v.target=Mf}}}}function ZL(e,t,a,l,c,p,v){var w=a?_f(a):window;switch(t){case"focusin":(Ev(w)||w.contentEditable==="true")&&(Mf=w,q0=a,Th=null);break;case"focusout":Mf=null,q0=null,Th=null;break;case"mousedown":X0=!0;break;case"contextmenu":case"mouseup":case"dragend":X0=!1,AE(e,l,c);break;case"selectionchange":if(QL)break;case"keydown":case"keyup":AE(e,l,c)}}function Tv(e,t){var a={};return a[e.toLowerCase()]=t.toLowerCase(),a["Webkit"+e]="webkit"+t,a["Moz"+e]="moz"+t,a}var Of={animationend:Tv("Animation","AnimationEnd"),animationiteration:Tv("Animation","AnimationIteration"),animationstart:Tv("Animation","AnimationStart"),transitionend:Tv("Transition","TransitionEnd")},J0={},jE={};an&&(jE=document.createElement("div").style,"AnimationEvent"in window||(delete Of.animationend.animation,delete Of.animationiteration.animation,delete Of.animationstart.animation),"TransitionEvent"in window||delete Of.transitionend.transition);function kv(e){if(J0[e])return J0[e];if(!Of[e])return e;var t=Of[e];for(var a in t)if(t.hasOwnProperty(a)&&a in jE)return J0[e]=t[a];return e}var _E=kv("animationend"),LE=kv("animationiteration"),zE=kv("animationstart"),NE=kv("transitionend"),PE=new Map,FE=["abort","auxClick","cancel","canPlay","canPlayThrough","click","close","contextMenu","copy","cut","drag","dragEnd","dragEnter","dragExit","dragLeave","dragOver","dragStart","drop","durationChange","emptied","encrypted","ended","error","gotPointerCapture","input","invalid","keyDown","keyPress","keyUp","load","loadedData","loadedMetadata","loadStart","lostPointerCapture","mouseDown","mouseMove","mouseOut","mouseOver","mouseUp","paste","pause","play","playing","pointerCancel","pointerDown","pointerMove","pointerOut","pointerOver","pointerUp","progress","rateChange","reset","resize","seeked","seeking","stalled","submit","suspend","timeUpdate","touchCancel","touchEnd","touchStart","volumeChange","scroll","toggle","touchMove","waiting","wheel"];function su(e,t){PE.set(e,t),Ut(t,[e])}function ez(){for(var e=0;e<FE.length;e++){var t=FE[e],a=t.toLowerCase(),l=t[0].toUpperCase()+t.slice(1);su(a,"on"+l)}su(_E,"onAnimationEnd"),su(LE,"onAnimationIteration"),su(zE,"onAnimationStart"),su("dblclick","onDoubleClick"),su("focusin","onFocus"),su("focusout","onBlur"),su(NE,"onTransitionEnd")}function tz(e,t,a,l,c,p,v){var w=PE.get(t);if(w!==void 0){var C=Ni,R=t;switch(t){case"keypress":if(au(l)===0)return;case"keydown":case"keyup":C=B0;break;case"focusin":R="focus",C=xf;break;case"focusout":R="blur",C=xf;break;case"beforeblur":case"afterblur":C=xf;break;case"click":if(l.button===2)return;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":C=yf;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":C=gh;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":C=V0;break;case _E:case LE:case zE:C=mh;break;case NE:C=bh;break;case"scroll":C=fv;break;case"wheel":C=il;break;case"copy":case"cut":case"paste":C=F0;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":C=xh;break}var M=(p&$l)!==0;{var U=!M&&t==="scroll",F=az(a,w,l.type,M,U);if(F.length>0){var X=new C(w,R,null,l,c);e.push({event:X,listeners:F})}}}}ez(),I(),G0(),qL(),bv();function nz(e,t,a,l,c,p,v){tz(e,t,a,l,c,p);var w=(p&_p)===0;w&&(K(e,t,a,l,c),W(e,t,a,l,c),ZL(e,t,a,l,c),Y0(e,t,a,l,c))}var kh=["abort","canplay","canplaythrough","durationchange","emptied","encrypted","ended","error","loadeddata","loadedmetadata","loadstart","pause","play","playing","progress","ratechange","resize","seeked","seeking","stalled","suspend","timeupdate","volumechange","waiting"],Z0=new Set(["cancel","close","invalid","load","scroll","toggle"].concat(kh));function IE(e,t,a){var l=e.type||"unknown-event";e.currentTarget=a,rc(l,t,void 0,e),e.currentTarget=null}function rz(e,t,a){var l;if(a)for(var c=t.length-1;c>=0;c--){var p=t[c],v=p.instance,w=p.currentTarget,C=p.listener;if(v!==l&&e.isPropagationStopped())return;IE(e,C,w),l=v}else for(var R=0;R<t.length;R++){var M=t[R],U=M.instance,F=M.currentTarget,X=M.listener;if(U!==l&&e.isPropagationStopped())return;IE(e,X,F),l=U}}function UE(e,t){for(var a=(t&$l)!==0,l=0;l<e.length;l++){var c=e[l],p=c.event,v=c.listeners;rz(p,v,a)}vo()}function iz(e,t,a,l,c){var p=kd(a),v=[];nz(v,e,l,a,p,t),UE(v,t)}function jn(e,t){Z0.has(e)||y('Did not expect a listenToNonDelegatedEvent() call for "%s". This is a bug in React. Please file an issue.',e);var a=!1,l=_N(t),c=sz(e);l.has(c)||(BE(t,e,Ua,a),l.add(c))}function eb(e,t,a){Z0.has(e)&&!t&&y('Did not expect a listenToNativeEvent() call for "%s" in the bubble phase. This is a bug in React. Please file an issue.',e);var l=0;t&&(l|=$l),BE(a,e,l,t)}var Rv="_reactListening"+Math.random().toString(36).slice(2);function Rh(e){if(!e[Rv]){e[Rv]=!0,Ot.forEach(function(a){a!=="selectionchange"&&(Z0.has(a)||eb(a,!1,e),eb(a,!0,e))});var t=e.nodeType===mo?e:e.ownerDocument;t!==null&&(t[Rv]||(t[Rv]=!0,eb("selectionchange",!1,t)))}}function BE(e,t,a,l,c){var p=Li(e,t,a),v=void 0;nc&&(t==="touchstart"||t==="touchmove"||t==="wheel")&&(v=!0),e=e,l?v!==void 0?ru(e,t,p,v):hh(e,t,p):v!==void 0?So(e,t,p,v):bi(e,t,p)}function HE(e,t){return e===t||e.nodeType===Qn&&e.parentNode===t}function tb(e,t,a,l,c){var p=l;if(!(t&jp)&&!(t&Ua)){var v=c;if(l!==null){var w=l;e:for(;;){if(w===null)return;var C=w.tag;if(C===j||C===H){var R=w.stateNode.containerInfo;if(HE(R,v))break;if(C===H)for(var M=w.return;M!==null;){var U=M.tag;if(U===j||U===H){var F=M.stateNode.containerInfo;if(HE(F,v))return}M=M.return}for(;R!==null;){var X=Pc(R);if(X===null)return;var Z=X.tag;if(Z===L||Z===Q){w=p=X;continue e}R=R.parentNode}}w=w.return}}}Nm(function(){return iz(e,t,a,p)})}function Dh(e,t,a){return{instance:e,listener:t,currentTarget:a}}function az(e,t,a,l,c,p){for(var v=t!==null?t+"Capture":null,w=l?v:t,C=[],R=e,M=null;R!==null;){var U=R,F=U.stateNode,X=U.tag;if(X===L&&F!==null&&(M=F,w!==null)){var Z=jl(R,w);Z!=null&&C.push(Dh(R,Z,M))}if(c)break;R=R.return}return C}function Dv(e,t){for(var a=t+"Capture",l=[],c=e;c!==null;){var p=c,v=p.stateNode,w=p.tag;if(w===L&&v!==null){var C=v,R=jl(c,a);R!=null&&l.unshift(Dh(c,R,C));var M=jl(c,t);M!=null&&l.push(Dh(c,M,C))}c=c.return}return l}function $f(e){if(e===null)return null;do e=e.return;while(e&&e.tag!==L);return e||null}function oz(e,t){for(var a=e,l=t,c=0,p=a;p;p=$f(p))c++;for(var v=0,w=l;w;w=$f(w))v++;for(;c-v>0;)a=$f(a),c--;for(;v-c>0;)l=$f(l),v--;for(var C=c;C--;){if(a===l||l!==null&&a===l.alternate)return a;a=$f(a),l=$f(l)}return null}function VE(e,t,a,l,c){for(var p=t._reactName,v=[],w=a;w!==null&&w!==l;){var C=w,R=C.alternate,M=C.stateNode,U=C.tag;if(R!==null&&R===l)break;if(U===L&&M!==null){var F=M;if(c){var X=jl(w,p);X!=null&&v.unshift(Dh(w,X,F))}else if(!c){var Z=jl(w,p);Z!=null&&v.push(Dh(w,Z,F))}}w=w.return}v.length!==0&&e.push({event:t,listeners:v})}function lz(e,t,a,l,c){var p=l&&c?oz(l,c):null;l!==null&&VE(e,t,l,p,!1),c!==null&&a!==null&&VE(e,a,c,p,!0)}function sz(e,t){return e+"__bubble"}var ua=!1,Mh="dangerouslySetInnerHTML",Mv="suppressContentEditableWarning",uu="suppressHydrationWarning",WE="autoFocus",zc="children",Nc="style",Ov="__html",nb,$v,Oh,YE,Av,GE,KE;nb={dialog:!0,webview:!0},$v=function(e,t){$m(e,t),Ns(e,t),zm(e,t,{registrationNameDependencies:tt,possibleRegistrationNames:vt})},GE=an&&!document.documentMode,Oh=function(e,t,a){if(!ua){var l=jv(a),c=jv(t);c!==l&&(ua=!0,y("Prop `%s` did not match. Server: %s Client: %s",e,JSON.stringify(c),JSON.stringify(l)))}},YE=function(e){if(!ua){ua=!0;var t=[];e.forEach(function(a){t.push(a)}),y("Extra attributes from the server: %s",t)}},Av=function(e,t){t===!1?y("Expected `%s` listener to be a function, instead got `false`.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.",e,e,e):y("Expected `%s` listener to be a function, instead got a value of `%s` type.",e,typeof t)},KE=function(e,t){var a=e.namespaceURI===ya?e.ownerDocument.createElement(e.tagName):e.ownerDocument.createElementNS(e.namespaceURI,e.tagName);return a.innerHTML=t,a.innerHTML};var uz=/\r\n?/g,cz=/\u0000|\uFFFD/g;function jv(e){nr(e);var t=typeof e=="string"?e:""+e;return t.replace(uz,`
`).replace(cz,"")}function _v(e,t,a,l){var c=jv(t),p=jv(e);if(p!==c&&(l&&(ua||(ua=!0,y('Text content did not match. Server: "%s" Client: "%s"',p,c))),a&&se))throw new Error("Text content does not match server-rendered HTML.")}function QE(e){return e.nodeType===mo?e:e.ownerDocument}function dz(){}function Lv(e){e.onclick=dz}function fz(e,t,a,l,c){for(var p in l)if(l.hasOwnProperty(p)){var v=l[p];if(p===Nc)v&&Object.freeze(v),km(t,v);else if(p===Mh){var w=v?v[Ov]:void 0;w!=null&&hm(t,w)}else if(p===zc)if(typeof v=="string"){var C=e!=="textarea"||v!=="";C&&Io(t,v)}else typeof v=="number"&&Io(t,""+v);else p===Mv||p===uu||p===WE||(tt.hasOwnProperty(p)?v!=null&&(typeof v!="function"&&Av(p,v),p==="onScroll"&&jn("scroll",t)):v!=null&&Oi(t,p,v,c))}}function pz(e,t,a,l){for(var c=0;c<t.length;c+=2){var p=t[c],v=t[c+1];p===Nc?km(e,v):p===Mh?hm(e,v):p===zc?Io(e,v):Oi(e,p,v,l)}}function hz(e,t,a,l){var c,p=QE(a),v,w=l;if(w===ya&&(w=Rp(e)),w===ya){if(c=Uo(e,t),!c&&e!==e.toLowerCase()&&y("<%s /> is using incorrect casing. Use PascalCase for React components, or lowercase for HTML elements.",e),e==="script"){var C=p.createElement("div");C.innerHTML="<script><\/script>";var R=C.firstChild;v=C.removeChild(R)}else if(typeof t.is=="string")v=p.createElement(e,{is:t.is});else if(v=p.createElement(e),e==="select"){var M=v;t.multiple?M.multiple=!0:t.size&&(M.size=t.size)}}else v=p.createElementNS(w,e);return w===ya&&!c&&Object.prototype.toString.call(v)==="[object HTMLUnknownElement]"&&!Pn.call(nb,e)&&(nb[e]=!0,y("The tag <%s> is unrecognized in this browser. If you meant to render a React component, start its name with an uppercase letter.",e)),v}function gz(e,t){return QE(t).createTextNode(e)}function mz(e,t,a,l){var c=Uo(t,a);$v(t,a);var p;switch(t){case"dialog":jn("cancel",e),jn("close",e),p=a;break;case"iframe":case"object":case"embed":jn("load",e),p=a;break;case"video":case"audio":for(var v=0;v<kh.length;v++)jn(kh[v],e);p=a;break;case"source":jn("error",e),p=a;break;case"img":case"image":case"link":jn("error",e),jn("load",e),p=a;break;case"details":jn("toggle",e),p=a;break;case"input":Os(e,a),p=ta(e,a),jn("invalid",e);break;case"option":qt(e,a),p=a;break;case"select":Vu(e,a),p=Ol(e,a),jn("invalid",e);break;case"textarea":dm(e,a),p=bd(e,a),jn("invalid",e);break;default:p=a}switch(Ed(t,p),fz(t,e,l,p,c),t){case"input":ea(e),q(e,a,!1);break;case"textarea":ea(e),pm(e);break;case"option":sn(e,a);break;case"select":Ep(e,a);break;default:typeof p.onClick=="function"&&Lv(e);break}}function vz(e,t,a,l,c){$v(t,l);var p=null,v,w;switch(t){case"input":v=ta(e,a),w=ta(e,l),p=[];break;case"select":v=Ol(e,a),w=Ol(e,l),p=[];break;case"textarea":v=bd(e,a),w=bd(e,l),p=[];break;default:v=a,w=l,typeof v.onClick!="function"&&typeof w.onClick=="function"&&Lv(e);break}Ed(t,w);var C,R,M=null;for(C in v)if(!(w.hasOwnProperty(C)||!v.hasOwnProperty(C)||v[C]==null))if(C===Nc){var U=v[C];for(R in U)U.hasOwnProperty(R)&&(M||(M={}),M[R]="")}else C===Mh||C===zc||C===Mv||C===uu||C===WE||(tt.hasOwnProperty(C)?p||(p=[]):(p=p||[]).push(C,null));for(C in w){var F=w[C],X=v!=null?v[C]:void 0;if(!(!w.hasOwnProperty(C)||F===X||F==null&&X==null))if(C===Nc)if(F&&Object.freeze(F),X){for(R in X)X.hasOwnProperty(R)&&(!F||!F.hasOwnProperty(R))&&(M||(M={}),M[R]="");for(R in F)F.hasOwnProperty(R)&&X[R]!==F[R]&&(M||(M={}),M[R]=F[R])}else M||(p||(p=[]),p.push(C,M)),M=F;else if(C===Mh){var Z=F?F[Ov]:void 0,te=X?X[Ov]:void 0;Z!=null&&te!==Z&&(p=p||[]).push(C,Z)}else C===zc?(typeof F=="string"||typeof F=="number")&&(p=p||[]).push(C,""+F):C===Mv||C===uu||(tt.hasOwnProperty(C)?(F!=null&&(typeof F!="function"&&Av(C,F),C==="onScroll"&&jn("scroll",e)),!p&&X!==F&&(p=[])):(p=p||[]).push(C,F))}return M&&(xa(M,w[Nc]),(p=p||[]).push(Nc,M)),p}function yz(e,t,a,l,c){a==="input"&&c.type==="radio"&&c.name!=null&&T(e,c);var p=Uo(a,l),v=Uo(a,c);switch(pz(e,t,p,v),a){case"input":_(e,c);break;case"textarea":fm(e,c);break;case"select":xd(e,c);break}}function xz(e){{var t=e.toLowerCase();return _s.hasOwnProperty(t)&&_s[t]||null}}function bz(e,t,a,l,c,p,v){var w,C;switch(w=Uo(t,a),$v(t,a),t){case"dialog":jn("cancel",e),jn("close",e);break;case"iframe":case"object":case"embed":jn("load",e);break;case"video":case"audio":for(var R=0;R<kh.length;R++)jn(kh[R],e);break;case"source":jn("error",e);break;case"img":case"image":case"link":jn("error",e),jn("load",e);break;case"details":jn("toggle",e);break;case"input":Os(e,a),jn("invalid",e);break;case"option":qt(e,a);break;case"select":Vu(e,a),jn("invalid",e);break;case"textarea":dm(e,a),jn("invalid",e);break}Ed(t,a);{C=new Set;for(var M=e.attributes,U=0;U<M.length;U++){var F=M[U].name.toLowerCase();switch(F){case"value":break;case"checked":break;case"selected":break;default:C.add(M[U].name)}}}var X=null;for(var Z in a)if(a.hasOwnProperty(Z)){var te=a[Z];if(Z===zc)typeof te=="string"?e.textContent!==te&&(a[uu]!==!0&&_v(e.textContent,te,p,v),X=[zc,te]):typeof te=="number"&&e.textContent!==""+te&&(a[uu]!==!0&&_v(e.textContent,te,p,v),X=[zc,""+te]);else if(tt.hasOwnProperty(Z))te!=null&&(typeof te!="function"&&Av(Z,te),Z==="onScroll"&&jn("scroll",e));else if(v&&typeof w=="boolean"){var Re=void 0,Xe=vn(Z);if(a[uu]!==!0){if(!(Z===Mv||Z===uu||Z==="value"||Z==="checked"||Z==="selected")){if(Z===Mh){var We=e.innerHTML,It=te?te[Ov]:void 0;if(It!=null){var _t=KE(e,It);_t!==We&&Oh(Z,We,_t)}}else if(Z===Nc){if(C.delete(Z),GE){var Y=M0(te);Re=e.getAttribute("style"),Y!==Re&&Oh(Z,Re,Y)}}else if(w&&!re)C.delete(Z.toLowerCase()),Re=ja(e,Z,te),te!==Re&&Oh(Z,Re,te);else if(!wn(Z,Xe,w)&&!dr(Z,te,Xe,w)){var ne=!1;if(Xe!==null)C.delete(Xe.attributeName),Re=Cl(e,Z,te,Xe);else{var G=l;if(G===ya&&(G=Rp(t)),G===ya)C.delete(Z.toLowerCase());else{var me=xz(Z);me!==null&&me!==Z&&(ne=!0,C.delete(me)),C.delete(Z)}Re=ja(e,Z,te)}var Le=re;!Le&&te!==Re&&!ne&&Oh(Z,Re,te)}}}}}switch(v&&C.size>0&&a[uu]!==!0&&YE(C),t){case"input":ea(e),q(e,a,!0);break;case"textarea":ea(e),pm(e);break;case"select":case"option":break;default:typeof a.onClick=="function"&&Lv(e);break}return X}function wz(e,t,a){var l=e.nodeValue!==t;return l}function rb(e,t){{if(ua)return;ua=!0,y("Did not expect server HTML to contain a <%s> in <%s>.",t.nodeName.toLowerCase(),e.nodeName.toLowerCase())}}function ib(e,t){{if(ua)return;ua=!0,y('Did not expect server HTML to contain the text node "%s" in <%s>.',t.nodeValue,e.nodeName.toLowerCase())}}function ab(e,t,a){{if(ua)return;ua=!0,y("Expected server HTML to contain a matching <%s> in <%s>.",t,e.nodeName.toLowerCase())}}function ob(e,t){{if(t===""||ua)return;ua=!0,y('Expected server HTML to contain a matching text node for "%s" in <%s>.',t,e.nodeName.toLowerCase())}}function Sz(e,t,a){switch(t){case"input":ee(e,a);return;case"textarea":S0(e,a);return;case"select":Tp(e,a);return}}var $h=function(){},Ah=function(){};{var Cz=["address","applet","area","article","aside","base","basefont","bgsound","blockquote","body","br","button","caption","center","col","colgroup","dd","details","dir","div","dl","dt","embed","fieldset","figcaption","figure","footer","form","frame","frameset","h1","h2","h3","h4","h5","h6","head","header","hgroup","hr","html","iframe","img","input","isindex","li","link","listing","main","marquee","menu","menuitem","meta","nav","noembed","noframes","noscript","object","ol","p","param","plaintext","pre","script","section","select","source","style","summary","table","tbody","td","template","textarea","tfoot","th","thead","title","tr","track","ul","wbr","xmp"],qE=["applet","caption","html","table","td","th","marquee","object","template","foreignObject","desc","title"],Ez=qE.concat(["button"]),Tz=["dd","dt","li","option","optgroup","p","rp","rt"],XE={current:null,formTag:null,aTagInScope:null,buttonTagInScope:null,nobrTagInScope:null,pTagInButtonScope:null,listItemTagAutoclosing:null,dlItemTagAutoclosing:null};Ah=function(e,t){var a=gt({},e||XE),l={tag:t};return qE.indexOf(t)!==-1&&(a.aTagInScope=null,a.buttonTagInScope=null,a.nobrTagInScope=null),Ez.indexOf(t)!==-1&&(a.pTagInButtonScope=null),Cz.indexOf(t)!==-1&&t!=="address"&&t!=="div"&&t!=="p"&&(a.listItemTagAutoclosing=null,a.dlItemTagAutoclosing=null),a.current=l,t==="form"&&(a.formTag=l),t==="a"&&(a.aTagInScope=l),t==="button"&&(a.buttonTagInScope=l),t==="nobr"&&(a.nobrTagInScope=l),t==="p"&&(a.pTagInButtonScope=l),t==="li"&&(a.listItemTagAutoclosing=l),(t==="dd"||t==="dt")&&(a.dlItemTagAutoclosing=l),a};var kz=function(e,t){switch(t){case"select":return e==="option"||e==="optgroup"||e==="#text";case"optgroup":return e==="option"||e==="#text";case"option":return e==="#text";case"tr":return e==="th"||e==="td"||e==="style"||e==="script"||e==="template";case"tbody":case"thead":case"tfoot":return e==="tr"||e==="style"||e==="script"||e==="template";case"colgroup":return e==="col"||e==="template";case"table":return e==="caption"||e==="colgroup"||e==="tbody"||e==="tfoot"||e==="thead"||e==="style"||e==="script"||e==="template";case"head":return e==="base"||e==="basefont"||e==="bgsound"||e==="link"||e==="meta"||e==="title"||e==="noscript"||e==="noframes"||e==="style"||e==="script"||e==="template";case"html":return e==="head"||e==="body"||e==="frameset";case"frameset":return e==="frame";case"#document":return e==="html"}switch(e){case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t!=="h1"&&t!=="h2"&&t!=="h3"&&t!=="h4"&&t!=="h5"&&t!=="h6";case"rp":case"rt":return Tz.indexOf(t)===-1;case"body":case"caption":case"col":case"colgroup":case"frameset":case"frame":case"head":case"html":case"tbody":case"td":case"tfoot":case"th":case"thead":case"tr":return t==null}return!0},Rz=function(e,t){switch(e){case"address":case"article":case"aside":case"blockquote":case"center":case"details":case"dialog":case"dir":case"div":case"dl":case"fieldset":case"figcaption":case"figure":case"footer":case"header":case"hgroup":case"main":case"menu":case"nav":case"ol":case"p":case"section":case"summary":case"ul":case"pre":case"listing":case"table":case"hr":case"xmp":case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t.pTagInButtonScope;case"form":return t.formTag||t.pTagInButtonScope;case"li":return t.listItemTagAutoclosing;case"dd":case"dt":return t.dlItemTagAutoclosing;case"button":return t.buttonTagInScope;case"a":return t.aTagInScope;case"nobr":return t.nobrTagInScope}return null},JE={};$h=function(e,t,a){a=a||XE;var l=a.current,c=l&&l.tag;t!=null&&(e!=null&&y("validateDOMNesting: when childText is passed, childTag should be null"),e="#text");var p=kz(e,c)?null:l,v=p?null:Rz(e,a),w=p||v;if(w){var C=w.tag,R=!!p+"|"+e+"|"+C;if(!JE[R]){JE[R]=!0;var M=e,U="";if(e==="#text"?/\S/.test(t)?M="Text nodes":(M="Whitespace text nodes",U=" Make sure you don't have any extra whitespace between tags on each line of your source code."):M="<"+e+">",p){var F="";C==="table"&&e==="tr"&&(F+=" Add a <tbody>, <thead> or <tfoot> to your code to match the DOM tree generated by the browser."),y("validateDOMNesting(...): %s cannot appear as a child of <%s>.%s%s",M,C,U,F)}else y("validateDOMNesting(...): %s cannot appear as a descendant of <%s>.",M,C)}}}}var zv="suppressHydrationWarning",Nv="$",Pv="/$",jh="$?",_h="$!",Dz="style",lb=null,sb=null;function Mz(e){var t,a,l=e.nodeType;switch(l){case mo:case Wu:{t=l===mo?"#document":"#fragment";var c=e.documentElement;a=c?c.namespaceURI:wd(null,"");break}default:{var p=l===Qn?e.parentNode:e,v=p.namespaceURI||null;t=p.tagName,a=wd(v,t);break}}{var w=t.toLowerCase(),C=Ah(null,w);return{namespace:a,ancestorInfo:C}}}function Oz(e,t,a){{var l=e,c=wd(l.namespace,t),p=Ah(l.ancestorInfo,t);return{namespace:c,ancestorInfo:p}}}function $F(e){return e}function $z(e){lb=Tr(),sb=WL();var t=null;return Hn(!1),t}function Az(e){YL(sb),Hn(lb),lb=null,sb=null}function jz(e,t,a,l,c){var p;{var v=l;if($h(e,null,v.ancestorInfo),typeof t.children=="string"||typeof t.children=="number"){var w=""+t.children,C=Ah(v.ancestorInfo,e);$h(null,w,C)}p=v.namespace}var R=hz(e,t,a,p);return Nh(c,R),mb(R,t),R}function _z(e,t){e.appendChild(t)}function Lz(e,t,a,l,c){switch(mz(e,t,a,l),t){case"button":case"input":case"select":case"textarea":return!!a.autoFocus;case"img":return!0;default:return!1}}function zz(e,t,a,l,c,p){{var v=p;if(typeof l.children!=typeof a.children&&(typeof l.children=="string"||typeof l.children=="number")){var w=""+l.children,C=Ah(v.ancestorInfo,t);$h(null,w,C)}}return vz(e,t,a,l)}function ub(e,t){return e==="textarea"||e==="noscript"||typeof t.children=="string"||typeof t.children=="number"||typeof t.dangerouslySetInnerHTML=="object"&&t.dangerouslySetInnerHTML!==null&&t.dangerouslySetInnerHTML.__html!=null}function Nz(e,t,a,l){{var c=a;$h(null,e,c.ancestorInfo)}var p=gz(e,t);return Nh(l,p),p}function Pz(){var e=window.event;return e===void 0?Cr:nu(e.type)}var cb=typeof setTimeout=="function"?setTimeout:void 0,Fz=typeof clearTimeout=="function"?clearTimeout:void 0,db=-1,ZE=typeof Promise=="function"?Promise:void 0,Iz=typeof queueMicrotask=="function"?queueMicrotask:typeof ZE<"u"?function(e){return ZE.resolve(null).then(e).catch(Uz)}:cb;function Uz(e){setTimeout(function(){throw e})}function Bz(e,t,a,l){switch(t){case"button":case"input":case"select":case"textarea":a.autoFocus&&e.focus();return;case"img":{a.src&&(e.src=a.src);return}}}function Hz(e,t,a,l,c,p){yz(e,t,a,l,c),mb(e,c)}function eT(e){Io(e,"")}function Vz(e,t,a){e.nodeValue=a}function Wz(e,t){e.appendChild(t)}function Yz(e,t){var a;e.nodeType===Qn?(a=e.parentNode,a.insertBefore(t,e)):(a=e,a.appendChild(t));var l=e._reactRootContainer;l==null&&a.onclick===null&&Lv(a)}function Gz(e,t,a){e.insertBefore(t,a)}function Kz(e,t,a){e.nodeType===Qn?e.parentNode.insertBefore(t,a):e.insertBefore(t,a)}function Qz(e,t){e.removeChild(t)}function qz(e,t){e.nodeType===Qn?e.parentNode.removeChild(t):e.removeChild(t)}function fb(e,t){var a=t,l=0;do{var c=a.nextSibling;if(e.removeChild(a),c&&c.nodeType===Qn){var p=c.data;if(p===Pv)if(l===0){e.removeChild(c),Ur(t);return}else l--;else(p===Nv||p===jh||p===_h)&&l++}a=c}while(a);Ur(t)}function Xz(e,t){e.nodeType===Qn?fb(e.parentNode,t):e.nodeType===di&&fb(e,t),Ur(e)}function Jz(e){e=e;var t=e.style;typeof t.setProperty=="function"?t.setProperty("display","none","important"):t.display="none"}function Zz(e){e.nodeValue=""}function eN(e,t){e=e;var a=t[Dz],l=a!=null&&a.hasOwnProperty("display")?a.display:null;e.style.display=Cd("display",l)}function tN(e,t){e.nodeValue=t}function nN(e){e.nodeType===di?e.textContent="":e.nodeType===mo&&e.documentElement&&e.removeChild(e.documentElement)}function rN(e,t,a){return e.nodeType!==di||t.toLowerCase()!==e.nodeName.toLowerCase()?null:e}function iN(e,t){return t===""||e.nodeType!==go?null:e}function aN(e){return e.nodeType!==Qn?null:e}function tT(e){return e.data===jh}function pb(e){return e.data===_h}function oN(e){var t=e.nextSibling&&e.nextSibling.dataset,a,l,c;return t&&(a=t.dgst,l=t.msg,c=t.stck),{message:l,digest:a,stack:c}}function lN(e,t){e._reactRetry=t}function Fv(e){for(;e!=null;e=e.nextSibling){var t=e.nodeType;if(t===di||t===go)break;if(t===Qn){var a=e.data;if(a===Nv||a===_h||a===jh)break;if(a===Pv)return null}}return e}function Lh(e){return Fv(e.nextSibling)}function sN(e){return Fv(e.firstChild)}function uN(e){return Fv(e.firstChild)}function cN(e){return Fv(e.nextSibling)}function dN(e,t,a,l,c,p,v){Nh(p,e),mb(e,a);var w;{var C=c;w=C.namespace}var R=(p.mode&Dt)!==Ge;return bz(e,t,a,w,l,R,v)}function fN(e,t,a,l){return Nh(a,e),a.mode&Dt,wz(e,t)}function pN(e,t){Nh(t,e)}function hN(e){for(var t=e.nextSibling,a=0;t;){if(t.nodeType===Qn){var l=t.data;if(l===Pv){if(a===0)return Lh(t);a--}else(l===Nv||l===_h||l===jh)&&a++}t=t.nextSibling}return null}function nT(e){for(var t=e.previousSibling,a=0;t;){if(t.nodeType===Qn){var l=t.data;if(l===Nv||l===_h||l===jh){if(a===0)return t;a--}else l===Pv&&a++}t=t.previousSibling}return null}function gN(e){Ur(e)}function mN(e){Ur(e)}function vN(e){return e!=="head"&&e!=="body"}function yN(e,t,a,l){var c=!0;_v(t.nodeValue,a,l,c)}function xN(e,t,a,l,c,p){if(t[zv]!==!0){var v=!0;_v(l.nodeValue,c,p,v)}}function bN(e,t){t.nodeType===di?rb(e,t):t.nodeType===Qn||ib(e,t)}function wN(e,t){{var a=e.parentNode;a!==null&&(t.nodeType===di?rb(a,t):t.nodeType===Qn||ib(a,t))}}function SN(e,t,a,l,c){(c||t[zv]!==!0)&&(l.nodeType===di?rb(a,l):l.nodeType===Qn||ib(a,l))}function CN(e,t,a){ab(e,t)}function EN(e,t){ob(e,t)}function TN(e,t,a){{var l=e.parentNode;l!==null&&ab(l,t)}}function kN(e,t){{var a=e.parentNode;a!==null&&ob(a,t)}}function RN(e,t,a,l,c,p){(p||t[zv]!==!0)&&ab(a,l)}function DN(e,t,a,l,c){(c||t[zv]!==!0)&&ob(a,l)}function MN(e){y("An error occurred during hydration. The server HTML was replaced with client content in <%s>.",e.nodeName.toLowerCase())}function ON(e){Rh(e)}var Af=Math.random().toString(36).slice(2),jf="__reactFiber$"+Af,hb="__reactProps$"+Af,zh="__reactContainer$"+Af,gb="__reactEvents$"+Af,$N="__reactListeners$"+Af,AN="__reactHandles$"+Af;function jN(e){delete e[jf],delete e[hb],delete e[gb],delete e[$N],delete e[AN]}function Nh(e,t){t[jf]=e}function Iv(e,t){t[zh]=e}function rT(e){e[zh]=null}function Ph(e){return!!e[zh]}function Pc(e){var t=e[jf];if(t)return t;for(var a=e.parentNode;a;){if(t=a[zh]||a[jf],t){var l=t.alternate;if(t.child!==null||l!==null&&l.child!==null)for(var c=nT(e);c!==null;){var p=c[jf];if(p)return p;c=nT(c)}return t}e=a,a=e.parentNode}return null}function cu(e){var t=e[jf]||e[zh];return t&&(t.tag===L||t.tag===Q||t.tag===le||t.tag===j)?t:null}function _f(e){if(e.tag===L||e.tag===Q)return e.stateNode;throw new Error("getNodeFromInstance: Invalid argument.")}function Uv(e){return e[hb]||null}function mb(e,t){e[hb]=t}function _N(e){var t=e[gb];return t===void 0&&(t=e[gb]=new Set),t}var iT={},aT=d.ReactDebugCurrentFrame;function Bv(e){if(e){var t=e._owner,a=Fu(e.type,e._source,t?t.type:null);aT.setExtraStackFrame(a)}else aT.setExtraStackFrame(null)}function Co(e,t,a,l,c){{var p=Function.call.bind(Pn);for(var v in e)if(p(e,v)){var w=void 0;try{if(typeof e[v]!="function"){var C=Error((l||"React class")+": "+a+" type `"+v+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof e[v]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw C.name="Invariant Violation",C}w=e[v](t,v,l,a,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(R){w=R}w&&!(w instanceof Error)&&(Bv(c),y("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",l||"React class",a,v,typeof w),Bv(null)),w instanceof Error&&!(w.message in iT)&&(iT[w.message]=!0,Bv(c),y("Failed %s type: %s",a,w.message),Bv(null))}}}var vb=[],Hv;Hv=[];var ns=-1;function du(e){return{current:e}}function wi(e,t){if(ns<0){y("Unexpected pop.");return}t!==Hv[ns]&&y("Unexpected Fiber popped."),e.current=vb[ns],vb[ns]=null,Hv[ns]=null,ns--}function Si(e,t,a){ns++,vb[ns]=e.current,Hv[ns]=a,e.current=t}var yb;yb={};var ka={};Object.freeze(ka);var rs=du(ka),sl=du(!1),xb=ka;function Lf(e,t,a){return a&&ul(t)?xb:rs.current}function oT(e,t,a){{var l=e.stateNode;l.__reactInternalMemoizedUnmaskedChildContext=t,l.__reactInternalMemoizedMaskedChildContext=a}}function zf(e,t){{var a=e.type,l=a.contextTypes;if(!l)return ka;var c=e.stateNode;if(c&&c.__reactInternalMemoizedUnmaskedChildContext===t)return c.__reactInternalMemoizedMaskedChildContext;var p={};for(var v in l)p[v]=t[v];{var w=lt(e)||"Unknown";Co(l,p,"context",w)}return c&&oT(e,t,p),p}}function Vv(){return sl.current}function ul(e){{var t=e.childContextTypes;return t!=null}}function Wv(e){wi(sl,e),wi(rs,e)}function bb(e){wi(sl,e),wi(rs,e)}function lT(e,t,a){{if(rs.current!==ka)throw new Error("Unexpected context found on stack. This error is likely caused by a bug in React. Please file an issue.");Si(rs,t,e),Si(sl,a,e)}}function sT(e,t,a){{var l=e.stateNode,c=t.childContextTypes;if(typeof l.getChildContext!="function"){{var p=lt(e)||"Unknown";yb[p]||(yb[p]=!0,y("%s.childContextTypes is specified but there is no getChildContext() method on the instance. You can either define getChildContext() on %s or remove childContextTypes from it.",p,p))}return a}var v=l.getChildContext();for(var w in v)if(!(w in c))throw new Error((lt(e)||"Unknown")+'.getChildContext(): key "'+w+'" is not defined in childContextTypes.');{var C=lt(e)||"Unknown";Co(c,v,"child context",C)}return gt({},a,v)}}function Yv(e){{var t=e.stateNode,a=t&&t.__reactInternalMemoizedMergedChildContext||ka;return xb=rs.current,Si(rs,a,e),Si(sl,sl.current,e),!0}}function uT(e,t,a){{var l=e.stateNode;if(!l)throw new Error("Expected to have an instance by this point. This error is likely caused by a bug in React. Please file an issue.");if(a){var c=sT(e,t,xb);l.__reactInternalMemoizedMergedChildContext=c,wi(sl,e),wi(rs,e),Si(rs,c,e),Si(sl,a,e)}else wi(sl,e),Si(sl,a,e)}}function LN(e){{if(!Um(e)||e.tag!==$)throw new Error("Expected subtree parent to be a mounted class component. This error is likely caused by a bug in React. Please file an issue.");var t=e;do{switch(t.tag){case j:return t.stateNode.context;case $:{var a=t.type;if(ul(a))return t.stateNode.__reactInternalMemoizedMergedChildContext;break}}t=t.return}while(t!==null);throw new Error("Found unexpected detached subtree parent. This error is likely caused by a bug in React. Please file an issue.")}}var fu=0,Gv=1,is=null,wb=!1,Sb=!1;function cT(e){is===null?is=[e]:is.push(e)}function zN(e){wb=!0,cT(e)}function dT(){wb&&pu()}function pu(){if(!Sb&&is!==null){Sb=!0;var e=0,t=_i();try{var a=!0,l=is;for(ir(xi);e<l.length;e++){var c=l[e];do c=c(a);while(c!==null)}is=null,wb=!1}catch(p){throw is!==null&&(is=is.slice(e+1)),Bp(xo,pu),p}finally{ir(t),Sb=!1}}return null}var Nf=[],Pf=0,Kv=null,Qv=0,Ka=[],Qa=0,Fc=null,as=1,os="";function NN(e){return Uc(),(e.flags&ac)!==Ye}function PN(e){return Uc(),Qv}function FN(){var e=os,t=as,a=t&~IN(t);return a.toString(32)+e}function Ic(e,t){Uc(),Nf[Pf++]=Qv,Nf[Pf++]=Kv,Kv=e,Qv=t}function fT(e,t,a){Uc(),Ka[Qa++]=as,Ka[Qa++]=os,Ka[Qa++]=Fc,Fc=e;var l=as,c=os,p=qv(l)-1,v=l&~(1<<p),w=a+1,C=qv(t)+p;if(C>30){var R=p-p%5,M=(1<<R)-1,U=(v&M).toString(32),F=v>>R,X=p-R,Z=qv(t)+X,te=w<<X,Re=te|F,Xe=U+c;as=1<<Z|Re,os=Xe}else{var We=w<<p,It=We|v,_t=c;as=1<<C|It,os=_t}}function Cb(e){Uc();var t=e.return;if(t!==null){var a=1,l=0;Ic(e,a),fT(e,a,l)}}function qv(e){return 32-rr(e)}function IN(e){return 1<<qv(e)-1}function Eb(e){for(;e===Kv;)Kv=Nf[--Pf],Nf[Pf]=null,Qv=Nf[--Pf],Nf[Pf]=null;for(;e===Fc;)Fc=Ka[--Qa],Ka[Qa]=null,os=Ka[--Qa],Ka[Qa]=null,as=Ka[--Qa],Ka[Qa]=null}function UN(){return Uc(),Fc!==null?{id:as,overflow:os}:null}function BN(e,t){Uc(),Ka[Qa++]=as,Ka[Qa++]=os,Ka[Qa++]=Fc,as=t.id,os=t.overflow,Fc=e}function Uc(){Xr()||y("Expected to be hydrating. This is a bug in React. Please file an issue.")}var qr=null,qa=null,Eo=!1,Bc=!1,hu=null;function HN(){Eo&&y("We should not be hydrating here. This is a bug in React. Please file a bug.")}function pT(){Bc=!0}function VN(){return Bc}function WN(e){var t=e.stateNode.containerInfo;return qa=uN(t),qr=e,Eo=!0,hu=null,Bc=!1,!0}function YN(e,t,a){return qa=cN(t),qr=e,Eo=!0,hu=null,Bc=!1,a!==null&&BN(e,a),!0}function hT(e,t){switch(e.tag){case j:{bN(e.stateNode.containerInfo,t);break}case L:{var a=(e.mode&Dt)!==Ge;SN(e.type,e.memoizedProps,e.stateNode,t,a);break}case le:{var l=e.memoizedState;l.dehydrated!==null&&wN(l.dehydrated,t);break}}}function gT(e,t){hT(e,t);var a=q5();a.stateNode=t,a.return=e;var l=e.deletions;l===null?(e.deletions=[a],e.flags|=fi):l.push(a)}function Tb(e,t){{if(Bc)return;switch(e.tag){case j:{var a=e.stateNode.containerInfo;switch(t.tag){case L:var l=t.type;t.pendingProps,CN(a,l);break;case Q:var c=t.pendingProps;EN(a,c);break}break}case L:{var p=e.type,v=e.memoizedProps,w=e.stateNode;switch(t.tag){case L:{var C=t.type,R=t.pendingProps,M=(e.mode&Dt)!==Ge;RN(p,v,w,C,R,M);break}case Q:{var U=t.pendingProps,F=(e.mode&Dt)!==Ge;DN(p,v,w,U,F);break}}break}case le:{var X=e.memoizedState,Z=X.dehydrated;if(Z!==null)switch(t.tag){case L:var te=t.type;t.pendingProps,TN(Z,te);break;case Q:var Re=t.pendingProps;kN(Z,Re);break}break}default:return}}}function mT(e,t){t.flags=t.flags&~zn|Ln,Tb(e,t)}function vT(e,t){switch(e.tag){case L:{var a=e.type;e.pendingProps;var l=rN(t,a);return l!==null?(e.stateNode=l,qr=e,qa=sN(l),!0):!1}case Q:{var c=e.pendingProps,p=iN(t,c);return p!==null?(e.stateNode=p,qr=e,qa=null,!0):!1}case le:{var v=aN(t);if(v!==null){var w={dehydrated:v,treeContext:UN(),retryLane:mi};e.memoizedState=w;var C=X5(v);return C.return=e,e.child=C,qr=e,qa=null,!0}return!1}default:return!1}}function kb(e){return(e.mode&Dt)!==Ge&&(e.flags&Et)===Ye}function Rb(e){throw new Error("Hydration failed because the initial UI does not match what was rendered on the server.")}function Db(e){if(Eo){var t=qa;if(!t){kb(e)&&(Tb(qr,e),Rb()),mT(qr,e),Eo=!1,qr=e;return}var a=t;if(!vT(e,t)){kb(e)&&(Tb(qr,e),Rb()),t=Lh(a);var l=qr;if(!t||!vT(e,t)){mT(qr,e),Eo=!1,qr=e;return}gT(l,a)}}}function GN(e,t,a){var l=e.stateNode,c=!Bc,p=dN(l,e.type,e.memoizedProps,t,a,e,c);return e.updateQueue=p,p!==null}function KN(e){var t=e.stateNode,a=e.memoizedProps,l=fN(t,a,e);if(l){var c=qr;if(c!==null)switch(c.tag){case j:{var p=c.stateNode.containerInfo,v=(c.mode&Dt)!==Ge;yN(p,t,a,v);break}case L:{var w=c.type,C=c.memoizedProps,R=c.stateNode,M=(c.mode&Dt)!==Ge;xN(w,C,R,t,a,M);break}}}return l}function QN(e){var t=e.memoizedState,a=t!==null?t.dehydrated:null;if(!a)throw new Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");pN(a,e)}function qN(e){var t=e.memoizedState,a=t!==null?t.dehydrated:null;if(!a)throw new Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");return hN(a)}function yT(e){for(var t=e.return;t!==null&&t.tag!==L&&t.tag!==j&&t.tag!==le;)t=t.return;qr=t}function Xv(e){if(e!==qr)return!1;if(!Eo)return yT(e),Eo=!0,!1;if(e.tag!==j&&(e.tag!==L||vN(e.type)&&!ub(e.type,e.memoizedProps))){var t=qa;if(t)if(kb(e))xT(e),Rb();else for(;t;)gT(e,t),t=Lh(t)}return yT(e),e.tag===le?qa=qN(e):qa=qr?Lh(e.stateNode):null,!0}function XN(){return Eo&&qa!==null}function xT(e){for(var t=qa;t;)hT(e,t),t=Lh(t)}function Ff(){qr=null,qa=null,Eo=!1,Bc=!1}function bT(){hu!==null&&(pR(hu),hu=null)}function Xr(){return Eo}function Mb(e){hu===null?hu=[e]:hu.push(e)}var JN=d.ReactCurrentBatchConfig,ZN=null;function eP(){return JN.transition}var To={recordUnsafeLifecycleWarnings:function(e,t){},flushPendingUnsafeLifecycleWarnings:function(){},recordLegacyContextWarning:function(e,t){},flushLegacyContextWarning:function(){},discardPendingWarnings:function(){}};{var tP=function(e){for(var t=null,a=e;a!==null;)a.mode&mt&&(t=a),a=a.return;return t},Hc=function(e){var t=[];return e.forEach(function(a){t.push(a)}),t.sort().join(", ")},Fh=[],Ih=[],Uh=[],Bh=[],Hh=[],Vh=[],Vc=new Set;To.recordUnsafeLifecycleWarnings=function(e,t){Vc.has(e.type)||(typeof t.componentWillMount=="function"&&t.componentWillMount.__suppressDeprecationWarning!==!0&&Fh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillMount=="function"&&Ih.push(e),typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps.__suppressDeprecationWarning!==!0&&Uh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillReceiveProps=="function"&&Bh.push(e),typeof t.componentWillUpdate=="function"&&t.componentWillUpdate.__suppressDeprecationWarning!==!0&&Hh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillUpdate=="function"&&Vh.push(e))},To.flushPendingUnsafeLifecycleWarnings=function(){var e=new Set;Fh.length>0&&(Fh.forEach(function(F){e.add(lt(F)||"Component"),Vc.add(F.type)}),Fh=[]);var t=new Set;Ih.length>0&&(Ih.forEach(function(F){t.add(lt(F)||"Component"),Vc.add(F.type)}),Ih=[]);var a=new Set;Uh.length>0&&(Uh.forEach(function(F){a.add(lt(F)||"Component"),Vc.add(F.type)}),Uh=[]);var l=new Set;Bh.length>0&&(Bh.forEach(function(F){l.add(lt(F)||"Component"),Vc.add(F.type)}),Bh=[]);var c=new Set;Hh.length>0&&(Hh.forEach(function(F){c.add(lt(F)||"Component"),Vc.add(F.type)}),Hh=[]);var p=new Set;if(Vh.length>0&&(Vh.forEach(function(F){p.add(lt(F)||"Component"),Vc.add(F.type)}),Vh=[]),t.size>0){var v=Hc(t);y(`Using UNSAFE_componentWillMount in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.

Please update the following components: %s`,v)}if(l.size>0){var w=Hc(l);y(`Using UNSAFE_componentWillReceiveProps in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://reactjs.org/link/derived-state

Please update the following components: %s`,w)}if(p.size>0){var C=Hc(p);y(`Using UNSAFE_componentWillUpdate in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.

Please update the following components: %s`,C)}if(e.size>0){var R=Hc(e);S(`componentWillMount has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.
* Rename componentWillMount to UNSAFE_componentWillMount to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,R)}if(a.size>0){var M=Hc(a);S(`componentWillReceiveProps has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://reactjs.org/link/derived-state
* Rename componentWillReceiveProps to UNSAFE_componentWillReceiveProps to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,M)}if(c.size>0){var U=Hc(c);S(`componentWillUpdate has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* Rename componentWillUpdate to UNSAFE_componentWillUpdate to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,U)}};var Jv=new Map,wT=new Set;To.recordLegacyContextWarning=function(e,t){var a=tP(e);if(a===null){y("Expected to find a StrictMode component in a strict mode tree. This error is likely caused by a bug in React. Please file an issue.");return}if(!wT.has(e.type)){var l=Jv.get(a);(e.type.contextTypes!=null||e.type.childContextTypes!=null||t!==null&&typeof t.getChildContext=="function")&&(l===void 0&&(l=[],Jv.set(a,l)),l.push(e))}},To.flushLegacyContextWarning=function(){Jv.forEach(function(e,t){if(e.length!==0){var a=e[0],l=new Set;e.forEach(function(p){l.add(lt(p)||"Component"),wT.add(p.type)});var c=Hc(l);try{ln(a),y(`Legacy context API has been detected within a strict-mode tree.

The old API will be supported in all 16.x releases, but applications using it should migrate to the new version.

Please update the following components: %s

Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)}finally{_n()}}})},To.discardPendingWarnings=function(){Fh=[],Ih=[],Uh=[],Bh=[],Hh=[],Vh=[],Jv=new Map}}var Ob,$b,Ab,jb,_b,ST=function(e,t){};Ob=!1,$b=!1,Ab={},jb={},_b={},ST=function(e,t){if(!(e===null||typeof e!="object")&&!(!e._store||e._store.validated||e.key!=null)){if(typeof e._store!="object")throw new Error("React Component in warnForMissingKey should have a _store. This error is likely caused by a bug in React. Please file an issue.");e._store.validated=!0;var a=lt(t)||"Component";jb[a]||(jb[a]=!0,y('Each child in a list should have a unique "key" prop. See https://reactjs.org/link/warning-keys for more information.'))}};function nP(e){return e.prototype&&e.prototype.isReactComponent}function Wh(e,t,a){var l=a.ref;if(l!==null&&typeof l!="function"&&typeof l!="object"){if((e.mode&mt||Ve)&&!(a._owner&&a._self&&a._owner.stateNode!==a._self)&&!(a._owner&&a._owner.tag!==$)&&!(typeof a.type=="function"&&!nP(a.type))&&a._owner){var c=lt(e)||"Component";Ab[c]||(y('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. We recommend using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',c,l),Ab[c]=!0)}if(a._owner){var p=a._owner,v;if(p){var w=p;if(w.tag!==$)throw new Error("Function components cannot have string refs. We recommend using useRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref");v=w.stateNode}if(!v)throw new Error("Missing owner for string ref "+l+". This error is likely caused by a bug in React. Please file an issue.");var C=v;ha(l,"ref");var R=""+l;if(t!==null&&t.ref!==null&&typeof t.ref=="function"&&t.ref._stringRef===R)return t.ref;var M=function(U){var F=C.refs;U===null?delete F[R]:F[R]=U};return M._stringRef=R,M}else{if(typeof l!="string")throw new Error("Expected ref to be a function, a string, an object returned by React.createRef(), or null.");if(!a._owner)throw new Error("Element ref was specified as a string ("+l+`) but no owner was set. This could happen for one of the following reasons:
1. You may be adding a ref to a function component
2. You may be adding a ref to a component that was not created inside a component's render method
3. You have multiple copies of React loaded
See https://reactjs.org/link/refs-must-have-owner for more information.`)}}return l}function Zv(e,t){var a=Object.prototype.toString.call(t);throw new Error("Objects are not valid as a React child (found: "+(a==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":a)+"). If you meant to render a collection of children, use an array instead.")}function ey(e){{var t=lt(e)||"Component";if(_b[t])return;_b[t]=!0,y("Functions are not valid as a React child. This may happen if you return a Component instead of <Component /> from render. Or maybe you meant to call this function rather than return it.")}}function CT(e){var t=e._payload,a=e._init;return a(t)}function ET(e){function t(Y,ne){if(e){var G=Y.deletions;G===null?(Y.deletions=[ne],Y.flags|=fi):G.push(ne)}}function a(Y,ne){if(!e)return null;for(var G=ne;G!==null;)t(Y,G),G=G.sibling;return null}function l(Y,ne){for(var G=new Map,me=ne;me!==null;)me.key!==null?G.set(me.key,me):G.set(me.index,me),me=me.sibling;return G}function c(Y,ne){var G=Zc(Y,ne);return G.index=0,G.sibling=null,G}function p(Y,ne,G){if(Y.index=G,!e)return Y.flags|=ac,ne;var me=Y.alternate;if(me!==null){var Le=me.index;return Le<ne?(Y.flags|=Ln,ne):Le}else return Y.flags|=Ln,ne}function v(Y){return e&&Y.alternate===null&&(Y.flags|=Ln),Y}function w(Y,ne,G,me){if(ne===null||ne.tag!==Q){var Le=OS(G,Y.mode,me);return Le.return=Y,Le}else{var Oe=c(ne,G);return Oe.return=Y,Oe}}function C(Y,ne,G,me){var Le=G.type;if(Le===li)return M(Y,ne,G.props.children,me,G.key);if(ne!==null&&(ne.elementType===Le||MR(ne,G)||typeof Le=="object"&&Le!==null&&Le.$$typeof===ut&&CT(Le)===ne.type)){var Oe=c(ne,G.props);return Oe.ref=Wh(Y,ne,G),Oe.return=Y,Oe._debugSource=G._source,Oe._debugOwner=G._owner,Oe}var ot=MS(G,Y.mode,me);return ot.ref=Wh(Y,ne,G),ot.return=Y,ot}function R(Y,ne,G,me){if(ne===null||ne.tag!==H||ne.stateNode.containerInfo!==G.containerInfo||ne.stateNode.implementation!==G.implementation){var Le=$S(G,Y.mode,me);return Le.return=Y,Le}else{var Oe=c(ne,G.children||[]);return Oe.return=Y,Oe}}function M(Y,ne,G,me,Le){if(ne===null||ne.tag!==xe){var Oe=Tu(G,Y.mode,me,Le);return Oe.return=Y,Oe}else{var ot=c(ne,G);return ot.return=Y,ot}}function U(Y,ne,G){if(typeof ne=="string"&&ne!==""||typeof ne=="number"){var me=OS(""+ne,Y.mode,G);return me.return=Y,me}if(typeof ne=="object"&&ne!==null){switch(ne.$$typeof){case br:{var Le=MS(ne,Y.mode,G);return Le.ref=Wh(Y,null,ne),Le.return=Y,Le}case $i:{var Oe=$S(ne,Y.mode,G);return Oe.return=Y,Oe}case ut:{var ot=ne._payload,dt=ne._init;return U(Y,dt(ot),G)}}if(yt(ne)||kn(ne)){var fn=Tu(ne,Y.mode,G,null);return fn.return=Y,fn}Zv(Y,ne)}return typeof ne=="function"&&ey(Y),null}function F(Y,ne,G,me){var Le=ne!==null?ne.key:null;if(typeof G=="string"&&G!==""||typeof G=="number")return Le!==null?null:w(Y,ne,""+G,me);if(typeof G=="object"&&G!==null){switch(G.$$typeof){case br:return G.key===Le?C(Y,ne,G,me):null;case $i:return G.key===Le?R(Y,ne,G,me):null;case ut:{var Oe=G._payload,ot=G._init;return F(Y,ne,ot(Oe),me)}}if(yt(G)||kn(G))return Le!==null?null:M(Y,ne,G,me,null);Zv(Y,G)}return typeof G=="function"&&ey(Y),null}function X(Y,ne,G,me,Le){if(typeof me=="string"&&me!==""||typeof me=="number"){var Oe=Y.get(G)||null;return w(ne,Oe,""+me,Le)}if(typeof me=="object"&&me!==null){switch(me.$$typeof){case br:{var ot=Y.get(me.key===null?G:me.key)||null;return C(ne,ot,me,Le)}case $i:{var dt=Y.get(me.key===null?G:me.key)||null;return R(ne,dt,me,Le)}case ut:var fn=me._payload,Wt=me._init;return X(Y,ne,G,Wt(fn),Le)}if(yt(me)||kn(me)){var lr=Y.get(G)||null;return M(ne,lr,me,Le,null)}Zv(ne,me)}return typeof me=="function"&&ey(ne),null}function Z(Y,ne,G){{if(typeof Y!="object"||Y===null)return ne;switch(Y.$$typeof){case br:case $i:ST(Y,G);var me=Y.key;if(typeof me!="string")break;if(ne===null){ne=new Set,ne.add(me);break}if(!ne.has(me)){ne.add(me);break}y("Encountered two children with the same key, `%s`. Keys should be unique so that components maintain their identity across updates. Non-unique keys may cause children to be duplicated and/or omitted — the behavior is unsupported and could change in a future version.",me);break;case ut:var Le=Y._payload,Oe=Y._init;Z(Oe(Le),ne,G);break}}return ne}function te(Y,ne,G,me){for(var Le=null,Oe=0;Oe<G.length;Oe++){var ot=G[Oe];Le=Z(ot,Le,Y)}for(var dt=null,fn=null,Wt=ne,lr=0,Yt=0,er=null;Wt!==null&&Yt<G.length;Yt++){Wt.index>Yt?(er=Wt,Wt=null):er=Wt.sibling;var Ei=F(Y,Wt,G[Yt],me);if(Ei===null){Wt===null&&(Wt=er);break}e&&Wt&&Ei.alternate===null&&t(Y,Wt),lr=p(Ei,lr,Yt),fn===null?dt=Ei:fn.sibling=Ei,fn=Ei,Wt=er}if(Yt===G.length){if(a(Y,Wt),Xr()){var ii=Yt;Ic(Y,ii)}return dt}if(Wt===null){for(;Yt<G.length;Yt++){var Da=U(Y,G[Yt],me);Da!==null&&(lr=p(Da,lr,Yt),fn===null?dt=Da:fn.sibling=Da,fn=Da)}if(Xr()){var Ui=Yt;Ic(Y,Ui)}return dt}for(var Bi=l(Y,Wt);Yt<G.length;Yt++){var Ti=X(Bi,Y,Yt,G[Yt],me);Ti!==null&&(e&&Ti.alternate!==null&&Bi.delete(Ti.key===null?Yt:Ti.key),lr=p(Ti,lr,Yt),fn===null?dt=Ti:fn.sibling=Ti,fn=Ti)}if(e&&Bi.forEach(function(ip){return t(Y,ip)}),Xr()){var ps=Yt;Ic(Y,ps)}return dt}function Re(Y,ne,G,me){var Le=kn(G);if(typeof Le!="function")throw new Error("An object is not an iterable. This error is likely caused by a bug in React. Please file an issue.");{typeof Symbol=="function"&&G[Symbol.toStringTag]==="Generator"&&($b||y("Using Generators as children is unsupported and will likely yield unexpected results because enumerating a generator mutates it. You may convert it to an array with `Array.from()` or the `[...spread]` operator before rendering. Keep in mind you might need to polyfill these features for older browsers."),$b=!0),G.entries===Le&&(Ob||y("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),Ob=!0);var Oe=Le.call(G);if(Oe)for(var ot=null,dt=Oe.next();!dt.done;dt=Oe.next()){var fn=dt.value;ot=Z(fn,ot,Y)}}var Wt=Le.call(G);if(Wt==null)throw new Error("An iterable object provided no iterator.");for(var lr=null,Yt=null,er=ne,Ei=0,ii=0,Da=null,Ui=Wt.next();er!==null&&!Ui.done;ii++,Ui=Wt.next()){er.index>ii?(Da=er,er=null):Da=er.sibling;var Bi=F(Y,er,Ui.value,me);if(Bi===null){er===null&&(er=Da);break}e&&er&&Bi.alternate===null&&t(Y,er),Ei=p(Bi,Ei,ii),Yt===null?lr=Bi:Yt.sibling=Bi,Yt=Bi,er=Da}if(Ui.done){if(a(Y,er),Xr()){var Ti=ii;Ic(Y,Ti)}return lr}if(er===null){for(;!Ui.done;ii++,Ui=Wt.next()){var ps=U(Y,Ui.value,me);ps!==null&&(Ei=p(ps,Ei,ii),Yt===null?lr=ps:Yt.sibling=ps,Yt=ps)}if(Xr()){var ip=ii;Ic(Y,ip)}return lr}for(var Cg=l(Y,er);!Ui.done;ii++,Ui=Wt.next()){var vl=X(Cg,Y,ii,Ui.value,me);vl!==null&&(e&&vl.alternate!==null&&Cg.delete(vl.key===null?ii:vl.key),Ei=p(vl,Ei,ii),Yt===null?lr=vl:Yt.sibling=vl,Yt=vl)}if(e&&Cg.forEach(function(DF){return t(Y,DF)}),Xr()){var RF=ii;Ic(Y,RF)}return lr}function Xe(Y,ne,G,me){if(ne!==null&&ne.tag===Q){a(Y,ne.sibling);var Le=c(ne,G);return Le.return=Y,Le}a(Y,ne);var Oe=OS(G,Y.mode,me);return Oe.return=Y,Oe}function We(Y,ne,G,me){for(var Le=G.key,Oe=ne;Oe!==null;){if(Oe.key===Le){var ot=G.type;if(ot===li){if(Oe.tag===xe){a(Y,Oe.sibling);var dt=c(Oe,G.props.children);return dt.return=Y,dt._debugSource=G._source,dt._debugOwner=G._owner,dt}}else if(Oe.elementType===ot||MR(Oe,G)||typeof ot=="object"&&ot!==null&&ot.$$typeof===ut&&CT(ot)===Oe.type){a(Y,Oe.sibling);var fn=c(Oe,G.props);return fn.ref=Wh(Y,Oe,G),fn.return=Y,fn._debugSource=G._source,fn._debugOwner=G._owner,fn}a(Y,Oe);break}else t(Y,Oe);Oe=Oe.sibling}if(G.type===li){var Wt=Tu(G.props.children,Y.mode,me,G.key);return Wt.return=Y,Wt}else{var lr=MS(G,Y.mode,me);return lr.ref=Wh(Y,ne,G),lr.return=Y,lr}}function It(Y,ne,G,me){for(var Le=G.key,Oe=ne;Oe!==null;){if(Oe.key===Le)if(Oe.tag===H&&Oe.stateNode.containerInfo===G.containerInfo&&Oe.stateNode.implementation===G.implementation){a(Y,Oe.sibling);var ot=c(Oe,G.children||[]);return ot.return=Y,ot}else{a(Y,Oe);break}else t(Y,Oe);Oe=Oe.sibling}var dt=$S(G,Y.mode,me);return dt.return=Y,dt}function _t(Y,ne,G,me){var Le=typeof G=="object"&&G!==null&&G.type===li&&G.key===null;if(Le&&(G=G.props.children),typeof G=="object"&&G!==null){switch(G.$$typeof){case br:return v(We(Y,ne,G,me));case $i:return v(It(Y,ne,G,me));case ut:var Oe=G._payload,ot=G._init;return _t(Y,ne,ot(Oe),me)}if(yt(G))return te(Y,ne,G,me);if(kn(G))return Re(Y,ne,G,me);Zv(Y,G)}return typeof G=="string"&&G!==""||typeof G=="number"?v(Xe(Y,ne,""+G,me)):(typeof G=="function"&&ey(Y),a(Y,ne))}return _t}var If=ET(!0),TT=ET(!1);function rP(e,t){if(e!==null&&t.child!==e.child)throw new Error("Resuming work not yet implemented.");if(t.child!==null){var a=t.child,l=Zc(a,a.pendingProps);for(t.child=l,l.return=t;a.sibling!==null;)a=a.sibling,l=l.sibling=Zc(a,a.pendingProps),l.return=t;l.sibling=null}}function iP(e,t){for(var a=e.child;a!==null;)W5(a,t),a=a.sibling}var Lb=du(null),zb;zb={};var ty=null,Uf=null,Nb=null,ny=!1;function ry(){ty=null,Uf=null,Nb=null,ny=!1}function kT(){ny=!0}function RT(){ny=!1}function DT(e,t,a){Si(Lb,t._currentValue,e),t._currentValue=a,t._currentRenderer!==void 0&&t._currentRenderer!==null&&t._currentRenderer!==zb&&y("Detected multiple renderers concurrently rendering the same context provider. This is currently unsupported."),t._currentRenderer=zb}function Pb(e,t){var a=Lb.current;wi(Lb,t),e._currentValue=a}function Fb(e,t,a){for(var l=e;l!==null;){var c=l.alternate;if(Wl(l.childLanes,t)?c!==null&&!Wl(c.childLanes,t)&&(c.childLanes=xt(c.childLanes,t)):(l.childLanes=xt(l.childLanes,t),c!==null&&(c.childLanes=xt(c.childLanes,t))),l===a)break;l=l.return}l!==a&&y("Expected to find the propagation root when scheduling context work. This error is likely caused by a bug in React. Please file an issue.")}function aP(e,t,a){oP(e,t,a)}function oP(e,t,a){var l=e.child;for(l!==null&&(l.return=e);l!==null;){var c=void 0,p=l.dependencies;if(p!==null){c=l.child;for(var v=p.firstContext;v!==null;){if(v.context===t){if(l.tag===$){var w=gr(a),C=ls(rn,w);C.tag=ay;var R=l.updateQueue;if(R!==null){var M=R.shared,U=M.pending;U===null?C.next=C:(C.next=U.next,U.next=C),M.pending=C}}l.lanes=xt(l.lanes,a);var F=l.alternate;F!==null&&(F.lanes=xt(F.lanes,a)),Fb(l.return,a,e),p.lanes=xt(p.lanes,a);break}v=v.next}}else if(l.tag===ae)c=l.type===e.type?null:l.child;else if(l.tag===Tt){var X=l.return;if(X===null)throw new Error("We just came from a parent so we must have had a parent. This is a bug in React.");X.lanes=xt(X.lanes,a);var Z=X.alternate;Z!==null&&(Z.lanes=xt(Z.lanes,a)),Fb(X,a,e),c=l.sibling}else c=l.child;if(c!==null)c.return=l;else for(c=l;c!==null;){if(c===e){c=null;break}var te=c.sibling;if(te!==null){te.return=c.return,c=te;break}c=c.return}l=c}}function Bf(e,t){ty=e,Uf=null,Nb=null;var a=e.dependencies;if(a!==null){var l=a.firstContext;l!==null&&(yi(a.lanes,t)&&og(),a.firstContext=null)}}function vr(e){ny&&y("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");var t=e._currentValue;if(Nb!==e){var a={context:e,memoizedValue:t,next:null};if(Uf===null){if(ty===null)throw new Error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");Uf=a,ty.dependencies={lanes:ie,firstContext:a}}else Uf=Uf.next=a}return t}var Wc=null;function Ib(e){Wc===null?Wc=[e]:Wc.push(e)}function lP(){if(Wc!==null){for(var e=0;e<Wc.length;e++){var t=Wc[e],a=t.interleaved;if(a!==null){t.interleaved=null;var l=a.next,c=t.pending;if(c!==null){var p=c.next;c.next=l,a.next=p}t.pending=a}}Wc=null}}function MT(e,t,a,l){var c=t.interleaved;return c===null?(a.next=a,Ib(t)):(a.next=c.next,c.next=a),t.interleaved=a,iy(e,l)}function sP(e,t,a,l){var c=t.interleaved;c===null?(a.next=a,Ib(t)):(a.next=c.next,c.next=a),t.interleaved=a}function uP(e,t,a,l){var c=t.interleaved;return c===null?(a.next=a,Ib(t)):(a.next=c.next,c.next=a),t.interleaved=a,iy(e,l)}function ca(e,t){return iy(e,t)}var cP=iy;function iy(e,t){e.lanes=xt(e.lanes,t);var a=e.alternate;a!==null&&(a.lanes=xt(a.lanes,t)),a===null&&(e.flags&(Ln|zn))!==Ye&&TR(e);for(var l=e,c=e.return;c!==null;)c.childLanes=xt(c.childLanes,t),a=c.alternate,a!==null?a.childLanes=xt(a.childLanes,t):(c.flags&(Ln|zn))!==Ye&&TR(e),l=c,c=c.return;if(l.tag===j){var p=l.stateNode;return p}else return null}var OT=0,$T=1,ay=2,Ub=3,oy=!1,Bb,ly;Bb=!1,ly=null;function Hb(e){var t={baseState:e.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:ie},effects:null};e.updateQueue=t}function AT(e,t){var a=t.updateQueue,l=e.updateQueue;if(a===l){var c={baseState:l.baseState,firstBaseUpdate:l.firstBaseUpdate,lastBaseUpdate:l.lastBaseUpdate,shared:l.shared,effects:l.effects};t.updateQueue=c}}function ls(e,t){var a={eventTime:e,lane:t,tag:OT,payload:null,callback:null,next:null};return a}function gu(e,t,a){var l=e.updateQueue;if(l===null)return null;var c=l.shared;if(ly===c&&!Bb&&(y("An update (setState, replaceState, or forceUpdate) was scheduled from inside an update function. Update functions should be pure, with zero side-effects. Consider using componentDidUpdate or a callback."),Bb=!0),s5()){var p=c.pending;return p===null?t.next=t:(t.next=p.next,p.next=t),c.pending=t,cP(e,a)}else return uP(e,c,t,a)}function sy(e,t,a){var l=t.updateQueue;if(l!==null){var c=l.shared;if(ah(a)){var p=c.lanes;p=lf(p,e.pendingLanes);var v=xt(p,a);c.lanes=v,wc(e,v)}}}function Vb(e,t){var a=e.updateQueue,l=e.alternate;if(l!==null){var c=l.updateQueue;if(a===c){var p=null,v=null,w=a.firstBaseUpdate;if(w!==null){var C=w;do{var R={eventTime:C.eventTime,lane:C.lane,tag:C.tag,payload:C.payload,callback:C.callback,next:null};v===null?p=v=R:(v.next=R,v=R),C=C.next}while(C!==null);v===null?p=v=t:(v.next=t,v=t)}else p=v=t;a={baseState:c.baseState,firstBaseUpdate:p,lastBaseUpdate:v,shared:c.shared,effects:c.effects},e.updateQueue=a;return}}var M=a.lastBaseUpdate;M===null?a.firstBaseUpdate=t:M.next=t,a.lastBaseUpdate=t}function dP(e,t,a,l,c,p){switch(a.tag){case $T:{var v=a.payload;if(typeof v=="function"){kT();var w=v.call(p,l,c);{if(e.mode&mt){nn(!0);try{v.call(p,l,c)}finally{nn(!1)}}RT()}return w}return v}case Ub:e.flags=e.flags&~Pr|Et;case OT:{var C=a.payload,R;if(typeof C=="function"){kT(),R=C.call(p,l,c);{if(e.mode&mt){nn(!0);try{C.call(p,l,c)}finally{nn(!1)}}RT()}}else R=C;return R==null?l:gt({},l,R)}case ay:return oy=!0,l}return l}function uy(e,t,a,l){var c=e.updateQueue;oy=!1,ly=c.shared;var p=c.firstBaseUpdate,v=c.lastBaseUpdate,w=c.shared.pending;if(w!==null){c.shared.pending=null;var C=w,R=C.next;C.next=null,v===null?p=R:v.next=R,v=C;var M=e.alternate;if(M!==null){var U=M.updateQueue,F=U.lastBaseUpdate;F!==v&&(F===null?U.firstBaseUpdate=R:F.next=R,U.lastBaseUpdate=C)}}if(p!==null){var X=c.baseState,Z=ie,te=null,Re=null,Xe=null,We=p;do{var It=We.lane,_t=We.eventTime;if(Wl(l,It)){if(Xe!==null){var ne={eventTime:_t,lane:Jn,tag:We.tag,payload:We.payload,callback:We.callback,next:null};Xe=Xe.next=ne}X=dP(e,c,We,X,t,a);var G=We.callback;if(G!==null&&We.lane!==Jn){e.flags|=gn;var me=c.effects;me===null?c.effects=[We]:me.push(We)}}else{var Y={eventTime:_t,lane:It,tag:We.tag,payload:We.payload,callback:We.callback,next:null};Xe===null?(Re=Xe=Y,te=X):Xe=Xe.next=Y,Z=xt(Z,It)}if(We=We.next,We===null){if(w=c.shared.pending,w===null)break;var Le=w,Oe=Le.next;Le.next=null,We=Oe,c.lastBaseUpdate=Le,c.shared.pending=null}}while(!0);Xe===null&&(te=X),c.baseState=te,c.firstBaseUpdate=Re,c.lastBaseUpdate=Xe;var ot=c.shared.interleaved;if(ot!==null){var dt=ot;do Z=xt(Z,dt.lane),dt=dt.next;while(dt!==ot)}else p===null&&(c.shared.lanes=ie);yg(Z),e.lanes=Z,e.memoizedState=X}ly=null}function fP(e,t){if(typeof e!="function")throw new Error("Invalid argument passed as callback. Expected a function. Instead "+("received: "+e));e.call(t)}function jT(){oy=!1}function cy(){return oy}function _T(e,t,a){var l=t.effects;if(t.effects=null,l!==null)for(var c=0;c<l.length;c++){var p=l[c],v=p.callback;v!==null&&(p.callback=null,fP(v,a))}}var Yh={},mu=du(Yh),Gh=du(Yh),dy=du(Yh);function fy(e){if(e===Yh)throw new Error("Expected host context to exist. This error is likely caused by a bug in React. Please file an issue.");return e}function LT(){var e=fy(dy.current);return e}function Wb(e,t){Si(dy,t,e),Si(Gh,e,e),Si(mu,Yh,e);var a=Mz(t);wi(mu,e),Si(mu,a,e)}function Hf(e){wi(mu,e),wi(Gh,e),wi(dy,e)}function Yb(){var e=fy(mu.current);return e}function zT(e){fy(dy.current);var t=fy(mu.current),a=Oz(t,e.type);t!==a&&(Si(Gh,e,e),Si(mu,a,e))}function Gb(e){Gh.current===e&&(wi(mu,e),wi(Gh,e))}var pP=0,NT=1,PT=1,Kh=2,ko=du(pP);function Kb(e,t){return(e&t)!==0}function Vf(e){return e&NT}function Qb(e,t){return e&NT|t}function hP(e,t){return e|t}function vu(e,t){Si(ko,t,e)}function Wf(e){wi(ko,e)}function gP(e,t){var a=e.memoizedState;return a!==null?a.dehydrated!==null:(e.memoizedProps,!0)}function py(e){for(var t=e;t!==null;){if(t.tag===le){var a=t.memoizedState;if(a!==null){var l=a.dehydrated;if(l===null||tT(l)||pb(l))return t}}else if(t.tag===bt&&t.memoizedProps.revealOrder!==void 0){var c=(t.flags&Et)!==Ye;if(c)return t}else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)return null;for(;t.sibling===null;){if(t.return===null||t.return===e)return null;t=t.return}t.sibling.return=t.return,t=t.sibling}return null}var da=0,kr=1,cl=2,Rr=4,Jr=8,qb=[];function Xb(){for(var e=0;e<qb.length;e++){var t=qb[e];t._workInProgressVersionPrimary=null}qb.length=0}function mP(e,t){var a=t._getVersion,l=a(t._source);e.mutableSourceEagerHydrationData==null?e.mutableSourceEagerHydrationData=[t,l]:e.mutableSourceEagerHydrationData.push(t,l)}var _e=d.ReactCurrentDispatcher,Qh=d.ReactCurrentBatchConfig,Jb,Yf;Jb=new Set;var Yc=ie,dn=null,Dr=null,Mr=null,hy=!1,qh=!1,Xh=0,vP=0,yP=25,oe=null,Xa=null,yu=-1,Zb=!1;function Jt(){{var e=oe;Xa===null?Xa=[e]:Xa.push(e)}}function Se(){{var e=oe;Xa!==null&&(yu++,Xa[yu]!==e&&xP(e))}}function Gf(e){e!=null&&!yt(e)&&y("%s received a final argument that is not an array (instead, received `%s`). When specified, the final argument must be an array.",oe,typeof e)}function xP(e){{var t=lt(dn);if(!Jb.has(t)&&(Jb.add(t),Xa!==null)){for(var a="",l=30,c=0;c<=yu;c++){for(var p=Xa[c],v=c===yu?e:p,w=c+1+". "+p;w.length<l;)w+=" ";w+=v+`
`,a+=w}y(`React has detected a change in the order of Hooks called by %s. This will lead to bugs and errors if not fixed. For more information, read the Rules of Hooks: https://reactjs.org/link/rules-of-hooks

   Previous render            Next render
   ------------------------------------------------------
%s   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
`,t,a)}}}function Ci(){throw new Error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://reactjs.org/link/invalid-hook-call for tips about how to debug and fix this problem.`)}function tw(e,t){if(Zb)return!1;if(t===null)return y("%s received a final argument during this render, but not during the previous render. Even though the final argument is optional, its type cannot change between renders.",oe),!1;e.length!==t.length&&y(`The final argument passed to %s changed size between renders. The order and size of this array must remain constant.

Previous: %s
Incoming: %s`,oe,"["+t.join(", ")+"]","["+e.join(", ")+"]");for(var a=0;a<t.length&&a<e.length;a++)if(!Me(e[a],t[a]))return!1;return!0}function Kf(e,t,a,l,c,p){Yc=p,dn=t,Xa=e!==null?e._debugHookTypes:null,yu=-1,Zb=e!==null&&e.type!==t.type,t.memoizedState=null,t.updateQueue=null,t.lanes=ie,e!==null&&e.memoizedState!==null?_e.current=ok:Xa!==null?_e.current=ak:_e.current=ik;var v=a(l,c);if(qh){var w=0;do{if(qh=!1,Xh=0,w>=yP)throw new Error("Too many re-renders. React limits the number of renders to prevent an infinite loop.");w+=1,Zb=!1,Dr=null,Mr=null,t.updateQueue=null,yu=-1,_e.current=lk,v=a(l,c)}while(qh)}_e.current=Ry,t._debugHookTypes=Xa;var C=Dr!==null&&Dr.next!==null;if(Yc=ie,dn=null,Dr=null,Mr=null,oe=null,Xa=null,yu=-1,e!==null&&(e.flags&Xn)!==(t.flags&Xn)&&(e.mode&Dt)!==Ge&&y("Internal React error: Expected static flag was missing. Please notify the React team."),hy=!1,C)throw new Error("Rendered fewer hooks than expected. This may be caused by an accidental early return statement.");return v}function Qf(){var e=Xh!==0;return Xh=0,e}function FT(e,t,a){t.updateQueue=e.updateQueue,(t.mode&cn)!==Ge?t.flags&=-50333701:t.flags&=-2053,e.lanes=bc(e.lanes,a)}function IT(){if(_e.current=Ry,hy){for(var e=dn.memoizedState;e!==null;){var t=e.queue;t!==null&&(t.pending=null),e=e.next}hy=!1}Yc=ie,dn=null,Dr=null,Mr=null,Xa=null,yu=-1,oe=null,ZT=!1,qh=!1,Xh=0}function dl(){var e={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return Mr===null?dn.memoizedState=Mr=e:Mr=Mr.next=e,Mr}function Ja(){var e;if(Dr===null){var t=dn.alternate;t!==null?e=t.memoizedState:e=null}else e=Dr.next;var a;if(Mr===null?a=dn.memoizedState:a=Mr.next,a!==null)Mr=a,a=Mr.next,Dr=e;else{if(e===null)throw new Error("Rendered more hooks than during the previous render.");Dr=e;var l={memoizedState:Dr.memoizedState,baseState:Dr.baseState,baseQueue:Dr.baseQueue,queue:Dr.queue,next:null};Mr===null?dn.memoizedState=Mr=l:Mr=Mr.next=l}return Mr}function UT(){return{lastEffect:null,stores:null}}function nw(e,t){return typeof t=="function"?t(e):t}function rw(e,t,a){var l=dl(),c;a!==void 0?c=a(t):c=t,l.memoizedState=l.baseState=c;var p={pending:null,interleaved:null,lanes:ie,dispatch:null,lastRenderedReducer:e,lastRenderedState:c};l.queue=p;var v=p.dispatch=CP.bind(null,dn,p);return[l.memoizedState,v]}function iw(e,t,a){var l=Ja(),c=l.queue;if(c===null)throw new Error("Should have a queue. This is likely a bug in React. Please file an issue.");c.lastRenderedReducer=e;var p=Dr,v=p.baseQueue,w=c.pending;if(w!==null){if(v!==null){var C=v.next,R=w.next;v.next=R,w.next=C}p.baseQueue!==v&&y("Internal error: Expected work-in-progress queue to be a clone. This is a bug in React."),p.baseQueue=v=w,c.pending=null}if(v!==null){var M=v.next,U=p.baseState,F=null,X=null,Z=null,te=M;do{var Re=te.lane;if(Wl(Yc,Re)){if(Z!==null){var We={lane:Jn,action:te.action,hasEagerState:te.hasEagerState,eagerState:te.eagerState,next:null};Z=Z.next=We}if(te.hasEagerState)U=te.eagerState;else{var It=te.action;U=e(U,It)}}else{var Xe={lane:Re,action:te.action,hasEagerState:te.hasEagerState,eagerState:te.eagerState,next:null};Z===null?(X=Z=Xe,F=U):Z=Z.next=Xe,dn.lanes=xt(dn.lanes,Re),yg(Re)}te=te.next}while(te!==null&&te!==M);Z===null?F=U:Z.next=X,Me(U,l.memoizedState)||og(),l.memoizedState=U,l.baseState=F,l.baseQueue=Z,c.lastRenderedState=U}var _t=c.interleaved;if(_t!==null){var Y=_t;do{var ne=Y.lane;dn.lanes=xt(dn.lanes,ne),yg(ne),Y=Y.next}while(Y!==_t)}else v===null&&(c.lanes=ie);var G=c.dispatch;return[l.memoizedState,G]}function aw(e,t,a){var l=Ja(),c=l.queue;if(c===null)throw new Error("Should have a queue. This is likely a bug in React. Please file an issue.");c.lastRenderedReducer=e;var p=c.dispatch,v=c.pending,w=l.memoizedState;if(v!==null){c.pending=null;var C=v.next,R=C;do{var M=R.action;w=e(w,M),R=R.next}while(R!==C);Me(w,l.memoizedState)||og(),l.memoizedState=w,l.baseQueue===null&&(l.baseState=w),c.lastRenderedState=w}return[w,p]}function AF(e,t,a){}function jF(e,t,a){}function ow(e,t,a){var l=dn,c=dl(),p,v=Xr();if(v){if(a===void 0)throw new Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");p=a(),Yf||p!==a()&&(y("The result of getServerSnapshot should be cached to avoid an infinite loop"),Yf=!0)}else{if(p=t(),!Yf){var w=t();Me(p,w)||(y("The result of getSnapshot should be cached to avoid an infinite loop"),Yf=!0)}var C=Yy();if(C===null)throw new Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");xc(C,Yc)||BT(l,t,p)}c.memoizedState=p;var R={value:p,getSnapshot:t};return c.queue=R,xy(VT.bind(null,l,R,e),[e]),l.flags|=Ai,Jh(kr|Jr,HT.bind(null,l,R,p,t),void 0,null),p}function gy(e,t,a){var l=dn,c=Ja(),p=t();if(!Yf){var v=t();Me(p,v)||(y("The result of getSnapshot should be cached to avoid an infinite loop"),Yf=!0)}var w=c.memoizedState,C=!Me(w,p);C&&(c.memoizedState=p,og());var R=c.queue;if(eg(VT.bind(null,l,R,e),[e]),R.getSnapshot!==t||C||Mr!==null&&Mr.memoizedState.tag&kr){l.flags|=Ai,Jh(kr|Jr,HT.bind(null,l,R,p,t),void 0,null);var M=Yy();if(M===null)throw new Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");xc(M,Yc)||BT(l,t,p)}return p}function BT(e,t,a){e.flags|=_d;var l={getSnapshot:t,value:a},c=dn.updateQueue;if(c===null)c=UT(),dn.updateQueue=c,c.stores=[l];else{var p=c.stores;p===null?c.stores=[l]:p.push(l)}}function HT(e,t,a,l){t.value=a,t.getSnapshot=l,WT(t)&&YT(e)}function VT(e,t,a){var l=function(){WT(t)&&YT(e)};return a(l)}function WT(e){var t=e.getSnapshot,a=e.value;try{var l=t();return!Me(a,l)}catch{return!0}}function YT(e){var t=ca(e,nt);t!==null&&jr(t,e,nt,rn)}function my(e){var t=dl();typeof e=="function"&&(e=e()),t.memoizedState=t.baseState=e;var a={pending:null,interleaved:null,lanes:ie,dispatch:null,lastRenderedReducer:nw,lastRenderedState:e};t.queue=a;var l=a.dispatch=EP.bind(null,dn,a);return[t.memoizedState,l]}function lw(e){return iw(nw)}function sw(e){return aw(nw)}function Jh(e,t,a,l){var c={tag:e,create:t,destroy:a,deps:l,next:null},p=dn.updateQueue;if(p===null)p=UT(),dn.updateQueue=p,p.lastEffect=c.next=c;else{var v=p.lastEffect;if(v===null)p.lastEffect=c.next=c;else{var w=v.next;v.next=c,c.next=w,p.lastEffect=c}}return c}function uw(e){var t=dl();{var a={current:e};return t.memoizedState=a,a}}function vy(e){var t=Ja();return t.memoizedState}function Zh(e,t,a,l){var c=dl(),p=l===void 0?null:l;dn.flags|=e,c.memoizedState=Jh(kr|t,a,void 0,p)}function yy(e,t,a,l){var c=Ja(),p=l===void 0?null:l,v=void 0;if(Dr!==null){var w=Dr.memoizedState;if(v=w.destroy,p!==null){var C=w.deps;if(tw(p,C)){c.memoizedState=Jh(t,a,v,p);return}}}dn.flags|=e,c.memoizedState=Jh(kr|t,a,v,p)}function xy(e,t){return(dn.mode&cn)!==Ge?Zh(Vo|Ai|Fp,Jr,e,t):Zh(Ai|Fp,Jr,e,t)}function eg(e,t){return yy(Ai,Jr,e,t)}function cw(e,t){return Zh(At,cl,e,t)}function by(e,t){return yy(At,cl,e,t)}function dw(e,t){var a=At;return a|=Ho,(dn.mode&cn)!==Ge&&(a|=Gr),Zh(a,Rr,e,t)}function wy(e,t){return yy(At,Rr,e,t)}function GT(e,t){if(typeof t=="function"){var a=t,l=e();return a(l),function(){a(null)}}else if(t!=null){var c=t;c.hasOwnProperty("current")||y("Expected useImperativeHandle() first argument to either be a ref callback or React.createRef() object. Instead received: %s.","an object with keys {"+Object.keys(c).join(", ")+"}");var p=e();return c.current=p,function(){c.current=null}}}function fw(e,t,a){typeof t!="function"&&y("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null");var l=a!=null?a.concat([e]):null,c=At;return c|=Ho,(dn.mode&cn)!==Ge&&(c|=Gr),Zh(c,Rr,GT.bind(null,t,e),l)}function Sy(e,t,a){typeof t!="function"&&y("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null");var l=a!=null?a.concat([e]):null;return yy(At,Rr,GT.bind(null,t,e),l)}function bP(e,t){}var Cy=bP;function pw(e,t){var a=dl(),l=t===void 0?null:t;return a.memoizedState=[e,l],e}function Ey(e,t){var a=Ja(),l=t===void 0?null:t,c=a.memoizedState;if(c!==null&&l!==null){var p=c[1];if(tw(l,p))return c[0]}return a.memoizedState=[e,l],e}function hw(e,t){var a=dl(),l=t===void 0?null:t,c=e();return a.memoizedState=[c,l],c}function Ty(e,t){var a=Ja(),l=t===void 0?null:t,c=a.memoizedState;if(c!==null&&l!==null){var p=c[1];if(tw(l,p))return c[0]}var v=e();return a.memoizedState=[v,l],v}function gw(e){var t=dl();return t.memoizedState=e,e}function KT(e){var t=Ja(),a=Dr,l=a.memoizedState;return qT(t,l,e)}function QT(e){var t=Ja();if(Dr===null)return t.memoizedState=e,e;var a=Dr.memoizedState;return qT(t,a,e)}function qT(e,t,a){var l=!ih(Yc);if(l){if(!Me(a,t)){var c=oh();dn.lanes=xt(dn.lanes,c),yg(c),e.baseState=!0}return t}else return e.baseState&&(e.baseState=!1,og()),e.memoizedState=a,a}function wP(e,t,a){var l=_i();ir(Sc(l,aa)),e(!0);var c=Qh.transition;Qh.transition={};var p=Qh.transition;Qh.transition._updatedFibers=new Set;try{e(!1),t()}finally{if(ir(l),Qh.transition=c,c===null&&p._updatedFibers){var v=p._updatedFibers.size;v>10&&S("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."),p._updatedFibers.clear()}}}function mw(){var e=my(!1),t=e[0],a=e[1],l=wP.bind(null,a),c=dl();return c.memoizedState=l,[t,l]}function XT(){var e=lw(),t=e[0],a=Ja(),l=a.memoizedState;return[t,l]}function JT(){var e=sw(),t=e[0],a=Ja(),l=a.memoizedState;return[t,l]}var ZT=!1;function SP(){return ZT}function vw(){var e=dl(),t=Yy(),a=t.identifierPrefix,l;if(Xr()){var c=FN();l=":"+a+"R"+c;var p=Xh++;p>0&&(l+="H"+p.toString(32)),l+=":"}else{var v=vP++;l=":"+a+"r"+v.toString(32)+":"}return e.memoizedState=l,l}function ky(){var e=Ja(),t=e.memoizedState;return t}function CP(e,t,a){typeof arguments[3]=="function"&&y("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect().");var l=Cu(e),c={lane:l,action:a,hasEagerState:!1,eagerState:null,next:null};if(ek(e))tk(t,c);else{var p=MT(e,t,c,l);if(p!==null){var v=Ii();jr(p,e,l,v),nk(p,t,l)}}rk(e,l)}function EP(e,t,a){typeof arguments[3]=="function"&&y("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect().");var l=Cu(e),c={lane:l,action:a,hasEagerState:!1,eagerState:null,next:null};if(ek(e))tk(t,c);else{var p=e.alternate;if(e.lanes===ie&&(p===null||p.lanes===ie)){var v=t.lastRenderedReducer;if(v!==null){var w;w=_e.current,_e.current=Ro;try{var C=t.lastRenderedState,R=v(C,a);if(c.hasEagerState=!0,c.eagerState=R,Me(R,C)){sP(e,t,c,l);return}}catch{}finally{_e.current=w}}}var M=MT(e,t,c,l);if(M!==null){var U=Ii();jr(M,e,l,U),nk(M,t,l)}}rk(e,l)}function ek(e){var t=e.alternate;return e===dn||t!==null&&t===dn}function tk(e,t){qh=hy=!0;var a=e.pending;a===null?t.next=t:(t.next=a.next,a.next=t),e.pending=t}function nk(e,t,a){if(ah(a)){var l=t.lanes;l=lf(l,e.pendingLanes);var c=xt(l,a);t.lanes=c,wc(e,c)}}function rk(e,t,a){dc(e,t)}var Ry={readContext:vr,useCallback:Ci,useContext:Ci,useEffect:Ci,useImperativeHandle:Ci,useInsertionEffect:Ci,useLayoutEffect:Ci,useMemo:Ci,useReducer:Ci,useRef:Ci,useState:Ci,useDebugValue:Ci,useDeferredValue:Ci,useTransition:Ci,useMutableSource:Ci,useSyncExternalStore:Ci,useId:Ci,unstable_isNewReconciler:ke},ik=null,ak=null,ok=null,lk=null,fl=null,Ro=null,Dy=null;{var yw=function(){y("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().")},ct=function(){y("Do not call Hooks inside useEffect(...), useMemo(...), or other built-in Hooks. You can only call Hooks at the top level of your React function. For more information, see https://reactjs.org/link/rules-of-hooks")};ik={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Jt(),Gf(t),pw(e,t)},useContext:function(e){return oe="useContext",Jt(),vr(e)},useEffect:function(e,t){return oe="useEffect",Jt(),Gf(t),xy(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Jt(),Gf(a),fw(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Jt(),Gf(t),cw(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Jt(),Gf(t),dw(e,t)},useMemo:function(e,t){oe="useMemo",Jt(),Gf(t);var a=_e.current;_e.current=fl;try{return hw(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",Jt();var l=_e.current;_e.current=fl;try{return rw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",Jt(),uw(e)},useState:function(e){oe="useState",Jt();var t=_e.current;_e.current=fl;try{return my(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Jt(),void 0},useDeferredValue:function(e){return oe="useDeferredValue",Jt(),gw(e)},useTransition:function(){return oe="useTransition",Jt(),mw()},useMutableSource:function(e,t,a){return oe="useMutableSource",Jt(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Jt(),ow(e,t,a)},useId:function(){return oe="useId",Jt(),vw()},unstable_isNewReconciler:ke},ak={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Se(),pw(e,t)},useContext:function(e){return oe="useContext",Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",Se(),xy(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Se(),fw(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Se(),cw(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Se(),dw(e,t)},useMemo:function(e,t){oe="useMemo",Se();var a=_e.current;_e.current=fl;try{return hw(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",Se();var l=_e.current;_e.current=fl;try{return rw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",Se(),uw(e)},useState:function(e){oe="useState",Se();var t=_e.current;_e.current=fl;try{return my(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Se(),void 0},useDeferredValue:function(e){return oe="useDeferredValue",Se(),gw(e)},useTransition:function(){return oe="useTransition",Se(),mw()},useMutableSource:function(e,t,a){return oe="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Se(),ow(e,t,a)},useId:function(){return oe="useId",Se(),vw()},unstable_isNewReconciler:ke},ok={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Se(),Ey(e,t)},useContext:function(e){return oe="useContext",Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",Se(),eg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Se(),Sy(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Se(),by(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Se(),wy(e,t)},useMemo:function(e,t){oe="useMemo",Se();var a=_e.current;_e.current=Ro;try{return Ty(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",Se();var l=_e.current;_e.current=Ro;try{return iw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",Se(),vy()},useState:function(e){oe="useState",Se();var t=_e.current;_e.current=Ro;try{return lw(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Se(),Cy()},useDeferredValue:function(e){return oe="useDeferredValue",Se(),KT(e)},useTransition:function(){return oe="useTransition",Se(),XT()},useMutableSource:function(e,t,a){return oe="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Se(),gy(e,t)},useId:function(){return oe="useId",Se(),ky()},unstable_isNewReconciler:ke},lk={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Se(),Ey(e,t)},useContext:function(e){return oe="useContext",Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",Se(),eg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Se(),Sy(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Se(),by(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Se(),wy(e,t)},useMemo:function(e,t){oe="useMemo",Se();var a=_e.current;_e.current=Dy;try{return Ty(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",Se();var l=_e.current;_e.current=Dy;try{return aw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",Se(),vy()},useState:function(e){oe="useState",Se();var t=_e.current;_e.current=Dy;try{return sw(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Se(),Cy()},useDeferredValue:function(e){return oe="useDeferredValue",Se(),QT(e)},useTransition:function(){return oe="useTransition",Se(),JT()},useMutableSource:function(e,t,a){return oe="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Se(),gy(e,t)},useId:function(){return oe="useId",Se(),ky()},unstable_isNewReconciler:ke},fl={readContext:function(e){return yw(),vr(e)},useCallback:function(e,t){return oe="useCallback",ct(),Jt(),pw(e,t)},useContext:function(e){return oe="useContext",ct(),Jt(),vr(e)},useEffect:function(e,t){return oe="useEffect",ct(),Jt(),xy(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",ct(),Jt(),fw(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",ct(),Jt(),cw(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",ct(),Jt(),dw(e,t)},useMemo:function(e,t){oe="useMemo",ct(),Jt();var a=_e.current;_e.current=fl;try{return hw(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",ct(),Jt();var l=_e.current;_e.current=fl;try{return rw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",ct(),Jt(),uw(e)},useState:function(e){oe="useState",ct(),Jt();var t=_e.current;_e.current=fl;try{return my(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",ct(),Jt(),void 0},useDeferredValue:function(e){return oe="useDeferredValue",ct(),Jt(),gw(e)},useTransition:function(){return oe="useTransition",ct(),Jt(),mw()},useMutableSource:function(e,t,a){return oe="useMutableSource",ct(),Jt(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",ct(),Jt(),ow(e,t,a)},useId:function(){return oe="useId",ct(),Jt(),vw()},unstable_isNewReconciler:ke},Ro={readContext:function(e){return yw(),vr(e)},useCallback:function(e,t){return oe="useCallback",ct(),Se(),Ey(e,t)},useContext:function(e){return oe="useContext",ct(),Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",ct(),Se(),eg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",ct(),Se(),Sy(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",ct(),Se(),by(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",ct(),Se(),wy(e,t)},useMemo:function(e,t){oe="useMemo",ct(),Se();var a=_e.current;_e.current=Ro;try{return Ty(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",ct(),Se();var l=_e.current;_e.current=Ro;try{return iw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",ct(),Se(),vy()},useState:function(e){oe="useState",ct(),Se();var t=_e.current;_e.current=Ro;try{return lw(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",ct(),Se(),Cy()},useDeferredValue:function(e){return oe="useDeferredValue",ct(),Se(),KT(e)},useTransition:function(){return oe="useTransition",ct(),Se(),XT()},useMutableSource:function(e,t,a){return oe="useMutableSource",ct(),Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",ct(),Se(),gy(e,t)},useId:function(){return oe="useId",ct(),Se(),ky()},unstable_isNewReconciler:ke},Dy={readContext:function(e){return yw(),vr(e)},useCallback:function(e,t){return oe="useCallback",ct(),Se(),Ey(e,t)},useContext:function(e){return oe="useContext",ct(),Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",ct(),Se(),eg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",ct(),Se(),Sy(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",ct(),Se(),by(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",ct(),Se(),wy(e,t)},useMemo:function(e,t){oe="useMemo",ct(),Se();var a=_e.current;_e.current=Ro;try{return Ty(e,t)}finally{_e.current=a}},useReducer:function(e,t,a){oe="useReducer",ct(),Se();var l=_e.current;_e.current=Ro;try{return aw(e,t,a)}finally{_e.current=l}},useRef:function(e){return oe="useRef",ct(),Se(),vy()},useState:function(e){oe="useState",ct(),Se();var t=_e.current;_e.current=Ro;try{return sw(e)}finally{_e.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",ct(),Se(),Cy()},useDeferredValue:function(e){return oe="useDeferredValue",ct(),Se(),QT(e)},useTransition:function(){return oe="useTransition",ct(),Se(),JT()},useMutableSource:function(e,t,a){return oe="useMutableSource",ct(),Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",ct(),Se(),gy(e,t)},useId:function(){return oe="useId",ct(),Se(),ky()},unstable_isNewReconciler:ke}}var xu=s.unstable_now,sk=0,My=-1,tg=-1,Oy=-1,xw=!1,$y=!1;function uk(){return xw}function TP(){$y=!0}function kP(){xw=!1,$y=!1}function RP(){xw=$y,$y=!1}function ck(){return sk}function dk(){sk=xu()}function bw(e){tg=xu(),e.actualStartTime<0&&(e.actualStartTime=xu())}function fk(e){tg=-1}function Ay(e,t){if(tg>=0){var a=xu()-tg;e.actualDuration+=a,t&&(e.selfBaseDuration=a),tg=-1}}function pl(e){if(My>=0){var t=xu()-My;My=-1;for(var a=e.return;a!==null;){switch(a.tag){case j:var l=a.stateNode;l.effectDuration+=t;return;case Ee:var c=a.stateNode;c.effectDuration+=t;return}a=a.return}}}function ww(e){if(Oy>=0){var t=xu()-Oy;Oy=-1;for(var a=e.return;a!==null;){switch(a.tag){case j:var l=a.stateNode;l!==null&&(l.passiveEffectDuration+=t);return;case Ee:var c=a.stateNode;c!==null&&(c.passiveEffectDuration+=t);return}a=a.return}}}function hl(){My=xu()}function Sw(){Oy=xu()}function Cw(e){for(var t=e.child;t;)e.actualDuration+=t.actualDuration,t=t.sibling}function Do(e,t){if(e&&e.defaultProps){var a=gt({},t),l=e.defaultProps;for(var c in l)a[c]===void 0&&(a[c]=l[c]);return a}return t}var Ew={},Tw,kw,Rw,Dw,Mw,pk,jy,Ow,$w,Aw,ng;{Tw=new Set,kw=new Set,Rw=new Set,Dw=new Set,Ow=new Set,Mw=new Set,$w=new Set,Aw=new Set,ng=new Set;var hk=new Set;jy=function(e,t){if(!(e===null||typeof e=="function")){var a=t+"_"+e;hk.has(a)||(hk.add(a),y("%s(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",t,e))}},pk=function(e,t){if(t===void 0){var a=Pt(e)||"Component";Mw.has(a)||(Mw.add(a),y("%s.getDerivedStateFromProps(): A valid state object (or null) must be returned. You have returned undefined.",a))}},Object.defineProperty(Ew,"_processChildContext",{enumerable:!1,value:function(){throw new Error("_processChildContext is not available in React 16+. This likely means you have multiple copies of React and are attempting to nest a React 15 tree inside a React 16 tree using unstable_renderSubtreeIntoContainer, which isn't supported. Try to make sure you have only one copy of React (and ideally, switch to ReactDOM.createPortal).")}}),Object.freeze(Ew)}function jw(e,t,a,l){var c=e.memoizedState,p=a(l,c);{if(e.mode&mt){nn(!0);try{p=a(l,c)}finally{nn(!1)}}pk(t,p)}var v=p==null?c:gt({},c,p);if(e.memoizedState=v,e.lanes===ie){var w=e.updateQueue;w.baseState=v}}var _w={isMounted:Ip,enqueueSetState:function(e,t,a){var l=Bs(e),c=Ii(),p=Cu(l),v=ls(c,p);v.payload=t,a!=null&&(jy(a,"setState"),v.callback=a);var w=gu(l,v,p);w!==null&&(jr(w,l,p,c),sy(w,l,p)),dc(l,p)},enqueueReplaceState:function(e,t,a){var l=Bs(e),c=Ii(),p=Cu(l),v=ls(c,p);v.tag=$T,v.payload=t,a!=null&&(jy(a,"replaceState"),v.callback=a);var w=gu(l,v,p);w!==null&&(jr(w,l,p,c),sy(w,l,p)),dc(l,p)},enqueueForceUpdate:function(e,t){var a=Bs(e),l=Ii(),c=Cu(a),p=ls(l,c);p.tag=ay,t!=null&&(jy(t,"forceUpdate"),p.callback=t);var v=gu(a,p,c);v!==null&&(jr(v,a,c,l),sy(v,a,c)),eh(a,c)}};function gk(e,t,a,l,c,p,v){var w=e.stateNode;if(typeof w.shouldComponentUpdate=="function"){var C=w.shouldComponentUpdate(l,p,v);{if(e.mode&mt){nn(!0);try{C=w.shouldComponentUpdate(l,p,v)}finally{nn(!1)}}C===void 0&&y("%s.shouldComponentUpdate(): Returned undefined instead of a boolean value. Make sure to return true or false.",Pt(t)||"Component")}return C}return t.prototype&&t.prototype.isPureReactComponent?!Ke(a,l)||!Ke(c,p):!0}function DP(e,t,a){var l=e.stateNode;{var c=Pt(t)||"Component",p=l.render;p||(t.prototype&&typeof t.prototype.render=="function"?y("%s(...): No `render` method found on the returned component instance: did you accidentally return an object from the constructor?",c):y("%s(...): No `render` method found on the returned component instance: you may have forgotten to define `render`.",c)),l.getInitialState&&!l.getInitialState.isReactClassApproved&&!l.state&&y("getInitialState was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Did you mean to define a state property instead?",c),l.getDefaultProps&&!l.getDefaultProps.isReactClassApproved&&y("getDefaultProps was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Use a static property to define defaultProps instead.",c),l.propTypes&&y("propTypes was defined as an instance property on %s. Use a static property to define propTypes instead.",c),l.contextType&&y("contextType was defined as an instance property on %s. Use a static property to define contextType instead.",c),t.childContextTypes&&!ng.has(t)&&(e.mode&mt)===Ge&&(ng.add(t),y(`%s uses the legacy childContextTypes API which is no longer supported and will be removed in the next major release. Use React.createContext() instead

.Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)),t.contextTypes&&!ng.has(t)&&(e.mode&mt)===Ge&&(ng.add(t),y(`%s uses the legacy contextTypes API which is no longer supported and will be removed in the next major release. Use React.createContext() with static contextType instead.

Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)),l.contextTypes&&y("contextTypes was defined as an instance property on %s. Use a static property to define contextTypes instead.",c),t.contextType&&t.contextTypes&&!$w.has(t)&&($w.add(t),y("%s declares both contextTypes and contextType static properties. The legacy contextTypes property will be ignored.",c)),typeof l.componentShouldUpdate=="function"&&y("%s has a method called componentShouldUpdate(). Did you mean shouldComponentUpdate()? The name is phrased as a question because the function is expected to return a value.",c),t.prototype&&t.prototype.isPureReactComponent&&typeof l.shouldComponentUpdate<"u"&&y("%s has a method called shouldComponentUpdate(). shouldComponentUpdate should not be used when extending React.PureComponent. Please extend React.Component if shouldComponentUpdate is used.",Pt(t)||"A pure component"),typeof l.componentDidUnmount=="function"&&y("%s has a method called componentDidUnmount(). But there is no such lifecycle method. Did you mean componentWillUnmount()?",c),typeof l.componentDidReceiveProps=="function"&&y("%s has a method called componentDidReceiveProps(). But there is no such lifecycle method. If you meant to update the state in response to changing props, use componentWillReceiveProps(). If you meant to fetch data or run side-effects or mutations after React has updated the UI, use componentDidUpdate().",c),typeof l.componentWillRecieveProps=="function"&&y("%s has a method called componentWillRecieveProps(). Did you mean componentWillReceiveProps()?",c),typeof l.UNSAFE_componentWillRecieveProps=="function"&&y("%s has a method called UNSAFE_componentWillRecieveProps(). Did you mean UNSAFE_componentWillReceiveProps()?",c);var v=l.props!==a;l.props!==void 0&&v&&y("%s(...): When calling super() in `%s`, make sure to pass up the same props that your component's constructor was passed.",c,c),l.defaultProps&&y("Setting defaultProps as an instance property on %s is not supported and will be ignored. Instead, define defaultProps as a static property on %s.",c,c),typeof l.getSnapshotBeforeUpdate=="function"&&typeof l.componentDidUpdate!="function"&&!Rw.has(t)&&(Rw.add(t),y("%s: getSnapshotBeforeUpdate() should be used with componentDidUpdate(). This component defines getSnapshotBeforeUpdate() only.",Pt(t))),typeof l.getDerivedStateFromProps=="function"&&y("%s: getDerivedStateFromProps() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof l.getDerivedStateFromError=="function"&&y("%s: getDerivedStateFromError() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof t.getSnapshotBeforeUpdate=="function"&&y("%s: getSnapshotBeforeUpdate() is defined as a static method and will be ignored. Instead, declare it as an instance method.",c);var w=l.state;w&&(typeof w!="object"||yt(w))&&y("%s.state: must be set to an object or null",c),typeof l.getChildContext=="function"&&typeof t.childContextTypes!="object"&&y("%s.getChildContext(): childContextTypes must be defined in order to use getChildContext().",c)}}function mk(e,t){t.updater=_w,e.stateNode=t,ic(t,e),t._reactInternalInstance=Ew}function vk(e,t,a){var l=!1,c=ka,p=ka,v=t.contextType;if("contextType"in t){var w=v===null||v!==void 0&&v.$$typeof===z&&v._context===void 0;if(!w&&!Aw.has(t)){Aw.add(t);var C="";v===void 0?C=" However, it is set to undefined. This can be caused by a typo or by mixing up named and default imports. This can also happen due to a circular dependency, so try moving the createContext() call to a separate file.":typeof v!="object"?C=" However, it is set to a "+typeof v+".":v.$$typeof===oo?C=" Did you accidentally pass the Context.Provider instead?":v._context!==void 0?C=" Did you accidentally pass the Context.Consumer instead?":C=" However, it is set to an object with keys {"+Object.keys(v).join(", ")+"}.",y("%s defines an invalid contextType. contextType should point to the Context object returned by React.createContext().%s",Pt(t)||"Component",C)}}if(typeof v=="object"&&v!==null)p=vr(v);else{c=Lf(e,t,!0);var R=t.contextTypes;l=R!=null,p=l?zf(e,c):ka}var M=new t(a,p);if(e.mode&mt){nn(!0);try{M=new t(a,p)}finally{nn(!1)}}var U=e.memoizedState=M.state!==null&&M.state!==void 0?M.state:null;mk(e,M);{if(typeof t.getDerivedStateFromProps=="function"&&U===null){var F=Pt(t)||"Component";kw.has(F)||(kw.add(F),y("`%s` uses `getDerivedStateFromProps` but its initial state is %s. This is not recommended. Instead, define the initial state by assigning an object to `this.state` in the constructor of `%s`. This ensures that `getDerivedStateFromProps` arguments have a consistent shape.",F,M.state===null?"null":"undefined",F))}if(typeof t.getDerivedStateFromProps=="function"||typeof M.getSnapshotBeforeUpdate=="function"){var X=null,Z=null,te=null;if(typeof M.componentWillMount=="function"&&M.componentWillMount.__suppressDeprecationWarning!==!0?X="componentWillMount":typeof M.UNSAFE_componentWillMount=="function"&&(X="UNSAFE_componentWillMount"),typeof M.componentWillReceiveProps=="function"&&M.componentWillReceiveProps.__suppressDeprecationWarning!==!0?Z="componentWillReceiveProps":typeof M.UNSAFE_componentWillReceiveProps=="function"&&(Z="UNSAFE_componentWillReceiveProps"),typeof M.componentWillUpdate=="function"&&M.componentWillUpdate.__suppressDeprecationWarning!==!0?te="componentWillUpdate":typeof M.UNSAFE_componentWillUpdate=="function"&&(te="UNSAFE_componentWillUpdate"),X!==null||Z!==null||te!==null){var Re=Pt(t)||"Component",Xe=typeof t.getDerivedStateFromProps=="function"?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";Dw.has(Re)||(Dw.add(Re),y(`Unsafe legacy lifecycles will not be called for components using new component APIs.

%s uses %s but also contains the following legacy lifecycles:%s%s%s

The above lifecycles should be removed. Learn more about this warning here:
https://reactjs.org/link/unsafe-component-lifecycles`,Re,Xe,X!==null?`
  `+X:"",Z!==null?`
  `+Z:"",te!==null?`
  `+te:""))}}}return l&&oT(e,c,p),M}function MP(e,t){var a=t.state;typeof t.componentWillMount=="function"&&t.componentWillMount(),typeof t.UNSAFE_componentWillMount=="function"&&t.UNSAFE_componentWillMount(),a!==t.state&&(y("%s.componentWillMount(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",lt(e)||"Component"),_w.enqueueReplaceState(t,t.state,null))}function yk(e,t,a,l){var c=t.state;if(typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps(a,l),typeof t.UNSAFE_componentWillReceiveProps=="function"&&t.UNSAFE_componentWillReceiveProps(a,l),t.state!==c){{var p=lt(e)||"Component";Tw.has(p)||(Tw.add(p),y("%s.componentWillReceiveProps(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",p))}_w.enqueueReplaceState(t,t.state,null)}}function Lw(e,t,a,l){DP(e,t,a);var c=e.stateNode;c.props=a,c.state=e.memoizedState,c.refs={},Hb(e);var p=t.contextType;if(typeof p=="object"&&p!==null)c.context=vr(p);else{var v=Lf(e,t,!0);c.context=zf(e,v)}{if(c.state===a){var w=Pt(t)||"Component";Ow.has(w)||(Ow.add(w),y("%s: It is not recommended to assign props directly to state because updates to props won't be reflected in state. In most cases, it is better to use props directly.",w))}e.mode&mt&&To.recordLegacyContextWarning(e,c),To.recordUnsafeLifecycleWarnings(e,c)}c.state=e.memoizedState;var C=t.getDerivedStateFromProps;if(typeof C=="function"&&(jw(e,t,C,a),c.state=e.memoizedState),typeof t.getDerivedStateFromProps!="function"&&typeof c.getSnapshotBeforeUpdate!="function"&&(typeof c.UNSAFE_componentWillMount=="function"||typeof c.componentWillMount=="function")&&(MP(e,c),uy(e,a,c,l),c.state=e.memoizedState),typeof c.componentDidMount=="function"){var R=At;R|=Ho,(e.mode&cn)!==Ge&&(R|=Gr),e.flags|=R}}function OP(e,t,a,l){var c=e.stateNode,p=e.memoizedProps;c.props=p;var v=c.context,w=t.contextType,C=ka;if(typeof w=="object"&&w!==null)C=vr(w);else{var R=Lf(e,t,!0);C=zf(e,R)}var M=t.getDerivedStateFromProps,U=typeof M=="function"||typeof c.getSnapshotBeforeUpdate=="function";!U&&(typeof c.UNSAFE_componentWillReceiveProps=="function"||typeof c.componentWillReceiveProps=="function")&&(p!==a||v!==C)&&yk(e,c,a,C),jT();var F=e.memoizedState,X=c.state=F;if(uy(e,a,c,l),X=e.memoizedState,p===a&&F===X&&!Vv()&&!cy()){if(typeof c.componentDidMount=="function"){var Z=At;Z|=Ho,(e.mode&cn)!==Ge&&(Z|=Gr),e.flags|=Z}return!1}typeof M=="function"&&(jw(e,t,M,a),X=e.memoizedState);var te=cy()||gk(e,t,p,a,F,X,C);if(te){if(!U&&(typeof c.UNSAFE_componentWillMount=="function"||typeof c.componentWillMount=="function")&&(typeof c.componentWillMount=="function"&&c.componentWillMount(),typeof c.UNSAFE_componentWillMount=="function"&&c.UNSAFE_componentWillMount()),typeof c.componentDidMount=="function"){var Re=At;Re|=Ho,(e.mode&cn)!==Ge&&(Re|=Gr),e.flags|=Re}}else{if(typeof c.componentDidMount=="function"){var Xe=At;Xe|=Ho,(e.mode&cn)!==Ge&&(Xe|=Gr),e.flags|=Xe}e.memoizedProps=a,e.memoizedState=X}return c.props=a,c.state=X,c.context=C,te}function $P(e,t,a,l,c){var p=t.stateNode;AT(e,t);var v=t.memoizedProps,w=t.type===t.elementType?v:Do(t.type,v);p.props=w;var C=t.pendingProps,R=p.context,M=a.contextType,U=ka;if(typeof M=="object"&&M!==null)U=vr(M);else{var F=Lf(t,a,!0);U=zf(t,F)}var X=a.getDerivedStateFromProps,Z=typeof X=="function"||typeof p.getSnapshotBeforeUpdate=="function";!Z&&(typeof p.UNSAFE_componentWillReceiveProps=="function"||typeof p.componentWillReceiveProps=="function")&&(v!==C||R!==U)&&yk(t,p,l,U),jT();var te=t.memoizedState,Re=p.state=te;if(uy(t,l,p,c),Re=t.memoizedState,v===C&&te===Re&&!Vv()&&!cy()&&!be)return typeof p.componentDidUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=ra),!1;typeof X=="function"&&(jw(t,a,X,l),Re=t.memoizedState);var Xe=cy()||gk(t,a,w,l,te,Re,U)||be;return Xe?(!Z&&(typeof p.UNSAFE_componentWillUpdate=="function"||typeof p.componentWillUpdate=="function")&&(typeof p.componentWillUpdate=="function"&&p.componentWillUpdate(l,Re,U),typeof p.UNSAFE_componentWillUpdate=="function"&&p.UNSAFE_componentWillUpdate(l,Re,U)),typeof p.componentDidUpdate=="function"&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(t.flags|=ra)):(typeof p.componentDidUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=ra),t.memoizedProps=l,t.memoizedState=Re),p.props=l,p.state=Re,p.context=U,Xe}function Gc(e,t){return{value:e,source:t,stack:Nt(t),digest:null}}function zw(e,t,a){return{value:e,source:null,stack:a??null,digest:t??null}}function AP(e,t){return!0}function Nw(e,t){try{var a=AP(e,t);if(a===!1)return;var l=t.value,c=t.source,p=t.stack,v=p!==null?p:"";if(l!=null&&l._suppressLogging){if(e.tag===$)return;console.error(l)}var w=c?lt(c):null,C=w?"The above error occurred in the <"+w+"> component:":"The above error occurred in one of your React components:",R;if(e.tag===j)R=`Consider adding an error boundary to your tree to customize error handling behavior.
Visit https://reactjs.org/link/error-boundaries to learn more about error boundaries.`;else{var M=lt(e)||"Anonymous";R="React will try to recreate this component tree from scratch "+("using the error boundary you provided, "+M+".")}var U=C+`
`+v+`

`+(""+R);console.error(U)}catch(F){setTimeout(function(){throw F})}}var jP=typeof WeakMap=="function"?WeakMap:Map;function xk(e,t,a){var l=ls(rn,a);l.tag=Ub,l.payload={element:null};var c=t.value;return l.callback=function(){T5(c),Nw(e,t)},l}function Pw(e,t,a){var l=ls(rn,a);l.tag=Ub;var c=e.type.getDerivedStateFromError;if(typeof c=="function"){var p=t.value;l.payload=function(){return c(p)},l.callback=function(){OR(e),Nw(e,t)}}var v=e.stateNode;return v!==null&&typeof v.componentDidCatch=="function"&&(l.callback=function(){OR(e),Nw(e,t),typeof c!="function"&&C5(this);var C=t.value,R=t.stack;this.componentDidCatch(C,{componentStack:R!==null?R:""}),typeof c!="function"&&(yi(e.lanes,nt)||y("%s: Error boundaries should implement getDerivedStateFromError(). In that method, return a state update to display an error message or fallback UI.",lt(e)||"Unknown"))}),l}function bk(e,t,a){var l=e.pingCache,c;if(l===null?(l=e.pingCache=new jP,c=new Set,l.set(t,c)):(c=l.get(t),c===void 0&&(c=new Set,l.set(t,c))),!c.has(a)){c.add(a);var p=k5.bind(null,e,t,a);Fr&&xg(e,a),t.then(p,p)}}function _P(e,t,a,l){var c=e.updateQueue;if(c===null){var p=new Set;p.add(a),e.updateQueue=p}else c.add(a)}function LP(e,t){var a=e.tag;if((e.mode&Dt)===Ge&&(a===O||a===ce||a===$e)){var l=e.alternate;l?(e.updateQueue=l.updateQueue,e.memoizedState=l.memoizedState,e.lanes=l.lanes):(e.updateQueue=null,e.memoizedState=null)}}function wk(e){var t=e;do{if(t.tag===le&&gP(t))return t;t=t.return}while(t!==null);return null}function Sk(e,t,a,l,c){if((e.mode&Dt)===Ge){if(e===t)e.flags|=Pr;else{if(e.flags|=Et,a.flags|=wa,a.flags&=-52805,a.tag===$){var p=a.alternate;if(p===null)a.tag=He;else{var v=ls(rn,nt);v.tag=ay,gu(a,v,nt)}}a.lanes=xt(a.lanes,nt)}return e}return e.flags|=Pr,e.lanes=c,e}function zP(e,t,a,l,c){if(a.flags|=zl,Fr&&xg(e,c),l!==null&&typeof l=="object"&&typeof l.then=="function"){var p=l;LP(a),Xr()&&a.mode&Dt&&pT();var v=wk(t);if(v!==null){v.flags&=~Rn,Sk(v,t,a,e,c),v.mode&Dt&&bk(e,p,c),_P(v,e,p);return}else{if(!rh(c)){bk(e,p,c),vS();return}var w=new Error("A component suspended while responding to synchronous input. This will cause the UI to be replaced with a loading indicator. To fix, updates that suspend should be wrapped with startTransition.");l=w}}else if(Xr()&&a.mode&Dt){pT();var C=wk(t);if(C!==null){(C.flags&Pr)===Ye&&(C.flags|=Rn),Sk(C,t,a,e,c),Mb(Gc(l,a));return}}l=Gc(l,a),g5(l);var R=t;do{switch(R.tag){case j:{var M=l;R.flags|=Pr;var U=gr(c);R.lanes=xt(R.lanes,U);var F=xk(R,M,U);Vb(R,F);return}case $:var X=l,Z=R.type,te=R.stateNode;if((R.flags&Et)===Ye&&(typeof Z.getDerivedStateFromError=="function"||te!==null&&typeof te.componentDidCatch=="function"&&!wR(te))){R.flags|=Pr;var Re=gr(c);R.lanes=xt(R.lanes,Re);var Xe=Pw(R,X,Re);Vb(R,Xe);return}break}R=R.return}while(R!==null)}function NP(){return null}var rg=d.ReactCurrentOwner,Mo=!1,Fw,ig,Iw,Uw,Bw,Kc,Hw,_y,ag;Fw={},ig={},Iw={},Uw={},Bw={},Kc=!1,Hw={},_y={},ag={};function Pi(e,t,a,l){e===null?t.child=TT(t,null,a,l):t.child=If(t,e.child,a,l)}function PP(e,t,a,l){t.child=If(t,e.child,null,l),t.child=If(t,null,a,l)}function Ck(e,t,a,l,c){if(t.type!==t.elementType){var p=a.propTypes;p&&Co(p,l,"prop",Pt(a))}var v=a.render,w=t.ref,C,R;Bf(t,c),ia(t);{if(rg.current=t,Ji(!0),C=Kf(e,t,v,l,w,c),R=Qf(),t.mode&mt){nn(!0);try{C=Kf(e,t,v,l,w,c),R=Qf()}finally{nn(!1)}}Ji(!1)}return Ko(),e!==null&&!Mo?(FT(e,t,c),ss(e,t,c)):(Xr()&&R&&Cb(t),t.flags|=yo,Pi(e,t,C,c),t.child)}function Ek(e,t,a,l,c){if(e===null){var p=a.type;if(H5(p)&&a.compare===null&&a.defaultProps===void 0){var v=p;return v=rp(p),t.tag=$e,t.type=v,Yw(t,p),Tk(e,t,v,l,c)}{var w=p.propTypes;if(w&&Co(w,l,"prop",Pt(p)),a.defaultProps!==void 0){var C=Pt(p)||"Unknown";ag[C]||(y("%s: Support for defaultProps will be removed from memo components in a future major release. Use JavaScript default parameters instead.",C),ag[C]=!0)}}var R=DS(a.type,null,l,t,t.mode,c);return R.ref=t.ref,R.return=t,t.child=R,R}{var M=a.type,U=M.propTypes;U&&Co(U,l,"prop",Pt(M))}var F=e.child,X=Jw(e,c);if(!X){var Z=F.memoizedProps,te=a.compare;if(te=te!==null?te:Ke,te(Z,l)&&e.ref===t.ref)return ss(e,t,c)}t.flags|=yo;var Re=Zc(F,l);return Re.ref=t.ref,Re.return=t,t.child=Re,Re}function Tk(e,t,a,l,c){if(t.type!==t.elementType){var p=t.elementType;if(p.$$typeof===ut){var v=p,w=v._payload,C=v._init;try{p=C(w)}catch{p=null}var R=p&&p.propTypes;R&&Co(R,l,"prop",Pt(p))}}if(e!==null){var M=e.memoizedProps;if(Ke(M,l)&&e.ref===t.ref&&t.type===e.type)if(Mo=!1,t.pendingProps=l=M,Jw(e,c))(e.flags&wa)!==Ye&&(Mo=!0);else return t.lanes=e.lanes,ss(e,t,c)}return Vw(e,t,a,l,c)}function kk(e,t,a){var l=t.pendingProps,c=l.children,p=e!==null?e.memoizedState:null;if(l.mode==="hidden"||B)if((t.mode&Dt)===Ge){var v={baseLanes:ie,cachePool:null,transitions:null};t.memoizedState=v,Gy(t,a)}else if(yi(a,mi)){var U={baseLanes:ie,cachePool:null,transitions:null};t.memoizedState=U;var F=p!==null?p.baseLanes:a;Gy(t,F)}else{var w=null,C;if(p!==null){var R=p.baseLanes;C=xt(R,a)}else C=a;t.lanes=t.childLanes=mi;var M={baseLanes:C,cachePool:w,transitions:null};return t.memoizedState=M,t.updateQueue=null,Gy(t,C),null}else{var X;p!==null?(X=xt(p.baseLanes,a),t.memoizedState=null):X=a,Gy(t,X)}return Pi(e,t,c,a),t.child}function FP(e,t,a){var l=t.pendingProps;return Pi(e,t,l,a),t.child}function IP(e,t,a){var l=t.pendingProps.children;return Pi(e,t,l,a),t.child}function UP(e,t,a){{t.flags|=At;{var l=t.stateNode;l.effectDuration=0,l.passiveEffectDuration=0}}var c=t.pendingProps,p=c.children;return Pi(e,t,p,a),t.child}function Rk(e,t){var a=t.ref;(e===null&&a!==null||e!==null&&e.ref!==a)&&(t.flags|=qn,t.flags|=oc)}function Vw(e,t,a,l,c){if(t.type!==t.elementType){var p=a.propTypes;p&&Co(p,l,"prop",Pt(a))}var v;{var w=Lf(t,a,!0);v=zf(t,w)}var C,R;Bf(t,c),ia(t);{if(rg.current=t,Ji(!0),C=Kf(e,t,a,l,v,c),R=Qf(),t.mode&mt){nn(!0);try{C=Kf(e,t,a,l,v,c),R=Qf()}finally{nn(!1)}}Ji(!1)}return Ko(),e!==null&&!Mo?(FT(e,t,c),ss(e,t,c)):(Xr()&&R&&Cb(t),t.flags|=yo,Pi(e,t,C,c),t.child)}function Dk(e,t,a,l,c){{switch(iF(t)){case!1:{var p=t.stateNode,v=t.type,w=new v(t.memoizedProps,p.context),C=w.state;p.updater.enqueueSetState(p,C,null);break}case!0:{t.flags|=Et,t.flags|=Pr;var R=new Error("Simulated error coming from DevTools"),M=gr(c);t.lanes=xt(t.lanes,M);var U=Pw(t,Gc(R,t),M);Vb(t,U);break}}if(t.type!==t.elementType){var F=a.propTypes;F&&Co(F,l,"prop",Pt(a))}}var X;ul(a)?(X=!0,Yv(t)):X=!1,Bf(t,c);var Z=t.stateNode,te;Z===null?(zy(e,t),vk(t,a,l),Lw(t,a,l,c),te=!0):e===null?te=OP(t,a,l,c):te=$P(e,t,a,l,c);var Re=Ww(e,t,a,te,X,c);{var Xe=t.stateNode;te&&Xe.props!==l&&(Kc||y("It looks like %s is reassigning its own `this.props` while rendering. This is not supported and can lead to confusing bugs.",lt(t)||"a component"),Kc=!0)}return Re}function Ww(e,t,a,l,c,p){Rk(e,t);var v=(t.flags&Et)!==Ye;if(!l&&!v)return c&&uT(t,a,!1),ss(e,t,p);var w=t.stateNode;rg.current=t;var C;if(v&&typeof a.getDerivedStateFromError!="function")C=null,fk();else{ia(t);{if(Ji(!0),C=w.render(),t.mode&mt){nn(!0);try{w.render()}finally{nn(!1)}}Ji(!1)}Ko()}return t.flags|=yo,e!==null&&v?PP(e,t,C,p):Pi(e,t,C,p),t.memoizedState=w.state,c&&uT(t,a,!0),t.child}function Mk(e){var t=e.stateNode;t.pendingContext?lT(e,t.pendingContext,t.pendingContext!==t.context):t.context&&lT(e,t.context,!1),Wb(e,t.containerInfo)}function BP(e,t,a){if(Mk(t),e===null)throw new Error("Should have a current fiber. This is a bug in React.");var l=t.pendingProps,c=t.memoizedState,p=c.element;AT(e,t),uy(t,l,null,a);var v=t.memoizedState;t.stateNode;var w=v.element;if(c.isDehydrated){var C={element:w,isDehydrated:!1,cache:v.cache,pendingSuspenseBoundaries:v.pendingSuspenseBoundaries,transitions:v.transitions},R=t.updateQueue;if(R.baseState=C,t.memoizedState=C,t.flags&Rn){var M=Gc(new Error("There was an error while hydrating. Because the error happened outside of a Suspense boundary, the entire root will switch to client rendering."),t);return Ok(e,t,w,a,M)}else if(w!==p){var U=Gc(new Error("This root received an early update, before anything was able hydrate. Switched the entire root to client rendering."),t);return Ok(e,t,w,a,U)}else{WN(t);var F=TT(t,null,w,a);t.child=F;for(var X=F;X;)X.flags=X.flags&~Ln|zn,X=X.sibling}}else{if(Ff(),w===p)return ss(e,t,a);Pi(e,t,w,a)}return t.child}function Ok(e,t,a,l,c){return Ff(),Mb(c),t.flags|=Rn,Pi(e,t,a,l),t.child}function HP(e,t,a){zT(t),e===null&&Db(t);var l=t.type,c=t.pendingProps,p=e!==null?e.memoizedProps:null,v=c.children,w=ub(l,c);return w?v=null:p!==null&&ub(l,p)&&(t.flags|=tn),Rk(e,t),Pi(e,t,v,a),t.child}function VP(e,t){return e===null&&Db(t),null}function WP(e,t,a,l){zy(e,t);var c=t.pendingProps,p=a,v=p._payload,w=p._init,C=w(v);t.type=C;var R=t.tag=V5(C),M=Do(C,c),U;switch(R){case O:return Yw(t,C),t.type=C=rp(C),U=Vw(null,t,C,M,l),U;case $:return t.type=C=SS(C),U=Dk(null,t,C,M,l),U;case ce:return t.type=C=CS(C),U=Ck(null,t,C,M,l),U;case ue:{if(t.type!==t.elementType){var F=C.propTypes;F&&Co(F,M,"prop",Pt(C))}return U=Ek(null,t,C,Do(C.type,M),l),U}}var X="";throw C!==null&&typeof C=="object"&&C.$$typeof===ut&&(X=" Did you wrap a component in React.lazy() more than once?"),new Error("Element type is invalid. Received a promise that resolves to: "+C+". "+("Lazy element type must resolve to a class or function."+X))}function YP(e,t,a,l,c){zy(e,t),t.tag=$;var p;return ul(a)?(p=!0,Yv(t)):p=!1,Bf(t,c),vk(t,a,l),Lw(t,a,l,c),Ww(null,t,a,!0,p,c)}function GP(e,t,a,l){zy(e,t);var c=t.pendingProps,p;{var v=Lf(t,a,!1);p=zf(t,v)}Bf(t,l);var w,C;ia(t);{if(a.prototype&&typeof a.prototype.render=="function"){var R=Pt(a)||"Unknown";Fw[R]||(y("The <%s /> component appears to have a render method, but doesn't extend React.Component. This is likely to cause errors. Change %s to extend React.Component instead.",R,R),Fw[R]=!0)}t.mode&mt&&To.recordLegacyContextWarning(t,null),Ji(!0),rg.current=t,w=Kf(null,t,a,c,p,l),C=Qf(),Ji(!1)}if(Ko(),t.flags|=yo,typeof w=="object"&&w!==null&&typeof w.render=="function"&&w.$$typeof===void 0){var M=Pt(a)||"Unknown";ig[M]||(y("The <%s /> component appears to be a function component that returns a class instance. Change %s to a class that extends React.Component instead. If you can't use a class try assigning the prototype on the function as a workaround. `%s.prototype = React.Component.prototype`. Don't use an arrow function since it cannot be called with `new` by React.",M,M,M),ig[M]=!0)}if(typeof w=="object"&&w!==null&&typeof w.render=="function"&&w.$$typeof===void 0){{var U=Pt(a)||"Unknown";ig[U]||(y("The <%s /> component appears to be a function component that returns a class instance. Change %s to a class that extends React.Component instead. If you can't use a class try assigning the prototype on the function as a workaround. `%s.prototype = React.Component.prototype`. Don't use an arrow function since it cannot be called with `new` by React.",U,U,U),ig[U]=!0)}t.tag=$,t.memoizedState=null,t.updateQueue=null;var F=!1;return ul(a)?(F=!0,Yv(t)):F=!1,t.memoizedState=w.state!==null&&w.state!==void 0?w.state:null,Hb(t),mk(t,w),Lw(t,a,c,l),Ww(null,t,a,!0,F,l)}else{if(t.tag=O,t.mode&mt){nn(!0);try{w=Kf(null,t,a,c,p,l),C=Qf()}finally{nn(!1)}}return Xr()&&C&&Cb(t),Pi(null,t,w,l),Yw(t,a),t.child}}function Yw(e,t){{if(t&&t.childContextTypes&&y("%s(...): childContextTypes cannot be defined on a function component.",t.displayName||t.name||"Component"),e.ref!==null){var a="",l=Wr();l&&(a+=`

Check the render method of \``+l+"`.");var c=l||"",p=e._debugSource;p&&(c=p.fileName+":"+p.lineNumber),Bw[c]||(Bw[c]=!0,y("Function components cannot be given refs. Attempts to access this ref will fail. Did you mean to use React.forwardRef()?%s",a))}if(t.defaultProps!==void 0){var v=Pt(t)||"Unknown";ag[v]||(y("%s: Support for defaultProps will be removed from function components in a future major release. Use JavaScript default parameters instead.",v),ag[v]=!0)}if(typeof t.getDerivedStateFromProps=="function"){var w=Pt(t)||"Unknown";Uw[w]||(y("%s: Function components do not support getDerivedStateFromProps.",w),Uw[w]=!0)}if(typeof t.contextType=="object"&&t.contextType!==null){var C=Pt(t)||"Unknown";Iw[C]||(y("%s: Function components do not support contextType.",C),Iw[C]=!0)}}}var Gw={dehydrated:null,treeContext:null,retryLane:Jn};function Kw(e){return{baseLanes:e,cachePool:NP(),transitions:null}}function KP(e,t){var a=null;return{baseLanes:xt(e.baseLanes,t),cachePool:a,transitions:e.transitions}}function QP(e,t,a,l){if(t!==null){var c=t.memoizedState;if(c===null)return!1}return Kb(e,Kh)}function qP(e,t){return bc(e.childLanes,t)}function $k(e,t,a){var l=t.pendingProps;aF(t)&&(t.flags|=Et);var c=ko.current,p=!1,v=(t.flags&Et)!==Ye;if(v||QP(c,e)?(p=!0,t.flags&=~Et):(e===null||e.memoizedState!==null)&&(c=hP(c,PT)),c=Vf(c),vu(t,c),e===null){Db(t);var w=t.memoizedState;if(w!==null){var C=w.dehydrated;if(C!==null)return t3(t,C)}var R=l.children,M=l.fallback;if(p){var U=XP(t,R,M,a),F=t.child;return F.memoizedState=Kw(a),t.memoizedState=Gw,U}else return Qw(t,R)}else{var X=e.memoizedState;if(X!==null){var Z=X.dehydrated;if(Z!==null)return n3(e,t,v,l,Z,X,a)}if(p){var te=l.fallback,Re=l.children,Xe=ZP(e,t,Re,te,a),We=t.child,It=e.child.memoizedState;return We.memoizedState=It===null?Kw(a):KP(It,a),We.childLanes=qP(e,a),t.memoizedState=Gw,Xe}else{var _t=l.children,Y=JP(e,t,_t,a);return t.memoizedState=null,Y}}}function Qw(e,t,a){var l=e.mode,c={mode:"visible",children:t},p=qw(c,l);return p.return=e,e.child=p,p}function XP(e,t,a,l){var c=e.mode,p=e.child,v={mode:"hidden",children:t},w,C;return(c&Dt)===Ge&&p!==null?(w=p,w.childLanes=ie,w.pendingProps=v,e.mode&zt&&(w.actualDuration=0,w.actualStartTime=-1,w.selfBaseDuration=0,w.treeBaseDuration=0),C=Tu(a,c,l,null)):(w=qw(v,c),C=Tu(a,c,l,null)),w.return=e,C.return=e,w.sibling=C,e.child=w,C}function qw(e,t,a){return AR(e,t,ie,null)}function Ak(e,t){return Zc(e,t)}function JP(e,t,a,l){var c=e.child,p=c.sibling,v=Ak(c,{mode:"visible",children:a});if((t.mode&Dt)===Ge&&(v.lanes=l),v.return=t,v.sibling=null,p!==null){var w=t.deletions;w===null?(t.deletions=[p],t.flags|=fi):w.push(p)}return t.child=v,v}function ZP(e,t,a,l,c){var p=t.mode,v=e.child,w=v.sibling,C={mode:"hidden",children:a},R;if((p&Dt)===Ge&&t.child!==v){var M=t.child;R=M,R.childLanes=ie,R.pendingProps=C,t.mode&zt&&(R.actualDuration=0,R.actualStartTime=-1,R.selfBaseDuration=v.selfBaseDuration,R.treeBaseDuration=v.treeBaseDuration),t.deletions=null}else R=Ak(v,C),R.subtreeFlags=v.subtreeFlags&Xn;var U;return w!==null?U=Zc(w,l):(U=Tu(l,p,c,null),U.flags|=Ln),U.return=t,R.return=t,R.sibling=U,t.child=R,U}function Ly(e,t,a,l){l!==null&&Mb(l),If(t,e.child,null,a);var c=t.pendingProps,p=c.children,v=Qw(t,p);return v.flags|=Ln,t.memoizedState=null,v}function e3(e,t,a,l,c){var p=t.mode,v={mode:"visible",children:a},w=qw(v,p),C=Tu(l,p,c,null);return C.flags|=Ln,w.return=t,C.return=t,w.sibling=C,t.child=w,(t.mode&Dt)!==Ge&&If(t,e.child,null,c),C}function t3(e,t,a){return(e.mode&Dt)===Ge?(y("Cannot hydrate Suspense in legacy mode. Switch from ReactDOM.hydrate(element, container) to ReactDOMClient.hydrateRoot(container, <App />).render(element) or remove the Suspense components from the server rendered components."),e.lanes=nt):pb(t)?e.lanes=hr:e.lanes=mi,null}function n3(e,t,a,l,c,p,v){if(a)if(t.flags&Rn){t.flags&=~Rn;var Y=zw(new Error("There was an error while hydrating this Suspense boundary. Switched to client rendering."));return Ly(e,t,v,Y)}else{if(t.memoizedState!==null)return t.child=e.child,t.flags|=Et,null;var ne=l.children,G=l.fallback,me=e3(e,t,ne,G,v),Le=t.child;return Le.memoizedState=Kw(v),t.memoizedState=Gw,me}else{if(HN(),(t.mode&Dt)===Ge)return Ly(e,t,v,null);if(pb(c)){var w,C,R;{var M=oN(c);w=M.digest,C=M.message,R=M.stack}var U;C?U=new Error(C):U=new Error("The server could not finish this Suspense boundary, likely due to an error during server rendering. Switched to client rendering.");var F=zw(U,w,R);return Ly(e,t,v,F)}var X=yi(v,e.childLanes);if(Mo||X){var Z=Yy();if(Z!==null){var te=cf(Z,v);if(te!==Jn&&te!==p.retryLane){p.retryLane=te;var Re=rn;ca(e,te),jr(Z,e,te,Re)}}vS();var Xe=zw(new Error("This Suspense boundary received an update before it finished hydrating. This caused the boundary to switch to client rendering. The usual way to fix this is to wrap the original update in startTransition."));return Ly(e,t,v,Xe)}else if(tT(c)){t.flags|=Et,t.child=e.child;var We=R5.bind(null,e);return lN(c,We),null}else{YN(t,c,p.treeContext);var It=l.children,_t=Qw(t,It);return _t.flags|=zn,_t}}}function jk(e,t,a){e.lanes=xt(e.lanes,t);var l=e.alternate;l!==null&&(l.lanes=xt(l.lanes,t)),Fb(e.return,t,a)}function r3(e,t,a){for(var l=t;l!==null;){if(l.tag===le){var c=l.memoizedState;c!==null&&jk(l,a,e)}else if(l.tag===bt)jk(l,a,e);else if(l.child!==null){l.child.return=l,l=l.child;continue}if(l===e)return;for(;l.sibling===null;){if(l.return===null||l.return===e)return;l=l.return}l.sibling.return=l.return,l=l.sibling}}function i3(e){for(var t=e,a=null;t!==null;){var l=t.alternate;l!==null&&py(l)===null&&(a=t),t=t.sibling}return a}function a3(e){if(e!==void 0&&e!=="forwards"&&e!=="backwards"&&e!=="together"&&!Hw[e])if(Hw[e]=!0,typeof e=="string")switch(e.toLowerCase()){case"together":case"forwards":case"backwards":{y('"%s" is not a valid value for revealOrder on <SuspenseList />. Use lowercase "%s" instead.',e,e.toLowerCase());break}case"forward":case"backward":{y('"%s" is not a valid value for revealOrder on <SuspenseList />. React uses the -s suffix in the spelling. Use "%ss" instead.',e,e.toLowerCase());break}default:y('"%s" is not a supported revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',e);break}else y('%s is not a supported value for revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',e)}function o3(e,t){e!==void 0&&!_y[e]&&(e!=="collapsed"&&e!=="hidden"?(_y[e]=!0,y('"%s" is not a supported value for tail on <SuspenseList />. Did you mean "collapsed" or "hidden"?',e)):t!=="forwards"&&t!=="backwards"&&(_y[e]=!0,y('<SuspenseList tail="%s" /> is only valid if revealOrder is "forwards" or "backwards". Did you mean to specify revealOrder="forwards"?',e)))}function _k(e,t){{var a=yt(e),l=!a&&typeof kn(e)=="function";if(a||l){var c=a?"array":"iterable";return y("A nested %s was passed to row #%s in <SuspenseList />. Wrap it in an additional SuspenseList to configure its revealOrder: <SuspenseList revealOrder=...> ... <SuspenseList revealOrder=...>{%s}</SuspenseList> ... </SuspenseList>",c,t,c),!1}}return!0}function l3(e,t){if((t==="forwards"||t==="backwards")&&e!==void 0&&e!==null&&e!==!1)if(yt(e)){for(var a=0;a<e.length;a++)if(!_k(e[a],a))return}else{var l=kn(e);if(typeof l=="function"){var c=l.call(e);if(c)for(var p=c.next(),v=0;!p.done;p=c.next()){if(!_k(p.value,v))return;v++}}else y('A single row was passed to a <SuspenseList revealOrder="%s" />. This is not useful since it needs multiple rows. Did you mean to pass multiple children or an array?',t)}}function Xw(e,t,a,l,c){var p=e.memoizedState;p===null?e.memoizedState={isBackwards:t,rendering:null,renderingStartTime:0,last:l,tail:a,tailMode:c}:(p.isBackwards=t,p.rendering=null,p.renderingStartTime=0,p.last=l,p.tail=a,p.tailMode=c)}function Lk(e,t,a){var l=t.pendingProps,c=l.revealOrder,p=l.tail,v=l.children;a3(c),o3(p,c),l3(v,c),Pi(e,t,v,a);var w=ko.current,C=Kb(w,Kh);if(C)w=Qb(w,Kh),t.flags|=Et;else{var R=e!==null&&(e.flags&Et)!==Ye;R&&r3(t,t.child,a),w=Vf(w)}if(vu(t,w),(t.mode&Dt)===Ge)t.memoizedState=null;else switch(c){case"forwards":{var M=i3(t.child),U;M===null?(U=t.child,t.child=null):(U=M.sibling,M.sibling=null),Xw(t,!1,U,M,p);break}case"backwards":{var F=null,X=t.child;for(t.child=null;X!==null;){var Z=X.alternate;if(Z!==null&&py(Z)===null){t.child=X;break}var te=X.sibling;X.sibling=F,F=X,X=te}Xw(t,!0,F,null,p);break}case"together":{Xw(t,!1,null,null,void 0);break}default:t.memoizedState=null}return t.child}function s3(e,t,a){Wb(t,t.stateNode.containerInfo);var l=t.pendingProps;return e===null?t.child=If(t,null,l,a):Pi(e,t,l,a),t.child}var zk=!1;function u3(e,t,a){var l=t.type,c=l._context,p=t.pendingProps,v=t.memoizedProps,w=p.value;{"value"in p||zk||(zk=!0,y("The `value` prop is required for the `<Context.Provider>`. Did you misspell it or forget to pass it?"));var C=t.type.propTypes;C&&Co(C,p,"prop","Context.Provider")}if(DT(t,c,w),v!==null){var R=v.value;if(Me(R,w)){if(v.children===p.children&&!Vv())return ss(e,t,a)}else aP(t,c,a)}var M=p.children;return Pi(e,t,M,a),t.child}var Nk=!1;function c3(e,t,a){var l=t.type;l._context===void 0?l!==l.Consumer&&(Nk||(Nk=!0,y("Rendering <Context> directly is not supported and will be removed in a future major release. Did you mean to render <Context.Consumer> instead?"))):l=l._context;var c=t.pendingProps,p=c.children;typeof p!="function"&&y("A context consumer was rendered with multiple children, or a child that isn't a function. A context consumer expects a single child that is a function. If you did pass a function, make sure there is no trailing or leading whitespace around it."),Bf(t,a);var v=vr(l);ia(t);var w;return rg.current=t,Ji(!0),w=p(v),Ji(!1),Ko(),t.flags|=yo,Pi(e,t,w,a),t.child}function og(){Mo=!0}function zy(e,t){(t.mode&Dt)===Ge&&e!==null&&(e.alternate=null,t.alternate=null,t.flags|=Ln)}function ss(e,t,a){return e!==null&&(t.dependencies=e.dependencies),fk(),yg(t.lanes),yi(a,t.childLanes)?(rP(e,t),t.child):null}function d3(e,t,a){{var l=t.return;if(l===null)throw new Error("Cannot swap the root fiber.");if(e.alternate=null,t.alternate=null,a.index=t.index,a.sibling=t.sibling,a.return=t.return,a.ref=t.ref,t===l.child)l.child=a;else{var c=l.child;if(c===null)throw new Error("Expected parent to have a child.");for(;c.sibling!==t;)if(c=c.sibling,c===null)throw new Error("Expected to find the previous sibling.");c.sibling=a}var p=l.deletions;return p===null?(l.deletions=[e],l.flags|=fi):p.push(e),a.flags|=Ln,a}}function Jw(e,t){var a=e.lanes;return!!yi(a,t)}function f3(e,t,a){switch(t.tag){case j:Mk(t),t.stateNode,Ff();break;case L:zT(t);break;case $:{var l=t.type;ul(l)&&Yv(t);break}case H:Wb(t,t.stateNode.containerInfo);break;case ae:{var c=t.memoizedProps.value,p=t.type._context;DT(t,p,c);break}case Ee:{var v=yi(a,t.childLanes);v&&(t.flags|=At);{var w=t.stateNode;w.effectDuration=0,w.passiveEffectDuration=0}}break;case le:{var C=t.memoizedState;if(C!==null){if(C.dehydrated!==null)return vu(t,Vf(ko.current)),t.flags|=Et,null;var R=t.child,M=R.childLanes;if(yi(a,M))return $k(e,t,a);vu(t,Vf(ko.current));var U=ss(e,t,a);return U!==null?U.sibling:null}else vu(t,Vf(ko.current));break}case bt:{var F=(e.flags&Et)!==Ye,X=yi(a,t.childLanes);if(F){if(X)return Lk(e,t,a);t.flags|=Et}var Z=t.memoizedState;if(Z!==null&&(Z.rendering=null,Z.tail=null,Z.lastEffect=null),vu(t,ko.current),X)break;return null}case Be:case Ht:return t.lanes=ie,kk(e,t,a)}return ss(e,t,a)}function Pk(e,t,a){if(t._debugNeedsRemount&&e!==null)return d3(e,t,DS(t.type,t.key,t.pendingProps,t._debugOwner||null,t.mode,t.lanes));if(e!==null){var l=e.memoizedProps,c=t.pendingProps;if(l!==c||Vv()||t.type!==e.type)Mo=!0;else{var p=Jw(e,a);if(!p&&(t.flags&Et)===Ye)return Mo=!1,f3(e,t,a);(e.flags&wa)!==Ye?Mo=!0:Mo=!1}}else if(Mo=!1,Xr()&&NN(t)){var v=t.index,w=PN();fT(t,w,v)}switch(t.lanes=ie,t.tag){case P:return GP(e,t,t.type,a);case ft:{var C=t.elementType;return WP(e,t,C,a)}case O:{var R=t.type,M=t.pendingProps,U=t.elementType===R?M:Do(R,M);return Vw(e,t,R,U,a)}case $:{var F=t.type,X=t.pendingProps,Z=t.elementType===F?X:Do(F,X);return Dk(e,t,F,Z,a)}case j:return BP(e,t,a);case L:return HP(e,t,a);case Q:return VP(e,t);case le:return $k(e,t,a);case H:return s3(e,t,a);case ce:{var te=t.type,Re=t.pendingProps,Xe=t.elementType===te?Re:Do(te,Re);return Ck(e,t,te,Xe,a)}case xe:return FP(e,t,a);case ze:return IP(e,t,a);case Ee:return UP(e,t,a);case ae:return u3(e,t,a);case fe:return c3(e,t,a);case ue:{var We=t.type,It=t.pendingProps,_t=Do(We,It);if(t.type!==t.elementType){var Y=We.propTypes;Y&&Co(Y,_t,"prop",Pt(We))}return _t=Do(We.type,_t),Ek(e,t,We,_t,a)}case $e:return Tk(e,t,t.type,t.pendingProps,a);case He:{var ne=t.type,G=t.pendingProps,me=t.elementType===ne?G:Do(ne,G);return YP(e,t,ne,me,a)}case bt:return Lk(e,t,a);case rt:break;case Be:return kk(e,t,a)}throw new Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function qf(e){e.flags|=At}function Fk(e){e.flags|=qn,e.flags|=oc}var Ik,Zw,Uk,Bk;Ik=function(e,t,a,l){for(var c=t.child;c!==null;){if(c.tag===L||c.tag===Q)_z(e,c.stateNode);else if(c.tag!==H){if(c.child!==null){c.child.return=c,c=c.child;continue}}if(c===t)return;for(;c.sibling===null;){if(c.return===null||c.return===t)return;c=c.return}c.sibling.return=c.return,c=c.sibling}},Zw=function(e,t){},Uk=function(e,t,a,l,c){var p=e.memoizedProps;if(p!==l){var v=t.stateNode,w=Yb(),C=zz(v,a,p,l,c,w);t.updateQueue=C,C&&qf(t)}},Bk=function(e,t,a,l){a!==l&&qf(t)};function lg(e,t){if(!Xr())switch(e.tailMode){case"hidden":{for(var a=e.tail,l=null;a!==null;)a.alternate!==null&&(l=a),a=a.sibling;l===null?e.tail=null:l.sibling=null;break}case"collapsed":{for(var c=e.tail,p=null;c!==null;)c.alternate!==null&&(p=c),c=c.sibling;p===null?!t&&e.tail!==null?e.tail.sibling=null:e.tail=null:p.sibling=null;break}}}function Zr(e){var t=e.alternate!==null&&e.alternate.child===e.child,a=ie,l=Ye;if(t){if((e.mode&zt)!==Ge){for(var C=e.selfBaseDuration,R=e.child;R!==null;)a=xt(a,xt(R.lanes,R.childLanes)),l|=R.subtreeFlags&Xn,l|=R.flags&Xn,C+=R.treeBaseDuration,R=R.sibling;e.treeBaseDuration=C}else for(var M=e.child;M!==null;)a=xt(a,xt(M.lanes,M.childLanes)),l|=M.subtreeFlags&Xn,l|=M.flags&Xn,M.return=e,M=M.sibling;e.subtreeFlags|=l}else{if((e.mode&zt)!==Ge){for(var c=e.actualDuration,p=e.selfBaseDuration,v=e.child;v!==null;)a=xt(a,xt(v.lanes,v.childLanes)),l|=v.subtreeFlags,l|=v.flags,c+=v.actualDuration,p+=v.treeBaseDuration,v=v.sibling;e.actualDuration=c,e.treeBaseDuration=p}else for(var w=e.child;w!==null;)a=xt(a,xt(w.lanes,w.childLanes)),l|=w.subtreeFlags,l|=w.flags,w.return=e,w=w.sibling;e.subtreeFlags|=l}return e.childLanes=a,t}function p3(e,t,a){if(XN()&&(t.mode&Dt)!==Ge&&(t.flags&Et)===Ye)return xT(t),Ff(),t.flags|=Rn|zl|Pr,!1;var l=Xv(t);if(a!==null&&a.dehydrated!==null)if(e===null){if(!l)throw new Error("A dehydrated suspense component was completed without a hydrated node. This is probably a bug in React.");if(QN(t),Zr(t),(t.mode&zt)!==Ge){var c=a!==null;if(c){var p=t.child;p!==null&&(t.treeBaseDuration-=p.treeBaseDuration)}}return!1}else{if(Ff(),(t.flags&Et)===Ye&&(t.memoizedState=null),t.flags|=At,Zr(t),(t.mode&zt)!==Ge){var v=a!==null;if(v){var w=t.child;w!==null&&(t.treeBaseDuration-=w.treeBaseDuration)}}return!1}else return bT(),!0}function Hk(e,t,a){var l=t.pendingProps;switch(Eb(t),t.tag){case P:case ft:case $e:case O:case ce:case xe:case ze:case Ee:case fe:case ue:return Zr(t),null;case $:{var c=t.type;return ul(c)&&Wv(t),Zr(t),null}case j:{var p=t.stateNode;if(Hf(t),bb(t),Xb(),p.pendingContext&&(p.context=p.pendingContext,p.pendingContext=null),e===null||e.child===null){var v=Xv(t);if(v)qf(t);else if(e!==null){var w=e.memoizedState;(!w.isDehydrated||(t.flags&Rn)!==Ye)&&(t.flags|=ra,bT())}}return Zw(e,t),Zr(t),null}case L:{Gb(t);var C=LT(),R=t.type;if(e!==null&&t.stateNode!=null)Uk(e,t,R,l,C),e.ref!==t.ref&&Fk(t);else{if(!l){if(t.stateNode===null)throw new Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return Zr(t),null}var M=Yb(),U=Xv(t);if(U)GN(t,C,M)&&qf(t);else{var F=jz(R,l,C,M,t);Ik(F,t,!1,!1),t.stateNode=F,Lz(F,R,l,C)&&qf(t)}t.ref!==null&&Fk(t)}return Zr(t),null}case Q:{var X=l;if(e&&t.stateNode!=null){var Z=e.memoizedProps;Bk(e,t,Z,X)}else{if(typeof X!="string"&&t.stateNode===null)throw new Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");var te=LT(),Re=Yb(),Xe=Xv(t);Xe?KN(t)&&qf(t):t.stateNode=Nz(X,te,Re,t)}return Zr(t),null}case le:{Wf(t);var We=t.memoizedState;if(e===null||e.memoizedState!==null&&e.memoizedState.dehydrated!==null){var It=p3(e,t,We);if(!It)return t.flags&Pr?t:null}if((t.flags&Et)!==Ye)return t.lanes=a,(t.mode&zt)!==Ge&&Cw(t),t;var _t=We!==null,Y=e!==null&&e.memoizedState!==null;if(_t!==Y&&_t){var ne=t.child;if(ne.flags|=Ba,(t.mode&Dt)!==Ge){var G=e===null&&(t.memoizedProps.unstable_avoidThisFallback!==!0||!0);G||Kb(ko.current,PT)?h5():vS()}}var me=t.updateQueue;if(me!==null&&(t.flags|=At),Zr(t),(t.mode&zt)!==Ge&&_t){var Le=t.child;Le!==null&&(t.treeBaseDuration-=Le.treeBaseDuration)}return null}case H:return Hf(t),Zw(e,t),e===null&&ON(t.stateNode.containerInfo),Zr(t),null;case ae:var Oe=t.type._context;return Pb(Oe,t),Zr(t),null;case He:{var ot=t.type;return ul(ot)&&Wv(t),Zr(t),null}case bt:{Wf(t);var dt=t.memoizedState;if(dt===null)return Zr(t),null;var fn=(t.flags&Et)!==Ye,Wt=dt.rendering;if(Wt===null)if(fn)lg(dt,!1);else{var lr=m5()&&(e===null||(e.flags&Et)===Ye);if(!lr)for(var Yt=t.child;Yt!==null;){var er=py(Yt);if(er!==null){fn=!0,t.flags|=Et,lg(dt,!1);var Ei=er.updateQueue;return Ei!==null&&(t.updateQueue=Ei,t.flags|=At),t.subtreeFlags=Ye,iP(t,a),vu(t,Qb(ko.current,Kh)),t.child}Yt=Yt.sibling}dt.tail!==null&&Un()>cR()&&(t.flags|=Et,fn=!0,lg(dt,!1),t.lanes=Qm)}else{if(!fn){var ii=py(Wt);if(ii!==null){t.flags|=Et,fn=!0;var Da=ii.updateQueue;if(Da!==null&&(t.updateQueue=Da,t.flags|=At),lg(dt,!0),dt.tail===null&&dt.tailMode==="hidden"&&!Wt.alternate&&!Xr())return Zr(t),null}else Un()*2-dt.renderingStartTime>cR()&&a!==mi&&(t.flags|=Et,fn=!0,lg(dt,!1),t.lanes=Qm)}if(dt.isBackwards)Wt.sibling=t.child,t.child=Wt;else{var Ui=dt.last;Ui!==null?Ui.sibling=Wt:t.child=Wt,dt.last=Wt}}if(dt.tail!==null){var Bi=dt.tail;dt.rendering=Bi,dt.tail=Bi.sibling,dt.renderingStartTime=Un(),Bi.sibling=null;var Ti=ko.current;return fn?Ti=Qb(Ti,Kh):Ti=Vf(Ti),vu(t,Ti),Bi}return Zr(t),null}case rt:break;case Be:case Ht:{mS(t);var ps=t.memoizedState,ip=ps!==null;if(e!==null){var Cg=e.memoizedState,vl=Cg!==null;vl!==ip&&!B&&(t.flags|=Ba)}return!ip||(t.mode&Dt)===Ge?Zr(t):yi(ml,mi)&&(Zr(t),t.subtreeFlags&(Ln|At)&&(t.flags|=Ba)),null}case kt:return null;case pt:return null}throw new Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function h3(e,t,a){switch(Eb(t),t.tag){case $:{var l=t.type;ul(l)&&Wv(t);var c=t.flags;return c&Pr?(t.flags=c&~Pr|Et,(t.mode&zt)!==Ge&&Cw(t),t):null}case j:{t.stateNode,Hf(t),bb(t),Xb();var p=t.flags;return(p&Pr)!==Ye&&(p&Et)===Ye?(t.flags=p&~Pr|Et,t):null}case L:return Gb(t),null;case le:{Wf(t);var v=t.memoizedState;if(v!==null&&v.dehydrated!==null){if(t.alternate===null)throw new Error("Threw in newly mounted dehydrated component. This is likely a bug in React. Please file an issue.");Ff()}var w=t.flags;return w&Pr?(t.flags=w&~Pr|Et,(t.mode&zt)!==Ge&&Cw(t),t):null}case bt:return Wf(t),null;case H:return Hf(t),null;case ae:var C=t.type._context;return Pb(C,t),null;case Be:case Ht:return mS(t),null;case kt:return null;default:return null}}function Vk(e,t,a){switch(Eb(t),t.tag){case $:{var l=t.type.childContextTypes;l!=null&&Wv(t);break}case j:{t.stateNode,Hf(t),bb(t),Xb();break}case L:{Gb(t);break}case H:Hf(t);break;case le:Wf(t);break;case bt:Wf(t);break;case ae:var c=t.type._context;Pb(c,t);break;case Be:case Ht:mS(t);break}}var Wk=null;Wk=new Set;var Ny=!1,ei=!1,g3=typeof WeakSet=="function"?WeakSet:Set,Ie=null,Xf=null,Jf=null;function m3(e){na(null,function(){throw e}),Pp()}var v3=function(e,t){if(t.props=e.memoizedProps,t.state=e.memoizedState,e.mode&zt)try{hl(),t.componentWillUnmount()}finally{pl(e)}else t.componentWillUnmount()};function Yk(e,t){try{bu(Rr,e)}catch(a){Tn(e,t,a)}}function eS(e,t,a){try{v3(e,a)}catch(l){Tn(e,t,l)}}function y3(e,t,a){try{a.componentDidMount()}catch(l){Tn(e,t,l)}}function Gk(e,t){try{Qk(e)}catch(a){Tn(e,t,a)}}function Zf(e,t){var a=e.ref;if(a!==null)if(typeof a=="function"){var l;try{if(it&&ht&&e.mode&zt)try{hl(),l=a(null)}finally{pl(e)}else l=a(null)}catch(c){Tn(e,t,c)}typeof l=="function"&&y("Unexpected return value from a callback ref in %s. A callback ref should not return a function.",lt(e))}else a.current=null}function Py(e,t,a){try{a()}catch(l){Tn(e,t,l)}}var Kk=!1;function x3(e,t){$z(e.containerInfo),Ie=t,b3();var a=Kk;return Kk=!1,a}function b3(){for(;Ie!==null;){var e=Ie,t=e.child;(e.subtreeFlags&Wo)!==Ye&&t!==null?(t.return=e,Ie=t):w3()}}function w3(){for(;Ie!==null;){var e=Ie;ln(e);try{S3(e)}catch(a){Tn(e,e.return,a)}_n();var t=e.sibling;if(t!==null){t.return=e.return,Ie=t;return}Ie=e.return}}function S3(e){var t=e.alternate,a=e.flags;if((a&ra)!==Ye){switch(ln(e),e.tag){case O:case ce:case $e:break;case $:{if(t!==null){var l=t.memoizedProps,c=t.memoizedState,p=e.stateNode;e.type===e.elementType&&!Kc&&(p.props!==e.memoizedProps&&y("Expected %s props to match memoized props before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(e)||"instance"),p.state!==e.memoizedState&&y("Expected %s state to match memoized state before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(e)||"instance"));var v=p.getSnapshotBeforeUpdate(e.elementType===e.type?l:Do(e.type,l),c);{var w=Wk;v===void 0&&!w.has(e.type)&&(w.add(e.type),y("%s.getSnapshotBeforeUpdate(): A snapshot value (or null) must be returned. You have returned undefined.",lt(e)))}p.__reactInternalSnapshotBeforeUpdate=v}break}case j:{{var C=e.stateNode;nN(C.containerInfo)}break}case L:case Q:case H:case He:break;default:throw new Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}_n()}}function Oo(e,t,a){var l=t.updateQueue,c=l!==null?l.lastEffect:null;if(c!==null){var p=c.next,v=p;do{if((v.tag&e)===e){var w=v.destroy;v.destroy=void 0,w!==void 0&&((e&Jr)!==da?Qo(t):(e&Rr)!==da&&qp(t),(e&cl)!==da&&bg(!0),Py(t,a,w),(e&cl)!==da&&bg(!1),(e&Jr)!==da?zd():(e&Rr)!==da&&Ys())}v=v.next}while(v!==p)}}function bu(e,t){var a=t.updateQueue,l=a!==null?a.lastEffect:null;if(l!==null){var c=l.next,p=c;do{if((p.tag&e)===e){(e&Jr)!==da?Gm(t):(e&Rr)!==da&&Km(t);var v=p.create;(e&cl)!==da&&bg(!0),p.destroy=v(),(e&cl)!==da&&bg(!1),(e&Jr)!==da?bo():(e&Rr)!==da&&Nd();{var w=p.destroy;if(w!==void 0&&typeof w!="function"){var C=void 0;(p.tag&Rr)!==Ye?C="useLayoutEffect":(p.tag&cl)!==Ye?C="useInsertionEffect":C="useEffect";var R=void 0;w===null?R=" You returned null. If your effect does not require clean up, return undefined (or nothing).":typeof w.then=="function"?R=`

It looks like you wrote `+C+`(async () => ...) or returned a Promise. Instead, write the async function inside your effect and call it immediately:

`+C+`(() => {
  async function fetchData() {
    // You can await here
    const response = await MyAPI.getData(someId);
    // ...
  }
  fetchData();
}, [someId]); // Or [] if effect doesn't need props or state

Learn more about data fetching with Hooks: https://reactjs.org/link/hooks-data-fetching`:R=" You returned: "+w,y("%s must not return anything besides a function, which is used for clean-up.%s",C,R)}}}p=p.next}while(p!==c)}}function C3(e,t){if((t.flags&At)!==Ye)switch(t.tag){case Ee:{var a=t.stateNode.passiveEffectDuration,l=t.memoizedProps,c=l.id,p=l.onPostCommit,v=ck(),w=t.alternate===null?"mount":"update";uk()&&(w="nested-update"),typeof p=="function"&&p(c,w,a,v);var C=t.return;e:for(;C!==null;){switch(C.tag){case j:var R=C.stateNode;R.passiveEffectDuration+=a;break e;case Ee:var M=C.stateNode;M.passiveEffectDuration+=a;break e}C=C.return}break}}}function E3(e,t,a,l){if((a.flags&Yo)!==Ye)switch(a.tag){case O:case ce:case $e:{if(!ei)if(a.mode&zt)try{hl(),bu(Rr|kr,a)}finally{pl(a)}else bu(Rr|kr,a);break}case $:{var c=a.stateNode;if(a.flags&At&&!ei)if(t===null)if(a.type===a.elementType&&!Kc&&(c.props!==a.memoizedProps&&y("Expected %s props to match memoized props before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&y("Expected %s state to match memoized state before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),a.mode&zt)try{hl(),c.componentDidMount()}finally{pl(a)}else c.componentDidMount();else{var p=a.elementType===a.type?t.memoizedProps:Do(a.type,t.memoizedProps),v=t.memoizedState;if(a.type===a.elementType&&!Kc&&(c.props!==a.memoizedProps&&y("Expected %s props to match memoized props before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&y("Expected %s state to match memoized state before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),a.mode&zt)try{hl(),c.componentDidUpdate(p,v,c.__reactInternalSnapshotBeforeUpdate)}finally{pl(a)}else c.componentDidUpdate(p,v,c.__reactInternalSnapshotBeforeUpdate)}var w=a.updateQueue;w!==null&&(a.type===a.elementType&&!Kc&&(c.props!==a.memoizedProps&&y("Expected %s props to match memoized props before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&y("Expected %s state to match memoized state before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),_T(a,w,c));break}case j:{var C=a.updateQueue;if(C!==null){var R=null;if(a.child!==null)switch(a.child.tag){case L:R=a.child.stateNode;break;case $:R=a.child.stateNode;break}_T(a,C,R)}break}case L:{var M=a.stateNode;if(t===null&&a.flags&At){var U=a.type,F=a.memoizedProps;Bz(M,U,F)}break}case Q:break;case H:break;case Ee:{{var X=a.memoizedProps,Z=X.onCommit,te=X.onRender,Re=a.stateNode.effectDuration,Xe=ck(),We=t===null?"mount":"update";uk()&&(We="nested-update"),typeof te=="function"&&te(a.memoizedProps.id,We,a.actualDuration,a.treeBaseDuration,a.actualStartTime,Xe);{typeof Z=="function"&&Z(a.memoizedProps.id,We,Re,Xe),w5(a);var It=a.return;e:for(;It!==null;){switch(It.tag){case j:var _t=It.stateNode;_t.effectDuration+=Re;break e;case Ee:var Y=It.stateNode;Y.effectDuration+=Re;break e}It=It.return}}}break}case le:{A3(e,a);break}case bt:case He:case rt:case Be:case Ht:case pt:break;default:throw new Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}ei||a.flags&qn&&Qk(a)}function T3(e){switch(e.tag){case O:case ce:case $e:{if(e.mode&zt)try{hl(),Yk(e,e.return)}finally{pl(e)}else Yk(e,e.return);break}case $:{var t=e.stateNode;typeof t.componentDidMount=="function"&&y3(e,e.return,t),Gk(e,e.return);break}case L:{Gk(e,e.return);break}}}function k3(e,t){for(var a=null,l=e;;){if(l.tag===L){if(a===null){a=l;try{var c=l.stateNode;t?Jz(c):eN(l.stateNode,l.memoizedProps)}catch(v){Tn(e,e.return,v)}}}else if(l.tag===Q){if(a===null)try{var p=l.stateNode;t?Zz(p):tN(p,l.memoizedProps)}catch(v){Tn(e,e.return,v)}}else if(!((l.tag===Be||l.tag===Ht)&&l.memoizedState!==null&&l!==e)){if(l.child!==null){l.child.return=l,l=l.child;continue}}if(l===e)return;for(;l.sibling===null;){if(l.return===null||l.return===e)return;a===l&&(a=null),l=l.return}a===l&&(a=null),l.sibling.return=l.return,l=l.sibling}}function Qk(e){var t=e.ref;if(t!==null){var a=e.stateNode,l;switch(e.tag){case L:l=a;break;default:l=a}if(typeof t=="function"){var c;if(e.mode&zt)try{hl(),c=t(l)}finally{pl(e)}else c=t(l);typeof c=="function"&&y("Unexpected return value from a callback ref in %s. A callback ref should not return a function.",lt(e))}else t.hasOwnProperty("current")||y("Unexpected ref object provided for %s. Use either a ref-setter function or React.createRef().",lt(e)),t.current=l}}function R3(e){var t=e.alternate;t!==null&&(t.return=null),e.return=null}function qk(e){var t=e.alternate;t!==null&&(e.alternate=null,qk(t));{if(e.child=null,e.deletions=null,e.sibling=null,e.tag===L){var a=e.stateNode;a!==null&&jN(a)}e.stateNode=null,e._debugOwner=null,e.return=null,e.dependencies=null,e.memoizedProps=null,e.memoizedState=null,e.pendingProps=null,e.stateNode=null,e.updateQueue=null}}function D3(e){for(var t=e.return;t!==null;){if(Xk(t))return t;t=t.return}throw new Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.")}function Xk(e){return e.tag===L||e.tag===j||e.tag===H}function Jk(e){var t=e;e:for(;;){for(;t.sibling===null;){if(t.return===null||Xk(t.return))return null;t=t.return}for(t.sibling.return=t.return,t=t.sibling;t.tag!==L&&t.tag!==Q&&t.tag!==Tt;){if(t.flags&Ln||t.child===null||t.tag===H)continue e;t.child.return=t,t=t.child}if(!(t.flags&Ln))return t.stateNode}}function M3(e){var t=D3(e);switch(t.tag){case L:{var a=t.stateNode;t.flags&tn&&(eT(a),t.flags&=~tn);var l=Jk(e);nS(e,l,a);break}case j:case H:{var c=t.stateNode.containerInfo,p=Jk(e);tS(e,p,c);break}default:throw new Error("Invalid host parent fiber. This error is likely caused by a bug in React. Please file an issue.")}}function tS(e,t,a){var l=e.tag,c=l===L||l===Q;if(c){var p=e.stateNode;t?Kz(a,p,t):Yz(a,p)}else if(l!==H){var v=e.child;if(v!==null){tS(v,t,a);for(var w=v.sibling;w!==null;)tS(w,t,a),w=w.sibling}}}function nS(e,t,a){var l=e.tag,c=l===L||l===Q;if(c){var p=e.stateNode;t?Gz(a,p,t):Wz(a,p)}else if(l!==H){var v=e.child;if(v!==null){nS(v,t,a);for(var w=v.sibling;w!==null;)nS(w,t,a),w=w.sibling}}}var ti=null,$o=!1;function O3(e,t,a){{var l=t;e:for(;l!==null;){switch(l.tag){case L:{ti=l.stateNode,$o=!1;break e}case j:{ti=l.stateNode.containerInfo,$o=!0;break e}case H:{ti=l.stateNode.containerInfo,$o=!0;break e}}l=l.return}if(ti===null)throw new Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");Zk(e,t,a),ti=null,$o=!1}R3(a)}function wu(e,t,a){for(var l=a.child;l!==null;)Zk(e,t,l),l=l.sibling}function Zk(e,t,a){switch(Ws(a),a.tag){case L:ei||Zf(a,t);case Q:{{var l=ti,c=$o;ti=null,wu(e,t,a),ti=l,$o=c,ti!==null&&($o?qz(ti,a.stateNode):Qz(ti,a.stateNode))}return}case Tt:{ti!==null&&($o?Xz(ti,a.stateNode):fb(ti,a.stateNode));return}case H:{{var p=ti,v=$o;ti=a.stateNode.containerInfo,$o=!0,wu(e,t,a),ti=p,$o=v}return}case O:case ce:case ue:case $e:{if(!ei){var w=a.updateQueue;if(w!==null){var C=w.lastEffect;if(C!==null){var R=C.next,M=R;do{var U=M,F=U.destroy,X=U.tag;F!==void 0&&((X&cl)!==da?Py(a,t,F):(X&Rr)!==da&&(qp(a),a.mode&zt?(hl(),Py(a,t,F),pl(a)):Py(a,t,F),Ys())),M=M.next}while(M!==R)}}}wu(e,t,a);return}case $:{if(!ei){Zf(a,t);var Z=a.stateNode;typeof Z.componentWillUnmount=="function"&&eS(a,t,Z)}wu(e,t,a);return}case rt:{wu(e,t,a);return}case Be:{if(a.mode&Dt){var te=ei;ei=te||a.memoizedState!==null,wu(e,t,a),ei=te}else wu(e,t,a);break}default:{wu(e,t,a);return}}}function $3(e){e.memoizedState}function A3(e,t){var a=t.memoizedState;if(a===null){var l=t.alternate;if(l!==null){var c=l.memoizedState;if(c!==null){var p=c.dehydrated;p!==null&&mN(p)}}}}function eR(e){var t=e.updateQueue;if(t!==null){e.updateQueue=null;var a=e.stateNode;a===null&&(a=e.stateNode=new g3),t.forEach(function(l){var c=D5.bind(null,e,l);if(!a.has(l)){if(a.add(l),Fr)if(Xf!==null&&Jf!==null)xg(Jf,Xf);else throw Error("Expected finished root and lanes to be set. This is a bug in React.");l.then(c,c)}})}}function j3(e,t,a){Xf=a,Jf=e,ln(t),tR(t,e),ln(t),Xf=null,Jf=null}function Ao(e,t,a){var l=t.deletions;if(l!==null)for(var c=0;c<l.length;c++){var p=l[c];try{O3(e,t,p)}catch(C){Tn(p,t,C)}}var v=va();if(t.subtreeFlags&Hs)for(var w=t.child;w!==null;)ln(w),tR(w,e),w=w.sibling;ln(v)}function tR(e,t,a){var l=e.alternate,c=e.flags;switch(e.tag){case O:case ce:case ue:case $e:{if(Ao(t,e),gl(e),c&At){try{Oo(cl|kr,e,e.return),bu(cl|kr,e)}catch(ot){Tn(e,e.return,ot)}if(e.mode&zt){try{hl(),Oo(Rr|kr,e,e.return)}catch(ot){Tn(e,e.return,ot)}pl(e)}else try{Oo(Rr|kr,e,e.return)}catch(ot){Tn(e,e.return,ot)}}return}case $:{Ao(t,e),gl(e),c&qn&&l!==null&&Zf(l,l.return);return}case L:{Ao(t,e),gl(e),c&qn&&l!==null&&Zf(l,l.return);{if(e.flags&tn){var p=e.stateNode;try{eT(p)}catch(ot){Tn(e,e.return,ot)}}if(c&At){var v=e.stateNode;if(v!=null){var w=e.memoizedProps,C=l!==null?l.memoizedProps:w,R=e.type,M=e.updateQueue;if(e.updateQueue=null,M!==null)try{Hz(v,M,R,C,w,e)}catch(ot){Tn(e,e.return,ot)}}}}return}case Q:{if(Ao(t,e),gl(e),c&At){if(e.stateNode===null)throw new Error("This should have a text node initialized. This error is likely caused by a bug in React. Please file an issue.");var U=e.stateNode,F=e.memoizedProps,X=l!==null?l.memoizedProps:F;try{Vz(U,X,F)}catch(ot){Tn(e,e.return,ot)}}return}case j:{if(Ao(t,e),gl(e),c&At&&l!==null){var Z=l.memoizedState;if(Z.isDehydrated)try{gN(t.containerInfo)}catch(ot){Tn(e,e.return,ot)}}return}case H:{Ao(t,e),gl(e);return}case le:{Ao(t,e),gl(e);var te=e.child;if(te.flags&Ba){var Re=te.stateNode,Xe=te.memoizedState,We=Xe!==null;if(Re.isHidden=We,We){var It=te.alternate!==null&&te.alternate.memoizedState!==null;It||p5()}}if(c&At){try{$3(e)}catch(ot){Tn(e,e.return,ot)}eR(e)}return}case Be:{var _t=l!==null&&l.memoizedState!==null;if(e.mode&Dt){var Y=ei;ei=Y||_t,Ao(t,e),ei=Y}else Ao(t,e);if(gl(e),c&Ba){var ne=e.stateNode,G=e.memoizedState,me=G!==null,Le=e;if(ne.isHidden=me,me&&!_t&&(Le.mode&Dt)!==Ge){Ie=Le;for(var Oe=Le.child;Oe!==null;)Ie=Oe,L3(Oe),Oe=Oe.sibling}k3(Le,me)}return}case bt:{Ao(t,e),gl(e),c&At&&eR(e);return}case rt:return;default:{Ao(t,e),gl(e);return}}}function gl(e){var t=e.flags;if(t&Ln){try{M3(e)}catch(a){Tn(e,e.return,a)}e.flags&=~Ln}t&zn&&(e.flags&=~zn)}function _3(e,t,a){Xf=a,Jf=t,Ie=e,nR(e,t,a),Xf=null,Jf=null}function nR(e,t,a){for(var l=(e.mode&Dt)!==Ge;Ie!==null;){var c=Ie,p=c.child;if(c.tag===Be&&l){var v=c.memoizedState!==null,w=v||Ny;if(w){rS(e,t,a);continue}else{var C=c.alternate,R=C!==null&&C.memoizedState!==null,M=R||ei,U=Ny,F=ei;Ny=w,ei=M,ei&&!F&&(Ie=c,z3(c));for(var X=p;X!==null;)Ie=X,nR(X,t,a),X=X.sibling;Ie=c,Ny=U,ei=F,rS(e,t,a);continue}}(c.subtreeFlags&Yo)!==Ye&&p!==null?(p.return=c,Ie=p):rS(e,t,a)}}function rS(e,t,a){for(;Ie!==null;){var l=Ie;if((l.flags&Yo)!==Ye){var c=l.alternate;ln(l);try{E3(t,c,l,a)}catch(v){Tn(l,l.return,v)}_n()}if(l===e){Ie=null;return}var p=l.sibling;if(p!==null){p.return=l.return,Ie=p;return}Ie=l.return}}function L3(e){for(;Ie!==null;){var t=Ie,a=t.child;switch(t.tag){case O:case ce:case ue:case $e:{if(t.mode&zt)try{hl(),Oo(Rr,t,t.return)}finally{pl(t)}else Oo(Rr,t,t.return);break}case $:{Zf(t,t.return);var l=t.stateNode;typeof l.componentWillUnmount=="function"&&eS(t,t.return,l);break}case L:{Zf(t,t.return);break}case Be:{var c=t.memoizedState!==null;if(c){rR(e);continue}break}}a!==null?(a.return=t,Ie=a):rR(e)}}function rR(e){for(;Ie!==null;){var t=Ie;if(t===e){Ie=null;return}var a=t.sibling;if(a!==null){a.return=t.return,Ie=a;return}Ie=t.return}}function z3(e){for(;Ie!==null;){var t=Ie,a=t.child;if(t.tag===Be){var l=t.memoizedState!==null;if(l){iR(e);continue}}a!==null?(a.return=t,Ie=a):iR(e)}}function iR(e){for(;Ie!==null;){var t=Ie;ln(t);try{T3(t)}catch(l){Tn(t,t.return,l)}if(_n(),t===e){Ie=null;return}var a=t.sibling;if(a!==null){a.return=t.return,Ie=a;return}Ie=t.return}}function N3(e,t,a,l){Ie=t,P3(t,e,a,l)}function P3(e,t,a,l){for(;Ie!==null;){var c=Ie,p=c.child;(c.subtreeFlags&wr)!==Ye&&p!==null?(p.return=c,Ie=p):F3(e,t,a,l)}}function F3(e,t,a,l){for(;Ie!==null;){var c=Ie;if((c.flags&Ai)!==Ye){ln(c);try{I3(t,c,a,l)}catch(v){Tn(c,c.return,v)}_n()}if(c===e){Ie=null;return}var p=c.sibling;if(p!==null){p.return=c.return,Ie=p;return}Ie=c.return}}function I3(e,t,a,l){switch(t.tag){case O:case ce:case $e:{if(t.mode&zt){Sw();try{bu(Jr|kr,t)}finally{ww(t)}}else bu(Jr|kr,t);break}}}function U3(e){Ie=e,B3()}function B3(){for(;Ie!==null;){var e=Ie,t=e.child;if((Ie.flags&fi)!==Ye){var a=e.deletions;if(a!==null){for(var l=0;l<a.length;l++){var c=a[l];Ie=c,W3(c,e)}{var p=e.alternate;if(p!==null){var v=p.child;if(v!==null){p.child=null;do{var w=v.sibling;v.sibling=null,v=w}while(v!==null)}}}Ie=e}}(e.subtreeFlags&wr)!==Ye&&t!==null?(t.return=e,Ie=t):H3()}}function H3(){for(;Ie!==null;){var e=Ie;(e.flags&Ai)!==Ye&&(ln(e),V3(e),_n());var t=e.sibling;if(t!==null){t.return=e.return,Ie=t;return}Ie=e.return}}function V3(e){switch(e.tag){case O:case ce:case $e:{e.mode&zt?(Sw(),Oo(Jr|kr,e,e.return),ww(e)):Oo(Jr|kr,e,e.return);break}}}function W3(e,t){for(;Ie!==null;){var a=Ie;ln(a),G3(a,t),_n();var l=a.child;l!==null?(l.return=a,Ie=l):Y3(e)}}function Y3(e){for(;Ie!==null;){var t=Ie,a=t.sibling,l=t.return;if(qk(t),t===e){Ie=null;return}if(a!==null){a.return=l,Ie=a;return}Ie=l}}function G3(e,t){switch(e.tag){case O:case ce:case $e:{e.mode&zt?(Sw(),Oo(Jr,e,t),ww(e)):Oo(Jr,e,t);break}}}function K3(e){switch(e.tag){case O:case ce:case $e:{try{bu(Rr|kr,e)}catch(a){Tn(e,e.return,a)}break}case $:{var t=e.stateNode;try{t.componentDidMount()}catch(a){Tn(e,e.return,a)}break}}}function Q3(e){switch(e.tag){case O:case ce:case $e:{try{bu(Jr|kr,e)}catch(t){Tn(e,e.return,t)}break}}}function q3(e){switch(e.tag){case O:case ce:case $e:{try{Oo(Rr|kr,e,e.return)}catch(a){Tn(e,e.return,a)}break}case $:{var t=e.stateNode;typeof t.componentWillUnmount=="function"&&eS(e,e.return,t);break}}}function X3(e){switch(e.tag){case O:case ce:case $e:try{Oo(Jr|kr,e,e.return)}catch(t){Tn(e,e.return,t)}}}if(typeof Symbol=="function"&&Symbol.for){var sg=Symbol.for;sg("selector.component"),sg("selector.has_pseudo_class"),sg("selector.role"),sg("selector.test_id"),sg("selector.text")}var J3=[];function Z3(){J3.forEach(function(e){return e()})}var e5=d.ReactCurrentActQueue;function t5(e){{var t=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0,a=typeof jest<"u";return a&&t!==!1}}function aR(){{var e=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0;return!e&&e5.current!==null&&y("The current testing environment is not configured to support act(...)"),e}}var n5=Math.ceil,iS=d.ReactCurrentDispatcher,aS=d.ReactCurrentOwner,ni=d.ReactCurrentBatchConfig,jo=d.ReactCurrentActQueue,Or=0,oR=1,ri=2,Za=4,us=0,ug=1,Qc=2,Fy=3,cg=4,lR=5,oS=6,Ft=Or,Fi=null,Wn=null,$r=ie,ml=ie,lS=du(ie),Ar=us,dg=null,Iy=ie,fg=ie,Uy=ie,pg=null,fa=null,sS=0,sR=500,uR=1/0,r5=500,cs=null;function hg(){uR=Un()+r5}function cR(){return uR}var By=!1,uS=null,ep=null,qc=!1,Su=null,gg=ie,cS=[],dS=null,i5=50,mg=0,fS=null,pS=!1,Hy=!1,a5=50,tp=0,Vy=null,vg=rn,Wy=ie,dR=!1;function Yy(){return Fi}function Ii(){return(Ft&(ri|Za))!==Or?Un():(vg!==rn||(vg=Un()),vg)}function Cu(e){var t=e.mode;if((t&Dt)===Ge)return nt;if((Ft&ri)!==Or&&$r!==ie)return gr($r);var a=eP()!==ZN;if(a){if(ni.transition!==null){var l=ni.transition;l._updatedFibers||(l._updatedFibers=new Set),l._updatedFibers.add(e)}return Wy===Jn&&(Wy=oh()),Wy}var c=_i();if(c!==Jn)return c;var p=Pz();return p}function o5(e){var t=e.mode;return(t&Dt)===Ge?nt:tv()}function jr(e,t,a,l){O5(),dR&&y("useInsertionEffect must not schedule updates."),pS&&(Hy=!0),Js(e,a,l),(Ft&ri)!==ie&&e===Fi?j5(t):(Fr&&rv(e,t,a),_5(t),e===Fi&&((Ft&ri)===Or&&(fg=xt(fg,a)),Ar===cg&&Eu(e,$r)),pa(e,l),a===nt&&Ft===Or&&(t.mode&Dt)===Ge&&!jo.isBatchingLegacy&&(hg(),dT()))}function l5(e,t,a){var l=e.current;l.lanes=t,Js(e,t,a),pa(e,a)}function s5(e){return(Ft&ri)!==Or}function pa(e,t){var a=e.callbackNode;Jm(e,t);var l=vi(e,e===Fi?$r:ie);if(l===ie){a!==null&&RR(a),e.callbackNode=null,e.callbackPriority=Jn;return}var c=Vl(l),p=e.callbackPriority;if(p===c&&!(jo.current!==null&&a!==bS)){a==null&&p!==nt&&y("Expected scheduled callback to exist. This error is likely caused by a bug in React. Please file an issue.");return}a!=null&&RR(a);var v;if(c===nt)e.tag===fu?(jo.isBatchingLegacy!==null&&(jo.didScheduleLegacyUpdate=!0),zN(hR.bind(null,e))):cT(hR.bind(null,e)),jo.current!==null?jo.current.push(pu):Iz(function(){(Ft&(ri|Za))===Or&&pu()}),v=null;else{var w;switch(av(l)){case xi:w=xo;break;case aa:w=lc;break;case Cr:w=Pl;break;case ff:w=Vs;break;default:w=Pl;break}v=wS(w,fR.bind(null,e))}e.callbackPriority=c,e.callbackNode=v}function fR(e,t){if(kP(),vg=rn,Wy=ie,(Ft&(ri|Za))!==Or)throw new Error("Should not already be working.");var a=e.callbackNode,l=fs();if(l&&e.callbackNode!==a)return null;var c=vi(e,e===Fi?$r:ie);if(c===ie)return null;var p=!xc(e,c)&&!ev(e,c)&&!t,v=p?y5(e,c):Ky(e,c);if(v!==us){if(v===Qc){var w=rf(e);w!==ie&&(c=w,v=hS(e,w))}if(v===ug){var C=dg;throw Xc(e,ie),Eu(e,c),pa(e,Un()),C}if(v===oS)Eu(e,c);else{var R=!xc(e,c),M=e.current.alternate;if(R&&!c5(M)){if(v=Ky(e,c),v===Qc){var U=rf(e);U!==ie&&(c=U,v=hS(e,U))}if(v===ug){var F=dg;throw Xc(e,ie),Eu(e,c),pa(e,Un()),F}}e.finishedWork=M,e.finishedLanes=c,u5(e,v,c)}}return pa(e,Un()),e.callbackNode===a?fR.bind(null,e):null}function hS(e,t){var a=pg;if(Yl(e)){var l=Xc(e,t);l.flags|=Rn,MN(e.containerInfo)}var c=Ky(e,t);if(c!==Qc){var p=fa;fa=a,p!==null&&pR(p)}return c}function pR(e){fa===null?fa=e:fa.push.apply(fa,e)}function u5(e,t,a){switch(t){case us:case ug:throw new Error("Root did not complete. This is a bug in React.");case Qc:{Jc(e,fa,cs);break}case Fy:{if(Eu(e,a),af(a)&&!DR()){var l=sS+sR-Un();if(l>10){var c=vi(e,ie);if(c!==ie)break;var p=e.suspendedLanes;if(!Wl(p,a)){Ii(),uf(e,p);break}e.timeoutHandle=cb(Jc.bind(null,e,fa,cs),l);break}}Jc(e,fa,cs);break}case cg:{if(Eu(e,a),L0(a))break;if(!DR()){var v=nh(e,a),w=v,C=Un()-w,R=M5(C)-C;if(R>10){e.timeoutHandle=cb(Jc.bind(null,e,fa,cs),R);break}}Jc(e,fa,cs);break}case lR:{Jc(e,fa,cs);break}default:throw new Error("Unknown root exit status.")}}function c5(e){for(var t=e;;){if(t.flags&_d){var a=t.updateQueue;if(a!==null){var l=a.stores;if(l!==null)for(var c=0;c<l.length;c++){var p=l[c],v=p.getSnapshot,w=p.value;try{if(!Me(v(),w))return!1}catch{return!1}}}}var C=t.child;if(t.subtreeFlags&_d&&C!==null){C.return=t,t=C;continue}if(t===e)return!0;for(;t.sibling===null;){if(t.return===null||t.return===e)return!0;t=t.return}t.sibling.return=t.return,t=t.sibling}return!0}function Eu(e,t){t=bc(t,Uy),t=bc(t,fg),sh(e,t)}function hR(e){if(RP(),(Ft&(ri|Za))!==Or)throw new Error("Should not already be working.");fs();var t=vi(e,ie);if(!yi(t,nt))return pa(e,Un()),null;var a=Ky(e,t);if(e.tag!==fu&&a===Qc){var l=rf(e);l!==ie&&(t=l,a=hS(e,l))}if(a===ug){var c=dg;throw Xc(e,ie),Eu(e,t),pa(e,Un()),c}if(a===oS)throw new Error("Root did not complete. This is a bug in React.");var p=e.current.alternate;return e.finishedWork=p,e.finishedLanes=t,Jc(e,fa,cs),pa(e,Un()),null}function d5(e,t){t!==ie&&(wc(e,xt(t,nt)),pa(e,Un()),(Ft&(ri|Za))===Or&&(hg(),pu()))}function gS(e,t){var a=Ft;Ft|=oR;try{return e(t)}finally{Ft=a,Ft===Or&&!jo.isBatchingLegacy&&(hg(),dT())}}function f5(e,t,a,l,c){var p=_i(),v=ni.transition;try{return ni.transition=null,ir(xi),e(t,a,l,c)}finally{ir(p),ni.transition=v,Ft===Or&&hg()}}function ds(e){Su!==null&&Su.tag===fu&&(Ft&(ri|Za))===Or&&fs();var t=Ft;Ft|=oR;var a=ni.transition,l=_i();try{return ni.transition=null,ir(xi),e?e():void 0}finally{ir(l),ni.transition=a,Ft=t,(Ft&(ri|Za))===Or&&pu()}}function gR(){return(Ft&(ri|Za))!==Or}function Gy(e,t){Si(lS,ml,e),ml=xt(ml,t)}function mS(e){ml=lS.current,wi(lS,e)}function Xc(e,t){e.finishedWork=null,e.finishedLanes=ie;var a=e.timeoutHandle;if(a!==db&&(e.timeoutHandle=db,Fz(a)),Wn!==null)for(var l=Wn.return;l!==null;){var c=l.alternate;Vk(c,l),l=l.return}Fi=e;var p=Zc(e.current,null);return Wn=p,$r=ml=t,Ar=us,dg=null,Iy=ie,fg=ie,Uy=ie,pg=null,fa=null,lP(),To.discardPendingWarnings(),p}function mR(e,t){do{var a=Wn;try{if(ry(),IT(),_n(),aS.current=null,a===null||a.return===null){Ar=ug,dg=t,Wn=null;return}if(it&&a.mode&zt&&Ay(a,!0),et)if(Ko(),t!==null&&typeof t=="object"&&typeof t.then=="function"){var l=t;uc(a,l,$r)}else Wa(a,t,$r);zP(e,a.return,a,t,$r),bR(a)}catch(c){t=c,Wn===a&&a!==null?(a=a.return,Wn=a):a=Wn;continue}return}while(!0)}function vR(){var e=iS.current;return iS.current=Ry,e===null?Ry:e}function yR(e){iS.current=e}function p5(){sS=Un()}function yg(e){Iy=xt(e,Iy)}function h5(){Ar===us&&(Ar=Fy)}function vS(){(Ar===us||Ar===Fy||Ar===Qc)&&(Ar=cg),Fi!==null&&(Zo(Iy)||Zo(fg))&&Eu(Fi,$r)}function g5(e){Ar!==cg&&(Ar=Qc),pg===null?pg=[e]:pg.push(e)}function m5(){return Ar===us}function Ky(e,t){var a=Ft;Ft|=ri;var l=vR();if(Fi!==e||$r!==t){if(Fr){var c=e.memoizedUpdaters;c.size>0&&(xg(e,$r),c.clear()),uh(e,t)}cs=df(),Xc(e,t)}Jp(t);do try{v5();break}catch(p){mR(e,p)}while(!0);if(ry(),Ft=a,yR(l),Wn!==null)throw new Error("Cannot commit an incomplete root. This error is likely caused by a bug in React. Please file an issue.");return Dn(),Fi=null,$r=ie,Ar}function v5(){for(;Wn!==null;)xR(Wn)}function y5(e,t){var a=Ft;Ft|=ri;var l=vR();if(Fi!==e||$r!==t){if(Fr){var c=e.memoizedUpdaters;c.size>0&&(xg(e,$r),c.clear()),uh(e,t)}cs=df(),hg(),Xc(e,t)}Jp(t);do try{x5();break}catch(p){mR(e,p)}while(!0);return ry(),yR(l),Ft=a,Wn!==null?(Zp(),us):(Dn(),Fi=null,$r=ie,Ar)}function x5(){for(;Wn!==null&&!Vp();)xR(Wn)}function xR(e){var t=e.alternate;ln(e);var a;(e.mode&zt)!==Ge?(bw(e),a=yS(t,e,ml),Ay(e,!0)):a=yS(t,e,ml),_n(),e.memoizedProps=e.pendingProps,a===null?bR(e):Wn=a,aS.current=null}function bR(e){var t=e;do{var a=t.alternate,l=t.return;if((t.flags&zl)===Ye){ln(t);var c=void 0;if((t.mode&zt)===Ge?c=Hk(a,t,ml):(bw(t),c=Hk(a,t,ml),Ay(t,!1)),_n(),c!==null){Wn=c;return}}else{var p=h3(a,t);if(p!==null){p.flags&=Im,Wn=p;return}if((t.mode&zt)!==Ge){Ay(t,!1);for(var v=t.actualDuration,w=t.child;w!==null;)v+=w.actualDuration,w=w.sibling;t.actualDuration=v}if(l!==null)l.flags|=zl,l.subtreeFlags=Ye,l.deletions=null;else{Ar=oS,Wn=null;return}}var C=t.sibling;if(C!==null){Wn=C;return}t=l,Wn=t}while(t!==null);Ar===us&&(Ar=lR)}function Jc(e,t,a){var l=_i(),c=ni.transition;try{ni.transition=null,ir(xi),b5(e,t,a,l)}finally{ni.transition=c,ir(l)}return null}function b5(e,t,a,l){do fs();while(Su!==null);if($5(),(Ft&(ri|Za))!==Or)throw new Error("Should not already be working.");var c=e.finishedWork,p=e.finishedLanes;if(Ym(p),c===null)return Va(),null;if(p===ie&&y("root.finishedLanes should not be empty during a commit. This is a bug in React."),e.finishedWork=null,e.finishedLanes=ie,c===e.current)throw new Error("Cannot commit the same tree as before. This error is likely caused by a bug in React. Please file an issue.");e.callbackNode=null,e.callbackPriority=Jn;var v=xt(c.lanes,c.childLanes);nv(e,v),e===Fi&&(Fi=null,Wn=null,$r=ie),((c.subtreeFlags&wr)!==Ye||(c.flags&wr)!==Ye)&&(qc||(qc=!0,dS=a,wS(Pl,function(){return fs(),null})));var w=(c.subtreeFlags&(Wo|Hs|Yo|wr))!==Ye,C=(c.flags&(Wo|Hs|Yo|wr))!==Ye;if(w||C){var R=ni.transition;ni.transition=null;var M=_i();ir(xi);var U=Ft;Ft|=Za,aS.current=null,x3(e,c),dk(),j3(e,c,p),Az(e.containerInfo),e.current=c,cc(p),_3(c,e,p),Il(),Bm(),Ft=U,ir(M),ni.transition=R}else e.current=c,dk();var F=qc;if(qc?(qc=!1,Su=e,gg=p):(tp=0,Vy=null),v=e.pendingLanes,v===ie&&(ep=null),F||ER(e.current,!1),Gp(c.stateNode,l),Fr&&e.memoizedUpdaters.clear(),Z3(),pa(e,Un()),t!==null)for(var X=e.onRecoverableError,Z=0;Z<t.length;Z++){var te=t[Z],Re=te.stack,Xe=te.digest;X(te.value,{componentStack:Re,digest:Xe})}if(By){By=!1;var We=uS;throw uS=null,We}return yi(gg,nt)&&e.tag!==fu&&fs(),v=e.pendingLanes,yi(v,nt)?(TP(),e===fS?mg++:(mg=0,fS=e)):mg=0,pu(),Va(),null}function fs(){if(Su!==null){var e=av(gg),t=Ir(Cr,e),a=ni.transition,l=_i();try{return ni.transition=null,ir(t),S5()}finally{ir(l),ni.transition=a}}return!1}function w5(e){cS.push(e),qc||(qc=!0,wS(Pl,function(){return fs(),null}))}function S5(){if(Su===null)return!1;var e=dS;dS=null;var t=Su,a=gg;if(Su=null,gg=ie,(Ft&(ri|Za))!==Or)throw new Error("Cannot flush passive effects while already rendering.");pS=!0,Hy=!1,Xp(a);var l=Ft;Ft|=Za,U3(t.current),N3(t,t.current,a,e);{var c=cS;cS=[];for(var p=0;p<c.length;p++){var v=c[p];C3(t,v)}}Gs(),ER(t.current,!0),Ft=l,pu(),Hy?t===Vy?tp++:(tp=0,Vy=t):tp=0,pS=!1,Hy=!1,Kp(t);{var w=t.current.stateNode;w.effectDuration=0,w.passiveEffectDuration=0}return!0}function wR(e){return ep!==null&&ep.has(e)}function C5(e){ep===null?ep=new Set([e]):ep.add(e)}function E5(e){By||(By=!0,uS=e)}var T5=E5;function SR(e,t,a){var l=Gc(a,t),c=xk(e,l,nt),p=gu(e,c,nt),v=Ii();p!==null&&(Js(p,nt,v),pa(p,v))}function Tn(e,t,a){if(m3(a),bg(!1),e.tag===j){SR(e,e,a);return}var l=null;for(l=t;l!==null;){if(l.tag===j){SR(l,e,a);return}else if(l.tag===$){var c=l.type,p=l.stateNode;if(typeof c.getDerivedStateFromError=="function"||typeof p.componentDidCatch=="function"&&!wR(p)){var v=Gc(a,e),w=Pw(l,v,nt),C=gu(l,w,nt),R=Ii();C!==null&&(Js(C,nt,R),pa(C,R));return}}l=l.return}y(`Internal React error: Attempted to capture a commit phase error inside a detached tree. This indicates a bug in React. Likely causes include deleting the same fiber more than once, committing an already-finished tree, or an inconsistent return pointer.

Error message:

%s`,a)}function k5(e,t,a){var l=e.pingCache;l!==null&&l.delete(t);var c=Ii();uf(e,a),L5(e),Fi===e&&Wl($r,a)&&(Ar===cg||Ar===Fy&&af($r)&&Un()-sS<sR?Xc(e,ie):Uy=xt(Uy,a)),pa(e,c)}function CR(e,t){t===Jn&&(t=o5(e));var a=Ii(),l=ca(e,t);l!==null&&(Js(l,t,a),pa(l,a))}function R5(e){var t=e.memoizedState,a=Jn;t!==null&&(a=t.retryLane),CR(e,a)}function D5(e,t){var a=Jn,l;switch(e.tag){case le:l=e.stateNode;var c=e.memoizedState;c!==null&&(a=c.retryLane);break;case bt:l=e.stateNode;break;default:throw new Error("Pinged unknown suspense boundary type. This is probably a bug in React.")}l!==null&&l.delete(t),CR(e,a)}function M5(e){return e<120?120:e<480?480:e<1080?1080:e<1920?1920:e<3e3?3e3:e<4320?4320:n5(e/1960)*1960}function O5(){if(mg>i5)throw mg=0,fS=null,new Error("Maximum update depth exceeded. This can happen when a component repeatedly calls setState inside componentWillUpdate or componentDidUpdate. React limits the number of nested updates to prevent infinite loops.");tp>a5&&(tp=0,Vy=null,y("Maximum update depth exceeded. This can happen when a component calls setState inside useEffect, but useEffect either doesn't have a dependency array, or one of the dependencies changes on every render."))}function $5(){To.flushLegacyContextWarning(),To.flushPendingUnsafeLifecycleWarnings()}function ER(e,t){ln(e),Qy(e,Gr,q3),t&&Qy(e,Vo,X3),Qy(e,Gr,K3),t&&Qy(e,Vo,Q3),_n()}function Qy(e,t,a){for(var l=e,c=null;l!==null;){var p=l.subtreeFlags&t;l!==c&&l.child!==null&&p!==Ye?l=l.child:((l.flags&t)!==Ye&&a(l),l.sibling!==null?l=l.sibling:l=c=l.return)}}var qy=null;function TR(e){{if((Ft&ri)!==Or||!(e.mode&Dt))return;var t=e.tag;if(t!==P&&t!==j&&t!==$&&t!==O&&t!==ce&&t!==ue&&t!==$e)return;var a=lt(e)||"ReactComponent";if(qy!==null){if(qy.has(a))return;qy.add(a)}else qy=new Set([a]);var l=fr;try{ln(e),y("Can't perform a React state update on a component that hasn't mounted yet. This indicates that you have a side-effect in your render function that asynchronously later calls tries to update the component. Move this work to useEffect instead.")}finally{l?ln(e):_n()}}}var yS;{var A5=null;yS=function(e,t,a){var l=jR(A5,t);try{return Pk(e,t,a)}catch(p){if(VN()||p!==null&&typeof p=="object"&&typeof p.then=="function")throw p;if(ry(),IT(),Vk(e,t),jR(t,l),t.mode&zt&&bw(t),na(null,Pk,null,e,t,a),Np()){var c=Pp();typeof c=="object"&&c!==null&&c._suppressLogging&&typeof p=="object"&&p!==null&&!p._suppressLogging&&(p._suppressLogging=!0)}throw p}}}var kR=!1,xS;xS=new Set;function j5(e){if(ci&&!SP())switch(e.tag){case O:case ce:case $e:{var t=Wn&&lt(Wn)||"Unknown",a=t;if(!xS.has(a)){xS.add(a);var l=lt(e)||"Unknown";y("Cannot update a component (`%s`) while rendering a different component (`%s`). To locate the bad setState() call inside `%s`, follow the stack trace as described in https://reactjs.org/link/setstate-in-render",l,t,t)}break}case $:{kR||(y("Cannot update during an existing state transition (such as within `render`). Render methods should be a pure function of props and state."),kR=!0);break}}}function xg(e,t){if(Fr){var a=e.memoizedUpdaters;a.forEach(function(l){rv(e,l,t)})}}var bS={};function wS(e,t){{var a=jo.current;return a!==null?(a.push(t),bS):Bp(e,t)}}function RR(e){if(e!==bS)return Hp(e)}function DR(){return jo.current!==null}function _5(e){{if(e.mode&Dt){if(!aR())return}else if(!t5()||Ft!==Or||e.tag!==O&&e.tag!==ce&&e.tag!==$e)return;if(jo.current===null){var t=fr;try{ln(e),y(`An update to %s inside a test was not wrapped in act(...).

When testing, code that causes React state updates should be wrapped into act(...):

act(() => {
  /* fire events that update state */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://reactjs.org/link/wrap-tests-with-act`,lt(e))}finally{t?ln(e):_n()}}}}function L5(e){e.tag!==fu&&aR()&&jo.current===null&&y(`A suspended resource finished loading inside a test, but the event was not wrapped in act(...).

When testing, code that resolves suspended data should be wrapped into act(...):

act(() => {
  /* finish loading suspended data */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://reactjs.org/link/wrap-tests-with-act`)}function bg(e){dR=e}var eo=null,np=null,z5=function(e){eo=e};function rp(e){{if(eo===null)return e;var t=eo(e);return t===void 0?e:t.current}}function SS(e){return rp(e)}function CS(e){{if(eo===null)return e;var t=eo(e);if(t===void 0){if(e!=null&&typeof e.render=="function"){var a=rp(e.render);if(e.render!==a){var l={$$typeof:de,render:a};return e.displayName!==void 0&&(l.displayName=e.displayName),l}}return e}return t.current}}function MR(e,t){{if(eo===null)return!1;var a=e.elementType,l=t.type,c=!1,p=typeof l=="object"&&l!==null?l.$$typeof:null;switch(e.tag){case $:{typeof l=="function"&&(c=!0);break}case O:{(typeof l=="function"||p===ut)&&(c=!0);break}case ce:{(p===de||p===ut)&&(c=!0);break}case ue:case $e:{(p===Rt||p===ut)&&(c=!0);break}default:return!1}if(c){var v=eo(a);if(v!==void 0&&v===eo(l))return!0}return!1}}function OR(e){{if(eo===null||typeof WeakSet!="function")return;np===null&&(np=new WeakSet),np.add(e)}}var N5=function(e,t){{if(eo===null)return;var a=t.staleFamilies,l=t.updatedFamilies;fs(),ds(function(){ES(e.current,l,a)})}},P5=function(e,t){{if(e.context!==ka)return;fs(),ds(function(){wg(t,e,null,null)})}};function ES(e,t,a){{var l=e.alternate,c=e.child,p=e.sibling,v=e.tag,w=e.type,C=null;switch(v){case O:case $e:case $:C=w;break;case ce:C=w.render;break}if(eo===null)throw new Error("Expected resolveFamily to be set during hot reload.");var R=!1,M=!1;if(C!==null){var U=eo(C);U!==void 0&&(a.has(U)?M=!0:t.has(U)&&(v===$?M=!0:R=!0))}if(np!==null&&(np.has(e)||l!==null&&np.has(l))&&(M=!0),M&&(e._debugNeedsRemount=!0),M||R){var F=ca(e,nt);F!==null&&jr(F,e,nt,rn)}c!==null&&!M&&ES(c,t,a),p!==null&&ES(p,t,a)}}var F5=function(e,t){{var a=new Set,l=new Set(t.map(function(c){return c.current}));return TS(e.current,l,a),a}};function TS(e,t,a){{var l=e.child,c=e.sibling,p=e.tag,v=e.type,w=null;switch(p){case O:case $e:case $:w=v;break;case ce:w=v.render;break}var C=!1;w!==null&&t.has(w)&&(C=!0),C?I5(e,a):l!==null&&TS(l,t,a),c!==null&&TS(c,t,a)}}function I5(e,t){{var a=U5(e,t);if(a)return;for(var l=e;;){switch(l.tag){case L:t.add(l.stateNode);return;case H:t.add(l.stateNode.containerInfo);return;case j:t.add(l.stateNode.containerInfo);return}if(l.return===null)throw new Error("Expected to reach root first.");l=l.return}}}function U5(e,t){for(var a=e,l=!1;;){if(a.tag===L)l=!0,t.add(a.stateNode);else if(a.child!==null){a.child.return=a,a=a.child;continue}if(a===e)return l;for(;a.sibling===null;){if(a.return===null||a.return===e)return l;a=a.return}a.sibling.return=a.return,a=a.sibling}return!1}var kS;{kS=!1;try{var $R=Object.preventExtensions({})}catch{kS=!0}}function B5(e,t,a,l){this.tag=e,this.key=a,this.elementType=null,this.type=null,this.stateNode=null,this.return=null,this.child=null,this.sibling=null,this.index=0,this.ref=null,this.pendingProps=t,this.memoizedProps=null,this.updateQueue=null,this.memoizedState=null,this.dependencies=null,this.mode=l,this.flags=Ye,this.subtreeFlags=Ye,this.deletions=null,this.lanes=ie,this.childLanes=ie,this.alternate=null,this.actualDuration=Number.NaN,this.actualStartTime=Number.NaN,this.selfBaseDuration=Number.NaN,this.treeBaseDuration=Number.NaN,this.actualDuration=0,this.actualStartTime=-1,this.selfBaseDuration=0,this.treeBaseDuration=0,this._debugSource=null,this._debugOwner=null,this._debugNeedsRemount=!1,this._debugHookTypes=null,!kS&&typeof Object.preventExtensions=="function"&&Object.preventExtensions(this)}var Ra=function(e,t,a,l){return new B5(e,t,a,l)};function RS(e){var t=e.prototype;return!!(t&&t.isReactComponent)}function H5(e){return typeof e=="function"&&!RS(e)&&e.defaultProps===void 0}function V5(e){if(typeof e=="function")return RS(e)?$:O;if(e!=null){var t=e.$$typeof;if(t===de)return ce;if(t===Rt)return ue}return P}function Zc(e,t){var a=e.alternate;a===null?(a=Ra(e.tag,t,e.key,e.mode),a.elementType=e.elementType,a.type=e.type,a.stateNode=e.stateNode,a._debugSource=e._debugSource,a._debugOwner=e._debugOwner,a._debugHookTypes=e._debugHookTypes,a.alternate=e,e.alternate=a):(a.pendingProps=t,a.type=e.type,a.flags=Ye,a.subtreeFlags=Ye,a.deletions=null,a.actualDuration=0,a.actualStartTime=-1),a.flags=e.flags&Xn,a.childLanes=e.childLanes,a.lanes=e.lanes,a.child=e.child,a.memoizedProps=e.memoizedProps,a.memoizedState=e.memoizedState,a.updateQueue=e.updateQueue;var l=e.dependencies;switch(a.dependencies=l===null?null:{lanes:l.lanes,firstContext:l.firstContext},a.sibling=e.sibling,a.index=e.index,a.ref=e.ref,a.selfBaseDuration=e.selfBaseDuration,a.treeBaseDuration=e.treeBaseDuration,a._debugNeedsRemount=e._debugNeedsRemount,a.tag){case P:case O:case $e:a.type=rp(e.type);break;case $:a.type=SS(e.type);break;case ce:a.type=CS(e.type);break}return a}function W5(e,t){e.flags&=Xn|Ln;var a=e.alternate;if(a===null)e.childLanes=ie,e.lanes=t,e.child=null,e.subtreeFlags=Ye,e.memoizedProps=null,e.memoizedState=null,e.updateQueue=null,e.dependencies=null,e.stateNode=null,e.selfBaseDuration=0,e.treeBaseDuration=0;else{e.childLanes=a.childLanes,e.lanes=a.lanes,e.child=a.child,e.subtreeFlags=Ye,e.deletions=null,e.memoizedProps=a.memoizedProps,e.memoizedState=a.memoizedState,e.updateQueue=a.updateQueue,e.type=a.type;var l=a.dependencies;e.dependencies=l===null?null:{lanes:l.lanes,firstContext:l.firstContext},e.selfBaseDuration=a.selfBaseDuration,e.treeBaseDuration=a.treeBaseDuration}return e}function Y5(e,t,a){var l;return e===Gv?(l=Dt,t===!0&&(l|=mt,l|=cn)):l=Ge,Fr&&(l|=zt),Ra(j,null,null,l)}function DS(e,t,a,l,c,p){var v=P,w=e;if(typeof e=="function")RS(e)?(v=$,w=SS(w)):w=rp(w);else if(typeof e=="string")v=L;else e:switch(e){case li:return Tu(a.children,c,p,t);case _a:v=ze,c|=mt,(c&Dt)!==Ge&&(c|=cn);break;case La:return G5(a,c,p,t);case Te:return K5(a,c,p,t);case De:return Q5(a,c,p,t);case Fn:return AR(a,c,p,t);case yn:case $t:case Cn:case Lr:case St:default:{if(typeof e=="object"&&e!==null)switch(e.$$typeof){case oo:v=ae;break e;case z:v=fe;break e;case de:v=ce,w=CS(w);break e;case Rt:v=ue;break e;case ut:v=ft,w=null;break e}var C="";{(e===void 0||typeof e=="object"&&e!==null&&Object.keys(e).length===0)&&(C+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var R=l?lt(l):null;R&&(C+=`

Check the render method of \``+R+"`.")}throw new Error("Element type is invalid: expected a string (for built-in components) or a class/function (for composite components) "+("but got: "+(e==null?e:typeof e)+"."+C))}}var M=Ra(v,a,t,c);return M.elementType=e,M.type=w,M.lanes=p,M._debugOwner=l,M}function MS(e,t,a){var l=null;l=e._owner;var c=e.type,p=e.key,v=e.props,w=DS(c,p,v,l,t,a);return w._debugSource=e._source,w._debugOwner=e._owner,w}function Tu(e,t,a,l){var c=Ra(xe,e,l,t);return c.lanes=a,c}function G5(e,t,a,l){typeof e.id!="string"&&y('Profiler must specify an "id" of type `string` as a prop. Received the type `%s` instead.',typeof e.id);var c=Ra(Ee,e,l,t|zt);return c.elementType=La,c.lanes=a,c.stateNode={effectDuration:0,passiveEffectDuration:0},c}function K5(e,t,a,l){var c=Ra(le,e,l,t);return c.elementType=Te,c.lanes=a,c}function Q5(e,t,a,l){var c=Ra(bt,e,l,t);return c.elementType=De,c.lanes=a,c}function AR(e,t,a,l){var c=Ra(Be,e,l,t);c.elementType=Fn,c.lanes=a;var p={isHidden:!1};return c.stateNode=p,c}function OS(e,t,a){var l=Ra(Q,e,null,t);return l.lanes=a,l}function q5(){var e=Ra(L,null,null,Ge);return e.elementType="DELETED",e}function X5(e){var t=Ra(Tt,null,null,Ge);return t.stateNode=e,t}function $S(e,t,a){var l=e.children!==null?e.children:[],c=Ra(H,l,e.key,t);return c.lanes=a,c.stateNode={containerInfo:e.containerInfo,pendingChildren:null,implementation:e.implementation},c}function jR(e,t){return e===null&&(e=Ra(P,null,null,Ge)),e.tag=t.tag,e.key=t.key,e.elementType=t.elementType,e.type=t.type,e.stateNode=t.stateNode,e.return=t.return,e.child=t.child,e.sibling=t.sibling,e.index=t.index,e.ref=t.ref,e.pendingProps=t.pendingProps,e.memoizedProps=t.memoizedProps,e.updateQueue=t.updateQueue,e.memoizedState=t.memoizedState,e.dependencies=t.dependencies,e.mode=t.mode,e.flags=t.flags,e.subtreeFlags=t.subtreeFlags,e.deletions=t.deletions,e.lanes=t.lanes,e.childLanes=t.childLanes,e.alternate=t.alternate,e.actualDuration=t.actualDuration,e.actualStartTime=t.actualStartTime,e.selfBaseDuration=t.selfBaseDuration,e.treeBaseDuration=t.treeBaseDuration,e._debugSource=t._debugSource,e._debugOwner=t._debugOwner,e._debugNeedsRemount=t._debugNeedsRemount,e._debugHookTypes=t._debugHookTypes,e}function J5(e,t,a,l,c){this.tag=t,this.containerInfo=e,this.pendingChildren=null,this.current=null,this.pingCache=null,this.finishedWork=null,this.timeoutHandle=db,this.context=null,this.pendingContext=null,this.callbackNode=null,this.callbackPriority=Jn,this.eventTimes=sf(ie),this.expirationTimes=sf(rn),this.pendingLanes=ie,this.suspendedLanes=ie,this.pingedLanes=ie,this.expiredLanes=ie,this.mutableReadLanes=ie,this.finishedLanes=ie,this.entangledLanes=ie,this.entanglements=sf(ie),this.identifierPrefix=l,this.onRecoverableError=c,this.mutableSourceEagerHydrationData=null,this.effectDuration=0,this.passiveEffectDuration=0;{this.memoizedUpdaters=new Set;for(var p=this.pendingUpdatersLaneMap=[],v=0;v<th;v++)p.push(new Set)}switch(t){case Gv:this._debugRootType=a?"hydrateRoot()":"createRoot()";break;case fu:this._debugRootType=a?"hydrate()":"render()";break}}function _R(e,t,a,l,c,p,v,w,C,R){var M=new J5(e,t,a,w,C),U=Y5(t,p);M.current=U,U.stateNode=M;{var F={element:l,isDehydrated:a,cache:null,transitions:null,pendingSuspenseBoundaries:null};U.memoizedState=F}return Hb(U),M}var AS="18.3.1";function Z5(e,t,a){var l=arguments.length>3&&arguments[3]!==void 0?arguments[3]:null;return Ri(l),{$$typeof:$i,key:l==null?null:""+l,children:e,containerInfo:t,implementation:a}}var jS,_S;jS=!1,_S={};function LR(e){if(!e)return ka;var t=Bs(e),a=LN(t);if(t.tag===$){var l=t.type;if(ul(l))return sT(t,l,a)}return a}function eF(e,t){{var a=Bs(e);if(a===void 0){if(typeof e.render=="function")throw new Error("Unable to find node on an unmounted component.");var l=Object.keys(e).join(",");throw new Error("Argument appears to not be a ReactComponent. Keys: "+l)}var c=hi(a);if(c===null)return null;if(c.mode&mt){var p=lt(a)||"Component";if(!_S[p]){_S[p]=!0;var v=fr;try{ln(c),a.mode&mt?y("%s is deprecated in StrictMode. %s was passed an instance of %s which is inside StrictMode. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node",t,t,p):y("%s is deprecated in StrictMode. %s was passed an instance of %s which renders StrictMode children. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node",t,t,p)}finally{v?ln(v):_n()}}}return c.stateNode}}function zR(e,t,a,l,c,p,v,w){var C=!1,R=null;return _R(e,t,C,R,a,l,c,p,v)}function NR(e,t,a,l,c,p,v,w,C,R){var M=!0,U=_R(a,l,M,e,c,p,v,w,C);U.context=LR(null);var F=U.current,X=Ii(),Z=Cu(F),te=ls(X,Z);return te.callback=t??null,gu(F,te,Z),l5(U,Z,X),U}function wg(e,t,a,l){Yp(t,e);var c=t.current,p=Ii(),v=Cu(c);Pd(v);var w=LR(a);t.context===null?t.context=w:t.pendingContext=w,ci&&fr!==null&&!jS&&(jS=!0,y(`Render methods should be a pure function of props and state; triggering nested component updates from render is not allowed. If necessary, trigger nested updates in componentDidUpdate.

Check the render method of %s.`,lt(fr)||"Unknown"));var C=ls(p,v);C.payload={element:e},l=l===void 0?null:l,l!==null&&(typeof l!="function"&&y("render(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",l),C.callback=l);var R=gu(c,C,v);return R!==null&&(jr(R,c,v,p),sy(R,c,v)),v}function Xy(e){var t=e.current;if(!t.child)return null;switch(t.child.tag){case L:return t.child.stateNode;default:return t.child.stateNode}}function tF(e){switch(e.tag){case j:{var t=e.stateNode;if(Yl(t)){var a=Zm(t);d5(t,a)}break}case le:{ds(function(){var c=ca(e,nt);if(c!==null){var p=Ii();jr(c,e,nt,p)}});var l=nt;LS(e,l);break}}}function PR(e,t){var a=e.memoizedState;a!==null&&a.dehydrated!==null&&(a.retryLane=lh(a.retryLane,t))}function LS(e,t){PR(e,t);var a=e.alternate;a&&PR(a,t)}function nF(e){if(e.tag===le){var t=Xs,a=ca(e,t);if(a!==null){var l=Ii();jr(a,e,t,l)}LS(e,t)}}function rF(e){if(e.tag===le){var t=Cu(e),a=ca(e,t);if(a!==null){var l=Ii();jr(a,e,t,l)}LS(e,t)}}function FR(e){var t=Sa(e);return t===null?null:t.stateNode}var IR=function(e){return null};function iF(e){return IR(e)}var UR=function(e){return!1};function aF(e){return UR(e)}var BR=null,HR=null,VR=null,WR=null,YR=null,GR=null,KR=null,QR=null,qR=null;{var XR=function(e,t,a){var l=t[a],c=yt(e)?e.slice():gt({},e);return a+1===t.length?(yt(c)?c.splice(l,1):delete c[l],c):(c[l]=XR(e[l],t,a+1),c)},JR=function(e,t){return XR(e,t,0)},ZR=function(e,t,a,l){var c=t[l],p=yt(e)?e.slice():gt({},e);if(l+1===t.length){var v=a[l];p[v]=p[c],yt(p)?p.splice(c,1):delete p[c]}else p[c]=ZR(e[c],t,a,l+1);return p},eD=function(e,t,a){if(t.length!==a.length){S("copyWithRename() expects paths of the same length");return}else for(var l=0;l<a.length-1;l++)if(t[l]!==a[l]){S("copyWithRename() expects paths to be the same except for the deepest key");return}return ZR(e,t,a,0)},tD=function(e,t,a,l){if(a>=t.length)return l;var c=t[a],p=yt(e)?e.slice():gt({},e);return p[c]=tD(e[c],t,a+1,l),p},nD=function(e,t,a){return tD(e,t,0,a)},zS=function(e,t){for(var a=e.memoizedState;a!==null&&t>0;)a=a.next,t--;return a};BR=function(e,t,a,l){var c=zS(e,t);if(c!==null){var p=nD(c.memoizedState,a,l);c.memoizedState=p,c.baseState=p,e.memoizedProps=gt({},e.memoizedProps);var v=ca(e,nt);v!==null&&jr(v,e,nt,rn)}},HR=function(e,t,a){var l=zS(e,t);if(l!==null){var c=JR(l.memoizedState,a);l.memoizedState=c,l.baseState=c,e.memoizedProps=gt({},e.memoizedProps);var p=ca(e,nt);p!==null&&jr(p,e,nt,rn)}},VR=function(e,t,a,l){var c=zS(e,t);if(c!==null){var p=eD(c.memoizedState,a,l);c.memoizedState=p,c.baseState=p,e.memoizedProps=gt({},e.memoizedProps);var v=ca(e,nt);v!==null&&jr(v,e,nt,rn)}},WR=function(e,t,a){e.pendingProps=nD(e.memoizedProps,t,a),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var l=ca(e,nt);l!==null&&jr(l,e,nt,rn)},YR=function(e,t){e.pendingProps=JR(e.memoizedProps,t),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var a=ca(e,nt);a!==null&&jr(a,e,nt,rn)},GR=function(e,t,a){e.pendingProps=eD(e.memoizedProps,t,a),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var l=ca(e,nt);l!==null&&jr(l,e,nt,rn)},KR=function(e){var t=ca(e,nt);t!==null&&jr(t,e,nt,rn)},QR=function(e){IR=e},qR=function(e){UR=e}}function oF(e){var t=hi(e);return t===null?null:t.stateNode}function lF(e){return null}function sF(){return fr}function uF(e){var t=e.findFiberByHostInstance,a=d.ReactCurrentDispatcher;return Wp({bundleType:e.bundleType,version:e.version,rendererPackageName:e.rendererPackageName,rendererConfig:e.rendererConfig,overrideHookState:BR,overrideHookStateDeletePath:HR,overrideHookStateRenamePath:VR,overrideProps:WR,overridePropsDeletePath:YR,overridePropsRenamePath:GR,setErrorHandler:QR,setSuspenseHandler:qR,scheduleUpdate:KR,currentDispatcherRef:a,findHostInstanceByFiber:oF,findFiberByHostInstance:t||lF,findHostInstancesForRefresh:F5,scheduleRefresh:N5,scheduleRoot:P5,setRefreshHandler:z5,getCurrentFiber:sF,reconcilerVersion:AS})}var rD=typeof reportError=="function"?reportError:function(e){console.error(e)};function NS(e){this._internalRoot=e}Jy.prototype.render=NS.prototype.render=function(e){var t=this._internalRoot;if(t===null)throw new Error("Cannot update an unmounted root.");{typeof arguments[1]=="function"?y("render(...): does not support the second callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."):Zy(arguments[1])?y("You passed a container to the second argument of root.render(...). You don't need to pass it again since you already passed it to create the root."):typeof arguments[1]<"u"&&y("You passed a second argument to root.render(...) but it only accepts one argument.");var a=t.containerInfo;if(a.nodeType!==Qn){var l=FR(t.current);l&&l.parentNode!==a&&y("render(...): It looks like the React-rendered content of the root container was removed without using React. This is not supported and will cause errors. Instead, call root.unmount() to empty a root's container.")}}wg(e,t,null,null)},Jy.prototype.unmount=NS.prototype.unmount=function(){typeof arguments[0]=="function"&&y("unmount(...): does not support a callback argument. To execute a side effect after rendering, declare it in a component body with useEffect().");var e=this._internalRoot;if(e!==null){this._internalRoot=null;var t=e.containerInfo;gR()&&y("Attempted to synchronously unmount a root while React was already rendering. React cannot finish unmounting the root until the current render has completed, which may lead to a race condition."),ds(function(){wg(null,e,null,null)}),rT(t)}};function cF(e,t){if(!Zy(e))throw new Error("createRoot(...): Target container is not a DOM element.");iD(e);var a=!1,l=!1,c="",p=rD;t!=null&&(t.hydrate?S("hydrate through createRoot is deprecated. Use ReactDOMClient.hydrateRoot(container, <App />) instead."):typeof t=="object"&&t!==null&&t.$$typeof===br&&y(`You passed a JSX element to createRoot. You probably meant to call root.render instead. Example usage:

  let root = createRoot(domContainer);
  root.render(<App />);`),t.unstable_strictMode===!0&&(a=!0),t.identifierPrefix!==void 0&&(c=t.identifierPrefix),t.onRecoverableError!==void 0&&(p=t.onRecoverableError),t.transitionCallbacks!==void 0&&t.transitionCallbacks);var v=zR(e,Gv,null,a,l,c,p);Iv(v.current,e);var w=e.nodeType===Qn?e.parentNode:e;return Rh(w),new NS(v)}function Jy(e){this._internalRoot=e}function dF(e){e&&cv(e)}Jy.prototype.unstable_scheduleHydration=dF;function fF(e,t,a){if(!Zy(e))throw new Error("hydrateRoot(...): Target container is not a DOM element.");iD(e),t===void 0&&y("Must provide initial children as second argument to hydrateRoot. Example usage: hydrateRoot(domContainer, <App />)");var l=a??null,c=a!=null&&a.hydratedSources||null,p=!1,v=!1,w="",C=rD;a!=null&&(a.unstable_strictMode===!0&&(p=!0),a.identifierPrefix!==void 0&&(w=a.identifierPrefix),a.onRecoverableError!==void 0&&(C=a.onRecoverableError));var R=NR(t,null,e,Gv,l,p,v,w,C);if(Iv(R.current,e),Rh(e),c)for(var M=0;M<c.length;M++){var U=c[M];mP(R,U)}return new Jy(R)}function Zy(e){return!!(e&&(e.nodeType===di||e.nodeType===mo||e.nodeType===Wu))}function Sg(e){return!!(e&&(e.nodeType===di||e.nodeType===mo||e.nodeType===Wu||e.nodeType===Qn&&e.nodeValue===" react-mount-point-unstable "))}function iD(e){e.nodeType===di&&e.tagName&&e.tagName.toUpperCase()==="BODY"&&y("createRoot(): Creating roots directly with document.body is discouraged, since its children are often manipulated by third-party scripts and browser extensions. This may lead to subtle reconciliation issues. Try using a container element created for your app."),Ph(e)&&(e._reactRootContainer?y("You are calling ReactDOMClient.createRoot() on a container that was previously passed to ReactDOM.render(). This is not supported."):y("You are calling ReactDOMClient.createRoot() on a container that has already been passed to createRoot() before. Instead, call root.render() on the existing root instead if you want to update it."))}var pF=d.ReactCurrentOwner,aD;aD=function(e){if(e._reactRootContainer&&e.nodeType!==Qn){var t=FR(e._reactRootContainer.current);t&&t.parentNode!==e&&y("render(...): It looks like the React-rendered content of this container was removed without using React. This is not supported and will cause errors. Instead, call ReactDOM.unmountComponentAtNode to empty a container.")}var a=!!e._reactRootContainer,l=PS(e),c=!!(l&&cu(l));c&&!a&&y("render(...): Replacing React-rendered children with a new root component. If you intended to update the children of this node, you should instead have the existing children update their state and render the new components instead of calling ReactDOM.render."),e.nodeType===di&&e.tagName&&e.tagName.toUpperCase()==="BODY"&&y("render(): Rendering components directly into document.body is discouraged, since its children are often manipulated by third-party scripts and browser extensions. This may lead to subtle reconciliation issues. Try rendering into a container element created for your app.")};function PS(e){return e?e.nodeType===mo?e.documentElement:e.firstChild:null}function oD(){}function hF(e,t,a,l,c){if(c){if(typeof l=="function"){var p=l;l=function(){var F=Xy(v);p.call(F)}}var v=NR(t,l,e,fu,null,!1,!1,"",oD);e._reactRootContainer=v,Iv(v.current,e);var w=e.nodeType===Qn?e.parentNode:e;return Rh(w),ds(),v}else{for(var C;C=e.lastChild;)e.removeChild(C);if(typeof l=="function"){var R=l;l=function(){var F=Xy(M);R.call(F)}}var M=zR(e,fu,null,!1,!1,"",oD);e._reactRootContainer=M,Iv(M.current,e);var U=e.nodeType===Qn?e.parentNode:e;return Rh(U),ds(function(){wg(t,M,a,l)}),M}}function gF(e,t){e!==null&&typeof e!="function"&&y("%s(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",t,e)}function ex(e,t,a,l,c){aD(a),gF(c===void 0?null:c,"render");var p=a._reactRootContainer,v;if(!p)v=hF(a,t,e,c,l);else{if(v=p,typeof c=="function"){var w=c;c=function(){var C=Xy(v);w.call(C)}}wg(t,v,e,c)}return Xy(v)}var lD=!1;function mF(e){{lD||(lD=!0,y("findDOMNode is deprecated and will be removed in the next major release. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node"));var t=pF.current;if(t!==null&&t.stateNode!==null){var a=t.stateNode._warnedAboutRefsInRender;a||y("%s is accessing findDOMNode inside its render(). render() should be a pure function of props and state. It should never access something that requires stale data from the previous render, such as refs. Move this logic to componentDidMount and componentDidUpdate instead.",Pt(t.type)||"A component"),t.stateNode._warnedAboutRefsInRender=!0}}return e==null?null:e.nodeType===di?e:eF(e,"findDOMNode")}function vF(e,t,a){if(y("ReactDOM.hydrate is no longer supported in React 18. Use hydrateRoot instead. Until you switch to the new API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!Sg(t))throw new Error("Target container is not a DOM element.");{var l=Ph(t)&&t._reactRootContainer===void 0;l&&y("You are calling ReactDOM.hydrate() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call hydrateRoot(container, element)?")}return ex(null,e,t,!0,a)}function yF(e,t,a){if(y("ReactDOM.render is no longer supported in React 18. Use createRoot instead. Until you switch to the new API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!Sg(t))throw new Error("Target container is not a DOM element.");{var l=Ph(t)&&t._reactRootContainer===void 0;l&&y("You are calling ReactDOM.render() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call root.render(element)?")}return ex(null,e,t,!1,a)}function xF(e,t,a,l){if(y("ReactDOM.unstable_renderSubtreeIntoContainer() is no longer supported in React 18. Consider using a portal instead. Until you switch to the createRoot API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!Sg(a))throw new Error("Target container is not a DOM element.");if(e==null||!Ll(e))throw new Error("parentComponent must be a valid React Component");return ex(e,t,a,!1,l)}var sD=!1;function bF(e){if(sD||(sD=!0,y("unmountComponentAtNode is deprecated and will be removed in the next major release. Switch to the createRoot API. Learn more: https://reactjs.org/link/switch-to-createroot")),!Sg(e))throw new Error("unmountComponentAtNode(...): Target container is not a DOM element.");{var t=Ph(e)&&e._reactRootContainer===void 0;t&&y("You are calling ReactDOM.unmountComponentAtNode() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call root.unmount()?")}if(e._reactRootContainer){{var a=PS(e),l=a&&!cu(a);l&&y("unmountComponentAtNode(): The node you're attempting to unmount was rendered by another copy of React.")}return ds(function(){ex(null,null,e,!1,function(){e._reactRootContainer=null,rT(e)})}),!0}else{{var c=PS(e),p=!!(c&&cu(c)),v=e.nodeType===di&&Sg(e.parentNode)&&!!e.parentNode._reactRootContainer;p&&y("unmountComponentAtNode(): The node you're attempting to unmount was rendered by React and is not a top-level container. %s",v?"You may have accidentally passed in a React root node instead of its container.":"Instead, have the parent component update its state and rerender in order to remove this component.")}return!1}}N0(tF),dh(nF),P0(rF),pf(_i),ov(iv),(typeof Map!="function"||Map.prototype==null||typeof Map.prototype.forEach!="function"||typeof Set!="function"||Set.prototype==null||typeof Set.prototype.clear!="function"||typeof Set.prototype.forEach!="function")&&y("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"),ec(Sz),Pm(gS,f5,ds);function wF(e,t){var a=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;if(!Zy(t))throw new Error("Target container is not a DOM element.");return Z5(e,t,null,a)}function SF(e,t,a,l){return xF(e,t,a,l)}var FS={usingClientEntryPoint:!1,Events:[cu,_f,Uv,Lp,Ps,gS]};function CF(e,t){return FS.usingClientEntryPoint||y('You are importing createRoot from "react-dom" which is not supported. You should instead import it from "react-dom/client".'),cF(e,t)}function EF(e,t,a){return FS.usingClientEntryPoint||y('You are importing hydrateRoot from "react-dom" which is not supported. You should instead import it from "react-dom/client".'),fF(e,t,a)}function TF(e){return gR()&&y("flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task."),ds(e)}var kF=uF({findFiberByHostInstance:Pc,bundleType:1,version:AS,rendererPackageName:"react-dom"});if(!kF&&an&&window.top===window.self&&(navigator.userAgent.indexOf("Chrome")>-1&&navigator.userAgent.indexOf("Edge")===-1||navigator.userAgent.indexOf("Firefox")>-1)){var uD=window.location.protocol;/^(https?|file):$/.test(uD)&&console.info("%cDownload the React DevTools for a better development experience: https://reactjs.org/link/react-devtools"+(uD==="file:"?`
You might need to use a local HTTP server (instead of file://): https://reactjs.org/link/react-devtools-faq`:""),"font-weight:bold")}Vi.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=FS,Vi.createPortal=wF,Vi.createRoot=CF,Vi.findDOMNode=mF,Vi.flushSync=TF,Vi.hydrate=vF,Vi.hydrateRoot=EF,Vi.render=yF,Vi.unmountComponentAtNode=bF,Vi.unstable_batchedUpdates=gS,Vi.unstable_renderSubtreeIntoContainer=SF,Vi.version=AS,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}(),Vi}var qS={};function XS(){if(!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u"||typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE!="function")){if(qS.NODE_ENV!=="production")throw new Error("^_^");try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(XS)}catch(o){console.error(o)}}}qS.NODE_ENV==="production"?(XS(),rx.exports=yD()):rx.exports=xD();var bD=rx.exports,ox,wD={},kg=bD;if(wD.NODE_ENV==="production")ox=kg.createRoot,kg.hydrateRoot;else{var JS=kg.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;ox=function(o,r){JS.usingClientEntryPoint=!0;try{return kg.createRoot(o,r)}finally{JS.usingClientEntryPoint=!1}}}var Wi=function(){return Wi=Object.assign||function(r){for(var s,d=1,g=arguments.length;d<g;d++){s=arguments[d];for(var b in s)Object.prototype.hasOwnProperty.call(s,b)&&(r[b]=s[b])}return r},Wi.apply(this,arguments)};function ed(o,r,s){if(s||arguments.length===2)for(var d=0,g=r.length,b;d<g;d++)(b||!(d in r))&&(b||(b=Array.prototype.slice.call(r,0,d)),b[d]=r[d]);return o.concat(b||Array.prototype.slice.call(r))}typeof SuppressedError=="function"&&SuppressedError;function SD(o){var r=Object.create(null);return function(s){return r[s]===void 0&&(r[s]=o(s)),r[s]}}var CD=/^((children|dangerouslySetInnerHTML|key|ref|autoFocus|defaultValue|defaultChecked|innerHTML|suppressContentEditableWarning|suppressHydrationWarning|valueLink|abbr|accept|acceptCharset|accessKey|action|allow|allowUserMedia|allowPaymentRequest|allowFullScreen|allowTransparency|alt|async|autoComplete|autoPlay|capture|cellPadding|cellSpacing|challenge|charSet|checked|cite|classID|className|cols|colSpan|content|contentEditable|contextMenu|controls|controlsList|coords|crossOrigin|data|dateTime|decoding|default|defer|dir|disabled|disablePictureInPicture|disableRemotePlayback|download|draggable|encType|enterKeyHint|fetchpriority|fetchPriority|form|formAction|formEncType|formMethod|formNoValidate|formTarget|frameBorder|headers|height|hidden|high|href|hrefLang|htmlFor|httpEquiv|id|inputMode|integrity|is|keyParams|keyType|kind|label|lang|list|loading|loop|low|marginHeight|marginWidth|max|maxLength|media|mediaGroup|method|min|minLength|multiple|muted|name|nonce|noValidate|open|optimum|pattern|placeholder|playsInline|popover|popoverTarget|popoverTargetAction|poster|preload|profile|radioGroup|readOnly|referrerPolicy|rel|required|reversed|role|rows|rowSpan|sandbox|scope|scoped|scrolling|seamless|selected|shape|size|sizes|slot|span|spellCheck|src|srcDoc|srcLang|srcSet|start|step|style|summary|tabIndex|target|title|translate|type|useMap|value|width|wmode|wrap|about|datatype|inlist|prefix|property|resource|typeof|vocab|autoCapitalize|autoCorrect|autoSave|color|incremental|fallback|inert|itemProp|itemScope|itemType|itemID|itemRef|on|option|results|security|unselectable|accentHeight|accumulate|additive|alignmentBaseline|allowReorder|alphabetic|amplitude|arabicForm|ascent|attributeName|attributeType|autoReverse|azimuth|baseFrequency|baselineShift|baseProfile|bbox|begin|bias|by|calcMode|capHeight|clip|clipPathUnits|clipPath|clipRule|colorInterpolation|colorInterpolationFilters|colorProfile|colorRendering|contentScriptType|contentStyleType|cursor|cx|cy|d|decelerate|descent|diffuseConstant|direction|display|divisor|dominantBaseline|dur|dx|dy|edgeMode|elevation|enableBackground|end|exponent|externalResourcesRequired|fill|fillOpacity|fillRule|filter|filterRes|filterUnits|floodColor|floodOpacity|focusable|fontFamily|fontSize|fontSizeAdjust|fontStretch|fontStyle|fontVariant|fontWeight|format|from|fr|fx|fy|g1|g2|glyphName|glyphOrientationHorizontal|glyphOrientationVertical|glyphRef|gradientTransform|gradientUnits|hanging|horizAdvX|horizOriginX|ideographic|imageRendering|in|in2|intercept|k|k1|k2|k3|k4|kernelMatrix|kernelUnitLength|kerning|keyPoints|keySplines|keyTimes|lengthAdjust|letterSpacing|lightingColor|limitingConeAngle|local|markerEnd|markerMid|markerStart|markerHeight|markerUnits|markerWidth|mask|maskContentUnits|maskUnits|mathematical|mode|numOctaves|offset|opacity|operator|order|orient|orientation|origin|overflow|overlinePosition|overlineThickness|panose1|paintOrder|pathLength|patternContentUnits|patternTransform|patternUnits|pointerEvents|points|pointsAtX|pointsAtY|pointsAtZ|preserveAlpha|preserveAspectRatio|primitiveUnits|r|radius|refX|refY|renderingIntent|repeatCount|repeatDur|requiredExtensions|requiredFeatures|restart|result|rotate|rx|ry|scale|seed|shapeRendering|slope|spacing|specularConstant|specularExponent|speed|spreadMethod|startOffset|stdDeviation|stemh|stemv|stitchTiles|stopColor|stopOpacity|strikethroughPosition|strikethroughThickness|string|stroke|strokeDasharray|strokeDashoffset|strokeLinecap|strokeLinejoin|strokeMiterlimit|strokeOpacity|strokeWidth|surfaceScale|systemLanguage|tableValues|targetX|targetY|textAnchor|textDecoration|textRendering|textLength|to|transform|u1|u2|underlinePosition|underlineThickness|unicode|unicodeBidi|unicodeRange|unitsPerEm|vAlphabetic|vHanging|vIdeographic|vMathematical|values|vectorEffect|version|vertAdvY|vertOriginX|vertOriginY|viewBox|viewTarget|visibility|widths|wordSpacing|writingMode|x|xHeight|x1|x2|xChannelSelector|xlinkActuate|xlinkArcrole|xlinkHref|xlinkRole|xlinkShow|xlinkTitle|xlinkType|xmlBase|xmlns|xmlnsXlink|xmlLang|xmlSpace|y|y1|y2|yChannelSelector|z|zoomAndPan|for|class|autofocus)|(([Dd][Aa][Tt][Aa]|[Aa][Rr][Ii][Aa]|x)-.*))$/,ED=SD(function(o){return CD.test(o)||o.charCodeAt(0)===111&&o.charCodeAt(1)===110&&o.charCodeAt(2)<91}),$n="-ms-",sp="-moz-",Zt="-webkit-",ZS="comm",Rg="rule",lx="decl",TD="@import",kD="@namespace",e1="@keyframes",RD="@layer",t1=Math.abs,sx=String.fromCharCode,ux=Object.assign;function DD(o,r){return yr(o,0)^45?(((r<<2^yr(o,0))<<2^yr(o,1))<<2^yr(o,2))<<2^yr(o,3):0}function n1(o){return o.trim()}function yl(o,r){return(o=r.exec(o))?o[0]:o}function Mt(o,r,s){return o.replace(r,s)}function Dg(o,r,s){return o.indexOf(r,s)}function yr(o,r){return o.charCodeAt(r)|0}function ku(o,r,s){return o.slice(r,s)}function to(o){return o.length}function r1(o){return o.length}function up(o,r){return r.push(o),o}function MD(o,r){return o.map(r).join("")}function i1(o,r){return o.filter(function(s){return!yl(s,r)})}var Mg=1,td=1,a1=0,Ma=0,sr=0,nd="";function Og(o,r,s,d,g,b,S,y){return{value:o,root:r,parent:s,type:d,props:g,children:b,line:Mg,column:td,length:S,return:"",siblings:y}}function hs(o,r){return ux(Og("",null,null,"",null,null,0,o.siblings),o,{length:-o.length},r)}function rd(o){for(;o.root;)o=hs(o.root,{children:[o]});up(o,o.siblings)}function OD(){return sr}function $D(){return sr=Ma>0?yr(nd,--Ma):0,td--,sr===10&&(td=1,Mg--),sr}function no(){return sr=Ma<a1?yr(nd,Ma++):0,td++,sr===10&&(td=1,Mg++),sr}function gs(){return yr(nd,Ma)}function $g(){return Ma}function Ag(o,r){return ku(nd,o,r)}function cp(o){switch(o){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function AD(o){return Mg=td=1,a1=to(nd=o),Ma=0,[]}function jD(o){return nd="",o}function cx(o){return n1(Ag(Ma-1,dx(o===91?o+2:o===40?o+1:o)))}function _D(o){for(;(sr=gs())&&sr<33;)no();return cp(o)>2||cp(sr)>3?"":" "}function LD(o,r){for(;--r&&no()&&!(sr<48||sr>102||sr>57&&sr<65||sr>70&&sr<97););return Ag(o,$g()+(r<6&&gs()==32&&no()==32))}function dx(o){for(;no();)switch(sr){case o:return Ma;case 34:case 39:o!==34&&o!==39&&dx(sr);break;case 40:o===41&&dx(o);break;case 92:no();break}return Ma}function zD(o,r){for(;no()&&o+sr!==57;)if(o+sr===84&&gs()===47)break;return"/*"+Ag(r,Ma-1)+"*"+sx(o===47?o:no())}function ND(o){for(;!cp(gs());)no();return Ag(o,Ma)}function PD(o){return jD(jg("",null,null,null,[""],o=AD(o),0,[0],o))}function jg(o,r,s,d,g,b,S,y,E){for(var O=0,$=0,P=S,j=0,H=0,L=0,Q=1,xe=1,ze=1,fe=0,ae="",ce=g,Ee=b,le=d,ue=ae;xe;)switch(L=fe,fe=no()){case 40:if(L!=108&&yr(ue,P-1)==58){Dg(ue+=Mt(cx(fe),"&","&\f"),"&\f",t1(O?y[O-1]:0))!=-1&&(ze=-1);break}case 34:case 39:case 91:ue+=cx(fe);break;case 9:case 10:case 13:case 32:ue+=_D(L);break;case 92:ue+=LD($g()-1,7);continue;case 47:switch(gs()){case 42:case 47:up(FD(zD(no(),$g()),r,s,E),E),(cp(L||1)==5||cp(gs()||1)==5)&&to(ue)&&ku(ue,-1,void 0)!==" "&&(ue+=" ");break;default:ue+="/"}break;case 123*Q:y[O++]=to(ue)*ze;case 125*Q:case 59:case 0:switch(fe){case 0:case 125:xe=0;case 59+$:ze==-1&&(ue=Mt(ue,/\f/g,"")),H>0&&(to(ue)-P||Q===0&&L===47)&&up(H>32?l1(ue+";",d,s,P-1,E):l1(Mt(ue," ","")+";",d,s,P-2,E),E);break;case 59:ue+=";";default:if(up(le=o1(ue,r,s,O,$,g,y,ae,ce=[],Ee=[],P,b),b),fe===123)if($===0)jg(ue,r,le,le,ce,b,P,y,Ee);else{switch(j){case 99:if(yr(ue,3)===110)break;case 108:if(yr(ue,2)===97)break;default:$=0;case 100:case 109:case 115:}$?jg(o,le,le,d&&up(o1(o,le,le,0,0,g,y,ae,g,ce=[],P,Ee),Ee),g,Ee,P,y,d?ce:Ee):jg(ue,le,le,le,[""],Ee,0,y,Ee)}}O=$=H=0,Q=ze=1,ae=ue="",P=S;break;case 58:P=1+to(ue),H=L;default:if(Q<1){if(fe==123)--Q;else if(fe==125&&Q++==0&&$D()==125)continue}switch(ue+=sx(fe),fe*Q){case 38:ze=$>0?1:(ue+="\f",-1);break;case 44:y[O++]=(to(ue)-1)*ze,ze=1;break;case 64:gs()===45&&(ue+=cx(no())),j=gs(),$=P=to(ae=ue+=ND($g())),fe++;break;case 45:L===45&&to(ue)==2&&(Q=0)}}return b}function o1(o,r,s,d,g,b,S,y,E,O,$,P){for(var j=g-1,H=g===0?b:[""],L=r1(H),Q=0,xe=0,ze=0;Q<d;++Q)for(var fe=0,ae=ku(o,j+1,j=t1(xe=S[Q])),ce=o;fe<L;++fe)(ce=n1(xe>0?H[fe]+" "+ae:Mt(ae,/&\f/g,H[fe])))&&(E[ze++]=ce);return Og(o,r,s,g===0?Rg:y,E,O,$,P)}function FD(o,r,s,d){return Og(o,r,s,ZS,sx(OD()),ku(o,2,-2),0,d)}function l1(o,r,s,d,g){return Og(o,r,s,lx,ku(o,0,d),ku(o,d+1,-1),d,g)}function s1(o,r,s){switch(DD(o,r)){case 5103:return Zt+"print-"+o+o;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return Zt+o+o;case 4855:return Zt+o.replace("add","source-over").replace("substract","source-out").replace("intersect","source-in").replace("exclude","xor")+o;case 4789:return sp+o+o;case 5349:case 4246:case 4810:case 6968:case 2756:return Zt+o+sp+o+$n+o+o;case 5936:switch(yr(o,r+11)){case 114:return Zt+o+$n+Mt(o,/[svh]\w+-[tblr]{2}/,"tb")+o;case 108:return Zt+o+$n+Mt(o,/[svh]\w+-[tblr]{2}/,"tb-rl")+o;case 45:return Zt+o+$n+Mt(o,/[svh]\w+-[tblr]{2}/,"lr")+o}case 6828:case 4268:case 2903:return Zt+o+$n+o+o;case 6165:return Zt+o+$n+"flex-"+o+o;case 5187:return Zt+o+Mt(o,/(\w+).+(:[^]+)/,Zt+"box-$1$2"+$n+"flex-$1$2")+o;case 5443:return Zt+o+$n+"flex-item-"+Mt(o,/flex-|-self/g,"")+(yl(o,/flex-|baseline/)?"":$n+"grid-row-"+Mt(o,/flex-|-self/g,""))+o;case 4675:return Zt+o+$n+"flex-line-pack"+Mt(o,/align-content|flex-|-self/g,"")+o;case 5548:return Zt+o+$n+Mt(o,"shrink","negative")+o;case 5292:return Zt+o+$n+Mt(o,"basis","preferred-size")+o;case 6060:return Zt+"box-"+Mt(o,"-grow","")+Zt+o+$n+Mt(o,"grow","positive")+o;case 4554:return Zt+Mt(o,/([^-])(transform)/g,"$1"+Zt+"$2")+o;case 6187:return Mt(Mt(Mt(o,/(zoom-|grab)/,Zt+"$1"),/(image-set)/,Zt+"$1"),o,"")+o;case 5495:case 3959:return Mt(o,/(image-set\([^]*)/,Zt+"$1$`$1");case 4968:return Mt(Mt(o,/(.+:)(flex-)?(.*)/,Zt+"box-pack:$3"+$n+"flex-pack:$3"),/space-between/,"justify")+Zt+o+o;case 4200:if(!yl(o,/flex-|baseline/))return $n+"grid-column-align"+ku(o,r)+o;break;case 2592:case 3360:return $n+Mt(o,"template-","")+o;case 4384:case 3616:return s&&s.some(function(d,g){return r=g,yl(d.props,/grid-\w+-end/)})?~Dg(o+(s=s[r].value),"span",0)?o:$n+Mt(o,"-start","")+o+$n+"grid-row-span:"+(~Dg(s,"span",0)?yl(s,/\d+/):+yl(s,/\d+/)-+yl(o,/\d+/))+";":$n+Mt(o,"-start","")+o;case 4896:case 4128:return s&&s.some(function(d){return yl(d.props,/grid-\w+-start/)})?o:$n+Mt(Mt(o,"-end","-span"),"span ","")+o;case 4095:case 3583:case 4068:case 2532:return Mt(o,/(.+)-inline(.+)/,Zt+"$1$2")+o;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(to(o)-1-r>6)switch(yr(o,r+1)){case 109:if(yr(o,r+4)!==45)break;case 102:return Mt(o,/(.+:)(.+)-([^]+)/,"$1"+Zt+"$2-$3$1"+sp+(yr(o,r+3)==108?"$3":"$2-$3"))+o;case 115:return~Dg(o,"stretch",0)?s1(Mt(o,"stretch","fill-available"),r,s)+o:o}break;case 5152:case 5920:return Mt(o,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(d,g,b,S,y,E,O){return $n+g+":"+b+O+(S?$n+g+"-span:"+(y?E:+E-+b)+O:"")+o});case 4949:if(yr(o,r+6)===121)return Mt(o,":",":"+Zt)+o;break;case 6444:switch(yr(o,yr(o,14)===45?18:11)){case 120:return Mt(o,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,"$1"+Zt+(yr(o,14)===45?"inline-":"")+"box$3$1"+Zt+"$2$3$1"+$n+"$2box$3")+o;case 100:return Mt(o,":",":"+$n)+o}break;case 5719:case 2647:case 2135:case 3927:case 2391:return Mt(o,"scroll-","scroll-snap-")+o}return o}function _g(o,r){for(var s="",d=0;d<o.length;d++)s+=r(o[d],d,o,r)||"";return s}function ID(o,r,s,d){switch(o.type){case RD:if(o.children.length)break;case TD:case kD:case lx:return o.return=o.return||o.value;case ZS:return"";case e1:return o.return=o.value+"{"+_g(o.children,d)+"}";case Rg:if(!to(o.value=o.props.join(",")))return""}return to(s=_g(o.children,d))?o.return=o.value+"{"+s+"}":""}function UD(o){var r=r1(o);return function(s,d,g,b){for(var S="",y=0;y<r;y++)S+=o[y](s,d,g,b)||"";return S}}function BD(o){return function(r){r.root||(r=r.return)&&o(r)}}function HD(o,r,s,d){if(o.length>-1&&!o.return)switch(o.type){case lx:o.return=s1(o.value,o.length,s);return;case e1:return _g([hs(o,{value:Mt(o.value,"@","@"+Zt)})],d);case Rg:if(o.length)return MD(s=o.props,function(g){switch(yl(g,d=/(::plac\w+|:read-\w+)/)){case":read-only":case":read-write":rd(hs(o,{props:[Mt(g,/:(read-\w+)/,":"+sp+"$1")]})),rd(hs(o,{props:[g]})),ux(o,{props:i1(s,d)});break;case"::placeholder":rd(hs(o,{props:[Mt(g,/:(plac\w+)/,":"+Zt+"input-$1")]})),rd(hs(o,{props:[Mt(g,/:(plac\w+)/,":"+sp+"$1")]})),rd(hs(o,{props:[Mt(g,/:(plac\w+)/,$n+"input-$1")]})),rd(hs(o,{props:[g]})),ux(o,{props:i1(s,d)});break}return""})}}var VD={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},en={},Ru=typeof process<"u"&&en!==void 0&&(en.REACT_APP_SC_ATTR||en.SC_ATTR)||"data-styled",u1="active",c1="data-styled-version",Lg="6.3.8",fx=`/*!sc*/
`,zg=typeof window<"u"&&typeof document<"u",Du=On.createContext===void 0,WD=!!(typeof SC_DISABLE_SPEEDY=="boolean"?SC_DISABLE_SPEEDY:typeof process<"u"&&en!==void 0&&en.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&en.REACT_APP_SC_DISABLE_SPEEDY!==""?en.REACT_APP_SC_DISABLE_SPEEDY!=="false"&&en.REACT_APP_SC_DISABLE_SPEEDY:typeof process<"u"&&en!==void 0&&en.SC_DISABLE_SPEEDY!==void 0&&en.SC_DISABLE_SPEEDY!==""?en.SC_DISABLE_SPEEDY!=="false"&&en.SC_DISABLE_SPEEDY:en.NODE_ENV!=="production"),d1=/invalid hook call/i,Ng=new Set,YD=function(o,r){if(en.NODE_ENV!=="production"){if(Du)return;var s=r?' with the id of "'.concat(r,'"'):"",d="The component ".concat(o).concat(s,` has been created dynamically.
`)+`You may see this warning because you've called styled inside another component.
To resolve this only create new StyledComponents outside of any render method and function component.
See https://styled-components.com/docs/basics#define-styled-components-outside-of-the-render-method for more info.
`,g=console.error;try{var b=!0;console.error=function(S){for(var y=[],E=1;E<arguments.length;E++)y[E-1]=arguments[E];d1.test(S)?(b=!1,Ng.delete(d)):g.apply(void 0,ed([S],y,!1))},typeof On.useState=="function"&&On.useState(null),b&&!Ng.has(d)&&(console.warn(d),Ng.add(d))}catch(S){d1.test(S.message)&&Ng.delete(d)}finally{console.error=g}}},Pg=Object.freeze([]),id=Object.freeze({});function GD(o,r,s){return s===void 0&&(s=id),o.theme!==s.theme&&o.theme||r||s.theme}var px=new Set(["a","abbr","address","area","article","aside","audio","b","bdi","bdo","blockquote","body","button","br","canvas","caption","cite","code","col","colgroup","data","datalist","dd","del","details","dfn","dialog","div","dl","dt","em","embed","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","html","i","iframe","img","input","ins","kbd","label","legend","li","main","map","mark","menu","meter","nav","object","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","search","section","select","slot","small","span","strong","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","u","ul","var","video","wbr","circle","clipPath","defs","ellipse","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feDropShadow","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feImage","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence","filter","foreignObject","g","image","line","linearGradient","marker","mask","path","pattern","polygon","polyline","radialGradient","rect","stop","svg","switch","symbol","text","textPath","tspan","use"]),KD=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,QD=/(^-|-$)/g;function f1(o){return o.replace(KD,"-").replace(QD,"")}var qD=/(a)(d)/gi,p1=function(o){return String.fromCharCode(o+(o>25?39:97))};function hx(o){var r,s="";for(r=Math.abs(o);r>52;r=r/52|0)s=p1(r%52)+s;return(p1(r%52)+s).replace(qD,"$1-$2")}var gx,Mu=function(o,r){for(var s=r.length;s;)o=33*o^r.charCodeAt(--s);return o},h1=function(o){return Mu(5381,o)};function XD(o){return hx(h1(o)>>>0)}function g1(o){return en.NODE_ENV!=="production"&&typeof o=="string"&&o||o.displayName||o.name||"Component"}function mx(o){return typeof o=="string"&&(en.NODE_ENV==="production"||o.charAt(0)===o.charAt(0).toLowerCase())}var m1=typeof Symbol=="function"&&Symbol.for,v1=m1?Symbol.for("react.memo"):60115,JD=m1?Symbol.for("react.forward_ref"):60112,ZD={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},eM={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},y1={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},tM=((gx={})[JD]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},gx[v1]=y1,gx);function x1(o){return("type"in(r=o)&&r.type.$$typeof)===v1?y1:"$$typeof"in o?tM[o.$$typeof]:ZD;var r}var nM=Object.defineProperty,rM=Object.getOwnPropertyNames,b1=Object.getOwnPropertySymbols,iM=Object.getOwnPropertyDescriptor,aM=Object.getPrototypeOf,w1=Object.prototype;function S1(o,r,s){if(typeof r!="string"){if(w1){var d=aM(r);d&&d!==w1&&S1(o,d,s)}var g=rM(r);b1&&(g=g.concat(b1(r)));for(var b=x1(o),S=x1(r),y=0;y<g.length;++y){var E=g[y];if(!(E in eM||s&&s[E]||S&&E in S||b&&E in b)){var O=iM(r,E);try{nM(o,E,O)}catch{}}}}return o}function ad(o){return typeof o=="function"}function vx(o){return typeof o=="object"&&"styledComponentId"in o}function Ou(o,r){return o&&r?"".concat(o," ").concat(r):o||r||""}function C1(o,r){if(o.length===0)return"";for(var s=o[0],d=1;d<o.length;d++)s+=o[d];return s}function od(o){return o!==null&&typeof o=="object"&&o.constructor.name===Object.name&&!("props"in o&&o.$$typeof)}function yx(o,r,s){if(s===void 0&&(s=!1),!s&&!od(o)&&!Array.isArray(o))return r;if(Array.isArray(r))for(var d=0;d<r.length;d++)o[d]=yx(o[d],r[d]);else if(od(r))for(var d in r)o[d]=yx(o[d],r[d]);return o}function xx(o,r){Object.defineProperty(o,"toString",{value:r})}var oM=en.NODE_ENV!=="production"?{1:`Cannot create styled-component for component: %s.

`,2:`Can't collect styles once you've consumed a \`ServerStyleSheet\`'s styles! \`ServerStyleSheet\` is a one off instance for each server-side render cycle.

- Are you trying to reuse it across renders?
- Are you accidentally calling collectStyles twice?

`,3:`Streaming SSR is only supported in a Node.js environment; Please do not try to call this method in the browser.

`,4:`The \`StyleSheetManager\` expects a valid target or sheet prop!

- Does this error occur on the client and is your target falsy?
- Does this error occur on the server and is the sheet falsy?

`,5:`The clone method cannot be used on the client!

- Are you running in a client-like environment on the server?
- Are you trying to run SSR on the client?

`,6:`Trying to insert a new style tag, but the given Node is unmounted!

- Are you using a custom target that isn't mounted?
- Does your document not have a valid head element?
- Have you accidentally removed a style tag manually?

`,7:'ThemeProvider: Please return an object from your "theme" prop function, e.g.\n\n```js\ntheme={() => ({})}\n```\n\n',8:`ThemeProvider: Please make your "theme" prop an object.

`,9:"Missing document `<head>`\n\n",10:`Cannot find a StyleSheet instance. Usually this happens if there are multiple copies of styled-components loaded at once. Check out this issue for how to troubleshoot and fix the common cases where this situation can happen: https://github.com/styled-components/styled-components/issues/1941#issuecomment-417862021

`,11:`_This error was replaced with a dev-time warning, it will be deleted for v4 final._ [createGlobalStyle] received children which will not be rendered. Please use the component without passing children elements.

`,12:"It seems you are interpolating a keyframe declaration (%s) into an untagged string. This was supported in styled-components v3, but is not longer supported in v4 as keyframes are now injected on-demand. Please wrap your string in the css\\`\\` helper which ensures the styles are injected correctly. See https://www.styled-components.com/docs/api#css\n\n",13:`%s is not a styled component and cannot be referred to via component selector. See https://www.styled-components.com/docs/advanced#referring-to-other-components for more details.

`,14:`ThemeProvider: "theme" prop is required.

`,15:"A stylis plugin has been supplied that is not named. We need a name for each plugin to be able to prevent styling collisions between different stylis configurations within the same app. Before you pass your plugin to `<StyleSheetManager stylisPlugins={[]}>`, please make sure each plugin is uniquely-named, e.g.\n\n```js\nObject.defineProperty(importedPlugin, 'name', { value: 'some-unique-name' });\n```\n\n",16:`Reached the limit of how many styled components may be created at group %s.
You may only create up to 1,073,741,824 components. If you're creating components dynamically,
as for instance in your render method then you may be running into this limitation.

`,17:`CSSStyleSheet could not be found on HTMLStyleElement.
Has styled-components' style tag been unmounted or altered by another script?
`,18:"ThemeProvider: Please make sure your useTheme hook is within a `<ThemeProvider>`"}:{};function lM(){for(var o=[],r=0;r<arguments.length;r++)o[r]=arguments[r];for(var s=o[0],d=[],g=1,b=o.length;g<b;g+=1)d.push(o[g]);return d.forEach(function(S){s=s.replace(/%[a-z]/,S)}),s}function ld(o){for(var r=[],s=1;s<arguments.length;s++)r[s-1]=arguments[s];return en.NODE_ENV==="production"?new Error("An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#".concat(o," for more information.").concat(r.length>0?" Args: ".concat(r.join(", ")):"")):new Error(lM.apply(void 0,ed([oM[o]],r,!1)).trim())}var sM=function(){function o(r){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=r}return o.prototype.indexOfGroup=function(r){for(var s=0,d=0;d<r;d++)s+=this.groupSizes[d];return s},o.prototype.insertRules=function(r,s){if(r>=this.groupSizes.length){for(var d=this.groupSizes,g=d.length,b=g;r>=b;)if((b<<=1)<0)throw ld(16,"".concat(r));this.groupSizes=new Uint32Array(b),this.groupSizes.set(d),this.length=b;for(var S=g;S<b;S++)this.groupSizes[S]=0}for(var y=this.indexOfGroup(r+1),E=(S=0,s.length);S<E;S++)this.tag.insertRule(y,s[S])&&(this.groupSizes[r]++,y++)},o.prototype.clearGroup=function(r){if(r<this.length){var s=this.groupSizes[r],d=this.indexOfGroup(r),g=d+s;this.groupSizes[r]=0;for(var b=d;b<g;b++)this.tag.deleteRule(d)}},o.prototype.getGroup=function(r){var s="";if(r>=this.length||this.groupSizes[r]===0)return s;for(var d=this.groupSizes[r],g=this.indexOfGroup(r),b=g+d,S=g;S<b;S++)s+="".concat(this.tag.getRule(S)).concat(fx);return s},o}(),uM=1<<30,Fg=new Map,Ig=new Map,Ug=1,dp=function(o){if(Fg.has(o))return Fg.get(o);for(;Ig.has(Ug);)Ug++;var r=Ug++;if(en.NODE_ENV!=="production"&&((0|r)<0||r>uM))throw ld(16,"".concat(r));return Fg.set(o,r),Ig.set(r,o),r},cM=function(o,r){Ug=r+1,Fg.set(o,r),Ig.set(r,o)},dM="style[".concat(Ru,"][").concat(c1,'="').concat(Lg,'"]'),fM=new RegExp("^".concat(Ru,'\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)')),pM=function(o,r,s){for(var d,g=s.split(","),b=0,S=g.length;b<S;b++)(d=g[b])&&o.registerName(r,d)},hM=function(o,r){for(var s,d=((s=r.textContent)!==null&&s!==void 0?s:"").split(fx),g=[],b=0,S=d.length;b<S;b++){var y=d[b].trim();if(y){var E=y.match(fM);if(E){var O=0|parseInt(E[1],10),$=E[2];O!==0&&(cM($,O),pM(o,$,E[3]),o.getTag().insertRules(O,g)),g.length=0}else g.push(y)}}},E1=function(o){for(var r=document.querySelectorAll(dM),s=0,d=r.length;s<d;s++){var g=r[s];g&&g.getAttribute(Ru)!==u1&&(hM(o,g),g.parentNode&&g.parentNode.removeChild(g))}};function gM(){return typeof __webpack_nonce__<"u"?__webpack_nonce__:null}var T1=function(o){var r=document.head,s=o||r,d=document.createElement("style"),g=function(y){var E=Array.from(y.querySelectorAll("style[".concat(Ru,"]")));return E[E.length-1]}(s),b=g!==void 0?g.nextSibling:null;d.setAttribute(Ru,u1),d.setAttribute(c1,Lg);var S=gM();return S&&d.setAttribute("nonce",S),s.insertBefore(d,b),d},mM=function(){function o(r){this.element=T1(r),this.element.appendChild(document.createTextNode("")),this.sheet=function(s){if(s.sheet)return s.sheet;for(var d=document.styleSheets,g=0,b=d.length;g<b;g++){var S=d[g];if(S.ownerNode===s)return S}throw ld(17)}(this.element),this.length=0}return o.prototype.insertRule=function(r,s){try{return this.sheet.insertRule(s,r),this.length++,!0}catch{return!1}},o.prototype.deleteRule=function(r){this.sheet.deleteRule(r),this.length--},o.prototype.getRule=function(r){var s=this.sheet.cssRules[r];return s&&s.cssText?s.cssText:""},o}(),vM=function(){function o(r){this.element=T1(r),this.nodes=this.element.childNodes,this.length=0}return o.prototype.insertRule=function(r,s){if(r<=this.length&&r>=0){var d=document.createTextNode(s);return this.element.insertBefore(d,this.nodes[r]||null),this.length++,!0}return!1},o.prototype.deleteRule=function(r){this.element.removeChild(this.nodes[r]),this.length--},o.prototype.getRule=function(r){return r<this.length?this.nodes[r].textContent:""},o}(),yM=function(){function o(r){this.rules=[],this.length=0}return o.prototype.insertRule=function(r,s){return r<=this.length&&(this.rules.splice(r,0,s),this.length++,!0)},o.prototype.deleteRule=function(r){this.rules.splice(r,1),this.length--},o.prototype.getRule=function(r){return r<this.length?this.rules[r]:""},o}(),k1=zg,xM={isServer:!zg,useCSSOMInjection:!WD},R1=function(){function o(r,s,d){r===void 0&&(r=id),s===void 0&&(s={});var g=this;this.options=Wi(Wi({},xM),r),this.gs=s,this.names=new Map(d),this.server=!!r.isServer,!this.server&&zg&&k1&&(k1=!1,E1(this)),xx(this,function(){return function(b){for(var S=b.getTag(),y=S.length,E="",O=function(P){var j=function(ze){return Ig.get(ze)}(P);if(j===void 0)return"continue";var H=b.names.get(j),L=S.getGroup(P);if(H===void 0||!H.size||L.length===0)return"continue";var Q="".concat(Ru,".g").concat(P,'[id="').concat(j,'"]'),xe="";H!==void 0&&H.forEach(function(ze){ze.length>0&&(xe+="".concat(ze,","))}),E+="".concat(L).concat(Q,'{content:"').concat(xe,'"}').concat(fx)},$=0;$<y;$++)O($);return E}(g)})}return o.registerId=function(r){return dp(r)},o.prototype.rehydrate=function(){!this.server&&zg&&E1(this)},o.prototype.reconstructWithOptions=function(r,s){return s===void 0&&(s=!0),new o(Wi(Wi({},this.options),r),this.gs,s&&this.names||void 0)},o.prototype.allocateGSInstance=function(r){return this.gs[r]=(this.gs[r]||0)+1},o.prototype.getTag=function(){return this.tag||(this.tag=(r=function(s){var d=s.useCSSOMInjection,g=s.target;return s.isServer?new yM(g):d?new mM(g):new vM(g)}(this.options),new sM(r)));var r},o.prototype.hasNameForId=function(r,s){return this.names.has(r)&&this.names.get(r).has(s)},o.prototype.registerName=function(r,s){if(dp(r),this.names.has(r))this.names.get(r).add(s);else{var d=new Set;d.add(s),this.names.set(r,d)}},o.prototype.insertRules=function(r,s,d){this.registerName(r,s),this.getTag().insertRules(dp(r),d)},o.prototype.clearNames=function(r){this.names.has(r)&&this.names.get(r).clear()},o.prototype.clearRules=function(r){this.getTag().clearGroup(dp(r)),this.clearNames(r)},o.prototype.clearTag=function(){this.tag=void 0},o}(),bM=/&/g,sd=47;function D1(o){if(o.indexOf("}")===-1)return!1;for(var r=o.length,s=0,d=0,g=!1,b=0;b<r;b++){var S=o.charCodeAt(b);if(d!==0||g||S!==sd||o.charCodeAt(b+1)!==42)if(g)S===42&&o.charCodeAt(b+1)===sd&&(g=!1,b++);else if(S!==34&&S!==39||b!==0&&o.charCodeAt(b-1)===92){if(d===0){if(S===123)s++;else if(S===125&&--s<0)return!0}}else d===0?d=S:d===S&&(d=0);else g=!0,b++}return s!==0||d!==0}function M1(o,r){return o.map(function(s){return s.type==="rule"&&(s.value="".concat(r," ").concat(s.value),s.value=s.value.replaceAll(",",",".concat(r," ")),s.props=s.props.map(function(d){return"".concat(r," ").concat(d)})),Array.isArray(s.children)&&s.type!=="@keyframes"&&(s.children=M1(s.children,r)),s})}function wM(o){var r,s,d,g=id,b=g.options,S=b===void 0?id:b,y=g.plugins,E=y===void 0?Pg:y,O=function(j,H,L){return L.startsWith(s)&&L.endsWith(s)&&L.replaceAll(s,"").length>0?".".concat(r):j},$=E.slice();$.push(function(j){j.type===Rg&&j.value.includes("&")&&(j.props[0]=j.props[0].replace(bM,s).replace(d,O))}),S.prefix&&$.push(HD),$.push(ID);var P=function(j,H,L,Q){H===void 0&&(H=""),L===void 0&&(L=""),Q===void 0&&(Q="&"),r=Q,s=H,d=new RegExp("\\".concat(s,"\\b"),"g");var xe=function(ae){if(!D1(ae))return ae;for(var ce=ae.length,Ee="",le=0,ue=0,$e=0,ft=!1,He=0;He<ce;He++){var Tt=ae.charCodeAt(He);if($e!==0||ft||Tt!==sd||ae.charCodeAt(He+1)!==42)if(ft)Tt===42&&ae.charCodeAt(He+1)===sd&&(ft=!1,He++);else if(Tt!==34&&Tt!==39||He!==0&&ae.charCodeAt(He-1)===92){if($e===0)if(Tt===123)ue++;else if(Tt===125){if(--ue<0){for(var bt=He+1;bt<ce;){var rt=ae.charCodeAt(bt);if(rt===59||rt===10)break;bt++}bt<ce&&ae.charCodeAt(bt)===59&&bt++,ue=0,He=bt-1,le=bt;continue}ue===0&&(Ee+=ae.substring(le,He+1),le=He+1)}else Tt===59&&ue===0&&(Ee+=ae.substring(le,He+1),le=He+1)}else $e===0?$e=Tt:$e===Tt&&($e=0);else ft=!0,He++}if(le<ce){var Be=ae.substring(le);D1(Be)||(Ee+=Be)}return Ee}(function(ae){if(ae.indexOf("//")===-1)return ae;for(var ce=ae.length,Ee=[],le=0,ue=0,$e=0,ft=0;ue<ce;){var He=ae.charCodeAt(ue);if(He!==34&&He!==39||ue!==0&&ae.charCodeAt(ue-1)===92)if($e===0)if(He===40&&ue>=3&&(32|ae.charCodeAt(ue-1))==108&&(32|ae.charCodeAt(ue-2))==114&&(32|ae.charCodeAt(ue-3))==117)ft=1,ue++;else if(ft>0)He===41?ft--:He===40&&ft++,ue++;else if(He===sd&&ue+1<ce&&ae.charCodeAt(ue+1)===sd){for(ue>le&&Ee.push(ae.substring(le,ue));ue<ce&&ae.charCodeAt(ue)!==10;)ue++;le=ue}else ue++;else ue++;else $e===0?$e=He:$e===He&&($e=0),ue++}return le===0?ae:(le<ce&&Ee.push(ae.substring(le)),Ee.join(""))}(j)),ze=PD(L||H?"".concat(L," ").concat(H," { ").concat(xe," }"):xe);S.namespace&&(ze=M1(ze,S.namespace));var fe=[];return _g(ze,UD($.concat(BD(function(ae){return fe.push(ae)})))),fe};return P.hash=E.length?E.reduce(function(j,H){return H.name||ld(15),Mu(j,H.name)},5381).toString():"",P}var SM=new R1,bx=wM(),wx={shouldForwardProp:void 0,styleSheet:SM,stylis:bx},O1=Du?{Provider:function(o){return o.children},Consumer:function(o){return(0,o.children)(wx)}}:On.createContext(wx);O1.Consumer,Du||On.createContext(void 0);function $1(){return Du?wx:On.useContext(O1)}var A1=function(){function o(r,s){var d=this;this.inject=function(g,b){b===void 0&&(b=bx);var S=d.name+b.hash;g.hasNameForId(d.id,S)||g.insertRules(d.id,S,b(d.rules,S,"@keyframes"))},this.name=r,this.id="sc-keyframes-".concat(r),this.rules=s,xx(this,function(){throw ld(12,String(d.name))})}return o.prototype.getName=function(r){return r===void 0&&(r=bx),this.name+r.hash},o}();function CM(o,r){return r==null||typeof r=="boolean"||r===""?"":typeof r!="number"||r===0||o in VD||o.startsWith("--")?String(r).trim():"".concat(r,"px")}var EM=function(o){return o>="A"&&o<="Z"};function j1(o){for(var r="",s=0;s<o.length;s++){var d=o[s];if(s===1&&d==="-"&&o[0]==="-")return o;EM(d)?r+="-"+d.toLowerCase():r+=d}return r.startsWith("ms-")?"-"+r:r}var _1=function(o){return o==null||o===!1||o===""},L1=function(o){var r=[];for(var s in o){var d=o[s];o.hasOwnProperty(s)&&!_1(d)&&(Array.isArray(d)&&d.isCss||ad(d)?r.push("".concat(j1(s),":"),d,";"):od(d)?r.push.apply(r,ed(ed(["".concat(s," {")],L1(d),!1),["}"],!1)):r.push("".concat(j1(s),": ").concat(CM(s,d),";")))}return r};function $u(o,r,s,d){if(_1(o))return[];if(vx(o))return[".".concat(o.styledComponentId)];if(ad(o)){if(!ad(b=o)||b.prototype&&b.prototype.isReactComponent||!r)return[o];var g=o(r);return en.NODE_ENV==="production"||typeof g!="object"||Array.isArray(g)||g instanceof A1||od(g)||g===null||console.error("".concat(g1(o)," is not a styled component and cannot be referred to via component selector. See https://www.styled-components.com/docs/advanced#referring-to-other-components for more details.")),$u(g,r,s,d)}var b;return o instanceof A1?s?(o.inject(s,d),[o.getName(d)]):[o]:od(o)?L1(o):Array.isArray(o)?Array.prototype.concat.apply(Pg,o.map(function(S){return $u(S,r,s,d)})):[o.toString()]}function TM(o){for(var r=0;r<o.length;r+=1){var s=o[r];if(ad(s)&&!vx(s))return!1}return!0}var kM=h1(Lg),RM=function(){function o(r,s,d){this.rules=r,this.staticRulesId="",this.isStatic=en.NODE_ENV==="production"&&(d===void 0||d.isStatic)&&TM(r),this.componentId=s,this.baseHash=Mu(kM,s),this.baseStyle=d,R1.registerId(s)}return o.prototype.generateAndInjectStyles=function(r,s,d){var g=this.baseStyle?this.baseStyle.generateAndInjectStyles(r,s,d).className:"";if(this.isStatic&&!d.hash)if(this.staticRulesId&&s.hasNameForId(this.componentId,this.staticRulesId))g=Ou(g,this.staticRulesId);else{var b=C1($u(this.rules,r,s,d)),S=hx(Mu(this.baseHash,b)>>>0);if(!s.hasNameForId(this.componentId,S)){var y=d(b,".".concat(S),void 0,this.componentId);s.insertRules(this.componentId,S,y)}g=Ou(g,S),this.staticRulesId=S}else{for(var E=Mu(this.baseHash,d.hash),O="",$=0;$<this.rules.length;$++){var P=this.rules[$];if(typeof P=="string")O+=P,en.NODE_ENV!=="production"&&(E=Mu(E,P));else if(P){var j=C1($u(P,r,s,d));E=Mu(E,j+$),O+=j}}if(O){var H=hx(E>>>0);if(!s.hasNameForId(this.componentId,H)){var L=d(O,".".concat(H),void 0,this.componentId);s.insertRules(this.componentId,H,L)}g=Ou(g,H)}}return{className:g,css:typeof window>"u"?s.getTag().getGroup(dp(this.componentId)):""}},o}(),z1=Du?{Provider:function(o){return o.children},Consumer:function(o){return(0,o.children)(void 0)}}:On.createContext(void 0);z1.Consumer;var Sx={},N1=new Set;function DM(o,r,s){var d=vx(o),g=o,b=!mx(o),S=r.attrs,y=S===void 0?Pg:S,E=r.componentId,O=E===void 0?function(ce,Ee){var le=typeof ce!="string"?"sc":f1(ce);Sx[le]=(Sx[le]||0)+1;var ue="".concat(le,"-").concat(XD(Lg+le+Sx[le]));return Ee?"".concat(Ee,"-").concat(ue):ue}(r.displayName,r.parentComponentId):E,$=r.displayName,P=$===void 0?function(ce){return mx(ce)?"styled.".concat(ce):"Styled(".concat(g1(ce),")")}(o):$,j=r.displayName&&r.componentId?"".concat(f1(r.displayName),"-").concat(r.componentId):r.componentId||O,H=d&&g.attrs?g.attrs.concat(y).filter(Boolean):y,L=r.shouldForwardProp;if(d&&g.shouldForwardProp){var Q=g.shouldForwardProp;if(r.shouldForwardProp){var xe=r.shouldForwardProp;L=function(ce,Ee){return Q(ce,Ee)&&xe(ce,Ee)}}else L=Q}var ze=new RM(s,j,d?g.componentStyle:void 0);function fe(ce,Ee){return function(le,ue,$e){var ft=le.attrs,He=le.componentStyle,Tt=le.defaultProps,bt=le.foldedComponentIds,rt=le.styledComponentId,Be=le.target,Ht=Du?void 0:On.useContext(z1),kt=$1(),pt=le.shouldForwardProp||kt.shouldForwardProp;en.NODE_ENV!=="production"&&On.useDebugValue&&On.useDebugValue(rt);var se=GD(ue,Ht,Tt)||id,ke=function(tt,vt,Ut){for(var pn,an=Wi(Wi({},vt),{className:void 0,theme:Ut}),Pn=0;Pn<tt.length;Pn+=1){var xn=ad(pn=tt[Pn])?pn(an):pn;for(var An in xn)An==="className"?an.className=Ou(an.className,xn[An]):An==="style"?an.style=Wi(Wi({},an.style),xn[An]):an[An]=xn[An]}return"className"in vt&&typeof vt.className=="string"&&(an.className=Ou(an.className,vt.className)),an}(ft,ue,se),be=ke.as||Be,B={};for(var re in ke)ke[re]===void 0||re[0]==="$"||re==="as"||re==="theme"&&ke.theme===se||(re==="forwardedAs"?B.as=ke.forwardedAs:pt&&!pt(re,be)||(B[re]=ke[re],pt||en.NODE_ENV!=="development"||ED(re)||N1.has(re)||!px.has(be)||(N1.add(re),console.warn('styled-components: it looks like an unknown prop "'.concat(re,'" is being sent through to the DOM, which will likely trigger a React console error. If you would like automatic filtering of unknown props, you can opt-into that behavior via `<StyleSheetManager shouldForwardProp={...}>` (connect an API like `@emotion/is-prop-valid`) or consider using transient props (`$` prefix for automatic filtering.)')))));var Ve=function(tt,vt){var Ut=$1(),pn=tt.generateAndInjectStyles(vt,Ut.styleSheet,Ut.stylis);return en.NODE_ENV!=="production"&&On.useDebugValue&&On.useDebugValue(pn.className),pn}(He,ke),et=Ve.className,it=Ve.css;en.NODE_ENV!=="production"&&le.warnTooManyClasses&&le.warnTooManyClasses(et);var ht=Ou(bt,rt);et&&(ht+=" "+et),ke.className&&(ht+=" "+ke.className),B[mx(be)&&!px.has(be)?"class":"className"]=ht,$e&&(B.ref=$e);var Ot=Je.createElement(be,B);return Du&&it?On.createElement(On.Fragment,null,On.createElement("style",{precedence:"styled-components",href:"sc-".concat(rt,"-").concat(et),children:it}),Ot):Ot}(ae,ce,Ee)}fe.displayName=P;var ae=On.forwardRef(fe);return ae.attrs=H,ae.componentStyle=ze,ae.displayName=P,ae.shouldForwardProp=L,ae.foldedComponentIds=d?Ou(g.foldedComponentIds,g.styledComponentId):"",ae.styledComponentId=j,ae.target=d?g.target:o,Object.defineProperty(ae,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(ce){this._foldedDefaultProps=d?function(Ee){for(var le=[],ue=1;ue<arguments.length;ue++)le[ue-1]=arguments[ue];for(var $e=0,ft=le;$e<ft.length;$e++)yx(Ee,ft[$e],!0);return Ee}({},g.defaultProps,ce):ce}}),en.NODE_ENV!=="production"&&(YD(P,j),ae.warnTooManyClasses=function(ce,Ee){var le={},ue=!1;return function($e){if(!ue&&(le[$e]=!0,Object.keys(le).length>=200)){var ft=Ee?' with the id of "'.concat(Ee,'"'):"";console.warn("Over ".concat(200," classes were generated for component ").concat(ce).concat(ft,`.
`)+`Consider using the attrs method, together with a style object for frequently changed styles.
Example:
  const Component = styled.div.attrs(props => ({
    style: {
      background: props.background,
    },
  }))\`width: 100%;\`

  <Component />`),ue=!0,le={}}}}(P,j)),xx(ae,function(){return".".concat(ae.styledComponentId)}),b&&S1(ae,o,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),ae}function P1(o,r){for(var s=[o[0]],d=0,g=r.length;d<g;d+=1)s.push(r[d],o[d+1]);return s}var F1=function(o){return Object.assign(o,{isCss:!0})};function ud(o){for(var r=[],s=1;s<arguments.length;s++)r[s-1]=arguments[s];if(ad(o)||od(o))return F1($u(P1(Pg,ed([o],r,!0))));var d=o;return r.length===0&&d.length===1&&typeof d[0]=="string"?$u(d):F1($u(P1(d,r)))}function Cx(o,r,s){if(s===void 0&&(s=id),!r)throw ld(1,r);var d=function(g){for(var b=[],S=1;S<arguments.length;S++)b[S-1]=arguments[S];return o(r,s,ud.apply(void 0,ed([g],b,!1)))};return d.attrs=function(g){return Cx(o,r,Wi(Wi({},s),{attrs:Array.prototype.concat(s.attrs,g).filter(Boolean)}))},d.withConfig=function(g){return Cx(o,r,Wi(Wi({},s),g))},d}var I1=function(o){return Cx(DM,o)},D=I1;px.forEach(function(o){D[o]=I1(o)}),en.NODE_ENV!=="production"&&typeof navigator<"u"&&navigator.product==="ReactNative"&&console.warn(`It looks like you've imported 'styled-components' on React Native.
Perhaps you're looking to import 'styled-components/native'?
Read more about this at https://www.styled-components.com/docs/basics#react-native`);var Bg="__sc-".concat(Ru,"__");en.NODE_ENV!=="production"&&en.NODE_ENV!=="test"&&typeof window<"u"&&(window[Bg]||(window[Bg]=0),window[Bg]===1&&console.warn(`It looks like there are several instances of 'styled-components' initialized in this application. This may cause dynamic styles to not render properly, errors during the rehydration process, a missing theme prop, and makes your application bigger without good reason.

See https://styled-components.com/docs/faqs#why-am-i-getting-a-warning-about-several-instances-of-module-on-the-page for more info.`),window[Bg]+=1);const V={colors:{windowBg:"#152029de",panelBg:"#04161c",panelBgGlass:"rgba(4, 22, 28, 0.22)",line:"#496791",text:"#ffffff",textAccent:"#C6E2FF",textDim:"#7f9bb8",healthOk:"#427231",healthCrit:"#ed6738",warning:"#e1b000",warningSoft:"#e6b400",statusOk:"limegreen",statusAlert:"#e1b000",statusBad:"red",statusPending:"#00b8e6",enhText:"#d8be86",enhTitle:"#e8cf93",enhBg:"rgba(169, 128, 56, 0.30)",enhLine:"#8a6d3b",custom:"#cccc00",chromeText:"#deebff",overlayBg:"black",overlayBgSoft:"rgba(0, 0, 0, 0.65)"},fonts:{body:"arial",mono:'Consolas, "Lucida Console", monospace'},radii:{modal:"0px",tooltip:"7px"},hud:{btn:"36px",btnSmall:"25px",icon:"26px",iconSmall:"17px",glyph:"30px",glyphSmall:"20px"}},MM=D.div`
    border: 1px solid ${V.colors.line};
    color: ${V.colors.chromeText};
    background-color: ${V.colors.windowBg};
    font-family:${V.fonts.body};
`,OM=D.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,Hg=D(MM)`
    box-shadow: 5px 5px 10px ${V.colors.overlayBg};
`,$M=D.span`
    font-family: arial;
    font-size: 16px;
    text-transform: uppercase;
    color: #deebff;
    padding: 10px;
    font-weight: bold;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        padding: 5px 10px;
        font-size: 14px;
    }
`;D($M)`
    font-weight: normal;
`;const Yi=ud`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;class Oa extends Je.Component{render(){return m.jsxs(AM,{children:[m.jsx(jM,{children:this.props.label}),m.jsx(_M,{type:this.props.type||"text",value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:"0"})]})}}const AM=D.div`
    width: 100%;
    box-sizing: border-box; /* keep the 14px side padding inside 100% so the row
                               never exceeds the Body width (no h-scrollbar) */
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 14px;
    border-bottom: 1px solid rgba(88, 126, 141, 0.14);

    &:hover {
        background-color: rgba(73, 196, 212, 0.05);
    }

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        padding: 4px 10px;
        gap: 8px;
    }
`,jM=D.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,_M=D.input`
    flex: 0 0 128px;
    box-sizing: border-box;
    padding: 5px 8px;
    font-family: "Courier New", monospace;
    font-size: 12px;
    letter-spacing: 0.04em;
    text-align: center;
    text-transform: uppercase;
    color: #d6f7fd;
    background-color: #041e24;
    border: 1px solid #3a6570;
    border-radius: 4px;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;

    &::placeholder {
        color: #4d7580;
        text-transform: none;
    }

    &:hover {
        border-color: #49c4d4;
    }

    &:focus {
        border-color: #49c4d4;
        box-shadow: 0 0 0 1px #49c4d4, 0 0 10px rgba(73, 196, 212, 0.35);
    }

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        flex-basis: 96px;
        font-size: 11px;
        padding: 3px 6px;
    }
`;class LM extends Je.Component{render(){const{children:r,className:s}=this.props;return m.jsx("div",{className:s,children:r})}}const Ex=D(LM)`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${V.fonts.body};
    font-size:12px;
    color:${V.colors.text};
    background-color:${V.colors.overlayBgSoft};
    border-radius: ${V.radii.tooltip};
    -moz-border-radius: ${V.radii.tooltip};
    -webkit-border-radius: ${V.radii.tooltip};
    padding:3px 3px 3px 3px;
    padding-bottom: 8px;
`,Tx=D.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,Au=D.div`
    color: ${o=>o.$type=="good"?"#6fc126;":o.$type=="bad"?"#ff7b3f;":"white;"}
    font-weight: ${o=>o.$important?"bold":"inherit"};
    font-size: ${o=>o.$important?"14px":"12px"};
    margin-top: ${o=>o.$space?"14px":"0"};
`;class zM extends Je.Component{getOnChange(r){return s=>{this.props.set(r,s.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(r){return s=>{if(console.log("keydown"),s.preventDefault(),s.stopPropagation(),!U1[s.keyCode])return;const d={keyCode:s.keyCode,shiftKey:s.shiftKey,altKey:s.altKey,ctrlKey:s.ctrlKey,metaKey:s.metaKey};this.props.set(r,d),this.props.save(),this.forceUpdate()}}getKey(r){return console.log(this.props.settings),NM(this.props.settings[r])}get(r){return this.props.settings[r]}render(){return m.jsx(PM,{onClick:this.props.close,children:m.jsxs(FM,{onClick:r=>r.stopPropagation(),children:[m.jsxs(IM,{children:[m.jsx(UM,{children:"Player Settings"}),m.jsx(BM,{onClick:this.props.close,title:"Close",children:"✕"})]}),m.jsxs(HM,{children:[m.jsx(VM,{children:"Settings apply to this browser and device only. Reload the page for changes to take effect."}),m.jsx(Vg,{children:"Keys"}),m.jsx(Oa,{label:"Display ALL Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllEW"),value:this.getKey.call(this,"ShowAllEW")}),m.jsx(Oa,{label:"Display FRIENDLY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyEW"),value:this.getKey.call(this,"ShowFriendlyEW")}),m.jsx(Oa,{label:"Display ENEMY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyEW"),value:this.getKey.call(this,"ShowEnemyEW")}),m.jsx(Oa,{label:"Display ALL Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllBallistics"),value:this.getKey.call(this,"ShowAllBallistics")}),m.jsx(Oa,{label:"Display FRIENDLY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyBallistics"),value:this.getKey.call(this,"ShowFriendlyBallistics")}),m.jsx(Oa,{label:"Display ENEMY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyBallistics"),value:this.getKey.call(this,"ShowEnemyBallistics")}),m.jsx(Oa,{label:"Toggle RULER tool",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleLoS"),value:this.getKey.call(this,"ToggleLoS")}),m.jsx(Oa,{label:"Toggle HEX numbers",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleHexNumbers"),value:this.getKey.call(this,"ToggleHexNumbers")}),m.jsx(Oa,{label:"Toggle MAP background",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleBackground"),value:this.getKey.call(this,"ToggleBackground")}),m.jsx(Vg,{children:"Replay"}),m.jsx(Oa,{label:"Play / pause Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"TogglePlayPause"),value:this.getKey.call(this,"TogglePlayPause")}),m.jsx(Vg,{children:"Sound"}),m.jsx(Oa,{label:"Toggle sound in Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleSound"),value:this.getKey.call(this,"ToggleSound")}),m.jsx(Vg,{children:"Visual"}),m.jsx(Oa,{placeholder:"0",type:"number",label:"Zoom level to switch to strategic view",onChange:this.getOnChange.call(this,"ZoomLevelToStrategic"),value:this.get.call(this,"ZoomLevelToStrategic")}),m.jsx(WM,{children:"Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners."})]})]})})}}const NM=o=>{let r=U1[o.keyCode];return r=r.toUpperCase(),o.shiftKey&&(r+=" + shift"),o.altKey&&(r+=" + alt"),o.ctrlKey&&(r+=" + ctrl"),o.metaKey&&(r+=" + cmd"),r},PM=D(OM)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,FM=D.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: ${V.colors.panelBg};
    border: 1px solid ${V.colors.line};
    border-radius: ${V.radii.modal};
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: ${V.colors.chromeText};
    font-family: ${V.fonts.body};
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`,IM=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${V.colors.windowBg};
    border-bottom: 1px solid ${V.colors.line};
`,UM=D.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,BM=D.div`
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    line-height: 1;
    color: #7ba2ea;
    border: 1px solid transparent;
    border-radius: 4px;
    transition: color 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
    ${Yi}

    &:hover {
        color: #d6f7fd;
        border-color: #49c4d4;
        background-color: rgba(73, 196, 212, 0.12);
    }
`,HM=D.div`
    overflow-y: auto;
    overflow-x: hidden; /* rows are box-sized to fit; never scroll sideways */
    padding: 4px 0 12px;

    &::-webkit-scrollbar {
        width: 10px;
    }
    &::-webkit-scrollbar-track {
        background: #0d1620;
    }
    &::-webkit-scrollbar-thumb {
        background: #3c5574;
        border-radius: 6px;
    }
    &::-webkit-scrollbar-thumb:hover {
        background: #5a7ea8;
    }
`,VM=D.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`,Vg=D.div`
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 14px 14px 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: #49c4d4;

    &::after {
        content: "";
        flex: 1;
        height: 1px;
        background: linear-gradient(to right, rgba(73, 196, 212, 0.4), transparent);
    }

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        margin: 10px 10px 2px;
    }
`,WM=D.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,U1={32:"space",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",58:":",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z"};class YM extends Je.Component{constructor(r){super(r),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?m.jsx(zM,{close:this.close.bind(this),...this.props}):m.jsx(GM,{onClick:this.open.bind(this),children:"⚙"})}}const GM=D(Hg)`
    width: ${V.hud.btn};
    height: ${V.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${V.hud.glyph};
    border-right: none;
    border-top: none;
    ${Yi}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${V.hud.btnSmall};
        height: ${V.hud.btnSmall};
        font-size: ${V.hud.glyphSmall};

    }
`,B1=D.span`
    color: white;
    font-family:arial;
    font-size:12px;
`,KM=D.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    position: relative; // Needed for absolute positioning of ::before

    &::before {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: -1;
        background-image: ${o=>{switch(o.$crits){case 11:return"url(img/systemicons/thruster1-critical12.png);";case 10:return"url(img/systemicons/thruster1-critical1.png);";case 1:return"url(img/systemicons/thruster1-critical2.png);";default:return"url(img/systemicons/thruster1.png);"}}}
        background-size: cover;
        transform: ${o=>{switch(o.$direction){case 4:return"rotate(180deg)";case 1:return"rotate(90deg)";case 2:return"rotate(270deg)";default:return"none"}}};
      }

    
    ${Yi}
`,H1=D.div`
    display: flex;
    position: absolute;
`,V1=D(H1)`
    flex-direction: row;
    left: 60px;
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: 40px;
`,QM=D(V1)`
    left: -100px;
`,W1=D(H1)`
    flex-direction: row;
    top: -120px;
    transform: translate(-50%, 0);
`,qM=D(W1)`
    top: 80px;
`,XM=D.div`
    position: relative;
    transform: rotate(${o=>o.$rotation}deg);

    & ${B1} {
        transform: rotate(${o=>-o.$rotation}deg);
    }
`,JM=D.div`
    position: absolute;
    left: ${o=>o.$left};
    top: ${o=>o.$top};
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: blue;
`,ZM=D(Ex)`
    top: 125px;
    min-width: 180px;
    z-index: 10001;
`,eO=D.div`
    margin-top: 14px;
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
`,Y1=D.div`
    width: 40px;
    height: 40px;
    background-size: cover;
    margin: 5px;
    font-size: 30px;
    
    ${Yi}
`,G1=D(Au)`
    ${Yi}
`;class tO extends Je.Component{constructor(r){super(r)}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}resetThrust(){const r=this.props.ship;window.shipManager.movement.revertAutoThrust(r),window.shipManager.movement.updateAssignThrust(r)}autoAssign(){const r=this.props.ship;window.shipManager.movement.revertAutoThrust(r),window.shipManager.movement.autoAssignThrust(r),window.shipManager.movement.updateAssignThrust(r)}render(){const{ship:r,position:s,rotation:d,totalRequired:g,remainginRequired:b,movement:S}=this.props;return m.jsxs(JM,{onMouseOver:y=>y.preventDefault(),onContextMenu:y=>y.preventDefault(),id:"thrustUIContainer",$left:`${s.x}px`,$top:`${s.y}px`,children:[m.jsxs(XM,{style:{transform:`rotate(${Math.round(Math.abs(d))}deg)`},$rotation:Math.round(Math.abs(d)),children:[m.jsx(V1,{children:Wg(r,1,g,b)}),m.jsx(W1,{children:Wg(r,3,g,b)}),m.jsx(qM,{children:Wg(r,4,g,b)}),m.jsx(QM,{children:Wg(r,2,g,b)})]}),m.jsxs(ZM,{children:[m.jsx(Tx,{children:"Assign thrust"}),iO(g,b,S),nO(r),rO(r,S),m.jsx(G1,{$space:!0,$important:!0,onClick:this.resetThrust.bind(this),children:"RESET THRUST"}),m.jsx(G1,{$important:!0,onClick:this.autoAssign.bind(this),children:"AUTO ASSIGN"}),m.jsxs(eO,{children:[m.jsx(Y1,{onClick:this.ready.bind(this),children:"✔"}),m.jsx(Y1,{onClick:this.cancel.bind(this),children:"🛇"})]})]})]})}}const nO=o=>{const r=shipManager.movement.getRemainingEngineThrust(o);return m.jsxs(Au,{$space:!0,$important:!0,children:["Thrust available: ",r]})},rO=(o,r)=>{if(!shipManager.movement.isTurn(r))return null;const s=shipManager.movement.calculateTurndelay(o,r,r.speed);return m.jsxs(Au,{$important:!0,children:["Current turn delay: ",s]})},iO=(o,r,s)=>{const d=Array("either","front","aft","port","starboard");s.type=="roll"&&(d[0]="any");const g=r.map((b,S)=>b<=0||b===null?null:m.jsxs(Au,{$type:b===0?"good":"bad",children:[b," thrust to ",d[S]," thrusters"]},`assign-thrust-text-${S}`)).filter(b=>b!==null);return g.length===0?m.jsx(Au,{$type:"good",children:"All done!"}):g},Wg=(o,r,s,d)=>{const g=shipManager.systems.getThrusters(o,r);return d.type!=="roll"&&s[r]===null?null:g.map((b,S)=>{const y=()=>{shipManager.movement.assignThrust(o,b),shipManager.movement.updateAssignThrust(o)},E=j=>{j.preventDefault(),shipManager.movement.unAssignThrust(o,b),shipManager.movement.updateAssignThrust(o)};let O=shipManager.criticals.hasCritical(b,"HalfEfficiency")?10:0;shipManager.criticals.hasCritical(b,"FirstThrustIgnored")&&(O+=1);const $=shipManager.movement.getAmountChanneled(o,b),P=shipManager.systems.getOutput(o,b);return m.jsx(KM,{$crits:O,onClick:y,onContextMenu:E,$direction:r,children:m.jsxs(B1,{children:[$,"/",P]})},`thruster-${r}-${S}`)})},aO=()=>m.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[m.jsx("path",{d:"M9 3H3v6"}),m.jsx("path",{d:"M15 3h6v6"}),m.jsx("path",{d:"M9 21H3v-6"}),m.jsx("path",{d:"M15 21h6v-6"})]});class oO extends Je.Component{fullScreen(){var r=window.document,s=r.documentElement,d=s.requestFullscreen||s.mozRequestFullScreen||s.webkitRequestFullScreen||s.msRequestFullscreen,g=r.exitFullscreen||r.mozCancelFullScreen||r.webkitExitFullscreen||r.msExitFullscreen;!r.fullscreenElement&&!r.mozFullScreenElement&&!r.webkitFullscreenElement&&!r.msFullscreenElement?d.call(s):g.call(r)}render(){return m.jsx(lO,{onClick:this.fullScreen.bind(this),title:"Full screen",children:m.jsx(aO,{})})}}const lO=D(Hg)`
    width: ${V.hud.btn};
    height: ${V.hud.btn};
    position: fixed;
    right: calc((${V.hud.btn} * 2) + 20px);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Yi}

    svg {
        width: ${V.hud.icon};
        height: ${V.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${V.hud.btnSmall};
        height: ${V.hud.btnSmall};
        right: calc((${V.hud.btnSmall} * 2) + 20px);

        svg {
            width: ${V.hud.iconSmall};
            height: ${V.hud.iconSmall};
        }
    }
`;class sO extends Je.Component{constructor(r){super(r),this.state={available:K1()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{const r=K1();this.state.available!==r&&this.setState({available:r})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?m.jsx(cO,{onClick:this.surrender,title:"Surrender",children:m.jsx("img",{src:uO(),alt:""})}):null}}const K1=()=>{if(typeof window.gamedata>"u")return!1;const o=window.gamedata;if(o.replay||!o.isPlayerInGame()||o.status==="SURRENDERED"||o.status==="FINISHED")return!1;for(const r in o.slots){const s=o.slots[r];if(s.playerid==o.thisplayer&&s.surrendered!==null&&s.surrendered!==void 0)return!1}return!0},uO=()=>{const o="./img/surrender_icon1.png";return window.AssetManager?window.AssetManager.getSmartImagePath(o):o},Q1="34px",q1="22px",cO=D(Hg)`
    width: ${V.hud.btn};
    height: ${V.hud.btn};
    position: fixed;
    right: calc(${V.hud.btn} + 10px);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Yi}

    img {
        width: ${Q1};
        height: ${Q1};
        display: block;
        transform: translateY(${"-4px"});
    }

    /* Clickable's hover cue is text-shadow + colour, which a raster icon cannot answer -
       FullScreen gets away with it because its SVG strokes in currentColor. Lift the PNG
       instead, the same way the EW strip signals state on its background art. */
    &:hover img {
        filter: brightness(1.4);
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${V.hud.btnSmall};
        height: ${V.hud.btnSmall};
        right: calc(${V.hud.btnSmall} + 10px);

        img {
            width: ${q1};
            height: ${q1};
            transform: translateY(${"-2px"});
        }
    }
`;class dO extends On.Component{constructor(r){super(r),this.state={losToggled:!1,hexToggled:!1,soundToggled:!0,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener("LoSToggled",this.externalToggleLoS),window.addEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.addEventListener("BackgroundToggled",this.externalToggleBackground),window.addEventListener("soundToggled",this.externalToggleSound),this.replayCheck=setInterval(()=>{const r=this.getReplayMode();this.state.replayMode!==r&&this.setState({replayMode:r})},500)}componentWillUnmount(){window.removeEventListener("LoSToggled",this.externalToggleLoS),window.removeEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.removeEventListener("BackgroundToggled",this.externalToggleBackground),window.removeEventListener("soundToggled",this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(r=>({hexToggled:!r.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}showFriendlyEW(r){webglScene.customEvent("ShowFriendlyEW",{up:r})}showEnemyEW(r){webglScene.customEvent("ShowEnemyEW",{up:r})}toggleFriendlyBallisticLines(r){if(r)return;const s=!this.state.fbToggled;this.setState({fbToggled:s}),webglScene.customEvent("ToggleFriendlyBallisticLines",{up:r})}toggleEnemyBallisticLines(r){if(r)return;const s=!this.state.ebToggled;this.setState({ebToggled:s}),webglScene.customEvent("ToggleEnemyBallisticLines",{up:r})}toggleLoS(r){if(r)return;const s=!this.state.losToggled;this.setState({losToggled:s}),webglScene.customEvent("ToggleLoS",{up:r}),window.dispatchEvent(new CustomEvent("LoSToggled"))}toggleHexNumbers(r){if(r)return;const s=!this.state.hexToggled;this.setState({hexToggled:s}),webglScene.customEvent("ToggleHexNumbers",{up:r}),window.dispatchEvent(new CustomEvent("HexNumbersToggled"))}toggleSound(){const r=!this.state.soundToggled;this.setState({soundToggled:r}),webglScene.customEvent("ToggleSound",{enabled:r})}toggleBackground(){const r=document.getElementById("background");if(!r)return;const s=!this.state.bgToggled;let d=this.state.originalBgImage;s?(d||(d=r.style.backgroundImage),r.style.backgroundImage="none",r.style.backgroundColor="black"):(r.style.backgroundImage=d||"",r.style.backgroundColor=""),this.setState({bgToggled:s,originalBgImage:d})}render(){return m.jsxs(fO,{children:[m.jsx(hO,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),m.jsx(pO,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),m.jsx(mO,{$toggled:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),m.jsx(gO,{$toggled:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),m.jsx(vO,{$toggled:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),m.jsx(yO,{$toggled:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),m.jsx(xO,{$toggled:this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?"Enable Background":"Disable Background"}),this.state.replayMode&&m.jsx(bO,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?"Sound On":"Sound Off"})]})}}const fO=D.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,ms=D(Hg)`
    display: flex;
    width: ${V.hud.btn};
    height: ${V.hud.btn};
    align-items: center;
    justify-content: center;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${Yi}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${V.hud.btnSmall};
        height: ${V.hud.btnSmall};
    }
`,pO=D(ms)`
    background-image: url("./img/EEW.png");
`,hO=D(ms)`
    background-image: url("./img/FEW.png");
`,gO=D(ms)`
    background-image: url("./img/ballisticTarget2.png");
    box-shadow: ${o=>o.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${o=>o.$toggled?"#1b533d":V.colors.windowBg};
    border: 1px solid ${o=>o.$toggled?"limegreen":V.colors.line};
    border-right: none;
`,mO=D(ms)`
    background-image: url("./img/ballisticLaunch2.png");
    box-shadow: ${o=>o.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${o=>o.$toggled?"#1b533d":V.colors.windowBg};
    border: 1px solid ${o=>o.$toggled?"limegreen":V.colors.line};
    border-right: none;
`,vO=D(ms)`
    background-image: url("./img/los1.png");
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":V.colors.line};
    border-right: none;
`,yO=D(ms)`
    background-image: url("./img/hexNumber.png");
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":V.colors.line};
    border-right: none;
`,xO=D(ms)`
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":V.colors.line};
    border-right: none;
    position: relative;


    &::after {
        content: '';
        position: absolute;
        width: 50%;
        height: 50%;
        background: linear-gradient(135deg, black 40%, #49915fff 60%);
        border: 2px solid #fdfdfdb4;
        border-radius: 4px;
        box-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    margin-left: 4px;           
    }
`,bO=D(ms)`
    background-image: ${o=>o.$toggled?'url("./img/soundOn.png")':'url("./img/soundOff.png")'};
    border: 1px solid ${V.colors.line};
    border-right: none;
`,wO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,SO=D.div`
    padding: 3px;
    background-color: #571616;
    border: 1px solid #b43131;
    border-bottom: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,CO=D.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,EO=D.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${o=>o.img});
    background-size: cover;
    align-items: center;
    opacity: 1 !important;    
    justify-content: center;
    ${Yi}
    border: 1px solid ${o=>o.selected?"#ef4444":"transparent"};
    position: relative;
    box-shadow: ${o=>o.selected?"0 0 5px #b43131":"none"};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;class TO extends Je.Component{selectMode(r,s){r.stopPropagation(),r.preventDefault();const{ship:d,system:g}=this.props;weaponManager.onSetModeClicked(d,g,s)}selectAllMode(r,s){r.stopPropagation(),r.preventDefault();const{ship:d,system:g}=this.props;weaponManager.onSetModeAllClicked(d,g,s)}render(){const{ship:r,system:s}=this.props,d=this.props.showModes!==!1,g=parseInt(s.firingMode);let b="";s.iconPath?b=`./img/systemicons/${s.iconPath}`:b=`./img/systemicons/${s.name}.png`;const S=[];for(const y in s.firingModes)if(s.firingModes.hasOwnProperty(y)){const E=parseInt(y),O=s.firingModes[y],$=E===g;let P=s.modeLetters||1;s.modeLettersArray&&s.modeLettersArray[E]&&(P=s.modeLettersArray[E]),S.push(m.jsx(EO,{img:b,selected:$,onClick:j=>this.selectMode(j,E),onContextMenu:j=>this.selectAllMode(j,E),title:`Set mode: ${O} ${$?"(Current)":""} (Right click to set all)`,children:O.substring(0,P)},E))}return m.jsxs(wO,{children:[d&&m.jsx(SO,{children:"Select Firing Mode"}),m.jsxs(CO,{children:[d&&S,this.props.children]})]})}}const kO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,RO=D.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,DO=D.div`
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    max-height: 200px;
    overflow-y: auto;
    /* Never show a horizontal scrollbar — the absolutely-positioned dragged row
       can momentarily be a hair wider than the content box. */
    overflow-x: hidden;
    display: block;
    /* Positioning context for the absolutely-positioned dragged row. */
    position: relative;

    /* Always reserve the scrollbar gutter so the box width never changes when the
       scrollbar appears/disappears — e.g. during a drag the height is pinned and
       the content momentarily fits, which would otherwise hide the scrollbar and
       widen the content (Chromium 94+, which the FV client runs on). */
    scrollbar-gutter: stable;

    /* While a row is being dragged, pin the box to its pre-drag height so the
       source-collapse / target-gap margin animations can't resize it. */
    ${o=>o.$lockHeight?`height: ${o.$lockHeight}px; max-height: ${o.$lockHeight}px;`:""}

    &::-webkit-scrollbar {
        width: 6px;
    }
    &::-webkit-scrollbar-track {
        background: #0d1620; 
    }
    &::-webkit-scrollbar-thumb {
        background: #2b3e51; 
    }
    &::-webkit-scrollbar-thumb:hover {
        background: #5a7ea8; 
    }

    @media (max-width: 768px) {
        text-align: center;         
    }    

`,X1=D.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 5px;
    margin-right: 3px;
    border-bottom: 1px solid #2b3e51;
    font-size: 12px;
    color: #e6e6e6;

    /* Drag-to-reorder: rows are grabbable; touch-action:none stops the touch
       gesture scrolling the list instead of dragging the row. In view-only mode
       (outside Initial Orders) dragging is disabled, so the row is a plain default
       cursor and touch gestures may scroll the list normally. */
    cursor: ${o=>o.$readOnly?"default":"grab"};
    touch-action: ${o=>o.$readOnly?"auto":"none"};
    user-select: none;
    position: relative;

    /* A live gap opens where the dragged row will land, so the drop target is
       obvious. The gap height matches the dragged row (gapSize, inline). No
       margin transition: an animating gap makes the rows' measured centres a
       moving target for the drop-slot scan, which feels sticky/jittery while
       dragging across several rows. Snapping the gap open is crisper. */

    /* The row currently being dragged. It is pulled OUT OF FLOW (position:
       absolute, positioned imperatively — see onDragMove) so the list closes up
       behind it automatically and, crucially, gaps opening/closing elsewhere
       never shift its baseline. It floats under the pointer via translateY from
       that fixed origin. z-index lifts it above the rest. */
    ${o=>o.$dragging&&`
        position: absolute;
        opacity: 0.95;
        z-index: 3;
        cursor: grabbing;
        background-color: rgba(43, 62, 81, 0.92);
        pointer-events: none;
        transition: none;
    `}

    /* MIDDLE drop target: a bold glowing yellow marker line sits at the row's top
       edge WITHOUT opening a gap — so no rows relayout as the pointer crosses
       slots, which is what made the full-gap approach judder. Only the very top
       and very bottom of the list still open a real gap (below). */
    ${o=>o.$lineBefore&&`
        &::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -2px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    /* TOP-of-list drop: open a real gap above the first row (only one row moves,
       so it stays smooth) with the same yellow marker line inside it. */
    ${o=>o.$gapBefore&&`
        margin-top: ${o.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -${o.$gapSize}px;
            height: ${o.$gapSize}px;
            box-shadow: inset 0 0 0 2px #ffcc33, 0 0 6px 1px rgba(255, 204, 51, 0.5);
            background-color: rgba(255, 204, 51, 0.12);
            pointer-events: none;
        }
    `}

    /* BOTTOM-of-list drop: a marker line at the last row's BOTTOM edge. It is
       deliberately NOT a real gap: a bottom margin-bottom grows the container's
       scrollable content, and closing it (dragging back up one) clamps scrollTop
       and shifts the pointer's content position up by a row — which made the
       bottom row jump TWO slots (badly on mobile, where the list is always
       scrolled and rows are tall). A line changes no layout, so no clamp. Nothing
       sits below the last row, so a line here is unambiguous (unlike the TOP). */
    ${o=>o.$lineAtEnd&&`
        &::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    &:last-child {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        flex-direction: column;
        align-items: stretch;
        margin-right: 0px;
    }
`,MO=D.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,kx=D.span`
    font-weight: bold;
`,J1=D.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-right: 10px;    
    margin-left: 1px;

    @media (max-width: 768px) {
        text-align: center;
        margin-right: 0px;    
        margin-left: 0px;                  
    }

`,OO=D(kx)`
    color: #ffb833;
    font-weight: normal;    
`,$O=D(kx)`
    color: #ff3333;
    font-weight: normal;    
`,AO=D.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,Yg=D.div`
    width: 18px;
    height: 18px;
    background-image: url(${o=>o.img});
    background-size: cover;
    cursor: pointer;
    opacity: 0.9;
    margin-left: 3px;
    &:hover {
        opacity: 1;
    }
    
     ${Yi}
`,jO=D.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,_O=D.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;       
    display: inline-block;
    
    &:hover {
        background-color: #496791;
        color: #ffffff;
    }
`,Z1=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,LO=D(X1)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,Rx=D.span`
    color: #00b8e6;
    font-weight: normal;
    margin-right: 4px;
`,zO=D.input`
    width: 20px;
    height: 16px;
    background: rgba(0,0,0,0.5);
    border: 1px solid #496791;
    color: #e6e6e6;
    text-align: center;
    font-size: 11px; 
    margin: 0 2px;
    
    // Hide spinner
    &::-webkit-inner-spin-button, 
    &::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    -moz-appearance: textfield;
`,NO=D.span`
    min-width: 20px;
    height: 16px;
    color: #e6e6e6;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
    margin: 0 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
`;class PO extends Je.Component{constructor(r){super(r),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getEffectiveCriticalRepairCost(r,s){return s.name==="cnC"?4:r.repairCost}getDockedUnits(){const{ship:r,system:s}=this.props;if(!s.servicesDockedUnits)return[];const d=[],g=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const b of g)if(!(!b.isDockingBay||!Array.isArray(b.shipsDocked)))for(const S of b.shipsDocked){const y=gamedata.getShip(S.shipId);y&&d.indexOf(y)===-1&&d.push(y)}return d}getDockedRepairables(){const{system:r}=this.props,s=[];for(const d of this.getDockedUnits()){const g=Array.isArray(d.systems)?d.systems:Object.values(d.systems),b="d"+d.id+":";for(const S of g){const y=S.name==="structure"||S.name==="cnC"||S.name==="SelfRepair";if(!shipManager.systems.isDestroyed(d,S)&&S.repairPriority>=1&&S.criticals){const j=Array.isArray(S.criticals)?S.criticals:Object.values(S.criticals);for(const H of j){if(H.repairPriority===0||H.turn>=gamedata.turn||H.oneturn||H.turnend>0)continue;const L=b+S.id+"-"+H.id;let Q=H.repairPriority||0,xe=!1;r.priorityChanges&&L in r.priorityChanges&&r.priorityChanges[L]>=0?(Q=r.priorityChanges[L],xe=!0):Q<10&&(Q+=S.repairPriority),!(Q<1)&&s.push({type:"critical",sys:S,crit:H,ownerShip:d,shipId:d.id,docked:!0,priority:Q,overridden:xe,cost:this.getEffectiveCriticalRepairCost(H,S),id:S.id,subId:H.id,keyId:L})}}if(!y||S.repairPriority===0)continue;const E=shipManager.systems.getTotalDamage(S);if(E<=0)continue;if(S.name==="structure"){if(shipManager.systems.isDestroyed(d,S))continue}else{const j=S.structureHomeLocation!==void 0&&S.structureHomeLocation!==null?S.structureHomeLocation:S.location;if(j!=0){const H=shipManager.systems.getStructureSystem(d,j);if(H&&shipManager.systems.isDestroyed(d,H))continue}}const O=b+S.id;let $=S.repairPriority,P=!1;r.priorityChanges&&O in r.priorityChanges&&r.priorityChanges[O]>=0&&($=r.priorityChanges[O],P=!0),!($<1)&&(!P&&shipManager.systems.isDestroyed(d,S)&&$<=10&&($+=10),s.push({type:"system",sys:S,ownerShip:d,shipId:d.id,docked:!0,priority:$,overridden:P,damage:E,maxHealth:S.maxhealth,id:S.id,subId:0,keyId:O}))}}return s}getRepairableSystems(){const{ship:r,system:s}=this.props,d=[],g=[],b=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const E of b){if(E.name==="SelfRepair"||E.repairPriority===0||E.privateRepairOnly&&!s.repairRestrictedTo||s.repairRestrictedTo&&!s.repairRestrictedTo.includes(E.id)||E.name=="structure"&&shipManager.systems.isDestroyed(E.ship,E))continue;const O=E.structureHomeLocation!==void 0&&E.structureHomeLocation!==null?E.structureHomeLocation:E.location;if(E.name!="structure"&&O!=0){var S=shipManager.systems.getStructureSystem(E.ship,O);if(S&&shipManager.systems.isDestroyed(E.ship,S))continue}let $=E.repairPriority,P=!1;if(s.priorityChanges&&E.id in s.priorityChanges&&s.priorityChanges[E.id]>=0&&($=s.priorityChanges[E.id],P=!0),!P&&shipManager.systems.isDestroyed(r,E)&&$<=10&&($+=10),!shipManager.systems.isDestroyed(r,E)&&E.criticals){const H=Array.isArray(E.criticals)?E.criticals:Object.values(E.criticals);for(const L of H){if(L.repairPriority===0||L.turn>=gamedata.turn||L.oneturn||L.turnend>0)continue;let Q=L.repairPriority||0;const xe=E.id+"-"+L.id;let ze=!1;s.priorityChanges&&xe in s.priorityChanges&&s.priorityChanges[xe]>=0?(Q=s.priorityChanges[xe],ze=!0):Q<10&&(Q+=E.repairPriority),g.push({type:"critical",sys:E,crit:L,ownerShip:r,shipId:0,docked:!1,priority:Q,overridden:ze,cost:this.getEffectiveCriticalRepairCost(L,E),id:E.id,subId:L.id,keyId:xe})}}const j=shipManager.systems.getTotalDamage(E);j>0&&d.push({type:"system",sys:E,ownerShip:r,shipId:0,docked:!1,priority:$,overridden:P,damage:j,maxHealth:E.maxhealth,id:E.id,subId:0,keyId:E.id})}const y=[...g,...d,...this.getDockedRepairables()];return y.sort((E,O)=>{if(E.priority!==O.priority)return O.priority-E.priority;const $=!!E.overridden,P=!!O.overridden;if($!==P)return $?-1:1;if(this.lastOrder&&this.lastOrder.length>0){const L=this.lastOrder.indexOf(E.keyId),Q=this.lastOrder.indexOf(O.keyId);if(L!==-1&&Q!==-1)return L-Q}const j=E.shipId||0,H=O.shipId||0;return j!==H?j-H:E.id!==O.id?E.id-O.id:E.subId-O.subId}),this.lastOrder=y.map(E=>E.keyId),y}handleInputChange(r,s,d){const g=r.target.value;if(g===""){this.setState(S=>({priorityInputs:{...S.priorityInputs,[s]:""}}));return}const b=parseInt(g,10);isNaN(b)||(this.setState(S=>({priorityInputs:{...S.priorityInputs,[s]:b}})),this.setPriority(s,b))}handleWheel(r,s,d){r.preventDefault();const g=r.deltaY<0?1:-1,b=d+g;b<1||this.setPriority(s,b)}componentDidUpdate(r){if(this.dragRef&&this.dragRef.started&&this.listRef){const S=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');S&&this.positionDraggedEl(S,this.dragRef,this.dragRef.lastClientY)}const s=this.getRepairableSystems(),d=this.state.priorityInputs,g={};let b=!1;s.forEach(S=>{const y=S.keyId,E=S.priority;d[y]!==void 0&&d[y]!==E&&document.activeElement!==document.getElementById(`prio-input-${y}`)&&(g[y]=E,b=!0)}),b&&this.setState(S=>({priorityInputs:{...S.priorityInputs,...g}}))}handleTop(r,s){r.stopPropagation();const d=this.getRepairableSystems();if(d.length===0)return;const g=d[0].priority,b=d.find(y=>y.keyId===s);if(!b||b.priority===g)return;let S=g+1;this.setPriority(s,S)}handleUp(r,s,d){r.stopPropagation();let g=d+1;g!==d&&this.setPriority(s,g)}handleDown(r,s,d){r.stopPropagation(),!(d<=1)&&this.setPriority(s,d-1)}handleReset(r,s){r.stopPropagation(),this.setPriority(s,-1)}setPriority(r,s){const{ship:d,system:g}=this.props;g.setOverride(r,s),webglScene.customEvent("SystemDataChanged",{ship:d,system:g})}onRowPointerDown(r,s,d,g){if(this.props.readOnly||r.button!=null&&r.button!==0||r.target&&r.target.closest&&r.target.closest("input, .sr-action-button"))return;const b=r.currentTarget,S=b?b.offsetHeight:24,y=this.listRef?this.listRef.offsetHeight:0,E=b?b.offsetWidth:0,O=b?r.clientY-b.getBoundingClientRect().top:0;this.dragRef={keyId:s,pointerId:r.pointerId,startY:r.clientY,startIdx:d,order:g,gapSize:S,lockHeight:y,anchorWidth:E,grabOffsetInRow:O,lastClientY:r.clientY,started:!1},r.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(r){const s=this.dragRef;if(!(!s||r.pointerId!==s.pointerId)){if(!s.started){if(Math.abs(r.clientY-s.startY)<4)return;s.started=!0}r.preventDefault(),s.lastClientY=r.clientY,this.updateDragForPointer(r.clientY),this.updateAutoScroll(r.clientY)}}updateDragForPointer(r){const s=this.dragRef;if(!s||!this.listRef)return;const d=this.listRef.querySelectorAll("[data-keyid]"),g=this.listRef.getBoundingClientRect(),b=r-g.top+this.listRef.scrollTop,y=this.state.drag&&this.state.drag.dropIdx===0?s.gapSize:0;let E=0,O=null;for(let P=0;P<d.length;P++){if(d[P].getAttribute("data-keyid")===String(s.keyId)){O=d[P];continue}const j=d[P].offsetTop-y+d[P].offsetHeight/2;if(b<j)break;E++}O&&this.positionDraggedEl(O,s,r);const $=this.state.drag;(!$||$.keyId!==s.keyId||$.dropIdx!==E)&&this.setState({drag:{keyId:s.keyId,startIdx:s.startIdx,dropIdx:E,gapSize:s.gapSize,lockHeight:s.lockHeight}})}positionDraggedEl(r,s,d){const g=this.listRef.getBoundingClientRect();let b=d-g.top+this.listRef.scrollTop-s.grabOffsetInRow;const S=Math.max(0,this.listRef.scrollHeight-s.gapSize);b<0?b=0:b>S&&(b=S),r.style.top=b+"px",r.style.left="0px",r.style.width=s.anchorWidth+"px",r.style.transform="none"}updateAutoScroll(r){const s=this.listRef;if(!s){this.stopAutoScroll();return}const d=30,g=s.getBoundingClientRect(),b=s.scrollTop>0,S=s.scrollTop<s.scrollHeight-s.clientHeight-1,y=r-g.top,E=g.bottom-r;b&&y<d?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,y)/d),this.ensureAutoScrollRunning()):S&&E<d?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,E)/d),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const r=this.listRef,s=this.dragRef;if(!r||!s||this.autoScrollDir===0)return;const d=r.scrollTop;if(r.scrollTop=d+this.autoScrollDir*(this.autoScrollSpeed||6),r.scrollTop===d){this.stopAutoScroll();return}this.updateDragForPointer(s.lastClientY),this.updateAutoScroll(s.lastClientY)}onDragEnd(r){const s=this.dragRef;if(!s||r&&r.pointerId!=null&&r.pointerId!==s.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const g=this.listRef.querySelector('[data-keyid="'+s.keyId+'"]');g&&(g.style.transform="",g.style.top="",g.style.left="",g.style.width="")}this.dragRef=null;const d=this.state.drag;this.setState({drag:null}),!(!s.started||!d)&&d.dropIdx!==s.startIdx&&this.applyDropReorder(s.order,s.keyId,d.dropIdx)}applyDropReorder(r,s,d){const{ship:g,system:b}=this.props,S=r.findIndex(j=>j.keyId===s);if(S===-1)return;const y=r.slice(),[E]=y.splice(S,1);y.splice(d,0,E);const O=y[d+1],$=y[d-1];let P;O?P=O.priority+1:$?P=Math.max(1,$.priority-1):P=1,b.setOverride(s,P);for(let j=d-1;j>=0&&!(y[j].priority>P);j--)P+=1,b.setOverride(y[j].keyId,P);webglScene.customEvent("SystemDataChanged",{ship:g,system:b})}handlePropagate(r){r.stopPropagation();const{ship:s,system:d}=this.props;for(const g of s.systems)if(g.name==="SelfRepair"&&g.id!==d.id){if(g.priorityChanges)for(const b in g.priorityChanges)(!d.priorityChanges||!(b in d.priorityChanges))&&g.setOverride(b,-1);if(d.priorityChanges)for(const b in d.priorityChanges){const S=d.priorityChanges[b];S>=0&&g.setOverride(b,S)}}webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{ship:r,readOnly:s}=this.props,d=this.getRepairableSystems();let g=0;const b=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const S of b)S.name==="SelfRepair"&&g++;return m.jsxs(kO,{children:[m.jsx(RO,{children:s?"Repair Queue (view only)":"Manage Repair Queue"}),m.jsxs(DO,{ref:S=>{this.listRef=S},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[d.length===0&&m.jsx(LO,{children:"No damaged systems"}),d.map((S,y)=>{const E=this.state.drag,O=E&&E.keyId===S.keyId;let $=!1,P=!1,j=!1;if(E&&!O){const L=d.length-1,Q=y>E.startIdx?y-1:y;E.dropIdx===0&&Q===0?$=!0:E.dropIdx===L&&Q===L-1?P=!0:E.dropIdx===Q&&(j=!0)}const H=S.ownerShip||r;return m.jsxs(X1,{"data-keyid":S.keyId,$dragging:O,$gapBefore:$,$lineAtEnd:P,$lineBefore:j,$gapSize:E?E.gapSize:0,$readOnly:s,onPointerDown:L=>this.onRowPointerDown(L,S.keyId,y,d),children:[m.jsx(MO,{children:S.type==="critical"?m.jsxs(m.Fragment,{children:[m.jsxs(OO,{children:[S.docked&&m.jsxs(Rx,{children:[H.name,":"]}),S.sys.displayName," (",S.crit.description||S.crit.phpclass,")"]}),m.jsxs(J1,{children:["Cost: ",S.cost," ",m.jsx(Z1,{})," Id: ",S.sys.id]})]}):m.jsxs(m.Fragment,{children:[shipManager.systems.isDestroyed(H,S.sys)?m.jsxs($O,{children:[S.docked&&m.jsxs(Rx,{children:[H.name,":"]}),S.sys.displayName]}):m.jsxs(kx,{children:[S.docked&&m.jsxs(Rx,{children:[H.name,":"]}),S.sys.displayName]}),m.jsxs(J1,{children:["HP: ",shipManager.systems.getRemainingHealth(S.sys)," / ",S.sys.maxhealth," ",m.jsx(Z1,{})," Id: ",S.sys.id]})]})}),m.jsx(AO,{children:s?m.jsx(NO,{title:"Priority (view only)",children:S.priority}):m.jsxs(m.Fragment,{children:[m.jsx(Yg,{className:"sr-action-button",title:"Reset Default",onClick:L=>this.handleReset(L,S.keyId),img:"./img/iconSRCancel.png"}),m.jsx(Yg,{className:"sr-action-button",title:"Decrease Priority",onClick:L=>this.handleDown(L,S.keyId,S.priority),img:"./img/systemicons/AAclasses/iconMinus.png"}),m.jsx(zO,{id:`prio-input-${S.keyId}`,type:"number",value:this.state.priorityInputs[S.keyId]!==void 0?this.state.priorityInputs[S.keyId]:S.priority,onChange:L=>this.handleInputChange(L,S.keyId,S.priority),onClick:L=>L.stopPropagation(),onWheel:L=>this.handleWheel(L,S.keyId,S.priority)}),m.jsx(Yg,{className:"sr-action-button",title:"Increase Priority",onClick:L=>this.handleUp(L,S.keyId,S.priority),img:"./img/systemicons/AAclasses/iconPlus.png"}),m.jsx(Yg,{className:"sr-action-button",title:"Move to Top",onClick:L=>this.handleTop(L,S.keyId),img:"./img/iconSRHigh.png"})]})})]},S.keyId)})]}),!s&&g>1&&m.jsx(jO,{children:m.jsx(_O,{onClick:S=>this.handlePropagate(S),children:"Set all Self Repair systems"})})]})}}const FO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
`,IO=D.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,UO=D.div`
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    display: block;
    position: relative;
    scrollbar-gutter: stable;
    ${o=>o.$lockHeight?`height: ${o.$lockHeight}px; max-height: ${o.$lockHeight}px;`:""}

    &::-webkit-scrollbar { width: 6px; }
    &::-webkit-scrollbar-track { background: #0d1620; }
    &::-webkit-scrollbar-thumb { background: #2b3e51; }
    &::-webkit-scrollbar-thumb:hover { background: #5a7ea8; }
`,eC=D.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 5px;
    margin-right: 3px;
    border-bottom: 1px solid #2b3e51;
    font-size: 12px;
    color: #e6e6e6;
    cursor: ${o=>o.$readOnly?"default":"grab"};
    touch-action: ${o=>o.$readOnly?"auto":"none"};
    user-select: none;
    position: relative;

    ${o=>o.$dragging&&`
        position: absolute;
        opacity: 0.95;
        z-index: 3;
        cursor: grabbing;
        background-color: rgba(43, 62, 81, 0.92);
        pointer-events: none;
        transition: none;
    `}

    ${o=>o.$lineBefore&&`
        &::before {
            content: "";
            position: absolute;
            left: 0; right: 0; top: -2px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    ${o=>o.$gapBefore&&`
        margin-top: ${o.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0; right: 0;
            top: -${o.$gapSize}px;
            height: ${o.$gapSize}px;
            box-shadow: inset 0 0 0 2px #ffcc33, 0 0 6px 1px rgba(255, 204, 51, 0.5);
            background-color: rgba(255, 204, 51, 0.12);
            pointer-events: none;
        }
    `}

    ${o=>o.$lineAtEnd&&`
        &::after {
            content: "";
            position: absolute;
            left: 0; right: 0; bottom: -1px;
            height: 3px;
            background-color: #c9a028;
            border-radius: 2px;
            z-index: 4;
            pointer-events: none;
        }
    `}

    &:last-child { border-bottom: none; }
`,BO=D(eC)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,HO=D.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,tC=D.span`
    font-weight: bold;
`,VO=D(tC)`
    color: #ff3333;
    font-weight: normal;
`,WO=D.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,YO=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,GO=D.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,KO=D.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,QO=D.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;class qO extends Je.Component{constructor(r){super(r),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getOrderedBlocks(){const{ship:r,system:s}=this.props,d=(s.structureBlocks||[]).map(y=>{const E=(Array.isArray(r.systems)?r.systems:Object.values(r.systems)).find(j=>j.id===y.id),O=E?shipManager.systems.getRemainingHealth(E):y.maxhealth,$=E?E.maxhealth:y.maxhealth||0,P=E?shipManager.systems.isDestroyed(r,E):!1;return{id:y.id,displayName:y.displayName,hp:O,maxhealth:$,destroyed:P}}),g=s.repairOrder||[];if(g.length===0)return d.slice().sort((y,E)=>{const O=E.destroyed?1:0,$=y.destroyed?1:0;return O!==$?O-$:E.maxhealth-E.hp-(y.maxhealth-y.hp)});const b=[],S=new Set;for(const y of g){const E=d.find(O=>O.id===y);E&&(b.push(E),S.add(y))}for(const y of d)S.has(y.id)||b.push(y);return b}onRowPointerDown(r,s,d,g){if(this.props.readOnly||r.button!=null&&r.button!==0||r.target&&r.target.closest&&r.target.closest(".ssr-action-button"))return;const b=r.currentTarget,S=b?b.offsetHeight:24,y=this.listRef?this.listRef.offsetHeight:0,E=b?b.offsetWidth:0,O=b?r.clientY-b.getBoundingClientRect().top:0;this.dragRef={keyId:s,pointerId:r.pointerId,startY:r.clientY,startIdx:d,order:g,gapSize:S,lockHeight:y,anchorWidth:E,grabOffsetInRow:O,lastClientY:r.clientY,started:!1},r.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(r){const s=this.dragRef;if(!(!s||r.pointerId!==s.pointerId)){if(!s.started){if(Math.abs(r.clientY-s.startY)<4)return;s.started=!0}r.preventDefault(),s.lastClientY=r.clientY,this.updateDragForPointer(r.clientY),this.updateAutoScroll(r.clientY)}}updateDragForPointer(r){const s=this.dragRef;if(!s||!this.listRef)return;const d=this.listRef.querySelectorAll("[data-keyid]"),g=this.listRef.getBoundingClientRect(),b=r-g.top+this.listRef.scrollTop,y=this.state.drag&&this.state.drag.dropIdx===0?s.gapSize:0;let E=0,O=null;for(let P=0;P<d.length;P++){if(d[P].getAttribute("data-keyid")===String(s.keyId)){O=d[P];continue}const j=d[P].offsetTop-y+d[P].offsetHeight/2;if(b<j)break;E++}O&&this.positionDraggedEl(O,s,r);const $=this.state.drag;(!$||$.keyId!==s.keyId||$.dropIdx!==E)&&this.setState({drag:{keyId:s.keyId,startIdx:s.startIdx,dropIdx:E,gapSize:s.gapSize,lockHeight:s.lockHeight}})}positionDraggedEl(r,s,d){const g=this.listRef.getBoundingClientRect();let b=d-g.top+this.listRef.scrollTop-s.grabOffsetInRow;const S=Math.max(0,this.listRef.scrollHeight-s.gapSize);b<0?b=0:b>S&&(b=S),r.style.top=b+"px",r.style.left="0px",r.style.width=s.anchorWidth+"px",r.style.transform="none"}updateAutoScroll(r){const s=this.listRef;if(!s){this.stopAutoScroll();return}const d=30,g=s.getBoundingClientRect(),b=s.scrollTop>0,S=s.scrollTop<s.scrollHeight-s.clientHeight-1,y=r-g.top,E=g.bottom-r;b&&y<d?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,y)/d),this.ensureAutoScrollRunning()):S&&E<d?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,E)/d),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const r=this.listRef,s=this.dragRef;if(!r||!s||this.autoScrollDir===0)return;const d=r.scrollTop;if(r.scrollTop=d+this.autoScrollDir*(this.autoScrollSpeed||6),r.scrollTop===d){this.stopAutoScroll();return}this.updateDragForPointer(s.lastClientY),this.updateAutoScroll(s.lastClientY)}onDragEnd(r){const s=this.dragRef;if(!s||r&&r.pointerId!=null&&r.pointerId!==s.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const g=this.listRef.querySelector('[data-keyid="'+s.keyId+'"]');g&&(g.style.transform="",g.style.top="",g.style.left="",g.style.width="")}this.dragRef=null;const d=this.state.drag;this.setState({drag:null}),!(!s.started||!d)&&d.dropIdx!==s.startIdx&&this.applyDropReorder(s.order,s.keyId,d.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){const r=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');r&&this.positionDraggedEl(r,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(r,s,d){const{ship:g,system:b}=this.props,S=r.slice(),y=S.findIndex($=>$.id===s);if(y===-1)return;const[E]=S.splice(y,1);S.splice(d,0,E);const O=S.map($=>$.id);b.setRepairOrder(O),webglScene.customEvent("SystemDataChanged",{ship:g,system:b})}handleReset(r){r.stopPropagation();const{ship:s,system:d}=this.props;d.setRepairOrder([]),webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{ship:r,readOnly:s}=this.props,d=this.getOrderedBlocks(),g=(this.props.system.repairOrder||[]).length>0;return m.jsxs(FO,{children:[m.jsx(IO,{children:s?"Structure Repair Order (view only)":"Manage Structure Repair"}),!s&&m.jsx(QO,{children:"Drag rows to set repair priority — top = first repaired"}),m.jsxs(UO,{ref:b=>{this.listRef=b},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[d.length===0&&m.jsx(BO,{children:"No structure blocks found"}),d.map((b,S)=>{const y=this.state.drag,E=y&&y.keyId===b.id;let O=!1,$=!1,P=!1;if(y&&!E){const H=d.length-1,L=S>y.startIdx?S-1:S;y.dropIdx===0&&L===0?O=!0:y.dropIdx===H&&L===H-1?$=!0:y.dropIdx===L&&(P=!0)}const j=b.maxhealth-b.hp;return m.jsx(eC,{"data-keyid":b.id,$dragging:E,$gapBefore:O,$lineAtEnd:$,$lineBefore:P,$gapSize:y?y.gapSize:0,$readOnly:s,onPointerDown:H=>this.onRowPointerDown(H,b.id,S,d),children:m.jsxs(HO,{children:[b.destroyed?m.jsx(VO,{children:b.displayName}):m.jsx(tC,{children:b.displayName}),m.jsxs(WO,{children:["HP: ",b.hp," / ",b.maxhealth,j>0&&m.jsxs(m.Fragment,{children:[m.jsx(YO,{}),"Dmg: ",j,b.destroyed?" — DESTROYED":""]})]})]})},b.id)})]}),!s&&m.jsx(GO,{children:m.jsx(KO,{className:"ssr-action-button",onClick:b=>this.handleReset(b),title:"Clear custom order and return to default (destroyed first, then most damaged)",children:g?"Reset to Default Order":"Using Default Order"})})]})}}const XO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,JO=D.div`
    padding: 3px;
    background-color: #4d4d4d;
    border: 1px solid #4d4d4d;
    border-bottom: 1px solid #808080;    
    color: #ffffff;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;    
    font-weight: bold;
`,ZO=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,nC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,e$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,t$=D.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,n$=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,r$=D.div`
    width: 20px;
    text-align: center;
`,Dx=D.div`
    width: 16px;
    height: 16px;
    background: #666666;
    border: 1px solid #808080;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    padding: 0;
    opacity: 0.9;

    &:hover {
        background: #3a536e;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #2b3e51; color: #f2f2f2; }
    `}
`,i$=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class a$ extends Je.Component{constructor(r){super(r),this.listRef=On.createRef()}handleIncrease(r){const{system:s}=this.props;s.setCurrDmgType(r),s.canIncrease()&&(s.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:s}=this.props;s.setCurrDmgType(r),s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating AA setting for:",r),d.setCurrDmgType(r);const y=d.allocatedAA[r];var E=[];for(var O in g.ships){var $=g.ships[O];if($.userid==s.userid&&!b.isDestroyed($))if($.flight)for(var P=0;P<$.systems.length;P++){var j=$.systems[P];if(j)for(var H=0;H<j.systems.length;H++){var L=j.systems[H];if(L&&L.displayName=="Adaptive Armor Controller"){E.push(L);break}}}else for(var H=0;H<$.systems.length;H++){var L=$.systems[H];if(L.displayName=="Adaptive Armor Controller"){E.push(L);break}}}console.log("Found AA controllers:",E.length);for(var Q=0;Q<E.length;Q++){var L=E[Q];L.setCurrDmgType(r);let ze=0;for(;L.getCurrAllocated()<y&&L.canIncrease()&&ze<100;)L.doIncrease(),ze++;ze>=100&&console.warn("AA Propagation safety break for",L)}S.customEvent("SystemDataChanged",{ship:s,system:d})}getRelevantArmorTypes(){const{ship:r,system:s}=this.props;return s.getRelevantArmorTypes(r)}render(){const{ship:r,system:s}=this.props;if(!s||!s.availableAA)return null;const d=this.getRelevantArmorTypes(),g=s.allocatedAA,b=E=>{const O=s.AAtotal_used,$=s.AAtotal,P=g[E]||0,j=s.AApertype,H=s.availableAA[E],L=s.AApreallocated,Q=s.AApreallocated_used;return!(O>=$||P>=j||L<=Q&&H<=P)},S=E=>s.currchangedAA[E]>0,y=E=>g[E]>0;return m.jsxs(XO,{children:[m.jsx(JO,{children:"Manage Adaptive Armor"}),m.jsxs(ZO,{ref:this.listRef,children:[d.map(E=>m.jsxs(nC,{children:[m.jsx(e$,{src:`./img/systemicons/AAclasses/${E}.png`,alt:E}),m.jsx(t$,{children:E}),m.jsxs(n$,{children:[m.jsx(Dx,{onClick:()=>this.handleDecrease(E),disabled:!S(E),children:"-"}),m.jsx(r$,{children:g[E]}),m.jsx(Dx,{onClick:()=>this.handleIncrease(E),disabled:!b(E),children:"+"}),m.jsx(Dx,{title:"Propagate to all units",onClick:()=>this.handlePropagate(E),disabled:!y(E),style:{marginLeft:"5px"},children:m.jsx("img",{src:"./img/systemicons/AAclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},E)),d.length===0&&m.jsx(nC,{children:"No armor types available"})]}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",s.AAtotal_used," / ",s.AAtotal," ",m.jsx(i$,{})," Max Per Type: ",s.AApertype]})]})}}const o$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,l$=D.div`
    padding: 3px;
    background-color: #5d3564;
    border: 1px solid #5d3564;
    border-bottom: 1px solid #5d3564;    
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,s$=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,rC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,u$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,c$=D.div`
    flex: 1;
    font-weight: normal; 
`,d$=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,f$=D.div`
    width: 20px;
    text-align: center;
`,Mx=D.div`
    width: 16px;
    height: 16px;
    background: #5d3564;
    border: 1px solid #7c4686;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    padding: 0;
    opacity: 0.9;

    &:hover {
        background: #5e3666;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`,p$=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class h$ extends Je.Component{constructor(r){super(r),this.listRef=On.createRef()}handleIncrease(r){const{system:s}=this.props;s.setCurrFCType(r),s.canIncrease()&&(s.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:s}=this.props;s.setCurrFCType(r),s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating BFCP setting for:",r),d.setCurrFCType(r);const y=d.allocatedBFCP[r];var E=[];for(var O in g.ships){var $=g.ships[O];if($.userid==s.userid&&!b.isDestroyed($))if($.flight)for(var P=0;P<$.systems.length;P++){var j=$.systems[P];if(j)for(var H=0;H<j.systems.length;H++){var L=j.systems[H];if(L&&L.displayName=="Computer"){E.push(L);break}}}else for(var H=0;H<$.systems.length;H++){var L=$.systems[H];if(L.displayName=="Computer"){E.push(L);break}}}console.log("Found BFCP controllers:",E.length);for(var Q=0;Q<E.length;Q++){var L=E[Q];L.setCurrFCType(r);let ze=0;for(;L.getCurrAllocated()<y&&L.canIncrease()&&ze<100;)L.doIncrease(),ze++;for(;L.getCurrAllocated()>y&&L.canDecrease()&&ze<100;)L.doDecrease(),ze++;ze>=100&&console.warn("BFCP Propagation safety break for",L)}S.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{system:r}=this.props;if(!r||!r.allocatedBFCP)return null;const s=Object.keys(r.allocatedBFCP),d=r.allocatedBFCP,g=y=>{const E=r.BFCPtotal_used,O=r.output,$=d[y]||0,P=r.BFCPpertype;return!(E>=O||$>=P)},b=y=>d[y]>0,S=y=>d[y]>=0;return m.jsxs(o$,{children:[m.jsx(l$,{children:"Hyach Computer"}),m.jsxs(s$,{ref:this.listRef,children:[s.map(y=>m.jsxs(rC,{children:[m.jsx(u$,{src:`./img/systemicons/BFCPclasses/${y}.png`,alt:y}),m.jsx(c$,{children:y}),m.jsxs(d$,{children:[m.jsx(Mx,{onClick:()=>this.handleDecrease(y),disabled:!b(y),children:"-"}),m.jsx(f$,{children:d[y]}),m.jsx(Mx,{onClick:()=>this.handleIncrease(y),disabled:!g(y),children:"+"}),m.jsx(Mx,{title:"Propagate to all units",onClick:()=>this.handlePropagate(y),disabled:!S(y),style:{marginLeft:"5px"},children:m.jsx("img",{src:"./img/systemicons/BFCPclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},y)),s.length===0&&m.jsx(rC,{children:"No FC types available"})]}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",r.BFCPtotal_used," / ",r.output," ",m.jsx(p$,{})," Max Per Type: ",r.BFCPpertype]})]})}}const g$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,m$=D.div`
    padding: 3px;
    background-color: #5d3564;
    border: 1px solid #5d3564;
    border-bottom: 1px solid #5d3564;    
    color: #f2f2f2;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;    
    font-weight: bold;
`,v$=D.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,iC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
    
    &:last-child {
        border-bottom: none;
    }
`,y$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,x$=D.span`
    flex-grow: 1;
    font-weight: bold;
`,b$=D.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,Gg=D.div`
    width: 16px; 
    height: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: ${o=>o.disabled?"#555":"#7c4686"};
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: ${o=>o.disabled?"default":"pointer"};
    border: 1px solid #aaa;
    opacity: ${o=>o.disabled?.5:1};
    
    &:hover {
         background-color: ${o=>o.disabled?"#555":"#a06daa"};
    }
    ${o=>!o.disabled&&Yi}
`,w$=o=>window.gamedata.turn===window.shipManager.getTurnPlaced(o)&&window.gamedata.gamephase===-1;class S$ extends Je.Component{constructor(r){super(r)}handleSelect(r){const{system:s}=this.props;s.specCurrClass=r,s.canSelect()&&(s.doSelect(),this.forceUpdate())}handleUnselect(r){const{system:s}=this.props;s.specCurrClass=r,s.canUnselect()&&(s.doUnselect(),this.forceUpdate())}handleUse(r){const{system:s}=this.props;s.specCurrClass=r,s.canUse()&&(s.doUse(),this.forceUpdate())}handleCancel(r){const{system:s}=this.props;s.specCurrClass=r,s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}render(){const{ship:r,system:s}=this.props;if(!s)return null;const d=w$(r);let g=[];d?s.allSpec&&(g=Object.keys(s.allSpec)):s.availableSpec&&(g=Object.keys(s.availableSpec).filter(S=>s.availableSpec[S]>0)),g.sort();let b="";return d?b=`Specialists Selected: ${Object.values(s.availableSpec||{}).reduce((y,E)=>y+E,0)} / ${s.specTotal}`:b=`Specialists Used: ${s.specTotal_used||0} / ${s.specTotal}`,m.jsxs(g$,{children:[m.jsx(m$,{children:"Hyach Specialists"}),m.jsxs(v$,{children:[g.map(S=>{const y=d&&(s.specCurrClass=S,s.canSelect()),E=d&&(s.specCurrClass=S,s.canUnselect()),O=!d&&(s.specCurrClass=S,s.canUse()),$=!d&&(s.specCurrClass=S,s.canDecrease());s.availableSpec&&s.availableSpec[S]>0,s.currAllocatedSpec&&s.currAllocatedSpec[S];const P=`./img/systemicons/Specialistclasses/${S}.png?v=2`;return m.jsxs(iC,{children:[m.jsx(y$,{src:P,alt:S}),m.jsx(x$,{children:S}),m.jsx(b$,{children:d?m.jsxs(m.Fragment,{children:[m.jsx(Gg,{onClick:()=>this.handleUnselect(S),disabled:!E,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})}),m.jsx(Gg,{onClick:()=>this.handleSelect(S),disabled:!y,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})})]}):m.jsxs(m.Fragment,{children:[m.jsx(Gg,{onClick:()=>this.handleUse(S),disabled:!O,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})}),m.jsx(Gg,{onClick:()=>this.handleCancel(S),disabled:!$,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})})]})})]},S)}),g.length===0&&m.jsx(iC,{style:{justifyContent:"center",fontStyle:"italic"},children:"No Specialists Available"})]}),m.jsx("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:b})]})}}const C$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${V.colors.line};
`,E$=D.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${V.colors.line};
    border-bottom: 1px solid ${V.colors.line};
    color: ${V.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,T$=D.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,aC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 8px;
    border-bottom: 1px solid #496791;
    font-size: 11px;
    color: #deebff;
    flex-wrap: wrap;

    &:hover {
        background-color: rgba(73, 103, 145, 0.4);
    }
`,k$=D.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,R$=D.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,D$=D.input`
    width: 30px;
    height: 18px;
    background: rgba(0,0,0,0.5);
    border: 1px solid #496791;
    color: gold;
    text-align: center;
    font-size: 12px; 
    
    // Hide spinner
    &::-webkit-inner-spin-button, 
    &::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    -moz-appearance: textfield;
`,$a=D.div`
    width: 22px;
    height: 16px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 9px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #496791;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    &.small {
        width: 18px;
    }
`;D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 6px;
    vertical-align: middle;
    opacity: 0.7;
`;const M$=D.div`
    width: 100%;
    height: 24px;
    background: #203348;
    border-top: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    font-weight: bold;
    user-select: none;
    transition: background 0.2s;

    &:hover {
        background: #2c4766;
        color: #ffffff;
    }

    &:active {
        background: #1b3348;
    }
`;class oC extends Je.Component{constructor(r){super(r),this.listRef=On.createRef(),this.state={shieldInputs:{}}}getShieldLabel(r){let s=r.startArc,d=r.endArc;if(s===void 0||d===void 0)return r.displayName;let g=(s+d)/2;s>d&&(g=(s+d+360)/2),g=g%360;let b="";return g>=337.5||g<22.5?b="Front":g>=22.5&&g<67.5?b="Front Starboard":g>=67.5&&g<112.5?b="Starboard":g>=112.5&&g<157.5?b="Aft Starboard":g>=157.5&&g<202.5?b="Aft":g>=202.5&&g<247.5?b="Aft Port":g>=247.5&&g<292.5?b="Port":g>=292.5&&g<337.5&&(b="Front Port"),b?`${b} - ${r.displayName}`:r.displayName}getShieldSortPriority(r){const s=this.getShieldLabel(r).split(" - ")[0];return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[s]||99}getGeneratorAndShields(){const{ship:r,system:s}=this.props;let d=null,g=[],b="",S="";return s.name==="ThirdspaceShield"||s.name==="ThirdspaceShieldGenerator"?(b="ThirdspaceShield",S="ThirdspaceShieldGenerator"):(s.name==="ThoughtShield"||s.name==="ThoughtShieldGenerator")&&(b="ThoughtShield",S="ThoughtShieldGenerator"),b?(r.systems&&(Array.isArray(r.systems)?r.systems:Object.values(r.systems)).forEach(E=>{E.name===S&&(d=E),E.name===b&&g.push(E)}),g.sort((y,E)=>{const O=this.getShieldSortPriority(y),$=this.getShieldSortPriority(E);return O!==$?O-$:y.id-E.id}),{generator:d,shields:g,systemName:b}):{generator:null,shields:[]}}handleIncrease(r,s){r.canIncrease()&&(r.doIncrease(s),this.afterShieldChange(r))}handleDecrease(r,s){r.canDecrease()&&(r.doDecrease(s),this.afterShieldChange(r))}handleMin(r){r.canDecrease()&&(r.doMin(),this.afterShieldChange(r))}handleMax(r){r.canIncrease()&&(r.doMax(),this.afterShieldChange(r))}afterShieldChange(r){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}),this.updateInputState(r)}handleInputChange(r,s){if(s===""){this.setState(S=>({shieldInputs:{...S.shieldInputs,[r.id]:""}}));return}const d=parseInt(s,10);if(isNaN(d))return;const g=r.currentHealth,b=d-g;b>0?this.handleIncrease(r,b):b<0&&this.handleDecrease(r,Math.abs(b))}updateInputState(r){this.setState(s=>({shieldInputs:{...s.shieldInputs,[r.id]:r.currentHealth}}))}handleWheel(r,s){r.preventDefault(),(r.deltaY<0?1:-1)>0?this.handleIncrease(s,1):this.handleDecrease(s,1)}handleMouseEnter(r){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const s=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);s&&(s.showWeaponArc(this.props.ship,r),window.webglScene.requestRender())}}handleMouseLeave(r){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const s=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);s&&(s.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(r){r&&(shipManager.power.clickPlus(this.props.ship,r),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}handleDeBoost(r){r&&(shipManager.power.clickMinus(this.props.ship,r),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}handleEqualise(r){r&&(r.doEqualise(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}componentDidMount(){const{generator:r,shields:s}=this.getGeneratorAndShields(),d={};s.forEach(g=>d[g.id]=g.currentHealth),this.setState({shieldInputs:d})}componentDidUpdate(r){const{generator:s,shields:d}=this.getGeneratorAndShields(),g=this.state.shieldInputs,b={};let S=!1;d.forEach(y=>{g[y.id]!==y.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${y.id}`)&&(b[y.id]=y.currentHealth,S=!0)}),S&&this.setState(y=>({shieldInputs:{...y.shieldInputs,...b}}))}render(){const{generator:r,shields:s,systemName:d}=this.getGeneratorAndShields();return r?m.jsxs(C$,{children:[m.jsx(E$,{children:d==="ThirdspaceShield"?"Thirdspace Shields":"Thought Shields"}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791"},children:["Unallocated Shield Energy: ",r.storedCapacity]}),d==="ThirdspaceShield"&&m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791",display:"flex",justifyContent:"center",alignItems:"center",gap:"5px"},children:["Regeneration Rate: ",shipManager.systems.getOutputNoBoost(this.props.ship,r)+shipManager.power.getBoost(r)*s.length,m.jsx($a,{className:"small",onClick:()=>this.handleDeBoost(r),title:"Reduce Boost",children:"-"}),m.jsx($a,{className:"small",onClick:()=>this.handleBoost(r),title:"Boost Generator",children:"+"})]}),m.jsxs(T$,{ref:this.listRef,children:[s.map(g=>m.jsxs(aC,{onMouseEnter:()=>this.handleMouseEnter(g),onMouseLeave:()=>this.handleMouseLeave(g),children:[m.jsx(k$,{children:this.getShieldLabel(g)}),m.jsxs(R$,{children:[m.jsx($a,{onClick:()=>this.handleMin(g),disabled:!g.canDecrease(),title:"Drop shield to 0",children:"Min"}),m.jsx($a,{className:"small",onClick:()=>this.handleDecrease(g,25),disabled:!g.canDecrease(),children:"-25"}),m.jsx($a,{className:"small",onClick:()=>this.handleDecrease(g,10),disabled:!g.canDecrease(),children:"-10"}),m.jsx($a,{className:"small",onClick:()=>this.handleDecrease(g,5),disabled:!g.canDecrease(),children:"-5"}),m.jsx($a,{className:"small",onClick:()=>this.handleDecrease(g,1),disabled:!g.canDecrease(),children:"-1"}),m.jsx(D$,{id:`shield-input-${g.id}`,type:"number",value:this.state.shieldInputs[g.id]!==void 0?this.state.shieldInputs[g.id]:g.currentHealth,onChange:b=>this.handleInputChange(g,b.target.value),onWheel:b=>this.handleWheel(b,g)}),m.jsx($a,{className:"small",onClick:()=>this.handleIncrease(g,1),disabled:!g.canIncrease(),children:"+1"}),m.jsx($a,{className:"small",onClick:()=>this.handleIncrease(g,5),disabled:!g.canIncrease(),children:"+5"}),m.jsx($a,{className:"small",onClick:()=>this.handleIncrease(g,10),disabled:!g.canIncrease(),children:"+10"}),m.jsx($a,{className:"small",onClick:()=>this.handleIncrease(g,25),disabled:!g.canIncrease(),children:"+25"}),m.jsx($a,{onClick:()=>this.handleMax(g),disabled:!g.canIncrease(),title:"Raise shield to maximum",children:"Max"})]})]},g.id)),s.length===0&&m.jsx(aC,{children:"No Shields Found"})]}),r&&m.jsx(M$,{onClick:()=>this.handleEqualise(r),children:"Equalise Shields"})]}):m.jsx("div",{children:"No Generator Found"})}}const lC=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${V.colors.line};
`,sC=D.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${V.colors.line};
    border-bottom: 1px solid ${V.colors.line};
    color: ${V.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,Ox=D.div`
    display: flex;
    align-items: center;
    padding: 3px 8px;
    border-bottom: 1px solid #496791;
    font-size: 11px;
    color: #deebff;
    justify-content: space-between;

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background-color: rgba(73, 103, 145, 0.4);
    }
`,fp=D.div`
    flex: 1;
`,pp=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
`,O$=D.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${V.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`,cd=D.div`
    width: 24px;
    height: 18px;
    background: #203348;
    border: 1px solid #496791;
    color: #deebff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #496791;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${o=>o.$active&&o.$variant!=="activate"&&`
        background: #806c00;
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;
    `}

    ${o=>o.$active&&o.$variant==="activate"&&`
        background: #1b5e20;
        color: white;
        border: 1px solid #4caf50;
        opacity: 1;

        &:hover {
            background: #2e7d32;
            border: 1px solid #66bb6a;
            color: #ffffff;
            opacity: 1;
        }
    `}
`;class $$ extends Je.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}canBoost(){const{ship:r,system:s}=this.props;return s.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(r,s)}canDeBoost(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(s)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){const{ship:r,system:s}=this.props,d=shipManager.power.getBoost(s),g=s.active;return m.jsxs(lC,{children:[m.jsx(sC,{children:"Power Capacitor"}),s.boostable&&m.jsxs(Ox,{children:[m.jsx(fp,{children:"Open Petals"}),m.jsxs(pp,{children:[m.jsx(cd,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:d===0,children:"OFF"}),m.jsx(cd,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:d>0,$variant:"activate",children:"ON"})]})]}),m.jsxs(Ox,{children:[m.jsx(fp,{children:"Double Recharge"}),m.jsxs(pp,{children:[m.jsx(cd,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!g,children:"OFF"}),m.jsx(cd,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:g,$variant:"activate",children:"ON"})]})]})]})}}const uC=D(lC)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`,ai={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",hover:"rgba(75, 43, 81, 0.6)",text:"#f2f2f2",textDim:"#d8b9e6"},A$=D(uC)`
    background-color: ${ai.surface};
    border: 1px solid ${ai.line};
`,j$=D.div`
    padding: 3px;
    background-color: ${ai.line};
    border: 1px solid ${ai.accent};
    color: ${ai.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,cC=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 8px;
    border-bottom: 1px solid ${ai.line};
    font-size: 11px;
    color: ${ai.text};

    &:hover {
        background-color: ${ai.hover};
    }
`,_$=D.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${ai.textDim};
`,Kg=D.div`
    min-width: 24px;
    width: ${o=>o.$wide?"auto":"24px"};
    padding: ${o=>o.$wide?"0 6px":"0"};
    height: 18px;
    box-sizing: border-box;
    background: ${ai.line};
    border: 1px solid ${ai.accent};
    color: ${ai.text};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #5e3666;
        border: 1px solid #9a5aa6;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.$active&&`
        background: ${ai.accent};
        border: 1px solid #a06daa;
        color: #ffffff;
        opacity: 1;
        cursor: default;
        &:hover { background: ${ai.accent}; border: 1px solid #a06daa; }
    `}

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: ${ai.textDim}; border: 1px solid ${ai.accent}; }
    `}
`;class L$ extends Je.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}handlePower(r){const{ship:s,system:d}=this.props;if(!dC(s,d))return;const g=d.getAbductionOrder();d.setAbductionPowerLevel(d.getAbductionPowerLevel(g)+r)&&(webglScene.customEvent("SystemDataChanged",{ship:d.getOwningUnit()||s,system:d}),this.forceUpdate())}handleCancel(){const{ship:r,system:s}=this.props;gamedata.gamephase!==1||!gamedata.isMyShip(s.getOwningUnit()||r)||(weaponManager.removeFiringOrder(s.getOwningUnit()||r,s),this.forceUpdate())}renderMaintain(){const{system:r}=this.props,s=r.isMaintainingVortex(),d=r.chargesVortexUpkeep()?r.getVortexUpkeepCost():0;return m.jsxs(On.Fragment,{children:[m.jsx(sC,{children:"Jump Point"}),m.jsxs(Ox,{children:[m.jsx(fp,{children:"Maintain Vortex"}),m.jsxs(pp,{children:[m.jsx(cd,{onClick:()=>this.handleDeactivate(),disabled:!r.canDeactivate(),$active:!s,children:"OFF"}),m.jsx(cd,{onClick:()=>this.handleActivate(),disabled:!r.canActivate(),$active:s,$variant:"activate",children:"ON"})]})]}),m.jsx(O$,{children:d>0?s?"Held open. "+d+" power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.":"Closes at end of turn unless maintained. Costs "+d+" power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.":s?"Held open. All powered systems except the Scanner are shut down this turn.":"Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner."})]})}renderAbduction(){const{ship:r,system:s}=this.props,d=s.getAbductionOrder(),g=gamedata.getShip(d.targetid),b=s.getAbductionPowerLevel(d),S=dC(r,s),y=s.isExtraDimensional(),E=s.abductionMaxPower||1,O=window.JumpEngine.formatAbductionHalves(s.getAbductionHalves(d)),$=window.JumpEngine.getAbductionChain(d.targetid),P=window.JumpEngine.getAbductionCostPreview(d.targetid),j=s.isAbductionPowered(d);let H;if(j)H=O+" power-turn"+(O==="1"?"":"s")+" for "+s.getAbductionPowerDraw()+" power. So far "+window.JumpEngine.formatAbductionHalves($.total)+" of "+$.cost+" power-turns.";else{const L=!!g&&gamedata.isTerrain(g.shipSizeClass,g.userid),Q=!!g&&((g.hexOffsets||[]).length>0||g.Huge>0);H=(L?"Targeting: takes hold if "+(Q?"every hex it occupies is":"it is")+" inside your connected field and your OEW beats its DEW.":"Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.")+" Power can be applied from next turn"+(P!==null?", and "+P+" power-turns will abduct it.":".")}return m.jsxs(A$,{children:[m.jsxs(j$,{children:["Abduction",g?": "+g.name:""]}),j&&m.jsxs(cC,{children:[m.jsx(fp,{children:y?"Power":"Double power"}),y?m.jsxs(pp,{children:[m.jsx(Kg,{onClick:()=>this.handlePower(-1),disabled:!S||b<=1,children:"-"}),m.jsx(Kg,{$active:!0,children:"x"+b}),m.jsx(Kg,{onClick:()=>this.handlePower(1),disabled:!S||b>=E,children:"+"})]}):null]}),gamedata.gamephase===1&&gamedata.isMyShip(s.getOwningUnit()||r)&&m.jsxs(cC,{children:[m.jsx(fp,{children:"Declaration"}),m.jsx(pp,{children:m.jsx(Kg,{$wide:!0,onClick:()=>this.handleCancel(),children:"CANCEL"})})]}),m.jsx(_$,{children:H})]})}render(){const{system:r}=this.props,s=r.canMaintainVortex()||r.canDeactivate(),d=typeof r.getAbductionOrder=="function"&&!!r.getAbductionOrder();return m.jsxs(On.Fragment,{children:[s&&m.jsx(uC,{children:this.renderMaintain()}),d&&this.renderAbduction()]})}}const dC=(o,r)=>gamedata.gamephase===1&&gamedata.isMyShip(r.getOwningUnit()||o)&&r.isExtraDimensional()&&r.isAbductionPowered(),Aa={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",text:"#f2f2f2"},z$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${o=>o.$isWeapon||o.$isPurple?Aa.surface:"rgba(16, 26, 38, 0.9)"};
    border: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Aa.line:V.colors.line};
`,N$=D.div`
    padding: 3px;
    background-color: ${o=>o.$isWeapon?"#571616":o.$isPurple?Aa.line:"#215a7a"};
    border: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Aa.line:V.colors.line};
    border-bottom: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Aa.line:V.colors.line};
    color: ${o=>o.$isWeapon||o.$isPurple?Aa.text:V.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,P$=D.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid ${o=>o.$isPurple?Aa.line:"#496791"};
    font-size: 12px;
    color: ${o=>o.$isPurple?Aa.text:"#deebff"};
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;D.div`
    flex: 1;
    margin-right: 10px;    
`;const F$=D.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,fC=D.div`
    flex: 1;
    height: 18px;
    background: ${o=>o.$isPurple?Aa.line:"#203348"};
    border: 1px solid ${o=>o.$isPurple?Aa.accent:"#496791"};
    color: ${o=>o.$isPurple?Aa.text:"#deebff"};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${o=>o.$isPurple?"#5e3666":"#496791"};
        border: 1px solid ${o=>o.$isPurple?"#9a5aa6":"#5d82b6ff"};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&(o.$isPurple?`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; border: 1px solid ${Aa.accent}; }
    `:`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `)}

    ${o=>(o.$active||o.$variant==="activate"&&o.$isWeapon)&&o.$variant==="activate"&&!o.$isWeapon&&`
        background: #1b5e20;
        color: white;
        border: 1px solid #4caf50;
        opacity: 1;

        &:hover {
            background: #2e7d32;
            border: 1px solid #66bb6a;
            color: #ffffff;
            opacity: 1;
        }
    `}

    /* An ordinary weapon's Fire button is orange whether or not it has fired: that colour is the
       "this menu belongs to a weapon" signal, not a state readout, and it stays that way.
       ⭐ A TOGGLE weapon ($isToggle - activationIsToggle) is the exception. Its two buttons are the
       two states of ONE switch, so the button that is NOT the current state has to fall through to
       the idle chrome above, the same muted blue its opposite number wears - otherwise both sides
       are lit and nothing on the box says which mode the array is actually in (user request
       2026-09-06, Wide Beam / Normal Beam). */
    ${o=>o.$variant==="activate"&&o.$isWeapon&&!(o.$isToggle&&!o.$active)&&`
        background: #7a3b00e5;
        color: #fff3e0;
        border: 1px solid #ff9900b6;
        opacity: 1;

        &:hover {
            background: #b35900;
            border: 1px solid #ffb74d;
            color: #ffffff;
            opacity: 1;
        }

        ${o.$active?`
            background: #b35900;
            border: 1px solid #ffb74d;
            box-shadow: 0 0 5px #ff9800;
        `:""}
    `}

    ${o=>o.$active&&o.$variant==="deactivate"&&`
        background: #7f1d1d; 
        color: white;
        border: 1px solid #ef4444;
        opacity: 1;

        &:hover {
            background: #991b1b; 
            border: 1px solid #f87171;      
            color: #ffffff;
            opacity: 1;
        }
    `}
`;class I$ extends Je.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivateAll(r){r.preventDefault();const{ship:s,system:d}=this.props;let g=[];s.flight?g=s.systems.map(S=>S.systems).reduce((S,y)=>S.concat(y),[]):g=s.systems;let b=!1;g.forEach(S=>{!S.name||S.name!==d.name||S.canActivate&&typeof S.canActivate=="function"&&S.canActivate()&&(S.doActivate(),b=!0)}),b&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(r){r.preventDefault();const{ship:s,system:d}=this.props;let g=[];s.flight?g=s.systems.map(S=>S.systems).reduce((S,y)=>S.concat(y),[]):g=s.systems;let b=!1;g.forEach(S=>{!S.name||S.name!==d.name||S.canDeactivate&&typeof S.canDeactivate=="function"&&S.canDeactivate()&&(S.doDeactivate(),b=!0)}),b&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate=="function"&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate=="function"&&this.props.system.canDeactivate()}render(){const{ship:r,system:s}=this.props,d=s.active||s.weapon&&!s.activationIsToggle&&weaponManager.hasFiringOrder(r,s),g=typeof s.getActivateLabel=="function"?s.getActivateLabel():null,b=typeof s.getDeactivateLabel=="function"?s.getDeactivateLabel():null,S=g||(s.weapon?"Fire":"Activate"),y=b||(s.weapon?"Don't Fire":"Deactivate"),E=!!s.singleActivationButton,O=!E||this.canActivate(),$=(!s.weapon||s.activationIsToggle)&&(!E||this.canDeactivate()),P=!!s.activationMenuPurple&&!s.weapon;return m.jsxs(z$,{$isWeapon:s.weapon,$isPurple:P,children:[m.jsx(N$,{$isWeapon:s.weapon,$isPurple:P,children:s.displayName}),m.jsx(P$,{$isPurple:P,children:m.jsxs(F$,{children:[O&&m.jsx(fC,{onClick:()=>this.handleActivate(),onContextMenu:j=>this.handleActivateAll(j),disabled:!this.canActivate(),$active:d,$variant:"activate",$isWeapon:s.weapon,$isToggle:!!s.activationIsToggle,$isPurple:P,children:S}),$&&m.jsx(fC,{onClick:()=>this.handleDeactivate(),onContextMenu:j=>this.handleDeactivateAll(j),disabled:!this.canDeactivate(),$active:!d,$variant:"deactivate",$isPurple:P,children:y})]})})]})}}const U$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,B$=D.div`
    padding: 3px;
    background-color: #7a6220;
    border: 1px solid #8d7e40;
    border-bottom: 1px solid #8d7e40;
    color: #fff8d6;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 0.95 !important;
    font-weight: bold;
`,$x=D.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid #917940;
    font-size: 11px;
    color: #fff8d6;
    justify-content: space-between;
    min-width: 120px;

    &:last-child {
        border-bottom: none;
    }

    &:hover {
        background-color: rgba(145, 121, 64, 0.08);
    }
`,Ax=D.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,Qg=D.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,H$=D.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`,vs=D.div`
    width: ${o=>o.$narrow?"18px":"30px"};
    height: 18px;
    background: #3d2e107c;
    border: 1px solid #8d7e40be;
    color: #e0e7ef;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: #917940;
        border: 1px solid #8d7e40;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #3d2e10; color: #fff8d6; }
    `}

    ${o=>o.$active&&o.$variant==="activate"&&`
        background: #1b5e20; 
        color: white;
        border: 1px solid #4caf50;
        opacity: 1;

        &:hover {
            background: #2e7d32; 
            border: 1px solid #66bb6a;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${o=>o.$active&&o.$variant==="deactivate"&&`
        background: #7f1d1d; 
        color: white;
        border: 1px solid #ef4444;
        opacity: 1;

        &:hover {
            background: #991b1b; 
            border: 1px solid #f87171;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${o=>o.$active&&o.$variant==="warning"&&`
        background: #806c00; 
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;

        &:hover {
            background: #998100; 
            border: 1px solid #ffda00;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${o=>o.$active&&o.$variant==="risk"&&`
        background: #a65d00; 
        color: white;
        border: 1px solid #ff9800;
        opacity: 1;

        &:hover {
            background: #cc7a00; 
            border: 1px solid #ffb74d;      
            color: #ffffff;
            opacity: 1;
        }
    `}

    ${o=>o.$active&&o.$variant==="info"&&`
        background: #7a6220;
        color: white;
        border: 1px solid #8d7e40;
        opacity: 1;

        &:hover {
            background: #7a5720;
            border: 1px solid #edcf6d;
            color: #ffffff;
            opacity: 1;
        }
    `}
`;class V$ extends Je.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(r){r.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){const{ship:r,system:s}=this.props;for(;shipManager.power.getBoost(s)>0;)shipManager.power.clickMinus(r,s);shipManager.power.onOfflineClicked(r,s),this.handleUpdate()}}handleOfflineAll(r){if(r.preventDefault(),this.canOffline()){const{ship:s,system:d}=this.props;for(;shipManager.power.getBoost(d)>0;)shipManager.power.clickMinus(s,d);shipManager.power.offlineAll(s,d),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system})}canOffline(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&(s.canOffLine||s.powerReq>0)&&!s.powerLocked&&!shipManager.power.isOffline(r,s)&&!weaponManager.hasFiringOrder(r,s)}canOnline(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(r,s)&&!shipManager.power.isForcedOffline(r,s)&&!shipManager.power.isVortexLockedOffline(r,s)}canBoost(){const{ship:r,system:s}=this.props;return s.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(r,s)&&(!s.isScanner()||s.id==shipManager.power.getHighestSensorsId(r))&&s.name!=="ThirdspaceShieldGenerator"&&s.name!=="powerCapacitor"&&s.name!=="PowerCapacitor"}canDeBoost(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(s)&&s.name!=="ThirdspaceShieldGenerator"&&s.name!=="powerCapacitor"&&s.name!=="PowerCapacitor"}canOverload(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(r,s)&&s.weapon&&s.overloadable&&!shipManager.power.isOverloading(r,s)}canStopOverload(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&s.weapon&&s.overloadable&&shipManager.power.isOverloading(r,s)&&(s.overloadshots>=s.extraoverloadshots||s.overloadshots==0)}render(){const{ship:r,system:s}=this.props,d=this.canOffline()||this.canOnline(),g=s.boostable&&(this.canBoost()||this.canDeBoost()),b=s.overloadable&&(this.canOverload()||this.canStopOverload());if(!d&&!g&&!b)return null;const S=shipManager.power.isOffline(r,s),y=shipManager.power.getBoost(s),E=shipManager.power.isOverloading(r,s),O=s.name==="reactor",$=s.name==="jumpEngine",P=$&&typeof s.getChargeBoostMax=="function"&&s.getChargeBoostMax()>0;let j="Boost Level";return O&&(j="Self-Destruct"),$&&(j=P?"Extra Charging":"Jump to Hyperspace"),m.jsxs(U$,{children:[m.jsx(B$,{children:"Power Settings"}),d&&m.jsxs($x,{children:[m.jsx(Ax,{children:"Power"}),m.jsxs(Qg,{children:[m.jsx(vs,{onClick:()=>this.handleOnline(),onContextMenu:H=>this.handleOnlineAll(H),disabled:!this.canOnline(),$active:!S,$variant:"activate",children:"On"}),m.jsx(vs,{onClick:()=>this.handleOffline(),onContextMenu:H=>this.handleOfflineAll(H),disabled:!this.canOffline(),$active:S,$variant:"deactivate",children:"Off"})]})]}),g&&m.jsxs($x,{children:[m.jsx(Ax,{children:j}),O||$&&!P?m.jsxs(Qg,{children:[m.jsx(vs,{onClick:()=>this.handleBoost(),disabled:y>0||!this.canBoost(),$active:y>0,$variant:O?"deactivate":"risk",children:"Yes"}),m.jsx(vs,{onClick:()=>this.handleDeBoost(),disabled:y===0||!this.canDeBoost(),$active:y===0,$variant:"activate",children:"No"})]}):m.jsxs(Qg,{children:[m.jsx(vs,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:"-"}),m.jsx(H$,{children:y}),m.jsx(vs,{onClick:()=>this.handleBoost(),$narrow:!0,children:"+"})]})]}),b&&m.jsxs($x,{children:[m.jsx(Ax,{children:"Overcharge"}),m.jsxs(Qg,{children:[m.jsx(vs,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:E,$variant:"warning",children:"Yes"}),m.jsx(vs,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!E,$variant:"deactivate",children:"No"})]})]})]})}}const W$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Y$=D.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    border-bottom: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,G$=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
`,pC=D.div`
    width: 20px;
    height: 18px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: #854242;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; }
    `}
`,K$=D.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,Q$=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,hC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,q$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,X$=D.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,J$=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,Z$=D.div`
    width: 20px;
    text-align: center;
`,jx=D.div`
    width: 16px;
    height: 16px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    padding: 0;
    opacity: 0.9;


    &:hover {
        background: #854242;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`;D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class eA extends Je.Component{constructor(r){super(r),this.listRef=On.createRef()}handleIncrease(r){const{system:s}=this.props;s.setCurrShipType(r),s.canIncrease()&&(s.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:s}=this.props;s.setCurrShipType(r),s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene,y=!!(d.hasMultiTarget&&d.hasMultiTarget()),E=y?d.getCurrWeaponId():null,O=y?d.getMineWeapons():[],$=y?O.find($e=>String($e.id)===String(E)):null;if(y&&!$)return;d.setCurrShipType(r);const P=d.rangeSetting||d.range,j=y?d.allocatedRanges[E]:d.allocatedRanges;if(!j)return;const H=j[r]===null||j[r]===void 0?P:j[r];var L=[];for(var Q in g.ships){var xe=g.ships[Q];if(xe.userid==s.userid&&!b.isDestroyed(xe)){if(xe.phpclass&&s.phpclass){if(xe.phpclass!=s.phpclass)continue}else if(xe.shipClass!=s.shipClass)continue;for(var ze in xe.systems){var fe=xe.systems[ze];if(fe&&fe.name===d.name){var ae=!!(fe.hasMultiTarget&&fe.hasMultiTarget());ae===y&&L.push({unit:xe,ctrl:fe})}}}}for(var ce=0;ce<L.length;ce++){var Ee=L[ce],fe=Ee.ctrl;if(y){fe.ensureMultiAllocatedShape&&fe.ensureMultiAllocatedShape();var le=fe.getMineWeapons(),ue=le.find(rt=>rt.displayName===$.displayName&&rt.indexInGroup===$.indexInGroup);if(!ue)continue;fe.setCurrWeaponId(ue.id)}fe.setCurrShipType(r);let ft=0;const He=()=>y?fe.allocatedRanges[fe.getCurrWeaponId()]:fe.allocatedRanges,Tt=()=>fe.range||fe.rangeSetting,bt=()=>{const rt=He();if(!rt)return Tt();const Be=rt[r];return Be??Tt()};for(;bt()<H&&fe.canIncrease()&&ft<100;)fe.doIncrease(),ft++;for(;bt()>H&&fe.canDecrease()&&ft<100;)fe.doDecrease(),ft++;ft>=100&&console.warn("Mine Settings Propagation safety break for",fe)}S.customEvent("SystemDataChanged",{ship:s,system:d})}cycleWeapon(r){const{system:s}=this.props;if(!s.hasMultiTarget||!s.hasMultiTarget())return;const d=s.getMineWeapons();if(d.length===0)return;const g=s.getCurrWeaponId();let b=d.findIndex(y=>String(y.id)===String(g));b<0&&(b=0);const S=(b+r+d.length)%d.length;s.setCurrWeaponId(d[S].id),this.forceUpdate()}render(){const{system:r}=this.props;if(!r||(r.range=r.range||r.rangeSetting,!r.range))return null;const s=!!(r.hasMultiTarget&&r.hasMultiTarget());let d=[],g=null,b=null,S=r.allocatedRanges||{};s?(r.ensureMultiAllocatedShape&&r.ensureMultiAllocatedShape(),d=r.getMineWeapons(),g=r.getCurrWeaponId(),b=d.find(L=>String(L.id)===String(g))||d[0]||null,S=g!=null&&r.allocatedRanges[g]?r.allocatedRanges[g]:{}):(r.ensureFlatAllocatedShape&&r.ensureFlatAllocatedShape(),S=r.allocatedRanges||{});const y=Object.keys(S),E=r.validTargets||y,O=L=>{if(!E.includes(L))return"N/A";const Q=S[L];return Q??r.range},$=L=>E.includes(L)?O(L)<r.range:!1,P=L=>E.includes(L)?O(L)>0:!1,j=L=>!!E.includes(L),H=s&&d.length>0?m.jsxs(G$,{children:[m.jsx(pC,{onClick:()=>this.cycleWeapon(-1),disabled:d.length<2,title:"Previous weapon",children:"<"}),m.jsx(K$,{children:b?b.label:""}),m.jsx(pC,{onClick:()=>this.cycleWeapon(1),disabled:d.length<2,title:"Next weapon",children:">"})]}):m.jsx(Y$,{children:"Set Mine Range"});return m.jsxs(W$,{children:[H,m.jsxs(Q$,{ref:this.listRef,children:[y.map(L=>m.jsxs(hC,{children:[m.jsx(q$,{src:`./img/systemicons/BFCPclasses/${L}.png`,alt:L}),m.jsx(X$,{children:L}),m.jsxs(J$,{onWheel:Q=>{Q.deltaY<0&&$(L)?this.handleIncrease(L):Q.deltaY>0&&P(L)&&this.handleDecrease(L)},children:[m.jsx(jx,{onClick:()=>this.handleDecrease(L),disabled:!P(L),children:"-"}),m.jsx(Z$,{children:O(L)}),m.jsx(jx,{onClick:()=>this.handleIncrease(L),disabled:!$(L),children:"+"}),m.jsx(jx,{title:s?"Propagate this weapon's settings to same-class mines with Multiple Targets":"Propagate to all mines of same type",onClick:()=>this.handlePropagate(L),disabled:!j(L),style:{marginLeft:"5px"},children:m.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},L)),y.length===0&&m.jsx(hC,{children:"No ship types available"})]}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Max Range: ",r.range]})]})}}const tA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,nA=D.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    border-bottom: 1px solid #b43131;    
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,rA=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,gC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,iA=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,aA=D.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,oA=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,lA=D.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${o=>o.$active?"#4CAF50":"#F44336"};
`,mC=D.div`
    height: 16px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.9;

    &:hover {
        background: #854242;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`;class sA extends Je.Component{constructor(r){super(r),this.listRef=On.createRef()}handleToggle(r){const{system:s}=this.props;s.setCurrShipType(r),s.canSet()?(s.doSet(),this.forceUpdate()):s.canUnset()&&(s.doUnset(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating Mine settings for:",r),d.setCurrShipType(r);const y=d.allocatedShipTypes[r];var E=[];for(var O in g.ships){var $=g.ships[O];if($.userid==s.userid&&!b.isDestroyed($))for(var P=0;P<$.systems.length;P++){var j=$.systems[P];if($.shipClass==s.shipClass&&j.name===d.name){E.push(j);break}}}console.log("Found Mine Weapons of same type:",E.length);for(var H=0;H<E.length;H++){var j=E[H];j.setCurrShipType(r);let Q=0;for(;j.allocatedShipTypes[r]!==y&&(y?j.canSet():j.canUnset())&&Q<10;)y?j.doSet():j.doUnset(),Q++;Q>=10&&console.warn("Mine Settings Propagation safety break for",j)}S.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{system:r}=this.props;if(!r)return null;const s=r.allocatedShipTypes||{},d=Object.keys(s),g=S=>s[S]?"YES":"NO",b=S=>{const y=Number(this.props.ship.spawned),E=y===-1?1:y+1;return window.gamedata.turn===E};return m.jsxs(tA,{children:[m.jsx(nA,{children:"Set Target Types"}),m.jsxs(rA,{ref:this.listRef,children:[d.map(S=>m.jsxs(gC,{children:[m.jsx(iA,{src:`./img/systemicons/BFCPclasses/${S}.png`,alt:S}),m.jsx(aA,{children:S}),m.jsxs(oA,{children:[m.jsx(lA,{$active:s[S],children:g(S)}),m.jsx(mC,{onClick:()=>this.handleToggle(S),disabled:!b(),style:{marginLeft:"5px"},children:"Toggle"}),m.jsx(mC,{title:"Propagate to all mines of same type",onClick:()=>this.handlePropagate(S),disabled:!1,style:{marginLeft:"5px",width:"16px",padding:"0"},children:m.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},S)),d.length===0&&m.jsx(gC,{children:"No ship types available"})]})]})}}const _x=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: rgba(8, 28, 12, 0.92);
    border: 1px solid #3f8a3f;
`,vC=D.div`
    padding: 3px;
    background-color: #16401b;
    border: 1px solid #3f8a3f;
    color: #e6ffe6;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,uA=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: #16401b;
    border: 1px solid #3f8a3f;
    color: #e6ffe6;
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 2px;
    min-width: 220px;
`,yC=D.div`
    width: 20px;
    height: 18px;
    background: #1b5e20;
    border: 1px solid #2e7d32;
    color: #e6ffe6;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: #2e7d32;
        border: 1px solid #66bb6a;
        color: #ffffff;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #1b5e20; border: 1px solid #2e7d32; color: #e6ffe6; }
    `}
`,cA=D.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;D.div`
    text-align: center;
    color: #bdf0bd;
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;const hp=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,xC=D.div`
    width: 70px;
    font-size: 10px;
    color: #bdf0bd;
    user-select: none;
`,ju=D.div`
    flex: 1;
    height: 20px;
    background: #1b5e20;
    border: 1px solid #2e7d32;
    color: #e6ffe6;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.95;
    user-select: none;

    &:hover {
        background: #2e7d32;
        border: 1px solid #66bb6a;
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.$active&&`
        background: #2e7d32;
        border: 1px solid #66bb6a;
        box-shadow: 0 0 5px #4caf50;
        color: #ffffff;
        opacity: 1;
    `}

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #1b5e20; border: 1px solid #2e7d32; color: #e6ffe6; }
    `}
`;class dA extends Je.Component{refresh(){const{ship:r,system:s}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:s})}cycleMode(r){const{system:s}=this.props;if(!gamedata.isMyShip(this.props.ship))return;const d=s.firingMode==1?2:1;s.setFiringMode(d),typeof s.initializationUpdate=="function"&&s.initializationUpdate(),this.refresh()}activateMode1(){const{ship:r,system:s}=this.props;s.canActivate()&&(s.doActivate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:s}),webglScene.customEvent("CloseSystemInfo"))}targetWarrior(){const{ship:r,system:s}=this.props;weaponManager.isSelectedWeapon(s)||weaponManager.selectWeapon(r,s),webglScene.customEvent("SystemDataChanged",{ship:r,system:s}),webglScene.customEvent("CloseSystemInfo")}setRotation(r,s){const{system:d}=this.props;d.rotationDirection=r,d.rotationAmount=s,typeof d.updateRotationNotes=="function"&&d.updateRotationNotes(),this.refresh()}renderInitialOrders(){const{system:r}=this.props,s=parseInt(r.firingMode,10),d=r.firingModes[s]||"";return m.jsxs(_x,{children:[m.jsxs(uA,{children:[m.jsx(yC,{onClick:()=>this.cycleMode(-1),title:"Previous mode",children:"<"}),m.jsx(cA,{children:d}),m.jsx(yC,{onClick:()=>this.cycleMode(1),title:"Next mode",children:">"})]}),s==1&&m.jsx(hp,{children:m.jsx(ju,{onClick:()=>this.activateMode1(),children:"Activate"})}),s==2&&m.jsx(hp,{children:m.jsx(ju,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(r),children:"Target friendly Warrior"})})]})}engageMode3(){const{ship:r,system:s}=this.props;gamedata.isMyShip(r)&&(weaponManager.hasFiringOrder(r,s)||(s.setFiringMode(3),typeof s.initializationUpdate=="function"&&s.initializationUpdate(),this.refresh()))}renderPreFiring(){const{system:r}=this.props;if(r.firingMode!=3)return m.jsxs(_x,{children:[m.jsx(vC,{children:"Gravitic Augmenter"}),m.jsx(hp,{children:m.jsx(ju,{onClick:()=>this.engageMode3(),children:"Engage Gravity Shifting"})})]});const s=r.rotationDirection||1,d=r.rotationAmount||1,g=(b,S)=>s==b&&d==S;return m.jsxs(_x,{children:[m.jsx(vC,{children:"Gravity Shift Settings"}),m.jsxs(hp,{children:[m.jsx(xC,{children:"Clockwise"}),m.jsx(ju,{onClick:()=>this.setRotation(1,1),$active:g(1,1),children:"60°"}),m.jsx(ju,{onClick:()=>this.setRotation(1,2),$active:g(1,2),children:"120°"})]}),m.jsxs(hp,{children:[m.jsx(xC,{children:"Anti-Clockwise"}),m.jsx(ju,{onClick:()=>this.setRotation(2,1),$active:g(2,1),children:"60°"}),m.jsx(ju,{onClick:()=>this.setRotation(2,2),$active:g(2,2),children:"120°"})]})]})}render(){const{system:r}=this.props;return r?gamedata.gamephase==1?(r.firingMode==3&&(r.setFiringMode(1),typeof r.initializationUpdate=="function"&&r.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}}const qg=3,fA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,pA=D.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,hA=D.div`
    text-align: center;
    color: ${o=>o.$empty?"#f0a0a0":"#f2f2f2"};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,gA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,mA=D.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,bC=D.div`
    width: 20px;
    height: 20px;
    flex: 0 0 20px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 13px;
    font-weight: bold;
    user-select: none;

    &:hover {
        background: #854242;
        border: 1px solid #b43131;
        color: #ffffff;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; border: 1px solid #641b1b; color: #f2f2f2; }
    `}
`,vA=D.input`
    flex: 0 0 48px;
    width: 60px;
    height: 20px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    line-height: 20px;
    font-family: "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #ffffff;
    background-color: #200014;
    border: 1px solid #641b1b;
    outline: none;

    &:focus {
        border-color: #b43131;
        box-shadow: 0 0 5px rgba(180, 49, 49, 0.6);
    }
`;D.div`
    margin: 3px 5px 5px 5px;
    height: 20px;
    background: #683333;
    border: 1px solid #641b1b;
    color: #f2f2f2;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    user-select: none;

    &:hover {
        background: #854242;
        border: 1px solid #b43131;
        color: #ffffff;
    }
`;const _u=[{key:"hitBoost5",label:"Hit Chance",increment:5,prefix:"+",suffix:"%",display:o=>o*2,parse:o=>o/2},{key:"shotBoost",label:"Shots",increment:1,prefix:"+",suffix:"",display:o=>o,parse:o=>o},{key:"dmgBoost5",label:"Damage",increment:5,prefix:"+",suffix:"",display:o=>o,parse:o=>o}],yA=(o,r)=>o.prefix+o.display(r|0)+o.suffix;class xA extends Je.Component{fieldSteps(r,s){return(s|0)/r.increment}allocatedSteps(r){return _u.reduce((s,d)=>s+this.fieldSteps(d,r[d.key]),0)}maxSteps(){const{ship:r,system:s}=this.props;return typeof s.getMaxSteps=="function"?s.getMaxSteps(r):Math.floor(shipManager.movement.getRemainingEngineThrust(r)/qg)}totalShots(r){return(r.guns|0)+(r.shotBoost|0)}maxDamageSteps(r){return this.totalShots(r)}clampDamageToShots(r){const s=_u.find(g=>g.key==="dmgBoost5"),d=this.maxDamageSteps(r)*s.increment;(r.dmgBoost5|0)>d&&(r.dmgBoost5=d)}refresh(){const{ship:r,system:s}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:s})}setValue(r,s){const{ship:d,system:g}=this.props;if(!gamedata.isMyShip(d))return;let b=Math.max(0,Math.round((s|0)/r.increment)*r.increment);const S=this.allocatedSteps(g)-this.fieldSteps(r,g[r.key]),y=Math.max(0,this.maxSteps()-S);if(b/r.increment>y&&(b=y*r.increment),r.key==="dmgBoost5"){const O=this.maxDamageSteps(g)*r.increment;b>O&&(b=O)}g[r.key]=b,r.key==="shotBoost"&&this.clampDamageToShots(g),typeof g.updateBoostNotes=="function"&&g.updateBoostNotes(),this.syncFlight(g),this.refresh()}syncFlight(r){const s=this.getFlightPulsars();for(let d=0;d<s.length;d++){const g=s[d];g!==r&&(_u.forEach(b=>{g[b.key]=r[b.key]|0}),typeof g.updateBoostNotes=="function"&&g.updateBoostNotes())}}step(r,s){const{system:d}=this.props;this.setValue(r,(d[r.key]|0)+s*r.increment)}onWheel(r,s){s.preventDefault(),this.step(r,s.deltaY<0?1:-1)}onInput(r,s){const d=String(s.target.value).replace(/[^0-9]/g,""),g=d===""?0:parseInt(d,10);this.setValue(r,r.parse(g))}propagate(){const{ship:r,system:s}=this.props;if(!gamedata.isMyShip(r))return;const d=this.getFlightPulsars();for(let g=0;g<d.length;g++){const b=d[g];b!==s&&(_u.forEach(S=>{b[S.key]=s[S.key]|0}),this.clampWeaponToBudget(b),typeof b.updateBoostNotes=="function"&&b.updateBoostNotes())}this.refresh()}getFlightPulsars(){const{ship:r}=this.props,s=[],d=r&&r.systems?r.systems:[];for(let g=0;g<d.length;g++){const b=d[g]&&d[g].systems?d[g].systems:[];for(let S=0;S<b.length;S++)b[S]&&b[S].name==="MinorThoughtPulsar"&&s.push(b[S])}return s}clampWeaponToBudget(r){const s=["dmgBoost5","shotBoost","hitBoost5"],d=b=>_u.find(S=>S.key===b);let g=0;for(;g++<200&&!(_u.reduce((S,y)=>S+(r[y.key]|0)/y.increment,0)<=this.maxSteps());)for(let S=0;S<s.length;S++){const y=s[S];if((r[y]|0)>0){r[y]=(r[y]|0)-d(y).increment;break}}}render(){const{ship:r,system:s}=this.props;if(!s)return null;const d=typeof s.getSpareThrust=="function"?s.getSpareThrust(r):shipManager.movement.getRemainingEngineThrust(r),g=this.allocatedSteps(s)*qg,b=Math.max(0,d-g),S=b>=qg;return m.jsxs(fA,{children:[m.jsx(pA,{children:"Minor Thought Pulsar"}),m.jsxs(hA,{$empty:b<qg,children:["Available thrust: ",b]}),_u.map(y=>{const E=s[y.key]|0,O=y.key==="dmgBoost5"&&E/y.increment>=this.maxDamageSteps(s);return m.jsxs(gA,{children:[m.jsx(mA,{children:y.label}),m.jsx(bC,{title:"Less",disabled:E<=0,onClick:()=>this.step(y,-1),children:"−"}),m.jsx(vA,{type:"text",value:yA(y,E),onChange:$=>this.onInput(y,$),onWheel:$=>this.onWheel(y,$)}),m.jsx(bC,{title:O?"One +5 per shot (add shots for more)":"More",disabled:!S||O,onClick:()=>this.step(y,1),children:"+"})]},y.key)})]})}}const gp=o=>{let r=null;const s=d=>{d.preventDefault(),o(d)};return d=>{r!==d&&(r&&r.removeEventListener("wheel",s,{passive:!1}),r=d,r&&r.addEventListener("wheel",s,{passive:!1}))}},qe={bg:"rgba(8, 12, 16, 0.96)",line:"#33414f",titleBg:"#1b242e",text:"#c7d3de",dim:"#6c7a87",btnBg:"#161d25",btnText:"#aebac6",well:"#05080b",focus:"#4d6070"},Br={enh:{rail:V.colors.enhLine,btnBg:"#292114",btnText:V.colors.enhTitle},damage:{rail:"#3d7a9c",btnBg:"#142129",btnText:"#a4cde3"},crit:{rail:"#a85c33",btnBg:"#291914",btnText:"#eab99e"}},bA={rail:qe.line,btnBg:qe.btnBg,btnText:qe.btnText},xl=o=>o.$ink||bA,wA=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${o=>o.$gold?V.colors.enhText:qe.text};
`,SA=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,CA=D.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;D.span`
    flex: 0 0 auto;
    color: ${o=>o.$gold?V.colors.enhText:qe.dim};
    font-size: 10px;
    opacity: ${o=>o.$gold?.75:1};
`;const dd=D.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: ${o=>xl(o).btnBg};
    border: 1px solid ${o=>xl(o).rail};
    color: ${o=>xl(o).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${o=>xl(o).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${xl(o).btnBg};
            color: ${xl(o).btnText};
        }
    `}
`,wC=D.input`
    flex: 0 0 44px;
    width: 44px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${V.fonts.mono};
    font-size: 12px;
    color: ${o=>o.$destroyed?"#ff8a80":"#ffffff"};
    background-color: ${qe.well};
    border: 1px solid ${o=>xl(o).rail};
    outline: none;

    &:focus {
        border-color: ${o=>xl(o).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,SC=D.div`
    padding: 3px;
    background-color: ${qe.titleBg};
    border-bottom: 1px solid ${qe.line};
    color: ${qe.text};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    ${o=>o.$sticky?"position: sticky; top: 0;":""}
`,Lx=D.div`
    padding: 3px;
    text-align: center;
    font-size: 10px;
    letter-spacing: 0.5px;
    user-select: none;

    /*ApplyDamageMenu has no title bar any more, so whichever bar comes first butts straight
      onto the container's own 1px border - two hairlines in two colours, which reads as a
      rendering fault rather than as a frame. Self-maintaining: it is always whichever section
      happens to be on top, and Enhancements is absent more often than not.*/
    &:first-child {
        border-top: none;
    }
`,zx=D.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 2px 0 0 ${o=>xl(o).rail};
`,EA=D(Lx)`
    background-color: ${V.colors.enhBg};
    border-top: 1px solid ${V.colors.enhLine};
    border-bottom: 1px solid ${V.colors.enhLine};
    color: ${V.colors.enhTitle};
`,TA=D(Lx)`
    background-color: #23506b;
    border-top: 1px solid ${Br.damage.rail};
    border-bottom: 1px solid ${Br.damage.rail};
    color: #e8f2ff;
`,kA=D(Lx)`
    background-color: #6d3823;
    border-top: 1px solid ${Br.crit.rail};
    border-bottom: 1px solid ${Br.crit.rail};
    color: #ffece2;
`,CC=D.div`
    height: 2px;
    background-color: ${o=>o.$chrome?qe.line:V.colors.enhLine};
    opacity: 0.8;
`,RA=D.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,DA=kA,MA=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 2px 8px;
    font-size: 11px;
    color: ${V.colors.warningSoft};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${o=>o.$empty&&`
        color: #6f6257;
    `}
`,OA=D.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,$A=D.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${qe.dim};
`,AA=D.div`
    flex: 0 0 auto;
    color: ${qe.dim};
`,jA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,_A=D.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${V.fonts.mono};
    font-size: 11px;
    color: ${o=>o.$empty?"#6f6257":"#ffffff"};
`,LA=D.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,zA=D.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,NA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px 4px;
`,PA=D.select`
    flex: 1 1 auto;
    /*Both needed: min-width:0 lets a flex item shrink below its content, width:100% stops
      it claiming its longest option's width once the menu's max-width has bounded it.*/
    min-width: 0;
    width: 100%;
    height: 18px;
    box-sizing: border-box;
    padding: 0 2px;
    font-family: inherit;
    font-size: 10px;
    color: ${qe.text};
    background-color: ${qe.well};
    /*Takes the section's ink like the tickers above it - it is the widest control in the
      section, so leaving it on the chassis border was the one thing that still read as
      unpainted once the tickers went rust.*/
    border: 1px solid ${Br.crit.rail};
    outline: none;

    &:focus { border-color: ${Br.crit.btnText}; }
`;D.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${qe.dim};
    cursor: pointer;
    user-select: none;
`;const EC=(o,r,s,d)=>{const g=[];for(const b in o||{}){if(!o.hasOwnProperty(b))continue;const S=battleDamage.PARAM_CRITICALS[b],y=parseInt((d||{})[b],10)||0;g.push({type:b,isParam:!!S,paramLabel:S?S.label:null,label:battleDamage.critLabel(b,r,y),count:parseInt(o[b],10)||0,param:y,transient:!!(s&&s[b])})}return g};class TC extends Je.Component{constructor(r){super(r),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(r){r.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=gp(s=>this.step(r,s.deltaY<0?1:-1))),this.wheelRefs[r]}critMap(){const r=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return r&&r.c?Object.assign({},r.c):{}}paramMap(){const r=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return r&&r.p?Object.assign({},r.p):{}}valueOf(r){return r.isParam?r.param:r.count}maxValueOf(r){if(!r.isParam)return battleDamage.critLimit(r.type);const s=battleDamage.PARAM_CRITICALS[r.type];return Math.min(battleDamage.MAX_CRIT_PARAM,s&&s.max||battleDamage.MAX_CRIT_PARAM)}setValue(r,s){const{ship:d,kind:g,reference:b,onChange:S}=this.props,y=this.critMap(),E=this.paramMap();s>0?r.isParam?(y[r.type]=1,E[r.type]=Math.min(s,this.maxValueOf(r))):y[r.type]=Math.min(s,battleDamage.critLimit(r.type)):(delete y[r.type],delete E[r.type]),battleDamage.setCriticals(d,g,b,y,E),S&&S()}step(r,s){const d=this.rowForType(r);this.setValue(d,this.valueOf(d)+s)}rowForType(r){const{ship:s}=this.props,d=battleDamage.PARAM_CRITICALS[r],g=parseInt(this.paramMap()[r],10)||0;return{type:r,isParam:!!d,paramLabel:d?d.label:null,label:battleDamage.critLabel(r,s.preBattleCritDesc,g),count:parseInt(this.critMap()[r],10)||0,param:g,transient:!!(s.preBattleCritTransient&&s.preBattleCritTransient[r])}}displayRows(r){const{ship:s,kind:d,reference:g}=this.props;return battleDamage.rememberCriticals(s,d,g,(r||[]).map(S=>S.type)).map(S=>this.rowForType(S))}addableTypes(r){const{ship:s,kind:d,reference:g}=this.props,b=battleDamage.offerableCriticals(s,d,g,this.state.showAll),S={};return r.forEach(y=>{S[y]=!0}),b.filter(y=>!S[y]).map(y=>({type:y,label:this.pickerLabel(y)})).sort((y,E)=>y.label.localeCompare(E.label))}pickerLabel(r){const s=battleDamage.PARAM_CRITICALS[r];return s?s.label:battleDamage.critLabel(r,this.props.ship.preBattleCritDesc,0)}onAdd(r){r&&this.setValue(this.rowForType(r),1)}render(){const{ship:r,rows:s,editable:d}=this.props,g=d?this.displayRows(s):s||[],b=!!(d&&battleDamage.catalogueFor(r)),S=b?this.addableTypes(g.map(y=>y.type)):[];return!g.length&&!b?null:m.jsxs(RA,{children:[m.jsx(CC,{$chrome:!0}),m.jsx(DA,{children:"Critical Effects"}),m.jsxs(zx,{$ink:Br.crit,children:[g.map(y=>{const E=this.valueOf(y),O=this.maxValueOf(y);return m.jsxs(MA,{$empty:d&&E<=0,children:[m.jsxs(OA,{title:y.type,children:[d&&y.isParam?y.paramLabel:y.label,y.transient&&m.jsx($A,{children:"(turn 1 only)"})]}),d?m.jsxs(jA,{children:[m.jsx(dd,{$ink:Br.crit,title:y.isParam?"Reduce":"One fewer",disabled:E<=0,onClick:()=>this.step(y.type,-1),children:"−"}),m.jsx(_A,{$empty:E<=0,ref:this.wheelRef(y.type),children:E}),m.jsx(dd,{$ink:Br.crit,title:y.isParam?"Increase":E>=O&&O===1?"This effect only applies once":"One more",disabled:E>=O,onClick:()=>this.step(y.type,1),children:"+"})]}):y.count>1&&m.jsxs(AA,{children:["(x",y.count,")"]})]},y.type)}),b&&m.jsx(NA,{children:m.jsxs(PA,{value:"",disabled:S.length===0,title:"Add a critical effect to this unit before the battle",onChange:y=>this.onAdd(y.target.value),children:[m.jsx("option",{value:"",children:S.length?"+ Add effect…":"Nothing to add"}),S.map(y=>m.jsx("option",{value:y.type,children:y.label},y.type))]})})]})]})}}const FA=D.div`
    display: flex;
    justify-content: flex-end;
    padding: 2px 8px 4px;
    font-size: 10px;
    color: ${V.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,IA=D.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${V.fonts.mono};
    font-size: 10px;
    color: ${V.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${o=>o.$spent?.6:1};
`;class UA extends Je.Component{constructor(r){super(r),this.wheelRef=gp(s=>this.step(s.deltaY<0?1:-1))}step(r){const{row:s,onChange:d}=this.props,g=Math.max(0,Math.min(s.max,s.count+r));g!==s.count&&d(s.enhID,g)}onInput(r){const{row:s,onChange:d}=this.props,g=String(r.target.value).replace(/[^0-9]/g,""),b=g===""?0:parseInt(g,10);d(s.enhID,Math.max(0,Math.min(s.max,b)))}render(){const{row:r}=this.props,s=r.count>=r.max,d=r.max>1?`${r.label} - ${r.count}/${r.max} levels`+(r.count>0?`, ${r.price} pts spent`:"")+(s?"":`; next level ${r.nextPrice} pts`):`${r.label} - ${r.price||r.nextPrice} pts`;return m.jsxs(wA,{$gold:!0,title:d,children:[m.jsx(SA,{children:m.jsx(CA,{children:r.label})}),m.jsx(dd,{$ink:Br.enh,title:"Remove a level",disabled:r.count<=0,onClick:()=>this.step(-1),children:"−"}),m.jsx(wC,{ref:this.wheelRef,$ink:Br.enh,type:"text",value:r.count,onChange:g=>this.onInput(g)}),m.jsx(dd,{$ink:Br.enh,title:r.count>=r.max?"Already at the maximum":`Add a level (${r.nextPrice} pts)`,disabled:r.count>=r.max,onClick:()=>this.step(1),children:"+"}),m.jsx(IA,{$spent:s,title:s?`Fully bought - ${r.price} pts`:"Cost of the next level",children:s?`${r.price}p`:`${r.nextPrice}p`})]})}}class BA extends Je.Component{render(){const{rows:r,onChange:s}=this.props;if(!r||r.length===0)return null;const d=r.reduce((g,b)=>g+(b.count>0?b.price:0),0);return m.jsxs(On.Fragment,{children:[m.jsx(EA,{children:"✦ ENHANCEMENTS"}),m.jsxs(zx,{$ink:Br.enh,children:[r.map(g=>m.jsx(UA,{row:g,onChange:s},g.enhID)),d>0&&m.jsxs(FA,{children:["Refits: ",d," pts"]})]})]})}}const HA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    max-width: 300px;
    box-sizing: border-box;
    /*Fill and frame from ./menuControls, shared with the fighter and mine editors - see the
      note there on why the title bar is no longer the old teal, and why none of the three
      carries an element opacity any more.*/
    background-color: ${qe.bg};
    border: 1px solid ${qe.line};
`,VA=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${qe.text};
`,WA=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,YA=D.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${o=>o.$disabled?"not-allowed":"pointer"};
    user-select: none;
    opacity: ${o=>o.$disabled?.4:1};
    color: ${o=>o.$on?"#ff8a80":qe.dim};
`,GA=D.span`
    flex: 0 0 auto;
    color: ${qe.dim};
    font-size: 10px;
`,KA=D.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;class QA extends Je.Component{constructor(r){super(r),this.wheelRef=gp(s=>this.step(s.deltaY<0?1:-1))}entry(){const{ship:r,system:s}=this.props;return battleDamage.getEntry(r,battleDamage.KIND_SYSTEM,s.id)||{}}remaining(){const{system:r}=this.props,s=this.entry();return s.k?0:Math.max(0,r.maxhealth-(parseInt(s.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return this.isIndestructible()?1:0}setRemaining(r){const{ship:s,system:d}=this.props,g=d.maxhealth,b=Math.min(this.floor(),g);let S=parseInt(r,10);isNaN(S)&&(S=g),S=Math.max(b,Math.min(g,S));const y=g-S,E=S===0&&g>0;E&&this.rememberHealth(),battleDamage.setSystem(s,d.id,{d:y,k:E?1:0}),this.refresh()}rememberHealth(){const{ship:r,system:s}=this.props;this.isDestroyed()||battleDamage.rememberHealth(r,battleDamage.KIND_SYSTEM,s.id,this.remaining())}setDestroyed(r){const{ship:s,system:d}=this.props;if(r&&this.isIndestructible())return;if(r){this.rememberHealth(),battleDamage.setSystem(s,d.id,{d:d.maxhealth,k:1}),this.refresh();return}const g=battleDamage.healthMemory(s,battleDamage.KIND_SYSTEM,d.id),b=g>0?Math.min(d.maxhealth,g):d.maxhealth;battleDamage.setSystem(s,d.id,{d:d.maxhealth-b,k:0}),this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r);let s=[];if(window.systemEnhancements){const d=parseFloat(r.pointCostSysEnh)||0;s=systemEnhancements.dropDestroyed(r),s.length&&this.settleRefitCost(r,d)}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate(),s.length&&window.confirm&&typeof confirm.warning=="function"&&confirm.warning(systemEnhancements.describeRemoved(s))}settleRefitCost(r,s){const d=parseFloat(r.pointCostSysEnh)||0;r.pointCost=(parseFloat(r.pointCost)||0)-(s-d),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet()}setEnhancement(r,s){const{ship:d,system:g}=this.props;if(!window.systemEnhancements)return;const b=parseFloat(d.pointCostSysEnh)||0,S=systemEnhancements.taken(d,g.id,r);if(s===S)return;if(systemEnhancements.set(d,g.id,r,s),this.settleRefitCost(d,b),!(!window.gamedata||typeof gamedata.canAffordRefit!="function"||gamedata.canAffordRefit(d))){const E=parseFloat(d.pointCostSysEnh)||0;systemEnhancements.set(d,g.id,r,S),this.settleRefitCost(d,E),systemEnhancements.apply(d),this.forceUpdate(),window.confirm&&typeof confirm.error=="function"&&confirm.error("You cannot afford that enhancement!",function(){});return}systemEnhancements.apply(d),this.refresh()}step(r){this.setRemaining(this.remaining()+r)}onInput(r){const s=String(r.target.value).replace(/[^0-9]/g,"");this.setRemaining(s===""?0:parseInt(s,10))}render(){const{ship:r,system:s}=this.props;if(!s||!(s.maxhealth>0))return null;const d=this.remaining(),g=this.isDestroyed(),b=this.isIndestructible(),S=this.entry(),y=EC(S.c,r.preBattleCritDesc,r.preBattleCritTransient,S.p),E=window.systemEnhancements&&!g?systemEnhancements.menuRowsFor(r,s):[];return m.jsxs(HA,{onClick:O=>O.stopPropagation(),children:[m.jsx(BA,{rows:E,onChange:(O,$)=>this.setEnhancement(O,$)}),E.length>0&&m.jsx(CC,{}),m.jsx(TA,{children:"Damage"}),m.jsx(zx,{$ink:Br.damage,children:m.jsxs(VA,{children:[m.jsxs(WA,{title:`${s.displayName||s.name} (system id ${s.id})`,children:[m.jsx(KA,{children:s.displayName||s.name}),m.jsxs(GA,{children:["#",s.id]})]}),m.jsx(dd,{$ink:Br.damage,title:b&&d<=1?"A reactor cannot be destroyed before the battle":"More damage",disabled:g||d<=this.floor(),onClick:()=>this.step(-1),children:"−"}),m.jsx(wC,{ref:this.wheelRef,$ink:Br.damage,type:"text",$destroyed:g,disabled:g,value:g?0:d,onChange:O=>this.onInput(O)}),m.jsx(dd,{$ink:Br.damage,title:"Repair",disabled:g||d>=s.maxhealth,onClick:()=>this.step(1),children:"+"}),m.jsxs(YA,{$on:g,$disabled:b,title:b?"A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship":"Mark this system destroyed before the battle starts",children:[m.jsx(LA,{type:"checkbox",checked:g,disabled:b,onChange:O=>this.setDestroyed(O.target.checked)}),m.jsx(zA,{children:"Destroy"})]})]})}),m.jsx(TC,{ship:r,kind:battleDamage.KIND_SYSTEM,reference:s.id,rows:y,editable:!0,onChange:()=>this.refresh()})]})}}const kC=D.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,RC=D.div`
    display: flex;
    flex-wrap: wrap;
`,ys=D.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${o=>o.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    mix-blend-mode: ${o=>o.$blend||"normal"};
    ${Yi}

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;class qA extends Je.Component{constructor(r){super(r)}online(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onOnlineClicked(s,d),webglScene.customEvent("CloseSystemInfo")}offline(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Jg(s,d)&&(shipManager.power.onOfflineClicked(s,d),webglScene.customEvent("CloseSystemInfo"))}allOnline(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onlineAll(s,d),webglScene.customEvent("CloseSystemInfo")}allOffline(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Jg(s,d)&&(shipManager.power.offlineAll(s,d),webglScene.customEvent("CloseSystemInfo"))}overload(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onOverloadClicked(s,d),webglScene.customEvent("CloseSystemInfo")}stopOverload(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onStopOverloadClicked(s,d),webglScene.customEvent("CloseSystemInfo")}boost(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.clickPlus(s,d)}deboost(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.clickMinus(s,d)}addShots(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Jx(s,d)&&weaponManager.changeShots(s,d,1)}reduceShots(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Zx(s,d)&&weaponManager.changeShots(s,d,-1)}removeFireOrderMulti(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Zg(s,d)&&weaponManager.removeFiringOrderMulti(s,d)}removeFireOrder(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;vp(s,d)&&(weaponManager.removeFiringOrder(s,d),webglScene.customEvent("CloseSystemInfo"))}removeFireOrderAll(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;vp(s,d)&&(weaponManager.removeFiringOrderAll(s,d),webglScene.customEvent("CloseSystemInfo"))}allChangeFiringMode(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;if(fd(s,d)){weaponManager.onModeClicked(s,d);var g=d.firingMode,b=[];s.flight?b=s.systems.map(j=>j.systems).reduce((j,H)=>j.concat(H),[]).filter(j=>j.weapon):b=s.systems.filter(j=>j.weapon);for(var S=weaponManager.stripPairingSuffix(d.displayName),y=new Array,E=0;E<b.length;E++)S===weaponManager.stripPairingSuffix(b[E].displayName)&&d.weapon&&y.push(b[E]);for(var E=0;E<y.length;E++){var O=y[E];if(O.firingMode!=g&&fd(s,O)){for(var $=O.firingMode,P=0;O.firingMode!=g&&P<2;)weaponManager.onModeClicked(s,O),O.firingMode==1&&P++;if(O.firingMode!=g)for(;O.firingMode!=$;)weaponManager.onModeClicked(s,O)}}}}changeFiringMode(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;fd(s,d)&&weaponManager.onModeClicked(s,d)}selectAllWeapons(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;weaponManager.selectAllWeapons(s,d,"forceSelect"),webglScene.customEvent("CloseSystemInfo")}deselectAllWeapons(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;weaponManager.selectAllWeapons(s,d,"forceDeselect"),webglScene.customEvent("CloseSystemInfo")}declareSelfIntercept(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;if(em(s,d)){if(weaponManager.onDeclareSelfInterceptSingle(s,d),d.canSplitShots)var g=d.checkFinished();g&&webglScene.customEvent("CloseSystemInfo")}}declareSelfInterceptAll(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(s,d),d.canSplitShots)var g=d.checkFinished();g&&webglScene.customEvent("CloseSystemInfo")}remSelfIntercept(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;tm(s,d)&&weaponManager.removeSelfInterceptSingle(s,d)}nextCurrClass(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;d.nextCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}prevCurrClass(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;d.prevCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{ship:r,selectedShip:s,system:d}=this.props;return Xx(r,d)?gamedata.gamephase===-2?m.jsx(kC,{children:m.jsx(QA,{ship:r,system:d})}):m.jsxs(kC,{children:[PC(r,d)&&m.jsx(V$,{ship:r,system:d}),m.jsxs(RC,{children:[Jx(r,d)&&m.jsx(ys,{title:"More shots",onClick:this.addShots.bind(this),img:"./img/plussquare.png"}),Zx(r,d)&&m.jsx(ys,{title:"Less shots",onClick:this.reduceShots.bind(this),img:"./img/minussquare.png"})]}),zC(r,d)&&m.jsxs(TO,{ship:r,system:d,showModes:!!fd(r,d),children:[em(r,d)&&m.jsx(ys,{title:"Allow interception (RMB = All systems selected)",onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:"./img/addSelfIntercept.png"}),tm(r,d)&&m.jsx(ys,{title:"Remove an intercept order",onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:"./img/remSelfIntercept.png"}),Zg(r,d)&&m.jsx(ys,{title:"Remove last fire order",onClick:this.removeFireOrderMulti.bind(this),img:"./img/unfiringSmall.png"}),vp(r,d)&&m.jsx(ys,{title:"Remove all fire orders (RMB = All weapons selected)",onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:"./img/firing.png"})]}),m.jsxs(RC,{children:[Nx(r,d)&&m.jsx(ys,{title:"Select all weapons of this type",onClick:this.selectAllWeapons.bind(this),img:"./img/selectAllWeapons.png",$blend:"screen"}),Nx(r,d)&&m.jsx(ys,{title:"Deselect all weapons of this type",onClick:this.deselectAllWeapons.bind(this),img:"./img/deselectAllWeapons.png",$blend:"screen"})]}),t0(r,d)&&m.jsx(I$,{ship:r,system:d}),Px(r,d)&&m.jsx(a$,{ship:r,system:d}),Hx(r,d)&&m.jsx(h$,{system:d,ship:r}),Vx(r,d)&&m.jsx(S$,{system:d,ship:r}),Ux(r,d)&&m.jsx(eA,{system:d,ship:r}),Bx(r,d)&&m.jsx(sA,{system:d,ship:r}),Fx(r,d)&&m.jsx(dA,{system:d,ship:r}),Ix(r,d)&&m.jsx(xA,{system:d,ship:r}),(Wx(r,d)||Yx(r,d))&&m.jsx(oC,{system:d,ship:r}),(Gx(r,d)||Kx(r,d))&&m.jsx(oC,{system:d,ship:r}),Xg(r,d)&&m.jsx(PO,{ship:r,system:d,readOnly:!XA(r,d)}),Qx(r,d)&&m.jsx(qO,{ship:r,system:d,readOnly:!JA(r,d)}),"   ",nm(r,d)&&m.jsx($$,{ship:r,system:d}),NC(r,d)&&m.jsx(L$,{ship:r,system:d})]}):null}}const Nx=(o,r)=>!(!window.matchMedia("(pointer: coarse)").matches||!r.weapon||mp(o,r)||gamedata.gamephase!=3&&!r.ballistic&&!r.preFires||gamedata.gamephase!=1&&r.ballistic||gamedata.gamephase!=5&&r.preFires),Px=(o,r)=>gamedata.gamephase===1&&r.name=="adaptiveArmorController",Fx=(o,r)=>r.name==="GraviticAugmenter"&&gamedata.isMyShip(o)&&!r.stowed&&!shipManager.power.isOffline(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(o,r)||gamedata.gamephase===5),Ix=(o,r)=>r.name==="MinorThoughtPulsar"&&gamedata.gamephase===3&&gamedata.isMyShip(o)&&!r.stowed&&!shipManager.systems.isDestroyed(o,r)&&!shipManager.power.isOffline(o,r),Ux=(o,r)=>gamedata.gamephase===-1&&o.mine&&(o.spawned==-1&&gamedata.turn==1||o.spawned==gamedata.turn-1)&&(r.name=="CaptorMine"||r.name=="MineControllerDEW"),Bx=(o,r)=>gamedata.gamephase===-1&&o.mine&&(o.spawned==-1&&gamedata.turn==1||o.spawned==gamedata.turn-1)&&r.name=="ProximityMine",Hx=(o,r)=>gamedata.gamephase===1&&r.name=="hyachComputer",Vx=(o,r)=>r.name==="hyachSpecialists",Wx=(o,r)=>gamedata.gamephase===1&&r.name==="ThirdspaceShield",Yx=(o,r)=>gamedata.gamephase===1&&r.name==="ThirdspaceShieldGenerator",Gx=(o,r)=>gamedata.gamephase===1&&r.name==="ThoughtShield",Kx=(o,r)=>gamedata.gamephase===1&&r.name==="ThoughtShieldGenerator",Xg=(o,r)=>gamedata.isMyShip(o)&&r.name=="SelfRepair",XA=(o,r)=>Xg(o,r)&&gamedata.gamephase===1,Qx=(o,r)=>gamedata.isMyShip(o)&&(r.name=="StructureSelfRepair"||r.name=="CoopStructureSelfRepair"),JA=(o,r)=>Qx(o,r)&&gamedata.gamephase===1,DC=()=>typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted(),Lu=(o,r)=>gamedata.gamephase===-2&&!DC()&&o&&o.userid!=0&&!o.flight&&!o.mine&&!ZA(r),MC=(o,r)=>Lu(o,r)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(o,r)&&systemEnhancements.offersFor(o,r).length>0,qx=o=>gamedata.gamephase===-2&&!DC()&&o&&o.userid!=0&&!!o.mine&&battleDamage.mineMaxHealth(o)>1,ZA=o=>!o||!(o.maxhealth>0)||o.isTargetable===!1||!!o.hideInShipWindow||Array.isArray(o.systems),Xx=(o,r)=>gamedata.gamephase===-2?Lu(o,r)||MC(o,r):Jg(o,r)||OC(o,r)||$C(o,r)||AC(o,r)||jC(o,r)||_C(o,r)||Jx(o,r)||Zx(o,r)||Zg(o,r)||vp(o,r)||fd(o,r)||em(o,r)||tm(o,r)||Px(o,r)||Hx(o,r)||Vx(o,r)||Wx(o,r)||Gx(o,r)||Yx(o,r)||Kx(o,r)||Xg(o,r)||ej(o,r)||tj(o,r)||nm(o,r)||NC(o,r)||t0(o,r)||Nx(o,r)||Ux(o,r)||Bx(o,r)||Fx(o,r)||Ix(o,r),Jg=(o,r)=>gamedata.gamephase===1&&(r.canOffLine||r.powerReq>0)&&!r.powerLocked&&!shipManager.power.isOffline(o,r)&&!shipManager.power.getBoost(r)&&!weaponManager.hasFiringOrder(o,r),OC=(o,r)=>gamedata.gamephase===1&&shipManager.power.isOffline(o,r)&&!shipManager.power.isForcedOffline(o,r)&&!shipManager.power.isVortexLockedOffline(o,r),$C=(o,r)=>gamedata.gamephase===1&&!shipManager.power.isOffline(o,r)&&r.weapon&&r.overloadable&&!shipManager.power.isOverloading(o,r),AC=(o,r)=>gamedata.gamephase===1&&r.weapon&&r.overloadable&&shipManager.power.isOverloading(o,r)&&(r.overloadshots>=r.extraoverloadshots||r.overloadshots==0),jC=(o,r)=>r.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(o,r)&&(!r.isScanner()||r.id==shipManager.power.getHighestSensorsId(o))&&r.name!=="ThirdspaceShieldGenerator"&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor",_C=(o,r)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(r)&&r.name!=="ThirdspaceShieldGenerator"&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor",LC=(o,r)=>{const s=weaponManager.getFiringOrder(o,r);return s&&s.type!=="intercept"&&s.type!=="selfIntercept"?s:null},Jx=(o,r)=>{if(mp(o,r)||!r.weapon||!r.canChangeShots||!weaponManager.hasFiringOrder(o,r))return!1;const s=LC(o,r);return!!s&&s.shots<r.maxVariableShots},Zx=(o,r)=>{if(mp(o,r)||!r.weapon||!r.canChangeShots||!weaponManager.hasFiringOrder(o,r))return!1;const s=LC(o,r);return!!s&&s.shots>1},mp=(o,r)=>r.name==="jumpEngine"&&typeof r.getHeldVortex=="function"&&!!r.getHeldVortex(),Zg=(o,r)=>r.weapon&&weaponManager.hasOrderForMode(r)&&r.canSplitShots&&!mp(o,r),vp=(o,r)=>r.weapon&&weaponManager.hasFiringOrder(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked())&&!mp(o,r),e0=o=>!!(window.shipManager&&shipManager.isDockingRider(o)),fd=(o,r)=>r.weapon&&!o.mine&&!r.stowed&&!r.hideFiringModeSelector&&!e0(o)&&r.name!=="GraviticAugmenter"&&r.name!=="MinorThoughtPulsar"&&(gamedata.gamephase===1&&r.ballistic||gamedata.gamephase===5&&r.preFires||gamedata.gamephase===3&&!r.ballistic&&!r.preFires)&&(!weaponManager.hasFiringOrder(o,r)||r.multiModeSplit)&&Object.keys(r.firingModes).length>1,em=(o,r)=>r.weapon&&!e0(o)&&weaponManager.canSelfInterceptSingle(o,r),tm=(o,r)=>r.weapon&&r.canSplitShots&&!e0(o)&&weaponManager.canRemInterceptSingle(o,r),zC=(o,r)=>fd(o,r)||em(o,r)||tm(o,r)||Zg(o,r)||vp(o,r),ej=(o,r)=>r.canActivate&&typeof r.canActivate=="function"&&r.canActivate()&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor"&&r.name!=="GraviticAugmenter"&&r.name!=="jumpEngine",tj=(o,r)=>r.canDeactivate&&typeof r.canDeactivate=="function"&&r.canDeactivate()&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor"&&r.name!=="GraviticAugmenter"&&r.name!=="jumpEngine",NC=(o,r)=>r.name==="jumpEngine"&&typeof r.canMaintainVortex=="function"&&(r.canMaintainVortex()||r.canDeactivate()||typeof r.getAbductionOrder=="function"&&!!r.getAbductionOrder()),nm=(o,r)=>r.name==="powerCapacitor"||r.name==="PowerCapacitor",PC=(o,r)=>Jg(o,r)||OC(o,r)||$C(o,r)||AC(o,r)||r.boostable&&(jC(o,r)||_C(o,r)),t0=(o,r)=>nm(o,r)||r.name==="GraviticAugmenter"||r.name==="jumpEngine"?!1:!!(r.canActivate&&typeof r.canActivate=="function"&&r.canActivate()||r.canDeactivate&&typeof r.canDeactivate=="function"&&r.canDeactivate()),nj=(o,r)=>gamedata.gamephase===-2?Lu(o,r)||MC(o,r):Px(o,r)||Hx(o,r)||Vx(o,r)||Ux(o,r)||Bx(o,r)||Fx(o,r)||Ix(o,r)||Wx(o,r)||Yx(o,r)||Gx(o,r)||Kx(o,r)||Xg(o,r)||Qx(o,r)||nm(o,r)||PC(o,r)||t0(o,r)||zC(o,r),FC=D.div`
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 7px;
    border: 2px solid black;
    box-sizing: border-box;
    
    background-color: black;

    &::before {
        content: "";
        position:absolute;
        width:  ${o=>o.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        background-color: ${o=>o.$docked?"#00ffff":o.$criticals?o.$criticalsBenign?"#00ffff":"#ed6738":"#427231"};
    }
`,IC=D.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,rj=D.div`
    position: absolute;
    top: 0px;
    left: 1px;
    z-index: 1;
    pointer-events: none;
    /*11px on a 32px icon: the 7px it launched at was legible only if you already knew to
      look for it (user report 2026-08-15). Still small enough to clear the icon art and the
      [n/n] load counter, which sits along the BOTTOM edge.*/
    font-size: 11px;
    line-height: 11px;
    color: ${V.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,UC=D.div`
    position: relative;
    box-sizing: border-box;
    width: 32px;
    height: 32px;
    margin: ${o=>o.$scs?"3px 0":"2px"};
   border: ${o=>o.$firing&&o.$calledShot?"2px solid #ff3366":o.$firing&&o.$intercepting?"1px solid #52b352":o.$firing?"1px solid #eb5c15":o.$orderPending?"2px solid #00e5ff":o.$highlight==="Yellow"?"1px solid #e1b000":o.$highlight==="Orange"?"2px solid #ff6d3c":o.$highlight==="Red"?"2px solid #ff0000":"1px solid #496791"};
     background-color:  ${o=>o.$selected?"#4e6c91":o.$firing&&o.$intercepting?"#2f7a3a":o.$firing?"#e06f01":o.$off?"#852d2d":o.$boosted?"#cca300":o.$loading&&o.$loadedAlternate?"#CD9E9E":"rgba(0, 0, 0, 0.7)"};
    box-shadow: ${o=>o.$selected?"0px 0px 15px #0099ff":o.$firing&&o.$calledShot?"0px 0px 12px #ff3366":o.$firing&&o.$intercepting?"0px 0px 15px #52b352":o.$firing?"box-shadow: 0px 0px 15px #eb5c15":o.$orderPending?"0px 0px 12px #00e5ff":"none"};
    /*$mirror (rolled ship, port/starboard drawn swapped): the icon ART is flipped
      horizontally on an ::after layer so its facing matches the drawn side, while
      text, health bar and state overlays stay unflipped and readable*/
    background-image: ${o=>o.$mirror?"none":`url(${o.$background})`};
    background-size: cover;
    ${o=>o.$mirror?"z-index: 0;":""} /*own stacking context keeps the z:-1 art layer inside this icon*/
    ${o=>o.$mirror?`
    &::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url(${o.$background});
        background-size: cover;
        transform: scaleX(-1);
        z-index: -1;
    }
    `:""}
    filter: ${o=>o.$destroyed?"blur(1px)":"none"};
    cursor: pointer;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    
    ${IC} {
        display: ${o=>o.$offline?"none":"flex"};
        /*docked Kirishiac Orbital: keep the icon text ([n/5] regeneration counter) fully
        readable above the blue fade - the ::before overlay is positioned, so the static
        text would otherwise paint underneath it*/
        ${o=>o.$docked?"position: relative; z-index: 1;":""}
    }


    &::before {
        content: "";
        position:absolute;
        width: 100%;
        height: 100%;
        opacity: ${o=>o.$destroyed||o.$offline||o.$loading||o.$docked?"0.5":"0"};

        background-color: ${o=>o.$destroyed||o.$offline?"black":o.$loading?"orange":o.$docked?"#1a4a6e":"transparent"};

        background-image: ${o=>o.$offline?"url(./img/offline.png)":"none"};
    }
`;class n0 extends Je.Component{constructor(r){super(r),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(r){if(r.stopPropagation(),r.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:s,ship:d}=this.props;s=shipManager.systems.initializeSystem(s);const g=Lu(d,s);if((gamedata.waiting||gamedata.replay)&&!g)return;const b=!!d.removed&&!shipManager.isDestroyedByDamage(d);if(!(!g&&!b&&(shipManager.isDestroyed(d)||shipManager.isDestroyed(d,s)&&!s.clickableWhenDestroyed))){if(b&&!g){gamedata.isMyShip(d)&&window.uiEvents.relay("SystemClicked",{ship:d,system:s,element:r.currentTarget,showMenu:!0});return}if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(d)){var S=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(S&&S.ship.id!=d.id&&!weaponManager.isSelectedWeapon(s)){window.uiEvents.relay("SystemTargeted",{ship:d,system:s});return}}var y=s.weapon&&typeof s.isSpentLocked=="function"&&s.isSpentLocked(),E=typeof s.canSelectForAbduction=="function"&&s.canSelectForAbduction(d);if(!y&&(s.weapon&&gamedata.gamephase===3&&!s.ballistic&&!s.preFires||gamedata.gamephase===1&&s.ballistic||gamedata.gamephase===5&&s.preFires||weaponManager.canManuallyInterceptWith(d,s)||E)&&!shipManager.isAdrift(d)&&gamedata.isMyShip(d)){if(s.hasSpecialTargeting&&typeof s.reopenSpecialTargeting=="function"&&weaponManager.hasFiringOrder(d,s)&&s.reopenSpecialTargeting(d))return;var O=weaponManager.hasFiringOrder(d,s),$=O&&O!=="self"&&!s.canSplitShots&&!s.hasSpecialTargeting;weaponManager.isSelectedWeapon(s)?weaponManager.unSelectWeapon(d,s):$||weaponManager.selectWeapon(d,s)}if(gamedata.isMyShip(d)&&(s.name==="hangar"||s.name==="catapult"||s.name==="fighterRail")){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog=="function"&&window.DeploymentDock.shipHasOpenableDockDialog(d)&&window.confirm&&typeof window.confirm.hangarDeployDock=="function"){window.confirm.hangarDeployDock(d);return}if(gamedata.gamephase===3&&!s.isShadowHangar&&!shipManager.movement.isRolling(d)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(d)!=="no")&&window.confirm&&typeof window.confirm.hangarLaunch=="function"){window.confirm.hangarLaunch(d);return}}if(gamedata.isMyShip(d)&&(s.name==="dockingCollar"||s.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(d)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(d)!=="no")&&typeof window.lcvRailLaunchable=="function"&&window.lcvRailLaunchable(d,s)&&window.confirm&&typeof window.confirm.lcvLaunch=="function"){window.confirm.lcvLaunch(d);return}gamedata.isMyShip(d)?window.uiEvents.relay("SystemClicked",{ship:d,system:s,element:r.currentTarget,showMenu:!0}):window.uiEvents.relay("SystemTargeted",{ship:d,system:s})}}onSystemMouseOver(r){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;r.stopPropagation(),r.preventDefault();let{system:s,ship:d}=this.props;s=shipManager.systems.initializeSystem(s),window.uiEvents.relay("SystemMouseOver",{ship:d,system:s,element:r.currentTarget,showInfo:!0})}onSystemMouseOut(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),r.preventDefault(),window.uiEvents.relay("SystemMouseOut"))}onTouchStart(r){r.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:g,ship:b}=this.props;g=shipManager.systems.initializeSystem(g),window.uiEvents.relay("SystemMouseOver",{ship:b,system:g,element:s,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(r){if(r.stopPropagation(),!this.longPressTimer)return;const s=r.touches[0],d=s.clientX-this.touchStartX,g=s.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(g)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(r){r.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onTouchEnd(r){r.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(r){if(r.stopPropagation(),r.preventDefault(),window.matchMedia("(pointer: coarse)").matches)return;let{system:s,ship:d}=this.props;s=shipManager.systems.initializeSystem(s),s.weapon&&weaponManager.selectAllWeapons(d,s)}render(){let{system:r,ship:s,scs:d,fighter:g,destroyed:b,mirror:S}=this.props;return r=shipManager.systems.initializeSystem(r),r=shipManager.systems.initializeSystem(r),(r0(s,r)||b)&&!r.clickableWhenDestroyed&&!Lu(s,r)?m.jsxs(UC,{$background:VC(r),$destroyed:!0,$mirror:S,children:[BC(s,r),m.jsx(FC,{$health:"0"})]}):m.jsxs(UC,{$scs:d,$highlight:vj(s,r),$destroyed:r0(s,r)||b,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:VC(r),$mirror:S,$offline:cj(s,r),$loading:sj(r),$loadedAlternate:uj(r),$selected:yj(r),$firing:ij(s,r),$intercepting:oj(s,r),$calledShot:lj(s,r),$boosted:fj(s,r),$off:dj(r),$docked:HC(r),$orderPending:hj(r),children:[BC(s,r),m.jsx(IC,{children:xj(s,r)}),(!g||WC(r))&&m.jsx(FC,{$scs:d,$health:r0(s,r)||b?0:gj(s,r),$criticals:WC(r),$criticalsBenign:mj(r),$docked:pj(r)})]})}}const BC=(o,r)=>!window.systemEnhancements||!o||!r||!systemEnhancements.hasAny(o,r.id)?null:m.jsx(rj,{title:"Carries a system enhancement",children:"✦"}),ij=(o,r)=>(weaponManager.hasFiringOrder(o,r)||aj(r))&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked()),aj=o=>gamedata.gamephase===1&&typeof o.getAbductionOrder=="function"&&!!o.getAbductionOrder(),oj=(o,r)=>weaponManager.isInterceptOnly(o,r),lj=(o,r)=>!r.weapon||!weaponManager.hasFiringOrder(o,r)?!1:weaponManager.getCalledShotInfo(o,r)!==null,sj=o=>o.weapon&&(!weaponManager.isLoaded(o)||typeof o.isSpentLocked=="function"&&o.isSpentLocked()),uj=o=>o.weapon&&weaponManager.isLoadedAlternate(o),cj=(o,r)=>shipManager.power.isOffline(o,r),dj=o=>o.activeMeansOff&&o.active,fj=(o,r)=>shipManager.power.isBoosted(o,r)||r.active&&!r.activeMeansOff&&!r.suppressActiveBoost,HC=o=>!!(o.showDockedVisual&&o.activeEffective||o.stowed&&o.stowedArcStart==null||o.dockedWithOrbital),pj=o=>HC(o)||!!o.stowed,hj=o=>!!(o.showDockedVisual&&typeof o.hasPendingDockingOrder=="function"&&o.hasPendingDockingOrder()),gj=(o,r)=>(r.name==="ThirdspaceShield"||r.name==="ThoughtShield")&&r.baseRating?Math.min(100,r.currentHealth/r.baseRating*100):(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,r0=(o,r)=>shipManager.systems.isDestroyed(o,r),VC=o=>o.name=="thruster"&&!o.iconPath?window.AssetManager.getSmartImagePath("./img/systemicons/thruster"+o.direction+".png"):o.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${o.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${o.name}.png`),WC=o=>shipManager.criticals.hasCriticalsIcon(o),mj=o=>shipManager.criticals.hasOnlyCritical(o,"HangarOperations",!0)||shipManager.criticals.hasOnlyCritical(o,"LCVLaunchedThisTurn",!0),vj=(o,r)=>shipManager.systems.hasBorderHighlight(o,r),yj=o=>weaponManager.isSelectedWeapon(o),xj=(o,r)=>{if(r.outputDisplay!==void 0&&r.outputDisplay!==null&&r.outputDisplay!="")return r.outputDisplay;if(r.weapon){if(r.stowed&&r.stowedArcStart==null)return"-";if(typeof r.isSpentLocked=="function"&&r.isSpentLocked())return"✓";if(typeof r.getVortexIconLoad=="function"){const g=r.getVortexIconLoad();if(g!=null)return g}const d=weaponManager.hasFiringOrder(o,r);if(d&&r.canChangeShots)return weaponManager.getFiringOrder(o,r).shots+"/"+r.shots;if(d){var s=weaponManager.getCalledShotInfo(o,r);if(s)return"⊕"}else if(!d){let g=weaponManager.getWeaponCurrentLoading(r),b=r.loadingtime;r.normalload>0&&(b=r.normalload),g>b&&(g=b);let S="";return r.overloadturns>0&&shipManager.power.isOverloading(o,r)&&(S="("+r.overloadturns+")"),r.overloadshots>0?"S"+r.overloadshots:g+S+"/"+b}}else{if(r.outputType==="thrust")return shipManager.movement.getRemainingEngineThrust(o);if(r.outputType==="power"){let d=shipManager.power.getReactorPower(o,r);return gamedata.gamephase>1&&d<0?0:d}else return shipManager.systems.getOutput(o,r)}},bj=D.div`
    display:flex;
    z-index: 2;
    position:fixed;
    left: 805px;
    width: calc(100% - 810px);
    bottom: 0;
    flex-wrap: wrap-reverse;

    @media (max-width: 1024px) {
        left: 0;
        width: calc(100% - 50px);
    }
`;class wj extends Je.Component{constructor(r){super(r)}getWeapons(r,s){return r.flight?r.systems.map(d=>d.systems).reduce((d,g)=>d.concat(g),[]).filter(d=>d.weapon):r.systems.filter(d=>d.weapon||d.outputType==="thrust"||d.outputType==="EW"||d.outputType==="power"||d.outputType==="settings")}render(){const{ship:r,gamePhase:s}=this.props;if(!r)return null;const d=this.getWeapons(r,s);return m.jsx(bj,{children:d.map((g,b)=>m.jsx(n0,{fighter:r.flight,system:g,ship:r},`system-${b}`))})}}const YC=o=>{if(!o.hitChart)return[];const r=["Primary","Front","Aft","Port","Starboard"];let s=5;o.base&&!o.smallBase?(r[1]="Sections",s=2):o.SixSidedShip&&(r[31]="Port Front",r[32]="Port Aft",r[41]="Starboard Front",r[42]="Starboard Aft",s=43);const d=[];for(let g=0;g<s;g++){if(o.hitChart[g]===void 0)continue;const b=[];let S=0;for(const y in o.hitChart[g]){const E=Math.floor((y-S)/20*100);S=y;let O=o.hitChart[g][y];const $=O.indexOf(":");$>0&&(O=O.substring($+1)),b.push({name:O,chance:E})}d.push({location:g,name:r[g],entries:b})}return d},Sj=D.div`
    ${o=>o.$tightBottom?"& > *:last-child { display: none; }":""}
    ${o=>o.$compactText?`
    ${Bt} {
        font-size: 10px;
        line-height: 1.4;
        color: ${V.colors.textAccent};
    }
    ${Yn} {
        font-size: 10px;
        font-style: normal;
        color: ${V.colors.text};
    }`:""}
`;class GC extends Je.Component{render(){const{ship:r,hideHitChart:s,tightBottom:d,compactText:g}=this.props,b=!!r.mine||window.gamedata&&typeof gamedata.isTerrain=="function"&&gamedata.isTerrain(r.shipSizeClass,r.userid),S=!!r.flight||b;var y=new Array,E=new Array,O=new Array;r.notes&&(y=r.notes.split("<br>")),r.hitChart&&!s&&YC(r).forEach(function(Q){E[Q.name]=Q.entries.map(function(xe){return xe.name+" "+xe.chance+"%"}).join(", ")}),r.enhancementTooltip!=""&&(O=r.enhancementTooltip.split("<br>"));let $={};if(!r.flight&&r.hasAttached&&Object.keys(r.hasAttached).length>0){const Q={1:"Forward",2:"Aft",3:"Port",31:"Port-Forward",32:"Port-Aft",4:"Starboard",41:"Starboard-Forward",42:"Starboard-Aft"};for(let xe in r.hasAttached){let ze=r.hasAttached[xe],fe=Q[ze]||"Unknown";$[fe]||($[fe]=0),$[fe]++}}let P=r.offensivebonus;r.flight&&gamedata.areMinesPresent&&(r.minesweeper?P-=window.ew.getDetectMEW(r):P-=window.ew.getDetectMEW(r)*2);var j=0,H=!0;if(r.mine){var L=shipManager.systems.getSystemByName(r,"mineStealth");L&&!L.isMineRevealed(r)&&(H=!1,E=new Array,y=["No details known, scan with OEW to identify."],O=new Array)}return m.jsxs(Sj,{$tightBottom:d,$compactText:g,children:[r.flight&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Offensive bonus: "}),P*5]},j++),r.flight&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Armor (F/S/A): "}),shipManager.systems.getFlightArmour(r)]},j++),r.flight&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Profile - Front/Side: "}),r.forwardDefense*5,"/",r.sideDefense*5]},j++),r.flight&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Initiative: "}),r.iniativebonus]},j++),r.flight&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Thrust: "}),r.freethrust]},j++),r.flight&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Turn Cost: "}),r.turncost]},j++),r.flight&&H&&r.turndelay&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Turn Delay: "}),r.turndelaycost]},j++),r.flight&&H&&m.jsx(Bt,{children:" "},j++),Object.keys(y).length>0&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"NOTES:"})," "]},j++),Object.keys(y).length>0&&Object.keys(y).map(Q=>m.jsx(Bt,{children:y[Q]},j++)),Object.keys(y).length>0&&m.jsx(Bt,{children:" "},j++),Object.keys(E).length>0&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"HIT CHART:"})," "]},j++),Object.keys(E).length>0&&Object.keys(E).map(Q=>m.jsxs(Bt,{children:[m.jsxs(Yn,{children:[Q,": "]}),E[Q]]},j++)),Object.keys(E).length>0&&m.jsx(Bt,{children:" "},j++),Object.keys($).length>0&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"UNITS ATTACHED:"})," "]},j++),Object.keys($).length>0&&Object.keys($).map(Q=>m.jsxs(Bt,{children:[m.jsxs(Yn,{children:[Q,": "]}),$[Q]]},j++)),Object.keys($).length>0&&m.jsx(Bt,{children:" "},j++),S&&r.enhancementTooltip!=""&&H&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"ENHANCEMENTS:"})," "]},j++),S&&r.enhancementTooltip!=""&&H&&Object.keys(O).map(Q=>m.jsx(Bt,{children:O[Q]},j++)),S&&r.enhancementTooltip!=""&&H&&m.jsx(Bt,{children:" "},j++)]})}}const pd=D(Tx)`
    /*font-size: 12px;*/
	font-size: 13px;
`,KC=D(Ex)`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.position).reduce((r,s)=>r+`
`+s+":"+o.position[s]+"px;","")}
    width: ${o=>o.ship?"320px":"220px"};
    text-align: left;
    opacity:0.8;
`,QC=D.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,Cj={MissileLost:"A missile was lost to damage"},qC={DamageReductionReduced:o=>`Damage reduction reduced by ${o}`},Bt=D(Au)`
    text-align: left;
    /*color: #5e85bc;*/
	color: #BDEAFA; /*replace dark blue above with bluish white, more eyes friendly*/
    font-family: arial;
    /*font-size: 11px;*/
	font-size: 12px;
`,Yn=D.span`
    color: white;
	font-style:italic;
	font-size: 11px;
`,Ej=D.span`
    color: #C6E2FF;
`;class Tj extends Je.Component{render(){const{ship:r,selectedShip:s,system:d,boundingBox:g}=this.props;if(d instanceof Ship||d===r){var b=r.shipClass,S=r.name;if(d.flight&&(b=d.systems[1].displayName),r.mine){var y=shipManager.systems.getSystemByName(r,"mineStealth");y&&!y.isMineRevealed(r)&&(b="Mine",S="Mine")}return m.jsxs(KC,{ship:!0,position:JC(g),children:[m.jsxs(pd,{children:[m.jsx(Ej,{children:S})," - ",b]}),m.jsx(GC,{ship:r})]})}var E=new Array;d.data.Special&&d.data.Special!=""&&(E=d.data.Special.split("<br>"));var O="Special",$=0;let P=r.offensivebonus;r.flight&&gamedata.areMinesPresent&&(r.minesweeper?P-=window.ew.getDetectMEW(r):P-=window.ew.getDetectMEW(r)*2);var j=d.displayName,H=d.firingModes?d.firingModes[d.firingMode]:null,L=null;d.name==="ShadowFighterBomb"&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool=="function"&&(L=weaponManager.shadowFighterBombPool(r,d,!0));var Q=null;if(d.outputType==="power"&&window.shipManager&&shipManager.power&&typeof shipManager.power.getDockedPowerSummary=="function"){var xe=shipManager.power.getDockedPowerSummary(r);xe.donors>0&&(Q=xe)}var ze=null;if(typeof d.getAbductionOrder=="function"){var fe=d.getAbductionOrder();if(fe){var ae=gamedata.getShip(fe.targetid);ze=ae?ae.name:"Unit "+fe.targetid}}let ce=!1;if(r.mine){var y=shipManager.systems.getSystemByName(r,"mineStealth");y&&!y.isMineRevealed(r)&&(ce=!0,j="Mine",E=["No details known, scan with OEW to identify."])}return m.jsxs(KC,{position:JC(g),children:[m.jsx(pd,{children:j}),!r.flight&&!ce&&bl("Structure",d.maxhealth-damageManager.getDamage(r,d)+"/"+d.maxhealth),!r.flight&&!ce&&bl("Armor",shipManager.systems.getArmour(r,d)),r.flight&&!ce&&bl("Offensive bonus",Dj(d,P*5)),d.firingModes&&!ce&&bl("Firing mode",H),d.missileArray&&Object.keys(d.missileArray).length>0&&!ce&&bl("Ammo Amount",d.missileArray[d.firingMode].amount),!ce&&Object.keys(d.data).map((Ee,le)=>Ee!=O&&!(Ee==="Ammunition"&&(d.name==="GrapplingClaw"||d.name==="Marines"))&&bl(Ee,Mj(d,Ee),"data"+le)),L!==null&&bl("Fighters available",L),Q&&bl("Shared by docked ships","+"+Q.shared+" of "+Q.surplus+" pooled from "+Q.donors+(Q.donors===1?" ship":" ships")),ze!==null&&bl("Abduction target",ze),Object.keys(E).length>0&&m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Special: "})," "]},`special-${$++}`),Object.keys(E).length>0&&Object.keys(E).map(Ee=>m.jsx(Bt,{children:E[Ee]},`special-${$++}`)),(Object.keys(d.critData).length>0||d.criticals&&d.criticals.length>0)&&!ce&&Rj(d),!gamedata.isMyShip(r)&&!ce&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&s&&XC(r,s,d),gamedata.isMyShip(r)&&!ce&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&s&&XC(r,s,d),gamedata.isMyShip(r)&&!ce&&d.weapon&&weaponManager.hasFiringOrder(r,d)&&kj(r,d)]})}}const XC=(o,r,s)=>weaponManager.canCalledshot(o,s,r)?[m.jsx(pd,{children:"Called shot"},"calledHeader")].concat(gamedata.selectedSystems.map((d,g)=>{if(weaponManager.isOnWeaponArc(r,o,d))if(weaponManager.checkIsInRange(r,o,d)){var b=d.firingMode;return b=d.firingModes[b],s.id!=null&&!weaponManager.canWeaponCall(d)?m.jsxs(Bt,{children:[m.jsx(Yn,{children:d.displayName}),": Cannot Called Shot"]},`called-${g}`):m.jsxs(Bt,{children:[m.jsx(Yn,{children:d.displayName})," - Approx:  ",weaponManager.calculateHitChange(r,o,d,s.id).hitChance,"%"]},`called-${g}`)}else return m.jsxs(Bt,{children:[m.jsx(Yn,{children:d.displayName}),": Not in Range"]},`called-${g}`);else return m.jsxs(Bt,{children:[m.jsx(Yn,{children:d.displayName}),": Not in Arc"]},`called-${g}`)})):[m.jsx(pd,{children:"Called shot"},"calledHeader")].concat(m.jsx(Bt,{children:"Cannot Target"},"cannotTarget")),kj=(o,r)=>{var s=weaponManager.getCalledShotInfo(o,r);return s?[m.jsx(QC,{},"calledShotDivider"),m.jsx(pd,{children:"Called Shot"},"calledShotHeader"),m.jsxs(Bt,{children:[m.jsx(Yn,{children:"Target: "}),s.targetSystem.displayName," (Id: ",s.targetSystem.id,") on ",s.targetShip.name]},"calledShotTarget")]:null},Rj=o=>{const r=Object.keys(o.critData).length>0?Object.keys(o.critData):[...new Set((o.criticals||[]).map(s=>s.phpclass))];return r.length===0?null:[m.jsx(QC,{},"critDivider"),m.jsx(pd,{children:"Criticals"},"criticalHeader")].concat(r.map(s=>{let d=0,g=0;var b=0,S=0,y=!1,E="";b=0,S=0,y=!1,E="";for(const $ in o.criticals)o.criticals[$].phpclass==s&&o.criticals[$].turn<=gamedata.turn&&(o.criticals[$].turnend==0||o.criticals[$].turnend>=gamedata.turn)&&(d++,g+=parseInt(o.criticals[$].param,10)||0,d==1&&(b=o.criticals[$].turnend,S=o.criticals[$].turnend,y=o.criticals[$].turnend==0),o.criticals[$].turnend>0?(o.criticals[$].turnend>S&&(S=o.criticals[$].turnend),(o.criticals[$].turnend<b||b==0)&&(b=o.criticals[$].turnend)):y=!0);if(b>0&&(E=" (until end of turn "+b,y?E=E+"+":S>b&&(E=E+"-"+S),E=E+")"),d>=1&&qC[s]){const $=qC[s](g);return m.jsxs(Bt,{children:[$," ",E]},`critical-${s}`)}const O=o.critData[s]||Cj[s]||s;return d>1?m.jsxs(Bt,{children:["(",d," x) ",O," ",E]},`critical-${s}`):d==1?m.jsxs(Bt,{children:[O," ",E]},`critical-${s}`):null}))},Dj=(o,r)=>typeof o.adjustOffensiveBonusDisplay=="function"?o.adjustOffensiveBonusDisplay(r):r,Mj=(o,r)=>typeof o.adjustDataValueDisplay=="function"?o.adjustDataValueDisplay(r,o.data[r]):o.data[r],bl=(o,r,s)=>{if(typeof r=="string"&&r.indexOf("<br>")!==-1){const d=r.split("<br>");return m.jsxs(Bt,{children:[m.jsxs(Yn,{children:[o,": "]}),d.map((g,b)=>m.jsxs(Je.Fragment,{children:[b>0&&m.jsx("br",{}),g]},b))]},s)}return m.jsxs(Bt,{children:[m.jsxs(Yn,{children:[o,": "]}),r]},s)},JC=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left+o.width,r};D(Tx)`
    font-size: 12px;
`;const Oj=D(Ex)`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.position).reduce((r,s)=>r+`
`+s+":"+o.position[s]+"px;","")}
    max-width: 500px;
    text-align: left;
    opacity: ${o=>o.opacity||.8};
    border: 1px solid #496791;
    padding-bottom: 3px;
    /*the lobby mounts #systemInfoReact inside a pointer-events: none fixed overlay
      (same as #shipWindowsReact) - this menu is interactive, so it must opt back in.
      No-op in game.php, where the mount point has no pointer-events override.*/
    pointer-events: auto;
`;D(Au)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,D.span`
    color: white;
`;class $j extends Je.Component{render(){const{ship:r,system:s,boundingBox:d}=this.props;return Xx(r,s)?m.jsx(Oj,{position:Aj(d),opacity:nj(r,s)?.95:.8,children:m.jsx(qA,{...this.props})}):null}}const Aj=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r},jj={0:"Primary",1:"Forward",2:"Aft",3:"Port",4:"Starboard",5:"",31:"Port Fwd",32:"Port Aft",41:"Stbd Fwd",42:"Stbd Aft"},_j=D.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${V.colors.panelBgGlass};
    ${o=>o.$area?`grid-area: ${o.$area};`:""}
    ${o=>o.$valign?`align-self: ${o.$valign};`:""}
    ${o=>o.$justify?`justify-self: ${o.$justify};`:""}
    width: ${o=>o.$isTerrain?"125px":o.$wide?"156px":"128px"};
    ${o=>o.$minHeight?`min-height: ${o.$minHeight}px;`:""}
    /*Ship Art toggle (item 3, 2026-07-22): hide the whole section (header + icons) but
      keep its grid footprint so the window/watermark never resize while the art shows*/
    ${o=>o.$hidden?"visibility: hidden;":""}
    margin: ${o=>o.$area?"0":"2px"};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;

    border: ${o=>{switch(o.$location){case 0:return`1px solid ${V.colors.line}`;default:return`1px dotted ${V.colors.line}`}}};
`,Lj=D.div`
    position: relative;
    height: 15px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
    padding: 0 4px;
    box-sizing: border-box;
    background-color: black;
    border-bottom: 1px solid ${V.colors.healthOk};
    overflow: hidden;
    /*lobby pre-battle damage: the bar is the only way to reach a section's Structure,
      which has no icon of its own in the grid*/
    ${o=>o.$damageable?"cursor: pointer;":""}

    /*structure health fill - the header line doubles as the section's health bar*/
    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: ${o=>o.$health}%;
        background-color: ${o=>o.$criticals?V.colors.healthCrit:V.colors.healthOk};
    }
`,zj=D.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${V.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
`,Nj=D.span`
    position: relative;
    z-index: 1;
    font-family: ${V.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${o=>o.$destroyed?"transparent":V.colors.text};
    filter: ${o=>o.$destroyed?"blur(1px)":"none"};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,Pj=D.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;class ZC extends Je.Component{constructor(r){super(r),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(r){const{ship:s,systems:d}=this.props,g=i0(d);if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(qx(s)){r.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("MineDamageClicked",{ship:s,element:r.currentTarget});return}!g||!Lu(s,g)||(r.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:s,system:g,element:r.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.hideStructureArc()}showStructureArc(){const{ship:r,systems:s}=this.props;this.arcShown=!0,window.uiEvents.relay("StructureMouseOver",{ship:r,structure:i0(s)})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay("StructureMouseOut"))}onStructureMouseOver(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),this.showStructureArc())}onStructureMouseOut(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(r){r.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.touches[0];this.touchStartX=s.clientX,this.touchStartY=s.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(r){if(r.stopPropagation(),!this.longPressTimer)return;const s=r.touches[0];(Math.abs(s.clientX-this.touchStartX)>10||Math.abs(s.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(r){r.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(r){r.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,this.hideStructureArc()}render(){const{ship:r,systems:s,location:d,displayLocation:g,area:b,valign:S,justify:y,wide:E,isTerrain:O,minHeight:$,nameOverride:P,hidden:j}=this.props,H=i0(s),L=qx(r),Q=L?battleDamage.mineHealth(r,1):0,xe=L?battleDamage.mineMaxHealth(r):0,ze=L?Q/xe*100:H?Fj(r,H):0,fe=g!==void 0?g:d,ae=g!==void 0&&g!==d;return m.jsxs(_j,{$location:d,$area:b,$valign:S,$justify:y,$wide:E,$isTerrain:O,$minHeight:$,$hidden:j,children:[H&&m.jsxs(Lj,{$health:ze,$criticals:Ij(H),$damageable:Lu(r,H)||qx(r),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[m.jsx(zj,{children:P||jj[d]||""}),m.jsxs(Nj,{$destroyed:ze===0,children:[L?Q:H.maxhealth-damageManager.getDamage(r,H),"/",L?xe:H.maxhealth," A",shipManager.systems.getArmour(r,H)]})]}),m.jsx(Pj,{children:Uj(s,fe,E).map(ce=>m.jsx(n0,{scs:!0,mirror:ae,system:ce,ship:r},`system-scs-${d}-${r.id}-${ce.id}`))})]})}}const Fj=(o,r)=>(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,Ij=o=>shipManager.criticals.hasCriticals(o),eE=o=>o.name==="structure",i0=o=>o.find(eE),rm=o=>o.filter(r=>!eE(r)),Uj=(o,r,s)=>(o=rm(o),s?a0(o):[4,41,42].includes(r)?o0(o):[3,31,32].includes(r)?Bj(o0(o)):[1,2,0].includes(r)?a0(o):Hj(o)),Bj=o=>{let r=[];return o.forEach((s,d)=>{const g=d%3;g===0?r[d+2]=s:g===1?r[d]=s:r[d-2]=s}),r},Hj=o=>(o=rm(o),o.length===3?o0(o):o.length===4?a0(o):o),a0=o=>{o=rm(o);let r=[];for(;;){const{picked:s,remaining:d}=s0(o,4);if(s.length===0)break;o=d,r=r.concat(s)}for(;;){const{picked:s,remaining:d}=s0(o,2);if(s.length===0)break;o=d;const g=s0(o,2);g.picked.length>0?(o=g.remaining,r=r.concat([s[0],g.picked[0],g.picked[1],s[1]])):(r=r.concat([s[0],o.shift(),o.shift(),s[1]]),r=r.filter(b=>b))}return r=r.concat(o),r},o0=o=>{o=rm(o);let r=[];for(;;){const{picked:s,remaining:d}=l0(o,3);if(s.length===0)break;o=d,r=r.concat(s)}for(;;){const{picked:s,remaining:d}=l0(o,2);if(s.length===0)break;const{three:g,remainingSystems:b}=Vj(s,d);o=b,r=r.concat(g)}return r=r.concat(o),r},Vj=(o,r)=>{const s=l0(r,1);return s.picked.length===1?{three:[s.picked[0],o[0],o[1]],remainingSystems:s.remaining}:r.length>0?{three:[r.shift(),o[0],o[1]],remainingSystems:r}:{three:[o[0],o[1]],remainingSystems:r}},l0=(o,r=3)=>{const s=o.find(b=>{const S=o.reduce((y,E)=>E.name===b.name?y+1:y,0);return r===1?S===r:S>=r});if(!s)return{picked:[],remaining:o};let d=[];const g=o.filter(b=>b.name===s.name&&r>0?(r--,d.push(b),!1):!0);return{picked:d,remaining:g}},s0=(o,r=3)=>{const s=o.find(O=>{const $=o.reduce((P,j)=>j.name===O.name?P+1:P,0);return r===1?$===r:$>=r});if(!s)return{picked:[],remaining:o};let d=[],g=[];const b=o.filter(O=>O.name===s.name?(g.push(O),!1):!0);for(var S=Math.ceil(r/2),y=Math.floor(r/2),E=0;E<g.length;E++)E<S||E>=g.length-y?d.push(g[E]):b.unshift(g[E]);return{picked:d,remaining:b}},tE={DEW:"#aecdea",CCEW:V.colors.text,SDEW:"#9ac1e5",OEW:"#acd7a8",BDEW:"#8ac785","Detect Mines":"#bfa3db","Detect Stealth":"#ccb6e2",DIST:"#e6b98f",SOEW:V.colors.text,OEW_HOSTILE:"#e49b9b","Saved EW":"#e0d39a"},xs=(o,r)=>o==="OEW"&&r&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(r)?tE.OEW_HOSTILE:tE[o]||V.colors.textAccent,nE=D.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${o=>o.$flight?"":ud`
        grid-area: ew;
        justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    `}
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${V.colors.panelBgGlass};
    border: 1px solid ${V.colors.line};
    padding: 1px 4px 1px;
`,rE=D.div`
    /*flex-centred fixed-height bar so the title sits dead-centre, consistent with every
      other chrome title/header bar (user request 2026-07-22)*/
    display: flex;
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${V.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${V.colors.line};
`,bs=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${V.colors.text};
    /*ONE row pitch for the whole panel - ship rows and $target rows alike (user 2026-07-30).
      It was 1px, which read as too tight between DEW and CCEW. The cause was not those rows
      (they are the same component with the same props as every other ship row) but the panel
      top: EwTitle's 2px bottom margin plus this padding gave DEW 3px of air above and 1px
      below, so the eye took the 3px as the intended rhythm and the 1px as a mistake. Matching
      the two settles it. At 1px the visible separation was mostly the fonts' own half-leading
      - 10px Consolas values in a 9px row - rather than anything deliberate.

      The first row still clears the title by 5px (2px title margin + 3px here) against 3px
      between rows; that extra is wanted, since a header rule reads better with more clearance
      than the rows it heads.*/
    padding-top: 2px;
    /*$target rows carry a wrappable ship name (see RowTarget), so they need more air than
      the single-line ship rows: without it a name's second line sits as close to the NEXT
      row's label as to its own first line, and the eye groups it with the wrong row.

      They also swap baseline alignment for centre: a target row's two children are the
      TargetMain block (label + name, internally baseline-aligned) and the value, so
      centring floats the value to the vertical middle of however many lines the name
      took - level with the single line of a short name, midway between the two lines of
      a wrapped one, with no line-counting needed. Ship rows KEEP baseline: their 8px
      label and 10px value never wrap, and centring them would shift the value off the
      label's baseline for no gain.*/
    ${o=>o.$target&&ud`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${o=>o.$hoverable&&ud`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,Wj=D.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,ws=D.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${o=>o.$color||V.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,Ss=D.span`
    font-family: ${V.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,Yj=D.span`
    flex: 1 1 auto;
    min-width: 0;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    overflow: hidden;
    text-overflow: ellipsis;
    overflow-wrap: break-word; /*a single over-long token breaks instead of overflowing the column*/
    line-height: 1.2; /*tight, so the second line costs as little panel height as possible*/
    text-align: center;
    color: ${V.colors.textAccent};
    ${o=>o.$interactive&&ud`
        cursor: pointer;
        &:hover {
            color: ${V.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;class iE extends Je.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(r,s){window.webglScene&&(window.uiEvents.relay("EwRangeHover",{shipId:this.props.ship.id,type:r,active:s}),this.activeRangeOverlay=s?r:null)}onTargetClick(r,s){s.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(r)||window.uiEvents.relay("ScrollToShip",{shipId:r.id}))}setTargetHighlight(r,s,d){window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:r.id,type:s,active:d}),this.activeHighlight=d?{targetId:r.id,type:s}:null)}render(){const{ship:r,flight:s}=this.props;return s?m.jsxs(nE,{$flight:!0,children:[m.jsx(rE,{children:"Electronic Warfare"}),Gj(r),oE(r,this)]}):m.jsxs(nE,{children:[m.jsx(rE,{children:"Electronic Warfare"}),Kj(r,this),oE(r,this)]})}}const Gj=o=>{const r=[m.jsxs(bs,{children:[m.jsx(ws,{$color:xs("DEW"),children:"DEW"}),m.jsx(Ss,{children:ki(ew.getFlightDEW(o))})]},`dew-scs-${o.id}`)],s=aE(o);return s&&r.push(s),r},aE=o=>{if(!gamedata.isPlayerInGame()||!gamedata.isMyorMyTeamShip(o))return null;const r=ew.getSavedEwAllowance(o);if(r<=0)return null;const d=ew.isLateEwWindowOpen(o)||ew.isLateEwPhase()&&gamedata.isMyShip(o)?`${ki(ew.getLateEwRemaining(o))} / ${ki(r)}`:ki(r);return m.jsxs(bs,{children:[m.jsx(ws,{$color:xs("Saved EW"),children:"Saved EW"}),m.jsx(Ss,{children:d})]},`savedew-scs-${o.id}`)},Kj=(o,r)=>{let s=[];const d=!!window.webglScene,g=$=>d?{$hoverable:!0,onMouseEnter:()=>r.setRangeOverlay($,!0),onMouseLeave:()=>r.setRangeOverlay($,!1)}:{};s.push(m.jsxs(bs,{children:[m.jsx(ws,{$color:xs("DEW"),children:"DEW"}),m.jsx(Ss,{children:ki(ew.getDefensiveEW(o))})]},`dew-scs-${o.id}`));var b=Math.max(0,ew.getCCEW(o)-ew.getDistruptionEW(o));b>0&&s.push(m.jsxs(bs,{children:[m.jsx(ws,{$color:xs("CCEW"),children:"CCEW"}),m.jsx(Ss,{children:ki(b)})]},`ccew-scs-${o.id}`));let S=ew.getBDEW(o)*.25,y=ew.getDetectSEW(o),E=ew.getDetectMEW(o);shipManager.hasSpecialAbility(o,"ConstrainedEW")&&(S=ew.getBDEW(o)*.2),S&&s.push(m.jsxs(bs,{...g("BDEW"),children:[m.jsx(ws,{$color:xs("BDEW"),children:"BDEW"}),m.jsx(Ss,{children:ki(S)})]},`bdew-scs-${o.id}`)),E&&s.push(m.jsxs(bs,{...g("MDEW"),children:[m.jsx(ws,{$color:xs("Detect Mines"),children:"Detect Mines"}),m.jsx(Ss,{children:ki(E)})]},`DetectMEW-scs-${o.id}`)),y&&s.push(m.jsxs(bs,{children:[m.jsx(ws,{$color:xs("Detect Stealth"),children:"Detect Stealth"}),m.jsx(Ss,{children:ki(y)})]},`DetectSEW-scs-${o.id}`));const O=aE(o);return O&&s.push(O),s},oE=(o,r)=>{const s=!!window.webglScene;return o.EW.filter(d=>d.turn===gamedata.turn).filter(d=>d.type==="OEW"||d.type==="DIST"||d.type==="SOEW"||d.type==="SDEW").map(d=>{const g=gamedata.getShip(d.targetid);return m.jsxs(bs,{$target:!0,children:[m.jsxs(Wj,{children:[m.jsx(ws,{$color:xs(d.type,o),children:d.type}),m.jsx(Yj,{$interactive:s,title:void 0,onClick:s?r.onTargetClick.bind(r,g):void 0,onMouseEnter:s?()=>r.setTargetHighlight(g,d.type,!0):void 0,onMouseLeave:s?()=>r.setTargetHighlight(g,d.type,!1):void 0,children:g.name})]}),m.jsx(Ss,{children:Qj(d,o)})]},`${d.type}-scs-${o.id}-${d.targetid}`)})},Qj=(o,r)=>{switch(o.type){case"SDEW":if(shipManager.hasSpecialAbility(r,"ConstrainedEW")){let s=o.amount*.333;return s=Math.round(s*3)/3,ki(s)}else return ki(o.amount*.5);case"DIST":return shipManager.hasSpecialAbility(r,"ConstrainedEW")?ki(o.amount/4):ki(o.amount/3);case"OEW":return ki(Math.max(0,o.amount-ew.getDistruptionEW(r)));default:return ki(o.amount)}},ki=o=>Math.round(o*100)/100,im=()=>!!window.gamedata&&window.gamedata.gamephase===-2,qj=D.div`
    position: relative;
    width: 114px;
    height: 150px;
    background-color: black;
    border: 1px solid #496791;
    box-sizing: border-box;
    margin: 5px;

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;
`,Xj=D.div`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    background-image: url(${o=>o.$img});
    background-size: 80%;
    background-position: center;
    background-repeat: no-repeat;
    filter: ${o=>o.$destroyed?"blur(1px)":"none"};
    opacity: ${o=>o.$destroyed?"0.5":"1"};
`,yp=D.div`
    position: absolute;
    top: 55px;
    left: 9px;
    width: 96px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    padding: 5px 0;
    background-color: black;
    color: ${o=>o.$color};
    border: 1px solid ${o=>o.$color};
    z-index: 2;
    pointer-events: none;
    opacity: 0.7;
`,lE=D.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,Jj=D(lE)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,Zj=D.div`
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: calc(100% - 4px);
    height: 16px;
    box-sizing: border-box;
    background-color: black;
    cursor: ${o=>o.$clickable?"pointer":"default"};
    color: ${o=>o.$health===0?"transparent":"white"};
    font-family: arial;
    font-size: 11px;
    text-shadow: black 0 0 6px, black 0 0 6px;
    border: 1px solid #496791;
    margin: 2px;

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;

    &::before {
        box-sizing: border-box;
        content: "";
        position:absolute;
        width:  ${o=>o.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        z-index: 0;
        background-color: ${o=>o.$docked?"#00b8e6":o.$criticals?o.$criticalsBenign?"#00ccff":"#ed6738":"#427231"};
        border: 1px solid black;
    }
`,e_=D.div`
    z-index: 1;
`;class t_ extends Je.Component{onSystemMouseOver(r){if(im()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||r.nativeEvent&&r.nativeEvent.sourceCapabilities&&r.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:s}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:s,system:s,element:r.target})}onSystemMouseOut(){im()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onFighterTouchStart(r){if(im())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:g}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:g,system:g,element:s,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(r){if(!this.longPressTimer)return;const s=r.touches[0],d=s.clientX-this.touchStartX,g=s.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(g)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(r){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onFighterTouchEnd(r){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){const{ship:r}=this.props,s=window.gamedata&&typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted();return im()&&!s&&!!r&&r.userid!=0&&!!r.flight}onHealthBarClick(r){this.canApplyPreBattleDamage()&&(r.stopPropagation(),r.preventDefault(),window.uiEvents.relay("FighterDamageClicked",{ship:this.props.ship,fighter:this.props.fighter,element:r.currentTarget}))}render(){const{ship:r,fighter:s}=this.props,d=shipManager.systems.isDestroyed(r,s),g=shipManager.criticals.isDockedFighter(s),b=!g&&shipManager.criticals.isSplitLaunchedFighter(s),S=!g&&!b&&shipManager.criticals.isDisengagedFighter(s),y=shipManager.criticals.isCutOffFighter(s);let E=null;d?g?E=m.jsx(yp,{$color:"#00b8e6",children:"DOCKED"}):b?E=m.jsx(yp,{$color:"#00b8e6",children:"SPLIT"}):S?E=m.jsx(yp,{$color:"#ff8c00",children:"DROPOUT"}):E=m.jsx(yp,{$color:"#ff5252",children:"DESTROYED"}):y&&(E=m.jsx(yp,{$color:"#ff5252",children:"CUT OFF"}));const O=this.canApplyPreBattleDamage(),$=O?battleDamage.fighterHealth(r,1)/s.maxhealth*100:n_(r,s),P=O?`${battleDamage.fighterHealth(r,1)} / ${s.maxhealth}`:`${s.maxhealth-damageManager.getDamage(r,s)} / ${s.maxhealth}`;return m.jsxs(qj,{$docked:g,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[m.jsxs(Xj,{$destroyed:d,$img:window.AssetManager.getSmartImagePath(s.iconPath),children:[m.jsx(lE,{children:sE(r,s,a_(s),d)}),m.jsx(Jj,{children:sE(r,s,o_(s),d)}),m.jsx(Zj,{$health:$,$criticals:r_(s),$criticalsBenign:i_(s),$docked:g,$clickable:O,title:O?"Apply pre-battle damage to this flight":void 0,onClick:this.onHealthBarClick.bind(this),children:m.jsx(e_,{children:P})})]}),E]})}}const n_=(o,r)=>(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,r_=o=>shipManager.criticals.hasCriticals(o),i_=o=>shipManager.criticals.hasOnlyCritical(o,"LaunchedThisTurn",!1),a_=o=>o.systems.filter(r=>r.location==1),o_=o=>o.systems.filter(r=>r.location!=1),sE=(o,r,s,d)=>s.map((g,b)=>m.jsx(n0,{$destroyed:d,fighter:!0,scs:!0,system:g,ship:o},`system-scs-fighter${r.id}-${o.id}-${g.id}-${b}`)),l_=D.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;class u0 extends Je.Component{render(){const{ship:r}=this.props;return m.jsx(l_,{children:s_(r)})}}const s_=o=>o.systems.map((r,s)=>m.jsx(t_,{fighter:r,ship:o},`flight-${o.id}-${s}`)),u_=[3,31,32],c_=[1,0,2],d_=[4,41,42],f_=D.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,c0=D.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${o=>o.$side?"center":"flex-start"};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,p_=D.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${V.colors.line};
    padding: 3px 5px;
`,h_=D.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${V.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${V.colors.line};
`,g_=D.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${V.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`,m_=D.span`
    font-family: ${V.fonts.mono};
    color: ${V.colors.text};
    flex-shrink: 0;
`,v_=(o,r)=>m.jsxs(p_,{children:[m.jsx(h_,{children:r.name}),[...r.entries].sort((s,d)=>d.chance-s.chance).map((s,d)=>m.jsxs(g_,{children:[m.jsx("span",{children:s.name}),m.jsxs(m_,{children:[s.chance,"%"]})]},`hitchart-${o.id}-${r.location}-${d}`))]},`hitchart-${o.id}-${r.location}`);class y_ extends Je.Component{render(){const{ship:r}=this.props,s=YC(r);if(s.length===0)return null;const d={};s.forEach(E=>{d[E.location]=E});const g=E=>E.filter(O=>d[O]).map(O=>v_(r,d[O])),b=g(u_),S=g(c_),y=g(d_);return m.jsxs(f_,{children:[b.length>0&&m.jsx(c0,{$side:!0,children:b}),S.length>0&&m.jsx(c0,{children:S}),y.length>0&&m.jsx(c0,{$side:!0,children:y})]})}}const x_=o=>o.split(" ").map(r=>r.charAt(0).toUpperCase()+r.slice(1)).join(" "),b_=o=>{const r=[];if(!o||o.flight)return r;const s=o.fighters||{};if(Object.keys(s).length>0){const g={};if(shipManager.systems.shipHasRestrictedHangar(o)){const b=shipManager.systems.getReservedFighterComposition(o);for(let S=0;S<b.length;S++){const y=b[S].category;g[y]||(g[y]=[]);const E=g[y];let O=!1;for(let $=0;$<E.length;$++)if(E[$].phpclass===b[S].phpclass){E[$].count+=b[S].count,O=!0;break}O||E.push({category:b[S].category,phpclass:b[S].phpclass,displayName:b[S].displayName,count:b[S].count,isGroup:b[S].isGroup})}}for(const b in s){const S=s[b],y=x_(b),E=g[b];if(E&&(b==="heavy"||b==="medium"||b==="light")){let O=S;for(let $=0;$<E.length;$++){const P=Math.min(E[$].count,O);P<=0||(E[$].isGroup?r.push(P+" "+E[$].displayName+"s"):r.push(P+" "+E[$].displayName+" "+y+" Fighters"),O-=P)}O>0&&r.push(O+" "+y+" Fighters");continue}if(b==="normal")r.push(S+" Fighters");else if(b==="superheavy"||b==="heavy"||b==="medium"||b==="light"||b==="ultralight")r.push(S+" "+y+" Fighters");else{if(b==="shuttles"||b==="minesweeping shuttles"||b==="cargo shuttles"||b==="lifeboats"||b==="medical shuttles"||b==="presidential shuttle"||b==="yacht")continue;r.push(S+" "+y)}}}const d=shipManager.systems.getDefaultShuttleComposition(o);for(let g=0;g<d.length;g++)r.push(d[g].count+" "+d[g].type);return r},w_=D.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${V.colors.textAccent};
    ${o=>o.$grid?`
    grid-area: ew;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px;`:o.$full?`
    width: 100%;
    padding: 4px;`:`
    flex: 0 0 auto;
    width: 200px;
    padding: 4px;`}
`,xp=D.div`
    background-color: ${V.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${o=>o.$gold?V.colors.enhLine:V.colors.line};
    padding: 0 8px 3px;
`,d0=D.div`
    /*flex-centred fixed-height bar (user request 2026-07-22): consistent vertical
      centring across every chrome title/header bar in the ship window*/
    display: flex;
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${o=>o.$gold?V.colors.enhTitle:V.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${o=>o.$gold?V.colors.enhBg:"rgba(73, 103, 145, 0.25)"};
    border-bottom: 1px solid ${o=>o.$gold?V.colors.enhLine:V.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,bp=D.div`
    padding: 1px 0;
`,S_=D.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${V.colors.custom};
`,Gi=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,Ki=D.span`
    font-size: 10px;
    color: ${V.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,Qi=D.span`
    font-family: ${V.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${o=>o.$changed?V.colors.custom:V.colors.text};
    margin-right: 5px;
`,C_=D.div`
    width: 150px;
    box-sizing: border-box;
    ${o=>o.$bare?`
    padding: 0;`:`
    background-color: ${V.colors.panelBgGlass};
    border: 1px dotted ${V.colors.line};
    padding: 2px 4px 3px;`}
`,E_=D.div`
    display: flex;
    /*centred, matching the Hit Chart button and every other title bar (user request
      2026-07-22); the bar-graph glyph centres alongside the text*/
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    gap: 4px; /*matches the Hit Chart button's icon/label gap so the title lines up*/
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${V.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${V.colors.line};
`,uE=D.span`
    display: inline-flex;
    align-items: flex-end;
    justify-content: center;
    gap: 1px;
    flex: 0 0 auto;
    width: 12px;
    height: 9px;
    i {
        display: block;
        width: 2px;
        background-color: ${V.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;D.div`
    text-align: center;
    font-size: 10px;
    color: ${V.colors.warning};
    padding-top: 2px;
`;const cE=D.div`
    /*flex-centred fixed-height bar (user request 2026-07-22), matching BlockTitle*/
    display: flex;
    align-items: center;
    box-sizing: border-box;
    min-height: 15px;
    line-height: 1;
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    color: ${V.colors.enhTitle};
    background-color: ${V.colors.enhBg};
    border-bottom: 1px solid ${V.colors.enhLine};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,T_=D.div`
    grid-area: enh;
    justify-self: center;
    align-self: start; /*top of its cell - starts directly below the Starboard section (feedback round 5)*/
    /*>>> ENHANCEMENTS-BOX GAP <<< minimum space above the Enhancements box (between it
      and the Starboard section above), applied on BOTH game.php and the lobby since they
      share this component. Adjust this one value to taste.*/
    margin-top: 0px;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    /*150px on BOTH screens (user 2026-08-02): the game.php box was 130px on the strength of
      "matches the EW panel it sits below", but EwPanel is 150px (ShipWindowEw.js) - as are the
      lobby's datasheet panels in the same column - so 130 was the odd one out and read as a
      narrower box stacked under a wider one. One width for the whole right-hand column; change
      it here and in EwPanel together.*/
    width: 150px;
    box-sizing: border-box;
    font-size: 10px;
    line-height: 1.4;
    color: ${V.colors.enhText};
`,f0=o=>typeof o=="number"?o.toFixed(2):o,dE=(o,r)=>o*5+"/"+r*5,am=(o,r)=>o+" ("+f0(r)+")",k_=o=>{const r=window.shipManager;if(!r)return null;const s={},d=r.systems?r.systems.getSystemByName(o,"CnC"):null,g=d&&r.criticals?r.criticals.hasCritical(d,"ProfileIncreased"):0;s.profile=dE(o.forwardDefense+g,o.sideDefense+g),s.profileChanged=g!==0;const b=o.iniativeadded||0;s.initiative=(o.iniativebonus||0)+b,s.initiativeChanged=b!==0;const S=r.movement;if(S&&typeof S.getTurnCost=="function"&&o.movement&&o.movement.length>0){const y=S.getSpeed(o),E=S.getDockedLcvTurnSurcharge(o),O=S.getTurnDelayCost(o);let $=S.getTurnCost(o);o.submarine&&S.isGoingBackwards(o)&&($=$*1.33);const P=Math.max(1,Math.ceil(y*$))+E,j=S.applyCrewTurnDelay(o,Math.ceil(y*O))+E;s.turnCost=am(P,$),s.turnDelay=am(j,O),s.turnCostChanged=s.turnCost!==am(Math.max(1,Math.ceil(y*o.turncost)),o.turncost),s.turnDelayChanged=s.turnDelay!==am(Math.ceil(y*o.turndelaycost),o.turndelaycost)}return s},fE=({ship:o,live:r,bare:s})=>{const d=!o.base,g=r?k_(o):null;return m.jsxs(C_,{$bare:s,children:[!s&&m.jsxs(E_,{children:[m.jsxs(uE,{children:[m.jsx("i",{}),m.jsx("i",{}),m.jsx("i",{})]}),"Ship Stats"]}),d&&m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Turn cost"}),m.jsx(Qi,{$changed:!!(g&&g.turnCostChanged),children:g&&g.turnCost?g.turnCost:f0(o.turncost)})]}),d&&m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Turn delay"}),m.jsx(Qi,{$changed:!!(g&&g.turnDelayChanged),children:g&&g.turnDelay?g.turnDelay:f0(o.turndelaycost)})]}),d&&m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Accel/decel"}),m.jsx(Qi,{children:o.accelcost})]}),d&&m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Pivot"}),m.jsx(Qi,{children:o.pivotcost})]}),d&&m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Roll"}),m.jsx(Qi,{children:o.rollcost})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Profile - Front / Side"}),m.jsx(Qi,{$changed:!!(g&&g.profileChanged),children:g?g.profile:dE(o.forwardDefense,o.sideDefense)})]}),d&&m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Initiative"}),m.jsx(Qi,{$changed:!!(g&&g.initiativeChanged),children:g?g.initiative:o.iniativebonus})]})]})},R_=({ship:o})=>{const r=p0(o.enhancementTooltip);return r.length===0?null:m.jsx(T_,{children:m.jsxs(xp,{$gold:!0,children:[m.jsx(cE,{children:"Enhancements"}),r.map((s,d)=>m.jsx(bp,{children:s},`enh-${d}`))]})})},p0=o=>(o||"").split(/<br\s*\/?>/i).map(r=>r.replace(/<[^>]*>/g,"").replace(/&nbsp;/g," ").trim()).filter(Boolean);class h0 extends Je.Component{render(){const{ship:r,full:s,grid:d,hideEnhancements:g}=this.props,b=b_(r),S=p0(r.notes),y=g?[]:p0(r.enhancementTooltip),E=[];if(r.limited&&r.limited!=0&&E.push("Limited: "+r.limited+"%"),r.variantOf){const P=r.occurence?r.occurence.charAt(0).toUpperCase()+r.occurence.slice(1)+" ":"";E.push(P+"variant of "+r.variantOf)}r.isd&&E.push("In-Service (ISD): "+r.isd);let O=null;r.unofficial==="S"?O="Semi-Custom":r.unofficial&&(O="Custom");const $=S.length>0||E.length>0||O;return m.jsxs(w_,{$full:s,$grid:d,children:[r.flight&&m.jsxs(xp,{children:[m.jsx(d0,{children:"Flight Stats"}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Armor F/S/A"}),m.jsx(Qi,{children:shipManager.systems.getFlightArmour(r)})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Offensive bonus"}),m.jsx(Qi,{children:r.offensivebonus*5})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Profile - Front / Side"}),m.jsxs(Qi,{children:[r.forwardDefense*5,"/",r.sideDefense*5]})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Thrust"}),m.jsx(Qi,{children:r.freethrust})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Turn Cost / Delay"}),m.jsxs(Qi,{children:[r.turncost," / ",r.turndelaycost==0?0:r.turndelaycost]})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Accel. / Pivot / Roll"}),m.jsxs(Qi,{children:[r.accelcost," / ",r.pivotcost," / ",r.rollcost]})]}),m.jsxs(Gi,{children:[m.jsx(Ki,{children:"Initiative"}),m.jsx(Qi,{children:r.iniativebonus})]})]}),b.length>0&&m.jsxs(xp,{children:[m.jsx(d0,{children:"Hangar Capacity"}),b.map((P,j)=>m.jsx(bp,{children:P},`comp-${j}`))]}),$&&m.jsxs(xp,{children:[m.jsx(d0,{children:"Notes"}),S.map((P,j)=>m.jsx(bp,{children:P},`note-${j}`)),E.map((P,j)=>m.jsx(bp,{children:P},`meta-${j}`)),O&&m.jsx(S_,{children:O})]}),y.length>0&&m.jsxs(xp,{$gold:!0,children:[m.jsx(cE,{children:"Enhancements"}),y.map((P,j)=>m.jsx(bp,{children:P},`enh-${j}`))]})]})}}const D_=400,M_=D.div`
    display: flex;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 4px 0 0;
`,O_=D.div`
    flex: 0 1 auto;
    min-width: 0;
    width: max-content;
    max-width: ${D_}px;
`,wp=D.div`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${o=>o.$isMyTeam?`left: 50px; 
 top: 50px;`:`right: 50px; 
 top: 50px;`}
    width: ${o=>o.$variant==="terrain"?"250px":o.$variant==="flight"||o.$variant==="flightEw"?"auto":"fit-content"};
    max-width: ${o=>o.$variant==="flight"?"400px":o.$variant==="flightEw"?"574px":o.$variant==="flightLobby"?"620px":"unset"};
    height: auto;
    border: 1px solid ${V.colors.line};
    background-color: ${V.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${V.colors.text};
    font-family: ${V.fonts.body};

    /* Prevent text selection and callouts on mobile */
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;

    @media (max-width: 1024px) {
        /*docked a few px in from the screen edge, not flush against it: at top: 0 the drag
          handle sits under the browser's own top-edge gesture area and is awkward to grab
          (user report 2026-07-23)*/
        ${o=>o.$isMyTeam?`left: 4px; 
 top: 8px; 
 right: unset;`:`right: 4px; 
 top: 8px; 
 left: unset;`}
        /*Touch screens size the window from its CONTENT ONLY, never from the viewport, so
          it lays out identically in portrait and landscape and applyScreenFit() just scales
          that one fixed layout to whatever screen it lands on (user request 2026-07-23).
          fit-content / auto are available-width dependent: in landscape the extra room
          let a flight window stretch its FighterList into one long row and a single-Primary
          ship spread out, while portrait wrapped them. max-content + the same variant caps
          the desktop rule uses (the caps are what make FighterList wrap at all) removes the
          viewport from the equation. The old 100vw clamp is gone with them - clamping the
          LAYOUT width just squeezed the fixed-width sections into an internally-scrolling
          box (the "too wide in game, too narrow in the lobby" report).*/
        width: ${o=>o.$variant==="terrain"?"250px":"max-content"};
        max-width: ${o=>o.$variant==="flight"?"400px":o.$variant==="flightEw"?"574px":o.$variant==="flightLobby"?"620px":"none"};
        max-height: 100vh;
        /*auto, not scroll: scroll pins a permanent (usually inert) scrollbar to
          the window on classic-scrollbar platforms even when nothing overflows.
          When it does engage, it wears the site-standard scrollbar (same as
          PopupHolder / #gameinfo / the log panel).*/
        overflow-y: auto;
        /*scrolling the window's own overflow must not chain into the page underneath -
          on the lobby that hands the gesture to the page (and to pull-to-refresh at the
          top), which is exactly what steals a drag mid-flight*/
        overscroll-behavior: contain;

        scrollbar-width: thin;
        scrollbar-color: #3c5574 #0d1620;

        &::-webkit-scrollbar {
            width: 10px;
        }
        &::-webkit-scrollbar-track {
            background: #0d1620;
        }
        &::-webkit-scrollbar-thumb {
            background: #3c5574;
        }
        &::-webkit-scrollbar-thumb:hover {
            background: #5a7ea8;
        }
    }
`,$_=D.div`
    /*sticky, not relative (2026-08-06): on a small screen the container is its own scroll
      box (overflow-y: auto + the fitted max-height), and a plain header scrolls straight
      out of it - the player then swipes what looks like the top of the window and gets the
      body, with the only drag handle parked above the visible area. Sticky pins it to the
      top of the scroll box, so the handle is always where the window's top edge is. On
      desktop, where the container never scrolls, this renders identically to relative.
      Still a containing block for the absolutely-positioned close button.*/
    position: sticky;
    top: 0;
    z-index: 4; /*above the section grid (2) so the pinned bar is never drawn through*/
    background-color: ${V.colors.panelBg};
    border-bottom: 1px solid ${V.colors.line};
    height: 26px;
    display: flex;
    align-items: baseline; /*name + class share a text baseline (different font sizes)*/
    gap: 6px;
    padding: 0 26px 0 5px; /*right padding clears the ✕ button*/
    width: 100%;
    box-sizing: border-box;
    flex-shrink: 0;
    cursor: move;
    /*hand the touch gesture to our drag instead of scrolling the page (2026-07-23 -
      without this a finger-drag on the header just scrolls the lobby)*/
    touch-action: none;

    /*NO small-screen height override (user request 2026-07-23, round 12): the header
      hugs its text on every screen, exactly like desktop, and shrinks with the rest of
      the window. Round 10 had made it a flat 44px finger strip and round 11 counter-scaled
      that to a constant ~44 VISUAL px - both read as a disproportionately fat bar once the
      window was scaled down. Cost: the grab target is now 26px * scale (~13-16px on a
      phone). If dragging turns fiddly again, grow the TARGET without growing the BAR (a
      transparent hit-area) rather than restoring a taller header.*/
`,A_=D.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${o=>o.$tint||V.colors.text};
`,j_=D.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${V.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,__=D.div`
    width: 25px;
    height: 25px;
    position: absolute;
    right: 0;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    padding-left: 5px;
    margin-top: -2px;
    color: ${V.colors.line};
    ${Yi}
`,L_=D.div`
    position: sticky;
    bottom: 0;
    align-self: ${o=>o.$mirror?"flex-start":"flex-end"};
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    /*$overlap: draw the mark ON TOP of the row above (a status banner) instead of taking a
      row of its own - the negative margin is exactly the grip's own height, so the row above
      keeps the window's bottom edge. See renderStatusStrip.*/
    ${o=>o.$overlap?"margin-top: -16px;":""}
    box-sizing: border-box;
    z-index: 5;
    cursor: ${o=>o.$mirror?"nesw-resize":"nwse-resize"};
    /*the grip owns the gesture: without this a finger drag scrolls the window/page instead*/
    touch-action: none;

    &::after { /*three diagonal rules clipped to the corner triangle - the standard grip mark*/
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(${o=>o.$mirror?"45deg":"315deg"}, ${V.colors.line} 0 1.5px, transparent 1.5px 4px);
        clip-path: ${o=>o.$mirror?"polygon(0 0, 0 100%, 100% 100%)":"polygon(100% 0, 100% 100%, 0 100%)"};
        opacity: 0.85;
    }

    &::before { /*finger pad - see the note above*/
        content: "";
        position: absolute;
        top: -${o=>o.$pad}px;
        bottom: 0;
        left: ${o=>o.$mirror?"0":"-"+o.$pad+"px"};
        right: ${o=>o.$mirror?"-"+o.$pad+"px":"0"};
    }

    &:hover::after {
        opacity: 1;
    }
`,z_=D.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${o=>o.$compact?"width: 100%; align-items: center; margin-bottom: 5px;":"align-items: stretch;"}
    gap: 4px;
`,om=D.div`
    display: flex;
    /*center: icon + label are vertically centred in the button, consistent with every
      other chrome title/header bar (user request 2026-07-22)*/
    align-items: center;
    line-height: 1;
    gap: 4px;
    padding: 3px 6px 3px 4px;
    box-sizing: border-box;
    /*chrome column width: 150px datasheet panels in the lobby ($wide), 130px in game
      (user 2026-07-19: 150 was too wide on the game screen, 120 too tight) - matches the
      EW / Enhancements panels on each page so the two chrome columns stay symmetric*/
    min-width: ${o=>o.$wide?"150px":"130px"};
    border: 1px solid ${V.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${o=>o.$active?"rgba(198, 226, 255, 0.12)":"rgba(73, 103, 145, 0.25)"};
    color: ${V.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${Yi}
`,pE=D.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,N_=D.span`
    position: relative;
    display: inline-block;
    flex: 0 0 auto;
    align-self: center;
    box-sizing: border-box;
    width: 12px;
    height: 10px;
    border: 1px solid currentColor;
    overflow: hidden;
    &::before { /*sun*/
        content: "";
        position: absolute;
        top: 1.5px;
        right: 1.5px;
        width: 2.5px;
        height: 2.5px;
        border-radius: 50%;
        background-color: currentColor;
    }
    &::after { /*mountain*/
        content: "";
        position: absolute;
        left: -1px;
        bottom: -1px;
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 7px solid transparent;
        border-bottom: 5px solid currentColor;
    }
`,g0=D.div`
    position: absolute;
    top: ${o=>o.$top||78}px;
    /*left edge aligns with the control buttons (measured - they sit centred in the
      grid's ctrl column, not at the window's 6px margin); feedback 2026-07-19*/
    left: ${o=>o.$left!=null?o.$left:6}px;
    /*$fit (Notes): size to content instead of spanning the window*/
    ${o=>o.$fit?"right: auto; width: fit-content; max-width: calc(100% - 12px);":"right: 6px;"}
    /*Notes popup never narrower than the 130px Notes button it drops from (both
      border-box), so short notes still read as one block under the button*/
    ${o=>o.$notes?"min-width: 130px;":""}
    z-index: 20;
    max-height: 70vh;
    overflow-y: auto;
    box-sizing: border-box;
    background-color: ${V.colors.panelBg};
    border: 1px solid ${V.colors.line};
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.7);
    /*Notes ($notes) trims the bottom gap to ~5px (feedback 2026-07-19; paired with
      ShipInfo dropping its trailing blank line); Hit Chart keeps the extra bottom
      padding so the last chart rows never look clipped*/
    padding: 5px 5px ${o=>o.$notes?"5px":"10px"};
    cursor: default;

    scrollbar-width: thin;
    scrollbar-color: #3c5574 #0d1620;

    &::-webkit-scrollbar {
        width: 10px;
    }
    &::-webkit-scrollbar-track {
        background: #0d1620;
    }
    &::-webkit-scrollbar-thumb {
        background: #3c5574;
    }
    &::-webkit-scrollbar-thumb:hover {
        background: #5a7ea8;
    }
`,P_=D.div`
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    grid-template-areas: ${o=>o.$areas};
    justify-content: center;
    gap: 8px;
    padding: 5px 5px 5px 5px;
    box-sizing: border-box;
    width: 100%;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`,F_=D.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,I_=D.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,m0=D.div`
    position: absolute;
    top: 50%;
    left: 50%;
    height: 80%;
    width: auto;
    aspect-ratio: 1 / 1;
    max-width: 80%;
    max-height: 380px;
    transform: translate(-50%, calc(-50% + ${o=>o.$offsetY||0}px)) rotate(-90deg);
    background-image: ${o=>`url(${o.$img})`};
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    filter: ${o=>o.$art?"none":"grayscale(1) brightness(2.2)"};
    opacity: ${o=>o.$art?1:.75};
    pointer-events: none;
    z-index: 0;
`,v0=D.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,U_=D.div`
    width: 100%;
    box-sizing: border-box;
    /*equal top/bottom: the old 2px-top/3px-bottom made the text sit visibly high in
      the tinted strip (the border-top reads as a separator line, not banner fill,
      so it doesn't compensate). At 9px uppercase every half-pixel shows.

      $grip: the resize grip is drawn in this banner's corner (renderStatusStrip), so the
      16px mark plus the usual 6px gutter is reserved - on BOTH sides, because a one-sided
      reserve would push the centred text off the window's midline, and which corner the
      grip sits on follows the dock.*/
    padding: 3px ${o=>o.$grip?"22px":"6px"};
    text-align: center;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: ${o=>o.$color||V.colors.warning};
    background-color: ${o=>o.$bg||"rgba(225, 176, 0, 0.10)"};
    border-top: 1px solid ${V.colors.line};
    flex-shrink: 0;
`,hE=D.div`
    position: relative; /*watermark anchor for the compact variant*/
    width: 100%;
    min-height: 120px; /*room for the watermark art in sparse windows (mines)*/
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-items: flex-start;
    padding: 2px;
    box-sizing: border-box;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`,B_=D.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${V.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${V.fonts.body};
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`,H_={1:"fwd",2:"aft",0:"prim",3:"left",4:"right",31:"lfwd",41:"rfwd",32:"laft",42:"raft"},gE={3:4,4:3,31:41,41:31,32:42,42:32},V_={fwd:"end",aft:"center",prim:"center",left:"center",right:"center",lfwd:"start",rfwd:"start",laft:"end",raft:"end"},W_={left:"end",lfwd:"end",laft:"end",right:"start",rfwd:"start",raft:"start"},Y_=[1,3,31,32,0,4,41,42,2],G_=[1,3,31,0,4,41,32,2,42],K_=[31,32,41,42],Q_=[3,4,31,41,32,42];class q_ extends Je.Component{constructor(r){super(r),this.elementRef=Je.createRef(),this.controlsRef=Je.createRef(),this.popupRef=Je.createRef(),this.hitChartBtnRef=Je.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return um(this.props.ship)?"left":"right"}isMirroredGrip(){return this.side()==="right"}applyScreenFit(){const r=this.elementRef.current;if(!r)return;const s=vE(),d=sm(this.side());if(!s&&d===1){(this.screenFit!==1||this.screenFitHeight)&&(r.style.transform="",r.style.transformOrigin="",r.style.maxHeight="",this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}const g=this.measureNatural();if(!g)return;const b=s?rL():null,S=window.innerHeight||document.documentElement.clientHeight;let y=1;if(s){const $=(document.documentElement.clientWidth||window.innerWidth)*b.fillW,P=S*b.fillH;y=Math.min($/g.width,P/g.height),y=Math.min(b.max,Math.max(b.min,y))}this.autoFit=y;let E=b0(y*d);E=Math.round(E*100)/100;let O=null;if(s){const $=Math.min(y0,b.fillH*Math.max(1,d));O=Math.round(S*$/E)}E===this.screenFit&&O===this.screenFitHeight||(this.screenFit=E,this.screenFitHeight=O,r.style.transformOrigin=this.transformOrigin(),r.style.transform=E===1?"":"scale("+E+")",r.style.maxHeight=O==null?"":O+"px",this.resizeStart||this.keepGripOnScreen())}measureNatural(){const r=this.elementRef.current;if(!r)return null;const s=r.style.maxHeight;r.style.maxHeight="none";const d=r.offsetWidth,g=r.offsetHeight;return r.style.maxHeight=s,d&&g?{width:d,height:g}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:um(this.props.ship)?"top left":"top right"}onScreenResize(){this.applyScreenFit()}isDragHandle(r){return!r||!r.closest||r.closest(".shipwindow-nodrag")?!1:!!r.closest(".shipwindow-drag-handle")}isResizeHandle(r){return!!(r&&r.closest&&r.closest(".shipwindow-resize-grip"))}isDragSlop(r,s){if(!r||!r.closest||!r.closest(".shipwindow-grab-slop"))return!1;const d=this.elementRef.current,g=d&&d.querySelector(".shipwindow-drag-handle");if(!g)return!1;const b=g.getBoundingClientRect();return s>=b.top&&s<=b.bottom+lL}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(r,s,d){const g=Date.now(),b=!!this.lastPress&&this.lastPress.kind===r&&g-this.lastPress.time<sL;this.lastPress={kind:r,time:g},this.pressPoint={x:s,y:d},this.pendingReset=b}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(r,s){const d=this.elementRef.current;if(!d)return!1;const g=window.getComputedStyle(d);let b=parseFloat(g.left),S=parseFloat(g.top);return isFinite(b)||(b=d.offsetLeft),isFinite(S)||(S=d.offsetTop),this.dragStart={x:r,y:s,left:b,top:S},this.positioned=!0,d.style.left=b+"px",d.style.top=S+"px",d.style.right="auto",!0}moveDrag(r,s){const d=this.elementRef.current;!this.dragStart||!d||(d.style.left=this.dragStart.left+(r-this.dragStart.x)+"px",d.style.top=this.dragStart.top+(s-this.dragStart.y)+"px",this.clampIntoView())}beginResize(r,s){const d=this.elementRef.current;if(!d)return!1;const g=this.measureNatural();if(!g)return!1;const b=window.getComputedStyle(d);let S=parseFloat(b.left),y=parseFloat(b.top);isFinite(S)||(S=d.offsetLeft),isFinite(y)||(y=d.offsetTop);const E=this.screenFit||1,O=this.isMirroredGrip(),$=O?"top right":"top left";!O&&this.transformOrigin()==="top right"&&(S+=g.width*(1-E)),d.style.left=S+"px",d.style.top=y+"px",d.style.right="auto",d.style.transformOrigin=$,this.resizeOrigin=$,this.positioned=!0;const P=d.getBoundingClientRect(),j=O?P.right:P.left,H=O?-1:1;return this.resizeStart={originX:j,originY:P.top,flipX:H,width:g.width,height:g.height,scale:E,maxScale:O?j/g.width:1/0,base:xE(g.width,g.height,H*(r-j),s-P.top)},!0}moveResize(r,s){const d=this.resizeStart;if(!d)return;const g=xE(d.width,d.height,d.flipX*(r-d.originX),s-d.originY),b=b0(Math.min(d.maxScale,d.scale+(g-d.base))),S=this.side(),y=Math.round(b/(this.autoFit||1)*100)/100;y!==sm(S)&&(bE(S,y),this.applyScreenFit())}moveGesture(r,s){this.pressPoint&&(Math.abs(r-this.pressPoint.x)>yE||Math.abs(s-this.pressPoint.y)>yE)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(r,s):this.moveDrag(r,s)}finishGesture(){const r=this.elementRef.current,s=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),s){const d=this.side();wE(d,sm(d)),this.keepGripOnScreen(),this.clampIntoView()}r&&(mE[this.side()]={top:parseFloat(r.style.top)||0,left:parseFloat(r.style.left)||0})}clampIntoView(r){const s=this.elementRef.current;if(!s||!this.positioned)return;const d=s.getBoundingClientRect(),g=document.documentElement.clientWidth||window.innerWidth,b=window.innerHeight||document.documentElement.clientHeight;let S=0,y=0;d.top<0?y=-d.top:d.top>b-hd&&(y=b-hd-d.top),r&&d.width<=g&&d.left<0?S=-d.left:d.right<hd?S=hd-d.right:d.left>g-hd&&(S=g-hd-d.left),!(!S&&!y)&&(s.style.left=(parseFloat(s.style.left)||0)+S+"px",s.style.top=(parseFloat(s.style.top)||0)+y+"px")}keepGripOnScreen(){const r=this.elementRef.current;if(!r)return;const s=r.getBoundingClientRect(),d=document.documentElement.clientWidth||window.innerWidth;let g;if(this.isMirroredGrip()){if(s.left>=0||(g=Math.min(-s.left,d-s.right),g<=0))return}else if(g=d-s.right,g>=0)return;const b=window.getComputedStyle(r);let S=parseFloat(b.left);isFinite(S)||(S=r.offsetLeft),r.style.left=S+g+"px",r.style.right="auto",this.positioned=!0}resetUserScale(){const r=this.side();sm(r)!==1&&(bE(r,1),wE(r,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(r){r.stopPropagation(),this.resetUserScale()}onDragStart(r){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] pointerdown",r.pointerType,"handle:",this.isDragHandle(r.target),"grip:",this.isResizeHandle(r.target)),r.pointerType==="touch"||r.button!=null&&r.button>0)return;const s=this.isResizeHandle(r.target);if(!s&&!this.isDragHandle(r.target))return;if(this.notePress(s?"grip":"header",r.clientX,r.clientY),!(s?this.beginResize(r.clientX,r.clientY):this.beginDrag(r.clientX,r.clientY))){this.cancelDoublePress();return}const d=this.elementRef.current;let g=!1;try{d.setPointerCapture(r.pointerId),g=!0}catch{}this.dragTarget=g?d:document,this.dragTarget.addEventListener("pointermove",this.onDragMove),this.dragTarget.addEventListener("pointerup",this.onDragEnd),this.dragTarget.addEventListener("pointercancel",this.onDragEnd),this.dragPointerId=r.pointerId,r.preventDefault()}onDragMove(r){!this.gestureActive()||r.pointerId!==this.dragPointerId||this.moveGesture(r.clientX,r.clientY)}onDragEnd(r){!this.gestureActive()||r&&r.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(r){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] touchstart",r.touches&&r.touches.length,"handle:",this.isDragHandle(r.target),"grip:",this.isResizeHandle(r.target)),this.gestureActive()||!r.touches||r.touches.length!==1)return;const s=r.touches[0],d=this.isResizeHandle(r.target);if(!(!d&&!this.isDragHandle(r.target)&&!this.isDragSlop(r.target,s.clientY))){if(this.notePress(d?"grip":"header",s.clientX,s.clientY),!(d?this.beginResize(s.clientX,s.clientY):this.beginDrag(s.clientX,s.clientY))){this.cancelDoublePress();return}this.touchDragId=s.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.addEventListener("touchend",this.onTouchDragEnd),document.addEventListener("touchcancel",this.onTouchDragEnd),r.cancelable&&r.preventDefault()}}onTouchDragMove(r){const s=SE(r.touches,this.touchDragId);if(!this.gestureActive()||!s)return;this.moveGesture(s.clientX,s.clientY);const d=this.dragScroll;d&&((window.scrollX||window.pageXOffset||0)!==d.x||(window.scrollY||window.pageYOffset||0)!==d.y)&&window.scrollTo(d.x,d.y),r.cancelable&&r.preventDefault()}onTouchDragEnd(r){r&&r.touches&&SE(r.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.removeEventListener("touchend",this.onTouchDragEnd),document.removeEventListener("touchcancel",this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){const r=this.dragTarget;r&&(r.removeEventListener("pointermove",this.onDragMove),r.removeEventListener("pointerup",this.onDragEnd),r.removeEventListener("pointercancel",this.onDragEnd),this.dragPointerId!=null&&r.hasPointerCapture&&r.hasPointerCapture(this.dragPointerId)&&r.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(r){r.stopPropagation();let{ship:s}=this.props;if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(zu()){window.uiEvents.relay("CloseSystemInfo");return}window.uiEvents.relay("SystemClicked",{ship:s,system:s,element:r.target})}onShipTouchMove(r){if(!this.longPressTimer)return;const s=r.touches[0],d=s.clientX-this.touchStartX,g=s.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(g)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(r){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onShipTouchEnd(r){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(r){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:s}=this.props,d=shipManager.systems.getSystemByName(s,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:s,system:d||s.systems[0],element:r.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onUnknownTouchStart(r){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:g}=this.props,b=shipManager.systems.getSystemByName(g,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:g,system:b||g.systems[0],element:s,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){const r=this.elementRef.current,s=um(this.props.ship)?"left":"right";r.addEventListener("pointerdown",this.onDragStart),r.addEventListener("touchstart",this.onTouchDragStart,{passive:!1});const d=mE[s];if(d&&!vE()){const g=Math.max(0,Math.min(d.top,window.innerHeight-60)),b=Math.max(60-r.offsetWidth,Math.min(d.left,window.innerWidth-60));r.style.top=g+"px",r.style.left=b+"px",r.style.right="auto",this.positioned=!0}document.addEventListener("pointerdown",this.onDocumentPointerDown),this.applyScreenFit(),window.addEventListener("resize",this.onScreenResize),window.addEventListener("orientationchange",this.onScreenResize)}componentDidUpdate(){this.applyScreenFit()}componentWillUnmount(){document.removeEventListener("pointerdown",this.onDocumentPointerDown),window.removeEventListener("resize",this.onScreenResize),window.removeEventListener("orientationchange",this.onScreenResize);const r=this.elementRef.current;r&&(r.removeEventListener("pointerdown",this.onDragStart),r.removeEventListener("touchstart",this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(r){this.panelHoverTimer&&(clearTimeout(this.panelHoverTimer),this.panelHoverTimer=null),this.state.hoverPanel!==r&&this.setState({hoverPanel:r})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(r){if(!this.state.openPanel)return;const s=this.controlsRef.current,d=this.popupRef.current;s&&s.contains(r.target)||d&&d.contains(r.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay("CloseShipWindow",{ship:this.props.ship})}togglePanel(r,s){s.stopPropagation(),this.setState(d=>({openPanel:d.openPanel===r?null:r}))}toggleArt(r){r&&r.stopPropagation(),this.setState(s=>({showArt:!s.showArt,openPanel:null}))}renderResizeGrip(r){return m.jsx(L_,{className:"shipwindow-resize-grip shipwindow-nodrag",$pad:oL,$mirror:this.isMirroredGrip(),$overlap:!!r,onDoubleClick:this.onGripDoubleClick,title:"Drag to resize this window — double-click to reset its size"})}renderStatusStrip(r,s){const d=s?[{key:"rolled",text:"⟲ Rolled — port / starboard reversed"}].concat(kE(r)):kE(r);return m.jsxs(Je.Fragment,{children:[d.map((g,b)=>m.jsx(U_,{$color:g.color,$bg:g.bg,$grip:b===d.length-1,children:g.text},g.key)),this.renderResizeGrip(d.length>0)]})}renderHeader(r,s,d){return m.jsxs($_,{className:"shipwindow-drag-handle",title:"Drag to move — double-click to reset window size",children:[m.jsx(A_,{$tint:d,title:r,children:r}),m.jsx(j_,{title:s,children:s}),m.jsx(__,{className:"shipwindow-nodrag",onClick:this.close.bind(this),children:"✕"})]})}renderHitChartButton(r){const{openPanel:s}=this.state;return m.jsxs(om,{ref:this.hitChartBtnRef,$wide:r,$active:s==="hitchart",onClick:this.togglePanel.bind(this,"hitchart"),children:[m.jsx(pE,{children:"⊕"}),"Hit Chart"]})}renderStatsButton(r){const{openPanel:s}=this.state;return m.jsxs(om,{$wide:r,$active:s==="shipstats",onClick:this.togglePanel.bind(this,"shipstats"),onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[m.jsxs(uE,{children:[m.jsx("i",{}),m.jsx("i",{}),m.jsx("i",{})]}),"Ship Stats"]})}renderArtButton(r){return m.jsxs(om,{$wide:r,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[m.jsx(N_,{}),"Ship Art"]})}renderNotesButton(r){const{openPanel:s}=this.state;return m.jsxs(om,{$wide:r,$active:s==="notes",onClick:this.togglePanel.bind(this,"notes"),onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[m.jsx(pE,{children:"✎"}),"Notes"]})}renderControls(r,s,d,g){const b=zu(),S=EE(this.props.ship),y=TE(this.props.ship);if(!r&&!s&&!g&&!S&&!y)return null;const E=b;return m.jsxs(z_,{ref:this.controlsRef,$compact:d,children:[r&&this.renderHitChartButton(E),S&&this.renderArtButton(E),y&&this.renderStatsButton(E),s&&this.renderNotesButton(E),g&&m.jsx(fE,{ship:this.props.ship})]})}getButtonLeft(){const r=this.controlsRef.current,s=this.elementRef.current;return!r||!s?null:Math.round((r.getBoundingClientRect().left-s.getBoundingClientRect().left)/(this.screenFit||1)-s.clientLeft)}getAnchorBelow(r,s){const d=r&&r.current,g=this.elementRef.current;if(!d||!g)return{top:s,left:this.getButtonLeft()};const b=d.getBoundingClientRect(),S=g.getBoundingClientRect(),y=this.screenFit||1;return{left:Math.round((b.left-S.left)/y-g.clientLeft),top:Math.round((b.bottom-S.top)/y-g.clientTop)+4}}renderPopup(r,s,d){const{ship:g}=this.props,{openPanel:b,hoverPanel:S}=this.state,y=b||S;if(!y)return null;const E=y==="hitchart"&&zu()?this.hitChartBtnRef:this.controlsRef,{top:O,left:$}=this.getAnchorBelow(E,d);return y==="hitchart"&&r?m.jsx(g0,{ref:this.popupRef,$top:O,$left:$,$fit:!0,children:m.jsx(y_,{ship:g})}):y==="shipstats"&&TE(g)?m.jsx(g0,{ref:this.popupRef,$top:O,$left:$,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:m.jsx(fE,{ship:g,live:!0,bare:!0})}):y==="notes"&&s?m.jsx(g0,{ref:this.popupRef,$top:O,$left:$,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:m.jsx(GC,{ship:g,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){const{ship:r}=this.props,s=zu(),d=um(r);var g=r.shipClass,b=r.name;let S=!1;if(r.mine){var y=shipManager.systems.getSystemByName(r,"mineStealth");y&&!y.isMineRevealed(r)&&(g="Mine",b="Mine",S=!0)}b||(b=g,g="");const E=!S&&fL(r),O=!s&&!S&&pL(r);if(r.flight){if(s)return m.jsxs(wp,{ref:this.elementRef,onClick:Sp,onContextMenu:ae=>{ae.preventDefault(),ae.stopPropagation()},$isMyTeam:d,$variant:"flightLobby",children:[this.renderHeader(b,g,w0()),m.jsxs(F_,{children:[m.jsx(I_,{children:m.jsx(u0,{ship:r})}),m.jsx(h0,{ship:r})]}),this.renderResizeGrip()]});const fe=!!(window.ew&&ew.isFlightEwPool(r));return m.jsxs(wp,{ref:this.elementRef,onClick:Sp,onContextMenu:ae=>{ae.preventDefault(),ae.stopPropagation()},$isMyTeam:d,$variant:fe?"flightEw":"flight",children:[this.renderHeader(b,g,w0()),fe?m.jsxs(M_,{children:[m.jsx(O_,{children:m.jsx(u0,{ship:r})}),m.jsx(iE,{ship:r,flight:!0})]}):m.jsx(u0,{ship:r}),this.renderStatusStrip(r)]})}if(S)return m.jsxs(wp,{ref:this.elementRef,onClick:Sp,onContextMenu:fe=>{fe.preventDefault(),fe.stopPropagation()},$isMyTeam:d,$variant:"terrain",children:[this.renderHeader(b,g,null),m.jsxs(hE,{children:[m.jsx(v0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),m.jsx(m0,{$img:window.AssetManager.getSmartImagePath(r.imagePath)}),m.jsx(B_,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:"?"})]}),this.renderResizeGrip()]});const $=yL(r),P=vL($);if((window.gamedata.isTerrain(r.shipSizeClass,r.userid)||r.mine)&&!CE($)){const fe=E||O||EE(r);return m.jsxs(wp,{ref:this.elementRef,onClick:Sp,onContextMenu:ae=>{ae.preventDefault(),ae.stopPropagation()},$isMyTeam:d,$variant:"terrain",children:[this.renderHeader(b,g,null),m.jsxs(hE,{children:[m.jsx(v0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),m.jsx(m0,{$img:window.AssetManager.getSmartImagePath(r.imagePath),$art:this.state.showArt,$offsetY:r.mine&&fe?dL:0}),this.renderControls(E,O,!0),G_.map(ae=>$[ae].length>0&&m.jsx(ZC,{location:ae,nameOverride:P[ae],ship:r,systems:$[ae],isTerrain:!0,hidden:this.state.showArt},`section-${r.id}-${ae}`))]}),s&&m.jsx(h0,{ship:r,full:!0}),this.renderStatusStrip(r),this.renderPopup(E,O,72)]})}const H=shipManager.movement.isRolled(r),L=!!r.enhancementTooltip,Q=xL($,L),xe=r.base&&!r.smallBase,ze=m.jsxs(P_,{$areas:Q,children:[m.jsx(v0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),m.jsx(m0,{$img:window.AssetManager.getSmartImagePath(r.imagePath),$art:this.state.showArt,$offsetY:s&&CE($)?cL:0}),this.renderControls(E,O,!1,s&&!r.mine),s?m.jsx(h0,{ship:r,grid:!0,hideEnhancements:L}):m.jsx(iE,{ship:r}),L&&m.jsx(R_,{ship:r}),Y_.map(fe=>{if($[fe].length===0)return null;const ae=H&&gE[fe]!==void 0?gE[fe]:fe,ce=H_[ae],Ee=fe===0||fe===1||fe===2||xe&&K_.includes(fe);return m.jsx(ZC,{location:fe,displayLocation:ae,area:ce,valign:V_[ce],justify:W_[ce],wide:Ee,minHeight:void 0,nameOverride:P[fe],hidden:this.state.showArt,ship:r,systems:$[fe]},`section-${r.id}-${fe}`)})]});return m.jsxs(wp,{ref:this.elementRef,onClick:Sp,onContextMenu:fe=>{fe.preventDefault(),fe.stopPropagation()},$isMyTeam:d,$variant:"ship",children:[this.renderHeader(b,g,w0()),ze,this.renderStatusStrip(r,H),this.renderPopup(E,O)]})}}const Sp=()=>window.uiEvents.relay("CloseSystemInfo"),mE={left:null,right:null},X_="(max-width: 1024px)",J_="(orientation: portrait)",Z_={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},eL={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},y0=.98,vE=()=>!!window.matchMedia&&window.matchMedia(X_).matches,tL=()=>window.matchMedia?window.matchMedia(J_).matches:window.innerHeight>=window.innerWidth,nL=(o,r)=>r===1?o:{fillW:Math.min(y0,o.fillW*r),fillH:Math.min(y0,o.fillH*r),min:Math.min(o.max,o.min*r),max:o.max},rL=()=>{const o=zu()?eL:Z_;return tL()?nL(o,o.portrait):o},iL=.35,aL=3,oL=6,lL=8,sL=400,yE=6,hd=40,x0="fv.shipwindow.userScale",b0=o=>Math.min(aL,Math.max(iL,o)),xE=(o,r,s,d)=>(s*o+d*r)/(o*o+r*r),lm={left:null,right:null},sm=o=>(lm[o]==null&&(lm[o]=uL(o)),lm[o]),bE=(o,r)=>{lm[o]=r},uL=o=>{try{let r=parseFloat(window.localStorage.getItem(x0+"."+o));if(isFinite(r)||(r=parseFloat(window.localStorage.getItem(x0))),isFinite(r)&&r>0)return b0(r)}catch{}return 1},wE=(o,r)=>{try{window.localStorage.setItem(x0+"."+o,String(r))}catch{}},SE=(o,r)=>{if(!o||r==null)return null;for(let s=0;s<o.length;s++)if(o[s].identifier===r)return o[s];return null},zu=()=>!!window.gamedata&&window.gamedata.gamephase===-2,cL=35,CE=o=>Q_.some(r=>o[r].length>0),dL=20,um=o=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide=="function"?window.ShipWindowManager.isLeftSide(o):o.team===window.gamedata.getPlayerTeam(),fL=o=>!!o.hitChart&&Object.keys(o.hitChart).length>0,EE=o=>!!(o&&o.imagePath)&&!o.flight,TE=o=>!!o&&!zu()&&!o.flight&&!o.mine&&!window.gamedata.isTerrain(o.shipSizeClass,o.userid),pL=o=>!!o.notes||!!o.enhancementTooltip||!!(o.hasAttached&&Object.keys(o.hasAttached).length>0),w0=o=>null,kE=o=>{if(zu())return[];const r=[],s=shipManager.getTurnDeployed(o);s>window.gamedata.turn&&s<999&&r.push({key:"deploying",color:V.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Deploying on Turn "+s});const d=shipManager.getArrivalIniPenalty(o);d!==0&&r.push({key:"arrivalScatter",color:V.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Arrival Scatter "+d+" Ini"});const g=o.trueStealth?shipManager.getStealthToggleForecast(o):null;if(o.trueStealth)if(mL(o,g))r.push({key:"undetected",color:V.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Undetected"});else if(o.mine){const y=shipManager.systems.getSystemByName(o,"mineStealth");!y||y.isMineRevealedToOpponent(o)?r.push({key:"detected",color:V.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected - Revealed"}):r.push({key:"detected",color:V.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Detected - Not Revealed"})}else g===!0?r.push({key:"detected",color:V.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Would be Detected"}):r.push({key:"detected",color:V.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected"});shipManager.isJumpingToHyperspace(o)&&r.push({key:"jumping",color:V.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:"Jumping to Hyperspace"});const b=window.JumpEngine&&typeof window.JumpEngine.getAbductionChain=="function"?window.JumpEngine.getAbductionChain(o.id):null;b&&r.push({key:"abducted",color:"#b36bff",bg:"rgba(127, 0, 255, 0.10)",text:"Being abducted: "+window.JumpEngine.formatAbductionHalves(b.total)+"/"+b.cost+" power-turns"});const S=shipManager.getHangarManoeuvre(o);if(S&&r.push({key:"hangarManoeuvre",color:V.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:S.text}),o.attached&&Object.keys(o.attached).length>0&&!o.detached&&!(S&&S.riding)){const y=window.gamedata.getShip(Object.keys(o.attached)[0]);if(y){const E=Object.values(o.attached)[0];let O="";E==1?O="Front":E==2?O="Aft":E==3||E==31||E==32?O="Port":(E==4||E==41||E==42)&&(O="Starboard"),r.push({key:"attached",color:V.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Attached to "+y.name+(O?" ["+O+"]":"")})}}return o.hasAttached&&Object.keys(o.hasAttached).length>0&&Object.keys(o.hasAttached).filter(E=>{const O=window.gamedata.getShip(E);return!(O&&shipManager.isDockingRider(O))}).length>0&&r.push({key:"boarded",color:V.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Ship is being boarded!"}),o.flight&&gL(o)&&r.push({key:"edfGrounded",color:hL,bg:"rgba(210, 80, 255, 0.12)",text:"Energy Drained - cannot fire"}),r},hL="#d250ff",gL=o=>{const r=shipManager.systems.getSystem(o,1);return!r||!r.criticals?!1:r.criticals.some(s=>s.phpclass==="EdfFighterGrounded"&&s.turn+1===window.gamedata.turn)},mL=(o,r)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(o)==gamedata.turn||shipManager.getTurnDeployed(o)==gamedata.turn))return!0;if(r!=null)return!r;let s=shipManager.isDetected(o);if(!s&&o.team==gamedata.getPlayerTeam()){let d=null;o.mine?d=shipManager.systems.getSystemByName(o,"mineStealth"):o.faction=="Torvalus Speculators"?d=shipManager.systems.getSystemByName(o,"ShadingField"):shipManager.getSpecialAbilityStealth(o,"Cloaking")?d=shipManager.systems.getSystemByName(o,"CloakingDevice"):shipManager.getSpecialAbilityStealth(o,"Stealth")&&(d=shipManager.systems.getSystemByName(o,"stealth")),d&&(Array.isArray(d.detected)&&d.detected.length>0||d.detected===!0||Array.isArray(d.detectedNew)&&d.detectedNew.length>0||d.detectedNew===!0)&&(s=!0)}return!s},vL=o=>{const r={};return[{locations:[3,31,32],name:"Port"},{locations:[4,41,42],name:"Starboard"}].forEach(s=>{const d=s.locations.filter(g=>o[g].some(b=>b.name==="structure"));d.length===1&&d[0]!==s.locations[0]&&(r[d[0]]=s.name)}),r},yL=o=>{const r={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return o.systems.forEach(s=>{s.hideInShipWindow||r[s.location].push(s)}),r},xL=(o,r)=>{const s=[["ctrl","fwd","ew"]];let d=0;if((o[31].length||o[41].length)&&(s.push(["lfwd","prim","rfwd"]),d++),(o[3].length||o[4].length)&&(s.push(["left","prim","right"]),d++),(o[32].length||o[42].length)&&(s.push(["laft","prim","raft"]),d++),d===0&&o[0].length&&s.push([null,"prim",null]),o[2].length&&s.push([null,"aft",null]),r){const g=s[s.length-1];s.length>1&&g[2]===null?g[2]="enh":s.push([null,null,"enh"])}for(let g=1;g<s.length&&s[g][0]===null;g++)s[g][0]="ctrl";for(let g=1;g<s.length&&s[g][2]===null;g++)s[g][2]="ew";return s.map(g=>`"${g[0]||"."}  ${g[1]||"."}  ${g[2]||"."}"`).join(" ")},bL=D.div`

`,wL=D.div`
    position: absolute;
    top: 50px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10001;
    pointer-events: auto;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 10px;
    border: 1px solid ${V.colors.warning};
    background-color: ${V.colors.windowBg};
    color: ${V.colors.text};
    font-family: ${V.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`,SL=D.span`
    cursor: pointer;
    color: ${V.colors.line};
    font-size: 16px;

    &:hover {
        color: ${V.colors.text};
    }
`;class CL extends Je.Component{constructor(r){super(r),this.state={error:null}}static getDerivedStateFromError(r){return{error:r}}componentDidCatch(r,s){const d=this.props.ship;console.error("Ship window render failed for",d&&(d.name||d.shipClass),r,s)}render(){if(this.state.error){const r=this.props.ship;return m.jsxs(wL,{children:[m.jsxs("span",{children:[r&&(r.name||r.shipClass)||"Ship"," — window failed to render (see console)"]}),m.jsx(SL,{onClick:()=>window.uiEvents.relay("CloseShipWindow",{ship:r}),children:"✕"})]})}return this.props.children}}class EL extends Je.Component{render(){const{ships:r}=this.props;return m.jsx(bL,{children:r.map(s=>m.jsx(CL,{ship:s,children:m.jsx(q_,{ship:s})},`shipwindow-${s.userid}-${s.id}`))})}}const TL=D.div`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.$position).reduce((r,s)=>r+`
`+s+":"+o.$position[s]+"px;","")}
    /*the lobby mounts #systemInfoReact inside a pointer-events: none overlay*/
    pointer-events: auto;
    max-height: 70vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    min-width: 230px;
    /*Shrink-to-fit, so the critical picker's <select> would otherwise stretch this to its
      longest option the moment "All" is ticked - see the note on ApplyDamageMenu's
      Container. A max-width is what clamps the max-content contribution.*/
    max-width: 300px;
    box-sizing: border-box;
    /*Fill and frame shared with the ship and mine editors - see ../system/menuControls. No
      element opacity beside the fill's alpha: the two compound, and it faded the text.*/
    background-color: ${qe.bg};
    border: 1px solid ${qe.line};
`;D.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${qe.dim};
    user-select: none;
`;const kL=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 6px;
    font-size: 11px;
    color: ${qe.text};

    &:hover {
        background-color: rgba(51, 65, 79, 0.45);
    }
`,RL=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,DL=D.span`
    flex: 0 0 auto;
    color: ${qe.dim};
    font-size: 10px;
    user-select: none;
`,RE=D.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: ${qe.btnBg};
    border: 1px solid ${qe.line};
    color: ${qe.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${qe.line};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${qe.btnBg}; color: ${qe.btnText}; }
    `}
`,ML=D.input`
    flex: 0 0 40px;
    width: 40px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${V.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${qe.well};
    border: 1px solid ${qe.line};
    outline: none;

    &:focus { border-color: ${qe.focus}; }
`,OL=D.div`
    margin: 4px 6px 6px 6px;
    height: 20px;
    background: ${qe.btnBg};
    border: 1px solid ${qe.line};
    color: ${qe.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    user-select: none;

    &:hover { background: ${qe.line}; color: #ffffff; }
`,$L=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r};class AL extends Je.Component{constructor(r){super(r),this.wheelRefs={}}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=gp(s=>this.step(r,s.deltaY<0?1:-1))),this.wheelRefs[r]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(r,s){const{ship:d}=this.props,g=this.maxHealth();let b=parseInt(s,10);isNaN(b)&&(b=g),b=Math.max(1,Math.min(g,b)),battleDamage.setFighter(d,r,{d:g-b}),this.refresh()}step(r,s){this.setRemaining(r,battleDamage.fighterHealth(this.props.ship,r)+s)}onInput(r,s){const d=String(s.target.value).replace(/[^0-9]/g,"");this.setRemaining(r,d===""?0:parseInt(d,10))}propagate(){const{ship:r}=this.props,s=parseInt(r.flightSize,10)||0,d=battleDamage.fighterMaxHealth(r)-battleDamage.fighterHealth(r,1);for(let g=2;g<=s;g++)battleDamage.setFighter(r,g,{d});this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate()}render(){const{ship:r,boundingBox:s}=this.props,d=parseInt(r&&r.flightSize,10)||0,g=this.maxHealth();if(!d||!g)return null;battleDamage.flightSummary(r);const b=[];for(let E=1;E<=d;E++){const O=battleDamage.fighterHealth(r,E);b.push(m.jsxs(kL,{children:[m.jsxs(RL,{children:["Fighter ",E]}),m.jsx(RE,{title:"More damage",disabled:O<=1,onClick:()=>this.step(E,-1),children:"−"}),m.jsx(ML,{ref:this.wheelRef(E),type:"text",value:O,onChange:$=>this.onInput(E,$)}),m.jsx(RE,{title:"Repair",disabled:O>=g,onClick:()=>this.step(E,1),children:"+"}),m.jsxs(DL,{children:["/ ",g]})]},`ftr-${E}`))}const S=battleDamage.flightCritEntry(r)||{},y=EC(S.c,r.preBattleCritDesc,r.preBattleCritTransient,S.p);return m.jsxs(TL,{$position:$L(s),onClick:E=>E.stopPropagation(),children:[m.jsx(SC,{$sticky:!0,children:"Fighter Damage"}),b,d>1&&m.jsx(OL,{title:"Copy Fighter 1's damage to every fighter in this flight",onClick:()=>this.propagate(),children:"Apply Fighter 1's damage to all"}),m.jsx(TC,{ship:r,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:y,editable:!0,onChange:()=>this.refresh()})]})}}const jL=D.div`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.$position).reduce((r,s)=>r+`
`+s+":"+o.$position[s]+"px;","")}
    /*the lobby mounts #systemInfoReact inside a pointer-events: none overlay*/
    pointer-events: auto;
    max-height: 70vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    min-width: 210px;
    box-sizing: border-box;
    /*Fill and frame shared with the ship and fighter editors - see ../system/menuControls.
      No element opacity beside the fill's alpha: the two compound, and it faded the text.*/
    background-color: ${qe.bg};
    border: 1px solid ${qe.line};
`,_L=D.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${qe.dim};
    user-select: none;
`,LL=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 6px;
    font-size: 11px;
    color: ${qe.text};

    &:hover {
        background-color: rgba(51, 65, 79, 0.45);
    }
`,zL=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,NL=D.span`
    flex: 0 0 auto;
    color: ${qe.dim};
    font-size: 10px;
    user-select: none;
`,DE=D.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: ${qe.btnBg};
    border: 1px solid ${qe.line};
    color: ${qe.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${qe.line};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${qe.btnBg}; color: ${qe.btnText}; }
    `}
`,PL=D.input`
    flex: 0 0 40px;
    width: 40px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${V.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${qe.well};
    border: 1px solid ${qe.line};
    outline: none;

    &:focus { border-color: ${qe.focus}; }
`,FL=D.div`
    margin: 4px 6px 6px 6px;
    height: 20px;
    background: ${qe.btnBg};
    border: 1px solid ${qe.line};
    color: ${qe.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    user-select: none;

    &:hover { background: ${qe.line}; color: #ffffff; }
`,IL=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r};class UL extends Je.Component{constructor(r){super(r),this.wheelRefs={}}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=gp(s=>this.step(r,s.deltaY<0?1:-1))),this.wheelRefs[r]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(r,s){const{ship:d}=this.props,g=this.maxHealth();let b=parseInt(s,10);isNaN(b)&&(b=g),b=Math.max(1,Math.min(g,b)),battleDamage.setMine(d,r,{d:g-b}),this.refresh()}step(r,s){this.setRemaining(r,battleDamage.mineHealth(this.props.ship,r)+s)}onInput(r,s){const d=String(s.target.value).replace(/[^0-9]/g,"");this.setRemaining(r,d===""?0:parseInt(d,10))}propagate(){const{ship:r}=this.props,s=battleDamage.mineCount(r),d=battleDamage.getEntry(r,battleDamage.KIND_MINE,1);for(let g=2;g<=s;g++)battleDamage.setWholeEntry(r,battleDamage.KIND_MINE,g,d);this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate()}render(){const{ship:r,boundingBox:s}=this.props,d=battleDamage.mineCount(r),g=this.maxHealth();if(!d||g<2)return null;const b=battleDamage.mineSummary(r),S=[];for(let y=1;y<=d;y++){const E=battleDamage.mineHealth(r,y);S.push(m.jsxs(LL,{children:[m.jsxs(zL,{children:["Mine ",y]}),m.jsx(DE,{title:"More damage",disabled:E<=1,onClick:()=>this.step(y,-1),children:"−"}),m.jsx(PL,{ref:this.wheelRef(y),type:"text",value:E,onChange:O=>this.onInput(y,O)}),m.jsx(DE,{title:"Repair",disabled:E>=g,onClick:()=>this.step(y,1),children:"+"}),m.jsxs(NL,{children:["/ ",g]})]},`mne-${y}`))}return m.jsxs(jL,{$position:IL(s),onClick:y=>y.stopPropagation(),children:[m.jsx(SC,{$sticky:!0,children:"Mine Damage"}),m.jsxs(_L,{children:[b.remaining," / ",b.total," structure"]}),S,d>1&&m.jsx(FL,{title:"Copy Mine 1's damage to every mine in this purchase",onClick:()=>this.propagate(),children:"Apply Mine 1 to all"})]})}}class BL{constructor(r){this.parentElement=r,this.roots=new Map}getRoot(r){const s=jQuery(r,this.parentElement)[0];return s?(this.roots.has(s)||this.roots.set(s,ox(s)),this.roots.get(s)):null}unmountRoot(r){const s=jQuery(r,this.parentElement)[0];s&&this.roots.has(s)&&(this.roots.get(s).unmount(),this.roots.delete(s))}EwButtons(r){const s=this.getRoot("#showEwButtons");s&&s.render(m.jsx(dO,{...r}))}FullScreen(r){const s=this.getRoot("#fullScreen");s&&s.render(m.jsx(oO,{...r}))}Surrender(r){const s=this.getRoot("#surrender");s&&s.render(m.jsx(sO,{...r}))}PlayerSettings(r){const s=this.getRoot("#playerSettings");s&&s.render(m.jsx(YM,{...r}))}showShipThrustUI(r){const s=this.getRoot("#shipThrust");s&&s.render(m.jsx(tO,{...r}))}hideShipThrustUI(){this.unmountRoot("#shipThrust")}showWeaponList(r){const s=this.getRoot("#weaponList");s&&s.render(m.jsx(wj,{...r}))}hideWeaponList(){this.unmountRoot("#weaponList")}showSystemInfo(r){const s=this.getRoot("#systemInfoReact");s&&s.render(m.jsx(Tj,{...r}))}hideSystemInfo(){this.unmountRoot("#systemInfoReact")}showSystemInfoMenu(r){const s=this.getRoot("#systemInfoReact");s&&s.render(m.jsx($j,{...r}))}hideSystemInfoMenu(){this.unmountRoot("#systemInfoReact")}canShowSystemInfoMenu(r,s){return Xx(r,s)}showFighterDamageMenu(r){const s=this.getRoot("#systemInfoReact");s&&s.render(m.jsx(AL,{...r}))}showMineDamageMenu(r){const s=this.getRoot("#systemInfoReact");s&&s.render(m.jsx(UL,{...r}))}renderShipWindows(r){const s=this.getRoot("#shipWindowsReact");s&&s.render(m.jsx(EL,{...r}))}}window.UIManager=BL});
