(function(Sg){typeof define=="function"&&define.amd?define(Sg):Sg()})(function(){"use strict";function Sg(o){return o&&o.__esModule&&Object.prototype.hasOwnProperty.call(o,"default")?o.default:o}var qy={exports:{}},rp={},Xy={exports:{}},Lt={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var LS;function XR(){if(LS)return Lt;LS=1;var o=Symbol.for("react.element"),r=Symbol.for("react.portal"),s=Symbol.for("react.fragment"),d=Symbol.for("react.strict_mode"),g=Symbol.for("react.profiler"),b=Symbol.for("react.provider"),S=Symbol.for("react.context"),v=Symbol.for("react.forward_ref"),T=Symbol.for("react.suspense"),$=Symbol.for("react.memo"),O=Symbol.for("react.lazy"),P=Symbol.iterator;function _(B){return B===null||typeof B!="object"?null:(B=P&&B[P]||B["@@iterator"],typeof B=="function"?B:null)}var V={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},N=Object.assign,J={};function xe(B,re,Ve){this.props=B,this.context=re,this.refs=J,this.updater=Ve||V}xe.prototype.isReactComponent={},xe.prototype.setState=function(B,re){if(typeof B!="object"&&typeof B!="function"&&B!=null)throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,B,re,"setState")},xe.prototype.forceUpdate=function(B){this.updater.enqueueForceUpdate(this,B,"forceUpdate")};function Pe(){}Pe.prototype=xe.prototype;function de(B,re,Ve){this.props=B,this.context=re,this.refs=J,this.updater=Ve||V}var ue=de.prototype=new Pe;ue.constructor=de,N(ue,xe.prototype),ue.isPureReactComponent=!0;var he=Array.isArray,Le=Object.prototype.hasOwnProperty,se={current:null},le={key:!0,ref:!0,__self:!0,__source:!0};function Oe(B,re,Ve){var et,it={},ht=null,Ot=null;if(re!=null)for(et in re.ref!==void 0&&(Ot=re.ref),re.key!==void 0&&(ht=""+re.key),re)Le.call(re,et)&&!le.hasOwnProperty(et)&&(it[et]=re[et]);var tt=arguments.length-2;if(tt===1)it.children=Ve;else if(1<tt){for(var vt=Array(tt),Ut=0;Ut<tt;Ut++)vt[Ut]=arguments[Ut+2];it.children=vt}if(B&&B.defaultProps)for(et in tt=B.defaultProps,tt)it[et]===void 0&&(it[et]=tt[et]);return{$$typeof:o,type:B,key:ht,ref:Ot,props:it,_owner:se.current}}function ft(B,re){return{$$typeof:o,type:B.type,key:re,ref:B.ref,props:B.props,_owner:B._owner}}function He(B){return typeof B=="object"&&B!==null&&B.$$typeof===o}function Tt(B){var re={"=":"=0",":":"=2"};return"$"+B.replace(/[=:]/g,function(Ve){return re[Ve]})}var bt=/\/+/g;function rt(B,re){return typeof B=="object"&&B!==null&&B.key!=null?Tt(""+B.key):re.toString(36)}function Be(B,re,Ve,et,it){var ht=typeof B;(ht==="undefined"||ht==="boolean")&&(B=null);var Ot=!1;if(B===null)Ot=!0;else switch(ht){case"string":case"number":Ot=!0;break;case"object":switch(B.$$typeof){case o:case r:Ot=!0}}if(Ot)return Ot=B,it=it(Ot),B=et===""?"."+rt(Ot,0):et,he(it)?(Ve="",B!=null&&(Ve=B.replace(bt,"$&/")+"/"),Be(it,re,Ve,"",function(Ut){return Ut})):it!=null&&(He(it)&&(it=ft(it,Ve+(!it.key||Ot&&Ot.key===it.key?"":(""+it.key).replace(bt,"$&/")+"/")+B)),re.push(it)),1;if(Ot=0,et=et===""?".":et+":",he(B))for(var tt=0;tt<B.length;tt++){ht=B[tt];var vt=et+rt(ht,tt);Ot+=Be(ht,re,Ve,vt,it)}else if(vt=_(B),typeof vt=="function")for(B=vt.call(B),tt=0;!(ht=B.next()).done;)ht=ht.value,vt=et+rt(ht,tt++),Ot+=Be(ht,re,Ve,vt,it);else if(ht==="object")throw re=String(B),Error("Objects are not valid as a React child (found: "+(re==="[object Object]"?"object with keys {"+Object.keys(B).join(", ")+"}":re)+"). If you meant to render a collection of children, use an array instead.");return Ot}function Bt(B,re,Ve){if(B==null)return B;var et=[],it=0;return Be(B,et,"","",function(ht){return re.call(Ve,ht,it++)}),et}function kt(B){if(B._status===-1){var re=B._result;re=re(),re.then(function(Ve){(B._status===0||B._status===-1)&&(B._status=1,B._result=Ve)},function(Ve){(B._status===0||B._status===-1)&&(B._status=2,B._result=Ve)}),B._status===-1&&(B._status=0,B._result=re)}if(B._status===1)return B._result.default;throw B._result}var pt={current:null},oe={transition:null},Te={ReactCurrentDispatcher:pt,ReactCurrentBatchConfig:oe,ReactCurrentOwner:se};function be(){throw Error("act(...) is not supported in production builds of React.")}return Lt.Children={map:Bt,forEach:function(B,re,Ve){Bt(B,function(){re.apply(this,arguments)},Ve)},count:function(B){var re=0;return Bt(B,function(){re++}),re},toArray:function(B){return Bt(B,function(re){return re})||[]},only:function(B){if(!He(B))throw Error("React.Children.only expected to receive a single React element child.");return B}},Lt.Component=xe,Lt.Fragment=s,Lt.Profiler=g,Lt.PureComponent=de,Lt.StrictMode=d,Lt.Suspense=T,Lt.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=Te,Lt.act=be,Lt.cloneElement=function(B,re,Ve){if(B==null)throw Error("React.cloneElement(...): The argument must be a React element, but you passed "+B+".");var et=N({},B.props),it=B.key,ht=B.ref,Ot=B._owner;if(re!=null){if(re.ref!==void 0&&(ht=re.ref,Ot=se.current),re.key!==void 0&&(it=""+re.key),B.type&&B.type.defaultProps)var tt=B.type.defaultProps;for(vt in re)Le.call(re,vt)&&!le.hasOwnProperty(vt)&&(et[vt]=re[vt]===void 0&&tt!==void 0?tt[vt]:re[vt])}var vt=arguments.length-2;if(vt===1)et.children=Ve;else if(1<vt){tt=Array(vt);for(var Ut=0;Ut<vt;Ut++)tt[Ut]=arguments[Ut+2];et.children=tt}return{$$typeof:o,type:B.type,key:it,ref:ht,props:et,_owner:Ot}},Lt.createContext=function(B){return B={$$typeof:S,_currentValue:B,_currentValue2:B,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null},B.Provider={$$typeof:b,_context:B},B.Consumer=B},Lt.createElement=Oe,Lt.createFactory=function(B){var re=Oe.bind(null,B);return re.type=B,re},Lt.createRef=function(){return{current:null}},Lt.forwardRef=function(B){return{$$typeof:v,render:B}},Lt.isValidElement=He,Lt.lazy=function(B){return{$$typeof:O,_payload:{_status:-1,_result:B},_init:kt}},Lt.memo=function(B,re){return{$$typeof:$,type:B,compare:re===void 0?null:re}},Lt.startTransition=function(B){var re=oe.transition;oe.transition={};try{B()}finally{oe.transition=re}},Lt.unstable_act=be,Lt.useCallback=function(B,re){return pt.current.useCallback(B,re)},Lt.useContext=function(B){return pt.current.useContext(B)},Lt.useDebugValue=function(){},Lt.useDeferredValue=function(B){return pt.current.useDeferredValue(B)},Lt.useEffect=function(B,re){return pt.current.useEffect(B,re)},Lt.useId=function(){return pt.current.useId()},Lt.useImperativeHandle=function(B,re,Ve){return pt.current.useImperativeHandle(B,re,Ve)},Lt.useInsertionEffect=function(B,re){return pt.current.useInsertionEffect(B,re)},Lt.useLayoutEffect=function(B,re){return pt.current.useLayoutEffect(B,re)},Lt.useMemo=function(B,re){return pt.current.useMemo(B,re)},Lt.useReducer=function(B,re,Ve){return pt.current.useReducer(B,re,Ve)},Lt.useRef=function(B){return pt.current.useRef(B)},Lt.useState=function(B){return pt.current.useState(B)},Lt.useSyncExternalStore=function(B,re,Ve){return pt.current.useSyncExternalStore(B,re,Ve)},Lt.useTransition=function(){return pt.current.useTransition()},Lt.version="18.3.1",Lt}var ip={exports:{}};ip.exports;var zS;function ZR(){return zS||(zS=1,function(o,r){var s={};/**
 * @license React
 * react.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */s.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var d="18.3.1",g=Symbol.for("react.element"),b=Symbol.for("react.portal"),S=Symbol.for("react.fragment"),v=Symbol.for("react.strict_mode"),T=Symbol.for("react.profiler"),$=Symbol.for("react.provider"),O=Symbol.for("react.context"),P=Symbol.for("react.forward_ref"),_=Symbol.for("react.suspense"),V=Symbol.for("react.suspense_list"),N=Symbol.for("react.memo"),J=Symbol.for("react.lazy"),xe=Symbol.for("react.offscreen"),Pe=Symbol.iterator,de="@@iterator";function ue(E){if(E===null||typeof E!="object")return null;var j=Pe&&E[Pe]||E[de];return typeof j=="function"?j:null}var he={current:null},Le={transition:null},se={current:null,isBatchingLegacy:!1,didScheduleLegacyUpdate:!1},le={current:null},Oe={},ft=null;function He(E){ft=E}Oe.setExtraStackFrame=function(E){ft=E},Oe.getCurrentStack=null,Oe.getStackAddendum=function(){var E="";ft&&(E+=ft);var j=Oe.getCurrentStack;return j&&(E+=j()||""),E};var Tt=!1,bt=!1,rt=!1,Be=!1,Bt=!1,kt={ReactCurrentDispatcher:he,ReactCurrentBatchConfig:Le,ReactCurrentOwner:le};kt.ReactDebugCurrentFrame=Oe,kt.ReactCurrentActQueue=se;function pt(E){{for(var j=arguments.length,Q=new Array(j>1?j-1:0),ee=1;ee<j;ee++)Q[ee-1]=arguments[ee];Te("warn",E,Q)}}function oe(E){{for(var j=arguments.length,Q=new Array(j>1?j-1:0),ee=1;ee<j;ee++)Q[ee-1]=arguments[ee];Te("error",E,Q)}}function Te(E,j,Q){{var ee=kt.ReactDebugCurrentFrame,ye=ee.getStackAddendum();ye!==""&&(j+="%s",Q=Q.concat([ye]));var Ne=Q.map(function($e){return String($e)});Ne.unshift("Warning: "+j),Function.prototype.apply.call(console[E],console,Ne)}}var be={};function B(E,j){{var Q=E.constructor,ee=Q&&(Q.displayName||Q.name)||"ReactClass",ye=ee+"."+j;if(be[ye])return;oe("Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",j,ee),be[ye]=!0}}var re={isMounted:function(E){return!1},enqueueForceUpdate:function(E,j,Q){B(E,"forceUpdate")},enqueueReplaceState:function(E,j,Q,ee){B(E,"replaceState")},enqueueSetState:function(E,j,Q,ee){B(E,"setState")}},Ve=Object.assign,et={};Object.freeze(et);function it(E,j,Q){this.props=E,this.context=j,this.refs=et,this.updater=Q||re}it.prototype.isReactComponent={},it.prototype.setState=function(E,j){if(typeof E!="object"&&typeof E!="function"&&E!=null)throw new Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,E,j,"setState")},it.prototype.forceUpdate=function(E){this.updater.enqueueForceUpdate(this,E,"forceUpdate")};{var ht={isMounted:["isMounted","Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."],replaceState:["replaceState","Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."]},Ot=function(E,j){Object.defineProperty(it.prototype,E,{get:function(){pt("%s(...) is deprecated in plain JavaScript React classes. %s",j[0],j[1])}})};for(var tt in ht)ht.hasOwnProperty(tt)&&Ot(tt,ht[tt])}function vt(){}vt.prototype=it.prototype;function Ut(E,j,Q){this.props=E,this.context=j,this.refs=et,this.updater=Q||re}var pn=Ut.prototype=new vt;pn.constructor=Ut,Ve(pn,it.prototype),pn.isPureReactComponent=!0;function an(){var E={current:null};return Object.seal(E),E}var Pn=Array.isArray;function xn(E){return Pn(E)}function $n(E){{var j=typeof Symbol=="function"&&Symbol.toStringTag,Q=j&&E[Symbol.toStringTag]||E.constructor.name||"Object";return Q}}function er(E){try{return Yn(E),!1}catch{return!0}}function Yn(E){return""+E}function Ti(E){if(er(E))return oe("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",$n(E)),Yn(E)}function ua(E,j,Q){var ee=E.displayName;if(ee)return ee;var ye=j.displayName||j.name||"";return ye!==""?Q+"("+ye+")":Q}function Hr(E){return E.displayName||"Context"}function tr(E){if(E==null)return null;if(typeof E.tag=="number"&&oe("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof E=="function")return E.displayName||E.name||null;if(typeof E=="string")return E;switch(E){case S:return"Fragment";case b:return"Portal";case T:return"Profiler";case v:return"StrictMode";case _:return"Suspense";case V:return"SuspenseList"}if(typeof E=="object")switch(E.$$typeof){case O:var j=E;return Hr(j)+".Consumer";case $:var Q=E;return Hr(Q._context)+".Provider";case P:return ua(E,E.render,"ForwardRef");case N:var ee=E.displayName||null;return ee!==null?ee:tr(E.type)||"Memo";case J:{var ye=E,Ne=ye._payload,$e=ye._init;try{return tr($e(Ne))}catch{return null}}}return null}var sr=Object.prototype.hasOwnProperty,ur={key:!0,ref:!0,__self:!0,__source:!0},_r,ca,Gn;Gn={};function xr(E){if(sr.call(E,"ref")){var j=Object.getOwnPropertyDescriptor(E,"ref").get;if(j&&j.isReactWarning)return!1}return E.ref!==void 0}function ai(E){if(sr.call(E,"key")){var j=Object.getOwnPropertyDescriptor(E,"key").get;if(j&&j.isReactWarning)return!1}return E.key!==void 0}function no(E,j){var Q=function(){_r||(_r=!0,oe("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",j))};Q.isReactWarning=!0,Object.defineProperty(E,"key",{get:Q,configurable:!0})}function ki(E,j){var Q=function(){ca||(ca=!0,oe("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",j))};Q.isReactWarning=!0,Object.defineProperty(E,"ref",{get:Q,configurable:!0})}function we(E){if(typeof E.ref=="string"&&le.current&&E.__self&&le.current.stateNode!==E.__self){var j=tr(le.current.type);Gn[j]||(oe('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. This case cannot be automatically converted to an arrow function. We ask you to manually fix this case by using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',j,E.ref),Gn[j]=!0)}}var Qe=function(E,j,Q,ee,ye,Ne,$e){var at={$$typeof:g,type:E,key:j,ref:Q,props:$e,_owner:Ne};return at._store={},Object.defineProperty(at._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(at,"_self",{configurable:!1,enumerable:!1,writable:!1,value:ee}),Object.defineProperty(at,"_source",{configurable:!1,enumerable:!1,writable:!1,value:ye}),Object.freeze&&(Object.freeze(at.props),Object.freeze(at)),at};function wt(E,j,Q){var ee,ye={},Ne=null,$e=null,at=null,Ct=null;if(j!=null){xr(j)&&($e=j.ref,we(j)),ai(j)&&(Ti(j.key),Ne=""+j.key),at=j.__self===void 0?null:j.__self,Ct=j.__source===void 0?null:j.__source;for(ee in j)sr.call(j,ee)&&!ur.hasOwnProperty(ee)&&(ye[ee]=j[ee])}var Qt=arguments.length-2;if(Qt===1)ye.children=Q;else if(Qt>1){for(var sn=Array(Qt),un=0;un<Qt;un++)sn[un]=arguments[un+2];Object.freeze&&Object.freeze(sn),ye.children=sn}if(E&&E.defaultProps){var yt=E.defaultProps;for(ee in yt)ye[ee]===void 0&&(ye[ee]=yt[ee])}if(Ne||$e){var hn=typeof E=="function"?E.displayName||E.name||"Unknown":E;Ne&&no(ye,hn),$e&&ki(ye,hn)}return Qe(E,Ne,$e,at,Ct,le.current,ye)}function Yt(E,j){var Q=Qe(E.type,j,E.ref,E._self,E._source,E._owner,E.props);return Q}function bn(E,j,Q){if(E==null)throw new Error("React.cloneElement(...): The argument must be a React element, but you passed "+E+".");var ee,ye=Ve({},E.props),Ne=E.key,$e=E.ref,at=E._self,Ct=E._source,Qt=E._owner;if(j!=null){xr(j)&&($e=j.ref,Qt=le.current),ai(j)&&(Ti(j.key),Ne=""+j.key);var sn;E.type&&E.type.defaultProps&&(sn=E.type.defaultProps);for(ee in j)sr.call(j,ee)&&!ur.hasOwnProperty(ee)&&(j[ee]===void 0&&sn!==void 0?ye[ee]=sn[ee]:ye[ee]=j[ee])}var un=arguments.length-2;if(un===1)ye.children=Q;else if(un>1){for(var yt=Array(un),hn=0;hn<un;hn++)yt[hn]=arguments[hn+2];ye.children=yt}return Qe(E.type,Ne,$e,at,Ct,Qt,ye)}function wn(E){return typeof E=="object"&&E!==null&&E.$$typeof===g}var Sn=".",cr=":";function vn(E){var j=/[=:]/g,Q={"=":"=0",":":"=2"},ee=E.replace(j,function(ye){return Q[ye]});return"$"+ee}var on=!1,Gt=/\/+/g;function Ri(E){return E.replace(Gt,"$&/")}function Wi(E,j){return typeof E=="object"&&E!==null&&E.key!=null?(Ti(E.key),vn(""+E.key)):j.toString(36)}function Yi(E,j,Q,ee,ye){var Ne=typeof E;(Ne==="undefined"||Ne==="boolean")&&(E=null);var $e=!1;if(E===null)$e=!0;else switch(Ne){case"string":case"number":$e=!0;break;case"object":switch(E.$$typeof){case g:case b:$e=!0}}if($e){var at=E,Ct=ye(at),Qt=ee===""?Sn+Wi(at,0):ee;if(xn(Ct)){var sn="";Qt!=null&&(sn=Ri(Qt)+"/"),Yi(Ct,j,sn,"",function(Cp){return Cp})}else Ct!=null&&(wn(Ct)&&(Ct.key&&(!at||at.key!==Ct.key)&&Ti(Ct.key),Ct=Yt(Ct,Q+(Ct.key&&(!at||at.key!==Ct.key)?Ri(""+Ct.key)+"/":"")+Qt)),j.push(Ct));return 1}var un,yt,hn=0,In=ee===""?Sn:ee+cr;if(xn(E))for(var Rl=0;Rl<E.length;Rl++)un=E[Rl],yt=In+Wi(un,Rl),hn+=Yi(un,j,Q,yt,ye);else{var Fu=ue(E);if(typeof Fu=="function"){var po=E;Fu===po.entries&&(on||pt("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),on=!0);for(var Dl=Fu.call(po),Iu,Sp=0;!(Iu=Dl.next()).done;)un=Iu.value,yt=In+Wi(un,Sp++),hn+=Yi(un,j,Q,yt,ye)}else if(Ne==="object"){var vd=String(E);throw new Error("Objects are not valid as a React child (found: "+(vd==="[object Object]"?"object with keys {"+Object.keys(E).join(", ")+"}":vd)+"). If you meant to render a collection of children, use an array instead.")}}return hn}function ro(E,j,Q){if(E==null)return E;var ee=[],ye=0;return Yi(E,ee,"","",function(Ne){return j.call(Q,Ne,ye++)}),ee}function xl(E){var j=0;return ro(E,function(){j++}),j}function bl(E,j,Q){ro(E,function(){j.apply(this,arguments)},Q)}function io(E){return ro(E,function(j){return j})||[]}function wl(E){if(!wn(E))throw new Error("React.Children.only expected to receive a single React element child.");return E}function $a(E){var j={$$typeof:O,_currentValue:E,_currentValue2:E,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null};j.Provider={$$typeof:$,_context:j};var Q=!1,ee=!1,ye=!1;{var Ne={$$typeof:O,_context:j};Object.defineProperties(Ne,{Provider:{get:function(){return ee||(ee=!0,oe("Rendering <Context.Consumer.Provider> is not supported and will be removed in a future major release. Did you mean to render <Context.Provider> instead?")),j.Provider},set:function($e){j.Provider=$e}},_currentValue:{get:function(){return j._currentValue},set:function($e){j._currentValue=$e}},_currentValue2:{get:function(){return j._currentValue2},set:function($e){j._currentValue2=$e}},_threadCount:{get:function(){return j._threadCount},set:function($e){j._threadCount=$e}},Consumer:{get:function(){return Q||(Q=!0,oe("Rendering <Context.Consumer.Consumer> is not supported and will be removed in a future major release. Did you mean to render <Context.Consumer> instead?")),j.Consumer}},displayName:{get:function(){return j.displayName},set:function($e){ye||(pt("Setting `displayName` on Context.Consumer has no effect. You should set it directly on the context with Context.displayName = '%s'.",$e),ye=!0)}}}),j.Consumer=Ne}return j._currentRenderer=null,j._currentRenderer2=null,j}var Di=-1,br=0,Mi=1,oi=2;function Aa(E){if(E._status===Di){var j=E._result,Q=j();if(Q.then(function(Ne){if(E._status===br||E._status===Di){var $e=E;$e._status=Mi,$e._result=Ne}},function(Ne){if(E._status===br||E._status===Di){var $e=E;$e._status=oi,$e._result=Ne}}),E._status===Di){var ee=E;ee._status=br,ee._result=Q}}if(E._status===Mi){var ye=E._result;return ye===void 0&&oe(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))

Did you accidentally put curly braces around the import?`,ye),"default"in ye||oe(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))`,ye),ye.default}else throw E._result}function ja(E){var j={_status:Di,_result:E},Q={$$typeof:J,_payload:j,_init:Aa};{var ee,ye;Object.defineProperties(Q,{defaultProps:{configurable:!0,get:function(){return ee},set:function(Ne){oe("React.lazy(...): It is not supported to assign `defaultProps` to a lazy component import. Either specify them where the component is defined, or create a wrapping component around it."),ee=Ne,Object.defineProperty(Q,"defaultProps",{enumerable:!0})}},propTypes:{configurable:!0,get:function(){return ye},set:function(Ne){oe("React.lazy(...): It is not supported to assign `propTypes` to a lazy component import. Either specify them where the component is defined, or create a wrapping component around it."),ye=Ne,Object.defineProperty(Q,"propTypes",{enumerable:!0})}}})}return Q}function ao(E){E!=null&&E.$$typeof===N?oe("forwardRef requires a render function but received a `memo` component. Instead of forwardRef(memo(...)), use memo(forwardRef(...))."):typeof E!="function"?oe("forwardRef requires a render function but was given %s.",E===null?"null":typeof E):E.length!==0&&E.length!==2&&oe("forwardRef render functions accept exactly two parameters: props and ref. %s",E.length===1?"Did you forget to use the ref parameter?":"Any additional parameter will be undefined."),E!=null&&(E.defaultProps!=null||E.propTypes!=null)&&oe("forwardRef render functions do not support propTypes or defaultProps. Did you accidentally pass a React component?");var j={$$typeof:P,render:E};{var Q;Object.defineProperty(j,"displayName",{enumerable:!1,configurable:!0,get:function(){return Q},set:function(ee){Q=ee,!E.name&&!E.displayName&&(E.displayName=ee)}})}return j}var L;L=Symbol.for("react.module.reference");function ce(E){return!!(typeof E=="string"||typeof E=="function"||E===S||E===T||Bt||E===v||E===_||E===V||Be||E===xe||Tt||bt||rt||typeof E=="object"&&E!==null&&(E.$$typeof===J||E.$$typeof===N||E.$$typeof===$||E.$$typeof===O||E.$$typeof===P||E.$$typeof===L||E.getModuleId!==void 0))}function Ee(E,j){ce(E)||oe("memo: The first argument must be a component. Instead received: %s",E===null?"null":typeof E);var Q={$$typeof:N,type:E,compare:j===void 0?null:j};{var ee;Object.defineProperty(Q,"displayName",{enumerable:!1,configurable:!0,get:function(){return ee},set:function(ye){ee=ye,!E.name&&!E.displayName&&(E.displayName=ye)}})}return Q}function Re(){var E=he.current;return E===null&&oe(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://reactjs.org/link/invalid-hook-call for tips about how to debug and fix this problem.`),E}function Rt(E){var j=Re();if(E._context!==void 0){var Q=E._context;Q.Consumer===E?oe("Calling useContext(Context.Consumer) is not supported, may cause bugs, and will be removed in a future major release. Did you mean to call useContext(Context) instead?"):Q.Provider===E&&oe("Calling useContext(Context.Provider) is not supported. Did you mean to call useContext(Context) instead?")}return j.useContext(E)}function ut(E){var j=Re();return j.useState(E)}function $t(E,j,Q){var ee=Re();return ee.useReducer(E,j,Q)}function St(E){var j=Re();return j.useRef(E)}function Fn(E,j){var Q=Re();return Q.useEffect(E,j)}function yn(E,j){var Q=Re();return Q.useInsertionEffect(E,j)}function Cn(E,j){var Q=Re();return Q.useLayoutEffect(E,j)}function Lr(E,j){var Q=Re();return Q.useCallback(E,j)}function da(E,j){var Q=Re();return Q.useMemo(E,j)}function Kt(E,j,Q){var ee=Re();return ee.useImperativeHandle(E,j,Q)}function kn(E,j){{var Q=Re();return Q.useDebugValue(E,j)}}function gt(){var E=Re();return E.useTransition()}function _a(E){var j=Re();return j.useDeferredValue(E)}function oo(){var E=Re();return E.useId()}function pd(E,j,Q){var ee=Re();return ee.useSyncExternalStore(E,j,Q)}var lo=0,jo,li,ju,Vr,_u,hd,gd;function so(){}so.__reactDisabledLog=!0;function _o(){{if(lo===0){jo=console.log,li=console.info,ju=console.warn,Vr=console.error,_u=console.group,hd=console.groupCollapsed,gd=console.groupEnd;var E={configurable:!0,enumerable:!0,value:so,writable:!0};Object.defineProperties(console,{info:E,log:E,warn:E,error:E,group:E,groupCollapsed:E,groupEnd:E})}lo++}}function si(){{if(lo--,lo===0){var E={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:Ve({},E,{value:jo}),info:Ve({},E,{value:li}),warn:Ve({},E,{value:ju}),error:Ve({},E,{value:Vr}),group:Ve({},E,{value:_u}),groupCollapsed:Ve({},E,{value:hd}),groupEnd:Ve({},E,{value:gd})})}lo<0&&oe("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var La=kt.ReactCurrentDispatcher,Lo;function vs(E,j,Q){{if(Lo===void 0)try{throw Error()}catch(ye){var ee=ye.stack.trim().match(/\n( *(at )?)/);Lo=ee&&ee[1]||""}return`
`+Lo+E}}var uo=!1,Sl;{var Cl=typeof WeakMap=="function"?WeakMap:Map;Sl=new Cl}function zo(E,j){if(!E||uo)return"";{var Q=Sl.get(E);if(Q!==void 0)return Q}var ee;uo=!0;var ye=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var Ne;Ne=La.current,La.current=null,_o();try{if(j){var $e=function(){throw Error()};if(Object.defineProperty($e.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct($e,[])}catch(In){ee=In}Reflect.construct(E,[],$e)}else{try{$e.call()}catch(In){ee=In}E.call($e.prototype)}}else{try{throw Error()}catch(In){ee=In}E()}}catch(In){if(In&&ee&&typeof In.stack=="string"){for(var at=In.stack.split(`
`),Ct=ee.stack.split(`
`),Qt=at.length-1,sn=Ct.length-1;Qt>=1&&sn>=0&&at[Qt]!==Ct[sn];)sn--;for(;Qt>=1&&sn>=0;Qt--,sn--)if(at[Qt]!==Ct[sn]){if(Qt!==1||sn!==1)do if(Qt--,sn--,sn<0||at[Qt]!==Ct[sn]){var un=`
`+at[Qt].replace(" at new "," at ");return E.displayName&&un.includes("<anonymous>")&&(un=un.replace("<anonymous>",E.displayName)),typeof E=="function"&&Sl.set(E,un),un}while(Qt>=1&&sn>=0);break}}}finally{uo=!1,La.current=Ne,si(),Error.prepareStackTrace=ye}var yt=E?E.displayName||E.name:"",hn=yt?vs(yt):"";return typeof E=="function"&&Sl.set(E,hn),hn}function Lu(E,j,Q){return zo(E,!1)}function zu(E){var j=E.prototype;return!!(j&&j.isReactComponent)}function Nt(E,j,Q){if(E==null)return"";if(typeof E=="function")return zo(E,zu(E));if(typeof E=="string")return vs(E);switch(E){case _:return vs("Suspense");case V:return vs("SuspenseList")}if(typeof E=="object")switch(E.$$typeof){case P:return Lu(E.render);case N:return Nt(E.type,j,Q);case J:{var ee=E,ye=ee._payload,Ne=ee._init;try{return Nt(Ne(ye),j,Q)}catch{}}}return""}var Nu={},ys=kt.ReactDebugCurrentFrame;function Pt(E){if(E){var j=E._owner,Q=Nt(E.type,E._source,j?j.type:null);ys.setExtraStackFrame(Q)}else ys.setExtraStackFrame(null)}function md(E,j,Q,ee,ye){{var Ne=Function.call.bind(sr);for(var $e in E)if(Ne(E,$e)){var at=void 0;try{if(typeof E[$e]!="function"){var Ct=Error((ee||"React class")+": "+Q+" type `"+$e+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof E[$e]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw Ct.name="Invariant Violation",Ct}at=E[$e](j,$e,ee,Q,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(Qt){at=Qt}at&&!(at instanceof Error)&&(Pt(ye),oe("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",ee||"React class",Q,$e,typeof at),Pt(null)),at instanceof Error&&!(at.message in Nu)&&(Nu[at.message]=!0,Pt(ye),oe("Failed %s type: %s",Q,at.message),Pt(null))}}}function za(E){if(E){var j=E._owner,Q=Nt(E.type,E._source,j?j.type:null);He(Q)}else He(null)}var lt;lt=!1;function El(){if(le.current){var E=tr(le.current.type);if(E)return`

Check the render method of \``+E+"`."}return""}function dr(E){if(E!==void 0){var j=E.fileName.replace(/^.*[\\\/]/,""),Q=E.lineNumber;return`

Check your code at `+j+":"+Q+"."}return""}function ui(E){return E!=null?dr(E.__source):""}var Wr={};function Na(E){var j=El();if(!j){var Q=typeof E=="string"?E:E.displayName||E.name;Q&&(j=`

Check the top-level render call using <`+Q+">.")}return j}function jn(E,j){if(!(!E._store||E._store.validated||E.key!=null)){E._store.validated=!0;var Q=Na(j);if(!Wr[Q]){Wr[Q]=!0;var ee="";E&&E._owner&&E._owner!==le.current&&(ee=" It was passed a child from "+tr(E._owner.type)+"."),za(E),oe('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',Q,ee),za(null)}}}function ln(E,j){if(typeof E=="object"){if(xn(E))for(var Q=0;Q<E.length;Q++){var ee=E[Q];wn(ee)&&jn(ee,j)}else if(wn(E))E._store&&(E._store.validated=!0);else if(E){var ye=ue(E);if(typeof ye=="function"&&ye!==E.entries)for(var Ne=ye.call(E),$e;!($e=Ne.next()).done;)wn($e.value)&&jn($e.value,j)}}}function fa(E){{var j=E.type;if(j==null||typeof j=="string")return;var Q;if(typeof j=="function")Q=j.propTypes;else if(typeof j=="object"&&(j.$$typeof===P||j.$$typeof===N))Q=j.propTypes;else return;if(Q){var ee=tr(j);md(Q,E.props,"prop",ee,E)}else if(j.PropTypes!==void 0&&!lt){lt=!0;var ye=tr(j);oe("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",ye||"Unknown")}typeof j.getDefaultProps=="function"&&!j.getDefaultProps.isReactClassApproved&&oe("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function Gi(E){{for(var j=Object.keys(E.props),Q=0;Q<j.length;Q++){var ee=j[Q];if(ee!=="children"&&ee!=="key"){za(E),oe("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",ee),za(null);break}}E.ref!==null&&(za(E),oe("Invalid attribute `ref` supplied to `React.Fragment`."),za(null))}}function zr(E,j,Q){var ee=ce(E);if(!ee){var ye="";(E===void 0||typeof E=="object"&&E!==null&&Object.keys(E).length===0)&&(ye+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var Ne=ui(j);Ne?ye+=Ne:ye+=El();var $e;E===null?$e="null":xn(E)?$e="array":E!==void 0&&E.$$typeof===g?($e="<"+(tr(E.type)||"Unknown")+" />",ye=" Did you accidentally export a JSX literal instead of a component?"):$e=typeof E,oe("React.createElement: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",$e,ye)}var at=wt.apply(this,arguments);if(at==null)return at;if(ee)for(var Ct=2;Ct<arguments.length;Ct++)ln(arguments[Ct],E);return E===S?Gi(at):fa(at),at}var Yr=!1;function wp(E){var j=zr.bind(null,E);return j.type=E,Yr||(Yr=!0,pt("React.createFactory() is deprecated and will be removed in a future major release. Consider using JSX or use React.createElement() directly instead.")),Object.defineProperty(j,"type",{enumerable:!1,get:function(){return pt("Factory.type is deprecated. Access the class directly before passing it to createFactory."),Object.defineProperty(this,"type",{value:E}),E}}),j}function xs(E,j,Q){for(var ee=bn.apply(this,arguments),ye=2;ye<arguments.length;ye++)ln(arguments[ye],ee.type);return fa(ee),ee}function Tl(E,j){var Q=Le.transition;Le.transition={};var ee=Le.transition;Le.transition._updatedFibers=new Set;try{E()}finally{if(Le.transition=Q,Q===null&&ee._updatedFibers){var ye=ee._updatedFibers.size;ye>10&&pt("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."),ee._updatedFibers.clear()}}}var bs=!1,ws=null;function kl(E){if(ws===null)try{var j=("require"+Math.random()).slice(0,7),Q=o&&o[j];ws=Q.call(o,"timers").setImmediate}catch{ws=function(ye){bs===!1&&(bs=!0,typeof MessageChannel>"u"&&oe("This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."));var Ne=new MessageChannel;Ne.port1.onmessage=ye,Ne.port2.postMessage(void 0)}}return ws(E)}var Ki=0,Qi=!1;function No(E){{var j=Ki;Ki++,se.current===null&&(se.current=[]);var Q=se.isBatchingLegacy,ee;try{if(se.isBatchingLegacy=!0,ee=E(),!Q&&se.didScheduleLegacyUpdate){var ye=se.current;ye!==null&&(se.didScheduleLegacyUpdate=!1,fo(ye))}}catch(yt){throw co(j),yt}finally{se.isBatchingLegacy=Q}if(ee!==null&&typeof ee=="object"&&typeof ee.then=="function"){var Ne=ee,$e=!1,at={then:function(yt,hn){$e=!0,Ne.then(function(In){co(j),Ki===0?Ss(In,yt,hn):yt(In)},function(In){co(j),hn(In)})}};return!Qi&&typeof Promise<"u"&&Promise.resolve().then(function(){}).then(function(){$e||(Qi=!0,oe("You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"))}),at}else{var Ct=ee;if(co(j),Ki===0){var Qt=se.current;Qt!==null&&(fo(Qt),se.current=null);var sn={then:function(yt,hn){se.current===null?(se.current=[],Ss(Ct,yt,hn)):yt(Ct)}};return sn}else{var un={then:function(yt,hn){yt(Ct)}};return un}}}}function co(E){E!==Ki-1&&oe("You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "),Ki=E}function Ss(E,j,Q){{var ee=se.current;if(ee!==null)try{fo(ee),kl(function(){ee.length===0?(se.current=null,j(E)):Ss(E,j,Q)})}catch(ye){Q(ye)}else j(E)}}var Po=!1;function fo(E){if(!Po){Po=!0;var j=0;try{for(;j<E.length;j++){var Q=E[j];do Q=Q(!0);while(Q!==null)}E.length=0}catch(ee){throw E=E.slice(j+1),ee}finally{Po=!1}}}var Cs=zr,Pu=xs,qi=wp,Es={map:ro,forEach:bl,count:xl,toArray:io,only:wl};r.Children=Es,r.Component=it,r.Fragment=S,r.Profiler=T,r.PureComponent=Ut,r.StrictMode=v,r.Suspense=_,r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=kt,r.act=No,r.cloneElement=Pu,r.createContext=$a,r.createElement=Cs,r.createFactory=qi,r.createRef=an,r.forwardRef=ao,r.isValidElement=wn,r.lazy=ja,r.memo=Ee,r.startTransition=Tl,r.unstable_act=No,r.useCallback=Lr,r.useContext=Rt,r.useDebugValue=kn,r.useDeferredValue=_a,r.useEffect=Fn,r.useId=oo,r.useImperativeHandle=Kt,r.useInsertionEffect=yn,r.useLayoutEffect=Cn,r.useMemo=da,r.useReducer=$t,r.useRef=St,r.useState=ut,r.useSyncExternalStore=pd,r.useTransition=gt,r.version=d,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}()}(ip,ip.exports)),ip.exports}var JR={};JR.NODE_ENV==="production"?Xy.exports=XR():Xy.exports=ZR();var Ze=Xy.exports;const Nn=Sg(Ze);/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var NS;function eD(){if(NS)return rp;NS=1;var o=Ze,r=Symbol.for("react.element"),s=Symbol.for("react.fragment"),d=Object.prototype.hasOwnProperty,g=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,b={key:!0,ref:!0,__self:!0,__source:!0};function S(v,T,$){var O,P={},_=null,V=null;$!==void 0&&(_=""+$),T.key!==void 0&&(_=""+T.key),T.ref!==void 0&&(V=T.ref);for(O in T)d.call(T,O)&&!b.hasOwnProperty(O)&&(P[O]=T[O]);if(v&&v.defaultProps)for(O in T=v.defaultProps,T)P[O]===void 0&&(P[O]=T[O]);return{$$typeof:r,type:v,key:_,ref:V,props:P,_owner:g.current}}return rp.Fragment=s,rp.jsx=S,rp.jsxs=S,rp}var ap={},PS;function tD(){if(PS)return ap;PS=1;var o={};/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */return o.NODE_ENV!=="production"&&function(){var r=Ze,s=Symbol.for("react.element"),d=Symbol.for("react.portal"),g=Symbol.for("react.fragment"),b=Symbol.for("react.strict_mode"),S=Symbol.for("react.profiler"),v=Symbol.for("react.provider"),T=Symbol.for("react.context"),$=Symbol.for("react.forward_ref"),O=Symbol.for("react.suspense"),P=Symbol.for("react.suspense_list"),_=Symbol.for("react.memo"),V=Symbol.for("react.lazy"),N=Symbol.for("react.offscreen"),J=Symbol.iterator,xe="@@iterator";function Pe(L){if(L===null||typeof L!="object")return null;var ce=J&&L[J]||L[xe];return typeof ce=="function"?ce:null}var de=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;function ue(L){{for(var ce=arguments.length,Ee=new Array(ce>1?ce-1:0),Re=1;Re<ce;Re++)Ee[Re-1]=arguments[Re];he("error",L,Ee)}}function he(L,ce,Ee){{var Re=de.ReactDebugCurrentFrame,Rt=Re.getStackAddendum();Rt!==""&&(ce+="%s",Ee=Ee.concat([Rt]));var ut=Ee.map(function($t){return String($t)});ut.unshift("Warning: "+ce),Function.prototype.apply.call(console[L],console,ut)}}var Le=!1,se=!1,le=!1,Oe=!1,ft=!1,He;He=Symbol.for("react.module.reference");function Tt(L){return!!(typeof L=="string"||typeof L=="function"||L===g||L===S||ft||L===b||L===O||L===P||Oe||L===N||Le||se||le||typeof L=="object"&&L!==null&&(L.$$typeof===V||L.$$typeof===_||L.$$typeof===v||L.$$typeof===T||L.$$typeof===$||L.$$typeof===He||L.getModuleId!==void 0))}function bt(L,ce,Ee){var Re=L.displayName;if(Re)return Re;var Rt=ce.displayName||ce.name||"";return Rt!==""?Ee+"("+Rt+")":Ee}function rt(L){return L.displayName||"Context"}function Be(L){if(L==null)return null;if(typeof L.tag=="number"&&ue("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof L=="function")return L.displayName||L.name||null;if(typeof L=="string")return L;switch(L){case g:return"Fragment";case d:return"Portal";case S:return"Profiler";case b:return"StrictMode";case O:return"Suspense";case P:return"SuspenseList"}if(typeof L=="object")switch(L.$$typeof){case T:var ce=L;return rt(ce)+".Consumer";case v:var Ee=L;return rt(Ee._context)+".Provider";case $:return bt(L,L.render,"ForwardRef");case _:var Re=L.displayName||null;return Re!==null?Re:Be(L.type)||"Memo";case V:{var Rt=L,ut=Rt._payload,$t=Rt._init;try{return Be($t(ut))}catch{return null}}}return null}var Bt=Object.assign,kt=0,pt,oe,Te,be,B,re,Ve;function et(){}et.__reactDisabledLog=!0;function it(){{if(kt===0){pt=console.log,oe=console.info,Te=console.warn,be=console.error,B=console.group,re=console.groupCollapsed,Ve=console.groupEnd;var L={configurable:!0,enumerable:!0,value:et,writable:!0};Object.defineProperties(console,{info:L,log:L,warn:L,error:L,group:L,groupCollapsed:L,groupEnd:L})}kt++}}function ht(){{if(kt--,kt===0){var L={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:Bt({},L,{value:pt}),info:Bt({},L,{value:oe}),warn:Bt({},L,{value:Te}),error:Bt({},L,{value:be}),group:Bt({},L,{value:B}),groupCollapsed:Bt({},L,{value:re}),groupEnd:Bt({},L,{value:Ve})})}kt<0&&ue("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var Ot=de.ReactCurrentDispatcher,tt;function vt(L,ce,Ee){{if(tt===void 0)try{throw Error()}catch(Rt){var Re=Rt.stack.trim().match(/\n( *(at )?)/);tt=Re&&Re[1]||""}return`
`+tt+L}}var Ut=!1,pn;{var an=typeof WeakMap=="function"?WeakMap:Map;pn=new an}function Pn(L,ce){if(!L||Ut)return"";{var Ee=pn.get(L);if(Ee!==void 0)return Ee}var Re;Ut=!0;var Rt=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var ut;ut=Ot.current,Ot.current=null,it();try{if(ce){var $t=function(){throw Error()};if(Object.defineProperty($t.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct($t,[])}catch(kn){Re=kn}Reflect.construct(L,[],$t)}else{try{$t.call()}catch(kn){Re=kn}L.call($t.prototype)}}else{try{throw Error()}catch(kn){Re=kn}L()}}catch(kn){if(kn&&Re&&typeof kn.stack=="string"){for(var St=kn.stack.split(`
`),Fn=Re.stack.split(`
`),yn=St.length-1,Cn=Fn.length-1;yn>=1&&Cn>=0&&St[yn]!==Fn[Cn];)Cn--;for(;yn>=1&&Cn>=0;yn--,Cn--)if(St[yn]!==Fn[Cn]){if(yn!==1||Cn!==1)do if(yn--,Cn--,Cn<0||St[yn]!==Fn[Cn]){var Lr=`
`+St[yn].replace(" at new "," at ");return L.displayName&&Lr.includes("<anonymous>")&&(Lr=Lr.replace("<anonymous>",L.displayName)),typeof L=="function"&&pn.set(L,Lr),Lr}while(yn>=1&&Cn>=0);break}}}finally{Ut=!1,Ot.current=ut,ht(),Error.prepareStackTrace=Rt}var da=L?L.displayName||L.name:"",Kt=da?vt(da):"";return typeof L=="function"&&pn.set(L,Kt),Kt}function xn(L,ce,Ee){return Pn(L,!1)}function $n(L){var ce=L.prototype;return!!(ce&&ce.isReactComponent)}function er(L,ce,Ee){if(L==null)return"";if(typeof L=="function")return Pn(L,$n(L));if(typeof L=="string")return vt(L);switch(L){case O:return vt("Suspense");case P:return vt("SuspenseList")}if(typeof L=="object")switch(L.$$typeof){case $:return xn(L.render);case _:return er(L.type,ce,Ee);case V:{var Re=L,Rt=Re._payload,ut=Re._init;try{return er(ut(Rt),ce,Ee)}catch{}}}return""}var Yn=Object.prototype.hasOwnProperty,Ti={},ua=de.ReactDebugCurrentFrame;function Hr(L){if(L){var ce=L._owner,Ee=er(L.type,L._source,ce?ce.type:null);ua.setExtraStackFrame(Ee)}else ua.setExtraStackFrame(null)}function tr(L,ce,Ee,Re,Rt){{var ut=Function.call.bind(Yn);for(var $t in L)if(ut(L,$t)){var St=void 0;try{if(typeof L[$t]!="function"){var Fn=Error((Re||"React class")+": "+Ee+" type `"+$t+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof L[$t]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw Fn.name="Invariant Violation",Fn}St=L[$t](ce,$t,Re,Ee,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(yn){St=yn}St&&!(St instanceof Error)&&(Hr(Rt),ue("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",Re||"React class",Ee,$t,typeof St),Hr(null)),St instanceof Error&&!(St.message in Ti)&&(Ti[St.message]=!0,Hr(Rt),ue("Failed %s type: %s",Ee,St.message),Hr(null))}}}var sr=Array.isArray;function ur(L){return sr(L)}function _r(L){{var ce=typeof Symbol=="function"&&Symbol.toStringTag,Ee=ce&&L[Symbol.toStringTag]||L.constructor.name||"Object";return Ee}}function ca(L){try{return Gn(L),!1}catch{return!0}}function Gn(L){return""+L}function xr(L){if(ca(L))return ue("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",_r(L)),Gn(L)}var ai=de.ReactCurrentOwner,no={key:!0,ref:!0,__self:!0,__source:!0},ki,we;function Qe(L){if(Yn.call(L,"ref")){var ce=Object.getOwnPropertyDescriptor(L,"ref").get;if(ce&&ce.isReactWarning)return!1}return L.ref!==void 0}function wt(L){if(Yn.call(L,"key")){var ce=Object.getOwnPropertyDescriptor(L,"key").get;if(ce&&ce.isReactWarning)return!1}return L.key!==void 0}function Yt(L,ce){typeof L.ref=="string"&&ai.current}function bn(L,ce){{var Ee=function(){ki||(ki=!0,ue("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",ce))};Ee.isReactWarning=!0,Object.defineProperty(L,"key",{get:Ee,configurable:!0})}}function wn(L,ce){{var Ee=function(){we||(we=!0,ue("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",ce))};Ee.isReactWarning=!0,Object.defineProperty(L,"ref",{get:Ee,configurable:!0})}}var Sn=function(L,ce,Ee,Re,Rt,ut,$t){var St={$$typeof:s,type:L,key:ce,ref:Ee,props:$t,_owner:ut};return St._store={},Object.defineProperty(St._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(St,"_self",{configurable:!1,enumerable:!1,writable:!1,value:Re}),Object.defineProperty(St,"_source",{configurable:!1,enumerable:!1,writable:!1,value:Rt}),Object.freeze&&(Object.freeze(St.props),Object.freeze(St)),St};function cr(L,ce,Ee,Re,Rt){{var ut,$t={},St=null,Fn=null;Ee!==void 0&&(xr(Ee),St=""+Ee),wt(ce)&&(xr(ce.key),St=""+ce.key),Qe(ce)&&(Fn=ce.ref,Yt(ce,Rt));for(ut in ce)Yn.call(ce,ut)&&!no.hasOwnProperty(ut)&&($t[ut]=ce[ut]);if(L&&L.defaultProps){var yn=L.defaultProps;for(ut in yn)$t[ut]===void 0&&($t[ut]=yn[ut])}if(St||Fn){var Cn=typeof L=="function"?L.displayName||L.name||"Unknown":L;St&&bn($t,Cn),Fn&&wn($t,Cn)}return Sn(L,St,Fn,Rt,Re,ai.current,$t)}}var vn=de.ReactCurrentOwner,on=de.ReactDebugCurrentFrame;function Gt(L){if(L){var ce=L._owner,Ee=er(L.type,L._source,ce?ce.type:null);on.setExtraStackFrame(Ee)}else on.setExtraStackFrame(null)}var Ri;Ri=!1;function Wi(L){return typeof L=="object"&&L!==null&&L.$$typeof===s}function Yi(){{if(vn.current){var L=Be(vn.current.type);if(L)return`

Check the render method of \``+L+"`."}return""}}function ro(L){return""}var xl={};function bl(L){{var ce=Yi();if(!ce){var Ee=typeof L=="string"?L:L.displayName||L.name;Ee&&(ce=`

Check the top-level render call using <`+Ee+">.")}return ce}}function io(L,ce){{if(!L._store||L._store.validated||L.key!=null)return;L._store.validated=!0;var Ee=bl(ce);if(xl[Ee])return;xl[Ee]=!0;var Re="";L&&L._owner&&L._owner!==vn.current&&(Re=" It was passed a child from "+Be(L._owner.type)+"."),Gt(L),ue('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',Ee,Re),Gt(null)}}function wl(L,ce){{if(typeof L!="object")return;if(ur(L))for(var Ee=0;Ee<L.length;Ee++){var Re=L[Ee];Wi(Re)&&io(Re,ce)}else if(Wi(L))L._store&&(L._store.validated=!0);else if(L){var Rt=Pe(L);if(typeof Rt=="function"&&Rt!==L.entries)for(var ut=Rt.call(L),$t;!($t=ut.next()).done;)Wi($t.value)&&io($t.value,ce)}}}function $a(L){{var ce=L.type;if(ce==null||typeof ce=="string")return;var Ee;if(typeof ce=="function")Ee=ce.propTypes;else if(typeof ce=="object"&&(ce.$$typeof===$||ce.$$typeof===_))Ee=ce.propTypes;else return;if(Ee){var Re=Be(ce);tr(Ee,L.props,"prop",Re,L)}else if(ce.PropTypes!==void 0&&!Ri){Ri=!0;var Rt=Be(ce);ue("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",Rt||"Unknown")}typeof ce.getDefaultProps=="function"&&!ce.getDefaultProps.isReactClassApproved&&ue("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function Di(L){{for(var ce=Object.keys(L.props),Ee=0;Ee<ce.length;Ee++){var Re=ce[Ee];if(Re!=="children"&&Re!=="key"){Gt(L),ue("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",Re),Gt(null);break}}L.ref!==null&&(Gt(L),ue("Invalid attribute `ref` supplied to `React.Fragment`."),Gt(null))}}var br={};function Mi(L,ce,Ee,Re,Rt,ut){{var $t=Tt(L);if(!$t){var St="";(L===void 0||typeof L=="object"&&L!==null&&Object.keys(L).length===0)&&(St+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var Fn=ro();Fn?St+=Fn:St+=Yi();var yn;L===null?yn="null":ur(L)?yn="array":L!==void 0&&L.$$typeof===s?(yn="<"+(Be(L.type)||"Unknown")+" />",St=" Did you accidentally export a JSX literal instead of a component?"):yn=typeof L,ue("React.jsx: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",yn,St)}var Cn=cr(L,ce,Ee,Rt,ut);if(Cn==null)return Cn;if($t){var Lr=ce.children;if(Lr!==void 0)if(Re)if(ur(Lr)){for(var da=0;da<Lr.length;da++)wl(Lr[da],L);Object.freeze&&Object.freeze(Lr)}else ue("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");else wl(Lr,L)}if(Yn.call(ce,"key")){var Kt=Be(L),kn=Object.keys(ce).filter(function(oo){return oo!=="key"}),gt=kn.length>0?"{key: someKey, "+kn.join(": ..., ")+": ...}":"{key: someKey}";if(!br[Kt+gt]){var _a=kn.length>0?"{"+kn.join(": ..., ")+": ...}":"{}";ue(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,gt,Kt,_a,Kt),br[Kt+gt]=!0}}return L===g?Di(Cn):$a(Cn),Cn}}function oi(L,ce,Ee){return Mi(L,ce,Ee,!0)}function Aa(L,ce,Ee){return Mi(L,ce,Ee,!1)}var ja=Aa,ao=oi;ap.Fragment=g,ap.jsx=ja,ap.jsxs=ao}(),ap}var nD={};nD.NODE_ENV==="production"?qy.exports=eD():qy.exports=tD();var y=qy.exports,Zy={exports:{}},Ui={},Cg={exports:{}},Jy={};/**
 * @license React
 * scheduler.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var FS;function rD(){return FS||(FS=1,function(o){function r(oe,Te){var be=oe.length;oe.push(Te);e:for(;0<be;){var B=be-1>>>1,re=oe[B];if(0<g(re,Te))oe[B]=Te,oe[be]=re,be=B;else break e}}function s(oe){return oe.length===0?null:oe[0]}function d(oe){if(oe.length===0)return null;var Te=oe[0],be=oe.pop();if(be!==Te){oe[0]=be;e:for(var B=0,re=oe.length,Ve=re>>>1;B<Ve;){var et=2*(B+1)-1,it=oe[et],ht=et+1,Ot=oe[ht];if(0>g(it,be))ht<re&&0>g(Ot,it)?(oe[B]=Ot,oe[ht]=be,B=ht):(oe[B]=it,oe[et]=be,B=et);else if(ht<re&&0>g(Ot,be))oe[B]=Ot,oe[ht]=be,B=ht;else break e}}return Te}function g(oe,Te){var be=oe.sortIndex-Te.sortIndex;return be!==0?be:oe.id-Te.id}if(typeof performance=="object"&&typeof performance.now=="function"){var b=performance;o.unstable_now=function(){return b.now()}}else{var S=Date,v=S.now();o.unstable_now=function(){return S.now()-v}}var T=[],$=[],O=1,P=null,_=3,V=!1,N=!1,J=!1,xe=typeof setTimeout=="function"?setTimeout:null,Pe=typeof clearTimeout=="function"?clearTimeout:null,de=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function ue(oe){for(var Te=s($);Te!==null;){if(Te.callback===null)d($);else if(Te.startTime<=oe)d($),Te.sortIndex=Te.expirationTime,r(T,Te);else break;Te=s($)}}function he(oe){if(J=!1,ue(oe),!N)if(s(T)!==null)N=!0,kt(Le);else{var Te=s($);Te!==null&&pt(he,Te.startTime-oe)}}function Le(oe,Te){N=!1,J&&(J=!1,Pe(Oe),Oe=-1),V=!0;var be=_;try{for(ue(Te),P=s(T);P!==null&&(!(P.expirationTime>Te)||oe&&!Tt());){var B=P.callback;if(typeof B=="function"){P.callback=null,_=P.priorityLevel;var re=B(P.expirationTime<=Te);Te=o.unstable_now(),typeof re=="function"?P.callback=re:P===s(T)&&d(T),ue(Te)}else d(T);P=s(T)}if(P!==null)var Ve=!0;else{var et=s($);et!==null&&pt(he,et.startTime-Te),Ve=!1}return Ve}finally{P=null,_=be,V=!1}}var se=!1,le=null,Oe=-1,ft=5,He=-1;function Tt(){return!(o.unstable_now()-He<ft)}function bt(){if(le!==null){var oe=o.unstable_now();He=oe;var Te=!0;try{Te=le(!0,oe)}finally{Te?rt():(se=!1,le=null)}}else se=!1}var rt;if(typeof de=="function")rt=function(){de(bt)};else if(typeof MessageChannel<"u"){var Be=new MessageChannel,Bt=Be.port2;Be.port1.onmessage=bt,rt=function(){Bt.postMessage(null)}}else rt=function(){xe(bt,0)};function kt(oe){le=oe,se||(se=!0,rt())}function pt(oe,Te){Oe=xe(function(){oe(o.unstable_now())},Te)}o.unstable_IdlePriority=5,o.unstable_ImmediatePriority=1,o.unstable_LowPriority=4,o.unstable_NormalPriority=3,o.unstable_Profiling=null,o.unstable_UserBlockingPriority=2,o.unstable_cancelCallback=function(oe){oe.callback=null},o.unstable_continueExecution=function(){N||V||(N=!0,kt(Le))},o.unstable_forceFrameRate=function(oe){0>oe||125<oe?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):ft=0<oe?Math.floor(1e3/oe):5},o.unstable_getCurrentPriorityLevel=function(){return _},o.unstable_getFirstCallbackNode=function(){return s(T)},o.unstable_next=function(oe){switch(_){case 1:case 2:case 3:var Te=3;break;default:Te=_}var be=_;_=Te;try{return oe()}finally{_=be}},o.unstable_pauseExecution=function(){},o.unstable_requestPaint=function(){},o.unstable_runWithPriority=function(oe,Te){switch(oe){case 1:case 2:case 3:case 4:case 5:break;default:oe=3}var be=_;_=oe;try{return Te()}finally{_=be}},o.unstable_scheduleCallback=function(oe,Te,be){var B=o.unstable_now();switch(typeof be=="object"&&be!==null?(be=be.delay,be=typeof be=="number"&&0<be?B+be:B):be=B,oe){case 1:var re=-1;break;case 2:re=250;break;case 5:re=1073741823;break;case 4:re=1e4;break;default:re=5e3}return re=be+re,oe={id:O++,callback:Te,priorityLevel:oe,startTime:be,expirationTime:re,sortIndex:-1},be>B?(oe.sortIndex=be,r($,oe),s(T)===null&&oe===s($)&&(J?(Pe(Oe),Oe=-1):J=!0,pt(he,be-B))):(oe.sortIndex=re,r(T,oe),N||V||(N=!0,kt(Le))),oe},o.unstable_shouldYield=Tt,o.unstable_wrapCallback=function(oe){var Te=_;return function(){var be=_;_=Te;try{return oe.apply(this,arguments)}finally{_=be}}}}(Jy)),Jy}var ex={},IS;function iD(){return IS||(IS=1,function(o){var r={};/**
 * @license React
 * scheduler.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */r.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var s=!1,d=5;function g(we,Qe){var wt=we.length;we.push(Qe),v(we,Qe,wt)}function b(we){return we.length===0?null:we[0]}function S(we){if(we.length===0)return null;var Qe=we[0],wt=we.pop();return wt!==Qe&&(we[0]=wt,T(we,wt,0)),Qe}function v(we,Qe,wt){for(var Yt=wt;Yt>0;){var bn=Yt-1>>>1,wn=we[bn];if($(wn,Qe)>0)we[bn]=Qe,we[Yt]=wn,Yt=bn;else return}}function T(we,Qe,wt){for(var Yt=wt,bn=we.length,wn=bn>>>1;Yt<wn;){var Sn=(Yt+1)*2-1,cr=we[Sn],vn=Sn+1,on=we[vn];if($(cr,Qe)<0)vn<bn&&$(on,cr)<0?(we[Yt]=on,we[vn]=Qe,Yt=vn):(we[Yt]=cr,we[Sn]=Qe,Yt=Sn);else if(vn<bn&&$(on,Qe)<0)we[Yt]=on,we[vn]=Qe,Yt=vn;else return}}function $(we,Qe){var wt=we.sortIndex-Qe.sortIndex;return wt!==0?wt:we.id-Qe.id}var O=1,P=2,_=3,V=4,N=5;function J(we,Qe){}var xe=typeof performance=="object"&&typeof performance.now=="function";if(xe){var Pe=performance;o.unstable_now=function(){return Pe.now()}}else{var de=Date,ue=de.now();o.unstable_now=function(){return de.now()-ue}}var he=1073741823,Le=-1,se=250,le=5e3,Oe=1e4,ft=he,He=[],Tt=[],bt=1,rt=null,Be=_,Bt=!1,kt=!1,pt=!1,oe=typeof setTimeout=="function"?setTimeout:null,Te=typeof clearTimeout=="function"?clearTimeout:null,be=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function B(we){for(var Qe=b(Tt);Qe!==null;){if(Qe.callback===null)S(Tt);else if(Qe.startTime<=we)S(Tt),Qe.sortIndex=Qe.expirationTime,g(He,Qe);else return;Qe=b(Tt)}}function re(we){if(pt=!1,B(we),!kt)if(b(He)!==null)kt=!0,Gn(Ve);else{var Qe=b(Tt);Qe!==null&&xr(re,Qe.startTime-we)}}function Ve(we,Qe){kt=!1,pt&&(pt=!1,ai()),Bt=!0;var wt=Be;try{var Yt;if(!s)return et(we,Qe)}finally{rt=null,Be=wt,Bt=!1}}function et(we,Qe){var wt=Qe;for(B(wt),rt=b(He);rt!==null&&!(rt.expirationTime>wt&&(!we||ua()));){var Yt=rt.callback;if(typeof Yt=="function"){rt.callback=null,Be=rt.priorityLevel;var bn=rt.expirationTime<=wt,wn=Yt(bn);wt=o.unstable_now(),typeof wn=="function"?rt.callback=wn:rt===b(He)&&S(He),B(wt)}else S(He);rt=b(He)}if(rt!==null)return!0;var Sn=b(Tt);return Sn!==null&&xr(re,Sn.startTime-wt),!1}function it(we,Qe){switch(we){case O:case P:case _:case V:case N:break;default:we=_}var wt=Be;Be=we;try{return Qe()}finally{Be=wt}}function ht(we){var Qe;switch(Be){case O:case P:case _:Qe=_;break;default:Qe=Be;break}var wt=Be;Be=Qe;try{return we()}finally{Be=wt}}function Ot(we){var Qe=Be;return function(){var wt=Be;Be=Qe;try{return we.apply(this,arguments)}finally{Be=wt}}}function tt(we,Qe,wt){var Yt=o.unstable_now(),bn;if(typeof wt=="object"&&wt!==null){var wn=wt.delay;typeof wn=="number"&&wn>0?bn=Yt+wn:bn=Yt}else bn=Yt;var Sn;switch(we){case O:Sn=Le;break;case P:Sn=se;break;case N:Sn=ft;break;case V:Sn=Oe;break;case _:default:Sn=le;break}var cr=bn+Sn,vn={id:bt++,callback:Qe,priorityLevel:we,startTime:bn,expirationTime:cr,sortIndex:-1};return bn>Yt?(vn.sortIndex=bn,g(Tt,vn),b(He)===null&&vn===b(Tt)&&(pt?ai():pt=!0,xr(re,bn-Yt))):(vn.sortIndex=cr,g(He,vn),!kt&&!Bt&&(kt=!0,Gn(Ve))),vn}function vt(){}function Ut(){!kt&&!Bt&&(kt=!0,Gn(Ve))}function pn(){return b(He)}function an(we){we.callback=null}function Pn(){return Be}var xn=!1,$n=null,er=-1,Yn=d,Ti=-1;function ua(){var we=o.unstable_now()-Ti;return!(we<Yn)}function Hr(){}function tr(we){if(we<0||we>125){console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported");return}we>0?Yn=Math.floor(1e3/we):Yn=d}var sr=function(){if($n!==null){var we=o.unstable_now();Ti=we;var Qe=!0,wt=!0;try{wt=$n(Qe,we)}finally{wt?ur():(xn=!1,$n=null)}}else xn=!1},ur;if(typeof be=="function")ur=function(){be(sr)};else if(typeof MessageChannel<"u"){var _r=new MessageChannel,ca=_r.port2;_r.port1.onmessage=sr,ur=function(){ca.postMessage(null)}}else ur=function(){oe(sr,0)};function Gn(we){$n=we,xn||(xn=!0,ur())}function xr(we,Qe){er=oe(function(){we(o.unstable_now())},Qe)}function ai(){Te(er),er=-1}var no=Hr,ki=null;o.unstable_IdlePriority=N,o.unstable_ImmediatePriority=O,o.unstable_LowPriority=V,o.unstable_NormalPriority=_,o.unstable_Profiling=ki,o.unstable_UserBlockingPriority=P,o.unstable_cancelCallback=an,o.unstable_continueExecution=Ut,o.unstable_forceFrameRate=tr,o.unstable_getCurrentPriorityLevel=Pn,o.unstable_getFirstCallbackNode=pn,o.unstable_next=ht,o.unstable_pauseExecution=vt,o.unstable_requestPaint=no,o.unstable_runWithPriority=it,o.unstable_scheduleCallback=tt,o.unstable_shouldYield=ua,o.unstable_wrapCallback=Ot,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}()}(ex)),ex}var US;function BS(){if(US)return Cg.exports;US=1;var o={};return o.NODE_ENV==="production"?Cg.exports=rD():Cg.exports=iD(),Cg.exports}/**
 * @license React
 * react-dom.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var HS;function aD(){if(HS)return Ui;HS=1;var o=Ze,r=BS();function s(n){for(var i="https://reactjs.org/docs/error-decoder.html?invariant="+n,u=1;u<arguments.length;u++)i+="&args[]="+encodeURIComponent(arguments[u]);return"Minified React error #"+n+"; visit "+i+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var d=new Set,g={};function b(n,i){S(n,i),S(n+"Capture",i)}function S(n,i){for(g[n]=i,n=0;n<i.length;n++)d.add(i[n])}var v=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),T=Object.prototype.hasOwnProperty,$=/^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,O={},P={};function _(n){return T.call(P,n)?!0:T.call(O,n)?!1:$.test(n)?P[n]=!0:(O[n]=!0,!1)}function V(n,i,u,f){if(u!==null&&u.type===0)return!1;switch(typeof i){case"function":case"symbol":return!0;case"boolean":return f?!1:u!==null?!u.acceptsBooleans:(n=n.toLowerCase().slice(0,5),n!=="data-"&&n!=="aria-");default:return!1}}function N(n,i,u,f){if(i===null||typeof i>"u"||V(n,i,u,f))return!0;if(f)return!1;if(u!==null)switch(u.type){case 3:return!i;case 4:return i===!1;case 5:return isNaN(i);case 6:return isNaN(i)||1>i}return!1}function J(n,i,u,f,h,x,k){this.acceptsBooleans=i===2||i===3||i===4,this.attributeName=f,this.attributeNamespace=h,this.mustUseProperty=u,this.propertyName=n,this.type=i,this.sanitizeURL=x,this.removeEmptyString=k}var xe={};"children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(n){xe[n]=new J(n,0,!1,n,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(n){var i=n[0];xe[i]=new J(i,1,!1,n[1],null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(n){xe[n]=new J(n,2,!1,n.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(n){xe[n]=new J(n,2,!1,n,null,!1,!1)}),"allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(n){xe[n]=new J(n,3,!1,n.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(n){xe[n]=new J(n,3,!0,n,null,!1,!1)}),["capture","download"].forEach(function(n){xe[n]=new J(n,4,!1,n,null,!1,!1)}),["cols","rows","size","span"].forEach(function(n){xe[n]=new J(n,6,!1,n,null,!1,!1)}),["rowSpan","start"].forEach(function(n){xe[n]=new J(n,5,!1,n.toLowerCase(),null,!1,!1)});var Pe=/[\-:]([a-z])/g;function de(n){return n[1].toUpperCase()}"accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(n){var i=n.replace(Pe,de);xe[i]=new J(i,1,!1,n,null,!1,!1)}),"xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(n){var i=n.replace(Pe,de);xe[i]=new J(i,1,!1,n,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(n){var i=n.replace(Pe,de);xe[i]=new J(i,1,!1,n,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(n){xe[n]=new J(n,1,!1,n.toLowerCase(),null,!1,!1)}),xe.xlinkHref=new J("xlinkHref",1,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(n){xe[n]=new J(n,1,!1,n.toLowerCase(),null,!0,!0)});function ue(n,i,u,f){var h=xe.hasOwnProperty(i)?xe[i]:null;(h!==null?h.type!==0:f||!(2<i.length)||i[0]!=="o"&&i[0]!=="O"||i[1]!=="n"&&i[1]!=="N")&&(N(i,u,h,f)&&(u=null),f||h===null?_(i)&&(u===null?n.removeAttribute(i):n.setAttribute(i,""+u)):h.mustUseProperty?n[h.propertyName]=u===null?h.type===3?!1:"":u:(i=h.attributeName,f=h.attributeNamespace,u===null?n.removeAttribute(i):(h=h.type,u=h===3||h===4&&u===!0?"":""+u,f?n.setAttributeNS(f,i,u):n.setAttribute(i,u))))}var he=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,Le=Symbol.for("react.element"),se=Symbol.for("react.portal"),le=Symbol.for("react.fragment"),Oe=Symbol.for("react.strict_mode"),ft=Symbol.for("react.profiler"),He=Symbol.for("react.provider"),Tt=Symbol.for("react.context"),bt=Symbol.for("react.forward_ref"),rt=Symbol.for("react.suspense"),Be=Symbol.for("react.suspense_list"),Bt=Symbol.for("react.memo"),kt=Symbol.for("react.lazy"),pt=Symbol.for("react.offscreen"),oe=Symbol.iterator;function Te(n){return n===null||typeof n!="object"?null:(n=oe&&n[oe]||n["@@iterator"],typeof n=="function"?n:null)}var be=Object.assign,B;function re(n){if(B===void 0)try{throw Error()}catch(u){var i=u.stack.trim().match(/\n( *(at )?)/);B=i&&i[1]||""}return`
`+B+n}var Ve=!1;function et(n,i){if(!n||Ve)return"";Ve=!0;var u=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{if(i)if(i=function(){throw Error()},Object.defineProperty(i.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(i,[])}catch(X){var f=X}Reflect.construct(n,[],i)}else{try{i.call()}catch(X){f=X}n.call(i.prototype)}else{try{throw Error()}catch(X){f=X}n()}}catch(X){if(X&&f&&typeof X.stack=="string"){for(var h=X.stack.split(`
`),x=f.stack.split(`
`),k=h.length-1,A=x.length-1;1<=k&&0<=A&&h[k]!==x[A];)A--;for(;1<=k&&0<=A;k--,A--)if(h[k]!==x[A]){if(k!==1||A!==1)do if(k--,A--,0>A||h[k]!==x[A]){var z=`
`+h[k].replace(" at new "," at ");return n.displayName&&z.includes("<anonymous>")&&(z=z.replace("<anonymous>",n.displayName)),z}while(1<=k&&0<=A);break}}}finally{Ve=!1,Error.prepareStackTrace=u}return(n=n?n.displayName||n.name:"")?re(n):""}function it(n){switch(n.tag){case 5:return re(n.type);case 16:return re("Lazy");case 13:return re("Suspense");case 19:return re("SuspenseList");case 0:case 2:case 15:return n=et(n.type,!1),n;case 11:return n=et(n.type.render,!1),n;case 1:return n=et(n.type,!0),n;default:return""}}function ht(n){if(n==null)return null;if(typeof n=="function")return n.displayName||n.name||null;if(typeof n=="string")return n;switch(n){case le:return"Fragment";case se:return"Portal";case ft:return"Profiler";case Oe:return"StrictMode";case rt:return"Suspense";case Be:return"SuspenseList"}if(typeof n=="object")switch(n.$$typeof){case Tt:return(n.displayName||"Context")+".Consumer";case He:return(n._context.displayName||"Context")+".Provider";case bt:var i=n.render;return n=n.displayName,n||(n=i.displayName||i.name||"",n=n!==""?"ForwardRef("+n+")":"ForwardRef"),n;case Bt:return i=n.displayName||null,i!==null?i:ht(n.type)||"Memo";case kt:i=n._payload,n=n._init;try{return ht(n(i))}catch{}}return null}function Ot(n){var i=n.type;switch(n.tag){case 24:return"Cache";case 9:return(i.displayName||"Context")+".Consumer";case 10:return(i._context.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return n=i.render,n=n.displayName||n.name||"",i.displayName||(n!==""?"ForwardRef("+n+")":"ForwardRef");case 7:return"Fragment";case 5:return i;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return ht(i);case 8:return i===Oe?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 17:case 2:case 14:case 15:if(typeof i=="function")return i.displayName||i.name||null;if(typeof i=="string")return i}return null}function tt(n){switch(typeof n){case"boolean":case"number":case"string":case"undefined":return n;case"object":return n;default:return""}}function vt(n){var i=n.type;return(n=n.nodeName)&&n.toLowerCase()==="input"&&(i==="checkbox"||i==="radio")}function Ut(n){var i=vt(n)?"checked":"value",u=Object.getOwnPropertyDescriptor(n.constructor.prototype,i),f=""+n[i];if(!n.hasOwnProperty(i)&&typeof u<"u"&&typeof u.get=="function"&&typeof u.set=="function"){var h=u.get,x=u.set;return Object.defineProperty(n,i,{configurable:!0,get:function(){return h.call(this)},set:function(k){f=""+k,x.call(this,k)}}),Object.defineProperty(n,i,{enumerable:u.enumerable}),{getValue:function(){return f},setValue:function(k){f=""+k},stopTracking:function(){n._valueTracker=null,delete n[i]}}}}function pn(n){n._valueTracker||(n._valueTracker=Ut(n))}function an(n){if(!n)return!1;var i=n._valueTracker;if(!i)return!0;var u=i.getValue(),f="";return n&&(f=vt(n)?n.checked?"true":"false":n.value),n=f,n!==u?(i.setValue(n),!0):!1}function Pn(n){if(n=n||(typeof document<"u"?document:void 0),typeof n>"u")return null;try{return n.activeElement||n.body}catch{return n.body}}function xn(n,i){var u=i.checked;return be({},i,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:u??n._wrapperState.initialChecked})}function $n(n,i){var u=i.defaultValue==null?"":i.defaultValue,f=i.checked!=null?i.checked:i.defaultChecked;u=tt(i.value!=null?i.value:u),n._wrapperState={initialChecked:f,initialValue:u,controlled:i.type==="checkbox"||i.type==="radio"?i.checked!=null:i.value!=null}}function er(n,i){i=i.checked,i!=null&&ue(n,"checked",i,!1)}function Yn(n,i){er(n,i);var u=tt(i.value),f=i.type;if(u!=null)f==="number"?(u===0&&n.value===""||n.value!=u)&&(n.value=""+u):n.value!==""+u&&(n.value=""+u);else if(f==="submit"||f==="reset"){n.removeAttribute("value");return}i.hasOwnProperty("value")?ua(n,i.type,u):i.hasOwnProperty("defaultValue")&&ua(n,i.type,tt(i.defaultValue)),i.checked==null&&i.defaultChecked!=null&&(n.defaultChecked=!!i.defaultChecked)}function Ti(n,i,u){if(i.hasOwnProperty("value")||i.hasOwnProperty("defaultValue")){var f=i.type;if(!(f!=="submit"&&f!=="reset"||i.value!==void 0&&i.value!==null))return;i=""+n._wrapperState.initialValue,u||i===n.value||(n.value=i),n.defaultValue=i}u=n.name,u!==""&&(n.name=""),n.defaultChecked=!!n._wrapperState.initialChecked,u!==""&&(n.name=u)}function ua(n,i,u){(i!=="number"||Pn(n.ownerDocument)!==n)&&(u==null?n.defaultValue=""+n._wrapperState.initialValue:n.defaultValue!==""+u&&(n.defaultValue=""+u))}var Hr=Array.isArray;function tr(n,i,u,f){if(n=n.options,i){i={};for(var h=0;h<u.length;h++)i["$"+u[h]]=!0;for(u=0;u<n.length;u++)h=i.hasOwnProperty("$"+n[u].value),n[u].selected!==h&&(n[u].selected=h),h&&f&&(n[u].defaultSelected=!0)}else{for(u=""+tt(u),i=null,h=0;h<n.length;h++){if(n[h].value===u){n[h].selected=!0,f&&(n[h].defaultSelected=!0);return}i!==null||n[h].disabled||(i=n[h])}i!==null&&(i.selected=!0)}}function sr(n,i){if(i.dangerouslySetInnerHTML!=null)throw Error(s(91));return be({},i,{value:void 0,defaultValue:void 0,children:""+n._wrapperState.initialValue})}function ur(n,i){var u=i.value;if(u==null){if(u=i.children,i=i.defaultValue,u!=null){if(i!=null)throw Error(s(92));if(Hr(u)){if(1<u.length)throw Error(s(93));u=u[0]}i=u}i==null&&(i=""),u=i}n._wrapperState={initialValue:tt(u)}}function _r(n,i){var u=tt(i.value),f=tt(i.defaultValue);u!=null&&(u=""+u,u!==n.value&&(n.value=u),i.defaultValue==null&&n.defaultValue!==u&&(n.defaultValue=u)),f!=null&&(n.defaultValue=""+f)}function ca(n){var i=n.textContent;i===n._wrapperState.initialValue&&i!==""&&i!==null&&(n.value=i)}function Gn(n){switch(n){case"svg":return"http://www.w3.org/2000/svg";case"math":return"http://www.w3.org/1998/Math/MathML";default:return"http://www.w3.org/1999/xhtml"}}function xr(n,i){return n==null||n==="http://www.w3.org/1999/xhtml"?Gn(i):n==="http://www.w3.org/2000/svg"&&i==="foreignObject"?"http://www.w3.org/1999/xhtml":n}var ai,no=function(n){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(i,u,f,h){MSApp.execUnsafeLocalFunction(function(){return n(i,u,f,h)})}:n}(function(n,i){if(n.namespaceURI!=="http://www.w3.org/2000/svg"||"innerHTML"in n)n.innerHTML=i;else{for(ai=ai||document.createElement("div"),ai.innerHTML="<svg>"+i.valueOf().toString()+"</svg>",i=ai.firstChild;n.firstChild;)n.removeChild(n.firstChild);for(;i.firstChild;)n.appendChild(i.firstChild)}});function ki(n,i){if(i){var u=n.firstChild;if(u&&u===n.lastChild&&u.nodeType===3){u.nodeValue=i;return}}n.textContent=i}var we={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0},Qe=["Webkit","ms","Moz","O"];Object.keys(we).forEach(function(n){Qe.forEach(function(i){i=i+n.charAt(0).toUpperCase()+n.substring(1),we[i]=we[n]})});function wt(n,i,u){return i==null||typeof i=="boolean"||i===""?"":u||typeof i!="number"||i===0||we.hasOwnProperty(n)&&we[n]?(""+i).trim():i+"px"}function Yt(n,i){n=n.style;for(var u in i)if(i.hasOwnProperty(u)){var f=u.indexOf("--")===0,h=wt(u,i[u],f);u==="float"&&(u="cssFloat"),f?n.setProperty(u,h):n[u]=h}}var bn=be({menuitem:!0},{area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0});function wn(n,i){if(i){if(bn[n]&&(i.children!=null||i.dangerouslySetInnerHTML!=null))throw Error(s(137,n));if(i.dangerouslySetInnerHTML!=null){if(i.children!=null)throw Error(s(60));if(typeof i.dangerouslySetInnerHTML!="object"||!("__html"in i.dangerouslySetInnerHTML))throw Error(s(61))}if(i.style!=null&&typeof i.style!="object")throw Error(s(62))}}function Sn(n,i){if(n.indexOf("-")===-1)return typeof i.is=="string";switch(n){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var cr=null;function vn(n){return n=n.target||n.srcElement||window,n.correspondingUseElement&&(n=n.correspondingUseElement),n.nodeType===3?n.parentNode:n}var on=null,Gt=null,Ri=null;function Wi(n){if(n=ec(n)){if(typeof on!="function")throw Error(s(280));var i=n.stateNode;i&&(i=vo(i),on(n.stateNode,n.type,i))}}function Yi(n){Gt?Ri?Ri.push(n):Ri=[n]:Gt=n}function ro(){if(Gt){var n=Gt,i=Ri;if(Ri=Gt=null,Wi(n),i)for(n=0;n<i.length;n++)Wi(i[n])}}function xl(n,i){return n(i)}function bl(){}var io=!1;function wl(n,i,u){if(io)return n(i,u);io=!0;try{return xl(n,i,u)}finally{io=!1,(Gt!==null||Ri!==null)&&(bl(),ro())}}function $a(n,i){var u=n.stateNode;if(u===null)return null;var f=vo(u);if(f===null)return null;u=f[i];e:switch(i){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(f=!f.disabled)||(n=n.type,f=!(n==="button"||n==="input"||n==="select"||n==="textarea")),n=!f;break e;default:n=!1}if(n)return null;if(u&&typeof u!="function")throw Error(s(231,i,typeof u));return u}var Di=!1;if(v)try{var br={};Object.defineProperty(br,"passive",{get:function(){Di=!0}}),window.addEventListener("test",br,br),window.removeEventListener("test",br,br)}catch{Di=!1}function Mi(n,i,u,f,h,x,k,A,z){var X=Array.prototype.slice.call(arguments,3);try{i.apply(u,X)}catch(pe){this.onError(pe)}}var oi=!1,Aa=null,ja=!1,ao=null,L={onError:function(n){oi=!0,Aa=n}};function ce(n,i,u,f,h,x,k,A,z){oi=!1,Aa=null,Mi.apply(L,arguments)}function Ee(n,i,u,f,h,x,k,A,z){if(ce.apply(this,arguments),oi){if(oi){var X=Aa;oi=!1,Aa=null}else throw Error(s(198));ja||(ja=!0,ao=X)}}function Re(n){var i=n,u=n;if(n.alternate)for(;i.return;)i=i.return;else{n=i;do i=n,i.flags&4098&&(u=i.return),n=i.return;while(n)}return i.tag===3?u:null}function Rt(n){if(n.tag===13){var i=n.memoizedState;if(i===null&&(n=n.alternate,n!==null&&(i=n.memoizedState)),i!==null)return i.dehydrated}return null}function ut(n){if(Re(n)!==n)throw Error(s(188))}function $t(n){var i=n.alternate;if(!i){if(i=Re(n),i===null)throw Error(s(188));return i!==n?null:n}for(var u=n,f=i;;){var h=u.return;if(h===null)break;var x=h.alternate;if(x===null){if(f=h.return,f!==null){u=f;continue}break}if(h.child===x.child){for(x=h.child;x;){if(x===u)return ut(h),n;if(x===f)return ut(h),i;x=x.sibling}throw Error(s(188))}if(u.return!==f.return)u=h,f=x;else{for(var k=!1,A=h.child;A;){if(A===u){k=!0,u=h,f=x;break}if(A===f){k=!0,f=h,u=x;break}A=A.sibling}if(!k){for(A=x.child;A;){if(A===u){k=!0,u=x,f=h;break}if(A===f){k=!0,f=x,u=h;break}A=A.sibling}if(!k)throw Error(s(189))}}if(u.alternate!==f)throw Error(s(190))}if(u.tag!==3)throw Error(s(188));return u.stateNode.current===u?n:i}function St(n){return n=$t(n),n!==null?Fn(n):null}function Fn(n){if(n.tag===5||n.tag===6)return n;for(n=n.child;n!==null;){var i=Fn(n);if(i!==null)return i;n=n.sibling}return null}var yn=r.unstable_scheduleCallback,Cn=r.unstable_cancelCallback,Lr=r.unstable_shouldYield,da=r.unstable_requestPaint,Kt=r.unstable_now,kn=r.unstable_getCurrentPriorityLevel,gt=r.unstable_ImmediatePriority,_a=r.unstable_UserBlockingPriority,oo=r.unstable_NormalPriority,pd=r.unstable_LowPriority,lo=r.unstable_IdlePriority,jo=null,li=null;function ju(n){if(li&&typeof li.onCommitFiberRoot=="function")try{li.onCommitFiberRoot(jo,n,void 0,(n.current.flags&128)===128)}catch{}}var Vr=Math.clz32?Math.clz32:gd,_u=Math.log,hd=Math.LN2;function gd(n){return n>>>=0,n===0?32:31-(_u(n)/hd|0)|0}var so=64,_o=4194304;function si(n){switch(n&-n){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return n&4194240;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return n&130023424;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 1073741824;default:return n}}function La(n,i){var u=n.pendingLanes;if(u===0)return 0;var f=0,h=n.suspendedLanes,x=n.pingedLanes,k=u&268435455;if(k!==0){var A=k&~h;A!==0?f=si(A):(x&=k,x!==0&&(f=si(x)))}else k=u&~h,k!==0?f=si(k):x!==0&&(f=si(x));if(f===0)return 0;if(i!==0&&i!==f&&!(i&h)&&(h=f&-f,x=i&-i,h>=x||h===16&&(x&4194240)!==0))return i;if(f&4&&(f|=u&16),i=n.entangledLanes,i!==0)for(n=n.entanglements,i&=f;0<i;)u=31-Vr(i),h=1<<u,f|=n[u],i&=~h;return f}function Lo(n,i){switch(n){case 1:case 2:case 4:return i+250;case 8:case 16:case 32:case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return i+5e3;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return-1;case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function vs(n,i){for(var u=n.suspendedLanes,f=n.pingedLanes,h=n.expirationTimes,x=n.pendingLanes;0<x;){var k=31-Vr(x),A=1<<k,z=h[k];z===-1?(!(A&u)||A&f)&&(h[k]=Lo(A,i)):z<=i&&(n.expiredLanes|=A),x&=~A}}function uo(n){return n=n.pendingLanes&-1073741825,n!==0?n:n&1073741824?1073741824:0}function Sl(){var n=so;return so<<=1,!(so&4194240)&&(so=64),n}function Cl(n){for(var i=[],u=0;31>u;u++)i.push(n);return i}function zo(n,i,u){n.pendingLanes|=i,i!==536870912&&(n.suspendedLanes=0,n.pingedLanes=0),n=n.eventTimes,i=31-Vr(i),n[i]=u}function Lu(n,i){var u=n.pendingLanes&~i;n.pendingLanes=i,n.suspendedLanes=0,n.pingedLanes=0,n.expiredLanes&=i,n.mutableReadLanes&=i,n.entangledLanes&=i,i=n.entanglements;var f=n.eventTimes;for(n=n.expirationTimes;0<u;){var h=31-Vr(u),x=1<<h;i[h]=0,f[h]=-1,n[h]=-1,u&=~x}}function zu(n,i){var u=n.entangledLanes|=i;for(n=n.entanglements;u;){var f=31-Vr(u),h=1<<f;h&i|n[f]&i&&(n[f]|=i),u&=~h}}var Nt=0;function Nu(n){return n&=-n,1<n?4<n?n&268435455?16:536870912:4:1}var ys,Pt,md,za,lt,El=!1,dr=[],ui=null,Wr=null,Na=null,jn=new Map,ln=new Map,fa=[],Gi="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");function zr(n,i){switch(n){case"focusin":case"focusout":ui=null;break;case"dragenter":case"dragleave":Wr=null;break;case"mouseover":case"mouseout":Na=null;break;case"pointerover":case"pointerout":jn.delete(i.pointerId);break;case"gotpointercapture":case"lostpointercapture":ln.delete(i.pointerId)}}function Yr(n,i,u,f,h,x){return n===null||n.nativeEvent!==x?(n={blockedOn:i,domEventName:u,eventSystemFlags:f,nativeEvent:x,targetContainers:[h]},i!==null&&(i=ec(i),i!==null&&Pt(i)),n):(n.eventSystemFlags|=f,i=n.targetContainers,h!==null&&i.indexOf(h)===-1&&i.push(h),n)}function wp(n,i,u,f,h){switch(i){case"focusin":return ui=Yr(ui,n,i,u,f,h),!0;case"dragenter":return Wr=Yr(Wr,n,i,u,f,h),!0;case"mouseover":return Na=Yr(Na,n,i,u,f,h),!0;case"pointerover":var x=h.pointerId;return jn.set(x,Yr(jn.get(x)||null,n,i,u,f,h)),!0;case"gotpointercapture":return x=h.pointerId,ln.set(x,Yr(ln.get(x)||null,n,i,u,f,h)),!0}return!1}function xs(n){var i=jl(n.target);if(i!==null){var u=Re(i);if(u!==null){if(i=u.tag,i===13){if(i=Rt(u),i!==null){n.blockedOn=i,lt(n.priority,function(){md(u)});return}}else if(i===3&&u.stateNode.current.memoizedState.isDehydrated){n.blockedOn=u.tag===3?u.stateNode.containerInfo:null;return}}}n.blockedOn=null}function Tl(n){if(n.blockedOn!==null)return!1;for(var i=n.targetContainers;0<i.length;){var u=Cs(n.domEventName,n.eventSystemFlags,i[0],n.nativeEvent);if(u===null){u=n.nativeEvent;var f=new u.constructor(u.type,u);cr=f,u.target.dispatchEvent(f),cr=null}else return i=ec(u),i!==null&&Pt(i),n.blockedOn=u,!1;i.shift()}return!0}function bs(n,i,u){Tl(n)&&u.delete(i)}function ws(){El=!1,ui!==null&&Tl(ui)&&(ui=null),Wr!==null&&Tl(Wr)&&(Wr=null),Na!==null&&Tl(Na)&&(Na=null),jn.forEach(bs),ln.forEach(bs)}function kl(n,i){n.blockedOn===i&&(n.blockedOn=null,El||(El=!0,r.unstable_scheduleCallback(r.unstable_NormalPriority,ws)))}function Ki(n){function i(h){return kl(h,n)}if(0<dr.length){kl(dr[0],n);for(var u=1;u<dr.length;u++){var f=dr[u];f.blockedOn===n&&(f.blockedOn=null)}}for(ui!==null&&kl(ui,n),Wr!==null&&kl(Wr,n),Na!==null&&kl(Na,n),jn.forEach(i),ln.forEach(i),u=0;u<fa.length;u++)f=fa[u],f.blockedOn===n&&(f.blockedOn=null);for(;0<fa.length&&(u=fa[0],u.blockedOn===null);)xs(u),u.blockedOn===null&&fa.shift()}var Qi=he.ReactCurrentBatchConfig,No=!0;function co(n,i,u,f){var h=Nt,x=Qi.transition;Qi.transition=null;try{Nt=1,Po(n,i,u,f)}finally{Nt=h,Qi.transition=x}}function Ss(n,i,u,f){var h=Nt,x=Qi.transition;Qi.transition=null;try{Nt=4,Po(n,i,u,f)}finally{Nt=h,Qi.transition=x}}function Po(n,i,u,f){if(No){var h=Cs(n,i,u,f);if(h===null)_p(n,i,f,fo,u),zr(n,f);else if(wp(h,n,i,u,f))f.stopPropagation();else if(zr(n,f),i&4&&-1<Gi.indexOf(n)){for(;h!==null;){var x=ec(h);if(x!==null&&ys(x),x=Cs(n,i,u,f),x===null&&_p(n,i,f,fo,u),x===h)break;h=x}h!==null&&f.stopPropagation()}else _p(n,i,f,null,u)}}var fo=null;function Cs(n,i,u,f){if(fo=null,n=vn(f),n=jl(n),n!==null)if(i=Re(n),i===null)n=null;else if(u=i.tag,u===13){if(n=Rt(i),n!==null)return n;n=null}else if(u===3){if(i.stateNode.current.memoizedState.isDehydrated)return i.tag===3?i.stateNode.containerInfo:null;n=null}else i!==n&&(n=null);return fo=n,null}function Pu(n){switch(n){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return 1;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return 4;case"message":switch(kn()){case gt:return 1;case _a:return 4;case oo:case pd:return 16;case lo:return 536870912;default:return 16}default:return 16}}var qi=null,Es=null,E=null;function j(){if(E)return E;var n,i=Es,u=i.length,f,h="value"in qi?qi.value:qi.textContent,x=h.length;for(n=0;n<u&&i[n]===h[n];n++);var k=u-n;for(f=1;f<=k&&i[u-f]===h[x-f];f++);return E=h.slice(n,1<f?1-f:void 0)}function Q(n){var i=n.keyCode;return"charCode"in n?(n=n.charCode,n===0&&i===13&&(n=13)):n=i,n===10&&(n=13),32<=n||n===13?n:0}function ee(){return!0}function ye(){return!1}function Ne(n){function i(u,f,h,x,k){this._reactName=u,this._targetInst=h,this.type=f,this.nativeEvent=x,this.target=k,this.currentTarget=null;for(var A in n)n.hasOwnProperty(A)&&(u=n[A],this[A]=u?u(x):x[A]);return this.isDefaultPrevented=(x.defaultPrevented!=null?x.defaultPrevented:x.returnValue===!1)?ee:ye,this.isPropagationStopped=ye,this}return be(i.prototype,{preventDefault:function(){this.defaultPrevented=!0;var u=this.nativeEvent;u&&(u.preventDefault?u.preventDefault():typeof u.returnValue!="unknown"&&(u.returnValue=!1),this.isDefaultPrevented=ee)},stopPropagation:function(){var u=this.nativeEvent;u&&(u.stopPropagation?u.stopPropagation():typeof u.cancelBubble!="unknown"&&(u.cancelBubble=!0),this.isPropagationStopped=ee)},persist:function(){},isPersistent:ee}),i}var $e={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(n){return n.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},at=Ne($e),Ct=be({},$e,{view:0,detail:0}),Qt=Ne(Ct),sn,un,yt,hn=be({},Ct,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:pa,button:0,buttons:0,relatedTarget:function(n){return n.relatedTarget===void 0?n.fromElement===n.srcElement?n.toElement:n.fromElement:n.relatedTarget},movementX:function(n){return"movementX"in n?n.movementX:(n!==yt&&(yt&&n.type==="mousemove"?(sn=n.screenX-yt.screenX,un=n.screenY-yt.screenY):un=sn=0,yt=n),sn)},movementY:function(n){return"movementY"in n?n.movementY:un}}),In=Ne(hn),Rl=be({},hn,{dataTransfer:0}),Fu=Ne(Rl),po=be({},Ct,{relatedTarget:0}),Dl=Ne(po),Iu=be({},$e,{animationName:0,elapsedTime:0,pseudoElement:0}),Sp=Ne(Iu),vd=be({},$e,{clipboardData:function(n){return"clipboardData"in n?n.clipboardData:window.clipboardData}}),Cp=Ne(vd),am=be({},$e,{data:0}),yd=Ne(am),om={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},lm={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},sm={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function v0(n){var i=this.nativeEvent;return i.getModifierState?i.getModifierState(n):(n=sm[n])?!!i[n]:!1}function pa(){return v0}var y0=be({},Ct,{key:function(n){if(n.key){var i=om[n.key]||n.key;if(i!=="Unidentified")return i}return n.type==="keypress"?(n=Q(n),n===13?"Enter":String.fromCharCode(n)):n.type==="keydown"||n.type==="keyup"?lm[n.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:pa,charCode:function(n){return n.type==="keypress"?Q(n):0},keyCode:function(n){return n.type==="keydown"||n.type==="keyup"?n.keyCode:0},which:function(n){return n.type==="keypress"?Q(n):n.type==="keydown"||n.type==="keyup"?n.keyCode:0}}),Ep=Ne(y0),Tp=be({},hn,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),xd=Ne(Tp),x0=be({},Ct,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:pa}),bd=Ne(x0),um=be({},$e,{propertyName:0,elapsedTime:0,pseudoElement:0}),ci=Ne(um),ho=be({},hn,{deltaX:function(n){return"deltaX"in n?n.deltaX:"wheelDeltaX"in n?-n.wheelDeltaX:0},deltaY:function(n){return"deltaY"in n?n.deltaY:"wheelDeltaY"in n?-n.wheelDeltaY:"wheelDelta"in n?-n.wheelDelta:0},deltaZ:0,deltaMode:0}),Kn=Ne(ho),go=[9,13,27,32],Uu=v&&"CompositionEvent"in window,Fo=null;v&&"documentMode"in document&&(Fo=document.documentMode);var b0=v&&"TextEvent"in window&&!Fo,Ts=v&&(!Uu||Fo&&8<Fo&&11>=Fo),cm=" ",dm=!1;function wd(n,i){switch(n){case"keyup":return go.indexOf(i.keyCode)!==-1;case"keydown":return i.keyCode!==229;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function fm(n){return n=n.detail,typeof n=="object"&&"data"in n?n.data:null}var ks=!1;function w0(n,i){switch(n){case"compositionend":return fm(i);case"keypress":return i.which!==32?null:(dm=!0,cm);case"textInput":return n=i.data,n===cm&&dm?null:n;default:return null}}function pm(n,i){if(ks)return n==="compositionend"||!Uu&&wd(n,i)?(n=j(),E=Es=qi=null,ks=!1,n):null;switch(n){case"paste":return null;case"keypress":if(!(i.ctrlKey||i.altKey||i.metaKey)||i.ctrlKey&&i.altKey){if(i.char&&1<i.char.length)return i.char;if(i.which)return String.fromCharCode(i.which)}return null;case"compositionend":return Ts&&i.locale!=="ko"?null:i.data;default:return null}}var S0={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function hm(n){var i=n&&n.nodeName&&n.nodeName.toLowerCase();return i==="input"?!!S0[n.type]:i==="textarea"}function gm(n,i,u,f){Yi(f),i=Xu(i,"onChange"),0<i.length&&(u=new at("onChange","change",null,u,f),n.push({event:u,listeners:i}))}var Rs=null,Pa=null;function kp(n){Td(n,0)}function Bu(n){var i=Ye(n);if(an(i))return n}function mm(n,i){if(n==="change")return i}var vm=!1;if(v){var Rp;if(v){var Dp="oninput"in document;if(!Dp){var ym=document.createElement("div");ym.setAttribute("oninput","return;"),Dp=typeof ym.oninput=="function"}Rp=Dp}else Rp=!1;vm=Rp&&(!document.documentMode||9<document.documentMode)}function xm(){Rs&&(Rs.detachEvent("onpropertychange",bm),Pa=Rs=null)}function bm(n){if(n.propertyName==="value"&&Bu(Pa)){var i=[];gm(i,Pa,n,vn(n)),wl(kp,i)}}function C0(n,i,u){n==="focusin"?(xm(),Rs=i,Pa=u,Rs.attachEvent("onpropertychange",bm)):n==="focusout"&&xm()}function E0(n){if(n==="selectionchange"||n==="keyup"||n==="keydown")return Bu(Pa)}function wm(n,i){if(n==="click")return Bu(i)}function T0(n,i){if(n==="input"||n==="change")return Bu(i)}function Sm(n,i){return n===i&&(n!==0||1/n===1/i)||n!==n&&i!==i}var ha=typeof Object.is=="function"?Object.is:Sm;function Hu(n,i){if(ha(n,i))return!0;if(typeof n!="object"||n===null||typeof i!="object"||i===null)return!1;var u=Object.keys(n),f=Object.keys(i);if(u.length!==f.length)return!1;for(f=0;f<u.length;f++){var h=u[f];if(!T.call(i,h)||!ha(n[h],i[h]))return!1}return!0}function Cm(n){for(;n&&n.firstChild;)n=n.firstChild;return n}function Em(n,i){var u=Cm(n);n=0;for(var f;u;){if(u.nodeType===3){if(f=n+u.textContent.length,n<=i&&f>=i)return{node:u,offset:i-n};n=f}e:{for(;u;){if(u.nextSibling){u=u.nextSibling;break e}u=u.parentNode}u=void 0}u=Cm(u)}}function Sd(n,i){return n&&i?n===i?!0:n&&n.nodeType===3?!1:i&&i.nodeType===3?Sd(n,i.parentNode):"contains"in n?n.contains(i):n.compareDocumentPosition?!!(n.compareDocumentPosition(i)&16):!1:!1}function Io(){for(var n=window,i=Pn();i instanceof n.HTMLIFrameElement;){try{var u=typeof i.contentWindow.location.href=="string"}catch{u=!1}if(u)n=i.contentWindow;else break;i=Pn(n.document)}return i}function Ds(n){var i=n&&n.nodeName&&n.nodeName.toLowerCase();return i&&(i==="input"&&(n.type==="text"||n.type==="search"||n.type==="tel"||n.type==="url"||n.type==="password")||i==="textarea"||n.contentEditable==="true")}function Tm(n){var i=Io(),u=n.focusedElem,f=n.selectionRange;if(i!==u&&u&&u.ownerDocument&&Sd(u.ownerDocument.documentElement,u)){if(f!==null&&Ds(u)){if(i=f.start,n=f.end,n===void 0&&(n=i),"selectionStart"in u)u.selectionStart=i,u.selectionEnd=Math.min(n,u.value.length);else if(n=(i=u.ownerDocument||document)&&i.defaultView||window,n.getSelection){n=n.getSelection();var h=u.textContent.length,x=Math.min(f.start,h);f=f.end===void 0?x:Math.min(f.end,h),!n.extend&&x>f&&(h=f,f=x,x=h),h=Em(u,x);var k=Em(u,f);h&&k&&(n.rangeCount!==1||n.anchorNode!==h.node||n.anchorOffset!==h.offset||n.focusNode!==k.node||n.focusOffset!==k.offset)&&(i=i.createRange(),i.setStart(h.node,h.offset),n.removeAllRanges(),x>f?(n.addRange(i),n.extend(k.node,k.offset)):(i.setEnd(k.node,k.offset),n.addRange(i)))}}for(i=[],n=u;n=n.parentNode;)n.nodeType===1&&i.push({element:n,left:n.scrollLeft,top:n.scrollTop});for(typeof u.focus=="function"&&u.focus(),u=0;u<i.length;u++)n=i[u],n.element.scrollLeft=n.left,n.element.scrollTop=n.top}}var Ms=v&&"documentMode"in document&&11>=document.documentMode,Os=null,Mp=null,Vu=null,Op=!1;function km(n,i,u){var f=u.window===u?u.document:u.nodeType===9?u:u.ownerDocument;Op||Os==null||Os!==Pn(f)||(f=Os,"selectionStart"in f&&Ds(f)?f={start:f.selectionStart,end:f.selectionEnd}:(f=(f.ownerDocument&&f.ownerDocument.defaultView||window).getSelection(),f={anchorNode:f.anchorNode,anchorOffset:f.anchorOffset,focusNode:f.focusNode,focusOffset:f.focusOffset}),Vu&&Hu(Vu,f)||(Vu=f,f=Xu(Mp,"onSelect"),0<f.length&&(i=new at("onSelect","select",null,i,u),n.push({event:i,listeners:f}),i.target=Os)))}function Wu(n,i){var u={};return u[n.toLowerCase()]=i.toLowerCase(),u["Webkit"+n]="webkit"+i,u["Moz"+n]="moz"+i,u}var $s={animationend:Wu("Animation","AnimationEnd"),animationiteration:Wu("Animation","AnimationIteration"),animationstart:Wu("Animation","AnimationStart"),transitionend:Wu("Transition","TransitionEnd")},Cd={},Nr={};v&&(Nr=document.createElement("div").style,"AnimationEvent"in window||(delete $s.animationend.animation,delete $s.animationiteration.animation,delete $s.animationstart.animation),"TransitionEvent"in window||delete $s.transitionend.transition);function Yu(n){if(Cd[n])return Cd[n];if(!$s[n])return n;var i=$s[n],u;for(u in i)if(i.hasOwnProperty(u)&&u in Nr)return Cd[n]=i[u];return n}var Rm=Yu("animationend"),Dm=Yu("animationiteration"),Mm=Yu("animationstart"),Om=Yu("transitionend"),$m=new Map,$p="abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");function Fa(n,i){$m.set(n,i),b(i,[n])}for(var Ml=0;Ml<$p.length;Ml++){var Ap=$p[Ml],Gu=Ap.toLowerCase(),k0=Ap[0].toUpperCase()+Ap.slice(1);Fa(Gu,"on"+k0)}Fa(Rm,"onAnimationEnd"),Fa(Dm,"onAnimationIteration"),Fa(Mm,"onAnimationStart"),Fa("dblclick","onDoubleClick"),Fa("focusin","onFocus"),Fa("focusout","onBlur"),Fa(Om,"onTransitionEnd"),S("onMouseEnter",["mouseout","mouseover"]),S("onMouseLeave",["mouseout","mouseover"]),S("onPointerEnter",["pointerout","pointerover"]),S("onPointerLeave",["pointerout","pointerover"]),b("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),b("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),b("onBeforeInput",["compositionend","keypress","textInput","paste"]),b("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),b("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),b("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var Ku="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),R0=new Set("cancel close invalid load scroll toggle".split(" ").concat(Ku));function Ed(n,i,u){var f=n.type||"unknown-event";n.currentTarget=u,Ee(f,i,void 0,n),n.currentTarget=null}function Td(n,i){i=(i&4)!==0;for(var u=0;u<n.length;u++){var f=n[u],h=f.event;f=f.listeners;e:{var x=void 0;if(i)for(var k=f.length-1;0<=k;k--){var A=f[k],z=A.instance,X=A.currentTarget;if(A=A.listener,z!==x&&h.isPropagationStopped())break e;Ed(h,A,X),x=z}else for(k=0;k<f.length;k++){if(A=f[k],z=A.instance,X=A.currentTarget,A=A.listener,z!==x&&h.isPropagationStopped())break e;Ed(h,A,X),x=z}}}if(ja)throw n=ao,ja=!1,ao=null,n}function qt(n,i){var u=i[Lp];u===void 0&&(u=i[Lp]=new Set);var f=n+"__bubble";u.has(f)||(jp(i,n,2,!1),u.add(f))}function Uo(n,i,u){var f=0;i&&(f|=4),jp(u,n,f,i)}var Qu="_reactListening"+Math.random().toString(36).slice(2);function qu(n){if(!n[Qu]){n[Qu]=!0,d.forEach(function(u){u!=="selectionchange"&&(R0.has(u)||Uo(u,!1,n),Uo(u,!0,n))});var i=n.nodeType===9?n:n.ownerDocument;i===null||i[Qu]||(i[Qu]=!0,Uo("selectionchange",!1,i))}}function jp(n,i,u,f){switch(Pu(i)){case 1:var h=co;break;case 4:h=Ss;break;default:h=Po}u=h.bind(null,i,u,n),h=void 0,!Di||i!=="touchstart"&&i!=="touchmove"&&i!=="wheel"||(h=!0),f?h!==void 0?n.addEventListener(i,u,{capture:!0,passive:h}):n.addEventListener(i,u,!0):h!==void 0?n.addEventListener(i,u,{passive:h}):n.addEventListener(i,u,!1)}function _p(n,i,u,f,h){var x=f;if(!(i&1)&&!(i&2)&&f!==null)e:for(;;){if(f===null)return;var k=f.tag;if(k===3||k===4){var A=f.stateNode.containerInfo;if(A===h||A.nodeType===8&&A.parentNode===h)break;if(k===4)for(k=f.return;k!==null;){var z=k.tag;if((z===3||z===4)&&(z=k.stateNode.containerInfo,z===h||z.nodeType===8&&z.parentNode===h))return;k=k.return}for(;A!==null;){if(k=jl(A),k===null)return;if(z=k.tag,z===5||z===6){f=x=k;continue e}A=A.parentNode}}f=f.return}wl(function(){var X=x,pe=vn(u),ge=[];e:{var fe=$m.get(n);if(fe!==void 0){var Ae=at,Fe=n;switch(n){case"keypress":if(Q(u)===0)break e;case"keydown":case"keyup":Ae=Ep;break;case"focusin":Fe="focus",Ae=Dl;break;case"focusout":Fe="blur",Ae=Dl;break;case"beforeblur":case"afterblur":Ae=Dl;break;case"click":if(u.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":Ae=In;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":Ae=Fu;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":Ae=bd;break;case Rm:case Dm:case Mm:Ae=Sp;break;case Om:Ae=ci;break;case"scroll":Ae=Qt;break;case"wheel":Ae=Kn;break;case"copy":case"cut":case"paste":Ae=Cp;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":Ae=xd}var Ue=(i&4)!==0,Vn=!Ue&&n==="scroll",W=Ue?fe!==null?fe+"Capture":null:fe;Ue=[];for(var I=X,K;I!==null;){K=I;var ve=K.stateNode;if(K.tag===5&&ve!==null&&(K=ve,W!==null&&(ve=$a(I,W),ve!=null&&Ue.push(As(I,ve,K)))),Vn)break;I=I.return}0<Ue.length&&(fe=new Ae(fe,Fe,null,u,pe),ge.push({event:fe,listeners:Ue}))}}if(!(i&7)){e:{if(fe=n==="mouseover"||n==="pointerover",Ae=n==="mouseout"||n==="pointerout",fe&&u!==cr&&(Fe=u.relatedTarget||u.fromElement)&&(jl(Fe)||Fe[mo]))break e;if((Ae||fe)&&(fe=pe.window===pe?pe:(fe=pe.ownerDocument)?fe.defaultView||fe.parentWindow:window,Ae?(Fe=u.relatedTarget||u.toElement,Ae=X,Fe=Fe?jl(Fe):null,Fe!==null&&(Vn=Re(Fe),Fe!==Vn||Fe.tag!==5&&Fe.tag!==6)&&(Fe=null)):(Ae=null,Fe=X),Ae!==Fe)){if(Ue=In,ve="onMouseLeave",W="onMouseEnter",I="mouse",(n==="pointerout"||n==="pointerover")&&(Ue=xd,ve="onPointerLeave",W="onPointerEnter",I="pointer"),Vn=Ae==null?fe:Ye(Ae),K=Fe==null?fe:Ye(Fe),fe=new Ue(ve,I+"leave",Ae,u,pe),fe.target=Vn,fe.relatedTarget=K,ve=null,jl(pe)===X&&(Ue=new Ue(W,I+"enter",Fe,u,pe),Ue.target=K,Ue.relatedTarget=Vn,ve=Ue),Vn=ve,Ae&&Fe)t:{for(Ue=Ae,W=Fe,I=0,K=Ue;K;K=Ol(K))I++;for(K=0,ve=W;ve;ve=Ol(ve))K++;for(;0<I-K;)Ue=Ol(Ue),I--;for(;0<K-I;)W=Ol(W),K--;for(;I--;){if(Ue===W||W!==null&&Ue===W.alternate)break t;Ue=Ol(Ue),W=Ol(W)}Ue=null}else Ue=null;Ae!==null&&kd(ge,fe,Ae,Ue,!1),Fe!==null&&Vn!==null&&kd(ge,Vn,Fe,Ue,!0)}}e:{if(fe=X?Ye(X):window,Ae=fe.nodeName&&fe.nodeName.toLowerCase(),Ae==="select"||Ae==="input"&&fe.type==="file")var De=mm;else if(hm(fe))if(vm)De=T0;else{De=E0;var Ke=C0}else(Ae=fe.nodeName)&&Ae.toLowerCase()==="input"&&(fe.type==="checkbox"||fe.type==="radio")&&(De=wm);if(De&&(De=De(n,X))){gm(ge,De,u,pe);break e}Ke&&Ke(n,fe,X),n==="focusout"&&(Ke=fe._wrapperState)&&Ke.controlled&&fe.type==="number"&&ua(fe,"number",fe.value)}switch(Ke=X?Ye(X):window,n){case"focusin":(hm(Ke)||Ke.contentEditable==="true")&&(Os=Ke,Mp=X,Vu=null);break;case"focusout":Vu=Mp=Os=null;break;case"mousedown":Op=!0;break;case"contextmenu":case"mouseup":case"dragend":Op=!1,km(ge,u,pe);break;case"selectionchange":if(Ms)break;case"keydown":case"keyup":km(ge,u,pe)}var Je;if(Uu)e:{switch(n){case"compositionstart":var st="onCompositionStart";break e;case"compositionend":st="onCompositionEnd";break e;case"compositionupdate":st="onCompositionUpdate";break e}st=void 0}else ks?wd(n,u)&&(st="onCompositionEnd"):n==="keydown"&&u.keyCode===229&&(st="onCompositionStart");st&&(Ts&&u.locale!=="ko"&&(ks||st!=="onCompositionStart"?st==="onCompositionEnd"&&ks&&(Je=j()):(qi=pe,Es="value"in qi?qi.value:qi.textContent,ks=!0)),Ke=Xu(X,st),0<Ke.length&&(st=new yd(st,n,null,u,pe),ge.push({event:st,listeners:Ke}),Je?st.data=Je:(Je=fm(u),Je!==null&&(st.data=Je)))),(Je=b0?w0(n,u):pm(n,u))&&(X=Xu(X,"onBeforeInput"),0<X.length&&(pe=new yd("onBeforeInput","beforeinput",null,u,pe),ge.push({event:pe,listeners:X}),pe.data=Je))}Td(ge,i)})}function As(n,i,u){return{instance:n,listener:i,currentTarget:u}}function Xu(n,i){for(var u=i+"Capture",f=[];n!==null;){var h=n,x=h.stateNode;h.tag===5&&x!==null&&(h=x,x=$a(n,u),x!=null&&f.unshift(As(n,x,h)),x=$a(n,i),x!=null&&f.push(As(n,x,h))),n=n.return}return f}function Ol(n){if(n===null)return null;do n=n.return;while(n&&n.tag!==5);return n||null}function kd(n,i,u,f,h){for(var x=i._reactName,k=[];u!==null&&u!==f;){var A=u,z=A.alternate,X=A.stateNode;if(z!==null&&z===f)break;A.tag===5&&X!==null&&(A=X,h?(z=$a(u,x),z!=null&&k.unshift(As(u,z,A))):h||(z=$a(u,x),z!=null&&k.push(As(u,z,A)))),u=u.return}k.length!==0&&n.push({event:i,listeners:k})}var D0=/\r\n?/g,Am=/\u0000|\uFFFD/g;function jm(n){return(typeof n=="string"?n:""+n).replace(D0,`
`).replace(Am,"")}function Rd(n,i,u){if(i=jm(i),jm(n)!==i&&u)throw Error(s(425))}function Dd(){}var $l=null,Zu=null;function Al(n,i){return n==="textarea"||n==="noscript"||typeof i.children=="string"||typeof i.children=="number"||typeof i.dangerouslySetInnerHTML=="object"&&i.dangerouslySetInnerHTML!==null&&i.dangerouslySetInnerHTML.__html!=null}var Md=typeof setTimeout=="function"?setTimeout:void 0,_m=typeof clearTimeout=="function"?clearTimeout:void 0,Od=typeof Promise=="function"?Promise:void 0,M0=typeof queueMicrotask=="function"?queueMicrotask:typeof Od<"u"?function(n){return Od.resolve(null).then(n).catch(js)}:Md;function js(n){setTimeout(function(){throw n})}function _s(n,i){var u=i,f=0;do{var h=u.nextSibling;if(n.removeChild(u),h&&h.nodeType===8)if(u=h.data,u==="/$"){if(f===0){n.removeChild(h),Ki(i);return}f--}else u!=="$"&&u!=="$?"&&u!=="$!"||f++;u=h}while(u);Ki(i)}function ga(n){for(;n!=null;n=n.nextSibling){var i=n.nodeType;if(i===1||i===3)break;if(i===8){if(i=n.data,i==="$"||i==="$!"||i==="$?")break;if(i==="/$")return null}}return n}function $d(n){n=n.previousSibling;for(var i=0;n;){if(n.nodeType===8){var u=n.data;if(u==="$"||u==="$!"||u==="$?"){if(i===0)return n;i--}else u==="/$"&&i++}n=n.previousSibling}return null}var Ls=Math.random().toString(36).slice(2),Xi="__reactFiber$"+Ls,Ju="__reactProps$"+Ls,mo="__reactContainer$"+Ls,Lp="__reactEvents$"+Ls,zp="__reactListeners$"+Ls,zs="__reactHandles$"+Ls;function jl(n){var i=n[Xi];if(i)return i;for(var u=n.parentNode;u;){if(i=u[mo]||u[Xi]){if(u=i.alternate,i.child!==null||u!==null&&u.child!==null)for(n=$d(n);n!==null;){if(u=n[Xi])return u;n=$d(n)}return i}n=u,u=n.parentNode}return null}function ec(n){return n=n[Xi]||n[mo],!n||n.tag!==5&&n.tag!==6&&n.tag!==13&&n.tag!==3?null:n}function Ye(n){if(n.tag===5||n.tag===6)return n.stateNode;throw Error(s(33))}function vo(n){return n[Ju]||null}var _n=[],At=-1;function di(n){return{current:n}}function tn(n){0>At||(n.current=_n[At],_n[At]=null,At--)}function gn(n,i){At++,_n[At]=n.current,n.current=i}var Et={},Rn=di(Et),Qn=di(!1),Zi=Et;function Oi(n,i){var u=n.type.contextTypes;if(!u)return Et;var f=n.stateNode;if(f&&f.__reactInternalMemoizedUnmaskedChildContext===i)return f.__reactInternalMemoizedMaskedChildContext;var h={},x;for(x in u)h[x]=i[x];return f&&(n=n.stateNode,n.__reactInternalMemoizedUnmaskedChildContext=i,n.__reactInternalMemoizedMaskedChildContext=h),h}function Ln(n){return n=n.childContextTypes,n!=null}function Ia(){tn(Qn),tn(Rn)}function Ad(n,i,u){if(Rn.current!==Et)throw Error(s(168));gn(Rn,i),gn(Qn,u)}function Lm(n,i,u){var f=n.stateNode;if(i=i.childContextTypes,typeof f.getChildContext!="function")return u;f=f.getChildContext();for(var h in f)if(!(h in i))throw Error(s(108,Ot(n)||"Unknown",h));return be({},u,f)}function _l(n){return n=(n=n.stateNode)&&n.__reactInternalMemoizedMergedChildContext||Et,Zi=Rn.current,gn(Rn,n),gn(Qn,Qn.current),!0}function Pr(n,i,u){var f=n.stateNode;if(!f)throw Error(s(169));u?(n=Lm(n,i,Zi),f.__reactInternalMemoizedMergedChildContext=n,tn(Qn),tn(Rn),gn(Rn,n)):tn(Qn),gn(Qn,u)}var ma=null,tc=!1,nc=!1;function Bo(n){ma===null?ma=[n]:ma.push(n)}function Np(n){tc=!0,Bo(n)}function Gr(){if(!nc&&ma!==null){nc=!0;var n=0,i=Nt;try{var u=ma;for(Nt=1;n<u.length;n++){var f=u[n];do f=f(!0);while(f!==null)}ma=null,tc=!1}catch(h){throw ma!==null&&(ma=ma.slice(n+1)),yn(gt,Gr),h}finally{Nt=i,nc=!1}}return null}var Ho=[],Vo=0,Ns=null,Wo=0,wr=[],qn=0,Ll=null,Kr=1,Ua="";function Yo(n,i){Ho[Vo++]=Wo,Ho[Vo++]=Ns,Ns=n,Wo=i}function zm(n,i,u){wr[qn++]=Kr,wr[qn++]=Ua,wr[qn++]=Ll,Ll=n;var f=Kr;n=Ua;var h=32-Vr(f)-1;f&=~(1<<h),u+=1;var x=32-Vr(i)+h;if(30<x){var k=h-h%5;x=(f&(1<<k)-1).toString(32),f>>=k,h-=k,Kr=1<<32-Vr(i)+h|u<<h|f,Ua=x+n}else Kr=1<<x|u<<h|f,Ua=n}function Pp(n){n.return!==null&&(Yo(n,1),zm(n,1,0))}function jd(n){for(;n===Ns;)Ns=Ho[--Vo],Ho[Vo]=null,Wo=Ho[--Vo],Ho[Vo]=null;for(;n===Ll;)Ll=wr[--qn],wr[qn]=null,Ua=wr[--qn],wr[qn]=null,Kr=wr[--qn],wr[qn]=null}var fi=null,pi=null,En=!1,va=null;function Fp(n,i){var u=ra(5,null,null,0);u.elementType="DELETED",u.stateNode=i,u.return=n,i=n.deletions,i===null?(n.deletions=[u],n.flags|=16):i.push(u)}function Ip(n,i){switch(n.tag){case 5:var u=n.type;return i=i.nodeType!==1||u.toLowerCase()!==i.nodeName.toLowerCase()?null:i,i!==null?(n.stateNode=i,fi=n,pi=ga(i.firstChild),!0):!1;case 6:return i=n.pendingProps===""||i.nodeType!==3?null:i,i!==null?(n.stateNode=i,fi=n,pi=null,!0):!1;case 13:return i=i.nodeType!==8?null:i,i!==null?(u=Ll!==null?{id:Kr,overflow:Ua}:null,n.memoizedState={dehydrated:i,treeContext:u,retryLane:1073741824},u=ra(18,null,null,0),u.stateNode=i,u.return=n,n.child=u,fi=n,pi=null,!0):!1;default:return!1}}function Up(n){return(n.mode&1)!==0&&(n.flags&128)===0}function Bp(n){if(En){var i=pi;if(i){var u=i;if(!Ip(n,i)){if(Up(n))throw Error(s(418));i=ga(u.nextSibling);var f=fi;i&&Ip(n,i)?Fp(f,u):(n.flags=n.flags&-4097|2,En=!1,fi=n)}}else{if(Up(n))throw Error(s(418));n.flags=n.flags&-4097|2,En=!1,fi=n}}}function Nm(n){for(n=n.return;n!==null&&n.tag!==5&&n.tag!==3&&n.tag!==13;)n=n.return;fi=n}function Un(n){if(n!==fi)return!1;if(!En)return Nm(n),En=!0,!1;var i;if((i=n.tag!==3)&&!(i=n.tag!==5)&&(i=n.type,i=i!=="head"&&i!=="body"&&!Al(n.type,n.memoizedProps)),i&&(i=pi)){if(Up(n))throw Pm(),Error(s(418));for(;i;)Fp(n,i),i=ga(i.nextSibling)}if(Nm(n),n.tag===13){if(n=n.memoizedState,n=n!==null?n.dehydrated:null,!n)throw Error(s(317));e:{for(n=n.nextSibling,i=0;n;){if(n.nodeType===8){var u=n.data;if(u==="/$"){if(i===0){pi=ga(n.nextSibling);break e}i--}else u!=="$"&&u!=="$!"&&u!=="$?"||i++}n=n.nextSibling}pi=null}}else pi=fi?ga(n.stateNode.nextSibling):null;return!0}function Pm(){for(var n=pi;n;)n=ga(n.nextSibling)}function yo(){pi=fi=null,En=!1}function rc(n){va===null?va=[n]:va.push(n)}var zl=he.ReactCurrentBatchConfig;function ic(n,i,u){if(n=u.ref,n!==null&&typeof n!="function"&&typeof n!="object"){if(u._owner){if(u=u._owner,u){if(u.tag!==1)throw Error(s(309));var f=u.stateNode}if(!f)throw Error(s(147,n));var h=f,x=""+n;return i!==null&&i.ref!==null&&typeof i.ref=="function"&&i.ref._stringRef===x?i.ref:(i=function(k){var A=h.refs;k===null?delete A[x]:A[x]=k},i._stringRef=x,i)}if(typeof n!="string")throw Error(s(284));if(!u._owner)throw Error(s(290,n))}return n}function Ps(n,i){throw n=Object.prototype.toString.call(i),Error(s(31,n==="[object Object]"?"object with keys {"+Object.keys(i).join(", ")+"}":n))}function Fm(n){var i=n._init;return i(n._payload)}function Im(n){function i(W,I){if(n){var K=W.deletions;K===null?(W.deletions=[I],W.flags|=16):K.push(I)}}function u(W,I){if(!n)return null;for(;I!==null;)i(W,I),I=I.sibling;return null}function f(W,I){for(W=new Map;I!==null;)I.key!==null?W.set(I.key,I):W.set(I.index,I),I=I.sibling;return W}function h(W,I){return W=rl(W,I),W.index=0,W.sibling=null,W}function x(W,I,K){return W.index=K,n?(K=W.alternate,K!==null?(K=K.index,K<I?(W.flags|=2,I):K):(W.flags|=2,I)):(W.flags|=1048576,I)}function k(W){return n&&W.alternate===null&&(W.flags|=2),W}function A(W,I,K,ve){return I===null||I.tag!==6?(I=Jl(K,W.mode,ve),I.return=W,I):(I=h(I,K),I.return=W,I)}function z(W,I,K,ve){var De=K.type;return De===le?pe(W,I,K.props.children,ve,K.key):I!==null&&(I.elementType===De||typeof De=="object"&&De!==null&&De.$$typeof===kt&&Fm(De)===I.type)?(ve=h(I,K.props),ve.ref=ic(W,I,K),ve.return=W,ve):(ve=xf(K.type,K.key,K.props,null,W.mode,ve),ve.ref=ic(W,I,K),ve.return=W,ve)}function X(W,I,K,ve){return I===null||I.tag!==4||I.stateNode.containerInfo!==K.containerInfo||I.stateNode.implementation!==K.implementation?(I=xh(K,W.mode,ve),I.return=W,I):(I=h(I,K.children||[]),I.return=W,I)}function pe(W,I,K,ve,De){return I===null||I.tag!==7?(I=il(K,W.mode,ve,De),I.return=W,I):(I=h(I,K),I.return=W,I)}function ge(W,I,K){if(typeof I=="string"&&I!==""||typeof I=="number")return I=Jl(""+I,W.mode,K),I.return=W,I;if(typeof I=="object"&&I!==null){switch(I.$$typeof){case Le:return K=xf(I.type,I.key,I.props,null,W.mode,K),K.ref=ic(W,null,I),K.return=W,K;case se:return I=xh(I,W.mode,K),I.return=W,I;case kt:var ve=I._init;return ge(W,ve(I._payload),K)}if(Hr(I)||Te(I))return I=il(I,W.mode,K,null),I.return=W,I;Ps(W,I)}return null}function fe(W,I,K,ve){var De=I!==null?I.key:null;if(typeof K=="string"&&K!==""||typeof K=="number")return De!==null?null:A(W,I,""+K,ve);if(typeof K=="object"&&K!==null){switch(K.$$typeof){case Le:return K.key===De?z(W,I,K,ve):null;case se:return K.key===De?X(W,I,K,ve):null;case kt:return De=K._init,fe(W,I,De(K._payload),ve)}if(Hr(K)||Te(K))return De!==null?null:pe(W,I,K,ve,null);Ps(W,K)}return null}function Ae(W,I,K,ve,De){if(typeof ve=="string"&&ve!==""||typeof ve=="number")return W=W.get(K)||null,A(I,W,""+ve,De);if(typeof ve=="object"&&ve!==null){switch(ve.$$typeof){case Le:return W=W.get(ve.key===null?K:ve.key)||null,z(I,W,ve,De);case se:return W=W.get(ve.key===null?K:ve.key)||null,X(I,W,ve,De);case kt:var Ke=ve._init;return Ae(W,I,K,Ke(ve._payload),De)}if(Hr(ve)||Te(ve))return W=W.get(K)||null,pe(I,W,ve,De,null);Ps(I,ve)}return null}function Fe(W,I,K,ve){for(var De=null,Ke=null,Je=I,st=I=0,ar=null;Je!==null&&st<K.length;st++){Je.index>st?(ar=Je,Je=null):ar=Je.sibling;var Ht=fe(W,Je,K[st],ve);if(Ht===null){Je===null&&(Je=ar);break}n&&Je&&Ht.alternate===null&&i(W,Je),I=x(Ht,I,st),Ke===null?De=Ht:Ke.sibling=Ht,Ke=Ht,Je=ar}if(st===K.length)return u(W,Je),En&&Yo(W,st),De;if(Je===null){for(;st<K.length;st++)Je=ge(W,K[st],ve),Je!==null&&(I=x(Je,I,st),Ke===null?De=Je:Ke.sibling=Je,Ke=Je);return En&&Yo(W,st),De}for(Je=f(W,Je);st<K.length;st++)ar=Ae(Je,W,st,K[st],ve),ar!==null&&(n&&ar.alternate!==null&&Je.delete(ar.key===null?st:ar.key),I=x(ar,I,st),Ke===null?De=ar:Ke.sibling=ar,Ke=ar);return n&&Je.forEach(function(ol){return i(W,ol)}),En&&Yo(W,st),De}function Ue(W,I,K,ve){var De=Te(K);if(typeof De!="function")throw Error(s(150));if(K=De.call(K),K==null)throw Error(s(151));for(var Ke=De=null,Je=I,st=I=0,ar=null,Ht=K.next();Je!==null&&!Ht.done;st++,Ht=K.next()){Je.index>st?(ar=Je,Je=null):ar=Je.sibling;var ol=fe(W,Je,Ht.value,ve);if(ol===null){Je===null&&(Je=ar);break}n&&Je&&ol.alternate===null&&i(W,Je),I=x(ol,I,st),Ke===null?De=ol:Ke.sibling=ol,Ke=ol,Je=ar}if(Ht.done)return u(W,Je),En&&Yo(W,st),De;if(Je===null){for(;!Ht.done;st++,Ht=K.next())Ht=ge(W,Ht.value,ve),Ht!==null&&(I=x(Ht,I,st),Ke===null?De=Ht:Ke.sibling=Ht,Ke=Ht);return En&&Yo(W,st),De}for(Je=f(W,Je);!Ht.done;st++,Ht=K.next())Ht=Ae(Je,W,st,Ht.value,ve),Ht!==null&&(n&&Ht.alternate!==null&&Je.delete(Ht.key===null?st:Ht.key),I=x(Ht,I,st),Ke===null?De=Ht:Ke.sibling=Ht,Ke=Ht);return n&&Je.forEach(function(H0){return i(W,H0)}),En&&Yo(W,st),De}function Vn(W,I,K,ve){if(typeof K=="object"&&K!==null&&K.type===le&&K.key===null&&(K=K.props.children),typeof K=="object"&&K!==null){switch(K.$$typeof){case Le:e:{for(var De=K.key,Ke=I;Ke!==null;){if(Ke.key===De){if(De=K.type,De===le){if(Ke.tag===7){u(W,Ke.sibling),I=h(Ke,K.props.children),I.return=W,W=I;break e}}else if(Ke.elementType===De||typeof De=="object"&&De!==null&&De.$$typeof===kt&&Fm(De)===Ke.type){u(W,Ke.sibling),I=h(Ke,K.props),I.ref=ic(W,Ke,K),I.return=W,W=I;break e}u(W,Ke);break}else i(W,Ke);Ke=Ke.sibling}K.type===le?(I=il(K.props.children,W.mode,ve,K.key),I.return=W,W=I):(ve=xf(K.type,K.key,K.props,null,W.mode,ve),ve.ref=ic(W,I,K),ve.return=W,W=ve)}return k(W);case se:e:{for(Ke=K.key;I!==null;){if(I.key===Ke)if(I.tag===4&&I.stateNode.containerInfo===K.containerInfo&&I.stateNode.implementation===K.implementation){u(W,I.sibling),I=h(I,K.children||[]),I.return=W,W=I;break e}else{u(W,I);break}else i(W,I);I=I.sibling}I=xh(K,W.mode,ve),I.return=W,W=I}return k(W);case kt:return Ke=K._init,Vn(W,I,Ke(K._payload),ve)}if(Hr(K))return Fe(W,I,K,ve);if(Te(K))return Ue(W,I,K,ve);Ps(W,K)}return typeof K=="string"&&K!==""||typeof K=="number"?(K=""+K,I!==null&&I.tag===6?(u(W,I.sibling),I=h(I,K),I.return=W,W=I):(u(W,I),I=Jl(K,W.mode,ve),I.return=W,W=I),k(W)):u(W,I)}return Vn}var ya=Im(!0),Sr=Im(!1),Ce=di(null),$i=null,Fr=null,Hp=null;function Vp(){Hp=Fr=$i=null}function Wp(n){var i=Ce.current;tn(Ce),n._currentValue=i}function Yp(n,i,u){for(;n!==null;){var f=n.alternate;if((n.childLanes&i)!==i?(n.childLanes|=i,f!==null&&(f.childLanes|=i)):f!==null&&(f.childLanes&i)!==i&&(f.childLanes|=i),n===u)break;n=n.return}}function Fs(n,i){$i=n,Hp=Fr=null,n=n.dependencies,n!==null&&n.firstContext!==null&&(n.lanes&i&&(hr=!0),n.firstContext=null)}function nn(n){var i=n._currentValue;if(Hp!==n)if(n={context:n,memoizedValue:i,next:null},Fr===null){if($i===null)throw Error(s(308));Fr=n,$i.dependencies={lanes:0,firstContext:n}}else Fr=Fr.next=n;return i}var Nl=null;function Gp(n){Nl===null?Nl=[n]:Nl.push(n)}function Um(n,i,u,f){var h=i.interleaved;return h===null?(u.next=u,Gp(i)):(u.next=h.next,h.next=u),i.interleaved=u,Ba(n,f)}function Ba(n,i){n.lanes|=i;var u=n.alternate;for(u!==null&&(u.lanes|=i),u=n,n=n.return;n!==null;)n.childLanes|=i,u=n.alternate,u!==null&&(u.childLanes|=i),u=n,n=n.return;return u.tag===3?u.stateNode:null}var Ji=!1;function Go(n){n.updateQueue={baseState:n.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:0},effects:null}}function Bm(n,i){n=n.updateQueue,i.updateQueue===n&&(i.updateQueue={baseState:n.baseState,firstBaseUpdate:n.firstBaseUpdate,lastBaseUpdate:n.lastBaseUpdate,shared:n.shared,effects:n.effects})}function xo(n,i){return{eventTime:n,lane:i,tag:0,payload:null,callback:null,next:null}}function Ko(n,i,u){var f=n.updateQueue;if(f===null)return null;if(f=f.shared,jt&2){var h=f.pending;return h===null?i.next=i:(i.next=h.next,h.next=i),f.pending=i,Ba(n,u)}return h=f.interleaved,h===null?(i.next=i,Gp(f)):(i.next=h.next,h.next=i),f.interleaved=i,Ba(n,u)}function _d(n,i,u){if(i=i.updateQueue,i!==null&&(i=i.shared,(u&4194240)!==0)){var f=i.lanes;f&=n.pendingLanes,u|=f,i.lanes=u,zu(n,u)}}function Hm(n,i){var u=n.updateQueue,f=n.alternate;if(f!==null&&(f=f.updateQueue,u===f)){var h=null,x=null;if(u=u.firstBaseUpdate,u!==null){do{var k={eventTime:u.eventTime,lane:u.lane,tag:u.tag,payload:u.payload,callback:u.callback,next:null};x===null?h=x=k:x=x.next=k,u=u.next}while(u!==null);x===null?h=x=i:x=x.next=i}else h=x=i;u={baseState:f.baseState,firstBaseUpdate:h,lastBaseUpdate:x,shared:f.shared,effects:f.effects},n.updateQueue=u;return}n=u.lastBaseUpdate,n===null?u.firstBaseUpdate=i:n.next=i,u.lastBaseUpdate=i}function Ld(n,i,u,f){var h=n.updateQueue;Ji=!1;var x=h.firstBaseUpdate,k=h.lastBaseUpdate,A=h.shared.pending;if(A!==null){h.shared.pending=null;var z=A,X=z.next;z.next=null,k===null?x=X:k.next=X,k=z;var pe=n.alternate;pe!==null&&(pe=pe.updateQueue,A=pe.lastBaseUpdate,A!==k&&(A===null?pe.firstBaseUpdate=X:A.next=X,pe.lastBaseUpdate=z))}if(x!==null){var ge=h.baseState;k=0,pe=X=z=null,A=x;do{var fe=A.lane,Ae=A.eventTime;if((f&fe)===fe){pe!==null&&(pe=pe.next={eventTime:Ae,lane:0,tag:A.tag,payload:A.payload,callback:A.callback,next:null});e:{var Fe=n,Ue=A;switch(fe=i,Ae=u,Ue.tag){case 1:if(Fe=Ue.payload,typeof Fe=="function"){ge=Fe.call(Ae,ge,fe);break e}ge=Fe;break e;case 3:Fe.flags=Fe.flags&-65537|128;case 0:if(Fe=Ue.payload,fe=typeof Fe=="function"?Fe.call(Ae,ge,fe):Fe,fe==null)break e;ge=be({},ge,fe);break e;case 2:Ji=!0}}A.callback!==null&&A.lane!==0&&(n.flags|=64,fe=h.effects,fe===null?h.effects=[A]:fe.push(A))}else Ae={eventTime:Ae,lane:fe,tag:A.tag,payload:A.payload,callback:A.callback,next:null},pe===null?(X=pe=Ae,z=ge):pe=pe.next=Ae,k|=fe;if(A=A.next,A===null){if(A=h.shared.pending,A===null)break;fe=A,A=fe.next,fe.next=null,h.lastBaseUpdate=fe,h.shared.pending=null}}while(!0);if(pe===null&&(z=ge),h.baseState=z,h.firstBaseUpdate=X,h.lastBaseUpdate=pe,i=h.shared.interleaved,i!==null){h=i;do k|=h.lane,h=h.next;while(h!==i)}else x===null&&(h.shared.lanes=0);Gl|=k,n.lanes=k,n.memoizedState=ge}}function Kp(n,i,u){if(n=i.effects,i.effects=null,n!==null)for(i=0;i<n.length;i++){var f=n[i],h=f.callback;if(h!==null){if(f.callback=null,f=u,typeof h!="function")throw Error(s(191,h));h.call(f)}}}var Is={},Ha=di(Is),ac=di(Is),oc=di(Is);function Pl(n){if(n===Is)throw Error(s(174));return n}function Qp(n,i){switch(gn(oc,i),gn(ac,n),gn(Ha,Is),n=i.nodeType,n){case 9:case 11:i=(i=i.documentElement)?i.namespaceURI:xr(null,"");break;default:n=n===8?i.parentNode:i,i=n.namespaceURI||null,n=n.tagName,i=xr(i,n)}tn(Ha),gn(Ha,i)}function Us(){tn(Ha),tn(ac),tn(oc)}function qp(n){Pl(oc.current);var i=Pl(Ha.current),u=xr(i,n.type);i!==u&&(gn(ac,n),gn(Ha,u))}function Xp(n){ac.current===n&&(tn(Ha),tn(ac))}var Dn=di(0);function zd(n){for(var i=n;i!==null;){if(i.tag===13){var u=i.memoizedState;if(u!==null&&(u=u.dehydrated,u===null||u.data==="$?"||u.data==="$!"))return i}else if(i.tag===19&&i.memoizedProps.revealOrder!==void 0){if(i.flags&128)return i}else if(i.child!==null){i.child.return=i,i=i.child;continue}if(i===n)break;for(;i.sibling===null;){if(i.return===null||i.return===n)return null;i=i.return}i.sibling.return=i.return,i=i.sibling}return null}var Zp=[];function lc(){for(var n=0;n<Zp.length;n++)Zp[n]._workInProgressVersionPrimary=null;Zp.length=0}var Ge=he.ReactCurrentDispatcher,Dt=he.ReactCurrentBatchConfig,zt=0,mt=null,cn=null,nr=null,Nd=!1,sc=!1,uc=0,Jp=0;function ie(){throw Error(s(321))}function Xn(n,i){if(i===null)return!1;for(var u=0;u<i.length&&u<n.length;u++)if(!ha(n[u],i[u]))return!1;return!0}function nt(n,i,u,f,h,x){if(zt=x,mt=i,i.memoizedState=null,i.updateQueue=null,i.lanes=0,Ge.current=n===null||n.memoizedState===null?Zd:Jd,n=u(f,h),sc){x=0;do{if(sc=!1,uc=0,25<=x)throw Error(s(301));x+=1,nr=cn=null,i.updateQueue=null,Ge.current=hc,n=u(f,h)}while(sc)}if(Ge.current=rn,i=cn!==null&&cn.next!==null,zt=0,nr=cn=mt=null,Nd=!1,i)throw Error(s(300));return n}function Qo(){var n=uc!==0;return uc=0,n}function fr(){var n={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return nr===null?mt.memoizedState=nr=n:nr=nr.next=n,nr}function pr(){if(cn===null){var n=mt.alternate;n=n!==null?n.memoizedState:null}else n=cn.next;var i=nr===null?mt.memoizedState:nr.next;if(i!==null)nr=i,cn=n;else{if(n===null)throw Error(s(310));cn=n,n={memoizedState:cn.memoizedState,baseState:cn.baseState,baseQueue:cn.baseQueue,queue:cn.queue,next:null},nr===null?mt.memoizedState=nr=n:nr=nr.next=n}return nr}function hi(n,i){return typeof i=="function"?i(n):i}function Fl(n){var i=pr(),u=i.queue;if(u===null)throw Error(s(311));u.lastRenderedReducer=n;var f=cn,h=f.baseQueue,x=u.pending;if(x!==null){if(h!==null){var k=h.next;h.next=x.next,x.next=k}f.baseQueue=h=x,u.pending=null}if(h!==null){x=h.next,f=f.baseState;var A=k=null,z=null,X=x;do{var pe=X.lane;if((zt&pe)===pe)z!==null&&(z=z.next={lane:0,action:X.action,hasEagerState:X.hasEagerState,eagerState:X.eagerState,next:null}),f=X.hasEagerState?X.eagerState:n(f,X.action);else{var ge={lane:pe,action:X.action,hasEagerState:X.hasEagerState,eagerState:X.eagerState,next:null};z===null?(A=z=ge,k=f):z=z.next=ge,mt.lanes|=pe,Gl|=pe}X=X.next}while(X!==null&&X!==x);z===null?k=f:z.next=A,ha(f,i.memoizedState)||(hr=!0),i.memoizedState=f,i.baseState=k,i.baseQueue=z,u.lastRenderedState=f}if(n=u.interleaved,n!==null){h=n;do x=h.lane,mt.lanes|=x,Gl|=x,h=h.next;while(h!==n)}else h===null&&(u.lanes=0);return[i.memoizedState,u.dispatch]}function qo(n){var i=pr(),u=i.queue;if(u===null)throw Error(s(311));u.lastRenderedReducer=n;var f=u.dispatch,h=u.pending,x=i.memoizedState;if(h!==null){u.pending=null;var k=h=h.next;do x=n(x,k.action),k=k.next;while(k!==h);ha(x,i.memoizedState)||(hr=!0),i.memoizedState=x,i.baseQueue===null&&(i.baseState=x),u.lastRenderedState=x}return[x,f]}function Bs(){}function Pd(n,i){var u=mt,f=pr(),h=i(),x=!ha(f.memoizedState,h);if(x&&(f.memoizedState=h,hr=!0),f=f.queue,cc(Ud.bind(null,u,f,n),[n]),f.getSnapshot!==i||x||nr!==null&&nr.memoizedState.tag&1){if(u.flags|=2048,Il(9,Id.bind(null,u,f,h,i),void 0,null),Zn===null)throw Error(s(349));zt&30||Fd(u,i,h)}return h}function Fd(n,i,u){n.flags|=16384,n={getSnapshot:i,value:u},i=mt.updateQueue,i===null?(i={lastEffect:null,stores:null},mt.updateQueue=i,i.stores=[n]):(u=i.stores,u===null?i.stores=[n]:u.push(n))}function Id(n,i,u,f){i.value=u,i.getSnapshot=f,Bd(i)&&Hd(n)}function Ud(n,i,u){return u(function(){Bd(i)&&Hd(n)})}function Bd(n){var i=n.getSnapshot;n=n.value;try{var u=i();return!ha(n,u)}catch{return!0}}function Hd(n){var i=Ba(n,1);i!==null&&Li(i,n,1,-1)}function Vd(n){var i=fr();return typeof n=="function"&&(n=n()),i.memoizedState=i.baseState=n,n={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:hi,lastRenderedState:n},i.queue=n,n=n.dispatch=pc.bind(null,mt,n),[i.memoizedState,n]}function Il(n,i,u,f){return n={tag:n,create:i,destroy:u,deps:f,next:null},i=mt.updateQueue,i===null?(i={lastEffect:null,stores:null},mt.updateQueue=i,i.lastEffect=n.next=n):(u=i.lastEffect,u===null?i.lastEffect=n.next=n:(f=u.next,u.next=n,n.next=f,i.lastEffect=n)),n}function Wd(){return pr().memoizedState}function Hs(n,i,u,f){var h=fr();mt.flags|=n,h.memoizedState=Il(1|i,u,void 0,f===void 0?null:f)}function Vs(n,i,u,f){var h=pr();f=f===void 0?null:f;var x=void 0;if(cn!==null){var k=cn.memoizedState;if(x=k.destroy,f!==null&&Xn(f,k.deps)){h.memoizedState=Il(i,u,x,f);return}}mt.flags|=n,h.memoizedState=Il(1|i,u,x,f)}function Yd(n,i){return Hs(8390656,8,n,i)}function cc(n,i){return Vs(2048,8,n,i)}function Gd(n,i){return Vs(4,2,n,i)}function Kd(n,i){return Vs(4,4,n,i)}function dc(n,i){if(typeof i=="function")return n=n(),i(n),function(){i(null)};if(i!=null)return n=n(),i.current=n,function(){i.current=null}}function Ul(n,i,u){return u=u!=null?u.concat([n]):null,Vs(4,4,dc.bind(null,i,n),u)}function fc(){}function Qd(n,i){var u=pr();i=i===void 0?null:i;var f=u.memoizedState;return f!==null&&i!==null&&Xn(i,f[1])?f[0]:(u.memoizedState=[n,i],n)}function qd(n,i){var u=pr();i=i===void 0?null:i;var f=u.memoizedState;return f!==null&&i!==null&&Xn(i,f[1])?f[0]:(n=n(),u.memoizedState=[n,i],n)}function Xd(n,i,u){return zt&21?(ha(u,i)||(u=Sl(),mt.lanes|=u,Gl|=u,n.baseState=!0),i):(n.baseState&&(n.baseState=!1,hr=!0),n.memoizedState=u)}function Vm(n,i){var u=Nt;Nt=u!==0&&4>u?u:4,n(!0);var f=Dt.transition;Dt.transition={};try{n(!1),i()}finally{Nt=u,Dt.transition=f}}function Ws(){return pr().memoizedState}function Wm(n,i,u){var f=_i(n);if(u={lane:f,action:u,hasEagerState:!1,eagerState:null,next:null},Xo(n))gi(i,u);else if(u=Um(n,i,u,f),u!==null){var h=mn();Li(u,n,f,h),Ym(u,i,f)}}function pc(n,i,u){var f=_i(n),h={lane:f,action:u,hasEagerState:!1,eagerState:null,next:null};if(Xo(n))gi(i,h);else{var x=n.alternate;if(n.lanes===0&&(x===null||x.lanes===0)&&(x=i.lastRenderedReducer,x!==null))try{var k=i.lastRenderedState,A=x(k,u);if(h.hasEagerState=!0,h.eagerState=A,ha(A,k)){var z=i.interleaved;z===null?(h.next=h,Gp(i)):(h.next=z.next,z.next=h),i.interleaved=h;return}}catch{}finally{}u=Um(n,i,h,f),u!==null&&(h=mn(),Li(u,n,f,h),Ym(u,i,f))}}function Xo(n){var i=n.alternate;return n===mt||i!==null&&i===mt}function gi(n,i){sc=Nd=!0;var u=n.pending;u===null?i.next=i:(i.next=u.next,u.next=i),n.pending=i}function Ym(n,i,u){if(u&4194240){var f=i.lanes;f&=n.pendingLanes,u|=f,i.lanes=u,zu(n,u)}}var rn={readContext:nn,useCallback:ie,useContext:ie,useEffect:ie,useImperativeHandle:ie,useInsertionEffect:ie,useLayoutEffect:ie,useMemo:ie,useReducer:ie,useRef:ie,useState:ie,useDebugValue:ie,useDeferredValue:ie,useTransition:ie,useMutableSource:ie,useSyncExternalStore:ie,useId:ie,unstable_isNewReconciler:!1},Zd={readContext:nn,useCallback:function(n,i){return fr().memoizedState=[n,i===void 0?null:i],n},useContext:nn,useEffect:Yd,useImperativeHandle:function(n,i,u){return u=u!=null?u.concat([n]):null,Hs(4194308,4,dc.bind(null,i,n),u)},useLayoutEffect:function(n,i){return Hs(4194308,4,n,i)},useInsertionEffect:function(n,i){return Hs(4,2,n,i)},useMemo:function(n,i){var u=fr();return i=i===void 0?null:i,n=n(),u.memoizedState=[n,i],n},useReducer:function(n,i,u){var f=fr();return i=u!==void 0?u(i):i,f.memoizedState=f.baseState=i,n={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:n,lastRenderedState:i},f.queue=n,n=n.dispatch=Wm.bind(null,mt,n),[f.memoizedState,n]},useRef:function(n){var i=fr();return n={current:n},i.memoizedState=n},useState:Vd,useDebugValue:fc,useDeferredValue:function(n){return fr().memoizedState=n},useTransition:function(){var n=Vd(!1),i=n[0];return n=Vm.bind(null,n[1]),fr().memoizedState=n,[i,n]},useMutableSource:function(){},useSyncExternalStore:function(n,i,u){var f=mt,h=fr();if(En){if(u===void 0)throw Error(s(407));u=u()}else{if(u=i(),Zn===null)throw Error(s(349));zt&30||Fd(f,i,u)}h.memoizedState=u;var x={value:u,getSnapshot:i};return h.queue=x,Yd(Ud.bind(null,f,x,n),[n]),f.flags|=2048,Il(9,Id.bind(null,f,x,u,i),void 0,null),u},useId:function(){var n=fr(),i=Zn.identifierPrefix;if(En){var u=Ua,f=Kr;u=(f&~(1<<32-Vr(f)-1)).toString(32)+u,i=":"+i+"R"+u,u=uc++,0<u&&(i+="H"+u.toString(32)),i+=":"}else u=Jp++,i=":"+i+"r"+u.toString(32)+":";return n.memoizedState=i},unstable_isNewReconciler:!1},Jd={readContext:nn,useCallback:Qd,useContext:nn,useEffect:cc,useImperativeHandle:Ul,useInsertionEffect:Gd,useLayoutEffect:Kd,useMemo:qd,useReducer:Fl,useRef:Wd,useState:function(){return Fl(hi)},useDebugValue:fc,useDeferredValue:function(n){var i=pr();return Xd(i,cn.memoizedState,n)},useTransition:function(){var n=Fl(hi)[0],i=pr().memoizedState;return[n,i]},useMutableSource:Bs,useSyncExternalStore:Pd,useId:Ws,unstable_isNewReconciler:!1},hc={readContext:nn,useCallback:Qd,useContext:nn,useEffect:cc,useImperativeHandle:Ul,useInsertionEffect:Gd,useLayoutEffect:Kd,useMemo:qd,useReducer:qo,useRef:Wd,useState:function(){return qo(hi)},useDebugValue:fc,useDeferredValue:function(n){var i=pr();return cn===null?i.memoizedState=n:Xd(i,cn.memoizedState,n)},useTransition:function(){var n=qo(hi)[0],i=pr().memoizedState;return[n,i]},useMutableSource:Bs,useSyncExternalStore:Pd,useId:Ws,unstable_isNewReconciler:!1};function mi(n,i){if(n&&n.defaultProps){i=be({},i),n=n.defaultProps;for(var u in n)i[u]===void 0&&(i[u]=n[u]);return i}return i}function eh(n,i,u,f){i=n.memoizedState,u=u(f,i),u=u==null?i:be({},i,u),n.memoizedState=u,n.lanes===0&&(n.updateQueue.baseState=u)}var ef={isMounted:function(n){return(n=n._reactInternals)?Re(n)===n:!1},enqueueSetState:function(n,i,u){n=n._reactInternals;var f=mn(),h=_i(n),x=xo(f,h);x.payload=i,u!=null&&(x.callback=u),i=Ko(n,x,h),i!==null&&(Li(i,n,h,f),_d(i,n,h))},enqueueReplaceState:function(n,i,u){n=n._reactInternals;var f=mn(),h=_i(n),x=xo(f,h);x.tag=1,x.payload=i,u!=null&&(x.callback=u),i=Ko(n,x,h),i!==null&&(Li(i,n,h,f),_d(i,n,h))},enqueueForceUpdate:function(n,i){n=n._reactInternals;var u=mn(),f=_i(n),h=xo(u,f);h.tag=2,i!=null&&(h.callback=i),i=Ko(n,h,f),i!==null&&(Li(i,n,f,u),_d(i,n,f))}};function Gm(n,i,u,f,h,x,k){return n=n.stateNode,typeof n.shouldComponentUpdate=="function"?n.shouldComponentUpdate(f,x,k):i.prototype&&i.prototype.isPureReactComponent?!Hu(u,f)||!Hu(h,x):!0}function Km(n,i,u){var f=!1,h=Et,x=i.contextType;return typeof x=="object"&&x!==null?x=nn(x):(h=Ln(i)?Zi:Rn.current,f=i.contextTypes,x=(f=f!=null)?Oi(n,h):Et),i=new i(u,x),n.memoizedState=i.state!==null&&i.state!==void 0?i.state:null,i.updater=ef,n.stateNode=i,i._reactInternals=n,f&&(n=n.stateNode,n.__reactInternalMemoizedUnmaskedChildContext=h,n.__reactInternalMemoizedMaskedChildContext=x),i}function tf(n,i,u,f){n=i.state,typeof i.componentWillReceiveProps=="function"&&i.componentWillReceiveProps(u,f),typeof i.UNSAFE_componentWillReceiveProps=="function"&&i.UNSAFE_componentWillReceiveProps(u,f),i.state!==n&&ef.enqueueReplaceState(i,i.state,null)}function th(n,i,u,f){var h=n.stateNode;h.props=u,h.state=n.memoizedState,h.refs={},Go(n);var x=i.contextType;typeof x=="object"&&x!==null?h.context=nn(x):(x=Ln(i)?Zi:Rn.current,h.context=Oi(n,x)),h.state=n.memoizedState,x=i.getDerivedStateFromProps,typeof x=="function"&&(eh(n,i,x,u),h.state=n.memoizedState),typeof i.getDerivedStateFromProps=="function"||typeof h.getSnapshotBeforeUpdate=="function"||typeof h.UNSAFE_componentWillMount!="function"&&typeof h.componentWillMount!="function"||(i=h.state,typeof h.componentWillMount=="function"&&h.componentWillMount(),typeof h.UNSAFE_componentWillMount=="function"&&h.UNSAFE_componentWillMount(),i!==h.state&&ef.enqueueReplaceState(h,h.state,null),Ld(n,u,h,f),h.state=n.memoizedState),typeof h.componentDidMount=="function"&&(n.flags|=4194308)}function Zo(n,i){try{var u="",f=i;do u+=it(f),f=f.return;while(f);var h=u}catch(x){h=`
Error generating stack: `+x.message+`
`+x.stack}return{value:n,source:i,stack:h,digest:null}}function nf(n,i,u){return{value:n,source:null,stack:u??null,digest:i??null}}function nh(n,i){try{console.error(i.value)}catch(u){setTimeout(function(){throw u})}}var O0=typeof WeakMap=="function"?WeakMap:Map;function gc(n,i,u){u=xo(-1,u),u.tag=3,u.payload={element:null};var f=i.value;return u.callback=function(){el||(el=!0,Cc=f),nh(n,i)},u}function Qm(n,i,u){u=xo(-1,u),u.tag=3;var f=n.type.getDerivedStateFromError;if(typeof f=="function"){var h=i.value;u.payload=function(){return f(h)},u.callback=function(){nh(n,i)}}var x=n.stateNode;return x!==null&&typeof x.componentDidCatch=="function"&&(u.callback=function(){nh(n,i),typeof f!="function"&&(na===null?na=new Set([this]):na.add(this));var k=i.stack;this.componentDidCatch(i.value,{componentStack:k!==null?k:""})}),u}function rh(n,i,u){var f=n.pingCache;if(f===null){f=n.pingCache=new O0;var h=new Set;f.set(i,h)}else h=f.get(i),h===void 0&&(h=new Set,f.set(i,h));h.has(u)||(h.add(u),n=mh.bind(null,n,i,u),i.then(n,n))}function ih(n){do{var i;if((i=n.tag===13)&&(i=n.memoizedState,i=i!==null?i.dehydrated!==null:!0),i)return n;n=n.return}while(n!==null);return null}function qm(n,i,u,f,h){return n.mode&1?(n.flags|=65536,n.lanes=h,n):(n===i?n.flags|=65536:(n.flags|=128,u.flags|=131072,u.flags&=-52805,u.tag===1&&(u.alternate===null?u.tag=17:(i=xo(-1,1),i.tag=2,Ko(u,i,1))),u.lanes|=1),n)}var Bl=he.ReactCurrentOwner,hr=!1;function Bn(n,i,u,f){i.child=n===null?Sr(i,null,u,f):ya(i,n.child,u,f)}function rf(n,i,u,f,h){u=u.render;var x=i.ref;return Fs(i,h),f=nt(n,i,u,f,x,h),u=Qo(),n!==null&&!hr?(i.updateQueue=n.updateQueue,i.flags&=-2053,n.lanes&=~h,Cr(n,i,h)):(En&&u&&Pp(i),i.flags|=1,Bn(n,i,f,h),i.child)}function vi(n,i,u,f,h){if(n===null){var x=u.type;return typeof x=="function"&&!yh(x)&&x.defaultProps===void 0&&u.compare===null&&u.defaultProps===void 0?(i.tag=15,i.type=x,Hl(n,i,x,f,h)):(n=xf(u.type,null,f,i,i.mode,h),n.ref=i.ref,n.return=i,i.child=n)}if(x=n.child,!(n.lanes&h)){var k=x.memoizedProps;if(u=u.compare,u=u!==null?u:Hu,u(k,f)&&n.ref===i.ref)return Cr(n,i,h)}return i.flags|=1,n=rl(x,f),n.ref=i.ref,n.return=i,i.child=n}function Hl(n,i,u,f,h){if(n!==null){var x=n.memoizedProps;if(Hu(x,f)&&n.ref===i.ref)if(hr=!1,i.pendingProps=f=x,(n.lanes&h)!==0)n.flags&131072&&(hr=!0);else return i.lanes=n.lanes,Cr(n,i,h)}return af(n,i,u,f,h)}function xt(n,i,u){var f=i.pendingProps,h=f.children,x=n!==null?n.memoizedState:null;if(f.mode==="hidden")if(!(i.mode&1))i.memoizedState={baseLanes:0,cachePool:null,transitions:null},gn(Qs,ji),ji|=u;else{if(!(u&1073741824))return n=x!==null?x.baseLanes|u:u,i.lanes=i.childLanes=1073741824,i.memoizedState={baseLanes:n,cachePool:null,transitions:null},i.updateQueue=null,gn(Qs,ji),ji|=n,null;i.memoizedState={baseLanes:0,cachePool:null,transitions:null},f=x!==null?x.baseLanes:u,gn(Qs,ji),ji|=f}else x!==null?(f=x.baseLanes|u,i.memoizedState=null):f=u,gn(Qs,ji),ji|=f;return Bn(n,i,h,u),i.child}function mc(n,i){var u=i.ref;(n===null&&u!==null||n!==null&&n.ref!==u)&&(i.flags|=512,i.flags|=2097152)}function af(n,i,u,f,h){var x=Ln(u)?Zi:Rn.current;return x=Oi(i,x),Fs(i,h),u=nt(n,i,u,f,x,h),f=Qo(),n!==null&&!hr?(i.updateQueue=n.updateQueue,i.flags&=-2053,n.lanes&=~h,Cr(n,i,h)):(En&&f&&Pp(i),i.flags|=1,Bn(n,i,u,h),i.child)}function $0(n,i,u,f,h){if(Ln(u)){var x=!0;_l(i)}else x=!1;if(Fs(i,h),i.stateNode===null)ea(n,i),Km(i,u,f),th(i,u,f,h),f=!0;else if(n===null){var k=i.stateNode,A=i.memoizedProps;k.props=A;var z=k.context,X=u.contextType;typeof X=="object"&&X!==null?X=nn(X):(X=Ln(u)?Zi:Rn.current,X=Oi(i,X));var pe=u.getDerivedStateFromProps,ge=typeof pe=="function"||typeof k.getSnapshotBeforeUpdate=="function";ge||typeof k.UNSAFE_componentWillReceiveProps!="function"&&typeof k.componentWillReceiveProps!="function"||(A!==f||z!==X)&&tf(i,k,f,X),Ji=!1;var fe=i.memoizedState;k.state=fe,Ld(i,f,k,h),z=i.memoizedState,A!==f||fe!==z||Qn.current||Ji?(typeof pe=="function"&&(eh(i,u,pe,f),z=i.memoizedState),(A=Ji||Gm(i,u,A,f,fe,z,X))?(ge||typeof k.UNSAFE_componentWillMount!="function"&&typeof k.componentWillMount!="function"||(typeof k.componentWillMount=="function"&&k.componentWillMount(),typeof k.UNSAFE_componentWillMount=="function"&&k.UNSAFE_componentWillMount()),typeof k.componentDidMount=="function"&&(i.flags|=4194308)):(typeof k.componentDidMount=="function"&&(i.flags|=4194308),i.memoizedProps=f,i.memoizedState=z),k.props=f,k.state=z,k.context=X,f=A):(typeof k.componentDidMount=="function"&&(i.flags|=4194308),f=!1)}else{k=i.stateNode,Bm(n,i),A=i.memoizedProps,X=i.type===i.elementType?A:mi(i.type,A),k.props=X,ge=i.pendingProps,fe=k.context,z=u.contextType,typeof z=="object"&&z!==null?z=nn(z):(z=Ln(u)?Zi:Rn.current,z=Oi(i,z));var Ae=u.getDerivedStateFromProps;(pe=typeof Ae=="function"||typeof k.getSnapshotBeforeUpdate=="function")||typeof k.UNSAFE_componentWillReceiveProps!="function"&&typeof k.componentWillReceiveProps!="function"||(A!==ge||fe!==z)&&tf(i,k,f,z),Ji=!1,fe=i.memoizedState,k.state=fe,Ld(i,f,k,h);var Fe=i.memoizedState;A!==ge||fe!==Fe||Qn.current||Ji?(typeof Ae=="function"&&(eh(i,u,Ae,f),Fe=i.memoizedState),(X=Ji||Gm(i,u,X,f,fe,Fe,z)||!1)?(pe||typeof k.UNSAFE_componentWillUpdate!="function"&&typeof k.componentWillUpdate!="function"||(typeof k.componentWillUpdate=="function"&&k.componentWillUpdate(f,Fe,z),typeof k.UNSAFE_componentWillUpdate=="function"&&k.UNSAFE_componentWillUpdate(f,Fe,z)),typeof k.componentDidUpdate=="function"&&(i.flags|=4),typeof k.getSnapshotBeforeUpdate=="function"&&(i.flags|=1024)):(typeof k.componentDidUpdate!="function"||A===n.memoizedProps&&fe===n.memoizedState||(i.flags|=4),typeof k.getSnapshotBeforeUpdate!="function"||A===n.memoizedProps&&fe===n.memoizedState||(i.flags|=1024),i.memoizedProps=f,i.memoizedState=Fe),k.props=f,k.state=Fe,k.context=z,f=X):(typeof k.componentDidUpdate!="function"||A===n.memoizedProps&&fe===n.memoizedState||(i.flags|=4),typeof k.getSnapshotBeforeUpdate!="function"||A===n.memoizedProps&&fe===n.memoizedState||(i.flags|=1024),f=!1)}return ah(n,i,u,f,x,h)}function ah(n,i,u,f,h,x){mc(n,i);var k=(i.flags&128)!==0;if(!f&&!k)return h&&Pr(i,u,!1),Cr(n,i,x);f=i.stateNode,Bl.current=i;var A=k&&typeof u.getDerivedStateFromError!="function"?null:f.render();return i.flags|=1,n!==null&&k?(i.child=ya(i,n.child,null,x),i.child=ya(i,null,A,x)):Bn(n,i,A,x),i.memoizedState=f.state,h&&Pr(i,u,!0),i.child}function of(n){var i=n.stateNode;i.pendingContext?Ad(n,i.pendingContext,i.pendingContext!==i.context):i.context&&Ad(n,i.context,!1),Qp(n,i.containerInfo)}function Ys(n,i,u,f,h){return yo(),rc(h),i.flags|=256,Bn(n,i,u,f),i.child}var oh={dehydrated:null,treeContext:null,retryLane:0};function lf(n){return{baseLanes:n,cachePool:null,transitions:null}}function Xm(n,i,u){var f=i.pendingProps,h=Dn.current,x=!1,k=(i.flags&128)!==0,A;if((A=k)||(A=n!==null&&n.memoizedState===null?!1:(h&2)!==0),A?(x=!0,i.flags&=-129):(n===null||n.memoizedState!==null)&&(h|=1),gn(Dn,h&1),n===null)return Bp(i),n=i.memoizedState,n!==null&&(n=n.dehydrated,n!==null)?(i.mode&1?n.data==="$!"?i.lanes=8:i.lanes=1073741824:i.lanes=1,null):(k=f.children,n=f.fallback,x?(f=i.mode,x=i.child,k={mode:"hidden",children:k},!(f&1)&&x!==null?(x.childLanes=0,x.pendingProps=k):x=tu(k,f,0,null),n=il(n,f,u,null),x.return=i,n.return=i,x.sibling=n,i.child=x,i.child.memoizedState=lf(u),i.memoizedState=oh,n):vc(i,k));if(h=n.memoizedState,h!==null&&(A=h.dehydrated,A!==null))return Zm(n,i,k,f,A,h,u);if(x){x=f.fallback,k=i.mode,h=n.child,A=h.sibling;var z={mode:"hidden",children:f.children};return!(k&1)&&i.child!==h?(f=i.child,f.childLanes=0,f.pendingProps=z,i.deletions=null):(f=rl(h,z),f.subtreeFlags=h.subtreeFlags&14680064),A!==null?x=rl(A,x):(x=il(x,k,u,null),x.flags|=2),x.return=i,f.return=i,f.sibling=x,i.child=f,f=x,x=i.child,k=n.child.memoizedState,k=k===null?lf(u):{baseLanes:k.baseLanes|u,cachePool:null,transitions:k.transitions},x.memoizedState=k,x.childLanes=n.childLanes&~u,i.memoizedState=oh,f}return x=n.child,n=x.sibling,f=rl(x,{mode:"visible",children:f.children}),!(i.mode&1)&&(f.lanes=u),f.return=i,f.sibling=null,n!==null&&(u=i.deletions,u===null?(i.deletions=[n],i.flags|=16):u.push(n)),i.child=f,i.memoizedState=null,f}function vc(n,i){return i=tu({mode:"visible",children:i},n.mode,0,null),i.return=n,n.child=i}function sf(n,i,u,f){return f!==null&&rc(f),ya(i,n.child,null,u),n=vc(i,i.pendingProps.children),n.flags|=2,i.memoizedState=null,n}function Zm(n,i,u,f,h,x,k){if(u)return i.flags&256?(i.flags&=-257,f=nf(Error(s(422))),sf(n,i,k,f)):i.memoizedState!==null?(i.child=n.child,i.flags|=128,null):(x=f.fallback,h=i.mode,f=tu({mode:"visible",children:f.children},h,0,null),x=il(x,h,k,null),x.flags|=2,f.return=i,x.return=i,f.sibling=x,i.child=f,i.mode&1&&ya(i,n.child,null,k),i.child.memoizedState=lf(k),i.memoizedState=oh,x);if(!(i.mode&1))return sf(n,i,k,null);if(h.data==="$!"){if(f=h.nextSibling&&h.nextSibling.dataset,f)var A=f.dgst;return f=A,x=Error(s(419)),f=nf(x,f,void 0),sf(n,i,k,f)}if(A=(k&n.childLanes)!==0,hr||A){if(f=Zn,f!==null){switch(k&-k){case 4:h=2;break;case 16:h=8;break;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:h=32;break;case 536870912:h=268435456;break;default:h=0}h=h&(f.suspendedLanes|k)?0:h,h!==0&&h!==x.retryLane&&(x.retryLane=h,Ba(n,h),Li(f,n,h,-1))}return hh(),f=nf(Error(s(421))),sf(n,i,k,f)}return h.data==="$?"?(i.flags|=128,i.child=n.child,i=N0.bind(null,n),h._reactRetry=i,null):(n=x.treeContext,pi=ga(h.nextSibling),fi=i,En=!0,va=null,n!==null&&(wr[qn++]=Kr,wr[qn++]=Ua,wr[qn++]=Ll,Kr=n.id,Ua=n.overflow,Ll=i),i=vc(i,f.children),i.flags|=4096,i)}function lh(n,i,u){n.lanes|=i;var f=n.alternate;f!==null&&(f.lanes|=i),Yp(n.return,i,u)}function uf(n,i,u,f,h){var x=n.memoizedState;x===null?n.memoizedState={isBackwards:i,rendering:null,renderingStartTime:0,last:f,tail:u,tailMode:h}:(x.isBackwards=i,x.rendering=null,x.renderingStartTime=0,x.last=f,x.tail=u,x.tailMode=h)}function yi(n,i,u){var f=i.pendingProps,h=f.revealOrder,x=f.tail;if(Bn(n,i,f.children,u),f=Dn.current,f&2)f=f&1|2,i.flags|=128;else{if(n!==null&&n.flags&128)e:for(n=i.child;n!==null;){if(n.tag===13)n.memoizedState!==null&&lh(n,u,i);else if(n.tag===19)lh(n,u,i);else if(n.child!==null){n.child.return=n,n=n.child;continue}if(n===i)break e;for(;n.sibling===null;){if(n.return===null||n.return===i)break e;n=n.return}n.sibling.return=n.return,n=n.sibling}f&=1}if(gn(Dn,f),!(i.mode&1))i.memoizedState=null;else switch(h){case"forwards":for(u=i.child,h=null;u!==null;)n=u.alternate,n!==null&&zd(n)===null&&(h=u),u=u.sibling;u=h,u===null?(h=i.child,i.child=null):(h=u.sibling,u.sibling=null),uf(i,!1,h,u,x);break;case"backwards":for(u=null,h=i.child,i.child=null;h!==null;){if(n=h.alternate,n!==null&&zd(n)===null){i.child=h;break}n=h.sibling,h.sibling=u,u=h,h=n}uf(i,!0,u,null,x);break;case"together":uf(i,!1,null,null,void 0);break;default:i.memoizedState=null}return i.child}function ea(n,i){!(i.mode&1)&&n!==null&&(n.alternate=null,i.alternate=null,i.flags|=2)}function Cr(n,i,u){if(n!==null&&(i.dependencies=n.dependencies),Gl|=i.lanes,!(u&i.childLanes))return null;if(n!==null&&i.child!==n.child)throw Error(s(153));if(i.child!==null){for(n=i.child,u=rl(n,n.pendingProps),i.child=u,u.return=i;n.sibling!==null;)n=n.sibling,u=u.sibling=rl(n,n.pendingProps),u.return=i;u.sibling=null}return i.child}function cf(n,i,u){switch(i.tag){case 3:of(i),yo();break;case 5:qp(i);break;case 1:Ln(i.type)&&_l(i);break;case 4:Qp(i,i.stateNode.containerInfo);break;case 10:var f=i.type._context,h=i.memoizedProps.value;gn(Ce,f._currentValue),f._currentValue=h;break;case 13:if(f=i.memoizedState,f!==null)return f.dehydrated!==null?(gn(Dn,Dn.current&1),i.flags|=128,null):u&i.child.childLanes?Xm(n,i,u):(gn(Dn,Dn.current&1),n=Cr(n,i,u),n!==null?n.sibling:null);gn(Dn,Dn.current&1);break;case 19:if(f=(u&i.childLanes)!==0,n.flags&128){if(f)return yi(n,i,u);i.flags|=128}if(h=i.memoizedState,h!==null&&(h.rendering=null,h.tail=null,h.lastEffect=null),gn(Dn,Dn.current),f)break;return null;case 22:case 23:return i.lanes=0,xt(n,i,u)}return Cr(n,i,u)}var Gs,Ai,rr,Jm;Gs=function(n,i){for(var u=i.child;u!==null;){if(u.tag===5||u.tag===6)n.appendChild(u.stateNode);else if(u.tag!==4&&u.child!==null){u.child.return=u,u=u.child;continue}if(u===i)break;for(;u.sibling===null;){if(u.return===null||u.return===i)return;u=u.return}u.sibling.return=u.return,u=u.sibling}},Ai=function(){},rr=function(n,i,u,f){var h=n.memoizedProps;if(h!==f){n=i.stateNode,Pl(Ha.current);var x=null;switch(u){case"input":h=xn(n,h),f=xn(n,f),x=[];break;case"select":h=be({},h,{value:void 0}),f=be({},f,{value:void 0}),x=[];break;case"textarea":h=sr(n,h),f=sr(n,f),x=[];break;default:typeof h.onClick!="function"&&typeof f.onClick=="function"&&(n.onclick=Dd)}wn(u,f);var k;u=null;for(X in h)if(!f.hasOwnProperty(X)&&h.hasOwnProperty(X)&&h[X]!=null)if(X==="style"){var A=h[X];for(k in A)A.hasOwnProperty(k)&&(u||(u={}),u[k]="")}else X!=="dangerouslySetInnerHTML"&&X!=="children"&&X!=="suppressContentEditableWarning"&&X!=="suppressHydrationWarning"&&X!=="autoFocus"&&(g.hasOwnProperty(X)?x||(x=[]):(x=x||[]).push(X,null));for(X in f){var z=f[X];if(A=h!=null?h[X]:void 0,f.hasOwnProperty(X)&&z!==A&&(z!=null||A!=null))if(X==="style")if(A){for(k in A)!A.hasOwnProperty(k)||z&&z.hasOwnProperty(k)||(u||(u={}),u[k]="");for(k in z)z.hasOwnProperty(k)&&A[k]!==z[k]&&(u||(u={}),u[k]=z[k])}else u||(x||(x=[]),x.push(X,u)),u=z;else X==="dangerouslySetInnerHTML"?(z=z?z.__html:void 0,A=A?A.__html:void 0,z!=null&&A!==z&&(x=x||[]).push(X,z)):X==="children"?typeof z!="string"&&typeof z!="number"||(x=x||[]).push(X,""+z):X!=="suppressContentEditableWarning"&&X!=="suppressHydrationWarning"&&(g.hasOwnProperty(X)?(z!=null&&X==="onScroll"&&qt("scroll",n),x||A===z||(x=[])):(x=x||[]).push(X,z))}u&&(x=x||[]).push("style",u);var X=x;(i.updateQueue=X)&&(i.flags|=4)}},Jm=function(n,i,u,f){u!==f&&(i.flags|=4)};function yc(n,i){if(!En)switch(n.tailMode){case"hidden":i=n.tail;for(var u=null;i!==null;)i.alternate!==null&&(u=i),i=i.sibling;u===null?n.tail=null:u.sibling=null;break;case"collapsed":u=n.tail;for(var f=null;u!==null;)u.alternate!==null&&(f=u),u=u.sibling;f===null?i||n.tail===null?n.tail=null:n.tail.sibling=null:f.sibling=null}}function Ir(n){var i=n.alternate!==null&&n.alternate.child===n.child,u=0,f=0;if(i)for(var h=n.child;h!==null;)u|=h.lanes|h.childLanes,f|=h.subtreeFlags&14680064,f|=h.flags&14680064,h.return=n,h=h.sibling;else for(h=n.child;h!==null;)u|=h.lanes|h.childLanes,f|=h.subtreeFlags,f|=h.flags,h.return=n,h=h.sibling;return n.subtreeFlags|=f,n.childLanes=u,i}function sh(n,i,u){var f=i.pendingProps;switch(jd(i),i.tag){case 2:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return Ir(i),null;case 1:return Ln(i.type)&&Ia(),Ir(i),null;case 3:return f=i.stateNode,Us(),tn(Qn),tn(Rn),lc(),f.pendingContext&&(f.context=f.pendingContext,f.pendingContext=null),(n===null||n.child===null)&&(Un(i)?i.flags|=4:n===null||n.memoizedState.isDehydrated&&!(i.flags&256)||(i.flags|=1024,va!==null&&(Rc(va),va=null))),Ai(n,i),Ir(i),null;case 5:Xp(i);var h=Pl(oc.current);if(u=i.type,n!==null&&i.stateNode!=null)rr(n,i,u,f,h),n.ref!==i.ref&&(i.flags|=512,i.flags|=2097152);else{if(!f){if(i.stateNode===null)throw Error(s(166));return Ir(i),null}if(n=Pl(Ha.current),Un(i)){f=i.stateNode,u=i.type;var x=i.memoizedProps;switch(f[Xi]=i,f[Ju]=x,n=(i.mode&1)!==0,u){case"dialog":qt("cancel",f),qt("close",f);break;case"iframe":case"object":case"embed":qt("load",f);break;case"video":case"audio":for(h=0;h<Ku.length;h++)qt(Ku[h],f);break;case"source":qt("error",f);break;case"img":case"image":case"link":qt("error",f),qt("load",f);break;case"details":qt("toggle",f);break;case"input":$n(f,x),qt("invalid",f);break;case"select":f._wrapperState={wasMultiple:!!x.multiple},qt("invalid",f);break;case"textarea":ur(f,x),qt("invalid",f)}wn(u,x),h=null;for(var k in x)if(x.hasOwnProperty(k)){var A=x[k];k==="children"?typeof A=="string"?f.textContent!==A&&(x.suppressHydrationWarning!==!0&&Rd(f.textContent,A,n),h=["children",A]):typeof A=="number"&&f.textContent!==""+A&&(x.suppressHydrationWarning!==!0&&Rd(f.textContent,A,n),h=["children",""+A]):g.hasOwnProperty(k)&&A!=null&&k==="onScroll"&&qt("scroll",f)}switch(u){case"input":pn(f),Ti(f,x,!0);break;case"textarea":pn(f),ca(f);break;case"select":case"option":break;default:typeof x.onClick=="function"&&(f.onclick=Dd)}f=h,i.updateQueue=f,f!==null&&(i.flags|=4)}else{k=h.nodeType===9?h:h.ownerDocument,n==="http://www.w3.org/1999/xhtml"&&(n=Gn(u)),n==="http://www.w3.org/1999/xhtml"?u==="script"?(n=k.createElement("div"),n.innerHTML="<script><\/script>",n=n.removeChild(n.firstChild)):typeof f.is=="string"?n=k.createElement(u,{is:f.is}):(n=k.createElement(u),u==="select"&&(k=n,f.multiple?k.multiple=!0:f.size&&(k.size=f.size))):n=k.createElementNS(n,u),n[Xi]=i,n[Ju]=f,Gs(n,i,!1,!1),i.stateNode=n;e:{switch(k=Sn(u,f),u){case"dialog":qt("cancel",n),qt("close",n),h=f;break;case"iframe":case"object":case"embed":qt("load",n),h=f;break;case"video":case"audio":for(h=0;h<Ku.length;h++)qt(Ku[h],n);h=f;break;case"source":qt("error",n),h=f;break;case"img":case"image":case"link":qt("error",n),qt("load",n),h=f;break;case"details":qt("toggle",n),h=f;break;case"input":$n(n,f),h=xn(n,f),qt("invalid",n);break;case"option":h=f;break;case"select":n._wrapperState={wasMultiple:!!f.multiple},h=be({},f,{value:void 0}),qt("invalid",n);break;case"textarea":ur(n,f),h=sr(n,f),qt("invalid",n);break;default:h=f}wn(u,h),A=h;for(x in A)if(A.hasOwnProperty(x)){var z=A[x];x==="style"?Yt(n,z):x==="dangerouslySetInnerHTML"?(z=z?z.__html:void 0,z!=null&&no(n,z)):x==="children"?typeof z=="string"?(u!=="textarea"||z!=="")&&ki(n,z):typeof z=="number"&&ki(n,""+z):x!=="suppressContentEditableWarning"&&x!=="suppressHydrationWarning"&&x!=="autoFocus"&&(g.hasOwnProperty(x)?z!=null&&x==="onScroll"&&qt("scroll",n):z!=null&&ue(n,x,z,k))}switch(u){case"input":pn(n),Ti(n,f,!1);break;case"textarea":pn(n),ca(n);break;case"option":f.value!=null&&n.setAttribute("value",""+tt(f.value));break;case"select":n.multiple=!!f.multiple,x=f.value,x!=null?tr(n,!!f.multiple,x,!1):f.defaultValue!=null&&tr(n,!!f.multiple,f.defaultValue,!0);break;default:typeof h.onClick=="function"&&(n.onclick=Dd)}switch(u){case"button":case"input":case"select":case"textarea":f=!!f.autoFocus;break e;case"img":f=!0;break e;default:f=!1}}f&&(i.flags|=4)}i.ref!==null&&(i.flags|=512,i.flags|=2097152)}return Ir(i),null;case 6:if(n&&i.stateNode!=null)Jm(n,i,n.memoizedProps,f);else{if(typeof f!="string"&&i.stateNode===null)throw Error(s(166));if(u=Pl(oc.current),Pl(Ha.current),Un(i)){if(f=i.stateNode,u=i.memoizedProps,f[Xi]=i,(x=f.nodeValue!==u)&&(n=fi,n!==null))switch(n.tag){case 3:Rd(f.nodeValue,u,(n.mode&1)!==0);break;case 5:n.memoizedProps.suppressHydrationWarning!==!0&&Rd(f.nodeValue,u,(n.mode&1)!==0)}x&&(i.flags|=4)}else f=(u.nodeType===9?u:u.ownerDocument).createTextNode(f),f[Xi]=i,i.stateNode=f}return Ir(i),null;case 13:if(tn(Dn),f=i.memoizedState,n===null||n.memoizedState!==null&&n.memoizedState.dehydrated!==null){if(En&&pi!==null&&i.mode&1&&!(i.flags&128))Pm(),yo(),i.flags|=98560,x=!1;else if(x=Un(i),f!==null&&f.dehydrated!==null){if(n===null){if(!x)throw Error(s(318));if(x=i.memoizedState,x=x!==null?x.dehydrated:null,!x)throw Error(s(317));x[Xi]=i}else yo(),!(i.flags&128)&&(i.memoizedState=null),i.flags|=4;Ir(i),x=!1}else va!==null&&(Rc(va),va=null),x=!0;if(!x)return i.flags&65536?i:null}return i.flags&128?(i.lanes=u,i):(f=f!==null,f!==(n!==null&&n.memoizedState!==null)&&f&&(i.child.flags|=8192,i.mode&1&&(n===null||Dn.current&1?ir===0&&(ir=3):hh())),i.updateQueue!==null&&(i.flags|=4),Ir(i),null);case 4:return Us(),Ai(n,i),n===null&&qu(i.stateNode.containerInfo),Ir(i),null;case 10:return Wp(i.type._context),Ir(i),null;case 17:return Ln(i.type)&&Ia(),Ir(i),null;case 19:if(tn(Dn),x=i.memoizedState,x===null)return Ir(i),null;if(f=(i.flags&128)!==0,k=x.rendering,k===null)if(f)yc(x,!1);else{if(ir!==0||n!==null&&n.flags&128)for(n=i.child;n!==null;){if(k=zd(n),k!==null){for(i.flags|=128,yc(x,!1),f=k.updateQueue,f!==null&&(i.updateQueue=f,i.flags|=4),i.subtreeFlags=0,f=u,u=i.child;u!==null;)x=u,n=f,x.flags&=14680066,k=x.alternate,k===null?(x.childLanes=0,x.lanes=n,x.child=null,x.subtreeFlags=0,x.memoizedProps=null,x.memoizedState=null,x.updateQueue=null,x.dependencies=null,x.stateNode=null):(x.childLanes=k.childLanes,x.lanes=k.lanes,x.child=k.child,x.subtreeFlags=0,x.deletions=null,x.memoizedProps=k.memoizedProps,x.memoizedState=k.memoizedState,x.updateQueue=k.updateQueue,x.type=k.type,n=k.dependencies,x.dependencies=n===null?null:{lanes:n.lanes,firstContext:n.firstContext}),u=u.sibling;return gn(Dn,Dn.current&1|2),i.child}n=n.sibling}x.tail!==null&&Kt()>Xs&&(i.flags|=128,f=!0,yc(x,!1),i.lanes=4194304)}else{if(!f)if(n=zd(k),n!==null){if(i.flags|=128,f=!0,u=n.updateQueue,u!==null&&(i.updateQueue=u,i.flags|=4),yc(x,!0),x.tail===null&&x.tailMode==="hidden"&&!k.alternate&&!En)return Ir(i),null}else 2*Kt()-x.renderingStartTime>Xs&&u!==1073741824&&(i.flags|=128,f=!0,yc(x,!1),i.lanes=4194304);x.isBackwards?(k.sibling=i.child,i.child=k):(u=x.last,u!==null?u.sibling=k:i.child=k,x.last=k)}return x.tail!==null?(i=x.tail,x.rendering=i,x.tail=i.sibling,x.renderingStartTime=Kt(),i.sibling=null,u=Dn.current,gn(Dn,f?u&1|2:u&1),i):(Ir(i),null);case 22:case 23:return ph(),f=i.memoizedState!==null,n!==null&&n.memoizedState!==null!==f&&(i.flags|=8192),f&&i.mode&1?ji&1073741824&&(Ir(i),i.subtreeFlags&6&&(i.flags|=8192)):Ir(i),null;case 24:return null;case 25:return null}throw Error(s(156,i.tag))}function ev(n,i){switch(jd(i),i.tag){case 1:return Ln(i.type)&&Ia(),n=i.flags,n&65536?(i.flags=n&-65537|128,i):null;case 3:return Us(),tn(Qn),tn(Rn),lc(),n=i.flags,n&65536&&!(n&128)?(i.flags=n&-65537|128,i):null;case 5:return Xp(i),null;case 13:if(tn(Dn),n=i.memoizedState,n!==null&&n.dehydrated!==null){if(i.alternate===null)throw Error(s(340));yo()}return n=i.flags,n&65536?(i.flags=n&-65537|128,i):null;case 19:return tn(Dn),null;case 4:return Us(),null;case 10:return Wp(i.type._context),null;case 22:case 23:return ph(),null;case 24:return null;default:return null}}var Vl=!1,Er=!1,A0=typeof WeakSet=="function"?WeakSet:Set,ze=null;function Jo(n,i){var u=n.ref;if(u!==null)if(typeof u=="function")try{u(null)}catch(f){zn(n,i,f)}else u.current=null}function uh(n,i,u){try{u()}catch(f){zn(n,i,f)}}var ch=!1;function j0(n,i){if($l=No,n=Io(),Ds(n)){if("selectionStart"in n)var u={start:n.selectionStart,end:n.selectionEnd};else e:{u=(u=n.ownerDocument)&&u.defaultView||window;var f=u.getSelection&&u.getSelection();if(f&&f.rangeCount!==0){u=f.anchorNode;var h=f.anchorOffset,x=f.focusNode;f=f.focusOffset;try{u.nodeType,x.nodeType}catch{u=null;break e}var k=0,A=-1,z=-1,X=0,pe=0,ge=n,fe=null;t:for(;;){for(var Ae;ge!==u||h!==0&&ge.nodeType!==3||(A=k+h),ge!==x||f!==0&&ge.nodeType!==3||(z=k+f),ge.nodeType===3&&(k+=ge.nodeValue.length),(Ae=ge.firstChild)!==null;)fe=ge,ge=Ae;for(;;){if(ge===n)break t;if(fe===u&&++X===h&&(A=k),fe===x&&++pe===f&&(z=k),(Ae=ge.nextSibling)!==null)break;ge=fe,fe=ge.parentNode}ge=Ae}u=A===-1||z===-1?null:{start:A,end:z}}else u=null}u=u||{start:0,end:0}}else u=null;for(Zu={focusedElem:n,selectionRange:u},No=!1,ze=i;ze!==null;)if(i=ze,n=i.child,(i.subtreeFlags&1028)!==0&&n!==null)n.return=i,ze=n;else for(;ze!==null;){i=ze;try{var Fe=i.alternate;if(i.flags&1024)switch(i.tag){case 0:case 11:case 15:break;case 1:if(Fe!==null){var Ue=Fe.memoizedProps,Vn=Fe.memoizedState,W=i.stateNode,I=W.getSnapshotBeforeUpdate(i.elementType===i.type?Ue:mi(i.type,Ue),Vn);W.__reactInternalSnapshotBeforeUpdate=I}break;case 3:var K=i.stateNode.containerInfo;K.nodeType===1?K.textContent="":K.nodeType===9&&K.documentElement&&K.removeChild(K.documentElement);break;case 5:case 6:case 4:case 17:break;default:throw Error(s(163))}}catch(ve){zn(i,i.return,ve)}if(n=i.sibling,n!==null){n.return=i.return,ze=n;break}ze=i.return}return Fe=ch,ch=!1,Fe}function Ks(n,i,u){var f=i.updateQueue;if(f=f!==null?f.lastEffect:null,f!==null){var h=f=f.next;do{if((h.tag&n)===n){var x=h.destroy;h.destroy=void 0,x!==void 0&&uh(i,u,x)}h=h.next}while(h!==f)}}function df(n,i){if(i=i.updateQueue,i=i!==null?i.lastEffect:null,i!==null){var u=i=i.next;do{if((u.tag&n)===n){var f=u.create;u.destroy=f()}u=u.next}while(u!==i)}}function ff(n){var i=n.ref;if(i!==null){var u=n.stateNode;switch(n.tag){case 5:n=u;break;default:n=u}typeof i=="function"?i(n):i.current=n}}function tv(n){var i=n.alternate;i!==null&&(n.alternate=null,tv(i)),n.child=null,n.deletions=null,n.sibling=null,n.tag===5&&(i=n.stateNode,i!==null&&(delete i[Xi],delete i[Ju],delete i[Lp],delete i[zp],delete i[zs])),n.stateNode=null,n.return=null,n.dependencies=null,n.memoizedProps=null,n.memoizedState=null,n.pendingProps=null,n.stateNode=null,n.updateQueue=null}function pf(n){return n.tag===5||n.tag===3||n.tag===4}function xc(n){e:for(;;){for(;n.sibling===null;){if(n.return===null||pf(n.return))return null;n=n.return}for(n.sibling.return=n.return,n=n.sibling;n.tag!==5&&n.tag!==6&&n.tag!==18;){if(n.flags&2||n.child===null||n.tag===4)continue e;n.child.return=n,n=n.child}if(!(n.flags&2))return n.stateNode}}function Va(n,i,u){var f=n.tag;if(f===5||f===6)n=n.stateNode,i?u.nodeType===8?u.parentNode.insertBefore(n,i):u.insertBefore(n,i):(u.nodeType===8?(i=u.parentNode,i.insertBefore(n,u)):(i=u,i.appendChild(n)),u=u._reactRootContainer,u!=null||i.onclick!==null||(i.onclick=Dd));else if(f!==4&&(n=n.child,n!==null))for(Va(n,i,u),n=n.sibling;n!==null;)Va(n,i,u),n=n.sibling}function Wa(n,i,u){var f=n.tag;if(f===5||f===6)n=n.stateNode,i?u.insertBefore(n,i):u.appendChild(n);else if(f!==4&&(n=n.child,n!==null))for(Wa(n,i,u),n=n.sibling;n!==null;)Wa(n,i,u),n=n.sibling}var Mn=null,Qr=!1;function ta(n,i,u){for(u=u.child;u!==null;)bo(n,i,u),u=u.sibling}function bo(n,i,u){if(li&&typeof li.onCommitFiberUnmount=="function")try{li.onCommitFiberUnmount(jo,u)}catch{}switch(u.tag){case 5:Er||Jo(u,i);case 6:var f=Mn,h=Qr;Mn=null,ta(n,i,u),Mn=f,Qr=h,Mn!==null&&(Qr?(n=Mn,u=u.stateNode,n.nodeType===8?n.parentNode.removeChild(u):n.removeChild(u)):Mn.removeChild(u.stateNode));break;case 18:Mn!==null&&(Qr?(n=Mn,u=u.stateNode,n.nodeType===8?_s(n.parentNode,u):n.nodeType===1&&_s(n,u),Ki(n)):_s(Mn,u.stateNode));break;case 4:f=Mn,h=Qr,Mn=u.stateNode.containerInfo,Qr=!0,ta(n,i,u),Mn=f,Qr=h;break;case 0:case 11:case 14:case 15:if(!Er&&(f=u.updateQueue,f!==null&&(f=f.lastEffect,f!==null))){h=f=f.next;do{var x=h,k=x.destroy;x=x.tag,k!==void 0&&(x&2||x&4)&&uh(u,i,k),h=h.next}while(h!==f)}ta(n,i,u);break;case 1:if(!Er&&(Jo(u,i),f=u.stateNode,typeof f.componentWillUnmount=="function"))try{f.props=u.memoizedProps,f.state=u.memoizedState,f.componentWillUnmount()}catch(A){zn(u,i,A)}ta(n,i,u);break;case 21:ta(n,i,u);break;case 22:u.mode&1?(Er=(f=Er)||u.memoizedState!==null,ta(n,i,u),Er=f):ta(n,i,u);break;default:ta(n,i,u)}}function nv(n){var i=n.updateQueue;if(i!==null){n.updateQueue=null;var u=n.stateNode;u===null&&(u=n.stateNode=new A0),i.forEach(function(f){var h=P0.bind(null,n,f);u.has(f)||(u.add(f),f.then(h,h))})}}function xa(n,i){var u=i.deletions;if(u!==null)for(var f=0;f<u.length;f++){var h=u[f];try{var x=n,k=i,A=k;e:for(;A!==null;){switch(A.tag){case 5:Mn=A.stateNode,Qr=!1;break e;case 3:Mn=A.stateNode.containerInfo,Qr=!0;break e;case 4:Mn=A.stateNode.containerInfo,Qr=!0;break e}A=A.return}if(Mn===null)throw Error(s(160));bo(x,k,h),Mn=null,Qr=!1;var z=h.alternate;z!==null&&(z.return=null),h.return=null}catch(X){zn(h,i,X)}}if(i.subtreeFlags&12854)for(i=i.child;i!==null;)rv(i,n),i=i.sibling}function rv(n,i){var u=n.alternate,f=n.flags;switch(n.tag){case 0:case 11:case 14:case 15:if(xa(i,n),ba(n),f&4){try{Ks(3,n,n.return),df(3,n)}catch(Ue){zn(n,n.return,Ue)}try{Ks(5,n,n.return)}catch(Ue){zn(n,n.return,Ue)}}break;case 1:xa(i,n),ba(n),f&512&&u!==null&&Jo(u,u.return);break;case 5:if(xa(i,n),ba(n),f&512&&u!==null&&Jo(u,u.return),n.flags&32){var h=n.stateNode;try{ki(h,"")}catch(Ue){zn(n,n.return,Ue)}}if(f&4&&(h=n.stateNode,h!=null)){var x=n.memoizedProps,k=u!==null?u.memoizedProps:x,A=n.type,z=n.updateQueue;if(n.updateQueue=null,z!==null)try{A==="input"&&x.type==="radio"&&x.name!=null&&er(h,x),Sn(A,k);var X=Sn(A,x);for(k=0;k<z.length;k+=2){var pe=z[k],ge=z[k+1];pe==="style"?Yt(h,ge):pe==="dangerouslySetInnerHTML"?no(h,ge):pe==="children"?ki(h,ge):ue(h,pe,ge,X)}switch(A){case"input":Yn(h,x);break;case"textarea":_r(h,x);break;case"select":var fe=h._wrapperState.wasMultiple;h._wrapperState.wasMultiple=!!x.multiple;var Ae=x.value;Ae!=null?tr(h,!!x.multiple,Ae,!1):fe!==!!x.multiple&&(x.defaultValue!=null?tr(h,!!x.multiple,x.defaultValue,!0):tr(h,!!x.multiple,x.multiple?[]:"",!1))}h[Ju]=x}catch(Ue){zn(n,n.return,Ue)}}break;case 6:if(xa(i,n),ba(n),f&4){if(n.stateNode===null)throw Error(s(162));h=n.stateNode,x=n.memoizedProps;try{h.nodeValue=x}catch(Ue){zn(n,n.return,Ue)}}break;case 3:if(xa(i,n),ba(n),f&4&&u!==null&&u.memoizedState.isDehydrated)try{Ki(i.containerInfo)}catch(Ue){zn(n,n.return,Ue)}break;case 4:xa(i,n),ba(n);break;case 13:xa(i,n),ba(n),h=n.child,h.flags&8192&&(x=h.memoizedState!==null,h.stateNode.isHidden=x,!x||h.alternate!==null&&h.alternate.memoizedState!==null||(fh=Kt())),f&4&&nv(n);break;case 22:if(pe=u!==null&&u.memoizedState!==null,n.mode&1?(Er=(X=Er)||pe,xa(i,n),Er=X):xa(i,n),ba(n),f&8192){if(X=n.memoizedState!==null,(n.stateNode.isHidden=X)&&!pe&&n.mode&1)for(ze=n,pe=n.child;pe!==null;){for(ge=ze=pe;ze!==null;){switch(fe=ze,Ae=fe.child,fe.tag){case 0:case 11:case 14:case 15:Ks(4,fe,fe.return);break;case 1:Jo(fe,fe.return);var Fe=fe.stateNode;if(typeof Fe.componentWillUnmount=="function"){f=fe,u=fe.return;try{i=f,Fe.props=i.memoizedProps,Fe.state=i.memoizedState,Fe.componentWillUnmount()}catch(Ue){zn(f,u,Ue)}}break;case 5:Jo(fe,fe.return);break;case 22:if(fe.memoizedState!==null){av(ge);continue}}Ae!==null?(Ae.return=fe,ze=Ae):av(ge)}pe=pe.sibling}e:for(pe=null,ge=n;;){if(ge.tag===5){if(pe===null){pe=ge;try{h=ge.stateNode,X?(x=h.style,typeof x.setProperty=="function"?x.setProperty("display","none","important"):x.display="none"):(A=ge.stateNode,z=ge.memoizedProps.style,k=z!=null&&z.hasOwnProperty("display")?z.display:null,A.style.display=wt("display",k))}catch(Ue){zn(n,n.return,Ue)}}}else if(ge.tag===6){if(pe===null)try{ge.stateNode.nodeValue=X?"":ge.memoizedProps}catch(Ue){zn(n,n.return,Ue)}}else if((ge.tag!==22&&ge.tag!==23||ge.memoizedState===null||ge===n)&&ge.child!==null){ge.child.return=ge,ge=ge.child;continue}if(ge===n)break e;for(;ge.sibling===null;){if(ge.return===null||ge.return===n)break e;pe===ge&&(pe=null),ge=ge.return}pe===ge&&(pe=null),ge.sibling.return=ge.return,ge=ge.sibling}}break;case 19:xa(i,n),ba(n),f&4&&nv(n);break;case 21:break;default:xa(i,n),ba(n)}}function ba(n){var i=n.flags;if(i&2){try{e:{for(var u=n.return;u!==null;){if(pf(u)){var f=u;break e}u=u.return}throw Error(s(160))}switch(f.tag){case 5:var h=f.stateNode;f.flags&32&&(ki(h,""),f.flags&=-33);var x=xc(n);Wa(n,x,h);break;case 3:case 4:var k=f.stateNode.containerInfo,A=xc(n);Va(n,A,k);break;default:throw Error(s(161))}}catch(z){zn(n,n.return,z)}n.flags&=-3}i&4096&&(n.flags&=-4097)}function bc(n,i,u){ze=n,iv(n)}function iv(n,i,u){for(var f=(n.mode&1)!==0;ze!==null;){var h=ze,x=h.child;if(h.tag===22&&f){var k=h.memoizedState!==null||Vl;if(!k){var A=h.alternate,z=A!==null&&A.memoizedState!==null||Er;A=Vl;var X=Er;if(Vl=k,(Er=z)&&!X)for(ze=h;ze!==null;)k=ze,z=k.child,k.tag===22&&k.memoizedState!==null?wc(h):z!==null?(z.return=k,ze=z):wc(h);for(;x!==null;)ze=x,iv(x),x=x.sibling;ze=h,Vl=A,Er=X}dh(n)}else h.subtreeFlags&8772&&x!==null?(x.return=h,ze=x):dh(n)}}function dh(n){for(;ze!==null;){var i=ze;if(i.flags&8772){var u=i.alternate;try{if(i.flags&8772)switch(i.tag){case 0:case 11:case 15:Er||df(5,i);break;case 1:var f=i.stateNode;if(i.flags&4&&!Er)if(u===null)f.componentDidMount();else{var h=i.elementType===i.type?u.memoizedProps:mi(i.type,u.memoizedProps);f.componentDidUpdate(h,u.memoizedState,f.__reactInternalSnapshotBeforeUpdate)}var x=i.updateQueue;x!==null&&Kp(i,x,f);break;case 3:var k=i.updateQueue;if(k!==null){if(u=null,i.child!==null)switch(i.child.tag){case 5:u=i.child.stateNode;break;case 1:u=i.child.stateNode}Kp(i,k,u)}break;case 5:var A=i.stateNode;if(u===null&&i.flags&4){u=A;var z=i.memoizedProps;switch(i.type){case"button":case"input":case"select":case"textarea":z.autoFocus&&u.focus();break;case"img":z.src&&(u.src=z.src)}}break;case 6:break;case 4:break;case 12:break;case 13:if(i.memoizedState===null){var X=i.alternate;if(X!==null){var pe=X.memoizedState;if(pe!==null){var ge=pe.dehydrated;ge!==null&&Ki(ge)}}}break;case 19:case 17:case 21:case 22:case 23:case 25:break;default:throw Error(s(163))}Er||i.flags&512&&ff(i)}catch(fe){zn(i,i.return,fe)}}if(i===n){ze=null;break}if(u=i.sibling,u!==null){u.return=i.return,ze=u;break}ze=i.return}}function av(n){for(;ze!==null;){var i=ze;if(i===n){ze=null;break}var u=i.sibling;if(u!==null){u.return=i.return,ze=u;break}ze=i.return}}function wc(n){for(;ze!==null;){var i=ze;try{switch(i.tag){case 0:case 11:case 15:var u=i.return;try{df(4,i)}catch(z){zn(i,u,z)}break;case 1:var f=i.stateNode;if(typeof f.componentDidMount=="function"){var h=i.return;try{f.componentDidMount()}catch(z){zn(i,h,z)}}var x=i.return;try{ff(i)}catch(z){zn(i,x,z)}break;case 5:var k=i.return;try{ff(i)}catch(z){zn(i,k,z)}}}catch(z){zn(i,i.return,z)}if(i===n){ze=null;break}var A=i.sibling;if(A!==null){A.return=i.return,ze=A;break}ze=i.return}}var ov=Math.ceil,hf=he.ReactCurrentDispatcher,Wl=he.ReactCurrentOwner,Ur=he.ReactCurrentBatchConfig,jt=0,Zn=null,Hn=null,Tr=0,ji=0,Qs=di(0),ir=0,Yl=null,Gl=0,Kl=0,Sc=0,qs=null,xi=null,fh=0,Xs=1/0,wo=null,el=!1,Cc=null,na=null,gf=!1,tl=null,Ec=0,Zs=0,Js=null,Ql=-1,Tc=0;function mn(){return jt&6?Kt():Ql!==-1?Ql:Ql=Kt()}function _i(n){return n.mode&1?jt&2&&Tr!==0?Tr&-Tr:zl.transition!==null?(Tc===0&&(Tc=Sl()),Tc):(n=Nt,n!==0||(n=window.event,n=n===void 0?16:Pu(n.type)),n):1}function Li(n,i,u,f){if(50<Zs)throw Zs=0,Js=null,Error(s(185));zo(n,u,f),(!(jt&2)||n!==Zn)&&(n===Zn&&(!(jt&2)&&(Kl|=u),ir===4&&nl(n,Tr)),gr(n,f),u===1&&jt===0&&!(i.mode&1)&&(Xs=Kt()+500,tc&&Gr()))}function gr(n,i){var u=n.callbackNode;vs(n,i);var f=La(n,n===Zn?Tr:0);if(f===0)u!==null&&Cn(u),n.callbackNode=null,n.callbackPriority=0;else if(i=f&-f,n.callbackPriority!==i){if(u!=null&&Cn(u),i===1)n.tag===0?Np(Mc.bind(null,n)):Bo(Mc.bind(null,n)),M0(function(){!(jt&6)&&Gr()}),u=null;else{switch(Nu(f)){case 1:u=gt;break;case 4:u=_a;break;case 16:u=oo;break;case 536870912:u=lo;break;default:u=oo}u=fv(u,lv.bind(null,n))}n.callbackPriority=i,n.callbackNode=u}}function lv(n,i){if(Ql=-1,Tc=0,jt&6)throw Error(s(327));var u=n.callbackNode;if(eu()&&n.callbackNode!==u)return null;var f=La(n,n===Zn?Tr:0);if(f===0)return null;if(f&30||f&n.expiredLanes||i)i=yf(n,f);else{i=f;var h=jt;jt|=2;var x=sv();(Zn!==n||Tr!==i)&&(wo=null,Xs=Kt()+500,Xl(n,i));do try{L0();break}catch(A){vf(n,A)}while(!0);Vp(),hf.current=x,jt=h,Hn!==null?i=0:(Zn=null,Tr=0,i=ir)}if(i!==0){if(i===2&&(h=uo(n),h!==0&&(f=h,i=kc(n,h))),i===1)throw u=Yl,Xl(n,0),nl(n,f),gr(n,Kt()),u;if(i===6)nl(n,f);else{if(h=n.current.alternate,!(f&30)&&!Dc(h)&&(i=yf(n,f),i===2&&(x=uo(n),x!==0&&(f=x,i=kc(n,x))),i===1))throw u=Yl,Xl(n,0),nl(n,f),gr(n,Kt()),u;switch(n.finishedWork=h,n.finishedLanes=f,i){case 0:case 1:throw Error(s(345));case 2:Zl(n,xi,wo);break;case 3:if(nl(n,f),(f&130023424)===f&&(i=fh+500-Kt(),10<i)){if(La(n,0)!==0)break;if(h=n.suspendedLanes,(h&f)!==f){mn(),n.pingedLanes|=n.suspendedLanes&h;break}n.timeoutHandle=Md(Zl.bind(null,n,xi,wo),i);break}Zl(n,xi,wo);break;case 4:if(nl(n,f),(f&4194240)===f)break;for(i=n.eventTimes,h=-1;0<f;){var k=31-Vr(f);x=1<<k,k=i[k],k>h&&(h=k),f&=~x}if(f=h,f=Kt()-f,f=(120>f?120:480>f?480:1080>f?1080:1920>f?1920:3e3>f?3e3:4320>f?4320:1960*ov(f/1960))-f,10<f){n.timeoutHandle=Md(Zl.bind(null,n,xi,wo),f);break}Zl(n,xi,wo);break;case 5:Zl(n,xi,wo);break;default:throw Error(s(329))}}}return gr(n,Kt()),n.callbackNode===u?lv.bind(null,n):null}function kc(n,i){var u=qs;return n.current.memoizedState.isDehydrated&&(Xl(n,i).flags|=256),n=yf(n,i),n!==2&&(i=xi,xi=u,i!==null&&Rc(i)),n}function Rc(n){xi===null?xi=n:xi.push.apply(xi,n)}function Dc(n){for(var i=n;;){if(i.flags&16384){var u=i.updateQueue;if(u!==null&&(u=u.stores,u!==null))for(var f=0;f<u.length;f++){var h=u[f],x=h.getSnapshot;h=h.value;try{if(!ha(x(),h))return!1}catch{return!1}}}if(u=i.child,i.subtreeFlags&16384&&u!==null)u.return=i,i=u;else{if(i===n)break;for(;i.sibling===null;){if(i.return===null||i.return===n)return!0;i=i.return}i.sibling.return=i.return,i=i.sibling}}return!0}function nl(n,i){for(i&=~Sc,i&=~Kl,n.suspendedLanes|=i,n.pingedLanes&=~i,n=n.expirationTimes;0<i;){var u=31-Vr(i),f=1<<u;n[u]=-1,i&=~f}}function Mc(n){if(jt&6)throw Error(s(327));eu();var i=La(n,0);if(!(i&1))return gr(n,Kt()),null;var u=yf(n,i);if(n.tag!==0&&u===2){var f=uo(n);f!==0&&(i=f,u=kc(n,f))}if(u===1)throw u=Yl,Xl(n,0),nl(n,i),gr(n,Kt()),u;if(u===6)throw Error(s(345));return n.finishedWork=n.current.alternate,n.finishedLanes=i,Zl(n,xi,wo),gr(n,Kt()),null}function mf(n,i){var u=jt;jt|=1;try{return n(i)}finally{jt=u,jt===0&&(Xs=Kt()+500,tc&&Gr())}}function ql(n){tl!==null&&tl.tag===0&&!(jt&6)&&eu();var i=jt;jt|=1;var u=Ur.transition,f=Nt;try{if(Ur.transition=null,Nt=1,n)return n()}finally{Nt=f,Ur.transition=u,jt=i,!(jt&6)&&Gr()}}function ph(){ji=Qs.current,tn(Qs)}function Xl(n,i){n.finishedWork=null,n.finishedLanes=0;var u=n.timeoutHandle;if(u!==-1&&(n.timeoutHandle=-1,_m(u)),Hn!==null)for(u=Hn.return;u!==null;){var f=u;switch(jd(f),f.tag){case 1:f=f.type.childContextTypes,f!=null&&Ia();break;case 3:Us(),tn(Qn),tn(Rn),lc();break;case 5:Xp(f);break;case 4:Us();break;case 13:tn(Dn);break;case 19:tn(Dn);break;case 10:Wp(f.type._context);break;case 22:case 23:ph()}u=u.return}if(Zn=n,Hn=n=rl(n.current,null),Tr=ji=i,ir=0,Yl=null,Sc=Kl=Gl=0,xi=qs=null,Nl!==null){for(i=0;i<Nl.length;i++)if(u=Nl[i],f=u.interleaved,f!==null){u.interleaved=null;var h=f.next,x=u.pending;if(x!==null){var k=x.next;x.next=h,f.next=k}u.pending=f}Nl=null}return n}function vf(n,i){do{var u=Hn;try{if(Vp(),Ge.current=rn,Nd){for(var f=mt.memoizedState;f!==null;){var h=f.queue;h!==null&&(h.pending=null),f=f.next}Nd=!1}if(zt=0,nr=cn=mt=null,sc=!1,uc=0,Wl.current=null,u===null||u.return===null){ir=1,Yl=i,Hn=null;break}e:{var x=n,k=u.return,A=u,z=i;if(i=Tr,A.flags|=32768,z!==null&&typeof z=="object"&&typeof z.then=="function"){var X=z,pe=A,ge=pe.tag;if(!(pe.mode&1)&&(ge===0||ge===11||ge===15)){var fe=pe.alternate;fe?(pe.updateQueue=fe.updateQueue,pe.memoizedState=fe.memoizedState,pe.lanes=fe.lanes):(pe.updateQueue=null,pe.memoizedState=null)}var Ae=ih(k);if(Ae!==null){Ae.flags&=-257,qm(Ae,k,A,x,i),Ae.mode&1&&rh(x,X,i),i=Ae,z=X;var Fe=i.updateQueue;if(Fe===null){var Ue=new Set;Ue.add(z),i.updateQueue=Ue}else Fe.add(z);break e}else{if(!(i&1)){rh(x,X,i),hh();break e}z=Error(s(426))}}else if(En&&A.mode&1){var Vn=ih(k);if(Vn!==null){!(Vn.flags&65536)&&(Vn.flags|=256),qm(Vn,k,A,x,i),rc(Zo(z,A));break e}}x=z=Zo(z,A),ir!==4&&(ir=2),qs===null?qs=[x]:qs.push(x),x=k;do{switch(x.tag){case 3:x.flags|=65536,i&=-i,x.lanes|=i;var W=gc(x,z,i);Hm(x,W);break e;case 1:A=z;var I=x.type,K=x.stateNode;if(!(x.flags&128)&&(typeof I.getDerivedStateFromError=="function"||K!==null&&typeof K.componentDidCatch=="function"&&(na===null||!na.has(K)))){x.flags|=65536,i&=-i,x.lanes|=i;var ve=Qm(x,A,i);Hm(x,ve);break e}}x=x.return}while(x!==null)}uv(u)}catch(De){i=De,Hn===u&&u!==null&&(Hn=u=u.return);continue}break}while(!0)}function sv(){var n=hf.current;return hf.current=rn,n===null?rn:n}function hh(){(ir===0||ir===3||ir===2)&&(ir=4),Zn===null||!(Gl&268435455)&&!(Kl&268435455)||nl(Zn,Tr)}function yf(n,i){var u=jt;jt|=2;var f=sv();(Zn!==n||Tr!==i)&&(wo=null,Xl(n,i));do try{_0();break}catch(h){vf(n,h)}while(!0);if(Vp(),jt=u,hf.current=f,Hn!==null)throw Error(s(261));return Zn=null,Tr=0,ir}function _0(){for(;Hn!==null;)gh(Hn)}function L0(){for(;Hn!==null&&!Lr();)gh(Hn)}function gh(n){var i=vh(n.alternate,n,ji);n.memoizedProps=n.pendingProps,i===null?uv(n):Hn=i,Wl.current=null}function uv(n){var i=n;do{var u=i.alternate;if(n=i.return,i.flags&32768){if(u=ev(u,i),u!==null){u.flags&=32767,Hn=u;return}if(n!==null)n.flags|=32768,n.subtreeFlags=0,n.deletions=null;else{ir=6,Hn=null;return}}else if(u=sh(u,i,ji),u!==null){Hn=u;return}if(i=i.sibling,i!==null){Hn=i;return}Hn=i=n}while(i!==null);ir===0&&(ir=5)}function Zl(n,i,u){var f=Nt,h=Ur.transition;try{Ur.transition=null,Nt=1,z0(n,i,u,f)}finally{Ur.transition=h,Nt=f}return null}function z0(n,i,u,f){do eu();while(tl!==null);if(jt&6)throw Error(s(327));u=n.finishedWork;var h=n.finishedLanes;if(u===null)return null;if(n.finishedWork=null,n.finishedLanes=0,u===n.current)throw Error(s(177));n.callbackNode=null,n.callbackPriority=0;var x=u.lanes|u.childLanes;if(Lu(n,x),n===Zn&&(Hn=Zn=null,Tr=0),!(u.subtreeFlags&2064)&&!(u.flags&2064)||gf||(gf=!0,fv(oo,function(){return eu(),null})),x=(u.flags&15990)!==0,u.subtreeFlags&15990||x){x=Ur.transition,Ur.transition=null;var k=Nt;Nt=1;var A=jt;jt|=4,Wl.current=null,j0(n,u),rv(u,n),Tm(Zu),No=!!$l,Zu=$l=null,n.current=u,bc(u),da(),jt=A,Nt=k,Ur.transition=x}else n.current=u;if(gf&&(gf=!1,tl=n,Ec=h),x=n.pendingLanes,x===0&&(na=null),ju(u.stateNode),gr(n,Kt()),i!==null)for(f=n.onRecoverableError,u=0;u<i.length;u++)h=i[u],f(h.value,{componentStack:h.stack,digest:h.digest});if(el)throw el=!1,n=Cc,Cc=null,n;return Ec&1&&n.tag!==0&&eu(),x=n.pendingLanes,x&1?n===Js?Zs++:(Zs=0,Js=n):Zs=0,Gr(),null}function eu(){if(tl!==null){var n=Nu(Ec),i=Ur.transition,u=Nt;try{if(Ur.transition=null,Nt=16>n?16:n,tl===null)var f=!1;else{if(n=tl,tl=null,Ec=0,jt&6)throw Error(s(331));var h=jt;for(jt|=4,ze=n.current;ze!==null;){var x=ze,k=x.child;if(ze.flags&16){var A=x.deletions;if(A!==null){for(var z=0;z<A.length;z++){var X=A[z];for(ze=X;ze!==null;){var pe=ze;switch(pe.tag){case 0:case 11:case 15:Ks(8,pe,x)}var ge=pe.child;if(ge!==null)ge.return=pe,ze=ge;else for(;ze!==null;){pe=ze;var fe=pe.sibling,Ae=pe.return;if(tv(pe),pe===X){ze=null;break}if(fe!==null){fe.return=Ae,ze=fe;break}ze=Ae}}}var Fe=x.alternate;if(Fe!==null){var Ue=Fe.child;if(Ue!==null){Fe.child=null;do{var Vn=Ue.sibling;Ue.sibling=null,Ue=Vn}while(Ue!==null)}}ze=x}}if(x.subtreeFlags&2064&&k!==null)k.return=x,ze=k;else e:for(;ze!==null;){if(x=ze,x.flags&2048)switch(x.tag){case 0:case 11:case 15:Ks(9,x,x.return)}var W=x.sibling;if(W!==null){W.return=x.return,ze=W;break e}ze=x.return}}var I=n.current;for(ze=I;ze!==null;){k=ze;var K=k.child;if(k.subtreeFlags&2064&&K!==null)K.return=k,ze=K;else e:for(k=I;ze!==null;){if(A=ze,A.flags&2048)try{switch(A.tag){case 0:case 11:case 15:df(9,A)}}catch(De){zn(A,A.return,De)}if(A===k){ze=null;break e}var ve=A.sibling;if(ve!==null){ve.return=A.return,ze=ve;break e}ze=A.return}}if(jt=h,Gr(),li&&typeof li.onPostCommitFiberRoot=="function")try{li.onPostCommitFiberRoot(jo,n)}catch{}f=!0}return f}finally{Nt=u,Ur.transition=i}}return!1}function cv(n,i,u){i=Zo(u,i),i=gc(n,i,1),n=Ko(n,i,1),i=mn(),n!==null&&(zo(n,1,i),gr(n,i))}function zn(n,i,u){if(n.tag===3)cv(n,n,u);else for(;i!==null;){if(i.tag===3){cv(i,n,u);break}else if(i.tag===1){var f=i.stateNode;if(typeof i.type.getDerivedStateFromError=="function"||typeof f.componentDidCatch=="function"&&(na===null||!na.has(f))){n=Zo(u,n),n=Qm(i,n,1),i=Ko(i,n,1),n=mn(),i!==null&&(zo(i,1,n),gr(i,n));break}}i=i.return}}function mh(n,i,u){var f=n.pingCache;f!==null&&f.delete(i),i=mn(),n.pingedLanes|=n.suspendedLanes&u,Zn===n&&(Tr&u)===u&&(ir===4||ir===3&&(Tr&130023424)===Tr&&500>Kt()-fh?Xl(n,0):Sc|=u),gr(n,i)}function dv(n,i){i===0&&(n.mode&1?(i=_o,_o<<=1,!(_o&130023424)&&(_o=4194304)):i=1);var u=mn();n=Ba(n,i),n!==null&&(zo(n,i,u),gr(n,u))}function N0(n){var i=n.memoizedState,u=0;i!==null&&(u=i.retryLane),dv(n,u)}function P0(n,i){var u=0;switch(n.tag){case 13:var f=n.stateNode,h=n.memoizedState;h!==null&&(u=h.retryLane);break;case 19:f=n.stateNode;break;default:throw Error(s(314))}f!==null&&f.delete(i),dv(n,u)}var vh;vh=function(n,i,u){if(n!==null)if(n.memoizedProps!==i.pendingProps||Qn.current)hr=!0;else{if(!(n.lanes&u)&&!(i.flags&128))return hr=!1,cf(n,i,u);hr=!!(n.flags&131072)}else hr=!1,En&&i.flags&1048576&&zm(i,Wo,i.index);switch(i.lanes=0,i.tag){case 2:var f=i.type;ea(n,i),n=i.pendingProps;var h=Oi(i,Rn.current);Fs(i,u),h=nt(null,i,f,n,h,u);var x=Qo();return i.flags|=1,typeof h=="object"&&h!==null&&typeof h.render=="function"&&h.$$typeof===void 0?(i.tag=1,i.memoizedState=null,i.updateQueue=null,Ln(f)?(x=!0,_l(i)):x=!1,i.memoizedState=h.state!==null&&h.state!==void 0?h.state:null,Go(i),h.updater=ef,i.stateNode=h,h._reactInternals=i,th(i,f,n,u),i=ah(null,i,f,!0,x,u)):(i.tag=0,En&&x&&Pp(i),Bn(null,i,h,u),i=i.child),i;case 16:f=i.elementType;e:{switch(ea(n,i),n=i.pendingProps,h=f._init,f=h(f._payload),i.type=f,h=i.tag=I0(f),n=mi(f,n),h){case 0:i=af(null,i,f,n,u);break e;case 1:i=$0(null,i,f,n,u);break e;case 11:i=rf(null,i,f,n,u);break e;case 14:i=vi(null,i,f,mi(f.type,n),u);break e}throw Error(s(306,f,""))}return i;case 0:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:mi(f,h),af(n,i,f,h,u);case 1:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:mi(f,h),$0(n,i,f,h,u);case 3:e:{if(of(i),n===null)throw Error(s(387));f=i.pendingProps,x=i.memoizedState,h=x.element,Bm(n,i),Ld(i,f,null,u);var k=i.memoizedState;if(f=k.element,x.isDehydrated)if(x={element:f,isDehydrated:!1,cache:k.cache,pendingSuspenseBoundaries:k.pendingSuspenseBoundaries,transitions:k.transitions},i.updateQueue.baseState=x,i.memoizedState=x,i.flags&256){h=Zo(Error(s(423)),i),i=Ys(n,i,f,u,h);break e}else if(f!==h){h=Zo(Error(s(424)),i),i=Ys(n,i,f,u,h);break e}else for(pi=ga(i.stateNode.containerInfo.firstChild),fi=i,En=!0,va=null,u=Sr(i,null,f,u),i.child=u;u;)u.flags=u.flags&-3|4096,u=u.sibling;else{if(yo(),f===h){i=Cr(n,i,u);break e}Bn(n,i,f,u)}i=i.child}return i;case 5:return qp(i),n===null&&Bp(i),f=i.type,h=i.pendingProps,x=n!==null?n.memoizedProps:null,k=h.children,Al(f,h)?k=null:x!==null&&Al(f,x)&&(i.flags|=32),mc(n,i),Bn(n,i,k,u),i.child;case 6:return n===null&&Bp(i),null;case 13:return Xm(n,i,u);case 4:return Qp(i,i.stateNode.containerInfo),f=i.pendingProps,n===null?i.child=ya(i,null,f,u):Bn(n,i,f,u),i.child;case 11:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:mi(f,h),rf(n,i,f,h,u);case 7:return Bn(n,i,i.pendingProps,u),i.child;case 8:return Bn(n,i,i.pendingProps.children,u),i.child;case 12:return Bn(n,i,i.pendingProps.children,u),i.child;case 10:e:{if(f=i.type._context,h=i.pendingProps,x=i.memoizedProps,k=h.value,gn(Ce,f._currentValue),f._currentValue=k,x!==null)if(ha(x.value,k)){if(x.children===h.children&&!Qn.current){i=Cr(n,i,u);break e}}else for(x=i.child,x!==null&&(x.return=i);x!==null;){var A=x.dependencies;if(A!==null){k=x.child;for(var z=A.firstContext;z!==null;){if(z.context===f){if(x.tag===1){z=xo(-1,u&-u),z.tag=2;var X=x.updateQueue;if(X!==null){X=X.shared;var pe=X.pending;pe===null?z.next=z:(z.next=pe.next,pe.next=z),X.pending=z}}x.lanes|=u,z=x.alternate,z!==null&&(z.lanes|=u),Yp(x.return,u,i),A.lanes|=u;break}z=z.next}}else if(x.tag===10)k=x.type===i.type?null:x.child;else if(x.tag===18){if(k=x.return,k===null)throw Error(s(341));k.lanes|=u,A=k.alternate,A!==null&&(A.lanes|=u),Yp(k,u,i),k=x.sibling}else k=x.child;if(k!==null)k.return=x;else for(k=x;k!==null;){if(k===i){k=null;break}if(x=k.sibling,x!==null){x.return=k.return,k=x;break}k=k.return}x=k}Bn(n,i,h.children,u),i=i.child}return i;case 9:return h=i.type,f=i.pendingProps.children,Fs(i,u),h=nn(h),f=f(h),i.flags|=1,Bn(n,i,f,u),i.child;case 14:return f=i.type,h=mi(f,i.pendingProps),h=mi(f.type,h),vi(n,i,f,h,u);case 15:return Hl(n,i,i.type,i.pendingProps,u);case 17:return f=i.type,h=i.pendingProps,h=i.elementType===f?h:mi(f,h),ea(n,i),i.tag=1,Ln(f)?(n=!0,_l(i)):n=!1,Fs(i,u),Km(i,f,h),th(i,f,h,u),ah(null,i,f,!0,n,u);case 19:return yi(n,i,u);case 22:return xt(n,i,u)}throw Error(s(156,i.tag))};function fv(n,i){return yn(n,i)}function F0(n,i,u,f){this.tag=n,this.key=u,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.ref=null,this.pendingProps=i,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=f,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function ra(n,i,u,f){return new F0(n,i,u,f)}function yh(n){return n=n.prototype,!(!n||!n.isReactComponent)}function I0(n){if(typeof n=="function")return yh(n)?1:0;if(n!=null){if(n=n.$$typeof,n===bt)return 11;if(n===Bt)return 14}return 2}function rl(n,i){var u=n.alternate;return u===null?(u=ra(n.tag,i,n.key,n.mode),u.elementType=n.elementType,u.type=n.type,u.stateNode=n.stateNode,u.alternate=n,n.alternate=u):(u.pendingProps=i,u.type=n.type,u.flags=0,u.subtreeFlags=0,u.deletions=null),u.flags=n.flags&14680064,u.childLanes=n.childLanes,u.lanes=n.lanes,u.child=n.child,u.memoizedProps=n.memoizedProps,u.memoizedState=n.memoizedState,u.updateQueue=n.updateQueue,i=n.dependencies,u.dependencies=i===null?null:{lanes:i.lanes,firstContext:i.firstContext},u.sibling=n.sibling,u.index=n.index,u.ref=n.ref,u}function xf(n,i,u,f,h,x){var k=2;if(f=n,typeof n=="function")yh(n)&&(k=1);else if(typeof n=="string")k=5;else e:switch(n){case le:return il(u.children,h,x,i);case Oe:k=8,h|=8;break;case ft:return n=ra(12,u,i,h|2),n.elementType=ft,n.lanes=x,n;case rt:return n=ra(13,u,i,h),n.elementType=rt,n.lanes=x,n;case Be:return n=ra(19,u,i,h),n.elementType=Be,n.lanes=x,n;case pt:return tu(u,h,x,i);default:if(typeof n=="object"&&n!==null)switch(n.$$typeof){case He:k=10;break e;case Tt:k=9;break e;case bt:k=11;break e;case Bt:k=14;break e;case kt:k=16,f=null;break e}throw Error(s(130,n==null?n:typeof n,""))}return i=ra(k,u,i,h),i.elementType=n,i.type=f,i.lanes=x,i}function il(n,i,u,f){return n=ra(7,n,f,i),n.lanes=u,n}function tu(n,i,u,f){return n=ra(22,n,f,i),n.elementType=pt,n.lanes=u,n.stateNode={isHidden:!1},n}function Jl(n,i,u){return n=ra(6,n,null,i),n.lanes=u,n}function xh(n,i,u){return i=ra(4,n.children!==null?n.children:[],n.key,i),i.lanes=u,i.stateNode={containerInfo:n.containerInfo,pendingChildren:null,implementation:n.implementation},i}function pv(n,i,u,f,h){this.tag=i,this.containerInfo=n,this.finishedWork=this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.pendingContext=this.context=null,this.callbackPriority=0,this.eventTimes=Cl(0),this.expirationTimes=Cl(-1),this.entangledLanes=this.finishedLanes=this.mutableReadLanes=this.expiredLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=Cl(0),this.identifierPrefix=f,this.onRecoverableError=h,this.mutableSourceEagerHydrationData=null}function bf(n,i,u,f,h,x,k,A,z){return n=new pv(n,i,u,A,z),i===1?(i=1,x===!0&&(i|=8)):i=0,x=ra(3,null,null,i),n.current=x,x.stateNode=n,x.memoizedState={element:f,isDehydrated:u,cache:null,transitions:null,pendingSuspenseBoundaries:null},Go(x),n}function hv(n,i,u){var f=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:se,key:f==null?null:""+f,children:n,containerInfo:i,implementation:u}}function gv(n){if(!n)return Et;n=n._reactInternals;e:{if(Re(n)!==n||n.tag!==1)throw Error(s(170));var i=n;do{switch(i.tag){case 3:i=i.stateNode.context;break e;case 1:if(Ln(i.type)){i=i.stateNode.__reactInternalMemoizedMergedChildContext;break e}}i=i.return}while(i!==null);throw Error(s(171))}if(n.tag===1){var u=n.type;if(Ln(u))return Lm(n,u,i)}return i}function bh(n,i,u,f,h,x,k,A,z){return n=bf(u,f,!0,n,h,x,k,A,z),n.context=gv(null),u=n.current,f=mn(),h=_i(u),x=xo(f,h),x.callback=i??null,Ko(u,x,h),n.current.lanes=h,zo(n,h,f),gr(n,f),n}function wf(n,i,u,f){var h=i.current,x=mn(),k=_i(h);return u=gv(u),i.context===null?i.context=u:i.pendingContext=u,i=xo(x,k),i.payload={element:n},f=f===void 0?null:f,f!==null&&(i.callback=f),n=Ko(h,i,k),n!==null&&(Li(n,h,k,x),_d(n,h,k)),k}function Sf(n){if(n=n.current,!n.child)return null;switch(n.child.tag){case 5:return n.child.stateNode;default:return n.child.stateNode}}function mv(n,i){if(n=n.memoizedState,n!==null&&n.dehydrated!==null){var u=n.retryLane;n.retryLane=u!==0&&u<i?u:i}}function Cf(n,i){mv(n,i),(n=n.alternate)&&mv(n,i)}function vv(){return null}var wh=typeof reportError=="function"?reportError:function(n){console.error(n)};function al(n){this._internalRoot=n}Ef.prototype.render=al.prototype.render=function(n){var i=this._internalRoot;if(i===null)throw Error(s(409));wf(n,i,null,null)},Ef.prototype.unmount=al.prototype.unmount=function(){var n=this._internalRoot;if(n!==null){this._internalRoot=null;var i=n.containerInfo;ql(function(){wf(null,n,null,null)}),i[mo]=null}};function Ef(n){this._internalRoot=n}Ef.prototype.unstable_scheduleHydration=function(n){if(n){var i=za();n={blockedOn:null,target:n,priority:i};for(var u=0;u<fa.length&&i!==0&&i<fa[u].priority;u++);fa.splice(u,0,n),u===0&&xs(n)}};function Sh(n){return!(!n||n.nodeType!==1&&n.nodeType!==9&&n.nodeType!==11)}function Tf(n){return!(!n||n.nodeType!==1&&n.nodeType!==9&&n.nodeType!==11&&(n.nodeType!==8||n.nodeValue!==" react-mount-point-unstable "))}function yv(){}function U0(n,i,u,f,h){if(h){if(typeof f=="function"){var x=f;f=function(){var X=Sf(k);x.call(X)}}var k=bh(i,f,n,0,null,!1,!1,"",yv);return n._reactRootContainer=k,n[mo]=k.current,qu(n.nodeType===8?n.parentNode:n),ql(),k}for(;h=n.lastChild;)n.removeChild(h);if(typeof f=="function"){var A=f;f=function(){var X=Sf(z);A.call(X)}}var z=bf(n,0,!1,null,null,!1,!1,"",yv);return n._reactRootContainer=z,n[mo]=z.current,qu(n.nodeType===8?n.parentNode:n),ql(function(){wf(i,z,u,f)}),z}function kf(n,i,u,f,h){var x=u._reactRootContainer;if(x){var k=x;if(typeof h=="function"){var A=h;h=function(){var z=Sf(k);A.call(z)}}wf(i,k,n,h)}else k=U0(u,i,n,h,f);return Sf(k)}ys=function(n){switch(n.tag){case 3:var i=n.stateNode;if(i.current.memoizedState.isDehydrated){var u=si(i.pendingLanes);u!==0&&(zu(i,u|1),gr(i,Kt()),!(jt&6)&&(Xs=Kt()+500,Gr()))}break;case 13:ql(function(){var f=Ba(n,1);if(f!==null){var h=mn();Li(f,n,1,h)}}),Cf(n,1)}},Pt=function(n){if(n.tag===13){var i=Ba(n,134217728);if(i!==null){var u=mn();Li(i,n,134217728,u)}Cf(n,134217728)}},md=function(n){if(n.tag===13){var i=_i(n),u=Ba(n,i);if(u!==null){var f=mn();Li(u,n,i,f)}Cf(n,i)}},za=function(){return Nt},lt=function(n,i){var u=Nt;try{return Nt=n,i()}finally{Nt=u}},on=function(n,i,u){switch(i){case"input":if(Yn(n,u),i=u.name,u.type==="radio"&&i!=null){for(u=n;u.parentNode;)u=u.parentNode;for(u=u.querySelectorAll("input[name="+JSON.stringify(""+i)+'][type="radio"]'),i=0;i<u.length;i++){var f=u[i];if(f!==n&&f.form===n.form){var h=vo(f);if(!h)throw Error(s(90));an(f),Yn(f,h)}}}break;case"textarea":_r(n,u);break;case"select":i=u.value,i!=null&&tr(n,!!u.multiple,i,!1)}},xl=mf,bl=ql;var xv={usingClientEntryPoint:!1,Events:[ec,Ye,vo,Yi,ro,mf]},Oc={findFiberByHostInstance:jl,bundleType:0,version:"18.3.1",rendererPackageName:"react-dom"},B0={bundleType:Oc.bundleType,version:Oc.version,rendererPackageName:Oc.rendererPackageName,rendererConfig:Oc.rendererConfig,overrideHookState:null,overrideHookStateDeletePath:null,overrideHookStateRenamePath:null,overrideProps:null,overridePropsDeletePath:null,overridePropsRenamePath:null,setErrorHandler:null,setSuspenseHandler:null,scheduleUpdate:null,currentDispatcherRef:he.ReactCurrentDispatcher,findHostInstanceByFiber:function(n){return n=St(n),n===null?null:n.stateNode},findFiberByHostInstance:Oc.findFiberByHostInstance||vv,findHostInstancesForRefresh:null,scheduleRefresh:null,scheduleRoot:null,setRefreshHandler:null,getCurrentFiber:null,reconcilerVersion:"18.3.1-next-f1338f8080-20240426"};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"){var $c=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!$c.isDisabled&&$c.supportsFiber)try{jo=$c.inject(B0),li=$c}catch{}}return Ui.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=xv,Ui.createPortal=function(n,i){var u=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!Sh(i))throw Error(s(200));return hv(n,i,null,u)},Ui.createRoot=function(n,i){if(!Sh(n))throw Error(s(299));var u=!1,f="",h=wh;return i!=null&&(i.unstable_strictMode===!0&&(u=!0),i.identifierPrefix!==void 0&&(f=i.identifierPrefix),i.onRecoverableError!==void 0&&(h=i.onRecoverableError)),i=bf(n,1,!1,null,null,u,!1,f,h),n[mo]=i.current,qu(n.nodeType===8?n.parentNode:n),new al(i)},Ui.findDOMNode=function(n){if(n==null)return null;if(n.nodeType===1)return n;var i=n._reactInternals;if(i===void 0)throw typeof n.render=="function"?Error(s(188)):(n=Object.keys(n).join(","),Error(s(268,n)));return n=St(i),n=n===null?null:n.stateNode,n},Ui.flushSync=function(n){return ql(n)},Ui.hydrate=function(n,i,u){if(!Tf(i))throw Error(s(200));return kf(null,n,i,!0,u)},Ui.hydrateRoot=function(n,i,u){if(!Sh(n))throw Error(s(405));var f=u!=null&&u.hydratedSources||null,h=!1,x="",k=wh;if(u!=null&&(u.unstable_strictMode===!0&&(h=!0),u.identifierPrefix!==void 0&&(x=u.identifierPrefix),u.onRecoverableError!==void 0&&(k=u.onRecoverableError)),i=bh(i,null,n,1,u??null,h,!1,x,k),n[mo]=i.current,qu(n),f)for(n=0;n<f.length;n++)u=f[n],h=u._getVersion,h=h(u._source),i.mutableSourceEagerHydrationData==null?i.mutableSourceEagerHydrationData=[u,h]:i.mutableSourceEagerHydrationData.push(u,h);return new Ef(i)},Ui.render=function(n,i,u){if(!Tf(i))throw Error(s(200));return kf(null,n,i,!1,u)},Ui.unmountComponentAtNode=function(n){if(!Tf(n))throw Error(s(40));return n._reactRootContainer?(ql(function(){kf(null,null,n,!1,function(){n._reactRootContainer=null,n[mo]=null})}),!0):!1},Ui.unstable_batchedUpdates=mf,Ui.unstable_renderSubtreeIntoContainer=function(n,i,u,f){if(!Tf(u))throw Error(s(200));if(n==null||n._reactInternals===void 0)throw Error(s(38));return kf(n,i,u,!1,f)},Ui.version="18.3.1-next-f1338f8080-20240426",Ui}var Bi={},VS;function oD(){if(VS)return Bi;VS=1;var o={};/**
 * @license React
 * react-dom.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */return o.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var r=Ze,s=BS(),d=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,g=!1;function b(e){g=e}function S(e){if(!g){for(var t=arguments.length,a=new Array(t>1?t-1:0),l=1;l<t;l++)a[l-1]=arguments[l];T("warn",e,a)}}function v(e){if(!g){for(var t=arguments.length,a=new Array(t>1?t-1:0),l=1;l<t;l++)a[l-1]=arguments[l];T("error",e,a)}}function T(e,t,a){{var l=d.ReactDebugCurrentFrame,c=l.getStackAddendum();c!==""&&(t+="%s",a=a.concat([c]));var p=a.map(function(m){return String(m)});p.unshift("Warning: "+t),Function.prototype.apply.call(console[e],console,p)}}var $=0,O=1,P=2,_=3,V=4,N=5,J=6,xe=7,Pe=8,de=9,ue=10,he=11,Le=12,se=13,le=14,Oe=15,ft=16,He=17,Tt=18,bt=19,rt=21,Be=22,Bt=23,kt=24,pt=25,oe=!0,Te=!1,be=!1,B=!1,re=!1,Ve=!0,et=!0,it=!0,ht=!0,Ot=new Set,tt={},vt={};function Ut(e,t){pn(e,t),pn(e+"Capture",t)}function pn(e,t){tt[e]&&v("EventRegistry: More than one plugin attempted to publish the same registration name, `%s`.",e),tt[e]=t;{var a=e.toLowerCase();vt[a]=e,e==="onDoubleClick"&&(vt.ondblclick=e)}for(var l=0;l<t.length;l++)Ot.add(t[l])}var an=typeof window<"u"&&typeof window.document<"u"&&typeof window.document.createElement<"u",Pn=Object.prototype.hasOwnProperty;function xn(e){{var t=typeof Symbol=="function"&&Symbol.toStringTag,a=t&&e[Symbol.toStringTag]||e.constructor.name||"Object";return a}}function $n(e){try{return er(e),!1}catch{return!0}}function er(e){return""+e}function Yn(e,t){if($n(e))return v("The provided `%s` attribute is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),er(e)}function Ti(e){if($n(e))return v("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",xn(e)),er(e)}function ua(e,t){if($n(e))return v("The provided `%s` prop is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),er(e)}function Hr(e,t){if($n(e))return v("The provided `%s` CSS property is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),er(e)}function tr(e){if($n(e))return v("The provided HTML markup uses a value of unsupported type %s. This value must be coerced to a string before before using it here.",xn(e)),er(e)}function sr(e){if($n(e))return v("Form field values (value, checked, defaultValue, or defaultChecked props) must be strings, not %s. This value must be coerced to a string before before using it here.",xn(e)),er(e)}var ur=0,_r=1,ca=2,Gn=3,xr=4,ai=5,no=6,ki=":A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD",we=ki+"\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040",Qe=new RegExp("^["+ki+"]["+we+"]*$"),wt={},Yt={};function bn(e){return Pn.call(Yt,e)?!0:Pn.call(wt,e)?!1:Qe.test(e)?(Yt[e]=!0,!0):(wt[e]=!0,v("Invalid attribute name: `%s`",e),!1)}function wn(e,t,a){return t!==null?t.type===ur:a?!1:e.length>2&&(e[0]==="o"||e[0]==="O")&&(e[1]==="n"||e[1]==="N")}function Sn(e,t,a,l){if(a!==null&&a.type===ur)return!1;switch(typeof t){case"function":case"symbol":return!0;case"boolean":{if(l)return!1;if(a!==null)return!a.acceptsBooleans;var c=e.toLowerCase().slice(0,5);return c!=="data-"&&c!=="aria-"}default:return!1}}function cr(e,t,a,l){if(t===null||typeof t>"u"||Sn(e,t,a,l))return!0;if(l)return!1;if(a!==null)switch(a.type){case Gn:return!t;case xr:return t===!1;case ai:return isNaN(t);case no:return isNaN(t)||t<1}return!1}function vn(e){return Gt.hasOwnProperty(e)?Gt[e]:null}function on(e,t,a,l,c,p,m){this.acceptsBooleans=t===ca||t===Gn||t===xr,this.attributeName=l,this.attributeNamespace=c,this.mustUseProperty=a,this.propertyName=e,this.type=t,this.sanitizeURL=p,this.removeEmptyString=m}var Gt={},Ri=["children","dangerouslySetInnerHTML","defaultValue","defaultChecked","innerHTML","suppressContentEditableWarning","suppressHydrationWarning","style"];Ri.forEach(function(e){Gt[e]=new on(e,ur,!1,e,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(e){var t=e[0],a=e[1];Gt[t]=new on(t,_r,!1,a,null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(e){Gt[e]=new on(e,ca,!1,e.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(e){Gt[e]=new on(e,ca,!1,e,null,!1,!1)}),["allowFullScreen","async","autoFocus","autoPlay","controls","default","defer","disabled","disablePictureInPicture","disableRemotePlayback","formNoValidate","hidden","loop","noModule","noValidate","open","playsInline","readOnly","required","reversed","scoped","seamless","itemScope"].forEach(function(e){Gt[e]=new on(e,Gn,!1,e.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(e){Gt[e]=new on(e,Gn,!0,e,null,!1,!1)}),["capture","download"].forEach(function(e){Gt[e]=new on(e,xr,!1,e,null,!1,!1)}),["cols","rows","size","span"].forEach(function(e){Gt[e]=new on(e,no,!1,e,null,!1,!1)}),["rowSpan","start"].forEach(function(e){Gt[e]=new on(e,ai,!1,e.toLowerCase(),null,!1,!1)});var Wi=/[\-\:]([a-z])/g,Yi=function(e){return e[1].toUpperCase()};["accent-height","alignment-baseline","arabic-form","baseline-shift","cap-height","clip-path","clip-rule","color-interpolation","color-interpolation-filters","color-profile","color-rendering","dominant-baseline","enable-background","fill-opacity","fill-rule","flood-color","flood-opacity","font-family","font-size","font-size-adjust","font-stretch","font-style","font-variant","font-weight","glyph-name","glyph-orientation-horizontal","glyph-orientation-vertical","horiz-adv-x","horiz-origin-x","image-rendering","letter-spacing","lighting-color","marker-end","marker-mid","marker-start","overline-position","overline-thickness","paint-order","panose-1","pointer-events","rendering-intent","shape-rendering","stop-color","stop-opacity","strikethrough-position","strikethrough-thickness","stroke-dasharray","stroke-dashoffset","stroke-linecap","stroke-linejoin","stroke-miterlimit","stroke-opacity","stroke-width","text-anchor","text-decoration","text-rendering","underline-position","underline-thickness","unicode-bidi","unicode-range","units-per-em","v-alphabetic","v-hanging","v-ideographic","v-mathematical","vector-effect","vert-adv-y","vert-origin-x","vert-origin-y","word-spacing","writing-mode","xmlns:xlink","x-height"].forEach(function(e){var t=e.replace(Wi,Yi);Gt[t]=new on(t,_r,!1,e,null,!1,!1)}),["xlink:actuate","xlink:arcrole","xlink:role","xlink:show","xlink:title","xlink:type"].forEach(function(e){var t=e.replace(Wi,Yi);Gt[t]=new on(t,_r,!1,e,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(e){var t=e.replace(Wi,Yi);Gt[t]=new on(t,_r,!1,e,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(e){Gt[e]=new on(e,_r,!1,e.toLowerCase(),null,!1,!1)});var ro="xlinkHref";Gt[ro]=new on("xlinkHref",_r,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(e){Gt[e]=new on(e,_r,!1,e.toLowerCase(),null,!0,!0)});var xl=/^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*\:/i,bl=!1;function io(e){!bl&&xl.test(e)&&(bl=!0,v("A future version of React will block javascript: URLs as a security precaution. Use event handlers instead if you can. If you need to generate unsafe HTML try using dangerouslySetInnerHTML instead. React was passed %s.",JSON.stringify(e)))}function wl(e,t,a,l){if(l.mustUseProperty){var c=l.propertyName;return e[c]}else{Yn(a,t),l.sanitizeURL&&io(""+a);var p=l.attributeName,m=null;if(l.type===xr){if(e.hasAttribute(p)){var w=e.getAttribute(p);return w===""?!0:cr(t,a,l,!1)?w:w===""+a?a:w}}else if(e.hasAttribute(p)){if(cr(t,a,l,!1))return e.getAttribute(p);if(l.type===Gn)return a;m=e.getAttribute(p)}return cr(t,a,l,!1)?m===null?a:m:m===""+a?a:m}}function $a(e,t,a,l){{if(!bn(t))return;if(!e.hasAttribute(t))return a===void 0?void 0:null;var c=e.getAttribute(t);return Yn(a,t),c===""+a?a:c}}function Di(e,t,a,l){var c=vn(t);if(!wn(t,c,l)){if(cr(t,a,c,l)&&(a=null),l||c===null){if(bn(t)){var p=t;a===null?e.removeAttribute(p):(Yn(a,t),e.setAttribute(p,""+a))}return}var m=c.mustUseProperty;if(m){var w=c.propertyName;if(a===null){var C=c.type;e[w]=C===Gn?!1:""}else e[w]=a;return}var R=c.attributeName,M=c.attributeNamespace;if(a===null)e.removeAttribute(R);else{var U=c.type,F;U===Gn||U===xr&&a===!0?F="":(Yn(a,R),F=""+a,c.sanitizeURL&&io(F.toString())),M?e.setAttributeNS(M,R,F):e.setAttribute(R,F)}}}var br=Symbol.for("react.element"),Mi=Symbol.for("react.portal"),oi=Symbol.for("react.fragment"),Aa=Symbol.for("react.strict_mode"),ja=Symbol.for("react.profiler"),ao=Symbol.for("react.provider"),L=Symbol.for("react.context"),ce=Symbol.for("react.forward_ref"),Ee=Symbol.for("react.suspense"),Re=Symbol.for("react.suspense_list"),Rt=Symbol.for("react.memo"),ut=Symbol.for("react.lazy"),$t=Symbol.for("react.scope"),St=Symbol.for("react.debug_trace_mode"),Fn=Symbol.for("react.offscreen"),yn=Symbol.for("react.legacy_hidden"),Cn=Symbol.for("react.cache"),Lr=Symbol.for("react.tracing_marker"),da=Symbol.iterator,Kt="@@iterator";function kn(e){if(e===null||typeof e!="object")return null;var t=da&&e[da]||e[Kt];return typeof t=="function"?t:null}var gt=Object.assign,_a=0,oo,pd,lo,jo,li,ju,Vr;function _u(){}_u.__reactDisabledLog=!0;function hd(){{if(_a===0){oo=console.log,pd=console.info,lo=console.warn,jo=console.error,li=console.group,ju=console.groupCollapsed,Vr=console.groupEnd;var e={configurable:!0,enumerable:!0,value:_u,writable:!0};Object.defineProperties(console,{info:e,log:e,warn:e,error:e,group:e,groupCollapsed:e,groupEnd:e})}_a++}}function gd(){{if(_a--,_a===0){var e={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:gt({},e,{value:oo}),info:gt({},e,{value:pd}),warn:gt({},e,{value:lo}),error:gt({},e,{value:jo}),group:gt({},e,{value:li}),groupCollapsed:gt({},e,{value:ju}),groupEnd:gt({},e,{value:Vr})})}_a<0&&v("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var so=d.ReactCurrentDispatcher,_o;function si(e,t,a){{if(_o===void 0)try{throw Error()}catch(c){var l=c.stack.trim().match(/\n( *(at )?)/);_o=l&&l[1]||""}return`
`+_o+e}}var La=!1,Lo;{var vs=typeof WeakMap=="function"?WeakMap:Map;Lo=new vs}function uo(e,t){if(!e||La)return"";{var a=Lo.get(e);if(a!==void 0)return a}var l;La=!0;var c=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var p;p=so.current,so.current=null,hd();try{if(t){var m=function(){throw Error()};if(Object.defineProperty(m.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(m,[])}catch(Z){l=Z}Reflect.construct(e,[],m)}else{try{m.call()}catch(Z){l=Z}e.call(m.prototype)}}else{try{throw Error()}catch(Z){l=Z}e()}}catch(Z){if(Z&&l&&typeof Z.stack=="string"){for(var w=Z.stack.split(`
`),C=l.stack.split(`
`),R=w.length-1,M=C.length-1;R>=1&&M>=0&&w[R]!==C[M];)M--;for(;R>=1&&M>=0;R--,M--)if(w[R]!==C[M]){if(R!==1||M!==1)do if(R--,M--,M<0||w[R]!==C[M]){var U=`
`+w[R].replace(" at new "," at ");return e.displayName&&U.includes("<anonymous>")&&(U=U.replace("<anonymous>",e.displayName)),typeof e=="function"&&Lo.set(e,U),U}while(R>=1&&M>=0);break}}}finally{La=!1,so.current=p,gd(),Error.prepareStackTrace=c}var F=e?e.displayName||e.name:"",q=F?si(F):"";return typeof e=="function"&&Lo.set(e,q),q}function Sl(e,t,a){return uo(e,!0)}function Cl(e,t,a){return uo(e,!1)}function zo(e){var t=e.prototype;return!!(t&&t.isReactComponent)}function Lu(e,t,a){if(e==null)return"";if(typeof e=="function")return uo(e,zo(e));if(typeof e=="string")return si(e);switch(e){case Ee:return si("Suspense");case Re:return si("SuspenseList")}if(typeof e=="object")switch(e.$$typeof){case ce:return Cl(e.render);case Rt:return Lu(e.type,t,a);case ut:{var l=e,c=l._payload,p=l._init;try{return Lu(p(c),t,a)}catch{}}}return""}function zu(e){switch(e._debugOwner&&e._debugOwner.type,e._debugSource,e.tag){case N:return si(e.type);case ft:return si("Lazy");case se:return si("Suspense");case bt:return si("SuspenseList");case $:case P:case Oe:return Cl(e.type);case he:return Cl(e.type.render);case O:return Sl(e.type);default:return""}}function Nt(e){try{var t="",a=e;do t+=zu(a),a=a.return;while(a);return t}catch(l){return`
Error generating stack: `+l.message+`
`+l.stack}}function Nu(e,t,a){var l=e.displayName;if(l)return l;var c=t.displayName||t.name||"";return c!==""?a+"("+c+")":a}function ys(e){return e.displayName||"Context"}function Pt(e){if(e==null)return null;if(typeof e.tag=="number"&&v("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof e=="function")return e.displayName||e.name||null;if(typeof e=="string")return e;switch(e){case oi:return"Fragment";case Mi:return"Portal";case ja:return"Profiler";case Aa:return"StrictMode";case Ee:return"Suspense";case Re:return"SuspenseList"}if(typeof e=="object")switch(e.$$typeof){case L:var t=e;return ys(t)+".Consumer";case ao:var a=e;return ys(a._context)+".Provider";case ce:return Nu(e,e.render,"ForwardRef");case Rt:var l=e.displayName||null;return l!==null?l:Pt(e.type)||"Memo";case ut:{var c=e,p=c._payload,m=c._init;try{return Pt(m(p))}catch{return null}}}return null}function md(e,t,a){var l=t.displayName||t.name||"";return e.displayName||(l!==""?a+"("+l+")":a)}function za(e){return e.displayName||"Context"}function lt(e){var t=e.tag,a=e.type;switch(t){case kt:return"Cache";case de:var l=a;return za(l)+".Consumer";case ue:var c=a;return za(c._context)+".Provider";case Tt:return"DehydratedFragment";case he:return md(a,a.render,"ForwardRef");case xe:return"Fragment";case N:return a;case V:return"Portal";case _:return"Root";case J:return"Text";case ft:return Pt(a);case Pe:return a===Aa?"StrictMode":"Mode";case Be:return"Offscreen";case Le:return"Profiler";case rt:return"Scope";case se:return"Suspense";case bt:return"SuspenseList";case pt:return"TracingMarker";case O:case $:case He:case P:case le:case Oe:if(typeof a=="function")return a.displayName||a.name||null;if(typeof a=="string")return a;break}return null}var El=d.ReactDebugCurrentFrame,dr=null,ui=!1;function Wr(){{if(dr===null)return null;var e=dr._debugOwner;if(e!==null&&typeof e<"u")return lt(e)}return null}function Na(){return dr===null?"":Nt(dr)}function jn(){El.getCurrentStack=null,dr=null,ui=!1}function ln(e){El.getCurrentStack=e===null?null:Na,dr=e,ui=!1}function fa(){return dr}function Gi(e){ui=e}function zr(e){return""+e}function Yr(e){switch(typeof e){case"boolean":case"number":case"string":case"undefined":return e;case"object":return sr(e),e;default:return""}}var wp={button:!0,checkbox:!0,image:!0,hidden:!0,radio:!0,reset:!0,submit:!0};function xs(e,t){wp[t.type]||t.onChange||t.onInput||t.readOnly||t.disabled||t.value==null||v("You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set either `onChange` or `readOnly`."),t.onChange||t.readOnly||t.disabled||t.checked==null||v("You provided a `checked` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultChecked`. Otherwise, set either `onChange` or `readOnly`.")}function Tl(e){var t=e.type,a=e.nodeName;return a&&a.toLowerCase()==="input"&&(t==="checkbox"||t==="radio")}function bs(e){return e._valueTracker}function ws(e){e._valueTracker=null}function kl(e){var t="";return e&&(Tl(e)?t=e.checked?"true":"false":t=e.value),t}function Ki(e){var t=Tl(e)?"checked":"value",a=Object.getOwnPropertyDescriptor(e.constructor.prototype,t);sr(e[t]);var l=""+e[t];if(!(e.hasOwnProperty(t)||typeof a>"u"||typeof a.get!="function"||typeof a.set!="function")){var c=a.get,p=a.set;Object.defineProperty(e,t,{configurable:!0,get:function(){return c.call(this)},set:function(w){sr(w),l=""+w,p.call(this,w)}}),Object.defineProperty(e,t,{enumerable:a.enumerable});var m={getValue:function(){return l},setValue:function(w){sr(w),l=""+w},stopTracking:function(){ws(e),delete e[t]}};return m}}function Qi(e){bs(e)||(e._valueTracker=Ki(e))}function No(e){if(!e)return!1;var t=bs(e);if(!t)return!0;var a=t.getValue(),l=kl(e);return l!==a?(t.setValue(l),!0):!1}function co(e){if(e=e||(typeof document<"u"?document:void 0),typeof e>"u")return null;try{return e.activeElement||e.body}catch{return e.body}}var Ss=!1,Po=!1,fo=!1,Cs=!1;function Pu(e){var t=e.type==="checkbox"||e.type==="radio";return t?e.checked!=null:e.value!=null}function qi(e,t){var a=e,l=t.checked,c=gt({},t,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:l??a._wrapperState.initialChecked});return c}function Es(e,t){xs("input",t),t.checked!==void 0&&t.defaultChecked!==void 0&&!Po&&(v("%s contains an input of type %s with both checked and defaultChecked props. Input elements must be either controlled or uncontrolled (specify either the checked prop, or the defaultChecked prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://reactjs.org/link/controlled-components",Wr()||"A component",t.type),Po=!0),t.value!==void 0&&t.defaultValue!==void 0&&!Ss&&(v("%s contains an input of type %s with both value and defaultValue props. Input elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://reactjs.org/link/controlled-components",Wr()||"A component",t.type),Ss=!0);var a=e,l=t.defaultValue==null?"":t.defaultValue;a._wrapperState={initialChecked:t.checked!=null?t.checked:t.defaultChecked,initialValue:Yr(t.value!=null?t.value:l),controlled:Pu(t)}}function E(e,t){var a=e,l=t.checked;l!=null&&Di(a,"checked",l,!1)}function j(e,t){var a=e;{var l=Pu(t);!a._wrapperState.controlled&&l&&!Cs&&(v("A component is changing an uncontrolled input to be controlled. This is likely caused by the value changing from undefined to a defined value, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://reactjs.org/link/controlled-components"),Cs=!0),a._wrapperState.controlled&&!l&&!fo&&(v("A component is changing a controlled input to be uncontrolled. This is likely caused by the value changing from a defined to undefined, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://reactjs.org/link/controlled-components"),fo=!0)}E(e,t);var c=Yr(t.value),p=t.type;if(c!=null)p==="number"?(c===0&&a.value===""||a.value!=c)&&(a.value=zr(c)):a.value!==zr(c)&&(a.value=zr(c));else if(p==="submit"||p==="reset"){a.removeAttribute("value");return}t.hasOwnProperty("value")?Ne(a,t.type,c):t.hasOwnProperty("defaultValue")&&Ne(a,t.type,Yr(t.defaultValue)),t.checked==null&&t.defaultChecked!=null&&(a.defaultChecked=!!t.defaultChecked)}function Q(e,t,a){var l=e;if(t.hasOwnProperty("value")||t.hasOwnProperty("defaultValue")){var c=t.type,p=c==="submit"||c==="reset";if(p&&(t.value===void 0||t.value===null))return;var m=zr(l._wrapperState.initialValue);a||m!==l.value&&(l.value=m),l.defaultValue=m}var w=l.name;w!==""&&(l.name=""),l.defaultChecked=!l.defaultChecked,l.defaultChecked=!!l._wrapperState.initialChecked,w!==""&&(l.name=w)}function ee(e,t){var a=e;j(a,t),ye(a,t)}function ye(e,t){var a=t.name;if(t.type==="radio"&&a!=null){for(var l=e;l.parentNode;)l=l.parentNode;Yn(a,"name");for(var c=l.querySelectorAll("input[name="+JSON.stringify(""+a)+'][type="radio"]'),p=0;p<c.length;p++){var m=c[p];if(!(m===e||m.form!==e.form)){var w=zv(m);if(!w)throw new Error("ReactDOMInput: Mixing React and non-React radio inputs with the same `name` is not supported.");No(m),j(m,w)}}}}function Ne(e,t,a){(t!=="number"||co(e.ownerDocument)!==e)&&(a==null?e.defaultValue=zr(e._wrapperState.initialValue):e.defaultValue!==zr(a)&&(e.defaultValue=zr(a)))}var $e=!1,at=!1,Ct=!1;function Qt(e,t){t.value==null&&(typeof t.children=="object"&&t.children!==null?r.Children.forEach(t.children,function(a){a!=null&&(typeof a=="string"||typeof a=="number"||at||(at=!0,v("Cannot infer the option value of complex children. Pass a `value` prop or use a plain string as children to <option>.")))}):t.dangerouslySetInnerHTML!=null&&(Ct||(Ct=!0,v("Pass a `value` prop if you set dangerouslyInnerHTML so React knows which value should be selected.")))),t.selected!=null&&!$e&&(v("Use the `defaultValue` or `value` props on <select> instead of setting `selected` on <option>."),$e=!0)}function sn(e,t){t.value!=null&&e.setAttribute("value",zr(Yr(t.value)))}var un=Array.isArray;function yt(e){return un(e)}var hn;hn=!1;function In(){var e=Wr();return e?`

Check the render method of \``+e+"`.":""}var Rl=["value","defaultValue"];function Fu(e){{xs("select",e);for(var t=0;t<Rl.length;t++){var a=Rl[t];if(e[a]!=null){var l=yt(e[a]);e.multiple&&!l?v("The `%s` prop supplied to <select> must be an array if `multiple` is true.%s",a,In()):!e.multiple&&l&&v("The `%s` prop supplied to <select> must be a scalar value if `multiple` is false.%s",a,In())}}}}function po(e,t,a,l){var c=e.options;if(t){for(var p=a,m={},w=0;w<p.length;w++)m["$"+p[w]]=!0;for(var C=0;C<c.length;C++){var R=m.hasOwnProperty("$"+c[C].value);c[C].selected!==R&&(c[C].selected=R),R&&l&&(c[C].defaultSelected=!0)}}else{for(var M=zr(Yr(a)),U=null,F=0;F<c.length;F++){if(c[F].value===M){c[F].selected=!0,l&&(c[F].defaultSelected=!0);return}U===null&&!c[F].disabled&&(U=c[F])}U!==null&&(U.selected=!0)}}function Dl(e,t){return gt({},t,{value:void 0})}function Iu(e,t){var a=e;Fu(t),a._wrapperState={wasMultiple:!!t.multiple},t.value!==void 0&&t.defaultValue!==void 0&&!hn&&(v("Select elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled select element and remove one of these props. More info: https://reactjs.org/link/controlled-components"),hn=!0)}function Sp(e,t){var a=e;a.multiple=!!t.multiple;var l=t.value;l!=null?po(a,!!t.multiple,l,!1):t.defaultValue!=null&&po(a,!!t.multiple,t.defaultValue,!0)}function vd(e,t){var a=e,l=a._wrapperState.wasMultiple;a._wrapperState.wasMultiple=!!t.multiple;var c=t.value;c!=null?po(a,!!t.multiple,c,!1):l!==!!t.multiple&&(t.defaultValue!=null?po(a,!!t.multiple,t.defaultValue,!0):po(a,!!t.multiple,t.multiple?[]:"",!1))}function Cp(e,t){var a=e,l=t.value;l!=null&&po(a,!!t.multiple,l,!1)}var am=!1;function yd(e,t){var a=e;if(t.dangerouslySetInnerHTML!=null)throw new Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");var l=gt({},t,{value:void 0,defaultValue:void 0,children:zr(a._wrapperState.initialValue)});return l}function om(e,t){var a=e;xs("textarea",t),t.value!==void 0&&t.defaultValue!==void 0&&!am&&(v("%s contains a textarea with both value and defaultValue props. Textarea elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled textarea and remove one of these props. More info: https://reactjs.org/link/controlled-components",Wr()||"A component"),am=!0);var l=t.value;if(l==null){var c=t.children,p=t.defaultValue;if(c!=null){v("Use the `defaultValue` or `value` props instead of setting children on <textarea>.");{if(p!=null)throw new Error("If you supply `defaultValue` on a <textarea>, do not pass children.");if(yt(c)){if(c.length>1)throw new Error("<textarea> can only have at most one child.");c=c[0]}p=c}}p==null&&(p=""),l=p}a._wrapperState={initialValue:Yr(l)}}function lm(e,t){var a=e,l=Yr(t.value),c=Yr(t.defaultValue);if(l!=null){var p=zr(l);p!==a.value&&(a.value=p),t.defaultValue==null&&a.defaultValue!==p&&(a.defaultValue=p)}c!=null&&(a.defaultValue=zr(c))}function sm(e,t){var a=e,l=a.textContent;l===a._wrapperState.initialValue&&l!==""&&l!==null&&(a.value=l)}function v0(e,t){lm(e,t)}var pa="http://www.w3.org/1999/xhtml",y0="http://www.w3.org/1998/Math/MathML",Ep="http://www.w3.org/2000/svg";function Tp(e){switch(e){case"svg":return Ep;case"math":return y0;default:return pa}}function xd(e,t){return e==null||e===pa?Tp(t):e===Ep&&t==="foreignObject"?pa:e}var x0=function(e){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(t,a,l,c){MSApp.execUnsafeLocalFunction(function(){return e(t,a,l,c)})}:e},bd,um=x0(function(e,t){if(e.namespaceURI===Ep&&!("innerHTML"in e)){bd=bd||document.createElement("div"),bd.innerHTML="<svg>"+t.valueOf().toString()+"</svg>";for(var a=bd.firstChild;e.firstChild;)e.removeChild(e.firstChild);for(;a.firstChild;)e.appendChild(a.firstChild);return}e.innerHTML=t}),ci=1,ho=3,Kn=8,go=9,Uu=11,Fo=function(e,t){if(t){var a=e.firstChild;if(a&&a===e.lastChild&&a.nodeType===ho){a.nodeValue=t;return}}e.textContent=t},b0={animation:["animationDelay","animationDirection","animationDuration","animationFillMode","animationIterationCount","animationName","animationPlayState","animationTimingFunction"],background:["backgroundAttachment","backgroundClip","backgroundColor","backgroundImage","backgroundOrigin","backgroundPositionX","backgroundPositionY","backgroundRepeat","backgroundSize"],backgroundPosition:["backgroundPositionX","backgroundPositionY"],border:["borderBottomColor","borderBottomStyle","borderBottomWidth","borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth","borderLeftColor","borderLeftStyle","borderLeftWidth","borderRightColor","borderRightStyle","borderRightWidth","borderTopColor","borderTopStyle","borderTopWidth"],borderBlockEnd:["borderBlockEndColor","borderBlockEndStyle","borderBlockEndWidth"],borderBlockStart:["borderBlockStartColor","borderBlockStartStyle","borderBlockStartWidth"],borderBottom:["borderBottomColor","borderBottomStyle","borderBottomWidth"],borderColor:["borderBottomColor","borderLeftColor","borderRightColor","borderTopColor"],borderImage:["borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth"],borderInlineEnd:["borderInlineEndColor","borderInlineEndStyle","borderInlineEndWidth"],borderInlineStart:["borderInlineStartColor","borderInlineStartStyle","borderInlineStartWidth"],borderLeft:["borderLeftColor","borderLeftStyle","borderLeftWidth"],borderRadius:["borderBottomLeftRadius","borderBottomRightRadius","borderTopLeftRadius","borderTopRightRadius"],borderRight:["borderRightColor","borderRightStyle","borderRightWidth"],borderStyle:["borderBottomStyle","borderLeftStyle","borderRightStyle","borderTopStyle"],borderTop:["borderTopColor","borderTopStyle","borderTopWidth"],borderWidth:["borderBottomWidth","borderLeftWidth","borderRightWidth","borderTopWidth"],columnRule:["columnRuleColor","columnRuleStyle","columnRuleWidth"],columns:["columnCount","columnWidth"],flex:["flexBasis","flexGrow","flexShrink"],flexFlow:["flexDirection","flexWrap"],font:["fontFamily","fontFeatureSettings","fontKerning","fontLanguageOverride","fontSize","fontSizeAdjust","fontStretch","fontStyle","fontVariant","fontVariantAlternates","fontVariantCaps","fontVariantEastAsian","fontVariantLigatures","fontVariantNumeric","fontVariantPosition","fontWeight","lineHeight"],fontVariant:["fontVariantAlternates","fontVariantCaps","fontVariantEastAsian","fontVariantLigatures","fontVariantNumeric","fontVariantPosition"],gap:["columnGap","rowGap"],grid:["gridAutoColumns","gridAutoFlow","gridAutoRows","gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],gridArea:["gridColumnEnd","gridColumnStart","gridRowEnd","gridRowStart"],gridColumn:["gridColumnEnd","gridColumnStart"],gridColumnGap:["columnGap"],gridGap:["columnGap","rowGap"],gridRow:["gridRowEnd","gridRowStart"],gridRowGap:["rowGap"],gridTemplate:["gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],listStyle:["listStyleImage","listStylePosition","listStyleType"],margin:["marginBottom","marginLeft","marginRight","marginTop"],marker:["markerEnd","markerMid","markerStart"],mask:["maskClip","maskComposite","maskImage","maskMode","maskOrigin","maskPositionX","maskPositionY","maskRepeat","maskSize"],maskPosition:["maskPositionX","maskPositionY"],outline:["outlineColor","outlineStyle","outlineWidth"],overflow:["overflowX","overflowY"],padding:["paddingBottom","paddingLeft","paddingRight","paddingTop"],placeContent:["alignContent","justifyContent"],placeItems:["alignItems","justifyItems"],placeSelf:["alignSelf","justifySelf"],textDecoration:["textDecorationColor","textDecorationLine","textDecorationStyle"],textEmphasis:["textEmphasisColor","textEmphasisStyle"],transition:["transitionDelay","transitionDuration","transitionProperty","transitionTimingFunction"],wordWrap:["overflowWrap"]},Ts={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0};function cm(e,t){return e+t.charAt(0).toUpperCase()+t.substring(1)}var dm=["Webkit","ms","Moz","O"];Object.keys(Ts).forEach(function(e){dm.forEach(function(t){Ts[cm(t,e)]=Ts[e]})});function wd(e,t,a){var l=t==null||typeof t=="boolean"||t==="";return l?"":!a&&typeof t=="number"&&t!==0&&!(Ts.hasOwnProperty(e)&&Ts[e])?t+"px":(Hr(t,e),(""+t).trim())}var fm=/([A-Z])/g,ks=/^ms-/;function w0(e){return e.replace(fm,"-$1").toLowerCase().replace(ks,"-ms-")}var pm=function(){};{var S0=/^(?:webkit|moz|o)[A-Z]/,hm=/^-ms-/,gm=/-(.)/g,Rs=/;\s*$/,Pa={},kp={},Bu=!1,mm=!1,vm=function(e){return e.replace(gm,function(t,a){return a.toUpperCase()})},Rp=function(e){Pa.hasOwnProperty(e)&&Pa[e]||(Pa[e]=!0,v("Unsupported style property %s. Did you mean %s?",e,vm(e.replace(hm,"ms-"))))},Dp=function(e){Pa.hasOwnProperty(e)&&Pa[e]||(Pa[e]=!0,v("Unsupported vendor-prefixed style property %s. Did you mean %s?",e,e.charAt(0).toUpperCase()+e.slice(1)))},ym=function(e,t){kp.hasOwnProperty(t)&&kp[t]||(kp[t]=!0,v(`Style property values shouldn't contain a semicolon. Try "%s: %s" instead.`,e,t.replace(Rs,"")))},xm=function(e,t){Bu||(Bu=!0,v("`NaN` is an invalid value for the `%s` css style property.",e))},bm=function(e,t){mm||(mm=!0,v("`Infinity` is an invalid value for the `%s` css style property.",e))};pm=function(e,t){e.indexOf("-")>-1?Rp(e):S0.test(e)?Dp(e):Rs.test(t)&&ym(e,t),typeof t=="number"&&(isNaN(t)?xm(e,t):isFinite(t)||bm(e,t))}}var C0=pm;function E0(e){{var t="",a="";for(var l in e)if(e.hasOwnProperty(l)){var c=e[l];if(c!=null){var p=l.indexOf("--")===0;t+=a+(p?l:w0(l))+":",t+=wd(l,c,p),a=";"}}return t||null}}function wm(e,t){var a=e.style;for(var l in t)if(t.hasOwnProperty(l)){var c=l.indexOf("--")===0;c||C0(l,t[l]);var p=wd(l,t[l],c);l==="float"&&(l="cssFloat"),c?a.setProperty(l,p):a[l]=p}}function T0(e){return e==null||typeof e=="boolean"||e===""}function Sm(e){var t={};for(var a in e)for(var l=b0[a]||[a],c=0;c<l.length;c++)t[l[c]]=a;return t}function ha(e,t){{if(!t)return;var a=Sm(e),l=Sm(t),c={};for(var p in a){var m=a[p],w=l[p];if(w&&m!==w){var C=m+","+w;if(c[C])continue;c[C]=!0,v("%s a style property during rerender (%s) when a conflicting property is set (%s) can lead to styling bugs. To avoid this, don't mix shorthand and non-shorthand properties for the same value; instead, replace the shorthand with separate values.",T0(e[m])?"Removing":"Updating",m,w)}}}}var Hu={area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0},Cm=gt({menuitem:!0},Hu),Em="__html";function Sd(e,t){if(t){if(Cm[e]&&(t.children!=null||t.dangerouslySetInnerHTML!=null))throw new Error(e+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");if(t.dangerouslySetInnerHTML!=null){if(t.children!=null)throw new Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");if(typeof t.dangerouslySetInnerHTML!="object"||!(Em in t.dangerouslySetInnerHTML))throw new Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://reactjs.org/link/dangerously-set-inner-html for more information.")}if(!t.suppressContentEditableWarning&&t.contentEditable&&t.children!=null&&v("A component is `contentEditable` and contains `children` managed by React. It is now your responsibility to guarantee that none of those nodes are unexpectedly modified or duplicated. This is probably not intentional."),t.style!=null&&typeof t.style!="object")throw new Error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.")}}function Io(e,t){if(e.indexOf("-")===-1)return typeof t.is=="string";switch(e){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var Ds={accept:"accept",acceptcharset:"acceptCharset","accept-charset":"acceptCharset",accesskey:"accessKey",action:"action",allowfullscreen:"allowFullScreen",alt:"alt",as:"as",async:"async",autocapitalize:"autoCapitalize",autocomplete:"autoComplete",autocorrect:"autoCorrect",autofocus:"autoFocus",autoplay:"autoPlay",autosave:"autoSave",capture:"capture",cellpadding:"cellPadding",cellspacing:"cellSpacing",challenge:"challenge",charset:"charSet",checked:"checked",children:"children",cite:"cite",class:"className",classid:"classID",classname:"className",cols:"cols",colspan:"colSpan",content:"content",contenteditable:"contentEditable",contextmenu:"contextMenu",controls:"controls",controlslist:"controlsList",coords:"coords",crossorigin:"crossOrigin",dangerouslysetinnerhtml:"dangerouslySetInnerHTML",data:"data",datetime:"dateTime",default:"default",defaultchecked:"defaultChecked",defaultvalue:"defaultValue",defer:"defer",dir:"dir",disabled:"disabled",disablepictureinpicture:"disablePictureInPicture",disableremoteplayback:"disableRemotePlayback",download:"download",draggable:"draggable",enctype:"encType",enterkeyhint:"enterKeyHint",for:"htmlFor",form:"form",formmethod:"formMethod",formaction:"formAction",formenctype:"formEncType",formnovalidate:"formNoValidate",formtarget:"formTarget",frameborder:"frameBorder",headers:"headers",height:"height",hidden:"hidden",high:"high",href:"href",hreflang:"hrefLang",htmlfor:"htmlFor",httpequiv:"httpEquiv","http-equiv":"httpEquiv",icon:"icon",id:"id",imagesizes:"imageSizes",imagesrcset:"imageSrcSet",innerhtml:"innerHTML",inputmode:"inputMode",integrity:"integrity",is:"is",itemid:"itemID",itemprop:"itemProp",itemref:"itemRef",itemscope:"itemScope",itemtype:"itemType",keyparams:"keyParams",keytype:"keyType",kind:"kind",label:"label",lang:"lang",list:"list",loop:"loop",low:"low",manifest:"manifest",marginwidth:"marginWidth",marginheight:"marginHeight",max:"max",maxlength:"maxLength",media:"media",mediagroup:"mediaGroup",method:"method",min:"min",minlength:"minLength",multiple:"multiple",muted:"muted",name:"name",nomodule:"noModule",nonce:"nonce",novalidate:"noValidate",open:"open",optimum:"optimum",pattern:"pattern",placeholder:"placeholder",playsinline:"playsInline",poster:"poster",preload:"preload",profile:"profile",radiogroup:"radioGroup",readonly:"readOnly",referrerpolicy:"referrerPolicy",rel:"rel",required:"required",reversed:"reversed",role:"role",rows:"rows",rowspan:"rowSpan",sandbox:"sandbox",scope:"scope",scoped:"scoped",scrolling:"scrolling",seamless:"seamless",selected:"selected",shape:"shape",size:"size",sizes:"sizes",span:"span",spellcheck:"spellCheck",src:"src",srcdoc:"srcDoc",srclang:"srcLang",srcset:"srcSet",start:"start",step:"step",style:"style",summary:"summary",tabindex:"tabIndex",target:"target",title:"title",type:"type",usemap:"useMap",value:"value",width:"width",wmode:"wmode",wrap:"wrap",about:"about",accentheight:"accentHeight","accent-height":"accentHeight",accumulate:"accumulate",additive:"additive",alignmentbaseline:"alignmentBaseline","alignment-baseline":"alignmentBaseline",allowreorder:"allowReorder",alphabetic:"alphabetic",amplitude:"amplitude",arabicform:"arabicForm","arabic-form":"arabicForm",ascent:"ascent",attributename:"attributeName",attributetype:"attributeType",autoreverse:"autoReverse",azimuth:"azimuth",basefrequency:"baseFrequency",baselineshift:"baselineShift","baseline-shift":"baselineShift",baseprofile:"baseProfile",bbox:"bbox",begin:"begin",bias:"bias",by:"by",calcmode:"calcMode",capheight:"capHeight","cap-height":"capHeight",clip:"clip",clippath:"clipPath","clip-path":"clipPath",clippathunits:"clipPathUnits",cliprule:"clipRule","clip-rule":"clipRule",color:"color",colorinterpolation:"colorInterpolation","color-interpolation":"colorInterpolation",colorinterpolationfilters:"colorInterpolationFilters","color-interpolation-filters":"colorInterpolationFilters",colorprofile:"colorProfile","color-profile":"colorProfile",colorrendering:"colorRendering","color-rendering":"colorRendering",contentscripttype:"contentScriptType",contentstyletype:"contentStyleType",cursor:"cursor",cx:"cx",cy:"cy",d:"d",datatype:"datatype",decelerate:"decelerate",descent:"descent",diffuseconstant:"diffuseConstant",direction:"direction",display:"display",divisor:"divisor",dominantbaseline:"dominantBaseline","dominant-baseline":"dominantBaseline",dur:"dur",dx:"dx",dy:"dy",edgemode:"edgeMode",elevation:"elevation",enablebackground:"enableBackground","enable-background":"enableBackground",end:"end",exponent:"exponent",externalresourcesrequired:"externalResourcesRequired",fill:"fill",fillopacity:"fillOpacity","fill-opacity":"fillOpacity",fillrule:"fillRule","fill-rule":"fillRule",filter:"filter",filterres:"filterRes",filterunits:"filterUnits",floodopacity:"floodOpacity","flood-opacity":"floodOpacity",floodcolor:"floodColor","flood-color":"floodColor",focusable:"focusable",fontfamily:"fontFamily","font-family":"fontFamily",fontsize:"fontSize","font-size":"fontSize",fontsizeadjust:"fontSizeAdjust","font-size-adjust":"fontSizeAdjust",fontstretch:"fontStretch","font-stretch":"fontStretch",fontstyle:"fontStyle","font-style":"fontStyle",fontvariant:"fontVariant","font-variant":"fontVariant",fontweight:"fontWeight","font-weight":"fontWeight",format:"format",from:"from",fx:"fx",fy:"fy",g1:"g1",g2:"g2",glyphname:"glyphName","glyph-name":"glyphName",glyphorientationhorizontal:"glyphOrientationHorizontal","glyph-orientation-horizontal":"glyphOrientationHorizontal",glyphorientationvertical:"glyphOrientationVertical","glyph-orientation-vertical":"glyphOrientationVertical",glyphref:"glyphRef",gradienttransform:"gradientTransform",gradientunits:"gradientUnits",hanging:"hanging",horizadvx:"horizAdvX","horiz-adv-x":"horizAdvX",horizoriginx:"horizOriginX","horiz-origin-x":"horizOriginX",ideographic:"ideographic",imagerendering:"imageRendering","image-rendering":"imageRendering",in2:"in2",in:"in",inlist:"inlist",intercept:"intercept",k1:"k1",k2:"k2",k3:"k3",k4:"k4",k:"k",kernelmatrix:"kernelMatrix",kernelunitlength:"kernelUnitLength",kerning:"kerning",keypoints:"keyPoints",keysplines:"keySplines",keytimes:"keyTimes",lengthadjust:"lengthAdjust",letterspacing:"letterSpacing","letter-spacing":"letterSpacing",lightingcolor:"lightingColor","lighting-color":"lightingColor",limitingconeangle:"limitingConeAngle",local:"local",markerend:"markerEnd","marker-end":"markerEnd",markerheight:"markerHeight",markermid:"markerMid","marker-mid":"markerMid",markerstart:"markerStart","marker-start":"markerStart",markerunits:"markerUnits",markerwidth:"markerWidth",mask:"mask",maskcontentunits:"maskContentUnits",maskunits:"maskUnits",mathematical:"mathematical",mode:"mode",numoctaves:"numOctaves",offset:"offset",opacity:"opacity",operator:"operator",order:"order",orient:"orient",orientation:"orientation",origin:"origin",overflow:"overflow",overlineposition:"overlinePosition","overline-position":"overlinePosition",overlinethickness:"overlineThickness","overline-thickness":"overlineThickness",paintorder:"paintOrder","paint-order":"paintOrder",panose1:"panose1","panose-1":"panose1",pathlength:"pathLength",patterncontentunits:"patternContentUnits",patterntransform:"patternTransform",patternunits:"patternUnits",pointerevents:"pointerEvents","pointer-events":"pointerEvents",points:"points",pointsatx:"pointsAtX",pointsaty:"pointsAtY",pointsatz:"pointsAtZ",prefix:"prefix",preservealpha:"preserveAlpha",preserveaspectratio:"preserveAspectRatio",primitiveunits:"primitiveUnits",property:"property",r:"r",radius:"radius",refx:"refX",refy:"refY",renderingintent:"renderingIntent","rendering-intent":"renderingIntent",repeatcount:"repeatCount",repeatdur:"repeatDur",requiredextensions:"requiredExtensions",requiredfeatures:"requiredFeatures",resource:"resource",restart:"restart",result:"result",results:"results",rotate:"rotate",rx:"rx",ry:"ry",scale:"scale",security:"security",seed:"seed",shaperendering:"shapeRendering","shape-rendering":"shapeRendering",slope:"slope",spacing:"spacing",specularconstant:"specularConstant",specularexponent:"specularExponent",speed:"speed",spreadmethod:"spreadMethod",startoffset:"startOffset",stddeviation:"stdDeviation",stemh:"stemh",stemv:"stemv",stitchtiles:"stitchTiles",stopcolor:"stopColor","stop-color":"stopColor",stopopacity:"stopOpacity","stop-opacity":"stopOpacity",strikethroughposition:"strikethroughPosition","strikethrough-position":"strikethroughPosition",strikethroughthickness:"strikethroughThickness","strikethrough-thickness":"strikethroughThickness",string:"string",stroke:"stroke",strokedasharray:"strokeDasharray","stroke-dasharray":"strokeDasharray",strokedashoffset:"strokeDashoffset","stroke-dashoffset":"strokeDashoffset",strokelinecap:"strokeLinecap","stroke-linecap":"strokeLinecap",strokelinejoin:"strokeLinejoin","stroke-linejoin":"strokeLinejoin",strokemiterlimit:"strokeMiterlimit","stroke-miterlimit":"strokeMiterlimit",strokewidth:"strokeWidth","stroke-width":"strokeWidth",strokeopacity:"strokeOpacity","stroke-opacity":"strokeOpacity",suppresscontenteditablewarning:"suppressContentEditableWarning",suppresshydrationwarning:"suppressHydrationWarning",surfacescale:"surfaceScale",systemlanguage:"systemLanguage",tablevalues:"tableValues",targetx:"targetX",targety:"targetY",textanchor:"textAnchor","text-anchor":"textAnchor",textdecoration:"textDecoration","text-decoration":"textDecoration",textlength:"textLength",textrendering:"textRendering","text-rendering":"textRendering",to:"to",transform:"transform",typeof:"typeof",u1:"u1",u2:"u2",underlineposition:"underlinePosition","underline-position":"underlinePosition",underlinethickness:"underlineThickness","underline-thickness":"underlineThickness",unicode:"unicode",unicodebidi:"unicodeBidi","unicode-bidi":"unicodeBidi",unicoderange:"unicodeRange","unicode-range":"unicodeRange",unitsperem:"unitsPerEm","units-per-em":"unitsPerEm",unselectable:"unselectable",valphabetic:"vAlphabetic","v-alphabetic":"vAlphabetic",values:"values",vectoreffect:"vectorEffect","vector-effect":"vectorEffect",version:"version",vertadvy:"vertAdvY","vert-adv-y":"vertAdvY",vertoriginx:"vertOriginX","vert-origin-x":"vertOriginX",vertoriginy:"vertOriginY","vert-origin-y":"vertOriginY",vhanging:"vHanging","v-hanging":"vHanging",videographic:"vIdeographic","v-ideographic":"vIdeographic",viewbox:"viewBox",viewtarget:"viewTarget",visibility:"visibility",vmathematical:"vMathematical","v-mathematical":"vMathematical",vocab:"vocab",widths:"widths",wordspacing:"wordSpacing","word-spacing":"wordSpacing",writingmode:"writingMode","writing-mode":"writingMode",x1:"x1",x2:"x2",x:"x",xchannelselector:"xChannelSelector",xheight:"xHeight","x-height":"xHeight",xlinkactuate:"xlinkActuate","xlink:actuate":"xlinkActuate",xlinkarcrole:"xlinkArcrole","xlink:arcrole":"xlinkArcrole",xlinkhref:"xlinkHref","xlink:href":"xlinkHref",xlinkrole:"xlinkRole","xlink:role":"xlinkRole",xlinkshow:"xlinkShow","xlink:show":"xlinkShow",xlinktitle:"xlinkTitle","xlink:title":"xlinkTitle",xlinktype:"xlinkType","xlink:type":"xlinkType",xmlbase:"xmlBase","xml:base":"xmlBase",xmllang:"xmlLang","xml:lang":"xmlLang",xmlns:"xmlns","xml:space":"xmlSpace",xmlnsxlink:"xmlnsXlink","xmlns:xlink":"xmlnsXlink",xmlspace:"xmlSpace",y1:"y1",y2:"y2",y:"y",ychannelselector:"yChannelSelector",z:"z",zoomandpan:"zoomAndPan"},Tm={"aria-current":0,"aria-description":0,"aria-details":0,"aria-disabled":0,"aria-hidden":0,"aria-invalid":0,"aria-keyshortcuts":0,"aria-label":0,"aria-roledescription":0,"aria-autocomplete":0,"aria-checked":0,"aria-expanded":0,"aria-haspopup":0,"aria-level":0,"aria-modal":0,"aria-multiline":0,"aria-multiselectable":0,"aria-orientation":0,"aria-placeholder":0,"aria-pressed":0,"aria-readonly":0,"aria-required":0,"aria-selected":0,"aria-sort":0,"aria-valuemax":0,"aria-valuemin":0,"aria-valuenow":0,"aria-valuetext":0,"aria-atomic":0,"aria-busy":0,"aria-live":0,"aria-relevant":0,"aria-dropeffect":0,"aria-grabbed":0,"aria-activedescendant":0,"aria-colcount":0,"aria-colindex":0,"aria-colspan":0,"aria-controls":0,"aria-describedby":0,"aria-errormessage":0,"aria-flowto":0,"aria-labelledby":0,"aria-owns":0,"aria-posinset":0,"aria-rowcount":0,"aria-rowindex":0,"aria-rowspan":0,"aria-setsize":0},Ms={},Os=new RegExp("^(aria)-["+we+"]*$"),Mp=new RegExp("^(aria)[A-Z]["+we+"]*$");function Vu(e,t){{if(Pn.call(Ms,t)&&Ms[t])return!0;if(Mp.test(t)){var a="aria-"+t.slice(4).toLowerCase(),l=Tm.hasOwnProperty(a)?a:null;if(l==null)return v("Invalid ARIA attribute `%s`. ARIA attributes follow the pattern aria-* and must be lowercase.",t),Ms[t]=!0,!0;if(t!==l)return v("Invalid ARIA attribute `%s`. Did you mean `%s`?",t,l),Ms[t]=!0,!0}if(Os.test(t)){var c=t.toLowerCase(),p=Tm.hasOwnProperty(c)?c:null;if(p==null)return Ms[t]=!0,!1;if(t!==p)return v("Unknown ARIA attribute `%s`. Did you mean `%s`?",t,p),Ms[t]=!0,!0}}return!0}function Op(e,t){{var a=[];for(var l in t){var c=Vu(e,l);c||a.push(l)}var p=a.map(function(m){return"`"+m+"`"}).join(", ");a.length===1?v("Invalid aria prop %s on <%s> tag. For details, see https://reactjs.org/link/invalid-aria-props",p,e):a.length>1&&v("Invalid aria props %s on <%s> tag. For details, see https://reactjs.org/link/invalid-aria-props",p,e)}}function km(e,t){Io(e,t)||Op(e,t)}var Wu=!1;function $s(e,t){{if(e!=="input"&&e!=="textarea"&&e!=="select")return;t!=null&&t.value===null&&!Wu&&(Wu=!0,e==="select"&&t.multiple?v("`value` prop on `%s` should not be null. Consider using an empty array when `multiple` is set to `true` to clear the component or `undefined` for uncontrolled components.",e):v("`value` prop on `%s` should not be null. Consider using an empty string to clear the component or `undefined` for uncontrolled components.",e))}}var Cd=function(){};{var Nr={},Yu=/^on./,Rm=/^on[^A-Z]/,Dm=new RegExp("^(aria)-["+we+"]*$"),Mm=new RegExp("^(aria)[A-Z]["+we+"]*$");Cd=function(e,t,a,l){if(Pn.call(Nr,t)&&Nr[t])return!0;var c=t.toLowerCase();if(c==="onfocusin"||c==="onfocusout")return v("React uses onFocus and onBlur instead of onFocusIn and onFocusOut. All React events are normalized to bubble, so onFocusIn and onFocusOut are not needed/supported by React."),Nr[t]=!0,!0;if(l!=null){var p=l.registrationNameDependencies,m=l.possibleRegistrationNames;if(p.hasOwnProperty(t))return!0;var w=m.hasOwnProperty(c)?m[c]:null;if(w!=null)return v("Invalid event handler property `%s`. Did you mean `%s`?",t,w),Nr[t]=!0,!0;if(Yu.test(t))return v("Unknown event handler property `%s`. It will be ignored.",t),Nr[t]=!0,!0}else if(Yu.test(t))return Rm.test(t)&&v("Invalid event handler property `%s`. React events use the camelCase naming convention, for example `onClick`.",t),Nr[t]=!0,!0;if(Dm.test(t)||Mm.test(t))return!0;if(c==="innerhtml")return v("Directly setting property `innerHTML` is not permitted. For more information, lookup documentation on `dangerouslySetInnerHTML`."),Nr[t]=!0,!0;if(c==="aria")return v("The `aria` attribute is reserved for future use in React. Pass individual `aria-` attributes instead."),Nr[t]=!0,!0;if(c==="is"&&a!==null&&a!==void 0&&typeof a!="string")return v("Received a `%s` for a string attribute `is`. If this is expected, cast the value to a string.",typeof a),Nr[t]=!0,!0;if(typeof a=="number"&&isNaN(a))return v("Received NaN for the `%s` attribute. If this is expected, cast the value to a string.",t),Nr[t]=!0,!0;var C=vn(t),R=C!==null&&C.type===ur;if(Ds.hasOwnProperty(c)){var M=Ds[c];if(M!==t)return v("Invalid DOM property `%s`. Did you mean `%s`?",t,M),Nr[t]=!0,!0}else if(!R&&t!==c)return v("React does not recognize the `%s` prop on a DOM element. If you intentionally want it to appear in the DOM as a custom attribute, spell it as lowercase `%s` instead. If you accidentally passed it from a parent component, remove it from the DOM element.",t,c),Nr[t]=!0,!0;return typeof a=="boolean"&&Sn(t,a,C,!1)?(a?v('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.',a,t,t,a,t):v('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.',a,t,t,a,t,t,t),Nr[t]=!0,!0):R?!0:Sn(t,a,C,!1)?(Nr[t]=!0,!1):((a==="false"||a==="true")&&C!==null&&C.type===Gn&&(v("Received the string `%s` for the boolean attribute `%s`. %s Did you mean %s={%s}?",a,t,a==="false"?"The browser will interpret it as a truthy value.":'Although this works, it will not work as expected if you pass the string "false".',t,a),Nr[t]=!0),!0)}}var Om=function(e,t,a){{var l=[];for(var c in t){var p=Cd(e,c,t[c],a);p||l.push(c)}var m=l.map(function(w){return"`"+w+"`"}).join(", ");l.length===1?v("Invalid value for prop %s on <%s> tag. Either remove it from the element, or pass a string or number value to keep it in the DOM. For details, see https://reactjs.org/link/attribute-behavior ",m,e):l.length>1&&v("Invalid values for props %s on <%s> tag. Either remove them from the element, or pass a string or number value to keep them in the DOM. For details, see https://reactjs.org/link/attribute-behavior ",m,e)}};function $m(e,t,a){Io(e,t)||Om(e,t,a)}var $p=1,Fa=2,Ml=4,Ap=$p|Fa|Ml,Gu=null;function k0(e){Gu!==null&&v("Expected currently replaying event to be null. This error is likely caused by a bug in React. Please file an issue."),Gu=e}function Ku(){Gu===null&&v("Expected currently replaying event to not be null. This error is likely caused by a bug in React. Please file an issue."),Gu=null}function R0(e){return e===Gu}function Ed(e){var t=e.target||e.srcElement||window;return t.correspondingUseElement&&(t=t.correspondingUseElement),t.nodeType===ho?t.parentNode:t}var Td=null,qt=null,Uo=null;function Qu(e){var t=iu(e);if(t){if(typeof Td!="function")throw new Error("setRestoreImplementation() needs to be called to handle a target for controlled events. This error is likely caused by a bug in React. Please file an issue.");var a=t.stateNode;if(a){var l=zv(a);Td(t.stateNode,t.type,l)}}}function qu(e){Td=e}function jp(e){qt?Uo?Uo.push(e):Uo=[e]:qt=e}function _p(){return qt!==null||Uo!==null}function As(){if(qt){var e=qt,t=Uo;if(qt=null,Uo=null,Qu(e),t)for(var a=0;a<t.length;a++)Qu(t[a])}}var Xu=function(e,t){return e(t)},Ol=function(){},kd=!1;function D0(){var e=_p();e&&(Ol(),As())}function Am(e,t,a){if(kd)return e(t,a);kd=!0;try{return Xu(e,t,a)}finally{kd=!1,D0()}}function jm(e,t,a){Xu=e,Ol=a}function Rd(e){return e==="button"||e==="input"||e==="select"||e==="textarea"}function Dd(e,t,a){switch(e){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":return!!(a.disabled&&Rd(t));default:return!1}}function $l(e,t){var a=e.stateNode;if(a===null)return null;var l=zv(a);if(l===null)return null;var c=l[t];if(Dd(t,e.type,l))return null;if(c&&typeof c!="function")throw new Error("Expected `"+t+"` listener to be a function, instead got a value of `"+typeof c+"` type.");return c}var Zu=!1;if(an)try{var Al={};Object.defineProperty(Al,"passive",{get:function(){Zu=!0}}),window.addEventListener("test",Al,Al),window.removeEventListener("test",Al,Al)}catch{Zu=!1}function Md(e,t,a,l,c,p,m,w,C){var R=Array.prototype.slice.call(arguments,3);try{t.apply(a,R)}catch(M){this.onError(M)}}var _m=Md;if(typeof window<"u"&&typeof window.dispatchEvent=="function"&&typeof document<"u"&&typeof document.createEvent=="function"){var Od=document.createElement("react");_m=function(t,a,l,c,p,m,w,C,R){if(typeof document>"u"||document===null)throw new Error("The `document` global was defined when React was initialized, but is not defined anymore. This can happen in a test environment if a component schedules an update from an asynchronous callback, but the test has already finished running. To solve this, you can either unmount the component at the end of your test (and ensure that any asynchronous operations get canceled in `componentWillUnmount`), or you can change the test itself to be asynchronous.");var M=document.createEvent("Event"),U=!1,F=!0,q=window.event,Z=Object.getOwnPropertyDescriptor(window,"event");function te(){Od.removeEventListener(ne,Xe,!1),typeof window.event<"u"&&window.hasOwnProperty("event")&&(window.event=q)}var ke=Array.prototype.slice.call(arguments,3);function Xe(){U=!0,te(),a.apply(l,ke),F=!1}var We,It=!1,_t=!1;function Y(G){if(We=G.error,It=!0,We===null&&G.colno===0&&G.lineno===0&&(_t=!0),G.defaultPrevented&&We!=null&&typeof We=="object")try{We._suppressLogging=!0}catch{}}var ne="react-"+(t||"invokeguardedcallback");if(window.addEventListener("error",Y),Od.addEventListener(ne,Xe,!1),M.initEvent(ne,!1,!1),Od.dispatchEvent(M),Z&&Object.defineProperty(window,"event",Z),U&&F&&(It?_t&&(We=new Error("A cross-origin error was thrown. React doesn't have access to the actual error object in development. See https://reactjs.org/link/crossorigin-error for more information.")):We=new Error(`An error was thrown inside one of your components, but React doesn't know what it was. This is likely due to browser flakiness. React does its best to preserve the "Pause on exceptions" behavior of the DevTools, which requires some DEV-mode only tricks. It's possible that these don't work in your browser. Try triggering the error in production mode, or switching to a modern browser. If you suspect that this is actually an issue with React, please file an issue.`),this.onError(We)),window.removeEventListener("error",Y),!U)return te(),Md.apply(this,arguments)}}var M0=_m,js=!1,_s=null,ga=!1,$d=null,Ls={onError:function(e){js=!0,_s=e}};function Xi(e,t,a,l,c,p,m,w,C){js=!1,_s=null,M0.apply(Ls,arguments)}function Ju(e,t,a,l,c,p,m,w,C){if(Xi.apply(this,arguments),js){var R=zp();ga||(ga=!0,$d=R)}}function mo(){if(ga){var e=$d;throw ga=!1,$d=null,e}}function Lp(){return js}function zp(){if(js){var e=_s;return js=!1,_s=null,e}else throw new Error("clearCaughtError was called but no error was captured. This error is likely caused by a bug in React. Please file an issue.")}function zs(e){return e._reactInternals}function jl(e){return e._reactInternals!==void 0}function ec(e,t){e._reactInternals=t}var Ye=0,vo=1,_n=2,At=4,di=16,tn=32,gn=64,Et=128,Rn=256,Qn=512,Zi=1024,Oi=2048,Ln=4096,Ia=8192,Ad=16384,Lm=32767,_l=32768,Pr=65536,ma=131072,tc=1048576,nc=2097152,Bo=4194304,Np=8388608,Gr=16777216,Ho=33554432,Vo=At|Zi|0,Ns=_n|At|di|tn|Qn|Ln|Ia,Wo=At|gn|Qn|Ia,wr=Oi|di,qn=Bo|Np|nc,Ll=d.ReactCurrentOwner;function Kr(e){var t=e,a=e;if(e.alternate)for(;t.return;)t=t.return;else{var l=t;do t=l,(t.flags&(_n|Ln))!==Ye&&(a=t.return),l=t.return;while(l)}return t.tag===_?a:null}function Ua(e){if(e.tag===se){var t=e.memoizedState;if(t===null){var a=e.alternate;a!==null&&(t=a.memoizedState)}if(t!==null)return t.dehydrated}return null}function Yo(e){return e.tag===_?e.stateNode.containerInfo:null}function zm(e){return Kr(e)===e}function Pp(e){{var t=Ll.current;if(t!==null&&t.tag===O){var a=t,l=a.stateNode;l._warnedAboutRefsInRender||v("%s is accessing isMounted inside its render() function. render() should be a pure function of props and state. It should never access something that requires stale data from the previous render, such as refs. Move this logic to componentDidMount and componentDidUpdate instead.",lt(a)||"A component"),l._warnedAboutRefsInRender=!0}}var c=zs(e);return c?Kr(c)===c:!1}function jd(e){if(Kr(e)!==e)throw new Error("Unable to find node on an unmounted component.")}function fi(e){var t=e.alternate;if(!t){var a=Kr(e);if(a===null)throw new Error("Unable to find node on an unmounted component.");return a!==e?null:e}for(var l=e,c=t;;){var p=l.return;if(p===null)break;var m=p.alternate;if(m===null){var w=p.return;if(w!==null){l=c=w;continue}break}if(p.child===m.child){for(var C=p.child;C;){if(C===l)return jd(p),e;if(C===c)return jd(p),t;C=C.sibling}throw new Error("Unable to find node on an unmounted component.")}if(l.return!==c.return)l=p,c=m;else{for(var R=!1,M=p.child;M;){if(M===l){R=!0,l=p,c=m;break}if(M===c){R=!0,c=p,l=m;break}M=M.sibling}if(!R){for(M=m.child;M;){if(M===l){R=!0,l=m,c=p;break}if(M===c){R=!0,c=m,l=p;break}M=M.sibling}if(!R)throw new Error("Child was not found in either parent set. This indicates a bug in React related to the return pointer. Please file an issue.")}}if(l.alternate!==c)throw new Error("Return fibers should always be each others' alternates. This error is likely caused by a bug in React. Please file an issue.")}if(l.tag!==_)throw new Error("Unable to find node on an unmounted component.");return l.stateNode.current===l?e:t}function pi(e){var t=fi(e);return t!==null?En(t):null}function En(e){if(e.tag===N||e.tag===J)return e;for(var t=e.child;t!==null;){var a=En(t);if(a!==null)return a;t=t.sibling}return null}function va(e){var t=fi(e);return t!==null?Fp(t):null}function Fp(e){if(e.tag===N||e.tag===J)return e;for(var t=e.child;t!==null;){if(t.tag!==V){var a=Fp(t);if(a!==null)return a}t=t.sibling}return null}var Ip=s.unstable_scheduleCallback,Up=s.unstable_cancelCallback,Bp=s.unstable_shouldYield,Nm=s.unstable_requestPaint,Un=s.unstable_now,Pm=s.unstable_getCurrentPriorityLevel,yo=s.unstable_ImmediatePriority,rc=s.unstable_UserBlockingPriority,zl=s.unstable_NormalPriority,ic=s.unstable_LowPriority,Ps=s.unstable_IdlePriority,Fm=s.unstable_yieldValue,Im=s.unstable_setDisableYieldValue,ya=null,Sr=null,Ce=null,$i=!1,Fr=typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u";function Hp(e){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u")return!1;var t=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(t.isDisabled)return!0;if(!t.supportsFiber)return v("The installed version of React DevTools is too old and will not work with the current version of React. Please update React DevTools. https://reactjs.org/link/react-devtools"),!0;try{et&&(e=gt({},e,{getLaneLabelMap:Gp,injectProfilingHooks:Nl})),ya=t.inject(e),Sr=t}catch(a){v("React instrumentation encountered an error: %s.",a)}return!!t.checkDCE}function Vp(e,t){if(Sr&&typeof Sr.onScheduleFiberRoot=="function")try{Sr.onScheduleFiberRoot(ya,e,t)}catch(a){$i||($i=!0,v("React instrumentation encountered an error: %s",a))}}function Wp(e,t){if(Sr&&typeof Sr.onCommitFiberRoot=="function")try{var a=(e.current.flags&Et)===Et;if(it){var l;switch(t){case yi:l=yo;break;case ea:l=rc;break;case Cr:l=zl;break;case cf:l=Ps;break;default:l=zl;break}Sr.onCommitFiberRoot(ya,e,l,a)}}catch(c){$i||($i=!0,v("React instrumentation encountered an error: %s",c))}}function Yp(e){if(Sr&&typeof Sr.onPostCommitFiberRoot=="function")try{Sr.onPostCommitFiberRoot(ya,e)}catch(t){$i||($i=!0,v("React instrumentation encountered an error: %s",t))}}function Fs(e){if(Sr&&typeof Sr.onCommitFiberUnmount=="function")try{Sr.onCommitFiberUnmount(ya,e)}catch(t){$i||($i=!0,v("React instrumentation encountered an error: %s",t))}}function nn(e){if(typeof Fm=="function"&&(Im(e),b(e)),Sr&&typeof Sr.setStrictMode=="function")try{Sr.setStrictMode(ya,e)}catch(t){$i||($i=!0,v("React instrumentation encountered an error: %s",t))}}function Nl(e){Ce=e}function Gp(){{for(var e=new Map,t=1,a=0;a<Jp;a++){var l=Ym(t);e.set(t,l),t*=2}return e}}function Um(e){Ce!==null&&typeof Ce.markCommitStarted=="function"&&Ce.markCommitStarted(e)}function Ba(){Ce!==null&&typeof Ce.markCommitStopped=="function"&&Ce.markCommitStopped()}function Ji(e){Ce!==null&&typeof Ce.markComponentRenderStarted=="function"&&Ce.markComponentRenderStarted(e)}function Go(){Ce!==null&&typeof Ce.markComponentRenderStopped=="function"&&Ce.markComponentRenderStopped()}function Bm(e){Ce!==null&&typeof Ce.markComponentPassiveEffectMountStarted=="function"&&Ce.markComponentPassiveEffectMountStarted(e)}function xo(){Ce!==null&&typeof Ce.markComponentPassiveEffectMountStopped=="function"&&Ce.markComponentPassiveEffectMountStopped()}function Ko(e){Ce!==null&&typeof Ce.markComponentPassiveEffectUnmountStarted=="function"&&Ce.markComponentPassiveEffectUnmountStarted(e)}function _d(){Ce!==null&&typeof Ce.markComponentPassiveEffectUnmountStopped=="function"&&Ce.markComponentPassiveEffectUnmountStopped()}function Hm(e){Ce!==null&&typeof Ce.markComponentLayoutEffectMountStarted=="function"&&Ce.markComponentLayoutEffectMountStarted(e)}function Ld(){Ce!==null&&typeof Ce.markComponentLayoutEffectMountStopped=="function"&&Ce.markComponentLayoutEffectMountStopped()}function Kp(e){Ce!==null&&typeof Ce.markComponentLayoutEffectUnmountStarted=="function"&&Ce.markComponentLayoutEffectUnmountStarted(e)}function Is(){Ce!==null&&typeof Ce.markComponentLayoutEffectUnmountStopped=="function"&&Ce.markComponentLayoutEffectUnmountStopped()}function Ha(e,t,a){Ce!==null&&typeof Ce.markComponentErrored=="function"&&Ce.markComponentErrored(e,t,a)}function ac(e,t,a){Ce!==null&&typeof Ce.markComponentSuspended=="function"&&Ce.markComponentSuspended(e,t,a)}function oc(e){Ce!==null&&typeof Ce.markLayoutEffectsStarted=="function"&&Ce.markLayoutEffectsStarted(e)}function Pl(){Ce!==null&&typeof Ce.markLayoutEffectsStopped=="function"&&Ce.markLayoutEffectsStopped()}function Qp(e){Ce!==null&&typeof Ce.markPassiveEffectsStarted=="function"&&Ce.markPassiveEffectsStarted(e)}function Us(){Ce!==null&&typeof Ce.markPassiveEffectsStopped=="function"&&Ce.markPassiveEffectsStopped()}function qp(e){Ce!==null&&typeof Ce.markRenderStarted=="function"&&Ce.markRenderStarted(e)}function Xp(){Ce!==null&&typeof Ce.markRenderYielded=="function"&&Ce.markRenderYielded()}function Dn(){Ce!==null&&typeof Ce.markRenderStopped=="function"&&Ce.markRenderStopped()}function zd(e){Ce!==null&&typeof Ce.markRenderScheduled=="function"&&Ce.markRenderScheduled(e)}function Zp(e,t){Ce!==null&&typeof Ce.markForceUpdateScheduled=="function"&&Ce.markForceUpdateScheduled(e,t)}function lc(e,t){Ce!==null&&typeof Ce.markStateUpdateScheduled=="function"&&Ce.markStateUpdateScheduled(e,t)}var Ge=0,Dt=1,zt=2,mt=8,cn=16,nr=Math.clz32?Math.clz32:uc,Nd=Math.log,sc=Math.LN2;function uc(e){var t=e>>>0;return t===0?32:31-(Nd(t)/sc|0)|0}var Jp=31,ie=0,Xn=0,nt=1,Qo=2,fr=4,pr=8,hi=16,Fl=32,qo=4194240,Bs=64,Pd=128,Fd=256,Id=512,Ud=1024,Bd=2048,Hd=4096,Vd=8192,Il=16384,Wd=32768,Hs=65536,Vs=131072,Yd=262144,cc=524288,Gd=1048576,Kd=2097152,dc=130023424,Ul=4194304,fc=8388608,Qd=16777216,qd=33554432,Xd=67108864,Vm=Ul,Ws=134217728,Wm=268435455,pc=268435456,Xo=536870912,gi=1073741824;function Ym(e){{if(e&nt)return"Sync";if(e&Qo)return"InputContinuousHydration";if(e&fr)return"InputContinuous";if(e&pr)return"DefaultHydration";if(e&hi)return"Default";if(e&Fl)return"TransitionHydration";if(e&qo)return"Transition";if(e&dc)return"Retry";if(e&Ws)return"SelectiveHydration";if(e&pc)return"IdleHydration";if(e&Xo)return"Idle";if(e&gi)return"Offscreen"}}var rn=-1,Zd=Bs,Jd=Ul;function hc(e){switch(Bl(e)){case nt:return nt;case Qo:return Qo;case fr:return fr;case pr:return pr;case hi:return hi;case Fl:return Fl;case Bs:case Pd:case Fd:case Id:case Ud:case Bd:case Hd:case Vd:case Il:case Wd:case Hs:case Vs:case Yd:case cc:case Gd:case Kd:return e&qo;case Ul:case fc:case Qd:case qd:case Xd:return e&dc;case Ws:return Ws;case pc:return pc;case Xo:return Xo;case gi:return gi;default:return v("Should have found matching lanes. This is a bug in React."),e}}function mi(e,t){var a=e.pendingLanes;if(a===ie)return ie;var l=ie,c=e.suspendedLanes,p=e.pingedLanes,m=a&Wm;if(m!==ie){var w=m&~c;if(w!==ie)l=hc(w);else{var C=m&p;C!==ie&&(l=hc(C))}}else{var R=a&~c;R!==ie?l=hc(R):p!==ie&&(l=hc(p))}if(l===ie)return ie;if(t!==ie&&t!==l&&(t&c)===ie){var M=Bl(l),U=Bl(t);if(M>=U||M===hi&&(U&qo)!==ie)return t}(l&fr)!==ie&&(l|=a&hi);var F=e.entangledLanes;if(F!==ie)for(var q=e.entanglements,Z=l&F;Z>0;){var te=Bn(Z),ke=1<<te;l|=q[te],Z&=~ke}return l}function eh(e,t){for(var a=e.eventTimes,l=rn;t>0;){var c=Bn(t),p=1<<c,m=a[c];m>l&&(l=m),t&=~p}return l}function ef(e,t){switch(e){case nt:case Qo:case fr:return t+250;case pr:case hi:case Fl:case Bs:case Pd:case Fd:case Id:case Ud:case Bd:case Hd:case Vd:case Il:case Wd:case Hs:case Vs:case Yd:case cc:case Gd:case Kd:return t+5e3;case Ul:case fc:case Qd:case qd:case Xd:return rn;case Ws:case pc:case Xo:case gi:return rn;default:return v("Should have found matching lanes. This is a bug in React."),rn}}function Gm(e,t){for(var a=e.pendingLanes,l=e.suspendedLanes,c=e.pingedLanes,p=e.expirationTimes,m=a;m>0;){var w=Bn(m),C=1<<w,R=p[w];R===rn?((C&l)===ie||(C&c)!==ie)&&(p[w]=ef(C,t)):R<=t&&(e.expiredLanes|=C),m&=~C}}function Km(e){return hc(e.pendingLanes)}function tf(e){var t=e.pendingLanes&~gi;return t!==ie?t:t&gi?gi:ie}function th(e){return(e&nt)!==ie}function Zo(e){return(e&Wm)!==ie}function nf(e){return(e&dc)===e}function nh(e){var t=nt|fr|hi;return(e&t)===ie}function O0(e){return(e&qo)===e}function gc(e,t){var a=Qo|fr|pr|hi;return(t&a)!==ie}function Qm(e,t){return(t&e.expiredLanes)!==ie}function rh(e){return(e&qo)!==ie}function ih(){var e=Zd;return Zd<<=1,(Zd&qo)===ie&&(Zd=Bs),e}function qm(){var e=Jd;return Jd<<=1,(Jd&dc)===ie&&(Jd=Ul),e}function Bl(e){return e&-e}function hr(e){return Bl(e)}function Bn(e){return 31-nr(e)}function rf(e){return Bn(e)}function vi(e,t){return(e&t)!==ie}function Hl(e,t){return(e&t)===t}function xt(e,t){return e|t}function mc(e,t){return e&~t}function af(e,t){return e&t}function $0(e){return e}function ah(e,t){return e!==Xn&&e<t?e:t}function of(e){for(var t=[],a=0;a<Jp;a++)t.push(e);return t}function Ys(e,t,a){e.pendingLanes|=t,t!==Xo&&(e.suspendedLanes=ie,e.pingedLanes=ie);var l=e.eventTimes,c=rf(t);l[c]=a}function oh(e,t){e.suspendedLanes|=t,e.pingedLanes&=~t;for(var a=e.expirationTimes,l=t;l>0;){var c=Bn(l),p=1<<c;a[c]=rn,l&=~p}}function lf(e,t,a){e.pingedLanes|=e.suspendedLanes&t}function Xm(e,t){var a=e.pendingLanes&~t;e.pendingLanes=t,e.suspendedLanes=ie,e.pingedLanes=ie,e.expiredLanes&=t,e.mutableReadLanes&=t,e.entangledLanes&=t;for(var l=e.entanglements,c=e.eventTimes,p=e.expirationTimes,m=a;m>0;){var w=Bn(m),C=1<<w;l[w]=ie,c[w]=rn,p[w]=rn,m&=~C}}function vc(e,t){for(var a=e.entangledLanes|=t,l=e.entanglements,c=a;c;){var p=Bn(c),m=1<<p;m&t|l[p]&t&&(l[p]|=t),c&=~m}}function sf(e,t){var a=Bl(t),l;switch(a){case fr:l=Qo;break;case hi:l=pr;break;case Bs:case Pd:case Fd:case Id:case Ud:case Bd:case Hd:case Vd:case Il:case Wd:case Hs:case Vs:case Yd:case cc:case Gd:case Kd:case Ul:case fc:case Qd:case qd:case Xd:l=Fl;break;case Xo:l=pc;break;default:l=Xn;break}return(l&(e.suspendedLanes|t))!==Xn?Xn:l}function Zm(e,t,a){if(Fr)for(var l=e.pendingUpdatersLaneMap;a>0;){var c=rf(a),p=1<<c,m=l[c];m.add(t),a&=~p}}function lh(e,t){if(Fr)for(var a=e.pendingUpdatersLaneMap,l=e.memoizedUpdaters;t>0;){var c=rf(t),p=1<<c,m=a[c];m.size>0&&(m.forEach(function(w){var C=w.alternate;(C===null||!l.has(C))&&l.add(w)}),m.clear()),t&=~p}}function uf(e,t){return null}var yi=nt,ea=fr,Cr=hi,cf=Xo,Gs=Xn;function Ai(){return Gs}function rr(e){Gs=e}function Jm(e,t){var a=Gs;try{return Gs=e,t()}finally{Gs=a}}function yc(e,t){return e!==0&&e<t?e:t}function Ir(e,t){return e>t?e:t}function sh(e,t){return e!==0&&e<t}function ev(e){var t=Bl(e);return sh(yi,t)?sh(ea,t)?Zo(t)?Cr:cf:ea:yi}function Vl(e){var t=e.current.memoizedState;return t.isDehydrated}var Er;function A0(e){Er=e}function ze(e){Er(e)}var Jo;function uh(e){Jo=e}var ch;function j0(e){ch=e}var Ks;function df(e){Ks=e}var ff;function tv(e){ff=e}var pf=!1,xc=[],Va=null,Wa=null,Mn=null,Qr=new Map,ta=new Map,bo=[],nv=["mousedown","mouseup","touchcancel","touchend","touchstart","auxclick","dblclick","pointercancel","pointerdown","pointerup","dragend","dragstart","drop","compositionend","compositionstart","keydown","keypress","keyup","input","textInput","copy","cut","paste","click","change","contextmenu","reset","submit"];function xa(e){return nv.indexOf(e)>-1}function rv(e,t,a,l,c){return{blockedOn:e,domEventName:t,eventSystemFlags:a,nativeEvent:c,targetContainers:[l]}}function ba(e,t){switch(e){case"focusin":case"focusout":Va=null;break;case"dragenter":case"dragleave":Wa=null;break;case"mouseover":case"mouseout":Mn=null;break;case"pointerover":case"pointerout":{var a=t.pointerId;Qr.delete(a);break}case"gotpointercapture":case"lostpointercapture":{var l=t.pointerId;ta.delete(l);break}}}function bc(e,t,a,l,c,p){if(e===null||e.nativeEvent!==p){var m=rv(t,a,l,c,p);if(t!==null){var w=iu(t);w!==null&&Jo(w)}return m}e.eventSystemFlags|=l;var C=e.targetContainers;return c!==null&&C.indexOf(c)===-1&&C.push(c),e}function iv(e,t,a,l,c){switch(t){case"focusin":{var p=c;return Va=bc(Va,e,t,a,l,p),!0}case"dragenter":{var m=c;return Wa=bc(Wa,e,t,a,l,m),!0}case"mouseover":{var w=c;return Mn=bc(Mn,e,t,a,l,w),!0}case"pointerover":{var C=c,R=C.pointerId;return Qr.set(R,bc(Qr.get(R)||null,e,t,a,l,C)),!0}case"gotpointercapture":{var M=c,U=M.pointerId;return ta.set(U,bc(ta.get(U)||null,e,t,a,l,M)),!0}}return!1}function dh(e){var t=_c(e.target);if(t!==null){var a=Kr(t);if(a!==null){var l=a.tag;if(l===se){var c=Ua(a);if(c!==null){e.blockedOn=c,ff(e.priority,function(){ch(a)});return}}else if(l===_){var p=a.stateNode;if(Vl(p)){e.blockedOn=Yo(a);return}}}}e.blockedOn=null}function av(e){for(var t=Ks(),a={blockedOn:null,target:e,priority:t},l=0;l<bo.length&&sh(t,bo[l].priority);l++);bo.splice(l,0,a),l===0&&dh(a)}function wc(e){if(e.blockedOn!==null)return!1;for(var t=e.targetContainers;t.length>0;){var a=t[0],l=Sc(e.domEventName,e.eventSystemFlags,a,e.nativeEvent);if(l===null){var c=e.nativeEvent,p=new c.constructor(c.type,c);k0(p),c.target.dispatchEvent(p),Ku()}else{var m=iu(l);return m!==null&&Jo(m),e.blockedOn=l,!1}t.shift()}return!0}function ov(e,t,a){wc(e)&&a.delete(t)}function hf(){pf=!1,Va!==null&&wc(Va)&&(Va=null),Wa!==null&&wc(Wa)&&(Wa=null),Mn!==null&&wc(Mn)&&(Mn=null),Qr.forEach(ov),ta.forEach(ov)}function Wl(e,t){e.blockedOn===t&&(e.blockedOn=null,pf||(pf=!0,s.unstable_scheduleCallback(s.unstable_NormalPriority,hf)))}function Ur(e){if(xc.length>0){Wl(xc[0],e);for(var t=1;t<xc.length;t++){var a=xc[t];a.blockedOn===e&&(a.blockedOn=null)}}Va!==null&&Wl(Va,e),Wa!==null&&Wl(Wa,e),Mn!==null&&Wl(Mn,e);var l=function(w){return Wl(w,e)};Qr.forEach(l),ta.forEach(l);for(var c=0;c<bo.length;c++){var p=bo[c];p.blockedOn===e&&(p.blockedOn=null)}for(;bo.length>0;){var m=bo[0];if(m.blockedOn!==null)break;dh(m),m.blockedOn===null&&bo.shift()}}var jt=d.ReactCurrentBatchConfig,Zn=!0;function Hn(e){Zn=!!e}function Tr(){return Zn}function ji(e,t,a){var l=qs(t),c;switch(l){case yi:c=Qs;break;case ea:c=ir;break;case Cr:default:c=Yl;break}return c.bind(null,t,a,e)}function Qs(e,t,a,l){var c=Ai(),p=jt.transition;jt.transition=null;try{rr(yi),Yl(e,t,a,l)}finally{rr(c),jt.transition=p}}function ir(e,t,a,l){var c=Ai(),p=jt.transition;jt.transition=null;try{rr(ea),Yl(e,t,a,l)}finally{rr(c),jt.transition=p}}function Yl(e,t,a,l){Zn&&Gl(e,t,a,l)}function Gl(e,t,a,l){var c=Sc(e,t,a,l);if(c===null){q0(e,t,l,Kl,a),ba(e,l);return}if(iv(c,e,t,a,l)){l.stopPropagation();return}if(ba(e,l),t&Ml&&xa(e)){for(;c!==null;){var p=iu(c);p!==null&&ze(p);var m=Sc(e,t,a,l);if(m===null&&q0(e,t,l,Kl,a),m===c)break;c=m}c!==null&&l.stopPropagation();return}q0(e,t,l,null,a)}var Kl=null;function Sc(e,t,a,l){Kl=null;var c=Ed(l),p=_c(c);if(p!==null){var m=Kr(p);if(m===null)p=null;else{var w=m.tag;if(w===se){var C=Ua(m);if(C!==null)return C;p=null}else if(w===_){var R=m.stateNode;if(Vl(R))return Yo(m);p=null}else m!==p&&(p=null)}}return Kl=p,null}function qs(e){switch(e){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return yi;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return ea;case"message":{var t=Pm();switch(t){case yo:return yi;case rc:return ea;case zl:case ic:return Cr;case Ps:return cf;default:return Cr}}default:return Cr}}function xi(e,t,a){return e.addEventListener(t,a,!1),a}function fh(e,t,a){return e.addEventListener(t,a,!0),a}function Xs(e,t,a,l){return e.addEventListener(t,a,{capture:!0,passive:l}),a}function wo(e,t,a,l){return e.addEventListener(t,a,{passive:l}),a}var el=null,Cc=null,na=null;function gf(e){return el=e,Cc=Zs(),!0}function tl(){el=null,Cc=null,na=null}function Ec(){if(na)return na;var e,t=Cc,a=t.length,l,c=Zs(),p=c.length;for(e=0;e<a&&t[e]===c[e];e++);var m=a-e;for(l=1;l<=m&&t[a-l]===c[p-l];l++);var w=l>1?1-l:void 0;return na=c.slice(e,w),na}function Zs(){return"value"in el?el.value:el.textContent}function Js(e){var t,a=e.keyCode;return"charCode"in e?(t=e.charCode,t===0&&a===13&&(t=13)):t=a,t===10&&(t=13),t>=32||t===13?t:0}function Ql(){return!0}function Tc(){return!1}function mn(e){function t(a,l,c,p,m){this._reactName=a,this._targetInst=c,this.type=l,this.nativeEvent=p,this.target=m,this.currentTarget=null;for(var w in e)if(e.hasOwnProperty(w)){var C=e[w];C?this[w]=C(p):this[w]=p[w]}var R=p.defaultPrevented!=null?p.defaultPrevented:p.returnValue===!1;return R?this.isDefaultPrevented=Ql:this.isDefaultPrevented=Tc,this.isPropagationStopped=Tc,this}return gt(t.prototype,{preventDefault:function(){this.defaultPrevented=!0;var a=this.nativeEvent;a&&(a.preventDefault?a.preventDefault():typeof a.returnValue!="unknown"&&(a.returnValue=!1),this.isDefaultPrevented=Ql)},stopPropagation:function(){var a=this.nativeEvent;a&&(a.stopPropagation?a.stopPropagation():typeof a.cancelBubble!="unknown"&&(a.cancelBubble=!0),this.isPropagationStopped=Ql)},persist:function(){},isPersistent:Ql}),t}var _i={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(e){return e.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Li=mn(_i),gr=gt({},_i,{view:0,detail:0}),lv=mn(gr),kc,Rc,Dc;function nl(e){e!==Dc&&(Dc&&e.type==="mousemove"?(kc=e.screenX-Dc.screenX,Rc=e.screenY-Dc.screenY):(kc=0,Rc=0),Dc=e)}var Mc=gt({},gr,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:mh,button:0,buttons:0,relatedTarget:function(e){return e.relatedTarget===void 0?e.fromElement===e.srcElement?e.toElement:e.fromElement:e.relatedTarget},movementX:function(e){return"movementX"in e?e.movementX:(nl(e),kc)},movementY:function(e){return"movementY"in e?e.movementY:Rc}}),mf=mn(Mc),ql=gt({},Mc,{dataTransfer:0}),ph=mn(ql),Xl=gt({},gr,{relatedTarget:0}),vf=mn(Xl),sv=gt({},_i,{animationName:0,elapsedTime:0,pseudoElement:0}),hh=mn(sv),yf=gt({},_i,{clipboardData:function(e){return"clipboardData"in e?e.clipboardData:window.clipboardData}}),_0=mn(yf),L0=gt({},_i,{data:0}),gh=mn(L0),uv=gh,Zl={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},z0={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"};function eu(e){if(e.key){var t=Zl[e.key]||e.key;if(t!=="Unidentified")return t}if(e.type==="keypress"){var a=Js(e);return a===13?"Enter":String.fromCharCode(a)}return e.type==="keydown"||e.type==="keyup"?z0[e.keyCode]||"Unidentified":""}var cv={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function zn(e){var t=this,a=t.nativeEvent;if(a.getModifierState)return a.getModifierState(e);var l=cv[e];return l?!!a[l]:!1}function mh(e){return zn}var dv=gt({},gr,{key:eu,code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:mh,charCode:function(e){return e.type==="keypress"?Js(e):0},keyCode:function(e){return e.type==="keydown"||e.type==="keyup"?e.keyCode:0},which:function(e){return e.type==="keypress"?Js(e):e.type==="keydown"||e.type==="keyup"?e.keyCode:0}}),N0=mn(dv),P0=gt({},Mc,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),vh=mn(P0),fv=gt({},gr,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:mh}),F0=mn(fv),ra=gt({},_i,{propertyName:0,elapsedTime:0,pseudoElement:0}),yh=mn(ra),I0=gt({},Mc,{deltaX:function(e){return"deltaX"in e?e.deltaX:"wheelDeltaX"in e?-e.wheelDeltaX:0},deltaY:function(e){return"deltaY"in e?e.deltaY:"wheelDeltaY"in e?-e.wheelDeltaY:"wheelDelta"in e?-e.wheelDelta:0},deltaZ:0,deltaMode:0}),rl=mn(I0),xf=[9,13,27,32],il=229,tu=an&&"CompositionEvent"in window,Jl=null;an&&"documentMode"in document&&(Jl=document.documentMode);var xh=an&&"TextEvent"in window&&!Jl,pv=an&&(!tu||Jl&&Jl>8&&Jl<=11),bf=32,hv=String.fromCharCode(bf);function gv(){Ut("onBeforeInput",["compositionend","keypress","textInput","paste"]),Ut("onCompositionEnd",["compositionend","focusout","keydown","keypress","keyup","mousedown"]),Ut("onCompositionStart",["compositionstart","focusout","keydown","keypress","keyup","mousedown"]),Ut("onCompositionUpdate",["compositionupdate","focusout","keydown","keypress","keyup","mousedown"])}var bh=!1;function wf(e){return(e.ctrlKey||e.altKey||e.metaKey)&&!(e.ctrlKey&&e.altKey)}function Sf(e){switch(e){case"compositionstart":return"onCompositionStart";case"compositionend":return"onCompositionEnd";case"compositionupdate":return"onCompositionUpdate"}}function mv(e,t){return e==="keydown"&&t.keyCode===il}function Cf(e,t){switch(e){case"keyup":return xf.indexOf(t.keyCode)!==-1;case"keydown":return t.keyCode!==il;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function vv(e){var t=e.detail;return typeof t=="object"&&"data"in t?t.data:null}function wh(e){return e.locale==="ko"}var al=!1;function Ef(e,t,a,l,c){var p,m;if(tu?p=Sf(t):al?Cf(t,l)&&(p="onCompositionEnd"):mv(t,l)&&(p="onCompositionStart"),!p)return null;pv&&!wh(l)&&(!al&&p==="onCompositionStart"?al=gf(c):p==="onCompositionEnd"&&al&&(m=Ec()));var w=Cv(a,p);if(w.length>0){var C=new gh(p,t,null,l,c);if(e.push({event:C,listeners:w}),m)C.data=m;else{var R=vv(l);R!==null&&(C.data=R)}}}function Sh(e,t){switch(e){case"compositionend":return vv(t);case"keypress":var a=t.which;return a!==bf?null:(bh=!0,hv);case"textInput":var l=t.data;return l===hv&&bh?null:l;default:return null}}function Tf(e,t){if(al){if(e==="compositionend"||!tu&&Cf(e,t)){var a=Ec();return tl(),al=!1,a}return null}switch(e){case"paste":return null;case"keypress":if(!wf(t)){if(t.char&&t.char.length>1)return t.char;if(t.which)return String.fromCharCode(t.which)}return null;case"compositionend":return pv&&!wh(t)?null:t.data;default:return null}}function yv(e,t,a,l,c){var p;if(xh?p=Sh(t,l):p=Tf(t,l),!p)return null;var m=Cv(a,"onBeforeInput");if(m.length>0){var w=new uv("onBeforeInput","beforeinput",null,l,c);e.push({event:w,listeners:m}),w.data=p}}function U0(e,t,a,l,c,p,m){Ef(e,t,a,l,c),yv(e,t,a,l,c)}var kf={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function xv(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t==="input"?!!kf[e.type]:t==="textarea"}/**
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
 */function Oc(e){if(!an)return!1;var t="on"+e,a=t in document;if(!a){var l=document.createElement("div");l.setAttribute(t,"return;"),a=typeof l[t]=="function"}return a}function B0(){Ut("onChange",["change","click","focusin","focusout","input","keydown","keyup","selectionchange"])}function $c(e,t,a,l){jp(l);var c=Cv(t,"onChange");if(c.length>0){var p=new Li("onChange","change",null,a,l);e.push({event:p,listeners:c})}}var n=null,i=null;function u(e){var t=e.nodeName&&e.nodeName.toLowerCase();return t==="select"||t==="input"&&e.type==="file"}function f(e){var t=[];$c(t,i,e,Ed(e)),Am(h,t)}function h(e){DE(e,0)}function x(e){var t=Af(e);if(No(t))return e}function k(e,t){if(e==="change")return t}var A=!1;an&&(A=Oc("input")&&(!document.documentMode||document.documentMode>9));function z(e,t){n=e,i=t,n.attachEvent("onpropertychange",pe)}function X(){n&&(n.detachEvent("onpropertychange",pe),n=null,i=null)}function pe(e){e.propertyName==="value"&&x(i)&&f(e)}function ge(e,t,a){e==="focusin"?(X(),z(t,a)):e==="focusout"&&X()}function fe(e,t){if(e==="selectionchange"||e==="keyup"||e==="keydown")return x(i)}function Ae(e){var t=e.nodeName;return t&&t.toLowerCase()==="input"&&(e.type==="checkbox"||e.type==="radio")}function Fe(e,t){if(e==="click")return x(t)}function Ue(e,t){if(e==="input"||e==="change")return x(t)}function Vn(e){var t=e._wrapperState;!t||!t.controlled||e.type!=="number"||Ne(e,"number",e.value)}function W(e,t,a,l,c,p,m){var w=a?Af(a):window,C,R;if(u(w)?C=k:xv(w)?A?C=Ue:(C=fe,R=ge):Ae(w)&&(C=Fe),C){var M=C(t,a);if(M){$c(e,M,l,c);return}}R&&R(t,w,a),t==="focusout"&&Vn(w)}function I(){pn("onMouseEnter",["mouseout","mouseover"]),pn("onMouseLeave",["mouseout","mouseover"]),pn("onPointerEnter",["pointerout","pointerover"]),pn("onPointerLeave",["pointerout","pointerover"])}function K(e,t,a,l,c,p,m){var w=t==="mouseover"||t==="pointerover",C=t==="mouseout"||t==="pointerout";if(w&&!R0(l)){var R=l.relatedTarget||l.fromElement;if(R&&(_c(R)||zh(R)))return}if(!(!C&&!w)){var M;if(c.window===c)M=c;else{var U=c.ownerDocument;U?M=U.defaultView||U.parentWindow:M=window}var F,q;if(C){var Z=l.relatedTarget||l.toElement;if(F=a,q=Z?_c(Z):null,q!==null){var te=Kr(q);(q!==te||q.tag!==N&&q.tag!==J)&&(q=null)}}else F=null,q=a;if(F!==q){var ke=mf,Xe="onMouseLeave",We="onMouseEnter",It="mouse";(t==="pointerout"||t==="pointerover")&&(ke=vh,Xe="onPointerLeave",We="onPointerEnter",It="pointer");var _t=F==null?M:Af(F),Y=q==null?M:Af(q),ne=new ke(Xe,It+"leave",F,l,c);ne.target=_t,ne.relatedTarget=Y;var G=null,me=_c(c);if(me===a){var _e=new ke(We,It+"enter",q,l,c);_e.target=Y,_e.relatedTarget=_t,G=_e}HL(e,ne,G,F,q)}}}function ve(e,t){return e===t&&(e!==0||1/e===1/t)||e!==e&&t!==t}var De=typeof Object.is=="function"?Object.is:ve;function Ke(e,t){if(De(e,t))return!0;if(typeof e!="object"||e===null||typeof t!="object"||t===null)return!1;var a=Object.keys(e),l=Object.keys(t);if(a.length!==l.length)return!1;for(var c=0;c<a.length;c++){var p=a[c];if(!Pn.call(t,p)||!De(e[p],t[p]))return!1}return!0}function Je(e){for(;e&&e.firstChild;)e=e.firstChild;return e}function st(e){for(;e;){if(e.nextSibling)return e.nextSibling;e=e.parentNode}}function ar(e,t){for(var a=Je(e),l=0,c=0;a;){if(a.nodeType===ho){if(c=l+a.textContent.length,l<=t&&c>=t)return{node:a,offset:t-l};l=c}a=Je(st(a))}}function Ht(e){var t=e.ownerDocument,a=t&&t.defaultView||window,l=a.getSelection&&a.getSelection();if(!l||l.rangeCount===0)return null;var c=l.anchorNode,p=l.anchorOffset,m=l.focusNode,w=l.focusOffset;try{c.nodeType,m.nodeType}catch{return null}return ol(e,c,p,m,w)}function ol(e,t,a,l,c){var p=0,m=-1,w=-1,C=0,R=0,M=e,U=null;e:for(;;){for(var F=null;M===t&&(a===0||M.nodeType===ho)&&(m=p+a),M===l&&(c===0||M.nodeType===ho)&&(w=p+c),M.nodeType===ho&&(p+=M.nodeValue.length),(F=M.firstChild)!==null;)U=M,M=F;for(;;){if(M===e)break e;if(U===t&&++C===a&&(m=p),U===l&&++R===c&&(w=p),(F=M.nextSibling)!==null)break;M=U,U=M.parentNode}M=F}return m===-1||w===-1?null:{start:m,end:w}}function H0(e,t){var a=e.ownerDocument||document,l=a&&a.defaultView||window;if(l.getSelection){var c=l.getSelection(),p=e.textContent.length,m=Math.min(t.start,p),w=t.end===void 0?m:Math.min(t.end,p);if(!c.extend&&m>w){var C=w;w=m,m=C}var R=ar(e,m),M=ar(e,w);if(R&&M){if(c.rangeCount===1&&c.anchorNode===R.node&&c.anchorOffset===R.offset&&c.focusNode===M.node&&c.focusOffset===M.offset)return;var U=a.createRange();U.setStart(R.node,R.offset),c.removeAllRanges(),m>w?(c.addRange(U),c.extend(M.node,M.offset)):(U.setEnd(M.node,M.offset),c.addRange(U))}}}function mE(e){return e&&e.nodeType===ho}function vE(e,t){return!e||!t?!1:e===t?!0:mE(e)?!1:mE(t)?vE(e,t.parentNode):"contains"in e?e.contains(t):e.compareDocumentPosition?!!(e.compareDocumentPosition(t)&16):!1}function TL(e){return e&&e.ownerDocument&&vE(e.ownerDocument.documentElement,e)}function kL(e){try{return typeof e.contentWindow.location.href=="string"}catch{return!1}}function yE(){for(var e=window,t=co();t instanceof e.HTMLIFrameElement;){if(kL(t))e=t.contentWindow;else return t;t=co(e.document)}return t}function V0(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t&&(t==="input"&&(e.type==="text"||e.type==="search"||e.type==="tel"||e.type==="url"||e.type==="password")||t==="textarea"||e.contentEditable==="true")}function RL(){var e=yE();return{focusedElem:e,selectionRange:V0(e)?ML(e):null}}function DL(e){var t=yE(),a=e.focusedElem,l=e.selectionRange;if(t!==a&&TL(a)){l!==null&&V0(a)&&OL(a,l);for(var c=[],p=a;p=p.parentNode;)p.nodeType===ci&&c.push({element:p,left:p.scrollLeft,top:p.scrollTop});typeof a.focus=="function"&&a.focus();for(var m=0;m<c.length;m++){var w=c[m];w.element.scrollLeft=w.left,w.element.scrollTop=w.top}}}function ML(e){var t;return"selectionStart"in e?t={start:e.selectionStart,end:e.selectionEnd}:t=Ht(e),t||{start:0,end:0}}function OL(e,t){var a=t.start,l=t.end;l===void 0&&(l=a),"selectionStart"in e?(e.selectionStart=a,e.selectionEnd=Math.min(l,e.value.length)):H0(e,t)}var $L=an&&"documentMode"in document&&document.documentMode<=11;function AL(){Ut("onSelect",["focusout","contextmenu","dragend","focusin","keydown","keyup","mousedown","mouseup","selectionchange"])}var Rf=null,W0=null,Ch=null,Y0=!1;function jL(e){if("selectionStart"in e&&V0(e))return{start:e.selectionStart,end:e.selectionEnd};var t=e.ownerDocument&&e.ownerDocument.defaultView||window,a=t.getSelection();return{anchorNode:a.anchorNode,anchorOffset:a.anchorOffset,focusNode:a.focusNode,focusOffset:a.focusOffset}}function _L(e){return e.window===e?e.document:e.nodeType===go?e:e.ownerDocument}function xE(e,t,a){var l=_L(a);if(!(Y0||Rf==null||Rf!==co(l))){var c=jL(Rf);if(!Ch||!Ke(Ch,c)){Ch=c;var p=Cv(W0,"onSelect");if(p.length>0){var m=new Li("onSelect","select",null,t,a);e.push({event:m,listeners:p}),m.target=Rf}}}}function LL(e,t,a,l,c,p,m){var w=a?Af(a):window;switch(t){case"focusin":(xv(w)||w.contentEditable==="true")&&(Rf=w,W0=a,Ch=null);break;case"focusout":Rf=null,W0=null,Ch=null;break;case"mousedown":Y0=!0;break;case"contextmenu":case"mouseup":case"dragend":Y0=!1,xE(e,l,c);break;case"selectionchange":if($L)break;case"keydown":case"keyup":xE(e,l,c)}}function bv(e,t){var a={};return a[e.toLowerCase()]=t.toLowerCase(),a["Webkit"+e]="webkit"+t,a["Moz"+e]="moz"+t,a}var Df={animationend:bv("Animation","AnimationEnd"),animationiteration:bv("Animation","AnimationIteration"),animationstart:bv("Animation","AnimationStart"),transitionend:bv("Transition","TransitionEnd")},G0={},bE={};an&&(bE=document.createElement("div").style,"AnimationEvent"in window||(delete Df.animationend.animation,delete Df.animationiteration.animation,delete Df.animationstart.animation),"TransitionEvent"in window||delete Df.transitionend.transition);function wv(e){if(G0[e])return G0[e];if(!Df[e])return e;var t=Df[e];for(var a in t)if(t.hasOwnProperty(a)&&a in bE)return G0[e]=t[a];return e}var wE=wv("animationend"),SE=wv("animationiteration"),CE=wv("animationstart"),EE=wv("transitionend"),TE=new Map,kE=["abort","auxClick","cancel","canPlay","canPlayThrough","click","close","contextMenu","copy","cut","drag","dragEnd","dragEnter","dragExit","dragLeave","dragOver","dragStart","drop","durationChange","emptied","encrypted","ended","error","gotPointerCapture","input","invalid","keyDown","keyPress","keyUp","load","loadedData","loadedMetadata","loadStart","lostPointerCapture","mouseDown","mouseMove","mouseOut","mouseOver","mouseUp","paste","pause","play","playing","pointerCancel","pointerDown","pointerMove","pointerOut","pointerOver","pointerUp","progress","rateChange","reset","resize","seeked","seeking","stalled","submit","suspend","timeUpdate","touchCancel","touchEnd","touchStart","volumeChange","scroll","toggle","touchMove","waiting","wheel"];function nu(e,t){TE.set(e,t),Ut(t,[e])}function zL(){for(var e=0;e<kE.length;e++){var t=kE[e],a=t.toLowerCase(),l=t[0].toUpperCase()+t.slice(1);nu(a,"on"+l)}nu(wE,"onAnimationEnd"),nu(SE,"onAnimationIteration"),nu(CE,"onAnimationStart"),nu("dblclick","onDoubleClick"),nu("focusin","onFocus"),nu("focusout","onBlur"),nu(EE,"onTransitionEnd")}function NL(e,t,a,l,c,p,m){var w=TE.get(t);if(w!==void 0){var C=Li,R=t;switch(t){case"keypress":if(Js(l)===0)return;case"keydown":case"keyup":C=N0;break;case"focusin":R="focus",C=vf;break;case"focusout":R="blur",C=vf;break;case"beforeblur":case"afterblur":C=vf;break;case"click":if(l.button===2)return;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":C=mf;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":C=ph;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":C=F0;break;case wE:case SE:case CE:C=hh;break;case EE:C=yh;break;case"scroll":C=lv;break;case"wheel":C=rl;break;case"copy":case"cut":case"paste":C=_0;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":C=vh;break}var M=(p&Ml)!==0;{var U=!M&&t==="scroll",F=UL(a,w,l.type,M,U);if(F.length>0){var q=new C(w,R,null,l,c);e.push({event:q,listeners:F})}}}}zL(),I(),B0(),AL(),gv();function PL(e,t,a,l,c,p,m){NL(e,t,a,l,c,p);var w=(p&Ap)===0;w&&(K(e,t,a,l,c),W(e,t,a,l,c),LL(e,t,a,l,c),U0(e,t,a,l,c))}var Eh=["abort","canplay","canplaythrough","durationchange","emptied","encrypted","ended","error","loadeddata","loadedmetadata","loadstart","pause","play","playing","progress","ratechange","resize","seeked","seeking","stalled","suspend","timeupdate","volumechange","waiting"],K0=new Set(["cancel","close","invalid","load","scroll","toggle"].concat(Eh));function RE(e,t,a){var l=e.type||"unknown-event";e.currentTarget=a,Ju(l,t,void 0,e),e.currentTarget=null}function FL(e,t,a){var l;if(a)for(var c=t.length-1;c>=0;c--){var p=t[c],m=p.instance,w=p.currentTarget,C=p.listener;if(m!==l&&e.isPropagationStopped())return;RE(e,C,w),l=m}else for(var R=0;R<t.length;R++){var M=t[R],U=M.instance,F=M.currentTarget,q=M.listener;if(U!==l&&e.isPropagationStopped())return;RE(e,q,F),l=U}}function DE(e,t){for(var a=(t&Ml)!==0,l=0;l<e.length;l++){var c=e[l],p=c.event,m=c.listeners;FL(p,m,a)}mo()}function IL(e,t,a,l,c){var p=Ed(a),m=[];PL(m,e,l,a,p,t),DE(m,t)}function An(e,t){K0.has(e)||v('Did not expect a listenToNonDelegatedEvent() call for "%s". This is a bug in React. Please file an issue.',e);var a=!1,l=mN(t),c=VL(e);l.has(c)||(ME(t,e,Fa,a),l.add(c))}function Q0(e,t,a){K0.has(e)&&!t&&v('Did not expect a listenToNativeEvent() call for "%s" in the bubble phase. This is a bug in React. Please file an issue.',e);var l=0;t&&(l|=Ml),ME(a,e,l,t)}var Sv="_reactListening"+Math.random().toString(36).slice(2);function Th(e){if(!e[Sv]){e[Sv]=!0,Ot.forEach(function(a){a!=="selectionchange"&&(K0.has(a)||Q0(a,!1,e),Q0(a,!0,e))});var t=e.nodeType===go?e:e.ownerDocument;t!==null&&(t[Sv]||(t[Sv]=!0,Q0("selectionchange",!1,t)))}}function ME(e,t,a,l,c){var p=ji(e,t,a),m=void 0;Zu&&(t==="touchstart"||t==="touchmove"||t==="wheel")&&(m=!0),e=e,l?m!==void 0?Xs(e,t,p,m):fh(e,t,p):m!==void 0?wo(e,t,p,m):xi(e,t,p)}function OE(e,t){return e===t||e.nodeType===Kn&&e.parentNode===t}function q0(e,t,a,l,c){var p=l;if(!(t&$p)&&!(t&Fa)){var m=c;if(l!==null){var w=l;e:for(;;){if(w===null)return;var C=w.tag;if(C===_||C===V){var R=w.stateNode.containerInfo;if(OE(R,m))break;if(C===V)for(var M=w.return;M!==null;){var U=M.tag;if(U===_||U===V){var F=M.stateNode.containerInfo;if(OE(F,m))return}M=M.return}for(;R!==null;){var q=_c(R);if(q===null)return;var Z=q.tag;if(Z===N||Z===J){w=p=q;continue e}R=R.parentNode}}w=w.return}}}Am(function(){return IL(e,t,a,p)})}function kh(e,t,a){return{instance:e,listener:t,currentTarget:a}}function UL(e,t,a,l,c,p){for(var m=t!==null?t+"Capture":null,w=l?m:t,C=[],R=e,M=null;R!==null;){var U=R,F=U.stateNode,q=U.tag;if(q===N&&F!==null&&(M=F,w!==null)){var Z=$l(R,w);Z!=null&&C.push(kh(R,Z,M))}if(c)break;R=R.return}return C}function Cv(e,t){for(var a=t+"Capture",l=[],c=e;c!==null;){var p=c,m=p.stateNode,w=p.tag;if(w===N&&m!==null){var C=m,R=$l(c,a);R!=null&&l.unshift(kh(c,R,C));var M=$l(c,t);M!=null&&l.push(kh(c,M,C))}c=c.return}return l}function Mf(e){if(e===null)return null;do e=e.return;while(e&&e.tag!==N);return e||null}function BL(e,t){for(var a=e,l=t,c=0,p=a;p;p=Mf(p))c++;for(var m=0,w=l;w;w=Mf(w))m++;for(;c-m>0;)a=Mf(a),c--;for(;m-c>0;)l=Mf(l),m--;for(var C=c;C--;){if(a===l||l!==null&&a===l.alternate)return a;a=Mf(a),l=Mf(l)}return null}function $E(e,t,a,l,c){for(var p=t._reactName,m=[],w=a;w!==null&&w!==l;){var C=w,R=C.alternate,M=C.stateNode,U=C.tag;if(R!==null&&R===l)break;if(U===N&&M!==null){var F=M;if(c){var q=$l(w,p);q!=null&&m.unshift(kh(w,q,F))}else if(!c){var Z=$l(w,p);Z!=null&&m.push(kh(w,Z,F))}}w=w.return}m.length!==0&&e.push({event:t,listeners:m})}function HL(e,t,a,l,c){var p=l&&c?BL(l,c):null;l!==null&&$E(e,t,l,p,!1),c!==null&&a!==null&&$E(e,a,c,p,!0)}function VL(e,t){return e+"__bubble"}var ia=!1,Rh="dangerouslySetInnerHTML",Ev="suppressContentEditableWarning",ru="suppressHydrationWarning",AE="autoFocus",Ac="children",jc="style",Tv="__html",X0,kv,Dh,jE,Rv,_E,LE;X0={dialog:!0,webview:!0},kv=function(e,t){km(e,t),$s(e,t),$m(e,t,{registrationNameDependencies:tt,possibleRegistrationNames:vt})},_E=an&&!document.documentMode,Dh=function(e,t,a){if(!ia){var l=Dv(a),c=Dv(t);c!==l&&(ia=!0,v("Prop `%s` did not match. Server: %s Client: %s",e,JSON.stringify(c),JSON.stringify(l)))}},jE=function(e){if(!ia){ia=!0;var t=[];e.forEach(function(a){t.push(a)}),v("Extra attributes from the server: %s",t)}},Rv=function(e,t){t===!1?v("Expected `%s` listener to be a function, instead got `false`.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.",e,e,e):v("Expected `%s` listener to be a function, instead got a value of `%s` type.",e,typeof t)},LE=function(e,t){var a=e.namespaceURI===pa?e.ownerDocument.createElement(e.tagName):e.ownerDocument.createElementNS(e.namespaceURI,e.tagName);return a.innerHTML=t,a.innerHTML};var WL=/\r\n?/g,YL=/\u0000|\uFFFD/g;function Dv(e){tr(e);var t=typeof e=="string"?e:""+e;return t.replace(WL,`
`).replace(YL,"")}function Mv(e,t,a,l){var c=Dv(t),p=Dv(e);if(p!==c&&(l&&(ia||(ia=!0,v('Text content did not match. Server: "%s" Client: "%s"',p,c))),a&&oe))throw new Error("Text content does not match server-rendered HTML.")}function zE(e){return e.nodeType===go?e:e.ownerDocument}function GL(){}function Ov(e){e.onclick=GL}function KL(e,t,a,l,c){for(var p in l)if(l.hasOwnProperty(p)){var m=l[p];if(p===jc)m&&Object.freeze(m),wm(t,m);else if(p===Rh){var w=m?m[Tv]:void 0;w!=null&&um(t,w)}else if(p===Ac)if(typeof m=="string"){var C=e!=="textarea"||m!=="";C&&Fo(t,m)}else typeof m=="number"&&Fo(t,""+m);else p===Ev||p===ru||p===AE||(tt.hasOwnProperty(p)?m!=null&&(typeof m!="function"&&Rv(p,m),p==="onScroll"&&An("scroll",t)):m!=null&&Di(t,p,m,c))}}function QL(e,t,a,l){for(var c=0;c<t.length;c+=2){var p=t[c],m=t[c+1];p===jc?wm(e,m):p===Rh?um(e,m):p===Ac?Fo(e,m):Di(e,p,m,l)}}function qL(e,t,a,l){var c,p=zE(a),m,w=l;if(w===pa&&(w=Tp(e)),w===pa){if(c=Io(e,t),!c&&e!==e.toLowerCase()&&v("<%s /> is using incorrect casing. Use PascalCase for React components, or lowercase for HTML elements.",e),e==="script"){var C=p.createElement("div");C.innerHTML="<script><\/script>";var R=C.firstChild;m=C.removeChild(R)}else if(typeof t.is=="string")m=p.createElement(e,{is:t.is});else if(m=p.createElement(e),e==="select"){var M=m;t.multiple?M.multiple=!0:t.size&&(M.size=t.size)}}else m=p.createElementNS(w,e);return w===pa&&!c&&Object.prototype.toString.call(m)==="[object HTMLUnknownElement]"&&!Pn.call(X0,e)&&(X0[e]=!0,v("The tag <%s> is unrecognized in this browser. If you meant to render a React component, start its name with an uppercase letter.",e)),m}function XL(e,t){return zE(t).createTextNode(e)}function ZL(e,t,a,l){var c=Io(t,a);kv(t,a);var p;switch(t){case"dialog":An("cancel",e),An("close",e),p=a;break;case"iframe":case"object":case"embed":An("load",e),p=a;break;case"video":case"audio":for(var m=0;m<Eh.length;m++)An(Eh[m],e);p=a;break;case"source":An("error",e),p=a;break;case"img":case"image":case"link":An("error",e),An("load",e),p=a;break;case"details":An("toggle",e),p=a;break;case"input":Es(e,a),p=qi(e,a),An("invalid",e);break;case"option":Qt(e,a),p=a;break;case"select":Iu(e,a),p=Dl(e,a),An("invalid",e);break;case"textarea":om(e,a),p=yd(e,a),An("invalid",e);break;default:p=a}switch(Sd(t,p),KL(t,e,l,p,c),t){case"input":Qi(e),Q(e,a,!1);break;case"textarea":Qi(e),sm(e);break;case"option":sn(e,a);break;case"select":Sp(e,a);break;default:typeof p.onClick=="function"&&Ov(e);break}}function JL(e,t,a,l,c){kv(t,l);var p=null,m,w;switch(t){case"input":m=qi(e,a),w=qi(e,l),p=[];break;case"select":m=Dl(e,a),w=Dl(e,l),p=[];break;case"textarea":m=yd(e,a),w=yd(e,l),p=[];break;default:m=a,w=l,typeof m.onClick!="function"&&typeof w.onClick=="function"&&Ov(e);break}Sd(t,w);var C,R,M=null;for(C in m)if(!(w.hasOwnProperty(C)||!m.hasOwnProperty(C)||m[C]==null))if(C===jc){var U=m[C];for(R in U)U.hasOwnProperty(R)&&(M||(M={}),M[R]="")}else C===Rh||C===Ac||C===Ev||C===ru||C===AE||(tt.hasOwnProperty(C)?p||(p=[]):(p=p||[]).push(C,null));for(C in w){var F=w[C],q=m!=null?m[C]:void 0;if(!(!w.hasOwnProperty(C)||F===q||F==null&&q==null))if(C===jc)if(F&&Object.freeze(F),q){for(R in q)q.hasOwnProperty(R)&&(!F||!F.hasOwnProperty(R))&&(M||(M={}),M[R]="");for(R in F)F.hasOwnProperty(R)&&q[R]!==F[R]&&(M||(M={}),M[R]=F[R])}else M||(p||(p=[]),p.push(C,M)),M=F;else if(C===Rh){var Z=F?F[Tv]:void 0,te=q?q[Tv]:void 0;Z!=null&&te!==Z&&(p=p||[]).push(C,Z)}else C===Ac?(typeof F=="string"||typeof F=="number")&&(p=p||[]).push(C,""+F):C===Ev||C===ru||(tt.hasOwnProperty(C)?(F!=null&&(typeof F!="function"&&Rv(C,F),C==="onScroll"&&An("scroll",e)),!p&&q!==F&&(p=[])):(p=p||[]).push(C,F))}return M&&(ha(M,w[jc]),(p=p||[]).push(jc,M)),p}function ez(e,t,a,l,c){a==="input"&&c.type==="radio"&&c.name!=null&&E(e,c);var p=Io(a,l),m=Io(a,c);switch(QL(e,t,p,m),a){case"input":j(e,c);break;case"textarea":lm(e,c);break;case"select":vd(e,c);break}}function tz(e){{var t=e.toLowerCase();return Ds.hasOwnProperty(t)&&Ds[t]||null}}function nz(e,t,a,l,c,p,m){var w,C;switch(w=Io(t,a),kv(t,a),t){case"dialog":An("cancel",e),An("close",e);break;case"iframe":case"object":case"embed":An("load",e);break;case"video":case"audio":for(var R=0;R<Eh.length;R++)An(Eh[R],e);break;case"source":An("error",e);break;case"img":case"image":case"link":An("error",e),An("load",e);break;case"details":An("toggle",e);break;case"input":Es(e,a),An("invalid",e);break;case"option":Qt(e,a);break;case"select":Iu(e,a),An("invalid",e);break;case"textarea":om(e,a),An("invalid",e);break}Sd(t,a);{C=new Set;for(var M=e.attributes,U=0;U<M.length;U++){var F=M[U].name.toLowerCase();switch(F){case"value":break;case"checked":break;case"selected":break;default:C.add(M[U].name)}}}var q=null;for(var Z in a)if(a.hasOwnProperty(Z)){var te=a[Z];if(Z===Ac)typeof te=="string"?e.textContent!==te&&(a[ru]!==!0&&Mv(e.textContent,te,p,m),q=[Ac,te]):typeof te=="number"&&e.textContent!==""+te&&(a[ru]!==!0&&Mv(e.textContent,te,p,m),q=[Ac,""+te]);else if(tt.hasOwnProperty(Z))te!=null&&(typeof te!="function"&&Rv(Z,te),Z==="onScroll"&&An("scroll",e));else if(m&&typeof w=="boolean"){var ke=void 0,Xe=vn(Z);if(a[ru]!==!0){if(!(Z===Ev||Z===ru||Z==="value"||Z==="checked"||Z==="selected")){if(Z===Rh){var We=e.innerHTML,It=te?te[Tv]:void 0;if(It!=null){var _t=LE(e,It);_t!==We&&Dh(Z,We,_t)}}else if(Z===jc){if(C.delete(Z),_E){var Y=E0(te);ke=e.getAttribute("style"),Y!==ke&&Dh(Z,ke,Y)}}else if(w&&!re)C.delete(Z.toLowerCase()),ke=$a(e,Z,te),te!==ke&&Dh(Z,ke,te);else if(!wn(Z,Xe,w)&&!cr(Z,te,Xe,w)){var ne=!1;if(Xe!==null)C.delete(Xe.attributeName),ke=wl(e,Z,te,Xe);else{var G=l;if(G===pa&&(G=Tp(t)),G===pa)C.delete(Z.toLowerCase());else{var me=tz(Z);me!==null&&me!==Z&&(ne=!0,C.delete(me)),C.delete(Z)}ke=$a(e,Z,te)}var _e=re;!_e&&te!==ke&&!ne&&Dh(Z,ke,te)}}}}}switch(m&&C.size>0&&a[ru]!==!0&&jE(C),t){case"input":Qi(e),Q(e,a,!0);break;case"textarea":Qi(e),sm(e);break;case"select":case"option":break;default:typeof a.onClick=="function"&&Ov(e);break}return q}function rz(e,t,a){var l=e.nodeValue!==t;return l}function Z0(e,t){{if(ia)return;ia=!0,v("Did not expect server HTML to contain a <%s> in <%s>.",t.nodeName.toLowerCase(),e.nodeName.toLowerCase())}}function J0(e,t){{if(ia)return;ia=!0,v('Did not expect server HTML to contain the text node "%s" in <%s>.',t.nodeValue,e.nodeName.toLowerCase())}}function eb(e,t,a){{if(ia)return;ia=!0,v("Expected server HTML to contain a matching <%s> in <%s>.",t,e.nodeName.toLowerCase())}}function tb(e,t){{if(t===""||ia)return;ia=!0,v('Expected server HTML to contain a matching text node for "%s" in <%s>.',t,e.nodeName.toLowerCase())}}function iz(e,t,a){switch(t){case"input":ee(e,a);return;case"textarea":v0(e,a);return;case"select":Cp(e,a);return}}var Mh=function(){},Oh=function(){};{var az=["address","applet","area","article","aside","base","basefont","bgsound","blockquote","body","br","button","caption","center","col","colgroup","dd","details","dir","div","dl","dt","embed","fieldset","figcaption","figure","footer","form","frame","frameset","h1","h2","h3","h4","h5","h6","head","header","hgroup","hr","html","iframe","img","input","isindex","li","link","listing","main","marquee","menu","menuitem","meta","nav","noembed","noframes","noscript","object","ol","p","param","plaintext","pre","script","section","select","source","style","summary","table","tbody","td","template","textarea","tfoot","th","thead","title","tr","track","ul","wbr","xmp"],NE=["applet","caption","html","table","td","th","marquee","object","template","foreignObject","desc","title"],oz=NE.concat(["button"]),lz=["dd","dt","li","option","optgroup","p","rp","rt"],PE={current:null,formTag:null,aTagInScope:null,buttonTagInScope:null,nobrTagInScope:null,pTagInButtonScope:null,listItemTagAutoclosing:null,dlItemTagAutoclosing:null};Oh=function(e,t){var a=gt({},e||PE),l={tag:t};return NE.indexOf(t)!==-1&&(a.aTagInScope=null,a.buttonTagInScope=null,a.nobrTagInScope=null),oz.indexOf(t)!==-1&&(a.pTagInButtonScope=null),az.indexOf(t)!==-1&&t!=="address"&&t!=="div"&&t!=="p"&&(a.listItemTagAutoclosing=null,a.dlItemTagAutoclosing=null),a.current=l,t==="form"&&(a.formTag=l),t==="a"&&(a.aTagInScope=l),t==="button"&&(a.buttonTagInScope=l),t==="nobr"&&(a.nobrTagInScope=l),t==="p"&&(a.pTagInButtonScope=l),t==="li"&&(a.listItemTagAutoclosing=l),(t==="dd"||t==="dt")&&(a.dlItemTagAutoclosing=l),a};var sz=function(e,t){switch(t){case"select":return e==="option"||e==="optgroup"||e==="#text";case"optgroup":return e==="option"||e==="#text";case"option":return e==="#text";case"tr":return e==="th"||e==="td"||e==="style"||e==="script"||e==="template";case"tbody":case"thead":case"tfoot":return e==="tr"||e==="style"||e==="script"||e==="template";case"colgroup":return e==="col"||e==="template";case"table":return e==="caption"||e==="colgroup"||e==="tbody"||e==="tfoot"||e==="thead"||e==="style"||e==="script"||e==="template";case"head":return e==="base"||e==="basefont"||e==="bgsound"||e==="link"||e==="meta"||e==="title"||e==="noscript"||e==="noframes"||e==="style"||e==="script"||e==="template";case"html":return e==="head"||e==="body"||e==="frameset";case"frameset":return e==="frame";case"#document":return e==="html"}switch(e){case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t!=="h1"&&t!=="h2"&&t!=="h3"&&t!=="h4"&&t!=="h5"&&t!=="h6";case"rp":case"rt":return lz.indexOf(t)===-1;case"body":case"caption":case"col":case"colgroup":case"frameset":case"frame":case"head":case"html":case"tbody":case"td":case"tfoot":case"th":case"thead":case"tr":return t==null}return!0},uz=function(e,t){switch(e){case"address":case"article":case"aside":case"blockquote":case"center":case"details":case"dialog":case"dir":case"div":case"dl":case"fieldset":case"figcaption":case"figure":case"footer":case"header":case"hgroup":case"main":case"menu":case"nav":case"ol":case"p":case"section":case"summary":case"ul":case"pre":case"listing":case"table":case"hr":case"xmp":case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t.pTagInButtonScope;case"form":return t.formTag||t.pTagInButtonScope;case"li":return t.listItemTagAutoclosing;case"dd":case"dt":return t.dlItemTagAutoclosing;case"button":return t.buttonTagInScope;case"a":return t.aTagInScope;case"nobr":return t.nobrTagInScope}return null},FE={};Mh=function(e,t,a){a=a||PE;var l=a.current,c=l&&l.tag;t!=null&&(e!=null&&v("validateDOMNesting: when childText is passed, childTag should be null"),e="#text");var p=sz(e,c)?null:l,m=p?null:uz(e,a),w=p||m;if(w){var C=w.tag,R=!!p+"|"+e+"|"+C;if(!FE[R]){FE[R]=!0;var M=e,U="";if(e==="#text"?/\S/.test(t)?M="Text nodes":(M="Whitespace text nodes",U=" Make sure you don't have any extra whitespace between tags on each line of your source code."):M="<"+e+">",p){var F="";C==="table"&&e==="tr"&&(F+=" Add a <tbody>, <thead> or <tfoot> to your code to match the DOM tree generated by the browser."),v("validateDOMNesting(...): %s cannot appear as a child of <%s>.%s%s",M,C,U,F)}else v("validateDOMNesting(...): %s cannot appear as a descendant of <%s>.",M,C)}}}}var $v="suppressHydrationWarning",Av="$",jv="/$",$h="$?",Ah="$!",cz="style",nb=null,rb=null;function dz(e){var t,a,l=e.nodeType;switch(l){case go:case Uu:{t=l===go?"#document":"#fragment";var c=e.documentElement;a=c?c.namespaceURI:xd(null,"");break}default:{var p=l===Kn?e.parentNode:e,m=p.namespaceURI||null;t=p.tagName,a=xd(m,t);break}}{var w=t.toLowerCase(),C=Oh(null,w);return{namespace:a,ancestorInfo:C}}}function fz(e,t,a){{var l=e,c=xd(l.namespace,t),p=Oh(l.ancestorInfo,t);return{namespace:c,ancestorInfo:p}}}function p4(e){return e}function pz(e){nb=Tr(),rb=RL();var t=null;return Hn(!1),t}function hz(e){DL(rb),Hn(nb),nb=null,rb=null}function gz(e,t,a,l,c){var p;{var m=l;if(Mh(e,null,m.ancestorInfo),typeof t.children=="string"||typeof t.children=="number"){var w=""+t.children,C=Oh(m.ancestorInfo,e);Mh(null,w,C)}p=m.namespace}var R=qL(e,t,a,p);return Lh(c,R),db(R,t),R}function mz(e,t){e.appendChild(t)}function vz(e,t,a,l,c){switch(ZL(e,t,a,l),t){case"button":case"input":case"select":case"textarea":return!!a.autoFocus;case"img":return!0;default:return!1}}function yz(e,t,a,l,c,p){{var m=p;if(typeof l.children!=typeof a.children&&(typeof l.children=="string"||typeof l.children=="number")){var w=""+l.children,C=Oh(m.ancestorInfo,t);Mh(null,w,C)}}return JL(e,t,a,l)}function ib(e,t){return e==="textarea"||e==="noscript"||typeof t.children=="string"||typeof t.children=="number"||typeof t.dangerouslySetInnerHTML=="object"&&t.dangerouslySetInnerHTML!==null&&t.dangerouslySetInnerHTML.__html!=null}function xz(e,t,a,l){{var c=a;Mh(null,e,c.ancestorInfo)}var p=XL(e,t);return Lh(l,p),p}function bz(){var e=window.event;return e===void 0?Cr:qs(e.type)}var ab=typeof setTimeout=="function"?setTimeout:void 0,wz=typeof clearTimeout=="function"?clearTimeout:void 0,ob=-1,IE=typeof Promise=="function"?Promise:void 0,Sz=typeof queueMicrotask=="function"?queueMicrotask:typeof IE<"u"?function(e){return IE.resolve(null).then(e).catch(Cz)}:ab;function Cz(e){setTimeout(function(){throw e})}function Ez(e,t,a,l){switch(t){case"button":case"input":case"select":case"textarea":a.autoFocus&&e.focus();return;case"img":{a.src&&(e.src=a.src);return}}}function Tz(e,t,a,l,c,p){ez(e,t,a,l,c),db(e,c)}function UE(e){Fo(e,"")}function kz(e,t,a){e.nodeValue=a}function Rz(e,t){e.appendChild(t)}function Dz(e,t){var a;e.nodeType===Kn?(a=e.parentNode,a.insertBefore(t,e)):(a=e,a.appendChild(t));var l=e._reactRootContainer;l==null&&a.onclick===null&&Ov(a)}function Mz(e,t,a){e.insertBefore(t,a)}function Oz(e,t,a){e.nodeType===Kn?e.parentNode.insertBefore(t,a):e.insertBefore(t,a)}function $z(e,t){e.removeChild(t)}function Az(e,t){e.nodeType===Kn?e.parentNode.removeChild(t):e.removeChild(t)}function lb(e,t){var a=t,l=0;do{var c=a.nextSibling;if(e.removeChild(a),c&&c.nodeType===Kn){var p=c.data;if(p===jv)if(l===0){e.removeChild(c),Ur(t);return}else l--;else(p===Av||p===$h||p===Ah)&&l++}a=c}while(a);Ur(t)}function jz(e,t){e.nodeType===Kn?lb(e.parentNode,t):e.nodeType===ci&&lb(e,t),Ur(e)}function _z(e){e=e;var t=e.style;typeof t.setProperty=="function"?t.setProperty("display","none","important"):t.display="none"}function Lz(e){e.nodeValue=""}function zz(e,t){e=e;var a=t[cz],l=a!=null&&a.hasOwnProperty("display")?a.display:null;e.style.display=wd("display",l)}function Nz(e,t){e.nodeValue=t}function Pz(e){e.nodeType===ci?e.textContent="":e.nodeType===go&&e.documentElement&&e.removeChild(e.documentElement)}function Fz(e,t,a){return e.nodeType!==ci||t.toLowerCase()!==e.nodeName.toLowerCase()?null:e}function Iz(e,t){return t===""||e.nodeType!==ho?null:e}function Uz(e){return e.nodeType!==Kn?null:e}function BE(e){return e.data===$h}function sb(e){return e.data===Ah}function Bz(e){var t=e.nextSibling&&e.nextSibling.dataset,a,l,c;return t&&(a=t.dgst,l=t.msg,c=t.stck),{message:l,digest:a,stack:c}}function Hz(e,t){e._reactRetry=t}function _v(e){for(;e!=null;e=e.nextSibling){var t=e.nodeType;if(t===ci||t===ho)break;if(t===Kn){var a=e.data;if(a===Av||a===Ah||a===$h)break;if(a===jv)return null}}return e}function jh(e){return _v(e.nextSibling)}function Vz(e){return _v(e.firstChild)}function Wz(e){return _v(e.firstChild)}function Yz(e){return _v(e.nextSibling)}function Gz(e,t,a,l,c,p,m){Lh(p,e),db(e,a);var w;{var C=c;w=C.namespace}var R=(p.mode&Dt)!==Ge;return nz(e,t,a,w,l,R,m)}function Kz(e,t,a,l){return Lh(a,e),a.mode&Dt,rz(e,t)}function Qz(e,t){Lh(t,e)}function qz(e){for(var t=e.nextSibling,a=0;t;){if(t.nodeType===Kn){var l=t.data;if(l===jv){if(a===0)return jh(t);a--}else(l===Av||l===Ah||l===$h)&&a++}t=t.nextSibling}return null}function HE(e){for(var t=e.previousSibling,a=0;t;){if(t.nodeType===Kn){var l=t.data;if(l===Av||l===Ah||l===$h){if(a===0)return t;a--}else l===jv&&a++}t=t.previousSibling}return null}function Xz(e){Ur(e)}function Zz(e){Ur(e)}function Jz(e){return e!=="head"&&e!=="body"}function eN(e,t,a,l){var c=!0;Mv(t.nodeValue,a,l,c)}function tN(e,t,a,l,c,p){if(t[$v]!==!0){var m=!0;Mv(l.nodeValue,c,p,m)}}function nN(e,t){t.nodeType===ci?Z0(e,t):t.nodeType===Kn||J0(e,t)}function rN(e,t){{var a=e.parentNode;a!==null&&(t.nodeType===ci?Z0(a,t):t.nodeType===Kn||J0(a,t))}}function iN(e,t,a,l,c){(c||t[$v]!==!0)&&(l.nodeType===ci?Z0(a,l):l.nodeType===Kn||J0(a,l))}function aN(e,t,a){eb(e,t)}function oN(e,t){tb(e,t)}function lN(e,t,a){{var l=e.parentNode;l!==null&&eb(l,t)}}function sN(e,t){{var a=e.parentNode;a!==null&&tb(a,t)}}function uN(e,t,a,l,c,p){(p||t[$v]!==!0)&&eb(a,l)}function cN(e,t,a,l,c){(c||t[$v]!==!0)&&tb(a,l)}function dN(e){v("An error occurred during hydration. The server HTML was replaced with client content in <%s>.",e.nodeName.toLowerCase())}function fN(e){Th(e)}var Of=Math.random().toString(36).slice(2),$f="__reactFiber$"+Of,ub="__reactProps$"+Of,_h="__reactContainer$"+Of,cb="__reactEvents$"+Of,pN="__reactListeners$"+Of,hN="__reactHandles$"+Of;function gN(e){delete e[$f],delete e[ub],delete e[cb],delete e[pN],delete e[hN]}function Lh(e,t){t[$f]=e}function Lv(e,t){t[_h]=e}function VE(e){e[_h]=null}function zh(e){return!!e[_h]}function _c(e){var t=e[$f];if(t)return t;for(var a=e.parentNode;a;){if(t=a[_h]||a[$f],t){var l=t.alternate;if(t.child!==null||l!==null&&l.child!==null)for(var c=HE(e);c!==null;){var p=c[$f];if(p)return p;c=HE(c)}return t}e=a,a=e.parentNode}return null}function iu(e){var t=e[$f]||e[_h];return t&&(t.tag===N||t.tag===J||t.tag===se||t.tag===_)?t:null}function Af(e){if(e.tag===N||e.tag===J)return e.stateNode;throw new Error("getNodeFromInstance: Invalid argument.")}function zv(e){return e[ub]||null}function db(e,t){e[ub]=t}function mN(e){var t=e[cb];return t===void 0&&(t=e[cb]=new Set),t}var WE={},YE=d.ReactDebugCurrentFrame;function Nv(e){if(e){var t=e._owner,a=Lu(e.type,e._source,t?t.type:null);YE.setExtraStackFrame(a)}else YE.setExtraStackFrame(null)}function So(e,t,a,l,c){{var p=Function.call.bind(Pn);for(var m in e)if(p(e,m)){var w=void 0;try{if(typeof e[m]!="function"){var C=Error((l||"React class")+": "+a+" type `"+m+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof e[m]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw C.name="Invariant Violation",C}w=e[m](t,m,l,a,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(R){w=R}w&&!(w instanceof Error)&&(Nv(c),v("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",l||"React class",a,m,typeof w),Nv(null)),w instanceof Error&&!(w.message in WE)&&(WE[w.message]=!0,Nv(c),v("Failed %s type: %s",a,w.message),Nv(null))}}}var fb=[],Pv;Pv=[];var es=-1;function au(e){return{current:e}}function bi(e,t){if(es<0){v("Unexpected pop.");return}t!==Pv[es]&&v("Unexpected Fiber popped."),e.current=fb[es],fb[es]=null,Pv[es]=null,es--}function wi(e,t,a){es++,fb[es]=e.current,Pv[es]=a,e.current=t}var pb;pb={};var wa={};Object.freeze(wa);var ts=au(wa),ll=au(!1),hb=wa;function jf(e,t,a){return a&&sl(t)?hb:ts.current}function GE(e,t,a){{var l=e.stateNode;l.__reactInternalMemoizedUnmaskedChildContext=t,l.__reactInternalMemoizedMaskedChildContext=a}}function _f(e,t){{var a=e.type,l=a.contextTypes;if(!l)return wa;var c=e.stateNode;if(c&&c.__reactInternalMemoizedUnmaskedChildContext===t)return c.__reactInternalMemoizedMaskedChildContext;var p={};for(var m in l)p[m]=t[m];{var w=lt(e)||"Unknown";So(l,p,"context",w)}return c&&GE(e,t,p),p}}function Fv(){return ll.current}function sl(e){{var t=e.childContextTypes;return t!=null}}function Iv(e){bi(ll,e),bi(ts,e)}function gb(e){bi(ll,e),bi(ts,e)}function KE(e,t,a){{if(ts.current!==wa)throw new Error("Unexpected context found on stack. This error is likely caused by a bug in React. Please file an issue.");wi(ts,t,e),wi(ll,a,e)}}function QE(e,t,a){{var l=e.stateNode,c=t.childContextTypes;if(typeof l.getChildContext!="function"){{var p=lt(e)||"Unknown";pb[p]||(pb[p]=!0,v("%s.childContextTypes is specified but there is no getChildContext() method on the instance. You can either define getChildContext() on %s or remove childContextTypes from it.",p,p))}return a}var m=l.getChildContext();for(var w in m)if(!(w in c))throw new Error((lt(e)||"Unknown")+'.getChildContext(): key "'+w+'" is not defined in childContextTypes.');{var C=lt(e)||"Unknown";So(c,m,"child context",C)}return gt({},a,m)}}function Uv(e){{var t=e.stateNode,a=t&&t.__reactInternalMemoizedMergedChildContext||wa;return hb=ts.current,wi(ts,a,e),wi(ll,ll.current,e),!0}}function qE(e,t,a){{var l=e.stateNode;if(!l)throw new Error("Expected to have an instance by this point. This error is likely caused by a bug in React. Please file an issue.");if(a){var c=QE(e,t,hb);l.__reactInternalMemoizedMergedChildContext=c,bi(ll,e),bi(ts,e),wi(ts,c,e),wi(ll,a,e)}else bi(ll,e),wi(ll,a,e)}}function vN(e){{if(!zm(e)||e.tag!==O)throw new Error("Expected subtree parent to be a mounted class component. This error is likely caused by a bug in React. Please file an issue.");var t=e;do{switch(t.tag){case _:return t.stateNode.context;case O:{var a=t.type;if(sl(a))return t.stateNode.__reactInternalMemoizedMergedChildContext;break}}t=t.return}while(t!==null);throw new Error("Found unexpected detached subtree parent. This error is likely caused by a bug in React. Please file an issue.")}}var ou=0,Bv=1,ns=null,mb=!1,vb=!1;function XE(e){ns===null?ns=[e]:ns.push(e)}function yN(e){mb=!0,XE(e)}function ZE(){mb&&lu()}function lu(){if(!vb&&ns!==null){vb=!0;var e=0,t=Ai();try{var a=!0,l=ns;for(rr(yi);e<l.length;e++){var c=l[e];do c=c(a);while(c!==null)}ns=null,mb=!1}catch(p){throw ns!==null&&(ns=ns.slice(e+1)),Ip(yo,lu),p}finally{rr(t),vb=!1}}return null}var Lf=[],zf=0,Hv=null,Vv=0,Ya=[],Ga=0,Lc=null,rs=1,is="";function xN(e){return Nc(),(e.flags&tc)!==Ye}function bN(e){return Nc(),Vv}function wN(){var e=is,t=rs,a=t&~SN(t);return a.toString(32)+e}function zc(e,t){Nc(),Lf[zf++]=Vv,Lf[zf++]=Hv,Hv=e,Vv=t}function JE(e,t,a){Nc(),Ya[Ga++]=rs,Ya[Ga++]=is,Ya[Ga++]=Lc,Lc=e;var l=rs,c=is,p=Wv(l)-1,m=l&~(1<<p),w=a+1,C=Wv(t)+p;if(C>30){var R=p-p%5,M=(1<<R)-1,U=(m&M).toString(32),F=m>>R,q=p-R,Z=Wv(t)+q,te=w<<q,ke=te|F,Xe=U+c;rs=1<<Z|ke,is=Xe}else{var We=w<<p,It=We|m,_t=c;rs=1<<C|It,is=_t}}function yb(e){Nc();var t=e.return;if(t!==null){var a=1,l=0;zc(e,a),JE(e,a,l)}}function Wv(e){return 32-nr(e)}function SN(e){return 1<<Wv(e)-1}function xb(e){for(;e===Hv;)Hv=Lf[--zf],Lf[zf]=null,Vv=Lf[--zf],Lf[zf]=null;for(;e===Lc;)Lc=Ya[--Ga],Ya[Ga]=null,is=Ya[--Ga],Ya[Ga]=null,rs=Ya[--Ga],Ya[Ga]=null}function CN(){return Nc(),Lc!==null?{id:rs,overflow:is}:null}function EN(e,t){Nc(),Ya[Ga++]=rs,Ya[Ga++]=is,Ya[Ga++]=Lc,rs=t.id,is=t.overflow,Lc=e}function Nc(){Xr()||v("Expected to be hydrating. This is a bug in React. Please file an issue.")}var qr=null,Ka=null,Co=!1,Pc=!1,su=null;function TN(){Co&&v("We should not be hydrating here. This is a bug in React. Please file a bug.")}function eT(){Pc=!0}function kN(){return Pc}function RN(e){var t=e.stateNode.containerInfo;return Ka=Wz(t),qr=e,Co=!0,su=null,Pc=!1,!0}function DN(e,t,a){return Ka=Yz(t),qr=e,Co=!0,su=null,Pc=!1,a!==null&&EN(e,a),!0}function tT(e,t){switch(e.tag){case _:{nN(e.stateNode.containerInfo,t);break}case N:{var a=(e.mode&Dt)!==Ge;iN(e.type,e.memoizedProps,e.stateNode,t,a);break}case se:{var l=e.memoizedState;l.dehydrated!==null&&rN(l.dehydrated,t);break}}}function nT(e,t){tT(e,t);var a=A5();a.stateNode=t,a.return=e;var l=e.deletions;l===null?(e.deletions=[a],e.flags|=di):l.push(a)}function bb(e,t){{if(Pc)return;switch(e.tag){case _:{var a=e.stateNode.containerInfo;switch(t.tag){case N:var l=t.type;t.pendingProps,aN(a,l);break;case J:var c=t.pendingProps;oN(a,c);break}break}case N:{var p=e.type,m=e.memoizedProps,w=e.stateNode;switch(t.tag){case N:{var C=t.type,R=t.pendingProps,M=(e.mode&Dt)!==Ge;uN(p,m,w,C,R,M);break}case J:{var U=t.pendingProps,F=(e.mode&Dt)!==Ge;cN(p,m,w,U,F);break}}break}case se:{var q=e.memoizedState,Z=q.dehydrated;if(Z!==null)switch(t.tag){case N:var te=t.type;t.pendingProps,lN(Z,te);break;case J:var ke=t.pendingProps;sN(Z,ke);break}break}default:return}}}function rT(e,t){t.flags=t.flags&~Ln|_n,bb(e,t)}function iT(e,t){switch(e.tag){case N:{var a=e.type;e.pendingProps;var l=Fz(t,a);return l!==null?(e.stateNode=l,qr=e,Ka=Vz(l),!0):!1}case J:{var c=e.pendingProps,p=Iz(t,c);return p!==null?(e.stateNode=p,qr=e,Ka=null,!0):!1}case se:{var m=Uz(t);if(m!==null){var w={dehydrated:m,treeContext:CN(),retryLane:gi};e.memoizedState=w;var C=j5(m);return C.return=e,e.child=C,qr=e,Ka=null,!0}return!1}default:return!1}}function wb(e){return(e.mode&Dt)!==Ge&&(e.flags&Et)===Ye}function Sb(e){throw new Error("Hydration failed because the initial UI does not match what was rendered on the server.")}function Cb(e){if(Co){var t=Ka;if(!t){wb(e)&&(bb(qr,e),Sb()),rT(qr,e),Co=!1,qr=e;return}var a=t;if(!iT(e,t)){wb(e)&&(bb(qr,e),Sb()),t=jh(a);var l=qr;if(!t||!iT(e,t)){rT(qr,e),Co=!1,qr=e;return}nT(l,a)}}}function MN(e,t,a){var l=e.stateNode,c=!Pc,p=Gz(l,e.type,e.memoizedProps,t,a,e,c);return e.updateQueue=p,p!==null}function ON(e){var t=e.stateNode,a=e.memoizedProps,l=Kz(t,a,e);if(l){var c=qr;if(c!==null)switch(c.tag){case _:{var p=c.stateNode.containerInfo,m=(c.mode&Dt)!==Ge;eN(p,t,a,m);break}case N:{var w=c.type,C=c.memoizedProps,R=c.stateNode,M=(c.mode&Dt)!==Ge;tN(w,C,R,t,a,M);break}}}return l}function $N(e){var t=e.memoizedState,a=t!==null?t.dehydrated:null;if(!a)throw new Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");Qz(a,e)}function AN(e){var t=e.memoizedState,a=t!==null?t.dehydrated:null;if(!a)throw new Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");return qz(a)}function aT(e){for(var t=e.return;t!==null&&t.tag!==N&&t.tag!==_&&t.tag!==se;)t=t.return;qr=t}function Yv(e){if(e!==qr)return!1;if(!Co)return aT(e),Co=!0,!1;if(e.tag!==_&&(e.tag!==N||Jz(e.type)&&!ib(e.type,e.memoizedProps))){var t=Ka;if(t)if(wb(e))oT(e),Sb();else for(;t;)nT(e,t),t=jh(t)}return aT(e),e.tag===se?Ka=AN(e):Ka=qr?jh(e.stateNode):null,!0}function jN(){return Co&&Ka!==null}function oT(e){for(var t=Ka;t;)tT(e,t),t=jh(t)}function Nf(){qr=null,Ka=null,Co=!1,Pc=!1}function lT(){su!==null&&(eR(su),su=null)}function Xr(){return Co}function Eb(e){su===null?su=[e]:su.push(e)}var _N=d.ReactCurrentBatchConfig,LN=null;function zN(){return _N.transition}var Eo={recordUnsafeLifecycleWarnings:function(e,t){},flushPendingUnsafeLifecycleWarnings:function(){},recordLegacyContextWarning:function(e,t){},flushLegacyContextWarning:function(){},discardPendingWarnings:function(){}};{var NN=function(e){for(var t=null,a=e;a!==null;)a.mode&mt&&(t=a),a=a.return;return t},Fc=function(e){var t=[];return e.forEach(function(a){t.push(a)}),t.sort().join(", ")},Nh=[],Ph=[],Fh=[],Ih=[],Uh=[],Bh=[],Ic=new Set;Eo.recordUnsafeLifecycleWarnings=function(e,t){Ic.has(e.type)||(typeof t.componentWillMount=="function"&&t.componentWillMount.__suppressDeprecationWarning!==!0&&Nh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillMount=="function"&&Ph.push(e),typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps.__suppressDeprecationWarning!==!0&&Fh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillReceiveProps=="function"&&Ih.push(e),typeof t.componentWillUpdate=="function"&&t.componentWillUpdate.__suppressDeprecationWarning!==!0&&Uh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillUpdate=="function"&&Bh.push(e))},Eo.flushPendingUnsafeLifecycleWarnings=function(){var e=new Set;Nh.length>0&&(Nh.forEach(function(F){e.add(lt(F)||"Component"),Ic.add(F.type)}),Nh=[]);var t=new Set;Ph.length>0&&(Ph.forEach(function(F){t.add(lt(F)||"Component"),Ic.add(F.type)}),Ph=[]);var a=new Set;Fh.length>0&&(Fh.forEach(function(F){a.add(lt(F)||"Component"),Ic.add(F.type)}),Fh=[]);var l=new Set;Ih.length>0&&(Ih.forEach(function(F){l.add(lt(F)||"Component"),Ic.add(F.type)}),Ih=[]);var c=new Set;Uh.length>0&&(Uh.forEach(function(F){c.add(lt(F)||"Component"),Ic.add(F.type)}),Uh=[]);var p=new Set;if(Bh.length>0&&(Bh.forEach(function(F){p.add(lt(F)||"Component"),Ic.add(F.type)}),Bh=[]),t.size>0){var m=Fc(t);v(`Using UNSAFE_componentWillMount in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.

Please update the following components: %s`,m)}if(l.size>0){var w=Fc(l);v(`Using UNSAFE_componentWillReceiveProps in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://reactjs.org/link/derived-state

Please update the following components: %s`,w)}if(p.size>0){var C=Fc(p);v(`Using UNSAFE_componentWillUpdate in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.

Please update the following components: %s`,C)}if(e.size>0){var R=Fc(e);S(`componentWillMount has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.
* Rename componentWillMount to UNSAFE_componentWillMount to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,R)}if(a.size>0){var M=Fc(a);S(`componentWillReceiveProps has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://reactjs.org/link/derived-state
* Rename componentWillReceiveProps to UNSAFE_componentWillReceiveProps to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,M)}if(c.size>0){var U=Fc(c);S(`componentWillUpdate has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* Rename componentWillUpdate to UNSAFE_componentWillUpdate to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,U)}};var Gv=new Map,sT=new Set;Eo.recordLegacyContextWarning=function(e,t){var a=NN(e);if(a===null){v("Expected to find a StrictMode component in a strict mode tree. This error is likely caused by a bug in React. Please file an issue.");return}if(!sT.has(e.type)){var l=Gv.get(a);(e.type.contextTypes!=null||e.type.childContextTypes!=null||t!==null&&typeof t.getChildContext=="function")&&(l===void 0&&(l=[],Gv.set(a,l)),l.push(e))}},Eo.flushLegacyContextWarning=function(){Gv.forEach(function(e,t){if(e.length!==0){var a=e[0],l=new Set;e.forEach(function(p){l.add(lt(p)||"Component"),sT.add(p.type)});var c=Fc(l);try{ln(a),v(`Legacy context API has been detected within a strict-mode tree.

The old API will be supported in all 16.x releases, but applications using it should migrate to the new version.

Please update the following components: %s

Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)}finally{jn()}}})},Eo.discardPendingWarnings=function(){Nh=[],Ph=[],Fh=[],Ih=[],Uh=[],Bh=[],Gv=new Map}}var Tb,kb,Rb,Db,Mb,uT=function(e,t){};Tb=!1,kb=!1,Rb={},Db={},Mb={},uT=function(e,t){if(!(e===null||typeof e!="object")&&!(!e._store||e._store.validated||e.key!=null)){if(typeof e._store!="object")throw new Error("React Component in warnForMissingKey should have a _store. This error is likely caused by a bug in React. Please file an issue.");e._store.validated=!0;var a=lt(t)||"Component";Db[a]||(Db[a]=!0,v('Each child in a list should have a unique "key" prop. See https://reactjs.org/link/warning-keys for more information.'))}};function PN(e){return e.prototype&&e.prototype.isReactComponent}function Hh(e,t,a){var l=a.ref;if(l!==null&&typeof l!="function"&&typeof l!="object"){if((e.mode&mt||Ve)&&!(a._owner&&a._self&&a._owner.stateNode!==a._self)&&!(a._owner&&a._owner.tag!==O)&&!(typeof a.type=="function"&&!PN(a.type))&&a._owner){var c=lt(e)||"Component";Rb[c]||(v('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. We recommend using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',c,l),Rb[c]=!0)}if(a._owner){var p=a._owner,m;if(p){var w=p;if(w.tag!==O)throw new Error("Function components cannot have string refs. We recommend using useRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref");m=w.stateNode}if(!m)throw new Error("Missing owner for string ref "+l+". This error is likely caused by a bug in React. Please file an issue.");var C=m;ua(l,"ref");var R=""+l;if(t!==null&&t.ref!==null&&typeof t.ref=="function"&&t.ref._stringRef===R)return t.ref;var M=function(U){var F=C.refs;U===null?delete F[R]:F[R]=U};return M._stringRef=R,M}else{if(typeof l!="string")throw new Error("Expected ref to be a function, a string, an object returned by React.createRef(), or null.");if(!a._owner)throw new Error("Element ref was specified as a string ("+l+`) but no owner was set. This could happen for one of the following reasons:
1. You may be adding a ref to a function component
2. You may be adding a ref to a component that was not created inside a component's render method
3. You have multiple copies of React loaded
See https://reactjs.org/link/refs-must-have-owner for more information.`)}}return l}function Kv(e,t){var a=Object.prototype.toString.call(t);throw new Error("Objects are not valid as a React child (found: "+(a==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":a)+"). If you meant to render a collection of children, use an array instead.")}function Qv(e){{var t=lt(e)||"Component";if(Mb[t])return;Mb[t]=!0,v("Functions are not valid as a React child. This may happen if you return a Component instead of <Component /> from render. Or maybe you meant to call this function rather than return it.")}}function cT(e){var t=e._payload,a=e._init;return a(t)}function dT(e){function t(Y,ne){if(e){var G=Y.deletions;G===null?(Y.deletions=[ne],Y.flags|=di):G.push(ne)}}function a(Y,ne){if(!e)return null;for(var G=ne;G!==null;)t(Y,G),G=G.sibling;return null}function l(Y,ne){for(var G=new Map,me=ne;me!==null;)me.key!==null?G.set(me.key,me):G.set(me.index,me),me=me.sibling;return G}function c(Y,ne){var G=Qc(Y,ne);return G.index=0,G.sibling=null,G}function p(Y,ne,G){if(Y.index=G,!e)return Y.flags|=tc,ne;var me=Y.alternate;if(me!==null){var _e=me.index;return _e<ne?(Y.flags|=_n,ne):_e}else return Y.flags|=_n,ne}function m(Y){return e&&Y.alternate===null&&(Y.flags|=_n),Y}function w(Y,ne,G,me){if(ne===null||ne.tag!==J){var _e=TS(G,Y.mode,me);return _e.return=Y,_e}else{var Me=c(ne,G);return Me.return=Y,Me}}function C(Y,ne,G,me){var _e=G.type;if(_e===oi)return M(Y,ne,G.props.children,me,G.key);if(ne!==null&&(ne.elementType===_e||mR(ne,G)||typeof _e=="object"&&_e!==null&&_e.$$typeof===ut&&cT(_e)===ne.type)){var Me=c(ne,G.props);return Me.ref=Hh(Y,ne,G),Me.return=Y,Me._debugSource=G._source,Me._debugOwner=G._owner,Me}var ot=ES(G,Y.mode,me);return ot.ref=Hh(Y,ne,G),ot.return=Y,ot}function R(Y,ne,G,me){if(ne===null||ne.tag!==V||ne.stateNode.containerInfo!==G.containerInfo||ne.stateNode.implementation!==G.implementation){var _e=kS(G,Y.mode,me);return _e.return=Y,_e}else{var Me=c(ne,G.children||[]);return Me.return=Y,Me}}function M(Y,ne,G,me,_e){if(ne===null||ne.tag!==xe){var Me=xu(G,Y.mode,me,_e);return Me.return=Y,Me}else{var ot=c(ne,G);return ot.return=Y,ot}}function U(Y,ne,G){if(typeof ne=="string"&&ne!==""||typeof ne=="number"){var me=TS(""+ne,Y.mode,G);return me.return=Y,me}if(typeof ne=="object"&&ne!==null){switch(ne.$$typeof){case br:{var _e=ES(ne,Y.mode,G);return _e.ref=Hh(Y,null,ne),_e.return=Y,_e}case Mi:{var Me=kS(ne,Y.mode,G);return Me.return=Y,Me}case ut:{var ot=ne._payload,dt=ne._init;return U(Y,dt(ot),G)}}if(yt(ne)||kn(ne)){var fn=xu(ne,Y.mode,G,null);return fn.return=Y,fn}Kv(Y,ne)}return typeof ne=="function"&&Qv(Y),null}function F(Y,ne,G,me){var _e=ne!==null?ne.key:null;if(typeof G=="string"&&G!==""||typeof G=="number")return _e!==null?null:w(Y,ne,""+G,me);if(typeof G=="object"&&G!==null){switch(G.$$typeof){case br:return G.key===_e?C(Y,ne,G,me):null;case Mi:return G.key===_e?R(Y,ne,G,me):null;case ut:{var Me=G._payload,ot=G._init;return F(Y,ne,ot(Me),me)}}if(yt(G)||kn(G))return _e!==null?null:M(Y,ne,G,me,null);Kv(Y,G)}return typeof G=="function"&&Qv(Y),null}function q(Y,ne,G,me,_e){if(typeof me=="string"&&me!==""||typeof me=="number"){var Me=Y.get(G)||null;return w(ne,Me,""+me,_e)}if(typeof me=="object"&&me!==null){switch(me.$$typeof){case br:{var ot=Y.get(me.key===null?G:me.key)||null;return C(ne,ot,me,_e)}case Mi:{var dt=Y.get(me.key===null?G:me.key)||null;return R(ne,dt,me,_e)}case ut:var fn=me._payload,Vt=me._init;return q(Y,ne,G,Vt(fn),_e)}if(yt(me)||kn(me)){var or=Y.get(G)||null;return M(ne,or,me,_e,null)}Kv(ne,me)}return typeof me=="function"&&Qv(ne),null}function Z(Y,ne,G){{if(typeof Y!="object"||Y===null)return ne;switch(Y.$$typeof){case br:case Mi:uT(Y,G);var me=Y.key;if(typeof me!="string")break;if(ne===null){ne=new Set,ne.add(me);break}if(!ne.has(me)){ne.add(me);break}v("Encountered two children with the same key, `%s`. Keys should be unique so that components maintain their identity across updates. Non-unique keys may cause children to be duplicated and/or omitted — the behavior is unsupported and could change in a future version.",me);break;case ut:var _e=Y._payload,Me=Y._init;Z(Me(_e),ne,G);break}}return ne}function te(Y,ne,G,me){for(var _e=null,Me=0;Me<G.length;Me++){var ot=G[Me];_e=Z(ot,_e,Y)}for(var dt=null,fn=null,Vt=ne,or=0,Wt=0,Jn=null;Vt!==null&&Wt<G.length;Wt++){Vt.index>Wt?(Jn=Vt,Vt=null):Jn=Vt.sibling;var Ci=F(Y,Vt,G[Wt],me);if(Ci===null){Vt===null&&(Vt=Jn);break}e&&Vt&&Ci.alternate===null&&t(Y,Vt),or=p(Ci,or,Wt),fn===null?dt=Ci:fn.sibling=Ci,fn=Ci,Vt=Jn}if(Wt===G.length){if(a(Y,Vt),Xr()){var ii=Wt;zc(Y,ii)}return dt}if(Vt===null){for(;Wt<G.length;Wt++){var Ca=U(Y,G[Wt],me);Ca!==null&&(or=p(Ca,or,Wt),fn===null?dt=Ca:fn.sibling=Ca,fn=Ca)}if(Xr()){var Fi=Wt;zc(Y,Fi)}return dt}for(var Ii=l(Y,Vt);Wt<G.length;Wt++){var Ei=q(Ii,Y,Wt,G[Wt],me);Ei!==null&&(e&&Ei.alternate!==null&&Ii.delete(Ei.key===null?Wt:Ei.key),or=p(Ei,or,Wt),fn===null?dt=Ei:fn.sibling=Ei,fn=Ei)}if(e&&Ii.forEach(function(np){return t(Y,np)}),Xr()){var ds=Wt;zc(Y,ds)}return dt}function ke(Y,ne,G,me){var _e=kn(G);if(typeof _e!="function")throw new Error("An object is not an iterable. This error is likely caused by a bug in React. Please file an issue.");{typeof Symbol=="function"&&G[Symbol.toStringTag]==="Generator"&&(kb||v("Using Generators as children is unsupported and will likely yield unexpected results because enumerating a generator mutates it. You may convert it to an array with `Array.from()` or the `[...spread]` operator before rendering. Keep in mind you might need to polyfill these features for older browsers."),kb=!0),G.entries===_e&&(Tb||v("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),Tb=!0);var Me=_e.call(G);if(Me)for(var ot=null,dt=Me.next();!dt.done;dt=Me.next()){var fn=dt.value;ot=Z(fn,ot,Y)}}var Vt=_e.call(G);if(Vt==null)throw new Error("An iterable object provided no iterator.");for(var or=null,Wt=null,Jn=ne,Ci=0,ii=0,Ca=null,Fi=Vt.next();Jn!==null&&!Fi.done;ii++,Fi=Vt.next()){Jn.index>ii?(Ca=Jn,Jn=null):Ca=Jn.sibling;var Ii=F(Y,Jn,Fi.value,me);if(Ii===null){Jn===null&&(Jn=Ca);break}e&&Jn&&Ii.alternate===null&&t(Y,Jn),Ci=p(Ii,Ci,ii),Wt===null?or=Ii:Wt.sibling=Ii,Wt=Ii,Jn=Ca}if(Fi.done){if(a(Y,Jn),Xr()){var Ei=ii;zc(Y,Ei)}return or}if(Jn===null){for(;!Fi.done;ii++,Fi=Vt.next()){var ds=U(Y,Fi.value,me);ds!==null&&(Ci=p(ds,Ci,ii),Wt===null?or=ds:Wt.sibling=ds,Wt=ds)}if(Xr()){var np=ii;zc(Y,np)}return or}for(var wg=l(Y,Jn);!Fi.done;ii++,Fi=Vt.next()){var ml=q(wg,Y,ii,Fi.value,me);ml!==null&&(e&&ml.alternate!==null&&wg.delete(ml.key===null?ii:ml.key),Ci=p(ml,Ci,ii),Wt===null?or=ml:Wt.sibling=ml,Wt=ml)}if(e&&wg.forEach(function(c4){return t(Y,c4)}),Xr()){var u4=ii;zc(Y,u4)}return or}function Xe(Y,ne,G,me){if(ne!==null&&ne.tag===J){a(Y,ne.sibling);var _e=c(ne,G);return _e.return=Y,_e}a(Y,ne);var Me=TS(G,Y.mode,me);return Me.return=Y,Me}function We(Y,ne,G,me){for(var _e=G.key,Me=ne;Me!==null;){if(Me.key===_e){var ot=G.type;if(ot===oi){if(Me.tag===xe){a(Y,Me.sibling);var dt=c(Me,G.props.children);return dt.return=Y,dt._debugSource=G._source,dt._debugOwner=G._owner,dt}}else if(Me.elementType===ot||mR(Me,G)||typeof ot=="object"&&ot!==null&&ot.$$typeof===ut&&cT(ot)===Me.type){a(Y,Me.sibling);var fn=c(Me,G.props);return fn.ref=Hh(Y,Me,G),fn.return=Y,fn._debugSource=G._source,fn._debugOwner=G._owner,fn}a(Y,Me);break}else t(Y,Me);Me=Me.sibling}if(G.type===oi){var Vt=xu(G.props.children,Y.mode,me,G.key);return Vt.return=Y,Vt}else{var or=ES(G,Y.mode,me);return or.ref=Hh(Y,ne,G),or.return=Y,or}}function It(Y,ne,G,me){for(var _e=G.key,Me=ne;Me!==null;){if(Me.key===_e)if(Me.tag===V&&Me.stateNode.containerInfo===G.containerInfo&&Me.stateNode.implementation===G.implementation){a(Y,Me.sibling);var ot=c(Me,G.children||[]);return ot.return=Y,ot}else{a(Y,Me);break}else t(Y,Me);Me=Me.sibling}var dt=kS(G,Y.mode,me);return dt.return=Y,dt}function _t(Y,ne,G,me){var _e=typeof G=="object"&&G!==null&&G.type===oi&&G.key===null;if(_e&&(G=G.props.children),typeof G=="object"&&G!==null){switch(G.$$typeof){case br:return m(We(Y,ne,G,me));case Mi:return m(It(Y,ne,G,me));case ut:var Me=G._payload,ot=G._init;return _t(Y,ne,ot(Me),me)}if(yt(G))return te(Y,ne,G,me);if(kn(G))return ke(Y,ne,G,me);Kv(Y,G)}return typeof G=="string"&&G!==""||typeof G=="number"?m(Xe(Y,ne,""+G,me)):(typeof G=="function"&&Qv(Y),a(Y,ne))}return _t}var Pf=dT(!0),fT=dT(!1);function FN(e,t){if(e!==null&&t.child!==e.child)throw new Error("Resuming work not yet implemented.");if(t.child!==null){var a=t.child,l=Qc(a,a.pendingProps);for(t.child=l,l.return=t;a.sibling!==null;)a=a.sibling,l=l.sibling=Qc(a,a.pendingProps),l.return=t;l.sibling=null}}function IN(e,t){for(var a=e.child;a!==null;)R5(a,t),a=a.sibling}var Ob=au(null),$b;$b={};var qv=null,Ff=null,Ab=null,Xv=!1;function Zv(){qv=null,Ff=null,Ab=null,Xv=!1}function pT(){Xv=!0}function hT(){Xv=!1}function gT(e,t,a){wi(Ob,t._currentValue,e),t._currentValue=a,t._currentRenderer!==void 0&&t._currentRenderer!==null&&t._currentRenderer!==$b&&v("Detected multiple renderers concurrently rendering the same context provider. This is currently unsupported."),t._currentRenderer=$b}function jb(e,t){var a=Ob.current;bi(Ob,t),e._currentValue=a}function _b(e,t,a){for(var l=e;l!==null;){var c=l.alternate;if(Hl(l.childLanes,t)?c!==null&&!Hl(c.childLanes,t)&&(c.childLanes=xt(c.childLanes,t)):(l.childLanes=xt(l.childLanes,t),c!==null&&(c.childLanes=xt(c.childLanes,t))),l===a)break;l=l.return}l!==a&&v("Expected to find the propagation root when scheduling context work. This error is likely caused by a bug in React. Please file an issue.")}function UN(e,t,a){BN(e,t,a)}function BN(e,t,a){var l=e.child;for(l!==null&&(l.return=e);l!==null;){var c=void 0,p=l.dependencies;if(p!==null){c=l.child;for(var m=p.firstContext;m!==null;){if(m.context===t){if(l.tag===O){var w=hr(a),C=as(rn,w);C.tag=ey;var R=l.updateQueue;if(R!==null){var M=R.shared,U=M.pending;U===null?C.next=C:(C.next=U.next,U.next=C),M.pending=C}}l.lanes=xt(l.lanes,a);var F=l.alternate;F!==null&&(F.lanes=xt(F.lanes,a)),_b(l.return,a,e),p.lanes=xt(p.lanes,a);break}m=m.next}}else if(l.tag===ue)c=l.type===e.type?null:l.child;else if(l.tag===Tt){var q=l.return;if(q===null)throw new Error("We just came from a parent so we must have had a parent. This is a bug in React.");q.lanes=xt(q.lanes,a);var Z=q.alternate;Z!==null&&(Z.lanes=xt(Z.lanes,a)),_b(q,a,e),c=l.sibling}else c=l.child;if(c!==null)c.return=l;else for(c=l;c!==null;){if(c===e){c=null;break}var te=c.sibling;if(te!==null){te.return=c.return,c=te;break}c=c.return}l=c}}function If(e,t){qv=e,Ff=null,Ab=null;var a=e.dependencies;if(a!==null){var l=a.firstContext;l!==null&&(vi(a.lanes,t)&&ig(),a.firstContext=null)}}function mr(e){Xv&&v("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");var t=e._currentValue;if(Ab!==e){var a={context:e,memoizedValue:t,next:null};if(Ff===null){if(qv===null)throw new Error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");Ff=a,qv.dependencies={lanes:ie,firstContext:a}}else Ff=Ff.next=a}return t}var Uc=null;function Lb(e){Uc===null?Uc=[e]:Uc.push(e)}function HN(){if(Uc!==null){for(var e=0;e<Uc.length;e++){var t=Uc[e],a=t.interleaved;if(a!==null){t.interleaved=null;var l=a.next,c=t.pending;if(c!==null){var p=c.next;c.next=l,a.next=p}t.pending=a}}Uc=null}}function mT(e,t,a,l){var c=t.interleaved;return c===null?(a.next=a,Lb(t)):(a.next=c.next,c.next=a),t.interleaved=a,Jv(e,l)}function VN(e,t,a,l){var c=t.interleaved;c===null?(a.next=a,Lb(t)):(a.next=c.next,c.next=a),t.interleaved=a}function WN(e,t,a,l){var c=t.interleaved;return c===null?(a.next=a,Lb(t)):(a.next=c.next,c.next=a),t.interleaved=a,Jv(e,l)}function aa(e,t){return Jv(e,t)}var YN=Jv;function Jv(e,t){e.lanes=xt(e.lanes,t);var a=e.alternate;a!==null&&(a.lanes=xt(a.lanes,t)),a===null&&(e.flags&(_n|Ln))!==Ye&&fR(e);for(var l=e,c=e.return;c!==null;)c.childLanes=xt(c.childLanes,t),a=c.alternate,a!==null?a.childLanes=xt(a.childLanes,t):(c.flags&(_n|Ln))!==Ye&&fR(e),l=c,c=c.return;if(l.tag===_){var p=l.stateNode;return p}else return null}var vT=0,yT=1,ey=2,zb=3,ty=!1,Nb,ny;Nb=!1,ny=null;function Pb(e){var t={baseState:e.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:ie},effects:null};e.updateQueue=t}function xT(e,t){var a=t.updateQueue,l=e.updateQueue;if(a===l){var c={baseState:l.baseState,firstBaseUpdate:l.firstBaseUpdate,lastBaseUpdate:l.lastBaseUpdate,shared:l.shared,effects:l.effects};t.updateQueue=c}}function as(e,t){var a={eventTime:e,lane:t,tag:vT,payload:null,callback:null,next:null};return a}function uu(e,t,a){var l=e.updateQueue;if(l===null)return null;var c=l.shared;if(ny===c&&!Nb&&(v("An update (setState, replaceState, or forceUpdate) was scheduled from inside an update function. Update functions should be pure, with zero side-effects. Consider using componentDidUpdate or a callback."),Nb=!0),VP()){var p=c.pending;return p===null?t.next=t:(t.next=p.next,p.next=t),c.pending=t,YN(e,a)}else return WN(e,c,t,a)}function ry(e,t,a){var l=t.updateQueue;if(l!==null){var c=l.shared;if(rh(a)){var p=c.lanes;p=af(p,e.pendingLanes);var m=xt(p,a);c.lanes=m,vc(e,m)}}}function Fb(e,t){var a=e.updateQueue,l=e.alternate;if(l!==null){var c=l.updateQueue;if(a===c){var p=null,m=null,w=a.firstBaseUpdate;if(w!==null){var C=w;do{var R={eventTime:C.eventTime,lane:C.lane,tag:C.tag,payload:C.payload,callback:C.callback,next:null};m===null?p=m=R:(m.next=R,m=R),C=C.next}while(C!==null);m===null?p=m=t:(m.next=t,m=t)}else p=m=t;a={baseState:c.baseState,firstBaseUpdate:p,lastBaseUpdate:m,shared:c.shared,effects:c.effects},e.updateQueue=a;return}}var M=a.lastBaseUpdate;M===null?a.firstBaseUpdate=t:M.next=t,a.lastBaseUpdate=t}function GN(e,t,a,l,c,p){switch(a.tag){case yT:{var m=a.payload;if(typeof m=="function"){pT();var w=m.call(p,l,c);{if(e.mode&mt){nn(!0);try{m.call(p,l,c)}finally{nn(!1)}}hT()}return w}return m}case zb:e.flags=e.flags&~Pr|Et;case vT:{var C=a.payload,R;if(typeof C=="function"){pT(),R=C.call(p,l,c);{if(e.mode&mt){nn(!0);try{C.call(p,l,c)}finally{nn(!1)}}hT()}}else R=C;return R==null?l:gt({},l,R)}case ey:return ty=!0,l}return l}function iy(e,t,a,l){var c=e.updateQueue;ty=!1,ny=c.shared;var p=c.firstBaseUpdate,m=c.lastBaseUpdate,w=c.shared.pending;if(w!==null){c.shared.pending=null;var C=w,R=C.next;C.next=null,m===null?p=R:m.next=R,m=C;var M=e.alternate;if(M!==null){var U=M.updateQueue,F=U.lastBaseUpdate;F!==m&&(F===null?U.firstBaseUpdate=R:F.next=R,U.lastBaseUpdate=C)}}if(p!==null){var q=c.baseState,Z=ie,te=null,ke=null,Xe=null,We=p;do{var It=We.lane,_t=We.eventTime;if(Hl(l,It)){if(Xe!==null){var ne={eventTime:_t,lane:Xn,tag:We.tag,payload:We.payload,callback:We.callback,next:null};Xe=Xe.next=ne}q=GN(e,c,We,q,t,a);var G=We.callback;if(G!==null&&We.lane!==Xn){e.flags|=gn;var me=c.effects;me===null?c.effects=[We]:me.push(We)}}else{var Y={eventTime:_t,lane:It,tag:We.tag,payload:We.payload,callback:We.callback,next:null};Xe===null?(ke=Xe=Y,te=q):Xe=Xe.next=Y,Z=xt(Z,It)}if(We=We.next,We===null){if(w=c.shared.pending,w===null)break;var _e=w,Me=_e.next;_e.next=null,We=Me,c.lastBaseUpdate=_e,c.shared.pending=null}}while(!0);Xe===null&&(te=q),c.baseState=te,c.firstBaseUpdate=ke,c.lastBaseUpdate=Xe;var ot=c.shared.interleaved;if(ot!==null){var dt=ot;do Z=xt(Z,dt.lane),dt=dt.next;while(dt!==ot)}else p===null&&(c.shared.lanes=ie);mg(Z),e.lanes=Z,e.memoizedState=q}ny=null}function KN(e,t){if(typeof e!="function")throw new Error("Invalid argument passed as callback. Expected a function. Instead "+("received: "+e));e.call(t)}function bT(){ty=!1}function ay(){return ty}function wT(e,t,a){var l=t.effects;if(t.effects=null,l!==null)for(var c=0;c<l.length;c++){var p=l[c],m=p.callback;m!==null&&(p.callback=null,KN(m,a))}}var Vh={},cu=au(Vh),Wh=au(Vh),oy=au(Vh);function ly(e){if(e===Vh)throw new Error("Expected host context to exist. This error is likely caused by a bug in React. Please file an issue.");return e}function ST(){var e=ly(oy.current);return e}function Ib(e,t){wi(oy,t,e),wi(Wh,e,e),wi(cu,Vh,e);var a=dz(t);bi(cu,e),wi(cu,a,e)}function Uf(e){bi(cu,e),bi(Wh,e),bi(oy,e)}function Ub(){var e=ly(cu.current);return e}function CT(e){ly(oy.current);var t=ly(cu.current),a=fz(t,e.type);t!==a&&(wi(Wh,e,e),wi(cu,a,e))}function Bb(e){Wh.current===e&&(bi(cu,e),bi(Wh,e))}var QN=0,ET=1,TT=1,Yh=2,To=au(QN);function Hb(e,t){return(e&t)!==0}function Bf(e){return e&ET}function Vb(e,t){return e&ET|t}function qN(e,t){return e|t}function du(e,t){wi(To,t,e)}function Hf(e){bi(To,e)}function XN(e,t){var a=e.memoizedState;return a!==null?a.dehydrated!==null:(e.memoizedProps,!0)}function sy(e){for(var t=e;t!==null;){if(t.tag===se){var a=t.memoizedState;if(a!==null){var l=a.dehydrated;if(l===null||BE(l)||sb(l))return t}}else if(t.tag===bt&&t.memoizedProps.revealOrder!==void 0){var c=(t.flags&Et)!==Ye;if(c)return t}else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)return null;for(;t.sibling===null;){if(t.return===null||t.return===e)return null;t=t.return}t.sibling.return=t.return,t=t.sibling}return null}var oa=0,kr=1,ul=2,Rr=4,Zr=8,Wb=[];function Yb(){for(var e=0;e<Wb.length;e++){var t=Wb[e];t._workInProgressVersionPrimary=null}Wb.length=0}function ZN(e,t){var a=t._getVersion,l=a(t._source);e.mutableSourceEagerHydrationData==null?e.mutableSourceEagerHydrationData=[t,l]:e.mutableSourceEagerHydrationData.push(t,l)}var je=d.ReactCurrentDispatcher,Gh=d.ReactCurrentBatchConfig,Gb,Vf;Gb=new Set;var Bc=ie,dn=null,Dr=null,Mr=null,uy=!1,Kh=!1,Qh=0,JN=0,e3=25,ae=null,Qa=null,fu=-1,Kb=!1;function Xt(){{var e=ae;Qa===null?Qa=[e]:Qa.push(e)}}function Se(){{var e=ae;Qa!==null&&(fu++,Qa[fu]!==e&&t3(e))}}function Wf(e){e!=null&&!yt(e)&&v("%s received a final argument that is not an array (instead, received `%s`). When specified, the final argument must be an array.",ae,typeof e)}function t3(e){{var t=lt(dn);if(!Gb.has(t)&&(Gb.add(t),Qa!==null)){for(var a="",l=30,c=0;c<=fu;c++){for(var p=Qa[c],m=c===fu?e:p,w=c+1+". "+p;w.length<l;)w+=" ";w+=m+`
`,a+=w}v(`React has detected a change in the order of Hooks called by %s. This will lead to bugs and errors if not fixed. For more information, read the Rules of Hooks: https://reactjs.org/link/rules-of-hooks

   Previous render            Next render
   ------------------------------------------------------
%s   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
`,t,a)}}}function Si(){throw new Error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://reactjs.org/link/invalid-hook-call for tips about how to debug and fix this problem.`)}function Qb(e,t){if(Kb)return!1;if(t===null)return v("%s received a final argument during this render, but not during the previous render. Even though the final argument is optional, its type cannot change between renders.",ae),!1;e.length!==t.length&&v(`The final argument passed to %s changed size between renders. The order and size of this array must remain constant.

Previous: %s
Incoming: %s`,ae,"["+t.join(", ")+"]","["+e.join(", ")+"]");for(var a=0;a<t.length&&a<e.length;a++)if(!De(e[a],t[a]))return!1;return!0}function Yf(e,t,a,l,c,p){Bc=p,dn=t,Qa=e!==null?e._debugHookTypes:null,fu=-1,Kb=e!==null&&e.type!==t.type,t.memoizedState=null,t.updateQueue=null,t.lanes=ie,e!==null&&e.memoizedState!==null?je.current=GT:Qa!==null?je.current=YT:je.current=WT;var m=a(l,c);if(Kh){var w=0;do{if(Kh=!1,Qh=0,w>=e3)throw new Error("Too many re-renders. React limits the number of renders to prevent an infinite loop.");w+=1,Kb=!1,Dr=null,Mr=null,t.updateQueue=null,fu=-1,je.current=KT,m=a(l,c)}while(Kh)}je.current=Sy,t._debugHookTypes=Qa;var C=Dr!==null&&Dr.next!==null;if(Bc=ie,dn=null,Dr=null,Mr=null,ae=null,Qa=null,fu=-1,e!==null&&(e.flags&qn)!==(t.flags&qn)&&(e.mode&Dt)!==Ge&&v("Internal React error: Expected static flag was missing. Please notify the React team."),uy=!1,C)throw new Error("Rendered fewer hooks than expected. This may be caused by an accidental early return statement.");return m}function Gf(){var e=Qh!==0;return Qh=0,e}function kT(e,t,a){t.updateQueue=e.updateQueue,(t.mode&cn)!==Ge?t.flags&=-50333701:t.flags&=-2053,e.lanes=mc(e.lanes,a)}function RT(){if(je.current=Sy,uy){for(var e=dn.memoizedState;e!==null;){var t=e.queue;t!==null&&(t.pending=null),e=e.next}uy=!1}Bc=ie,dn=null,Dr=null,Mr=null,Qa=null,fu=-1,ae=null,IT=!1,Kh=!1,Qh=0}function cl(){var e={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return Mr===null?dn.memoizedState=Mr=e:Mr=Mr.next=e,Mr}function qa(){var e;if(Dr===null){var t=dn.alternate;t!==null?e=t.memoizedState:e=null}else e=Dr.next;var a;if(Mr===null?a=dn.memoizedState:a=Mr.next,a!==null)Mr=a,a=Mr.next,Dr=e;else{if(e===null)throw new Error("Rendered more hooks than during the previous render.");Dr=e;var l={memoizedState:Dr.memoizedState,baseState:Dr.baseState,baseQueue:Dr.baseQueue,queue:Dr.queue,next:null};Mr===null?dn.memoizedState=Mr=l:Mr=Mr.next=l}return Mr}function DT(){return{lastEffect:null,stores:null}}function qb(e,t){return typeof t=="function"?t(e):t}function Xb(e,t,a){var l=cl(),c;a!==void 0?c=a(t):c=t,l.memoizedState=l.baseState=c;var p={pending:null,interleaved:null,lanes:ie,dispatch:null,lastRenderedReducer:e,lastRenderedState:c};l.queue=p;var m=p.dispatch=a3.bind(null,dn,p);return[l.memoizedState,m]}function Zb(e,t,a){var l=qa(),c=l.queue;if(c===null)throw new Error("Should have a queue. This is likely a bug in React. Please file an issue.");c.lastRenderedReducer=e;var p=Dr,m=p.baseQueue,w=c.pending;if(w!==null){if(m!==null){var C=m.next,R=w.next;m.next=R,w.next=C}p.baseQueue!==m&&v("Internal error: Expected work-in-progress queue to be a clone. This is a bug in React."),p.baseQueue=m=w,c.pending=null}if(m!==null){var M=m.next,U=p.baseState,F=null,q=null,Z=null,te=M;do{var ke=te.lane;if(Hl(Bc,ke)){if(Z!==null){var We={lane:Xn,action:te.action,hasEagerState:te.hasEagerState,eagerState:te.eagerState,next:null};Z=Z.next=We}if(te.hasEagerState)U=te.eagerState;else{var It=te.action;U=e(U,It)}}else{var Xe={lane:ke,action:te.action,hasEagerState:te.hasEagerState,eagerState:te.eagerState,next:null};Z===null?(q=Z=Xe,F=U):Z=Z.next=Xe,dn.lanes=xt(dn.lanes,ke),mg(ke)}te=te.next}while(te!==null&&te!==M);Z===null?F=U:Z.next=q,De(U,l.memoizedState)||ig(),l.memoizedState=U,l.baseState=F,l.baseQueue=Z,c.lastRenderedState=U}var _t=c.interleaved;if(_t!==null){var Y=_t;do{var ne=Y.lane;dn.lanes=xt(dn.lanes,ne),mg(ne),Y=Y.next}while(Y!==_t)}else m===null&&(c.lanes=ie);var G=c.dispatch;return[l.memoizedState,G]}function Jb(e,t,a){var l=qa(),c=l.queue;if(c===null)throw new Error("Should have a queue. This is likely a bug in React. Please file an issue.");c.lastRenderedReducer=e;var p=c.dispatch,m=c.pending,w=l.memoizedState;if(m!==null){c.pending=null;var C=m.next,R=C;do{var M=R.action;w=e(w,M),R=R.next}while(R!==C);De(w,l.memoizedState)||ig(),l.memoizedState=w,l.baseQueue===null&&(l.baseState=w),c.lastRenderedState=w}return[w,p]}function h4(e,t,a){}function g4(e,t,a){}function tw(e,t,a){var l=dn,c=cl(),p,m=Xr();if(m){if(a===void 0)throw new Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");p=a(),Vf||p!==a()&&(v("The result of getServerSnapshot should be cached to avoid an infinite loop"),Vf=!0)}else{if(p=t(),!Vf){var w=t();De(p,w)||(v("The result of getSnapshot should be cached to avoid an infinite loop"),Vf=!0)}var C=Uy();if(C===null)throw new Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");gc(C,Bc)||MT(l,t,p)}c.memoizedState=p;var R={value:p,getSnapshot:t};return c.queue=R,hy($T.bind(null,l,R,e),[e]),l.flags|=Oi,qh(kr|Zr,OT.bind(null,l,R,p,t),void 0,null),p}function cy(e,t,a){var l=dn,c=qa(),p=t();if(!Vf){var m=t();De(p,m)||(v("The result of getSnapshot should be cached to avoid an infinite loop"),Vf=!0)}var w=c.memoizedState,C=!De(w,p);C&&(c.memoizedState=p,ig());var R=c.queue;if(Zh($T.bind(null,l,R,e),[e]),R.getSnapshot!==t||C||Mr!==null&&Mr.memoizedState.tag&kr){l.flags|=Oi,qh(kr|Zr,OT.bind(null,l,R,p,t),void 0,null);var M=Uy();if(M===null)throw new Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");gc(M,Bc)||MT(l,t,p)}return p}function MT(e,t,a){e.flags|=Ad;var l={getSnapshot:t,value:a},c=dn.updateQueue;if(c===null)c=DT(),dn.updateQueue=c,c.stores=[l];else{var p=c.stores;p===null?c.stores=[l]:p.push(l)}}function OT(e,t,a,l){t.value=a,t.getSnapshot=l,AT(t)&&jT(e)}function $T(e,t,a){var l=function(){AT(t)&&jT(e)};return a(l)}function AT(e){var t=e.getSnapshot,a=e.value;try{var l=t();return!De(a,l)}catch{return!0}}function jT(e){var t=aa(e,nt);t!==null&&jr(t,e,nt,rn)}function dy(e){var t=cl();typeof e=="function"&&(e=e()),t.memoizedState=t.baseState=e;var a={pending:null,interleaved:null,lanes:ie,dispatch:null,lastRenderedReducer:qb,lastRenderedState:e};t.queue=a;var l=a.dispatch=o3.bind(null,dn,a);return[t.memoizedState,l]}function nw(e){return Zb(qb)}function rw(e){return Jb(qb)}function qh(e,t,a,l){var c={tag:e,create:t,destroy:a,deps:l,next:null},p=dn.updateQueue;if(p===null)p=DT(),dn.updateQueue=p,p.lastEffect=c.next=c;else{var m=p.lastEffect;if(m===null)p.lastEffect=c.next=c;else{var w=m.next;m.next=c,c.next=w,p.lastEffect=c}}return c}function iw(e){var t=cl();{var a={current:e};return t.memoizedState=a,a}}function fy(e){var t=qa();return t.memoizedState}function Xh(e,t,a,l){var c=cl(),p=l===void 0?null:l;dn.flags|=e,c.memoizedState=qh(kr|t,a,void 0,p)}function py(e,t,a,l){var c=qa(),p=l===void 0?null:l,m=void 0;if(Dr!==null){var w=Dr.memoizedState;if(m=w.destroy,p!==null){var C=w.deps;if(Qb(p,C)){c.memoizedState=qh(t,a,m,p);return}}}dn.flags|=e,c.memoizedState=qh(kr|t,a,m,p)}function hy(e,t){return(dn.mode&cn)!==Ge?Xh(Ho|Oi|Np,Zr,e,t):Xh(Oi|Np,Zr,e,t)}function Zh(e,t){return py(Oi,Zr,e,t)}function aw(e,t){return Xh(At,ul,e,t)}function gy(e,t){return py(At,ul,e,t)}function ow(e,t){var a=At;return a|=Bo,(dn.mode&cn)!==Ge&&(a|=Gr),Xh(a,Rr,e,t)}function my(e,t){return py(At,Rr,e,t)}function _T(e,t){if(typeof t=="function"){var a=t,l=e();return a(l),function(){a(null)}}else if(t!=null){var c=t;c.hasOwnProperty("current")||v("Expected useImperativeHandle() first argument to either be a ref callback or React.createRef() object. Instead received: %s.","an object with keys {"+Object.keys(c).join(", ")+"}");var p=e();return c.current=p,function(){c.current=null}}}function lw(e,t,a){typeof t!="function"&&v("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null");var l=a!=null?a.concat([e]):null,c=At;return c|=Bo,(dn.mode&cn)!==Ge&&(c|=Gr),Xh(c,Rr,_T.bind(null,t,e),l)}function vy(e,t,a){typeof t!="function"&&v("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null");var l=a!=null?a.concat([e]):null;return py(At,Rr,_T.bind(null,t,e),l)}function n3(e,t){}var yy=n3;function sw(e,t){var a=cl(),l=t===void 0?null:t;return a.memoizedState=[e,l],e}function xy(e,t){var a=qa(),l=t===void 0?null:t,c=a.memoizedState;if(c!==null&&l!==null){var p=c[1];if(Qb(l,p))return c[0]}return a.memoizedState=[e,l],e}function uw(e,t){var a=cl(),l=t===void 0?null:t,c=e();return a.memoizedState=[c,l],c}function by(e,t){var a=qa(),l=t===void 0?null:t,c=a.memoizedState;if(c!==null&&l!==null){var p=c[1];if(Qb(l,p))return c[0]}var m=e();return a.memoizedState=[m,l],m}function cw(e){var t=cl();return t.memoizedState=e,e}function LT(e){var t=qa(),a=Dr,l=a.memoizedState;return NT(t,l,e)}function zT(e){var t=qa();if(Dr===null)return t.memoizedState=e,e;var a=Dr.memoizedState;return NT(t,a,e)}function NT(e,t,a){var l=!nh(Bc);if(l){if(!De(a,t)){var c=ih();dn.lanes=xt(dn.lanes,c),mg(c),e.baseState=!0}return t}else return e.baseState&&(e.baseState=!1,ig()),e.memoizedState=a,a}function r3(e,t,a){var l=Ai();rr(yc(l,ea)),e(!0);var c=Gh.transition;Gh.transition={};var p=Gh.transition;Gh.transition._updatedFibers=new Set;try{e(!1),t()}finally{if(rr(l),Gh.transition=c,c===null&&p._updatedFibers){var m=p._updatedFibers.size;m>10&&S("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."),p._updatedFibers.clear()}}}function dw(){var e=dy(!1),t=e[0],a=e[1],l=r3.bind(null,a),c=cl();return c.memoizedState=l,[t,l]}function PT(){var e=nw(),t=e[0],a=qa(),l=a.memoizedState;return[t,l]}function FT(){var e=rw(),t=e[0],a=qa(),l=a.memoizedState;return[t,l]}var IT=!1;function i3(){return IT}function fw(){var e=cl(),t=Uy(),a=t.identifierPrefix,l;if(Xr()){var c=wN();l=":"+a+"R"+c;var p=Qh++;p>0&&(l+="H"+p.toString(32)),l+=":"}else{var m=JN++;l=":"+a+"r"+m.toString(32)+":"}return e.memoizedState=l,l}function wy(){var e=qa(),t=e.memoizedState;return t}function a3(e,t,a){typeof arguments[3]=="function"&&v("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect().");var l=vu(e),c={lane:l,action:a,hasEagerState:!1,eagerState:null,next:null};if(UT(e))BT(t,c);else{var p=mT(e,t,c,l);if(p!==null){var m=Pi();jr(p,e,l,m),HT(p,t,l)}}VT(e,l)}function o3(e,t,a){typeof arguments[3]=="function"&&v("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect().");var l=vu(e),c={lane:l,action:a,hasEagerState:!1,eagerState:null,next:null};if(UT(e))BT(t,c);else{var p=e.alternate;if(e.lanes===ie&&(p===null||p.lanes===ie)){var m=t.lastRenderedReducer;if(m!==null){var w;w=je.current,je.current=ko;try{var C=t.lastRenderedState,R=m(C,a);if(c.hasEagerState=!0,c.eagerState=R,De(R,C)){VN(e,t,c,l);return}}catch{}finally{je.current=w}}}var M=mT(e,t,c,l);if(M!==null){var U=Pi();jr(M,e,l,U),HT(M,t,l)}}VT(e,l)}function UT(e){var t=e.alternate;return e===dn||t!==null&&t===dn}function BT(e,t){Kh=uy=!0;var a=e.pending;a===null?t.next=t:(t.next=a.next,a.next=t),e.pending=t}function HT(e,t,a){if(rh(a)){var l=t.lanes;l=af(l,e.pendingLanes);var c=xt(l,a);t.lanes=c,vc(e,c)}}function VT(e,t,a){lc(e,t)}var Sy={readContext:mr,useCallback:Si,useContext:Si,useEffect:Si,useImperativeHandle:Si,useInsertionEffect:Si,useLayoutEffect:Si,useMemo:Si,useReducer:Si,useRef:Si,useState:Si,useDebugValue:Si,useDeferredValue:Si,useTransition:Si,useMutableSource:Si,useSyncExternalStore:Si,useId:Si,unstable_isNewReconciler:Te},WT=null,YT=null,GT=null,KT=null,dl=null,ko=null,Cy=null;{var pw=function(){v("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().")},ct=function(){v("Do not call Hooks inside useEffect(...), useMemo(...), or other built-in Hooks. You can only call Hooks at the top level of your React function. For more information, see https://reactjs.org/link/rules-of-hooks")};WT={readContext:function(e){return mr(e)},useCallback:function(e,t){return ae="useCallback",Xt(),Wf(t),sw(e,t)},useContext:function(e){return ae="useContext",Xt(),mr(e)},useEffect:function(e,t){return ae="useEffect",Xt(),Wf(t),hy(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",Xt(),Wf(a),lw(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",Xt(),Wf(t),aw(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",Xt(),Wf(t),ow(e,t)},useMemo:function(e,t){ae="useMemo",Xt(),Wf(t);var a=je.current;je.current=dl;try{return uw(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",Xt();var l=je.current;je.current=dl;try{return Xb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",Xt(),iw(e)},useState:function(e){ae="useState",Xt();var t=je.current;je.current=dl;try{return dy(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",Xt(),void 0},useDeferredValue:function(e){return ae="useDeferredValue",Xt(),cw(e)},useTransition:function(){return ae="useTransition",Xt(),dw()},useMutableSource:function(e,t,a){return ae="useMutableSource",Xt(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",Xt(),tw(e,t,a)},useId:function(){return ae="useId",Xt(),fw()},unstable_isNewReconciler:Te},YT={readContext:function(e){return mr(e)},useCallback:function(e,t){return ae="useCallback",Se(),sw(e,t)},useContext:function(e){return ae="useContext",Se(),mr(e)},useEffect:function(e,t){return ae="useEffect",Se(),hy(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",Se(),lw(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",Se(),aw(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",Se(),ow(e,t)},useMemo:function(e,t){ae="useMemo",Se();var a=je.current;je.current=dl;try{return uw(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",Se();var l=je.current;je.current=dl;try{return Xb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",Se(),iw(e)},useState:function(e){ae="useState",Se();var t=je.current;je.current=dl;try{return dy(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",Se(),void 0},useDeferredValue:function(e){return ae="useDeferredValue",Se(),cw(e)},useTransition:function(){return ae="useTransition",Se(),dw()},useMutableSource:function(e,t,a){return ae="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",Se(),tw(e,t,a)},useId:function(){return ae="useId",Se(),fw()},unstable_isNewReconciler:Te},GT={readContext:function(e){return mr(e)},useCallback:function(e,t){return ae="useCallback",Se(),xy(e,t)},useContext:function(e){return ae="useContext",Se(),mr(e)},useEffect:function(e,t){return ae="useEffect",Se(),Zh(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",Se(),vy(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",Se(),gy(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",Se(),my(e,t)},useMemo:function(e,t){ae="useMemo",Se();var a=je.current;je.current=ko;try{return by(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",Se();var l=je.current;je.current=ko;try{return Zb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",Se(),fy()},useState:function(e){ae="useState",Se();var t=je.current;je.current=ko;try{return nw(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",Se(),yy()},useDeferredValue:function(e){return ae="useDeferredValue",Se(),LT(e)},useTransition:function(){return ae="useTransition",Se(),PT()},useMutableSource:function(e,t,a){return ae="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",Se(),cy(e,t)},useId:function(){return ae="useId",Se(),wy()},unstable_isNewReconciler:Te},KT={readContext:function(e){return mr(e)},useCallback:function(e,t){return ae="useCallback",Se(),xy(e,t)},useContext:function(e){return ae="useContext",Se(),mr(e)},useEffect:function(e,t){return ae="useEffect",Se(),Zh(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",Se(),vy(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",Se(),gy(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",Se(),my(e,t)},useMemo:function(e,t){ae="useMemo",Se();var a=je.current;je.current=Cy;try{return by(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",Se();var l=je.current;je.current=Cy;try{return Jb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",Se(),fy()},useState:function(e){ae="useState",Se();var t=je.current;je.current=Cy;try{return rw(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",Se(),yy()},useDeferredValue:function(e){return ae="useDeferredValue",Se(),zT(e)},useTransition:function(){return ae="useTransition",Se(),FT()},useMutableSource:function(e,t,a){return ae="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",Se(),cy(e,t)},useId:function(){return ae="useId",Se(),wy()},unstable_isNewReconciler:Te},dl={readContext:function(e){return pw(),mr(e)},useCallback:function(e,t){return ae="useCallback",ct(),Xt(),sw(e,t)},useContext:function(e){return ae="useContext",ct(),Xt(),mr(e)},useEffect:function(e,t){return ae="useEffect",ct(),Xt(),hy(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",ct(),Xt(),lw(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",ct(),Xt(),aw(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",ct(),Xt(),ow(e,t)},useMemo:function(e,t){ae="useMemo",ct(),Xt();var a=je.current;je.current=dl;try{return uw(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",ct(),Xt();var l=je.current;je.current=dl;try{return Xb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",ct(),Xt(),iw(e)},useState:function(e){ae="useState",ct(),Xt();var t=je.current;je.current=dl;try{return dy(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",ct(),Xt(),void 0},useDeferredValue:function(e){return ae="useDeferredValue",ct(),Xt(),cw(e)},useTransition:function(){return ae="useTransition",ct(),Xt(),dw()},useMutableSource:function(e,t,a){return ae="useMutableSource",ct(),Xt(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",ct(),Xt(),tw(e,t,a)},useId:function(){return ae="useId",ct(),Xt(),fw()},unstable_isNewReconciler:Te},ko={readContext:function(e){return pw(),mr(e)},useCallback:function(e,t){return ae="useCallback",ct(),Se(),xy(e,t)},useContext:function(e){return ae="useContext",ct(),Se(),mr(e)},useEffect:function(e,t){return ae="useEffect",ct(),Se(),Zh(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",ct(),Se(),vy(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",ct(),Se(),gy(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",ct(),Se(),my(e,t)},useMemo:function(e,t){ae="useMemo",ct(),Se();var a=je.current;je.current=ko;try{return by(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",ct(),Se();var l=je.current;je.current=ko;try{return Zb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",ct(),Se(),fy()},useState:function(e){ae="useState",ct(),Se();var t=je.current;je.current=ko;try{return nw(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",ct(),Se(),yy()},useDeferredValue:function(e){return ae="useDeferredValue",ct(),Se(),LT(e)},useTransition:function(){return ae="useTransition",ct(),Se(),PT()},useMutableSource:function(e,t,a){return ae="useMutableSource",ct(),Se(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",ct(),Se(),cy(e,t)},useId:function(){return ae="useId",ct(),Se(),wy()},unstable_isNewReconciler:Te},Cy={readContext:function(e){return pw(),mr(e)},useCallback:function(e,t){return ae="useCallback",ct(),Se(),xy(e,t)},useContext:function(e){return ae="useContext",ct(),Se(),mr(e)},useEffect:function(e,t){return ae="useEffect",ct(),Se(),Zh(e,t)},useImperativeHandle:function(e,t,a){return ae="useImperativeHandle",ct(),Se(),vy(e,t,a)},useInsertionEffect:function(e,t){return ae="useInsertionEffect",ct(),Se(),gy(e,t)},useLayoutEffect:function(e,t){return ae="useLayoutEffect",ct(),Se(),my(e,t)},useMemo:function(e,t){ae="useMemo",ct(),Se();var a=je.current;je.current=ko;try{return by(e,t)}finally{je.current=a}},useReducer:function(e,t,a){ae="useReducer",ct(),Se();var l=je.current;je.current=ko;try{return Jb(e,t,a)}finally{je.current=l}},useRef:function(e){return ae="useRef",ct(),Se(),fy()},useState:function(e){ae="useState",ct(),Se();var t=je.current;je.current=ko;try{return rw(e)}finally{je.current=t}},useDebugValue:function(e,t){return ae="useDebugValue",ct(),Se(),yy()},useDeferredValue:function(e){return ae="useDeferredValue",ct(),Se(),zT(e)},useTransition:function(){return ae="useTransition",ct(),Se(),FT()},useMutableSource:function(e,t,a){return ae="useMutableSource",ct(),Se(),void 0},useSyncExternalStore:function(e,t,a){return ae="useSyncExternalStore",ct(),Se(),cy(e,t)},useId:function(){return ae="useId",ct(),Se(),wy()},unstable_isNewReconciler:Te}}var pu=s.unstable_now,QT=0,Ey=-1,Jh=-1,Ty=-1,hw=!1,ky=!1;function qT(){return hw}function l3(){ky=!0}function s3(){hw=!1,ky=!1}function u3(){hw=ky,ky=!1}function XT(){return QT}function ZT(){QT=pu()}function gw(e){Jh=pu(),e.actualStartTime<0&&(e.actualStartTime=pu())}function JT(e){Jh=-1}function Ry(e,t){if(Jh>=0){var a=pu()-Jh;e.actualDuration+=a,t&&(e.selfBaseDuration=a),Jh=-1}}function fl(e){if(Ey>=0){var t=pu()-Ey;Ey=-1;for(var a=e.return;a!==null;){switch(a.tag){case _:var l=a.stateNode;l.effectDuration+=t;return;case Le:var c=a.stateNode;c.effectDuration+=t;return}a=a.return}}}function mw(e){if(Ty>=0){var t=pu()-Ty;Ty=-1;for(var a=e.return;a!==null;){switch(a.tag){case _:var l=a.stateNode;l!==null&&(l.passiveEffectDuration+=t);return;case Le:var c=a.stateNode;c!==null&&(c.passiveEffectDuration+=t);return}a=a.return}}}function pl(){Ey=pu()}function vw(){Ty=pu()}function yw(e){for(var t=e.child;t;)e.actualDuration+=t.actualDuration,t=t.sibling}function Ro(e,t){if(e&&e.defaultProps){var a=gt({},t),l=e.defaultProps;for(var c in l)a[c]===void 0&&(a[c]=l[c]);return a}return t}var xw={},bw,ww,Sw,Cw,Ew,ek,Dy,Tw,kw,Rw,eg;{bw=new Set,ww=new Set,Sw=new Set,Cw=new Set,Tw=new Set,Ew=new Set,kw=new Set,Rw=new Set,eg=new Set;var tk=new Set;Dy=function(e,t){if(!(e===null||typeof e=="function")){var a=t+"_"+e;tk.has(a)||(tk.add(a),v("%s(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",t,e))}},ek=function(e,t){if(t===void 0){var a=Pt(e)||"Component";Ew.has(a)||(Ew.add(a),v("%s.getDerivedStateFromProps(): A valid state object (or null) must be returned. You have returned undefined.",a))}},Object.defineProperty(xw,"_processChildContext",{enumerable:!1,value:function(){throw new Error("_processChildContext is not available in React 16+. This likely means you have multiple copies of React and are attempting to nest a React 15 tree inside a React 16 tree using unstable_renderSubtreeIntoContainer, which isn't supported. Try to make sure you have only one copy of React (and ideally, switch to ReactDOM.createPortal).")}}),Object.freeze(xw)}function Dw(e,t,a,l){var c=e.memoizedState,p=a(l,c);{if(e.mode&mt){nn(!0);try{p=a(l,c)}finally{nn(!1)}}ek(t,p)}var m=p==null?c:gt({},c,p);if(e.memoizedState=m,e.lanes===ie){var w=e.updateQueue;w.baseState=m}}var Mw={isMounted:Pp,enqueueSetState:function(e,t,a){var l=zs(e),c=Pi(),p=vu(l),m=as(c,p);m.payload=t,a!=null&&(Dy(a,"setState"),m.callback=a);var w=uu(l,m,p);w!==null&&(jr(w,l,p,c),ry(w,l,p)),lc(l,p)},enqueueReplaceState:function(e,t,a){var l=zs(e),c=Pi(),p=vu(l),m=as(c,p);m.tag=yT,m.payload=t,a!=null&&(Dy(a,"replaceState"),m.callback=a);var w=uu(l,m,p);w!==null&&(jr(w,l,p,c),ry(w,l,p)),lc(l,p)},enqueueForceUpdate:function(e,t){var a=zs(e),l=Pi(),c=vu(a),p=as(l,c);p.tag=ey,t!=null&&(Dy(t,"forceUpdate"),p.callback=t);var m=uu(a,p,c);m!==null&&(jr(m,a,c,l),ry(m,a,c)),Zp(a,c)}};function nk(e,t,a,l,c,p,m){var w=e.stateNode;if(typeof w.shouldComponentUpdate=="function"){var C=w.shouldComponentUpdate(l,p,m);{if(e.mode&mt){nn(!0);try{C=w.shouldComponentUpdate(l,p,m)}finally{nn(!1)}}C===void 0&&v("%s.shouldComponentUpdate(): Returned undefined instead of a boolean value. Make sure to return true or false.",Pt(t)||"Component")}return C}return t.prototype&&t.prototype.isPureReactComponent?!Ke(a,l)||!Ke(c,p):!0}function c3(e,t,a){var l=e.stateNode;{var c=Pt(t)||"Component",p=l.render;p||(t.prototype&&typeof t.prototype.render=="function"?v("%s(...): No `render` method found on the returned component instance: did you accidentally return an object from the constructor?",c):v("%s(...): No `render` method found on the returned component instance: you may have forgotten to define `render`.",c)),l.getInitialState&&!l.getInitialState.isReactClassApproved&&!l.state&&v("getInitialState was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Did you mean to define a state property instead?",c),l.getDefaultProps&&!l.getDefaultProps.isReactClassApproved&&v("getDefaultProps was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Use a static property to define defaultProps instead.",c),l.propTypes&&v("propTypes was defined as an instance property on %s. Use a static property to define propTypes instead.",c),l.contextType&&v("contextType was defined as an instance property on %s. Use a static property to define contextType instead.",c),t.childContextTypes&&!eg.has(t)&&(e.mode&mt)===Ge&&(eg.add(t),v(`%s uses the legacy childContextTypes API which is no longer supported and will be removed in the next major release. Use React.createContext() instead

.Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)),t.contextTypes&&!eg.has(t)&&(e.mode&mt)===Ge&&(eg.add(t),v(`%s uses the legacy contextTypes API which is no longer supported and will be removed in the next major release. Use React.createContext() with static contextType instead.

Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)),l.contextTypes&&v("contextTypes was defined as an instance property on %s. Use a static property to define contextTypes instead.",c),t.contextType&&t.contextTypes&&!kw.has(t)&&(kw.add(t),v("%s declares both contextTypes and contextType static properties. The legacy contextTypes property will be ignored.",c)),typeof l.componentShouldUpdate=="function"&&v("%s has a method called componentShouldUpdate(). Did you mean shouldComponentUpdate()? The name is phrased as a question because the function is expected to return a value.",c),t.prototype&&t.prototype.isPureReactComponent&&typeof l.shouldComponentUpdate<"u"&&v("%s has a method called shouldComponentUpdate(). shouldComponentUpdate should not be used when extending React.PureComponent. Please extend React.Component if shouldComponentUpdate is used.",Pt(t)||"A pure component"),typeof l.componentDidUnmount=="function"&&v("%s has a method called componentDidUnmount(). But there is no such lifecycle method. Did you mean componentWillUnmount()?",c),typeof l.componentDidReceiveProps=="function"&&v("%s has a method called componentDidReceiveProps(). But there is no such lifecycle method. If you meant to update the state in response to changing props, use componentWillReceiveProps(). If you meant to fetch data or run side-effects or mutations after React has updated the UI, use componentDidUpdate().",c),typeof l.componentWillRecieveProps=="function"&&v("%s has a method called componentWillRecieveProps(). Did you mean componentWillReceiveProps()?",c),typeof l.UNSAFE_componentWillRecieveProps=="function"&&v("%s has a method called UNSAFE_componentWillRecieveProps(). Did you mean UNSAFE_componentWillReceiveProps()?",c);var m=l.props!==a;l.props!==void 0&&m&&v("%s(...): When calling super() in `%s`, make sure to pass up the same props that your component's constructor was passed.",c,c),l.defaultProps&&v("Setting defaultProps as an instance property on %s is not supported and will be ignored. Instead, define defaultProps as a static property on %s.",c,c),typeof l.getSnapshotBeforeUpdate=="function"&&typeof l.componentDidUpdate!="function"&&!Sw.has(t)&&(Sw.add(t),v("%s: getSnapshotBeforeUpdate() should be used with componentDidUpdate(). This component defines getSnapshotBeforeUpdate() only.",Pt(t))),typeof l.getDerivedStateFromProps=="function"&&v("%s: getDerivedStateFromProps() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof l.getDerivedStateFromError=="function"&&v("%s: getDerivedStateFromError() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof t.getSnapshotBeforeUpdate=="function"&&v("%s: getSnapshotBeforeUpdate() is defined as a static method and will be ignored. Instead, declare it as an instance method.",c);var w=l.state;w&&(typeof w!="object"||yt(w))&&v("%s.state: must be set to an object or null",c),typeof l.getChildContext=="function"&&typeof t.childContextTypes!="object"&&v("%s.getChildContext(): childContextTypes must be defined in order to use getChildContext().",c)}}function rk(e,t){t.updater=Mw,e.stateNode=t,ec(t,e),t._reactInternalInstance=xw}function ik(e,t,a){var l=!1,c=wa,p=wa,m=t.contextType;if("contextType"in t){var w=m===null||m!==void 0&&m.$$typeof===L&&m._context===void 0;if(!w&&!Rw.has(t)){Rw.add(t);var C="";m===void 0?C=" However, it is set to undefined. This can be caused by a typo or by mixing up named and default imports. This can also happen due to a circular dependency, so try moving the createContext() call to a separate file.":typeof m!="object"?C=" However, it is set to a "+typeof m+".":m.$$typeof===ao?C=" Did you accidentally pass the Context.Provider instead?":m._context!==void 0?C=" Did you accidentally pass the Context.Consumer instead?":C=" However, it is set to an object with keys {"+Object.keys(m).join(", ")+"}.",v("%s defines an invalid contextType. contextType should point to the Context object returned by React.createContext().%s",Pt(t)||"Component",C)}}if(typeof m=="object"&&m!==null)p=mr(m);else{c=jf(e,t,!0);var R=t.contextTypes;l=R!=null,p=l?_f(e,c):wa}var M=new t(a,p);if(e.mode&mt){nn(!0);try{M=new t(a,p)}finally{nn(!1)}}var U=e.memoizedState=M.state!==null&&M.state!==void 0?M.state:null;rk(e,M);{if(typeof t.getDerivedStateFromProps=="function"&&U===null){var F=Pt(t)||"Component";ww.has(F)||(ww.add(F),v("`%s` uses `getDerivedStateFromProps` but its initial state is %s. This is not recommended. Instead, define the initial state by assigning an object to `this.state` in the constructor of `%s`. This ensures that `getDerivedStateFromProps` arguments have a consistent shape.",F,M.state===null?"null":"undefined",F))}if(typeof t.getDerivedStateFromProps=="function"||typeof M.getSnapshotBeforeUpdate=="function"){var q=null,Z=null,te=null;if(typeof M.componentWillMount=="function"&&M.componentWillMount.__suppressDeprecationWarning!==!0?q="componentWillMount":typeof M.UNSAFE_componentWillMount=="function"&&(q="UNSAFE_componentWillMount"),typeof M.componentWillReceiveProps=="function"&&M.componentWillReceiveProps.__suppressDeprecationWarning!==!0?Z="componentWillReceiveProps":typeof M.UNSAFE_componentWillReceiveProps=="function"&&(Z="UNSAFE_componentWillReceiveProps"),typeof M.componentWillUpdate=="function"&&M.componentWillUpdate.__suppressDeprecationWarning!==!0?te="componentWillUpdate":typeof M.UNSAFE_componentWillUpdate=="function"&&(te="UNSAFE_componentWillUpdate"),q!==null||Z!==null||te!==null){var ke=Pt(t)||"Component",Xe=typeof t.getDerivedStateFromProps=="function"?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";Cw.has(ke)||(Cw.add(ke),v(`Unsafe legacy lifecycles will not be called for components using new component APIs.

%s uses %s but also contains the following legacy lifecycles:%s%s%s

The above lifecycles should be removed. Learn more about this warning here:
https://reactjs.org/link/unsafe-component-lifecycles`,ke,Xe,q!==null?`
  `+q:"",Z!==null?`
  `+Z:"",te!==null?`
  `+te:""))}}}return l&&GE(e,c,p),M}function d3(e,t){var a=t.state;typeof t.componentWillMount=="function"&&t.componentWillMount(),typeof t.UNSAFE_componentWillMount=="function"&&t.UNSAFE_componentWillMount(),a!==t.state&&(v("%s.componentWillMount(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",lt(e)||"Component"),Mw.enqueueReplaceState(t,t.state,null))}function ak(e,t,a,l){var c=t.state;if(typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps(a,l),typeof t.UNSAFE_componentWillReceiveProps=="function"&&t.UNSAFE_componentWillReceiveProps(a,l),t.state!==c){{var p=lt(e)||"Component";bw.has(p)||(bw.add(p),v("%s.componentWillReceiveProps(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",p))}Mw.enqueueReplaceState(t,t.state,null)}}function Ow(e,t,a,l){c3(e,t,a);var c=e.stateNode;c.props=a,c.state=e.memoizedState,c.refs={},Pb(e);var p=t.contextType;if(typeof p=="object"&&p!==null)c.context=mr(p);else{var m=jf(e,t,!0);c.context=_f(e,m)}{if(c.state===a){var w=Pt(t)||"Component";Tw.has(w)||(Tw.add(w),v("%s: It is not recommended to assign props directly to state because updates to props won't be reflected in state. In most cases, it is better to use props directly.",w))}e.mode&mt&&Eo.recordLegacyContextWarning(e,c),Eo.recordUnsafeLifecycleWarnings(e,c)}c.state=e.memoizedState;var C=t.getDerivedStateFromProps;if(typeof C=="function"&&(Dw(e,t,C,a),c.state=e.memoizedState),typeof t.getDerivedStateFromProps!="function"&&typeof c.getSnapshotBeforeUpdate!="function"&&(typeof c.UNSAFE_componentWillMount=="function"||typeof c.componentWillMount=="function")&&(d3(e,c),iy(e,a,c,l),c.state=e.memoizedState),typeof c.componentDidMount=="function"){var R=At;R|=Bo,(e.mode&cn)!==Ge&&(R|=Gr),e.flags|=R}}function f3(e,t,a,l){var c=e.stateNode,p=e.memoizedProps;c.props=p;var m=c.context,w=t.contextType,C=wa;if(typeof w=="object"&&w!==null)C=mr(w);else{var R=jf(e,t,!0);C=_f(e,R)}var M=t.getDerivedStateFromProps,U=typeof M=="function"||typeof c.getSnapshotBeforeUpdate=="function";!U&&(typeof c.UNSAFE_componentWillReceiveProps=="function"||typeof c.componentWillReceiveProps=="function")&&(p!==a||m!==C)&&ak(e,c,a,C),bT();var F=e.memoizedState,q=c.state=F;if(iy(e,a,c,l),q=e.memoizedState,p===a&&F===q&&!Fv()&&!ay()){if(typeof c.componentDidMount=="function"){var Z=At;Z|=Bo,(e.mode&cn)!==Ge&&(Z|=Gr),e.flags|=Z}return!1}typeof M=="function"&&(Dw(e,t,M,a),q=e.memoizedState);var te=ay()||nk(e,t,p,a,F,q,C);if(te){if(!U&&(typeof c.UNSAFE_componentWillMount=="function"||typeof c.componentWillMount=="function")&&(typeof c.componentWillMount=="function"&&c.componentWillMount(),typeof c.UNSAFE_componentWillMount=="function"&&c.UNSAFE_componentWillMount()),typeof c.componentDidMount=="function"){var ke=At;ke|=Bo,(e.mode&cn)!==Ge&&(ke|=Gr),e.flags|=ke}}else{if(typeof c.componentDidMount=="function"){var Xe=At;Xe|=Bo,(e.mode&cn)!==Ge&&(Xe|=Gr),e.flags|=Xe}e.memoizedProps=a,e.memoizedState=q}return c.props=a,c.state=q,c.context=C,te}function p3(e,t,a,l,c){var p=t.stateNode;xT(e,t);var m=t.memoizedProps,w=t.type===t.elementType?m:Ro(t.type,m);p.props=w;var C=t.pendingProps,R=p.context,M=a.contextType,U=wa;if(typeof M=="object"&&M!==null)U=mr(M);else{var F=jf(t,a,!0);U=_f(t,F)}var q=a.getDerivedStateFromProps,Z=typeof q=="function"||typeof p.getSnapshotBeforeUpdate=="function";!Z&&(typeof p.UNSAFE_componentWillReceiveProps=="function"||typeof p.componentWillReceiveProps=="function")&&(m!==C||R!==U)&&ak(t,p,l,U),bT();var te=t.memoizedState,ke=p.state=te;if(iy(t,l,p,c),ke=t.memoizedState,m===C&&te===ke&&!Fv()&&!ay()&&!be)return typeof p.componentDidUpdate=="function"&&(m!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(m!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=Zi),!1;typeof q=="function"&&(Dw(t,a,q,l),ke=t.memoizedState);var Xe=ay()||nk(t,a,w,l,te,ke,U)||be;return Xe?(!Z&&(typeof p.UNSAFE_componentWillUpdate=="function"||typeof p.componentWillUpdate=="function")&&(typeof p.componentWillUpdate=="function"&&p.componentWillUpdate(l,ke,U),typeof p.UNSAFE_componentWillUpdate=="function"&&p.UNSAFE_componentWillUpdate(l,ke,U)),typeof p.componentDidUpdate=="function"&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(t.flags|=Zi)):(typeof p.componentDidUpdate=="function"&&(m!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(m!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=Zi),t.memoizedProps=l,t.memoizedState=ke),p.props=l,p.state=ke,p.context=U,Xe}function Hc(e,t){return{value:e,source:t,stack:Nt(t),digest:null}}function $w(e,t,a){return{value:e,source:null,stack:a??null,digest:t??null}}function h3(e,t){return!0}function Aw(e,t){try{var a=h3(e,t);if(a===!1)return;var l=t.value,c=t.source,p=t.stack,m=p!==null?p:"";if(l!=null&&l._suppressLogging){if(e.tag===O)return;console.error(l)}var w=c?lt(c):null,C=w?"The above error occurred in the <"+w+"> component:":"The above error occurred in one of your React components:",R;if(e.tag===_)R=`Consider adding an error boundary to your tree to customize error handling behavior.
Visit https://reactjs.org/link/error-boundaries to learn more about error boundaries.`;else{var M=lt(e)||"Anonymous";R="React will try to recreate this component tree from scratch "+("using the error boundary you provided, "+M+".")}var U=C+`
`+m+`

`+(""+R);console.error(U)}catch(F){setTimeout(function(){throw F})}}var g3=typeof WeakMap=="function"?WeakMap:Map;function ok(e,t,a){var l=as(rn,a);l.tag=zb,l.payload={element:null};var c=t.value;return l.callback=function(){l5(c),Aw(e,t)},l}function jw(e,t,a){var l=as(rn,a);l.tag=zb;var c=e.type.getDerivedStateFromError;if(typeof c=="function"){var p=t.value;l.payload=function(){return c(p)},l.callback=function(){vR(e),Aw(e,t)}}var m=e.stateNode;return m!==null&&typeof m.componentDidCatch=="function"&&(l.callback=function(){vR(e),Aw(e,t),typeof c!="function"&&a5(this);var C=t.value,R=t.stack;this.componentDidCatch(C,{componentStack:R!==null?R:""}),typeof c!="function"&&(vi(e.lanes,nt)||v("%s: Error boundaries should implement getDerivedStateFromError(). In that method, return a state update to display an error message or fallback UI.",lt(e)||"Unknown"))}),l}function lk(e,t,a){var l=e.pingCache,c;if(l===null?(l=e.pingCache=new g3,c=new Set,l.set(t,c)):(c=l.get(t),c===void 0&&(c=new Set,l.set(t,c))),!c.has(a)){c.add(a);var p=s5.bind(null,e,t,a);Fr&&vg(e,a),t.then(p,p)}}function m3(e,t,a,l){var c=e.updateQueue;if(c===null){var p=new Set;p.add(a),e.updateQueue=p}else c.add(a)}function v3(e,t){var a=e.tag;if((e.mode&Dt)===Ge&&(a===$||a===he||a===Oe)){var l=e.alternate;l?(e.updateQueue=l.updateQueue,e.memoizedState=l.memoizedState,e.lanes=l.lanes):(e.updateQueue=null,e.memoizedState=null)}}function sk(e){var t=e;do{if(t.tag===se&&XN(t))return t;t=t.return}while(t!==null);return null}function uk(e,t,a,l,c){if((e.mode&Dt)===Ge){if(e===t)e.flags|=Pr;else{if(e.flags|=Et,a.flags|=ma,a.flags&=-52805,a.tag===O){var p=a.alternate;if(p===null)a.tag=He;else{var m=as(rn,nt);m.tag=ey,uu(a,m,nt)}}a.lanes=xt(a.lanes,nt)}return e}return e.flags|=Pr,e.lanes=c,e}function y3(e,t,a,l,c){if(a.flags|=_l,Fr&&vg(e,c),l!==null&&typeof l=="object"&&typeof l.then=="function"){var p=l;v3(a),Xr()&&a.mode&Dt&&eT();var m=sk(t);if(m!==null){m.flags&=~Rn,uk(m,t,a,e,c),m.mode&Dt&&lk(e,p,c),m3(m,e,p);return}else{if(!th(c)){lk(e,p,c),fS();return}var w=new Error("A component suspended while responding to synchronous input. This will cause the UI to be replaced with a loading indicator. To fix, updates that suspend should be wrapped with startTransition.");l=w}}else if(Xr()&&a.mode&Dt){eT();var C=sk(t);if(C!==null){(C.flags&Pr)===Ye&&(C.flags|=Rn),uk(C,t,a,e,c),Eb(Hc(l,a));return}}l=Hc(l,a),XP(l);var R=t;do{switch(R.tag){case _:{var M=l;R.flags|=Pr;var U=hr(c);R.lanes=xt(R.lanes,U);var F=ok(R,M,U);Fb(R,F);return}case O:var q=l,Z=R.type,te=R.stateNode;if((R.flags&Et)===Ye&&(typeof Z.getDerivedStateFromError=="function"||te!==null&&typeof te.componentDidCatch=="function"&&!sR(te))){R.flags|=Pr;var ke=hr(c);R.lanes=xt(R.lanes,ke);var Xe=jw(R,q,ke);Fb(R,Xe);return}break}R=R.return}while(R!==null)}function x3(){return null}var tg=d.ReactCurrentOwner,Do=!1,_w,ng,Lw,zw,Nw,Vc,Pw,My,rg;_w={},ng={},Lw={},zw={},Nw={},Vc=!1,Pw={},My={},rg={};function zi(e,t,a,l){e===null?t.child=fT(t,null,a,l):t.child=Pf(t,e.child,a,l)}function b3(e,t,a,l){t.child=Pf(t,e.child,null,l),t.child=Pf(t,null,a,l)}function ck(e,t,a,l,c){if(t.type!==t.elementType){var p=a.propTypes;p&&So(p,l,"prop",Pt(a))}var m=a.render,w=t.ref,C,R;If(t,c),Ji(t);{if(tg.current=t,Gi(!0),C=Yf(e,t,m,l,w,c),R=Gf(),t.mode&mt){nn(!0);try{C=Yf(e,t,m,l,w,c),R=Gf()}finally{nn(!1)}}Gi(!1)}return Go(),e!==null&&!Do?(kT(e,t,c),os(e,t,c)):(Xr()&&R&&yb(t),t.flags|=vo,zi(e,t,C,c),t.child)}function dk(e,t,a,l,c){if(e===null){var p=a.type;if(T5(p)&&a.compare===null&&a.defaultProps===void 0){var m=p;return m=tp(p),t.tag=Oe,t.type=m,Uw(t,p),fk(e,t,m,l,c)}{var w=p.propTypes;if(w&&So(w,l,"prop",Pt(p)),a.defaultProps!==void 0){var C=Pt(p)||"Unknown";rg[C]||(v("%s: Support for defaultProps will be removed from memo components in a future major release. Use JavaScript default parameters instead.",C),rg[C]=!0)}}var R=CS(a.type,null,l,t,t.mode,c);return R.ref=t.ref,R.return=t,t.child=R,R}{var M=a.type,U=M.propTypes;U&&So(U,l,"prop",Pt(M))}var F=e.child,q=Gw(e,c);if(!q){var Z=F.memoizedProps,te=a.compare;if(te=te!==null?te:Ke,te(Z,l)&&e.ref===t.ref)return os(e,t,c)}t.flags|=vo;var ke=Qc(F,l);return ke.ref=t.ref,ke.return=t,t.child=ke,ke}function fk(e,t,a,l,c){if(t.type!==t.elementType){var p=t.elementType;if(p.$$typeof===ut){var m=p,w=m._payload,C=m._init;try{p=C(w)}catch{p=null}var R=p&&p.propTypes;R&&So(R,l,"prop",Pt(p))}}if(e!==null){var M=e.memoizedProps;if(Ke(M,l)&&e.ref===t.ref&&t.type===e.type)if(Do=!1,t.pendingProps=l=M,Gw(e,c))(e.flags&ma)!==Ye&&(Do=!0);else return t.lanes=e.lanes,os(e,t,c)}return Fw(e,t,a,l,c)}function pk(e,t,a){var l=t.pendingProps,c=l.children,p=e!==null?e.memoizedState:null;if(l.mode==="hidden"||B)if((t.mode&Dt)===Ge){var m={baseLanes:ie,cachePool:null,transitions:null};t.memoizedState=m,By(t,a)}else if(vi(a,gi)){var U={baseLanes:ie,cachePool:null,transitions:null};t.memoizedState=U;var F=p!==null?p.baseLanes:a;By(t,F)}else{var w=null,C;if(p!==null){var R=p.baseLanes;C=xt(R,a)}else C=a;t.lanes=t.childLanes=gi;var M={baseLanes:C,cachePool:w,transitions:null};return t.memoizedState=M,t.updateQueue=null,By(t,C),null}else{var q;p!==null?(q=xt(p.baseLanes,a),t.memoizedState=null):q=a,By(t,q)}return zi(e,t,c,a),t.child}function w3(e,t,a){var l=t.pendingProps;return zi(e,t,l,a),t.child}function S3(e,t,a){var l=t.pendingProps.children;return zi(e,t,l,a),t.child}function C3(e,t,a){{t.flags|=At;{var l=t.stateNode;l.effectDuration=0,l.passiveEffectDuration=0}}var c=t.pendingProps,p=c.children;return zi(e,t,p,a),t.child}function hk(e,t){var a=t.ref;(e===null&&a!==null||e!==null&&e.ref!==a)&&(t.flags|=Qn,t.flags|=nc)}function Fw(e,t,a,l,c){if(t.type!==t.elementType){var p=a.propTypes;p&&So(p,l,"prop",Pt(a))}var m;{var w=jf(t,a,!0);m=_f(t,w)}var C,R;If(t,c),Ji(t);{if(tg.current=t,Gi(!0),C=Yf(e,t,a,l,m,c),R=Gf(),t.mode&mt){nn(!0);try{C=Yf(e,t,a,l,m,c),R=Gf()}finally{nn(!1)}}Gi(!1)}return Go(),e!==null&&!Do?(kT(e,t,c),os(e,t,c)):(Xr()&&R&&yb(t),t.flags|=vo,zi(e,t,C,c),t.child)}function gk(e,t,a,l,c){{switch(I5(t)){case!1:{var p=t.stateNode,m=t.type,w=new m(t.memoizedProps,p.context),C=w.state;p.updater.enqueueSetState(p,C,null);break}case!0:{t.flags|=Et,t.flags|=Pr;var R=new Error("Simulated error coming from DevTools"),M=hr(c);t.lanes=xt(t.lanes,M);var U=jw(t,Hc(R,t),M);Fb(t,U);break}}if(t.type!==t.elementType){var F=a.propTypes;F&&So(F,l,"prop",Pt(a))}}var q;sl(a)?(q=!0,Uv(t)):q=!1,If(t,c);var Z=t.stateNode,te;Z===null?($y(e,t),ik(t,a,l),Ow(t,a,l,c),te=!0):e===null?te=f3(t,a,l,c):te=p3(e,t,a,l,c);var ke=Iw(e,t,a,te,q,c);{var Xe=t.stateNode;te&&Xe.props!==l&&(Vc||v("It looks like %s is reassigning its own `this.props` while rendering. This is not supported and can lead to confusing bugs.",lt(t)||"a component"),Vc=!0)}return ke}function Iw(e,t,a,l,c,p){hk(e,t);var m=(t.flags&Et)!==Ye;if(!l&&!m)return c&&qE(t,a,!1),os(e,t,p);var w=t.stateNode;tg.current=t;var C;if(m&&typeof a.getDerivedStateFromError!="function")C=null,JT();else{Ji(t);{if(Gi(!0),C=w.render(),t.mode&mt){nn(!0);try{w.render()}finally{nn(!1)}}Gi(!1)}Go()}return t.flags|=vo,e!==null&&m?b3(e,t,C,p):zi(e,t,C,p),t.memoizedState=w.state,c&&qE(t,a,!0),t.child}function mk(e){var t=e.stateNode;t.pendingContext?KE(e,t.pendingContext,t.pendingContext!==t.context):t.context&&KE(e,t.context,!1),Ib(e,t.containerInfo)}function E3(e,t,a){if(mk(t),e===null)throw new Error("Should have a current fiber. This is a bug in React.");var l=t.pendingProps,c=t.memoizedState,p=c.element;xT(e,t),iy(t,l,null,a);var m=t.memoizedState;t.stateNode;var w=m.element;if(c.isDehydrated){var C={element:w,isDehydrated:!1,cache:m.cache,pendingSuspenseBoundaries:m.pendingSuspenseBoundaries,transitions:m.transitions},R=t.updateQueue;if(R.baseState=C,t.memoizedState=C,t.flags&Rn){var M=Hc(new Error("There was an error while hydrating. Because the error happened outside of a Suspense boundary, the entire root will switch to client rendering."),t);return vk(e,t,w,a,M)}else if(w!==p){var U=Hc(new Error("This root received an early update, before anything was able hydrate. Switched the entire root to client rendering."),t);return vk(e,t,w,a,U)}else{RN(t);var F=fT(t,null,w,a);t.child=F;for(var q=F;q;)q.flags=q.flags&~_n|Ln,q=q.sibling}}else{if(Nf(),w===p)return os(e,t,a);zi(e,t,w,a)}return t.child}function vk(e,t,a,l,c){return Nf(),Eb(c),t.flags|=Rn,zi(e,t,a,l),t.child}function T3(e,t,a){CT(t),e===null&&Cb(t);var l=t.type,c=t.pendingProps,p=e!==null?e.memoizedProps:null,m=c.children,w=ib(l,c);return w?m=null:p!==null&&ib(l,p)&&(t.flags|=tn),hk(e,t),zi(e,t,m,a),t.child}function k3(e,t){return e===null&&Cb(t),null}function R3(e,t,a,l){$y(e,t);var c=t.pendingProps,p=a,m=p._payload,w=p._init,C=w(m);t.type=C;var R=t.tag=k5(C),M=Ro(C,c),U;switch(R){case $:return Uw(t,C),t.type=C=tp(C),U=Fw(null,t,C,M,l),U;case O:return t.type=C=vS(C),U=gk(null,t,C,M,l),U;case he:return t.type=C=yS(C),U=ck(null,t,C,M,l),U;case le:{if(t.type!==t.elementType){var F=C.propTypes;F&&So(F,M,"prop",Pt(C))}return U=dk(null,t,C,Ro(C.type,M),l),U}}var q="";throw C!==null&&typeof C=="object"&&C.$$typeof===ut&&(q=" Did you wrap a component in React.lazy() more than once?"),new Error("Element type is invalid. Received a promise that resolves to: "+C+". "+("Lazy element type must resolve to a class or function."+q))}function D3(e,t,a,l,c){$y(e,t),t.tag=O;var p;return sl(a)?(p=!0,Uv(t)):p=!1,If(t,c),ik(t,a,l),Ow(t,a,l,c),Iw(null,t,a,!0,p,c)}function M3(e,t,a,l){$y(e,t);var c=t.pendingProps,p;{var m=jf(t,a,!1);p=_f(t,m)}If(t,l);var w,C;Ji(t);{if(a.prototype&&typeof a.prototype.render=="function"){var R=Pt(a)||"Unknown";_w[R]||(v("The <%s /> component appears to have a render method, but doesn't extend React.Component. This is likely to cause errors. Change %s to extend React.Component instead.",R,R),_w[R]=!0)}t.mode&mt&&Eo.recordLegacyContextWarning(t,null),Gi(!0),tg.current=t,w=Yf(null,t,a,c,p,l),C=Gf(),Gi(!1)}if(Go(),t.flags|=vo,typeof w=="object"&&w!==null&&typeof w.render=="function"&&w.$$typeof===void 0){var M=Pt(a)||"Unknown";ng[M]||(v("The <%s /> component appears to be a function component that returns a class instance. Change %s to a class that extends React.Component instead. If you can't use a class try assigning the prototype on the function as a workaround. `%s.prototype = React.Component.prototype`. Don't use an arrow function since it cannot be called with `new` by React.",M,M,M),ng[M]=!0)}if(typeof w=="object"&&w!==null&&typeof w.render=="function"&&w.$$typeof===void 0){{var U=Pt(a)||"Unknown";ng[U]||(v("The <%s /> component appears to be a function component that returns a class instance. Change %s to a class that extends React.Component instead. If you can't use a class try assigning the prototype on the function as a workaround. `%s.prototype = React.Component.prototype`. Don't use an arrow function since it cannot be called with `new` by React.",U,U,U),ng[U]=!0)}t.tag=O,t.memoizedState=null,t.updateQueue=null;var F=!1;return sl(a)?(F=!0,Uv(t)):F=!1,t.memoizedState=w.state!==null&&w.state!==void 0?w.state:null,Pb(t),rk(t,w),Ow(t,a,c,l),Iw(null,t,a,!0,F,l)}else{if(t.tag=$,t.mode&mt){nn(!0);try{w=Yf(null,t,a,c,p,l),C=Gf()}finally{nn(!1)}}return Xr()&&C&&yb(t),zi(null,t,w,l),Uw(t,a),t.child}}function Uw(e,t){{if(t&&t.childContextTypes&&v("%s(...): childContextTypes cannot be defined on a function component.",t.displayName||t.name||"Component"),e.ref!==null){var a="",l=Wr();l&&(a+=`

Check the render method of \``+l+"`.");var c=l||"",p=e._debugSource;p&&(c=p.fileName+":"+p.lineNumber),Nw[c]||(Nw[c]=!0,v("Function components cannot be given refs. Attempts to access this ref will fail. Did you mean to use React.forwardRef()?%s",a))}if(t.defaultProps!==void 0){var m=Pt(t)||"Unknown";rg[m]||(v("%s: Support for defaultProps will be removed from function components in a future major release. Use JavaScript default parameters instead.",m),rg[m]=!0)}if(typeof t.getDerivedStateFromProps=="function"){var w=Pt(t)||"Unknown";zw[w]||(v("%s: Function components do not support getDerivedStateFromProps.",w),zw[w]=!0)}if(typeof t.contextType=="object"&&t.contextType!==null){var C=Pt(t)||"Unknown";Lw[C]||(v("%s: Function components do not support contextType.",C),Lw[C]=!0)}}}var Bw={dehydrated:null,treeContext:null,retryLane:Xn};function Hw(e){return{baseLanes:e,cachePool:x3(),transitions:null}}function O3(e,t){var a=null;return{baseLanes:xt(e.baseLanes,t),cachePool:a,transitions:e.transitions}}function $3(e,t,a,l){if(t!==null){var c=t.memoizedState;if(c===null)return!1}return Hb(e,Yh)}function A3(e,t){return mc(e.childLanes,t)}function yk(e,t,a){var l=t.pendingProps;U5(t)&&(t.flags|=Et);var c=To.current,p=!1,m=(t.flags&Et)!==Ye;if(m||$3(c,e)?(p=!0,t.flags&=~Et):(e===null||e.memoizedState!==null)&&(c=qN(c,TT)),c=Bf(c),du(t,c),e===null){Cb(t);var w=t.memoizedState;if(w!==null){var C=w.dehydrated;if(C!==null)return N3(t,C)}var R=l.children,M=l.fallback;if(p){var U=j3(t,R,M,a),F=t.child;return F.memoizedState=Hw(a),t.memoizedState=Bw,U}else return Vw(t,R)}else{var q=e.memoizedState;if(q!==null){var Z=q.dehydrated;if(Z!==null)return P3(e,t,m,l,Z,q,a)}if(p){var te=l.fallback,ke=l.children,Xe=L3(e,t,ke,te,a),We=t.child,It=e.child.memoizedState;return We.memoizedState=It===null?Hw(a):O3(It,a),We.childLanes=A3(e,a),t.memoizedState=Bw,Xe}else{var _t=l.children,Y=_3(e,t,_t,a);return t.memoizedState=null,Y}}}function Vw(e,t,a){var l=e.mode,c={mode:"visible",children:t},p=Ww(c,l);return p.return=e,e.child=p,p}function j3(e,t,a,l){var c=e.mode,p=e.child,m={mode:"hidden",children:t},w,C;return(c&Dt)===Ge&&p!==null?(w=p,w.childLanes=ie,w.pendingProps=m,e.mode&zt&&(w.actualDuration=0,w.actualStartTime=-1,w.selfBaseDuration=0,w.treeBaseDuration=0),C=xu(a,c,l,null)):(w=Ww(m,c),C=xu(a,c,l,null)),w.return=e,C.return=e,w.sibling=C,e.child=w,C}function Ww(e,t,a){return xR(e,t,ie,null)}function xk(e,t){return Qc(e,t)}function _3(e,t,a,l){var c=e.child,p=c.sibling,m=xk(c,{mode:"visible",children:a});if((t.mode&Dt)===Ge&&(m.lanes=l),m.return=t,m.sibling=null,p!==null){var w=t.deletions;w===null?(t.deletions=[p],t.flags|=di):w.push(p)}return t.child=m,m}function L3(e,t,a,l,c){var p=t.mode,m=e.child,w=m.sibling,C={mode:"hidden",children:a},R;if((p&Dt)===Ge&&t.child!==m){var M=t.child;R=M,R.childLanes=ie,R.pendingProps=C,t.mode&zt&&(R.actualDuration=0,R.actualStartTime=-1,R.selfBaseDuration=m.selfBaseDuration,R.treeBaseDuration=m.treeBaseDuration),t.deletions=null}else R=xk(m,C),R.subtreeFlags=m.subtreeFlags&qn;var U;return w!==null?U=Qc(w,l):(U=xu(l,p,c,null),U.flags|=_n),U.return=t,R.return=t,R.sibling=U,t.child=R,U}function Oy(e,t,a,l){l!==null&&Eb(l),Pf(t,e.child,null,a);var c=t.pendingProps,p=c.children,m=Vw(t,p);return m.flags|=_n,t.memoizedState=null,m}function z3(e,t,a,l,c){var p=t.mode,m={mode:"visible",children:a},w=Ww(m,p),C=xu(l,p,c,null);return C.flags|=_n,w.return=t,C.return=t,w.sibling=C,t.child=w,(t.mode&Dt)!==Ge&&Pf(t,e.child,null,c),C}function N3(e,t,a){return(e.mode&Dt)===Ge?(v("Cannot hydrate Suspense in legacy mode. Switch from ReactDOM.hydrate(element, container) to ReactDOMClient.hydrateRoot(container, <App />).render(element) or remove the Suspense components from the server rendered components."),e.lanes=nt):sb(t)?e.lanes=pr:e.lanes=gi,null}function P3(e,t,a,l,c,p,m){if(a)if(t.flags&Rn){t.flags&=~Rn;var Y=$w(new Error("There was an error while hydrating this Suspense boundary. Switched to client rendering."));return Oy(e,t,m,Y)}else{if(t.memoizedState!==null)return t.child=e.child,t.flags|=Et,null;var ne=l.children,G=l.fallback,me=z3(e,t,ne,G,m),_e=t.child;return _e.memoizedState=Hw(m),t.memoizedState=Bw,me}else{if(TN(),(t.mode&Dt)===Ge)return Oy(e,t,m,null);if(sb(c)){var w,C,R;{var M=Bz(c);w=M.digest,C=M.message,R=M.stack}var U;C?U=new Error(C):U=new Error("The server could not finish this Suspense boundary, likely due to an error during server rendering. Switched to client rendering.");var F=$w(U,w,R);return Oy(e,t,m,F)}var q=vi(m,e.childLanes);if(Do||q){var Z=Uy();if(Z!==null){var te=sf(Z,m);if(te!==Xn&&te!==p.retryLane){p.retryLane=te;var ke=rn;aa(e,te),jr(Z,e,te,ke)}}fS();var Xe=$w(new Error("This Suspense boundary received an update before it finished hydrating. This caused the boundary to switch to client rendering. The usual way to fix this is to wrap the original update in startTransition."));return Oy(e,t,m,Xe)}else if(BE(c)){t.flags|=Et,t.child=e.child;var We=u5.bind(null,e);return Hz(c,We),null}else{DN(t,c,p.treeContext);var It=l.children,_t=Vw(t,It);return _t.flags|=Ln,_t}}}function bk(e,t,a){e.lanes=xt(e.lanes,t);var l=e.alternate;l!==null&&(l.lanes=xt(l.lanes,t)),_b(e.return,t,a)}function F3(e,t,a){for(var l=t;l!==null;){if(l.tag===se){var c=l.memoizedState;c!==null&&bk(l,a,e)}else if(l.tag===bt)bk(l,a,e);else if(l.child!==null){l.child.return=l,l=l.child;continue}if(l===e)return;for(;l.sibling===null;){if(l.return===null||l.return===e)return;l=l.return}l.sibling.return=l.return,l=l.sibling}}function I3(e){for(var t=e,a=null;t!==null;){var l=t.alternate;l!==null&&sy(l)===null&&(a=t),t=t.sibling}return a}function U3(e){if(e!==void 0&&e!=="forwards"&&e!=="backwards"&&e!=="together"&&!Pw[e])if(Pw[e]=!0,typeof e=="string")switch(e.toLowerCase()){case"together":case"forwards":case"backwards":{v('"%s" is not a valid value for revealOrder on <SuspenseList />. Use lowercase "%s" instead.',e,e.toLowerCase());break}case"forward":case"backward":{v('"%s" is not a valid value for revealOrder on <SuspenseList />. React uses the -s suffix in the spelling. Use "%ss" instead.',e,e.toLowerCase());break}default:v('"%s" is not a supported revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',e);break}else v('%s is not a supported value for revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',e)}function B3(e,t){e!==void 0&&!My[e]&&(e!=="collapsed"&&e!=="hidden"?(My[e]=!0,v('"%s" is not a supported value for tail on <SuspenseList />. Did you mean "collapsed" or "hidden"?',e)):t!=="forwards"&&t!=="backwards"&&(My[e]=!0,v('<SuspenseList tail="%s" /> is only valid if revealOrder is "forwards" or "backwards". Did you mean to specify revealOrder="forwards"?',e)))}function wk(e,t){{var a=yt(e),l=!a&&typeof kn(e)=="function";if(a||l){var c=a?"array":"iterable";return v("A nested %s was passed to row #%s in <SuspenseList />. Wrap it in an additional SuspenseList to configure its revealOrder: <SuspenseList revealOrder=...> ... <SuspenseList revealOrder=...>{%s}</SuspenseList> ... </SuspenseList>",c,t,c),!1}}return!0}function H3(e,t){if((t==="forwards"||t==="backwards")&&e!==void 0&&e!==null&&e!==!1)if(yt(e)){for(var a=0;a<e.length;a++)if(!wk(e[a],a))return}else{var l=kn(e);if(typeof l=="function"){var c=l.call(e);if(c)for(var p=c.next(),m=0;!p.done;p=c.next()){if(!wk(p.value,m))return;m++}}else v('A single row was passed to a <SuspenseList revealOrder="%s" />. This is not useful since it needs multiple rows. Did you mean to pass multiple children or an array?',t)}}function Yw(e,t,a,l,c){var p=e.memoizedState;p===null?e.memoizedState={isBackwards:t,rendering:null,renderingStartTime:0,last:l,tail:a,tailMode:c}:(p.isBackwards=t,p.rendering=null,p.renderingStartTime=0,p.last=l,p.tail=a,p.tailMode=c)}function Sk(e,t,a){var l=t.pendingProps,c=l.revealOrder,p=l.tail,m=l.children;U3(c),B3(p,c),H3(m,c),zi(e,t,m,a);var w=To.current,C=Hb(w,Yh);if(C)w=Vb(w,Yh),t.flags|=Et;else{var R=e!==null&&(e.flags&Et)!==Ye;R&&F3(t,t.child,a),w=Bf(w)}if(du(t,w),(t.mode&Dt)===Ge)t.memoizedState=null;else switch(c){case"forwards":{var M=I3(t.child),U;M===null?(U=t.child,t.child=null):(U=M.sibling,M.sibling=null),Yw(t,!1,U,M,p);break}case"backwards":{var F=null,q=t.child;for(t.child=null;q!==null;){var Z=q.alternate;if(Z!==null&&sy(Z)===null){t.child=q;break}var te=q.sibling;q.sibling=F,F=q,q=te}Yw(t,!0,F,null,p);break}case"together":{Yw(t,!1,null,null,void 0);break}default:t.memoizedState=null}return t.child}function V3(e,t,a){Ib(t,t.stateNode.containerInfo);var l=t.pendingProps;return e===null?t.child=Pf(t,null,l,a):zi(e,t,l,a),t.child}var Ck=!1;function W3(e,t,a){var l=t.type,c=l._context,p=t.pendingProps,m=t.memoizedProps,w=p.value;{"value"in p||Ck||(Ck=!0,v("The `value` prop is required for the `<Context.Provider>`. Did you misspell it or forget to pass it?"));var C=t.type.propTypes;C&&So(C,p,"prop","Context.Provider")}if(gT(t,c,w),m!==null){var R=m.value;if(De(R,w)){if(m.children===p.children&&!Fv())return os(e,t,a)}else UN(t,c,a)}var M=p.children;return zi(e,t,M,a),t.child}var Ek=!1;function Y3(e,t,a){var l=t.type;l._context===void 0?l!==l.Consumer&&(Ek||(Ek=!0,v("Rendering <Context> directly is not supported and will be removed in a future major release. Did you mean to render <Context.Consumer> instead?"))):l=l._context;var c=t.pendingProps,p=c.children;typeof p!="function"&&v("A context consumer was rendered with multiple children, or a child that isn't a function. A context consumer expects a single child that is a function. If you did pass a function, make sure there is no trailing or leading whitespace around it."),If(t,a);var m=mr(l);Ji(t);var w;return tg.current=t,Gi(!0),w=p(m),Gi(!1),Go(),t.flags|=vo,zi(e,t,w,a),t.child}function ig(){Do=!0}function $y(e,t){(t.mode&Dt)===Ge&&e!==null&&(e.alternate=null,t.alternate=null,t.flags|=_n)}function os(e,t,a){return e!==null&&(t.dependencies=e.dependencies),JT(),mg(t.lanes),vi(a,t.childLanes)?(FN(e,t),t.child):null}function G3(e,t,a){{var l=t.return;if(l===null)throw new Error("Cannot swap the root fiber.");if(e.alternate=null,t.alternate=null,a.index=t.index,a.sibling=t.sibling,a.return=t.return,a.ref=t.ref,t===l.child)l.child=a;else{var c=l.child;if(c===null)throw new Error("Expected parent to have a child.");for(;c.sibling!==t;)if(c=c.sibling,c===null)throw new Error("Expected to find the previous sibling.");c.sibling=a}var p=l.deletions;return p===null?(l.deletions=[e],l.flags|=di):p.push(e),a.flags|=_n,a}}function Gw(e,t){var a=e.lanes;return!!vi(a,t)}function K3(e,t,a){switch(t.tag){case _:mk(t),t.stateNode,Nf();break;case N:CT(t);break;case O:{var l=t.type;sl(l)&&Uv(t);break}case V:Ib(t,t.stateNode.containerInfo);break;case ue:{var c=t.memoizedProps.value,p=t.type._context;gT(t,p,c);break}case Le:{var m=vi(a,t.childLanes);m&&(t.flags|=At);{var w=t.stateNode;w.effectDuration=0,w.passiveEffectDuration=0}}break;case se:{var C=t.memoizedState;if(C!==null){if(C.dehydrated!==null)return du(t,Bf(To.current)),t.flags|=Et,null;var R=t.child,M=R.childLanes;if(vi(a,M))return yk(e,t,a);du(t,Bf(To.current));var U=os(e,t,a);return U!==null?U.sibling:null}else du(t,Bf(To.current));break}case bt:{var F=(e.flags&Et)!==Ye,q=vi(a,t.childLanes);if(F){if(q)return Sk(e,t,a);t.flags|=Et}var Z=t.memoizedState;if(Z!==null&&(Z.rendering=null,Z.tail=null,Z.lastEffect=null),du(t,To.current),q)break;return null}case Be:case Bt:return t.lanes=ie,pk(e,t,a)}return os(e,t,a)}function Tk(e,t,a){if(t._debugNeedsRemount&&e!==null)return G3(e,t,CS(t.type,t.key,t.pendingProps,t._debugOwner||null,t.mode,t.lanes));if(e!==null){var l=e.memoizedProps,c=t.pendingProps;if(l!==c||Fv()||t.type!==e.type)Do=!0;else{var p=Gw(e,a);if(!p&&(t.flags&Et)===Ye)return Do=!1,K3(e,t,a);(e.flags&ma)!==Ye?Do=!0:Do=!1}}else if(Do=!1,Xr()&&xN(t)){var m=t.index,w=bN();JE(t,w,m)}switch(t.lanes=ie,t.tag){case P:return M3(e,t,t.type,a);case ft:{var C=t.elementType;return R3(e,t,C,a)}case $:{var R=t.type,M=t.pendingProps,U=t.elementType===R?M:Ro(R,M);return Fw(e,t,R,U,a)}case O:{var F=t.type,q=t.pendingProps,Z=t.elementType===F?q:Ro(F,q);return gk(e,t,F,Z,a)}case _:return E3(e,t,a);case N:return T3(e,t,a);case J:return k3(e,t);case se:return yk(e,t,a);case V:return V3(e,t,a);case he:{var te=t.type,ke=t.pendingProps,Xe=t.elementType===te?ke:Ro(te,ke);return ck(e,t,te,Xe,a)}case xe:return w3(e,t,a);case Pe:return S3(e,t,a);case Le:return C3(e,t,a);case ue:return W3(e,t,a);case de:return Y3(e,t,a);case le:{var We=t.type,It=t.pendingProps,_t=Ro(We,It);if(t.type!==t.elementType){var Y=We.propTypes;Y&&So(Y,_t,"prop",Pt(We))}return _t=Ro(We.type,_t),dk(e,t,We,_t,a)}case Oe:return fk(e,t,t.type,t.pendingProps,a);case He:{var ne=t.type,G=t.pendingProps,me=t.elementType===ne?G:Ro(ne,G);return D3(e,t,ne,me,a)}case bt:return Sk(e,t,a);case rt:break;case Be:return pk(e,t,a)}throw new Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function Kf(e){e.flags|=At}function kk(e){e.flags|=Qn,e.flags|=nc}var Rk,Kw,Dk,Mk;Rk=function(e,t,a,l){for(var c=t.child;c!==null;){if(c.tag===N||c.tag===J)mz(e,c.stateNode);else if(c.tag!==V){if(c.child!==null){c.child.return=c,c=c.child;continue}}if(c===t)return;for(;c.sibling===null;){if(c.return===null||c.return===t)return;c=c.return}c.sibling.return=c.return,c=c.sibling}},Kw=function(e,t){},Dk=function(e,t,a,l,c){var p=e.memoizedProps;if(p!==l){var m=t.stateNode,w=Ub(),C=yz(m,a,p,l,c,w);t.updateQueue=C,C&&Kf(t)}},Mk=function(e,t,a,l){a!==l&&Kf(t)};function ag(e,t){if(!Xr())switch(e.tailMode){case"hidden":{for(var a=e.tail,l=null;a!==null;)a.alternate!==null&&(l=a),a=a.sibling;l===null?e.tail=null:l.sibling=null;break}case"collapsed":{for(var c=e.tail,p=null;c!==null;)c.alternate!==null&&(p=c),c=c.sibling;p===null?!t&&e.tail!==null?e.tail.sibling=null:e.tail=null:p.sibling=null;break}}}function Jr(e){var t=e.alternate!==null&&e.alternate.child===e.child,a=ie,l=Ye;if(t){if((e.mode&zt)!==Ge){for(var C=e.selfBaseDuration,R=e.child;R!==null;)a=xt(a,xt(R.lanes,R.childLanes)),l|=R.subtreeFlags&qn,l|=R.flags&qn,C+=R.treeBaseDuration,R=R.sibling;e.treeBaseDuration=C}else for(var M=e.child;M!==null;)a=xt(a,xt(M.lanes,M.childLanes)),l|=M.subtreeFlags&qn,l|=M.flags&qn,M.return=e,M=M.sibling;e.subtreeFlags|=l}else{if((e.mode&zt)!==Ge){for(var c=e.actualDuration,p=e.selfBaseDuration,m=e.child;m!==null;)a=xt(a,xt(m.lanes,m.childLanes)),l|=m.subtreeFlags,l|=m.flags,c+=m.actualDuration,p+=m.treeBaseDuration,m=m.sibling;e.actualDuration=c,e.treeBaseDuration=p}else for(var w=e.child;w!==null;)a=xt(a,xt(w.lanes,w.childLanes)),l|=w.subtreeFlags,l|=w.flags,w.return=e,w=w.sibling;e.subtreeFlags|=l}return e.childLanes=a,t}function Q3(e,t,a){if(jN()&&(t.mode&Dt)!==Ge&&(t.flags&Et)===Ye)return oT(t),Nf(),t.flags|=Rn|_l|Pr,!1;var l=Yv(t);if(a!==null&&a.dehydrated!==null)if(e===null){if(!l)throw new Error("A dehydrated suspense component was completed without a hydrated node. This is probably a bug in React.");if($N(t),Jr(t),(t.mode&zt)!==Ge){var c=a!==null;if(c){var p=t.child;p!==null&&(t.treeBaseDuration-=p.treeBaseDuration)}}return!1}else{if(Nf(),(t.flags&Et)===Ye&&(t.memoizedState=null),t.flags|=At,Jr(t),(t.mode&zt)!==Ge){var m=a!==null;if(m){var w=t.child;w!==null&&(t.treeBaseDuration-=w.treeBaseDuration)}}return!1}else return lT(),!0}function Ok(e,t,a){var l=t.pendingProps;switch(xb(t),t.tag){case P:case ft:case Oe:case $:case he:case xe:case Pe:case Le:case de:case le:return Jr(t),null;case O:{var c=t.type;return sl(c)&&Iv(t),Jr(t),null}case _:{var p=t.stateNode;if(Uf(t),gb(t),Yb(),p.pendingContext&&(p.context=p.pendingContext,p.pendingContext=null),e===null||e.child===null){var m=Yv(t);if(m)Kf(t);else if(e!==null){var w=e.memoizedState;(!w.isDehydrated||(t.flags&Rn)!==Ye)&&(t.flags|=Zi,lT())}}return Kw(e,t),Jr(t),null}case N:{Bb(t);var C=ST(),R=t.type;if(e!==null&&t.stateNode!=null)Dk(e,t,R,l,C),e.ref!==t.ref&&kk(t);else{if(!l){if(t.stateNode===null)throw new Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return Jr(t),null}var M=Ub(),U=Yv(t);if(U)MN(t,C,M)&&Kf(t);else{var F=gz(R,l,C,M,t);Rk(F,t,!1,!1),t.stateNode=F,vz(F,R,l,C)&&Kf(t)}t.ref!==null&&kk(t)}return Jr(t),null}case J:{var q=l;if(e&&t.stateNode!=null){var Z=e.memoizedProps;Mk(e,t,Z,q)}else{if(typeof q!="string"&&t.stateNode===null)throw new Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");var te=ST(),ke=Ub(),Xe=Yv(t);Xe?ON(t)&&Kf(t):t.stateNode=xz(q,te,ke,t)}return Jr(t),null}case se:{Hf(t);var We=t.memoizedState;if(e===null||e.memoizedState!==null&&e.memoizedState.dehydrated!==null){var It=Q3(e,t,We);if(!It)return t.flags&Pr?t:null}if((t.flags&Et)!==Ye)return t.lanes=a,(t.mode&zt)!==Ge&&yw(t),t;var _t=We!==null,Y=e!==null&&e.memoizedState!==null;if(_t!==Y&&_t){var ne=t.child;if(ne.flags|=Ia,(t.mode&Dt)!==Ge){var G=e===null&&(t.memoizedProps.unstable_avoidThisFallback!==!0||!0);G||Hb(To.current,TT)?qP():fS()}}var me=t.updateQueue;if(me!==null&&(t.flags|=At),Jr(t),(t.mode&zt)!==Ge&&_t){var _e=t.child;_e!==null&&(t.treeBaseDuration-=_e.treeBaseDuration)}return null}case V:return Uf(t),Kw(e,t),e===null&&fN(t.stateNode.containerInfo),Jr(t),null;case ue:var Me=t.type._context;return jb(Me,t),Jr(t),null;case He:{var ot=t.type;return sl(ot)&&Iv(t),Jr(t),null}case bt:{Hf(t);var dt=t.memoizedState;if(dt===null)return Jr(t),null;var fn=(t.flags&Et)!==Ye,Vt=dt.rendering;if(Vt===null)if(fn)ag(dt,!1);else{var or=ZP()&&(e===null||(e.flags&Et)===Ye);if(!or)for(var Wt=t.child;Wt!==null;){var Jn=sy(Wt);if(Jn!==null){fn=!0,t.flags|=Et,ag(dt,!1);var Ci=Jn.updateQueue;return Ci!==null&&(t.updateQueue=Ci,t.flags|=At),t.subtreeFlags=Ye,IN(t,a),du(t,Vb(To.current,Yh)),t.child}Wt=Wt.sibling}dt.tail!==null&&Un()>Xk()&&(t.flags|=Et,fn=!0,ag(dt,!1),t.lanes=Vm)}else{if(!fn){var ii=sy(Vt);if(ii!==null){t.flags|=Et,fn=!0;var Ca=ii.updateQueue;if(Ca!==null&&(t.updateQueue=Ca,t.flags|=At),ag(dt,!0),dt.tail===null&&dt.tailMode==="hidden"&&!Vt.alternate&&!Xr())return Jr(t),null}else Un()*2-dt.renderingStartTime>Xk()&&a!==gi&&(t.flags|=Et,fn=!0,ag(dt,!1),t.lanes=Vm)}if(dt.isBackwards)Vt.sibling=t.child,t.child=Vt;else{var Fi=dt.last;Fi!==null?Fi.sibling=Vt:t.child=Vt,dt.last=Vt}}if(dt.tail!==null){var Ii=dt.tail;dt.rendering=Ii,dt.tail=Ii.sibling,dt.renderingStartTime=Un(),Ii.sibling=null;var Ei=To.current;return fn?Ei=Vb(Ei,Yh):Ei=Bf(Ei),du(t,Ei),Ii}return Jr(t),null}case rt:break;case Be:case Bt:{dS(t);var ds=t.memoizedState,np=ds!==null;if(e!==null){var wg=e.memoizedState,ml=wg!==null;ml!==np&&!B&&(t.flags|=Ia)}return!np||(t.mode&Dt)===Ge?Jr(t):vi(gl,gi)&&(Jr(t),t.subtreeFlags&(_n|At)&&(t.flags|=Ia)),null}case kt:return null;case pt:return null}throw new Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function q3(e,t,a){switch(xb(t),t.tag){case O:{var l=t.type;sl(l)&&Iv(t);var c=t.flags;return c&Pr?(t.flags=c&~Pr|Et,(t.mode&zt)!==Ge&&yw(t),t):null}case _:{t.stateNode,Uf(t),gb(t),Yb();var p=t.flags;return(p&Pr)!==Ye&&(p&Et)===Ye?(t.flags=p&~Pr|Et,t):null}case N:return Bb(t),null;case se:{Hf(t);var m=t.memoizedState;if(m!==null&&m.dehydrated!==null){if(t.alternate===null)throw new Error("Threw in newly mounted dehydrated component. This is likely a bug in React. Please file an issue.");Nf()}var w=t.flags;return w&Pr?(t.flags=w&~Pr|Et,(t.mode&zt)!==Ge&&yw(t),t):null}case bt:return Hf(t),null;case V:return Uf(t),null;case ue:var C=t.type._context;return jb(C,t),null;case Be:case Bt:return dS(t),null;case kt:return null;default:return null}}function $k(e,t,a){switch(xb(t),t.tag){case O:{var l=t.type.childContextTypes;l!=null&&Iv(t);break}case _:{t.stateNode,Uf(t),gb(t),Yb();break}case N:{Bb(t);break}case V:Uf(t);break;case se:Hf(t);break;case bt:Hf(t);break;case ue:var c=t.type._context;jb(c,t);break;case Be:case Bt:dS(t);break}}var Ak=null;Ak=new Set;var Ay=!1,ei=!1,X3=typeof WeakSet=="function"?WeakSet:Set,Ie=null,Qf=null,qf=null;function Z3(e){Xi(null,function(){throw e}),zp()}var J3=function(e,t){if(t.props=e.memoizedProps,t.state=e.memoizedState,e.mode&zt)try{pl(),t.componentWillUnmount()}finally{fl(e)}else t.componentWillUnmount()};function jk(e,t){try{hu(Rr,e)}catch(a){Tn(e,t,a)}}function Qw(e,t,a){try{J3(e,a)}catch(l){Tn(e,t,l)}}function eP(e,t,a){try{a.componentDidMount()}catch(l){Tn(e,t,l)}}function _k(e,t){try{zk(e)}catch(a){Tn(e,t,a)}}function Xf(e,t){var a=e.ref;if(a!==null)if(typeof a=="function"){var l;try{if(it&&ht&&e.mode&zt)try{pl(),l=a(null)}finally{fl(e)}else l=a(null)}catch(c){Tn(e,t,c)}typeof l=="function"&&v("Unexpected return value from a callback ref in %s. A callback ref should not return a function.",lt(e))}else a.current=null}function jy(e,t,a){try{a()}catch(l){Tn(e,t,l)}}var Lk=!1;function tP(e,t){pz(e.containerInfo),Ie=t,nP();var a=Lk;return Lk=!1,a}function nP(){for(;Ie!==null;){var e=Ie,t=e.child;(e.subtreeFlags&Vo)!==Ye&&t!==null?(t.return=e,Ie=t):rP()}}function rP(){for(;Ie!==null;){var e=Ie;ln(e);try{iP(e)}catch(a){Tn(e,e.return,a)}jn();var t=e.sibling;if(t!==null){t.return=e.return,Ie=t;return}Ie=e.return}}function iP(e){var t=e.alternate,a=e.flags;if((a&Zi)!==Ye){switch(ln(e),e.tag){case $:case he:case Oe:break;case O:{if(t!==null){var l=t.memoizedProps,c=t.memoizedState,p=e.stateNode;e.type===e.elementType&&!Vc&&(p.props!==e.memoizedProps&&v("Expected %s props to match memoized props before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(e)||"instance"),p.state!==e.memoizedState&&v("Expected %s state to match memoized state before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(e)||"instance"));var m=p.getSnapshotBeforeUpdate(e.elementType===e.type?l:Ro(e.type,l),c);{var w=Ak;m===void 0&&!w.has(e.type)&&(w.add(e.type),v("%s.getSnapshotBeforeUpdate(): A snapshot value (or null) must be returned. You have returned undefined.",lt(e)))}p.__reactInternalSnapshotBeforeUpdate=m}break}case _:{{var C=e.stateNode;Pz(C.containerInfo)}break}case N:case J:case V:case He:break;default:throw new Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}jn()}}function Mo(e,t,a){var l=t.updateQueue,c=l!==null?l.lastEffect:null;if(c!==null){var p=c.next,m=p;do{if((m.tag&e)===e){var w=m.destroy;m.destroy=void 0,w!==void 0&&((e&Zr)!==oa?Ko(t):(e&Rr)!==oa&&Kp(t),(e&ul)!==oa&&yg(!0),jy(t,a,w),(e&ul)!==oa&&yg(!1),(e&Zr)!==oa?_d():(e&Rr)!==oa&&Is())}m=m.next}while(m!==p)}}function hu(e,t){var a=t.updateQueue,l=a!==null?a.lastEffect:null;if(l!==null){var c=l.next,p=c;do{if((p.tag&e)===e){(e&Zr)!==oa?Bm(t):(e&Rr)!==oa&&Hm(t);var m=p.create;(e&ul)!==oa&&yg(!0),p.destroy=m(),(e&ul)!==oa&&yg(!1),(e&Zr)!==oa?xo():(e&Rr)!==oa&&Ld();{var w=p.destroy;if(w!==void 0&&typeof w!="function"){var C=void 0;(p.tag&Rr)!==Ye?C="useLayoutEffect":(p.tag&ul)!==Ye?C="useInsertionEffect":C="useEffect";var R=void 0;w===null?R=" You returned null. If your effect does not require clean up, return undefined (or nothing).":typeof w.then=="function"?R=`

It looks like you wrote `+C+`(async () => ...) or returned a Promise. Instead, write the async function inside your effect and call it immediately:

`+C+`(() => {
  async function fetchData() {
    // You can await here
    const response = await MyAPI.getData(someId);
    // ...
  }
  fetchData();
}, [someId]); // Or [] if effect doesn't need props or state

Learn more about data fetching with Hooks: https://reactjs.org/link/hooks-data-fetching`:R=" You returned: "+w,v("%s must not return anything besides a function, which is used for clean-up.%s",C,R)}}}p=p.next}while(p!==c)}}function aP(e,t){if((t.flags&At)!==Ye)switch(t.tag){case Le:{var a=t.stateNode.passiveEffectDuration,l=t.memoizedProps,c=l.id,p=l.onPostCommit,m=XT(),w=t.alternate===null?"mount":"update";qT()&&(w="nested-update"),typeof p=="function"&&p(c,w,a,m);var C=t.return;e:for(;C!==null;){switch(C.tag){case _:var R=C.stateNode;R.passiveEffectDuration+=a;break e;case Le:var M=C.stateNode;M.passiveEffectDuration+=a;break e}C=C.return}break}}}function oP(e,t,a,l){if((a.flags&Wo)!==Ye)switch(a.tag){case $:case he:case Oe:{if(!ei)if(a.mode&zt)try{pl(),hu(Rr|kr,a)}finally{fl(a)}else hu(Rr|kr,a);break}case O:{var c=a.stateNode;if(a.flags&At&&!ei)if(t===null)if(a.type===a.elementType&&!Vc&&(c.props!==a.memoizedProps&&v("Expected %s props to match memoized props before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&v("Expected %s state to match memoized state before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),a.mode&zt)try{pl(),c.componentDidMount()}finally{fl(a)}else c.componentDidMount();else{var p=a.elementType===a.type?t.memoizedProps:Ro(a.type,t.memoizedProps),m=t.memoizedState;if(a.type===a.elementType&&!Vc&&(c.props!==a.memoizedProps&&v("Expected %s props to match memoized props before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&v("Expected %s state to match memoized state before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),a.mode&zt)try{pl(),c.componentDidUpdate(p,m,c.__reactInternalSnapshotBeforeUpdate)}finally{fl(a)}else c.componentDidUpdate(p,m,c.__reactInternalSnapshotBeforeUpdate)}var w=a.updateQueue;w!==null&&(a.type===a.elementType&&!Vc&&(c.props!==a.memoizedProps&&v("Expected %s props to match memoized props before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&v("Expected %s state to match memoized state before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),wT(a,w,c));break}case _:{var C=a.updateQueue;if(C!==null){var R=null;if(a.child!==null)switch(a.child.tag){case N:R=a.child.stateNode;break;case O:R=a.child.stateNode;break}wT(a,C,R)}break}case N:{var M=a.stateNode;if(t===null&&a.flags&At){var U=a.type,F=a.memoizedProps;Ez(M,U,F)}break}case J:break;case V:break;case Le:{{var q=a.memoizedProps,Z=q.onCommit,te=q.onRender,ke=a.stateNode.effectDuration,Xe=XT(),We=t===null?"mount":"update";qT()&&(We="nested-update"),typeof te=="function"&&te(a.memoizedProps.id,We,a.actualDuration,a.treeBaseDuration,a.actualStartTime,Xe);{typeof Z=="function"&&Z(a.memoizedProps.id,We,ke,Xe),r5(a);var It=a.return;e:for(;It!==null;){switch(It.tag){case _:var _t=It.stateNode;_t.effectDuration+=ke;break e;case Le:var Y=It.stateNode;Y.effectDuration+=ke;break e}It=It.return}}}break}case se:{hP(e,a);break}case bt:case He:case rt:case Be:case Bt:case pt:break;default:throw new Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}ei||a.flags&Qn&&zk(a)}function lP(e){switch(e.tag){case $:case he:case Oe:{if(e.mode&zt)try{pl(),jk(e,e.return)}finally{fl(e)}else jk(e,e.return);break}case O:{var t=e.stateNode;typeof t.componentDidMount=="function"&&eP(e,e.return,t),_k(e,e.return);break}case N:{_k(e,e.return);break}}}function sP(e,t){for(var a=null,l=e;;){if(l.tag===N){if(a===null){a=l;try{var c=l.stateNode;t?_z(c):zz(l.stateNode,l.memoizedProps)}catch(m){Tn(e,e.return,m)}}}else if(l.tag===J){if(a===null)try{var p=l.stateNode;t?Lz(p):Nz(p,l.memoizedProps)}catch(m){Tn(e,e.return,m)}}else if(!((l.tag===Be||l.tag===Bt)&&l.memoizedState!==null&&l!==e)){if(l.child!==null){l.child.return=l,l=l.child;continue}}if(l===e)return;for(;l.sibling===null;){if(l.return===null||l.return===e)return;a===l&&(a=null),l=l.return}a===l&&(a=null),l.sibling.return=l.return,l=l.sibling}}function zk(e){var t=e.ref;if(t!==null){var a=e.stateNode,l;switch(e.tag){case N:l=a;break;default:l=a}if(typeof t=="function"){var c;if(e.mode&zt)try{pl(),c=t(l)}finally{fl(e)}else c=t(l);typeof c=="function"&&v("Unexpected return value from a callback ref in %s. A callback ref should not return a function.",lt(e))}else t.hasOwnProperty("current")||v("Unexpected ref object provided for %s. Use either a ref-setter function or React.createRef().",lt(e)),t.current=l}}function uP(e){var t=e.alternate;t!==null&&(t.return=null),e.return=null}function Nk(e){var t=e.alternate;t!==null&&(e.alternate=null,Nk(t));{if(e.child=null,e.deletions=null,e.sibling=null,e.tag===N){var a=e.stateNode;a!==null&&gN(a)}e.stateNode=null,e._debugOwner=null,e.return=null,e.dependencies=null,e.memoizedProps=null,e.memoizedState=null,e.pendingProps=null,e.stateNode=null,e.updateQueue=null}}function cP(e){for(var t=e.return;t!==null;){if(Pk(t))return t;t=t.return}throw new Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.")}function Pk(e){return e.tag===N||e.tag===_||e.tag===V}function Fk(e){var t=e;e:for(;;){for(;t.sibling===null;){if(t.return===null||Pk(t.return))return null;t=t.return}for(t.sibling.return=t.return,t=t.sibling;t.tag!==N&&t.tag!==J&&t.tag!==Tt;){if(t.flags&_n||t.child===null||t.tag===V)continue e;t.child.return=t,t=t.child}if(!(t.flags&_n))return t.stateNode}}function dP(e){var t=cP(e);switch(t.tag){case N:{var a=t.stateNode;t.flags&tn&&(UE(a),t.flags&=~tn);var l=Fk(e);Xw(e,l,a);break}case _:case V:{var c=t.stateNode.containerInfo,p=Fk(e);qw(e,p,c);break}default:throw new Error("Invalid host parent fiber. This error is likely caused by a bug in React. Please file an issue.")}}function qw(e,t,a){var l=e.tag,c=l===N||l===J;if(c){var p=e.stateNode;t?Oz(a,p,t):Dz(a,p)}else if(l!==V){var m=e.child;if(m!==null){qw(m,t,a);for(var w=m.sibling;w!==null;)qw(w,t,a),w=w.sibling}}}function Xw(e,t,a){var l=e.tag,c=l===N||l===J;if(c){var p=e.stateNode;t?Mz(a,p,t):Rz(a,p)}else if(l!==V){var m=e.child;if(m!==null){Xw(m,t,a);for(var w=m.sibling;w!==null;)Xw(w,t,a),w=w.sibling}}}var ti=null,Oo=!1;function fP(e,t,a){{var l=t;e:for(;l!==null;){switch(l.tag){case N:{ti=l.stateNode,Oo=!1;break e}case _:{ti=l.stateNode.containerInfo,Oo=!0;break e}case V:{ti=l.stateNode.containerInfo,Oo=!0;break e}}l=l.return}if(ti===null)throw new Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");Ik(e,t,a),ti=null,Oo=!1}uP(a)}function gu(e,t,a){for(var l=a.child;l!==null;)Ik(e,t,l),l=l.sibling}function Ik(e,t,a){switch(Fs(a),a.tag){case N:ei||Xf(a,t);case J:{{var l=ti,c=Oo;ti=null,gu(e,t,a),ti=l,Oo=c,ti!==null&&(Oo?Az(ti,a.stateNode):$z(ti,a.stateNode))}return}case Tt:{ti!==null&&(Oo?jz(ti,a.stateNode):lb(ti,a.stateNode));return}case V:{{var p=ti,m=Oo;ti=a.stateNode.containerInfo,Oo=!0,gu(e,t,a),ti=p,Oo=m}return}case $:case he:case le:case Oe:{if(!ei){var w=a.updateQueue;if(w!==null){var C=w.lastEffect;if(C!==null){var R=C.next,M=R;do{var U=M,F=U.destroy,q=U.tag;F!==void 0&&((q&ul)!==oa?jy(a,t,F):(q&Rr)!==oa&&(Kp(a),a.mode&zt?(pl(),jy(a,t,F),fl(a)):jy(a,t,F),Is())),M=M.next}while(M!==R)}}}gu(e,t,a);return}case O:{if(!ei){Xf(a,t);var Z=a.stateNode;typeof Z.componentWillUnmount=="function"&&Qw(a,t,Z)}gu(e,t,a);return}case rt:{gu(e,t,a);return}case Be:{if(a.mode&Dt){var te=ei;ei=te||a.memoizedState!==null,gu(e,t,a),ei=te}else gu(e,t,a);break}default:{gu(e,t,a);return}}}function pP(e){e.memoizedState}function hP(e,t){var a=t.memoizedState;if(a===null){var l=t.alternate;if(l!==null){var c=l.memoizedState;if(c!==null){var p=c.dehydrated;p!==null&&Zz(p)}}}}function Uk(e){var t=e.updateQueue;if(t!==null){e.updateQueue=null;var a=e.stateNode;a===null&&(a=e.stateNode=new X3),t.forEach(function(l){var c=c5.bind(null,e,l);if(!a.has(l)){if(a.add(l),Fr)if(Qf!==null&&qf!==null)vg(qf,Qf);else throw Error("Expected finished root and lanes to be set. This is a bug in React.");l.then(c,c)}})}}function gP(e,t,a){Qf=a,qf=e,ln(t),Bk(t,e),ln(t),Qf=null,qf=null}function $o(e,t,a){var l=t.deletions;if(l!==null)for(var c=0;c<l.length;c++){var p=l[c];try{fP(e,t,p)}catch(C){Tn(p,t,C)}}var m=fa();if(t.subtreeFlags&Ns)for(var w=t.child;w!==null;)ln(w),Bk(w,e),w=w.sibling;ln(m)}function Bk(e,t,a){var l=e.alternate,c=e.flags;switch(e.tag){case $:case he:case le:case Oe:{if($o(t,e),hl(e),c&At){try{Mo(ul|kr,e,e.return),hu(ul|kr,e)}catch(ot){Tn(e,e.return,ot)}if(e.mode&zt){try{pl(),Mo(Rr|kr,e,e.return)}catch(ot){Tn(e,e.return,ot)}fl(e)}else try{Mo(Rr|kr,e,e.return)}catch(ot){Tn(e,e.return,ot)}}return}case O:{$o(t,e),hl(e),c&Qn&&l!==null&&Xf(l,l.return);return}case N:{$o(t,e),hl(e),c&Qn&&l!==null&&Xf(l,l.return);{if(e.flags&tn){var p=e.stateNode;try{UE(p)}catch(ot){Tn(e,e.return,ot)}}if(c&At){var m=e.stateNode;if(m!=null){var w=e.memoizedProps,C=l!==null?l.memoizedProps:w,R=e.type,M=e.updateQueue;if(e.updateQueue=null,M!==null)try{Tz(m,M,R,C,w,e)}catch(ot){Tn(e,e.return,ot)}}}}return}case J:{if($o(t,e),hl(e),c&At){if(e.stateNode===null)throw new Error("This should have a text node initialized. This error is likely caused by a bug in React. Please file an issue.");var U=e.stateNode,F=e.memoizedProps,q=l!==null?l.memoizedProps:F;try{kz(U,q,F)}catch(ot){Tn(e,e.return,ot)}}return}case _:{if($o(t,e),hl(e),c&At&&l!==null){var Z=l.memoizedState;if(Z.isDehydrated)try{Xz(t.containerInfo)}catch(ot){Tn(e,e.return,ot)}}return}case V:{$o(t,e),hl(e);return}case se:{$o(t,e),hl(e);var te=e.child;if(te.flags&Ia){var ke=te.stateNode,Xe=te.memoizedState,We=Xe!==null;if(ke.isHidden=We,We){var It=te.alternate!==null&&te.alternate.memoizedState!==null;It||QP()}}if(c&At){try{pP(e)}catch(ot){Tn(e,e.return,ot)}Uk(e)}return}case Be:{var _t=l!==null&&l.memoizedState!==null;if(e.mode&Dt){var Y=ei;ei=Y||_t,$o(t,e),ei=Y}else $o(t,e);if(hl(e),c&Ia){var ne=e.stateNode,G=e.memoizedState,me=G!==null,_e=e;if(ne.isHidden=me,me&&!_t&&(_e.mode&Dt)!==Ge){Ie=_e;for(var Me=_e.child;Me!==null;)Ie=Me,vP(Me),Me=Me.sibling}sP(_e,me)}return}case bt:{$o(t,e),hl(e),c&At&&Uk(e);return}case rt:return;default:{$o(t,e),hl(e);return}}}function hl(e){var t=e.flags;if(t&_n){try{dP(e)}catch(a){Tn(e,e.return,a)}e.flags&=~_n}t&Ln&&(e.flags&=~Ln)}function mP(e,t,a){Qf=a,qf=t,Ie=e,Hk(e,t,a),Qf=null,qf=null}function Hk(e,t,a){for(var l=(e.mode&Dt)!==Ge;Ie!==null;){var c=Ie,p=c.child;if(c.tag===Be&&l){var m=c.memoizedState!==null,w=m||Ay;if(w){Zw(e,t,a);continue}else{var C=c.alternate,R=C!==null&&C.memoizedState!==null,M=R||ei,U=Ay,F=ei;Ay=w,ei=M,ei&&!F&&(Ie=c,yP(c));for(var q=p;q!==null;)Ie=q,Hk(q,t,a),q=q.sibling;Ie=c,Ay=U,ei=F,Zw(e,t,a);continue}}(c.subtreeFlags&Wo)!==Ye&&p!==null?(p.return=c,Ie=p):Zw(e,t,a)}}function Zw(e,t,a){for(;Ie!==null;){var l=Ie;if((l.flags&Wo)!==Ye){var c=l.alternate;ln(l);try{oP(t,c,l,a)}catch(m){Tn(l,l.return,m)}jn()}if(l===e){Ie=null;return}var p=l.sibling;if(p!==null){p.return=l.return,Ie=p;return}Ie=l.return}}function vP(e){for(;Ie!==null;){var t=Ie,a=t.child;switch(t.tag){case $:case he:case le:case Oe:{if(t.mode&zt)try{pl(),Mo(Rr,t,t.return)}finally{fl(t)}else Mo(Rr,t,t.return);break}case O:{Xf(t,t.return);var l=t.stateNode;typeof l.componentWillUnmount=="function"&&Qw(t,t.return,l);break}case N:{Xf(t,t.return);break}case Be:{var c=t.memoizedState!==null;if(c){Vk(e);continue}break}}a!==null?(a.return=t,Ie=a):Vk(e)}}function Vk(e){for(;Ie!==null;){var t=Ie;if(t===e){Ie=null;return}var a=t.sibling;if(a!==null){a.return=t.return,Ie=a;return}Ie=t.return}}function yP(e){for(;Ie!==null;){var t=Ie,a=t.child;if(t.tag===Be){var l=t.memoizedState!==null;if(l){Wk(e);continue}}a!==null?(a.return=t,Ie=a):Wk(e)}}function Wk(e){for(;Ie!==null;){var t=Ie;ln(t);try{lP(t)}catch(l){Tn(t,t.return,l)}if(jn(),t===e){Ie=null;return}var a=t.sibling;if(a!==null){a.return=t.return,Ie=a;return}Ie=t.return}}function xP(e,t,a,l){Ie=t,bP(t,e,a,l)}function bP(e,t,a,l){for(;Ie!==null;){var c=Ie,p=c.child;(c.subtreeFlags&wr)!==Ye&&p!==null?(p.return=c,Ie=p):wP(e,t,a,l)}}function wP(e,t,a,l){for(;Ie!==null;){var c=Ie;if((c.flags&Oi)!==Ye){ln(c);try{SP(t,c,a,l)}catch(m){Tn(c,c.return,m)}jn()}if(c===e){Ie=null;return}var p=c.sibling;if(p!==null){p.return=c.return,Ie=p;return}Ie=c.return}}function SP(e,t,a,l){switch(t.tag){case $:case he:case Oe:{if(t.mode&zt){vw();try{hu(Zr|kr,t)}finally{mw(t)}}else hu(Zr|kr,t);break}}}function CP(e){Ie=e,EP()}function EP(){for(;Ie!==null;){var e=Ie,t=e.child;if((Ie.flags&di)!==Ye){var a=e.deletions;if(a!==null){for(var l=0;l<a.length;l++){var c=a[l];Ie=c,RP(c,e)}{var p=e.alternate;if(p!==null){var m=p.child;if(m!==null){p.child=null;do{var w=m.sibling;m.sibling=null,m=w}while(m!==null)}}}Ie=e}}(e.subtreeFlags&wr)!==Ye&&t!==null?(t.return=e,Ie=t):TP()}}function TP(){for(;Ie!==null;){var e=Ie;(e.flags&Oi)!==Ye&&(ln(e),kP(e),jn());var t=e.sibling;if(t!==null){t.return=e.return,Ie=t;return}Ie=e.return}}function kP(e){switch(e.tag){case $:case he:case Oe:{e.mode&zt?(vw(),Mo(Zr|kr,e,e.return),mw(e)):Mo(Zr|kr,e,e.return);break}}}function RP(e,t){for(;Ie!==null;){var a=Ie;ln(a),MP(a,t),jn();var l=a.child;l!==null?(l.return=a,Ie=l):DP(e)}}function DP(e){for(;Ie!==null;){var t=Ie,a=t.sibling,l=t.return;if(Nk(t),t===e){Ie=null;return}if(a!==null){a.return=l,Ie=a;return}Ie=l}}function MP(e,t){switch(e.tag){case $:case he:case Oe:{e.mode&zt?(vw(),Mo(Zr,e,t),mw(e)):Mo(Zr,e,t);break}}}function OP(e){switch(e.tag){case $:case he:case Oe:{try{hu(Rr|kr,e)}catch(a){Tn(e,e.return,a)}break}case O:{var t=e.stateNode;try{t.componentDidMount()}catch(a){Tn(e,e.return,a)}break}}}function $P(e){switch(e.tag){case $:case he:case Oe:{try{hu(Zr|kr,e)}catch(t){Tn(e,e.return,t)}break}}}function AP(e){switch(e.tag){case $:case he:case Oe:{try{Mo(Rr|kr,e,e.return)}catch(a){Tn(e,e.return,a)}break}case O:{var t=e.stateNode;typeof t.componentWillUnmount=="function"&&Qw(e,e.return,t);break}}}function jP(e){switch(e.tag){case $:case he:case Oe:try{Mo(Zr|kr,e,e.return)}catch(t){Tn(e,e.return,t)}}}if(typeof Symbol=="function"&&Symbol.for){var og=Symbol.for;og("selector.component"),og("selector.has_pseudo_class"),og("selector.role"),og("selector.test_id"),og("selector.text")}var _P=[];function LP(){_P.forEach(function(e){return e()})}var zP=d.ReactCurrentActQueue;function NP(e){{var t=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0,a=typeof jest<"u";return a&&t!==!1}}function Yk(){{var e=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0;return!e&&zP.current!==null&&v("The current testing environment is not configured to support act(...)"),e}}var PP=Math.ceil,Jw=d.ReactCurrentDispatcher,eS=d.ReactCurrentOwner,ni=d.ReactCurrentBatchConfig,Ao=d.ReactCurrentActQueue,Or=0,Gk=1,ri=2,Xa=4,ls=0,lg=1,Wc=2,_y=3,sg=4,Kk=5,tS=6,Ft=Or,Ni=null,Wn=null,$r=ie,gl=ie,nS=au(ie),Ar=ls,ug=null,Ly=ie,cg=ie,zy=ie,dg=null,la=null,rS=0,Qk=500,qk=1/0,FP=500,ss=null;function fg(){qk=Un()+FP}function Xk(){return qk}var Ny=!1,iS=null,Zf=null,Yc=!1,mu=null,pg=ie,aS=[],oS=null,IP=50,hg=0,lS=null,sS=!1,Py=!1,UP=50,Jf=0,Fy=null,gg=rn,Iy=ie,Zk=!1;function Uy(){return Ni}function Pi(){return(Ft&(ri|Xa))!==Or?Un():(gg!==rn||(gg=Un()),gg)}function vu(e){var t=e.mode;if((t&Dt)===Ge)return nt;if((Ft&ri)!==Or&&$r!==ie)return hr($r);var a=zN()!==LN;if(a){if(ni.transition!==null){var l=ni.transition;l._updatedFibers||(l._updatedFibers=new Set),l._updatedFibers.add(e)}return Iy===Xn&&(Iy=ih()),Iy}var c=Ai();if(c!==Xn)return c;var p=bz();return p}function BP(e){var t=e.mode;return(t&Dt)===Ge?nt:qm()}function jr(e,t,a,l){f5(),Zk&&v("useInsertionEffect must not schedule updates."),sS&&(Py=!0),Ys(e,a,l),(Ft&ri)!==ie&&e===Ni?g5(t):(Fr&&Zm(e,t,a),m5(t),e===Ni&&((Ft&ri)===Or&&(cg=xt(cg,a)),Ar===sg&&yu(e,$r)),sa(e,l),a===nt&&Ft===Or&&(t.mode&Dt)===Ge&&!Ao.isBatchingLegacy&&(fg(),ZE()))}function HP(e,t,a){var l=e.current;l.lanes=t,Ys(e,t,a),sa(e,a)}function VP(e){return(Ft&ri)!==Or}function sa(e,t){var a=e.callbackNode;Gm(e,t);var l=mi(e,e===Ni?$r:ie);if(l===ie){a!==null&&hR(a),e.callbackNode=null,e.callbackPriority=Xn;return}var c=Bl(l),p=e.callbackPriority;if(p===c&&!(Ao.current!==null&&a!==gS)){a==null&&p!==nt&&v("Expected scheduled callback to exist. This error is likely caused by a bug in React. Please file an issue.");return}a!=null&&hR(a);var m;if(c===nt)e.tag===ou?(Ao.isBatchingLegacy!==null&&(Ao.didScheduleLegacyUpdate=!0),yN(tR.bind(null,e))):XE(tR.bind(null,e)),Ao.current!==null?Ao.current.push(lu):Sz(function(){(Ft&(ri|Xa))===Or&&lu()}),m=null;else{var w;switch(ev(l)){case yi:w=yo;break;case ea:w=rc;break;case Cr:w=zl;break;case cf:w=Ps;break;default:w=zl;break}m=mS(w,Jk.bind(null,e))}e.callbackPriority=c,e.callbackNode=m}function Jk(e,t){if(s3(),gg=rn,Iy=ie,(Ft&(ri|Xa))!==Or)throw new Error("Should not already be working.");var a=e.callbackNode,l=cs();if(l&&e.callbackNode!==a)return null;var c=mi(e,e===Ni?$r:ie);if(c===ie)return null;var p=!gc(e,c)&&!Qm(e,c)&&!t,m=p?e5(e,c):Hy(e,c);if(m!==ls){if(m===Wc){var w=tf(e);w!==ie&&(c=w,m=uS(e,w))}if(m===lg){var C=ug;throw Gc(e,ie),yu(e,c),sa(e,Un()),C}if(m===tS)yu(e,c);else{var R=!gc(e,c),M=e.current.alternate;if(R&&!YP(M)){if(m=Hy(e,c),m===Wc){var U=tf(e);U!==ie&&(c=U,m=uS(e,U))}if(m===lg){var F=ug;throw Gc(e,ie),yu(e,c),sa(e,Un()),F}}e.finishedWork=M,e.finishedLanes=c,WP(e,m,c)}}return sa(e,Un()),e.callbackNode===a?Jk.bind(null,e):null}function uS(e,t){var a=dg;if(Vl(e)){var l=Gc(e,t);l.flags|=Rn,dN(e.containerInfo)}var c=Hy(e,t);if(c!==Wc){var p=la;la=a,p!==null&&eR(p)}return c}function eR(e){la===null?la=e:la.push.apply(la,e)}function WP(e,t,a){switch(t){case ls:case lg:throw new Error("Root did not complete. This is a bug in React.");case Wc:{Kc(e,la,ss);break}case _y:{if(yu(e,a),nf(a)&&!gR()){var l=rS+Qk-Un();if(l>10){var c=mi(e,ie);if(c!==ie)break;var p=e.suspendedLanes;if(!Hl(p,a)){Pi(),lf(e,p);break}e.timeoutHandle=ab(Kc.bind(null,e,la,ss),l);break}}Kc(e,la,ss);break}case sg:{if(yu(e,a),O0(a))break;if(!gR()){var m=eh(e,a),w=m,C=Un()-w,R=d5(C)-C;if(R>10){e.timeoutHandle=ab(Kc.bind(null,e,la,ss),R);break}}Kc(e,la,ss);break}case Kk:{Kc(e,la,ss);break}default:throw new Error("Unknown root exit status.")}}function YP(e){for(var t=e;;){if(t.flags&Ad){var a=t.updateQueue;if(a!==null){var l=a.stores;if(l!==null)for(var c=0;c<l.length;c++){var p=l[c],m=p.getSnapshot,w=p.value;try{if(!De(m(),w))return!1}catch{return!1}}}}var C=t.child;if(t.subtreeFlags&Ad&&C!==null){C.return=t,t=C;continue}if(t===e)return!0;for(;t.sibling===null;){if(t.return===null||t.return===e)return!0;t=t.return}t.sibling.return=t.return,t=t.sibling}return!0}function yu(e,t){t=mc(t,zy),t=mc(t,cg),oh(e,t)}function tR(e){if(u3(),(Ft&(ri|Xa))!==Or)throw new Error("Should not already be working.");cs();var t=mi(e,ie);if(!vi(t,nt))return sa(e,Un()),null;var a=Hy(e,t);if(e.tag!==ou&&a===Wc){var l=tf(e);l!==ie&&(t=l,a=uS(e,l))}if(a===lg){var c=ug;throw Gc(e,ie),yu(e,t),sa(e,Un()),c}if(a===tS)throw new Error("Root did not complete. This is a bug in React.");var p=e.current.alternate;return e.finishedWork=p,e.finishedLanes=t,Kc(e,la,ss),sa(e,Un()),null}function GP(e,t){t!==ie&&(vc(e,xt(t,nt)),sa(e,Un()),(Ft&(ri|Xa))===Or&&(fg(),lu()))}function cS(e,t){var a=Ft;Ft|=Gk;try{return e(t)}finally{Ft=a,Ft===Or&&!Ao.isBatchingLegacy&&(fg(),ZE())}}function KP(e,t,a,l,c){var p=Ai(),m=ni.transition;try{return ni.transition=null,rr(yi),e(t,a,l,c)}finally{rr(p),ni.transition=m,Ft===Or&&fg()}}function us(e){mu!==null&&mu.tag===ou&&(Ft&(ri|Xa))===Or&&cs();var t=Ft;Ft|=Gk;var a=ni.transition,l=Ai();try{return ni.transition=null,rr(yi),e?e():void 0}finally{rr(l),ni.transition=a,Ft=t,(Ft&(ri|Xa))===Or&&lu()}}function nR(){return(Ft&(ri|Xa))!==Or}function By(e,t){wi(nS,gl,e),gl=xt(gl,t)}function dS(e){gl=nS.current,bi(nS,e)}function Gc(e,t){e.finishedWork=null,e.finishedLanes=ie;var a=e.timeoutHandle;if(a!==ob&&(e.timeoutHandle=ob,wz(a)),Wn!==null)for(var l=Wn.return;l!==null;){var c=l.alternate;$k(c,l),l=l.return}Ni=e;var p=Qc(e.current,null);return Wn=p,$r=gl=t,Ar=ls,ug=null,Ly=ie,cg=ie,zy=ie,dg=null,la=null,HN(),Eo.discardPendingWarnings(),p}function rR(e,t){do{var a=Wn;try{if(Zv(),RT(),jn(),eS.current=null,a===null||a.return===null){Ar=lg,ug=t,Wn=null;return}if(it&&a.mode&zt&&Ry(a,!0),et)if(Go(),t!==null&&typeof t=="object"&&typeof t.then=="function"){var l=t;ac(a,l,$r)}else Ha(a,t,$r);y3(e,a.return,a,t,$r),lR(a)}catch(c){t=c,Wn===a&&a!==null?(a=a.return,Wn=a):a=Wn;continue}return}while(!0)}function iR(){var e=Jw.current;return Jw.current=Sy,e===null?Sy:e}function aR(e){Jw.current=e}function QP(){rS=Un()}function mg(e){Ly=xt(e,Ly)}function qP(){Ar===ls&&(Ar=_y)}function fS(){(Ar===ls||Ar===_y||Ar===Wc)&&(Ar=sg),Ni!==null&&(Zo(Ly)||Zo(cg))&&yu(Ni,$r)}function XP(e){Ar!==sg&&(Ar=Wc),dg===null?dg=[e]:dg.push(e)}function ZP(){return Ar===ls}function Hy(e,t){var a=Ft;Ft|=ri;var l=iR();if(Ni!==e||$r!==t){if(Fr){var c=e.memoizedUpdaters;c.size>0&&(vg(e,$r),c.clear()),lh(e,t)}ss=uf(),Gc(e,t)}qp(t);do try{JP();break}catch(p){rR(e,p)}while(!0);if(Zv(),Ft=a,aR(l),Wn!==null)throw new Error("Cannot commit an incomplete root. This error is likely caused by a bug in React. Please file an issue.");return Dn(),Ni=null,$r=ie,Ar}function JP(){for(;Wn!==null;)oR(Wn)}function e5(e,t){var a=Ft;Ft|=ri;var l=iR();if(Ni!==e||$r!==t){if(Fr){var c=e.memoizedUpdaters;c.size>0&&(vg(e,$r),c.clear()),lh(e,t)}ss=uf(),fg(),Gc(e,t)}qp(t);do try{t5();break}catch(p){rR(e,p)}while(!0);return Zv(),aR(l),Ft=a,Wn!==null?(Xp(),ls):(Dn(),Ni=null,$r=ie,Ar)}function t5(){for(;Wn!==null&&!Bp();)oR(Wn)}function oR(e){var t=e.alternate;ln(e);var a;(e.mode&zt)!==Ge?(gw(e),a=pS(t,e,gl),Ry(e,!0)):a=pS(t,e,gl),jn(),e.memoizedProps=e.pendingProps,a===null?lR(e):Wn=a,eS.current=null}function lR(e){var t=e;do{var a=t.alternate,l=t.return;if((t.flags&_l)===Ye){ln(t);var c=void 0;if((t.mode&zt)===Ge?c=Ok(a,t,gl):(gw(t),c=Ok(a,t,gl),Ry(t,!1)),jn(),c!==null){Wn=c;return}}else{var p=q3(a,t);if(p!==null){p.flags&=Lm,Wn=p;return}if((t.mode&zt)!==Ge){Ry(t,!1);for(var m=t.actualDuration,w=t.child;w!==null;)m+=w.actualDuration,w=w.sibling;t.actualDuration=m}if(l!==null)l.flags|=_l,l.subtreeFlags=Ye,l.deletions=null;else{Ar=tS,Wn=null;return}}var C=t.sibling;if(C!==null){Wn=C;return}t=l,Wn=t}while(t!==null);Ar===ls&&(Ar=Kk)}function Kc(e,t,a){var l=Ai(),c=ni.transition;try{ni.transition=null,rr(yi),n5(e,t,a,l)}finally{ni.transition=c,rr(l)}return null}function n5(e,t,a,l){do cs();while(mu!==null);if(p5(),(Ft&(ri|Xa))!==Or)throw new Error("Should not already be working.");var c=e.finishedWork,p=e.finishedLanes;if(Um(p),c===null)return Ba(),null;if(p===ie&&v("root.finishedLanes should not be empty during a commit. This is a bug in React."),e.finishedWork=null,e.finishedLanes=ie,c===e.current)throw new Error("Cannot commit the same tree as before. This error is likely caused by a bug in React. Please file an issue.");e.callbackNode=null,e.callbackPriority=Xn;var m=xt(c.lanes,c.childLanes);Xm(e,m),e===Ni&&(Ni=null,Wn=null,$r=ie),((c.subtreeFlags&wr)!==Ye||(c.flags&wr)!==Ye)&&(Yc||(Yc=!0,oS=a,mS(zl,function(){return cs(),null})));var w=(c.subtreeFlags&(Vo|Ns|Wo|wr))!==Ye,C=(c.flags&(Vo|Ns|Wo|wr))!==Ye;if(w||C){var R=ni.transition;ni.transition=null;var M=Ai();rr(yi);var U=Ft;Ft|=Xa,eS.current=null,tP(e,c),ZT(),gP(e,c,p),hz(e.containerInfo),e.current=c,oc(p),mP(c,e,p),Pl(),Nm(),Ft=U,rr(M),ni.transition=R}else e.current=c,ZT();var F=Yc;if(Yc?(Yc=!1,mu=e,pg=p):(Jf=0,Fy=null),m=e.pendingLanes,m===ie&&(Zf=null),F||dR(e.current,!1),Wp(c.stateNode,l),Fr&&e.memoizedUpdaters.clear(),LP(),sa(e,Un()),t!==null)for(var q=e.onRecoverableError,Z=0;Z<t.length;Z++){var te=t[Z],ke=te.stack,Xe=te.digest;q(te.value,{componentStack:ke,digest:Xe})}if(Ny){Ny=!1;var We=iS;throw iS=null,We}return vi(pg,nt)&&e.tag!==ou&&cs(),m=e.pendingLanes,vi(m,nt)?(l3(),e===lS?hg++:(hg=0,lS=e)):hg=0,lu(),Ba(),null}function cs(){if(mu!==null){var e=ev(pg),t=Ir(Cr,e),a=ni.transition,l=Ai();try{return ni.transition=null,rr(t),i5()}finally{rr(l),ni.transition=a}}return!1}function r5(e){aS.push(e),Yc||(Yc=!0,mS(zl,function(){return cs(),null}))}function i5(){if(mu===null)return!1;var e=oS;oS=null;var t=mu,a=pg;if(mu=null,pg=ie,(Ft&(ri|Xa))!==Or)throw new Error("Cannot flush passive effects while already rendering.");sS=!0,Py=!1,Qp(a);var l=Ft;Ft|=Xa,CP(t.current),xP(t,t.current,a,e);{var c=aS;aS=[];for(var p=0;p<c.length;p++){var m=c[p];aP(t,m)}}Us(),dR(t.current,!0),Ft=l,lu(),Py?t===Fy?Jf++:(Jf=0,Fy=t):Jf=0,sS=!1,Py=!1,Yp(t);{var w=t.current.stateNode;w.effectDuration=0,w.passiveEffectDuration=0}return!0}function sR(e){return Zf!==null&&Zf.has(e)}function a5(e){Zf===null?Zf=new Set([e]):Zf.add(e)}function o5(e){Ny||(Ny=!0,iS=e)}var l5=o5;function uR(e,t,a){var l=Hc(a,t),c=ok(e,l,nt),p=uu(e,c,nt),m=Pi();p!==null&&(Ys(p,nt,m),sa(p,m))}function Tn(e,t,a){if(Z3(a),yg(!1),e.tag===_){uR(e,e,a);return}var l=null;for(l=t;l!==null;){if(l.tag===_){uR(l,e,a);return}else if(l.tag===O){var c=l.type,p=l.stateNode;if(typeof c.getDerivedStateFromError=="function"||typeof p.componentDidCatch=="function"&&!sR(p)){var m=Hc(a,e),w=jw(l,m,nt),C=uu(l,w,nt),R=Pi();C!==null&&(Ys(C,nt,R),sa(C,R));return}}l=l.return}v(`Internal React error: Attempted to capture a commit phase error inside a detached tree. This indicates a bug in React. Likely causes include deleting the same fiber more than once, committing an already-finished tree, or an inconsistent return pointer.

Error message:

%s`,a)}function s5(e,t,a){var l=e.pingCache;l!==null&&l.delete(t);var c=Pi();lf(e,a),v5(e),Ni===e&&Hl($r,a)&&(Ar===sg||Ar===_y&&nf($r)&&Un()-rS<Qk?Gc(e,ie):zy=xt(zy,a)),sa(e,c)}function cR(e,t){t===Xn&&(t=BP(e));var a=Pi(),l=aa(e,t);l!==null&&(Ys(l,t,a),sa(l,a))}function u5(e){var t=e.memoizedState,a=Xn;t!==null&&(a=t.retryLane),cR(e,a)}function c5(e,t){var a=Xn,l;switch(e.tag){case se:l=e.stateNode;var c=e.memoizedState;c!==null&&(a=c.retryLane);break;case bt:l=e.stateNode;break;default:throw new Error("Pinged unknown suspense boundary type. This is probably a bug in React.")}l!==null&&l.delete(t),cR(e,a)}function d5(e){return e<120?120:e<480?480:e<1080?1080:e<1920?1920:e<3e3?3e3:e<4320?4320:PP(e/1960)*1960}function f5(){if(hg>IP)throw hg=0,lS=null,new Error("Maximum update depth exceeded. This can happen when a component repeatedly calls setState inside componentWillUpdate or componentDidUpdate. React limits the number of nested updates to prevent infinite loops.");Jf>UP&&(Jf=0,Fy=null,v("Maximum update depth exceeded. This can happen when a component calls setState inside useEffect, but useEffect either doesn't have a dependency array, or one of the dependencies changes on every render."))}function p5(){Eo.flushLegacyContextWarning(),Eo.flushPendingUnsafeLifecycleWarnings()}function dR(e,t){ln(e),Vy(e,Gr,AP),t&&Vy(e,Ho,jP),Vy(e,Gr,OP),t&&Vy(e,Ho,$P),jn()}function Vy(e,t,a){for(var l=e,c=null;l!==null;){var p=l.subtreeFlags&t;l!==c&&l.child!==null&&p!==Ye?l=l.child:((l.flags&t)!==Ye&&a(l),l.sibling!==null?l=l.sibling:l=c=l.return)}}var Wy=null;function fR(e){{if((Ft&ri)!==Or||!(e.mode&Dt))return;var t=e.tag;if(t!==P&&t!==_&&t!==O&&t!==$&&t!==he&&t!==le&&t!==Oe)return;var a=lt(e)||"ReactComponent";if(Wy!==null){if(Wy.has(a))return;Wy.add(a)}else Wy=new Set([a]);var l=dr;try{ln(e),v("Can't perform a React state update on a component that hasn't mounted yet. This indicates that you have a side-effect in your render function that asynchronously later calls tries to update the component. Move this work to useEffect instead.")}finally{l?ln(e):jn()}}}var pS;{var h5=null;pS=function(e,t,a){var l=bR(h5,t);try{return Tk(e,t,a)}catch(p){if(kN()||p!==null&&typeof p=="object"&&typeof p.then=="function")throw p;if(Zv(),RT(),$k(e,t),bR(t,l),t.mode&zt&&gw(t),Xi(null,Tk,null,e,t,a),Lp()){var c=zp();typeof c=="object"&&c!==null&&c._suppressLogging&&typeof p=="object"&&p!==null&&!p._suppressLogging&&(p._suppressLogging=!0)}throw p}}}var pR=!1,hS;hS=new Set;function g5(e){if(ui&&!i3())switch(e.tag){case $:case he:case Oe:{var t=Wn&&lt(Wn)||"Unknown",a=t;if(!hS.has(a)){hS.add(a);var l=lt(e)||"Unknown";v("Cannot update a component (`%s`) while rendering a different component (`%s`). To locate the bad setState() call inside `%s`, follow the stack trace as described in https://reactjs.org/link/setstate-in-render",l,t,t)}break}case O:{pR||(v("Cannot update during an existing state transition (such as within `render`). Render methods should be a pure function of props and state."),pR=!0);break}}}function vg(e,t){if(Fr){var a=e.memoizedUpdaters;a.forEach(function(l){Zm(e,l,t)})}}var gS={};function mS(e,t){{var a=Ao.current;return a!==null?(a.push(t),gS):Ip(e,t)}}function hR(e){if(e!==gS)return Up(e)}function gR(){return Ao.current!==null}function m5(e){{if(e.mode&Dt){if(!Yk())return}else if(!NP()||Ft!==Or||e.tag!==$&&e.tag!==he&&e.tag!==Oe)return;if(Ao.current===null){var t=dr;try{ln(e),v(`An update to %s inside a test was not wrapped in act(...).

When testing, code that causes React state updates should be wrapped into act(...):

act(() => {
  /* fire events that update state */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://reactjs.org/link/wrap-tests-with-act`,lt(e))}finally{t?ln(e):jn()}}}}function v5(e){e.tag!==ou&&Yk()&&Ao.current===null&&v(`A suspended resource finished loading inside a test, but the event was not wrapped in act(...).

When testing, code that resolves suspended data should be wrapped into act(...):

act(() => {
  /* finish loading suspended data */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://reactjs.org/link/wrap-tests-with-act`)}function yg(e){Zk=e}var Za=null,ep=null,y5=function(e){Za=e};function tp(e){{if(Za===null)return e;var t=Za(e);return t===void 0?e:t.current}}function vS(e){return tp(e)}function yS(e){{if(Za===null)return e;var t=Za(e);if(t===void 0){if(e!=null&&typeof e.render=="function"){var a=tp(e.render);if(e.render!==a){var l={$$typeof:ce,render:a};return e.displayName!==void 0&&(l.displayName=e.displayName),l}}return e}return t.current}}function mR(e,t){{if(Za===null)return!1;var a=e.elementType,l=t.type,c=!1,p=typeof l=="object"&&l!==null?l.$$typeof:null;switch(e.tag){case O:{typeof l=="function"&&(c=!0);break}case $:{(typeof l=="function"||p===ut)&&(c=!0);break}case he:{(p===ce||p===ut)&&(c=!0);break}case le:case Oe:{(p===Rt||p===ut)&&(c=!0);break}default:return!1}if(c){var m=Za(a);if(m!==void 0&&m===Za(l))return!0}return!1}}function vR(e){{if(Za===null||typeof WeakSet!="function")return;ep===null&&(ep=new WeakSet),ep.add(e)}}var x5=function(e,t){{if(Za===null)return;var a=t.staleFamilies,l=t.updatedFamilies;cs(),us(function(){xS(e.current,l,a)})}},b5=function(e,t){{if(e.context!==wa)return;cs(),us(function(){xg(t,e,null,null)})}};function xS(e,t,a){{var l=e.alternate,c=e.child,p=e.sibling,m=e.tag,w=e.type,C=null;switch(m){case $:case Oe:case O:C=w;break;case he:C=w.render;break}if(Za===null)throw new Error("Expected resolveFamily to be set during hot reload.");var R=!1,M=!1;if(C!==null){var U=Za(C);U!==void 0&&(a.has(U)?M=!0:t.has(U)&&(m===O?M=!0:R=!0))}if(ep!==null&&(ep.has(e)||l!==null&&ep.has(l))&&(M=!0),M&&(e._debugNeedsRemount=!0),M||R){var F=aa(e,nt);F!==null&&jr(F,e,nt,rn)}c!==null&&!M&&xS(c,t,a),p!==null&&xS(p,t,a)}}var w5=function(e,t){{var a=new Set,l=new Set(t.map(function(c){return c.current}));return bS(e.current,l,a),a}};function bS(e,t,a){{var l=e.child,c=e.sibling,p=e.tag,m=e.type,w=null;switch(p){case $:case Oe:case O:w=m;break;case he:w=m.render;break}var C=!1;w!==null&&t.has(w)&&(C=!0),C?S5(e,a):l!==null&&bS(l,t,a),c!==null&&bS(c,t,a)}}function S5(e,t){{var a=C5(e,t);if(a)return;for(var l=e;;){switch(l.tag){case N:t.add(l.stateNode);return;case V:t.add(l.stateNode.containerInfo);return;case _:t.add(l.stateNode.containerInfo);return}if(l.return===null)throw new Error("Expected to reach root first.");l=l.return}}}function C5(e,t){for(var a=e,l=!1;;){if(a.tag===N)l=!0,t.add(a.stateNode);else if(a.child!==null){a.child.return=a,a=a.child;continue}if(a===e)return l;for(;a.sibling===null;){if(a.return===null||a.return===e)return l;a=a.return}a.sibling.return=a.return,a=a.sibling}return!1}var wS;{wS=!1;try{var yR=Object.preventExtensions({})}catch{wS=!0}}function E5(e,t,a,l){this.tag=e,this.key=a,this.elementType=null,this.type=null,this.stateNode=null,this.return=null,this.child=null,this.sibling=null,this.index=0,this.ref=null,this.pendingProps=t,this.memoizedProps=null,this.updateQueue=null,this.memoizedState=null,this.dependencies=null,this.mode=l,this.flags=Ye,this.subtreeFlags=Ye,this.deletions=null,this.lanes=ie,this.childLanes=ie,this.alternate=null,this.actualDuration=Number.NaN,this.actualStartTime=Number.NaN,this.selfBaseDuration=Number.NaN,this.treeBaseDuration=Number.NaN,this.actualDuration=0,this.actualStartTime=-1,this.selfBaseDuration=0,this.treeBaseDuration=0,this._debugSource=null,this._debugOwner=null,this._debugNeedsRemount=!1,this._debugHookTypes=null,!wS&&typeof Object.preventExtensions=="function"&&Object.preventExtensions(this)}var Sa=function(e,t,a,l){return new E5(e,t,a,l)};function SS(e){var t=e.prototype;return!!(t&&t.isReactComponent)}function T5(e){return typeof e=="function"&&!SS(e)&&e.defaultProps===void 0}function k5(e){if(typeof e=="function")return SS(e)?O:$;if(e!=null){var t=e.$$typeof;if(t===ce)return he;if(t===Rt)return le}return P}function Qc(e,t){var a=e.alternate;a===null?(a=Sa(e.tag,t,e.key,e.mode),a.elementType=e.elementType,a.type=e.type,a.stateNode=e.stateNode,a._debugSource=e._debugSource,a._debugOwner=e._debugOwner,a._debugHookTypes=e._debugHookTypes,a.alternate=e,e.alternate=a):(a.pendingProps=t,a.type=e.type,a.flags=Ye,a.subtreeFlags=Ye,a.deletions=null,a.actualDuration=0,a.actualStartTime=-1),a.flags=e.flags&qn,a.childLanes=e.childLanes,a.lanes=e.lanes,a.child=e.child,a.memoizedProps=e.memoizedProps,a.memoizedState=e.memoizedState,a.updateQueue=e.updateQueue;var l=e.dependencies;switch(a.dependencies=l===null?null:{lanes:l.lanes,firstContext:l.firstContext},a.sibling=e.sibling,a.index=e.index,a.ref=e.ref,a.selfBaseDuration=e.selfBaseDuration,a.treeBaseDuration=e.treeBaseDuration,a._debugNeedsRemount=e._debugNeedsRemount,a.tag){case P:case $:case Oe:a.type=tp(e.type);break;case O:a.type=vS(e.type);break;case he:a.type=yS(e.type);break}return a}function R5(e,t){e.flags&=qn|_n;var a=e.alternate;if(a===null)e.childLanes=ie,e.lanes=t,e.child=null,e.subtreeFlags=Ye,e.memoizedProps=null,e.memoizedState=null,e.updateQueue=null,e.dependencies=null,e.stateNode=null,e.selfBaseDuration=0,e.treeBaseDuration=0;else{e.childLanes=a.childLanes,e.lanes=a.lanes,e.child=a.child,e.subtreeFlags=Ye,e.deletions=null,e.memoizedProps=a.memoizedProps,e.memoizedState=a.memoizedState,e.updateQueue=a.updateQueue,e.type=a.type;var l=a.dependencies;e.dependencies=l===null?null:{lanes:l.lanes,firstContext:l.firstContext},e.selfBaseDuration=a.selfBaseDuration,e.treeBaseDuration=a.treeBaseDuration}return e}function D5(e,t,a){var l;return e===Bv?(l=Dt,t===!0&&(l|=mt,l|=cn)):l=Ge,Fr&&(l|=zt),Sa(_,null,null,l)}function CS(e,t,a,l,c,p){var m=P,w=e;if(typeof e=="function")SS(e)?(m=O,w=vS(w)):w=tp(w);else if(typeof e=="string")m=N;else e:switch(e){case oi:return xu(a.children,c,p,t);case Aa:m=Pe,c|=mt,(c&Dt)!==Ge&&(c|=cn);break;case ja:return M5(a,c,p,t);case Ee:return O5(a,c,p,t);case Re:return $5(a,c,p,t);case Fn:return xR(a,c,p,t);case yn:case $t:case Cn:case Lr:case St:default:{if(typeof e=="object"&&e!==null)switch(e.$$typeof){case ao:m=ue;break e;case L:m=de;break e;case ce:m=he,w=yS(w);break e;case Rt:m=le;break e;case ut:m=ft,w=null;break e}var C="";{(e===void 0||typeof e=="object"&&e!==null&&Object.keys(e).length===0)&&(C+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var R=l?lt(l):null;R&&(C+=`

Check the render method of \``+R+"`.")}throw new Error("Element type is invalid: expected a string (for built-in components) or a class/function (for composite components) "+("but got: "+(e==null?e:typeof e)+"."+C))}}var M=Sa(m,a,t,c);return M.elementType=e,M.type=w,M.lanes=p,M._debugOwner=l,M}function ES(e,t,a){var l=null;l=e._owner;var c=e.type,p=e.key,m=e.props,w=CS(c,p,m,l,t,a);return w._debugSource=e._source,w._debugOwner=e._owner,w}function xu(e,t,a,l){var c=Sa(xe,e,l,t);return c.lanes=a,c}function M5(e,t,a,l){typeof e.id!="string"&&v('Profiler must specify an "id" of type `string` as a prop. Received the type `%s` instead.',typeof e.id);var c=Sa(Le,e,l,t|zt);return c.elementType=ja,c.lanes=a,c.stateNode={effectDuration:0,passiveEffectDuration:0},c}function O5(e,t,a,l){var c=Sa(se,e,l,t);return c.elementType=Ee,c.lanes=a,c}function $5(e,t,a,l){var c=Sa(bt,e,l,t);return c.elementType=Re,c.lanes=a,c}function xR(e,t,a,l){var c=Sa(Be,e,l,t);c.elementType=Fn,c.lanes=a;var p={isHidden:!1};return c.stateNode=p,c}function TS(e,t,a){var l=Sa(J,e,null,t);return l.lanes=a,l}function A5(){var e=Sa(N,null,null,Ge);return e.elementType="DELETED",e}function j5(e){var t=Sa(Tt,null,null,Ge);return t.stateNode=e,t}function kS(e,t,a){var l=e.children!==null?e.children:[],c=Sa(V,l,e.key,t);return c.lanes=a,c.stateNode={containerInfo:e.containerInfo,pendingChildren:null,implementation:e.implementation},c}function bR(e,t){return e===null&&(e=Sa(P,null,null,Ge)),e.tag=t.tag,e.key=t.key,e.elementType=t.elementType,e.type=t.type,e.stateNode=t.stateNode,e.return=t.return,e.child=t.child,e.sibling=t.sibling,e.index=t.index,e.ref=t.ref,e.pendingProps=t.pendingProps,e.memoizedProps=t.memoizedProps,e.updateQueue=t.updateQueue,e.memoizedState=t.memoizedState,e.dependencies=t.dependencies,e.mode=t.mode,e.flags=t.flags,e.subtreeFlags=t.subtreeFlags,e.deletions=t.deletions,e.lanes=t.lanes,e.childLanes=t.childLanes,e.alternate=t.alternate,e.actualDuration=t.actualDuration,e.actualStartTime=t.actualStartTime,e.selfBaseDuration=t.selfBaseDuration,e.treeBaseDuration=t.treeBaseDuration,e._debugSource=t._debugSource,e._debugOwner=t._debugOwner,e._debugNeedsRemount=t._debugNeedsRemount,e._debugHookTypes=t._debugHookTypes,e}function _5(e,t,a,l,c){this.tag=t,this.containerInfo=e,this.pendingChildren=null,this.current=null,this.pingCache=null,this.finishedWork=null,this.timeoutHandle=ob,this.context=null,this.pendingContext=null,this.callbackNode=null,this.callbackPriority=Xn,this.eventTimes=of(ie),this.expirationTimes=of(rn),this.pendingLanes=ie,this.suspendedLanes=ie,this.pingedLanes=ie,this.expiredLanes=ie,this.mutableReadLanes=ie,this.finishedLanes=ie,this.entangledLanes=ie,this.entanglements=of(ie),this.identifierPrefix=l,this.onRecoverableError=c,this.mutableSourceEagerHydrationData=null,this.effectDuration=0,this.passiveEffectDuration=0;{this.memoizedUpdaters=new Set;for(var p=this.pendingUpdatersLaneMap=[],m=0;m<Jp;m++)p.push(new Set)}switch(t){case Bv:this._debugRootType=a?"hydrateRoot()":"createRoot()";break;case ou:this._debugRootType=a?"hydrate()":"render()";break}}function wR(e,t,a,l,c,p,m,w,C,R){var M=new _5(e,t,a,w,C),U=D5(t,p);M.current=U,U.stateNode=M;{var F={element:l,isDehydrated:a,cache:null,transitions:null,pendingSuspenseBoundaries:null};U.memoizedState=F}return Pb(U),M}var RS="18.3.1";function L5(e,t,a){var l=arguments.length>3&&arguments[3]!==void 0?arguments[3]:null;return Ti(l),{$$typeof:Mi,key:l==null?null:""+l,children:e,containerInfo:t,implementation:a}}var DS,MS;DS=!1,MS={};function SR(e){if(!e)return wa;var t=zs(e),a=vN(t);if(t.tag===O){var l=t.type;if(sl(l))return QE(t,l,a)}return a}function z5(e,t){{var a=zs(e);if(a===void 0){if(typeof e.render=="function")throw new Error("Unable to find node on an unmounted component.");var l=Object.keys(e).join(",");throw new Error("Argument appears to not be a ReactComponent. Keys: "+l)}var c=pi(a);if(c===null)return null;if(c.mode&mt){var p=lt(a)||"Component";if(!MS[p]){MS[p]=!0;var m=dr;try{ln(c),a.mode&mt?v("%s is deprecated in StrictMode. %s was passed an instance of %s which is inside StrictMode. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node",t,t,p):v("%s is deprecated in StrictMode. %s was passed an instance of %s which renders StrictMode children. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node",t,t,p)}finally{m?ln(m):jn()}}}return c.stateNode}}function CR(e,t,a,l,c,p,m,w){var C=!1,R=null;return wR(e,t,C,R,a,l,c,p,m)}function ER(e,t,a,l,c,p,m,w,C,R){var M=!0,U=wR(a,l,M,e,c,p,m,w,C);U.context=SR(null);var F=U.current,q=Pi(),Z=vu(F),te=as(q,Z);return te.callback=t??null,uu(F,te,Z),HP(U,Z,q),U}function xg(e,t,a,l){Vp(t,e);var c=t.current,p=Pi(),m=vu(c);zd(m);var w=SR(a);t.context===null?t.context=w:t.pendingContext=w,ui&&dr!==null&&!DS&&(DS=!0,v(`Render methods should be a pure function of props and state; triggering nested component updates from render is not allowed. If necessary, trigger nested updates in componentDidUpdate.

Check the render method of %s.`,lt(dr)||"Unknown"));var C=as(p,m);C.payload={element:e},l=l===void 0?null:l,l!==null&&(typeof l!="function"&&v("render(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",l),C.callback=l);var R=uu(c,C,m);return R!==null&&(jr(R,c,m,p),ry(R,c,m)),m}function Yy(e){var t=e.current;if(!t.child)return null;switch(t.child.tag){case N:return t.child.stateNode;default:return t.child.stateNode}}function N5(e){switch(e.tag){case _:{var t=e.stateNode;if(Vl(t)){var a=Km(t);GP(t,a)}break}case se:{us(function(){var c=aa(e,nt);if(c!==null){var p=Pi();jr(c,e,nt,p)}});var l=nt;OS(e,l);break}}}function TR(e,t){var a=e.memoizedState;a!==null&&a.dehydrated!==null&&(a.retryLane=ah(a.retryLane,t))}function OS(e,t){TR(e,t);var a=e.alternate;a&&TR(a,t)}function P5(e){if(e.tag===se){var t=Ws,a=aa(e,t);if(a!==null){var l=Pi();jr(a,e,t,l)}OS(e,t)}}function F5(e){if(e.tag===se){var t=vu(e),a=aa(e,t);if(a!==null){var l=Pi();jr(a,e,t,l)}OS(e,t)}}function kR(e){var t=va(e);return t===null?null:t.stateNode}var RR=function(e){return null};function I5(e){return RR(e)}var DR=function(e){return!1};function U5(e){return DR(e)}var MR=null,OR=null,$R=null,AR=null,jR=null,_R=null,LR=null,zR=null,NR=null;{var PR=function(e,t,a){var l=t[a],c=yt(e)?e.slice():gt({},e);return a+1===t.length?(yt(c)?c.splice(l,1):delete c[l],c):(c[l]=PR(e[l],t,a+1),c)},FR=function(e,t){return PR(e,t,0)},IR=function(e,t,a,l){var c=t[l],p=yt(e)?e.slice():gt({},e);if(l+1===t.length){var m=a[l];p[m]=p[c],yt(p)?p.splice(c,1):delete p[c]}else p[c]=IR(e[c],t,a,l+1);return p},UR=function(e,t,a){if(t.length!==a.length){S("copyWithRename() expects paths of the same length");return}else for(var l=0;l<a.length-1;l++)if(t[l]!==a[l]){S("copyWithRename() expects paths to be the same except for the deepest key");return}return IR(e,t,a,0)},BR=function(e,t,a,l){if(a>=t.length)return l;var c=t[a],p=yt(e)?e.slice():gt({},e);return p[c]=BR(e[c],t,a+1,l),p},HR=function(e,t,a){return BR(e,t,0,a)},$S=function(e,t){for(var a=e.memoizedState;a!==null&&t>0;)a=a.next,t--;return a};MR=function(e,t,a,l){var c=$S(e,t);if(c!==null){var p=HR(c.memoizedState,a,l);c.memoizedState=p,c.baseState=p,e.memoizedProps=gt({},e.memoizedProps);var m=aa(e,nt);m!==null&&jr(m,e,nt,rn)}},OR=function(e,t,a){var l=$S(e,t);if(l!==null){var c=FR(l.memoizedState,a);l.memoizedState=c,l.baseState=c,e.memoizedProps=gt({},e.memoizedProps);var p=aa(e,nt);p!==null&&jr(p,e,nt,rn)}},$R=function(e,t,a,l){var c=$S(e,t);if(c!==null){var p=UR(c.memoizedState,a,l);c.memoizedState=p,c.baseState=p,e.memoizedProps=gt({},e.memoizedProps);var m=aa(e,nt);m!==null&&jr(m,e,nt,rn)}},AR=function(e,t,a){e.pendingProps=HR(e.memoizedProps,t,a),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var l=aa(e,nt);l!==null&&jr(l,e,nt,rn)},jR=function(e,t){e.pendingProps=FR(e.memoizedProps,t),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var a=aa(e,nt);a!==null&&jr(a,e,nt,rn)},_R=function(e,t,a){e.pendingProps=UR(e.memoizedProps,t,a),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var l=aa(e,nt);l!==null&&jr(l,e,nt,rn)},LR=function(e){var t=aa(e,nt);t!==null&&jr(t,e,nt,rn)},zR=function(e){RR=e},NR=function(e){DR=e}}function B5(e){var t=pi(e);return t===null?null:t.stateNode}function H5(e){return null}function V5(){return dr}function W5(e){var t=e.findFiberByHostInstance,a=d.ReactCurrentDispatcher;return Hp({bundleType:e.bundleType,version:e.version,rendererPackageName:e.rendererPackageName,rendererConfig:e.rendererConfig,overrideHookState:MR,overrideHookStateDeletePath:OR,overrideHookStateRenamePath:$R,overrideProps:AR,overridePropsDeletePath:jR,overridePropsRenamePath:_R,setErrorHandler:zR,setSuspenseHandler:NR,scheduleUpdate:LR,currentDispatcherRef:a,findHostInstanceByFiber:B5,findFiberByHostInstance:t||H5,findHostInstancesForRefresh:w5,scheduleRefresh:x5,scheduleRoot:b5,setRefreshHandler:y5,getCurrentFiber:V5,reconcilerVersion:RS})}var VR=typeof reportError=="function"?reportError:function(e){console.error(e)};function AS(e){this._internalRoot=e}Gy.prototype.render=AS.prototype.render=function(e){var t=this._internalRoot;if(t===null)throw new Error("Cannot update an unmounted root.");{typeof arguments[1]=="function"?v("render(...): does not support the second callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."):Ky(arguments[1])?v("You passed a container to the second argument of root.render(...). You don't need to pass it again since you already passed it to create the root."):typeof arguments[1]<"u"&&v("You passed a second argument to root.render(...) but it only accepts one argument.");var a=t.containerInfo;if(a.nodeType!==Kn){var l=kR(t.current);l&&l.parentNode!==a&&v("render(...): It looks like the React-rendered content of the root container was removed without using React. This is not supported and will cause errors. Instead, call root.unmount() to empty a root's container.")}}xg(e,t,null,null)},Gy.prototype.unmount=AS.prototype.unmount=function(){typeof arguments[0]=="function"&&v("unmount(...): does not support a callback argument. To execute a side effect after rendering, declare it in a component body with useEffect().");var e=this._internalRoot;if(e!==null){this._internalRoot=null;var t=e.containerInfo;nR()&&v("Attempted to synchronously unmount a root while React was already rendering. React cannot finish unmounting the root until the current render has completed, which may lead to a race condition."),us(function(){xg(null,e,null,null)}),VE(t)}};function Y5(e,t){if(!Ky(e))throw new Error("createRoot(...): Target container is not a DOM element.");WR(e);var a=!1,l=!1,c="",p=VR;t!=null&&(t.hydrate?S("hydrate through createRoot is deprecated. Use ReactDOMClient.hydrateRoot(container, <App />) instead."):typeof t=="object"&&t!==null&&t.$$typeof===br&&v(`You passed a JSX element to createRoot. You probably meant to call root.render instead. Example usage:

  let root = createRoot(domContainer);
  root.render(<App />);`),t.unstable_strictMode===!0&&(a=!0),t.identifierPrefix!==void 0&&(c=t.identifierPrefix),t.onRecoverableError!==void 0&&(p=t.onRecoverableError),t.transitionCallbacks!==void 0&&t.transitionCallbacks);var m=CR(e,Bv,null,a,l,c,p);Lv(m.current,e);var w=e.nodeType===Kn?e.parentNode:e;return Th(w),new AS(m)}function Gy(e){this._internalRoot=e}function G5(e){e&&av(e)}Gy.prototype.unstable_scheduleHydration=G5;function K5(e,t,a){if(!Ky(e))throw new Error("hydrateRoot(...): Target container is not a DOM element.");WR(e),t===void 0&&v("Must provide initial children as second argument to hydrateRoot. Example usage: hydrateRoot(domContainer, <App />)");var l=a??null,c=a!=null&&a.hydratedSources||null,p=!1,m=!1,w="",C=VR;a!=null&&(a.unstable_strictMode===!0&&(p=!0),a.identifierPrefix!==void 0&&(w=a.identifierPrefix),a.onRecoverableError!==void 0&&(C=a.onRecoverableError));var R=ER(t,null,e,Bv,l,p,m,w,C);if(Lv(R.current,e),Th(e),c)for(var M=0;M<c.length;M++){var U=c[M];ZN(R,U)}return new Gy(R)}function Ky(e){return!!(e&&(e.nodeType===ci||e.nodeType===go||e.nodeType===Uu))}function bg(e){return!!(e&&(e.nodeType===ci||e.nodeType===go||e.nodeType===Uu||e.nodeType===Kn&&e.nodeValue===" react-mount-point-unstable "))}function WR(e){e.nodeType===ci&&e.tagName&&e.tagName.toUpperCase()==="BODY"&&v("createRoot(): Creating roots directly with document.body is discouraged, since its children are often manipulated by third-party scripts and browser extensions. This may lead to subtle reconciliation issues. Try using a container element created for your app."),zh(e)&&(e._reactRootContainer?v("You are calling ReactDOMClient.createRoot() on a container that was previously passed to ReactDOM.render(). This is not supported."):v("You are calling ReactDOMClient.createRoot() on a container that has already been passed to createRoot() before. Instead, call root.render() on the existing root instead if you want to update it."))}var Q5=d.ReactCurrentOwner,YR;YR=function(e){if(e._reactRootContainer&&e.nodeType!==Kn){var t=kR(e._reactRootContainer.current);t&&t.parentNode!==e&&v("render(...): It looks like the React-rendered content of this container was removed without using React. This is not supported and will cause errors. Instead, call ReactDOM.unmountComponentAtNode to empty a container.")}var a=!!e._reactRootContainer,l=jS(e),c=!!(l&&iu(l));c&&!a&&v("render(...): Replacing React-rendered children with a new root component. If you intended to update the children of this node, you should instead have the existing children update their state and render the new components instead of calling ReactDOM.render."),e.nodeType===ci&&e.tagName&&e.tagName.toUpperCase()==="BODY"&&v("render(): Rendering components directly into document.body is discouraged, since its children are often manipulated by third-party scripts and browser extensions. This may lead to subtle reconciliation issues. Try rendering into a container element created for your app.")};function jS(e){return e?e.nodeType===go?e.documentElement:e.firstChild:null}function GR(){}function q5(e,t,a,l,c){if(c){if(typeof l=="function"){var p=l;l=function(){var F=Yy(m);p.call(F)}}var m=ER(t,l,e,ou,null,!1,!1,"",GR);e._reactRootContainer=m,Lv(m.current,e);var w=e.nodeType===Kn?e.parentNode:e;return Th(w),us(),m}else{for(var C;C=e.lastChild;)e.removeChild(C);if(typeof l=="function"){var R=l;l=function(){var F=Yy(M);R.call(F)}}var M=CR(e,ou,null,!1,!1,"",GR);e._reactRootContainer=M,Lv(M.current,e);var U=e.nodeType===Kn?e.parentNode:e;return Th(U),us(function(){xg(t,M,a,l)}),M}}function X5(e,t){e!==null&&typeof e!="function"&&v("%s(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",t,e)}function Qy(e,t,a,l,c){YR(a),X5(c===void 0?null:c,"render");var p=a._reactRootContainer,m;if(!p)m=q5(a,t,e,c,l);else{if(m=p,typeof c=="function"){var w=c;c=function(){var C=Yy(m);w.call(C)}}xg(t,m,e,c)}return Yy(m)}var KR=!1;function Z5(e){{KR||(KR=!0,v("findDOMNode is deprecated and will be removed in the next major release. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node"));var t=Q5.current;if(t!==null&&t.stateNode!==null){var a=t.stateNode._warnedAboutRefsInRender;a||v("%s is accessing findDOMNode inside its render(). render() should be a pure function of props and state. It should never access something that requires stale data from the previous render, such as refs. Move this logic to componentDidMount and componentDidUpdate instead.",Pt(t.type)||"A component"),t.stateNode._warnedAboutRefsInRender=!0}}return e==null?null:e.nodeType===ci?e:z5(e,"findDOMNode")}function J5(e,t,a){if(v("ReactDOM.hydrate is no longer supported in React 18. Use hydrateRoot instead. Until you switch to the new API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!bg(t))throw new Error("Target container is not a DOM element.");{var l=zh(t)&&t._reactRootContainer===void 0;l&&v("You are calling ReactDOM.hydrate() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call hydrateRoot(container, element)?")}return Qy(null,e,t,!0,a)}function e4(e,t,a){if(v("ReactDOM.render is no longer supported in React 18. Use createRoot instead. Until you switch to the new API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!bg(t))throw new Error("Target container is not a DOM element.");{var l=zh(t)&&t._reactRootContainer===void 0;l&&v("You are calling ReactDOM.render() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call root.render(element)?")}return Qy(null,e,t,!1,a)}function t4(e,t,a,l){if(v("ReactDOM.unstable_renderSubtreeIntoContainer() is no longer supported in React 18. Consider using a portal instead. Until you switch to the createRoot API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!bg(a))throw new Error("Target container is not a DOM element.");if(e==null||!jl(e))throw new Error("parentComponent must be a valid React Component");return Qy(e,t,a,!1,l)}var QR=!1;function n4(e){if(QR||(QR=!0,v("unmountComponentAtNode is deprecated and will be removed in the next major release. Switch to the createRoot API. Learn more: https://reactjs.org/link/switch-to-createroot")),!bg(e))throw new Error("unmountComponentAtNode(...): Target container is not a DOM element.");{var t=zh(e)&&e._reactRootContainer===void 0;t&&v("You are calling ReactDOM.unmountComponentAtNode() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call root.unmount()?")}if(e._reactRootContainer){{var a=jS(e),l=a&&!iu(a);l&&v("unmountComponentAtNode(): The node you're attempting to unmount was rendered by another copy of React.")}return us(function(){Qy(null,null,e,!1,function(){e._reactRootContainer=null,VE(e)})}),!0}else{{var c=jS(e),p=!!(c&&iu(c)),m=e.nodeType===ci&&bg(e.parentNode)&&!!e.parentNode._reactRootContainer;p&&v("unmountComponentAtNode(): The node you're attempting to unmount was rendered by React and is not a top-level container. %s",m?"You may have accidentally passed in a React root node instead of its container.":"Instead, have the parent component update its state and rerender in order to remove this component.")}return!1}}A0(N5),uh(P5),j0(F5),df(Ai),tv(Jm),(typeof Map!="function"||Map.prototype==null||typeof Map.prototype.forEach!="function"||typeof Set!="function"||Set.prototype==null||typeof Set.prototype.clear!="function"||typeof Set.prototype.forEach!="function")&&v("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"),qu(iz),jm(cS,KP,us);function r4(e,t){var a=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;if(!Ky(t))throw new Error("Target container is not a DOM element.");return L5(e,t,null,a)}function i4(e,t,a,l){return t4(e,t,a,l)}var _S={usingClientEntryPoint:!1,Events:[iu,Af,zv,jp,As,cS]};function a4(e,t){return _S.usingClientEntryPoint||v('You are importing createRoot from "react-dom" which is not supported. You should instead import it from "react-dom/client".'),Y5(e,t)}function o4(e,t,a){return _S.usingClientEntryPoint||v('You are importing hydrateRoot from "react-dom" which is not supported. You should instead import it from "react-dom/client".'),K5(e,t,a)}function l4(e){return nR()&&v("flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task."),us(e)}var s4=W5({findFiberByHostInstance:_c,bundleType:1,version:RS,rendererPackageName:"react-dom"});if(!s4&&an&&window.top===window.self&&(navigator.userAgent.indexOf("Chrome")>-1&&navigator.userAgent.indexOf("Edge")===-1||navigator.userAgent.indexOf("Firefox")>-1)){var qR=window.location.protocol;/^(https?|file):$/.test(qR)&&console.info("%cDownload the React DevTools for a better development experience: https://reactjs.org/link/react-devtools"+(qR==="file:"?`
You might need to use a local HTTP server (instead of file://): https://reactjs.org/link/react-devtools-faq`:""),"font-weight:bold")}Bi.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=_S,Bi.createPortal=r4,Bi.createRoot=a4,Bi.findDOMNode=Z5,Bi.flushSync=l4,Bi.hydrate=J5,Bi.hydrateRoot=o4,Bi.render=e4,Bi.unmountComponentAtNode=n4,Bi.unstable_batchedUpdates=cS,Bi.unstable_renderSubtreeIntoContainer=i4,Bi.version=RS,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}(),Bi}var WS={};function YS(){if(!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u"||typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE!="function")){if(WS.NODE_ENV!=="production")throw new Error("^_^");try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(YS)}catch(o){console.error(o)}}}WS.NODE_ENV==="production"?(YS(),Zy.exports=aD()):Zy.exports=oD();var lD=Zy.exports,tx,sD={},Eg=lD;if(sD.NODE_ENV==="production")tx=Eg.createRoot,Eg.hydrateRoot;else{var GS=Eg.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;tx=function(o,r){GS.usingClientEntryPoint=!0;try{return Eg.createRoot(o,r)}finally{GS.usingClientEntryPoint=!1}}}var Hi=function(){return Hi=Object.assign||function(r){for(var s,d=1,g=arguments.length;d<g;d++){s=arguments[d];for(var b in s)Object.prototype.hasOwnProperty.call(s,b)&&(r[b]=s[b])}return r},Hi.apply(this,arguments)};function qc(o,r,s){if(s||arguments.length===2)for(var d=0,g=r.length,b;d<g;d++)(b||!(d in r))&&(b||(b=Array.prototype.slice.call(r,0,d)),b[d]=r[d]);return o.concat(b||Array.prototype.slice.call(r))}typeof SuppressedError=="function"&&SuppressedError;function uD(o){var r=Object.create(null);return function(s){return r[s]===void 0&&(r[s]=o(s)),r[s]}}var cD=/^((children|dangerouslySetInnerHTML|key|ref|autoFocus|defaultValue|defaultChecked|innerHTML|suppressContentEditableWarning|suppressHydrationWarning|valueLink|abbr|accept|acceptCharset|accessKey|action|allow|allowUserMedia|allowPaymentRequest|allowFullScreen|allowTransparency|alt|async|autoComplete|autoPlay|capture|cellPadding|cellSpacing|challenge|charSet|checked|cite|classID|className|cols|colSpan|content|contentEditable|contextMenu|controls|controlsList|coords|crossOrigin|data|dateTime|decoding|default|defer|dir|disabled|disablePictureInPicture|disableRemotePlayback|download|draggable|encType|enterKeyHint|fetchpriority|fetchPriority|form|formAction|formEncType|formMethod|formNoValidate|formTarget|frameBorder|headers|height|hidden|high|href|hrefLang|htmlFor|httpEquiv|id|inputMode|integrity|is|keyParams|keyType|kind|label|lang|list|loading|loop|low|marginHeight|marginWidth|max|maxLength|media|mediaGroup|method|min|minLength|multiple|muted|name|nonce|noValidate|open|optimum|pattern|placeholder|playsInline|popover|popoverTarget|popoverTargetAction|poster|preload|profile|radioGroup|readOnly|referrerPolicy|rel|required|reversed|role|rows|rowSpan|sandbox|scope|scoped|scrolling|seamless|selected|shape|size|sizes|slot|span|spellCheck|src|srcDoc|srcLang|srcSet|start|step|style|summary|tabIndex|target|title|translate|type|useMap|value|width|wmode|wrap|about|datatype|inlist|prefix|property|resource|typeof|vocab|autoCapitalize|autoCorrect|autoSave|color|incremental|fallback|inert|itemProp|itemScope|itemType|itemID|itemRef|on|option|results|security|unselectable|accentHeight|accumulate|additive|alignmentBaseline|allowReorder|alphabetic|amplitude|arabicForm|ascent|attributeName|attributeType|autoReverse|azimuth|baseFrequency|baselineShift|baseProfile|bbox|begin|bias|by|calcMode|capHeight|clip|clipPathUnits|clipPath|clipRule|colorInterpolation|colorInterpolationFilters|colorProfile|colorRendering|contentScriptType|contentStyleType|cursor|cx|cy|d|decelerate|descent|diffuseConstant|direction|display|divisor|dominantBaseline|dur|dx|dy|edgeMode|elevation|enableBackground|end|exponent|externalResourcesRequired|fill|fillOpacity|fillRule|filter|filterRes|filterUnits|floodColor|floodOpacity|focusable|fontFamily|fontSize|fontSizeAdjust|fontStretch|fontStyle|fontVariant|fontWeight|format|from|fr|fx|fy|g1|g2|glyphName|glyphOrientationHorizontal|glyphOrientationVertical|glyphRef|gradientTransform|gradientUnits|hanging|horizAdvX|horizOriginX|ideographic|imageRendering|in|in2|intercept|k|k1|k2|k3|k4|kernelMatrix|kernelUnitLength|kerning|keyPoints|keySplines|keyTimes|lengthAdjust|letterSpacing|lightingColor|limitingConeAngle|local|markerEnd|markerMid|markerStart|markerHeight|markerUnits|markerWidth|mask|maskContentUnits|maskUnits|mathematical|mode|numOctaves|offset|opacity|operator|order|orient|orientation|origin|overflow|overlinePosition|overlineThickness|panose1|paintOrder|pathLength|patternContentUnits|patternTransform|patternUnits|pointerEvents|points|pointsAtX|pointsAtY|pointsAtZ|preserveAlpha|preserveAspectRatio|primitiveUnits|r|radius|refX|refY|renderingIntent|repeatCount|repeatDur|requiredExtensions|requiredFeatures|restart|result|rotate|rx|ry|scale|seed|shapeRendering|slope|spacing|specularConstant|specularExponent|speed|spreadMethod|startOffset|stdDeviation|stemh|stemv|stitchTiles|stopColor|stopOpacity|strikethroughPosition|strikethroughThickness|string|stroke|strokeDasharray|strokeDashoffset|strokeLinecap|strokeLinejoin|strokeMiterlimit|strokeOpacity|strokeWidth|surfaceScale|systemLanguage|tableValues|targetX|targetY|textAnchor|textDecoration|textRendering|textLength|to|transform|u1|u2|underlinePosition|underlineThickness|unicode|unicodeBidi|unicodeRange|unitsPerEm|vAlphabetic|vHanging|vIdeographic|vMathematical|values|vectorEffect|version|vertAdvY|vertOriginX|vertOriginY|viewBox|viewTarget|visibility|widths|wordSpacing|writingMode|x|xHeight|x1|x2|xChannelSelector|xlinkActuate|xlinkArcrole|xlinkHref|xlinkRole|xlinkShow|xlinkTitle|xlinkType|xmlBase|xmlns|xmlnsXlink|xmlLang|xmlSpace|y|y1|y2|yChannelSelector|z|zoomAndPan|for|class|autofocus)|(([Dd][Aa][Tt][Aa]|[Aa][Rr][Ii][Aa]|x)-.*))$/,dD=uD(function(o){return cD.test(o)||o.charCodeAt(0)===111&&o.charCodeAt(1)===110&&o.charCodeAt(2)<91}),On="-ms-",op="-moz-",Zt="-webkit-",KS="comm",Tg="rule",nx="decl",fD="@import",pD="@namespace",QS="@keyframes",hD="@layer",qS=Math.abs,rx=String.fromCharCode,ix=Object.assign;function gD(o,r){return vr(o,0)^45?(((r<<2^vr(o,0))<<2^vr(o,1))<<2^vr(o,2))<<2^vr(o,3):0}function XS(o){return o.trim()}function vl(o,r){return(o=r.exec(o))?o[0]:o}function Mt(o,r,s){return o.replace(r,s)}function kg(o,r,s){return o.indexOf(r,s)}function vr(o,r){return o.charCodeAt(r)|0}function bu(o,r,s){return o.slice(r,s)}function Ja(o){return o.length}function ZS(o){return o.length}function lp(o,r){return r.push(o),o}function mD(o,r){return o.map(r).join("")}function JS(o,r){return o.filter(function(s){return!vl(s,r)})}var Rg=1,Xc=1,e1=0,Ea=0,lr=0,Zc="";function Dg(o,r,s,d,g,b,S,v){return{value:o,root:r,parent:s,type:d,props:g,children:b,line:Rg,column:Xc,length:S,return:"",siblings:v}}function fs(o,r){return ix(Dg("",null,null,"",null,null,0,o.siblings),o,{length:-o.length},r)}function Jc(o){for(;o.root;)o=fs(o.root,{children:[o]});lp(o,o.siblings)}function vD(){return lr}function yD(){return lr=Ea>0?vr(Zc,--Ea):0,Xc--,lr===10&&(Xc=1,Rg--),lr}function eo(){return lr=Ea<e1?vr(Zc,Ea++):0,Xc++,lr===10&&(Xc=1,Rg++),lr}function ps(){return vr(Zc,Ea)}function Mg(){return Ea}function Og(o,r){return bu(Zc,o,r)}function sp(o){switch(o){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function xD(o){return Rg=Xc=1,e1=Ja(Zc=o),Ea=0,[]}function bD(o){return Zc="",o}function ax(o){return XS(Og(Ea-1,ox(o===91?o+2:o===40?o+1:o)))}function wD(o){for(;(lr=ps())&&lr<33;)eo();return sp(o)>2||sp(lr)>3?"":" "}function SD(o,r){for(;--r&&eo()&&!(lr<48||lr>102||lr>57&&lr<65||lr>70&&lr<97););return Og(o,Mg()+(r<6&&ps()==32&&eo()==32))}function ox(o){for(;eo();)switch(lr){case o:return Ea;case 34:case 39:o!==34&&o!==39&&ox(lr);break;case 40:o===41&&ox(o);break;case 92:eo();break}return Ea}function CD(o,r){for(;eo()&&o+lr!==57;)if(o+lr===84&&ps()===47)break;return"/*"+Og(r,Ea-1)+"*"+rx(o===47?o:eo())}function ED(o){for(;!sp(ps());)eo();return Og(o,Ea)}function TD(o){return bD($g("",null,null,null,[""],o=xD(o),0,[0],o))}function $g(o,r,s,d,g,b,S,v,T){for(var $=0,O=0,P=S,_=0,V=0,N=0,J=1,xe=1,Pe=1,de=0,ue="",he=g,Le=b,se=d,le=ue;xe;)switch(N=de,de=eo()){case 40:if(N!=108&&vr(le,P-1)==58){kg(le+=Mt(ax(de),"&","&\f"),"&\f",qS($?v[$-1]:0))!=-1&&(Pe=-1);break}case 34:case 39:case 91:le+=ax(de);break;case 9:case 10:case 13:case 32:le+=wD(N);break;case 92:le+=SD(Mg()-1,7);continue;case 47:switch(ps()){case 42:case 47:lp(kD(CD(eo(),Mg()),r,s,T),T),(sp(N||1)==5||sp(ps()||1)==5)&&Ja(le)&&bu(le,-1,void 0)!==" "&&(le+=" ");break;default:le+="/"}break;case 123*J:v[$++]=Ja(le)*Pe;case 125*J:case 59:case 0:switch(de){case 0:case 125:xe=0;case 59+O:Pe==-1&&(le=Mt(le,/\f/g,"")),V>0&&(Ja(le)-P||J===0&&N===47)&&lp(V>32?n1(le+";",d,s,P-1,T):n1(Mt(le," ","")+";",d,s,P-2,T),T);break;case 59:le+=";";default:if(lp(se=t1(le,r,s,$,O,g,v,ue,he=[],Le=[],P,b),b),de===123)if(O===0)$g(le,r,se,se,he,b,P,v,Le);else{switch(_){case 99:if(vr(le,3)===110)break;case 108:if(vr(le,2)===97)break;default:O=0;case 100:case 109:case 115:}O?$g(o,se,se,d&&lp(t1(o,se,se,0,0,g,v,ue,g,he=[],P,Le),Le),g,Le,P,v,d?he:Le):$g(le,se,se,se,[""],Le,0,v,Le)}}$=O=V=0,J=Pe=1,ue=le="",P=S;break;case 58:P=1+Ja(le),V=N;default:if(J<1){if(de==123)--J;else if(de==125&&J++==0&&yD()==125)continue}switch(le+=rx(de),de*J){case 38:Pe=O>0?1:(le+="\f",-1);break;case 44:v[$++]=(Ja(le)-1)*Pe,Pe=1;break;case 64:ps()===45&&(le+=ax(eo())),_=ps(),O=P=Ja(ue=le+=ED(Mg())),de++;break;case 45:N===45&&Ja(le)==2&&(J=0)}}return b}function t1(o,r,s,d,g,b,S,v,T,$,O,P){for(var _=g-1,V=g===0?b:[""],N=ZS(V),J=0,xe=0,Pe=0;J<d;++J)for(var de=0,ue=bu(o,_+1,_=qS(xe=S[J])),he=o;de<N;++de)(he=XS(xe>0?V[de]+" "+ue:Mt(ue,/&\f/g,V[de])))&&(T[Pe++]=he);return Dg(o,r,s,g===0?Tg:v,T,$,O,P)}function kD(o,r,s,d){return Dg(o,r,s,KS,rx(vD()),bu(o,2,-2),0,d)}function n1(o,r,s,d,g){return Dg(o,r,s,nx,bu(o,0,d),bu(o,d+1,-1),d,g)}function r1(o,r,s){switch(gD(o,r)){case 5103:return Zt+"print-"+o+o;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return Zt+o+o;case 4855:return Zt+o.replace("add","source-over").replace("substract","source-out").replace("intersect","source-in").replace("exclude","xor")+o;case 4789:return op+o+o;case 5349:case 4246:case 4810:case 6968:case 2756:return Zt+o+op+o+On+o+o;case 5936:switch(vr(o,r+11)){case 114:return Zt+o+On+Mt(o,/[svh]\w+-[tblr]{2}/,"tb")+o;case 108:return Zt+o+On+Mt(o,/[svh]\w+-[tblr]{2}/,"tb-rl")+o;case 45:return Zt+o+On+Mt(o,/[svh]\w+-[tblr]{2}/,"lr")+o}case 6828:case 4268:case 2903:return Zt+o+On+o+o;case 6165:return Zt+o+On+"flex-"+o+o;case 5187:return Zt+o+Mt(o,/(\w+).+(:[^]+)/,Zt+"box-$1$2"+On+"flex-$1$2")+o;case 5443:return Zt+o+On+"flex-item-"+Mt(o,/flex-|-self/g,"")+(vl(o,/flex-|baseline/)?"":On+"grid-row-"+Mt(o,/flex-|-self/g,""))+o;case 4675:return Zt+o+On+"flex-line-pack"+Mt(o,/align-content|flex-|-self/g,"")+o;case 5548:return Zt+o+On+Mt(o,"shrink","negative")+o;case 5292:return Zt+o+On+Mt(o,"basis","preferred-size")+o;case 6060:return Zt+"box-"+Mt(o,"-grow","")+Zt+o+On+Mt(o,"grow","positive")+o;case 4554:return Zt+Mt(o,/([^-])(transform)/g,"$1"+Zt+"$2")+o;case 6187:return Mt(Mt(Mt(o,/(zoom-|grab)/,Zt+"$1"),/(image-set)/,Zt+"$1"),o,"")+o;case 5495:case 3959:return Mt(o,/(image-set\([^]*)/,Zt+"$1$`$1");case 4968:return Mt(Mt(o,/(.+:)(flex-)?(.*)/,Zt+"box-pack:$3"+On+"flex-pack:$3"),/space-between/,"justify")+Zt+o+o;case 4200:if(!vl(o,/flex-|baseline/))return On+"grid-column-align"+bu(o,r)+o;break;case 2592:case 3360:return On+Mt(o,"template-","")+o;case 4384:case 3616:return s&&s.some(function(d,g){return r=g,vl(d.props,/grid-\w+-end/)})?~kg(o+(s=s[r].value),"span",0)?o:On+Mt(o,"-start","")+o+On+"grid-row-span:"+(~kg(s,"span",0)?vl(s,/\d+/):+vl(s,/\d+/)-+vl(o,/\d+/))+";":On+Mt(o,"-start","")+o;case 4896:case 4128:return s&&s.some(function(d){return vl(d.props,/grid-\w+-start/)})?o:On+Mt(Mt(o,"-end","-span"),"span ","")+o;case 4095:case 3583:case 4068:case 2532:return Mt(o,/(.+)-inline(.+)/,Zt+"$1$2")+o;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(Ja(o)-1-r>6)switch(vr(o,r+1)){case 109:if(vr(o,r+4)!==45)break;case 102:return Mt(o,/(.+:)(.+)-([^]+)/,"$1"+Zt+"$2-$3$1"+op+(vr(o,r+3)==108?"$3":"$2-$3"))+o;case 115:return~kg(o,"stretch",0)?r1(Mt(o,"stretch","fill-available"),r,s)+o:o}break;case 5152:case 5920:return Mt(o,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(d,g,b,S,v,T,$){return On+g+":"+b+$+(S?On+g+"-span:"+(v?T:+T-+b)+$:"")+o});case 4949:if(vr(o,r+6)===121)return Mt(o,":",":"+Zt)+o;break;case 6444:switch(vr(o,vr(o,14)===45?18:11)){case 120:return Mt(o,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,"$1"+Zt+(vr(o,14)===45?"inline-":"")+"box$3$1"+Zt+"$2$3$1"+On+"$2box$3")+o;case 100:return Mt(o,":",":"+On)+o}break;case 5719:case 2647:case 2135:case 3927:case 2391:return Mt(o,"scroll-","scroll-snap-")+o}return o}function Ag(o,r){for(var s="",d=0;d<o.length;d++)s+=r(o[d],d,o,r)||"";return s}function RD(o,r,s,d){switch(o.type){case hD:if(o.children.length)break;case fD:case pD:case nx:return o.return=o.return||o.value;case KS:return"";case QS:return o.return=o.value+"{"+Ag(o.children,d)+"}";case Tg:if(!Ja(o.value=o.props.join(",")))return""}return Ja(s=Ag(o.children,d))?o.return=o.value+"{"+s+"}":""}function DD(o){var r=ZS(o);return function(s,d,g,b){for(var S="",v=0;v<r;v++)S+=o[v](s,d,g,b)||"";return S}}function MD(o){return function(r){r.root||(r=r.return)&&o(r)}}function OD(o,r,s,d){if(o.length>-1&&!o.return)switch(o.type){case nx:o.return=r1(o.value,o.length,s);return;case QS:return Ag([fs(o,{value:Mt(o.value,"@","@"+Zt)})],d);case Tg:if(o.length)return mD(s=o.props,function(g){switch(vl(g,d=/(::plac\w+|:read-\w+)/)){case":read-only":case":read-write":Jc(fs(o,{props:[Mt(g,/:(read-\w+)/,":"+op+"$1")]})),Jc(fs(o,{props:[g]})),ix(o,{props:JS(s,d)});break;case"::placeholder":Jc(fs(o,{props:[Mt(g,/:(plac\w+)/,":"+Zt+"input-$1")]})),Jc(fs(o,{props:[Mt(g,/:(plac\w+)/,":"+op+"$1")]})),Jc(fs(o,{props:[Mt(g,/:(plac\w+)/,On+"input-$1")]})),Jc(fs(o,{props:[g]})),ix(o,{props:JS(s,d)});break}return""})}}var $D={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},Jt={},wu=typeof process<"u"&&Jt!==void 0&&(Jt.REACT_APP_SC_ATTR||Jt.SC_ATTR)||"data-styled",i1="active",a1="data-styled-version",jg="6.3.8",lx=`/*!sc*/
`,_g=typeof window<"u"&&typeof document<"u",Su=Nn.createContext===void 0,AD=!!(typeof SC_DISABLE_SPEEDY=="boolean"?SC_DISABLE_SPEEDY:typeof process<"u"&&Jt!==void 0&&Jt.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&Jt.REACT_APP_SC_DISABLE_SPEEDY!==""?Jt.REACT_APP_SC_DISABLE_SPEEDY!=="false"&&Jt.REACT_APP_SC_DISABLE_SPEEDY:typeof process<"u"&&Jt!==void 0&&Jt.SC_DISABLE_SPEEDY!==void 0&&Jt.SC_DISABLE_SPEEDY!==""?Jt.SC_DISABLE_SPEEDY!=="false"&&Jt.SC_DISABLE_SPEEDY:Jt.NODE_ENV!=="production"),o1=/invalid hook call/i,Lg=new Set,jD=function(o,r){if(Jt.NODE_ENV!=="production"){if(Su)return;var s=r?' with the id of "'.concat(r,'"'):"",d="The component ".concat(o).concat(s,` has been created dynamically.
`)+`You may see this warning because you've called styled inside another component.
To resolve this only create new StyledComponents outside of any render method and function component.
See https://styled-components.com/docs/basics#define-styled-components-outside-of-the-render-method for more info.
`,g=console.error;try{var b=!0;console.error=function(S){for(var v=[],T=1;T<arguments.length;T++)v[T-1]=arguments[T];o1.test(S)?(b=!1,Lg.delete(d)):g.apply(void 0,qc([S],v,!1))},typeof Nn.useState=="function"&&Nn.useState(null),b&&!Lg.has(d)&&(console.warn(d),Lg.add(d))}catch(S){o1.test(S.message)&&Lg.delete(d)}finally{console.error=g}}},zg=Object.freeze([]),ed=Object.freeze({});function _D(o,r,s){return s===void 0&&(s=ed),o.theme!==s.theme&&o.theme||r||s.theme}var sx=new Set(["a","abbr","address","area","article","aside","audio","b","bdi","bdo","blockquote","body","button","br","canvas","caption","cite","code","col","colgroup","data","datalist","dd","del","details","dfn","dialog","div","dl","dt","em","embed","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","html","i","iframe","img","input","ins","kbd","label","legend","li","main","map","mark","menu","meter","nav","object","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","search","section","select","slot","small","span","strong","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","u","ul","var","video","wbr","circle","clipPath","defs","ellipse","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feDropShadow","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feImage","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence","filter","foreignObject","g","image","line","linearGradient","marker","mask","path","pattern","polygon","polyline","radialGradient","rect","stop","svg","switch","symbol","text","textPath","tspan","use"]),LD=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,zD=/(^-|-$)/g;function l1(o){return o.replace(LD,"-").replace(zD,"")}var ND=/(a)(d)/gi,s1=function(o){return String.fromCharCode(o+(o>25?39:97))};function ux(o){var r,s="";for(r=Math.abs(o);r>52;r=r/52|0)s=s1(r%52)+s;return(s1(r%52)+s).replace(ND,"$1-$2")}var cx,Cu=function(o,r){for(var s=r.length;s;)o=33*o^r.charCodeAt(--s);return o},u1=function(o){return Cu(5381,o)};function PD(o){return ux(u1(o)>>>0)}function c1(o){return Jt.NODE_ENV!=="production"&&typeof o=="string"&&o||o.displayName||o.name||"Component"}function dx(o){return typeof o=="string"&&(Jt.NODE_ENV==="production"||o.charAt(0)===o.charAt(0).toLowerCase())}var d1=typeof Symbol=="function"&&Symbol.for,f1=d1?Symbol.for("react.memo"):60115,FD=d1?Symbol.for("react.forward_ref"):60112,ID={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},UD={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},p1={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},BD=((cx={})[FD]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},cx[f1]=p1,cx);function h1(o){return("type"in(r=o)&&r.type.$$typeof)===f1?p1:"$$typeof"in o?BD[o.$$typeof]:ID;var r}var HD=Object.defineProperty,VD=Object.getOwnPropertyNames,g1=Object.getOwnPropertySymbols,WD=Object.getOwnPropertyDescriptor,YD=Object.getPrototypeOf,m1=Object.prototype;function v1(o,r,s){if(typeof r!="string"){if(m1){var d=YD(r);d&&d!==m1&&v1(o,d,s)}var g=VD(r);g1&&(g=g.concat(g1(r)));for(var b=h1(o),S=h1(r),v=0;v<g.length;++v){var T=g[v];if(!(T in UD||s&&s[T]||S&&T in S||b&&T in b)){var $=WD(r,T);try{HD(o,T,$)}catch{}}}}return o}function td(o){return typeof o=="function"}function fx(o){return typeof o=="object"&&"styledComponentId"in o}function Eu(o,r){return o&&r?"".concat(o," ").concat(r):o||r||""}function y1(o,r){if(o.length===0)return"";for(var s=o[0],d=1;d<o.length;d++)s+=o[d];return s}function nd(o){return o!==null&&typeof o=="object"&&o.constructor.name===Object.name&&!("props"in o&&o.$$typeof)}function px(o,r,s){if(s===void 0&&(s=!1),!s&&!nd(o)&&!Array.isArray(o))return r;if(Array.isArray(r))for(var d=0;d<r.length;d++)o[d]=px(o[d],r[d]);else if(nd(r))for(var d in r)o[d]=px(o[d],r[d]);return o}function hx(o,r){Object.defineProperty(o,"toString",{value:r})}var GD=Jt.NODE_ENV!=="production"?{1:`Cannot create styled-component for component: %s.

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
`,18:"ThemeProvider: Please make sure your useTheme hook is within a `<ThemeProvider>`"}:{};function KD(){for(var o=[],r=0;r<arguments.length;r++)o[r]=arguments[r];for(var s=o[0],d=[],g=1,b=o.length;g<b;g+=1)d.push(o[g]);return d.forEach(function(S){s=s.replace(/%[a-z]/,S)}),s}function rd(o){for(var r=[],s=1;s<arguments.length;s++)r[s-1]=arguments[s];return Jt.NODE_ENV==="production"?new Error("An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#".concat(o," for more information.").concat(r.length>0?" Args: ".concat(r.join(", ")):"")):new Error(KD.apply(void 0,qc([GD[o]],r,!1)).trim())}var QD=function(){function o(r){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=r}return o.prototype.indexOfGroup=function(r){for(var s=0,d=0;d<r;d++)s+=this.groupSizes[d];return s},o.prototype.insertRules=function(r,s){if(r>=this.groupSizes.length){for(var d=this.groupSizes,g=d.length,b=g;r>=b;)if((b<<=1)<0)throw rd(16,"".concat(r));this.groupSizes=new Uint32Array(b),this.groupSizes.set(d),this.length=b;for(var S=g;S<b;S++)this.groupSizes[S]=0}for(var v=this.indexOfGroup(r+1),T=(S=0,s.length);S<T;S++)this.tag.insertRule(v,s[S])&&(this.groupSizes[r]++,v++)},o.prototype.clearGroup=function(r){if(r<this.length){var s=this.groupSizes[r],d=this.indexOfGroup(r),g=d+s;this.groupSizes[r]=0;for(var b=d;b<g;b++)this.tag.deleteRule(d)}},o.prototype.getGroup=function(r){var s="";if(r>=this.length||this.groupSizes[r]===0)return s;for(var d=this.groupSizes[r],g=this.indexOfGroup(r),b=g+d,S=g;S<b;S++)s+="".concat(this.tag.getRule(S)).concat(lx);return s},o}(),qD=1<<30,Ng=new Map,Pg=new Map,Fg=1,up=function(o){if(Ng.has(o))return Ng.get(o);for(;Pg.has(Fg);)Fg++;var r=Fg++;if(Jt.NODE_ENV!=="production"&&((0|r)<0||r>qD))throw rd(16,"".concat(r));return Ng.set(o,r),Pg.set(r,o),r},XD=function(o,r){Fg=r+1,Ng.set(o,r),Pg.set(r,o)},ZD="style[".concat(wu,"][").concat(a1,'="').concat(jg,'"]'),JD=new RegExp("^".concat(wu,'\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)')),eM=function(o,r,s){for(var d,g=s.split(","),b=0,S=g.length;b<S;b++)(d=g[b])&&o.registerName(r,d)},tM=function(o,r){for(var s,d=((s=r.textContent)!==null&&s!==void 0?s:"").split(lx),g=[],b=0,S=d.length;b<S;b++){var v=d[b].trim();if(v){var T=v.match(JD);if(T){var $=0|parseInt(T[1],10),O=T[2];$!==0&&(XD(O,$),eM(o,O,T[3]),o.getTag().insertRules($,g)),g.length=0}else g.push(v)}}},x1=function(o){for(var r=document.querySelectorAll(ZD),s=0,d=r.length;s<d;s++){var g=r[s];g&&g.getAttribute(wu)!==i1&&(tM(o,g),g.parentNode&&g.parentNode.removeChild(g))}};function nM(){return typeof __webpack_nonce__<"u"?__webpack_nonce__:null}var b1=function(o){var r=document.head,s=o||r,d=document.createElement("style"),g=function(v){var T=Array.from(v.querySelectorAll("style[".concat(wu,"]")));return T[T.length-1]}(s),b=g!==void 0?g.nextSibling:null;d.setAttribute(wu,i1),d.setAttribute(a1,jg);var S=nM();return S&&d.setAttribute("nonce",S),s.insertBefore(d,b),d},rM=function(){function o(r){this.element=b1(r),this.element.appendChild(document.createTextNode("")),this.sheet=function(s){if(s.sheet)return s.sheet;for(var d=document.styleSheets,g=0,b=d.length;g<b;g++){var S=d[g];if(S.ownerNode===s)return S}throw rd(17)}(this.element),this.length=0}return o.prototype.insertRule=function(r,s){try{return this.sheet.insertRule(s,r),this.length++,!0}catch{return!1}},o.prototype.deleteRule=function(r){this.sheet.deleteRule(r),this.length--},o.prototype.getRule=function(r){var s=this.sheet.cssRules[r];return s&&s.cssText?s.cssText:""},o}(),iM=function(){function o(r){this.element=b1(r),this.nodes=this.element.childNodes,this.length=0}return o.prototype.insertRule=function(r,s){if(r<=this.length&&r>=0){var d=document.createTextNode(s);return this.element.insertBefore(d,this.nodes[r]||null),this.length++,!0}return!1},o.prototype.deleteRule=function(r){this.element.removeChild(this.nodes[r]),this.length--},o.prototype.getRule=function(r){return r<this.length?this.nodes[r].textContent:""},o}(),aM=function(){function o(r){this.rules=[],this.length=0}return o.prototype.insertRule=function(r,s){return r<=this.length&&(this.rules.splice(r,0,s),this.length++,!0)},o.prototype.deleteRule=function(r){this.rules.splice(r,1),this.length--},o.prototype.getRule=function(r){return r<this.length?this.rules[r]:""},o}(),w1=_g,oM={isServer:!_g,useCSSOMInjection:!AD},S1=function(){function o(r,s,d){r===void 0&&(r=ed),s===void 0&&(s={});var g=this;this.options=Hi(Hi({},oM),r),this.gs=s,this.names=new Map(d),this.server=!!r.isServer,!this.server&&_g&&w1&&(w1=!1,x1(this)),hx(this,function(){return function(b){for(var S=b.getTag(),v=S.length,T="",$=function(P){var _=function(Pe){return Pg.get(Pe)}(P);if(_===void 0)return"continue";var V=b.names.get(_),N=S.getGroup(P);if(V===void 0||!V.size||N.length===0)return"continue";var J="".concat(wu,".g").concat(P,'[id="').concat(_,'"]'),xe="";V!==void 0&&V.forEach(function(Pe){Pe.length>0&&(xe+="".concat(Pe,","))}),T+="".concat(N).concat(J,'{content:"').concat(xe,'"}').concat(lx)},O=0;O<v;O++)$(O);return T}(g)})}return o.registerId=function(r){return up(r)},o.prototype.rehydrate=function(){!this.server&&_g&&x1(this)},o.prototype.reconstructWithOptions=function(r,s){return s===void 0&&(s=!0),new o(Hi(Hi({},this.options),r),this.gs,s&&this.names||void 0)},o.prototype.allocateGSInstance=function(r){return this.gs[r]=(this.gs[r]||0)+1},o.prototype.getTag=function(){return this.tag||(this.tag=(r=function(s){var d=s.useCSSOMInjection,g=s.target;return s.isServer?new aM(g):d?new rM(g):new iM(g)}(this.options),new QD(r)));var r},o.prototype.hasNameForId=function(r,s){return this.names.has(r)&&this.names.get(r).has(s)},o.prototype.registerName=function(r,s){if(up(r),this.names.has(r))this.names.get(r).add(s);else{var d=new Set;d.add(s),this.names.set(r,d)}},o.prototype.insertRules=function(r,s,d){this.registerName(r,s),this.getTag().insertRules(up(r),d)},o.prototype.clearNames=function(r){this.names.has(r)&&this.names.get(r).clear()},o.prototype.clearRules=function(r){this.getTag().clearGroup(up(r)),this.clearNames(r)},o.prototype.clearTag=function(){this.tag=void 0},o}(),lM=/&/g,id=47;function C1(o){if(o.indexOf("}")===-1)return!1;for(var r=o.length,s=0,d=0,g=!1,b=0;b<r;b++){var S=o.charCodeAt(b);if(d!==0||g||S!==id||o.charCodeAt(b+1)!==42)if(g)S===42&&o.charCodeAt(b+1)===id&&(g=!1,b++);else if(S!==34&&S!==39||b!==0&&o.charCodeAt(b-1)===92){if(d===0){if(S===123)s++;else if(S===125&&--s<0)return!0}}else d===0?d=S:d===S&&(d=0);else g=!0,b++}return s!==0||d!==0}function E1(o,r){return o.map(function(s){return s.type==="rule"&&(s.value="".concat(r," ").concat(s.value),s.value=s.value.replaceAll(",",",".concat(r," ")),s.props=s.props.map(function(d){return"".concat(r," ").concat(d)})),Array.isArray(s.children)&&s.type!=="@keyframes"&&(s.children=E1(s.children,r)),s})}function sM(o){var r,s,d,g=ed,b=g.options,S=b===void 0?ed:b,v=g.plugins,T=v===void 0?zg:v,$=function(_,V,N){return N.startsWith(s)&&N.endsWith(s)&&N.replaceAll(s,"").length>0?".".concat(r):_},O=T.slice();O.push(function(_){_.type===Tg&&_.value.includes("&")&&(_.props[0]=_.props[0].replace(lM,s).replace(d,$))}),S.prefix&&O.push(OD),O.push(RD);var P=function(_,V,N,J){V===void 0&&(V=""),N===void 0&&(N=""),J===void 0&&(J="&"),r=J,s=V,d=new RegExp("\\".concat(s,"\\b"),"g");var xe=function(ue){if(!C1(ue))return ue;for(var he=ue.length,Le="",se=0,le=0,Oe=0,ft=!1,He=0;He<he;He++){var Tt=ue.charCodeAt(He);if(Oe!==0||ft||Tt!==id||ue.charCodeAt(He+1)!==42)if(ft)Tt===42&&ue.charCodeAt(He+1)===id&&(ft=!1,He++);else if(Tt!==34&&Tt!==39||He!==0&&ue.charCodeAt(He-1)===92){if(Oe===0)if(Tt===123)le++;else if(Tt===125){if(--le<0){for(var bt=He+1;bt<he;){var rt=ue.charCodeAt(bt);if(rt===59||rt===10)break;bt++}bt<he&&ue.charCodeAt(bt)===59&&bt++,le=0,He=bt-1,se=bt;continue}le===0&&(Le+=ue.substring(se,He+1),se=He+1)}else Tt===59&&le===0&&(Le+=ue.substring(se,He+1),se=He+1)}else Oe===0?Oe=Tt:Oe===Tt&&(Oe=0);else ft=!0,He++}if(se<he){var Be=ue.substring(se);C1(Be)||(Le+=Be)}return Le}(function(ue){if(ue.indexOf("//")===-1)return ue;for(var he=ue.length,Le=[],se=0,le=0,Oe=0,ft=0;le<he;){var He=ue.charCodeAt(le);if(He!==34&&He!==39||le!==0&&ue.charCodeAt(le-1)===92)if(Oe===0)if(He===40&&le>=3&&(32|ue.charCodeAt(le-1))==108&&(32|ue.charCodeAt(le-2))==114&&(32|ue.charCodeAt(le-3))==117)ft=1,le++;else if(ft>0)He===41?ft--:He===40&&ft++,le++;else if(He===id&&le+1<he&&ue.charCodeAt(le+1)===id){for(le>se&&Le.push(ue.substring(se,le));le<he&&ue.charCodeAt(le)!==10;)le++;se=le}else le++;else le++;else Oe===0?Oe=He:Oe===He&&(Oe=0),le++}return se===0?ue:(se<he&&Le.push(ue.substring(se)),Le.join(""))}(_)),Pe=TD(N||V?"".concat(N," ").concat(V," { ").concat(xe," }"):xe);S.namespace&&(Pe=E1(Pe,S.namespace));var de=[];return Ag(Pe,DD(O.concat(MD(function(ue){return de.push(ue)})))),de};return P.hash=T.length?T.reduce(function(_,V){return V.name||rd(15),Cu(_,V.name)},5381).toString():"",P}var uM=new S1,gx=sM(),mx={shouldForwardProp:void 0,styleSheet:uM,stylis:gx},T1=Su?{Provider:function(o){return o.children},Consumer:function(o){return(0,o.children)(mx)}}:Nn.createContext(mx);T1.Consumer,Su||Nn.createContext(void 0);function k1(){return Su?mx:Nn.useContext(T1)}var R1=function(){function o(r,s){var d=this;this.inject=function(g,b){b===void 0&&(b=gx);var S=d.name+b.hash;g.hasNameForId(d.id,S)||g.insertRules(d.id,S,b(d.rules,S,"@keyframes"))},this.name=r,this.id="sc-keyframes-".concat(r),this.rules=s,hx(this,function(){throw rd(12,String(d.name))})}return o.prototype.getName=function(r){return r===void 0&&(r=gx),this.name+r.hash},o}();function cM(o,r){return r==null||typeof r=="boolean"||r===""?"":typeof r!="number"||r===0||o in $D||o.startsWith("--")?String(r).trim():"".concat(r,"px")}var dM=function(o){return o>="A"&&o<="Z"};function D1(o){for(var r="",s=0;s<o.length;s++){var d=o[s];if(s===1&&d==="-"&&o[0]==="-")return o;dM(d)?r+="-"+d.toLowerCase():r+=d}return r.startsWith("ms-")?"-"+r:r}var M1=function(o){return o==null||o===!1||o===""},O1=function(o){var r=[];for(var s in o){var d=o[s];o.hasOwnProperty(s)&&!M1(d)&&(Array.isArray(d)&&d.isCss||td(d)?r.push("".concat(D1(s),":"),d,";"):nd(d)?r.push.apply(r,qc(qc(["".concat(s," {")],O1(d),!1),["}"],!1)):r.push("".concat(D1(s),": ").concat(cM(s,d),";")))}return r};function Tu(o,r,s,d){if(M1(o))return[];if(fx(o))return[".".concat(o.styledComponentId)];if(td(o)){if(!td(b=o)||b.prototype&&b.prototype.isReactComponent||!r)return[o];var g=o(r);return Jt.NODE_ENV==="production"||typeof g!="object"||Array.isArray(g)||g instanceof R1||nd(g)||g===null||console.error("".concat(c1(o)," is not a styled component and cannot be referred to via component selector. See https://www.styled-components.com/docs/advanced#referring-to-other-components for more details.")),Tu(g,r,s,d)}var b;return o instanceof R1?s?(o.inject(s,d),[o.getName(d)]):[o]:nd(o)?O1(o):Array.isArray(o)?Array.prototype.concat.apply(zg,o.map(function(S){return Tu(S,r,s,d)})):[o.toString()]}function fM(o){for(var r=0;r<o.length;r+=1){var s=o[r];if(td(s)&&!fx(s))return!1}return!0}var pM=u1(jg),hM=function(){function o(r,s,d){this.rules=r,this.staticRulesId="",this.isStatic=Jt.NODE_ENV==="production"&&(d===void 0||d.isStatic)&&fM(r),this.componentId=s,this.baseHash=Cu(pM,s),this.baseStyle=d,S1.registerId(s)}return o.prototype.generateAndInjectStyles=function(r,s,d){var g=this.baseStyle?this.baseStyle.generateAndInjectStyles(r,s,d).className:"";if(this.isStatic&&!d.hash)if(this.staticRulesId&&s.hasNameForId(this.componentId,this.staticRulesId))g=Eu(g,this.staticRulesId);else{var b=y1(Tu(this.rules,r,s,d)),S=ux(Cu(this.baseHash,b)>>>0);if(!s.hasNameForId(this.componentId,S)){var v=d(b,".".concat(S),void 0,this.componentId);s.insertRules(this.componentId,S,v)}g=Eu(g,S),this.staticRulesId=S}else{for(var T=Cu(this.baseHash,d.hash),$="",O=0;O<this.rules.length;O++){var P=this.rules[O];if(typeof P=="string")$+=P,Jt.NODE_ENV!=="production"&&(T=Cu(T,P));else if(P){var _=y1(Tu(P,r,s,d));T=Cu(T,_+O),$+=_}}if($){var V=ux(T>>>0);if(!s.hasNameForId(this.componentId,V)){var N=d($,".".concat(V),void 0,this.componentId);s.insertRules(this.componentId,V,N)}g=Eu(g,V)}}return{className:g,css:typeof window>"u"?s.getTag().getGroup(up(this.componentId)):""}},o}(),$1=Su?{Provider:function(o){return o.children},Consumer:function(o){return(0,o.children)(void 0)}}:Nn.createContext(void 0);$1.Consumer;var vx={},A1=new Set;function gM(o,r,s){var d=fx(o),g=o,b=!dx(o),S=r.attrs,v=S===void 0?zg:S,T=r.componentId,$=T===void 0?function(he,Le){var se=typeof he!="string"?"sc":l1(he);vx[se]=(vx[se]||0)+1;var le="".concat(se,"-").concat(PD(jg+se+vx[se]));return Le?"".concat(Le,"-").concat(le):le}(r.displayName,r.parentComponentId):T,O=r.displayName,P=O===void 0?function(he){return dx(he)?"styled.".concat(he):"Styled(".concat(c1(he),")")}(o):O,_=r.displayName&&r.componentId?"".concat(l1(r.displayName),"-").concat(r.componentId):r.componentId||$,V=d&&g.attrs?g.attrs.concat(v).filter(Boolean):v,N=r.shouldForwardProp;if(d&&g.shouldForwardProp){var J=g.shouldForwardProp;if(r.shouldForwardProp){var xe=r.shouldForwardProp;N=function(he,Le){return J(he,Le)&&xe(he,Le)}}else N=J}var Pe=new hM(s,_,d?g.componentStyle:void 0);function de(he,Le){return function(se,le,Oe){var ft=se.attrs,He=se.componentStyle,Tt=se.defaultProps,bt=se.foldedComponentIds,rt=se.styledComponentId,Be=se.target,Bt=Su?void 0:Nn.useContext($1),kt=k1(),pt=se.shouldForwardProp||kt.shouldForwardProp;Jt.NODE_ENV!=="production"&&Nn.useDebugValue&&Nn.useDebugValue(rt);var oe=_D(le,Bt,Tt)||ed,Te=function(tt,vt,Ut){for(var pn,an=Hi(Hi({},vt),{className:void 0,theme:Ut}),Pn=0;Pn<tt.length;Pn+=1){var xn=td(pn=tt[Pn])?pn(an):pn;for(var $n in xn)$n==="className"?an.className=Eu(an.className,xn[$n]):$n==="style"?an.style=Hi(Hi({},an.style),xn[$n]):an[$n]=xn[$n]}return"className"in vt&&typeof vt.className=="string"&&(an.className=Eu(an.className,vt.className)),an}(ft,le,oe),be=Te.as||Be,B={};for(var re in Te)Te[re]===void 0||re[0]==="$"||re==="as"||re==="theme"&&Te.theme===oe||(re==="forwardedAs"?B.as=Te.forwardedAs:pt&&!pt(re,be)||(B[re]=Te[re],pt||Jt.NODE_ENV!=="development"||dD(re)||A1.has(re)||!sx.has(be)||(A1.add(re),console.warn('styled-components: it looks like an unknown prop "'.concat(re,'" is being sent through to the DOM, which will likely trigger a React console error. If you would like automatic filtering of unknown props, you can opt-into that behavior via `<StyleSheetManager shouldForwardProp={...}>` (connect an API like `@emotion/is-prop-valid`) or consider using transient props (`$` prefix for automatic filtering.)')))));var Ve=function(tt,vt){var Ut=k1(),pn=tt.generateAndInjectStyles(vt,Ut.styleSheet,Ut.stylis);return Jt.NODE_ENV!=="production"&&Nn.useDebugValue&&Nn.useDebugValue(pn.className),pn}(He,Te),et=Ve.className,it=Ve.css;Jt.NODE_ENV!=="production"&&se.warnTooManyClasses&&se.warnTooManyClasses(et);var ht=Eu(bt,rt);et&&(ht+=" "+et),Te.className&&(ht+=" "+Te.className),B[dx(be)&&!sx.has(be)?"class":"className"]=ht,Oe&&(B.ref=Oe);var Ot=Ze.createElement(be,B);return Su&&it?Nn.createElement(Nn.Fragment,null,Nn.createElement("style",{precedence:"styled-components",href:"sc-".concat(rt,"-").concat(et),children:it}),Ot):Ot}(ue,he,Le)}de.displayName=P;var ue=Nn.forwardRef(de);return ue.attrs=V,ue.componentStyle=Pe,ue.displayName=P,ue.shouldForwardProp=N,ue.foldedComponentIds=d?Eu(g.foldedComponentIds,g.styledComponentId):"",ue.styledComponentId=_,ue.target=d?g.target:o,Object.defineProperty(ue,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(he){this._foldedDefaultProps=d?function(Le){for(var se=[],le=1;le<arguments.length;le++)se[le-1]=arguments[le];for(var Oe=0,ft=se;Oe<ft.length;Oe++)px(Le,ft[Oe],!0);return Le}({},g.defaultProps,he):he}}),Jt.NODE_ENV!=="production"&&(jD(P,_),ue.warnTooManyClasses=function(he,Le){var se={},le=!1;return function(Oe){if(!le&&(se[Oe]=!0,Object.keys(se).length>=200)){var ft=Le?' with the id of "'.concat(Le,'"'):"";console.warn("Over ".concat(200," classes were generated for component ").concat(he).concat(ft,`.
`)+`Consider using the attrs method, together with a style object for frequently changed styles.
Example:
  const Component = styled.div.attrs(props => ({
    style: {
      background: props.background,
    },
  }))\`width: 100%;\`

  <Component />`),le=!0,se={}}}}(P,_)),hx(ue,function(){return".".concat(ue.styledComponentId)}),b&&v1(ue,o,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),ue}function j1(o,r){for(var s=[o[0]],d=0,g=r.length;d<g;d+=1)s.push(r[d],o[d+1]);return s}var _1=function(o){return Object.assign(o,{isCss:!0})};function cp(o){for(var r=[],s=1;s<arguments.length;s++)r[s-1]=arguments[s];if(td(o)||nd(o))return _1(Tu(j1(zg,qc([o],r,!0))));var d=o;return r.length===0&&d.length===1&&typeof d[0]=="string"?Tu(d):_1(Tu(j1(d,r)))}function yx(o,r,s){if(s===void 0&&(s=ed),!r)throw rd(1,r);var d=function(g){for(var b=[],S=1;S<arguments.length;S++)b[S-1]=arguments[S];return o(r,s,cp.apply(void 0,qc([g],b,!1)))};return d.attrs=function(g){return yx(o,r,Hi(Hi({},s),{attrs:Array.prototype.concat(s.attrs,g).filter(Boolean)}))},d.withConfig=function(g){return yx(o,r,Hi(Hi({},s),g))},d}var L1=function(o){return yx(gM,o)},D=L1;sx.forEach(function(o){D[o]=L1(o)}),Jt.NODE_ENV!=="production"&&typeof navigator<"u"&&navigator.product==="ReactNative"&&console.warn(`It looks like you've imported 'styled-components' on React Native.
Perhaps you're looking to import 'styled-components/native'?
Read more about this at https://www.styled-components.com/docs/basics#react-native`);var Ig="__sc-".concat(wu,"__");Jt.NODE_ENV!=="production"&&Jt.NODE_ENV!=="test"&&typeof window<"u"&&(window[Ig]||(window[Ig]=0),window[Ig]===1&&console.warn(`It looks like there are several instances of 'styled-components' initialized in this application. This may cause dynamic styles to not render properly, errors during the rehydration process, a missing theme prop, and makes your application bigger without good reason.

See https://styled-components.com/docs/faqs#why-am-i-getting-a-warning-about-several-instances-of-module-on-the-page for more info.`),window[Ig]+=1);const H={colors:{windowBg:"#152029de",panelBg:"#04161c",panelBgGlass:"rgba(4, 22, 28, 0.22)",line:"#496791",text:"#ffffff",textAccent:"#C6E2FF",textDim:"#7f9bb8",healthOk:"#427231",healthCrit:"#ed6738",warning:"#e1b000",warningSoft:"#e6b400",statusOk:"limegreen",statusAlert:"#e1b000",statusBad:"red",statusPending:"#00b8e6",enhText:"#d8be86",enhTitle:"#e8cf93",enhBg:"rgba(169, 128, 56, 0.30)",enhLine:"#8a6d3b",custom:"#cccc00",chromeText:"#deebff",overlayBg:"black",overlayBgSoft:"rgba(0, 0, 0, 0.65)"},fonts:{body:"arial",mono:'Consolas, "Lucida Console", monospace'},radii:{modal:"0px",tooltip:"7px"},hud:{btn:"36px",btnSmall:"25px",icon:"26px",iconSmall:"17px",glyph:"30px",glyphSmall:"20px"}},mM=D.div`
    border: 1px solid ${H.colors.line};
    color: ${H.colors.chromeText};
    background-color: ${H.colors.windowBg};
    font-family:${H.fonts.body};
`,vM=D.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,Ug=D(mM)`
    box-shadow: 5px 5px 10px ${H.colors.overlayBg};
`,yM=D.span`
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
`;D(yM)`
    font-weight: normal;
`;const Vi=cp`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;class Ta extends Ze.Component{render(){return y.jsxs(xM,{children:[y.jsx(bM,{children:this.props.label}),y.jsx(wM,{type:this.props.type||"text",value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:"0"})]})}}const xM=D.div`
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
`,bM=D.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,wM=D.input`
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
`;class SM extends Ze.Component{render(){const{children:r,className:s}=this.props;return y.jsx("div",{className:s,children:r})}}const xx=D(SM)`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${H.fonts.body};
    font-size:12px;
    color:${H.colors.text};
    background-color:${H.colors.overlayBgSoft};
    border-radius: ${H.radii.tooltip};
    -moz-border-radius: ${H.radii.tooltip};
    -webkit-border-radius: ${H.radii.tooltip};
    padding:3px 3px 3px 3px;
    padding-bottom: 8px;
`,bx=D.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,ku=D.div`
    color: ${o=>o.$type=="good"?"#6fc126;":o.$type=="bad"?"#ff7b3f;":"white;"}
    font-weight: ${o=>o.$important?"bold":"inherit"};
    font-size: ${o=>o.$important?"14px":"12px"};
    margin-top: ${o=>o.$space?"14px":"0"};
`;class CM extends Ze.Component{getOnChange(r){return s=>{this.props.set(r,s.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(r){return s=>{if(console.log("keydown"),s.preventDefault(),s.stopPropagation(),!z1[s.keyCode])return;const d={keyCode:s.keyCode,shiftKey:s.shiftKey,altKey:s.altKey,ctrlKey:s.ctrlKey,metaKey:s.metaKey};this.props.set(r,d),this.props.save(),this.forceUpdate()}}getKey(r){return console.log(this.props.settings),EM(this.props.settings[r])}get(r){return this.props.settings[r]}render(){return y.jsx(TM,{onClick:this.props.close,children:y.jsxs(kM,{onClick:r=>r.stopPropagation(),children:[y.jsxs(RM,{children:[y.jsx(DM,{children:"Player Settings"}),y.jsx(MM,{onClick:this.props.close,title:"Close",children:"✕"})]}),y.jsxs(OM,{children:[y.jsx($M,{children:"Settings apply to this browser and device only. Reload the page for changes to take effect."}),y.jsx(Bg,{children:"Keys"}),y.jsx(Ta,{label:"Display ALL Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllEW"),value:this.getKey.call(this,"ShowAllEW")}),y.jsx(Ta,{label:"Display FRIENDLY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyEW"),value:this.getKey.call(this,"ShowFriendlyEW")}),y.jsx(Ta,{label:"Display ENEMY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyEW"),value:this.getKey.call(this,"ShowEnemyEW")}),y.jsx(Ta,{label:"Display ALL Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllBallistics"),value:this.getKey.call(this,"ShowAllBallistics")}),y.jsx(Ta,{label:"Display FRIENDLY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyBallistics"),value:this.getKey.call(this,"ShowFriendlyBallistics")}),y.jsx(Ta,{label:"Display ENEMY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyBallistics"),value:this.getKey.call(this,"ShowEnemyBallistics")}),y.jsx(Ta,{label:"Toggle RULER tool",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleLoS"),value:this.getKey.call(this,"ToggleLoS")}),y.jsx(Ta,{label:"Toggle HEX numbers",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleHexNumbers"),value:this.getKey.call(this,"ToggleHexNumbers")}),y.jsx(Ta,{label:"Toggle MAP background",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleBackground"),value:this.getKey.call(this,"ToggleBackground")}),y.jsx(Bg,{children:"Replay"}),y.jsx(Ta,{label:"Play / pause Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"TogglePlayPause"),value:this.getKey.call(this,"TogglePlayPause")}),y.jsx(Bg,{children:"Sound"}),y.jsx(Ta,{label:"Toggle sound in Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleSound"),value:this.getKey.call(this,"ToggleSound")}),y.jsx(Bg,{children:"Visual"}),y.jsx(Ta,{placeholder:"0",type:"number",label:"Zoom level to switch to strategic view",onChange:this.getOnChange.call(this,"ZoomLevelToStrategic"),value:this.get.call(this,"ZoomLevelToStrategic")}),y.jsx(AM,{children:"Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners."})]})]})})}}const EM=o=>{let r=z1[o.keyCode];return r=r.toUpperCase(),o.shiftKey&&(r+=" + shift"),o.altKey&&(r+=" + alt"),o.ctrlKey&&(r+=" + ctrl"),o.metaKey&&(r+=" + cmd"),r},TM=D(vM)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,kM=D.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: ${H.colors.panelBg};
    border: 1px solid ${H.colors.line};
    border-radius: ${H.radii.modal};
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: ${H.colors.chromeText};
    font-family: ${H.fonts.body};
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`,RM=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${H.colors.windowBg};
    border-bottom: 1px solid ${H.colors.line};
`,DM=D.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,MM=D.div`
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
    ${Vi}

    &:hover {
        color: #d6f7fd;
        border-color: #49c4d4;
        background-color: rgba(73, 196, 212, 0.12);
    }
`,OM=D.div`
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
`,$M=D.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`,Bg=D.div`
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
`,AM=D.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,z1={32:"space",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",58:":",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z"};class jM extends Ze.Component{constructor(r){super(r),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?y.jsx(CM,{close:this.close.bind(this),...this.props}):y.jsx(_M,{onClick:this.open.bind(this),children:"⚙"})}}const _M=D(Ug)`
    width: ${H.hud.btn};
    height: ${H.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${H.hud.glyph};
    border-right: none;
    border-top: none;
    ${Vi}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${H.hud.btnSmall};
        height: ${H.hud.btnSmall};
        font-size: ${H.hud.glyphSmall};

    }
`,N1=D.span`
    color: white;
    font-family:arial;
    font-size:12px;
`,LM=D.div`
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

    
    ${Vi}
`,P1=D.div`
    display: flex;
    position: absolute;
`,F1=D(P1)`
    flex-direction: row;
    left: 60px;
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: 40px;
`,zM=D(F1)`
    left: -100px;
`,I1=D(P1)`
    flex-direction: row;
    top: -120px;
    transform: translate(-50%, 0);
`,NM=D(I1)`
    top: 80px;
`,PM=D.div`
    position: relative;
    transform: rotate(${o=>o.$rotation}deg);

    & ${N1} {
        transform: rotate(${o=>-o.$rotation}deg);
    }
`,FM=D.div`
    position: absolute;
    left: ${o=>o.$left};
    top: ${o=>o.$top};
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: blue;
`,IM=D(xx)`
    top: 125px;
    min-width: 180px;
    z-index: 10001;
`,UM=D.div`
    margin-top: 14px;
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
`,U1=D.div`
    width: 40px;
    height: 40px;
    background-size: cover;
    margin: 5px;
    font-size: 30px;
    
    ${Vi}
`,B1=D(ku)`
    ${Vi}
`;class BM extends Ze.Component{constructor(r){super(r)}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}resetThrust(){const r=this.props.ship;window.shipManager.movement.revertAutoThrust(r),window.shipManager.movement.updateAssignThrust(r)}autoAssign(){const r=this.props.ship;window.shipManager.movement.revertAutoThrust(r),window.shipManager.movement.autoAssignThrust(r),window.shipManager.movement.updateAssignThrust(r)}render(){const{ship:r,position:s,rotation:d,totalRequired:g,remainginRequired:b,movement:S}=this.props;return y.jsxs(FM,{onMouseOver:v=>v.preventDefault(),onContextMenu:v=>v.preventDefault(),id:"thrustUIContainer",$left:`${s.x}px`,$top:`${s.y}px`,children:[y.jsxs(PM,{style:{transform:`rotate(${Math.round(Math.abs(d))}deg)`},$rotation:Math.round(Math.abs(d)),children:[y.jsx(F1,{children:Hg(r,1,g,b)}),y.jsx(I1,{children:Hg(r,3,g,b)}),y.jsx(NM,{children:Hg(r,4,g,b)}),y.jsx(zM,{children:Hg(r,2,g,b)})]}),y.jsxs(IM,{children:[y.jsx(bx,{children:"Assign thrust"}),WM(g,b,S),HM(r),VM(r,S),y.jsx(B1,{$space:!0,$important:!0,onClick:this.resetThrust.bind(this),children:"RESET THRUST"}),y.jsx(B1,{$important:!0,onClick:this.autoAssign.bind(this),children:"AUTO ASSIGN"}),y.jsxs(UM,{children:[y.jsx(U1,{onClick:this.ready.bind(this),children:"✔"}),y.jsx(U1,{onClick:this.cancel.bind(this),children:"🛇"})]})]})]})}}const HM=o=>{const r=shipManager.movement.getRemainingEngineThrust(o);return y.jsxs(ku,{$space:!0,$important:!0,children:["Thrust available: ",r]})},VM=(o,r)=>{if(!shipManager.movement.isTurn(r))return null;const s=shipManager.movement.calculateTurndelay(o,r,r.speed);return y.jsxs(ku,{$important:!0,children:["Current turn delay: ",s]})},WM=(o,r,s)=>{const d=Array("either","front","aft","port","starboard");s.type=="roll"&&(d[0]="any");const g=r.map((b,S)=>b<=0||b===null?null:y.jsxs(ku,{$type:b===0?"good":"bad",children:[b," thrust to ",d[S]," thrusters"]},`assign-thrust-text-${S}`)).filter(b=>b!==null);return g.length===0?y.jsx(ku,{$type:"good",children:"All done!"}):g},Hg=(o,r,s,d)=>{const g=shipManager.systems.getThrusters(o,r);return d.type!=="roll"&&s[r]===null?null:g.map((b,S)=>{const v=()=>{shipManager.movement.assignThrust(o,b),shipManager.movement.updateAssignThrust(o)},T=_=>{_.preventDefault(),shipManager.movement.unAssignThrust(o,b),shipManager.movement.updateAssignThrust(o)};let $=shipManager.criticals.hasCritical(b,"HalfEfficiency")?10:0;shipManager.criticals.hasCritical(b,"FirstThrustIgnored")&&($+=1);const O=shipManager.movement.getAmountChanneled(o,b),P=shipManager.systems.getOutput(o,b);return y.jsx(LM,{$crits:$,onClick:v,onContextMenu:T,$direction:r,children:y.jsxs(N1,{children:[O,"/",P]})},`thruster-${r}-${S}`)})},YM=()=>y.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[y.jsx("path",{d:"M9 3H3v6"}),y.jsx("path",{d:"M15 3h6v6"}),y.jsx("path",{d:"M9 21H3v-6"}),y.jsx("path",{d:"M15 21h6v-6"})]});class GM extends Ze.Component{fullScreen(){var r=window.document,s=r.documentElement,d=s.requestFullscreen||s.mozRequestFullScreen||s.webkitRequestFullScreen||s.msRequestFullscreen,g=r.exitFullscreen||r.mozCancelFullScreen||r.webkitExitFullscreen||r.msExitFullscreen;!r.fullscreenElement&&!r.mozFullScreenElement&&!r.webkitFullscreenElement&&!r.msFullscreenElement?d.call(s):g.call(r)}render(){return y.jsx(KM,{onClick:this.fullScreen.bind(this),title:"Full screen",children:y.jsx(YM,{})})}}const KM=D(Ug)`
    width: ${H.hud.btn};
    height: ${H.hud.btn};
    position: fixed;
    right: calc((${H.hud.btn} * 2) + 20px);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Vi}

    svg {
        width: ${H.hud.icon};
        height: ${H.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${H.hud.btnSmall};
        height: ${H.hud.btnSmall};
        right: calc((${H.hud.btnSmall} * 2) + 20px);

        svg {
            width: ${H.hud.iconSmall};
            height: ${H.hud.iconSmall};
        }
    }
`;class QM extends Ze.Component{constructor(r){super(r),this.state={available:H1()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{const r=H1();this.state.available!==r&&this.setState({available:r})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?y.jsx(XM,{onClick:this.surrender,title:"Surrender",children:y.jsx("img",{src:qM(),alt:""})}):null}}const H1=()=>{if(typeof window.gamedata>"u")return!1;const o=window.gamedata;if(o.replay||!o.isPlayerInGame()||o.status==="SURRENDERED"||o.status==="FINISHED")return!1;for(const r in o.slots){const s=o.slots[r];if(s.playerid==o.thisplayer&&s.surrendered!==null&&s.surrendered!==void 0)return!1}return!0},qM=()=>{const o="./img/surrender_icon1.png";return window.AssetManager?window.AssetManager.getSmartImagePath(o):o},V1="34px",W1="22px",XM=D(Ug)`
    width: ${H.hud.btn};
    height: ${H.hud.btn};
    position: fixed;
    right: calc(${H.hud.btn} + 10px);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Vi}

    img {
        width: ${V1};
        height: ${V1};
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
        width: ${H.hud.btnSmall};
        height: ${H.hud.btnSmall};
        right: calc(${H.hud.btnSmall} + 10px);

        img {
            width: ${W1};
            height: ${W1};
            transform: translateY(${"-2px"});
        }
    }
`;class ZM extends Nn.Component{constructor(r){super(r),this.state={losToggled:!1,hexToggled:!1,soundToggled:!0,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener("LoSToggled",this.externalToggleLoS),window.addEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.addEventListener("BackgroundToggled",this.externalToggleBackground),window.addEventListener("soundToggled",this.externalToggleSound),this.replayCheck=setInterval(()=>{const r=this.getReplayMode();this.state.replayMode!==r&&this.setState({replayMode:r})},500)}componentWillUnmount(){window.removeEventListener("LoSToggled",this.externalToggleLoS),window.removeEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.removeEventListener("BackgroundToggled",this.externalToggleBackground),window.removeEventListener("soundToggled",this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(r=>({hexToggled:!r.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}showFriendlyEW(r){webglScene.customEvent("ShowFriendlyEW",{up:r})}showEnemyEW(r){webglScene.customEvent("ShowEnemyEW",{up:r})}toggleFriendlyBallisticLines(r){if(r)return;const s=!this.state.fbToggled;this.setState({fbToggled:s}),webglScene.customEvent("ToggleFriendlyBallisticLines",{up:r})}toggleEnemyBallisticLines(r){if(r)return;const s=!this.state.ebToggled;this.setState({ebToggled:s}),webglScene.customEvent("ToggleEnemyBallisticLines",{up:r})}toggleLoS(r){if(r)return;const s=!this.state.losToggled;this.setState({losToggled:s}),webglScene.customEvent("ToggleLoS",{up:r}),window.dispatchEvent(new CustomEvent("LoSToggled"))}toggleHexNumbers(r){if(r)return;const s=!this.state.hexToggled;this.setState({hexToggled:s}),webglScene.customEvent("ToggleHexNumbers",{up:r}),window.dispatchEvent(new CustomEvent("HexNumbersToggled"))}toggleSound(){const r=!this.state.soundToggled;this.setState({soundToggled:r}),webglScene.customEvent("ToggleSound",{enabled:r})}toggleBackground(){const r=document.getElementById("background");if(!r)return;const s=!this.state.bgToggled;let d=this.state.originalBgImage;s?(d||(d=r.style.backgroundImage),r.style.backgroundImage="none",r.style.backgroundColor="black"):(r.style.backgroundImage=d||"",r.style.backgroundColor=""),this.setState({bgToggled:s,originalBgImage:d})}render(){return y.jsxs(JM,{children:[y.jsx(tO,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),y.jsx(eO,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),y.jsx(rO,{$toggled:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),y.jsx(nO,{$toggled:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),y.jsx(iO,{$toggled:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),y.jsx(aO,{$toggled:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),y.jsx(oO,{$toggled:this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?"Enable Background":"Disable Background"}),this.state.replayMode&&y.jsx(lO,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?"Sound On":"Sound Off"})]})}}const JM=D.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,hs=D(Ug)`
    display: flex;
    width: ${H.hud.btn};
    height: ${H.hud.btn};
    align-items: center;
    justify-content: center;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${Vi}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${H.hud.btnSmall};
        height: ${H.hud.btnSmall};
    }
`,eO=D(hs)`
    background-image: url("./img/EEW.png");
`,tO=D(hs)`
    background-image: url("./img/FEW.png");
`,nO=D(hs)`
    background-image: url("./img/ballisticTarget2.png");
    box-shadow: ${o=>o.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${o=>o.$toggled?"#1b533d":H.colors.windowBg};
    border: 1px solid ${o=>o.$toggled?"limegreen":H.colors.line};
    border-right: none;
`,rO=D(hs)`
    background-image: url("./img/ballisticLaunch2.png");
    box-shadow: ${o=>o.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${o=>o.$toggled?"#1b533d":H.colors.windowBg};
    border: 1px solid ${o=>o.$toggled?"limegreen":H.colors.line};
    border-right: none;
`,iO=D(hs)`
    background-image: url("./img/los1.png");
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":H.colors.line};
    border-right: none;
`,aO=D(hs)`
    background-image: url("./img/hexNumber.png");
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":H.colors.line};
    border-right: none;
`,oO=D(hs)`
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":H.colors.line};
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
`,lO=D(hs)`
    background-image: ${o=>o.$toggled?'url("./img/soundOn.png")':'url("./img/soundOff.png")'};
    border: 1px solid ${H.colors.line};
    border-right: none;
`,sO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,uO=D.div`
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
`,cO=D.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,dO=D.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${o=>o.img});
    background-size: cover;
    align-items: center;
    opacity: 1 !important;    
    justify-content: center;
    ${Vi}
    border: 1px solid ${o=>o.selected?"#ef4444":"transparent"};
    position: relative;
    box-shadow: ${o=>o.selected?"0 0 5px #b43131":"none"};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;class fO extends Ze.Component{selectMode(r,s){r.stopPropagation(),r.preventDefault();const{ship:d,system:g}=this.props;weaponManager.onSetModeClicked(d,g,s)}selectAllMode(r,s){r.stopPropagation(),r.preventDefault();const{ship:d,system:g}=this.props;weaponManager.onSetModeAllClicked(d,g,s)}render(){const{ship:r,system:s}=this.props,d=this.props.showModes!==!1,g=parseInt(s.firingMode);let b="";s.iconPath?b=`./img/systemicons/${s.iconPath}`:b=`./img/systemicons/${s.name}.png`;const S=[];for(const v in s.firingModes)if(s.firingModes.hasOwnProperty(v)){const T=parseInt(v),$=s.firingModes[v],O=T===g;let P=s.modeLetters||1;s.modeLettersArray&&s.modeLettersArray[T]&&(P=s.modeLettersArray[T]),S.push(y.jsx(dO,{img:b,selected:O,onClick:_=>this.selectMode(_,T),onContextMenu:_=>this.selectAllMode(_,T),title:`Set mode: ${$} ${O?"(Current)":""} (Right click to set all)`,children:$.substring(0,P)},T))}return y.jsxs(sO,{children:[d&&y.jsx(uO,{children:"Select Firing Mode"}),y.jsxs(cO,{children:[d&&S,this.props.children]})]})}}const pO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,hO=D.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,gO=D.div`
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

`,Y1=D.div`
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
`,mO=D.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,wx=D.span`
    font-weight: bold;
`,G1=D.span`
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

`,vO=D(wx)`
    color: #ffb833;
    font-weight: normal;    
`,yO=D(wx)`
    color: #ff3333;
    font-weight: normal;    
`,xO=D.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,Vg=D.div`
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
    
     ${Vi}
`,bO=D.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,wO=D.div`
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
`,K1=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,SO=D(Y1)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,CO=D.input`
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
`,EO=D.span`
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
`;class TO extends Ze.Component{constructor(r){super(r),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getEffectiveCriticalRepairCost(r,s){return s.name==="cnC"?4:r.repairCost}getRepairableSystems(){const{ship:r,system:s}=this.props,d=[],g=[],b=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const T of b){if(T.name==="SelfRepair"||T.repairPriority===0||T.privateRepairOnly&&!s.repairRestrictedTo||s.repairRestrictedTo&&!s.repairRestrictedTo.includes(T.id)||T.name=="structure"&&shipManager.systems.isDestroyed(T.ship,T))continue;const $=T.structureHomeLocation!==void 0&&T.structureHomeLocation!==null?T.structureHomeLocation:T.location;if(T.name!="structure"&&$!=0){var S=shipManager.systems.getStructureSystem(T.ship,$);if(S&&shipManager.systems.isDestroyed(T.ship,S))continue}let O=T.repairPriority,P=!1;if(s.priorityChanges&&T.id in s.priorityChanges&&s.priorityChanges[T.id]>=0&&(O=s.priorityChanges[T.id],P=!0),!P&&shipManager.systems.isDestroyed(r,T)&&O<=10&&(O+=10),!shipManager.systems.isDestroyed(r,T)&&T.criticals){const V=Array.isArray(T.criticals)?T.criticals:Object.values(T.criticals);for(const N of V){if(N.repairPriority===0||N.turn>=gamedata.turn||N.oneturn||N.turnend>0)continue;let J=N.repairPriority||0;const xe=T.id+"-"+N.id;let Pe=!1;s.priorityChanges&&xe in s.priorityChanges&&s.priorityChanges[xe]>=0?(J=s.priorityChanges[xe],Pe=!0):J<10&&(J+=T.repairPriority),g.push({type:"critical",sys:T,crit:N,priority:J,overridden:Pe,cost:this.getEffectiveCriticalRepairCost(N,T),id:T.id,subId:N.id,keyId:xe})}}const _=shipManager.systems.getTotalDamage(T);_>0&&d.push({type:"system",sys:T,priority:O,overridden:P,damage:_,maxHealth:T.maxhealth,id:T.id,subId:0,keyId:T.id})}const v=[...g,...d];return v.sort((T,$)=>{if(T.priority!==$.priority)return $.priority-T.priority;const O=!!T.overridden,P=!!$.overridden;if(O!==P)return O?-1:1;if(this.lastOrder&&this.lastOrder.length>0){const _=this.lastOrder.indexOf(T.keyId),V=this.lastOrder.indexOf($.keyId);if(_!==-1&&V!==-1)return _-V}return T.id!==$.id?T.id-$.id:T.subId-$.subId}),this.lastOrder=v.map(T=>T.keyId),v}handleInputChange(r,s,d){const g=r.target.value;if(g===""){this.setState(S=>({priorityInputs:{...S.priorityInputs,[s]:""}}));return}const b=parseInt(g,10);isNaN(b)||(this.setState(S=>({priorityInputs:{...S.priorityInputs,[s]:b}})),this.setPriority(s,b))}handleWheel(r,s,d){r.preventDefault();const g=r.deltaY<0?1:-1,b=d+g;b<1||this.setPriority(s,b)}componentDidUpdate(r){if(this.dragRef&&this.dragRef.started&&this.listRef){const S=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');S&&this.positionDraggedEl(S,this.dragRef,this.dragRef.lastClientY)}const s=this.getRepairableSystems(),d=this.state.priorityInputs,g={};let b=!1;s.forEach(S=>{const v=S.keyId,T=S.priority;d[v]!==void 0&&d[v]!==T&&document.activeElement!==document.getElementById(`prio-input-${v}`)&&(g[v]=T,b=!0)}),b&&this.setState(S=>({priorityInputs:{...S.priorityInputs,...g}}))}handleTop(r,s){r.stopPropagation();const d=this.getRepairableSystems();if(d.length===0)return;const g=d[0].priority,b=d.find(v=>v.keyId===s);if(!b||b.priority===g)return;let S=g+1;this.setPriority(s,S)}handleUp(r,s,d){r.stopPropagation();let g=d+1;g!==d&&this.setPriority(s,g)}handleDown(r,s,d){r.stopPropagation(),!(d<=1)&&this.setPriority(s,d-1)}handleReset(r,s){r.stopPropagation(),this.setPriority(s,-1)}setPriority(r,s){const{ship:d,system:g}=this.props;g.setOverride(r,s),webglScene.customEvent("SystemDataChanged",{ship:d,system:g})}onRowPointerDown(r,s,d,g){if(this.props.readOnly||r.button!=null&&r.button!==0||r.target&&r.target.closest&&r.target.closest("input, .sr-action-button"))return;const b=r.currentTarget,S=b?b.offsetHeight:24,v=this.listRef?this.listRef.offsetHeight:0,T=b?b.offsetWidth:0,$=b?r.clientY-b.getBoundingClientRect().top:0;this.dragRef={keyId:s,pointerId:r.pointerId,startY:r.clientY,startIdx:d,order:g,gapSize:S,lockHeight:v,anchorWidth:T,grabOffsetInRow:$,lastClientY:r.clientY,started:!1},r.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(r){const s=this.dragRef;if(!(!s||r.pointerId!==s.pointerId)){if(!s.started){if(Math.abs(r.clientY-s.startY)<4)return;s.started=!0}r.preventDefault(),s.lastClientY=r.clientY,this.updateDragForPointer(r.clientY),this.updateAutoScroll(r.clientY)}}updateDragForPointer(r){const s=this.dragRef;if(!s||!this.listRef)return;const d=this.listRef.querySelectorAll("[data-keyid]"),g=this.listRef.getBoundingClientRect(),b=r-g.top+this.listRef.scrollTop,v=this.state.drag&&this.state.drag.dropIdx===0?s.gapSize:0;let T=0,$=null;for(let P=0;P<d.length;P++){if(d[P].getAttribute("data-keyid")===String(s.keyId)){$=d[P];continue}const _=d[P].offsetTop-v+d[P].offsetHeight/2;if(b<_)break;T++}$&&this.positionDraggedEl($,s,r);const O=this.state.drag;(!O||O.keyId!==s.keyId||O.dropIdx!==T)&&this.setState({drag:{keyId:s.keyId,startIdx:s.startIdx,dropIdx:T,gapSize:s.gapSize,lockHeight:s.lockHeight}})}positionDraggedEl(r,s,d){const g=this.listRef.getBoundingClientRect();let b=d-g.top+this.listRef.scrollTop-s.grabOffsetInRow;const S=Math.max(0,this.listRef.scrollHeight-s.gapSize);b<0?b=0:b>S&&(b=S),r.style.top=b+"px",r.style.left="0px",r.style.width=s.anchorWidth+"px",r.style.transform="none"}updateAutoScroll(r){const s=this.listRef;if(!s){this.stopAutoScroll();return}const d=30,g=s.getBoundingClientRect(),b=s.scrollTop>0,S=s.scrollTop<s.scrollHeight-s.clientHeight-1,v=r-g.top,T=g.bottom-r;b&&v<d?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,v)/d),this.ensureAutoScrollRunning()):S&&T<d?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,T)/d),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const r=this.listRef,s=this.dragRef;if(!r||!s||this.autoScrollDir===0)return;const d=r.scrollTop;if(r.scrollTop=d+this.autoScrollDir*(this.autoScrollSpeed||6),r.scrollTop===d){this.stopAutoScroll();return}this.updateDragForPointer(s.lastClientY),this.updateAutoScroll(s.lastClientY)}onDragEnd(r){const s=this.dragRef;if(!s||r&&r.pointerId!=null&&r.pointerId!==s.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const g=this.listRef.querySelector('[data-keyid="'+s.keyId+'"]');g&&(g.style.transform="",g.style.top="",g.style.left="",g.style.width="")}this.dragRef=null;const d=this.state.drag;this.setState({drag:null}),!(!s.started||!d)&&d.dropIdx!==s.startIdx&&this.applyDropReorder(s.order,s.keyId,d.dropIdx)}applyDropReorder(r,s,d){const{ship:g,system:b}=this.props,S=r.findIndex(_=>_.keyId===s);if(S===-1)return;const v=r.slice(),[T]=v.splice(S,1);v.splice(d,0,T);const $=v[d+1],O=v[d-1];let P;$?P=$.priority+1:O?P=Math.max(1,O.priority-1):P=1,b.setOverride(s,P);for(let _=d-1;_>=0&&!(v[_].priority>P);_--)P+=1,b.setOverride(v[_].keyId,P);webglScene.customEvent("SystemDataChanged",{ship:g,system:b})}handlePropagate(r){r.stopPropagation();const{ship:s,system:d}=this.props;for(const g of s.systems)if(g.name==="SelfRepair"&&g.id!==d.id){if(g.priorityChanges)for(const b in g.priorityChanges)(!d.priorityChanges||!(b in d.priorityChanges))&&g.setOverride(b,-1);if(d.priorityChanges)for(const b in d.priorityChanges){const S=d.priorityChanges[b];S>=0&&g.setOverride(b,S)}}webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{ship:r,readOnly:s}=this.props,d=this.getRepairableSystems();let g=0;const b=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const S of b)S.name==="SelfRepair"&&g++;return y.jsxs(pO,{children:[y.jsx(hO,{children:s?"Repair Queue (view only)":"Manage Repair Queue"}),y.jsxs(gO,{ref:S=>{this.listRef=S},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[d.length===0&&y.jsx(SO,{children:"No damaged systems"}),d.map((S,v)=>{const T=this.state.drag,$=T&&T.keyId===S.keyId;let O=!1,P=!1,_=!1;if(T&&!$){const V=d.length-1,N=v>T.startIdx?v-1:v;T.dropIdx===0&&N===0?O=!0:T.dropIdx===V&&N===V-1?P=!0:T.dropIdx===N&&(_=!0)}return y.jsxs(Y1,{"data-keyid":S.keyId,$dragging:$,$gapBefore:O,$lineAtEnd:P,$lineBefore:_,$gapSize:T?T.gapSize:0,$readOnly:s,onPointerDown:V=>this.onRowPointerDown(V,S.keyId,v,d),children:[y.jsx(mO,{children:S.type==="critical"?y.jsxs(y.Fragment,{children:[y.jsxs(vO,{children:[S.sys.displayName," (",S.crit.description||S.crit.phpclass,")"]}),y.jsxs(G1,{children:["Cost: ",S.cost," ",y.jsx(K1,{})," Id: ",S.sys.id]})]}):y.jsxs(y.Fragment,{children:[shipManager.systems.isDestroyed(r,S.sys)?y.jsx(yO,{children:S.sys.displayName}):y.jsx(wx,{children:S.sys.displayName}),y.jsxs(G1,{children:["HP: ",shipManager.systems.getRemainingHealth(S.sys)," / ",S.sys.maxhealth," ",y.jsx(K1,{})," Id: ",S.sys.id]})]})}),y.jsx(xO,{children:s?y.jsx(EO,{title:"Priority (view only)",children:S.priority}):y.jsxs(y.Fragment,{children:[y.jsx(Vg,{className:"sr-action-button",title:"Reset Default",onClick:V=>this.handleReset(V,S.keyId),img:"./img/iconSRCancel.png"}),y.jsx(Vg,{className:"sr-action-button",title:"Decrease Priority",onClick:V=>this.handleDown(V,S.keyId,S.priority),img:"./img/systemicons/AAclasses/iconMinus.png"}),y.jsx(CO,{id:`prio-input-${S.keyId}`,type:"number",value:this.state.priorityInputs[S.keyId]!==void 0?this.state.priorityInputs[S.keyId]:S.priority,onChange:V=>this.handleInputChange(V,S.keyId,S.priority),onClick:V=>V.stopPropagation(),onWheel:V=>this.handleWheel(V,S.keyId,S.priority)}),y.jsx(Vg,{className:"sr-action-button",title:"Increase Priority",onClick:V=>this.handleUp(V,S.keyId,S.priority),img:"./img/systemicons/AAclasses/iconPlus.png"}),y.jsx(Vg,{className:"sr-action-button",title:"Move to Top",onClick:V=>this.handleTop(V,S.keyId),img:"./img/iconSRHigh.png"})]})})]},`${S.type}-${S.sys.id}${S.crit?"-"+S.crit.id:""}`)})]}),!s&&g>1&&y.jsx(bO,{children:y.jsx(wO,{onClick:S=>this.handlePropagate(S),children:"Set all Self Repair systems"})})]})}}const kO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
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
    overflow-x: hidden;
    display: block;
    position: relative;
    scrollbar-gutter: stable;
    ${o=>o.$lockHeight?`height: ${o.$lockHeight}px; max-height: ${o.$lockHeight}px;`:""}

    &::-webkit-scrollbar { width: 6px; }
    &::-webkit-scrollbar-track { background: #0d1620; }
    &::-webkit-scrollbar-thumb { background: #2b3e51; }
    &::-webkit-scrollbar-thumb:hover { background: #5a7ea8; }
`,Q1=D.div`
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
`,MO=D(Q1)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,OO=D.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,q1=D.span`
    font-weight: bold;
`,$O=D(q1)`
    color: #ff3333;
    font-weight: normal;
`,AO=D.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,jO=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,_O=D.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,LO=D.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,zO=D.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;class NO extends Ze.Component{constructor(r){super(r),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getOrderedBlocks(){const{ship:r,system:s}=this.props,d=(s.structureBlocks||[]).map(v=>{const T=(Array.isArray(r.systems)?r.systems:Object.values(r.systems)).find(_=>_.id===v.id),$=T?shipManager.systems.getRemainingHealth(T):v.maxhealth,O=T?T.maxhealth:v.maxhealth||0,P=T?shipManager.systems.isDestroyed(r,T):!1;return{id:v.id,displayName:v.displayName,hp:$,maxhealth:O,destroyed:P}}),g=s.repairOrder||[];if(g.length===0)return d.slice().sort((v,T)=>{const $=T.destroyed?1:0,O=v.destroyed?1:0;return $!==O?$-O:T.maxhealth-T.hp-(v.maxhealth-v.hp)});const b=[],S=new Set;for(const v of g){const T=d.find($=>$.id===v);T&&(b.push(T),S.add(v))}for(const v of d)S.has(v.id)||b.push(v);return b}onRowPointerDown(r,s,d,g){if(this.props.readOnly||r.button!=null&&r.button!==0||r.target&&r.target.closest&&r.target.closest(".ssr-action-button"))return;const b=r.currentTarget,S=b?b.offsetHeight:24,v=this.listRef?this.listRef.offsetHeight:0,T=b?b.offsetWidth:0,$=b?r.clientY-b.getBoundingClientRect().top:0;this.dragRef={keyId:s,pointerId:r.pointerId,startY:r.clientY,startIdx:d,order:g,gapSize:S,lockHeight:v,anchorWidth:T,grabOffsetInRow:$,lastClientY:r.clientY,started:!1},r.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(r){const s=this.dragRef;if(!(!s||r.pointerId!==s.pointerId)){if(!s.started){if(Math.abs(r.clientY-s.startY)<4)return;s.started=!0}r.preventDefault(),s.lastClientY=r.clientY,this.updateDragForPointer(r.clientY),this.updateAutoScroll(r.clientY)}}updateDragForPointer(r){const s=this.dragRef;if(!s||!this.listRef)return;const d=this.listRef.querySelectorAll("[data-keyid]"),g=this.listRef.getBoundingClientRect(),b=r-g.top+this.listRef.scrollTop,v=this.state.drag&&this.state.drag.dropIdx===0?s.gapSize:0;let T=0,$=null;for(let P=0;P<d.length;P++){if(d[P].getAttribute("data-keyid")===String(s.keyId)){$=d[P];continue}const _=d[P].offsetTop-v+d[P].offsetHeight/2;if(b<_)break;T++}$&&this.positionDraggedEl($,s,r);const O=this.state.drag;(!O||O.keyId!==s.keyId||O.dropIdx!==T)&&this.setState({drag:{keyId:s.keyId,startIdx:s.startIdx,dropIdx:T,gapSize:s.gapSize,lockHeight:s.lockHeight}})}positionDraggedEl(r,s,d){const g=this.listRef.getBoundingClientRect();let b=d-g.top+this.listRef.scrollTop-s.grabOffsetInRow;const S=Math.max(0,this.listRef.scrollHeight-s.gapSize);b<0?b=0:b>S&&(b=S),r.style.top=b+"px",r.style.left="0px",r.style.width=s.anchorWidth+"px",r.style.transform="none"}updateAutoScroll(r){const s=this.listRef;if(!s){this.stopAutoScroll();return}const d=30,g=s.getBoundingClientRect(),b=s.scrollTop>0,S=s.scrollTop<s.scrollHeight-s.clientHeight-1,v=r-g.top,T=g.bottom-r;b&&v<d?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,v)/d),this.ensureAutoScrollRunning()):S&&T<d?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,T)/d),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const r=this.listRef,s=this.dragRef;if(!r||!s||this.autoScrollDir===0)return;const d=r.scrollTop;if(r.scrollTop=d+this.autoScrollDir*(this.autoScrollSpeed||6),r.scrollTop===d){this.stopAutoScroll();return}this.updateDragForPointer(s.lastClientY),this.updateAutoScroll(s.lastClientY)}onDragEnd(r){const s=this.dragRef;if(!s||r&&r.pointerId!=null&&r.pointerId!==s.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const g=this.listRef.querySelector('[data-keyid="'+s.keyId+'"]');g&&(g.style.transform="",g.style.top="",g.style.left="",g.style.width="")}this.dragRef=null;const d=this.state.drag;this.setState({drag:null}),!(!s.started||!d)&&d.dropIdx!==s.startIdx&&this.applyDropReorder(s.order,s.keyId,d.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){const r=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');r&&this.positionDraggedEl(r,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(r,s,d){const{ship:g,system:b}=this.props,S=r.slice(),v=S.findIndex(O=>O.id===s);if(v===-1)return;const[T]=S.splice(v,1);S.splice(d,0,T);const $=S.map(O=>O.id);b.setRepairOrder($),webglScene.customEvent("SystemDataChanged",{ship:g,system:b})}handleReset(r){r.stopPropagation();const{ship:s,system:d}=this.props;d.setRepairOrder([]),webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{ship:r,readOnly:s}=this.props,d=this.getOrderedBlocks(),g=(this.props.system.repairOrder||[]).length>0;return y.jsxs(kO,{children:[y.jsx(RO,{children:s?"Structure Repair Order (view only)":"Manage Structure Repair"}),!s&&y.jsx(zO,{children:"Drag rows to set repair priority — top = first repaired"}),y.jsxs(DO,{ref:b=>{this.listRef=b},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[d.length===0&&y.jsx(MO,{children:"No structure blocks found"}),d.map((b,S)=>{const v=this.state.drag,T=v&&v.keyId===b.id;let $=!1,O=!1,P=!1;if(v&&!T){const V=d.length-1,N=S>v.startIdx?S-1:S;v.dropIdx===0&&N===0?$=!0:v.dropIdx===V&&N===V-1?O=!0:v.dropIdx===N&&(P=!0)}const _=b.maxhealth-b.hp;return y.jsx(Q1,{"data-keyid":b.id,$dragging:T,$gapBefore:$,$lineAtEnd:O,$lineBefore:P,$gapSize:v?v.gapSize:0,$readOnly:s,onPointerDown:V=>this.onRowPointerDown(V,b.id,S,d),children:y.jsxs(OO,{children:[b.destroyed?y.jsx($O,{children:b.displayName}):y.jsx(q1,{children:b.displayName}),y.jsxs(AO,{children:["HP: ",b.hp," / ",b.maxhealth,_>0&&y.jsxs(y.Fragment,{children:[y.jsx(jO,{}),"Dmg: ",_,b.destroyed?" — DESTROYED":""]})]})]})},b.id)})]}),!s&&y.jsx(_O,{children:y.jsx(LO,{className:"ssr-action-button",onClick:b=>this.handleReset(b),title:"Clear custom order and return to default (destroyed first, then most damaged)",children:g?"Reset to Default Order":"Using Default Order"})})]})}}const PO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,FO=D.div`
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
`,IO=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,X1=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,UO=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,BO=D.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,HO=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,VO=D.div`
    width: 20px;
    text-align: center;
`,Sx=D.div`
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
`,WO=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class YO extends Ze.Component{constructor(r){super(r),this.listRef=Nn.createRef()}handleIncrease(r){const{system:s}=this.props;s.setCurrDmgType(r),s.canIncrease()&&(s.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:s}=this.props;s.setCurrDmgType(r),s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating AA setting for:",r),d.setCurrDmgType(r);const v=d.allocatedAA[r];var T=[];for(var $ in g.ships){var O=g.ships[$];if(O.userid==s.userid&&!b.isDestroyed(O))if(O.flight)for(var P=0;P<O.systems.length;P++){var _=O.systems[P];if(_)for(var V=0;V<_.systems.length;V++){var N=_.systems[V];if(N&&N.displayName=="Adaptive Armor Controller"){T.push(N);break}}}else for(var V=0;V<O.systems.length;V++){var N=O.systems[V];if(N.displayName=="Adaptive Armor Controller"){T.push(N);break}}}console.log("Found AA controllers:",T.length);for(var J=0;J<T.length;J++){var N=T[J];N.setCurrDmgType(r);let Pe=0;for(;N.getCurrAllocated()<v&&N.canIncrease()&&Pe<100;)N.doIncrease(),Pe++;Pe>=100&&console.warn("AA Propagation safety break for",N)}S.customEvent("SystemDataChanged",{ship:s,system:d})}getRelevantArmorTypes(){const{ship:r,system:s}=this.props;return s.getRelevantArmorTypes(r)}render(){const{ship:r,system:s}=this.props;if(!s||!s.availableAA)return null;const d=this.getRelevantArmorTypes(),g=s.allocatedAA,b=T=>{const $=s.AAtotal_used,O=s.AAtotal,P=g[T]||0,_=s.AApertype,V=s.availableAA[T],N=s.AApreallocated,J=s.AApreallocated_used;return!($>=O||P>=_||N<=J&&V<=P)},S=T=>s.currchangedAA[T]>0,v=T=>g[T]>0;return y.jsxs(PO,{children:[y.jsx(FO,{children:"Manage Adaptive Armor"}),y.jsxs(IO,{ref:this.listRef,children:[d.map(T=>y.jsxs(X1,{children:[y.jsx(UO,{src:`./img/systemicons/AAclasses/${T}.png`,alt:T}),y.jsx(BO,{children:T}),y.jsxs(HO,{children:[y.jsx(Sx,{onClick:()=>this.handleDecrease(T),disabled:!S(T),children:"-"}),y.jsx(VO,{children:g[T]}),y.jsx(Sx,{onClick:()=>this.handleIncrease(T),disabled:!b(T),children:"+"}),y.jsx(Sx,{title:"Propagate to all units",onClick:()=>this.handlePropagate(T),disabled:!v(T),style:{marginLeft:"5px"},children:y.jsx("img",{src:"./img/systemicons/AAclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},T)),d.length===0&&y.jsx(X1,{children:"No armor types available"})]}),y.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",s.AAtotal_used," / ",s.AAtotal," ",y.jsx(WO,{})," Max Per Type: ",s.AApertype]})]})}}const GO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,KO=D.div`
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
`,QO=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Z1=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,qO=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,XO=D.div`
    flex: 1;
    font-weight: normal; 
`,ZO=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,JO=D.div`
    width: 20px;
    text-align: center;
`,Cx=D.div`
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
`,e$=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class t$ extends Ze.Component{constructor(r){super(r),this.listRef=Nn.createRef()}handleIncrease(r){const{system:s}=this.props;s.setCurrFCType(r),s.canIncrease()&&(s.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:s}=this.props;s.setCurrFCType(r),s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating BFCP setting for:",r),d.setCurrFCType(r);const v=d.allocatedBFCP[r];var T=[];for(var $ in g.ships){var O=g.ships[$];if(O.userid==s.userid&&!b.isDestroyed(O))if(O.flight)for(var P=0;P<O.systems.length;P++){var _=O.systems[P];if(_)for(var V=0;V<_.systems.length;V++){var N=_.systems[V];if(N&&N.displayName=="Computer"){T.push(N);break}}}else for(var V=0;V<O.systems.length;V++){var N=O.systems[V];if(N.displayName=="Computer"){T.push(N);break}}}console.log("Found BFCP controllers:",T.length);for(var J=0;J<T.length;J++){var N=T[J];N.setCurrFCType(r);let Pe=0;for(;N.getCurrAllocated()<v&&N.canIncrease()&&Pe<100;)N.doIncrease(),Pe++;for(;N.getCurrAllocated()>v&&N.canDecrease()&&Pe<100;)N.doDecrease(),Pe++;Pe>=100&&console.warn("BFCP Propagation safety break for",N)}S.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{system:r}=this.props;if(!r||!r.allocatedBFCP)return null;const s=Object.keys(r.allocatedBFCP),d=r.allocatedBFCP,g=v=>{const T=r.BFCPtotal_used,$=r.output,O=d[v]||0,P=r.BFCPpertype;return!(T>=$||O>=P)},b=v=>d[v]>0,S=v=>d[v]>=0;return y.jsxs(GO,{children:[y.jsx(KO,{children:"Hyach Computer"}),y.jsxs(QO,{ref:this.listRef,children:[s.map(v=>y.jsxs(Z1,{children:[y.jsx(qO,{src:`./img/systemicons/BFCPclasses/${v}.png`,alt:v}),y.jsx(XO,{children:v}),y.jsxs(ZO,{children:[y.jsx(Cx,{onClick:()=>this.handleDecrease(v),disabled:!b(v),children:"-"}),y.jsx(JO,{children:d[v]}),y.jsx(Cx,{onClick:()=>this.handleIncrease(v),disabled:!g(v),children:"+"}),y.jsx(Cx,{title:"Propagate to all units",onClick:()=>this.handlePropagate(v),disabled:!S(v),style:{marginLeft:"5px"},children:y.jsx("img",{src:"./img/systemicons/BFCPclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},v)),s.length===0&&y.jsx(Z1,{children:"No FC types available"})]}),y.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",r.BFCPtotal_used," / ",r.output," ",y.jsx(e$,{})," Max Per Type: ",r.BFCPpertype]})]})}}const n$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,r$=D.div`
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
`,i$=D.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,J1=D.div`
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
`,a$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,o$=D.span`
    flex-grow: 1;
    font-weight: bold;
`,l$=D.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,Wg=D.div`
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
    ${o=>!o.disabled&&Vi}
`,s$=o=>window.gamedata.turn===window.shipManager.getTurnPlaced(o)&&window.gamedata.gamephase===-1;class u$ extends Ze.Component{constructor(r){super(r)}handleSelect(r){const{system:s}=this.props;s.specCurrClass=r,s.canSelect()&&(s.doSelect(),this.forceUpdate())}handleUnselect(r){const{system:s}=this.props;s.specCurrClass=r,s.canUnselect()&&(s.doUnselect(),this.forceUpdate())}handleUse(r){const{system:s}=this.props;s.specCurrClass=r,s.canUse()&&(s.doUse(),this.forceUpdate())}handleCancel(r){const{system:s}=this.props;s.specCurrClass=r,s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}render(){const{ship:r,system:s}=this.props;if(!s)return null;const d=s$(r);let g=[];d?s.allSpec&&(g=Object.keys(s.allSpec)):s.availableSpec&&(g=Object.keys(s.availableSpec).filter(S=>s.availableSpec[S]>0)),g.sort();let b="";return d?b=`Specialists Selected: ${Object.values(s.availableSpec||{}).reduce((v,T)=>v+T,0)} / ${s.specTotal}`:b=`Specialists Used: ${s.specTotal_used||0} / ${s.specTotal}`,y.jsxs(n$,{children:[y.jsx(r$,{children:"Hyach Specialists"}),y.jsxs(i$,{children:[g.map(S=>{const v=d&&(s.specCurrClass=S,s.canSelect()),T=d&&(s.specCurrClass=S,s.canUnselect()),$=!d&&(s.specCurrClass=S,s.canUse()),O=!d&&(s.specCurrClass=S,s.canDecrease());s.availableSpec&&s.availableSpec[S]>0,s.currAllocatedSpec&&s.currAllocatedSpec[S];const P=`./img/systemicons/Specialistclasses/${S}.png?v=2`;return y.jsxs(J1,{children:[y.jsx(a$,{src:P,alt:S}),y.jsx(o$,{children:S}),y.jsx(l$,{children:d?y.jsxs(y.Fragment,{children:[y.jsx(Wg,{onClick:()=>this.handleUnselect(S),disabled:!T,children:y.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})}),y.jsx(Wg,{onClick:()=>this.handleSelect(S),disabled:!v,children:y.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})})]}):y.jsxs(y.Fragment,{children:[y.jsx(Wg,{onClick:()=>this.handleUse(S),disabled:!$,children:y.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})}),y.jsx(Wg,{onClick:()=>this.handleCancel(S),disabled:!O,children:y.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})})]})})]},S)}),g.length===0&&y.jsx(J1,{style:{justifyContent:"center",fontStyle:"italic"},children:"No Specialists Available"})]}),y.jsx("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:b})]})}}const c$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${H.colors.line};
`,d$=D.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${H.colors.line};
    border-bottom: 1px solid ${H.colors.line};
    color: ${H.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,f$=D.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,eC=D.div`
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
`,p$=D.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,h$=D.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,g$=D.input`
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
`,ka=D.div`
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
`;const m$=D.div`
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
`;class tC extends Ze.Component{constructor(r){super(r),this.listRef=Nn.createRef(),this.state={shieldInputs:{}}}getShieldLabel(r){let s=r.startArc,d=r.endArc;if(s===void 0||d===void 0)return r.displayName;let g=(s+d)/2;s>d&&(g=(s+d+360)/2),g=g%360;let b="";return g>=337.5||g<22.5?b="Front":g>=22.5&&g<67.5?b="Front Starboard":g>=67.5&&g<112.5?b="Starboard":g>=112.5&&g<157.5?b="Aft Starboard":g>=157.5&&g<202.5?b="Aft":g>=202.5&&g<247.5?b="Aft Port":g>=247.5&&g<292.5?b="Port":g>=292.5&&g<337.5&&(b="Front Port"),b?`${b} - ${r.displayName}`:r.displayName}getShieldSortPriority(r){const s=this.getShieldLabel(r).split(" - ")[0];return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[s]||99}getGeneratorAndShields(){const{ship:r,system:s}=this.props;let d=null,g=[],b="",S="";return s.name==="ThirdspaceShield"||s.name==="ThirdspaceShieldGenerator"?(b="ThirdspaceShield",S="ThirdspaceShieldGenerator"):(s.name==="ThoughtShield"||s.name==="ThoughtShieldGenerator")&&(b="ThoughtShield",S="ThoughtShieldGenerator"),b?(r.systems&&(Array.isArray(r.systems)?r.systems:Object.values(r.systems)).forEach(T=>{T.name===S&&(d=T),T.name===b&&g.push(T)}),g.sort((v,T)=>{const $=this.getShieldSortPriority(v),O=this.getShieldSortPriority(T);return $!==O?$-O:v.id-T.id}),{generator:d,shields:g,systemName:b}):{generator:null,shields:[]}}handleIncrease(r,s){r.canIncrease()&&(r.doIncrease(s),this.afterShieldChange(r))}handleDecrease(r,s){r.canDecrease()&&(r.doDecrease(s),this.afterShieldChange(r))}handleMin(r){r.canDecrease()&&(r.doMin(),this.afterShieldChange(r))}handleMax(r){r.canIncrease()&&(r.doMax(),this.afterShieldChange(r))}afterShieldChange(r){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}),this.updateInputState(r)}handleInputChange(r,s){if(s===""){this.setState(S=>({shieldInputs:{...S.shieldInputs,[r.id]:""}}));return}const d=parseInt(s,10);if(isNaN(d))return;const g=r.currentHealth,b=d-g;b>0?this.handleIncrease(r,b):b<0&&this.handleDecrease(r,Math.abs(b))}updateInputState(r){this.setState(s=>({shieldInputs:{...s.shieldInputs,[r.id]:r.currentHealth}}))}handleWheel(r,s){r.preventDefault(),(r.deltaY<0?1:-1)>0?this.handleIncrease(s,1):this.handleDecrease(s,1)}handleMouseEnter(r){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const s=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);s&&(s.showWeaponArc(this.props.ship,r),window.webglScene.requestRender())}}handleMouseLeave(r){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const s=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);s&&(s.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(r){r&&(shipManager.power.clickPlus(this.props.ship,r),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}handleDeBoost(r){r&&(shipManager.power.clickMinus(this.props.ship,r),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}handleEqualise(r){r&&(r.doEqualise(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}componentDidMount(){const{generator:r,shields:s}=this.getGeneratorAndShields(),d={};s.forEach(g=>d[g.id]=g.currentHealth),this.setState({shieldInputs:d})}componentDidUpdate(r){const{generator:s,shields:d}=this.getGeneratorAndShields(),g=this.state.shieldInputs,b={};let S=!1;d.forEach(v=>{g[v.id]!==v.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${v.id}`)&&(b[v.id]=v.currentHealth,S=!0)}),S&&this.setState(v=>({shieldInputs:{...v.shieldInputs,...b}}))}render(){const{generator:r,shields:s,systemName:d}=this.getGeneratorAndShields();return r?y.jsxs(c$,{children:[y.jsx(d$,{children:d==="ThirdspaceShield"?"Thirdspace Shields":"Thought Shields"}),y.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791"},children:["Unallocated Shield Energy: ",r.storedCapacity]}),d==="ThirdspaceShield"&&y.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791",display:"flex",justifyContent:"center",alignItems:"center",gap:"5px"},children:["Regeneration Rate: ",shipManager.systems.getOutputNoBoost(this.props.ship,r)+shipManager.power.getBoost(r)*s.length,y.jsx(ka,{className:"small",onClick:()=>this.handleDeBoost(r),title:"Reduce Boost",children:"-"}),y.jsx(ka,{className:"small",onClick:()=>this.handleBoost(r),title:"Boost Generator",children:"+"})]}),y.jsxs(f$,{ref:this.listRef,children:[s.map(g=>y.jsxs(eC,{onMouseEnter:()=>this.handleMouseEnter(g),onMouseLeave:()=>this.handleMouseLeave(g),children:[y.jsx(p$,{children:this.getShieldLabel(g)}),y.jsxs(h$,{children:[y.jsx(ka,{onClick:()=>this.handleMin(g),disabled:!g.canDecrease(),title:"Drop shield to 0",children:"Min"}),y.jsx(ka,{className:"small",onClick:()=>this.handleDecrease(g,25),disabled:!g.canDecrease(),children:"-25"}),y.jsx(ka,{className:"small",onClick:()=>this.handleDecrease(g,10),disabled:!g.canDecrease(),children:"-10"}),y.jsx(ka,{className:"small",onClick:()=>this.handleDecrease(g,5),disabled:!g.canDecrease(),children:"-5"}),y.jsx(ka,{className:"small",onClick:()=>this.handleDecrease(g,1),disabled:!g.canDecrease(),children:"-1"}),y.jsx(g$,{id:`shield-input-${g.id}`,type:"number",value:this.state.shieldInputs[g.id]!==void 0?this.state.shieldInputs[g.id]:g.currentHealth,onChange:b=>this.handleInputChange(g,b.target.value),onWheel:b=>this.handleWheel(b,g)}),y.jsx(ka,{className:"small",onClick:()=>this.handleIncrease(g,1),disabled:!g.canIncrease(),children:"+1"}),y.jsx(ka,{className:"small",onClick:()=>this.handleIncrease(g,5),disabled:!g.canIncrease(),children:"+5"}),y.jsx(ka,{className:"small",onClick:()=>this.handleIncrease(g,10),disabled:!g.canIncrease(),children:"+10"}),y.jsx(ka,{className:"small",onClick:()=>this.handleIncrease(g,25),disabled:!g.canIncrease(),children:"+25"}),y.jsx(ka,{onClick:()=>this.handleMax(g),disabled:!g.canIncrease(),title:"Raise shield to maximum",children:"Max"})]})]},g.id)),s.length===0&&y.jsx(eC,{children:"No Shields Found"})]}),r&&y.jsx(m$,{onClick:()=>this.handleEqualise(r),children:"Equalise Shields"})]}):y.jsx("div",{children:"No Generator Found"})}}const nC=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${H.colors.line};
`,rC=D.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${H.colors.line};
    border-bottom: 1px solid ${H.colors.line};
    color: ${H.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,Ex=D.div`
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
`,Tx=D.div`
    flex: 1;
`,kx=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
`,v$=D.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${H.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`,ad=D.div`
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
`;class y$ extends Ze.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}canBoost(){const{ship:r,system:s}=this.props;return s.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(r,s)}canDeBoost(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(s)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){const{ship:r,system:s}=this.props,d=shipManager.power.getBoost(s),g=s.active;return y.jsxs(nC,{children:[y.jsx(rC,{children:"Power Capacitor"}),s.boostable&&y.jsxs(Ex,{children:[y.jsx(Tx,{children:"Open Petals"}),y.jsxs(kx,{children:[y.jsx(ad,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:d===0,children:"OFF"}),y.jsx(ad,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:d>0,$variant:"activate",children:"ON"})]})]}),y.jsxs(Ex,{children:[y.jsx(Tx,{children:"Double Recharge"}),y.jsxs(kx,{children:[y.jsx(ad,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!g,children:"OFF"}),y.jsx(ad,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:g,$variant:"activate",children:"ON"})]})]})]})}}const x$=D(nC)`
    min-width: 0;
    width: 190px;
`;class b$ extends Ze.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}render(){const{system:r}=this.props,s=r.isMaintainingVortex();return y.jsxs(x$,{children:[y.jsx(rC,{children:"Jump Point"}),y.jsxs(Ex,{children:[y.jsx(Tx,{children:"Maintain Vortex"}),y.jsxs(kx,{children:[y.jsx(ad,{onClick:()=>this.handleDeactivate(),disabled:!r.canDeactivate(),$active:!s,children:"OFF"}),y.jsx(ad,{onClick:()=>this.handleActivate(),disabled:!r.canActivate(),$active:s,$variant:"activate",children:"ON"})]})]}),y.jsx(v$,{children:s?"Held open. All powered systems except the Scanner are shut down this turn.":"Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner."})]})}}const Ra={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",text:"#f2f2f2"},w$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${o=>o.$isWeapon||o.$isPurple?Ra.surface:"rgba(16, 26, 38, 0.9)"};
    border: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Ra.line:H.colors.line};
`,S$=D.div`
    padding: 3px;
    background-color: ${o=>o.$isWeapon?"#571616":o.$isPurple?Ra.line:"#215a7a"};
    border: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Ra.line:H.colors.line};
    border-bottom: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Ra.line:H.colors.line};
    color: ${o=>o.$isWeapon||o.$isPurple?Ra.text:H.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,C$=D.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid ${o=>o.$isPurple?Ra.line:"#496791"};
    font-size: 12px;
    color: ${o=>o.$isPurple?Ra.text:"#deebff"};
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;D.div`
    flex: 1;
    margin-right: 10px;    
`;const E$=D.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,iC=D.div`
    flex: 1;
    height: 18px;
    background: ${o=>o.$isPurple?Ra.line:"#203348"};
    border: 1px solid ${o=>o.$isPurple?Ra.accent:"#496791"};
    color: ${o=>o.$isPurple?Ra.text:"#deebff"};
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
        &:hover { background: #4b2b51; color: #d8b9e6; border: 1px solid ${Ra.accent}; }
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

    ${o=>o.$variant==="activate"&&o.$isWeapon&&`
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
`;class T$ extends Ze.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivateAll(r){r.preventDefault();const{ship:s,system:d}=this.props;let g=[];s.flight?g=s.systems.map(S=>S.systems).reduce((S,v)=>S.concat(v),[]):g=s.systems;let b=!1;g.forEach(S=>{!S.name||S.name!==d.name||S.canActivate&&typeof S.canActivate=="function"&&S.canActivate()&&(S.doActivate(),b=!0)}),b&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(r){r.preventDefault();const{ship:s,system:d}=this.props;let g=[];s.flight?g=s.systems.map(S=>S.systems).reduce((S,v)=>S.concat(v),[]):g=s.systems;let b=!1;g.forEach(S=>{!S.name||S.name!==d.name||S.canDeactivate&&typeof S.canDeactivate=="function"&&S.canDeactivate()&&(S.doDeactivate(),b=!0)}),b&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate=="function"&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate=="function"&&this.props.system.canDeactivate()}render(){const{ship:r,system:s}=this.props,d=s.active||s.weapon&&weaponManager.hasFiringOrder(r,s),g=typeof s.getActivateLabel=="function"?s.getActivateLabel():null,b=typeof s.getDeactivateLabel=="function"?s.getDeactivateLabel():null,S=g||(s.weapon?"Fire":"Activate"),v=b||(s.weapon?"Don't Fire":"Deactivate"),T=!!s.singleActivationButton,$=!T||this.canActivate(),O=!s.weapon&&(!T||this.canDeactivate()),P=!!s.activationMenuPurple&&!s.weapon;return y.jsxs(w$,{$isWeapon:s.weapon,$isPurple:P,children:[y.jsx(S$,{$isWeapon:s.weapon,$isPurple:P,children:s.displayName}),y.jsx(C$,{$isPurple:P,children:y.jsxs(E$,{children:[$&&y.jsx(iC,{onClick:()=>this.handleActivate(),onContextMenu:_=>this.handleActivateAll(_),disabled:!this.canActivate(),$active:d,$variant:"activate",$isWeapon:s.weapon,$isPurple:P,children:S}),O&&y.jsx(iC,{onClick:()=>this.handleDeactivate(),onContextMenu:_=>this.handleDeactivateAll(_),disabled:!this.canDeactivate(),$active:!d,$variant:"deactivate",$isPurple:P,children:v})]})})]})}}const k$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,R$=D.div`
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
`,Rx=D.div`
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
`,Dx=D.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,Yg=D.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,D$=D.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`,gs=D.div`
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
`;class M$ extends Ze.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(r){r.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){const{ship:r,system:s}=this.props;for(;shipManager.power.getBoost(s)>0;)shipManager.power.clickMinus(r,s);shipManager.power.onOfflineClicked(r,s),this.handleUpdate()}}handleOfflineAll(r){if(r.preventDefault(),this.canOffline()){const{ship:s,system:d}=this.props;for(;shipManager.power.getBoost(d)>0;)shipManager.power.clickMinus(s,d);shipManager.power.offlineAll(s,d),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system})}canOffline(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&(s.canOffLine||s.powerReq>0)&&!s.powerLocked&&!shipManager.power.isOffline(r,s)&&!weaponManager.hasFiringOrder(r,s)}canOnline(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(r,s)&&!shipManager.power.isForcedOffline(r,s)&&!shipManager.power.isVortexLockedOffline(r,s)}canBoost(){const{ship:r,system:s}=this.props;return s.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(r,s)&&(!s.isScanner()||s.id==shipManager.power.getHighestSensorsId(r))&&s.name!=="ThirdspaceShieldGenerator"&&s.name!=="powerCapacitor"&&s.name!=="PowerCapacitor"}canDeBoost(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(s)&&s.name!=="ThirdspaceShieldGenerator"&&s.name!=="powerCapacitor"&&s.name!=="PowerCapacitor"}canOverload(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(r,s)&&s.weapon&&s.overloadable&&!shipManager.power.isOverloading(r,s)}canStopOverload(){const{ship:r,system:s}=this.props;return gamedata.gamephase===1&&s.weapon&&s.overloadable&&shipManager.power.isOverloading(r,s)&&(s.overloadshots>=s.extraoverloadshots||s.overloadshots==0)}render(){const{ship:r,system:s}=this.props,d=this.canOffline()||this.canOnline(),g=s.boostable&&(this.canBoost()||this.canDeBoost()),b=s.overloadable&&(this.canOverload()||this.canStopOverload());if(!d&&!g&&!b)return null;const S=shipManager.power.isOffline(r,s),v=shipManager.power.getBoost(s),T=shipManager.power.isOverloading(r,s),$=s.name==="reactor",O=s.name==="jumpEngine";let P="Boost Level";return $&&(P="Self-Destruct"),O&&(P="Jump to Hyperspace"),y.jsxs(k$,{children:[y.jsx(R$,{children:"Power Settings"}),d&&y.jsxs(Rx,{children:[y.jsx(Dx,{children:"Power"}),y.jsxs(Yg,{children:[y.jsx(gs,{onClick:()=>this.handleOnline(),onContextMenu:_=>this.handleOnlineAll(_),disabled:!this.canOnline(),$active:!S,$variant:"activate",children:"On"}),y.jsx(gs,{onClick:()=>this.handleOffline(),onContextMenu:_=>this.handleOfflineAll(_),disabled:!this.canOffline(),$active:S,$variant:"deactivate",children:"Off"})]})]}),g&&y.jsxs(Rx,{children:[y.jsx(Dx,{children:P}),$||O?y.jsxs(Yg,{children:[y.jsx(gs,{onClick:()=>this.handleBoost(),disabled:v>0||!this.canBoost(),$active:v>0,$variant:$?"deactivate":"risk",children:"Yes"}),y.jsx(gs,{onClick:()=>this.handleDeBoost(),disabled:v===0||!this.canDeBoost(),$active:v===0,$variant:"activate",children:"No"})]}):y.jsxs(Yg,{children:[y.jsx(gs,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:"-"}),y.jsx(D$,{children:v}),y.jsx(gs,{onClick:()=>this.handleBoost(),$narrow:!0,children:"+"})]})]}),b&&y.jsxs(Rx,{children:[y.jsx(Dx,{children:"Overcharge"}),y.jsxs(Yg,{children:[y.jsx(gs,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:T,$variant:"warning",children:"Yes"}),y.jsx(gs,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!T,$variant:"deactivate",children:"No"})]})]})]})}}const O$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,$$=D.div`
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
`,A$=D.div`
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
`,aC=D.div`
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
`,j$=D.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,_$=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,oC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,L$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,z$=D.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,N$=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,P$=D.div`
    width: 20px;
    text-align: center;
`,Mx=D.div`
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
`;class F$ extends Ze.Component{constructor(r){super(r),this.listRef=Nn.createRef()}handleIncrease(r){const{system:s}=this.props;s.setCurrShipType(r),s.canIncrease()&&(s.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:s}=this.props;s.setCurrShipType(r),s.canDecrease()&&(s.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene,v=!!(d.hasMultiTarget&&d.hasMultiTarget()),T=v?d.getCurrWeaponId():null,$=v?d.getMineWeapons():[],O=v?$.find(Oe=>String(Oe.id)===String(T)):null;if(v&&!O)return;d.setCurrShipType(r);const P=d.rangeSetting||d.range,_=v?d.allocatedRanges[T]:d.allocatedRanges;if(!_)return;const V=_[r]===null||_[r]===void 0?P:_[r];var N=[];for(var J in g.ships){var xe=g.ships[J];if(xe.userid==s.userid&&!b.isDestroyed(xe)){if(xe.phpclass&&s.phpclass){if(xe.phpclass!=s.phpclass)continue}else if(xe.shipClass!=s.shipClass)continue;for(var Pe in xe.systems){var de=xe.systems[Pe];if(de&&de.name===d.name){var ue=!!(de.hasMultiTarget&&de.hasMultiTarget());ue===v&&N.push({unit:xe,ctrl:de})}}}}for(var he=0;he<N.length;he++){var Le=N[he],de=Le.ctrl;if(v){de.ensureMultiAllocatedShape&&de.ensureMultiAllocatedShape();var se=de.getMineWeapons(),le=se.find(rt=>rt.displayName===O.displayName&&rt.indexInGroup===O.indexInGroup);if(!le)continue;de.setCurrWeaponId(le.id)}de.setCurrShipType(r);let ft=0;const He=()=>v?de.allocatedRanges[de.getCurrWeaponId()]:de.allocatedRanges,Tt=()=>de.range||de.rangeSetting,bt=()=>{const rt=He();if(!rt)return Tt();const Be=rt[r];return Be??Tt()};for(;bt()<V&&de.canIncrease()&&ft<100;)de.doIncrease(),ft++;for(;bt()>V&&de.canDecrease()&&ft<100;)de.doDecrease(),ft++;ft>=100&&console.warn("Mine Settings Propagation safety break for",de)}S.customEvent("SystemDataChanged",{ship:s,system:d})}cycleWeapon(r){const{system:s}=this.props;if(!s.hasMultiTarget||!s.hasMultiTarget())return;const d=s.getMineWeapons();if(d.length===0)return;const g=s.getCurrWeaponId();let b=d.findIndex(v=>String(v.id)===String(g));b<0&&(b=0);const S=(b+r+d.length)%d.length;s.setCurrWeaponId(d[S].id),this.forceUpdate()}render(){const{system:r}=this.props;if(!r||(r.range=r.range||r.rangeSetting,!r.range))return null;const s=!!(r.hasMultiTarget&&r.hasMultiTarget());let d=[],g=null,b=null,S=r.allocatedRanges||{};s?(r.ensureMultiAllocatedShape&&r.ensureMultiAllocatedShape(),d=r.getMineWeapons(),g=r.getCurrWeaponId(),b=d.find(N=>String(N.id)===String(g))||d[0]||null,S=g!=null&&r.allocatedRanges[g]?r.allocatedRanges[g]:{}):(r.ensureFlatAllocatedShape&&r.ensureFlatAllocatedShape(),S=r.allocatedRanges||{});const v=Object.keys(S),T=r.validTargets||v,$=N=>{if(!T.includes(N))return"N/A";const J=S[N];return J??r.range},O=N=>T.includes(N)?$(N)<r.range:!1,P=N=>T.includes(N)?$(N)>0:!1,_=N=>!!T.includes(N),V=s&&d.length>0?y.jsxs(A$,{children:[y.jsx(aC,{onClick:()=>this.cycleWeapon(-1),disabled:d.length<2,title:"Previous weapon",children:"<"}),y.jsx(j$,{children:b?b.label:""}),y.jsx(aC,{onClick:()=>this.cycleWeapon(1),disabled:d.length<2,title:"Next weapon",children:">"})]}):y.jsx($$,{children:"Set Mine Range"});return y.jsxs(O$,{children:[V,y.jsxs(_$,{ref:this.listRef,children:[v.map(N=>y.jsxs(oC,{children:[y.jsx(L$,{src:`./img/systemicons/BFCPclasses/${N}.png`,alt:N}),y.jsx(z$,{children:N}),y.jsxs(N$,{onWheel:J=>{J.deltaY<0&&O(N)?this.handleIncrease(N):J.deltaY>0&&P(N)&&this.handleDecrease(N)},children:[y.jsx(Mx,{onClick:()=>this.handleDecrease(N),disabled:!P(N),children:"-"}),y.jsx(P$,{children:$(N)}),y.jsx(Mx,{onClick:()=>this.handleIncrease(N),disabled:!O(N),children:"+"}),y.jsx(Mx,{title:s?"Propagate this weapon's settings to same-class mines with Multiple Targets":"Propagate to all mines of same type",onClick:()=>this.handlePropagate(N),disabled:!_(N),style:{marginLeft:"5px"},children:y.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},N)),v.length===0&&y.jsx(oC,{children:"No ship types available"})]}),y.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Max Range: ",r.range]})]})}}const I$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,U$=D.div`
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
`,B$=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,lC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,H$=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,V$=D.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,W$=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,Y$=D.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${o=>o.$active?"#4CAF50":"#F44336"};
`,sC=D.div`
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
`;class G$ extends Ze.Component{constructor(r){super(r),this.listRef=Nn.createRef()}handleToggle(r){const{system:s}=this.props;s.setCurrShipType(r),s.canSet()?(s.doSet(),this.forceUpdate()):s.canUnset()&&(s.doUnset(),this.forceUpdate())}handlePropagate(r){const{ship:s,system:d}=this.props,g=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating Mine settings for:",r),d.setCurrShipType(r);const v=d.allocatedShipTypes[r];var T=[];for(var $ in g.ships){var O=g.ships[$];if(O.userid==s.userid&&!b.isDestroyed(O))for(var P=0;P<O.systems.length;P++){var _=O.systems[P];if(O.shipClass==s.shipClass&&_.name===d.name){T.push(_);break}}}console.log("Found Mine Weapons of same type:",T.length);for(var V=0;V<T.length;V++){var _=T[V];_.setCurrShipType(r);let J=0;for(;_.allocatedShipTypes[r]!==v&&(v?_.canSet():_.canUnset())&&J<10;)v?_.doSet():_.doUnset(),J++;J>=10&&console.warn("Mine Settings Propagation safety break for",_)}S.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{system:r}=this.props;if(!r)return null;const s=r.allocatedShipTypes||{},d=Object.keys(s),g=S=>s[S]?"YES":"NO",b=S=>{const v=Number(this.props.ship.spawned),T=v===-1?1:v+1;return window.gamedata.turn===T};return y.jsxs(I$,{children:[y.jsx(U$,{children:"Set Target Types"}),y.jsxs(B$,{ref:this.listRef,children:[d.map(S=>y.jsxs(lC,{children:[y.jsx(H$,{src:`./img/systemicons/BFCPclasses/${S}.png`,alt:S}),y.jsx(V$,{children:S}),y.jsxs(W$,{children:[y.jsx(Y$,{$active:s[S],children:g(S)}),y.jsx(sC,{onClick:()=>this.handleToggle(S),disabled:!b(),style:{marginLeft:"5px"},children:"Toggle"}),y.jsx(sC,{title:"Propagate to all mines of same type",onClick:()=>this.handlePropagate(S),disabled:!1,style:{marginLeft:"5px",width:"16px",padding:"0"},children:y.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},S)),d.length===0&&y.jsx(lC,{children:"No ship types available"})]})]})}}const Ox=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: rgba(8, 28, 12, 0.92);
    border: 1px solid #3f8a3f;
`,uC=D.div`
    padding: 3px;
    background-color: #16401b;
    border: 1px solid #3f8a3f;
    color: #e6ffe6;
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,K$=D.div`
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
`,cC=D.div`
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
`,Q$=D.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;D.div`
    text-align: center;
    color: #bdf0bd;
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;const dp=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,dC=D.div`
    width: 70px;
    font-size: 10px;
    color: #bdf0bd;
    user-select: none;
`,Ru=D.div`
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
`;class q$ extends Ze.Component{refresh(){const{ship:r,system:s}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:s})}cycleMode(r){const{system:s}=this.props;if(!gamedata.isMyShip(this.props.ship))return;const d=s.firingMode==1?2:1;s.setFiringMode(d),typeof s.initializationUpdate=="function"&&s.initializationUpdate(),this.refresh()}activateMode1(){const{ship:r,system:s}=this.props;s.canActivate()&&(s.doActivate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:s}),webglScene.customEvent("CloseSystemInfo"))}targetWarrior(){const{ship:r,system:s}=this.props;weaponManager.isSelectedWeapon(s)||weaponManager.selectWeapon(r,s),webglScene.customEvent("SystemDataChanged",{ship:r,system:s}),webglScene.customEvent("CloseSystemInfo")}setRotation(r,s){const{system:d}=this.props;d.rotationDirection=r,d.rotationAmount=s,typeof d.updateRotationNotes=="function"&&d.updateRotationNotes(),this.refresh()}renderInitialOrders(){const{system:r}=this.props,s=parseInt(r.firingMode,10),d=r.firingModes[s]||"";return y.jsxs(Ox,{children:[y.jsxs(K$,{children:[y.jsx(cC,{onClick:()=>this.cycleMode(-1),title:"Previous mode",children:"<"}),y.jsx(Q$,{children:d}),y.jsx(cC,{onClick:()=>this.cycleMode(1),title:"Next mode",children:">"})]}),s==1&&y.jsx(dp,{children:y.jsx(Ru,{onClick:()=>this.activateMode1(),children:"Activate"})}),s==2&&y.jsx(dp,{children:y.jsx(Ru,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(r),children:"Target friendly Warrior"})})]})}engageMode3(){const{ship:r,system:s}=this.props;gamedata.isMyShip(r)&&(weaponManager.hasFiringOrder(r,s)||(s.setFiringMode(3),typeof s.initializationUpdate=="function"&&s.initializationUpdate(),this.refresh()))}renderPreFiring(){const{system:r}=this.props;if(r.firingMode!=3)return y.jsxs(Ox,{children:[y.jsx(uC,{children:"Gravitic Augmenter"}),y.jsx(dp,{children:y.jsx(Ru,{onClick:()=>this.engageMode3(),children:"Engage Gravity Shifting"})})]});const s=r.rotationDirection||1,d=r.rotationAmount||1,g=(b,S)=>s==b&&d==S;return y.jsxs(Ox,{children:[y.jsx(uC,{children:"Gravity Shift Settings"}),y.jsxs(dp,{children:[y.jsx(dC,{children:"Clockwise"}),y.jsx(Ru,{onClick:()=>this.setRotation(1,1),$active:g(1,1),children:"60°"}),y.jsx(Ru,{onClick:()=>this.setRotation(1,2),$active:g(1,2),children:"120°"})]}),y.jsxs(dp,{children:[y.jsx(dC,{children:"Anti-Clockwise"}),y.jsx(Ru,{onClick:()=>this.setRotation(2,1),$active:g(2,1),children:"60°"}),y.jsx(Ru,{onClick:()=>this.setRotation(2,2),$active:g(2,2),children:"120°"})]})]})}render(){const{system:r}=this.props;return r?gamedata.gamephase==1?(r.firingMode==3&&(r.setFiringMode(1),typeof r.initializationUpdate=="function"&&r.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}}const Gg=3,X$=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Z$=D.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,J$=D.div`
    text-align: center;
    color: ${o=>o.$empty?"#f0a0a0":"#f2f2f2"};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,eA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,tA=D.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,fC=D.div`
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
`,nA=D.input`
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
`;const Du=[{key:"hitBoost5",label:"Hit Chance",increment:5,prefix:"+",suffix:"%",display:o=>o*2,parse:o=>o/2},{key:"shotBoost",label:"Shots",increment:1,prefix:"+",suffix:"",display:o=>o,parse:o=>o},{key:"dmgBoost5",label:"Damage",increment:5,prefix:"+",suffix:"",display:o=>o,parse:o=>o}],rA=(o,r)=>o.prefix+o.display(r|0)+o.suffix;class iA extends Ze.Component{fieldSteps(r,s){return(s|0)/r.increment}allocatedSteps(r){return Du.reduce((s,d)=>s+this.fieldSteps(d,r[d.key]),0)}maxSteps(){const{ship:r,system:s}=this.props;return typeof s.getMaxSteps=="function"?s.getMaxSteps(r):Math.floor(shipManager.movement.getRemainingEngineThrust(r)/Gg)}totalShots(r){return(r.guns|0)+(r.shotBoost|0)}maxDamageSteps(r){return this.totalShots(r)}clampDamageToShots(r){const s=Du.find(g=>g.key==="dmgBoost5"),d=this.maxDamageSteps(r)*s.increment;(r.dmgBoost5|0)>d&&(r.dmgBoost5=d)}refresh(){const{ship:r,system:s}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:s})}setValue(r,s){const{ship:d,system:g}=this.props;if(!gamedata.isMyShip(d))return;let b=Math.max(0,Math.round((s|0)/r.increment)*r.increment);const S=this.allocatedSteps(g)-this.fieldSteps(r,g[r.key]),v=Math.max(0,this.maxSteps()-S);if(b/r.increment>v&&(b=v*r.increment),r.key==="dmgBoost5"){const $=this.maxDamageSteps(g)*r.increment;b>$&&(b=$)}g[r.key]=b,r.key==="shotBoost"&&this.clampDamageToShots(g),typeof g.updateBoostNotes=="function"&&g.updateBoostNotes(),this.syncFlight(g),this.refresh()}syncFlight(r){const s=this.getFlightPulsars();for(let d=0;d<s.length;d++){const g=s[d];g!==r&&(Du.forEach(b=>{g[b.key]=r[b.key]|0}),typeof g.updateBoostNotes=="function"&&g.updateBoostNotes())}}step(r,s){const{system:d}=this.props;this.setValue(r,(d[r.key]|0)+s*r.increment)}onWheel(r,s){s.preventDefault(),this.step(r,s.deltaY<0?1:-1)}onInput(r,s){const d=String(s.target.value).replace(/[^0-9]/g,""),g=d===""?0:parseInt(d,10);this.setValue(r,r.parse(g))}propagate(){const{ship:r,system:s}=this.props;if(!gamedata.isMyShip(r))return;const d=this.getFlightPulsars();for(let g=0;g<d.length;g++){const b=d[g];b!==s&&(Du.forEach(S=>{b[S.key]=s[S.key]|0}),this.clampWeaponToBudget(b),typeof b.updateBoostNotes=="function"&&b.updateBoostNotes())}this.refresh()}getFlightPulsars(){const{ship:r}=this.props,s=[],d=r&&r.systems?r.systems:[];for(let g=0;g<d.length;g++){const b=d[g]&&d[g].systems?d[g].systems:[];for(let S=0;S<b.length;S++)b[S]&&b[S].name==="MinorThoughtPulsar"&&s.push(b[S])}return s}clampWeaponToBudget(r){const s=["dmgBoost5","shotBoost","hitBoost5"],d=b=>Du.find(S=>S.key===b);let g=0;for(;g++<200&&!(Du.reduce((S,v)=>S+(r[v.key]|0)/v.increment,0)<=this.maxSteps());)for(let S=0;S<s.length;S++){const v=s[S];if((r[v]|0)>0){r[v]=(r[v]|0)-d(v).increment;break}}}render(){const{ship:r,system:s}=this.props;if(!s)return null;const d=typeof s.getSpareThrust=="function"?s.getSpareThrust(r):shipManager.movement.getRemainingEngineThrust(r),g=this.allocatedSteps(s)*Gg,b=Math.max(0,d-g),S=b>=Gg;return y.jsxs(X$,{children:[y.jsx(Z$,{children:"Minor Thought Pulsar"}),y.jsxs(J$,{$empty:b<Gg,children:["Available thrust: ",b]}),Du.map(v=>{const T=s[v.key]|0,$=v.key==="dmgBoost5"&&T/v.increment>=this.maxDamageSteps(s);return y.jsxs(eA,{children:[y.jsx(tA,{children:v.label}),y.jsx(fC,{title:"Less",disabled:T<=0,onClick:()=>this.step(v,-1),children:"−"}),y.jsx(nA,{type:"text",value:rA(v,T),onChange:O=>this.onInput(v,O),onWheel:O=>this.onWheel(v,O)}),y.jsx(fC,{title:$?"One +5 per shot (add shots for more)":"More",disabled:!S||$,onClick:()=>this.step(v,1),children:"+"})]},v.key)})]})}}const fp=o=>{let r=null;const s=d=>{d.preventDefault(),o(d)};return d=>{r!==d&&(r&&r.removeEventListener("wheel",s,{passive:!1}),r=d,r&&r.addEventListener("wheel",s,{passive:!1}))}},qe={bg:"rgba(8, 12, 16, 0.96)",line:"#33414f",titleBg:"#1b242e",text:"#c7d3de",dim:"#6c7a87",btnBg:"#161d25",btnText:"#aebac6",well:"#05080b",focus:"#4d6070"},Br={enh:{rail:H.colors.enhLine,btnBg:"#292114",btnText:H.colors.enhTitle},damage:{rail:"#3d7a9c",btnBg:"#142129",btnText:"#a4cde3"},crit:{rail:"#a85c33",btnBg:"#291914",btnText:"#eab99e"}},aA={rail:qe.line,btnBg:qe.btnBg,btnText:qe.btnText},yl=o=>o.$ink||aA,oA=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${o=>o.$gold?H.colors.enhText:qe.text};
`,lA=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,sA=D.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;D.span`
    flex: 0 0 auto;
    color: ${o=>o.$gold?H.colors.enhText:qe.dim};
    font-size: 10px;
    opacity: ${o=>o.$gold?.75:1};
`;const od=D.div`
    width: 24px;
    height: 18px;
    flex: 0 0 24px;
    background: ${o=>yl(o).btnBg};
    border: 1px solid ${o=>yl(o).rail};
    color: ${o=>yl(o).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 10px;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${o=>yl(o).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${yl(o).btnBg};
            color: ${yl(o).btnText};
        }
    `}
`,pC=D.input`
    flex: 0 0 44px;
    width: 44px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${H.fonts.mono};
    font-size: 12px;
    color: ${o=>o.$destroyed?"#ff8a80":"#ffffff"};
    background-color: ${qe.well};
    border: 1px solid ${o=>yl(o).rail};
    outline: none;

    &:focus {
        border-color: ${o=>yl(o).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,hC=D.div`
    padding: 3px;
    background-color: ${qe.titleBg};
    border-bottom: 1px solid ${qe.line};
    color: ${qe.text};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    ${o=>o.$sticky?"position: sticky; top: 0;":""}
`,$x=D.div`
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
`,Ax=D.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 2px 0 0 ${o=>yl(o).rail};
`,uA=D($x)`
    background-color: ${H.colors.enhBg};
    border-top: 1px solid ${H.colors.enhLine};
    border-bottom: 1px solid ${H.colors.enhLine};
    color: ${H.colors.enhTitle};
`,cA=D($x)`
    background-color: #23506b;
    border-top: 1px solid ${Br.damage.rail};
    border-bottom: 1px solid ${Br.damage.rail};
    color: #e8f2ff;
`,dA=D($x)`
    background-color: #6d3823;
    border-top: 1px solid ${Br.crit.rail};
    border-bottom: 1px solid ${Br.crit.rail};
    color: #ffece2;
`,gC=D.div`
    height: 2px;
    background-color: ${o=>o.$chrome?qe.line:H.colors.enhLine};
    opacity: 0.8;
`,fA=D.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,pA=dA,hA=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 2px 8px;
    font-size: 11px;
    color: ${H.colors.warningSoft};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${o=>o.$empty&&`
        color: #6f6257;
    `}
`,gA=D.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,mA=D.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${qe.dim};
`,vA=D.div`
    flex: 0 0 auto;
    color: ${qe.dim};
`,yA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,xA=D.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${H.fonts.mono};
    font-size: 11px;
    color: ${o=>o.$empty?"#6f6257":"#ffffff"};
`,bA=D.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,wA=D.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,SA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px 4px;
`,CA=D.select`
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
`;const mC=(o,r,s,d)=>{const g=[];for(const b in o||{}){if(!o.hasOwnProperty(b))continue;const S=battleDamage.PARAM_CRITICALS[b],v=parseInt((d||{})[b],10)||0;g.push({type:b,isParam:!!S,paramLabel:S?S.label:null,label:battleDamage.critLabel(b,r,v),count:parseInt(o[b],10)||0,param:v,transient:!!(s&&s[b])})}return g};class vC extends Ze.Component{constructor(r){super(r),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(r){r.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=fp(s=>this.step(r,s.deltaY<0?1:-1))),this.wheelRefs[r]}critMap(){const r=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return r&&r.c?Object.assign({},r.c):{}}paramMap(){const r=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return r&&r.p?Object.assign({},r.p):{}}valueOf(r){return r.isParam?r.param:r.count}maxValueOf(r){if(!r.isParam)return battleDamage.critLimit(r.type);const s=battleDamage.PARAM_CRITICALS[r.type];return Math.min(battleDamage.MAX_CRIT_PARAM,s&&s.max||battleDamage.MAX_CRIT_PARAM)}setValue(r,s){const{ship:d,kind:g,reference:b,onChange:S}=this.props,v=this.critMap(),T=this.paramMap();s>0?r.isParam?(v[r.type]=1,T[r.type]=Math.min(s,this.maxValueOf(r))):v[r.type]=Math.min(s,battleDamage.critLimit(r.type)):(delete v[r.type],delete T[r.type]),battleDamage.setCriticals(d,g,b,v,T),S&&S()}step(r,s){const d=this.rowForType(r);this.setValue(d,this.valueOf(d)+s)}rowForType(r){const{ship:s}=this.props,d=battleDamage.PARAM_CRITICALS[r],g=parseInt(this.paramMap()[r],10)||0;return{type:r,isParam:!!d,paramLabel:d?d.label:null,label:battleDamage.critLabel(r,s.preBattleCritDesc,g),count:parseInt(this.critMap()[r],10)||0,param:g,transient:!!(s.preBattleCritTransient&&s.preBattleCritTransient[r])}}displayRows(r){const{ship:s,kind:d,reference:g}=this.props;return battleDamage.rememberCriticals(s,d,g,(r||[]).map(S=>S.type)).map(S=>this.rowForType(S))}addableTypes(r){const{ship:s,kind:d,reference:g}=this.props,b=battleDamage.offerableCriticals(s,d,g,this.state.showAll),S={};return r.forEach(v=>{S[v]=!0}),b.filter(v=>!S[v]).map(v=>({type:v,label:this.pickerLabel(v)})).sort((v,T)=>v.label.localeCompare(T.label))}pickerLabel(r){const s=battleDamage.PARAM_CRITICALS[r];return s?s.label:battleDamage.critLabel(r,this.props.ship.preBattleCritDesc,0)}onAdd(r){r&&this.setValue(this.rowForType(r),1)}render(){const{ship:r,rows:s,editable:d}=this.props,g=d?this.displayRows(s):s||[],b=!!(d&&battleDamage.catalogueFor(r)),S=b?this.addableTypes(g.map(v=>v.type)):[];return!g.length&&!b?null:y.jsxs(fA,{children:[y.jsx(gC,{$chrome:!0}),y.jsx(pA,{children:"Critical Effects"}),y.jsxs(Ax,{$ink:Br.crit,children:[g.map(v=>{const T=this.valueOf(v),$=this.maxValueOf(v);return y.jsxs(hA,{$empty:d&&T<=0,children:[y.jsxs(gA,{title:v.type,children:[d&&v.isParam?v.paramLabel:v.label,v.transient&&y.jsx(mA,{children:"(turn 1 only)"})]}),d?y.jsxs(yA,{children:[y.jsx(od,{$ink:Br.crit,title:v.isParam?"Reduce":"One fewer",disabled:T<=0,onClick:()=>this.step(v.type,-1),children:"−"}),y.jsx(xA,{$empty:T<=0,ref:this.wheelRef(v.type),children:T}),y.jsx(od,{$ink:Br.crit,title:v.isParam?"Increase":T>=$&&$===1?"This effect only applies once":"One more",disabled:T>=$,onClick:()=>this.step(v.type,1),children:"+"})]}):v.count>1&&y.jsxs(vA,{children:["(x",v.count,")"]})]},v.type)}),b&&y.jsx(SA,{children:y.jsxs(CA,{value:"",disabled:S.length===0,title:"Add a critical effect to this unit before the battle",onChange:v=>this.onAdd(v.target.value),children:[y.jsx("option",{value:"",children:S.length?"+ Add effect…":"Nothing to add"}),S.map(v=>y.jsx("option",{value:v.type,children:v.label},v.type))]})})]})]})}}const EA=D.div`
    display: flex;
    justify-content: flex-end;
    padding: 2px 8px 4px;
    font-size: 10px;
    color: ${H.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,TA=D.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${H.fonts.mono};
    font-size: 10px;
    color: ${H.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${o=>o.$spent?.6:1};
`;class kA extends Ze.Component{constructor(r){super(r),this.wheelRef=fp(s=>this.step(s.deltaY<0?1:-1))}step(r){const{row:s,onChange:d}=this.props,g=Math.max(0,Math.min(s.max,s.count+r));g!==s.count&&d(s.enhID,g)}onInput(r){const{row:s,onChange:d}=this.props,g=String(r.target.value).replace(/[^0-9]/g,""),b=g===""?0:parseInt(g,10);d(s.enhID,Math.max(0,Math.min(s.max,b)))}render(){const{row:r}=this.props,s=r.count>=r.max,d=r.max>1?`${r.label} - ${r.count}/${r.max} levels`+(r.count>0?`, ${r.price} pts spent`:"")+(s?"":`; next level ${r.nextPrice} pts`):`${r.label} - ${r.price||r.nextPrice} pts`;return y.jsxs(oA,{$gold:!0,title:d,children:[y.jsx(lA,{children:y.jsx(sA,{children:r.label})}),y.jsx(od,{$ink:Br.enh,title:"Remove a level",disabled:r.count<=0,onClick:()=>this.step(-1),children:"−"}),y.jsx(pC,{ref:this.wheelRef,$ink:Br.enh,type:"text",value:r.count,onChange:g=>this.onInput(g)}),y.jsx(od,{$ink:Br.enh,title:r.count>=r.max?"Already at the maximum":`Add a level (${r.nextPrice} pts)`,disabled:r.count>=r.max,onClick:()=>this.step(1),children:"+"}),y.jsx(TA,{$spent:s,title:s?`Fully bought - ${r.price} pts`:"Cost of the next level",children:s?`${r.price}p`:`${r.nextPrice}p`})]})}}class RA extends Ze.Component{render(){const{rows:r,onChange:s}=this.props;if(!r||r.length===0)return null;const d=r.reduce((g,b)=>g+(b.count>0?b.price:0),0);return y.jsxs(Nn.Fragment,{children:[y.jsx(uA,{children:"✦ ENHANCEMENTS"}),y.jsxs(Ax,{$ink:Br.enh,children:[r.map(g=>y.jsx(kA,{row:g,onChange:s},g.enhID)),d>0&&y.jsxs(EA,{children:["Refits: ",d," pts"]})]})]})}}const DA=D.div`
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
`,MA=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 6px;
    font-size: 11px;
    color: ${qe.text};
`,OA=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,$A=D.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${o=>o.$disabled?"not-allowed":"pointer"};
    user-select: none;
    opacity: ${o=>o.$disabled?.4:1};
    color: ${o=>o.$on?"#ff8a80":qe.dim};
`,AA=D.span`
    flex: 0 0 auto;
    color: ${qe.dim};
    font-size: 10px;
`,jA=D.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;class _A extends Ze.Component{constructor(r){super(r),this.wheelRef=fp(s=>this.step(s.deltaY<0?1:-1))}entry(){const{ship:r,system:s}=this.props;return battleDamage.getEntry(r,battleDamage.KIND_SYSTEM,s.id)||{}}remaining(){const{system:r}=this.props,s=this.entry();return s.k?0:Math.max(0,r.maxhealth-(parseInt(s.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return this.isIndestructible()?1:0}setRemaining(r){const{ship:s,system:d}=this.props,g=d.maxhealth,b=Math.min(this.floor(),g);let S=parseInt(r,10);isNaN(S)&&(S=g),S=Math.max(b,Math.min(g,S));const v=g-S,T=S===0&&g>0;T&&this.rememberHealth(),battleDamage.setSystem(s,d.id,{d:v,k:T?1:0}),this.refresh()}rememberHealth(){const{ship:r,system:s}=this.props;this.isDestroyed()||battleDamage.rememberHealth(r,battleDamage.KIND_SYSTEM,s.id,this.remaining())}setDestroyed(r){const{ship:s,system:d}=this.props;if(r&&this.isIndestructible())return;if(r){this.rememberHealth(),battleDamage.setSystem(s,d.id,{d:d.maxhealth,k:1}),this.refresh();return}const g=battleDamage.healthMemory(s,battleDamage.KIND_SYSTEM,d.id),b=g>0?Math.min(d.maxhealth,g):d.maxhealth;battleDamage.setSystem(s,d.id,{d:d.maxhealth-b,k:0}),this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r);let s=[];if(window.systemEnhancements){const d=parseFloat(r.pointCostSysEnh)||0;s=systemEnhancements.dropDestroyed(r),s.length&&this.settleRefitCost(r,d)}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate(),s.length&&window.confirm&&typeof confirm.warning=="function"&&confirm.warning(systemEnhancements.describeRemoved(s))}settleRefitCost(r,s){const d=parseFloat(r.pointCostSysEnh)||0;r.pointCost=(parseFloat(r.pointCost)||0)-(s-d),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet()}setEnhancement(r,s){const{ship:d,system:g}=this.props;if(!window.systemEnhancements)return;const b=parseFloat(d.pointCostSysEnh)||0,S=systemEnhancements.taken(d,g.id,r);if(s===S)return;if(systemEnhancements.set(d,g.id,r,s),this.settleRefitCost(d,b),!(!window.gamedata||typeof gamedata.canAffordRefit!="function"||gamedata.canAffordRefit(d))){const T=parseFloat(d.pointCostSysEnh)||0;systemEnhancements.set(d,g.id,r,S),this.settleRefitCost(d,T),systemEnhancements.apply(d),this.forceUpdate(),window.confirm&&typeof confirm.error=="function"&&confirm.error("You cannot afford that enhancement!",function(){});return}systemEnhancements.apply(d),this.refresh()}step(r){this.setRemaining(this.remaining()+r)}onInput(r){const s=String(r.target.value).replace(/[^0-9]/g,"");this.setRemaining(s===""?0:parseInt(s,10))}render(){const{ship:r,system:s}=this.props;if(!s||!(s.maxhealth>0))return null;const d=this.remaining(),g=this.isDestroyed(),b=this.isIndestructible(),S=this.entry(),v=mC(S.c,r.preBattleCritDesc,r.preBattleCritTransient,S.p),T=window.systemEnhancements&&!g?systemEnhancements.menuRowsFor(r,s):[];return y.jsxs(DA,{onClick:$=>$.stopPropagation(),children:[y.jsx(RA,{rows:T,onChange:($,O)=>this.setEnhancement($,O)}),T.length>0&&y.jsx(gC,{}),y.jsx(cA,{children:"Damage"}),y.jsx(Ax,{$ink:Br.damage,children:y.jsxs(MA,{children:[y.jsxs(OA,{title:`${s.displayName||s.name} (system id ${s.id})`,children:[y.jsx(jA,{children:s.displayName||s.name}),y.jsxs(AA,{children:["#",s.id]})]}),y.jsx(od,{$ink:Br.damage,title:b&&d<=1?"A reactor cannot be destroyed before the battle":"More damage",disabled:g||d<=this.floor(),onClick:()=>this.step(-1),children:"−"}),y.jsx(pC,{ref:this.wheelRef,$ink:Br.damage,type:"text",$destroyed:g,disabled:g,value:g?0:d,onChange:$=>this.onInput($)}),y.jsx(od,{$ink:Br.damage,title:"Repair",disabled:g||d>=s.maxhealth,onClick:()=>this.step(1),children:"+"}),y.jsxs($A,{$on:g,$disabled:b,title:b?"A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship":"Mark this system destroyed before the battle starts",children:[y.jsx(bA,{type:"checkbox",checked:g,disabled:b,onChange:$=>this.setDestroyed($.target.checked)}),y.jsx(wA,{children:"Destroy"})]})]})}),y.jsx(vC,{ship:r,kind:battleDamage.KIND_SYSTEM,reference:s.id,rows:v,editable:!0,onChange:()=>this.refresh()})]})}}const yC=D.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,xC=D.div`
    display: flex;
    flex-wrap: wrap;
`,ms=D.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${o=>o.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    mix-blend-mode: ${o=>o.$blend||"normal"};
    ${Vi}

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;class LA extends Ze.Component{constructor(r){super(r)}online(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onOnlineClicked(s,d),webglScene.customEvent("CloseSystemInfo")}offline(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Qg(s,d)&&(shipManager.power.onOfflineClicked(s,d),webglScene.customEvent("CloseSystemInfo"))}allOnline(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onlineAll(s,d),webglScene.customEvent("CloseSystemInfo")}allOffline(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Qg(s,d)&&(shipManager.power.offlineAll(s,d),webglScene.customEvent("CloseSystemInfo"))}overload(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onOverloadClicked(s,d),webglScene.customEvent("CloseSystemInfo")}stopOverload(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.onStopOverloadClicked(s,d),webglScene.customEvent("CloseSystemInfo")}boost(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.clickPlus(s,d)}deboost(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;shipManager.power.clickMinus(s,d)}addShots(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Kx(s,d)&&weaponManager.changeShots(s,d,1)}reduceShots(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Qx(s,d)&&weaponManager.changeShots(s,d,-1)}removeFireOrderMulti(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;qx(s,d)&&weaponManager.removeFiringOrderMulti(s,d)}removeFireOrder(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;qg(s,d)&&(weaponManager.removeFiringOrder(s,d),webglScene.customEvent("CloseSystemInfo"))}removeFireOrderAll(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;qg(s,d)&&(weaponManager.removeFiringOrderAll(s,d),webglScene.customEvent("CloseSystemInfo"))}allChangeFiringMode(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;if(Ou(s,d)){weaponManager.onModeClicked(s,d);var g=d.firingMode,b=[];s.flight?b=s.systems.map(_=>_.systems).reduce((_,V)=>_.concat(V),[]).filter(_=>_.weapon):b=s.systems.filter(_=>_.weapon);for(var S=weaponManager.stripPairingSuffix(d.displayName),v=new Array,T=0;T<b.length;T++)S===weaponManager.stripPairingSuffix(b[T].displayName)&&d.weapon&&v.push(b[T]);for(var T=0;T<v.length;T++){var $=v[T];if($.firingMode!=g&&Ou(s,$)){for(var O=$.firingMode,P=0;$.firingMode!=g&&P<2;)weaponManager.onModeClicked(s,$),$.firingMode==1&&P++;if($.firingMode!=g)for(;$.firingMode!=O;)weaponManager.onModeClicked(s,$)}}}}changeFiringMode(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;Ou(s,d)&&weaponManager.onModeClicked(s,d)}selectAllWeapons(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;weaponManager.selectAllWeapons(s,d,"forceSelect"),webglScene.customEvent("CloseSystemInfo")}deselectAllWeapons(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;weaponManager.selectAllWeapons(s,d,"forceDeselect"),webglScene.customEvent("CloseSystemInfo")}declareSelfIntercept(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;if(hp(s,d)){if(weaponManager.onDeclareSelfInterceptSingle(s,d),d.canSplitShots)var g=d.checkFinished();g&&webglScene.customEvent("CloseSystemInfo")}}declareSelfInterceptAll(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(s,d),d.canSplitShots)var g=d.checkFinished();g&&webglScene.customEvent("CloseSystemInfo")}remSelfIntercept(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;gp(s,d)&&weaponManager.removeSelfInterceptSingle(s,d)}nextCurrClass(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;d.nextCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}prevCurrClass(r){r.stopPropagation(),r.preventDefault();const{ship:s,system:d}=this.props;d.prevCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:s,system:d})}render(){const{ship:r,selectedShip:s,system:d}=this.props;return Gx(r,d)?gamedata.gamephase===-2?y.jsx(yC,{children:y.jsx(_A,{ship:r,system:d})}):y.jsxs(yC,{children:[MC(r,d)&&y.jsx(M$,{ship:r,system:d}),y.jsxs(xC,{children:[Kx(r,d)&&y.jsx(ms,{title:"More shots",onClick:this.addShots.bind(this),img:"./img/plussquare.png"}),Qx(r,d)&&y.jsx(ms,{title:"Less shots",onClick:this.reduceShots.bind(this),img:"./img/minussquare.png"}),qx(r,d)&&y.jsx(ms,{title:"Remove last fire order",onClick:this.removeFireOrderMulti.bind(this),img:"./img/unfiringSmall.png"}),qg(r,d)&&y.jsx(ms,{title:"Remove all fire orders (RMB = All weapons selected)",onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:"./img/firing.png"})]}),(Ou(r,d)||hp(r,d)||gp(r,d))&&y.jsxs(fO,{ship:r,system:d,showModes:Ou(r,d),children:[hp(r,d)&&y.jsx(ms,{title:"Allow interception (RMB = All systems selected)",onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:"./img/addSelfIntercept.png"}),gp(r,d)&&y.jsx(ms,{title:"Remove an intercept order",onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:"./img/remSelfIntercept.png"})]}),y.jsxs(xC,{children:[jx(r,d)&&y.jsx(ms,{title:"Select all weapons of this type",onClick:this.selectAllWeapons.bind(this),img:"./img/selectAllWeapons.png",$blend:"screen"}),jx(r,d)&&y.jsx(ms,{title:"Deselect all weapons of this type",onClick:this.deselectAllWeapons.bind(this),img:"./img/deselectAllWeapons.png",$blend:"screen"})]}),Xx(r,d)&&y.jsx(T$,{ship:r,system:d}),_x(r,d)&&y.jsx(YO,{ship:r,system:d}),Fx(r,d)&&y.jsx(t$,{system:d,ship:r}),Ix(r,d)&&y.jsx(u$,{system:d,ship:r}),Nx(r,d)&&y.jsx(F$,{system:d,ship:r}),Px(r,d)&&y.jsx(G$,{system:d,ship:r}),Lx(r,d)&&y.jsx(q$,{system:d,ship:r}),zx(r,d)&&y.jsx(iA,{system:d,ship:r}),(Ux(r,d)||Bx(r,d))&&y.jsx(tC,{system:d,ship:r}),(Hx(r,d)||Vx(r,d))&&y.jsx(tC,{system:d,ship:r}),Kg(r,d)&&y.jsx(TO,{ship:r,system:d,readOnly:!zA(r,d)}),Wx(r,d)&&y.jsx(NO,{ship:r,system:d,readOnly:!NA(r,d)}),"   ",Xg(r,d)&&y.jsx(y$,{ship:r,system:d}),DC(r,d)&&y.jsx(b$,{ship:r,system:d})]}):null}}const jx=(o,r)=>!(!window.matchMedia("(pointer: coarse)").matches||!r.weapon||pp(o,r)||gamedata.gamephase!=3&&!r.ballistic&&!r.preFires||gamedata.gamephase!=1&&r.ballistic||gamedata.gamephase!=5&&r.preFires),_x=(o,r)=>gamedata.gamephase===1&&r.name=="adaptiveArmorController",Lx=(o,r)=>r.name==="GraviticAugmenter"&&gamedata.isMyShip(o)&&!r.stowed&&!shipManager.power.isOffline(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(o,r)||gamedata.gamephase===5),zx=(o,r)=>r.name==="MinorThoughtPulsar"&&gamedata.gamephase===3&&gamedata.isMyShip(o)&&!r.stowed&&!shipManager.systems.isDestroyed(o,r)&&!shipManager.power.isOffline(o,r),Nx=(o,r)=>gamedata.gamephase===-1&&o.mine&&(o.spawned==-1&&gamedata.turn==1||o.spawned==gamedata.turn-1)&&(r.name=="CaptorMine"||r.name=="MineControllerDEW"),Px=(o,r)=>gamedata.gamephase===-1&&o.mine&&(o.spawned==-1&&gamedata.turn==1||o.spawned==gamedata.turn-1)&&r.name=="ProximityMine",Fx=(o,r)=>gamedata.gamephase===1&&r.name=="hyachComputer",Ix=(o,r)=>r.name==="hyachSpecialists",Ux=(o,r)=>gamedata.gamephase===1&&r.name==="ThirdspaceShield",Bx=(o,r)=>gamedata.gamephase===1&&r.name==="ThirdspaceShieldGenerator",Hx=(o,r)=>gamedata.gamephase===1&&r.name==="ThoughtShield",Vx=(o,r)=>gamedata.gamephase===1&&r.name==="ThoughtShieldGenerator",Kg=(o,r)=>gamedata.isMyShip(o)&&r.name=="SelfRepair",zA=(o,r)=>Kg(o,r)&&gamedata.gamephase===1,Wx=(o,r)=>gamedata.isMyShip(o)&&(r.name=="StructureSelfRepair"||r.name=="CoopStructureSelfRepair"),NA=(o,r)=>Wx(o,r)&&gamedata.gamephase===1,bC=()=>typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted(),Mu=(o,r)=>gamedata.gamephase===-2&&!bC()&&o&&o.userid!=0&&!o.flight&&!o.mine&&!PA(r),wC=(o,r)=>Mu(o,r)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(o,r)&&systemEnhancements.offersFor(o,r).length>0,Yx=o=>gamedata.gamephase===-2&&!bC()&&o&&o.userid!=0&&!!o.mine&&battleDamage.mineMaxHealth(o)>1,PA=o=>!o||!(o.maxhealth>0)||o.isTargetable===!1||!!o.hideInShipWindow||Array.isArray(o.systems),Gx=(o,r)=>gamedata.gamephase===-2?Mu(o,r)||wC(o,r):Qg(o,r)||SC(o,r)||CC(o,r)||EC(o,r)||TC(o,r)||kC(o,r)||Kx(o,r)||Qx(o,r)||qx(o,r)||qg(o,r)||Ou(o,r)||hp(o,r)||gp(o,r)||_x(o,r)||Fx(o,r)||Ix(o,r)||Ux(o,r)||Hx(o,r)||Bx(o,r)||Vx(o,r)||Kg(o,r)||FA(o,r)||IA(o,r)||Xg(o,r)||DC(o,r)||Xx(o,r)||jx(o,r)||Nx(o,r)||Px(o,r)||Lx(o,r)||zx(o,r),Qg=(o,r)=>gamedata.gamephase===1&&(r.canOffLine||r.powerReq>0)&&!r.powerLocked&&!shipManager.power.isOffline(o,r)&&!shipManager.power.getBoost(r)&&!weaponManager.hasFiringOrder(o,r),SC=(o,r)=>gamedata.gamephase===1&&shipManager.power.isOffline(o,r)&&!shipManager.power.isForcedOffline(o,r)&&!shipManager.power.isVortexLockedOffline(o,r),CC=(o,r)=>gamedata.gamephase===1&&!shipManager.power.isOffline(o,r)&&r.weapon&&r.overloadable&&!shipManager.power.isOverloading(o,r),EC=(o,r)=>gamedata.gamephase===1&&r.weapon&&r.overloadable&&shipManager.power.isOverloading(o,r)&&(r.overloadshots>=r.extraoverloadshots||r.overloadshots==0),TC=(o,r)=>r.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(o,r)&&(!r.isScanner()||r.id==shipManager.power.getHighestSensorsId(o))&&r.name!=="ThirdspaceShieldGenerator"&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor",kC=(o,r)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(r)&&r.name!=="ThirdspaceShieldGenerator"&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor",RC=(o,r)=>{const s=weaponManager.getFiringOrder(o,r);return s&&s.type!=="intercept"&&s.type!=="selfIntercept"?s:null},Kx=(o,r)=>{if(pp(o,r)||!r.weapon||!r.canChangeShots||!weaponManager.hasFiringOrder(o,r))return!1;const s=RC(o,r);return!!s&&s.shots<r.maxVariableShots},Qx=(o,r)=>{if(pp(o,r)||!r.weapon||!r.canChangeShots||!weaponManager.hasFiringOrder(o,r))return!1;const s=RC(o,r);return!!s&&s.shots>1},pp=(o,r)=>r.name==="jumpEngine"&&typeof r.getHeldVortex=="function"&&!!r.getHeldVortex(),qx=(o,r)=>r.weapon&&weaponManager.hasOrderForMode(r)&&r.canSplitShots&&!pp(o,r),qg=(o,r)=>r.weapon&&weaponManager.hasFiringOrder(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked())&&!pp(o,r),Ou=(o,r)=>r.weapon&&!o.mine&&!r.stowed&&!r.hideFiringModeSelector&&r.name!=="GraviticAugmenter"&&r.name!=="MinorThoughtPulsar"&&(gamedata.gamephase===1&&r.ballistic||gamedata.gamephase===5&&r.preFires||gamedata.gamephase===3&&!r.ballistic&&!r.preFires)&&(!weaponManager.hasFiringOrder(o,r)||r.multiModeSplit)&&Object.keys(r.firingModes).length>1,hp=(o,r)=>r.weapon&&weaponManager.canSelfInterceptSingle(o,r),gp=(o,r)=>r.weapon&&r.canSplitShots&&weaponManager.canRemInterceptSingle(o,r),FA=(o,r)=>r.canActivate&&typeof r.canActivate=="function"&&r.canActivate()&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor"&&r.name!=="GraviticAugmenter"&&r.name!=="jumpEngine",IA=(o,r)=>r.canDeactivate&&typeof r.canDeactivate=="function"&&r.canDeactivate()&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor"&&r.name!=="GraviticAugmenter"&&r.name!=="jumpEngine",DC=(o,r)=>r.name==="jumpEngine"&&typeof r.canMaintainVortex=="function"&&(r.canMaintainVortex()||r.canDeactivate()),Xg=(o,r)=>r.name==="powerCapacitor"||r.name==="PowerCapacitor",MC=(o,r)=>Qg(o,r)||SC(o,r)||CC(o,r)||EC(o,r)||r.boostable&&(TC(o,r)||kC(o,r)),Xx=(o,r)=>Xg(o,r)||r.name==="GraviticAugmenter"||r.name==="jumpEngine"?!1:!!(r.canActivate&&typeof r.canActivate=="function"&&r.canActivate()||r.canDeactivate&&typeof r.canDeactivate=="function"&&r.canDeactivate()),UA=(o,r)=>gamedata.gamephase===-2?Mu(o,r)||wC(o,r):_x(o,r)||Fx(o,r)||Ix(o,r)||Nx(o,r)||Px(o,r)||Lx(o,r)||zx(o,r)||Ux(o,r)||Bx(o,r)||Hx(o,r)||Vx(o,r)||Kg(o,r)||Wx(o,r)||Xg(o,r)||MC(o,r)||Xx(o,r)||Ou(o,r)||hp(o,r)||gp(o,r),OC=D.div`
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
`,$C=D.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,BA=D.div`
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
    color: ${H.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,AC=D.div`
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
    
    ${$C} {
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
`;class Zx extends Ze.Component{constructor(r){super(r),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(r){if(r.stopPropagation(),r.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:s,ship:d}=this.props;s=shipManager.systems.initializeSystem(s);const g=Mu(d,s);if(!((gamedata.waiting||gamedata.replay)&&!g)&&!(!g&&(shipManager.isDestroyed(d)||shipManager.isDestroyed(d,s)&&!s.clickableWhenDestroyed))){if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(d)){var b=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(b&&b.ship.id!=d.id&&!weaponManager.isSelectedWeapon(s)){window.uiEvents.relay("SystemTargeted",{ship:d,system:s});return}}var S=s.weapon&&typeof s.isSpentLocked=="function"&&s.isSpentLocked();if(!S&&(s.weapon&&gamedata.gamephase===3&&!s.ballistic&&!s.preFires||gamedata.gamephase===1&&s.ballistic||gamedata.gamephase===5&&s.preFires||weaponManager.canManuallyInterceptWith(d,s))&&!shipManager.isAdrift(d)&&gamedata.isMyShip(d)){if(s.hasSpecialTargeting&&typeof s.reopenSpecialTargeting=="function"&&weaponManager.hasFiringOrder(d,s)&&s.reopenSpecialTargeting(d))return;var v=weaponManager.hasFiringOrder(d,s),T=v&&v!=="self"&&!s.canSplitShots&&!s.hasSpecialTargeting;weaponManager.isSelectedWeapon(s)?weaponManager.unSelectWeapon(d,s):T||weaponManager.selectWeapon(d,s)}if(gamedata.isMyShip(d)&&(s.name==="hangar"||s.name==="catapult"||s.name==="fighterRail")){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog=="function"&&window.DeploymentDock.shipHasOpenableDockDialog(d)&&window.confirm&&typeof window.confirm.hangarDeployDock=="function"){window.confirm.hangarDeployDock(d);return}if(gamedata.gamephase===3&&!s.isShadowHangar&&!shipManager.movement.isRolling(d)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(d)!=="no")&&window.confirm&&typeof window.confirm.hangarLaunch=="function"){window.confirm.hangarLaunch(d);return}}if(gamedata.isMyShip(d)&&(s.name==="dockingCollar"||s.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(d)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(d)!=="no")&&typeof window.lcvRailLaunchable=="function"&&window.lcvRailLaunchable(d,s)&&window.confirm&&typeof window.confirm.lcvLaunch=="function"){window.confirm.lcvLaunch(d);return}gamedata.isMyShip(d)?window.uiEvents.relay("SystemClicked",{ship:d,system:s,element:r.currentTarget,showMenu:!0}):window.uiEvents.relay("SystemTargeted",{ship:d,system:s})}}onSystemMouseOver(r){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;r.stopPropagation(),r.preventDefault();let{system:s,ship:d}=this.props;s=shipManager.systems.initializeSystem(s),window.uiEvents.relay("SystemMouseOver",{ship:d,system:s,element:r.currentTarget,showInfo:!0})}onSystemMouseOut(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),r.preventDefault(),window.uiEvents.relay("SystemMouseOut"))}onTouchStart(r){r.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:g,ship:b}=this.props;g=shipManager.systems.initializeSystem(g),window.uiEvents.relay("SystemMouseOver",{ship:b,system:g,element:s,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(r){if(r.stopPropagation(),!this.longPressTimer)return;const s=r.touches[0],d=s.clientX-this.touchStartX,g=s.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(g)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(r){r.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onTouchEnd(r){r.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(r){if(r.stopPropagation(),r.preventDefault(),window.matchMedia("(pointer: coarse)").matches)return;let{system:s,ship:d}=this.props;s=shipManager.systems.initializeSystem(s),s.weapon&&weaponManager.selectAllWeapons(d,s)}render(){let{system:r,ship:s,scs:d,fighter:g,destroyed:b,mirror:S}=this.props;return r=shipManager.systems.initializeSystem(r),r=shipManager.systems.initializeSystem(r),(Jx(s,r)||b)&&!r.clickableWhenDestroyed&&!Mu(s,r)?y.jsxs(AC,{$background:LC(r),$destroyed:!0,$mirror:S,children:[jC(s,r),y.jsx(OC,{$health:"0"})]}):y.jsxs(AC,{$scs:d,$highlight:tj(s,r),$destroyed:Jx(s,r)||b,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:LC(r),$mirror:S,$offline:KA(s,r),$loading:YA(r),$loadedAlternate:GA(r),$selected:nj(r),$firing:HA(s,r),$intercepting:VA(s,r),$calledShot:WA(s,r),$boosted:qA(s,r),$off:QA(r),$docked:_C(r),$orderPending:ZA(r),children:[jC(s,r),y.jsx($C,{children:rj(s,r)}),(!g||zC(r))&&y.jsx(OC,{$scs:d,$health:Jx(s,r)||b?0:JA(s,r),$criticals:zC(r),$criticalsBenign:ej(r),$docked:XA(r)})]})}}const jC=(o,r)=>!window.systemEnhancements||!o||!r||!systemEnhancements.hasAny(o,r.id)?null:y.jsx(BA,{title:"Carries a system enhancement",children:"✦"}),HA=(o,r)=>weaponManager.hasFiringOrder(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked()),VA=(o,r)=>weaponManager.isInterceptOnly(o,r),WA=(o,r)=>!r.weapon||!weaponManager.hasFiringOrder(o,r)?!1:weaponManager.getCalledShotInfo(o,r)!==null,YA=o=>o.weapon&&(!weaponManager.isLoaded(o)||typeof o.isSpentLocked=="function"&&o.isSpentLocked()),GA=o=>o.weapon&&weaponManager.isLoadedAlternate(o),KA=(o,r)=>shipManager.power.isOffline(o,r),QA=o=>o.activeMeansOff&&o.active,qA=(o,r)=>shipManager.power.isBoosted(o,r)||r.active&&!r.activeMeansOff&&!r.suppressActiveBoost,_C=o=>!!(o.showDockedVisual&&o.activeEffective||o.stowed&&o.stowedArcStart==null||o.dockedWithOrbital),XA=o=>_C(o)||!!o.stowed,ZA=o=>!!(o.showDockedVisual&&typeof o.hasPendingDockingOrder=="function"&&o.hasPendingDockingOrder()),JA=(o,r)=>(r.name==="ThirdspaceShield"||r.name==="ThoughtShield")&&r.baseRating?Math.min(100,r.currentHealth/r.baseRating*100):(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,Jx=(o,r)=>shipManager.systems.isDestroyed(o,r),LC=o=>o.name=="thruster"&&!o.iconPath?window.AssetManager.getSmartImagePath("./img/systemicons/thruster"+o.direction+".png"):o.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${o.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${o.name}.png`),zC=o=>shipManager.criticals.hasCriticalsIcon(o),ej=o=>shipManager.criticals.hasOnlyCritical(o,"HangarOperations",!0)||shipManager.criticals.hasOnlyCritical(o,"LCVLaunchedThisTurn",!0),tj=(o,r)=>shipManager.systems.hasBorderHighlight(o,r),nj=o=>weaponManager.isSelectedWeapon(o),rj=(o,r)=>{if(r.outputDisplay!==void 0&&r.outputDisplay!==null&&r.outputDisplay!="")return r.outputDisplay;if(r.weapon){if(r.stowed&&r.stowedArcStart==null)return"-";if(typeof r.isSpentLocked=="function"&&r.isSpentLocked())return"✓";if(typeof r.getVortexIconLoad=="function"){const g=r.getVortexIconLoad();if(g!=null)return g}const d=weaponManager.hasFiringOrder(o,r);if(d&&r.canChangeShots)return weaponManager.getFiringOrder(o,r).shots+"/"+r.shots;if(d){var s=weaponManager.getCalledShotInfo(o,r);if(s)return"⊕"}else if(!d){let g=weaponManager.getWeaponCurrentLoading(r),b=r.loadingtime;r.normalload>0&&(b=r.normalload),g>b&&(g=b);let S="";return r.overloadturns>0&&shipManager.power.isOverloading(o,r)&&(S="("+r.overloadturns+")"),r.overloadshots>0?"S"+r.overloadshots:g+S+"/"+b}}else{if(r.outputType==="thrust")return shipManager.movement.getRemainingEngineThrust(o);if(r.outputType==="power"){let d=shipManager.power.getReactorPower(o,r);return gamedata.gamephase>1&&d<0?0:d}else return shipManager.systems.getOutput(o,r)}},ij=D.div`
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
`;class aj extends Ze.Component{constructor(r){super(r)}getWeapons(r,s){return r.flight?r.systems.map(d=>d.systems).reduce((d,g)=>d.concat(g),[]).filter(d=>d.weapon):r.systems.filter(d=>d.weapon||d.outputType==="thrust"||d.outputType==="EW"||d.outputType==="power"||d.outputType==="settings")}render(){const{ship:r,gamePhase:s}=this.props;if(!r)return null;const d=this.getWeapons(r,s);return y.jsx(ij,{children:d.map((g,b)=>y.jsx(Zx,{fighter:r.flight,system:g,ship:r},`system-${b}`))})}}const NC=o=>{if(!o.hitChart)return[];const r=["Primary","Front","Aft","Port","Starboard"];let s=5;o.base&&!o.smallBase?(r[1]="Sections",s=2):o.SixSidedShip&&(r[31]="Port Front",r[32]="Port Aft",r[41]="Starboard Front",r[42]="Starboard Aft",s=43);const d=[];for(let g=0;g<s;g++){if(o.hitChart[g]===void 0)continue;const b=[];let S=0;for(const v in o.hitChart[g]){const T=Math.floor((v-S)/20*100);S=v;let $=o.hitChart[g][v];const O=$.indexOf(":");O>0&&($=$.substring(O+1)),b.push({name:$,chance:T})}d.push({location:g,name:r[g],entries:b})}return d},oj=D.div`
    ${o=>o.$tightBottom?"& > *:last-child { display: none; }":""}
    ${o=>o.$compactText?`
    ${en} {
        font-size: 10px;
        line-height: 1.4;
        color: ${H.colors.textAccent};
    }
    ${yr} {
        font-size: 10px;
        font-style: normal;
        color: ${H.colors.text};
    }`:""}
`;class PC extends Ze.Component{render(){const{ship:r,hideHitChart:s,tightBottom:d,compactText:g}=this.props,b=!!r.mine||window.gamedata&&typeof gamedata.isTerrain=="function"&&gamedata.isTerrain(r.shipSizeClass,r.userid),S=!!r.flight||b;var v=new Array,T=new Array,$=new Array;r.notes&&(v=r.notes.split("<br>")),r.hitChart&&!s&&NC(r).forEach(function(J){T[J.name]=J.entries.map(function(xe){return xe.name+" "+xe.chance+"%"}).join(", ")}),r.enhancementTooltip!=""&&($=r.enhancementTooltip.split("<br>"));let O={};if(!r.flight&&r.hasAttached&&Object.keys(r.hasAttached).length>0){const J={1:"Forward",2:"Aft",3:"Port",31:"Port-Forward",32:"Port-Aft",4:"Starboard",41:"Starboard-Forward",42:"Starboard-Aft"};for(let xe in r.hasAttached){let Pe=r.hasAttached[xe],de=J[Pe]||"Unknown";O[de]||(O[de]=0),O[de]++}}let P=r.offensivebonus;r.flight&&gamedata.areMinesPresent&&(r.minesweeper?P-=window.ew.getDetectMEW(r):P-=window.ew.getDetectMEW(r)*2);var _=0,V=!0;if(r.mine){var N=shipManager.systems.getSystemByName(r,"mineStealth");N&&!N.isMineRevealed(r)&&(V=!1,T=new Array,v=["No details known, scan with OEW to identify."],$=new Array)}return y.jsxs(oj,{$tightBottom:d,$compactText:g,children:[r.flight&&V&&y.jsxs(en,{children:[y.jsx(yr,{children:"Offensive bonus: "}),P*5]},_++),r.flight&&V&&y.jsxs(en,{children:[y.jsx(yr,{children:"Armor (F/S/A): "}),shipManager.systems.getFlightArmour(r)]},_++),r.flight&&V&&y.jsxs(en,{children:[y.jsx(yr,{children:"Profile - Front/Side: "}),r.forwardDefense*5,"/",r.sideDefense*5]},_++),r.flight&&V&&y.jsxs(en,{children:[y.jsx(yr,{children:"Thrust per turn: "}),r.freethrust]},_++),r.flight&&V&&y.jsx(en,{children:" "},_++),Object.keys(v).length>0&&y.jsxs(en,{children:[y.jsx(yr,{children:"NOTES:"})," "]},_++),Object.keys(v).length>0&&Object.keys(v).map(J=>y.jsx(en,{children:v[J]},_++)),Object.keys(v).length>0&&y.jsx(en,{children:" "},_++),Object.keys(T).length>0&&y.jsxs(en,{children:[y.jsx(yr,{children:"HIT CHART:"})," "]},_++),Object.keys(T).length>0&&Object.keys(T).map(J=>y.jsxs(en,{children:[y.jsxs(yr,{children:[J,": "]}),T[J]]},_++)),Object.keys(T).length>0&&y.jsx(en,{children:" "},_++),Object.keys(O).length>0&&y.jsxs(en,{children:[y.jsx(yr,{children:"UNITS ATTACHED:"})," "]},_++),Object.keys(O).length>0&&Object.keys(O).map(J=>y.jsxs(en,{children:[y.jsxs(yr,{children:[J,": "]}),O[J]]},_++)),Object.keys(O).length>0&&y.jsx(en,{children:" "},_++),S&&r.enhancementTooltip!=""&&V&&y.jsxs(en,{children:[y.jsx(yr,{children:"ENHANCEMENTS:"})," "]},_++),S&&r.enhancementTooltip!=""&&V&&Object.keys($).map(J=>y.jsx(en,{children:$[J]},_++)),S&&r.enhancementTooltip!=""&&V&&y.jsx(en,{children:" "},_++)]})}}const ld=D(bx)`
    /*font-size: 12px;*/
	font-size: 13px;
`,FC=D(xx)`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.position).reduce((r,s)=>r+`
`+s+":"+o.position[s]+"px;","")}
    width: ${o=>o.ship?"320px":"220px"};
    text-align: left;
    opacity:0.8;
`,IC=D.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,lj={MissileLost:"A missile was lost to damage"},UC={DamageReductionReduced:o=>`Damage reduction reduced by ${o}`},en=D(ku)`
    text-align: left;
    /*color: #5e85bc;*/
	color: #BDEAFA; /*replace dark blue above with bluish white, more eyes friendly*/
    font-family: arial;
    /*font-size: 11px;*/
	font-size: 12px;
`,yr=D.span`
    color: white;
	font-style:italic;
	font-size: 11px;
`,sj=D.span`
    color: #C6E2FF;
`;class uj extends Ze.Component{render(){const{ship:r,selectedShip:s,system:d,boundingBox:g}=this.props;if(d instanceof Ship||d===r){var b=r.shipClass,S=r.name;if(d.flight&&(b=d.systems[1].displayName),r.mine){var v=shipManager.systems.getSystemByName(r,"mineStealth");v&&!v.isMineRevealed(r)&&(b="Mine",S="Mine")}return y.jsxs(FC,{ship:!0,position:HC(g),children:[y.jsxs(ld,{children:[y.jsx(sj,{children:S})," - ",b]}),y.jsx(PC,{ship:r})]})}var T=new Array;d.data.Special&&d.data.Special!=""&&(T=d.data.Special.split("<br>"));var $="Special",O=0;let P=r.offensivebonus;r.flight&&gamedata.areMinesPresent&&(r.minesweeper?P-=window.ew.getDetectMEW(r):P-=window.ew.getDetectMEW(r)*2);var _=d.displayName,V=d.firingModes?d.firingModes[d.firingMode]:null,N=null;d.name==="ShadowFighterBomb"&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool=="function"&&(N=weaponManager.shadowFighterBombPool(r,d,!0));let J=!1;if(r.mine){var v=shipManager.systems.getSystemByName(r,"mineStealth");v&&!v.isMineRevealed(r)&&(J=!0,_="Mine",T=["No details known, scan with OEW to identify."])}return y.jsxs(FC,{position:HC(g),children:[y.jsx(ld,{children:_}),!r.flight&&!J&&$u("Structure",d.maxhealth-damageManager.getDamage(r,d)+"/"+d.maxhealth),!r.flight&&!J&&$u("Armor",shipManager.systems.getArmour(r,d)),r.flight&&!J&&$u("Offensive bonus",fj(d,P*5)),d.firingModes&&!J&&$u("Firing mode",V),d.missileArray&&Object.keys(d.missileArray).length>0&&!J&&$u("Ammo Amount",d.missileArray[d.firingMode].amount),!J&&Object.keys(d.data).map((xe,Pe)=>xe!=$&&!(xe==="Ammunition"&&(d.name==="GrapplingClaw"||d.name==="Marines"))&&$u(xe,pj(d,xe),"data"+Pe)),N!==null&&$u("Fighters available",N),Object.keys(T).length>0&&y.jsxs(en,{children:[y.jsx(yr,{children:"Special: "})," "]},`special-${O++}`),Object.keys(T).length>0&&Object.keys(T).map(xe=>y.jsx(en,{children:T[xe]},`special-${O++}`)),(Object.keys(d.critData).length>0||d.criticals&&d.criticals.length>0)&&!J&&dj(d),!gamedata.isMyShip(r)&&!J&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&s&&BC(r,s,d),gamedata.isMyShip(r)&&!J&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&s&&BC(r,s,d),gamedata.isMyShip(r)&&!J&&d.weapon&&weaponManager.hasFiringOrder(r,d)&&cj(r,d)]})}}const BC=(o,r,s)=>weaponManager.canCalledshot(o,s,r)?[y.jsx(ld,{children:"Called shot"},"calledHeader")].concat(gamedata.selectedSystems.map((d,g)=>{if(weaponManager.isOnWeaponArc(r,o,d))if(weaponManager.checkIsInRange(r,o,d)){var b=d.firingMode;return b=d.firingModes[b],s.id!=null&&!weaponManager.canWeaponCall(d)?y.jsxs(en,{children:[y.jsx(yr,{children:d.displayName}),": Cannot Called Shot"]},`called-${g}`):y.jsxs(en,{children:[y.jsx(yr,{children:d.displayName})," - Approx:  ",weaponManager.calculateHitChange(r,o,d,s.id).hitChance,"%"]},`called-${g}`)}else return y.jsxs(en,{children:[y.jsx(yr,{children:d.displayName}),": Not in Range"]},`called-${g}`);else return y.jsxs(en,{children:[y.jsx(yr,{children:d.displayName}),": Not in Arc"]},`called-${g}`)})):[y.jsx(ld,{children:"Called shot"},"calledHeader")].concat(y.jsx(en,{children:"Cannot Target"},"cannotTarget")),cj=(o,r)=>{var s=weaponManager.getCalledShotInfo(o,r);return s?[y.jsx(IC,{},"calledShotDivider"),y.jsx(ld,{children:"Called Shot"},"calledShotHeader"),y.jsxs(en,{children:[y.jsx(yr,{children:"Target: "}),s.targetSystem.displayName," (Id: ",s.targetSystem.id,") on ",s.targetShip.name]},"calledShotTarget")]:null},dj=o=>{const r=Object.keys(o.critData).length>0?Object.keys(o.critData):[...new Set((o.criticals||[]).map(s=>s.phpclass))];return r.length===0?null:[y.jsx(IC,{},"critDivider"),y.jsx(ld,{children:"Criticals"},"criticalHeader")].concat(r.map(s=>{let d=0,g=0;var b=0,S=0,v=!1,T="";b=0,S=0,v=!1,T="";for(const O in o.criticals)o.criticals[O].phpclass==s&&o.criticals[O].turn<=gamedata.turn&&(o.criticals[O].turnend==0||o.criticals[O].turnend>=gamedata.turn)&&(d++,g+=parseInt(o.criticals[O].param,10)||0,d==1&&(b=o.criticals[O].turnend,S=o.criticals[O].turnend,v=o.criticals[O].turnend==0),o.criticals[O].turnend>0?(o.criticals[O].turnend>S&&(S=o.criticals[O].turnend),(o.criticals[O].turnend<b||b==0)&&(b=o.criticals[O].turnend)):v=!0);if(b>0&&(T=" (until end of turn "+b,v?T=T+"+":S>b&&(T=T+"-"+S),T=T+")"),d>=1&&UC[s]){const O=UC[s](g);return y.jsxs(en,{children:[O," ",T]},`critical-${s}`)}const $=o.critData[s]||lj[s]||s;return d>1?y.jsxs(en,{children:["(",d," x) ",$," ",T]},`critical-${s}`):d==1?y.jsxs(en,{children:[$," ",T]},`critical-${s}`):null}))},fj=(o,r)=>typeof o.adjustOffensiveBonusDisplay=="function"?o.adjustOffensiveBonusDisplay(r):r,pj=(o,r)=>typeof o.adjustDataValueDisplay=="function"?o.adjustDataValueDisplay(r,o.data[r]):o.data[r],$u=(o,r,s)=>{if(typeof r=="string"&&r.indexOf("<br>")!==-1){const d=r.split("<br>");return y.jsxs(en,{children:[y.jsxs(yr,{children:[o,": "]}),d.map((g,b)=>y.jsxs(Ze.Fragment,{children:[b>0&&y.jsx("br",{}),g]},b))]},s)}return y.jsxs(en,{children:[y.jsxs(yr,{children:[o,": "]}),r]},s)},HC=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left+o.width,r};D(bx)`
    font-size: 12px;
`;const hj=D(xx)`
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
`;D(ku)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,D.span`
    color: white;
`;class gj extends Ze.Component{render(){const{ship:r,system:s,boundingBox:d}=this.props;return Gx(r,s)?y.jsx(hj,{position:mj(d),opacity:UA(r,s)?.95:.8,children:y.jsx(LA,{...this.props})}):null}}const mj=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r},vj={0:"Primary",1:"Forward",2:"Aft",3:"Port",4:"Starboard",5:"",31:"Port Fwd",32:"Port Aft",41:"Stbd Fwd",42:"Stbd Aft"},yj=D.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${H.colors.panelBgGlass};
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

    border: ${o=>{switch(o.$location){case 0:return`1px solid ${H.colors.line}`;default:return`1px dotted ${H.colors.line}`}}};
`,xj=D.div`
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
    border-bottom: 1px solid ${H.colors.healthOk};
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
        background-color: ${o=>o.$criticals?H.colors.healthCrit:H.colors.healthOk};
    }
`,bj=D.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${H.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
`,wj=D.span`
    position: relative;
    z-index: 1;
    font-family: ${H.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${o=>o.$destroyed?"transparent":H.colors.text};
    filter: ${o=>o.$destroyed?"blur(1px)":"none"};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,Sj=D.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;class VC extends Ze.Component{constructor(r){super(r),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(r){const{ship:s,systems:d}=this.props,g=e0(d);if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Yx(s)){r.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("MineDamageClicked",{ship:s,element:r.currentTarget});return}!g||!Mu(s,g)||(r.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:s,system:g,element:r.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.hideStructureArc()}showStructureArc(){const{ship:r,systems:s}=this.props;this.arcShown=!0,window.uiEvents.relay("StructureMouseOver",{ship:r,structure:e0(s)})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay("StructureMouseOut"))}onStructureMouseOver(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),this.showStructureArc())}onStructureMouseOut(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(r){r.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.touches[0];this.touchStartX=s.clientX,this.touchStartY=s.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(r){if(r.stopPropagation(),!this.longPressTimer)return;const s=r.touches[0];(Math.abs(s.clientX-this.touchStartX)>10||Math.abs(s.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(r){r.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(r){r.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,this.hideStructureArc()}render(){const{ship:r,systems:s,location:d,displayLocation:g,area:b,valign:S,justify:v,wide:T,isTerrain:$,minHeight:O,nameOverride:P,hidden:_}=this.props,V=e0(s),N=Yx(r),J=N?battleDamage.mineHealth(r,1):0,xe=N?battleDamage.mineMaxHealth(r):0,Pe=N?J/xe*100:V?Cj(r,V):0,de=g!==void 0?g:d,ue=g!==void 0&&g!==d;return y.jsxs(yj,{$location:d,$area:b,$valign:S,$justify:v,$wide:T,$isTerrain:$,$minHeight:O,$hidden:_,children:[V&&y.jsxs(xj,{$health:Pe,$criticals:Ej(V),$damageable:Mu(r,V)||Yx(r),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[y.jsx(bj,{children:P||vj[d]||""}),y.jsxs(wj,{$destroyed:Pe===0,children:[N?J:V.maxhealth-damageManager.getDamage(r,V),"/",N?xe:V.maxhealth," A",shipManager.systems.getArmour(r,V)]})]}),y.jsx(Sj,{children:Tj(s,de,T).map(he=>y.jsx(Zx,{scs:!0,mirror:ue,system:he,ship:r},`system-scs-${d}-${r.id}-${he.id}`))})]})}}const Cj=(o,r)=>(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,Ej=o=>shipManager.criticals.hasCriticals(o),WC=o=>o.name==="structure",e0=o=>o.find(WC),Zg=o=>o.filter(r=>!WC(r)),Tj=(o,r,s)=>(o=Zg(o),s?t0(o):[4,41,42].includes(r)?n0(o):[3,31,32].includes(r)?kj(n0(o)):[1,2,0].includes(r)?t0(o):Rj(o)),kj=o=>{let r=[];return o.forEach((s,d)=>{const g=d%3;g===0?r[d+2]=s:g===1?r[d]=s:r[d-2]=s}),r},Rj=o=>(o=Zg(o),o.length===3?n0(o):o.length===4?t0(o):o),t0=o=>{o=Zg(o);let r=[];for(;;){const{picked:s,remaining:d}=i0(o,4);if(s.length===0)break;o=d,r=r.concat(s)}for(;;){const{picked:s,remaining:d}=i0(o,2);if(s.length===0)break;o=d;const g=i0(o,2);g.picked.length>0?(o=g.remaining,r=r.concat([s[0],g.picked[0],g.picked[1],s[1]])):(r=r.concat([s[0],o.shift(),o.shift(),s[1]]),r=r.filter(b=>b))}return r=r.concat(o),r},n0=o=>{o=Zg(o);let r=[];for(;;){const{picked:s,remaining:d}=r0(o,3);if(s.length===0)break;o=d,r=r.concat(s)}for(;;){const{picked:s,remaining:d}=r0(o,2);if(s.length===0)break;const{three:g,remainingSystems:b}=Dj(s,d);o=b,r=r.concat(g)}return r=r.concat(o),r},Dj=(o,r)=>{const s=r0(r,1);return s.picked.length===1?{three:[s.picked[0],o[0],o[1]],remainingSystems:s.remaining}:r.length>0?{three:[r.shift(),o[0],o[1]],remainingSystems:r}:{three:[o[0],o[1]],remainingSystems:r}},r0=(o,r=3)=>{const s=o.find(b=>{const S=o.reduce((v,T)=>T.name===b.name?v+1:v,0);return r===1?S===r:S>=r});if(!s)return{picked:[],remaining:o};let d=[];const g=o.filter(b=>b.name===s.name&&r>0?(r--,d.push(b),!1):!0);return{picked:d,remaining:g}},i0=(o,r=3)=>{const s=o.find($=>{const O=o.reduce((P,_)=>_.name===$.name?P+1:P,0);return r===1?O===r:O>=r});if(!s)return{picked:[],remaining:o};let d=[],g=[];const b=o.filter($=>$.name===s.name?(g.push($),!1):!0);for(var S=Math.ceil(r/2),v=Math.floor(r/2),T=0;T<g.length;T++)T<S||T>=g.length-v?d.push(g[T]):b.unshift(g[T]);return{picked:d,remaining:b}},YC={DEW:"#aecdea",CCEW:H.colors.text,SDEW:"#9ac1e5",OEW:"#acd7a8",BDEW:"#8ac785","Detect Mines":"#bfa3db","Detect Stealth":"#ccb6e2",DIST:"#e6b98f",SOEW:H.colors.text,OEW_HOSTILE:"#e49b9b"},sd=(o,r)=>o==="OEW"&&r&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(r)?YC.OEW_HOSTILE:YC[o]||H.colors.textAccent,Mj=D.div`
    grid-area: ew;
    justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${H.colors.panelBgGlass};
    border: 1px solid ${H.colors.line};
    padding: 1px 4px 1px;
`,Oj=D.div`
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
    color: ${H.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${H.colors.line};
`,ud=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${H.colors.text};
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
    ${o=>o.$target&&cp`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${o=>o.$hoverable&&cp`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,$j=D.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,cd=D.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${o=>o.$color||H.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,dd=D.span`
    font-family: ${H.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,Aj=D.span`
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
    color: ${H.colors.textAccent};
    ${o=>o.$interactive&&cp`
        cursor: pointer;
        &:hover {
            color: ${H.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;class jj extends Ze.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(r,s){window.webglScene&&(window.uiEvents.relay("EwRangeHover",{shipId:this.props.ship.id,type:r,active:s}),this.activeRangeOverlay=s?r:null)}onTargetClick(r,s){s.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(r)||window.uiEvents.relay("ScrollToShip",{shipId:r.id}))}setTargetHighlight(r,s,d){window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:r.id,type:s,active:d}),this.activeHighlight=d?{targetId:r.id,type:s}:null)}render(){const{ship:r}=this.props;return y.jsxs(Mj,{children:[y.jsx(Oj,{children:"Electronic Warfare"}),_j(r,this),Lj(r,this)]})}}const _j=(o,r)=>{let s=[];const d=!!window.webglScene,g=$=>d?{$hoverable:!0,onMouseEnter:()=>r.setRangeOverlay($,!0),onMouseLeave:()=>r.setRangeOverlay($,!1)}:{};s.push(y.jsxs(ud,{children:[y.jsx(cd,{$color:sd("DEW"),children:"DEW"}),y.jsx(dd,{children:to(ew.getDefensiveEW(o))})]},`dew-scs-${o.id}`));var b=Math.max(0,ew.getCCEW(o)-ew.getDistruptionEW(o));b>0&&s.push(y.jsxs(ud,{children:[y.jsx(cd,{$color:sd("CCEW"),children:"CCEW"}),y.jsx(dd,{children:to(b)})]},`ccew-scs-${o.id}`));let S=ew.getBDEW(o)*.25,v=ew.getDetectSEW(o),T=ew.getDetectMEW(o);return shipManager.hasSpecialAbility(o,"ConstrainedEW")&&(S=ew.getBDEW(o)*.2),S&&s.push(y.jsxs(ud,{...g("BDEW"),children:[y.jsx(cd,{$color:sd("BDEW"),children:"BDEW"}),y.jsx(dd,{children:to(S)})]},`bdew-scs-${o.id}`)),T&&s.push(y.jsxs(ud,{...g("MDEW"),children:[y.jsx(cd,{$color:sd("Detect Mines"),children:"Detect Mines"}),y.jsx(dd,{children:to(T)})]},`DetectMEW-scs-${o.id}`)),v&&s.push(y.jsxs(ud,{children:[y.jsx(cd,{$color:sd("Detect Stealth"),children:"Detect Stealth"}),y.jsx(dd,{children:to(v)})]},`DetectSEW-scs-${o.id}`)),s},Lj=(o,r)=>{const s=!!window.webglScene;return o.EW.filter(d=>d.turn===gamedata.turn).filter(d=>d.type==="OEW"||d.type==="DIST"||d.type==="SOEW"||d.type==="SDEW").map(d=>{const g=gamedata.getShip(d.targetid);return y.jsxs(ud,{$target:!0,children:[y.jsxs($j,{children:[y.jsx(cd,{$color:sd(d.type,o),children:d.type}),y.jsx(Aj,{$interactive:s,title:void 0,onClick:s?r.onTargetClick.bind(r,g):void 0,onMouseEnter:s?()=>r.setTargetHighlight(g,d.type,!0):void 0,onMouseLeave:s?()=>r.setTargetHighlight(g,d.type,!1):void 0,children:g.name})]}),y.jsx(dd,{children:zj(d,o)})]},`${d.type}-scs-${o.id}-${d.targetid}`)})},zj=(o,r)=>{switch(o.type){case"SDEW":if(shipManager.hasSpecialAbility(r,"ConstrainedEW")){let s=o.amount*.333;return s=Math.round(s*3)/3,to(s)}else return to(o.amount*.5);case"DIST":return shipManager.hasSpecialAbility(r,"ConstrainedEW")?to(o.amount/4):to(o.amount/3);case"OEW":return to(Math.max(0,o.amount-ew.getDistruptionEW(r)));default:return to(o.amount)}},to=o=>Math.round(o*100)/100,Jg=()=>!!window.gamedata&&window.gamedata.gamephase===-2,Nj=D.div`
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
`,Pj=D.div`
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
`,mp=D.div`
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
`,GC=D.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,Fj=D(GC)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,Ij=D.div`
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
`,Uj=D.div`
    z-index: 1;
`;class Bj extends Ze.Component{onSystemMouseOver(r){if(Jg()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||r.nativeEvent&&r.nativeEvent.sourceCapabilities&&r.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:s}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:s,system:s,element:r.target})}onSystemMouseOut(){Jg()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onFighterTouchStart(r){if(Jg())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:g}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:g,system:g,element:s,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(r){if(!this.longPressTimer)return;const s=r.touches[0],d=s.clientX-this.touchStartX,g=s.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(g)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(r){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onFighterTouchEnd(r){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){const{ship:r}=this.props,s=window.gamedata&&typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted();return Jg()&&!s&&!!r&&r.userid!=0&&!!r.flight}onHealthBarClick(r){this.canApplyPreBattleDamage()&&(r.stopPropagation(),r.preventDefault(),window.uiEvents.relay("FighterDamageClicked",{ship:this.props.ship,fighter:this.props.fighter,element:r.currentTarget}))}render(){const{ship:r,fighter:s}=this.props,d=shipManager.systems.isDestroyed(r,s),g=shipManager.criticals.isDockedFighter(s),b=!g&&shipManager.criticals.isSplitLaunchedFighter(s),S=!g&&!b&&shipManager.criticals.isDisengagedFighter(s),v=shipManager.criticals.isCutOffFighter(s);let T=null;d?g?T=y.jsx(mp,{$color:"#00b8e6",children:"DOCKED"}):b?T=y.jsx(mp,{$color:"#00b8e6",children:"SPLIT"}):S?T=y.jsx(mp,{$color:"#ff8c00",children:"DROPOUT"}):T=y.jsx(mp,{$color:"#ff5252",children:"DESTROYED"}):v&&(T=y.jsx(mp,{$color:"#ff5252",children:"CUT OFF"}));const $=this.canApplyPreBattleDamage(),O=$?battleDamage.fighterHealth(r,1)/s.maxhealth*100:Hj(r,s),P=$?`${battleDamage.fighterHealth(r,1)} / ${s.maxhealth}`:`${s.maxhealth-damageManager.getDamage(r,s)} / ${s.maxhealth}`;return y.jsxs(Nj,{$docked:g,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[y.jsxs(Pj,{$destroyed:d,$img:window.AssetManager.getSmartImagePath(s.iconPath),children:[y.jsx(GC,{children:KC(r,s,Yj(s),d)}),y.jsx(Fj,{children:KC(r,s,Gj(s),d)}),y.jsx(Ij,{$health:O,$criticals:Vj(s),$criticalsBenign:Wj(s),$docked:g,$clickable:$,title:$?"Apply pre-battle damage to this flight":void 0,onClick:this.onHealthBarClick.bind(this),children:y.jsx(Uj,{children:P})})]}),T]})}}const Hj=(o,r)=>(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,Vj=o=>shipManager.criticals.hasCriticals(o),Wj=o=>shipManager.criticals.hasOnlyCritical(o,"LaunchedThisTurn",!1),Yj=o=>o.systems.filter(r=>r.location==1),Gj=o=>o.systems.filter(r=>r.location!=1),KC=(o,r,s,d)=>s.map((g,b)=>y.jsx(Zx,{$destroyed:d,fighter:!0,scs:!0,system:g,ship:o},`system-scs-fighter${r.id}-${o.id}-${g.id}-${b}`)),Kj=D.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;class QC extends Ze.Component{render(){const{ship:r}=this.props;return y.jsx(Kj,{children:Qj(r)})}}const Qj=o=>o.systems.map((r,s)=>y.jsx(Bj,{fighter:r,ship:o},`flight-${o.id}-${s}`)),qj=[3,31,32],Xj=[1,0,2],Zj=[4,41,42],Jj=D.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,a0=D.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${o=>o.$side?"center":"flex-start"};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,e_=D.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${H.colors.line};
    padding: 3px 5px;
`,t_=D.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${H.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${H.colors.line};
`,n_=D.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${H.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`,r_=D.span`
    font-family: ${H.fonts.mono};
    color: ${H.colors.text};
    flex-shrink: 0;
`,i_=(o,r)=>y.jsxs(e_,{children:[y.jsx(t_,{children:r.name}),[...r.entries].sort((s,d)=>d.chance-s.chance).map((s,d)=>y.jsxs(n_,{children:[y.jsx("span",{children:s.name}),y.jsxs(r_,{children:[s.chance,"%"]})]},`hitchart-${o.id}-${r.location}-${d}`))]},`hitchart-${o.id}-${r.location}`);class a_ extends Ze.Component{render(){const{ship:r}=this.props,s=NC(r);if(s.length===0)return null;const d={};s.forEach(T=>{d[T.location]=T});const g=T=>T.filter($=>d[$]).map($=>i_(r,d[$])),b=g(qj),S=g(Xj),v=g(Zj);return y.jsxs(Jj,{children:[b.length>0&&y.jsx(a0,{$side:!0,children:b}),S.length>0&&y.jsx(a0,{children:S}),v.length>0&&y.jsx(a0,{$side:!0,children:v})]})}}const o_=o=>o.split(" ").map(r=>r.charAt(0).toUpperCase()+r.slice(1)).join(" "),l_=o=>{const r=[];if(!o||o.flight)return r;const s=o.fighters||{};if(Object.keys(s).length>0){const g={};if(shipManager.systems.shipHasRestrictedHangar(o)){const b=shipManager.systems.getReservedFighterComposition(o);for(let S=0;S<b.length;S++){const v=b[S].category;g[v]||(g[v]=[]);const T=g[v];let $=!1;for(let O=0;O<T.length;O++)if(T[O].phpclass===b[S].phpclass){T[O].count+=b[S].count,$=!0;break}$||T.push({category:b[S].category,phpclass:b[S].phpclass,displayName:b[S].displayName,count:b[S].count,isGroup:b[S].isGroup})}}for(const b in s){const S=s[b],v=o_(b),T=g[b];if(T&&(b==="heavy"||b==="medium"||b==="light")){let $=S;for(let O=0;O<T.length;O++){const P=Math.min(T[O].count,$);P<=0||(T[O].isGroup?r.push(P+" "+T[O].displayName+"s"):r.push(P+" "+T[O].displayName+" "+v+" Fighters"),$-=P)}$>0&&r.push($+" "+v+" Fighters");continue}if(b==="normal")r.push(S+" Fighters");else if(b==="superheavy"||b==="heavy"||b==="medium"||b==="light"||b==="ultralight")r.push(S+" "+v+" Fighters");else{if(b==="shuttles"||b==="minesweeping shuttles"||b==="cargo shuttles"||b==="lifeboats"||b==="medical shuttles"||b==="presidential shuttle"||b==="yacht")continue;r.push(S+" "+v)}}}const d=shipManager.systems.getDefaultShuttleComposition(o);for(let g=0;g<d.length;g++)r.push(d[g].count+" "+d[g].type);return r},s_=D.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${H.colors.textAccent};
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
`,vp=D.div`
    background-color: ${H.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${o=>o.$gold?H.colors.enhLine:H.colors.line};
    padding: 0 8px 3px;
`,o0=D.div`
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
    color: ${o=>o.$gold?H.colors.enhTitle:H.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${o=>o.$gold?H.colors.enhBg:"rgba(73, 103, 145, 0.25)"};
    border-bottom: 1px solid ${o=>o.$gold?H.colors.enhLine:H.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,yp=D.div`
    padding: 1px 0;
`,u_=D.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${H.colors.custom};
`,Da=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,Ma=D.span`
    font-size: 10px;
    color: ${H.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,Oa=D.span`
    font-family: ${H.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${o=>o.$changed?H.colors.custom:H.colors.text};
    margin-right: 5px;
`,c_=D.div`
    width: 150px;
    box-sizing: border-box;
    ${o=>o.$bare?`
    padding: 0;`:`
    background-color: ${H.colors.panelBgGlass};
    border: 1px dotted ${H.colors.line};
    padding: 2px 4px 3px;`}
`,d_=D.div`
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
    color: ${H.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${H.colors.line};
`,qC=D.span`
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
        background-color: ${H.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;D.div`
    text-align: center;
    font-size: 10px;
    color: ${H.colors.warning};
    padding-top: 2px;
`;const XC=D.div`
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
    color: ${H.colors.enhTitle};
    background-color: ${H.colors.enhBg};
    border-bottom: 1px solid ${H.colors.enhLine};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,f_=D.div`
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
    color: ${H.colors.enhText};
`,l0=o=>typeof o=="number"?o.toFixed(2):o,ZC=(o,r)=>o*5+"/"+r*5,em=(o,r)=>o+" ("+l0(r)+")",p_=o=>{const r=window.shipManager;if(!r)return null;const s={},d=r.systems?r.systems.getSystemByName(o,"CnC"):null,g=d&&r.criticals?r.criticals.hasCritical(d,"ProfileIncreased"):0;s.profile=ZC(o.forwardDefense+g,o.sideDefense+g),s.profileChanged=g!==0;const b=o.iniativeadded||0;s.initiative=(o.iniativebonus||0)+b,s.initiativeChanged=b!==0;const S=r.movement;if(S&&typeof S.getTurnCost=="function"&&o.movement&&o.movement.length>0){const v=S.getSpeed(o),T=S.getDockedLcvTurnSurcharge(o),$=S.getTurnDelayCost(o);let O=S.getTurnCost(o);o.submarine&&S.isGoingBackwards(o)&&(O=O*1.33);const P=Math.max(1,Math.ceil(v*O))+T,_=Math.ceil(v*$)+T;s.turnCost=em(P,O),s.turnDelay=em(_,$),s.turnCostChanged=s.turnCost!==em(Math.max(1,Math.ceil(v*o.turncost)),o.turncost),s.turnDelayChanged=s.turnDelay!==em(Math.ceil(v*o.turndelaycost),o.turndelaycost)}return s},JC=({ship:o,live:r,bare:s})=>{const d=!o.base,g=r?p_(o):null;return y.jsxs(c_,{$bare:s,children:[!s&&y.jsxs(d_,{children:[y.jsxs(qC,{children:[y.jsx("i",{}),y.jsx("i",{}),y.jsx("i",{})]}),"Ship Stats"]}),d&&y.jsxs(Da,{children:[y.jsx(Ma,{children:"Turn cost"}),y.jsx(Oa,{$changed:!!(g&&g.turnCostChanged),children:g&&g.turnCost?g.turnCost:l0(o.turncost)})]}),d&&y.jsxs(Da,{children:[y.jsx(Ma,{children:"Turn delay"}),y.jsx(Oa,{$changed:!!(g&&g.turnDelayChanged),children:g&&g.turnDelay?g.turnDelay:l0(o.turndelaycost)})]}),d&&y.jsxs(Da,{children:[y.jsx(Ma,{children:"Accel/decel"}),y.jsx(Oa,{children:o.accelcost})]}),d&&y.jsxs(Da,{children:[y.jsx(Ma,{children:"Pivot"}),y.jsx(Oa,{children:o.pivotcost})]}),d&&y.jsxs(Da,{children:[y.jsx(Ma,{children:"Roll"}),y.jsx(Oa,{children:o.rollcost})]}),y.jsxs(Da,{children:[y.jsx(Ma,{children:"Profile - Front / Side"}),y.jsx(Oa,{$changed:!!(g&&g.profileChanged),children:g?g.profile:ZC(o.forwardDefense,o.sideDefense)})]}),d&&y.jsxs(Da,{children:[y.jsx(Ma,{children:"Initiative"}),y.jsx(Oa,{$changed:!!(g&&g.initiativeChanged),children:g?g.initiative:o.iniativebonus})]})]})},h_=({ship:o})=>{const r=s0(o.enhancementTooltip);return r.length===0?null:y.jsx(f_,{children:y.jsxs(vp,{$gold:!0,children:[y.jsx(XC,{children:"Enhancements"}),r.map((s,d)=>y.jsx(yp,{children:s},`enh-${d}`))]})})},s0=o=>(o||"").split(/<br\s*\/?>/i).map(r=>r.replace(/<[^>]*>/g,"").replace(/&nbsp;/g," ").trim()).filter(Boolean);class u0 extends Ze.Component{render(){const{ship:r,full:s,grid:d,hideEnhancements:g}=this.props,b=l_(r),S=s0(r.notes),v=g?[]:s0(r.enhancementTooltip),T=[];if(r.limited&&r.limited!=0&&T.push("Limited: "+r.limited+"%"),r.variantOf){const P=r.occurence?r.occurence.charAt(0).toUpperCase()+r.occurence.slice(1)+" ":"";T.push(P+"variant of "+r.variantOf)}r.isd&&T.push("In-Service (ISD): "+r.isd);let $=null;r.unofficial==="S"?$="Semi-Custom":r.unofficial&&($="Custom");const O=S.length>0||T.length>0||$;return y.jsxs(s_,{$full:s,$grid:d,children:[r.flight&&y.jsxs(vp,{children:[y.jsx(o0,{children:"Flight Stats"}),y.jsxs(Da,{children:[y.jsx(Ma,{children:"Armor F/S/A"}),y.jsx(Oa,{children:shipManager.systems.getFlightArmour(r)})]}),y.jsxs(Da,{children:[y.jsx(Ma,{children:"Off. bonus"}),y.jsx(Oa,{children:r.offensivebonus*5})]}),y.jsxs(Da,{children:[y.jsx(Ma,{children:"Profile - Front / Side"}),y.jsxs(Oa,{children:[r.forwardDefense*5,"/",r.sideDefense*5]})]}),y.jsxs(Da,{children:[y.jsx(Ma,{children:"Thrust"}),y.jsx(Oa,{children:r.freethrust})]}),y.jsxs(Da,{children:[y.jsx(Ma,{children:"Initiative"}),y.jsx(Oa,{children:r.iniativebonus})]})]}),b.length>0&&y.jsxs(vp,{children:[y.jsx(o0,{children:"Hangar Capacity"}),b.map((P,_)=>y.jsx(yp,{children:P},`comp-${_}`))]}),O&&y.jsxs(vp,{children:[y.jsx(o0,{children:"Notes"}),S.map((P,_)=>y.jsx(yp,{children:P},`note-${_}`)),T.map((P,_)=>y.jsx(yp,{children:P},`meta-${_}`)),$&&y.jsx(u_,{children:$})]}),v.length>0&&y.jsxs(vp,{$gold:!0,children:[y.jsx(XC,{children:"Enhancements"}),v.map((P,_)=>y.jsx(yp,{children:P},`enh-${_}`))]})]})}}const xp=D.div`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${o=>o.$isMyTeam?`left: 50px; 
 top: 50px;`:`right: 50px; 
 top: 50px;`}
    width: ${o=>o.$variant==="terrain"?"250px":o.$variant==="flight"?"auto":"fit-content"};
    max-width: ${o=>o.$variant==="flight"?"400px":o.$variant==="flightLobby"?"620px":"unset"};
    height: auto;
    border: 1px solid ${H.colors.line};
    background-color: ${H.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${H.colors.text};
    font-family: ${H.fonts.body};

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
        max-width: ${o=>o.$variant==="flight"?"400px":o.$variant==="flightLobby"?"620px":"none"};
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
`,g_=D.div`
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
    background-color: ${H.colors.panelBg};
    border-bottom: 1px solid ${H.colors.line};
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
`,m_=D.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${o=>o.$tint||H.colors.text};
`,v_=D.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${H.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,y_=D.div`
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
    color: ${H.colors.line};
    ${Vi}
`,x_=D.div`
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
        background: repeating-linear-gradient(${o=>o.$mirror?"45deg":"315deg"}, ${H.colors.line} 0 1.5px, transparent 1.5px 4px);
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
`,b_=D.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${o=>o.$compact?"width: 100%; align-items: center; margin-bottom: 5px;":"align-items: stretch;"}
    gap: 4px;
`,tm=D.div`
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
    border: 1px solid ${H.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${o=>o.$active?"rgba(198, 226, 255, 0.12)":"rgba(73, 103, 145, 0.25)"};
    color: ${H.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${Vi}
`,eE=D.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,w_=D.span`
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
`,c0=D.div`
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
    background-color: ${H.colors.panelBg};
    border: 1px solid ${H.colors.line};
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
`,S_=D.div`
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
`,C_=D.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,E_=D.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,d0=D.div`
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
`,f0=D.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,T_=D.div`
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
    color: ${o=>o.$color||H.colors.warning};
    background-color: ${o=>o.$bg||"rgba(225, 176, 0, 0.10)"};
    border-top: 1px solid ${H.colors.line};
    flex-shrink: 0;
`,tE=D.div`
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
`,k_=D.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${H.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${H.fonts.body};
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
`,R_={1:"fwd",2:"aft",0:"prim",3:"left",4:"right",31:"lfwd",41:"rfwd",32:"laft",42:"raft"},nE={3:4,4:3,31:41,41:31,32:42,42:32},D_={fwd:"end",aft:"center",prim:"center",left:"center",right:"center",lfwd:"start",rfwd:"start",laft:"end",raft:"end"},M_={left:"end",lfwd:"end",laft:"end",right:"start",rfwd:"start",raft:"start"},O_=[1,3,31,32,0,4,41,42,2],$_=[1,3,31,0,4,41,32,2,42],A_=[31,32,41,42],j_=[3,4,31,41,32,42];class __ extends Ze.Component{constructor(r){super(r),this.elementRef=Ze.createRef(),this.controlsRef=Ze.createRef(),this.popupRef=Ze.createRef(),this.hitChartBtnRef=Ze.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return im(this.props.ship)?"left":"right"}isMirroredGrip(){return this.side()==="right"}applyScreenFit(){const r=this.elementRef.current;if(!r)return;const s=iE(),d=rm(this.side());if(!s&&d===1){(this.screenFit!==1||this.screenFitHeight)&&(r.style.transform="",r.style.transformOrigin="",r.style.maxHeight="",this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}const g=this.measureNatural();if(!g)return;const b=s?U_():null,S=window.innerHeight||document.documentElement.clientHeight;let v=1;if(s){const O=(document.documentElement.clientWidth||window.innerWidth)*b.fillW,P=S*b.fillH;v=Math.min(O/g.width,P/g.height),v=Math.min(b.max,Math.max(b.min,v))}this.autoFit=v;let T=g0(v*d);T=Math.round(T*100)/100;let $=null;if(s){const O=Math.min(p0,b.fillH*Math.max(1,d));$=Math.round(S*O/T)}T===this.screenFit&&$===this.screenFitHeight||(this.screenFit=T,this.screenFitHeight=$,r.style.transformOrigin=this.transformOrigin(),r.style.transform=T===1?"":"scale("+T+")",r.style.maxHeight=$==null?"":$+"px",this.resizeStart||this.keepGripOnScreen())}measureNatural(){const r=this.elementRef.current;if(!r)return null;const s=r.style.maxHeight;r.style.maxHeight="none";const d=r.offsetWidth,g=r.offsetHeight;return r.style.maxHeight=s,d&&g?{width:d,height:g}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:im(this.props.ship)?"top left":"top right"}onScreenResize(){this.applyScreenFit()}isDragHandle(r){return!r||!r.closest||r.closest(".shipwindow-nodrag")?!1:!!r.closest(".shipwindow-drag-handle")}isResizeHandle(r){return!!(r&&r.closest&&r.closest(".shipwindow-resize-grip"))}isDragSlop(r,s){if(!r||!r.closest||!r.closest(".shipwindow-grab-slop"))return!1;const d=this.elementRef.current,g=d&&d.querySelector(".shipwindow-drag-handle");if(!g)return!1;const b=g.getBoundingClientRect();return s>=b.top&&s<=b.bottom+W_}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(r,s,d){const g=Date.now(),b=!!this.lastPress&&this.lastPress.kind===r&&g-this.lastPress.time<Y_;this.lastPress={kind:r,time:g},this.pressPoint={x:s,y:d},this.pendingReset=b}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(r,s){const d=this.elementRef.current;if(!d)return!1;const g=window.getComputedStyle(d);let b=parseFloat(g.left),S=parseFloat(g.top);return isFinite(b)||(b=d.offsetLeft),isFinite(S)||(S=d.offsetTop),this.dragStart={x:r,y:s,left:b,top:S},this.positioned=!0,d.style.left=b+"px",d.style.top=S+"px",d.style.right="auto",!0}moveDrag(r,s){const d=this.elementRef.current;!this.dragStart||!d||(d.style.left=this.dragStart.left+(r-this.dragStart.x)+"px",d.style.top=this.dragStart.top+(s-this.dragStart.y)+"px",this.clampIntoView())}beginResize(r,s){const d=this.elementRef.current;if(!d)return!1;const g=this.measureNatural();if(!g)return!1;const b=window.getComputedStyle(d);let S=parseFloat(b.left),v=parseFloat(b.top);isFinite(S)||(S=d.offsetLeft),isFinite(v)||(v=d.offsetTop);const T=this.screenFit||1,$=this.isMirroredGrip(),O=$?"top right":"top left";!$&&this.transformOrigin()==="top right"&&(S+=g.width*(1-T)),d.style.left=S+"px",d.style.top=v+"px",d.style.right="auto",d.style.transformOrigin=O,this.resizeOrigin=O,this.positioned=!0;const P=d.getBoundingClientRect(),_=$?P.right:P.left,V=$?-1:1;return this.resizeStart={originX:_,originY:P.top,flipX:V,width:g.width,height:g.height,scale:T,maxScale:$?_/g.width:1/0,base:oE(g.width,g.height,V*(r-_),s-P.top)},!0}moveResize(r,s){const d=this.resizeStart;if(!d)return;const g=oE(d.width,d.height,d.flipX*(r-d.originX),s-d.originY),b=g0(Math.min(d.maxScale,d.scale+(g-d.base))),S=this.side(),v=Math.round(b/(this.autoFit||1)*100)/100;v!==rm(S)&&(lE(S,v),this.applyScreenFit())}moveGesture(r,s){this.pressPoint&&(Math.abs(r-this.pressPoint.x)>aE||Math.abs(s-this.pressPoint.y)>aE)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(r,s):this.moveDrag(r,s)}finishGesture(){const r=this.elementRef.current,s=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),s){const d=this.side();sE(d,rm(d)),this.keepGripOnScreen(),this.clampIntoView()}r&&(rE[this.side()]={top:parseFloat(r.style.top)||0,left:parseFloat(r.style.left)||0})}clampIntoView(r){const s=this.elementRef.current;if(!s||!this.positioned)return;const d=s.getBoundingClientRect(),g=document.documentElement.clientWidth||window.innerWidth,b=window.innerHeight||document.documentElement.clientHeight;let S=0,v=0;d.top<0?v=-d.top:d.top>b-fd&&(v=b-fd-d.top),r&&d.width<=g&&d.left<0?S=-d.left:d.right<fd?S=fd-d.right:d.left>g-fd&&(S=g-fd-d.left),!(!S&&!v)&&(s.style.left=(parseFloat(s.style.left)||0)+S+"px",s.style.top=(parseFloat(s.style.top)||0)+v+"px")}keepGripOnScreen(){const r=this.elementRef.current;if(!r)return;const s=r.getBoundingClientRect(),d=document.documentElement.clientWidth||window.innerWidth;let g;if(this.isMirroredGrip()){if(s.left>=0||(g=Math.min(-s.left,d-s.right),g<=0))return}else if(g=d-s.right,g>=0)return;const b=window.getComputedStyle(r);let S=parseFloat(b.left);isFinite(S)||(S=r.offsetLeft),r.style.left=S+g+"px",r.style.right="auto",this.positioned=!0}resetUserScale(){const r=this.side();rm(r)!==1&&(lE(r,1),sE(r,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(r){r.stopPropagation(),this.resetUserScale()}onDragStart(r){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] pointerdown",r.pointerType,"handle:",this.isDragHandle(r.target),"grip:",this.isResizeHandle(r.target)),r.pointerType==="touch"||r.button!=null&&r.button>0)return;const s=this.isResizeHandle(r.target);if(!s&&!this.isDragHandle(r.target))return;if(this.notePress(s?"grip":"header",r.clientX,r.clientY),!(s?this.beginResize(r.clientX,r.clientY):this.beginDrag(r.clientX,r.clientY))){this.cancelDoublePress();return}const d=this.elementRef.current;let g=!1;try{d.setPointerCapture(r.pointerId),g=!0}catch{}this.dragTarget=g?d:document,this.dragTarget.addEventListener("pointermove",this.onDragMove),this.dragTarget.addEventListener("pointerup",this.onDragEnd),this.dragTarget.addEventListener("pointercancel",this.onDragEnd),this.dragPointerId=r.pointerId,r.preventDefault()}onDragMove(r){!this.gestureActive()||r.pointerId!==this.dragPointerId||this.moveGesture(r.clientX,r.clientY)}onDragEnd(r){!this.gestureActive()||r&&r.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(r){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] touchstart",r.touches&&r.touches.length,"handle:",this.isDragHandle(r.target),"grip:",this.isResizeHandle(r.target)),this.gestureActive()||!r.touches||r.touches.length!==1)return;const s=r.touches[0],d=this.isResizeHandle(r.target);if(!(!d&&!this.isDragHandle(r.target)&&!this.isDragSlop(r.target,s.clientY))){if(this.notePress(d?"grip":"header",s.clientX,s.clientY),!(d?this.beginResize(s.clientX,s.clientY):this.beginDrag(s.clientX,s.clientY))){this.cancelDoublePress();return}this.touchDragId=s.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.addEventListener("touchend",this.onTouchDragEnd),document.addEventListener("touchcancel",this.onTouchDragEnd),r.cancelable&&r.preventDefault()}}onTouchDragMove(r){const s=uE(r.touches,this.touchDragId);if(!this.gestureActive()||!s)return;this.moveGesture(s.clientX,s.clientY);const d=this.dragScroll;d&&((window.scrollX||window.pageXOffset||0)!==d.x||(window.scrollY||window.pageYOffset||0)!==d.y)&&window.scrollTo(d.x,d.y),r.cancelable&&r.preventDefault()}onTouchDragEnd(r){r&&r.touches&&uE(r.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.removeEventListener("touchend",this.onTouchDragEnd),document.removeEventListener("touchcancel",this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){const r=this.dragTarget;r&&(r.removeEventListener("pointermove",this.onDragMove),r.removeEventListener("pointerup",this.onDragEnd),r.removeEventListener("pointercancel",this.onDragEnd),this.dragPointerId!=null&&r.hasPointerCapture&&r.hasPointerCapture(this.dragPointerId)&&r.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(r){r.stopPropagation();let{ship:s}=this.props;if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Au()){window.uiEvents.relay("CloseSystemInfo");return}window.uiEvents.relay("SystemClicked",{ship:s,system:s,element:r.target})}onShipTouchMove(r){if(!this.longPressTimer)return;const s=r.touches[0],d=s.clientX-this.touchStartX,g=s.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(g)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(r){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onShipTouchEnd(r){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(r){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:s}=this.props,d=shipManager.systems.getSystemByName(s,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:s,system:d||s.systems[0],element:r.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onUnknownTouchStart(r){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const s=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:g}=this.props,b=shipManager.systems.getSystemByName(g,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:g,system:b||g.systems[0],element:s,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){const r=this.elementRef.current,s=im(this.props.ship)?"left":"right";r.addEventListener("pointerdown",this.onDragStart),r.addEventListener("touchstart",this.onTouchDragStart,{passive:!1});const d=rE[s];if(d&&!iE()){const g=Math.max(0,Math.min(d.top,window.innerHeight-60)),b=Math.max(60-r.offsetWidth,Math.min(d.left,window.innerWidth-60));r.style.top=g+"px",r.style.left=b+"px",r.style.right="auto",this.positioned=!0}document.addEventListener("pointerdown",this.onDocumentPointerDown),this.applyScreenFit(),window.addEventListener("resize",this.onScreenResize),window.addEventListener("orientationchange",this.onScreenResize)}componentDidUpdate(){this.applyScreenFit()}componentWillUnmount(){document.removeEventListener("pointerdown",this.onDocumentPointerDown),window.removeEventListener("resize",this.onScreenResize),window.removeEventListener("orientationchange",this.onScreenResize);const r=this.elementRef.current;r&&(r.removeEventListener("pointerdown",this.onDragStart),r.removeEventListener("touchstart",this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(r){this.panelHoverTimer&&(clearTimeout(this.panelHoverTimer),this.panelHoverTimer=null),this.state.hoverPanel!==r&&this.setState({hoverPanel:r})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(r){if(!this.state.openPanel)return;const s=this.controlsRef.current,d=this.popupRef.current;s&&s.contains(r.target)||d&&d.contains(r.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay("CloseShipWindow",{ship:this.props.ship})}togglePanel(r,s){s.stopPropagation(),this.setState(d=>({openPanel:d.openPanel===r?null:r}))}toggleArt(r){r&&r.stopPropagation(),this.setState(s=>({showArt:!s.showArt,openPanel:null}))}renderResizeGrip(r){return y.jsx(x_,{className:"shipwindow-resize-grip shipwindow-nodrag",$pad:V_,$mirror:this.isMirroredGrip(),$overlap:!!r,onDoubleClick:this.onGripDoubleClick,title:"Drag to resize this window — double-click to reset its size"})}renderStatusStrip(r,s){const d=s?[{key:"rolled",text:"⟲ Rolled — port / starboard reversed"}].concat(pE(r)):pE(r);return y.jsxs(Ze.Fragment,{children:[d.map((g,b)=>y.jsx(T_,{$color:g.color,$bg:g.bg,$grip:b===d.length-1,children:g.text},g.key)),this.renderResizeGrip(d.length>0)]})}renderHeader(r,s,d){return y.jsxs(g_,{className:"shipwindow-drag-handle",title:"Drag to move — double-click to reset window size",children:[y.jsx(m_,{$tint:d,title:r,children:r}),y.jsx(v_,{title:s,children:s}),y.jsx(y_,{className:"shipwindow-nodrag",onClick:this.close.bind(this),children:"✕"})]})}renderHitChartButton(r){const{openPanel:s}=this.state;return y.jsxs(tm,{ref:this.hitChartBtnRef,$wide:r,$active:s==="hitchart",onClick:this.togglePanel.bind(this,"hitchart"),children:[y.jsx(eE,{children:"⊕"}),"Hit Chart"]})}renderStatsButton(r){const{openPanel:s}=this.state;return y.jsxs(tm,{$wide:r,$active:s==="shipstats",onClick:this.togglePanel.bind(this,"shipstats"),onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[y.jsxs(qC,{children:[y.jsx("i",{}),y.jsx("i",{}),y.jsx("i",{})]}),"Ship Stats"]})}renderArtButton(r){return y.jsxs(tm,{$wide:r,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[y.jsx(w_,{}),"Ship Art"]})}renderNotesButton(r){const{openPanel:s}=this.state;return y.jsxs(tm,{$wide:r,$active:s==="notes",onClick:this.togglePanel.bind(this,"notes"),onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[y.jsx(eE,{children:"✎"}),"Notes"]})}renderControls(r,s,d,g){const b=Au(),S=dE(this.props.ship),v=fE(this.props.ship);if(!r&&!s&&!g&&!S&&!v)return null;const T=b;return y.jsxs(b_,{ref:this.controlsRef,$compact:d,children:[r&&this.renderHitChartButton(T),S&&this.renderArtButton(T),v&&this.renderStatsButton(T),s&&this.renderNotesButton(T),g&&y.jsx(JC,{ship:this.props.ship})]})}getButtonLeft(){const r=this.controlsRef.current,s=this.elementRef.current;return!r||!s?null:Math.round((r.getBoundingClientRect().left-s.getBoundingClientRect().left)/(this.screenFit||1)-s.clientLeft)}getAnchorBelow(r,s){const d=r&&r.current,g=this.elementRef.current;if(!d||!g)return{top:s,left:this.getButtonLeft()};const b=d.getBoundingClientRect(),S=g.getBoundingClientRect(),v=this.screenFit||1;return{left:Math.round((b.left-S.left)/v-g.clientLeft),top:Math.round((b.bottom-S.top)/v-g.clientTop)+4}}renderPopup(r,s,d){const{ship:g}=this.props,{openPanel:b,hoverPanel:S}=this.state,v=b||S;if(!v)return null;const T=v==="hitchart"&&Au()?this.hitChartBtnRef:this.controlsRef,{top:$,left:O}=this.getAnchorBelow(T,d);return v==="hitchart"&&r?y.jsx(c0,{ref:this.popupRef,$top:$,$left:O,$fit:!0,children:y.jsx(a_,{ship:g})}):v==="shipstats"&&fE(g)?y.jsx(c0,{ref:this.popupRef,$top:$,$left:O,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:y.jsx(JC,{ship:g,live:!0,bare:!0})}):v==="notes"&&s?y.jsx(c0,{ref:this.popupRef,$top:$,$left:O,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:y.jsx(PC,{ship:g,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){const{ship:r}=this.props,s=Au(),d=im(r);var g=r.shipClass,b=r.name;let S=!1;if(r.mine){var v=shipManager.systems.getSystemByName(r,"mineStealth");v&&!v.isMineRevealed(r)&&(g="Mine",b="Mine",S=!0)}b||(b=g,g="");const T=!S&&q_(r),$=!s&&!S&&X_(r);if(r.flight)return s?y.jsxs(xp,{ref:this.elementRef,onClick:bp,onContextMenu:de=>{de.preventDefault(),de.stopPropagation()},$isMyTeam:d,$variant:"flightLobby",children:[this.renderHeader(b,g,m0()),y.jsxs(C_,{children:[y.jsx(E_,{children:y.jsx(QC,{ship:r})}),y.jsx(u0,{ship:r})]}),this.renderResizeGrip()]}):y.jsxs(xp,{ref:this.elementRef,onClick:bp,onContextMenu:de=>{de.preventDefault(),de.stopPropagation()},$isMyTeam:d,$variant:"flight",children:[this.renderHeader(b,g,m0()),y.jsx(QC,{ship:r}),this.renderStatusStrip(r)]});if(S)return y.jsxs(xp,{ref:this.elementRef,onClick:bp,onContextMenu:de=>{de.preventDefault(),de.stopPropagation()},$isMyTeam:d,$variant:"terrain",children:[this.renderHeader(b,g,null),y.jsxs(tE,{children:[y.jsx(f0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),y.jsx(d0,{$img:window.AssetManager.getSmartImagePath(r.imagePath)}),y.jsx(k_,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:"?"})]}),this.renderResizeGrip()]});const O=eL(r),P=J_(O);if((window.gamedata.isTerrain(r.shipSizeClass,r.userid)||r.mine)&&!cE(O)){const de=T||$||dE(r);return y.jsxs(xp,{ref:this.elementRef,onClick:bp,onContextMenu:ue=>{ue.preventDefault(),ue.stopPropagation()},$isMyTeam:d,$variant:"terrain",children:[this.renderHeader(b,g,null),y.jsxs(tE,{children:[y.jsx(f0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),y.jsx(d0,{$img:window.AssetManager.getSmartImagePath(r.imagePath),$art:this.state.showArt,$offsetY:r.mine&&de?Q_:0}),this.renderControls(T,$,!0),$_.map(ue=>O[ue].length>0&&y.jsx(VC,{location:ue,nameOverride:P[ue],ship:r,systems:O[ue],isTerrain:!0,hidden:this.state.showArt},`section-${r.id}-${ue}`))]}),s&&y.jsx(u0,{ship:r,full:!0}),this.renderStatusStrip(r),this.renderPopup(T,$,72)]})}const V=shipManager.movement.isRolled(r),N=!!r.enhancementTooltip,J=tL(O,N),xe=r.base&&!r.smallBase,Pe=y.jsxs(S_,{$areas:J,children:[y.jsx(f0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),y.jsx(d0,{$img:window.AssetManager.getSmartImagePath(r.imagePath),$art:this.state.showArt,$offsetY:s&&cE(O)?K_:0}),this.renderControls(T,$,!1,s&&!r.mine),s?y.jsx(u0,{ship:r,grid:!0,hideEnhancements:N}):y.jsx(jj,{ship:r}),N&&y.jsx(h_,{ship:r}),O_.map(de=>{if(O[de].length===0)return null;const ue=V&&nE[de]!==void 0?nE[de]:de,he=R_[ue],Le=de===0||de===1||de===2||xe&&A_.includes(de);return y.jsx(VC,{location:de,displayLocation:ue,area:he,valign:D_[he],justify:M_[he],wide:Le,minHeight:void 0,nameOverride:P[de],hidden:this.state.showArt,ship:r,systems:O[de]},`section-${r.id}-${de}`)})]});return y.jsxs(xp,{ref:this.elementRef,onClick:bp,onContextMenu:de=>{de.preventDefault(),de.stopPropagation()},$isMyTeam:d,$variant:"ship",children:[this.renderHeader(b,g,m0()),Pe,this.renderStatusStrip(r,V),this.renderPopup(T,$)]})}}const bp=()=>window.uiEvents.relay("CloseSystemInfo"),rE={left:null,right:null},L_="(max-width: 1024px)",z_="(orientation: portrait)",N_={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},P_={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},p0=.98,iE=()=>!!window.matchMedia&&window.matchMedia(L_).matches,F_=()=>window.matchMedia?window.matchMedia(z_).matches:window.innerHeight>=window.innerWidth,I_=(o,r)=>r===1?o:{fillW:Math.min(p0,o.fillW*r),fillH:Math.min(p0,o.fillH*r),min:Math.min(o.max,o.min*r),max:o.max},U_=()=>{const o=Au()?P_:N_;return F_()?I_(o,o.portrait):o},B_=.35,H_=3,V_=6,W_=8,Y_=400,aE=6,fd=40,h0="fv.shipwindow.userScale",g0=o=>Math.min(H_,Math.max(B_,o)),oE=(o,r,s,d)=>(s*o+d*r)/(o*o+r*r),nm={left:null,right:null},rm=o=>(nm[o]==null&&(nm[o]=G_(o)),nm[o]),lE=(o,r)=>{nm[o]=r},G_=o=>{try{let r=parseFloat(window.localStorage.getItem(h0+"."+o));if(isFinite(r)||(r=parseFloat(window.localStorage.getItem(h0))),isFinite(r)&&r>0)return g0(r)}catch{}return 1},sE=(o,r)=>{try{window.localStorage.setItem(h0+"."+o,String(r))}catch{}},uE=(o,r)=>{if(!o||r==null)return null;for(let s=0;s<o.length;s++)if(o[s].identifier===r)return o[s];return null},Au=()=>!!window.gamedata&&window.gamedata.gamephase===-2,K_=35,cE=o=>j_.some(r=>o[r].length>0),Q_=20,im=o=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide=="function"?window.ShipWindowManager.isLeftSide(o):o.team===window.gamedata.getPlayerTeam(),q_=o=>!!o.hitChart&&Object.keys(o.hitChart).length>0,dE=o=>!!(o&&o.imagePath)&&!o.flight,fE=o=>!!o&&!Au()&&!o.flight&&!o.mine&&!window.gamedata.isTerrain(o.shipSizeClass,o.userid),X_=o=>!!o.notes||!!o.enhancementTooltip||!!(o.hasAttached&&Object.keys(o.hasAttached).length>0),m0=o=>null,pE=o=>{if(Au())return[];const r=[],s=shipManager.getTurnDeployed(o);s>window.gamedata.turn&&s<999&&r.push({key:"deploying",color:H.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Deploying on Turn "+s});const d=shipManager.getArrivalIniPenalty(o);d!==0&&r.push({key:"arrivalScatter",color:H.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Arrival Scatter "+d+" Ini"});const g=o.trueStealth?shipManager.getStealthToggleForecast(o):null;if(o.trueStealth)if(Z_(o,g))r.push({key:"undetected",color:H.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Undetected"});else if(o.mine){const b=shipManager.systems.getSystemByName(o,"mineStealth");!b||b.isMineRevealedToOpponent(o)?r.push({key:"detected",color:H.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected - Revealed"}):r.push({key:"detected",color:H.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Detected - Not Revealed"})}else g===!0?r.push({key:"detected",color:H.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Would be Detected"}):r.push({key:"detected",color:H.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected"});if(shipManager.isJumpingToHyperspace(o)&&r.push({key:"jumping",color:H.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:"Jumping to Hyperspace"}),o.attached&&Object.keys(o.attached).length>0&&!o.detached){const b=window.gamedata.getShip(Object.keys(o.attached)[0]);if(b){const S=Object.values(o.attached)[0];let v="";S==1?v="Front":S==2?v="Aft":S==3||S==31||S==32?v="Port":(S==4||S==41||S==42)&&(v="Starboard"),r.push({key:"attached",color:H.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Attached to "+b.name+(v?" ["+v+"]":"")})}}return o.hasAttached&&Object.keys(o.hasAttached).length>0&&r.push({key:"boarded",color:H.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Ship is being boarded!"}),r},Z_=(o,r)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(o)==gamedata.turn||shipManager.getTurnDeployed(o)==gamedata.turn))return!0;if(r!=null)return!r;let s=shipManager.isDetected(o);if(!s&&o.team==gamedata.getPlayerTeam()){let d=null;o.mine?d=shipManager.systems.getSystemByName(o,"mineStealth"):o.faction=="Torvalus Speculators"?d=shipManager.systems.getSystemByName(o,"ShadingField"):shipManager.getSpecialAbilityStealth(o,"Cloaking")?d=shipManager.systems.getSystemByName(o,"CloakingDevice"):shipManager.getSpecialAbilityStealth(o,"Stealth")&&(d=shipManager.systems.getSystemByName(o,"stealth")),d&&(Array.isArray(d.detected)&&d.detected.length>0||d.detected===!0||Array.isArray(d.detectedNew)&&d.detectedNew.length>0||d.detectedNew===!0)&&(s=!0)}return!s},J_=o=>{const r={};return[{locations:[3,31,32],name:"Port"},{locations:[4,41,42],name:"Starboard"}].forEach(s=>{const d=s.locations.filter(g=>o[g].some(b=>b.name==="structure"));d.length===1&&d[0]!==s.locations[0]&&(r[d[0]]=s.name)}),r},eL=o=>{const r={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return o.systems.forEach(s=>{s.hideInShipWindow||r[s.location].push(s)}),r},tL=(o,r)=>{const s=[["ctrl","fwd","ew"]];let d=0;if((o[31].length||o[41].length)&&(s.push(["lfwd","prim","rfwd"]),d++),(o[3].length||o[4].length)&&(s.push(["left","prim","right"]),d++),(o[32].length||o[42].length)&&(s.push(["laft","prim","raft"]),d++),d===0&&o[0].length&&s.push([null,"prim",null]),o[2].length&&s.push([null,"aft",null]),r){const g=s[s.length-1];s.length>1&&g[2]===null?g[2]="enh":s.push([null,null,"enh"])}for(let g=1;g<s.length&&s[g][0]===null;g++)s[g][0]="ctrl";for(let g=1;g<s.length&&s[g][2]===null;g++)s[g][2]="ew";return s.map(g=>`"${g[0]||"."}  ${g[1]||"."}  ${g[2]||"."}"`).join(" ")},nL=D.div`

`,rL=D.div`
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
    border: 1px solid ${H.colors.warning};
    background-color: ${H.colors.windowBg};
    color: ${H.colors.text};
    font-family: ${H.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`,iL=D.span`
    cursor: pointer;
    color: ${H.colors.line};
    font-size: 16px;

    &:hover {
        color: ${H.colors.text};
    }
`;class aL extends Ze.Component{constructor(r){super(r),this.state={error:null}}static getDerivedStateFromError(r){return{error:r}}componentDidCatch(r,s){const d=this.props.ship;console.error("Ship window render failed for",d&&(d.name||d.shipClass),r,s)}render(){if(this.state.error){const r=this.props.ship;return y.jsxs(rL,{children:[y.jsxs("span",{children:[r&&(r.name||r.shipClass)||"Ship"," — window failed to render (see console)"]}),y.jsx(iL,{onClick:()=>window.uiEvents.relay("CloseShipWindow",{ship:r}),children:"✕"})]})}return this.props.children}}class oL extends Ze.Component{render(){const{ships:r}=this.props;return y.jsx(nL,{children:r.map(s=>y.jsx(aL,{ship:s,children:y.jsx(__,{ship:s})},`shipwindow-${s.userid}-${s.id}`))})}}const lL=D.div`
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
`;const sL=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 6px;
    font-size: 11px;
    color: ${qe.text};

    &:hover {
        background-color: rgba(51, 65, 79, 0.45);
    }
`,uL=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,cL=D.span`
    flex: 0 0 auto;
    color: ${qe.dim};
    font-size: 10px;
    user-select: none;
`,hE=D.div`
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
`,dL=D.input`
    flex: 0 0 40px;
    width: 40px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${H.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${qe.well};
    border: 1px solid ${qe.line};
    outline: none;

    &:focus { border-color: ${qe.focus}; }
`,fL=D.div`
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
`,pL=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r};class hL extends Ze.Component{constructor(r){super(r),this.wheelRefs={}}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=fp(s=>this.step(r,s.deltaY<0?1:-1))),this.wheelRefs[r]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(r,s){const{ship:d}=this.props,g=this.maxHealth();let b=parseInt(s,10);isNaN(b)&&(b=g),b=Math.max(1,Math.min(g,b)),battleDamage.setFighter(d,r,{d:g-b}),this.refresh()}step(r,s){this.setRemaining(r,battleDamage.fighterHealth(this.props.ship,r)+s)}onInput(r,s){const d=String(s.target.value).replace(/[^0-9]/g,"");this.setRemaining(r,d===""?0:parseInt(d,10))}propagate(){const{ship:r}=this.props,s=parseInt(r.flightSize,10)||0,d=battleDamage.fighterMaxHealth(r)-battleDamage.fighterHealth(r,1);for(let g=2;g<=s;g++)battleDamage.setFighter(r,g,{d});this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate()}render(){const{ship:r,boundingBox:s}=this.props,d=parseInt(r&&r.flightSize,10)||0,g=this.maxHealth();if(!d||!g)return null;battleDamage.flightSummary(r);const b=[];for(let T=1;T<=d;T++){const $=battleDamage.fighterHealth(r,T);b.push(y.jsxs(sL,{children:[y.jsxs(uL,{children:["Fighter ",T]}),y.jsx(hE,{title:"More damage",disabled:$<=1,onClick:()=>this.step(T,-1),children:"−"}),y.jsx(dL,{ref:this.wheelRef(T),type:"text",value:$,onChange:O=>this.onInput(T,O)}),y.jsx(hE,{title:"Repair",disabled:$>=g,onClick:()=>this.step(T,1),children:"+"}),y.jsxs(cL,{children:["/ ",g]})]},`ftr-${T}`))}const S=battleDamage.flightCritEntry(r)||{},v=mC(S.c,r.preBattleCritDesc,r.preBattleCritTransient,S.p);return y.jsxs(lL,{$position:pL(s),onClick:T=>T.stopPropagation(),children:[y.jsx(hC,{$sticky:!0,children:"Fighter Damage"}),b,d>1&&y.jsx(fL,{title:"Copy Fighter 1's damage to every fighter in this flight",onClick:()=>this.propagate(),children:"Apply Fighter 1's damage to all"}),y.jsx(vC,{ship:r,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:v,editable:!0,onChange:()=>this.refresh()})]})}}const gL=D.div`
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
`,mL=D.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${qe.dim};
    user-select: none;
`,vL=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 6px;
    font-size: 11px;
    color: ${qe.text};

    &:hover {
        background-color: rgba(51, 65, 79, 0.45);
    }
`,yL=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,xL=D.span`
    flex: 0 0 auto;
    color: ${qe.dim};
    font-size: 10px;
    user-select: none;
`,gE=D.div`
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
`,bL=D.input`
    flex: 0 0 40px;
    width: 40px;
    height: 18px;
    box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-family: ${H.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${qe.well};
    border: 1px solid ${qe.line};
    outline: none;

    &:focus { border-color: ${qe.focus}; }
`,wL=D.div`
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
`,SL=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r};class CL extends Ze.Component{constructor(r){super(r),this.wheelRefs={}}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=fp(s=>this.step(r,s.deltaY<0?1:-1))),this.wheelRefs[r]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(r,s){const{ship:d}=this.props,g=this.maxHealth();let b=parseInt(s,10);isNaN(b)&&(b=g),b=Math.max(1,Math.min(g,b)),battleDamage.setMine(d,r,{d:g-b}),this.refresh()}step(r,s){this.setRemaining(r,battleDamage.mineHealth(this.props.ship,r)+s)}onInput(r,s){const d=String(s.target.value).replace(/[^0-9]/g,"");this.setRemaining(r,d===""?0:parseInt(d,10))}propagate(){const{ship:r}=this.props,s=battleDamage.mineCount(r),d=battleDamage.getEntry(r,battleDamage.KIND_MINE,1);for(let g=2;g<=s;g++)battleDamage.setWholeEntry(r,battleDamage.KIND_MINE,g,d);this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate()}render(){const{ship:r,boundingBox:s}=this.props,d=battleDamage.mineCount(r),g=this.maxHealth();if(!d||g<2)return null;const b=battleDamage.mineSummary(r),S=[];for(let v=1;v<=d;v++){const T=battleDamage.mineHealth(r,v);S.push(y.jsxs(vL,{children:[y.jsxs(yL,{children:["Mine ",v]}),y.jsx(gE,{title:"More damage",disabled:T<=1,onClick:()=>this.step(v,-1),children:"−"}),y.jsx(bL,{ref:this.wheelRef(v),type:"text",value:T,onChange:$=>this.onInput(v,$)}),y.jsx(gE,{title:"Repair",disabled:T>=g,onClick:()=>this.step(v,1),children:"+"}),y.jsxs(xL,{children:["/ ",g]})]},`mne-${v}`))}return y.jsxs(gL,{$position:SL(s),onClick:v=>v.stopPropagation(),children:[y.jsx(hC,{$sticky:!0,children:"Mine Damage"}),y.jsxs(mL,{children:[b.remaining," / ",b.total," structure"]}),S,d>1&&y.jsx(wL,{title:"Copy Mine 1's damage to every mine in this purchase",onClick:()=>this.propagate(),children:"Apply Mine 1 to all"})]})}}class EL{constructor(r){this.parentElement=r,this.roots=new Map}getRoot(r){const s=jQuery(r,this.parentElement)[0];return s?(this.roots.has(s)||this.roots.set(s,tx(s)),this.roots.get(s)):null}unmountRoot(r){const s=jQuery(r,this.parentElement)[0];s&&this.roots.has(s)&&(this.roots.get(s).unmount(),this.roots.delete(s))}EwButtons(r){const s=this.getRoot("#showEwButtons");s&&s.render(y.jsx(ZM,{...r}))}FullScreen(r){const s=this.getRoot("#fullScreen");s&&s.render(y.jsx(GM,{...r}))}Surrender(r){const s=this.getRoot("#surrender");s&&s.render(y.jsx(QM,{...r}))}PlayerSettings(r){const s=this.getRoot("#playerSettings");s&&s.render(y.jsx(jM,{...r}))}showShipThrustUI(r){const s=this.getRoot("#shipThrust");s&&s.render(y.jsx(BM,{...r}))}hideShipThrustUI(){this.unmountRoot("#shipThrust")}showWeaponList(r){const s=this.getRoot("#weaponList");s&&s.render(y.jsx(aj,{...r}))}hideWeaponList(){this.unmountRoot("#weaponList")}showSystemInfo(r){const s=this.getRoot("#systemInfoReact");s&&s.render(y.jsx(uj,{...r}))}hideSystemInfo(){this.unmountRoot("#systemInfoReact")}showSystemInfoMenu(r){const s=this.getRoot("#systemInfoReact");s&&s.render(y.jsx(gj,{...r}))}hideSystemInfoMenu(){this.unmountRoot("#systemInfoReact")}canShowSystemInfoMenu(r,s){return Gx(r,s)}showFighterDamageMenu(r){const s=this.getRoot("#systemInfoReact");s&&s.render(y.jsx(hL,{...r}))}showMineDamageMenu(r){const s=this.getRoot("#systemInfoReact");s&&s.render(y.jsx(CL,{...r}))}renderShipWindows(r){const s=this.getRoot("#shipWindowsReact");s&&s.render(y.jsx(oL,{...r}))}}window.UIManager=EL});
