(function(Ag){typeof define=="function"&&define.amd?define(Ag):Ag()})(function(){"use strict";function Ag(o){return o&&o.__esModule&&Object.prototype.hasOwnProperty.call(o,"default")?o.default:o}var dx={exports:{}},dp={},fx={exports:{}},Lt={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var qS;function RD(){if(qS)return Lt;qS=1;var o=Symbol.for("react.element"),r=Symbol.for("react.portal"),l=Symbol.for("react.fragment"),d=Symbol.for("react.strict_mode"),h=Symbol.for("react.profiler"),b=Symbol.for("react.provider"),S=Symbol.for("react.context"),y=Symbol.for("react.forward_ref"),E=Symbol.for("react.suspense"),$=Symbol.for("react.memo"),M=Symbol.for("react.lazy"),N=Symbol.iterator;function j(V){return V===null||typeof V!="object"?null:(V=N&&V[N]||V["@@iterator"],typeof V=="function"?V:null)}var H={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},L=Object.assign,K={};function de(V,re,We){this.props=V,this.context=re,this.refs=K,this.updater=We||H}de.prototype.isReactComponent={},de.prototype.setState=function(V,re){if(typeof V!="object"&&typeof V!="function"&&V!=null)throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,V,re,"setState")},de.prototype.forceUpdate=function(V){this.updater.enqueueForceUpdate(this,V,"forceUpdate")};function Ee(){}Ee.prototype=de.prototype;function pe(V,re,We){this.props=V,this.context=re,this.refs=K,this.updater=We||H}var ae=pe.prototype=new Ee;ae.constructor=pe,L(ae,de.prototype),ae.isPureReactComponent=!0;var ce=Array.isArray,Te=Object.prototype.hasOwnProperty,le={current:null},ue={key:!0,ref:!0,__self:!0,__source:!0};function Ae(V,re,We){var et,it={},ht=null,$t=null;if(re!=null)for(et in re.ref!==void 0&&($t=re.ref),re.key!==void 0&&(ht=""+re.key),re)Te.call(re,et)&&!ue.hasOwnProperty(et)&&(it[et]=re[et]);var tt=arguments.length-2;if(tt===1)it.children=We;else if(1<tt){for(var vt=Array(tt),It=0;It<tt;It++)vt[It]=arguments[It+2];it.children=vt}if(V&&V.defaultProps)for(et in tt=V.defaultProps,tt)it[et]===void 0&&(it[et]=tt[et]);return{$$typeof:o,type:V,key:ht,ref:$t,props:it,_owner:le.current}}function ft(V,re){return{$$typeof:o,type:V.type,key:re,ref:V.ref,props:V.props,_owner:V._owner}}function Ve(V){return typeof V=="object"&&V!==null&&V.$$typeof===o}function Tt(V){var re={"=":"=0",":":"=2"};return"$"+V.replace(/[=:]/g,function(We){return re[We]})}var bt=/\/+/g;function rt(V,re){return typeof V=="object"&&V!==null&&V.key!=null?Tt(""+V.key):re.toString(36)}function He(V,re,We,et,it){var ht=typeof V;(ht==="undefined"||ht==="boolean")&&(V=null);var $t=!1;if(V===null)$t=!0;else switch(ht){case"string":case"number":$t=!0;break;case"object":switch(V.$$typeof){case o:case r:$t=!0}}if($t)return $t=V,it=it($t),V=et===""?"."+rt($t,0):et,ce(it)?(We="",V!=null&&(We=V.replace(bt,"$&/")+"/"),He(it,re,We,"",function(It){return It})):it!=null&&(Ve(it)&&(it=ft(it,We+(!it.key||$t&&$t.key===it.key?"":(""+it.key).replace(bt,"$&/")+"/")+V)),re.push(it)),1;if($t=0,et=et===""?".":et+":",ce(V))for(var tt=0;tt<V.length;tt++){ht=V[tt];var vt=et+rt(ht,tt);$t+=He(ht,re,We,vt,it)}else if(vt=j(V),typeof vt=="function")for(V=vt.call(V),tt=0;!(ht=V.next()).done;)ht=ht.value,vt=et+rt(ht,tt++),$t+=He(ht,re,We,vt,it);else if(ht==="object")throw re=String(V),Error("Objects are not valid as a React child (found: "+(re==="[object Object]"?"object with keys {"+Object.keys(V).join(", ")+"}":re)+"). If you meant to render a collection of children, use an array instead.");return $t}function Ht(V,re,We){if(V==null)return V;var et=[],it=0;return He(V,et,"","",function(ht){return re.call(We,ht,it++)}),et}function kt(V){if(V._status===-1){var re=V._result;re=re(),re.then(function(We){(V._status===0||V._status===-1)&&(V._status=1,V._result=We)},function(We){(V._status===0||V._status===-1)&&(V._status=2,V._result=We)}),V._status===-1&&(V._status=0,V._result=re)}if(V._status===1)return V._result.default;throw V._result}var pt={current:null},se={transition:null},Re={ReactCurrentDispatcher:pt,ReactCurrentBatchConfig:se,ReactCurrentOwner:le};function be(){throw Error("act(...) is not supported in production builds of React.")}return Lt.Children={map:Ht,forEach:function(V,re,We){Ht(V,function(){re.apply(this,arguments)},We)},count:function(V){var re=0;return Ht(V,function(){re++}),re},toArray:function(V){return Ht(V,function(re){return re})||[]},only:function(V){if(!Ve(V))throw Error("React.Children.only expected to receive a single React element child.");return V}},Lt.Component=de,Lt.Fragment=l,Lt.Profiler=h,Lt.PureComponent=pe,Lt.StrictMode=d,Lt.Suspense=E,Lt.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=Re,Lt.act=be,Lt.cloneElement=function(V,re,We){if(V==null)throw Error("React.cloneElement(...): The argument must be a React element, but you passed "+V+".");var et=L({},V.props),it=V.key,ht=V.ref,$t=V._owner;if(re!=null){if(re.ref!==void 0&&(ht=re.ref,$t=le.current),re.key!==void 0&&(it=""+re.key),V.type&&V.type.defaultProps)var tt=V.type.defaultProps;for(vt in re)Te.call(re,vt)&&!ue.hasOwnProperty(vt)&&(et[vt]=re[vt]===void 0&&tt!==void 0?tt[vt]:re[vt])}var vt=arguments.length-2;if(vt===1)et.children=We;else if(1<vt){tt=Array(vt);for(var It=0;It<vt;It++)tt[It]=arguments[It+2];et.children=tt}return{$$typeof:o,type:V.type,key:it,ref:ht,props:et,_owner:$t}},Lt.createContext=function(V){return V={$$typeof:S,_currentValue:V,_currentValue2:V,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null},V.Provider={$$typeof:b,_context:V},V.Consumer=V},Lt.createElement=Ae,Lt.createFactory=function(V){var re=Ae.bind(null,V);return re.type=V,re},Lt.createRef=function(){return{current:null}},Lt.forwardRef=function(V){return{$$typeof:y,render:V}},Lt.isValidElement=Ve,Lt.lazy=function(V){return{$$typeof:M,_payload:{_status:-1,_result:V},_init:kt}},Lt.memo=function(V,re){return{$$typeof:$,type:V,compare:re===void 0?null:re}},Lt.startTransition=function(V){var re=se.transition;se.transition={};try{V()}finally{se.transition=re}},Lt.unstable_act=be,Lt.useCallback=function(V,re){return pt.current.useCallback(V,re)},Lt.useContext=function(V){return pt.current.useContext(V)},Lt.useDebugValue=function(){},Lt.useDeferredValue=function(V){return pt.current.useDeferredValue(V)},Lt.useEffect=function(V,re){return pt.current.useEffect(V,re)},Lt.useId=function(){return pt.current.useId()},Lt.useImperativeHandle=function(V,re,We){return pt.current.useImperativeHandle(V,re,We)},Lt.useInsertionEffect=function(V,re){return pt.current.useInsertionEffect(V,re)},Lt.useLayoutEffect=function(V,re){return pt.current.useLayoutEffect(V,re)},Lt.useMemo=function(V,re){return pt.current.useMemo(V,re)},Lt.useReducer=function(V,re,We){return pt.current.useReducer(V,re,We)},Lt.useRef=function(V){return pt.current.useRef(V)},Lt.useState=function(V){return pt.current.useState(V)},Lt.useSyncExternalStore=function(V,re,We){return pt.current.useSyncExternalStore(V,re,We)},Lt.useTransition=function(){return pt.current.useTransition()},Lt.version="18.3.1",Lt}var fp={exports:{}};fp.exports;var XS;function DD(){return XS||(XS=1,function(o,r){var l={};/**
 * @license React
 * react.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */l.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var d="18.3.1",h=Symbol.for("react.element"),b=Symbol.for("react.portal"),S=Symbol.for("react.fragment"),y=Symbol.for("react.strict_mode"),E=Symbol.for("react.profiler"),$=Symbol.for("react.provider"),M=Symbol.for("react.context"),N=Symbol.for("react.forward_ref"),j=Symbol.for("react.suspense"),H=Symbol.for("react.suspense_list"),L=Symbol.for("react.memo"),K=Symbol.for("react.lazy"),de=Symbol.for("react.offscreen"),Ee=Symbol.iterator,pe="@@iterator";function ae(T){if(T===null||typeof T!="object")return null;var z=Ee&&T[Ee]||T[pe];return typeof z=="function"?z:null}var ce={current:null},Te={transition:null},le={current:null,isBatchingLegacy:!1,didScheduleLegacyUpdate:!1},ue={current:null},Ae={},ft=null;function Ve(T){ft=T}Ae.setExtraStackFrame=function(T){ft=T},Ae.getCurrentStack=null,Ae.getStackAddendum=function(){var T="";ft&&(T+=ft);var z=Ae.getCurrentStack;return z&&(T+=z()||""),T};var Tt=!1,bt=!1,rt=!1,He=!1,Ht=!1,kt={ReactCurrentDispatcher:ce,ReactCurrentBatchConfig:Te,ReactCurrentOwner:ue};kt.ReactDebugCurrentFrame=Ae,kt.ReactCurrentActQueue=le;function pt(T){{for(var z=arguments.length,q=new Array(z>1?z-1:0),ee=1;ee<z;ee++)q[ee-1]=arguments[ee];Re("warn",T,q)}}function se(T){{for(var z=arguments.length,q=new Array(z>1?z-1:0),ee=1;ee<z;ee++)q[ee-1]=arguments[ee];Re("error",T,q)}}function Re(T,z,q){{var ee=kt.ReactDebugCurrentFrame,xe=ee.getStackAddendum();xe!==""&&(z+="%s",q=q.concat([xe]));var Pe=q.map(function(je){return String(je)});Pe.unshift("Warning: "+z),Function.prototype.apply.call(console[T],console,Pe)}}var be={};function V(T,z){{var q=T.constructor,ee=q&&(q.displayName||q.name)||"ReactClass",xe=ee+"."+z;if(be[xe])return;se("Can't call %s on a component that is not yet mounted. This is a no-op, but it might indicate a bug in your application. Instead, assign to `this.state` directly or define a `state = {};` class property with the desired state in the %s component.",z,ee),be[xe]=!0}}var re={isMounted:function(T){return!1},enqueueForceUpdate:function(T,z,q){V(T,"forceUpdate")},enqueueReplaceState:function(T,z,q,ee){V(T,"replaceState")},enqueueSetState:function(T,z,q,ee){V(T,"setState")}},We=Object.assign,et={};Object.freeze(et);function it(T,z,q){this.props=T,this.context=z,this.refs=et,this.updater=q||re}it.prototype.isReactComponent={},it.prototype.setState=function(T,z){if(typeof T!="object"&&typeof T!="function"&&T!=null)throw new Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,T,z,"setState")},it.prototype.forceUpdate=function(T){this.updater.enqueueForceUpdate(this,T,"forceUpdate")};{var ht={isMounted:["isMounted","Instead, make sure to clean up subscriptions and pending requests in componentWillUnmount to prevent memory leaks."],replaceState:["replaceState","Refactor your code to use setState instead (see https://github.com/facebook/react/issues/3236)."]},$t=function(T,z){Object.defineProperty(it.prototype,T,{get:function(){pt("%s(...) is deprecated in plain JavaScript React classes. %s",z[0],z[1])}})};for(var tt in ht)ht.hasOwnProperty(tt)&&$t(tt,ht[tt])}function vt(){}vt.prototype=it.prototype;function It(T,z,q){this.props=T,this.context=z,this.refs=et,this.updater=q||re}var pn=It.prototype=new vt;pn.constructor=It,We(pn,it.prototype),pn.isPureReactComponent=!0;function an(){var T={current:null};return Object.seal(T),T}var Pn=Array.isArray;function xn(T){return Pn(T)}function An(T){{var z=typeof Symbol=="function"&&Symbol.toStringTag,q=z&&T[Symbol.toStringTag]||T.constructor.name||"Object";return q}}function tr(T){try{return Gn(T),!1}catch{return!0}}function Gn(T){return""+T}function Ri(T){if(tr(T))return se("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",An(T)),Gn(T)}function ga(T,z,q){var ee=T.displayName;if(ee)return ee;var xe=z.displayName||z.name||"";return xe!==""?q+"("+xe+")":q}function Ur(T){return T.displayName||"Context"}function nr(T){if(T==null)return null;if(typeof T.tag=="number"&&se("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof T=="function")return T.displayName||T.name||null;if(typeof T=="string")return T;switch(T){case S:return"Fragment";case b:return"Portal";case E:return"Profiler";case y:return"StrictMode";case j:return"Suspense";case H:return"SuspenseList"}if(typeof T=="object")switch(T.$$typeof){case M:var z=T;return Ur(z)+".Consumer";case $:var q=T;return Ur(q._context)+".Provider";case N:return ga(T,T.render,"ForwardRef");case L:var ee=T.displayName||null;return ee!==null?ee:nr(T.type)||"Memo";case K:{var xe=T,Pe=xe._payload,je=xe._init;try{return nr(je(Pe))}catch{return null}}}return null}var ur=Object.prototype.hasOwnProperty,cr={key:!0,ref:!0,__self:!0,__source:!0},_r,ma,Kn;Kn={};function xr(T){if(ur.call(T,"ref")){var z=Object.getOwnPropertyDescriptor(T,"ref").get;if(z&&z.isReactWarning)return!1}return T.ref!==void 0}function oi(T){if(ur.call(T,"key")){var z=Object.getOwnPropertyDescriptor(T,"key").get;if(z&&z.isReactWarning)return!1}return T.key!==void 0}function ro(T,z){var q=function(){_r||(_r=!0,se("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",z))};q.isReactWarning=!0,Object.defineProperty(T,"key",{get:q,configurable:!0})}function Di(T,z){var q=function(){ma||(ma=!0,se("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",z))};q.isReactWarning=!0,Object.defineProperty(T,"ref",{get:q,configurable:!0})}function we(T){if(typeof T.ref=="string"&&ue.current&&T.__self&&ue.current.stateNode!==T.__self){var z=nr(ue.current.type);Kn[z]||(se('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. This case cannot be automatically converted to an arrow function. We ask you to manually fix this case by using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',z,T.ref),Kn[z]=!0)}}var Xe=function(T,z,q,ee,xe,Pe,je){var at={$$typeof:h,type:T,key:z,ref:q,props:je,_owner:Pe};return at._store={},Object.defineProperty(at._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(at,"_self",{configurable:!1,enumerable:!1,writable:!1,value:ee}),Object.defineProperty(at,"_source",{configurable:!1,enumerable:!1,writable:!1,value:xe}),Object.freeze&&(Object.freeze(at.props),Object.freeze(at)),at};function wt(T,z,q){var ee,xe={},Pe=null,je=null,at=null,Ct=null;if(z!=null){xr(z)&&(je=z.ref,we(z)),oi(z)&&(Ri(z.key),Pe=""+z.key),at=z.__self===void 0?null:z.__self,Ct=z.__source===void 0?null:z.__source;for(ee in z)ur.call(z,ee)&&!cr.hasOwnProperty(ee)&&(xe[ee]=z[ee])}var qt=arguments.length-2;if(qt===1)xe.children=q;else if(qt>1){for(var sn=Array(qt),un=0;un<qt;un++)sn[un]=arguments[un+2];Object.freeze&&Object.freeze(sn),xe.children=sn}if(T&&T.defaultProps){var yt=T.defaultProps;for(ee in yt)xe[ee]===void 0&&(xe[ee]=yt[ee])}if(Pe||je){var hn=typeof T=="function"?T.displayName||T.name||"Unknown":T;Pe&&ro(xe,hn),je&&Di(xe,hn)}return Xe(T,Pe,je,at,Ct,ue.current,xe)}function Gt(T,z){var q=Xe(T.type,z,T.ref,T._self,T._source,T._owner,T.props);return q}function bn(T,z,q){if(T==null)throw new Error("React.cloneElement(...): The argument must be a React element, but you passed "+T+".");var ee,xe=We({},T.props),Pe=T.key,je=T.ref,at=T._self,Ct=T._source,qt=T._owner;if(z!=null){xr(z)&&(je=z.ref,qt=ue.current),oi(z)&&(Ri(z.key),Pe=""+z.key);var sn;T.type&&T.type.defaultProps&&(sn=T.type.defaultProps);for(ee in z)ur.call(z,ee)&&!cr.hasOwnProperty(ee)&&(z[ee]===void 0&&sn!==void 0?xe[ee]=sn[ee]:xe[ee]=z[ee])}var un=arguments.length-2;if(un===1)xe.children=q;else if(un>1){for(var yt=Array(un),hn=0;hn<un;hn++)yt[hn]=arguments[hn+2];xe.children=yt}return Xe(T.type,Pe,je,at,Ct,qt,xe)}function wn(T){return typeof T=="object"&&T!==null&&T.$$typeof===h}var Sn=".",dr=":";function vn(T){var z=/[=:]/g,q={"=":"=0",":":"=2"},ee=T.replace(z,function(xe){return q[xe]});return"$"+ee}var on=!1,Kt=/\/+/g;function Mi(T){return T.replace(Kt,"$&/")}function Qi(T,z){return typeof T=="object"&&T!==null&&T.key!=null?(Ri(T.key),vn(""+T.key)):z.toString(36)}function qi(T,z,q,ee,xe){var Pe=typeof T;(Pe==="undefined"||Pe==="boolean")&&(T=null);var je=!1;if(T===null)je=!0;else switch(Pe){case"string":case"number":je=!0;break;case"object":switch(T.$$typeof){case h:case b:je=!0}}if(je){var at=T,Ct=xe(at),qt=ee===""?Sn+Qi(at,0):ee;if(xn(Ct)){var sn="";qt!=null&&(sn=Mi(qt)+"/"),qi(Ct,z,sn,"",function(jp){return jp})}else Ct!=null&&(wn(Ct)&&(Ct.key&&(!at||at.key!==Ct.key)&&Ri(Ct.key),Ct=Gt(Ct,q+(Ct.key&&(!at||at.key!==Ct.key)?Mi(""+Ct.key)+"/":"")+qt)),z.push(Ct));return 1}var un,yt,hn=0,Bn=ee===""?Sn:ee+dr;if(xn(T))for(var $l=0;$l<T.length;$l++)un=T[$l],yt=Bn+Qi(un,$l),hn+=qi(un,z,q,yt,xe);else{var Yu=ae(T);if(typeof Yu=="function"){var ho=T;Yu===ho.entries&&(on||pt("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),on=!0);for(var Ol=Yu.call(ho),Gu,Ap=0;!(Gu=Ol.next()).done;)un=Gu.value,yt=Bn+Qi(un,Ap++),hn+=qi(un,z,q,yt,xe)}else if(Pe==="object"){var Td=String(T);throw new Error("Objects are not valid as a React child (found: "+(Td==="[object Object]"?"object with keys {"+Object.keys(T).join(", ")+"}":Td)+"). If you meant to render a collection of children, use an array instead.")}}return hn}function io(T,z,q){if(T==null)return T;var ee=[],xe=0;return qi(T,ee,"","",function(Pe){return z.call(q,Pe,xe++)}),ee}function Sl(T){var z=0;return io(T,function(){z++}),z}function Cl(T,z,q){io(T,function(){z.apply(this,arguments)},q)}function ao(T){return io(T,function(z){return z})||[]}function El(T){if(!wn(T))throw new Error("React.Children.only expected to receive a single React element child.");return T}function ja(T){var z={$$typeof:M,_currentValue:T,_currentValue2:T,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null};z.Provider={$$typeof:$,_context:z};var q=!1,ee=!1,xe=!1;{var Pe={$$typeof:M,_context:z};Object.defineProperties(Pe,{Provider:{get:function(){return ee||(ee=!0,se("Rendering <Context.Consumer.Provider> is not supported and will be removed in a future major release. Did you mean to render <Context.Provider> instead?")),z.Provider},set:function(je){z.Provider=je}},_currentValue:{get:function(){return z._currentValue},set:function(je){z._currentValue=je}},_currentValue2:{get:function(){return z._currentValue2},set:function(je){z._currentValue2=je}},_threadCount:{get:function(){return z._threadCount},set:function(je){z._threadCount=je}},Consumer:{get:function(){return q||(q=!0,se("Rendering <Context.Consumer.Consumer> is not supported and will be removed in a future major release. Did you mean to render <Context.Consumer> instead?")),z.Consumer}},displayName:{get:function(){return z.displayName},set:function(je){xe||(pt("Setting `displayName` on Context.Consumer has no effect. You should set it directly on the context with Context.displayName = '%s'.",je),xe=!0)}}}),z.Consumer=Pe}return z._currentRenderer=null,z._currentRenderer2=null,z}var $i=-1,br=0,Oi=1,li=2;function _a(T){if(T._status===$i){var z=T._result,q=z();if(q.then(function(Pe){if(T._status===br||T._status===$i){var je=T;je._status=Oi,je._result=Pe}},function(Pe){if(T._status===br||T._status===$i){var je=T;je._status=li,je._result=Pe}}),T._status===$i){var ee=T;ee._status=br,ee._result=q}}if(T._status===Oi){var xe=T._result;return xe===void 0&&se(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))

Did you accidentally put curly braces around the import?`,xe),"default"in xe||se(`lazy: Expected the result of a dynamic import() call. Instead received: %s

Your code should look like: 
  const MyComponent = lazy(() => import('./MyComponent'))`,xe),xe.default}else throw T._result}function La(T){var z={_status:$i,_result:T},q={$$typeof:K,_payload:z,_init:_a};{var ee,xe;Object.defineProperties(q,{defaultProps:{configurable:!0,get:function(){return ee},set:function(Pe){se("React.lazy(...): It is not supported to assign `defaultProps` to a lazy component import. Either specify them where the component is defined, or create a wrapping component around it."),ee=Pe,Object.defineProperty(q,"defaultProps",{enumerable:!0})}},propTypes:{configurable:!0,get:function(){return xe},set:function(Pe){se("React.lazy(...): It is not supported to assign `propTypes` to a lazy component import. Either specify them where the component is defined, or create a wrapping component around it."),xe=Pe,Object.defineProperty(q,"propTypes",{enumerable:!0})}}})}return q}function oo(T){T!=null&&T.$$typeof===L?se("forwardRef requires a render function but received a `memo` component. Instead of forwardRef(memo(...)), use memo(forwardRef(...))."):typeof T!="function"?se("forwardRef requires a render function but was given %s.",T===null?"null":typeof T):T.length!==0&&T.length!==2&&se("forwardRef render functions accept exactly two parameters: props and ref. %s",T.length===1?"Did you forget to use the ref parameter?":"Any additional parameter will be undefined."),T!=null&&(T.defaultProps!=null||T.propTypes!=null)&&se("forwardRef render functions do not support propTypes or defaultProps. Did you accidentally pass a React component?");var z={$$typeof:N,render:T};{var q;Object.defineProperty(z,"displayName",{enumerable:!1,configurable:!0,get:function(){return q},set:function(ee){q=ee,!T.name&&!T.displayName&&(T.displayName=ee)}})}return z}var P;P=Symbol.for("react.module.reference");function fe(T){return!!(typeof T=="string"||typeof T=="function"||T===S||T===E||Ht||T===y||T===j||T===H||He||T===de||Tt||bt||rt||typeof T=="object"&&T!==null&&(T.$$typeof===K||T.$$typeof===L||T.$$typeof===$||T.$$typeof===M||T.$$typeof===N||T.$$typeof===P||T.getModuleId!==void 0))}function ke(T,z){fe(T)||se("memo: The first argument must be a component. Instead received: %s",T===null?"null":typeof T);var q={$$typeof:L,type:T,compare:z===void 0?null:z};{var ee;Object.defineProperty(q,"displayName",{enumerable:!1,configurable:!0,get:function(){return ee},set:function(xe){ee=xe,!T.name&&!T.displayName&&(T.displayName=xe)}})}return q}function Me(){var T=ce.current;return T===null&&se(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://reactjs.org/link/invalid-hook-call for tips about how to debug and fix this problem.`),T}function Rt(T){var z=Me();if(T._context!==void 0){var q=T._context;q.Consumer===T?se("Calling useContext(Context.Consumer) is not supported, may cause bugs, and will be removed in a future major release. Did you mean to call useContext(Context) instead?"):q.Provider===T&&se("Calling useContext(Context.Provider) is not supported. Did you mean to call useContext(Context) instead?")}return z.useContext(T)}function ut(T){var z=Me();return z.useState(T)}function Ot(T,z,q){var ee=Me();return ee.useReducer(T,z,q)}function St(T){var z=Me();return z.useRef(T)}function Fn(T,z){var q=Me();return q.useEffect(T,z)}function yn(T,z){var q=Me();return q.useInsertionEffect(T,z)}function Cn(T,z){var q=Me();return q.useLayoutEffect(T,z)}function Lr(T,z){var q=Me();return q.useCallback(T,z)}function va(T,z){var q=Me();return q.useMemo(T,z)}function Qt(T,z,q){var ee=Me();return ee.useImperativeHandle(T,z,q)}function kn(T,z){{var q=Me();return q.useDebugValue(T,z)}}function gt(){var T=Me();return T.useTransition()}function za(T){var z=Me();return z.useDeferredValue(T)}function lo(){var T=Me();return T.useId()}function wd(T,z,q){var ee=Me();return ee.useSyncExternalStore(T,z,q)}var so=0,Lo,si,Bu,Hr,Iu,Sd,Cd;function uo(){}uo.__reactDisabledLog=!0;function zo(){{if(so===0){Lo=console.log,si=console.info,Bu=console.warn,Hr=console.error,Iu=console.group,Sd=console.groupCollapsed,Cd=console.groupEnd;var T={configurable:!0,enumerable:!0,value:uo,writable:!0};Object.defineProperties(console,{info:T,log:T,warn:T,error:T,group:T,groupCollapsed:T,groupEnd:T})}so++}}function ui(){{if(so--,so===0){var T={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:We({},T,{value:Lo}),info:We({},T,{value:si}),warn:We({},T,{value:Bu}),error:We({},T,{value:Hr}),group:We({},T,{value:Iu}),groupCollapsed:We({},T,{value:Sd}),groupEnd:We({},T,{value:Cd})})}so<0&&se("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var Na=kt.ReactCurrentDispatcher,No;function Es(T,z,q){{if(No===void 0)try{throw Error()}catch(xe){var ee=xe.stack.trim().match(/\n( *(at )?)/);No=ee&&ee[1]||""}return`
`+No+T}}var co=!1,Tl;{var kl=typeof WeakMap=="function"?WeakMap:Map;Tl=new kl}function Po(T,z){if(!T||co)return"";{var q=Tl.get(T);if(q!==void 0)return q}var ee;co=!0;var xe=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var Pe;Pe=Na.current,Na.current=null,zo();try{if(z){var je=function(){throw Error()};if(Object.defineProperty(je.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(je,[])}catch(Bn){ee=Bn}Reflect.construct(T,[],je)}else{try{je.call()}catch(Bn){ee=Bn}T.call(je.prototype)}}else{try{throw Error()}catch(Bn){ee=Bn}T()}}catch(Bn){if(Bn&&ee&&typeof Bn.stack=="string"){for(var at=Bn.stack.split(`
`),Ct=ee.stack.split(`
`),qt=at.length-1,sn=Ct.length-1;qt>=1&&sn>=0&&at[qt]!==Ct[sn];)sn--;for(;qt>=1&&sn>=0;qt--,sn--)if(at[qt]!==Ct[sn]){if(qt!==1||sn!==1)do if(qt--,sn--,sn<0||at[qt]!==Ct[sn]){var un=`
`+at[qt].replace(" at new "," at ");return T.displayName&&un.includes("<anonymous>")&&(un=un.replace("<anonymous>",T.displayName)),typeof T=="function"&&Tl.set(T,un),un}while(qt>=1&&sn>=0);break}}}finally{co=!1,Na.current=Pe,ui(),Error.prepareStackTrace=xe}var yt=T?T.displayName||T.name:"",hn=yt?Es(yt):"";return typeof T=="function"&&Tl.set(T,hn),hn}function Uu(T,z,q){return Po(T,!1)}function Hu(T){var z=T.prototype;return!!(z&&z.isReactComponent)}function Nt(T,z,q){if(T==null)return"";if(typeof T=="function")return Po(T,Hu(T));if(typeof T=="string")return Es(T);switch(T){case j:return Es("Suspense");case H:return Es("SuspenseList")}if(typeof T=="object")switch(T.$$typeof){case N:return Uu(T.render);case L:return Nt(T.type,z,q);case K:{var ee=T,xe=ee._payload,Pe=ee._init;try{return Nt(Pe(xe),z,q)}catch{}}}return""}var Vu={},Ts=kt.ReactDebugCurrentFrame;function Pt(T){if(T){var z=T._owner,q=Nt(T.type,T._source,z?z.type:null);Ts.setExtraStackFrame(q)}else Ts.setExtraStackFrame(null)}function Ed(T,z,q,ee,xe){{var Pe=Function.call.bind(ur);for(var je in T)if(Pe(T,je)){var at=void 0;try{if(typeof T[je]!="function"){var Ct=Error((ee||"React class")+": "+q+" type `"+je+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof T[je]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw Ct.name="Invariant Violation",Ct}at=T[je](z,je,ee,q,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(qt){at=qt}at&&!(at instanceof Error)&&(Pt(xe),se("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",ee||"React class",q,je,typeof at),Pt(null)),at instanceof Error&&!(at.message in Vu)&&(Vu[at.message]=!0,Pt(xe),se("Failed %s type: %s",q,at.message),Pt(null))}}}function Pa(T){if(T){var z=T._owner,q=Nt(T.type,T._source,z?z.type:null);Ve(q)}else Ve(null)}var lt;lt=!1;function Rl(){if(ue.current){var T=nr(ue.current.type);if(T)return`

Check the render method of \``+T+"`."}return""}function fr(T){if(T!==void 0){var z=T.fileName.replace(/^.*[\\\/]/,""),q=T.lineNumber;return`

Check your code at `+z+":"+q+"."}return""}function ci(T){return T!=null?fr(T.__source):""}var Vr={};function Fa(T){var z=Rl();if(!z){var q=typeof T=="string"?T:T.displayName||T.name;q&&(z=`

Check the top-level render call using <`+q+">.")}return z}function _n(T,z){if(!(!T._store||T._store.validated||T.key!=null)){T._store.validated=!0;var q=Fa(z);if(!Vr[q]){Vr[q]=!0;var ee="";T&&T._owner&&T._owner!==ue.current&&(ee=" It was passed a child from "+nr(T._owner.type)+"."),Pa(T),se('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',q,ee),Pa(null)}}}function ln(T,z){if(typeof T=="object"){if(xn(T))for(var q=0;q<T.length;q++){var ee=T[q];wn(ee)&&_n(ee,z)}else if(wn(T))T._store&&(T._store.validated=!0);else if(T){var xe=ae(T);if(typeof xe=="function"&&xe!==T.entries)for(var Pe=xe.call(T),je;!(je=Pe.next()).done;)wn(je.value)&&_n(je.value,z)}}}function ya(T){{var z=T.type;if(z==null||typeof z=="string")return;var q;if(typeof z=="function")q=z.propTypes;else if(typeof z=="object"&&(z.$$typeof===N||z.$$typeof===L))q=z.propTypes;else return;if(q){var ee=nr(z);Ed(q,T.props,"prop",ee,T)}else if(z.PropTypes!==void 0&&!lt){lt=!0;var xe=nr(z);se("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",xe||"Unknown")}typeof z.getDefaultProps=="function"&&!z.getDefaultProps.isReactClassApproved&&se("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function Xi(T){{for(var z=Object.keys(T.props),q=0;q<z.length;q++){var ee=z[q];if(ee!=="children"&&ee!=="key"){Pa(T),se("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",ee),Pa(null);break}}T.ref!==null&&(Pa(T),se("Invalid attribute `ref` supplied to `React.Fragment`."),Pa(null))}}function zr(T,z,q){var ee=fe(T);if(!ee){var xe="";(T===void 0||typeof T=="object"&&T!==null&&Object.keys(T).length===0)&&(xe+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var Pe=ci(z);Pe?xe+=Pe:xe+=Rl();var je;T===null?je="null":xn(T)?je="array":T!==void 0&&T.$$typeof===h?(je="<"+(nr(T.type)||"Unknown")+" />",xe=" Did you accidentally export a JSX literal instead of a component?"):je=typeof T,se("React.createElement: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",je,xe)}var at=wt.apply(this,arguments);if(at==null)return at;if(ee)for(var Ct=2;Ct<arguments.length;Ct++)ln(arguments[Ct],T);return T===S?Xi(at):ya(at),at}var Wr=!1;function Op(T){var z=zr.bind(null,T);return z.type=T,Wr||(Wr=!0,pt("React.createFactory() is deprecated and will be removed in a future major release. Consider using JSX or use React.createElement() directly instead.")),Object.defineProperty(z,"type",{enumerable:!1,get:function(){return pt("Factory.type is deprecated. Access the class directly before passing it to createFactory."),Object.defineProperty(this,"type",{value:T}),T}}),z}function ks(T,z,q){for(var ee=bn.apply(this,arguments),xe=2;xe<arguments.length;xe++)ln(arguments[xe],ee.type);return ya(ee),ee}function Dl(T,z){var q=Te.transition;Te.transition={};var ee=Te.transition;Te.transition._updatedFibers=new Set;try{T()}finally{if(Te.transition=q,q===null&&ee._updatedFibers){var xe=ee._updatedFibers.size;xe>10&&pt("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."),ee._updatedFibers.clear()}}}var Rs=!1,Ds=null;function Ml(T){if(Ds===null)try{var z=("require"+Math.random()).slice(0,7),q=o&&o[z];Ds=q.call(o,"timers").setImmediate}catch{Ds=function(xe){Rs===!1&&(Rs=!0,typeof MessageChannel>"u"&&se("This browser does not have a MessageChannel implementation, so enqueuing tasks via await act(async () => ...) will fail. Please file an issue at https://github.com/facebook/react/issues if you encounter this warning."));var Pe=new MessageChannel;Pe.port1.onmessage=xe,Pe.port2.postMessage(void 0)}}return Ds(T)}var Ji=0,Zi=!1;function Fo(T){{var z=Ji;Ji++,le.current===null&&(le.current=[]);var q=le.isBatchingLegacy,ee;try{if(le.isBatchingLegacy=!0,ee=T(),!q&&le.didScheduleLegacyUpdate){var xe=le.current;xe!==null&&(le.didScheduleLegacyUpdate=!1,po(xe))}}catch(yt){throw fo(z),yt}finally{le.isBatchingLegacy=q}if(ee!==null&&typeof ee=="object"&&typeof ee.then=="function"){var Pe=ee,je=!1,at={then:function(yt,hn){je=!0,Pe.then(function(Bn){fo(z),Ji===0?Ms(Bn,yt,hn):yt(Bn)},function(Bn){fo(z),hn(Bn)})}};return!Zi&&typeof Promise<"u"&&Promise.resolve().then(function(){}).then(function(){je||(Zi=!0,se("You called act(async () => ...) without await. This could lead to unexpected testing behaviour, interleaving multiple act calls and mixing their scopes. You should - await act(async () => ...);"))}),at}else{var Ct=ee;if(fo(z),Ji===0){var qt=le.current;qt!==null&&(po(qt),le.current=null);var sn={then:function(yt,hn){le.current===null?(le.current=[],Ms(Ct,yt,hn)):yt(Ct)}};return sn}else{var un={then:function(yt,hn){yt(Ct)}};return un}}}}function fo(T){T!==Ji-1&&se("You seem to have overlapping act() calls, this is not supported. Be sure to await previous act() calls before making a new one. "),Ji=T}function Ms(T,z,q){{var ee=le.current;if(ee!==null)try{po(ee),Ml(function(){ee.length===0?(le.current=null,z(T)):Ms(T,z,q)})}catch(xe){q(xe)}else z(T)}}var Bo=!1;function po(T){if(!Bo){Bo=!0;var z=0;try{for(;z<T.length;z++){var q=T[z];do q=q(!0);while(q!==null)}T.length=0}catch(ee){throw T=T.slice(z+1),ee}finally{Bo=!1}}}var $s=zr,Wu=ks,ea=Op,Os={map:io,forEach:Cl,count:Sl,toArray:ao,only:El};r.Children=Os,r.Component=it,r.Fragment=S,r.Profiler=E,r.PureComponent=It,r.StrictMode=y,r.Suspense=j,r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=kt,r.act=Fo,r.cloneElement=Wu,r.createContext=ja,r.createElement=$s,r.createFactory=ea,r.createRef=an,r.forwardRef=oo,r.isValidElement=wn,r.lazy=La,r.memo=ke,r.startTransition=Dl,r.unstable_act=Fo,r.useCallback=Lr,r.useContext=Rt,r.useDebugValue=kn,r.useDeferredValue=za,r.useEffect=Fn,r.useId=lo,r.useImperativeHandle=Qt,r.useInsertionEffect=yn,r.useLayoutEffect=Cn,r.useMemo=va,r.useReducer=Ot,r.useRef=St,r.useState=ut,r.useSyncExternalStore=wd,r.useTransition=gt,r.version=d,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}()}(fp,fp.exports)),fp.exports}var MD={};MD.NODE_ENV==="production"?fx.exports=RD():fx.exports=DD();var Ge=fx.exports;const $n=Ag(Ge);/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var JS;function $D(){if(JS)return dp;JS=1;var o=Ge,r=Symbol.for("react.element"),l=Symbol.for("react.fragment"),d=Object.prototype.hasOwnProperty,h=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,b={key:!0,ref:!0,__self:!0,__source:!0};function S(y,E,$){var M,N={},j=null,H=null;$!==void 0&&(j=""+$),E.key!==void 0&&(j=""+E.key),E.ref!==void 0&&(H=E.ref);for(M in E)d.call(E,M)&&!b.hasOwnProperty(M)&&(N[M]=E[M]);if(y&&y.defaultProps)for(M in E=y.defaultProps,E)N[M]===void 0&&(N[M]=E[M]);return{$$typeof:r,type:y,key:j,ref:H,props:N,_owner:h.current}}return dp.Fragment=l,dp.jsx=S,dp.jsxs=S,dp}var pp={},ZS;function OD(){if(ZS)return pp;ZS=1;var o={};/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */return o.NODE_ENV!=="production"&&function(){var r=Ge,l=Symbol.for("react.element"),d=Symbol.for("react.portal"),h=Symbol.for("react.fragment"),b=Symbol.for("react.strict_mode"),S=Symbol.for("react.profiler"),y=Symbol.for("react.provider"),E=Symbol.for("react.context"),$=Symbol.for("react.forward_ref"),M=Symbol.for("react.suspense"),N=Symbol.for("react.suspense_list"),j=Symbol.for("react.memo"),H=Symbol.for("react.lazy"),L=Symbol.for("react.offscreen"),K=Symbol.iterator,de="@@iterator";function Ee(P){if(P===null||typeof P!="object")return null;var fe=K&&P[K]||P[de];return typeof fe=="function"?fe:null}var pe=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;function ae(P){{for(var fe=arguments.length,ke=new Array(fe>1?fe-1:0),Me=1;Me<fe;Me++)ke[Me-1]=arguments[Me];ce("error",P,ke)}}function ce(P,fe,ke){{var Me=pe.ReactDebugCurrentFrame,Rt=Me.getStackAddendum();Rt!==""&&(fe+="%s",ke=ke.concat([Rt]));var ut=ke.map(function(Ot){return String(Ot)});ut.unshift("Warning: "+fe),Function.prototype.apply.call(console[P],console,ut)}}var Te=!1,le=!1,ue=!1,Ae=!1,ft=!1,Ve;Ve=Symbol.for("react.module.reference");function Tt(P){return!!(typeof P=="string"||typeof P=="function"||P===h||P===S||ft||P===b||P===M||P===N||Ae||P===L||Te||le||ue||typeof P=="object"&&P!==null&&(P.$$typeof===H||P.$$typeof===j||P.$$typeof===y||P.$$typeof===E||P.$$typeof===$||P.$$typeof===Ve||P.getModuleId!==void 0))}function bt(P,fe,ke){var Me=P.displayName;if(Me)return Me;var Rt=fe.displayName||fe.name||"";return Rt!==""?ke+"("+Rt+")":ke}function rt(P){return P.displayName||"Context"}function He(P){if(P==null)return null;if(typeof P.tag=="number"&&ae("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof P=="function")return P.displayName||P.name||null;if(typeof P=="string")return P;switch(P){case h:return"Fragment";case d:return"Portal";case S:return"Profiler";case b:return"StrictMode";case M:return"Suspense";case N:return"SuspenseList"}if(typeof P=="object")switch(P.$$typeof){case E:var fe=P;return rt(fe)+".Consumer";case y:var ke=P;return rt(ke._context)+".Provider";case $:return bt(P,P.render,"ForwardRef");case j:var Me=P.displayName||null;return Me!==null?Me:He(P.type)||"Memo";case H:{var Rt=P,ut=Rt._payload,Ot=Rt._init;try{return He(Ot(ut))}catch{return null}}}return null}var Ht=Object.assign,kt=0,pt,se,Re,be,V,re,We;function et(){}et.__reactDisabledLog=!0;function it(){{if(kt===0){pt=console.log,se=console.info,Re=console.warn,be=console.error,V=console.group,re=console.groupCollapsed,We=console.groupEnd;var P={configurable:!0,enumerable:!0,value:et,writable:!0};Object.defineProperties(console,{info:P,log:P,warn:P,error:P,group:P,groupCollapsed:P,groupEnd:P})}kt++}}function ht(){{if(kt--,kt===0){var P={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:Ht({},P,{value:pt}),info:Ht({},P,{value:se}),warn:Ht({},P,{value:Re}),error:Ht({},P,{value:be}),group:Ht({},P,{value:V}),groupCollapsed:Ht({},P,{value:re}),groupEnd:Ht({},P,{value:We})})}kt<0&&ae("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var $t=pe.ReactCurrentDispatcher,tt;function vt(P,fe,ke){{if(tt===void 0)try{throw Error()}catch(Rt){var Me=Rt.stack.trim().match(/\n( *(at )?)/);tt=Me&&Me[1]||""}return`
`+tt+P}}var It=!1,pn;{var an=typeof WeakMap=="function"?WeakMap:Map;pn=new an}function Pn(P,fe){if(!P||It)return"";{var ke=pn.get(P);if(ke!==void 0)return ke}var Me;It=!0;var Rt=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var ut;ut=$t.current,$t.current=null,it();try{if(fe){var Ot=function(){throw Error()};if(Object.defineProperty(Ot.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(Ot,[])}catch(kn){Me=kn}Reflect.construct(P,[],Ot)}else{try{Ot.call()}catch(kn){Me=kn}P.call(Ot.prototype)}}else{try{throw Error()}catch(kn){Me=kn}P()}}catch(kn){if(kn&&Me&&typeof kn.stack=="string"){for(var St=kn.stack.split(`
`),Fn=Me.stack.split(`
`),yn=St.length-1,Cn=Fn.length-1;yn>=1&&Cn>=0&&St[yn]!==Fn[Cn];)Cn--;for(;yn>=1&&Cn>=0;yn--,Cn--)if(St[yn]!==Fn[Cn]){if(yn!==1||Cn!==1)do if(yn--,Cn--,Cn<0||St[yn]!==Fn[Cn]){var Lr=`
`+St[yn].replace(" at new "," at ");return P.displayName&&Lr.includes("<anonymous>")&&(Lr=Lr.replace("<anonymous>",P.displayName)),typeof P=="function"&&pn.set(P,Lr),Lr}while(yn>=1&&Cn>=0);break}}}finally{It=!1,$t.current=ut,ht(),Error.prepareStackTrace=Rt}var va=P?P.displayName||P.name:"",Qt=va?vt(va):"";return typeof P=="function"&&pn.set(P,Qt),Qt}function xn(P,fe,ke){return Pn(P,!1)}function An(P){var fe=P.prototype;return!!(fe&&fe.isReactComponent)}function tr(P,fe,ke){if(P==null)return"";if(typeof P=="function")return Pn(P,An(P));if(typeof P=="string")return vt(P);switch(P){case M:return vt("Suspense");case N:return vt("SuspenseList")}if(typeof P=="object")switch(P.$$typeof){case $:return xn(P.render);case j:return tr(P.type,fe,ke);case H:{var Me=P,Rt=Me._payload,ut=Me._init;try{return tr(ut(Rt),fe,ke)}catch{}}}return""}var Gn=Object.prototype.hasOwnProperty,Ri={},ga=pe.ReactDebugCurrentFrame;function Ur(P){if(P){var fe=P._owner,ke=tr(P.type,P._source,fe?fe.type:null);ga.setExtraStackFrame(ke)}else ga.setExtraStackFrame(null)}function nr(P,fe,ke,Me,Rt){{var ut=Function.call.bind(Gn);for(var Ot in P)if(ut(P,Ot)){var St=void 0;try{if(typeof P[Ot]!="function"){var Fn=Error((Me||"React class")+": "+ke+" type `"+Ot+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof P[Ot]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw Fn.name="Invariant Violation",Fn}St=P[Ot](fe,Ot,Me,ke,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(yn){St=yn}St&&!(St instanceof Error)&&(Ur(Rt),ae("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",Me||"React class",ke,Ot,typeof St),Ur(null)),St instanceof Error&&!(St.message in Ri)&&(Ri[St.message]=!0,Ur(Rt),ae("Failed %s type: %s",ke,St.message),Ur(null))}}}var ur=Array.isArray;function cr(P){return ur(P)}function _r(P){{var fe=typeof Symbol=="function"&&Symbol.toStringTag,ke=fe&&P[Symbol.toStringTag]||P.constructor.name||"Object";return ke}}function ma(P){try{return Kn(P),!1}catch{return!0}}function Kn(P){return""+P}function xr(P){if(ma(P))return ae("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",_r(P)),Kn(P)}var oi=pe.ReactCurrentOwner,ro={key:!0,ref:!0,__self:!0,__source:!0},Di,we;function Xe(P){if(Gn.call(P,"ref")){var fe=Object.getOwnPropertyDescriptor(P,"ref").get;if(fe&&fe.isReactWarning)return!1}return P.ref!==void 0}function wt(P){if(Gn.call(P,"key")){var fe=Object.getOwnPropertyDescriptor(P,"key").get;if(fe&&fe.isReactWarning)return!1}return P.key!==void 0}function Gt(P,fe){typeof P.ref=="string"&&oi.current}function bn(P,fe){{var ke=function(){Di||(Di=!0,ae("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",fe))};ke.isReactWarning=!0,Object.defineProperty(P,"key",{get:ke,configurable:!0})}}function wn(P,fe){{var ke=function(){we||(we=!0,ae("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",fe))};ke.isReactWarning=!0,Object.defineProperty(P,"ref",{get:ke,configurable:!0})}}var Sn=function(P,fe,ke,Me,Rt,ut,Ot){var St={$$typeof:l,type:P,key:fe,ref:ke,props:Ot,_owner:ut};return St._store={},Object.defineProperty(St._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(St,"_self",{configurable:!1,enumerable:!1,writable:!1,value:Me}),Object.defineProperty(St,"_source",{configurable:!1,enumerable:!1,writable:!1,value:Rt}),Object.freeze&&(Object.freeze(St.props),Object.freeze(St)),St};function dr(P,fe,ke,Me,Rt){{var ut,Ot={},St=null,Fn=null;ke!==void 0&&(xr(ke),St=""+ke),wt(fe)&&(xr(fe.key),St=""+fe.key),Xe(fe)&&(Fn=fe.ref,Gt(fe,Rt));for(ut in fe)Gn.call(fe,ut)&&!ro.hasOwnProperty(ut)&&(Ot[ut]=fe[ut]);if(P&&P.defaultProps){var yn=P.defaultProps;for(ut in yn)Ot[ut]===void 0&&(Ot[ut]=yn[ut])}if(St||Fn){var Cn=typeof P=="function"?P.displayName||P.name||"Unknown":P;St&&bn(Ot,Cn),Fn&&wn(Ot,Cn)}return Sn(P,St,Fn,Rt,Me,oi.current,Ot)}}var vn=pe.ReactCurrentOwner,on=pe.ReactDebugCurrentFrame;function Kt(P){if(P){var fe=P._owner,ke=tr(P.type,P._source,fe?fe.type:null);on.setExtraStackFrame(ke)}else on.setExtraStackFrame(null)}var Mi;Mi=!1;function Qi(P){return typeof P=="object"&&P!==null&&P.$$typeof===l}function qi(){{if(vn.current){var P=He(vn.current.type);if(P)return`

Check the render method of \``+P+"`."}return""}}function io(P){return""}var Sl={};function Cl(P){{var fe=qi();if(!fe){var ke=typeof P=="string"?P:P.displayName||P.name;ke&&(fe=`

Check the top-level render call using <`+ke+">.")}return fe}}function ao(P,fe){{if(!P._store||P._store.validated||P.key!=null)return;P._store.validated=!0;var ke=Cl(fe);if(Sl[ke])return;Sl[ke]=!0;var Me="";P&&P._owner&&P._owner!==vn.current&&(Me=" It was passed a child from "+He(P._owner.type)+"."),Kt(P),ae('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',ke,Me),Kt(null)}}function El(P,fe){{if(typeof P!="object")return;if(cr(P))for(var ke=0;ke<P.length;ke++){var Me=P[ke];Qi(Me)&&ao(Me,fe)}else if(Qi(P))P._store&&(P._store.validated=!0);else if(P){var Rt=Ee(P);if(typeof Rt=="function"&&Rt!==P.entries)for(var ut=Rt.call(P),Ot;!(Ot=ut.next()).done;)Qi(Ot.value)&&ao(Ot.value,fe)}}}function ja(P){{var fe=P.type;if(fe==null||typeof fe=="string")return;var ke;if(typeof fe=="function")ke=fe.propTypes;else if(typeof fe=="object"&&(fe.$$typeof===$||fe.$$typeof===j))ke=fe.propTypes;else return;if(ke){var Me=He(fe);nr(ke,P.props,"prop",Me,P)}else if(fe.PropTypes!==void 0&&!Mi){Mi=!0;var Rt=He(fe);ae("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",Rt||"Unknown")}typeof fe.getDefaultProps=="function"&&!fe.getDefaultProps.isReactClassApproved&&ae("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function $i(P){{for(var fe=Object.keys(P.props),ke=0;ke<fe.length;ke++){var Me=fe[ke];if(Me!=="children"&&Me!=="key"){Kt(P),ae("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",Me),Kt(null);break}}P.ref!==null&&(Kt(P),ae("Invalid attribute `ref` supplied to `React.Fragment`."),Kt(null))}}var br={};function Oi(P,fe,ke,Me,Rt,ut){{var Ot=Tt(P);if(!Ot){var St="";(P===void 0||typeof P=="object"&&P!==null&&Object.keys(P).length===0)&&(St+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var Fn=io();Fn?St+=Fn:St+=qi();var yn;P===null?yn="null":cr(P)?yn="array":P!==void 0&&P.$$typeof===l?(yn="<"+(He(P.type)||"Unknown")+" />",St=" Did you accidentally export a JSX literal instead of a component?"):yn=typeof P,ae("React.jsx: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",yn,St)}var Cn=dr(P,fe,ke,Rt,ut);if(Cn==null)return Cn;if(Ot){var Lr=fe.children;if(Lr!==void 0)if(Me)if(cr(Lr)){for(var va=0;va<Lr.length;va++)El(Lr[va],P);Object.freeze&&Object.freeze(Lr)}else ae("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");else El(Lr,P)}if(Gn.call(fe,"key")){var Qt=He(P),kn=Object.keys(fe).filter(function(lo){return lo!=="key"}),gt=kn.length>0?"{key: someKey, "+kn.join(": ..., ")+": ...}":"{key: someKey}";if(!br[Qt+gt]){var za=kn.length>0?"{"+kn.join(": ..., ")+": ...}":"{}";ae(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,gt,Qt,za,Qt),br[Qt+gt]=!0}}return P===h?$i(Cn):ja(Cn),Cn}}function li(P,fe,ke){return Oi(P,fe,ke,!0)}function _a(P,fe,ke){return Oi(P,fe,ke,!1)}var La=_a,oo=li;pp.Fragment=h,pp.jsx=La,pp.jsxs=oo}(),pp}var AD={};AD.NODE_ENV==="production"?dx.exports=$D():dx.exports=OD();var m=dx.exports,px={exports:{}},Hi={},jg={exports:{}},hx={};/**
 * @license React
 * scheduler.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var e1;function jD(){return e1||(e1=1,function(o){function r(se,Re){var be=se.length;se.push(Re);e:for(;0<be;){var V=be-1>>>1,re=se[V];if(0<h(re,Re))se[V]=Re,se[be]=re,be=V;else break e}}function l(se){return se.length===0?null:se[0]}function d(se){if(se.length===0)return null;var Re=se[0],be=se.pop();if(be!==Re){se[0]=be;e:for(var V=0,re=se.length,We=re>>>1;V<We;){var et=2*(V+1)-1,it=se[et],ht=et+1,$t=se[ht];if(0>h(it,be))ht<re&&0>h($t,it)?(se[V]=$t,se[ht]=be,V=ht):(se[V]=it,se[et]=be,V=et);else if(ht<re&&0>h($t,be))se[V]=$t,se[ht]=be,V=ht;else break e}}return Re}function h(se,Re){var be=se.sortIndex-Re.sortIndex;return be!==0?be:se.id-Re.id}if(typeof performance=="object"&&typeof performance.now=="function"){var b=performance;o.unstable_now=function(){return b.now()}}else{var S=Date,y=S.now();o.unstable_now=function(){return S.now()-y}}var E=[],$=[],M=1,N=null,j=3,H=!1,L=!1,K=!1,de=typeof setTimeout=="function"?setTimeout:null,Ee=typeof clearTimeout=="function"?clearTimeout:null,pe=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function ae(se){for(var Re=l($);Re!==null;){if(Re.callback===null)d($);else if(Re.startTime<=se)d($),Re.sortIndex=Re.expirationTime,r(E,Re);else break;Re=l($)}}function ce(se){if(K=!1,ae(se),!L)if(l(E)!==null)L=!0,kt(Te);else{var Re=l($);Re!==null&&pt(ce,Re.startTime-se)}}function Te(se,Re){L=!1,K&&(K=!1,Ee(Ae),Ae=-1),H=!0;var be=j;try{for(ae(Re),N=l(E);N!==null&&(!(N.expirationTime>Re)||se&&!Tt());){var V=N.callback;if(typeof V=="function"){N.callback=null,j=N.priorityLevel;var re=V(N.expirationTime<=Re);Re=o.unstable_now(),typeof re=="function"?N.callback=re:N===l(E)&&d(E),ae(Re)}else d(E);N=l(E)}if(N!==null)var We=!0;else{var et=l($);et!==null&&pt(ce,et.startTime-Re),We=!1}return We}finally{N=null,j=be,H=!1}}var le=!1,ue=null,Ae=-1,ft=5,Ve=-1;function Tt(){return!(o.unstable_now()-Ve<ft)}function bt(){if(ue!==null){var se=o.unstable_now();Ve=se;var Re=!0;try{Re=ue(!0,se)}finally{Re?rt():(le=!1,ue=null)}}else le=!1}var rt;if(typeof pe=="function")rt=function(){pe(bt)};else if(typeof MessageChannel<"u"){var He=new MessageChannel,Ht=He.port2;He.port1.onmessage=bt,rt=function(){Ht.postMessage(null)}}else rt=function(){de(bt,0)};function kt(se){ue=se,le||(le=!0,rt())}function pt(se,Re){Ae=de(function(){se(o.unstable_now())},Re)}o.unstable_IdlePriority=5,o.unstable_ImmediatePriority=1,o.unstable_LowPriority=4,o.unstable_NormalPriority=3,o.unstable_Profiling=null,o.unstable_UserBlockingPriority=2,o.unstable_cancelCallback=function(se){se.callback=null},o.unstable_continueExecution=function(){L||H||(L=!0,kt(Te))},o.unstable_forceFrameRate=function(se){0>se||125<se?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):ft=0<se?Math.floor(1e3/se):5},o.unstable_getCurrentPriorityLevel=function(){return j},o.unstable_getFirstCallbackNode=function(){return l(E)},o.unstable_next=function(se){switch(j){case 1:case 2:case 3:var Re=3;break;default:Re=j}var be=j;j=Re;try{return se()}finally{j=be}},o.unstable_pauseExecution=function(){},o.unstable_requestPaint=function(){},o.unstable_runWithPriority=function(se,Re){switch(se){case 1:case 2:case 3:case 4:case 5:break;default:se=3}var be=j;j=se;try{return Re()}finally{j=be}},o.unstable_scheduleCallback=function(se,Re,be){var V=o.unstable_now();switch(typeof be=="object"&&be!==null?(be=be.delay,be=typeof be=="number"&&0<be?V+be:V):be=V,se){case 1:var re=-1;break;case 2:re=250;break;case 5:re=1073741823;break;case 4:re=1e4;break;default:re=5e3}return re=be+re,se={id:M++,callback:Re,priorityLevel:se,startTime:be,expirationTime:re,sortIndex:-1},be>V?(se.sortIndex=be,r($,se),l(E)===null&&se===l($)&&(K?(Ee(Ae),Ae=-1):K=!0,pt(ce,be-V))):(se.sortIndex=re,r(E,se),L||H||(L=!0,kt(Te))),se},o.unstable_shouldYield=Tt,o.unstable_wrapCallback=function(se){var Re=j;return function(){var be=j;j=Re;try{return se.apply(this,arguments)}finally{j=be}}}}(hx)),hx}var gx={},t1;function _D(){return t1||(t1=1,function(o){var r={};/**
 * @license React
 * scheduler.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */r.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var l=!1,d=5;function h(we,Xe){var wt=we.length;we.push(Xe),y(we,Xe,wt)}function b(we){return we.length===0?null:we[0]}function S(we){if(we.length===0)return null;var Xe=we[0],wt=we.pop();return wt!==Xe&&(we[0]=wt,E(we,wt,0)),Xe}function y(we,Xe,wt){for(var Gt=wt;Gt>0;){var bn=Gt-1>>>1,wn=we[bn];if($(wn,Xe)>0)we[bn]=Xe,we[Gt]=wn,Gt=bn;else return}}function E(we,Xe,wt){for(var Gt=wt,bn=we.length,wn=bn>>>1;Gt<wn;){var Sn=(Gt+1)*2-1,dr=we[Sn],vn=Sn+1,on=we[vn];if($(dr,Xe)<0)vn<bn&&$(on,dr)<0?(we[Gt]=on,we[vn]=Xe,Gt=vn):(we[Gt]=dr,we[Sn]=Xe,Gt=Sn);else if(vn<bn&&$(on,Xe)<0)we[Gt]=on,we[vn]=Xe,Gt=vn;else return}}function $(we,Xe){var wt=we.sortIndex-Xe.sortIndex;return wt!==0?wt:we.id-Xe.id}var M=1,N=2,j=3,H=4,L=5;function K(we,Xe){}var de=typeof performance=="object"&&typeof performance.now=="function";if(de){var Ee=performance;o.unstable_now=function(){return Ee.now()}}else{var pe=Date,ae=pe.now();o.unstable_now=function(){return pe.now()-ae}}var ce=1073741823,Te=-1,le=250,ue=5e3,Ae=1e4,ft=ce,Ve=[],Tt=[],bt=1,rt=null,He=j,Ht=!1,kt=!1,pt=!1,se=typeof setTimeout=="function"?setTimeout:null,Re=typeof clearTimeout=="function"?clearTimeout:null,be=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function V(we){for(var Xe=b(Tt);Xe!==null;){if(Xe.callback===null)S(Tt);else if(Xe.startTime<=we)S(Tt),Xe.sortIndex=Xe.expirationTime,h(Ve,Xe);else return;Xe=b(Tt)}}function re(we){if(pt=!1,V(we),!kt)if(b(Ve)!==null)kt=!0,Kn(We);else{var Xe=b(Tt);Xe!==null&&xr(re,Xe.startTime-we)}}function We(we,Xe){kt=!1,pt&&(pt=!1,oi()),Ht=!0;var wt=He;try{var Gt;if(!l)return et(we,Xe)}finally{rt=null,He=wt,Ht=!1}}function et(we,Xe){var wt=Xe;for(V(wt),rt=b(Ve);rt!==null&&!(rt.expirationTime>wt&&(!we||ga()));){var Gt=rt.callback;if(typeof Gt=="function"){rt.callback=null,He=rt.priorityLevel;var bn=rt.expirationTime<=wt,wn=Gt(bn);wt=o.unstable_now(),typeof wn=="function"?rt.callback=wn:rt===b(Ve)&&S(Ve),V(wt)}else S(Ve);rt=b(Ve)}if(rt!==null)return!0;var Sn=b(Tt);return Sn!==null&&xr(re,Sn.startTime-wt),!1}function it(we,Xe){switch(we){case M:case N:case j:case H:case L:break;default:we=j}var wt=He;He=we;try{return Xe()}finally{He=wt}}function ht(we){var Xe;switch(He){case M:case N:case j:Xe=j;break;default:Xe=He;break}var wt=He;He=Xe;try{return we()}finally{He=wt}}function $t(we){var Xe=He;return function(){var wt=He;He=Xe;try{return we.apply(this,arguments)}finally{He=wt}}}function tt(we,Xe,wt){var Gt=o.unstable_now(),bn;if(typeof wt=="object"&&wt!==null){var wn=wt.delay;typeof wn=="number"&&wn>0?bn=Gt+wn:bn=Gt}else bn=Gt;var Sn;switch(we){case M:Sn=Te;break;case N:Sn=le;break;case L:Sn=ft;break;case H:Sn=Ae;break;case j:default:Sn=ue;break}var dr=bn+Sn,vn={id:bt++,callback:Xe,priorityLevel:we,startTime:bn,expirationTime:dr,sortIndex:-1};return bn>Gt?(vn.sortIndex=bn,h(Tt,vn),b(Ve)===null&&vn===b(Tt)&&(pt?oi():pt=!0,xr(re,bn-Gt))):(vn.sortIndex=dr,h(Ve,vn),!kt&&!Ht&&(kt=!0,Kn(We))),vn}function vt(){}function It(){!kt&&!Ht&&(kt=!0,Kn(We))}function pn(){return b(Ve)}function an(we){we.callback=null}function Pn(){return He}var xn=!1,An=null,tr=-1,Gn=d,Ri=-1;function ga(){var we=o.unstable_now()-Ri;return!(we<Gn)}function Ur(){}function nr(we){if(we<0||we>125){console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported");return}we>0?Gn=Math.floor(1e3/we):Gn=d}var ur=function(){if(An!==null){var we=o.unstable_now();Ri=we;var Xe=!0,wt=!0;try{wt=An(Xe,we)}finally{wt?cr():(xn=!1,An=null)}}else xn=!1},cr;if(typeof be=="function")cr=function(){be(ur)};else if(typeof MessageChannel<"u"){var _r=new MessageChannel,ma=_r.port2;_r.port1.onmessage=ur,cr=function(){ma.postMessage(null)}}else cr=function(){se(ur,0)};function Kn(we){An=we,xn||(xn=!0,cr())}function xr(we,Xe){tr=se(function(){we(o.unstable_now())},Xe)}function oi(){Re(tr),tr=-1}var ro=Ur,Di=null;o.unstable_IdlePriority=L,o.unstable_ImmediatePriority=M,o.unstable_LowPriority=H,o.unstable_NormalPriority=j,o.unstable_Profiling=Di,o.unstable_UserBlockingPriority=N,o.unstable_cancelCallback=an,o.unstable_continueExecution=It,o.unstable_forceFrameRate=nr,o.unstable_getCurrentPriorityLevel=Pn,o.unstable_getFirstCallbackNode=pn,o.unstable_next=ht,o.unstable_pauseExecution=vt,o.unstable_requestPaint=ro,o.unstable_runWithPriority=it,o.unstable_scheduleCallback=tt,o.unstable_shouldYield=ga,o.unstable_wrapCallback=$t,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}()}(gx)),gx}var n1;function r1(){if(n1)return jg.exports;n1=1;var o={};return o.NODE_ENV==="production"?jg.exports=jD():jg.exports=_D(),jg.exports}/**
 * @license React
 * react-dom.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var i1;function LD(){if(i1)return Hi;i1=1;var o=Ge,r=r1();function l(n){for(var i="https://reactjs.org/docs/error-decoder.html?invariant="+n,u=1;u<arguments.length;u++)i+="&args[]="+encodeURIComponent(arguments[u]);return"Minified React error #"+n+"; visit "+i+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var d=new Set,h={};function b(n,i){S(n,i),S(n+"Capture",i)}function S(n,i){for(h[n]=i,n=0;n<i.length;n++)d.add(i[n])}var y=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),E=Object.prototype.hasOwnProperty,$=/^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,M={},N={};function j(n){return E.call(N,n)?!0:E.call(M,n)?!1:$.test(n)?N[n]=!0:(M[n]=!0,!1)}function H(n,i,u,f){if(u!==null&&u.type===0)return!1;switch(typeof i){case"function":case"symbol":return!0;case"boolean":return f?!1:u!==null?!u.acceptsBooleans:(n=n.toLowerCase().slice(0,5),n!=="data-"&&n!=="aria-");default:return!1}}function L(n,i,u,f){if(i===null||typeof i>"u"||H(n,i,u,f))return!0;if(f)return!1;if(u!==null)switch(u.type){case 3:return!i;case 4:return i===!1;case 5:return isNaN(i);case 6:return isNaN(i)||1>i}return!1}function K(n,i,u,f,g,x,k){this.acceptsBooleans=i===2||i===3||i===4,this.attributeName=f,this.attributeNamespace=g,this.mustUseProperty=u,this.propertyName=n,this.type=i,this.sanitizeURL=x,this.removeEmptyString=k}var de={};"children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(n){de[n]=new K(n,0,!1,n,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(n){var i=n[0];de[i]=new K(i,1,!1,n[1],null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(n){de[n]=new K(n,2,!1,n.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(n){de[n]=new K(n,2,!1,n,null,!1,!1)}),"allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(n){de[n]=new K(n,3,!1,n.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(n){de[n]=new K(n,3,!0,n,null,!1,!1)}),["capture","download"].forEach(function(n){de[n]=new K(n,4,!1,n,null,!1,!1)}),["cols","rows","size","span"].forEach(function(n){de[n]=new K(n,6,!1,n,null,!1,!1)}),["rowSpan","start"].forEach(function(n){de[n]=new K(n,5,!1,n.toLowerCase(),null,!1,!1)});var Ee=/[\-:]([a-z])/g;function pe(n){return n[1].toUpperCase()}"accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(n){var i=n.replace(Ee,pe);de[i]=new K(i,1,!1,n,null,!1,!1)}),"xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(n){var i=n.replace(Ee,pe);de[i]=new K(i,1,!1,n,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(n){var i=n.replace(Ee,pe);de[i]=new K(i,1,!1,n,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(n){de[n]=new K(n,1,!1,n.toLowerCase(),null,!1,!1)}),de.xlinkHref=new K("xlinkHref",1,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(n){de[n]=new K(n,1,!1,n.toLowerCase(),null,!0,!0)});function ae(n,i,u,f){var g=de.hasOwnProperty(i)?de[i]:null;(g!==null?g.type!==0:f||!(2<i.length)||i[0]!=="o"&&i[0]!=="O"||i[1]!=="n"&&i[1]!=="N")&&(L(i,u,g,f)&&(u=null),f||g===null?j(i)&&(u===null?n.removeAttribute(i):n.setAttribute(i,""+u)):g.mustUseProperty?n[g.propertyName]=u===null?g.type===3?!1:"":u:(i=g.attributeName,f=g.attributeNamespace,u===null?n.removeAttribute(i):(g=g.type,u=g===3||g===4&&u===!0?"":""+u,f?n.setAttributeNS(f,i,u):n.setAttribute(i,u))))}var ce=o.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,Te=Symbol.for("react.element"),le=Symbol.for("react.portal"),ue=Symbol.for("react.fragment"),Ae=Symbol.for("react.strict_mode"),ft=Symbol.for("react.profiler"),Ve=Symbol.for("react.provider"),Tt=Symbol.for("react.context"),bt=Symbol.for("react.forward_ref"),rt=Symbol.for("react.suspense"),He=Symbol.for("react.suspense_list"),Ht=Symbol.for("react.memo"),kt=Symbol.for("react.lazy"),pt=Symbol.for("react.offscreen"),se=Symbol.iterator;function Re(n){return n===null||typeof n!="object"?null:(n=se&&n[se]||n["@@iterator"],typeof n=="function"?n:null)}var be=Object.assign,V;function re(n){if(V===void 0)try{throw Error()}catch(u){var i=u.stack.trim().match(/\n( *(at )?)/);V=i&&i[1]||""}return`
`+V+n}var We=!1;function et(n,i){if(!n||We)return"";We=!0;var u=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{if(i)if(i=function(){throw Error()},Object.defineProperty(i.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(i,[])}catch(J){var f=J}Reflect.construct(n,[],i)}else{try{i.call()}catch(J){f=J}n.call(i.prototype)}else{try{throw Error()}catch(J){f=J}n()}}catch(J){if(J&&f&&typeof J.stack=="string"){for(var g=J.stack.split(`
`),x=f.stack.split(`
`),k=g.length-1,_=x.length-1;1<=k&&0<=_&&g[k]!==x[_];)_--;for(;1<=k&&0<=_;k--,_--)if(g[k]!==x[_]){if(k!==1||_!==1)do if(k--,_--,0>_||g[k]!==x[_]){var F=`
`+g[k].replace(" at new "," at ");return n.displayName&&F.includes("<anonymous>")&&(F=F.replace("<anonymous>",n.displayName)),F}while(1<=k&&0<=_);break}}}finally{We=!1,Error.prepareStackTrace=u}return(n=n?n.displayName||n.name:"")?re(n):""}function it(n){switch(n.tag){case 5:return re(n.type);case 16:return re("Lazy");case 13:return re("Suspense");case 19:return re("SuspenseList");case 0:case 2:case 15:return n=et(n.type,!1),n;case 11:return n=et(n.type.render,!1),n;case 1:return n=et(n.type,!0),n;default:return""}}function ht(n){if(n==null)return null;if(typeof n=="function")return n.displayName||n.name||null;if(typeof n=="string")return n;switch(n){case ue:return"Fragment";case le:return"Portal";case ft:return"Profiler";case Ae:return"StrictMode";case rt:return"Suspense";case He:return"SuspenseList"}if(typeof n=="object")switch(n.$$typeof){case Tt:return(n.displayName||"Context")+".Consumer";case Ve:return(n._context.displayName||"Context")+".Provider";case bt:var i=n.render;return n=n.displayName,n||(n=i.displayName||i.name||"",n=n!==""?"ForwardRef("+n+")":"ForwardRef"),n;case Ht:return i=n.displayName||null,i!==null?i:ht(n.type)||"Memo";case kt:i=n._payload,n=n._init;try{return ht(n(i))}catch{}}return null}function $t(n){var i=n.type;switch(n.tag){case 24:return"Cache";case 9:return(i.displayName||"Context")+".Consumer";case 10:return(i._context.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return n=i.render,n=n.displayName||n.name||"",i.displayName||(n!==""?"ForwardRef("+n+")":"ForwardRef");case 7:return"Fragment";case 5:return i;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return ht(i);case 8:return i===Ae?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 17:case 2:case 14:case 15:if(typeof i=="function")return i.displayName||i.name||null;if(typeof i=="string")return i}return null}function tt(n){switch(typeof n){case"boolean":case"number":case"string":case"undefined":return n;case"object":return n;default:return""}}function vt(n){var i=n.type;return(n=n.nodeName)&&n.toLowerCase()==="input"&&(i==="checkbox"||i==="radio")}function It(n){var i=vt(n)?"checked":"value",u=Object.getOwnPropertyDescriptor(n.constructor.prototype,i),f=""+n[i];if(!n.hasOwnProperty(i)&&typeof u<"u"&&typeof u.get=="function"&&typeof u.set=="function"){var g=u.get,x=u.set;return Object.defineProperty(n,i,{configurable:!0,get:function(){return g.call(this)},set:function(k){f=""+k,x.call(this,k)}}),Object.defineProperty(n,i,{enumerable:u.enumerable}),{getValue:function(){return f},setValue:function(k){f=""+k},stopTracking:function(){n._valueTracker=null,delete n[i]}}}}function pn(n){n._valueTracker||(n._valueTracker=It(n))}function an(n){if(!n)return!1;var i=n._valueTracker;if(!i)return!0;var u=i.getValue(),f="";return n&&(f=vt(n)?n.checked?"true":"false":n.value),n=f,n!==u?(i.setValue(n),!0):!1}function Pn(n){if(n=n||(typeof document<"u"?document:void 0),typeof n>"u")return null;try{return n.activeElement||n.body}catch{return n.body}}function xn(n,i){var u=i.checked;return be({},i,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:u??n._wrapperState.initialChecked})}function An(n,i){var u=i.defaultValue==null?"":i.defaultValue,f=i.checked!=null?i.checked:i.defaultChecked;u=tt(i.value!=null?i.value:u),n._wrapperState={initialChecked:f,initialValue:u,controlled:i.type==="checkbox"||i.type==="radio"?i.checked!=null:i.value!=null}}function tr(n,i){i=i.checked,i!=null&&ae(n,"checked",i,!1)}function Gn(n,i){tr(n,i);var u=tt(i.value),f=i.type;if(u!=null)f==="number"?(u===0&&n.value===""||n.value!=u)&&(n.value=""+u):n.value!==""+u&&(n.value=""+u);else if(f==="submit"||f==="reset"){n.removeAttribute("value");return}i.hasOwnProperty("value")?ga(n,i.type,u):i.hasOwnProperty("defaultValue")&&ga(n,i.type,tt(i.defaultValue)),i.checked==null&&i.defaultChecked!=null&&(n.defaultChecked=!!i.defaultChecked)}function Ri(n,i,u){if(i.hasOwnProperty("value")||i.hasOwnProperty("defaultValue")){var f=i.type;if(!(f!=="submit"&&f!=="reset"||i.value!==void 0&&i.value!==null))return;i=""+n._wrapperState.initialValue,u||i===n.value||(n.value=i),n.defaultValue=i}u=n.name,u!==""&&(n.name=""),n.defaultChecked=!!n._wrapperState.initialChecked,u!==""&&(n.name=u)}function ga(n,i,u){(i!=="number"||Pn(n.ownerDocument)!==n)&&(u==null?n.defaultValue=""+n._wrapperState.initialValue:n.defaultValue!==""+u&&(n.defaultValue=""+u))}var Ur=Array.isArray;function nr(n,i,u,f){if(n=n.options,i){i={};for(var g=0;g<u.length;g++)i["$"+u[g]]=!0;for(u=0;u<n.length;u++)g=i.hasOwnProperty("$"+n[u].value),n[u].selected!==g&&(n[u].selected=g),g&&f&&(n[u].defaultSelected=!0)}else{for(u=""+tt(u),i=null,g=0;g<n.length;g++){if(n[g].value===u){n[g].selected=!0,f&&(n[g].defaultSelected=!0);return}i!==null||n[g].disabled||(i=n[g])}i!==null&&(i.selected=!0)}}function ur(n,i){if(i.dangerouslySetInnerHTML!=null)throw Error(l(91));return be({},i,{value:void 0,defaultValue:void 0,children:""+n._wrapperState.initialValue})}function cr(n,i){var u=i.value;if(u==null){if(u=i.children,i=i.defaultValue,u!=null){if(i!=null)throw Error(l(92));if(Ur(u)){if(1<u.length)throw Error(l(93));u=u[0]}i=u}i==null&&(i=""),u=i}n._wrapperState={initialValue:tt(u)}}function _r(n,i){var u=tt(i.value),f=tt(i.defaultValue);u!=null&&(u=""+u,u!==n.value&&(n.value=u),i.defaultValue==null&&n.defaultValue!==u&&(n.defaultValue=u)),f!=null&&(n.defaultValue=""+f)}function ma(n){var i=n.textContent;i===n._wrapperState.initialValue&&i!==""&&i!==null&&(n.value=i)}function Kn(n){switch(n){case"svg":return"http://www.w3.org/2000/svg";case"math":return"http://www.w3.org/1998/Math/MathML";default:return"http://www.w3.org/1999/xhtml"}}function xr(n,i){return n==null||n==="http://www.w3.org/1999/xhtml"?Kn(i):n==="http://www.w3.org/2000/svg"&&i==="foreignObject"?"http://www.w3.org/1999/xhtml":n}var oi,ro=function(n){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(i,u,f,g){MSApp.execUnsafeLocalFunction(function(){return n(i,u,f,g)})}:n}(function(n,i){if(n.namespaceURI!=="http://www.w3.org/2000/svg"||"innerHTML"in n)n.innerHTML=i;else{for(oi=oi||document.createElement("div"),oi.innerHTML="<svg>"+i.valueOf().toString()+"</svg>",i=oi.firstChild;n.firstChild;)n.removeChild(n.firstChild);for(;i.firstChild;)n.appendChild(i.firstChild)}});function Di(n,i){if(i){var u=n.firstChild;if(u&&u===n.lastChild&&u.nodeType===3){u.nodeValue=i;return}}n.textContent=i}var we={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0},Xe=["Webkit","ms","Moz","O"];Object.keys(we).forEach(function(n){Xe.forEach(function(i){i=i+n.charAt(0).toUpperCase()+n.substring(1),we[i]=we[n]})});function wt(n,i,u){return i==null||typeof i=="boolean"||i===""?"":u||typeof i!="number"||i===0||we.hasOwnProperty(n)&&we[n]?(""+i).trim():i+"px"}function Gt(n,i){n=n.style;for(var u in i)if(i.hasOwnProperty(u)){var f=u.indexOf("--")===0,g=wt(u,i[u],f);u==="float"&&(u="cssFloat"),f?n.setProperty(u,g):n[u]=g}}var bn=be({menuitem:!0},{area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0});function wn(n,i){if(i){if(bn[n]&&(i.children!=null||i.dangerouslySetInnerHTML!=null))throw Error(l(137,n));if(i.dangerouslySetInnerHTML!=null){if(i.children!=null)throw Error(l(60));if(typeof i.dangerouslySetInnerHTML!="object"||!("__html"in i.dangerouslySetInnerHTML))throw Error(l(61))}if(i.style!=null&&typeof i.style!="object")throw Error(l(62))}}function Sn(n,i){if(n.indexOf("-")===-1)return typeof i.is=="string";switch(n){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var dr=null;function vn(n){return n=n.target||n.srcElement||window,n.correspondingUseElement&&(n=n.correspondingUseElement),n.nodeType===3?n.parentNode:n}var on=null,Kt=null,Mi=null;function Qi(n){if(n=lc(n)){if(typeof on!="function")throw Error(l(280));var i=n.stateNode;i&&(i=yo(i),on(n.stateNode,n.type,i))}}function qi(n){Kt?Mi?Mi.push(n):Mi=[n]:Kt=n}function io(){if(Kt){var n=Kt,i=Mi;if(Mi=Kt=null,Qi(n),i)for(n=0;n<i.length;n++)Qi(i[n])}}function Sl(n,i){return n(i)}function Cl(){}var ao=!1;function El(n,i,u){if(ao)return n(i,u);ao=!0;try{return Sl(n,i,u)}finally{ao=!1,(Kt!==null||Mi!==null)&&(Cl(),io())}}function ja(n,i){var u=n.stateNode;if(u===null)return null;var f=yo(u);if(f===null)return null;u=f[i];e:switch(i){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(f=!f.disabled)||(n=n.type,f=!(n==="button"||n==="input"||n==="select"||n==="textarea")),n=!f;break e;default:n=!1}if(n)return null;if(u&&typeof u!="function")throw Error(l(231,i,typeof u));return u}var $i=!1;if(y)try{var br={};Object.defineProperty(br,"passive",{get:function(){$i=!0}}),window.addEventListener("test",br,br),window.removeEventListener("test",br,br)}catch{$i=!1}function Oi(n,i,u,f,g,x,k,_,F){var J=Array.prototype.slice.call(arguments,3);try{i.apply(u,J)}catch(ge){this.onError(ge)}}var li=!1,_a=null,La=!1,oo=null,P={onError:function(n){li=!0,_a=n}};function fe(n,i,u,f,g,x,k,_,F){li=!1,_a=null,Oi.apply(P,arguments)}function ke(n,i,u,f,g,x,k,_,F){if(fe.apply(this,arguments),li){if(li){var J=_a;li=!1,_a=null}else throw Error(l(198));La||(La=!0,oo=J)}}function Me(n){var i=n,u=n;if(n.alternate)for(;i.return;)i=i.return;else{n=i;do i=n,i.flags&4098&&(u=i.return),n=i.return;while(n)}return i.tag===3?u:null}function Rt(n){if(n.tag===13){var i=n.memoizedState;if(i===null&&(n=n.alternate,n!==null&&(i=n.memoizedState)),i!==null)return i.dehydrated}return null}function ut(n){if(Me(n)!==n)throw Error(l(188))}function Ot(n){var i=n.alternate;if(!i){if(i=Me(n),i===null)throw Error(l(188));return i!==n?null:n}for(var u=n,f=i;;){var g=u.return;if(g===null)break;var x=g.alternate;if(x===null){if(f=g.return,f!==null){u=f;continue}break}if(g.child===x.child){for(x=g.child;x;){if(x===u)return ut(g),n;if(x===f)return ut(g),i;x=x.sibling}throw Error(l(188))}if(u.return!==f.return)u=g,f=x;else{for(var k=!1,_=g.child;_;){if(_===u){k=!0,u=g,f=x;break}if(_===f){k=!0,f=g,u=x;break}_=_.sibling}if(!k){for(_=x.child;_;){if(_===u){k=!0,u=x,f=g;break}if(_===f){k=!0,f=x,u=g;break}_=_.sibling}if(!k)throw Error(l(189))}}if(u.alternate!==f)throw Error(l(190))}if(u.tag!==3)throw Error(l(188));return u.stateNode.current===u?n:i}function St(n){return n=Ot(n),n!==null?Fn(n):null}function Fn(n){if(n.tag===5||n.tag===6)return n;for(n=n.child;n!==null;){var i=Fn(n);if(i!==null)return i;n=n.sibling}return null}var yn=r.unstable_scheduleCallback,Cn=r.unstable_cancelCallback,Lr=r.unstable_shouldYield,va=r.unstable_requestPaint,Qt=r.unstable_now,kn=r.unstable_getCurrentPriorityLevel,gt=r.unstable_ImmediatePriority,za=r.unstable_UserBlockingPriority,lo=r.unstable_NormalPriority,wd=r.unstable_LowPriority,so=r.unstable_IdlePriority,Lo=null,si=null;function Bu(n){if(si&&typeof si.onCommitFiberRoot=="function")try{si.onCommitFiberRoot(Lo,n,void 0,(n.current.flags&128)===128)}catch{}}var Hr=Math.clz32?Math.clz32:Cd,Iu=Math.log,Sd=Math.LN2;function Cd(n){return n>>>=0,n===0?32:31-(Iu(n)/Sd|0)|0}var uo=64,zo=4194304;function ui(n){switch(n&-n){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return n&4194240;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return n&130023424;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 1073741824;default:return n}}function Na(n,i){var u=n.pendingLanes;if(u===0)return 0;var f=0,g=n.suspendedLanes,x=n.pingedLanes,k=u&268435455;if(k!==0){var _=k&~g;_!==0?f=ui(_):(x&=k,x!==0&&(f=ui(x)))}else k=u&~g,k!==0?f=ui(k):x!==0&&(f=ui(x));if(f===0)return 0;if(i!==0&&i!==f&&!(i&g)&&(g=f&-f,x=i&-i,g>=x||g===16&&(x&4194240)!==0))return i;if(f&4&&(f|=u&16),i=n.entangledLanes,i!==0)for(n=n.entanglements,i&=f;0<i;)u=31-Hr(i),g=1<<u,f|=n[u],i&=~g;return f}function No(n,i){switch(n){case 1:case 2:case 4:return i+250;case 8:case 16:case 32:case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return i+5e3;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return-1;case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function Es(n,i){for(var u=n.suspendedLanes,f=n.pingedLanes,g=n.expirationTimes,x=n.pendingLanes;0<x;){var k=31-Hr(x),_=1<<k,F=g[k];F===-1?(!(_&u)||_&f)&&(g[k]=No(_,i)):F<=i&&(n.expiredLanes|=_),x&=~_}}function co(n){return n=n.pendingLanes&-1073741825,n!==0?n:n&1073741824?1073741824:0}function Tl(){var n=uo;return uo<<=1,!(uo&4194240)&&(uo=64),n}function kl(n){for(var i=[],u=0;31>u;u++)i.push(n);return i}function Po(n,i,u){n.pendingLanes|=i,i!==536870912&&(n.suspendedLanes=0,n.pingedLanes=0),n=n.eventTimes,i=31-Hr(i),n[i]=u}function Uu(n,i){var u=n.pendingLanes&~i;n.pendingLanes=i,n.suspendedLanes=0,n.pingedLanes=0,n.expiredLanes&=i,n.mutableReadLanes&=i,n.entangledLanes&=i,i=n.entanglements;var f=n.eventTimes;for(n=n.expirationTimes;0<u;){var g=31-Hr(u),x=1<<g;i[g]=0,f[g]=-1,n[g]=-1,u&=~x}}function Hu(n,i){var u=n.entangledLanes|=i;for(n=n.entanglements;u;){var f=31-Hr(u),g=1<<f;g&i|n[f]&i&&(n[f]|=i),u&=~g}}var Nt=0;function Vu(n){return n&=-n,1<n?4<n?n&268435455?16:536870912:4:1}var Ts,Pt,Ed,Pa,lt,Rl=!1,fr=[],ci=null,Vr=null,Fa=null,_n=new Map,ln=new Map,ya=[],Xi="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");function zr(n,i){switch(n){case"focusin":case"focusout":ci=null;break;case"dragenter":case"dragleave":Vr=null;break;case"mouseover":case"mouseout":Fa=null;break;case"pointerover":case"pointerout":_n.delete(i.pointerId);break;case"gotpointercapture":case"lostpointercapture":ln.delete(i.pointerId)}}function Wr(n,i,u,f,g,x){return n===null||n.nativeEvent!==x?(n={blockedOn:i,domEventName:u,eventSystemFlags:f,nativeEvent:x,targetContainers:[g]},i!==null&&(i=lc(i),i!==null&&Pt(i)),n):(n.eventSystemFlags|=f,i=n.targetContainers,g!==null&&i.indexOf(g)===-1&&i.push(g),n)}function Op(n,i,u,f,g){switch(i){case"focusin":return ci=Wr(ci,n,i,u,f,g),!0;case"dragenter":return Vr=Wr(Vr,n,i,u,f,g),!0;case"mouseover":return Fa=Wr(Fa,n,i,u,f,g),!0;case"pointerover":var x=g.pointerId;return _n.set(x,Wr(_n.get(x)||null,n,i,u,f,g)),!0;case"gotpointercapture":return x=g.pointerId,ln.set(x,Wr(ln.get(x)||null,n,i,u,f,g)),!0}return!1}function ks(n){var i=zl(n.target);if(i!==null){var u=Me(i);if(u!==null){if(i=u.tag,i===13){if(i=Rt(u),i!==null){n.blockedOn=i,lt(n.priority,function(){Ed(u)});return}}else if(i===3&&u.stateNode.current.memoizedState.isDehydrated){n.blockedOn=u.tag===3?u.stateNode.containerInfo:null;return}}}n.blockedOn=null}function Dl(n){if(n.blockedOn!==null)return!1;for(var i=n.targetContainers;0<i.length;){var u=$s(n.domEventName,n.eventSystemFlags,i[0],n.nativeEvent);if(u===null){u=n.nativeEvent;var f=new u.constructor(u.type,u);dr=f,u.target.dispatchEvent(f),dr=null}else return i=lc(u),i!==null&&Pt(i),n.blockedOn=u,!1;i.shift()}return!0}function Rs(n,i,u){Dl(n)&&u.delete(i)}function Ds(){Rl=!1,ci!==null&&Dl(ci)&&(ci=null),Vr!==null&&Dl(Vr)&&(Vr=null),Fa!==null&&Dl(Fa)&&(Fa=null),_n.forEach(Rs),ln.forEach(Rs)}function Ml(n,i){n.blockedOn===i&&(n.blockedOn=null,Rl||(Rl=!0,r.unstable_scheduleCallback(r.unstable_NormalPriority,Ds)))}function Ji(n){function i(g){return Ml(g,n)}if(0<fr.length){Ml(fr[0],n);for(var u=1;u<fr.length;u++){var f=fr[u];f.blockedOn===n&&(f.blockedOn=null)}}for(ci!==null&&Ml(ci,n),Vr!==null&&Ml(Vr,n),Fa!==null&&Ml(Fa,n),_n.forEach(i),ln.forEach(i),u=0;u<ya.length;u++)f=ya[u],f.blockedOn===n&&(f.blockedOn=null);for(;0<ya.length&&(u=ya[0],u.blockedOn===null);)ks(u),u.blockedOn===null&&ya.shift()}var Zi=ce.ReactCurrentBatchConfig,Fo=!0;function fo(n,i,u,f){var g=Nt,x=Zi.transition;Zi.transition=null;try{Nt=1,Bo(n,i,u,f)}finally{Nt=g,Zi.transition=x}}function Ms(n,i,u,f){var g=Nt,x=Zi.transition;Zi.transition=null;try{Nt=4,Bo(n,i,u,f)}finally{Nt=g,Zi.transition=x}}function Bo(n,i,u,f){if(Fo){var g=$s(n,i,u,f);if(g===null)Vp(n,i,f,po,u),zr(n,f);else if(Op(g,n,i,u,f))f.stopPropagation();else if(zr(n,f),i&4&&-1<Xi.indexOf(n)){for(;g!==null;){var x=lc(g);if(x!==null&&Ts(x),x=$s(n,i,u,f),x===null&&Vp(n,i,f,po,u),x===g)break;g=x}g!==null&&f.stopPropagation()}else Vp(n,i,f,null,u)}}var po=null;function $s(n,i,u,f){if(po=null,n=vn(f),n=zl(n),n!==null)if(i=Me(n),i===null)n=null;else if(u=i.tag,u===13){if(n=Rt(i),n!==null)return n;n=null}else if(u===3){if(i.stateNode.current.memoizedState.isDehydrated)return i.tag===3?i.stateNode.containerInfo:null;n=null}else i!==n&&(n=null);return po=n,null}function Wu(n){switch(n){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return 1;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return 4;case"message":switch(kn()){case gt:return 1;case za:return 4;case lo:case wd:return 16;case so:return 536870912;default:return 16}default:return 16}}var ea=null,Os=null,T=null;function z(){if(T)return T;var n,i=Os,u=i.length,f,g="value"in ea?ea.value:ea.textContent,x=g.length;for(n=0;n<u&&i[n]===g[n];n++);var k=u-n;for(f=1;f<=k&&i[u-f]===g[x-f];f++);return T=g.slice(n,1<f?1-f:void 0)}function q(n){var i=n.keyCode;return"charCode"in n?(n=n.charCode,n===0&&i===13&&(n=13)):n=i,n===10&&(n=13),32<=n||n===13?n:0}function ee(){return!0}function xe(){return!1}function Pe(n){function i(u,f,g,x,k){this._reactName=u,this._targetInst=g,this.type=f,this.nativeEvent=x,this.target=k,this.currentTarget=null;for(var _ in n)n.hasOwnProperty(_)&&(u=n[_],this[_]=u?u(x):x[_]);return this.isDefaultPrevented=(x.defaultPrevented!=null?x.defaultPrevented:x.returnValue===!1)?ee:xe,this.isPropagationStopped=xe,this}return be(i.prototype,{preventDefault:function(){this.defaultPrevented=!0;var u=this.nativeEvent;u&&(u.preventDefault?u.preventDefault():typeof u.returnValue!="unknown"&&(u.returnValue=!1),this.isDefaultPrevented=ee)},stopPropagation:function(){var u=this.nativeEvent;u&&(u.stopPropagation?u.stopPropagation():typeof u.cancelBubble!="unknown"&&(u.cancelBubble=!0),this.isPropagationStopped=ee)},persist:function(){},isPersistent:ee}),i}var je={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(n){return n.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},at=Pe(je),Ct=be({},je,{view:0,detail:0}),qt=Pe(Ct),sn,un,yt,hn=be({},Ct,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:xa,button:0,buttons:0,relatedTarget:function(n){return n.relatedTarget===void 0?n.fromElement===n.srcElement?n.toElement:n.fromElement:n.relatedTarget},movementX:function(n){return"movementX"in n?n.movementX:(n!==yt&&(yt&&n.type==="mousemove"?(sn=n.screenX-yt.screenX,un=n.screenY-yt.screenY):un=sn=0,yt=n),sn)},movementY:function(n){return"movementY"in n?n.movementY:un}}),Bn=Pe(hn),$l=be({},hn,{dataTransfer:0}),Yu=Pe($l),ho=be({},Ct,{relatedTarget:0}),Ol=Pe(ho),Gu=be({},je,{animationName:0,elapsedTime:0,pseudoElement:0}),Ap=Pe(Gu),Td=be({},je,{clipboardData:function(n){return"clipboardData"in n?n.clipboardData:window.clipboardData}}),jp=Pe(Td),bm=be({},je,{data:0}),kd=Pe(bm),wm={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},Sm={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},Cm={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function A0(n){var i=this.nativeEvent;return i.getModifierState?i.getModifierState(n):(n=Cm[n])?!!i[n]:!1}function xa(){return A0}var j0=be({},Ct,{key:function(n){if(n.key){var i=wm[n.key]||n.key;if(i!=="Unidentified")return i}return n.type==="keypress"?(n=q(n),n===13?"Enter":String.fromCharCode(n)):n.type==="keydown"||n.type==="keyup"?Sm[n.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:xa,charCode:function(n){return n.type==="keypress"?q(n):0},keyCode:function(n){return n.type==="keydown"||n.type==="keyup"?n.keyCode:0},which:function(n){return n.type==="keypress"?q(n):n.type==="keydown"||n.type==="keyup"?n.keyCode:0}}),_p=Pe(j0),Lp=be({},hn,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),Rd=Pe(Lp),_0=be({},Ct,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:xa}),Dd=Pe(_0),Em=be({},je,{propertyName:0,elapsedTime:0,pseudoElement:0}),di=Pe(Em),go=be({},hn,{deltaX:function(n){return"deltaX"in n?n.deltaX:"wheelDeltaX"in n?-n.wheelDeltaX:0},deltaY:function(n){return"deltaY"in n?n.deltaY:"wheelDeltaY"in n?-n.wheelDeltaY:"wheelDelta"in n?-n.wheelDelta:0},deltaZ:0,deltaMode:0}),Qn=Pe(go),mo=[9,13,27,32],Ku=y&&"CompositionEvent"in window,Io=null;y&&"documentMode"in document&&(Io=document.documentMode);var L0=y&&"TextEvent"in window&&!Io,As=y&&(!Ku||Io&&8<Io&&11>=Io),Tm=" ",km=!1;function Md(n,i){switch(n){case"keyup":return mo.indexOf(i.keyCode)!==-1;case"keydown":return i.keyCode!==229;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function Rm(n){return n=n.detail,typeof n=="object"&&"data"in n?n.data:null}var js=!1;function z0(n,i){switch(n){case"compositionend":return Rm(i);case"keypress":return i.which!==32?null:(km=!0,Tm);case"textInput":return n=i.data,n===Tm&&km?null:n;default:return null}}function Dm(n,i){if(js)return n==="compositionend"||!Ku&&Md(n,i)?(n=z(),T=Os=ea=null,js=!1,n):null;switch(n){case"paste":return null;case"keypress":if(!(i.ctrlKey||i.altKey||i.metaKey)||i.ctrlKey&&i.altKey){if(i.char&&1<i.char.length)return i.char;if(i.which)return String.fromCharCode(i.which)}return null;case"compositionend":return As&&i.locale!=="ko"?null:i.data;default:return null}}var N0={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function Mm(n){var i=n&&n.nodeName&&n.nodeName.toLowerCase();return i==="input"?!!N0[n.type]:i==="textarea"}function $m(n,i,u,f){qi(f),i=ic(i,"onChange"),0<i.length&&(u=new at("onChange","change",null,u,f),n.push({event:u,listeners:i}))}var _s=null,Ba=null;function zp(n){jd(n,0)}function Qu(n){var i=Ke(n);if(an(i))return n}function Om(n,i){if(n==="change")return i}var Am=!1;if(y){var Np;if(y){var Pp="oninput"in document;if(!Pp){var jm=document.createElement("div");jm.setAttribute("oninput","return;"),Pp=typeof jm.oninput=="function"}Np=Pp}else Np=!1;Am=Np&&(!document.documentMode||9<document.documentMode)}function _m(){_s&&(_s.detachEvent("onpropertychange",Lm),Ba=_s=null)}function Lm(n){if(n.propertyName==="value"&&Qu(Ba)){var i=[];$m(i,Ba,n,vn(n)),El(zp,i)}}function P0(n,i,u){n==="focusin"?(_m(),_s=i,Ba=u,_s.attachEvent("onpropertychange",Lm)):n==="focusout"&&_m()}function F0(n){if(n==="selectionchange"||n==="keyup"||n==="keydown")return Qu(Ba)}function zm(n,i){if(n==="click")return Qu(i)}function B0(n,i){if(n==="input"||n==="change")return Qu(i)}function Nm(n,i){return n===i&&(n!==0||1/n===1/i)||n!==n&&i!==i}var ba=typeof Object.is=="function"?Object.is:Nm;function qu(n,i){if(ba(n,i))return!0;if(typeof n!="object"||n===null||typeof i!="object"||i===null)return!1;var u=Object.keys(n),f=Object.keys(i);if(u.length!==f.length)return!1;for(f=0;f<u.length;f++){var g=u[f];if(!E.call(i,g)||!ba(n[g],i[g]))return!1}return!0}function Pm(n){for(;n&&n.firstChild;)n=n.firstChild;return n}function Fm(n,i){var u=Pm(n);n=0;for(var f;u;){if(u.nodeType===3){if(f=n+u.textContent.length,n<=i&&f>=i)return{node:u,offset:i-n};n=f}e:{for(;u;){if(u.nextSibling){u=u.nextSibling;break e}u=u.parentNode}u=void 0}u=Pm(u)}}function $d(n,i){return n&&i?n===i?!0:n&&n.nodeType===3?!1:i&&i.nodeType===3?$d(n,i.parentNode):"contains"in n?n.contains(i):n.compareDocumentPosition?!!(n.compareDocumentPosition(i)&16):!1:!1}function Uo(){for(var n=window,i=Pn();i instanceof n.HTMLIFrameElement;){try{var u=typeof i.contentWindow.location.href=="string"}catch{u=!1}if(u)n=i.contentWindow;else break;i=Pn(n.document)}return i}function Ls(n){var i=n&&n.nodeName&&n.nodeName.toLowerCase();return i&&(i==="input"&&(n.type==="text"||n.type==="search"||n.type==="tel"||n.type==="url"||n.type==="password")||i==="textarea"||n.contentEditable==="true")}function Bm(n){var i=Uo(),u=n.focusedElem,f=n.selectionRange;if(i!==u&&u&&u.ownerDocument&&$d(u.ownerDocument.documentElement,u)){if(f!==null&&Ls(u)){if(i=f.start,n=f.end,n===void 0&&(n=i),"selectionStart"in u)u.selectionStart=i,u.selectionEnd=Math.min(n,u.value.length);else if(n=(i=u.ownerDocument||document)&&i.defaultView||window,n.getSelection){n=n.getSelection();var g=u.textContent.length,x=Math.min(f.start,g);f=f.end===void 0?x:Math.min(f.end,g),!n.extend&&x>f&&(g=f,f=x,x=g),g=Fm(u,x);var k=Fm(u,f);g&&k&&(n.rangeCount!==1||n.anchorNode!==g.node||n.anchorOffset!==g.offset||n.focusNode!==k.node||n.focusOffset!==k.offset)&&(i=i.createRange(),i.setStart(g.node,g.offset),n.removeAllRanges(),x>f?(n.addRange(i),n.extend(k.node,k.offset)):(i.setEnd(k.node,k.offset),n.addRange(i)))}}for(i=[],n=u;n=n.parentNode;)n.nodeType===1&&i.push({element:n,left:n.scrollLeft,top:n.scrollTop});for(typeof u.focus=="function"&&u.focus(),u=0;u<i.length;u++)n=i[u],n.element.scrollLeft=n.left,n.element.scrollTop=n.top}}var zs=y&&"documentMode"in document&&11>=document.documentMode,Ns=null,Fp=null,Xu=null,Bp=!1;function Im(n,i,u){var f=u.window===u?u.document:u.nodeType===9?u:u.ownerDocument;Bp||Ns==null||Ns!==Pn(f)||(f=Ns,"selectionStart"in f&&Ls(f)?f={start:f.selectionStart,end:f.selectionEnd}:(f=(f.ownerDocument&&f.ownerDocument.defaultView||window).getSelection(),f={anchorNode:f.anchorNode,anchorOffset:f.anchorOffset,focusNode:f.focusNode,focusOffset:f.focusOffset}),Xu&&qu(Xu,f)||(Xu=f,f=ic(Fp,"onSelect"),0<f.length&&(i=new at("onSelect","select",null,i,u),n.push({event:i,listeners:f}),i.target=Ns)))}function Ju(n,i){var u={};return u[n.toLowerCase()]=i.toLowerCase(),u["Webkit"+n]="webkit"+i,u["Moz"+n]="moz"+i,u}var Ps={animationend:Ju("Animation","AnimationEnd"),animationiteration:Ju("Animation","AnimationIteration"),animationstart:Ju("Animation","AnimationStart"),transitionend:Ju("Transition","TransitionEnd")},Od={},Nr={};y&&(Nr=document.createElement("div").style,"AnimationEvent"in window||(delete Ps.animationend.animation,delete Ps.animationiteration.animation,delete Ps.animationstart.animation),"TransitionEvent"in window||delete Ps.transitionend.transition);function Zu(n){if(Od[n])return Od[n];if(!Ps[n])return n;var i=Ps[n],u;for(u in i)if(i.hasOwnProperty(u)&&u in Nr)return Od[n]=i[u];return n}var Um=Zu("animationend"),Hm=Zu("animationiteration"),Vm=Zu("animationstart"),Wm=Zu("transitionend"),Ym=new Map,Ip="abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");function Ia(n,i){Ym.set(n,i),b(i,[n])}for(var Al=0;Al<Ip.length;Al++){var Up=Ip[Al],ec=Up.toLowerCase(),I0=Up[0].toUpperCase()+Up.slice(1);Ia(ec,"on"+I0)}Ia(Um,"onAnimationEnd"),Ia(Hm,"onAnimationIteration"),Ia(Vm,"onAnimationStart"),Ia("dblclick","onDoubleClick"),Ia("focusin","onFocus"),Ia("focusout","onBlur"),Ia(Wm,"onTransitionEnd"),S("onMouseEnter",["mouseout","mouseover"]),S("onMouseLeave",["mouseout","mouseover"]),S("onPointerEnter",["pointerout","pointerover"]),S("onPointerLeave",["pointerout","pointerover"]),b("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),b("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),b("onBeforeInput",["compositionend","keypress","textInput","paste"]),b("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),b("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),b("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var tc="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),U0=new Set("cancel close invalid load scroll toggle".split(" ").concat(tc));function Ad(n,i,u){var f=n.type||"unknown-event";n.currentTarget=u,ke(f,i,void 0,n),n.currentTarget=null}function jd(n,i){i=(i&4)!==0;for(var u=0;u<n.length;u++){var f=n[u],g=f.event;f=f.listeners;e:{var x=void 0;if(i)for(var k=f.length-1;0<=k;k--){var _=f[k],F=_.instance,J=_.currentTarget;if(_=_.listener,F!==x&&g.isPropagationStopped())break e;Ad(g,_,J),x=F}else for(k=0;k<f.length;k++){if(_=f[k],F=_.instance,J=_.currentTarget,_=_.listener,F!==x&&g.isPropagationStopped())break e;Ad(g,_,J),x=F}}}if(La)throw n=oo,La=!1,oo=null,n}function Xt(n,i){var u=i[Wp];u===void 0&&(u=i[Wp]=new Set);var f=n+"__bubble";u.has(f)||(Hp(i,n,2,!1),u.add(f))}function Ho(n,i,u){var f=0;i&&(f|=4),Hp(u,n,f,i)}var nc="_reactListening"+Math.random().toString(36).slice(2);function rc(n){if(!n[nc]){n[nc]=!0,d.forEach(function(u){u!=="selectionchange"&&(U0.has(u)||Ho(u,!1,n),Ho(u,!0,n))});var i=n.nodeType===9?n:n.ownerDocument;i===null||i[nc]||(i[nc]=!0,Ho("selectionchange",!1,i))}}function Hp(n,i,u,f){switch(Wu(i)){case 1:var g=fo;break;case 4:g=Ms;break;default:g=Bo}u=g.bind(null,i,u,n),g=void 0,!$i||i!=="touchstart"&&i!=="touchmove"&&i!=="wheel"||(g=!0),f?g!==void 0?n.addEventListener(i,u,{capture:!0,passive:g}):n.addEventListener(i,u,!0):g!==void 0?n.addEventListener(i,u,{passive:g}):n.addEventListener(i,u,!1)}function Vp(n,i,u,f,g){var x=f;if(!(i&1)&&!(i&2)&&f!==null)e:for(;;){if(f===null)return;var k=f.tag;if(k===3||k===4){var _=f.stateNode.containerInfo;if(_===g||_.nodeType===8&&_.parentNode===g)break;if(k===4)for(k=f.return;k!==null;){var F=k.tag;if((F===3||F===4)&&(F=k.stateNode.containerInfo,F===g||F.nodeType===8&&F.parentNode===g))return;k=k.return}for(;_!==null;){if(k=zl(_),k===null)return;if(F=k.tag,F===5||F===6){f=x=k;continue e}_=_.parentNode}}f=f.return}El(function(){var J=x,ge=vn(u),me=[];e:{var he=Ym.get(n);if(he!==void 0){var _e=at,Fe=n;switch(n){case"keypress":if(q(u)===0)break e;case"keydown":case"keyup":_e=_p;break;case"focusin":Fe="focus",_e=Ol;break;case"focusout":Fe="blur",_e=Ol;break;case"beforeblur":case"afterblur":_e=Ol;break;case"click":if(u.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":_e=Bn;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":_e=Yu;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":_e=Dd;break;case Um:case Hm:case Vm:_e=Ap;break;case Wm:_e=di;break;case"scroll":_e=qt;break;case"wheel":_e=Qn;break;case"copy":case"cut":case"paste":_e=jp;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":_e=Rd}var Ie=(i&4)!==0,Vn=!Ie&&n==="scroll",W=Ie?he!==null?he+"Capture":null:he;Ie=[];for(var I=J,Q;I!==null;){Q=I;var ye=Q.stateNode;if(Q.tag===5&&ye!==null&&(Q=ye,W!==null&&(ye=ja(I,W),ye!=null&&Ie.push(Fs(I,ye,Q)))),Vn)break;I=I.return}0<Ie.length&&(he=new _e(he,Fe,null,u,ge),me.push({event:he,listeners:Ie}))}}if(!(i&7)){e:{if(he=n==="mouseover"||n==="pointerover",_e=n==="mouseout"||n==="pointerout",he&&u!==dr&&(Fe=u.relatedTarget||u.fromElement)&&(zl(Fe)||Fe[vo]))break e;if((_e||he)&&(he=ge.window===ge?ge:(he=ge.ownerDocument)?he.defaultView||he.parentWindow:window,_e?(Fe=u.relatedTarget||u.toElement,_e=J,Fe=Fe?zl(Fe):null,Fe!==null&&(Vn=Me(Fe),Fe!==Vn||Fe.tag!==5&&Fe.tag!==6)&&(Fe=null)):(_e=null,Fe=J),_e!==Fe)){if(Ie=Bn,ye="onMouseLeave",W="onMouseEnter",I="mouse",(n==="pointerout"||n==="pointerover")&&(Ie=Rd,ye="onPointerLeave",W="onPointerEnter",I="pointer"),Vn=_e==null?he:Ke(_e),Q=Fe==null?he:Ke(Fe),he=new Ie(ye,I+"leave",_e,u,ge),he.target=Vn,he.relatedTarget=Q,ye=null,zl(ge)===J&&(Ie=new Ie(W,I+"enter",Fe,u,ge),Ie.target=Q,Ie.relatedTarget=Vn,ye=Ie),Vn=ye,_e&&Fe)t:{for(Ie=_e,W=Fe,I=0,Q=Ie;Q;Q=jl(Q))I++;for(Q=0,ye=W;ye;ye=jl(ye))Q++;for(;0<I-Q;)Ie=jl(Ie),I--;for(;0<Q-I;)W=jl(W),Q--;for(;I--;){if(Ie===W||W!==null&&Ie===W.alternate)break t;Ie=jl(Ie),W=jl(W)}Ie=null}else Ie=null;_e!==null&&_d(me,he,_e,Ie,!1),Fe!==null&&Vn!==null&&_d(me,Vn,Fe,Ie,!0)}}e:{if(he=J?Ke(J):window,_e=he.nodeName&&he.nodeName.toLowerCase(),_e==="select"||_e==="input"&&he.type==="file")var $e=Om;else if(Mm(he))if(Am)$e=B0;else{$e=F0;var qe=P0}else(_e=he.nodeName)&&_e.toLowerCase()==="input"&&(he.type==="checkbox"||he.type==="radio")&&($e=zm);if($e&&($e=$e(n,J))){$m(me,$e,u,ge);break e}qe&&qe(n,he,J),n==="focusout"&&(qe=he._wrapperState)&&qe.controlled&&he.type==="number"&&ga(he,"number",he.value)}switch(qe=J?Ke(J):window,n){case"focusin":(Mm(qe)||qe.contentEditable==="true")&&(Ns=qe,Fp=J,Xu=null);break;case"focusout":Xu=Fp=Ns=null;break;case"mousedown":Bp=!0;break;case"contextmenu":case"mouseup":case"dragend":Bp=!1,Im(me,u,ge);break;case"selectionchange":if(zs)break;case"keydown":case"keyup":Im(me,u,ge)}var Ze;if(Ku)e:{switch(n){case"compositionstart":var st="onCompositionStart";break e;case"compositionend":st="onCompositionEnd";break e;case"compositionupdate":st="onCompositionUpdate";break e}st=void 0}else js?Md(n,u)&&(st="onCompositionEnd"):n==="keydown"&&u.keyCode===229&&(st="onCompositionStart");st&&(As&&u.locale!=="ko"&&(js||st!=="onCompositionStart"?st==="onCompositionEnd"&&js&&(Ze=z()):(ea=ge,Os="value"in ea?ea.value:ea.textContent,js=!0)),qe=ic(J,st),0<qe.length&&(st=new kd(st,n,null,u,ge),me.push({event:st,listeners:qe}),Ze?st.data=Ze:(Ze=Rm(u),Ze!==null&&(st.data=Ze)))),(Ze=L0?z0(n,u):Dm(n,u))&&(J=ic(J,"onBeforeInput"),0<J.length&&(ge=new kd("onBeforeInput","beforeinput",null,u,ge),me.push({event:ge,listeners:J}),ge.data=Ze))}jd(me,i)})}function Fs(n,i,u){return{instance:n,listener:i,currentTarget:u}}function ic(n,i){for(var u=i+"Capture",f=[];n!==null;){var g=n,x=g.stateNode;g.tag===5&&x!==null&&(g=x,x=ja(n,u),x!=null&&f.unshift(Fs(n,x,g)),x=ja(n,i),x!=null&&f.push(Fs(n,x,g))),n=n.return}return f}function jl(n){if(n===null)return null;do n=n.return;while(n&&n.tag!==5);return n||null}function _d(n,i,u,f,g){for(var x=i._reactName,k=[];u!==null&&u!==f;){var _=u,F=_.alternate,J=_.stateNode;if(F!==null&&F===f)break;_.tag===5&&J!==null&&(_=J,g?(F=ja(u,x),F!=null&&k.unshift(Fs(u,F,_))):g||(F=ja(u,x),F!=null&&k.push(Fs(u,F,_)))),u=u.return}k.length!==0&&n.push({event:i,listeners:k})}var H0=/\r\n?/g,Gm=/\u0000|\uFFFD/g;function Km(n){return(typeof n=="string"?n:""+n).replace(H0,`
`).replace(Gm,"")}function Ld(n,i,u){if(i=Km(i),Km(n)!==i&&u)throw Error(l(425))}function zd(){}var _l=null,ac=null;function Ll(n,i){return n==="textarea"||n==="noscript"||typeof i.children=="string"||typeof i.children=="number"||typeof i.dangerouslySetInnerHTML=="object"&&i.dangerouslySetInnerHTML!==null&&i.dangerouslySetInnerHTML.__html!=null}var Nd=typeof setTimeout=="function"?setTimeout:void 0,Qm=typeof clearTimeout=="function"?clearTimeout:void 0,Pd=typeof Promise=="function"?Promise:void 0,V0=typeof queueMicrotask=="function"?queueMicrotask:typeof Pd<"u"?function(n){return Pd.resolve(null).then(n).catch(Bs)}:Nd;function Bs(n){setTimeout(function(){throw n})}function Is(n,i){var u=i,f=0;do{var g=u.nextSibling;if(n.removeChild(u),g&&g.nodeType===8)if(u=g.data,u==="/$"){if(f===0){n.removeChild(g),Ji(i);return}f--}else u!=="$"&&u!=="$?"&&u!=="$!"||f++;u=g}while(u);Ji(i)}function wa(n){for(;n!=null;n=n.nextSibling){var i=n.nodeType;if(i===1||i===3)break;if(i===8){if(i=n.data,i==="$"||i==="$!"||i==="$?")break;if(i==="/$")return null}}return n}function Fd(n){n=n.previousSibling;for(var i=0;n;){if(n.nodeType===8){var u=n.data;if(u==="$"||u==="$!"||u==="$?"){if(i===0)return n;i--}else u==="/$"&&i++}n=n.previousSibling}return null}var Us=Math.random().toString(36).slice(2),ta="__reactFiber$"+Us,oc="__reactProps$"+Us,vo="__reactContainer$"+Us,Wp="__reactEvents$"+Us,Yp="__reactListeners$"+Us,Hs="__reactHandles$"+Us;function zl(n){var i=n[ta];if(i)return i;for(var u=n.parentNode;u;){if(i=u[vo]||u[ta]){if(u=i.alternate,i.child!==null||u!==null&&u.child!==null)for(n=Fd(n);n!==null;){if(u=n[ta])return u;n=Fd(n)}return i}n=u,u=n.parentNode}return null}function lc(n){return n=n[ta]||n[vo],!n||n.tag!==5&&n.tag!==6&&n.tag!==13&&n.tag!==3?null:n}function Ke(n){if(n.tag===5||n.tag===6)return n.stateNode;throw Error(l(33))}function yo(n){return n[oc]||null}var Ln=[],At=-1;function fi(n){return{current:n}}function tn(n){0>At||(n.current=Ln[At],Ln[At]=null,At--)}function gn(n,i){At++,Ln[At]=n.current,n.current=i}var Et={},Rn=fi(Et),qn=fi(!1),na=Et;function Ai(n,i){var u=n.type.contextTypes;if(!u)return Et;var f=n.stateNode;if(f&&f.__reactInternalMemoizedUnmaskedChildContext===i)return f.__reactInternalMemoizedMaskedChildContext;var g={},x;for(x in u)g[x]=i[x];return f&&(n=n.stateNode,n.__reactInternalMemoizedUnmaskedChildContext=i,n.__reactInternalMemoizedMaskedChildContext=g),g}function zn(n){return n=n.childContextTypes,n!=null}function Ua(){tn(qn),tn(Rn)}function Bd(n,i,u){if(Rn.current!==Et)throw Error(l(168));gn(Rn,i),gn(qn,u)}function qm(n,i,u){var f=n.stateNode;if(i=i.childContextTypes,typeof f.getChildContext!="function")return u;f=f.getChildContext();for(var g in f)if(!(g in i))throw Error(l(108,$t(n)||"Unknown",g));return be({},u,f)}function Nl(n){return n=(n=n.stateNode)&&n.__reactInternalMemoizedMergedChildContext||Et,na=Rn.current,gn(Rn,n),gn(qn,qn.current),!0}function Pr(n,i,u){var f=n.stateNode;if(!f)throw Error(l(169));u?(n=qm(n,i,na),f.__reactInternalMemoizedMergedChildContext=n,tn(qn),tn(Rn),gn(Rn,n)):tn(qn),gn(qn,u)}var Sa=null,sc=!1,uc=!1;function Vo(n){Sa===null?Sa=[n]:Sa.push(n)}function Gp(n){sc=!0,Vo(n)}function Yr(){if(!uc&&Sa!==null){uc=!0;var n=0,i=Nt;try{var u=Sa;for(Nt=1;n<u.length;n++){var f=u[n];do f=f(!0);while(f!==null)}Sa=null,sc=!1}catch(g){throw Sa!==null&&(Sa=Sa.slice(n+1)),yn(gt,Yr),g}finally{Nt=i,uc=!1}}return null}var Wo=[],Yo=0,Vs=null,Go=0,wr=[],Xn=0,Pl=null,Gr=1,Ha="";function Ko(n,i){Wo[Yo++]=Go,Wo[Yo++]=Vs,Vs=n,Go=i}function Xm(n,i,u){wr[Xn++]=Gr,wr[Xn++]=Ha,wr[Xn++]=Pl,Pl=n;var f=Gr;n=Ha;var g=32-Hr(f)-1;f&=~(1<<g),u+=1;var x=32-Hr(i)+g;if(30<x){var k=g-g%5;x=(f&(1<<k)-1).toString(32),f>>=k,g-=k,Gr=1<<32-Hr(i)+g|u<<g|f,Ha=x+n}else Gr=1<<x|u<<g|f,Ha=n}function Kp(n){n.return!==null&&(Ko(n,1),Xm(n,1,0))}function Id(n){for(;n===Vs;)Vs=Wo[--Yo],Wo[Yo]=null,Go=Wo[--Yo],Wo[Yo]=null;for(;n===Pl;)Pl=wr[--Xn],wr[Xn]=null,Ha=wr[--Xn],wr[Xn]=null,Gr=wr[--Xn],wr[Xn]=null}var pi=null,hi=null,En=!1,Ca=null;function Qp(n,i){var u=la(5,null,null,0);u.elementType="DELETED",u.stateNode=i,u.return=n,i=n.deletions,i===null?(n.deletions=[u],n.flags|=16):i.push(u)}function qp(n,i){switch(n.tag){case 5:var u=n.type;return i=i.nodeType!==1||u.toLowerCase()!==i.nodeName.toLowerCase()?null:i,i!==null?(n.stateNode=i,pi=n,hi=wa(i.firstChild),!0):!1;case 6:return i=n.pendingProps===""||i.nodeType!==3?null:i,i!==null?(n.stateNode=i,pi=n,hi=null,!0):!1;case 13:return i=i.nodeType!==8?null:i,i!==null?(u=Pl!==null?{id:Gr,overflow:Ha}:null,n.memoizedState={dehydrated:i,treeContext:u,retryLane:1073741824},u=la(18,null,null,0),u.stateNode=i,u.return=n,n.child=u,pi=n,hi=null,!0):!1;default:return!1}}function Xp(n){return(n.mode&1)!==0&&(n.flags&128)===0}function Jp(n){if(En){var i=hi;if(i){var u=i;if(!qp(n,i)){if(Xp(n))throw Error(l(418));i=wa(u.nextSibling);var f=pi;i&&qp(n,i)?Qp(f,u):(n.flags=n.flags&-4097|2,En=!1,pi=n)}}else{if(Xp(n))throw Error(l(418));n.flags=n.flags&-4097|2,En=!1,pi=n}}}function Jm(n){for(n=n.return;n!==null&&n.tag!==5&&n.tag!==3&&n.tag!==13;)n=n.return;pi=n}function In(n){if(n!==pi)return!1;if(!En)return Jm(n),En=!0,!1;var i;if((i=n.tag!==3)&&!(i=n.tag!==5)&&(i=n.type,i=i!=="head"&&i!=="body"&&!Ll(n.type,n.memoizedProps)),i&&(i=hi)){if(Xp(n))throw Zm(),Error(l(418));for(;i;)Qp(n,i),i=wa(i.nextSibling)}if(Jm(n),n.tag===13){if(n=n.memoizedState,n=n!==null?n.dehydrated:null,!n)throw Error(l(317));e:{for(n=n.nextSibling,i=0;n;){if(n.nodeType===8){var u=n.data;if(u==="/$"){if(i===0){hi=wa(n.nextSibling);break e}i--}else u!=="$"&&u!=="$!"&&u!=="$?"||i++}n=n.nextSibling}hi=null}}else hi=pi?wa(n.stateNode.nextSibling):null;return!0}function Zm(){for(var n=hi;n;)n=wa(n.nextSibling)}function xo(){hi=pi=null,En=!1}function cc(n){Ca===null?Ca=[n]:Ca.push(n)}var Fl=ce.ReactCurrentBatchConfig;function dc(n,i,u){if(n=u.ref,n!==null&&typeof n!="function"&&typeof n!="object"){if(u._owner){if(u=u._owner,u){if(u.tag!==1)throw Error(l(309));var f=u.stateNode}if(!f)throw Error(l(147,n));var g=f,x=""+n;return i!==null&&i.ref!==null&&typeof i.ref=="function"&&i.ref._stringRef===x?i.ref:(i=function(k){var _=g.refs;k===null?delete _[x]:_[x]=k},i._stringRef=x,i)}if(typeof n!="string")throw Error(l(284));if(!u._owner)throw Error(l(290,n))}return n}function Ws(n,i){throw n=Object.prototype.toString.call(i),Error(l(31,n==="[object Object]"?"object with keys {"+Object.keys(i).join(", ")+"}":n))}function ev(n){var i=n._init;return i(n._payload)}function tv(n){function i(W,I){if(n){var Q=W.deletions;Q===null?(W.deletions=[I],W.flags|=16):Q.push(I)}}function u(W,I){if(!n)return null;for(;I!==null;)i(W,I),I=I.sibling;return null}function f(W,I){for(W=new Map;I!==null;)I.key!==null?W.set(I.key,I):W.set(I.index,I),I=I.sibling;return W}function g(W,I){return W=al(W,I),W.index=0,W.sibling=null,W}function x(W,I,Q){return W.index=Q,n?(Q=W.alternate,Q!==null?(Q=Q.index,Q<I?(W.flags|=2,I):Q):(W.flags|=2,I)):(W.flags|=1048576,I)}function k(W){return n&&W.alternate===null&&(W.flags|=2),W}function _(W,I,Q,ye){return I===null||I.tag!==6?(I=ns(Q,W.mode,ye),I.return=W,I):(I=g(I,Q),I.return=W,I)}function F(W,I,Q,ye){var $e=Q.type;return $e===ue?ge(W,I,Q.props.children,ye,Q.key):I!==null&&(I.elementType===$e||typeof $e=="object"&&$e!==null&&$e.$$typeof===kt&&ev($e)===I.type)?(ye=g(I,Q.props),ye.ref=dc(W,I,Q),ye.return=W,ye):(ye=Rf(Q.type,Q.key,Q.props,null,W.mode,ye),ye.ref=dc(W,I,Q),ye.return=W,ye)}function J(W,I,Q,ye){return I===null||I.tag!==4||I.stateNode.containerInfo!==Q.containerInfo||I.stateNode.implementation!==Q.implementation?(I=Mh(Q,W.mode,ye),I.return=W,I):(I=g(I,Q.children||[]),I.return=W,I)}function ge(W,I,Q,ye,$e){return I===null||I.tag!==7?(I=ol(Q,W.mode,ye,$e),I.return=W,I):(I=g(I,Q),I.return=W,I)}function me(W,I,Q){if(typeof I=="string"&&I!==""||typeof I=="number")return I=ns(""+I,W.mode,Q),I.return=W,I;if(typeof I=="object"&&I!==null){switch(I.$$typeof){case Te:return Q=Rf(I.type,I.key,I.props,null,W.mode,Q),Q.ref=dc(W,null,I),Q.return=W,Q;case le:return I=Mh(I,W.mode,Q),I.return=W,I;case kt:var ye=I._init;return me(W,ye(I._payload),Q)}if(Ur(I)||Re(I))return I=ol(I,W.mode,Q,null),I.return=W,I;Ws(W,I)}return null}function he(W,I,Q,ye){var $e=I!==null?I.key:null;if(typeof Q=="string"&&Q!==""||typeof Q=="number")return $e!==null?null:_(W,I,""+Q,ye);if(typeof Q=="object"&&Q!==null){switch(Q.$$typeof){case Te:return Q.key===$e?F(W,I,Q,ye):null;case le:return Q.key===$e?J(W,I,Q,ye):null;case kt:return $e=Q._init,he(W,I,$e(Q._payload),ye)}if(Ur(Q)||Re(Q))return $e!==null?null:ge(W,I,Q,ye,null);Ws(W,Q)}return null}function _e(W,I,Q,ye,$e){if(typeof ye=="string"&&ye!==""||typeof ye=="number")return W=W.get(Q)||null,_(I,W,""+ye,$e);if(typeof ye=="object"&&ye!==null){switch(ye.$$typeof){case Te:return W=W.get(ye.key===null?Q:ye.key)||null,F(I,W,ye,$e);case le:return W=W.get(ye.key===null?Q:ye.key)||null,J(I,W,ye,$e);case kt:var qe=ye._init;return _e(W,I,Q,qe(ye._payload),$e)}if(Ur(ye)||Re(ye))return W=W.get(Q)||null,ge(I,W,ye,$e,null);Ws(I,ye)}return null}function Fe(W,I,Q,ye){for(var $e=null,qe=null,Ze=I,st=I=0,or=null;Ze!==null&&st<Q.length;st++){Ze.index>st?(or=Ze,Ze=null):or=Ze.sibling;var Vt=he(W,Ze,Q[st],ye);if(Vt===null){Ze===null&&(Ze=or);break}n&&Ze&&Vt.alternate===null&&i(W,Ze),I=x(Vt,I,st),qe===null?$e=Vt:qe.sibling=Vt,qe=Vt,Ze=or}if(st===Q.length)return u(W,Ze),En&&Ko(W,st),$e;if(Ze===null){for(;st<Q.length;st++)Ze=me(W,Q[st],ye),Ze!==null&&(I=x(Ze,I,st),qe===null?$e=Ze:qe.sibling=Ze,qe=Ze);return En&&Ko(W,st),$e}for(Ze=f(W,Ze);st<Q.length;st++)or=_e(Ze,W,st,Q[st],ye),or!==null&&(n&&or.alternate!==null&&Ze.delete(or.key===null?st:or.key),I=x(or,I,st),qe===null?$e=or:qe.sibling=or,qe=or);return n&&Ze.forEach(function(sl){return i(W,sl)}),En&&Ko(W,st),$e}function Ie(W,I,Q,ye){var $e=Re(Q);if(typeof $e!="function")throw Error(l(150));if(Q=$e.call(Q),Q==null)throw Error(l(151));for(var qe=$e=null,Ze=I,st=I=0,or=null,Vt=Q.next();Ze!==null&&!Vt.done;st++,Vt=Q.next()){Ze.index>st?(or=Ze,Ze=null):or=Ze.sibling;var sl=he(W,Ze,Vt.value,ye);if(sl===null){Ze===null&&(Ze=or);break}n&&Ze&&sl.alternate===null&&i(W,Ze),I=x(sl,I,st),qe===null?$e=sl:qe.sibling=sl,qe=sl,Ze=or}if(Vt.done)return u(W,Ze),En&&Ko(W,st),$e;if(Ze===null){for(;!Vt.done;st++,Vt=Q.next())Vt=me(W,Vt.value,ye),Vt!==null&&(I=x(Vt,I,st),qe===null?$e=Vt:qe.sibling=Vt,qe=Vt);return En&&Ko(W,st),$e}for(Ze=f(W,Ze);!Vt.done;st++,Vt=Q.next())Vt=_e(Ze,W,st,Vt.value,ye),Vt!==null&&(n&&Vt.alternate!==null&&Ze.delete(Vt.key===null?st:Vt.key),I=x(Vt,I,st),qe===null?$e=Vt:qe.sibling=Vt,qe=Vt);return n&&Ze.forEach(function(ib){return i(W,ib)}),En&&Ko(W,st),$e}function Vn(W,I,Q,ye){if(typeof Q=="object"&&Q!==null&&Q.type===ue&&Q.key===null&&(Q=Q.props.children),typeof Q=="object"&&Q!==null){switch(Q.$$typeof){case Te:e:{for(var $e=Q.key,qe=I;qe!==null;){if(qe.key===$e){if($e=Q.type,$e===ue){if(qe.tag===7){u(W,qe.sibling),I=g(qe,Q.props.children),I.return=W,W=I;break e}}else if(qe.elementType===$e||typeof $e=="object"&&$e!==null&&$e.$$typeof===kt&&ev($e)===qe.type){u(W,qe.sibling),I=g(qe,Q.props),I.ref=dc(W,qe,Q),I.return=W,W=I;break e}u(W,qe);break}else i(W,qe);qe=qe.sibling}Q.type===ue?(I=ol(Q.props.children,W.mode,ye,Q.key),I.return=W,W=I):(ye=Rf(Q.type,Q.key,Q.props,null,W.mode,ye),ye.ref=dc(W,I,Q),ye.return=W,W=ye)}return k(W);case le:e:{for(qe=Q.key;I!==null;){if(I.key===qe)if(I.tag===4&&I.stateNode.containerInfo===Q.containerInfo&&I.stateNode.implementation===Q.implementation){u(W,I.sibling),I=g(I,Q.children||[]),I.return=W,W=I;break e}else{u(W,I);break}else i(W,I);I=I.sibling}I=Mh(Q,W.mode,ye),I.return=W,W=I}return k(W);case kt:return qe=Q._init,Vn(W,I,qe(Q._payload),ye)}if(Ur(Q))return Fe(W,I,Q,ye);if(Re(Q))return Ie(W,I,Q,ye);Ws(W,Q)}return typeof Q=="string"&&Q!==""||typeof Q=="number"?(Q=""+Q,I!==null&&I.tag===6?(u(W,I.sibling),I=g(I,Q),I.return=W,W=I):(u(W,I),I=ns(Q,W.mode,ye),I.return=W,W=I),k(W)):u(W,I)}return Vn}var Ea=tv(!0),Sr=tv(!1),Ce=fi(null),ji=null,Fr=null,Zp=null;function eh(){Zp=Fr=ji=null}function th(n){var i=Ce.current;tn(Ce),n._currentValue=i}function nh(n,i,u){for(;n!==null;){var f=n.alternate;if((n.childLanes&i)!==i?(n.childLanes|=i,f!==null&&(f.childLanes|=i)):f!==null&&(f.childLanes&i)!==i&&(f.childLanes|=i),n===u)break;n=n.return}}function Ys(n,i){ji=n,Zp=Fr=null,n=n.dependencies,n!==null&&n.firstContext!==null&&(n.lanes&i&&(gr=!0),n.firstContext=null)}function nn(n){var i=n._currentValue;if(Zp!==n)if(n={context:n,memoizedValue:i,next:null},Fr===null){if(ji===null)throw Error(l(308));Fr=n,ji.dependencies={lanes:0,firstContext:n}}else Fr=Fr.next=n;return i}var Bl=null;function rh(n){Bl===null?Bl=[n]:Bl.push(n)}function nv(n,i,u,f){var g=i.interleaved;return g===null?(u.next=u,rh(i)):(u.next=g.next,g.next=u),i.interleaved=u,Va(n,f)}function Va(n,i){n.lanes|=i;var u=n.alternate;for(u!==null&&(u.lanes|=i),u=n,n=n.return;n!==null;)n.childLanes|=i,u=n.alternate,u!==null&&(u.childLanes|=i),u=n,n=n.return;return u.tag===3?u.stateNode:null}var ra=!1;function Qo(n){n.updateQueue={baseState:n.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:0},effects:null}}function rv(n,i){n=n.updateQueue,i.updateQueue===n&&(i.updateQueue={baseState:n.baseState,firstBaseUpdate:n.firstBaseUpdate,lastBaseUpdate:n.lastBaseUpdate,shared:n.shared,effects:n.effects})}function bo(n,i){return{eventTime:n,lane:i,tag:0,payload:null,callback:null,next:null}}function qo(n,i,u){var f=n.updateQueue;if(f===null)return null;if(f=f.shared,jt&2){var g=f.pending;return g===null?i.next=i:(i.next=g.next,g.next=i),f.pending=i,Va(n,u)}return g=f.interleaved,g===null?(i.next=i,rh(f)):(i.next=g.next,g.next=i),f.interleaved=i,Va(n,u)}function Ud(n,i,u){if(i=i.updateQueue,i!==null&&(i=i.shared,(u&4194240)!==0)){var f=i.lanes;f&=n.pendingLanes,u|=f,i.lanes=u,Hu(n,u)}}function iv(n,i){var u=n.updateQueue,f=n.alternate;if(f!==null&&(f=f.updateQueue,u===f)){var g=null,x=null;if(u=u.firstBaseUpdate,u!==null){do{var k={eventTime:u.eventTime,lane:u.lane,tag:u.tag,payload:u.payload,callback:u.callback,next:null};x===null?g=x=k:x=x.next=k,u=u.next}while(u!==null);x===null?g=x=i:x=x.next=i}else g=x=i;u={baseState:f.baseState,firstBaseUpdate:g,lastBaseUpdate:x,shared:f.shared,effects:f.effects},n.updateQueue=u;return}n=u.lastBaseUpdate,n===null?u.firstBaseUpdate=i:n.next=i,u.lastBaseUpdate=i}function Hd(n,i,u,f){var g=n.updateQueue;ra=!1;var x=g.firstBaseUpdate,k=g.lastBaseUpdate,_=g.shared.pending;if(_!==null){g.shared.pending=null;var F=_,J=F.next;F.next=null,k===null?x=J:k.next=J,k=F;var ge=n.alternate;ge!==null&&(ge=ge.updateQueue,_=ge.lastBaseUpdate,_!==k&&(_===null?ge.firstBaseUpdate=J:_.next=J,ge.lastBaseUpdate=F))}if(x!==null){var me=g.baseState;k=0,ge=J=F=null,_=x;do{var he=_.lane,_e=_.eventTime;if((f&he)===he){ge!==null&&(ge=ge.next={eventTime:_e,lane:0,tag:_.tag,payload:_.payload,callback:_.callback,next:null});e:{var Fe=n,Ie=_;switch(he=i,_e=u,Ie.tag){case 1:if(Fe=Ie.payload,typeof Fe=="function"){me=Fe.call(_e,me,he);break e}me=Fe;break e;case 3:Fe.flags=Fe.flags&-65537|128;case 0:if(Fe=Ie.payload,he=typeof Fe=="function"?Fe.call(_e,me,he):Fe,he==null)break e;me=be({},me,he);break e;case 2:ra=!0}}_.callback!==null&&_.lane!==0&&(n.flags|=64,he=g.effects,he===null?g.effects=[_]:he.push(_))}else _e={eventTime:_e,lane:he,tag:_.tag,payload:_.payload,callback:_.callback,next:null},ge===null?(J=ge=_e,F=me):ge=ge.next=_e,k|=he;if(_=_.next,_===null){if(_=g.shared.pending,_===null)break;he=_,_=he.next,he.next=null,g.lastBaseUpdate=he,g.shared.pending=null}}while(!0);if(ge===null&&(F=me),g.baseState=F,g.firstBaseUpdate=J,g.lastBaseUpdate=ge,i=g.shared.interleaved,i!==null){g=i;do k|=g.lane,g=g.next;while(g!==i)}else x===null&&(g.shared.lanes=0);ql|=k,n.lanes=k,n.memoizedState=me}}function ih(n,i,u){if(n=i.effects,i.effects=null,n!==null)for(i=0;i<n.length;i++){var f=n[i],g=f.callback;if(g!==null){if(f.callback=null,f=u,typeof g!="function")throw Error(l(191,g));g.call(f)}}}var Gs={},Wa=fi(Gs),fc=fi(Gs),pc=fi(Gs);function Il(n){if(n===Gs)throw Error(l(174));return n}function ah(n,i){switch(gn(pc,i),gn(fc,n),gn(Wa,Gs),n=i.nodeType,n){case 9:case 11:i=(i=i.documentElement)?i.namespaceURI:xr(null,"");break;default:n=n===8?i.parentNode:i,i=n.namespaceURI||null,n=n.tagName,i=xr(i,n)}tn(Wa),gn(Wa,i)}function Ks(){tn(Wa),tn(fc),tn(pc)}function oh(n){Il(pc.current);var i=Il(Wa.current),u=xr(i,n.type);i!==u&&(gn(fc,n),gn(Wa,u))}function lh(n){fc.current===n&&(tn(Wa),tn(fc))}var Dn=fi(0);function Vd(n){for(var i=n;i!==null;){if(i.tag===13){var u=i.memoizedState;if(u!==null&&(u=u.dehydrated,u===null||u.data==="$?"||u.data==="$!"))return i}else if(i.tag===19&&i.memoizedProps.revealOrder!==void 0){if(i.flags&128)return i}else if(i.child!==null){i.child.return=i,i=i.child;continue}if(i===n)break;for(;i.sibling===null;){if(i.return===null||i.return===n)return null;i=i.return}i.sibling.return=i.return,i=i.sibling}return null}var sh=[];function hc(){for(var n=0;n<sh.length;n++)sh[n]._workInProgressVersionPrimary=null;sh.length=0}var Qe=ce.ReactCurrentDispatcher,Dt=ce.ReactCurrentBatchConfig,zt=0,mt=null,cn=null,rr=null,Wd=!1,gc=!1,mc=0,uh=0;function ie(){throw Error(l(321))}function Jn(n,i){if(i===null)return!1;for(var u=0;u<i.length&&u<n.length;u++)if(!ba(n[u],i[u]))return!1;return!0}function nt(n,i,u,f,g,x){if(zt=x,mt=i,i.memoizedState=null,i.updateQueue=null,i.lanes=0,Qe.current=n===null||n.memoizedState===null?lf:sf,n=u(f,g),gc){x=0;do{if(gc=!1,mc=0,25<=x)throw Error(l(301));x+=1,rr=cn=null,i.updateQueue=null,Qe.current=wc,n=u(f,g)}while(gc)}if(Qe.current=rn,i=cn!==null&&cn.next!==null,zt=0,rr=cn=mt=null,Wd=!1,i)throw Error(l(300));return n}function Xo(){var n=mc!==0;return mc=0,n}function pr(){var n={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return rr===null?mt.memoizedState=rr=n:rr=rr.next=n,rr}function hr(){if(cn===null){var n=mt.alternate;n=n!==null?n.memoizedState:null}else n=cn.next;var i=rr===null?mt.memoizedState:rr.next;if(i!==null)rr=i,cn=n;else{if(n===null)throw Error(l(310));cn=n,n={memoizedState:cn.memoizedState,baseState:cn.baseState,baseQueue:cn.baseQueue,queue:cn.queue,next:null},rr===null?mt.memoizedState=rr=n:rr=rr.next=n}return rr}function gi(n,i){return typeof i=="function"?i(n):i}function Ul(n){var i=hr(),u=i.queue;if(u===null)throw Error(l(311));u.lastRenderedReducer=n;var f=cn,g=f.baseQueue,x=u.pending;if(x!==null){if(g!==null){var k=g.next;g.next=x.next,x.next=k}f.baseQueue=g=x,u.pending=null}if(g!==null){x=g.next,f=f.baseState;var _=k=null,F=null,J=x;do{var ge=J.lane;if((zt&ge)===ge)F!==null&&(F=F.next={lane:0,action:J.action,hasEagerState:J.hasEagerState,eagerState:J.eagerState,next:null}),f=J.hasEagerState?J.eagerState:n(f,J.action);else{var me={lane:ge,action:J.action,hasEagerState:J.hasEagerState,eagerState:J.eagerState,next:null};F===null?(_=F=me,k=f):F=F.next=me,mt.lanes|=ge,ql|=ge}J=J.next}while(J!==null&&J!==x);F===null?k=f:F.next=_,ba(f,i.memoizedState)||(gr=!0),i.memoizedState=f,i.baseState=k,i.baseQueue=F,u.lastRenderedState=f}if(n=u.interleaved,n!==null){g=n;do x=g.lane,mt.lanes|=x,ql|=x,g=g.next;while(g!==n)}else g===null&&(u.lanes=0);return[i.memoizedState,u.dispatch]}function Jo(n){var i=hr(),u=i.queue;if(u===null)throw Error(l(311));u.lastRenderedReducer=n;var f=u.dispatch,g=u.pending,x=i.memoizedState;if(g!==null){u.pending=null;var k=g=g.next;do x=n(x,k.action),k=k.next;while(k!==g);ba(x,i.memoizedState)||(gr=!0),i.memoizedState=x,i.baseQueue===null&&(i.baseState=x),u.lastRenderedState=x}return[x,f]}function Qs(){}function Yd(n,i){var u=mt,f=hr(),g=i(),x=!ba(f.memoizedState,g);if(x&&(f.memoizedState=g,gr=!0),f=f.queue,vc(Qd.bind(null,u,f,n),[n]),f.getSnapshot!==i||x||rr!==null&&rr.memoizedState.tag&1){if(u.flags|=2048,Hl(9,Kd.bind(null,u,f,g,i),void 0,null),Zn===null)throw Error(l(349));zt&30||Gd(u,i,g)}return g}function Gd(n,i,u){n.flags|=16384,n={getSnapshot:i,value:u},i=mt.updateQueue,i===null?(i={lastEffect:null,stores:null},mt.updateQueue=i,i.stores=[n]):(u=i.stores,u===null?i.stores=[n]:u.push(n))}function Kd(n,i,u,f){i.value=u,i.getSnapshot=f,qd(i)&&Xd(n)}function Qd(n,i,u){return u(function(){qd(i)&&Xd(n)})}function qd(n){var i=n.getSnapshot;n=n.value;try{var u=i();return!ba(n,u)}catch{return!0}}function Xd(n){var i=Va(n,1);i!==null&&Ni(i,n,1,-1)}function Jd(n){var i=pr();return typeof n=="function"&&(n=n()),i.memoizedState=i.baseState=n,n={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:gi,lastRenderedState:n},i.queue=n,n=n.dispatch=bc.bind(null,mt,n),[i.memoizedState,n]}function Hl(n,i,u,f){return n={tag:n,create:i,destroy:u,deps:f,next:null},i=mt.updateQueue,i===null?(i={lastEffect:null,stores:null},mt.updateQueue=i,i.lastEffect=n.next=n):(u=i.lastEffect,u===null?i.lastEffect=n.next=n:(f=u.next,u.next=n,n.next=f,i.lastEffect=n)),n}function Zd(){return hr().memoizedState}function qs(n,i,u,f){var g=pr();mt.flags|=n,g.memoizedState=Hl(1|i,u,void 0,f===void 0?null:f)}function Xs(n,i,u,f){var g=hr();f=f===void 0?null:f;var x=void 0;if(cn!==null){var k=cn.memoizedState;if(x=k.destroy,f!==null&&Jn(f,k.deps)){g.memoizedState=Hl(i,u,x,f);return}}mt.flags|=n,g.memoizedState=Hl(1|i,u,x,f)}function ef(n,i){return qs(8390656,8,n,i)}function vc(n,i){return Xs(2048,8,n,i)}function tf(n,i){return Xs(4,2,n,i)}function nf(n,i){return Xs(4,4,n,i)}function yc(n,i){if(typeof i=="function")return n=n(),i(n),function(){i(null)};if(i!=null)return n=n(),i.current=n,function(){i.current=null}}function Vl(n,i,u){return u=u!=null?u.concat([n]):null,Xs(4,4,yc.bind(null,i,n),u)}function xc(){}function rf(n,i){var u=hr();i=i===void 0?null:i;var f=u.memoizedState;return f!==null&&i!==null&&Jn(i,f[1])?f[0]:(u.memoizedState=[n,i],n)}function af(n,i){var u=hr();i=i===void 0?null:i;var f=u.memoizedState;return f!==null&&i!==null&&Jn(i,f[1])?f[0]:(n=n(),u.memoizedState=[n,i],n)}function of(n,i,u){return zt&21?(ba(u,i)||(u=Tl(),mt.lanes|=u,ql|=u,n.baseState=!0),i):(n.baseState&&(n.baseState=!1,gr=!0),n.memoizedState=u)}function av(n,i){var u=Nt;Nt=u!==0&&4>u?u:4,n(!0);var f=Dt.transition;Dt.transition={};try{n(!1),i()}finally{Nt=u,Dt.transition=f}}function Js(){return hr().memoizedState}function ov(n,i,u){var f=zi(n);if(u={lane:f,action:u,hasEagerState:!1,eagerState:null,next:null},Zo(n))mi(i,u);else if(u=nv(n,i,u,f),u!==null){var g=mn();Ni(u,n,f,g),lv(u,i,f)}}function bc(n,i,u){var f=zi(n),g={lane:f,action:u,hasEagerState:!1,eagerState:null,next:null};if(Zo(n))mi(i,g);else{var x=n.alternate;if(n.lanes===0&&(x===null||x.lanes===0)&&(x=i.lastRenderedReducer,x!==null))try{var k=i.lastRenderedState,_=x(k,u);if(g.hasEagerState=!0,g.eagerState=_,ba(_,k)){var F=i.interleaved;F===null?(g.next=g,rh(i)):(g.next=F.next,F.next=g),i.interleaved=g;return}}catch{}finally{}u=nv(n,i,g,f),u!==null&&(g=mn(),Ni(u,n,f,g),lv(u,i,f))}}function Zo(n){var i=n.alternate;return n===mt||i!==null&&i===mt}function mi(n,i){gc=Wd=!0;var u=n.pending;u===null?i.next=i:(i.next=u.next,u.next=i),n.pending=i}function lv(n,i,u){if(u&4194240){var f=i.lanes;f&=n.pendingLanes,u|=f,i.lanes=u,Hu(n,u)}}var rn={readContext:nn,useCallback:ie,useContext:ie,useEffect:ie,useImperativeHandle:ie,useInsertionEffect:ie,useLayoutEffect:ie,useMemo:ie,useReducer:ie,useRef:ie,useState:ie,useDebugValue:ie,useDeferredValue:ie,useTransition:ie,useMutableSource:ie,useSyncExternalStore:ie,useId:ie,unstable_isNewReconciler:!1},lf={readContext:nn,useCallback:function(n,i){return pr().memoizedState=[n,i===void 0?null:i],n},useContext:nn,useEffect:ef,useImperativeHandle:function(n,i,u){return u=u!=null?u.concat([n]):null,qs(4194308,4,yc.bind(null,i,n),u)},useLayoutEffect:function(n,i){return qs(4194308,4,n,i)},useInsertionEffect:function(n,i){return qs(4,2,n,i)},useMemo:function(n,i){var u=pr();return i=i===void 0?null:i,n=n(),u.memoizedState=[n,i],n},useReducer:function(n,i,u){var f=pr();return i=u!==void 0?u(i):i,f.memoizedState=f.baseState=i,n={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:n,lastRenderedState:i},f.queue=n,n=n.dispatch=ov.bind(null,mt,n),[f.memoizedState,n]},useRef:function(n){var i=pr();return n={current:n},i.memoizedState=n},useState:Jd,useDebugValue:xc,useDeferredValue:function(n){return pr().memoizedState=n},useTransition:function(){var n=Jd(!1),i=n[0];return n=av.bind(null,n[1]),pr().memoizedState=n,[i,n]},useMutableSource:function(){},useSyncExternalStore:function(n,i,u){var f=mt,g=pr();if(En){if(u===void 0)throw Error(l(407));u=u()}else{if(u=i(),Zn===null)throw Error(l(349));zt&30||Gd(f,i,u)}g.memoizedState=u;var x={value:u,getSnapshot:i};return g.queue=x,ef(Qd.bind(null,f,x,n),[n]),f.flags|=2048,Hl(9,Kd.bind(null,f,x,u,i),void 0,null),u},useId:function(){var n=pr(),i=Zn.identifierPrefix;if(En){var u=Ha,f=Gr;u=(f&~(1<<32-Hr(f)-1)).toString(32)+u,i=":"+i+"R"+u,u=mc++,0<u&&(i+="H"+u.toString(32)),i+=":"}else u=uh++,i=":"+i+"r"+u.toString(32)+":";return n.memoizedState=i},unstable_isNewReconciler:!1},sf={readContext:nn,useCallback:rf,useContext:nn,useEffect:vc,useImperativeHandle:Vl,useInsertionEffect:tf,useLayoutEffect:nf,useMemo:af,useReducer:Ul,useRef:Zd,useState:function(){return Ul(gi)},useDebugValue:xc,useDeferredValue:function(n){var i=hr();return of(i,cn.memoizedState,n)},useTransition:function(){var n=Ul(gi)[0],i=hr().memoizedState;return[n,i]},useMutableSource:Qs,useSyncExternalStore:Yd,useId:Js,unstable_isNewReconciler:!1},wc={readContext:nn,useCallback:rf,useContext:nn,useEffect:vc,useImperativeHandle:Vl,useInsertionEffect:tf,useLayoutEffect:nf,useMemo:af,useReducer:Jo,useRef:Zd,useState:function(){return Jo(gi)},useDebugValue:xc,useDeferredValue:function(n){var i=hr();return cn===null?i.memoizedState=n:of(i,cn.memoizedState,n)},useTransition:function(){var n=Jo(gi)[0],i=hr().memoizedState;return[n,i]},useMutableSource:Qs,useSyncExternalStore:Yd,useId:Js,unstable_isNewReconciler:!1};function vi(n,i){if(n&&n.defaultProps){i=be({},i),n=n.defaultProps;for(var u in n)i[u]===void 0&&(i[u]=n[u]);return i}return i}function ch(n,i,u,f){i=n.memoizedState,u=u(f,i),u=u==null?i:be({},i,u),n.memoizedState=u,n.lanes===0&&(n.updateQueue.baseState=u)}var uf={isMounted:function(n){return(n=n._reactInternals)?Me(n)===n:!1},enqueueSetState:function(n,i,u){n=n._reactInternals;var f=mn(),g=zi(n),x=bo(f,g);x.payload=i,u!=null&&(x.callback=u),i=qo(n,x,g),i!==null&&(Ni(i,n,g,f),Ud(i,n,g))},enqueueReplaceState:function(n,i,u){n=n._reactInternals;var f=mn(),g=zi(n),x=bo(f,g);x.tag=1,x.payload=i,u!=null&&(x.callback=u),i=qo(n,x,g),i!==null&&(Ni(i,n,g,f),Ud(i,n,g))},enqueueForceUpdate:function(n,i){n=n._reactInternals;var u=mn(),f=zi(n),g=bo(u,f);g.tag=2,i!=null&&(g.callback=i),i=qo(n,g,f),i!==null&&(Ni(i,n,f,u),Ud(i,n,f))}};function sv(n,i,u,f,g,x,k){return n=n.stateNode,typeof n.shouldComponentUpdate=="function"?n.shouldComponentUpdate(f,x,k):i.prototype&&i.prototype.isPureReactComponent?!qu(u,f)||!qu(g,x):!0}function uv(n,i,u){var f=!1,g=Et,x=i.contextType;return typeof x=="object"&&x!==null?x=nn(x):(g=zn(i)?na:Rn.current,f=i.contextTypes,x=(f=f!=null)?Ai(n,g):Et),i=new i(u,x),n.memoizedState=i.state!==null&&i.state!==void 0?i.state:null,i.updater=uf,n.stateNode=i,i._reactInternals=n,f&&(n=n.stateNode,n.__reactInternalMemoizedUnmaskedChildContext=g,n.__reactInternalMemoizedMaskedChildContext=x),i}function cf(n,i,u,f){n=i.state,typeof i.componentWillReceiveProps=="function"&&i.componentWillReceiveProps(u,f),typeof i.UNSAFE_componentWillReceiveProps=="function"&&i.UNSAFE_componentWillReceiveProps(u,f),i.state!==n&&uf.enqueueReplaceState(i,i.state,null)}function dh(n,i,u,f){var g=n.stateNode;g.props=u,g.state=n.memoizedState,g.refs={},Qo(n);var x=i.contextType;typeof x=="object"&&x!==null?g.context=nn(x):(x=zn(i)?na:Rn.current,g.context=Ai(n,x)),g.state=n.memoizedState,x=i.getDerivedStateFromProps,typeof x=="function"&&(ch(n,i,x,u),g.state=n.memoizedState),typeof i.getDerivedStateFromProps=="function"||typeof g.getSnapshotBeforeUpdate=="function"||typeof g.UNSAFE_componentWillMount!="function"&&typeof g.componentWillMount!="function"||(i=g.state,typeof g.componentWillMount=="function"&&g.componentWillMount(),typeof g.UNSAFE_componentWillMount=="function"&&g.UNSAFE_componentWillMount(),i!==g.state&&uf.enqueueReplaceState(g,g.state,null),Hd(n,u,g,f),g.state=n.memoizedState),typeof g.componentDidMount=="function"&&(n.flags|=4194308)}function el(n,i){try{var u="",f=i;do u+=it(f),f=f.return;while(f);var g=u}catch(x){g=`
Error generating stack: `+x.message+`
`+x.stack}return{value:n,source:i,stack:g,digest:null}}function df(n,i,u){return{value:n,source:null,stack:u??null,digest:i??null}}function fh(n,i){try{console.error(i.value)}catch(u){setTimeout(function(){throw u})}}var W0=typeof WeakMap=="function"?WeakMap:Map;function Sc(n,i,u){u=bo(-1,u),u.tag=3,u.payload={element:null};var f=i.value;return u.callback=function(){nl||(nl=!0,$c=f),fh(n,i)},u}function cv(n,i,u){u=bo(-1,u),u.tag=3;var f=n.type.getDerivedStateFromError;if(typeof f=="function"){var g=i.value;u.payload=function(){return f(g)},u.callback=function(){fh(n,i)}}var x=n.stateNode;return x!==null&&typeof x.componentDidCatch=="function"&&(u.callback=function(){fh(n,i),typeof f!="function"&&(oa===null?oa=new Set([this]):oa.add(this));var k=i.stack;this.componentDidCatch(i.value,{componentStack:k!==null?k:""})}),u}function ph(n,i,u){var f=n.pingCache;if(f===null){f=n.pingCache=new W0;var g=new Set;f.set(i,g)}else g=f.get(i),g===void 0&&(g=new Set,f.set(i,g));g.has(u)||(g.add(u),n=kh.bind(null,n,i,u),i.then(n,n))}function hh(n){do{var i;if((i=n.tag===13)&&(i=n.memoizedState,i=i!==null?i.dehydrated!==null:!0),i)return n;n=n.return}while(n!==null);return null}function dv(n,i,u,f,g){return n.mode&1?(n.flags|=65536,n.lanes=g,n):(n===i?n.flags|=65536:(n.flags|=128,u.flags|=131072,u.flags&=-52805,u.tag===1&&(u.alternate===null?u.tag=17:(i=bo(-1,1),i.tag=2,qo(u,i,1))),u.lanes|=1),n)}var Wl=ce.ReactCurrentOwner,gr=!1;function Un(n,i,u,f){i.child=n===null?Sr(i,null,u,f):Ea(i,n.child,u,f)}function ff(n,i,u,f,g){u=u.render;var x=i.ref;return Ys(i,g),f=nt(n,i,u,f,x,g),u=Xo(),n!==null&&!gr?(i.updateQueue=n.updateQueue,i.flags&=-2053,n.lanes&=~g,Cr(n,i,g)):(En&&u&&Kp(i),i.flags|=1,Un(n,i,f,g),i.child)}function yi(n,i,u,f,g){if(n===null){var x=u.type;return typeof x=="function"&&!Dh(x)&&x.defaultProps===void 0&&u.compare===null&&u.defaultProps===void 0?(i.tag=15,i.type=x,Yl(n,i,x,f,g)):(n=Rf(u.type,null,f,i,i.mode,g),n.ref=i.ref,n.return=i,i.child=n)}if(x=n.child,!(n.lanes&g)){var k=x.memoizedProps;if(u=u.compare,u=u!==null?u:qu,u(k,f)&&n.ref===i.ref)return Cr(n,i,g)}return i.flags|=1,n=al(x,f),n.ref=i.ref,n.return=i,i.child=n}function Yl(n,i,u,f,g){if(n!==null){var x=n.memoizedProps;if(qu(x,f)&&n.ref===i.ref)if(gr=!1,i.pendingProps=f=x,(n.lanes&g)!==0)n.flags&131072&&(gr=!0);else return i.lanes=n.lanes,Cr(n,i,g)}return pf(n,i,u,f,g)}function xt(n,i,u){var f=i.pendingProps,g=f.children,x=n!==null?n.memoizedState:null;if(f.mode==="hidden")if(!(i.mode&1))i.memoizedState={baseLanes:0,cachePool:null,transitions:null},gn(nu,Li),Li|=u;else{if(!(u&1073741824))return n=x!==null?x.baseLanes|u:u,i.lanes=i.childLanes=1073741824,i.memoizedState={baseLanes:n,cachePool:null,transitions:null},i.updateQueue=null,gn(nu,Li),Li|=n,null;i.memoizedState={baseLanes:0,cachePool:null,transitions:null},f=x!==null?x.baseLanes:u,gn(nu,Li),Li|=f}else x!==null?(f=x.baseLanes|u,i.memoizedState=null):f=u,gn(nu,Li),Li|=f;return Un(n,i,g,u),i.child}function Cc(n,i){var u=i.ref;(n===null&&u!==null||n!==null&&n.ref!==u)&&(i.flags|=512,i.flags|=2097152)}function pf(n,i,u,f,g){var x=zn(u)?na:Rn.current;return x=Ai(i,x),Ys(i,g),u=nt(n,i,u,f,x,g),f=Xo(),n!==null&&!gr?(i.updateQueue=n.updateQueue,i.flags&=-2053,n.lanes&=~g,Cr(n,i,g)):(En&&f&&Kp(i),i.flags|=1,Un(n,i,u,g),i.child)}function Y0(n,i,u,f,g){if(zn(u)){var x=!0;Nl(i)}else x=!1;if(Ys(i,g),i.stateNode===null)ia(n,i),uv(i,u,f),dh(i,u,f,g),f=!0;else if(n===null){var k=i.stateNode,_=i.memoizedProps;k.props=_;var F=k.context,J=u.contextType;typeof J=="object"&&J!==null?J=nn(J):(J=zn(u)?na:Rn.current,J=Ai(i,J));var ge=u.getDerivedStateFromProps,me=typeof ge=="function"||typeof k.getSnapshotBeforeUpdate=="function";me||typeof k.UNSAFE_componentWillReceiveProps!="function"&&typeof k.componentWillReceiveProps!="function"||(_!==f||F!==J)&&cf(i,k,f,J),ra=!1;var he=i.memoizedState;k.state=he,Hd(i,f,k,g),F=i.memoizedState,_!==f||he!==F||qn.current||ra?(typeof ge=="function"&&(ch(i,u,ge,f),F=i.memoizedState),(_=ra||sv(i,u,_,f,he,F,J))?(me||typeof k.UNSAFE_componentWillMount!="function"&&typeof k.componentWillMount!="function"||(typeof k.componentWillMount=="function"&&k.componentWillMount(),typeof k.UNSAFE_componentWillMount=="function"&&k.UNSAFE_componentWillMount()),typeof k.componentDidMount=="function"&&(i.flags|=4194308)):(typeof k.componentDidMount=="function"&&(i.flags|=4194308),i.memoizedProps=f,i.memoizedState=F),k.props=f,k.state=F,k.context=J,f=_):(typeof k.componentDidMount=="function"&&(i.flags|=4194308),f=!1)}else{k=i.stateNode,rv(n,i),_=i.memoizedProps,J=i.type===i.elementType?_:vi(i.type,_),k.props=J,me=i.pendingProps,he=k.context,F=u.contextType,typeof F=="object"&&F!==null?F=nn(F):(F=zn(u)?na:Rn.current,F=Ai(i,F));var _e=u.getDerivedStateFromProps;(ge=typeof _e=="function"||typeof k.getSnapshotBeforeUpdate=="function")||typeof k.UNSAFE_componentWillReceiveProps!="function"&&typeof k.componentWillReceiveProps!="function"||(_!==me||he!==F)&&cf(i,k,f,F),ra=!1,he=i.memoizedState,k.state=he,Hd(i,f,k,g);var Fe=i.memoizedState;_!==me||he!==Fe||qn.current||ra?(typeof _e=="function"&&(ch(i,u,_e,f),Fe=i.memoizedState),(J=ra||sv(i,u,J,f,he,Fe,F)||!1)?(ge||typeof k.UNSAFE_componentWillUpdate!="function"&&typeof k.componentWillUpdate!="function"||(typeof k.componentWillUpdate=="function"&&k.componentWillUpdate(f,Fe,F),typeof k.UNSAFE_componentWillUpdate=="function"&&k.UNSAFE_componentWillUpdate(f,Fe,F)),typeof k.componentDidUpdate=="function"&&(i.flags|=4),typeof k.getSnapshotBeforeUpdate=="function"&&(i.flags|=1024)):(typeof k.componentDidUpdate!="function"||_===n.memoizedProps&&he===n.memoizedState||(i.flags|=4),typeof k.getSnapshotBeforeUpdate!="function"||_===n.memoizedProps&&he===n.memoizedState||(i.flags|=1024),i.memoizedProps=f,i.memoizedState=Fe),k.props=f,k.state=Fe,k.context=F,f=J):(typeof k.componentDidUpdate!="function"||_===n.memoizedProps&&he===n.memoizedState||(i.flags|=4),typeof k.getSnapshotBeforeUpdate!="function"||_===n.memoizedProps&&he===n.memoizedState||(i.flags|=1024),f=!1)}return gh(n,i,u,f,x,g)}function gh(n,i,u,f,g,x){Cc(n,i);var k=(i.flags&128)!==0;if(!f&&!k)return g&&Pr(i,u,!1),Cr(n,i,x);f=i.stateNode,Wl.current=i;var _=k&&typeof u.getDerivedStateFromError!="function"?null:f.render();return i.flags|=1,n!==null&&k?(i.child=Ea(i,n.child,null,x),i.child=Ea(i,null,_,x)):Un(n,i,_,x),i.memoizedState=f.state,g&&Pr(i,u,!0),i.child}function hf(n){var i=n.stateNode;i.pendingContext?Bd(n,i.pendingContext,i.pendingContext!==i.context):i.context&&Bd(n,i.context,!1),ah(n,i.containerInfo)}function Zs(n,i,u,f,g){return xo(),cc(g),i.flags|=256,Un(n,i,u,f),i.child}var mh={dehydrated:null,treeContext:null,retryLane:0};function gf(n){return{baseLanes:n,cachePool:null,transitions:null}}function fv(n,i,u){var f=i.pendingProps,g=Dn.current,x=!1,k=(i.flags&128)!==0,_;if((_=k)||(_=n!==null&&n.memoizedState===null?!1:(g&2)!==0),_?(x=!0,i.flags&=-129):(n===null||n.memoizedState!==null)&&(g|=1),gn(Dn,g&1),n===null)return Jp(i),n=i.memoizedState,n!==null&&(n=n.dehydrated,n!==null)?(i.mode&1?n.data==="$!"?i.lanes=8:i.lanes=1073741824:i.lanes=1,null):(k=f.children,n=f.fallback,x?(f=i.mode,x=i.child,k={mode:"hidden",children:k},!(f&1)&&x!==null?(x.childLanes=0,x.pendingProps=k):x=su(k,f,0,null),n=ol(n,f,u,null),x.return=i,n.return=i,x.sibling=n,i.child=x,i.child.memoizedState=gf(u),i.memoizedState=mh,n):Ec(i,k));if(g=n.memoizedState,g!==null&&(_=g.dehydrated,_!==null))return pv(n,i,k,f,_,g,u);if(x){x=f.fallback,k=i.mode,g=n.child,_=g.sibling;var F={mode:"hidden",children:f.children};return!(k&1)&&i.child!==g?(f=i.child,f.childLanes=0,f.pendingProps=F,i.deletions=null):(f=al(g,F),f.subtreeFlags=g.subtreeFlags&14680064),_!==null?x=al(_,x):(x=ol(x,k,u,null),x.flags|=2),x.return=i,f.return=i,f.sibling=x,i.child=f,f=x,x=i.child,k=n.child.memoizedState,k=k===null?gf(u):{baseLanes:k.baseLanes|u,cachePool:null,transitions:k.transitions},x.memoizedState=k,x.childLanes=n.childLanes&~u,i.memoizedState=mh,f}return x=n.child,n=x.sibling,f=al(x,{mode:"visible",children:f.children}),!(i.mode&1)&&(f.lanes=u),f.return=i,f.sibling=null,n!==null&&(u=i.deletions,u===null?(i.deletions=[n],i.flags|=16):u.push(n)),i.child=f,i.memoizedState=null,f}function Ec(n,i){return i=su({mode:"visible",children:i},n.mode,0,null),i.return=n,n.child=i}function mf(n,i,u,f){return f!==null&&cc(f),Ea(i,n.child,null,u),n=Ec(i,i.pendingProps.children),n.flags|=2,i.memoizedState=null,n}function pv(n,i,u,f,g,x,k){if(u)return i.flags&256?(i.flags&=-257,f=df(Error(l(422))),mf(n,i,k,f)):i.memoizedState!==null?(i.child=n.child,i.flags|=128,null):(x=f.fallback,g=i.mode,f=su({mode:"visible",children:f.children},g,0,null),x=ol(x,g,k,null),x.flags|=2,f.return=i,x.return=i,f.sibling=x,i.child=f,i.mode&1&&Ea(i,n.child,null,k),i.child.memoizedState=gf(k),i.memoizedState=mh,x);if(!(i.mode&1))return mf(n,i,k,null);if(g.data==="$!"){if(f=g.nextSibling&&g.nextSibling.dataset,f)var _=f.dgst;return f=_,x=Error(l(419)),f=df(x,f,void 0),mf(n,i,k,f)}if(_=(k&n.childLanes)!==0,gr||_){if(f=Zn,f!==null){switch(k&-k){case 4:g=2;break;case 16:g=8;break;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:g=32;break;case 536870912:g=268435456;break;default:g=0}g=g&(f.suspendedLanes|k)?0:g,g!==0&&g!==x.retryLane&&(x.retryLane=g,Va(n,g),Ni(f,n,g,-1))}return Eh(),f=df(Error(l(421))),mf(n,i,k,f)}return g.data==="$?"?(i.flags|=128,i.child=n.child,i=J0.bind(null,n),g._reactRetry=i,null):(n=x.treeContext,hi=wa(g.nextSibling),pi=i,En=!0,Ca=null,n!==null&&(wr[Xn++]=Gr,wr[Xn++]=Ha,wr[Xn++]=Pl,Gr=n.id,Ha=n.overflow,Pl=i),i=Ec(i,f.children),i.flags|=4096,i)}function vh(n,i,u){n.lanes|=i;var f=n.alternate;f!==null&&(f.lanes|=i),nh(n.return,i,u)}function vf(n,i,u,f,g){var x=n.memoizedState;x===null?n.memoizedState={isBackwards:i,rendering:null,renderingStartTime:0,last:f,tail:u,tailMode:g}:(x.isBackwards=i,x.rendering=null,x.renderingStartTime=0,x.last=f,x.tail=u,x.tailMode=g)}function xi(n,i,u){var f=i.pendingProps,g=f.revealOrder,x=f.tail;if(Un(n,i,f.children,u),f=Dn.current,f&2)f=f&1|2,i.flags|=128;else{if(n!==null&&n.flags&128)e:for(n=i.child;n!==null;){if(n.tag===13)n.memoizedState!==null&&vh(n,u,i);else if(n.tag===19)vh(n,u,i);else if(n.child!==null){n.child.return=n,n=n.child;continue}if(n===i)break e;for(;n.sibling===null;){if(n.return===null||n.return===i)break e;n=n.return}n.sibling.return=n.return,n=n.sibling}f&=1}if(gn(Dn,f),!(i.mode&1))i.memoizedState=null;else switch(g){case"forwards":for(u=i.child,g=null;u!==null;)n=u.alternate,n!==null&&Vd(n)===null&&(g=u),u=u.sibling;u=g,u===null?(g=i.child,i.child=null):(g=u.sibling,u.sibling=null),vf(i,!1,g,u,x);break;case"backwards":for(u=null,g=i.child,i.child=null;g!==null;){if(n=g.alternate,n!==null&&Vd(n)===null){i.child=g;break}n=g.sibling,g.sibling=u,u=g,g=n}vf(i,!0,u,null,x);break;case"together":vf(i,!1,null,null,void 0);break;default:i.memoizedState=null}return i.child}function ia(n,i){!(i.mode&1)&&n!==null&&(n.alternate=null,i.alternate=null,i.flags|=2)}function Cr(n,i,u){if(n!==null&&(i.dependencies=n.dependencies),ql|=i.lanes,!(u&i.childLanes))return null;if(n!==null&&i.child!==n.child)throw Error(l(153));if(i.child!==null){for(n=i.child,u=al(n,n.pendingProps),i.child=u,u.return=i;n.sibling!==null;)n=n.sibling,u=u.sibling=al(n,n.pendingProps),u.return=i;u.sibling=null}return i.child}function yf(n,i,u){switch(i.tag){case 3:hf(i),xo();break;case 5:oh(i);break;case 1:zn(i.type)&&Nl(i);break;case 4:ah(i,i.stateNode.containerInfo);break;case 10:var f=i.type._context,g=i.memoizedProps.value;gn(Ce,f._currentValue),f._currentValue=g;break;case 13:if(f=i.memoizedState,f!==null)return f.dehydrated!==null?(gn(Dn,Dn.current&1),i.flags|=128,null):u&i.child.childLanes?fv(n,i,u):(gn(Dn,Dn.current&1),n=Cr(n,i,u),n!==null?n.sibling:null);gn(Dn,Dn.current&1);break;case 19:if(f=(u&i.childLanes)!==0,n.flags&128){if(f)return xi(n,i,u);i.flags|=128}if(g=i.memoizedState,g!==null&&(g.rendering=null,g.tail=null,g.lastEffect=null),gn(Dn,Dn.current),f)break;return null;case 22:case 23:return i.lanes=0,xt(n,i,u)}return Cr(n,i,u)}var eu,_i,ir,hv;eu=function(n,i){for(var u=i.child;u!==null;){if(u.tag===5||u.tag===6)n.appendChild(u.stateNode);else if(u.tag!==4&&u.child!==null){u.child.return=u,u=u.child;continue}if(u===i)break;for(;u.sibling===null;){if(u.return===null||u.return===i)return;u=u.return}u.sibling.return=u.return,u=u.sibling}},_i=function(){},ir=function(n,i,u,f){var g=n.memoizedProps;if(g!==f){n=i.stateNode,Il(Wa.current);var x=null;switch(u){case"input":g=xn(n,g),f=xn(n,f),x=[];break;case"select":g=be({},g,{value:void 0}),f=be({},f,{value:void 0}),x=[];break;case"textarea":g=ur(n,g),f=ur(n,f),x=[];break;default:typeof g.onClick!="function"&&typeof f.onClick=="function"&&(n.onclick=zd)}wn(u,f);var k;u=null;for(J in g)if(!f.hasOwnProperty(J)&&g.hasOwnProperty(J)&&g[J]!=null)if(J==="style"){var _=g[J];for(k in _)_.hasOwnProperty(k)&&(u||(u={}),u[k]="")}else J!=="dangerouslySetInnerHTML"&&J!=="children"&&J!=="suppressContentEditableWarning"&&J!=="suppressHydrationWarning"&&J!=="autoFocus"&&(h.hasOwnProperty(J)?x||(x=[]):(x=x||[]).push(J,null));for(J in f){var F=f[J];if(_=g!=null?g[J]:void 0,f.hasOwnProperty(J)&&F!==_&&(F!=null||_!=null))if(J==="style")if(_){for(k in _)!_.hasOwnProperty(k)||F&&F.hasOwnProperty(k)||(u||(u={}),u[k]="");for(k in F)F.hasOwnProperty(k)&&_[k]!==F[k]&&(u||(u={}),u[k]=F[k])}else u||(x||(x=[]),x.push(J,u)),u=F;else J==="dangerouslySetInnerHTML"?(F=F?F.__html:void 0,_=_?_.__html:void 0,F!=null&&_!==F&&(x=x||[]).push(J,F)):J==="children"?typeof F!="string"&&typeof F!="number"||(x=x||[]).push(J,""+F):J!=="suppressContentEditableWarning"&&J!=="suppressHydrationWarning"&&(h.hasOwnProperty(J)?(F!=null&&J==="onScroll"&&Xt("scroll",n),x||_===F||(x=[])):(x=x||[]).push(J,F))}u&&(x=x||[]).push("style",u);var J=x;(i.updateQueue=J)&&(i.flags|=4)}},hv=function(n,i,u,f){u!==f&&(i.flags|=4)};function Tc(n,i){if(!En)switch(n.tailMode){case"hidden":i=n.tail;for(var u=null;i!==null;)i.alternate!==null&&(u=i),i=i.sibling;u===null?n.tail=null:u.sibling=null;break;case"collapsed":u=n.tail;for(var f=null;u!==null;)u.alternate!==null&&(f=u),u=u.sibling;f===null?i||n.tail===null?n.tail=null:n.tail.sibling=null:f.sibling=null}}function Br(n){var i=n.alternate!==null&&n.alternate.child===n.child,u=0,f=0;if(i)for(var g=n.child;g!==null;)u|=g.lanes|g.childLanes,f|=g.subtreeFlags&14680064,f|=g.flags&14680064,g.return=n,g=g.sibling;else for(g=n.child;g!==null;)u|=g.lanes|g.childLanes,f|=g.subtreeFlags,f|=g.flags,g.return=n,g=g.sibling;return n.subtreeFlags|=f,n.childLanes=u,i}function yh(n,i,u){var f=i.pendingProps;switch(Id(i),i.tag){case 2:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return Br(i),null;case 1:return zn(i.type)&&Ua(),Br(i),null;case 3:return f=i.stateNode,Ks(),tn(qn),tn(Rn),hc(),f.pendingContext&&(f.context=f.pendingContext,f.pendingContext=null),(n===null||n.child===null)&&(In(i)?i.flags|=4:n===null||n.memoizedState.isDehydrated&&!(i.flags&256)||(i.flags|=1024,Ca!==null&&(_c(Ca),Ca=null))),_i(n,i),Br(i),null;case 5:lh(i);var g=Il(pc.current);if(u=i.type,n!==null&&i.stateNode!=null)ir(n,i,u,f,g),n.ref!==i.ref&&(i.flags|=512,i.flags|=2097152);else{if(!f){if(i.stateNode===null)throw Error(l(166));return Br(i),null}if(n=Il(Wa.current),In(i)){f=i.stateNode,u=i.type;var x=i.memoizedProps;switch(f[ta]=i,f[oc]=x,n=(i.mode&1)!==0,u){case"dialog":Xt("cancel",f),Xt("close",f);break;case"iframe":case"object":case"embed":Xt("load",f);break;case"video":case"audio":for(g=0;g<tc.length;g++)Xt(tc[g],f);break;case"source":Xt("error",f);break;case"img":case"image":case"link":Xt("error",f),Xt("load",f);break;case"details":Xt("toggle",f);break;case"input":An(f,x),Xt("invalid",f);break;case"select":f._wrapperState={wasMultiple:!!x.multiple},Xt("invalid",f);break;case"textarea":cr(f,x),Xt("invalid",f)}wn(u,x),g=null;for(var k in x)if(x.hasOwnProperty(k)){var _=x[k];k==="children"?typeof _=="string"?f.textContent!==_&&(x.suppressHydrationWarning!==!0&&Ld(f.textContent,_,n),g=["children",_]):typeof _=="number"&&f.textContent!==""+_&&(x.suppressHydrationWarning!==!0&&Ld(f.textContent,_,n),g=["children",""+_]):h.hasOwnProperty(k)&&_!=null&&k==="onScroll"&&Xt("scroll",f)}switch(u){case"input":pn(f),Ri(f,x,!0);break;case"textarea":pn(f),ma(f);break;case"select":case"option":break;default:typeof x.onClick=="function"&&(f.onclick=zd)}f=g,i.updateQueue=f,f!==null&&(i.flags|=4)}else{k=g.nodeType===9?g:g.ownerDocument,n==="http://www.w3.org/1999/xhtml"&&(n=Kn(u)),n==="http://www.w3.org/1999/xhtml"?u==="script"?(n=k.createElement("div"),n.innerHTML="<script><\/script>",n=n.removeChild(n.firstChild)):typeof f.is=="string"?n=k.createElement(u,{is:f.is}):(n=k.createElement(u),u==="select"&&(k=n,f.multiple?k.multiple=!0:f.size&&(k.size=f.size))):n=k.createElementNS(n,u),n[ta]=i,n[oc]=f,eu(n,i,!1,!1),i.stateNode=n;e:{switch(k=Sn(u,f),u){case"dialog":Xt("cancel",n),Xt("close",n),g=f;break;case"iframe":case"object":case"embed":Xt("load",n),g=f;break;case"video":case"audio":for(g=0;g<tc.length;g++)Xt(tc[g],n);g=f;break;case"source":Xt("error",n),g=f;break;case"img":case"image":case"link":Xt("error",n),Xt("load",n),g=f;break;case"details":Xt("toggle",n),g=f;break;case"input":An(n,f),g=xn(n,f),Xt("invalid",n);break;case"option":g=f;break;case"select":n._wrapperState={wasMultiple:!!f.multiple},g=be({},f,{value:void 0}),Xt("invalid",n);break;case"textarea":cr(n,f),g=ur(n,f),Xt("invalid",n);break;default:g=f}wn(u,g),_=g;for(x in _)if(_.hasOwnProperty(x)){var F=_[x];x==="style"?Gt(n,F):x==="dangerouslySetInnerHTML"?(F=F?F.__html:void 0,F!=null&&ro(n,F)):x==="children"?typeof F=="string"?(u!=="textarea"||F!=="")&&Di(n,F):typeof F=="number"&&Di(n,""+F):x!=="suppressContentEditableWarning"&&x!=="suppressHydrationWarning"&&x!=="autoFocus"&&(h.hasOwnProperty(x)?F!=null&&x==="onScroll"&&Xt("scroll",n):F!=null&&ae(n,x,F,k))}switch(u){case"input":pn(n),Ri(n,f,!1);break;case"textarea":pn(n),ma(n);break;case"option":f.value!=null&&n.setAttribute("value",""+tt(f.value));break;case"select":n.multiple=!!f.multiple,x=f.value,x!=null?nr(n,!!f.multiple,x,!1):f.defaultValue!=null&&nr(n,!!f.multiple,f.defaultValue,!0);break;default:typeof g.onClick=="function"&&(n.onclick=zd)}switch(u){case"button":case"input":case"select":case"textarea":f=!!f.autoFocus;break e;case"img":f=!0;break e;default:f=!1}}f&&(i.flags|=4)}i.ref!==null&&(i.flags|=512,i.flags|=2097152)}return Br(i),null;case 6:if(n&&i.stateNode!=null)hv(n,i,n.memoizedProps,f);else{if(typeof f!="string"&&i.stateNode===null)throw Error(l(166));if(u=Il(pc.current),Il(Wa.current),In(i)){if(f=i.stateNode,u=i.memoizedProps,f[ta]=i,(x=f.nodeValue!==u)&&(n=pi,n!==null))switch(n.tag){case 3:Ld(f.nodeValue,u,(n.mode&1)!==0);break;case 5:n.memoizedProps.suppressHydrationWarning!==!0&&Ld(f.nodeValue,u,(n.mode&1)!==0)}x&&(i.flags|=4)}else f=(u.nodeType===9?u:u.ownerDocument).createTextNode(f),f[ta]=i,i.stateNode=f}return Br(i),null;case 13:if(tn(Dn),f=i.memoizedState,n===null||n.memoizedState!==null&&n.memoizedState.dehydrated!==null){if(En&&hi!==null&&i.mode&1&&!(i.flags&128))Zm(),xo(),i.flags|=98560,x=!1;else if(x=In(i),f!==null&&f.dehydrated!==null){if(n===null){if(!x)throw Error(l(318));if(x=i.memoizedState,x=x!==null?x.dehydrated:null,!x)throw Error(l(317));x[ta]=i}else xo(),!(i.flags&128)&&(i.memoizedState=null),i.flags|=4;Br(i),x=!1}else Ca!==null&&(_c(Ca),Ca=null),x=!0;if(!x)return i.flags&65536?i:null}return i.flags&128?(i.lanes=u,i):(f=f!==null,f!==(n!==null&&n.memoizedState!==null)&&f&&(i.child.flags|=8192,i.mode&1&&(n===null||Dn.current&1?ar===0&&(ar=3):Eh())),i.updateQueue!==null&&(i.flags|=4),Br(i),null);case 4:return Ks(),_i(n,i),n===null&&rc(i.stateNode.containerInfo),Br(i),null;case 10:return th(i.type._context),Br(i),null;case 17:return zn(i.type)&&Ua(),Br(i),null;case 19:if(tn(Dn),x=i.memoizedState,x===null)return Br(i),null;if(f=(i.flags&128)!==0,k=x.rendering,k===null)if(f)Tc(x,!1);else{if(ar!==0||n!==null&&n.flags&128)for(n=i.child;n!==null;){if(k=Vd(n),k!==null){for(i.flags|=128,Tc(x,!1),f=k.updateQueue,f!==null&&(i.updateQueue=f,i.flags|=4),i.subtreeFlags=0,f=u,u=i.child;u!==null;)x=u,n=f,x.flags&=14680066,k=x.alternate,k===null?(x.childLanes=0,x.lanes=n,x.child=null,x.subtreeFlags=0,x.memoizedProps=null,x.memoizedState=null,x.updateQueue=null,x.dependencies=null,x.stateNode=null):(x.childLanes=k.childLanes,x.lanes=k.lanes,x.child=k.child,x.subtreeFlags=0,x.deletions=null,x.memoizedProps=k.memoizedProps,x.memoizedState=k.memoizedState,x.updateQueue=k.updateQueue,x.type=k.type,n=k.dependencies,x.dependencies=n===null?null:{lanes:n.lanes,firstContext:n.firstContext}),u=u.sibling;return gn(Dn,Dn.current&1|2),i.child}n=n.sibling}x.tail!==null&&Qt()>iu&&(i.flags|=128,f=!0,Tc(x,!1),i.lanes=4194304)}else{if(!f)if(n=Vd(k),n!==null){if(i.flags|=128,f=!0,u=n.updateQueue,u!==null&&(i.updateQueue=u,i.flags|=4),Tc(x,!0),x.tail===null&&x.tailMode==="hidden"&&!k.alternate&&!En)return Br(i),null}else 2*Qt()-x.renderingStartTime>iu&&u!==1073741824&&(i.flags|=128,f=!0,Tc(x,!1),i.lanes=4194304);x.isBackwards?(k.sibling=i.child,i.child=k):(u=x.last,u!==null?u.sibling=k:i.child=k,x.last=k)}return x.tail!==null?(i=x.tail,x.rendering=i,x.tail=i.sibling,x.renderingStartTime=Qt(),i.sibling=null,u=Dn.current,gn(Dn,f?u&1|2:u&1),i):(Br(i),null);case 22:case 23:return Ch(),f=i.memoizedState!==null,n!==null&&n.memoizedState!==null!==f&&(i.flags|=8192),f&&i.mode&1?Li&1073741824&&(Br(i),i.subtreeFlags&6&&(i.flags|=8192)):Br(i),null;case 24:return null;case 25:return null}throw Error(l(156,i.tag))}function gv(n,i){switch(Id(i),i.tag){case 1:return zn(i.type)&&Ua(),n=i.flags,n&65536?(i.flags=n&-65537|128,i):null;case 3:return Ks(),tn(qn),tn(Rn),hc(),n=i.flags,n&65536&&!(n&128)?(i.flags=n&-65537|128,i):null;case 5:return lh(i),null;case 13:if(tn(Dn),n=i.memoizedState,n!==null&&n.dehydrated!==null){if(i.alternate===null)throw Error(l(340));xo()}return n=i.flags,n&65536?(i.flags=n&-65537|128,i):null;case 19:return tn(Dn),null;case 4:return Ks(),null;case 10:return th(i.type._context),null;case 22:case 23:return Ch(),null;case 24:return null;default:return null}}var Gl=!1,Er=!1,G0=typeof WeakSet=="function"?WeakSet:Set,Ne=null;function tl(n,i){var u=n.ref;if(u!==null)if(typeof u=="function")try{u(null)}catch(f){Nn(n,i,f)}else u.current=null}function xh(n,i,u){try{u()}catch(f){Nn(n,i,f)}}var bh=!1;function K0(n,i){if(_l=Fo,n=Uo(),Ls(n)){if("selectionStart"in n)var u={start:n.selectionStart,end:n.selectionEnd};else e:{u=(u=n.ownerDocument)&&u.defaultView||window;var f=u.getSelection&&u.getSelection();if(f&&f.rangeCount!==0){u=f.anchorNode;var g=f.anchorOffset,x=f.focusNode;f=f.focusOffset;try{u.nodeType,x.nodeType}catch{u=null;break e}var k=0,_=-1,F=-1,J=0,ge=0,me=n,he=null;t:for(;;){for(var _e;me!==u||g!==0&&me.nodeType!==3||(_=k+g),me!==x||f!==0&&me.nodeType!==3||(F=k+f),me.nodeType===3&&(k+=me.nodeValue.length),(_e=me.firstChild)!==null;)he=me,me=_e;for(;;){if(me===n)break t;if(he===u&&++J===g&&(_=k),he===x&&++ge===f&&(F=k),(_e=me.nextSibling)!==null)break;me=he,he=me.parentNode}me=_e}u=_===-1||F===-1?null:{start:_,end:F}}else u=null}u=u||{start:0,end:0}}else u=null;for(ac={focusedElem:n,selectionRange:u},Fo=!1,Ne=i;Ne!==null;)if(i=Ne,n=i.child,(i.subtreeFlags&1028)!==0&&n!==null)n.return=i,Ne=n;else for(;Ne!==null;){i=Ne;try{var Fe=i.alternate;if(i.flags&1024)switch(i.tag){case 0:case 11:case 15:break;case 1:if(Fe!==null){var Ie=Fe.memoizedProps,Vn=Fe.memoizedState,W=i.stateNode,I=W.getSnapshotBeforeUpdate(i.elementType===i.type?Ie:vi(i.type,Ie),Vn);W.__reactInternalSnapshotBeforeUpdate=I}break;case 3:var Q=i.stateNode.containerInfo;Q.nodeType===1?Q.textContent="":Q.nodeType===9&&Q.documentElement&&Q.removeChild(Q.documentElement);break;case 5:case 6:case 4:case 17:break;default:throw Error(l(163))}}catch(ye){Nn(i,i.return,ye)}if(n=i.sibling,n!==null){n.return=i.return,Ne=n;break}Ne=i.return}return Fe=bh,bh=!1,Fe}function tu(n,i,u){var f=i.updateQueue;if(f=f!==null?f.lastEffect:null,f!==null){var g=f=f.next;do{if((g.tag&n)===n){var x=g.destroy;g.destroy=void 0,x!==void 0&&xh(i,u,x)}g=g.next}while(g!==f)}}function xf(n,i){if(i=i.updateQueue,i=i!==null?i.lastEffect:null,i!==null){var u=i=i.next;do{if((u.tag&n)===n){var f=u.create;u.destroy=f()}u=u.next}while(u!==i)}}function bf(n){var i=n.ref;if(i!==null){var u=n.stateNode;switch(n.tag){case 5:n=u;break;default:n=u}typeof i=="function"?i(n):i.current=n}}function mv(n){var i=n.alternate;i!==null&&(n.alternate=null,mv(i)),n.child=null,n.deletions=null,n.sibling=null,n.tag===5&&(i=n.stateNode,i!==null&&(delete i[ta],delete i[oc],delete i[Wp],delete i[Yp],delete i[Hs])),n.stateNode=null,n.return=null,n.dependencies=null,n.memoizedProps=null,n.memoizedState=null,n.pendingProps=null,n.stateNode=null,n.updateQueue=null}function wf(n){return n.tag===5||n.tag===3||n.tag===4}function kc(n){e:for(;;){for(;n.sibling===null;){if(n.return===null||wf(n.return))return null;n=n.return}for(n.sibling.return=n.return,n=n.sibling;n.tag!==5&&n.tag!==6&&n.tag!==18;){if(n.flags&2||n.child===null||n.tag===4)continue e;n.child.return=n,n=n.child}if(!(n.flags&2))return n.stateNode}}function Ya(n,i,u){var f=n.tag;if(f===5||f===6)n=n.stateNode,i?u.nodeType===8?u.parentNode.insertBefore(n,i):u.insertBefore(n,i):(u.nodeType===8?(i=u.parentNode,i.insertBefore(n,u)):(i=u,i.appendChild(n)),u=u._reactRootContainer,u!=null||i.onclick!==null||(i.onclick=zd));else if(f!==4&&(n=n.child,n!==null))for(Ya(n,i,u),n=n.sibling;n!==null;)Ya(n,i,u),n=n.sibling}function Ga(n,i,u){var f=n.tag;if(f===5||f===6)n=n.stateNode,i?u.insertBefore(n,i):u.appendChild(n);else if(f!==4&&(n=n.child,n!==null))for(Ga(n,i,u),n=n.sibling;n!==null;)Ga(n,i,u),n=n.sibling}var Mn=null,Kr=!1;function aa(n,i,u){for(u=u.child;u!==null;)wo(n,i,u),u=u.sibling}function wo(n,i,u){if(si&&typeof si.onCommitFiberUnmount=="function")try{si.onCommitFiberUnmount(Lo,u)}catch{}switch(u.tag){case 5:Er||tl(u,i);case 6:var f=Mn,g=Kr;Mn=null,aa(n,i,u),Mn=f,Kr=g,Mn!==null&&(Kr?(n=Mn,u=u.stateNode,n.nodeType===8?n.parentNode.removeChild(u):n.removeChild(u)):Mn.removeChild(u.stateNode));break;case 18:Mn!==null&&(Kr?(n=Mn,u=u.stateNode,n.nodeType===8?Is(n.parentNode,u):n.nodeType===1&&Is(n,u),Ji(n)):Is(Mn,u.stateNode));break;case 4:f=Mn,g=Kr,Mn=u.stateNode.containerInfo,Kr=!0,aa(n,i,u),Mn=f,Kr=g;break;case 0:case 11:case 14:case 15:if(!Er&&(f=u.updateQueue,f!==null&&(f=f.lastEffect,f!==null))){g=f=f.next;do{var x=g,k=x.destroy;x=x.tag,k!==void 0&&(x&2||x&4)&&xh(u,i,k),g=g.next}while(g!==f)}aa(n,i,u);break;case 1:if(!Er&&(tl(u,i),f=u.stateNode,typeof f.componentWillUnmount=="function"))try{f.props=u.memoizedProps,f.state=u.memoizedState,f.componentWillUnmount()}catch(_){Nn(u,i,_)}aa(n,i,u);break;case 21:aa(n,i,u);break;case 22:u.mode&1?(Er=(f=Er)||u.memoizedState!==null,aa(n,i,u),Er=f):aa(n,i,u);break;default:aa(n,i,u)}}function vv(n){var i=n.updateQueue;if(i!==null){n.updateQueue=null;var u=n.stateNode;u===null&&(u=n.stateNode=new G0),i.forEach(function(f){var g=Z0.bind(null,n,f);u.has(f)||(u.add(f),f.then(g,g))})}}function Ta(n,i){var u=i.deletions;if(u!==null)for(var f=0;f<u.length;f++){var g=u[f];try{var x=n,k=i,_=k;e:for(;_!==null;){switch(_.tag){case 5:Mn=_.stateNode,Kr=!1;break e;case 3:Mn=_.stateNode.containerInfo,Kr=!0;break e;case 4:Mn=_.stateNode.containerInfo,Kr=!0;break e}_=_.return}if(Mn===null)throw Error(l(160));wo(x,k,g),Mn=null,Kr=!1;var F=g.alternate;F!==null&&(F.return=null),g.return=null}catch(J){Nn(g,i,J)}}if(i.subtreeFlags&12854)for(i=i.child;i!==null;)yv(i,n),i=i.sibling}function yv(n,i){var u=n.alternate,f=n.flags;switch(n.tag){case 0:case 11:case 14:case 15:if(Ta(i,n),ka(n),f&4){try{tu(3,n,n.return),xf(3,n)}catch(Ie){Nn(n,n.return,Ie)}try{tu(5,n,n.return)}catch(Ie){Nn(n,n.return,Ie)}}break;case 1:Ta(i,n),ka(n),f&512&&u!==null&&tl(u,u.return);break;case 5:if(Ta(i,n),ka(n),f&512&&u!==null&&tl(u,u.return),n.flags&32){var g=n.stateNode;try{Di(g,"")}catch(Ie){Nn(n,n.return,Ie)}}if(f&4&&(g=n.stateNode,g!=null)){var x=n.memoizedProps,k=u!==null?u.memoizedProps:x,_=n.type,F=n.updateQueue;if(n.updateQueue=null,F!==null)try{_==="input"&&x.type==="radio"&&x.name!=null&&tr(g,x),Sn(_,k);var J=Sn(_,x);for(k=0;k<F.length;k+=2){var ge=F[k],me=F[k+1];ge==="style"?Gt(g,me):ge==="dangerouslySetInnerHTML"?ro(g,me):ge==="children"?Di(g,me):ae(g,ge,me,J)}switch(_){case"input":Gn(g,x);break;case"textarea":_r(g,x);break;case"select":var he=g._wrapperState.wasMultiple;g._wrapperState.wasMultiple=!!x.multiple;var _e=x.value;_e!=null?nr(g,!!x.multiple,_e,!1):he!==!!x.multiple&&(x.defaultValue!=null?nr(g,!!x.multiple,x.defaultValue,!0):nr(g,!!x.multiple,x.multiple?[]:"",!1))}g[oc]=x}catch(Ie){Nn(n,n.return,Ie)}}break;case 6:if(Ta(i,n),ka(n),f&4){if(n.stateNode===null)throw Error(l(162));g=n.stateNode,x=n.memoizedProps;try{g.nodeValue=x}catch(Ie){Nn(n,n.return,Ie)}}break;case 3:if(Ta(i,n),ka(n),f&4&&u!==null&&u.memoizedState.isDehydrated)try{Ji(i.containerInfo)}catch(Ie){Nn(n,n.return,Ie)}break;case 4:Ta(i,n),ka(n);break;case 13:Ta(i,n),ka(n),g=n.child,g.flags&8192&&(x=g.memoizedState!==null,g.stateNode.isHidden=x,!x||g.alternate!==null&&g.alternate.memoizedState!==null||(Sh=Qt())),f&4&&vv(n);break;case 22:if(ge=u!==null&&u.memoizedState!==null,n.mode&1?(Er=(J=Er)||ge,Ta(i,n),Er=J):Ta(i,n),ka(n),f&8192){if(J=n.memoizedState!==null,(n.stateNode.isHidden=J)&&!ge&&n.mode&1)for(Ne=n,ge=n.child;ge!==null;){for(me=Ne=ge;Ne!==null;){switch(he=Ne,_e=he.child,he.tag){case 0:case 11:case 14:case 15:tu(4,he,he.return);break;case 1:tl(he,he.return);var Fe=he.stateNode;if(typeof Fe.componentWillUnmount=="function"){f=he,u=he.return;try{i=f,Fe.props=i.memoizedProps,Fe.state=i.memoizedState,Fe.componentWillUnmount()}catch(Ie){Nn(f,u,Ie)}}break;case 5:tl(he,he.return);break;case 22:if(he.memoizedState!==null){bv(me);continue}}_e!==null?(_e.return=he,Ne=_e):bv(me)}ge=ge.sibling}e:for(ge=null,me=n;;){if(me.tag===5){if(ge===null){ge=me;try{g=me.stateNode,J?(x=g.style,typeof x.setProperty=="function"?x.setProperty("display","none","important"):x.display="none"):(_=me.stateNode,F=me.memoizedProps.style,k=F!=null&&F.hasOwnProperty("display")?F.display:null,_.style.display=wt("display",k))}catch(Ie){Nn(n,n.return,Ie)}}}else if(me.tag===6){if(ge===null)try{me.stateNode.nodeValue=J?"":me.memoizedProps}catch(Ie){Nn(n,n.return,Ie)}}else if((me.tag!==22&&me.tag!==23||me.memoizedState===null||me===n)&&me.child!==null){me.child.return=me,me=me.child;continue}if(me===n)break e;for(;me.sibling===null;){if(me.return===null||me.return===n)break e;ge===me&&(ge=null),me=me.return}ge===me&&(ge=null),me.sibling.return=me.return,me=me.sibling}}break;case 19:Ta(i,n),ka(n),f&4&&vv(n);break;case 21:break;default:Ta(i,n),ka(n)}}function ka(n){var i=n.flags;if(i&2){try{e:{for(var u=n.return;u!==null;){if(wf(u)){var f=u;break e}u=u.return}throw Error(l(160))}switch(f.tag){case 5:var g=f.stateNode;f.flags&32&&(Di(g,""),f.flags&=-33);var x=kc(n);Ga(n,x,g);break;case 3:case 4:var k=f.stateNode.containerInfo,_=kc(n);Ya(n,_,k);break;default:throw Error(l(161))}}catch(F){Nn(n,n.return,F)}n.flags&=-3}i&4096&&(n.flags&=-4097)}function Rc(n,i,u){Ne=n,xv(n)}function xv(n,i,u){for(var f=(n.mode&1)!==0;Ne!==null;){var g=Ne,x=g.child;if(g.tag===22&&f){var k=g.memoizedState!==null||Gl;if(!k){var _=g.alternate,F=_!==null&&_.memoizedState!==null||Er;_=Gl;var J=Er;if(Gl=k,(Er=F)&&!J)for(Ne=g;Ne!==null;)k=Ne,F=k.child,k.tag===22&&k.memoizedState!==null?Dc(g):F!==null?(F.return=k,Ne=F):Dc(g);for(;x!==null;)Ne=x,xv(x),x=x.sibling;Ne=g,Gl=_,Er=J}wh(n)}else g.subtreeFlags&8772&&x!==null?(x.return=g,Ne=x):wh(n)}}function wh(n){for(;Ne!==null;){var i=Ne;if(i.flags&8772){var u=i.alternate;try{if(i.flags&8772)switch(i.tag){case 0:case 11:case 15:Er||xf(5,i);break;case 1:var f=i.stateNode;if(i.flags&4&&!Er)if(u===null)f.componentDidMount();else{var g=i.elementType===i.type?u.memoizedProps:vi(i.type,u.memoizedProps);f.componentDidUpdate(g,u.memoizedState,f.__reactInternalSnapshotBeforeUpdate)}var x=i.updateQueue;x!==null&&ih(i,x,f);break;case 3:var k=i.updateQueue;if(k!==null){if(u=null,i.child!==null)switch(i.child.tag){case 5:u=i.child.stateNode;break;case 1:u=i.child.stateNode}ih(i,k,u)}break;case 5:var _=i.stateNode;if(u===null&&i.flags&4){u=_;var F=i.memoizedProps;switch(i.type){case"button":case"input":case"select":case"textarea":F.autoFocus&&u.focus();break;case"img":F.src&&(u.src=F.src)}}break;case 6:break;case 4:break;case 12:break;case 13:if(i.memoizedState===null){var J=i.alternate;if(J!==null){var ge=J.memoizedState;if(ge!==null){var me=ge.dehydrated;me!==null&&Ji(me)}}}break;case 19:case 17:case 21:case 22:case 23:case 25:break;default:throw Error(l(163))}Er||i.flags&512&&bf(i)}catch(he){Nn(i,i.return,he)}}if(i===n){Ne=null;break}if(u=i.sibling,u!==null){u.return=i.return,Ne=u;break}Ne=i.return}}function bv(n){for(;Ne!==null;){var i=Ne;if(i===n){Ne=null;break}var u=i.sibling;if(u!==null){u.return=i.return,Ne=u;break}Ne=i.return}}function Dc(n){for(;Ne!==null;){var i=Ne;try{switch(i.tag){case 0:case 11:case 15:var u=i.return;try{xf(4,i)}catch(F){Nn(i,u,F)}break;case 1:var f=i.stateNode;if(typeof f.componentDidMount=="function"){var g=i.return;try{f.componentDidMount()}catch(F){Nn(i,g,F)}}var x=i.return;try{bf(i)}catch(F){Nn(i,x,F)}break;case 5:var k=i.return;try{bf(i)}catch(F){Nn(i,k,F)}}}catch(F){Nn(i,i.return,F)}if(i===n){Ne=null;break}var _=i.sibling;if(_!==null){_.return=i.return,Ne=_;break}Ne=i.return}}var wv=Math.ceil,Sf=ce.ReactCurrentDispatcher,Kl=ce.ReactCurrentOwner,Ir=ce.ReactCurrentBatchConfig,jt=0,Zn=null,Hn=null,Tr=0,Li=0,nu=fi(0),ar=0,Ql=null,ql=0,Xl=0,Mc=0,ru=null,bi=null,Sh=0,iu=1/0,So=null,nl=!1,$c=null,oa=null,Cf=!1,rl=null,Oc=0,au=0,ou=null,Jl=-1,Ac=0;function mn(){return jt&6?Qt():Jl!==-1?Jl:Jl=Qt()}function zi(n){return n.mode&1?jt&2&&Tr!==0?Tr&-Tr:Fl.transition!==null?(Ac===0&&(Ac=Tl()),Ac):(n=Nt,n!==0||(n=window.event,n=n===void 0?16:Wu(n.type)),n):1}function Ni(n,i,u,f){if(50<au)throw au=0,ou=null,Error(l(185));Po(n,u,f),(!(jt&2)||n!==Zn)&&(n===Zn&&(!(jt&2)&&(Xl|=u),ar===4&&il(n,Tr)),mr(n,f),u===1&&jt===0&&!(i.mode&1)&&(iu=Qt()+500,sc&&Yr()))}function mr(n,i){var u=n.callbackNode;Es(n,i);var f=Na(n,n===Zn?Tr:0);if(f===0)u!==null&&Cn(u),n.callbackNode=null,n.callbackPriority=0;else if(i=f&-f,n.callbackPriority!==i){if(u!=null&&Cn(u),i===1)n.tag===0?Gp(zc.bind(null,n)):Vo(zc.bind(null,n)),V0(function(){!(jt&6)&&Yr()}),u=null;else{switch(Vu(f)){case 1:u=gt;break;case 4:u=za;break;case 16:u=lo;break;case 536870912:u=so;break;default:u=lo}u=Rv(u,Sv.bind(null,n))}n.callbackPriority=i,n.callbackNode=u}}function Sv(n,i){if(Jl=-1,Ac=0,jt&6)throw Error(l(327));var u=n.callbackNode;if(lu()&&n.callbackNode!==u)return null;var f=Na(n,n===Zn?Tr:0);if(f===0)return null;if(f&30||f&n.expiredLanes||i)i=kf(n,f);else{i=f;var g=jt;jt|=2;var x=Cv();(Zn!==n||Tr!==i)&&(So=null,iu=Qt()+500,es(n,i));do try{q0();break}catch(_){Tf(n,_)}while(!0);eh(),Sf.current=x,jt=g,Hn!==null?i=0:(Zn=null,Tr=0,i=ar)}if(i!==0){if(i===2&&(g=co(n),g!==0&&(f=g,i=jc(n,g))),i===1)throw u=Ql,es(n,0),il(n,f),mr(n,Qt()),u;if(i===6)il(n,f);else{if(g=n.current.alternate,!(f&30)&&!Lc(g)&&(i=kf(n,f),i===2&&(x=co(n),x!==0&&(f=x,i=jc(n,x))),i===1))throw u=Ql,es(n,0),il(n,f),mr(n,Qt()),u;switch(n.finishedWork=g,n.finishedLanes=f,i){case 0:case 1:throw Error(l(345));case 2:ts(n,bi,So);break;case 3:if(il(n,f),(f&130023424)===f&&(i=Sh+500-Qt(),10<i)){if(Na(n,0)!==0)break;if(g=n.suspendedLanes,(g&f)!==f){mn(),n.pingedLanes|=n.suspendedLanes&g;break}n.timeoutHandle=Nd(ts.bind(null,n,bi,So),i);break}ts(n,bi,So);break;case 4:if(il(n,f),(f&4194240)===f)break;for(i=n.eventTimes,g=-1;0<f;){var k=31-Hr(f);x=1<<k,k=i[k],k>g&&(g=k),f&=~x}if(f=g,f=Qt()-f,f=(120>f?120:480>f?480:1080>f?1080:1920>f?1920:3e3>f?3e3:4320>f?4320:1960*wv(f/1960))-f,10<f){n.timeoutHandle=Nd(ts.bind(null,n,bi,So),f);break}ts(n,bi,So);break;case 5:ts(n,bi,So);break;default:throw Error(l(329))}}}return mr(n,Qt()),n.callbackNode===u?Sv.bind(null,n):null}function jc(n,i){var u=ru;return n.current.memoizedState.isDehydrated&&(es(n,i).flags|=256),n=kf(n,i),n!==2&&(i=bi,bi=u,i!==null&&_c(i)),n}function _c(n){bi===null?bi=n:bi.push.apply(bi,n)}function Lc(n){for(var i=n;;){if(i.flags&16384){var u=i.updateQueue;if(u!==null&&(u=u.stores,u!==null))for(var f=0;f<u.length;f++){var g=u[f],x=g.getSnapshot;g=g.value;try{if(!ba(x(),g))return!1}catch{return!1}}}if(u=i.child,i.subtreeFlags&16384&&u!==null)u.return=i,i=u;else{if(i===n)break;for(;i.sibling===null;){if(i.return===null||i.return===n)return!0;i=i.return}i.sibling.return=i.return,i=i.sibling}}return!0}function il(n,i){for(i&=~Mc,i&=~Xl,n.suspendedLanes|=i,n.pingedLanes&=~i,n=n.expirationTimes;0<i;){var u=31-Hr(i),f=1<<u;n[u]=-1,i&=~f}}function zc(n){if(jt&6)throw Error(l(327));lu();var i=Na(n,0);if(!(i&1))return mr(n,Qt()),null;var u=kf(n,i);if(n.tag!==0&&u===2){var f=co(n);f!==0&&(i=f,u=jc(n,f))}if(u===1)throw u=Ql,es(n,0),il(n,i),mr(n,Qt()),u;if(u===6)throw Error(l(345));return n.finishedWork=n.current.alternate,n.finishedLanes=i,ts(n,bi,So),mr(n,Qt()),null}function Ef(n,i){var u=jt;jt|=1;try{return n(i)}finally{jt=u,jt===0&&(iu=Qt()+500,sc&&Yr())}}function Zl(n){rl!==null&&rl.tag===0&&!(jt&6)&&lu();var i=jt;jt|=1;var u=Ir.transition,f=Nt;try{if(Ir.transition=null,Nt=1,n)return n()}finally{Nt=f,Ir.transition=u,jt=i,!(jt&6)&&Yr()}}function Ch(){Li=nu.current,tn(nu)}function es(n,i){n.finishedWork=null,n.finishedLanes=0;var u=n.timeoutHandle;if(u!==-1&&(n.timeoutHandle=-1,Qm(u)),Hn!==null)for(u=Hn.return;u!==null;){var f=u;switch(Id(f),f.tag){case 1:f=f.type.childContextTypes,f!=null&&Ua();break;case 3:Ks(),tn(qn),tn(Rn),hc();break;case 5:lh(f);break;case 4:Ks();break;case 13:tn(Dn);break;case 19:tn(Dn);break;case 10:th(f.type._context);break;case 22:case 23:Ch()}u=u.return}if(Zn=n,Hn=n=al(n.current,null),Tr=Li=i,ar=0,Ql=null,Mc=Xl=ql=0,bi=ru=null,Bl!==null){for(i=0;i<Bl.length;i++)if(u=Bl[i],f=u.interleaved,f!==null){u.interleaved=null;var g=f.next,x=u.pending;if(x!==null){var k=x.next;x.next=g,f.next=k}u.pending=f}Bl=null}return n}function Tf(n,i){do{var u=Hn;try{if(eh(),Qe.current=rn,Wd){for(var f=mt.memoizedState;f!==null;){var g=f.queue;g!==null&&(g.pending=null),f=f.next}Wd=!1}if(zt=0,rr=cn=mt=null,gc=!1,mc=0,Kl.current=null,u===null||u.return===null){ar=1,Ql=i,Hn=null;break}e:{var x=n,k=u.return,_=u,F=i;if(i=Tr,_.flags|=32768,F!==null&&typeof F=="object"&&typeof F.then=="function"){var J=F,ge=_,me=ge.tag;if(!(ge.mode&1)&&(me===0||me===11||me===15)){var he=ge.alternate;he?(ge.updateQueue=he.updateQueue,ge.memoizedState=he.memoizedState,ge.lanes=he.lanes):(ge.updateQueue=null,ge.memoizedState=null)}var _e=hh(k);if(_e!==null){_e.flags&=-257,dv(_e,k,_,x,i),_e.mode&1&&ph(x,J,i),i=_e,F=J;var Fe=i.updateQueue;if(Fe===null){var Ie=new Set;Ie.add(F),i.updateQueue=Ie}else Fe.add(F);break e}else{if(!(i&1)){ph(x,J,i),Eh();break e}F=Error(l(426))}}else if(En&&_.mode&1){var Vn=hh(k);if(Vn!==null){!(Vn.flags&65536)&&(Vn.flags|=256),dv(Vn,k,_,x,i),cc(el(F,_));break e}}x=F=el(F,_),ar!==4&&(ar=2),ru===null?ru=[x]:ru.push(x),x=k;do{switch(x.tag){case 3:x.flags|=65536,i&=-i,x.lanes|=i;var W=Sc(x,F,i);iv(x,W);break e;case 1:_=F;var I=x.type,Q=x.stateNode;if(!(x.flags&128)&&(typeof I.getDerivedStateFromError=="function"||Q!==null&&typeof Q.componentDidCatch=="function"&&(oa===null||!oa.has(Q)))){x.flags|=65536,i&=-i,x.lanes|=i;var ye=cv(x,_,i);iv(x,ye);break e}}x=x.return}while(x!==null)}Ev(u)}catch($e){i=$e,Hn===u&&u!==null&&(Hn=u=u.return);continue}break}while(!0)}function Cv(){var n=Sf.current;return Sf.current=rn,n===null?rn:n}function Eh(){(ar===0||ar===3||ar===2)&&(ar=4),Zn===null||!(ql&268435455)&&!(Xl&268435455)||il(Zn,Tr)}function kf(n,i){var u=jt;jt|=2;var f=Cv();(Zn!==n||Tr!==i)&&(So=null,es(n,i));do try{Q0();break}catch(g){Tf(n,g)}while(!0);if(eh(),jt=u,Sf.current=f,Hn!==null)throw Error(l(261));return Zn=null,Tr=0,ar}function Q0(){for(;Hn!==null;)Th(Hn)}function q0(){for(;Hn!==null&&!Lr();)Th(Hn)}function Th(n){var i=Rh(n.alternate,n,Li);n.memoizedProps=n.pendingProps,i===null?Ev(n):Hn=i,Kl.current=null}function Ev(n){var i=n;do{var u=i.alternate;if(n=i.return,i.flags&32768){if(u=gv(u,i),u!==null){u.flags&=32767,Hn=u;return}if(n!==null)n.flags|=32768,n.subtreeFlags=0,n.deletions=null;else{ar=6,Hn=null;return}}else if(u=yh(u,i,Li),u!==null){Hn=u;return}if(i=i.sibling,i!==null){Hn=i;return}Hn=i=n}while(i!==null);ar===0&&(ar=5)}function ts(n,i,u){var f=Nt,g=Ir.transition;try{Ir.transition=null,Nt=1,X0(n,i,u,f)}finally{Ir.transition=g,Nt=f}return null}function X0(n,i,u,f){do lu();while(rl!==null);if(jt&6)throw Error(l(327));u=n.finishedWork;var g=n.finishedLanes;if(u===null)return null;if(n.finishedWork=null,n.finishedLanes=0,u===n.current)throw Error(l(177));n.callbackNode=null,n.callbackPriority=0;var x=u.lanes|u.childLanes;if(Uu(n,x),n===Zn&&(Hn=Zn=null,Tr=0),!(u.subtreeFlags&2064)&&!(u.flags&2064)||Cf||(Cf=!0,Rv(lo,function(){return lu(),null})),x=(u.flags&15990)!==0,u.subtreeFlags&15990||x){x=Ir.transition,Ir.transition=null;var k=Nt;Nt=1;var _=jt;jt|=4,Kl.current=null,K0(n,u),yv(u,n),Bm(ac),Fo=!!_l,ac=_l=null,n.current=u,Rc(u),va(),jt=_,Nt=k,Ir.transition=x}else n.current=u;if(Cf&&(Cf=!1,rl=n,Oc=g),x=n.pendingLanes,x===0&&(oa=null),Bu(u.stateNode),mr(n,Qt()),i!==null)for(f=n.onRecoverableError,u=0;u<i.length;u++)g=i[u],f(g.value,{componentStack:g.stack,digest:g.digest});if(nl)throw nl=!1,n=$c,$c=null,n;return Oc&1&&n.tag!==0&&lu(),x=n.pendingLanes,x&1?n===ou?au++:(au=0,ou=n):au=0,Yr(),null}function lu(){if(rl!==null){var n=Vu(Oc),i=Ir.transition,u=Nt;try{if(Ir.transition=null,Nt=16>n?16:n,rl===null)var f=!1;else{if(n=rl,rl=null,Oc=0,jt&6)throw Error(l(331));var g=jt;for(jt|=4,Ne=n.current;Ne!==null;){var x=Ne,k=x.child;if(Ne.flags&16){var _=x.deletions;if(_!==null){for(var F=0;F<_.length;F++){var J=_[F];for(Ne=J;Ne!==null;){var ge=Ne;switch(ge.tag){case 0:case 11:case 15:tu(8,ge,x)}var me=ge.child;if(me!==null)me.return=ge,Ne=me;else for(;Ne!==null;){ge=Ne;var he=ge.sibling,_e=ge.return;if(mv(ge),ge===J){Ne=null;break}if(he!==null){he.return=_e,Ne=he;break}Ne=_e}}}var Fe=x.alternate;if(Fe!==null){var Ie=Fe.child;if(Ie!==null){Fe.child=null;do{var Vn=Ie.sibling;Ie.sibling=null,Ie=Vn}while(Ie!==null)}}Ne=x}}if(x.subtreeFlags&2064&&k!==null)k.return=x,Ne=k;else e:for(;Ne!==null;){if(x=Ne,x.flags&2048)switch(x.tag){case 0:case 11:case 15:tu(9,x,x.return)}var W=x.sibling;if(W!==null){W.return=x.return,Ne=W;break e}Ne=x.return}}var I=n.current;for(Ne=I;Ne!==null;){k=Ne;var Q=k.child;if(k.subtreeFlags&2064&&Q!==null)Q.return=k,Ne=Q;else e:for(k=I;Ne!==null;){if(_=Ne,_.flags&2048)try{switch(_.tag){case 0:case 11:case 15:xf(9,_)}}catch($e){Nn(_,_.return,$e)}if(_===k){Ne=null;break e}var ye=_.sibling;if(ye!==null){ye.return=_.return,Ne=ye;break e}Ne=_.return}}if(jt=g,Yr(),si&&typeof si.onPostCommitFiberRoot=="function")try{si.onPostCommitFiberRoot(Lo,n)}catch{}f=!0}return f}finally{Nt=u,Ir.transition=i}}return!1}function Tv(n,i,u){i=el(u,i),i=Sc(n,i,1),n=qo(n,i,1),i=mn(),n!==null&&(Po(n,1,i),mr(n,i))}function Nn(n,i,u){if(n.tag===3)Tv(n,n,u);else for(;i!==null;){if(i.tag===3){Tv(i,n,u);break}else if(i.tag===1){var f=i.stateNode;if(typeof i.type.getDerivedStateFromError=="function"||typeof f.componentDidCatch=="function"&&(oa===null||!oa.has(f))){n=el(u,n),n=cv(i,n,1),i=qo(i,n,1),n=mn(),i!==null&&(Po(i,1,n),mr(i,n));break}}i=i.return}}function kh(n,i,u){var f=n.pingCache;f!==null&&f.delete(i),i=mn(),n.pingedLanes|=n.suspendedLanes&u,Zn===n&&(Tr&u)===u&&(ar===4||ar===3&&(Tr&130023424)===Tr&&500>Qt()-Sh?es(n,0):Mc|=u),mr(n,i)}function kv(n,i){i===0&&(n.mode&1?(i=zo,zo<<=1,!(zo&130023424)&&(zo=4194304)):i=1);var u=mn();n=Va(n,i),n!==null&&(Po(n,i,u),mr(n,u))}function J0(n){var i=n.memoizedState,u=0;i!==null&&(u=i.retryLane),kv(n,u)}function Z0(n,i){var u=0;switch(n.tag){case 13:var f=n.stateNode,g=n.memoizedState;g!==null&&(u=g.retryLane);break;case 19:f=n.stateNode;break;default:throw Error(l(314))}f!==null&&f.delete(i),kv(n,u)}var Rh;Rh=function(n,i,u){if(n!==null)if(n.memoizedProps!==i.pendingProps||qn.current)gr=!0;else{if(!(n.lanes&u)&&!(i.flags&128))return gr=!1,yf(n,i,u);gr=!!(n.flags&131072)}else gr=!1,En&&i.flags&1048576&&Xm(i,Go,i.index);switch(i.lanes=0,i.tag){case 2:var f=i.type;ia(n,i),n=i.pendingProps;var g=Ai(i,Rn.current);Ys(i,u),g=nt(null,i,f,n,g,u);var x=Xo();return i.flags|=1,typeof g=="object"&&g!==null&&typeof g.render=="function"&&g.$$typeof===void 0?(i.tag=1,i.memoizedState=null,i.updateQueue=null,zn(f)?(x=!0,Nl(i)):x=!1,i.memoizedState=g.state!==null&&g.state!==void 0?g.state:null,Qo(i),g.updater=uf,i.stateNode=g,g._reactInternals=i,dh(i,f,n,u),i=gh(null,i,f,!0,x,u)):(i.tag=0,En&&x&&Kp(i),Un(null,i,g,u),i=i.child),i;case 16:f=i.elementType;e:{switch(ia(n,i),n=i.pendingProps,g=f._init,f=g(f._payload),i.type=f,g=i.tag=tb(f),n=vi(f,n),g){case 0:i=pf(null,i,f,n,u);break e;case 1:i=Y0(null,i,f,n,u);break e;case 11:i=ff(null,i,f,n,u);break e;case 14:i=yi(null,i,f,vi(f.type,n),u);break e}throw Error(l(306,f,""))}return i;case 0:return f=i.type,g=i.pendingProps,g=i.elementType===f?g:vi(f,g),pf(n,i,f,g,u);case 1:return f=i.type,g=i.pendingProps,g=i.elementType===f?g:vi(f,g),Y0(n,i,f,g,u);case 3:e:{if(hf(i),n===null)throw Error(l(387));f=i.pendingProps,x=i.memoizedState,g=x.element,rv(n,i),Hd(i,f,null,u);var k=i.memoizedState;if(f=k.element,x.isDehydrated)if(x={element:f,isDehydrated:!1,cache:k.cache,pendingSuspenseBoundaries:k.pendingSuspenseBoundaries,transitions:k.transitions},i.updateQueue.baseState=x,i.memoizedState=x,i.flags&256){g=el(Error(l(423)),i),i=Zs(n,i,f,u,g);break e}else if(f!==g){g=el(Error(l(424)),i),i=Zs(n,i,f,u,g);break e}else for(hi=wa(i.stateNode.containerInfo.firstChild),pi=i,En=!0,Ca=null,u=Sr(i,null,f,u),i.child=u;u;)u.flags=u.flags&-3|4096,u=u.sibling;else{if(xo(),f===g){i=Cr(n,i,u);break e}Un(n,i,f,u)}i=i.child}return i;case 5:return oh(i),n===null&&Jp(i),f=i.type,g=i.pendingProps,x=n!==null?n.memoizedProps:null,k=g.children,Ll(f,g)?k=null:x!==null&&Ll(f,x)&&(i.flags|=32),Cc(n,i),Un(n,i,k,u),i.child;case 6:return n===null&&Jp(i),null;case 13:return fv(n,i,u);case 4:return ah(i,i.stateNode.containerInfo),f=i.pendingProps,n===null?i.child=Ea(i,null,f,u):Un(n,i,f,u),i.child;case 11:return f=i.type,g=i.pendingProps,g=i.elementType===f?g:vi(f,g),ff(n,i,f,g,u);case 7:return Un(n,i,i.pendingProps,u),i.child;case 8:return Un(n,i,i.pendingProps.children,u),i.child;case 12:return Un(n,i,i.pendingProps.children,u),i.child;case 10:e:{if(f=i.type._context,g=i.pendingProps,x=i.memoizedProps,k=g.value,gn(Ce,f._currentValue),f._currentValue=k,x!==null)if(ba(x.value,k)){if(x.children===g.children&&!qn.current){i=Cr(n,i,u);break e}}else for(x=i.child,x!==null&&(x.return=i);x!==null;){var _=x.dependencies;if(_!==null){k=x.child;for(var F=_.firstContext;F!==null;){if(F.context===f){if(x.tag===1){F=bo(-1,u&-u),F.tag=2;var J=x.updateQueue;if(J!==null){J=J.shared;var ge=J.pending;ge===null?F.next=F:(F.next=ge.next,ge.next=F),J.pending=F}}x.lanes|=u,F=x.alternate,F!==null&&(F.lanes|=u),nh(x.return,u,i),_.lanes|=u;break}F=F.next}}else if(x.tag===10)k=x.type===i.type?null:x.child;else if(x.tag===18){if(k=x.return,k===null)throw Error(l(341));k.lanes|=u,_=k.alternate,_!==null&&(_.lanes|=u),nh(k,u,i),k=x.sibling}else k=x.child;if(k!==null)k.return=x;else for(k=x;k!==null;){if(k===i){k=null;break}if(x=k.sibling,x!==null){x.return=k.return,k=x;break}k=k.return}x=k}Un(n,i,g.children,u),i=i.child}return i;case 9:return g=i.type,f=i.pendingProps.children,Ys(i,u),g=nn(g),f=f(g),i.flags|=1,Un(n,i,f,u),i.child;case 14:return f=i.type,g=vi(f,i.pendingProps),g=vi(f.type,g),yi(n,i,f,g,u);case 15:return Yl(n,i,i.type,i.pendingProps,u);case 17:return f=i.type,g=i.pendingProps,g=i.elementType===f?g:vi(f,g),ia(n,i),i.tag=1,zn(f)?(n=!0,Nl(i)):n=!1,Ys(i,u),uv(i,f,g),dh(i,f,g,u),gh(null,i,f,!0,n,u);case 19:return xi(n,i,u);case 22:return xt(n,i,u)}throw Error(l(156,i.tag))};function Rv(n,i){return yn(n,i)}function eb(n,i,u,f){this.tag=n,this.key=u,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.ref=null,this.pendingProps=i,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=f,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function la(n,i,u,f){return new eb(n,i,u,f)}function Dh(n){return n=n.prototype,!(!n||!n.isReactComponent)}function tb(n){if(typeof n=="function")return Dh(n)?1:0;if(n!=null){if(n=n.$$typeof,n===bt)return 11;if(n===Ht)return 14}return 2}function al(n,i){var u=n.alternate;return u===null?(u=la(n.tag,i,n.key,n.mode),u.elementType=n.elementType,u.type=n.type,u.stateNode=n.stateNode,u.alternate=n,n.alternate=u):(u.pendingProps=i,u.type=n.type,u.flags=0,u.subtreeFlags=0,u.deletions=null),u.flags=n.flags&14680064,u.childLanes=n.childLanes,u.lanes=n.lanes,u.child=n.child,u.memoizedProps=n.memoizedProps,u.memoizedState=n.memoizedState,u.updateQueue=n.updateQueue,i=n.dependencies,u.dependencies=i===null?null:{lanes:i.lanes,firstContext:i.firstContext},u.sibling=n.sibling,u.index=n.index,u.ref=n.ref,u}function Rf(n,i,u,f,g,x){var k=2;if(f=n,typeof n=="function")Dh(n)&&(k=1);else if(typeof n=="string")k=5;else e:switch(n){case ue:return ol(u.children,g,x,i);case Ae:k=8,g|=8;break;case ft:return n=la(12,u,i,g|2),n.elementType=ft,n.lanes=x,n;case rt:return n=la(13,u,i,g),n.elementType=rt,n.lanes=x,n;case He:return n=la(19,u,i,g),n.elementType=He,n.lanes=x,n;case pt:return su(u,g,x,i);default:if(typeof n=="object"&&n!==null)switch(n.$$typeof){case Ve:k=10;break e;case Tt:k=9;break e;case bt:k=11;break e;case Ht:k=14;break e;case kt:k=16,f=null;break e}throw Error(l(130,n==null?n:typeof n,""))}return i=la(k,u,i,g),i.elementType=n,i.type=f,i.lanes=x,i}function ol(n,i,u,f){return n=la(7,n,f,i),n.lanes=u,n}function su(n,i,u,f){return n=la(22,n,f,i),n.elementType=pt,n.lanes=u,n.stateNode={isHidden:!1},n}function ns(n,i,u){return n=la(6,n,null,i),n.lanes=u,n}function Mh(n,i,u){return i=la(4,n.children!==null?n.children:[],n.key,i),i.lanes=u,i.stateNode={containerInfo:n.containerInfo,pendingChildren:null,implementation:n.implementation},i}function Dv(n,i,u,f,g){this.tag=i,this.containerInfo=n,this.finishedWork=this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.pendingContext=this.context=null,this.callbackPriority=0,this.eventTimes=kl(0),this.expirationTimes=kl(-1),this.entangledLanes=this.finishedLanes=this.mutableReadLanes=this.expiredLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=kl(0),this.identifierPrefix=f,this.onRecoverableError=g,this.mutableSourceEagerHydrationData=null}function Df(n,i,u,f,g,x,k,_,F){return n=new Dv(n,i,u,_,F),i===1?(i=1,x===!0&&(i|=8)):i=0,x=la(3,null,null,i),n.current=x,x.stateNode=n,x.memoizedState={element:f,isDehydrated:u,cache:null,transitions:null,pendingSuspenseBoundaries:null},Qo(x),n}function Mv(n,i,u){var f=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:le,key:f==null?null:""+f,children:n,containerInfo:i,implementation:u}}function $v(n){if(!n)return Et;n=n._reactInternals;e:{if(Me(n)!==n||n.tag!==1)throw Error(l(170));var i=n;do{switch(i.tag){case 3:i=i.stateNode.context;break e;case 1:if(zn(i.type)){i=i.stateNode.__reactInternalMemoizedMergedChildContext;break e}}i=i.return}while(i!==null);throw Error(l(171))}if(n.tag===1){var u=n.type;if(zn(u))return qm(n,u,i)}return i}function $h(n,i,u,f,g,x,k,_,F){return n=Df(u,f,!0,n,g,x,k,_,F),n.context=$v(null),u=n.current,f=mn(),g=zi(u),x=bo(f,g),x.callback=i??null,qo(u,x,g),n.current.lanes=g,Po(n,g,f),mr(n,f),n}function Mf(n,i,u,f){var g=i.current,x=mn(),k=zi(g);return u=$v(u),i.context===null?i.context=u:i.pendingContext=u,i=bo(x,k),i.payload={element:n},f=f===void 0?null:f,f!==null&&(i.callback=f),n=qo(g,i,k),n!==null&&(Ni(n,g,k,x),Ud(n,g,k)),k}function $f(n){if(n=n.current,!n.child)return null;switch(n.child.tag){case 5:return n.child.stateNode;default:return n.child.stateNode}}function Ov(n,i){if(n=n.memoizedState,n!==null&&n.dehydrated!==null){var u=n.retryLane;n.retryLane=u!==0&&u<i?u:i}}function Of(n,i){Ov(n,i),(n=n.alternate)&&Ov(n,i)}function Av(){return null}var Oh=typeof reportError=="function"?reportError:function(n){console.error(n)};function ll(n){this._internalRoot=n}Af.prototype.render=ll.prototype.render=function(n){var i=this._internalRoot;if(i===null)throw Error(l(409));Mf(n,i,null,null)},Af.prototype.unmount=ll.prototype.unmount=function(){var n=this._internalRoot;if(n!==null){this._internalRoot=null;var i=n.containerInfo;Zl(function(){Mf(null,n,null,null)}),i[vo]=null}};function Af(n){this._internalRoot=n}Af.prototype.unstable_scheduleHydration=function(n){if(n){var i=Pa();n={blockedOn:null,target:n,priority:i};for(var u=0;u<ya.length&&i!==0&&i<ya[u].priority;u++);ya.splice(u,0,n),u===0&&ks(n)}};function Ah(n){return!(!n||n.nodeType!==1&&n.nodeType!==9&&n.nodeType!==11)}function jf(n){return!(!n||n.nodeType!==1&&n.nodeType!==9&&n.nodeType!==11&&(n.nodeType!==8||n.nodeValue!==" react-mount-point-unstable "))}function jv(){}function nb(n,i,u,f,g){if(g){if(typeof f=="function"){var x=f;f=function(){var J=$f(k);x.call(J)}}var k=$h(i,f,n,0,null,!1,!1,"",jv);return n._reactRootContainer=k,n[vo]=k.current,rc(n.nodeType===8?n.parentNode:n),Zl(),k}for(;g=n.lastChild;)n.removeChild(g);if(typeof f=="function"){var _=f;f=function(){var J=$f(F);_.call(J)}}var F=Df(n,0,!1,null,null,!1,!1,"",jv);return n._reactRootContainer=F,n[vo]=F.current,rc(n.nodeType===8?n.parentNode:n),Zl(function(){Mf(i,F,u,f)}),F}function _f(n,i,u,f,g){var x=u._reactRootContainer;if(x){var k=x;if(typeof g=="function"){var _=g;g=function(){var F=$f(k);_.call(F)}}Mf(i,k,n,g)}else k=nb(u,i,n,g,f);return $f(k)}Ts=function(n){switch(n.tag){case 3:var i=n.stateNode;if(i.current.memoizedState.isDehydrated){var u=ui(i.pendingLanes);u!==0&&(Hu(i,u|1),mr(i,Qt()),!(jt&6)&&(iu=Qt()+500,Yr()))}break;case 13:Zl(function(){var f=Va(n,1);if(f!==null){var g=mn();Ni(f,n,1,g)}}),Of(n,1)}},Pt=function(n){if(n.tag===13){var i=Va(n,134217728);if(i!==null){var u=mn();Ni(i,n,134217728,u)}Of(n,134217728)}},Ed=function(n){if(n.tag===13){var i=zi(n),u=Va(n,i);if(u!==null){var f=mn();Ni(u,n,i,f)}Of(n,i)}},Pa=function(){return Nt},lt=function(n,i){var u=Nt;try{return Nt=n,i()}finally{Nt=u}},on=function(n,i,u){switch(i){case"input":if(Gn(n,u),i=u.name,u.type==="radio"&&i!=null){for(u=n;u.parentNode;)u=u.parentNode;for(u=u.querySelectorAll("input[name="+JSON.stringify(""+i)+'][type="radio"]'),i=0;i<u.length;i++){var f=u[i];if(f!==n&&f.form===n.form){var g=yo(f);if(!g)throw Error(l(90));an(f),Gn(f,g)}}}break;case"textarea":_r(n,u);break;case"select":i=u.value,i!=null&&nr(n,!!u.multiple,i,!1)}},Sl=Ef,Cl=Zl;var _v={usingClientEntryPoint:!1,Events:[lc,Ke,yo,qi,io,Ef]},Nc={findFiberByHostInstance:zl,bundleType:0,version:"18.3.1",rendererPackageName:"react-dom"},rb={bundleType:Nc.bundleType,version:Nc.version,rendererPackageName:Nc.rendererPackageName,rendererConfig:Nc.rendererConfig,overrideHookState:null,overrideHookStateDeletePath:null,overrideHookStateRenamePath:null,overrideProps:null,overridePropsDeletePath:null,overridePropsRenamePath:null,setErrorHandler:null,setSuspenseHandler:null,scheduleUpdate:null,currentDispatcherRef:ce.ReactCurrentDispatcher,findHostInstanceByFiber:function(n){return n=St(n),n===null?null:n.stateNode},findFiberByHostInstance:Nc.findFiberByHostInstance||Av,findHostInstancesForRefresh:null,scheduleRefresh:null,scheduleRoot:null,setRefreshHandler:null,getCurrentFiber:null,reconcilerVersion:"18.3.1-next-f1338f8080-20240426"};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"){var Pc=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!Pc.isDisabled&&Pc.supportsFiber)try{Lo=Pc.inject(rb),si=Pc}catch{}}return Hi.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=_v,Hi.createPortal=function(n,i){var u=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!Ah(i))throw Error(l(200));return Mv(n,i,null,u)},Hi.createRoot=function(n,i){if(!Ah(n))throw Error(l(299));var u=!1,f="",g=Oh;return i!=null&&(i.unstable_strictMode===!0&&(u=!0),i.identifierPrefix!==void 0&&(f=i.identifierPrefix),i.onRecoverableError!==void 0&&(g=i.onRecoverableError)),i=Df(n,1,!1,null,null,u,!1,f,g),n[vo]=i.current,rc(n.nodeType===8?n.parentNode:n),new ll(i)},Hi.findDOMNode=function(n){if(n==null)return null;if(n.nodeType===1)return n;var i=n._reactInternals;if(i===void 0)throw typeof n.render=="function"?Error(l(188)):(n=Object.keys(n).join(","),Error(l(268,n)));return n=St(i),n=n===null?null:n.stateNode,n},Hi.flushSync=function(n){return Zl(n)},Hi.hydrate=function(n,i,u){if(!jf(i))throw Error(l(200));return _f(null,n,i,!0,u)},Hi.hydrateRoot=function(n,i,u){if(!Ah(n))throw Error(l(405));var f=u!=null&&u.hydratedSources||null,g=!1,x="",k=Oh;if(u!=null&&(u.unstable_strictMode===!0&&(g=!0),u.identifierPrefix!==void 0&&(x=u.identifierPrefix),u.onRecoverableError!==void 0&&(k=u.onRecoverableError)),i=$h(i,null,n,1,u??null,g,!1,x,k),n[vo]=i.current,rc(n),f)for(n=0;n<f.length;n++)u=f[n],g=u._getVersion,g=g(u._source),i.mutableSourceEagerHydrationData==null?i.mutableSourceEagerHydrationData=[u,g]:i.mutableSourceEagerHydrationData.push(u,g);return new Af(i)},Hi.render=function(n,i,u){if(!jf(i))throw Error(l(200));return _f(null,n,i,!1,u)},Hi.unmountComponentAtNode=function(n){if(!jf(n))throw Error(l(40));return n._reactRootContainer?(Zl(function(){_f(null,null,n,!1,function(){n._reactRootContainer=null,n[vo]=null})}),!0):!1},Hi.unstable_batchedUpdates=Ef,Hi.unstable_renderSubtreeIntoContainer=function(n,i,u,f){if(!jf(u))throw Error(l(200));if(n==null||n._reactInternals===void 0)throw Error(l(38));return _f(n,i,u,!1,f)},Hi.version="18.3.1-next-f1338f8080-20240426",Hi}var Vi={},a1;function zD(){if(a1)return Vi;a1=1;var o={};/**
 * @license React
 * react-dom.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */return o.NODE_ENV!=="production"&&function(){typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStart(new Error);var r=Ge,l=r1(),d=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,h=!1;function b(e){h=e}function S(e){if(!h){for(var t=arguments.length,a=new Array(t>1?t-1:0),s=1;s<t;s++)a[s-1]=arguments[s];E("warn",e,a)}}function y(e){if(!h){for(var t=arguments.length,a=new Array(t>1?t-1:0),s=1;s<t;s++)a[s-1]=arguments[s];E("error",e,a)}}function E(e,t,a){{var s=d.ReactDebugCurrentFrame,c=s.getStackAddendum();c!==""&&(t+="%s",a=a.concat([c]));var p=a.map(function(v){return String(v)});p.unshift("Warning: "+t),Function.prototype.apply.call(console[e],console,p)}}var $=0,M=1,N=2,j=3,H=4,L=5,K=6,de=7,Ee=8,pe=9,ae=10,ce=11,Te=12,le=13,ue=14,Ae=15,ft=16,Ve=17,Tt=18,bt=19,rt=21,He=22,Ht=23,kt=24,pt=25,se=!0,Re=!1,be=!1,V=!1,re=!1,We=!0,et=!0,it=!0,ht=!0,$t=new Set,tt={},vt={};function It(e,t){pn(e,t),pn(e+"Capture",t)}function pn(e,t){tt[e]&&y("EventRegistry: More than one plugin attempted to publish the same registration name, `%s`.",e),tt[e]=t;{var a=e.toLowerCase();vt[a]=e,e==="onDoubleClick"&&(vt.ondblclick=e)}for(var s=0;s<t.length;s++)$t.add(t[s])}var an=typeof window<"u"&&typeof window.document<"u"&&typeof window.document.createElement<"u",Pn=Object.prototype.hasOwnProperty;function xn(e){{var t=typeof Symbol=="function"&&Symbol.toStringTag,a=t&&e[Symbol.toStringTag]||e.constructor.name||"Object";return a}}function An(e){try{return tr(e),!1}catch{return!0}}function tr(e){return""+e}function Gn(e,t){if(An(e))return y("The provided `%s` attribute is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),tr(e)}function Ri(e){if(An(e))return y("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",xn(e)),tr(e)}function ga(e,t){if(An(e))return y("The provided `%s` prop is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),tr(e)}function Ur(e,t){if(An(e))return y("The provided `%s` CSS property is an unsupported type %s. This value must be coerced to a string before before using it here.",t,xn(e)),tr(e)}function nr(e){if(An(e))return y("The provided HTML markup uses a value of unsupported type %s. This value must be coerced to a string before before using it here.",xn(e)),tr(e)}function ur(e){if(An(e))return y("Form field values (value, checked, defaultValue, or defaultChecked props) must be strings, not %s. This value must be coerced to a string before before using it here.",xn(e)),tr(e)}var cr=0,_r=1,ma=2,Kn=3,xr=4,oi=5,ro=6,Di=":A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD",we=Di+"\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040",Xe=new RegExp("^["+Di+"]["+we+"]*$"),wt={},Gt={};function bn(e){return Pn.call(Gt,e)?!0:Pn.call(wt,e)?!1:Xe.test(e)?(Gt[e]=!0,!0):(wt[e]=!0,y("Invalid attribute name: `%s`",e),!1)}function wn(e,t,a){return t!==null?t.type===cr:a?!1:e.length>2&&(e[0]==="o"||e[0]==="O")&&(e[1]==="n"||e[1]==="N")}function Sn(e,t,a,s){if(a!==null&&a.type===cr)return!1;switch(typeof t){case"function":case"symbol":return!0;case"boolean":{if(s)return!1;if(a!==null)return!a.acceptsBooleans;var c=e.toLowerCase().slice(0,5);return c!=="data-"&&c!=="aria-"}default:return!1}}function dr(e,t,a,s){if(t===null||typeof t>"u"||Sn(e,t,a,s))return!0;if(s)return!1;if(a!==null)switch(a.type){case Kn:return!t;case xr:return t===!1;case oi:return isNaN(t);case ro:return isNaN(t)||t<1}return!1}function vn(e){return Kt.hasOwnProperty(e)?Kt[e]:null}function on(e,t,a,s,c,p,v){this.acceptsBooleans=t===ma||t===Kn||t===xr,this.attributeName=s,this.attributeNamespace=c,this.mustUseProperty=a,this.propertyName=e,this.type=t,this.sanitizeURL=p,this.removeEmptyString=v}var Kt={},Mi=["children","dangerouslySetInnerHTML","defaultValue","defaultChecked","innerHTML","suppressContentEditableWarning","suppressHydrationWarning","style"];Mi.forEach(function(e){Kt[e]=new on(e,cr,!1,e,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(e){var t=e[0],a=e[1];Kt[t]=new on(t,_r,!1,a,null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(e){Kt[e]=new on(e,ma,!1,e.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(e){Kt[e]=new on(e,ma,!1,e,null,!1,!1)}),["allowFullScreen","async","autoFocus","autoPlay","controls","default","defer","disabled","disablePictureInPicture","disableRemotePlayback","formNoValidate","hidden","loop","noModule","noValidate","open","playsInline","readOnly","required","reversed","scoped","seamless","itemScope"].forEach(function(e){Kt[e]=new on(e,Kn,!1,e.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(e){Kt[e]=new on(e,Kn,!0,e,null,!1,!1)}),["capture","download"].forEach(function(e){Kt[e]=new on(e,xr,!1,e,null,!1,!1)}),["cols","rows","size","span"].forEach(function(e){Kt[e]=new on(e,ro,!1,e,null,!1,!1)}),["rowSpan","start"].forEach(function(e){Kt[e]=new on(e,oi,!1,e.toLowerCase(),null,!1,!1)});var Qi=/[\-\:]([a-z])/g,qi=function(e){return e[1].toUpperCase()};["accent-height","alignment-baseline","arabic-form","baseline-shift","cap-height","clip-path","clip-rule","color-interpolation","color-interpolation-filters","color-profile","color-rendering","dominant-baseline","enable-background","fill-opacity","fill-rule","flood-color","flood-opacity","font-family","font-size","font-size-adjust","font-stretch","font-style","font-variant","font-weight","glyph-name","glyph-orientation-horizontal","glyph-orientation-vertical","horiz-adv-x","horiz-origin-x","image-rendering","letter-spacing","lighting-color","marker-end","marker-mid","marker-start","overline-position","overline-thickness","paint-order","panose-1","pointer-events","rendering-intent","shape-rendering","stop-color","stop-opacity","strikethrough-position","strikethrough-thickness","stroke-dasharray","stroke-dashoffset","stroke-linecap","stroke-linejoin","stroke-miterlimit","stroke-opacity","stroke-width","text-anchor","text-decoration","text-rendering","underline-position","underline-thickness","unicode-bidi","unicode-range","units-per-em","v-alphabetic","v-hanging","v-ideographic","v-mathematical","vector-effect","vert-adv-y","vert-origin-x","vert-origin-y","word-spacing","writing-mode","xmlns:xlink","x-height"].forEach(function(e){var t=e.replace(Qi,qi);Kt[t]=new on(t,_r,!1,e,null,!1,!1)}),["xlink:actuate","xlink:arcrole","xlink:role","xlink:show","xlink:title","xlink:type"].forEach(function(e){var t=e.replace(Qi,qi);Kt[t]=new on(t,_r,!1,e,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(e){var t=e.replace(Qi,qi);Kt[t]=new on(t,_r,!1,e,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(e){Kt[e]=new on(e,_r,!1,e.toLowerCase(),null,!1,!1)});var io="xlinkHref";Kt[io]=new on("xlinkHref",_r,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(e){Kt[e]=new on(e,_r,!1,e.toLowerCase(),null,!0,!0)});var Sl=/^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*\:/i,Cl=!1;function ao(e){!Cl&&Sl.test(e)&&(Cl=!0,y("A future version of React will block javascript: URLs as a security precaution. Use event handlers instead if you can. If you need to generate unsafe HTML try using dangerouslySetInnerHTML instead. React was passed %s.",JSON.stringify(e)))}function El(e,t,a,s){if(s.mustUseProperty){var c=s.propertyName;return e[c]}else{Gn(a,t),s.sanitizeURL&&ao(""+a);var p=s.attributeName,v=null;if(s.type===xr){if(e.hasAttribute(p)){var w=e.getAttribute(p);return w===""?!0:dr(t,a,s,!1)?w:w===""+a?a:w}}else if(e.hasAttribute(p)){if(dr(t,a,s,!1))return e.getAttribute(p);if(s.type===Kn)return a;v=e.getAttribute(p)}return dr(t,a,s,!1)?v===null?a:v:v===""+a?a:v}}function ja(e,t,a,s){{if(!bn(t))return;if(!e.hasAttribute(t))return a===void 0?void 0:null;var c=e.getAttribute(t);return Gn(a,t),c===""+a?a:c}}function $i(e,t,a,s){var c=vn(t);if(!wn(t,c,s)){if(dr(t,a,c,s)&&(a=null),s||c===null){if(bn(t)){var p=t;a===null?e.removeAttribute(p):(Gn(a,t),e.setAttribute(p,""+a))}return}var v=c.mustUseProperty;if(v){var w=c.propertyName;if(a===null){var C=c.type;e[w]=C===Kn?!1:""}else e[w]=a;return}var R=c.attributeName,O=c.attributeNamespace;if(a===null)e.removeAttribute(R);else{var U=c.type,B;U===Kn||U===xr&&a===!0?B="":(Gn(a,R),B=""+a,c.sanitizeURL&&ao(B.toString())),O?e.setAttributeNS(O,R,B):e.setAttribute(R,B)}}}var br=Symbol.for("react.element"),Oi=Symbol.for("react.portal"),li=Symbol.for("react.fragment"),_a=Symbol.for("react.strict_mode"),La=Symbol.for("react.profiler"),oo=Symbol.for("react.provider"),P=Symbol.for("react.context"),fe=Symbol.for("react.forward_ref"),ke=Symbol.for("react.suspense"),Me=Symbol.for("react.suspense_list"),Rt=Symbol.for("react.memo"),ut=Symbol.for("react.lazy"),Ot=Symbol.for("react.scope"),St=Symbol.for("react.debug_trace_mode"),Fn=Symbol.for("react.offscreen"),yn=Symbol.for("react.legacy_hidden"),Cn=Symbol.for("react.cache"),Lr=Symbol.for("react.tracing_marker"),va=Symbol.iterator,Qt="@@iterator";function kn(e){if(e===null||typeof e!="object")return null;var t=va&&e[va]||e[Qt];return typeof t=="function"?t:null}var gt=Object.assign,za=0,lo,wd,so,Lo,si,Bu,Hr;function Iu(){}Iu.__reactDisabledLog=!0;function Sd(){{if(za===0){lo=console.log,wd=console.info,so=console.warn,Lo=console.error,si=console.group,Bu=console.groupCollapsed,Hr=console.groupEnd;var e={configurable:!0,enumerable:!0,value:Iu,writable:!0};Object.defineProperties(console,{info:e,log:e,warn:e,error:e,group:e,groupCollapsed:e,groupEnd:e})}za++}}function Cd(){{if(za--,za===0){var e={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:gt({},e,{value:lo}),info:gt({},e,{value:wd}),warn:gt({},e,{value:so}),error:gt({},e,{value:Lo}),group:gt({},e,{value:si}),groupCollapsed:gt({},e,{value:Bu}),groupEnd:gt({},e,{value:Hr})})}za<0&&y("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var uo=d.ReactCurrentDispatcher,zo;function ui(e,t,a){{if(zo===void 0)try{throw Error()}catch(c){var s=c.stack.trim().match(/\n( *(at )?)/);zo=s&&s[1]||""}return`
`+zo+e}}var Na=!1,No;{var Es=typeof WeakMap=="function"?WeakMap:Map;No=new Es}function co(e,t){if(!e||Na)return"";{var a=No.get(e);if(a!==void 0)return a}var s;Na=!0;var c=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var p;p=uo.current,uo.current=null,Sd();try{if(t){var v=function(){throw Error()};if(Object.defineProperty(v.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(v,[])}catch(Z){s=Z}Reflect.construct(e,[],v)}else{try{v.call()}catch(Z){s=Z}e.call(v.prototype)}}else{try{throw Error()}catch(Z){s=Z}e()}}catch(Z){if(Z&&s&&typeof Z.stack=="string"){for(var w=Z.stack.split(`
`),C=s.stack.split(`
`),R=w.length-1,O=C.length-1;R>=1&&O>=0&&w[R]!==C[O];)O--;for(;R>=1&&O>=0;R--,O--)if(w[R]!==C[O]){if(R!==1||O!==1)do if(R--,O--,O<0||w[R]!==C[O]){var U=`
`+w[R].replace(" at new "," at ");return e.displayName&&U.includes("<anonymous>")&&(U=U.replace("<anonymous>",e.displayName)),typeof e=="function"&&No.set(e,U),U}while(R>=1&&O>=0);break}}}finally{Na=!1,uo.current=p,Cd(),Error.prepareStackTrace=c}var B=e?e.displayName||e.name:"",X=B?ui(B):"";return typeof e=="function"&&No.set(e,X),X}function Tl(e,t,a){return co(e,!0)}function kl(e,t,a){return co(e,!1)}function Po(e){var t=e.prototype;return!!(t&&t.isReactComponent)}function Uu(e,t,a){if(e==null)return"";if(typeof e=="function")return co(e,Po(e));if(typeof e=="string")return ui(e);switch(e){case ke:return ui("Suspense");case Me:return ui("SuspenseList")}if(typeof e=="object")switch(e.$$typeof){case fe:return kl(e.render);case Rt:return Uu(e.type,t,a);case ut:{var s=e,c=s._payload,p=s._init;try{return Uu(p(c),t,a)}catch{}}}return""}function Hu(e){switch(e._debugOwner&&e._debugOwner.type,e._debugSource,e.tag){case L:return ui(e.type);case ft:return ui("Lazy");case le:return ui("Suspense");case bt:return ui("SuspenseList");case $:case N:case Ae:return kl(e.type);case ce:return kl(e.type.render);case M:return Tl(e.type);default:return""}}function Nt(e){try{var t="",a=e;do t+=Hu(a),a=a.return;while(a);return t}catch(s){return`
Error generating stack: `+s.message+`
`+s.stack}}function Vu(e,t,a){var s=e.displayName;if(s)return s;var c=t.displayName||t.name||"";return c!==""?a+"("+c+")":a}function Ts(e){return e.displayName||"Context"}function Pt(e){if(e==null)return null;if(typeof e.tag=="number"&&y("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof e=="function")return e.displayName||e.name||null;if(typeof e=="string")return e;switch(e){case li:return"Fragment";case Oi:return"Portal";case La:return"Profiler";case _a:return"StrictMode";case ke:return"Suspense";case Me:return"SuspenseList"}if(typeof e=="object")switch(e.$$typeof){case P:var t=e;return Ts(t)+".Consumer";case oo:var a=e;return Ts(a._context)+".Provider";case fe:return Vu(e,e.render,"ForwardRef");case Rt:var s=e.displayName||null;return s!==null?s:Pt(e.type)||"Memo";case ut:{var c=e,p=c._payload,v=c._init;try{return Pt(v(p))}catch{return null}}}return null}function Ed(e,t,a){var s=t.displayName||t.name||"";return e.displayName||(s!==""?a+"("+s+")":a)}function Pa(e){return e.displayName||"Context"}function lt(e){var t=e.tag,a=e.type;switch(t){case kt:return"Cache";case pe:var s=a;return Pa(s)+".Consumer";case ae:var c=a;return Pa(c._context)+".Provider";case Tt:return"DehydratedFragment";case ce:return Ed(a,a.render,"ForwardRef");case de:return"Fragment";case L:return a;case H:return"Portal";case j:return"Root";case K:return"Text";case ft:return Pt(a);case Ee:return a===_a?"StrictMode":"Mode";case He:return"Offscreen";case Te:return"Profiler";case rt:return"Scope";case le:return"Suspense";case bt:return"SuspenseList";case pt:return"TracingMarker";case M:case $:case Ve:case N:case ue:case Ae:if(typeof a=="function")return a.displayName||a.name||null;if(typeof a=="string")return a;break}return null}var Rl=d.ReactDebugCurrentFrame,fr=null,ci=!1;function Vr(){{if(fr===null)return null;var e=fr._debugOwner;if(e!==null&&typeof e<"u")return lt(e)}return null}function Fa(){return fr===null?"":Nt(fr)}function _n(){Rl.getCurrentStack=null,fr=null,ci=!1}function ln(e){Rl.getCurrentStack=e===null?null:Fa,fr=e,ci=!1}function ya(){return fr}function Xi(e){ci=e}function zr(e){return""+e}function Wr(e){switch(typeof e){case"boolean":case"number":case"string":case"undefined":return e;case"object":return ur(e),e;default:return""}}var Op={button:!0,checkbox:!0,image:!0,hidden:!0,radio:!0,reset:!0,submit:!0};function ks(e,t){Op[t.type]||t.onChange||t.onInput||t.readOnly||t.disabled||t.value==null||y("You provided a `value` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultValue`. Otherwise, set either `onChange` or `readOnly`."),t.onChange||t.readOnly||t.disabled||t.checked==null||y("You provided a `checked` prop to a form field without an `onChange` handler. This will render a read-only field. If the field should be mutable use `defaultChecked`. Otherwise, set either `onChange` or `readOnly`.")}function Dl(e){var t=e.type,a=e.nodeName;return a&&a.toLowerCase()==="input"&&(t==="checkbox"||t==="radio")}function Rs(e){return e._valueTracker}function Ds(e){e._valueTracker=null}function Ml(e){var t="";return e&&(Dl(e)?t=e.checked?"true":"false":t=e.value),t}function Ji(e){var t=Dl(e)?"checked":"value",a=Object.getOwnPropertyDescriptor(e.constructor.prototype,t);ur(e[t]);var s=""+e[t];if(!(e.hasOwnProperty(t)||typeof a>"u"||typeof a.get!="function"||typeof a.set!="function")){var c=a.get,p=a.set;Object.defineProperty(e,t,{configurable:!0,get:function(){return c.call(this)},set:function(w){ur(w),s=""+w,p.call(this,w)}}),Object.defineProperty(e,t,{enumerable:a.enumerable});var v={getValue:function(){return s},setValue:function(w){ur(w),s=""+w},stopTracking:function(){Ds(e),delete e[t]}};return v}}function Zi(e){Rs(e)||(e._valueTracker=Ji(e))}function Fo(e){if(!e)return!1;var t=Rs(e);if(!t)return!0;var a=t.getValue(),s=Ml(e);return s!==a?(t.setValue(s),!0):!1}function fo(e){if(e=e||(typeof document<"u"?document:void 0),typeof e>"u")return null;try{return e.activeElement||e.body}catch{return e.body}}var Ms=!1,Bo=!1,po=!1,$s=!1;function Wu(e){var t=e.type==="checkbox"||e.type==="radio";return t?e.checked!=null:e.value!=null}function ea(e,t){var a=e,s=t.checked,c=gt({},t,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:s??a._wrapperState.initialChecked});return c}function Os(e,t){ks("input",t),t.checked!==void 0&&t.defaultChecked!==void 0&&!Bo&&(y("%s contains an input of type %s with both checked and defaultChecked props. Input elements must be either controlled or uncontrolled (specify either the checked prop, or the defaultChecked prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://reactjs.org/link/controlled-components",Vr()||"A component",t.type),Bo=!0),t.value!==void 0&&t.defaultValue!==void 0&&!Ms&&(y("%s contains an input of type %s with both value and defaultValue props. Input elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled input element and remove one of these props. More info: https://reactjs.org/link/controlled-components",Vr()||"A component",t.type),Ms=!0);var a=e,s=t.defaultValue==null?"":t.defaultValue;a._wrapperState={initialChecked:t.checked!=null?t.checked:t.defaultChecked,initialValue:Wr(t.value!=null?t.value:s),controlled:Wu(t)}}function T(e,t){var a=e,s=t.checked;s!=null&&$i(a,"checked",s,!1)}function z(e,t){var a=e;{var s=Wu(t);!a._wrapperState.controlled&&s&&!$s&&(y("A component is changing an uncontrolled input to be controlled. This is likely caused by the value changing from undefined to a defined value, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://reactjs.org/link/controlled-components"),$s=!0),a._wrapperState.controlled&&!s&&!po&&(y("A component is changing a controlled input to be uncontrolled. This is likely caused by the value changing from a defined to undefined, which should not happen. Decide between using a controlled or uncontrolled input element for the lifetime of the component. More info: https://reactjs.org/link/controlled-components"),po=!0)}T(e,t);var c=Wr(t.value),p=t.type;if(c!=null)p==="number"?(c===0&&a.value===""||a.value!=c)&&(a.value=zr(c)):a.value!==zr(c)&&(a.value=zr(c));else if(p==="submit"||p==="reset"){a.removeAttribute("value");return}t.hasOwnProperty("value")?Pe(a,t.type,c):t.hasOwnProperty("defaultValue")&&Pe(a,t.type,Wr(t.defaultValue)),t.checked==null&&t.defaultChecked!=null&&(a.defaultChecked=!!t.defaultChecked)}function q(e,t,a){var s=e;if(t.hasOwnProperty("value")||t.hasOwnProperty("defaultValue")){var c=t.type,p=c==="submit"||c==="reset";if(p&&(t.value===void 0||t.value===null))return;var v=zr(s._wrapperState.initialValue);a||v!==s.value&&(s.value=v),s.defaultValue=v}var w=s.name;w!==""&&(s.name=""),s.defaultChecked=!s.defaultChecked,s.defaultChecked=!!s._wrapperState.initialChecked,w!==""&&(s.name=w)}function ee(e,t){var a=e;z(a,t),xe(a,t)}function xe(e,t){var a=t.name;if(t.type==="radio"&&a!=null){for(var s=e;s.parentNode;)s=s.parentNode;Gn(a,"name");for(var c=s.querySelectorAll("input[name="+JSON.stringify(""+a)+'][type="radio"]'),p=0;p<c.length;p++){var v=c[p];if(!(v===e||v.form!==e.form)){var w=Xv(v);if(!w)throw new Error("ReactDOMInput: Mixing React and non-React radio inputs with the same `name` is not supported.");Fo(v),z(v,w)}}}}function Pe(e,t,a){(t!=="number"||fo(e.ownerDocument)!==e)&&(a==null?e.defaultValue=zr(e._wrapperState.initialValue):e.defaultValue!==zr(a)&&(e.defaultValue=zr(a)))}var je=!1,at=!1,Ct=!1;function qt(e,t){t.value==null&&(typeof t.children=="object"&&t.children!==null?r.Children.forEach(t.children,function(a){a!=null&&(typeof a=="string"||typeof a=="number"||at||(at=!0,y("Cannot infer the option value of complex children. Pass a `value` prop or use a plain string as children to <option>.")))}):t.dangerouslySetInnerHTML!=null&&(Ct||(Ct=!0,y("Pass a `value` prop if you set dangerouslyInnerHTML so React knows which value should be selected.")))),t.selected!=null&&!je&&(y("Use the `defaultValue` or `value` props on <select> instead of setting `selected` on <option>."),je=!0)}function sn(e,t){t.value!=null&&e.setAttribute("value",zr(Wr(t.value)))}var un=Array.isArray;function yt(e){return un(e)}var hn;hn=!1;function Bn(){var e=Vr();return e?`

Check the render method of \``+e+"`.":""}var $l=["value","defaultValue"];function Yu(e){{ks("select",e);for(var t=0;t<$l.length;t++){var a=$l[t];if(e[a]!=null){var s=yt(e[a]);e.multiple&&!s?y("The `%s` prop supplied to <select> must be an array if `multiple` is true.%s",a,Bn()):!e.multiple&&s&&y("The `%s` prop supplied to <select> must be a scalar value if `multiple` is false.%s",a,Bn())}}}}function ho(e,t,a,s){var c=e.options;if(t){for(var p=a,v={},w=0;w<p.length;w++)v["$"+p[w]]=!0;for(var C=0;C<c.length;C++){var R=v.hasOwnProperty("$"+c[C].value);c[C].selected!==R&&(c[C].selected=R),R&&s&&(c[C].defaultSelected=!0)}}else{for(var O=zr(Wr(a)),U=null,B=0;B<c.length;B++){if(c[B].value===O){c[B].selected=!0,s&&(c[B].defaultSelected=!0);return}U===null&&!c[B].disabled&&(U=c[B])}U!==null&&(U.selected=!0)}}function Ol(e,t){return gt({},t,{value:void 0})}function Gu(e,t){var a=e;Yu(t),a._wrapperState={wasMultiple:!!t.multiple},t.value!==void 0&&t.defaultValue!==void 0&&!hn&&(y("Select elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled select element and remove one of these props. More info: https://reactjs.org/link/controlled-components"),hn=!0)}function Ap(e,t){var a=e;a.multiple=!!t.multiple;var s=t.value;s!=null?ho(a,!!t.multiple,s,!1):t.defaultValue!=null&&ho(a,!!t.multiple,t.defaultValue,!0)}function Td(e,t){var a=e,s=a._wrapperState.wasMultiple;a._wrapperState.wasMultiple=!!t.multiple;var c=t.value;c!=null?ho(a,!!t.multiple,c,!1):s!==!!t.multiple&&(t.defaultValue!=null?ho(a,!!t.multiple,t.defaultValue,!0):ho(a,!!t.multiple,t.multiple?[]:"",!1))}function jp(e,t){var a=e,s=t.value;s!=null&&ho(a,!!t.multiple,s,!1)}var bm=!1;function kd(e,t){var a=e;if(t.dangerouslySetInnerHTML!=null)throw new Error("`dangerouslySetInnerHTML` does not make sense on <textarea>.");var s=gt({},t,{value:void 0,defaultValue:void 0,children:zr(a._wrapperState.initialValue)});return s}function wm(e,t){var a=e;ks("textarea",t),t.value!==void 0&&t.defaultValue!==void 0&&!bm&&(y("%s contains a textarea with both value and defaultValue props. Textarea elements must be either controlled or uncontrolled (specify either the value prop, or the defaultValue prop, but not both). Decide between using a controlled or uncontrolled textarea and remove one of these props. More info: https://reactjs.org/link/controlled-components",Vr()||"A component"),bm=!0);var s=t.value;if(s==null){var c=t.children,p=t.defaultValue;if(c!=null){y("Use the `defaultValue` or `value` props instead of setting children on <textarea>.");{if(p!=null)throw new Error("If you supply `defaultValue` on a <textarea>, do not pass children.");if(yt(c)){if(c.length>1)throw new Error("<textarea> can only have at most one child.");c=c[0]}p=c}}p==null&&(p=""),s=p}a._wrapperState={initialValue:Wr(s)}}function Sm(e,t){var a=e,s=Wr(t.value),c=Wr(t.defaultValue);if(s!=null){var p=zr(s);p!==a.value&&(a.value=p),t.defaultValue==null&&a.defaultValue!==p&&(a.defaultValue=p)}c!=null&&(a.defaultValue=zr(c))}function Cm(e,t){var a=e,s=a.textContent;s===a._wrapperState.initialValue&&s!==""&&s!==null&&(a.value=s)}function A0(e,t){Sm(e,t)}var xa="http://www.w3.org/1999/xhtml",j0="http://www.w3.org/1998/Math/MathML",_p="http://www.w3.org/2000/svg";function Lp(e){switch(e){case"svg":return _p;case"math":return j0;default:return xa}}function Rd(e,t){return e==null||e===xa?Lp(t):e===_p&&t==="foreignObject"?xa:e}var _0=function(e){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(t,a,s,c){MSApp.execUnsafeLocalFunction(function(){return e(t,a,s,c)})}:e},Dd,Em=_0(function(e,t){if(e.namespaceURI===_p&&!("innerHTML"in e)){Dd=Dd||document.createElement("div"),Dd.innerHTML="<svg>"+t.valueOf().toString()+"</svg>";for(var a=Dd.firstChild;e.firstChild;)e.removeChild(e.firstChild);for(;a.firstChild;)e.appendChild(a.firstChild);return}e.innerHTML=t}),di=1,go=3,Qn=8,mo=9,Ku=11,Io=function(e,t){if(t){var a=e.firstChild;if(a&&a===e.lastChild&&a.nodeType===go){a.nodeValue=t;return}}e.textContent=t},L0={animation:["animationDelay","animationDirection","animationDuration","animationFillMode","animationIterationCount","animationName","animationPlayState","animationTimingFunction"],background:["backgroundAttachment","backgroundClip","backgroundColor","backgroundImage","backgroundOrigin","backgroundPositionX","backgroundPositionY","backgroundRepeat","backgroundSize"],backgroundPosition:["backgroundPositionX","backgroundPositionY"],border:["borderBottomColor","borderBottomStyle","borderBottomWidth","borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth","borderLeftColor","borderLeftStyle","borderLeftWidth","borderRightColor","borderRightStyle","borderRightWidth","borderTopColor","borderTopStyle","borderTopWidth"],borderBlockEnd:["borderBlockEndColor","borderBlockEndStyle","borderBlockEndWidth"],borderBlockStart:["borderBlockStartColor","borderBlockStartStyle","borderBlockStartWidth"],borderBottom:["borderBottomColor","borderBottomStyle","borderBottomWidth"],borderColor:["borderBottomColor","borderLeftColor","borderRightColor","borderTopColor"],borderImage:["borderImageOutset","borderImageRepeat","borderImageSlice","borderImageSource","borderImageWidth"],borderInlineEnd:["borderInlineEndColor","borderInlineEndStyle","borderInlineEndWidth"],borderInlineStart:["borderInlineStartColor","borderInlineStartStyle","borderInlineStartWidth"],borderLeft:["borderLeftColor","borderLeftStyle","borderLeftWidth"],borderRadius:["borderBottomLeftRadius","borderBottomRightRadius","borderTopLeftRadius","borderTopRightRadius"],borderRight:["borderRightColor","borderRightStyle","borderRightWidth"],borderStyle:["borderBottomStyle","borderLeftStyle","borderRightStyle","borderTopStyle"],borderTop:["borderTopColor","borderTopStyle","borderTopWidth"],borderWidth:["borderBottomWidth","borderLeftWidth","borderRightWidth","borderTopWidth"],columnRule:["columnRuleColor","columnRuleStyle","columnRuleWidth"],columns:["columnCount","columnWidth"],flex:["flexBasis","flexGrow","flexShrink"],flexFlow:["flexDirection","flexWrap"],font:["fontFamily","fontFeatureSettings","fontKerning","fontLanguageOverride","fontSize","fontSizeAdjust","fontStretch","fontStyle","fontVariant","fontVariantAlternates","fontVariantCaps","fontVariantEastAsian","fontVariantLigatures","fontVariantNumeric","fontVariantPosition","fontWeight","lineHeight"],fontVariant:["fontVariantAlternates","fontVariantCaps","fontVariantEastAsian","fontVariantLigatures","fontVariantNumeric","fontVariantPosition"],gap:["columnGap","rowGap"],grid:["gridAutoColumns","gridAutoFlow","gridAutoRows","gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],gridArea:["gridColumnEnd","gridColumnStart","gridRowEnd","gridRowStart"],gridColumn:["gridColumnEnd","gridColumnStart"],gridColumnGap:["columnGap"],gridGap:["columnGap","rowGap"],gridRow:["gridRowEnd","gridRowStart"],gridRowGap:["rowGap"],gridTemplate:["gridTemplateAreas","gridTemplateColumns","gridTemplateRows"],listStyle:["listStyleImage","listStylePosition","listStyleType"],margin:["marginBottom","marginLeft","marginRight","marginTop"],marker:["markerEnd","markerMid","markerStart"],mask:["maskClip","maskComposite","maskImage","maskMode","maskOrigin","maskPositionX","maskPositionY","maskRepeat","maskSize"],maskPosition:["maskPositionX","maskPositionY"],outline:["outlineColor","outlineStyle","outlineWidth"],overflow:["overflowX","overflowY"],padding:["paddingBottom","paddingLeft","paddingRight","paddingTop"],placeContent:["alignContent","justifyContent"],placeItems:["alignItems","justifyItems"],placeSelf:["alignSelf","justifySelf"],textDecoration:["textDecorationColor","textDecorationLine","textDecorationStyle"],textEmphasis:["textEmphasisColor","textEmphasisStyle"],transition:["transitionDelay","transitionDuration","transitionProperty","transitionTimingFunction"],wordWrap:["overflowWrap"]},As={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0};function Tm(e,t){return e+t.charAt(0).toUpperCase()+t.substring(1)}var km=["Webkit","ms","Moz","O"];Object.keys(As).forEach(function(e){km.forEach(function(t){As[Tm(t,e)]=As[e]})});function Md(e,t,a){var s=t==null||typeof t=="boolean"||t==="";return s?"":!a&&typeof t=="number"&&t!==0&&!(As.hasOwnProperty(e)&&As[e])?t+"px":(Ur(t,e),(""+t).trim())}var Rm=/([A-Z])/g,js=/^ms-/;function z0(e){return e.replace(Rm,"-$1").toLowerCase().replace(js,"-ms-")}var Dm=function(){};{var N0=/^(?:webkit|moz|o)[A-Z]/,Mm=/^-ms-/,$m=/-(.)/g,_s=/;\s*$/,Ba={},zp={},Qu=!1,Om=!1,Am=function(e){return e.replace($m,function(t,a){return a.toUpperCase()})},Np=function(e){Ba.hasOwnProperty(e)&&Ba[e]||(Ba[e]=!0,y("Unsupported style property %s. Did you mean %s?",e,Am(e.replace(Mm,"ms-"))))},Pp=function(e){Ba.hasOwnProperty(e)&&Ba[e]||(Ba[e]=!0,y("Unsupported vendor-prefixed style property %s. Did you mean %s?",e,e.charAt(0).toUpperCase()+e.slice(1)))},jm=function(e,t){zp.hasOwnProperty(t)&&zp[t]||(zp[t]=!0,y(`Style property values shouldn't contain a semicolon. Try "%s: %s" instead.`,e,t.replace(_s,"")))},_m=function(e,t){Qu||(Qu=!0,y("`NaN` is an invalid value for the `%s` css style property.",e))},Lm=function(e,t){Om||(Om=!0,y("`Infinity` is an invalid value for the `%s` css style property.",e))};Dm=function(e,t){e.indexOf("-")>-1?Np(e):N0.test(e)?Pp(e):_s.test(t)&&jm(e,t),typeof t=="number"&&(isNaN(t)?_m(e,t):isFinite(t)||Lm(e,t))}}var P0=Dm;function F0(e){{var t="",a="";for(var s in e)if(e.hasOwnProperty(s)){var c=e[s];if(c!=null){var p=s.indexOf("--")===0;t+=a+(p?s:z0(s))+":",t+=Md(s,c,p),a=";"}}return t||null}}function zm(e,t){var a=e.style;for(var s in t)if(t.hasOwnProperty(s)){var c=s.indexOf("--")===0;c||P0(s,t[s]);var p=Md(s,t[s],c);s==="float"&&(s="cssFloat"),c?a.setProperty(s,p):a[s]=p}}function B0(e){return e==null||typeof e=="boolean"||e===""}function Nm(e){var t={};for(var a in e)for(var s=L0[a]||[a],c=0;c<s.length;c++)t[s[c]]=a;return t}function ba(e,t){{if(!t)return;var a=Nm(e),s=Nm(t),c={};for(var p in a){var v=a[p],w=s[p];if(w&&v!==w){var C=v+","+w;if(c[C])continue;c[C]=!0,y("%s a style property during rerender (%s) when a conflicting property is set (%s) can lead to styling bugs. To avoid this, don't mix shorthand and non-shorthand properties for the same value; instead, replace the shorthand with separate values.",B0(e[v])?"Removing":"Updating",v,w)}}}}var qu={area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0},Pm=gt({menuitem:!0},qu),Fm="__html";function $d(e,t){if(t){if(Pm[e]&&(t.children!=null||t.dangerouslySetInnerHTML!=null))throw new Error(e+" is a void element tag and must neither have `children` nor use `dangerouslySetInnerHTML`.");if(t.dangerouslySetInnerHTML!=null){if(t.children!=null)throw new Error("Can only set one of `children` or `props.dangerouslySetInnerHTML`.");if(typeof t.dangerouslySetInnerHTML!="object"||!(Fm in t.dangerouslySetInnerHTML))throw new Error("`props.dangerouslySetInnerHTML` must be in the form `{__html: ...}`. Please visit https://reactjs.org/link/dangerously-set-inner-html for more information.")}if(!t.suppressContentEditableWarning&&t.contentEditable&&t.children!=null&&y("A component is `contentEditable` and contains `children` managed by React. It is now your responsibility to guarantee that none of those nodes are unexpectedly modified or duplicated. This is probably not intentional."),t.style!=null&&typeof t.style!="object")throw new Error("The `style` prop expects a mapping from style properties to values, not a string. For example, style={{marginRight: spacing + 'em'}} when using JSX.")}}function Uo(e,t){if(e.indexOf("-")===-1)return typeof t.is=="string";switch(e){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var Ls={accept:"accept",acceptcharset:"acceptCharset","accept-charset":"acceptCharset",accesskey:"accessKey",action:"action",allowfullscreen:"allowFullScreen",alt:"alt",as:"as",async:"async",autocapitalize:"autoCapitalize",autocomplete:"autoComplete",autocorrect:"autoCorrect",autofocus:"autoFocus",autoplay:"autoPlay",autosave:"autoSave",capture:"capture",cellpadding:"cellPadding",cellspacing:"cellSpacing",challenge:"challenge",charset:"charSet",checked:"checked",children:"children",cite:"cite",class:"className",classid:"classID",classname:"className",cols:"cols",colspan:"colSpan",content:"content",contenteditable:"contentEditable",contextmenu:"contextMenu",controls:"controls",controlslist:"controlsList",coords:"coords",crossorigin:"crossOrigin",dangerouslysetinnerhtml:"dangerouslySetInnerHTML",data:"data",datetime:"dateTime",default:"default",defaultchecked:"defaultChecked",defaultvalue:"defaultValue",defer:"defer",dir:"dir",disabled:"disabled",disablepictureinpicture:"disablePictureInPicture",disableremoteplayback:"disableRemotePlayback",download:"download",draggable:"draggable",enctype:"encType",enterkeyhint:"enterKeyHint",for:"htmlFor",form:"form",formmethod:"formMethod",formaction:"formAction",formenctype:"formEncType",formnovalidate:"formNoValidate",formtarget:"formTarget",frameborder:"frameBorder",headers:"headers",height:"height",hidden:"hidden",high:"high",href:"href",hreflang:"hrefLang",htmlfor:"htmlFor",httpequiv:"httpEquiv","http-equiv":"httpEquiv",icon:"icon",id:"id",imagesizes:"imageSizes",imagesrcset:"imageSrcSet",innerhtml:"innerHTML",inputmode:"inputMode",integrity:"integrity",is:"is",itemid:"itemID",itemprop:"itemProp",itemref:"itemRef",itemscope:"itemScope",itemtype:"itemType",keyparams:"keyParams",keytype:"keyType",kind:"kind",label:"label",lang:"lang",list:"list",loop:"loop",low:"low",manifest:"manifest",marginwidth:"marginWidth",marginheight:"marginHeight",max:"max",maxlength:"maxLength",media:"media",mediagroup:"mediaGroup",method:"method",min:"min",minlength:"minLength",multiple:"multiple",muted:"muted",name:"name",nomodule:"noModule",nonce:"nonce",novalidate:"noValidate",open:"open",optimum:"optimum",pattern:"pattern",placeholder:"placeholder",playsinline:"playsInline",poster:"poster",preload:"preload",profile:"profile",radiogroup:"radioGroup",readonly:"readOnly",referrerpolicy:"referrerPolicy",rel:"rel",required:"required",reversed:"reversed",role:"role",rows:"rows",rowspan:"rowSpan",sandbox:"sandbox",scope:"scope",scoped:"scoped",scrolling:"scrolling",seamless:"seamless",selected:"selected",shape:"shape",size:"size",sizes:"sizes",span:"span",spellcheck:"spellCheck",src:"src",srcdoc:"srcDoc",srclang:"srcLang",srcset:"srcSet",start:"start",step:"step",style:"style",summary:"summary",tabindex:"tabIndex",target:"target",title:"title",type:"type",usemap:"useMap",value:"value",width:"width",wmode:"wmode",wrap:"wrap",about:"about",accentheight:"accentHeight","accent-height":"accentHeight",accumulate:"accumulate",additive:"additive",alignmentbaseline:"alignmentBaseline","alignment-baseline":"alignmentBaseline",allowreorder:"allowReorder",alphabetic:"alphabetic",amplitude:"amplitude",arabicform:"arabicForm","arabic-form":"arabicForm",ascent:"ascent",attributename:"attributeName",attributetype:"attributeType",autoreverse:"autoReverse",azimuth:"azimuth",basefrequency:"baseFrequency",baselineshift:"baselineShift","baseline-shift":"baselineShift",baseprofile:"baseProfile",bbox:"bbox",begin:"begin",bias:"bias",by:"by",calcmode:"calcMode",capheight:"capHeight","cap-height":"capHeight",clip:"clip",clippath:"clipPath","clip-path":"clipPath",clippathunits:"clipPathUnits",cliprule:"clipRule","clip-rule":"clipRule",color:"color",colorinterpolation:"colorInterpolation","color-interpolation":"colorInterpolation",colorinterpolationfilters:"colorInterpolationFilters","color-interpolation-filters":"colorInterpolationFilters",colorprofile:"colorProfile","color-profile":"colorProfile",colorrendering:"colorRendering","color-rendering":"colorRendering",contentscripttype:"contentScriptType",contentstyletype:"contentStyleType",cursor:"cursor",cx:"cx",cy:"cy",d:"d",datatype:"datatype",decelerate:"decelerate",descent:"descent",diffuseconstant:"diffuseConstant",direction:"direction",display:"display",divisor:"divisor",dominantbaseline:"dominantBaseline","dominant-baseline":"dominantBaseline",dur:"dur",dx:"dx",dy:"dy",edgemode:"edgeMode",elevation:"elevation",enablebackground:"enableBackground","enable-background":"enableBackground",end:"end",exponent:"exponent",externalresourcesrequired:"externalResourcesRequired",fill:"fill",fillopacity:"fillOpacity","fill-opacity":"fillOpacity",fillrule:"fillRule","fill-rule":"fillRule",filter:"filter",filterres:"filterRes",filterunits:"filterUnits",floodopacity:"floodOpacity","flood-opacity":"floodOpacity",floodcolor:"floodColor","flood-color":"floodColor",focusable:"focusable",fontfamily:"fontFamily","font-family":"fontFamily",fontsize:"fontSize","font-size":"fontSize",fontsizeadjust:"fontSizeAdjust","font-size-adjust":"fontSizeAdjust",fontstretch:"fontStretch","font-stretch":"fontStretch",fontstyle:"fontStyle","font-style":"fontStyle",fontvariant:"fontVariant","font-variant":"fontVariant",fontweight:"fontWeight","font-weight":"fontWeight",format:"format",from:"from",fx:"fx",fy:"fy",g1:"g1",g2:"g2",glyphname:"glyphName","glyph-name":"glyphName",glyphorientationhorizontal:"glyphOrientationHorizontal","glyph-orientation-horizontal":"glyphOrientationHorizontal",glyphorientationvertical:"glyphOrientationVertical","glyph-orientation-vertical":"glyphOrientationVertical",glyphref:"glyphRef",gradienttransform:"gradientTransform",gradientunits:"gradientUnits",hanging:"hanging",horizadvx:"horizAdvX","horiz-adv-x":"horizAdvX",horizoriginx:"horizOriginX","horiz-origin-x":"horizOriginX",ideographic:"ideographic",imagerendering:"imageRendering","image-rendering":"imageRendering",in2:"in2",in:"in",inlist:"inlist",intercept:"intercept",k1:"k1",k2:"k2",k3:"k3",k4:"k4",k:"k",kernelmatrix:"kernelMatrix",kernelunitlength:"kernelUnitLength",kerning:"kerning",keypoints:"keyPoints",keysplines:"keySplines",keytimes:"keyTimes",lengthadjust:"lengthAdjust",letterspacing:"letterSpacing","letter-spacing":"letterSpacing",lightingcolor:"lightingColor","lighting-color":"lightingColor",limitingconeangle:"limitingConeAngle",local:"local",markerend:"markerEnd","marker-end":"markerEnd",markerheight:"markerHeight",markermid:"markerMid","marker-mid":"markerMid",markerstart:"markerStart","marker-start":"markerStart",markerunits:"markerUnits",markerwidth:"markerWidth",mask:"mask",maskcontentunits:"maskContentUnits",maskunits:"maskUnits",mathematical:"mathematical",mode:"mode",numoctaves:"numOctaves",offset:"offset",opacity:"opacity",operator:"operator",order:"order",orient:"orient",orientation:"orientation",origin:"origin",overflow:"overflow",overlineposition:"overlinePosition","overline-position":"overlinePosition",overlinethickness:"overlineThickness","overline-thickness":"overlineThickness",paintorder:"paintOrder","paint-order":"paintOrder",panose1:"panose1","panose-1":"panose1",pathlength:"pathLength",patterncontentunits:"patternContentUnits",patterntransform:"patternTransform",patternunits:"patternUnits",pointerevents:"pointerEvents","pointer-events":"pointerEvents",points:"points",pointsatx:"pointsAtX",pointsaty:"pointsAtY",pointsatz:"pointsAtZ",prefix:"prefix",preservealpha:"preserveAlpha",preserveaspectratio:"preserveAspectRatio",primitiveunits:"primitiveUnits",property:"property",r:"r",radius:"radius",refx:"refX",refy:"refY",renderingintent:"renderingIntent","rendering-intent":"renderingIntent",repeatcount:"repeatCount",repeatdur:"repeatDur",requiredextensions:"requiredExtensions",requiredfeatures:"requiredFeatures",resource:"resource",restart:"restart",result:"result",results:"results",rotate:"rotate",rx:"rx",ry:"ry",scale:"scale",security:"security",seed:"seed",shaperendering:"shapeRendering","shape-rendering":"shapeRendering",slope:"slope",spacing:"spacing",specularconstant:"specularConstant",specularexponent:"specularExponent",speed:"speed",spreadmethod:"spreadMethod",startoffset:"startOffset",stddeviation:"stdDeviation",stemh:"stemh",stemv:"stemv",stitchtiles:"stitchTiles",stopcolor:"stopColor","stop-color":"stopColor",stopopacity:"stopOpacity","stop-opacity":"stopOpacity",strikethroughposition:"strikethroughPosition","strikethrough-position":"strikethroughPosition",strikethroughthickness:"strikethroughThickness","strikethrough-thickness":"strikethroughThickness",string:"string",stroke:"stroke",strokedasharray:"strokeDasharray","stroke-dasharray":"strokeDasharray",strokedashoffset:"strokeDashoffset","stroke-dashoffset":"strokeDashoffset",strokelinecap:"strokeLinecap","stroke-linecap":"strokeLinecap",strokelinejoin:"strokeLinejoin","stroke-linejoin":"strokeLinejoin",strokemiterlimit:"strokeMiterlimit","stroke-miterlimit":"strokeMiterlimit",strokewidth:"strokeWidth","stroke-width":"strokeWidth",strokeopacity:"strokeOpacity","stroke-opacity":"strokeOpacity",suppresscontenteditablewarning:"suppressContentEditableWarning",suppresshydrationwarning:"suppressHydrationWarning",surfacescale:"surfaceScale",systemlanguage:"systemLanguage",tablevalues:"tableValues",targetx:"targetX",targety:"targetY",textanchor:"textAnchor","text-anchor":"textAnchor",textdecoration:"textDecoration","text-decoration":"textDecoration",textlength:"textLength",textrendering:"textRendering","text-rendering":"textRendering",to:"to",transform:"transform",typeof:"typeof",u1:"u1",u2:"u2",underlineposition:"underlinePosition","underline-position":"underlinePosition",underlinethickness:"underlineThickness","underline-thickness":"underlineThickness",unicode:"unicode",unicodebidi:"unicodeBidi","unicode-bidi":"unicodeBidi",unicoderange:"unicodeRange","unicode-range":"unicodeRange",unitsperem:"unitsPerEm","units-per-em":"unitsPerEm",unselectable:"unselectable",valphabetic:"vAlphabetic","v-alphabetic":"vAlphabetic",values:"values",vectoreffect:"vectorEffect","vector-effect":"vectorEffect",version:"version",vertadvy:"vertAdvY","vert-adv-y":"vertAdvY",vertoriginx:"vertOriginX","vert-origin-x":"vertOriginX",vertoriginy:"vertOriginY","vert-origin-y":"vertOriginY",vhanging:"vHanging","v-hanging":"vHanging",videographic:"vIdeographic","v-ideographic":"vIdeographic",viewbox:"viewBox",viewtarget:"viewTarget",visibility:"visibility",vmathematical:"vMathematical","v-mathematical":"vMathematical",vocab:"vocab",widths:"widths",wordspacing:"wordSpacing","word-spacing":"wordSpacing",writingmode:"writingMode","writing-mode":"writingMode",x1:"x1",x2:"x2",x:"x",xchannelselector:"xChannelSelector",xheight:"xHeight","x-height":"xHeight",xlinkactuate:"xlinkActuate","xlink:actuate":"xlinkActuate",xlinkarcrole:"xlinkArcrole","xlink:arcrole":"xlinkArcrole",xlinkhref:"xlinkHref","xlink:href":"xlinkHref",xlinkrole:"xlinkRole","xlink:role":"xlinkRole",xlinkshow:"xlinkShow","xlink:show":"xlinkShow",xlinktitle:"xlinkTitle","xlink:title":"xlinkTitle",xlinktype:"xlinkType","xlink:type":"xlinkType",xmlbase:"xmlBase","xml:base":"xmlBase",xmllang:"xmlLang","xml:lang":"xmlLang",xmlns:"xmlns","xml:space":"xmlSpace",xmlnsxlink:"xmlnsXlink","xmlns:xlink":"xmlnsXlink",xmlspace:"xmlSpace",y1:"y1",y2:"y2",y:"y",ychannelselector:"yChannelSelector",z:"z",zoomandpan:"zoomAndPan"},Bm={"aria-current":0,"aria-description":0,"aria-details":0,"aria-disabled":0,"aria-hidden":0,"aria-invalid":0,"aria-keyshortcuts":0,"aria-label":0,"aria-roledescription":0,"aria-autocomplete":0,"aria-checked":0,"aria-expanded":0,"aria-haspopup":0,"aria-level":0,"aria-modal":0,"aria-multiline":0,"aria-multiselectable":0,"aria-orientation":0,"aria-placeholder":0,"aria-pressed":0,"aria-readonly":0,"aria-required":0,"aria-selected":0,"aria-sort":0,"aria-valuemax":0,"aria-valuemin":0,"aria-valuenow":0,"aria-valuetext":0,"aria-atomic":0,"aria-busy":0,"aria-live":0,"aria-relevant":0,"aria-dropeffect":0,"aria-grabbed":0,"aria-activedescendant":0,"aria-colcount":0,"aria-colindex":0,"aria-colspan":0,"aria-controls":0,"aria-describedby":0,"aria-errormessage":0,"aria-flowto":0,"aria-labelledby":0,"aria-owns":0,"aria-posinset":0,"aria-rowcount":0,"aria-rowindex":0,"aria-rowspan":0,"aria-setsize":0},zs={},Ns=new RegExp("^(aria)-["+we+"]*$"),Fp=new RegExp("^(aria)[A-Z]["+we+"]*$");function Xu(e,t){{if(Pn.call(zs,t)&&zs[t])return!0;if(Fp.test(t)){var a="aria-"+t.slice(4).toLowerCase(),s=Bm.hasOwnProperty(a)?a:null;if(s==null)return y("Invalid ARIA attribute `%s`. ARIA attributes follow the pattern aria-* and must be lowercase.",t),zs[t]=!0,!0;if(t!==s)return y("Invalid ARIA attribute `%s`. Did you mean `%s`?",t,s),zs[t]=!0,!0}if(Ns.test(t)){var c=t.toLowerCase(),p=Bm.hasOwnProperty(c)?c:null;if(p==null)return zs[t]=!0,!1;if(t!==p)return y("Unknown ARIA attribute `%s`. Did you mean `%s`?",t,p),zs[t]=!0,!0}}return!0}function Bp(e,t){{var a=[];for(var s in t){var c=Xu(e,s);c||a.push(s)}var p=a.map(function(v){return"`"+v+"`"}).join(", ");a.length===1?y("Invalid aria prop %s on <%s> tag. For details, see https://reactjs.org/link/invalid-aria-props",p,e):a.length>1&&y("Invalid aria props %s on <%s> tag. For details, see https://reactjs.org/link/invalid-aria-props",p,e)}}function Im(e,t){Uo(e,t)||Bp(e,t)}var Ju=!1;function Ps(e,t){{if(e!=="input"&&e!=="textarea"&&e!=="select")return;t!=null&&t.value===null&&!Ju&&(Ju=!0,e==="select"&&t.multiple?y("`value` prop on `%s` should not be null. Consider using an empty array when `multiple` is set to `true` to clear the component or `undefined` for uncontrolled components.",e):y("`value` prop on `%s` should not be null. Consider using an empty string to clear the component or `undefined` for uncontrolled components.",e))}}var Od=function(){};{var Nr={},Zu=/^on./,Um=/^on[^A-Z]/,Hm=new RegExp("^(aria)-["+we+"]*$"),Vm=new RegExp("^(aria)[A-Z]["+we+"]*$");Od=function(e,t,a,s){if(Pn.call(Nr,t)&&Nr[t])return!0;var c=t.toLowerCase();if(c==="onfocusin"||c==="onfocusout")return y("React uses onFocus and onBlur instead of onFocusIn and onFocusOut. All React events are normalized to bubble, so onFocusIn and onFocusOut are not needed/supported by React."),Nr[t]=!0,!0;if(s!=null){var p=s.registrationNameDependencies,v=s.possibleRegistrationNames;if(p.hasOwnProperty(t))return!0;var w=v.hasOwnProperty(c)?v[c]:null;if(w!=null)return y("Invalid event handler property `%s`. Did you mean `%s`?",t,w),Nr[t]=!0,!0;if(Zu.test(t))return y("Unknown event handler property `%s`. It will be ignored.",t),Nr[t]=!0,!0}else if(Zu.test(t))return Um.test(t)&&y("Invalid event handler property `%s`. React events use the camelCase naming convention, for example `onClick`.",t),Nr[t]=!0,!0;if(Hm.test(t)||Vm.test(t))return!0;if(c==="innerhtml")return y("Directly setting property `innerHTML` is not permitted. For more information, lookup documentation on `dangerouslySetInnerHTML`."),Nr[t]=!0,!0;if(c==="aria")return y("The `aria` attribute is reserved for future use in React. Pass individual `aria-` attributes instead."),Nr[t]=!0,!0;if(c==="is"&&a!==null&&a!==void 0&&typeof a!="string")return y("Received a `%s` for a string attribute `is`. If this is expected, cast the value to a string.",typeof a),Nr[t]=!0,!0;if(typeof a=="number"&&isNaN(a))return y("Received NaN for the `%s` attribute. If this is expected, cast the value to a string.",t),Nr[t]=!0,!0;var C=vn(t),R=C!==null&&C.type===cr;if(Ls.hasOwnProperty(c)){var O=Ls[c];if(O!==t)return y("Invalid DOM property `%s`. Did you mean `%s`?",t,O),Nr[t]=!0,!0}else if(!R&&t!==c)return y("React does not recognize the `%s` prop on a DOM element. If you intentionally want it to appear in the DOM as a custom attribute, spell it as lowercase `%s` instead. If you accidentally passed it from a parent component, remove it from the DOM element.",t,c),Nr[t]=!0,!0;return typeof a=="boolean"&&Sn(t,a,C,!1)?(a?y('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.',a,t,t,a,t):y('Received `%s` for a non-boolean attribute `%s`.\n\nIf you want to write it to the DOM, pass a string instead: %s="%s" or %s={value.toString()}.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.',a,t,t,a,t,t,t),Nr[t]=!0,!0):R?!0:Sn(t,a,C,!1)?(Nr[t]=!0,!1):((a==="false"||a==="true")&&C!==null&&C.type===Kn&&(y("Received the string `%s` for the boolean attribute `%s`. %s Did you mean %s={%s}?",a,t,a==="false"?"The browser will interpret it as a truthy value.":'Although this works, it will not work as expected if you pass the string "false".',t,a),Nr[t]=!0),!0)}}var Wm=function(e,t,a){{var s=[];for(var c in t){var p=Od(e,c,t[c],a);p||s.push(c)}var v=s.map(function(w){return"`"+w+"`"}).join(", ");s.length===1?y("Invalid value for prop %s on <%s> tag. Either remove it from the element, or pass a string or number value to keep it in the DOM. For details, see https://reactjs.org/link/attribute-behavior ",v,e):s.length>1&&y("Invalid values for props %s on <%s> tag. Either remove them from the element, or pass a string or number value to keep them in the DOM. For details, see https://reactjs.org/link/attribute-behavior ",v,e)}};function Ym(e,t,a){Uo(e,t)||Wm(e,t,a)}var Ip=1,Ia=2,Al=4,Up=Ip|Ia|Al,ec=null;function I0(e){ec!==null&&y("Expected currently replaying event to be null. This error is likely caused by a bug in React. Please file an issue."),ec=e}function tc(){ec===null&&y("Expected currently replaying event to not be null. This error is likely caused by a bug in React. Please file an issue."),ec=null}function U0(e){return e===ec}function Ad(e){var t=e.target||e.srcElement||window;return t.correspondingUseElement&&(t=t.correspondingUseElement),t.nodeType===go?t.parentNode:t}var jd=null,Xt=null,Ho=null;function nc(e){var t=du(e);if(t){if(typeof jd!="function")throw new Error("setRestoreImplementation() needs to be called to handle a target for controlled events. This error is likely caused by a bug in React. Please file an issue.");var a=t.stateNode;if(a){var s=Xv(a);jd(t.stateNode,t.type,s)}}}function rc(e){jd=e}function Hp(e){Xt?Ho?Ho.push(e):Ho=[e]:Xt=e}function Vp(){return Xt!==null||Ho!==null}function Fs(){if(Xt){var e=Xt,t=Ho;if(Xt=null,Ho=null,nc(e),t)for(var a=0;a<t.length;a++)nc(t[a])}}var ic=function(e,t){return e(t)},jl=function(){},_d=!1;function H0(){var e=Vp();e&&(jl(),Fs())}function Gm(e,t,a){if(_d)return e(t,a);_d=!0;try{return ic(e,t,a)}finally{_d=!1,H0()}}function Km(e,t,a){ic=e,jl=a}function Ld(e){return e==="button"||e==="input"||e==="select"||e==="textarea"}function zd(e,t,a){switch(e){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":return!!(a.disabled&&Ld(t));default:return!1}}function _l(e,t){var a=e.stateNode;if(a===null)return null;var s=Xv(a);if(s===null)return null;var c=s[t];if(zd(t,e.type,s))return null;if(c&&typeof c!="function")throw new Error("Expected `"+t+"` listener to be a function, instead got a value of `"+typeof c+"` type.");return c}var ac=!1;if(an)try{var Ll={};Object.defineProperty(Ll,"passive",{get:function(){ac=!0}}),window.addEventListener("test",Ll,Ll),window.removeEventListener("test",Ll,Ll)}catch{ac=!1}function Nd(e,t,a,s,c,p,v,w,C){var R=Array.prototype.slice.call(arguments,3);try{t.apply(a,R)}catch(O){this.onError(O)}}var Qm=Nd;if(typeof window<"u"&&typeof window.dispatchEvent=="function"&&typeof document<"u"&&typeof document.createEvent=="function"){var Pd=document.createElement("react");Qm=function(t,a,s,c,p,v,w,C,R){if(typeof document>"u"||document===null)throw new Error("The `document` global was defined when React was initialized, but is not defined anymore. This can happen in a test environment if a component schedules an update from an asynchronous callback, but the test has already finished running. To solve this, you can either unmount the component at the end of your test (and ensure that any asynchronous operations get canceled in `componentWillUnmount`), or you can change the test itself to be asynchronous.");var O=document.createEvent("Event"),U=!1,B=!0,X=window.event,Z=Object.getOwnPropertyDescriptor(window,"event");function te(){Pd.removeEventListener(ne,Je,!1),typeof window.event<"u"&&window.hasOwnProperty("event")&&(window.event=X)}var De=Array.prototype.slice.call(arguments,3);function Je(){U=!0,te(),a.apply(s,De),B=!1}var Ye,Bt=!1,_t=!1;function Y(G){if(Ye=G.error,Bt=!0,Ye===null&&G.colno===0&&G.lineno===0&&(_t=!0),G.defaultPrevented&&Ye!=null&&typeof Ye=="object")try{Ye._suppressLogging=!0}catch{}}var ne="react-"+(t||"invokeguardedcallback");if(window.addEventListener("error",Y),Pd.addEventListener(ne,Je,!1),O.initEvent(ne,!1,!1),Pd.dispatchEvent(O),Z&&Object.defineProperty(window,"event",Z),U&&B&&(Bt?_t&&(Ye=new Error("A cross-origin error was thrown. React doesn't have access to the actual error object in development. See https://reactjs.org/link/crossorigin-error for more information.")):Ye=new Error(`An error was thrown inside one of your components, but React doesn't know what it was. This is likely due to browser flakiness. React does its best to preserve the "Pause on exceptions" behavior of the DevTools, which requires some DEV-mode only tricks. It's possible that these don't work in your browser. Try triggering the error in production mode, or switching to a modern browser. If you suspect that this is actually an issue with React, please file an issue.`),this.onError(Ye)),window.removeEventListener("error",Y),!U)return te(),Nd.apply(this,arguments)}}var V0=Qm,Bs=!1,Is=null,wa=!1,Fd=null,Us={onError:function(e){Bs=!0,Is=e}};function ta(e,t,a,s,c,p,v,w,C){Bs=!1,Is=null,V0.apply(Us,arguments)}function oc(e,t,a,s,c,p,v,w,C){if(ta.apply(this,arguments),Bs){var R=Yp();wa||(wa=!0,Fd=R)}}function vo(){if(wa){var e=Fd;throw wa=!1,Fd=null,e}}function Wp(){return Bs}function Yp(){if(Bs){var e=Is;return Bs=!1,Is=null,e}else throw new Error("clearCaughtError was called but no error was captured. This error is likely caused by a bug in React. Please file an issue.")}function Hs(e){return e._reactInternals}function zl(e){return e._reactInternals!==void 0}function lc(e,t){e._reactInternals=t}var Ke=0,yo=1,Ln=2,At=4,fi=16,tn=32,gn=64,Et=128,Rn=256,qn=512,na=1024,Ai=2048,zn=4096,Ua=8192,Bd=16384,qm=32767,Nl=32768,Pr=65536,Sa=131072,sc=1048576,uc=2097152,Vo=4194304,Gp=8388608,Yr=16777216,Wo=33554432,Yo=At|na|0,Vs=Ln|At|fi|tn|qn|zn|Ua,Go=At|gn|qn|Ua,wr=Ai|fi,Xn=Vo|Gp|uc,Pl=d.ReactCurrentOwner;function Gr(e){var t=e,a=e;if(e.alternate)for(;t.return;)t=t.return;else{var s=t;do t=s,(t.flags&(Ln|zn))!==Ke&&(a=t.return),s=t.return;while(s)}return t.tag===j?a:null}function Ha(e){if(e.tag===le){var t=e.memoizedState;if(t===null){var a=e.alternate;a!==null&&(t=a.memoizedState)}if(t!==null)return t.dehydrated}return null}function Ko(e){return e.tag===j?e.stateNode.containerInfo:null}function Xm(e){return Gr(e)===e}function Kp(e){{var t=Pl.current;if(t!==null&&t.tag===M){var a=t,s=a.stateNode;s._warnedAboutRefsInRender||y("%s is accessing isMounted inside its render() function. render() should be a pure function of props and state. It should never access something that requires stale data from the previous render, such as refs. Move this logic to componentDidMount and componentDidUpdate instead.",lt(a)||"A component"),s._warnedAboutRefsInRender=!0}}var c=Hs(e);return c?Gr(c)===c:!1}function Id(e){if(Gr(e)!==e)throw new Error("Unable to find node on an unmounted component.")}function pi(e){var t=e.alternate;if(!t){var a=Gr(e);if(a===null)throw new Error("Unable to find node on an unmounted component.");return a!==e?null:e}for(var s=e,c=t;;){var p=s.return;if(p===null)break;var v=p.alternate;if(v===null){var w=p.return;if(w!==null){s=c=w;continue}break}if(p.child===v.child){for(var C=p.child;C;){if(C===s)return Id(p),e;if(C===c)return Id(p),t;C=C.sibling}throw new Error("Unable to find node on an unmounted component.")}if(s.return!==c.return)s=p,c=v;else{for(var R=!1,O=p.child;O;){if(O===s){R=!0,s=p,c=v;break}if(O===c){R=!0,c=p,s=v;break}O=O.sibling}if(!R){for(O=v.child;O;){if(O===s){R=!0,s=v,c=p;break}if(O===c){R=!0,c=v,s=p;break}O=O.sibling}if(!R)throw new Error("Child was not found in either parent set. This indicates a bug in React related to the return pointer. Please file an issue.")}}if(s.alternate!==c)throw new Error("Return fibers should always be each others' alternates. This error is likely caused by a bug in React. Please file an issue.")}if(s.tag!==j)throw new Error("Unable to find node on an unmounted component.");return s.stateNode.current===s?e:t}function hi(e){var t=pi(e);return t!==null?En(t):null}function En(e){if(e.tag===L||e.tag===K)return e;for(var t=e.child;t!==null;){var a=En(t);if(a!==null)return a;t=t.sibling}return null}function Ca(e){var t=pi(e);return t!==null?Qp(t):null}function Qp(e){if(e.tag===L||e.tag===K)return e;for(var t=e.child;t!==null;){if(t.tag!==H){var a=Qp(t);if(a!==null)return a}t=t.sibling}return null}var qp=l.unstable_scheduleCallback,Xp=l.unstable_cancelCallback,Jp=l.unstable_shouldYield,Jm=l.unstable_requestPaint,In=l.unstable_now,Zm=l.unstable_getCurrentPriorityLevel,xo=l.unstable_ImmediatePriority,cc=l.unstable_UserBlockingPriority,Fl=l.unstable_NormalPriority,dc=l.unstable_LowPriority,Ws=l.unstable_IdlePriority,ev=l.unstable_yieldValue,tv=l.unstable_setDisableYieldValue,Ea=null,Sr=null,Ce=null,ji=!1,Fr=typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u";function Zp(e){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u")return!1;var t=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(t.isDisabled)return!0;if(!t.supportsFiber)return y("The installed version of React DevTools is too old and will not work with the current version of React. Please update React DevTools. https://reactjs.org/link/react-devtools"),!0;try{et&&(e=gt({},e,{getLaneLabelMap:rh,injectProfilingHooks:Bl})),Ea=t.inject(e),Sr=t}catch(a){y("React instrumentation encountered an error: %s.",a)}return!!t.checkDCE}function eh(e,t){if(Sr&&typeof Sr.onScheduleFiberRoot=="function")try{Sr.onScheduleFiberRoot(Ea,e,t)}catch(a){ji||(ji=!0,y("React instrumentation encountered an error: %s",a))}}function th(e,t){if(Sr&&typeof Sr.onCommitFiberRoot=="function")try{var a=(e.current.flags&Et)===Et;if(it){var s;switch(t){case xi:s=xo;break;case ia:s=cc;break;case Cr:s=Fl;break;case yf:s=Ws;break;default:s=Fl;break}Sr.onCommitFiberRoot(Ea,e,s,a)}}catch(c){ji||(ji=!0,y("React instrumentation encountered an error: %s",c))}}function nh(e){if(Sr&&typeof Sr.onPostCommitFiberRoot=="function")try{Sr.onPostCommitFiberRoot(Ea,e)}catch(t){ji||(ji=!0,y("React instrumentation encountered an error: %s",t))}}function Ys(e){if(Sr&&typeof Sr.onCommitFiberUnmount=="function")try{Sr.onCommitFiberUnmount(Ea,e)}catch(t){ji||(ji=!0,y("React instrumentation encountered an error: %s",t))}}function nn(e){if(typeof ev=="function"&&(tv(e),b(e)),Sr&&typeof Sr.setStrictMode=="function")try{Sr.setStrictMode(Ea,e)}catch(t){ji||(ji=!0,y("React instrumentation encountered an error: %s",t))}}function Bl(e){Ce=e}function rh(){{for(var e=new Map,t=1,a=0;a<uh;a++){var s=lv(t);e.set(t,s),t*=2}return e}}function nv(e){Ce!==null&&typeof Ce.markCommitStarted=="function"&&Ce.markCommitStarted(e)}function Va(){Ce!==null&&typeof Ce.markCommitStopped=="function"&&Ce.markCommitStopped()}function ra(e){Ce!==null&&typeof Ce.markComponentRenderStarted=="function"&&Ce.markComponentRenderStarted(e)}function Qo(){Ce!==null&&typeof Ce.markComponentRenderStopped=="function"&&Ce.markComponentRenderStopped()}function rv(e){Ce!==null&&typeof Ce.markComponentPassiveEffectMountStarted=="function"&&Ce.markComponentPassiveEffectMountStarted(e)}function bo(){Ce!==null&&typeof Ce.markComponentPassiveEffectMountStopped=="function"&&Ce.markComponentPassiveEffectMountStopped()}function qo(e){Ce!==null&&typeof Ce.markComponentPassiveEffectUnmountStarted=="function"&&Ce.markComponentPassiveEffectUnmountStarted(e)}function Ud(){Ce!==null&&typeof Ce.markComponentPassiveEffectUnmountStopped=="function"&&Ce.markComponentPassiveEffectUnmountStopped()}function iv(e){Ce!==null&&typeof Ce.markComponentLayoutEffectMountStarted=="function"&&Ce.markComponentLayoutEffectMountStarted(e)}function Hd(){Ce!==null&&typeof Ce.markComponentLayoutEffectMountStopped=="function"&&Ce.markComponentLayoutEffectMountStopped()}function ih(e){Ce!==null&&typeof Ce.markComponentLayoutEffectUnmountStarted=="function"&&Ce.markComponentLayoutEffectUnmountStarted(e)}function Gs(){Ce!==null&&typeof Ce.markComponentLayoutEffectUnmountStopped=="function"&&Ce.markComponentLayoutEffectUnmountStopped()}function Wa(e,t,a){Ce!==null&&typeof Ce.markComponentErrored=="function"&&Ce.markComponentErrored(e,t,a)}function fc(e,t,a){Ce!==null&&typeof Ce.markComponentSuspended=="function"&&Ce.markComponentSuspended(e,t,a)}function pc(e){Ce!==null&&typeof Ce.markLayoutEffectsStarted=="function"&&Ce.markLayoutEffectsStarted(e)}function Il(){Ce!==null&&typeof Ce.markLayoutEffectsStopped=="function"&&Ce.markLayoutEffectsStopped()}function ah(e){Ce!==null&&typeof Ce.markPassiveEffectsStarted=="function"&&Ce.markPassiveEffectsStarted(e)}function Ks(){Ce!==null&&typeof Ce.markPassiveEffectsStopped=="function"&&Ce.markPassiveEffectsStopped()}function oh(e){Ce!==null&&typeof Ce.markRenderStarted=="function"&&Ce.markRenderStarted(e)}function lh(){Ce!==null&&typeof Ce.markRenderYielded=="function"&&Ce.markRenderYielded()}function Dn(){Ce!==null&&typeof Ce.markRenderStopped=="function"&&Ce.markRenderStopped()}function Vd(e){Ce!==null&&typeof Ce.markRenderScheduled=="function"&&Ce.markRenderScheduled(e)}function sh(e,t){Ce!==null&&typeof Ce.markForceUpdateScheduled=="function"&&Ce.markForceUpdateScheduled(e,t)}function hc(e,t){Ce!==null&&typeof Ce.markStateUpdateScheduled=="function"&&Ce.markStateUpdateScheduled(e,t)}var Qe=0,Dt=1,zt=2,mt=8,cn=16,rr=Math.clz32?Math.clz32:mc,Wd=Math.log,gc=Math.LN2;function mc(e){var t=e>>>0;return t===0?32:31-(Wd(t)/gc|0)|0}var uh=31,ie=0,Jn=0,nt=1,Xo=2,pr=4,hr=8,gi=16,Ul=32,Jo=4194240,Qs=64,Yd=128,Gd=256,Kd=512,Qd=1024,qd=2048,Xd=4096,Jd=8192,Hl=16384,Zd=32768,qs=65536,Xs=131072,ef=262144,vc=524288,tf=1048576,nf=2097152,yc=130023424,Vl=4194304,xc=8388608,rf=16777216,af=33554432,of=67108864,av=Vl,Js=134217728,ov=268435455,bc=268435456,Zo=536870912,mi=1073741824;function lv(e){{if(e&nt)return"Sync";if(e&Xo)return"InputContinuousHydration";if(e&pr)return"InputContinuous";if(e&hr)return"DefaultHydration";if(e&gi)return"Default";if(e&Ul)return"TransitionHydration";if(e&Jo)return"Transition";if(e&yc)return"Retry";if(e&Js)return"SelectiveHydration";if(e&bc)return"IdleHydration";if(e&Zo)return"Idle";if(e&mi)return"Offscreen"}}var rn=-1,lf=Qs,sf=Vl;function wc(e){switch(Wl(e)){case nt:return nt;case Xo:return Xo;case pr:return pr;case hr:return hr;case gi:return gi;case Ul:return Ul;case Qs:case Yd:case Gd:case Kd:case Qd:case qd:case Xd:case Jd:case Hl:case Zd:case qs:case Xs:case ef:case vc:case tf:case nf:return e&Jo;case Vl:case xc:case rf:case af:case of:return e&yc;case Js:return Js;case bc:return bc;case Zo:return Zo;case mi:return mi;default:return y("Should have found matching lanes. This is a bug in React."),e}}function vi(e,t){var a=e.pendingLanes;if(a===ie)return ie;var s=ie,c=e.suspendedLanes,p=e.pingedLanes,v=a&ov;if(v!==ie){var w=v&~c;if(w!==ie)s=wc(w);else{var C=v&p;C!==ie&&(s=wc(C))}}else{var R=a&~c;R!==ie?s=wc(R):p!==ie&&(s=wc(p))}if(s===ie)return ie;if(t!==ie&&t!==s&&(t&c)===ie){var O=Wl(s),U=Wl(t);if(O>=U||O===gi&&(U&Jo)!==ie)return t}(s&pr)!==ie&&(s|=a&gi);var B=e.entangledLanes;if(B!==ie)for(var X=e.entanglements,Z=s&B;Z>0;){var te=Un(Z),De=1<<te;s|=X[te],Z&=~De}return s}function ch(e,t){for(var a=e.eventTimes,s=rn;t>0;){var c=Un(t),p=1<<c,v=a[c];v>s&&(s=v),t&=~p}return s}function uf(e,t){switch(e){case nt:case Xo:case pr:return t+250;case hr:case gi:case Ul:case Qs:case Yd:case Gd:case Kd:case Qd:case qd:case Xd:case Jd:case Hl:case Zd:case qs:case Xs:case ef:case vc:case tf:case nf:return t+5e3;case Vl:case xc:case rf:case af:case of:return rn;case Js:case bc:case Zo:case mi:return rn;default:return y("Should have found matching lanes. This is a bug in React."),rn}}function sv(e,t){for(var a=e.pendingLanes,s=e.suspendedLanes,c=e.pingedLanes,p=e.expirationTimes,v=a;v>0;){var w=Un(v),C=1<<w,R=p[w];R===rn?((C&s)===ie||(C&c)!==ie)&&(p[w]=uf(C,t)):R<=t&&(e.expiredLanes|=C),v&=~C}}function uv(e){return wc(e.pendingLanes)}function cf(e){var t=e.pendingLanes&~mi;return t!==ie?t:t&mi?mi:ie}function dh(e){return(e&nt)!==ie}function el(e){return(e&ov)!==ie}function df(e){return(e&yc)===e}function fh(e){var t=nt|pr|gi;return(e&t)===ie}function W0(e){return(e&Jo)===e}function Sc(e,t){var a=Xo|pr|hr|gi;return(t&a)!==ie}function cv(e,t){return(t&e.expiredLanes)!==ie}function ph(e){return(e&Jo)!==ie}function hh(){var e=lf;return lf<<=1,(lf&Jo)===ie&&(lf=Qs),e}function dv(){var e=sf;return sf<<=1,(sf&yc)===ie&&(sf=Vl),e}function Wl(e){return e&-e}function gr(e){return Wl(e)}function Un(e){return 31-rr(e)}function ff(e){return Un(e)}function yi(e,t){return(e&t)!==ie}function Yl(e,t){return(e&t)===t}function xt(e,t){return e|t}function Cc(e,t){return e&~t}function pf(e,t){return e&t}function Y0(e){return e}function gh(e,t){return e!==Jn&&e<t?e:t}function hf(e){for(var t=[],a=0;a<uh;a++)t.push(e);return t}function Zs(e,t,a){e.pendingLanes|=t,t!==Zo&&(e.suspendedLanes=ie,e.pingedLanes=ie);var s=e.eventTimes,c=ff(t);s[c]=a}function mh(e,t){e.suspendedLanes|=t,e.pingedLanes&=~t;for(var a=e.expirationTimes,s=t;s>0;){var c=Un(s),p=1<<c;a[c]=rn,s&=~p}}function gf(e,t,a){e.pingedLanes|=e.suspendedLanes&t}function fv(e,t){var a=e.pendingLanes&~t;e.pendingLanes=t,e.suspendedLanes=ie,e.pingedLanes=ie,e.expiredLanes&=t,e.mutableReadLanes&=t,e.entangledLanes&=t;for(var s=e.entanglements,c=e.eventTimes,p=e.expirationTimes,v=a;v>0;){var w=Un(v),C=1<<w;s[w]=ie,c[w]=rn,p[w]=rn,v&=~C}}function Ec(e,t){for(var a=e.entangledLanes|=t,s=e.entanglements,c=a;c;){var p=Un(c),v=1<<p;v&t|s[p]&t&&(s[p]|=t),c&=~v}}function mf(e,t){var a=Wl(t),s;switch(a){case pr:s=Xo;break;case gi:s=hr;break;case Qs:case Yd:case Gd:case Kd:case Qd:case qd:case Xd:case Jd:case Hl:case Zd:case qs:case Xs:case ef:case vc:case tf:case nf:case Vl:case xc:case rf:case af:case of:s=Ul;break;case Zo:s=bc;break;default:s=Jn;break}return(s&(e.suspendedLanes|t))!==Jn?Jn:s}function pv(e,t,a){if(Fr)for(var s=e.pendingUpdatersLaneMap;a>0;){var c=ff(a),p=1<<c,v=s[c];v.add(t),a&=~p}}function vh(e,t){if(Fr)for(var a=e.pendingUpdatersLaneMap,s=e.memoizedUpdaters;t>0;){var c=ff(t),p=1<<c,v=a[c];v.size>0&&(v.forEach(function(w){var C=w.alternate;(C===null||!s.has(C))&&s.add(w)}),v.clear()),t&=~p}}function vf(e,t){return null}var xi=nt,ia=pr,Cr=gi,yf=Zo,eu=Jn;function _i(){return eu}function ir(e){eu=e}function hv(e,t){var a=eu;try{return eu=e,t()}finally{eu=a}}function Tc(e,t){return e!==0&&e<t?e:t}function Br(e,t){return e>t?e:t}function yh(e,t){return e!==0&&e<t}function gv(e){var t=Wl(e);return yh(xi,t)?yh(ia,t)?el(t)?Cr:yf:ia:xi}function Gl(e){var t=e.current.memoizedState;return t.isDehydrated}var Er;function G0(e){Er=e}function Ne(e){Er(e)}var tl;function xh(e){tl=e}var bh;function K0(e){bh=e}var tu;function xf(e){tu=e}var bf;function mv(e){bf=e}var wf=!1,kc=[],Ya=null,Ga=null,Mn=null,Kr=new Map,aa=new Map,wo=[],vv=["mousedown","mouseup","touchcancel","touchend","touchstart","auxclick","dblclick","pointercancel","pointerdown","pointerup","dragend","dragstart","drop","compositionend","compositionstart","keydown","keypress","keyup","input","textInput","copy","cut","paste","click","change","contextmenu","reset","submit"];function Ta(e){return vv.indexOf(e)>-1}function yv(e,t,a,s,c){return{blockedOn:e,domEventName:t,eventSystemFlags:a,nativeEvent:c,targetContainers:[s]}}function ka(e,t){switch(e){case"focusin":case"focusout":Ya=null;break;case"dragenter":case"dragleave":Ga=null;break;case"mouseover":case"mouseout":Mn=null;break;case"pointerover":case"pointerout":{var a=t.pointerId;Kr.delete(a);break}case"gotpointercapture":case"lostpointercapture":{var s=t.pointerId;aa.delete(s);break}}}function Rc(e,t,a,s,c,p){if(e===null||e.nativeEvent!==p){var v=yv(t,a,s,c,p);if(t!==null){var w=du(t);w!==null&&tl(w)}return v}e.eventSystemFlags|=s;var C=e.targetContainers;return c!==null&&C.indexOf(c)===-1&&C.push(c),e}function xv(e,t,a,s,c){switch(t){case"focusin":{var p=c;return Ya=Rc(Ya,e,t,a,s,p),!0}case"dragenter":{var v=c;return Ga=Rc(Ga,e,t,a,s,v),!0}case"mouseover":{var w=c;return Mn=Rc(Mn,e,t,a,s,w),!0}case"pointerover":{var C=c,R=C.pointerId;return Kr.set(R,Rc(Kr.get(R)||null,e,t,a,s,C)),!0}case"gotpointercapture":{var O=c,U=O.pointerId;return aa.set(U,Rc(aa.get(U)||null,e,t,a,s,O)),!0}}return!1}function wh(e){var t=Ic(e.target);if(t!==null){var a=Gr(t);if(a!==null){var s=a.tag;if(s===le){var c=Ha(a);if(c!==null){e.blockedOn=c,bf(e.priority,function(){bh(a)});return}}else if(s===j){var p=a.stateNode;if(Gl(p)){e.blockedOn=Ko(a);return}}}}e.blockedOn=null}function bv(e){for(var t=tu(),a={blockedOn:null,target:e,priority:t},s=0;s<wo.length&&yh(t,wo[s].priority);s++);wo.splice(s,0,a),s===0&&wh(a)}function Dc(e){if(e.blockedOn!==null)return!1;for(var t=e.targetContainers;t.length>0;){var a=t[0],s=Mc(e.domEventName,e.eventSystemFlags,a,e.nativeEvent);if(s===null){var c=e.nativeEvent,p=new c.constructor(c.type,c);I0(p),c.target.dispatchEvent(p),tc()}else{var v=du(s);return v!==null&&tl(v),e.blockedOn=s,!1}t.shift()}return!0}function wv(e,t,a){Dc(e)&&a.delete(t)}function Sf(){wf=!1,Ya!==null&&Dc(Ya)&&(Ya=null),Ga!==null&&Dc(Ga)&&(Ga=null),Mn!==null&&Dc(Mn)&&(Mn=null),Kr.forEach(wv),aa.forEach(wv)}function Kl(e,t){e.blockedOn===t&&(e.blockedOn=null,wf||(wf=!0,l.unstable_scheduleCallback(l.unstable_NormalPriority,Sf)))}function Ir(e){if(kc.length>0){Kl(kc[0],e);for(var t=1;t<kc.length;t++){var a=kc[t];a.blockedOn===e&&(a.blockedOn=null)}}Ya!==null&&Kl(Ya,e),Ga!==null&&Kl(Ga,e),Mn!==null&&Kl(Mn,e);var s=function(w){return Kl(w,e)};Kr.forEach(s),aa.forEach(s);for(var c=0;c<wo.length;c++){var p=wo[c];p.blockedOn===e&&(p.blockedOn=null)}for(;wo.length>0;){var v=wo[0];if(v.blockedOn!==null)break;wh(v),v.blockedOn===null&&wo.shift()}}var jt=d.ReactCurrentBatchConfig,Zn=!0;function Hn(e){Zn=!!e}function Tr(){return Zn}function Li(e,t,a){var s=ru(t),c;switch(s){case xi:c=nu;break;case ia:c=ar;break;case Cr:default:c=Ql;break}return c.bind(null,t,a,e)}function nu(e,t,a,s){var c=_i(),p=jt.transition;jt.transition=null;try{ir(xi),Ql(e,t,a,s)}finally{ir(c),jt.transition=p}}function ar(e,t,a,s){var c=_i(),p=jt.transition;jt.transition=null;try{ir(ia),Ql(e,t,a,s)}finally{ir(c),jt.transition=p}}function Ql(e,t,a,s){Zn&&ql(e,t,a,s)}function ql(e,t,a,s){var c=Mc(e,t,a,s);if(c===null){db(e,t,s,Xl,a),ka(e,s);return}if(xv(c,e,t,a,s)){s.stopPropagation();return}if(ka(e,s),t&Al&&Ta(e)){for(;c!==null;){var p=du(c);p!==null&&Ne(p);var v=Mc(e,t,a,s);if(v===null&&db(e,t,s,Xl,a),v===c)break;c=v}c!==null&&s.stopPropagation();return}db(e,t,s,null,a)}var Xl=null;function Mc(e,t,a,s){Xl=null;var c=Ad(s),p=Ic(c);if(p!==null){var v=Gr(p);if(v===null)p=null;else{var w=v.tag;if(w===le){var C=Ha(v);if(C!==null)return C;p=null}else if(w===j){var R=v.stateNode;if(Gl(R))return Ko(v);p=null}else v!==p&&(p=null)}}return Xl=p,null}function ru(e){switch(e){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return xi;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return ia;case"message":{var t=Zm();switch(t){case xo:return xi;case cc:return ia;case Fl:case dc:return Cr;case Ws:return yf;default:return Cr}}default:return Cr}}function bi(e,t,a){return e.addEventListener(t,a,!1),a}function Sh(e,t,a){return e.addEventListener(t,a,!0),a}function iu(e,t,a,s){return e.addEventListener(t,a,{capture:!0,passive:s}),a}function So(e,t,a,s){return e.addEventListener(t,a,{passive:s}),a}var nl=null,$c=null,oa=null;function Cf(e){return nl=e,$c=au(),!0}function rl(){nl=null,$c=null,oa=null}function Oc(){if(oa)return oa;var e,t=$c,a=t.length,s,c=au(),p=c.length;for(e=0;e<a&&t[e]===c[e];e++);var v=a-e;for(s=1;s<=v&&t[a-s]===c[p-s];s++);var w=s>1?1-s:void 0;return oa=c.slice(e,w),oa}function au(){return"value"in nl?nl.value:nl.textContent}function ou(e){var t,a=e.keyCode;return"charCode"in e?(t=e.charCode,t===0&&a===13&&(t=13)):t=a,t===10&&(t=13),t>=32||t===13?t:0}function Jl(){return!0}function Ac(){return!1}function mn(e){function t(a,s,c,p,v){this._reactName=a,this._targetInst=c,this.type=s,this.nativeEvent=p,this.target=v,this.currentTarget=null;for(var w in e)if(e.hasOwnProperty(w)){var C=e[w];C?this[w]=C(p):this[w]=p[w]}var R=p.defaultPrevented!=null?p.defaultPrevented:p.returnValue===!1;return R?this.isDefaultPrevented=Jl:this.isDefaultPrevented=Ac,this.isPropagationStopped=Ac,this}return gt(t.prototype,{preventDefault:function(){this.defaultPrevented=!0;var a=this.nativeEvent;a&&(a.preventDefault?a.preventDefault():typeof a.returnValue!="unknown"&&(a.returnValue=!1),this.isDefaultPrevented=Jl)},stopPropagation:function(){var a=this.nativeEvent;a&&(a.stopPropagation?a.stopPropagation():typeof a.cancelBubble!="unknown"&&(a.cancelBubble=!0),this.isPropagationStopped=Jl)},persist:function(){},isPersistent:Jl}),t}var zi={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(e){return e.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Ni=mn(zi),mr=gt({},zi,{view:0,detail:0}),Sv=mn(mr),jc,_c,Lc;function il(e){e!==Lc&&(Lc&&e.type==="mousemove"?(jc=e.screenX-Lc.screenX,_c=e.screenY-Lc.screenY):(jc=0,_c=0),Lc=e)}var zc=gt({},mr,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:kh,button:0,buttons:0,relatedTarget:function(e){return e.relatedTarget===void 0?e.fromElement===e.srcElement?e.toElement:e.fromElement:e.relatedTarget},movementX:function(e){return"movementX"in e?e.movementX:(il(e),jc)},movementY:function(e){return"movementY"in e?e.movementY:_c}}),Ef=mn(zc),Zl=gt({},zc,{dataTransfer:0}),Ch=mn(Zl),es=gt({},mr,{relatedTarget:0}),Tf=mn(es),Cv=gt({},zi,{animationName:0,elapsedTime:0,pseudoElement:0}),Eh=mn(Cv),kf=gt({},zi,{clipboardData:function(e){return"clipboardData"in e?e.clipboardData:window.clipboardData}}),Q0=mn(kf),q0=gt({},zi,{data:0}),Th=mn(q0),Ev=Th,ts={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},X0={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"};function lu(e){if(e.key){var t=ts[e.key]||e.key;if(t!=="Unidentified")return t}if(e.type==="keypress"){var a=ou(e);return a===13?"Enter":String.fromCharCode(a)}return e.type==="keydown"||e.type==="keyup"?X0[e.keyCode]||"Unidentified":""}var Tv={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function Nn(e){var t=this,a=t.nativeEvent;if(a.getModifierState)return a.getModifierState(e);var s=Tv[e];return s?!!a[s]:!1}function kh(e){return Nn}var kv=gt({},mr,{key:lu,code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:kh,charCode:function(e){return e.type==="keypress"?ou(e):0},keyCode:function(e){return e.type==="keydown"||e.type==="keyup"?e.keyCode:0},which:function(e){return e.type==="keypress"?ou(e):e.type==="keydown"||e.type==="keyup"?e.keyCode:0}}),J0=mn(kv),Z0=gt({},zc,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),Rh=mn(Z0),Rv=gt({},mr,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:kh}),eb=mn(Rv),la=gt({},zi,{propertyName:0,elapsedTime:0,pseudoElement:0}),Dh=mn(la),tb=gt({},zc,{deltaX:function(e){return"deltaX"in e?e.deltaX:"wheelDeltaX"in e?-e.wheelDeltaX:0},deltaY:function(e){return"deltaY"in e?e.deltaY:"wheelDeltaY"in e?-e.wheelDeltaY:"wheelDelta"in e?-e.wheelDelta:0},deltaZ:0,deltaMode:0}),al=mn(tb),Rf=[9,13,27,32],ol=229,su=an&&"CompositionEvent"in window,ns=null;an&&"documentMode"in document&&(ns=document.documentMode);var Mh=an&&"TextEvent"in window&&!ns,Dv=an&&(!su||ns&&ns>8&&ns<=11),Df=32,Mv=String.fromCharCode(Df);function $v(){It("onBeforeInput",["compositionend","keypress","textInput","paste"]),It("onCompositionEnd",["compositionend","focusout","keydown","keypress","keyup","mousedown"]),It("onCompositionStart",["compositionstart","focusout","keydown","keypress","keyup","mousedown"]),It("onCompositionUpdate",["compositionupdate","focusout","keydown","keypress","keyup","mousedown"])}var $h=!1;function Mf(e){return(e.ctrlKey||e.altKey||e.metaKey)&&!(e.ctrlKey&&e.altKey)}function $f(e){switch(e){case"compositionstart":return"onCompositionStart";case"compositionend":return"onCompositionEnd";case"compositionupdate":return"onCompositionUpdate"}}function Ov(e,t){return e==="keydown"&&t.keyCode===ol}function Of(e,t){switch(e){case"keyup":return Rf.indexOf(t.keyCode)!==-1;case"keydown":return t.keyCode!==ol;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function Av(e){var t=e.detail;return typeof t=="object"&&"data"in t?t.data:null}function Oh(e){return e.locale==="ko"}var ll=!1;function Af(e,t,a,s,c){var p,v;if(su?p=$f(t):ll?Of(t,s)&&(p="onCompositionEnd"):Ov(t,s)&&(p="onCompositionStart"),!p)return null;Dv&&!Oh(s)&&(!ll&&p==="onCompositionStart"?ll=Cf(c):p==="onCompositionEnd"&&ll&&(v=Oc()));var w=Pv(a,p);if(w.length>0){var C=new Th(p,t,null,s,c);if(e.push({event:C,listeners:w}),v)C.data=v;else{var R=Av(s);R!==null&&(C.data=R)}}}function Ah(e,t){switch(e){case"compositionend":return Av(t);case"keypress":var a=t.which;return a!==Df?null:($h=!0,Mv);case"textInput":var s=t.data;return s===Mv&&$h?null:s;default:return null}}function jf(e,t){if(ll){if(e==="compositionend"||!su&&Of(e,t)){var a=Oc();return rl(),ll=!1,a}return null}switch(e){case"paste":return null;case"keypress":if(!Mf(t)){if(t.char&&t.char.length>1)return t.char;if(t.which)return String.fromCharCode(t.which)}return null;case"compositionend":return Dv&&!Oh(t)?null:t.data;default:return null}}function jv(e,t,a,s,c){var p;if(Mh?p=Ah(t,s):p=jf(t,s),!p)return null;var v=Pv(a,"onBeforeInput");if(v.length>0){var w=new Ev("onBeforeInput","beforeinput",null,s,c);e.push({event:w,listeners:v}),w.data=p}}function nb(e,t,a,s,c,p,v){Af(e,t,a,s,c),jv(e,t,a,s,c)}var _f={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function _v(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t==="input"?!!_f[e.type]:t==="textarea"}/**
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
 */function Nc(e){if(!an)return!1;var t="on"+e,a=t in document;if(!a){var s=document.createElement("div");s.setAttribute(t,"return;"),a=typeof s[t]=="function"}return a}function rb(){It("onChange",["change","click","focusin","focusout","input","keydown","keyup","selectionchange"])}function Pc(e,t,a,s){Hp(s);var c=Pv(t,"onChange");if(c.length>0){var p=new Ni("onChange","change",null,a,s);e.push({event:p,listeners:c})}}var n=null,i=null;function u(e){var t=e.nodeName&&e.nodeName.toLowerCase();return t==="select"||t==="input"&&e.type==="file"}function f(e){var t=[];Pc(t,i,e,Ad(e)),Gm(g,t)}function g(e){iT(e,0)}function x(e){var t=Bf(e);if(Fo(t))return e}function k(e,t){if(e==="change")return t}var _=!1;an&&(_=Nc("input")&&(!document.documentMode||document.documentMode>9));function F(e,t){n=e,i=t,n.attachEvent("onpropertychange",ge)}function J(){n&&(n.detachEvent("onpropertychange",ge),n=null,i=null)}function ge(e){e.propertyName==="value"&&x(i)&&f(e)}function me(e,t,a){e==="focusin"?(J(),F(t,a)):e==="focusout"&&J()}function he(e,t){if(e==="selectionchange"||e==="keyup"||e==="keydown")return x(i)}function _e(e){var t=e.nodeName;return t&&t.toLowerCase()==="input"&&(e.type==="checkbox"||e.type==="radio")}function Fe(e,t){if(e==="click")return x(t)}function Ie(e,t){if(e==="input"||e==="change")return x(t)}function Vn(e){var t=e._wrapperState;!t||!t.controlled||e.type!=="number"||Pe(e,"number",e.value)}function W(e,t,a,s,c,p,v){var w=a?Bf(a):window,C,R;if(u(w)?C=k:_v(w)?_?C=Ie:(C=he,R=me):_e(w)&&(C=Fe),C){var O=C(t,a);if(O){Pc(e,O,s,c);return}}R&&R(t,w,a),t==="focusout"&&Vn(w)}function I(){pn("onMouseEnter",["mouseout","mouseover"]),pn("onMouseLeave",["mouseout","mouseover"]),pn("onPointerEnter",["pointerout","pointerover"]),pn("onPointerLeave",["pointerout","pointerover"])}function Q(e,t,a,s,c,p,v){var w=t==="mouseover"||t==="pointerover",C=t==="mouseout"||t==="pointerout";if(w&&!U0(s)){var R=s.relatedTarget||s.fromElement;if(R&&(Ic(R)||Yh(R)))return}if(!(!C&&!w)){var O;if(c.window===c)O=c;else{var U=c.ownerDocument;U?O=U.defaultView||U.parentWindow:O=window}var B,X;if(C){var Z=s.relatedTarget||s.toElement;if(B=a,X=Z?Ic(Z):null,X!==null){var te=Gr(X);(X!==te||X.tag!==L&&X.tag!==K)&&(X=null)}}else B=null,X=a;if(B!==X){var De=Ef,Je="onMouseLeave",Ye="onMouseEnter",Bt="mouse";(t==="pointerout"||t==="pointerover")&&(De=Rh,Je="onPointerLeave",Ye="onPointerEnter",Bt="pointer");var _t=B==null?O:Bf(B),Y=X==null?O:Bf(X),ne=new De(Je,Bt+"leave",B,s,c);ne.target=_t,ne.relatedTarget=Y;var G=null,ve=Ic(c);if(ve===a){var ze=new De(Ye,Bt+"enter",X,s,c);ze.target=Y,ze.relatedTarget=_t,G=ze}Hz(e,ne,G,B,X)}}}function ye(e,t){return e===t&&(e!==0||1/e===1/t)||e!==e&&t!==t}var $e=typeof Object.is=="function"?Object.is:ye;function qe(e,t){if($e(e,t))return!0;if(typeof e!="object"||e===null||typeof t!="object"||t===null)return!1;var a=Object.keys(e),s=Object.keys(t);if(a.length!==s.length)return!1;for(var c=0;c<a.length;c++){var p=a[c];if(!Pn.call(t,p)||!$e(e[p],t[p]))return!1}return!0}function Ze(e){for(;e&&e.firstChild;)e=e.firstChild;return e}function st(e){for(;e;){if(e.nextSibling)return e.nextSibling;e=e.parentNode}}function or(e,t){for(var a=Ze(e),s=0,c=0;a;){if(a.nodeType===go){if(c=s+a.textContent.length,s<=t&&c>=t)return{node:a,offset:t-s};s=c}a=Ze(st(a))}}function Vt(e){var t=e.ownerDocument,a=t&&t.defaultView||window,s=a.getSelection&&a.getSelection();if(!s||s.rangeCount===0)return null;var c=s.anchorNode,p=s.anchorOffset,v=s.focusNode,w=s.focusOffset;try{c.nodeType,v.nodeType}catch{return null}return sl(e,c,p,v,w)}function sl(e,t,a,s,c){var p=0,v=-1,w=-1,C=0,R=0,O=e,U=null;e:for(;;){for(var B=null;O===t&&(a===0||O.nodeType===go)&&(v=p+a),O===s&&(c===0||O.nodeType===go)&&(w=p+c),O.nodeType===go&&(p+=O.nodeValue.length),(B=O.firstChild)!==null;)U=O,O=B;for(;;){if(O===e)break e;if(U===t&&++C===a&&(v=p),U===s&&++R===c&&(w=p),(B=O.nextSibling)!==null)break;O=U,U=O.parentNode}O=B}return v===-1||w===-1?null:{start:v,end:w}}function ib(e,t){var a=e.ownerDocument||document,s=a&&a.defaultView||window;if(s.getSelection){var c=s.getSelection(),p=e.textContent.length,v=Math.min(t.start,p),w=t.end===void 0?v:Math.min(t.end,p);if(!c.extend&&v>w){var C=w;w=v,v=C}var R=or(e,v),O=or(e,w);if(R&&O){if(c.rangeCount===1&&c.anchorNode===R.node&&c.anchorOffset===R.offset&&c.focusNode===O.node&&c.focusOffset===O.offset)return;var U=a.createRange();U.setStart(R.node,R.offset),c.removeAllRanges(),v>w?(c.addRange(U),c.extend(O.node,O.offset)):(U.setEnd(O.node,O.offset),c.addRange(U))}}}function YE(e){return e&&e.nodeType===go}function GE(e,t){return!e||!t?!1:e===t?!0:YE(e)?!1:YE(t)?GE(e,t.parentNode):"contains"in e?e.contains(t):e.compareDocumentPosition?!!(e.compareDocumentPosition(t)&16):!1}function Tz(e){return e&&e.ownerDocument&&GE(e.ownerDocument.documentElement,e)}function kz(e){try{return typeof e.contentWindow.location.href=="string"}catch{return!1}}function KE(){for(var e=window,t=fo();t instanceof e.HTMLIFrameElement;){if(kz(t))e=t.contentWindow;else return t;t=fo(e.document)}return t}function ab(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t&&(t==="input"&&(e.type==="text"||e.type==="search"||e.type==="tel"||e.type==="url"||e.type==="password")||t==="textarea"||e.contentEditable==="true")}function Rz(){var e=KE();return{focusedElem:e,selectionRange:ab(e)?Mz(e):null}}function Dz(e){var t=KE(),a=e.focusedElem,s=e.selectionRange;if(t!==a&&Tz(a)){s!==null&&ab(a)&&$z(a,s);for(var c=[],p=a;p=p.parentNode;)p.nodeType===di&&c.push({element:p,left:p.scrollLeft,top:p.scrollTop});typeof a.focus=="function"&&a.focus();for(var v=0;v<c.length;v++){var w=c[v];w.element.scrollLeft=w.left,w.element.scrollTop=w.top}}}function Mz(e){var t;return"selectionStart"in e?t={start:e.selectionStart,end:e.selectionEnd}:t=Vt(e),t||{start:0,end:0}}function $z(e,t){var a=t.start,s=t.end;s===void 0&&(s=a),"selectionStart"in e?(e.selectionStart=a,e.selectionEnd=Math.min(s,e.value.length)):ib(e,t)}var Oz=an&&"documentMode"in document&&document.documentMode<=11;function Az(){It("onSelect",["focusout","contextmenu","dragend","focusin","keydown","keyup","mousedown","mouseup","selectionchange"])}var Lf=null,ob=null,jh=null,lb=!1;function jz(e){if("selectionStart"in e&&ab(e))return{start:e.selectionStart,end:e.selectionEnd};var t=e.ownerDocument&&e.ownerDocument.defaultView||window,a=t.getSelection();return{anchorNode:a.anchorNode,anchorOffset:a.anchorOffset,focusNode:a.focusNode,focusOffset:a.focusOffset}}function _z(e){return e.window===e?e.document:e.nodeType===mo?e:e.ownerDocument}function QE(e,t,a){var s=_z(a);if(!(lb||Lf==null||Lf!==fo(s))){var c=jz(Lf);if(!jh||!qe(jh,c)){jh=c;var p=Pv(ob,"onSelect");if(p.length>0){var v=new Ni("onSelect","select",null,t,a);e.push({event:v,listeners:p}),v.target=Lf}}}}function Lz(e,t,a,s,c,p,v){var w=a?Bf(a):window;switch(t){case"focusin":(_v(w)||w.contentEditable==="true")&&(Lf=w,ob=a,jh=null);break;case"focusout":Lf=null,ob=null,jh=null;break;case"mousedown":lb=!0;break;case"contextmenu":case"mouseup":case"dragend":lb=!1,QE(e,s,c);break;case"selectionchange":if(Oz)break;case"keydown":case"keyup":QE(e,s,c)}}function Lv(e,t){var a={};return a[e.toLowerCase()]=t.toLowerCase(),a["Webkit"+e]="webkit"+t,a["Moz"+e]="moz"+t,a}var zf={animationend:Lv("Animation","AnimationEnd"),animationiteration:Lv("Animation","AnimationIteration"),animationstart:Lv("Animation","AnimationStart"),transitionend:Lv("Transition","TransitionEnd")},sb={},qE={};an&&(qE=document.createElement("div").style,"AnimationEvent"in window||(delete zf.animationend.animation,delete zf.animationiteration.animation,delete zf.animationstart.animation),"TransitionEvent"in window||delete zf.transitionend.transition);function zv(e){if(sb[e])return sb[e];if(!zf[e])return e;var t=zf[e];for(var a in t)if(t.hasOwnProperty(a)&&a in qE)return sb[e]=t[a];return e}var XE=zv("animationend"),JE=zv("animationiteration"),ZE=zv("animationstart"),eT=zv("transitionend"),tT=new Map,nT=["abort","auxClick","cancel","canPlay","canPlayThrough","click","close","contextMenu","copy","cut","drag","dragEnd","dragEnter","dragExit","dragLeave","dragOver","dragStart","drop","durationChange","emptied","encrypted","ended","error","gotPointerCapture","input","invalid","keyDown","keyPress","keyUp","load","loadedData","loadedMetadata","loadStart","lostPointerCapture","mouseDown","mouseMove","mouseOut","mouseOver","mouseUp","paste","pause","play","playing","pointerCancel","pointerDown","pointerMove","pointerOut","pointerOver","pointerUp","progress","rateChange","reset","resize","seeked","seeking","stalled","submit","suspend","timeUpdate","touchCancel","touchEnd","touchStart","volumeChange","scroll","toggle","touchMove","waiting","wheel"];function uu(e,t){tT.set(e,t),It(t,[e])}function zz(){for(var e=0;e<nT.length;e++){var t=nT[e],a=t.toLowerCase(),s=t[0].toUpperCase()+t.slice(1);uu(a,"on"+s)}uu(XE,"onAnimationEnd"),uu(JE,"onAnimationIteration"),uu(ZE,"onAnimationStart"),uu("dblclick","onDoubleClick"),uu("focusin","onFocus"),uu("focusout","onBlur"),uu(eT,"onTransitionEnd")}function Nz(e,t,a,s,c,p,v){var w=tT.get(t);if(w!==void 0){var C=Ni,R=t;switch(t){case"keypress":if(ou(s)===0)return;case"keydown":case"keyup":C=J0;break;case"focusin":R="focus",C=Tf;break;case"focusout":R="blur",C=Tf;break;case"beforeblur":case"afterblur":C=Tf;break;case"click":if(s.button===2)return;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":C=Ef;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":C=Ch;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":C=eb;break;case XE:case JE:case ZE:C=Eh;break;case eT:C=Dh;break;case"scroll":C=Sv;break;case"wheel":C=al;break;case"copy":case"cut":case"paste":C=Q0;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":C=Rh;break}var O=(p&Al)!==0;{var U=!O&&t==="scroll",B=Iz(a,w,s.type,O,U);if(B.length>0){var X=new C(w,R,null,s,c);e.push({event:X,listeners:B})}}}}zz(),I(),rb(),Az(),$v();function Pz(e,t,a,s,c,p,v){Nz(e,t,a,s,c,p);var w=(p&Up)===0;w&&(Q(e,t,a,s,c),W(e,t,a,s,c),Lz(e,t,a,s,c),nb(e,t,a,s,c))}var _h=["abort","canplay","canplaythrough","durationchange","emptied","encrypted","ended","error","loadeddata","loadedmetadata","loadstart","pause","play","playing","progress","ratechange","resize","seeked","seeking","stalled","suspend","timeupdate","volumechange","waiting"],ub=new Set(["cancel","close","invalid","load","scroll","toggle"].concat(_h));function rT(e,t,a){var s=e.type||"unknown-event";e.currentTarget=a,oc(s,t,void 0,e),e.currentTarget=null}function Fz(e,t,a){var s;if(a)for(var c=t.length-1;c>=0;c--){var p=t[c],v=p.instance,w=p.currentTarget,C=p.listener;if(v!==s&&e.isPropagationStopped())return;rT(e,C,w),s=v}else for(var R=0;R<t.length;R++){var O=t[R],U=O.instance,B=O.currentTarget,X=O.listener;if(U!==s&&e.isPropagationStopped())return;rT(e,X,B),s=U}}function iT(e,t){for(var a=(t&Al)!==0,s=0;s<e.length;s++){var c=e[s],p=c.event,v=c.listeners;Fz(p,v,a)}vo()}function Bz(e,t,a,s,c){var p=Ad(a),v=[];Pz(v,e,s,a,p,t),iT(v,t)}function jn(e,t){ub.has(e)||y('Did not expect a listenToNonDelegatedEvent() call for "%s". This is a bug in React. Please file an issue.',e);var a=!1,s=mP(t),c=Vz(e);s.has(c)||(aT(t,e,Ia,a),s.add(c))}function cb(e,t,a){ub.has(e)&&!t&&y('Did not expect a listenToNativeEvent() call for "%s" in the bubble phase. This is a bug in React. Please file an issue.',e);var s=0;t&&(s|=Al),aT(a,e,s,t)}var Nv="_reactListening"+Math.random().toString(36).slice(2);function Lh(e){if(!e[Nv]){e[Nv]=!0,$t.forEach(function(a){a!=="selectionchange"&&(ub.has(a)||cb(a,!1,e),cb(a,!0,e))});var t=e.nodeType===mo?e:e.ownerDocument;t!==null&&(t[Nv]||(t[Nv]=!0,cb("selectionchange",!1,t)))}}function aT(e,t,a,s,c){var p=Li(e,t,a),v=void 0;ac&&(t==="touchstart"||t==="touchmove"||t==="wheel")&&(v=!0),e=e,s?v!==void 0?iu(e,t,p,v):Sh(e,t,p):v!==void 0?So(e,t,p,v):bi(e,t,p)}function oT(e,t){return e===t||e.nodeType===Qn&&e.parentNode===t}function db(e,t,a,s,c){var p=s;if(!(t&Ip)&&!(t&Ia)){var v=c;if(s!==null){var w=s;e:for(;;){if(w===null)return;var C=w.tag;if(C===j||C===H){var R=w.stateNode.containerInfo;if(oT(R,v))break;if(C===H)for(var O=w.return;O!==null;){var U=O.tag;if(U===j||U===H){var B=O.stateNode.containerInfo;if(oT(B,v))return}O=O.return}for(;R!==null;){var X=Ic(R);if(X===null)return;var Z=X.tag;if(Z===L||Z===K){w=p=X;continue e}R=R.parentNode}}w=w.return}}}Gm(function(){return Bz(e,t,a,p)})}function zh(e,t,a){return{instance:e,listener:t,currentTarget:a}}function Iz(e,t,a,s,c,p){for(var v=t!==null?t+"Capture":null,w=s?v:t,C=[],R=e,O=null;R!==null;){var U=R,B=U.stateNode,X=U.tag;if(X===L&&B!==null&&(O=B,w!==null)){var Z=_l(R,w);Z!=null&&C.push(zh(R,Z,O))}if(c)break;R=R.return}return C}function Pv(e,t){for(var a=t+"Capture",s=[],c=e;c!==null;){var p=c,v=p.stateNode,w=p.tag;if(w===L&&v!==null){var C=v,R=_l(c,a);R!=null&&s.unshift(zh(c,R,C));var O=_l(c,t);O!=null&&s.push(zh(c,O,C))}c=c.return}return s}function Nf(e){if(e===null)return null;do e=e.return;while(e&&e.tag!==L);return e||null}function Uz(e,t){for(var a=e,s=t,c=0,p=a;p;p=Nf(p))c++;for(var v=0,w=s;w;w=Nf(w))v++;for(;c-v>0;)a=Nf(a),c--;for(;v-c>0;)s=Nf(s),v--;for(var C=c;C--;){if(a===s||s!==null&&a===s.alternate)return a;a=Nf(a),s=Nf(s)}return null}function lT(e,t,a,s,c){for(var p=t._reactName,v=[],w=a;w!==null&&w!==s;){var C=w,R=C.alternate,O=C.stateNode,U=C.tag;if(R!==null&&R===s)break;if(U===L&&O!==null){var B=O;if(c){var X=_l(w,p);X!=null&&v.unshift(zh(w,X,B))}else if(!c){var Z=_l(w,p);Z!=null&&v.push(zh(w,Z,B))}}w=w.return}v.length!==0&&e.push({event:t,listeners:v})}function Hz(e,t,a,s,c){var p=s&&c?Uz(s,c):null;s!==null&&lT(e,t,s,p,!1),c!==null&&a!==null&&lT(e,a,c,p,!0)}function Vz(e,t){return e+"__bubble"}var sa=!1,Nh="dangerouslySetInnerHTML",Fv="suppressContentEditableWarning",cu="suppressHydrationWarning",sT="autoFocus",Fc="children",Bc="style",Bv="__html",fb,Iv,Ph,uT,Uv,cT,dT;fb={dialog:!0,webview:!0},Iv=function(e,t){Im(e,t),Ps(e,t),Ym(e,t,{registrationNameDependencies:tt,possibleRegistrationNames:vt})},cT=an&&!document.documentMode,Ph=function(e,t,a){if(!sa){var s=Hv(a),c=Hv(t);c!==s&&(sa=!0,y("Prop `%s` did not match. Server: %s Client: %s",e,JSON.stringify(c),JSON.stringify(s)))}},uT=function(e){if(!sa){sa=!0;var t=[];e.forEach(function(a){t.push(a)}),y("Extra attributes from the server: %s",t)}},Uv=function(e,t){t===!1?y("Expected `%s` listener to be a function, instead got `false`.\n\nIf you used to conditionally omit it with %s={condition && value}, pass %s={condition ? value : undefined} instead.",e,e,e):y("Expected `%s` listener to be a function, instead got a value of `%s` type.",e,typeof t)},dT=function(e,t){var a=e.namespaceURI===xa?e.ownerDocument.createElement(e.tagName):e.ownerDocument.createElementNS(e.namespaceURI,e.tagName);return a.innerHTML=t,a.innerHTML};var Wz=/\r\n?/g,Yz=/\u0000|\uFFFD/g;function Hv(e){nr(e);var t=typeof e=="string"?e:""+e;return t.replace(Wz,`
`).replace(Yz,"")}function Vv(e,t,a,s){var c=Hv(t),p=Hv(e);if(p!==c&&(s&&(sa||(sa=!0,y('Text content did not match. Server: "%s" Client: "%s"',p,c))),a&&se))throw new Error("Text content does not match server-rendered HTML.")}function fT(e){return e.nodeType===mo?e:e.ownerDocument}function Gz(){}function Wv(e){e.onclick=Gz}function Kz(e,t,a,s,c){for(var p in s)if(s.hasOwnProperty(p)){var v=s[p];if(p===Bc)v&&Object.freeze(v),zm(t,v);else if(p===Nh){var w=v?v[Bv]:void 0;w!=null&&Em(t,w)}else if(p===Fc)if(typeof v=="string"){var C=e!=="textarea"||v!=="";C&&Io(t,v)}else typeof v=="number"&&Io(t,""+v);else p===Fv||p===cu||p===sT||(tt.hasOwnProperty(p)?v!=null&&(typeof v!="function"&&Uv(p,v),p==="onScroll"&&jn("scroll",t)):v!=null&&$i(t,p,v,c))}}function Qz(e,t,a,s){for(var c=0;c<t.length;c+=2){var p=t[c],v=t[c+1];p===Bc?zm(e,v):p===Nh?Em(e,v):p===Fc?Io(e,v):$i(e,p,v,s)}}function qz(e,t,a,s){var c,p=fT(a),v,w=s;if(w===xa&&(w=Lp(e)),w===xa){if(c=Uo(e,t),!c&&e!==e.toLowerCase()&&y("<%s /> is using incorrect casing. Use PascalCase for React components, or lowercase for HTML elements.",e),e==="script"){var C=p.createElement("div");C.innerHTML="<script><\/script>";var R=C.firstChild;v=C.removeChild(R)}else if(typeof t.is=="string")v=p.createElement(e,{is:t.is});else if(v=p.createElement(e),e==="select"){var O=v;t.multiple?O.multiple=!0:t.size&&(O.size=t.size)}}else v=p.createElementNS(w,e);return w===xa&&!c&&Object.prototype.toString.call(v)==="[object HTMLUnknownElement]"&&!Pn.call(fb,e)&&(fb[e]=!0,y("The tag <%s> is unrecognized in this browser. If you meant to render a React component, start its name with an uppercase letter.",e)),v}function Xz(e,t){return fT(t).createTextNode(e)}function Jz(e,t,a,s){var c=Uo(t,a);Iv(t,a);var p;switch(t){case"dialog":jn("cancel",e),jn("close",e),p=a;break;case"iframe":case"object":case"embed":jn("load",e),p=a;break;case"video":case"audio":for(var v=0;v<_h.length;v++)jn(_h[v],e);p=a;break;case"source":jn("error",e),p=a;break;case"img":case"image":case"link":jn("error",e),jn("load",e),p=a;break;case"details":jn("toggle",e),p=a;break;case"input":Os(e,a),p=ea(e,a),jn("invalid",e);break;case"option":qt(e,a),p=a;break;case"select":Gu(e,a),p=Ol(e,a),jn("invalid",e);break;case"textarea":wm(e,a),p=kd(e,a),jn("invalid",e);break;default:p=a}switch($d(t,p),Kz(t,e,s,p,c),t){case"input":Zi(e),q(e,a,!1);break;case"textarea":Zi(e),Cm(e);break;case"option":sn(e,a);break;case"select":Ap(e,a);break;default:typeof p.onClick=="function"&&Wv(e);break}}function Zz(e,t,a,s,c){Iv(t,s);var p=null,v,w;switch(t){case"input":v=ea(e,a),w=ea(e,s),p=[];break;case"select":v=Ol(e,a),w=Ol(e,s),p=[];break;case"textarea":v=kd(e,a),w=kd(e,s),p=[];break;default:v=a,w=s,typeof v.onClick!="function"&&typeof w.onClick=="function"&&Wv(e);break}$d(t,w);var C,R,O=null;for(C in v)if(!(w.hasOwnProperty(C)||!v.hasOwnProperty(C)||v[C]==null))if(C===Bc){var U=v[C];for(R in U)U.hasOwnProperty(R)&&(O||(O={}),O[R]="")}else C===Nh||C===Fc||C===Fv||C===cu||C===sT||(tt.hasOwnProperty(C)?p||(p=[]):(p=p||[]).push(C,null));for(C in w){var B=w[C],X=v!=null?v[C]:void 0;if(!(!w.hasOwnProperty(C)||B===X||B==null&&X==null))if(C===Bc)if(B&&Object.freeze(B),X){for(R in X)X.hasOwnProperty(R)&&(!B||!B.hasOwnProperty(R))&&(O||(O={}),O[R]="");for(R in B)B.hasOwnProperty(R)&&X[R]!==B[R]&&(O||(O={}),O[R]=B[R])}else O||(p||(p=[]),p.push(C,O)),O=B;else if(C===Nh){var Z=B?B[Bv]:void 0,te=X?X[Bv]:void 0;Z!=null&&te!==Z&&(p=p||[]).push(C,Z)}else C===Fc?(typeof B=="string"||typeof B=="number")&&(p=p||[]).push(C,""+B):C===Fv||C===cu||(tt.hasOwnProperty(C)?(B!=null&&(typeof B!="function"&&Uv(C,B),C==="onScroll"&&jn("scroll",e)),!p&&X!==B&&(p=[])):(p=p||[]).push(C,B))}return O&&(ba(O,w[Bc]),(p=p||[]).push(Bc,O)),p}function eN(e,t,a,s,c){a==="input"&&c.type==="radio"&&c.name!=null&&T(e,c);var p=Uo(a,s),v=Uo(a,c);switch(Qz(e,t,p,v),a){case"input":z(e,c);break;case"textarea":Sm(e,c);break;case"select":Td(e,c);break}}function tN(e){{var t=e.toLowerCase();return Ls.hasOwnProperty(t)&&Ls[t]||null}}function nN(e,t,a,s,c,p,v){var w,C;switch(w=Uo(t,a),Iv(t,a),t){case"dialog":jn("cancel",e),jn("close",e);break;case"iframe":case"object":case"embed":jn("load",e);break;case"video":case"audio":for(var R=0;R<_h.length;R++)jn(_h[R],e);break;case"source":jn("error",e);break;case"img":case"image":case"link":jn("error",e),jn("load",e);break;case"details":jn("toggle",e);break;case"input":Os(e,a),jn("invalid",e);break;case"option":qt(e,a);break;case"select":Gu(e,a),jn("invalid",e);break;case"textarea":wm(e,a),jn("invalid",e);break}$d(t,a);{C=new Set;for(var O=e.attributes,U=0;U<O.length;U++){var B=O[U].name.toLowerCase();switch(B){case"value":break;case"checked":break;case"selected":break;default:C.add(O[U].name)}}}var X=null;for(var Z in a)if(a.hasOwnProperty(Z)){var te=a[Z];if(Z===Fc)typeof te=="string"?e.textContent!==te&&(a[cu]!==!0&&Vv(e.textContent,te,p,v),X=[Fc,te]):typeof te=="number"&&e.textContent!==""+te&&(a[cu]!==!0&&Vv(e.textContent,te,p,v),X=[Fc,""+te]);else if(tt.hasOwnProperty(Z))te!=null&&(typeof te!="function"&&Uv(Z,te),Z==="onScroll"&&jn("scroll",e));else if(v&&typeof w=="boolean"){var De=void 0,Je=vn(Z);if(a[cu]!==!0){if(!(Z===Fv||Z===cu||Z==="value"||Z==="checked"||Z==="selected")){if(Z===Nh){var Ye=e.innerHTML,Bt=te?te[Bv]:void 0;if(Bt!=null){var _t=dT(e,Bt);_t!==Ye&&Ph(Z,Ye,_t)}}else if(Z===Bc){if(C.delete(Z),cT){var Y=F0(te);De=e.getAttribute("style"),Y!==De&&Ph(Z,De,Y)}}else if(w&&!re)C.delete(Z.toLowerCase()),De=ja(e,Z,te),te!==De&&Ph(Z,De,te);else if(!wn(Z,Je,w)&&!dr(Z,te,Je,w)){var ne=!1;if(Je!==null)C.delete(Je.attributeName),De=El(e,Z,te,Je);else{var G=s;if(G===xa&&(G=Lp(t)),G===xa)C.delete(Z.toLowerCase());else{var ve=tN(Z);ve!==null&&ve!==Z&&(ne=!0,C.delete(ve)),C.delete(Z)}De=ja(e,Z,te)}var ze=re;!ze&&te!==De&&!ne&&Ph(Z,De,te)}}}}}switch(v&&C.size>0&&a[cu]!==!0&&uT(C),t){case"input":Zi(e),q(e,a,!0);break;case"textarea":Zi(e),Cm(e);break;case"select":case"option":break;default:typeof a.onClick=="function"&&Wv(e);break}return X}function rN(e,t,a){var s=e.nodeValue!==t;return s}function pb(e,t){{if(sa)return;sa=!0,y("Did not expect server HTML to contain a <%s> in <%s>.",t.nodeName.toLowerCase(),e.nodeName.toLowerCase())}}function hb(e,t){{if(sa)return;sa=!0,y('Did not expect server HTML to contain the text node "%s" in <%s>.',t.nodeValue,e.nodeName.toLowerCase())}}function gb(e,t,a){{if(sa)return;sa=!0,y("Expected server HTML to contain a matching <%s> in <%s>.",t,e.nodeName.toLowerCase())}}function mb(e,t){{if(t===""||sa)return;sa=!0,y('Expected server HTML to contain a matching text node for "%s" in <%s>.',t,e.nodeName.toLowerCase())}}function iN(e,t,a){switch(t){case"input":ee(e,a);return;case"textarea":A0(e,a);return;case"select":jp(e,a);return}}var Fh=function(){},Bh=function(){};{var aN=["address","applet","area","article","aside","base","basefont","bgsound","blockquote","body","br","button","caption","center","col","colgroup","dd","details","dir","div","dl","dt","embed","fieldset","figcaption","figure","footer","form","frame","frameset","h1","h2","h3","h4","h5","h6","head","header","hgroup","hr","html","iframe","img","input","isindex","li","link","listing","main","marquee","menu","menuitem","meta","nav","noembed","noframes","noscript","object","ol","p","param","plaintext","pre","script","section","select","source","style","summary","table","tbody","td","template","textarea","tfoot","th","thead","title","tr","track","ul","wbr","xmp"],pT=["applet","caption","html","table","td","th","marquee","object","template","foreignObject","desc","title"],oN=pT.concat(["button"]),lN=["dd","dt","li","option","optgroup","p","rp","rt"],hT={current:null,formTag:null,aTagInScope:null,buttonTagInScope:null,nobrTagInScope:null,pTagInButtonScope:null,listItemTagAutoclosing:null,dlItemTagAutoclosing:null};Bh=function(e,t){var a=gt({},e||hT),s={tag:t};return pT.indexOf(t)!==-1&&(a.aTagInScope=null,a.buttonTagInScope=null,a.nobrTagInScope=null),oN.indexOf(t)!==-1&&(a.pTagInButtonScope=null),aN.indexOf(t)!==-1&&t!=="address"&&t!=="div"&&t!=="p"&&(a.listItemTagAutoclosing=null,a.dlItemTagAutoclosing=null),a.current=s,t==="form"&&(a.formTag=s),t==="a"&&(a.aTagInScope=s),t==="button"&&(a.buttonTagInScope=s),t==="nobr"&&(a.nobrTagInScope=s),t==="p"&&(a.pTagInButtonScope=s),t==="li"&&(a.listItemTagAutoclosing=s),(t==="dd"||t==="dt")&&(a.dlItemTagAutoclosing=s),a};var sN=function(e,t){switch(t){case"select":return e==="option"||e==="optgroup"||e==="#text";case"optgroup":return e==="option"||e==="#text";case"option":return e==="#text";case"tr":return e==="th"||e==="td"||e==="style"||e==="script"||e==="template";case"tbody":case"thead":case"tfoot":return e==="tr"||e==="style"||e==="script"||e==="template";case"colgroup":return e==="col"||e==="template";case"table":return e==="caption"||e==="colgroup"||e==="tbody"||e==="tfoot"||e==="thead"||e==="style"||e==="script"||e==="template";case"head":return e==="base"||e==="basefont"||e==="bgsound"||e==="link"||e==="meta"||e==="title"||e==="noscript"||e==="noframes"||e==="style"||e==="script"||e==="template";case"html":return e==="head"||e==="body"||e==="frameset";case"frameset":return e==="frame";case"#document":return e==="html"}switch(e){case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t!=="h1"&&t!=="h2"&&t!=="h3"&&t!=="h4"&&t!=="h5"&&t!=="h6";case"rp":case"rt":return lN.indexOf(t)===-1;case"body":case"caption":case"col":case"colgroup":case"frameset":case"frame":case"head":case"html":case"tbody":case"td":case"tfoot":case"th":case"thead":case"tr":return t==null}return!0},uN=function(e,t){switch(e){case"address":case"article":case"aside":case"blockquote":case"center":case"details":case"dialog":case"dir":case"div":case"dl":case"fieldset":case"figcaption":case"figure":case"footer":case"header":case"hgroup":case"main":case"menu":case"nav":case"ol":case"p":case"section":case"summary":case"ul":case"pre":case"listing":case"table":case"hr":case"xmp":case"h1":case"h2":case"h3":case"h4":case"h5":case"h6":return t.pTagInButtonScope;case"form":return t.formTag||t.pTagInButtonScope;case"li":return t.listItemTagAutoclosing;case"dd":case"dt":return t.dlItemTagAutoclosing;case"button":return t.buttonTagInScope;case"a":return t.aTagInScope;case"nobr":return t.nobrTagInScope}return null},gT={};Fh=function(e,t,a){a=a||hT;var s=a.current,c=s&&s.tag;t!=null&&(e!=null&&y("validateDOMNesting: when childText is passed, childTag should be null"),e="#text");var p=sN(e,c)?null:s,v=p?null:uN(e,a),w=p||v;if(w){var C=w.tag,R=!!p+"|"+e+"|"+C;if(!gT[R]){gT[R]=!0;var O=e,U="";if(e==="#text"?/\S/.test(t)?O="Text nodes":(O="Whitespace text nodes",U=" Make sure you don't have any extra whitespace between tags on each line of your source code."):O="<"+e+">",p){var B="";C==="table"&&e==="tr"&&(B+=" Add a <tbody>, <thead> or <tfoot> to your code to match the DOM tree generated by the browser."),y("validateDOMNesting(...): %s cannot appear as a child of <%s>.%s%s",O,C,U,B)}else y("validateDOMNesting(...): %s cannot appear as a descendant of <%s>.",O,C)}}}}var Yv="suppressHydrationWarning",Gv="$",Kv="/$",Ih="$?",Uh="$!",cN="style",vb=null,yb=null;function dN(e){var t,a,s=e.nodeType;switch(s){case mo:case Ku:{t=s===mo?"#document":"#fragment";var c=e.documentElement;a=c?c.namespaceURI:Rd(null,"");break}default:{var p=s===Qn?e.parentNode:e,v=p.namespaceURI||null;t=p.tagName,a=Rd(v,t);break}}{var w=t.toLowerCase(),C=Bh(null,w);return{namespace:a,ancestorInfo:C}}}function fN(e,t,a){{var s=e,c=Rd(s.namespace,t),p=Bh(s.ancestorInfo,t);return{namespace:c,ancestorInfo:p}}}function pF(e){return e}function pN(e){vb=Tr(),yb=Rz();var t=null;return Hn(!1),t}function hN(e){Dz(yb),Hn(vb),vb=null,yb=null}function gN(e,t,a,s,c){var p;{var v=s;if(Fh(e,null,v.ancestorInfo),typeof t.children=="string"||typeof t.children=="number"){var w=""+t.children,C=Bh(v.ancestorInfo,e);Fh(null,w,C)}p=v.namespace}var R=qz(e,t,a,p);return Wh(c,R),kb(R,t),R}function mN(e,t){e.appendChild(t)}function vN(e,t,a,s,c){switch(Jz(e,t,a,s),t){case"button":case"input":case"select":case"textarea":return!!a.autoFocus;case"img":return!0;default:return!1}}function yN(e,t,a,s,c,p){{var v=p;if(typeof s.children!=typeof a.children&&(typeof s.children=="string"||typeof s.children=="number")){var w=""+s.children,C=Bh(v.ancestorInfo,t);Fh(null,w,C)}}return Zz(e,t,a,s)}function xb(e,t){return e==="textarea"||e==="noscript"||typeof t.children=="string"||typeof t.children=="number"||typeof t.dangerouslySetInnerHTML=="object"&&t.dangerouslySetInnerHTML!==null&&t.dangerouslySetInnerHTML.__html!=null}function xN(e,t,a,s){{var c=a;Fh(null,e,c.ancestorInfo)}var p=Xz(e,t);return Wh(s,p),p}function bN(){var e=window.event;return e===void 0?Cr:ru(e.type)}var bb=typeof setTimeout=="function"?setTimeout:void 0,wN=typeof clearTimeout=="function"?clearTimeout:void 0,wb=-1,mT=typeof Promise=="function"?Promise:void 0,SN=typeof queueMicrotask=="function"?queueMicrotask:typeof mT<"u"?function(e){return mT.resolve(null).then(e).catch(CN)}:bb;function CN(e){setTimeout(function(){throw e})}function EN(e,t,a,s){switch(t){case"button":case"input":case"select":case"textarea":a.autoFocus&&e.focus();return;case"img":{a.src&&(e.src=a.src);return}}}function TN(e,t,a,s,c,p){eN(e,t,a,s,c),kb(e,c)}function vT(e){Io(e,"")}function kN(e,t,a){e.nodeValue=a}function RN(e,t){e.appendChild(t)}function DN(e,t){var a;e.nodeType===Qn?(a=e.parentNode,a.insertBefore(t,e)):(a=e,a.appendChild(t));var s=e._reactRootContainer;s==null&&a.onclick===null&&Wv(a)}function MN(e,t,a){e.insertBefore(t,a)}function $N(e,t,a){e.nodeType===Qn?e.parentNode.insertBefore(t,a):e.insertBefore(t,a)}function ON(e,t){e.removeChild(t)}function AN(e,t){e.nodeType===Qn?e.parentNode.removeChild(t):e.removeChild(t)}function Sb(e,t){var a=t,s=0;do{var c=a.nextSibling;if(e.removeChild(a),c&&c.nodeType===Qn){var p=c.data;if(p===Kv)if(s===0){e.removeChild(c),Ir(t);return}else s--;else(p===Gv||p===Ih||p===Uh)&&s++}a=c}while(a);Ir(t)}function jN(e,t){e.nodeType===Qn?Sb(e.parentNode,t):e.nodeType===di&&Sb(e,t),Ir(e)}function _N(e){e=e;var t=e.style;typeof t.setProperty=="function"?t.setProperty("display","none","important"):t.display="none"}function LN(e){e.nodeValue=""}function zN(e,t){e=e;var a=t[cN],s=a!=null&&a.hasOwnProperty("display")?a.display:null;e.style.display=Md("display",s)}function NN(e,t){e.nodeValue=t}function PN(e){e.nodeType===di?e.textContent="":e.nodeType===mo&&e.documentElement&&e.removeChild(e.documentElement)}function FN(e,t,a){return e.nodeType!==di||t.toLowerCase()!==e.nodeName.toLowerCase()?null:e}function BN(e,t){return t===""||e.nodeType!==go?null:e}function IN(e){return e.nodeType!==Qn?null:e}function yT(e){return e.data===Ih}function Cb(e){return e.data===Uh}function UN(e){var t=e.nextSibling&&e.nextSibling.dataset,a,s,c;return t&&(a=t.dgst,s=t.msg,c=t.stck),{message:s,digest:a,stack:c}}function HN(e,t){e._reactRetry=t}function Qv(e){for(;e!=null;e=e.nextSibling){var t=e.nodeType;if(t===di||t===go)break;if(t===Qn){var a=e.data;if(a===Gv||a===Uh||a===Ih)break;if(a===Kv)return null}}return e}function Hh(e){return Qv(e.nextSibling)}function VN(e){return Qv(e.firstChild)}function WN(e){return Qv(e.firstChild)}function YN(e){return Qv(e.nextSibling)}function GN(e,t,a,s,c,p,v){Wh(p,e),kb(e,a);var w;{var C=c;w=C.namespace}var R=(p.mode&Dt)!==Qe;return nN(e,t,a,w,s,R,v)}function KN(e,t,a,s){return Wh(a,e),a.mode&Dt,rN(e,t)}function QN(e,t){Wh(t,e)}function qN(e){for(var t=e.nextSibling,a=0;t;){if(t.nodeType===Qn){var s=t.data;if(s===Kv){if(a===0)return Hh(t);a--}else(s===Gv||s===Uh||s===Ih)&&a++}t=t.nextSibling}return null}function xT(e){for(var t=e.previousSibling,a=0;t;){if(t.nodeType===Qn){var s=t.data;if(s===Gv||s===Uh||s===Ih){if(a===0)return t;a--}else s===Kv&&a++}t=t.previousSibling}return null}function XN(e){Ir(e)}function JN(e){Ir(e)}function ZN(e){return e!=="head"&&e!=="body"}function eP(e,t,a,s){var c=!0;Vv(t.nodeValue,a,s,c)}function tP(e,t,a,s,c,p){if(t[Yv]!==!0){var v=!0;Vv(s.nodeValue,c,p,v)}}function nP(e,t){t.nodeType===di?pb(e,t):t.nodeType===Qn||hb(e,t)}function rP(e,t){{var a=e.parentNode;a!==null&&(t.nodeType===di?pb(a,t):t.nodeType===Qn||hb(a,t))}}function iP(e,t,a,s,c){(c||t[Yv]!==!0)&&(s.nodeType===di?pb(a,s):s.nodeType===Qn||hb(a,s))}function aP(e,t,a){gb(e,t)}function oP(e,t){mb(e,t)}function lP(e,t,a){{var s=e.parentNode;s!==null&&gb(s,t)}}function sP(e,t){{var a=e.parentNode;a!==null&&mb(a,t)}}function uP(e,t,a,s,c,p){(p||t[Yv]!==!0)&&gb(a,s)}function cP(e,t,a,s,c){(c||t[Yv]!==!0)&&mb(a,s)}function dP(e){y("An error occurred during hydration. The server HTML was replaced with client content in <%s>.",e.nodeName.toLowerCase())}function fP(e){Lh(e)}var Pf=Math.random().toString(36).slice(2),Ff="__reactFiber$"+Pf,Eb="__reactProps$"+Pf,Vh="__reactContainer$"+Pf,Tb="__reactEvents$"+Pf,pP="__reactListeners$"+Pf,hP="__reactHandles$"+Pf;function gP(e){delete e[Ff],delete e[Eb],delete e[Tb],delete e[pP],delete e[hP]}function Wh(e,t){t[Ff]=e}function qv(e,t){t[Vh]=e}function bT(e){e[Vh]=null}function Yh(e){return!!e[Vh]}function Ic(e){var t=e[Ff];if(t)return t;for(var a=e.parentNode;a;){if(t=a[Vh]||a[Ff],t){var s=t.alternate;if(t.child!==null||s!==null&&s.child!==null)for(var c=xT(e);c!==null;){var p=c[Ff];if(p)return p;c=xT(c)}return t}e=a,a=e.parentNode}return null}function du(e){var t=e[Ff]||e[Vh];return t&&(t.tag===L||t.tag===K||t.tag===le||t.tag===j)?t:null}function Bf(e){if(e.tag===L||e.tag===K)return e.stateNode;throw new Error("getNodeFromInstance: Invalid argument.")}function Xv(e){return e[Eb]||null}function kb(e,t){e[Eb]=t}function mP(e){var t=e[Tb];return t===void 0&&(t=e[Tb]=new Set),t}var wT={},ST=d.ReactDebugCurrentFrame;function Jv(e){if(e){var t=e._owner,a=Uu(e.type,e._source,t?t.type:null);ST.setExtraStackFrame(a)}else ST.setExtraStackFrame(null)}function Co(e,t,a,s,c){{var p=Function.call.bind(Pn);for(var v in e)if(p(e,v)){var w=void 0;try{if(typeof e[v]!="function"){var C=Error((s||"React class")+": "+a+" type `"+v+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof e[v]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw C.name="Invariant Violation",C}w=e[v](t,v,s,a,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(R){w=R}w&&!(w instanceof Error)&&(Jv(c),y("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",s||"React class",a,v,typeof w),Jv(null)),w instanceof Error&&!(w.message in wT)&&(wT[w.message]=!0,Jv(c),y("Failed %s type: %s",a,w.message),Jv(null))}}}var Rb=[],Zv;Zv=[];var rs=-1;function fu(e){return{current:e}}function wi(e,t){if(rs<0){y("Unexpected pop.");return}t!==Zv[rs]&&y("Unexpected Fiber popped."),e.current=Rb[rs],Rb[rs]=null,Zv[rs]=null,rs--}function Si(e,t,a){rs++,Rb[rs]=e.current,Zv[rs]=a,e.current=t}var Db;Db={};var Ra={};Object.freeze(Ra);var is=fu(Ra),ul=fu(!1),Mb=Ra;function If(e,t,a){return a&&cl(t)?Mb:is.current}function CT(e,t,a){{var s=e.stateNode;s.__reactInternalMemoizedUnmaskedChildContext=t,s.__reactInternalMemoizedMaskedChildContext=a}}function Uf(e,t){{var a=e.type,s=a.contextTypes;if(!s)return Ra;var c=e.stateNode;if(c&&c.__reactInternalMemoizedUnmaskedChildContext===t)return c.__reactInternalMemoizedMaskedChildContext;var p={};for(var v in s)p[v]=t[v];{var w=lt(e)||"Unknown";Co(s,p,"context",w)}return c&&CT(e,t,p),p}}function ey(){return ul.current}function cl(e){{var t=e.childContextTypes;return t!=null}}function ty(e){wi(ul,e),wi(is,e)}function $b(e){wi(ul,e),wi(is,e)}function ET(e,t,a){{if(is.current!==Ra)throw new Error("Unexpected context found on stack. This error is likely caused by a bug in React. Please file an issue.");Si(is,t,e),Si(ul,a,e)}}function TT(e,t,a){{var s=e.stateNode,c=t.childContextTypes;if(typeof s.getChildContext!="function"){{var p=lt(e)||"Unknown";Db[p]||(Db[p]=!0,y("%s.childContextTypes is specified but there is no getChildContext() method on the instance. You can either define getChildContext() on %s or remove childContextTypes from it.",p,p))}return a}var v=s.getChildContext();for(var w in v)if(!(w in c))throw new Error((lt(e)||"Unknown")+'.getChildContext(): key "'+w+'" is not defined in childContextTypes.');{var C=lt(e)||"Unknown";Co(c,v,"child context",C)}return gt({},a,v)}}function ny(e){{var t=e.stateNode,a=t&&t.__reactInternalMemoizedMergedChildContext||Ra;return Mb=is.current,Si(is,a,e),Si(ul,ul.current,e),!0}}function kT(e,t,a){{var s=e.stateNode;if(!s)throw new Error("Expected to have an instance by this point. This error is likely caused by a bug in React. Please file an issue.");if(a){var c=TT(e,t,Mb);s.__reactInternalMemoizedMergedChildContext=c,wi(ul,e),wi(is,e),Si(is,c,e),Si(ul,a,e)}else wi(ul,e),Si(ul,a,e)}}function vP(e){{if(!Xm(e)||e.tag!==M)throw new Error("Expected subtree parent to be a mounted class component. This error is likely caused by a bug in React. Please file an issue.");var t=e;do{switch(t.tag){case j:return t.stateNode.context;case M:{var a=t.type;if(cl(a))return t.stateNode.__reactInternalMemoizedMergedChildContext;break}}t=t.return}while(t!==null);throw new Error("Found unexpected detached subtree parent. This error is likely caused by a bug in React. Please file an issue.")}}var pu=0,ry=1,as=null,Ob=!1,Ab=!1;function RT(e){as===null?as=[e]:as.push(e)}function yP(e){Ob=!0,RT(e)}function DT(){Ob&&hu()}function hu(){if(!Ab&&as!==null){Ab=!0;var e=0,t=_i();try{var a=!0,s=as;for(ir(xi);e<s.length;e++){var c=s[e];do c=c(a);while(c!==null)}as=null,Ob=!1}catch(p){throw as!==null&&(as=as.slice(e+1)),qp(xo,hu),p}finally{ir(t),Ab=!1}}return null}var Hf=[],Vf=0,iy=null,ay=0,Ka=[],Qa=0,Uc=null,os=1,ls="";function xP(e){return Vc(),(e.flags&sc)!==Ke}function bP(e){return Vc(),ay}function wP(){var e=ls,t=os,a=t&~SP(t);return a.toString(32)+e}function Hc(e,t){Vc(),Hf[Vf++]=ay,Hf[Vf++]=iy,iy=e,ay=t}function MT(e,t,a){Vc(),Ka[Qa++]=os,Ka[Qa++]=ls,Ka[Qa++]=Uc,Uc=e;var s=os,c=ls,p=oy(s)-1,v=s&~(1<<p),w=a+1,C=oy(t)+p;if(C>30){var R=p-p%5,O=(1<<R)-1,U=(v&O).toString(32),B=v>>R,X=p-R,Z=oy(t)+X,te=w<<X,De=te|B,Je=U+c;os=1<<Z|De,ls=Je}else{var Ye=w<<p,Bt=Ye|v,_t=c;os=1<<C|Bt,ls=_t}}function jb(e){Vc();var t=e.return;if(t!==null){var a=1,s=0;Hc(e,a),MT(e,a,s)}}function oy(e){return 32-rr(e)}function SP(e){return 1<<oy(e)-1}function _b(e){for(;e===iy;)iy=Hf[--Vf],Hf[Vf]=null,ay=Hf[--Vf],Hf[Vf]=null;for(;e===Uc;)Uc=Ka[--Qa],Ka[Qa]=null,ls=Ka[--Qa],Ka[Qa]=null,os=Ka[--Qa],Ka[Qa]=null}function CP(){return Vc(),Uc!==null?{id:os,overflow:ls}:null}function EP(e,t){Vc(),Ka[Qa++]=os,Ka[Qa++]=ls,Ka[Qa++]=Uc,os=t.id,ls=t.overflow,Uc=e}function Vc(){qr()||y("Expected to be hydrating. This is a bug in React. Please file an issue.")}var Qr=null,qa=null,Eo=!1,Wc=!1,gu=null;function TP(){Eo&&y("We should not be hydrating here. This is a bug in React. Please file a bug.")}function $T(){Wc=!0}function kP(){return Wc}function RP(e){var t=e.stateNode.containerInfo;return qa=WN(t),Qr=e,Eo=!0,gu=null,Wc=!1,!0}function DP(e,t,a){return qa=YN(t),Qr=e,Eo=!0,gu=null,Wc=!1,a!==null&&EP(e,a),!0}function OT(e,t){switch(e.tag){case j:{nP(e.stateNode.containerInfo,t);break}case L:{var a=(e.mode&Dt)!==Qe;iP(e.type,e.memoizedProps,e.stateNode,t,a);break}case le:{var s=e.memoizedState;s.dehydrated!==null&&rP(s.dehydrated,t);break}}}function AT(e,t){OT(e,t);var a=A4();a.stateNode=t,a.return=e;var s=e.deletions;s===null?(e.deletions=[a],e.flags|=fi):s.push(a)}function Lb(e,t){{if(Wc)return;switch(e.tag){case j:{var a=e.stateNode.containerInfo;switch(t.tag){case L:var s=t.type;t.pendingProps,aP(a,s);break;case K:var c=t.pendingProps;oP(a,c);break}break}case L:{var p=e.type,v=e.memoizedProps,w=e.stateNode;switch(t.tag){case L:{var C=t.type,R=t.pendingProps,O=(e.mode&Dt)!==Qe;uP(p,v,w,C,R,O);break}case K:{var U=t.pendingProps,B=(e.mode&Dt)!==Qe;cP(p,v,w,U,B);break}}break}case le:{var X=e.memoizedState,Z=X.dehydrated;if(Z!==null)switch(t.tag){case L:var te=t.type;t.pendingProps,lP(Z,te);break;case K:var De=t.pendingProps;sP(Z,De);break}break}default:return}}}function jT(e,t){t.flags=t.flags&~zn|Ln,Lb(e,t)}function _T(e,t){switch(e.tag){case L:{var a=e.type;e.pendingProps;var s=FN(t,a);return s!==null?(e.stateNode=s,Qr=e,qa=VN(s),!0):!1}case K:{var c=e.pendingProps,p=BN(t,c);return p!==null?(e.stateNode=p,Qr=e,qa=null,!0):!1}case le:{var v=IN(t);if(v!==null){var w={dehydrated:v,treeContext:CP(),retryLane:mi};e.memoizedState=w;var C=j4(v);return C.return=e,e.child=C,Qr=e,qa=null,!0}return!1}default:return!1}}function zb(e){return(e.mode&Dt)!==Qe&&(e.flags&Et)===Ke}function Nb(e){throw new Error("Hydration failed because the initial UI does not match what was rendered on the server.")}function Pb(e){if(Eo){var t=qa;if(!t){zb(e)&&(Lb(Qr,e),Nb()),jT(Qr,e),Eo=!1,Qr=e;return}var a=t;if(!_T(e,t)){zb(e)&&(Lb(Qr,e),Nb()),t=Hh(a);var s=Qr;if(!t||!_T(e,t)){jT(Qr,e),Eo=!1,Qr=e;return}AT(s,a)}}}function MP(e,t,a){var s=e.stateNode,c=!Wc,p=GN(s,e.type,e.memoizedProps,t,a,e,c);return e.updateQueue=p,p!==null}function $P(e){var t=e.stateNode,a=e.memoizedProps,s=KN(t,a,e);if(s){var c=Qr;if(c!==null)switch(c.tag){case j:{var p=c.stateNode.containerInfo,v=(c.mode&Dt)!==Qe;eP(p,t,a,v);break}case L:{var w=c.type,C=c.memoizedProps,R=c.stateNode,O=(c.mode&Dt)!==Qe;tP(w,C,R,t,a,O);break}}}return s}function OP(e){var t=e.memoizedState,a=t!==null?t.dehydrated:null;if(!a)throw new Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");QN(a,e)}function AP(e){var t=e.memoizedState,a=t!==null?t.dehydrated:null;if(!a)throw new Error("Expected to have a hydrated suspense instance. This error is likely caused by a bug in React. Please file an issue.");return qN(a)}function LT(e){for(var t=e.return;t!==null&&t.tag!==L&&t.tag!==j&&t.tag!==le;)t=t.return;Qr=t}function ly(e){if(e!==Qr)return!1;if(!Eo)return LT(e),Eo=!0,!1;if(e.tag!==j&&(e.tag!==L||ZN(e.type)&&!xb(e.type,e.memoizedProps))){var t=qa;if(t)if(zb(e))zT(e),Nb();else for(;t;)AT(e,t),t=Hh(t)}return LT(e),e.tag===le?qa=AP(e):qa=Qr?Hh(e.stateNode):null,!0}function jP(){return Eo&&qa!==null}function zT(e){for(var t=qa;t;)OT(e,t),t=Hh(t)}function Wf(){Qr=null,qa=null,Eo=!1,Wc=!1}function NT(){gu!==null&&($R(gu),gu=null)}function qr(){return Eo}function Fb(e){gu===null?gu=[e]:gu.push(e)}var _P=d.ReactCurrentBatchConfig,LP=null;function zP(){return _P.transition}var To={recordUnsafeLifecycleWarnings:function(e,t){},flushPendingUnsafeLifecycleWarnings:function(){},recordLegacyContextWarning:function(e,t){},flushLegacyContextWarning:function(){},discardPendingWarnings:function(){}};{var NP=function(e){for(var t=null,a=e;a!==null;)a.mode&mt&&(t=a),a=a.return;return t},Yc=function(e){var t=[];return e.forEach(function(a){t.push(a)}),t.sort().join(", ")},Gh=[],Kh=[],Qh=[],qh=[],Xh=[],Jh=[],Gc=new Set;To.recordUnsafeLifecycleWarnings=function(e,t){Gc.has(e.type)||(typeof t.componentWillMount=="function"&&t.componentWillMount.__suppressDeprecationWarning!==!0&&Gh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillMount=="function"&&Kh.push(e),typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps.__suppressDeprecationWarning!==!0&&Qh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillReceiveProps=="function"&&qh.push(e),typeof t.componentWillUpdate=="function"&&t.componentWillUpdate.__suppressDeprecationWarning!==!0&&Xh.push(e),e.mode&mt&&typeof t.UNSAFE_componentWillUpdate=="function"&&Jh.push(e))},To.flushPendingUnsafeLifecycleWarnings=function(){var e=new Set;Gh.length>0&&(Gh.forEach(function(B){e.add(lt(B)||"Component"),Gc.add(B.type)}),Gh=[]);var t=new Set;Kh.length>0&&(Kh.forEach(function(B){t.add(lt(B)||"Component"),Gc.add(B.type)}),Kh=[]);var a=new Set;Qh.length>0&&(Qh.forEach(function(B){a.add(lt(B)||"Component"),Gc.add(B.type)}),Qh=[]);var s=new Set;qh.length>0&&(qh.forEach(function(B){s.add(lt(B)||"Component"),Gc.add(B.type)}),qh=[]);var c=new Set;Xh.length>0&&(Xh.forEach(function(B){c.add(lt(B)||"Component"),Gc.add(B.type)}),Xh=[]);var p=new Set;if(Jh.length>0&&(Jh.forEach(function(B){p.add(lt(B)||"Component"),Gc.add(B.type)}),Jh=[]),t.size>0){var v=Yc(t);y(`Using UNSAFE_componentWillMount in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.

Please update the following components: %s`,v)}if(s.size>0){var w=Yc(s);y(`Using UNSAFE_componentWillReceiveProps in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://reactjs.org/link/derived-state

Please update the following components: %s`,w)}if(p.size>0){var C=Yc(p);y(`Using UNSAFE_componentWillUpdate in strict mode is not recommended and may indicate bugs in your code. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.

Please update the following components: %s`,C)}if(e.size>0){var R=Yc(e);S(`componentWillMount has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move code with side effects to componentDidMount, and set initial state in the constructor.
* Rename componentWillMount to UNSAFE_componentWillMount to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,R)}if(a.size>0){var O=Yc(a);S(`componentWillReceiveProps has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* If you're updating state whenever props change, refactor your code to use memoization techniques or move it to static getDerivedStateFromProps. Learn more at: https://reactjs.org/link/derived-state
* Rename componentWillReceiveProps to UNSAFE_componentWillReceiveProps to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,O)}if(c.size>0){var U=Yc(c);S(`componentWillUpdate has been renamed, and is not recommended for use. See https://reactjs.org/link/unsafe-component-lifecycles for details.

* Move data fetching code or side effects to componentDidUpdate.
* Rename componentWillUpdate to UNSAFE_componentWillUpdate to suppress this warning in non-strict mode. In React 18.x, only the UNSAFE_ name will work. To rename all deprecated lifecycles to their new names, you can run \`npx react-codemod rename-unsafe-lifecycles\` in your project source folder.

Please update the following components: %s`,U)}};var sy=new Map,PT=new Set;To.recordLegacyContextWarning=function(e,t){var a=NP(e);if(a===null){y("Expected to find a StrictMode component in a strict mode tree. This error is likely caused by a bug in React. Please file an issue.");return}if(!PT.has(e.type)){var s=sy.get(a);(e.type.contextTypes!=null||e.type.childContextTypes!=null||t!==null&&typeof t.getChildContext=="function")&&(s===void 0&&(s=[],sy.set(a,s)),s.push(e))}},To.flushLegacyContextWarning=function(){sy.forEach(function(e,t){if(e.length!==0){var a=e[0],s=new Set;e.forEach(function(p){s.add(lt(p)||"Component"),PT.add(p.type)});var c=Yc(s);try{ln(a),y(`Legacy context API has been detected within a strict-mode tree.

The old API will be supported in all 16.x releases, but applications using it should migrate to the new version.

Please update the following components: %s

Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)}finally{_n()}}})},To.discardPendingWarnings=function(){Gh=[],Kh=[],Qh=[],qh=[],Xh=[],Jh=[],sy=new Map}}var Bb,Ib,Ub,Hb,Vb,FT=function(e,t){};Bb=!1,Ib=!1,Ub={},Hb={},Vb={},FT=function(e,t){if(!(e===null||typeof e!="object")&&!(!e._store||e._store.validated||e.key!=null)){if(typeof e._store!="object")throw new Error("React Component in warnForMissingKey should have a _store. This error is likely caused by a bug in React. Please file an issue.");e._store.validated=!0;var a=lt(t)||"Component";Hb[a]||(Hb[a]=!0,y('Each child in a list should have a unique "key" prop. See https://reactjs.org/link/warning-keys for more information.'))}};function PP(e){return e.prototype&&e.prototype.isReactComponent}function Zh(e,t,a){var s=a.ref;if(s!==null&&typeof s!="function"&&typeof s!="object"){if((e.mode&mt||We)&&!(a._owner&&a._self&&a._owner.stateNode!==a._self)&&!(a._owner&&a._owner.tag!==M)&&!(typeof a.type=="function"&&!PP(a.type))&&a._owner){var c=lt(e)||"Component";Ub[c]||(y('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. We recommend using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',c,s),Ub[c]=!0)}if(a._owner){var p=a._owner,v;if(p){var w=p;if(w.tag!==M)throw new Error("Function components cannot have string refs. We recommend using useRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref");v=w.stateNode}if(!v)throw new Error("Missing owner for string ref "+s+". This error is likely caused by a bug in React. Please file an issue.");var C=v;ga(s,"ref");var R=""+s;if(t!==null&&t.ref!==null&&typeof t.ref=="function"&&t.ref._stringRef===R)return t.ref;var O=function(U){var B=C.refs;U===null?delete B[R]:B[R]=U};return O._stringRef=R,O}else{if(typeof s!="string")throw new Error("Expected ref to be a function, a string, an object returned by React.createRef(), or null.");if(!a._owner)throw new Error("Element ref was specified as a string ("+s+`) but no owner was set. This could happen for one of the following reasons:
1. You may be adding a ref to a function component
2. You may be adding a ref to a component that was not created inside a component's render method
3. You have multiple copies of React loaded
See https://reactjs.org/link/refs-must-have-owner for more information.`)}}return s}function uy(e,t){var a=Object.prototype.toString.call(t);throw new Error("Objects are not valid as a React child (found: "+(a==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":a)+"). If you meant to render a collection of children, use an array instead.")}function cy(e){{var t=lt(e)||"Component";if(Vb[t])return;Vb[t]=!0,y("Functions are not valid as a React child. This may happen if you return a Component instead of <Component /> from render. Or maybe you meant to call this function rather than return it.")}}function BT(e){var t=e._payload,a=e._init;return a(t)}function IT(e){function t(Y,ne){if(e){var G=Y.deletions;G===null?(Y.deletions=[ne],Y.flags|=fi):G.push(ne)}}function a(Y,ne){if(!e)return null;for(var G=ne;G!==null;)t(Y,G),G=G.sibling;return null}function s(Y,ne){for(var G=new Map,ve=ne;ve!==null;)ve.key!==null?G.set(ve.key,ve):G.set(ve.index,ve),ve=ve.sibling;return G}function c(Y,ne){var G=nd(Y,ne);return G.index=0,G.sibling=null,G}function p(Y,ne,G){if(Y.index=G,!e)return Y.flags|=sc,ne;var ve=Y.alternate;if(ve!==null){var ze=ve.index;return ze<ne?(Y.flags|=Ln,ne):ze}else return Y.flags|=Ln,ne}function v(Y){return e&&Y.alternate===null&&(Y.flags|=Ln),Y}function w(Y,ne,G,ve){if(ne===null||ne.tag!==K){var ze=BS(G,Y.mode,ve);return ze.return=Y,ze}else{var Oe=c(ne,G);return Oe.return=Y,Oe}}function C(Y,ne,G,ve){var ze=G.type;if(ze===li)return O(Y,ne,G.props.children,ve,G.key);if(ne!==null&&(ne.elementType===ze||YR(ne,G)||typeof ze=="object"&&ze!==null&&ze.$$typeof===ut&&BT(ze)===ne.type)){var Oe=c(ne,G.props);return Oe.ref=Zh(Y,ne,G),Oe.return=Y,Oe._debugSource=G._source,Oe._debugOwner=G._owner,Oe}var ot=FS(G,Y.mode,ve);return ot.ref=Zh(Y,ne,G),ot.return=Y,ot}function R(Y,ne,G,ve){if(ne===null||ne.tag!==H||ne.stateNode.containerInfo!==G.containerInfo||ne.stateNode.implementation!==G.implementation){var ze=IS(G,Y.mode,ve);return ze.return=Y,ze}else{var Oe=c(ne,G.children||[]);return Oe.return=Y,Oe}}function O(Y,ne,G,ve,ze){if(ne===null||ne.tag!==de){var Oe=ku(G,Y.mode,ve,ze);return Oe.return=Y,Oe}else{var ot=c(ne,G);return ot.return=Y,ot}}function U(Y,ne,G){if(typeof ne=="string"&&ne!==""||typeof ne=="number"){var ve=BS(""+ne,Y.mode,G);return ve.return=Y,ve}if(typeof ne=="object"&&ne!==null){switch(ne.$$typeof){case br:{var ze=FS(ne,Y.mode,G);return ze.ref=Zh(Y,null,ne),ze.return=Y,ze}case Oi:{var Oe=IS(ne,Y.mode,G);return Oe.return=Y,Oe}case ut:{var ot=ne._payload,dt=ne._init;return U(Y,dt(ot),G)}}if(yt(ne)||kn(ne)){var fn=ku(ne,Y.mode,G,null);return fn.return=Y,fn}uy(Y,ne)}return typeof ne=="function"&&cy(Y),null}function B(Y,ne,G,ve){var ze=ne!==null?ne.key:null;if(typeof G=="string"&&G!==""||typeof G=="number")return ze!==null?null:w(Y,ne,""+G,ve);if(typeof G=="object"&&G!==null){switch(G.$$typeof){case br:return G.key===ze?C(Y,ne,G,ve):null;case Oi:return G.key===ze?R(Y,ne,G,ve):null;case ut:{var Oe=G._payload,ot=G._init;return B(Y,ne,ot(Oe),ve)}}if(yt(G)||kn(G))return ze!==null?null:O(Y,ne,G,ve,null);uy(Y,G)}return typeof G=="function"&&cy(Y),null}function X(Y,ne,G,ve,ze){if(typeof ve=="string"&&ve!==""||typeof ve=="number"){var Oe=Y.get(G)||null;return w(ne,Oe,""+ve,ze)}if(typeof ve=="object"&&ve!==null){switch(ve.$$typeof){case br:{var ot=Y.get(ve.key===null?G:ve.key)||null;return C(ne,ot,ve,ze)}case Oi:{var dt=Y.get(ve.key===null?G:ve.key)||null;return R(ne,dt,ve,ze)}case ut:var fn=ve._payload,Wt=ve._init;return X(Y,ne,G,Wt(fn),ze)}if(yt(ve)||kn(ve)){var lr=Y.get(G)||null;return O(ne,lr,ve,ze,null)}uy(ne,ve)}return typeof ve=="function"&&cy(ne),null}function Z(Y,ne,G){{if(typeof Y!="object"||Y===null)return ne;switch(Y.$$typeof){case br:case Oi:FT(Y,G);var ve=Y.key;if(typeof ve!="string")break;if(ne===null){ne=new Set,ne.add(ve);break}if(!ne.has(ve)){ne.add(ve);break}y("Encountered two children with the same key, `%s`. Keys should be unique so that components maintain their identity across updates. Non-unique keys may cause children to be duplicated and/or omitted — the behavior is unsupported and could change in a future version.",ve);break;case ut:var ze=Y._payload,Oe=Y._init;Z(Oe(ze),ne,G);break}}return ne}function te(Y,ne,G,ve){for(var ze=null,Oe=0;Oe<G.length;Oe++){var ot=G[Oe];ze=Z(ot,ze,Y)}for(var dt=null,fn=null,Wt=ne,lr=0,Yt=0,er=null;Wt!==null&&Yt<G.length;Yt++){Wt.index>Yt?(er=Wt,Wt=null):er=Wt.sibling;var Ei=B(Y,Wt,G[Yt],ve);if(Ei===null){Wt===null&&(Wt=er);break}e&&Wt&&Ei.alternate===null&&t(Y,Wt),lr=p(Ei,lr,Yt),fn===null?dt=Ei:fn.sibling=Ei,fn=Ei,Wt=er}if(Yt===G.length){if(a(Y,Wt),qr()){var ri=Yt;Hc(Y,ri)}return dt}if(Wt===null){for(;Yt<G.length;Yt++){var Ma=U(Y,G[Yt],ve);Ma!==null&&(lr=p(Ma,lr,Yt),fn===null?dt=Ma:fn.sibling=Ma,fn=Ma)}if(qr()){var Ii=Yt;Hc(Y,Ii)}return dt}for(var Ui=s(Y,Wt);Yt<G.length;Yt++){var Ti=X(Ui,Y,Yt,G[Yt],ve);Ti!==null&&(e&&Ti.alternate!==null&&Ui.delete(Ti.key===null?Yt:Ti.key),lr=p(Ti,lr,Yt),fn===null?dt=Ti:fn.sibling=Ti,fn=Ti)}if(e&&Ui.forEach(function(cp){return t(Y,cp)}),qr()){var hs=Yt;Hc(Y,hs)}return dt}function De(Y,ne,G,ve){var ze=kn(G);if(typeof ze!="function")throw new Error("An object is not an iterable. This error is likely caused by a bug in React. Please file an issue.");{typeof Symbol=="function"&&G[Symbol.toStringTag]==="Generator"&&(Ib||y("Using Generators as children is unsupported and will likely yield unexpected results because enumerating a generator mutates it. You may convert it to an array with `Array.from()` or the `[...spread]` operator before rendering. Keep in mind you might need to polyfill these features for older browsers."),Ib=!0),G.entries===ze&&(Bb||y("Using Maps as children is not supported. Use an array of keyed ReactElements instead."),Bb=!0);var Oe=ze.call(G);if(Oe)for(var ot=null,dt=Oe.next();!dt.done;dt=Oe.next()){var fn=dt.value;ot=Z(fn,ot,Y)}}var Wt=ze.call(G);if(Wt==null)throw new Error("An iterable object provided no iterator.");for(var lr=null,Yt=null,er=ne,Ei=0,ri=0,Ma=null,Ii=Wt.next();er!==null&&!Ii.done;ri++,Ii=Wt.next()){er.index>ri?(Ma=er,er=null):Ma=er.sibling;var Ui=B(Y,er,Ii.value,ve);if(Ui===null){er===null&&(er=Ma);break}e&&er&&Ui.alternate===null&&t(Y,er),Ei=p(Ui,Ei,ri),Yt===null?lr=Ui:Yt.sibling=Ui,Yt=Ui,er=Ma}if(Ii.done){if(a(Y,er),qr()){var Ti=ri;Hc(Y,Ti)}return lr}if(er===null){for(;!Ii.done;ri++,Ii=Wt.next()){var hs=U(Y,Ii.value,ve);hs!==null&&(Ei=p(hs,Ei,ri),Yt===null?lr=hs:Yt.sibling=hs,Yt=hs)}if(qr()){var cp=ri;Hc(Y,cp)}return lr}for(var Og=s(Y,er);!Ii.done;ri++,Ii=Wt.next()){var yl=X(Og,Y,ri,Ii.value,ve);yl!==null&&(e&&yl.alternate!==null&&Og.delete(yl.key===null?ri:yl.key),Ei=p(yl,Ei,ri),Yt===null?lr=yl:Yt.sibling=yl,Yt=yl)}if(e&&Og.forEach(function(cF){return t(Y,cF)}),qr()){var uF=ri;Hc(Y,uF)}return lr}function Je(Y,ne,G,ve){if(ne!==null&&ne.tag===K){a(Y,ne.sibling);var ze=c(ne,G);return ze.return=Y,ze}a(Y,ne);var Oe=BS(G,Y.mode,ve);return Oe.return=Y,Oe}function Ye(Y,ne,G,ve){for(var ze=G.key,Oe=ne;Oe!==null;){if(Oe.key===ze){var ot=G.type;if(ot===li){if(Oe.tag===de){a(Y,Oe.sibling);var dt=c(Oe,G.props.children);return dt.return=Y,dt._debugSource=G._source,dt._debugOwner=G._owner,dt}}else if(Oe.elementType===ot||YR(Oe,G)||typeof ot=="object"&&ot!==null&&ot.$$typeof===ut&&BT(ot)===Oe.type){a(Y,Oe.sibling);var fn=c(Oe,G.props);return fn.ref=Zh(Y,Oe,G),fn.return=Y,fn._debugSource=G._source,fn._debugOwner=G._owner,fn}a(Y,Oe);break}else t(Y,Oe);Oe=Oe.sibling}if(G.type===li){var Wt=ku(G.props.children,Y.mode,ve,G.key);return Wt.return=Y,Wt}else{var lr=FS(G,Y.mode,ve);return lr.ref=Zh(Y,ne,G),lr.return=Y,lr}}function Bt(Y,ne,G,ve){for(var ze=G.key,Oe=ne;Oe!==null;){if(Oe.key===ze)if(Oe.tag===H&&Oe.stateNode.containerInfo===G.containerInfo&&Oe.stateNode.implementation===G.implementation){a(Y,Oe.sibling);var ot=c(Oe,G.children||[]);return ot.return=Y,ot}else{a(Y,Oe);break}else t(Y,Oe);Oe=Oe.sibling}var dt=IS(G,Y.mode,ve);return dt.return=Y,dt}function _t(Y,ne,G,ve){var ze=typeof G=="object"&&G!==null&&G.type===li&&G.key===null;if(ze&&(G=G.props.children),typeof G=="object"&&G!==null){switch(G.$$typeof){case br:return v(Ye(Y,ne,G,ve));case Oi:return v(Bt(Y,ne,G,ve));case ut:var Oe=G._payload,ot=G._init;return _t(Y,ne,ot(Oe),ve)}if(yt(G))return te(Y,ne,G,ve);if(kn(G))return De(Y,ne,G,ve);uy(Y,G)}return typeof G=="string"&&G!==""||typeof G=="number"?v(Je(Y,ne,""+G,ve)):(typeof G=="function"&&cy(Y),a(Y,ne))}return _t}var Yf=IT(!0),UT=IT(!1);function FP(e,t){if(e!==null&&t.child!==e.child)throw new Error("Resuming work not yet implemented.");if(t.child!==null){var a=t.child,s=nd(a,a.pendingProps);for(t.child=s,s.return=t;a.sibling!==null;)a=a.sibling,s=s.sibling=nd(a,a.pendingProps),s.return=t;s.sibling=null}}function BP(e,t){for(var a=e.child;a!==null;)R4(a,t),a=a.sibling}var Wb=fu(null),Yb;Yb={};var dy=null,Gf=null,Gb=null,fy=!1;function py(){dy=null,Gf=null,Gb=null,fy=!1}function HT(){fy=!0}function VT(){fy=!1}function WT(e,t,a){Si(Wb,t._currentValue,e),t._currentValue=a,t._currentRenderer!==void 0&&t._currentRenderer!==null&&t._currentRenderer!==Yb&&y("Detected multiple renderers concurrently rendering the same context provider. This is currently unsupported."),t._currentRenderer=Yb}function Kb(e,t){var a=Wb.current;wi(Wb,t),e._currentValue=a}function Qb(e,t,a){for(var s=e;s!==null;){var c=s.alternate;if(Yl(s.childLanes,t)?c!==null&&!Yl(c.childLanes,t)&&(c.childLanes=xt(c.childLanes,t)):(s.childLanes=xt(s.childLanes,t),c!==null&&(c.childLanes=xt(c.childLanes,t))),s===a)break;s=s.return}s!==a&&y("Expected to find the propagation root when scheduling context work. This error is likely caused by a bug in React. Please file an issue.")}function IP(e,t,a){UP(e,t,a)}function UP(e,t,a){var s=e.child;for(s!==null&&(s.return=e);s!==null;){var c=void 0,p=s.dependencies;if(p!==null){c=s.child;for(var v=p.firstContext;v!==null;){if(v.context===t){if(s.tag===M){var w=gr(a),C=ss(rn,w);C.tag=gy;var R=s.updateQueue;if(R!==null){var O=R.shared,U=O.pending;U===null?C.next=C:(C.next=U.next,U.next=C),O.pending=C}}s.lanes=xt(s.lanes,a);var B=s.alternate;B!==null&&(B.lanes=xt(B.lanes,a)),Qb(s.return,a,e),p.lanes=xt(p.lanes,a);break}v=v.next}}else if(s.tag===ae)c=s.type===e.type?null:s.child;else if(s.tag===Tt){var X=s.return;if(X===null)throw new Error("We just came from a parent so we must have had a parent. This is a bug in React.");X.lanes=xt(X.lanes,a);var Z=X.alternate;Z!==null&&(Z.lanes=xt(Z.lanes,a)),Qb(X,a,e),c=s.sibling}else c=s.child;if(c!==null)c.return=s;else for(c=s;c!==null;){if(c===e){c=null;break}var te=c.sibling;if(te!==null){te.return=c.return,c=te;break}c=c.return}s=c}}function Kf(e,t){dy=e,Gf=null,Gb=null;var a=e.dependencies;if(a!==null){var s=a.firstContext;s!==null&&(yi(a.lanes,t)&&hg(),a.firstContext=null)}}function vr(e){fy&&y("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");var t=e._currentValue;if(Gb!==e){var a={context:e,memoizedValue:t,next:null};if(Gf===null){if(dy===null)throw new Error("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().");Gf=a,dy.dependencies={lanes:ie,firstContext:a}}else Gf=Gf.next=a}return t}var Kc=null;function qb(e){Kc===null?Kc=[e]:Kc.push(e)}function HP(){if(Kc!==null){for(var e=0;e<Kc.length;e++){var t=Kc[e],a=t.interleaved;if(a!==null){t.interleaved=null;var s=a.next,c=t.pending;if(c!==null){var p=c.next;c.next=s,a.next=p}t.pending=a}}Kc=null}}function YT(e,t,a,s){var c=t.interleaved;return c===null?(a.next=a,qb(t)):(a.next=c.next,c.next=a),t.interleaved=a,hy(e,s)}function VP(e,t,a,s){var c=t.interleaved;c===null?(a.next=a,qb(t)):(a.next=c.next,c.next=a),t.interleaved=a}function WP(e,t,a,s){var c=t.interleaved;return c===null?(a.next=a,qb(t)):(a.next=c.next,c.next=a),t.interleaved=a,hy(e,s)}function ua(e,t){return hy(e,t)}var YP=hy;function hy(e,t){e.lanes=xt(e.lanes,t);var a=e.alternate;a!==null&&(a.lanes=xt(a.lanes,t)),a===null&&(e.flags&(Ln|zn))!==Ke&&UR(e);for(var s=e,c=e.return;c!==null;)c.childLanes=xt(c.childLanes,t),a=c.alternate,a!==null?a.childLanes=xt(a.childLanes,t):(c.flags&(Ln|zn))!==Ke&&UR(e),s=c,c=c.return;if(s.tag===j){var p=s.stateNode;return p}else return null}var GT=0,KT=1,gy=2,Xb=3,my=!1,Jb,vy;Jb=!1,vy=null;function Zb(e){var t={baseState:e.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:ie},effects:null};e.updateQueue=t}function QT(e,t){var a=t.updateQueue,s=e.updateQueue;if(a===s){var c={baseState:s.baseState,firstBaseUpdate:s.firstBaseUpdate,lastBaseUpdate:s.lastBaseUpdate,shared:s.shared,effects:s.effects};t.updateQueue=c}}function ss(e,t){var a={eventTime:e,lane:t,tag:GT,payload:null,callback:null,next:null};return a}function mu(e,t,a){var s=e.updateQueue;if(s===null)return null;var c=s.shared;if(vy===c&&!Jb&&(y("An update (setState, replaceState, or forceUpdate) was scheduled from inside an update function. Update functions should be pure, with zero side-effects. Consider using componentDidUpdate or a callback."),Jb=!0),V5()){var p=c.pending;return p===null?t.next=t:(t.next=p.next,p.next=t),c.pending=t,YP(e,a)}else return WP(e,c,t,a)}function yy(e,t,a){var s=t.updateQueue;if(s!==null){var c=s.shared;if(ph(a)){var p=c.lanes;p=pf(p,e.pendingLanes);var v=xt(p,a);c.lanes=v,Ec(e,v)}}}function tw(e,t){var a=e.updateQueue,s=e.alternate;if(s!==null){var c=s.updateQueue;if(a===c){var p=null,v=null,w=a.firstBaseUpdate;if(w!==null){var C=w;do{var R={eventTime:C.eventTime,lane:C.lane,tag:C.tag,payload:C.payload,callback:C.callback,next:null};v===null?p=v=R:(v.next=R,v=R),C=C.next}while(C!==null);v===null?p=v=t:(v.next=t,v=t)}else p=v=t;a={baseState:c.baseState,firstBaseUpdate:p,lastBaseUpdate:v,shared:c.shared,effects:c.effects},e.updateQueue=a;return}}var O=a.lastBaseUpdate;O===null?a.firstBaseUpdate=t:O.next=t,a.lastBaseUpdate=t}function GP(e,t,a,s,c,p){switch(a.tag){case KT:{var v=a.payload;if(typeof v=="function"){HT();var w=v.call(p,s,c);{if(e.mode&mt){nn(!0);try{v.call(p,s,c)}finally{nn(!1)}}VT()}return w}return v}case Xb:e.flags=e.flags&~Pr|Et;case GT:{var C=a.payload,R;if(typeof C=="function"){HT(),R=C.call(p,s,c);{if(e.mode&mt){nn(!0);try{C.call(p,s,c)}finally{nn(!1)}}VT()}}else R=C;return R==null?s:gt({},s,R)}case gy:return my=!0,s}return s}function xy(e,t,a,s){var c=e.updateQueue;my=!1,vy=c.shared;var p=c.firstBaseUpdate,v=c.lastBaseUpdate,w=c.shared.pending;if(w!==null){c.shared.pending=null;var C=w,R=C.next;C.next=null,v===null?p=R:v.next=R,v=C;var O=e.alternate;if(O!==null){var U=O.updateQueue,B=U.lastBaseUpdate;B!==v&&(B===null?U.firstBaseUpdate=R:B.next=R,U.lastBaseUpdate=C)}}if(p!==null){var X=c.baseState,Z=ie,te=null,De=null,Je=null,Ye=p;do{var Bt=Ye.lane,_t=Ye.eventTime;if(Yl(s,Bt)){if(Je!==null){var ne={eventTime:_t,lane:Jn,tag:Ye.tag,payload:Ye.payload,callback:Ye.callback,next:null};Je=Je.next=ne}X=GP(e,c,Ye,X,t,a);var G=Ye.callback;if(G!==null&&Ye.lane!==Jn){e.flags|=gn;var ve=c.effects;ve===null?c.effects=[Ye]:ve.push(Ye)}}else{var Y={eventTime:_t,lane:Bt,tag:Ye.tag,payload:Ye.payload,callback:Ye.callback,next:null};Je===null?(De=Je=Y,te=X):Je=Je.next=Y,Z=xt(Z,Bt)}if(Ye=Ye.next,Ye===null){if(w=c.shared.pending,w===null)break;var ze=w,Oe=ze.next;ze.next=null,Ye=Oe,c.lastBaseUpdate=ze,c.shared.pending=null}}while(!0);Je===null&&(te=X),c.baseState=te,c.firstBaseUpdate=De,c.lastBaseUpdate=Je;var ot=c.shared.interleaved;if(ot!==null){var dt=ot;do Z=xt(Z,dt.lane),dt=dt.next;while(dt!==ot)}else p===null&&(c.shared.lanes=ie);kg(Z),e.lanes=Z,e.memoizedState=X}vy=null}function KP(e,t){if(typeof e!="function")throw new Error("Invalid argument passed as callback. Expected a function. Instead "+("received: "+e));e.call(t)}function qT(){my=!1}function by(){return my}function XT(e,t,a){var s=t.effects;if(t.effects=null,s!==null)for(var c=0;c<s.length;c++){var p=s[c],v=p.callback;v!==null&&(p.callback=null,KP(v,a))}}var eg={},vu=fu(eg),tg=fu(eg),wy=fu(eg);function Sy(e){if(e===eg)throw new Error("Expected host context to exist. This error is likely caused by a bug in React. Please file an issue.");return e}function JT(){var e=Sy(wy.current);return e}function nw(e,t){Si(wy,t,e),Si(tg,e,e),Si(vu,eg,e);var a=dN(t);wi(vu,e),Si(vu,a,e)}function Qf(e){wi(vu,e),wi(tg,e),wi(wy,e)}function rw(){var e=Sy(vu.current);return e}function ZT(e){Sy(wy.current);var t=Sy(vu.current),a=fN(t,e.type);t!==a&&(Si(tg,e,e),Si(vu,a,e))}function iw(e){tg.current===e&&(wi(vu,e),wi(tg,e))}var QP=0,ek=1,tk=1,ng=2,ko=fu(QP);function aw(e,t){return(e&t)!==0}function qf(e){return e&ek}function ow(e,t){return e&ek|t}function qP(e,t){return e|t}function yu(e,t){Si(ko,t,e)}function Xf(e){wi(ko,e)}function XP(e,t){var a=e.memoizedState;return a!==null?a.dehydrated!==null:(e.memoizedProps,!0)}function Cy(e){for(var t=e;t!==null;){if(t.tag===le){var a=t.memoizedState;if(a!==null){var s=a.dehydrated;if(s===null||yT(s)||Cb(s))return t}}else if(t.tag===bt&&t.memoizedProps.revealOrder!==void 0){var c=(t.flags&Et)!==Ke;if(c)return t}else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)return null;for(;t.sibling===null;){if(t.return===null||t.return===e)return null;t=t.return}t.sibling.return=t.return,t=t.sibling}return null}var ca=0,kr=1,dl=2,Rr=4,Xr=8,lw=[];function sw(){for(var e=0;e<lw.length;e++){var t=lw[e];t._workInProgressVersionPrimary=null}lw.length=0}function JP(e,t){var a=t._getVersion,s=a(t._source);e.mutableSourceEagerHydrationData==null?e.mutableSourceEagerHydrationData=[t,s]:e.mutableSourceEagerHydrationData.push(t,s)}var Le=d.ReactCurrentDispatcher,rg=d.ReactCurrentBatchConfig,uw,Jf;uw=new Set;var Qc=ie,dn=null,Dr=null,Mr=null,Ey=!1,ig=!1,ag=0,ZP=0,e3=25,oe=null,Xa=null,xu=-1,cw=!1;function Jt(){{var e=oe;Xa===null?Xa=[e]:Xa.push(e)}}function Se(){{var e=oe;Xa!==null&&(xu++,Xa[xu]!==e&&t3(e))}}function Zf(e){e!=null&&!yt(e)&&y("%s received a final argument that is not an array (instead, received `%s`). When specified, the final argument must be an array.",oe,typeof e)}function t3(e){{var t=lt(dn);if(!uw.has(t)&&(uw.add(t),Xa!==null)){for(var a="",s=30,c=0;c<=xu;c++){for(var p=Xa[c],v=c===xu?e:p,w=c+1+". "+p;w.length<s;)w+=" ";w+=v+`
`,a+=w}y(`React has detected a change in the order of Hooks called by %s. This will lead to bugs and errors if not fixed. For more information, read the Rules of Hooks: https://reactjs.org/link/rules-of-hooks

   Previous render            Next render
   ------------------------------------------------------
%s   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
`,t,a)}}}function Ci(){throw new Error(`Invalid hook call. Hooks can only be called inside of the body of a function component. This could happen for one of the following reasons:
1. You might have mismatching versions of React and the renderer (such as React DOM)
2. You might be breaking the Rules of Hooks
3. You might have more than one copy of React in the same app
See https://reactjs.org/link/invalid-hook-call for tips about how to debug and fix this problem.`)}function dw(e,t){if(cw)return!1;if(t===null)return y("%s received a final argument during this render, but not during the previous render. Even though the final argument is optional, its type cannot change between renders.",oe),!1;e.length!==t.length&&y(`The final argument passed to %s changed size between renders. The order and size of this array must remain constant.

Previous: %s
Incoming: %s`,oe,"["+t.join(", ")+"]","["+e.join(", ")+"]");for(var a=0;a<t.length&&a<e.length;a++)if(!$e(e[a],t[a]))return!1;return!0}function ep(e,t,a,s,c,p){Qc=p,dn=t,Xa=e!==null?e._debugHookTypes:null,xu=-1,cw=e!==null&&e.type!==t.type,t.memoizedState=null,t.updateQueue=null,t.lanes=ie,e!==null&&e.memoizedState!==null?Le.current=Ck:Xa!==null?Le.current=Sk:Le.current=wk;var v=a(s,c);if(ig){var w=0;do{if(ig=!1,ag=0,w>=e3)throw new Error("Too many re-renders. React limits the number of renders to prevent an infinite loop.");w+=1,cw=!1,Dr=null,Mr=null,t.updateQueue=null,xu=-1,Le.current=Ek,v=a(s,c)}while(ig)}Le.current=Ny,t._debugHookTypes=Xa;var C=Dr!==null&&Dr.next!==null;if(Qc=ie,dn=null,Dr=null,Mr=null,oe=null,Xa=null,xu=-1,e!==null&&(e.flags&Xn)!==(t.flags&Xn)&&(e.mode&Dt)!==Qe&&y("Internal React error: Expected static flag was missing. Please notify the React team."),Ey=!1,C)throw new Error("Rendered fewer hooks than expected. This may be caused by an accidental early return statement.");return v}function tp(){var e=ag!==0;return ag=0,e}function nk(e,t,a){t.updateQueue=e.updateQueue,(t.mode&cn)!==Qe?t.flags&=-50333701:t.flags&=-2053,e.lanes=Cc(e.lanes,a)}function rk(){if(Le.current=Ny,Ey){for(var e=dn.memoizedState;e!==null;){var t=e.queue;t!==null&&(t.pending=null),e=e.next}Ey=!1}Qc=ie,dn=null,Dr=null,Mr=null,Xa=null,xu=-1,oe=null,mk=!1,ig=!1,ag=0}function fl(){var e={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return Mr===null?dn.memoizedState=Mr=e:Mr=Mr.next=e,Mr}function Ja(){var e;if(Dr===null){var t=dn.alternate;t!==null?e=t.memoizedState:e=null}else e=Dr.next;var a;if(Mr===null?a=dn.memoizedState:a=Mr.next,a!==null)Mr=a,a=Mr.next,Dr=e;else{if(e===null)throw new Error("Rendered more hooks than during the previous render.");Dr=e;var s={memoizedState:Dr.memoizedState,baseState:Dr.baseState,baseQueue:Dr.baseQueue,queue:Dr.queue,next:null};Mr===null?dn.memoizedState=Mr=s:Mr=Mr.next=s}return Mr}function ik(){return{lastEffect:null,stores:null}}function fw(e,t){return typeof t=="function"?t(e):t}function pw(e,t,a){var s=fl(),c;a!==void 0?c=a(t):c=t,s.memoizedState=s.baseState=c;var p={pending:null,interleaved:null,lanes:ie,dispatch:null,lastRenderedReducer:e,lastRenderedState:c};s.queue=p;var v=p.dispatch=a3.bind(null,dn,p);return[s.memoizedState,v]}function hw(e,t,a){var s=Ja(),c=s.queue;if(c===null)throw new Error("Should have a queue. This is likely a bug in React. Please file an issue.");c.lastRenderedReducer=e;var p=Dr,v=p.baseQueue,w=c.pending;if(w!==null){if(v!==null){var C=v.next,R=w.next;v.next=R,w.next=C}p.baseQueue!==v&&y("Internal error: Expected work-in-progress queue to be a clone. This is a bug in React."),p.baseQueue=v=w,c.pending=null}if(v!==null){var O=v.next,U=p.baseState,B=null,X=null,Z=null,te=O;do{var De=te.lane;if(Yl(Qc,De)){if(Z!==null){var Ye={lane:Jn,action:te.action,hasEagerState:te.hasEagerState,eagerState:te.eagerState,next:null};Z=Z.next=Ye}if(te.hasEagerState)U=te.eagerState;else{var Bt=te.action;U=e(U,Bt)}}else{var Je={lane:De,action:te.action,hasEagerState:te.hasEagerState,eagerState:te.eagerState,next:null};Z===null?(X=Z=Je,B=U):Z=Z.next=Je,dn.lanes=xt(dn.lanes,De),kg(De)}te=te.next}while(te!==null&&te!==O);Z===null?B=U:Z.next=X,$e(U,s.memoizedState)||hg(),s.memoizedState=U,s.baseState=B,s.baseQueue=Z,c.lastRenderedState=U}var _t=c.interleaved;if(_t!==null){var Y=_t;do{var ne=Y.lane;dn.lanes=xt(dn.lanes,ne),kg(ne),Y=Y.next}while(Y!==_t)}else v===null&&(c.lanes=ie);var G=c.dispatch;return[s.memoizedState,G]}function gw(e,t,a){var s=Ja(),c=s.queue;if(c===null)throw new Error("Should have a queue. This is likely a bug in React. Please file an issue.");c.lastRenderedReducer=e;var p=c.dispatch,v=c.pending,w=s.memoizedState;if(v!==null){c.pending=null;var C=v.next,R=C;do{var O=R.action;w=e(w,O),R=R.next}while(R!==C);$e(w,s.memoizedState)||hg(),s.memoizedState=w,s.baseQueue===null&&(s.baseState=w),c.lastRenderedState=w}return[w,p]}function hF(e,t,a){}function gF(e,t,a){}function mw(e,t,a){var s=dn,c=fl(),p,v=qr();if(v){if(a===void 0)throw new Error("Missing getServerSnapshot, which is required for server-rendered content. Will revert to client rendering.");p=a(),Jf||p!==a()&&(y("The result of getServerSnapshot should be cached to avoid an infinite loop"),Jf=!0)}else{if(p=t(),!Jf){var w=t();$e(p,w)||(y("The result of getSnapshot should be cached to avoid an infinite loop"),Jf=!0)}var C=nx();if(C===null)throw new Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");Sc(C,Qc)||ak(s,t,p)}c.memoizedState=p;var R={value:p,getSnapshot:t};return c.queue=R,My(lk.bind(null,s,R,e),[e]),s.flags|=Ai,og(kr|Xr,ok.bind(null,s,R,p,t),void 0,null),p}function Ty(e,t,a){var s=dn,c=Ja(),p=t();if(!Jf){var v=t();$e(p,v)||(y("The result of getSnapshot should be cached to avoid an infinite loop"),Jf=!0)}var w=c.memoizedState,C=!$e(w,p);C&&(c.memoizedState=p,hg());var R=c.queue;if(sg(lk.bind(null,s,R,e),[e]),R.getSnapshot!==t||C||Mr!==null&&Mr.memoizedState.tag&kr){s.flags|=Ai,og(kr|Xr,ok.bind(null,s,R,p,t),void 0,null);var O=nx();if(O===null)throw new Error("Expected a work-in-progress root. This is a bug in React. Please file an issue.");Sc(O,Qc)||ak(s,t,p)}return p}function ak(e,t,a){e.flags|=Bd;var s={getSnapshot:t,value:a},c=dn.updateQueue;if(c===null)c=ik(),dn.updateQueue=c,c.stores=[s];else{var p=c.stores;p===null?c.stores=[s]:p.push(s)}}function ok(e,t,a,s){t.value=a,t.getSnapshot=s,sk(t)&&uk(e)}function lk(e,t,a){var s=function(){sk(t)&&uk(e)};return a(s)}function sk(e){var t=e.getSnapshot,a=e.value;try{var s=t();return!$e(a,s)}catch{return!0}}function uk(e){var t=ua(e,nt);t!==null&&jr(t,e,nt,rn)}function ky(e){var t=fl();typeof e=="function"&&(e=e()),t.memoizedState=t.baseState=e;var a={pending:null,interleaved:null,lanes:ie,dispatch:null,lastRenderedReducer:fw,lastRenderedState:e};t.queue=a;var s=a.dispatch=o3.bind(null,dn,a);return[t.memoizedState,s]}function vw(e){return hw(fw)}function yw(e){return gw(fw)}function og(e,t,a,s){var c={tag:e,create:t,destroy:a,deps:s,next:null},p=dn.updateQueue;if(p===null)p=ik(),dn.updateQueue=p,p.lastEffect=c.next=c;else{var v=p.lastEffect;if(v===null)p.lastEffect=c.next=c;else{var w=v.next;v.next=c,c.next=w,p.lastEffect=c}}return c}function xw(e){var t=fl();{var a={current:e};return t.memoizedState=a,a}}function Ry(e){var t=Ja();return t.memoizedState}function lg(e,t,a,s){var c=fl(),p=s===void 0?null:s;dn.flags|=e,c.memoizedState=og(kr|t,a,void 0,p)}function Dy(e,t,a,s){var c=Ja(),p=s===void 0?null:s,v=void 0;if(Dr!==null){var w=Dr.memoizedState;if(v=w.destroy,p!==null){var C=w.deps;if(dw(p,C)){c.memoizedState=og(t,a,v,p);return}}}dn.flags|=e,c.memoizedState=og(kr|t,a,v,p)}function My(e,t){return(dn.mode&cn)!==Qe?lg(Wo|Ai|Gp,Xr,e,t):lg(Ai|Gp,Xr,e,t)}function sg(e,t){return Dy(Ai,Xr,e,t)}function bw(e,t){return lg(At,dl,e,t)}function $y(e,t){return Dy(At,dl,e,t)}function ww(e,t){var a=At;return a|=Vo,(dn.mode&cn)!==Qe&&(a|=Yr),lg(a,Rr,e,t)}function Oy(e,t){return Dy(At,Rr,e,t)}function ck(e,t){if(typeof t=="function"){var a=t,s=e();return a(s),function(){a(null)}}else if(t!=null){var c=t;c.hasOwnProperty("current")||y("Expected useImperativeHandle() first argument to either be a ref callback or React.createRef() object. Instead received: %s.","an object with keys {"+Object.keys(c).join(", ")+"}");var p=e();return c.current=p,function(){c.current=null}}}function Sw(e,t,a){typeof t!="function"&&y("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null");var s=a!=null?a.concat([e]):null,c=At;return c|=Vo,(dn.mode&cn)!==Qe&&(c|=Yr),lg(c,Rr,ck.bind(null,t,e),s)}function Ay(e,t,a){typeof t!="function"&&y("Expected useImperativeHandle() second argument to be a function that creates a handle. Instead received: %s.",t!==null?typeof t:"null");var s=a!=null?a.concat([e]):null;return Dy(At,Rr,ck.bind(null,t,e),s)}function n3(e,t){}var jy=n3;function Cw(e,t){var a=fl(),s=t===void 0?null:t;return a.memoizedState=[e,s],e}function _y(e,t){var a=Ja(),s=t===void 0?null:t,c=a.memoizedState;if(c!==null&&s!==null){var p=c[1];if(dw(s,p))return c[0]}return a.memoizedState=[e,s],e}function Ew(e,t){var a=fl(),s=t===void 0?null:t,c=e();return a.memoizedState=[c,s],c}function Ly(e,t){var a=Ja(),s=t===void 0?null:t,c=a.memoizedState;if(c!==null&&s!==null){var p=c[1];if(dw(s,p))return c[0]}var v=e();return a.memoizedState=[v,s],v}function Tw(e){var t=fl();return t.memoizedState=e,e}function dk(e){var t=Ja(),a=Dr,s=a.memoizedState;return pk(t,s,e)}function fk(e){var t=Ja();if(Dr===null)return t.memoizedState=e,e;var a=Dr.memoizedState;return pk(t,a,e)}function pk(e,t,a){var s=!fh(Qc);if(s){if(!$e(a,t)){var c=hh();dn.lanes=xt(dn.lanes,c),kg(c),e.baseState=!0}return t}else return e.baseState&&(e.baseState=!1,hg()),e.memoizedState=a,a}function r3(e,t,a){var s=_i();ir(Tc(s,ia)),e(!0);var c=rg.transition;rg.transition={};var p=rg.transition;rg.transition._updatedFibers=new Set;try{e(!1),t()}finally{if(ir(s),rg.transition=c,c===null&&p._updatedFibers){var v=p._updatedFibers.size;v>10&&S("Detected a large number of updates inside startTransition. If this is due to a subscription please re-write it to use React provided hooks. Otherwise concurrent mode guarantees are off the table."),p._updatedFibers.clear()}}}function kw(){var e=ky(!1),t=e[0],a=e[1],s=r3.bind(null,a),c=fl();return c.memoizedState=s,[t,s]}function hk(){var e=vw(),t=e[0],a=Ja(),s=a.memoizedState;return[t,s]}function gk(){var e=yw(),t=e[0],a=Ja(),s=a.memoizedState;return[t,s]}var mk=!1;function i3(){return mk}function Rw(){var e=fl(),t=nx(),a=t.identifierPrefix,s;if(qr()){var c=wP();s=":"+a+"R"+c;var p=ag++;p>0&&(s+="H"+p.toString(32)),s+=":"}else{var v=ZP++;s=":"+a+"r"+v.toString(32)+":"}return e.memoizedState=s,s}function zy(){var e=Ja(),t=e.memoizedState;return t}function a3(e,t,a){typeof arguments[3]=="function"&&y("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect().");var s=Eu(e),c={lane:s,action:a,hasEagerState:!1,eagerState:null,next:null};if(vk(e))yk(t,c);else{var p=YT(e,t,c,s);if(p!==null){var v=Bi();jr(p,e,s,v),xk(p,t,s)}}bk(e,s)}function o3(e,t,a){typeof arguments[3]=="function"&&y("State updates from the useState() and useReducer() Hooks don't support the second callback argument. To execute a side effect after rendering, declare it in the component body with useEffect().");var s=Eu(e),c={lane:s,action:a,hasEagerState:!1,eagerState:null,next:null};if(vk(e))yk(t,c);else{var p=e.alternate;if(e.lanes===ie&&(p===null||p.lanes===ie)){var v=t.lastRenderedReducer;if(v!==null){var w;w=Le.current,Le.current=Ro;try{var C=t.lastRenderedState,R=v(C,a);if(c.hasEagerState=!0,c.eagerState=R,$e(R,C)){VP(e,t,c,s);return}}catch{}finally{Le.current=w}}}var O=YT(e,t,c,s);if(O!==null){var U=Bi();jr(O,e,s,U),xk(O,t,s)}}bk(e,s)}function vk(e){var t=e.alternate;return e===dn||t!==null&&t===dn}function yk(e,t){ig=Ey=!0;var a=e.pending;a===null?t.next=t:(t.next=a.next,a.next=t),e.pending=t}function xk(e,t,a){if(ph(a)){var s=t.lanes;s=pf(s,e.pendingLanes);var c=xt(s,a);t.lanes=c,Ec(e,c)}}function bk(e,t,a){hc(e,t)}var Ny={readContext:vr,useCallback:Ci,useContext:Ci,useEffect:Ci,useImperativeHandle:Ci,useInsertionEffect:Ci,useLayoutEffect:Ci,useMemo:Ci,useReducer:Ci,useRef:Ci,useState:Ci,useDebugValue:Ci,useDeferredValue:Ci,useTransition:Ci,useMutableSource:Ci,useSyncExternalStore:Ci,useId:Ci,unstable_isNewReconciler:Re},wk=null,Sk=null,Ck=null,Ek=null,pl=null,Ro=null,Py=null;{var Dw=function(){y("Context can only be read while React is rendering. In classes, you can read it in the render method or getDerivedStateFromProps. In function components, you can read it directly in the function body, but not inside Hooks like useReducer() or useMemo().")},ct=function(){y("Do not call Hooks inside useEffect(...), useMemo(...), or other built-in Hooks. You can only call Hooks at the top level of your React function. For more information, see https://reactjs.org/link/rules-of-hooks")};wk={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Jt(),Zf(t),Cw(e,t)},useContext:function(e){return oe="useContext",Jt(),vr(e)},useEffect:function(e,t){return oe="useEffect",Jt(),Zf(t),My(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Jt(),Zf(a),Sw(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Jt(),Zf(t),bw(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Jt(),Zf(t),ww(e,t)},useMemo:function(e,t){oe="useMemo",Jt(),Zf(t);var a=Le.current;Le.current=pl;try{return Ew(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",Jt();var s=Le.current;Le.current=pl;try{return pw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",Jt(),xw(e)},useState:function(e){oe="useState",Jt();var t=Le.current;Le.current=pl;try{return ky(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Jt(),void 0},useDeferredValue:function(e){return oe="useDeferredValue",Jt(),Tw(e)},useTransition:function(){return oe="useTransition",Jt(),kw()},useMutableSource:function(e,t,a){return oe="useMutableSource",Jt(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Jt(),mw(e,t,a)},useId:function(){return oe="useId",Jt(),Rw()},unstable_isNewReconciler:Re},Sk={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Se(),Cw(e,t)},useContext:function(e){return oe="useContext",Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",Se(),My(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Se(),Sw(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Se(),bw(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Se(),ww(e,t)},useMemo:function(e,t){oe="useMemo",Se();var a=Le.current;Le.current=pl;try{return Ew(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",Se();var s=Le.current;Le.current=pl;try{return pw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",Se(),xw(e)},useState:function(e){oe="useState",Se();var t=Le.current;Le.current=pl;try{return ky(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Se(),void 0},useDeferredValue:function(e){return oe="useDeferredValue",Se(),Tw(e)},useTransition:function(){return oe="useTransition",Se(),kw()},useMutableSource:function(e,t,a){return oe="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Se(),mw(e,t,a)},useId:function(){return oe="useId",Se(),Rw()},unstable_isNewReconciler:Re},Ck={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Se(),_y(e,t)},useContext:function(e){return oe="useContext",Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",Se(),sg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Se(),Ay(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Se(),$y(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Se(),Oy(e,t)},useMemo:function(e,t){oe="useMemo",Se();var a=Le.current;Le.current=Ro;try{return Ly(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",Se();var s=Le.current;Le.current=Ro;try{return hw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",Se(),Ry()},useState:function(e){oe="useState",Se();var t=Le.current;Le.current=Ro;try{return vw(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Se(),jy()},useDeferredValue:function(e){return oe="useDeferredValue",Se(),dk(e)},useTransition:function(){return oe="useTransition",Se(),hk()},useMutableSource:function(e,t,a){return oe="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Se(),Ty(e,t)},useId:function(){return oe="useId",Se(),zy()},unstable_isNewReconciler:Re},Ek={readContext:function(e){return vr(e)},useCallback:function(e,t){return oe="useCallback",Se(),_y(e,t)},useContext:function(e){return oe="useContext",Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",Se(),sg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",Se(),Ay(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",Se(),$y(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",Se(),Oy(e,t)},useMemo:function(e,t){oe="useMemo",Se();var a=Le.current;Le.current=Py;try{return Ly(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",Se();var s=Le.current;Le.current=Py;try{return gw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",Se(),Ry()},useState:function(e){oe="useState",Se();var t=Le.current;Le.current=Py;try{return yw(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",Se(),jy()},useDeferredValue:function(e){return oe="useDeferredValue",Se(),fk(e)},useTransition:function(){return oe="useTransition",Se(),gk()},useMutableSource:function(e,t,a){return oe="useMutableSource",Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",Se(),Ty(e,t)},useId:function(){return oe="useId",Se(),zy()},unstable_isNewReconciler:Re},pl={readContext:function(e){return Dw(),vr(e)},useCallback:function(e,t){return oe="useCallback",ct(),Jt(),Cw(e,t)},useContext:function(e){return oe="useContext",ct(),Jt(),vr(e)},useEffect:function(e,t){return oe="useEffect",ct(),Jt(),My(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",ct(),Jt(),Sw(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",ct(),Jt(),bw(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",ct(),Jt(),ww(e,t)},useMemo:function(e,t){oe="useMemo",ct(),Jt();var a=Le.current;Le.current=pl;try{return Ew(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",ct(),Jt();var s=Le.current;Le.current=pl;try{return pw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",ct(),Jt(),xw(e)},useState:function(e){oe="useState",ct(),Jt();var t=Le.current;Le.current=pl;try{return ky(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",ct(),Jt(),void 0},useDeferredValue:function(e){return oe="useDeferredValue",ct(),Jt(),Tw(e)},useTransition:function(){return oe="useTransition",ct(),Jt(),kw()},useMutableSource:function(e,t,a){return oe="useMutableSource",ct(),Jt(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",ct(),Jt(),mw(e,t,a)},useId:function(){return oe="useId",ct(),Jt(),Rw()},unstable_isNewReconciler:Re},Ro={readContext:function(e){return Dw(),vr(e)},useCallback:function(e,t){return oe="useCallback",ct(),Se(),_y(e,t)},useContext:function(e){return oe="useContext",ct(),Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",ct(),Se(),sg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",ct(),Se(),Ay(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",ct(),Se(),$y(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",ct(),Se(),Oy(e,t)},useMemo:function(e,t){oe="useMemo",ct(),Se();var a=Le.current;Le.current=Ro;try{return Ly(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",ct(),Se();var s=Le.current;Le.current=Ro;try{return hw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",ct(),Se(),Ry()},useState:function(e){oe="useState",ct(),Se();var t=Le.current;Le.current=Ro;try{return vw(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",ct(),Se(),jy()},useDeferredValue:function(e){return oe="useDeferredValue",ct(),Se(),dk(e)},useTransition:function(){return oe="useTransition",ct(),Se(),hk()},useMutableSource:function(e,t,a){return oe="useMutableSource",ct(),Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",ct(),Se(),Ty(e,t)},useId:function(){return oe="useId",ct(),Se(),zy()},unstable_isNewReconciler:Re},Py={readContext:function(e){return Dw(),vr(e)},useCallback:function(e,t){return oe="useCallback",ct(),Se(),_y(e,t)},useContext:function(e){return oe="useContext",ct(),Se(),vr(e)},useEffect:function(e,t){return oe="useEffect",ct(),Se(),sg(e,t)},useImperativeHandle:function(e,t,a){return oe="useImperativeHandle",ct(),Se(),Ay(e,t,a)},useInsertionEffect:function(e,t){return oe="useInsertionEffect",ct(),Se(),$y(e,t)},useLayoutEffect:function(e,t){return oe="useLayoutEffect",ct(),Se(),Oy(e,t)},useMemo:function(e,t){oe="useMemo",ct(),Se();var a=Le.current;Le.current=Ro;try{return Ly(e,t)}finally{Le.current=a}},useReducer:function(e,t,a){oe="useReducer",ct(),Se();var s=Le.current;Le.current=Ro;try{return gw(e,t,a)}finally{Le.current=s}},useRef:function(e){return oe="useRef",ct(),Se(),Ry()},useState:function(e){oe="useState",ct(),Se();var t=Le.current;Le.current=Ro;try{return yw(e)}finally{Le.current=t}},useDebugValue:function(e,t){return oe="useDebugValue",ct(),Se(),jy()},useDeferredValue:function(e){return oe="useDeferredValue",ct(),Se(),fk(e)},useTransition:function(){return oe="useTransition",ct(),Se(),gk()},useMutableSource:function(e,t,a){return oe="useMutableSource",ct(),Se(),void 0},useSyncExternalStore:function(e,t,a){return oe="useSyncExternalStore",ct(),Se(),Ty(e,t)},useId:function(){return oe="useId",ct(),Se(),zy()},unstable_isNewReconciler:Re}}var bu=l.unstable_now,Tk=0,Fy=-1,ug=-1,By=-1,Mw=!1,Iy=!1;function kk(){return Mw}function l3(){Iy=!0}function s3(){Mw=!1,Iy=!1}function u3(){Mw=Iy,Iy=!1}function Rk(){return Tk}function Dk(){Tk=bu()}function $w(e){ug=bu(),e.actualStartTime<0&&(e.actualStartTime=bu())}function Mk(e){ug=-1}function Uy(e,t){if(ug>=0){var a=bu()-ug;e.actualDuration+=a,t&&(e.selfBaseDuration=a),ug=-1}}function hl(e){if(Fy>=0){var t=bu()-Fy;Fy=-1;for(var a=e.return;a!==null;){switch(a.tag){case j:var s=a.stateNode;s.effectDuration+=t;return;case Te:var c=a.stateNode;c.effectDuration+=t;return}a=a.return}}}function Ow(e){if(By>=0){var t=bu()-By;By=-1;for(var a=e.return;a!==null;){switch(a.tag){case j:var s=a.stateNode;s!==null&&(s.passiveEffectDuration+=t);return;case Te:var c=a.stateNode;c!==null&&(c.passiveEffectDuration+=t);return}a=a.return}}}function gl(){Fy=bu()}function Aw(){By=bu()}function jw(e){for(var t=e.child;t;)e.actualDuration+=t.actualDuration,t=t.sibling}function Do(e,t){if(e&&e.defaultProps){var a=gt({},t),s=e.defaultProps;for(var c in s)a[c]===void 0&&(a[c]=s[c]);return a}return t}var _w={},Lw,zw,Nw,Pw,Fw,$k,Hy,Bw,Iw,Uw,cg;{Lw=new Set,zw=new Set,Nw=new Set,Pw=new Set,Bw=new Set,Fw=new Set,Iw=new Set,Uw=new Set,cg=new Set;var Ok=new Set;Hy=function(e,t){if(!(e===null||typeof e=="function")){var a=t+"_"+e;Ok.has(a)||(Ok.add(a),y("%s(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",t,e))}},$k=function(e,t){if(t===void 0){var a=Pt(e)||"Component";Fw.has(a)||(Fw.add(a),y("%s.getDerivedStateFromProps(): A valid state object (or null) must be returned. You have returned undefined.",a))}},Object.defineProperty(_w,"_processChildContext",{enumerable:!1,value:function(){throw new Error("_processChildContext is not available in React 16+. This likely means you have multiple copies of React and are attempting to nest a React 15 tree inside a React 16 tree using unstable_renderSubtreeIntoContainer, which isn't supported. Try to make sure you have only one copy of React (and ideally, switch to ReactDOM.createPortal).")}}),Object.freeze(_w)}function Hw(e,t,a,s){var c=e.memoizedState,p=a(s,c);{if(e.mode&mt){nn(!0);try{p=a(s,c)}finally{nn(!1)}}$k(t,p)}var v=p==null?c:gt({},c,p);if(e.memoizedState=v,e.lanes===ie){var w=e.updateQueue;w.baseState=v}}var Vw={isMounted:Kp,enqueueSetState:function(e,t,a){var s=Hs(e),c=Bi(),p=Eu(s),v=ss(c,p);v.payload=t,a!=null&&(Hy(a,"setState"),v.callback=a);var w=mu(s,v,p);w!==null&&(jr(w,s,p,c),yy(w,s,p)),hc(s,p)},enqueueReplaceState:function(e,t,a){var s=Hs(e),c=Bi(),p=Eu(s),v=ss(c,p);v.tag=KT,v.payload=t,a!=null&&(Hy(a,"replaceState"),v.callback=a);var w=mu(s,v,p);w!==null&&(jr(w,s,p,c),yy(w,s,p)),hc(s,p)},enqueueForceUpdate:function(e,t){var a=Hs(e),s=Bi(),c=Eu(a),p=ss(s,c);p.tag=gy,t!=null&&(Hy(t,"forceUpdate"),p.callback=t);var v=mu(a,p,c);v!==null&&(jr(v,a,c,s),yy(v,a,c)),sh(a,c)}};function Ak(e,t,a,s,c,p,v){var w=e.stateNode;if(typeof w.shouldComponentUpdate=="function"){var C=w.shouldComponentUpdate(s,p,v);{if(e.mode&mt){nn(!0);try{C=w.shouldComponentUpdate(s,p,v)}finally{nn(!1)}}C===void 0&&y("%s.shouldComponentUpdate(): Returned undefined instead of a boolean value. Make sure to return true or false.",Pt(t)||"Component")}return C}return t.prototype&&t.prototype.isPureReactComponent?!qe(a,s)||!qe(c,p):!0}function c3(e,t,a){var s=e.stateNode;{var c=Pt(t)||"Component",p=s.render;p||(t.prototype&&typeof t.prototype.render=="function"?y("%s(...): No `render` method found on the returned component instance: did you accidentally return an object from the constructor?",c):y("%s(...): No `render` method found on the returned component instance: you may have forgotten to define `render`.",c)),s.getInitialState&&!s.getInitialState.isReactClassApproved&&!s.state&&y("getInitialState was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Did you mean to define a state property instead?",c),s.getDefaultProps&&!s.getDefaultProps.isReactClassApproved&&y("getDefaultProps was defined on %s, a plain JavaScript class. This is only supported for classes created using React.createClass. Use a static property to define defaultProps instead.",c),s.propTypes&&y("propTypes was defined as an instance property on %s. Use a static property to define propTypes instead.",c),s.contextType&&y("contextType was defined as an instance property on %s. Use a static property to define contextType instead.",c),t.childContextTypes&&!cg.has(t)&&(e.mode&mt)===Qe&&(cg.add(t),y(`%s uses the legacy childContextTypes API which is no longer supported and will be removed in the next major release. Use React.createContext() instead

.Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)),t.contextTypes&&!cg.has(t)&&(e.mode&mt)===Qe&&(cg.add(t),y(`%s uses the legacy contextTypes API which is no longer supported and will be removed in the next major release. Use React.createContext() with static contextType instead.

Learn more about this warning here: https://reactjs.org/link/legacy-context`,c)),s.contextTypes&&y("contextTypes was defined as an instance property on %s. Use a static property to define contextTypes instead.",c),t.contextType&&t.contextTypes&&!Iw.has(t)&&(Iw.add(t),y("%s declares both contextTypes and contextType static properties. The legacy contextTypes property will be ignored.",c)),typeof s.componentShouldUpdate=="function"&&y("%s has a method called componentShouldUpdate(). Did you mean shouldComponentUpdate()? The name is phrased as a question because the function is expected to return a value.",c),t.prototype&&t.prototype.isPureReactComponent&&typeof s.shouldComponentUpdate<"u"&&y("%s has a method called shouldComponentUpdate(). shouldComponentUpdate should not be used when extending React.PureComponent. Please extend React.Component if shouldComponentUpdate is used.",Pt(t)||"A pure component"),typeof s.componentDidUnmount=="function"&&y("%s has a method called componentDidUnmount(). But there is no such lifecycle method. Did you mean componentWillUnmount()?",c),typeof s.componentDidReceiveProps=="function"&&y("%s has a method called componentDidReceiveProps(). But there is no such lifecycle method. If you meant to update the state in response to changing props, use componentWillReceiveProps(). If you meant to fetch data or run side-effects or mutations after React has updated the UI, use componentDidUpdate().",c),typeof s.componentWillRecieveProps=="function"&&y("%s has a method called componentWillRecieveProps(). Did you mean componentWillReceiveProps()?",c),typeof s.UNSAFE_componentWillRecieveProps=="function"&&y("%s has a method called UNSAFE_componentWillRecieveProps(). Did you mean UNSAFE_componentWillReceiveProps()?",c);var v=s.props!==a;s.props!==void 0&&v&&y("%s(...): When calling super() in `%s`, make sure to pass up the same props that your component's constructor was passed.",c,c),s.defaultProps&&y("Setting defaultProps as an instance property on %s is not supported and will be ignored. Instead, define defaultProps as a static property on %s.",c,c),typeof s.getSnapshotBeforeUpdate=="function"&&typeof s.componentDidUpdate!="function"&&!Nw.has(t)&&(Nw.add(t),y("%s: getSnapshotBeforeUpdate() should be used with componentDidUpdate(). This component defines getSnapshotBeforeUpdate() only.",Pt(t))),typeof s.getDerivedStateFromProps=="function"&&y("%s: getDerivedStateFromProps() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof s.getDerivedStateFromError=="function"&&y("%s: getDerivedStateFromError() is defined as an instance method and will be ignored. Instead, declare it as a static method.",c),typeof t.getSnapshotBeforeUpdate=="function"&&y("%s: getSnapshotBeforeUpdate() is defined as a static method and will be ignored. Instead, declare it as an instance method.",c);var w=s.state;w&&(typeof w!="object"||yt(w))&&y("%s.state: must be set to an object or null",c),typeof s.getChildContext=="function"&&typeof t.childContextTypes!="object"&&y("%s.getChildContext(): childContextTypes must be defined in order to use getChildContext().",c)}}function jk(e,t){t.updater=Vw,e.stateNode=t,lc(t,e),t._reactInternalInstance=_w}function _k(e,t,a){var s=!1,c=Ra,p=Ra,v=t.contextType;if("contextType"in t){var w=v===null||v!==void 0&&v.$$typeof===P&&v._context===void 0;if(!w&&!Uw.has(t)){Uw.add(t);var C="";v===void 0?C=" However, it is set to undefined. This can be caused by a typo or by mixing up named and default imports. This can also happen due to a circular dependency, so try moving the createContext() call to a separate file.":typeof v!="object"?C=" However, it is set to a "+typeof v+".":v.$$typeof===oo?C=" Did you accidentally pass the Context.Provider instead?":v._context!==void 0?C=" Did you accidentally pass the Context.Consumer instead?":C=" However, it is set to an object with keys {"+Object.keys(v).join(", ")+"}.",y("%s defines an invalid contextType. contextType should point to the Context object returned by React.createContext().%s",Pt(t)||"Component",C)}}if(typeof v=="object"&&v!==null)p=vr(v);else{c=If(e,t,!0);var R=t.contextTypes;s=R!=null,p=s?Uf(e,c):Ra}var O=new t(a,p);if(e.mode&mt){nn(!0);try{O=new t(a,p)}finally{nn(!1)}}var U=e.memoizedState=O.state!==null&&O.state!==void 0?O.state:null;jk(e,O);{if(typeof t.getDerivedStateFromProps=="function"&&U===null){var B=Pt(t)||"Component";zw.has(B)||(zw.add(B),y("`%s` uses `getDerivedStateFromProps` but its initial state is %s. This is not recommended. Instead, define the initial state by assigning an object to `this.state` in the constructor of `%s`. This ensures that `getDerivedStateFromProps` arguments have a consistent shape.",B,O.state===null?"null":"undefined",B))}if(typeof t.getDerivedStateFromProps=="function"||typeof O.getSnapshotBeforeUpdate=="function"){var X=null,Z=null,te=null;if(typeof O.componentWillMount=="function"&&O.componentWillMount.__suppressDeprecationWarning!==!0?X="componentWillMount":typeof O.UNSAFE_componentWillMount=="function"&&(X="UNSAFE_componentWillMount"),typeof O.componentWillReceiveProps=="function"&&O.componentWillReceiveProps.__suppressDeprecationWarning!==!0?Z="componentWillReceiveProps":typeof O.UNSAFE_componentWillReceiveProps=="function"&&(Z="UNSAFE_componentWillReceiveProps"),typeof O.componentWillUpdate=="function"&&O.componentWillUpdate.__suppressDeprecationWarning!==!0?te="componentWillUpdate":typeof O.UNSAFE_componentWillUpdate=="function"&&(te="UNSAFE_componentWillUpdate"),X!==null||Z!==null||te!==null){var De=Pt(t)||"Component",Je=typeof t.getDerivedStateFromProps=="function"?"getDerivedStateFromProps()":"getSnapshotBeforeUpdate()";Pw.has(De)||(Pw.add(De),y(`Unsafe legacy lifecycles will not be called for components using new component APIs.

%s uses %s but also contains the following legacy lifecycles:%s%s%s

The above lifecycles should be removed. Learn more about this warning here:
https://reactjs.org/link/unsafe-component-lifecycles`,De,Je,X!==null?`
  `+X:"",Z!==null?`
  `+Z:"",te!==null?`
  `+te:""))}}}return s&&CT(e,c,p),O}function d3(e,t){var a=t.state;typeof t.componentWillMount=="function"&&t.componentWillMount(),typeof t.UNSAFE_componentWillMount=="function"&&t.UNSAFE_componentWillMount(),a!==t.state&&(y("%s.componentWillMount(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",lt(e)||"Component"),Vw.enqueueReplaceState(t,t.state,null))}function Lk(e,t,a,s){var c=t.state;if(typeof t.componentWillReceiveProps=="function"&&t.componentWillReceiveProps(a,s),typeof t.UNSAFE_componentWillReceiveProps=="function"&&t.UNSAFE_componentWillReceiveProps(a,s),t.state!==c){{var p=lt(e)||"Component";Lw.has(p)||(Lw.add(p),y("%s.componentWillReceiveProps(): Assigning directly to this.state is deprecated (except inside a component's constructor). Use setState instead.",p))}Vw.enqueueReplaceState(t,t.state,null)}}function Ww(e,t,a,s){c3(e,t,a);var c=e.stateNode;c.props=a,c.state=e.memoizedState,c.refs={},Zb(e);var p=t.contextType;if(typeof p=="object"&&p!==null)c.context=vr(p);else{var v=If(e,t,!0);c.context=Uf(e,v)}{if(c.state===a){var w=Pt(t)||"Component";Bw.has(w)||(Bw.add(w),y("%s: It is not recommended to assign props directly to state because updates to props won't be reflected in state. In most cases, it is better to use props directly.",w))}e.mode&mt&&To.recordLegacyContextWarning(e,c),To.recordUnsafeLifecycleWarnings(e,c)}c.state=e.memoizedState;var C=t.getDerivedStateFromProps;if(typeof C=="function"&&(Hw(e,t,C,a),c.state=e.memoizedState),typeof t.getDerivedStateFromProps!="function"&&typeof c.getSnapshotBeforeUpdate!="function"&&(typeof c.UNSAFE_componentWillMount=="function"||typeof c.componentWillMount=="function")&&(d3(e,c),xy(e,a,c,s),c.state=e.memoizedState),typeof c.componentDidMount=="function"){var R=At;R|=Vo,(e.mode&cn)!==Qe&&(R|=Yr),e.flags|=R}}function f3(e,t,a,s){var c=e.stateNode,p=e.memoizedProps;c.props=p;var v=c.context,w=t.contextType,C=Ra;if(typeof w=="object"&&w!==null)C=vr(w);else{var R=If(e,t,!0);C=Uf(e,R)}var O=t.getDerivedStateFromProps,U=typeof O=="function"||typeof c.getSnapshotBeforeUpdate=="function";!U&&(typeof c.UNSAFE_componentWillReceiveProps=="function"||typeof c.componentWillReceiveProps=="function")&&(p!==a||v!==C)&&Lk(e,c,a,C),qT();var B=e.memoizedState,X=c.state=B;if(xy(e,a,c,s),X=e.memoizedState,p===a&&B===X&&!ey()&&!by()){if(typeof c.componentDidMount=="function"){var Z=At;Z|=Vo,(e.mode&cn)!==Qe&&(Z|=Yr),e.flags|=Z}return!1}typeof O=="function"&&(Hw(e,t,O,a),X=e.memoizedState);var te=by()||Ak(e,t,p,a,B,X,C);if(te){if(!U&&(typeof c.UNSAFE_componentWillMount=="function"||typeof c.componentWillMount=="function")&&(typeof c.componentWillMount=="function"&&c.componentWillMount(),typeof c.UNSAFE_componentWillMount=="function"&&c.UNSAFE_componentWillMount()),typeof c.componentDidMount=="function"){var De=At;De|=Vo,(e.mode&cn)!==Qe&&(De|=Yr),e.flags|=De}}else{if(typeof c.componentDidMount=="function"){var Je=At;Je|=Vo,(e.mode&cn)!==Qe&&(Je|=Yr),e.flags|=Je}e.memoizedProps=a,e.memoizedState=X}return c.props=a,c.state=X,c.context=C,te}function p3(e,t,a,s,c){var p=t.stateNode;QT(e,t);var v=t.memoizedProps,w=t.type===t.elementType?v:Do(t.type,v);p.props=w;var C=t.pendingProps,R=p.context,O=a.contextType,U=Ra;if(typeof O=="object"&&O!==null)U=vr(O);else{var B=If(t,a,!0);U=Uf(t,B)}var X=a.getDerivedStateFromProps,Z=typeof X=="function"||typeof p.getSnapshotBeforeUpdate=="function";!Z&&(typeof p.UNSAFE_componentWillReceiveProps=="function"||typeof p.componentWillReceiveProps=="function")&&(v!==C||R!==U)&&Lk(t,p,s,U),qT();var te=t.memoizedState,De=p.state=te;if(xy(t,s,p,c),De=t.memoizedState,v===C&&te===De&&!ey()&&!by()&&!be)return typeof p.componentDidUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=na),!1;typeof X=="function"&&(Hw(t,a,X,s),De=t.memoizedState);var Je=by()||Ak(t,a,w,s,te,De,U)||be;return Je?(!Z&&(typeof p.UNSAFE_componentWillUpdate=="function"||typeof p.componentWillUpdate=="function")&&(typeof p.componentWillUpdate=="function"&&p.componentWillUpdate(s,De,U),typeof p.UNSAFE_componentWillUpdate=="function"&&p.UNSAFE_componentWillUpdate(s,De,U)),typeof p.componentDidUpdate=="function"&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(t.flags|=na)):(typeof p.componentDidUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=At),typeof p.getSnapshotBeforeUpdate=="function"&&(v!==e.memoizedProps||te!==e.memoizedState)&&(t.flags|=na),t.memoizedProps=s,t.memoizedState=De),p.props=s,p.state=De,p.context=U,Je}function qc(e,t){return{value:e,source:t,stack:Nt(t),digest:null}}function Yw(e,t,a){return{value:e,source:null,stack:a??null,digest:t??null}}function h3(e,t){return!0}function Gw(e,t){try{var a=h3(e,t);if(a===!1)return;var s=t.value,c=t.source,p=t.stack,v=p!==null?p:"";if(s!=null&&s._suppressLogging){if(e.tag===M)return;console.error(s)}var w=c?lt(c):null,C=w?"The above error occurred in the <"+w+"> component:":"The above error occurred in one of your React components:",R;if(e.tag===j)R=`Consider adding an error boundary to your tree to customize error handling behavior.
Visit https://reactjs.org/link/error-boundaries to learn more about error boundaries.`;else{var O=lt(e)||"Anonymous";R="React will try to recreate this component tree from scratch "+("using the error boundary you provided, "+O+".")}var U=C+`
`+v+`

`+(""+R);console.error(U)}catch(B){setTimeout(function(){throw B})}}var g3=typeof WeakMap=="function"?WeakMap:Map;function zk(e,t,a){var s=ss(rn,a);s.tag=Xb,s.payload={element:null};var c=t.value;return s.callback=function(){l4(c),Gw(e,t)},s}function Kw(e,t,a){var s=ss(rn,a);s.tag=Xb;var c=e.type.getDerivedStateFromError;if(typeof c=="function"){var p=t.value;s.payload=function(){return c(p)},s.callback=function(){GR(e),Gw(e,t)}}var v=e.stateNode;return v!==null&&typeof v.componentDidCatch=="function"&&(s.callback=function(){GR(e),Gw(e,t),typeof c!="function"&&a4(this);var C=t.value,R=t.stack;this.componentDidCatch(C,{componentStack:R!==null?R:""}),typeof c!="function"&&(yi(e.lanes,nt)||y("%s: Error boundaries should implement getDerivedStateFromError(). In that method, return a state update to display an error message or fallback UI.",lt(e)||"Unknown"))}),s}function Nk(e,t,a){var s=e.pingCache,c;if(s===null?(s=e.pingCache=new g3,c=new Set,s.set(t,c)):(c=s.get(t),c===void 0&&(c=new Set,s.set(t,c))),!c.has(a)){c.add(a);var p=s4.bind(null,e,t,a);Fr&&Rg(e,a),t.then(p,p)}}function m3(e,t,a,s){var c=e.updateQueue;if(c===null){var p=new Set;p.add(a),e.updateQueue=p}else c.add(a)}function v3(e,t){var a=e.tag;if((e.mode&Dt)===Qe&&(a===$||a===ce||a===Ae)){var s=e.alternate;s?(e.updateQueue=s.updateQueue,e.memoizedState=s.memoizedState,e.lanes=s.lanes):(e.updateQueue=null,e.memoizedState=null)}}function Pk(e){var t=e;do{if(t.tag===le&&XP(t))return t;t=t.return}while(t!==null);return null}function Fk(e,t,a,s,c){if((e.mode&Dt)===Qe){if(e===t)e.flags|=Pr;else{if(e.flags|=Et,a.flags|=Sa,a.flags&=-52805,a.tag===M){var p=a.alternate;if(p===null)a.tag=Ve;else{var v=ss(rn,nt);v.tag=gy,mu(a,v,nt)}}a.lanes=xt(a.lanes,nt)}return e}return e.flags|=Pr,e.lanes=c,e}function y3(e,t,a,s,c){if(a.flags|=Nl,Fr&&Rg(e,c),s!==null&&typeof s=="object"&&typeof s.then=="function"){var p=s;v3(a),qr()&&a.mode&Dt&&$T();var v=Pk(t);if(v!==null){v.flags&=~Rn,Fk(v,t,a,e,c),v.mode&Dt&&Nk(e,p,c),m3(v,e,p);return}else{if(!dh(c)){Nk(e,p,c),RS();return}var w=new Error("A component suspended while responding to synchronous input. This will cause the UI to be replaced with a loading indicator. To fix, updates that suspend should be wrapped with startTransition.");s=w}}else if(qr()&&a.mode&Dt){$T();var C=Pk(t);if(C!==null){(C.flags&Pr)===Ke&&(C.flags|=Rn),Fk(C,t,a,e,c),Fb(qc(s,a));return}}s=qc(s,a),X5(s);var R=t;do{switch(R.tag){case j:{var O=s;R.flags|=Pr;var U=gr(c);R.lanes=xt(R.lanes,U);var B=zk(R,O,U);tw(R,B);return}case M:var X=s,Z=R.type,te=R.stateNode;if((R.flags&Et)===Ke&&(typeof Z.getDerivedStateFromError=="function"||te!==null&&typeof te.componentDidCatch=="function"&&!PR(te))){R.flags|=Pr;var De=gr(c);R.lanes=xt(R.lanes,De);var Je=Kw(R,X,De);tw(R,Je);return}break}R=R.return}while(R!==null)}function x3(){return null}var dg=d.ReactCurrentOwner,Mo=!1,Qw,fg,qw,Xw,Jw,Xc,Zw,Vy,pg;Qw={},fg={},qw={},Xw={},Jw={},Xc=!1,Zw={},Vy={},pg={};function Pi(e,t,a,s){e===null?t.child=UT(t,null,a,s):t.child=Yf(t,e.child,a,s)}function b3(e,t,a,s){t.child=Yf(t,e.child,null,s),t.child=Yf(t,null,a,s)}function Bk(e,t,a,s,c){if(t.type!==t.elementType){var p=a.propTypes;p&&Co(p,s,"prop",Pt(a))}var v=a.render,w=t.ref,C,R;Kf(t,c),ra(t);{if(dg.current=t,Xi(!0),C=ep(e,t,v,s,w,c),R=tp(),t.mode&mt){nn(!0);try{C=ep(e,t,v,s,w,c),R=tp()}finally{nn(!1)}}Xi(!1)}return Qo(),e!==null&&!Mo?(nk(e,t,c),us(e,t,c)):(qr()&&R&&jb(t),t.flags|=yo,Pi(e,t,C,c),t.child)}function Ik(e,t,a,s,c){if(e===null){var p=a.type;if(T4(p)&&a.compare===null&&a.defaultProps===void 0){var v=p;return v=up(p),t.tag=Ae,t.type=v,nS(t,p),Uk(e,t,v,s,c)}{var w=p.propTypes;if(w&&Co(w,s,"prop",Pt(p)),a.defaultProps!==void 0){var C=Pt(p)||"Unknown";pg[C]||(y("%s: Support for defaultProps will be removed from memo components in a future major release. Use JavaScript default parameters instead.",C),pg[C]=!0)}}var R=PS(a.type,null,s,t,t.mode,c);return R.ref=t.ref,R.return=t,t.child=R,R}{var O=a.type,U=O.propTypes;U&&Co(U,s,"prop",Pt(O))}var B=e.child,X=sS(e,c);if(!X){var Z=B.memoizedProps,te=a.compare;if(te=te!==null?te:qe,te(Z,s)&&e.ref===t.ref)return us(e,t,c)}t.flags|=yo;var De=nd(B,s);return De.ref=t.ref,De.return=t,t.child=De,De}function Uk(e,t,a,s,c){if(t.type!==t.elementType){var p=t.elementType;if(p.$$typeof===ut){var v=p,w=v._payload,C=v._init;try{p=C(w)}catch{p=null}var R=p&&p.propTypes;R&&Co(R,s,"prop",Pt(p))}}if(e!==null){var O=e.memoizedProps;if(qe(O,s)&&e.ref===t.ref&&t.type===e.type)if(Mo=!1,t.pendingProps=s=O,sS(e,c))(e.flags&Sa)!==Ke&&(Mo=!0);else return t.lanes=e.lanes,us(e,t,c)}return eS(e,t,a,s,c)}function Hk(e,t,a){var s=t.pendingProps,c=s.children,p=e!==null?e.memoizedState:null;if(s.mode==="hidden"||V)if((t.mode&Dt)===Qe){var v={baseLanes:ie,cachePool:null,transitions:null};t.memoizedState=v,rx(t,a)}else if(yi(a,mi)){var U={baseLanes:ie,cachePool:null,transitions:null};t.memoizedState=U;var B=p!==null?p.baseLanes:a;rx(t,B)}else{var w=null,C;if(p!==null){var R=p.baseLanes;C=xt(R,a)}else C=a;t.lanes=t.childLanes=mi;var O={baseLanes:C,cachePool:w,transitions:null};return t.memoizedState=O,t.updateQueue=null,rx(t,C),null}else{var X;p!==null?(X=xt(p.baseLanes,a),t.memoizedState=null):X=a,rx(t,X)}return Pi(e,t,c,a),t.child}function w3(e,t,a){var s=t.pendingProps;return Pi(e,t,s,a),t.child}function S3(e,t,a){var s=t.pendingProps.children;return Pi(e,t,s,a),t.child}function C3(e,t,a){{t.flags|=At;{var s=t.stateNode;s.effectDuration=0,s.passiveEffectDuration=0}}var c=t.pendingProps,p=c.children;return Pi(e,t,p,a),t.child}function Vk(e,t){var a=t.ref;(e===null&&a!==null||e!==null&&e.ref!==a)&&(t.flags|=qn,t.flags|=uc)}function eS(e,t,a,s,c){if(t.type!==t.elementType){var p=a.propTypes;p&&Co(p,s,"prop",Pt(a))}var v;{var w=If(t,a,!0);v=Uf(t,w)}var C,R;Kf(t,c),ra(t);{if(dg.current=t,Xi(!0),C=ep(e,t,a,s,v,c),R=tp(),t.mode&mt){nn(!0);try{C=ep(e,t,a,s,v,c),R=tp()}finally{nn(!1)}}Xi(!1)}return Qo(),e!==null&&!Mo?(nk(e,t,c),us(e,t,c)):(qr()&&R&&jb(t),t.flags|=yo,Pi(e,t,C,c),t.child)}function Wk(e,t,a,s,c){{switch(B4(t)){case!1:{var p=t.stateNode,v=t.type,w=new v(t.memoizedProps,p.context),C=w.state;p.updater.enqueueSetState(p,C,null);break}case!0:{t.flags|=Et,t.flags|=Pr;var R=new Error("Simulated error coming from DevTools"),O=gr(c);t.lanes=xt(t.lanes,O);var U=Kw(t,qc(R,t),O);tw(t,U);break}}if(t.type!==t.elementType){var B=a.propTypes;B&&Co(B,s,"prop",Pt(a))}}var X;cl(a)?(X=!0,ny(t)):X=!1,Kf(t,c);var Z=t.stateNode,te;Z===null?(Yy(e,t),_k(t,a,s),Ww(t,a,s,c),te=!0):e===null?te=f3(t,a,s,c):te=p3(e,t,a,s,c);var De=tS(e,t,a,te,X,c);{var Je=t.stateNode;te&&Je.props!==s&&(Xc||y("It looks like %s is reassigning its own `this.props` while rendering. This is not supported and can lead to confusing bugs.",lt(t)||"a component"),Xc=!0)}return De}function tS(e,t,a,s,c,p){Vk(e,t);var v=(t.flags&Et)!==Ke;if(!s&&!v)return c&&kT(t,a,!1),us(e,t,p);var w=t.stateNode;dg.current=t;var C;if(v&&typeof a.getDerivedStateFromError!="function")C=null,Mk();else{ra(t);{if(Xi(!0),C=w.render(),t.mode&mt){nn(!0);try{w.render()}finally{nn(!1)}}Xi(!1)}Qo()}return t.flags|=yo,e!==null&&v?b3(e,t,C,p):Pi(e,t,C,p),t.memoizedState=w.state,c&&kT(t,a,!0),t.child}function Yk(e){var t=e.stateNode;t.pendingContext?ET(e,t.pendingContext,t.pendingContext!==t.context):t.context&&ET(e,t.context,!1),nw(e,t.containerInfo)}function E3(e,t,a){if(Yk(t),e===null)throw new Error("Should have a current fiber. This is a bug in React.");var s=t.pendingProps,c=t.memoizedState,p=c.element;QT(e,t),xy(t,s,null,a);var v=t.memoizedState;t.stateNode;var w=v.element;if(c.isDehydrated){var C={element:w,isDehydrated:!1,cache:v.cache,pendingSuspenseBoundaries:v.pendingSuspenseBoundaries,transitions:v.transitions},R=t.updateQueue;if(R.baseState=C,t.memoizedState=C,t.flags&Rn){var O=qc(new Error("There was an error while hydrating. Because the error happened outside of a Suspense boundary, the entire root will switch to client rendering."),t);return Gk(e,t,w,a,O)}else if(w!==p){var U=qc(new Error("This root received an early update, before anything was able hydrate. Switched the entire root to client rendering."),t);return Gk(e,t,w,a,U)}else{RP(t);var B=UT(t,null,w,a);t.child=B;for(var X=B;X;)X.flags=X.flags&~Ln|zn,X=X.sibling}}else{if(Wf(),w===p)return us(e,t,a);Pi(e,t,w,a)}return t.child}function Gk(e,t,a,s,c){return Wf(),Fb(c),t.flags|=Rn,Pi(e,t,a,s),t.child}function T3(e,t,a){ZT(t),e===null&&Pb(t);var s=t.type,c=t.pendingProps,p=e!==null?e.memoizedProps:null,v=c.children,w=xb(s,c);return w?v=null:p!==null&&xb(s,p)&&(t.flags|=tn),Vk(e,t),Pi(e,t,v,a),t.child}function k3(e,t){return e===null&&Pb(t),null}function R3(e,t,a,s){Yy(e,t);var c=t.pendingProps,p=a,v=p._payload,w=p._init,C=w(v);t.type=C;var R=t.tag=k4(C),O=Do(C,c),U;switch(R){case $:return nS(t,C),t.type=C=up(C),U=eS(null,t,C,O,s),U;case M:return t.type=C=AS(C),U=Wk(null,t,C,O,s),U;case ce:return t.type=C=jS(C),U=Bk(null,t,C,O,s),U;case ue:{if(t.type!==t.elementType){var B=C.propTypes;B&&Co(B,O,"prop",Pt(C))}return U=Ik(null,t,C,Do(C.type,O),s),U}}var X="";throw C!==null&&typeof C=="object"&&C.$$typeof===ut&&(X=" Did you wrap a component in React.lazy() more than once?"),new Error("Element type is invalid. Received a promise that resolves to: "+C+". "+("Lazy element type must resolve to a class or function."+X))}function D3(e,t,a,s,c){Yy(e,t),t.tag=M;var p;return cl(a)?(p=!0,ny(t)):p=!1,Kf(t,c),_k(t,a,s),Ww(t,a,s,c),tS(null,t,a,!0,p,c)}function M3(e,t,a,s){Yy(e,t);var c=t.pendingProps,p;{var v=If(t,a,!1);p=Uf(t,v)}Kf(t,s);var w,C;ra(t);{if(a.prototype&&typeof a.prototype.render=="function"){var R=Pt(a)||"Unknown";Qw[R]||(y("The <%s /> component appears to have a render method, but doesn't extend React.Component. This is likely to cause errors. Change %s to extend React.Component instead.",R,R),Qw[R]=!0)}t.mode&mt&&To.recordLegacyContextWarning(t,null),Xi(!0),dg.current=t,w=ep(null,t,a,c,p,s),C=tp(),Xi(!1)}if(Qo(),t.flags|=yo,typeof w=="object"&&w!==null&&typeof w.render=="function"&&w.$$typeof===void 0){var O=Pt(a)||"Unknown";fg[O]||(y("The <%s /> component appears to be a function component that returns a class instance. Change %s to a class that extends React.Component instead. If you can't use a class try assigning the prototype on the function as a workaround. `%s.prototype = React.Component.prototype`. Don't use an arrow function since it cannot be called with `new` by React.",O,O,O),fg[O]=!0)}if(typeof w=="object"&&w!==null&&typeof w.render=="function"&&w.$$typeof===void 0){{var U=Pt(a)||"Unknown";fg[U]||(y("The <%s /> component appears to be a function component that returns a class instance. Change %s to a class that extends React.Component instead. If you can't use a class try assigning the prototype on the function as a workaround. `%s.prototype = React.Component.prototype`. Don't use an arrow function since it cannot be called with `new` by React.",U,U,U),fg[U]=!0)}t.tag=M,t.memoizedState=null,t.updateQueue=null;var B=!1;return cl(a)?(B=!0,ny(t)):B=!1,t.memoizedState=w.state!==null&&w.state!==void 0?w.state:null,Zb(t),jk(t,w),Ww(t,a,c,s),tS(null,t,a,!0,B,s)}else{if(t.tag=$,t.mode&mt){nn(!0);try{w=ep(null,t,a,c,p,s),C=tp()}finally{nn(!1)}}return qr()&&C&&jb(t),Pi(null,t,w,s),nS(t,a),t.child}}function nS(e,t){{if(t&&t.childContextTypes&&y("%s(...): childContextTypes cannot be defined on a function component.",t.displayName||t.name||"Component"),e.ref!==null){var a="",s=Vr();s&&(a+=`

Check the render method of \``+s+"`.");var c=s||"",p=e._debugSource;p&&(c=p.fileName+":"+p.lineNumber),Jw[c]||(Jw[c]=!0,y("Function components cannot be given refs. Attempts to access this ref will fail. Did you mean to use React.forwardRef()?%s",a))}if(t.defaultProps!==void 0){var v=Pt(t)||"Unknown";pg[v]||(y("%s: Support for defaultProps will be removed from function components in a future major release. Use JavaScript default parameters instead.",v),pg[v]=!0)}if(typeof t.getDerivedStateFromProps=="function"){var w=Pt(t)||"Unknown";Xw[w]||(y("%s: Function components do not support getDerivedStateFromProps.",w),Xw[w]=!0)}if(typeof t.contextType=="object"&&t.contextType!==null){var C=Pt(t)||"Unknown";qw[C]||(y("%s: Function components do not support contextType.",C),qw[C]=!0)}}}var rS={dehydrated:null,treeContext:null,retryLane:Jn};function iS(e){return{baseLanes:e,cachePool:x3(),transitions:null}}function $3(e,t){var a=null;return{baseLanes:xt(e.baseLanes,t),cachePool:a,transitions:e.transitions}}function O3(e,t,a,s){if(t!==null){var c=t.memoizedState;if(c===null)return!1}return aw(e,ng)}function A3(e,t){return Cc(e.childLanes,t)}function Kk(e,t,a){var s=t.pendingProps;I4(t)&&(t.flags|=Et);var c=ko.current,p=!1,v=(t.flags&Et)!==Ke;if(v||O3(c,e)?(p=!0,t.flags&=~Et):(e===null||e.memoizedState!==null)&&(c=qP(c,tk)),c=qf(c),yu(t,c),e===null){Pb(t);var w=t.memoizedState;if(w!==null){var C=w.dehydrated;if(C!==null)return N3(t,C)}var R=s.children,O=s.fallback;if(p){var U=j3(t,R,O,a),B=t.child;return B.memoizedState=iS(a),t.memoizedState=rS,U}else return aS(t,R)}else{var X=e.memoizedState;if(X!==null){var Z=X.dehydrated;if(Z!==null)return P3(e,t,v,s,Z,X,a)}if(p){var te=s.fallback,De=s.children,Je=L3(e,t,De,te,a),Ye=t.child,Bt=e.child.memoizedState;return Ye.memoizedState=Bt===null?iS(a):$3(Bt,a),Ye.childLanes=A3(e,a),t.memoizedState=rS,Je}else{var _t=s.children,Y=_3(e,t,_t,a);return t.memoizedState=null,Y}}}function aS(e,t,a){var s=e.mode,c={mode:"visible",children:t},p=oS(c,s);return p.return=e,e.child=p,p}function j3(e,t,a,s){var c=e.mode,p=e.child,v={mode:"hidden",children:t},w,C;return(c&Dt)===Qe&&p!==null?(w=p,w.childLanes=ie,w.pendingProps=v,e.mode&zt&&(w.actualDuration=0,w.actualStartTime=-1,w.selfBaseDuration=0,w.treeBaseDuration=0),C=ku(a,c,s,null)):(w=oS(v,c),C=ku(a,c,s,null)),w.return=e,C.return=e,w.sibling=C,e.child=w,C}function oS(e,t,a){return QR(e,t,ie,null)}function Qk(e,t){return nd(e,t)}function _3(e,t,a,s){var c=e.child,p=c.sibling,v=Qk(c,{mode:"visible",children:a});if((t.mode&Dt)===Qe&&(v.lanes=s),v.return=t,v.sibling=null,p!==null){var w=t.deletions;w===null?(t.deletions=[p],t.flags|=fi):w.push(p)}return t.child=v,v}function L3(e,t,a,s,c){var p=t.mode,v=e.child,w=v.sibling,C={mode:"hidden",children:a},R;if((p&Dt)===Qe&&t.child!==v){var O=t.child;R=O,R.childLanes=ie,R.pendingProps=C,t.mode&zt&&(R.actualDuration=0,R.actualStartTime=-1,R.selfBaseDuration=v.selfBaseDuration,R.treeBaseDuration=v.treeBaseDuration),t.deletions=null}else R=Qk(v,C),R.subtreeFlags=v.subtreeFlags&Xn;var U;return w!==null?U=nd(w,s):(U=ku(s,p,c,null),U.flags|=Ln),U.return=t,R.return=t,R.sibling=U,t.child=R,U}function Wy(e,t,a,s){s!==null&&Fb(s),Yf(t,e.child,null,a);var c=t.pendingProps,p=c.children,v=aS(t,p);return v.flags|=Ln,t.memoizedState=null,v}function z3(e,t,a,s,c){var p=t.mode,v={mode:"visible",children:a},w=oS(v,p),C=ku(s,p,c,null);return C.flags|=Ln,w.return=t,C.return=t,w.sibling=C,t.child=w,(t.mode&Dt)!==Qe&&Yf(t,e.child,null,c),C}function N3(e,t,a){return(e.mode&Dt)===Qe?(y("Cannot hydrate Suspense in legacy mode. Switch from ReactDOM.hydrate(element, container) to ReactDOMClient.hydrateRoot(container, <App />).render(element) or remove the Suspense components from the server rendered components."),e.lanes=nt):Cb(t)?e.lanes=hr:e.lanes=mi,null}function P3(e,t,a,s,c,p,v){if(a)if(t.flags&Rn){t.flags&=~Rn;var Y=Yw(new Error("There was an error while hydrating this Suspense boundary. Switched to client rendering."));return Wy(e,t,v,Y)}else{if(t.memoizedState!==null)return t.child=e.child,t.flags|=Et,null;var ne=s.children,G=s.fallback,ve=z3(e,t,ne,G,v),ze=t.child;return ze.memoizedState=iS(v),t.memoizedState=rS,ve}else{if(TP(),(t.mode&Dt)===Qe)return Wy(e,t,v,null);if(Cb(c)){var w,C,R;{var O=UN(c);w=O.digest,C=O.message,R=O.stack}var U;C?U=new Error(C):U=new Error("The server could not finish this Suspense boundary, likely due to an error during server rendering. Switched to client rendering.");var B=Yw(U,w,R);return Wy(e,t,v,B)}var X=yi(v,e.childLanes);if(Mo||X){var Z=nx();if(Z!==null){var te=mf(Z,v);if(te!==Jn&&te!==p.retryLane){p.retryLane=te;var De=rn;ua(e,te),jr(Z,e,te,De)}}RS();var Je=Yw(new Error("This Suspense boundary received an update before it finished hydrating. This caused the boundary to switch to client rendering. The usual way to fix this is to wrap the original update in startTransition."));return Wy(e,t,v,Je)}else if(yT(c)){t.flags|=Et,t.child=e.child;var Ye=u4.bind(null,e);return HN(c,Ye),null}else{DP(t,c,p.treeContext);var Bt=s.children,_t=aS(t,Bt);return _t.flags|=zn,_t}}}function qk(e,t,a){e.lanes=xt(e.lanes,t);var s=e.alternate;s!==null&&(s.lanes=xt(s.lanes,t)),Qb(e.return,t,a)}function F3(e,t,a){for(var s=t;s!==null;){if(s.tag===le){var c=s.memoizedState;c!==null&&qk(s,a,e)}else if(s.tag===bt)qk(s,a,e);else if(s.child!==null){s.child.return=s,s=s.child;continue}if(s===e)return;for(;s.sibling===null;){if(s.return===null||s.return===e)return;s=s.return}s.sibling.return=s.return,s=s.sibling}}function B3(e){for(var t=e,a=null;t!==null;){var s=t.alternate;s!==null&&Cy(s)===null&&(a=t),t=t.sibling}return a}function I3(e){if(e!==void 0&&e!=="forwards"&&e!=="backwards"&&e!=="together"&&!Zw[e])if(Zw[e]=!0,typeof e=="string")switch(e.toLowerCase()){case"together":case"forwards":case"backwards":{y('"%s" is not a valid value for revealOrder on <SuspenseList />. Use lowercase "%s" instead.',e,e.toLowerCase());break}case"forward":case"backward":{y('"%s" is not a valid value for revealOrder on <SuspenseList />. React uses the -s suffix in the spelling. Use "%ss" instead.',e,e.toLowerCase());break}default:y('"%s" is not a supported revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',e);break}else y('%s is not a supported value for revealOrder on <SuspenseList />. Did you mean "together", "forwards" or "backwards"?',e)}function U3(e,t){e!==void 0&&!Vy[e]&&(e!=="collapsed"&&e!=="hidden"?(Vy[e]=!0,y('"%s" is not a supported value for tail on <SuspenseList />. Did you mean "collapsed" or "hidden"?',e)):t!=="forwards"&&t!=="backwards"&&(Vy[e]=!0,y('<SuspenseList tail="%s" /> is only valid if revealOrder is "forwards" or "backwards". Did you mean to specify revealOrder="forwards"?',e)))}function Xk(e,t){{var a=yt(e),s=!a&&typeof kn(e)=="function";if(a||s){var c=a?"array":"iterable";return y("A nested %s was passed to row #%s in <SuspenseList />. Wrap it in an additional SuspenseList to configure its revealOrder: <SuspenseList revealOrder=...> ... <SuspenseList revealOrder=...>{%s}</SuspenseList> ... </SuspenseList>",c,t,c),!1}}return!0}function H3(e,t){if((t==="forwards"||t==="backwards")&&e!==void 0&&e!==null&&e!==!1)if(yt(e)){for(var a=0;a<e.length;a++)if(!Xk(e[a],a))return}else{var s=kn(e);if(typeof s=="function"){var c=s.call(e);if(c)for(var p=c.next(),v=0;!p.done;p=c.next()){if(!Xk(p.value,v))return;v++}}else y('A single row was passed to a <SuspenseList revealOrder="%s" />. This is not useful since it needs multiple rows. Did you mean to pass multiple children or an array?',t)}}function lS(e,t,a,s,c){var p=e.memoizedState;p===null?e.memoizedState={isBackwards:t,rendering:null,renderingStartTime:0,last:s,tail:a,tailMode:c}:(p.isBackwards=t,p.rendering=null,p.renderingStartTime=0,p.last=s,p.tail=a,p.tailMode=c)}function Jk(e,t,a){var s=t.pendingProps,c=s.revealOrder,p=s.tail,v=s.children;I3(c),U3(p,c),H3(v,c),Pi(e,t,v,a);var w=ko.current,C=aw(w,ng);if(C)w=ow(w,ng),t.flags|=Et;else{var R=e!==null&&(e.flags&Et)!==Ke;R&&F3(t,t.child,a),w=qf(w)}if(yu(t,w),(t.mode&Dt)===Qe)t.memoizedState=null;else switch(c){case"forwards":{var O=B3(t.child),U;O===null?(U=t.child,t.child=null):(U=O.sibling,O.sibling=null),lS(t,!1,U,O,p);break}case"backwards":{var B=null,X=t.child;for(t.child=null;X!==null;){var Z=X.alternate;if(Z!==null&&Cy(Z)===null){t.child=X;break}var te=X.sibling;X.sibling=B,B=X,X=te}lS(t,!0,B,null,p);break}case"together":{lS(t,!1,null,null,void 0);break}default:t.memoizedState=null}return t.child}function V3(e,t,a){nw(t,t.stateNode.containerInfo);var s=t.pendingProps;return e===null?t.child=Yf(t,null,s,a):Pi(e,t,s,a),t.child}var Zk=!1;function W3(e,t,a){var s=t.type,c=s._context,p=t.pendingProps,v=t.memoizedProps,w=p.value;{"value"in p||Zk||(Zk=!0,y("The `value` prop is required for the `<Context.Provider>`. Did you misspell it or forget to pass it?"));var C=t.type.propTypes;C&&Co(C,p,"prop","Context.Provider")}if(WT(t,c,w),v!==null){var R=v.value;if($e(R,w)){if(v.children===p.children&&!ey())return us(e,t,a)}else IP(t,c,a)}var O=p.children;return Pi(e,t,O,a),t.child}var eR=!1;function Y3(e,t,a){var s=t.type;s._context===void 0?s!==s.Consumer&&(eR||(eR=!0,y("Rendering <Context> directly is not supported and will be removed in a future major release. Did you mean to render <Context.Consumer> instead?"))):s=s._context;var c=t.pendingProps,p=c.children;typeof p!="function"&&y("A context consumer was rendered with multiple children, or a child that isn't a function. A context consumer expects a single child that is a function. If you did pass a function, make sure there is no trailing or leading whitespace around it."),Kf(t,a);var v=vr(s);ra(t);var w;return dg.current=t,Xi(!0),w=p(v),Xi(!1),Qo(),t.flags|=yo,Pi(e,t,w,a),t.child}function hg(){Mo=!0}function Yy(e,t){(t.mode&Dt)===Qe&&e!==null&&(e.alternate=null,t.alternate=null,t.flags|=Ln)}function us(e,t,a){return e!==null&&(t.dependencies=e.dependencies),Mk(),kg(t.lanes),yi(a,t.childLanes)?(FP(e,t),t.child):null}function G3(e,t,a){{var s=t.return;if(s===null)throw new Error("Cannot swap the root fiber.");if(e.alternate=null,t.alternate=null,a.index=t.index,a.sibling=t.sibling,a.return=t.return,a.ref=t.ref,t===s.child)s.child=a;else{var c=s.child;if(c===null)throw new Error("Expected parent to have a child.");for(;c.sibling!==t;)if(c=c.sibling,c===null)throw new Error("Expected to find the previous sibling.");c.sibling=a}var p=s.deletions;return p===null?(s.deletions=[e],s.flags|=fi):p.push(e),a.flags|=Ln,a}}function sS(e,t){var a=e.lanes;return!!yi(a,t)}function K3(e,t,a){switch(t.tag){case j:Yk(t),t.stateNode,Wf();break;case L:ZT(t);break;case M:{var s=t.type;cl(s)&&ny(t);break}case H:nw(t,t.stateNode.containerInfo);break;case ae:{var c=t.memoizedProps.value,p=t.type._context;WT(t,p,c);break}case Te:{var v=yi(a,t.childLanes);v&&(t.flags|=At);{var w=t.stateNode;w.effectDuration=0,w.passiveEffectDuration=0}}break;case le:{var C=t.memoizedState;if(C!==null){if(C.dehydrated!==null)return yu(t,qf(ko.current)),t.flags|=Et,null;var R=t.child,O=R.childLanes;if(yi(a,O))return Kk(e,t,a);yu(t,qf(ko.current));var U=us(e,t,a);return U!==null?U.sibling:null}else yu(t,qf(ko.current));break}case bt:{var B=(e.flags&Et)!==Ke,X=yi(a,t.childLanes);if(B){if(X)return Jk(e,t,a);t.flags|=Et}var Z=t.memoizedState;if(Z!==null&&(Z.rendering=null,Z.tail=null,Z.lastEffect=null),yu(t,ko.current),X)break;return null}case He:case Ht:return t.lanes=ie,Hk(e,t,a)}return us(e,t,a)}function tR(e,t,a){if(t._debugNeedsRemount&&e!==null)return G3(e,t,PS(t.type,t.key,t.pendingProps,t._debugOwner||null,t.mode,t.lanes));if(e!==null){var s=e.memoizedProps,c=t.pendingProps;if(s!==c||ey()||t.type!==e.type)Mo=!0;else{var p=sS(e,a);if(!p&&(t.flags&Et)===Ke)return Mo=!1,K3(e,t,a);(e.flags&Sa)!==Ke?Mo=!0:Mo=!1}}else if(Mo=!1,qr()&&xP(t)){var v=t.index,w=bP();MT(t,w,v)}switch(t.lanes=ie,t.tag){case N:return M3(e,t,t.type,a);case ft:{var C=t.elementType;return R3(e,t,C,a)}case $:{var R=t.type,O=t.pendingProps,U=t.elementType===R?O:Do(R,O);return eS(e,t,R,U,a)}case M:{var B=t.type,X=t.pendingProps,Z=t.elementType===B?X:Do(B,X);return Wk(e,t,B,Z,a)}case j:return E3(e,t,a);case L:return T3(e,t,a);case K:return k3(e,t);case le:return Kk(e,t,a);case H:return V3(e,t,a);case ce:{var te=t.type,De=t.pendingProps,Je=t.elementType===te?De:Do(te,De);return Bk(e,t,te,Je,a)}case de:return w3(e,t,a);case Ee:return S3(e,t,a);case Te:return C3(e,t,a);case ae:return W3(e,t,a);case pe:return Y3(e,t,a);case ue:{var Ye=t.type,Bt=t.pendingProps,_t=Do(Ye,Bt);if(t.type!==t.elementType){var Y=Ye.propTypes;Y&&Co(Y,_t,"prop",Pt(Ye))}return _t=Do(Ye.type,_t),Ik(e,t,Ye,_t,a)}case Ae:return Uk(e,t,t.type,t.pendingProps,a);case Ve:{var ne=t.type,G=t.pendingProps,ve=t.elementType===ne?G:Do(ne,G);return D3(e,t,ne,ve,a)}case bt:return Jk(e,t,a);case rt:break;case He:return Hk(e,t,a)}throw new Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function np(e){e.flags|=At}function nR(e){e.flags|=qn,e.flags|=uc}var rR,uS,iR,aR;rR=function(e,t,a,s){for(var c=t.child;c!==null;){if(c.tag===L||c.tag===K)mN(e,c.stateNode);else if(c.tag!==H){if(c.child!==null){c.child.return=c,c=c.child;continue}}if(c===t)return;for(;c.sibling===null;){if(c.return===null||c.return===t)return;c=c.return}c.sibling.return=c.return,c=c.sibling}},uS=function(e,t){},iR=function(e,t,a,s,c){var p=e.memoizedProps;if(p!==s){var v=t.stateNode,w=rw(),C=yN(v,a,p,s,c,w);t.updateQueue=C,C&&np(t)}},aR=function(e,t,a,s){a!==s&&np(t)};function gg(e,t){if(!qr())switch(e.tailMode){case"hidden":{for(var a=e.tail,s=null;a!==null;)a.alternate!==null&&(s=a),a=a.sibling;s===null?e.tail=null:s.sibling=null;break}case"collapsed":{for(var c=e.tail,p=null;c!==null;)c.alternate!==null&&(p=c),c=c.sibling;p===null?!t&&e.tail!==null?e.tail.sibling=null:e.tail=null:p.sibling=null;break}}}function Jr(e){var t=e.alternate!==null&&e.alternate.child===e.child,a=ie,s=Ke;if(t){if((e.mode&zt)!==Qe){for(var C=e.selfBaseDuration,R=e.child;R!==null;)a=xt(a,xt(R.lanes,R.childLanes)),s|=R.subtreeFlags&Xn,s|=R.flags&Xn,C+=R.treeBaseDuration,R=R.sibling;e.treeBaseDuration=C}else for(var O=e.child;O!==null;)a=xt(a,xt(O.lanes,O.childLanes)),s|=O.subtreeFlags&Xn,s|=O.flags&Xn,O.return=e,O=O.sibling;e.subtreeFlags|=s}else{if((e.mode&zt)!==Qe){for(var c=e.actualDuration,p=e.selfBaseDuration,v=e.child;v!==null;)a=xt(a,xt(v.lanes,v.childLanes)),s|=v.subtreeFlags,s|=v.flags,c+=v.actualDuration,p+=v.treeBaseDuration,v=v.sibling;e.actualDuration=c,e.treeBaseDuration=p}else for(var w=e.child;w!==null;)a=xt(a,xt(w.lanes,w.childLanes)),s|=w.subtreeFlags,s|=w.flags,w.return=e,w=w.sibling;e.subtreeFlags|=s}return e.childLanes=a,t}function Q3(e,t,a){if(jP()&&(t.mode&Dt)!==Qe&&(t.flags&Et)===Ke)return zT(t),Wf(),t.flags|=Rn|Nl|Pr,!1;var s=ly(t);if(a!==null&&a.dehydrated!==null)if(e===null){if(!s)throw new Error("A dehydrated suspense component was completed without a hydrated node. This is probably a bug in React.");if(OP(t),Jr(t),(t.mode&zt)!==Qe){var c=a!==null;if(c){var p=t.child;p!==null&&(t.treeBaseDuration-=p.treeBaseDuration)}}return!1}else{if(Wf(),(t.flags&Et)===Ke&&(t.memoizedState=null),t.flags|=At,Jr(t),(t.mode&zt)!==Qe){var v=a!==null;if(v){var w=t.child;w!==null&&(t.treeBaseDuration-=w.treeBaseDuration)}}return!1}else return NT(),!0}function oR(e,t,a){var s=t.pendingProps;switch(_b(t),t.tag){case N:case ft:case Ae:case $:case ce:case de:case Ee:case Te:case pe:case ue:return Jr(t),null;case M:{var c=t.type;return cl(c)&&ty(t),Jr(t),null}case j:{var p=t.stateNode;if(Qf(t),$b(t),sw(),p.pendingContext&&(p.context=p.pendingContext,p.pendingContext=null),e===null||e.child===null){var v=ly(t);if(v)np(t);else if(e!==null){var w=e.memoizedState;(!w.isDehydrated||(t.flags&Rn)!==Ke)&&(t.flags|=na,NT())}}return uS(e,t),Jr(t),null}case L:{iw(t);var C=JT(),R=t.type;if(e!==null&&t.stateNode!=null)iR(e,t,R,s,C),e.ref!==t.ref&&nR(t);else{if(!s){if(t.stateNode===null)throw new Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");return Jr(t),null}var O=rw(),U=ly(t);if(U)MP(t,C,O)&&np(t);else{var B=gN(R,s,C,O,t);rR(B,t,!1,!1),t.stateNode=B,vN(B,R,s,C)&&np(t)}t.ref!==null&&nR(t)}return Jr(t),null}case K:{var X=s;if(e&&t.stateNode!=null){var Z=e.memoizedProps;aR(e,t,Z,X)}else{if(typeof X!="string"&&t.stateNode===null)throw new Error("We must have new props for new mounts. This error is likely caused by a bug in React. Please file an issue.");var te=JT(),De=rw(),Je=ly(t);Je?$P(t)&&np(t):t.stateNode=xN(X,te,De,t)}return Jr(t),null}case le:{Xf(t);var Ye=t.memoizedState;if(e===null||e.memoizedState!==null&&e.memoizedState.dehydrated!==null){var Bt=Q3(e,t,Ye);if(!Bt)return t.flags&Pr?t:null}if((t.flags&Et)!==Ke)return t.lanes=a,(t.mode&zt)!==Qe&&jw(t),t;var _t=Ye!==null,Y=e!==null&&e.memoizedState!==null;if(_t!==Y&&_t){var ne=t.child;if(ne.flags|=Ua,(t.mode&Dt)!==Qe){var G=e===null&&(t.memoizedProps.unstable_avoidThisFallback!==!0||!0);G||aw(ko.current,tk)?q5():RS()}}var ve=t.updateQueue;if(ve!==null&&(t.flags|=At),Jr(t),(t.mode&zt)!==Qe&&_t){var ze=t.child;ze!==null&&(t.treeBaseDuration-=ze.treeBaseDuration)}return null}case H:return Qf(t),uS(e,t),e===null&&fP(t.stateNode.containerInfo),Jr(t),null;case ae:var Oe=t.type._context;return Kb(Oe,t),Jr(t),null;case Ve:{var ot=t.type;return cl(ot)&&ty(t),Jr(t),null}case bt:{Xf(t);var dt=t.memoizedState;if(dt===null)return Jr(t),null;var fn=(t.flags&Et)!==Ke,Wt=dt.rendering;if(Wt===null)if(fn)gg(dt,!1);else{var lr=J5()&&(e===null||(e.flags&Et)===Ke);if(!lr)for(var Yt=t.child;Yt!==null;){var er=Cy(Yt);if(er!==null){fn=!0,t.flags|=Et,gg(dt,!1);var Ei=er.updateQueue;return Ei!==null&&(t.updateQueue=Ei,t.flags|=At),t.subtreeFlags=Ke,BP(t,a),yu(t,ow(ko.current,ng)),t.child}Yt=Yt.sibling}dt.tail!==null&&In()>RR()&&(t.flags|=Et,fn=!0,gg(dt,!1),t.lanes=av)}else{if(!fn){var ri=Cy(Wt);if(ri!==null){t.flags|=Et,fn=!0;var Ma=ri.updateQueue;if(Ma!==null&&(t.updateQueue=Ma,t.flags|=At),gg(dt,!0),dt.tail===null&&dt.tailMode==="hidden"&&!Wt.alternate&&!qr())return Jr(t),null}else In()*2-dt.renderingStartTime>RR()&&a!==mi&&(t.flags|=Et,fn=!0,gg(dt,!1),t.lanes=av)}if(dt.isBackwards)Wt.sibling=t.child,t.child=Wt;else{var Ii=dt.last;Ii!==null?Ii.sibling=Wt:t.child=Wt,dt.last=Wt}}if(dt.tail!==null){var Ui=dt.tail;dt.rendering=Ui,dt.tail=Ui.sibling,dt.renderingStartTime=In(),Ui.sibling=null;var Ti=ko.current;return fn?Ti=ow(Ti,ng):Ti=qf(Ti),yu(t,Ti),Ui}return Jr(t),null}case rt:break;case He:case Ht:{kS(t);var hs=t.memoizedState,cp=hs!==null;if(e!==null){var Og=e.memoizedState,yl=Og!==null;yl!==cp&&!V&&(t.flags|=Ua)}return!cp||(t.mode&Dt)===Qe?Jr(t):yi(vl,mi)&&(Jr(t),t.subtreeFlags&(Ln|At)&&(t.flags|=Ua)),null}case kt:return null;case pt:return null}throw new Error("Unknown unit of work tag ("+t.tag+"). This error is likely caused by a bug in React. Please file an issue.")}function q3(e,t,a){switch(_b(t),t.tag){case M:{var s=t.type;cl(s)&&ty(t);var c=t.flags;return c&Pr?(t.flags=c&~Pr|Et,(t.mode&zt)!==Qe&&jw(t),t):null}case j:{t.stateNode,Qf(t),$b(t),sw();var p=t.flags;return(p&Pr)!==Ke&&(p&Et)===Ke?(t.flags=p&~Pr|Et,t):null}case L:return iw(t),null;case le:{Xf(t);var v=t.memoizedState;if(v!==null&&v.dehydrated!==null){if(t.alternate===null)throw new Error("Threw in newly mounted dehydrated component. This is likely a bug in React. Please file an issue.");Wf()}var w=t.flags;return w&Pr?(t.flags=w&~Pr|Et,(t.mode&zt)!==Qe&&jw(t),t):null}case bt:return Xf(t),null;case H:return Qf(t),null;case ae:var C=t.type._context;return Kb(C,t),null;case He:case Ht:return kS(t),null;case kt:return null;default:return null}}function lR(e,t,a){switch(_b(t),t.tag){case M:{var s=t.type.childContextTypes;s!=null&&ty(t);break}case j:{t.stateNode,Qf(t),$b(t),sw();break}case L:{iw(t);break}case H:Qf(t);break;case le:Xf(t);break;case bt:Xf(t);break;case ae:var c=t.type._context;Kb(c,t);break;case He:case Ht:kS(t);break}}var sR=null;sR=new Set;var Gy=!1,Zr=!1,X3=typeof WeakSet=="function"?WeakSet:Set,Be=null,rp=null,ip=null;function J3(e){ta(null,function(){throw e}),Yp()}var Z3=function(e,t){if(t.props=e.memoizedProps,t.state=e.memoizedState,e.mode&zt)try{gl(),t.componentWillUnmount()}finally{hl(e)}else t.componentWillUnmount()};function uR(e,t){try{wu(Rr,e)}catch(a){Tn(e,t,a)}}function cS(e,t,a){try{Z3(e,a)}catch(s){Tn(e,t,s)}}function e5(e,t,a){try{a.componentDidMount()}catch(s){Tn(e,t,s)}}function cR(e,t){try{fR(e)}catch(a){Tn(e,t,a)}}function ap(e,t){var a=e.ref;if(a!==null)if(typeof a=="function"){var s;try{if(it&&ht&&e.mode&zt)try{gl(),s=a(null)}finally{hl(e)}else s=a(null)}catch(c){Tn(e,t,c)}typeof s=="function"&&y("Unexpected return value from a callback ref in %s. A callback ref should not return a function.",lt(e))}else a.current=null}function Ky(e,t,a){try{a()}catch(s){Tn(e,t,s)}}var dR=!1;function t5(e,t){pN(e.containerInfo),Be=t,n5();var a=dR;return dR=!1,a}function n5(){for(;Be!==null;){var e=Be,t=e.child;(e.subtreeFlags&Yo)!==Ke&&t!==null?(t.return=e,Be=t):r5()}}function r5(){for(;Be!==null;){var e=Be;ln(e);try{i5(e)}catch(a){Tn(e,e.return,a)}_n();var t=e.sibling;if(t!==null){t.return=e.return,Be=t;return}Be=e.return}}function i5(e){var t=e.alternate,a=e.flags;if((a&na)!==Ke){switch(ln(e),e.tag){case $:case ce:case Ae:break;case M:{if(t!==null){var s=t.memoizedProps,c=t.memoizedState,p=e.stateNode;e.type===e.elementType&&!Xc&&(p.props!==e.memoizedProps&&y("Expected %s props to match memoized props before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(e)||"instance"),p.state!==e.memoizedState&&y("Expected %s state to match memoized state before getSnapshotBeforeUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(e)||"instance"));var v=p.getSnapshotBeforeUpdate(e.elementType===e.type?s:Do(e.type,s),c);{var w=sR;v===void 0&&!w.has(e.type)&&(w.add(e.type),y("%s.getSnapshotBeforeUpdate(): A snapshot value (or null) must be returned. You have returned undefined.",lt(e)))}p.__reactInternalSnapshotBeforeUpdate=v}break}case j:{{var C=e.stateNode;PN(C.containerInfo)}break}case L:case K:case H:case Ve:break;default:throw new Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}_n()}}function $o(e,t,a){var s=t.updateQueue,c=s!==null?s.lastEffect:null;if(c!==null){var p=c.next,v=p;do{if((v.tag&e)===e){var w=v.destroy;v.destroy=void 0,w!==void 0&&((e&Xr)!==ca?qo(t):(e&Rr)!==ca&&ih(t),(e&dl)!==ca&&Dg(!0),Ky(t,a,w),(e&dl)!==ca&&Dg(!1),(e&Xr)!==ca?Ud():(e&Rr)!==ca&&Gs())}v=v.next}while(v!==p)}}function wu(e,t){var a=t.updateQueue,s=a!==null?a.lastEffect:null;if(s!==null){var c=s.next,p=c;do{if((p.tag&e)===e){(e&Xr)!==ca?rv(t):(e&Rr)!==ca&&iv(t);var v=p.create;(e&dl)!==ca&&Dg(!0),p.destroy=v(),(e&dl)!==ca&&Dg(!1),(e&Xr)!==ca?bo():(e&Rr)!==ca&&Hd();{var w=p.destroy;if(w!==void 0&&typeof w!="function"){var C=void 0;(p.tag&Rr)!==Ke?C="useLayoutEffect":(p.tag&dl)!==Ke?C="useInsertionEffect":C="useEffect";var R=void 0;w===null?R=" You returned null. If your effect does not require clean up, return undefined (or nothing).":typeof w.then=="function"?R=`

It looks like you wrote `+C+`(async () => ...) or returned a Promise. Instead, write the async function inside your effect and call it immediately:

`+C+`(() => {
  async function fetchData() {
    // You can await here
    const response = await MyAPI.getData(someId);
    // ...
  }
  fetchData();
}, [someId]); // Or [] if effect doesn't need props or state

Learn more about data fetching with Hooks: https://reactjs.org/link/hooks-data-fetching`:R=" You returned: "+w,y("%s must not return anything besides a function, which is used for clean-up.%s",C,R)}}}p=p.next}while(p!==c)}}function a5(e,t){if((t.flags&At)!==Ke)switch(t.tag){case Te:{var a=t.stateNode.passiveEffectDuration,s=t.memoizedProps,c=s.id,p=s.onPostCommit,v=Rk(),w=t.alternate===null?"mount":"update";kk()&&(w="nested-update"),typeof p=="function"&&p(c,w,a,v);var C=t.return;e:for(;C!==null;){switch(C.tag){case j:var R=C.stateNode;R.passiveEffectDuration+=a;break e;case Te:var O=C.stateNode;O.passiveEffectDuration+=a;break e}C=C.return}break}}}function o5(e,t,a,s){if((a.flags&Go)!==Ke)switch(a.tag){case $:case ce:case Ae:{if(!Zr)if(a.mode&zt)try{gl(),wu(Rr|kr,a)}finally{hl(a)}else wu(Rr|kr,a);break}case M:{var c=a.stateNode;if(a.flags&At&&!Zr)if(t===null)if(a.type===a.elementType&&!Xc&&(c.props!==a.memoizedProps&&y("Expected %s props to match memoized props before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&y("Expected %s state to match memoized state before componentDidMount. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),a.mode&zt)try{gl(),c.componentDidMount()}finally{hl(a)}else c.componentDidMount();else{var p=a.elementType===a.type?t.memoizedProps:Do(a.type,t.memoizedProps),v=t.memoizedState;if(a.type===a.elementType&&!Xc&&(c.props!==a.memoizedProps&&y("Expected %s props to match memoized props before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&y("Expected %s state to match memoized state before componentDidUpdate. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),a.mode&zt)try{gl(),c.componentDidUpdate(p,v,c.__reactInternalSnapshotBeforeUpdate)}finally{hl(a)}else c.componentDidUpdate(p,v,c.__reactInternalSnapshotBeforeUpdate)}var w=a.updateQueue;w!==null&&(a.type===a.elementType&&!Xc&&(c.props!==a.memoizedProps&&y("Expected %s props to match memoized props before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.props`. Please file an issue.",lt(a)||"instance"),c.state!==a.memoizedState&&y("Expected %s state to match memoized state before processing the update queue. This might either be because of a bug in React, or because a component reassigns its own `this.state`. Please file an issue.",lt(a)||"instance")),XT(a,w,c));break}case j:{var C=a.updateQueue;if(C!==null){var R=null;if(a.child!==null)switch(a.child.tag){case L:R=a.child.stateNode;break;case M:R=a.child.stateNode;break}XT(a,C,R)}break}case L:{var O=a.stateNode;if(t===null&&a.flags&At){var U=a.type,B=a.memoizedProps;EN(O,U,B)}break}case K:break;case H:break;case Te:{{var X=a.memoizedProps,Z=X.onCommit,te=X.onRender,De=a.stateNode.effectDuration,Je=Rk(),Ye=t===null?"mount":"update";kk()&&(Ye="nested-update"),typeof te=="function"&&te(a.memoizedProps.id,Ye,a.actualDuration,a.treeBaseDuration,a.actualStartTime,Je);{typeof Z=="function"&&Z(a.memoizedProps.id,Ye,De,Je),r4(a);var Bt=a.return;e:for(;Bt!==null;){switch(Bt.tag){case j:var _t=Bt.stateNode;_t.effectDuration+=De;break e;case Te:var Y=Bt.stateNode;Y.effectDuration+=De;break e}Bt=Bt.return}}}break}case le:{h5(e,a);break}case bt:case Ve:case rt:case He:case Ht:case pt:break;default:throw new Error("This unit of work tag should not have side-effects. This error is likely caused by a bug in React. Please file an issue.")}Zr||a.flags&qn&&fR(a)}function l5(e){switch(e.tag){case $:case ce:case Ae:{if(e.mode&zt)try{gl(),uR(e,e.return)}finally{hl(e)}else uR(e,e.return);break}case M:{var t=e.stateNode;typeof t.componentDidMount=="function"&&e5(e,e.return,t),cR(e,e.return);break}case L:{cR(e,e.return);break}}}function s5(e,t){for(var a=null,s=e;;){if(s.tag===L){if(a===null){a=s;try{var c=s.stateNode;t?_N(c):zN(s.stateNode,s.memoizedProps)}catch(v){Tn(e,e.return,v)}}}else if(s.tag===K){if(a===null)try{var p=s.stateNode;t?LN(p):NN(p,s.memoizedProps)}catch(v){Tn(e,e.return,v)}}else if(!((s.tag===He||s.tag===Ht)&&s.memoizedState!==null&&s!==e)){if(s.child!==null){s.child.return=s,s=s.child;continue}}if(s===e)return;for(;s.sibling===null;){if(s.return===null||s.return===e)return;a===s&&(a=null),s=s.return}a===s&&(a=null),s.sibling.return=s.return,s=s.sibling}}function fR(e){var t=e.ref;if(t!==null){var a=e.stateNode,s;switch(e.tag){case L:s=a;break;default:s=a}if(typeof t=="function"){var c;if(e.mode&zt)try{gl(),c=t(s)}finally{hl(e)}else c=t(s);typeof c=="function"&&y("Unexpected return value from a callback ref in %s. A callback ref should not return a function.",lt(e))}else t.hasOwnProperty("current")||y("Unexpected ref object provided for %s. Use either a ref-setter function or React.createRef().",lt(e)),t.current=s}}function u5(e){var t=e.alternate;t!==null&&(t.return=null),e.return=null}function pR(e){var t=e.alternate;t!==null&&(e.alternate=null,pR(t));{if(e.child=null,e.deletions=null,e.sibling=null,e.tag===L){var a=e.stateNode;a!==null&&gP(a)}e.stateNode=null,e._debugOwner=null,e.return=null,e.dependencies=null,e.memoizedProps=null,e.memoizedState=null,e.pendingProps=null,e.stateNode=null,e.updateQueue=null}}function c5(e){for(var t=e.return;t!==null;){if(hR(t))return t;t=t.return}throw new Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.")}function hR(e){return e.tag===L||e.tag===j||e.tag===H}function gR(e){var t=e;e:for(;;){for(;t.sibling===null;){if(t.return===null||hR(t.return))return null;t=t.return}for(t.sibling.return=t.return,t=t.sibling;t.tag!==L&&t.tag!==K&&t.tag!==Tt;){if(t.flags&Ln||t.child===null||t.tag===H)continue e;t.child.return=t,t=t.child}if(!(t.flags&Ln))return t.stateNode}}function d5(e){var t=c5(e);switch(t.tag){case L:{var a=t.stateNode;t.flags&tn&&(vT(a),t.flags&=~tn);var s=gR(e);fS(e,s,a);break}case j:case H:{var c=t.stateNode.containerInfo,p=gR(e);dS(e,p,c);break}default:throw new Error("Invalid host parent fiber. This error is likely caused by a bug in React. Please file an issue.")}}function dS(e,t,a){var s=e.tag,c=s===L||s===K;if(c){var p=e.stateNode;t?$N(a,p,t):DN(a,p)}else if(s!==H){var v=e.child;if(v!==null){dS(v,t,a);for(var w=v.sibling;w!==null;)dS(w,t,a),w=w.sibling}}}function fS(e,t,a){var s=e.tag,c=s===L||s===K;if(c){var p=e.stateNode;t?MN(a,p,t):RN(a,p)}else if(s!==H){var v=e.child;if(v!==null){fS(v,t,a);for(var w=v.sibling;w!==null;)fS(w,t,a),w=w.sibling}}}var ei=null,Oo=!1;function f5(e,t,a){{var s=t;e:for(;s!==null;){switch(s.tag){case L:{ei=s.stateNode,Oo=!1;break e}case j:{ei=s.stateNode.containerInfo,Oo=!0;break e}case H:{ei=s.stateNode.containerInfo,Oo=!0;break e}}s=s.return}if(ei===null)throw new Error("Expected to find a host parent. This error is likely caused by a bug in React. Please file an issue.");mR(e,t,a),ei=null,Oo=!1}u5(a)}function Su(e,t,a){for(var s=a.child;s!==null;)mR(e,t,s),s=s.sibling}function mR(e,t,a){switch(Ys(a),a.tag){case L:Zr||ap(a,t);case K:{{var s=ei,c=Oo;ei=null,Su(e,t,a),ei=s,Oo=c,ei!==null&&(Oo?AN(ei,a.stateNode):ON(ei,a.stateNode))}return}case Tt:{ei!==null&&(Oo?jN(ei,a.stateNode):Sb(ei,a.stateNode));return}case H:{{var p=ei,v=Oo;ei=a.stateNode.containerInfo,Oo=!0,Su(e,t,a),ei=p,Oo=v}return}case $:case ce:case ue:case Ae:{if(!Zr){var w=a.updateQueue;if(w!==null){var C=w.lastEffect;if(C!==null){var R=C.next,O=R;do{var U=O,B=U.destroy,X=U.tag;B!==void 0&&((X&dl)!==ca?Ky(a,t,B):(X&Rr)!==ca&&(ih(a),a.mode&zt?(gl(),Ky(a,t,B),hl(a)):Ky(a,t,B),Gs())),O=O.next}while(O!==R)}}}Su(e,t,a);return}case M:{if(!Zr){ap(a,t);var Z=a.stateNode;typeof Z.componentWillUnmount=="function"&&cS(a,t,Z)}Su(e,t,a);return}case rt:{Su(e,t,a);return}case He:{if(a.mode&Dt){var te=Zr;Zr=te||a.memoizedState!==null,Su(e,t,a),Zr=te}else Su(e,t,a);break}default:{Su(e,t,a);return}}}function p5(e){e.memoizedState}function h5(e,t){var a=t.memoizedState;if(a===null){var s=t.alternate;if(s!==null){var c=s.memoizedState;if(c!==null){var p=c.dehydrated;p!==null&&JN(p)}}}}function vR(e){var t=e.updateQueue;if(t!==null){e.updateQueue=null;var a=e.stateNode;a===null&&(a=e.stateNode=new X3),t.forEach(function(s){var c=c4.bind(null,e,s);if(!a.has(s)){if(a.add(s),Fr)if(rp!==null&&ip!==null)Rg(ip,rp);else throw Error("Expected finished root and lanes to be set. This is a bug in React.");s.then(c,c)}})}}function g5(e,t,a){rp=a,ip=e,ln(t),yR(t,e),ln(t),rp=null,ip=null}function Ao(e,t,a){var s=t.deletions;if(s!==null)for(var c=0;c<s.length;c++){var p=s[c];try{f5(e,t,p)}catch(C){Tn(p,t,C)}}var v=ya();if(t.subtreeFlags&Vs)for(var w=t.child;w!==null;)ln(w),yR(w,e),w=w.sibling;ln(v)}function yR(e,t,a){var s=e.alternate,c=e.flags;switch(e.tag){case $:case ce:case ue:case Ae:{if(Ao(t,e),ml(e),c&At){try{$o(dl|kr,e,e.return),wu(dl|kr,e)}catch(ot){Tn(e,e.return,ot)}if(e.mode&zt){try{gl(),$o(Rr|kr,e,e.return)}catch(ot){Tn(e,e.return,ot)}hl(e)}else try{$o(Rr|kr,e,e.return)}catch(ot){Tn(e,e.return,ot)}}return}case M:{Ao(t,e),ml(e),c&qn&&s!==null&&ap(s,s.return);return}case L:{Ao(t,e),ml(e),c&qn&&s!==null&&ap(s,s.return);{if(e.flags&tn){var p=e.stateNode;try{vT(p)}catch(ot){Tn(e,e.return,ot)}}if(c&At){var v=e.stateNode;if(v!=null){var w=e.memoizedProps,C=s!==null?s.memoizedProps:w,R=e.type,O=e.updateQueue;if(e.updateQueue=null,O!==null)try{TN(v,O,R,C,w,e)}catch(ot){Tn(e,e.return,ot)}}}}return}case K:{if(Ao(t,e),ml(e),c&At){if(e.stateNode===null)throw new Error("This should have a text node initialized. This error is likely caused by a bug in React. Please file an issue.");var U=e.stateNode,B=e.memoizedProps,X=s!==null?s.memoizedProps:B;try{kN(U,X,B)}catch(ot){Tn(e,e.return,ot)}}return}case j:{if(Ao(t,e),ml(e),c&At&&s!==null){var Z=s.memoizedState;if(Z.isDehydrated)try{XN(t.containerInfo)}catch(ot){Tn(e,e.return,ot)}}return}case H:{Ao(t,e),ml(e);return}case le:{Ao(t,e),ml(e);var te=e.child;if(te.flags&Ua){var De=te.stateNode,Je=te.memoizedState,Ye=Je!==null;if(De.isHidden=Ye,Ye){var Bt=te.alternate!==null&&te.alternate.memoizedState!==null;Bt||Q5()}}if(c&At){try{p5(e)}catch(ot){Tn(e,e.return,ot)}vR(e)}return}case He:{var _t=s!==null&&s.memoizedState!==null;if(e.mode&Dt){var Y=Zr;Zr=Y||_t,Ao(t,e),Zr=Y}else Ao(t,e);if(ml(e),c&Ua){var ne=e.stateNode,G=e.memoizedState,ve=G!==null,ze=e;if(ne.isHidden=ve,ve&&!_t&&(ze.mode&Dt)!==Qe){Be=ze;for(var Oe=ze.child;Oe!==null;)Be=Oe,v5(Oe),Oe=Oe.sibling}s5(ze,ve)}return}case bt:{Ao(t,e),ml(e),c&At&&vR(e);return}case rt:return;default:{Ao(t,e),ml(e);return}}}function ml(e){var t=e.flags;if(t&Ln){try{d5(e)}catch(a){Tn(e,e.return,a)}e.flags&=~Ln}t&zn&&(e.flags&=~zn)}function m5(e,t,a){rp=a,ip=t,Be=e,xR(e,t,a),rp=null,ip=null}function xR(e,t,a){for(var s=(e.mode&Dt)!==Qe;Be!==null;){var c=Be,p=c.child;if(c.tag===He&&s){var v=c.memoizedState!==null,w=v||Gy;if(w){pS(e,t,a);continue}else{var C=c.alternate,R=C!==null&&C.memoizedState!==null,O=R||Zr,U=Gy,B=Zr;Gy=w,Zr=O,Zr&&!B&&(Be=c,y5(c));for(var X=p;X!==null;)Be=X,xR(X,t,a),X=X.sibling;Be=c,Gy=U,Zr=B,pS(e,t,a);continue}}(c.subtreeFlags&Go)!==Ke&&p!==null?(p.return=c,Be=p):pS(e,t,a)}}function pS(e,t,a){for(;Be!==null;){var s=Be;if((s.flags&Go)!==Ke){var c=s.alternate;ln(s);try{o5(t,c,s,a)}catch(v){Tn(s,s.return,v)}_n()}if(s===e){Be=null;return}var p=s.sibling;if(p!==null){p.return=s.return,Be=p;return}Be=s.return}}function v5(e){for(;Be!==null;){var t=Be,a=t.child;switch(t.tag){case $:case ce:case ue:case Ae:{if(t.mode&zt)try{gl(),$o(Rr,t,t.return)}finally{hl(t)}else $o(Rr,t,t.return);break}case M:{ap(t,t.return);var s=t.stateNode;typeof s.componentWillUnmount=="function"&&cS(t,t.return,s);break}case L:{ap(t,t.return);break}case He:{var c=t.memoizedState!==null;if(c){bR(e);continue}break}}a!==null?(a.return=t,Be=a):bR(e)}}function bR(e){for(;Be!==null;){var t=Be;if(t===e){Be=null;return}var a=t.sibling;if(a!==null){a.return=t.return,Be=a;return}Be=t.return}}function y5(e){for(;Be!==null;){var t=Be,a=t.child;if(t.tag===He){var s=t.memoizedState!==null;if(s){wR(e);continue}}a!==null?(a.return=t,Be=a):wR(e)}}function wR(e){for(;Be!==null;){var t=Be;ln(t);try{l5(t)}catch(s){Tn(t,t.return,s)}if(_n(),t===e){Be=null;return}var a=t.sibling;if(a!==null){a.return=t.return,Be=a;return}Be=t.return}}function x5(e,t,a,s){Be=t,b5(t,e,a,s)}function b5(e,t,a,s){for(;Be!==null;){var c=Be,p=c.child;(c.subtreeFlags&wr)!==Ke&&p!==null?(p.return=c,Be=p):w5(e,t,a,s)}}function w5(e,t,a,s){for(;Be!==null;){var c=Be;if((c.flags&Ai)!==Ke){ln(c);try{S5(t,c,a,s)}catch(v){Tn(c,c.return,v)}_n()}if(c===e){Be=null;return}var p=c.sibling;if(p!==null){p.return=c.return,Be=p;return}Be=c.return}}function S5(e,t,a,s){switch(t.tag){case $:case ce:case Ae:{if(t.mode&zt){Aw();try{wu(Xr|kr,t)}finally{Ow(t)}}else wu(Xr|kr,t);break}}}function C5(e){Be=e,E5()}function E5(){for(;Be!==null;){var e=Be,t=e.child;if((Be.flags&fi)!==Ke){var a=e.deletions;if(a!==null){for(var s=0;s<a.length;s++){var c=a[s];Be=c,R5(c,e)}{var p=e.alternate;if(p!==null){var v=p.child;if(v!==null){p.child=null;do{var w=v.sibling;v.sibling=null,v=w}while(v!==null)}}}Be=e}}(e.subtreeFlags&wr)!==Ke&&t!==null?(t.return=e,Be=t):T5()}}function T5(){for(;Be!==null;){var e=Be;(e.flags&Ai)!==Ke&&(ln(e),k5(e),_n());var t=e.sibling;if(t!==null){t.return=e.return,Be=t;return}Be=e.return}}function k5(e){switch(e.tag){case $:case ce:case Ae:{e.mode&zt?(Aw(),$o(Xr|kr,e,e.return),Ow(e)):$o(Xr|kr,e,e.return);break}}}function R5(e,t){for(;Be!==null;){var a=Be;ln(a),M5(a,t),_n();var s=a.child;s!==null?(s.return=a,Be=s):D5(e)}}function D5(e){for(;Be!==null;){var t=Be,a=t.sibling,s=t.return;if(pR(t),t===e){Be=null;return}if(a!==null){a.return=s,Be=a;return}Be=s}}function M5(e,t){switch(e.tag){case $:case ce:case Ae:{e.mode&zt?(Aw(),$o(Xr,e,t),Ow(e)):$o(Xr,e,t);break}}}function $5(e){switch(e.tag){case $:case ce:case Ae:{try{wu(Rr|kr,e)}catch(a){Tn(e,e.return,a)}break}case M:{var t=e.stateNode;try{t.componentDidMount()}catch(a){Tn(e,e.return,a)}break}}}function O5(e){switch(e.tag){case $:case ce:case Ae:{try{wu(Xr|kr,e)}catch(t){Tn(e,e.return,t)}break}}}function A5(e){switch(e.tag){case $:case ce:case Ae:{try{$o(Rr|kr,e,e.return)}catch(a){Tn(e,e.return,a)}break}case M:{var t=e.stateNode;typeof t.componentWillUnmount=="function"&&cS(e,e.return,t);break}}}function j5(e){switch(e.tag){case $:case ce:case Ae:try{$o(Xr|kr,e,e.return)}catch(t){Tn(e,e.return,t)}}}if(typeof Symbol=="function"&&Symbol.for){var mg=Symbol.for;mg("selector.component"),mg("selector.has_pseudo_class"),mg("selector.role"),mg("selector.test_id"),mg("selector.text")}var _5=[];function L5(){_5.forEach(function(e){return e()})}var z5=d.ReactCurrentActQueue;function N5(e){{var t=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0,a=typeof jest<"u";return a&&t!==!1}}function SR(){{var e=typeof IS_REACT_ACT_ENVIRONMENT<"u"?IS_REACT_ACT_ENVIRONMENT:void 0;return!e&&z5.current!==null&&y("The current testing environment is not configured to support act(...)"),e}}var P5=Math.ceil,hS=d.ReactCurrentDispatcher,gS=d.ReactCurrentOwner,ti=d.ReactCurrentBatchConfig,jo=d.ReactCurrentActQueue,$r=0,CR=1,ni=2,Za=4,cs=0,vg=1,Jc=2,Qy=3,yg=4,ER=5,mS=6,Ft=$r,Fi=null,Wn=null,Or=ie,vl=ie,vS=fu(ie),Ar=cs,xg=null,qy=ie,bg=ie,Xy=ie,wg=null,da=null,yS=0,TR=500,kR=1/0,F5=500,ds=null;function Sg(){kR=In()+F5}function RR(){return kR}var Jy=!1,xS=null,op=null,Zc=!1,Cu=null,Cg=ie,bS=[],wS=null,B5=50,Eg=0,SS=null,CS=!1,Zy=!1,I5=50,lp=0,ex=null,Tg=rn,tx=ie,DR=!1;function nx(){return Fi}function Bi(){return(Ft&(ni|Za))!==$r?In():(Tg!==rn||(Tg=In()),Tg)}function Eu(e){var t=e.mode;if((t&Dt)===Qe)return nt;if((Ft&ni)!==$r&&Or!==ie)return gr(Or);var a=zP()!==LP;if(a){if(ti.transition!==null){var s=ti.transition;s._updatedFibers||(s._updatedFibers=new Set),s._updatedFibers.add(e)}return tx===Jn&&(tx=hh()),tx}var c=_i();if(c!==Jn)return c;var p=bN();return p}function U5(e){var t=e.mode;return(t&Dt)===Qe?nt:dv()}function jr(e,t,a,s){f4(),DR&&y("useInsertionEffect must not schedule updates."),CS&&(Zy=!0),Zs(e,a,s),(Ft&ni)!==ie&&e===Fi?g4(t):(Fr&&pv(e,t,a),m4(t),e===Fi&&((Ft&ni)===$r&&(bg=xt(bg,a)),Ar===yg&&Tu(e,Or)),fa(e,s),a===nt&&Ft===$r&&(t.mode&Dt)===Qe&&!jo.isBatchingLegacy&&(Sg(),DT()))}function H5(e,t,a){var s=e.current;s.lanes=t,Zs(e,t,a),fa(e,a)}function V5(e){return(Ft&ni)!==$r}function fa(e,t){var a=e.callbackNode;sv(e,t);var s=vi(e,e===Fi?Or:ie);if(s===ie){a!==null&&VR(a),e.callbackNode=null,e.callbackPriority=Jn;return}var c=Wl(s),p=e.callbackPriority;if(p===c&&!(jo.current!==null&&a!==$S)){a==null&&p!==nt&&y("Expected scheduled callback to exist. This error is likely caused by a bug in React. Please file an issue.");return}a!=null&&VR(a);var v;if(c===nt)e.tag===pu?(jo.isBatchingLegacy!==null&&(jo.didScheduleLegacyUpdate=!0),yP(OR.bind(null,e))):RT(OR.bind(null,e)),jo.current!==null?jo.current.push(hu):SN(function(){(Ft&(ni|Za))===$r&&hu()}),v=null;else{var w;switch(gv(s)){case xi:w=xo;break;case ia:w=cc;break;case Cr:w=Fl;break;case yf:w=Ws;break;default:w=Fl;break}v=OS(w,MR.bind(null,e))}e.callbackPriority=c,e.callbackNode=v}function MR(e,t){if(s3(),Tg=rn,tx=ie,(Ft&(ni|Za))!==$r)throw new Error("Should not already be working.");var a=e.callbackNode,s=ps();if(s&&e.callbackNode!==a)return null;var c=vi(e,e===Fi?Or:ie);if(c===ie)return null;var p=!Sc(e,c)&&!cv(e,c)&&!t,v=p?e4(e,c):ix(e,c);if(v!==cs){if(v===Jc){var w=cf(e);w!==ie&&(c=w,v=ES(e,w))}if(v===vg){var C=xg;throw ed(e,ie),Tu(e,c),fa(e,In()),C}if(v===mS)Tu(e,c);else{var R=!Sc(e,c),O=e.current.alternate;if(R&&!Y5(O)){if(v=ix(e,c),v===Jc){var U=cf(e);U!==ie&&(c=U,v=ES(e,U))}if(v===vg){var B=xg;throw ed(e,ie),Tu(e,c),fa(e,In()),B}}e.finishedWork=O,e.finishedLanes=c,W5(e,v,c)}}return fa(e,In()),e.callbackNode===a?MR.bind(null,e):null}function ES(e,t){var a=wg;if(Gl(e)){var s=ed(e,t);s.flags|=Rn,dP(e.containerInfo)}var c=ix(e,t);if(c!==Jc){var p=da;da=a,p!==null&&$R(p)}return c}function $R(e){da===null?da=e:da.push.apply(da,e)}function W5(e,t,a){switch(t){case cs:case vg:throw new Error("Root did not complete. This is a bug in React.");case Jc:{td(e,da,ds);break}case Qy:{if(Tu(e,a),df(a)&&!WR()){var s=yS+TR-In();if(s>10){var c=vi(e,ie);if(c!==ie)break;var p=e.suspendedLanes;if(!Yl(p,a)){Bi(),gf(e,p);break}e.timeoutHandle=bb(td.bind(null,e,da,ds),s);break}}td(e,da,ds);break}case yg:{if(Tu(e,a),W0(a))break;if(!WR()){var v=ch(e,a),w=v,C=In()-w,R=d4(C)-C;if(R>10){e.timeoutHandle=bb(td.bind(null,e,da,ds),R);break}}td(e,da,ds);break}case ER:{td(e,da,ds);break}default:throw new Error("Unknown root exit status.")}}function Y5(e){for(var t=e;;){if(t.flags&Bd){var a=t.updateQueue;if(a!==null){var s=a.stores;if(s!==null)for(var c=0;c<s.length;c++){var p=s[c],v=p.getSnapshot,w=p.value;try{if(!$e(v(),w))return!1}catch{return!1}}}}var C=t.child;if(t.subtreeFlags&Bd&&C!==null){C.return=t,t=C;continue}if(t===e)return!0;for(;t.sibling===null;){if(t.return===null||t.return===e)return!0;t=t.return}t.sibling.return=t.return,t=t.sibling}return!0}function Tu(e,t){t=Cc(t,Xy),t=Cc(t,bg),mh(e,t)}function OR(e){if(u3(),(Ft&(ni|Za))!==$r)throw new Error("Should not already be working.");ps();var t=vi(e,ie);if(!yi(t,nt))return fa(e,In()),null;var a=ix(e,t);if(e.tag!==pu&&a===Jc){var s=cf(e);s!==ie&&(t=s,a=ES(e,s))}if(a===vg){var c=xg;throw ed(e,ie),Tu(e,t),fa(e,In()),c}if(a===mS)throw new Error("Root did not complete. This is a bug in React.");var p=e.current.alternate;return e.finishedWork=p,e.finishedLanes=t,td(e,da,ds),fa(e,In()),null}function G5(e,t){t!==ie&&(Ec(e,xt(t,nt)),fa(e,In()),(Ft&(ni|Za))===$r&&(Sg(),hu()))}function TS(e,t){var a=Ft;Ft|=CR;try{return e(t)}finally{Ft=a,Ft===$r&&!jo.isBatchingLegacy&&(Sg(),DT())}}function K5(e,t,a,s,c){var p=_i(),v=ti.transition;try{return ti.transition=null,ir(xi),e(t,a,s,c)}finally{ir(p),ti.transition=v,Ft===$r&&Sg()}}function fs(e){Cu!==null&&Cu.tag===pu&&(Ft&(ni|Za))===$r&&ps();var t=Ft;Ft|=CR;var a=ti.transition,s=_i();try{return ti.transition=null,ir(xi),e?e():void 0}finally{ir(s),ti.transition=a,Ft=t,(Ft&(ni|Za))===$r&&hu()}}function AR(){return(Ft&(ni|Za))!==$r}function rx(e,t){Si(vS,vl,e),vl=xt(vl,t)}function kS(e){vl=vS.current,wi(vS,e)}function ed(e,t){e.finishedWork=null,e.finishedLanes=ie;var a=e.timeoutHandle;if(a!==wb&&(e.timeoutHandle=wb,wN(a)),Wn!==null)for(var s=Wn.return;s!==null;){var c=s.alternate;lR(c,s),s=s.return}Fi=e;var p=nd(e.current,null);return Wn=p,Or=vl=t,Ar=cs,xg=null,qy=ie,bg=ie,Xy=ie,wg=null,da=null,HP(),To.discardPendingWarnings(),p}function jR(e,t){do{var a=Wn;try{if(py(),rk(),_n(),gS.current=null,a===null||a.return===null){Ar=vg,xg=t,Wn=null;return}if(it&&a.mode&zt&&Uy(a,!0),et)if(Qo(),t!==null&&typeof t=="object"&&typeof t.then=="function"){var s=t;fc(a,s,Or)}else Wa(a,t,Or);y3(e,a.return,a,t,Or),NR(a)}catch(c){t=c,Wn===a&&a!==null?(a=a.return,Wn=a):a=Wn;continue}return}while(!0)}function _R(){var e=hS.current;return hS.current=Ny,e===null?Ny:e}function LR(e){hS.current=e}function Q5(){yS=In()}function kg(e){qy=xt(e,qy)}function q5(){Ar===cs&&(Ar=Qy)}function RS(){(Ar===cs||Ar===Qy||Ar===Jc)&&(Ar=yg),Fi!==null&&(el(qy)||el(bg))&&Tu(Fi,Or)}function X5(e){Ar!==yg&&(Ar=Jc),wg===null?wg=[e]:wg.push(e)}function J5(){return Ar===cs}function ix(e,t){var a=Ft;Ft|=ni;var s=_R();if(Fi!==e||Or!==t){if(Fr){var c=e.memoizedUpdaters;c.size>0&&(Rg(e,Or),c.clear()),vh(e,t)}ds=vf(),ed(e,t)}oh(t);do try{Z5();break}catch(p){jR(e,p)}while(!0);if(py(),Ft=a,LR(s),Wn!==null)throw new Error("Cannot commit an incomplete root. This error is likely caused by a bug in React. Please file an issue.");return Dn(),Fi=null,Or=ie,Ar}function Z5(){for(;Wn!==null;)zR(Wn)}function e4(e,t){var a=Ft;Ft|=ni;var s=_R();if(Fi!==e||Or!==t){if(Fr){var c=e.memoizedUpdaters;c.size>0&&(Rg(e,Or),c.clear()),vh(e,t)}ds=vf(),Sg(),ed(e,t)}oh(t);do try{t4();break}catch(p){jR(e,p)}while(!0);return py(),LR(s),Ft=a,Wn!==null?(lh(),cs):(Dn(),Fi=null,Or=ie,Ar)}function t4(){for(;Wn!==null&&!Jp();)zR(Wn)}function zR(e){var t=e.alternate;ln(e);var a;(e.mode&zt)!==Qe?($w(e),a=DS(t,e,vl),Uy(e,!0)):a=DS(t,e,vl),_n(),e.memoizedProps=e.pendingProps,a===null?NR(e):Wn=a,gS.current=null}function NR(e){var t=e;do{var a=t.alternate,s=t.return;if((t.flags&Nl)===Ke){ln(t);var c=void 0;if((t.mode&zt)===Qe?c=oR(a,t,vl):($w(t),c=oR(a,t,vl),Uy(t,!1)),_n(),c!==null){Wn=c;return}}else{var p=q3(a,t);if(p!==null){p.flags&=qm,Wn=p;return}if((t.mode&zt)!==Qe){Uy(t,!1);for(var v=t.actualDuration,w=t.child;w!==null;)v+=w.actualDuration,w=w.sibling;t.actualDuration=v}if(s!==null)s.flags|=Nl,s.subtreeFlags=Ke,s.deletions=null;else{Ar=mS,Wn=null;return}}var C=t.sibling;if(C!==null){Wn=C;return}t=s,Wn=t}while(t!==null);Ar===cs&&(Ar=ER)}function td(e,t,a){var s=_i(),c=ti.transition;try{ti.transition=null,ir(xi),n4(e,t,a,s)}finally{ti.transition=c,ir(s)}return null}function n4(e,t,a,s){do ps();while(Cu!==null);if(p4(),(Ft&(ni|Za))!==$r)throw new Error("Should not already be working.");var c=e.finishedWork,p=e.finishedLanes;if(nv(p),c===null)return Va(),null;if(p===ie&&y("root.finishedLanes should not be empty during a commit. This is a bug in React."),e.finishedWork=null,e.finishedLanes=ie,c===e.current)throw new Error("Cannot commit the same tree as before. This error is likely caused by a bug in React. Please file an issue.");e.callbackNode=null,e.callbackPriority=Jn;var v=xt(c.lanes,c.childLanes);fv(e,v),e===Fi&&(Fi=null,Wn=null,Or=ie),((c.subtreeFlags&wr)!==Ke||(c.flags&wr)!==Ke)&&(Zc||(Zc=!0,wS=a,OS(Fl,function(){return ps(),null})));var w=(c.subtreeFlags&(Yo|Vs|Go|wr))!==Ke,C=(c.flags&(Yo|Vs|Go|wr))!==Ke;if(w||C){var R=ti.transition;ti.transition=null;var O=_i();ir(xi);var U=Ft;Ft|=Za,gS.current=null,t5(e,c),Dk(),g5(e,c,p),hN(e.containerInfo),e.current=c,pc(p),m5(c,e,p),Il(),Jm(),Ft=U,ir(O),ti.transition=R}else e.current=c,Dk();var B=Zc;if(Zc?(Zc=!1,Cu=e,Cg=p):(lp=0,ex=null),v=e.pendingLanes,v===ie&&(op=null),B||IR(e.current,!1),th(c.stateNode,s),Fr&&e.memoizedUpdaters.clear(),L5(),fa(e,In()),t!==null)for(var X=e.onRecoverableError,Z=0;Z<t.length;Z++){var te=t[Z],De=te.stack,Je=te.digest;X(te.value,{componentStack:De,digest:Je})}if(Jy){Jy=!1;var Ye=xS;throw xS=null,Ye}return yi(Cg,nt)&&e.tag!==pu&&ps(),v=e.pendingLanes,yi(v,nt)?(l3(),e===SS?Eg++:(Eg=0,SS=e)):Eg=0,hu(),Va(),null}function ps(){if(Cu!==null){var e=gv(Cg),t=Br(Cr,e),a=ti.transition,s=_i();try{return ti.transition=null,ir(t),i4()}finally{ir(s),ti.transition=a}}return!1}function r4(e){bS.push(e),Zc||(Zc=!0,OS(Fl,function(){return ps(),null}))}function i4(){if(Cu===null)return!1;var e=wS;wS=null;var t=Cu,a=Cg;if(Cu=null,Cg=ie,(Ft&(ni|Za))!==$r)throw new Error("Cannot flush passive effects while already rendering.");CS=!0,Zy=!1,ah(a);var s=Ft;Ft|=Za,C5(t.current),x5(t,t.current,a,e);{var c=bS;bS=[];for(var p=0;p<c.length;p++){var v=c[p];a5(t,v)}}Ks(),IR(t.current,!0),Ft=s,hu(),Zy?t===ex?lp++:(lp=0,ex=t):lp=0,CS=!1,Zy=!1,nh(t);{var w=t.current.stateNode;w.effectDuration=0,w.passiveEffectDuration=0}return!0}function PR(e){return op!==null&&op.has(e)}function a4(e){op===null?op=new Set([e]):op.add(e)}function o4(e){Jy||(Jy=!0,xS=e)}var l4=o4;function FR(e,t,a){var s=qc(a,t),c=zk(e,s,nt),p=mu(e,c,nt),v=Bi();p!==null&&(Zs(p,nt,v),fa(p,v))}function Tn(e,t,a){if(J3(a),Dg(!1),e.tag===j){FR(e,e,a);return}var s=null;for(s=t;s!==null;){if(s.tag===j){FR(s,e,a);return}else if(s.tag===M){var c=s.type,p=s.stateNode;if(typeof c.getDerivedStateFromError=="function"||typeof p.componentDidCatch=="function"&&!PR(p)){var v=qc(a,e),w=Kw(s,v,nt),C=mu(s,w,nt),R=Bi();C!==null&&(Zs(C,nt,R),fa(C,R));return}}s=s.return}y(`Internal React error: Attempted to capture a commit phase error inside a detached tree. This indicates a bug in React. Likely causes include deleting the same fiber more than once, committing an already-finished tree, or an inconsistent return pointer.

Error message:

%s`,a)}function s4(e,t,a){var s=e.pingCache;s!==null&&s.delete(t);var c=Bi();gf(e,a),v4(e),Fi===e&&Yl(Or,a)&&(Ar===yg||Ar===Qy&&df(Or)&&In()-yS<TR?ed(e,ie):Xy=xt(Xy,a)),fa(e,c)}function BR(e,t){t===Jn&&(t=U5(e));var a=Bi(),s=ua(e,t);s!==null&&(Zs(s,t,a),fa(s,a))}function u4(e){var t=e.memoizedState,a=Jn;t!==null&&(a=t.retryLane),BR(e,a)}function c4(e,t){var a=Jn,s;switch(e.tag){case le:s=e.stateNode;var c=e.memoizedState;c!==null&&(a=c.retryLane);break;case bt:s=e.stateNode;break;default:throw new Error("Pinged unknown suspense boundary type. This is probably a bug in React.")}s!==null&&s.delete(t),BR(e,a)}function d4(e){return e<120?120:e<480?480:e<1080?1080:e<1920?1920:e<3e3?3e3:e<4320?4320:P5(e/1960)*1960}function f4(){if(Eg>B5)throw Eg=0,SS=null,new Error("Maximum update depth exceeded. This can happen when a component repeatedly calls setState inside componentWillUpdate or componentDidUpdate. React limits the number of nested updates to prevent infinite loops.");lp>I5&&(lp=0,ex=null,y("Maximum update depth exceeded. This can happen when a component calls setState inside useEffect, but useEffect either doesn't have a dependency array, or one of the dependencies changes on every render."))}function p4(){To.flushLegacyContextWarning(),To.flushPendingUnsafeLifecycleWarnings()}function IR(e,t){ln(e),ax(e,Yr,A5),t&&ax(e,Wo,j5),ax(e,Yr,$5),t&&ax(e,Wo,O5),_n()}function ax(e,t,a){for(var s=e,c=null;s!==null;){var p=s.subtreeFlags&t;s!==c&&s.child!==null&&p!==Ke?s=s.child:((s.flags&t)!==Ke&&a(s),s.sibling!==null?s=s.sibling:s=c=s.return)}}var ox=null;function UR(e){{if((Ft&ni)!==$r||!(e.mode&Dt))return;var t=e.tag;if(t!==N&&t!==j&&t!==M&&t!==$&&t!==ce&&t!==ue&&t!==Ae)return;var a=lt(e)||"ReactComponent";if(ox!==null){if(ox.has(a))return;ox.add(a)}else ox=new Set([a]);var s=fr;try{ln(e),y("Can't perform a React state update on a component that hasn't mounted yet. This indicates that you have a side-effect in your render function that asynchronously later calls tries to update the component. Move this work to useEffect instead.")}finally{s?ln(e):_n()}}}var DS;{var h4=null;DS=function(e,t,a){var s=qR(h4,t);try{return tR(e,t,a)}catch(p){if(kP()||p!==null&&typeof p=="object"&&typeof p.then=="function")throw p;if(py(),rk(),lR(e,t),qR(t,s),t.mode&zt&&$w(t),ta(null,tR,null,e,t,a),Wp()){var c=Yp();typeof c=="object"&&c!==null&&c._suppressLogging&&typeof p=="object"&&p!==null&&!p._suppressLogging&&(p._suppressLogging=!0)}throw p}}}var HR=!1,MS;MS=new Set;function g4(e){if(ci&&!i3())switch(e.tag){case $:case ce:case Ae:{var t=Wn&&lt(Wn)||"Unknown",a=t;if(!MS.has(a)){MS.add(a);var s=lt(e)||"Unknown";y("Cannot update a component (`%s`) while rendering a different component (`%s`). To locate the bad setState() call inside `%s`, follow the stack trace as described in https://reactjs.org/link/setstate-in-render",s,t,t)}break}case M:{HR||(y("Cannot update during an existing state transition (such as within `render`). Render methods should be a pure function of props and state."),HR=!0);break}}}function Rg(e,t){if(Fr){var a=e.memoizedUpdaters;a.forEach(function(s){pv(e,s,t)})}}var $S={};function OS(e,t){{var a=jo.current;return a!==null?(a.push(t),$S):qp(e,t)}}function VR(e){if(e!==$S)return Xp(e)}function WR(){return jo.current!==null}function m4(e){{if(e.mode&Dt){if(!SR())return}else if(!N5()||Ft!==$r||e.tag!==$&&e.tag!==ce&&e.tag!==Ae)return;if(jo.current===null){var t=fr;try{ln(e),y(`An update to %s inside a test was not wrapped in act(...).

When testing, code that causes React state updates should be wrapped into act(...):

act(() => {
  /* fire events that update state */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://reactjs.org/link/wrap-tests-with-act`,lt(e))}finally{t?ln(e):_n()}}}}function v4(e){e.tag!==pu&&SR()&&jo.current===null&&y(`A suspended resource finished loading inside a test, but the event was not wrapped in act(...).

When testing, code that resolves suspended data should be wrapped into act(...):

act(() => {
  /* finish loading suspended data */
});
/* assert on the output */

This ensures that you're testing the behavior the user would see in the browser. Learn more at https://reactjs.org/link/wrap-tests-with-act`)}function Dg(e){DR=e}var eo=null,sp=null,y4=function(e){eo=e};function up(e){{if(eo===null)return e;var t=eo(e);return t===void 0?e:t.current}}function AS(e){return up(e)}function jS(e){{if(eo===null)return e;var t=eo(e);if(t===void 0){if(e!=null&&typeof e.render=="function"){var a=up(e.render);if(e.render!==a){var s={$$typeof:fe,render:a};return e.displayName!==void 0&&(s.displayName=e.displayName),s}}return e}return t.current}}function YR(e,t){{if(eo===null)return!1;var a=e.elementType,s=t.type,c=!1,p=typeof s=="object"&&s!==null?s.$$typeof:null;switch(e.tag){case M:{typeof s=="function"&&(c=!0);break}case $:{(typeof s=="function"||p===ut)&&(c=!0);break}case ce:{(p===fe||p===ut)&&(c=!0);break}case ue:case Ae:{(p===Rt||p===ut)&&(c=!0);break}default:return!1}if(c){var v=eo(a);if(v!==void 0&&v===eo(s))return!0}return!1}}function GR(e){{if(eo===null||typeof WeakSet!="function")return;sp===null&&(sp=new WeakSet),sp.add(e)}}var x4=function(e,t){{if(eo===null)return;var a=t.staleFamilies,s=t.updatedFamilies;ps(),fs(function(){_S(e.current,s,a)})}},b4=function(e,t){{if(e.context!==Ra)return;ps(),fs(function(){Mg(t,e,null,null)})}};function _S(e,t,a){{var s=e.alternate,c=e.child,p=e.sibling,v=e.tag,w=e.type,C=null;switch(v){case $:case Ae:case M:C=w;break;case ce:C=w.render;break}if(eo===null)throw new Error("Expected resolveFamily to be set during hot reload.");var R=!1,O=!1;if(C!==null){var U=eo(C);U!==void 0&&(a.has(U)?O=!0:t.has(U)&&(v===M?O=!0:R=!0))}if(sp!==null&&(sp.has(e)||s!==null&&sp.has(s))&&(O=!0),O&&(e._debugNeedsRemount=!0),O||R){var B=ua(e,nt);B!==null&&jr(B,e,nt,rn)}c!==null&&!O&&_S(c,t,a),p!==null&&_S(p,t,a)}}var w4=function(e,t){{var a=new Set,s=new Set(t.map(function(c){return c.current}));return LS(e.current,s,a),a}};function LS(e,t,a){{var s=e.child,c=e.sibling,p=e.tag,v=e.type,w=null;switch(p){case $:case Ae:case M:w=v;break;case ce:w=v.render;break}var C=!1;w!==null&&t.has(w)&&(C=!0),C?S4(e,a):s!==null&&LS(s,t,a),c!==null&&LS(c,t,a)}}function S4(e,t){{var a=C4(e,t);if(a)return;for(var s=e;;){switch(s.tag){case L:t.add(s.stateNode);return;case H:t.add(s.stateNode.containerInfo);return;case j:t.add(s.stateNode.containerInfo);return}if(s.return===null)throw new Error("Expected to reach root first.");s=s.return}}}function C4(e,t){for(var a=e,s=!1;;){if(a.tag===L)s=!0,t.add(a.stateNode);else if(a.child!==null){a.child.return=a,a=a.child;continue}if(a===e)return s;for(;a.sibling===null;){if(a.return===null||a.return===e)return s;a=a.return}a.sibling.return=a.return,a=a.sibling}return!1}var zS;{zS=!1;try{var KR=Object.preventExtensions({})}catch{zS=!0}}function E4(e,t,a,s){this.tag=e,this.key=a,this.elementType=null,this.type=null,this.stateNode=null,this.return=null,this.child=null,this.sibling=null,this.index=0,this.ref=null,this.pendingProps=t,this.memoizedProps=null,this.updateQueue=null,this.memoizedState=null,this.dependencies=null,this.mode=s,this.flags=Ke,this.subtreeFlags=Ke,this.deletions=null,this.lanes=ie,this.childLanes=ie,this.alternate=null,this.actualDuration=Number.NaN,this.actualStartTime=Number.NaN,this.selfBaseDuration=Number.NaN,this.treeBaseDuration=Number.NaN,this.actualDuration=0,this.actualStartTime=-1,this.selfBaseDuration=0,this.treeBaseDuration=0,this._debugSource=null,this._debugOwner=null,this._debugNeedsRemount=!1,this._debugHookTypes=null,!zS&&typeof Object.preventExtensions=="function"&&Object.preventExtensions(this)}var Da=function(e,t,a,s){return new E4(e,t,a,s)};function NS(e){var t=e.prototype;return!!(t&&t.isReactComponent)}function T4(e){return typeof e=="function"&&!NS(e)&&e.defaultProps===void 0}function k4(e){if(typeof e=="function")return NS(e)?M:$;if(e!=null){var t=e.$$typeof;if(t===fe)return ce;if(t===Rt)return ue}return N}function nd(e,t){var a=e.alternate;a===null?(a=Da(e.tag,t,e.key,e.mode),a.elementType=e.elementType,a.type=e.type,a.stateNode=e.stateNode,a._debugSource=e._debugSource,a._debugOwner=e._debugOwner,a._debugHookTypes=e._debugHookTypes,a.alternate=e,e.alternate=a):(a.pendingProps=t,a.type=e.type,a.flags=Ke,a.subtreeFlags=Ke,a.deletions=null,a.actualDuration=0,a.actualStartTime=-1),a.flags=e.flags&Xn,a.childLanes=e.childLanes,a.lanes=e.lanes,a.child=e.child,a.memoizedProps=e.memoizedProps,a.memoizedState=e.memoizedState,a.updateQueue=e.updateQueue;var s=e.dependencies;switch(a.dependencies=s===null?null:{lanes:s.lanes,firstContext:s.firstContext},a.sibling=e.sibling,a.index=e.index,a.ref=e.ref,a.selfBaseDuration=e.selfBaseDuration,a.treeBaseDuration=e.treeBaseDuration,a._debugNeedsRemount=e._debugNeedsRemount,a.tag){case N:case $:case Ae:a.type=up(e.type);break;case M:a.type=AS(e.type);break;case ce:a.type=jS(e.type);break}return a}function R4(e,t){e.flags&=Xn|Ln;var a=e.alternate;if(a===null)e.childLanes=ie,e.lanes=t,e.child=null,e.subtreeFlags=Ke,e.memoizedProps=null,e.memoizedState=null,e.updateQueue=null,e.dependencies=null,e.stateNode=null,e.selfBaseDuration=0,e.treeBaseDuration=0;else{e.childLanes=a.childLanes,e.lanes=a.lanes,e.child=a.child,e.subtreeFlags=Ke,e.deletions=null,e.memoizedProps=a.memoizedProps,e.memoizedState=a.memoizedState,e.updateQueue=a.updateQueue,e.type=a.type;var s=a.dependencies;e.dependencies=s===null?null:{lanes:s.lanes,firstContext:s.firstContext},e.selfBaseDuration=a.selfBaseDuration,e.treeBaseDuration=a.treeBaseDuration}return e}function D4(e,t,a){var s;return e===ry?(s=Dt,t===!0&&(s|=mt,s|=cn)):s=Qe,Fr&&(s|=zt),Da(j,null,null,s)}function PS(e,t,a,s,c,p){var v=N,w=e;if(typeof e=="function")NS(e)?(v=M,w=AS(w)):w=up(w);else if(typeof e=="string")v=L;else e:switch(e){case li:return ku(a.children,c,p,t);case _a:v=Ee,c|=mt,(c&Dt)!==Qe&&(c|=cn);break;case La:return M4(a,c,p,t);case ke:return $4(a,c,p,t);case Me:return O4(a,c,p,t);case Fn:return QR(a,c,p,t);case yn:case Ot:case Cn:case Lr:case St:default:{if(typeof e=="object"&&e!==null)switch(e.$$typeof){case oo:v=ae;break e;case P:v=pe;break e;case fe:v=ce,w=jS(w);break e;case Rt:v=ue;break e;case ut:v=ft,w=null;break e}var C="";{(e===void 0||typeof e=="object"&&e!==null&&Object.keys(e).length===0)&&(C+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var R=s?lt(s):null;R&&(C+=`

Check the render method of \``+R+"`.")}throw new Error("Element type is invalid: expected a string (for built-in components) or a class/function (for composite components) "+("but got: "+(e==null?e:typeof e)+"."+C))}}var O=Da(v,a,t,c);return O.elementType=e,O.type=w,O.lanes=p,O._debugOwner=s,O}function FS(e,t,a){var s=null;s=e._owner;var c=e.type,p=e.key,v=e.props,w=PS(c,p,v,s,t,a);return w._debugSource=e._source,w._debugOwner=e._owner,w}function ku(e,t,a,s){var c=Da(de,e,s,t);return c.lanes=a,c}function M4(e,t,a,s){typeof e.id!="string"&&y('Profiler must specify an "id" of type `string` as a prop. Received the type `%s` instead.',typeof e.id);var c=Da(Te,e,s,t|zt);return c.elementType=La,c.lanes=a,c.stateNode={effectDuration:0,passiveEffectDuration:0},c}function $4(e,t,a,s){var c=Da(le,e,s,t);return c.elementType=ke,c.lanes=a,c}function O4(e,t,a,s){var c=Da(bt,e,s,t);return c.elementType=Me,c.lanes=a,c}function QR(e,t,a,s){var c=Da(He,e,s,t);c.elementType=Fn,c.lanes=a;var p={isHidden:!1};return c.stateNode=p,c}function BS(e,t,a){var s=Da(K,e,null,t);return s.lanes=a,s}function A4(){var e=Da(L,null,null,Qe);return e.elementType="DELETED",e}function j4(e){var t=Da(Tt,null,null,Qe);return t.stateNode=e,t}function IS(e,t,a){var s=e.children!==null?e.children:[],c=Da(H,s,e.key,t);return c.lanes=a,c.stateNode={containerInfo:e.containerInfo,pendingChildren:null,implementation:e.implementation},c}function qR(e,t){return e===null&&(e=Da(N,null,null,Qe)),e.tag=t.tag,e.key=t.key,e.elementType=t.elementType,e.type=t.type,e.stateNode=t.stateNode,e.return=t.return,e.child=t.child,e.sibling=t.sibling,e.index=t.index,e.ref=t.ref,e.pendingProps=t.pendingProps,e.memoizedProps=t.memoizedProps,e.updateQueue=t.updateQueue,e.memoizedState=t.memoizedState,e.dependencies=t.dependencies,e.mode=t.mode,e.flags=t.flags,e.subtreeFlags=t.subtreeFlags,e.deletions=t.deletions,e.lanes=t.lanes,e.childLanes=t.childLanes,e.alternate=t.alternate,e.actualDuration=t.actualDuration,e.actualStartTime=t.actualStartTime,e.selfBaseDuration=t.selfBaseDuration,e.treeBaseDuration=t.treeBaseDuration,e._debugSource=t._debugSource,e._debugOwner=t._debugOwner,e._debugNeedsRemount=t._debugNeedsRemount,e._debugHookTypes=t._debugHookTypes,e}function _4(e,t,a,s,c){this.tag=t,this.containerInfo=e,this.pendingChildren=null,this.current=null,this.pingCache=null,this.finishedWork=null,this.timeoutHandle=wb,this.context=null,this.pendingContext=null,this.callbackNode=null,this.callbackPriority=Jn,this.eventTimes=hf(ie),this.expirationTimes=hf(rn),this.pendingLanes=ie,this.suspendedLanes=ie,this.pingedLanes=ie,this.expiredLanes=ie,this.mutableReadLanes=ie,this.finishedLanes=ie,this.entangledLanes=ie,this.entanglements=hf(ie),this.identifierPrefix=s,this.onRecoverableError=c,this.mutableSourceEagerHydrationData=null,this.effectDuration=0,this.passiveEffectDuration=0;{this.memoizedUpdaters=new Set;for(var p=this.pendingUpdatersLaneMap=[],v=0;v<uh;v++)p.push(new Set)}switch(t){case ry:this._debugRootType=a?"hydrateRoot()":"createRoot()";break;case pu:this._debugRootType=a?"hydrate()":"render()";break}}function XR(e,t,a,s,c,p,v,w,C,R){var O=new _4(e,t,a,w,C),U=D4(t,p);O.current=U,U.stateNode=O;{var B={element:s,isDehydrated:a,cache:null,transitions:null,pendingSuspenseBoundaries:null};U.memoizedState=B}return Zb(U),O}var US="18.3.1";function L4(e,t,a){var s=arguments.length>3&&arguments[3]!==void 0?arguments[3]:null;return Ri(s),{$$typeof:Oi,key:s==null?null:""+s,children:e,containerInfo:t,implementation:a}}var HS,VS;HS=!1,VS={};function JR(e){if(!e)return Ra;var t=Hs(e),a=vP(t);if(t.tag===M){var s=t.type;if(cl(s))return TT(t,s,a)}return a}function z4(e,t){{var a=Hs(e);if(a===void 0){if(typeof e.render=="function")throw new Error("Unable to find node on an unmounted component.");var s=Object.keys(e).join(",");throw new Error("Argument appears to not be a ReactComponent. Keys: "+s)}var c=hi(a);if(c===null)return null;if(c.mode&mt){var p=lt(a)||"Component";if(!VS[p]){VS[p]=!0;var v=fr;try{ln(c),a.mode&mt?y("%s is deprecated in StrictMode. %s was passed an instance of %s which is inside StrictMode. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node",t,t,p):y("%s is deprecated in StrictMode. %s was passed an instance of %s which renders StrictMode children. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node",t,t,p)}finally{v?ln(v):_n()}}}return c.stateNode}}function ZR(e,t,a,s,c,p,v,w){var C=!1,R=null;return XR(e,t,C,R,a,s,c,p,v)}function eD(e,t,a,s,c,p,v,w,C,R){var O=!0,U=XR(a,s,O,e,c,p,v,w,C);U.context=JR(null);var B=U.current,X=Bi(),Z=Eu(B),te=ss(X,Z);return te.callback=t??null,mu(B,te,Z),H5(U,Z,X),U}function Mg(e,t,a,s){eh(t,e);var c=t.current,p=Bi(),v=Eu(c);Vd(v);var w=JR(a);t.context===null?t.context=w:t.pendingContext=w,ci&&fr!==null&&!HS&&(HS=!0,y(`Render methods should be a pure function of props and state; triggering nested component updates from render is not allowed. If necessary, trigger nested updates in componentDidUpdate.

Check the render method of %s.`,lt(fr)||"Unknown"));var C=ss(p,v);C.payload={element:e},s=s===void 0?null:s,s!==null&&(typeof s!="function"&&y("render(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",s),C.callback=s);var R=mu(c,C,v);return R!==null&&(jr(R,c,v,p),yy(R,c,v)),v}function lx(e){var t=e.current;if(!t.child)return null;switch(t.child.tag){case L:return t.child.stateNode;default:return t.child.stateNode}}function N4(e){switch(e.tag){case j:{var t=e.stateNode;if(Gl(t)){var a=uv(t);G5(t,a)}break}case le:{fs(function(){var c=ua(e,nt);if(c!==null){var p=Bi();jr(c,e,nt,p)}});var s=nt;WS(e,s);break}}}function tD(e,t){var a=e.memoizedState;a!==null&&a.dehydrated!==null&&(a.retryLane=gh(a.retryLane,t))}function WS(e,t){tD(e,t);var a=e.alternate;a&&tD(a,t)}function P4(e){if(e.tag===le){var t=Js,a=ua(e,t);if(a!==null){var s=Bi();jr(a,e,t,s)}WS(e,t)}}function F4(e){if(e.tag===le){var t=Eu(e),a=ua(e,t);if(a!==null){var s=Bi();jr(a,e,t,s)}WS(e,t)}}function nD(e){var t=Ca(e);return t===null?null:t.stateNode}var rD=function(e){return null};function B4(e){return rD(e)}var iD=function(e){return!1};function I4(e){return iD(e)}var aD=null,oD=null,lD=null,sD=null,uD=null,cD=null,dD=null,fD=null,pD=null;{var hD=function(e,t,a){var s=t[a],c=yt(e)?e.slice():gt({},e);return a+1===t.length?(yt(c)?c.splice(s,1):delete c[s],c):(c[s]=hD(e[s],t,a+1),c)},gD=function(e,t){return hD(e,t,0)},mD=function(e,t,a,s){var c=t[s],p=yt(e)?e.slice():gt({},e);if(s+1===t.length){var v=a[s];p[v]=p[c],yt(p)?p.splice(c,1):delete p[c]}else p[c]=mD(e[c],t,a,s+1);return p},vD=function(e,t,a){if(t.length!==a.length){S("copyWithRename() expects paths of the same length");return}else for(var s=0;s<a.length-1;s++)if(t[s]!==a[s]){S("copyWithRename() expects paths to be the same except for the deepest key");return}return mD(e,t,a,0)},yD=function(e,t,a,s){if(a>=t.length)return s;var c=t[a],p=yt(e)?e.slice():gt({},e);return p[c]=yD(e[c],t,a+1,s),p},xD=function(e,t,a){return yD(e,t,0,a)},YS=function(e,t){for(var a=e.memoizedState;a!==null&&t>0;)a=a.next,t--;return a};aD=function(e,t,a,s){var c=YS(e,t);if(c!==null){var p=xD(c.memoizedState,a,s);c.memoizedState=p,c.baseState=p,e.memoizedProps=gt({},e.memoizedProps);var v=ua(e,nt);v!==null&&jr(v,e,nt,rn)}},oD=function(e,t,a){var s=YS(e,t);if(s!==null){var c=gD(s.memoizedState,a);s.memoizedState=c,s.baseState=c,e.memoizedProps=gt({},e.memoizedProps);var p=ua(e,nt);p!==null&&jr(p,e,nt,rn)}},lD=function(e,t,a,s){var c=YS(e,t);if(c!==null){var p=vD(c.memoizedState,a,s);c.memoizedState=p,c.baseState=p,e.memoizedProps=gt({},e.memoizedProps);var v=ua(e,nt);v!==null&&jr(v,e,nt,rn)}},sD=function(e,t,a){e.pendingProps=xD(e.memoizedProps,t,a),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var s=ua(e,nt);s!==null&&jr(s,e,nt,rn)},uD=function(e,t){e.pendingProps=gD(e.memoizedProps,t),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var a=ua(e,nt);a!==null&&jr(a,e,nt,rn)},cD=function(e,t,a){e.pendingProps=vD(e.memoizedProps,t,a),e.alternate&&(e.alternate.pendingProps=e.pendingProps);var s=ua(e,nt);s!==null&&jr(s,e,nt,rn)},dD=function(e){var t=ua(e,nt);t!==null&&jr(t,e,nt,rn)},fD=function(e){rD=e},pD=function(e){iD=e}}function U4(e){var t=hi(e);return t===null?null:t.stateNode}function H4(e){return null}function V4(){return fr}function W4(e){var t=e.findFiberByHostInstance,a=d.ReactCurrentDispatcher;return Zp({bundleType:e.bundleType,version:e.version,rendererPackageName:e.rendererPackageName,rendererConfig:e.rendererConfig,overrideHookState:aD,overrideHookStateDeletePath:oD,overrideHookStateRenamePath:lD,overrideProps:sD,overridePropsDeletePath:uD,overridePropsRenamePath:cD,setErrorHandler:fD,setSuspenseHandler:pD,scheduleUpdate:dD,currentDispatcherRef:a,findHostInstanceByFiber:U4,findFiberByHostInstance:t||H4,findHostInstancesForRefresh:w4,scheduleRefresh:x4,scheduleRoot:b4,setRefreshHandler:y4,getCurrentFiber:V4,reconcilerVersion:US})}var bD=typeof reportError=="function"?reportError:function(e){console.error(e)};function GS(e){this._internalRoot=e}sx.prototype.render=GS.prototype.render=function(e){var t=this._internalRoot;if(t===null)throw new Error("Cannot update an unmounted root.");{typeof arguments[1]=="function"?y("render(...): does not support the second callback argument. To execute a side effect after rendering, declare it in a component body with useEffect()."):ux(arguments[1])?y("You passed a container to the second argument of root.render(...). You don't need to pass it again since you already passed it to create the root."):typeof arguments[1]<"u"&&y("You passed a second argument to root.render(...) but it only accepts one argument.");var a=t.containerInfo;if(a.nodeType!==Qn){var s=nD(t.current);s&&s.parentNode!==a&&y("render(...): It looks like the React-rendered content of the root container was removed without using React. This is not supported and will cause errors. Instead, call root.unmount() to empty a root's container.")}}Mg(e,t,null,null)},sx.prototype.unmount=GS.prototype.unmount=function(){typeof arguments[0]=="function"&&y("unmount(...): does not support a callback argument. To execute a side effect after rendering, declare it in a component body with useEffect().");var e=this._internalRoot;if(e!==null){this._internalRoot=null;var t=e.containerInfo;AR()&&y("Attempted to synchronously unmount a root while React was already rendering. React cannot finish unmounting the root until the current render has completed, which may lead to a race condition."),fs(function(){Mg(null,e,null,null)}),bT(t)}};function Y4(e,t){if(!ux(e))throw new Error("createRoot(...): Target container is not a DOM element.");wD(e);var a=!1,s=!1,c="",p=bD;t!=null&&(t.hydrate?S("hydrate through createRoot is deprecated. Use ReactDOMClient.hydrateRoot(container, <App />) instead."):typeof t=="object"&&t!==null&&t.$$typeof===br&&y(`You passed a JSX element to createRoot. You probably meant to call root.render instead. Example usage:

  let root = createRoot(domContainer);
  root.render(<App />);`),t.unstable_strictMode===!0&&(a=!0),t.identifierPrefix!==void 0&&(c=t.identifierPrefix),t.onRecoverableError!==void 0&&(p=t.onRecoverableError),t.transitionCallbacks!==void 0&&t.transitionCallbacks);var v=ZR(e,ry,null,a,s,c,p);qv(v.current,e);var w=e.nodeType===Qn?e.parentNode:e;return Lh(w),new GS(v)}function sx(e){this._internalRoot=e}function G4(e){e&&bv(e)}sx.prototype.unstable_scheduleHydration=G4;function K4(e,t,a){if(!ux(e))throw new Error("hydrateRoot(...): Target container is not a DOM element.");wD(e),t===void 0&&y("Must provide initial children as second argument to hydrateRoot. Example usage: hydrateRoot(domContainer, <App />)");var s=a??null,c=a!=null&&a.hydratedSources||null,p=!1,v=!1,w="",C=bD;a!=null&&(a.unstable_strictMode===!0&&(p=!0),a.identifierPrefix!==void 0&&(w=a.identifierPrefix),a.onRecoverableError!==void 0&&(C=a.onRecoverableError));var R=eD(t,null,e,ry,s,p,v,w,C);if(qv(R.current,e),Lh(e),c)for(var O=0;O<c.length;O++){var U=c[O];JP(R,U)}return new sx(R)}function ux(e){return!!(e&&(e.nodeType===di||e.nodeType===mo||e.nodeType===Ku))}function $g(e){return!!(e&&(e.nodeType===di||e.nodeType===mo||e.nodeType===Ku||e.nodeType===Qn&&e.nodeValue===" react-mount-point-unstable "))}function wD(e){e.nodeType===di&&e.tagName&&e.tagName.toUpperCase()==="BODY"&&y("createRoot(): Creating roots directly with document.body is discouraged, since its children are often manipulated by third-party scripts and browser extensions. This may lead to subtle reconciliation issues. Try using a container element created for your app."),Yh(e)&&(e._reactRootContainer?y("You are calling ReactDOMClient.createRoot() on a container that was previously passed to ReactDOM.render(). This is not supported."):y("You are calling ReactDOMClient.createRoot() on a container that has already been passed to createRoot() before. Instead, call root.render() on the existing root instead if you want to update it."))}var Q4=d.ReactCurrentOwner,SD;SD=function(e){if(e._reactRootContainer&&e.nodeType!==Qn){var t=nD(e._reactRootContainer.current);t&&t.parentNode!==e&&y("render(...): It looks like the React-rendered content of this container was removed without using React. This is not supported and will cause errors. Instead, call ReactDOM.unmountComponentAtNode to empty a container.")}var a=!!e._reactRootContainer,s=KS(e),c=!!(s&&du(s));c&&!a&&y("render(...): Replacing React-rendered children with a new root component. If you intended to update the children of this node, you should instead have the existing children update their state and render the new components instead of calling ReactDOM.render."),e.nodeType===di&&e.tagName&&e.tagName.toUpperCase()==="BODY"&&y("render(): Rendering components directly into document.body is discouraged, since its children are often manipulated by third-party scripts and browser extensions. This may lead to subtle reconciliation issues. Try rendering into a container element created for your app.")};function KS(e){return e?e.nodeType===mo?e.documentElement:e.firstChild:null}function CD(){}function q4(e,t,a,s,c){if(c){if(typeof s=="function"){var p=s;s=function(){var B=lx(v);p.call(B)}}var v=eD(t,s,e,pu,null,!1,!1,"",CD);e._reactRootContainer=v,qv(v.current,e);var w=e.nodeType===Qn?e.parentNode:e;return Lh(w),fs(),v}else{for(var C;C=e.lastChild;)e.removeChild(C);if(typeof s=="function"){var R=s;s=function(){var B=lx(O);R.call(B)}}var O=ZR(e,pu,null,!1,!1,"",CD);e._reactRootContainer=O,qv(O.current,e);var U=e.nodeType===Qn?e.parentNode:e;return Lh(U),fs(function(){Mg(t,O,a,s)}),O}}function X4(e,t){e!==null&&typeof e!="function"&&y("%s(...): Expected the last optional `callback` argument to be a function. Instead received: %s.",t,e)}function cx(e,t,a,s,c){SD(a),X4(c===void 0?null:c,"render");var p=a._reactRootContainer,v;if(!p)v=q4(a,t,e,c,s);else{if(v=p,typeof c=="function"){var w=c;c=function(){var C=lx(v);w.call(C)}}Mg(t,v,e,c)}return lx(v)}var ED=!1;function J4(e){{ED||(ED=!0,y("findDOMNode is deprecated and will be removed in the next major release. Instead, add a ref directly to the element you want to reference. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-find-node"));var t=Q4.current;if(t!==null&&t.stateNode!==null){var a=t.stateNode._warnedAboutRefsInRender;a||y("%s is accessing findDOMNode inside its render(). render() should be a pure function of props and state. It should never access something that requires stale data from the previous render, such as refs. Move this logic to componentDidMount and componentDidUpdate instead.",Pt(t.type)||"A component"),t.stateNode._warnedAboutRefsInRender=!0}}return e==null?null:e.nodeType===di?e:z4(e,"findDOMNode")}function Z4(e,t,a){if(y("ReactDOM.hydrate is no longer supported in React 18. Use hydrateRoot instead. Until you switch to the new API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!$g(t))throw new Error("Target container is not a DOM element.");{var s=Yh(t)&&t._reactRootContainer===void 0;s&&y("You are calling ReactDOM.hydrate() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call hydrateRoot(container, element)?")}return cx(null,e,t,!0,a)}function eF(e,t,a){if(y("ReactDOM.render is no longer supported in React 18. Use createRoot instead. Until you switch to the new API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!$g(t))throw new Error("Target container is not a DOM element.");{var s=Yh(t)&&t._reactRootContainer===void 0;s&&y("You are calling ReactDOM.render() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call root.render(element)?")}return cx(null,e,t,!1,a)}function tF(e,t,a,s){if(y("ReactDOM.unstable_renderSubtreeIntoContainer() is no longer supported in React 18. Consider using a portal instead. Until you switch to the createRoot API, your app will behave as if it's running React 17. Learn more: https://reactjs.org/link/switch-to-createroot"),!$g(a))throw new Error("Target container is not a DOM element.");if(e==null||!zl(e))throw new Error("parentComponent must be a valid React Component");return cx(e,t,a,!1,s)}var TD=!1;function nF(e){if(TD||(TD=!0,y("unmountComponentAtNode is deprecated and will be removed in the next major release. Switch to the createRoot API. Learn more: https://reactjs.org/link/switch-to-createroot")),!$g(e))throw new Error("unmountComponentAtNode(...): Target container is not a DOM element.");{var t=Yh(e)&&e._reactRootContainer===void 0;t&&y("You are calling ReactDOM.unmountComponentAtNode() on a container that was previously passed to ReactDOMClient.createRoot(). This is not supported. Did you mean to call root.unmount()?")}if(e._reactRootContainer){{var a=KS(e),s=a&&!du(a);s&&y("unmountComponentAtNode(): The node you're attempting to unmount was rendered by another copy of React.")}return fs(function(){cx(null,null,e,!1,function(){e._reactRootContainer=null,bT(e)})}),!0}else{{var c=KS(e),p=!!(c&&du(c)),v=e.nodeType===di&&$g(e.parentNode)&&!!e.parentNode._reactRootContainer;p&&y("unmountComponentAtNode(): The node you're attempting to unmount was rendered by React and is not a top-level container. %s",v?"You may have accidentally passed in a React root node instead of its container.":"Instead, have the parent component update its state and rerender in order to remove this component.")}return!1}}G0(N4),xh(P4),K0(F4),xf(_i),mv(hv),(typeof Map!="function"||Map.prototype==null||typeof Map.prototype.forEach!="function"||typeof Set!="function"||Set.prototype==null||typeof Set.prototype.clear!="function"||typeof Set.prototype.forEach!="function")&&y("React depends on Map and Set built-in types. Make sure that you load a polyfill in older browsers. https://reactjs.org/link/react-polyfills"),rc(iN),Km(TS,K5,fs);function rF(e,t){var a=arguments.length>2&&arguments[2]!==void 0?arguments[2]:null;if(!ux(t))throw new Error("Target container is not a DOM element.");return L4(e,t,null,a)}function iF(e,t,a,s){return tF(e,t,a,s)}var QS={usingClientEntryPoint:!1,Events:[du,Bf,Xv,Hp,Fs,TS]};function aF(e,t){return QS.usingClientEntryPoint||y('You are importing createRoot from "react-dom" which is not supported. You should instead import it from "react-dom/client".'),Y4(e,t)}function oF(e,t,a){return QS.usingClientEntryPoint||y('You are importing hydrateRoot from "react-dom" which is not supported. You should instead import it from "react-dom/client".'),K4(e,t,a)}function lF(e){return AR()&&y("flushSync was called from inside a lifecycle method. React cannot flush when React is already rendering. Consider moving this call to a scheduler task or micro task."),fs(e)}var sF=W4({findFiberByHostInstance:Ic,bundleType:1,version:US,rendererPackageName:"react-dom"});if(!sF&&an&&window.top===window.self&&(navigator.userAgent.indexOf("Chrome")>-1&&navigator.userAgent.indexOf("Edge")===-1||navigator.userAgent.indexOf("Firefox")>-1)){var kD=window.location.protocol;/^(https?|file):$/.test(kD)&&console.info("%cDownload the React DevTools for a better development experience: https://reactjs.org/link/react-devtools"+(kD==="file:"?`
You might need to use a local HTTP server (instead of file://): https://reactjs.org/link/react-devtools-faq`:""),"font-weight:bold")}Vi.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=QS,Vi.createPortal=rF,Vi.createRoot=aF,Vi.findDOMNode=J4,Vi.flushSync=lF,Vi.hydrate=Z4,Vi.hydrateRoot=oF,Vi.render=eF,Vi.unmountComponentAtNode=nF,Vi.unstable_batchedUpdates=TS,Vi.unstable_renderSubtreeIntoContainer=iF,Vi.version=US,typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop=="function"&&__REACT_DEVTOOLS_GLOBAL_HOOK__.registerInternalModuleStop(new Error)}(),Vi}var o1={};function l1(){if(!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u"||typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE!="function")){if(o1.NODE_ENV!=="production")throw new Error("^_^");try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(l1)}catch(o){console.error(o)}}}o1.NODE_ENV==="production"?(l1(),px.exports=LD()):px.exports=zD();var ND=px.exports,mx,PD={},_g=ND;if(PD.NODE_ENV==="production")mx=_g.createRoot,_g.hydrateRoot;else{var s1=_g.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;mx=function(o,r){s1.usingClientEntryPoint=!0;try{return _g.createRoot(o,r)}finally{s1.usingClientEntryPoint=!1}}}var Wi=function(){return Wi=Object.assign||function(r){for(var l,d=1,h=arguments.length;d<h;d++){l=arguments[d];for(var b in l)Object.prototype.hasOwnProperty.call(l,b)&&(r[b]=l[b])}return r},Wi.apply(this,arguments)};function rd(o,r,l){if(l||arguments.length===2)for(var d=0,h=r.length,b;d<h;d++)(b||!(d in r))&&(b||(b=Array.prototype.slice.call(r,0,d)),b[d]=r[d]);return o.concat(b||Array.prototype.slice.call(r))}typeof SuppressedError=="function"&&SuppressedError;function FD(o){var r=Object.create(null);return function(l){return r[l]===void 0&&(r[l]=o(l)),r[l]}}var BD=/^((children|dangerouslySetInnerHTML|key|ref|autoFocus|defaultValue|defaultChecked|innerHTML|suppressContentEditableWarning|suppressHydrationWarning|valueLink|abbr|accept|acceptCharset|accessKey|action|allow|allowUserMedia|allowPaymentRequest|allowFullScreen|allowTransparency|alt|async|autoComplete|autoPlay|capture|cellPadding|cellSpacing|challenge|charSet|checked|cite|classID|className|cols|colSpan|content|contentEditable|contextMenu|controls|controlsList|coords|crossOrigin|data|dateTime|decoding|default|defer|dir|disabled|disablePictureInPicture|disableRemotePlayback|download|draggable|encType|enterKeyHint|fetchpriority|fetchPriority|form|formAction|formEncType|formMethod|formNoValidate|formTarget|frameBorder|headers|height|hidden|high|href|hrefLang|htmlFor|httpEquiv|id|inputMode|integrity|is|keyParams|keyType|kind|label|lang|list|loading|loop|low|marginHeight|marginWidth|max|maxLength|media|mediaGroup|method|min|minLength|multiple|muted|name|nonce|noValidate|open|optimum|pattern|placeholder|playsInline|popover|popoverTarget|popoverTargetAction|poster|preload|profile|radioGroup|readOnly|referrerPolicy|rel|required|reversed|role|rows|rowSpan|sandbox|scope|scoped|scrolling|seamless|selected|shape|size|sizes|slot|span|spellCheck|src|srcDoc|srcLang|srcSet|start|step|style|summary|tabIndex|target|title|translate|type|useMap|value|width|wmode|wrap|about|datatype|inlist|prefix|property|resource|typeof|vocab|autoCapitalize|autoCorrect|autoSave|color|incremental|fallback|inert|itemProp|itemScope|itemType|itemID|itemRef|on|option|results|security|unselectable|accentHeight|accumulate|additive|alignmentBaseline|allowReorder|alphabetic|amplitude|arabicForm|ascent|attributeName|attributeType|autoReverse|azimuth|baseFrequency|baselineShift|baseProfile|bbox|begin|bias|by|calcMode|capHeight|clip|clipPathUnits|clipPath|clipRule|colorInterpolation|colorInterpolationFilters|colorProfile|colorRendering|contentScriptType|contentStyleType|cursor|cx|cy|d|decelerate|descent|diffuseConstant|direction|display|divisor|dominantBaseline|dur|dx|dy|edgeMode|elevation|enableBackground|end|exponent|externalResourcesRequired|fill|fillOpacity|fillRule|filter|filterRes|filterUnits|floodColor|floodOpacity|focusable|fontFamily|fontSize|fontSizeAdjust|fontStretch|fontStyle|fontVariant|fontWeight|format|from|fr|fx|fy|g1|g2|glyphName|glyphOrientationHorizontal|glyphOrientationVertical|glyphRef|gradientTransform|gradientUnits|hanging|horizAdvX|horizOriginX|ideographic|imageRendering|in|in2|intercept|k|k1|k2|k3|k4|kernelMatrix|kernelUnitLength|kerning|keyPoints|keySplines|keyTimes|lengthAdjust|letterSpacing|lightingColor|limitingConeAngle|local|markerEnd|markerMid|markerStart|markerHeight|markerUnits|markerWidth|mask|maskContentUnits|maskUnits|mathematical|mode|numOctaves|offset|opacity|operator|order|orient|orientation|origin|overflow|overlinePosition|overlineThickness|panose1|paintOrder|pathLength|patternContentUnits|patternTransform|patternUnits|pointerEvents|points|pointsAtX|pointsAtY|pointsAtZ|preserveAlpha|preserveAspectRatio|primitiveUnits|r|radius|refX|refY|renderingIntent|repeatCount|repeatDur|requiredExtensions|requiredFeatures|restart|result|rotate|rx|ry|scale|seed|shapeRendering|slope|spacing|specularConstant|specularExponent|speed|spreadMethod|startOffset|stdDeviation|stemh|stemv|stitchTiles|stopColor|stopOpacity|strikethroughPosition|strikethroughThickness|string|stroke|strokeDasharray|strokeDashoffset|strokeLinecap|strokeLinejoin|strokeMiterlimit|strokeOpacity|strokeWidth|surfaceScale|systemLanguage|tableValues|targetX|targetY|textAnchor|textDecoration|textRendering|textLength|to|transform|u1|u2|underlinePosition|underlineThickness|unicode|unicodeBidi|unicodeRange|unitsPerEm|vAlphabetic|vHanging|vIdeographic|vMathematical|values|vectorEffect|version|vertAdvY|vertOriginX|vertOriginY|viewBox|viewTarget|visibility|widths|wordSpacing|writingMode|x|xHeight|x1|x2|xChannelSelector|xlinkActuate|xlinkArcrole|xlinkHref|xlinkRole|xlinkShow|xlinkTitle|xlinkType|xmlBase|xmlns|xmlnsXlink|xmlLang|xmlSpace|y|y1|y2|yChannelSelector|z|zoomAndPan|for|class|autofocus)|(([Dd][Aa][Tt][Aa]|[Aa][Rr][Ii][Aa]|x)-.*))$/,ID=FD(function(o){return BD.test(o)||o.charCodeAt(0)===111&&o.charCodeAt(1)===110&&o.charCodeAt(2)<91}),On="-ms-",hp="-moz-",Zt="-webkit-",u1="comm",Lg="rule",vx="decl",UD="@import",HD="@namespace",c1="@keyframes",VD="@layer",d1=Math.abs,yx=String.fromCharCode,xx=Object.assign;function WD(o,r){return yr(o,0)^45?(((r<<2^yr(o,0))<<2^yr(o,1))<<2^yr(o,2))<<2^yr(o,3):0}function f1(o){return o.trim()}function xl(o,r){return(o=r.exec(o))?o[0]:o}function Mt(o,r,l){return o.replace(r,l)}function zg(o,r,l){return o.indexOf(r,l)}function yr(o,r){return o.charCodeAt(r)|0}function Ru(o,r,l){return o.slice(r,l)}function to(o){return o.length}function p1(o){return o.length}function gp(o,r){return r.push(o),o}function YD(o,r){return o.map(r).join("")}function h1(o,r){return o.filter(function(l){return!xl(l,r)})}var Ng=1,id=1,g1=0,$a=0,sr=0,ad="";function Pg(o,r,l,d,h,b,S,y){return{value:o,root:r,parent:l,type:d,props:h,children:b,line:Ng,column:id,length:S,return:"",siblings:y}}function gs(o,r){return xx(Pg("",null,null,"",null,null,0,o.siblings),o,{length:-o.length},r)}function od(o){for(;o.root;)o=gs(o.root,{children:[o]});gp(o,o.siblings)}function GD(){return sr}function KD(){return sr=$a>0?yr(ad,--$a):0,id--,sr===10&&(id=1,Ng--),sr}function no(){return sr=$a<g1?yr(ad,$a++):0,id++,sr===10&&(id=1,Ng++),sr}function ms(){return yr(ad,$a)}function Fg(){return $a}function Bg(o,r){return Ru(ad,o,r)}function mp(o){switch(o){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function QD(o){return Ng=id=1,g1=to(ad=o),$a=0,[]}function qD(o){return ad="",o}function bx(o){return f1(Bg($a-1,wx(o===91?o+2:o===40?o+1:o)))}function XD(o){for(;(sr=ms())&&sr<33;)no();return mp(o)>2||mp(sr)>3?"":" "}function JD(o,r){for(;--r&&no()&&!(sr<48||sr>102||sr>57&&sr<65||sr>70&&sr<97););return Bg(o,Fg()+(r<6&&ms()==32&&no()==32))}function wx(o){for(;no();)switch(sr){case o:return $a;case 34:case 39:o!==34&&o!==39&&wx(sr);break;case 40:o===41&&wx(o);break;case 92:no();break}return $a}function ZD(o,r){for(;no()&&o+sr!==57;)if(o+sr===84&&ms()===47)break;return"/*"+Bg(r,$a-1)+"*"+yx(o===47?o:no())}function eM(o){for(;!mp(ms());)no();return Bg(o,$a)}function tM(o){return qD(Ig("",null,null,null,[""],o=QD(o),0,[0],o))}function Ig(o,r,l,d,h,b,S,y,E){for(var $=0,M=0,N=S,j=0,H=0,L=0,K=1,de=1,Ee=1,pe=0,ae="",ce=h,Te=b,le=d,ue=ae;de;)switch(L=pe,pe=no()){case 40:if(L!=108&&yr(ue,N-1)==58){zg(ue+=Mt(bx(pe),"&","&\f"),"&\f",d1($?y[$-1]:0))!=-1&&(Ee=-1);break}case 34:case 39:case 91:ue+=bx(pe);break;case 9:case 10:case 13:case 32:ue+=XD(L);break;case 92:ue+=JD(Fg()-1,7);continue;case 47:switch(ms()){case 42:case 47:gp(nM(ZD(no(),Fg()),r,l,E),E),(mp(L||1)==5||mp(ms()||1)==5)&&to(ue)&&Ru(ue,-1,void 0)!==" "&&(ue+=" ");break;default:ue+="/"}break;case 123*K:y[$++]=to(ue)*Ee;case 125*K:case 59:case 0:switch(pe){case 0:case 125:de=0;case 59+M:Ee==-1&&(ue=Mt(ue,/\f/g,"")),H>0&&(to(ue)-N||K===0&&L===47)&&gp(H>32?v1(ue+";",d,l,N-1,E):v1(Mt(ue," ","")+";",d,l,N-2,E),E);break;case 59:ue+=";";default:if(gp(le=m1(ue,r,l,$,M,h,y,ae,ce=[],Te=[],N,b),b),pe===123)if(M===0)Ig(ue,r,le,le,ce,b,N,y,Te);else{switch(j){case 99:if(yr(ue,3)===110)break;case 108:if(yr(ue,2)===97)break;default:M=0;case 100:case 109:case 115:}M?Ig(o,le,le,d&&gp(m1(o,le,le,0,0,h,y,ae,h,ce=[],N,Te),Te),h,Te,N,y,d?ce:Te):Ig(ue,le,le,le,[""],Te,0,y,Te)}}$=M=H=0,K=Ee=1,ae=ue="",N=S;break;case 58:N=1+to(ue),H=L;default:if(K<1){if(pe==123)--K;else if(pe==125&&K++==0&&KD()==125)continue}switch(ue+=yx(pe),pe*K){case 38:Ee=M>0?1:(ue+="\f",-1);break;case 44:y[$++]=(to(ue)-1)*Ee,Ee=1;break;case 64:ms()===45&&(ue+=bx(no())),j=ms(),M=N=to(ae=ue+=eM(Fg())),pe++;break;case 45:L===45&&to(ue)==2&&(K=0)}}return b}function m1(o,r,l,d,h,b,S,y,E,$,M,N){for(var j=h-1,H=h===0?b:[""],L=p1(H),K=0,de=0,Ee=0;K<d;++K)for(var pe=0,ae=Ru(o,j+1,j=d1(de=S[K])),ce=o;pe<L;++pe)(ce=f1(de>0?H[pe]+" "+ae:Mt(ae,/&\f/g,H[pe])))&&(E[Ee++]=ce);return Pg(o,r,l,h===0?Lg:y,E,$,M,N)}function nM(o,r,l,d){return Pg(o,r,l,u1,yx(GD()),Ru(o,2,-2),0,d)}function v1(o,r,l,d,h){return Pg(o,r,l,vx,Ru(o,0,d),Ru(o,d+1,-1),d,h)}function y1(o,r,l){switch(WD(o,r)){case 5103:return Zt+"print-"+o+o;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return Zt+o+o;case 4855:return Zt+o.replace("add","source-over").replace("substract","source-out").replace("intersect","source-in").replace("exclude","xor")+o;case 4789:return hp+o+o;case 5349:case 4246:case 4810:case 6968:case 2756:return Zt+o+hp+o+On+o+o;case 5936:switch(yr(o,r+11)){case 114:return Zt+o+On+Mt(o,/[svh]\w+-[tblr]{2}/,"tb")+o;case 108:return Zt+o+On+Mt(o,/[svh]\w+-[tblr]{2}/,"tb-rl")+o;case 45:return Zt+o+On+Mt(o,/[svh]\w+-[tblr]{2}/,"lr")+o}case 6828:case 4268:case 2903:return Zt+o+On+o+o;case 6165:return Zt+o+On+"flex-"+o+o;case 5187:return Zt+o+Mt(o,/(\w+).+(:[^]+)/,Zt+"box-$1$2"+On+"flex-$1$2")+o;case 5443:return Zt+o+On+"flex-item-"+Mt(o,/flex-|-self/g,"")+(xl(o,/flex-|baseline/)?"":On+"grid-row-"+Mt(o,/flex-|-self/g,""))+o;case 4675:return Zt+o+On+"flex-line-pack"+Mt(o,/align-content|flex-|-self/g,"")+o;case 5548:return Zt+o+On+Mt(o,"shrink","negative")+o;case 5292:return Zt+o+On+Mt(o,"basis","preferred-size")+o;case 6060:return Zt+"box-"+Mt(o,"-grow","")+Zt+o+On+Mt(o,"grow","positive")+o;case 4554:return Zt+Mt(o,/([^-])(transform)/g,"$1"+Zt+"$2")+o;case 6187:return Mt(Mt(Mt(o,/(zoom-|grab)/,Zt+"$1"),/(image-set)/,Zt+"$1"),o,"")+o;case 5495:case 3959:return Mt(o,/(image-set\([^]*)/,Zt+"$1$`$1");case 4968:return Mt(Mt(o,/(.+:)(flex-)?(.*)/,Zt+"box-pack:$3"+On+"flex-pack:$3"),/space-between/,"justify")+Zt+o+o;case 4200:if(!xl(o,/flex-|baseline/))return On+"grid-column-align"+Ru(o,r)+o;break;case 2592:case 3360:return On+Mt(o,"template-","")+o;case 4384:case 3616:return l&&l.some(function(d,h){return r=h,xl(d.props,/grid-\w+-end/)})?~zg(o+(l=l[r].value),"span",0)?o:On+Mt(o,"-start","")+o+On+"grid-row-span:"+(~zg(l,"span",0)?xl(l,/\d+/):+xl(l,/\d+/)-+xl(o,/\d+/))+";":On+Mt(o,"-start","")+o;case 4896:case 4128:return l&&l.some(function(d){return xl(d.props,/grid-\w+-start/)})?o:On+Mt(Mt(o,"-end","-span"),"span ","")+o;case 4095:case 3583:case 4068:case 2532:return Mt(o,/(.+)-inline(.+)/,Zt+"$1$2")+o;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(to(o)-1-r>6)switch(yr(o,r+1)){case 109:if(yr(o,r+4)!==45)break;case 102:return Mt(o,/(.+:)(.+)-([^]+)/,"$1"+Zt+"$2-$3$1"+hp+(yr(o,r+3)==108?"$3":"$2-$3"))+o;case 115:return~zg(o,"stretch",0)?y1(Mt(o,"stretch","fill-available"),r,l)+o:o}break;case 5152:case 5920:return Mt(o,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(d,h,b,S,y,E,$){return On+h+":"+b+$+(S?On+h+"-span:"+(y?E:+E-+b)+$:"")+o});case 4949:if(yr(o,r+6)===121)return Mt(o,":",":"+Zt)+o;break;case 6444:switch(yr(o,yr(o,14)===45?18:11)){case 120:return Mt(o,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,"$1"+Zt+(yr(o,14)===45?"inline-":"")+"box$3$1"+Zt+"$2$3$1"+On+"$2box$3")+o;case 100:return Mt(o,":",":"+On)+o}break;case 5719:case 2647:case 2135:case 3927:case 2391:return Mt(o,"scroll-","scroll-snap-")+o}return o}function Ug(o,r){for(var l="",d=0;d<o.length;d++)l+=r(o[d],d,o,r)||"";return l}function rM(o,r,l,d){switch(o.type){case VD:if(o.children.length)break;case UD:case HD:case vx:return o.return=o.return||o.value;case u1:return"";case c1:return o.return=o.value+"{"+Ug(o.children,d)+"}";case Lg:if(!to(o.value=o.props.join(",")))return""}return to(l=Ug(o.children,d))?o.return=o.value+"{"+l+"}":""}function iM(o){var r=p1(o);return function(l,d,h,b){for(var S="",y=0;y<r;y++)S+=o[y](l,d,h,b)||"";return S}}function aM(o){return function(r){r.root||(r=r.return)&&o(r)}}function oM(o,r,l,d){if(o.length>-1&&!o.return)switch(o.type){case vx:o.return=y1(o.value,o.length,l);return;case c1:return Ug([gs(o,{value:Mt(o.value,"@","@"+Zt)})],d);case Lg:if(o.length)return YD(l=o.props,function(h){switch(xl(h,d=/(::plac\w+|:read-\w+)/)){case":read-only":case":read-write":od(gs(o,{props:[Mt(h,/:(read-\w+)/,":"+hp+"$1")]})),od(gs(o,{props:[h]})),xx(o,{props:h1(l,d)});break;case"::placeholder":od(gs(o,{props:[Mt(h,/:(plac\w+)/,":"+Zt+"input-$1")]})),od(gs(o,{props:[Mt(h,/:(plac\w+)/,":"+hp+"$1")]})),od(gs(o,{props:[Mt(h,/:(plac\w+)/,On+"input-$1")]})),od(gs(o,{props:[h]})),xx(o,{props:h1(l,d)});break}return""})}}var lM={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},en={},Du=typeof process<"u"&&en!==void 0&&(en.REACT_APP_SC_ATTR||en.SC_ATTR)||"data-styled",x1="active",b1="data-styled-version",Hg="6.3.8",Sx=`/*!sc*/
`,Vg=typeof window<"u"&&typeof document<"u",Mu=$n.createContext===void 0,sM=!!(typeof SC_DISABLE_SPEEDY=="boolean"?SC_DISABLE_SPEEDY:typeof process<"u"&&en!==void 0&&en.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&en.REACT_APP_SC_DISABLE_SPEEDY!==""?en.REACT_APP_SC_DISABLE_SPEEDY!=="false"&&en.REACT_APP_SC_DISABLE_SPEEDY:typeof process<"u"&&en!==void 0&&en.SC_DISABLE_SPEEDY!==void 0&&en.SC_DISABLE_SPEEDY!==""?en.SC_DISABLE_SPEEDY!=="false"&&en.SC_DISABLE_SPEEDY:en.NODE_ENV!=="production"),w1=/invalid hook call/i,Wg=new Set,uM=function(o,r){if(en.NODE_ENV!=="production"){if(Mu)return;var l=r?' with the id of "'.concat(r,'"'):"",d="The component ".concat(o).concat(l,` has been created dynamically.
`)+`You may see this warning because you've called styled inside another component.
To resolve this only create new StyledComponents outside of any render method and function component.
See https://styled-components.com/docs/basics#define-styled-components-outside-of-the-render-method for more info.
`,h=console.error;try{var b=!0;console.error=function(S){for(var y=[],E=1;E<arguments.length;E++)y[E-1]=arguments[E];w1.test(S)?(b=!1,Wg.delete(d)):h.apply(void 0,rd([S],y,!1))},typeof $n.useState=="function"&&$n.useState(null),b&&!Wg.has(d)&&(console.warn(d),Wg.add(d))}catch(S){w1.test(S.message)&&Wg.delete(d)}finally{console.error=h}}},Yg=Object.freeze([]),ld=Object.freeze({});function cM(o,r,l){return l===void 0&&(l=ld),o.theme!==l.theme&&o.theme||r||l.theme}var Cx=new Set(["a","abbr","address","area","article","aside","audio","b","bdi","bdo","blockquote","body","button","br","canvas","caption","cite","code","col","colgroup","data","datalist","dd","del","details","dfn","dialog","div","dl","dt","em","embed","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","html","i","iframe","img","input","ins","kbd","label","legend","li","main","map","mark","menu","meter","nav","object","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","search","section","select","slot","small","span","strong","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","u","ul","var","video","wbr","circle","clipPath","defs","ellipse","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feDropShadow","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feImage","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence","filter","foreignObject","g","image","line","linearGradient","marker","mask","path","pattern","polygon","polyline","radialGradient","rect","stop","svg","switch","symbol","text","textPath","tspan","use"]),dM=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,fM=/(^-|-$)/g;function S1(o){return o.replace(dM,"-").replace(fM,"")}var pM=/(a)(d)/gi,C1=function(o){return String.fromCharCode(o+(o>25?39:97))};function Ex(o){var r,l="";for(r=Math.abs(o);r>52;r=r/52|0)l=C1(r%52)+l;return(C1(r%52)+l).replace(pM,"$1-$2")}var Tx,$u=function(o,r){for(var l=r.length;l;)o=33*o^r.charCodeAt(--l);return o},E1=function(o){return $u(5381,o)};function hM(o){return Ex(E1(o)>>>0)}function T1(o){return en.NODE_ENV!=="production"&&typeof o=="string"&&o||o.displayName||o.name||"Component"}function kx(o){return typeof o=="string"&&(en.NODE_ENV==="production"||o.charAt(0)===o.charAt(0).toLowerCase())}var k1=typeof Symbol=="function"&&Symbol.for,R1=k1?Symbol.for("react.memo"):60115,gM=k1?Symbol.for("react.forward_ref"):60112,mM={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},vM={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},D1={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},yM=((Tx={})[gM]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},Tx[R1]=D1,Tx);function M1(o){return("type"in(r=o)&&r.type.$$typeof)===R1?D1:"$$typeof"in o?yM[o.$$typeof]:mM;var r}var xM=Object.defineProperty,bM=Object.getOwnPropertyNames,$1=Object.getOwnPropertySymbols,wM=Object.getOwnPropertyDescriptor,SM=Object.getPrototypeOf,O1=Object.prototype;function A1(o,r,l){if(typeof r!="string"){if(O1){var d=SM(r);d&&d!==O1&&A1(o,d,l)}var h=bM(r);$1&&(h=h.concat($1(r)));for(var b=M1(o),S=M1(r),y=0;y<h.length;++y){var E=h[y];if(!(E in vM||l&&l[E]||S&&E in S||b&&E in b)){var $=wM(r,E);try{xM(o,E,$)}catch{}}}}return o}function sd(o){return typeof o=="function"}function Rx(o){return typeof o=="object"&&"styledComponentId"in o}function Ou(o,r){return o&&r?"".concat(o," ").concat(r):o||r||""}function j1(o,r){if(o.length===0)return"";for(var l=o[0],d=1;d<o.length;d++)l+=o[d];return l}function ud(o){return o!==null&&typeof o=="object"&&o.constructor.name===Object.name&&!("props"in o&&o.$$typeof)}function Dx(o,r,l){if(l===void 0&&(l=!1),!l&&!ud(o)&&!Array.isArray(o))return r;if(Array.isArray(r))for(var d=0;d<r.length;d++)o[d]=Dx(o[d],r[d]);else if(ud(r))for(var d in r)o[d]=Dx(o[d],r[d]);return o}function Mx(o,r){Object.defineProperty(o,"toString",{value:r})}var CM=en.NODE_ENV!=="production"?{1:`Cannot create styled-component for component: %s.

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
`,18:"ThemeProvider: Please make sure your useTheme hook is within a `<ThemeProvider>`"}:{};function EM(){for(var o=[],r=0;r<arguments.length;r++)o[r]=arguments[r];for(var l=o[0],d=[],h=1,b=o.length;h<b;h+=1)d.push(o[h]);return d.forEach(function(S){l=l.replace(/%[a-z]/,S)}),l}function cd(o){for(var r=[],l=1;l<arguments.length;l++)r[l-1]=arguments[l];return en.NODE_ENV==="production"?new Error("An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#".concat(o," for more information.").concat(r.length>0?" Args: ".concat(r.join(", ")):"")):new Error(EM.apply(void 0,rd([CM[o]],r,!1)).trim())}var TM=function(){function o(r){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=r}return o.prototype.indexOfGroup=function(r){for(var l=0,d=0;d<r;d++)l+=this.groupSizes[d];return l},o.prototype.insertRules=function(r,l){if(r>=this.groupSizes.length){for(var d=this.groupSizes,h=d.length,b=h;r>=b;)if((b<<=1)<0)throw cd(16,"".concat(r));this.groupSizes=new Uint32Array(b),this.groupSizes.set(d),this.length=b;for(var S=h;S<b;S++)this.groupSizes[S]=0}for(var y=this.indexOfGroup(r+1),E=(S=0,l.length);S<E;S++)this.tag.insertRule(y,l[S])&&(this.groupSizes[r]++,y++)},o.prototype.clearGroup=function(r){if(r<this.length){var l=this.groupSizes[r],d=this.indexOfGroup(r),h=d+l;this.groupSizes[r]=0;for(var b=d;b<h;b++)this.tag.deleteRule(d)}},o.prototype.getGroup=function(r){var l="";if(r>=this.length||this.groupSizes[r]===0)return l;for(var d=this.groupSizes[r],h=this.indexOfGroup(r),b=h+d,S=h;S<b;S++)l+="".concat(this.tag.getRule(S)).concat(Sx);return l},o}(),kM=1<<30,Gg=new Map,Kg=new Map,Qg=1,vp=function(o){if(Gg.has(o))return Gg.get(o);for(;Kg.has(Qg);)Qg++;var r=Qg++;if(en.NODE_ENV!=="production"&&((0|r)<0||r>kM))throw cd(16,"".concat(r));return Gg.set(o,r),Kg.set(r,o),r},RM=function(o,r){Qg=r+1,Gg.set(o,r),Kg.set(r,o)},DM="style[".concat(Du,"][").concat(b1,'="').concat(Hg,'"]'),MM=new RegExp("^".concat(Du,'\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)')),$M=function(o,r,l){for(var d,h=l.split(","),b=0,S=h.length;b<S;b++)(d=h[b])&&o.registerName(r,d)},OM=function(o,r){for(var l,d=((l=r.textContent)!==null&&l!==void 0?l:"").split(Sx),h=[],b=0,S=d.length;b<S;b++){var y=d[b].trim();if(y){var E=y.match(MM);if(E){var $=0|parseInt(E[1],10),M=E[2];$!==0&&(RM(M,$),$M(o,M,E[3]),o.getTag().insertRules($,h)),h.length=0}else h.push(y)}}},_1=function(o){for(var r=document.querySelectorAll(DM),l=0,d=r.length;l<d;l++){var h=r[l];h&&h.getAttribute(Du)!==x1&&(OM(o,h),h.parentNode&&h.parentNode.removeChild(h))}};function AM(){return typeof __webpack_nonce__<"u"?__webpack_nonce__:null}var L1=function(o){var r=document.head,l=o||r,d=document.createElement("style"),h=function(y){var E=Array.from(y.querySelectorAll("style[".concat(Du,"]")));return E[E.length-1]}(l),b=h!==void 0?h.nextSibling:null;d.setAttribute(Du,x1),d.setAttribute(b1,Hg);var S=AM();return S&&d.setAttribute("nonce",S),l.insertBefore(d,b),d},jM=function(){function o(r){this.element=L1(r),this.element.appendChild(document.createTextNode("")),this.sheet=function(l){if(l.sheet)return l.sheet;for(var d=document.styleSheets,h=0,b=d.length;h<b;h++){var S=d[h];if(S.ownerNode===l)return S}throw cd(17)}(this.element),this.length=0}return o.prototype.insertRule=function(r,l){try{return this.sheet.insertRule(l,r),this.length++,!0}catch{return!1}},o.prototype.deleteRule=function(r){this.sheet.deleteRule(r),this.length--},o.prototype.getRule=function(r){var l=this.sheet.cssRules[r];return l&&l.cssText?l.cssText:""},o}(),_M=function(){function o(r){this.element=L1(r),this.nodes=this.element.childNodes,this.length=0}return o.prototype.insertRule=function(r,l){if(r<=this.length&&r>=0){var d=document.createTextNode(l);return this.element.insertBefore(d,this.nodes[r]||null),this.length++,!0}return!1},o.prototype.deleteRule=function(r){this.element.removeChild(this.nodes[r]),this.length--},o.prototype.getRule=function(r){return r<this.length?this.nodes[r].textContent:""},o}(),LM=function(){function o(r){this.rules=[],this.length=0}return o.prototype.insertRule=function(r,l){return r<=this.length&&(this.rules.splice(r,0,l),this.length++,!0)},o.prototype.deleteRule=function(r){this.rules.splice(r,1),this.length--},o.prototype.getRule=function(r){return r<this.length?this.rules[r]:""},o}(),z1=Vg,zM={isServer:!Vg,useCSSOMInjection:!sM},N1=function(){function o(r,l,d){r===void 0&&(r=ld),l===void 0&&(l={});var h=this;this.options=Wi(Wi({},zM),r),this.gs=l,this.names=new Map(d),this.server=!!r.isServer,!this.server&&Vg&&z1&&(z1=!1,_1(this)),Mx(this,function(){return function(b){for(var S=b.getTag(),y=S.length,E="",$=function(N){var j=function(Ee){return Kg.get(Ee)}(N);if(j===void 0)return"continue";var H=b.names.get(j),L=S.getGroup(N);if(H===void 0||!H.size||L.length===0)return"continue";var K="".concat(Du,".g").concat(N,'[id="').concat(j,'"]'),de="";H!==void 0&&H.forEach(function(Ee){Ee.length>0&&(de+="".concat(Ee,","))}),E+="".concat(L).concat(K,'{content:"').concat(de,'"}').concat(Sx)},M=0;M<y;M++)$(M);return E}(h)})}return o.registerId=function(r){return vp(r)},o.prototype.rehydrate=function(){!this.server&&Vg&&_1(this)},o.prototype.reconstructWithOptions=function(r,l){return l===void 0&&(l=!0),new o(Wi(Wi({},this.options),r),this.gs,l&&this.names||void 0)},o.prototype.allocateGSInstance=function(r){return this.gs[r]=(this.gs[r]||0)+1},o.prototype.getTag=function(){return this.tag||(this.tag=(r=function(l){var d=l.useCSSOMInjection,h=l.target;return l.isServer?new LM(h):d?new jM(h):new _M(h)}(this.options),new TM(r)));var r},o.prototype.hasNameForId=function(r,l){return this.names.has(r)&&this.names.get(r).has(l)},o.prototype.registerName=function(r,l){if(vp(r),this.names.has(r))this.names.get(r).add(l);else{var d=new Set;d.add(l),this.names.set(r,d)}},o.prototype.insertRules=function(r,l,d){this.registerName(r,l),this.getTag().insertRules(vp(r),d)},o.prototype.clearNames=function(r){this.names.has(r)&&this.names.get(r).clear()},o.prototype.clearRules=function(r){this.getTag().clearGroup(vp(r)),this.clearNames(r)},o.prototype.clearTag=function(){this.tag=void 0},o}(),NM=/&/g,dd=47;function P1(o){if(o.indexOf("}")===-1)return!1;for(var r=o.length,l=0,d=0,h=!1,b=0;b<r;b++){var S=o.charCodeAt(b);if(d!==0||h||S!==dd||o.charCodeAt(b+1)!==42)if(h)S===42&&o.charCodeAt(b+1)===dd&&(h=!1,b++);else if(S!==34&&S!==39||b!==0&&o.charCodeAt(b-1)===92){if(d===0){if(S===123)l++;else if(S===125&&--l<0)return!0}}else d===0?d=S:d===S&&(d=0);else h=!0,b++}return l!==0||d!==0}function F1(o,r){return o.map(function(l){return l.type==="rule"&&(l.value="".concat(r," ").concat(l.value),l.value=l.value.replaceAll(",",",".concat(r," ")),l.props=l.props.map(function(d){return"".concat(r," ").concat(d)})),Array.isArray(l.children)&&l.type!=="@keyframes"&&(l.children=F1(l.children,r)),l})}function PM(o){var r,l,d,h=ld,b=h.options,S=b===void 0?ld:b,y=h.plugins,E=y===void 0?Yg:y,$=function(j,H,L){return L.startsWith(l)&&L.endsWith(l)&&L.replaceAll(l,"").length>0?".".concat(r):j},M=E.slice();M.push(function(j){j.type===Lg&&j.value.includes("&")&&(j.props[0]=j.props[0].replace(NM,l).replace(d,$))}),S.prefix&&M.push(oM),M.push(rM);var N=function(j,H,L,K){H===void 0&&(H=""),L===void 0&&(L=""),K===void 0&&(K="&"),r=K,l=H,d=new RegExp("\\".concat(l,"\\b"),"g");var de=function(ae){if(!P1(ae))return ae;for(var ce=ae.length,Te="",le=0,ue=0,Ae=0,ft=!1,Ve=0;Ve<ce;Ve++){var Tt=ae.charCodeAt(Ve);if(Ae!==0||ft||Tt!==dd||ae.charCodeAt(Ve+1)!==42)if(ft)Tt===42&&ae.charCodeAt(Ve+1)===dd&&(ft=!1,Ve++);else if(Tt!==34&&Tt!==39||Ve!==0&&ae.charCodeAt(Ve-1)===92){if(Ae===0)if(Tt===123)ue++;else if(Tt===125){if(--ue<0){for(var bt=Ve+1;bt<ce;){var rt=ae.charCodeAt(bt);if(rt===59||rt===10)break;bt++}bt<ce&&ae.charCodeAt(bt)===59&&bt++,ue=0,Ve=bt-1,le=bt;continue}ue===0&&(Te+=ae.substring(le,Ve+1),le=Ve+1)}else Tt===59&&ue===0&&(Te+=ae.substring(le,Ve+1),le=Ve+1)}else Ae===0?Ae=Tt:Ae===Tt&&(Ae=0);else ft=!0,Ve++}if(le<ce){var He=ae.substring(le);P1(He)||(Te+=He)}return Te}(function(ae){if(ae.indexOf("//")===-1)return ae;for(var ce=ae.length,Te=[],le=0,ue=0,Ae=0,ft=0;ue<ce;){var Ve=ae.charCodeAt(ue);if(Ve!==34&&Ve!==39||ue!==0&&ae.charCodeAt(ue-1)===92)if(Ae===0)if(Ve===40&&ue>=3&&(32|ae.charCodeAt(ue-1))==108&&(32|ae.charCodeAt(ue-2))==114&&(32|ae.charCodeAt(ue-3))==117)ft=1,ue++;else if(ft>0)Ve===41?ft--:Ve===40&&ft++,ue++;else if(Ve===dd&&ue+1<ce&&ae.charCodeAt(ue+1)===dd){for(ue>le&&Te.push(ae.substring(le,ue));ue<ce&&ae.charCodeAt(ue)!==10;)ue++;le=ue}else ue++;else ue++;else Ae===0?Ae=Ve:Ae===Ve&&(Ae=0),ue++}return le===0?ae:(le<ce&&Te.push(ae.substring(le)),Te.join(""))}(j)),Ee=tM(L||H?"".concat(L," ").concat(H," { ").concat(de," }"):de);S.namespace&&(Ee=F1(Ee,S.namespace));var pe=[];return Ug(Ee,iM(M.concat(aM(function(ae){return pe.push(ae)})))),pe};return N.hash=E.length?E.reduce(function(j,H){return H.name||cd(15),$u(j,H.name)},5381).toString():"",N}var FM=new N1,$x=PM(),Ox={shouldForwardProp:void 0,styleSheet:FM,stylis:$x},B1=Mu?{Provider:function(o){return o.children},Consumer:function(o){return(0,o.children)(Ox)}}:$n.createContext(Ox);B1.Consumer,Mu||$n.createContext(void 0);function I1(){return Mu?Ox:$n.useContext(B1)}var U1=function(){function o(r,l){var d=this;this.inject=function(h,b){b===void 0&&(b=$x);var S=d.name+b.hash;h.hasNameForId(d.id,S)||h.insertRules(d.id,S,b(d.rules,S,"@keyframes"))},this.name=r,this.id="sc-keyframes-".concat(r),this.rules=l,Mx(this,function(){throw cd(12,String(d.name))})}return o.prototype.getName=function(r){return r===void 0&&(r=$x),this.name+r.hash},o}();function BM(o,r){return r==null||typeof r=="boolean"||r===""?"":typeof r!="number"||r===0||o in lM||o.startsWith("--")?String(r).trim():"".concat(r,"px")}var IM=function(o){return o>="A"&&o<="Z"};function H1(o){for(var r="",l=0;l<o.length;l++){var d=o[l];if(l===1&&d==="-"&&o[0]==="-")return o;IM(d)?r+="-"+d.toLowerCase():r+=d}return r.startsWith("ms-")?"-"+r:r}var V1=function(o){return o==null||o===!1||o===""},W1=function(o){var r=[];for(var l in o){var d=o[l];o.hasOwnProperty(l)&&!V1(d)&&(Array.isArray(d)&&d.isCss||sd(d)?r.push("".concat(H1(l),":"),d,";"):ud(d)?r.push.apply(r,rd(rd(["".concat(l," {")],W1(d),!1),["}"],!1)):r.push("".concat(H1(l),": ").concat(BM(l,d),";")))}return r};function Au(o,r,l,d){if(V1(o))return[];if(Rx(o))return[".".concat(o.styledComponentId)];if(sd(o)){if(!sd(b=o)||b.prototype&&b.prototype.isReactComponent||!r)return[o];var h=o(r);return en.NODE_ENV==="production"||typeof h!="object"||Array.isArray(h)||h instanceof U1||ud(h)||h===null||console.error("".concat(T1(o)," is not a styled component and cannot be referred to via component selector. See https://www.styled-components.com/docs/advanced#referring-to-other-components for more details.")),Au(h,r,l,d)}var b;return o instanceof U1?l?(o.inject(l,d),[o.getName(d)]):[o]:ud(o)?W1(o):Array.isArray(o)?Array.prototype.concat.apply(Yg,o.map(function(S){return Au(S,r,l,d)})):[o.toString()]}function UM(o){for(var r=0;r<o.length;r+=1){var l=o[r];if(sd(l)&&!Rx(l))return!1}return!0}var HM=E1(Hg),VM=function(){function o(r,l,d){this.rules=r,this.staticRulesId="",this.isStatic=en.NODE_ENV==="production"&&(d===void 0||d.isStatic)&&UM(r),this.componentId=l,this.baseHash=$u(HM,l),this.baseStyle=d,N1.registerId(l)}return o.prototype.generateAndInjectStyles=function(r,l,d){var h=this.baseStyle?this.baseStyle.generateAndInjectStyles(r,l,d).className:"";if(this.isStatic&&!d.hash)if(this.staticRulesId&&l.hasNameForId(this.componentId,this.staticRulesId))h=Ou(h,this.staticRulesId);else{var b=j1(Au(this.rules,r,l,d)),S=Ex($u(this.baseHash,b)>>>0);if(!l.hasNameForId(this.componentId,S)){var y=d(b,".".concat(S),void 0,this.componentId);l.insertRules(this.componentId,S,y)}h=Ou(h,S),this.staticRulesId=S}else{for(var E=$u(this.baseHash,d.hash),$="",M=0;M<this.rules.length;M++){var N=this.rules[M];if(typeof N=="string")$+=N,en.NODE_ENV!=="production"&&(E=$u(E,N));else if(N){var j=j1(Au(N,r,l,d));E=$u(E,j+M),$+=j}}if($){var H=Ex(E>>>0);if(!l.hasNameForId(this.componentId,H)){var L=d($,".".concat(H),void 0,this.componentId);l.insertRules(this.componentId,H,L)}h=Ou(h,H)}}return{className:h,css:typeof window>"u"?l.getTag().getGroup(vp(this.componentId)):""}},o}(),Y1=Mu?{Provider:function(o){return o.children},Consumer:function(o){return(0,o.children)(void 0)}}:$n.createContext(void 0);Y1.Consumer;var Ax={},G1=new Set;function WM(o,r,l){var d=Rx(o),h=o,b=!kx(o),S=r.attrs,y=S===void 0?Yg:S,E=r.componentId,$=E===void 0?function(ce,Te){var le=typeof ce!="string"?"sc":S1(ce);Ax[le]=(Ax[le]||0)+1;var ue="".concat(le,"-").concat(hM(Hg+le+Ax[le]));return Te?"".concat(Te,"-").concat(ue):ue}(r.displayName,r.parentComponentId):E,M=r.displayName,N=M===void 0?function(ce){return kx(ce)?"styled.".concat(ce):"Styled(".concat(T1(ce),")")}(o):M,j=r.displayName&&r.componentId?"".concat(S1(r.displayName),"-").concat(r.componentId):r.componentId||$,H=d&&h.attrs?h.attrs.concat(y).filter(Boolean):y,L=r.shouldForwardProp;if(d&&h.shouldForwardProp){var K=h.shouldForwardProp;if(r.shouldForwardProp){var de=r.shouldForwardProp;L=function(ce,Te){return K(ce,Te)&&de(ce,Te)}}else L=K}var Ee=new VM(l,j,d?h.componentStyle:void 0);function pe(ce,Te){return function(le,ue,Ae){var ft=le.attrs,Ve=le.componentStyle,Tt=le.defaultProps,bt=le.foldedComponentIds,rt=le.styledComponentId,He=le.target,Ht=Mu?void 0:$n.useContext(Y1),kt=I1(),pt=le.shouldForwardProp||kt.shouldForwardProp;en.NODE_ENV!=="production"&&$n.useDebugValue&&$n.useDebugValue(rt);var se=cM(ue,Ht,Tt)||ld,Re=function(tt,vt,It){for(var pn,an=Wi(Wi({},vt),{className:void 0,theme:It}),Pn=0;Pn<tt.length;Pn+=1){var xn=sd(pn=tt[Pn])?pn(an):pn;for(var An in xn)An==="className"?an.className=Ou(an.className,xn[An]):An==="style"?an.style=Wi(Wi({},an.style),xn[An]):an[An]=xn[An]}return"className"in vt&&typeof vt.className=="string"&&(an.className=Ou(an.className,vt.className)),an}(ft,ue,se),be=Re.as||He,V={};for(var re in Re)Re[re]===void 0||re[0]==="$"||re==="as"||re==="theme"&&Re.theme===se||(re==="forwardedAs"?V.as=Re.forwardedAs:pt&&!pt(re,be)||(V[re]=Re[re],pt||en.NODE_ENV!=="development"||ID(re)||G1.has(re)||!Cx.has(be)||(G1.add(re),console.warn('styled-components: it looks like an unknown prop "'.concat(re,'" is being sent through to the DOM, which will likely trigger a React console error. If you would like automatic filtering of unknown props, you can opt-into that behavior via `<StyleSheetManager shouldForwardProp={...}>` (connect an API like `@emotion/is-prop-valid`) or consider using transient props (`$` prefix for automatic filtering.)')))));var We=function(tt,vt){var It=I1(),pn=tt.generateAndInjectStyles(vt,It.styleSheet,It.stylis);return en.NODE_ENV!=="production"&&$n.useDebugValue&&$n.useDebugValue(pn.className),pn}(Ve,Re),et=We.className,it=We.css;en.NODE_ENV!=="production"&&le.warnTooManyClasses&&le.warnTooManyClasses(et);var ht=Ou(bt,rt);et&&(ht+=" "+et),Re.className&&(ht+=" "+Re.className),V[kx(be)&&!Cx.has(be)?"class":"className"]=ht,Ae&&(V.ref=Ae);var $t=Ge.createElement(be,V);return Mu&&it?$n.createElement($n.Fragment,null,$n.createElement("style",{precedence:"styled-components",href:"sc-".concat(rt,"-").concat(et),children:it}),$t):$t}(ae,ce,Te)}pe.displayName=N;var ae=$n.forwardRef(pe);return ae.attrs=H,ae.componentStyle=Ee,ae.displayName=N,ae.shouldForwardProp=L,ae.foldedComponentIds=d?Ou(h.foldedComponentIds,h.styledComponentId):"",ae.styledComponentId=j,ae.target=d?h.target:o,Object.defineProperty(ae,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(ce){this._foldedDefaultProps=d?function(Te){for(var le=[],ue=1;ue<arguments.length;ue++)le[ue-1]=arguments[ue];for(var Ae=0,ft=le;Ae<ft.length;Ae++)Dx(Te,ft[Ae],!0);return Te}({},h.defaultProps,ce):ce}}),en.NODE_ENV!=="production"&&(uM(N,j),ae.warnTooManyClasses=function(ce,Te){var le={},ue=!1;return function(Ae){if(!ue&&(le[Ae]=!0,Object.keys(le).length>=200)){var ft=Te?' with the id of "'.concat(Te,'"'):"";console.warn("Over ".concat(200," classes were generated for component ").concat(ce).concat(ft,`.
`)+`Consider using the attrs method, together with a style object for frequently changed styles.
Example:
  const Component = styled.div.attrs(props => ({
    style: {
      background: props.background,
    },
  }))\`width: 100%;\`

  <Component />`),ue=!0,le={}}}}(N,j)),Mx(ae,function(){return".".concat(ae.styledComponentId)}),b&&A1(ae,o,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),ae}function K1(o,r){for(var l=[o[0]],d=0,h=r.length;d<h;d+=1)l.push(r[d],o[d+1]);return l}var Q1=function(o){return Object.assign(o,{isCss:!0})};function fd(o){for(var r=[],l=1;l<arguments.length;l++)r[l-1]=arguments[l];if(sd(o)||ud(o))return Q1(Au(K1(Yg,rd([o],r,!0))));var d=o;return r.length===0&&d.length===1&&typeof d[0]=="string"?Au(d):Q1(Au(K1(d,r)))}function jx(o,r,l){if(l===void 0&&(l=ld),!r)throw cd(1,r);var d=function(h){for(var b=[],S=1;S<arguments.length;S++)b[S-1]=arguments[S];return o(r,l,fd.apply(void 0,rd([h],b,!1)))};return d.attrs=function(h){return jx(o,r,Wi(Wi({},l),{attrs:Array.prototype.concat(l.attrs,h).filter(Boolean)}))},d.withConfig=function(h){return jx(o,r,Wi(Wi({},l),h))},d}var q1=function(o){return jx(WM,o)},D=q1;Cx.forEach(function(o){D[o]=q1(o)}),en.NODE_ENV!=="production"&&typeof navigator<"u"&&navigator.product==="ReactNative"&&console.warn(`It looks like you've imported 'styled-components' on React Native.
Perhaps you're looking to import 'styled-components/native'?
Read more about this at https://www.styled-components.com/docs/basics#react-native`);var qg="__sc-".concat(Du,"__");en.NODE_ENV!=="production"&&en.NODE_ENV!=="test"&&typeof window<"u"&&(window[qg]||(window[qg]=0),window[qg]===1&&console.warn(`It looks like there are several instances of 'styled-components' initialized in this application. This may cause dynamic styles to not render properly, errors during the rehydration process, a missing theme prop, and makes your application bigger without good reason.

See https://styled-components.com/docs/faqs#why-am-i-getting-a-warning-about-several-instances-of-module-on-the-page for more info.`),window[qg]+=1);const A={colors:{windowBg:"#152029de",panelBg:"#04161c",panelBgGlass:"rgba(4, 22, 28, 0.22)",line:"#496791",text:"#ffffff",textAccent:"#C6E2FF",textDim:"#7f9bb8",healthOk:"#427231",healthCrit:"#ed6738",warning:"#e1b000",warningSoft:"#e6b400",statusOk:"limegreen",statusAlert:"#e1b000",statusBad:"red",statusPending:"#00b8e6",enhText:"#d8be86",enhTitle:"#e8cf93",enhBg:"rgba(169, 128, 56, 0.30)",enhLine:"#8a6d3b",greenBg:"rgba(8, 28, 12, 0.92)",greenLine:"#3f8a3f",greenTitleBg:"#16401b",greenText:"#e6ffe6",greenLabel:"#bdf0bd",greenBtnBg:"#1b5e20",greenBtnLine:"#2e7d32",greenBtnLineHover:"#66bb6a",greenGlow:"#4caf50",custom:"#cccc00",chromeText:"#deebff",overlayBg:"black",overlayBgSoft:"rgba(0, 0, 0, 0.65)"},fonts:{body:"arial",mono:'Consolas, "Lucida Console", monospace',display:'"Orbitron", sans-serif'},radii:{modal:"0px",tooltip:"7px"},hud:{btn:"36px",btnSmall:"25px",gap:"10px",gapSmall:"5px",icon:"26px",iconSmall:"17px",glyph:"30px",glyphSmall:"20px"}},YM=D.div`
    border: 1px solid ${A.colors.line};
    color: ${A.colors.chromeText};
    background-color: ${A.colors.windowBg};
    font-family:${A.fonts.body};
`,GM=D.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,yp=D(YM)`
    box-shadow: 5px 5px 10px ${A.colors.overlayBg};
`,KM=D.span`
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
`;D(KM)`
    font-weight: normal;
`;const pa=fd`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;class ha extends Ge.Component{render(){return m.jsxs(QM,{children:[m.jsx(qM,{children:this.props.label}),m.jsx(XM,{type:this.props.type||"text",value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:"0"})]})}}const QM=D.div`
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
`,qM=D.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,XM=D.input`
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
`;class JM extends Ge.Component{render(){const{children:r,className:l}=this.props;return m.jsx("div",{className:l,children:r})}}const X1=D(JM)`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${A.fonts.body};
    font-size:12px;
    color:${A.colors.text};
    background-color:${A.colors.overlayBgSoft};
    border-radius: ${A.radii.tooltip};
    -moz-border-radius: ${A.radii.tooltip};
    -webkit-border-radius: ${A.radii.tooltip};
    padding:3px 3px 3px 3px;
    padding-bottom: 8px;
`,J1=D.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,Z1=D.div`
    color: ${o=>o.$type=="good"?"#6fc126;":o.$type=="bad"?"#ff7b3f;":"white;"}
    font-weight: ${o=>o.$important?"bold":"inherit"};
    font-size: ${o=>o.$important?"14px":"12px"};
    margin-top: ${o=>o.$space?"14px":"0"};
`;class ZM extends Ge.Component{getOnChange(r){return l=>{this.props.set(r,l.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(r){return l=>{if(console.log("keydown"),l.preventDefault(),l.stopPropagation(),!eC[l.keyCode])return;const d={keyCode:l.keyCode,shiftKey:l.shiftKey,altKey:l.altKey,ctrlKey:l.ctrlKey,metaKey:l.metaKey};this.props.set(r,d),this.props.save(),this.forceUpdate()}}getKey(r){return console.log(this.props.settings),e$(this.props.settings[r])}get(r){return this.props.settings[r]}render(){return m.jsx(t$,{onClick:this.props.close,children:m.jsxs(n$,{onClick:r=>r.stopPropagation(),children:[m.jsxs(r$,{children:[m.jsx(i$,{children:"Player Settings"}),m.jsx(a$,{onClick:this.props.close,title:"Close",children:"✕"})]}),m.jsxs(o$,{children:[m.jsx(l$,{children:"Settings apply to this browser and device only. Reload the page for changes to take effect."}),m.jsx(Xg,{children:"Keys"}),m.jsx(ha,{label:"Display ALL Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllEW"),value:this.getKey.call(this,"ShowAllEW")}),m.jsx(ha,{label:"Display FRIENDLY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyEW"),value:this.getKey.call(this,"ShowFriendlyEW")}),m.jsx(ha,{label:"Display ENEMY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyEW"),value:this.getKey.call(this,"ShowEnemyEW")}),m.jsx(ha,{label:"Display ALL Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllBallistics"),value:this.getKey.call(this,"ShowAllBallistics")}),m.jsx(ha,{label:"Display FRIENDLY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyBallistics"),value:this.getKey.call(this,"ShowFriendlyBallistics")}),m.jsx(ha,{label:"Display ENEMY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyBallistics"),value:this.getKey.call(this,"ShowEnemyBallistics")}),m.jsx(ha,{label:"Toggle RULER tool",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleLoS"),value:this.getKey.call(this,"ToggleLoS")}),m.jsx(ha,{label:"Toggle HEX numbers",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleHexNumbers"),value:this.getKey.call(this,"ToggleHexNumbers")}),m.jsx(ha,{label:"Toggle MAP background",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleBackground"),value:this.getKey.call(this,"ToggleBackground")}),m.jsx(ha,{label:"Close all SHIP WINDOWS and tooltips",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"CloseAllWindows"),value:this.getKey.call(this,"CloseAllWindows")}),m.jsx(Xg,{children:"Replay"}),m.jsx(ha,{label:"Play / pause Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"TogglePlayPause"),value:this.getKey.call(this,"TogglePlayPause")}),m.jsx(Xg,{children:"Sound"}),m.jsx(ha,{label:"Toggle sound in Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleSound"),value:this.getKey.call(this,"ToggleSound")}),m.jsx(Xg,{children:"Visual"}),m.jsx(ha,{placeholder:"0",type:"number",label:"Zoom level to switch to strategic view",onChange:this.getOnChange.call(this,"ZoomLevelToStrategic"),value:this.get.call(this,"ZoomLevelToStrategic")}),m.jsx(s$,{children:"Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners."})]})]})})}}const e$=o=>{let r=eC[o.keyCode];return r=r.toUpperCase(),o.shiftKey&&(r+=" + shift"),o.altKey&&(r+=" + alt"),o.ctrlKey&&(r+=" + ctrl"),o.metaKey&&(r+=" + cmd"),r},t$=D(GM)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,n$=D.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: ${A.colors.panelBg};
    border: 1px solid ${A.colors.line};
    border-radius: ${A.radii.modal};
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: ${A.colors.chromeText};
    font-family: ${A.fonts.body};
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`,r$=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${A.colors.windowBg};
    border-bottom: 1px solid ${A.colors.line};
`,i$=D.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,a$=D.div`
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
    ${pa}

    &:hover {
        color: #d6f7fd;
        border-color: #49c4d4;
        background-color: rgba(73, 196, 212, 0.12);
    }
`,o$=D.div`
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
`,l$=D.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`,Xg=D.div`
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
`,s$=D.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,eC={27:"esc",32:"space",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",58:":",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z"};class u$ extends Ge.Component{constructor(r){super(r),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?m.jsx(ZM,{close:this.close.bind(this),...this.props}):m.jsx(c$,{onClick:this.open.bind(this),children:"⚙"})}}const c$=D(yp)`
    width: ${A.hud.btn};
    height: ${A.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${A.hud.glyph};
    border-right: none;
    border-top: none;
    ${pa}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${A.hud.btnSmall};
        height: ${A.hud.btnSmall};
        font-size: ${A.hud.glyphSmall};

    }
`,tC="fv-thrust-relayout",vs=8,Jg=6,pd=40,xp=60,hd=80,d$=4,f$=(o,r,l)=>{const d=window.coordinateConverter&&window.coordinateConverter.zoom||1,h=Math.min((o.canvasSize||200)*.75,250)/2/d+d$,b=Math.min(h,hd+pd),S=j=>l.type==="roll"||!Array.isArray(r)||r[j]!==null,y=j=>S(j)?shipManager.systems.getThrusters(o,j).length:0,E=Math.max(y(3),y(4))*pd/2,$=Math.max(y(1),y(2))*pd/2;let M=Math.min(h,xp),N=Math.min(h,hd);if(M<E&&N<$){const j=E<=xp?E-M:1/0,H=$<=hd?$-N:1/0;if(j===1/0&&H===1/0)return{x:xp,y:hd,clear:b};j<=H?M=E:N=$}return{x:M,y:N,clear:b}},nC=D.span`
    font-family: ${A.fonts.mono};
    font-size: 11px;
    line-height: 1;
    padding: 1px 2px;
    color: ${o=>o.$over?A.colors.healthCrit:A.colors.text};
    background-color: rgba(0, 0, 0, 0.55);
    pointer-events: none;
`,p$=D.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    position: relative; // Needed for absolute positioning of ::before
    box-sizing: border-box;
    ${o=>o.$box==="need"?`box-shadow: inset 0 0 0 1px ${A.colors.warning};`:o.$box==="extra"?`box-shadow: inset 0 0 0 1px ${A.colors.greenBtnLineHover};`:""}

    &::before {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: -1;
        /* thrusterGreen*.png: the ship window's blue thruster1*.png with only the blue body repainted
           in the panel's green (theme.colors.greenLine, shading kept) - the red critical bands are
           untouched, which a CSS hue-rotate could not do (it turned them purple). */
        background-image: ${o=>{switch(o.$crits){case 11:return"url(img/systemicons/thrusterGreen-critical12.png);";case 10:return"url(img/systemicons/thrusterGreen-critical1.png);";case 1:return"url(img/systemicons/thrusterGreen-critical2.png);";default:return"url(img/systemicons/thrusterGreen.png);"}}}
        background-size: cover;
        transform: ${o=>{switch(o.$direction){case 4:return"rotate(180deg)";case 1:return"rotate(90deg)";case 2:return"rotate(270deg)";default:return"none"}}};
        ${o=>o.$destroyed?"opacity: 0.3;":""}
    }

    ${o=>o.$destroyed?`
    cursor: default;
    &::after {
        content: "✕";
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        line-height: 1;
        color: ${A.colors.statusBad};
        pointer-events: none;
    }`:pa}
`,rC=D.div`
    display: flex;
    position: absolute;
`,iC=D(rC)`
    flex-direction: row;
    left: var(--fv-ring-x, ${xp}px);
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: ${pd}px;
`,h$=D(iC)`
    left: calc(-1 * var(--fv-ring-x, ${xp}px) - ${pd}px);
`,aC=D(rC)`
    flex-direction: row;
    top: calc(-1 * var(--fv-ring-y, ${hd}px) - ${pd}px);
    transform: translate(-50%, 0);
`,g$=D(aC)`
    top: var(--fv-ring-y, ${hd}px);
`,m$=D.div`
    position: relative;
    transform: rotate(${o=>o.$rotation}deg);

    & ${nC} {
        transform: rotate(${o=>-o.$rotation}deg);
    }
`,v$=D.div`
    position: absolute;
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
`,y$=D.div`
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 180px;
    box-sizing: border-box;
    background-color: ${A.colors.greenBg};
    border: 1px solid ${A.colors.greenLine};
    box-shadow: 5px 5px 10px ${A.colors.overlayBg};
    font-family: ${A.fonts.body};
    color: ${A.colors.greenText};
    text-align: left;
    user-select: none;
`,x$=D.div`
    box-sizing: border-box;
    padding: 3px 6px;
    line-height: 1.2;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    white-space: pre;
    overflow: hidden;
    text-overflow: ellipsis;
    color: ${A.colors.greenText};
    background-color: ${A.colors.greenTitleBg};
    border-bottom: 1px solid ${A.colors.greenLine};
`,b$=D.div`
    padding: 3px 6px 4px;
`,ju=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px;
    padding: 1px 0;
`,_u=D.span`
    font-size: 10px;
    color: ${A.colors.greenLabel};
    white-space: nowrap;
`,oC=()=>window.UI&&window.UI.shipMovement&&window.UI.shipMovement.extendedTurnColour||"#ff8c00",Lu=D.span`
    font-family: ${A.fonts.mono};
    font-size: 11px;
    white-space: nowrap;
    color: ${o=>o.$state==="ok"?A.colors.statusOk:o.$state==="open"?A.colors.warning:o.$state==="owed"?oC():A.colors.greenText};
`,_x=D.div`
    padding: 2px 0 1px;
    font-size: 10px;
    line-height: 1.25;
    color: ${A.colors.greenLabel};
`,w$=D.div`
    position: relative;
    height: 4px;
    margin: 2px 0 3px;
    background-color: rgba(0, 0, 0, 0.45);
    border: 1px solid ${A.colors.greenLine};
`,lC=D.div`
    position: absolute;
    top: 0;
    bottom: 0;
    background-color: ${o=>o.$owed?oC():A.colors.statusOk};
`,S$=D.div`
    position: absolute;
    top: -3px;
    bottom: -3px;
    width: 1px;
    background-color: ${A.colors.greenText};
`,C$=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 3px;
    min-height: 16px;
    font-size: 10px;
    color: ${A.colors.greenText};
    cursor: ${o=>o.$locked?"not-allowed":"pointer"};

    @media (pointer: coarse) {
        min-height: 32px;
    }
`,E$=D.span`
    position: relative;
    flex: 0 0 auto;
    width: 10px;
    height: 10px;
    box-sizing: border-box;
    border: 1px solid ${o=>o.$locked?A.colors.greenLine:A.colors.greenBtnLineHover};
    background-color: ${o=>o.$checked?A.colors.greenBtnLine:"rgba(0, 0, 0, 0.45)"};

    ${o=>o.$checked&&`
    &::after {
        content: "";
        position: absolute;
        left: 2.5px;
        top: 0;
        width: 3px;
        height: 6px;
        border-right: 1.5px solid ${A.colors.greenText};
        border-bottom: 1.5px solid ${A.colors.greenText};
        transform: rotate(45deg);
    }`}
`,T$=D.div`
    height: 1px;
    margin: 3px 0;
    background-color: ${A.colors.greenLine};
`,k$=D.div`
    display: flex;
    gap: 4px;
    padding: 0 6px 6px;
`,sC=D.div`
    flex: 1 1 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    height: 20px;
    padding: 0 4px;
    font-size: 11px;
    white-space: nowrap;
    cursor: pointer;
    background: ${A.colors.greenBtnBg};
    border: 1px solid ${A.colors.greenBtnLine};
    color: ${A.colors.greenText};

    &:hover {
        background: ${A.colors.greenBtnLine};
        border: 1px solid ${A.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${o=>o.$primary&&!o.disabled&&`
        background: ${A.colors.greenBtnLine};
        border: 1px solid ${A.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${A.colors.greenGlow};
        color: #ffffff;
    `}

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${A.colors.greenBtnBg}; border: 1px solid ${A.colors.greenBtnLine}; color: ${A.colors.greenText}; }
    `}

    @media (pointer: coarse) {
        height: 32px;
    }
`,R$=["Either","Front","Aft","Port","Stbd"],D$=[1,2,3,4,0],gd=o=>o?"Starboard":"Port",M$=o=>shipManager.movement.isExtendedTurnStart(o)?"begin":shipManager.movement.isExtendedTurnCompletion(o)?"complete":"normal",$$=(o,r)=>{switch(r.type){case"extendTurnLeft":case"extendTurnRight":return`Begin Extended Turn
`+gd(r.type==="extendTurnRight");case"turnleft":case"turnright":return r.value==="turnIntoPivot"?"Turn into Pivot · "+gd(r.type==="turnright"):r.value==="extendedTurn"?`Complete Extended Turn
`+gd(r.type==="turnright"):"Turn "+gd(r.type==="turnright");case"pivotleft":case"pivotright":return"Pivot "+gd(r.type==="pivotright");case"slipleft":case"slipright":return"Slip "+gd(r.type==="slipright");case"roll":return r.value==="emergencyRoll"?"Emergency Roll":"Roll";case"speedchange":{const l=o.movement.lastIndexOf(r),d=l>0?o.movement[l-1]:null;return d&&(r.speed<d.speed||r.heading!=d.heading)?"Decelerate":"Accelerate"}case"jink":return"Jink";case"halfPhase":return"Half-Phase";case"contract":return"Contraction";default:return"Assign Thrust"}},O$=(o,r)=>{if(!Array.isArray(o)||!Array.isArray(r))return{rows:[],extra:0};const l=[1,2,3,4].every(b=>!(o[b]>0)),d=[];D$.forEach(b=>{const S=o[b];if(!(S>0)||b>0&&r[b]===null)return;const y=Math.max(0,r[b]||0);d.push({slot:b,label:b===0?l?"Any":"Either":R$[b],paid:S-y,required:S,open:y})});const h=r[0]<0?-r[0]:0;return{rows:d,extra:h}},A$=o=>Array.isArray(o)&&!o.some(r=>r>0),j$=(o,r,l)=>{const d={left:r.left-l,top:r.top-l,right:r.left+l,bottom:r.top+l};return Array.from(o.children).forEach(h=>{const b=h.getBoundingClientRect();b.width===0||b.height===0||(d.left=Math.min(d.left,b.left),d.top=Math.min(d.top,b.top),d.right=Math.max(d.right,b.right),d.bottom=Math.max(d.bottom,b.bottom))}),d};class _$ extends Ge.Component{constructor(r){super(r),this.containerRef=Ge.createRef(),this.ringRef=Ge.createRef(),this.panelRef=Ge.createRef(),this.layout=this.layout.bind(this)}componentDidMount(){this.layout(),window.addEventListener("resize",this.layout),this.container=this.containerRef.current,this.container&&this.container.addEventListener(tC,this.layout)}componentDidUpdate(){this.layout()}componentWillUnmount(){window.removeEventListener("resize",this.layout),this.container&&this.container.removeEventListener(tC,this.layout)}layout(){const r=this.containerRef.current,l=this.ringRef.current,d=this.panelRef.current;if(!r||!l||!d)return;const{ship:h,totalRequired:b,movement:S}=this.props,y=f$(h,b,S);l.style.setProperty("--fv-ring-x",y.x+"px"),l.style.setProperty("--fv-ring-y",y.y+"px"),this.placePanel(r,l,d,y.clear)}placePanel(r,l,d,h){const b=r.getBoundingClientRect(),S=j$(l,b,h),y=window.innerWidth,E=window.innerHeight,$=d.offsetWidth,M=d.offsetHeight,N=Ee=>Math.min(Math.max(Ee,vs),y-$-vs),j=Ee=>Math.min(Math.max(Ee,vs),E-M-vs),H={left:N(b.left-$/2),top:S.bottom+Jg},L=[H,{left:N(b.left-$/2),top:S.top-Jg-M},{left:S.right+Jg,top:j(b.top-M/2)},{left:S.left-Jg-$,top:j(b.top-M/2)}],K=Ee=>Ee.left>=vs&&Ee.top>=vs&&Ee.left+$<=y-vs&&Ee.top+M<=E-vs,de=L.find(K)||{left:H.left,top:j(H.top)};d.style.left=Math.round(de.left-b.left)+"px",d.style.top=Math.round(de.top-b.top)+"px"}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}toggleExtendedTurn(r){r.enabled&&window.shipManager.movement.setExtendedTurn(this.props.ship,!r.checked)}render(){const{ship:r,position:l,rotation:d,totalRequired:h,remainginRequired:b,movement:S}=this.props,y=Math.round(Math.abs(d)),{rows:E,extra:$}=O$(h,b),M=M$(S),N=shipManager.movement.getExtendedTurnToggle(r,S);let j,H,L=null;M==="begin"?(L=shipManager.movement.getExtendedTurnStartPayment(r,S),j=L.valid,H=L.paid<L.minimum?`Pay at least ${L.minimum} now (${L.paid} so far)`:"Leave at least 1 thrust owed for next turn"):(j=A$(b),H="Still needed: "+E.filter(de=>de.open>0).map(de=>`${de.label} ${de.open}`).join(" · "));const K=de=>de.open===0?"ok":M==="begin"?"owed":"open";return m.jsxs(v$,{ref:this.containerRef,onMouseOver:de=>de.preventDefault(),onContextMenu:de=>de.preventDefault(),id:"thrustUIContainer",style:{left:`${l.x}px`,top:`${l.y}px`},children:[m.jsxs(m$,{ref:this.ringRef,style:{transform:`rotate(${y}deg)`},$rotation:y,children:[m.jsx(iC,{children:Zg(r,1,h,b,S)}),m.jsx(aC,{children:Zg(r,3,h,b,S)}),m.jsx(g$,{children:Zg(r,4,h,b,S)}),m.jsx(h$,{children:Zg(r,2,h,b,S)})]}),m.jsxs(y$,{ref:this.panelRef,children:[m.jsx(x$,{children:$$(r,S)}),m.jsxs(b$,{children:[E.map(de=>m.jsxs(ju,{children:[m.jsx(_u,{children:de.label}),m.jsxs(Lu,{$state:K(de),children:[de.paid,"/",de.required]})]},`thrust-row-${de.slot}`)),$>0&&m.jsxs(ju,{children:[m.jsx(_u,{children:"Extra thrust"}),m.jsxs(Lu,{$state:"ok",children:["+",$]})]}),m.jsx(T$,{}),L&&z$(L),m.jsxs(ju,{children:[m.jsx(_u,{children:"Engine thrust"}),m.jsx(Lu,{children:shipManager.movement.getRemainingEngineThrust(r)})]}),L$(r,S),M==="begin"&&m.jsx(_x,{children:"Completes on next turn's movement"}),M==="complete"&&m.jsx(_x,{children:"Begun last turn"}),N&&m.jsxs(m.Fragment,{children:[m.jsxs(C$,{$locked:!N.enabled,title:N.enabled?N.checked?"Make a normal turn instead":"Pay part of this turn now and the rest next turn":N.hint,onClick:()=>this.toggleExtendedTurn(N),children:[m.jsx(E$,{$checked:N.checked,$locked:!N.enabled}),"Make Extended Turn"]}),!N.enabled&&N.hint&&m.jsx(_x,{children:N.hint})]})]}),m.jsxs(k$,{children:[m.jsx(sC,{$primary:!0,disabled:!j,title:j?"Confirm this manoeuvre":H,onClick:j?this.ready.bind(this):void 0,children:"Confirm"}),m.jsx(sC,{onClick:this.cancel.bind(this),children:"Cancel"})]})]})]})}}const L$=(o,r)=>{if(!shipManager.movement.isTurn(r))return null;const l=shipManager.movement.calculateTurndelay(o,r,r.speed);return m.jsxs(ju,{children:[m.jsx(_u,{children:"Turn delay"}),m.jsx(Lu,{children:l})]})},z$=o=>{const r=o.paid>=o.minimum,l=o.owed.any+o.owed.rear+o.owed.side,d=h=>(o.cost>0?Math.min(100,Math.max(0,h/o.cost*100)):0)+"%";return m.jsxs(m.Fragment,{children:[m.jsxs(ju,{children:[m.jsx(_u,{children:"Turn cost"}),m.jsx(Lu,{children:o.cost})]}),m.jsxs(ju,{children:[m.jsxs(_u,{children:["Paying now (min ",o.minimum,")"]}),m.jsx(Lu,{$state:r?"ok":"open",children:o.paid})]}),m.jsxs(w$,{title:`At least ${o.minimum} now, at most ${o.cost-1}`,children:[m.jsx(lC,{style:{left:0,width:d(o.paid)}}),m.jsx(lC,{$owed:!0,style:{left:d(o.paid),width:d(o.cost-o.paid)}}),m.jsx(S$,{style:{left:d(o.minimum)}})]}),m.jsxs(ju,{children:[m.jsx(_u,{children:"Thrust owed next turn"}),m.jsx(Lu,{$state:"owed",children:l})]})]})},Zg=(o,r,l,d,h)=>{const b=shipManager.systems.getThrusters(o,r);if(h.type!=="roll"&&l[r]===null)return null;const S=Array.isArray(d)&&(d[r]>0||d[0]>0);return b.map((y,E)=>{const $=shipManager.systems.isDestroyed(o,y),M=$||!shipManager.movement.wouldAcceptThrust(o,y)?void 0:S?"need":"extra",N=()=>{shipManager.movement.assignThrust(o,y),shipManager.movement.updateAssignThrust(o)},j=de=>{de.preventDefault(),shipManager.movement.unAssignThrust(o,y),shipManager.movement.updateAssignThrust(o)};let H=shipManager.criticals.hasCritical(y,"HalfEfficiency")?10:0;shipManager.criticals.hasCritical(y,"FirstThrustIgnored")&&(H+=1);const L=shipManager.movement.getAmountChanneled(o,y),K=shipManager.systems.getOutput(o,y);return m.jsx(p$,{$crits:H,$direction:r,$destroyed:$,$box:M,onClick:$?void 0:N,onContextMenu:$?de=>de.preventDefault():j,children:m.jsxs(nC,{$over:L>K,children:[L,"/",K]})},`thruster-${r}-${E}`)})},N$=()=>m.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[m.jsx("path",{d:"M9 3H3v6"}),m.jsx("path",{d:"M15 3h6v6"}),m.jsx("path",{d:"M9 21H3v-6"}),m.jsx("path",{d:"M15 21h6v-6"})]});class P$ extends Ge.Component{fullScreen(){var r=window.document,l=r.documentElement,d=l.requestFullscreen||l.mozRequestFullScreen||l.webkitRequestFullScreen||l.msRequestFullscreen,h=r.exitFullscreen||r.mozCancelFullScreen||r.webkitExitFullscreen||r.msExitFullscreen;!r.fullscreenElement&&!r.mozFullScreenElement&&!r.webkitFullscreenElement&&!r.msFullscreenElement?d.call(l):h.call(r)}render(){return m.jsx(F$,{onClick:this.fullScreen.bind(this),title:"Full screen",children:m.jsx(N$,{})})}}const F$=D(yp)`
    width: ${A.hud.btn};
    height: ${A.hud.btn};
    position: fixed;
    right: calc((${A.hud.btn} + ${A.hud.gap}) * 2);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${pa}

    svg {
        width: ${A.hud.icon};
        height: ${A.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${A.hud.btnSmall};
        height: ${A.hud.btnSmall};
        right: calc((${A.hud.btnSmall} + ${A.hud.gapSmall}) * 2);

        svg {
            width: ${A.hud.iconSmall};
            height: ${A.hud.iconSmall};
        }
    }
`;class B$ extends Ge.Component{constructor(r){super(r),this.state={available:uC()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{const r=uC();this.state.available!==r&&this.setState({available:r})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?m.jsx(U$,{onClick:this.surrender,title:"Surrender",children:m.jsx("img",{src:I$(),alt:""})}):null}}const uC=()=>{if(typeof window.gamedata>"u")return!1;const o=window.gamedata;if(o.replay||!o.isPlayerInGame()||o.status==="SURRENDERED"||o.status==="FINISHED")return!1;for(const r in o.slots){const l=o.slots[r];if(l.playerid==o.thisplayer&&l.surrendered!==null&&l.surrendered!==void 0)return!1}return!0},I$=()=>{const o="./img/surrender_icon1.png";return window.AssetManager?window.AssetManager.getSmartImagePath(o):o},cC="34px",dC="22px",U$=D(yp)`
    width: ${A.hud.btn};
    height: ${A.hud.btn};
    position: fixed;
    right: calc(${A.hud.btn} + ${A.hud.gap});
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${pa}

    img {
        width: ${cC};
        height: ${cC};
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
        width: ${A.hud.btnSmall};
        height: ${A.hud.btnSmall};
        right: calc(${A.hud.btnSmall} + ${A.hud.gapSmall});

        img {
            width: ${dC};
            height: ${dC};
            transform: translateY(${"-2px"});
        }
    }
`,H$=()=>m.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[m.jsx("path",{d:"M3 10.5L12 3l9 7.5"}),m.jsx("path",{d:"M5 9v12h14V9"}),m.jsx("path",{d:"M10 21v-6h4v6"})]});class V$ extends Ge.Component{render(){return m.jsx(W$,{as:"a",href:"games.php",title:"Back to Lobby",children:m.jsx(H$,{})})}}const W$=D(yp)`
    width: ${A.hud.btn};
    height: ${A.hud.btn};
    position: fixed;
    right: calc((${A.hud.btn} + ${A.hud.gap}) * 3);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${pa}

    svg {
        width: ${A.hud.icon};
        height: ${A.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${A.hud.btnSmall};
        height: ${A.hud.btnSmall};
        right: calc((${A.hud.btnSmall} + ${A.hud.gapSmall}) * 3);

        svg {
            width: ${A.hud.iconSmall};
            height: ${A.hud.iconSmall};
        }
    }
`;class Y$ extends $n.Component{constructor(r){super(r),this.state={losToggled:!1,hexToggled:!1,soundToggled:gamedata.playAudio!==!1,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener("LoSToggled",this.externalToggleLoS),window.addEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.addEventListener("BackgroundToggled",this.externalToggleBackground),window.addEventListener("soundToggled",this.externalToggleSound),this.replayCheck=setInterval(()=>{const r=this.getReplayMode();this.state.replayMode!==r&&this.setState({replayMode:r})},500)}componentWillUnmount(){window.removeEventListener("LoSToggled",this.externalToggleLoS),window.removeEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.removeEventListener("BackgroundToggled",this.externalToggleBackground),window.removeEventListener("soundToggled",this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(r=>({hexToggled:!r.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}showFriendlyEW(r){webglScene.customEvent("ShowFriendlyEW",{up:r})}showEnemyEW(r){webglScene.customEvent("ShowEnemyEW",{up:r})}toggleFriendlyBallisticLines(r){if(r)return;const l=!this.state.fbToggled;this.setState({fbToggled:l}),webglScene.customEvent("ToggleFriendlyBallisticLines",{up:r})}toggleEnemyBallisticLines(r){if(r)return;const l=!this.state.ebToggled;this.setState({ebToggled:l}),webglScene.customEvent("ToggleEnemyBallisticLines",{up:r})}toggleLoS(r){if(r)return;const l=!this.state.losToggled;this.setState({losToggled:l}),webglScene.customEvent("ToggleLoS",{up:r}),window.dispatchEvent(new CustomEvent("LoSToggled"))}toggleHexNumbers(r){if(r)return;const l=!this.state.hexToggled;this.setState({hexToggled:l}),webglScene.customEvent("ToggleHexNumbers",{up:r}),window.dispatchEvent(new CustomEvent("HexNumbersToggled"))}toggleSound(){const r=!this.state.soundToggled;this.setState({soundToggled:r}),webglScene.customEvent("ToggleSound",{enabled:r})}toggleBackground(){const r=document.getElementById("background");if(!r)return;const l=!this.state.bgToggled;let d=this.state.originalBgImage;l?(d||(d=r.style.backgroundImage),r.style.backgroundImage="none",r.style.backgroundColor="black"):(r.style.backgroundImage=d||"",r.style.backgroundColor=""),this.setState({bgToggled:l,originalBgImage:d})}render(){return m.jsxs(G$,{children:[m.jsx(Q$,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),m.jsx(K$,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),m.jsx(X$,{$toggled:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),m.jsx(q$,{$toggled:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),m.jsx(J$,{$toggled:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),m.jsx(Z$,{$toggled:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),m.jsx(eO,{$toggled:this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?"Enable Background":"Disable Background"}),this.state.replayMode&&m.jsx(tO,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?"Sound On":"Sound Off"})]})}}const G$=D.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,ys=D(yp)`
    display: flex;
    width: ${A.hud.btn};
    height: ${A.hud.btn};
    align-items: center;
    justify-content: center;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${pa}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${A.hud.btnSmall};
        height: ${A.hud.btnSmall};
    }
`,K$=D(ys)`
    background-image: url("./img/EEW.png");
`,Q$=D(ys)`
    background-image: url("./img/FEW.png");
`,q$=D(ys)`
    background-image: url("./img/ballisticTarget2.png");
    box-shadow: ${o=>o.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${o=>o.$toggled?"#1b533d":A.colors.windowBg};
    border: 1px solid ${o=>o.$toggled?"limegreen":A.colors.line};
    border-right: none;
`,X$=D(ys)`
    background-image: url("./img/ballisticLaunch2.png");
    box-shadow: ${o=>o.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${o=>o.$toggled?"#1b533d":A.colors.windowBg};
    border: 1px solid ${o=>o.$toggled?"limegreen":A.colors.line};
    border-right: none;
`,J$=D(ys)`
    background-image: url("./img/los1.png");
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":A.colors.line};
    border-right: none;
`,Z$=D(ys)`
    background-image: url("./img/hexNumber.png");
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":A.colors.line};
    border-right: none;
`,eO=D(ys)`
    filter: ${o=>o.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${o=>o.$toggled?"limegreen":A.colors.line};
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
`,tO=D(ys)`
    background-image: ${o=>o.$toggled?'url("./img/soundOn.png")':'url("./img/soundOff.png")'};
    border: 1px solid ${A.colors.line};
    border-right: none;
`,nO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,rO=D.div`
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
`,iO=D.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,aO=D.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${o=>o.img});
    background-size: cover;
    align-items: center;
    opacity: 1 !important;    
    justify-content: center;
    ${pa}
    border: 1px solid ${o=>o.selected?"#ef4444":"transparent"};
    position: relative;
    box-shadow: ${o=>o.selected?"0 0 5px #b43131":"none"};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;class oO extends Ge.Component{selectMode(r,l){r.stopPropagation(),r.preventDefault();const{ship:d,system:h}=this.props;weaponManager.onSetModeClicked(d,h,l)}selectAllMode(r,l){r.stopPropagation(),r.preventDefault();const{ship:d,system:h}=this.props;weaponManager.onSetModeAllClicked(d,h,l)}render(){const{ship:r,system:l}=this.props,d=this.props.showModes!==!1,h=parseInt(l.firingMode);let b="";l.iconPath?b=`./img/systemicons/${l.iconPath}`:b=`./img/systemicons/${l.name}.png`;const S=[];for(const y in l.firingModes)if(l.firingModes.hasOwnProperty(y)){const E=parseInt(y),$=l.firingModes[y],M=E===h;let N=l.modeLetters||1;l.modeLettersArray&&l.modeLettersArray[E]&&(N=l.modeLettersArray[E]),S.push(m.jsx(aO,{img:b,selected:M,onClick:j=>this.selectMode(j,E),onContextMenu:j=>this.selectAllMode(j,E),title:`Set mode: ${$} ${M?"(Current)":""} (Right click to set all)`,children:$.substring(0,N)},E))}return m.jsxs(nO,{children:[d&&m.jsx(rO,{children:"Select Firing Mode"}),m.jsxs(iO,{children:[d&&S,this.props.children]})]})}}const lO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,sO=D.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,uO=D.div`
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

`,fC=D.div`
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
`,cO=D.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,Lx=D.span`
    font-weight: bold;
`,pC=D.span`
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

`,dO=D(Lx)`
    color: #ffb833;
    font-weight: normal;    
`,fO=D(Lx)`
    color: #ff3333;
    font-weight: normal;    
`,pO=D.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,em=D.div`
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
    
     ${pa}
`,hO=D.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,gO=D.div`
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
`,hC=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,mO=D(fC)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,zx=D.span`
    color: #00b8e6;
    font-weight: normal;
    margin-right: 4px;
`,vO=D.input`
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
`,yO=D.span`
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
`;class xO extends Ge.Component{constructor(r){super(r),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getEffectiveCriticalRepairCost(r,l){return l.name==="cnC"?4:r.repairCost}getDockedUnits(){const{ship:r,system:l}=this.props;if(!l.servicesDockedUnits)return[];const d=[],h=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const b of h)if(!(!b.isDockingBay||!Array.isArray(b.shipsDocked)))for(const S of b.shipsDocked){const y=gamedata.getShip(S.shipId);y&&d.indexOf(y)===-1&&d.push(y)}return d}getDockedRepairables(){const{system:r}=this.props,l=[];for(const d of this.getDockedUnits()){const h=Array.isArray(d.systems)?d.systems:Object.values(d.systems),b="d"+d.id+":";for(const S of h){const y=S.name==="structure"||S.name==="cnC"||S.name==="SelfRepair";if(!shipManager.systems.isDestroyed(d,S)&&S.repairPriority>=1&&S.criticals){const j=Array.isArray(S.criticals)?S.criticals:Object.values(S.criticals);for(const H of j){if(H.repairPriority===0||H.turn>=gamedata.turn||H.oneturn||H.turnend>0)continue;const L=b+S.id+"-"+H.id;let K=H.repairPriority||0,de=!1;r.priorityChanges&&L in r.priorityChanges&&r.priorityChanges[L]>=0?(K=r.priorityChanges[L],de=!0):K<10&&(K+=S.repairPriority),!(K<1)&&l.push({type:"critical",sys:S,crit:H,ownerShip:d,shipId:d.id,docked:!0,priority:K,overridden:de,cost:this.getEffectiveCriticalRepairCost(H,S),id:S.id,subId:H.id,keyId:L})}}if(!y||S.repairPriority===0)continue;const E=shipManager.systems.getTotalDamage(S);if(E<=0)continue;if(S.name==="structure"){if(shipManager.systems.isDestroyed(d,S))continue}else{const j=S.structureHomeLocation!==void 0&&S.structureHomeLocation!==null?S.structureHomeLocation:S.location;if(j!=0){const H=shipManager.systems.getStructureSystem(d,j);if(H&&shipManager.systems.isDestroyed(d,H))continue}}const $=b+S.id;let M=S.repairPriority,N=!1;r.priorityChanges&&$ in r.priorityChanges&&r.priorityChanges[$]>=0&&(M=r.priorityChanges[$],N=!0),!(M<1)&&(!N&&shipManager.systems.isDestroyed(d,S)&&M<=10&&(M+=10),l.push({type:"system",sys:S,ownerShip:d,shipId:d.id,docked:!0,priority:M,overridden:N,damage:E,maxHealth:S.maxhealth,id:S.id,subId:0,keyId:$}))}}return l}getRepairableSystems(){const{ship:r,system:l}=this.props,d=[],h=[],b=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const E of b){if(E.name==="SelfRepair"||E.repairPriority===0||E.privateRepairOnly&&!l.repairRestrictedTo||l.repairRestrictedTo&&!l.repairRestrictedTo.includes(E.id)||E.name=="structure"&&shipManager.systems.isDestroyed(E.ship,E))continue;const $=E.structureHomeLocation!==void 0&&E.structureHomeLocation!==null?E.structureHomeLocation:E.location;if(E.name!="structure"&&$!=0){var S=shipManager.systems.getStructureSystem(E.ship,$);if(S&&shipManager.systems.isDestroyed(E.ship,S))continue}let M=E.repairPriority,N=!1;if(l.priorityChanges&&E.id in l.priorityChanges&&l.priorityChanges[E.id]>=0&&(M=l.priorityChanges[E.id],N=!0),!N&&shipManager.systems.isDestroyed(r,E)&&M<=10&&(M+=10),!shipManager.systems.isDestroyed(r,E)&&E.criticals){const H=Array.isArray(E.criticals)?E.criticals:Object.values(E.criticals);for(const L of H){if(L.repairPriority===0||L.turn>=gamedata.turn||L.oneturn||L.turnend>0)continue;let K=L.repairPriority||0;const de=E.id+"-"+L.id;let Ee=!1;l.priorityChanges&&de in l.priorityChanges&&l.priorityChanges[de]>=0?(K=l.priorityChanges[de],Ee=!0):K<10&&(K+=E.repairPriority),h.push({type:"critical",sys:E,crit:L,ownerShip:r,shipId:0,docked:!1,priority:K,overridden:Ee,cost:this.getEffectiveCriticalRepairCost(L,E),id:E.id,subId:L.id,keyId:de})}}const j=shipManager.systems.getTotalDamage(E);j>0&&d.push({type:"system",sys:E,ownerShip:r,shipId:0,docked:!1,priority:M,overridden:N,damage:j,maxHealth:E.maxhealth,id:E.id,subId:0,keyId:E.id})}const y=[...h,...d,...this.getDockedRepairables()];return y.sort((E,$)=>{if(E.priority!==$.priority)return $.priority-E.priority;const M=!!E.overridden,N=!!$.overridden;if(M!==N)return M?-1:1;if(this.lastOrder&&this.lastOrder.length>0){const L=this.lastOrder.indexOf(E.keyId),K=this.lastOrder.indexOf($.keyId);if(L!==-1&&K!==-1)return L-K}const j=E.shipId||0,H=$.shipId||0;return j!==H?j-H:E.id!==$.id?E.id-$.id:E.subId-$.subId}),this.lastOrder=y.map(E=>E.keyId),y}handleInputChange(r,l,d){const h=r.target.value;if(h===""){this.setState(S=>({priorityInputs:{...S.priorityInputs,[l]:""}}));return}const b=parseInt(h,10);isNaN(b)||(this.setState(S=>({priorityInputs:{...S.priorityInputs,[l]:b}})),this.setPriority(l,b))}handleWheel(r,l,d){r.preventDefault();const h=r.deltaY<0?1:-1,b=d+h;b<1||this.setPriority(l,b)}componentDidUpdate(r){if(this.dragRef&&this.dragRef.started&&this.listRef){const S=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');S&&this.positionDraggedEl(S,this.dragRef,this.dragRef.lastClientY)}const l=this.getRepairableSystems(),d=this.state.priorityInputs,h={};let b=!1;l.forEach(S=>{const y=S.keyId,E=S.priority;d[y]!==void 0&&d[y]!==E&&document.activeElement!==document.getElementById(`prio-input-${y}`)&&(h[y]=E,b=!0)}),b&&this.setState(S=>({priorityInputs:{...S.priorityInputs,...h}}))}handleTop(r,l){r.stopPropagation();const d=this.getRepairableSystems();if(d.length===0)return;const h=d[0].priority,b=d.find(y=>y.keyId===l);if(!b||b.priority===h)return;let S=h+1;this.setPriority(l,S)}handleUp(r,l,d){r.stopPropagation();let h=d+1;h!==d&&this.setPriority(l,h)}handleDown(r,l,d){r.stopPropagation(),!(d<=1)&&this.setPriority(l,d-1)}handleReset(r,l){r.stopPropagation(),this.setPriority(l,-1)}setPriority(r,l){const{ship:d,system:h}=this.props;h.setOverride(r,l),webglScene.customEvent("SystemDataChanged",{ship:d,system:h})}onRowPointerDown(r,l,d,h){if(this.props.readOnly||r.button!=null&&r.button!==0||r.target&&r.target.closest&&r.target.closest("input, .sr-action-button"))return;const b=r.currentTarget,S=b?b.offsetHeight:24,y=this.listRef?this.listRef.offsetHeight:0,E=b?b.offsetWidth:0,$=b?r.clientY-b.getBoundingClientRect().top:0;this.dragRef={keyId:l,pointerId:r.pointerId,startY:r.clientY,startIdx:d,order:h,gapSize:S,lockHeight:y,anchorWidth:E,grabOffsetInRow:$,lastClientY:r.clientY,started:!1},r.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(r){const l=this.dragRef;if(!(!l||r.pointerId!==l.pointerId)){if(!l.started){if(Math.abs(r.clientY-l.startY)<4)return;l.started=!0}r.preventDefault(),l.lastClientY=r.clientY,this.updateDragForPointer(r.clientY),this.updateAutoScroll(r.clientY)}}updateDragForPointer(r){const l=this.dragRef;if(!l||!this.listRef)return;const d=this.listRef.querySelectorAll("[data-keyid]"),h=this.listRef.getBoundingClientRect(),b=r-h.top+this.listRef.scrollTop,y=this.state.drag&&this.state.drag.dropIdx===0?l.gapSize:0;let E=0,$=null;for(let N=0;N<d.length;N++){if(d[N].getAttribute("data-keyid")===String(l.keyId)){$=d[N];continue}const j=d[N].offsetTop-y+d[N].offsetHeight/2;if(b<j)break;E++}$&&this.positionDraggedEl($,l,r);const M=this.state.drag;(!M||M.keyId!==l.keyId||M.dropIdx!==E)&&this.setState({drag:{keyId:l.keyId,startIdx:l.startIdx,dropIdx:E,gapSize:l.gapSize,lockHeight:l.lockHeight}})}positionDraggedEl(r,l,d){const h=this.listRef.getBoundingClientRect();let b=d-h.top+this.listRef.scrollTop-l.grabOffsetInRow;const S=Math.max(0,this.listRef.scrollHeight-l.gapSize);b<0?b=0:b>S&&(b=S),r.style.top=b+"px",r.style.left="0px",r.style.width=l.anchorWidth+"px",r.style.transform="none"}updateAutoScroll(r){const l=this.listRef;if(!l){this.stopAutoScroll();return}const d=30,h=l.getBoundingClientRect(),b=l.scrollTop>0,S=l.scrollTop<l.scrollHeight-l.clientHeight-1,y=r-h.top,E=h.bottom-r;b&&y<d?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,y)/d),this.ensureAutoScrollRunning()):S&&E<d?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,E)/d),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const r=this.listRef,l=this.dragRef;if(!r||!l||this.autoScrollDir===0)return;const d=r.scrollTop;if(r.scrollTop=d+this.autoScrollDir*(this.autoScrollSpeed||6),r.scrollTop===d){this.stopAutoScroll();return}this.updateDragForPointer(l.lastClientY),this.updateAutoScroll(l.lastClientY)}onDragEnd(r){const l=this.dragRef;if(!l||r&&r.pointerId!=null&&r.pointerId!==l.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const h=this.listRef.querySelector('[data-keyid="'+l.keyId+'"]');h&&(h.style.transform="",h.style.top="",h.style.left="",h.style.width="")}this.dragRef=null;const d=this.state.drag;this.setState({drag:null}),!(!l.started||!d)&&d.dropIdx!==l.startIdx&&this.applyDropReorder(l.order,l.keyId,d.dropIdx)}applyDropReorder(r,l,d){const{ship:h,system:b}=this.props,S=r.findIndex(j=>j.keyId===l);if(S===-1)return;const y=r.slice(),[E]=y.splice(S,1);y.splice(d,0,E);const $=y[d+1],M=y[d-1];let N;$?N=$.priority+1:M?N=Math.max(1,M.priority-1):N=1,b.setOverride(l,N);for(let j=d-1;j>=0&&!(y[j].priority>N);j--)N+=1,b.setOverride(y[j].keyId,N);webglScene.customEvent("SystemDataChanged",{ship:h,system:b})}handlePropagate(r){r.stopPropagation();const{ship:l,system:d}=this.props;for(const h of l.systems)if(h.name==="SelfRepair"&&h.id!==d.id){if(h.priorityChanges)for(const b in h.priorityChanges)(!d.priorityChanges||!(b in d.priorityChanges))&&h.setOverride(b,-1);if(d.priorityChanges)for(const b in d.priorityChanges){const S=d.priorityChanges[b];S>=0&&h.setOverride(b,S)}}webglScene.customEvent("SystemDataChanged",{ship:l,system:d})}render(){const{ship:r,readOnly:l}=this.props,d=this.getRepairableSystems();let h=0;const b=Array.isArray(r.systems)?r.systems:Object.values(r.systems);for(const S of b)S.name==="SelfRepair"&&h++;return m.jsxs(lO,{children:[m.jsx(sO,{children:l?"Repair Queue (view only)":"Manage Repair Queue"}),m.jsxs(uO,{ref:S=>{this.listRef=S},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[d.length===0&&m.jsx(mO,{children:"No damaged systems"}),d.map((S,y)=>{const E=this.state.drag,$=E&&E.keyId===S.keyId;let M=!1,N=!1,j=!1;if(E&&!$){const L=d.length-1,K=y>E.startIdx?y-1:y;E.dropIdx===0&&K===0?M=!0:E.dropIdx===L&&K===L-1?N=!0:E.dropIdx===K&&(j=!0)}const H=S.ownerShip||r;return m.jsxs(fC,{"data-keyid":S.keyId,$dragging:$,$gapBefore:M,$lineAtEnd:N,$lineBefore:j,$gapSize:E?E.gapSize:0,$readOnly:l,onPointerDown:L=>this.onRowPointerDown(L,S.keyId,y,d),children:[m.jsx(cO,{children:S.type==="critical"?m.jsxs(m.Fragment,{children:[m.jsxs(dO,{children:[S.docked&&m.jsxs(zx,{children:[H.name,":"]}),S.sys.displayName," (",S.crit.description||S.crit.phpclass,")"]}),m.jsxs(pC,{children:["Cost: ",S.cost," ",m.jsx(hC,{})," Id: ",S.sys.id]})]}):m.jsxs(m.Fragment,{children:[shipManager.systems.isDestroyed(H,S.sys)?m.jsxs(fO,{children:[S.docked&&m.jsxs(zx,{children:[H.name,":"]}),S.sys.displayName]}):m.jsxs(Lx,{children:[S.docked&&m.jsxs(zx,{children:[H.name,":"]}),S.sys.displayName]}),m.jsxs(pC,{children:["HP: ",shipManager.systems.getRemainingHealth(S.sys)," / ",S.sys.maxhealth," ",m.jsx(hC,{})," Id: ",S.sys.id]})]})}),m.jsx(pO,{children:l?m.jsx(yO,{title:"Priority (view only)",children:S.priority}):m.jsxs(m.Fragment,{children:[m.jsx(em,{className:"sr-action-button",title:"Reset Default",onClick:L=>this.handleReset(L,S.keyId),img:"./img/iconSRCancel.png"}),m.jsx(em,{className:"sr-action-button",title:"Decrease Priority",onClick:L=>this.handleDown(L,S.keyId,S.priority),img:"./img/systemicons/AAclasses/iconMinus.png"}),m.jsx(vO,{id:`prio-input-${S.keyId}`,type:"number",value:this.state.priorityInputs[S.keyId]!==void 0?this.state.priorityInputs[S.keyId]:S.priority,onChange:L=>this.handleInputChange(L,S.keyId,S.priority),onClick:L=>L.stopPropagation(),onWheel:L=>this.handleWheel(L,S.keyId,S.priority)}),m.jsx(em,{className:"sr-action-button",title:"Increase Priority",onClick:L=>this.handleUp(L,S.keyId,S.priority),img:"./img/systemicons/AAclasses/iconPlus.png"}),m.jsx(em,{className:"sr-action-button",title:"Move to Top",onClick:L=>this.handleTop(L,S.keyId),img:"./img/iconSRHigh.png"})]})})]},S.keyId)})]}),!l&&h>1&&m.jsx(hO,{children:m.jsx(gO,{onClick:S=>this.handlePropagate(S),children:"Set all Self Repair systems"})})]})}}const bO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
`,wO=D.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,SO=D.div`
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
`,gC=D.div`
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
`,CO=D(gC)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,EO=D.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,mC=D.span`
    font-weight: bold;
`,TO=D(mC)`
    color: #ff3333;
    font-weight: normal;
`,kO=D.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,RO=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,DO=D.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,MO=D.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,$O=D.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;class OO extends Ge.Component{constructor(r){super(r),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getOrderedBlocks(){const{ship:r,system:l}=this.props,d=(l.structureBlocks||[]).map(y=>{const E=(Array.isArray(r.systems)?r.systems:Object.values(r.systems)).find(j=>j.id===y.id),$=E?shipManager.systems.getRemainingHealth(E):y.maxhealth,M=E?E.maxhealth:y.maxhealth||0,N=E?shipManager.systems.isDestroyed(r,E):!1;return{id:y.id,displayName:y.displayName,hp:$,maxhealth:M,destroyed:N}}),h=l.repairOrder||[];if(h.length===0)return d.slice().sort((y,E)=>{const $=E.destroyed?1:0,M=y.destroyed?1:0;return $!==M?$-M:E.maxhealth-E.hp-(y.maxhealth-y.hp)});const b=[],S=new Set;for(const y of h){const E=d.find($=>$.id===y);E&&(b.push(E),S.add(y))}for(const y of d)S.has(y.id)||b.push(y);return b}onRowPointerDown(r,l,d,h){if(this.props.readOnly||r.button!=null&&r.button!==0||r.target&&r.target.closest&&r.target.closest(".ssr-action-button"))return;const b=r.currentTarget,S=b?b.offsetHeight:24,y=this.listRef?this.listRef.offsetHeight:0,E=b?b.offsetWidth:0,$=b?r.clientY-b.getBoundingClientRect().top:0;this.dragRef={keyId:l,pointerId:r.pointerId,startY:r.clientY,startIdx:d,order:h,gapSize:S,lockHeight:y,anchorWidth:E,grabOffsetInRow:$,lastClientY:r.clientY,started:!1},r.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(r){const l=this.dragRef;if(!(!l||r.pointerId!==l.pointerId)){if(!l.started){if(Math.abs(r.clientY-l.startY)<4)return;l.started=!0}r.preventDefault(),l.lastClientY=r.clientY,this.updateDragForPointer(r.clientY),this.updateAutoScroll(r.clientY)}}updateDragForPointer(r){const l=this.dragRef;if(!l||!this.listRef)return;const d=this.listRef.querySelectorAll("[data-keyid]"),h=this.listRef.getBoundingClientRect(),b=r-h.top+this.listRef.scrollTop,y=this.state.drag&&this.state.drag.dropIdx===0?l.gapSize:0;let E=0,$=null;for(let N=0;N<d.length;N++){if(d[N].getAttribute("data-keyid")===String(l.keyId)){$=d[N];continue}const j=d[N].offsetTop-y+d[N].offsetHeight/2;if(b<j)break;E++}$&&this.positionDraggedEl($,l,r);const M=this.state.drag;(!M||M.keyId!==l.keyId||M.dropIdx!==E)&&this.setState({drag:{keyId:l.keyId,startIdx:l.startIdx,dropIdx:E,gapSize:l.gapSize,lockHeight:l.lockHeight}})}positionDraggedEl(r,l,d){const h=this.listRef.getBoundingClientRect();let b=d-h.top+this.listRef.scrollTop-l.grabOffsetInRow;const S=Math.max(0,this.listRef.scrollHeight-l.gapSize);b<0?b=0:b>S&&(b=S),r.style.top=b+"px",r.style.left="0px",r.style.width=l.anchorWidth+"px",r.style.transform="none"}updateAutoScroll(r){const l=this.listRef;if(!l){this.stopAutoScroll();return}const d=30,h=l.getBoundingClientRect(),b=l.scrollTop>0,S=l.scrollTop<l.scrollHeight-l.clientHeight-1,y=r-h.top,E=h.bottom-r;b&&y<d?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,y)/d),this.ensureAutoScrollRunning()):S&&E<d?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,E)/d),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const r=this.listRef,l=this.dragRef;if(!r||!l||this.autoScrollDir===0)return;const d=r.scrollTop;if(r.scrollTop=d+this.autoScrollDir*(this.autoScrollSpeed||6),r.scrollTop===d){this.stopAutoScroll();return}this.updateDragForPointer(l.lastClientY),this.updateAutoScroll(l.lastClientY)}onDragEnd(r){const l=this.dragRef;if(!l||r&&r.pointerId!=null&&r.pointerId!==l.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const h=this.listRef.querySelector('[data-keyid="'+l.keyId+'"]');h&&(h.style.transform="",h.style.top="",h.style.left="",h.style.width="")}this.dragRef=null;const d=this.state.drag;this.setState({drag:null}),!(!l.started||!d)&&d.dropIdx!==l.startIdx&&this.applyDropReorder(l.order,l.keyId,d.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){const r=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');r&&this.positionDraggedEl(r,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(r,l,d){const{ship:h,system:b}=this.props,S=r.slice(),y=S.findIndex(M=>M.id===l);if(y===-1)return;const[E]=S.splice(y,1);S.splice(d,0,E);const $=S.map(M=>M.id);b.setRepairOrder($),webglScene.customEvent("SystemDataChanged",{ship:h,system:b})}handleReset(r){r.stopPropagation();const{ship:l,system:d}=this.props;d.setRepairOrder([]),webglScene.customEvent("SystemDataChanged",{ship:l,system:d})}render(){const{ship:r,readOnly:l}=this.props,d=this.getOrderedBlocks(),h=(this.props.system.repairOrder||[]).length>0;return m.jsxs(bO,{children:[m.jsx(wO,{children:l?"Structure Repair Order (view only)":"Manage Structure Repair"}),!l&&m.jsx($O,{children:"Drag rows to set repair priority — top = first repaired"}),m.jsxs(SO,{ref:b=>{this.listRef=b},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[d.length===0&&m.jsx(CO,{children:"No structure blocks found"}),d.map((b,S)=>{const y=this.state.drag,E=y&&y.keyId===b.id;let $=!1,M=!1,N=!1;if(y&&!E){const H=d.length-1,L=S>y.startIdx?S-1:S;y.dropIdx===0&&L===0?$=!0:y.dropIdx===H&&L===H-1?M=!0:y.dropIdx===L&&(N=!0)}const j=b.maxhealth-b.hp;return m.jsx(gC,{"data-keyid":b.id,$dragging:E,$gapBefore:$,$lineAtEnd:M,$lineBefore:N,$gapSize:y?y.gapSize:0,$readOnly:l,onPointerDown:H=>this.onRowPointerDown(H,b.id,S,d),children:m.jsxs(EO,{children:[b.destroyed?m.jsx(TO,{children:b.displayName}):m.jsx(mC,{children:b.displayName}),m.jsxs(kO,{children:["HP: ",b.hp," / ",b.maxhealth,j>0&&m.jsxs(m.Fragment,{children:[m.jsx(RO,{}),"Dmg: ",j,b.destroyed?" — DESTROYED":""]})]})]})},b.id)})]}),!l&&m.jsx(DO,{children:m.jsx(MO,{className:"ssr-action-button",onClick:b=>this.handleReset(b),title:"Clear custom order and return to default (destroyed first, then most damaged)",children:h?"Reset to Default Order":"Using Default Order"})})]})}}const AO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,jO=D.div`
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
`,_O=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,vC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,LO=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,zO=D.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,NO=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,PO=D.div`
    width: 20px;
    text-align: center;
`,Nx=D.div`
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
`,FO=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class BO extends Ge.Component{constructor(r){super(r),this.listRef=$n.createRef()}handleIncrease(r){const{system:l}=this.props;l.setCurrDmgType(r),l.canIncrease()&&(l.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:l}=this.props;l.setCurrDmgType(r),l.canDecrease()&&(l.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:l,system:d}=this.props,h=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating AA setting for:",r),d.setCurrDmgType(r);const y=d.allocatedAA[r];var E=[];for(var $ in h.ships){var M=h.ships[$];if(M.userid==l.userid&&!b.isDestroyed(M))if(M.flight)for(var N=0;N<M.systems.length;N++){var j=M.systems[N];if(j)for(var H=0;H<j.systems.length;H++){var L=j.systems[H];if(L&&L.displayName=="Adaptive Armor Controller"){E.push(L);break}}}else for(var H=0;H<M.systems.length;H++){var L=M.systems[H];if(L.displayName=="Adaptive Armor Controller"){E.push(L);break}}}console.log("Found AA controllers:",E.length);for(var K=0;K<E.length;K++){var L=E[K];L.setCurrDmgType(r);let Ee=0;for(;L.getCurrAllocated()<y&&L.canIncrease()&&Ee<100;)L.doIncrease(),Ee++;Ee>=100&&console.warn("AA Propagation safety break for",L)}S.customEvent("SystemDataChanged",{ship:l,system:d})}getRelevantArmorTypes(){const{ship:r,system:l}=this.props;return l.getRelevantArmorTypes(r)}render(){const{ship:r,system:l}=this.props;if(!l||!l.availableAA)return null;const d=this.getRelevantArmorTypes(),h=l.allocatedAA,b=E=>{const $=l.AAtotal_used,M=l.AAtotal,N=h[E]||0,j=l.AApertype,H=l.availableAA[E],L=l.AApreallocated,K=l.AApreallocated_used;return!($>=M||N>=j||L<=K&&H<=N)},S=E=>l.currchangedAA[E]>0,y=E=>h[E]>0;return m.jsxs(AO,{children:[m.jsx(jO,{children:"Manage Adaptive Armor"}),m.jsxs(_O,{ref:this.listRef,children:[d.map(E=>m.jsxs(vC,{children:[m.jsx(LO,{src:`./img/systemicons/AAclasses/${E}.png`,alt:E}),m.jsx(zO,{children:E}),m.jsxs(NO,{children:[m.jsx(Nx,{onClick:()=>this.handleDecrease(E),disabled:!S(E),children:"-"}),m.jsx(PO,{children:h[E]}),m.jsx(Nx,{onClick:()=>this.handleIncrease(E),disabled:!b(E),children:"+"}),m.jsx(Nx,{title:"Propagate to all units",onClick:()=>this.handlePropagate(E),disabled:!y(E),style:{marginLeft:"5px"},children:m.jsx("img",{src:"./img/systemicons/AAclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},E)),d.length===0&&m.jsx(vC,{children:"No armor types available"})]}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",l.AAtotal_used," / ",l.AAtotal," ",m.jsx(FO,{})," Max Per Type: ",l.AApertype]})]})}}const IO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,UO=D.div`
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
`,HO=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,yC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,VO=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,WO=D.div`
    flex: 1;
    font-weight: normal; 
`,YO=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,GO=D.div`
    width: 20px;
    text-align: center;
`,Px=D.div`
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
`,KO=D.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class QO extends Ge.Component{constructor(r){super(r),this.listRef=$n.createRef()}handleIncrease(r){const{system:l}=this.props;l.setCurrFCType(r),l.canIncrease()&&(l.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:l}=this.props;l.setCurrFCType(r),l.canDecrease()&&(l.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:l,system:d}=this.props,h=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating BFCP setting for:",r),d.setCurrFCType(r);const y=d.allocatedBFCP[r];var E=[];for(var $ in h.ships){var M=h.ships[$];if(M.userid==l.userid&&!b.isDestroyed(M))if(M.flight)for(var N=0;N<M.systems.length;N++){var j=M.systems[N];if(j)for(var H=0;H<j.systems.length;H++){var L=j.systems[H];if(L&&L.displayName=="Computer"){E.push(L);break}}}else for(var H=0;H<M.systems.length;H++){var L=M.systems[H];if(L.displayName=="Computer"){E.push(L);break}}}console.log("Found BFCP controllers:",E.length);for(var K=0;K<E.length;K++){var L=E[K];L.setCurrFCType(r);let Ee=0;for(;L.getCurrAllocated()<y&&L.canIncrease()&&Ee<100;)L.doIncrease(),Ee++;for(;L.getCurrAllocated()>y&&L.canDecrease()&&Ee<100;)L.doDecrease(),Ee++;Ee>=100&&console.warn("BFCP Propagation safety break for",L)}S.customEvent("SystemDataChanged",{ship:l,system:d})}render(){const{system:r}=this.props;if(!r||!r.allocatedBFCP)return null;const l=Object.keys(r.allocatedBFCP),d=r.allocatedBFCP,h=y=>{const E=r.BFCPtotal_used,$=r.output,M=d[y]||0,N=r.BFCPpertype;return!(E>=$||M>=N)},b=y=>d[y]>0,S=y=>d[y]>=0;return m.jsxs(IO,{children:[m.jsx(UO,{children:"Hyach Computer"}),m.jsxs(HO,{ref:this.listRef,children:[l.map(y=>m.jsxs(yC,{children:[m.jsx(VO,{src:`./img/systemicons/BFCPclasses/${y}.png`,alt:y}),m.jsx(WO,{children:y}),m.jsxs(YO,{children:[m.jsx(Px,{onClick:()=>this.handleDecrease(y),disabled:!b(y),children:"-"}),m.jsx(GO,{children:d[y]}),m.jsx(Px,{onClick:()=>this.handleIncrease(y),disabled:!h(y),children:"+"}),m.jsx(Px,{title:"Propagate to all units",onClick:()=>this.handlePropagate(y),disabled:!S(y),style:{marginLeft:"5px"},children:m.jsx("img",{src:"./img/systemicons/BFCPclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},y)),l.length===0&&m.jsx(yC,{children:"No FC types available"})]}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",r.BFCPtotal_used," / ",r.output," ",m.jsx(KO,{})," Max Per Type: ",r.BFCPpertype]})]})}}const qO=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,XO=D.div`
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
`,JO=D.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,xC=D.div`
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
`,ZO=D.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,eA=D.span`
    flex-grow: 1;
    font-weight: bold;
`,tA=D.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,tm=D.div`
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
    ${o=>!o.disabled&&pa}
`,nA=o=>window.gamedata.turn===window.shipManager.getTurnPlaced(o)&&window.gamedata.gamephase===-1;class rA extends Ge.Component{constructor(r){super(r)}handleSelect(r){const{system:l}=this.props;l.specCurrClass=r,l.canSelect()&&(l.doSelect(),this.forceUpdate())}handleUnselect(r){const{system:l}=this.props;l.specCurrClass=r,l.canUnselect()&&(l.doUnselect(),this.forceUpdate())}handleUse(r){const{system:l}=this.props;l.specCurrClass=r,l.canUse()&&(l.doUse(),this.forceUpdate())}handleCancel(r){const{system:l}=this.props;l.specCurrClass=r,l.canDecrease()&&(l.doDecrease(),this.forceUpdate())}render(){const{ship:r,system:l}=this.props;if(!l)return null;const d=nA(r);let h=[];d?l.allSpec&&(h=Object.keys(l.allSpec)):l.availableSpec&&(h=Object.keys(l.availableSpec).filter(S=>l.availableSpec[S]>0)),h.sort();let b="";return d?b=`Specialists Selected: ${Object.values(l.availableSpec||{}).reduce((y,E)=>y+E,0)} / ${l.specTotal}`:b=`Specialists Used: ${l.specTotal_used||0} / ${l.specTotal}`,m.jsxs(qO,{children:[m.jsx(XO,{children:"Hyach Specialists"}),m.jsxs(JO,{children:[h.map(S=>{const y=d&&(l.specCurrClass=S,l.canSelect()),E=d&&(l.specCurrClass=S,l.canUnselect()),$=!d&&(l.specCurrClass=S,l.canUse()),M=!d&&(l.specCurrClass=S,l.canDecrease());l.availableSpec&&l.availableSpec[S]>0,l.currAllocatedSpec&&l.currAllocatedSpec[S];const N=`./img/systemicons/Specialistclasses/${S}.png?v=2`;return m.jsxs(xC,{children:[m.jsx(ZO,{src:N,alt:S}),m.jsx(eA,{children:S}),m.jsx(tA,{children:d?m.jsxs(m.Fragment,{children:[m.jsx(tm,{onClick:()=>this.handleUnselect(S),disabled:!E,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})}),m.jsx(tm,{onClick:()=>this.handleSelect(S),disabled:!y,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})})]}):m.jsxs(m.Fragment,{children:[m.jsx(tm,{onClick:()=>this.handleUse(S),disabled:!$,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})}),m.jsx(tm,{onClick:()=>this.handleCancel(S),disabled:!M,children:m.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})})]})})]},S)}),h.length===0&&m.jsx(xC,{style:{justifyContent:"center",fontStyle:"italic"},children:"No Specialists Available"})]}),m.jsx("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:b})]})}}const iA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${A.colors.line};
`,aA=D.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${A.colors.line};
    border-bottom: 1px solid ${A.colors.line};
    color: ${A.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,oA=D.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,bC=D.div`
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
`,lA=D.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,sA=D.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,uA=D.input`
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
`,Oa=D.div`
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
`;const cA=D.div`
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
`;class wC extends Ge.Component{constructor(r){super(r),this.listRef=$n.createRef(),this.state={shieldInputs:{}}}getShieldLabel(r){let l=r.startArc,d=r.endArc;if(l===void 0||d===void 0)return r.displayName;let h=(l+d)/2;l>d&&(h=(l+d+360)/2),h=h%360;let b="";return h>=337.5||h<22.5?b="Front":h>=22.5&&h<67.5?b="Front Starboard":h>=67.5&&h<112.5?b="Starboard":h>=112.5&&h<157.5?b="Aft Starboard":h>=157.5&&h<202.5?b="Aft":h>=202.5&&h<247.5?b="Aft Port":h>=247.5&&h<292.5?b="Port":h>=292.5&&h<337.5&&(b="Front Port"),b?`${b} - ${r.displayName}`:r.displayName}getShieldSortPriority(r){const l=this.getShieldLabel(r).split(" - ")[0];return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[l]||99}getGeneratorAndShields(){const{ship:r,system:l}=this.props;let d=null,h=[],b="",S="";return l.name==="ThirdspaceShield"||l.name==="ThirdspaceShieldGenerator"?(b="ThirdspaceShield",S="ThirdspaceShieldGenerator"):(l.name==="ThoughtShield"||l.name==="ThoughtShieldGenerator")&&(b="ThoughtShield",S="ThoughtShieldGenerator"),b?(r.systems&&(Array.isArray(r.systems)?r.systems:Object.values(r.systems)).forEach(E=>{E.name===S&&(d=E),E.name===b&&h.push(E)}),h.sort((y,E)=>{const $=this.getShieldSortPriority(y),M=this.getShieldSortPriority(E);return $!==M?$-M:y.id-E.id}),{generator:d,shields:h,systemName:b}):{generator:null,shields:[]}}handleIncrease(r,l){r.canIncrease()&&(r.doIncrease(l),this.afterShieldChange(r))}handleDecrease(r,l){r.canDecrease()&&(r.doDecrease(l),this.afterShieldChange(r))}handleMin(r){r.canDecrease()&&(r.doMin(),this.afterShieldChange(r))}handleMax(r){r.canIncrease()&&(r.doMax(),this.afterShieldChange(r))}afterShieldChange(r){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}),this.updateInputState(r)}handleInputChange(r,l){if(l===""){this.setState(S=>({shieldInputs:{...S.shieldInputs,[r.id]:""}}));return}const d=parseInt(l,10);if(isNaN(d))return;const h=r.currentHealth,b=d-h;b>0?this.handleIncrease(r,b):b<0&&this.handleDecrease(r,Math.abs(b))}updateInputState(r){this.setState(l=>({shieldInputs:{...l.shieldInputs,[r.id]:r.currentHealth}}))}handleWheel(r,l){r.preventDefault(),(r.deltaY<0?1:-1)>0?this.handleIncrease(l,1):this.handleDecrease(l,1)}handleMouseEnter(r){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const l=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);l&&(l.showWeaponArc(this.props.ship,r),window.webglScene.requestRender())}}handleMouseLeave(r){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const l=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);l&&(l.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(r){r&&(shipManager.power.clickPlus(this.props.ship,r),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}handleDeBoost(r){r&&(shipManager.power.clickMinus(this.props.ship,r),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}handleEqualise(r){r&&(r.doEqualise(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:r}))}componentDidMount(){const{generator:r,shields:l}=this.getGeneratorAndShields(),d={};l.forEach(h=>d[h.id]=h.currentHealth),this.setState({shieldInputs:d})}componentDidUpdate(r){const{generator:l,shields:d}=this.getGeneratorAndShields(),h=this.state.shieldInputs,b={};let S=!1;d.forEach(y=>{h[y.id]!==y.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${y.id}`)&&(b[y.id]=y.currentHealth,S=!0)}),S&&this.setState(y=>({shieldInputs:{...y.shieldInputs,...b}}))}render(){const{generator:r,shields:l,systemName:d}=this.getGeneratorAndShields();return r?m.jsxs(iA,{children:[m.jsx(aA,{children:d==="ThirdspaceShield"?"Thirdspace Shields":"Thought Shields"}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791"},children:["Unallocated Shield Energy: ",r.storedCapacity]}),d==="ThirdspaceShield"&&m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791",display:"flex",justifyContent:"center",alignItems:"center",gap:"5px"},children:["Regeneration Rate: ",shipManager.systems.getOutputNoBoost(this.props.ship,r)+shipManager.power.getBoost(r)*l.length,m.jsx(Oa,{className:"small",onClick:()=>this.handleDeBoost(r),title:"Reduce Boost",children:"-"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleBoost(r),title:"Boost Generator",children:"+"})]}),m.jsxs(oA,{ref:this.listRef,children:[l.map(h=>m.jsxs(bC,{onMouseEnter:()=>this.handleMouseEnter(h),onMouseLeave:()=>this.handleMouseLeave(h),children:[m.jsx(lA,{children:this.getShieldLabel(h)}),m.jsxs(sA,{children:[m.jsx(Oa,{onClick:()=>this.handleMin(h),disabled:!h.canDecrease(),title:"Drop shield to 0",children:"Min"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleDecrease(h,25),disabled:!h.canDecrease(),children:"-25"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleDecrease(h,10),disabled:!h.canDecrease(),children:"-10"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleDecrease(h,5),disabled:!h.canDecrease(),children:"-5"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleDecrease(h,1),disabled:!h.canDecrease(),children:"-1"}),m.jsx(uA,{id:`shield-input-${h.id}`,type:"number",value:this.state.shieldInputs[h.id]!==void 0?this.state.shieldInputs[h.id]:h.currentHealth,onChange:b=>this.handleInputChange(h,b.target.value),onWheel:b=>this.handleWheel(b,h)}),m.jsx(Oa,{className:"small",onClick:()=>this.handleIncrease(h,1),disabled:!h.canIncrease(),children:"+1"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleIncrease(h,5),disabled:!h.canIncrease(),children:"+5"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleIncrease(h,10),disabled:!h.canIncrease(),children:"+10"}),m.jsx(Oa,{className:"small",onClick:()=>this.handleIncrease(h,25),disabled:!h.canIncrease(),children:"+25"}),m.jsx(Oa,{onClick:()=>this.handleMax(h),disabled:!h.canIncrease(),title:"Raise shield to maximum",children:"Max"})]})]},h.id)),l.length===0&&m.jsx(bC,{children:"No Shields Found"})]}),r&&m.jsx(cA,{onClick:()=>this.handleEqualise(r),children:"Equalise Shields"})]}):m.jsx("div",{children:"No Generator Found"})}}const SC=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${A.colors.line};
`,CC=D.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${A.colors.line};
    border-bottom: 1px solid ${A.colors.line};
    color: ${A.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,Fx=D.div`
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
`,bp=D.div`
    flex: 1;
`,wp=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
`,dA=D.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${A.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`,md=D.div`
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
`;class fA extends Ge.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}canBoost(){const{ship:r,system:l}=this.props;return l.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(r,l)}canDeBoost(){const{ship:r,system:l}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(l)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){const{ship:r,system:l}=this.props,d=shipManager.power.getBoost(l),h=l.active;return m.jsxs(SC,{children:[m.jsx(CC,{children:"Power Capacitor"}),l.boostable&&m.jsxs(Fx,{children:[m.jsx(bp,{children:"Open Petals"}),m.jsxs(wp,{children:[m.jsx(md,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:d===0,children:"OFF"}),m.jsx(md,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:d>0,$variant:"activate",children:"ON"})]})]}),m.jsxs(Fx,{children:[m.jsx(bp,{children:"Double Recharge"}),m.jsxs(wp,{children:[m.jsx(md,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!h,children:"OFF"}),m.jsx(md,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:h,$variant:"activate",children:"ON"})]})]})]})}}const EC=D(SC)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`,ii={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",hover:"rgba(75, 43, 81, 0.6)",text:"#f2f2f2",textDim:"#d8b9e6"},pA=D(EC)`
    background-color: ${ii.surface};
    border: 1px solid ${ii.line};
`,hA=D.div`
    padding: 3px;
    background-color: ${ii.line};
    border: 1px solid ${ii.accent};
    color: ${ii.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,TC=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 8px;
    border-bottom: 1px solid ${ii.line};
    font-size: 11px;
    color: ${ii.text};

    &:hover {
        background-color: ${ii.hover};
    }
`,gA=D.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${ii.textDim};
`,nm=D.div`
    min-width: 24px;
    width: ${o=>o.$wide?"auto":"24px"};
    padding: ${o=>o.$wide?"0 6px":"0"};
    height: 18px;
    box-sizing: border-box;
    background: ${ii.line};
    border: 1px solid ${ii.accent};
    color: ${ii.text};
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
        background: ${ii.accent};
        border: 1px solid #a06daa;
        color: #ffffff;
        opacity: 1;
        cursor: default;
        &:hover { background: ${ii.accent}; border: 1px solid #a06daa; }
    `}

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: ${ii.textDim}; border: 1px solid ${ii.accent}; }
    `}
`;class mA extends Ge.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}handlePower(r){const{ship:l,system:d}=this.props;if(!kC(l,d))return;const h=d.getAbductionOrder();d.setAbductionPowerLevel(d.getAbductionPowerLevel(h)+r)&&(webglScene.customEvent("SystemDataChanged",{ship:d.getOwningUnit()||l,system:d}),this.forceUpdate())}handleCancel(){const{ship:r,system:l}=this.props;gamedata.gamephase!==1||!gamedata.isMyShip(l.getOwningUnit()||r)||(weaponManager.removeFiringOrder(l.getOwningUnit()||r,l),this.forceUpdate())}renderMaintain(){const{system:r}=this.props,l=r.isMaintainingVortex(),d=r.chargesVortexUpkeep()?r.getVortexUpkeepCost():0;return m.jsxs($n.Fragment,{children:[m.jsx(CC,{children:"Jump Point"}),m.jsxs(Fx,{children:[m.jsx(bp,{children:"Maintain Vortex"}),m.jsxs(wp,{children:[m.jsx(md,{onClick:()=>this.handleDeactivate(),disabled:!r.canDeactivate(),$active:!l,children:"OFF"}),m.jsx(md,{onClick:()=>this.handleActivate(),disabled:!r.canActivate(),$active:l,$variant:"activate",children:"ON"})]})]}),m.jsx(dA,{children:d>0?l?"Held open. "+d+" power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.":"Closes at end of turn unless maintained. Costs "+d+" power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.":l?"Held open. All powered systems except the Scanner are shut down this turn.":"Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner."})]})}renderAbduction(){const{ship:r,system:l}=this.props,d=l.getAbductionOrder(),h=gamedata.getShip(d.targetid),b=l.getAbductionPowerLevel(d),S=kC(r,l),y=l.isExtraDimensional(),E=l.abductionMaxPower||1,$=window.JumpEngine.formatAbductionHalves(l.getAbductionHalves(d)),M=window.JumpEngine.getAbductionChain(d.targetid),N=window.JumpEngine.getAbductionCostPreview(d.targetid),j=l.isAbductionPowered(d);let H;if(j)H=$+" power-turn"+($==="1"?"":"s")+" for "+l.getAbductionPowerDraw()+" power. So far "+window.JumpEngine.formatAbductionHalves(M.total)+" of "+M.cost+" power-turns.";else{const L=!!h&&gamedata.isTerrain(h.shipSizeClass,h.userid),K=!!h&&((h.hexOffsets||[]).length>0||h.Huge>0);H=(L?"Targeting: takes hold if "+(K?"every hex it occupies is":"it is")+" inside your connected field and your OEW beats its DEW.":"Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.")+" Power can be applied from next turn"+(N!==null?", and "+N+" power-turns will abduct it.":".")}return m.jsxs(pA,{children:[m.jsxs(hA,{children:["Abduction",h?": "+h.name:""]}),j&&m.jsxs(TC,{children:[m.jsx(bp,{children:y?"Power":"Double power"}),y?m.jsxs(wp,{children:[m.jsx(nm,{onClick:()=>this.handlePower(-1),disabled:!S||b<=1,children:"-"}),m.jsx(nm,{$active:!0,children:"x"+b}),m.jsx(nm,{onClick:()=>this.handlePower(1),disabled:!S||b>=E,children:"+"})]}):null]}),gamedata.gamephase===1&&gamedata.isMyShip(l.getOwningUnit()||r)&&m.jsxs(TC,{children:[m.jsx(bp,{children:"Declaration"}),m.jsx(wp,{children:m.jsx(nm,{$wide:!0,onClick:()=>this.handleCancel(),children:"CANCEL"})})]}),m.jsx(gA,{children:H})]})}render(){const{system:r}=this.props,l=r.canMaintainVortex()||r.canDeactivate(),d=typeof r.getAbductionOrder=="function"&&!!r.getAbductionOrder();return m.jsxs($n.Fragment,{children:[l&&m.jsx(EC,{children:this.renderMaintain()}),d&&this.renderAbduction()]})}}const kC=(o,r)=>gamedata.gamephase===1&&gamedata.isMyShip(r.getOwningUnit()||o)&&r.isExtraDimensional()&&r.isAbductionPowered(),Aa={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",text:"#f2f2f2"},vA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${o=>o.$isWeapon||o.$isPurple?Aa.surface:"rgba(16, 26, 38, 0.9)"};
    border: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Aa.line:A.colors.line};
`,yA=D.div`
    padding: 3px;
    background-color: ${o=>o.$isWeapon?"#571616":o.$isPurple?Aa.line:"#215a7a"};
    border: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Aa.line:A.colors.line};
    border-bottom: 1px solid ${o=>o.$isWeapon?"#b43131":o.$isPurple?Aa.line:A.colors.line};
    color: ${o=>o.$isWeapon||o.$isPurple?Aa.text:A.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,xA=D.div`
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
`;const bA=D.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,RC=D.div`
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
`;class wA extends Ge.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivateAll(r){r.preventDefault();const{ship:l,system:d}=this.props;let h=[];l.flight?h=l.systems.map(S=>S.systems).reduce((S,y)=>S.concat(y),[]):h=l.systems;let b=!1;h.forEach(S=>{!S.name||S.name!==d.name||S.canActivate&&typeof S.canActivate=="function"&&S.canActivate()&&(S.doActivate(),b=!0)}),b&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:l,system:d}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(r){r.preventDefault();const{ship:l,system:d}=this.props;let h=[];l.flight?h=l.systems.map(S=>S.systems).reduce((S,y)=>S.concat(y),[]):h=l.systems;let b=!1;h.forEach(S=>{!S.name||S.name!==d.name||S.canDeactivate&&typeof S.canDeactivate=="function"&&S.canDeactivate()&&(S.doDeactivate(),b=!0)}),b&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:l,system:d}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate=="function"&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate=="function"&&this.props.system.canDeactivate()}render(){const{ship:r,system:l}=this.props,d=l.active||l.weapon&&!l.activationIsToggle&&weaponManager.hasFiringOrder(r,l),h=typeof l.getActivateLabel=="function"?l.getActivateLabel():null,b=typeof l.getDeactivateLabel=="function"?l.getDeactivateLabel():null,S=h||(l.weapon?"Fire":"Activate"),y=b||(l.weapon?"Don't Fire":"Deactivate"),E=!!l.singleActivationButton,$=!E||this.canActivate(),M=(!l.weapon||l.activationIsToggle)&&(!E||this.canDeactivate()),N=!!l.activationMenuPurple&&!l.weapon;return m.jsxs(vA,{$isWeapon:l.weapon,$isPurple:N,children:[m.jsx(yA,{$isWeapon:l.weapon,$isPurple:N,children:l.displayName}),m.jsx(xA,{$isPurple:N,children:m.jsxs(bA,{children:[$&&m.jsx(RC,{onClick:()=>this.handleActivate(),onContextMenu:j=>this.handleActivateAll(j),disabled:!this.canActivate(),$active:d,$variant:"activate",$isWeapon:l.weapon,$isToggle:!!l.activationIsToggle,$isPurple:N,children:S}),M&&m.jsx(RC,{onClick:()=>this.handleDeactivate(),onContextMenu:j=>this.handleDeactivateAll(j),disabled:!this.canDeactivate(),$active:!d,$variant:"deactivate",$isPurple:N,children:y})]})})]})}}const SA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,CA=D.div`
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
`,Bx=D.div`
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
`,Ix=D.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,rm=D.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,EA=D.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`,xs=D.div`
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
`;class TA extends Ge.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(r){r.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){const{ship:r,system:l}=this.props;for(;shipManager.power.getBoost(l)>0;)shipManager.power.clickMinus(r,l);shipManager.power.onOfflineClicked(r,l),this.handleUpdate()}}handleOfflineAll(r){if(r.preventDefault(),this.canOffline()){const{ship:l,system:d}=this.props;for(;shipManager.power.getBoost(d)>0;)shipManager.power.clickMinus(l,d);shipManager.power.offlineAll(l,d),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system})}canOffline(){const{ship:r,system:l}=this.props;return gamedata.gamephase===1&&(l.canOffLine||l.powerReq>0)&&!l.powerLocked&&!shipManager.power.isOffline(r,l)&&!weaponManager.hasFiringOrder(r,l)}canOnline(){const{ship:r,system:l}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(r,l)&&!shipManager.power.isForcedOffline(r,l)&&!shipManager.power.isVortexLockedOffline(r,l)}canBoost(){const{ship:r,system:l}=this.props;return l.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(r,l)&&(!l.isScanner()||l.id==shipManager.power.getHighestSensorsId(r))&&l.name!=="ThirdspaceShieldGenerator"&&l.name!=="powerCapacitor"&&l.name!=="PowerCapacitor"}canDeBoost(){const{ship:r,system:l}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(l)&&l.name!=="ThirdspaceShieldGenerator"&&l.name!=="powerCapacitor"&&l.name!=="PowerCapacitor"}canOverload(){const{ship:r,system:l}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(r,l)&&l.weapon&&l.overloadable&&!shipManager.power.isOverloading(r,l)}canStopOverload(){const{ship:r,system:l}=this.props;return gamedata.gamephase===1&&l.weapon&&l.overloadable&&shipManager.power.isOverloading(r,l)&&(l.overloadshots>=l.extraoverloadshots||l.overloadshots==0)}render(){const{ship:r,system:l}=this.props,d=this.canOffline()||this.canOnline(),h=l.boostable&&(this.canBoost()||this.canDeBoost()),b=l.overloadable&&(this.canOverload()||this.canStopOverload());if(!d&&!h&&!b)return null;const S=shipManager.power.isOffline(r,l),y=shipManager.power.getBoost(l),E=shipManager.power.isOverloading(r,l),$=l.name==="reactor",M=l.name==="jumpEngine",N=M&&typeof l.getChargeBoostMax=="function"&&l.getChargeBoostMax()>0;let j="Boost Level";return $&&(j="Self-Destruct"),M&&(j=N?"Extra Charging":"Jump to Hyperspace"),m.jsxs(SA,{children:[m.jsx(CA,{children:"Power Settings"}),d&&m.jsxs(Bx,{children:[m.jsx(Ix,{children:"Power"}),m.jsxs(rm,{children:[m.jsx(xs,{onClick:()=>this.handleOnline(),onContextMenu:H=>this.handleOnlineAll(H),disabled:!this.canOnline(),$active:!S,$variant:"activate",children:"On"}),m.jsx(xs,{onClick:()=>this.handleOffline(),onContextMenu:H=>this.handleOfflineAll(H),disabled:!this.canOffline(),$active:S,$variant:"deactivate",children:"Off"})]})]}),h&&m.jsxs(Bx,{children:[m.jsx(Ix,{children:j}),$||M&&!N?m.jsxs(rm,{children:[m.jsx(xs,{onClick:()=>this.handleBoost(),disabled:y>0||!this.canBoost(),$active:y>0,$variant:$?"deactivate":"risk",children:"Yes"}),m.jsx(xs,{onClick:()=>this.handleDeBoost(),disabled:y===0||!this.canDeBoost(),$active:y===0,$variant:"activate",children:"No"})]}):m.jsxs(rm,{children:[m.jsx(xs,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:"-"}),m.jsx(EA,{children:y}),m.jsx(xs,{onClick:()=>this.handleBoost(),$narrow:!0,children:"+"})]})]}),b&&m.jsxs(Bx,{children:[m.jsx(Ix,{children:"Overcharge"}),m.jsxs(rm,{children:[m.jsx(xs,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:E,$variant:"warning",children:"Yes"}),m.jsx(xs,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!E,$variant:"deactivate",children:"No"})]})]})]})}}const kA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,RA=D.div`
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
`,DA=D.div`
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
`,DC=D.div`
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
`,MA=D.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,$A=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,MC=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,OA=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,AA=D.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,jA=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,_A=D.div`
    width: 20px;
    text-align: center;
`,Ux=D.div`
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
`;class LA extends Ge.Component{constructor(r){super(r),this.listRef=$n.createRef()}handleIncrease(r){const{system:l}=this.props;l.setCurrShipType(r),l.canIncrease()&&(l.doIncrease(),this.forceUpdate())}handleDecrease(r){const{system:l}=this.props;l.setCurrShipType(r),l.canDecrease()&&(l.doDecrease(),this.forceUpdate())}handlePropagate(r){const{ship:l,system:d}=this.props,h=window.gamedata,b=window.shipManager,S=window.webglScene,y=!!(d.hasMultiTarget&&d.hasMultiTarget()),E=y?d.getCurrWeaponId():null,$=y?d.getMineWeapons():[],M=y?$.find(Ae=>String(Ae.id)===String(E)):null;if(y&&!M)return;d.setCurrShipType(r);const N=d.rangeSetting||d.range,j=y?d.allocatedRanges[E]:d.allocatedRanges;if(!j)return;const H=j[r]===null||j[r]===void 0?N:j[r];var L=[];for(var K in h.ships){var de=h.ships[K];if(de.userid==l.userid&&!b.isDestroyed(de)){if(de.phpclass&&l.phpclass){if(de.phpclass!=l.phpclass)continue}else if(de.shipClass!=l.shipClass)continue;for(var Ee in de.systems){var pe=de.systems[Ee];if(pe&&pe.name===d.name){var ae=!!(pe.hasMultiTarget&&pe.hasMultiTarget());ae===y&&L.push({unit:de,ctrl:pe})}}}}for(var ce=0;ce<L.length;ce++){var Te=L[ce],pe=Te.ctrl;if(y){pe.ensureMultiAllocatedShape&&pe.ensureMultiAllocatedShape();var le=pe.getMineWeapons(),ue=le.find(rt=>rt.displayName===M.displayName&&rt.indexInGroup===M.indexInGroup);if(!ue)continue;pe.setCurrWeaponId(ue.id)}pe.setCurrShipType(r);let ft=0;const Ve=()=>y?pe.allocatedRanges[pe.getCurrWeaponId()]:pe.allocatedRanges,Tt=()=>pe.range||pe.rangeSetting,bt=()=>{const rt=Ve();if(!rt)return Tt();const He=rt[r];return He??Tt()};for(;bt()<H&&pe.canIncrease()&&ft<100;)pe.doIncrease(),ft++;for(;bt()>H&&pe.canDecrease()&&ft<100;)pe.doDecrease(),ft++;ft>=100&&console.warn("Mine Settings Propagation safety break for",pe)}S.customEvent("SystemDataChanged",{ship:l,system:d})}cycleWeapon(r){const{system:l}=this.props;if(!l.hasMultiTarget||!l.hasMultiTarget())return;const d=l.getMineWeapons();if(d.length===0)return;const h=l.getCurrWeaponId();let b=d.findIndex(y=>String(y.id)===String(h));b<0&&(b=0);const S=(b+r+d.length)%d.length;l.setCurrWeaponId(d[S].id),this.forceUpdate()}render(){const{system:r}=this.props;if(!r||(r.range=r.range||r.rangeSetting,!r.range))return null;const l=!!(r.hasMultiTarget&&r.hasMultiTarget());let d=[],h=null,b=null,S=r.allocatedRanges||{};l?(r.ensureMultiAllocatedShape&&r.ensureMultiAllocatedShape(),d=r.getMineWeapons(),h=r.getCurrWeaponId(),b=d.find(L=>String(L.id)===String(h))||d[0]||null,S=h!=null&&r.allocatedRanges[h]?r.allocatedRanges[h]:{}):(r.ensureFlatAllocatedShape&&r.ensureFlatAllocatedShape(),S=r.allocatedRanges||{});const y=Object.keys(S),E=r.validTargets||y,$=L=>{if(!E.includes(L))return"N/A";const K=S[L];return K??r.range},M=L=>E.includes(L)?$(L)<r.range:!1,N=L=>E.includes(L)?$(L)>0:!1,j=L=>!!E.includes(L),H=l&&d.length>0?m.jsxs(DA,{children:[m.jsx(DC,{onClick:()=>this.cycleWeapon(-1),disabled:d.length<2,title:"Previous weapon",children:"<"}),m.jsx(MA,{children:b?b.label:""}),m.jsx(DC,{onClick:()=>this.cycleWeapon(1),disabled:d.length<2,title:"Next weapon",children:">"})]}):m.jsx(RA,{children:"Set Mine Range"});return m.jsxs(kA,{children:[H,m.jsxs($A,{ref:this.listRef,children:[y.map(L=>m.jsxs(MC,{children:[m.jsx(OA,{src:`./img/systemicons/BFCPclasses/${L}.png`,alt:L}),m.jsx(AA,{children:L}),m.jsxs(jA,{onWheel:K=>{K.deltaY<0&&M(L)?this.handleIncrease(L):K.deltaY>0&&N(L)&&this.handleDecrease(L)},children:[m.jsx(Ux,{onClick:()=>this.handleDecrease(L),disabled:!N(L),children:"-"}),m.jsx(_A,{children:$(L)}),m.jsx(Ux,{onClick:()=>this.handleIncrease(L),disabled:!M(L),children:"+"}),m.jsx(Ux,{title:l?"Propagate this weapon's settings to same-class mines with Multiple Targets":"Propagate to all mines of same type",onClick:()=>this.handlePropagate(L),disabled:!j(L),style:{marginLeft:"5px"},children:m.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},L)),y.length===0&&m.jsx(MC,{children:"No ship types available"})]}),m.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Max Range: ",r.range]})]})}}const zA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,NA=D.div`
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
`,PA=D.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,$C=D.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,FA=D.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,BA=D.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,IA=D.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,UA=D.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${o=>o.$active?"#4CAF50":"#F44336"};
`,OC=D.div`
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
`;class HA extends Ge.Component{constructor(r){super(r),this.listRef=$n.createRef()}handleToggle(r){const{system:l}=this.props;l.setCurrShipType(r),l.canSet()?(l.doSet(),this.forceUpdate()):l.canUnset()&&(l.doUnset(),this.forceUpdate())}handlePropagate(r){const{ship:l,system:d}=this.props,h=window.gamedata,b=window.shipManager,S=window.webglScene;console.log("Propagating Mine settings for:",r),d.setCurrShipType(r);const y=d.allocatedShipTypes[r];var E=[];for(var $ in h.ships){var M=h.ships[$];if(M.userid==l.userid&&!b.isDestroyed(M))for(var N=0;N<M.systems.length;N++){var j=M.systems[N];if(M.shipClass==l.shipClass&&j.name===d.name){E.push(j);break}}}console.log("Found Mine Weapons of same type:",E.length);for(var H=0;H<E.length;H++){var j=E[H];j.setCurrShipType(r);let K=0;for(;j.allocatedShipTypes[r]!==y&&(y?j.canSet():j.canUnset())&&K<10;)y?j.doSet():j.doUnset(),K++;K>=10&&console.warn("Mine Settings Propagation safety break for",j)}S.customEvent("SystemDataChanged",{ship:l,system:d})}render(){const{system:r}=this.props;if(!r)return null;const l=r.allocatedShipTypes||{},d=Object.keys(l),h=S=>l[S]?"YES":"NO",b=S=>{const y=Number(this.props.ship.spawned),E=y===-1?1:y+1;return window.gamedata.turn===E};return m.jsxs(zA,{children:[m.jsx(NA,{children:"Set Target Types"}),m.jsxs(PA,{ref:this.listRef,children:[d.map(S=>m.jsxs($C,{children:[m.jsx(FA,{src:`./img/systemicons/BFCPclasses/${S}.png`,alt:S}),m.jsx(BA,{children:S}),m.jsxs(IA,{children:[m.jsx(UA,{$active:l[S],children:h(S)}),m.jsx(OC,{onClick:()=>this.handleToggle(S),disabled:!b(),style:{marginLeft:"5px"},children:"Toggle"}),m.jsx(OC,{title:"Propagate to all mines of same type",onClick:()=>this.handlePropagate(S),disabled:!1,style:{marginLeft:"5px",width:"16px",padding:"0"},children:m.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},S)),d.length===0&&m.jsx($C,{children:"No ship types available"})]})]})}}const Hx=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: ${A.colors.greenBg};
    border: 1px solid ${A.colors.greenLine};
`,AC=D.div`
    padding: 3px;
    background-color: ${A.colors.greenTitleBg};
    border: 1px solid ${A.colors.greenLine};
    color: ${A.colors.greenText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,VA=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: ${A.colors.greenTitleBg};
    border: 1px solid ${A.colors.greenLine};
    color: ${A.colors.greenText};
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 2px;
    min-width: 220px;
`,jC=D.div`
    width: 20px;
    height: 18px;
    background: ${A.colors.greenBtnBg};
    border: 1px solid ${A.colors.greenBtnLine};
    color: ${A.colors.greenText};
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: ${A.colors.greenBtnLine};
        border: 1px solid ${A.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${A.colors.greenBtnBg}; border: 1px solid ${A.colors.greenBtnLine}; color: ${A.colors.greenText}; }
    `}
`,WA=D.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;D.div`
    text-align: center;
    color: ${A.colors.greenLabel};
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;const Sp=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,_C=D.div`
    width: 70px;
    font-size: 10px;
    color: ${A.colors.greenLabel};
    user-select: none;
`,zu=D.div`
    flex: 1;
    height: 20px;
    background: ${A.colors.greenBtnBg};
    border: 1px solid ${A.colors.greenBtnLine};
    color: ${A.colors.greenText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.95;
    user-select: none;

    &:hover {
        background: ${A.colors.greenBtnLine};
        border: 1px solid ${A.colors.greenBtnLineHover};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.$active&&`
        background: ${A.colors.greenBtnLine};
        border: 1px solid ${A.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${A.colors.greenGlow};
        color: #ffffff;
        opacity: 1;
    `}

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${A.colors.greenBtnBg}; border: 1px solid ${A.colors.greenBtnLine}; color: ${A.colors.greenText}; }
    `}
`;class YA extends Ge.Component{refresh(){const{ship:r,system:l}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:l})}cycleMode(r){const{system:l}=this.props;if(!gamedata.isMyShip(this.props.ship))return;const d=l.firingMode==1?2:1;l.setFiringMode(d),typeof l.initializationUpdate=="function"&&l.initializationUpdate(),this.refresh()}activateMode1(){const{ship:r,system:l}=this.props;l.canActivate()&&(l.doActivate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:l}),webglScene.customEvent("CloseSystemInfo"))}targetWarrior(){const{ship:r,system:l}=this.props;weaponManager.isSelectedWeapon(l)||weaponManager.selectWeapon(r,l),webglScene.customEvent("SystemDataChanged",{ship:r,system:l}),webglScene.customEvent("CloseSystemInfo")}setRotation(r,l){const{system:d}=this.props;d.rotationDirection=r,d.rotationAmount=l,typeof d.updateRotationNotes=="function"&&d.updateRotationNotes(),this.refresh()}renderInitialOrders(){const{system:r}=this.props,l=parseInt(r.firingMode,10),d=r.firingModes[l]||"";return m.jsxs(Hx,{children:[m.jsxs(VA,{children:[m.jsx(jC,{onClick:()=>this.cycleMode(-1),title:"Previous mode",children:"<"}),m.jsx(WA,{children:d}),m.jsx(jC,{onClick:()=>this.cycleMode(1),title:"Next mode",children:">"})]}),l==1&&m.jsx(Sp,{children:m.jsx(zu,{onClick:()=>this.activateMode1(),children:"Activate"})}),l==2&&m.jsx(Sp,{children:m.jsx(zu,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(r),children:"Target friendly Warrior"})})]})}engageMode3(){const{ship:r,system:l}=this.props;gamedata.isMyShip(r)&&(weaponManager.hasFiringOrder(r,l)||(l.setFiringMode(3),typeof l.initializationUpdate=="function"&&l.initializationUpdate(),this.refresh()))}renderPreFiring(){const{system:r}=this.props;if(r.firingMode!=3)return m.jsxs(Hx,{children:[m.jsx(AC,{children:"Gravitic Augmenter"}),m.jsx(Sp,{children:m.jsx(zu,{onClick:()=>this.engageMode3(),children:"Engage Gravity Shifting"})})]});const l=r.rotationDirection||1,d=r.rotationAmount||1,h=(b,S)=>l==b&&d==S;return m.jsxs(Hx,{children:[m.jsx(AC,{children:"Gravity Shift Settings"}),m.jsxs(Sp,{children:[m.jsx(_C,{children:"Clockwise"}),m.jsx(zu,{onClick:()=>this.setRotation(1,1),$active:h(1,1),children:"60°"}),m.jsx(zu,{onClick:()=>this.setRotation(1,2),$active:h(1,2),children:"120°"})]}),m.jsxs(Sp,{children:[m.jsx(_C,{children:"Anti-Clockwise"}),m.jsx(zu,{onClick:()=>this.setRotation(2,1),$active:h(2,1),children:"60°"}),m.jsx(zu,{onClick:()=>this.setRotation(2,2),$active:h(2,2),children:"120°"})]})]})}render(){const{system:r}=this.props;return r?gamedata.gamephase==1?(r.firingMode==3&&(r.setFiringMode(1),typeof r.initializationUpdate=="function"&&r.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}}const im=3,GA=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,KA=D.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,QA=D.div`
    text-align: center;
    color: ${o=>o.$empty?"#f0a0a0":"#f2f2f2"};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,qA=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,XA=D.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,LC=D.div`
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
`,JA=D.input`
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
`;const Nu=[{key:"hitBoost5",label:"Hit Chance",increment:5,prefix:"+",suffix:"%",display:o=>o*2,parse:o=>o/2},{key:"shotBoost",label:"Shots",increment:1,prefix:"+",suffix:"",display:o=>o,parse:o=>o},{key:"dmgBoost5",label:"Damage",increment:5,prefix:"+",suffix:"",display:o=>o,parse:o=>o}],ZA=(o,r)=>o.prefix+o.display(r|0)+o.suffix;class ej extends Ge.Component{fieldSteps(r,l){return(l|0)/r.increment}allocatedSteps(r){return Nu.reduce((l,d)=>l+this.fieldSteps(d,r[d.key]),0)}maxSteps(){const{ship:r,system:l}=this.props;return typeof l.getMaxSteps=="function"?l.getMaxSteps(r):Math.floor(shipManager.movement.getRemainingEngineThrust(r)/im)}totalShots(r){return(r.guns|0)+(r.shotBoost|0)}maxDamageSteps(r){return this.totalShots(r)}clampDamageToShots(r){const l=Nu.find(h=>h.key==="dmgBoost5"),d=this.maxDamageSteps(r)*l.increment;(r.dmgBoost5|0)>d&&(r.dmgBoost5=d)}refresh(){const{ship:r,system:l}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:r,system:l})}setValue(r,l){const{ship:d,system:h}=this.props;if(!gamedata.isMyShip(d))return;let b=Math.max(0,Math.round((l|0)/r.increment)*r.increment);const S=this.allocatedSteps(h)-this.fieldSteps(r,h[r.key]),y=Math.max(0,this.maxSteps()-S);if(b/r.increment>y&&(b=y*r.increment),r.key==="dmgBoost5"){const $=this.maxDamageSteps(h)*r.increment;b>$&&(b=$)}h[r.key]=b,r.key==="shotBoost"&&this.clampDamageToShots(h),typeof h.updateBoostNotes=="function"&&h.updateBoostNotes(),this.syncFlight(h),this.refresh()}syncFlight(r){const l=this.getFlightPulsars();for(let d=0;d<l.length;d++){const h=l[d];h!==r&&(Nu.forEach(b=>{h[b.key]=r[b.key]|0}),typeof h.updateBoostNotes=="function"&&h.updateBoostNotes())}}step(r,l){const{system:d}=this.props;this.setValue(r,(d[r.key]|0)+l*r.increment)}onWheel(r,l){l.preventDefault(),this.step(r,l.deltaY<0?1:-1)}onInput(r,l){const d=String(l.target.value).replace(/[^0-9]/g,""),h=d===""?0:parseInt(d,10);this.setValue(r,r.parse(h))}propagate(){const{ship:r,system:l}=this.props;if(!gamedata.isMyShip(r))return;const d=this.getFlightPulsars();for(let h=0;h<d.length;h++){const b=d[h];b!==l&&(Nu.forEach(S=>{b[S.key]=l[S.key]|0}),this.clampWeaponToBudget(b),typeof b.updateBoostNotes=="function"&&b.updateBoostNotes())}this.refresh()}getFlightPulsars(){const{ship:r}=this.props,l=[],d=r&&r.systems?r.systems:[];for(let h=0;h<d.length;h++){const b=d[h]&&d[h].systems?d[h].systems:[];for(let S=0;S<b.length;S++)b[S]&&b[S].name==="MinorThoughtPulsar"&&l.push(b[S])}return l}clampWeaponToBudget(r){const l=["dmgBoost5","shotBoost","hitBoost5"],d=b=>Nu.find(S=>S.key===b);let h=0;for(;h++<200&&!(Nu.reduce((S,y)=>S+(r[y.key]|0)/y.increment,0)<=this.maxSteps());)for(let S=0;S<l.length;S++){const y=l[S];if((r[y]|0)>0){r[y]=(r[y]|0)-d(y).increment;break}}}render(){const{ship:r,system:l}=this.props;if(!l)return null;const d=typeof l.getSpareThrust=="function"?l.getSpareThrust(r):shipManager.movement.getRemainingEngineThrust(r),h=this.allocatedSteps(l)*im,b=Math.max(0,d-h),S=b>=im;return m.jsxs(GA,{children:[m.jsx(KA,{children:"Minor Thought Pulsar"}),m.jsxs(QA,{$empty:b<im,children:["Available thrust: ",b]}),Nu.map(y=>{const E=l[y.key]|0,$=y.key==="dmgBoost5"&&E/y.increment>=this.maxDamageSteps(l);return m.jsxs(qA,{children:[m.jsx(XA,{children:y.label}),m.jsx(LC,{title:"Less",disabled:E<=0,onClick:()=>this.step(y,-1),children:"−"}),m.jsx(JA,{type:"text",value:ZA(y,E),onChange:M=>this.onInput(y,M),onWheel:M=>this.onWheel(y,M)}),m.jsx(LC,{title:$?"One +5 per shot (add shots for more)":"More",disabled:!S||$,onClick:()=>this.step(y,1),children:"+"})]},y.key)})]})}}const Cp=o=>{let r=null;const l=d=>{d.preventDefault(),o(d)};return d=>{r!==d&&(r&&r.removeEventListener("wheel",l,{passive:!1}),r=d,r&&r.addEventListener("wheel",l,{passive:!1}))}},Ue={bg:"linear-gradient(180deg, rgb(27, 45, 62), rgb(18, 32, 45))",line:"#2a6b8f",radius:"6px",shadow:"0 8px 28px rgba(0, 0, 0, 0.6)",titleBg:"linear-gradient(180deg, rgb(54, 79, 110), rgb(36, 57, 80))",title:"#c6e2ff",text:"#deebff",dim:"#8ca5c0",btnBg:"#081420",btnText:"#deebff",well:"#000000",focus:"#8bcaf2"},ai={enh:{rail:A.colors.enhLine,bar:"#c39a52",wash:A.colors.enhBg,title:A.colors.enhTitle,btnBg:"#292114",btnText:A.colors.enhTitle},damage:{rail:"#2f7f92",bar:"#4cb8d0",wash:"rgba(58, 159, 181, 0.26)",title:"#e0f5fa",btnBg:"#0f262d",btnText:"#b6e3ee"},crit:{rail:"#a85c33",bar:"#dd7643",wash:"rgba(168, 92, 51, 0.34)",title:"#ffe8dc",btnBg:"#291914",btnText:"#eab99e"}},tj={rail:Ue.line,bar:Ue.line,btnBg:Ue.btnBg,btnText:Ue.btnText},bl=o=>o.$ink||tj,nj=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px 4px 10px;
    font-size: 11px;
    color: ${o=>o.$gold?A.colors.enhText:Ue.text};
`,rj=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,ij=D.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;D.span`
    flex: 0 0 auto;
    color: ${o=>o.$gold?A.colors.enhText:Ue.dim};
    font-size: 10px;
    opacity: ${o=>o.$gold?.75:1};
`;const vd=D.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${o=>bl(o).btnBg};
    border: 1px solid ${o=>bl(o).rail};
    border-radius: 2px;
    color: ${o=>bl(o).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${o=>bl(o).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${bl(o).btnBg};
            color: ${bl(o).btnText};
        }
    `}
`,zC=D.input`
    flex: 0 0 44px;
    width: 44px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${A.fonts.mono};
    font-size: 12px;
    color: ${o=>o.$destroyed?"#ff8a80":"#ffffff"};
    background-color: ${Ue.well};
    border: 1px solid ${o=>bl(o).rail};
    border-radius: 2px;
    outline: none;

    &:focus {
        border-color: ${o=>bl(o).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,NC=D.div`
    padding: 6px 10px 5px;
    background: ${Ue.titleBg};
    border-bottom: 1px solid ${Ue.line};
    color: ${Ue.title};
    text-align: left;
    font-family: ${A.fonts.display};
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    user-select: none;
    ${o=>o.$sticky?"position: sticky; top: 0; z-index: 1;":""}
`,Vx=o=>`
    color: ${o.title};
    background-color: ${o.wash};
    background-image: linear-gradient(to right, ${o.wash}, rgba(0, 0, 0, 0) 75%);
    border-left: 3px solid ${o.bar};
    border-top: 1px solid ${o.rail};
    border-bottom: 1px solid ${o.rail};
`,Wx=D.div`
    padding: 5px 10px 4px 7px;
    text-align: left;
    font-family: ${A.fonts.display};
    font-size: 9.5px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.65);
    user-select: none;

    /*ApplyDamageMenu has no title bar any more, so whichever bar comes first butts straight
      onto the container's own 1px border - two hairlines in two colours, which reads as a
      rendering fault rather than as a frame. Self-maintaining: it is always whichever section
      happens to be on top, and Enhancements is absent more often than not.*/
    &:first-child {
        border-top: none;
    }
`,Yx=D.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 3px 0 0 ${o=>bl(o).bar};
`,aj=D(Wx)`
    ${Vx(ai.enh)}
`,oj=D(Wx)`
    ${Vx(ai.damage)}
`,lj=D(Wx)`
    ${Vx(ai.crit)}
`,PC=D.div`
    height: 2px;
    background-color: ${o=>o.$chrome?Ue.line:A.colors.enhLine};
    opacity: 0.8;
`,sj=D.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,uj=lj,cj=D.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${A.colors.warningSoft};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${o=>o.$empty&&`
        color: #6f6257;
    `}
`,dj=D.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,fj=D.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${Ue.dim};
`,pj=D.div`
    flex: 0 0 auto;
    color: ${Ue.dim};
`,hj=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,gj=D.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${A.fonts.mono};
    font-size: 11px;
    color: ${o=>o.$empty?"#6f6257":"#ffffff"};
`,mj=D.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,vj=D.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,yj=D.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px 6px 10px;
`,xj=D.select`
    flex: 1 1 auto;
    /*Both needed: min-width:0 lets a flex item shrink below its content, width:100% stops
      it claiming its longest option's width once the menu's max-width has bounded it.*/
    min-width: 0;
    width: 100%;
    height: 22px;
    box-sizing: border-box;
    margin: 0;
    padding: 0 4px;
    font-family: inherit;
    font-size: 11px;
    color: ${Ue.text};
    /*the lobby's own input fill (.lb-input), not the number well*/
    background-color: ${Ue.btnBg};
    /*Takes the section's ink like the tickers above it - it is the widest control in the
      section, so leaving it on the chassis border was the one thing that still read as
      unpainted once the tickers went rust.*/
    border: 1px solid ${ai.crit.rail};
    border-radius: 2px;
    outline: none;
    cursor: pointer;

    &:focus { border-color: ${ai.crit.btnText}; }
    &:disabled { cursor: default; }
`;D.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${Ue.dim};
    cursor: pointer;
    user-select: none;
`;const FC=(o,r,l,d)=>{const h=[];for(const b in o||{}){if(!o.hasOwnProperty(b))continue;const S=battleDamage.PARAM_CRITICALS[b],y=parseInt((d||{})[b],10)||0;h.push({type:b,isParam:!!S,paramLabel:S?S.label:null,label:battleDamage.critLabel(b,r,y),count:parseInt(o[b],10)||0,param:y,transient:!!(l&&l[b])})}return h};class BC extends Ge.Component{constructor(r){super(r),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(r){r.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=Cp(l=>this.step(r,l.deltaY<0?1:-1))),this.wheelRefs[r]}critMap(){const r=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return r&&r.c?Object.assign({},r.c):{}}paramMap(){const r=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return r&&r.p?Object.assign({},r.p):{}}valueOf(r){return r.isParam?r.param:r.count}maxValueOf(r){if(!r.isParam)return battleDamage.critLimit(r.type);const l=battleDamage.PARAM_CRITICALS[r.type];return Math.min(battleDamage.MAX_CRIT_PARAM,l&&l.max||battleDamage.MAX_CRIT_PARAM)}setValue(r,l){const{ship:d,kind:h,reference:b,onChange:S}=this.props,y=this.critMap(),E=this.paramMap();l>0?r.isParam?(y[r.type]=1,E[r.type]=Math.min(l,this.maxValueOf(r))):y[r.type]=Math.min(l,battleDamage.critLimit(r.type)):(delete y[r.type],delete E[r.type]),battleDamage.setCriticals(d,h,b,y,E),S&&S()}step(r,l){const d=this.rowForType(r);this.setValue(d,this.valueOf(d)+l)}rowForType(r){const{ship:l}=this.props,d=battleDamage.PARAM_CRITICALS[r],h=parseInt(this.paramMap()[r],10)||0;return{type:r,isParam:!!d,paramLabel:d?d.label:null,label:battleDamage.critLabel(r,l.preBattleCritDesc,h),count:parseInt(this.critMap()[r],10)||0,param:h,transient:!!(l.preBattleCritTransient&&l.preBattleCritTransient[r])}}displayRows(r){const{ship:l,kind:d,reference:h}=this.props;return battleDamage.rememberCriticals(l,d,h,(r||[]).map(S=>S.type)).map(S=>this.rowForType(S))}addableTypes(r){const{ship:l,kind:d,reference:h}=this.props,b=battleDamage.offerableCriticals(l,d,h,this.state.showAll),S={};return r.forEach(y=>{S[y]=!0}),b.filter(y=>!S[y]).map(y=>({type:y,label:this.pickerLabel(y)})).sort((y,E)=>y.label.localeCompare(E.label))}pickerLabel(r){const l=battleDamage.PARAM_CRITICALS[r];return l?l.label:battleDamage.critLabel(r,this.props.ship.preBattleCritDesc,0)}onAdd(r){r&&this.setValue(this.rowForType(r),1)}render(){const{ship:r,rows:l,editable:d}=this.props,h=d?this.displayRows(l):l||[],b=!!(d&&battleDamage.catalogueFor(r)),S=b?this.addableTypes(h.map(y=>y.type)):[];return!h.length&&!b?null:m.jsxs(sj,{children:[m.jsx(PC,{$chrome:!0}),m.jsx(uj,{children:"Critical Effects"}),m.jsxs(Yx,{$ink:ai.crit,children:[h.map(y=>{const E=this.valueOf(y),$=this.maxValueOf(y);return m.jsxs(cj,{$empty:d&&E<=0,children:[m.jsxs(dj,{title:y.type,children:[d&&y.isParam?y.paramLabel:y.label,y.transient&&m.jsx(fj,{children:"(turn 1 only)"})]}),d?m.jsxs(hj,{children:[m.jsx(vd,{$ink:ai.crit,title:y.isParam?"Reduce":"One fewer",disabled:E<=0,onClick:()=>this.step(y.type,-1),children:"−"}),m.jsx(gj,{$empty:E<=0,ref:this.wheelRef(y.type),children:E}),m.jsx(vd,{$ink:ai.crit,title:y.isParam?"Increase":E>=$&&$===1?"This effect only applies once":"One more",disabled:E>=$,onClick:()=>this.step(y.type,1),children:"+"})]}):y.count>1&&m.jsxs(pj,{children:["(x",y.count,")"]})]},y.type)}),b&&m.jsx(yj,{children:m.jsxs(xj,{value:"",disabled:S.length===0,title:"Add a critical effect to this unit before the battle",onChange:y=>this.onAdd(y.target.value),children:[m.jsx("option",{value:"",children:S.length?"+ Add effect…":"Nothing to add"}),S.map(y=>m.jsx("option",{value:y.type,children:y.label},y.type))]})})]})]})}}const bj=D.div`
    display: flex;
    justify-content: flex-end;
    padding: 1px 8px 5px 10px;
    font-family: ${A.fonts.mono};
    font-size: 10px;
    color: ${A.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,wj=D.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${A.fonts.mono};
    font-size: 10px;
    color: ${A.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${o=>o.$spent?.6:1};
`;class Sj extends Ge.Component{constructor(r){super(r),this.wheelRef=Cp(l=>this.step(l.deltaY<0?1:-1))}step(r){const{row:l,onChange:d}=this.props,h=Math.max(0,Math.min(l.max,l.count+r));h!==l.count&&d(l.enhID,h)}onInput(r){const{row:l,onChange:d}=this.props,h=String(r.target.value).replace(/[^0-9]/g,""),b=h===""?0:parseInt(h,10);d(l.enhID,Math.max(0,Math.min(l.max,b)))}render(){const{row:r}=this.props,l=r.count>=r.max,d=r.max>1?`${r.label} - ${r.count}/${r.max} levels`+(r.count>0?`, ${r.price} pts spent`:"")+(l?"":`; next level ${r.nextPrice} pts`):`${r.label} - ${r.price||r.nextPrice} pts`;return m.jsxs(nj,{$gold:!0,title:d,children:[m.jsx(rj,{children:m.jsx(ij,{children:r.label})}),m.jsx(vd,{$ink:ai.enh,title:"Remove a level",disabled:r.count<=0,onClick:()=>this.step(-1),children:"−"}),m.jsx(zC,{ref:this.wheelRef,$ink:ai.enh,type:"text",value:r.count,onChange:h=>this.onInput(h)}),m.jsx(vd,{$ink:ai.enh,title:r.count>=r.max?"Already at the maximum":`Add a level (${r.nextPrice} pts)`,disabled:r.count>=r.max,onClick:()=>this.step(1),children:"+"}),m.jsx(wj,{$spent:l,title:l?`Fully bought - ${r.price} pts`:"Cost of the next level",children:l?`${r.price}p`:`${r.nextPrice}p`})]})}}class Cj extends Ge.Component{render(){const{rows:r,onChange:l}=this.props;if(!r||r.length===0)return null;const d=r.reduce((h,b)=>h+(b.count>0?b.price:0),0);return m.jsxs($n.Fragment,{children:[m.jsx(aj,{children:"✦ ENHANCEMENTS"}),m.jsxs(Yx,{$ink:ai.enh,children:[r.map(h=>m.jsx(Sj,{row:h,onChange:l},h.enhID)),d>0&&m.jsxs(bj,{children:["Refits: ",d," pts"]})]})]})}}const Ej=D.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    max-width: 300px;
    box-sizing: border-box;
    /*Fill and frame from ./menuControls, shared with the fighter and mine editors - see the
      note there on why the title bar is no longer the old teal, and why none of the three
      carries an element opacity any more. overflow: hidden so the section bars keep to the
      rounded corners.*/
    background: ${Ue.bg};
    border: 1px solid ${Ue.line};
    border-radius: ${Ue.radius};
    box-shadow: ${Ue.shadow};
    overflow: hidden;
`,Tj=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px 5px 10px;
    font-size: 11px;
    color: ${Ue.text};
`,kj=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,Rj=D.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${o=>o.$disabled?"not-allowed":"pointer"};
    user-select: none;
    opacity: ${o=>o.$disabled?.4:1};
    color: ${o=>o.$on?"#ff8a80":Ue.dim};
`,Dj=D.span`
    flex: 0 0 auto;
    color: ${Ue.dim};
    font-size: 10px;
`,Mj=D.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;class $j extends Ge.Component{constructor(r){super(r),this.wheelRef=Cp(l=>this.step(l.deltaY<0?1:-1))}entry(){const{ship:r,system:l}=this.props;return battleDamage.getEntry(r,battleDamage.KIND_SYSTEM,l.id)||{}}remaining(){const{system:r}=this.props,l=this.entry();return l.k?0:Math.max(0,r.maxhealth-(parseInt(l.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return this.isIndestructible()?1:0}setRemaining(r){const{ship:l,system:d}=this.props,h=d.maxhealth,b=Math.min(this.floor(),h);let S=parseInt(r,10);isNaN(S)&&(S=h),S=Math.max(b,Math.min(h,S));const y=h-S,E=S===0&&h>0;E&&this.rememberHealth(),battleDamage.setSystem(l,d.id,{d:y,k:E?1:0}),this.refresh()}rememberHealth(){const{ship:r,system:l}=this.props;this.isDestroyed()||battleDamage.rememberHealth(r,battleDamage.KIND_SYSTEM,l.id,this.remaining())}setDestroyed(r){const{ship:l,system:d}=this.props;if(r&&this.isIndestructible())return;if(r){this.rememberHealth(),battleDamage.setSystem(l,d.id,{d:d.maxhealth,k:1}),this.refresh();return}const h=battleDamage.healthMemory(l,battleDamage.KIND_SYSTEM,d.id),b=h>0?Math.min(d.maxhealth,h):d.maxhealth;battleDamage.setSystem(l,d.id,{d:d.maxhealth-b,k:0}),this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r);let l=[];if(window.systemEnhancements){const d=parseFloat(r.pointCostSysEnh)||0;l=systemEnhancements.dropDestroyed(r),l.length&&this.settleRefitCost(r,d)}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate(),l.length&&window.confirm&&typeof confirm.warning=="function"&&confirm.warning(systemEnhancements.describeRemoved(l))}settleRefitCost(r,l){const d=parseFloat(r.pointCostSysEnh)||0;r.pointCost=(parseFloat(r.pointCost)||0)-(l-d),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet()}setEnhancement(r,l){const{ship:d,system:h}=this.props;if(!window.systemEnhancements)return;const b=parseFloat(d.pointCostSysEnh)||0,S=systemEnhancements.taken(d,h.id,r);if(l===S)return;if(systemEnhancements.set(d,h.id,r,l),this.settleRefitCost(d,b),!(!window.gamedata||typeof gamedata.canAffordRefit!="function"||gamedata.canAffordRefit(d))){const E=parseFloat(d.pointCostSysEnh)||0;systemEnhancements.set(d,h.id,r,S),this.settleRefitCost(d,E),systemEnhancements.apply(d),this.forceUpdate(),window.confirm&&typeof confirm.error=="function"&&confirm.error("You cannot afford that enhancement!",function(){});return}systemEnhancements.apply(d),this.refresh()}step(r){this.setRemaining(this.remaining()+r)}onInput(r){const l=String(r.target.value).replace(/[^0-9]/g,"");this.setRemaining(l===""?0:parseInt(l,10))}render(){const{ship:r,system:l}=this.props;if(!l||!(l.maxhealth>0))return null;const d=this.remaining(),h=this.isDestroyed(),b=this.isIndestructible(),S=this.entry(),y=FC(S.c,r.preBattleCritDesc,r.preBattleCritTransient,S.p),E=window.systemEnhancements&&!h?systemEnhancements.menuRowsFor(r,l):[];return m.jsxs(Ej,{onClick:$=>$.stopPropagation(),children:[m.jsx(Cj,{rows:E,onChange:($,M)=>this.setEnhancement($,M)}),E.length>0&&m.jsx(PC,{}),m.jsx(oj,{children:"Damage"}),m.jsx(Yx,{$ink:ai.damage,children:m.jsxs(Tj,{children:[m.jsxs(kj,{title:`${l.displayName||l.name} (system id ${l.id})`,children:[m.jsx(Mj,{children:l.displayName||l.name}),m.jsxs(Dj,{children:["#",l.id]})]}),m.jsx(vd,{$ink:ai.damage,title:b&&d<=1?"A reactor cannot be destroyed before the battle":"More damage",disabled:h||d<=this.floor(),onClick:()=>this.step(-1),children:"−"}),m.jsx(zC,{ref:this.wheelRef,$ink:ai.damage,type:"text",$destroyed:h,disabled:h,value:h?0:d,onChange:$=>this.onInput($)}),m.jsx(vd,{$ink:ai.damage,title:"Repair",disabled:h||d>=l.maxhealth,onClick:()=>this.step(1),children:"+"}),m.jsxs(Rj,{$on:h,$disabled:b,title:b?"A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship":"Mark this system destroyed before the battle starts",children:[m.jsx(mj,{type:"checkbox",checked:h,disabled:b,onChange:$=>this.setDestroyed($.target.checked)}),m.jsx(vj,{children:"Destroy"})]})]})}),m.jsx(BC,{ship:r,kind:battleDamage.KIND_SYSTEM,reference:l.id,rows:y,editable:!0,onChange:()=>this.refresh()})]})}}const IC=D.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,UC=D.div`
    display: flex;
    flex-wrap: wrap;
`,_o=D.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${o=>o.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    mix-blend-mode: ${o=>o.$blend||"normal"};
    ${pa}

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`,HC=D.span`
    color: #ffd27a;
    font-size: 16px;
    line-height: 1;
    text-shadow: black 0 0 3px, black 0 0 3px;
    pointer-events: none;
`;class Oj extends Ge.Component{constructor(r){super(r)}online(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;shipManager.power.onOnlineClicked(l,d),webglScene.customEvent("CloseSystemInfo")}offline(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;om(l,d)&&(shipManager.power.onOfflineClicked(l,d),webglScene.customEvent("CloseSystemInfo"))}allOnline(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;shipManager.power.onlineAll(l,d),webglScene.customEvent("CloseSystemInfo")}allOffline(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;om(l,d)&&(shipManager.power.offlineAll(l,d),webglScene.customEvent("CloseSystemInfo"))}overload(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;shipManager.power.onOverloadClicked(l,d),webglScene.customEvent("CloseSystemInfo")}stopOverload(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;shipManager.power.onStopOverloadClicked(l,d),webglScene.customEvent("CloseSystemInfo")}boost(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;shipManager.power.clickPlus(l,d)}deboost(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;shipManager.power.clickMinus(l,d)}addShots(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;s0(l,d)&&weaponManager.changeShots(l,d,1)}reduceShots(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;u0(l,d)&&weaponManager.changeShots(l,d,-1)}removeFireOrderMulti(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;lm(l,d)&&weaponManager.removeFiringOrderMulti(l,d)}removeFireOrder(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;Tp(l,d)&&(weaponManager.removeFiringOrder(l,d),webglScene.customEvent("CloseSystemInfo"))}removeFireOrderAll(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;Tp(l,d)&&(weaponManager.removeFiringOrderAll(l,d),webglScene.customEvent("CloseSystemInfo"))}allChangeFiringMode(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;if(yd(l,d)){weaponManager.onModeClicked(l,d);var h=d.firingMode,b=[];l.flight?b=l.systems.map(j=>j.systems).reduce((j,H)=>j.concat(H),[]).filter(j=>j.weapon):b=l.systems.filter(j=>j.weapon);for(var S=weaponManager.stripPairingSuffix(d.displayName),y=new Array,E=0;E<b.length;E++)S===weaponManager.stripPairingSuffix(b[E].displayName)&&d.weapon&&y.push(b[E]);for(var E=0;E<y.length;E++){var $=y[E];if($.firingMode!=h&&yd(l,$)){for(var M=$.firingMode,N=0;$.firingMode!=h&&N<2;)weaponManager.onModeClicked(l,$),$.firingMode==1&&N++;if($.firingMode!=h)for(;$.firingMode!=M;)weaponManager.onModeClicked(l,$)}}}}changeFiringMode(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;yd(l,d)&&weaponManager.onModeClicked(l,d)}selectAllWeapons(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;weaponManager.selectAllWeapons(l,d,"forceSelect"),webglScene.customEvent("CloseSystemInfo")}deselectAllWeapons(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;weaponManager.selectAllWeapons(l,d,"forceDeselect"),webglScene.customEvent("CloseSystemInfo")}declareSelfIntercept(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;if(sm(l,d)){if(weaponManager.onDeclareSelfInterceptSingle(l,d),d.canSplitShots)var h=d.checkFinished();h&&webglScene.customEvent("CloseSystemInfo")}}declareSelfInterceptAll(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(l,d),d.canSplitShots)var h=d.checkFinished();h&&webglScene.customEvent("CloseSystemInfo")}remSelfIntercept(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;um(l,d)&&weaponManager.removeSelfInterceptSingle(l,d)}declareMeteorDefence(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;cm(l,d)&&(weaponManager.onDeclareMeteorDefence(l,d),webglScene.customEvent("CloseSystemInfo"))}declareMeteorDefenceAll(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;weaponManager.onDeclareMeteorDefenceAll(l,d),webglScene.customEvent("CloseSystemInfo")}remMeteorDefence(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;dm(l,d)&&(weaponManager.removeMeteorDefence(l,d),webglScene.customEvent("CloseSystemInfo"))}remMeteorDefenceAll(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;weaponManager.removeMeteorDefenceAll(l,d),webglScene.customEvent("CloseSystemInfo")}nextCurrClass(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;d.nextCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:l,system:d})}prevCurrClass(r){r.stopPropagation(),r.preventDefault();const{ship:l,system:d}=this.props;d.prevCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:l,system:d})}render(){const{ship:r,selectedShip:l,system:d}=this.props;return l0(r,d)?gamedata.gamephase===-2?m.jsx(IC,{children:m.jsx($j,{ship:r,system:d})}):m.jsxs(IC,{children:[eE(r,d)&&m.jsx(TA,{ship:r,system:d}),m.jsxs(UC,{children:[s0(r,d)&&m.jsx(_o,{title:"More shots",onClick:this.addShots.bind(this),img:"./img/plussquare.png"}),u0(r,d)&&m.jsx(_o,{title:"Less shots",onClick:this.reduceShots.bind(this),img:"./img/minussquare.png"})]}),JC(r,d)&&m.jsxs(oO,{ship:r,system:d,showModes:!!yd(r,d),children:[sm(r,d)&&m.jsx(_o,{title:"Allow interception (RMB = All systems selected)",onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:"./img/addSelfIntercept.png"}),um(r,d)&&m.jsx(_o,{title:"Remove an intercept order",onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:"./img/remSelfIntercept.png"}),cm(r,d)&&m.jsx(_o,{title:"Meteor Defence: commit this weapon to defend against meteors this turn - it cannot fire or intercept (RMB = all similar weapons)",onClick:this.declareMeteorDefence.bind(this),onContextMenu:this.declareMeteorDefenceAll.bind(this),img:"./img/selfIntercept.png",children:m.jsx(HC,{children:"☄"})}),dm(r,d)&&m.jsx(_o,{title:"Remove Meteor Defence (RMB = all similar weapons)",onClick:this.remMeteorDefence.bind(this),onContextMenu:this.remMeteorDefenceAll.bind(this),img:"./img/remSelfIntercept.png",children:m.jsx(HC,{children:"☄"})}),lm(r,d)&&m.jsx(_o,{title:"Remove last fire order",onClick:this.removeFireOrderMulti.bind(this),img:"./img/unfiringSmall.png"}),Tp(r,d)&&m.jsx(_o,{title:"Remove all fire orders (RMB = All weapons selected)",onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:"./img/firing.png"})]}),m.jsxs(UC,{children:[Gx(r,d)&&m.jsx(_o,{title:"Select all weapons of this type",onClick:this.selectAllWeapons.bind(this),img:"./img/selectAllWeapons.png",$blend:"screen"}),Gx(r,d)&&m.jsx(_o,{title:"Deselect all weapons of this type",onClick:this.deselectAllWeapons.bind(this),img:"./img/deselectAllWeapons.png",$blend:"screen"})]}),d0(r,d)&&m.jsx(wA,{ship:r,system:d}),Kx(r,d)&&m.jsx(BO,{ship:r,system:d}),Zx(r,d)&&m.jsx(QO,{system:d,ship:r}),e0(r,d)&&m.jsx(rA,{system:d,ship:r}),Xx(r,d)&&m.jsx(LA,{system:d,ship:r}),Jx(r,d)&&m.jsx(HA,{system:d,ship:r}),Qx(r,d)&&m.jsx(YA,{system:d,ship:r}),qx(r,d)&&m.jsx(ej,{system:d,ship:r}),(t0(r,d)||n0(r,d))&&m.jsx(wC,{system:d,ship:r}),(r0(r,d)||i0(r,d))&&m.jsx(wC,{system:d,ship:r}),am(r,d)&&m.jsx(xO,{ship:r,system:d,readOnly:!Aj(r,d)}),a0(r,d)&&m.jsx(OO,{ship:r,system:d,readOnly:!jj(r,d)}),"   ",fm(r,d)&&m.jsx(fA,{ship:r,system:d}),ZC(r,d)&&m.jsx(mA,{ship:r,system:d})]}):null}}const Gx=(o,r)=>!(!window.matchMedia("(pointer: coarse)").matches||!r.weapon||Ep(o,r)||gamedata.gamephase!=3&&!r.ballistic&&!r.preFires||gamedata.gamephase!=1&&r.ballistic||gamedata.gamephase!=5&&r.preFires),Kx=(o,r)=>gamedata.gamephase===1&&r.name=="adaptiveArmorController",Qx=(o,r)=>r.name==="GraviticAugmenter"&&gamedata.isMyShip(o)&&!r.stowed&&!shipManager.power.isOffline(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(o,r)||gamedata.gamephase===5),qx=(o,r)=>r.name==="MinorThoughtPulsar"&&gamedata.gamephase===3&&gamedata.isMyShip(o)&&!r.stowed&&!shipManager.systems.isDestroyed(o,r)&&!shipManager.power.isOffline(o,r),Xx=(o,r)=>gamedata.gamephase===-1&&o.mine&&(o.spawned==-1&&gamedata.turn==1||o.spawned==gamedata.turn-1)&&(r.name=="CaptorMine"||r.name=="MineControllerDEW"),Jx=(o,r)=>gamedata.gamephase===-1&&o.mine&&(o.spawned==-1&&gamedata.turn==1||o.spawned==gamedata.turn-1)&&r.name=="ProximityMine",Zx=(o,r)=>gamedata.gamephase===1&&r.name=="hyachComputer",e0=(o,r)=>r.name==="hyachSpecialists",t0=(o,r)=>gamedata.gamephase===1&&r.name==="ThirdspaceShield",n0=(o,r)=>gamedata.gamephase===1&&r.name==="ThirdspaceShieldGenerator",r0=(o,r)=>gamedata.gamephase===1&&r.name==="ThoughtShield",i0=(o,r)=>gamedata.gamephase===1&&r.name==="ThoughtShieldGenerator",am=(o,r)=>gamedata.isMyShip(o)&&r.name=="SelfRepair",Aj=(o,r)=>am(o,r)&&gamedata.gamephase===1,a0=(o,r)=>gamedata.isMyShip(o)&&(r.name=="StructureSelfRepair"||r.name=="CoopStructureSelfRepair"),jj=(o,r)=>a0(o,r)&&gamedata.gamephase===1,VC=()=>typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted(),Pu=(o,r)=>gamedata.gamephase===-2&&!VC()&&o&&o.userid!=0&&!o.flight&&!o.mine&&!_j(r),WC=(o,r)=>Pu(o,r)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(o,r)&&systemEnhancements.offersFor(o,r).length>0,o0=o=>gamedata.gamephase===-2&&!VC()&&o&&o.userid!=0&&!!o.mine&&battleDamage.mineMaxHealth(o)>1,_j=o=>!o||!(o.maxhealth>0)||o.isTargetable===!1||!!o.hideInShipWindow||Array.isArray(o.systems),l0=(o,r)=>gamedata.gamephase===-2?Pu(o,r)||WC(o,r):om(o,r)||YC(o,r)||GC(o,r)||KC(o,r)||QC(o,r)||qC(o,r)||s0(o,r)||u0(o,r)||lm(o,r)||Tp(o,r)||yd(o,r)||sm(o,r)||um(o,r)||cm(o,r)||dm(o,r)||Kx(o,r)||Zx(o,r)||e0(o,r)||t0(o,r)||r0(o,r)||n0(o,r)||i0(o,r)||am(o,r)||Lj(o,r)||zj(o,r)||fm(o,r)||ZC(o,r)||d0(o,r)||Gx(o,r)||Xx(o,r)||Jx(o,r)||Qx(o,r)||qx(o,r),om=(o,r)=>gamedata.gamephase===1&&(r.canOffLine||r.powerReq>0)&&!r.powerLocked&&!shipManager.power.isOffline(o,r)&&!shipManager.power.getBoost(r)&&!weaponManager.hasFiringOrder(o,r),YC=(o,r)=>gamedata.gamephase===1&&shipManager.power.isOffline(o,r)&&!shipManager.power.isForcedOffline(o,r)&&!shipManager.power.isVortexLockedOffline(o,r),GC=(o,r)=>gamedata.gamephase===1&&!shipManager.power.isOffline(o,r)&&r.weapon&&r.overloadable&&!shipManager.power.isOverloading(o,r),KC=(o,r)=>gamedata.gamephase===1&&r.weapon&&r.overloadable&&shipManager.power.isOverloading(o,r)&&(r.overloadshots>=r.extraoverloadshots||r.overloadshots==0),QC=(o,r)=>r.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(o,r)&&(!r.isScanner()||r.id==shipManager.power.getHighestSensorsId(o))&&r.name!=="ThirdspaceShieldGenerator"&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor",qC=(o,r)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(r)&&r.name!=="ThirdspaceShieldGenerator"&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor",XC=(o,r)=>{const l=weaponManager.getFiringOrder(o,r);return l&&l.type!=="intercept"&&l.type!=="selfIntercept"?l:null},s0=(o,r)=>{if(Ep(o,r)||!r.weapon||!r.canChangeShots||!weaponManager.hasFiringOrder(o,r))return!1;const l=XC(o,r);return!!l&&l.shots<r.maxVariableShots},u0=(o,r)=>{if(Ep(o,r)||!r.weapon||!r.canChangeShots||!weaponManager.hasFiringOrder(o,r))return!1;const l=XC(o,r);return!!l&&l.shots>1},Ep=(o,r)=>r.name==="jumpEngine"&&typeof r.getHeldVortex=="function"&&!!r.getHeldVortex(),lm=(o,r)=>r.weapon&&weaponManager.hasOrderForMode(r)&&r.canSplitShots&&!Ep(o,r),Tp=(o,r)=>r.weapon&&weaponManager.hasFiringOrder(o,r)&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked())&&!Ep(o,r),c0=o=>!!(window.shipManager&&shipManager.isDockingRider(o)),yd=(o,r)=>r.weapon&&!o.mine&&!r.stowed&&!r.hideFiringModeSelector&&!c0(o)&&r.name!=="GraviticAugmenter"&&r.name!=="MinorThoughtPulsar"&&(gamedata.gamephase===1&&r.ballistic||gamedata.gamephase===5&&r.preFires||gamedata.gamephase===3&&!r.ballistic&&!r.preFires)&&(!weaponManager.hasFiringOrder(o,r)||r.multiModeSplit)&&!weaponManager.hasMeteorDefence(o,r)&&Object.keys(r.firingModes).length>1,sm=(o,r)=>r.weapon&&!c0(o)&&weaponManager.canSelfInterceptSingle(o,r),um=(o,r)=>r.weapon&&r.canSplitShots&&!c0(o)&&weaponManager.canRemInterceptSingle(o,r),JC=(o,r)=>yd(o,r)||sm(o,r)||um(o,r)||cm(o,r)||dm(o,r)||lm(o,r)||Tp(o,r),cm=(o,r)=>r.weapon&&weaponManager.canDeclareMeteorDefence(o,r),dm=(o,r)=>r.weapon&&weaponManager.canRemoveMeteorDefence(o,r),Lj=(o,r)=>r.canActivate&&typeof r.canActivate=="function"&&r.canActivate()&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor"&&r.name!=="GraviticAugmenter"&&r.name!=="jumpEngine",zj=(o,r)=>r.canDeactivate&&typeof r.canDeactivate=="function"&&r.canDeactivate()&&r.name!=="powerCapacitor"&&r.name!=="PowerCapacitor"&&r.name!=="GraviticAugmenter"&&r.name!=="jumpEngine",ZC=(o,r)=>r.name==="jumpEngine"&&typeof r.canMaintainVortex=="function"&&(r.canMaintainVortex()||r.canDeactivate()||typeof r.getAbductionOrder=="function"&&!!r.getAbductionOrder()),fm=(o,r)=>r.name==="powerCapacitor"||r.name==="PowerCapacitor",eE=(o,r)=>om(o,r)||YC(o,r)||GC(o,r)||KC(o,r)||r.boostable&&(QC(o,r)||qC(o,r)),d0=(o,r)=>fm(o,r)||r.name==="GraviticAugmenter"||r.name==="jumpEngine"?!1:!!(r.canActivate&&typeof r.canActivate=="function"&&r.canActivate()||r.canDeactivate&&typeof r.canDeactivate=="function"&&r.canDeactivate()),Nj=(o,r)=>gamedata.gamephase===-2?Pu(o,r)||WC(o,r):Kx(o,r)||Zx(o,r)||e0(o,r)||Xx(o,r)||Jx(o,r)||Qx(o,r)||qx(o,r)||t0(o,r)||n0(o,r)||r0(o,r)||i0(o,r)||am(o,r)||a0(o,r)||fm(o,r)||eE(o,r)||d0(o,r)||JC(o,r),tE=D.div`
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
`,nE=D.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,Pj=D.div`
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
    color: ${A.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,Fj=D.div`
    position: absolute;
    top: 0px;
    right: 1px;
    z-index: 1;
    pointer-events: none;
    font-size: 11px;
    line-height: 11px;
    color: #ffd27a;
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,rE=D.div`
    position: relative;
    box-sizing: border-box;
    width: 32px;
    height: 32px;
    margin: ${o=>o.$scs?"3px 0":"2px"};
   border: ${o=>o.$firing&&o.$calledShot?"2px solid #ff3366":o.$meteorDefence?"1px solid #d9a441":o.$firing&&o.$intercepting?"1px solid #52b352":o.$firing?"1px solid #eb5c15":o.$orderPending?"2px solid #00e5ff":o.$highlight==="Yellow"?"1px solid #e1b000":o.$highlight==="Orange"?"2px solid #ff6d3c":o.$highlight==="Red"?"2px solid #ff0000":"1px solid #496791"};
     background-color:  ${o=>o.$selected?"#4e6c91":o.$meteorDefence?"#5c4318":o.$firing&&o.$intercepting?"#2f7a3a":o.$firing?"#e06f01":o.$off?"#852d2d":o.$boosted?"#cca300":o.$loading&&o.$loadedAlternate?"#CD9E9E":"rgba(0, 0, 0, 0.7)"};
    box-shadow: ${o=>o.$selected?"0px 0px 15px #0099ff":o.$firing&&o.$calledShot?"0px 0px 12px #ff3366":o.$meteorDefence?"0px 0px 12px #d9a441":o.$firing&&o.$intercepting?"0px 0px 15px #52b352":o.$firing?"box-shadow: 0px 0px 15px #eb5c15":o.$orderPending?"0px 0px 12px #00e5ff":"none"};
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
    
    ${nE} {
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
`;class f0 extends Ge.Component{constructor(r){super(r),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(r){if(r.stopPropagation(),r.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:l,ship:d}=this.props;l=shipManager.systems.initializeSystem(l);const h=Pu(d,l);if((gamedata.waiting||gamedata.replay)&&!h)return;const b=!!d.removed&&!shipManager.isDestroyedByDamage(d);if(!(!h&&!b&&(shipManager.isDestroyed(d)||shipManager.isDestroyed(d,l)&&!l.clickableWhenDestroyed))){if(b&&!h){gamedata.isMyShip(d)&&window.uiEvents.relay("SystemClicked",{ship:d,system:l,element:r.currentTarget,showMenu:!0});return}if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(d)){var S=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(S&&S.ship.id!=d.id&&!weaponManager.isSelectedWeapon(l)){window.uiEvents.relay("SystemTargeted",{ship:d,system:l});return}}var y=l.weapon&&typeof l.isSpentLocked=="function"&&l.isSpentLocked(),E=typeof l.canSelectForAbduction=="function"&&l.canSelectForAbduction(d);if(!y&&(l.weapon&&gamedata.gamephase===3&&!l.ballistic&&!l.preFires||gamedata.gamephase===1&&l.ballistic||gamedata.gamephase===5&&l.preFires||weaponManager.canManuallyInterceptWith(d,l)||E)&&!shipManager.isAdrift(d)&&gamedata.isMyShip(d)){if(l.hasSpecialTargeting&&typeof l.reopenSpecialTargeting=="function"&&weaponManager.hasFiringOrder(d,l)&&l.reopenSpecialTargeting(d))return;var $=weaponManager.hasFiringOrder(d,l),M=$&&$!=="self"&&!l.canSplitShots&&!l.hasSpecialTargeting;weaponManager.isSelectedWeapon(l)?weaponManager.unSelectWeapon(d,l):M||weaponManager.selectWeapon(d,l)}if(gamedata.isMyShip(d)&&(l.name==="hangar"||l.name==="catapult"||l.name==="fighterRail")){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog=="function"&&window.DeploymentDock.shipHasOpenableDockDialog(d)&&window.confirm&&typeof window.confirm.hangarDeployDock=="function"){window.confirm.hangarDeployDock(d);return}if(gamedata.gamephase===3&&!l.isShadowHangar&&!shipManager.movement.isRolling(d)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(d)!=="no")&&window.confirm&&typeof window.confirm.hangarLaunch=="function"){window.confirm.hangarLaunch(d);return}}if(gamedata.isMyShip(d)&&(l.name==="dockingCollar"||l.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(d)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(d)!=="no")&&typeof window.lcvRailLaunchable=="function"&&window.lcvRailLaunchable(d,l)&&window.confirm&&typeof window.confirm.lcvLaunch=="function"){window.confirm.lcvLaunch(d);return}gamedata.isMyShip(d)?window.uiEvents.relay("SystemClicked",{ship:d,system:l,element:r.currentTarget,showMenu:!0}):window.uiEvents.relay("SystemTargeted",{ship:d,system:l})}}onSystemMouseOver(r){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;r.stopPropagation(),r.preventDefault();let{system:l,ship:d}=this.props;l=shipManager.systems.initializeSystem(l),window.uiEvents.relay("SystemMouseOver",{ship:d,system:l,element:r.currentTarget,showInfo:!0})}onSystemMouseOut(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),r.preventDefault(),window.uiEvents.relay("SystemMouseOut"))}onTouchStart(r){r.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const l=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:h,ship:b}=this.props;h=shipManager.systems.initializeSystem(h),window.uiEvents.relay("SystemMouseOver",{ship:b,system:h,element:l,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(r){if(r.stopPropagation(),!this.longPressTimer)return;const l=r.touches[0],d=l.clientX-this.touchStartX,h=l.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(h)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(r){r.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onTouchEnd(r){r.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(r){if(r.stopPropagation(),r.preventDefault(),window.matchMedia("(pointer: coarse)").matches)return;let{system:l,ship:d}=this.props;l=shipManager.systems.initializeSystem(l),l.weapon&&weaponManager.selectAllWeapons(d,l)}render(){let{system:r,ship:l,scs:d,fighter:h,destroyed:b,mirror:S}=this.props;return r=shipManager.systems.initializeSystem(r),r=shipManager.systems.initializeSystem(r),(p0(l,r)||b)&&!r.clickableWhenDestroyed&&!Pu(l,r)?m.jsxs(rE,{$background:lE(r),$destroyed:!0,$mirror:S,children:[iE(l,r),m.jsx(tE,{$health:"0"})]}):m.jsxs(rE,{$scs:d,$highlight:Zj(l,r),$destroyed:p0(l,r)||b,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:lE(r),$mirror:S,$offline:Yj(l,r),$loading:Vj(r),$loadedAlternate:Wj(r),$selected:e_(r),$firing:Bj(l,r),$intercepting:Uj(l,r),$meteorDefence:aE(l,r),$calledShot:Hj(l,r),$boosted:Kj(l,r),$off:Gj(r),$docked:oE(r),$orderPending:qj(r),children:[iE(l,r),aE(l,r)&&m.jsx(Fj,{title:"Committed to meteor defence this turn",children:"☄"}),m.jsx(nE,{children:t_(l,r)}),(!h||sE(r))&&m.jsx(tE,{$scs:d,$health:p0(l,r)||b?0:Xj(l,r),$criticals:sE(r),$criticalsBenign:Jj(r),$docked:Qj(r)})]})}}const iE=(o,r)=>!window.systemEnhancements||!o||!r||!systemEnhancements.hasAny(o,r.id)?null:m.jsx(Pj,{title:"Carries a system enhancement",children:"✦"}),Bj=(o,r)=>(weaponManager.hasFiringOrder(o,r)||Ij(r))&&!(typeof r.isSpentLocked=="function"&&r.isSpentLocked()),Ij=o=>gamedata.gamephase===1&&typeof o.getAbductionOrder=="function"&&!!o.getAbductionOrder(),Uj=(o,r)=>weaponManager.isInterceptOnly(o,r),aE=(o,r)=>!!r.weapon&&weaponManager.hasMeteorDefence(o,r),Hj=(o,r)=>!r.weapon||!weaponManager.hasFiringOrder(o,r)?!1:weaponManager.getCalledShotInfo(o,r)!==null,Vj=o=>o.weapon&&(!weaponManager.isLoaded(o)||typeof o.isSpentLocked=="function"&&o.isSpentLocked()),Wj=o=>o.weapon&&weaponManager.isLoadedAlternate(o),Yj=(o,r)=>shipManager.power.isOffline(o,r),Gj=o=>o.activeMeansOff&&o.active,Kj=(o,r)=>shipManager.power.isBoosted(o,r)||r.active&&!r.activeMeansOff&&!r.suppressActiveBoost,oE=o=>!!(o.showDockedVisual&&o.activeEffective||o.stowed&&o.stowedArcStart==null||o.dockedWithOrbital),Qj=o=>oE(o)||!!o.stowed,qj=o=>!!(o.showDockedVisual&&typeof o.hasPendingDockingOrder=="function"&&o.hasPendingDockingOrder()),Xj=(o,r)=>(r.name==="ThirdspaceShield"||r.name==="ThoughtShield")&&r.baseRating?Math.min(100,r.currentHealth/r.baseRating*100):(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,p0=(o,r)=>shipManager.systems.isDestroyed(o,r),lE=o=>o.name=="thruster"&&!o.iconPath?window.AssetManager.getSmartImagePath("./img/systemicons/thruster"+o.direction+".png"):o.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${o.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${o.name}.png`),sE=o=>shipManager.criticals.hasCriticalsIcon(o),Jj=o=>shipManager.criticals.hasOnlyCritical(o,"HangarOperations",!0)||shipManager.criticals.hasOnlyCritical(o,"LCVLaunchedThisTurn",!0),Zj=(o,r)=>shipManager.systems.hasBorderHighlight(o,r),e_=o=>weaponManager.isSelectedWeapon(o),t_=(o,r)=>{if(r.outputDisplay!==void 0&&r.outputDisplay!==null&&r.outputDisplay!="")return r.outputDisplay;if(r.weapon){if(r.stowed&&r.stowedArcStart==null)return"-";if(typeof r.isSpentLocked=="function"&&r.isSpentLocked())return"✓";if(typeof r.getVortexIconLoad=="function"){const h=r.getVortexIconLoad();if(h!=null)return h}const d=weaponManager.hasFiringOrder(o,r);if(d&&r.canChangeShots)return weaponManager.getFiringOrder(o,r).shots+"/"+r.shots;if(d){var l=weaponManager.getCalledShotInfo(o,r);if(l)return"⊕"}else if(!d){let h=weaponManager.getWeaponCurrentLoading(r),b=r.loadingtime;r.normalload>0&&(b=r.normalload),h>b&&(h=b);let S="";return r.overloadturns>0&&shipManager.power.isOverloading(o,r)&&(S="("+r.overloadturns+")"),r.overloadshots>0?"S"+r.overloadshots:h+S+"/"+b}}else{if(r.outputType==="thrust")return shipManager.movement.getRemainingEngineThrust(o);if(r.outputType==="power"){let d=shipManager.power.getReactorPower(o,r);return gamedata.gamephase>1&&d<0?0:d}else return shipManager.systems.getOutput(o,r)}},n_=D.div`
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
`;class r_ extends Ge.Component{constructor(r){super(r)}getWeapons(r,l){return r.flight?r.systems.map(d=>d.systems).reduce((d,h)=>d.concat(h),[]).filter(d=>d.weapon):r.systems.filter(d=>d.weapon||d.outputType==="thrust"||d.outputType==="EW"||d.outputType==="power"||d.outputType==="settings")}render(){const{ship:r,gamePhase:l}=this.props;if(!r)return null;const d=this.getWeapons(r,l);return m.jsx(n_,{children:d.map((h,b)=>m.jsx(f0,{fighter:r.flight,system:h,ship:r},`system-${b}`))})}}const uE=o=>{if(!o.hitChart)return[];const r=["Primary","Front","Aft","Port","Starboard"];let l=5;o.base&&!o.smallBase?(r[1]="Sections",l=2):o.SixSidedShip&&(r[31]="Port Front",r[32]="Port Aft",r[41]="Starboard Front",r[42]="Starboard Aft",l=43);const d=[];for(let h=0;h<l;h++){if(o.hitChart[h]===void 0)continue;const b=[];let S=0;for(const y in o.hitChart[h]){const E=Math.floor((y-S)/20*100);S=y;let $=o.hitChart[h][y];const M=$.indexOf(":");M>0&&($=$.substring(M+1)),b.push({name:$,chance:E})}d.push({location:h,name:r[h],entries:b})}return d},i_=D.div`
    ${o=>o.$tightBottom?"& > *:last-child { display: none; }":""}
    ${o=>o.$compactText?`
    ${Ut} {
        font-size: 10px;
        line-height: 1.4;
        color: ${A.colors.textAccent};
    }
    ${Yn} {
        font-size: 10px;
        font-style: normal;
        color: ${A.colors.text};
    }`:""}
`;class cE extends Ge.Component{render(){const{ship:r,hideHitChart:l,tightBottom:d,compactText:h}=this.props,b=!!r.mine||window.gamedata&&typeof gamedata.isTerrain=="function"&&gamedata.isTerrain(r.shipSizeClass,r.userid),S=!!r.flight||b;var y=new Array,E=new Array,$=new Array;r.notes&&(y=r.notes.split("<br>")),r.hitChart&&!l&&uE(r).forEach(function(K){E[K.name]=K.entries.map(function(de){return de.name+" "+de.chance+"%"}).join(", ")}),r.enhancementTooltip!=""&&($=r.enhancementTooltip.split("<br>"));let M={};if(!r.flight&&r.hasAttached&&Object.keys(r.hasAttached).length>0){const K={1:"Forward",2:"Aft",3:"Port",31:"Port-Forward",32:"Port-Aft",4:"Starboard",41:"Starboard-Forward",42:"Starboard-Aft"};for(let de in r.hasAttached){let Ee=r.hasAttached[de],pe=K[Ee]||"Unknown";M[pe]||(M[pe]=0),M[pe]++}}let N=r.offensivebonus;r.flight&&gamedata.areMinesPresent&&(r.minesweeper?N-=window.ew.getDetectMEW(r):N-=window.ew.getDetectMEW(r)*2);var j=0,H=!0;if(r.mine){var L=shipManager.systems.getSystemByName(r,"mineStealth");L&&!L.isMineRevealed(r)&&(H=!1,E=new Array,y=["No details known, scan with OEW to identify."],$=new Array)}return m.jsxs(i_,{$tightBottom:d,$compactText:h,children:[r.flight&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Offensive bonus: "}),N*5]},j++),r.flight&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Armor (F/S/A): "}),shipManager.systems.getFlightArmour(r)]},j++),r.flight&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Profile - Front/Side: "}),r.forwardDefense*5,"/",r.sideDefense*5]},j++),r.flight&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Initiative: "}),r.iniativebonus]},j++),r.flight&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Thrust: "}),r.freethrust]},j++),r.flight&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Turn Cost: "}),r.turncost]},j++),r.flight&&H&&r.turndelay&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Turn Delay: "}),r.turndelaycost]},j++),r.flight&&H&&m.jsx(Ut,{children:" "},j++),Object.keys(y).length>0&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"NOTES:"})," "]},j++),Object.keys(y).length>0&&Object.keys(y).map(K=>m.jsx(Ut,{children:y[K]},j++)),Object.keys(y).length>0&&m.jsx(Ut,{children:" "},j++),Object.keys(E).length>0&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"HIT CHART:"})," "]},j++),Object.keys(E).length>0&&Object.keys(E).map(K=>m.jsxs(Ut,{children:[m.jsxs(Yn,{children:[K,": "]}),E[K]]},j++)),Object.keys(E).length>0&&m.jsx(Ut,{children:" "},j++),Object.keys(M).length>0&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"UNITS ATTACHED:"})," "]},j++),Object.keys(M).length>0&&Object.keys(M).map(K=>m.jsxs(Ut,{children:[m.jsxs(Yn,{children:[K,": "]}),M[K]]},j++)),Object.keys(M).length>0&&m.jsx(Ut,{children:" "},j++),S&&r.enhancementTooltip!=""&&H&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"ENHANCEMENTS:"})," "]},j++),S&&r.enhancementTooltip!=""&&H&&Object.keys($).map(K=>m.jsx(Ut,{children:$[K]},j++)),S&&r.enhancementTooltip!=""&&H&&m.jsx(Ut,{children:" "},j++)]})}}const xd=D(J1)`
    /*font-size: 12px;*/
	font-size: 13px;
`,dE=D(X1)`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.position).reduce((r,l)=>r+`
`+l+":"+o.position[l]+"px;","")}
    width: ${o=>o.ship?"320px":"220px"};
    text-align: left;
    opacity:0.8;
`,fE=D.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,a_={MissileLost:"A missile was lost to damage"},pE={DamageReductionReduced:o=>`Damage reduction reduced by ${o}`},Ut=D(Z1)`
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
`,o_=D.span`
    color: #C6E2FF;
`;class l_ extends Ge.Component{render(){const{ship:r,selectedShip:l,system:d,boundingBox:h}=this.props;if(d instanceof Ship||d===r){var b=r.shipClass,S=r.name;if(d.flight&&(b=d.systems[1].displayName),r.mine){var y=shipManager.systems.getSystemByName(r,"mineStealth");y&&!y.isMineRevealed(r)&&(b="Mine",S="Mine")}return m.jsxs(dE,{ship:!0,position:gE(h),children:[m.jsxs(xd,{children:[m.jsx(o_,{children:S})," - ",b]}),m.jsx(cE,{ship:r})]})}var E=new Array;d.data.Special&&d.data.Special!=""&&(E=d.data.Special.split("<br>"));var $="Special",M=0;let N=r.offensivebonus;r.flight&&gamedata.areMinesPresent&&(r.minesweeper?N-=window.ew.getDetectMEW(r):N-=window.ew.getDetectMEW(r)*2);var j=d.displayName,H=d.firingModes?d.firingModes[d.firingMode]:null,L=null;d.name==="ShadowFighterBomb"&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool=="function"&&(L=weaponManager.shadowFighterBombPool(r,d,!0));var K=null;if(d.outputType==="power"&&window.shipManager&&shipManager.power&&typeof shipManager.power.getDockedPowerSummary=="function"){var de=shipManager.power.getDockedPowerSummary(r);de.donors>0&&(K=de)}var Ee=null;if(typeof d.getAbductionOrder=="function"){var pe=d.getAbductionOrder();if(pe){var ae=gamedata.getShip(pe.targetid);Ee=ae?ae.name:"Unit "+pe.targetid}}let ce=!1;if(r.mine){var y=shipManager.systems.getSystemByName(r,"mineStealth");y&&!y.isMineRevealed(r)&&(ce=!0,j="Mine",E=["No details known, scan with OEW to identify."])}return m.jsxs(dE,{position:gE(h),children:[m.jsx(xd,{children:j}),!r.flight&&!ce&&wl("Structure",d.maxhealth-damageManager.getDamage(r,d)+"/"+d.maxhealth),!r.flight&&!ce&&wl("Armor",shipManager.systems.getArmour(r,d)),r.flight&&!ce&&wl("Offensive bonus",c_(d,N*5)),d.firingModes&&!ce&&wl("Firing mode",H),d.missileArray&&Object.keys(d.missileArray).length>0&&!ce&&wl("Ammo Amount",d.missileArray[d.firingMode].amount),!ce&&Object.keys(d.data).map((Te,le)=>Te!=$&&!(Te==="Ammunition"&&(d.name==="GrapplingClaw"||d.name==="Marines"))&&wl(Te,d_(d,Te),"data"+le)),L!==null&&wl("Fighters available",L),K&&wl("Shared by docked ships","+"+K.shared+" of "+K.surplus+" pooled from "+K.donors+(K.donors===1?" ship":" ships")),Ee!==null&&wl("Abduction target",Ee),Object.keys(E).length>0&&m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Special: "})," "]},`special-${M++}`),Object.keys(E).length>0&&Object.keys(E).map(Te=>m.jsx(Ut,{children:E[Te]},`special-${M++}`)),(Object.keys(d.critData).length>0||d.criticals&&d.criticals.length>0)&&!ce&&u_(d),!gamedata.isMyShip(r)&&!ce&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&l&&hE(r,l,d),gamedata.isMyShip(r)&&!ce&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&l&&hE(r,l,d),gamedata.isMyShip(r)&&!ce&&d.weapon&&weaponManager.hasFiringOrder(r,d)&&s_(r,d)]})}}const hE=(o,r,l)=>weaponManager.canCalledshot(o,l,r)?[m.jsx(xd,{children:"Called shot"},"calledHeader")].concat(gamedata.selectedSystems.map((d,h)=>{if(weaponManager.isOnWeaponArc(r,o,d))if(weaponManager.checkIsInRange(r,o,d)){var b=d.firingMode;return b=d.firingModes[b],l.id!=null&&!weaponManager.canWeaponCall(d)?m.jsxs(Ut,{children:[m.jsx(Yn,{children:d.displayName}),": Cannot Called Shot"]},`called-${h}`):m.jsxs(Ut,{children:[m.jsx(Yn,{children:d.displayName})," - Approx:  ",weaponManager.calculateHitChange(r,o,d,l.id).hitChance,"%"]},`called-${h}`)}else return m.jsxs(Ut,{children:[m.jsx(Yn,{children:d.displayName}),": Not in Range"]},`called-${h}`);else return m.jsxs(Ut,{children:[m.jsx(Yn,{children:d.displayName}),": Not in Arc"]},`called-${h}`)})):[m.jsx(xd,{children:"Called shot"},"calledHeader")].concat(m.jsx(Ut,{children:"Cannot Target"},"cannotTarget")),s_=(o,r)=>{var l=weaponManager.getCalledShotInfo(o,r);return l?[m.jsx(fE,{},"calledShotDivider"),m.jsx(xd,{children:"Called Shot"},"calledShotHeader"),m.jsxs(Ut,{children:[m.jsx(Yn,{children:"Target: "}),l.targetSystem.displayName," (Id: ",l.targetSystem.id,") on ",l.targetShip.name]},"calledShotTarget")]:null},u_=o=>{const r=Object.keys(o.critData).length>0?Object.keys(o.critData):[...new Set((o.criticals||[]).map(l=>l.phpclass))];return r.length===0?null:[m.jsx(fE,{},"critDivider"),m.jsx(xd,{children:"Criticals"},"criticalHeader")].concat(r.map(l=>{let d=0,h=0;var b=0,S=0,y=!1,E="";b=0,S=0,y=!1,E="";for(const M in o.criticals)o.criticals[M].phpclass==l&&o.criticals[M].turn<=gamedata.turn&&(o.criticals[M].turnend==0||o.criticals[M].turnend>=gamedata.turn)&&(d++,h+=parseInt(o.criticals[M].param,10)||0,d==1&&(b=o.criticals[M].turnend,S=o.criticals[M].turnend,y=o.criticals[M].turnend==0),o.criticals[M].turnend>0?(o.criticals[M].turnend>S&&(S=o.criticals[M].turnend),(o.criticals[M].turnend<b||b==0)&&(b=o.criticals[M].turnend)):y=!0);if(b>0&&(E=" (until end of turn "+b,y?E=E+"+":S>b&&(E=E+"-"+S),E=E+")"),d>=1&&pE[l]){const M=pE[l](h);return m.jsxs(Ut,{children:[M," ",E]},`critical-${l}`)}const $=o.critData[l]||a_[l]||l;return d>1?m.jsxs(Ut,{children:["(",d," x) ",$," ",E]},`critical-${l}`):d==1?m.jsxs(Ut,{children:[$," ",E]},`critical-${l}`):null}))},c_=(o,r)=>typeof o.adjustOffensiveBonusDisplay=="function"?o.adjustOffensiveBonusDisplay(r):r,d_=(o,r)=>typeof o.adjustDataValueDisplay=="function"?o.adjustDataValueDisplay(r,o.data[r]):o.data[r],wl=(o,r,l)=>{if(typeof r=="string"&&r.indexOf("<br>")!==-1){const d=r.split("<br>");return m.jsxs(Ut,{children:[m.jsxs(Yn,{children:[o,": "]}),d.map((h,b)=>m.jsxs(Ge.Fragment,{children:[b>0&&m.jsx("br",{}),h]},b))]},l)}return m.jsxs(Ut,{children:[m.jsxs(Yn,{children:[o,": "]}),r]},l)},gE=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left+o.width,r};D(J1)`
    font-size: 12px;
`;const f_=D(X1)`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.position).reduce((r,l)=>r+`
`+l+":"+o.position[l]+"px;","")}
    max-width: 500px;
    text-align: left;
    opacity: ${o=>o.opacity||.8};
    border: 1px solid #496791;
    padding-bottom: 3px;
    /*the lobby mounts #systemInfoReact inside a pointer-events: none fixed overlay
      (same as #shipWindowsReact) - this menu is interactive, so it must opt back in.
      No-op in game.php, where the mount point has no pointer-events override.*/
    pointer-events: auto;
    /*Lobby: the menu inside (ApplyDamageMenu) is a framed panel of its own, so this tooltip
      draws nothing round it - no second border, padding or fill, and no element opacity.*/
    ${o=>o.$bare&&`
        padding: 0;
        border: 0;
        border-radius: 0;
        background: none;
        opacity: 1;
    `}
`;D(Z1)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,D.span`
    color: white;
`;class p_ extends Ge.Component{render(){const{ship:r,system:l,boundingBox:d}=this.props;return l0(r,l)?m.jsx(f_,{position:h_(d),opacity:Nj(r,l)?.95:.8,$bare:gamedata.gamephase===-2,children:m.jsx(Oj,{...this.props})}):null}}const h_=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r},g_={0:"Primary",1:"Forward",2:"Aft",3:"Port",4:"Starboard",5:"",31:"Port Fwd",32:"Port Aft",41:"Stbd Fwd",42:"Stbd Aft"},m_=D.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${A.colors.panelBgGlass};
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

    border: ${o=>{switch(o.$location){case 0:return`1px solid ${A.colors.line}`;default:return`1px dotted ${A.colors.line}`}}};
`,v_=D.div`
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
    border-bottom: 1px solid ${A.colors.healthOk};
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
        background-color: ${o=>o.$criticals?A.colors.healthCrit:A.colors.healthOk};
    }
`,y_=D.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${A.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
`,x_=D.span`
    position: relative;
    z-index: 1;
    font-family: ${A.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${o=>o.$destroyed?"transparent":A.colors.text};
    filter: ${o=>o.$destroyed?"blur(1px)":"none"};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,b_=D.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;class mE extends Ge.Component{constructor(r){super(r),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(r){const{ship:l,systems:d}=this.props,h=h0(d);if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(o0(l)){r.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("MineDamageClicked",{ship:l,element:r.currentTarget});return}!h||!Pu(l,h)||(r.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:l,system:h,element:r.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.hideStructureArc()}showStructureArc(){const{ship:r,systems:l}=this.props;this.arcShown=!0,window.uiEvents.relay("StructureMouseOver",{ship:r,structure:h0(l)})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay("StructureMouseOut"))}onStructureMouseOver(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),this.showStructureArc())}onStructureMouseOut(r){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(r.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(r){r.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const l=r.touches[0];this.touchStartX=l.clientX,this.touchStartY=l.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(r){if(r.stopPropagation(),!this.longPressTimer)return;const l=r.touches[0];(Math.abs(l.clientX-this.touchStartX)>10||Math.abs(l.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(r){r.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(r){r.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,this.hideStructureArc()}render(){const{ship:r,systems:l,location:d,displayLocation:h,area:b,valign:S,justify:y,wide:E,isTerrain:$,minHeight:M,nameOverride:N,hidden:j}=this.props,H=h0(l),L=o0(r),K=L?battleDamage.mineHealth(r,1):0,de=L?battleDamage.mineMaxHealth(r):0,Ee=L?K/de*100:H?w_(r,H):0,pe=h!==void 0?h:d,ae=h!==void 0&&h!==d;return m.jsxs(m_,{$location:d,$area:b,$valign:S,$justify:y,$wide:E,$isTerrain:$,$minHeight:M,$hidden:j,children:[H&&m.jsxs(v_,{$health:Ee,$criticals:S_(H),$damageable:Pu(r,H)||o0(r),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[m.jsx(y_,{children:N||g_[d]||""}),m.jsxs(x_,{$destroyed:Ee===0,children:[L?K:H.maxhealth-damageManager.getDamage(r,H),"/",L?de:H.maxhealth," A",shipManager.systems.getArmour(r,H)]})]}),m.jsx(b_,{children:C_(l,pe,E).map(ce=>m.jsx(f0,{scs:!0,mirror:ae,system:ce,ship:r},`system-scs-${d}-${r.id}-${ce.id}`))})]})}}const w_=(o,r)=>(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,S_=o=>shipManager.criticals.hasCriticals(o),vE=o=>o.name==="structure",h0=o=>o.find(vE),pm=o=>o.filter(r=>!vE(r)),C_=(o,r,l)=>(o=pm(o),l?g0(o):[4,41,42].includes(r)?m0(o):[3,31,32].includes(r)?E_(m0(o)):[1,2,0].includes(r)?g0(o):T_(o)),E_=o=>{let r=[];return o.forEach((l,d)=>{const h=d%3;h===0?r[d+2]=l:h===1?r[d]=l:r[d-2]=l}),r},T_=o=>(o=pm(o),o.length===3?m0(o):o.length===4?g0(o):o),g0=o=>{o=pm(o);let r=[];for(;;){const{picked:l,remaining:d}=y0(o,4);if(l.length===0)break;o=d,r=r.concat(l)}for(;;){const{picked:l,remaining:d}=y0(o,2);if(l.length===0)break;o=d;const h=y0(o,2);h.picked.length>0?(o=h.remaining,r=r.concat([l[0],h.picked[0],h.picked[1],l[1]])):(r=r.concat([l[0],o.shift(),o.shift(),l[1]]),r=r.filter(b=>b))}return r=r.concat(o),r},m0=o=>{o=pm(o);let r=[];for(;;){const{picked:l,remaining:d}=v0(o,3);if(l.length===0)break;o=d,r=r.concat(l)}for(;;){const{picked:l,remaining:d}=v0(o,2);if(l.length===0)break;const{three:h,remainingSystems:b}=k_(l,d);o=b,r=r.concat(h)}return r=r.concat(o),r},k_=(o,r)=>{const l=v0(r,1);return l.picked.length===1?{three:[l.picked[0],o[0],o[1]],remainingSystems:l.remaining}:r.length>0?{three:[r.shift(),o[0],o[1]],remainingSystems:r}:{three:[o[0],o[1]],remainingSystems:r}},v0=(o,r=3)=>{const l=o.find(b=>{const S=o.reduce((y,E)=>E.name===b.name?y+1:y,0);return r===1?S===r:S>=r});if(!l)return{picked:[],remaining:o};let d=[];const h=o.filter(b=>b.name===l.name&&r>0?(r--,d.push(b),!1):!0);return{picked:d,remaining:h}},y0=(o,r=3)=>{const l=o.find($=>{const M=o.reduce((N,j)=>j.name===$.name?N+1:N,0);return r===1?M===r:M>=r});if(!l)return{picked:[],remaining:o};let d=[],h=[];const b=o.filter($=>$.name===l.name?(h.push($),!1):!0);for(var S=Math.ceil(r/2),y=Math.floor(r/2),E=0;E<h.length;E++)E<S||E>=h.length-y?d.push(h[E]):b.unshift(h[E]);return{picked:d,remaining:b}},yE={DEW:"#aecdea",CCEW:A.colors.text,SDEW:"#9ac1e5",OEW:"#acd7a8",BDEW:"#8ac785","Detect Mines":"#bfa3db","Detect Stealth":"#ccb6e2",DIST:"#e6b98f",SOEW:A.colors.text,OEW_HOSTILE:"#e49b9b","Saved EW":"#e0d39a"},bs=(o,r)=>o==="OEW"&&r&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(r)?yE.OEW_HOSTILE:yE[o]||A.colors.textAccent,xE=D.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${o=>o.$flight?"":fd`
        grid-area: ew;
        justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    `}
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${A.colors.panelBgGlass};
    border: 1px solid ${A.colors.line};
    padding: 1px 4px 1px;
`,bE=D.div`
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
    color: ${A.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${A.colors.line};
`,ws=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${A.colors.text};
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
    ${o=>o.$target&&fd`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${o=>o.$hoverable&&fd`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,R_=D.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,Ss=D.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${o=>o.$color||A.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,Cs=D.span`
    font-family: ${A.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,D_=D.span`
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
    color: ${A.colors.textAccent};
    ${o=>o.$interactive&&fd`
        cursor: pointer;
        &:hover {
            color: ${A.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;class wE extends Ge.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(r,l){window.webglScene&&(window.uiEvents.relay("EwRangeHover",{shipId:this.props.ship.id,type:r,active:l}),this.activeRangeOverlay=l?r:null)}onTargetClick(r,l){l.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(r)||window.uiEvents.relay("ScrollToShip",{shipId:r.id}))}setTargetHighlight(r,l,d){window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:r.id,type:l,active:d}),this.activeHighlight=d?{targetId:r.id,type:l}:null)}render(){const{ship:r,flight:l}=this.props;return l?m.jsxs(xE,{$flight:!0,children:[m.jsx(bE,{children:"Electronic Warfare"}),M_(r),CE(r,this)]}):m.jsxs(xE,{children:[m.jsx(bE,{children:"Electronic Warfare"}),$_(r,this),CE(r,this)]})}}const M_=o=>{const r=[m.jsxs(ws,{children:[m.jsx(Ss,{$color:bs("DEW"),children:"DEW"}),m.jsx(Cs,{children:ki(ew.getFlightDEW(o))})]},`dew-scs-${o.id}`)],l=SE(o);return l&&r.push(l),r},SE=o=>{if(!gamedata.isPlayerInGame()||!gamedata.isMyorMyTeamShip(o))return null;const r=ew.getSavedEwAllowance(o);if(r<=0)return null;const d=ew.isLateEwWindowOpen(o)||ew.isLateEwPhase()&&gamedata.isMyShip(o)?`${ki(ew.getLateEwRemaining(o))} / ${ki(r)}`:ki(r);return m.jsxs(ws,{children:[m.jsx(Ss,{$color:bs("Saved EW"),children:"Saved EW"}),m.jsx(Cs,{children:d})]},`savedew-scs-${o.id}`)},$_=(o,r)=>{let l=[];const d=!!window.webglScene,h=M=>d?{$hoverable:!0,onMouseEnter:()=>r.setRangeOverlay(M,!0),onMouseLeave:()=>r.setRangeOverlay(M,!1)}:{};l.push(m.jsxs(ws,{children:[m.jsx(Ss,{$color:bs("DEW"),children:"DEW"}),m.jsx(Cs,{children:ki(ew.getDefensiveEW(o))})]},`dew-scs-${o.id}`));var b=Math.max(0,ew.getCCEW(o)-ew.getDistruptionEW(o));b>0&&l.push(m.jsxs(ws,{children:[m.jsx(Ss,{$color:bs("CCEW"),children:"CCEW"}),m.jsx(Cs,{children:ki(b)})]},`ccew-scs-${o.id}`));let S=ew.getBDEW(o)*.25,y=ew.getDetectSEW(o),E=ew.getDetectMEW(o);shipManager.hasSpecialAbility(o,"ConstrainedEW")&&(S=ew.getBDEW(o)*.2),S&&l.push(m.jsxs(ws,{...h("BDEW"),children:[m.jsx(Ss,{$color:bs("BDEW"),children:"BDEW"}),m.jsx(Cs,{children:ki(S)})]},`bdew-scs-${o.id}`)),E&&l.push(m.jsxs(ws,{...h("MDEW"),children:[m.jsx(Ss,{$color:bs("Detect Mines"),children:"Detect Mines"}),m.jsx(Cs,{children:ki(E)})]},`DetectMEW-scs-${o.id}`)),y&&l.push(m.jsxs(ws,{children:[m.jsx(Ss,{$color:bs("Detect Stealth"),children:"Detect Stealth"}),m.jsx(Cs,{children:ki(y)})]},`DetectSEW-scs-${o.id}`));const $=SE(o);return $&&l.push($),l},CE=(o,r)=>{const l=!!window.webglScene;return o.EW.filter(d=>d.turn===gamedata.turn).filter(d=>d.type==="OEW"||d.type==="DIST"||d.type==="SOEW"||d.type==="SDEW").map(d=>{const h=gamedata.getShip(d.targetid);return m.jsxs(ws,{$target:!0,children:[m.jsxs(R_,{children:[m.jsx(Ss,{$color:bs(d.type,o),children:d.type}),m.jsx(D_,{$interactive:l,title:void 0,onClick:l?r.onTargetClick.bind(r,h):void 0,onMouseEnter:l?()=>r.setTargetHighlight(h,d.type,!0):void 0,onMouseLeave:l?()=>r.setTargetHighlight(h,d.type,!1):void 0,children:h.name})]}),m.jsx(Cs,{children:O_(d,o)})]},`${d.type}-scs-${o.id}-${d.targetid}`)})},O_=(o,r)=>{switch(o.type){case"SDEW":if(shipManager.hasSpecialAbility(r,"ConstrainedEW")){let l=o.amount*.333;return l=Math.round(l*3)/3,ki(l)}else return ki(o.amount*.5);case"DIST":return shipManager.hasSpecialAbility(r,"ConstrainedEW")?ki(o.amount/4):ki(o.amount/3);case"OEW":return ki(Math.max(0,o.amount-ew.getDistruptionEW(r)));default:return ki(o.amount)}},ki=o=>Math.round(o*100)/100,hm=()=>!!window.gamedata&&window.gamedata.gamephase===-2,A_=D.div`
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
`,j_=D.div`
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
`,kp=D.div`
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
`,EE=D.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,__=D(EE)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,L_=D.div`
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
`,z_=D.div`
    z-index: 1;
`;class N_ extends Ge.Component{onSystemMouseOver(r){if(hm()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||r.nativeEvent&&r.nativeEvent.sourceCapabilities&&r.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:l}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:l,system:l,element:r.target})}onSystemMouseOut(){hm()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onFighterTouchStart(r){if(hm())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const l=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:h}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:h,system:h,element:l,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(r){if(!this.longPressTimer)return;const l=r.touches[0],d=l.clientX-this.touchStartX,h=l.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(h)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(r){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onFighterTouchEnd(r){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){const{ship:r}=this.props,l=window.gamedata&&typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted();return hm()&&!l&&!!r&&r.userid!=0&&!!r.flight}onHealthBarClick(r){this.canApplyPreBattleDamage()&&(r.stopPropagation(),r.preventDefault(),window.uiEvents.relay("FighterDamageClicked",{ship:this.props.ship,fighter:this.props.fighter,element:r.currentTarget}))}render(){const{ship:r,fighter:l}=this.props,d=shipManager.systems.isDestroyed(r,l),h=shipManager.criticals.isDockedFighter(l),b=!h&&shipManager.criticals.isSplitLaunchedFighter(l),S=!h&&!b&&shipManager.criticals.isDisengagedFighter(l),y=shipManager.criticals.isCutOffFighter(l);let E=null;d?h?E=m.jsx(kp,{$color:"#00b8e6",children:"DOCKED"}):b?E=m.jsx(kp,{$color:"#00b8e6",children:"SPLIT"}):S?E=m.jsx(kp,{$color:"#ff8c00",children:"DROPOUT"}):E=m.jsx(kp,{$color:"#ff5252",children:"DESTROYED"}):y&&(E=m.jsx(kp,{$color:"#ff5252",children:"CUT OFF"}));const $=this.canApplyPreBattleDamage(),M=$?battleDamage.fighterHealth(r,1)/l.maxhealth*100:P_(r,l),N=$?`${battleDamage.fighterHealth(r,1)} / ${l.maxhealth}`:`${l.maxhealth-damageManager.getDamage(r,l)} / ${l.maxhealth}`;return m.jsxs(A_,{$docked:h,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[m.jsxs(j_,{$destroyed:d,$img:window.AssetManager.getSmartImagePath(l.iconPath),children:[m.jsx(EE,{children:TE(r,l,I_(l),d)}),m.jsx(__,{children:TE(r,l,U_(l),d)}),m.jsx(L_,{$health:M,$criticals:F_(l),$criticalsBenign:B_(l),$docked:h,$clickable:$,title:$?"Apply pre-battle damage to this flight":void 0,onClick:this.onHealthBarClick.bind(this),children:m.jsx(z_,{children:N})})]}),E]})}}const P_=(o,r)=>(r.maxhealth-damageManager.getDamage(o,r))/r.maxhealth*100,F_=o=>shipManager.criticals.hasCriticals(o),B_=o=>shipManager.criticals.hasOnlyCritical(o,"LaunchedThisTurn",!1),I_=o=>o.systems.filter(r=>r.location==1),U_=o=>o.systems.filter(r=>r.location!=1),TE=(o,r,l,d)=>l.map((h,b)=>m.jsx(f0,{$destroyed:d,fighter:!0,scs:!0,system:h,ship:o},`system-scs-fighter${r.id}-${o.id}-${h.id}-${b}`)),H_=D.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;class x0 extends Ge.Component{render(){const{ship:r}=this.props;return m.jsx(H_,{children:V_(r)})}}const V_=o=>o.systems.map((r,l)=>m.jsx(N_,{fighter:r,ship:o},`flight-${o.id}-${l}`)),W_=[3,31,32],Y_=[1,0,2],G_=[4,41,42],K_=D.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,b0=D.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${o=>o.$side?"center":"flex-start"};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,Q_=D.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${A.colors.line};
    padding: 3px 5px;
`,q_=D.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${A.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${A.colors.line};
`,X_=D.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${A.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`,J_=D.span`
    font-family: ${A.fonts.mono};
    color: ${A.colors.text};
    flex-shrink: 0;
`,Z_=(o,r)=>m.jsxs(Q_,{children:[m.jsx(q_,{children:r.name}),[...r.entries].sort((l,d)=>d.chance-l.chance).map((l,d)=>m.jsxs(X_,{children:[m.jsx("span",{children:l.name}),m.jsxs(J_,{children:[l.chance,"%"]})]},`hitchart-${o.id}-${r.location}-${d}`))]},`hitchart-${o.id}-${r.location}`);class eL extends Ge.Component{render(){const{ship:r}=this.props,l=uE(r);if(l.length===0)return null;const d={};l.forEach(E=>{d[E.location]=E});const h=E=>E.filter($=>d[$]).map($=>Z_(r,d[$])),b=h(W_),S=h(Y_),y=h(G_);return m.jsxs(K_,{children:[b.length>0&&m.jsx(b0,{$side:!0,children:b}),S.length>0&&m.jsx(b0,{children:S}),y.length>0&&m.jsx(b0,{$side:!0,children:y})]})}}const tL=o=>o.split(" ").map(r=>r.charAt(0).toUpperCase()+r.slice(1)).join(" "),nL=o=>{const r=[];if(!o||o.flight)return r;const l=o.fighters||{};if(Object.keys(l).length>0){const h={};if(shipManager.systems.shipHasRestrictedHangar(o)){const b=shipManager.systems.getReservedFighterComposition(o);for(let S=0;S<b.length;S++){const y=b[S].category;h[y]||(h[y]=[]);const E=h[y];let $=!1;for(let M=0;M<E.length;M++)if(E[M].phpclass===b[S].phpclass){E[M].count+=b[S].count,$=!0;break}$||E.push({category:b[S].category,phpclass:b[S].phpclass,displayName:b[S].displayName,count:b[S].count,isGroup:b[S].isGroup})}}for(const b in l){const S=l[b],y=tL(b),E=h[b];if(E&&(b==="heavy"||b==="medium"||b==="light")){let $=S;for(let M=0;M<E.length;M++){const N=Math.min(E[M].count,$);N<=0||(E[M].isGroup?r.push(N+" "+E[M].displayName+"s"):r.push(N+" "+E[M].displayName+" "+y+" Fighters"),$-=N)}$>0&&r.push($+" "+y+" Fighters");continue}if(b==="normal")r.push(S+" Fighters");else if(b==="superheavy"||b==="heavy"||b==="medium"||b==="light"||b==="ultralight")r.push(S+" "+y+" Fighters");else{if(b==="shuttles"||b==="minesweeping shuttles"||b==="cargo shuttles"||b==="lifeboats"||b==="medical shuttles"||b==="presidential shuttle"||b==="yacht")continue;r.push(S+" "+y)}}}const d=shipManager.systems.getDefaultShuttleComposition(o);for(let h=0;h<d.length;h++)r.push(d[h].count+" "+d[h].type);return r},rL=D.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${A.colors.textAccent};
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
`,Rp=D.div`
    background-color: ${A.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${o=>o.$gold?A.colors.enhLine:A.colors.line};
    padding: 0 8px 3px;
`,w0=D.div`
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
    color: ${o=>o.$gold?A.colors.enhTitle:A.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${o=>o.$gold?A.colors.enhBg:"rgba(73, 103, 145, 0.25)"};
    border-bottom: 1px solid ${o=>o.$gold?A.colors.enhLine:A.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,Dp=D.div`
    padding: 1px 0;
`,iL=D.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${A.colors.custom};
`,Yi=D.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,Gi=D.span`
    font-size: 10px;
    color: ${A.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,Ki=D.span`
    font-family: ${A.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${o=>o.$changed?A.colors.custom:A.colors.text};
    margin-right: 5px;
`,aL=D.div`
    width: 150px;
    box-sizing: border-box;
    ${o=>o.$bare?`
    padding: 0;`:`
    background-color: ${A.colors.panelBgGlass};
    border: 1px dotted ${A.colors.line};
    padding: 2px 4px 3px;`}
`,oL=D.div`
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
    color: ${A.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${A.colors.line};
`,kE=D.span`
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
        background-color: ${A.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;D.div`
    text-align: center;
    font-size: 10px;
    color: ${A.colors.warning};
    padding-top: 2px;
`;const RE=D.div`
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
    color: ${A.colors.enhTitle};
    background-color: ${A.colors.enhBg};
    border-bottom: 1px solid ${A.colors.enhLine};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,lL=D.div`
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
    color: ${A.colors.enhText};
`,S0=o=>typeof o=="number"?o.toFixed(2):o,DE=(o,r)=>o*5+"/"+r*5,gm=(o,r)=>o+" ("+S0(r)+")",sL=o=>{const r=window.shipManager;if(!r)return null;const l={},d=r.systems?r.systems.getSystemByName(o,"CnC"):null,h=d&&r.criticals?r.criticals.hasCritical(d,"ProfileIncreased"):0;l.profile=DE(o.forwardDefense+h,o.sideDefense+h),l.profileChanged=h!==0;const b=o.iniativeadded||0;l.initiative=(o.iniativebonus||0)+b,l.initiativeChanged=b!==0;const S=r.movement;if(S&&typeof S.getTurnCost=="function"&&o.movement&&o.movement.length>0){const y=S.getSpeed(o),E=S.getDockedLcvTurnSurcharge(o),$=S.getTurnDelayCost(o);let M=S.getTurnCost(o);o.submarine&&S.isGoingBackwards(o)&&(M=M*1.33);const N=Math.max(1,Math.ceil(y*M))+E,j=S.applyCrewTurnDelay(o,Math.ceil(y*$))+E;l.turnCost=gm(N,M),l.turnDelay=gm(j,$),l.turnCostChanged=l.turnCost!==gm(Math.max(1,Math.ceil(y*o.turncost)),o.turncost),l.turnDelayChanged=l.turnDelay!==gm(Math.ceil(y*o.turndelaycost),o.turndelaycost)}return l},ME=({ship:o,live:r,bare:l})=>{const d=!o.base,h=r?sL(o):null;return m.jsxs(aL,{$bare:l,children:[!l&&m.jsxs(oL,{children:[m.jsxs(kE,{children:[m.jsx("i",{}),m.jsx("i",{}),m.jsx("i",{})]}),"Ship Stats"]}),d&&m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Turn cost"}),m.jsx(Ki,{$changed:!!(h&&h.turnCostChanged),children:h&&h.turnCost?h.turnCost:S0(o.turncost)})]}),d&&m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Turn delay"}),m.jsx(Ki,{$changed:!!(h&&h.turnDelayChanged),children:h&&h.turnDelay?h.turnDelay:S0(o.turndelaycost)})]}),d&&m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Accel/decel"}),m.jsx(Ki,{children:o.accelcost})]}),d&&m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Pivot"}),m.jsx(Ki,{children:o.pivotcost})]}),d&&m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Roll"}),m.jsx(Ki,{children:o.rollcost})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Profile - Front / Side"}),m.jsx(Ki,{$changed:!!(h&&h.profileChanged),children:h?h.profile:DE(o.forwardDefense,o.sideDefense)})]}),d&&m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Initiative"}),m.jsx(Ki,{$changed:!!(h&&h.initiativeChanged),children:h?h.initiative:o.iniativebonus})]})]})},uL=({ship:o})=>{const r=C0(o.enhancementTooltip);return r.length===0?null:m.jsx(lL,{children:m.jsxs(Rp,{$gold:!0,children:[m.jsx(RE,{children:"Enhancements"}),r.map((l,d)=>m.jsx(Dp,{children:l},`enh-${d}`))]})})},C0=o=>(o||"").split(/<br\s*\/?>/i).map(r=>r.replace(/<[^>]*>/g,"").replace(/&nbsp;/g," ").trim()).filter(Boolean);class E0 extends Ge.Component{render(){const{ship:r,full:l,grid:d,hideEnhancements:h}=this.props,b=nL(r),S=C0(r.notes),y=h?[]:C0(r.enhancementTooltip),E=[];if(r.limited&&r.limited!=0&&E.push("Limited: "+r.limited+"%"),r.variantOf){const N=r.occurence?r.occurence.charAt(0).toUpperCase()+r.occurence.slice(1)+" ":"";E.push(N+"variant of "+r.variantOf)}r.isd&&E.push("In-Service (ISD): "+r.isd);let $=null;r.unofficial==="S"?$="Semi-Custom":r.unofficial&&($="Custom");const M=S.length>0||E.length>0||$;return m.jsxs(rL,{$full:l,$grid:d,children:[r.flight&&m.jsxs(Rp,{children:[m.jsx(w0,{children:"Flight Stats"}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Armor F/S/A"}),m.jsx(Ki,{children:shipManager.systems.getFlightArmour(r)})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Offensive bonus"}),m.jsx(Ki,{children:r.offensivebonus*5})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Profile - Front / Side"}),m.jsxs(Ki,{children:[r.forwardDefense*5,"/",r.sideDefense*5]})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Thrust"}),m.jsx(Ki,{children:r.freethrust})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Turn Cost / Delay"}),m.jsxs(Ki,{children:[r.turncost," / ",r.turndelaycost==0?0:r.turndelaycost]})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Accel. / Pivot / Roll"}),m.jsxs(Ki,{children:[r.accelcost," / ",r.pivotcost," / ",r.rollcost]})]}),m.jsxs(Yi,{children:[m.jsx(Gi,{children:"Initiative"}),m.jsx(Ki,{children:r.iniativebonus})]})]}),b.length>0&&m.jsxs(Rp,{children:[m.jsx(w0,{children:"Hangar Capacity"}),b.map((N,j)=>m.jsx(Dp,{children:N},`comp-${j}`))]}),M&&m.jsxs(Rp,{children:[m.jsx(w0,{children:"Notes"}),S.map((N,j)=>m.jsx(Dp,{children:N},`note-${j}`)),E.map((N,j)=>m.jsx(Dp,{children:N},`meta-${j}`)),$&&m.jsx(iL,{children:$})]}),y.length>0&&m.jsxs(Rp,{$gold:!0,children:[m.jsx(RE,{children:"Enhancements"}),y.map((N,j)=>m.jsx(Dp,{children:N},`enh-${j}`))]})]})}}const cL=400,dL=D.div`
    display: flex;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 4px 0 0;
`,fL=D.div`
    flex: 0 1 auto;
    min-width: 0;
    width: max-content;
    max-width: ${cL}px;
`,Mp=D.div`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${o=>o.$isMyTeam?`left: 50px; 
 top: 50px;`:`right: 50px; 
 top: 50px;`}
    width: ${o=>o.$variant==="terrain"?"250px":o.$variant==="flight"||o.$variant==="flightEw"?"auto":"fit-content"};
    max-width: ${o=>o.$variant==="flight"?"400px":o.$variant==="flightEw"?"574px":o.$variant==="flightLobby"?"620px":"unset"};
    height: auto;
    border: 1px solid ${A.colors.line};
    background-color: ${A.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${A.colors.text};
    font-family: ${A.fonts.body};

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
`,pL=D.div`
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
    background-color: ${A.colors.panelBg};
    border-bottom: 1px solid ${A.colors.line};
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
`,hL=D.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${o=>o.$tint||A.colors.text};
`,gL=D.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${A.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,mL=D.div`
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
    color: ${A.colors.line};
    ${pa}
`,vL=D.div`
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
        background: repeating-linear-gradient(${o=>o.$mirror?"45deg":"315deg"}, ${A.colors.line} 0 1.5px, transparent 1.5px 4px);
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
`,yL=D.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${o=>o.$compact?"width: 100%; align-items: center; margin-bottom: 5px;":"align-items: stretch;"}
    gap: 4px;
`,mm=D.div`
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
    border: 1px solid ${A.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${o=>o.$active?"rgba(198, 226, 255, 0.12)":"rgba(73, 103, 145, 0.25)"};
    color: ${A.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${pa}
`,$E=D.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,xL=D.span`
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
`,T0=D.div`
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
    background-color: ${A.colors.panelBg};
    border: 1px solid ${A.colors.line};
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
`,bL=D.div`
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
`,wL=D.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,SL=D.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,k0=D.div`
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
`,R0=D.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,CL=D.div`
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
    color: ${o=>o.$color||A.colors.warning};
    background-color: ${o=>o.$bg||"rgba(225, 176, 0, 0.10)"};
    border-top: 1px solid ${A.colors.line};
    flex-shrink: 0;
`,OE=D.div`
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
`,EL=D.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${A.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${A.fonts.body};
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
`,TL={1:"fwd",2:"aft",0:"prim",3:"left",4:"right",31:"lfwd",41:"rfwd",32:"laft",42:"raft"},AE={3:4,4:3,31:41,41:31,32:42,42:32},kL={fwd:"end",aft:"center",prim:"center",left:"center",right:"center",lfwd:"start",rfwd:"start",laft:"end",raft:"end"},RL={left:"end",lfwd:"end",laft:"end",right:"start",rfwd:"start",raft:"start"},DL=[1,3,31,32,0,4,41,42,2],ML=[1,3,31,0,4,41,32,2,42],$L=[31,32,41,42],OL=[3,4,31,41,32,42];class AL extends Ge.Component{constructor(r){super(r),this.elementRef=Ge.createRef(),this.controlsRef=Ge.createRef(),this.popupRef=Ge.createRef(),this.hitChartBtnRef=Ge.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return xm(this.props.ship)?"left":"right"}isMirroredGrip(){return this.side()==="right"}applyScreenFit(){const r=this.elementRef.current;if(!r)return;const l=_E(),d=ym(this.side());if(!l&&d===1){(this.screenFit!==1||this.screenFitHeight)&&(r.style.transform="",r.style.transformOrigin="",r.style.maxHeight="",this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}const h=this.measureNatural();if(!h)return;const b=l?FL():null,S=window.innerHeight||document.documentElement.clientHeight;let y=1;if(l){const M=(document.documentElement.clientWidth||window.innerWidth)*b.fillW,N=S*b.fillH;y=Math.min(M/h.width,N/h.height),y=Math.min(b.max,Math.max(b.min,y))}this.autoFit=y;let E=$0(y*d);E=Math.round(E*100)/100;let $=null;if(l){const M=Math.min(D0,b.fillH*Math.max(1,d));$=Math.round(S*M/E)}E===this.screenFit&&$===this.screenFitHeight||(this.screenFit=E,this.screenFitHeight=$,r.style.transformOrigin=this.transformOrigin(),r.style.transform=E===1?"":"scale("+E+")",r.style.maxHeight=$==null?"":$+"px",this.resizeStart||this.keepGripOnScreen())}measureNatural(){const r=this.elementRef.current;if(!r)return null;const l=r.style.maxHeight;r.style.maxHeight="none";const d=r.offsetWidth,h=r.offsetHeight;return r.style.maxHeight=l,d&&h?{width:d,height:h}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:xm(this.props.ship)?"top left":"top right"}onScreenResize(){this.applyScreenFit()}isDragHandle(r){return!r||!r.closest||r.closest(".shipwindow-nodrag")?!1:!!r.closest(".shipwindow-drag-handle")}isResizeHandle(r){return!!(r&&r.closest&&r.closest(".shipwindow-resize-grip"))}isDragSlop(r,l){if(!r||!r.closest||!r.closest(".shipwindow-grab-slop"))return!1;const d=this.elementRef.current,h=d&&d.querySelector(".shipwindow-drag-handle");if(!h)return!1;const b=h.getBoundingClientRect();return l>=b.top&&l<=b.bottom+HL}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(r,l,d){const h=Date.now(),b=!!this.lastPress&&this.lastPress.kind===r&&h-this.lastPress.time<VL;this.lastPress={kind:r,time:h},this.pressPoint={x:l,y:d},this.pendingReset=b}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(r,l){const d=this.elementRef.current;if(!d)return!1;const h=window.getComputedStyle(d);let b=parseFloat(h.left),S=parseFloat(h.top);return isFinite(b)||(b=d.offsetLeft),isFinite(S)||(S=d.offsetTop),this.dragStart={x:r,y:l,left:b,top:S},this.positioned=!0,d.style.left=b+"px",d.style.top=S+"px",d.style.right="auto",!0}moveDrag(r,l){const d=this.elementRef.current;!this.dragStart||!d||(d.style.left=this.dragStart.left+(r-this.dragStart.x)+"px",d.style.top=this.dragStart.top+(l-this.dragStart.y)+"px",this.clampIntoView())}beginResize(r,l){const d=this.elementRef.current;if(!d)return!1;const h=this.measureNatural();if(!h)return!1;const b=window.getComputedStyle(d);let S=parseFloat(b.left),y=parseFloat(b.top);isFinite(S)||(S=d.offsetLeft),isFinite(y)||(y=d.offsetTop);const E=this.screenFit||1,$=this.isMirroredGrip(),M=$?"top right":"top left";!$&&this.transformOrigin()==="top right"&&(S+=h.width*(1-E)),d.style.left=S+"px",d.style.top=y+"px",d.style.right="auto",d.style.transformOrigin=M,this.resizeOrigin=M,this.positioned=!0;const N=d.getBoundingClientRect(),j=$?N.right:N.left,H=$?-1:1;return this.resizeStart={originX:j,originY:N.top,flipX:H,width:h.width,height:h.height,scale:E,maxScale:$?j/h.width:1/0,base:zE(h.width,h.height,H*(r-j),l-N.top)},!0}moveResize(r,l){const d=this.resizeStart;if(!d)return;const h=zE(d.width,d.height,d.flipX*(r-d.originX),l-d.originY),b=$0(Math.min(d.maxScale,d.scale+(h-d.base))),S=this.side(),y=Math.round(b/(this.autoFit||1)*100)/100;y!==ym(S)&&(NE(S,y),this.applyScreenFit())}moveGesture(r,l){this.pressPoint&&(Math.abs(r-this.pressPoint.x)>LE||Math.abs(l-this.pressPoint.y)>LE)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(r,l):this.moveDrag(r,l)}finishGesture(){const r=this.elementRef.current,l=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),l){const d=this.side();PE(d,ym(d)),this.keepGripOnScreen(),this.clampIntoView()}r&&(jE[this.side()]={top:parseFloat(r.style.top)||0,left:parseFloat(r.style.left)||0})}clampIntoView(r){const l=this.elementRef.current;if(!l||!this.positioned)return;const d=l.getBoundingClientRect(),h=document.documentElement.clientWidth||window.innerWidth,b=window.innerHeight||document.documentElement.clientHeight;let S=0,y=0;d.top<0?y=-d.top:d.top>b-bd&&(y=b-bd-d.top),r&&d.width<=h&&d.left<0?S=-d.left:d.right<bd?S=bd-d.right:d.left>h-bd&&(S=h-bd-d.left),!(!S&&!y)&&(l.style.left=(parseFloat(l.style.left)||0)+S+"px",l.style.top=(parseFloat(l.style.top)||0)+y+"px")}keepGripOnScreen(){const r=this.elementRef.current;if(!r)return;const l=r.getBoundingClientRect(),d=document.documentElement.clientWidth||window.innerWidth;let h;if(this.isMirroredGrip()){if(l.left>=0||(h=Math.min(-l.left,d-l.right),h<=0))return}else if(h=d-l.right,h>=0)return;const b=window.getComputedStyle(r);let S=parseFloat(b.left);isFinite(S)||(S=r.offsetLeft),r.style.left=S+h+"px",r.style.right="auto",this.positioned=!0}resetUserScale(){const r=this.side();ym(r)!==1&&(NE(r,1),PE(r,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(r){r.stopPropagation(),this.resetUserScale()}onDragStart(r){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] pointerdown",r.pointerType,"handle:",this.isDragHandle(r.target),"grip:",this.isResizeHandle(r.target)),r.pointerType==="touch"||r.button!=null&&r.button>0)return;const l=this.isResizeHandle(r.target);if(!l&&!this.isDragHandle(r.target))return;if(this.notePress(l?"grip":"header",r.clientX,r.clientY),!(l?this.beginResize(r.clientX,r.clientY):this.beginDrag(r.clientX,r.clientY))){this.cancelDoublePress();return}const d=this.elementRef.current;let h=!1;try{d.setPointerCapture(r.pointerId),h=!0}catch{}this.dragTarget=h?d:document,this.dragTarget.addEventListener("pointermove",this.onDragMove),this.dragTarget.addEventListener("pointerup",this.onDragEnd),this.dragTarget.addEventListener("pointercancel",this.onDragEnd),this.dragPointerId=r.pointerId,r.preventDefault()}onDragMove(r){!this.gestureActive()||r.pointerId!==this.dragPointerId||this.moveGesture(r.clientX,r.clientY)}onDragEnd(r){!this.gestureActive()||r&&r.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(r){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] touchstart",r.touches&&r.touches.length,"handle:",this.isDragHandle(r.target),"grip:",this.isResizeHandle(r.target)),this.gestureActive()||!r.touches||r.touches.length!==1)return;const l=r.touches[0],d=this.isResizeHandle(r.target);if(!(!d&&!this.isDragHandle(r.target)&&!this.isDragSlop(r.target,l.clientY))){if(this.notePress(d?"grip":"header",l.clientX,l.clientY),!(d?this.beginResize(l.clientX,l.clientY):this.beginDrag(l.clientX,l.clientY))){this.cancelDoublePress();return}this.touchDragId=l.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.addEventListener("touchend",this.onTouchDragEnd),document.addEventListener("touchcancel",this.onTouchDragEnd),r.cancelable&&r.preventDefault()}}onTouchDragMove(r){const l=FE(r.touches,this.touchDragId);if(!this.gestureActive()||!l)return;this.moveGesture(l.clientX,l.clientY);const d=this.dragScroll;d&&((window.scrollX||window.pageXOffset||0)!==d.x||(window.scrollY||window.pageYOffset||0)!==d.y)&&window.scrollTo(d.x,d.y),r.cancelable&&r.preventDefault()}onTouchDragEnd(r){r&&r.touches&&FE(r.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.removeEventListener("touchend",this.onTouchDragEnd),document.removeEventListener("touchcancel",this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){const r=this.dragTarget;r&&(r.removeEventListener("pointermove",this.onDragMove),r.removeEventListener("pointerup",this.onDragEnd),r.removeEventListener("pointercancel",this.onDragEnd),this.dragPointerId!=null&&r.hasPointerCapture&&r.hasPointerCapture(this.dragPointerId)&&r.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(r){r.stopPropagation();let{ship:l}=this.props;if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Fu()){window.uiEvents.relay("CloseSystemInfo");return}window.uiEvents.relay("SystemClicked",{ship:l,system:l,element:r.target})}onShipTouchMove(r){if(!this.longPressTimer)return;const l=r.touches[0],d=l.clientX-this.touchStartX,h=l.clientY-this.touchStartY;(Math.abs(d)>10||Math.abs(h)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(r){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onShipTouchEnd(r){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(r){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:l}=this.props,d=shipManager.systems.getSystemByName(l,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:l,system:d||l.systems[0],element:r.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onUnknownTouchStart(r){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const l=r.currentTarget,d=r.touches[0];this.touchStartX=d.clientX,this.touchStartY=d.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:h}=this.props,b=shipManager.systems.getSystemByName(h,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:h,system:b||h.systems[0],element:l,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){const r=this.elementRef.current,l=xm(this.props.ship)?"left":"right";r.addEventListener("pointerdown",this.onDragStart),r.addEventListener("touchstart",this.onTouchDragStart,{passive:!1});const d=jE[l];if(d&&!_E()){const h=Math.max(0,Math.min(d.top,window.innerHeight-60)),b=Math.max(60-r.offsetWidth,Math.min(d.left,window.innerWidth-60));r.style.top=h+"px",r.style.left=b+"px",r.style.right="auto",this.positioned=!0}document.addEventListener("pointerdown",this.onDocumentPointerDown),this.applyScreenFit(),window.addEventListener("resize",this.onScreenResize),window.addEventListener("orientationchange",this.onScreenResize)}componentDidUpdate(){this.applyScreenFit()}componentWillUnmount(){document.removeEventListener("pointerdown",this.onDocumentPointerDown),window.removeEventListener("resize",this.onScreenResize),window.removeEventListener("orientationchange",this.onScreenResize);const r=this.elementRef.current;r&&(r.removeEventListener("pointerdown",this.onDragStart),r.removeEventListener("touchstart",this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(r){this.panelHoverTimer&&(clearTimeout(this.panelHoverTimer),this.panelHoverTimer=null),this.state.hoverPanel!==r&&this.setState({hoverPanel:r})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(r){if(!this.state.openPanel)return;const l=this.controlsRef.current,d=this.popupRef.current;l&&l.contains(r.target)||d&&d.contains(r.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay("CloseShipWindow",{ship:this.props.ship})}togglePanel(r,l){l.stopPropagation(),this.setState(d=>({openPanel:d.openPanel===r?null:r}))}toggleArt(r){r&&r.stopPropagation(),this.setState(l=>({showArt:!l.showArt,openPanel:null}))}renderResizeGrip(r){return m.jsx(vL,{className:"shipwindow-resize-grip shipwindow-nodrag",$pad:UL,$mirror:this.isMirroredGrip(),$overlap:!!r,onDoubleClick:this.onGripDoubleClick,title:"Drag to resize this window — double-click to reset its size"})}renderStatusStrip(r,l){const d=l?[{key:"rolled",text:"⟲ Rolled — port / starboard reversed"}].concat(HE(r)):HE(r);return m.jsxs(Ge.Fragment,{children:[d.map((h,b)=>m.jsx(CL,{$color:h.color,$bg:h.bg,$grip:b===d.length-1,children:h.text},h.key)),this.renderResizeGrip(d.length>0)]})}renderHeader(r,l,d){return m.jsxs(pL,{className:"shipwindow-drag-handle",title:"Drag to move — double-click to reset window size",children:[m.jsx(hL,{$tint:d,title:r,children:r}),m.jsx(gL,{title:l,children:l}),m.jsx(mL,{className:"shipwindow-nodrag",onClick:this.close.bind(this),children:"✕"})]})}renderHitChartButton(r){const{openPanel:l}=this.state;return m.jsxs(mm,{ref:this.hitChartBtnRef,$wide:r,$active:l==="hitchart",onClick:this.togglePanel.bind(this,"hitchart"),children:[m.jsx($E,{children:"⊕"}),"Hit Chart"]})}renderStatsButton(r){const{openPanel:l}=this.state;return m.jsxs(mm,{$wide:r,$active:l==="shipstats",onClick:this.togglePanel.bind(this,"shipstats"),onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[m.jsxs(kE,{children:[m.jsx("i",{}),m.jsx("i",{}),m.jsx("i",{})]}),"Ship Stats"]})}renderArtButton(r){return m.jsxs(mm,{$wide:r,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[m.jsx(xL,{}),"Ship Art"]})}renderNotesButton(r){const{openPanel:l}=this.state;return m.jsxs(mm,{$wide:r,$active:l==="notes",onClick:this.togglePanel.bind(this,"notes"),onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[m.jsx($E,{children:"✎"}),"Notes"]})}renderControls(r,l,d,h){const b=Fu(),S=IE(this.props.ship),y=UE(this.props.ship);if(!r&&!l&&!h&&!S&&!y)return null;const E=b;return m.jsxs(yL,{ref:this.controlsRef,$compact:d,children:[r&&this.renderHitChartButton(E),S&&this.renderArtButton(E),y&&this.renderStatsButton(E),l&&this.renderNotesButton(E),h&&m.jsx(ME,{ship:this.props.ship})]})}getButtonLeft(){const r=this.controlsRef.current,l=this.elementRef.current;return!r||!l?null:Math.round((r.getBoundingClientRect().left-l.getBoundingClientRect().left)/(this.screenFit||1)-l.clientLeft)}getAnchorBelow(r,l){const d=r&&r.current,h=this.elementRef.current;if(!d||!h)return{top:l,left:this.getButtonLeft()};const b=d.getBoundingClientRect(),S=h.getBoundingClientRect(),y=this.screenFit||1;return{left:Math.round((b.left-S.left)/y-h.clientLeft),top:Math.round((b.bottom-S.top)/y-h.clientTop)+4}}renderPopup(r,l,d){const{ship:h}=this.props,{openPanel:b,hoverPanel:S}=this.state,y=b||S;if(!y)return null;const E=y==="hitchart"&&Fu()?this.hitChartBtnRef:this.controlsRef,{top:$,left:M}=this.getAnchorBelow(E,d);return y==="hitchart"&&r?m.jsx(T0,{ref:this.popupRef,$top:$,$left:M,$fit:!0,children:m.jsx(eL,{ship:h})}):y==="shipstats"&&UE(h)?m.jsx(T0,{ref:this.popupRef,$top:$,$left:M,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:m.jsx(ME,{ship:h,live:!0,bare:!0})}):y==="notes"&&l?m.jsx(T0,{ref:this.popupRef,$top:$,$left:M,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:m.jsx(cE,{ship:h,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){const{ship:r}=this.props,l=Fu(),d=xm(r);var h=r.shipClass,b=r.name;let S=!1;if(r.mine){var y=shipManager.systems.getSystemByName(r,"mineStealth");y&&!y.isMineRevealed(r)&&(h="Mine",b="Mine",S=!0)}b||(b=h,h="");const E=!S&&KL(r),$=!l&&!S&&QL(r);if(r.flight){if(l)return m.jsxs(Mp,{ref:this.elementRef,onClick:$p,onContextMenu:ae=>{ae.preventDefault(),ae.stopPropagation()},$isMyTeam:d,$variant:"flightLobby",children:[this.renderHeader(b,h,O0()),m.jsxs(wL,{children:[m.jsx(SL,{children:m.jsx(x0,{ship:r})}),m.jsx(E0,{ship:r})]}),this.renderResizeGrip()]});const pe=!!(window.ew&&ew.isFlightEwPool(r));return m.jsxs(Mp,{ref:this.elementRef,onClick:$p,onContextMenu:ae=>{ae.preventDefault(),ae.stopPropagation()},$isMyTeam:d,$variant:pe?"flightEw":"flight",children:[this.renderHeader(b,h,O0()),pe?m.jsxs(dL,{children:[m.jsx(fL,{children:m.jsx(x0,{ship:r})}),m.jsx(wE,{ship:r,flight:!0})]}):m.jsx(x0,{ship:r}),this.renderStatusStrip(r)]})}if(S)return m.jsxs(Mp,{ref:this.elementRef,onClick:$p,onContextMenu:pe=>{pe.preventDefault(),pe.stopPropagation()},$isMyTeam:d,$variant:"terrain",children:[this.renderHeader(b,h,null),m.jsxs(OE,{children:[m.jsx(R0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),m.jsx(k0,{$img:window.AssetManager.getSmartImagePath(r.imagePath)}),m.jsx(EL,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:"?"})]}),this.renderResizeGrip()]});const M=ez(r),N=ZL(M);if((window.gamedata.isTerrain(r.shipSizeClass,r.userid)||r.mine)&&!BE(M)){const pe=E||$||IE(r);return m.jsxs(Mp,{ref:this.elementRef,onClick:$p,onContextMenu:ae=>{ae.preventDefault(),ae.stopPropagation()},$isMyTeam:d,$variant:"terrain",children:[this.renderHeader(b,h,null),m.jsxs(OE,{children:[m.jsx(R0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),m.jsx(k0,{$img:window.AssetManager.getSmartImagePath(r.imagePath),$art:this.state.showArt,$offsetY:r.mine&&pe?GL:0}),this.renderControls(E,$,!0),ML.map(ae=>M[ae].length>0&&m.jsx(mE,{location:ae,nameOverride:N[ae],ship:r,systems:M[ae],isTerrain:!0,hidden:this.state.showArt},`section-${r.id}-${ae}`))]}),l&&m.jsx(E0,{ship:r,full:!0}),this.renderStatusStrip(r),this.renderPopup(E,$,72)]})}const H=shipManager.movement.isRolled(r),L=!!r.enhancementTooltip,K=tz(M,L),de=r.base&&!r.smallBase,Ee=m.jsxs(bL,{$areas:K,children:[m.jsx(R0,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),m.jsx(k0,{$img:window.AssetManager.getSmartImagePath(r.imagePath),$art:this.state.showArt,$offsetY:l&&BE(M)?YL:0}),this.renderControls(E,$,!1,l&&!r.mine),l?m.jsx(E0,{ship:r,grid:!0,hideEnhancements:L}):m.jsx(wE,{ship:r}),L&&m.jsx(uL,{ship:r}),DL.map(pe=>{if(M[pe].length===0)return null;const ae=H&&AE[pe]!==void 0?AE[pe]:pe,ce=TL[ae],Te=pe===0||pe===1||pe===2||de&&$L.includes(pe);return m.jsx(mE,{location:pe,displayLocation:ae,area:ce,valign:kL[ce],justify:RL[ce],wide:Te,minHeight:void 0,nameOverride:N[pe],hidden:this.state.showArt,ship:r,systems:M[pe]},`section-${r.id}-${pe}`)})]});return m.jsxs(Mp,{ref:this.elementRef,onClick:$p,onContextMenu:pe=>{pe.preventDefault(),pe.stopPropagation()},$isMyTeam:d,$variant:"ship",children:[this.renderHeader(b,h,O0()),Ee,this.renderStatusStrip(r,H),this.renderPopup(E,$)]})}}const $p=()=>window.uiEvents.relay("CloseSystemInfo"),jE={left:null,right:null},jL="(max-width: 1024px)",_L="(orientation: portrait)",LL={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},zL={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},D0=.98,_E=()=>!!window.matchMedia&&window.matchMedia(jL).matches,NL=()=>window.matchMedia?window.matchMedia(_L).matches:window.innerHeight>=window.innerWidth,PL=(o,r)=>r===1?o:{fillW:Math.min(D0,o.fillW*r),fillH:Math.min(D0,o.fillH*r),min:Math.min(o.max,o.min*r),max:o.max},FL=()=>{const o=Fu()?zL:LL;return NL()?PL(o,o.portrait):o},BL=.35,IL=3,UL=6,HL=8,VL=400,LE=6,bd=40,M0="fv.shipwindow.userScale",$0=o=>Math.min(IL,Math.max(BL,o)),zE=(o,r,l,d)=>(l*o+d*r)/(o*o+r*r),vm={left:null,right:null},ym=o=>(vm[o]==null&&(vm[o]=WL(o)),vm[o]),NE=(o,r)=>{vm[o]=r},WL=o=>{try{let r=parseFloat(window.localStorage.getItem(M0+"."+o));if(isFinite(r)||(r=parseFloat(window.localStorage.getItem(M0))),isFinite(r)&&r>0)return $0(r)}catch{}return 1},PE=(o,r)=>{try{window.localStorage.setItem(M0+"."+o,String(r))}catch{}},FE=(o,r)=>{if(!o||r==null)return null;for(let l=0;l<o.length;l++)if(o[l].identifier===r)return o[l];return null},Fu=()=>!!window.gamedata&&window.gamedata.gamephase===-2,YL=35,BE=o=>OL.some(r=>o[r].length>0),GL=20,xm=o=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide=="function"?window.ShipWindowManager.isLeftSide(o):o.team===window.gamedata.getPlayerTeam(),KL=o=>!!o.hitChart&&Object.keys(o.hitChart).length>0,IE=o=>!!(o&&o.imagePath)&&!o.flight,UE=o=>!!o&&!Fu()&&!o.flight&&!o.mine&&!window.gamedata.isTerrain(o.shipSizeClass,o.userid),QL=o=>!!o.notes||!!o.enhancementTooltip||!!(o.hasAttached&&Object.keys(o.hasAttached).length>0),O0=o=>null,HE=o=>{if(Fu())return[];const r=[],l=shipManager.getTurnDeployed(o);l>window.gamedata.turn&&l<999&&r.push({key:"deploying",color:A.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Deploying on Turn "+l});const d=shipManager.getArrivalIniPenalty(o);d!==0&&r.push({key:"arrivalScatter",color:A.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Arrival Scatter "+d+" Ini"});const h=o.trueStealth?shipManager.getStealthToggleForecast(o):null;if(o.trueStealth)if(JL(o,h))r.push({key:"undetected",color:A.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Undetected"});else if(o.mine){const E=shipManager.systems.getSystemByName(o,"mineStealth");!E||E.isMineRevealedToOpponent(o)?r.push({key:"detected",color:A.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected - Revealed"}):r.push({key:"detected",color:A.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Detected - Not Revealed"})}else h===!0?r.push({key:"detected",color:A.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Would be Detected"}):r.push({key:"detected",color:A.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected"});shipManager.isJumpingToHyperspace(o)&&r.push({key:"jumping",color:A.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:"Jumping to Hyperspace"});const b=o.flight?null:shipManager.movement.getExtendedTurnStatus(o);b&&r.push(b.cancelled?{key:"extendedTurn",color:A.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:b.text}:{key:"extendedTurn",color:A.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:b.text});const S=window.JumpEngine&&typeof window.JumpEngine.getAbductionChain=="function"?window.JumpEngine.getAbductionChain(o.id):null;S&&r.push({key:"abducted",color:"#b36bff",bg:"rgba(127, 0, 255, 0.10)",text:"Being abducted: "+window.JumpEngine.formatAbductionHalves(S.total)+"/"+S.cost+" power-turns"});const y=shipManager.getHangarManoeuvre(o);if(y&&r.push({key:"hangarManoeuvre",color:A.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:y.text}),o.attached&&Object.keys(o.attached).length>0&&!o.detached&&!(y&&y.riding)){const E=window.gamedata.getShip(Object.keys(o.attached)[0]);if(E){const $=Object.values(o.attached)[0];let M="";$==1?M="Front":$==2?M="Aft":$==3||$==31||$==32?M="Port":($==4||$==41||$==42)&&(M="Starboard"),r.push({key:"attached",color:A.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Attached to "+E.name+(M?" ["+M+"]":"")})}}return o.hasAttached&&Object.keys(o.hasAttached).length>0&&Object.keys(o.hasAttached).filter($=>{const M=window.gamedata.getShip($);return!(M&&shipManager.isDockingRider(M))}).length>0&&r.push({key:"boarded",color:A.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Ship is being boarded!"}),o.flight&&XL(o)&&r.push({key:"edfGrounded",color:qL,bg:"rgba(210, 80, 255, 0.12)",text:"Energy Drained - cannot fire"}),r},qL="#d250ff",XL=o=>{const r=shipManager.systems.getSystem(o,1);return!r||!r.criticals?!1:r.criticals.some(l=>l.phpclass==="EdfFighterGrounded"&&l.turn+1===window.gamedata.turn)},JL=(o,r)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(o)==gamedata.turn||shipManager.getTurnDeployed(o)==gamedata.turn))return!0;if(r!=null)return!r;let l=shipManager.isDetected(o);if(!l&&o.team==gamedata.getPlayerTeam()){let d=null;o.mine?d=shipManager.systems.getSystemByName(o,"mineStealth"):o.faction=="Torvalus Speculators"?d=shipManager.systems.getSystemByName(o,"ShadingField"):shipManager.getSpecialAbilityStealth(o,"Cloaking")?d=shipManager.systems.getSystemByName(o,"CloakingDevice"):shipManager.getSpecialAbilityStealth(o,"Stealth")&&(d=shipManager.systems.getSystemByName(o,"stealth")),d&&(Array.isArray(d.detected)&&d.detected.length>0||d.detected===!0||Array.isArray(d.detectedNew)&&d.detectedNew.length>0||d.detectedNew===!0)&&(l=!0)}return!l},ZL=o=>{const r={};return[{locations:[3,31,32],name:"Port"},{locations:[4,41,42],name:"Starboard"}].forEach(l=>{const d=l.locations.filter(h=>o[h].some(b=>b.name==="structure"));d.length===1&&d[0]!==l.locations[0]&&(r[d[0]]=l.name)}),r},ez=o=>{const r={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return o.systems.forEach(l=>{l.hideInShipWindow||r[l.location].push(l)}),r},tz=(o,r)=>{const l=[["ctrl","fwd","ew"]];let d=0;if((o[31].length||o[41].length)&&(l.push(["lfwd","prim","rfwd"]),d++),(o[3].length||o[4].length)&&(l.push(["left","prim","right"]),d++),(o[32].length||o[42].length)&&(l.push(["laft","prim","raft"]),d++),d===0&&o[0].length&&l.push([null,"prim",null]),o[2].length&&l.push([null,"aft",null]),r){const h=l[l.length-1];l.length>1&&h[2]===null?h[2]="enh":l.push([null,null,"enh"])}for(let h=1;h<l.length&&l[h][0]===null;h++)l[h][0]="ctrl";for(let h=1;h<l.length&&l[h][2]===null;h++)l[h][2]="ew";return l.map(h=>`"${h[0]||"."}  ${h[1]||"."}  ${h[2]||"."}"`).join(" ")},nz=D.div`

`,rz=D.div`
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
    border: 1px solid ${A.colors.warning};
    background-color: ${A.colors.windowBg};
    color: ${A.colors.text};
    font-family: ${A.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`,iz=D.span`
    cursor: pointer;
    color: ${A.colors.line};
    font-size: 16px;

    &:hover {
        color: ${A.colors.text};
    }
`;class az extends Ge.Component{constructor(r){super(r),this.state={error:null}}static getDerivedStateFromError(r){return{error:r}}componentDidCatch(r,l){const d=this.props.ship;console.error("Ship window render failed for",d&&(d.name||d.shipClass),r,l)}render(){if(this.state.error){const r=this.props.ship;return m.jsxs(rz,{children:[m.jsxs("span",{children:[r&&(r.name||r.shipClass)||"Ship"," — window failed to render (see console)"]}),m.jsx(iz,{onClick:()=>window.uiEvents.relay("CloseShipWindow",{ship:r}),children:"✕"})]})}return this.props.children}}class oz extends Ge.Component{render(){const{ships:r}=this.props;return m.jsx(nz,{children:r.map(l=>m.jsx(az,{ship:l,children:m.jsx(AL,{ship:l})},`shipwindow-${l.userid}-${l.id}`))})}}const lz=D.div`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.$position).reduce((r,l)=>r+`
`+l+":"+o.$position[l]+"px;","")}
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
    background: ${Ue.bg};
    border: 1px solid ${Ue.line};
    border-radius: ${Ue.radius};
    box-shadow: ${Ue.shadow};
`;D.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${Ue.dim};
    user-select: none;
`;const sz=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${Ue.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,uz=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,cz=D.span`
    flex: 0 0 auto;
    color: ${Ue.dim};
    font-size: 10px;
    user-select: none;
`,VE=D.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${Ue.btnBg};
    border: 1px solid ${Ue.line};
    border-radius: 2px;
    color: ${Ue.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${Ue.line};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${Ue.btnBg}; color: ${Ue.btnText}; }
    `}
`,dz=D.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${A.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${Ue.well};
    border: 1px solid ${Ue.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${Ue.focus}; }
`,fz=D.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${Ue.btnBg};
    border: 1px solid ${Ue.line};
    border-radius: 2px;
    color: ${Ue.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${A.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${Ue.line}; color: #ffffff; }
`,pz=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r};class hz extends Ge.Component{constructor(r){super(r),this.wheelRefs={}}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=Cp(l=>this.step(r,l.deltaY<0?1:-1))),this.wheelRefs[r]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(r,l){const{ship:d}=this.props,h=this.maxHealth();let b=parseInt(l,10);isNaN(b)&&(b=h),b=Math.max(1,Math.min(h,b)),battleDamage.setFighter(d,r,{d:h-b}),this.refresh()}step(r,l){this.setRemaining(r,battleDamage.fighterHealth(this.props.ship,r)+l)}onInput(r,l){const d=String(l.target.value).replace(/[^0-9]/g,"");this.setRemaining(r,d===""?0:parseInt(d,10))}propagate(){const{ship:r}=this.props,l=parseInt(r.flightSize,10)||0,d=battleDamage.fighterMaxHealth(r)-battleDamage.fighterHealth(r,1);for(let h=2;h<=l;h++)battleDamage.setFighter(r,h,{d});this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate()}render(){const{ship:r,boundingBox:l}=this.props,d=parseInt(r&&r.flightSize,10)||0,h=this.maxHealth();if(!d||!h)return null;battleDamage.flightSummary(r);const b=[];for(let E=1;E<=d;E++){const $=battleDamage.fighterHealth(r,E);b.push(m.jsxs(sz,{children:[m.jsxs(uz,{children:["Fighter ",E]}),m.jsx(VE,{title:"More damage",disabled:$<=1,onClick:()=>this.step(E,-1),children:"−"}),m.jsx(dz,{ref:this.wheelRef(E),type:"text",value:$,onChange:M=>this.onInput(E,M)}),m.jsx(VE,{title:"Repair",disabled:$>=h,onClick:()=>this.step(E,1),children:"+"}),m.jsxs(cz,{children:["/ ",h]})]},`ftr-${E}`))}const S=battleDamage.flightCritEntry(r)||{},y=FC(S.c,r.preBattleCritDesc,r.preBattleCritTransient,S.p);return m.jsxs(lz,{$position:pz(l),onClick:E=>E.stopPropagation(),children:[m.jsx(NC,{$sticky:!0,children:"Fighter Damage"}),b,d>1&&m.jsx(fz,{title:"Copy Fighter 1's damage to every fighter in this flight",onClick:()=>this.propagate(),children:"Apply Fighter 1's damage to all"}),m.jsx(BC,{ship:r,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:y,editable:!0,onChange:()=>this.refresh()})]})}}const gz=D.div`
    position: absolute;
    z-index: 20000;
    ${o=>Object.keys(o.$position).reduce((r,l)=>r+`
`+l+":"+o.$position[l]+"px;","")}
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
    background: ${Ue.bg};
    border: 1px solid ${Ue.line};
    border-radius: ${Ue.radius};
    box-shadow: ${Ue.shadow};
`,mz=D.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${Ue.dim};
    user-select: none;
`,vz=D.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${Ue.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,yz=D.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,xz=D.span`
    flex: 0 0 auto;
    color: ${Ue.dim};
    font-size: 10px;
    user-select: none;
`,WE=D.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${Ue.btnBg};
    border: 1px solid ${Ue.line};
    border-radius: 2px;
    color: ${Ue.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${Ue.line};
        color: #ffffff;
        opacity: 1;
    }

    ${o=>o.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${Ue.btnBg}; color: ${Ue.btnText}; }
    `}
`,bz=D.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${A.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${Ue.well};
    border: 1px solid ${Ue.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${Ue.focus}; }
`,wz=D.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${Ue.btnBg};
    border: 1px solid ${Ue.line};
    border-radius: 2px;
    color: ${Ue.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${A.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${Ue.line}; color: #ffffff; }
`,Sz=o=>{const r={};return o.top>window.innerHeight/2?r.bottom=window.innerHeight-o.top:r.top=o.top+o.height,o.left>window.innerWidth/2?r.right=window.innerWidth-o.right:r.left=o.left,r};class Cz extends Ge.Component{constructor(r){super(r),this.wheelRefs={}}wheelRef(r){return this.wheelRefs[r]||(this.wheelRefs[r]=Cp(l=>this.step(r,l.deltaY<0?1:-1))),this.wheelRefs[r]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(r,l){const{ship:d}=this.props,h=this.maxHealth();let b=parseInt(l,10);isNaN(b)&&(b=h),b=Math.max(1,Math.min(h,b)),battleDamage.setMine(d,r,{d:h-b}),this.refresh()}step(r,l){this.setRemaining(r,battleDamage.mineHealth(this.props.ship,r)+l)}onInput(r,l){const d=String(l.target.value).replace(/[^0-9]/g,"");this.setRemaining(r,d===""?0:parseInt(d,10))}propagate(){const{ship:r}=this.props,l=battleDamage.mineCount(r),d=battleDamage.getEntry(r,battleDamage.KIND_MINE,1);for(let h=2;h<=l;h++)battleDamage.setWholeEntry(r,battleDamage.KIND_MINE,h,d);this.refresh()}refresh(){const{ship:r}=this.props;battleDamage.applyToShip(r),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(r),this.forceUpdate()}render(){const{ship:r,boundingBox:l}=this.props,d=battleDamage.mineCount(r),h=this.maxHealth();if(!d||h<2)return null;const b=battleDamage.mineSummary(r),S=[];for(let y=1;y<=d;y++){const E=battleDamage.mineHealth(r,y);S.push(m.jsxs(vz,{children:[m.jsxs(yz,{children:["Mine ",y]}),m.jsx(WE,{title:"More damage",disabled:E<=1,onClick:()=>this.step(y,-1),children:"−"}),m.jsx(bz,{ref:this.wheelRef(y),type:"text",value:E,onChange:$=>this.onInput(y,$)}),m.jsx(WE,{title:"Repair",disabled:E>=h,onClick:()=>this.step(y,1),children:"+"}),m.jsxs(xz,{children:["/ ",h]})]},`mne-${y}`))}return m.jsxs(gz,{$position:Sz(l),onClick:y=>y.stopPropagation(),children:[m.jsx(NC,{$sticky:!0,children:"Mine Damage"}),m.jsxs(mz,{children:[b.remaining," / ",b.total," structure"]}),S,d>1&&m.jsx(wz,{title:"Copy Mine 1's damage to every mine in this purchase",onClick:()=>this.propagate(),children:"Apply Mine 1 to all"})]})}}class Ez{constructor(r){this.parentElement=r,this.roots=new Map}getRoot(r){const l=jQuery(r,this.parentElement)[0];return l?(this.roots.has(l)||this.roots.set(l,mx(l)),this.roots.get(l)):null}unmountRoot(r){const l=jQuery(r,this.parentElement)[0];l&&this.roots.has(l)&&(this.roots.get(l).unmount(),this.roots.delete(l))}EwButtons(r){const l=this.getRoot("#showEwButtons");l&&l.render(m.jsx(Y$,{...r}))}FullScreen(r){const l=this.getRoot("#fullScreen");l&&l.render(m.jsx(P$,{...r}))}Surrender(r){const l=this.getRoot("#surrender");l&&l.render(m.jsx(B$,{...r}))}BackToLobby(r){const l=this.getRoot("#backToLobby");l&&l.render(m.jsx(V$,{...r}))}PlayerSettings(r){const l=this.getRoot("#playerSettings");l&&l.render(m.jsx(u$,{...r}))}showShipThrustUI(r){const l=this.getRoot("#shipThrust");l&&l.render(m.jsx(_$,{...r}))}hideShipThrustUI(){this.unmountRoot("#shipThrust")}showWeaponList(r){const l=this.getRoot("#weaponList");l&&l.render(m.jsx(r_,{...r}))}hideWeaponList(){this.unmountRoot("#weaponList")}showSystemInfo(r){const l=this.getRoot("#systemInfoReact");l&&l.render(m.jsx(l_,{...r}))}hideSystemInfo(){this.unmountRoot("#systemInfoReact")}showSystemInfoMenu(r){const l=this.getRoot("#systemInfoReact");l&&l.render(m.jsx(p_,{...r}))}hideSystemInfoMenu(){this.unmountRoot("#systemInfoReact")}canShowSystemInfoMenu(r,l){return l0(r,l)}showFighterDamageMenu(r){const l=this.getRoot("#systemInfoReact");l&&l.render(m.jsx(hz,{...r}))}showMineDamageMenu(r){const l=this.getRoot("#systemInfoReact");l&&l.render(m.jsx(Cz,{...r}))}renderShipWindows(r){const l=this.getRoot("#shipWindowsReact");l&&l.render(m.jsx(oz,{...r}))}}window.UIManager=Ez});
