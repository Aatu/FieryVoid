(function(Ni){typeof define=="function"&&define.amd?define(Ni):Ni()})(function(){"use strict";function Ni(t){return t&&t.__esModule&&Object.prototype.hasOwnProperty.call(t,"default")?t.default:t}var Sd={exports:{}},Fi={},kd={exports:{}},B={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var Dr=Symbol.for("react.element"),Mm=Symbol.for("react.portal"),jm=Symbol.for("react.fragment"),Em=Symbol.for("react.strict_mode"),Dm=Symbol.for("react.profiler"),Pm=Symbol.for("react.provider"),Am=Symbol.for("react.context"),Rm=Symbol.for("react.forward_ref"),Im=Symbol.for("react.suspense"),Om=Symbol.for("react.memo"),Lm=Symbol.for("react.lazy"),$d=Symbol.iterator;function zm(t){return t===null||typeof t!="object"?null:(t=$d&&t[$d]||t["@@iterator"],typeof t=="function"?t:null)}var Cd={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},Td=Object.assign,Md={};function zn(t,e,n){this.props=t,this.context=e,this.refs=Md,this.updater=n||Cd}zn.prototype.isReactComponent={},zn.prototype.setState=function(t,e){if(typeof t!="object"&&typeof t!="function"&&t!=null)throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,t,e,"setState")},zn.prototype.forceUpdate=function(t){this.updater.enqueueForceUpdate(this,t,"forceUpdate")};function jd(){}jd.prototype=zn.prototype;function Us(t,e,n){this.props=t,this.context=e,this.refs=Md,this.updater=n||Cd}var Gs=Us.prototype=new jd;Gs.constructor=Us,Td(Gs,zn.prototype),Gs.isPureReactComponent=!0;var Ed=Array.isArray,Dd=Object.prototype.hasOwnProperty,Vs={current:null},Pd={key:!0,ref:!0,__self:!0,__source:!0};function Ad(t,e,n){var r,i={},o=null,s=null;if(e!=null)for(r in e.ref!==void 0&&(s=e.ref),e.key!==void 0&&(o=""+e.key),e)Dd.call(e,r)&&!Pd.hasOwnProperty(r)&&(i[r]=e[r]);var l=arguments.length-2;if(l===1)i.children=n;else if(1<l){for(var c=Array(l),d=0;d<l;d++)c[d]=arguments[d+2];i.children=c}if(t&&t.defaultProps)for(r in l=t.defaultProps,l)i[r]===void 0&&(i[r]=l[r]);return{$$typeof:Dr,type:t,key:o,ref:s,props:i,_owner:Vs.current}}function Bm(t,e){return{$$typeof:Dr,type:t.type,key:e,ref:t.ref,props:t.props,_owner:t._owner}}function Ys(t){return typeof t=="object"&&t!==null&&t.$$typeof===Dr}function Nm(t){var e={"=":"=0",":":"=2"};return"$"+t.replace(/[=:]/g,function(n){return e[n]})}var Rd=/\/+/g;function Ks(t,e){return typeof t=="object"&&t!==null&&t.key!=null?Nm(""+t.key):e.toString(36)}function _i(t,e,n,r,i){var o=typeof t;(o==="undefined"||o==="boolean")&&(t=null);var s=!1;if(t===null)s=!0;else switch(o){case"string":case"number":s=!0;break;case"object":switch(t.$$typeof){case Dr:case Mm:s=!0}}if(s)return s=t,i=i(s),t=r===""?"."+Ks(s,0):r,Ed(i)?(n="",t!=null&&(n=t.replace(Rd,"$&/")+"/"),_i(i,e,n,"",function(d){return d})):i!=null&&(Ys(i)&&(i=Bm(i,n+(!i.key||s&&s.key===i.key?"":(""+i.key).replace(Rd,"$&/")+"/")+t)),e.push(i)),1;if(s=0,r=r===""?".":r+":",Ed(t))for(var l=0;l<t.length;l++){o=t[l];var c=r+Ks(o,l);s+=_i(o,e,n,c,i)}else if(c=zm(t),typeof c=="function")for(t=c.call(t),l=0;!(o=t.next()).done;)o=o.value,c=r+Ks(o,l++),s+=_i(o,e,n,c,i);else if(o==="object")throw e=String(t),Error("Objects are not valid as a React child (found: "+(e==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":e)+"). If you meant to render a collection of children, use an array instead.");return s}function Hi(t,e,n){if(t==null)return t;var r=[],i=0;return _i(t,r,"","",function(o){return e.call(n,o,i++)}),r}function Fm(t){if(t._status===-1){var e=t._result;e=e(),e.then(function(n){(t._status===0||t._status===-1)&&(t._status=1,t._result=n)},function(n){(t._status===0||t._status===-1)&&(t._status=2,t._result=n)}),t._status===-1&&(t._status=0,t._result=e)}if(t._status===1)return t._result.default;throw t._result}var ke={current:null},Wi={transition:null},_m={ReactCurrentDispatcher:ke,ReactCurrentBatchConfig:Wi,ReactCurrentOwner:Vs};function Id(){throw Error("act(...) is not supported in production builds of React.")}B.Children={map:Hi,forEach:function(t,e,n){Hi(t,function(){e.apply(this,arguments)},n)},count:function(t){var e=0;return Hi(t,function(){e++}),e},toArray:function(t){return Hi(t,function(e){return e})||[]},only:function(t){if(!Ys(t))throw Error("React.Children.only expected to receive a single React element child.");return t}},B.Component=zn,B.Fragment=jm,B.Profiler=Dm,B.PureComponent=Us,B.StrictMode=Em,B.Suspense=Im,B.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=_m,B.act=Id,B.cloneElement=function(t,e,n){if(t==null)throw Error("React.cloneElement(...): The argument must be a React element, but you passed "+t+".");var r=Td({},t.props),i=t.key,o=t.ref,s=t._owner;if(e!=null){if(e.ref!==void 0&&(o=e.ref,s=Vs.current),e.key!==void 0&&(i=""+e.key),t.type&&t.type.defaultProps)var l=t.type.defaultProps;for(c in e)Dd.call(e,c)&&!Pd.hasOwnProperty(c)&&(r[c]=e[c]===void 0&&l!==void 0?l[c]:e[c])}var c=arguments.length-2;if(c===1)r.children=n;else if(1<c){l=Array(c);for(var d=0;d<c;d++)l[d]=arguments[d+2];r.children=l}return{$$typeof:Dr,type:t.type,key:i,ref:o,props:r,_owner:s}},B.createContext=function(t){return t={$$typeof:Am,_currentValue:t,_currentValue2:t,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null},t.Provider={$$typeof:Pm,_context:t},t.Consumer=t},B.createElement=Ad,B.createFactory=function(t){var e=Ad.bind(null,t);return e.type=t,e},B.createRef=function(){return{current:null}},B.forwardRef=function(t){return{$$typeof:Rm,render:t}},B.isValidElement=Ys,B.lazy=function(t){return{$$typeof:Lm,_payload:{_status:-1,_result:t},_init:Fm}},B.memo=function(t,e){return{$$typeof:Om,type:t,compare:e===void 0?null:e}},B.startTransition=function(t){var e=Wi.transition;Wi.transition={};try{t()}finally{Wi.transition=e}},B.unstable_act=Id,B.useCallback=function(t,e){return ke.current.useCallback(t,e)},B.useContext=function(t){return ke.current.useContext(t)},B.useDebugValue=function(){},B.useDeferredValue=function(t){return ke.current.useDeferredValue(t)},B.useEffect=function(t,e){return ke.current.useEffect(t,e)},B.useId=function(){return ke.current.useId()},B.useImperativeHandle=function(t,e,n){return ke.current.useImperativeHandle(t,e,n)},B.useInsertionEffect=function(t,e){return ke.current.useInsertionEffect(t,e)},B.useLayoutEffect=function(t,e){return ke.current.useLayoutEffect(t,e)},B.useMemo=function(t,e){return ke.current.useMemo(t,e)},B.useReducer=function(t,e,n){return ke.current.useReducer(t,e,n)},B.useRef=function(t){return ke.current.useRef(t)},B.useState=function(t){return ke.current.useState(t)},B.useSyncExternalStore=function(t,e,n){return ke.current.useSyncExternalStore(t,e,n)},B.useTransition=function(){return ke.current.useTransition()},B.version="18.3.1",kd.exports=B;var R=kd.exports;const oe=Ni(R);/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var Hm=R,Wm=Symbol.for("react.element"),Um=Symbol.for("react.fragment"),Gm=Object.prototype.hasOwnProperty,Vm=Hm.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,Ym={key:!0,ref:!0,__self:!0,__source:!0};function Od(t,e,n){var r,i={},o=null,s=null;n!==void 0&&(o=""+n),e.key!==void 0&&(o=""+e.key),e.ref!==void 0&&(s=e.ref);for(r in e)Gm.call(e,r)&&!Ym.hasOwnProperty(r)&&(i[r]=e[r]);if(t&&t.defaultProps)for(r in e=t.defaultProps,e)i[r]===void 0&&(i[r]=e[r]);return{$$typeof:Wm,type:t,key:o,ref:s,props:i,_owner:Vm.current}}Fi.Fragment=Um,Fi.jsx=Od,Fi.jsxs=Od,Sd.exports=Fi;var a=Sd.exports,Ld={exports:{}},Le={},zd={exports:{}},Bd={};/**
 * @license React
 * scheduler.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */(function(t){function e(E,I){var O=E.length;E.push(I);e:for(;0<O;){var W=O-1>>>1,U=E[W];if(0<i(U,I))E[W]=I,E[O]=U,O=W;else break e}}function n(E){return E.length===0?null:E[0]}function r(E){if(E.length===0)return null;var I=E[0],O=E.pop();if(O!==I){E[0]=O;e:for(var W=0,U=E.length,On=U>>>1;W<On;){var dt=2*(W+1)-1,Mr=E[dt],ut=dt+1,Ln=E[ut];if(0>i(Mr,O))ut<U&&0>i(Ln,Mr)?(E[W]=Ln,E[ut]=O,W=ut):(E[W]=Mr,E[dt]=O,W=dt);else if(ut<U&&0>i(Ln,O))E[W]=Ln,E[ut]=O,W=ut;else break e}}return I}function i(E,I){var O=E.sortIndex-I.sortIndex;return O!==0?O:E.id-I.id}if(typeof performance=="object"&&typeof performance.now=="function"){var o=performance;t.unstable_now=function(){return o.now()}}else{var s=Date,l=s.now();t.unstable_now=function(){return s.now()-l}}var c=[],d=[],u=1,f=null,g=3,v=!1,x=!1,b=!1,M=typeof setTimeout=="function"?setTimeout:null,y=typeof clearTimeout=="function"?clearTimeout:null,p=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function w(E){for(var I=n(d);I!==null;){if(I.callback===null)r(d);else if(I.startTime<=E)r(d),I.sortIndex=I.expirationTime,e(c,I);else break;I=n(d)}}function S(E){if(b=!1,w(E),!x)if(n(c)!==null)x=!0,zi(C);else{var I=n(d);I!==null&&In(S,I.startTime-E)}}function C(E,I){x=!1,b&&(b=!1,y(j),j=-1),v=!0;var O=g;try{for(w(I),f=n(c);f!==null&&(!(f.expirationTime>I)||E&&!V());){var W=f.callback;if(typeof W=="function"){f.callback=null,g=f.priorityLevel;var U=W(f.expirationTime<=I);I=t.unstable_now(),typeof U=="function"?f.callback=U:f===n(c)&&r(c),w(I)}else r(c);f=n(c)}if(f!==null)var On=!0;else{var dt=n(d);dt!==null&&In(S,dt.startTime-I),On=!1}return On}finally{f=null,g=O,v=!1}}var $=!1,k=null,j=-1,L=5,P=-1;function V(){return!(t.unstable_now()-P<L)}function de(){if(k!==null){var E=t.unstable_now();P=E;var I=!0;try{I=k(!0,E)}finally{I?me():($=!1,k=null)}}else $=!1}var me;if(typeof p=="function")me=function(){p(de)};else if(typeof MessageChannel<"u"){var ct=new MessageChannel,vd=ct.port2;ct.port1.onmessage=de,me=function(){vd.postMessage(null)}}else me=function(){M(de,0)};function zi(E){k=E,$||($=!0,me())}function In(E,I){j=M(function(){E(t.unstable_now())},I)}t.unstable_IdlePriority=5,t.unstable_ImmediatePriority=1,t.unstable_LowPriority=4,t.unstable_NormalPriority=3,t.unstable_Profiling=null,t.unstable_UserBlockingPriority=2,t.unstable_cancelCallback=function(E){E.callback=null},t.unstable_continueExecution=function(){x||v||(x=!0,zi(C))},t.unstable_forceFrameRate=function(E){0>E||125<E?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):L=0<E?Math.floor(1e3/E):5},t.unstable_getCurrentPriorityLevel=function(){return g},t.unstable_getFirstCallbackNode=function(){return n(c)},t.unstable_next=function(E){switch(g){case 1:case 2:case 3:var I=3;break;default:I=g}var O=g;g=I;try{return E()}finally{g=O}},t.unstable_pauseExecution=function(){},t.unstable_requestPaint=function(){},t.unstable_runWithPriority=function(E,I){switch(E){case 1:case 2:case 3:case 4:case 5:break;default:E=3}var O=g;g=E;try{return I()}finally{g=O}},t.unstable_scheduleCallback=function(E,I,O){var W=t.unstable_now();switch(typeof O=="object"&&O!==null?(O=O.delay,O=typeof O=="number"&&0<O?W+O:W):O=W,E){case 1:var U=-1;break;case 2:U=250;break;case 5:U=1073741823;break;case 4:U=1e4;break;default:U=5e3}return U=O+U,E={id:u++,callback:I,priorityLevel:E,startTime:O,expirationTime:U,sortIndex:-1},O>W?(E.sortIndex=O,e(d,E),n(c)===null&&E===n(d)&&(b?(y(j),j=-1):b=!0,In(S,O-W))):(E.sortIndex=U,e(c,E),x||v||(x=!0,zi(C))),E},t.unstable_shouldYield=V,t.unstable_wrapCallback=function(E){var I=g;return function(){var O=g;g=I;try{return E.apply(this,arguments)}finally{g=O}}}})(Bd),zd.exports=Bd;var Km=zd.exports;/**
 * @license React
 * react-dom.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var Qm=R,ze=Km;function T(t){for(var e="https://reactjs.org/docs/error-decoder.html?invariant="+t,n=1;n<arguments.length;n++)e+="&args[]="+encodeURIComponent(arguments[n]);return"Minified React error #"+t+"; visit "+e+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var Nd=new Set,Pr={};function ln(t,e){Bn(t,e),Bn(t+"Capture",e)}function Bn(t,e){for(Pr[t]=e,t=0;t<e.length;t++)Nd.add(e[t])}var yt=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),Qs=Object.prototype.hasOwnProperty,Xm=/^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,Fd={},_d={};function Zm(t){return Qs.call(_d,t)?!0:Qs.call(Fd,t)?!1:Xm.test(t)?_d[t]=!0:(Fd[t]=!0,!1)}function qm(t,e,n,r){if(n!==null&&n.type===0)return!1;switch(typeof e){case"function":case"symbol":return!0;case"boolean":return r?!1:n!==null?!n.acceptsBooleans:(t=t.toLowerCase().slice(0,5),t!=="data-"&&t!=="aria-");default:return!1}}function Jm(t,e,n,r){if(e===null||typeof e>"u"||qm(t,e,n,r))return!0;if(r)return!1;if(n!==null)switch(n.type){case 3:return!e;case 4:return e===!1;case 5:return isNaN(e);case 6:return isNaN(e)||1>e}return!1}function $e(t,e,n,r,i,o,s){this.acceptsBooleans=e===2||e===3||e===4,this.attributeName=r,this.attributeNamespace=i,this.mustUseProperty=n,this.propertyName=t,this.type=e,this.sanitizeURL=o,this.removeEmptyString=s}var pe={};"children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(t){pe[t]=new $e(t,0,!1,t,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(t){var e=t[0];pe[e]=new $e(e,1,!1,t[1],null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(t){pe[t]=new $e(t,2,!1,t.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(t){pe[t]=new $e(t,2,!1,t,null,!1,!1)}),"allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(t){pe[t]=new $e(t,3,!1,t.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(t){pe[t]=new $e(t,3,!0,t,null,!1,!1)}),["capture","download"].forEach(function(t){pe[t]=new $e(t,4,!1,t,null,!1,!1)}),["cols","rows","size","span"].forEach(function(t){pe[t]=new $e(t,6,!1,t,null,!1,!1)}),["rowSpan","start"].forEach(function(t){pe[t]=new $e(t,5,!1,t.toLowerCase(),null,!1,!1)});var Xs=/[\-:]([a-z])/g;function Zs(t){return t[1].toUpperCase()}"accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(t){var e=t.replace(Xs,Zs);pe[e]=new $e(e,1,!1,t,null,!1,!1)}),"xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(t){var e=t.replace(Xs,Zs);pe[e]=new $e(e,1,!1,t,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(t){var e=t.replace(Xs,Zs);pe[e]=new $e(e,1,!1,t,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(t){pe[t]=new $e(t,1,!1,t.toLowerCase(),null,!1,!1)}),pe.xlinkHref=new $e("xlinkHref",1,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(t){pe[t]=new $e(t,1,!1,t.toLowerCase(),null,!0,!0)});function qs(t,e,n,r){var i=pe.hasOwnProperty(e)?pe[e]:null;(i!==null?i.type!==0:r||!(2<e.length)||e[0]!=="o"&&e[0]!=="O"||e[1]!=="n"&&e[1]!=="N")&&(Jm(e,n,i,r)&&(n=null),r||i===null?Zm(e)&&(n===null?t.removeAttribute(e):t.setAttribute(e,""+n)):i.mustUseProperty?t[i.propertyName]=n===null?i.type===3?!1:"":n:(e=i.attributeName,r=i.attributeNamespace,n===null?t.removeAttribute(e):(i=i.type,n=i===3||i===4&&n===!0?"":""+n,r?t.setAttributeNS(r,e,n):t.setAttribute(e,n))))}var wt=Qm.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,Ui=Symbol.for("react.element"),Nn=Symbol.for("react.portal"),Fn=Symbol.for("react.fragment"),Js=Symbol.for("react.strict_mode"),ea=Symbol.for("react.profiler"),Hd=Symbol.for("react.provider"),Wd=Symbol.for("react.context"),ta=Symbol.for("react.forward_ref"),na=Symbol.for("react.suspense"),ra=Symbol.for("react.suspense_list"),ia=Symbol.for("react.memo"),Lt=Symbol.for("react.lazy"),Ud=Symbol.for("react.offscreen"),Gd=Symbol.iterator;function Ar(t){return t===null||typeof t!="object"?null:(t=Gd&&t[Gd]||t["@@iterator"],typeof t=="function"?t:null)}var Z=Object.assign,oa;function Rr(t){if(oa===void 0)try{throw Error()}catch(n){var e=n.stack.trim().match(/\n( *(at )?)/);oa=e&&e[1]||""}return`
`+oa+t}var sa=!1;function aa(t,e){if(!t||sa)return"";sa=!0;var n=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{if(e)if(e=function(){throw Error()},Object.defineProperty(e.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(e,[])}catch(d){var r=d}Reflect.construct(t,[],e)}else{try{e.call()}catch(d){r=d}t.call(e.prototype)}else{try{throw Error()}catch(d){r=d}t()}}catch(d){if(d&&r&&typeof d.stack=="string"){for(var i=d.stack.split(`
`),o=r.stack.split(`
`),s=i.length-1,l=o.length-1;1<=s&&0<=l&&i[s]!==o[l];)l--;for(;1<=s&&0<=l;s--,l--)if(i[s]!==o[l]){if(s!==1||l!==1)do if(s--,l--,0>l||i[s]!==o[l]){var c=`
`+i[s].replace(" at new "," at ");return t.displayName&&c.includes("<anonymous>")&&(c=c.replace("<anonymous>",t.displayName)),c}while(1<=s&&0<=l);break}}}finally{sa=!1,Error.prepareStackTrace=n}return(t=t?t.displayName||t.name:"")?Rr(t):""}function ex(t){switch(t.tag){case 5:return Rr(t.type);case 16:return Rr("Lazy");case 13:return Rr("Suspense");case 19:return Rr("SuspenseList");case 0:case 2:case 15:return t=aa(t.type,!1),t;case 11:return t=aa(t.type.render,!1),t;case 1:return t=aa(t.type,!0),t;default:return""}}function la(t){if(t==null)return null;if(typeof t=="function")return t.displayName||t.name||null;if(typeof t=="string")return t;switch(t){case Fn:return"Fragment";case Nn:return"Portal";case ea:return"Profiler";case Js:return"StrictMode";case na:return"Suspense";case ra:return"SuspenseList"}if(typeof t=="object")switch(t.$$typeof){case Wd:return(t.displayName||"Context")+".Consumer";case Hd:return(t._context.displayName||"Context")+".Provider";case ta:var e=t.render;return t=t.displayName,t||(t=e.displayName||e.name||"",t=t!==""?"ForwardRef("+t+")":"ForwardRef"),t;case ia:return e=t.displayName||null,e!==null?e:la(t.type)||"Memo";case Lt:e=t._payload,t=t._init;try{return la(t(e))}catch{}}return null}function tx(t){var e=t.type;switch(t.tag){case 24:return"Cache";case 9:return(e.displayName||"Context")+".Consumer";case 10:return(e._context.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return t=e.render,t=t.displayName||t.name||"",e.displayName||(t!==""?"ForwardRef("+t+")":"ForwardRef");case 7:return"Fragment";case 5:return e;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return la(e);case 8:return e===Js?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 17:case 2:case 14:case 15:if(typeof e=="function")return e.displayName||e.name||null;if(typeof e=="string")return e}return null}function zt(t){switch(typeof t){case"boolean":case"number":case"string":case"undefined":return t;case"object":return t;default:return""}}function Vd(t){var e=t.type;return(t=t.nodeName)&&t.toLowerCase()==="input"&&(e==="checkbox"||e==="radio")}function nx(t){var e=Vd(t)?"checked":"value",n=Object.getOwnPropertyDescriptor(t.constructor.prototype,e),r=""+t[e];if(!t.hasOwnProperty(e)&&typeof n<"u"&&typeof n.get=="function"&&typeof n.set=="function"){var i=n.get,o=n.set;return Object.defineProperty(t,e,{configurable:!0,get:function(){return i.call(this)},set:function(s){r=""+s,o.call(this,s)}}),Object.defineProperty(t,e,{enumerable:n.enumerable}),{getValue:function(){return r},setValue:function(s){r=""+s},stopTracking:function(){t._valueTracker=null,delete t[e]}}}}function Gi(t){t._valueTracker||(t._valueTracker=nx(t))}function Yd(t){if(!t)return!1;var e=t._valueTracker;if(!e)return!0;var n=e.getValue(),r="";return t&&(r=Vd(t)?t.checked?"true":"false":t.value),t=r,t!==n?(e.setValue(t),!0):!1}function Vi(t){if(t=t||(typeof document<"u"?document:void 0),typeof t>"u")return null;try{return t.activeElement||t.body}catch{return t.body}}function ca(t,e){var n=e.checked;return Z({},e,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:n??t._wrapperState.initialChecked})}function Kd(t,e){var n=e.defaultValue==null?"":e.defaultValue,r=e.checked!=null?e.checked:e.defaultChecked;n=zt(e.value!=null?e.value:n),t._wrapperState={initialChecked:r,initialValue:n,controlled:e.type==="checkbox"||e.type==="radio"?e.checked!=null:e.value!=null}}function Qd(t,e){e=e.checked,e!=null&&qs(t,"checked",e,!1)}function da(t,e){Qd(t,e);var n=zt(e.value),r=e.type;if(n!=null)r==="number"?(n===0&&t.value===""||t.value!=n)&&(t.value=""+n):t.value!==""+n&&(t.value=""+n);else if(r==="submit"||r==="reset"){t.removeAttribute("value");return}e.hasOwnProperty("value")?ua(t,e.type,n):e.hasOwnProperty("defaultValue")&&ua(t,e.type,zt(e.defaultValue)),e.checked==null&&e.defaultChecked!=null&&(t.defaultChecked=!!e.defaultChecked)}function Xd(t,e,n){if(e.hasOwnProperty("value")||e.hasOwnProperty("defaultValue")){var r=e.type;if(!(r!=="submit"&&r!=="reset"||e.value!==void 0&&e.value!==null))return;e=""+t._wrapperState.initialValue,n||e===t.value||(t.value=e),t.defaultValue=e}n=t.name,n!==""&&(t.name=""),t.defaultChecked=!!t._wrapperState.initialChecked,n!==""&&(t.name=n)}function ua(t,e,n){(e!=="number"||Vi(t.ownerDocument)!==t)&&(n==null?t.defaultValue=""+t._wrapperState.initialValue:t.defaultValue!==""+n&&(t.defaultValue=""+n))}var Ir=Array.isArray;function _n(t,e,n,r){if(t=t.options,e){e={};for(var i=0;i<n.length;i++)e["$"+n[i]]=!0;for(n=0;n<t.length;n++)i=e.hasOwnProperty("$"+t[n].value),t[n].selected!==i&&(t[n].selected=i),i&&r&&(t[n].defaultSelected=!0)}else{for(n=""+zt(n),e=null,i=0;i<t.length;i++){if(t[i].value===n){t[i].selected=!0,r&&(t[i].defaultSelected=!0);return}e!==null||t[i].disabled||(e=t[i])}e!==null&&(e.selected=!0)}}function ha(t,e){if(e.dangerouslySetInnerHTML!=null)throw Error(T(91));return Z({},e,{value:void 0,defaultValue:void 0,children:""+t._wrapperState.initialValue})}function Zd(t,e){var n=e.value;if(n==null){if(n=e.children,e=e.defaultValue,n!=null){if(e!=null)throw Error(T(92));if(Ir(n)){if(1<n.length)throw Error(T(93));n=n[0]}e=n}e==null&&(e=""),n=e}t._wrapperState={initialValue:zt(n)}}function qd(t,e){var n=zt(e.value),r=zt(e.defaultValue);n!=null&&(n=""+n,n!==t.value&&(t.value=n),e.defaultValue==null&&t.defaultValue!==n&&(t.defaultValue=n)),r!=null&&(t.defaultValue=""+r)}function Jd(t){var e=t.textContent;e===t._wrapperState.initialValue&&e!==""&&e!==null&&(t.value=e)}function eu(t){switch(t){case"svg":return"http://www.w3.org/2000/svg";case"math":return"http://www.w3.org/1998/Math/MathML";default:return"http://www.w3.org/1999/xhtml"}}function pa(t,e){return t==null||t==="http://www.w3.org/1999/xhtml"?eu(e):t==="http://www.w3.org/2000/svg"&&e==="foreignObject"?"http://www.w3.org/1999/xhtml":t}var Yi,tu=function(t){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(e,n,r,i){MSApp.execUnsafeLocalFunction(function(){return t(e,n,r,i)})}:t}(function(t,e){if(t.namespaceURI!=="http://www.w3.org/2000/svg"||"innerHTML"in t)t.innerHTML=e;else{for(Yi=Yi||document.createElement("div"),Yi.innerHTML="<svg>"+e.valueOf().toString()+"</svg>",e=Yi.firstChild;t.firstChild;)t.removeChild(t.firstChild);for(;e.firstChild;)t.appendChild(e.firstChild)}});function Or(t,e){if(e){var n=t.firstChild;if(n&&n===t.lastChild&&n.nodeType===3){n.nodeValue=e;return}}t.textContent=e}var Lr={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0},rx=["Webkit","ms","Moz","O"];Object.keys(Lr).forEach(function(t){rx.forEach(function(e){e=e+t.charAt(0).toUpperCase()+t.substring(1),Lr[e]=Lr[t]})});function nu(t,e,n){return e==null||typeof e=="boolean"||e===""?"":n||typeof e!="number"||e===0||Lr.hasOwnProperty(t)&&Lr[t]?(""+e).trim():e+"px"}function ru(t,e){t=t.style;for(var n in e)if(e.hasOwnProperty(n)){var r=n.indexOf("--")===0,i=nu(n,e[n],r);n==="float"&&(n="cssFloat"),r?t.setProperty(n,i):t[n]=i}}var ix=Z({menuitem:!0},{area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0});function fa(t,e){if(e){if(ix[t]&&(e.children!=null||e.dangerouslySetInnerHTML!=null))throw Error(T(137,t));if(e.dangerouslySetInnerHTML!=null){if(e.children!=null)throw Error(T(60));if(typeof e.dangerouslySetInnerHTML!="object"||!("__html"in e.dangerouslySetInnerHTML))throw Error(T(61))}if(e.style!=null&&typeof e.style!="object")throw Error(T(62))}}function ga(t,e){if(t.indexOf("-")===-1)return typeof e.is=="string";switch(t){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var ma=null;function xa(t){return t=t.target||t.srcElement||window,t.correspondingUseElement&&(t=t.correspondingUseElement),t.nodeType===3?t.parentNode:t}var ya=null,Hn=null,Wn=null;function iu(t){if(t=ii(t)){if(typeof ya!="function")throw Error(T(280));var e=t.stateNode;e&&(e=xo(e),ya(t.stateNode,t.type,e))}}function ou(t){Hn?Wn?Wn.push(t):Wn=[t]:Hn=t}function su(){if(Hn){var t=Hn,e=Wn;if(Wn=Hn=null,iu(t),e)for(t=0;t<e.length;t++)iu(e[t])}}function au(t,e){return t(e)}function lu(){}var wa=!1;function cu(t,e,n){if(wa)return t(e,n);wa=!0;try{return au(t,e,n)}finally{wa=!1,(Hn!==null||Wn!==null)&&(lu(),su())}}function zr(t,e){var n=t.stateNode;if(n===null)return null;var r=xo(n);if(r===null)return null;n=r[e];e:switch(e){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(r=!r.disabled)||(t=t.type,r=!(t==="button"||t==="input"||t==="select"||t==="textarea")),t=!r;break e;default:t=!1}if(t)return null;if(n&&typeof n!="function")throw Error(T(231,e,typeof n));return n}var va=!1;if(yt)try{var Br={};Object.defineProperty(Br,"passive",{get:function(){va=!0}}),window.addEventListener("test",Br,Br),window.removeEventListener("test",Br,Br)}catch{va=!1}function ox(t,e,n,r,i,o,s,l,c){var d=Array.prototype.slice.call(arguments,3);try{e.apply(n,d)}catch(u){this.onError(u)}}var Nr=!1,Ki=null,Qi=!1,ba=null,sx={onError:function(t){Nr=!0,Ki=t}};function ax(t,e,n,r,i,o,s,l,c){Nr=!1,Ki=null,ox.apply(sx,arguments)}function lx(t,e,n,r,i,o,s,l,c){if(ax.apply(this,arguments),Nr){if(Nr){var d=Ki;Nr=!1,Ki=null}else throw Error(T(198));Qi||(Qi=!0,ba=d)}}function cn(t){var e=t,n=t;if(t.alternate)for(;e.return;)e=e.return;else{t=e;do e=t,e.flags&4098&&(n=e.return),t=e.return;while(t)}return e.tag===3?n:null}function du(t){if(t.tag===13){var e=t.memoizedState;if(e===null&&(t=t.alternate,t!==null&&(e=t.memoizedState)),e!==null)return e.dehydrated}return null}function uu(t){if(cn(t)!==t)throw Error(T(188))}function cx(t){var e=t.alternate;if(!e){if(e=cn(t),e===null)throw Error(T(188));return e!==t?null:t}for(var n=t,r=e;;){var i=n.return;if(i===null)break;var o=i.alternate;if(o===null){if(r=i.return,r!==null){n=r;continue}break}if(i.child===o.child){for(o=i.child;o;){if(o===n)return uu(i),t;if(o===r)return uu(i),e;o=o.sibling}throw Error(T(188))}if(n.return!==r.return)n=i,r=o;else{for(var s=!1,l=i.child;l;){if(l===n){s=!0,n=i,r=o;break}if(l===r){s=!0,r=i,n=o;break}l=l.sibling}if(!s){for(l=o.child;l;){if(l===n){s=!0,n=o,r=i;break}if(l===r){s=!0,r=o,n=i;break}l=l.sibling}if(!s)throw Error(T(189))}}if(n.alternate!==r)throw Error(T(190))}if(n.tag!==3)throw Error(T(188));return n.stateNode.current===n?t:e}function hu(t){return t=cx(t),t!==null?pu(t):null}function pu(t){if(t.tag===5||t.tag===6)return t;for(t=t.child;t!==null;){var e=pu(t);if(e!==null)return e;t=t.sibling}return null}var fu=ze.unstable_scheduleCallback,gu=ze.unstable_cancelCallback,dx=ze.unstable_shouldYield,ux=ze.unstable_requestPaint,ne=ze.unstable_now,hx=ze.unstable_getCurrentPriorityLevel,Sa=ze.unstable_ImmediatePriority,mu=ze.unstable_UserBlockingPriority,Xi=ze.unstable_NormalPriority,px=ze.unstable_LowPriority,xu=ze.unstable_IdlePriority,Zi=null,ht=null;function fx(t){if(ht&&typeof ht.onCommitFiberRoot=="function")try{ht.onCommitFiberRoot(Zi,t,void 0,(t.current.flags&128)===128)}catch{}}var et=Math.clz32?Math.clz32:xx,gx=Math.log,mx=Math.LN2;function xx(t){return t>>>=0,t===0?32:31-(gx(t)/mx|0)|0}var qi=64,Ji=4194304;function Fr(t){switch(t&-t){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return t&4194240;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return t&130023424;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 1073741824;default:return t}}function eo(t,e){var n=t.pendingLanes;if(n===0)return 0;var r=0,i=t.suspendedLanes,o=t.pingedLanes,s=n&268435455;if(s!==0){var l=s&~i;l!==0?r=Fr(l):(o&=s,o!==0&&(r=Fr(o)))}else s=n&~i,s!==0?r=Fr(s):o!==0&&(r=Fr(o));if(r===0)return 0;if(e!==0&&e!==r&&!(e&i)&&(i=r&-r,o=e&-e,i>=o||i===16&&(o&4194240)!==0))return e;if(r&4&&(r|=n&16),e=t.entangledLanes,e!==0)for(t=t.entanglements,e&=r;0<e;)n=31-et(e),i=1<<n,r|=t[n],e&=~i;return r}function yx(t,e){switch(t){case 1:case 2:case 4:return e+250;case 8:case 16:case 32:case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return e+5e3;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return-1;case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function wx(t,e){for(var n=t.suspendedLanes,r=t.pingedLanes,i=t.expirationTimes,o=t.pendingLanes;0<o;){var s=31-et(o),l=1<<s,c=i[s];c===-1?(!(l&n)||l&r)&&(i[s]=yx(l,e)):c<=e&&(t.expiredLanes|=l),o&=~l}}function ka(t){return t=t.pendingLanes&-1073741825,t!==0?t:t&1073741824?1073741824:0}function yu(){var t=qi;return qi<<=1,!(qi&4194240)&&(qi=64),t}function $a(t){for(var e=[],n=0;31>n;n++)e.push(t);return e}function _r(t,e,n){t.pendingLanes|=e,e!==536870912&&(t.suspendedLanes=0,t.pingedLanes=0),t=t.eventTimes,e=31-et(e),t[e]=n}function vx(t,e){var n=t.pendingLanes&~e;t.pendingLanes=e,t.suspendedLanes=0,t.pingedLanes=0,t.expiredLanes&=e,t.mutableReadLanes&=e,t.entangledLanes&=e,e=t.entanglements;var r=t.eventTimes;for(t=t.expirationTimes;0<n;){var i=31-et(n),o=1<<i;e[i]=0,r[i]=-1,t[i]=-1,n&=~o}}function Ca(t,e){var n=t.entangledLanes|=e;for(t=t.entanglements;n;){var r=31-et(n),i=1<<r;i&e|t[r]&e&&(t[r]|=e),n&=~i}}var H=0;function wu(t){return t&=-t,1<t?4<t?t&268435455?16:536870912:4:1}var vu,Ta,bu,Su,ku,Ma=!1,to=[],Bt=null,Nt=null,Ft=null,Hr=new Map,Wr=new Map,_t=[],bx="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");function $u(t,e){switch(t){case"focusin":case"focusout":Bt=null;break;case"dragenter":case"dragleave":Nt=null;break;case"mouseover":case"mouseout":Ft=null;break;case"pointerover":case"pointerout":Hr.delete(e.pointerId);break;case"gotpointercapture":case"lostpointercapture":Wr.delete(e.pointerId)}}function Ur(t,e,n,r,i,o){return t===null||t.nativeEvent!==o?(t={blockedOn:e,domEventName:n,eventSystemFlags:r,nativeEvent:o,targetContainers:[i]},e!==null&&(e=ii(e),e!==null&&Ta(e)),t):(t.eventSystemFlags|=r,e=t.targetContainers,i!==null&&e.indexOf(i)===-1&&e.push(i),t)}function Sx(t,e,n,r,i){switch(e){case"focusin":return Bt=Ur(Bt,t,e,n,r,i),!0;case"dragenter":return Nt=Ur(Nt,t,e,n,r,i),!0;case"mouseover":return Ft=Ur(Ft,t,e,n,r,i),!0;case"pointerover":var o=i.pointerId;return Hr.set(o,Ur(Hr.get(o)||null,t,e,n,r,i)),!0;case"gotpointercapture":return o=i.pointerId,Wr.set(o,Ur(Wr.get(o)||null,t,e,n,r,i)),!0}return!1}function Cu(t){var e=dn(t.target);if(e!==null){var n=cn(e);if(n!==null){if(e=n.tag,e===13){if(e=du(n),e!==null){t.blockedOn=e,ku(t.priority,function(){bu(n)});return}}else if(e===3&&n.stateNode.current.memoizedState.isDehydrated){t.blockedOn=n.tag===3?n.stateNode.containerInfo:null;return}}}t.blockedOn=null}function no(t){if(t.blockedOn!==null)return!1;for(var e=t.targetContainers;0<e.length;){var n=Ea(t.domEventName,t.eventSystemFlags,e[0],t.nativeEvent);if(n===null){n=t.nativeEvent;var r=new n.constructor(n.type,n);ma=r,n.target.dispatchEvent(r),ma=null}else return e=ii(n),e!==null&&Ta(e),t.blockedOn=n,!1;e.shift()}return!0}function Tu(t,e,n){no(t)&&n.delete(e)}function kx(){Ma=!1,Bt!==null&&no(Bt)&&(Bt=null),Nt!==null&&no(Nt)&&(Nt=null),Ft!==null&&no(Ft)&&(Ft=null),Hr.forEach(Tu),Wr.forEach(Tu)}function Gr(t,e){t.blockedOn===e&&(t.blockedOn=null,Ma||(Ma=!0,ze.unstable_scheduleCallback(ze.unstable_NormalPriority,kx)))}function Vr(t){function e(i){return Gr(i,t)}if(0<to.length){Gr(to[0],t);for(var n=1;n<to.length;n++){var r=to[n];r.blockedOn===t&&(r.blockedOn=null)}}for(Bt!==null&&Gr(Bt,t),Nt!==null&&Gr(Nt,t),Ft!==null&&Gr(Ft,t),Hr.forEach(e),Wr.forEach(e),n=0;n<_t.length;n++)r=_t[n],r.blockedOn===t&&(r.blockedOn=null);for(;0<_t.length&&(n=_t[0],n.blockedOn===null);)Cu(n),n.blockedOn===null&&_t.shift()}var Un=wt.ReactCurrentBatchConfig,ro=!0;function $x(t,e,n,r){var i=H,o=Un.transition;Un.transition=null;try{H=1,ja(t,e,n,r)}finally{H=i,Un.transition=o}}function Cx(t,e,n,r){var i=H,o=Un.transition;Un.transition=null;try{H=4,ja(t,e,n,r)}finally{H=i,Un.transition=o}}function ja(t,e,n,r){if(ro){var i=Ea(t,e,n,r);if(i===null)Va(t,e,r,io,n),$u(t,r);else if(Sx(i,t,e,n,r))r.stopPropagation();else if($u(t,r),e&4&&-1<bx.indexOf(t)){for(;i!==null;){var o=ii(i);if(o!==null&&vu(o),o=Ea(t,e,n,r),o===null&&Va(t,e,r,io,n),o===i)break;i=o}i!==null&&r.stopPropagation()}else Va(t,e,r,null,n)}}var io=null;function Ea(t,e,n,r){if(io=null,t=xa(r),t=dn(t),t!==null)if(e=cn(t),e===null)t=null;else if(n=e.tag,n===13){if(t=du(e),t!==null)return t;t=null}else if(n===3){if(e.stateNode.current.memoizedState.isDehydrated)return e.tag===3?e.stateNode.containerInfo:null;t=null}else e!==t&&(t=null);return io=t,null}function Mu(t){switch(t){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return 1;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return 4;case"message":switch(hx()){case Sa:return 1;case mu:return 4;case Xi:case px:return 16;case xu:return 536870912;default:return 16}default:return 16}}var Ht=null,Da=null,oo=null;function ju(){if(oo)return oo;var t,e=Da,n=e.length,r,i="value"in Ht?Ht.value:Ht.textContent,o=i.length;for(t=0;t<n&&e[t]===i[t];t++);var s=n-t;for(r=1;r<=s&&e[n-r]===i[o-r];r++);return oo=i.slice(t,1<r?1-r:void 0)}function so(t){var e=t.keyCode;return"charCode"in t?(t=t.charCode,t===0&&e===13&&(t=13)):t=e,t===10&&(t=13),32<=t||t===13?t:0}function ao(){return!0}function Eu(){return!1}function Be(t){function e(n,r,i,o,s){this._reactName=n,this._targetInst=i,this.type=r,this.nativeEvent=o,this.target=s,this.currentTarget=null;for(var l in t)t.hasOwnProperty(l)&&(n=t[l],this[l]=n?n(o):o[l]);return this.isDefaultPrevented=(o.defaultPrevented!=null?o.defaultPrevented:o.returnValue===!1)?ao:Eu,this.isPropagationStopped=Eu,this}return Z(e.prototype,{preventDefault:function(){this.defaultPrevented=!0;var n=this.nativeEvent;n&&(n.preventDefault?n.preventDefault():typeof n.returnValue!="unknown"&&(n.returnValue=!1),this.isDefaultPrevented=ao)},stopPropagation:function(){var n=this.nativeEvent;n&&(n.stopPropagation?n.stopPropagation():typeof n.cancelBubble!="unknown"&&(n.cancelBubble=!0),this.isPropagationStopped=ao)},persist:function(){},isPersistent:ao}),e}var Gn={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(t){return t.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Pa=Be(Gn),Yr=Z({},Gn,{view:0,detail:0}),Tx=Be(Yr),Aa,Ra,Kr,lo=Z({},Yr,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:Oa,button:0,buttons:0,relatedTarget:function(t){return t.relatedTarget===void 0?t.fromElement===t.srcElement?t.toElement:t.fromElement:t.relatedTarget},movementX:function(t){return"movementX"in t?t.movementX:(t!==Kr&&(Kr&&t.type==="mousemove"?(Aa=t.screenX-Kr.screenX,Ra=t.screenY-Kr.screenY):Ra=Aa=0,Kr=t),Aa)},movementY:function(t){return"movementY"in t?t.movementY:Ra}}),Du=Be(lo),Mx=Z({},lo,{dataTransfer:0}),jx=Be(Mx),Ex=Z({},Yr,{relatedTarget:0}),Ia=Be(Ex),Dx=Z({},Gn,{animationName:0,elapsedTime:0,pseudoElement:0}),Px=Be(Dx),Ax=Z({},Gn,{clipboardData:function(t){return"clipboardData"in t?t.clipboardData:window.clipboardData}}),Rx=Be(Ax),Ix=Z({},Gn,{data:0}),Pu=Be(Ix),Ox={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},Lx={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},zx={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function Bx(t){var e=this.nativeEvent;return e.getModifierState?e.getModifierState(t):(t=zx[t])?!!e[t]:!1}function Oa(){return Bx}var Nx=Z({},Yr,{key:function(t){if(t.key){var e=Ox[t.key]||t.key;if(e!=="Unidentified")return e}return t.type==="keypress"?(t=so(t),t===13?"Enter":String.fromCharCode(t)):t.type==="keydown"||t.type==="keyup"?Lx[t.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:Oa,charCode:function(t){return t.type==="keypress"?so(t):0},keyCode:function(t){return t.type==="keydown"||t.type==="keyup"?t.keyCode:0},which:function(t){return t.type==="keypress"?so(t):t.type==="keydown"||t.type==="keyup"?t.keyCode:0}}),Fx=Be(Nx),_x=Z({},lo,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),Au=Be(_x),Hx=Z({},Yr,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:Oa}),Wx=Be(Hx),Ux=Z({},Gn,{propertyName:0,elapsedTime:0,pseudoElement:0}),Gx=Be(Ux),Vx=Z({},lo,{deltaX:function(t){return"deltaX"in t?t.deltaX:"wheelDeltaX"in t?-t.wheelDeltaX:0},deltaY:function(t){return"deltaY"in t?t.deltaY:"wheelDeltaY"in t?-t.wheelDeltaY:"wheelDelta"in t?-t.wheelDelta:0},deltaZ:0,deltaMode:0}),Yx=Be(Vx),Kx=[9,13,27,32],La=yt&&"CompositionEvent"in window,Qr=null;yt&&"documentMode"in document&&(Qr=document.documentMode);var Qx=yt&&"TextEvent"in window&&!Qr,Ru=yt&&(!La||Qr&&8<Qr&&11>=Qr),Iu=" ",Ou=!1;function Lu(t,e){switch(t){case"keyup":return Kx.indexOf(e.keyCode)!==-1;case"keydown":return e.keyCode!==229;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function zu(t){return t=t.detail,typeof t=="object"&&"data"in t?t.data:null}var Vn=!1;function Xx(t,e){switch(t){case"compositionend":return zu(e);case"keypress":return e.which!==32?null:(Ou=!0,Iu);case"textInput":return t=e.data,t===Iu&&Ou?null:t;default:return null}}function Zx(t,e){if(Vn)return t==="compositionend"||!La&&Lu(t,e)?(t=ju(),oo=Da=Ht=null,Vn=!1,t):null;switch(t){case"paste":return null;case"keypress":if(!(e.ctrlKey||e.altKey||e.metaKey)||e.ctrlKey&&e.altKey){if(e.char&&1<e.char.length)return e.char;if(e.which)return String.fromCharCode(e.which)}return null;case"compositionend":return Ru&&e.locale!=="ko"?null:e.data;default:return null}}var qx={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function Bu(t){var e=t&&t.nodeName&&t.nodeName.toLowerCase();return e==="input"?!!qx[t.type]:e==="textarea"}function Nu(t,e,n,r){ou(r),e=fo(e,"onChange"),0<e.length&&(n=new Pa("onChange","change",null,n,r),t.push({event:n,listeners:e}))}var Xr=null,Zr=null;function Jx(t){rh(t,0)}function co(t){var e=Zn(t);if(Yd(e))return t}function e0(t,e){if(t==="change")return e}var Fu=!1;if(yt){var za;if(yt){var Ba="oninput"in document;if(!Ba){var _u=document.createElement("div");_u.setAttribute("oninput","return;"),Ba=typeof _u.oninput=="function"}za=Ba}else za=!1;Fu=za&&(!document.documentMode||9<document.documentMode)}function Hu(){Xr&&(Xr.detachEvent("onpropertychange",Wu),Zr=Xr=null)}function Wu(t){if(t.propertyName==="value"&&co(Zr)){var e=[];Nu(e,Zr,t,xa(t)),cu(Jx,e)}}function t0(t,e,n){t==="focusin"?(Hu(),Xr=e,Zr=n,Xr.attachEvent("onpropertychange",Wu)):t==="focusout"&&Hu()}function n0(t){if(t==="selectionchange"||t==="keyup"||t==="keydown")return co(Zr)}function r0(t,e){if(t==="click")return co(e)}function i0(t,e){if(t==="input"||t==="change")return co(e)}function o0(t,e){return t===e&&(t!==0||1/t===1/e)||t!==t&&e!==e}var tt=typeof Object.is=="function"?Object.is:o0;function qr(t,e){if(tt(t,e))return!0;if(typeof t!="object"||t===null||typeof e!="object"||e===null)return!1;var n=Object.keys(t),r=Object.keys(e);if(n.length!==r.length)return!1;for(r=0;r<n.length;r++){var i=n[r];if(!Qs.call(e,i)||!tt(t[i],e[i]))return!1}return!0}function Uu(t){for(;t&&t.firstChild;)t=t.firstChild;return t}function Gu(t,e){var n=Uu(t);t=0;for(var r;n;){if(n.nodeType===3){if(r=t+n.textContent.length,t<=e&&r>=e)return{node:n,offset:e-t};t=r}e:{for(;n;){if(n.nextSibling){n=n.nextSibling;break e}n=n.parentNode}n=void 0}n=Uu(n)}}function Vu(t,e){return t&&e?t===e?!0:t&&t.nodeType===3?!1:e&&e.nodeType===3?Vu(t,e.parentNode):"contains"in t?t.contains(e):t.compareDocumentPosition?!!(t.compareDocumentPosition(e)&16):!1:!1}function Yu(){for(var t=window,e=Vi();e instanceof t.HTMLIFrameElement;){try{var n=typeof e.contentWindow.location.href=="string"}catch{n=!1}if(n)t=e.contentWindow;else break;e=Vi(t.document)}return e}function Na(t){var e=t&&t.nodeName&&t.nodeName.toLowerCase();return e&&(e==="input"&&(t.type==="text"||t.type==="search"||t.type==="tel"||t.type==="url"||t.type==="password")||e==="textarea"||t.contentEditable==="true")}function s0(t){var e=Yu(),n=t.focusedElem,r=t.selectionRange;if(e!==n&&n&&n.ownerDocument&&Vu(n.ownerDocument.documentElement,n)){if(r!==null&&Na(n)){if(e=r.start,t=r.end,t===void 0&&(t=e),"selectionStart"in n)n.selectionStart=e,n.selectionEnd=Math.min(t,n.value.length);else if(t=(e=n.ownerDocument||document)&&e.defaultView||window,t.getSelection){t=t.getSelection();var i=n.textContent.length,o=Math.min(r.start,i);r=r.end===void 0?o:Math.min(r.end,i),!t.extend&&o>r&&(i=r,r=o,o=i),i=Gu(n,o);var s=Gu(n,r);i&&s&&(t.rangeCount!==1||t.anchorNode!==i.node||t.anchorOffset!==i.offset||t.focusNode!==s.node||t.focusOffset!==s.offset)&&(e=e.createRange(),e.setStart(i.node,i.offset),t.removeAllRanges(),o>r?(t.addRange(e),t.extend(s.node,s.offset)):(e.setEnd(s.node,s.offset),t.addRange(e)))}}for(e=[],t=n;t=t.parentNode;)t.nodeType===1&&e.push({element:t,left:t.scrollLeft,top:t.scrollTop});for(typeof n.focus=="function"&&n.focus(),n=0;n<e.length;n++)t=e[n],t.element.scrollLeft=t.left,t.element.scrollTop=t.top}}var a0=yt&&"documentMode"in document&&11>=document.documentMode,Yn=null,Fa=null,Jr=null,_a=!1;function Ku(t,e,n){var r=n.window===n?n.document:n.nodeType===9?n:n.ownerDocument;_a||Yn==null||Yn!==Vi(r)||(r=Yn,"selectionStart"in r&&Na(r)?r={start:r.selectionStart,end:r.selectionEnd}:(r=(r.ownerDocument&&r.ownerDocument.defaultView||window).getSelection(),r={anchorNode:r.anchorNode,anchorOffset:r.anchorOffset,focusNode:r.focusNode,focusOffset:r.focusOffset}),Jr&&qr(Jr,r)||(Jr=r,r=fo(Fa,"onSelect"),0<r.length&&(e=new Pa("onSelect","select",null,e,n),t.push({event:e,listeners:r}),e.target=Yn)))}function uo(t,e){var n={};return n[t.toLowerCase()]=e.toLowerCase(),n["Webkit"+t]="webkit"+e,n["Moz"+t]="moz"+e,n}var Kn={animationend:uo("Animation","AnimationEnd"),animationiteration:uo("Animation","AnimationIteration"),animationstart:uo("Animation","AnimationStart"),transitionend:uo("Transition","TransitionEnd")},Ha={},Qu={};yt&&(Qu=document.createElement("div").style,"AnimationEvent"in window||(delete Kn.animationend.animation,delete Kn.animationiteration.animation,delete Kn.animationstart.animation),"TransitionEvent"in window||delete Kn.transitionend.transition);function ho(t){if(Ha[t])return Ha[t];if(!Kn[t])return t;var e=Kn[t],n;for(n in e)if(e.hasOwnProperty(n)&&n in Qu)return Ha[t]=e[n];return t}var Xu=ho("animationend"),Zu=ho("animationiteration"),qu=ho("animationstart"),Ju=ho("transitionend"),eh=new Map,th="abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");function Wt(t,e){eh.set(t,e),ln(e,[t])}for(var Wa=0;Wa<th.length;Wa++){var Ua=th[Wa],l0=Ua.toLowerCase(),c0=Ua[0].toUpperCase()+Ua.slice(1);Wt(l0,"on"+c0)}Wt(Xu,"onAnimationEnd"),Wt(Zu,"onAnimationIteration"),Wt(qu,"onAnimationStart"),Wt("dblclick","onDoubleClick"),Wt("focusin","onFocus"),Wt("focusout","onBlur"),Wt(Ju,"onTransitionEnd"),Bn("onMouseEnter",["mouseout","mouseover"]),Bn("onMouseLeave",["mouseout","mouseover"]),Bn("onPointerEnter",["pointerout","pointerover"]),Bn("onPointerLeave",["pointerout","pointerover"]),ln("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),ln("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),ln("onBeforeInput",["compositionend","keypress","textInput","paste"]),ln("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),ln("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),ln("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var ei="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),d0=new Set("cancel close invalid load scroll toggle".split(" ").concat(ei));function nh(t,e,n){var r=t.type||"unknown-event";t.currentTarget=n,lx(r,e,void 0,t),t.currentTarget=null}function rh(t,e){e=(e&4)!==0;for(var n=0;n<t.length;n++){var r=t[n],i=r.event;r=r.listeners;e:{var o=void 0;if(e)for(var s=r.length-1;0<=s;s--){var l=r[s],c=l.instance,d=l.currentTarget;if(l=l.listener,c!==o&&i.isPropagationStopped())break e;nh(i,l,d),o=c}else for(s=0;s<r.length;s++){if(l=r[s],c=l.instance,d=l.currentTarget,l=l.listener,c!==o&&i.isPropagationStopped())break e;nh(i,l,d),o=c}}}if(Qi)throw t=ba,Qi=!1,ba=null,t}function Y(t,e){var n=e[qa];n===void 0&&(n=e[qa]=new Set);var r=t+"__bubble";n.has(r)||(ih(e,t,2,!1),n.add(r))}function Ga(t,e,n){var r=0;e&&(r|=4),ih(n,t,r,e)}var po="_reactListening"+Math.random().toString(36).slice(2);function ti(t){if(!t[po]){t[po]=!0,Nd.forEach(function(n){n!=="selectionchange"&&(d0.has(n)||Ga(n,!1,t),Ga(n,!0,t))});var e=t.nodeType===9?t:t.ownerDocument;e===null||e[po]||(e[po]=!0,Ga("selectionchange",!1,e))}}function ih(t,e,n,r){switch(Mu(e)){case 1:var i=$x;break;case 4:i=Cx;break;default:i=ja}n=i.bind(null,e,n,t),i=void 0,!va||e!=="touchstart"&&e!=="touchmove"&&e!=="wheel"||(i=!0),r?i!==void 0?t.addEventListener(e,n,{capture:!0,passive:i}):t.addEventListener(e,n,!0):i!==void 0?t.addEventListener(e,n,{passive:i}):t.addEventListener(e,n,!1)}function Va(t,e,n,r,i){var o=r;if(!(e&1)&&!(e&2)&&r!==null)e:for(;;){if(r===null)return;var s=r.tag;if(s===3||s===4){var l=r.stateNode.containerInfo;if(l===i||l.nodeType===8&&l.parentNode===i)break;if(s===4)for(s=r.return;s!==null;){var c=s.tag;if((c===3||c===4)&&(c=s.stateNode.containerInfo,c===i||c.nodeType===8&&c.parentNode===i))return;s=s.return}for(;l!==null;){if(s=dn(l),s===null)return;if(c=s.tag,c===5||c===6){r=o=s;continue e}l=l.parentNode}}r=r.return}cu(function(){var d=o,u=xa(n),f=[];e:{var g=eh.get(t);if(g!==void 0){var v=Pa,x=t;switch(t){case"keypress":if(so(n)===0)break e;case"keydown":case"keyup":v=Fx;break;case"focusin":x="focus",v=Ia;break;case"focusout":x="blur",v=Ia;break;case"beforeblur":case"afterblur":v=Ia;break;case"click":if(n.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":v=Du;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":v=jx;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":v=Wx;break;case Xu:case Zu:case qu:v=Px;break;case Ju:v=Gx;break;case"scroll":v=Tx;break;case"wheel":v=Yx;break;case"copy":case"cut":case"paste":v=Rx;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":v=Au}var b=(e&4)!==0,M=!b&&t==="scroll",y=b?g!==null?g+"Capture":null:g;b=[];for(var p=d,w;p!==null;){w=p;var S=w.stateNode;if(w.tag===5&&S!==null&&(w=S,y!==null&&(S=zr(p,y),S!=null&&b.push(ni(p,S,w)))),M)break;p=p.return}0<b.length&&(g=new v(g,x,null,n,u),f.push({event:g,listeners:b}))}}if(!(e&7)){e:{if(g=t==="mouseover"||t==="pointerover",v=t==="mouseout"||t==="pointerout",g&&n!==ma&&(x=n.relatedTarget||n.fromElement)&&(dn(x)||x[vt]))break e;if((v||g)&&(g=u.window===u?u:(g=u.ownerDocument)?g.defaultView||g.parentWindow:window,v?(x=n.relatedTarget||n.toElement,v=d,x=x?dn(x):null,x!==null&&(M=cn(x),x!==M||x.tag!==5&&x.tag!==6)&&(x=null)):(v=null,x=d),v!==x)){if(b=Du,S="onMouseLeave",y="onMouseEnter",p="mouse",(t==="pointerout"||t==="pointerover")&&(b=Au,S="onPointerLeave",y="onPointerEnter",p="pointer"),M=v==null?g:Zn(v),w=x==null?g:Zn(x),g=new b(S,p+"leave",v,n,u),g.target=M,g.relatedTarget=w,S=null,dn(u)===d&&(b=new b(y,p+"enter",x,n,u),b.target=w,b.relatedTarget=M,S=b),M=S,v&&x)t:{for(b=v,y=x,p=0,w=b;w;w=Qn(w))p++;for(w=0,S=y;S;S=Qn(S))w++;for(;0<p-w;)b=Qn(b),p--;for(;0<w-p;)y=Qn(y),w--;for(;p--;){if(b===y||y!==null&&b===y.alternate)break t;b=Qn(b),y=Qn(y)}b=null}else b=null;v!==null&&oh(f,g,v,b,!1),x!==null&&M!==null&&oh(f,M,x,b,!0)}}e:{if(g=d?Zn(d):window,v=g.nodeName&&g.nodeName.toLowerCase(),v==="select"||v==="input"&&g.type==="file")var C=e0;else if(Bu(g))if(Fu)C=i0;else{C=n0;var $=t0}else(v=g.nodeName)&&v.toLowerCase()==="input"&&(g.type==="checkbox"||g.type==="radio")&&(C=r0);if(C&&(C=C(t,d))){Nu(f,C,n,u);break e}$&&$(t,g,d),t==="focusout"&&($=g._wrapperState)&&$.controlled&&g.type==="number"&&ua(g,"number",g.value)}switch($=d?Zn(d):window,t){case"focusin":(Bu($)||$.contentEditable==="true")&&(Yn=$,Fa=d,Jr=null);break;case"focusout":Jr=Fa=Yn=null;break;case"mousedown":_a=!0;break;case"contextmenu":case"mouseup":case"dragend":_a=!1,Ku(f,n,u);break;case"selectionchange":if(a0)break;case"keydown":case"keyup":Ku(f,n,u)}var k;if(La)e:{switch(t){case"compositionstart":var j="onCompositionStart";break e;case"compositionend":j="onCompositionEnd";break e;case"compositionupdate":j="onCompositionUpdate";break e}j=void 0}else Vn?Lu(t,n)&&(j="onCompositionEnd"):t==="keydown"&&n.keyCode===229&&(j="onCompositionStart");j&&(Ru&&n.locale!=="ko"&&(Vn||j!=="onCompositionStart"?j==="onCompositionEnd"&&Vn&&(k=ju()):(Ht=u,Da="value"in Ht?Ht.value:Ht.textContent,Vn=!0)),$=fo(d,j),0<$.length&&(j=new Pu(j,t,null,n,u),f.push({event:j,listeners:$}),k?j.data=k:(k=zu(n),k!==null&&(j.data=k)))),(k=Qx?Xx(t,n):Zx(t,n))&&(d=fo(d,"onBeforeInput"),0<d.length&&(u=new Pu("onBeforeInput","beforeinput",null,n,u),f.push({event:u,listeners:d}),u.data=k))}rh(f,e)})}function ni(t,e,n){return{instance:t,listener:e,currentTarget:n}}function fo(t,e){for(var n=e+"Capture",r=[];t!==null;){var i=t,o=i.stateNode;i.tag===5&&o!==null&&(i=o,o=zr(t,n),o!=null&&r.unshift(ni(t,o,i)),o=zr(t,e),o!=null&&r.push(ni(t,o,i))),t=t.return}return r}function Qn(t){if(t===null)return null;do t=t.return;while(t&&t.tag!==5);return t||null}function oh(t,e,n,r,i){for(var o=e._reactName,s=[];n!==null&&n!==r;){var l=n,c=l.alternate,d=l.stateNode;if(c!==null&&c===r)break;l.tag===5&&d!==null&&(l=d,i?(c=zr(n,o),c!=null&&s.unshift(ni(n,c,l))):i||(c=zr(n,o),c!=null&&s.push(ni(n,c,l)))),n=n.return}s.length!==0&&t.push({event:e,listeners:s})}var u0=/\r\n?/g,h0=/\u0000|\uFFFD/g;function sh(t){return(typeof t=="string"?t:""+t).replace(u0,`
`).replace(h0,"")}function go(t,e,n){if(e=sh(e),sh(t)!==e&&n)throw Error(T(425))}function mo(){}var Ya=null,Ka=null;function Qa(t,e){return t==="textarea"||t==="noscript"||typeof e.children=="string"||typeof e.children=="number"||typeof e.dangerouslySetInnerHTML=="object"&&e.dangerouslySetInnerHTML!==null&&e.dangerouslySetInnerHTML.__html!=null}var Xa=typeof setTimeout=="function"?setTimeout:void 0,p0=typeof clearTimeout=="function"?clearTimeout:void 0,ah=typeof Promise=="function"?Promise:void 0,f0=typeof queueMicrotask=="function"?queueMicrotask:typeof ah<"u"?function(t){return ah.resolve(null).then(t).catch(g0)}:Xa;function g0(t){setTimeout(function(){throw t})}function Za(t,e){var n=e,r=0;do{var i=n.nextSibling;if(t.removeChild(n),i&&i.nodeType===8)if(n=i.data,n==="/$"){if(r===0){t.removeChild(i),Vr(e);return}r--}else n!=="$"&&n!=="$?"&&n!=="$!"||r++;n=i}while(n);Vr(e)}function Ut(t){for(;t!=null;t=t.nextSibling){var e=t.nodeType;if(e===1||e===3)break;if(e===8){if(e=t.data,e==="$"||e==="$!"||e==="$?")break;if(e==="/$")return null}}return t}function lh(t){t=t.previousSibling;for(var e=0;t;){if(t.nodeType===8){var n=t.data;if(n==="$"||n==="$!"||n==="$?"){if(e===0)return t;e--}else n==="/$"&&e++}t=t.previousSibling}return null}var Xn=Math.random().toString(36).slice(2),pt="__reactFiber$"+Xn,ri="__reactProps$"+Xn,vt="__reactContainer$"+Xn,qa="__reactEvents$"+Xn,m0="__reactListeners$"+Xn,x0="__reactHandles$"+Xn;function dn(t){var e=t[pt];if(e)return e;for(var n=t.parentNode;n;){if(e=n[vt]||n[pt]){if(n=e.alternate,e.child!==null||n!==null&&n.child!==null)for(t=lh(t);t!==null;){if(n=t[pt])return n;t=lh(t)}return e}t=n,n=t.parentNode}return null}function ii(t){return t=t[pt]||t[vt],!t||t.tag!==5&&t.tag!==6&&t.tag!==13&&t.tag!==3?null:t}function Zn(t){if(t.tag===5||t.tag===6)return t.stateNode;throw Error(T(33))}function xo(t){return t[ri]||null}var Ja=[],qn=-1;function Gt(t){return{current:t}}function K(t){0>qn||(t.current=Ja[qn],Ja[qn]=null,qn--)}function G(t,e){qn++,Ja[qn]=t.current,t.current=e}var Vt={},xe=Gt(Vt),Me=Gt(!1),un=Vt;function Jn(t,e){var n=t.type.contextTypes;if(!n)return Vt;var r=t.stateNode;if(r&&r.__reactInternalMemoizedUnmaskedChildContext===e)return r.__reactInternalMemoizedMaskedChildContext;var i={},o;for(o in n)i[o]=e[o];return r&&(t=t.stateNode,t.__reactInternalMemoizedUnmaskedChildContext=e,t.__reactInternalMemoizedMaskedChildContext=i),i}function je(t){return t=t.childContextTypes,t!=null}function yo(){K(Me),K(xe)}function ch(t,e,n){if(xe.current!==Vt)throw Error(T(168));G(xe,e),G(Me,n)}function dh(t,e,n){var r=t.stateNode;if(e=e.childContextTypes,typeof r.getChildContext!="function")return n;r=r.getChildContext();for(var i in r)if(!(i in e))throw Error(T(108,tx(t)||"Unknown",i));return Z({},n,r)}function wo(t){return t=(t=t.stateNode)&&t.__reactInternalMemoizedMergedChildContext||Vt,un=xe.current,G(xe,t),G(Me,Me.current),!0}function uh(t,e,n){var r=t.stateNode;if(!r)throw Error(T(169));n?(t=dh(t,e,un),r.__reactInternalMemoizedMergedChildContext=t,K(Me),K(xe),G(xe,t)):K(Me),G(Me,n)}var bt=null,vo=!1,el=!1;function hh(t){bt===null?bt=[t]:bt.push(t)}function y0(t){vo=!0,hh(t)}function Yt(){if(!el&&bt!==null){el=!0;var t=0,e=H;try{var n=bt;for(H=1;t<n.length;t++){var r=n[t];do r=r(!0);while(r!==null)}bt=null,vo=!1}catch(i){throw bt!==null&&(bt=bt.slice(t+1)),fu(Sa,Yt),i}finally{H=e,el=!1}}return null}var er=[],tr=0,bo=null,So=0,Ge=[],Ve=0,hn=null,St=1,kt="";function pn(t,e){er[tr++]=So,er[tr++]=bo,bo=t,So=e}function ph(t,e,n){Ge[Ve++]=St,Ge[Ve++]=kt,Ge[Ve++]=hn,hn=t;var r=St;t=kt;var i=32-et(r)-1;r&=~(1<<i),n+=1;var o=32-et(e)+i;if(30<o){var s=i-i%5;o=(r&(1<<s)-1).toString(32),r>>=s,i-=s,St=1<<32-et(e)+i|n<<i|r,kt=o+t}else St=1<<o|n<<i|r,kt=t}function tl(t){t.return!==null&&(pn(t,1),ph(t,1,0))}function nl(t){for(;t===bo;)bo=er[--tr],er[tr]=null,So=er[--tr],er[tr]=null;for(;t===hn;)hn=Ge[--Ve],Ge[Ve]=null,kt=Ge[--Ve],Ge[Ve]=null,St=Ge[--Ve],Ge[Ve]=null}var Ne=null,Fe=null,X=!1,nt=null;function fh(t,e){var n=Xe(5,null,null,0);n.elementType="DELETED",n.stateNode=e,n.return=t,e=t.deletions,e===null?(t.deletions=[n],t.flags|=16):e.push(n)}function gh(t,e){switch(t.tag){case 5:var n=t.type;return e=e.nodeType!==1||n.toLowerCase()!==e.nodeName.toLowerCase()?null:e,e!==null?(t.stateNode=e,Ne=t,Fe=Ut(e.firstChild),!0):!1;case 6:return e=t.pendingProps===""||e.nodeType!==3?null:e,e!==null?(t.stateNode=e,Ne=t,Fe=null,!0):!1;case 13:return e=e.nodeType!==8?null:e,e!==null?(n=hn!==null?{id:St,overflow:kt}:null,t.memoizedState={dehydrated:e,treeContext:n,retryLane:1073741824},n=Xe(18,null,null,0),n.stateNode=e,n.return=t,t.child=n,Ne=t,Fe=null,!0):!1;default:return!1}}function rl(t){return(t.mode&1)!==0&&(t.flags&128)===0}function il(t){if(X){var e=Fe;if(e){var n=e;if(!gh(t,e)){if(rl(t))throw Error(T(418));e=Ut(n.nextSibling);var r=Ne;e&&gh(t,e)?fh(r,n):(t.flags=t.flags&-4097|2,X=!1,Ne=t)}}else{if(rl(t))throw Error(T(418));t.flags=t.flags&-4097|2,X=!1,Ne=t}}}function mh(t){for(t=t.return;t!==null&&t.tag!==5&&t.tag!==3&&t.tag!==13;)t=t.return;Ne=t}function ko(t){if(t!==Ne)return!1;if(!X)return mh(t),X=!0,!1;var e;if((e=t.tag!==3)&&!(e=t.tag!==5)&&(e=t.type,e=e!=="head"&&e!=="body"&&!Qa(t.type,t.memoizedProps)),e&&(e=Fe)){if(rl(t))throw xh(),Error(T(418));for(;e;)fh(t,e),e=Ut(e.nextSibling)}if(mh(t),t.tag===13){if(t=t.memoizedState,t=t!==null?t.dehydrated:null,!t)throw Error(T(317));e:{for(t=t.nextSibling,e=0;t;){if(t.nodeType===8){var n=t.data;if(n==="/$"){if(e===0){Fe=Ut(t.nextSibling);break e}e--}else n!=="$"&&n!=="$!"&&n!=="$?"||e++}t=t.nextSibling}Fe=null}}else Fe=Ne?Ut(t.stateNode.nextSibling):null;return!0}function xh(){for(var t=Fe;t;)t=Ut(t.nextSibling)}function nr(){Fe=Ne=null,X=!1}function ol(t){nt===null?nt=[t]:nt.push(t)}var w0=wt.ReactCurrentBatchConfig;function oi(t,e,n){if(t=n.ref,t!==null&&typeof t!="function"&&typeof t!="object"){if(n._owner){if(n=n._owner,n){if(n.tag!==1)throw Error(T(309));var r=n.stateNode}if(!r)throw Error(T(147,t));var i=r,o=""+t;return e!==null&&e.ref!==null&&typeof e.ref=="function"&&e.ref._stringRef===o?e.ref:(e=function(s){var l=i.refs;s===null?delete l[o]:l[o]=s},e._stringRef=o,e)}if(typeof t!="string")throw Error(T(284));if(!n._owner)throw Error(T(290,t))}return t}function $o(t,e){throw t=Object.prototype.toString.call(e),Error(T(31,t==="[object Object]"?"object with keys {"+Object.keys(e).join(", ")+"}":t))}function yh(t){var e=t._init;return e(t._payload)}function wh(t){function e(y,p){if(t){var w=y.deletions;w===null?(y.deletions=[p],y.flags|=16):w.push(p)}}function n(y,p){if(!t)return null;for(;p!==null;)e(y,p),p=p.sibling;return null}function r(y,p){for(y=new Map;p!==null;)p.key!==null?y.set(p.key,p):y.set(p.index,p),p=p.sibling;return y}function i(y,p){return y=tn(y,p),y.index=0,y.sibling=null,y}function o(y,p,w){return y.index=w,t?(w=y.alternate,w!==null?(w=w.index,w<p?(y.flags|=2,p):w):(y.flags|=2,p)):(y.flags|=1048576,p)}function s(y){return t&&y.alternate===null&&(y.flags|=2),y}function l(y,p,w,S){return p===null||p.tag!==6?(p=Xl(w,y.mode,S),p.return=y,p):(p=i(p,w),p.return=y,p)}function c(y,p,w,S){var C=w.type;return C===Fn?u(y,p,w.props.children,S,w.key):p!==null&&(p.elementType===C||typeof C=="object"&&C!==null&&C.$$typeof===Lt&&yh(C)===p.type)?(S=i(p,w.props),S.ref=oi(y,p,w),S.return=y,S):(S=Ko(w.type,w.key,w.props,null,y.mode,S),S.ref=oi(y,p,w),S.return=y,S)}function d(y,p,w,S){return p===null||p.tag!==4||p.stateNode.containerInfo!==w.containerInfo||p.stateNode.implementation!==w.implementation?(p=Zl(w,y.mode,S),p.return=y,p):(p=i(p,w.children||[]),p.return=y,p)}function u(y,p,w,S,C){return p===null||p.tag!==7?(p=bn(w,y.mode,S,C),p.return=y,p):(p=i(p,w),p.return=y,p)}function f(y,p,w){if(typeof p=="string"&&p!==""||typeof p=="number")return p=Xl(""+p,y.mode,w),p.return=y,p;if(typeof p=="object"&&p!==null){switch(p.$$typeof){case Ui:return w=Ko(p.type,p.key,p.props,null,y.mode,w),w.ref=oi(y,null,p),w.return=y,w;case Nn:return p=Zl(p,y.mode,w),p.return=y,p;case Lt:var S=p._init;return f(y,S(p._payload),w)}if(Ir(p)||Ar(p))return p=bn(p,y.mode,w,null),p.return=y,p;$o(y,p)}return null}function g(y,p,w,S){var C=p!==null?p.key:null;if(typeof w=="string"&&w!==""||typeof w=="number")return C!==null?null:l(y,p,""+w,S);if(typeof w=="object"&&w!==null){switch(w.$$typeof){case Ui:return w.key===C?c(y,p,w,S):null;case Nn:return w.key===C?d(y,p,w,S):null;case Lt:return C=w._init,g(y,p,C(w._payload),S)}if(Ir(w)||Ar(w))return C!==null?null:u(y,p,w,S,null);$o(y,w)}return null}function v(y,p,w,S,C){if(typeof S=="string"&&S!==""||typeof S=="number")return y=y.get(w)||null,l(p,y,""+S,C);if(typeof S=="object"&&S!==null){switch(S.$$typeof){case Ui:return y=y.get(S.key===null?w:S.key)||null,c(p,y,S,C);case Nn:return y=y.get(S.key===null?w:S.key)||null,d(p,y,S,C);case Lt:var $=S._init;return v(y,p,w,$(S._payload),C)}if(Ir(S)||Ar(S))return y=y.get(w)||null,u(p,y,S,C,null);$o(p,S)}return null}function x(y,p,w,S){for(var C=null,$=null,k=p,j=p=0,L=null;k!==null&&j<w.length;j++){k.index>j?(L=k,k=null):L=k.sibling;var P=g(y,k,w[j],S);if(P===null){k===null&&(k=L);break}t&&k&&P.alternate===null&&e(y,k),p=o(P,p,j),$===null?C=P:$.sibling=P,$=P,k=L}if(j===w.length)return n(y,k),X&&pn(y,j),C;if(k===null){for(;j<w.length;j++)k=f(y,w[j],S),k!==null&&(p=o(k,p,j),$===null?C=k:$.sibling=k,$=k);return X&&pn(y,j),C}for(k=r(y,k);j<w.length;j++)L=v(k,y,j,w[j],S),L!==null&&(t&&L.alternate!==null&&k.delete(L.key===null?j:L.key),p=o(L,p,j),$===null?C=L:$.sibling=L,$=L);return t&&k.forEach(function(V){return e(y,V)}),X&&pn(y,j),C}function b(y,p,w,S){var C=Ar(w);if(typeof C!="function")throw Error(T(150));if(w=C.call(w),w==null)throw Error(T(151));for(var $=C=null,k=p,j=p=0,L=null,P=w.next();k!==null&&!P.done;j++,P=w.next()){k.index>j?(L=k,k=null):L=k.sibling;var V=g(y,k,P.value,S);if(V===null){k===null&&(k=L);break}t&&k&&V.alternate===null&&e(y,k),p=o(V,p,j),$===null?C=V:$.sibling=V,$=V,k=L}if(P.done)return n(y,k),X&&pn(y,j),C;if(k===null){for(;!P.done;j++,P=w.next())P=f(y,P.value,S),P!==null&&(p=o(P,p,j),$===null?C=P:$.sibling=P,$=P);return X&&pn(y,j),C}for(k=r(y,k);!P.done;j++,P=w.next())P=v(k,y,j,P.value,S),P!==null&&(t&&P.alternate!==null&&k.delete(P.key===null?j:P.key),p=o(P,p,j),$===null?C=P:$.sibling=P,$=P);return t&&k.forEach(function(de){return e(y,de)}),X&&pn(y,j),C}function M(y,p,w,S){if(typeof w=="object"&&w!==null&&w.type===Fn&&w.key===null&&(w=w.props.children),typeof w=="object"&&w!==null){switch(w.$$typeof){case Ui:e:{for(var C=w.key,$=p;$!==null;){if($.key===C){if(C=w.type,C===Fn){if($.tag===7){n(y,$.sibling),p=i($,w.props.children),p.return=y,y=p;break e}}else if($.elementType===C||typeof C=="object"&&C!==null&&C.$$typeof===Lt&&yh(C)===$.type){n(y,$.sibling),p=i($,w.props),p.ref=oi(y,$,w),p.return=y,y=p;break e}n(y,$);break}else e(y,$);$=$.sibling}w.type===Fn?(p=bn(w.props.children,y.mode,S,w.key),p.return=y,y=p):(S=Ko(w.type,w.key,w.props,null,y.mode,S),S.ref=oi(y,p,w),S.return=y,y=S)}return s(y);case Nn:e:{for($=w.key;p!==null;){if(p.key===$)if(p.tag===4&&p.stateNode.containerInfo===w.containerInfo&&p.stateNode.implementation===w.implementation){n(y,p.sibling),p=i(p,w.children||[]),p.return=y,y=p;break e}else{n(y,p);break}else e(y,p);p=p.sibling}p=Zl(w,y.mode,S),p.return=y,y=p}return s(y);case Lt:return $=w._init,M(y,p,$(w._payload),S)}if(Ir(w))return x(y,p,w,S);if(Ar(w))return b(y,p,w,S);$o(y,w)}return typeof w=="string"&&w!==""||typeof w=="number"?(w=""+w,p!==null&&p.tag===6?(n(y,p.sibling),p=i(p,w),p.return=y,y=p):(n(y,p),p=Xl(w,y.mode,S),p.return=y,y=p),s(y)):n(y,p)}return M}var rr=wh(!0),vh=wh(!1),Co=Gt(null),To=null,ir=null,sl=null;function al(){sl=ir=To=null}function ll(t){var e=Co.current;K(Co),t._currentValue=e}function cl(t,e,n){for(;t!==null;){var r=t.alternate;if((t.childLanes&e)!==e?(t.childLanes|=e,r!==null&&(r.childLanes|=e)):r!==null&&(r.childLanes&e)!==e&&(r.childLanes|=e),t===n)break;t=t.return}}function or(t,e){To=t,sl=ir=null,t=t.dependencies,t!==null&&t.firstContext!==null&&(t.lanes&e&&(Ee=!0),t.firstContext=null)}function Ye(t){var e=t._currentValue;if(sl!==t)if(t={context:t,memoizedValue:e,next:null},ir===null){if(To===null)throw Error(T(308));ir=t,To.dependencies={lanes:0,firstContext:t}}else ir=ir.next=t;return e}var fn=null;function dl(t){fn===null?fn=[t]:fn.push(t)}function bh(t,e,n,r){var i=e.interleaved;return i===null?(n.next=n,dl(e)):(n.next=i.next,i.next=n),e.interleaved=n,$t(t,r)}function $t(t,e){t.lanes|=e;var n=t.alternate;for(n!==null&&(n.lanes|=e),n=t,t=t.return;t!==null;)t.childLanes|=e,n=t.alternate,n!==null&&(n.childLanes|=e),n=t,t=t.return;return n.tag===3?n.stateNode:null}var Kt=!1;function ul(t){t.updateQueue={baseState:t.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:0},effects:null}}function Sh(t,e){t=t.updateQueue,e.updateQueue===t&&(e.updateQueue={baseState:t.baseState,firstBaseUpdate:t.firstBaseUpdate,lastBaseUpdate:t.lastBaseUpdate,shared:t.shared,effects:t.effects})}function Ct(t,e){return{eventTime:t,lane:e,tag:0,payload:null,callback:null,next:null}}function Qt(t,e,n){var r=t.updateQueue;if(r===null)return null;if(r=r.shared,F&2){var i=r.pending;return i===null?e.next=e:(e.next=i.next,i.next=e),r.pending=e,$t(t,n)}return i=r.interleaved,i===null?(e.next=e,dl(r)):(e.next=i.next,i.next=e),r.interleaved=e,$t(t,n)}function Mo(t,e,n){if(e=e.updateQueue,e!==null&&(e=e.shared,(n&4194240)!==0)){var r=e.lanes;r&=t.pendingLanes,n|=r,e.lanes=n,Ca(t,n)}}function kh(t,e){var n=t.updateQueue,r=t.alternate;if(r!==null&&(r=r.updateQueue,n===r)){var i=null,o=null;if(n=n.firstBaseUpdate,n!==null){do{var s={eventTime:n.eventTime,lane:n.lane,tag:n.tag,payload:n.payload,callback:n.callback,next:null};o===null?i=o=s:o=o.next=s,n=n.next}while(n!==null);o===null?i=o=e:o=o.next=e}else i=o=e;n={baseState:r.baseState,firstBaseUpdate:i,lastBaseUpdate:o,shared:r.shared,effects:r.effects},t.updateQueue=n;return}t=n.lastBaseUpdate,t===null?n.firstBaseUpdate=e:t.next=e,n.lastBaseUpdate=e}function jo(t,e,n,r){var i=t.updateQueue;Kt=!1;var o=i.firstBaseUpdate,s=i.lastBaseUpdate,l=i.shared.pending;if(l!==null){i.shared.pending=null;var c=l,d=c.next;c.next=null,s===null?o=d:s.next=d,s=c;var u=t.alternate;u!==null&&(u=u.updateQueue,l=u.lastBaseUpdate,l!==s&&(l===null?u.firstBaseUpdate=d:l.next=d,u.lastBaseUpdate=c))}if(o!==null){var f=i.baseState;s=0,u=d=c=null,l=o;do{var g=l.lane,v=l.eventTime;if((r&g)===g){u!==null&&(u=u.next={eventTime:v,lane:0,tag:l.tag,payload:l.payload,callback:l.callback,next:null});e:{var x=t,b=l;switch(g=e,v=n,b.tag){case 1:if(x=b.payload,typeof x=="function"){f=x.call(v,f,g);break e}f=x;break e;case 3:x.flags=x.flags&-65537|128;case 0:if(x=b.payload,g=typeof x=="function"?x.call(v,f,g):x,g==null)break e;f=Z({},f,g);break e;case 2:Kt=!0}}l.callback!==null&&l.lane!==0&&(t.flags|=64,g=i.effects,g===null?i.effects=[l]:g.push(l))}else v={eventTime:v,lane:g,tag:l.tag,payload:l.payload,callback:l.callback,next:null},u===null?(d=u=v,c=f):u=u.next=v,s|=g;if(l=l.next,l===null){if(l=i.shared.pending,l===null)break;g=l,l=g.next,g.next=null,i.lastBaseUpdate=g,i.shared.pending=null}}while(!0);if(u===null&&(c=f),i.baseState=c,i.firstBaseUpdate=d,i.lastBaseUpdate=u,e=i.shared.interleaved,e!==null){i=e;do s|=i.lane,i=i.next;while(i!==e)}else o===null&&(i.shared.lanes=0);xn|=s,t.lanes=s,t.memoizedState=f}}function $h(t,e,n){if(t=e.effects,e.effects=null,t!==null)for(e=0;e<t.length;e++){var r=t[e],i=r.callback;if(i!==null){if(r.callback=null,r=n,typeof i!="function")throw Error(T(191,i));i.call(r)}}}var si={},ft=Gt(si),ai=Gt(si),li=Gt(si);function gn(t){if(t===si)throw Error(T(174));return t}function hl(t,e){switch(G(li,e),G(ai,t),G(ft,si),t=e.nodeType,t){case 9:case 11:e=(e=e.documentElement)?e.namespaceURI:pa(null,"");break;default:t=t===8?e.parentNode:e,e=t.namespaceURI||null,t=t.tagName,e=pa(e,t)}K(ft),G(ft,e)}function sr(){K(ft),K(ai),K(li)}function Ch(t){gn(li.current);var e=gn(ft.current),n=pa(e,t.type);e!==n&&(G(ai,t),G(ft,n))}function pl(t){ai.current===t&&(K(ft),K(ai))}var q=Gt(0);function Eo(t){for(var e=t;e!==null;){if(e.tag===13){var n=e.memoizedState;if(n!==null&&(n=n.dehydrated,n===null||n.data==="$?"||n.data==="$!"))return e}else if(e.tag===19&&e.memoizedProps.revealOrder!==void 0){if(e.flags&128)return e}else if(e.child!==null){e.child.return=e,e=e.child;continue}if(e===t)break;for(;e.sibling===null;){if(e.return===null||e.return===t)return null;e=e.return}e.sibling.return=e.return,e=e.sibling}return null}var fl=[];function gl(){for(var t=0;t<fl.length;t++)fl[t]._workInProgressVersionPrimary=null;fl.length=0}var Do=wt.ReactCurrentDispatcher,ml=wt.ReactCurrentBatchConfig,mn=0,J=null,se=null,ue=null,Po=!1,ci=!1,di=0,v0=0;function ye(){throw Error(T(321))}function xl(t,e){if(e===null)return!1;for(var n=0;n<e.length&&n<t.length;n++)if(!tt(t[n],e[n]))return!1;return!0}function yl(t,e,n,r,i,o){if(mn=o,J=e,e.memoizedState=null,e.updateQueue=null,e.lanes=0,Do.current=t===null||t.memoizedState===null?$0:C0,t=n(r,i),ci){o=0;do{if(ci=!1,di=0,25<=o)throw Error(T(301));o+=1,ue=se=null,e.updateQueue=null,Do.current=T0,t=n(r,i)}while(ci)}if(Do.current=Io,e=se!==null&&se.next!==null,mn=0,ue=se=J=null,Po=!1,e)throw Error(T(300));return t}function wl(){var t=di!==0;return di=0,t}function gt(){var t={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return ue===null?J.memoizedState=ue=t:ue=ue.next=t,ue}function Ke(){if(se===null){var t=J.alternate;t=t!==null?t.memoizedState:null}else t=se.next;var e=ue===null?J.memoizedState:ue.next;if(e!==null)ue=e,se=t;else{if(t===null)throw Error(T(310));se=t,t={memoizedState:se.memoizedState,baseState:se.baseState,baseQueue:se.baseQueue,queue:se.queue,next:null},ue===null?J.memoizedState=ue=t:ue=ue.next=t}return ue}function ui(t,e){return typeof e=="function"?e(t):e}function vl(t){var e=Ke(),n=e.queue;if(n===null)throw Error(T(311));n.lastRenderedReducer=t;var r=se,i=r.baseQueue,o=n.pending;if(o!==null){if(i!==null){var s=i.next;i.next=o.next,o.next=s}r.baseQueue=i=o,n.pending=null}if(i!==null){o=i.next,r=r.baseState;var l=s=null,c=null,d=o;do{var u=d.lane;if((mn&u)===u)c!==null&&(c=c.next={lane:0,action:d.action,hasEagerState:d.hasEagerState,eagerState:d.eagerState,next:null}),r=d.hasEagerState?d.eagerState:t(r,d.action);else{var f={lane:u,action:d.action,hasEagerState:d.hasEagerState,eagerState:d.eagerState,next:null};c===null?(l=c=f,s=r):c=c.next=f,J.lanes|=u,xn|=u}d=d.next}while(d!==null&&d!==o);c===null?s=r:c.next=l,tt(r,e.memoizedState)||(Ee=!0),e.memoizedState=r,e.baseState=s,e.baseQueue=c,n.lastRenderedState=r}if(t=n.interleaved,t!==null){i=t;do o=i.lane,J.lanes|=o,xn|=o,i=i.next;while(i!==t)}else i===null&&(n.lanes=0);return[e.memoizedState,n.dispatch]}function bl(t){var e=Ke(),n=e.queue;if(n===null)throw Error(T(311));n.lastRenderedReducer=t;var r=n.dispatch,i=n.pending,o=e.memoizedState;if(i!==null){n.pending=null;var s=i=i.next;do o=t(o,s.action),s=s.next;while(s!==i);tt(o,e.memoizedState)||(Ee=!0),e.memoizedState=o,e.baseQueue===null&&(e.baseState=o),n.lastRenderedState=o}return[o,r]}function Th(){}function Mh(t,e){var n=J,r=Ke(),i=e(),o=!tt(r.memoizedState,i);if(o&&(r.memoizedState=i,Ee=!0),r=r.queue,Sl(Dh.bind(null,n,r,t),[t]),r.getSnapshot!==e||o||ue!==null&&ue.memoizedState.tag&1){if(n.flags|=2048,hi(9,Eh.bind(null,n,r,i,e),void 0,null),he===null)throw Error(T(349));mn&30||jh(n,e,i)}return i}function jh(t,e,n){t.flags|=16384,t={getSnapshot:e,value:n},e=J.updateQueue,e===null?(e={lastEffect:null,stores:null},J.updateQueue=e,e.stores=[t]):(n=e.stores,n===null?e.stores=[t]:n.push(t))}function Eh(t,e,n,r){e.value=n,e.getSnapshot=r,Ph(e)&&Ah(t)}function Dh(t,e,n){return n(function(){Ph(e)&&Ah(t)})}function Ph(t){var e=t.getSnapshot;t=t.value;try{var n=e();return!tt(t,n)}catch{return!0}}function Ah(t){var e=$t(t,1);e!==null&&st(e,t,1,-1)}function Rh(t){var e=gt();return typeof t=="function"&&(t=t()),e.memoizedState=e.baseState=t,t={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:ui,lastRenderedState:t},e.queue=t,t=t.dispatch=k0.bind(null,J,t),[e.memoizedState,t]}function hi(t,e,n,r){return t={tag:t,create:e,destroy:n,deps:r,next:null},e=J.updateQueue,e===null?(e={lastEffect:null,stores:null},J.updateQueue=e,e.lastEffect=t.next=t):(n=e.lastEffect,n===null?e.lastEffect=t.next=t:(r=n.next,n.next=t,t.next=r,e.lastEffect=t)),t}function Ih(){return Ke().memoizedState}function Ao(t,e,n,r){var i=gt();J.flags|=t,i.memoizedState=hi(1|e,n,void 0,r===void 0?null:r)}function Ro(t,e,n,r){var i=Ke();r=r===void 0?null:r;var o=void 0;if(se!==null){var s=se.memoizedState;if(o=s.destroy,r!==null&&xl(r,s.deps)){i.memoizedState=hi(e,n,o,r);return}}J.flags|=t,i.memoizedState=hi(1|e,n,o,r)}function Oh(t,e){return Ao(8390656,8,t,e)}function Sl(t,e){return Ro(2048,8,t,e)}function Lh(t,e){return Ro(4,2,t,e)}function zh(t,e){return Ro(4,4,t,e)}function Bh(t,e){if(typeof e=="function")return t=t(),e(t),function(){e(null)};if(e!=null)return t=t(),e.current=t,function(){e.current=null}}function Nh(t,e,n){return n=n!=null?n.concat([t]):null,Ro(4,4,Bh.bind(null,e,t),n)}function kl(){}function Fh(t,e){var n=Ke();e=e===void 0?null:e;var r=n.memoizedState;return r!==null&&e!==null&&xl(e,r[1])?r[0]:(n.memoizedState=[t,e],t)}function _h(t,e){var n=Ke();e=e===void 0?null:e;var r=n.memoizedState;return r!==null&&e!==null&&xl(e,r[1])?r[0]:(t=t(),n.memoizedState=[t,e],t)}function Hh(t,e,n){return mn&21?(tt(n,e)||(n=yu(),J.lanes|=n,xn|=n,t.baseState=!0),e):(t.baseState&&(t.baseState=!1,Ee=!0),t.memoizedState=n)}function b0(t,e){var n=H;H=n!==0&&4>n?n:4,t(!0);var r=ml.transition;ml.transition={};try{t(!1),e()}finally{H=n,ml.transition=r}}function Wh(){return Ke().memoizedState}function S0(t,e,n){var r=Jt(t);if(n={lane:r,action:n,hasEagerState:!1,eagerState:null,next:null},Uh(t))Gh(e,n);else if(n=bh(t,e,n,r),n!==null){var i=Te();st(n,t,r,i),Vh(n,e,r)}}function k0(t,e,n){var r=Jt(t),i={lane:r,action:n,hasEagerState:!1,eagerState:null,next:null};if(Uh(t))Gh(e,i);else{var o=t.alternate;if(t.lanes===0&&(o===null||o.lanes===0)&&(o=e.lastRenderedReducer,o!==null))try{var s=e.lastRenderedState,l=o(s,n);if(i.hasEagerState=!0,i.eagerState=l,tt(l,s)){var c=e.interleaved;c===null?(i.next=i,dl(e)):(i.next=c.next,c.next=i),e.interleaved=i;return}}catch{}finally{}n=bh(t,e,i,r),n!==null&&(i=Te(),st(n,t,r,i),Vh(n,e,r))}}function Uh(t){var e=t.alternate;return t===J||e!==null&&e===J}function Gh(t,e){ci=Po=!0;var n=t.pending;n===null?e.next=e:(e.next=n.next,n.next=e),t.pending=e}function Vh(t,e,n){if(n&4194240){var r=e.lanes;r&=t.pendingLanes,n|=r,e.lanes=n,Ca(t,n)}}var Io={readContext:Ye,useCallback:ye,useContext:ye,useEffect:ye,useImperativeHandle:ye,useInsertionEffect:ye,useLayoutEffect:ye,useMemo:ye,useReducer:ye,useRef:ye,useState:ye,useDebugValue:ye,useDeferredValue:ye,useTransition:ye,useMutableSource:ye,useSyncExternalStore:ye,useId:ye,unstable_isNewReconciler:!1},$0={readContext:Ye,useCallback:function(t,e){return gt().memoizedState=[t,e===void 0?null:e],t},useContext:Ye,useEffect:Oh,useImperativeHandle:function(t,e,n){return n=n!=null?n.concat([t]):null,Ao(4194308,4,Bh.bind(null,e,t),n)},useLayoutEffect:function(t,e){return Ao(4194308,4,t,e)},useInsertionEffect:function(t,e){return Ao(4,2,t,e)},useMemo:function(t,e){var n=gt();return e=e===void 0?null:e,t=t(),n.memoizedState=[t,e],t},useReducer:function(t,e,n){var r=gt();return e=n!==void 0?n(e):e,r.memoizedState=r.baseState=e,t={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:t,lastRenderedState:e},r.queue=t,t=t.dispatch=S0.bind(null,J,t),[r.memoizedState,t]},useRef:function(t){var e=gt();return t={current:t},e.memoizedState=t},useState:Rh,useDebugValue:kl,useDeferredValue:function(t){return gt().memoizedState=t},useTransition:function(){var t=Rh(!1),e=t[0];return t=b0.bind(null,t[1]),gt().memoizedState=t,[e,t]},useMutableSource:function(){},useSyncExternalStore:function(t,e,n){var r=J,i=gt();if(X){if(n===void 0)throw Error(T(407));n=n()}else{if(n=e(),he===null)throw Error(T(349));mn&30||jh(r,e,n)}i.memoizedState=n;var o={value:n,getSnapshot:e};return i.queue=o,Oh(Dh.bind(null,r,o,t),[t]),r.flags|=2048,hi(9,Eh.bind(null,r,o,n,e),void 0,null),n},useId:function(){var t=gt(),e=he.identifierPrefix;if(X){var n=kt,r=St;n=(r&~(1<<32-et(r)-1)).toString(32)+n,e=":"+e+"R"+n,n=di++,0<n&&(e+="H"+n.toString(32)),e+=":"}else n=v0++,e=":"+e+"r"+n.toString(32)+":";return t.memoizedState=e},unstable_isNewReconciler:!1},C0={readContext:Ye,useCallback:Fh,useContext:Ye,useEffect:Sl,useImperativeHandle:Nh,useInsertionEffect:Lh,useLayoutEffect:zh,useMemo:_h,useReducer:vl,useRef:Ih,useState:function(){return vl(ui)},useDebugValue:kl,useDeferredValue:function(t){var e=Ke();return Hh(e,se.memoizedState,t)},useTransition:function(){var t=vl(ui)[0],e=Ke().memoizedState;return[t,e]},useMutableSource:Th,useSyncExternalStore:Mh,useId:Wh,unstable_isNewReconciler:!1},T0={readContext:Ye,useCallback:Fh,useContext:Ye,useEffect:Sl,useImperativeHandle:Nh,useInsertionEffect:Lh,useLayoutEffect:zh,useMemo:_h,useReducer:bl,useRef:Ih,useState:function(){return bl(ui)},useDebugValue:kl,useDeferredValue:function(t){var e=Ke();return se===null?e.memoizedState=t:Hh(e,se.memoizedState,t)},useTransition:function(){var t=bl(ui)[0],e=Ke().memoizedState;return[t,e]},useMutableSource:Th,useSyncExternalStore:Mh,useId:Wh,unstable_isNewReconciler:!1};function rt(t,e){if(t&&t.defaultProps){e=Z({},e),t=t.defaultProps;for(var n in t)e[n]===void 0&&(e[n]=t[n]);return e}return e}function $l(t,e,n,r){e=t.memoizedState,n=n(r,e),n=n==null?e:Z({},e,n),t.memoizedState=n,t.lanes===0&&(t.updateQueue.baseState=n)}var Oo={isMounted:function(t){return(t=t._reactInternals)?cn(t)===t:!1},enqueueSetState:function(t,e,n){t=t._reactInternals;var r=Te(),i=Jt(t),o=Ct(r,i);o.payload=e,n!=null&&(o.callback=n),e=Qt(t,o,i),e!==null&&(st(e,t,i,r),Mo(e,t,i))},enqueueReplaceState:function(t,e,n){t=t._reactInternals;var r=Te(),i=Jt(t),o=Ct(r,i);o.tag=1,o.payload=e,n!=null&&(o.callback=n),e=Qt(t,o,i),e!==null&&(st(e,t,i,r),Mo(e,t,i))},enqueueForceUpdate:function(t,e){t=t._reactInternals;var n=Te(),r=Jt(t),i=Ct(n,r);i.tag=2,e!=null&&(i.callback=e),e=Qt(t,i,r),e!==null&&(st(e,t,r,n),Mo(e,t,r))}};function Yh(t,e,n,r,i,o,s){return t=t.stateNode,typeof t.shouldComponentUpdate=="function"?t.shouldComponentUpdate(r,o,s):e.prototype&&e.prototype.isPureReactComponent?!qr(n,r)||!qr(i,o):!0}function Kh(t,e,n){var r=!1,i=Vt,o=e.contextType;return typeof o=="object"&&o!==null?o=Ye(o):(i=je(e)?un:xe.current,r=e.contextTypes,o=(r=r!=null)?Jn(t,i):Vt),e=new e(n,o),t.memoizedState=e.state!==null&&e.state!==void 0?e.state:null,e.updater=Oo,t.stateNode=e,e._reactInternals=t,r&&(t=t.stateNode,t.__reactInternalMemoizedUnmaskedChildContext=i,t.__reactInternalMemoizedMaskedChildContext=o),e}function Qh(t,e,n,r){t=e.state,typeof e.componentWillReceiveProps=="function"&&e.componentWillReceiveProps(n,r),typeof e.UNSAFE_componentWillReceiveProps=="function"&&e.UNSAFE_componentWillReceiveProps(n,r),e.state!==t&&Oo.enqueueReplaceState(e,e.state,null)}function Cl(t,e,n,r){var i=t.stateNode;i.props=n,i.state=t.memoizedState,i.refs={},ul(t);var o=e.contextType;typeof o=="object"&&o!==null?i.context=Ye(o):(o=je(e)?un:xe.current,i.context=Jn(t,o)),i.state=t.memoizedState,o=e.getDerivedStateFromProps,typeof o=="function"&&($l(t,e,o,n),i.state=t.memoizedState),typeof e.getDerivedStateFromProps=="function"||typeof i.getSnapshotBeforeUpdate=="function"||typeof i.UNSAFE_componentWillMount!="function"&&typeof i.componentWillMount!="function"||(e=i.state,typeof i.componentWillMount=="function"&&i.componentWillMount(),typeof i.UNSAFE_componentWillMount=="function"&&i.UNSAFE_componentWillMount(),e!==i.state&&Oo.enqueueReplaceState(i,i.state,null),jo(t,n,i,r),i.state=t.memoizedState),typeof i.componentDidMount=="function"&&(t.flags|=4194308)}function ar(t,e){try{var n="",r=e;do n+=ex(r),r=r.return;while(r);var i=n}catch(o){i=`
Error generating stack: `+o.message+`
`+o.stack}return{value:t,source:e,stack:i,digest:null}}function Tl(t,e,n){return{value:t,source:null,stack:n??null,digest:e??null}}function Ml(t,e){try{console.error(e.value)}catch(n){setTimeout(function(){throw n})}}var M0=typeof WeakMap=="function"?WeakMap:Map;function Xh(t,e,n){n=Ct(-1,n),n.tag=3,n.payload={element:null};var r=e.value;return n.callback=function(){Ho||(Ho=!0,Hl=r),Ml(t,e)},n}function Zh(t,e,n){n=Ct(-1,n),n.tag=3;var r=t.type.getDerivedStateFromError;if(typeof r=="function"){var i=e.value;n.payload=function(){return r(i)},n.callback=function(){Ml(t,e)}}var o=t.stateNode;return o!==null&&typeof o.componentDidCatch=="function"&&(n.callback=function(){Ml(t,e),typeof r!="function"&&(Zt===null?Zt=new Set([this]):Zt.add(this));var s=e.stack;this.componentDidCatch(e.value,{componentStack:s!==null?s:""})}),n}function qh(t,e,n){var r=t.pingCache;if(r===null){r=t.pingCache=new M0;var i=new Set;r.set(e,i)}else i=r.get(e),i===void 0&&(i=new Set,r.set(e,i));i.has(n)||(i.add(n),t=_0.bind(null,t,e,n),e.then(t,t))}function Jh(t){do{var e;if((e=t.tag===13)&&(e=t.memoizedState,e=e!==null?e.dehydrated!==null:!0),e)return t;t=t.return}while(t!==null);return null}function ep(t,e,n,r,i){return t.mode&1?(t.flags|=65536,t.lanes=i,t):(t===e?t.flags|=65536:(t.flags|=128,n.flags|=131072,n.flags&=-52805,n.tag===1&&(n.alternate===null?n.tag=17:(e=Ct(-1,1),e.tag=2,Qt(n,e,1))),n.lanes|=1),t)}var j0=wt.ReactCurrentOwner,Ee=!1;function Ce(t,e,n,r){e.child=t===null?vh(e,null,n,r):rr(e,t.child,n,r)}function tp(t,e,n,r,i){n=n.render;var o=e.ref;return or(e,i),r=yl(t,e,n,r,o,i),n=wl(),t!==null&&!Ee?(e.updateQueue=t.updateQueue,e.flags&=-2053,t.lanes&=~i,Tt(t,e,i)):(X&&n&&tl(e),e.flags|=1,Ce(t,e,r,i),e.child)}function np(t,e,n,r,i){if(t===null){var o=n.type;return typeof o=="function"&&!Ql(o)&&o.defaultProps===void 0&&n.compare===null&&n.defaultProps===void 0?(e.tag=15,e.type=o,rp(t,e,o,r,i)):(t=Ko(n.type,null,r,e,e.mode,i),t.ref=e.ref,t.return=e,e.child=t)}if(o=t.child,!(t.lanes&i)){var s=o.memoizedProps;if(n=n.compare,n=n!==null?n:qr,n(s,r)&&t.ref===e.ref)return Tt(t,e,i)}return e.flags|=1,t=tn(o,r),t.ref=e.ref,t.return=e,e.child=t}function rp(t,e,n,r,i){if(t!==null){var o=t.memoizedProps;if(qr(o,r)&&t.ref===e.ref)if(Ee=!1,e.pendingProps=r=o,(t.lanes&i)!==0)t.flags&131072&&(Ee=!0);else return e.lanes=t.lanes,Tt(t,e,i)}return jl(t,e,n,r,i)}function ip(t,e,n){var r=e.pendingProps,i=r.children,o=t!==null?t.memoizedState:null;if(r.mode==="hidden")if(!(e.mode&1))e.memoizedState={baseLanes:0,cachePool:null,transitions:null},G(cr,_e),_e|=n;else{if(!(n&1073741824))return t=o!==null?o.baseLanes|n:n,e.lanes=e.childLanes=1073741824,e.memoizedState={baseLanes:t,cachePool:null,transitions:null},e.updateQueue=null,G(cr,_e),_e|=t,null;e.memoizedState={baseLanes:0,cachePool:null,transitions:null},r=o!==null?o.baseLanes:n,G(cr,_e),_e|=r}else o!==null?(r=o.baseLanes|n,e.memoizedState=null):r=n,G(cr,_e),_e|=r;return Ce(t,e,i,n),e.child}function op(t,e){var n=e.ref;(t===null&&n!==null||t!==null&&t.ref!==n)&&(e.flags|=512,e.flags|=2097152)}function jl(t,e,n,r,i){var o=je(n)?un:xe.current;return o=Jn(e,o),or(e,i),n=yl(t,e,n,r,o,i),r=wl(),t!==null&&!Ee?(e.updateQueue=t.updateQueue,e.flags&=-2053,t.lanes&=~i,Tt(t,e,i)):(X&&r&&tl(e),e.flags|=1,Ce(t,e,n,i),e.child)}function sp(t,e,n,r,i){if(je(n)){var o=!0;wo(e)}else o=!1;if(or(e,i),e.stateNode===null)zo(t,e),Kh(e,n,r),Cl(e,n,r,i),r=!0;else if(t===null){var s=e.stateNode,l=e.memoizedProps;s.props=l;var c=s.context,d=n.contextType;typeof d=="object"&&d!==null?d=Ye(d):(d=je(n)?un:xe.current,d=Jn(e,d));var u=n.getDerivedStateFromProps,f=typeof u=="function"||typeof s.getSnapshotBeforeUpdate=="function";f||typeof s.UNSAFE_componentWillReceiveProps!="function"&&typeof s.componentWillReceiveProps!="function"||(l!==r||c!==d)&&Qh(e,s,r,d),Kt=!1;var g=e.memoizedState;s.state=g,jo(e,r,s,i),c=e.memoizedState,l!==r||g!==c||Me.current||Kt?(typeof u=="function"&&($l(e,n,u,r),c=e.memoizedState),(l=Kt||Yh(e,n,l,r,g,c,d))?(f||typeof s.UNSAFE_componentWillMount!="function"&&typeof s.componentWillMount!="function"||(typeof s.componentWillMount=="function"&&s.componentWillMount(),typeof s.UNSAFE_componentWillMount=="function"&&s.UNSAFE_componentWillMount()),typeof s.componentDidMount=="function"&&(e.flags|=4194308)):(typeof s.componentDidMount=="function"&&(e.flags|=4194308),e.memoizedProps=r,e.memoizedState=c),s.props=r,s.state=c,s.context=d,r=l):(typeof s.componentDidMount=="function"&&(e.flags|=4194308),r=!1)}else{s=e.stateNode,Sh(t,e),l=e.memoizedProps,d=e.type===e.elementType?l:rt(e.type,l),s.props=d,f=e.pendingProps,g=s.context,c=n.contextType,typeof c=="object"&&c!==null?c=Ye(c):(c=je(n)?un:xe.current,c=Jn(e,c));var v=n.getDerivedStateFromProps;(u=typeof v=="function"||typeof s.getSnapshotBeforeUpdate=="function")||typeof s.UNSAFE_componentWillReceiveProps!="function"&&typeof s.componentWillReceiveProps!="function"||(l!==f||g!==c)&&Qh(e,s,r,c),Kt=!1,g=e.memoizedState,s.state=g,jo(e,r,s,i);var x=e.memoizedState;l!==f||g!==x||Me.current||Kt?(typeof v=="function"&&($l(e,n,v,r),x=e.memoizedState),(d=Kt||Yh(e,n,d,r,g,x,c)||!1)?(u||typeof s.UNSAFE_componentWillUpdate!="function"&&typeof s.componentWillUpdate!="function"||(typeof s.componentWillUpdate=="function"&&s.componentWillUpdate(r,x,c),typeof s.UNSAFE_componentWillUpdate=="function"&&s.UNSAFE_componentWillUpdate(r,x,c)),typeof s.componentDidUpdate=="function"&&(e.flags|=4),typeof s.getSnapshotBeforeUpdate=="function"&&(e.flags|=1024)):(typeof s.componentDidUpdate!="function"||l===t.memoizedProps&&g===t.memoizedState||(e.flags|=4),typeof s.getSnapshotBeforeUpdate!="function"||l===t.memoizedProps&&g===t.memoizedState||(e.flags|=1024),e.memoizedProps=r,e.memoizedState=x),s.props=r,s.state=x,s.context=c,r=d):(typeof s.componentDidUpdate!="function"||l===t.memoizedProps&&g===t.memoizedState||(e.flags|=4),typeof s.getSnapshotBeforeUpdate!="function"||l===t.memoizedProps&&g===t.memoizedState||(e.flags|=1024),r=!1)}return El(t,e,n,r,o,i)}function El(t,e,n,r,i,o){op(t,e);var s=(e.flags&128)!==0;if(!r&&!s)return i&&uh(e,n,!1),Tt(t,e,o);r=e.stateNode,j0.current=e;var l=s&&typeof n.getDerivedStateFromError!="function"?null:r.render();return e.flags|=1,t!==null&&s?(e.child=rr(e,t.child,null,o),e.child=rr(e,null,l,o)):Ce(t,e,l,o),e.memoizedState=r.state,i&&uh(e,n,!0),e.child}function ap(t){var e=t.stateNode;e.pendingContext?ch(t,e.pendingContext,e.pendingContext!==e.context):e.context&&ch(t,e.context,!1),hl(t,e.containerInfo)}function lp(t,e,n,r,i){return nr(),ol(i),e.flags|=256,Ce(t,e,n,r),e.child}var Dl={dehydrated:null,treeContext:null,retryLane:0};function Pl(t){return{baseLanes:t,cachePool:null,transitions:null}}function cp(t,e,n){var r=e.pendingProps,i=q.current,o=!1,s=(e.flags&128)!==0,l;if((l=s)||(l=t!==null&&t.memoizedState===null?!1:(i&2)!==0),l?(o=!0,e.flags&=-129):(t===null||t.memoizedState!==null)&&(i|=1),G(q,i&1),t===null)return il(e),t=e.memoizedState,t!==null&&(t=t.dehydrated,t!==null)?(e.mode&1?t.data==="$!"?e.lanes=8:e.lanes=1073741824:e.lanes=1,null):(s=r.children,t=r.fallback,o?(r=e.mode,o=e.child,s={mode:"hidden",children:s},!(r&1)&&o!==null?(o.childLanes=0,o.pendingProps=s):o=Qo(s,r,0,null),t=bn(t,r,n,null),o.return=e,t.return=e,o.sibling=t,e.child=o,e.child.memoizedState=Pl(n),e.memoizedState=Dl,t):Al(e,s));if(i=t.memoizedState,i!==null&&(l=i.dehydrated,l!==null))return E0(t,e,s,r,l,i,n);if(o){o=r.fallback,s=e.mode,i=t.child,l=i.sibling;var c={mode:"hidden",children:r.children};return!(s&1)&&e.child!==i?(r=e.child,r.childLanes=0,r.pendingProps=c,e.deletions=null):(r=tn(i,c),r.subtreeFlags=i.subtreeFlags&14680064),l!==null?o=tn(l,o):(o=bn(o,s,n,null),o.flags|=2),o.return=e,r.return=e,r.sibling=o,e.child=r,r=o,o=e.child,s=t.child.memoizedState,s=s===null?Pl(n):{baseLanes:s.baseLanes|n,cachePool:null,transitions:s.transitions},o.memoizedState=s,o.childLanes=t.childLanes&~n,e.memoizedState=Dl,r}return o=t.child,t=o.sibling,r=tn(o,{mode:"visible",children:r.children}),!(e.mode&1)&&(r.lanes=n),r.return=e,r.sibling=null,t!==null&&(n=e.deletions,n===null?(e.deletions=[t],e.flags|=16):n.push(t)),e.child=r,e.memoizedState=null,r}function Al(t,e){return e=Qo({mode:"visible",children:e},t.mode,0,null),e.return=t,t.child=e}function Lo(t,e,n,r){return r!==null&&ol(r),rr(e,t.child,null,n),t=Al(e,e.pendingProps.children),t.flags|=2,e.memoizedState=null,t}function E0(t,e,n,r,i,o,s){if(n)return e.flags&256?(e.flags&=-257,r=Tl(Error(T(422))),Lo(t,e,s,r)):e.memoizedState!==null?(e.child=t.child,e.flags|=128,null):(o=r.fallback,i=e.mode,r=Qo({mode:"visible",children:r.children},i,0,null),o=bn(o,i,s,null),o.flags|=2,r.return=e,o.return=e,r.sibling=o,e.child=r,e.mode&1&&rr(e,t.child,null,s),e.child.memoizedState=Pl(s),e.memoizedState=Dl,o);if(!(e.mode&1))return Lo(t,e,s,null);if(i.data==="$!"){if(r=i.nextSibling&&i.nextSibling.dataset,r)var l=r.dgst;return r=l,o=Error(T(419)),r=Tl(o,r,void 0),Lo(t,e,s,r)}if(l=(s&t.childLanes)!==0,Ee||l){if(r=he,r!==null){switch(s&-s){case 4:i=2;break;case 16:i=8;break;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:i=32;break;case 536870912:i=268435456;break;default:i=0}i=i&(r.suspendedLanes|s)?0:i,i!==0&&i!==o.retryLane&&(o.retryLane=i,$t(t,i),st(r,t,i,-1))}return Kl(),r=Tl(Error(T(421))),Lo(t,e,s,r)}return i.data==="$?"?(e.flags|=128,e.child=t.child,e=H0.bind(null,t),i._reactRetry=e,null):(t=o.treeContext,Fe=Ut(i.nextSibling),Ne=e,X=!0,nt=null,t!==null&&(Ge[Ve++]=St,Ge[Ve++]=kt,Ge[Ve++]=hn,St=t.id,kt=t.overflow,hn=e),e=Al(e,r.children),e.flags|=4096,e)}function dp(t,e,n){t.lanes|=e;var r=t.alternate;r!==null&&(r.lanes|=e),cl(t.return,e,n)}function Rl(t,e,n,r,i){var o=t.memoizedState;o===null?t.memoizedState={isBackwards:e,rendering:null,renderingStartTime:0,last:r,tail:n,tailMode:i}:(o.isBackwards=e,o.rendering=null,o.renderingStartTime=0,o.last=r,o.tail=n,o.tailMode=i)}function up(t,e,n){var r=e.pendingProps,i=r.revealOrder,o=r.tail;if(Ce(t,e,r.children,n),r=q.current,r&2)r=r&1|2,e.flags|=128;else{if(t!==null&&t.flags&128)e:for(t=e.child;t!==null;){if(t.tag===13)t.memoizedState!==null&&dp(t,n,e);else if(t.tag===19)dp(t,n,e);else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break e;for(;t.sibling===null;){if(t.return===null||t.return===e)break e;t=t.return}t.sibling.return=t.return,t=t.sibling}r&=1}if(G(q,r),!(e.mode&1))e.memoizedState=null;else switch(i){case"forwards":for(n=e.child,i=null;n!==null;)t=n.alternate,t!==null&&Eo(t)===null&&(i=n),n=n.sibling;n=i,n===null?(i=e.child,e.child=null):(i=n.sibling,n.sibling=null),Rl(e,!1,i,n,o);break;case"backwards":for(n=null,i=e.child,e.child=null;i!==null;){if(t=i.alternate,t!==null&&Eo(t)===null){e.child=i;break}t=i.sibling,i.sibling=n,n=i,i=t}Rl(e,!0,n,null,o);break;case"together":Rl(e,!1,null,null,void 0);break;default:e.memoizedState=null}return e.child}function zo(t,e){!(e.mode&1)&&t!==null&&(t.alternate=null,e.alternate=null,e.flags|=2)}function Tt(t,e,n){if(t!==null&&(e.dependencies=t.dependencies),xn|=e.lanes,!(n&e.childLanes))return null;if(t!==null&&e.child!==t.child)throw Error(T(153));if(e.child!==null){for(t=e.child,n=tn(t,t.pendingProps),e.child=n,n.return=e;t.sibling!==null;)t=t.sibling,n=n.sibling=tn(t,t.pendingProps),n.return=e;n.sibling=null}return e.child}function D0(t,e,n){switch(e.tag){case 3:ap(e),nr();break;case 5:Ch(e);break;case 1:je(e.type)&&wo(e);break;case 4:hl(e,e.stateNode.containerInfo);break;case 10:var r=e.type._context,i=e.memoizedProps.value;G(Co,r._currentValue),r._currentValue=i;break;case 13:if(r=e.memoizedState,r!==null)return r.dehydrated!==null?(G(q,q.current&1),e.flags|=128,null):n&e.child.childLanes?cp(t,e,n):(G(q,q.current&1),t=Tt(t,e,n),t!==null?t.sibling:null);G(q,q.current&1);break;case 19:if(r=(n&e.childLanes)!==0,t.flags&128){if(r)return up(t,e,n);e.flags|=128}if(i=e.memoizedState,i!==null&&(i.rendering=null,i.tail=null,i.lastEffect=null),G(q,q.current),r)break;return null;case 22:case 23:return e.lanes=0,ip(t,e,n)}return Tt(t,e,n)}var hp,Il,pp,fp;hp=function(t,e){for(var n=e.child;n!==null;){if(n.tag===5||n.tag===6)t.appendChild(n.stateNode);else if(n.tag!==4&&n.child!==null){n.child.return=n,n=n.child;continue}if(n===e)break;for(;n.sibling===null;){if(n.return===null||n.return===e)return;n=n.return}n.sibling.return=n.return,n=n.sibling}},Il=function(){},pp=function(t,e,n,r){var i=t.memoizedProps;if(i!==r){t=e.stateNode,gn(ft.current);var o=null;switch(n){case"input":i=ca(t,i),r=ca(t,r),o=[];break;case"select":i=Z({},i,{value:void 0}),r=Z({},r,{value:void 0}),o=[];break;case"textarea":i=ha(t,i),r=ha(t,r),o=[];break;default:typeof i.onClick!="function"&&typeof r.onClick=="function"&&(t.onclick=mo)}fa(n,r);var s;n=null;for(d in i)if(!r.hasOwnProperty(d)&&i.hasOwnProperty(d)&&i[d]!=null)if(d==="style"){var l=i[d];for(s in l)l.hasOwnProperty(s)&&(n||(n={}),n[s]="")}else d!=="dangerouslySetInnerHTML"&&d!=="children"&&d!=="suppressContentEditableWarning"&&d!=="suppressHydrationWarning"&&d!=="autoFocus"&&(Pr.hasOwnProperty(d)?o||(o=[]):(o=o||[]).push(d,null));for(d in r){var c=r[d];if(l=i!=null?i[d]:void 0,r.hasOwnProperty(d)&&c!==l&&(c!=null||l!=null))if(d==="style")if(l){for(s in l)!l.hasOwnProperty(s)||c&&c.hasOwnProperty(s)||(n||(n={}),n[s]="");for(s in c)c.hasOwnProperty(s)&&l[s]!==c[s]&&(n||(n={}),n[s]=c[s])}else n||(o||(o=[]),o.push(d,n)),n=c;else d==="dangerouslySetInnerHTML"?(c=c?c.__html:void 0,l=l?l.__html:void 0,c!=null&&l!==c&&(o=o||[]).push(d,c)):d==="children"?typeof c!="string"&&typeof c!="number"||(o=o||[]).push(d,""+c):d!=="suppressContentEditableWarning"&&d!=="suppressHydrationWarning"&&(Pr.hasOwnProperty(d)?(c!=null&&d==="onScroll"&&Y("scroll",t),o||l===c||(o=[])):(o=o||[]).push(d,c))}n&&(o=o||[]).push("style",n);var d=o;(e.updateQueue=d)&&(e.flags|=4)}},fp=function(t,e,n,r){n!==r&&(e.flags|=4)};function pi(t,e){if(!X)switch(t.tailMode){case"hidden":e=t.tail;for(var n=null;e!==null;)e.alternate!==null&&(n=e),e=e.sibling;n===null?t.tail=null:n.sibling=null;break;case"collapsed":n=t.tail;for(var r=null;n!==null;)n.alternate!==null&&(r=n),n=n.sibling;r===null?e||t.tail===null?t.tail=null:t.tail.sibling=null:r.sibling=null}}function we(t){var e=t.alternate!==null&&t.alternate.child===t.child,n=0,r=0;if(e)for(var i=t.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags&14680064,r|=i.flags&14680064,i.return=t,i=i.sibling;else for(i=t.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags,r|=i.flags,i.return=t,i=i.sibling;return t.subtreeFlags|=r,t.childLanes=n,e}function P0(t,e,n){var r=e.pendingProps;switch(nl(e),e.tag){case 2:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return we(e),null;case 1:return je(e.type)&&yo(),we(e),null;case 3:return r=e.stateNode,sr(),K(Me),K(xe),gl(),r.pendingContext&&(r.context=r.pendingContext,r.pendingContext=null),(t===null||t.child===null)&&(ko(e)?e.flags|=4:t===null||t.memoizedState.isDehydrated&&!(e.flags&256)||(e.flags|=1024,nt!==null&&(Gl(nt),nt=null))),Il(t,e),we(e),null;case 5:pl(e);var i=gn(li.current);if(n=e.type,t!==null&&e.stateNode!=null)pp(t,e,n,r,i),t.ref!==e.ref&&(e.flags|=512,e.flags|=2097152);else{if(!r){if(e.stateNode===null)throw Error(T(166));return we(e),null}if(t=gn(ft.current),ko(e)){r=e.stateNode,n=e.type;var o=e.memoizedProps;switch(r[pt]=e,r[ri]=o,t=(e.mode&1)!==0,n){case"dialog":Y("cancel",r),Y("close",r);break;case"iframe":case"object":case"embed":Y("load",r);break;case"video":case"audio":for(i=0;i<ei.length;i++)Y(ei[i],r);break;case"source":Y("error",r);break;case"img":case"image":case"link":Y("error",r),Y("load",r);break;case"details":Y("toggle",r);break;case"input":Kd(r,o),Y("invalid",r);break;case"select":r._wrapperState={wasMultiple:!!o.multiple},Y("invalid",r);break;case"textarea":Zd(r,o),Y("invalid",r)}fa(n,o),i=null;for(var s in o)if(o.hasOwnProperty(s)){var l=o[s];s==="children"?typeof l=="string"?r.textContent!==l&&(o.suppressHydrationWarning!==!0&&go(r.textContent,l,t),i=["children",l]):typeof l=="number"&&r.textContent!==""+l&&(o.suppressHydrationWarning!==!0&&go(r.textContent,l,t),i=["children",""+l]):Pr.hasOwnProperty(s)&&l!=null&&s==="onScroll"&&Y("scroll",r)}switch(n){case"input":Gi(r),Xd(r,o,!0);break;case"textarea":Gi(r),Jd(r);break;case"select":case"option":break;default:typeof o.onClick=="function"&&(r.onclick=mo)}r=i,e.updateQueue=r,r!==null&&(e.flags|=4)}else{s=i.nodeType===9?i:i.ownerDocument,t==="http://www.w3.org/1999/xhtml"&&(t=eu(n)),t==="http://www.w3.org/1999/xhtml"?n==="script"?(t=s.createElement("div"),t.innerHTML="<script><\/script>",t=t.removeChild(t.firstChild)):typeof r.is=="string"?t=s.createElement(n,{is:r.is}):(t=s.createElement(n),n==="select"&&(s=t,r.multiple?s.multiple=!0:r.size&&(s.size=r.size))):t=s.createElementNS(t,n),t[pt]=e,t[ri]=r,hp(t,e,!1,!1),e.stateNode=t;e:{switch(s=ga(n,r),n){case"dialog":Y("cancel",t),Y("close",t),i=r;break;case"iframe":case"object":case"embed":Y("load",t),i=r;break;case"video":case"audio":for(i=0;i<ei.length;i++)Y(ei[i],t);i=r;break;case"source":Y("error",t),i=r;break;case"img":case"image":case"link":Y("error",t),Y("load",t),i=r;break;case"details":Y("toggle",t),i=r;break;case"input":Kd(t,r),i=ca(t,r),Y("invalid",t);break;case"option":i=r;break;case"select":t._wrapperState={wasMultiple:!!r.multiple},i=Z({},r,{value:void 0}),Y("invalid",t);break;case"textarea":Zd(t,r),i=ha(t,r),Y("invalid",t);break;default:i=r}fa(n,i),l=i;for(o in l)if(l.hasOwnProperty(o)){var c=l[o];o==="style"?ru(t,c):o==="dangerouslySetInnerHTML"?(c=c?c.__html:void 0,c!=null&&tu(t,c)):o==="children"?typeof c=="string"?(n!=="textarea"||c!=="")&&Or(t,c):typeof c=="number"&&Or(t,""+c):o!=="suppressContentEditableWarning"&&o!=="suppressHydrationWarning"&&o!=="autoFocus"&&(Pr.hasOwnProperty(o)?c!=null&&o==="onScroll"&&Y("scroll",t):c!=null&&qs(t,o,c,s))}switch(n){case"input":Gi(t),Xd(t,r,!1);break;case"textarea":Gi(t),Jd(t);break;case"option":r.value!=null&&t.setAttribute("value",""+zt(r.value));break;case"select":t.multiple=!!r.multiple,o=r.value,o!=null?_n(t,!!r.multiple,o,!1):r.defaultValue!=null&&_n(t,!!r.multiple,r.defaultValue,!0);break;default:typeof i.onClick=="function"&&(t.onclick=mo)}switch(n){case"button":case"input":case"select":case"textarea":r=!!r.autoFocus;break e;case"img":r=!0;break e;default:r=!1}}r&&(e.flags|=4)}e.ref!==null&&(e.flags|=512,e.flags|=2097152)}return we(e),null;case 6:if(t&&e.stateNode!=null)fp(t,e,t.memoizedProps,r);else{if(typeof r!="string"&&e.stateNode===null)throw Error(T(166));if(n=gn(li.current),gn(ft.current),ko(e)){if(r=e.stateNode,n=e.memoizedProps,r[pt]=e,(o=r.nodeValue!==n)&&(t=Ne,t!==null))switch(t.tag){case 3:go(r.nodeValue,n,(t.mode&1)!==0);break;case 5:t.memoizedProps.suppressHydrationWarning!==!0&&go(r.nodeValue,n,(t.mode&1)!==0)}o&&(e.flags|=4)}else r=(n.nodeType===9?n:n.ownerDocument).createTextNode(r),r[pt]=e,e.stateNode=r}return we(e),null;case 13:if(K(q),r=e.memoizedState,t===null||t.memoizedState!==null&&t.memoizedState.dehydrated!==null){if(X&&Fe!==null&&e.mode&1&&!(e.flags&128))xh(),nr(),e.flags|=98560,o=!1;else if(o=ko(e),r!==null&&r.dehydrated!==null){if(t===null){if(!o)throw Error(T(318));if(o=e.memoizedState,o=o!==null?o.dehydrated:null,!o)throw Error(T(317));o[pt]=e}else nr(),!(e.flags&128)&&(e.memoizedState=null),e.flags|=4;we(e),o=!1}else nt!==null&&(Gl(nt),nt=null),o=!0;if(!o)return e.flags&65536?e:null}return e.flags&128?(e.lanes=n,e):(r=r!==null,r!==(t!==null&&t.memoizedState!==null)&&r&&(e.child.flags|=8192,e.mode&1&&(t===null||q.current&1?ae===0&&(ae=3):Kl())),e.updateQueue!==null&&(e.flags|=4),we(e),null);case 4:return sr(),Il(t,e),t===null&&ti(e.stateNode.containerInfo),we(e),null;case 10:return ll(e.type._context),we(e),null;case 17:return je(e.type)&&yo(),we(e),null;case 19:if(K(q),o=e.memoizedState,o===null)return we(e),null;if(r=(e.flags&128)!==0,s=o.rendering,s===null)if(r)pi(o,!1);else{if(ae!==0||t!==null&&t.flags&128)for(t=e.child;t!==null;){if(s=Eo(t),s!==null){for(e.flags|=128,pi(o,!1),r=s.updateQueue,r!==null&&(e.updateQueue=r,e.flags|=4),e.subtreeFlags=0,r=n,n=e.child;n!==null;)o=n,t=r,o.flags&=14680066,s=o.alternate,s===null?(o.childLanes=0,o.lanes=t,o.child=null,o.subtreeFlags=0,o.memoizedProps=null,o.memoizedState=null,o.updateQueue=null,o.dependencies=null,o.stateNode=null):(o.childLanes=s.childLanes,o.lanes=s.lanes,o.child=s.child,o.subtreeFlags=0,o.deletions=null,o.memoizedProps=s.memoizedProps,o.memoizedState=s.memoizedState,o.updateQueue=s.updateQueue,o.type=s.type,t=s.dependencies,o.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext}),n=n.sibling;return G(q,q.current&1|2),e.child}t=t.sibling}o.tail!==null&&ne()>dr&&(e.flags|=128,r=!0,pi(o,!1),e.lanes=4194304)}else{if(!r)if(t=Eo(s),t!==null){if(e.flags|=128,r=!0,n=t.updateQueue,n!==null&&(e.updateQueue=n,e.flags|=4),pi(o,!0),o.tail===null&&o.tailMode==="hidden"&&!s.alternate&&!X)return we(e),null}else 2*ne()-o.renderingStartTime>dr&&n!==1073741824&&(e.flags|=128,r=!0,pi(o,!1),e.lanes=4194304);o.isBackwards?(s.sibling=e.child,e.child=s):(n=o.last,n!==null?n.sibling=s:e.child=s,o.last=s)}return o.tail!==null?(e=o.tail,o.rendering=e,o.tail=e.sibling,o.renderingStartTime=ne(),e.sibling=null,n=q.current,G(q,r?n&1|2:n&1),e):(we(e),null);case 22:case 23:return Yl(),r=e.memoizedState!==null,t!==null&&t.memoizedState!==null!==r&&(e.flags|=8192),r&&e.mode&1?_e&1073741824&&(we(e),e.subtreeFlags&6&&(e.flags|=8192)):we(e),null;case 24:return null;case 25:return null}throw Error(T(156,e.tag))}function A0(t,e){switch(nl(e),e.tag){case 1:return je(e.type)&&yo(),t=e.flags,t&65536?(e.flags=t&-65537|128,e):null;case 3:return sr(),K(Me),K(xe),gl(),t=e.flags,t&65536&&!(t&128)?(e.flags=t&-65537|128,e):null;case 5:return pl(e),null;case 13:if(K(q),t=e.memoizedState,t!==null&&t.dehydrated!==null){if(e.alternate===null)throw Error(T(340));nr()}return t=e.flags,t&65536?(e.flags=t&-65537|128,e):null;case 19:return K(q),null;case 4:return sr(),null;case 10:return ll(e.type._context),null;case 22:case 23:return Yl(),null;case 24:return null;default:return null}}var Bo=!1,ve=!1,R0=typeof WeakSet=="function"?WeakSet:Set,D=null;function lr(t,e){var n=t.ref;if(n!==null)if(typeof n=="function")try{n(null)}catch(r){te(t,e,r)}else n.current=null}function Ol(t,e,n){try{n()}catch(r){te(t,e,r)}}var gp=!1;function I0(t,e){if(Ya=ro,t=Yu(),Na(t)){if("selectionStart"in t)var n={start:t.selectionStart,end:t.selectionEnd};else e:{n=(n=t.ownerDocument)&&n.defaultView||window;var r=n.getSelection&&n.getSelection();if(r&&r.rangeCount!==0){n=r.anchorNode;var i=r.anchorOffset,o=r.focusNode;r=r.focusOffset;try{n.nodeType,o.nodeType}catch{n=null;break e}var s=0,l=-1,c=-1,d=0,u=0,f=t,g=null;t:for(;;){for(var v;f!==n||i!==0&&f.nodeType!==3||(l=s+i),f!==o||r!==0&&f.nodeType!==3||(c=s+r),f.nodeType===3&&(s+=f.nodeValue.length),(v=f.firstChild)!==null;)g=f,f=v;for(;;){if(f===t)break t;if(g===n&&++d===i&&(l=s),g===o&&++u===r&&(c=s),(v=f.nextSibling)!==null)break;f=g,g=f.parentNode}f=v}n=l===-1||c===-1?null:{start:l,end:c}}else n=null}n=n||{start:0,end:0}}else n=null;for(Ka={focusedElem:t,selectionRange:n},ro=!1,D=e;D!==null;)if(e=D,t=e.child,(e.subtreeFlags&1028)!==0&&t!==null)t.return=e,D=t;else for(;D!==null;){e=D;try{var x=e.alternate;if(e.flags&1024)switch(e.tag){case 0:case 11:case 15:break;case 1:if(x!==null){var b=x.memoizedProps,M=x.memoizedState,y=e.stateNode,p=y.getSnapshotBeforeUpdate(e.elementType===e.type?b:rt(e.type,b),M);y.__reactInternalSnapshotBeforeUpdate=p}break;case 3:var w=e.stateNode.containerInfo;w.nodeType===1?w.textContent="":w.nodeType===9&&w.documentElement&&w.removeChild(w.documentElement);break;case 5:case 6:case 4:case 17:break;default:throw Error(T(163))}}catch(S){te(e,e.return,S)}if(t=e.sibling,t!==null){t.return=e.return,D=t;break}D=e.return}return x=gp,gp=!1,x}function fi(t,e,n){var r=e.updateQueue;if(r=r!==null?r.lastEffect:null,r!==null){var i=r=r.next;do{if((i.tag&t)===t){var o=i.destroy;i.destroy=void 0,o!==void 0&&Ol(e,n,o)}i=i.next}while(i!==r)}}function No(t,e){if(e=e.updateQueue,e=e!==null?e.lastEffect:null,e!==null){var n=e=e.next;do{if((n.tag&t)===t){var r=n.create;n.destroy=r()}n=n.next}while(n!==e)}}function Ll(t){var e=t.ref;if(e!==null){var n=t.stateNode;switch(t.tag){case 5:t=n;break;default:t=n}typeof e=="function"?e(t):e.current=t}}function mp(t){var e=t.alternate;e!==null&&(t.alternate=null,mp(e)),t.child=null,t.deletions=null,t.sibling=null,t.tag===5&&(e=t.stateNode,e!==null&&(delete e[pt],delete e[ri],delete e[qa],delete e[m0],delete e[x0])),t.stateNode=null,t.return=null,t.dependencies=null,t.memoizedProps=null,t.memoizedState=null,t.pendingProps=null,t.stateNode=null,t.updateQueue=null}function xp(t){return t.tag===5||t.tag===3||t.tag===4}function yp(t){e:for(;;){for(;t.sibling===null;){if(t.return===null||xp(t.return))return null;t=t.return}for(t.sibling.return=t.return,t=t.sibling;t.tag!==5&&t.tag!==6&&t.tag!==18;){if(t.flags&2||t.child===null||t.tag===4)continue e;t.child.return=t,t=t.child}if(!(t.flags&2))return t.stateNode}}function zl(t,e,n){var r=t.tag;if(r===5||r===6)t=t.stateNode,e?n.nodeType===8?n.parentNode.insertBefore(t,e):n.insertBefore(t,e):(n.nodeType===8?(e=n.parentNode,e.insertBefore(t,n)):(e=n,e.appendChild(t)),n=n._reactRootContainer,n!=null||e.onclick!==null||(e.onclick=mo));else if(r!==4&&(t=t.child,t!==null))for(zl(t,e,n),t=t.sibling;t!==null;)zl(t,e,n),t=t.sibling}function Bl(t,e,n){var r=t.tag;if(r===5||r===6)t=t.stateNode,e?n.insertBefore(t,e):n.appendChild(t);else if(r!==4&&(t=t.child,t!==null))for(Bl(t,e,n),t=t.sibling;t!==null;)Bl(t,e,n),t=t.sibling}var fe=null,it=!1;function Xt(t,e,n){for(n=n.child;n!==null;)wp(t,e,n),n=n.sibling}function wp(t,e,n){if(ht&&typeof ht.onCommitFiberUnmount=="function")try{ht.onCommitFiberUnmount(Zi,n)}catch{}switch(n.tag){case 5:ve||lr(n,e);case 6:var r=fe,i=it;fe=null,Xt(t,e,n),fe=r,it=i,fe!==null&&(it?(t=fe,n=n.stateNode,t.nodeType===8?t.parentNode.removeChild(n):t.removeChild(n)):fe.removeChild(n.stateNode));break;case 18:fe!==null&&(it?(t=fe,n=n.stateNode,t.nodeType===8?Za(t.parentNode,n):t.nodeType===1&&Za(t,n),Vr(t)):Za(fe,n.stateNode));break;case 4:r=fe,i=it,fe=n.stateNode.containerInfo,it=!0,Xt(t,e,n),fe=r,it=i;break;case 0:case 11:case 14:case 15:if(!ve&&(r=n.updateQueue,r!==null&&(r=r.lastEffect,r!==null))){i=r=r.next;do{var o=i,s=o.destroy;o=o.tag,s!==void 0&&(o&2||o&4)&&Ol(n,e,s),i=i.next}while(i!==r)}Xt(t,e,n);break;case 1:if(!ve&&(lr(n,e),r=n.stateNode,typeof r.componentWillUnmount=="function"))try{r.props=n.memoizedProps,r.state=n.memoizedState,r.componentWillUnmount()}catch(l){te(n,e,l)}Xt(t,e,n);break;case 21:Xt(t,e,n);break;case 22:n.mode&1?(ve=(r=ve)||n.memoizedState!==null,Xt(t,e,n),ve=r):Xt(t,e,n);break;default:Xt(t,e,n)}}function vp(t){var e=t.updateQueue;if(e!==null){t.updateQueue=null;var n=t.stateNode;n===null&&(n=t.stateNode=new R0),e.forEach(function(r){var i=W0.bind(null,t,r);n.has(r)||(n.add(r),r.then(i,i))})}}function ot(t,e){var n=e.deletions;if(n!==null)for(var r=0;r<n.length;r++){var i=n[r];try{var o=t,s=e,l=s;e:for(;l!==null;){switch(l.tag){case 5:fe=l.stateNode,it=!1;break e;case 3:fe=l.stateNode.containerInfo,it=!0;break e;case 4:fe=l.stateNode.containerInfo,it=!0;break e}l=l.return}if(fe===null)throw Error(T(160));wp(o,s,i),fe=null,it=!1;var c=i.alternate;c!==null&&(c.return=null),i.return=null}catch(d){te(i,e,d)}}if(e.subtreeFlags&12854)for(e=e.child;e!==null;)bp(e,t),e=e.sibling}function bp(t,e){var n=t.alternate,r=t.flags;switch(t.tag){case 0:case 11:case 14:case 15:if(ot(e,t),mt(t),r&4){try{fi(3,t,t.return),No(3,t)}catch(b){te(t,t.return,b)}try{fi(5,t,t.return)}catch(b){te(t,t.return,b)}}break;case 1:ot(e,t),mt(t),r&512&&n!==null&&lr(n,n.return);break;case 5:if(ot(e,t),mt(t),r&512&&n!==null&&lr(n,n.return),t.flags&32){var i=t.stateNode;try{Or(i,"")}catch(b){te(t,t.return,b)}}if(r&4&&(i=t.stateNode,i!=null)){var o=t.memoizedProps,s=n!==null?n.memoizedProps:o,l=t.type,c=t.updateQueue;if(t.updateQueue=null,c!==null)try{l==="input"&&o.type==="radio"&&o.name!=null&&Qd(i,o),ga(l,s);var d=ga(l,o);for(s=0;s<c.length;s+=2){var u=c[s],f=c[s+1];u==="style"?ru(i,f):u==="dangerouslySetInnerHTML"?tu(i,f):u==="children"?Or(i,f):qs(i,u,f,d)}switch(l){case"input":da(i,o);break;case"textarea":qd(i,o);break;case"select":var g=i._wrapperState.wasMultiple;i._wrapperState.wasMultiple=!!o.multiple;var v=o.value;v!=null?_n(i,!!o.multiple,v,!1):g!==!!o.multiple&&(o.defaultValue!=null?_n(i,!!o.multiple,o.defaultValue,!0):_n(i,!!o.multiple,o.multiple?[]:"",!1))}i[ri]=o}catch(b){te(t,t.return,b)}}break;case 6:if(ot(e,t),mt(t),r&4){if(t.stateNode===null)throw Error(T(162));i=t.stateNode,o=t.memoizedProps;try{i.nodeValue=o}catch(b){te(t,t.return,b)}}break;case 3:if(ot(e,t),mt(t),r&4&&n!==null&&n.memoizedState.isDehydrated)try{Vr(e.containerInfo)}catch(b){te(t,t.return,b)}break;case 4:ot(e,t),mt(t);break;case 13:ot(e,t),mt(t),i=t.child,i.flags&8192&&(o=i.memoizedState!==null,i.stateNode.isHidden=o,!o||i.alternate!==null&&i.alternate.memoizedState!==null||(_l=ne())),r&4&&vp(t);break;case 22:if(u=n!==null&&n.memoizedState!==null,t.mode&1?(ve=(d=ve)||u,ot(e,t),ve=d):ot(e,t),mt(t),r&8192){if(d=t.memoizedState!==null,(t.stateNode.isHidden=d)&&!u&&t.mode&1)for(D=t,u=t.child;u!==null;){for(f=D=u;D!==null;){switch(g=D,v=g.child,g.tag){case 0:case 11:case 14:case 15:fi(4,g,g.return);break;case 1:lr(g,g.return);var x=g.stateNode;if(typeof x.componentWillUnmount=="function"){r=g,n=g.return;try{e=r,x.props=e.memoizedProps,x.state=e.memoizedState,x.componentWillUnmount()}catch(b){te(r,n,b)}}break;case 5:lr(g,g.return);break;case 22:if(g.memoizedState!==null){$p(f);continue}}v!==null?(v.return=g,D=v):$p(f)}u=u.sibling}e:for(u=null,f=t;;){if(f.tag===5){if(u===null){u=f;try{i=f.stateNode,d?(o=i.style,typeof o.setProperty=="function"?o.setProperty("display","none","important"):o.display="none"):(l=f.stateNode,c=f.memoizedProps.style,s=c!=null&&c.hasOwnProperty("display")?c.display:null,l.style.display=nu("display",s))}catch(b){te(t,t.return,b)}}}else if(f.tag===6){if(u===null)try{f.stateNode.nodeValue=d?"":f.memoizedProps}catch(b){te(t,t.return,b)}}else if((f.tag!==22&&f.tag!==23||f.memoizedState===null||f===t)&&f.child!==null){f.child.return=f,f=f.child;continue}if(f===t)break e;for(;f.sibling===null;){if(f.return===null||f.return===t)break e;u===f&&(u=null),f=f.return}u===f&&(u=null),f.sibling.return=f.return,f=f.sibling}}break;case 19:ot(e,t),mt(t),r&4&&vp(t);break;case 21:break;default:ot(e,t),mt(t)}}function mt(t){var e=t.flags;if(e&2){try{e:{for(var n=t.return;n!==null;){if(xp(n)){var r=n;break e}n=n.return}throw Error(T(160))}switch(r.tag){case 5:var i=r.stateNode;r.flags&32&&(Or(i,""),r.flags&=-33);var o=yp(t);Bl(t,o,i);break;case 3:case 4:var s=r.stateNode.containerInfo,l=yp(t);zl(t,l,s);break;default:throw Error(T(161))}}catch(c){te(t,t.return,c)}t.flags&=-3}e&4096&&(t.flags&=-4097)}function O0(t,e,n){D=t,Sp(t)}function Sp(t,e,n){for(var r=(t.mode&1)!==0;D!==null;){var i=D,o=i.child;if(i.tag===22&&r){var s=i.memoizedState!==null||Bo;if(!s){var l=i.alternate,c=l!==null&&l.memoizedState!==null||ve;l=Bo;var d=ve;if(Bo=s,(ve=c)&&!d)for(D=i;D!==null;)s=D,c=s.child,s.tag===22&&s.memoizedState!==null?Cp(i):c!==null?(c.return=s,D=c):Cp(i);for(;o!==null;)D=o,Sp(o),o=o.sibling;D=i,Bo=l,ve=d}kp(t)}else i.subtreeFlags&8772&&o!==null?(o.return=i,D=o):kp(t)}}function kp(t){for(;D!==null;){var e=D;if(e.flags&8772){var n=e.alternate;try{if(e.flags&8772)switch(e.tag){case 0:case 11:case 15:ve||No(5,e);break;case 1:var r=e.stateNode;if(e.flags&4&&!ve)if(n===null)r.componentDidMount();else{var i=e.elementType===e.type?n.memoizedProps:rt(e.type,n.memoizedProps);r.componentDidUpdate(i,n.memoizedState,r.__reactInternalSnapshotBeforeUpdate)}var o=e.updateQueue;o!==null&&$h(e,o,r);break;case 3:var s=e.updateQueue;if(s!==null){if(n=null,e.child!==null)switch(e.child.tag){case 5:n=e.child.stateNode;break;case 1:n=e.child.stateNode}$h(e,s,n)}break;case 5:var l=e.stateNode;if(n===null&&e.flags&4){n=l;var c=e.memoizedProps;switch(e.type){case"button":case"input":case"select":case"textarea":c.autoFocus&&n.focus();break;case"img":c.src&&(n.src=c.src)}}break;case 6:break;case 4:break;case 12:break;case 13:if(e.memoizedState===null){var d=e.alternate;if(d!==null){var u=d.memoizedState;if(u!==null){var f=u.dehydrated;f!==null&&Vr(f)}}}break;case 19:case 17:case 21:case 22:case 23:case 25:break;default:throw Error(T(163))}ve||e.flags&512&&Ll(e)}catch(g){te(e,e.return,g)}}if(e===t){D=null;break}if(n=e.sibling,n!==null){n.return=e.return,D=n;break}D=e.return}}function $p(t){for(;D!==null;){var e=D;if(e===t){D=null;break}var n=e.sibling;if(n!==null){n.return=e.return,D=n;break}D=e.return}}function Cp(t){for(;D!==null;){var e=D;try{switch(e.tag){case 0:case 11:case 15:var n=e.return;try{No(4,e)}catch(c){te(e,n,c)}break;case 1:var r=e.stateNode;if(typeof r.componentDidMount=="function"){var i=e.return;try{r.componentDidMount()}catch(c){te(e,i,c)}}var o=e.return;try{Ll(e)}catch(c){te(e,o,c)}break;case 5:var s=e.return;try{Ll(e)}catch(c){te(e,s,c)}}}catch(c){te(e,e.return,c)}if(e===t){D=null;break}var l=e.sibling;if(l!==null){l.return=e.return,D=l;break}D=e.return}}var L0=Math.ceil,Fo=wt.ReactCurrentDispatcher,Nl=wt.ReactCurrentOwner,Qe=wt.ReactCurrentBatchConfig,F=0,he=null,re=null,ge=0,_e=0,cr=Gt(0),ae=0,gi=null,xn=0,_o=0,Fl=0,mi=null,De=null,_l=0,dr=1/0,Mt=null,Ho=!1,Hl=null,Zt=null,Wo=!1,qt=null,Uo=0,xi=0,Wl=null,Go=-1,Vo=0;function Te(){return F&6?ne():Go!==-1?Go:Go=ne()}function Jt(t){return t.mode&1?F&2&&ge!==0?ge&-ge:w0.transition!==null?(Vo===0&&(Vo=yu()),Vo):(t=H,t!==0||(t=window.event,t=t===void 0?16:Mu(t.type)),t):1}function st(t,e,n,r){if(50<xi)throw xi=0,Wl=null,Error(T(185));_r(t,n,r),(!(F&2)||t!==he)&&(t===he&&(!(F&2)&&(_o|=n),ae===4&&en(t,ge)),Pe(t,r),n===1&&F===0&&!(e.mode&1)&&(dr=ne()+500,vo&&Yt()))}function Pe(t,e){var n=t.callbackNode;wx(t,e);var r=eo(t,t===he?ge:0);if(r===0)n!==null&&gu(n),t.callbackNode=null,t.callbackPriority=0;else if(e=r&-r,t.callbackPriority!==e){if(n!=null&&gu(n),e===1)t.tag===0?y0(Mp.bind(null,t)):hh(Mp.bind(null,t)),f0(function(){!(F&6)&&Yt()}),n=null;else{switch(wu(r)){case 1:n=Sa;break;case 4:n=mu;break;case 16:n=Xi;break;case 536870912:n=xu;break;default:n=Xi}n=Op(n,Tp.bind(null,t))}t.callbackPriority=e,t.callbackNode=n}}function Tp(t,e){if(Go=-1,Vo=0,F&6)throw Error(T(327));var n=t.callbackNode;if(ur()&&t.callbackNode!==n)return null;var r=eo(t,t===he?ge:0);if(r===0)return null;if(r&30||r&t.expiredLanes||e)e=Yo(t,r);else{e=r;var i=F;F|=2;var o=Ep();(he!==t||ge!==e)&&(Mt=null,dr=ne()+500,wn(t,e));do try{N0();break}catch(l){jp(t,l)}while(!0);al(),Fo.current=o,F=i,re!==null?e=0:(he=null,ge=0,e=ae)}if(e!==0){if(e===2&&(i=ka(t),i!==0&&(r=i,e=Ul(t,i))),e===1)throw n=gi,wn(t,0),en(t,r),Pe(t,ne()),n;if(e===6)en(t,r);else{if(i=t.current.alternate,!(r&30)&&!z0(i)&&(e=Yo(t,r),e===2&&(o=ka(t),o!==0&&(r=o,e=Ul(t,o))),e===1))throw n=gi,wn(t,0),en(t,r),Pe(t,ne()),n;switch(t.finishedWork=i,t.finishedLanes=r,e){case 0:case 1:throw Error(T(345));case 2:vn(t,De,Mt);break;case 3:if(en(t,r),(r&130023424)===r&&(e=_l+500-ne(),10<e)){if(eo(t,0)!==0)break;if(i=t.suspendedLanes,(i&r)!==r){Te(),t.pingedLanes|=t.suspendedLanes&i;break}t.timeoutHandle=Xa(vn.bind(null,t,De,Mt),e);break}vn(t,De,Mt);break;case 4:if(en(t,r),(r&4194240)===r)break;for(e=t.eventTimes,i=-1;0<r;){var s=31-et(r);o=1<<s,s=e[s],s>i&&(i=s),r&=~o}if(r=i,r=ne()-r,r=(120>r?120:480>r?480:1080>r?1080:1920>r?1920:3e3>r?3e3:4320>r?4320:1960*L0(r/1960))-r,10<r){t.timeoutHandle=Xa(vn.bind(null,t,De,Mt),r);break}vn(t,De,Mt);break;case 5:vn(t,De,Mt);break;default:throw Error(T(329))}}}return Pe(t,ne()),t.callbackNode===n?Tp.bind(null,t):null}function Ul(t,e){var n=mi;return t.current.memoizedState.isDehydrated&&(wn(t,e).flags|=256),t=Yo(t,e),t!==2&&(e=De,De=n,e!==null&&Gl(e)),t}function Gl(t){De===null?De=t:De.push.apply(De,t)}function z0(t){for(var e=t;;){if(e.flags&16384){var n=e.updateQueue;if(n!==null&&(n=n.stores,n!==null))for(var r=0;r<n.length;r++){var i=n[r],o=i.getSnapshot;i=i.value;try{if(!tt(o(),i))return!1}catch{return!1}}}if(n=e.child,e.subtreeFlags&16384&&n!==null)n.return=e,e=n;else{if(e===t)break;for(;e.sibling===null;){if(e.return===null||e.return===t)return!0;e=e.return}e.sibling.return=e.return,e=e.sibling}}return!0}function en(t,e){for(e&=~Fl,e&=~_o,t.suspendedLanes|=e,t.pingedLanes&=~e,t=t.expirationTimes;0<e;){var n=31-et(e),r=1<<n;t[n]=-1,e&=~r}}function Mp(t){if(F&6)throw Error(T(327));ur();var e=eo(t,0);if(!(e&1))return Pe(t,ne()),null;var n=Yo(t,e);if(t.tag!==0&&n===2){var r=ka(t);r!==0&&(e=r,n=Ul(t,r))}if(n===1)throw n=gi,wn(t,0),en(t,e),Pe(t,ne()),n;if(n===6)throw Error(T(345));return t.finishedWork=t.current.alternate,t.finishedLanes=e,vn(t,De,Mt),Pe(t,ne()),null}function Vl(t,e){var n=F;F|=1;try{return t(e)}finally{F=n,F===0&&(dr=ne()+500,vo&&Yt())}}function yn(t){qt!==null&&qt.tag===0&&!(F&6)&&ur();var e=F;F|=1;var n=Qe.transition,r=H;try{if(Qe.transition=null,H=1,t)return t()}finally{H=r,Qe.transition=n,F=e,!(F&6)&&Yt()}}function Yl(){_e=cr.current,K(cr)}function wn(t,e){t.finishedWork=null,t.finishedLanes=0;var n=t.timeoutHandle;if(n!==-1&&(t.timeoutHandle=-1,p0(n)),re!==null)for(n=re.return;n!==null;){var r=n;switch(nl(r),r.tag){case 1:r=r.type.childContextTypes,r!=null&&yo();break;case 3:sr(),K(Me),K(xe),gl();break;case 5:pl(r);break;case 4:sr();break;case 13:K(q);break;case 19:K(q);break;case 10:ll(r.type._context);break;case 22:case 23:Yl()}n=n.return}if(he=t,re=t=tn(t.current,null),ge=_e=e,ae=0,gi=null,Fl=_o=xn=0,De=mi=null,fn!==null){for(e=0;e<fn.length;e++)if(n=fn[e],r=n.interleaved,r!==null){n.interleaved=null;var i=r.next,o=n.pending;if(o!==null){var s=o.next;o.next=i,r.next=s}n.pending=r}fn=null}return t}function jp(t,e){do{var n=re;try{if(al(),Do.current=Io,Po){for(var r=J.memoizedState;r!==null;){var i=r.queue;i!==null&&(i.pending=null),r=r.next}Po=!1}if(mn=0,ue=se=J=null,ci=!1,di=0,Nl.current=null,n===null||n.return===null){ae=1,gi=e,re=null;break}e:{var o=t,s=n.return,l=n,c=e;if(e=ge,l.flags|=32768,c!==null&&typeof c=="object"&&typeof c.then=="function"){var d=c,u=l,f=u.tag;if(!(u.mode&1)&&(f===0||f===11||f===15)){var g=u.alternate;g?(u.updateQueue=g.updateQueue,u.memoizedState=g.memoizedState,u.lanes=g.lanes):(u.updateQueue=null,u.memoizedState=null)}var v=Jh(s);if(v!==null){v.flags&=-257,ep(v,s,l,o,e),v.mode&1&&qh(o,d,e),e=v,c=d;var x=e.updateQueue;if(x===null){var b=new Set;b.add(c),e.updateQueue=b}else x.add(c);break e}else{if(!(e&1)){qh(o,d,e),Kl();break e}c=Error(T(426))}}else if(X&&l.mode&1){var M=Jh(s);if(M!==null){!(M.flags&65536)&&(M.flags|=256),ep(M,s,l,o,e),ol(ar(c,l));break e}}o=c=ar(c,l),ae!==4&&(ae=2),mi===null?mi=[o]:mi.push(o),o=s;do{switch(o.tag){case 3:o.flags|=65536,e&=-e,o.lanes|=e;var y=Xh(o,c,e);kh(o,y);break e;case 1:l=c;var p=o.type,w=o.stateNode;if(!(o.flags&128)&&(typeof p.getDerivedStateFromError=="function"||w!==null&&typeof w.componentDidCatch=="function"&&(Zt===null||!Zt.has(w)))){o.flags|=65536,e&=-e,o.lanes|=e;var S=Zh(o,l,e);kh(o,S);break e}}o=o.return}while(o!==null)}Pp(n)}catch(C){e=C,re===n&&n!==null&&(re=n=n.return);continue}break}while(!0)}function Ep(){var t=Fo.current;return Fo.current=Io,t===null?Io:t}function Kl(){(ae===0||ae===3||ae===2)&&(ae=4),he===null||!(xn&268435455)&&!(_o&268435455)||en(he,ge)}function Yo(t,e){var n=F;F|=2;var r=Ep();(he!==t||ge!==e)&&(Mt=null,wn(t,e));do try{B0();break}catch(i){jp(t,i)}while(!0);if(al(),F=n,Fo.current=r,re!==null)throw Error(T(261));return he=null,ge=0,ae}function B0(){for(;re!==null;)Dp(re)}function N0(){for(;re!==null&&!dx();)Dp(re)}function Dp(t){var e=Ip(t.alternate,t,_e);t.memoizedProps=t.pendingProps,e===null?Pp(t):re=e,Nl.current=null}function Pp(t){var e=t;do{var n=e.alternate;if(t=e.return,e.flags&32768){if(n=A0(n,e),n!==null){n.flags&=32767,re=n;return}if(t!==null)t.flags|=32768,t.subtreeFlags=0,t.deletions=null;else{ae=6,re=null;return}}else if(n=P0(n,e,_e),n!==null){re=n;return}if(e=e.sibling,e!==null){re=e;return}re=e=t}while(e!==null);ae===0&&(ae=5)}function vn(t,e,n){var r=H,i=Qe.transition;try{Qe.transition=null,H=1,F0(t,e,n,r)}finally{Qe.transition=i,H=r}return null}function F0(t,e,n,r){do ur();while(qt!==null);if(F&6)throw Error(T(327));n=t.finishedWork;var i=t.finishedLanes;if(n===null)return null;if(t.finishedWork=null,t.finishedLanes=0,n===t.current)throw Error(T(177));t.callbackNode=null,t.callbackPriority=0;var o=n.lanes|n.childLanes;if(vx(t,o),t===he&&(re=he=null,ge=0),!(n.subtreeFlags&2064)&&!(n.flags&2064)||Wo||(Wo=!0,Op(Xi,function(){return ur(),null})),o=(n.flags&15990)!==0,n.subtreeFlags&15990||o){o=Qe.transition,Qe.transition=null;var s=H;H=1;var l=F;F|=4,Nl.current=null,I0(t,n),bp(n,t),s0(Ka),ro=!!Ya,Ka=Ya=null,t.current=n,O0(n),ux(),F=l,H=s,Qe.transition=o}else t.current=n;if(Wo&&(Wo=!1,qt=t,Uo=i),o=t.pendingLanes,o===0&&(Zt=null),fx(n.stateNode),Pe(t,ne()),e!==null)for(r=t.onRecoverableError,n=0;n<e.length;n++)i=e[n],r(i.value,{componentStack:i.stack,digest:i.digest});if(Ho)throw Ho=!1,t=Hl,Hl=null,t;return Uo&1&&t.tag!==0&&ur(),o=t.pendingLanes,o&1?t===Wl?xi++:(xi=0,Wl=t):xi=0,Yt(),null}function ur(){if(qt!==null){var t=wu(Uo),e=Qe.transition,n=H;try{if(Qe.transition=null,H=16>t?16:t,qt===null)var r=!1;else{if(t=qt,qt=null,Uo=0,F&6)throw Error(T(331));var i=F;for(F|=4,D=t.current;D!==null;){var o=D,s=o.child;if(D.flags&16){var l=o.deletions;if(l!==null){for(var c=0;c<l.length;c++){var d=l[c];for(D=d;D!==null;){var u=D;switch(u.tag){case 0:case 11:case 15:fi(8,u,o)}var f=u.child;if(f!==null)f.return=u,D=f;else for(;D!==null;){u=D;var g=u.sibling,v=u.return;if(mp(u),u===d){D=null;break}if(g!==null){g.return=v,D=g;break}D=v}}}var x=o.alternate;if(x!==null){var b=x.child;if(b!==null){x.child=null;do{var M=b.sibling;b.sibling=null,b=M}while(b!==null)}}D=o}}if(o.subtreeFlags&2064&&s!==null)s.return=o,D=s;else e:for(;D!==null;){if(o=D,o.flags&2048)switch(o.tag){case 0:case 11:case 15:fi(9,o,o.return)}var y=o.sibling;if(y!==null){y.return=o.return,D=y;break e}D=o.return}}var p=t.current;for(D=p;D!==null;){s=D;var w=s.child;if(s.subtreeFlags&2064&&w!==null)w.return=s,D=w;else e:for(s=p;D!==null;){if(l=D,l.flags&2048)try{switch(l.tag){case 0:case 11:case 15:No(9,l)}}catch(C){te(l,l.return,C)}if(l===s){D=null;break e}var S=l.sibling;if(S!==null){S.return=l.return,D=S;break e}D=l.return}}if(F=i,Yt(),ht&&typeof ht.onPostCommitFiberRoot=="function")try{ht.onPostCommitFiberRoot(Zi,t)}catch{}r=!0}return r}finally{H=n,Qe.transition=e}}return!1}function Ap(t,e,n){e=ar(n,e),e=Xh(t,e,1),t=Qt(t,e,1),e=Te(),t!==null&&(_r(t,1,e),Pe(t,e))}function te(t,e,n){if(t.tag===3)Ap(t,t,n);else for(;e!==null;){if(e.tag===3){Ap(e,t,n);break}else if(e.tag===1){var r=e.stateNode;if(typeof e.type.getDerivedStateFromError=="function"||typeof r.componentDidCatch=="function"&&(Zt===null||!Zt.has(r))){t=ar(n,t),t=Zh(e,t,1),e=Qt(e,t,1),t=Te(),e!==null&&(_r(e,1,t),Pe(e,t));break}}e=e.return}}function _0(t,e,n){var r=t.pingCache;r!==null&&r.delete(e),e=Te(),t.pingedLanes|=t.suspendedLanes&n,he===t&&(ge&n)===n&&(ae===4||ae===3&&(ge&130023424)===ge&&500>ne()-_l?wn(t,0):Fl|=n),Pe(t,e)}function Rp(t,e){e===0&&(t.mode&1?(e=Ji,Ji<<=1,!(Ji&130023424)&&(Ji=4194304)):e=1);var n=Te();t=$t(t,e),t!==null&&(_r(t,e,n),Pe(t,n))}function H0(t){var e=t.memoizedState,n=0;e!==null&&(n=e.retryLane),Rp(t,n)}function W0(t,e){var n=0;switch(t.tag){case 13:var r=t.stateNode,i=t.memoizedState;i!==null&&(n=i.retryLane);break;case 19:r=t.stateNode;break;default:throw Error(T(314))}r!==null&&r.delete(e),Rp(t,n)}var Ip;Ip=function(t,e,n){if(t!==null)if(t.memoizedProps!==e.pendingProps||Me.current)Ee=!0;else{if(!(t.lanes&n)&&!(e.flags&128))return Ee=!1,D0(t,e,n);Ee=!!(t.flags&131072)}else Ee=!1,X&&e.flags&1048576&&ph(e,So,e.index);switch(e.lanes=0,e.tag){case 2:var r=e.type;zo(t,e),t=e.pendingProps;var i=Jn(e,xe.current);or(e,n),i=yl(null,e,r,t,i,n);var o=wl();return e.flags|=1,typeof i=="object"&&i!==null&&typeof i.render=="function"&&i.$$typeof===void 0?(e.tag=1,e.memoizedState=null,e.updateQueue=null,je(r)?(o=!0,wo(e)):o=!1,e.memoizedState=i.state!==null&&i.state!==void 0?i.state:null,ul(e),i.updater=Oo,e.stateNode=i,i._reactInternals=e,Cl(e,r,t,n),e=El(null,e,r,!0,o,n)):(e.tag=0,X&&o&&tl(e),Ce(null,e,i,n),e=e.child),e;case 16:r=e.elementType;e:{switch(zo(t,e),t=e.pendingProps,i=r._init,r=i(r._payload),e.type=r,i=e.tag=G0(r),t=rt(r,t),i){case 0:e=jl(null,e,r,t,n);break e;case 1:e=sp(null,e,r,t,n);break e;case 11:e=tp(null,e,r,t,n);break e;case 14:e=np(null,e,r,rt(r.type,t),n);break e}throw Error(T(306,r,""))}return e;case 0:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),jl(t,e,r,i,n);case 1:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),sp(t,e,r,i,n);case 3:e:{if(ap(e),t===null)throw Error(T(387));r=e.pendingProps,o=e.memoizedState,i=o.element,Sh(t,e),jo(e,r,null,n);var s=e.memoizedState;if(r=s.element,o.isDehydrated)if(o={element:r,isDehydrated:!1,cache:s.cache,pendingSuspenseBoundaries:s.pendingSuspenseBoundaries,transitions:s.transitions},e.updateQueue.baseState=o,e.memoizedState=o,e.flags&256){i=ar(Error(T(423)),e),e=lp(t,e,r,n,i);break e}else if(r!==i){i=ar(Error(T(424)),e),e=lp(t,e,r,n,i);break e}else for(Fe=Ut(e.stateNode.containerInfo.firstChild),Ne=e,X=!0,nt=null,n=vh(e,null,r,n),e.child=n;n;)n.flags=n.flags&-3|4096,n=n.sibling;else{if(nr(),r===i){e=Tt(t,e,n);break e}Ce(t,e,r,n)}e=e.child}return e;case 5:return Ch(e),t===null&&il(e),r=e.type,i=e.pendingProps,o=t!==null?t.memoizedProps:null,s=i.children,Qa(r,i)?s=null:o!==null&&Qa(r,o)&&(e.flags|=32),op(t,e),Ce(t,e,s,n),e.child;case 6:return t===null&&il(e),null;case 13:return cp(t,e,n);case 4:return hl(e,e.stateNode.containerInfo),r=e.pendingProps,t===null?e.child=rr(e,null,r,n):Ce(t,e,r,n),e.child;case 11:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),tp(t,e,r,i,n);case 7:return Ce(t,e,e.pendingProps,n),e.child;case 8:return Ce(t,e,e.pendingProps.children,n),e.child;case 12:return Ce(t,e,e.pendingProps.children,n),e.child;case 10:e:{if(r=e.type._context,i=e.pendingProps,o=e.memoizedProps,s=i.value,G(Co,r._currentValue),r._currentValue=s,o!==null)if(tt(o.value,s)){if(o.children===i.children&&!Me.current){e=Tt(t,e,n);break e}}else for(o=e.child,o!==null&&(o.return=e);o!==null;){var l=o.dependencies;if(l!==null){s=o.child;for(var c=l.firstContext;c!==null;){if(c.context===r){if(o.tag===1){c=Ct(-1,n&-n),c.tag=2;var d=o.updateQueue;if(d!==null){d=d.shared;var u=d.pending;u===null?c.next=c:(c.next=u.next,u.next=c),d.pending=c}}o.lanes|=n,c=o.alternate,c!==null&&(c.lanes|=n),cl(o.return,n,e),l.lanes|=n;break}c=c.next}}else if(o.tag===10)s=o.type===e.type?null:o.child;else if(o.tag===18){if(s=o.return,s===null)throw Error(T(341));s.lanes|=n,l=s.alternate,l!==null&&(l.lanes|=n),cl(s,n,e),s=o.sibling}else s=o.child;if(s!==null)s.return=o;else for(s=o;s!==null;){if(s===e){s=null;break}if(o=s.sibling,o!==null){o.return=s.return,s=o;break}s=s.return}o=s}Ce(t,e,i.children,n),e=e.child}return e;case 9:return i=e.type,r=e.pendingProps.children,or(e,n),i=Ye(i),r=r(i),e.flags|=1,Ce(t,e,r,n),e.child;case 14:return r=e.type,i=rt(r,e.pendingProps),i=rt(r.type,i),np(t,e,r,i,n);case 15:return rp(t,e,e.type,e.pendingProps,n);case 17:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),zo(t,e),e.tag=1,je(r)?(t=!0,wo(e)):t=!1,or(e,n),Kh(e,r,i),Cl(e,r,i,n),El(null,e,r,!0,t,n);case 19:return up(t,e,n);case 22:return ip(t,e,n)}throw Error(T(156,e.tag))};function Op(t,e){return fu(t,e)}function U0(t,e,n,r){this.tag=t,this.key=n,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.ref=null,this.pendingProps=e,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=r,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function Xe(t,e,n,r){return new U0(t,e,n,r)}function Ql(t){return t=t.prototype,!(!t||!t.isReactComponent)}function G0(t){if(typeof t=="function")return Ql(t)?1:0;if(t!=null){if(t=t.$$typeof,t===ta)return 11;if(t===ia)return 14}return 2}function tn(t,e){var n=t.alternate;return n===null?(n=Xe(t.tag,e,t.key,t.mode),n.elementType=t.elementType,n.type=t.type,n.stateNode=t.stateNode,n.alternate=t,t.alternate=n):(n.pendingProps=e,n.type=t.type,n.flags=0,n.subtreeFlags=0,n.deletions=null),n.flags=t.flags&14680064,n.childLanes=t.childLanes,n.lanes=t.lanes,n.child=t.child,n.memoizedProps=t.memoizedProps,n.memoizedState=t.memoizedState,n.updateQueue=t.updateQueue,e=t.dependencies,n.dependencies=e===null?null:{lanes:e.lanes,firstContext:e.firstContext},n.sibling=t.sibling,n.index=t.index,n.ref=t.ref,n}function Ko(t,e,n,r,i,o){var s=2;if(r=t,typeof t=="function")Ql(t)&&(s=1);else if(typeof t=="string")s=5;else e:switch(t){case Fn:return bn(n.children,i,o,e);case Js:s=8,i|=8;break;case ea:return t=Xe(12,n,e,i|2),t.elementType=ea,t.lanes=o,t;case na:return t=Xe(13,n,e,i),t.elementType=na,t.lanes=o,t;case ra:return t=Xe(19,n,e,i),t.elementType=ra,t.lanes=o,t;case Ud:return Qo(n,i,o,e);default:if(typeof t=="object"&&t!==null)switch(t.$$typeof){case Hd:s=10;break e;case Wd:s=9;break e;case ta:s=11;break e;case ia:s=14;break e;case Lt:s=16,r=null;break e}throw Error(T(130,t==null?t:typeof t,""))}return e=Xe(s,n,e,i),e.elementType=t,e.type=r,e.lanes=o,e}function bn(t,e,n,r){return t=Xe(7,t,r,e),t.lanes=n,t}function Qo(t,e,n,r){return t=Xe(22,t,r,e),t.elementType=Ud,t.lanes=n,t.stateNode={isHidden:!1},t}function Xl(t,e,n){return t=Xe(6,t,null,e),t.lanes=n,t}function Zl(t,e,n){return e=Xe(4,t.children!==null?t.children:[],t.key,e),e.lanes=n,e.stateNode={containerInfo:t.containerInfo,pendingChildren:null,implementation:t.implementation},e}function V0(t,e,n,r,i){this.tag=e,this.containerInfo=t,this.finishedWork=this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.pendingContext=this.context=null,this.callbackPriority=0,this.eventTimes=$a(0),this.expirationTimes=$a(-1),this.entangledLanes=this.finishedLanes=this.mutableReadLanes=this.expiredLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=$a(0),this.identifierPrefix=r,this.onRecoverableError=i,this.mutableSourceEagerHydrationData=null}function ql(t,e,n,r,i,o,s,l,c){return t=new V0(t,e,n,l,c),e===1?(e=1,o===!0&&(e|=8)):e=0,o=Xe(3,null,null,e),t.current=o,o.stateNode=t,o.memoizedState={element:r,isDehydrated:n,cache:null,transitions:null,pendingSuspenseBoundaries:null},ul(o),t}function Y0(t,e,n){var r=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:Nn,key:r==null?null:""+r,children:t,containerInfo:e,implementation:n}}function Lp(t){if(!t)return Vt;t=t._reactInternals;e:{if(cn(t)!==t||t.tag!==1)throw Error(T(170));var e=t;do{switch(e.tag){case 3:e=e.stateNode.context;break e;case 1:if(je(e.type)){e=e.stateNode.__reactInternalMemoizedMergedChildContext;break e}}e=e.return}while(e!==null);throw Error(T(171))}if(t.tag===1){var n=t.type;if(je(n))return dh(t,n,e)}return e}function zp(t,e,n,r,i,o,s,l,c){return t=ql(n,r,!0,t,i,o,s,l,c),t.context=Lp(null),n=t.current,r=Te(),i=Jt(n),o=Ct(r,i),o.callback=e??null,Qt(n,o,i),t.current.lanes=i,_r(t,i,r),Pe(t,r),t}function Xo(t,e,n,r){var i=e.current,o=Te(),s=Jt(i);return n=Lp(n),e.context===null?e.context=n:e.pendingContext=n,e=Ct(o,s),e.payload={element:t},r=r===void 0?null:r,r!==null&&(e.callback=r),t=Qt(i,e,s),t!==null&&(st(t,i,s,o),Mo(t,i,s)),s}function Zo(t){if(t=t.current,!t.child)return null;switch(t.child.tag){case 5:return t.child.stateNode;default:return t.child.stateNode}}function Bp(t,e){if(t=t.memoizedState,t!==null&&t.dehydrated!==null){var n=t.retryLane;t.retryLane=n!==0&&n<e?n:e}}function Jl(t,e){Bp(t,e),(t=t.alternate)&&Bp(t,e)}function K0(){return null}var Np=typeof reportError=="function"?reportError:function(t){console.error(t)};function ec(t){this._internalRoot=t}qo.prototype.render=ec.prototype.render=function(t){var e=this._internalRoot;if(e===null)throw Error(T(409));Xo(t,e,null,null)},qo.prototype.unmount=ec.prototype.unmount=function(){var t=this._internalRoot;if(t!==null){this._internalRoot=null;var e=t.containerInfo;yn(function(){Xo(null,t,null,null)}),e[vt]=null}};function qo(t){this._internalRoot=t}qo.prototype.unstable_scheduleHydration=function(t){if(t){var e=Su();t={blockedOn:null,target:t,priority:e};for(var n=0;n<_t.length&&e!==0&&e<_t[n].priority;n++);_t.splice(n,0,t),n===0&&Cu(t)}};function tc(t){return!(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11)}function Jo(t){return!(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11&&(t.nodeType!==8||t.nodeValue!==" react-mount-point-unstable "))}function Fp(){}function Q0(t,e,n,r,i){if(i){if(typeof r=="function"){var o=r;r=function(){var d=Zo(s);o.call(d)}}var s=zp(e,r,t,0,null,!1,!1,"",Fp);return t._reactRootContainer=s,t[vt]=s.current,ti(t.nodeType===8?t.parentNode:t),yn(),s}for(;i=t.lastChild;)t.removeChild(i);if(typeof r=="function"){var l=r;r=function(){var d=Zo(c);l.call(d)}}var c=ql(t,0,!1,null,null,!1,!1,"",Fp);return t._reactRootContainer=c,t[vt]=c.current,ti(t.nodeType===8?t.parentNode:t),yn(function(){Xo(e,c,n,r)}),c}function es(t,e,n,r,i){var o=n._reactRootContainer;if(o){var s=o;if(typeof i=="function"){var l=i;i=function(){var c=Zo(s);l.call(c)}}Xo(e,s,t,i)}else s=Q0(n,e,t,i,r);return Zo(s)}vu=function(t){switch(t.tag){case 3:var e=t.stateNode;if(e.current.memoizedState.isDehydrated){var n=Fr(e.pendingLanes);n!==0&&(Ca(e,n|1),Pe(e,ne()),!(F&6)&&(dr=ne()+500,Yt()))}break;case 13:yn(function(){var r=$t(t,1);if(r!==null){var i=Te();st(r,t,1,i)}}),Jl(t,1)}},Ta=function(t){if(t.tag===13){var e=$t(t,134217728);if(e!==null){var n=Te();st(e,t,134217728,n)}Jl(t,134217728)}},bu=function(t){if(t.tag===13){var e=Jt(t),n=$t(t,e);if(n!==null){var r=Te();st(n,t,e,r)}Jl(t,e)}},Su=function(){return H},ku=function(t,e){var n=H;try{return H=t,e()}finally{H=n}},ya=function(t,e,n){switch(e){case"input":if(da(t,n),e=n.name,n.type==="radio"&&e!=null){for(n=t;n.parentNode;)n=n.parentNode;for(n=n.querySelectorAll("input[name="+JSON.stringify(""+e)+'][type="radio"]'),e=0;e<n.length;e++){var r=n[e];if(r!==t&&r.form===t.form){var i=xo(r);if(!i)throw Error(T(90));Yd(r),da(r,i)}}}break;case"textarea":qd(t,n);break;case"select":e=n.value,e!=null&&_n(t,!!n.multiple,e,!1)}},au=Vl,lu=yn;var X0={usingClientEntryPoint:!1,Events:[ii,Zn,xo,ou,su,Vl]},yi={findFiberByHostInstance:dn,bundleType:0,version:"18.3.1",rendererPackageName:"react-dom"},Z0={bundleType:yi.bundleType,version:yi.version,rendererPackageName:yi.rendererPackageName,rendererConfig:yi.rendererConfig,overrideHookState:null,overrideHookStateDeletePath:null,overrideHookStateRenamePath:null,overrideProps:null,overridePropsDeletePath:null,overridePropsRenamePath:null,setErrorHandler:null,setSuspenseHandler:null,scheduleUpdate:null,currentDispatcherRef:wt.ReactCurrentDispatcher,findHostInstanceByFiber:function(t){return t=hu(t),t===null?null:t.stateNode},findFiberByHostInstance:yi.findFiberByHostInstance||K0,findHostInstancesForRefresh:null,scheduleRefresh:null,scheduleRoot:null,setRefreshHandler:null,getCurrentFiber:null,reconcilerVersion:"18.3.1-next-f1338f8080-20240426"};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"){var ts=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!ts.isDisabled&&ts.supportsFiber)try{Zi=ts.inject(Z0),ht=ts}catch{}}Le.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=X0,Le.createPortal=function(t,e){var n=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!tc(e))throw Error(T(200));return Y0(t,e,null,n)},Le.createRoot=function(t,e){if(!tc(t))throw Error(T(299));var n=!1,r="",i=Np;return e!=null&&(e.unstable_strictMode===!0&&(n=!0),e.identifierPrefix!==void 0&&(r=e.identifierPrefix),e.onRecoverableError!==void 0&&(i=e.onRecoverableError)),e=ql(t,1,!1,null,null,n,!1,r,i),t[vt]=e.current,ti(t.nodeType===8?t.parentNode:t),new ec(e)},Le.findDOMNode=function(t){if(t==null)return null;if(t.nodeType===1)return t;var e=t._reactInternals;if(e===void 0)throw typeof t.render=="function"?Error(T(188)):(t=Object.keys(t).join(","),Error(T(268,t)));return t=hu(e),t=t===null?null:t.stateNode,t},Le.flushSync=function(t){return yn(t)},Le.hydrate=function(t,e,n){if(!Jo(e))throw Error(T(200));return es(null,t,e,!0,n)},Le.hydrateRoot=function(t,e,n){if(!tc(t))throw Error(T(405));var r=n!=null&&n.hydratedSources||null,i=!1,o="",s=Np;if(n!=null&&(n.unstable_strictMode===!0&&(i=!0),n.identifierPrefix!==void 0&&(o=n.identifierPrefix),n.onRecoverableError!==void 0&&(s=n.onRecoverableError)),e=zp(e,null,t,1,n??null,i,!1,o,s),t[vt]=e.current,ti(t),r)for(t=0;t<r.length;t++)n=r[t],i=n._getVersion,i=i(n._source),e.mutableSourceEagerHydrationData==null?e.mutableSourceEagerHydrationData=[n,i]:e.mutableSourceEagerHydrationData.push(n,i);return new qo(e)},Le.render=function(t,e,n){if(!Jo(e))throw Error(T(200));return es(null,t,e,!1,n)},Le.unmountComponentAtNode=function(t){if(!Jo(t))throw Error(T(40));return t._reactRootContainer?(yn(function(){es(null,null,t,!1,function(){t._reactRootContainer=null,t[vt]=null})}),!0):!1},Le.unstable_batchedUpdates=Vl,Le.unstable_renderSubtreeIntoContainer=function(t,e,n,r){if(!Jo(n))throw Error(T(200));if(t==null||t._reactInternals===void 0)throw Error(T(38));return es(t,e,n,!1,r)},Le.version="18.3.1-next-f1338f8080-20240426";function _p(){if(!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u"||typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE!="function"))try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(_p)}catch(t){console.error(t)}}_p(),Ld.exports=Le;var q0=Ld.exports,Hp,Wp=q0;Hp=Wp.createRoot,Wp.hydrateRoot;var Ae=function(){return Ae=Object.assign||function(e){for(var n,r=1,i=arguments.length;r<i;r++){n=arguments[r];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},Ae.apply(this,arguments)};function ns(t,e,n){if(n||arguments.length===2)for(var r=0,i=e.length,o;r<i;r++)(o||!(r in e))&&(o||(o=Array.prototype.slice.call(e,0,r)),o[r]=e[r]);return t.concat(o||Array.prototype.slice.call(e))}typeof SuppressedError=="function"&&SuppressedError;var Q="-ms-",wi="-moz-",_="-webkit-",Up="comm",rs="rule",nc="decl",J0="@import",ey="@namespace",Gp="@keyframes",ty="@layer",Vp=Math.abs,rc=String.fromCharCode,ic=Object.assign;function ny(t,e){return le(t,0)^45?(((e<<2^le(t,0))<<2^le(t,1))<<2^le(t,2))<<2^le(t,3):0}function Yp(t){return t.trim()}function jt(t,e){return(t=e.exec(t))?t[0]:t}function z(t,e,n){return t.replace(e,n)}function is(t,e,n){return t.indexOf(e,n)}function le(t,e){return t.charCodeAt(e)|0}function Sn(t,e,n){return t.slice(e,n)}function at(t){return t.length}function Kp(t){return t.length}function vi(t,e){return e.push(t),t}function ry(t,e){return t.map(e).join("")}function Qp(t,e){return t.filter(function(n){return!jt(n,e)})}var os=1,hr=1,Xp=0,Ze=0,ie=0,pr="";function ss(t,e,n,r,i,o,s,l){return{value:t,root:e,parent:n,type:r,props:i,children:o,line:os,column:hr,length:s,return:"",siblings:l}}function nn(t,e){return ic(ss("",null,null,"",null,null,0,t.siblings),t,{length:-t.length},e)}function fr(t){for(;t.root;)t=nn(t.root,{children:[t]});vi(t,t.siblings)}function iy(){return ie}function oy(){return ie=Ze>0?le(pr,--Ze):0,hr--,ie===10&&(hr=1,os--),ie}function lt(){return ie=Ze<Xp?le(pr,Ze++):0,hr++,ie===10&&(hr=1,os++),ie}function rn(){return le(pr,Ze)}function as(){return Ze}function ls(t,e){return Sn(pr,t,e)}function bi(t){switch(t){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function sy(t){return os=hr=1,Xp=at(pr=t),Ze=0,[]}function ay(t){return pr="",t}function oc(t){return Yp(ls(Ze-1,sc(t===91?t+2:t===40?t+1:t)))}function ly(t){for(;(ie=rn())&&ie<33;)lt();return bi(t)>2||bi(ie)>3?"":" "}function cy(t,e){for(;--e&&lt()&&!(ie<48||ie>102||ie>57&&ie<65||ie>70&&ie<97););return ls(t,as()+(e<6&&rn()==32&&lt()==32))}function sc(t){for(;lt();)switch(ie){case t:return Ze;case 34:case 39:t!==34&&t!==39&&sc(ie);break;case 40:t===41&&sc(t);break;case 92:lt();break}return Ze}function dy(t,e){for(;lt()&&t+ie!==57;)if(t+ie===84&&rn()===47)break;return"/*"+ls(e,Ze-1)+"*"+rc(t===47?t:lt())}function uy(t){for(;!bi(rn());)lt();return ls(t,Ze)}function hy(t){return ay(cs("",null,null,null,[""],t=sy(t),0,[0],t))}function cs(t,e,n,r,i,o,s,l,c){for(var d=0,u=0,f=s,g=0,v=0,x=0,b=1,M=1,y=1,p=0,w="",S=i,C=o,$=r,k=w;M;)switch(x=p,p=lt()){case 40:if(x!=108&&le(k,f-1)==58){is(k+=z(oc(p),"&","&\f"),"&\f",Vp(d?l[d-1]:0))!=-1&&(y=-1);break}case 34:case 39:case 91:k+=oc(p);break;case 9:case 10:case 13:case 32:k+=ly(x);break;case 92:k+=cy(as()-1,7);continue;case 47:switch(rn()){case 42:case 47:vi(py(dy(lt(),as()),e,n,c),c),(bi(x||1)==5||bi(rn()||1)==5)&&at(k)&&Sn(k,-1,void 0)!==" "&&(k+=" ");break;default:k+="/"}break;case 123*b:l[d++]=at(k)*y;case 125*b:case 59:case 0:switch(p){case 0:case 125:M=0;case 59+u:y==-1&&(k=z(k,/\f/g,"")),v>0&&(at(k)-f||b===0&&x===47)&&vi(v>32?qp(k+";",r,n,f-1,c):qp(z(k," ","")+";",r,n,f-2,c),c);break;case 59:k+=";";default:if(vi($=Zp(k,e,n,d,u,i,l,w,S=[],C=[],f,o),o),p===123)if(u===0)cs(k,e,$,$,S,o,f,l,C);else{switch(g){case 99:if(le(k,3)===110)break;case 108:if(le(k,2)===97)break;default:u=0;case 100:case 109:case 115:}u?cs(t,$,$,r&&vi(Zp(t,$,$,0,0,i,l,w,i,S=[],f,C),C),i,C,f,l,r?S:C):cs(k,$,$,$,[""],C,0,l,C)}}d=u=v=0,b=y=1,w=k="",f=s;break;case 58:f=1+at(k),v=x;default:if(b<1){if(p==123)--b;else if(p==125&&b++==0&&oy()==125)continue}switch(k+=rc(p),p*b){case 38:y=u>0?1:(k+="\f",-1);break;case 44:l[d++]=(at(k)-1)*y,y=1;break;case 64:rn()===45&&(k+=oc(lt())),g=rn(),u=f=at(w=k+=uy(as())),p++;break;case 45:x===45&&at(k)==2&&(b=0)}}return o}function Zp(t,e,n,r,i,o,s,l,c,d,u,f){for(var g=i-1,v=i===0?o:[""],x=Kp(v),b=0,M=0,y=0;b<r;++b)for(var p=0,w=Sn(t,g+1,g=Vp(M=s[b])),S=t;p<x;++p)(S=Yp(M>0?v[p]+" "+w:z(w,/&\f/g,v[p])))&&(c[y++]=S);return ss(t,e,n,i===0?rs:l,c,d,u,f)}function py(t,e,n,r){return ss(t,e,n,Up,rc(iy()),Sn(t,2,-2),0,r)}function qp(t,e,n,r,i){return ss(t,e,n,nc,Sn(t,0,r),Sn(t,r+1,-1),r,i)}function Jp(t,e,n){switch(ny(t,e)){case 5103:return _+"print-"+t+t;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return _+t+t;case 4855:return _+t.replace("add","source-over").replace("substract","source-out").replace("intersect","source-in").replace("exclude","xor")+t;case 4789:return wi+t+t;case 5349:case 4246:case 4810:case 6968:case 2756:return _+t+wi+t+Q+t+t;case 5936:switch(le(t,e+11)){case 114:return _+t+Q+z(t,/[svh]\w+-[tblr]{2}/,"tb")+t;case 108:return _+t+Q+z(t,/[svh]\w+-[tblr]{2}/,"tb-rl")+t;case 45:return _+t+Q+z(t,/[svh]\w+-[tblr]{2}/,"lr")+t}case 6828:case 4268:case 2903:return _+t+Q+t+t;case 6165:return _+t+Q+"flex-"+t+t;case 5187:return _+t+z(t,/(\w+).+(:[^]+)/,_+"box-$1$2"+Q+"flex-$1$2")+t;case 5443:return _+t+Q+"flex-item-"+z(t,/flex-|-self/g,"")+(jt(t,/flex-|baseline/)?"":Q+"grid-row-"+z(t,/flex-|-self/g,""))+t;case 4675:return _+t+Q+"flex-line-pack"+z(t,/align-content|flex-|-self/g,"")+t;case 5548:return _+t+Q+z(t,"shrink","negative")+t;case 5292:return _+t+Q+z(t,"basis","preferred-size")+t;case 6060:return _+"box-"+z(t,"-grow","")+_+t+Q+z(t,"grow","positive")+t;case 4554:return _+z(t,/([^-])(transform)/g,"$1"+_+"$2")+t;case 6187:return z(z(z(t,/(zoom-|grab)/,_+"$1"),/(image-set)/,_+"$1"),t,"")+t;case 5495:case 3959:return z(t,/(image-set\([^]*)/,_+"$1$`$1");case 4968:return z(z(t,/(.+:)(flex-)?(.*)/,_+"box-pack:$3"+Q+"flex-pack:$3"),/space-between/,"justify")+_+t+t;case 4200:if(!jt(t,/flex-|baseline/))return Q+"grid-column-align"+Sn(t,e)+t;break;case 2592:case 3360:return Q+z(t,"template-","")+t;case 4384:case 3616:return n&&n.some(function(r,i){return e=i,jt(r.props,/grid-\w+-end/)})?~is(t+(n=n[e].value),"span",0)?t:Q+z(t,"-start","")+t+Q+"grid-row-span:"+(~is(n,"span",0)?jt(n,/\d+/):+jt(n,/\d+/)-+jt(t,/\d+/))+";":Q+z(t,"-start","")+t;case 4896:case 4128:return n&&n.some(function(r){return jt(r.props,/grid-\w+-start/)})?t:Q+z(z(t,"-end","-span"),"span ","")+t;case 4095:case 3583:case 4068:case 2532:return z(t,/(.+)-inline(.+)/,_+"$1$2")+t;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(at(t)-1-e>6)switch(le(t,e+1)){case 109:if(le(t,e+4)!==45)break;case 102:return z(t,/(.+:)(.+)-([^]+)/,"$1"+_+"$2-$3$1"+wi+(le(t,e+3)==108?"$3":"$2-$3"))+t;case 115:return~is(t,"stretch",0)?Jp(z(t,"stretch","fill-available"),e,n)+t:t}break;case 5152:case 5920:return z(t,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(r,i,o,s,l,c,d){return Q+i+":"+o+d+(s?Q+i+"-span:"+(l?c:+c-+o)+d:"")+t});case 4949:if(le(t,e+6)===121)return z(t,":",":"+_)+t;break;case 6444:switch(le(t,le(t,14)===45?18:11)){case 120:return z(t,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,"$1"+_+(le(t,14)===45?"inline-":"")+"box$3$1"+_+"$2$3$1"+Q+"$2box$3")+t;case 100:return z(t,":",":"+Q)+t}break;case 5719:case 2647:case 2135:case 3927:case 2391:return z(t,"scroll-","scroll-snap-")+t}return t}function ds(t,e){for(var n="",r=0;r<t.length;r++)n+=e(t[r],r,t,e)||"";return n}function fy(t,e,n,r){switch(t.type){case ty:if(t.children.length)break;case J0:case ey:case nc:return t.return=t.return||t.value;case Up:return"";case Gp:return t.return=t.value+"{"+ds(t.children,r)+"}";case rs:if(!at(t.value=t.props.join(",")))return""}return at(n=ds(t.children,r))?t.return=t.value+"{"+n+"}":""}function gy(t){var e=Kp(t);return function(n,r,i,o){for(var s="",l=0;l<e;l++)s+=t[l](n,r,i,o)||"";return s}}function my(t){return function(e){e.root||(e=e.return)&&t(e)}}function xy(t,e,n,r){if(t.length>-1&&!t.return)switch(t.type){case nc:t.return=Jp(t.value,t.length,n);return;case Gp:return ds([nn(t,{value:z(t.value,"@","@"+_)})],r);case rs:if(t.length)return ry(n=t.props,function(i){switch(jt(i,r=/(::plac\w+|:read-\w+)/)){case":read-only":case":read-write":fr(nn(t,{props:[z(i,/:(read-\w+)/,":"+wi+"$1")]})),fr(nn(t,{props:[i]})),ic(t,{props:Qp(n,r)});break;case"::placeholder":fr(nn(t,{props:[z(i,/:(plac\w+)/,":"+_+"input-$1")]})),fr(nn(t,{props:[z(i,/:(plac\w+)/,":"+wi+"$1")]})),fr(nn(t,{props:[z(i,/:(plac\w+)/,Q+"input-$1")]})),fr(nn(t,{props:[i]})),ic(t,{props:Qp(n,r)});break}return""})}}var yy={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},He={},gr=typeof process<"u"&&He!==void 0&&(He.REACT_APP_SC_ATTR||He.SC_ATTR)||"data-styled",ef="active",tf="data-styled-version",us="6.3.8",ac=`/*!sc*/
`,hs=typeof window<"u"&&typeof document<"u",mr=oe.createContext===void 0,wy=!!(typeof SC_DISABLE_SPEEDY=="boolean"?SC_DISABLE_SPEEDY:typeof process<"u"&&He!==void 0&&He.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&He.REACT_APP_SC_DISABLE_SPEEDY!==""?He.REACT_APP_SC_DISABLE_SPEEDY!=="false"&&He.REACT_APP_SC_DISABLE_SPEEDY:typeof process<"u"&&He!==void 0&&He.SC_DISABLE_SPEEDY!==void 0&&He.SC_DISABLE_SPEEDY!==""&&He.SC_DISABLE_SPEEDY!=="false"&&He.SC_DISABLE_SPEEDY),ps=Object.freeze([]),xr=Object.freeze({});function vy(t,e,n){return n===void 0&&(n=xr),t.theme!==n.theme&&t.theme||e||n.theme}var nf=new Set(["a","abbr","address","area","article","aside","audio","b","bdi","bdo","blockquote","body","button","br","canvas","caption","cite","code","col","colgroup","data","datalist","dd","del","details","dfn","dialog","div","dl","dt","em","embed","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","html","i","iframe","img","input","ins","kbd","label","legend","li","main","map","mark","menu","meter","nav","object","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","search","section","select","slot","small","span","strong","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","u","ul","var","video","wbr","circle","clipPath","defs","ellipse","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feDropShadow","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feImage","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence","filter","foreignObject","g","image","line","linearGradient","marker","mask","path","pattern","polygon","polyline","radialGradient","rect","stop","svg","switch","symbol","text","textPath","tspan","use"]),by=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,Sy=/(^-|-$)/g;function rf(t){return t.replace(by,"-").replace(Sy,"")}var ky=/(a)(d)/gi,of=function(t){return String.fromCharCode(t+(t>25?39:97))};function lc(t){var e,n="";for(e=Math.abs(t);e>52;e=e/52|0)n=of(e%52)+n;return(of(e%52)+n).replace(ky,"$1-$2")}var cc,yr=function(t,e){for(var n=e.length;n;)t=33*t^e.charCodeAt(--n);return t},sf=function(t){return yr(5381,t)};function $y(t){return lc(sf(t)>>>0)}function Cy(t){return t.displayName||t.name||"Component"}function dc(t){return typeof t=="string"&&!0}var af=typeof Symbol=="function"&&Symbol.for,lf=af?Symbol.for("react.memo"):60115,Ty=af?Symbol.for("react.forward_ref"):60112,My={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},jy={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},cf={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},Ey=((cc={})[Ty]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},cc[lf]=cf,cc);function df(t){return("type"in(e=t)&&e.type.$$typeof)===lf?cf:"$$typeof"in t?Ey[t.$$typeof]:My;var e}var Dy=Object.defineProperty,Py=Object.getOwnPropertyNames,uf=Object.getOwnPropertySymbols,Ay=Object.getOwnPropertyDescriptor,Ry=Object.getPrototypeOf,hf=Object.prototype;function pf(t,e,n){if(typeof e!="string"){if(hf){var r=Ry(e);r&&r!==hf&&pf(t,r,n)}var i=Py(e);uf&&(i=i.concat(uf(e)));for(var o=df(t),s=df(e),l=0;l<i.length;++l){var c=i[l];if(!(c in jy||n&&n[c]||s&&c in s||o&&c in o)){var d=Ay(e,c);try{Dy(t,c,d)}catch{}}}}return t}function wr(t){return typeof t=="function"}function uc(t){return typeof t=="object"&&"styledComponentId"in t}function kn(t,e){return t&&e?"".concat(t," ").concat(e):t||e||""}function ff(t,e){if(t.length===0)return"";for(var n=t[0],r=1;r<t.length;r++)n+=t[r];return n}function Si(t){return t!==null&&typeof t=="object"&&t.constructor.name===Object.name&&!("props"in t&&t.$$typeof)}function hc(t,e,n){if(n===void 0&&(n=!1),!n&&!Si(t)&&!Array.isArray(t))return e;if(Array.isArray(e))for(var r=0;r<e.length;r++)t[r]=hc(t[r],e[r]);else if(Si(e))for(var r in e)t[r]=hc(t[r],e[r]);return t}function pc(t,e){Object.defineProperty(t,"toString",{value:e})}function ki(t){for(var e=[],n=1;n<arguments.length;n++)e[n-1]=arguments[n];return new Error("An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#".concat(t," for more information.").concat(e.length>0?" Args: ".concat(e.join(", ")):""))}var Iy=function(){function t(e){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=e}return t.prototype.indexOfGroup=function(e){for(var n=0,r=0;r<e;r++)n+=this.groupSizes[r];return n},t.prototype.insertRules=function(e,n){if(e>=this.groupSizes.length){for(var r=this.groupSizes,i=r.length,o=i;e>=o;)if((o<<=1)<0)throw ki(16,"".concat(e));this.groupSizes=new Uint32Array(o),this.groupSizes.set(r),this.length=o;for(var s=i;s<o;s++)this.groupSizes[s]=0}for(var l=this.indexOfGroup(e+1),c=(s=0,n.length);s<c;s++)this.tag.insertRule(l,n[s])&&(this.groupSizes[e]++,l++)},t.prototype.clearGroup=function(e){if(e<this.length){var n=this.groupSizes[e],r=this.indexOfGroup(e),i=r+n;this.groupSizes[e]=0;for(var o=r;o<i;o++)this.tag.deleteRule(r)}},t.prototype.getGroup=function(e){var n="";if(e>=this.length||this.groupSizes[e]===0)return n;for(var r=this.groupSizes[e],i=this.indexOfGroup(e),o=i+r,s=i;s<o;s++)n+="".concat(this.tag.getRule(s)).concat(ac);return n},t}(),fs=new Map,gs=new Map,ms=1,$i=function(t){if(fs.has(t))return fs.get(t);for(;gs.has(ms);)ms++;var e=ms++;return fs.set(t,e),gs.set(e,t),e},Oy=function(t,e){ms=e+1,fs.set(t,e),gs.set(e,t)},Ly="style[".concat(gr,"][").concat(tf,'="').concat(us,'"]'),zy=new RegExp("^".concat(gr,'\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)')),By=function(t,e,n){for(var r,i=n.split(","),o=0,s=i.length;o<s;o++)(r=i[o])&&t.registerName(e,r)},Ny=function(t,e){for(var n,r=((n=e.textContent)!==null&&n!==void 0?n:"").split(ac),i=[],o=0,s=r.length;o<s;o++){var l=r[o].trim();if(l){var c=l.match(zy);if(c){var d=0|parseInt(c[1],10),u=c[2];d!==0&&(Oy(u,d),By(t,u,c[3]),t.getTag().insertRules(d,i)),i.length=0}else i.push(l)}}},gf=function(t){for(var e=document.querySelectorAll(Ly),n=0,r=e.length;n<r;n++){var i=e[n];i&&i.getAttribute(gr)!==ef&&(Ny(t,i),i.parentNode&&i.parentNode.removeChild(i))}};function Fy(){return typeof __webpack_nonce__<"u"?__webpack_nonce__:null}var mf=function(t){var e=document.head,n=t||e,r=document.createElement("style"),i=function(l){var c=Array.from(l.querySelectorAll("style[".concat(gr,"]")));return c[c.length-1]}(n),o=i!==void 0?i.nextSibling:null;r.setAttribute(gr,ef),r.setAttribute(tf,us);var s=Fy();return s&&r.setAttribute("nonce",s),n.insertBefore(r,o),r},_y=function(){function t(e){this.element=mf(e),this.element.appendChild(document.createTextNode("")),this.sheet=function(n){if(n.sheet)return n.sheet;for(var r=document.styleSheets,i=0,o=r.length;i<o;i++){var s=r[i];if(s.ownerNode===n)return s}throw ki(17)}(this.element),this.length=0}return t.prototype.insertRule=function(e,n){try{return this.sheet.insertRule(n,e),this.length++,!0}catch{return!1}},t.prototype.deleteRule=function(e){this.sheet.deleteRule(e),this.length--},t.prototype.getRule=function(e){var n=this.sheet.cssRules[e];return n&&n.cssText?n.cssText:""},t}(),Hy=function(){function t(e){this.element=mf(e),this.nodes=this.element.childNodes,this.length=0}return t.prototype.insertRule=function(e,n){if(e<=this.length&&e>=0){var r=document.createTextNode(n);return this.element.insertBefore(r,this.nodes[e]||null),this.length++,!0}return!1},t.prototype.deleteRule=function(e){this.element.removeChild(this.nodes[e]),this.length--},t.prototype.getRule=function(e){return e<this.length?this.nodes[e].textContent:""},t}(),Wy=function(){function t(e){this.rules=[],this.length=0}return t.prototype.insertRule=function(e,n){return e<=this.length&&(this.rules.splice(e,0,n),this.length++,!0)},t.prototype.deleteRule=function(e){this.rules.splice(e,1),this.length--},t.prototype.getRule=function(e){return e<this.length?this.rules[e]:""},t}(),xf=hs,Uy={isServer:!hs,useCSSOMInjection:!wy},yf=function(){function t(e,n,r){e===void 0&&(e=xr),n===void 0&&(n={});var i=this;this.options=Ae(Ae({},Uy),e),this.gs=n,this.names=new Map(r),this.server=!!e.isServer,!this.server&&hs&&xf&&(xf=!1,gf(this)),pc(this,function(){return function(o){for(var s=o.getTag(),l=s.length,c="",d=function(f){var g=function(y){return gs.get(y)}(f);if(g===void 0)return"continue";var v=o.names.get(g),x=s.getGroup(f);if(v===void 0||!v.size||x.length===0)return"continue";var b="".concat(gr,".g").concat(f,'[id="').concat(g,'"]'),M="";v!==void 0&&v.forEach(function(y){y.length>0&&(M+="".concat(y,","))}),c+="".concat(x).concat(b,'{content:"').concat(M,'"}').concat(ac)},u=0;u<l;u++)d(u);return c}(i)})}return t.registerId=function(e){return $i(e)},t.prototype.rehydrate=function(){!this.server&&hs&&gf(this)},t.prototype.reconstructWithOptions=function(e,n){return n===void 0&&(n=!0),new t(Ae(Ae({},this.options),e),this.gs,n&&this.names||void 0)},t.prototype.allocateGSInstance=function(e){return this.gs[e]=(this.gs[e]||0)+1},t.prototype.getTag=function(){return this.tag||(this.tag=(e=function(n){var r=n.useCSSOMInjection,i=n.target;return n.isServer?new Wy(i):r?new _y(i):new Hy(i)}(this.options),new Iy(e)));var e},t.prototype.hasNameForId=function(e,n){return this.names.has(e)&&this.names.get(e).has(n)},t.prototype.registerName=function(e,n){if($i(e),this.names.has(e))this.names.get(e).add(n);else{var r=new Set;r.add(n),this.names.set(e,r)}},t.prototype.insertRules=function(e,n,r){this.registerName(e,n),this.getTag().insertRules($i(e),r)},t.prototype.clearNames=function(e){this.names.has(e)&&this.names.get(e).clear()},t.prototype.clearRules=function(e){this.getTag().clearGroup($i(e)),this.clearNames(e)},t.prototype.clearTag=function(){this.tag=void 0},t}(),Gy=/&/g,vr=47;function wf(t){if(t.indexOf("}")===-1)return!1;for(var e=t.length,n=0,r=0,i=!1,o=0;o<e;o++){var s=t.charCodeAt(o);if(r!==0||i||s!==vr||t.charCodeAt(o+1)!==42)if(i)s===42&&t.charCodeAt(o+1)===vr&&(i=!1,o++);else if(s!==34&&s!==39||o!==0&&t.charCodeAt(o-1)===92){if(r===0){if(s===123)n++;else if(s===125&&--n<0)return!0}}else r===0?r=s:r===s&&(r=0);else i=!0,o++}return n!==0||r!==0}function vf(t,e){return t.map(function(n){return n.type==="rule"&&(n.value="".concat(e," ").concat(n.value),n.value=n.value.replaceAll(",",",".concat(e," ")),n.props=n.props.map(function(r){return"".concat(e," ").concat(r)})),Array.isArray(n.children)&&n.type!=="@keyframes"&&(n.children=vf(n.children,e)),n})}function Vy(t){var e,n,r,i=xr,o=i.options,s=o===void 0?xr:o,l=i.plugins,c=l===void 0?ps:l,d=function(g,v,x){return x.startsWith(n)&&x.endsWith(n)&&x.replaceAll(n,"").length>0?".".concat(e):g},u=c.slice();u.push(function(g){g.type===rs&&g.value.includes("&")&&(g.props[0]=g.props[0].replace(Gy,n).replace(r,d))}),s.prefix&&u.push(xy),u.push(fy);var f=function(g,v,x,b){v===void 0&&(v=""),x===void 0&&(x=""),b===void 0&&(b="&"),e=b,n=v,r=new RegExp("\\".concat(n,"\\b"),"g");var M=function(w){if(!wf(w))return w;for(var S=w.length,C="",$=0,k=0,j=0,L=!1,P=0;P<S;P++){var V=w.charCodeAt(P);if(j!==0||L||V!==vr||w.charCodeAt(P+1)!==42)if(L)V===42&&w.charCodeAt(P+1)===vr&&(L=!1,P++);else if(V!==34&&V!==39||P!==0&&w.charCodeAt(P-1)===92){if(j===0)if(V===123)k++;else if(V===125){if(--k<0){for(var de=P+1;de<S;){var me=w.charCodeAt(de);if(me===59||me===10)break;de++}de<S&&w.charCodeAt(de)===59&&de++,k=0,P=de-1,$=de;continue}k===0&&(C+=w.substring($,P+1),$=P+1)}else V===59&&k===0&&(C+=w.substring($,P+1),$=P+1)}else j===0?j=V:j===V&&(j=0);else L=!0,P++}if($<S){var ct=w.substring($);wf(ct)||(C+=ct)}return C}(function(w){if(w.indexOf("//")===-1)return w;for(var S=w.length,C=[],$=0,k=0,j=0,L=0;k<S;){var P=w.charCodeAt(k);if(P!==34&&P!==39||k!==0&&w.charCodeAt(k-1)===92)if(j===0)if(P===40&&k>=3&&(32|w.charCodeAt(k-1))==108&&(32|w.charCodeAt(k-2))==114&&(32|w.charCodeAt(k-3))==117)L=1,k++;else if(L>0)P===41?L--:P===40&&L++,k++;else if(P===vr&&k+1<S&&w.charCodeAt(k+1)===vr){for(k>$&&C.push(w.substring($,k));k<S&&w.charCodeAt(k)!==10;)k++;$=k}else k++;else k++;else j===0?j=P:j===P&&(j=0),k++}return $===0?w:($<S&&C.push(w.substring($)),C.join(""))}(g)),y=hy(x||v?"".concat(x," ").concat(v," { ").concat(M," }"):M);s.namespace&&(y=vf(y,s.namespace));var p=[];return ds(y,gy(u.concat(my(function(w){return p.push(w)})))),p};return f.hash=c.length?c.reduce(function(g,v){return v.name||ki(15),yr(g,v.name)},5381).toString():"",f}var Yy=new yf,fc=Vy(),gc={shouldForwardProp:void 0,styleSheet:Yy,stylis:fc},bf=mr?{Provider:function(t){return t.children},Consumer:function(t){return(0,t.children)(gc)}}:oe.createContext(gc);bf.Consumer,mr||oe.createContext(void 0);function Sf(){return mr?gc:oe.useContext(bf)}var Ky=function(){function t(e,n){var r=this;this.inject=function(i,o){o===void 0&&(o=fc);var s=r.name+o.hash;i.hasNameForId(r.id,s)||i.insertRules(r.id,s,o(r.rules,s,"@keyframes"))},this.name=e,this.id="sc-keyframes-".concat(e),this.rules=n,pc(this,function(){throw ki(12,String(r.name))})}return t.prototype.getName=function(e){return e===void 0&&(e=fc),this.name+e.hash},t}();function Qy(t,e){return e==null||typeof e=="boolean"||e===""?"":typeof e!="number"||e===0||t in yy||t.startsWith("--")?String(e).trim():"".concat(e,"px")}var Xy=function(t){return t>="A"&&t<="Z"};function kf(t){for(var e="",n=0;n<t.length;n++){var r=t[n];if(n===1&&r==="-"&&t[0]==="-")return t;Xy(r)?e+="-"+r.toLowerCase():e+=r}return e.startsWith("ms-")?"-"+e:e}var $f=function(t){return t==null||t===!1||t===""},Cf=function(t){var e=[];for(var n in t){var r=t[n];t.hasOwnProperty(n)&&!$f(r)&&(Array.isArray(r)&&r.isCss||wr(r)?e.push("".concat(kf(n),":"),r,";"):Si(r)?e.push.apply(e,ns(ns(["".concat(n," {")],Cf(r),!1),["}"],!1)):e.push("".concat(kf(n),": ").concat(Qy(n,r),";")))}return e};function $n(t,e,n,r){if($f(t))return[];if(uc(t))return[".".concat(t.styledComponentId)];if(wr(t)){if(!wr(o=t)||o.prototype&&o.prototype.isReactComponent||!e)return[t];var i=t(e);return $n(i,e,n,r)}var o;return t instanceof Ky?n?(t.inject(n,r),[t.getName(r)]):[t]:Si(t)?Cf(t):Array.isArray(t)?Array.prototype.concat.apply(ps,t.map(function(s){return $n(s,e,n,r)})):[t.toString()]}function Zy(t){for(var e=0;e<t.length;e+=1){var n=t[e];if(wr(n)&&!uc(n))return!1}return!0}var qy=sf(us),Jy=function(){function t(e,n,r){this.rules=e,this.staticRulesId="",this.isStatic=(r===void 0||r.isStatic)&&Zy(e),this.componentId=n,this.baseHash=yr(qy,n),this.baseStyle=r,yf.registerId(n)}return t.prototype.generateAndInjectStyles=function(e,n,r){var i=this.baseStyle?this.baseStyle.generateAndInjectStyles(e,n,r).className:"";if(this.isStatic&&!r.hash)if(this.staticRulesId&&n.hasNameForId(this.componentId,this.staticRulesId))i=kn(i,this.staticRulesId);else{var o=ff($n(this.rules,e,n,r)),s=lc(yr(this.baseHash,o)>>>0);if(!n.hasNameForId(this.componentId,s)){var l=r(o,".".concat(s),void 0,this.componentId);n.insertRules(this.componentId,s,l)}i=kn(i,s),this.staticRulesId=s}else{for(var c=yr(this.baseHash,r.hash),d="",u=0;u<this.rules.length;u++){var f=this.rules[u];if(typeof f=="string")d+=f;else if(f){var g=ff($n(f,e,n,r));c=yr(c,g+u),d+=g}}if(d){var v=lc(c>>>0);if(!n.hasNameForId(this.componentId,v)){var x=r(d,".".concat(v),void 0,this.componentId);n.insertRules(this.componentId,v,x)}i=kn(i,v)}}return{className:i,css:typeof window>"u"?n.getTag().getGroup($i(this.componentId)):""}},t}(),Tf=mr?{Provider:function(t){return t.children},Consumer:function(t){return(0,t.children)(void 0)}}:oe.createContext(void 0);Tf.Consumer;var mc={};function tw(t,e,n){var r=uc(t),i=t,o=!dc(t),s=e.attrs,l=s===void 0?ps:s,c=e.componentId,d=c===void 0?function(S,C){var $=typeof S!="string"?"sc":rf(S);mc[$]=(mc[$]||0)+1;var k="".concat($,"-").concat($y(us+$+mc[$]));return C?"".concat(C,"-").concat(k):k}(e.displayName,e.parentComponentId):c,u=e.displayName,f=u===void 0?function(S){return dc(S)?"styled.".concat(S):"Styled(".concat(Cy(S),")")}(t):u,g=e.displayName&&e.componentId?"".concat(rf(e.displayName),"-").concat(e.componentId):e.componentId||d,v=r&&i.attrs?i.attrs.concat(l).filter(Boolean):l,x=e.shouldForwardProp;if(r&&i.shouldForwardProp){var b=i.shouldForwardProp;if(e.shouldForwardProp){var M=e.shouldForwardProp;x=function(S,C){return b(S,C)&&M(S,C)}}else x=b}var y=new Jy(n,g,r?i.componentStyle:void 0);function p(S,C){return function($,k,j){var L=$.attrs,P=$.componentStyle,V=$.defaultProps,de=$.foldedComponentIds,me=$.styledComponentId,ct=$.target,vd=mr?void 0:oe.useContext(Tf),zi=Sf(),In=$.shouldForwardProp||zi.shouldForwardProp,E=vy(k,vd,V)||xr,I=function(_s,jr,Hs){for(var Bi,Ot=Ae(Ae({},jr),{className:void 0,theme:Hs}),bd=0;bd<_s.length;bd+=1){var Ws=wr(Bi=_s[bd])?Bi(Ot):Bi;for(var Er in Ws)Er==="className"?Ot.className=kn(Ot.className,Ws[Er]):Er==="style"?Ot.style=Ae(Ae({},Ot.style),Ws[Er]):Ot[Er]=Ws[Er]}return"className"in jr&&typeof jr.className=="string"&&(Ot.className=kn(Ot.className,jr.className)),Ot}(L,k,E),O=I.as||ct,W={};for(var U in I)I[U]===void 0||U[0]==="$"||U==="as"||U==="theme"&&I.theme===E||(U==="forwardedAs"?W.as=I.forwardedAs:In&&!In(U,O)||(W[U]=I[U]));var On=function(_s,jr){var Hs=Sf(),Bi=_s.generateAndInjectStyles(jr,Hs.styleSheet,Hs.stylis);return Bi}(P,I),dt=On.className,Mr=On.css,ut=kn(de,me);dt&&(ut+=" "+dt),I.className&&(ut+=" "+I.className),W[dc(O)&&!nf.has(O)?"class":"className"]=ut,j&&(W.ref=j);var Ln=R.createElement(O,W);return mr&&Mr?oe.createElement(oe.Fragment,null,oe.createElement("style",{precedence:"styled-components",href:"sc-".concat(me,"-").concat(dt),children:Mr}),Ln):Ln}(w,S,C)}p.displayName=f;var w=oe.forwardRef(p);return w.attrs=v,w.componentStyle=y,w.displayName=f,w.shouldForwardProp=x,w.foldedComponentIds=r?kn(i.foldedComponentIds,i.styledComponentId):"",w.styledComponentId=g,w.target=r?i.target:t,Object.defineProperty(w,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(S){this._foldedDefaultProps=r?function(C){for(var $=[],k=1;k<arguments.length;k++)$[k-1]=arguments[k];for(var j=0,L=$;j<L.length;j++)hc(C,L[j],!0);return C}({},i.defaultProps,S):S}}),pc(w,function(){return".".concat(w.styledComponentId)}),o&&pf(w,t,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),w}function Mf(t,e){for(var n=[t[0]],r=0,i=e.length;r<i;r+=1)n.push(e[r],t[r+1]);return n}var jf=function(t){return Object.assign(t,{isCss:!0})};function br(t){for(var e=[],n=1;n<arguments.length;n++)e[n-1]=arguments[n];if(wr(t)||Si(t))return jf($n(Mf(ps,ns([t],e,!0))));var r=t;return e.length===0&&r.length===1&&typeof r[0]=="string"?$n(r):jf($n(Mf(r,e)))}function xc(t,e,n){if(n===void 0&&(n=xr),!e)throw ki(1,e);var r=function(i){for(var o=[],s=1;s<arguments.length;s++)o[s-1]=arguments[s];return t(e,n,br.apply(void 0,ns([i],o,!1)))};return r.attrs=function(i){return xc(t,e,Ae(Ae({},n),{attrs:Array.prototype.concat(n.attrs,i).filter(Boolean)}))},r.withConfig=function(i){return xc(t,e,Ae(Ae({},n),i))},r}var Ef=function(t){return xc(tw,t)},h=Ef;nf.forEach(function(t){h[t]=Ef(t)});const m={colors:{windowBg:"#152029de",panelBg:"#04161c",panelBgGlass:"rgba(4, 22, 28, 0.22)",line:"#496791",text:"#ffffff",textAccent:"#C6E2FF",textDim:"#7f9bb8",healthOk:"#427231",healthCrit:"#ed6738",warning:"#e1b000",warningSoft:"#e6b400",statusOk:"limegreen",statusAlert:"#e1b000",statusBad:"red",statusPending:"#00b8e6",enhText:"#d8be86",enhTitle:"#e8cf93",enhBg:"rgba(169, 128, 56, 0.30)",enhLine:"#8a6d3b",greenBg:"rgba(8, 28, 12, 0.92)",greenLine:"#3f8a3f",greenTitleBg:"#16401b",greenText:"#e6ffe6",greenLabel:"#bdf0bd",greenBtnBg:"#1b5e20",greenBtnLine:"#2e7d32",greenBtnLineHover:"#66bb6a",greenGlow:"#4caf50",custom:"#cccc00",chromeText:"#deebff",overlayBg:"black",overlayBgSoft:"rgba(0, 0, 0, 0.65)"},fonts:{body:"arial",mono:'Consolas, "Lucida Console", monospace',display:'"Orbitron", sans-serif'},radii:{modal:"0px",tooltip:"7px"},hud:{btn:"36px",btnSmall:"25px",gap:"10px",gapSmall:"5px",icon:"26px",iconSmall:"17px",glyph:"30px",glyphSmall:"20px"}},nw=h.div`
    border: 1px solid ${m.colors.line};
    color: ${m.colors.chromeText};
    background-color: ${m.colors.windowBg};
    font-family:${m.fonts.body};
`,rw=h.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,Ci=h(nw)`
    box-shadow: 5px 5px 10px ${m.colors.overlayBg};
`,iw=h.span`
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
`;h(iw)`
    font-weight: normal;
`;const We=br`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;class Ue extends R.Component{render(){return a.jsxs(ow,{children:[a.jsx(sw,{children:this.props.label}),a.jsx(aw,{type:this.props.type||"text",value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:"0"})]})}}const ow=h.div`
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
`,sw=h.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,aw=h.input`
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
`;class lw extends R.Component{render(){const{children:e,className:n}=this.props;return a.jsx("div",{className:n,children:e})}}const Df=h(lw)`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${m.fonts.body};
    font-size:12px;
    color:${m.colors.text};
    background-color:${m.colors.overlayBgSoft};
    border-radius: ${m.radii.tooltip};
    -moz-border-radius: ${m.radii.tooltip};
    -webkit-border-radius: ${m.radii.tooltip};
    padding:3px 3px 3px 3px;
    padding-bottom: 8px;
`,Pf=h.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,Af=h.div`
    color: ${t=>t.$type=="good"?"#6fc126;":t.$type=="bad"?"#ff7b3f;":"white;"}
    font-weight: ${t=>t.$important?"bold":"inherit"};
    font-size: ${t=>t.$important?"14px":"12px"};
    margin-top: ${t=>t.$space?"14px":"0"};
`;class cw extends R.Component{getOnChange(e){return n=>{this.props.set(e,n.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(e){return n=>{if(console.log("keydown"),n.preventDefault(),n.stopPropagation(),!Rf[n.keyCode])return;const r={keyCode:n.keyCode,shiftKey:n.shiftKey,altKey:n.altKey,ctrlKey:n.ctrlKey,metaKey:n.metaKey};this.props.set(e,r),this.props.save(),this.forceUpdate()}}getKey(e){return console.log(this.props.settings),dw(this.props.settings[e])}get(e){return this.props.settings[e]}render(){return a.jsx(uw,{onClick:this.props.close,children:a.jsxs(hw,{onClick:e=>e.stopPropagation(),children:[a.jsxs(pw,{children:[a.jsx(fw,{children:"Player Settings"}),a.jsx(gw,{onClick:this.props.close,title:"Close",children:"✕"})]}),a.jsxs(mw,{children:[a.jsx(xw,{children:"Settings apply to this browser and device only. Reload the page for changes to take effect."}),a.jsx(xs,{children:"Keys"}),a.jsx(Ue,{label:"Display ALL Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllEW"),value:this.getKey.call(this,"ShowAllEW")}),a.jsx(Ue,{label:"Display FRIENDLY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyEW"),value:this.getKey.call(this,"ShowFriendlyEW")}),a.jsx(Ue,{label:"Display ENEMY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyEW"),value:this.getKey.call(this,"ShowEnemyEW")}),a.jsx(Ue,{label:"Display ALL Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllBallistics"),value:this.getKey.call(this,"ShowAllBallistics")}),a.jsx(Ue,{label:"Display FRIENDLY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyBallistics"),value:this.getKey.call(this,"ShowFriendlyBallistics")}),a.jsx(Ue,{label:"Display ENEMY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyBallistics"),value:this.getKey.call(this,"ShowEnemyBallistics")}),a.jsx(Ue,{label:"Toggle RULER tool",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleLoS"),value:this.getKey.call(this,"ToggleLoS")}),a.jsx(Ue,{label:"Toggle HEX numbers",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleHexNumbers"),value:this.getKey.call(this,"ToggleHexNumbers")}),a.jsx(Ue,{label:"Toggle MAP background",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleBackground"),value:this.getKey.call(this,"ToggleBackground")}),a.jsx(Ue,{label:"Close all SHIP WINDOWS and tooltips",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"CloseAllWindows"),value:this.getKey.call(this,"CloseAllWindows")}),a.jsx(xs,{children:"Replay"}),a.jsx(Ue,{label:"Play / pause Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"TogglePlayPause"),value:this.getKey.call(this,"TogglePlayPause")}),a.jsx(xs,{children:"Sound"}),a.jsx(Ue,{label:"Toggle sound in Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleSound"),value:this.getKey.call(this,"ToggleSound")}),a.jsx(xs,{children:"Visual"}),a.jsx(Ue,{placeholder:"0",type:"number",label:"Zoom level to switch to strategic view",onChange:this.getOnChange.call(this,"ZoomLevelToStrategic"),value:this.get.call(this,"ZoomLevelToStrategic")}),a.jsx(yw,{children:"Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners."})]})]})})}}const dw=t=>{let e=Rf[t.keyCode];return e=e.toUpperCase(),t.shiftKey&&(e+=" + shift"),t.altKey&&(e+=" + alt"),t.ctrlKey&&(e+=" + ctrl"),t.metaKey&&(e+=" + cmd"),e},uw=h(rw)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,hw=h.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: ${m.colors.panelBg};
    border: 1px solid ${m.colors.line};
    border-radius: ${m.radii.modal};
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: ${m.colors.chromeText};
    font-family: ${m.fonts.body};
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`,pw=h.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${m.colors.windowBg};
    border-bottom: 1px solid ${m.colors.line};
`,fw=h.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,gw=h.div`
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
    ${We}

    &:hover {
        color: #d6f7fd;
        border-color: #49c4d4;
        background-color: rgba(73, 196, 212, 0.12);
    }
`,mw=h.div`
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
`,xw=h.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`,xs=h.div`
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
`,yw=h.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,Rf={27:"esc",32:"space",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",58:":",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z"};class ww extends R.Component{constructor(e){super(e),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?a.jsx(cw,{close:this.close.bind(this),...this.props}):a.jsx(vw,{onClick:this.open.bind(this),children:"⚙"})}}const vw=h(Ci)`
    width: ${m.hud.btn};
    height: ${m.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${m.hud.glyph};
    border-right: none;
    border-top: none;
    ${We}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${m.hud.btnSmall};
        height: ${m.hud.btnSmall};
        font-size: ${m.hud.glyphSmall};

    }
`,If="fv-thrust-relayout",on=8,ys=6,Ti=40,yc=60,ws=80,bw=4,Sw=(t,e,n)=>{const r=window.coordinateConverter&&window.coordinateConverter.zoom||1,i=Math.min((t.canvasSize||200)*.75,250)/2/r+bw,o=Math.min(i,ws+Ti),s=d=>n.type==="roll"||!Array.isArray(e)||e[d]!==null,l=d=>s(d)?shipManager.systems.getThrusters(t,d).length:0,c=Math.max(l(1),l(2))*Ti/2;return{x:Math.min(i,yc),y:Math.max(Math.min(i,ws),c),clear:o}},Of=h.span`
    font-family: ${m.fonts.mono};
    font-size: 11px;
    line-height: 1;
    padding: 1px 2px;
    color: ${t=>t.$over?m.colors.healthCrit:m.colors.text};
    background-color: rgba(0, 0, 0, 0.55);
    pointer-events: none;
`,kw=h.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    position: relative; // Needed for absolute positioning of ::before
    box-sizing: border-box;
    ${t=>t.$destroyed?`box-shadow: inset 0 0 0 1px ${m.colors.statusBad};`:t.$box==="need"?`box-shadow: inset 0 0 0 1px ${m.colors.warning};`:t.$box==="extra"?`box-shadow: inset 0 0 0 1px ${m.colors.greenBtnLineHover};`:""}

    &::before {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: -1;
        /* thrusterGreen*.png: the ship window's blue thruster1*.png with only the blue body repainted
           in the panel's green (theme.colors.greenLine, shading kept) - the red critical bands are
           untouched, which a CSS hue-rotate could not do (it turned them purple). */
        background-image: ${t=>{switch(t.$crits){case 11:return"url(img/systemicons/thrusterGreen-critical12.png);";case 10:return"url(img/systemicons/thrusterGreen-critical1.png);";case 1:return"url(img/systemicons/thrusterGreen-critical2.png);";default:return"url(img/systemicons/thrusterGreen.png);"}}}
        background-size: cover;
        transform: ${t=>{switch(t.$direction){case 4:return"rotate(180deg)";case 1:return"rotate(90deg)";case 2:return"rotate(270deg)";default:return"none"}}};
        ${t=>t.$destroyed?"opacity: 0.3;":""}
    }

    ${t=>t.$destroyed?"cursor: default;":We}
`,Lf=h.div`
    display: flex;
    position: absolute;
`,zf=h(Lf)`
    flex-direction: row;
    left: var(--fv-ring-x, ${yc}px);
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: ${Ti}px;
`,$w=h(zf)`
    left: calc(-1 * var(--fv-ring-x, ${yc}px) - ${Ti}px);
`,Bf=h(Lf)`
    flex-direction: row;
    top: calc(-1 * var(--fv-ring-y, ${ws}px) - ${Ti}px);
    transform: translate(-50%, 0);
`,Cw=h(Bf)`
    top: var(--fv-ring-y, ${ws}px);
`,Tw=h.div`
    position: relative;
    transform: rotate(${t=>t.$rotation}deg);

    & ${Of} {
        transform: rotate(${t=>-t.$rotation}deg);
    }
`,Mw=h.div`
    position: absolute;
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
`,jw=h.div`
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 180px;
    box-sizing: border-box;
    background-color: ${m.colors.greenBg};
    border: 1px solid ${m.colors.greenLine};
    box-shadow: 5px 5px 10px ${m.colors.overlayBg};
    font-family: ${m.fonts.body};
    color: ${m.colors.greenText};
    text-align: left;
    user-select: none;
`,Ew=h.div`
    box-sizing: border-box;
    padding: 3px 6px;
    line-height: 1.2;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    white-space: pre;
    overflow: hidden;
    text-overflow: ellipsis;
    color: ${m.colors.greenText};
    background-color: ${m.colors.greenTitleBg};
    border-bottom: 1px solid ${m.colors.greenLine};
`,Dw=h.div`
    padding: 3px 6px 4px;
`,Cn=h.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px;
    padding: 1px 0;
`,Tn=h.span`
    font-size: 10px;
    color: ${m.colors.greenLabel};
    white-space: nowrap;
`,Nf=()=>window.UI&&window.UI.shipMovement&&window.UI.shipMovement.extendedTurnColour||"#ff8c00",Mn=h.span`
    font-family: ${m.fonts.mono};
    font-size: 11px;
    white-space: nowrap;
    color: ${t=>t.$state==="ok"?m.colors.statusOk:t.$state==="open"?m.colors.warning:t.$state==="owed"?Nf():m.colors.greenText};
`,wc=h.div`
    padding: 2px 0 1px;
    font-size: 10px;
    line-height: 1.25;
    color: ${m.colors.greenLabel};
`,Pw=h.div`
    position: relative;
    height: 4px;
    margin: 2px 0 3px;
    background-color: rgba(0, 0, 0, 0.45);
    border: 1px solid ${m.colors.greenLine};
`,Ff=h.div`
    position: absolute;
    top: 0;
    bottom: 0;
    background-color: ${t=>t.$owed?Nf():m.colors.statusOk};
`,Aw=h.div`
    position: absolute;
    top: -3px;
    bottom: -3px;
    width: 1px;
    background-color: ${m.colors.greenText};
`,Rw=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 3px;
    min-height: 16px;
    font-size: 10px;
    color: ${m.colors.greenText};
    cursor: ${t=>t.$locked?"not-allowed":"pointer"};

    @media (pointer: coarse) {
        min-height: 32px;
    }
`,Iw=h.span`
    position: relative;
    flex: 0 0 auto;
    width: 10px;
    height: 10px;
    box-sizing: border-box;
    border: 1px solid ${t=>t.$locked?m.colors.greenLine:m.colors.greenBtnLineHover};
    background-color: ${t=>t.$checked?m.colors.greenBtnLine:"rgba(0, 0, 0, 0.45)"};

    ${t=>t.$checked&&`
    &::after {
        content: "";
        position: absolute;
        left: 2.5px;
        top: 0;
        width: 3px;
        height: 6px;
        border-right: 1.5px solid ${m.colors.greenText};
        border-bottom: 1.5px solid ${m.colors.greenText};
        transform: rotate(45deg);
    }`}
`,Ow=h.div`
    height: 1px;
    margin: 3px 0;
    background-color: ${m.colors.greenLine};
`,Lw=h.div`
    display: flex;
    gap: 4px;
    padding: 0 6px 6px;
`,_f=h.div`
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
    background: ${m.colors.greenBtnBg};
    border: 1px solid ${m.colors.greenBtnLine};
    color: ${m.colors.greenText};

    &:hover {
        background: ${m.colors.greenBtnLine};
        border: 1px solid ${m.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${t=>t.$primary&&!t.disabled&&`
        background: ${m.colors.greenBtnLine};
        border: 1px solid ${m.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${m.colors.greenGlow};
        color: #ffffff;
    `}

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${m.colors.greenBtnBg}; border: 1px solid ${m.colors.greenBtnLine}; color: ${m.colors.greenText}; }
    `}

    @media (pointer: coarse) {
        height: 32px;
    }
`,zw=["Either","Front","Aft","Port","Stbd"],Bw=[1,2,3,4,0],Sr=t=>t?"Starboard":"Port",Nw=t=>shipManager.movement.isExtendedTurnStart(t)?"begin":shipManager.movement.isExtendedTurnCompletion(t)?"complete":"normal",Fw=(t,e)=>{switch(e.type){case"extendTurnLeft":case"extendTurnRight":return`Begin Extended Turn
`+Sr(e.type==="extendTurnRight");case"turnleft":case"turnright":return e.value==="turnIntoPivot"?"Turn into Pivot · "+Sr(e.type==="turnright"):e.value==="extendedTurn"?`Complete Extended Turn
`+Sr(e.type==="turnright"):"Turn "+Sr(e.type==="turnright");case"pivotleft":case"pivotright":return"Pivot "+Sr(e.type==="pivotright");case"slipleft":case"slipright":return"Slip "+Sr(e.type==="slipright");case"roll":return e.value==="emergencyRoll"?"Emergency Roll":"Roll";case"speedchange":{const n=t.movement.lastIndexOf(e),r=n>0?t.movement[n-1]:null;return r&&(e.speed<r.speed||e.heading!=r.heading)?"Decelerate":"Accelerate"}case"jink":return"Jink";case"halfPhase":return"Half-Phase";case"contract":return"Contraction";default:return"Assign Thrust"}},_w=(t,e)=>{if(!Array.isArray(t)||!Array.isArray(e))return{rows:[],extra:0};const n=[1,2,3,4].every(o=>!(t[o]>0)),r=[];Bw.forEach(o=>{const s=t[o];if(!(s>0)||o>0&&e[o]===null)return;const l=Math.max(0,e[o]||0);r.push({slot:o,label:o===0?n?"Any":"Either":zw[o],paid:s-l,required:s,open:l})});const i=e[0]<0?-e[0]:0;return{rows:r,extra:i}},Hw=t=>Array.isArray(t)&&!t.some(e=>e>0),Ww=(t,e,n)=>{const r={left:e.left-n,top:e.top-n,right:e.left+n,bottom:e.top+n};return Array.from(t.children).forEach(i=>{const o=i.getBoundingClientRect();o.width===0||o.height===0||(r.left=Math.min(r.left,o.left),r.top=Math.min(r.top,o.top),r.right=Math.max(r.right,o.right),r.bottom=Math.max(r.bottom,o.bottom))}),r};class Uw extends R.Component{constructor(e){super(e),this.containerRef=R.createRef(),this.ringRef=R.createRef(),this.panelRef=R.createRef(),this.layout=this.layout.bind(this)}componentDidMount(){this.layout(),window.addEventListener("resize",this.layout),this.container=this.containerRef.current,this.container&&this.container.addEventListener(If,this.layout)}componentDidUpdate(){this.layout()}componentWillUnmount(){window.removeEventListener("resize",this.layout),this.container&&this.container.removeEventListener(If,this.layout)}layout(){const e=this.containerRef.current,n=this.ringRef.current,r=this.panelRef.current;if(!e||!n||!r)return;const{ship:i,totalRequired:o,movement:s}=this.props,l=Sw(i,o,s);n.style.setProperty("--fv-ring-x",l.x+"px"),n.style.setProperty("--fv-ring-y",l.y+"px"),this.placePanel(e,n,r,l.clear)}placePanel(e,n,r,i){const o=e.getBoundingClientRect(),s=Ww(n,o,i),l=window.innerWidth,c=window.innerHeight,d=r.offsetWidth,u=r.offsetHeight,f=y=>Math.min(Math.max(y,on),l-d-on),g=y=>Math.min(Math.max(y,on),c-u-on),v={left:f(o.left-d/2),top:s.bottom+ys},x=[v,{left:f(o.left-d/2),top:s.top-ys-u},{left:s.right+ys,top:g(o.top-u/2)},{left:s.left-ys-d,top:g(o.top-u/2)}],b=y=>y.left>=on&&y.top>=on&&y.left+d<=l-on&&y.top+u<=c-on,M=x.find(b)||{left:v.left,top:g(v.top)};r.style.left=Math.round(M.left-o.left)+"px",r.style.top=Math.round(M.top-o.top)+"px"}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}toggleExtendedTurn(e){e.enabled&&window.shipManager.movement.setExtendedTurn(this.props.ship,!e.checked)}render(){const{ship:e,position:n,rotation:r,totalRequired:i,remainginRequired:o,movement:s}=this.props,l=Math.round(Math.abs(r)),{rows:c,extra:d}=_w(i,o),u=Nw(s),f=shipManager.movement.getExtendedTurnToggle(e,s);let g,v,x=null;u==="begin"?(x=shipManager.movement.getExtendedTurnStartPayment(e,s),g=x.valid,v=x.paid<x.minimum?`Pay at least ${x.minimum} now (${x.paid} so far)`:"Leave at least 1 thrust owed for next turn"):(g=Hw(o),v="Still needed: "+c.filter(M=>M.open>0).map(M=>`${M.label} ${M.open}`).join(" · "));const b=M=>M.open===0?"ok":u==="begin"?"owed":"open";return a.jsxs(Mw,{ref:this.containerRef,onMouseOver:M=>M.preventDefault(),onContextMenu:M=>M.preventDefault(),id:"thrustUIContainer",style:{left:`${n.x}px`,top:`${n.y}px`},children:[a.jsxs(Tw,{ref:this.ringRef,style:{transform:`rotate(${l}deg)`},$rotation:l,children:[a.jsx(zf,{children:vs(e,1,i,o,s)}),a.jsx(Bf,{children:vs(e,3,i,o,s)}),a.jsx(Cw,{children:vs(e,4,i,o,s)}),a.jsx($w,{children:vs(e,2,i,o,s)})]}),a.jsxs(jw,{ref:this.panelRef,children:[a.jsx(Ew,{children:Fw(e,s)}),a.jsxs(Dw,{children:[c.map(M=>a.jsxs(Cn,{children:[a.jsx(Tn,{children:M.label}),a.jsxs(Mn,{$state:b(M),children:[M.paid,"/",M.required]})]},`thrust-row-${M.slot}`)),d>0&&a.jsxs(Cn,{children:[a.jsx(Tn,{children:"Extra thrust"}),a.jsxs(Mn,{$state:"ok",children:["+",d]})]}),a.jsx(Ow,{}),x&&Vw(x),a.jsxs(Cn,{children:[a.jsx(Tn,{children:"Engine thrust"}),a.jsx(Mn,{children:shipManager.movement.getRemainingEngineThrust(e)})]}),Gw(e,s),u==="begin"&&a.jsx(wc,{children:"Completes on next turn's movement"}),u==="complete"&&a.jsx(wc,{children:"Begun last turn"}),f&&a.jsxs(a.Fragment,{children:[a.jsxs(Rw,{$locked:!f.enabled,title:f.enabled?f.checked?"Make a normal turn instead":"Pay part of this turn now and the rest next turn":f.hint,onClick:()=>this.toggleExtendedTurn(f),children:[a.jsx(Iw,{$checked:f.checked,$locked:!f.enabled}),"Make Extended Turn"]}),!f.enabled&&f.hint&&a.jsx(wc,{children:f.hint})]})]}),a.jsxs(Lw,{children:[a.jsx(_f,{$primary:!0,disabled:!g,title:g?"Confirm this manoeuvre":v,onClick:g?this.ready.bind(this):void 0,children:"Confirm"}),a.jsx(_f,{onClick:this.cancel.bind(this),children:"Cancel"})]})]})]})}}const Gw=(t,e)=>{if(!shipManager.movement.isTurn(e))return null;const n=shipManager.movement.calculateTurndelay(t,e,e.speed);return a.jsxs(Cn,{children:[a.jsx(Tn,{children:"Turn delay"}),a.jsx(Mn,{children:n})]})},Vw=t=>{const e=t.paid>=t.minimum,n=t.owed.any+t.owed.rear+t.owed.side,r=i=>(t.cost>0?Math.min(100,Math.max(0,i/t.cost*100)):0)+"%";return a.jsxs(a.Fragment,{children:[a.jsxs(Cn,{children:[a.jsx(Tn,{children:"Turn cost"}),a.jsx(Mn,{children:t.cost})]}),a.jsxs(Cn,{children:[a.jsxs(Tn,{children:["Paying now (min ",t.minimum,")"]}),a.jsx(Mn,{$state:e?"ok":"open",children:t.paid})]}),a.jsxs(Pw,{title:`At least ${t.minimum} now, at most ${t.cost-1}`,children:[a.jsx(Ff,{style:{left:0,width:r(t.paid)}}),a.jsx(Ff,{$owed:!0,style:{left:r(t.paid),width:r(t.cost-t.paid)}}),a.jsx(Aw,{style:{left:r(t.minimum)}})]}),a.jsxs(Cn,{children:[a.jsx(Tn,{children:"Thrust owed next turn"}),a.jsx(Mn,{$state:"owed",children:n})]})]})},vs=(t,e,n,r,i)=>{const o=shipManager.systems.getThrusters(t,e);if(i.type!=="roll"&&n[e]===null)return null;const s=Array.isArray(r)&&(r[e]>0||r[0]>0);return o.map((l,c)=>{const d=shipManager.systems.isDestroyed(t,l),u=d||!shipManager.movement.wouldAcceptThrust(t,l)?void 0:s?"need":"extra",f=()=>{shipManager.movement.assignThrust(t,l),shipManager.movement.updateAssignThrust(t)},g=M=>{M.preventDefault(),shipManager.movement.unAssignThrust(t,l),shipManager.movement.updateAssignThrust(t)};let v=shipManager.criticals.hasCritical(l,"HalfEfficiency")?10:0;shipManager.criticals.hasCritical(l,"FirstThrustIgnored")&&(v+=1);const x=shipManager.movement.getAmountChanneled(t,l),b=shipManager.systems.getOutput(t,l);return a.jsx(kw,{$crits:v,$direction:e,$destroyed:d,$box:u,onClick:d?void 0:f,onContextMenu:d?M=>M.preventDefault():g,children:a.jsxs(Of,{$over:x>b,children:[x,"/",b]})},`thruster-${e}-${c}`)})},Yw=()=>a.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[a.jsx("path",{d:"M9 3H3v6"}),a.jsx("path",{d:"M15 3h6v6"}),a.jsx("path",{d:"M9 21H3v-6"}),a.jsx("path",{d:"M15 21h6v-6"})]});class Kw extends R.Component{fullScreen(){var e=window.document,n=e.documentElement,r=n.requestFullscreen||n.mozRequestFullScreen||n.webkitRequestFullScreen||n.msRequestFullscreen,i=e.exitFullscreen||e.mozCancelFullScreen||e.webkitExitFullscreen||e.msExitFullscreen;!e.fullscreenElement&&!e.mozFullScreenElement&&!e.webkitFullscreenElement&&!e.msFullscreenElement?r.call(n):i.call(e)}render(){return a.jsx(Qw,{onClick:this.fullScreen.bind(this),title:"Full screen",children:a.jsx(Yw,{})})}}const Qw=h(Ci)`
    width: ${m.hud.btn};
    height: ${m.hud.btn};
    position: fixed;
    right: calc((${m.hud.btn} + ${m.hud.gap}) * 2);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${We}

    svg {
        width: ${m.hud.icon};
        height: ${m.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${m.hud.btnSmall};
        height: ${m.hud.btnSmall};
        right: calc((${m.hud.btnSmall} + ${m.hud.gapSmall}) * 2);

        svg {
            width: ${m.hud.iconSmall};
            height: ${m.hud.iconSmall};
        }
    }
`;class Xw extends R.Component{constructor(e){super(e),this.state={available:Hf()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{const e=Hf();this.state.available!==e&&this.setState({available:e})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?a.jsx(qw,{onClick:this.surrender,title:"Surrender",children:a.jsx("img",{src:Zw(),alt:""})}):null}}const Hf=()=>{if(typeof window.gamedata>"u")return!1;const t=window.gamedata;if(t.replay||!t.isPlayerInGame()||t.status==="SURRENDERED"||t.status==="FINISHED")return!1;for(const e in t.slots){const n=t.slots[e];if(n.playerid==t.thisplayer&&n.surrendered!==null&&n.surrendered!==void 0)return!1}return!0},Zw=()=>{const t="./img/surrender_icon1.png";return window.AssetManager?window.AssetManager.getSmartImagePath(t):t},Wf="34px",Uf="22px",qw=h(Ci)`
    width: ${m.hud.btn};
    height: ${m.hud.btn};
    position: fixed;
    right: calc(${m.hud.btn} + ${m.hud.gap});
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${We}

    img {
        width: ${Wf};
        height: ${Wf};
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
        width: ${m.hud.btnSmall};
        height: ${m.hud.btnSmall};
        right: calc(${m.hud.btnSmall} + ${m.hud.gapSmall});

        img {
            width: ${Uf};
            height: ${Uf};
            transform: translateY(${"-2px"});
        }
    }
`,Jw=()=>a.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[a.jsx("path",{d:"M3 10.5L12 3l9 7.5"}),a.jsx("path",{d:"M5 9v12h14V9"}),a.jsx("path",{d:"M10 21v-6h4v6"})]});class ev extends R.Component{render(){return a.jsx(tv,{as:"a",href:"games.php",title:"Back to Lobby",children:a.jsx(Jw,{})})}}const tv=h(Ci)`
    width: ${m.hud.btn};
    height: ${m.hud.btn};
    position: fixed;
    right: calc((${m.hud.btn} + ${m.hud.gap}) * 3);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${We}

    svg {
        width: ${m.hud.icon};
        height: ${m.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${m.hud.btnSmall};
        height: ${m.hud.btnSmall};
        right: calc((${m.hud.btnSmall} + ${m.hud.gapSmall}) * 3);

        svg {
            width: ${m.hud.iconSmall};
            height: ${m.hud.iconSmall};
        }
    }
`;class nv extends oe.Component{constructor(e){super(e),this.state={losToggled:!1,hexToggled:!1,soundToggled:gamedata.playAudio!==!1,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener("LoSToggled",this.externalToggleLoS),window.addEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.addEventListener("BackgroundToggled",this.externalToggleBackground),window.addEventListener("soundToggled",this.externalToggleSound),this.replayCheck=setInterval(()=>{const e=this.getReplayMode();this.state.replayMode!==e&&this.setState({replayMode:e})},500)}componentWillUnmount(){window.removeEventListener("LoSToggled",this.externalToggleLoS),window.removeEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.removeEventListener("BackgroundToggled",this.externalToggleBackground),window.removeEventListener("soundToggled",this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(e=>({hexToggled:!e.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}showFriendlyEW(e){webglScene.customEvent("ShowFriendlyEW",{up:e})}showEnemyEW(e){webglScene.customEvent("ShowEnemyEW",{up:e})}toggleFriendlyBallisticLines(e){if(e)return;const n=!this.state.fbToggled;this.setState({fbToggled:n}),webglScene.customEvent("ToggleFriendlyBallisticLines",{up:e})}toggleEnemyBallisticLines(e){if(e)return;const n=!this.state.ebToggled;this.setState({ebToggled:n}),webglScene.customEvent("ToggleEnemyBallisticLines",{up:e})}toggleLoS(e){if(e)return;const n=!this.state.losToggled;this.setState({losToggled:n}),webglScene.customEvent("ToggleLoS",{up:e}),window.dispatchEvent(new CustomEvent("LoSToggled"))}toggleHexNumbers(e){if(e)return;const n=!this.state.hexToggled;this.setState({hexToggled:n}),webglScene.customEvent("ToggleHexNumbers",{up:e}),window.dispatchEvent(new CustomEvent("HexNumbersToggled"))}toggleSound(){const e=!this.state.soundToggled;this.setState({soundToggled:e}),webglScene.customEvent("ToggleSound",{enabled:e})}toggleBackground(){const e=document.getElementById("background");if(!e)return;const n=!this.state.bgToggled;let r=this.state.originalBgImage;n?(r||(r=e.style.backgroundImage),e.style.backgroundImage="none",e.style.backgroundColor="black"):(e.style.backgroundImage=r||"",e.style.backgroundColor=""),this.setState({bgToggled:n,originalBgImage:r})}render(){return a.jsxs(rv,{children:[a.jsx(ov,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),a.jsx(iv,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),a.jsx(av,{$toggled:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),a.jsx(sv,{$toggled:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),a.jsx(lv,{$toggled:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),a.jsx(cv,{$toggled:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),a.jsx(dv,{$toggled:this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?"Enable Background":"Disable Background"}),this.state.replayMode&&a.jsx(uv,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?"Sound On":"Sound Off"})]})}}const rv=h.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,sn=h(Ci)`
    display: flex;
    width: ${m.hud.btn};
    height: ${m.hud.btn};
    align-items: center;
    justify-content: center;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${We}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${m.hud.btnSmall};
        height: ${m.hud.btnSmall};
    }
`,iv=h(sn)`
    background-image: url("./img/EEW.png");
`,ov=h(sn)`
    background-image: url("./img/FEW.png");
`,sv=h(sn)`
    background-image: url("./img/ballisticTarget2.png");
    box-shadow: ${t=>t.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${t=>t.$toggled?"#1b533d":m.colors.windowBg};
    border: 1px solid ${t=>t.$toggled?"limegreen":m.colors.line};
    border-right: none;
`,av=h(sn)`
    background-image: url("./img/ballisticLaunch2.png");
    box-shadow: ${t=>t.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${t=>t.$toggled?"#1b533d":m.colors.windowBg};
    border: 1px solid ${t=>t.$toggled?"limegreen":m.colors.line};
    border-right: none;
`,lv=h(sn)`
    background-image: url("./img/los1.png");
    filter: ${t=>t.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${t=>t.$toggled?"limegreen":m.colors.line};
    border-right: none;
`,cv=h(sn)`
    background-image: url("./img/hexNumber.png");
    filter: ${t=>t.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${t=>t.$toggled?"limegreen":m.colors.line};
    border-right: none;
`,dv=h(sn)`
    filter: ${t=>t.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${t=>t.$toggled?"limegreen":m.colors.line};
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
`,uv=h(sn)`
    background-image: ${t=>t.$toggled?'url("./img/soundOn.png")':'url("./img/soundOff.png")'};
    border: 1px solid ${m.colors.line};
    border-right: none;
`,hv=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,pv=h.div`
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
`,fv=h.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,gv=h.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${t=>t.img});
    background-size: cover;
    align-items: center;
    opacity: 1 !important;    
    justify-content: center;
    ${We}
    border: 1px solid ${t=>t.selected?"#ef4444":"transparent"};
    position: relative;
    box-shadow: ${t=>t.selected?"0 0 5px #b43131":"none"};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`;class mv extends R.Component{selectMode(e,n){e.stopPropagation(),e.preventDefault();const{ship:r,system:i}=this.props;weaponManager.onSetModeClicked(r,i,n)}selectAllMode(e,n){e.stopPropagation(),e.preventDefault();const{ship:r,system:i}=this.props;weaponManager.onSetModeAllClicked(r,i,n)}render(){const{ship:e,system:n}=this.props,r=this.props.showModes!==!1,i=parseInt(n.firingMode);let o="";n.iconPath?o=`./img/systemicons/${n.iconPath}`:o=`./img/systemicons/${n.name}.png`;const s=[];for(const l in n.firingModes)if(n.firingModes.hasOwnProperty(l)){const c=parseInt(l),d=n.firingModes[l],u=c===i;let f=n.modeLetters||1;n.modeLettersArray&&n.modeLettersArray[c]&&(f=n.modeLettersArray[c]),s.push(a.jsx(gv,{img:o,selected:u,onClick:g=>this.selectMode(g,c),onContextMenu:g=>this.selectAllMode(g,c),title:`Set mode: ${d} ${u?"(Current)":""} (Right click to set all)`,children:d.substring(0,f)},c))}return a.jsxs(hv,{children:[r&&a.jsx(pv,{children:"Select Firing Mode"}),a.jsxs(fv,{children:[r&&s,this.props.children]})]})}}const xv=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,yv=h.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,wv=h.div`
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
    ${t=>t.$lockHeight?`height: ${t.$lockHeight}px; max-height: ${t.$lockHeight}px;`:""}

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

`,Gf=h.div`
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
    cursor: ${t=>t.$readOnly?"default":"grab"};
    touch-action: ${t=>t.$readOnly?"auto":"none"};
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
    ${t=>t.$dragging&&`
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
    ${t=>t.$lineBefore&&`
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
    ${t=>t.$gapBefore&&`
        margin-top: ${t.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -${t.$gapSize}px;
            height: ${t.$gapSize}px;
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
    ${t=>t.$lineAtEnd&&`
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
`,vv=h.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,vc=h.span`
    font-weight: bold;
`,Vf=h.span`
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

`,bv=h(vc)`
    color: #ffb833;
    font-weight: normal;    
`,Sv=h(vc)`
    color: #ff3333;
    font-weight: normal;    
`,kv=h.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,bs=h.div`
    width: 18px;
    height: 18px;
    background-image: url(${t=>t.img});
    background-size: cover;
    cursor: pointer;
    opacity: 0.9;
    margin-left: 3px;
    &:hover {
        opacity: 1;
    }
    
     ${We}
`,$v=h.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,Cv=h.div`
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
`,Yf=h.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,Tv=h(Gf)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,bc=h.span`
    color: #00b8e6;
    font-weight: normal;
    margin-right: 4px;
`,Mv=h.input`
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
`,jv=h.span`
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
`;class Ev extends R.Component{constructor(e){super(e),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getEffectiveCriticalRepairCost(e,n){return n.name==="cnC"?4:e.repairCost}getDockedUnits(){const{ship:e,system:n}=this.props;if(!n.servicesDockedUnits)return[];const r=[],i=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const o of i)if(!(!o.isDockingBay||!Array.isArray(o.shipsDocked)))for(const s of o.shipsDocked){const l=gamedata.getShip(s.shipId);l&&r.indexOf(l)===-1&&r.push(l)}return r}getDockedRepairables(){const{system:e}=this.props,n=[];for(const r of this.getDockedUnits()){const i=Array.isArray(r.systems)?r.systems:Object.values(r.systems),o="d"+r.id+":";for(const s of i){const l=s.name==="structure"||s.name==="cnC"||s.name==="SelfRepair";if(!shipManager.systems.isDestroyed(r,s)&&s.repairPriority>=1&&s.criticals){const g=Array.isArray(s.criticals)?s.criticals:Object.values(s.criticals);for(const v of g){if(v.repairPriority===0||v.turn>=gamedata.turn||v.oneturn||v.turnend>0)continue;const x=o+s.id+"-"+v.id;let b=v.repairPriority||0,M=!1;e.priorityChanges&&x in e.priorityChanges&&e.priorityChanges[x]>=0?(b=e.priorityChanges[x],M=!0):b<10&&(b+=s.repairPriority),!(b<1)&&n.push({type:"critical",sys:s,crit:v,ownerShip:r,shipId:r.id,docked:!0,priority:b,overridden:M,cost:this.getEffectiveCriticalRepairCost(v,s),id:s.id,subId:v.id,keyId:x})}}if(!l||s.repairPriority===0)continue;const c=shipManager.systems.getTotalDamage(s);if(c<=0)continue;if(s.name==="structure"){if(shipManager.systems.isDestroyed(r,s))continue}else{const g=s.structureHomeLocation!==void 0&&s.structureHomeLocation!==null?s.structureHomeLocation:s.location;if(g!=0){const v=shipManager.systems.getStructureSystem(r,g);if(v&&shipManager.systems.isDestroyed(r,v))continue}}const d=o+s.id;let u=s.repairPriority,f=!1;e.priorityChanges&&d in e.priorityChanges&&e.priorityChanges[d]>=0&&(u=e.priorityChanges[d],f=!0),!(u<1)&&(!f&&shipManager.systems.isDestroyed(r,s)&&u<=10&&(u+=10),n.push({type:"system",sys:s,ownerShip:r,shipId:r.id,docked:!0,priority:u,overridden:f,damage:c,maxHealth:s.maxhealth,id:s.id,subId:0,keyId:d}))}}return n}getRepairableSystems(){const{ship:e,system:n}=this.props,r=[],i=[],o=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const c of o){if(c.name==="SelfRepair"||c.repairPriority===0||c.privateRepairOnly&&!n.repairRestrictedTo||n.repairRestrictedTo&&!n.repairRestrictedTo.includes(c.id)||c.name=="structure"&&shipManager.systems.isDestroyed(c.ship,c))continue;const d=c.structureHomeLocation!==void 0&&c.structureHomeLocation!==null?c.structureHomeLocation:c.location;if(c.name!="structure"&&d!=0){var s=shipManager.systems.getStructureSystem(c.ship,d);if(s&&shipManager.systems.isDestroyed(c.ship,s))continue}let u=c.repairPriority,f=!1;if(n.priorityChanges&&c.id in n.priorityChanges&&n.priorityChanges[c.id]>=0&&(u=n.priorityChanges[c.id],f=!0),!f&&shipManager.systems.isDestroyed(e,c)&&u<=10&&(u+=10),!shipManager.systems.isDestroyed(e,c)&&c.criticals){const v=Array.isArray(c.criticals)?c.criticals:Object.values(c.criticals);for(const x of v){if(x.repairPriority===0||x.turn>=gamedata.turn||x.oneturn||x.turnend>0)continue;let b=x.repairPriority||0;const M=c.id+"-"+x.id;let y=!1;n.priorityChanges&&M in n.priorityChanges&&n.priorityChanges[M]>=0?(b=n.priorityChanges[M],y=!0):b<10&&(b+=c.repairPriority),i.push({type:"critical",sys:c,crit:x,ownerShip:e,shipId:0,docked:!1,priority:b,overridden:y,cost:this.getEffectiveCriticalRepairCost(x,c),id:c.id,subId:x.id,keyId:M})}}const g=shipManager.systems.getTotalDamage(c);g>0&&r.push({type:"system",sys:c,ownerShip:e,shipId:0,docked:!1,priority:u,overridden:f,damage:g,maxHealth:c.maxhealth,id:c.id,subId:0,keyId:c.id})}const l=[...i,...r,...this.getDockedRepairables()];return l.sort((c,d)=>{if(c.priority!==d.priority)return d.priority-c.priority;const u=!!c.overridden,f=!!d.overridden;if(u!==f)return u?-1:1;if(this.lastOrder&&this.lastOrder.length>0){const x=this.lastOrder.indexOf(c.keyId),b=this.lastOrder.indexOf(d.keyId);if(x!==-1&&b!==-1)return x-b}const g=c.shipId||0,v=d.shipId||0;return g!==v?g-v:c.id!==d.id?c.id-d.id:c.subId-d.subId}),this.lastOrder=l.map(c=>c.keyId),l}handleInputChange(e,n,r){const i=e.target.value;if(i===""){this.setState(s=>({priorityInputs:{...s.priorityInputs,[n]:""}}));return}const o=parseInt(i,10);isNaN(o)||(this.setState(s=>({priorityInputs:{...s.priorityInputs,[n]:o}})),this.setPriority(n,o))}handleWheel(e,n,r){e.preventDefault();const i=e.deltaY<0?1:-1,o=r+i;o<1||this.setPriority(n,o)}componentDidUpdate(e){if(this.dragRef&&this.dragRef.started&&this.listRef){const s=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');s&&this.positionDraggedEl(s,this.dragRef,this.dragRef.lastClientY)}const n=this.getRepairableSystems(),r=this.state.priorityInputs,i={};let o=!1;n.forEach(s=>{const l=s.keyId,c=s.priority;r[l]!==void 0&&r[l]!==c&&document.activeElement!==document.getElementById(`prio-input-${l}`)&&(i[l]=c,o=!0)}),o&&this.setState(s=>({priorityInputs:{...s.priorityInputs,...i}}))}handleTop(e,n){e.stopPropagation();const r=this.getRepairableSystems();if(r.length===0)return;const i=r[0].priority,o=r.find(l=>l.keyId===n);if(!o||o.priority===i)return;let s=i+1;this.setPriority(n,s)}handleUp(e,n,r){e.stopPropagation();let i=r+1;i!==r&&this.setPriority(n,i)}handleDown(e,n,r){e.stopPropagation(),!(r<=1)&&this.setPriority(n,r-1)}handleReset(e,n){e.stopPropagation(),this.setPriority(n,-1)}setPriority(e,n){const{ship:r,system:i}=this.props;i.setOverride(e,n),webglScene.customEvent("SystemDataChanged",{ship:r,system:i})}onRowPointerDown(e,n,r,i){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest("input, .sr-action-button"))return;const o=e.currentTarget,s=o?o.offsetHeight:24,l=this.listRef?this.listRef.offsetHeight:0,c=o?o.offsetWidth:0,d=o?e.clientY-o.getBoundingClientRect().top:0;this.dragRef={keyId:n,pointerId:e.pointerId,startY:e.clientY,startIdx:r,order:i,gapSize:s,lockHeight:l,anchorWidth:c,grabOffsetInRow:d,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(e){const n=this.dragRef;if(!(!n||e.pointerId!==n.pointerId)){if(!n.started){if(Math.abs(e.clientY-n.startY)<4)return;n.started=!0}e.preventDefault(),n.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){const n=this.dragRef;if(!n||!this.listRef)return;const r=this.listRef.querySelectorAll("[data-keyid]"),i=this.listRef.getBoundingClientRect(),o=e-i.top+this.listRef.scrollTop,l=this.state.drag&&this.state.drag.dropIdx===0?n.gapSize:0;let c=0,d=null;for(let f=0;f<r.length;f++){if(r[f].getAttribute("data-keyid")===String(n.keyId)){d=r[f];continue}const g=r[f].offsetTop-l+r[f].offsetHeight/2;if(o<g)break;c++}d&&this.positionDraggedEl(d,n,e);const u=this.state.drag;(!u||u.keyId!==n.keyId||u.dropIdx!==c)&&this.setState({drag:{keyId:n.keyId,startIdx:n.startIdx,dropIdx:c,gapSize:n.gapSize,lockHeight:n.lockHeight}})}positionDraggedEl(e,n,r){const i=this.listRef.getBoundingClientRect();let o=r-i.top+this.listRef.scrollTop-n.grabOffsetInRow;const s=Math.max(0,this.listRef.scrollHeight-n.gapSize);o<0?o=0:o>s&&(o=s),e.style.top=o+"px",e.style.left="0px",e.style.width=n.anchorWidth+"px",e.style.transform="none"}updateAutoScroll(e){const n=this.listRef;if(!n){this.stopAutoScroll();return}const r=30,i=n.getBoundingClientRect(),o=n.scrollTop>0,s=n.scrollTop<n.scrollHeight-n.clientHeight-1,l=e-i.top,c=i.bottom-e;o&&l<r?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,l)/r),this.ensureAutoScrollRunning()):s&&c<r?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,c)/r),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const e=this.listRef,n=this.dragRef;if(!e||!n||this.autoScrollDir===0)return;const r=e.scrollTop;if(e.scrollTop=r+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===r){this.stopAutoScroll();return}this.updateDragForPointer(n.lastClientY),this.updateAutoScroll(n.lastClientY)}onDragEnd(e){const n=this.dragRef;if(!n||e&&e.pointerId!=null&&e.pointerId!==n.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const i=this.listRef.querySelector('[data-keyid="'+n.keyId+'"]');i&&(i.style.transform="",i.style.top="",i.style.left="",i.style.width="")}this.dragRef=null;const r=this.state.drag;this.setState({drag:null}),!(!n.started||!r)&&r.dropIdx!==n.startIdx&&this.applyDropReorder(n.order,n.keyId,r.dropIdx)}applyDropReorder(e,n,r){const{ship:i,system:o}=this.props,s=e.findIndex(g=>g.keyId===n);if(s===-1)return;const l=e.slice(),[c]=l.splice(s,1);l.splice(r,0,c);const d=l[r+1],u=l[r-1];let f;d?f=d.priority+1:u?f=Math.max(1,u.priority-1):f=1,o.setOverride(n,f);for(let g=r-1;g>=0&&!(l[g].priority>f);g--)f+=1,o.setOverride(l[g].keyId,f);webglScene.customEvent("SystemDataChanged",{ship:i,system:o})}handlePropagate(e){e.stopPropagation();const{ship:n,system:r}=this.props;for(const i of n.systems)if(i.name==="SelfRepair"&&i.id!==r.id){if(i.priorityChanges)for(const o in i.priorityChanges)(!r.priorityChanges||!(o in r.priorityChanges))&&i.setOverride(o,-1);if(r.priorityChanges)for(const o in r.priorityChanges){const s=r.priorityChanges[o];s>=0&&i.setOverride(o,s)}}webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,readOnly:n}=this.props,r=this.getRepairableSystems();let i=0;const o=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const s of o)s.name==="SelfRepair"&&i++;return a.jsxs(xv,{children:[a.jsx(yv,{children:n?"Repair Queue (view only)":"Manage Repair Queue"}),a.jsxs(wv,{ref:s=>{this.listRef=s},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[r.length===0&&a.jsx(Tv,{children:"No damaged systems"}),r.map((s,l)=>{const c=this.state.drag,d=c&&c.keyId===s.keyId;let u=!1,f=!1,g=!1;if(c&&!d){const x=r.length-1,b=l>c.startIdx?l-1:l;c.dropIdx===0&&b===0?u=!0:c.dropIdx===x&&b===x-1?f=!0:c.dropIdx===b&&(g=!0)}const v=s.ownerShip||e;return a.jsxs(Gf,{"data-keyid":s.keyId,$dragging:d,$gapBefore:u,$lineAtEnd:f,$lineBefore:g,$gapSize:c?c.gapSize:0,$readOnly:n,onPointerDown:x=>this.onRowPointerDown(x,s.keyId,l,r),children:[a.jsx(vv,{children:s.type==="critical"?a.jsxs(a.Fragment,{children:[a.jsxs(bv,{children:[s.docked&&a.jsxs(bc,{children:[v.name,":"]}),s.sys.displayName," (",s.crit.description||s.crit.phpclass,")"]}),a.jsxs(Vf,{children:["Cost: ",s.cost," ",a.jsx(Yf,{})," Id: ",s.sys.id]})]}):a.jsxs(a.Fragment,{children:[shipManager.systems.isDestroyed(v,s.sys)?a.jsxs(Sv,{children:[s.docked&&a.jsxs(bc,{children:[v.name,":"]}),s.sys.displayName]}):a.jsxs(vc,{children:[s.docked&&a.jsxs(bc,{children:[v.name,":"]}),s.sys.displayName]}),a.jsxs(Vf,{children:["HP: ",shipManager.systems.getRemainingHealth(s.sys)," / ",s.sys.maxhealth," ",a.jsx(Yf,{})," Id: ",s.sys.id]})]})}),a.jsx(kv,{children:n?a.jsx(jv,{title:"Priority (view only)",children:s.priority}):a.jsxs(a.Fragment,{children:[a.jsx(bs,{className:"sr-action-button",title:"Reset Default",onClick:x=>this.handleReset(x,s.keyId),img:"./img/iconSRCancel.png"}),a.jsx(bs,{className:"sr-action-button",title:"Decrease Priority",onClick:x=>this.handleDown(x,s.keyId,s.priority),img:"./img/systemicons/AAclasses/iconMinus.png"}),a.jsx(Mv,{id:`prio-input-${s.keyId}`,type:"number",value:this.state.priorityInputs[s.keyId]!==void 0?this.state.priorityInputs[s.keyId]:s.priority,onChange:x=>this.handleInputChange(x,s.keyId,s.priority),onClick:x=>x.stopPropagation(),onWheel:x=>this.handleWheel(x,s.keyId,s.priority)}),a.jsx(bs,{className:"sr-action-button",title:"Increase Priority",onClick:x=>this.handleUp(x,s.keyId,s.priority),img:"./img/systemicons/AAclasses/iconPlus.png"}),a.jsx(bs,{className:"sr-action-button",title:"Move to Top",onClick:x=>this.handleTop(x,s.keyId),img:"./img/iconSRHigh.png"})]})})]},s.keyId)})]}),!n&&i>1&&a.jsx($v,{children:a.jsx(Cv,{onClick:s=>this.handlePropagate(s),children:"Set all Self Repair systems"})})]})}}const Dv=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
`,Pv=h.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,Av=h.div`
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    display: block;
    position: relative;
    scrollbar-gutter: stable;
    ${t=>t.$lockHeight?`height: ${t.$lockHeight}px; max-height: ${t.$lockHeight}px;`:""}

    &::-webkit-scrollbar { width: 6px; }
    &::-webkit-scrollbar-track { background: #0d1620; }
    &::-webkit-scrollbar-thumb { background: #2b3e51; }
    &::-webkit-scrollbar-thumb:hover { background: #5a7ea8; }
`,Kf=h.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 5px;
    margin-right: 3px;
    border-bottom: 1px solid #2b3e51;
    font-size: 12px;
    color: #e6e6e6;
    cursor: ${t=>t.$readOnly?"default":"grab"};
    touch-action: ${t=>t.$readOnly?"auto":"none"};
    user-select: none;
    position: relative;

    ${t=>t.$dragging&&`
        position: absolute;
        opacity: 0.95;
        z-index: 3;
        cursor: grabbing;
        background-color: rgba(43, 62, 81, 0.92);
        pointer-events: none;
        transition: none;
    `}

    ${t=>t.$lineBefore&&`
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

    ${t=>t.$gapBefore&&`
        margin-top: ${t.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0; right: 0;
            top: -${t.$gapSize}px;
            height: ${t.$gapSize}px;
            box-shadow: inset 0 0 0 2px #ffcc33, 0 0 6px 1px rgba(255, 204, 51, 0.5);
            background-color: rgba(255, 204, 51, 0.12);
            pointer-events: none;
        }
    `}

    ${t=>t.$lineAtEnd&&`
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
`,Rv=h(Kf)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,Iv=h.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,Qf=h.span`
    font-weight: bold;
`,Ov=h(Qf)`
    color: #ff3333;
    font-weight: normal;
`,Lv=h.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,zv=h.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,Bv=h.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,Nv=h.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,Fv=h.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;class _v extends R.Component{constructor(e){super(e),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getOrderedBlocks(){const{ship:e,system:n}=this.props,r=(n.structureBlocks||[]).map(l=>{const c=(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).find(g=>g.id===l.id),d=c?shipManager.systems.getRemainingHealth(c):l.maxhealth,u=c?c.maxhealth:l.maxhealth||0,f=c?shipManager.systems.isDestroyed(e,c):!1;return{id:l.id,displayName:l.displayName,hp:d,maxhealth:u,destroyed:f}}),i=n.repairOrder||[];if(i.length===0)return r.slice().sort((l,c)=>{const d=c.destroyed?1:0,u=l.destroyed?1:0;return d!==u?d-u:c.maxhealth-c.hp-(l.maxhealth-l.hp)});const o=[],s=new Set;for(const l of i){const c=r.find(d=>d.id===l);c&&(o.push(c),s.add(l))}for(const l of r)s.has(l.id)||o.push(l);return o}onRowPointerDown(e,n,r,i){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest(".ssr-action-button"))return;const o=e.currentTarget,s=o?o.offsetHeight:24,l=this.listRef?this.listRef.offsetHeight:0,c=o?o.offsetWidth:0,d=o?e.clientY-o.getBoundingClientRect().top:0;this.dragRef={keyId:n,pointerId:e.pointerId,startY:e.clientY,startIdx:r,order:i,gapSize:s,lockHeight:l,anchorWidth:c,grabOffsetInRow:d,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(e){const n=this.dragRef;if(!(!n||e.pointerId!==n.pointerId)){if(!n.started){if(Math.abs(e.clientY-n.startY)<4)return;n.started=!0}e.preventDefault(),n.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){const n=this.dragRef;if(!n||!this.listRef)return;const r=this.listRef.querySelectorAll("[data-keyid]"),i=this.listRef.getBoundingClientRect(),o=e-i.top+this.listRef.scrollTop,l=this.state.drag&&this.state.drag.dropIdx===0?n.gapSize:0;let c=0,d=null;for(let f=0;f<r.length;f++){if(r[f].getAttribute("data-keyid")===String(n.keyId)){d=r[f];continue}const g=r[f].offsetTop-l+r[f].offsetHeight/2;if(o<g)break;c++}d&&this.positionDraggedEl(d,n,e);const u=this.state.drag;(!u||u.keyId!==n.keyId||u.dropIdx!==c)&&this.setState({drag:{keyId:n.keyId,startIdx:n.startIdx,dropIdx:c,gapSize:n.gapSize,lockHeight:n.lockHeight}})}positionDraggedEl(e,n,r){const i=this.listRef.getBoundingClientRect();let o=r-i.top+this.listRef.scrollTop-n.grabOffsetInRow;const s=Math.max(0,this.listRef.scrollHeight-n.gapSize);o<0?o=0:o>s&&(o=s),e.style.top=o+"px",e.style.left="0px",e.style.width=n.anchorWidth+"px",e.style.transform="none"}updateAutoScroll(e){const n=this.listRef;if(!n){this.stopAutoScroll();return}const r=30,i=n.getBoundingClientRect(),o=n.scrollTop>0,s=n.scrollTop<n.scrollHeight-n.clientHeight-1,l=e-i.top,c=i.bottom-e;o&&l<r?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,l)/r),this.ensureAutoScrollRunning()):s&&c<r?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,c)/r),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const e=this.listRef,n=this.dragRef;if(!e||!n||this.autoScrollDir===0)return;const r=e.scrollTop;if(e.scrollTop=r+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===r){this.stopAutoScroll();return}this.updateDragForPointer(n.lastClientY),this.updateAutoScroll(n.lastClientY)}onDragEnd(e){const n=this.dragRef;if(!n||e&&e.pointerId!=null&&e.pointerId!==n.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const i=this.listRef.querySelector('[data-keyid="'+n.keyId+'"]');i&&(i.style.transform="",i.style.top="",i.style.left="",i.style.width="")}this.dragRef=null;const r=this.state.drag;this.setState({drag:null}),!(!n.started||!r)&&r.dropIdx!==n.startIdx&&this.applyDropReorder(n.order,n.keyId,r.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){const e=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');e&&this.positionDraggedEl(e,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(e,n,r){const{ship:i,system:o}=this.props,s=e.slice(),l=s.findIndex(u=>u.id===n);if(l===-1)return;const[c]=s.splice(l,1);s.splice(r,0,c);const d=s.map(u=>u.id);o.setRepairOrder(d),webglScene.customEvent("SystemDataChanged",{ship:i,system:o})}handleReset(e){e.stopPropagation();const{ship:n,system:r}=this.props;r.setRepairOrder([]),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,readOnly:n}=this.props,r=this.getOrderedBlocks(),i=(this.props.system.repairOrder||[]).length>0;return a.jsxs(Dv,{children:[a.jsx(Pv,{children:n?"Structure Repair Order (view only)":"Manage Structure Repair"}),!n&&a.jsx(Fv,{children:"Drag rows to set repair priority — top = first repaired"}),a.jsxs(Av,{ref:o=>{this.listRef=o},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[r.length===0&&a.jsx(Rv,{children:"No structure blocks found"}),r.map((o,s)=>{const l=this.state.drag,c=l&&l.keyId===o.id;let d=!1,u=!1,f=!1;if(l&&!c){const v=r.length-1,x=s>l.startIdx?s-1:s;l.dropIdx===0&&x===0?d=!0:l.dropIdx===v&&x===v-1?u=!0:l.dropIdx===x&&(f=!0)}const g=o.maxhealth-o.hp;return a.jsx(Kf,{"data-keyid":o.id,$dragging:c,$gapBefore:d,$lineAtEnd:u,$lineBefore:f,$gapSize:l?l.gapSize:0,$readOnly:n,onPointerDown:v=>this.onRowPointerDown(v,o.id,s,r),children:a.jsxs(Iv,{children:[o.destroyed?a.jsx(Ov,{children:o.displayName}):a.jsx(Qf,{children:o.displayName}),a.jsxs(Lv,{children:["HP: ",o.hp," / ",o.maxhealth,g>0&&a.jsxs(a.Fragment,{children:[a.jsx(zv,{}),"Dmg: ",g,o.destroyed?" — DESTROYED":""]})]})]})},o.id)})]}),!n&&a.jsx(Bv,{children:a.jsx(Nv,{className:"ssr-action-button",onClick:o=>this.handleReset(o),title:"Clear custom order and return to default (destroyed first, then most damaged)",children:i?"Reset to Default Order":"Using Default Order"})})]})}}const Hv=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,Wv=h.div`
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
`,Uv=h.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Xf=h.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,Gv=h.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,Vv=h.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,Yv=h.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,Kv=h.div`
    width: 20px;
    text-align: center;
`,Sc=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #2b3e51; color: #f2f2f2; }
    `}
`,Qv=h.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class Xv extends R.Component{constructor(e){super(e),this.listRef=oe.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrDmgType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrDmgType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating AA setting for:",e),r.setCurrDmgType(e);const l=r.allocatedAA[e];var c=[];for(var d in i.ships){var u=i.ships[d];if(u.userid==n.userid&&!o.isDestroyed(u))if(u.flight)for(var f=0;f<u.systems.length;f++){var g=u.systems[f];if(g)for(var v=0;v<g.systems.length;v++){var x=g.systems[v];if(x&&x.displayName=="Adaptive Armor Controller"){c.push(x);break}}}else for(var v=0;v<u.systems.length;v++){var x=u.systems[v];if(x.displayName=="Adaptive Armor Controller"){c.push(x);break}}}console.log("Found AA controllers:",c.length);for(var b=0;b<c.length;b++){var x=c[b];x.setCurrDmgType(e);let y=0;for(;x.getCurrAllocated()<l&&x.canIncrease()&&y<100;)x.doIncrease(),y++;y>=100&&console.warn("AA Propagation safety break for",x)}s.customEvent("SystemDataChanged",{ship:n,system:r})}getRelevantArmorTypes(){const{ship:e,system:n}=this.props;return n.getRelevantArmorTypes(e)}render(){const{ship:e,system:n}=this.props;if(!n||!n.availableAA)return null;const r=this.getRelevantArmorTypes(),i=n.allocatedAA,o=c=>{const d=n.AAtotal_used,u=n.AAtotal,f=i[c]||0,g=n.AApertype,v=n.availableAA[c],x=n.AApreallocated,b=n.AApreallocated_used;return!(d>=u||f>=g||x<=b&&v<=f)},s=c=>n.currchangedAA[c]>0,l=c=>i[c]>0;return a.jsxs(Hv,{children:[a.jsx(Wv,{children:"Manage Adaptive Armor"}),a.jsxs(Uv,{ref:this.listRef,children:[r.map(c=>a.jsxs(Xf,{children:[a.jsx(Gv,{src:`./img/systemicons/AAclasses/${c}.png`,alt:c}),a.jsx(Vv,{children:c}),a.jsxs(Yv,{children:[a.jsx(Sc,{onClick:()=>this.handleDecrease(c),disabled:!s(c),children:"-"}),a.jsx(Kv,{children:i[c]}),a.jsx(Sc,{onClick:()=>this.handleIncrease(c),disabled:!o(c),children:"+"}),a.jsx(Sc,{title:"Propagate to all units",onClick:()=>this.handlePropagate(c),disabled:!l(c),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/AAclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},c)),r.length===0&&a.jsx(Xf,{children:"No armor types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",n.AAtotal_used," / ",n.AAtotal," ",a.jsx(Qv,{})," Max Per Type: ",n.AApertype]})]})}}const Zv=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,qv=h.div`
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
`,Jv=h.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Zf=h.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,e1=h.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,t1=h.div`
    flex: 1;
    font-weight: normal; 
`,n1=h.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,r1=h.div`
    width: 20px;
    text-align: center;
`,kc=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`,i1=h.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class o1 extends R.Component{constructor(e){super(e),this.listRef=oe.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrFCType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrFCType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating BFCP setting for:",e),r.setCurrFCType(e);const l=r.allocatedBFCP[e];var c=[];for(var d in i.ships){var u=i.ships[d];if(u.userid==n.userid&&!o.isDestroyed(u))if(u.flight)for(var f=0;f<u.systems.length;f++){var g=u.systems[f];if(g)for(var v=0;v<g.systems.length;v++){var x=g.systems[v];if(x&&x.displayName=="Computer"){c.push(x);break}}}else for(var v=0;v<u.systems.length;v++){var x=u.systems[v];if(x.displayName=="Computer"){c.push(x);break}}}console.log("Found BFCP controllers:",c.length);for(var b=0;b<c.length;b++){var x=c[b];x.setCurrFCType(e);let y=0;for(;x.getCurrAllocated()<l&&x.canIncrease()&&y<100;)x.doIncrease(),y++;for(;x.getCurrAllocated()>l&&x.canDecrease()&&y<100;)x.doDecrease(),y++;y>=100&&console.warn("BFCP Propagation safety break for",x)}s.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{system:e}=this.props;if(!e||!e.allocatedBFCP)return null;const n=Object.keys(e.allocatedBFCP),r=e.allocatedBFCP,i=l=>{const c=e.BFCPtotal_used,d=e.output,u=r[l]||0,f=e.BFCPpertype;return!(c>=d||u>=f)},o=l=>r[l]>0,s=l=>r[l]>=0;return a.jsxs(Zv,{children:[a.jsx(qv,{children:"Hyach Computer"}),a.jsxs(Jv,{ref:this.listRef,children:[n.map(l=>a.jsxs(Zf,{children:[a.jsx(e1,{src:`./img/systemicons/BFCPclasses/${l}.png`,alt:l}),a.jsx(t1,{children:l}),a.jsxs(n1,{children:[a.jsx(kc,{onClick:()=>this.handleDecrease(l),disabled:!o(l),children:"-"}),a.jsx(r1,{children:r[l]}),a.jsx(kc,{onClick:()=>this.handleIncrease(l),disabled:!i(l),children:"+"}),a.jsx(kc,{title:"Propagate to all units",onClick:()=>this.handlePropagate(l),disabled:!s(l),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},l)),n.length===0&&a.jsx(Zf,{children:"No FC types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",e.BFCPtotal_used," / ",e.output," ",a.jsx(i1,{})," Max Per Type: ",e.BFCPpertype]})]})}}const s1=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,a1=h.div`
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
`,l1=h.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,qf=h.div`
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
`,c1=h.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,d1=h.span`
    flex-grow: 1;
    font-weight: bold;
`,u1=h.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,Ss=h.div`
    width: 16px; 
    height: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: ${t=>t.disabled?"#555":"#7c4686"};
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: ${t=>t.disabled?"default":"pointer"};
    border: 1px solid #aaa;
    opacity: ${t=>t.disabled?.5:1};
    
    &:hover {
         background-color: ${t=>t.disabled?"#555":"#a06daa"};
    }
    ${t=>!t.disabled&&We}
`,h1=t=>window.gamedata.turn===window.shipManager.getTurnPlaced(t)&&window.gamedata.gamephase===-1;class p1 extends R.Component{constructor(e){super(e)}handleSelect(e){const{system:n}=this.props;n.specCurrClass=e,n.canSelect()&&(n.doSelect(),this.forceUpdate())}handleUnselect(e){const{system:n}=this.props;n.specCurrClass=e,n.canUnselect()&&(n.doUnselect(),this.forceUpdate())}handleUse(e){const{system:n}=this.props;n.specCurrClass=e,n.canUse()&&(n.doUse(),this.forceUpdate())}handleCancel(e){const{system:n}=this.props;n.specCurrClass=e,n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}render(){const{ship:e,system:n}=this.props;if(!n)return null;const r=h1(e);let i=[];r?n.allSpec&&(i=Object.keys(n.allSpec)):n.availableSpec&&(i=Object.keys(n.availableSpec).filter(s=>n.availableSpec[s]>0)),i.sort();let o="";return r?o=`Specialists Selected: ${Object.values(n.availableSpec||{}).reduce((l,c)=>l+c,0)} / ${n.specTotal}`:o=`Specialists Used: ${n.specTotal_used||0} / ${n.specTotal}`,a.jsxs(s1,{children:[a.jsx(a1,{children:"Hyach Specialists"}),a.jsxs(l1,{children:[i.map(s=>{const l=r&&(n.specCurrClass=s,n.canSelect()),c=r&&(n.specCurrClass=s,n.canUnselect()),d=!r&&(n.specCurrClass=s,n.canUse()),u=!r&&(n.specCurrClass=s,n.canDecrease());n.availableSpec&&n.availableSpec[s]>0,n.currAllocatedSpec&&n.currAllocatedSpec[s];const f=`./img/systemicons/Specialistclasses/${s}.png?v=2`;return a.jsxs(qf,{children:[a.jsx(c1,{src:f,alt:s}),a.jsx(d1,{children:s}),a.jsx(u1,{children:r?a.jsxs(a.Fragment,{children:[a.jsx(Ss,{onClick:()=>this.handleUnselect(s),disabled:!c,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})}),a.jsx(Ss,{onClick:()=>this.handleSelect(s),disabled:!l,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})})]}):a.jsxs(a.Fragment,{children:[a.jsx(Ss,{onClick:()=>this.handleUse(s),disabled:!d,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})}),a.jsx(Ss,{onClick:()=>this.handleCancel(s),disabled:!u,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})})]})})]},s)}),i.length===0&&a.jsx(qf,{style:{justifyContent:"center",fontStyle:"italic"},children:"No Specialists Available"})]}),a.jsx("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:o})]})}}const f1=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${m.colors.line};
`,g1=h.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${m.colors.line};
    border-bottom: 1px solid ${m.colors.line};
    color: ${m.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,m1=h.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Jf=h.div`
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
`,x1=h.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,y1=h.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,w1=h.input`
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
`,qe=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    &.small {
        width: 18px;
    }
`;h.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 6px;
    vertical-align: middle;
    opacity: 0.7;
`;const v1=h.div`
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
`;class eg extends R.Component{constructor(e){super(e),this.listRef=oe.createRef(),this.state={shieldInputs:{}}}getShieldLabel(e){let n=e.startArc,r=e.endArc;if(n===void 0||r===void 0)return e.displayName;let i=(n+r)/2;n>r&&(i=(n+r+360)/2),i=i%360;let o="";return i>=337.5||i<22.5?o="Front":i>=22.5&&i<67.5?o="Front Starboard":i>=67.5&&i<112.5?o="Starboard":i>=112.5&&i<157.5?o="Aft Starboard":i>=157.5&&i<202.5?o="Aft":i>=202.5&&i<247.5?o="Aft Port":i>=247.5&&i<292.5?o="Port":i>=292.5&&i<337.5&&(o="Front Port"),o?`${o} - ${e.displayName}`:e.displayName}getShieldSortPriority(e){const n=this.getShieldLabel(e).split(" - ")[0];return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[n]||99}getGeneratorAndShields(){const{ship:e,system:n}=this.props;let r=null,i=[],o="",s="";return n.name==="ThirdspaceShield"||n.name==="ThirdspaceShieldGenerator"?(o="ThirdspaceShield",s="ThirdspaceShieldGenerator"):(n.name==="ThoughtShield"||n.name==="ThoughtShieldGenerator")&&(o="ThoughtShield",s="ThoughtShieldGenerator"),o?(e.systems&&(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).forEach(c=>{c.name===s&&(r=c),c.name===o&&i.push(c)}),i.sort((l,c)=>{const d=this.getShieldSortPriority(l),u=this.getShieldSortPriority(c);return d!==u?d-u:l.id-c.id}),{generator:r,shields:i,systemName:o}):{generator:null,shields:[]}}handleIncrease(e,n){e.canIncrease()&&(e.doIncrease(n),this.afterShieldChange(e))}handleDecrease(e,n){e.canDecrease()&&(e.doDecrease(n),this.afterShieldChange(e))}handleMin(e){e.canDecrease()&&(e.doMin(),this.afterShieldChange(e))}handleMax(e){e.canIncrease()&&(e.doMax(),this.afterShieldChange(e))}afterShieldChange(e){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}),this.updateInputState(e)}handleInputChange(e,n){if(n===""){this.setState(s=>({shieldInputs:{...s.shieldInputs,[e.id]:""}}));return}const r=parseInt(n,10);if(isNaN(r))return;const i=e.currentHealth,o=r-i;o>0?this.handleIncrease(e,o):o<0&&this.handleDecrease(e,Math.abs(o))}updateInputState(e){this.setState(n=>({shieldInputs:{...n.shieldInputs,[e.id]:e.currentHealth}}))}handleWheel(e,n){e.preventDefault(),(e.deltaY<0?1:-1)>0?this.handleIncrease(n,1):this.handleDecrease(n,1)}handleMouseEnter(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const n=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);n&&(n.showWeaponArc(this.props.ship,e),window.webglScene.requestRender())}}handleMouseLeave(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const n=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);n&&(n.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(e){e&&(shipManager.power.clickPlus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}handleDeBoost(e){e&&(shipManager.power.clickMinus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}handleEqualise(e){e&&(e.doEqualise(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}componentDidMount(){const{generator:e,shields:n}=this.getGeneratorAndShields(),r={};n.forEach(i=>r[i.id]=i.currentHealth),this.setState({shieldInputs:r})}componentDidUpdate(e){const{generator:n,shields:r}=this.getGeneratorAndShields(),i=this.state.shieldInputs,o={};let s=!1;r.forEach(l=>{i[l.id]!==l.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${l.id}`)&&(o[l.id]=l.currentHealth,s=!0)}),s&&this.setState(l=>({shieldInputs:{...l.shieldInputs,...o}}))}render(){const{generator:e,shields:n,systemName:r}=this.getGeneratorAndShields();return e?a.jsxs(f1,{children:[a.jsx(g1,{children:r==="ThirdspaceShield"?"Thirdspace Shields":"Thought Shields"}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791"},children:["Unallocated Shield Energy: ",e.storedCapacity]}),r==="ThirdspaceShield"&&a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791",display:"flex",justifyContent:"center",alignItems:"center",gap:"5px"},children:["Regeneration Rate: ",shipManager.systems.getOutputNoBoost(this.props.ship,e)+shipManager.power.getBoost(e)*n.length,a.jsx(qe,{className:"small",onClick:()=>this.handleDeBoost(e),title:"Reduce Boost",children:"-"}),a.jsx(qe,{className:"small",onClick:()=>this.handleBoost(e),title:"Boost Generator",children:"+"})]}),a.jsxs(m1,{ref:this.listRef,children:[n.map(i=>a.jsxs(Jf,{onMouseEnter:()=>this.handleMouseEnter(i),onMouseLeave:()=>this.handleMouseLeave(i),children:[a.jsx(x1,{children:this.getShieldLabel(i)}),a.jsxs(y1,{children:[a.jsx(qe,{onClick:()=>this.handleMin(i),disabled:!i.canDecrease(),title:"Drop shield to 0",children:"Min"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,25),disabled:!i.canDecrease(),children:"-25"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,10),disabled:!i.canDecrease(),children:"-10"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,5),disabled:!i.canDecrease(),children:"-5"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,1),disabled:!i.canDecrease(),children:"-1"}),a.jsx(w1,{id:`shield-input-${i.id}`,type:"number",value:this.state.shieldInputs[i.id]!==void 0?this.state.shieldInputs[i.id]:i.currentHealth,onChange:o=>this.handleInputChange(i,o.target.value),onWheel:o=>this.handleWheel(o,i)}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,1),disabled:!i.canIncrease(),children:"+1"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,5),disabled:!i.canIncrease(),children:"+5"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,10),disabled:!i.canIncrease(),children:"+10"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,25),disabled:!i.canIncrease(),children:"+25"}),a.jsx(qe,{onClick:()=>this.handleMax(i),disabled:!i.canIncrease(),title:"Raise shield to maximum",children:"Max"})]})]},i.id)),n.length===0&&a.jsx(Jf,{children:"No Shields Found"})]}),e&&a.jsx(v1,{onClick:()=>this.handleEqualise(e),children:"Equalise Shields"})]}):a.jsx("div",{children:"No Generator Found"})}}const tg=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${m.colors.line};
`,ng=h.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${m.colors.line};
    border-bottom: 1px solid ${m.colors.line};
    color: ${m.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,$c=h.div`
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
`,Mi=h.div`
    flex: 1;
`,ji=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
`,b1=h.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${m.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`,kr=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${t=>t.$active&&t.$variant!=="activate"&&`
        background: #806c00;
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;
    `}

    ${t=>t.$active&&t.$variant==="activate"&&`
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
`;class S1 extends R.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}canBoost(){const{ship:e,system:n}=this.props;return n.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,n)}canDeBoost(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(n)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){const{ship:e,system:n}=this.props,r=shipManager.power.getBoost(n),i=n.active;return a.jsxs(tg,{children:[a.jsx(ng,{children:"Power Capacitor"}),n.boostable&&a.jsxs($c,{children:[a.jsx(Mi,{children:"Open Petals"}),a.jsxs(ji,{children:[a.jsx(kr,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:r===0,children:"OFF"}),a.jsx(kr,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:r>0,$variant:"activate",children:"ON"})]})]}),a.jsxs($c,{children:[a.jsx(Mi,{children:"Double Recharge"}),a.jsxs(ji,{children:[a.jsx(kr,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!i,children:"OFF"}),a.jsx(kr,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:i,$variant:"activate",children:"ON"})]})]})]})}}const rg=h(tg)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`,be={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",hover:"rgba(75, 43, 81, 0.6)",text:"#f2f2f2",textDim:"#d8b9e6"},k1=h(rg)`
    background-color: ${be.surface};
    border: 1px solid ${be.line};
`,$1=h.div`
    padding: 3px;
    background-color: ${be.line};
    border: 1px solid ${be.accent};
    color: ${be.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,ig=h.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 8px;
    border-bottom: 1px solid ${be.line};
    font-size: 11px;
    color: ${be.text};

    &:hover {
        background-color: ${be.hover};
    }
`,C1=h.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${be.textDim};
`,ks=h.div`
    min-width: 24px;
    width: ${t=>t.$wide?"auto":"24px"};
    padding: ${t=>t.$wide?"0 6px":"0"};
    height: 18px;
    box-sizing: border-box;
    background: ${be.line};
    border: 1px solid ${be.accent};
    color: ${be.text};
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

    ${t=>t.$active&&`
        background: ${be.accent};
        border: 1px solid #a06daa;
        color: #ffffff;
        opacity: 1;
        cursor: default;
        &:hover { background: ${be.accent}; border: 1px solid #a06daa; }
    `}

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: ${be.textDim}; border: 1px solid ${be.accent}; }
    `}
`;class T1 extends R.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}handlePower(e){const{ship:n,system:r}=this.props;if(!og(n,r))return;const i=r.getAbductionOrder();r.setAbductionPowerLevel(r.getAbductionPowerLevel(i)+e)&&(webglScene.customEvent("SystemDataChanged",{ship:r.getOwningUnit()||n,system:r}),this.forceUpdate())}handleCancel(){const{ship:e,system:n}=this.props;gamedata.gamephase!==1||!gamedata.isMyShip(n.getOwningUnit()||e)||(weaponManager.removeFiringOrder(n.getOwningUnit()||e,n),this.forceUpdate())}renderMaintain(){const{system:e}=this.props,n=e.isMaintainingVortex(),r=e.chargesVortexUpkeep()?e.getVortexUpkeepCost():0;return a.jsxs(oe.Fragment,{children:[a.jsx(ng,{children:"Jump Point"}),a.jsxs($c,{children:[a.jsx(Mi,{children:"Maintain Vortex"}),a.jsxs(ji,{children:[a.jsx(kr,{onClick:()=>this.handleDeactivate(),disabled:!e.canDeactivate(),$active:!n,children:"OFF"}),a.jsx(kr,{onClick:()=>this.handleActivate(),disabled:!e.canActivate(),$active:n,$variant:"activate",children:"ON"})]})]}),a.jsx(b1,{children:r>0?n?"Held open. "+r+" power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.":"Closes at end of turn unless maintained. Costs "+r+" power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.":n?"Held open. All powered systems except the Scanner are shut down this turn.":"Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner."})]})}renderAbduction(){const{ship:e,system:n}=this.props,r=n.getAbductionOrder(),i=gamedata.getShip(r.targetid),o=n.getAbductionPowerLevel(r),s=og(e,n),l=n.isExtraDimensional(),c=n.abductionMaxPower||1,d=window.JumpEngine.formatAbductionHalves(n.getAbductionHalves(r)),u=window.JumpEngine.getAbductionChain(r.targetid),f=window.JumpEngine.getAbductionCostPreview(r.targetid),g=n.isAbductionPowered(r);let v;if(g)v=d+" power-turn"+(d==="1"?"":"s")+" for "+n.getAbductionPowerDraw()+" power. So far "+window.JumpEngine.formatAbductionHalves(u.total)+" of "+u.cost+" power-turns.";else{const x=!!i&&gamedata.isTerrain(i.shipSizeClass,i.userid),b=!!i&&((i.hexOffsets||[]).length>0||i.Huge>0);v=(x?"Targeting: takes hold if "+(b?"every hex it occupies is":"it is")+" inside your connected field and your OEW beats its DEW.":"Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.")+" Power can be applied from next turn"+(f!==null?", and "+f+" power-turns will abduct it.":".")}return a.jsxs(k1,{children:[a.jsxs($1,{children:["Abduction",i?": "+i.name:""]}),g&&a.jsxs(ig,{children:[a.jsx(Mi,{children:l?"Power":"Double power"}),l?a.jsxs(ji,{children:[a.jsx(ks,{onClick:()=>this.handlePower(-1),disabled:!s||o<=1,children:"-"}),a.jsx(ks,{$active:!0,children:"x"+o}),a.jsx(ks,{onClick:()=>this.handlePower(1),disabled:!s||o>=c,children:"+"})]}):null]}),gamedata.gamephase===1&&gamedata.isMyShip(n.getOwningUnit()||e)&&a.jsxs(ig,{children:[a.jsx(Mi,{children:"Declaration"}),a.jsx(ji,{children:a.jsx(ks,{$wide:!0,onClick:()=>this.handleCancel(),children:"CANCEL"})})]}),a.jsx(C1,{children:v})]})}render(){const{system:e}=this.props,n=e.canMaintainVortex()||e.canDeactivate(),r=typeof e.getAbductionOrder=="function"&&!!e.getAbductionOrder();return a.jsxs(oe.Fragment,{children:[n&&a.jsx(rg,{children:this.renderMaintain()}),r&&this.renderAbduction()]})}}const og=(t,e)=>gamedata.gamephase===1&&gamedata.isMyShip(e.getOwningUnit()||t)&&e.isExtraDimensional()&&e.isAbductionPowered(),Je={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",text:"#f2f2f2"},M1=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${t=>t.$isWeapon||t.$isPurple?Je.surface:"rgba(16, 26, 38, 0.9)"};
    border: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Je.line:m.colors.line};
`,j1=h.div`
    padding: 3px;
    background-color: ${t=>t.$isWeapon?"#571616":t.$isPurple?Je.line:"#215a7a"};
    border: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Je.line:m.colors.line};
    border-bottom: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Je.line:m.colors.line};
    color: ${t=>t.$isWeapon||t.$isPurple?Je.text:m.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,E1=h.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid ${t=>t.$isPurple?Je.line:"#496791"};
    font-size: 12px;
    color: ${t=>t.$isPurple?Je.text:"#deebff"};
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;h.div`
    flex: 1;
    margin-right: 10px;    
`;const D1=h.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,sg=h.div`
    flex: 1;
    height: 18px;
    background: ${t=>t.$isPurple?Je.line:"#203348"};
    border: 1px solid ${t=>t.$isPurple?Je.accent:"#496791"};
    color: ${t=>t.$isPurple?Je.text:"#deebff"};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${t=>t.$isPurple?"#5e3666":"#496791"};
        border: 1px solid ${t=>t.$isPurple?"#9a5aa6":"#5d82b6ff"};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&(t.$isPurple?`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; border: 1px solid ${Je.accent}; }
    `:`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `)}

    ${t=>(t.$active||t.$variant==="activate"&&t.$isWeapon)&&t.$variant==="activate"&&!t.$isWeapon&&`
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
    ${t=>t.$variant==="activate"&&t.$isWeapon&&!(t.$isToggle&&!t.$active)&&`
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

        ${t.$active?`
            background: #b35900;
            border: 1px solid #ffb74d;
            box-shadow: 0 0 5px #ff9800;
        `:""}
    `}

    ${t=>t.$active&&t.$variant==="deactivate"&&`
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
`;class P1 extends R.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivateAll(e){e.preventDefault();const{ship:n,system:r}=this.props;let i=[];n.flight?i=n.systems.map(s=>s.systems).reduce((s,l)=>s.concat(l),[]):i=n.systems;let o=!1;i.forEach(s=>{!s.name||s.name!==r.name||s.canActivate&&typeof s.canActivate=="function"&&s.canActivate()&&(s.doActivate(),o=!0)}),o&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(e){e.preventDefault();const{ship:n,system:r}=this.props;let i=[];n.flight?i=n.systems.map(s=>s.systems).reduce((s,l)=>s.concat(l),[]):i=n.systems;let o=!1;i.forEach(s=>{!s.name||s.name!==r.name||s.canDeactivate&&typeof s.canDeactivate=="function"&&s.canDeactivate()&&(s.doDeactivate(),o=!0)}),o&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate=="function"&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate=="function"&&this.props.system.canDeactivate()}render(){const{ship:e,system:n}=this.props,r=n.active||n.weapon&&!n.activationIsToggle&&weaponManager.hasFiringOrder(e,n),i=typeof n.getActivateLabel=="function"?n.getActivateLabel():null,o=typeof n.getDeactivateLabel=="function"?n.getDeactivateLabel():null,s=i||(n.weapon?"Fire":"Activate"),l=o||(n.weapon?"Don't Fire":"Deactivate"),c=!!n.singleActivationButton,d=!c||this.canActivate(),u=(!n.weapon||n.activationIsToggle)&&(!c||this.canDeactivate()),f=!!n.activationMenuPurple&&!n.weapon;return a.jsxs(M1,{$isWeapon:n.weapon,$isPurple:f,children:[a.jsx(j1,{$isWeapon:n.weapon,$isPurple:f,children:n.displayName}),a.jsx(E1,{$isPurple:f,children:a.jsxs(D1,{children:[d&&a.jsx(sg,{onClick:()=>this.handleActivate(),onContextMenu:g=>this.handleActivateAll(g),disabled:!this.canActivate(),$active:r,$variant:"activate",$isWeapon:n.weapon,$isToggle:!!n.activationIsToggle,$isPurple:f,children:s}),u&&a.jsx(sg,{onClick:()=>this.handleDeactivate(),onContextMenu:g=>this.handleDeactivateAll(g),disabled:!this.canDeactivate(),$active:!r,$variant:"deactivate",$isPurple:f,children:l})]})})]})}}const A1=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,R1=h.div`
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
`,Cc=h.div`
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
`,Tc=h.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,$s=h.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,I1=h.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`,an=h.div`
    width: ${t=>t.$narrow?"18px":"30px"};
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #3d2e10; color: #fff8d6; }
    `}

    ${t=>t.$active&&t.$variant==="activate"&&`
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

    ${t=>t.$active&&t.$variant==="deactivate"&&`
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

    ${t=>t.$active&&t.$variant==="warning"&&`
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

    ${t=>t.$active&&t.$variant==="risk"&&`
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

    ${t=>t.$active&&t.$variant==="info"&&`
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
`;class O1 extends R.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(e){e.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){const{ship:e,system:n}=this.props;for(;shipManager.power.getBoost(n)>0;)shipManager.power.clickMinus(e,n);shipManager.power.onOfflineClicked(e,n),this.handleUpdate()}}handleOfflineAll(e){if(e.preventDefault(),this.canOffline()){const{ship:n,system:r}=this.props;for(;shipManager.power.getBoost(r)>0;)shipManager.power.clickMinus(n,r);shipManager.power.offlineAll(n,r),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system})}canOffline(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&(n.canOffLine||n.powerReq>0)&&!n.powerLocked&&!shipManager.power.isOffline(e,n)&&!weaponManager.hasFiringOrder(e,n)}canOnline(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(e,n)&&!shipManager.power.isForcedOffline(e,n)&&!shipManager.power.isVortexLockedOffline(e,n)}canBoost(){const{ship:e,system:n}=this.props;return n.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,n)&&(!n.isScanner()||n.id==shipManager.power.getHighestSensorsId(e))&&n.name!=="ThirdspaceShieldGenerator"&&n.name!=="powerCapacitor"&&n.name!=="PowerCapacitor"}canDeBoost(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(n)&&n.name!=="ThirdspaceShieldGenerator"&&n.name!=="powerCapacitor"&&n.name!=="PowerCapacitor"}canOverload(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(e,n)&&n.weapon&&n.overloadable&&!shipManager.power.isOverloading(e,n)}canStopOverload(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&n.weapon&&n.overloadable&&shipManager.power.isOverloading(e,n)&&(n.overloadshots>=n.extraoverloadshots||n.overloadshots==0)}render(){const{ship:e,system:n}=this.props,r=this.canOffline()||this.canOnline(),i=n.boostable&&(this.canBoost()||this.canDeBoost()),o=n.overloadable&&(this.canOverload()||this.canStopOverload());if(!r&&!i&&!o)return null;const s=shipManager.power.isOffline(e,n),l=shipManager.power.getBoost(n),c=shipManager.power.isOverloading(e,n),d=n.name==="reactor",u=n.name==="jumpEngine",f=u&&typeof n.getChargeBoostMax=="function"&&n.getChargeBoostMax()>0;let g="Boost Level";return d&&(g="Self-Destruct"),u&&(g=f?"Extra Charging":"Jump to Hyperspace"),a.jsxs(A1,{children:[a.jsx(R1,{children:"Power Settings"}),r&&a.jsxs(Cc,{children:[a.jsx(Tc,{children:"Power"}),a.jsxs($s,{children:[a.jsx(an,{onClick:()=>this.handleOnline(),onContextMenu:v=>this.handleOnlineAll(v),disabled:!this.canOnline(),$active:!s,$variant:"activate",children:"On"}),a.jsx(an,{onClick:()=>this.handleOffline(),onContextMenu:v=>this.handleOfflineAll(v),disabled:!this.canOffline(),$active:s,$variant:"deactivate",children:"Off"})]})]}),i&&a.jsxs(Cc,{children:[a.jsx(Tc,{children:g}),d||u&&!f?a.jsxs($s,{children:[a.jsx(an,{onClick:()=>this.handleBoost(),disabled:l>0||!this.canBoost(),$active:l>0,$variant:d?"deactivate":"risk",children:"Yes"}),a.jsx(an,{onClick:()=>this.handleDeBoost(),disabled:l===0||!this.canDeBoost(),$active:l===0,$variant:"activate",children:"No"})]}):a.jsxs($s,{children:[a.jsx(an,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:"-"}),a.jsx(I1,{children:l}),a.jsx(an,{onClick:()=>this.handleBoost(),$narrow:!0,children:"+"})]})]}),o&&a.jsxs(Cc,{children:[a.jsx(Tc,{children:"Overcharge"}),a.jsxs($s,{children:[a.jsx(an,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:c,$variant:"warning",children:"Yes"}),a.jsx(an,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!c,$variant:"deactivate",children:"No"})]})]})]})}}const L1=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,z1=h.div`
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
`,B1=h.div`
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
`,ag=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; }
    `}
`,N1=h.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,F1=h.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,lg=h.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,_1=h.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,H1=h.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,W1=h.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,U1=h.div`
    width: 20px;
    text-align: center;
`,Mc=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`;h.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class G1 extends R.Component{constructor(e){super(e),this.listRef=oe.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrShipType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrShipType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene,l=!!(r.hasMultiTarget&&r.hasMultiTarget()),c=l?r.getCurrWeaponId():null,d=l?r.getMineWeapons():[],u=l?d.find(j=>String(j.id)===String(c)):null;if(l&&!u)return;r.setCurrShipType(e);const f=r.rangeSetting||r.range,g=l?r.allocatedRanges[c]:r.allocatedRanges;if(!g)return;const v=g[e]===null||g[e]===void 0?f:g[e];var x=[];for(var b in i.ships){var M=i.ships[b];if(M.userid==n.userid&&!o.isDestroyed(M)){if(M.phpclass&&n.phpclass){if(M.phpclass!=n.phpclass)continue}else if(M.shipClass!=n.shipClass)continue;for(var y in M.systems){var p=M.systems[y];if(p&&p.name===r.name){var w=!!(p.hasMultiTarget&&p.hasMultiTarget());w===l&&x.push({unit:M,ctrl:p})}}}}for(var S=0;S<x.length;S++){var C=x[S],p=C.ctrl;if(l){p.ensureMultiAllocatedShape&&p.ensureMultiAllocatedShape();var $=p.getMineWeapons(),k=$.find(me=>me.displayName===u.displayName&&me.indexInGroup===u.indexInGroup);if(!k)continue;p.setCurrWeaponId(k.id)}p.setCurrShipType(e);let L=0;const P=()=>l?p.allocatedRanges[p.getCurrWeaponId()]:p.allocatedRanges,V=()=>p.range||p.rangeSetting,de=()=>{const me=P();if(!me)return V();const ct=me[e];return ct??V()};for(;de()<v&&p.canIncrease()&&L<100;)p.doIncrease(),L++;for(;de()>v&&p.canDecrease()&&L<100;)p.doDecrease(),L++;L>=100&&console.warn("Mine Settings Propagation safety break for",p)}s.customEvent("SystemDataChanged",{ship:n,system:r})}cycleWeapon(e){const{system:n}=this.props;if(!n.hasMultiTarget||!n.hasMultiTarget())return;const r=n.getMineWeapons();if(r.length===0)return;const i=n.getCurrWeaponId();let o=r.findIndex(l=>String(l.id)===String(i));o<0&&(o=0);const s=(o+e+r.length)%r.length;n.setCurrWeaponId(r[s].id),this.forceUpdate()}render(){const{system:e}=this.props;if(!e||(e.range=e.range||e.rangeSetting,!e.range))return null;const n=!!(e.hasMultiTarget&&e.hasMultiTarget());let r=[],i=null,o=null,s=e.allocatedRanges||{};n?(e.ensureMultiAllocatedShape&&e.ensureMultiAllocatedShape(),r=e.getMineWeapons(),i=e.getCurrWeaponId(),o=r.find(x=>String(x.id)===String(i))||r[0]||null,s=i!=null&&e.allocatedRanges[i]?e.allocatedRanges[i]:{}):(e.ensureFlatAllocatedShape&&e.ensureFlatAllocatedShape(),s=e.allocatedRanges||{});const l=Object.keys(s),c=e.validTargets||l,d=x=>{if(!c.includes(x))return"N/A";const b=s[x];return b??e.range},u=x=>c.includes(x)?d(x)<e.range:!1,f=x=>c.includes(x)?d(x)>0:!1,g=x=>!!c.includes(x),v=n&&r.length>0?a.jsxs(B1,{children:[a.jsx(ag,{onClick:()=>this.cycleWeapon(-1),disabled:r.length<2,title:"Previous weapon",children:"<"}),a.jsx(N1,{children:o?o.label:""}),a.jsx(ag,{onClick:()=>this.cycleWeapon(1),disabled:r.length<2,title:"Next weapon",children:">"})]}):a.jsx(z1,{children:"Set Mine Range"});return a.jsxs(L1,{children:[v,a.jsxs(F1,{ref:this.listRef,children:[l.map(x=>a.jsxs(lg,{children:[a.jsx(_1,{src:`./img/systemicons/BFCPclasses/${x}.png`,alt:x}),a.jsx(H1,{children:x}),a.jsxs(W1,{onWheel:b=>{b.deltaY<0&&u(x)?this.handleIncrease(x):b.deltaY>0&&f(x)&&this.handleDecrease(x)},children:[a.jsx(Mc,{onClick:()=>this.handleDecrease(x),disabled:!f(x),children:"-"}),a.jsx(U1,{children:d(x)}),a.jsx(Mc,{onClick:()=>this.handleIncrease(x),disabled:!u(x),children:"+"}),a.jsx(Mc,{title:n?"Propagate this weapon's settings to same-class mines with Multiple Targets":"Propagate to all mines of same type",onClick:()=>this.handlePropagate(x),disabled:!g(x),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},x)),l.length===0&&a.jsx(lg,{children:"No ship types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Max Range: ",e.range]})]})}}const V1=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Y1=h.div`
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
`,K1=h.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,cg=h.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,Q1=h.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,X1=h.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,Z1=h.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,q1=h.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${t=>t.$active?"#4CAF50":"#F44336"};
`,dg=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`;class J1 extends R.Component{constructor(e){super(e),this.listRef=oe.createRef()}handleToggle(e){const{system:n}=this.props;n.setCurrShipType(e),n.canSet()?(n.doSet(),this.forceUpdate()):n.canUnset()&&(n.doUnset(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating Mine settings for:",e),r.setCurrShipType(e);const l=r.allocatedShipTypes[e];var c=[];for(var d in i.ships){var u=i.ships[d];if(u.userid==n.userid&&!o.isDestroyed(u))for(var f=0;f<u.systems.length;f++){var g=u.systems[f];if(u.shipClass==n.shipClass&&g.name===r.name){c.push(g);break}}}console.log("Found Mine Weapons of same type:",c.length);for(var v=0;v<c.length;v++){var g=c[v];g.setCurrShipType(e);let b=0;for(;g.allocatedShipTypes[e]!==l&&(l?g.canSet():g.canUnset())&&b<10;)l?g.doSet():g.doUnset(),b++;b>=10&&console.warn("Mine Settings Propagation safety break for",g)}s.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{system:e}=this.props;if(!e)return null;const n=e.allocatedShipTypes||{},r=Object.keys(n),i=s=>n[s]?"YES":"NO",o=s=>{const l=Number(this.props.ship.spawned),c=l===-1?1:l+1;return window.gamedata.turn===c};return a.jsxs(V1,{children:[a.jsx(Y1,{children:"Set Target Types"}),a.jsxs(K1,{ref:this.listRef,children:[r.map(s=>a.jsxs(cg,{children:[a.jsx(Q1,{src:`./img/systemicons/BFCPclasses/${s}.png`,alt:s}),a.jsx(X1,{children:s}),a.jsxs(Z1,{children:[a.jsx(q1,{$active:n[s],children:i(s)}),a.jsx(dg,{onClick:()=>this.handleToggle(s),disabled:!o(),style:{marginLeft:"5px"},children:"Toggle"}),a.jsx(dg,{title:"Propagate to all mines of same type",onClick:()=>this.handlePropagate(s),disabled:!1,style:{marginLeft:"5px",width:"16px",padding:"0"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},s)),r.length===0&&a.jsx(cg,{children:"No ship types available"})]})]})}}const jc=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: ${m.colors.greenBg};
    border: 1px solid ${m.colors.greenLine};
`,ug=h.div`
    padding: 3px;
    background-color: ${m.colors.greenTitleBg};
    border: 1px solid ${m.colors.greenLine};
    color: ${m.colors.greenText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,eb=h.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: ${m.colors.greenTitleBg};
    border: 1px solid ${m.colors.greenLine};
    color: ${m.colors.greenText};
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 2px;
    min-width: 220px;
`,hg=h.div`
    width: 20px;
    height: 18px;
    background: ${m.colors.greenBtnBg};
    border: 1px solid ${m.colors.greenBtnLine};
    color: ${m.colors.greenText};
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: ${m.colors.greenBtnLine};
        border: 1px solid ${m.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${m.colors.greenBtnBg}; border: 1px solid ${m.colors.greenBtnLine}; color: ${m.colors.greenText}; }
    `}
`,tb=h.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;h.div`
    text-align: center;
    color: ${m.colors.greenLabel};
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;const Ei=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,pg=h.div`
    width: 70px;
    font-size: 10px;
    color: ${m.colors.greenLabel};
    user-select: none;
`,jn=h.div`
    flex: 1;
    height: 20px;
    background: ${m.colors.greenBtnBg};
    border: 1px solid ${m.colors.greenBtnLine};
    color: ${m.colors.greenText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.95;
    user-select: none;

    &:hover {
        background: ${m.colors.greenBtnLine};
        border: 1px solid ${m.colors.greenBtnLineHover};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.$active&&`
        background: ${m.colors.greenBtnLine};
        border: 1px solid ${m.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${m.colors.greenGlow};
        color: #ffffff;
        opacity: 1;
    `}

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${m.colors.greenBtnBg}; border: 1px solid ${m.colors.greenBtnLine}; color: ${m.colors.greenText}; }
    `}
`;class nb extends R.Component{refresh(){const{ship:e,system:n}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n})}cycleMode(e){const{system:n}=this.props;if(!gamedata.isMyShip(this.props.ship))return;const r=n.firingMode==1?2:1;n.setFiringMode(r),typeof n.initializationUpdate=="function"&&n.initializationUpdate(),this.refresh()}activateMode1(){const{ship:e,system:n}=this.props;n.canActivate()&&(n.doActivate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n}),webglScene.customEvent("CloseSystemInfo"))}targetWarrior(){const{ship:e,system:n}=this.props;weaponManager.isSelectedWeapon(n)||weaponManager.selectWeapon(e,n),webglScene.customEvent("SystemDataChanged",{ship:e,system:n}),webglScene.customEvent("CloseSystemInfo")}setRotation(e,n){const{system:r}=this.props;r.rotationDirection=e,r.rotationAmount=n,typeof r.updateRotationNotes=="function"&&r.updateRotationNotes(),this.refresh()}renderInitialOrders(){const{system:e}=this.props,n=parseInt(e.firingMode,10),r=e.firingModes[n]||"";return a.jsxs(jc,{children:[a.jsxs(eb,{children:[a.jsx(hg,{onClick:()=>this.cycleMode(-1),title:"Previous mode",children:"<"}),a.jsx(tb,{children:r}),a.jsx(hg,{onClick:()=>this.cycleMode(1),title:"Next mode",children:">"})]}),n==1&&a.jsx(Ei,{children:a.jsx(jn,{onClick:()=>this.activateMode1(),children:"Activate"})}),n==2&&a.jsx(Ei,{children:a.jsx(jn,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(e),children:"Target friendly Warrior"})})]})}engageMode3(){const{ship:e,system:n}=this.props;gamedata.isMyShip(e)&&(weaponManager.hasFiringOrder(e,n)||(n.setFiringMode(3),typeof n.initializationUpdate=="function"&&n.initializationUpdate(),this.refresh()))}renderPreFiring(){const{system:e}=this.props;if(e.firingMode!=3)return a.jsxs(jc,{children:[a.jsx(ug,{children:"Gravitic Augmenter"}),a.jsx(Ei,{children:a.jsx(jn,{onClick:()=>this.engageMode3(),children:"Engage Gravity Shifting"})})]});const n=e.rotationDirection||1,r=e.rotationAmount||1,i=(o,s)=>n==o&&r==s;return a.jsxs(jc,{children:[a.jsx(ug,{children:"Gravity Shift Settings"}),a.jsxs(Ei,{children:[a.jsx(pg,{children:"Clockwise"}),a.jsx(jn,{onClick:()=>this.setRotation(1,1),$active:i(1,1),children:"60°"}),a.jsx(jn,{onClick:()=>this.setRotation(1,2),$active:i(1,2),children:"120°"})]}),a.jsxs(Ei,{children:[a.jsx(pg,{children:"Anti-Clockwise"}),a.jsx(jn,{onClick:()=>this.setRotation(2,1),$active:i(2,1),children:"60°"}),a.jsx(jn,{onClick:()=>this.setRotation(2,2),$active:i(2,2),children:"120°"})]})]})}render(){const{system:e}=this.props;return e?gamedata.gamephase==1?(e.firingMode==3&&(e.setFiringMode(1),typeof e.initializationUpdate=="function"&&e.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}}const Cs=3,rb=h.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,ib=h.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,ob=h.div`
    text-align: center;
    color: ${t=>t.$empty?"#f0a0a0":"#f2f2f2"};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,sb=h.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,ab=h.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,fg=h.div`
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

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; border: 1px solid #641b1b; color: #f2f2f2; }
    `}
`,lb=h.input`
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
`;h.div`
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
`;const En=[{key:"hitBoost5",label:"Hit Chance",increment:5,prefix:"+",suffix:"%",display:t=>t*2,parse:t=>t/2},{key:"shotBoost",label:"Shots",increment:1,prefix:"+",suffix:"",display:t=>t,parse:t=>t},{key:"dmgBoost5",label:"Damage",increment:5,prefix:"+",suffix:"",display:t=>t,parse:t=>t}],cb=(t,e)=>t.prefix+t.display(e|0)+t.suffix;class db extends R.Component{fieldSteps(e,n){return(n|0)/e.increment}allocatedSteps(e){return En.reduce((n,r)=>n+this.fieldSteps(r,e[r.key]),0)}maxSteps(){const{ship:e,system:n}=this.props;return typeof n.getMaxSteps=="function"?n.getMaxSteps(e):Math.floor(shipManager.movement.getRemainingEngineThrust(e)/Cs)}totalShots(e){return(e.guns|0)+(e.shotBoost|0)}maxDamageSteps(e){return this.totalShots(e)}clampDamageToShots(e){const n=En.find(i=>i.key==="dmgBoost5"),r=this.maxDamageSteps(e)*n.increment;(e.dmgBoost5|0)>r&&(e.dmgBoost5=r)}refresh(){const{ship:e,system:n}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n})}setValue(e,n){const{ship:r,system:i}=this.props;if(!gamedata.isMyShip(r))return;let o=Math.max(0,Math.round((n|0)/e.increment)*e.increment);const s=this.allocatedSteps(i)-this.fieldSteps(e,i[e.key]),l=Math.max(0,this.maxSteps()-s);if(o/e.increment>l&&(o=l*e.increment),e.key==="dmgBoost5"){const d=this.maxDamageSteps(i)*e.increment;o>d&&(o=d)}i[e.key]=o,e.key==="shotBoost"&&this.clampDamageToShots(i),typeof i.updateBoostNotes=="function"&&i.updateBoostNotes(),this.syncFlight(i),this.refresh()}syncFlight(e){const n=this.getFlightPulsars();for(let r=0;r<n.length;r++){const i=n[r];i!==e&&(En.forEach(o=>{i[o.key]=e[o.key]|0}),typeof i.updateBoostNotes=="function"&&i.updateBoostNotes())}}step(e,n){const{system:r}=this.props;this.setValue(e,(r[e.key]|0)+n*e.increment)}onWheel(e,n){n.preventDefault(),this.step(e,n.deltaY<0?1:-1)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,""),i=r===""?0:parseInt(r,10);this.setValue(e,e.parse(i))}propagate(){const{ship:e,system:n}=this.props;if(!gamedata.isMyShip(e))return;const r=this.getFlightPulsars();for(let i=0;i<r.length;i++){const o=r[i];o!==n&&(En.forEach(s=>{o[s.key]=n[s.key]|0}),this.clampWeaponToBudget(o),typeof o.updateBoostNotes=="function"&&o.updateBoostNotes())}this.refresh()}getFlightPulsars(){const{ship:e}=this.props,n=[],r=e&&e.systems?e.systems:[];for(let i=0;i<r.length;i++){const o=r[i]&&r[i].systems?r[i].systems:[];for(let s=0;s<o.length;s++)o[s]&&o[s].name==="MinorThoughtPulsar"&&n.push(o[s])}return n}clampWeaponToBudget(e){const n=["dmgBoost5","shotBoost","hitBoost5"],r=o=>En.find(s=>s.key===o);let i=0;for(;i++<200&&!(En.reduce((s,l)=>s+(e[l.key]|0)/l.increment,0)<=this.maxSteps());)for(let s=0;s<n.length;s++){const l=n[s];if((e[l]|0)>0){e[l]=(e[l]|0)-r(l).increment;break}}}render(){const{ship:e,system:n}=this.props;if(!n)return null;const r=typeof n.getSpareThrust=="function"?n.getSpareThrust(e):shipManager.movement.getRemainingEngineThrust(e),i=this.allocatedSteps(n)*Cs,o=Math.max(0,r-i),s=o>=Cs;return a.jsxs(rb,{children:[a.jsx(ib,{children:"Minor Thought Pulsar"}),a.jsxs(ob,{$empty:o<Cs,children:["Available thrust: ",o]}),En.map(l=>{const c=n[l.key]|0,d=l.key==="dmgBoost5"&&c/l.increment>=this.maxDamageSteps(n);return a.jsxs(sb,{children:[a.jsx(ab,{children:l.label}),a.jsx(fg,{title:"Less",disabled:c<=0,onClick:()=>this.step(l,-1),children:"−"}),a.jsx(lb,{type:"text",value:cb(l,c),onChange:u=>this.onInput(l,u),onWheel:u=>this.onWheel(l,u)}),a.jsx(fg,{title:d?"One +5 per shot (add shots for more)":"More",disabled:!s||d,onClick:()=>this.step(l,1),children:"+"})]},l.key)})]})}}const Di=t=>{let e=null;const n=r=>{r.preventDefault(),t(r)};return r=>{e!==r&&(e&&e.removeEventListener("wheel",n,{passive:!1}),e=r,e&&e.addEventListener("wheel",n,{passive:!1}))}},A={bg:"linear-gradient(180deg, rgb(27, 45, 62), rgb(18, 32, 45))",line:"#2a6b8f",radius:"6px",shadow:"0 8px 28px rgba(0, 0, 0, 0.6)",titleBg:"linear-gradient(180deg, rgb(54, 79, 110), rgb(36, 57, 80))",title:"#c6e2ff",text:"#deebff",dim:"#8ca5c0",btnBg:"#081420",btnText:"#deebff",well:"#000000",focus:"#8bcaf2"},Se={enh:{rail:m.colors.enhLine,bar:"#c39a52",wash:m.colors.enhBg,title:m.colors.enhTitle,btnBg:"#292114",btnText:m.colors.enhTitle},damage:{rail:"#2f7f92",bar:"#4cb8d0",wash:"rgba(58, 159, 181, 0.26)",title:"#e0f5fa",btnBg:"#0f262d",btnText:"#b6e3ee"},crit:{rail:"#a85c33",bar:"#dd7643",wash:"rgba(168, 92, 51, 0.34)",title:"#ffe8dc",btnBg:"#291914",btnText:"#eab99e"}},ub={rail:A.line,bar:A.line,btnBg:A.btnBg,btnText:A.btnText},Et=t=>t.$ink||ub,hb=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px 4px 10px;
    font-size: 11px;
    color: ${t=>t.$gold?m.colors.enhText:A.text};
`,pb=h.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,fb=h.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;h.span`
    flex: 0 0 auto;
    color: ${t=>t.$gold?m.colors.enhText:A.dim};
    font-size: 10px;
    opacity: ${t=>t.$gold?.75:1};
`;const $r=h.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${t=>Et(t).btnBg};
    border: 1px solid ${t=>Et(t).rail};
    border-radius: 2px;
    color: ${t=>Et(t).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${t=>Et(t).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${Et(t).btnBg};
            color: ${Et(t).btnText};
        }
    `}
`,gg=h.input`
    flex: 0 0 44px;
    width: 44px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${m.fonts.mono};
    font-size: 12px;
    color: ${t=>t.$destroyed?"#ff8a80":"#ffffff"};
    background-color: ${A.well};
    border: 1px solid ${t=>Et(t).rail};
    border-radius: 2px;
    outline: none;

    &:focus {
        border-color: ${t=>Et(t).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,mg=h.div`
    padding: 6px 10px 5px;
    background: ${A.titleBg};
    border-bottom: 1px solid ${A.line};
    color: ${A.title};
    text-align: left;
    font-family: ${m.fonts.display};
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    user-select: none;
    ${t=>t.$sticky?"position: sticky; top: 0; z-index: 1;":""}
`,Ec=t=>`
    color: ${t.title};
    background-color: ${t.wash};
    background-image: linear-gradient(to right, ${t.wash}, rgba(0, 0, 0, 0) 75%);
    border-left: 3px solid ${t.bar};
    border-top: 1px solid ${t.rail};
    border-bottom: 1px solid ${t.rail};
`,Dc=h.div`
    padding: 5px 10px 4px 7px;
    text-align: left;
    font-family: ${m.fonts.display};
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
`,Pc=h.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 3px 0 0 ${t=>Et(t).bar};
`,gb=h(Dc)`
    ${Ec(Se.enh)}
`,mb=h(Dc)`
    ${Ec(Se.damage)}
`,xb=h(Dc)`
    ${Ec(Se.crit)}
`,xg=h.div`
    height: 2px;
    background-color: ${t=>t.$chrome?A.line:m.colors.enhLine};
    opacity: 0.8;
`,yb=h.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,wb=xb,vb=h.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${m.colors.warningSoft};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${t=>t.$empty&&`
        color: #6f6257;
    `}
`,bb=h.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,Sb=h.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${A.dim};
`,kb=h.div`
    flex: 0 0 auto;
    color: ${A.dim};
`,$b=h.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,Cb=h.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${m.fonts.mono};
    font-size: 11px;
    color: ${t=>t.$empty?"#6f6257":"#ffffff"};
`,Tb=h.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,Mb=h.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,jb=h.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px 6px 10px;
`,Eb=h.select`
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
    color: ${A.text};
    /*the lobby's own input fill (.lb-input), not the number well*/
    background-color: ${A.btnBg};
    /*Takes the section's ink like the tickers above it - it is the widest control in the
      section, so leaving it on the chassis border was the one thing that still read as
      unpainted once the tickers went rust.*/
    border: 1px solid ${Se.crit.rail};
    border-radius: 2px;
    outline: none;
    cursor: pointer;

    &:focus { border-color: ${Se.crit.btnText}; }
    &:disabled { cursor: default; }
`;h.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${A.dim};
    cursor: pointer;
    user-select: none;
`;const yg=(t,e,n,r)=>{const i=[];for(const o in t||{}){if(!t.hasOwnProperty(o))continue;const s=battleDamage.PARAM_CRITICALS[o],l=parseInt((r||{})[o],10)||0;i.push({type:o,isParam:!!s,paramLabel:s?s.label:null,label:battleDamage.critLabel(o,e,l),count:parseInt(t[o],10)||0,param:l,transient:!!(n&&n[o])})}return i};class wg extends R.Component{constructor(e){super(e),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(e){e.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Di(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}critMap(){const e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.c?Object.assign({},e.c):{}}paramMap(){const e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.p?Object.assign({},e.p):{}}valueOf(e){return e.isParam?e.param:e.count}maxValueOf(e){if(!e.isParam)return battleDamage.critLimit(e.type);const n=battleDamage.PARAM_CRITICALS[e.type];return Math.min(battleDamage.MAX_CRIT_PARAM,n&&n.max||battleDamage.MAX_CRIT_PARAM)}setValue(e,n){const{ship:r,kind:i,reference:o,onChange:s}=this.props,l=this.critMap(),c=this.paramMap();n>0?e.isParam?(l[e.type]=1,c[e.type]=Math.min(n,this.maxValueOf(e))):l[e.type]=Math.min(n,battleDamage.critLimit(e.type)):(delete l[e.type],delete c[e.type]),battleDamage.setCriticals(r,i,o,l,c),s&&s()}step(e,n){const r=this.rowForType(e);this.setValue(r,this.valueOf(r)+n)}rowForType(e){const{ship:n}=this.props,r=battleDamage.PARAM_CRITICALS[e],i=parseInt(this.paramMap()[e],10)||0;return{type:e,isParam:!!r,paramLabel:r?r.label:null,label:battleDamage.critLabel(e,n.preBattleCritDesc,i),count:parseInt(this.critMap()[e],10)||0,param:i,transient:!!(n.preBattleCritTransient&&n.preBattleCritTransient[e])}}displayRows(e){const{ship:n,kind:r,reference:i}=this.props;return battleDamage.rememberCriticals(n,r,i,(e||[]).map(s=>s.type)).map(s=>this.rowForType(s))}addableTypes(e){const{ship:n,kind:r,reference:i}=this.props,o=battleDamage.offerableCriticals(n,r,i,this.state.showAll),s={};return e.forEach(l=>{s[l]=!0}),o.filter(l=>!s[l]).map(l=>({type:l,label:this.pickerLabel(l)})).sort((l,c)=>l.label.localeCompare(c.label))}pickerLabel(e){const n=battleDamage.PARAM_CRITICALS[e];return n?n.label:battleDamage.critLabel(e,this.props.ship.preBattleCritDesc,0)}onAdd(e){e&&this.setValue(this.rowForType(e),1)}render(){const{ship:e,rows:n,editable:r}=this.props,i=r?this.displayRows(n):n||[],o=!!(r&&battleDamage.catalogueFor(e)),s=o?this.addableTypes(i.map(l=>l.type)):[];return!i.length&&!o?null:a.jsxs(yb,{children:[a.jsx(xg,{$chrome:!0}),a.jsx(wb,{children:"Critical Effects"}),a.jsxs(Pc,{$ink:Se.crit,children:[i.map(l=>{const c=this.valueOf(l),d=this.maxValueOf(l);return a.jsxs(vb,{$empty:r&&c<=0,children:[a.jsxs(bb,{title:l.type,children:[r&&l.isParam?l.paramLabel:l.label,l.transient&&a.jsx(Sb,{children:"(turn 1 only)"})]}),r?a.jsxs($b,{children:[a.jsx($r,{$ink:Se.crit,title:l.isParam?"Reduce":"One fewer",disabled:c<=0,onClick:()=>this.step(l.type,-1),children:"−"}),a.jsx(Cb,{$empty:c<=0,ref:this.wheelRef(l.type),children:c}),a.jsx($r,{$ink:Se.crit,title:l.isParam?"Increase":c>=d&&d===1?"This effect only applies once":"One more",disabled:c>=d,onClick:()=>this.step(l.type,1),children:"+"})]}):l.count>1&&a.jsxs(kb,{children:["(x",l.count,")"]})]},l.type)}),o&&a.jsx(jb,{children:a.jsxs(Eb,{value:"",disabled:s.length===0,title:"Add a critical effect to this unit before the battle",onChange:l=>this.onAdd(l.target.value),children:[a.jsx("option",{value:"",children:s.length?"+ Add effect…":"Nothing to add"}),s.map(l=>a.jsx("option",{value:l.type,children:l.label},l.type))]})})]})]})}}const Db=h.div`
    display: flex;
    justify-content: flex-end;
    padding: 1px 8px 5px 10px;
    font-family: ${m.fonts.mono};
    font-size: 10px;
    color: ${m.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,Pb=h.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${m.fonts.mono};
    font-size: 10px;
    color: ${m.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${t=>t.$spent?.6:1};
`;class Ab extends R.Component{constructor(e){super(e),this.wheelRef=Di(n=>this.step(n.deltaY<0?1:-1))}step(e){const{row:n,onChange:r}=this.props,i=Math.max(0,Math.min(n.max,n.count+e));i!==n.count&&r(n.enhID,i)}onInput(e){const{row:n,onChange:r}=this.props,i=String(e.target.value).replace(/[^0-9]/g,""),o=i===""?0:parseInt(i,10);r(n.enhID,Math.max(0,Math.min(n.max,o)))}render(){const{row:e}=this.props,n=e.count>=e.max,r=(e.max>1?`${e.label} - ${e.count}/${e.max} levels`+(e.count>0?`, ${e.price} pts spent`:"")+(n?"":`; next level ${e.nextPrice} pts`):`${e.label} - ${e.price||e.nextPrice} pts`)+(e.note?`
${e.note}`:"");return a.jsxs(hb,{$gold:!0,title:r,children:[a.jsx(pb,{children:a.jsx(fb,{children:e.label})}),a.jsx($r,{$ink:Se.enh,title:"Remove a level",disabled:e.count<=0,onClick:()=>this.step(-1),children:"−"}),a.jsx(gg,{ref:this.wheelRef,$ink:Se.enh,type:"text",value:e.count,onChange:i=>this.onInput(i)}),a.jsx($r,{$ink:Se.enh,title:e.count>=e.max?"Already at the maximum":`Add a level (${e.nextPrice} pts)`,disabled:e.count>=e.max,onClick:()=>this.step(1),children:"+"}),a.jsx(Pb,{$spent:n,title:n?`Fully bought - ${e.price} pts`:"Cost of the next level",children:n?`${e.price}p`:`${e.nextPrice}p`})]})}}class Rb extends R.Component{render(){const{rows:e,onChange:n}=this.props;if(!e||e.length===0)return null;const r=e.reduce((i,o)=>i+(o.count>0?o.price:0),0);return a.jsxs(oe.Fragment,{children:[a.jsx(gb,{children:"✦ ENHANCEMENTS"}),a.jsxs(Pc,{$ink:Se.enh,children:[e.map(i=>a.jsx(Ab,{row:i,onChange:n},i.enhID)),r>0&&a.jsxs(Db,{children:["Refits: ",r," pts"]})]})]})}}const Ib=h.div`
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
    background: ${A.bg};
    border: 1px solid ${A.line};
    border-radius: ${A.radius};
    box-shadow: ${A.shadow};
    overflow: hidden;
`,Ob=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px 5px 10px;
    font-size: 11px;
    color: ${A.text};
`,Lb=h.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,zb=h.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${t=>t.$disabled?"not-allowed":"pointer"};
    user-select: none;
    opacity: ${t=>t.$disabled?.4:1};
    color: ${t=>t.$on?"#ff8a80":A.dim};
`,Bb=h.span`
    flex: 0 0 auto;
    color: ${A.dim};
    font-size: 10px;
`,Nb=h.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;class Fb extends R.Component{constructor(e){super(e),this.wheelRef=Di(n=>this.step(n.deltaY<0?1:-1))}entry(){const{ship:e,system:n}=this.props;return battleDamage.getEntry(e,battleDamage.KIND_SYSTEM,n.id)||{}}remaining(){const{system:e}=this.props,n=this.entry();return n.k?0:Math.max(0,e.maxhealth-(parseInt(n.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return this.isIndestructible()?1:0}setRemaining(e){const{ship:n,system:r}=this.props,i=r.maxhealth,o=Math.min(this.floor(),i);let s=parseInt(e,10);isNaN(s)&&(s=i),s=Math.max(o,Math.min(i,s));const l=i-s,c=s===0&&i>0;c&&this.rememberHealth(),battleDamage.setSystem(n,r.id,{d:l,k:c?1:0}),this.refresh()}rememberHealth(){const{ship:e,system:n}=this.props;this.isDestroyed()||battleDamage.rememberHealth(e,battleDamage.KIND_SYSTEM,n.id,this.remaining())}setDestroyed(e){const{ship:n,system:r}=this.props;if(e&&this.isIndestructible())return;if(e){this.rememberHealth(),battleDamage.setSystem(n,r.id,{d:r.maxhealth,k:1}),this.refresh();return}const i=battleDamage.healthMemory(n,battleDamage.KIND_SYSTEM,r.id),o=i>0?Math.min(r.maxhealth,i):r.maxhealth;battleDamage.setSystem(n,r.id,{d:r.maxhealth-o,k:0}),this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e);let n=[];if(window.systemEnhancements){const r=parseFloat(e.pointCostSysEnh)||0;n=systemEnhancements.dropDestroyed(e),n.length&&this.settleRefitCost(e,r)}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate(),n.length&&window.confirm&&typeof confirm.warning=="function"&&confirm.warning(systemEnhancements.describeRemoved(n))}settleRefitCost(e,n){const r=parseFloat(e.pointCostSysEnh)||0;e.pointCost=(parseFloat(e.pointCost)||0)-(n-r),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet()}setEnhancement(e,n){const{ship:r,system:i}=this.props;if(!window.systemEnhancements)return;const o=parseFloat(r.pointCostSysEnh)||0,s=systemEnhancements.taken(r,i.id,e);if(n===s)return;if(systemEnhancements.set(r,i.id,e,n),this.settleRefitCost(r,o),!(!window.gamedata||typeof gamedata.canAffordRefit!="function"||gamedata.canAffordRefit(r))){const c=parseFloat(r.pointCostSysEnh)||0;systemEnhancements.set(r,i.id,e,s),this.settleRefitCost(r,c),systemEnhancements.apply(r),this.forceUpdate(),window.confirm&&typeof confirm.error=="function"&&confirm.error("You cannot afford that enhancement!",function(){});return}systemEnhancements.apply(r),this.refresh()}step(e){this.setRemaining(this.remaining()+e)}onInput(e){const n=String(e.target.value).replace(/[^0-9]/g,"");this.setRemaining(n===""?0:parseInt(n,10))}render(){const{ship:e,system:n}=this.props;if(!n||!(n.maxhealth>0))return null;const r=this.remaining(),i=this.isDestroyed(),o=this.isIndestructible(),s=this.entry(),l=yg(s.c,e.preBattleCritDesc,e.preBattleCritTransient,s.p),c=window.systemEnhancements&&!i?systemEnhancements.menuRowsFor(e,n):[];return a.jsxs(Ib,{onClick:d=>d.stopPropagation(),children:[a.jsx(Rb,{rows:c,onChange:(d,u)=>this.setEnhancement(d,u)}),c.length>0&&a.jsx(xg,{}),a.jsx(mb,{children:"Damage"}),a.jsx(Pc,{$ink:Se.damage,children:a.jsxs(Ob,{children:[a.jsxs(Lb,{title:`${n.displayName||n.name} (system id ${n.id})`,children:[a.jsx(Nb,{children:n.displayName||n.name}),a.jsxs(Bb,{children:["#",n.id]})]}),a.jsx($r,{$ink:Se.damage,title:o&&r<=1?"A reactor cannot be destroyed before the battle":"More damage",disabled:i||r<=this.floor(),onClick:()=>this.step(-1),children:"−"}),a.jsx(gg,{ref:this.wheelRef,$ink:Se.damage,type:"text",$destroyed:i,disabled:i,value:i?0:r,onChange:d=>this.onInput(d)}),a.jsx($r,{$ink:Se.damage,title:"Repair",disabled:i||r>=n.maxhealth,onClick:()=>this.step(1),children:"+"}),a.jsxs(zb,{$on:i,$disabled:o,title:o?"A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship":"Mark this system destroyed before the battle starts",children:[a.jsx(Tb,{type:"checkbox",checked:i,disabled:o,onChange:d=>this.setDestroyed(d.target.checked)}),a.jsx(Mb,{children:"Destroy"})]})]})}),a.jsx(wg,{ship:e,kind:battleDamage.KIND_SYSTEM,reference:n.id,rows:l,editable:!0,onChange:()=>this.refresh()})]})}}const vg=h.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,bg=h.div`
    display: flex;
    flex-wrap: wrap;
`,xt=h.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${t=>t.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    mix-blend-mode: ${t=>t.$blend||"normal"};
    ${We}

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`,Sg=h.span`
    color: #ffd27a;
    font-size: 16px;
    line-height: 1;
    text-shadow: black 0 0 3px, black 0 0 3px;
    pointer-events: none;
`;class _b extends R.Component{constructor(e){super(e)}online(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onOnlineClicked(n,r),webglScene.customEvent("CloseSystemInfo")}offline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ms(n,r)&&(shipManager.power.onOfflineClicked(n,r),webglScene.customEvent("CloseSystemInfo"))}allOnline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onlineAll(n,r),webglScene.customEvent("CloseSystemInfo")}allOffline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ms(n,r)&&(shipManager.power.offlineAll(n,r),webglScene.customEvent("CloseSystemInfo"))}overload(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onOverloadClicked(n,r),webglScene.customEvent("CloseSystemInfo")}stopOverload(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onStopOverloadClicked(n,r),webglScene.customEvent("CloseSystemInfo")}boost(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.clickPlus(n,r)}deboost(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.clickMinus(n,r)}addShots(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Yc(n,r)&&weaponManager.changeShots(n,r,1)}reduceShots(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Kc(n,r)&&weaponManager.changeShots(n,r,-1)}removeFireOrderMulti(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;js(n,r)&&weaponManager.removeFiringOrderMulti(n,r)}removeFireOrder(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ai(n,r)&&(weaponManager.removeFiringOrder(n,r),webglScene.customEvent("CloseSystemInfo"))}removeFireOrderAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ai(n,r)&&(weaponManager.removeFiringOrderAll(n,r),webglScene.customEvent("CloseSystemInfo"))}allChangeFiringMode(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(Cr(n,r)){weaponManager.onModeClicked(n,r);var i=r.firingMode,o=[];n.flight?o=n.systems.map(g=>g.systems).reduce((g,v)=>g.concat(v),[]).filter(g=>g.weapon):o=n.systems.filter(g=>g.weapon);for(var s=weaponManager.stripPairingSuffix(r.displayName),l=new Array,c=0;c<o.length;c++)s===weaponManager.stripPairingSuffix(o[c].displayName)&&r.weapon&&l.push(o[c]);for(var c=0;c<l.length;c++){var d=l[c];if(d.firingMode!=i&&Cr(n,d)){for(var u=d.firingMode,f=0;d.firingMode!=i&&f<2;)weaponManager.onModeClicked(n,d),d.firingMode==1&&f++;if(d.firingMode!=i)for(;d.firingMode!=u;)weaponManager.onModeClicked(n,d)}}}}changeFiringMode(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Cr(n,r)&&weaponManager.onModeClicked(n,r)}selectAllWeapons(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.selectAllWeapons(n,r,"forceSelect"),webglScene.customEvent("CloseSystemInfo")}deselectAllWeapons(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.selectAllWeapons(n,r,"forceDeselect"),webglScene.customEvent("CloseSystemInfo")}declareSelfIntercept(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(Es(n,r)){if(weaponManager.onDeclareSelfInterceptSingle(n,r),r.canSplitShots)var i=r.checkFinished();i&&webglScene.customEvent("CloseSystemInfo")}}declareSelfInterceptAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(n,r),r.canSplitShots)var i=r.checkFinished();i&&webglScene.customEvent("CloseSystemInfo")}remSelfIntercept(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ds(n,r)&&weaponManager.removeSelfInterceptSingle(n,r)}declareMeteorDefence(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ps(n,r)&&(weaponManager.onDeclareMeteorDefence(n,r),webglScene.customEvent("CloseSystemInfo"))}declareMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.onDeclareMeteorDefenceAll(n,r),webglScene.customEvent("CloseSystemInfo")}remMeteorDefence(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;As(n,r)&&(weaponManager.removeMeteorDefence(n,r),webglScene.customEvent("CloseSystemInfo"))}remMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.removeMeteorDefenceAll(n,r),webglScene.customEvent("CloseSystemInfo")}nextCurrClass(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;r.nextCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}prevCurrClass(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;r.prevCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,selectedShip:n,system:r}=this.props;return Vc(e,r)?gamedata.gamephase===-2?a.jsx(vg,{children:a.jsx(Fb,{ship:e,system:r})}):a.jsxs(vg,{children:[Rg(e,r)&&a.jsx(O1,{ship:e,system:r}),a.jsxs(bg,{children:[Yc(e,r)&&a.jsx(xt,{title:"More shots",onClick:this.addShots.bind(this),img:"./img/plussquare.png"}),Kc(e,r)&&a.jsx(xt,{title:"Less shots",onClick:this.reduceShots.bind(this),img:"./img/minussquare.png"})]}),Pg(e,r)&&a.jsxs(mv,{ship:e,system:r,showModes:!!Cr(e,r),children:[Es(e,r)&&a.jsx(xt,{title:"Allow interception (RMB = All systems selected)",onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:"./img/addSelfIntercept.png"}),Ds(e,r)&&a.jsx(xt,{title:"Remove an intercept order",onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:"./img/remSelfIntercept.png"}),Ps(e,r)&&a.jsx(xt,{title:"Meteor Defence: commit this weapon to defend against meteors this turn - it cannot fire or intercept (RMB = all similar weapons)",onClick:this.declareMeteorDefence.bind(this),onContextMenu:this.declareMeteorDefenceAll.bind(this),img:"./img/selfIntercept.png",children:a.jsx(Sg,{children:"☄"})}),As(e,r)&&a.jsx(xt,{title:"Remove Meteor Defence (RMB = all similar weapons)",onClick:this.remMeteorDefence.bind(this),onContextMenu:this.remMeteorDefenceAll.bind(this),img:"./img/remSelfIntercept.png",children:a.jsx(Sg,{children:"☄"})}),js(e,r)&&a.jsx(xt,{title:"Remove last fire order",onClick:this.removeFireOrderMulti.bind(this),img:"./img/unfiringSmall.png"}),Ai(e,r)&&a.jsx(xt,{title:"Remove all fire orders (RMB = All weapons selected)",onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:"./img/firing.png"})]}),a.jsxs(bg,{children:[Ac(e,r)&&a.jsx(xt,{title:"Select all weapons of this type",onClick:this.selectAllWeapons.bind(this),img:"./img/selectAllWeapons.png",$blend:"screen"}),Ac(e,r)&&a.jsx(xt,{title:"Deselect all weapons of this type",onClick:this.deselectAllWeapons.bind(this),img:"./img/deselectAllWeapons.png",$blend:"screen"})]}),Xc(e,r)&&a.jsx(P1,{ship:e,system:r}),Rc(e,r)&&a.jsx(Xv,{ship:e,system:r}),Bc(e,r)&&a.jsx(o1,{system:r,ship:e}),Nc(e,r)&&a.jsx(p1,{system:r,ship:e}),Lc(e,r)&&a.jsx(G1,{system:r,ship:e}),zc(e,r)&&a.jsx(J1,{system:r,ship:e}),Ic(e,r)&&a.jsx(nb,{system:r,ship:e}),Oc(e,r)&&a.jsx(db,{system:r,ship:e}),(Fc(e,r)||_c(e,r))&&a.jsx(eg,{system:r,ship:e}),(Hc(e,r)||Wc(e,r))&&a.jsx(eg,{system:r,ship:e}),Ts(e,r)&&a.jsx(Ev,{ship:e,system:r,readOnly:!Hb(e,r)}),Uc(e,r)&&a.jsx(_v,{ship:e,system:r,readOnly:!Wb(e,r)}),"   ",Rs(e,r)&&a.jsx(S1,{ship:e,system:r}),Ag(e,r)&&a.jsx(T1,{ship:e,system:r})]}):null}}const Ac=(t,e)=>!(!window.matchMedia("(pointer: coarse)").matches||!e.weapon||Pi(t,e)||gamedata.gamephase!=3&&!e.ballistic&&!e.preFires||gamedata.gamephase!=1&&e.ballistic||gamedata.gamephase!=5&&e.preFires),Rc=(t,e)=>gamedata.gamephase===1&&e.name=="adaptiveArmorController",Ic=(t,e)=>e.name==="GraviticAugmenter"&&gamedata.isMyShip(t)&&!e.stowed&&!shipManager.power.isOffline(t,e)&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(t,e)||gamedata.gamephase===5),Oc=(t,e)=>e.name==="MinorThoughtPulsar"&&gamedata.gamephase===3&&gamedata.isMyShip(t)&&!e.stowed&&!shipManager.systems.isDestroyed(t,e)&&!shipManager.power.isOffline(t,e),Lc=(t,e)=>gamedata.gamephase===-1&&t.mine&&(t.spawned==-1&&gamedata.turn==1||t.spawned==gamedata.turn-1)&&(e.name=="CaptorMine"||e.name=="MineControllerDEW"),zc=(t,e)=>gamedata.gamephase===-1&&t.mine&&(t.spawned==-1&&gamedata.turn==1||t.spawned==gamedata.turn-1)&&e.name=="ProximityMine",Bc=(t,e)=>gamedata.gamephase===1&&e.name=="hyachComputer",Nc=(t,e)=>e.name==="hyachSpecialists",Fc=(t,e)=>gamedata.gamephase===1&&e.name==="ThirdspaceShield",_c=(t,e)=>gamedata.gamephase===1&&e.name==="ThirdspaceShieldGenerator",Hc=(t,e)=>gamedata.gamephase===1&&e.name==="ThoughtShield",Wc=(t,e)=>gamedata.gamephase===1&&e.name==="ThoughtShieldGenerator",Ts=(t,e)=>gamedata.isMyShip(t)&&e.name=="SelfRepair",Hb=(t,e)=>Ts(t,e)&&gamedata.gamephase===1,Uc=(t,e)=>gamedata.isMyShip(t)&&(e.name=="StructureSelfRepair"||e.name=="CoopStructureSelfRepair"),Wb=(t,e)=>Uc(t,e)&&gamedata.gamephase===1,kg=()=>typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted(),Dn=(t,e)=>gamedata.gamephase===-2&&!kg()&&t&&t.userid!=0&&!t.flight&&!t.mine&&!Ub(e),$g=(t,e)=>Dn(t,e)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(t,e)&&systemEnhancements.offersFor(t,e).length>0,Gc=t=>gamedata.gamephase===-2&&!kg()&&t&&t.userid!=0&&!!t.mine&&battleDamage.mineMaxHealth(t)>1,Ub=t=>!t||!(t.maxhealth>0)||t.isTargetable===!1||!!t.hideInShipWindow||Array.isArray(t.systems),Vc=(t,e)=>gamedata.gamephase===-2?Dn(t,e)||$g(t,e):Ms(t,e)||Cg(t,e)||Tg(t,e)||Mg(t,e)||jg(t,e)||Eg(t,e)||Yc(t,e)||Kc(t,e)||js(t,e)||Ai(t,e)||Cr(t,e)||Es(t,e)||Ds(t,e)||Ps(t,e)||As(t,e)||Rc(t,e)||Bc(t,e)||Nc(t,e)||Fc(t,e)||Hc(t,e)||_c(t,e)||Wc(t,e)||Ts(t,e)||Gb(t,e)||Vb(t,e)||Rs(t,e)||Ag(t,e)||Xc(t,e)||Ac(t,e)||Lc(t,e)||zc(t,e)||Ic(t,e)||Oc(t,e),Ms=(t,e)=>gamedata.gamephase===1&&(e.canOffLine||e.powerReq>0)&&!e.powerLocked&&!shipManager.power.isOffline(t,e)&&!shipManager.power.getBoost(e)&&!weaponManager.hasFiringOrder(t,e),Cg=(t,e)=>gamedata.gamephase===1&&shipManager.power.isOffline(t,e)&&!shipManager.power.isForcedOffline(t,e)&&!shipManager.power.isVortexLockedOffline(t,e),Tg=(t,e)=>gamedata.gamephase===1&&!shipManager.power.isOffline(t,e)&&e.weapon&&e.overloadable&&!shipManager.power.isOverloading(t,e),Mg=(t,e)=>gamedata.gamephase===1&&e.weapon&&e.overloadable&&shipManager.power.isOverloading(t,e)&&(e.overloadshots>=e.extraoverloadshots||e.overloadshots==0),jg=(t,e)=>e.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(t,e)&&(!e.isScanner()||e.id==shipManager.power.getHighestSensorsId(t))&&e.name!=="ThirdspaceShieldGenerator"&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor",Eg=(t,e)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(e)&&e.name!=="ThirdspaceShieldGenerator"&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor",Dg=(t,e)=>{const n=weaponManager.getFiringOrder(t,e);return n&&n.type!=="intercept"&&n.type!=="selfIntercept"?n:null},Yc=(t,e)=>{if(Pi(t,e)||!e.weapon||!e.canChangeShots||!weaponManager.hasFiringOrder(t,e))return!1;const n=Dg(t,e);return!!n&&n.shots<e.maxVariableShots},Kc=(t,e)=>{if(Pi(t,e)||!e.weapon||!e.canChangeShots||!weaponManager.hasFiringOrder(t,e))return!1;const n=Dg(t,e);return!!n&&n.shots>1},Pi=(t,e)=>e.name==="jumpEngine"&&typeof e.getHeldVortex=="function"&&!!e.getHeldVortex(),js=(t,e)=>e.weapon&&weaponManager.hasOrderForMode(e)&&e.canSplitShots&&!Pi(t,e),Ai=(t,e)=>e.weapon&&weaponManager.hasFiringOrder(t,e)&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked())&&!Pi(t,e),Qc=t=>!!(window.shipManager&&shipManager.isDockingRider(t)),Cr=(t,e)=>e.weapon&&!t.mine&&!e.stowed&&!e.hideFiringModeSelector&&!Qc(t)&&e.name!=="GraviticAugmenter"&&e.name!=="MinorThoughtPulsar"&&(gamedata.gamephase===1&&e.ballistic||gamedata.gamephase===5&&e.preFires||gamedata.gamephase===3&&!e.ballistic&&!e.preFires)&&(!weaponManager.hasFiringOrder(t,e)||e.multiModeSplit)&&!weaponManager.hasMeteorDefence(t,e)&&Object.keys(e.firingModes).length>1,Es=(t,e)=>e.weapon&&!Qc(t)&&weaponManager.canSelfInterceptSingle(t,e),Ds=(t,e)=>e.weapon&&e.canSplitShots&&!Qc(t)&&weaponManager.canRemInterceptSingle(t,e),Pg=(t,e)=>Cr(t,e)||Es(t,e)||Ds(t,e)||Ps(t,e)||As(t,e)||js(t,e)||Ai(t,e),Ps=(t,e)=>e.weapon&&weaponManager.canDeclareMeteorDefence(t,e),As=(t,e)=>e.weapon&&weaponManager.canRemoveMeteorDefence(t,e),Gb=(t,e)=>e.canActivate&&typeof e.canActivate=="function"&&e.canActivate()&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor"&&e.name!=="GraviticAugmenter"&&e.name!=="jumpEngine",Vb=(t,e)=>e.canDeactivate&&typeof e.canDeactivate=="function"&&e.canDeactivate()&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor"&&e.name!=="GraviticAugmenter"&&e.name!=="jumpEngine",Ag=(t,e)=>e.name==="jumpEngine"&&typeof e.canMaintainVortex=="function"&&(e.canMaintainVortex()||e.canDeactivate()||typeof e.getAbductionOrder=="function"&&!!e.getAbductionOrder()),Rs=(t,e)=>e.name==="powerCapacitor"||e.name==="PowerCapacitor",Rg=(t,e)=>Ms(t,e)||Cg(t,e)||Tg(t,e)||Mg(t,e)||e.boostable&&(jg(t,e)||Eg(t,e)),Xc=(t,e)=>Rs(t,e)||e.name==="GraviticAugmenter"||e.name==="jumpEngine"?!1:!!(e.canActivate&&typeof e.canActivate=="function"&&e.canActivate()||e.canDeactivate&&typeof e.canDeactivate=="function"&&e.canDeactivate()),Yb=(t,e)=>gamedata.gamephase===-2?Dn(t,e)||$g(t,e):Rc(t,e)||Bc(t,e)||Nc(t,e)||Lc(t,e)||zc(t,e)||Ic(t,e)||Oc(t,e)||Fc(t,e)||_c(t,e)||Hc(t,e)||Wc(t,e)||Ts(t,e)||Uc(t,e)||Rs(t,e)||Rg(t,e)||Xc(t,e)||Pg(t,e),Ig=h.div`
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
        width:  ${t=>t.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        background-color: ${t=>t.$docked?"#00ffff":t.$criticals?t.$criticalsBenign?"#00ffff":"#ed6738":"#427231"};
    }
`,Og=h.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,Kb=h.div`
    /*11px on a 32px icon: the 7px it launched at was legible only if you already knew to
      look for it (user report 2026-08-15). Still small enough to clear the icon art and the
      [n/n] load counter, which sits along the BOTTOM edge.*/
    font-size: 11px;
    line-height: 11px;
    color: ${m.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,Qb=h.div`
    position: absolute;
    top: 0px;
    left: 1px;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    gap: 1px;
    pointer-events: none;
`,Xb=h.svg`
    display: block;
    width: 11px;
    height: 11px;
    overflow: visible;
    filter: drop-shadow(0 0 1px black) drop-shadow(0 0 1px black);
`,Zb={serving:m.colors.enhTitle,disabled:"#c4c4c4",killed:"#c4c4c4"},Lg=({state:t,title:e})=>a.jsxs(Xb,{viewBox:"0 0 12 12",role:"img","aria-label":e,children:[a.jsx("title",{children:e}),a.jsx("path",{d:"M1.5 5.5 L6 2 L10.5 5.5 M1.5 10 L6 6.5 L10.5 10",fill:"none",stroke:Zb[t],strokeWidth:"2",strokeLinecap:"square"}),t==="killed"&&a.jsx("path",{d:"M1 11.5 L11 0.5",stroke:"#ff4d4f",strokeWidth:"1.8",strokeLinecap:"round"})]}),qb=h.div`
    position: absolute;
    top: 0px;
    right: 1px;
    z-index: 1;
    pointer-events: none;
    font-size: 11px;
    line-height: 11px;
    color: #ffd27a;
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,zg=h.div`
    position: relative;
    box-sizing: border-box;
    width: 32px;
    height: 32px;
    margin: ${t=>t.$scs?"3px 0":"2px"};
   border: ${t=>t.$firing&&t.$calledShot?"2px solid #ff3366":t.$meteorDefence?"1px solid #d9a441":t.$firing&&t.$intercepting?"1px solid #52b352":t.$firing?"1px solid #eb5c15":t.$orderPending?"2px solid #00e5ff":t.$highlight==="Yellow"?"1px solid #e1b000":t.$highlight==="Orange"?"2px solid #ff6d3c":t.$highlight==="Red"?"2px solid #ff0000":"1px solid #496791"};
     background-color:  ${t=>t.$selected?"#4e6c91":t.$meteorDefence?"#5c4318":t.$firing&&t.$intercepting?"#2f7a3a":t.$firing?"#e06f01":t.$off?"#852d2d":t.$boosted?"#cca300":t.$loading&&t.$loadedAlternate?"#CD9E9E":"rgba(0, 0, 0, 0.7)"};
    box-shadow: ${t=>t.$selected?"0px 0px 15px #0099ff":t.$firing&&t.$calledShot?"0px 0px 12px #ff3366":t.$meteorDefence?"0px 0px 12px #d9a441":t.$firing&&t.$intercepting?"0px 0px 15px #52b352":t.$firing?"box-shadow: 0px 0px 15px #eb5c15":t.$orderPending?"0px 0px 12px #00e5ff":"none"};
    /*$mirror (rolled ship, port/starboard drawn swapped): the icon ART is flipped
      horizontally on an ::after layer so its facing matches the drawn side, while
      text, health bar and state overlays stay unflipped and readable*/
    background-image: ${t=>t.$mirror?"none":`url(${t.$background})`};
    background-size: cover;
    ${t=>t.$mirror?"z-index: 0;":""} /*own stacking context keeps the z:-1 art layer inside this icon*/
    ${t=>t.$mirror?`
    &::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url(${t.$background});
        background-size: cover;
        transform: scaleX(-1);
        z-index: -1;
    }
    `:""}
    filter: ${t=>t.$destroyed?"blur(1px)":"none"};
    cursor: pointer;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    
    ${Og} {
        display: ${t=>t.$offline?"none":"flex"};
        /*docked Kirishiac Orbital: keep the icon text ([n/5] regeneration counter) fully
        readable above the blue fade - the ::before overlay is positioned, so the static
        text would otherwise paint underneath it*/
        ${t=>t.$docked?"position: relative; z-index: 1;":""}
    }


    &::before {
        content: "";
        position:absolute;
        width: 100%;
        height: 100%;
        opacity: ${t=>t.$destroyed||t.$offline||t.$loading||t.$docked?"0.5":"0"};

        background-color: ${t=>t.$destroyed||t.$offline?"black":t.$loading?"orange":t.$docked?"#1a4a6e":"transparent"};

        background-image: ${t=>t.$offline?"url(./img/offline.png)":"none"};
    }
`;class Zc extends R.Component{constructor(e){super(e),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(e){if(e.stopPropagation(),e.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n);const i=Dn(r,n);if((gamedata.waiting||gamedata.replay)&&!i)return;const o=!!r.removed&&!shipManager.isDestroyedByDamage(r);if(!(!i&&!o&&(shipManager.isDestroyed(r)||shipManager.isDestroyed(r,n)&&!n.clickableWhenDestroyed))){if(o&&!i){gamedata.isMyShip(r)&&window.uiEvents.relay("SystemClicked",{ship:r,system:n,element:e.currentTarget,showMenu:!0});return}if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(r)){var s=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(s&&s.ship.id!=r.id&&!weaponManager.isSelectedWeapon(n)){window.uiEvents.relay("SystemTargeted",{ship:r,system:n});return}}var l=n.weapon&&typeof n.isSpentLocked=="function"&&n.isSpentLocked(),c=typeof n.canSelectForAbduction=="function"&&n.canSelectForAbduction(r);if(!l&&(n.weapon&&gamedata.gamephase===3&&!n.ballistic&&!n.preFires||gamedata.gamephase===1&&n.ballistic||gamedata.gamephase===5&&n.preFires||weaponManager.canManuallyInterceptWith(r,n)||c)&&!shipManager.isAdrift(r)&&gamedata.isMyShip(r)){if(n.hasSpecialTargeting&&typeof n.reopenSpecialTargeting=="function"&&weaponManager.hasFiringOrder(r,n)&&n.reopenSpecialTargeting(r))return;var d=weaponManager.hasFiringOrder(r,n),u=d&&d!=="self"&&!n.canSplitShots&&!n.hasSpecialTargeting;weaponManager.isSelectedWeapon(n)?weaponManager.unSelectWeapon(r,n):u||weaponManager.selectWeapon(r,n)}if(gamedata.isMyShip(r)&&(n.name==="hangar"||n.name==="catapult"||n.name==="fighterRail")){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog=="function"&&window.DeploymentDock.shipHasOpenableDockDialog(r)&&window.confirm&&typeof window.confirm.hangarDeployDock=="function"){window.confirm.hangarDeployDock(r);return}if(gamedata.gamephase===3&&!n.isShadowHangar&&!shipManager.movement.isRolling(r)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(r)!=="no")&&window.confirm&&typeof window.confirm.hangarLaunch=="function"){window.confirm.hangarLaunch(r);return}}if(gamedata.isMyShip(r)&&(n.name==="dockingCollar"||n.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(r)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(r)!=="no")&&typeof window.lcvRailLaunchable=="function"&&window.lcvRailLaunchable(r,n)&&window.confirm&&typeof window.confirm.lcvLaunch=="function"){window.confirm.lcvLaunch(r);return}gamedata.isMyShip(r)?window.uiEvents.relay("SystemClicked",{ship:r,system:n,element:e.currentTarget,showMenu:!0}):window.uiEvents.relay("SystemTargeted",{ship:r,system:n})}}onSystemMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;e.stopPropagation(),e.preventDefault();let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n),window.uiEvents.relay("SystemMouseOver",{ship:r,system:n,element:e.currentTarget,showInfo:!0})}onSystemMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay("SystemMouseOut"))}onTouchStart(e){e.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:i,ship:o}=this.props;i=shipManager.systems.initializeSystem(i),window.uiEvents.relay("SystemMouseOver",{ship:o,system:i,element:n,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(e){e.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(e){if(e.stopPropagation(),e.preventDefault(),window.matchMedia("(pointer: coarse)").matches)return;let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n),n.weapon&&weaponManager.selectAllWeapons(r,n)}render(){let{system:e,ship:n,scs:r,fighter:i,destroyed:o,mirror:s}=this.props;return e=shipManager.systems.initializeSystem(e),e=shipManager.systems.initializeSystem(e),(qc(n,e)||o)&&!e.clickableWhenDestroyed&&!Dn(n,e)?a.jsxs(zg,{$background:_g(e),$destroyed:!0,$mirror:s,children:[Bg(n,e),a.jsx(Ig,{$health:"0"})]}):a.jsxs(zg,{$scs:r,$highlight:hS(n,e),$destroyed:qc(n,e)||o,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:_g(e),$mirror:s,$offline:oS(n,e),$loading:rS(e),$loadedAlternate:iS(e),$selected:pS(e),$firing:Jb(n,e),$intercepting:tS(n,e),$meteorDefence:Ng(n,e),$calledShot:nS(n,e),$boosted:aS(n,e),$off:sS(e),$docked:Fg(e),$orderPending:cS(e),children:[Bg(n,e),Ng(n,e)&&a.jsx(qb,{title:"Committed to meteor defence this turn",children:"☄"}),a.jsx(Og,{children:fS(n,e)}),(!i||Hg(e))&&a.jsx(Ig,{$scs:r,$health:qc(n,e)||o?0:dS(n,e),$criticals:Hg(e),$criticalsBenign:uS(e),$docked:lS(e)})]})}}const Bg=(t,e)=>{if(!t||!e)return null;const n=!!window.systemEnhancements&&systemEnhancements.hasAny(t,e.id),r=window.officers?officers.postState(t,e.id):null;return!n&&!r?null:a.jsxs(Qb,{children:[n&&a.jsx(Kb,{title:"Carries a system enhancement",children:"✦"}),r&&a.jsx(Lg,{state:r,title:officers.describePost(t,e.id)})]})},Jb=(t,e)=>(weaponManager.hasFiringOrder(t,e)||eS(e))&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked()),eS=t=>gamedata.gamephase===1&&typeof t.getAbductionOrder=="function"&&!!t.getAbductionOrder(),tS=(t,e)=>weaponManager.isInterceptOnly(t,e),Ng=(t,e)=>!!e.weapon&&weaponManager.hasMeteorDefence(t,e),nS=(t,e)=>!e.weapon||!weaponManager.hasFiringOrder(t,e)?!1:weaponManager.getCalledShotInfo(t,e)!==null,rS=t=>t.weapon&&(!weaponManager.isLoaded(t)||typeof t.isSpentLocked=="function"&&t.isSpentLocked()),iS=t=>t.weapon&&weaponManager.isLoadedAlternate(t),oS=(t,e)=>shipManager.power.isOffline(t,e),sS=t=>t.activeMeansOff&&t.active,aS=(t,e)=>shipManager.power.isBoosted(t,e)||e.active&&!e.activeMeansOff&&!e.suppressActiveBoost,Fg=t=>!!(t.showDockedVisual&&t.activeEffective||t.stowed&&t.stowedArcStart==null||t.dockedWithOrbital),lS=t=>Fg(t)||!!t.stowed,cS=t=>!!(t.showDockedVisual&&typeof t.hasPendingDockingOrder=="function"&&t.hasPendingDockingOrder()),dS=(t,e)=>(e.name==="ThirdspaceShield"||e.name==="ThoughtShield")&&e.baseRating?Math.min(100,e.currentHealth/e.baseRating*100):(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,qc=(t,e)=>shipManager.systems.isDestroyed(t,e),_g=t=>t.name=="thruster"&&!t.iconPath?window.AssetManager.getSmartImagePath("./img/systemicons/thruster"+t.direction+".png"):t.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${t.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${t.name}.png`),Hg=t=>shipManager.criticals.hasCriticalsIcon(t),uS=t=>shipManager.criticals.hasOnlyCritical(t,"HangarOperations",!0)||shipManager.criticals.hasOnlyCritical(t,"LCVLaunchedThisTurn",!0),hS=(t,e)=>shipManager.systems.hasBorderHighlight(t,e),pS=t=>weaponManager.isSelectedWeapon(t),fS=(t,e)=>{if(e.outputDisplay!==void 0&&e.outputDisplay!==null&&e.outputDisplay!="")return e.outputDisplay;if(e.weapon){if(e.stowed&&e.stowedArcStart==null)return"-";if(typeof e.isSpentLocked=="function"&&e.isSpentLocked())return"✓";if(typeof e.getVortexIconLoad=="function"){const i=e.getVortexIconLoad();if(i!=null)return i}const r=weaponManager.hasFiringOrder(t,e);if(r&&e.canChangeShots)return weaponManager.getFiringOrder(t,e).shots+"/"+e.shots;if(r){var n=weaponManager.getCalledShotInfo(t,e);if(n)return"⊕"}else if(!r){let i=weaponManager.getWeaponCurrentLoading(e),o=e.loadingtime;e.normalload>0&&(o=e.normalload),i>o&&(i=o);let s="";return e.overloadturns>0&&shipManager.power.isOverloading(t,e)&&(s="("+e.overloadturns+")"),e.overloadshots>0?"S"+e.overloadshots:i+s+"/"+o}}else{if(e.outputType==="thrust")return shipManager.movement.getRemainingEngineThrust(t);if(e.outputType==="power"){let r=shipManager.power.getReactorPower(t,e);return gamedata.gamephase>1&&r<0?0:r}else return shipManager.systems.getOutput(t,e)}},gS=h.div`
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
`;class mS extends R.Component{constructor(e){super(e)}getWeapons(e,n){return e.flight?e.systems.map(r=>r.systems).reduce((r,i)=>r.concat(i),[]).filter(r=>r.weapon):e.systems.filter(r=>r.weapon||r.outputType==="thrust"||r.outputType==="EW"||r.outputType==="power"||r.outputType==="settings")}render(){const{ship:e,gamePhase:n}=this.props;if(!e)return null;const r=this.getWeapons(e,n);return a.jsx(gS,{children:r.map((i,o)=>a.jsx(Zc,{fighter:e.flight,system:i,ship:e},`system-${o}`))})}}const Wg=t=>{if(!t.hitChart)return[];const e=["Primary","Front","Aft","Port","Starboard"];let n=5;t.base&&!t.smallBase?(e[1]="Sections",n=2):t.SixSidedShip&&(e[31]="Port Front",e[32]="Port Aft",e[41]="Starboard Front",e[42]="Starboard Aft",n=43);const r=[];for(let i=0;i<n;i++){if(t.hitChart[i]===void 0)continue;const o=[];let s=0;for(const l in t.hitChart[i]){const c=Math.floor((l-s)/20*100);s=l;let d=t.hitChart[i][l];const u=d.indexOf(":");u>0&&(d=d.substring(u+1)),o.push({name:d,chance:c})}r.push({location:i,name:e[i],entries:o})}return r},xS=h.div`
    ${t=>t.$tightBottom?"& > *:last-child { display: none; }":""}
    ${t=>t.$compactText?`
    ${N} {
        font-size: 10px;
        line-height: 1.4;
        color: ${m.colors.textAccent};
    }
    ${ee} {
        font-size: 10px;
        font-style: normal;
        color: ${m.colors.text};
    }`:""}
`;class Ug extends R.Component{render(){const{ship:e,hideHitChart:n,tightBottom:r,compactText:i}=this.props,o=!!e.mine||window.gamedata&&typeof gamedata.isTerrain=="function"&&gamedata.isTerrain(e.shipSizeClass,e.userid),s=!!e.flight||o;var l=new Array,c=new Array,d=new Array;e.notes&&(l=e.notes.split("<br>")),e.hitChart&&!n&&Wg(e).forEach(function(y){c[y.name]=y.entries.map(function(p){return p.name+" "+p.chance+"%"}).join(", ")}),e.enhancementTooltip!=""&&(d=e.enhancementTooltip.split("<br>"));let u={};if(!e.flight&&e.hasAttached&&Object.keys(e.hasAttached).length>0){const y={1:"Forward",2:"Aft",3:"Port",31:"Port-Forward",32:"Port-Aft",4:"Starboard",41:"Starboard-Forward",42:"Starboard-Aft"};for(let p in e.hasAttached){let w=e.hasAttached[p],S=y[w]||"Unknown";u[S]||(u[S]=0),u[S]++}}let f=window.officers?officers.displayStat(e,"offensivebonus"):e.offensivebonus;e.flight&&gamedata.areMinesPresent&&(e.minesweeper?f-=window.ew.getDetectMEW(e):f-=window.ew.getDetectMEW(e)*2);const g=window.officers?officers.currentTurn():0,v=e.flight&&window.officers?officers.listFor(e).map(y=>{const p=officers.get(y.id),w=officers.stateText(y);return{name:officers.label(y.id),text:(p?p.summary:"")+(w?" "+w:""),out:!officers.serves(y,g)}}):[];var x=0,b=!0;if(e.mine){var M=shipManager.systems.getSystemByName(e,"mineStealth");M&&!M.isMineRevealed(e)&&(b=!1,c=new Array,l=["No details known, scan with OEW to identify."],d=new Array)}return a.jsxs(xS,{$tightBottom:r,$compactText:i,children:[e.flight&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"Offensive bonus: "}),f*5]},x++),e.flight&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"Armor (F/S/A): "}),shipManager.systems.getFlightArmour(e)]},x++),e.flight&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"Profile - Front/Side: "}),e.forwardDefense*5,"/",e.sideDefense*5]},x++),e.flight&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"Initiative: "}),window.officers?officers.displayIniativeBonus(e):e.iniativebonus]},x++),e.flight&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"Thrust: "}),e.freethrust]},x++),e.flight&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"Turn Cost: "}),e.turncost]},x++),e.flight&&b&&e.turndelay&&a.jsxs(N,{children:[a.jsx(ee,{children:"Turn Delay: "}),e.turndelaycost]},x++),e.flight&&b&&a.jsx(N,{children:" "},x++),Object.keys(l).length>0&&a.jsxs(N,{children:[a.jsx(ee,{children:"NOTES:"})," "]},x++),Object.keys(l).length>0&&Object.keys(l).map(y=>a.jsx(N,{children:l[y]},x++)),Object.keys(l).length>0&&a.jsx(N,{children:" "},x++),Object.keys(c).length>0&&a.jsxs(N,{children:[a.jsx(ee,{children:"HIT CHART:"})," "]},x++),Object.keys(c).length>0&&Object.keys(c).map(y=>a.jsxs(N,{children:[a.jsxs(ee,{children:[y,": "]}),c[y]]},x++)),Object.keys(c).length>0&&a.jsx(N,{children:" "},x++),Object.keys(u).length>0&&a.jsxs(N,{children:[a.jsx(ee,{children:"UNITS ATTACHED:"})," "]},x++),Object.keys(u).length>0&&Object.keys(u).map(y=>a.jsxs(N,{children:[a.jsxs(ee,{children:[y,": "]}),u[y]]},x++)),Object.keys(u).length>0&&a.jsx(N,{children:" "},x++),s&&e.enhancementTooltip!=""&&b&&a.jsxs(N,{children:[a.jsx(ee,{children:"ENHANCEMENTS:"})," "]},x++),s&&e.enhancementTooltip!=""&&b&&Object.keys(d).map(y=>a.jsx(N,{children:d[y]},x++)),s&&e.enhancementTooltip!=""&&b&&a.jsx(N,{children:" "},x++),e.flight&&v.length>0&&a.jsxs(N,{children:[a.jsx(bS,{children:"OFFICERS:"})," "]},x++),e.flight&&v.map(y=>a.jsxs(Yg,{$out:y.out,children:[a.jsxs(Kg,{children:[y.name,": "]}),y.text]},x++)),e.flight&&v.length>0&&a.jsx(N,{children:" "},x++)]})}}const Pn=h(Pf)`
    /*font-size: 12px;*/
	font-size: 13px;
`,Gg=h(Df)`
    position: absolute;
    z-index: 20000;
    ${t=>Object.keys(t.position).reduce((e,n)=>e+`
`+n+":"+t.position[n]+"px;","")}
    width: ${t=>t.ship?"320px":"220px"};
    text-align: left;
    opacity:0.8;
`,Jc=h.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,yS={MissileLost:"A missile was lost to damage"},Vg={DamageReductionReduced:t=>`Damage reduction reduced by ${t}`},N=h(Af)`
    text-align: left;
    /*color: #5e85bc;*/
	color: #BDEAFA; /*replace dark blue above with bluish white, more eyes friendly*/
    font-family: arial;
    /*font-size: 11px;*/
	font-size: 12px;
`,ee=h.span`
    color: white;
	font-size: 11px;
    font-weight: bold;
`,wS=h.span`
    color: #C6E2FF;
`,vS=h(Pn)`
    color: ${m.colors.enhTitle};
`,Yg=h(N)`
    &&& { color: ${t=>t.$out?m.colors.textDim:m.colors.enhText}; }
`,Kg=h.span`
    &&& { color: ${m.colors.enhTitle}; }
`,bS=h(ee)`
    &&& { color: ${m.colors.enhTitle}; }
`;class SS extends R.Component{render(){const{ship:e,selectedShip:n,system:r,boundingBox:i}=this.props;if(r instanceof Ship||r===e){var o=e.shipClass,s=e.name;if(r.flight&&(o=r.systems[1].displayName),e.mine){var l=shipManager.systems.getSystemByName(e,"mineStealth");l&&!l.isMineRevealed(e)&&(o="Mine",s="Mine")}return a.jsxs(Gg,{ship:!0,position:Xg(i),children:[a.jsxs(Pn,{children:[a.jsx(wS,{children:s})," - ",o]}),a.jsx(Ug,{ship:e})]})}var c=new Array;r.data.Special&&r.data.Special!=""&&(c=r.data.Special.split("<br>"));var d="Special",u=0;let f=window.officers?officers.displayStat(e,"offensivebonus"):e.offensivebonus;e.flight&&gamedata.areMinesPresent&&(e.minesweeper?f-=window.ew.getDetectMEW(e):f-=window.ew.getDetectMEW(e)*2);var g=r.displayName,v=r.firingModes?r.firingModes[r.firingMode]:null,x=null;r.name==="ShadowFighterBomb"&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool=="function"&&(x=weaponManager.shadowFighterBombPool(e,r,!0));var b=null;if(r.outputType==="power"&&window.shipManager&&shipManager.power&&typeof shipManager.power.getDockedPowerSummary=="function"){var M=shipManager.power.getDockedPowerSummary(e);M.donors>0&&(b=M)}var y=null;if(typeof r.getAbductionOrder=="function"){var p=r.getAbductionOrder();if(p){var w=gamedata.getShip(p.targetid);y=w?w.name:"Unit "+p.targetid}}let S=!1;if(e.mine){var l=shipManager.systems.getSystemByName(e,"mineStealth");l&&!l.isMineRevealed(e)&&(S=!0,g="Mine",c=["No details known, scan with OEW to identify."])}return a.jsxs(Gg,{position:Xg(i),children:[a.jsx(Pn,{children:g}),!e.flight&&!S&&Dt("Structure",r.maxhealth-damageManager.getDamage(e,r)+"/"+r.maxhealth),!e.flight&&!S&&Dt("Armor",shipManager.systems.getArmour(e,r)),e.flight&&!S&&Dt("Offensive bonus",TS(r,f*5)),r.firingModes&&!S&&Dt("Firing mode",v),r.missileArray&&Object.keys(r.missileArray).length>0&&!S&&Dt("Ammo Amount",r.missileArray[r.firingMode].amount),!S&&Object.keys(r.data).map((C,$)=>C!=d&&!(C==="Ammunition"&&(r.name==="GrapplingClaw"||r.name==="Marines"))&&Dt(C,MS(e,r,C),"data"+$)),x!==null&&Dt("Fighters available",x),b&&Dt("Shared by docked ships","+"+b.shared+" of "+b.surplus+" pooled from "+b.donors+(b.donors===1?" ship":" ships")),y!==null&&Dt("Abduction target",y),Object.keys(c).length>0&&a.jsxs(N,{children:[a.jsx(ee,{children:"Special: "})," "]},`special-${u++}`),Object.keys(c).length>0&&Object.keys(c).map(C=>a.jsx(N,{children:c[C]},`special-${u++}`)),!S&&$S(e,r),(Object.keys(r.critData).length>0||r.criticals&&r.criticals.length>0)&&!S&&CS(r),!gamedata.isMyShip(e)&&!S&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&n&&Qg(e,n,r),gamedata.isMyShip(e)&&!S&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&n&&Qg(e,n,r),gamedata.isMyShip(e)&&!S&&r.weapon&&weaponManager.hasFiringOrder(e,r)&&kS(e,r)]})}}const Qg=(t,e,n)=>weaponManager.canCalledshot(t,n,e)?[a.jsx(Pn,{children:"Called shot"},"calledHeader")].concat(gamedata.selectedSystems.map((r,i)=>{if(weaponManager.isOnWeaponArc(e,t,r))if(weaponManager.checkIsInRange(e,t,r)){var o=r.firingMode;return o=r.firingModes[o],n.id!=null&&!weaponManager.canWeaponCall(r)?a.jsxs(N,{children:[a.jsx(ee,{children:r.displayName}),": Cannot Called Shot"]},`called-${i}`):a.jsxs(N,{children:[a.jsx(ee,{children:r.displayName})," - Approx:  ",weaponManager.calculateHitChange(e,t,r,n.id).hitChance,"%"]},`called-${i}`)}else return a.jsxs(N,{children:[a.jsx(ee,{children:r.displayName}),": Not in Range"]},`called-${i}`);else return a.jsxs(N,{children:[a.jsx(ee,{children:r.displayName}),": Not in Arc"]},`called-${i}`)})):[a.jsx(Pn,{children:"Called shot"},"calledHeader")].concat(a.jsx(N,{children:"Cannot Target"},"cannotTarget")),kS=(t,e)=>{var n=weaponManager.getCalledShotInfo(t,e);return n?[a.jsx(Jc,{},"calledShotDivider"),a.jsx(Pn,{children:"Called Shot"},"calledShotHeader"),a.jsxs(N,{children:[a.jsx(ee,{children:"Target: "}),n.targetSystem.displayName," (Id: ",n.targetSystem.id,") on ",n.targetShip.name]},"calledShotTarget")]:null},$S=(t,e)=>{if(!window.officers||!t||!e)return null;const n=officers.onPost(t,e.id);if(n.length===0)return null;const r=officers.currentTurn();return[a.jsx(Jc,{},"officerDivider"),a.jsx(vS,{children:"Officers"},"officerHeader")].concat(n.map(i=>{const o=officers.get(i.id),s=officers.stateText(i);return a.jsxs(Yg,{$out:!officers.serves(i,r),children:[a.jsxs(Kg,{children:[officers.label(i.id),": "]}),o?o.summary:"",s?" "+s:""]},`officer-${i.id}`)}))},CS=t=>{const e=Object.keys(t.critData).length>0?Object.keys(t.critData):[...new Set((t.criticals||[]).map(n=>n.phpclass))];return e.length===0?null:[a.jsx(Jc,{},"critDivider"),a.jsx(Pn,{children:"Criticals"},"criticalHeader")].concat(e.map(n=>{let r=0,i=0;var o=0,s=0,l=!1,c="";o=0,s=0,l=!1,c="";for(const u in t.criticals)t.criticals[u].phpclass==n&&t.criticals[u].turn<=gamedata.turn&&(t.criticals[u].turnend==0||t.criticals[u].turnend>=gamedata.turn)&&(r++,i+=parseInt(t.criticals[u].param,10)||0,r==1&&(o=t.criticals[u].turnend,s=t.criticals[u].turnend,l=t.criticals[u].turnend==0),t.criticals[u].turnend>0?(t.criticals[u].turnend>s&&(s=t.criticals[u].turnend),(t.criticals[u].turnend<o||o==0)&&(o=t.criticals[u].turnend)):l=!0);if(o>0&&(c=" (until end of turn "+o,l?c=c+"+":s>o&&(c=c+"-"+s),c=c+")"),r>=1&&Vg[n]){const u=Vg[n](i);return a.jsxs(N,{children:[u," ",c]},`critical-${n}`)}const d=t.critData[n]||yS[n]||n;return r>1?a.jsxs(N,{children:["(",r," x) ",d," ",c]},`critical-${n}`):r==1?a.jsxs(N,{children:[d," ",c]},`critical-${n}`):null}))},TS=(t,e)=>typeof t.adjustOffensiveBonusDisplay=="function"?t.adjustOffensiveBonusDisplay(e):e,MS=(t,e,n)=>{const r=typeof e.adjustDataValueDisplay=="function"?e.adjustDataValueDisplay(n,e.data[n]):e.data[n];return window.officers?officers.adjustDataDisplay(t,e,n,r):r},Dt=(t,e,n)=>{if(typeof e=="string"&&e.indexOf("<br>")!==-1){const r=e.split("<br>");return a.jsxs(N,{children:[a.jsxs(ee,{children:[t,": "]}),r.map((i,o)=>a.jsxs(R.Fragment,{children:[o>0&&a.jsx("br",{}),i]},o))]},n)}return a.jsxs(N,{children:[a.jsxs(ee,{children:[t,": "]}),e]},n)},Xg=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left+t.width,e};h(Pf)`
    font-size: 12px;
`;const jS=h(Df)`
    position: absolute;
    z-index: 20000;
    ${t=>Object.keys(t.position).reduce((e,n)=>e+`
`+n+":"+t.position[n]+"px;","")}
    max-width: 500px;
    text-align: left;
    opacity: ${t=>t.opacity||.8};
    border: 1px solid #496791;
    padding-bottom: 3px;
    /*the lobby mounts #systemInfoReact inside a pointer-events: none fixed overlay
      (same as #shipWindowsReact) - this menu is interactive, so it must opt back in.
      No-op in game.php, where the mount point has no pointer-events override.*/
    pointer-events: auto;
    /*Lobby: the menu inside (ApplyDamageMenu) is a framed panel of its own, so this tooltip
      draws nothing round it - no second border, padding or fill, and no element opacity.*/
    ${t=>t.$bare&&`
        padding: 0;
        border: 0;
        border-radius: 0;
        background: none;
        opacity: 1;
    `}
`;h(Af)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,h.span`
    color: white;
`;class ES extends R.Component{render(){const{ship:e,system:n,boundingBox:r}=this.props;return Vc(e,n)?a.jsx(jS,{position:DS(r),opacity:Yb(e,n)?.95:.8,$bare:gamedata.gamephase===-2,children:a.jsx(_b,{...this.props})}):null}}const DS=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e},PS={0:"Primary",1:"Forward",2:"Aft",3:"Port",4:"Starboard",5:"",31:"Port Fwd",32:"Port Aft",41:"Stbd Fwd",42:"Stbd Aft"},AS=h.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${m.colors.panelBgGlass};
    ${t=>t.$area?`grid-area: ${t.$area};`:""}
    ${t=>t.$valign?`align-self: ${t.$valign};`:""}
    ${t=>t.$justify?`justify-self: ${t.$justify};`:""}
    width: ${t=>t.$isTerrain?"125px":t.$wide?"156px":"128px"};
    ${t=>t.$minHeight?`min-height: ${t.$minHeight}px;`:""}
    /*Ship Art toggle (item 3, 2026-07-22): hide the whole section (header + icons) but
      keep its grid footprint so the window/watermark never resize while the art shows*/
    ${t=>t.$hidden?"visibility: hidden;":""}
    margin: ${t=>t.$area?"0":"2px"};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;

    border: ${t=>{switch(t.$location){case 0:return`1px solid ${m.colors.line}`;default:return`1px dotted ${m.colors.line}`}}};
`,RS=h.div`
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
    border-bottom: 1px solid ${m.colors.healthOk};
    overflow: hidden;
    /*lobby pre-battle damage: the bar is the only way to reach a section's Structure,
      which has no icon of its own in the grid*/
    ${t=>t.$damageable?"cursor: pointer;":""}

    /*structure health fill - the header line doubles as the section's health bar*/
    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: ${t=>t.$health}%;
        background-color: ${t=>t.$criticals?m.colors.healthCrit:m.colors.healthOk};
    }
`,IS=h.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${m.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
`,OS=h.span`
    position: relative;
    z-index: 1;
    font-family: ${m.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${t=>t.$destroyed?"transparent":m.colors.text};
    filter: ${t=>t.$destroyed?"blur(1px)":"none"};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,LS=h.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;class Zg extends R.Component{constructor(e){super(e),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(e){const{ship:n,systems:r}=this.props,i=ed(r);if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Gc(n)){e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("MineDamageClicked",{ship:n,element:e.currentTarget});return}!i||!Dn(n,i)||(e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:n,system:i,element:e.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.hideStructureArc()}showStructureArc(){const{ship:e,systems:n}=this.props;this.arcShown=!0,window.uiEvents.relay("StructureMouseOver",{ship:e,structure:ed(n)})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay("StructureMouseOut"))}onStructureMouseOver(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.showStructureArc())}onStructureMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(e){e.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.touches[0];this.touchStartX=n.clientX,this.touchStartY=n.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;const n=e.touches[0];(Math.abs(n.clientX-this.touchStartX)>10||Math.abs(n.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(e){e.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,this.hideStructureArc()}render(){const{ship:e,systems:n,location:r,displayLocation:i,area:o,valign:s,justify:l,wide:c,isTerrain:d,minHeight:u,nameOverride:f,hidden:g}=this.props,v=ed(n),x=Gc(e),b=x?battleDamage.mineHealth(e,1):0,M=x?battleDamage.mineMaxHealth(e):0,y=x?b/M*100:v?zS(e,v):0,p=i!==void 0?i:r,w=i!==void 0&&i!==r;return a.jsxs(AS,{$location:r,$area:o,$valign:s,$justify:l,$wide:c,$isTerrain:d,$minHeight:u,$hidden:g,children:[v&&a.jsxs(RS,{$health:y,$criticals:BS(v),$damageable:Dn(e,v)||Gc(e),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[a.jsx(IS,{children:f||PS[r]||""}),a.jsxs(OS,{$destroyed:y===0,children:[x?b:v.maxhealth-damageManager.getDamage(e,v),"/",x?M:v.maxhealth," A",shipManager.systems.getArmour(e,v)]})]}),a.jsx(LS,{children:NS(n,p,c).map(S=>a.jsx(Zc,{scs:!0,mirror:w,system:S,ship:e},`system-scs-${r}-${e.id}-${S.id}`))})]})}}const zS=(t,e)=>(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,BS=t=>shipManager.criticals.hasCriticals(t),qg=t=>t.name==="structure",ed=t=>t.find(qg),Is=t=>t.filter(e=>!qg(e)),NS=(t,e,n)=>(t=Is(t),n?td(t):[4,41,42].includes(e)?nd(t):[3,31,32].includes(e)?FS(nd(t)):[1,2,0].includes(e)?td(t):_S(t)),FS=t=>{let e=[];return t.forEach((n,r)=>{const i=r%3;i===0?e[r+2]=n:i===1?e[r]=n:e[r-2]=n}),e},_S=t=>(t=Is(t),t.length===3?nd(t):t.length===4?td(t):t),td=t=>{t=Is(t);let e=[];for(;;){const{picked:n,remaining:r}=id(t,4);if(n.length===0)break;t=r,e=e.concat(n)}for(;;){const{picked:n,remaining:r}=id(t,2);if(n.length===0)break;t=r;const i=id(t,2);i.picked.length>0?(t=i.remaining,e=e.concat([n[0],i.picked[0],i.picked[1],n[1]])):(e=e.concat([n[0],t.shift(),t.shift(),n[1]]),e=e.filter(o=>o))}return e=e.concat(t),e},nd=t=>{t=Is(t);let e=[];for(;;){const{picked:n,remaining:r}=rd(t,3);if(n.length===0)break;t=r,e=e.concat(n)}for(;;){const{picked:n,remaining:r}=rd(t,2);if(n.length===0)break;const{three:i,remainingSystems:o}=HS(n,r);t=o,e=e.concat(i)}return e=e.concat(t),e},HS=(t,e)=>{const n=rd(e,1);return n.picked.length===1?{three:[n.picked[0],t[0],t[1]],remainingSystems:n.remaining}:e.length>0?{three:[e.shift(),t[0],t[1]],remainingSystems:e}:{three:[t[0],t[1]],remainingSystems:e}},rd=(t,e=3)=>{const n=t.find(o=>{const s=t.reduce((l,c)=>c.name===o.name?l+1:l,0);return e===1?s===e:s>=e});if(!n)return{picked:[],remaining:t};let r=[];const i=t.filter(o=>o.name===n.name&&e>0?(e--,r.push(o),!1):!0);return{picked:r,remaining:i}},id=(t,e=3)=>{const n=t.find(d=>{const u=t.reduce((f,g)=>g.name===d.name?f+1:f,0);return e===1?u===e:u>=e});if(!n)return{picked:[],remaining:t};let r=[],i=[];const o=t.filter(d=>d.name===n.name?(i.push(d),!1):!0);for(var s=Math.ceil(e/2),l=Math.floor(e/2),c=0;c<i.length;c++)c<s||c>=i.length-l?r.push(i[c]):o.unshift(i[c]);return{picked:r,remaining:o}},Jg={DEW:"#aecdea",CCEW:m.colors.text,SDEW:"#9ac1e5",OEW:"#acd7a8",BDEW:"#8ac785","Detect Mines":"#bfa3db","Detect Stealth":"#ccb6e2",DIST:"#e6b98f",SOEW:m.colors.text,OEW_HOSTILE:"#e49b9b","Saved EW":"#e0d39a",ELINT:"#9ad4cf"},Pt=(t,e)=>t==="OEW"&&e&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(e)?Jg.OEW_HOSTILE:Jg[t]||m.colors.textAccent,em=h.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${t=>t.$flight?"":br`
        grid-area: ew;
        justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    `}
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${m.colors.panelBgGlass};
    border: 1px solid ${m.colors.line};
    padding: 1px 4px 1px;
`,tm=h.div`
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
    color: ${m.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${m.colors.line};
`,At=h.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${m.colors.text};
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
    ${t=>t.$target&&br`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${t=>t.$hoverable&&br`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,WS=h.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,Rt=h.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${t=>t.$color||m.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,It=h.span`
    font-family: ${m.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,US=h.span`
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
    color: ${m.colors.textAccent};
    ${t=>t.$interactive&&br`
        cursor: pointer;
        &:hover {
            color: ${m.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;class nm extends R.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(e,n){window.webglScene&&(window.uiEvents.relay("EwRangeHover",{shipId:this.props.ship.id,type:e,active:n}),this.activeRangeOverlay=n?e:null)}onTargetClick(e,n){n.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(e)||window.uiEvents.relay("ScrollToShip",{shipId:e.id}))}setTargetHighlight(e,n,r){window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:e.id,type:n,active:r}),this.activeHighlight=r?{targetId:e.id,type:n}:null)}render(){const{ship:e,flight:n}=this.props;return n?a.jsxs(em,{$flight:!0,children:[a.jsx(tm,{children:"Electronic Warfare"}),GS(e),im(e,this)]}):a.jsxs(em,{children:[a.jsx(tm,{children:"Electronic Warfare"}),VS(e,this),im(e,this)]})}}const GS=t=>{const e=[a.jsxs(At,{children:[a.jsx(Rt,{$color:Pt("DEW"),children:"DEW"}),a.jsx(It,{children:ce(ew.getFlightDEW(t))})]},`dew-scs-${t.id}`)],n=rm(t);return n&&e.push(n),e},rm=t=>{if(!gamedata.isPlayerInGame()||!gamedata.isMyorMyTeamShip(t))return null;const e=ew.getSavedEwAllowance(t);if(e<=0)return null;const r=ew.isLateEwWindowOpen(t)||ew.isLateEwPhase()&&gamedata.isMyShip(t)?`${ce(ew.getLateEwRemaining(t))} / ${ce(e)}`:ce(e);return a.jsxs(At,{children:[a.jsx(Rt,{$color:Pt("Saved EW"),children:"Saved EW"}),a.jsx(It,{children:r})]},`savedew-scs-${t.id}`)},VS=(t,e)=>{let n=[];const r=!!window.webglScene,i=u=>r?{$hoverable:!0,onMouseEnter:()=>e.setRangeOverlay(u,!0),onMouseLeave:()=>e.setRangeOverlay(u,!1)}:{};if(n.push(a.jsxs(At,{children:[a.jsx(Rt,{$color:Pt("DEW"),children:"DEW"}),a.jsx(It,{children:ce(ew.getDefensiveEW(t))})]},`dew-scs-${t.id}`)),r&&ew.hasElintModules(t)&&gamedata.isMyorMyTeamShip(t)){const u=ew.getElintModuleSplit(t);let f=0;ew.getElintModules(t).forEach(v=>{f+=v.elintLost||0});const g=gamedata.gamephase==1?`${ce(u.freeM)} / ${ce(u.M)}`:f>0?`${ce(u.M)} (lost ${f})`:ce(u.M);n.push(a.jsxs(At,{children:[a.jsx(Rt,{$color:Pt("ELINT"),children:"ELINT Modules"}),a.jsx(It,{children:g})]},`elint-scs-${t.id}`))}var o=Math.max(0,ew.getCCEW(t)-ew.getDistruptionEW(t));o>0&&n.push(a.jsxs(At,{children:[a.jsx(Rt,{$color:Pt("CCEW"),children:"CCEW"}),a.jsx(It,{children:ce(o)})]},`ccew-scs-${t.id}`));let s=ew.getBDEW(t)*.25,l=ew.getDetectSEW(t),c=ew.getDetectMEW(t);shipManager.hasSpecialAbility(t,"ConstrainedEW")&&(s=ew.getBDEW(t)*.2),s&&n.push(a.jsxs(At,{...i("BDEW"),children:[a.jsx(Rt,{$color:Pt("BDEW"),children:"BDEW"}),a.jsx(It,{children:ce(s)})]},`bdew-scs-${t.id}`)),c&&n.push(a.jsxs(At,{...i("MDEW"),children:[a.jsx(Rt,{$color:Pt("Detect Mines"),children:"Detect Mines"}),a.jsx(It,{children:ce(c)})]},`DetectMEW-scs-${t.id}`)),l&&n.push(a.jsxs(At,{children:[a.jsx(Rt,{$color:Pt("Detect Stealth"),children:"Detect Stealth"}),a.jsx(It,{children:ce(l)})]},`DetectSEW-scs-${t.id}`));const d=rm(t);return d&&n.push(d),n},im=(t,e)=>{const n=!!window.webglScene;return t.EW.filter(r=>r.turn===gamedata.turn).filter(r=>r.type==="OEW"||r.type==="DIST"||r.type==="SOEW"||r.type==="SDEW").map(r=>{const i=gamedata.getShip(r.targetid);return a.jsxs(At,{$target:!0,children:[a.jsxs(WS,{children:[a.jsx(Rt,{$color:Pt(r.type,t),children:r.type}),a.jsx(US,{$interactive:n,title:void 0,onClick:n?e.onTargetClick.bind(e,i):void 0,onMouseEnter:n?()=>e.setTargetHighlight(i,r.type,!0):void 0,onMouseLeave:n?()=>e.setTargetHighlight(i,r.type,!1):void 0,children:i.name})]}),a.jsx(It,{children:YS(r,t)})]},`${r.type}-scs-${t.id}-${r.targetid}`)})},YS=(t,e)=>{switch(t.type){case"SDEW":if(shipManager.hasSpecialAbility(e,"ConstrainedEW")){let n=t.amount*.333;return n=Math.round(n*3)/3,ce(n)}else return ce(t.amount*.5);case"DIST":return shipManager.hasSpecialAbility(e,"ConstrainedEW")?ce(t.amount/4):ce(t.amount/3);case"OEW":return ce(Math.max(0,t.amount-ew.getDistruptionEW(e)));default:return ce(t.amount)}},ce=t=>Math.round(t*100)/100,Os=()=>!!window.gamedata&&window.gamedata.gamephase===-2,KS=h.div`
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
`,QS=h.div`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    background-image: url(${t=>t.$img});
    background-size: 80%;
    background-position: center;
    background-repeat: no-repeat;
    filter: ${t=>t.$destroyed?"blur(1px)":"none"};
    opacity: ${t=>t.$destroyed?"0.5":"1"};
`,Ri=h.div`
    position: absolute;
    top: 55px;
    left: 9px;
    width: 96px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    padding: 5px 0;
    background-color: black;
    color: ${t=>t.$color};
    border: 1px solid ${t=>t.$color};
    z-index: 2;
    pointer-events: none;
    opacity: 0.7;
`,XS=h.div`
    position: absolute;
    top: -5px;
    left: -5px;
    z-index: 3;
    pointer-events: none;
`,om=h.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,ZS=h(om)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,qS=h.div`
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: calc(100% - 4px);
    height: 16px;
    box-sizing: border-box;
    background-color: black;
    cursor: ${t=>t.$clickable?"pointer":"default"};
    color: ${t=>t.$health===0?"transparent":"white"};
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
        width:  ${t=>t.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        z-index: 0;
        background-color: ${t=>t.$docked?"#00b8e6":t.$criticals?t.$criticalsBenign?"#00ccff":"#ed6738":"#427231"};
        border: 1px solid black;
    }
`,JS=h.div`
    z-index: 1;
`;class ek extends R.Component{onSystemMouseOver(e){if(Os()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||e.nativeEvent&&e.nativeEvent.sourceCapabilities&&e.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:n}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:n,system:n,element:e.target})}onSystemMouseOut(){Os()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onFighterTouchStart(e){if(Os())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:i}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:i,system:i,element:n,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(e){if(!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(e){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onFighterTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){const{ship:e}=this.props,n=window.gamedata&&typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted();return Os()&&!n&&!!e&&e.userid!=0&&!!e.flight}onHealthBarClick(e){this.canApplyPreBattleDamage()&&(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay("FighterDamageClicked",{ship:this.props.ship,fighter:this.props.fighter,element:e.currentTarget}))}render(){const{ship:e,fighter:n}=this.props,r=shipManager.systems.isDestroyed(e,n),i=shipManager.criticals.isDockedFighter(n),o=!i&&shipManager.criticals.isSplitLaunchedFighter(n),s=!i&&!o&&shipManager.criticals.isDisengagedFighter(n),l=shipManager.criticals.isCutOffFighter(n);let c=null;r?i?c=a.jsx(Ri,{$color:"#00b8e6",children:"DOCKED"}):o?c=a.jsx(Ri,{$color:"#00b8e6",children:"SPLIT"}):s?c=a.jsx(Ri,{$color:"#ff8c00",children:"DROPOUT"}):c=a.jsx(Ri,{$color:"#ff5252",children:"DESTROYED"}):l&&(c=a.jsx(Ri,{$color:"#ff5252",children:"CUT OFF"}));const d=this.canApplyPreBattleDamage(),u=d?battleDamage.fighterHealth(e,1)/n.maxhealth*100:tk(e,n),f=d?`${battleDamage.fighterHealth(e,1)} / ${n.maxhealth}`:`${n.maxhealth-damageManager.getDamage(e,n)} / ${n.maxhealth}`,g=window.officers?officers.postState(e,n.id):null;return a.jsxs(KS,{$docked:i,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[a.jsxs(QS,{$destroyed:r,$img:window.AssetManager.getSmartImagePath(n.iconPath),children:[a.jsx(om,{children:sm(e,n,ik(n),r)}),a.jsx(ZS,{children:sm(e,n,ok(n),r)}),a.jsx(qS,{$health:u,$criticals:nk(n),$criticalsBenign:rk(n),$docked:i,$clickable:d,title:d?"Apply pre-battle damage to this flight":void 0,onClick:this.onHealthBarClick.bind(this),children:a.jsx(JS,{children:f})})]}),c,g&&a.jsx(XS,{children:a.jsx(Lg,{state:g,title:officers.describePost(e,n.id)})})]})}}const tk=(t,e)=>(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,nk=t=>shipManager.criticals.hasCriticals(t),rk=t=>shipManager.criticals.hasOnlyCritical(t,"LaunchedThisTurn",!1),ik=t=>t.systems.filter(e=>e.location==1),ok=t=>t.systems.filter(e=>e.location!=1),sm=(t,e,n,r)=>n.map((i,o)=>a.jsx(Zc,{$destroyed:r,fighter:!0,scs:!0,system:i,ship:t},`system-scs-fighter${e.id}-${t.id}-${i.id}-${o}`)),sk=h.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;class od extends R.Component{render(){const{ship:e}=this.props;return a.jsx(sk,{children:ak(e)})}}const ak=t=>t.systems.map((e,n)=>a.jsx(ek,{fighter:e,ship:t},`flight-${t.id}-${n}`)),lk=[3,31,32],ck=[1,0,2],dk=[4,41,42],uk=h.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,sd=h.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${t=>t.$side?"center":"flex-start"};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,hk=h.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${m.colors.line};
    padding: 3px 5px;
`,pk=h.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${m.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${m.colors.line};
`,fk=h.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${m.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`,gk=h.span`
    font-family: ${m.fonts.mono};
    color: ${m.colors.text};
    flex-shrink: 0;
`,mk=(t,e)=>a.jsxs(hk,{children:[a.jsx(pk,{children:e.name}),[...e.entries].sort((n,r)=>r.chance-n.chance).map((n,r)=>a.jsxs(fk,{children:[a.jsx("span",{children:n.name}),a.jsxs(gk,{children:[n.chance,"%"]})]},`hitchart-${t.id}-${e.location}-${r}`))]},`hitchart-${t.id}-${e.location}`);class xk extends R.Component{render(){const{ship:e}=this.props,n=Wg(e);if(n.length===0)return null;const r={};n.forEach(c=>{r[c.location]=c});const i=c=>c.filter(d=>r[d]).map(d=>mk(e,r[d])),o=i(lk),s=i(ck),l=i(dk);return a.jsxs(uk,{children:[o.length>0&&a.jsx(sd,{$side:!0,children:o}),s.length>0&&a.jsx(sd,{children:s}),l.length>0&&a.jsx(sd,{$side:!0,children:l})]})}}const yk=t=>t.split(" ").map(e=>e.charAt(0).toUpperCase()+e.slice(1)).join(" "),wk=t=>{const e=[];if(!t||t.flight)return e;const n=t.fighters||{};if(Object.keys(n).length>0){const i={};if(shipManager.systems.shipHasRestrictedHangar(t)){const o=shipManager.systems.getReservedFighterComposition(t);for(let s=0;s<o.length;s++){const l=o[s].category;i[l]||(i[l]=[]);const c=i[l];let d=!1;for(let u=0;u<c.length;u++)if(c[u].phpclass===o[s].phpclass){c[u].count+=o[s].count,d=!0;break}d||c.push({category:o[s].category,phpclass:o[s].phpclass,displayName:o[s].displayName,count:o[s].count,isGroup:o[s].isGroup})}}for(const o in n){const s=n[o],l=yk(o),c=i[o];if(c&&(o==="heavy"||o==="medium"||o==="light")){let d=s;for(let u=0;u<c.length;u++){const f=Math.min(c[u].count,d);f<=0||(c[u].isGroup?e.push(f+" "+c[u].displayName+"s"):e.push(f+" "+c[u].displayName+" "+l+" Fighters"),d-=f)}d>0&&e.push(d+" "+l+" Fighters");continue}if(o==="normal")e.push(s+" Fighters");else if(o==="superheavy"||o==="heavy"||o==="medium"||o==="light"||o==="ultralight")e.push(s+" "+l+" Fighters");else{if(o==="shuttles"||o==="minesweeping shuttles"||o==="cargo shuttles"||o==="lifeboats"||o==="medical shuttles"||o==="presidential shuttle"||o==="yacht")continue;e.push(s+" "+l)}}}const r=shipManager.systems.getDefaultShuttleComposition(t);for(let i=0;i<r.length;i++)e.push(r[i].count+" "+r[i].type);return e},vk=h.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${m.colors.textAccent};
    ${t=>t.$grid?`
    grid-area: ew;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px;`:t.$full?`
    width: 100%;
    padding: 4px;`:`
    flex: 0 0 auto;
    width: 200px;
    padding: 4px;`}
`,Ii=h.div`
    background-color: ${m.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${t=>t.$gold?m.colors.enhLine:m.colors.line};
    padding: 0 8px 3px;
    /*$gold: the enhancement lines in the gold body text on every window (user request 2026-10-11).
      The grid's EnhancementsPanel got it from its EnhArea wrapper, but a flight's block sits in
      the blue Rail and read blue; set here, both read the same.*/
    ${t=>t.$gold?`color: ${m.colors.enhText};`:""}
`,ad=h.div`
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
    color: ${t=>t.$gold?m.colors.enhTitle:m.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${t=>t.$gold?m.colors.enhBg:"rgba(73, 103, 145, 0.25)"};
    border-bottom: 1px solid ${t=>t.$gold?m.colors.enhLine:m.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,An=h.div`
    padding: 1px 0;
`,bk=h.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${m.colors.custom};
`,Re=h.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,Ie=h.span`
    font-size: 10px;
    color: ${m.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,Oe=h.span`
    font-family: ${m.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${t=>t.$changed?m.colors.custom:m.colors.text};
    margin-right: 5px;
`,Sk=h.div`
    width: 150px;
    box-sizing: border-box;
    ${t=>t.$bare?`
    padding: 0;`:`
    background-color: ${m.colors.panelBgGlass};
    border: 1px dotted ${m.colors.line};
    padding: 2px 4px 3px;`}
`,kk=h.div`
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
    color: ${m.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${m.colors.line};
`,am=h.span`
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
        background-color: ${m.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;h.div`
    text-align: center;
    font-size: 10px;
    color: ${m.colors.warning};
    padding-top: 2px;
`;const lm=h.div`
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
    color: ${m.colors.enhTitle};
    background-color: ${m.colors.enhBg};
    border-bottom: 1px solid ${m.colors.enhLine};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,$k=h.div`
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
    color: ${m.colors.enhText};
`,ld=t=>typeof t=="number"?t.toFixed(2):t,cm=(t,e)=>window.officers?officers.displayStat(t,e):t[e],cd=(t,e)=>t*5+"/"+e*5,Ls=(t,e)=>t+" ("+ld(e)+")",Ck=t=>{const e=window.shipManager;if(!e)return null;const n={},r=e.systems?e.systems.getSystemByName(t,"CnC"):null,i=r&&e.criticals?e.criticals.hasCritical(r,"ProfileIncreased"):0;n.profile=cd(t.forwardDefense+i,t.sideDefense+i),n.profileChanged=i!==0;const o=t.iniativeadded||0;n.initiative=(t.iniativebonus||0)+o,n.initiativeChanged=o!==0;const s=e.movement;if(s&&typeof s.getTurnCost=="function"&&t.movement&&t.movement.length>0){const l=s.getSpeed(t),c=s.getDockedLcvTurnSurcharge(t),d=s.getTurnDelayCost(t);let u=s.getTurnCost(t);t.submarine&&s.isGoingBackwards(t)&&(u=u*1.33);const f=Math.max(1,Math.ceil(l*u))+c,g=s.applyCrewTurnDelay(t,Math.ceil(l*d))+c;n.turnCost=Ls(f,u),n.turnDelay=Ls(g,d),n.turnCostChanged=n.turnCost!==Ls(Math.max(1,Math.ceil(l*t.turncost)),t.turncost),n.turnDelayChanged=n.turnDelay!==Ls(Math.ceil(l*t.turndelaycost),t.turndelaycost)}return n},dm=({ship:t,live:e,bare:n})=>{const r=!t.base,i=e?Ck(t):null;return a.jsxs(Sk,{$bare:n,children:[!n&&a.jsxs(kk,{children:[a.jsxs(am,{children:[a.jsx("i",{}),a.jsx("i",{}),a.jsx("i",{})]}),"Ship Stats"]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn cost"}),a.jsx(Oe,{$changed:!!(i&&i.turnCostChanged),children:i&&i.turnCost?i.turnCost:ld(t.turncost)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn delay"}),a.jsx(Oe,{$changed:!!(i&&i.turnDelayChanged),children:i&&i.turnDelay?i.turnDelay:ld(t.turndelaycost)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Accel/decel"}),a.jsx(Oe,{children:t.accelcost})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Pivot"}),a.jsx(Oe,{children:t.pivotcost})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Roll"}),a.jsx(Oe,{children:t.rollcost})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Profile - Front / Side"}),a.jsx(Oe,{$changed:!!(i&&i.profileChanged),children:i?i.profile:window.officers?cd(officers.displayStat(t,"forwardDefense"),officers.displayStat(t,"sideDefense")):cd(t.forwardDefense,t.sideDefense)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Initiative"}),a.jsx(Oe,{$changed:!!(i&&i.initiativeChanged),children:i?i.initiative:window.officers?officers.displayIniativeBonus(t):t.iniativebonus})]})]})},Tk=({ship:t})=>{const e=ud(t.enhancementTooltip),n=dd(t);return e.length===0&&!n?null:a.jsx($k,{children:a.jsxs(Ii,{$gold:!0,children:[a.jsx(lm,{children:"Enhancements"}),e.map((r,i)=>a.jsx(An,{children:r},`enh-${i}`)),n&&a.jsx(An,{children:n},"officers")]})})},dd=t=>window.officers&&t?officers.summaryLine(t):null,ud=t=>(t||"").split(/<br\s*\/?>/i).map(e=>e.replace(/<[^>]*>/g,"").replace(/&nbsp;/g," ").trim()).filter(Boolean);class hd extends R.Component{render(){const{ship:e,full:n,grid:r,hideEnhancements:i}=this.props,o=wk(e),s=ud(e.notes),l=i?[]:ud(e.enhancementTooltip),c=i?null:dd(e),d=[];if(e.limited&&e.limited!=0&&d.push("Limited: "+e.limited+"%"),e.variantOf){const g=e.occurence?e.occurence.charAt(0).toUpperCase()+e.occurence.slice(1)+" ":"";d.push(g+"variant of "+e.variantOf)}e.isd&&d.push("In-Service (ISD): "+e.isd);let u=null;e.unofficial==="S"?u="Semi-Custom":e.unofficial&&(u="Custom");const f=s.length>0||d.length>0||u;return a.jsxs(vk,{$full:n,$grid:r,children:[e.flight&&a.jsxs(Ii,{children:[a.jsx(ad,{children:"Flight Stats"}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Armor F/S/A"}),a.jsx(Oe,{children:shipManager.systems.getFlightArmour(e)})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Offensive bonus"}),a.jsx(Oe,{children:cm(e,"offensivebonus")*5})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Profile - Front / Side"}),a.jsxs(Oe,{children:[e.forwardDefense*5,"/",e.sideDefense*5]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Thrust"}),a.jsx(Oe,{children:e.freethrust})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn Cost / Delay"}),a.jsxs(Oe,{children:[e.turncost," / ",e.turndelaycost==0?0:e.turndelaycost]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Accel. / Pivot / Roll"}),a.jsxs(Oe,{children:[e.accelcost," / ",e.pivotcost," / ",e.rollcost]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Initiative"}),a.jsx(Oe,{children:cm(e,"iniativebonus")})]})]}),o.length>0&&a.jsxs(Ii,{children:[a.jsx(ad,{children:"Hangar Capacity"}),o.map((g,v)=>a.jsx(An,{children:g},`comp-${v}`))]}),f&&a.jsxs(Ii,{children:[a.jsx(ad,{children:"Notes"}),s.map((g,v)=>a.jsx(An,{children:g},`note-${v}`)),d.map((g,v)=>a.jsx(An,{children:g},`meta-${v}`)),u&&a.jsx(bk,{children:u})]}),(l.length>0||c)&&a.jsxs(Ii,{$gold:!0,children:[a.jsx(lm,{children:"Enhancements"}),l.map((g,v)=>a.jsx(An,{children:g},`enh-${v}`)),c&&a.jsx(An,{children:c},"officers")]})]})}}const Mk=400,jk=h.div`
    display: flex;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 4px 0 0;
`,Ek=h.div`
    flex: 0 1 auto;
    min-width: 0;
    width: max-content;
    max-width: ${Mk}px;
`,Oi=h.div`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${t=>t.$isMyTeam?`left: 50px; 
 top: 50px;`:`right: 50px; 
 top: 50px;`}
    width: ${t=>t.$variant==="terrain"?"250px":t.$variant==="flight"||t.$variant==="flightEw"?"auto":"fit-content"};
    max-width: ${t=>t.$variant==="flight"?"400px":t.$variant==="flightEw"?"574px":t.$variant==="flightLobby"?"620px":"unset"};
    height: auto;
    border: 1px solid ${m.colors.line};
    background-color: ${m.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${m.colors.text};
    font-family: ${m.fonts.body};

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
        ${t=>t.$isMyTeam?`left: 4px; 
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
        width: ${t=>t.$variant==="terrain"?"250px":"max-content"};
        max-width: ${t=>t.$variant==="flight"?"400px":t.$variant==="flightEw"?"574px":t.$variant==="flightLobby"?"620px":"none"};
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
`,Dk=h.div`
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
    background-color: ${m.colors.panelBg};
    border-bottom: 1px solid ${m.colors.line};
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
`,Pk=h.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${t=>t.$tint||m.colors.text};
`,Ak=h.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${m.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,Rk=h.div`
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
    color: ${m.colors.line};
    ${We}
`,Ik=h.div`
    position: sticky;
    bottom: 0;
    align-self: ${t=>t.$mirror?"flex-start":"flex-end"};
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    /*$overlap: draw the mark ON TOP of the row above (a status banner) instead of taking a
      row of its own - the negative margin is exactly the grip's own height, so the row above
      keeps the window's bottom edge. See renderStatusStrip.*/
    ${t=>t.$overlap?"margin-top: -16px;":""}
    box-sizing: border-box;
    z-index: 5;
    cursor: ${t=>t.$mirror?"nesw-resize":"nwse-resize"};
    /*the grip owns the gesture: without this a finger drag scrolls the window/page instead*/
    touch-action: none;

    &::after { /*three diagonal rules clipped to the corner triangle - the standard grip mark*/
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(${t=>t.$mirror?"45deg":"315deg"}, ${m.colors.line} 0 1.5px, transparent 1.5px 4px);
        clip-path: ${t=>t.$mirror?"polygon(0 0, 0 100%, 100% 100%)":"polygon(100% 0, 100% 100%, 0 100%)"};
        opacity: 0.85;
    }

    &::before { /*finger pad - see the note above*/
        content: "";
        position: absolute;
        top: -${t=>t.$pad}px;
        bottom: 0;
        left: ${t=>t.$mirror?"0":"-"+t.$pad+"px"};
        right: ${t=>t.$mirror?"-"+t.$pad+"px":"0"};
    }

    &:hover::after {
        opacity: 1;
    }
`,Ok=h.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${t=>t.$compact?"width: 100%; align-items: center; margin-bottom: 5px;":"align-items: stretch;"}
    gap: 4px;
`,zs=h.div`
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
    min-width: ${t=>t.$wide?"150px":"130px"};
    border: 1px solid ${m.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${t=>t.$active?"rgba(198, 226, 255, 0.12)":"rgba(73, 103, 145, 0.25)"};
    color: ${m.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${We}
`,um=h.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,Lk=h.span`
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
`,pd=h.div`
    position: absolute;
    top: ${t=>t.$top||78}px;
    /*left edge aligns with the control buttons (measured - they sit centred in the
      grid's ctrl column, not at the window's 6px margin); feedback 2026-07-19*/
    left: ${t=>t.$left!=null?t.$left:6}px;
    /*$fit (Notes): size to content instead of spanning the window*/
    ${t=>t.$fit?"right: auto; width: fit-content; max-width: calc(100% - 12px);":"right: 6px;"}
    /*Notes popup never narrower than the 130px Notes button it drops from (both
      border-box), so short notes still read as one block under the button*/
    ${t=>t.$notes?"min-width: 130px;":""}
    z-index: 20;
    max-height: 70vh;
    overflow-y: auto;
    box-sizing: border-box;
    background-color: ${m.colors.panelBg};
    border: 1px solid ${m.colors.line};
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.7);
    /*Notes ($notes) trims the bottom gap to ~5px (feedback 2026-07-19; paired with
      ShipInfo dropping its trailing blank line); Hit Chart keeps the extra bottom
      padding so the last chart rows never look clipped*/
    padding: 5px 5px ${t=>t.$notes?"5px":"10px"};
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
`,zk=h.div`
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    grid-template-areas: ${t=>t.$areas};
    justify-content: center;
    gap: 8px;
    padding: 5px 5px 5px 5px;
    box-sizing: border-box;
    width: 100%;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`,Bk=h.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,Nk=h.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,fd=h.div`
    position: absolute;
    top: 50%;
    left: 50%;
    height: 80%;
    width: auto;
    aspect-ratio: 1 / 1;
    max-width: 80%;
    max-height: 380px;
    transform: translate(-50%, calc(-50% + ${t=>t.$offsetY||0}px)) rotate(-90deg);
    background-image: ${t=>`url(${t.$img})`};
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    filter: ${t=>t.$art?"none":"grayscale(1) brightness(2.2)"};
    opacity: ${t=>t.$art?1:.75};
    pointer-events: none;
    z-index: 0;
`,gd=h.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,Fk=h.div`
    width: 100%;
    box-sizing: border-box;
    /*equal top/bottom: the old 2px-top/3px-bottom made the text sit visibly high in
      the tinted strip (the border-top reads as a separator line, not banner fill,
      so it doesn't compensate). At 9px uppercase every half-pixel shows.

      $grip: the resize grip is drawn in this banner's corner (renderStatusStrip), so the
      16px mark plus the usual 6px gutter is reserved - on BOTH sides, because a one-sided
      reserve would push the centred text off the window's midline, and which corner the
      grip sits on follows the dock.*/
    padding: 3px ${t=>t.$grip?"22px":"6px"};
    text-align: center;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: ${t=>t.$color||m.colors.warning};
    background-color: ${t=>t.$bg||"rgba(225, 176, 0, 0.10)"};
    border-top: 1px solid ${m.colors.line};
    flex-shrink: 0;
`,hm=h.div`
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
`,_k=h.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${m.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${m.fonts.body};
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
`,Hk={1:"fwd",2:"aft",0:"prim",3:"left",4:"right",31:"lfwd",41:"rfwd",32:"laft",42:"raft"},pm={3:4,4:3,31:41,41:31,32:42,42:32},Wk={fwd:"end",aft:"center",prim:"center",left:"center",right:"center",lfwd:"start",rfwd:"start",laft:"end",raft:"end"},Uk={left:"end",lfwd:"end",laft:"end",right:"start",rfwd:"start",raft:"start"},Gk=[1,3,31,32,0,4,41,42,2],Vk=[1,3,31,0,4,41,32,2,42],Yk=[31,32,41,42],Kk=[3,4,31,41,32,42];class Qk extends R.Component{constructor(e){super(e),this.elementRef=R.createRef(),this.controlsRef=R.createRef(),this.popupRef=R.createRef(),this.hitChartBtnRef=R.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return Fs(this.props.ship)?"left":"right"}isMirroredGrip(){return this.side()==="right"}applyScreenFit(){const e=this.elementRef.current;if(!e)return;const n=gm(),r=Ns(this.side());if(!n&&r===1){(this.screenFit!==1||this.screenFitHeight)&&(e.style.transform="",e.style.transformOrigin="",e.style.maxHeight="",this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}const i=this.measureNatural();if(!i)return;const o=n?n$():null,s=window.innerHeight||document.documentElement.clientHeight;let l=1;if(n){const u=(document.documentElement.clientWidth||window.innerWidth)*o.fillW,f=s*o.fillH;l=Math.min(u/i.width,f/i.height),l=Math.min(o.max,Math.max(o.min,l))}this.autoFit=l;let c=yd(l*r);c=Math.round(c*100)/100;let d=null;if(n){const u=Math.min(md,o.fillH*Math.max(1,r));d=Math.round(s*u/c)}c===this.screenFit&&d===this.screenFitHeight||(this.screenFit=c,this.screenFitHeight=d,e.style.transformOrigin=this.transformOrigin(),e.style.transform=c===1?"":"scale("+c+")",e.style.maxHeight=d==null?"":d+"px",this.resizeStart||this.keepGripOnScreen())}measureNatural(){const e=this.elementRef.current;if(!e)return null;const n=e.style.maxHeight;e.style.maxHeight="none";const r=e.offsetWidth,i=e.offsetHeight;return e.style.maxHeight=n,r&&i?{width:r,height:i}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:Fs(this.props.ship)?"top left":"top right"}onScreenResize(){this.applyScreenFit()}isDragHandle(e){return!e||!e.closest||e.closest(".shipwindow-nodrag")?!1:!!e.closest(".shipwindow-drag-handle")}isResizeHandle(e){return!!(e&&e.closest&&e.closest(".shipwindow-resize-grip"))}isDragSlop(e,n){if(!e||!e.closest||!e.closest(".shipwindow-grab-slop"))return!1;const r=this.elementRef.current,i=r&&r.querySelector(".shipwindow-drag-handle");if(!i)return!1;const o=i.getBoundingClientRect();return n>=o.top&&n<=o.bottom+s$}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(e,n,r){const i=Date.now(),o=!!this.lastPress&&this.lastPress.kind===e&&i-this.lastPress.time<a$;this.lastPress={kind:e,time:i},this.pressPoint={x:n,y:r},this.pendingReset=o}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(e,n){const r=this.elementRef.current;if(!r)return!1;const i=window.getComputedStyle(r);let o=parseFloat(i.left),s=parseFloat(i.top);return isFinite(o)||(o=r.offsetLeft),isFinite(s)||(s=r.offsetTop),this.dragStart={x:e,y:n,left:o,top:s},this.positioned=!0,r.style.left=o+"px",r.style.top=s+"px",r.style.right="auto",!0}moveDrag(e,n){const r=this.elementRef.current;!this.dragStart||!r||(r.style.left=this.dragStart.left+(e-this.dragStart.x)+"px",r.style.top=this.dragStart.top+(n-this.dragStart.y)+"px",this.clampIntoView())}beginResize(e,n){const r=this.elementRef.current;if(!r)return!1;const i=this.measureNatural();if(!i)return!1;const o=window.getComputedStyle(r);let s=parseFloat(o.left),l=parseFloat(o.top);isFinite(s)||(s=r.offsetLeft),isFinite(l)||(l=r.offsetTop);const c=this.screenFit||1,d=this.isMirroredGrip(),u=d?"top right":"top left";!d&&this.transformOrigin()==="top right"&&(s+=i.width*(1-c)),r.style.left=s+"px",r.style.top=l+"px",r.style.right="auto",r.style.transformOrigin=u,this.resizeOrigin=u,this.positioned=!0;const f=r.getBoundingClientRect(),g=d?f.right:f.left,v=d?-1:1;return this.resizeStart={originX:g,originY:f.top,flipX:v,width:i.width,height:i.height,scale:c,maxScale:d?g/i.width:1/0,base:xm(i.width,i.height,v*(e-g),n-f.top)},!0}moveResize(e,n){const r=this.resizeStart;if(!r)return;const i=xm(r.width,r.height,r.flipX*(e-r.originX),n-r.originY),o=yd(Math.min(r.maxScale,r.scale+(i-r.base))),s=this.side(),l=Math.round(o/(this.autoFit||1)*100)/100;l!==Ns(s)&&(ym(s,l),this.applyScreenFit())}moveGesture(e,n){this.pressPoint&&(Math.abs(e-this.pressPoint.x)>mm||Math.abs(n-this.pressPoint.y)>mm)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(e,n):this.moveDrag(e,n)}finishGesture(){const e=this.elementRef.current,n=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),n){const r=this.side();wm(r,Ns(r)),this.keepGripOnScreen(),this.clampIntoView()}e&&(fm[this.side()]={top:parseFloat(e.style.top)||0,left:parseFloat(e.style.left)||0})}clampIntoView(e){const n=this.elementRef.current;if(!n||!this.positioned)return;const r=n.getBoundingClientRect(),i=document.documentElement.clientWidth||window.innerWidth,o=window.innerHeight||document.documentElement.clientHeight;let s=0,l=0;r.top<0?l=-r.top:r.top>o-Tr&&(l=o-Tr-r.top),e&&r.width<=i&&r.left<0?s=-r.left:r.right<Tr?s=Tr-r.right:r.left>i-Tr&&(s=i-Tr-r.left),!(!s&&!l)&&(n.style.left=(parseFloat(n.style.left)||0)+s+"px",n.style.top=(parseFloat(n.style.top)||0)+l+"px")}keepGripOnScreen(){const e=this.elementRef.current;if(!e)return;const n=e.getBoundingClientRect(),r=document.documentElement.clientWidth||window.innerWidth;let i;if(this.isMirroredGrip()){if(n.left>=0||(i=Math.min(-n.left,r-n.right),i<=0))return}else if(i=r-n.right,i>=0)return;const o=window.getComputedStyle(e);let s=parseFloat(o.left);isFinite(s)||(s=e.offsetLeft),e.style.left=s+i+"px",e.style.right="auto",this.positioned=!0}resetUserScale(){const e=this.side();Ns(e)!==1&&(ym(e,1),wm(e,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(e){e.stopPropagation(),this.resetUserScale()}onDragStart(e){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] pointerdown",e.pointerType,"handle:",this.isDragHandle(e.target),"grip:",this.isResizeHandle(e.target)),e.pointerType==="touch"||e.button!=null&&e.button>0)return;const n=this.isResizeHandle(e.target);if(!n&&!this.isDragHandle(e.target))return;if(this.notePress(n?"grip":"header",e.clientX,e.clientY),!(n?this.beginResize(e.clientX,e.clientY):this.beginDrag(e.clientX,e.clientY))){this.cancelDoublePress();return}const r=this.elementRef.current;let i=!1;try{r.setPointerCapture(e.pointerId),i=!0}catch{}this.dragTarget=i?r:document,this.dragTarget.addEventListener("pointermove",this.onDragMove),this.dragTarget.addEventListener("pointerup",this.onDragEnd),this.dragTarget.addEventListener("pointercancel",this.onDragEnd),this.dragPointerId=e.pointerId,e.preventDefault()}onDragMove(e){!this.gestureActive()||e.pointerId!==this.dragPointerId||this.moveGesture(e.clientX,e.clientY)}onDragEnd(e){!this.gestureActive()||e&&e.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(e){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] touchstart",e.touches&&e.touches.length,"handle:",this.isDragHandle(e.target),"grip:",this.isResizeHandle(e.target)),this.gestureActive()||!e.touches||e.touches.length!==1)return;const n=e.touches[0],r=this.isResizeHandle(e.target);if(!(!r&&!this.isDragHandle(e.target)&&!this.isDragSlop(e.target,n.clientY))){if(this.notePress(r?"grip":"header",n.clientX,n.clientY),!(r?this.beginResize(n.clientX,n.clientY):this.beginDrag(n.clientX,n.clientY))){this.cancelDoublePress();return}this.touchDragId=n.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.addEventListener("touchend",this.onTouchDragEnd),document.addEventListener("touchcancel",this.onTouchDragEnd),e.cancelable&&e.preventDefault()}}onTouchDragMove(e){const n=vm(e.touches,this.touchDragId);if(!this.gestureActive()||!n)return;this.moveGesture(n.clientX,n.clientY);const r=this.dragScroll;r&&((window.scrollX||window.pageXOffset||0)!==r.x||(window.scrollY||window.pageYOffset||0)!==r.y)&&window.scrollTo(r.x,r.y),e.cancelable&&e.preventDefault()}onTouchDragEnd(e){e&&e.touches&&vm(e.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.removeEventListener("touchend",this.onTouchDragEnd),document.removeEventListener("touchcancel",this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){const e=this.dragTarget;e&&(e.removeEventListener("pointermove",this.onDragMove),e.removeEventListener("pointerup",this.onDragEnd),e.removeEventListener("pointercancel",this.onDragEnd),this.dragPointerId!=null&&e.hasPointerCapture&&e.hasPointerCapture(this.dragPointerId)&&e.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(e){e.stopPropagation();let{ship:n}=this.props;if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Rn()){window.uiEvents.relay("CloseSystemInfo");return}window.uiEvents.relay("SystemClicked",{ship:n,system:n,element:e.target})}onShipTouchMove(e){if(!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(e){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onShipTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:n}=this.props,r=shipManager.systems.getSystemByName(n,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:n,system:r||n.systems[0],element:e.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onUnknownTouchStart(e){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:i}=this.props,o=shipManager.systems.getSystemByName(i,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:i,system:o||i.systems[0],element:n,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){const e=this.elementRef.current,n=Fs(this.props.ship)?"left":"right";e.addEventListener("pointerdown",this.onDragStart),e.addEventListener("touchstart",this.onTouchDragStart,{passive:!1});const r=fm[n];if(r&&!gm()){const i=Math.max(0,Math.min(r.top,window.innerHeight-60)),o=Math.max(60-e.offsetWidth,Math.min(r.left,window.innerWidth-60));e.style.top=i+"px",e.style.left=o+"px",e.style.right="auto",this.positioned=!0}document.addEventListener("pointerdown",this.onDocumentPointerDown),this.applyScreenFit(),window.addEventListener("resize",this.onScreenResize),window.addEventListener("orientationchange",this.onScreenResize)}componentDidUpdate(){this.applyScreenFit()}componentWillUnmount(){document.removeEventListener("pointerdown",this.onDocumentPointerDown),window.removeEventListener("resize",this.onScreenResize),window.removeEventListener("orientationchange",this.onScreenResize);const e=this.elementRef.current;e&&(e.removeEventListener("pointerdown",this.onDragStart),e.removeEventListener("touchstart",this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(e){this.panelHoverTimer&&(clearTimeout(this.panelHoverTimer),this.panelHoverTimer=null),this.state.hoverPanel!==e&&this.setState({hoverPanel:e})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(e){if(!this.state.openPanel)return;const n=this.controlsRef.current,r=this.popupRef.current;n&&n.contains(e.target)||r&&r.contains(e.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay("CloseShipWindow",{ship:this.props.ship})}togglePanel(e,n){n.stopPropagation(),this.setState(r=>({openPanel:r.openPanel===e?null:e}))}toggleArt(e){e&&e.stopPropagation(),this.setState(n=>({showArt:!n.showArt,openPanel:null}))}renderResizeGrip(e){return a.jsx(Ik,{className:"shipwindow-resize-grip shipwindow-nodrag",$pad:o$,$mirror:this.isMirroredGrip(),$overlap:!!e,onDoubleClick:this.onGripDoubleClick,title:"Drag to resize this window — double-click to reset its size"})}renderStatusStrip(e,n){const r=n?[{key:"rolled",text:"⟲ Rolled — port / starboard reversed"}].concat($m(e)):$m(e);return a.jsxs(R.Fragment,{children:[r.map((i,o)=>a.jsx(Fk,{$color:i.color,$bg:i.bg,$grip:o===r.length-1,children:i.text},i.key)),this.renderResizeGrip(r.length>0)]})}renderHeader(e,n,r){return a.jsxs(Dk,{className:"shipwindow-drag-handle",title:"Drag to move — double-click to reset window size",children:[a.jsx(Pk,{$tint:r,title:e,children:e}),a.jsx(Ak,{title:n,children:n}),a.jsx(Rk,{className:"shipwindow-nodrag",onClick:this.close.bind(this),children:"✕"})]})}renderHitChartButton(e){const{openPanel:n}=this.state;return a.jsxs(zs,{ref:this.hitChartBtnRef,$wide:e,$active:n==="hitchart",onClick:this.togglePanel.bind(this,"hitchart"),children:[a.jsx(um,{children:"⊕"}),"Hit Chart"]})}renderStatsButton(e){const{openPanel:n}=this.state;return a.jsxs(zs,{$wide:e,$active:n==="shipstats",onClick:this.togglePanel.bind(this,"shipstats"),onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[a.jsxs(am,{children:[a.jsx("i",{}),a.jsx("i",{}),a.jsx("i",{})]}),"Ship Stats"]})}renderArtButton(e){return a.jsxs(zs,{$wide:e,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[a.jsx(Lk,{}),"Ship Art"]})}renderNotesButton(e){const{openPanel:n}=this.state;return a.jsxs(zs,{$wide:e,$active:n==="notes",onClick:this.togglePanel.bind(this,"notes"),onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[a.jsx(um,{children:"✎"}),"Notes"]})}renderControls(e,n,r,i){const o=Rn(),s=Sm(this.props.ship),l=km(this.props.ship);if(!e&&!n&&!i&&!s&&!l)return null;const c=o;return a.jsxs(Ok,{ref:this.controlsRef,$compact:r,children:[e&&this.renderHitChartButton(c),s&&this.renderArtButton(c),l&&this.renderStatsButton(c),n&&this.renderNotesButton(c),i&&a.jsx(dm,{ship:this.props.ship})]})}getButtonLeft(){const e=this.controlsRef.current,n=this.elementRef.current;return!e||!n?null:Math.round((e.getBoundingClientRect().left-n.getBoundingClientRect().left)/(this.screenFit||1)-n.clientLeft)}getAnchorBelow(e,n){const r=e&&e.current,i=this.elementRef.current;if(!r||!i)return{top:n,left:this.getButtonLeft()};const o=r.getBoundingClientRect(),s=i.getBoundingClientRect(),l=this.screenFit||1;return{left:Math.round((o.left-s.left)/l-i.clientLeft),top:Math.round((o.bottom-s.top)/l-i.clientTop)+4}}renderPopup(e,n,r){const{ship:i}=this.props,{openPanel:o,hoverPanel:s}=this.state,l=o||s;if(!l)return null;const c=l==="hitchart"&&Rn()?this.hitChartBtnRef:this.controlsRef,{top:d,left:u}=this.getAnchorBelow(c,r);return l==="hitchart"&&e?a.jsx(pd,{ref:this.popupRef,$top:d,$left:u,$fit:!0,children:a.jsx(xk,{ship:i})}):l==="shipstats"&&km(i)?a.jsx(pd,{ref:this.popupRef,$top:d,$left:u,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:a.jsx(dm,{ship:i,live:!0,bare:!0})}):l==="notes"&&n?a.jsx(pd,{ref:this.popupRef,$top:d,$left:u,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:a.jsx(Ug,{ship:i,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){const{ship:e}=this.props,n=Rn(),r=Fs(e);var i=e.shipClass,o=e.name;let s=!1;if(e.mine){var l=shipManager.systems.getSystemByName(e,"mineStealth");l&&!l.isMineRevealed(e)&&(i="Mine",o="Mine",s=!0)}o||(o=i,i="");const c=!s&&u$(e),d=!n&&!s&&h$(e);if(e.flight){if(n)return a.jsxs(Oi,{ref:this.elementRef,onClick:Li,onContextMenu:w=>{w.preventDefault(),w.stopPropagation()},$isMyTeam:r,$variant:"flightLobby",children:[this.renderHeader(o,i,wd()),a.jsxs(Bk,{children:[a.jsx(Nk,{children:a.jsx(od,{ship:e})}),a.jsx(hd,{ship:e})]}),this.renderResizeGrip()]});const p=!!(window.ew&&ew.isFlightEwPool(e));return a.jsxs(Oi,{ref:this.elementRef,onClick:Li,onContextMenu:w=>{w.preventDefault(),w.stopPropagation()},$isMyTeam:r,$variant:p?"flightEw":"flight",children:[this.renderHeader(o,i,wd()),p?a.jsxs(jk,{children:[a.jsx(Ek,{children:a.jsx(od,{ship:e})}),a.jsx(nm,{ship:e,flight:!0})]}):a.jsx(od,{ship:e}),this.renderStatusStrip(e)]})}if(s)return a.jsxs(Oi,{ref:this.elementRef,onClick:Li,onContextMenu:p=>{p.preventDefault(),p.stopPropagation()},$isMyTeam:r,$variant:"terrain",children:[this.renderHeader(o,i,null),a.jsxs(hm,{children:[a.jsx(gd,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(fd,{$img:window.AssetManager.getSmartImagePath(e.imagePath)}),a.jsx(_k,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:"?"})]}),this.renderResizeGrip()]});const u=x$(e),f=m$(u);if((window.gamedata.isTerrain(e.shipSizeClass,e.userid)||e.mine)&&!bm(u)){const p=c||d||Sm(e);return a.jsxs(Oi,{ref:this.elementRef,onClick:Li,onContextMenu:w=>{w.preventDefault(),w.stopPropagation()},$isMyTeam:r,$variant:"terrain",children:[this.renderHeader(o,i,null),a.jsxs(hm,{children:[a.jsx(gd,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(fd,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:e.mine&&p?d$:0}),this.renderControls(c,d,!0),Vk.map(w=>u[w].length>0&&a.jsx(Zg,{location:w,nameOverride:f[w],ship:e,systems:u[w],isTerrain:!0,hidden:this.state.showArt},`section-${e.id}-${w}`))]}),n&&a.jsx(hd,{ship:e,full:!0}),this.renderStatusStrip(e),this.renderPopup(c,d,72)]})}const v=shipManager.movement.isRolled(e),x=!!e.enhancementTooltip||dd(e)!==null,b=y$(u,x),M=e.base&&!e.smallBase,y=a.jsxs(zk,{$areas:b,children:[a.jsx(gd,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(fd,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:n&&bm(u)?c$:0}),this.renderControls(c,d,!1,n&&!e.mine),n?a.jsx(hd,{ship:e,grid:!0,hideEnhancements:x}):a.jsx(nm,{ship:e}),x&&a.jsx(Tk,{ship:e}),Gk.map(p=>{if(u[p].length===0)return null;const w=v&&pm[p]!==void 0?pm[p]:p,S=Hk[w],C=p===0||p===1||p===2||M&&Yk.includes(p);return a.jsx(Zg,{location:p,displayLocation:w,area:S,valign:Wk[S],justify:Uk[S],wide:C,minHeight:void 0,nameOverride:f[p],hidden:this.state.showArt,ship:e,systems:u[p]},`section-${e.id}-${p}`)})]});return a.jsxs(Oi,{ref:this.elementRef,onClick:Li,onContextMenu:p=>{p.preventDefault(),p.stopPropagation()},$isMyTeam:r,$variant:"ship",children:[this.renderHeader(o,i,wd()),y,this.renderStatusStrip(e,v),this.renderPopup(c,d)]})}}const Li=()=>window.uiEvents.relay("CloseSystemInfo"),fm={left:null,right:null},Xk="(max-width: 1024px)",Zk="(orientation: portrait)",qk={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},Jk={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},md=.98,gm=()=>!!window.matchMedia&&window.matchMedia(Xk).matches,e$=()=>window.matchMedia?window.matchMedia(Zk).matches:window.innerHeight>=window.innerWidth,t$=(t,e)=>e===1?t:{fillW:Math.min(md,t.fillW*e),fillH:Math.min(md,t.fillH*e),min:Math.min(t.max,t.min*e),max:t.max},n$=()=>{const t=Rn()?Jk:qk;return e$()?t$(t,t.portrait):t},r$=.35,i$=3,o$=6,s$=8,a$=400,mm=6,Tr=40,xd="fv.shipwindow.userScale",yd=t=>Math.min(i$,Math.max(r$,t)),xm=(t,e,n,r)=>(n*t+r*e)/(t*t+e*e),Bs={left:null,right:null},Ns=t=>(Bs[t]==null&&(Bs[t]=l$(t)),Bs[t]),ym=(t,e)=>{Bs[t]=e},l$=t=>{try{let e=parseFloat(window.localStorage.getItem(xd+"."+t));if(isFinite(e)||(e=parseFloat(window.localStorage.getItem(xd))),isFinite(e)&&e>0)return yd(e)}catch{}return 1},wm=(t,e)=>{try{window.localStorage.setItem(xd+"."+t,String(e))}catch{}},vm=(t,e)=>{if(!t||e==null)return null;for(let n=0;n<t.length;n++)if(t[n].identifier===e)return t[n];return null},Rn=()=>!!window.gamedata&&window.gamedata.gamephase===-2,c$=35,bm=t=>Kk.some(e=>t[e].length>0),d$=20,Fs=t=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide=="function"?window.ShipWindowManager.isLeftSide(t):t.team===window.gamedata.getPlayerTeam(),u$=t=>!!t.hitChart&&Object.keys(t.hitChart).length>0,Sm=t=>!!(t&&t.imagePath)&&!t.flight,km=t=>!!t&&!Rn()&&!t.flight&&!t.mine&&!window.gamedata.isTerrain(t.shipSizeClass,t.userid),h$=t=>!!t.notes||!!t.enhancementTooltip||!!(t.hasAttached&&Object.keys(t.hasAttached).length>0),wd=t=>null,$m=t=>{if(Rn())return[];const e=[],n=shipManager.getTurnDeployed(t);n>window.gamedata.turn&&n<999&&e.push({key:"deploying",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Deploying on Turn "+n});const r=shipManager.getArrivalIniPenalty(t);r!==0&&e.push({key:"arrivalScatter",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Arrival Scatter "+r+" Ini"});const i=t.trueStealth?shipManager.getStealthToggleForecast(t):null;if(t.trueStealth)if(g$(t,i))e.push({key:"undetected",color:m.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Undetected"});else if(t.mine){const c=shipManager.systems.getSystemByName(t,"mineStealth");!c||c.isMineRevealedToOpponent(t)?e.push({key:"detected",color:m.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected - Revealed"}):e.push({key:"detected",color:m.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Detected - Not Revealed"})}else i===!0?e.push({key:"detected",color:m.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Would be Detected"}):e.push({key:"detected",color:m.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected"});shipManager.isJumpingToHyperspace(t)&&e.push({key:"jumping",color:m.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:"Jumping to Hyperspace"});const o=t.flight?null:shipManager.movement.getExtendedTurnStatus(t);o&&e.push(o.cancelled?{key:"extendedTurn",color:m.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:o.text}:{key:"extendedTurn",color:m.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:o.text});const s=window.JumpEngine&&typeof window.JumpEngine.getAbductionChain=="function"?window.JumpEngine.getAbductionChain(t.id):null;s&&e.push({key:"abducted",color:"#b36bff",bg:"rgba(127, 0, 255, 0.10)",text:"Being abducted: "+window.JumpEngine.formatAbductionHalves(s.total)+"/"+s.cost+" power-turns"});const l=shipManager.getHangarManoeuvre(t);if(l&&e.push({key:"hangarManoeuvre",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:l.text}),t.attached&&Object.keys(t.attached).length>0&&!t.detached&&!(l&&l.riding)){const c=window.gamedata.getShip(Object.keys(t.attached)[0]);if(c){const d=Object.values(t.attached)[0];let u="";d==1?u="Front":d==2?u="Aft":d==3||d==31||d==32?u="Port":(d==4||d==41||d==42)&&(u="Starboard"),e.push({key:"attached",color:m.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Attached to "+c.name+(u?" ["+u+"]":"")})}}return t.hasAttached&&Object.keys(t.hasAttached).length>0&&Object.keys(t.hasAttached).filter(d=>{const u=window.gamedata.getShip(d);return!(u&&shipManager.isDockingRider(u))}).length>0&&e.push({key:"boarded",color:m.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Ship is being boarded!"}),t.flight&&f$(t)&&e.push({key:"edfGrounded",color:p$,bg:"rgba(210, 80, 255, 0.12)",text:"Energy Drained - cannot fire"}),e},p$="#d250ff",f$=t=>{const e=shipManager.systems.getSystem(t,1);return!e||!e.criticals?!1:e.criticals.some(n=>n.phpclass==="EdfFighterGrounded"&&n.turn+1===window.gamedata.turn)},g$=(t,e)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(t)==gamedata.turn||shipManager.getTurnDeployed(t)==gamedata.turn))return!0;if(e!=null)return!e;let n=shipManager.isDetected(t);if(!n&&t.team==gamedata.getPlayerTeam()){let r=null;t.mine?r=shipManager.systems.getSystemByName(t,"mineStealth"):t.faction=="Torvalus Speculators"?r=shipManager.systems.getSystemByName(t,"ShadingField"):shipManager.getSpecialAbilityStealth(t,"Cloaking")?r=shipManager.systems.getSystemByName(t,"CloakingDevice"):shipManager.getSpecialAbilityStealth(t,"Stealth")&&(r=shipManager.systems.getSystemByName(t,"stealth")),r&&(Array.isArray(r.detected)&&r.detected.length>0||r.detected===!0||Array.isArray(r.detectedNew)&&r.detectedNew.length>0||r.detectedNew===!0)&&(n=!0)}return!n},m$=t=>{const e={};return[{locations:[3,31,32],name:"Port"},{locations:[4,41,42],name:"Starboard"}].forEach(n=>{const r=n.locations.filter(i=>t[i].some(o=>o.name==="structure"));r.length===1&&r[0]!==n.locations[0]&&(e[r[0]]=n.name)}),e},x$=t=>{const e={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return t.systems.forEach(n=>{n.hideInShipWindow||e[n.location].push(n)}),e},y$=(t,e)=>{const n=[["ctrl","fwd","ew"]];let r=0;if((t[31].length||t[41].length)&&(n.push(["lfwd","prim","rfwd"]),r++),(t[3].length||t[4].length)&&(n.push(["left","prim","right"]),r++),(t[32].length||t[42].length)&&(n.push(["laft","prim","raft"]),r++),r===0&&t[0].length&&n.push([null,"prim",null]),t[2].length&&n.push([null,"aft",null]),e){const i=n[n.length-1];n.length>1&&i[2]===null?i[2]="enh":n.push([null,null,"enh"])}for(let i=1;i<n.length&&n[i][0]===null;i++)n[i][0]="ctrl";for(let i=1;i<n.length&&n[i][2]===null;i++)n[i][2]="ew";return n.map(i=>`"${i[0]||"."}  ${i[1]||"."}  ${i[2]||"."}"`).join(" ")},w$=h.div`

`,v$=h.div`
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
    border: 1px solid ${m.colors.warning};
    background-color: ${m.colors.windowBg};
    color: ${m.colors.text};
    font-family: ${m.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`,b$=h.span`
    cursor: pointer;
    color: ${m.colors.line};
    font-size: 16px;

    &:hover {
        color: ${m.colors.text};
    }
`;class S$ extends R.Component{constructor(e){super(e),this.state={error:null}}static getDerivedStateFromError(e){return{error:e}}componentDidCatch(e,n){const r=this.props.ship;console.error("Ship window render failed for",r&&(r.name||r.shipClass),e,n)}render(){if(this.state.error){const e=this.props.ship;return a.jsxs(v$,{children:[a.jsxs("span",{children:[e&&(e.name||e.shipClass)||"Ship"," — window failed to render (see console)"]}),a.jsx(b$,{onClick:()=>window.uiEvents.relay("CloseShipWindow",{ship:e}),children:"✕"})]})}return this.props.children}}class k$ extends R.Component{render(){const{ships:e}=this.props;return a.jsx(w$,{children:e.map(n=>a.jsx(S$,{ship:n,children:a.jsx(Qk,{ship:n})},`shipwindow-${n.userid}-${n.id}`))})}}const $$=h.div`
    position: absolute;
    z-index: 20000;
    ${t=>Object.keys(t.$position).reduce((e,n)=>e+`
`+n+":"+t.$position[n]+"px;","")}
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
    background: ${A.bg};
    border: 1px solid ${A.line};
    border-radius: ${A.radius};
    box-shadow: ${A.shadow};
`;h.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${A.dim};
    user-select: none;
`;const C$=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${A.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,T$=h.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,M$=h.span`
    flex: 0 0 auto;
    color: ${A.dim};
    font-size: 10px;
    user-select: none;
`,Cm=h.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${A.btnBg};
    border: 1px solid ${A.line};
    border-radius: 2px;
    color: ${A.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${A.line};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${A.btnBg}; color: ${A.btnText}; }
    `}
`,j$=h.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${m.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${A.well};
    border: 1px solid ${A.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${A.focus}; }
`,E$=h.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${A.btnBg};
    border: 1px solid ${A.line};
    border-radius: 2px;
    color: ${A.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${m.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${A.line}; color: #ffffff; }
`,D$=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e};class P$ extends R.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Di(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(e,n){const{ship:r}=this.props,i=this.maxHealth();let o=parseInt(n,10);isNaN(o)&&(o=i),o=Math.max(1,Math.min(i,o)),battleDamage.setFighter(r,e,{d:i-o}),this.refresh()}step(e,n){this.setRemaining(e,battleDamage.fighterHealth(this.props.ship,e)+n)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,"");this.setRemaining(e,r===""?0:parseInt(r,10))}propagate(){const{ship:e}=this.props,n=parseInt(e.flightSize,10)||0,r=battleDamage.fighterMaxHealth(e)-battleDamage.fighterHealth(e,1);for(let i=2;i<=n;i++)battleDamage.setFighter(e,i,{d:r});this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){const{ship:e,boundingBox:n}=this.props,r=parseInt(e&&e.flightSize,10)||0,i=this.maxHealth();if(!r||!i)return null;battleDamage.flightSummary(e);const o=[];for(let c=1;c<=r;c++){const d=battleDamage.fighterHealth(e,c);o.push(a.jsxs(C$,{children:[a.jsxs(T$,{children:["Fighter ",c]}),a.jsx(Cm,{title:"More damage",disabled:d<=1,onClick:()=>this.step(c,-1),children:"−"}),a.jsx(j$,{ref:this.wheelRef(c),type:"text",value:d,onChange:u=>this.onInput(c,u)}),a.jsx(Cm,{title:"Repair",disabled:d>=i,onClick:()=>this.step(c,1),children:"+"}),a.jsxs(M$,{children:["/ ",i]})]},`ftr-${c}`))}const s=battleDamage.flightCritEntry(e)||{},l=yg(s.c,e.preBattleCritDesc,e.preBattleCritTransient,s.p);return a.jsxs($$,{$position:D$(n),onClick:c=>c.stopPropagation(),children:[a.jsx(mg,{$sticky:!0,children:"Fighter Damage"}),o,r>1&&a.jsx(E$,{title:"Copy Fighter 1's damage to every fighter in this flight",onClick:()=>this.propagate(),children:"Apply Fighter 1's damage to all"}),a.jsx(wg,{ship:e,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:l,editable:!0,onChange:()=>this.refresh()})]})}}const A$=h.div`
    position: absolute;
    z-index: 20000;
    ${t=>Object.keys(t.$position).reduce((e,n)=>e+`
`+n+":"+t.$position[n]+"px;","")}
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
    background: ${A.bg};
    border: 1px solid ${A.line};
    border-radius: ${A.radius};
    box-shadow: ${A.shadow};
`,R$=h.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${A.dim};
    user-select: none;
`,I$=h.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${A.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,O$=h.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,L$=h.span`
    flex: 0 0 auto;
    color: ${A.dim};
    font-size: 10px;
    user-select: none;
`,Tm=h.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${A.btnBg};
    border: 1px solid ${A.line};
    border-radius: 2px;
    color: ${A.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${A.line};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${A.btnBg}; color: ${A.btnText}; }
    `}
`,z$=h.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${m.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${A.well};
    border: 1px solid ${A.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${A.focus}; }
`,B$=h.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${A.btnBg};
    border: 1px solid ${A.line};
    border-radius: 2px;
    color: ${A.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${m.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${A.line}; color: #ffffff; }
`,N$=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e};class F$ extends R.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Di(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(e,n){const{ship:r}=this.props,i=this.maxHealth();let o=parseInt(n,10);isNaN(o)&&(o=i),o=Math.max(1,Math.min(i,o)),battleDamage.setMine(r,e,{d:i-o}),this.refresh()}step(e,n){this.setRemaining(e,battleDamage.mineHealth(this.props.ship,e)+n)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,"");this.setRemaining(e,r===""?0:parseInt(r,10))}propagate(){const{ship:e}=this.props,n=battleDamage.mineCount(e),r=battleDamage.getEntry(e,battleDamage.KIND_MINE,1);for(let i=2;i<=n;i++)battleDamage.setWholeEntry(e,battleDamage.KIND_MINE,i,r);this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){const{ship:e,boundingBox:n}=this.props,r=battleDamage.mineCount(e),i=this.maxHealth();if(!r||i<2)return null;const o=battleDamage.mineSummary(e),s=[];for(let l=1;l<=r;l++){const c=battleDamage.mineHealth(e,l);s.push(a.jsxs(I$,{children:[a.jsxs(O$,{children:["Mine ",l]}),a.jsx(Tm,{title:"More damage",disabled:c<=1,onClick:()=>this.step(l,-1),children:"−"}),a.jsx(z$,{ref:this.wheelRef(l),type:"text",value:c,onChange:d=>this.onInput(l,d)}),a.jsx(Tm,{title:"Repair",disabled:c>=i,onClick:()=>this.step(l,1),children:"+"}),a.jsxs(L$,{children:["/ ",i]})]},`mne-${l}`))}return a.jsxs(A$,{$position:N$(n),onClick:l=>l.stopPropagation(),children:[a.jsx(mg,{$sticky:!0,children:"Mine Damage"}),a.jsxs(R$,{children:[o.remaining," / ",o.total," structure"]}),s,r>1&&a.jsx(B$,{title:"Copy Mine 1's damage to every mine in this purchase",onClick:()=>this.propagate(),children:"Apply Mine 1 to all"})]})}}class _${constructor(e){this.parentElement=e,this.roots=new Map}getRoot(e){const n=jQuery(e,this.parentElement)[0];return n?(this.roots.has(n)||this.roots.set(n,Hp(n)),this.roots.get(n)):null}unmountRoot(e){const n=jQuery(e,this.parentElement)[0];n&&this.roots.has(n)&&(this.roots.get(n).unmount(),this.roots.delete(n))}EwButtons(e){const n=this.getRoot("#showEwButtons");n&&n.render(a.jsx(nv,{...e}))}FullScreen(e){const n=this.getRoot("#fullScreen");n&&n.render(a.jsx(Kw,{...e}))}Surrender(e){const n=this.getRoot("#surrender");n&&n.render(a.jsx(Xw,{...e}))}BackToLobby(e){const n=this.getRoot("#backToLobby");n&&n.render(a.jsx(ev,{...e}))}PlayerSettings(e){const n=this.getRoot("#playerSettings");n&&n.render(a.jsx(ww,{...e}))}showShipThrustUI(e){const n=this.getRoot("#shipThrust");n&&n.render(a.jsx(Uw,{...e}))}hideShipThrustUI(){this.unmountRoot("#shipThrust")}showWeaponList(e){const n=this.getRoot("#weaponList");n&&n.render(a.jsx(mS,{...e}))}hideWeaponList(){this.unmountRoot("#weaponList")}showSystemInfo(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(SS,{...e}))}hideSystemInfo(){this.unmountRoot("#systemInfoReact")}showSystemInfoMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(ES,{...e}))}hideSystemInfoMenu(){this.unmountRoot("#systemInfoReact")}canShowSystemInfoMenu(e,n){return Vc(e,n)}showFighterDamageMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(P$,{...e}))}showMineDamageMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(F$,{...e}))}renderShipWindows(e){const n=this.getRoot("#shipWindowsReact");n&&n.render(a.jsx(k$,{...e}))}}window.UIManager=_$});
