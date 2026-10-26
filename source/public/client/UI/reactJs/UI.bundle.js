(function(Hi){typeof define=="function"&&define.amd?define(Hi):Hi()})(function(){"use strict";function Hi(t){return t&&t.__esModule&&Object.prototype.hasOwnProperty.call(t,"default")?t.default:t}var Od={exports:{}},Wi={},Ld={exports:{}},B={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var Ir=Symbol.for("react.element"),Vm=Symbol.for("react.portal"),Ym=Symbol.for("react.fragment"),Km=Symbol.for("react.strict_mode"),Qm=Symbol.for("react.profiler"),Xm=Symbol.for("react.provider"),Jm=Symbol.for("react.context"),qm=Symbol.for("react.forward_ref"),Zm=Symbol.for("react.suspense"),ex=Symbol.for("react.memo"),tx=Symbol.for("react.lazy"),zd=Symbol.iterator;function nx(t){return t===null||typeof t!="object"?null:(t=zd&&t[zd]||t["@@iterator"],typeof t=="function"?t:null)}var Bd={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},Nd=Object.assign,Fd={};function Fn(t,e,n){this.props=t,this.context=e,this.refs=Fd,this.updater=n||Bd}Fn.prototype.isReactComponent={},Fn.prototype.setState=function(t,e){if(typeof t!="object"&&typeof t!="function"&&t!=null)throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,t,e,"setState")},Fn.prototype.forceUpdate=function(t){this.updater.enqueueForceUpdate(this,t,"forceUpdate")};function _d(){}_d.prototype=Fn.prototype;function qs(t,e,n){this.props=t,this.context=e,this.refs=Fd,this.updater=n||Bd}var Zs=qs.prototype=new _d;Zs.constructor=qs,Nd(Zs,Fn.prototype),Zs.isPureReactComponent=!0;var Hd=Array.isArray,Wd=Object.prototype.hasOwnProperty,ea={current:null},Ud={key:!0,ref:!0,__self:!0,__source:!0};function Gd(t,e,n){var r,i={},o=null,s=null;if(e!=null)for(r in e.ref!==void 0&&(s=e.ref),e.key!==void 0&&(o=""+e.key),e)Wd.call(e,r)&&!Ud.hasOwnProperty(r)&&(i[r]=e[r]);var l=arguments.length-2;if(l===1)i.children=n;else if(1<l){for(var c=Array(l),d=0;d<l;d++)c[d]=arguments[d+2];i.children=c}if(t&&t.defaultProps)for(r in l=t.defaultProps,l)i[r]===void 0&&(i[r]=l[r]);return{$$typeof:Ir,type:t,key:o,ref:s,props:i,_owner:ea.current}}function rx(t,e){return{$$typeof:Ir,type:t.type,key:e,ref:t.ref,props:t.props,_owner:t._owner}}function ta(t){return typeof t=="object"&&t!==null&&t.$$typeof===Ir}function ix(t){var e={"=":"=0",":":"=2"};return"$"+t.replace(/[=:]/g,function(n){return e[n]})}var Vd=/\/+/g;function na(t,e){return typeof t=="object"&&t!==null&&t.key!=null?ix(""+t.key):e.toString(36)}function Ui(t,e,n,r,i){var o=typeof t;(o==="undefined"||o==="boolean")&&(t=null);var s=!1;if(t===null)s=!0;else switch(o){case"string":case"number":s=!0;break;case"object":switch(t.$$typeof){case Ir:case Vm:s=!0}}if(s)return s=t,i=i(s),t=r===""?"."+na(s,0):r,Hd(i)?(n="",t!=null&&(n=t.replace(Vd,"$&/")+"/"),Ui(i,e,n,"",function(d){return d})):i!=null&&(ta(i)&&(i=rx(i,n+(!i.key||s&&s.key===i.key?"":(""+i.key).replace(Vd,"$&/")+"/")+t)),e.push(i)),1;if(s=0,r=r===""?".":r+":",Hd(t))for(var l=0;l<t.length;l++){o=t[l];var c=r+na(o,l);s+=Ui(o,e,n,c,i)}else if(c=nx(t),typeof c=="function")for(t=c.call(t),l=0;!(o=t.next()).done;)o=o.value,c=r+na(o,l++),s+=Ui(o,e,n,c,i);else if(o==="object")throw e=String(t),Error("Objects are not valid as a React child (found: "+(e==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":e)+"). If you meant to render a collection of children, use an array instead.");return s}function Gi(t,e,n){if(t==null)return t;var r=[],i=0;return Ui(t,r,"","",function(o){return e.call(n,o,i++)}),r}function ox(t){if(t._status===-1){var e=t._result;e=e(),e.then(function(n){(t._status===0||t._status===-1)&&(t._status=1,t._result=n)},function(n){(t._status===0||t._status===-1)&&(t._status=2,t._result=n)}),t._status===-1&&(t._status=0,t._result=e)}if(t._status===1)return t._result.default;throw t._result}var ke={current:null},Vi={transition:null},sx={ReactCurrentDispatcher:ke,ReactCurrentBatchConfig:Vi,ReactCurrentOwner:ea};function Yd(){throw Error("act(...) is not supported in production builds of React.")}B.Children={map:Gi,forEach:function(t,e,n){Gi(t,function(){e.apply(this,arguments)},n)},count:function(t){var e=0;return Gi(t,function(){e++}),e},toArray:function(t){return Gi(t,function(e){return e})||[]},only:function(t){if(!ta(t))throw Error("React.Children.only expected to receive a single React element child.");return t}},B.Component=Fn,B.Fragment=Ym,B.Profiler=Qm,B.PureComponent=qs,B.StrictMode=Km,B.Suspense=Zm,B.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=sx,B.act=Yd,B.cloneElement=function(t,e,n){if(t==null)throw Error("React.cloneElement(...): The argument must be a React element, but you passed "+t+".");var r=Nd({},t.props),i=t.key,o=t.ref,s=t._owner;if(e!=null){if(e.ref!==void 0&&(o=e.ref,s=ea.current),e.key!==void 0&&(i=""+e.key),t.type&&t.type.defaultProps)var l=t.type.defaultProps;for(c in e)Wd.call(e,c)&&!Ud.hasOwnProperty(c)&&(r[c]=e[c]===void 0&&l!==void 0?l[c]:e[c])}var c=arguments.length-2;if(c===1)r.children=n;else if(1<c){l=Array(c);for(var d=0;d<c;d++)l[d]=arguments[d+2];r.children=l}return{$$typeof:Ir,type:t.type,key:i,ref:o,props:r,_owner:s}},B.createContext=function(t){return t={$$typeof:Jm,_currentValue:t,_currentValue2:t,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null},t.Provider={$$typeof:Xm,_context:t},t.Consumer=t},B.createElement=Gd,B.createFactory=function(t){var e=Gd.bind(null,t);return e.type=t,e},B.createRef=function(){return{current:null}},B.forwardRef=function(t){return{$$typeof:qm,render:t}},B.isValidElement=ta,B.lazy=function(t){return{$$typeof:tx,_payload:{_status:-1,_result:t},_init:ox}},B.memo=function(t,e){return{$$typeof:ex,type:t,compare:e===void 0?null:e}},B.startTransition=function(t){var e=Vi.transition;Vi.transition={};try{t()}finally{Vi.transition=e}},B.unstable_act=Yd,B.useCallback=function(t,e){return ke.current.useCallback(t,e)},B.useContext=function(t){return ke.current.useContext(t)},B.useDebugValue=function(){},B.useDeferredValue=function(t){return ke.current.useDeferredValue(t)},B.useEffect=function(t,e){return ke.current.useEffect(t,e)},B.useId=function(){return ke.current.useId()},B.useImperativeHandle=function(t,e,n){return ke.current.useImperativeHandle(t,e,n)},B.useInsertionEffect=function(t,e){return ke.current.useInsertionEffect(t,e)},B.useLayoutEffect=function(t,e){return ke.current.useLayoutEffect(t,e)},B.useMemo=function(t,e){return ke.current.useMemo(t,e)},B.useReducer=function(t,e,n){return ke.current.useReducer(t,e,n)},B.useRef=function(t){return ke.current.useRef(t)},B.useState=function(t){return ke.current.useState(t)},B.useSyncExternalStore=function(t,e,n){return ke.current.useSyncExternalStore(t,e,n)},B.useTransition=function(){return ke.current.useTransition()},B.version="18.3.1",Ld.exports=B;var I=Ld.exports;const ie=Hi(I);/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var ax=I,lx=Symbol.for("react.element"),cx=Symbol.for("react.fragment"),dx=Object.prototype.hasOwnProperty,ux=ax.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,hx={key:!0,ref:!0,__self:!0,__source:!0};function Kd(t,e,n){var r,i={},o=null,s=null;n!==void 0&&(o=""+n),e.key!==void 0&&(o=""+e.key),e.ref!==void 0&&(s=e.ref);for(r in e)dx.call(e,r)&&!hx.hasOwnProperty(r)&&(i[r]=e[r]);if(t&&t.defaultProps)for(r in e=t.defaultProps,e)i[r]===void 0&&(i[r]=e[r]);return{$$typeof:lx,type:t,key:o,ref:s,props:i,_owner:ux.current}}Wi.Fragment=cx,Wi.jsx=Kd,Wi.jsxs=Kd,Od.exports=Wi;var a=Od.exports,Qd={exports:{}},Le={},Xd={exports:{}},Jd={};/**
 * @license React
 * scheduler.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */(function(t){function e(A,O){var L=A.length;A.push(O);e:for(;0<L;){var G=L-1>>>1,V=A[G];if(0<i(V,O))A[G]=O,A[L]=V,L=G;else break e}}function n(A){return A.length===0?null:A[0]}function r(A){if(A.length===0)return null;var O=A[0],L=A.pop();if(L!==O){A[0]=L;e:for(var G=0,V=A.length,Bn=V>>>1;G<Bn;){var ht=2*(G+1)-1,Pr=A[ht],pt=ht+1,Nn=A[pt];if(0>i(Pr,L))pt<V&&0>i(Nn,Pr)?(A[G]=Nn,A[pt]=L,G=pt):(A[G]=Pr,A[ht]=L,G=ht);else if(pt<V&&0>i(Nn,L))A[G]=Nn,A[pt]=L,G=pt;else break e}}return O}function i(A,O){var L=A.sortIndex-O.sortIndex;return L!==0?L:A.id-O.id}if(typeof performance=="object"&&typeof performance.now=="function"){var o=performance;t.unstable_now=function(){return o.now()}}else{var s=Date,l=s.now();t.unstable_now=function(){return s.now()-l}}var c=[],d=[],h=1,p=null,f=3,v=!1,x=!1,b=!1,M=typeof setTimeout=="function"?setTimeout:null,w=typeof clearTimeout=="function"?clearTimeout:null,m=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function y(A){for(var O=n(d);O!==null;){if(O.callback===null)r(d);else if(O.startTime<=A)r(d),O.sortIndex=O.expirationTime,e(c,O);else break;O=n(d)}}function S(A){if(b=!1,y(A),!x)if(n(c)!==null)x=!0,Fi(T);else{var O=n(d);O!==null&&zn(S,O.startTime-A)}}function T(A,O){x=!1,b&&(b=!1,w($),$=-1),v=!0;var L=f;try{for(y(O),p=n(c);p!==null&&(!(p.expirationTime>O)||A&&!H());){var G=p.callback;if(typeof G=="function"){p.callback=null,f=p.priorityLevel;var V=G(p.expirationTime<=O);O=t.unstable_now(),typeof V=="function"?p.callback=V:p===n(c)&&r(c),y(O)}else r(c);p=n(c)}if(p!==null)var Bn=!0;else{var ht=n(d);ht!==null&&zn(S,ht.startTime-O),Bn=!1}return Bn}finally{p=null,f=L,v=!1}}var C=!1,k=null,$=-1,D=5,E=-1;function H(){return!(t.unstable_now()-E<D)}function K(){if(k!==null){var A=t.unstable_now();E=A;var O=!0;try{O=k(!0,A)}finally{O?he():(C=!1,k=null)}}else C=!1}var he;if(typeof m=="function")he=function(){m(K)};else if(typeof MessageChannel<"u"){var ut=new MessageChannel,Rd=ut.port2;ut.port1.onmessage=K,he=function(){Rd.postMessage(null)}}else he=function(){M(K,0)};function Fi(A){k=A,C||(C=!0,he())}function zn(A,O){$=M(function(){A(t.unstable_now())},O)}t.unstable_IdlePriority=5,t.unstable_ImmediatePriority=1,t.unstable_LowPriority=4,t.unstable_NormalPriority=3,t.unstable_Profiling=null,t.unstable_UserBlockingPriority=2,t.unstable_cancelCallback=function(A){A.callback=null},t.unstable_continueExecution=function(){x||v||(x=!0,Fi(T))},t.unstable_forceFrameRate=function(A){0>A||125<A?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):D=0<A?Math.floor(1e3/A):5},t.unstable_getCurrentPriorityLevel=function(){return f},t.unstable_getFirstCallbackNode=function(){return n(c)},t.unstable_next=function(A){switch(f){case 1:case 2:case 3:var O=3;break;default:O=f}var L=f;f=O;try{return A()}finally{f=L}},t.unstable_pauseExecution=function(){},t.unstable_requestPaint=function(){},t.unstable_runWithPriority=function(A,O){switch(A){case 1:case 2:case 3:case 4:case 5:break;default:A=3}var L=f;f=A;try{return O()}finally{f=L}},t.unstable_scheduleCallback=function(A,O,L){var G=t.unstable_now();switch(typeof L=="object"&&L!==null?(L=L.delay,L=typeof L=="number"&&0<L?G+L:G):L=G,A){case 1:var V=-1;break;case 2:V=250;break;case 5:V=1073741823;break;case 4:V=1e4;break;default:V=5e3}return V=L+V,A={id:h++,callback:O,priorityLevel:A,startTime:L,expirationTime:V,sortIndex:-1},L>G?(A.sortIndex=L,e(d,A),n(c)===null&&A===n(d)&&(b?(w($),$=-1):b=!0,zn(S,L-G))):(A.sortIndex=V,e(c,A),x||v||(x=!0,Fi(T))),A},t.unstable_shouldYield=H,t.unstable_wrapCallback=function(A){var O=f;return function(){var L=f;f=O;try{return A.apply(this,arguments)}finally{f=L}}}})(Jd),Xd.exports=Jd;var px=Xd.exports;/**
 * @license React
 * react-dom.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var fx=I,ze=px;function j(t){for(var e="https://reactjs.org/docs/error-decoder.html?invariant="+t,n=1;n<arguments.length;n++)e+="&args[]="+encodeURIComponent(arguments[n]);return"Minified React error #"+t+"; visit "+e+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var qd=new Set,Or={};function hn(t,e){_n(t,e),_n(t+"Capture",e)}function _n(t,e){for(Or[t]=e,t=0;t<e.length;t++)qd.add(e[t])}var vt=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),ra=Object.prototype.hasOwnProperty,gx=/^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,Zd={},eu={};function mx(t){return ra.call(eu,t)?!0:ra.call(Zd,t)?!1:gx.test(t)?eu[t]=!0:(Zd[t]=!0,!1)}function xx(t,e,n,r){if(n!==null&&n.type===0)return!1;switch(typeof e){case"function":case"symbol":return!0;case"boolean":return r?!1:n!==null?!n.acceptsBooleans:(t=t.toLowerCase().slice(0,5),t!=="data-"&&t!=="aria-");default:return!1}}function yx(t,e,n,r){if(e===null||typeof e>"u"||xx(t,e,n,r))return!0;if(r)return!1;if(n!==null)switch(n.type){case 3:return!e;case 4:return e===!1;case 5:return isNaN(e);case 6:return isNaN(e)||1>e}return!1}function $e(t,e,n,r,i,o,s){this.acceptsBooleans=e===2||e===3||e===4,this.attributeName=r,this.attributeNamespace=i,this.mustUseProperty=n,this.propertyName=t,this.type=e,this.sanitizeURL=o,this.removeEmptyString=s}var ge={};"children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(t){ge[t]=new $e(t,0,!1,t,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(t){var e=t[0];ge[e]=new $e(e,1,!1,t[1],null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(t){ge[t]=new $e(t,2,!1,t.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(t){ge[t]=new $e(t,2,!1,t,null,!1,!1)}),"allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(t){ge[t]=new $e(t,3,!1,t.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(t){ge[t]=new $e(t,3,!0,t,null,!1,!1)}),["capture","download"].forEach(function(t){ge[t]=new $e(t,4,!1,t,null,!1,!1)}),["cols","rows","size","span"].forEach(function(t){ge[t]=new $e(t,6,!1,t,null,!1,!1)}),["rowSpan","start"].forEach(function(t){ge[t]=new $e(t,5,!1,t.toLowerCase(),null,!1,!1)});var ia=/[\-:]([a-z])/g;function oa(t){return t[1].toUpperCase()}"accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(t){var e=t.replace(ia,oa);ge[e]=new $e(e,1,!1,t,null,!1,!1)}),"xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(t){var e=t.replace(ia,oa);ge[e]=new $e(e,1,!1,t,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(t){var e=t.replace(ia,oa);ge[e]=new $e(e,1,!1,t,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(t){ge[t]=new $e(t,1,!1,t.toLowerCase(),null,!1,!1)}),ge.xlinkHref=new $e("xlinkHref",1,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(t){ge[t]=new $e(t,1,!1,t.toLowerCase(),null,!0,!0)});function sa(t,e,n,r){var i=ge.hasOwnProperty(e)?ge[e]:null;(i!==null?i.type!==0:r||!(2<e.length)||e[0]!=="o"&&e[0]!=="O"||e[1]!=="n"&&e[1]!=="N")&&(yx(e,n,i,r)&&(n=null),r||i===null?mx(e)&&(n===null?t.removeAttribute(e):t.setAttribute(e,""+n)):i.mustUseProperty?t[i.propertyName]=n===null?i.type===3?!1:"":n:(e=i.attributeName,r=i.attributeNamespace,n===null?t.removeAttribute(e):(i=i.type,n=i===3||i===4&&n===!0?"":""+n,r?t.setAttributeNS(r,e,n):t.setAttribute(e,n))))}var bt=fx.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,Yi=Symbol.for("react.element"),Hn=Symbol.for("react.portal"),Wn=Symbol.for("react.fragment"),aa=Symbol.for("react.strict_mode"),la=Symbol.for("react.profiler"),tu=Symbol.for("react.provider"),nu=Symbol.for("react.context"),ca=Symbol.for("react.forward_ref"),da=Symbol.for("react.suspense"),ua=Symbol.for("react.suspense_list"),ha=Symbol.for("react.memo"),zt=Symbol.for("react.lazy"),ru=Symbol.for("react.offscreen"),iu=Symbol.iterator;function Lr(t){return t===null||typeof t!="object"?null:(t=iu&&t[iu]||t["@@iterator"],typeof t=="function"?t:null)}var Z=Object.assign,pa;function zr(t){if(pa===void 0)try{throw Error()}catch(n){var e=n.stack.trim().match(/\n( *(at )?)/);pa=e&&e[1]||""}return`
`+pa+t}var fa=!1;function ga(t,e){if(!t||fa)return"";fa=!0;var n=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{if(e)if(e=function(){throw Error()},Object.defineProperty(e.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(e,[])}catch(d){var r=d}Reflect.construct(t,[],e)}else{try{e.call()}catch(d){r=d}t.call(e.prototype)}else{try{throw Error()}catch(d){r=d}t()}}catch(d){if(d&&r&&typeof d.stack=="string"){for(var i=d.stack.split(`
`),o=r.stack.split(`
`),s=i.length-1,l=o.length-1;1<=s&&0<=l&&i[s]!==o[l];)l--;for(;1<=s&&0<=l;s--,l--)if(i[s]!==o[l]){if(s!==1||l!==1)do if(s--,l--,0>l||i[s]!==o[l]){var c=`
`+i[s].replace(" at new "," at ");return t.displayName&&c.includes("<anonymous>")&&(c=c.replace("<anonymous>",t.displayName)),c}while(1<=s&&0<=l);break}}}finally{fa=!1,Error.prepareStackTrace=n}return(t=t?t.displayName||t.name:"")?zr(t):""}function wx(t){switch(t.tag){case 5:return zr(t.type);case 16:return zr("Lazy");case 13:return zr("Suspense");case 19:return zr("SuspenseList");case 0:case 2:case 15:return t=ga(t.type,!1),t;case 11:return t=ga(t.type.render,!1),t;case 1:return t=ga(t.type,!0),t;default:return""}}function ma(t){if(t==null)return null;if(typeof t=="function")return t.displayName||t.name||null;if(typeof t=="string")return t;switch(t){case Wn:return"Fragment";case Hn:return"Portal";case la:return"Profiler";case aa:return"StrictMode";case da:return"Suspense";case ua:return"SuspenseList"}if(typeof t=="object")switch(t.$$typeof){case nu:return(t.displayName||"Context")+".Consumer";case tu:return(t._context.displayName||"Context")+".Provider";case ca:var e=t.render;return t=t.displayName,t||(t=e.displayName||e.name||"",t=t!==""?"ForwardRef("+t+")":"ForwardRef"),t;case ha:return e=t.displayName||null,e!==null?e:ma(t.type)||"Memo";case zt:e=t._payload,t=t._init;try{return ma(t(e))}catch{}}return null}function vx(t){var e=t.type;switch(t.tag){case 24:return"Cache";case 9:return(e.displayName||"Context")+".Consumer";case 10:return(e._context.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return t=e.render,t=t.displayName||t.name||"",e.displayName||(t!==""?"ForwardRef("+t+")":"ForwardRef");case 7:return"Fragment";case 5:return e;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return ma(e);case 8:return e===aa?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 17:case 2:case 14:case 15:if(typeof e=="function")return e.displayName||e.name||null;if(typeof e=="string")return e}return null}function Bt(t){switch(typeof t){case"boolean":case"number":case"string":case"undefined":return t;case"object":return t;default:return""}}function ou(t){var e=t.type;return(t=t.nodeName)&&t.toLowerCase()==="input"&&(e==="checkbox"||e==="radio")}function bx(t){var e=ou(t)?"checked":"value",n=Object.getOwnPropertyDescriptor(t.constructor.prototype,e),r=""+t[e];if(!t.hasOwnProperty(e)&&typeof n<"u"&&typeof n.get=="function"&&typeof n.set=="function"){var i=n.get,o=n.set;return Object.defineProperty(t,e,{configurable:!0,get:function(){return i.call(this)},set:function(s){r=""+s,o.call(this,s)}}),Object.defineProperty(t,e,{enumerable:n.enumerable}),{getValue:function(){return r},setValue:function(s){r=""+s},stopTracking:function(){t._valueTracker=null,delete t[e]}}}}function Ki(t){t._valueTracker||(t._valueTracker=bx(t))}function su(t){if(!t)return!1;var e=t._valueTracker;if(!e)return!0;var n=e.getValue(),r="";return t&&(r=ou(t)?t.checked?"true":"false":t.value),t=r,t!==n?(e.setValue(t),!0):!1}function Qi(t){if(t=t||(typeof document<"u"?document:void 0),typeof t>"u")return null;try{return t.activeElement||t.body}catch{return t.body}}function xa(t,e){var n=e.checked;return Z({},e,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:n??t._wrapperState.initialChecked})}function au(t,e){var n=e.defaultValue==null?"":e.defaultValue,r=e.checked!=null?e.checked:e.defaultChecked;n=Bt(e.value!=null?e.value:n),t._wrapperState={initialChecked:r,initialValue:n,controlled:e.type==="checkbox"||e.type==="radio"?e.checked!=null:e.value!=null}}function lu(t,e){e=e.checked,e!=null&&sa(t,"checked",e,!1)}function ya(t,e){lu(t,e);var n=Bt(e.value),r=e.type;if(n!=null)r==="number"?(n===0&&t.value===""||t.value!=n)&&(t.value=""+n):t.value!==""+n&&(t.value=""+n);else if(r==="submit"||r==="reset"){t.removeAttribute("value");return}e.hasOwnProperty("value")?wa(t,e.type,n):e.hasOwnProperty("defaultValue")&&wa(t,e.type,Bt(e.defaultValue)),e.checked==null&&e.defaultChecked!=null&&(t.defaultChecked=!!e.defaultChecked)}function cu(t,e,n){if(e.hasOwnProperty("value")||e.hasOwnProperty("defaultValue")){var r=e.type;if(!(r!=="submit"&&r!=="reset"||e.value!==void 0&&e.value!==null))return;e=""+t._wrapperState.initialValue,n||e===t.value||(t.value=e),t.defaultValue=e}n=t.name,n!==""&&(t.name=""),t.defaultChecked=!!t._wrapperState.initialChecked,n!==""&&(t.name=n)}function wa(t,e,n){(e!=="number"||Qi(t.ownerDocument)!==t)&&(n==null?t.defaultValue=""+t._wrapperState.initialValue:t.defaultValue!==""+n&&(t.defaultValue=""+n))}var Br=Array.isArray;function Un(t,e,n,r){if(t=t.options,e){e={};for(var i=0;i<n.length;i++)e["$"+n[i]]=!0;for(n=0;n<t.length;n++)i=e.hasOwnProperty("$"+t[n].value),t[n].selected!==i&&(t[n].selected=i),i&&r&&(t[n].defaultSelected=!0)}else{for(n=""+Bt(n),e=null,i=0;i<t.length;i++){if(t[i].value===n){t[i].selected=!0,r&&(t[i].defaultSelected=!0);return}e!==null||t[i].disabled||(e=t[i])}e!==null&&(e.selected=!0)}}function va(t,e){if(e.dangerouslySetInnerHTML!=null)throw Error(j(91));return Z({},e,{value:void 0,defaultValue:void 0,children:""+t._wrapperState.initialValue})}function du(t,e){var n=e.value;if(n==null){if(n=e.children,e=e.defaultValue,n!=null){if(e!=null)throw Error(j(92));if(Br(n)){if(1<n.length)throw Error(j(93));n=n[0]}e=n}e==null&&(e=""),n=e}t._wrapperState={initialValue:Bt(n)}}function uu(t,e){var n=Bt(e.value),r=Bt(e.defaultValue);n!=null&&(n=""+n,n!==t.value&&(t.value=n),e.defaultValue==null&&t.defaultValue!==n&&(t.defaultValue=n)),r!=null&&(t.defaultValue=""+r)}function hu(t){var e=t.textContent;e===t._wrapperState.initialValue&&e!==""&&e!==null&&(t.value=e)}function pu(t){switch(t){case"svg":return"http://www.w3.org/2000/svg";case"math":return"http://www.w3.org/1998/Math/MathML";default:return"http://www.w3.org/1999/xhtml"}}function ba(t,e){return t==null||t==="http://www.w3.org/1999/xhtml"?pu(e):t==="http://www.w3.org/2000/svg"&&e==="foreignObject"?"http://www.w3.org/1999/xhtml":t}var Xi,fu=function(t){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(e,n,r,i){MSApp.execUnsafeLocalFunction(function(){return t(e,n,r,i)})}:t}(function(t,e){if(t.namespaceURI!=="http://www.w3.org/2000/svg"||"innerHTML"in t)t.innerHTML=e;else{for(Xi=Xi||document.createElement("div"),Xi.innerHTML="<svg>"+e.valueOf().toString()+"</svg>",e=Xi.firstChild;t.firstChild;)t.removeChild(t.firstChild);for(;e.firstChild;)t.appendChild(e.firstChild)}});function Nr(t,e){if(e){var n=t.firstChild;if(n&&n===t.lastChild&&n.nodeType===3){n.nodeValue=e;return}}t.textContent=e}var Fr={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0},Sx=["Webkit","ms","Moz","O"];Object.keys(Fr).forEach(function(t){Sx.forEach(function(e){e=e+t.charAt(0).toUpperCase()+t.substring(1),Fr[e]=Fr[t]})});function gu(t,e,n){return e==null||typeof e=="boolean"||e===""?"":n||typeof e!="number"||e===0||Fr.hasOwnProperty(t)&&Fr[t]?(""+e).trim():e+"px"}function mu(t,e){t=t.style;for(var n in e)if(e.hasOwnProperty(n)){var r=n.indexOf("--")===0,i=gu(n,e[n],r);n==="float"&&(n="cssFloat"),r?t.setProperty(n,i):t[n]=i}}var kx=Z({menuitem:!0},{area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0});function Sa(t,e){if(e){if(kx[t]&&(e.children!=null||e.dangerouslySetInnerHTML!=null))throw Error(j(137,t));if(e.dangerouslySetInnerHTML!=null){if(e.children!=null)throw Error(j(60));if(typeof e.dangerouslySetInnerHTML!="object"||!("__html"in e.dangerouslySetInnerHTML))throw Error(j(61))}if(e.style!=null&&typeof e.style!="object")throw Error(j(62))}}function ka(t,e){if(t.indexOf("-")===-1)return typeof e.is=="string";switch(t){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var $a=null;function Ca(t){return t=t.target||t.srcElement||window,t.correspondingUseElement&&(t=t.correspondingUseElement),t.nodeType===3?t.parentNode:t}var Ta=null,Gn=null,Vn=null;function xu(t){if(t=li(t)){if(typeof Ta!="function")throw Error(j(280));var e=t.stateNode;e&&(e=vo(e),Ta(t.stateNode,t.type,e))}}function yu(t){Gn?Vn?Vn.push(t):Vn=[t]:Gn=t}function wu(){if(Gn){var t=Gn,e=Vn;if(Vn=Gn=null,xu(t),e)for(t=0;t<e.length;t++)xu(e[t])}}function vu(t,e){return t(e)}function bu(){}var Ma=!1;function Su(t,e,n){if(Ma)return t(e,n);Ma=!0;try{return vu(t,e,n)}finally{Ma=!1,(Gn!==null||Vn!==null)&&(bu(),wu())}}function _r(t,e){var n=t.stateNode;if(n===null)return null;var r=vo(n);if(r===null)return null;n=r[e];e:switch(e){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(r=!r.disabled)||(t=t.type,r=!(t==="button"||t==="input"||t==="select"||t==="textarea")),t=!r;break e;default:t=!1}if(t)return null;if(n&&typeof n!="function")throw Error(j(231,e,typeof n));return n}var ja=!1;if(vt)try{var Hr={};Object.defineProperty(Hr,"passive",{get:function(){ja=!0}}),window.addEventListener("test",Hr,Hr),window.removeEventListener("test",Hr,Hr)}catch{ja=!1}function $x(t,e,n,r,i,o,s,l,c){var d=Array.prototype.slice.call(arguments,3);try{e.apply(n,d)}catch(h){this.onError(h)}}var Wr=!1,Ji=null,qi=!1,Ea=null,Cx={onError:function(t){Wr=!0,Ji=t}};function Tx(t,e,n,r,i,o,s,l,c){Wr=!1,Ji=null,$x.apply(Cx,arguments)}function Mx(t,e,n,r,i,o,s,l,c){if(Tx.apply(this,arguments),Wr){if(Wr){var d=Ji;Wr=!1,Ji=null}else throw Error(j(198));qi||(qi=!0,Ea=d)}}function pn(t){var e=t,n=t;if(t.alternate)for(;e.return;)e=e.return;else{t=e;do e=t,e.flags&4098&&(n=e.return),t=e.return;while(t)}return e.tag===3?n:null}function ku(t){if(t.tag===13){var e=t.memoizedState;if(e===null&&(t=t.alternate,t!==null&&(e=t.memoizedState)),e!==null)return e.dehydrated}return null}function $u(t){if(pn(t)!==t)throw Error(j(188))}function jx(t){var e=t.alternate;if(!e){if(e=pn(t),e===null)throw Error(j(188));return e!==t?null:t}for(var n=t,r=e;;){var i=n.return;if(i===null)break;var o=i.alternate;if(o===null){if(r=i.return,r!==null){n=r;continue}break}if(i.child===o.child){for(o=i.child;o;){if(o===n)return $u(i),t;if(o===r)return $u(i),e;o=o.sibling}throw Error(j(188))}if(n.return!==r.return)n=i,r=o;else{for(var s=!1,l=i.child;l;){if(l===n){s=!0,n=i,r=o;break}if(l===r){s=!0,r=i,n=o;break}l=l.sibling}if(!s){for(l=o.child;l;){if(l===n){s=!0,n=o,r=i;break}if(l===r){s=!0,r=o,n=i;break}l=l.sibling}if(!s)throw Error(j(189))}}if(n.alternate!==r)throw Error(j(190))}if(n.tag!==3)throw Error(j(188));return n.stateNode.current===n?t:e}function Cu(t){return t=jx(t),t!==null?Tu(t):null}function Tu(t){if(t.tag===5||t.tag===6)return t;for(t=t.child;t!==null;){var e=Tu(t);if(e!==null)return e;t=t.sibling}return null}var Mu=ze.unstable_scheduleCallback,ju=ze.unstable_cancelCallback,Ex=ze.unstable_shouldYield,Dx=ze.unstable_requestPaint,oe=ze.unstable_now,Px=ze.unstable_getCurrentPriorityLevel,Da=ze.unstable_ImmediatePriority,Eu=ze.unstable_UserBlockingPriority,Zi=ze.unstable_NormalPriority,Ax=ze.unstable_LowPriority,Du=ze.unstable_IdlePriority,eo=null,ft=null;function Rx(t){if(ft&&typeof ft.onCommitFiberRoot=="function")try{ft.onCommitFiberRoot(eo,t,void 0,(t.current.flags&128)===128)}catch{}}var et=Math.clz32?Math.clz32:Lx,Ix=Math.log,Ox=Math.LN2;function Lx(t){return t>>>=0,t===0?32:31-(Ix(t)/Ox|0)|0}var to=64,no=4194304;function Ur(t){switch(t&-t){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return t&4194240;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return t&130023424;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 1073741824;default:return t}}function ro(t,e){var n=t.pendingLanes;if(n===0)return 0;var r=0,i=t.suspendedLanes,o=t.pingedLanes,s=n&268435455;if(s!==0){var l=s&~i;l!==0?r=Ur(l):(o&=s,o!==0&&(r=Ur(o)))}else s=n&~i,s!==0?r=Ur(s):o!==0&&(r=Ur(o));if(r===0)return 0;if(e!==0&&e!==r&&!(e&i)&&(i=r&-r,o=e&-e,i>=o||i===16&&(o&4194240)!==0))return e;if(r&4&&(r|=n&16),e=t.entangledLanes,e!==0)for(t=t.entanglements,e&=r;0<e;)n=31-et(e),i=1<<n,r|=t[n],e&=~i;return r}function zx(t,e){switch(t){case 1:case 2:case 4:return e+250;case 8:case 16:case 32:case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return e+5e3;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return-1;case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function Bx(t,e){for(var n=t.suspendedLanes,r=t.pingedLanes,i=t.expirationTimes,o=t.pendingLanes;0<o;){var s=31-et(o),l=1<<s,c=i[s];c===-1?(!(l&n)||l&r)&&(i[s]=zx(l,e)):c<=e&&(t.expiredLanes|=l),o&=~l}}function Pa(t){return t=t.pendingLanes&-1073741825,t!==0?t:t&1073741824?1073741824:0}function Pu(){var t=to;return to<<=1,!(to&4194240)&&(to=64),t}function Aa(t){for(var e=[],n=0;31>n;n++)e.push(t);return e}function Gr(t,e,n){t.pendingLanes|=e,e!==536870912&&(t.suspendedLanes=0,t.pingedLanes=0),t=t.eventTimes,e=31-et(e),t[e]=n}function Nx(t,e){var n=t.pendingLanes&~e;t.pendingLanes=e,t.suspendedLanes=0,t.pingedLanes=0,t.expiredLanes&=e,t.mutableReadLanes&=e,t.entangledLanes&=e,e=t.entanglements;var r=t.eventTimes;for(t=t.expirationTimes;0<n;){var i=31-et(n),o=1<<i;e[i]=0,r[i]=-1,t[i]=-1,n&=~o}}function Ra(t,e){var n=t.entangledLanes|=e;for(t=t.entanglements;n;){var r=31-et(n),i=1<<r;i&e|t[r]&e&&(t[r]|=e),n&=~i}}var U=0;function Au(t){return t&=-t,1<t?4<t?t&268435455?16:536870912:4:1}var Ru,Ia,Iu,Ou,Lu,Oa=!1,io=[],Nt=null,Ft=null,_t=null,Vr=new Map,Yr=new Map,Ht=[],Fx="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");function zu(t,e){switch(t){case"focusin":case"focusout":Nt=null;break;case"dragenter":case"dragleave":Ft=null;break;case"mouseover":case"mouseout":_t=null;break;case"pointerover":case"pointerout":Vr.delete(e.pointerId);break;case"gotpointercapture":case"lostpointercapture":Yr.delete(e.pointerId)}}function Kr(t,e,n,r,i,o){return t===null||t.nativeEvent!==o?(t={blockedOn:e,domEventName:n,eventSystemFlags:r,nativeEvent:o,targetContainers:[i]},e!==null&&(e=li(e),e!==null&&Ia(e)),t):(t.eventSystemFlags|=r,e=t.targetContainers,i!==null&&e.indexOf(i)===-1&&e.push(i),t)}function _x(t,e,n,r,i){switch(e){case"focusin":return Nt=Kr(Nt,t,e,n,r,i),!0;case"dragenter":return Ft=Kr(Ft,t,e,n,r,i),!0;case"mouseover":return _t=Kr(_t,t,e,n,r,i),!0;case"pointerover":var o=i.pointerId;return Vr.set(o,Kr(Vr.get(o)||null,t,e,n,r,i)),!0;case"gotpointercapture":return o=i.pointerId,Yr.set(o,Kr(Yr.get(o)||null,t,e,n,r,i)),!0}return!1}function Bu(t){var e=fn(t.target);if(e!==null){var n=pn(e);if(n!==null){if(e=n.tag,e===13){if(e=ku(n),e!==null){t.blockedOn=e,Lu(t.priority,function(){Iu(n)});return}}else if(e===3&&n.stateNode.current.memoizedState.isDehydrated){t.blockedOn=n.tag===3?n.stateNode.containerInfo:null;return}}}t.blockedOn=null}function oo(t){if(t.blockedOn!==null)return!1;for(var e=t.targetContainers;0<e.length;){var n=za(t.domEventName,t.eventSystemFlags,e[0],t.nativeEvent);if(n===null){n=t.nativeEvent;var r=new n.constructor(n.type,n);$a=r,n.target.dispatchEvent(r),$a=null}else return e=li(n),e!==null&&Ia(e),t.blockedOn=n,!1;e.shift()}return!0}function Nu(t,e,n){oo(t)&&n.delete(e)}function Hx(){Oa=!1,Nt!==null&&oo(Nt)&&(Nt=null),Ft!==null&&oo(Ft)&&(Ft=null),_t!==null&&oo(_t)&&(_t=null),Vr.forEach(Nu),Yr.forEach(Nu)}function Qr(t,e){t.blockedOn===e&&(t.blockedOn=null,Oa||(Oa=!0,ze.unstable_scheduleCallback(ze.unstable_NormalPriority,Hx)))}function Xr(t){function e(i){return Qr(i,t)}if(0<io.length){Qr(io[0],t);for(var n=1;n<io.length;n++){var r=io[n];r.blockedOn===t&&(r.blockedOn=null)}}for(Nt!==null&&Qr(Nt,t),Ft!==null&&Qr(Ft,t),_t!==null&&Qr(_t,t),Vr.forEach(e),Yr.forEach(e),n=0;n<Ht.length;n++)r=Ht[n],r.blockedOn===t&&(r.blockedOn=null);for(;0<Ht.length&&(n=Ht[0],n.blockedOn===null);)Bu(n),n.blockedOn===null&&Ht.shift()}var Yn=bt.ReactCurrentBatchConfig,so=!0;function Wx(t,e,n,r){var i=U,o=Yn.transition;Yn.transition=null;try{U=1,La(t,e,n,r)}finally{U=i,Yn.transition=o}}function Ux(t,e,n,r){var i=U,o=Yn.transition;Yn.transition=null;try{U=4,La(t,e,n,r)}finally{U=i,Yn.transition=o}}function La(t,e,n,r){if(so){var i=za(t,e,n,r);if(i===null)el(t,e,r,ao,n),zu(t,r);else if(_x(i,t,e,n,r))r.stopPropagation();else if(zu(t,r),e&4&&-1<Fx.indexOf(t)){for(;i!==null;){var o=li(i);if(o!==null&&Ru(o),o=za(t,e,n,r),o===null&&el(t,e,r,ao,n),o===i)break;i=o}i!==null&&r.stopPropagation()}else el(t,e,r,null,n)}}var ao=null;function za(t,e,n,r){if(ao=null,t=Ca(r),t=fn(t),t!==null)if(e=pn(t),e===null)t=null;else if(n=e.tag,n===13){if(t=ku(e),t!==null)return t;t=null}else if(n===3){if(e.stateNode.current.memoizedState.isDehydrated)return e.tag===3?e.stateNode.containerInfo:null;t=null}else e!==t&&(t=null);return ao=t,null}function Fu(t){switch(t){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return 1;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return 4;case"message":switch(Px()){case Da:return 1;case Eu:return 4;case Zi:case Ax:return 16;case Du:return 536870912;default:return 16}default:return 16}}var Wt=null,Ba=null,lo=null;function _u(){if(lo)return lo;var t,e=Ba,n=e.length,r,i="value"in Wt?Wt.value:Wt.textContent,o=i.length;for(t=0;t<n&&e[t]===i[t];t++);var s=n-t;for(r=1;r<=s&&e[n-r]===i[o-r];r++);return lo=i.slice(t,1<r?1-r:void 0)}function co(t){var e=t.keyCode;return"charCode"in t?(t=t.charCode,t===0&&e===13&&(t=13)):t=e,t===10&&(t=13),32<=t||t===13?t:0}function uo(){return!0}function Hu(){return!1}function Be(t){function e(n,r,i,o,s){this._reactName=n,this._targetInst=i,this.type=r,this.nativeEvent=o,this.target=s,this.currentTarget=null;for(var l in t)t.hasOwnProperty(l)&&(n=t[l],this[l]=n?n(o):o[l]);return this.isDefaultPrevented=(o.defaultPrevented!=null?o.defaultPrevented:o.returnValue===!1)?uo:Hu,this.isPropagationStopped=Hu,this}return Z(e.prototype,{preventDefault:function(){this.defaultPrevented=!0;var n=this.nativeEvent;n&&(n.preventDefault?n.preventDefault():typeof n.returnValue!="unknown"&&(n.returnValue=!1),this.isDefaultPrevented=uo)},stopPropagation:function(){var n=this.nativeEvent;n&&(n.stopPropagation?n.stopPropagation():typeof n.cancelBubble!="unknown"&&(n.cancelBubble=!0),this.isPropagationStopped=uo)},persist:function(){},isPersistent:uo}),e}var Kn={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(t){return t.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Na=Be(Kn),Jr=Z({},Kn,{view:0,detail:0}),Gx=Be(Jr),Fa,_a,qr,ho=Z({},Jr,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:Wa,button:0,buttons:0,relatedTarget:function(t){return t.relatedTarget===void 0?t.fromElement===t.srcElement?t.toElement:t.fromElement:t.relatedTarget},movementX:function(t){return"movementX"in t?t.movementX:(t!==qr&&(qr&&t.type==="mousemove"?(Fa=t.screenX-qr.screenX,_a=t.screenY-qr.screenY):_a=Fa=0,qr=t),Fa)},movementY:function(t){return"movementY"in t?t.movementY:_a}}),Wu=Be(ho),Vx=Z({},ho,{dataTransfer:0}),Yx=Be(Vx),Kx=Z({},Jr,{relatedTarget:0}),Ha=Be(Kx),Qx=Z({},Kn,{animationName:0,elapsedTime:0,pseudoElement:0}),Xx=Be(Qx),Jx=Z({},Kn,{clipboardData:function(t){return"clipboardData"in t?t.clipboardData:window.clipboardData}}),qx=Be(Jx),Zx=Z({},Kn,{data:0}),Uu=Be(Zx),e0={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},t0={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},n0={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function r0(t){var e=this.nativeEvent;return e.getModifierState?e.getModifierState(t):(t=n0[t])?!!e[t]:!1}function Wa(){return r0}var i0=Z({},Jr,{key:function(t){if(t.key){var e=e0[t.key]||t.key;if(e!=="Unidentified")return e}return t.type==="keypress"?(t=co(t),t===13?"Enter":String.fromCharCode(t)):t.type==="keydown"||t.type==="keyup"?t0[t.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:Wa,charCode:function(t){return t.type==="keypress"?co(t):0},keyCode:function(t){return t.type==="keydown"||t.type==="keyup"?t.keyCode:0},which:function(t){return t.type==="keypress"?co(t):t.type==="keydown"||t.type==="keyup"?t.keyCode:0}}),o0=Be(i0),s0=Z({},ho,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),Gu=Be(s0),a0=Z({},Jr,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:Wa}),l0=Be(a0),c0=Z({},Kn,{propertyName:0,elapsedTime:0,pseudoElement:0}),d0=Be(c0),u0=Z({},ho,{deltaX:function(t){return"deltaX"in t?t.deltaX:"wheelDeltaX"in t?-t.wheelDeltaX:0},deltaY:function(t){return"deltaY"in t?t.deltaY:"wheelDeltaY"in t?-t.wheelDeltaY:"wheelDelta"in t?-t.wheelDelta:0},deltaZ:0,deltaMode:0}),h0=Be(u0),p0=[9,13,27,32],Ua=vt&&"CompositionEvent"in window,Zr=null;vt&&"documentMode"in document&&(Zr=document.documentMode);var f0=vt&&"TextEvent"in window&&!Zr,Vu=vt&&(!Ua||Zr&&8<Zr&&11>=Zr),Yu=" ",Ku=!1;function Qu(t,e){switch(t){case"keyup":return p0.indexOf(e.keyCode)!==-1;case"keydown":return e.keyCode!==229;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function Xu(t){return t=t.detail,typeof t=="object"&&"data"in t?t.data:null}var Qn=!1;function g0(t,e){switch(t){case"compositionend":return Xu(e);case"keypress":return e.which!==32?null:(Ku=!0,Yu);case"textInput":return t=e.data,t===Yu&&Ku?null:t;default:return null}}function m0(t,e){if(Qn)return t==="compositionend"||!Ua&&Qu(t,e)?(t=_u(),lo=Ba=Wt=null,Qn=!1,t):null;switch(t){case"paste":return null;case"keypress":if(!(e.ctrlKey||e.altKey||e.metaKey)||e.ctrlKey&&e.altKey){if(e.char&&1<e.char.length)return e.char;if(e.which)return String.fromCharCode(e.which)}return null;case"compositionend":return Vu&&e.locale!=="ko"?null:e.data;default:return null}}var x0={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function Ju(t){var e=t&&t.nodeName&&t.nodeName.toLowerCase();return e==="input"?!!x0[t.type]:e==="textarea"}function qu(t,e,n,r){yu(r),e=xo(e,"onChange"),0<e.length&&(n=new Na("onChange","change",null,n,r),t.push({event:n,listeners:e}))}var ei=null,ti=null;function y0(t){mh(t,0)}function po(t){var e=er(t);if(su(e))return t}function w0(t,e){if(t==="change")return e}var Zu=!1;if(vt){var Ga;if(vt){var Va="oninput"in document;if(!Va){var eh=document.createElement("div");eh.setAttribute("oninput","return;"),Va=typeof eh.oninput=="function"}Ga=Va}else Ga=!1;Zu=Ga&&(!document.documentMode||9<document.documentMode)}function th(){ei&&(ei.detachEvent("onpropertychange",nh),ti=ei=null)}function nh(t){if(t.propertyName==="value"&&po(ti)){var e=[];qu(e,ti,t,Ca(t)),Su(y0,e)}}function v0(t,e,n){t==="focusin"?(th(),ei=e,ti=n,ei.attachEvent("onpropertychange",nh)):t==="focusout"&&th()}function b0(t){if(t==="selectionchange"||t==="keyup"||t==="keydown")return po(ti)}function S0(t,e){if(t==="click")return po(e)}function k0(t,e){if(t==="input"||t==="change")return po(e)}function $0(t,e){return t===e&&(t!==0||1/t===1/e)||t!==t&&e!==e}var tt=typeof Object.is=="function"?Object.is:$0;function ni(t,e){if(tt(t,e))return!0;if(typeof t!="object"||t===null||typeof e!="object"||e===null)return!1;var n=Object.keys(t),r=Object.keys(e);if(n.length!==r.length)return!1;for(r=0;r<n.length;r++){var i=n[r];if(!ra.call(e,i)||!tt(t[i],e[i]))return!1}return!0}function rh(t){for(;t&&t.firstChild;)t=t.firstChild;return t}function ih(t,e){var n=rh(t);t=0;for(var r;n;){if(n.nodeType===3){if(r=t+n.textContent.length,t<=e&&r>=e)return{node:n,offset:e-t};t=r}e:{for(;n;){if(n.nextSibling){n=n.nextSibling;break e}n=n.parentNode}n=void 0}n=rh(n)}}function oh(t,e){return t&&e?t===e?!0:t&&t.nodeType===3?!1:e&&e.nodeType===3?oh(t,e.parentNode):"contains"in t?t.contains(e):t.compareDocumentPosition?!!(t.compareDocumentPosition(e)&16):!1:!1}function sh(){for(var t=window,e=Qi();e instanceof t.HTMLIFrameElement;){try{var n=typeof e.contentWindow.location.href=="string"}catch{n=!1}if(n)t=e.contentWindow;else break;e=Qi(t.document)}return e}function Ya(t){var e=t&&t.nodeName&&t.nodeName.toLowerCase();return e&&(e==="input"&&(t.type==="text"||t.type==="search"||t.type==="tel"||t.type==="url"||t.type==="password")||e==="textarea"||t.contentEditable==="true")}function C0(t){var e=sh(),n=t.focusedElem,r=t.selectionRange;if(e!==n&&n&&n.ownerDocument&&oh(n.ownerDocument.documentElement,n)){if(r!==null&&Ya(n)){if(e=r.start,t=r.end,t===void 0&&(t=e),"selectionStart"in n)n.selectionStart=e,n.selectionEnd=Math.min(t,n.value.length);else if(t=(e=n.ownerDocument||document)&&e.defaultView||window,t.getSelection){t=t.getSelection();var i=n.textContent.length,o=Math.min(r.start,i);r=r.end===void 0?o:Math.min(r.end,i),!t.extend&&o>r&&(i=r,r=o,o=i),i=ih(n,o);var s=ih(n,r);i&&s&&(t.rangeCount!==1||t.anchorNode!==i.node||t.anchorOffset!==i.offset||t.focusNode!==s.node||t.focusOffset!==s.offset)&&(e=e.createRange(),e.setStart(i.node,i.offset),t.removeAllRanges(),o>r?(t.addRange(e),t.extend(s.node,s.offset)):(e.setEnd(s.node,s.offset),t.addRange(e)))}}for(e=[],t=n;t=t.parentNode;)t.nodeType===1&&e.push({element:t,left:t.scrollLeft,top:t.scrollTop});for(typeof n.focus=="function"&&n.focus(),n=0;n<e.length;n++)t=e[n],t.element.scrollLeft=t.left,t.element.scrollTop=t.top}}var T0=vt&&"documentMode"in document&&11>=document.documentMode,Xn=null,Ka=null,ri=null,Qa=!1;function ah(t,e,n){var r=n.window===n?n.document:n.nodeType===9?n:n.ownerDocument;Qa||Xn==null||Xn!==Qi(r)||(r=Xn,"selectionStart"in r&&Ya(r)?r={start:r.selectionStart,end:r.selectionEnd}:(r=(r.ownerDocument&&r.ownerDocument.defaultView||window).getSelection(),r={anchorNode:r.anchorNode,anchorOffset:r.anchorOffset,focusNode:r.focusNode,focusOffset:r.focusOffset}),ri&&ni(ri,r)||(ri=r,r=xo(Ka,"onSelect"),0<r.length&&(e=new Na("onSelect","select",null,e,n),t.push({event:e,listeners:r}),e.target=Xn)))}function fo(t,e){var n={};return n[t.toLowerCase()]=e.toLowerCase(),n["Webkit"+t]="webkit"+e,n["Moz"+t]="moz"+e,n}var Jn={animationend:fo("Animation","AnimationEnd"),animationiteration:fo("Animation","AnimationIteration"),animationstart:fo("Animation","AnimationStart"),transitionend:fo("Transition","TransitionEnd")},Xa={},lh={};vt&&(lh=document.createElement("div").style,"AnimationEvent"in window||(delete Jn.animationend.animation,delete Jn.animationiteration.animation,delete Jn.animationstart.animation),"TransitionEvent"in window||delete Jn.transitionend.transition);function go(t){if(Xa[t])return Xa[t];if(!Jn[t])return t;var e=Jn[t],n;for(n in e)if(e.hasOwnProperty(n)&&n in lh)return Xa[t]=e[n];return t}var ch=go("animationend"),dh=go("animationiteration"),uh=go("animationstart"),hh=go("transitionend"),ph=new Map,fh="abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");function Ut(t,e){ph.set(t,e),hn(e,[t])}for(var Ja=0;Ja<fh.length;Ja++){var qa=fh[Ja],M0=qa.toLowerCase(),j0=qa[0].toUpperCase()+qa.slice(1);Ut(M0,"on"+j0)}Ut(ch,"onAnimationEnd"),Ut(dh,"onAnimationIteration"),Ut(uh,"onAnimationStart"),Ut("dblclick","onDoubleClick"),Ut("focusin","onFocus"),Ut("focusout","onBlur"),Ut(hh,"onTransitionEnd"),_n("onMouseEnter",["mouseout","mouseover"]),_n("onMouseLeave",["mouseout","mouseover"]),_n("onPointerEnter",["pointerout","pointerover"]),_n("onPointerLeave",["pointerout","pointerover"]),hn("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),hn("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),hn("onBeforeInput",["compositionend","keypress","textInput","paste"]),hn("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),hn("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),hn("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var ii="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),E0=new Set("cancel close invalid load scroll toggle".split(" ").concat(ii));function gh(t,e,n){var r=t.type||"unknown-event";t.currentTarget=n,Mx(r,e,void 0,t),t.currentTarget=null}function mh(t,e){e=(e&4)!==0;for(var n=0;n<t.length;n++){var r=t[n],i=r.event;r=r.listeners;e:{var o=void 0;if(e)for(var s=r.length-1;0<=s;s--){var l=r[s],c=l.instance,d=l.currentTarget;if(l=l.listener,c!==o&&i.isPropagationStopped())break e;gh(i,l,d),o=c}else for(s=0;s<r.length;s++){if(l=r[s],c=l.instance,d=l.currentTarget,l=l.listener,c!==o&&i.isPropagationStopped())break e;gh(i,l,d),o=c}}}if(qi)throw t=Ea,qi=!1,Ea=null,t}function Q(t,e){var n=e[sl];n===void 0&&(n=e[sl]=new Set);var r=t+"__bubble";n.has(r)||(xh(e,t,2,!1),n.add(r))}function Za(t,e,n){var r=0;e&&(r|=4),xh(n,t,r,e)}var mo="_reactListening"+Math.random().toString(36).slice(2);function oi(t){if(!t[mo]){t[mo]=!0,qd.forEach(function(n){n!=="selectionchange"&&(E0.has(n)||Za(n,!1,t),Za(n,!0,t))});var e=t.nodeType===9?t:t.ownerDocument;e===null||e[mo]||(e[mo]=!0,Za("selectionchange",!1,e))}}function xh(t,e,n,r){switch(Fu(e)){case 1:var i=Wx;break;case 4:i=Ux;break;default:i=La}n=i.bind(null,e,n,t),i=void 0,!ja||e!=="touchstart"&&e!=="touchmove"&&e!=="wheel"||(i=!0),r?i!==void 0?t.addEventListener(e,n,{capture:!0,passive:i}):t.addEventListener(e,n,!0):i!==void 0?t.addEventListener(e,n,{passive:i}):t.addEventListener(e,n,!1)}function el(t,e,n,r,i){var o=r;if(!(e&1)&&!(e&2)&&r!==null)e:for(;;){if(r===null)return;var s=r.tag;if(s===3||s===4){var l=r.stateNode.containerInfo;if(l===i||l.nodeType===8&&l.parentNode===i)break;if(s===4)for(s=r.return;s!==null;){var c=s.tag;if((c===3||c===4)&&(c=s.stateNode.containerInfo,c===i||c.nodeType===8&&c.parentNode===i))return;s=s.return}for(;l!==null;){if(s=fn(l),s===null)return;if(c=s.tag,c===5||c===6){r=o=s;continue e}l=l.parentNode}}r=r.return}Su(function(){var d=o,h=Ca(n),p=[];e:{var f=ph.get(t);if(f!==void 0){var v=Na,x=t;switch(t){case"keypress":if(co(n)===0)break e;case"keydown":case"keyup":v=o0;break;case"focusin":x="focus",v=Ha;break;case"focusout":x="blur",v=Ha;break;case"beforeblur":case"afterblur":v=Ha;break;case"click":if(n.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":v=Wu;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":v=Yx;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":v=l0;break;case ch:case dh:case uh:v=Xx;break;case hh:v=d0;break;case"scroll":v=Gx;break;case"wheel":v=h0;break;case"copy":case"cut":case"paste":v=qx;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":v=Gu}var b=(e&4)!==0,M=!b&&t==="scroll",w=b?f!==null?f+"Capture":null:f;b=[];for(var m=d,y;m!==null;){y=m;var S=y.stateNode;if(y.tag===5&&S!==null&&(y=S,w!==null&&(S=_r(m,w),S!=null&&b.push(si(m,S,y)))),M)break;m=m.return}0<b.length&&(f=new v(f,x,null,n,h),p.push({event:f,listeners:b}))}}if(!(e&7)){e:{if(f=t==="mouseover"||t==="pointerover",v=t==="mouseout"||t==="pointerout",f&&n!==$a&&(x=n.relatedTarget||n.fromElement)&&(fn(x)||x[St]))break e;if((v||f)&&(f=h.window===h?h:(f=h.ownerDocument)?f.defaultView||f.parentWindow:window,v?(x=n.relatedTarget||n.toElement,v=d,x=x?fn(x):null,x!==null&&(M=pn(x),x!==M||x.tag!==5&&x.tag!==6)&&(x=null)):(v=null,x=d),v!==x)){if(b=Wu,S="onMouseLeave",w="onMouseEnter",m="mouse",(t==="pointerout"||t==="pointerover")&&(b=Gu,S="onPointerLeave",w="onPointerEnter",m="pointer"),M=v==null?f:er(v),y=x==null?f:er(x),f=new b(S,m+"leave",v,n,h),f.target=M,f.relatedTarget=y,S=null,fn(h)===d&&(b=new b(w,m+"enter",x,n,h),b.target=y,b.relatedTarget=M,S=b),M=S,v&&x)t:{for(b=v,w=x,m=0,y=b;y;y=qn(y))m++;for(y=0,S=w;S;S=qn(S))y++;for(;0<m-y;)b=qn(b),m--;for(;0<y-m;)w=qn(w),y--;for(;m--;){if(b===w||w!==null&&b===w.alternate)break t;b=qn(b),w=qn(w)}b=null}else b=null;v!==null&&yh(p,f,v,b,!1),x!==null&&M!==null&&yh(p,M,x,b,!0)}}e:{if(f=d?er(d):window,v=f.nodeName&&f.nodeName.toLowerCase(),v==="select"||v==="input"&&f.type==="file")var T=w0;else if(Ju(f))if(Zu)T=k0;else{T=b0;var C=v0}else(v=f.nodeName)&&v.toLowerCase()==="input"&&(f.type==="checkbox"||f.type==="radio")&&(T=S0);if(T&&(T=T(t,d))){qu(p,T,n,h);break e}C&&C(t,f,d),t==="focusout"&&(C=f._wrapperState)&&C.controlled&&f.type==="number"&&wa(f,"number",f.value)}switch(C=d?er(d):window,t){case"focusin":(Ju(C)||C.contentEditable==="true")&&(Xn=C,Ka=d,ri=null);break;case"focusout":ri=Ka=Xn=null;break;case"mousedown":Qa=!0;break;case"contextmenu":case"mouseup":case"dragend":Qa=!1,ah(p,n,h);break;case"selectionchange":if(T0)break;case"keydown":case"keyup":ah(p,n,h)}var k;if(Ua)e:{switch(t){case"compositionstart":var $="onCompositionStart";break e;case"compositionend":$="onCompositionEnd";break e;case"compositionupdate":$="onCompositionUpdate";break e}$=void 0}else Qn?Qu(t,n)&&($="onCompositionEnd"):t==="keydown"&&n.keyCode===229&&($="onCompositionStart");$&&(Vu&&n.locale!=="ko"&&(Qn||$!=="onCompositionStart"?$==="onCompositionEnd"&&Qn&&(k=_u()):(Wt=h,Ba="value"in Wt?Wt.value:Wt.textContent,Qn=!0)),C=xo(d,$),0<C.length&&($=new Uu($,t,null,n,h),p.push({event:$,listeners:C}),k?$.data=k:(k=Xu(n),k!==null&&($.data=k)))),(k=f0?g0(t,n):m0(t,n))&&(d=xo(d,"onBeforeInput"),0<d.length&&(h=new Uu("onBeforeInput","beforeinput",null,n,h),p.push({event:h,listeners:d}),h.data=k))}mh(p,e)})}function si(t,e,n){return{instance:t,listener:e,currentTarget:n}}function xo(t,e){for(var n=e+"Capture",r=[];t!==null;){var i=t,o=i.stateNode;i.tag===5&&o!==null&&(i=o,o=_r(t,n),o!=null&&r.unshift(si(t,o,i)),o=_r(t,e),o!=null&&r.push(si(t,o,i))),t=t.return}return r}function qn(t){if(t===null)return null;do t=t.return;while(t&&t.tag!==5);return t||null}function yh(t,e,n,r,i){for(var o=e._reactName,s=[];n!==null&&n!==r;){var l=n,c=l.alternate,d=l.stateNode;if(c!==null&&c===r)break;l.tag===5&&d!==null&&(l=d,i?(c=_r(n,o),c!=null&&s.unshift(si(n,c,l))):i||(c=_r(n,o),c!=null&&s.push(si(n,c,l)))),n=n.return}s.length!==0&&t.push({event:e,listeners:s})}var D0=/\r\n?/g,P0=/\u0000|\uFFFD/g;function wh(t){return(typeof t=="string"?t:""+t).replace(D0,`
`).replace(P0,"")}function yo(t,e,n){if(e=wh(e),wh(t)!==e&&n)throw Error(j(425))}function wo(){}var tl=null,nl=null;function rl(t,e){return t==="textarea"||t==="noscript"||typeof e.children=="string"||typeof e.children=="number"||typeof e.dangerouslySetInnerHTML=="object"&&e.dangerouslySetInnerHTML!==null&&e.dangerouslySetInnerHTML.__html!=null}var il=typeof setTimeout=="function"?setTimeout:void 0,A0=typeof clearTimeout=="function"?clearTimeout:void 0,vh=typeof Promise=="function"?Promise:void 0,R0=typeof queueMicrotask=="function"?queueMicrotask:typeof vh<"u"?function(t){return vh.resolve(null).then(t).catch(I0)}:il;function I0(t){setTimeout(function(){throw t})}function ol(t,e){var n=e,r=0;do{var i=n.nextSibling;if(t.removeChild(n),i&&i.nodeType===8)if(n=i.data,n==="/$"){if(r===0){t.removeChild(i),Xr(e);return}r--}else n!=="$"&&n!=="$?"&&n!=="$!"||r++;n=i}while(n);Xr(e)}function Gt(t){for(;t!=null;t=t.nextSibling){var e=t.nodeType;if(e===1||e===3)break;if(e===8){if(e=t.data,e==="$"||e==="$!"||e==="$?")break;if(e==="/$")return null}}return t}function bh(t){t=t.previousSibling;for(var e=0;t;){if(t.nodeType===8){var n=t.data;if(n==="$"||n==="$!"||n==="$?"){if(e===0)return t;e--}else n==="/$"&&e++}t=t.previousSibling}return null}var Zn=Math.random().toString(36).slice(2),gt="__reactFiber$"+Zn,ai="__reactProps$"+Zn,St="__reactContainer$"+Zn,sl="__reactEvents$"+Zn,O0="__reactListeners$"+Zn,L0="__reactHandles$"+Zn;function fn(t){var e=t[gt];if(e)return e;for(var n=t.parentNode;n;){if(e=n[St]||n[gt]){if(n=e.alternate,e.child!==null||n!==null&&n.child!==null)for(t=bh(t);t!==null;){if(n=t[gt])return n;t=bh(t)}return e}t=n,n=t.parentNode}return null}function li(t){return t=t[gt]||t[St],!t||t.tag!==5&&t.tag!==6&&t.tag!==13&&t.tag!==3?null:t}function er(t){if(t.tag===5||t.tag===6)return t.stateNode;throw Error(j(33))}function vo(t){return t[ai]||null}var al=[],tr=-1;function Vt(t){return{current:t}}function X(t){0>tr||(t.current=al[tr],al[tr]=null,tr--)}function Y(t,e){tr++,al[tr]=t.current,t.current=e}var Yt={},ye=Vt(Yt),Me=Vt(!1),gn=Yt;function nr(t,e){var n=t.type.contextTypes;if(!n)return Yt;var r=t.stateNode;if(r&&r.__reactInternalMemoizedUnmaskedChildContext===e)return r.__reactInternalMemoizedMaskedChildContext;var i={},o;for(o in n)i[o]=e[o];return r&&(t=t.stateNode,t.__reactInternalMemoizedUnmaskedChildContext=e,t.__reactInternalMemoizedMaskedChildContext=i),i}function je(t){return t=t.childContextTypes,t!=null}function bo(){X(Me),X(ye)}function Sh(t,e,n){if(ye.current!==Yt)throw Error(j(168));Y(ye,e),Y(Me,n)}function kh(t,e,n){var r=t.stateNode;if(e=e.childContextTypes,typeof r.getChildContext!="function")return n;r=r.getChildContext();for(var i in r)if(!(i in e))throw Error(j(108,vx(t)||"Unknown",i));return Z({},n,r)}function So(t){return t=(t=t.stateNode)&&t.__reactInternalMemoizedMergedChildContext||Yt,gn=ye.current,Y(ye,t),Y(Me,Me.current),!0}function $h(t,e,n){var r=t.stateNode;if(!r)throw Error(j(169));n?(t=kh(t,e,gn),r.__reactInternalMemoizedMergedChildContext=t,X(Me),X(ye),Y(ye,t)):X(Me),Y(Me,n)}var kt=null,ko=!1,ll=!1;function Ch(t){kt===null?kt=[t]:kt.push(t)}function z0(t){ko=!0,Ch(t)}function Kt(){if(!ll&&kt!==null){ll=!0;var t=0,e=U;try{var n=kt;for(U=1;t<n.length;t++){var r=n[t];do r=r(!0);while(r!==null)}kt=null,ko=!1}catch(i){throw kt!==null&&(kt=kt.slice(t+1)),Mu(Da,Kt),i}finally{U=e,ll=!1}}return null}var rr=[],ir=0,$o=null,Co=0,Ge=[],Ve=0,mn=null,$t=1,Ct="";function xn(t,e){rr[ir++]=Co,rr[ir++]=$o,$o=t,Co=e}function Th(t,e,n){Ge[Ve++]=$t,Ge[Ve++]=Ct,Ge[Ve++]=mn,mn=t;var r=$t;t=Ct;var i=32-et(r)-1;r&=~(1<<i),n+=1;var o=32-et(e)+i;if(30<o){var s=i-i%5;o=(r&(1<<s)-1).toString(32),r>>=s,i-=s,$t=1<<32-et(e)+i|n<<i|r,Ct=o+t}else $t=1<<o|n<<i|r,Ct=t}function cl(t){t.return!==null&&(xn(t,1),Th(t,1,0))}function dl(t){for(;t===$o;)$o=rr[--ir],rr[ir]=null,Co=rr[--ir],rr[ir]=null;for(;t===mn;)mn=Ge[--Ve],Ge[Ve]=null,Ct=Ge[--Ve],Ge[Ve]=null,$t=Ge[--Ve],Ge[Ve]=null}var Ne=null,Fe=null,q=!1,nt=null;function Mh(t,e){var n=Xe(5,null,null,0);n.elementType="DELETED",n.stateNode=e,n.return=t,e=t.deletions,e===null?(t.deletions=[n],t.flags|=16):e.push(n)}function jh(t,e){switch(t.tag){case 5:var n=t.type;return e=e.nodeType!==1||n.toLowerCase()!==e.nodeName.toLowerCase()?null:e,e!==null?(t.stateNode=e,Ne=t,Fe=Gt(e.firstChild),!0):!1;case 6:return e=t.pendingProps===""||e.nodeType!==3?null:e,e!==null?(t.stateNode=e,Ne=t,Fe=null,!0):!1;case 13:return e=e.nodeType!==8?null:e,e!==null?(n=mn!==null?{id:$t,overflow:Ct}:null,t.memoizedState={dehydrated:e,treeContext:n,retryLane:1073741824},n=Xe(18,null,null,0),n.stateNode=e,n.return=t,t.child=n,Ne=t,Fe=null,!0):!1;default:return!1}}function ul(t){return(t.mode&1)!==0&&(t.flags&128)===0}function hl(t){if(q){var e=Fe;if(e){var n=e;if(!jh(t,e)){if(ul(t))throw Error(j(418));e=Gt(n.nextSibling);var r=Ne;e&&jh(t,e)?Mh(r,n):(t.flags=t.flags&-4097|2,q=!1,Ne=t)}}else{if(ul(t))throw Error(j(418));t.flags=t.flags&-4097|2,q=!1,Ne=t}}}function Eh(t){for(t=t.return;t!==null&&t.tag!==5&&t.tag!==3&&t.tag!==13;)t=t.return;Ne=t}function To(t){if(t!==Ne)return!1;if(!q)return Eh(t),q=!0,!1;var e;if((e=t.tag!==3)&&!(e=t.tag!==5)&&(e=t.type,e=e!=="head"&&e!=="body"&&!rl(t.type,t.memoizedProps)),e&&(e=Fe)){if(ul(t))throw Dh(),Error(j(418));for(;e;)Mh(t,e),e=Gt(e.nextSibling)}if(Eh(t),t.tag===13){if(t=t.memoizedState,t=t!==null?t.dehydrated:null,!t)throw Error(j(317));e:{for(t=t.nextSibling,e=0;t;){if(t.nodeType===8){var n=t.data;if(n==="/$"){if(e===0){Fe=Gt(t.nextSibling);break e}e--}else n!=="$"&&n!=="$!"&&n!=="$?"||e++}t=t.nextSibling}Fe=null}}else Fe=Ne?Gt(t.stateNode.nextSibling):null;return!0}function Dh(){for(var t=Fe;t;)t=Gt(t.nextSibling)}function or(){Fe=Ne=null,q=!1}function pl(t){nt===null?nt=[t]:nt.push(t)}var B0=bt.ReactCurrentBatchConfig;function ci(t,e,n){if(t=n.ref,t!==null&&typeof t!="function"&&typeof t!="object"){if(n._owner){if(n=n._owner,n){if(n.tag!==1)throw Error(j(309));var r=n.stateNode}if(!r)throw Error(j(147,t));var i=r,o=""+t;return e!==null&&e.ref!==null&&typeof e.ref=="function"&&e.ref._stringRef===o?e.ref:(e=function(s){var l=i.refs;s===null?delete l[o]:l[o]=s},e._stringRef=o,e)}if(typeof t!="string")throw Error(j(284));if(!n._owner)throw Error(j(290,t))}return t}function Mo(t,e){throw t=Object.prototype.toString.call(e),Error(j(31,t==="[object Object]"?"object with keys {"+Object.keys(e).join(", ")+"}":t))}function Ph(t){var e=t._init;return e(t._payload)}function Ah(t){function e(w,m){if(t){var y=w.deletions;y===null?(w.deletions=[m],w.flags|=16):y.push(m)}}function n(w,m){if(!t)return null;for(;m!==null;)e(w,m),m=m.sibling;return null}function r(w,m){for(w=new Map;m!==null;)m.key!==null?w.set(m.key,m):w.set(m.index,m),m=m.sibling;return w}function i(w,m){return w=nn(w,m),w.index=0,w.sibling=null,w}function o(w,m,y){return w.index=y,t?(y=w.alternate,y!==null?(y=y.index,y<m?(w.flags|=2,m):y):(w.flags|=2,m)):(w.flags|=1048576,m)}function s(w){return t&&w.alternate===null&&(w.flags|=2),w}function l(w,m,y,S){return m===null||m.tag!==6?(m=ic(y,w.mode,S),m.return=w,m):(m=i(m,y),m.return=w,m)}function c(w,m,y,S){var T=y.type;return T===Wn?h(w,m,y.props.children,S,y.key):m!==null&&(m.elementType===T||typeof T=="object"&&T!==null&&T.$$typeof===zt&&Ph(T)===m.type)?(S=i(m,y.props),S.ref=ci(w,m,y),S.return=w,S):(S=Jo(y.type,y.key,y.props,null,w.mode,S),S.ref=ci(w,m,y),S.return=w,S)}function d(w,m,y,S){return m===null||m.tag!==4||m.stateNode.containerInfo!==y.containerInfo||m.stateNode.implementation!==y.implementation?(m=oc(y,w.mode,S),m.return=w,m):(m=i(m,y.children||[]),m.return=w,m)}function h(w,m,y,S,T){return m===null||m.tag!==7?(m=Cn(y,w.mode,S,T),m.return=w,m):(m=i(m,y),m.return=w,m)}function p(w,m,y){if(typeof m=="string"&&m!==""||typeof m=="number")return m=ic(""+m,w.mode,y),m.return=w,m;if(typeof m=="object"&&m!==null){switch(m.$$typeof){case Yi:return y=Jo(m.type,m.key,m.props,null,w.mode,y),y.ref=ci(w,null,m),y.return=w,y;case Hn:return m=oc(m,w.mode,y),m.return=w,m;case zt:var S=m._init;return p(w,S(m._payload),y)}if(Br(m)||Lr(m))return m=Cn(m,w.mode,y,null),m.return=w,m;Mo(w,m)}return null}function f(w,m,y,S){var T=m!==null?m.key:null;if(typeof y=="string"&&y!==""||typeof y=="number")return T!==null?null:l(w,m,""+y,S);if(typeof y=="object"&&y!==null){switch(y.$$typeof){case Yi:return y.key===T?c(w,m,y,S):null;case Hn:return y.key===T?d(w,m,y,S):null;case zt:return T=y._init,f(w,m,T(y._payload),S)}if(Br(y)||Lr(y))return T!==null?null:h(w,m,y,S,null);Mo(w,y)}return null}function v(w,m,y,S,T){if(typeof S=="string"&&S!==""||typeof S=="number")return w=w.get(y)||null,l(m,w,""+S,T);if(typeof S=="object"&&S!==null){switch(S.$$typeof){case Yi:return w=w.get(S.key===null?y:S.key)||null,c(m,w,S,T);case Hn:return w=w.get(S.key===null?y:S.key)||null,d(m,w,S,T);case zt:var C=S._init;return v(w,m,y,C(S._payload),T)}if(Br(S)||Lr(S))return w=w.get(y)||null,h(m,w,S,T,null);Mo(m,S)}return null}function x(w,m,y,S){for(var T=null,C=null,k=m,$=m=0,D=null;k!==null&&$<y.length;$++){k.index>$?(D=k,k=null):D=k.sibling;var E=f(w,k,y[$],S);if(E===null){k===null&&(k=D);break}t&&k&&E.alternate===null&&e(w,k),m=o(E,m,$),C===null?T=E:C.sibling=E,C=E,k=D}if($===y.length)return n(w,k),q&&xn(w,$),T;if(k===null){for(;$<y.length;$++)k=p(w,y[$],S),k!==null&&(m=o(k,m,$),C===null?T=k:C.sibling=k,C=k);return q&&xn(w,$),T}for(k=r(w,k);$<y.length;$++)D=v(k,w,$,y[$],S),D!==null&&(t&&D.alternate!==null&&k.delete(D.key===null?$:D.key),m=o(D,m,$),C===null?T=D:C.sibling=D,C=D);return t&&k.forEach(function(H){return e(w,H)}),q&&xn(w,$),T}function b(w,m,y,S){var T=Lr(y);if(typeof T!="function")throw Error(j(150));if(y=T.call(y),y==null)throw Error(j(151));for(var C=T=null,k=m,$=m=0,D=null,E=y.next();k!==null&&!E.done;$++,E=y.next()){k.index>$?(D=k,k=null):D=k.sibling;var H=f(w,k,E.value,S);if(H===null){k===null&&(k=D);break}t&&k&&H.alternate===null&&e(w,k),m=o(H,m,$),C===null?T=H:C.sibling=H,C=H,k=D}if(E.done)return n(w,k),q&&xn(w,$),T;if(k===null){for(;!E.done;$++,E=y.next())E=p(w,E.value,S),E!==null&&(m=o(E,m,$),C===null?T=E:C.sibling=E,C=E);return q&&xn(w,$),T}for(k=r(w,k);!E.done;$++,E=y.next())E=v(k,w,$,E.value,S),E!==null&&(t&&E.alternate!==null&&k.delete(E.key===null?$:E.key),m=o(E,m,$),C===null?T=E:C.sibling=E,C=E);return t&&k.forEach(function(K){return e(w,K)}),q&&xn(w,$),T}function M(w,m,y,S){if(typeof y=="object"&&y!==null&&y.type===Wn&&y.key===null&&(y=y.props.children),typeof y=="object"&&y!==null){switch(y.$$typeof){case Yi:e:{for(var T=y.key,C=m;C!==null;){if(C.key===T){if(T=y.type,T===Wn){if(C.tag===7){n(w,C.sibling),m=i(C,y.props.children),m.return=w,w=m;break e}}else if(C.elementType===T||typeof T=="object"&&T!==null&&T.$$typeof===zt&&Ph(T)===C.type){n(w,C.sibling),m=i(C,y.props),m.ref=ci(w,C,y),m.return=w,w=m;break e}n(w,C);break}else e(w,C);C=C.sibling}y.type===Wn?(m=Cn(y.props.children,w.mode,S,y.key),m.return=w,w=m):(S=Jo(y.type,y.key,y.props,null,w.mode,S),S.ref=ci(w,m,y),S.return=w,w=S)}return s(w);case Hn:e:{for(C=y.key;m!==null;){if(m.key===C)if(m.tag===4&&m.stateNode.containerInfo===y.containerInfo&&m.stateNode.implementation===y.implementation){n(w,m.sibling),m=i(m,y.children||[]),m.return=w,w=m;break e}else{n(w,m);break}else e(w,m);m=m.sibling}m=oc(y,w.mode,S),m.return=w,w=m}return s(w);case zt:return C=y._init,M(w,m,C(y._payload),S)}if(Br(y))return x(w,m,y,S);if(Lr(y))return b(w,m,y,S);Mo(w,y)}return typeof y=="string"&&y!==""||typeof y=="number"?(y=""+y,m!==null&&m.tag===6?(n(w,m.sibling),m=i(m,y),m.return=w,w=m):(n(w,m),m=ic(y,w.mode,S),m.return=w,w=m),s(w)):n(w,m)}return M}var sr=Ah(!0),Rh=Ah(!1),jo=Vt(null),Eo=null,ar=null,fl=null;function gl(){fl=ar=Eo=null}function ml(t){var e=jo.current;X(jo),t._currentValue=e}function xl(t,e,n){for(;t!==null;){var r=t.alternate;if((t.childLanes&e)!==e?(t.childLanes|=e,r!==null&&(r.childLanes|=e)):r!==null&&(r.childLanes&e)!==e&&(r.childLanes|=e),t===n)break;t=t.return}}function lr(t,e){Eo=t,fl=ar=null,t=t.dependencies,t!==null&&t.firstContext!==null&&(t.lanes&e&&(Ee=!0),t.firstContext=null)}function Ye(t){var e=t._currentValue;if(fl!==t)if(t={context:t,memoizedValue:e,next:null},ar===null){if(Eo===null)throw Error(j(308));ar=t,Eo.dependencies={lanes:0,firstContext:t}}else ar=ar.next=t;return e}var yn=null;function yl(t){yn===null?yn=[t]:yn.push(t)}function Ih(t,e,n,r){var i=e.interleaved;return i===null?(n.next=n,yl(e)):(n.next=i.next,i.next=n),e.interleaved=n,Tt(t,r)}function Tt(t,e){t.lanes|=e;var n=t.alternate;for(n!==null&&(n.lanes|=e),n=t,t=t.return;t!==null;)t.childLanes|=e,n=t.alternate,n!==null&&(n.childLanes|=e),n=t,t=t.return;return n.tag===3?n.stateNode:null}var Qt=!1;function wl(t){t.updateQueue={baseState:t.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:0},effects:null}}function Oh(t,e){t=t.updateQueue,e.updateQueue===t&&(e.updateQueue={baseState:t.baseState,firstBaseUpdate:t.firstBaseUpdate,lastBaseUpdate:t.lastBaseUpdate,shared:t.shared,effects:t.effects})}function Mt(t,e){return{eventTime:t,lane:e,tag:0,payload:null,callback:null,next:null}}function Xt(t,e,n){var r=t.updateQueue;if(r===null)return null;if(r=r.shared,F&2){var i=r.pending;return i===null?e.next=e:(e.next=i.next,i.next=e),r.pending=e,Tt(t,n)}return i=r.interleaved,i===null?(e.next=e,yl(r)):(e.next=i.next,i.next=e),r.interleaved=e,Tt(t,n)}function Do(t,e,n){if(e=e.updateQueue,e!==null&&(e=e.shared,(n&4194240)!==0)){var r=e.lanes;r&=t.pendingLanes,n|=r,e.lanes=n,Ra(t,n)}}function Lh(t,e){var n=t.updateQueue,r=t.alternate;if(r!==null&&(r=r.updateQueue,n===r)){var i=null,o=null;if(n=n.firstBaseUpdate,n!==null){do{var s={eventTime:n.eventTime,lane:n.lane,tag:n.tag,payload:n.payload,callback:n.callback,next:null};o===null?i=o=s:o=o.next=s,n=n.next}while(n!==null);o===null?i=o=e:o=o.next=e}else i=o=e;n={baseState:r.baseState,firstBaseUpdate:i,lastBaseUpdate:o,shared:r.shared,effects:r.effects},t.updateQueue=n;return}t=n.lastBaseUpdate,t===null?n.firstBaseUpdate=e:t.next=e,n.lastBaseUpdate=e}function Po(t,e,n,r){var i=t.updateQueue;Qt=!1;var o=i.firstBaseUpdate,s=i.lastBaseUpdate,l=i.shared.pending;if(l!==null){i.shared.pending=null;var c=l,d=c.next;c.next=null,s===null?o=d:s.next=d,s=c;var h=t.alternate;h!==null&&(h=h.updateQueue,l=h.lastBaseUpdate,l!==s&&(l===null?h.firstBaseUpdate=d:l.next=d,h.lastBaseUpdate=c))}if(o!==null){var p=i.baseState;s=0,h=d=c=null,l=o;do{var f=l.lane,v=l.eventTime;if((r&f)===f){h!==null&&(h=h.next={eventTime:v,lane:0,tag:l.tag,payload:l.payload,callback:l.callback,next:null});e:{var x=t,b=l;switch(f=e,v=n,b.tag){case 1:if(x=b.payload,typeof x=="function"){p=x.call(v,p,f);break e}p=x;break e;case 3:x.flags=x.flags&-65537|128;case 0:if(x=b.payload,f=typeof x=="function"?x.call(v,p,f):x,f==null)break e;p=Z({},p,f);break e;case 2:Qt=!0}}l.callback!==null&&l.lane!==0&&(t.flags|=64,f=i.effects,f===null?i.effects=[l]:f.push(l))}else v={eventTime:v,lane:f,tag:l.tag,payload:l.payload,callback:l.callback,next:null},h===null?(d=h=v,c=p):h=h.next=v,s|=f;if(l=l.next,l===null){if(l=i.shared.pending,l===null)break;f=l,l=f.next,f.next=null,i.lastBaseUpdate=f,i.shared.pending=null}}while(!0);if(h===null&&(c=p),i.baseState=c,i.firstBaseUpdate=d,i.lastBaseUpdate=h,e=i.shared.interleaved,e!==null){i=e;do s|=i.lane,i=i.next;while(i!==e)}else o===null&&(i.shared.lanes=0);bn|=s,t.lanes=s,t.memoizedState=p}}function zh(t,e,n){if(t=e.effects,e.effects=null,t!==null)for(e=0;e<t.length;e++){var r=t[e],i=r.callback;if(i!==null){if(r.callback=null,r=n,typeof i!="function")throw Error(j(191,i));i.call(r)}}}var di={},mt=Vt(di),ui=Vt(di),hi=Vt(di);function wn(t){if(t===di)throw Error(j(174));return t}function vl(t,e){switch(Y(hi,e),Y(ui,t),Y(mt,di),t=e.nodeType,t){case 9:case 11:e=(e=e.documentElement)?e.namespaceURI:ba(null,"");break;default:t=t===8?e.parentNode:e,e=t.namespaceURI||null,t=t.tagName,e=ba(e,t)}X(mt),Y(mt,e)}function cr(){X(mt),X(ui),X(hi)}function Bh(t){wn(hi.current);var e=wn(mt.current),n=ba(e,t.type);e!==n&&(Y(ui,t),Y(mt,n))}function bl(t){ui.current===t&&(X(mt),X(ui))}var ee=Vt(0);function Ao(t){for(var e=t;e!==null;){if(e.tag===13){var n=e.memoizedState;if(n!==null&&(n=n.dehydrated,n===null||n.data==="$?"||n.data==="$!"))return e}else if(e.tag===19&&e.memoizedProps.revealOrder!==void 0){if(e.flags&128)return e}else if(e.child!==null){e.child.return=e,e=e.child;continue}if(e===t)break;for(;e.sibling===null;){if(e.return===null||e.return===t)return null;e=e.return}e.sibling.return=e.return,e=e.sibling}return null}var Sl=[];function kl(){for(var t=0;t<Sl.length;t++)Sl[t]._workInProgressVersionPrimary=null;Sl.length=0}var Ro=bt.ReactCurrentDispatcher,$l=bt.ReactCurrentBatchConfig,vn=0,te=null,le=null,pe=null,Io=!1,pi=!1,fi=0,N0=0;function we(){throw Error(j(321))}function Cl(t,e){if(e===null)return!1;for(var n=0;n<e.length&&n<t.length;n++)if(!tt(t[n],e[n]))return!1;return!0}function Tl(t,e,n,r,i,o){if(vn=o,te=e,e.memoizedState=null,e.updateQueue=null,e.lanes=0,Ro.current=t===null||t.memoizedState===null?W0:U0,t=n(r,i),pi){o=0;do{if(pi=!1,fi=0,25<=o)throw Error(j(301));o+=1,pe=le=null,e.updateQueue=null,Ro.current=G0,t=n(r,i)}while(pi)}if(Ro.current=zo,e=le!==null&&le.next!==null,vn=0,pe=le=te=null,Io=!1,e)throw Error(j(300));return t}function Ml(){var t=fi!==0;return fi=0,t}function xt(){var t={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return pe===null?te.memoizedState=pe=t:pe=pe.next=t,pe}function Ke(){if(le===null){var t=te.alternate;t=t!==null?t.memoizedState:null}else t=le.next;var e=pe===null?te.memoizedState:pe.next;if(e!==null)pe=e,le=t;else{if(t===null)throw Error(j(310));le=t,t={memoizedState:le.memoizedState,baseState:le.baseState,baseQueue:le.baseQueue,queue:le.queue,next:null},pe===null?te.memoizedState=pe=t:pe=pe.next=t}return pe}function gi(t,e){return typeof e=="function"?e(t):e}function jl(t){var e=Ke(),n=e.queue;if(n===null)throw Error(j(311));n.lastRenderedReducer=t;var r=le,i=r.baseQueue,o=n.pending;if(o!==null){if(i!==null){var s=i.next;i.next=o.next,o.next=s}r.baseQueue=i=o,n.pending=null}if(i!==null){o=i.next,r=r.baseState;var l=s=null,c=null,d=o;do{var h=d.lane;if((vn&h)===h)c!==null&&(c=c.next={lane:0,action:d.action,hasEagerState:d.hasEagerState,eagerState:d.eagerState,next:null}),r=d.hasEagerState?d.eagerState:t(r,d.action);else{var p={lane:h,action:d.action,hasEagerState:d.hasEagerState,eagerState:d.eagerState,next:null};c===null?(l=c=p,s=r):c=c.next=p,te.lanes|=h,bn|=h}d=d.next}while(d!==null&&d!==o);c===null?s=r:c.next=l,tt(r,e.memoizedState)||(Ee=!0),e.memoizedState=r,e.baseState=s,e.baseQueue=c,n.lastRenderedState=r}if(t=n.interleaved,t!==null){i=t;do o=i.lane,te.lanes|=o,bn|=o,i=i.next;while(i!==t)}else i===null&&(n.lanes=0);return[e.memoizedState,n.dispatch]}function El(t){var e=Ke(),n=e.queue;if(n===null)throw Error(j(311));n.lastRenderedReducer=t;var r=n.dispatch,i=n.pending,o=e.memoizedState;if(i!==null){n.pending=null;var s=i=i.next;do o=t(o,s.action),s=s.next;while(s!==i);tt(o,e.memoizedState)||(Ee=!0),e.memoizedState=o,e.baseQueue===null&&(e.baseState=o),n.lastRenderedState=o}return[o,r]}function Nh(){}function Fh(t,e){var n=te,r=Ke(),i=e(),o=!tt(r.memoizedState,i);if(o&&(r.memoizedState=i,Ee=!0),r=r.queue,Dl(Wh.bind(null,n,r,t),[t]),r.getSnapshot!==e||o||pe!==null&&pe.memoizedState.tag&1){if(n.flags|=2048,mi(9,Hh.bind(null,n,r,i,e),void 0,null),fe===null)throw Error(j(349));vn&30||_h(n,e,i)}return i}function _h(t,e,n){t.flags|=16384,t={getSnapshot:e,value:n},e=te.updateQueue,e===null?(e={lastEffect:null,stores:null},te.updateQueue=e,e.stores=[t]):(n=e.stores,n===null?e.stores=[t]:n.push(t))}function Hh(t,e,n,r){e.value=n,e.getSnapshot=r,Uh(e)&&Gh(t)}function Wh(t,e,n){return n(function(){Uh(e)&&Gh(t)})}function Uh(t){var e=t.getSnapshot;t=t.value;try{var n=e();return!tt(t,n)}catch{return!0}}function Gh(t){var e=Tt(t,1);e!==null&&st(e,t,1,-1)}function Vh(t){var e=xt();return typeof t=="function"&&(t=t()),e.memoizedState=e.baseState=t,t={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:gi,lastRenderedState:t},e.queue=t,t=t.dispatch=H0.bind(null,te,t),[e.memoizedState,t]}function mi(t,e,n,r){return t={tag:t,create:e,destroy:n,deps:r,next:null},e=te.updateQueue,e===null?(e={lastEffect:null,stores:null},te.updateQueue=e,e.lastEffect=t.next=t):(n=e.lastEffect,n===null?e.lastEffect=t.next=t:(r=n.next,n.next=t,t.next=r,e.lastEffect=t)),t}function Yh(){return Ke().memoizedState}function Oo(t,e,n,r){var i=xt();te.flags|=t,i.memoizedState=mi(1|e,n,void 0,r===void 0?null:r)}function Lo(t,e,n,r){var i=Ke();r=r===void 0?null:r;var o=void 0;if(le!==null){var s=le.memoizedState;if(o=s.destroy,r!==null&&Cl(r,s.deps)){i.memoizedState=mi(e,n,o,r);return}}te.flags|=t,i.memoizedState=mi(1|e,n,o,r)}function Kh(t,e){return Oo(8390656,8,t,e)}function Dl(t,e){return Lo(2048,8,t,e)}function Qh(t,e){return Lo(4,2,t,e)}function Xh(t,e){return Lo(4,4,t,e)}function Jh(t,e){if(typeof e=="function")return t=t(),e(t),function(){e(null)};if(e!=null)return t=t(),e.current=t,function(){e.current=null}}function qh(t,e,n){return n=n!=null?n.concat([t]):null,Lo(4,4,Jh.bind(null,e,t),n)}function Pl(){}function Zh(t,e){var n=Ke();e=e===void 0?null:e;var r=n.memoizedState;return r!==null&&e!==null&&Cl(e,r[1])?r[0]:(n.memoizedState=[t,e],t)}function ep(t,e){var n=Ke();e=e===void 0?null:e;var r=n.memoizedState;return r!==null&&e!==null&&Cl(e,r[1])?r[0]:(t=t(),n.memoizedState=[t,e],t)}function tp(t,e,n){return vn&21?(tt(n,e)||(n=Pu(),te.lanes|=n,bn|=n,t.baseState=!0),e):(t.baseState&&(t.baseState=!1,Ee=!0),t.memoizedState=n)}function F0(t,e){var n=U;U=n!==0&&4>n?n:4,t(!0);var r=$l.transition;$l.transition={};try{t(!1),e()}finally{U=n,$l.transition=r}}function np(){return Ke().memoizedState}function _0(t,e,n){var r=en(t);if(n={lane:r,action:n,hasEagerState:!1,eagerState:null,next:null},rp(t))ip(e,n);else if(n=Ih(t,e,n,r),n!==null){var i=Te();st(n,t,r,i),op(n,e,r)}}function H0(t,e,n){var r=en(t),i={lane:r,action:n,hasEagerState:!1,eagerState:null,next:null};if(rp(t))ip(e,i);else{var o=t.alternate;if(t.lanes===0&&(o===null||o.lanes===0)&&(o=e.lastRenderedReducer,o!==null))try{var s=e.lastRenderedState,l=o(s,n);if(i.hasEagerState=!0,i.eagerState=l,tt(l,s)){var c=e.interleaved;c===null?(i.next=i,yl(e)):(i.next=c.next,c.next=i),e.interleaved=i;return}}catch{}finally{}n=Ih(t,e,i,r),n!==null&&(i=Te(),st(n,t,r,i),op(n,e,r))}}function rp(t){var e=t.alternate;return t===te||e!==null&&e===te}function ip(t,e){pi=Io=!0;var n=t.pending;n===null?e.next=e:(e.next=n.next,n.next=e),t.pending=e}function op(t,e,n){if(n&4194240){var r=e.lanes;r&=t.pendingLanes,n|=r,e.lanes=n,Ra(t,n)}}var zo={readContext:Ye,useCallback:we,useContext:we,useEffect:we,useImperativeHandle:we,useInsertionEffect:we,useLayoutEffect:we,useMemo:we,useReducer:we,useRef:we,useState:we,useDebugValue:we,useDeferredValue:we,useTransition:we,useMutableSource:we,useSyncExternalStore:we,useId:we,unstable_isNewReconciler:!1},W0={readContext:Ye,useCallback:function(t,e){return xt().memoizedState=[t,e===void 0?null:e],t},useContext:Ye,useEffect:Kh,useImperativeHandle:function(t,e,n){return n=n!=null?n.concat([t]):null,Oo(4194308,4,Jh.bind(null,e,t),n)},useLayoutEffect:function(t,e){return Oo(4194308,4,t,e)},useInsertionEffect:function(t,e){return Oo(4,2,t,e)},useMemo:function(t,e){var n=xt();return e=e===void 0?null:e,t=t(),n.memoizedState=[t,e],t},useReducer:function(t,e,n){var r=xt();return e=n!==void 0?n(e):e,r.memoizedState=r.baseState=e,t={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:t,lastRenderedState:e},r.queue=t,t=t.dispatch=_0.bind(null,te,t),[r.memoizedState,t]},useRef:function(t){var e=xt();return t={current:t},e.memoizedState=t},useState:Vh,useDebugValue:Pl,useDeferredValue:function(t){return xt().memoizedState=t},useTransition:function(){var t=Vh(!1),e=t[0];return t=F0.bind(null,t[1]),xt().memoizedState=t,[e,t]},useMutableSource:function(){},useSyncExternalStore:function(t,e,n){var r=te,i=xt();if(q){if(n===void 0)throw Error(j(407));n=n()}else{if(n=e(),fe===null)throw Error(j(349));vn&30||_h(r,e,n)}i.memoizedState=n;var o={value:n,getSnapshot:e};return i.queue=o,Kh(Wh.bind(null,r,o,t),[t]),r.flags|=2048,mi(9,Hh.bind(null,r,o,n,e),void 0,null),n},useId:function(){var t=xt(),e=fe.identifierPrefix;if(q){var n=Ct,r=$t;n=(r&~(1<<32-et(r)-1)).toString(32)+n,e=":"+e+"R"+n,n=fi++,0<n&&(e+="H"+n.toString(32)),e+=":"}else n=N0++,e=":"+e+"r"+n.toString(32)+":";return t.memoizedState=e},unstable_isNewReconciler:!1},U0={readContext:Ye,useCallback:Zh,useContext:Ye,useEffect:Dl,useImperativeHandle:qh,useInsertionEffect:Qh,useLayoutEffect:Xh,useMemo:ep,useReducer:jl,useRef:Yh,useState:function(){return jl(gi)},useDebugValue:Pl,useDeferredValue:function(t){var e=Ke();return tp(e,le.memoizedState,t)},useTransition:function(){var t=jl(gi)[0],e=Ke().memoizedState;return[t,e]},useMutableSource:Nh,useSyncExternalStore:Fh,useId:np,unstable_isNewReconciler:!1},G0={readContext:Ye,useCallback:Zh,useContext:Ye,useEffect:Dl,useImperativeHandle:qh,useInsertionEffect:Qh,useLayoutEffect:Xh,useMemo:ep,useReducer:El,useRef:Yh,useState:function(){return El(gi)},useDebugValue:Pl,useDeferredValue:function(t){var e=Ke();return le===null?e.memoizedState=t:tp(e,le.memoizedState,t)},useTransition:function(){var t=El(gi)[0],e=Ke().memoizedState;return[t,e]},useMutableSource:Nh,useSyncExternalStore:Fh,useId:np,unstable_isNewReconciler:!1};function rt(t,e){if(t&&t.defaultProps){e=Z({},e),t=t.defaultProps;for(var n in t)e[n]===void 0&&(e[n]=t[n]);return e}return e}function Al(t,e,n,r){e=t.memoizedState,n=n(r,e),n=n==null?e:Z({},e,n),t.memoizedState=n,t.lanes===0&&(t.updateQueue.baseState=n)}var Bo={isMounted:function(t){return(t=t._reactInternals)?pn(t)===t:!1},enqueueSetState:function(t,e,n){t=t._reactInternals;var r=Te(),i=en(t),o=Mt(r,i);o.payload=e,n!=null&&(o.callback=n),e=Xt(t,o,i),e!==null&&(st(e,t,i,r),Do(e,t,i))},enqueueReplaceState:function(t,e,n){t=t._reactInternals;var r=Te(),i=en(t),o=Mt(r,i);o.tag=1,o.payload=e,n!=null&&(o.callback=n),e=Xt(t,o,i),e!==null&&(st(e,t,i,r),Do(e,t,i))},enqueueForceUpdate:function(t,e){t=t._reactInternals;var n=Te(),r=en(t),i=Mt(n,r);i.tag=2,e!=null&&(i.callback=e),e=Xt(t,i,r),e!==null&&(st(e,t,r,n),Do(e,t,r))}};function sp(t,e,n,r,i,o,s){return t=t.stateNode,typeof t.shouldComponentUpdate=="function"?t.shouldComponentUpdate(r,o,s):e.prototype&&e.prototype.isPureReactComponent?!ni(n,r)||!ni(i,o):!0}function ap(t,e,n){var r=!1,i=Yt,o=e.contextType;return typeof o=="object"&&o!==null?o=Ye(o):(i=je(e)?gn:ye.current,r=e.contextTypes,o=(r=r!=null)?nr(t,i):Yt),e=new e(n,o),t.memoizedState=e.state!==null&&e.state!==void 0?e.state:null,e.updater=Bo,t.stateNode=e,e._reactInternals=t,r&&(t=t.stateNode,t.__reactInternalMemoizedUnmaskedChildContext=i,t.__reactInternalMemoizedMaskedChildContext=o),e}function lp(t,e,n,r){t=e.state,typeof e.componentWillReceiveProps=="function"&&e.componentWillReceiveProps(n,r),typeof e.UNSAFE_componentWillReceiveProps=="function"&&e.UNSAFE_componentWillReceiveProps(n,r),e.state!==t&&Bo.enqueueReplaceState(e,e.state,null)}function Rl(t,e,n,r){var i=t.stateNode;i.props=n,i.state=t.memoizedState,i.refs={},wl(t);var o=e.contextType;typeof o=="object"&&o!==null?i.context=Ye(o):(o=je(e)?gn:ye.current,i.context=nr(t,o)),i.state=t.memoizedState,o=e.getDerivedStateFromProps,typeof o=="function"&&(Al(t,e,o,n),i.state=t.memoizedState),typeof e.getDerivedStateFromProps=="function"||typeof i.getSnapshotBeforeUpdate=="function"||typeof i.UNSAFE_componentWillMount!="function"&&typeof i.componentWillMount!="function"||(e=i.state,typeof i.componentWillMount=="function"&&i.componentWillMount(),typeof i.UNSAFE_componentWillMount=="function"&&i.UNSAFE_componentWillMount(),e!==i.state&&Bo.enqueueReplaceState(i,i.state,null),Po(t,n,i,r),i.state=t.memoizedState),typeof i.componentDidMount=="function"&&(t.flags|=4194308)}function dr(t,e){try{var n="",r=e;do n+=wx(r),r=r.return;while(r);var i=n}catch(o){i=`
Error generating stack: `+o.message+`
`+o.stack}return{value:t,source:e,stack:i,digest:null}}function Il(t,e,n){return{value:t,source:null,stack:n??null,digest:e??null}}function Ol(t,e){try{console.error(e.value)}catch(n){setTimeout(function(){throw n})}}var V0=typeof WeakMap=="function"?WeakMap:Map;function cp(t,e,n){n=Mt(-1,n),n.tag=3,n.payload={element:null};var r=e.value;return n.callback=function(){Go||(Go=!0,Xl=r),Ol(t,e)},n}function dp(t,e,n){n=Mt(-1,n),n.tag=3;var r=t.type.getDerivedStateFromError;if(typeof r=="function"){var i=e.value;n.payload=function(){return r(i)},n.callback=function(){Ol(t,e)}}var o=t.stateNode;return o!==null&&typeof o.componentDidCatch=="function"&&(n.callback=function(){Ol(t,e),typeof r!="function"&&(qt===null?qt=new Set([this]):qt.add(this));var s=e.stack;this.componentDidCatch(e.value,{componentStack:s!==null?s:""})}),n}function up(t,e,n){var r=t.pingCache;if(r===null){r=t.pingCache=new V0;var i=new Set;r.set(e,i)}else i=r.get(e),i===void 0&&(i=new Set,r.set(e,i));i.has(n)||(i.add(n),t=sy.bind(null,t,e,n),e.then(t,t))}function hp(t){do{var e;if((e=t.tag===13)&&(e=t.memoizedState,e=e!==null?e.dehydrated!==null:!0),e)return t;t=t.return}while(t!==null);return null}function pp(t,e,n,r,i){return t.mode&1?(t.flags|=65536,t.lanes=i,t):(t===e?t.flags|=65536:(t.flags|=128,n.flags|=131072,n.flags&=-52805,n.tag===1&&(n.alternate===null?n.tag=17:(e=Mt(-1,1),e.tag=2,Xt(n,e,1))),n.lanes|=1),t)}var Y0=bt.ReactCurrentOwner,Ee=!1;function Ce(t,e,n,r){e.child=t===null?Rh(e,null,n,r):sr(e,t.child,n,r)}function fp(t,e,n,r,i){n=n.render;var o=e.ref;return lr(e,i),r=Tl(t,e,n,r,o,i),n=Ml(),t!==null&&!Ee?(e.updateQueue=t.updateQueue,e.flags&=-2053,t.lanes&=~i,jt(t,e,i)):(q&&n&&cl(e),e.flags|=1,Ce(t,e,r,i),e.child)}function gp(t,e,n,r,i){if(t===null){var o=n.type;return typeof o=="function"&&!rc(o)&&o.defaultProps===void 0&&n.compare===null&&n.defaultProps===void 0?(e.tag=15,e.type=o,mp(t,e,o,r,i)):(t=Jo(n.type,null,r,e,e.mode,i),t.ref=e.ref,t.return=e,e.child=t)}if(o=t.child,!(t.lanes&i)){var s=o.memoizedProps;if(n=n.compare,n=n!==null?n:ni,n(s,r)&&t.ref===e.ref)return jt(t,e,i)}return e.flags|=1,t=nn(o,r),t.ref=e.ref,t.return=e,e.child=t}function mp(t,e,n,r,i){if(t!==null){var o=t.memoizedProps;if(ni(o,r)&&t.ref===e.ref)if(Ee=!1,e.pendingProps=r=o,(t.lanes&i)!==0)t.flags&131072&&(Ee=!0);else return e.lanes=t.lanes,jt(t,e,i)}return Ll(t,e,n,r,i)}function xp(t,e,n){var r=e.pendingProps,i=r.children,o=t!==null?t.memoizedState:null;if(r.mode==="hidden")if(!(e.mode&1))e.memoizedState={baseLanes:0,cachePool:null,transitions:null},Y(hr,_e),_e|=n;else{if(!(n&1073741824))return t=o!==null?o.baseLanes|n:n,e.lanes=e.childLanes=1073741824,e.memoizedState={baseLanes:t,cachePool:null,transitions:null},e.updateQueue=null,Y(hr,_e),_e|=t,null;e.memoizedState={baseLanes:0,cachePool:null,transitions:null},r=o!==null?o.baseLanes:n,Y(hr,_e),_e|=r}else o!==null?(r=o.baseLanes|n,e.memoizedState=null):r=n,Y(hr,_e),_e|=r;return Ce(t,e,i,n),e.child}function yp(t,e){var n=e.ref;(t===null&&n!==null||t!==null&&t.ref!==n)&&(e.flags|=512,e.flags|=2097152)}function Ll(t,e,n,r,i){var o=je(n)?gn:ye.current;return o=nr(e,o),lr(e,i),n=Tl(t,e,n,r,o,i),r=Ml(),t!==null&&!Ee?(e.updateQueue=t.updateQueue,e.flags&=-2053,t.lanes&=~i,jt(t,e,i)):(q&&r&&cl(e),e.flags|=1,Ce(t,e,n,i),e.child)}function wp(t,e,n,r,i){if(je(n)){var o=!0;So(e)}else o=!1;if(lr(e,i),e.stateNode===null)Fo(t,e),ap(e,n,r),Rl(e,n,r,i),r=!0;else if(t===null){var s=e.stateNode,l=e.memoizedProps;s.props=l;var c=s.context,d=n.contextType;typeof d=="object"&&d!==null?d=Ye(d):(d=je(n)?gn:ye.current,d=nr(e,d));var h=n.getDerivedStateFromProps,p=typeof h=="function"||typeof s.getSnapshotBeforeUpdate=="function";p||typeof s.UNSAFE_componentWillReceiveProps!="function"&&typeof s.componentWillReceiveProps!="function"||(l!==r||c!==d)&&lp(e,s,r,d),Qt=!1;var f=e.memoizedState;s.state=f,Po(e,r,s,i),c=e.memoizedState,l!==r||f!==c||Me.current||Qt?(typeof h=="function"&&(Al(e,n,h,r),c=e.memoizedState),(l=Qt||sp(e,n,l,r,f,c,d))?(p||typeof s.UNSAFE_componentWillMount!="function"&&typeof s.componentWillMount!="function"||(typeof s.componentWillMount=="function"&&s.componentWillMount(),typeof s.UNSAFE_componentWillMount=="function"&&s.UNSAFE_componentWillMount()),typeof s.componentDidMount=="function"&&(e.flags|=4194308)):(typeof s.componentDidMount=="function"&&(e.flags|=4194308),e.memoizedProps=r,e.memoizedState=c),s.props=r,s.state=c,s.context=d,r=l):(typeof s.componentDidMount=="function"&&(e.flags|=4194308),r=!1)}else{s=e.stateNode,Oh(t,e),l=e.memoizedProps,d=e.type===e.elementType?l:rt(e.type,l),s.props=d,p=e.pendingProps,f=s.context,c=n.contextType,typeof c=="object"&&c!==null?c=Ye(c):(c=je(n)?gn:ye.current,c=nr(e,c));var v=n.getDerivedStateFromProps;(h=typeof v=="function"||typeof s.getSnapshotBeforeUpdate=="function")||typeof s.UNSAFE_componentWillReceiveProps!="function"&&typeof s.componentWillReceiveProps!="function"||(l!==p||f!==c)&&lp(e,s,r,c),Qt=!1,f=e.memoizedState,s.state=f,Po(e,r,s,i);var x=e.memoizedState;l!==p||f!==x||Me.current||Qt?(typeof v=="function"&&(Al(e,n,v,r),x=e.memoizedState),(d=Qt||sp(e,n,d,r,f,x,c)||!1)?(h||typeof s.UNSAFE_componentWillUpdate!="function"&&typeof s.componentWillUpdate!="function"||(typeof s.componentWillUpdate=="function"&&s.componentWillUpdate(r,x,c),typeof s.UNSAFE_componentWillUpdate=="function"&&s.UNSAFE_componentWillUpdate(r,x,c)),typeof s.componentDidUpdate=="function"&&(e.flags|=4),typeof s.getSnapshotBeforeUpdate=="function"&&(e.flags|=1024)):(typeof s.componentDidUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=4),typeof s.getSnapshotBeforeUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=1024),e.memoizedProps=r,e.memoizedState=x),s.props=r,s.state=x,s.context=c,r=d):(typeof s.componentDidUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=4),typeof s.getSnapshotBeforeUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=1024),r=!1)}return zl(t,e,n,r,o,i)}function zl(t,e,n,r,i,o){yp(t,e);var s=(e.flags&128)!==0;if(!r&&!s)return i&&$h(e,n,!1),jt(t,e,o);r=e.stateNode,Y0.current=e;var l=s&&typeof n.getDerivedStateFromError!="function"?null:r.render();return e.flags|=1,t!==null&&s?(e.child=sr(e,t.child,null,o),e.child=sr(e,null,l,o)):Ce(t,e,l,o),e.memoizedState=r.state,i&&$h(e,n,!0),e.child}function vp(t){var e=t.stateNode;e.pendingContext?Sh(t,e.pendingContext,e.pendingContext!==e.context):e.context&&Sh(t,e.context,!1),vl(t,e.containerInfo)}function bp(t,e,n,r,i){return or(),pl(i),e.flags|=256,Ce(t,e,n,r),e.child}var Bl={dehydrated:null,treeContext:null,retryLane:0};function Nl(t){return{baseLanes:t,cachePool:null,transitions:null}}function Sp(t,e,n){var r=e.pendingProps,i=ee.current,o=!1,s=(e.flags&128)!==0,l;if((l=s)||(l=t!==null&&t.memoizedState===null?!1:(i&2)!==0),l?(o=!0,e.flags&=-129):(t===null||t.memoizedState!==null)&&(i|=1),Y(ee,i&1),t===null)return hl(e),t=e.memoizedState,t!==null&&(t=t.dehydrated,t!==null)?(e.mode&1?t.data==="$!"?e.lanes=8:e.lanes=1073741824:e.lanes=1,null):(s=r.children,t=r.fallback,o?(r=e.mode,o=e.child,s={mode:"hidden",children:s},!(r&1)&&o!==null?(o.childLanes=0,o.pendingProps=s):o=qo(s,r,0,null),t=Cn(t,r,n,null),o.return=e,t.return=e,o.sibling=t,e.child=o,e.child.memoizedState=Nl(n),e.memoizedState=Bl,t):Fl(e,s));if(i=t.memoizedState,i!==null&&(l=i.dehydrated,l!==null))return K0(t,e,s,r,l,i,n);if(o){o=r.fallback,s=e.mode,i=t.child,l=i.sibling;var c={mode:"hidden",children:r.children};return!(s&1)&&e.child!==i?(r=e.child,r.childLanes=0,r.pendingProps=c,e.deletions=null):(r=nn(i,c),r.subtreeFlags=i.subtreeFlags&14680064),l!==null?o=nn(l,o):(o=Cn(o,s,n,null),o.flags|=2),o.return=e,r.return=e,r.sibling=o,e.child=r,r=o,o=e.child,s=t.child.memoizedState,s=s===null?Nl(n):{baseLanes:s.baseLanes|n,cachePool:null,transitions:s.transitions},o.memoizedState=s,o.childLanes=t.childLanes&~n,e.memoizedState=Bl,r}return o=t.child,t=o.sibling,r=nn(o,{mode:"visible",children:r.children}),!(e.mode&1)&&(r.lanes=n),r.return=e,r.sibling=null,t!==null&&(n=e.deletions,n===null?(e.deletions=[t],e.flags|=16):n.push(t)),e.child=r,e.memoizedState=null,r}function Fl(t,e){return e=qo({mode:"visible",children:e},t.mode,0,null),e.return=t,t.child=e}function No(t,e,n,r){return r!==null&&pl(r),sr(e,t.child,null,n),t=Fl(e,e.pendingProps.children),t.flags|=2,e.memoizedState=null,t}function K0(t,e,n,r,i,o,s){if(n)return e.flags&256?(e.flags&=-257,r=Il(Error(j(422))),No(t,e,s,r)):e.memoizedState!==null?(e.child=t.child,e.flags|=128,null):(o=r.fallback,i=e.mode,r=qo({mode:"visible",children:r.children},i,0,null),o=Cn(o,i,s,null),o.flags|=2,r.return=e,o.return=e,r.sibling=o,e.child=r,e.mode&1&&sr(e,t.child,null,s),e.child.memoizedState=Nl(s),e.memoizedState=Bl,o);if(!(e.mode&1))return No(t,e,s,null);if(i.data==="$!"){if(r=i.nextSibling&&i.nextSibling.dataset,r)var l=r.dgst;return r=l,o=Error(j(419)),r=Il(o,r,void 0),No(t,e,s,r)}if(l=(s&t.childLanes)!==0,Ee||l){if(r=fe,r!==null){switch(s&-s){case 4:i=2;break;case 16:i=8;break;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:i=32;break;case 536870912:i=268435456;break;default:i=0}i=i&(r.suspendedLanes|s)?0:i,i!==0&&i!==o.retryLane&&(o.retryLane=i,Tt(t,i),st(r,t,i,-1))}return nc(),r=Il(Error(j(421))),No(t,e,s,r)}return i.data==="$?"?(e.flags|=128,e.child=t.child,e=ay.bind(null,t),i._reactRetry=e,null):(t=o.treeContext,Fe=Gt(i.nextSibling),Ne=e,q=!0,nt=null,t!==null&&(Ge[Ve++]=$t,Ge[Ve++]=Ct,Ge[Ve++]=mn,$t=t.id,Ct=t.overflow,mn=e),e=Fl(e,r.children),e.flags|=4096,e)}function kp(t,e,n){t.lanes|=e;var r=t.alternate;r!==null&&(r.lanes|=e),xl(t.return,e,n)}function _l(t,e,n,r,i){var o=t.memoizedState;o===null?t.memoizedState={isBackwards:e,rendering:null,renderingStartTime:0,last:r,tail:n,tailMode:i}:(o.isBackwards=e,o.rendering=null,o.renderingStartTime=0,o.last=r,o.tail=n,o.tailMode=i)}function $p(t,e,n){var r=e.pendingProps,i=r.revealOrder,o=r.tail;if(Ce(t,e,r.children,n),r=ee.current,r&2)r=r&1|2,e.flags|=128;else{if(t!==null&&t.flags&128)e:for(t=e.child;t!==null;){if(t.tag===13)t.memoizedState!==null&&kp(t,n,e);else if(t.tag===19)kp(t,n,e);else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break e;for(;t.sibling===null;){if(t.return===null||t.return===e)break e;t=t.return}t.sibling.return=t.return,t=t.sibling}r&=1}if(Y(ee,r),!(e.mode&1))e.memoizedState=null;else switch(i){case"forwards":for(n=e.child,i=null;n!==null;)t=n.alternate,t!==null&&Ao(t)===null&&(i=n),n=n.sibling;n=i,n===null?(i=e.child,e.child=null):(i=n.sibling,n.sibling=null),_l(e,!1,i,n,o);break;case"backwards":for(n=null,i=e.child,e.child=null;i!==null;){if(t=i.alternate,t!==null&&Ao(t)===null){e.child=i;break}t=i.sibling,i.sibling=n,n=i,i=t}_l(e,!0,n,null,o);break;case"together":_l(e,!1,null,null,void 0);break;default:e.memoizedState=null}return e.child}function Fo(t,e){!(e.mode&1)&&t!==null&&(t.alternate=null,e.alternate=null,e.flags|=2)}function jt(t,e,n){if(t!==null&&(e.dependencies=t.dependencies),bn|=e.lanes,!(n&e.childLanes))return null;if(t!==null&&e.child!==t.child)throw Error(j(153));if(e.child!==null){for(t=e.child,n=nn(t,t.pendingProps),e.child=n,n.return=e;t.sibling!==null;)t=t.sibling,n=n.sibling=nn(t,t.pendingProps),n.return=e;n.sibling=null}return e.child}function Q0(t,e,n){switch(e.tag){case 3:vp(e),or();break;case 5:Bh(e);break;case 1:je(e.type)&&So(e);break;case 4:vl(e,e.stateNode.containerInfo);break;case 10:var r=e.type._context,i=e.memoizedProps.value;Y(jo,r._currentValue),r._currentValue=i;break;case 13:if(r=e.memoizedState,r!==null)return r.dehydrated!==null?(Y(ee,ee.current&1),e.flags|=128,null):n&e.child.childLanes?Sp(t,e,n):(Y(ee,ee.current&1),t=jt(t,e,n),t!==null?t.sibling:null);Y(ee,ee.current&1);break;case 19:if(r=(n&e.childLanes)!==0,t.flags&128){if(r)return $p(t,e,n);e.flags|=128}if(i=e.memoizedState,i!==null&&(i.rendering=null,i.tail=null,i.lastEffect=null),Y(ee,ee.current),r)break;return null;case 22:case 23:return e.lanes=0,xp(t,e,n)}return jt(t,e,n)}var Cp,Hl,Tp,Mp;Cp=function(t,e){for(var n=e.child;n!==null;){if(n.tag===5||n.tag===6)t.appendChild(n.stateNode);else if(n.tag!==4&&n.child!==null){n.child.return=n,n=n.child;continue}if(n===e)break;for(;n.sibling===null;){if(n.return===null||n.return===e)return;n=n.return}n.sibling.return=n.return,n=n.sibling}},Hl=function(){},Tp=function(t,e,n,r){var i=t.memoizedProps;if(i!==r){t=e.stateNode,wn(mt.current);var o=null;switch(n){case"input":i=xa(t,i),r=xa(t,r),o=[];break;case"select":i=Z({},i,{value:void 0}),r=Z({},r,{value:void 0}),o=[];break;case"textarea":i=va(t,i),r=va(t,r),o=[];break;default:typeof i.onClick!="function"&&typeof r.onClick=="function"&&(t.onclick=wo)}Sa(n,r);var s;n=null;for(d in i)if(!r.hasOwnProperty(d)&&i.hasOwnProperty(d)&&i[d]!=null)if(d==="style"){var l=i[d];for(s in l)l.hasOwnProperty(s)&&(n||(n={}),n[s]="")}else d!=="dangerouslySetInnerHTML"&&d!=="children"&&d!=="suppressContentEditableWarning"&&d!=="suppressHydrationWarning"&&d!=="autoFocus"&&(Or.hasOwnProperty(d)?o||(o=[]):(o=o||[]).push(d,null));for(d in r){var c=r[d];if(l=i!=null?i[d]:void 0,r.hasOwnProperty(d)&&c!==l&&(c!=null||l!=null))if(d==="style")if(l){for(s in l)!l.hasOwnProperty(s)||c&&c.hasOwnProperty(s)||(n||(n={}),n[s]="");for(s in c)c.hasOwnProperty(s)&&l[s]!==c[s]&&(n||(n={}),n[s]=c[s])}else n||(o||(o=[]),o.push(d,n)),n=c;else d==="dangerouslySetInnerHTML"?(c=c?c.__html:void 0,l=l?l.__html:void 0,c!=null&&l!==c&&(o=o||[]).push(d,c)):d==="children"?typeof c!="string"&&typeof c!="number"||(o=o||[]).push(d,""+c):d!=="suppressContentEditableWarning"&&d!=="suppressHydrationWarning"&&(Or.hasOwnProperty(d)?(c!=null&&d==="onScroll"&&Q("scroll",t),o||l===c||(o=[])):(o=o||[]).push(d,c))}n&&(o=o||[]).push("style",n);var d=o;(e.updateQueue=d)&&(e.flags|=4)}},Mp=function(t,e,n,r){n!==r&&(e.flags|=4)};function xi(t,e){if(!q)switch(t.tailMode){case"hidden":e=t.tail;for(var n=null;e!==null;)e.alternate!==null&&(n=e),e=e.sibling;n===null?t.tail=null:n.sibling=null;break;case"collapsed":n=t.tail;for(var r=null;n!==null;)n.alternate!==null&&(r=n),n=n.sibling;r===null?e||t.tail===null?t.tail=null:t.tail.sibling=null:r.sibling=null}}function ve(t){var e=t.alternate!==null&&t.alternate.child===t.child,n=0,r=0;if(e)for(var i=t.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags&14680064,r|=i.flags&14680064,i.return=t,i=i.sibling;else for(i=t.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags,r|=i.flags,i.return=t,i=i.sibling;return t.subtreeFlags|=r,t.childLanes=n,e}function X0(t,e,n){var r=e.pendingProps;switch(dl(e),e.tag){case 2:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return ve(e),null;case 1:return je(e.type)&&bo(),ve(e),null;case 3:return r=e.stateNode,cr(),X(Me),X(ye),kl(),r.pendingContext&&(r.context=r.pendingContext,r.pendingContext=null),(t===null||t.child===null)&&(To(e)?e.flags|=4:t===null||t.memoizedState.isDehydrated&&!(e.flags&256)||(e.flags|=1024,nt!==null&&(Zl(nt),nt=null))),Hl(t,e),ve(e),null;case 5:bl(e);var i=wn(hi.current);if(n=e.type,t!==null&&e.stateNode!=null)Tp(t,e,n,r,i),t.ref!==e.ref&&(e.flags|=512,e.flags|=2097152);else{if(!r){if(e.stateNode===null)throw Error(j(166));return ve(e),null}if(t=wn(mt.current),To(e)){r=e.stateNode,n=e.type;var o=e.memoizedProps;switch(r[gt]=e,r[ai]=o,t=(e.mode&1)!==0,n){case"dialog":Q("cancel",r),Q("close",r);break;case"iframe":case"object":case"embed":Q("load",r);break;case"video":case"audio":for(i=0;i<ii.length;i++)Q(ii[i],r);break;case"source":Q("error",r);break;case"img":case"image":case"link":Q("error",r),Q("load",r);break;case"details":Q("toggle",r);break;case"input":au(r,o),Q("invalid",r);break;case"select":r._wrapperState={wasMultiple:!!o.multiple},Q("invalid",r);break;case"textarea":du(r,o),Q("invalid",r)}Sa(n,o),i=null;for(var s in o)if(o.hasOwnProperty(s)){var l=o[s];s==="children"?typeof l=="string"?r.textContent!==l&&(o.suppressHydrationWarning!==!0&&yo(r.textContent,l,t),i=["children",l]):typeof l=="number"&&r.textContent!==""+l&&(o.suppressHydrationWarning!==!0&&yo(r.textContent,l,t),i=["children",""+l]):Or.hasOwnProperty(s)&&l!=null&&s==="onScroll"&&Q("scroll",r)}switch(n){case"input":Ki(r),cu(r,o,!0);break;case"textarea":Ki(r),hu(r);break;case"select":case"option":break;default:typeof o.onClick=="function"&&(r.onclick=wo)}r=i,e.updateQueue=r,r!==null&&(e.flags|=4)}else{s=i.nodeType===9?i:i.ownerDocument,t==="http://www.w3.org/1999/xhtml"&&(t=pu(n)),t==="http://www.w3.org/1999/xhtml"?n==="script"?(t=s.createElement("div"),t.innerHTML="<script><\/script>",t=t.removeChild(t.firstChild)):typeof r.is=="string"?t=s.createElement(n,{is:r.is}):(t=s.createElement(n),n==="select"&&(s=t,r.multiple?s.multiple=!0:r.size&&(s.size=r.size))):t=s.createElementNS(t,n),t[gt]=e,t[ai]=r,Cp(t,e,!1,!1),e.stateNode=t;e:{switch(s=ka(n,r),n){case"dialog":Q("cancel",t),Q("close",t),i=r;break;case"iframe":case"object":case"embed":Q("load",t),i=r;break;case"video":case"audio":for(i=0;i<ii.length;i++)Q(ii[i],t);i=r;break;case"source":Q("error",t),i=r;break;case"img":case"image":case"link":Q("error",t),Q("load",t),i=r;break;case"details":Q("toggle",t),i=r;break;case"input":au(t,r),i=xa(t,r),Q("invalid",t);break;case"option":i=r;break;case"select":t._wrapperState={wasMultiple:!!r.multiple},i=Z({},r,{value:void 0}),Q("invalid",t);break;case"textarea":du(t,r),i=va(t,r),Q("invalid",t);break;default:i=r}Sa(n,i),l=i;for(o in l)if(l.hasOwnProperty(o)){var c=l[o];o==="style"?mu(t,c):o==="dangerouslySetInnerHTML"?(c=c?c.__html:void 0,c!=null&&fu(t,c)):o==="children"?typeof c=="string"?(n!=="textarea"||c!=="")&&Nr(t,c):typeof c=="number"&&Nr(t,""+c):o!=="suppressContentEditableWarning"&&o!=="suppressHydrationWarning"&&o!=="autoFocus"&&(Or.hasOwnProperty(o)?c!=null&&o==="onScroll"&&Q("scroll",t):c!=null&&sa(t,o,c,s))}switch(n){case"input":Ki(t),cu(t,r,!1);break;case"textarea":Ki(t),hu(t);break;case"option":r.value!=null&&t.setAttribute("value",""+Bt(r.value));break;case"select":t.multiple=!!r.multiple,o=r.value,o!=null?Un(t,!!r.multiple,o,!1):r.defaultValue!=null&&Un(t,!!r.multiple,r.defaultValue,!0);break;default:typeof i.onClick=="function"&&(t.onclick=wo)}switch(n){case"button":case"input":case"select":case"textarea":r=!!r.autoFocus;break e;case"img":r=!0;break e;default:r=!1}}r&&(e.flags|=4)}e.ref!==null&&(e.flags|=512,e.flags|=2097152)}return ve(e),null;case 6:if(t&&e.stateNode!=null)Mp(t,e,t.memoizedProps,r);else{if(typeof r!="string"&&e.stateNode===null)throw Error(j(166));if(n=wn(hi.current),wn(mt.current),To(e)){if(r=e.stateNode,n=e.memoizedProps,r[gt]=e,(o=r.nodeValue!==n)&&(t=Ne,t!==null))switch(t.tag){case 3:yo(r.nodeValue,n,(t.mode&1)!==0);break;case 5:t.memoizedProps.suppressHydrationWarning!==!0&&yo(r.nodeValue,n,(t.mode&1)!==0)}o&&(e.flags|=4)}else r=(n.nodeType===9?n:n.ownerDocument).createTextNode(r),r[gt]=e,e.stateNode=r}return ve(e),null;case 13:if(X(ee),r=e.memoizedState,t===null||t.memoizedState!==null&&t.memoizedState.dehydrated!==null){if(q&&Fe!==null&&e.mode&1&&!(e.flags&128))Dh(),or(),e.flags|=98560,o=!1;else if(o=To(e),r!==null&&r.dehydrated!==null){if(t===null){if(!o)throw Error(j(318));if(o=e.memoizedState,o=o!==null?o.dehydrated:null,!o)throw Error(j(317));o[gt]=e}else or(),!(e.flags&128)&&(e.memoizedState=null),e.flags|=4;ve(e),o=!1}else nt!==null&&(Zl(nt),nt=null),o=!0;if(!o)return e.flags&65536?e:null}return e.flags&128?(e.lanes=n,e):(r=r!==null,r!==(t!==null&&t.memoizedState!==null)&&r&&(e.child.flags|=8192,e.mode&1&&(t===null||ee.current&1?ce===0&&(ce=3):nc())),e.updateQueue!==null&&(e.flags|=4),ve(e),null);case 4:return cr(),Hl(t,e),t===null&&oi(e.stateNode.containerInfo),ve(e),null;case 10:return ml(e.type._context),ve(e),null;case 17:return je(e.type)&&bo(),ve(e),null;case 19:if(X(ee),o=e.memoizedState,o===null)return ve(e),null;if(r=(e.flags&128)!==0,s=o.rendering,s===null)if(r)xi(o,!1);else{if(ce!==0||t!==null&&t.flags&128)for(t=e.child;t!==null;){if(s=Ao(t),s!==null){for(e.flags|=128,xi(o,!1),r=s.updateQueue,r!==null&&(e.updateQueue=r,e.flags|=4),e.subtreeFlags=0,r=n,n=e.child;n!==null;)o=n,t=r,o.flags&=14680066,s=o.alternate,s===null?(o.childLanes=0,o.lanes=t,o.child=null,o.subtreeFlags=0,o.memoizedProps=null,o.memoizedState=null,o.updateQueue=null,o.dependencies=null,o.stateNode=null):(o.childLanes=s.childLanes,o.lanes=s.lanes,o.child=s.child,o.subtreeFlags=0,o.deletions=null,o.memoizedProps=s.memoizedProps,o.memoizedState=s.memoizedState,o.updateQueue=s.updateQueue,o.type=s.type,t=s.dependencies,o.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext}),n=n.sibling;return Y(ee,ee.current&1|2),e.child}t=t.sibling}o.tail!==null&&oe()>pr&&(e.flags|=128,r=!0,xi(o,!1),e.lanes=4194304)}else{if(!r)if(t=Ao(s),t!==null){if(e.flags|=128,r=!0,n=t.updateQueue,n!==null&&(e.updateQueue=n,e.flags|=4),xi(o,!0),o.tail===null&&o.tailMode==="hidden"&&!s.alternate&&!q)return ve(e),null}else 2*oe()-o.renderingStartTime>pr&&n!==1073741824&&(e.flags|=128,r=!0,xi(o,!1),e.lanes=4194304);o.isBackwards?(s.sibling=e.child,e.child=s):(n=o.last,n!==null?n.sibling=s:e.child=s,o.last=s)}return o.tail!==null?(e=o.tail,o.rendering=e,o.tail=e.sibling,o.renderingStartTime=oe(),e.sibling=null,n=ee.current,Y(ee,r?n&1|2:n&1),e):(ve(e),null);case 22:case 23:return tc(),r=e.memoizedState!==null,t!==null&&t.memoizedState!==null!==r&&(e.flags|=8192),r&&e.mode&1?_e&1073741824&&(ve(e),e.subtreeFlags&6&&(e.flags|=8192)):ve(e),null;case 24:return null;case 25:return null}throw Error(j(156,e.tag))}function J0(t,e){switch(dl(e),e.tag){case 1:return je(e.type)&&bo(),t=e.flags,t&65536?(e.flags=t&-65537|128,e):null;case 3:return cr(),X(Me),X(ye),kl(),t=e.flags,t&65536&&!(t&128)?(e.flags=t&-65537|128,e):null;case 5:return bl(e),null;case 13:if(X(ee),t=e.memoizedState,t!==null&&t.dehydrated!==null){if(e.alternate===null)throw Error(j(340));or()}return t=e.flags,t&65536?(e.flags=t&-65537|128,e):null;case 19:return X(ee),null;case 4:return cr(),null;case 10:return ml(e.type._context),null;case 22:case 23:return tc(),null;case 24:return null;default:return null}}var _o=!1,be=!1,q0=typeof WeakSet=="function"?WeakSet:Set,R=null;function ur(t,e){var n=t.ref;if(n!==null)if(typeof n=="function")try{n(null)}catch(r){re(t,e,r)}else n.current=null}function Wl(t,e,n){try{n()}catch(r){re(t,e,r)}}var jp=!1;function Z0(t,e){if(tl=so,t=sh(),Ya(t)){if("selectionStart"in t)var n={start:t.selectionStart,end:t.selectionEnd};else e:{n=(n=t.ownerDocument)&&n.defaultView||window;var r=n.getSelection&&n.getSelection();if(r&&r.rangeCount!==0){n=r.anchorNode;var i=r.anchorOffset,o=r.focusNode;r=r.focusOffset;try{n.nodeType,o.nodeType}catch{n=null;break e}var s=0,l=-1,c=-1,d=0,h=0,p=t,f=null;t:for(;;){for(var v;p!==n||i!==0&&p.nodeType!==3||(l=s+i),p!==o||r!==0&&p.nodeType!==3||(c=s+r),p.nodeType===3&&(s+=p.nodeValue.length),(v=p.firstChild)!==null;)f=p,p=v;for(;;){if(p===t)break t;if(f===n&&++d===i&&(l=s),f===o&&++h===r&&(c=s),(v=p.nextSibling)!==null)break;p=f,f=p.parentNode}p=v}n=l===-1||c===-1?null:{start:l,end:c}}else n=null}n=n||{start:0,end:0}}else n=null;for(nl={focusedElem:t,selectionRange:n},so=!1,R=e;R!==null;)if(e=R,t=e.child,(e.subtreeFlags&1028)!==0&&t!==null)t.return=e,R=t;else for(;R!==null;){e=R;try{var x=e.alternate;if(e.flags&1024)switch(e.tag){case 0:case 11:case 15:break;case 1:if(x!==null){var b=x.memoizedProps,M=x.memoizedState,w=e.stateNode,m=w.getSnapshotBeforeUpdate(e.elementType===e.type?b:rt(e.type,b),M);w.__reactInternalSnapshotBeforeUpdate=m}break;case 3:var y=e.stateNode.containerInfo;y.nodeType===1?y.textContent="":y.nodeType===9&&y.documentElement&&y.removeChild(y.documentElement);break;case 5:case 6:case 4:case 17:break;default:throw Error(j(163))}}catch(S){re(e,e.return,S)}if(t=e.sibling,t!==null){t.return=e.return,R=t;break}R=e.return}return x=jp,jp=!1,x}function yi(t,e,n){var r=e.updateQueue;if(r=r!==null?r.lastEffect:null,r!==null){var i=r=r.next;do{if((i.tag&t)===t){var o=i.destroy;i.destroy=void 0,o!==void 0&&Wl(e,n,o)}i=i.next}while(i!==r)}}function Ho(t,e){if(e=e.updateQueue,e=e!==null?e.lastEffect:null,e!==null){var n=e=e.next;do{if((n.tag&t)===t){var r=n.create;n.destroy=r()}n=n.next}while(n!==e)}}function Ul(t){var e=t.ref;if(e!==null){var n=t.stateNode;switch(t.tag){case 5:t=n;break;default:t=n}typeof e=="function"?e(t):e.current=t}}function Ep(t){var e=t.alternate;e!==null&&(t.alternate=null,Ep(e)),t.child=null,t.deletions=null,t.sibling=null,t.tag===5&&(e=t.stateNode,e!==null&&(delete e[gt],delete e[ai],delete e[sl],delete e[O0],delete e[L0])),t.stateNode=null,t.return=null,t.dependencies=null,t.memoizedProps=null,t.memoizedState=null,t.pendingProps=null,t.stateNode=null,t.updateQueue=null}function Dp(t){return t.tag===5||t.tag===3||t.tag===4}function Pp(t){e:for(;;){for(;t.sibling===null;){if(t.return===null||Dp(t.return))return null;t=t.return}for(t.sibling.return=t.return,t=t.sibling;t.tag!==5&&t.tag!==6&&t.tag!==18;){if(t.flags&2||t.child===null||t.tag===4)continue e;t.child.return=t,t=t.child}if(!(t.flags&2))return t.stateNode}}function Gl(t,e,n){var r=t.tag;if(r===5||r===6)t=t.stateNode,e?n.nodeType===8?n.parentNode.insertBefore(t,e):n.insertBefore(t,e):(n.nodeType===8?(e=n.parentNode,e.insertBefore(t,n)):(e=n,e.appendChild(t)),n=n._reactRootContainer,n!=null||e.onclick!==null||(e.onclick=wo));else if(r!==4&&(t=t.child,t!==null))for(Gl(t,e,n),t=t.sibling;t!==null;)Gl(t,e,n),t=t.sibling}function Vl(t,e,n){var r=t.tag;if(r===5||r===6)t=t.stateNode,e?n.insertBefore(t,e):n.appendChild(t);else if(r!==4&&(t=t.child,t!==null))for(Vl(t,e,n),t=t.sibling;t!==null;)Vl(t,e,n),t=t.sibling}var me=null,it=!1;function Jt(t,e,n){for(n=n.child;n!==null;)Ap(t,e,n),n=n.sibling}function Ap(t,e,n){if(ft&&typeof ft.onCommitFiberUnmount=="function")try{ft.onCommitFiberUnmount(eo,n)}catch{}switch(n.tag){case 5:be||ur(n,e);case 6:var r=me,i=it;me=null,Jt(t,e,n),me=r,it=i,me!==null&&(it?(t=me,n=n.stateNode,t.nodeType===8?t.parentNode.removeChild(n):t.removeChild(n)):me.removeChild(n.stateNode));break;case 18:me!==null&&(it?(t=me,n=n.stateNode,t.nodeType===8?ol(t.parentNode,n):t.nodeType===1&&ol(t,n),Xr(t)):ol(me,n.stateNode));break;case 4:r=me,i=it,me=n.stateNode.containerInfo,it=!0,Jt(t,e,n),me=r,it=i;break;case 0:case 11:case 14:case 15:if(!be&&(r=n.updateQueue,r!==null&&(r=r.lastEffect,r!==null))){i=r=r.next;do{var o=i,s=o.destroy;o=o.tag,s!==void 0&&(o&2||o&4)&&Wl(n,e,s),i=i.next}while(i!==r)}Jt(t,e,n);break;case 1:if(!be&&(ur(n,e),r=n.stateNode,typeof r.componentWillUnmount=="function"))try{r.props=n.memoizedProps,r.state=n.memoizedState,r.componentWillUnmount()}catch(l){re(n,e,l)}Jt(t,e,n);break;case 21:Jt(t,e,n);break;case 22:n.mode&1?(be=(r=be)||n.memoizedState!==null,Jt(t,e,n),be=r):Jt(t,e,n);break;default:Jt(t,e,n)}}function Rp(t){var e=t.updateQueue;if(e!==null){t.updateQueue=null;var n=t.stateNode;n===null&&(n=t.stateNode=new q0),e.forEach(function(r){var i=ly.bind(null,t,r);n.has(r)||(n.add(r),r.then(i,i))})}}function ot(t,e){var n=e.deletions;if(n!==null)for(var r=0;r<n.length;r++){var i=n[r];try{var o=t,s=e,l=s;e:for(;l!==null;){switch(l.tag){case 5:me=l.stateNode,it=!1;break e;case 3:me=l.stateNode.containerInfo,it=!0;break e;case 4:me=l.stateNode.containerInfo,it=!0;break e}l=l.return}if(me===null)throw Error(j(160));Ap(o,s,i),me=null,it=!1;var c=i.alternate;c!==null&&(c.return=null),i.return=null}catch(d){re(i,e,d)}}if(e.subtreeFlags&12854)for(e=e.child;e!==null;)Ip(e,t),e=e.sibling}function Ip(t,e){var n=t.alternate,r=t.flags;switch(t.tag){case 0:case 11:case 14:case 15:if(ot(e,t),yt(t),r&4){try{yi(3,t,t.return),Ho(3,t)}catch(b){re(t,t.return,b)}try{yi(5,t,t.return)}catch(b){re(t,t.return,b)}}break;case 1:ot(e,t),yt(t),r&512&&n!==null&&ur(n,n.return);break;case 5:if(ot(e,t),yt(t),r&512&&n!==null&&ur(n,n.return),t.flags&32){var i=t.stateNode;try{Nr(i,"")}catch(b){re(t,t.return,b)}}if(r&4&&(i=t.stateNode,i!=null)){var o=t.memoizedProps,s=n!==null?n.memoizedProps:o,l=t.type,c=t.updateQueue;if(t.updateQueue=null,c!==null)try{l==="input"&&o.type==="radio"&&o.name!=null&&lu(i,o),ka(l,s);var d=ka(l,o);for(s=0;s<c.length;s+=2){var h=c[s],p=c[s+1];h==="style"?mu(i,p):h==="dangerouslySetInnerHTML"?fu(i,p):h==="children"?Nr(i,p):sa(i,h,p,d)}switch(l){case"input":ya(i,o);break;case"textarea":uu(i,o);break;case"select":var f=i._wrapperState.wasMultiple;i._wrapperState.wasMultiple=!!o.multiple;var v=o.value;v!=null?Un(i,!!o.multiple,v,!1):f!==!!o.multiple&&(o.defaultValue!=null?Un(i,!!o.multiple,o.defaultValue,!0):Un(i,!!o.multiple,o.multiple?[]:"",!1))}i[ai]=o}catch(b){re(t,t.return,b)}}break;case 6:if(ot(e,t),yt(t),r&4){if(t.stateNode===null)throw Error(j(162));i=t.stateNode,o=t.memoizedProps;try{i.nodeValue=o}catch(b){re(t,t.return,b)}}break;case 3:if(ot(e,t),yt(t),r&4&&n!==null&&n.memoizedState.isDehydrated)try{Xr(e.containerInfo)}catch(b){re(t,t.return,b)}break;case 4:ot(e,t),yt(t);break;case 13:ot(e,t),yt(t),i=t.child,i.flags&8192&&(o=i.memoizedState!==null,i.stateNode.isHidden=o,!o||i.alternate!==null&&i.alternate.memoizedState!==null||(Ql=oe())),r&4&&Rp(t);break;case 22:if(h=n!==null&&n.memoizedState!==null,t.mode&1?(be=(d=be)||h,ot(e,t),be=d):ot(e,t),yt(t),r&8192){if(d=t.memoizedState!==null,(t.stateNode.isHidden=d)&&!h&&t.mode&1)for(R=t,h=t.child;h!==null;){for(p=R=h;R!==null;){switch(f=R,v=f.child,f.tag){case 0:case 11:case 14:case 15:yi(4,f,f.return);break;case 1:ur(f,f.return);var x=f.stateNode;if(typeof x.componentWillUnmount=="function"){r=f,n=f.return;try{e=r,x.props=e.memoizedProps,x.state=e.memoizedState,x.componentWillUnmount()}catch(b){re(r,n,b)}}break;case 5:ur(f,f.return);break;case 22:if(f.memoizedState!==null){zp(p);continue}}v!==null?(v.return=f,R=v):zp(p)}h=h.sibling}e:for(h=null,p=t;;){if(p.tag===5){if(h===null){h=p;try{i=p.stateNode,d?(o=i.style,typeof o.setProperty=="function"?o.setProperty("display","none","important"):o.display="none"):(l=p.stateNode,c=p.memoizedProps.style,s=c!=null&&c.hasOwnProperty("display")?c.display:null,l.style.display=gu("display",s))}catch(b){re(t,t.return,b)}}}else if(p.tag===6){if(h===null)try{p.stateNode.nodeValue=d?"":p.memoizedProps}catch(b){re(t,t.return,b)}}else if((p.tag!==22&&p.tag!==23||p.memoizedState===null||p===t)&&p.child!==null){p.child.return=p,p=p.child;continue}if(p===t)break e;for(;p.sibling===null;){if(p.return===null||p.return===t)break e;h===p&&(h=null),p=p.return}h===p&&(h=null),p.sibling.return=p.return,p=p.sibling}}break;case 19:ot(e,t),yt(t),r&4&&Rp(t);break;case 21:break;default:ot(e,t),yt(t)}}function yt(t){var e=t.flags;if(e&2){try{e:{for(var n=t.return;n!==null;){if(Dp(n)){var r=n;break e}n=n.return}throw Error(j(160))}switch(r.tag){case 5:var i=r.stateNode;r.flags&32&&(Nr(i,""),r.flags&=-33);var o=Pp(t);Vl(t,o,i);break;case 3:case 4:var s=r.stateNode.containerInfo,l=Pp(t);Gl(t,l,s);break;default:throw Error(j(161))}}catch(c){re(t,t.return,c)}t.flags&=-3}e&4096&&(t.flags&=-4097)}function ey(t,e,n){R=t,Op(t)}function Op(t,e,n){for(var r=(t.mode&1)!==0;R!==null;){var i=R,o=i.child;if(i.tag===22&&r){var s=i.memoizedState!==null||_o;if(!s){var l=i.alternate,c=l!==null&&l.memoizedState!==null||be;l=_o;var d=be;if(_o=s,(be=c)&&!d)for(R=i;R!==null;)s=R,c=s.child,s.tag===22&&s.memoizedState!==null?Bp(i):c!==null?(c.return=s,R=c):Bp(i);for(;o!==null;)R=o,Op(o),o=o.sibling;R=i,_o=l,be=d}Lp(t)}else i.subtreeFlags&8772&&o!==null?(o.return=i,R=o):Lp(t)}}function Lp(t){for(;R!==null;){var e=R;if(e.flags&8772){var n=e.alternate;try{if(e.flags&8772)switch(e.tag){case 0:case 11:case 15:be||Ho(5,e);break;case 1:var r=e.stateNode;if(e.flags&4&&!be)if(n===null)r.componentDidMount();else{var i=e.elementType===e.type?n.memoizedProps:rt(e.type,n.memoizedProps);r.componentDidUpdate(i,n.memoizedState,r.__reactInternalSnapshotBeforeUpdate)}var o=e.updateQueue;o!==null&&zh(e,o,r);break;case 3:var s=e.updateQueue;if(s!==null){if(n=null,e.child!==null)switch(e.child.tag){case 5:n=e.child.stateNode;break;case 1:n=e.child.stateNode}zh(e,s,n)}break;case 5:var l=e.stateNode;if(n===null&&e.flags&4){n=l;var c=e.memoizedProps;switch(e.type){case"button":case"input":case"select":case"textarea":c.autoFocus&&n.focus();break;case"img":c.src&&(n.src=c.src)}}break;case 6:break;case 4:break;case 12:break;case 13:if(e.memoizedState===null){var d=e.alternate;if(d!==null){var h=d.memoizedState;if(h!==null){var p=h.dehydrated;p!==null&&Xr(p)}}}break;case 19:case 17:case 21:case 22:case 23:case 25:break;default:throw Error(j(163))}be||e.flags&512&&Ul(e)}catch(f){re(e,e.return,f)}}if(e===t){R=null;break}if(n=e.sibling,n!==null){n.return=e.return,R=n;break}R=e.return}}function zp(t){for(;R!==null;){var e=R;if(e===t){R=null;break}var n=e.sibling;if(n!==null){n.return=e.return,R=n;break}R=e.return}}function Bp(t){for(;R!==null;){var e=R;try{switch(e.tag){case 0:case 11:case 15:var n=e.return;try{Ho(4,e)}catch(c){re(e,n,c)}break;case 1:var r=e.stateNode;if(typeof r.componentDidMount=="function"){var i=e.return;try{r.componentDidMount()}catch(c){re(e,i,c)}}var o=e.return;try{Ul(e)}catch(c){re(e,o,c)}break;case 5:var s=e.return;try{Ul(e)}catch(c){re(e,s,c)}}}catch(c){re(e,e.return,c)}if(e===t){R=null;break}var l=e.sibling;if(l!==null){l.return=e.return,R=l;break}R=e.return}}var ty=Math.ceil,Wo=bt.ReactCurrentDispatcher,Yl=bt.ReactCurrentOwner,Qe=bt.ReactCurrentBatchConfig,F=0,fe=null,se=null,xe=0,_e=0,hr=Vt(0),ce=0,wi=null,bn=0,Uo=0,Kl=0,vi=null,De=null,Ql=0,pr=1/0,Et=null,Go=!1,Xl=null,qt=null,Vo=!1,Zt=null,Yo=0,bi=0,Jl=null,Ko=-1,Qo=0;function Te(){return F&6?oe():Ko!==-1?Ko:Ko=oe()}function en(t){return t.mode&1?F&2&&xe!==0?xe&-xe:B0.transition!==null?(Qo===0&&(Qo=Pu()),Qo):(t=U,t!==0||(t=window.event,t=t===void 0?16:Fu(t.type)),t):1}function st(t,e,n,r){if(50<bi)throw bi=0,Jl=null,Error(j(185));Gr(t,n,r),(!(F&2)||t!==fe)&&(t===fe&&(!(F&2)&&(Uo|=n),ce===4&&tn(t,xe)),Pe(t,r),n===1&&F===0&&!(e.mode&1)&&(pr=oe()+500,ko&&Kt()))}function Pe(t,e){var n=t.callbackNode;Bx(t,e);var r=ro(t,t===fe?xe:0);if(r===0)n!==null&&ju(n),t.callbackNode=null,t.callbackPriority=0;else if(e=r&-r,t.callbackPriority!==e){if(n!=null&&ju(n),e===1)t.tag===0?z0(Fp.bind(null,t)):Ch(Fp.bind(null,t)),R0(function(){!(F&6)&&Kt()}),n=null;else{switch(Au(r)){case 1:n=Da;break;case 4:n=Eu;break;case 16:n=Zi;break;case 536870912:n=Du;break;default:n=Zi}n=Kp(n,Np.bind(null,t))}t.callbackPriority=e,t.callbackNode=n}}function Np(t,e){if(Ko=-1,Qo=0,F&6)throw Error(j(327));var n=t.callbackNode;if(fr()&&t.callbackNode!==n)return null;var r=ro(t,t===fe?xe:0);if(r===0)return null;if(r&30||r&t.expiredLanes||e)e=Xo(t,r);else{e=r;var i=F;F|=2;var o=Hp();(fe!==t||xe!==e)&&(Et=null,pr=oe()+500,kn(t,e));do try{iy();break}catch(l){_p(t,l)}while(!0);gl(),Wo.current=o,F=i,se!==null?e=0:(fe=null,xe=0,e=ce)}if(e!==0){if(e===2&&(i=Pa(t),i!==0&&(r=i,e=ql(t,i))),e===1)throw n=wi,kn(t,0),tn(t,r),Pe(t,oe()),n;if(e===6)tn(t,r);else{if(i=t.current.alternate,!(r&30)&&!ny(i)&&(e=Xo(t,r),e===2&&(o=Pa(t),o!==0&&(r=o,e=ql(t,o))),e===1))throw n=wi,kn(t,0),tn(t,r),Pe(t,oe()),n;switch(t.finishedWork=i,t.finishedLanes=r,e){case 0:case 1:throw Error(j(345));case 2:$n(t,De,Et);break;case 3:if(tn(t,r),(r&130023424)===r&&(e=Ql+500-oe(),10<e)){if(ro(t,0)!==0)break;if(i=t.suspendedLanes,(i&r)!==r){Te(),t.pingedLanes|=t.suspendedLanes&i;break}t.timeoutHandle=il($n.bind(null,t,De,Et),e);break}$n(t,De,Et);break;case 4:if(tn(t,r),(r&4194240)===r)break;for(e=t.eventTimes,i=-1;0<r;){var s=31-et(r);o=1<<s,s=e[s],s>i&&(i=s),r&=~o}if(r=i,r=oe()-r,r=(120>r?120:480>r?480:1080>r?1080:1920>r?1920:3e3>r?3e3:4320>r?4320:1960*ty(r/1960))-r,10<r){t.timeoutHandle=il($n.bind(null,t,De,Et),r);break}$n(t,De,Et);break;case 5:$n(t,De,Et);break;default:throw Error(j(329))}}}return Pe(t,oe()),t.callbackNode===n?Np.bind(null,t):null}function ql(t,e){var n=vi;return t.current.memoizedState.isDehydrated&&(kn(t,e).flags|=256),t=Xo(t,e),t!==2&&(e=De,De=n,e!==null&&Zl(e)),t}function Zl(t){De===null?De=t:De.push.apply(De,t)}function ny(t){for(var e=t;;){if(e.flags&16384){var n=e.updateQueue;if(n!==null&&(n=n.stores,n!==null))for(var r=0;r<n.length;r++){var i=n[r],o=i.getSnapshot;i=i.value;try{if(!tt(o(),i))return!1}catch{return!1}}}if(n=e.child,e.subtreeFlags&16384&&n!==null)n.return=e,e=n;else{if(e===t)break;for(;e.sibling===null;){if(e.return===null||e.return===t)return!0;e=e.return}e.sibling.return=e.return,e=e.sibling}}return!0}function tn(t,e){for(e&=~Kl,e&=~Uo,t.suspendedLanes|=e,t.pingedLanes&=~e,t=t.expirationTimes;0<e;){var n=31-et(e),r=1<<n;t[n]=-1,e&=~r}}function Fp(t){if(F&6)throw Error(j(327));fr();var e=ro(t,0);if(!(e&1))return Pe(t,oe()),null;var n=Xo(t,e);if(t.tag!==0&&n===2){var r=Pa(t);r!==0&&(e=r,n=ql(t,r))}if(n===1)throw n=wi,kn(t,0),tn(t,e),Pe(t,oe()),n;if(n===6)throw Error(j(345));return t.finishedWork=t.current.alternate,t.finishedLanes=e,$n(t,De,Et),Pe(t,oe()),null}function ec(t,e){var n=F;F|=1;try{return t(e)}finally{F=n,F===0&&(pr=oe()+500,ko&&Kt())}}function Sn(t){Zt!==null&&Zt.tag===0&&!(F&6)&&fr();var e=F;F|=1;var n=Qe.transition,r=U;try{if(Qe.transition=null,U=1,t)return t()}finally{U=r,Qe.transition=n,F=e,!(F&6)&&Kt()}}function tc(){_e=hr.current,X(hr)}function kn(t,e){t.finishedWork=null,t.finishedLanes=0;var n=t.timeoutHandle;if(n!==-1&&(t.timeoutHandle=-1,A0(n)),se!==null)for(n=se.return;n!==null;){var r=n;switch(dl(r),r.tag){case 1:r=r.type.childContextTypes,r!=null&&bo();break;case 3:cr(),X(Me),X(ye),kl();break;case 5:bl(r);break;case 4:cr();break;case 13:X(ee);break;case 19:X(ee);break;case 10:ml(r.type._context);break;case 22:case 23:tc()}n=n.return}if(fe=t,se=t=nn(t.current,null),xe=_e=e,ce=0,wi=null,Kl=Uo=bn=0,De=vi=null,yn!==null){for(e=0;e<yn.length;e++)if(n=yn[e],r=n.interleaved,r!==null){n.interleaved=null;var i=r.next,o=n.pending;if(o!==null){var s=o.next;o.next=i,r.next=s}n.pending=r}yn=null}return t}function _p(t,e){do{var n=se;try{if(gl(),Ro.current=zo,Io){for(var r=te.memoizedState;r!==null;){var i=r.queue;i!==null&&(i.pending=null),r=r.next}Io=!1}if(vn=0,pe=le=te=null,pi=!1,fi=0,Yl.current=null,n===null||n.return===null){ce=1,wi=e,se=null;break}e:{var o=t,s=n.return,l=n,c=e;if(e=xe,l.flags|=32768,c!==null&&typeof c=="object"&&typeof c.then=="function"){var d=c,h=l,p=h.tag;if(!(h.mode&1)&&(p===0||p===11||p===15)){var f=h.alternate;f?(h.updateQueue=f.updateQueue,h.memoizedState=f.memoizedState,h.lanes=f.lanes):(h.updateQueue=null,h.memoizedState=null)}var v=hp(s);if(v!==null){v.flags&=-257,pp(v,s,l,o,e),v.mode&1&&up(o,d,e),e=v,c=d;var x=e.updateQueue;if(x===null){var b=new Set;b.add(c),e.updateQueue=b}else x.add(c);break e}else{if(!(e&1)){up(o,d,e),nc();break e}c=Error(j(426))}}else if(q&&l.mode&1){var M=hp(s);if(M!==null){!(M.flags&65536)&&(M.flags|=256),pp(M,s,l,o,e),pl(dr(c,l));break e}}o=c=dr(c,l),ce!==4&&(ce=2),vi===null?vi=[o]:vi.push(o),o=s;do{switch(o.tag){case 3:o.flags|=65536,e&=-e,o.lanes|=e;var w=cp(o,c,e);Lh(o,w);break e;case 1:l=c;var m=o.type,y=o.stateNode;if(!(o.flags&128)&&(typeof m.getDerivedStateFromError=="function"||y!==null&&typeof y.componentDidCatch=="function"&&(qt===null||!qt.has(y)))){o.flags|=65536,e&=-e,o.lanes|=e;var S=dp(o,l,e);Lh(o,S);break e}}o=o.return}while(o!==null)}Up(n)}catch(T){e=T,se===n&&n!==null&&(se=n=n.return);continue}break}while(!0)}function Hp(){var t=Wo.current;return Wo.current=zo,t===null?zo:t}function nc(){(ce===0||ce===3||ce===2)&&(ce=4),fe===null||!(bn&268435455)&&!(Uo&268435455)||tn(fe,xe)}function Xo(t,e){var n=F;F|=2;var r=Hp();(fe!==t||xe!==e)&&(Et=null,kn(t,e));do try{ry();break}catch(i){_p(t,i)}while(!0);if(gl(),F=n,Wo.current=r,se!==null)throw Error(j(261));return fe=null,xe=0,ce}function ry(){for(;se!==null;)Wp(se)}function iy(){for(;se!==null&&!Ex();)Wp(se)}function Wp(t){var e=Yp(t.alternate,t,_e);t.memoizedProps=t.pendingProps,e===null?Up(t):se=e,Yl.current=null}function Up(t){var e=t;do{var n=e.alternate;if(t=e.return,e.flags&32768){if(n=J0(n,e),n!==null){n.flags&=32767,se=n;return}if(t!==null)t.flags|=32768,t.subtreeFlags=0,t.deletions=null;else{ce=6,se=null;return}}else if(n=X0(n,e,_e),n!==null){se=n;return}if(e=e.sibling,e!==null){se=e;return}se=e=t}while(e!==null);ce===0&&(ce=5)}function $n(t,e,n){var r=U,i=Qe.transition;try{Qe.transition=null,U=1,oy(t,e,n,r)}finally{Qe.transition=i,U=r}return null}function oy(t,e,n,r){do fr();while(Zt!==null);if(F&6)throw Error(j(327));n=t.finishedWork;var i=t.finishedLanes;if(n===null)return null;if(t.finishedWork=null,t.finishedLanes=0,n===t.current)throw Error(j(177));t.callbackNode=null,t.callbackPriority=0;var o=n.lanes|n.childLanes;if(Nx(t,o),t===fe&&(se=fe=null,xe=0),!(n.subtreeFlags&2064)&&!(n.flags&2064)||Vo||(Vo=!0,Kp(Zi,function(){return fr(),null})),o=(n.flags&15990)!==0,n.subtreeFlags&15990||o){o=Qe.transition,Qe.transition=null;var s=U;U=1;var l=F;F|=4,Yl.current=null,Z0(t,n),Ip(n,t),C0(nl),so=!!tl,nl=tl=null,t.current=n,ey(n),Dx(),F=l,U=s,Qe.transition=o}else t.current=n;if(Vo&&(Vo=!1,Zt=t,Yo=i),o=t.pendingLanes,o===0&&(qt=null),Rx(n.stateNode),Pe(t,oe()),e!==null)for(r=t.onRecoverableError,n=0;n<e.length;n++)i=e[n],r(i.value,{componentStack:i.stack,digest:i.digest});if(Go)throw Go=!1,t=Xl,Xl=null,t;return Yo&1&&t.tag!==0&&fr(),o=t.pendingLanes,o&1?t===Jl?bi++:(bi=0,Jl=t):bi=0,Kt(),null}function fr(){if(Zt!==null){var t=Au(Yo),e=Qe.transition,n=U;try{if(Qe.transition=null,U=16>t?16:t,Zt===null)var r=!1;else{if(t=Zt,Zt=null,Yo=0,F&6)throw Error(j(331));var i=F;for(F|=4,R=t.current;R!==null;){var o=R,s=o.child;if(R.flags&16){var l=o.deletions;if(l!==null){for(var c=0;c<l.length;c++){var d=l[c];for(R=d;R!==null;){var h=R;switch(h.tag){case 0:case 11:case 15:yi(8,h,o)}var p=h.child;if(p!==null)p.return=h,R=p;else for(;R!==null;){h=R;var f=h.sibling,v=h.return;if(Ep(h),h===d){R=null;break}if(f!==null){f.return=v,R=f;break}R=v}}}var x=o.alternate;if(x!==null){var b=x.child;if(b!==null){x.child=null;do{var M=b.sibling;b.sibling=null,b=M}while(b!==null)}}R=o}}if(o.subtreeFlags&2064&&s!==null)s.return=o,R=s;else e:for(;R!==null;){if(o=R,o.flags&2048)switch(o.tag){case 0:case 11:case 15:yi(9,o,o.return)}var w=o.sibling;if(w!==null){w.return=o.return,R=w;break e}R=o.return}}var m=t.current;for(R=m;R!==null;){s=R;var y=s.child;if(s.subtreeFlags&2064&&y!==null)y.return=s,R=y;else e:for(s=m;R!==null;){if(l=R,l.flags&2048)try{switch(l.tag){case 0:case 11:case 15:Ho(9,l)}}catch(T){re(l,l.return,T)}if(l===s){R=null;break e}var S=l.sibling;if(S!==null){S.return=l.return,R=S;break e}R=l.return}}if(F=i,Kt(),ft&&typeof ft.onPostCommitFiberRoot=="function")try{ft.onPostCommitFiberRoot(eo,t)}catch{}r=!0}return r}finally{U=n,Qe.transition=e}}return!1}function Gp(t,e,n){e=dr(n,e),e=cp(t,e,1),t=Xt(t,e,1),e=Te(),t!==null&&(Gr(t,1,e),Pe(t,e))}function re(t,e,n){if(t.tag===3)Gp(t,t,n);else for(;e!==null;){if(e.tag===3){Gp(e,t,n);break}else if(e.tag===1){var r=e.stateNode;if(typeof e.type.getDerivedStateFromError=="function"||typeof r.componentDidCatch=="function"&&(qt===null||!qt.has(r))){t=dr(n,t),t=dp(e,t,1),e=Xt(e,t,1),t=Te(),e!==null&&(Gr(e,1,t),Pe(e,t));break}}e=e.return}}function sy(t,e,n){var r=t.pingCache;r!==null&&r.delete(e),e=Te(),t.pingedLanes|=t.suspendedLanes&n,fe===t&&(xe&n)===n&&(ce===4||ce===3&&(xe&130023424)===xe&&500>oe()-Ql?kn(t,0):Kl|=n),Pe(t,e)}function Vp(t,e){e===0&&(t.mode&1?(e=no,no<<=1,!(no&130023424)&&(no=4194304)):e=1);var n=Te();t=Tt(t,e),t!==null&&(Gr(t,e,n),Pe(t,n))}function ay(t){var e=t.memoizedState,n=0;e!==null&&(n=e.retryLane),Vp(t,n)}function ly(t,e){var n=0;switch(t.tag){case 13:var r=t.stateNode,i=t.memoizedState;i!==null&&(n=i.retryLane);break;case 19:r=t.stateNode;break;default:throw Error(j(314))}r!==null&&r.delete(e),Vp(t,n)}var Yp;Yp=function(t,e,n){if(t!==null)if(t.memoizedProps!==e.pendingProps||Me.current)Ee=!0;else{if(!(t.lanes&n)&&!(e.flags&128))return Ee=!1,Q0(t,e,n);Ee=!!(t.flags&131072)}else Ee=!1,q&&e.flags&1048576&&Th(e,Co,e.index);switch(e.lanes=0,e.tag){case 2:var r=e.type;Fo(t,e),t=e.pendingProps;var i=nr(e,ye.current);lr(e,n),i=Tl(null,e,r,t,i,n);var o=Ml();return e.flags|=1,typeof i=="object"&&i!==null&&typeof i.render=="function"&&i.$$typeof===void 0?(e.tag=1,e.memoizedState=null,e.updateQueue=null,je(r)?(o=!0,So(e)):o=!1,e.memoizedState=i.state!==null&&i.state!==void 0?i.state:null,wl(e),i.updater=Bo,e.stateNode=i,i._reactInternals=e,Rl(e,r,t,n),e=zl(null,e,r,!0,o,n)):(e.tag=0,q&&o&&cl(e),Ce(null,e,i,n),e=e.child),e;case 16:r=e.elementType;e:{switch(Fo(t,e),t=e.pendingProps,i=r._init,r=i(r._payload),e.type=r,i=e.tag=dy(r),t=rt(r,t),i){case 0:e=Ll(null,e,r,t,n);break e;case 1:e=wp(null,e,r,t,n);break e;case 11:e=fp(null,e,r,t,n);break e;case 14:e=gp(null,e,r,rt(r.type,t),n);break e}throw Error(j(306,r,""))}return e;case 0:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),Ll(t,e,r,i,n);case 1:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),wp(t,e,r,i,n);case 3:e:{if(vp(e),t===null)throw Error(j(387));r=e.pendingProps,o=e.memoizedState,i=o.element,Oh(t,e),Po(e,r,null,n);var s=e.memoizedState;if(r=s.element,o.isDehydrated)if(o={element:r,isDehydrated:!1,cache:s.cache,pendingSuspenseBoundaries:s.pendingSuspenseBoundaries,transitions:s.transitions},e.updateQueue.baseState=o,e.memoizedState=o,e.flags&256){i=dr(Error(j(423)),e),e=bp(t,e,r,n,i);break e}else if(r!==i){i=dr(Error(j(424)),e),e=bp(t,e,r,n,i);break e}else for(Fe=Gt(e.stateNode.containerInfo.firstChild),Ne=e,q=!0,nt=null,n=Rh(e,null,r,n),e.child=n;n;)n.flags=n.flags&-3|4096,n=n.sibling;else{if(or(),r===i){e=jt(t,e,n);break e}Ce(t,e,r,n)}e=e.child}return e;case 5:return Bh(e),t===null&&hl(e),r=e.type,i=e.pendingProps,o=t!==null?t.memoizedProps:null,s=i.children,rl(r,i)?s=null:o!==null&&rl(r,o)&&(e.flags|=32),yp(t,e),Ce(t,e,s,n),e.child;case 6:return t===null&&hl(e),null;case 13:return Sp(t,e,n);case 4:return vl(e,e.stateNode.containerInfo),r=e.pendingProps,t===null?e.child=sr(e,null,r,n):Ce(t,e,r,n),e.child;case 11:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),fp(t,e,r,i,n);case 7:return Ce(t,e,e.pendingProps,n),e.child;case 8:return Ce(t,e,e.pendingProps.children,n),e.child;case 12:return Ce(t,e,e.pendingProps.children,n),e.child;case 10:e:{if(r=e.type._context,i=e.pendingProps,o=e.memoizedProps,s=i.value,Y(jo,r._currentValue),r._currentValue=s,o!==null)if(tt(o.value,s)){if(o.children===i.children&&!Me.current){e=jt(t,e,n);break e}}else for(o=e.child,o!==null&&(o.return=e);o!==null;){var l=o.dependencies;if(l!==null){s=o.child;for(var c=l.firstContext;c!==null;){if(c.context===r){if(o.tag===1){c=Mt(-1,n&-n),c.tag=2;var d=o.updateQueue;if(d!==null){d=d.shared;var h=d.pending;h===null?c.next=c:(c.next=h.next,h.next=c),d.pending=c}}o.lanes|=n,c=o.alternate,c!==null&&(c.lanes|=n),xl(o.return,n,e),l.lanes|=n;break}c=c.next}}else if(o.tag===10)s=o.type===e.type?null:o.child;else if(o.tag===18){if(s=o.return,s===null)throw Error(j(341));s.lanes|=n,l=s.alternate,l!==null&&(l.lanes|=n),xl(s,n,e),s=o.sibling}else s=o.child;if(s!==null)s.return=o;else for(s=o;s!==null;){if(s===e){s=null;break}if(o=s.sibling,o!==null){o.return=s.return,s=o;break}s=s.return}o=s}Ce(t,e,i.children,n),e=e.child}return e;case 9:return i=e.type,r=e.pendingProps.children,lr(e,n),i=Ye(i),r=r(i),e.flags|=1,Ce(t,e,r,n),e.child;case 14:return r=e.type,i=rt(r,e.pendingProps),i=rt(r.type,i),gp(t,e,r,i,n);case 15:return mp(t,e,e.type,e.pendingProps,n);case 17:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),Fo(t,e),e.tag=1,je(r)?(t=!0,So(e)):t=!1,lr(e,n),ap(e,r,i),Rl(e,r,i,n),zl(null,e,r,!0,t,n);case 19:return $p(t,e,n);case 22:return xp(t,e,n)}throw Error(j(156,e.tag))};function Kp(t,e){return Mu(t,e)}function cy(t,e,n,r){this.tag=t,this.key=n,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.ref=null,this.pendingProps=e,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=r,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function Xe(t,e,n,r){return new cy(t,e,n,r)}function rc(t){return t=t.prototype,!(!t||!t.isReactComponent)}function dy(t){if(typeof t=="function")return rc(t)?1:0;if(t!=null){if(t=t.$$typeof,t===ca)return 11;if(t===ha)return 14}return 2}function nn(t,e){var n=t.alternate;return n===null?(n=Xe(t.tag,e,t.key,t.mode),n.elementType=t.elementType,n.type=t.type,n.stateNode=t.stateNode,n.alternate=t,t.alternate=n):(n.pendingProps=e,n.type=t.type,n.flags=0,n.subtreeFlags=0,n.deletions=null),n.flags=t.flags&14680064,n.childLanes=t.childLanes,n.lanes=t.lanes,n.child=t.child,n.memoizedProps=t.memoizedProps,n.memoizedState=t.memoizedState,n.updateQueue=t.updateQueue,e=t.dependencies,n.dependencies=e===null?null:{lanes:e.lanes,firstContext:e.firstContext},n.sibling=t.sibling,n.index=t.index,n.ref=t.ref,n}function Jo(t,e,n,r,i,o){var s=2;if(r=t,typeof t=="function")rc(t)&&(s=1);else if(typeof t=="string")s=5;else e:switch(t){case Wn:return Cn(n.children,i,o,e);case aa:s=8,i|=8;break;case la:return t=Xe(12,n,e,i|2),t.elementType=la,t.lanes=o,t;case da:return t=Xe(13,n,e,i),t.elementType=da,t.lanes=o,t;case ua:return t=Xe(19,n,e,i),t.elementType=ua,t.lanes=o,t;case ru:return qo(n,i,o,e);default:if(typeof t=="object"&&t!==null)switch(t.$$typeof){case tu:s=10;break e;case nu:s=9;break e;case ca:s=11;break e;case ha:s=14;break e;case zt:s=16,r=null;break e}throw Error(j(130,t==null?t:typeof t,""))}return e=Xe(s,n,e,i),e.elementType=t,e.type=r,e.lanes=o,e}function Cn(t,e,n,r){return t=Xe(7,t,r,e),t.lanes=n,t}function qo(t,e,n,r){return t=Xe(22,t,r,e),t.elementType=ru,t.lanes=n,t.stateNode={isHidden:!1},t}function ic(t,e,n){return t=Xe(6,t,null,e),t.lanes=n,t}function oc(t,e,n){return e=Xe(4,t.children!==null?t.children:[],t.key,e),e.lanes=n,e.stateNode={containerInfo:t.containerInfo,pendingChildren:null,implementation:t.implementation},e}function uy(t,e,n,r,i){this.tag=e,this.containerInfo=t,this.finishedWork=this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.pendingContext=this.context=null,this.callbackPriority=0,this.eventTimes=Aa(0),this.expirationTimes=Aa(-1),this.entangledLanes=this.finishedLanes=this.mutableReadLanes=this.expiredLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=Aa(0),this.identifierPrefix=r,this.onRecoverableError=i,this.mutableSourceEagerHydrationData=null}function sc(t,e,n,r,i,o,s,l,c){return t=new uy(t,e,n,l,c),e===1?(e=1,o===!0&&(e|=8)):e=0,o=Xe(3,null,null,e),t.current=o,o.stateNode=t,o.memoizedState={element:r,isDehydrated:n,cache:null,transitions:null,pendingSuspenseBoundaries:null},wl(o),t}function hy(t,e,n){var r=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:Hn,key:r==null?null:""+r,children:t,containerInfo:e,implementation:n}}function Qp(t){if(!t)return Yt;t=t._reactInternals;e:{if(pn(t)!==t||t.tag!==1)throw Error(j(170));var e=t;do{switch(e.tag){case 3:e=e.stateNode.context;break e;case 1:if(je(e.type)){e=e.stateNode.__reactInternalMemoizedMergedChildContext;break e}}e=e.return}while(e!==null);throw Error(j(171))}if(t.tag===1){var n=t.type;if(je(n))return kh(t,n,e)}return e}function Xp(t,e,n,r,i,o,s,l,c){return t=sc(n,r,!0,t,i,o,s,l,c),t.context=Qp(null),n=t.current,r=Te(),i=en(n),o=Mt(r,i),o.callback=e??null,Xt(n,o,i),t.current.lanes=i,Gr(t,i,r),Pe(t,r),t}function Zo(t,e,n,r){var i=e.current,o=Te(),s=en(i);return n=Qp(n),e.context===null?e.context=n:e.pendingContext=n,e=Mt(o,s),e.payload={element:t},r=r===void 0?null:r,r!==null&&(e.callback=r),t=Xt(i,e,s),t!==null&&(st(t,i,s,o),Do(t,i,s)),s}function es(t){if(t=t.current,!t.child)return null;switch(t.child.tag){case 5:return t.child.stateNode;default:return t.child.stateNode}}function Jp(t,e){if(t=t.memoizedState,t!==null&&t.dehydrated!==null){var n=t.retryLane;t.retryLane=n!==0&&n<e?n:e}}function ac(t,e){Jp(t,e),(t=t.alternate)&&Jp(t,e)}function py(){return null}var qp=typeof reportError=="function"?reportError:function(t){console.error(t)};function lc(t){this._internalRoot=t}ts.prototype.render=lc.prototype.render=function(t){var e=this._internalRoot;if(e===null)throw Error(j(409));Zo(t,e,null,null)},ts.prototype.unmount=lc.prototype.unmount=function(){var t=this._internalRoot;if(t!==null){this._internalRoot=null;var e=t.containerInfo;Sn(function(){Zo(null,t,null,null)}),e[St]=null}};function ts(t){this._internalRoot=t}ts.prototype.unstable_scheduleHydration=function(t){if(t){var e=Ou();t={blockedOn:null,target:t,priority:e};for(var n=0;n<Ht.length&&e!==0&&e<Ht[n].priority;n++);Ht.splice(n,0,t),n===0&&Bu(t)}};function cc(t){return!(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11)}function ns(t){return!(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11&&(t.nodeType!==8||t.nodeValue!==" react-mount-point-unstable "))}function Zp(){}function fy(t,e,n,r,i){if(i){if(typeof r=="function"){var o=r;r=function(){var d=es(s);o.call(d)}}var s=Xp(e,r,t,0,null,!1,!1,"",Zp);return t._reactRootContainer=s,t[St]=s.current,oi(t.nodeType===8?t.parentNode:t),Sn(),s}for(;i=t.lastChild;)t.removeChild(i);if(typeof r=="function"){var l=r;r=function(){var d=es(c);l.call(d)}}var c=sc(t,0,!1,null,null,!1,!1,"",Zp);return t._reactRootContainer=c,t[St]=c.current,oi(t.nodeType===8?t.parentNode:t),Sn(function(){Zo(e,c,n,r)}),c}function rs(t,e,n,r,i){var o=n._reactRootContainer;if(o){var s=o;if(typeof i=="function"){var l=i;i=function(){var c=es(s);l.call(c)}}Zo(e,s,t,i)}else s=fy(n,e,t,i,r);return es(s)}Ru=function(t){switch(t.tag){case 3:var e=t.stateNode;if(e.current.memoizedState.isDehydrated){var n=Ur(e.pendingLanes);n!==0&&(Ra(e,n|1),Pe(e,oe()),!(F&6)&&(pr=oe()+500,Kt()))}break;case 13:Sn(function(){var r=Tt(t,1);if(r!==null){var i=Te();st(r,t,1,i)}}),ac(t,1)}},Ia=function(t){if(t.tag===13){var e=Tt(t,134217728);if(e!==null){var n=Te();st(e,t,134217728,n)}ac(t,134217728)}},Iu=function(t){if(t.tag===13){var e=en(t),n=Tt(t,e);if(n!==null){var r=Te();st(n,t,e,r)}ac(t,e)}},Ou=function(){return U},Lu=function(t,e){var n=U;try{return U=t,e()}finally{U=n}},Ta=function(t,e,n){switch(e){case"input":if(ya(t,n),e=n.name,n.type==="radio"&&e!=null){for(n=t;n.parentNode;)n=n.parentNode;for(n=n.querySelectorAll("input[name="+JSON.stringify(""+e)+'][type="radio"]'),e=0;e<n.length;e++){var r=n[e];if(r!==t&&r.form===t.form){var i=vo(r);if(!i)throw Error(j(90));su(r),ya(r,i)}}}break;case"textarea":uu(t,n);break;case"select":e=n.value,e!=null&&Un(t,!!n.multiple,e,!1)}},vu=ec,bu=Sn;var gy={usingClientEntryPoint:!1,Events:[li,er,vo,yu,wu,ec]},Si={findFiberByHostInstance:fn,bundleType:0,version:"18.3.1",rendererPackageName:"react-dom"},my={bundleType:Si.bundleType,version:Si.version,rendererPackageName:Si.rendererPackageName,rendererConfig:Si.rendererConfig,overrideHookState:null,overrideHookStateDeletePath:null,overrideHookStateRenamePath:null,overrideProps:null,overridePropsDeletePath:null,overridePropsRenamePath:null,setErrorHandler:null,setSuspenseHandler:null,scheduleUpdate:null,currentDispatcherRef:bt.ReactCurrentDispatcher,findHostInstanceByFiber:function(t){return t=Cu(t),t===null?null:t.stateNode},findFiberByHostInstance:Si.findFiberByHostInstance||py,findHostInstancesForRefresh:null,scheduleRefresh:null,scheduleRoot:null,setRefreshHandler:null,getCurrentFiber:null,reconcilerVersion:"18.3.1-next-f1338f8080-20240426"};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"){var is=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!is.isDisabled&&is.supportsFiber)try{eo=is.inject(my),ft=is}catch{}}Le.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=gy,Le.createPortal=function(t,e){var n=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!cc(e))throw Error(j(200));return hy(t,e,null,n)},Le.createRoot=function(t,e){if(!cc(t))throw Error(j(299));var n=!1,r="",i=qp;return e!=null&&(e.unstable_strictMode===!0&&(n=!0),e.identifierPrefix!==void 0&&(r=e.identifierPrefix),e.onRecoverableError!==void 0&&(i=e.onRecoverableError)),e=sc(t,1,!1,null,null,n,!1,r,i),t[St]=e.current,oi(t.nodeType===8?t.parentNode:t),new lc(e)},Le.findDOMNode=function(t){if(t==null)return null;if(t.nodeType===1)return t;var e=t._reactInternals;if(e===void 0)throw typeof t.render=="function"?Error(j(188)):(t=Object.keys(t).join(","),Error(j(268,t)));return t=Cu(e),t=t===null?null:t.stateNode,t},Le.flushSync=function(t){return Sn(t)},Le.hydrate=function(t,e,n){if(!ns(e))throw Error(j(200));return rs(null,t,e,!0,n)},Le.hydrateRoot=function(t,e,n){if(!cc(t))throw Error(j(405));var r=n!=null&&n.hydratedSources||null,i=!1,o="",s=qp;if(n!=null&&(n.unstable_strictMode===!0&&(i=!0),n.identifierPrefix!==void 0&&(o=n.identifierPrefix),n.onRecoverableError!==void 0&&(s=n.onRecoverableError)),e=Xp(e,null,t,1,n??null,i,!1,o,s),t[St]=e.current,oi(t),r)for(t=0;t<r.length;t++)n=r[t],i=n._getVersion,i=i(n._source),e.mutableSourceEagerHydrationData==null?e.mutableSourceEagerHydrationData=[n,i]:e.mutableSourceEagerHydrationData.push(n,i);return new ts(e)},Le.render=function(t,e,n){if(!ns(e))throw Error(j(200));return rs(null,t,e,!1,n)},Le.unmountComponentAtNode=function(t){if(!ns(t))throw Error(j(40));return t._reactRootContainer?(Sn(function(){rs(null,null,t,!1,function(){t._reactRootContainer=null,t[St]=null})}),!0):!1},Le.unstable_batchedUpdates=ec,Le.unstable_renderSubtreeIntoContainer=function(t,e,n,r){if(!ns(n))throw Error(j(200));if(t==null||t._reactInternals===void 0)throw Error(j(38));return rs(t,e,n,!1,r)},Le.version="18.3.1-next-f1338f8080-20240426";function ef(){if(!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u"||typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE!="function"))try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(ef)}catch(t){console.error(t)}}ef(),Qd.exports=Le;var xy=Qd.exports,tf,nf=xy;tf=nf.createRoot,nf.hydrateRoot;var Ae=function(){return Ae=Object.assign||function(e){for(var n,r=1,i=arguments.length;r<i;r++){n=arguments[r];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},Ae.apply(this,arguments)};function os(t,e,n){if(n||arguments.length===2)for(var r=0,i=e.length,o;r<i;r++)(o||!(r in e))&&(o||(o=Array.prototype.slice.call(e,0,r)),o[r]=e[r]);return t.concat(o||Array.prototype.slice.call(e))}typeof SuppressedError=="function"&&SuppressedError;var J="-ms-",ki="-moz-",_="-webkit-",rf="comm",ss="rule",dc="decl",yy="@import",wy="@namespace",of="@keyframes",vy="@layer",sf=Math.abs,uc=String.fromCharCode,hc=Object.assign;function by(t,e){return de(t,0)^45?(((e<<2^de(t,0))<<2^de(t,1))<<2^de(t,2))<<2^de(t,3):0}function af(t){return t.trim()}function Dt(t,e){return(t=e.exec(t))?t[0]:t}function z(t,e,n){return t.replace(e,n)}function as(t,e,n){return t.indexOf(e,n)}function de(t,e){return t.charCodeAt(e)|0}function Tn(t,e,n){return t.slice(e,n)}function at(t){return t.length}function lf(t){return t.length}function $i(t,e){return e.push(t),t}function Sy(t,e){return t.map(e).join("")}function cf(t,e){return t.filter(function(n){return!Dt(n,e)})}var ls=1,gr=1,df=0,Je=0,ae=0,mr="";function cs(t,e,n,r,i,o,s,l){return{value:t,root:e,parent:n,type:r,props:i,children:o,line:ls,column:gr,length:s,return:"",siblings:l}}function rn(t,e){return hc(cs("",null,null,"",null,null,0,t.siblings),t,{length:-t.length},e)}function xr(t){for(;t.root;)t=rn(t.root,{children:[t]});$i(t,t.siblings)}function ky(){return ae}function $y(){return ae=Je>0?de(mr,--Je):0,gr--,ae===10&&(gr=1,ls--),ae}function lt(){return ae=Je<df?de(mr,Je++):0,gr++,ae===10&&(gr=1,ls++),ae}function on(){return de(mr,Je)}function ds(){return Je}function us(t,e){return Tn(mr,t,e)}function Ci(t){switch(t){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function Cy(t){return ls=gr=1,df=at(mr=t),Je=0,[]}function Ty(t){return mr="",t}function pc(t){return af(us(Je-1,fc(t===91?t+2:t===40?t+1:t)))}function My(t){for(;(ae=on())&&ae<33;)lt();return Ci(t)>2||Ci(ae)>3?"":" "}function jy(t,e){for(;--e&&lt()&&!(ae<48||ae>102||ae>57&&ae<65||ae>70&&ae<97););return us(t,ds()+(e<6&&on()==32&&lt()==32))}function fc(t){for(;lt();)switch(ae){case t:return Je;case 34:case 39:t!==34&&t!==39&&fc(ae);break;case 40:t===41&&fc(t);break;case 92:lt();break}return Je}function Ey(t,e){for(;lt()&&t+ae!==57;)if(t+ae===84&&on()===47)break;return"/*"+us(e,Je-1)+"*"+uc(t===47?t:lt())}function Dy(t){for(;!Ci(on());)lt();return us(t,Je)}function Py(t){return Ty(hs("",null,null,null,[""],t=Cy(t),0,[0],t))}function hs(t,e,n,r,i,o,s,l,c){for(var d=0,h=0,p=s,f=0,v=0,x=0,b=1,M=1,w=1,m=0,y="",S=i,T=o,C=r,k=y;M;)switch(x=m,m=lt()){case 40:if(x!=108&&de(k,p-1)==58){as(k+=z(pc(m),"&","&\f"),"&\f",sf(d?l[d-1]:0))!=-1&&(w=-1);break}case 34:case 39:case 91:k+=pc(m);break;case 9:case 10:case 13:case 32:k+=My(x);break;case 92:k+=jy(ds()-1,7);continue;case 47:switch(on()){case 42:case 47:$i(Ay(Ey(lt(),ds()),e,n,c),c),(Ci(x||1)==5||Ci(on()||1)==5)&&at(k)&&Tn(k,-1,void 0)!==" "&&(k+=" ");break;default:k+="/"}break;case 123*b:l[d++]=at(k)*w;case 125*b:case 59:case 0:switch(m){case 0:case 125:M=0;case 59+h:w==-1&&(k=z(k,/\f/g,"")),v>0&&(at(k)-p||b===0&&x===47)&&$i(v>32?hf(k+";",r,n,p-1,c):hf(z(k," ","")+";",r,n,p-2,c),c);break;case 59:k+=";";default:if($i(C=uf(k,e,n,d,h,i,l,y,S=[],T=[],p,o),o),m===123)if(h===0)hs(k,e,C,C,S,o,p,l,T);else{switch(f){case 99:if(de(k,3)===110)break;case 108:if(de(k,2)===97)break;default:h=0;case 100:case 109:case 115:}h?hs(t,C,C,r&&$i(uf(t,C,C,0,0,i,l,y,i,S=[],p,T),T),i,T,p,l,r?S:T):hs(k,C,C,C,[""],T,0,l,T)}}d=h=v=0,b=w=1,y=k="",p=s;break;case 58:p=1+at(k),v=x;default:if(b<1){if(m==123)--b;else if(m==125&&b++==0&&$y()==125)continue}switch(k+=uc(m),m*b){case 38:w=h>0?1:(k+="\f",-1);break;case 44:l[d++]=(at(k)-1)*w,w=1;break;case 64:on()===45&&(k+=pc(lt())),f=on(),h=p=at(y=k+=Dy(ds())),m++;break;case 45:x===45&&at(k)==2&&(b=0)}}return o}function uf(t,e,n,r,i,o,s,l,c,d,h,p){for(var f=i-1,v=i===0?o:[""],x=lf(v),b=0,M=0,w=0;b<r;++b)for(var m=0,y=Tn(t,f+1,f=sf(M=s[b])),S=t;m<x;++m)(S=af(M>0?v[m]+" "+y:z(y,/&\f/g,v[m])))&&(c[w++]=S);return cs(t,e,n,i===0?ss:l,c,d,h,p)}function Ay(t,e,n,r){return cs(t,e,n,rf,uc(ky()),Tn(t,2,-2),0,r)}function hf(t,e,n,r,i){return cs(t,e,n,dc,Tn(t,0,r),Tn(t,r+1,-1),r,i)}function pf(t,e,n){switch(by(t,e)){case 5103:return _+"print-"+t+t;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return _+t+t;case 4855:return _+t.replace("add","source-over").replace("substract","source-out").replace("intersect","source-in").replace("exclude","xor")+t;case 4789:return ki+t+t;case 5349:case 4246:case 4810:case 6968:case 2756:return _+t+ki+t+J+t+t;case 5936:switch(de(t,e+11)){case 114:return _+t+J+z(t,/[svh]\w+-[tblr]{2}/,"tb")+t;case 108:return _+t+J+z(t,/[svh]\w+-[tblr]{2}/,"tb-rl")+t;case 45:return _+t+J+z(t,/[svh]\w+-[tblr]{2}/,"lr")+t}case 6828:case 4268:case 2903:return _+t+J+t+t;case 6165:return _+t+J+"flex-"+t+t;case 5187:return _+t+z(t,/(\w+).+(:[^]+)/,_+"box-$1$2"+J+"flex-$1$2")+t;case 5443:return _+t+J+"flex-item-"+z(t,/flex-|-self/g,"")+(Dt(t,/flex-|baseline/)?"":J+"grid-row-"+z(t,/flex-|-self/g,""))+t;case 4675:return _+t+J+"flex-line-pack"+z(t,/align-content|flex-|-self/g,"")+t;case 5548:return _+t+J+z(t,"shrink","negative")+t;case 5292:return _+t+J+z(t,"basis","preferred-size")+t;case 6060:return _+"box-"+z(t,"-grow","")+_+t+J+z(t,"grow","positive")+t;case 4554:return _+z(t,/([^-])(transform)/g,"$1"+_+"$2")+t;case 6187:return z(z(z(t,/(zoom-|grab)/,_+"$1"),/(image-set)/,_+"$1"),t,"")+t;case 5495:case 3959:return z(t,/(image-set\([^]*)/,_+"$1$`$1");case 4968:return z(z(t,/(.+:)(flex-)?(.*)/,_+"box-pack:$3"+J+"flex-pack:$3"),/space-between/,"justify")+_+t+t;case 4200:if(!Dt(t,/flex-|baseline/))return J+"grid-column-align"+Tn(t,e)+t;break;case 2592:case 3360:return J+z(t,"template-","")+t;case 4384:case 3616:return n&&n.some(function(r,i){return e=i,Dt(r.props,/grid-\w+-end/)})?~as(t+(n=n[e].value),"span",0)?t:J+z(t,"-start","")+t+J+"grid-row-span:"+(~as(n,"span",0)?Dt(n,/\d+/):+Dt(n,/\d+/)-+Dt(t,/\d+/))+";":J+z(t,"-start","")+t;case 4896:case 4128:return n&&n.some(function(r){return Dt(r.props,/grid-\w+-start/)})?t:J+z(z(t,"-end","-span"),"span ","")+t;case 4095:case 3583:case 4068:case 2532:return z(t,/(.+)-inline(.+)/,_+"$1$2")+t;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(at(t)-1-e>6)switch(de(t,e+1)){case 109:if(de(t,e+4)!==45)break;case 102:return z(t,/(.+:)(.+)-([^]+)/,"$1"+_+"$2-$3$1"+ki+(de(t,e+3)==108?"$3":"$2-$3"))+t;case 115:return~as(t,"stretch",0)?pf(z(t,"stretch","fill-available"),e,n)+t:t}break;case 5152:case 5920:return z(t,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(r,i,o,s,l,c,d){return J+i+":"+o+d+(s?J+i+"-span:"+(l?c:+c-+o)+d:"")+t});case 4949:if(de(t,e+6)===121)return z(t,":",":"+_)+t;break;case 6444:switch(de(t,de(t,14)===45?18:11)){case 120:return z(t,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,"$1"+_+(de(t,14)===45?"inline-":"")+"box$3$1"+_+"$2$3$1"+J+"$2box$3")+t;case 100:return z(t,":",":"+J)+t}break;case 5719:case 2647:case 2135:case 3927:case 2391:return z(t,"scroll-","scroll-snap-")+t}return t}function ps(t,e){for(var n="",r=0;r<t.length;r++)n+=e(t[r],r,t,e)||"";return n}function Ry(t,e,n,r){switch(t.type){case vy:if(t.children.length)break;case yy:case wy:case dc:return t.return=t.return||t.value;case rf:return"";case of:return t.return=t.value+"{"+ps(t.children,r)+"}";case ss:if(!at(t.value=t.props.join(",")))return""}return at(n=ps(t.children,r))?t.return=t.value+"{"+n+"}":""}function Iy(t){var e=lf(t);return function(n,r,i,o){for(var s="",l=0;l<e;l++)s+=t[l](n,r,i,o)||"";return s}}function Oy(t){return function(e){e.root||(e=e.return)&&t(e)}}function Ly(t,e,n,r){if(t.length>-1&&!t.return)switch(t.type){case dc:t.return=pf(t.value,t.length,n);return;case of:return ps([rn(t,{value:z(t.value,"@","@"+_)})],r);case ss:if(t.length)return Sy(n=t.props,function(i){switch(Dt(i,r=/(::plac\w+|:read-\w+)/)){case":read-only":case":read-write":xr(rn(t,{props:[z(i,/:(read-\w+)/,":"+ki+"$1")]})),xr(rn(t,{props:[i]})),hc(t,{props:cf(n,r)});break;case"::placeholder":xr(rn(t,{props:[z(i,/:(plac\w+)/,":"+_+"input-$1")]})),xr(rn(t,{props:[z(i,/:(plac\w+)/,":"+ki+"$1")]})),xr(rn(t,{props:[z(i,/:(plac\w+)/,J+"input-$1")]})),xr(rn(t,{props:[i]})),hc(t,{props:cf(n,r)});break}return""})}}var zy={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},He={},yr=typeof process<"u"&&He!==void 0&&(He.REACT_APP_SC_ATTR||He.SC_ATTR)||"data-styled",ff="active",gf="data-styled-version",fs="6.3.8",gc=`/*!sc*/
`,gs=typeof window<"u"&&typeof document<"u",wr=ie.createContext===void 0,By=!!(typeof SC_DISABLE_SPEEDY=="boolean"?SC_DISABLE_SPEEDY:typeof process<"u"&&He!==void 0&&He.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&He.REACT_APP_SC_DISABLE_SPEEDY!==""?He.REACT_APP_SC_DISABLE_SPEEDY!=="false"&&He.REACT_APP_SC_DISABLE_SPEEDY:typeof process<"u"&&He!==void 0&&He.SC_DISABLE_SPEEDY!==void 0&&He.SC_DISABLE_SPEEDY!==""&&He.SC_DISABLE_SPEEDY!=="false"&&He.SC_DISABLE_SPEEDY),ms=Object.freeze([]),vr=Object.freeze({});function Ny(t,e,n){return n===void 0&&(n=vr),t.theme!==n.theme&&t.theme||e||n.theme}var mf=new Set(["a","abbr","address","area","article","aside","audio","b","bdi","bdo","blockquote","body","button","br","canvas","caption","cite","code","col","colgroup","data","datalist","dd","del","details","dfn","dialog","div","dl","dt","em","embed","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","html","i","iframe","img","input","ins","kbd","label","legend","li","main","map","mark","menu","meter","nav","object","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","search","section","select","slot","small","span","strong","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","u","ul","var","video","wbr","circle","clipPath","defs","ellipse","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feDropShadow","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feImage","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence","filter","foreignObject","g","image","line","linearGradient","marker","mask","path","pattern","polygon","polyline","radialGradient","rect","stop","svg","switch","symbol","text","textPath","tspan","use"]),Fy=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,_y=/(^-|-$)/g;function xf(t){return t.replace(Fy,"-").replace(_y,"")}var Hy=/(a)(d)/gi,yf=function(t){return String.fromCharCode(t+(t>25?39:97))};function mc(t){var e,n="";for(e=Math.abs(t);e>52;e=e/52|0)n=yf(e%52)+n;return(yf(e%52)+n).replace(Hy,"$1-$2")}var xc,br=function(t,e){for(var n=e.length;n;)t=33*t^e.charCodeAt(--n);return t},wf=function(t){return br(5381,t)};function Wy(t){return mc(wf(t)>>>0)}function Uy(t){return t.displayName||t.name||"Component"}function yc(t){return typeof t=="string"&&!0}var vf=typeof Symbol=="function"&&Symbol.for,bf=vf?Symbol.for("react.memo"):60115,Gy=vf?Symbol.for("react.forward_ref"):60112,Vy={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},Yy={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},Sf={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},Ky=((xc={})[Gy]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},xc[bf]=Sf,xc);function kf(t){return("type"in(e=t)&&e.type.$$typeof)===bf?Sf:"$$typeof"in t?Ky[t.$$typeof]:Vy;var e}var Qy=Object.defineProperty,Xy=Object.getOwnPropertyNames,$f=Object.getOwnPropertySymbols,Jy=Object.getOwnPropertyDescriptor,qy=Object.getPrototypeOf,Cf=Object.prototype;function Tf(t,e,n){if(typeof e!="string"){if(Cf){var r=qy(e);r&&r!==Cf&&Tf(t,r,n)}var i=Xy(e);$f&&(i=i.concat($f(e)));for(var o=kf(t),s=kf(e),l=0;l<i.length;++l){var c=i[l];if(!(c in Yy||n&&n[c]||s&&c in s||o&&c in o)){var d=Jy(e,c);try{Qy(t,c,d)}catch{}}}}return t}function Sr(t){return typeof t=="function"}function wc(t){return typeof t=="object"&&"styledComponentId"in t}function Mn(t,e){return t&&e?"".concat(t," ").concat(e):t||e||""}function Mf(t,e){if(t.length===0)return"";for(var n=t[0],r=1;r<t.length;r++)n+=t[r];return n}function Ti(t){return t!==null&&typeof t=="object"&&t.constructor.name===Object.name&&!("props"in t&&t.$$typeof)}function vc(t,e,n){if(n===void 0&&(n=!1),!n&&!Ti(t)&&!Array.isArray(t))return e;if(Array.isArray(e))for(var r=0;r<e.length;r++)t[r]=vc(t[r],e[r]);else if(Ti(e))for(var r in e)t[r]=vc(t[r],e[r]);return t}function bc(t,e){Object.defineProperty(t,"toString",{value:e})}function Mi(t){for(var e=[],n=1;n<arguments.length;n++)e[n-1]=arguments[n];return new Error("An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#".concat(t," for more information.").concat(e.length>0?" Args: ".concat(e.join(", ")):""))}var Zy=function(){function t(e){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=e}return t.prototype.indexOfGroup=function(e){for(var n=0,r=0;r<e;r++)n+=this.groupSizes[r];return n},t.prototype.insertRules=function(e,n){if(e>=this.groupSizes.length){for(var r=this.groupSizes,i=r.length,o=i;e>=o;)if((o<<=1)<0)throw Mi(16,"".concat(e));this.groupSizes=new Uint32Array(o),this.groupSizes.set(r),this.length=o;for(var s=i;s<o;s++)this.groupSizes[s]=0}for(var l=this.indexOfGroup(e+1),c=(s=0,n.length);s<c;s++)this.tag.insertRule(l,n[s])&&(this.groupSizes[e]++,l++)},t.prototype.clearGroup=function(e){if(e<this.length){var n=this.groupSizes[e],r=this.indexOfGroup(e),i=r+n;this.groupSizes[e]=0;for(var o=r;o<i;o++)this.tag.deleteRule(r)}},t.prototype.getGroup=function(e){var n="";if(e>=this.length||this.groupSizes[e]===0)return n;for(var r=this.groupSizes[e],i=this.indexOfGroup(e),o=i+r,s=i;s<o;s++)n+="".concat(this.tag.getRule(s)).concat(gc);return n},t}(),xs=new Map,ys=new Map,ws=1,ji=function(t){if(xs.has(t))return xs.get(t);for(;ys.has(ws);)ws++;var e=ws++;return xs.set(t,e),ys.set(e,t),e},tw=function(t,e){ws=e+1,xs.set(t,e),ys.set(e,t)},nw="style[".concat(yr,"][").concat(gf,'="').concat(fs,'"]'),rw=new RegExp("^".concat(yr,'\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)')),iw=function(t,e,n){for(var r,i=n.split(","),o=0,s=i.length;o<s;o++)(r=i[o])&&t.registerName(e,r)},ow=function(t,e){for(var n,r=((n=e.textContent)!==null&&n!==void 0?n:"").split(gc),i=[],o=0,s=r.length;o<s;o++){var l=r[o].trim();if(l){var c=l.match(rw);if(c){var d=0|parseInt(c[1],10),h=c[2];d!==0&&(tw(h,d),iw(t,h,c[3]),t.getTag().insertRules(d,i)),i.length=0}else i.push(l)}}},jf=function(t){for(var e=document.querySelectorAll(nw),n=0,r=e.length;n<r;n++){var i=e[n];i&&i.getAttribute(yr)!==ff&&(ow(t,i),i.parentNode&&i.parentNode.removeChild(i))}};function sw(){return typeof __webpack_nonce__<"u"?__webpack_nonce__:null}var Ef=function(t){var e=document.head,n=t||e,r=document.createElement("style"),i=function(l){var c=Array.from(l.querySelectorAll("style[".concat(yr,"]")));return c[c.length-1]}(n),o=i!==void 0?i.nextSibling:null;r.setAttribute(yr,ff),r.setAttribute(gf,fs);var s=sw();return s&&r.setAttribute("nonce",s),n.insertBefore(r,o),r},aw=function(){function t(e){this.element=Ef(e),this.element.appendChild(document.createTextNode("")),this.sheet=function(n){if(n.sheet)return n.sheet;for(var r=document.styleSheets,i=0,o=r.length;i<o;i++){var s=r[i];if(s.ownerNode===n)return s}throw Mi(17)}(this.element),this.length=0}return t.prototype.insertRule=function(e,n){try{return this.sheet.insertRule(n,e),this.length++,!0}catch{return!1}},t.prototype.deleteRule=function(e){this.sheet.deleteRule(e),this.length--},t.prototype.getRule=function(e){var n=this.sheet.cssRules[e];return n&&n.cssText?n.cssText:""},t}(),lw=function(){function t(e){this.element=Ef(e),this.nodes=this.element.childNodes,this.length=0}return t.prototype.insertRule=function(e,n){if(e<=this.length&&e>=0){var r=document.createTextNode(n);return this.element.insertBefore(r,this.nodes[e]||null),this.length++,!0}return!1},t.prototype.deleteRule=function(e){this.element.removeChild(this.nodes[e]),this.length--},t.prototype.getRule=function(e){return e<this.length?this.nodes[e].textContent:""},t}(),cw=function(){function t(e){this.rules=[],this.length=0}return t.prototype.insertRule=function(e,n){return e<=this.length&&(this.rules.splice(e,0,n),this.length++,!0)},t.prototype.deleteRule=function(e){this.rules.splice(e,1),this.length--},t.prototype.getRule=function(e){return e<this.length?this.rules[e]:""},t}(),Df=gs,dw={isServer:!gs,useCSSOMInjection:!By},Pf=function(){function t(e,n,r){e===void 0&&(e=vr),n===void 0&&(n={});var i=this;this.options=Ae(Ae({},dw),e),this.gs=n,this.names=new Map(r),this.server=!!e.isServer,!this.server&&gs&&Df&&(Df=!1,jf(this)),bc(this,function(){return function(o){for(var s=o.getTag(),l=s.length,c="",d=function(p){var f=function(w){return ys.get(w)}(p);if(f===void 0)return"continue";var v=o.names.get(f),x=s.getGroup(p);if(v===void 0||!v.size||x.length===0)return"continue";var b="".concat(yr,".g").concat(p,'[id="').concat(f,'"]'),M="";v!==void 0&&v.forEach(function(w){w.length>0&&(M+="".concat(w,","))}),c+="".concat(x).concat(b,'{content:"').concat(M,'"}').concat(gc)},h=0;h<l;h++)d(h);return c}(i)})}return t.registerId=function(e){return ji(e)},t.prototype.rehydrate=function(){!this.server&&gs&&jf(this)},t.prototype.reconstructWithOptions=function(e,n){return n===void 0&&(n=!0),new t(Ae(Ae({},this.options),e),this.gs,n&&this.names||void 0)},t.prototype.allocateGSInstance=function(e){return this.gs[e]=(this.gs[e]||0)+1},t.prototype.getTag=function(){return this.tag||(this.tag=(e=function(n){var r=n.useCSSOMInjection,i=n.target;return n.isServer?new cw(i):r?new aw(i):new lw(i)}(this.options),new Zy(e)));var e},t.prototype.hasNameForId=function(e,n){return this.names.has(e)&&this.names.get(e).has(n)},t.prototype.registerName=function(e,n){if(ji(e),this.names.has(e))this.names.get(e).add(n);else{var r=new Set;r.add(n),this.names.set(e,r)}},t.prototype.insertRules=function(e,n,r){this.registerName(e,n),this.getTag().insertRules(ji(e),r)},t.prototype.clearNames=function(e){this.names.has(e)&&this.names.get(e).clear()},t.prototype.clearRules=function(e){this.getTag().clearGroup(ji(e)),this.clearNames(e)},t.prototype.clearTag=function(){this.tag=void 0},t}(),uw=/&/g,kr=47;function Af(t){if(t.indexOf("}")===-1)return!1;for(var e=t.length,n=0,r=0,i=!1,o=0;o<e;o++){var s=t.charCodeAt(o);if(r!==0||i||s!==kr||t.charCodeAt(o+1)!==42)if(i)s===42&&t.charCodeAt(o+1)===kr&&(i=!1,o++);else if(s!==34&&s!==39||o!==0&&t.charCodeAt(o-1)===92){if(r===0){if(s===123)n++;else if(s===125&&--n<0)return!0}}else r===0?r=s:r===s&&(r=0);else i=!0,o++}return n!==0||r!==0}function Rf(t,e){return t.map(function(n){return n.type==="rule"&&(n.value="".concat(e," ").concat(n.value),n.value=n.value.replaceAll(",",",".concat(e," ")),n.props=n.props.map(function(r){return"".concat(e," ").concat(r)})),Array.isArray(n.children)&&n.type!=="@keyframes"&&(n.children=Rf(n.children,e)),n})}function hw(t){var e,n,r,i=vr,o=i.options,s=o===void 0?vr:o,l=i.plugins,c=l===void 0?ms:l,d=function(f,v,x){return x.startsWith(n)&&x.endsWith(n)&&x.replaceAll(n,"").length>0?".".concat(e):f},h=c.slice();h.push(function(f){f.type===ss&&f.value.includes("&")&&(f.props[0]=f.props[0].replace(uw,n).replace(r,d))}),s.prefix&&h.push(Ly),h.push(Ry);var p=function(f,v,x,b){v===void 0&&(v=""),x===void 0&&(x=""),b===void 0&&(b="&"),e=b,n=v,r=new RegExp("\\".concat(n,"\\b"),"g");var M=function(y){if(!Af(y))return y;for(var S=y.length,T="",C=0,k=0,$=0,D=!1,E=0;E<S;E++){var H=y.charCodeAt(E);if($!==0||D||H!==kr||y.charCodeAt(E+1)!==42)if(D)H===42&&y.charCodeAt(E+1)===kr&&(D=!1,E++);else if(H!==34&&H!==39||E!==0&&y.charCodeAt(E-1)===92){if($===0)if(H===123)k++;else if(H===125){if(--k<0){for(var K=E+1;K<S;){var he=y.charCodeAt(K);if(he===59||he===10)break;K++}K<S&&y.charCodeAt(K)===59&&K++,k=0,E=K-1,C=K;continue}k===0&&(T+=y.substring(C,E+1),C=E+1)}else H===59&&k===0&&(T+=y.substring(C,E+1),C=E+1)}else $===0?$=H:$===H&&($=0);else D=!0,E++}if(C<S){var ut=y.substring(C);Af(ut)||(T+=ut)}return T}(function(y){if(y.indexOf("//")===-1)return y;for(var S=y.length,T=[],C=0,k=0,$=0,D=0;k<S;){var E=y.charCodeAt(k);if(E!==34&&E!==39||k!==0&&y.charCodeAt(k-1)===92)if($===0)if(E===40&&k>=3&&(32|y.charCodeAt(k-1))==108&&(32|y.charCodeAt(k-2))==114&&(32|y.charCodeAt(k-3))==117)D=1,k++;else if(D>0)E===41?D--:E===40&&D++,k++;else if(E===kr&&k+1<S&&y.charCodeAt(k+1)===kr){for(k>C&&T.push(y.substring(C,k));k<S&&y.charCodeAt(k)!==10;)k++;C=k}else k++;else k++;else $===0?$=E:$===E&&($=0),k++}return C===0?y:(C<S&&T.push(y.substring(C)),T.join(""))}(f)),w=Py(x||v?"".concat(x," ").concat(v," { ").concat(M," }"):M);s.namespace&&(w=Rf(w,s.namespace));var m=[];return ps(w,Iy(h.concat(Oy(function(y){return m.push(y)})))),m};return p.hash=c.length?c.reduce(function(f,v){return v.name||Mi(15),br(f,v.name)},5381).toString():"",p}var pw=new Pf,Sc=hw(),kc={shouldForwardProp:void 0,styleSheet:pw,stylis:Sc},If=wr?{Provider:function(t){return t.children},Consumer:function(t){return(0,t.children)(kc)}}:ie.createContext(kc);If.Consumer,wr||ie.createContext(void 0);function Of(){return wr?kc:ie.useContext(If)}var fw=function(){function t(e,n){var r=this;this.inject=function(i,o){o===void 0&&(o=Sc);var s=r.name+o.hash;i.hasNameForId(r.id,s)||i.insertRules(r.id,s,o(r.rules,s,"@keyframes"))},this.name=e,this.id="sc-keyframes-".concat(e),this.rules=n,bc(this,function(){throw Mi(12,String(r.name))})}return t.prototype.getName=function(e){return e===void 0&&(e=Sc),this.name+e.hash},t}();function gw(t,e){return e==null||typeof e=="boolean"||e===""?"":typeof e!="number"||e===0||t in zy||t.startsWith("--")?String(e).trim():"".concat(e,"px")}var mw=function(t){return t>="A"&&t<="Z"};function Lf(t){for(var e="",n=0;n<t.length;n++){var r=t[n];if(n===1&&r==="-"&&t[0]==="-")return t;mw(r)?e+="-"+r.toLowerCase():e+=r}return e.startsWith("ms-")?"-"+e:e}var zf=function(t){return t==null||t===!1||t===""},Bf=function(t){var e=[];for(var n in t){var r=t[n];t.hasOwnProperty(n)&&!zf(r)&&(Array.isArray(r)&&r.isCss||Sr(r)?e.push("".concat(Lf(n),":"),r,";"):Ti(r)?e.push.apply(e,os(os(["".concat(n," {")],Bf(r),!1),["}"],!1)):e.push("".concat(Lf(n),": ").concat(gw(n,r),";")))}return e};function jn(t,e,n,r){if(zf(t))return[];if(wc(t))return[".".concat(t.styledComponentId)];if(Sr(t)){if(!Sr(o=t)||o.prototype&&o.prototype.isReactComponent||!e)return[t];var i=t(e);return jn(i,e,n,r)}var o;return t instanceof fw?n?(t.inject(n,r),[t.getName(r)]):[t]:Ti(t)?Bf(t):Array.isArray(t)?Array.prototype.concat.apply(ms,t.map(function(s){return jn(s,e,n,r)})):[t.toString()]}function xw(t){for(var e=0;e<t.length;e+=1){var n=t[e];if(Sr(n)&&!wc(n))return!1}return!0}var yw=wf(fs),ww=function(){function t(e,n,r){this.rules=e,this.staticRulesId="",this.isStatic=(r===void 0||r.isStatic)&&xw(e),this.componentId=n,this.baseHash=br(yw,n),this.baseStyle=r,Pf.registerId(n)}return t.prototype.generateAndInjectStyles=function(e,n,r){var i=this.baseStyle?this.baseStyle.generateAndInjectStyles(e,n,r).className:"";if(this.isStatic&&!r.hash)if(this.staticRulesId&&n.hasNameForId(this.componentId,this.staticRulesId))i=Mn(i,this.staticRulesId);else{var o=Mf(jn(this.rules,e,n,r)),s=mc(br(this.baseHash,o)>>>0);if(!n.hasNameForId(this.componentId,s)){var l=r(o,".".concat(s),void 0,this.componentId);n.insertRules(this.componentId,s,l)}i=Mn(i,s),this.staticRulesId=s}else{for(var c=br(this.baseHash,r.hash),d="",h=0;h<this.rules.length;h++){var p=this.rules[h];if(typeof p=="string")d+=p;else if(p){var f=Mf(jn(p,e,n,r));c=br(c,f+h),d+=f}}if(d){var v=mc(c>>>0);if(!n.hasNameForId(this.componentId,v)){var x=r(d,".".concat(v),void 0,this.componentId);n.insertRules(this.componentId,v,x)}i=Mn(i,v)}}return{className:i,css:typeof window>"u"?n.getTag().getGroup(ji(this.componentId)):""}},t}(),Nf=wr?{Provider:function(t){return t.children},Consumer:function(t){return(0,t.children)(void 0)}}:ie.createContext(void 0);Nf.Consumer;var $c={};function vw(t,e,n){var r=wc(t),i=t,o=!yc(t),s=e.attrs,l=s===void 0?ms:s,c=e.componentId,d=c===void 0?function(S,T){var C=typeof S!="string"?"sc":xf(S);$c[C]=($c[C]||0)+1;var k="".concat(C,"-").concat(Wy(fs+C+$c[C]));return T?"".concat(T,"-").concat(k):k}(e.displayName,e.parentComponentId):c,h=e.displayName,p=h===void 0?function(S){return yc(S)?"styled.".concat(S):"Styled(".concat(Uy(S),")")}(t):h,f=e.displayName&&e.componentId?"".concat(xf(e.displayName),"-").concat(e.componentId):e.componentId||d,v=r&&i.attrs?i.attrs.concat(l).filter(Boolean):l,x=e.shouldForwardProp;if(r&&i.shouldForwardProp){var b=i.shouldForwardProp;if(e.shouldForwardProp){var M=e.shouldForwardProp;x=function(S,T){return b(S,T)&&M(S,T)}}else x=b}var w=new ww(n,f,r?i.componentStyle:void 0);function m(S,T){return function(C,k,$){var D=C.attrs,E=C.componentStyle,H=C.defaultProps,K=C.foldedComponentIds,he=C.styledComponentId,ut=C.target,Rd=wr?void 0:ie.useContext(Nf),Fi=Of(),zn=C.shouldForwardProp||Fi.shouldForwardProp,A=Ny(k,Rd,H)||vr,O=function(Qs,Ar,Xs){for(var _i,Lt=Ae(Ae({},Ar),{className:void 0,theme:Xs}),Id=0;Id<Qs.length;Id+=1){var Js=Sr(_i=Qs[Id])?_i(Lt):_i;for(var Rr in Js)Rr==="className"?Lt.className=Mn(Lt.className,Js[Rr]):Rr==="style"?Lt.style=Ae(Ae({},Lt.style),Js[Rr]):Lt[Rr]=Js[Rr]}return"className"in Ar&&typeof Ar.className=="string"&&(Lt.className=Mn(Lt.className,Ar.className)),Lt}(D,k,A),L=O.as||ut,G={};for(var V in O)O[V]===void 0||V[0]==="$"||V==="as"||V==="theme"&&O.theme===A||(V==="forwardedAs"?G.as=O.forwardedAs:zn&&!zn(V,L)||(G[V]=O[V]));var Bn=function(Qs,Ar){var Xs=Of(),_i=Qs.generateAndInjectStyles(Ar,Xs.styleSheet,Xs.stylis);return _i}(E,O),ht=Bn.className,Pr=Bn.css,pt=Mn(K,he);ht&&(pt+=" "+ht),O.className&&(pt+=" "+O.className),G[yc(L)&&!mf.has(L)?"class":"className"]=pt,$&&(G.ref=$);var Nn=I.createElement(L,G);return wr&&Pr?ie.createElement(ie.Fragment,null,ie.createElement("style",{precedence:"styled-components",href:"sc-".concat(he,"-").concat(ht),children:Pr}),Nn):Nn}(y,S,T)}m.displayName=p;var y=ie.forwardRef(m);return y.attrs=v,y.componentStyle=w,y.displayName=p,y.shouldForwardProp=x,y.foldedComponentIds=r?Mn(i.foldedComponentIds,i.styledComponentId):"",y.styledComponentId=f,y.target=r?i.target:t,Object.defineProperty(y,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(S){this._foldedDefaultProps=r?function(T){for(var C=[],k=1;k<arguments.length;k++)C[k-1]=arguments[k];for(var $=0,D=C;$<D.length;$++)vc(T,D[$],!0);return T}({},i.defaultProps,S):S}}),bc(y,function(){return".".concat(y.styledComponentId)}),o&&Tf(y,t,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),y}function Ff(t,e){for(var n=[t[0]],r=0,i=e.length;r<i;r+=1)n.push(e[r],t[r+1]);return n}var _f=function(t){return Object.assign(t,{isCss:!0})};function $r(t){for(var e=[],n=1;n<arguments.length;n++)e[n-1]=arguments[n];if(Sr(t)||Ti(t))return _f(jn(Ff(ms,os([t],e,!0))));var r=t;return e.length===0&&r.length===1&&typeof r[0]=="string"?jn(r):_f(jn(Ff(r,e)))}function Cc(t,e,n){if(n===void 0&&(n=vr),!e)throw Mi(1,e);var r=function(i){for(var o=[],s=1;s<arguments.length;s++)o[s-1]=arguments[s];return t(e,n,$r.apply(void 0,os([i],o,!1)))};return r.attrs=function(i){return Cc(t,e,Ae(Ae({},n),{attrs:Array.prototype.concat(n.attrs,i).filter(Boolean)}))},r.withConfig=function(i){return Cc(t,e,Ae(Ae({},n),i))},r}var Hf=function(t){return Cc(vw,t)},u=Hf;mf.forEach(function(t){u[t]=Hf(t)});const g={colors:{windowBg:"#152029de",panelBg:"#04161c",panelBgGlass:"rgba(4, 22, 28, 0.22)",line:"#496791",text:"#ffffff",textAccent:"#C6E2FF",textDim:"#7f9bb8",healthOk:"#427231",healthCrit:"#ed6738",warning:"#e1b000",warningSoft:"#e6b400",statusOk:"limegreen",statusAlert:"#e1b000",statusBad:"red",statusPending:"#00b8e6",enhText:"#d8be86",enhTitle:"#e8cf93",enhBg:"rgba(169, 128, 56, 0.30)",enhLine:"#8a6d3b",greenBg:"rgba(8, 28, 12, 0.92)",greenLine:"#3f8a3f",greenTitleBg:"#16401b",greenText:"#e6ffe6",greenLabel:"#bdf0bd",greenBtnBg:"#1b5e20",greenBtnLine:"#2e7d32",greenBtnLineHover:"#66bb6a",greenGlow:"#4caf50",custom:"#cccc00",chromeText:"#deebff",overlayBg:"black",overlayBgSoft:"rgba(0, 0, 0, 0.65)"},fonts:{body:"arial",mono:'Consolas, "Lucida Console", monospace',display:'"Orbitron", sans-serif'},radii:{modal:"0px",tooltip:"7px"},hud:{btn:"36px",btnSmall:"25px",gap:"10px",gapSmall:"5px",icon:"26px",iconSmall:"17px",glyph:"30px",glyphSmall:"20px"}},bw=u.div`
    border: 1px solid ${g.colors.line};
    color: ${g.colors.chromeText};
    background-color: ${g.colors.windowBg};
    font-family:${g.fonts.body};
`,Sw=u.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,Ei=u(bw)`
    box-shadow: 5px 5px 10px ${g.colors.overlayBg};
`,kw=u.span`
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
`;u(kw)`
    font-weight: normal;
`;const We=$r`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;class Ue extends I.Component{render(){return a.jsxs($w,{children:[a.jsx(Cw,{children:this.props.label}),a.jsx(Tw,{type:this.props.type||"text",value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:"0"})]})}}const $w=u.div`
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
`,Cw=u.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,Tw=u.input`
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
`;class Mw extends I.Component{render(){const{children:e,className:n}=this.props;return a.jsx("div",{className:n,children:e})}}const Wf=u(Mw)`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${g.fonts.body};
    font-size:12px;
    color:${g.colors.text};
    background-color:${g.colors.overlayBgSoft};
    border-radius: ${g.radii.tooltip};
    -moz-border-radius: ${g.radii.tooltip};
    -webkit-border-radius: ${g.radii.tooltip};
    padding:3px 3px 3px 3px;
    padding-bottom: 8px;
`,Uf=u.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,Gf=u.div`
    color: ${t=>t.$type=="good"?"#6fc126;":t.$type=="bad"?"#ff7b3f;":"white;"}
    font-weight: ${t=>t.$important?"bold":"inherit"};
    font-size: ${t=>t.$important?"14px":"12px"};
    margin-top: ${t=>t.$space?"14px":"0"};
`;class jw extends I.Component{getOnChange(e){return n=>{this.props.set(e,n.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(e){return n=>{if(console.log("keydown"),n.preventDefault(),n.stopPropagation(),!Vf[n.keyCode])return;const r={keyCode:n.keyCode,shiftKey:n.shiftKey,altKey:n.altKey,ctrlKey:n.ctrlKey,metaKey:n.metaKey};this.props.set(e,r),this.props.save(),this.forceUpdate()}}getKey(e){return console.log(this.props.settings),Ew(this.props.settings[e])}get(e){return this.props.settings[e]}render(){return a.jsx(Dw,{onClick:this.props.close,children:a.jsxs(Pw,{onClick:e=>e.stopPropagation(),children:[a.jsxs(Aw,{children:[a.jsx(Rw,{children:"Player Settings"}),a.jsx(Iw,{onClick:this.props.close,title:"Close",children:"✕"})]}),a.jsxs(Ow,{children:[a.jsx(Lw,{children:"Settings apply to this browser and device only. Reload the page for changes to take effect."}),a.jsx(vs,{children:"Keys"}),a.jsx(Ue,{label:"Display ALL Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllEW"),value:this.getKey.call(this,"ShowAllEW")}),a.jsx(Ue,{label:"Display FRIENDLY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyEW"),value:this.getKey.call(this,"ShowFriendlyEW")}),a.jsx(Ue,{label:"Display ENEMY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyEW"),value:this.getKey.call(this,"ShowEnemyEW")}),a.jsx(Ue,{label:"Display ALL Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllBallistics"),value:this.getKey.call(this,"ShowAllBallistics")}),a.jsx(Ue,{label:"Display FRIENDLY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyBallistics"),value:this.getKey.call(this,"ShowFriendlyBallistics")}),a.jsx(Ue,{label:"Display ENEMY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyBallistics"),value:this.getKey.call(this,"ShowEnemyBallistics")}),a.jsx(Ue,{label:"Toggle RULER tool",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleLoS"),value:this.getKey.call(this,"ToggleLoS")}),a.jsx(Ue,{label:"Toggle HEX numbers",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleHexNumbers"),value:this.getKey.call(this,"ToggleHexNumbers")}),a.jsx(Ue,{label:"Toggle MAP background",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleBackground"),value:this.getKey.call(this,"ToggleBackground")}),a.jsx(Ue,{label:"Close all SHIP WINDOWS and tooltips",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"CloseAllWindows"),value:this.getKey.call(this,"CloseAllWindows")}),a.jsx(vs,{children:"Replay"}),a.jsx(Ue,{label:"Play / pause Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"TogglePlayPause"),value:this.getKey.call(this,"TogglePlayPause")}),a.jsx(vs,{children:"Sound"}),a.jsx(Ue,{label:"Toggle sound in Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleSound"),value:this.getKey.call(this,"ToggleSound")}),a.jsx(vs,{children:"Visual"}),a.jsx(Ue,{placeholder:"0",type:"number",label:"Zoom level to switch to strategic view",onChange:this.getOnChange.call(this,"ZoomLevelToStrategic"),value:this.get.call(this,"ZoomLevelToStrategic")}),a.jsx(zw,{children:"Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners."})]})]})})}}const Ew=t=>{let e=Vf[t.keyCode];return e=e.toUpperCase(),t.shiftKey&&(e+=" + shift"),t.altKey&&(e+=" + alt"),t.ctrlKey&&(e+=" + ctrl"),t.metaKey&&(e+=" + cmd"),e},Dw=u(Sw)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,Pw=u.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: ${g.colors.panelBg};
    border: 1px solid ${g.colors.line};
    border-radius: ${g.radii.modal};
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: ${g.colors.chromeText};
    font-family: ${g.fonts.body};
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`,Aw=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${g.colors.windowBg};
    border-bottom: 1px solid ${g.colors.line};
`,Rw=u.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,Iw=u.div`
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
`,Ow=u.div`
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
`,Lw=u.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`,vs=u.div`
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
`,zw=u.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,Vf={27:"esc",32:"space",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",58:":",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z"};class Bw extends I.Component{constructor(e){super(e),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?a.jsx(jw,{close:this.close.bind(this),...this.props}):a.jsx(Nw,{onClick:this.open.bind(this),children:"⚙"})}}const Nw=u(Ei)`
    width: ${g.hud.btn};
    height: ${g.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${g.hud.glyph};
    border-right: none;
    border-top: none;
    ${We}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${g.hud.btnSmall};
        height: ${g.hud.btnSmall};
        font-size: ${g.hud.glyphSmall};

    }
`,Yf="fv-thrust-relayout",sn=8,bs=6,Di=40,Tc=60,Ss=80,Fw=4,_w=(t,e,n)=>{const r=window.coordinateConverter&&window.coordinateConverter.zoom||1,i=Math.min((t.canvasSize||200)*.75,250)/2/r+Fw,o=Math.min(i,Ss+Di),s=d=>n.type==="roll"||!Array.isArray(e)||e[d]!==null,l=d=>s(d)?shipManager.systems.getThrusters(t,d).length:0,c=Math.max(l(1),l(2))*Di/2;return{x:Math.min(i,Tc),y:Math.max(Math.min(i,Ss),c),clear:o}},Kf=u.span`
    font-family: ${g.fonts.mono};
    font-size: 11px;
    line-height: 1;
    padding: 1px 2px;
    color: ${t=>t.$over?g.colors.healthCrit:g.colors.text};
    background-color: rgba(0, 0, 0, 0.55);
    pointer-events: none;
`,Hw=u.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    position: relative; // Needed for absolute positioning of ::before
    box-sizing: border-box;
    ${t=>t.$destroyed?`box-shadow: inset 0 0 0 1px ${g.colors.statusBad};`:t.$box==="need"?`box-shadow: inset 0 0 0 1px ${g.colors.warning};`:t.$box==="extra"?`box-shadow: inset 0 0 0 1px ${g.colors.greenBtnLineHover};`:""}

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
`,Qf=u.div`
    display: flex;
    position: absolute;
`,Xf=u(Qf)`
    flex-direction: row;
    left: var(--fv-ring-x, ${Tc}px);
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: ${Di}px;
`,Ww=u(Xf)`
    left: calc(-1 * var(--fv-ring-x, ${Tc}px) - ${Di}px);
`,Jf=u(Qf)`
    flex-direction: row;
    top: calc(-1 * var(--fv-ring-y, ${Ss}px) - ${Di}px);
    transform: translate(-50%, 0);
`,Uw=u(Jf)`
    top: var(--fv-ring-y, ${Ss}px);
`,Gw=u.div`
    position: relative;
    transform: rotate(${t=>t.$rotation}deg);

    & ${Kf} {
        transform: rotate(${t=>-t.$rotation}deg);
    }
`,Vw=u.div`
    position: absolute;
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
`,Yw=u.div`
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 180px;
    box-sizing: border-box;
    background-color: ${g.colors.greenBg};
    border: 1px solid ${g.colors.greenLine};
    box-shadow: 5px 5px 10px ${g.colors.overlayBg};
    font-family: ${g.fonts.body};
    color: ${g.colors.greenText};
    text-align: left;
    user-select: none;
`,Kw=u.div`
    box-sizing: border-box;
    padding: 3px 6px;
    line-height: 1.2;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    white-space: pre;
    overflow: hidden;
    text-overflow: ellipsis;
    color: ${g.colors.greenText};
    background-color: ${g.colors.greenTitleBg};
    border-bottom: 1px solid ${g.colors.greenLine};
`,Qw=u.div`
    padding: 3px 6px 4px;
`,En=u.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px;
    padding: 1px 0;
`,Dn=u.span`
    font-size: 10px;
    color: ${g.colors.greenLabel};
    white-space: nowrap;
`,qf=()=>window.UI&&window.UI.shipMovement&&window.UI.shipMovement.extendedTurnColour||"#ff8c00",Pn=u.span`
    font-family: ${g.fonts.mono};
    font-size: 11px;
    white-space: nowrap;
    color: ${t=>t.$state==="ok"?g.colors.statusOk:t.$state==="open"?g.colors.warning:t.$state==="owed"?qf():g.colors.greenText};
`,Mc=u.div`
    padding: 2px 0 1px;
    font-size: 10px;
    line-height: 1.25;
    color: ${g.colors.greenLabel};
`,Xw=u.div`
    position: relative;
    height: 4px;
    margin: 2px 0 3px;
    background-color: rgba(0, 0, 0, 0.45);
    border: 1px solid ${g.colors.greenLine};
`,Zf=u.div`
    position: absolute;
    top: 0;
    bottom: 0;
    background-color: ${t=>t.$owed?qf():g.colors.statusOk};
`,Jw=u.div`
    position: absolute;
    top: -3px;
    bottom: -3px;
    width: 1px;
    background-color: ${g.colors.greenText};
`,qw=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 3px;
    min-height: 16px;
    font-size: 10px;
    color: ${g.colors.greenText};
    cursor: ${t=>t.$locked?"not-allowed":"pointer"};

    @media (pointer: coarse) {
        min-height: 32px;
    }
`,Zw=u.span`
    position: relative;
    flex: 0 0 auto;
    width: 10px;
    height: 10px;
    box-sizing: border-box;
    border: 1px solid ${t=>t.$locked?g.colors.greenLine:g.colors.greenBtnLineHover};
    background-color: ${t=>t.$checked?g.colors.greenBtnLine:"rgba(0, 0, 0, 0.45)"};

    ${t=>t.$checked&&`
    &::after {
        content: "";
        position: absolute;
        left: 2.5px;
        top: 0;
        width: 3px;
        height: 6px;
        border-right: 1.5px solid ${g.colors.greenText};
        border-bottom: 1.5px solid ${g.colors.greenText};
        transform: rotate(45deg);
    }`}
`,ev=u.div`
    height: 1px;
    margin: 3px 0;
    background-color: ${g.colors.greenLine};
`,tv=u.div`
    display: flex;
    gap: 4px;
    padding: 0 6px 6px;
`,eg=u.div`
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
    background: ${g.colors.greenBtnBg};
    border: 1px solid ${g.colors.greenBtnLine};
    color: ${g.colors.greenText};

    &:hover {
        background: ${g.colors.greenBtnLine};
        border: 1px solid ${g.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${t=>t.$primary&&!t.disabled&&`
        background: ${g.colors.greenBtnLine};
        border: 1px solid ${g.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${g.colors.greenGlow};
        color: #ffffff;
    `}

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${g.colors.greenBtnBg}; border: 1px solid ${g.colors.greenBtnLine}; color: ${g.colors.greenText}; }
    `}

    @media (pointer: coarse) {
        height: 32px;
    }
`,nv=["Either","Front","Aft","Port","Stbd"],rv=[1,2,3,4,0],Cr=t=>t?"Starboard":"Port",iv=t=>shipManager.movement.isExtendedTurnStart(t)?"begin":shipManager.movement.isExtendedTurnCompletion(t)?"complete":"normal",ov=(t,e)=>{switch(e.type){case"extendTurnLeft":case"extendTurnRight":return`Begin Extended Turn
`+Cr(e.type==="extendTurnRight");case"turnleft":case"turnright":return e.value==="turnIntoPivot"?"Turn into Pivot · "+Cr(e.type==="turnright"):e.value==="extendedTurn"?`Complete Extended Turn
`+Cr(e.type==="turnright"):"Turn "+Cr(e.type==="turnright");case"pivotleft":case"pivotright":return"Pivot "+Cr(e.type==="pivotright");case"slipleft":case"slipright":return"Slip "+Cr(e.type==="slipright");case"roll":return e.value==="emergencyRoll"?"Emergency Roll":"Roll";case"speedchange":{const n=t.movement.lastIndexOf(e),r=n>0?t.movement[n-1]:null;return r&&(e.speed<r.speed||e.heading!=r.heading)?"Decelerate":"Accelerate"}case"jink":return"Jink";case"halfPhase":return"Half-Phase";case"contract":return"Contraction";default:return"Assign Thrust"}},sv=(t,e)=>{if(!Array.isArray(t)||!Array.isArray(e))return{rows:[],extra:0};const n=[1,2,3,4].every(o=>!(t[o]>0)),r=[];rv.forEach(o=>{const s=t[o];if(!(s>0)||o>0&&e[o]===null)return;const l=Math.max(0,e[o]||0);r.push({slot:o,label:o===0?n?"Any":"Either":nv[o],paid:s-l,required:s,open:l})});const i=e[0]<0?-e[0]:0;return{rows:r,extra:i}},av=t=>Array.isArray(t)&&!t.some(e=>e>0),lv=(t,e,n)=>{const r={left:e.left-n,top:e.top-n,right:e.left+n,bottom:e.top+n};return Array.from(t.children).forEach(i=>{const o=i.getBoundingClientRect();o.width===0||o.height===0||(r.left=Math.min(r.left,o.left),r.top=Math.min(r.top,o.top),r.right=Math.max(r.right,o.right),r.bottom=Math.max(r.bottom,o.bottom))}),r};class cv extends I.Component{constructor(e){super(e),this.containerRef=I.createRef(),this.ringRef=I.createRef(),this.panelRef=I.createRef(),this.layout=this.layout.bind(this)}componentDidMount(){this.layout(),window.addEventListener("resize",this.layout),this.container=this.containerRef.current,this.container&&this.container.addEventListener(Yf,this.layout)}componentDidUpdate(){this.layout()}componentWillUnmount(){window.removeEventListener("resize",this.layout),this.container&&this.container.removeEventListener(Yf,this.layout)}layout(){const e=this.containerRef.current,n=this.ringRef.current,r=this.panelRef.current;if(!e||!n||!r)return;const{ship:i,totalRequired:o,movement:s}=this.props,l=_w(i,o,s);n.style.setProperty("--fv-ring-x",l.x+"px"),n.style.setProperty("--fv-ring-y",l.y+"px"),this.placePanel(e,n,r,l.clear)}placePanel(e,n,r,i){const o=e.getBoundingClientRect(),s=lv(n,o,i),l=window.innerWidth,c=window.innerHeight,d=r.offsetWidth,h=r.offsetHeight,p=w=>Math.min(Math.max(w,sn),l-d-sn),f=w=>Math.min(Math.max(w,sn),c-h-sn),v={left:p(o.left-d/2),top:s.bottom+bs},x=[v,{left:p(o.left-d/2),top:s.top-bs-h},{left:s.right+bs,top:f(o.top-h/2)},{left:s.left-bs-d,top:f(o.top-h/2)}],b=w=>w.left>=sn&&w.top>=sn&&w.left+d<=l-sn&&w.top+h<=c-sn,M=x.find(b)||{left:v.left,top:f(v.top)};r.style.left=Math.round(M.left-o.left)+"px",r.style.top=Math.round(M.top-o.top)+"px"}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}toggleExtendedTurn(e){e.enabled&&window.shipManager.movement.setExtendedTurn(this.props.ship,!e.checked)}render(){const{ship:e,position:n,rotation:r,totalRequired:i,remainginRequired:o,movement:s}=this.props,l=Math.round(Math.abs(r)),{rows:c,extra:d}=sv(i,o),h=iv(s),p=shipManager.movement.getExtendedTurnToggle(e,s);let f,v,x=null;h==="begin"?(x=shipManager.movement.getExtendedTurnStartPayment(e,s),f=x.valid,v=x.paid<x.minimum?`Pay at least ${x.minimum} now (${x.paid} so far)`:"Leave at least 1 thrust owed for next turn"):(f=av(o),v="Still needed: "+c.filter(M=>M.open>0).map(M=>`${M.label} ${M.open}`).join(" · "));const b=M=>M.open===0?"ok":h==="begin"?"owed":"open";return a.jsxs(Vw,{ref:this.containerRef,onMouseOver:M=>M.preventDefault(),onContextMenu:M=>M.preventDefault(),id:"thrustUIContainer",style:{left:`${n.x}px`,top:`${n.y}px`},children:[a.jsxs(Gw,{ref:this.ringRef,style:{transform:`rotate(${l}deg)`},$rotation:l,children:[a.jsx(Xf,{children:ks(e,1,i,o,s)}),a.jsx(Jf,{children:ks(e,3,i,o,s)}),a.jsx(Uw,{children:ks(e,4,i,o,s)}),a.jsx(Ww,{children:ks(e,2,i,o,s)})]}),a.jsxs(Yw,{ref:this.panelRef,children:[a.jsx(Kw,{children:ov(e,s)}),a.jsxs(Qw,{children:[c.map(M=>a.jsxs(En,{children:[a.jsx(Dn,{children:M.label}),a.jsxs(Pn,{$state:b(M),children:[M.paid,"/",M.required]})]},`thrust-row-${M.slot}`)),d>0&&a.jsxs(En,{children:[a.jsx(Dn,{children:"Extra thrust"}),a.jsxs(Pn,{$state:"ok",children:["+",d]})]}),a.jsx(ev,{}),x&&uv(x),a.jsxs(En,{children:[a.jsx(Dn,{children:"Engine thrust"}),a.jsx(Pn,{children:shipManager.movement.getRemainingEngineThrust(e)})]}),dv(e,s),h==="begin"&&a.jsx(Mc,{children:"Completes on next turn's movement"}),h==="complete"&&a.jsx(Mc,{children:"Begun last turn"}),p&&a.jsxs(a.Fragment,{children:[a.jsxs(qw,{$locked:!p.enabled,title:p.enabled?p.checked?"Make a normal turn instead":"Pay part of this turn now and the rest next turn":p.hint,onClick:()=>this.toggleExtendedTurn(p),children:[a.jsx(Zw,{$checked:p.checked,$locked:!p.enabled}),"Make Extended Turn"]}),!p.enabled&&p.hint&&a.jsx(Mc,{children:p.hint})]})]}),a.jsxs(tv,{children:[a.jsx(eg,{$primary:!0,disabled:!f,title:f?"Confirm this manoeuvre":v,onClick:f?this.ready.bind(this):void 0,children:"Confirm"}),a.jsx(eg,{onClick:this.cancel.bind(this),children:"Cancel"})]})]})]})}}const dv=(t,e)=>{if(!shipManager.movement.isTurn(e))return null;const n=shipManager.movement.calculateTurndelay(t,e,e.speed);return a.jsxs(En,{children:[a.jsx(Dn,{children:"Turn delay"}),a.jsx(Pn,{children:n})]})},uv=t=>{const e=t.paid>=t.minimum,n=t.owed.any+t.owed.rear+t.owed.side,r=i=>(t.cost>0?Math.min(100,Math.max(0,i/t.cost*100)):0)+"%";return a.jsxs(a.Fragment,{children:[a.jsxs(En,{children:[a.jsx(Dn,{children:"Turn cost"}),a.jsx(Pn,{children:t.cost})]}),a.jsxs(En,{children:[a.jsxs(Dn,{children:["Paying now (min ",t.minimum,")"]}),a.jsx(Pn,{$state:e?"ok":"open",children:t.paid})]}),a.jsxs(Xw,{title:`At least ${t.minimum} now, at most ${t.cost-1}`,children:[a.jsx(Zf,{style:{left:0,width:r(t.paid)}}),a.jsx(Zf,{$owed:!0,style:{left:r(t.paid),width:r(t.cost-t.paid)}}),a.jsx(Jw,{style:{left:r(t.minimum)}})]}),a.jsxs(En,{children:[a.jsx(Dn,{children:"Thrust owed next turn"}),a.jsx(Pn,{$state:"owed",children:n})]})]})},ks=(t,e,n,r,i)=>{const o=shipManager.systems.getThrusters(t,e);if(i.type!=="roll"&&n[e]===null)return null;const s=Array.isArray(r)&&(r[e]>0||r[0]>0);return o.map((l,c)=>{const d=shipManager.systems.isDestroyed(t,l),h=d||!shipManager.movement.wouldAcceptThrust(t,l)?void 0:s?"need":"extra",p=()=>{shipManager.movement.assignThrust(t,l),shipManager.movement.updateAssignThrust(t)},f=M=>{M.preventDefault(),shipManager.movement.unAssignThrust(t,l),shipManager.movement.updateAssignThrust(t)};let v=shipManager.criticals.hasCritical(l,"HalfEfficiency")?10:0;shipManager.criticals.hasCritical(l,"FirstThrustIgnored")&&(v+=1);const x=shipManager.movement.getAmountChanneled(t,l),b=shipManager.systems.getOutput(t,l);return a.jsx(Hw,{$crits:v,$direction:e,$destroyed:d,$box:h,onClick:d?void 0:p,onContextMenu:d?M=>M.preventDefault():f,children:a.jsxs(Kf,{$over:x>b,children:[x,"/",b]})},`thruster-${e}-${c}`)})},hv=()=>a.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[a.jsx("path",{d:"M9 3H3v6"}),a.jsx("path",{d:"M15 3h6v6"}),a.jsx("path",{d:"M9 21H3v-6"}),a.jsx("path",{d:"M15 21h6v-6"})]});class pv extends I.Component{fullScreen(){var e=window.document,n=e.documentElement,r=n.requestFullscreen||n.mozRequestFullScreen||n.webkitRequestFullScreen||n.msRequestFullscreen,i=e.exitFullscreen||e.mozCancelFullScreen||e.webkitExitFullscreen||e.msExitFullscreen;!e.fullscreenElement&&!e.mozFullScreenElement&&!e.webkitFullscreenElement&&!e.msFullscreenElement?r.call(n):i.call(e)}render(){return a.jsx(fv,{onClick:this.fullScreen.bind(this),title:"Full screen",children:a.jsx(hv,{})})}}const fv=u(Ei)`
    width: ${g.hud.btn};
    height: ${g.hud.btn};
    position: fixed;
    right: calc((${g.hud.btn} + ${g.hud.gap}) * 2);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${We}

    svg {
        width: ${g.hud.icon};
        height: ${g.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${g.hud.btnSmall};
        height: ${g.hud.btnSmall};
        right: calc((${g.hud.btnSmall} + ${g.hud.gapSmall}) * 2);

        svg {
            width: ${g.hud.iconSmall};
            height: ${g.hud.iconSmall};
        }
    }
`;class gv extends I.Component{constructor(e){super(e),this.state={available:tg()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{const e=tg();this.state.available!==e&&this.setState({available:e})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?a.jsx(xv,{onClick:this.surrender,title:"Surrender",children:a.jsx("img",{src:mv(),alt:""})}):null}}const tg=()=>{if(typeof window.gamedata>"u")return!1;const t=window.gamedata;if(t.replay||!t.isPlayerInGame()||t.status==="SURRENDERED"||t.status==="FINISHED")return!1;for(const e in t.slots){const n=t.slots[e];if(n.playerid==t.thisplayer&&n.surrendered!==null&&n.surrendered!==void 0)return!1}return!0},mv=()=>{const t="./img/surrender_icon1.png";return window.AssetManager?window.AssetManager.getSmartImagePath(t):t},ng="34px",rg="22px",xv=u(Ei)`
    width: ${g.hud.btn};
    height: ${g.hud.btn};
    position: fixed;
    right: calc(${g.hud.btn} + ${g.hud.gap});
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${We}

    img {
        width: ${ng};
        height: ${ng};
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
        width: ${g.hud.btnSmall};
        height: ${g.hud.btnSmall};
        right: calc(${g.hud.btnSmall} + ${g.hud.gapSmall});

        img {
            width: ${rg};
            height: ${rg};
            transform: translateY(${"-2px"});
        }
    }
`,yv=()=>a.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[a.jsx("path",{d:"M3 10.5L12 3l9 7.5"}),a.jsx("path",{d:"M5 9v12h14V9"}),a.jsx("path",{d:"M10 21v-6h4v6"})]});class wv extends I.Component{render(){return a.jsx(vv,{as:"a",href:"games.php",title:"Back to Lobby",children:a.jsx(yv,{})})}}const vv=u(Ei)`
    width: ${g.hud.btn};
    height: ${g.hud.btn};
    position: fixed;
    right: calc((${g.hud.btn} + ${g.hud.gap}) * 3);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${We}

    svg {
        width: ${g.hud.icon};
        height: ${g.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${g.hud.btnSmall};
        height: ${g.hud.btnSmall};
        right: calc((${g.hud.btnSmall} + ${g.hud.gapSmall}) * 3);

        svg {
            width: ${g.hud.iconSmall};
            height: ${g.hud.iconSmall};
        }
    }
`;class bv extends ie.Component{constructor(e){super(e),this.state={losToggled:!1,hexToggled:!1,soundToggled:gamedata.playAudio!==!1,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this),this.setBackground=this.setBackground.bind(this),this.externalSetBackground=this.externalSetBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener("LoSToggled",this.externalToggleLoS),window.addEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.addEventListener("BackgroundToggled",this.externalToggleBackground),window.addEventListener("BackgroundPreferenceChanged",this.externalSetBackground),window.addEventListener("soundToggled",this.externalToggleSound),window.gameOptions&&gameOptions.get("hideMapBackground")&&this.setBackground(!0),this.replayCheck=setInterval(()=>{const e=this.getReplayMode();this.state.replayMode!==e&&this.setState({replayMode:e})},500)}componentWillUnmount(){window.removeEventListener("LoSToggled",this.externalToggleLoS),window.removeEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.removeEventListener("BackgroundToggled",this.externalToggleBackground),window.removeEventListener("BackgroundPreferenceChanged",this.externalSetBackground),window.removeEventListener("soundToggled",this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(e=>({hexToggled:!e.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}externalSetBackground(e){this.setBackground(!!(e.detail&&e.detail.off))}showFriendlyEW(e){webglScene.customEvent("ShowFriendlyEW",{up:e})}showEnemyEW(e){webglScene.customEvent("ShowEnemyEW",{up:e})}toggleFriendlyBallisticLines(e){if(e)return;const n=!this.state.fbToggled;this.setState({fbToggled:n}),webglScene.customEvent("ToggleFriendlyBallisticLines",{up:e})}toggleEnemyBallisticLines(e){if(e)return;const n=!this.state.ebToggled;this.setState({ebToggled:n}),webglScene.customEvent("ToggleEnemyBallisticLines",{up:e})}toggleLoS(e){if(e)return;const n=!this.state.losToggled;this.setState({losToggled:n}),webglScene.customEvent("ToggleLoS",{up:e}),window.dispatchEvent(new CustomEvent("LoSToggled"))}toggleHexNumbers(e){if(e)return;const n=!this.state.hexToggled;this.setState({hexToggled:n}),webglScene.customEvent("ToggleHexNumbers",{up:e}),window.dispatchEvent(new CustomEvent("HexNumbersToggled"))}toggleSound(){const e=!this.state.soundToggled;this.setState({soundToggled:e}),webglScene.customEvent("ToggleSound",{enabled:e})}toggleBackground(){this.setBackground(!this.state.bgToggled)}setBackground(e){const n=document.getElementById("background");if(!n||e===this.state.bgToggled)return;let r=this.state.originalBgImage;e?(r||(r=n.style.backgroundImage),n.style.backgroundImage="none",n.style.backgroundColor="black"):(n.style.backgroundImage=r||"",n.style.backgroundColor=""),this.setState({bgToggled:e,originalBgImage:r})}render(){return a.jsxs(Sv,{children:[a.jsx($v,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),a.jsx(kv,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),a.jsx(Tv,{$toggled:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),a.jsx(Cv,{$toggled:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),a.jsx(Mv,{$toggled:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),a.jsx(jv,{$toggled:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),a.jsx(Ev,{$toggled:this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?"Enable Background":"Disable Background"}),this.state.replayMode&&a.jsx(Dv,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?"Sound On":"Sound Off"})]})}}const Sv=u.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,an=u(Ei)`
    display: flex;
    width: ${g.hud.btn};
    height: ${g.hud.btn};
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
        width: ${g.hud.btnSmall};
        height: ${g.hud.btnSmall};
    }
`,kv=u(an)`
    background-image: url("./img/EEW.png");
`,$v=u(an)`
    background-image: url("./img/FEW.png");
`,Cv=u(an)`
    background-image: url("./img/ballisticTarget2.png");
    box-shadow: ${t=>t.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${t=>t.$toggled?"#1b533d":g.colors.windowBg};
    border: 1px solid ${t=>t.$toggled?"limegreen":g.colors.line};
    border-right: none;
`,Tv=u(an)`
    background-image: url("./img/ballisticLaunch2.png");
    box-shadow: ${t=>t.$toggled?"inset 0 0 15px 5px rgba(50, 205, 50, 0.4)":"none"};
    background-color: ${t=>t.$toggled?"#1b533d":g.colors.windowBg};
    border: 1px solid ${t=>t.$toggled?"limegreen":g.colors.line};
    border-right: none;
`,Mv=u(an)`
    background-image: url("./img/los1.png");
    filter: ${t=>t.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${t=>t.$toggled?"limegreen":g.colors.line};
    border-right: none;
`,jv=u(an)`
    background-image: url("./img/hexNumber.png");
    filter: ${t=>t.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${t=>t.$toggled?"limegreen":g.colors.line};
    border-right: none;
`,Ev=u(an)`
    filter: ${t=>t.$toggled?"brightness(1.6) sepia(0.85) hue-rotate(60deg) saturate(4)":"none"};
    border: 1px solid ${t=>t.$toggled?"limegreen":g.colors.line};
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
`,Dv=u(an)`
    background-image: ${t=>t.$toggled?'url("./img/soundOn.png")':'url("./img/soundOff.png")'};
    border: 1px solid ${g.colors.line};
    border-right: none;
`,Pv=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Av=u.div`
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
`,Rv=u.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,Iv=u.div`
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
`;class Ov extends I.Component{selectMode(e,n){e.stopPropagation(),e.preventDefault();const{ship:r,system:i}=this.props;weaponManager.onSetModeClicked(r,i,n)}selectAllMode(e,n){e.stopPropagation(),e.preventDefault();const{ship:r,system:i}=this.props;weaponManager.onSetModeAllClicked(r,i,n)}render(){const{ship:e,system:n}=this.props,r=this.props.showModes!==!1,i=parseInt(n.firingMode);let o="";n.iconPath?o=`./img/systemicons/${n.iconPath}`:o=`./img/systemicons/${n.name}.png`;const s=[];for(const l in n.firingModes)if(n.firingModes.hasOwnProperty(l)){const c=parseInt(l),d=n.firingModes[l],h=c===i;let p=n.modeLetters||1;n.modeLettersArray&&n.modeLettersArray[c]&&(p=n.modeLettersArray[c]),s.push(a.jsx(Iv,{img:o,selected:h,onClick:f=>this.selectMode(f,c),onContextMenu:f=>this.selectAllMode(f,c),title:`Set mode: ${d} ${h?"(Current)":""} (Right click to set all)`,children:d.substring(0,p)},c))}return a.jsxs(Pv,{children:[r&&a.jsx(Av,{children:"Select Firing Mode"}),a.jsxs(Rv,{children:[r&&s,this.props.children]})]})}}const Lv=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,zv=u.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,Bv=u.div`
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

`,ig=u.div`
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
`,Nv=u.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,jc=u.span`
    font-weight: bold;
`,og=u.span`
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

`,Fv=u(jc)`
    color: #ffb833;
    font-weight: normal;    
`,_v=u(jc)`
    color: #ff3333;
    font-weight: normal;    
`,Hv=u.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,$s=u.div`
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
`,Wv=u.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,Uv=u.div`
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
`,sg=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,Gv=u(ig)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,Ec=u.span`
    color: #00b8e6;
    font-weight: normal;
    margin-right: 4px;
`,Vv=u.input`
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
`,Yv=u.span`
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
`;class Kv extends I.Component{constructor(e){super(e),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getEffectiveCriticalRepairCost(e,n){return n.name==="cnC"?4:e.repairCost}getDockedUnits(){const{ship:e,system:n}=this.props;if(!n.servicesDockedUnits)return[];const r=[],i=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const o of i)if(!(!o.isDockingBay||!Array.isArray(o.shipsDocked)))for(const s of o.shipsDocked){const l=gamedata.getShip(s.shipId);l&&r.indexOf(l)===-1&&r.push(l)}return r}getDockedRepairables(){const{system:e}=this.props,n=[];for(const r of this.getDockedUnits()){const i=Array.isArray(r.systems)?r.systems:Object.values(r.systems),o="d"+r.id+":";for(const s of i){const l=s.name==="structure"||s.name==="cnC"||s.name==="SelfRepair";if(!shipManager.systems.isDestroyed(r,s)&&s.repairPriority>=1&&s.criticals){const f=Array.isArray(s.criticals)?s.criticals:Object.values(s.criticals);for(const v of f){if(v.repairPriority===0||v.turn>=gamedata.turn||v.oneturn||v.turnend>0)continue;const x=o+s.id+"-"+v.id;let b=v.repairPriority||0,M=!1;e.priorityChanges&&x in e.priorityChanges&&e.priorityChanges[x]>=0?(b=e.priorityChanges[x],M=!0):b<10&&(b+=s.repairPriority),!(b<1)&&n.push({type:"critical",sys:s,crit:v,ownerShip:r,shipId:r.id,docked:!0,priority:b,overridden:M,cost:this.getEffectiveCriticalRepairCost(v,s),id:s.id,subId:v.id,keyId:x})}}if(!l||s.repairPriority===0)continue;const c=shipManager.systems.getTotalDamage(s);if(c<=0)continue;if(s.name==="structure"){if(shipManager.systems.isDestroyed(r,s))continue}else{const f=s.structureHomeLocation!==void 0&&s.structureHomeLocation!==null?s.structureHomeLocation:s.location;if(f!=0){const v=shipManager.systems.getStructureSystem(r,f);if(v&&shipManager.systems.isDestroyed(r,v))continue}}const d=o+s.id;let h=s.repairPriority,p=!1;e.priorityChanges&&d in e.priorityChanges&&e.priorityChanges[d]>=0&&(h=e.priorityChanges[d],p=!0),!(h<1)&&(!p&&shipManager.systems.isDestroyed(r,s)&&h<=10&&(h+=10),n.push({type:"system",sys:s,ownerShip:r,shipId:r.id,docked:!0,priority:h,overridden:p,damage:c,maxHealth:s.maxhealth,id:s.id,subId:0,keyId:d}))}}return n}getRepairableSystems(){const{ship:e,system:n}=this.props,r=[],i=[],o=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const c of o){if(c.name==="SelfRepair"||c.repairPriority===0||c.privateRepairOnly&&!n.repairRestrictedTo||n.repairRestrictedTo&&!n.repairRestrictedTo.includes(c.id)||c.name=="structure"&&shipManager.systems.isDestroyed(c.ship,c))continue;const d=c.structureHomeLocation!==void 0&&c.structureHomeLocation!==null?c.structureHomeLocation:c.location;if(c.name!="structure"&&d!=0){var s=shipManager.systems.getStructureSystem(c.ship,d);if(s&&shipManager.systems.isDestroyed(c.ship,s))continue}let h=c.repairPriority,p=!1;if(n.priorityChanges&&c.id in n.priorityChanges&&n.priorityChanges[c.id]>=0&&(h=n.priorityChanges[c.id],p=!0),!p&&shipManager.systems.isDestroyed(e,c)&&h<=10&&(h+=10),!shipManager.systems.isDestroyed(e,c)&&c.criticals){const v=Array.isArray(c.criticals)?c.criticals:Object.values(c.criticals);for(const x of v){if(x.repairPriority===0||x.turn>=gamedata.turn||x.oneturn||x.turnend>0)continue;let b=x.repairPriority||0;const M=c.id+"-"+x.id;let w=!1;n.priorityChanges&&M in n.priorityChanges&&n.priorityChanges[M]>=0?(b=n.priorityChanges[M],w=!0):b<10&&(b+=c.repairPriority),i.push({type:"critical",sys:c,crit:x,ownerShip:e,shipId:0,docked:!1,priority:b,overridden:w,cost:this.getEffectiveCriticalRepairCost(x,c),id:c.id,subId:x.id,keyId:M})}}const f=shipManager.systems.getTotalDamage(c);f>0&&r.push({type:"system",sys:c,ownerShip:e,shipId:0,docked:!1,priority:h,overridden:p,damage:f,maxHealth:c.maxhealth,id:c.id,subId:0,keyId:c.id})}const l=[...i,...r,...this.getDockedRepairables()];return l.sort((c,d)=>{if(c.priority!==d.priority)return d.priority-c.priority;const h=!!c.overridden,p=!!d.overridden;if(h!==p)return h?-1:1;if(this.lastOrder&&this.lastOrder.length>0){const x=this.lastOrder.indexOf(c.keyId),b=this.lastOrder.indexOf(d.keyId);if(x!==-1&&b!==-1)return x-b}const f=c.shipId||0,v=d.shipId||0;return f!==v?f-v:c.id!==d.id?c.id-d.id:c.subId-d.subId}),this.lastOrder=l.map(c=>c.keyId),l}handleInputChange(e,n,r){const i=e.target.value;if(i===""){this.setState(s=>({priorityInputs:{...s.priorityInputs,[n]:""}}));return}const o=parseInt(i,10);isNaN(o)||(this.setState(s=>({priorityInputs:{...s.priorityInputs,[n]:o}})),this.setPriority(n,o))}handleWheel(e,n,r){e.preventDefault();const i=e.deltaY<0?1:-1,o=r+i;o<1||this.setPriority(n,o)}componentDidUpdate(e){if(this.dragRef&&this.dragRef.started&&this.listRef){const s=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');s&&this.positionDraggedEl(s,this.dragRef,this.dragRef.lastClientY)}const n=this.getRepairableSystems(),r=this.state.priorityInputs,i={};let o=!1;n.forEach(s=>{const l=s.keyId,c=s.priority;r[l]!==void 0&&r[l]!==c&&document.activeElement!==document.getElementById(`prio-input-${l}`)&&(i[l]=c,o=!0)}),o&&this.setState(s=>({priorityInputs:{...s.priorityInputs,...i}}))}handleTop(e,n){e.stopPropagation();const r=this.getRepairableSystems();if(r.length===0)return;const i=r[0].priority,o=r.find(l=>l.keyId===n);if(!o||o.priority===i)return;let s=i+1;this.setPriority(n,s)}handleUp(e,n,r){e.stopPropagation();let i=r+1;i!==r&&this.setPriority(n,i)}handleDown(e,n,r){e.stopPropagation(),!(r<=1)&&this.setPriority(n,r-1)}handleReset(e,n){e.stopPropagation(),this.setPriority(n,-1)}setPriority(e,n){const{ship:r,system:i}=this.props;i.setOverride(e,n),webglScene.customEvent("SystemDataChanged",{ship:r,system:i})}onRowPointerDown(e,n,r,i){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest("input, .sr-action-button"))return;const o=e.currentTarget,s=o?o.offsetHeight:24,l=this.listRef?this.listRef.offsetHeight:0,c=o?o.offsetWidth:0,d=o?e.clientY-o.getBoundingClientRect().top:0;this.dragRef={keyId:n,pointerId:e.pointerId,startY:e.clientY,startIdx:r,order:i,gapSize:s,lockHeight:l,anchorWidth:c,grabOffsetInRow:d,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(e){const n=this.dragRef;if(!(!n||e.pointerId!==n.pointerId)){if(!n.started){if(Math.abs(e.clientY-n.startY)<4)return;n.started=!0}e.preventDefault(),n.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){const n=this.dragRef;if(!n||!this.listRef)return;const r=this.listRef.querySelectorAll("[data-keyid]"),i=this.listRef.getBoundingClientRect(),o=e-i.top+this.listRef.scrollTop,l=this.state.drag&&this.state.drag.dropIdx===0?n.gapSize:0;let c=0,d=null;for(let p=0;p<r.length;p++){if(r[p].getAttribute("data-keyid")===String(n.keyId)){d=r[p];continue}const f=r[p].offsetTop-l+r[p].offsetHeight/2;if(o<f)break;c++}d&&this.positionDraggedEl(d,n,e);const h=this.state.drag;(!h||h.keyId!==n.keyId||h.dropIdx!==c)&&this.setState({drag:{keyId:n.keyId,startIdx:n.startIdx,dropIdx:c,gapSize:n.gapSize,lockHeight:n.lockHeight}})}positionDraggedEl(e,n,r){const i=this.listRef.getBoundingClientRect();let o=r-i.top+this.listRef.scrollTop-n.grabOffsetInRow;const s=Math.max(0,this.listRef.scrollHeight-n.gapSize);o<0?o=0:o>s&&(o=s),e.style.top=o+"px",e.style.left="0px",e.style.width=n.anchorWidth+"px",e.style.transform="none"}updateAutoScroll(e){const n=this.listRef;if(!n){this.stopAutoScroll();return}const r=30,i=n.getBoundingClientRect(),o=n.scrollTop>0,s=n.scrollTop<n.scrollHeight-n.clientHeight-1,l=e-i.top,c=i.bottom-e;o&&l<r?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,l)/r),this.ensureAutoScrollRunning()):s&&c<r?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,c)/r),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const e=this.listRef,n=this.dragRef;if(!e||!n||this.autoScrollDir===0)return;const r=e.scrollTop;if(e.scrollTop=r+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===r){this.stopAutoScroll();return}this.updateDragForPointer(n.lastClientY),this.updateAutoScroll(n.lastClientY)}onDragEnd(e){const n=this.dragRef;if(!n||e&&e.pointerId!=null&&e.pointerId!==n.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const i=this.listRef.querySelector('[data-keyid="'+n.keyId+'"]');i&&(i.style.transform="",i.style.top="",i.style.left="",i.style.width="")}this.dragRef=null;const r=this.state.drag;this.setState({drag:null}),!(!n.started||!r)&&r.dropIdx!==n.startIdx&&this.applyDropReorder(n.order,n.keyId,r.dropIdx)}applyDropReorder(e,n,r){const{ship:i,system:o}=this.props,s=e.findIndex(f=>f.keyId===n);if(s===-1)return;const l=e.slice(),[c]=l.splice(s,1);l.splice(r,0,c);const d=l[r+1],h=l[r-1];let p;d?p=d.priority+1:h?p=Math.max(1,h.priority-1):p=1,o.setOverride(n,p);for(let f=r-1;f>=0&&!(l[f].priority>p);f--)p+=1,o.setOverride(l[f].keyId,p);webglScene.customEvent("SystemDataChanged",{ship:i,system:o})}handlePropagate(e){e.stopPropagation();const{ship:n,system:r}=this.props;for(const i of n.systems)if(i.name==="SelfRepair"&&i.id!==r.id){if(i.priorityChanges)for(const o in i.priorityChanges)(!r.priorityChanges||!(o in r.priorityChanges))&&i.setOverride(o,-1);if(r.priorityChanges)for(const o in r.priorityChanges){const s=r.priorityChanges[o];s>=0&&i.setOverride(o,s)}}webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,readOnly:n}=this.props,r=this.getRepairableSystems();let i=0;const o=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const s of o)s.name==="SelfRepair"&&i++;return a.jsxs(Lv,{children:[a.jsx(zv,{children:n?"Repair Queue (view only)":"Manage Repair Queue"}),a.jsxs(Bv,{ref:s=>{this.listRef=s},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[r.length===0&&a.jsx(Gv,{children:"No damaged systems"}),r.map((s,l)=>{const c=this.state.drag,d=c&&c.keyId===s.keyId;let h=!1,p=!1,f=!1;if(c&&!d){const x=r.length-1,b=l>c.startIdx?l-1:l;c.dropIdx===0&&b===0?h=!0:c.dropIdx===x&&b===x-1?p=!0:c.dropIdx===b&&(f=!0)}const v=s.ownerShip||e;return a.jsxs(ig,{"data-keyid":s.keyId,$dragging:d,$gapBefore:h,$lineAtEnd:p,$lineBefore:f,$gapSize:c?c.gapSize:0,$readOnly:n,onPointerDown:x=>this.onRowPointerDown(x,s.keyId,l,r),children:[a.jsx(Nv,{children:s.type==="critical"?a.jsxs(a.Fragment,{children:[a.jsxs(Fv,{children:[s.docked&&a.jsxs(Ec,{children:[v.name,":"]}),s.sys.displayName," (",s.crit.description||s.crit.phpclass,")"]}),a.jsxs(og,{children:["Cost: ",s.cost," ",a.jsx(sg,{})," Id: ",s.sys.id]})]}):a.jsxs(a.Fragment,{children:[shipManager.systems.isDestroyed(v,s.sys)?a.jsxs(_v,{children:[s.docked&&a.jsxs(Ec,{children:[v.name,":"]}),s.sys.displayName]}):a.jsxs(jc,{children:[s.docked&&a.jsxs(Ec,{children:[v.name,":"]}),s.sys.displayName]}),a.jsxs(og,{children:["HP: ",shipManager.systems.getRemainingHealth(s.sys)," / ",s.sys.maxhealth," ",a.jsx(sg,{})," Id: ",s.sys.id]})]})}),a.jsx(Hv,{children:n?a.jsx(Yv,{title:"Priority (view only)",children:s.priority}):a.jsxs(a.Fragment,{children:[a.jsx($s,{className:"sr-action-button",title:"Reset Default",onClick:x=>this.handleReset(x,s.keyId),img:"./img/iconSRCancel.png"}),a.jsx($s,{className:"sr-action-button",title:"Decrease Priority",onClick:x=>this.handleDown(x,s.keyId,s.priority),img:"./img/systemicons/AAclasses/iconMinus.png"}),a.jsx(Vv,{id:`prio-input-${s.keyId}`,type:"number",value:this.state.priorityInputs[s.keyId]!==void 0?this.state.priorityInputs[s.keyId]:s.priority,onChange:x=>this.handleInputChange(x,s.keyId,s.priority),onClick:x=>x.stopPropagation(),onWheel:x=>this.handleWheel(x,s.keyId,s.priority)}),a.jsx($s,{className:"sr-action-button",title:"Increase Priority",onClick:x=>this.handleUp(x,s.keyId,s.priority),img:"./img/systemicons/AAclasses/iconPlus.png"}),a.jsx($s,{className:"sr-action-button",title:"Move to Top",onClick:x=>this.handleTop(x,s.keyId),img:"./img/iconSRHigh.png"})]})})]},s.keyId)})]}),!n&&i>1&&a.jsx(Wv,{children:a.jsx(Uv,{onClick:s=>this.handlePropagate(s),children:"Set all Self Repair systems"})})]})}}const Qv=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
`,Xv=u.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,Jv=u.div`
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
`,ag=u.div`
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
`,qv=u(ag)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,Zv=u.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,lg=u.span`
    font-weight: bold;
`,e1=u(lg)`
    color: #ff3333;
    font-weight: normal;
`,t1=u.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,n1=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,r1=u.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,i1=u.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,o1=u.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;class s1 extends I.Component{constructor(e){super(e),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getOrderedBlocks(){const{ship:e,system:n}=this.props,r=(n.structureBlocks||[]).map(l=>{const c=(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).find(f=>f.id===l.id),d=c?shipManager.systems.getRemainingHealth(c):l.maxhealth,h=c?c.maxhealth:l.maxhealth||0,p=c?shipManager.systems.isDestroyed(e,c):!1;return{id:l.id,displayName:l.displayName,hp:d,maxhealth:h,destroyed:p}}),i=n.repairOrder||[];if(i.length===0)return r.slice().sort((l,c)=>{const d=c.destroyed?1:0,h=l.destroyed?1:0;return d!==h?d-h:c.maxhealth-c.hp-(l.maxhealth-l.hp)});const o=[],s=new Set;for(const l of i){const c=r.find(d=>d.id===l);c&&(o.push(c),s.add(l))}for(const l of r)s.has(l.id)||o.push(l);return o}onRowPointerDown(e,n,r,i){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest(".ssr-action-button"))return;const o=e.currentTarget,s=o?o.offsetHeight:24,l=this.listRef?this.listRef.offsetHeight:0,c=o?o.offsetWidth:0,d=o?e.clientY-o.getBoundingClientRect().top:0;this.dragRef={keyId:n,pointerId:e.pointerId,startY:e.clientY,startIdx:r,order:i,gapSize:s,lockHeight:l,anchorWidth:c,grabOffsetInRow:d,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(e){const n=this.dragRef;if(!(!n||e.pointerId!==n.pointerId)){if(!n.started){if(Math.abs(e.clientY-n.startY)<4)return;n.started=!0}e.preventDefault(),n.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){const n=this.dragRef;if(!n||!this.listRef)return;const r=this.listRef.querySelectorAll("[data-keyid]"),i=this.listRef.getBoundingClientRect(),o=e-i.top+this.listRef.scrollTop,l=this.state.drag&&this.state.drag.dropIdx===0?n.gapSize:0;let c=0,d=null;for(let p=0;p<r.length;p++){if(r[p].getAttribute("data-keyid")===String(n.keyId)){d=r[p];continue}const f=r[p].offsetTop-l+r[p].offsetHeight/2;if(o<f)break;c++}d&&this.positionDraggedEl(d,n,e);const h=this.state.drag;(!h||h.keyId!==n.keyId||h.dropIdx!==c)&&this.setState({drag:{keyId:n.keyId,startIdx:n.startIdx,dropIdx:c,gapSize:n.gapSize,lockHeight:n.lockHeight}})}positionDraggedEl(e,n,r){const i=this.listRef.getBoundingClientRect();let o=r-i.top+this.listRef.scrollTop-n.grabOffsetInRow;const s=Math.max(0,this.listRef.scrollHeight-n.gapSize);o<0?o=0:o>s&&(o=s),e.style.top=o+"px",e.style.left="0px",e.style.width=n.anchorWidth+"px",e.style.transform="none"}updateAutoScroll(e){const n=this.listRef;if(!n){this.stopAutoScroll();return}const r=30,i=n.getBoundingClientRect(),o=n.scrollTop>0,s=n.scrollTop<n.scrollHeight-n.clientHeight-1,l=e-i.top,c=i.bottom-e;o&&l<r?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,l)/r),this.ensureAutoScrollRunning()):s&&c<r?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,c)/r),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const e=this.listRef,n=this.dragRef;if(!e||!n||this.autoScrollDir===0)return;const r=e.scrollTop;if(e.scrollTop=r+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===r){this.stopAutoScroll();return}this.updateDragForPointer(n.lastClientY),this.updateAutoScroll(n.lastClientY)}onDragEnd(e){const n=this.dragRef;if(!n||e&&e.pointerId!=null&&e.pointerId!==n.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const i=this.listRef.querySelector('[data-keyid="'+n.keyId+'"]');i&&(i.style.transform="",i.style.top="",i.style.left="",i.style.width="")}this.dragRef=null;const r=this.state.drag;this.setState({drag:null}),!(!n.started||!r)&&r.dropIdx!==n.startIdx&&this.applyDropReorder(n.order,n.keyId,r.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){const e=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');e&&this.positionDraggedEl(e,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(e,n,r){const{ship:i,system:o}=this.props,s=e.slice(),l=s.findIndex(h=>h.id===n);if(l===-1)return;const[c]=s.splice(l,1);s.splice(r,0,c);const d=s.map(h=>h.id);o.setRepairOrder(d),webglScene.customEvent("SystemDataChanged",{ship:i,system:o})}handleReset(e){e.stopPropagation();const{ship:n,system:r}=this.props;r.setRepairOrder([]),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,readOnly:n}=this.props,r=this.getOrderedBlocks(),i=(this.props.system.repairOrder||[]).length>0;return a.jsxs(Qv,{children:[a.jsx(Xv,{children:n?"Structure Repair Order (view only)":"Manage Structure Repair"}),!n&&a.jsx(o1,{children:"Drag rows to set repair priority — top = first repaired"}),a.jsxs(Jv,{ref:o=>{this.listRef=o},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[r.length===0&&a.jsx(qv,{children:"No structure blocks found"}),r.map((o,s)=>{const l=this.state.drag,c=l&&l.keyId===o.id;let d=!1,h=!1,p=!1;if(l&&!c){const v=r.length-1,x=s>l.startIdx?s-1:s;l.dropIdx===0&&x===0?d=!0:l.dropIdx===v&&x===v-1?h=!0:l.dropIdx===x&&(p=!0)}const f=o.maxhealth-o.hp;return a.jsx(ag,{"data-keyid":o.id,$dragging:c,$gapBefore:d,$lineAtEnd:h,$lineBefore:p,$gapSize:l?l.gapSize:0,$readOnly:n,onPointerDown:v=>this.onRowPointerDown(v,o.id,s,r),children:a.jsxs(Zv,{children:[o.destroyed?a.jsx(e1,{children:o.displayName}):a.jsx(lg,{children:o.displayName}),a.jsxs(t1,{children:["HP: ",o.hp," / ",o.maxhealth,f>0&&a.jsxs(a.Fragment,{children:[a.jsx(n1,{}),"Dmg: ",f,o.destroyed?" — DESTROYED":""]})]})]})},o.id)})]}),!n&&a.jsx(r1,{children:a.jsx(i1,{className:"ssr-action-button",onClick:o=>this.handleReset(o),title:"Clear custom order and return to default (destroyed first, then most damaged)",children:i?"Reset to Default Order":"Using Default Order"})})]})}}const a1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,l1=u.div`
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
`,c1=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,cg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,d1=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,u1=u.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,h1=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,p1=u.div`
    width: 20px;
    text-align: center;
`,Dc=u.div`
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
`,f1=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class g1 extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrDmgType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrDmgType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating AA setting for:",e),r.setCurrDmgType(e);const l=r.allocatedAA[e];var c=[];for(var d in i.ships){var h=i.ships[d];if(h.userid==n.userid&&!o.isDestroyed(h))if(h.flight)for(var p=0;p<h.systems.length;p++){var f=h.systems[p];if(f)for(var v=0;v<f.systems.length;v++){var x=f.systems[v];if(x&&x.displayName=="Adaptive Armor Controller"){c.push(x);break}}}else for(var v=0;v<h.systems.length;v++){var x=h.systems[v];if(x.displayName=="Adaptive Armor Controller"){c.push(x);break}}}console.log("Found AA controllers:",c.length);for(var b=0;b<c.length;b++){var x=c[b];x.setCurrDmgType(e);let w=0;for(;x.getCurrAllocated()<l&&x.canIncrease()&&w<100;)x.doIncrease(),w++;w>=100&&console.warn("AA Propagation safety break for",x)}s.customEvent("SystemDataChanged",{ship:n,system:r})}getRelevantArmorTypes(){const{ship:e,system:n}=this.props;return n.getRelevantArmorTypes(e)}render(){const{ship:e,system:n}=this.props;if(!n||!n.availableAA)return null;const r=this.getRelevantArmorTypes(),i=n.allocatedAA,o=c=>{const d=n.AAtotal_used,h=n.AAtotal,p=i[c]||0,f=n.AApertype,v=n.availableAA[c],x=n.AApreallocated,b=n.AApreallocated_used;return!(d>=h||p>=f||x<=b&&v<=p)},s=c=>n.currchangedAA[c]>0,l=c=>i[c]>0;return a.jsxs(a1,{children:[a.jsx(l1,{children:"Manage Adaptive Armor"}),a.jsxs(c1,{ref:this.listRef,children:[r.map(c=>a.jsxs(cg,{children:[a.jsx(d1,{src:`./img/systemicons/AAclasses/${c}.png`,alt:c}),a.jsx(u1,{children:c}),a.jsxs(h1,{children:[a.jsx(Dc,{onClick:()=>this.handleDecrease(c),disabled:!s(c),children:"-"}),a.jsx(p1,{children:i[c]}),a.jsx(Dc,{onClick:()=>this.handleIncrease(c),disabled:!o(c),children:"+"}),a.jsx(Dc,{title:"Propagate to all units",onClick:()=>this.handlePropagate(c),disabled:!l(c),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/AAclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},c)),r.length===0&&a.jsx(cg,{children:"No armor types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",n.AAtotal_used," / ",n.AAtotal," ",a.jsx(f1,{})," Max Per Type: ",n.AApertype]})]})}}const m1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,x1=u.div`
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
`,y1=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,dg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,w1=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,v1=u.div`
    flex: 1;
    font-weight: normal; 
`,b1=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,S1=u.div`
    width: 20px;
    text-align: center;
`,Pc=u.div`
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
`,k1=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class $1 extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrFCType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrFCType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating BFCP setting for:",e),r.setCurrFCType(e);const l=r.allocatedBFCP[e];var c=[];for(var d in i.ships){var h=i.ships[d];if(h.userid==n.userid&&!o.isDestroyed(h))if(h.flight)for(var p=0;p<h.systems.length;p++){var f=h.systems[p];if(f)for(var v=0;v<f.systems.length;v++){var x=f.systems[v];if(x&&x.displayName=="Computer"){c.push(x);break}}}else for(var v=0;v<h.systems.length;v++){var x=h.systems[v];if(x.displayName=="Computer"){c.push(x);break}}}console.log("Found BFCP controllers:",c.length);for(var b=0;b<c.length;b++){var x=c[b];x.setCurrFCType(e);let w=0;for(;x.getCurrAllocated()<l&&x.canIncrease()&&w<100;)x.doIncrease(),w++;for(;x.getCurrAllocated()>l&&x.canDecrease()&&w<100;)x.doDecrease(),w++;w>=100&&console.warn("BFCP Propagation safety break for",x)}s.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{system:e}=this.props;if(!e||!e.allocatedBFCP)return null;const n=Object.keys(e.allocatedBFCP),r=e.allocatedBFCP,i=(d,h)=>{const p=e.currClass;e.setCurrFCType(d);const f=!!e[h]();return e.currClass=p,f},o=d=>i(d,"canIncrease"),s=d=>i(d,"canDecrease"),l=window.gamedata&&window.gamedata.gamephase===3&&typeof e.firingMode=="function"?e.firingMode():null,c=d=>r[d]>=0;return a.jsxs(m1,{children:[a.jsx(x1,{children:"Hyach Computer"}),a.jsxs(y1,{ref:this.listRef,children:[n.map(d=>a.jsxs(dg,{children:[a.jsx(w1,{src:`./img/systemicons/BFCPclasses/${d}.png`,alt:d}),a.jsx(v1,{children:d}),a.jsxs(b1,{children:[a.jsx(Pc,{onClick:()=>this.handleDecrease(d),disabled:!s(d),children:"-"}),a.jsx(S1,{children:r[d]}),a.jsx(Pc,{onClick:()=>this.handleIncrease(d),disabled:!o(d),children:"+"}),a.jsx(Pc,{title:"Propagate to all units",onClick:()=>this.handlePropagate(d),disabled:!c(d),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},d)),n.length===0&&a.jsx(dg,{children:"No FC types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",e.BFCPtotal_used," / ",e.output," ",a.jsx(k1,{})," Max Per Type: ",e.BFCPpertype]}),l&&a.jsx("div",{style:{padding:"0 5px 5px",textAlign:"center",fontSize:"10px",color:"#c9a6cf"},children:l==="reshuffle"?"Computer Specialist: any point may be moved":"Expert Software Engineer: one held point may be added"})]})}}const C1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,T1=u.div`
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
`,M1=u.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,ug=u.div`
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
`,j1=u.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,E1=u.span`
    flex-grow: 1;
    font-weight: bold;
`,D1=u.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,Cs=u.div`
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
`,P1=t=>window.gamedata.turn===window.shipManager.getTurnPlaced(t)&&window.gamedata.gamephase===-1;class A1 extends I.Component{constructor(e){super(e)}handleSelect(e){const{system:n}=this.props;n.specCurrClass=e,n.canSelect()&&(n.doSelect(),this.forceUpdate())}handleUnselect(e){const{system:n}=this.props;n.specCurrClass=e,n.canUnselect()&&(n.doUnselect(),this.forceUpdate())}handleUse(e){const{system:n}=this.props;n.specCurrClass=e,n.canUse()&&(n.doUse(),this.forceUpdate())}handleCancel(e){const{system:n}=this.props;n.specCurrClass=e,n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}render(){const{ship:e,system:n}=this.props;if(!n)return null;const r=P1(e);let i=[];const o=!r&&window.officers?window.officers.specialistReuseList(e):[];r?n.allSpec&&(i=Object.keys(n.allSpec)):n.availableSpec&&(i=Object.keys(n.availableSpec).filter(l=>n.availableSpec[l]>0||o.indexOf(l)!==-1)),i.sort();let s="";return r?s=`Specialists Selected: ${Object.values(n.availableSpec||{}).reduce((c,d)=>c+d,0)} / ${n.specTotal}`:s=`Specialists Used: ${n.specTotal_used||0} / ${n.specTotal}`,a.jsxs(C1,{children:[a.jsx(T1,{children:"Hyach Specialists"}),a.jsxs(M1,{children:[i.map(l=>{const c=r&&(n.specCurrClass=l,n.canSelect()),d=r&&(n.specCurrClass=l,n.canUnselect()),h=!r&&(n.specCurrClass=l,n.canUse()),p=!r&&(n.specCurrClass=l,n.canDecrease());n.availableSpec&&n.availableSpec[l]>0,n.currAllocatedSpec&&n.currAllocatedSpec[l];const f=`./img/systemicons/Specialistclasses/${l}.png?v=2`;return a.jsxs(ug,{children:[a.jsx(j1,{src:f,alt:l}),a.jsxs(E1,{children:[l,o.indexOf(l)!==-1&&a.jsx("span",{style:{fontWeight:"normal"},children:" (repeat)"})]}),a.jsx(D1,{children:r?a.jsxs(a.Fragment,{children:[a.jsx(Cs,{onClick:()=>this.handleUnselect(l),disabled:!d,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})}),a.jsx(Cs,{onClick:()=>this.handleSelect(l),disabled:!c,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})})]}):a.jsxs(a.Fragment,{children:[a.jsx(Cs,{onClick:()=>this.handleUse(l),disabled:!h,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})}),a.jsx(Cs,{onClick:()=>this.handleCancel(l),disabled:!p,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})})]})})]},l)}),i.length===0&&a.jsx(ug,{style:{justifyContent:"center",fontStyle:"italic"},children:"No Specialists Available"})]}),a.jsx("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:s})]})}}const R1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${g.colors.line};
`,I1=u.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${g.colors.line};
    border-bottom: 1px solid ${g.colors.line};
    color: ${g.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,O1=u.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,hg=u.div`
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
`,L1=u.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,z1=u.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,B1=u.input`
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
`,qe=u.div`
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
`;u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 6px;
    vertical-align: middle;
    opacity: 0.7;
`;const N1=u.div`
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
`;class pg extends I.Component{constructor(e){super(e),this.listRef=ie.createRef(),this.state={shieldInputs:{}}}getShieldLabel(e){let n=e.startArc,r=e.endArc;if(n===void 0||r===void 0)return e.displayName;let i=(n+r)/2;n>r&&(i=(n+r+360)/2),i=i%360;let o="";return i>=337.5||i<22.5?o="Front":i>=22.5&&i<67.5?o="Front Starboard":i>=67.5&&i<112.5?o="Starboard":i>=112.5&&i<157.5?o="Aft Starboard":i>=157.5&&i<202.5?o="Aft":i>=202.5&&i<247.5?o="Aft Port":i>=247.5&&i<292.5?o="Port":i>=292.5&&i<337.5&&(o="Front Port"),o?`${o} - ${e.displayName}`:e.displayName}getShieldSortPriority(e){const n=this.getShieldLabel(e).split(" - ")[0];return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[n]||99}getGeneratorAndShields(){const{ship:e,system:n}=this.props;let r=null,i=[],o="",s="";return n.name==="ThirdspaceShield"||n.name==="ThirdspaceShieldGenerator"?(o="ThirdspaceShield",s="ThirdspaceShieldGenerator"):(n.name==="ThoughtShield"||n.name==="ThoughtShieldGenerator")&&(o="ThoughtShield",s="ThoughtShieldGenerator"),o?(e.systems&&(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).forEach(c=>{c.name===s&&(r=c),c.name===o&&i.push(c)}),i.sort((l,c)=>{const d=this.getShieldSortPriority(l),h=this.getShieldSortPriority(c);return d!==h?d-h:l.id-c.id}),{generator:r,shields:i,systemName:o}):{generator:null,shields:[]}}handleIncrease(e,n){e.canIncrease()&&(e.doIncrease(n),this.afterShieldChange(e))}handleDecrease(e,n){e.canDecrease()&&(e.doDecrease(n),this.afterShieldChange(e))}handleMin(e){e.canDecrease()&&(e.doMin(),this.afterShieldChange(e))}handleMax(e){e.canIncrease()&&(e.doMax(),this.afterShieldChange(e))}afterShieldChange(e){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}),this.updateInputState(e)}handleInputChange(e,n){if(n===""){this.setState(s=>({shieldInputs:{...s.shieldInputs,[e.id]:""}}));return}const r=parseInt(n,10);if(isNaN(r))return;const i=e.currentHealth,o=r-i;o>0?this.handleIncrease(e,o):o<0&&this.handleDecrease(e,Math.abs(o))}updateInputState(e){this.setState(n=>({shieldInputs:{...n.shieldInputs,[e.id]:e.currentHealth}}))}handleWheel(e,n){e.preventDefault(),(e.deltaY<0?1:-1)>0?this.handleIncrease(n,1):this.handleDecrease(n,1)}handleMouseEnter(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const n=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);n&&(n.showWeaponArc(this.props.ship,e),window.webglScene.requestRender())}}handleMouseLeave(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const n=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);n&&(n.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(e){e&&(shipManager.power.clickPlus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}handleDeBoost(e){e&&(shipManager.power.clickMinus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}handleEqualise(e){e&&(e.doEqualise(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}componentDidMount(){const{generator:e,shields:n}=this.getGeneratorAndShields(),r={};n.forEach(i=>r[i.id]=i.currentHealth),this.setState({shieldInputs:r})}componentDidUpdate(e){const{generator:n,shields:r}=this.getGeneratorAndShields(),i=this.state.shieldInputs,o={};let s=!1;r.forEach(l=>{i[l.id]!==l.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${l.id}`)&&(o[l.id]=l.currentHealth,s=!0)}),s&&this.setState(l=>({shieldInputs:{...l.shieldInputs,...o}}))}render(){const{generator:e,shields:n,systemName:r}=this.getGeneratorAndShields();return e?a.jsxs(R1,{children:[a.jsx(I1,{children:r==="ThirdspaceShield"?"Thirdspace Shields":"Thought Shields"}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791"},children:["Unallocated Shield Energy: ",e.storedCapacity]}),r==="ThirdspaceShield"&&a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791",display:"flex",justifyContent:"center",alignItems:"center",gap:"5px"},children:["Regeneration Rate: ",shipManager.systems.getOutputNoBoost(this.props.ship,e)+shipManager.power.getBoost(e)*n.length,a.jsx(qe,{className:"small",onClick:()=>this.handleDeBoost(e),title:"Reduce Boost",children:"-"}),a.jsx(qe,{className:"small",onClick:()=>this.handleBoost(e),title:"Boost Generator",children:"+"})]}),a.jsxs(O1,{ref:this.listRef,children:[n.map(i=>a.jsxs(hg,{onMouseEnter:()=>this.handleMouseEnter(i),onMouseLeave:()=>this.handleMouseLeave(i),children:[a.jsx(L1,{children:this.getShieldLabel(i)}),a.jsxs(z1,{children:[a.jsx(qe,{onClick:()=>this.handleMin(i),disabled:!i.canDecrease(),title:"Drop shield to 0",children:"Min"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,25),disabled:!i.canDecrease(),children:"-25"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,10),disabled:!i.canDecrease(),children:"-10"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,5),disabled:!i.canDecrease(),children:"-5"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,1),disabled:!i.canDecrease(),children:"-1"}),a.jsx(B1,{id:`shield-input-${i.id}`,type:"number",value:this.state.shieldInputs[i.id]!==void 0?this.state.shieldInputs[i.id]:i.currentHealth,onChange:o=>this.handleInputChange(i,o.target.value),onWheel:o=>this.handleWheel(o,i)}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,1),disabled:!i.canIncrease(),children:"+1"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,5),disabled:!i.canIncrease(),children:"+5"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,10),disabled:!i.canIncrease(),children:"+10"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,25),disabled:!i.canIncrease(),children:"+25"}),a.jsx(qe,{onClick:()=>this.handleMax(i),disabled:!i.canIncrease(),title:"Raise shield to maximum",children:"Max"})]})]},i.id)),n.length===0&&a.jsx(hg,{children:"No Shields Found"})]}),e&&a.jsx(N1,{onClick:()=>this.handleEqualise(e),children:"Equalise Shields"})]}):a.jsx("div",{children:"No Generator Found"})}}const fg=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${g.colors.line};
`,gg=u.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${g.colors.line};
    border-bottom: 1px solid ${g.colors.line};
    color: ${g.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,Ac=u.div`
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
`,Pi=u.div`
    flex: 1;
`,Ai=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
`,F1=u.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${g.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`,Tr=u.div`
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
`;class _1 extends I.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}canBoost(){const{ship:e,system:n}=this.props;return n.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,n)}canDeBoost(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(n)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){const{ship:e,system:n}=this.props,r=shipManager.power.getBoost(n),i=n.active;return a.jsxs(fg,{children:[a.jsx(gg,{children:"Power Capacitor"}),n.boostable&&a.jsxs(Ac,{children:[a.jsx(Pi,{children:"Open Petals"}),a.jsxs(Ai,{children:[a.jsx(Tr,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:r===0,children:"OFF"}),a.jsx(Tr,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:r>0,$variant:"activate",children:"ON"})]})]}),a.jsxs(Ac,{children:[a.jsx(Pi,{children:"Double Recharge"}),a.jsxs(Ai,{children:[a.jsx(Tr,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!i,children:"OFF"}),a.jsx(Tr,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:i,$variant:"activate",children:"ON"})]})]})]})}}const mg=u(fg)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`,Se={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",hover:"rgba(75, 43, 81, 0.6)",text:"#f2f2f2",textDim:"#d8b9e6"},H1=u(mg)`
    background-color: ${Se.surface};
    border: 1px solid ${Se.line};
`,W1=u.div`
    padding: 3px;
    background-color: ${Se.line};
    border: 1px solid ${Se.accent};
    color: ${Se.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,xg=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 8px;
    border-bottom: 1px solid ${Se.line};
    font-size: 11px;
    color: ${Se.text};

    &:hover {
        background-color: ${Se.hover};
    }
`,U1=u.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${Se.textDim};
`,Ts=u.div`
    min-width: 24px;
    width: ${t=>t.$wide?"auto":"24px"};
    padding: ${t=>t.$wide?"0 6px":"0"};
    height: 18px;
    box-sizing: border-box;
    background: ${Se.line};
    border: 1px solid ${Se.accent};
    color: ${Se.text};
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
        background: ${Se.accent};
        border: 1px solid #a06daa;
        color: #ffffff;
        opacity: 1;
        cursor: default;
        &:hover { background: ${Se.accent}; border: 1px solid #a06daa; }
    `}

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: ${Se.textDim}; border: 1px solid ${Se.accent}; }
    `}
`;class G1 extends I.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}handlePower(e){const{ship:n,system:r}=this.props;if(!yg(n,r))return;const i=r.getAbductionOrder();r.setAbductionPowerLevel(r.getAbductionPowerLevel(i)+e)&&(webglScene.customEvent("SystemDataChanged",{ship:r.getOwningUnit()||n,system:r}),this.forceUpdate())}handleCancel(){const{ship:e,system:n}=this.props;gamedata.gamephase!==1||!gamedata.isMyShip(n.getOwningUnit()||e)||(weaponManager.removeFiringOrder(n.getOwningUnit()||e,n),this.forceUpdate())}renderMaintain(){const{system:e}=this.props,n=e.isMaintainingVortex(),r=e.chargesVortexUpkeep()?e.getVortexUpkeepCost():0;return a.jsxs(ie.Fragment,{children:[a.jsx(gg,{children:"Jump Point"}),a.jsxs(Ac,{children:[a.jsx(Pi,{children:"Maintain Vortex"}),a.jsxs(Ai,{children:[a.jsx(Tr,{onClick:()=>this.handleDeactivate(),disabled:!e.canDeactivate(),$active:!n,children:"OFF"}),a.jsx(Tr,{onClick:()=>this.handleActivate(),disabled:!e.canActivate(),$active:n,$variant:"activate",children:"ON"})]})]}),a.jsx(F1,{children:r>0?n?"Held open. "+r+" power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.":"Closes at end of turn unless maintained. Costs "+r+" power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.":n?"Held open. All powered systems except the Scanner are shut down this turn.":"Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner."})]})}renderAbduction(){const{ship:e,system:n}=this.props,r=n.getAbductionOrder(),i=gamedata.getShip(r.targetid),o=n.getAbductionPowerLevel(r),s=yg(e,n),l=n.isExtraDimensional(),c=n.abductionMaxPower||1,d=window.JumpEngine.formatAbductionHalves(n.getAbductionHalves(r)),h=window.JumpEngine.getAbductionChain(r.targetid),p=window.JumpEngine.getAbductionCostPreview(r.targetid),f=n.isAbductionPowered(r);let v;if(f)v=d+" power-turn"+(d==="1"?"":"s")+" for "+n.getAbductionPowerDraw()+" power. So far "+window.JumpEngine.formatAbductionHalves(h.total)+" of "+h.cost+" power-turns.";else{const x=!!i&&gamedata.isTerrain(i.shipSizeClass,i.userid),b=!!i&&((i.hexOffsets||[]).length>0||i.Huge>0);v=(x?"Targeting: takes hold if "+(b?"every hex it occupies is":"it is")+" inside your connected field and your OEW beats its DEW.":"Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.")+" Power can be applied from next turn"+(p!==null?", and "+p+" power-turns will abduct it.":".")}return a.jsxs(H1,{children:[a.jsxs(W1,{children:["Abduction",i?": "+i.name:""]}),f&&a.jsxs(xg,{children:[a.jsx(Pi,{children:l?"Power":"Double power"}),l?a.jsxs(Ai,{children:[a.jsx(Ts,{onClick:()=>this.handlePower(-1),disabled:!s||o<=1,children:"-"}),a.jsx(Ts,{$active:!0,children:"x"+o}),a.jsx(Ts,{onClick:()=>this.handlePower(1),disabled:!s||o>=c,children:"+"})]}):null]}),gamedata.gamephase===1&&gamedata.isMyShip(n.getOwningUnit()||e)&&a.jsxs(xg,{children:[a.jsx(Pi,{children:"Declaration"}),a.jsx(Ai,{children:a.jsx(Ts,{$wide:!0,onClick:()=>this.handleCancel(),children:"CANCEL"})})]}),a.jsx(U1,{children:v})]})}render(){const{system:e}=this.props,n=e.canMaintainVortex()||e.canDeactivate(),r=typeof e.getAbductionOrder=="function"&&!!e.getAbductionOrder();return a.jsxs(ie.Fragment,{children:[n&&a.jsx(mg,{children:this.renderMaintain()}),r&&this.renderAbduction()]})}}const yg=(t,e)=>gamedata.gamephase===1&&gamedata.isMyShip(e.getOwningUnit()||t)&&e.isExtraDimensional()&&e.isAbductionPowered(),Ze={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",text:"#f2f2f2"},V1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${t=>t.$isWeapon||t.$isPurple?Ze.surface:"rgba(16, 26, 38, 0.9)"};
    border: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Ze.line:g.colors.line};
`,Y1=u.div`
    padding: 3px;
    background-color: ${t=>t.$isWeapon?"#571616":t.$isPurple?Ze.line:"#215a7a"};
    border: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Ze.line:g.colors.line};
    border-bottom: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Ze.line:g.colors.line};
    color: ${t=>t.$isWeapon||t.$isPurple?Ze.text:g.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,K1=u.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid ${t=>t.$isPurple?Ze.line:"#496791"};
    font-size: 12px;
    color: ${t=>t.$isPurple?Ze.text:"#deebff"};
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;u.div`
    flex: 1;
    margin-right: 10px;    
`;const Q1=u.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,wg=u.div`
    flex: 1;
    height: 18px;
    background: ${t=>t.$isPurple?Ze.line:"#203348"};
    border: 1px solid ${t=>t.$isPurple?Ze.accent:"#496791"};
    color: ${t=>t.$isPurple?Ze.text:"#deebff"};
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
        &:hover { background: #4b2b51; color: #d8b9e6; border: 1px solid ${Ze.accent}; }
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
`;class X1 extends I.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivateAll(e){e.preventDefault();const{ship:n,system:r}=this.props;let i=[];n.flight?i=n.systems.map(s=>s.systems).reduce((s,l)=>s.concat(l),[]):i=n.systems;let o=!1;i.forEach(s=>{!s.name||s.name!==r.name||s.canActivate&&typeof s.canActivate=="function"&&s.canActivate()&&(s.doActivate(),o=!0)}),o&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(e){e.preventDefault();const{ship:n,system:r}=this.props;let i=[];n.flight?i=n.systems.map(s=>s.systems).reduce((s,l)=>s.concat(l),[]):i=n.systems;let o=!1;i.forEach(s=>{!s.name||s.name!==r.name||s.canDeactivate&&typeof s.canDeactivate=="function"&&s.canDeactivate()&&(s.doDeactivate(),o=!0)}),o&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate=="function"&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate=="function"&&this.props.system.canDeactivate()}render(){const{ship:e,system:n}=this.props,r=n.active||n.weapon&&!n.activationIsToggle&&weaponManager.hasFiringOrder(e,n),i=typeof n.getActivateLabel=="function"?n.getActivateLabel():null,o=typeof n.getDeactivateLabel=="function"?n.getDeactivateLabel():null,s=i||(n.weapon?"Fire":"Activate"),l=o||(n.weapon?"Don't Fire":"Deactivate"),c=!!n.singleActivationButton,d=!c||this.canActivate(),h=(!n.weapon||n.activationIsToggle)&&(!c||this.canDeactivate()),p=!!n.activationMenuPurple&&!n.weapon;return a.jsxs(V1,{$isWeapon:n.weapon,$isPurple:p,children:[a.jsx(Y1,{$isWeapon:n.weapon,$isPurple:p,children:n.displayName}),a.jsx(K1,{$isPurple:p,children:a.jsxs(Q1,{children:[d&&a.jsx(wg,{onClick:()=>this.handleActivate(),onContextMenu:f=>this.handleActivateAll(f),disabled:!this.canActivate(),$active:r,$variant:"activate",$isWeapon:n.weapon,$isToggle:!!n.activationIsToggle,$isPurple:p,children:s}),h&&a.jsx(wg,{onClick:()=>this.handleDeactivate(),onContextMenu:f=>this.handleDeactivateAll(f),disabled:!this.canDeactivate(),$active:!r,$variant:"deactivate",$isPurple:p,children:l})]})})]})}}const J1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,q1=u.div`
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
`,Rc=u.div`
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
`,Ic=u.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,Ms=u.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,Z1=u.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`,ln=u.div`
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
`;class eb extends I.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(e){e.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){const{ship:e,system:n}=this.props;for(;shipManager.power.getBoost(n)>0;)shipManager.power.clickMinus(e,n);shipManager.power.onOfflineClicked(e,n),this.handleUpdate()}}handleOfflineAll(e){if(e.preventDefault(),this.canOffline()){const{ship:n,system:r}=this.props;for(;shipManager.power.getBoost(r)>0;)shipManager.power.clickMinus(n,r);shipManager.power.offlineAll(n,r),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system})}canOffline(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&(n.canOffLine||n.powerReq>0)&&!n.powerLocked&&!shipManager.power.isOffline(e,n)&&!weaponManager.hasFiringOrder(e,n)}canOnline(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(e,n)&&!shipManager.power.isForcedOffline(e,n)&&!shipManager.power.isVortexLockedOffline(e,n)}canBoost(){const{ship:e,system:n}=this.props;return n.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,n)&&(!n.isScanner()||n.id==shipManager.power.getHighestSensorsId(e))&&n.name!=="ThirdspaceShieldGenerator"&&n.name!=="powerCapacitor"&&n.name!=="PowerCapacitor"}canDeBoost(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(n)&&n.name!=="ThirdspaceShieldGenerator"&&n.name!=="powerCapacitor"&&n.name!=="PowerCapacitor"}canOverload(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(e,n)&&n.weapon&&n.overloadable&&!shipManager.power.isOverloading(e,n)}canStopOverload(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&n.weapon&&n.overloadable&&shipManager.power.isOverloading(e,n)&&(n.overloadshots>=n.extraoverloadshots||n.overloadshots==0)}render(){const{ship:e,system:n}=this.props,r=this.canOffline()||this.canOnline(),i=n.boostable&&(this.canBoost()||this.canDeBoost()),o=n.overloadable&&(this.canOverload()||this.canStopOverload());if(!r&&!i&&!o)return null;const s=shipManager.power.isOffline(e,n),l=shipManager.power.getBoost(n),c=shipManager.power.isOverloading(e,n),d=n.name==="reactor",h=n.name==="jumpEngine",p=h&&typeof n.getChargeBoostMax=="function"&&n.getChargeBoostMax()>0;let f="Boost Level";return d&&(f="Self-Destruct"),h&&(f=p?"Extra Charging":"Jump to Hyperspace"),a.jsxs(J1,{children:[a.jsx(q1,{children:"Power Settings"}),r&&a.jsxs(Rc,{children:[a.jsx(Ic,{children:"Power"}),a.jsxs(Ms,{children:[a.jsx(ln,{onClick:()=>this.handleOnline(),onContextMenu:v=>this.handleOnlineAll(v),disabled:!this.canOnline(),$active:!s,$variant:"activate",children:"On"}),a.jsx(ln,{onClick:()=>this.handleOffline(),onContextMenu:v=>this.handleOfflineAll(v),disabled:!this.canOffline(),$active:s,$variant:"deactivate",children:"Off"})]})]}),i&&a.jsxs(Rc,{children:[a.jsx(Ic,{children:f}),d||h&&!p?a.jsxs(Ms,{children:[a.jsx(ln,{onClick:()=>this.handleBoost(),disabled:l>0||!this.canBoost(),$active:l>0,$variant:d?"deactivate":"risk",children:"Yes"}),a.jsx(ln,{onClick:()=>this.handleDeBoost(),disabled:l===0||!this.canDeBoost(),$active:l===0,$variant:"activate",children:"No"})]}):a.jsxs(Ms,{children:[a.jsx(ln,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:"-"}),a.jsx(Z1,{children:l}),a.jsx(ln,{onClick:()=>this.handleBoost(),$narrow:!0,children:"+"})]})]}),o&&a.jsxs(Rc,{children:[a.jsx(Ic,{children:"Overcharge"}),a.jsxs(Ms,{children:[a.jsx(ln,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:c,$variant:"warning",children:"Yes"}),a.jsx(ln,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!c,$variant:"deactivate",children:"No"})]})]})]})}}const tb=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,nb=u.div`
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
`,rb=u.div`
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
`,vg=u.div`
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
`,ib=u.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,ob=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,bg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,sb=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,ab=u.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,lb=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,cb=u.div`
    width: 20px;
    text-align: center;
`,Oc=u.div`
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
`;u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class db extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrShipType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrShipType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene,l=!!(r.hasMultiTarget&&r.hasMultiTarget()),c=l?r.getCurrWeaponId():null,d=l?r.getMineWeapons():[],h=l?d.find($=>String($.id)===String(c)):null;if(l&&!h)return;r.setCurrShipType(e);const p=r.rangeSetting||r.range,f=l?r.allocatedRanges[c]:r.allocatedRanges;if(!f)return;const v=f[e]===null||f[e]===void 0?p:f[e];var x=[];for(var b in i.ships){var M=i.ships[b];if(M.userid==n.userid&&!o.isDestroyed(M)){if(M.phpclass&&n.phpclass){if(M.phpclass!=n.phpclass)continue}else if(M.shipClass!=n.shipClass)continue;for(var w in M.systems){var m=M.systems[w];if(m&&m.name===r.name){var y=!!(m.hasMultiTarget&&m.hasMultiTarget());y===l&&x.push({unit:M,ctrl:m})}}}}for(var S=0;S<x.length;S++){var T=x[S],m=T.ctrl;if(l){m.ensureMultiAllocatedShape&&m.ensureMultiAllocatedShape();var C=m.getMineWeapons(),k=C.find(he=>he.displayName===h.displayName&&he.indexInGroup===h.indexInGroup);if(!k)continue;m.setCurrWeaponId(k.id)}m.setCurrShipType(e);let D=0;const E=()=>l?m.allocatedRanges[m.getCurrWeaponId()]:m.allocatedRanges,H=()=>m.range||m.rangeSetting,K=()=>{const he=E();if(!he)return H();const ut=he[e];return ut??H()};for(;K()<v&&m.canIncrease()&&D<100;)m.doIncrease(),D++;for(;K()>v&&m.canDecrease()&&D<100;)m.doDecrease(),D++;D>=100&&console.warn("Mine Settings Propagation safety break for",m)}s.customEvent("SystemDataChanged",{ship:n,system:r})}cycleWeapon(e){const{system:n}=this.props;if(!n.hasMultiTarget||!n.hasMultiTarget())return;const r=n.getMineWeapons();if(r.length===0)return;const i=n.getCurrWeaponId();let o=r.findIndex(l=>String(l.id)===String(i));o<0&&(o=0);const s=(o+e+r.length)%r.length;n.setCurrWeaponId(r[s].id),this.forceUpdate()}render(){const{system:e}=this.props;if(!e||(e.range=e.range||e.rangeSetting,!e.range))return null;const n=!!(e.hasMultiTarget&&e.hasMultiTarget());let r=[],i=null,o=null,s=e.allocatedRanges||{};n?(e.ensureMultiAllocatedShape&&e.ensureMultiAllocatedShape(),r=e.getMineWeapons(),i=e.getCurrWeaponId(),o=r.find(x=>String(x.id)===String(i))||r[0]||null,s=i!=null&&e.allocatedRanges[i]?e.allocatedRanges[i]:{}):(e.ensureFlatAllocatedShape&&e.ensureFlatAllocatedShape(),s=e.allocatedRanges||{});const l=Object.keys(s),c=e.validTargets||l,d=x=>{if(!c.includes(x))return"N/A";const b=s[x];return b??e.range},h=x=>c.includes(x)?d(x)<e.range:!1,p=x=>c.includes(x)?d(x)>0:!1,f=x=>!!c.includes(x),v=n&&r.length>0?a.jsxs(rb,{children:[a.jsx(vg,{onClick:()=>this.cycleWeapon(-1),disabled:r.length<2,title:"Previous weapon",children:"<"}),a.jsx(ib,{children:o?o.label:""}),a.jsx(vg,{onClick:()=>this.cycleWeapon(1),disabled:r.length<2,title:"Next weapon",children:">"})]}):a.jsx(nb,{children:"Set Mine Range"});return a.jsxs(tb,{children:[v,a.jsxs(ob,{ref:this.listRef,children:[l.map(x=>a.jsxs(bg,{children:[a.jsx(sb,{src:`./img/systemicons/BFCPclasses/${x}.png`,alt:x}),a.jsx(ab,{children:x}),a.jsxs(lb,{onWheel:b=>{b.deltaY<0&&h(x)?this.handleIncrease(x):b.deltaY>0&&p(x)&&this.handleDecrease(x)},children:[a.jsx(Oc,{onClick:()=>this.handleDecrease(x),disabled:!p(x),children:"-"}),a.jsx(cb,{children:d(x)}),a.jsx(Oc,{onClick:()=>this.handleIncrease(x),disabled:!h(x),children:"+"}),a.jsx(Oc,{title:n?"Propagate this weapon's settings to same-class mines with Multiple Targets":"Propagate to all mines of same type",onClick:()=>this.handlePropagate(x),disabled:!f(x),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},x)),l.length===0&&a.jsx(bg,{children:"No ship types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Max Range: ",e.range]})]})}}const ub=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,hb=u.div`
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
`,pb=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Sg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,fb=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,gb=u.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,mb=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,xb=u.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${t=>t.$active?"#4CAF50":"#F44336"};
`,kg=u.div`
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
`;class yb extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleToggle(e){const{system:n}=this.props;n.setCurrShipType(e),n.canSet()?(n.doSet(),this.forceUpdate()):n.canUnset()&&(n.doUnset(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating Mine settings for:",e),r.setCurrShipType(e);const l=r.allocatedShipTypes[e];var c=[];for(var d in i.ships){var h=i.ships[d];if(h.userid==n.userid&&!o.isDestroyed(h))for(var p=0;p<h.systems.length;p++){var f=h.systems[p];if(h.shipClass==n.shipClass&&f.name===r.name){c.push(f);break}}}console.log("Found Mine Weapons of same type:",c.length);for(var v=0;v<c.length;v++){var f=c[v];f.setCurrShipType(e);let b=0;for(;f.allocatedShipTypes[e]!==l&&(l?f.canSet():f.canUnset())&&b<10;)l?f.doSet():f.doUnset(),b++;b>=10&&console.warn("Mine Settings Propagation safety break for",f)}s.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{system:e}=this.props;if(!e)return null;const n=e.allocatedShipTypes||{},r=Object.keys(n),i=s=>n[s]?"YES":"NO",o=s=>{const l=Number(this.props.ship.spawned),c=l===-1?1:l+1;return window.gamedata.turn===c};return a.jsxs(ub,{children:[a.jsx(hb,{children:"Set Target Types"}),a.jsxs(pb,{ref:this.listRef,children:[r.map(s=>a.jsxs(Sg,{children:[a.jsx(fb,{src:`./img/systemicons/BFCPclasses/${s}.png`,alt:s}),a.jsx(gb,{children:s}),a.jsxs(mb,{children:[a.jsx(xb,{$active:n[s],children:i(s)}),a.jsx(kg,{onClick:()=>this.handleToggle(s),disabled:!o(),style:{marginLeft:"5px"},children:"Toggle"}),a.jsx(kg,{title:"Propagate to all mines of same type",onClick:()=>this.handlePropagate(s),disabled:!1,style:{marginLeft:"5px",width:"16px",padding:"0"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},s)),r.length===0&&a.jsx(Sg,{children:"No ship types available"})]})]})}}const Lc=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: ${g.colors.greenBg};
    border: 1px solid ${g.colors.greenLine};
`,$g=u.div`
    padding: 3px;
    background-color: ${g.colors.greenTitleBg};
    border: 1px solid ${g.colors.greenLine};
    color: ${g.colors.greenText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,wb=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: ${g.colors.greenTitleBg};
    border: 1px solid ${g.colors.greenLine};
    color: ${g.colors.greenText};
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 2px;
    min-width: 220px;
`,Cg=u.div`
    width: 20px;
    height: 18px;
    background: ${g.colors.greenBtnBg};
    border: 1px solid ${g.colors.greenBtnLine};
    color: ${g.colors.greenText};
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: ${g.colors.greenBtnLine};
        border: 1px solid ${g.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${g.colors.greenBtnBg}; border: 1px solid ${g.colors.greenBtnLine}; color: ${g.colors.greenText}; }
    `}
`,vb=u.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;u.div`
    text-align: center;
    color: ${g.colors.greenLabel};
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;const Ri=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,Tg=u.div`
    width: 70px;
    font-size: 10px;
    color: ${g.colors.greenLabel};
    user-select: none;
`,An=u.div`
    flex: 1;
    height: 20px;
    background: ${g.colors.greenBtnBg};
    border: 1px solid ${g.colors.greenBtnLine};
    color: ${g.colors.greenText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.95;
    user-select: none;

    &:hover {
        background: ${g.colors.greenBtnLine};
        border: 1px solid ${g.colors.greenBtnLineHover};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.$active&&`
        background: ${g.colors.greenBtnLine};
        border: 1px solid ${g.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${g.colors.greenGlow};
        color: #ffffff;
        opacity: 1;
    `}

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${g.colors.greenBtnBg}; border: 1px solid ${g.colors.greenBtnLine}; color: ${g.colors.greenText}; }
    `}
`;class bb extends I.Component{refresh(){const{ship:e,system:n}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n})}cycleMode(e){const{system:n}=this.props;if(!gamedata.isMyShip(this.props.ship))return;const r=n.firingMode==1?2:1;n.setFiringMode(r),typeof n.initializationUpdate=="function"&&n.initializationUpdate(),this.refresh()}activateMode1(){const{ship:e,system:n}=this.props;n.canActivate()&&(n.doActivate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n}),webglScene.customEvent("CloseSystemInfo"))}targetWarrior(){const{ship:e,system:n}=this.props;weaponManager.isSelectedWeapon(n)||weaponManager.selectWeapon(e,n),webglScene.customEvent("SystemDataChanged",{ship:e,system:n}),webglScene.customEvent("CloseSystemInfo")}setRotation(e,n){const{system:r}=this.props;r.rotationDirection=e,r.rotationAmount=n,typeof r.updateRotationNotes=="function"&&r.updateRotationNotes(),this.refresh()}renderInitialOrders(){const{system:e}=this.props,n=parseInt(e.firingMode,10),r=e.firingModes[n]||"";return a.jsxs(Lc,{children:[a.jsxs(wb,{children:[a.jsx(Cg,{onClick:()=>this.cycleMode(-1),title:"Previous mode",children:"<"}),a.jsx(vb,{children:r}),a.jsx(Cg,{onClick:()=>this.cycleMode(1),title:"Next mode",children:">"})]}),n==1&&a.jsx(Ri,{children:a.jsx(An,{onClick:()=>this.activateMode1(),children:"Activate"})}),n==2&&a.jsx(Ri,{children:a.jsx(An,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(e),children:"Target friendly Warrior"})})]})}engageMode3(){const{ship:e,system:n}=this.props;gamedata.isMyShip(e)&&(weaponManager.hasFiringOrder(e,n)||(n.setFiringMode(3),typeof n.initializationUpdate=="function"&&n.initializationUpdate(),this.refresh()))}renderPreFiring(){const{system:e}=this.props;if(e.firingMode!=3)return a.jsxs(Lc,{children:[a.jsx($g,{children:"Gravitic Augmenter"}),a.jsx(Ri,{children:a.jsx(An,{onClick:()=>this.engageMode3(),children:"Engage Gravity Shifting"})})]});const n=e.rotationDirection||1,r=e.rotationAmount||1,i=(o,s)=>n==o&&r==s;return a.jsxs(Lc,{children:[a.jsx($g,{children:"Gravity Shift Settings"}),a.jsxs(Ri,{children:[a.jsx(Tg,{children:"Clockwise"}),a.jsx(An,{onClick:()=>this.setRotation(1,1),$active:i(1,1),children:"60°"}),a.jsx(An,{onClick:()=>this.setRotation(1,2),$active:i(1,2),children:"120°"})]}),a.jsxs(Ri,{children:[a.jsx(Tg,{children:"Anti-Clockwise"}),a.jsx(An,{onClick:()=>this.setRotation(2,1),$active:i(2,1),children:"60°"}),a.jsx(An,{onClick:()=>this.setRotation(2,2),$active:i(2,2),children:"120°"})]})]})}render(){const{system:e}=this.props;return e?gamedata.gamephase==1?(e.firingMode==3&&(e.setFiringMode(1),typeof e.initializationUpdate=="function"&&e.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}}const js=3,Sb=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,kb=u.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,$b=u.div`
    text-align: center;
    color: ${t=>t.$empty?"#f0a0a0":"#f2f2f2"};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,Cb=u.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,Tb=u.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,Mg=u.div`
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
`,Mb=u.input`
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
`;u.div`
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
`;const Rn=[{key:"hitBoost5",label:"Hit Chance",increment:5,prefix:"+",suffix:"%",display:t=>t*2,parse:t=>t/2},{key:"shotBoost",label:"Shots",increment:1,prefix:"+",suffix:"",display:t=>t,parse:t=>t},{key:"dmgBoost5",label:"Damage",increment:5,prefix:"+",suffix:"",display:t=>t,parse:t=>t}],jb=(t,e)=>t.prefix+t.display(e|0)+t.suffix;class Eb extends I.Component{fieldSteps(e,n){return(n|0)/e.increment}allocatedSteps(e){return Rn.reduce((n,r)=>n+this.fieldSteps(r,e[r.key]),0)}maxSteps(){const{ship:e,system:n}=this.props;return typeof n.getMaxSteps=="function"?n.getMaxSteps(e):Math.floor(shipManager.movement.getRemainingEngineThrust(e)/js)}totalShots(e){return(e.guns|0)+(e.shotBoost|0)}maxDamageSteps(e){return this.totalShots(e)}clampDamageToShots(e){const n=Rn.find(i=>i.key==="dmgBoost5"),r=this.maxDamageSteps(e)*n.increment;(e.dmgBoost5|0)>r&&(e.dmgBoost5=r)}refresh(){const{ship:e,system:n}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n})}setValue(e,n){const{ship:r,system:i}=this.props;if(!gamedata.isMyShip(r))return;let o=Math.max(0,Math.round((n|0)/e.increment)*e.increment);const s=this.allocatedSteps(i)-this.fieldSteps(e,i[e.key]),l=Math.max(0,this.maxSteps()-s);if(o/e.increment>l&&(o=l*e.increment),e.key==="dmgBoost5"){const d=this.maxDamageSteps(i)*e.increment;o>d&&(o=d)}i[e.key]=o,e.key==="shotBoost"&&this.clampDamageToShots(i),typeof i.updateBoostNotes=="function"&&i.updateBoostNotes(),this.syncFlight(i),this.refresh()}syncFlight(e){const n=this.getFlightPulsars();for(let r=0;r<n.length;r++){const i=n[r];i!==e&&(Rn.forEach(o=>{i[o.key]=e[o.key]|0}),typeof i.updateBoostNotes=="function"&&i.updateBoostNotes())}}step(e,n){const{system:r}=this.props;this.setValue(e,(r[e.key]|0)+n*e.increment)}onWheel(e,n){n.preventDefault(),this.step(e,n.deltaY<0?1:-1)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,""),i=r===""?0:parseInt(r,10);this.setValue(e,e.parse(i))}propagate(){const{ship:e,system:n}=this.props;if(!gamedata.isMyShip(e))return;const r=this.getFlightPulsars();for(let i=0;i<r.length;i++){const o=r[i];o!==n&&(Rn.forEach(s=>{o[s.key]=n[s.key]|0}),this.clampWeaponToBudget(o),typeof o.updateBoostNotes=="function"&&o.updateBoostNotes())}this.refresh()}getFlightPulsars(){const{ship:e}=this.props,n=[],r=e&&e.systems?e.systems:[];for(let i=0;i<r.length;i++){const o=r[i]&&r[i].systems?r[i].systems:[];for(let s=0;s<o.length;s++)o[s]&&o[s].name==="MinorThoughtPulsar"&&n.push(o[s])}return n}clampWeaponToBudget(e){const n=["dmgBoost5","shotBoost","hitBoost5"],r=o=>Rn.find(s=>s.key===o);let i=0;for(;i++<200&&!(Rn.reduce((s,l)=>s+(e[l.key]|0)/l.increment,0)<=this.maxSteps());)for(let s=0;s<n.length;s++){const l=n[s];if((e[l]|0)>0){e[l]=(e[l]|0)-r(l).increment;break}}}render(){const{ship:e,system:n}=this.props;if(!n)return null;const r=typeof n.getSpareThrust=="function"?n.getSpareThrust(e):shipManager.movement.getRemainingEngineThrust(e),i=this.allocatedSteps(n)*js,o=Math.max(0,r-i),s=o>=js;return a.jsxs(Sb,{children:[a.jsx(kb,{children:"Minor Thought Pulsar"}),a.jsxs($b,{$empty:o<js,children:["Available thrust: ",o]}),Rn.map(l=>{const c=n[l.key]|0,d=l.key==="dmgBoost5"&&c/l.increment>=this.maxDamageSteps(n);return a.jsxs(Cb,{children:[a.jsx(Tb,{children:l.label}),a.jsx(Mg,{title:"Less",disabled:c<=0,onClick:()=>this.step(l,-1),children:"−"}),a.jsx(Mb,{type:"text",value:jb(l,c),onChange:h=>this.onInput(l,h),onWheel:h=>this.onWheel(l,h)}),a.jsx(Mg,{title:d?"One +5 per shot (add shots for more)":"More",disabled:!s||d,onClick:()=>this.step(l,1),children:"+"})]},l.key)})]})}}const Ii=t=>{let e=null;const n=r=>{r.preventDefault(),t(r)};return r=>{e!==r&&(e&&e.removeEventListener("wheel",n,{passive:!1}),e=r,e&&e.addEventListener("wheel",n,{passive:!1}))}},P={bg:"linear-gradient(180deg, rgb(27, 45, 62), rgb(18, 32, 45))",line:"#2a6b8f",radius:"6px",shadow:"0 8px 28px rgba(0, 0, 0, 0.6)",titleBg:"linear-gradient(180deg, rgb(54, 79, 110), rgb(36, 57, 80))",title:"#c6e2ff",text:"#deebff",dim:"#8ca5c0",btnBg:"#081420",btnText:"#deebff",well:"#000000",focus:"#8bcaf2"},W={enh:{rail:g.colors.enhLine,bar:"#c39a52",wash:g.colors.enhBg,title:g.colors.enhTitle,btnBg:"#292114",btnText:g.colors.enhTitle},damage:{rail:"#2f7f92",bar:"#4cb8d0",wash:"rgba(58, 159, 181, 0.26)",title:"#e0f5fa",btnBg:"#0f262d",btnText:"#b6e3ee"},crit:{rail:"#a85c33",bar:"#dd7643",wash:"rgba(168, 92, 51, 0.34)",title:"#ffe8dc",btnBg:"#291914",btnText:"#eab99e"},officer:{rail:"#5d79a8",bar:"#90b1ee",wash:"rgba(144, 177, 238, 0.24)",title:"#ffffff",btnBg:"#141b29",btnText:"#b8cdf2"}},Db={rail:P.line,bar:P.line,btnBg:P.btnBg,btnText:P.btnText},Pt=t=>t.$ink||Db,Pb=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px 4px 10px;
    font-size: 11px;
    color: ${t=>t.$gold?g.colors.enhText:P.text};
`,Ab=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,Rb=u.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;u.span`
    flex: 0 0 auto;
    color: ${t=>t.$gold?g.colors.enhText:P.dim};
    font-size: 10px;
    opacity: ${t=>t.$gold?.75:1};
`;const Mr=u.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${t=>Pt(t).btnBg};
    border: 1px solid ${t=>Pt(t).rail};
    border-radius: 2px;
    color: ${t=>Pt(t).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${t=>Pt(t).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${Pt(t).btnBg};
            color: ${Pt(t).btnText};
        }
    `}
`,jg=u.input`
    flex: 0 0 44px;
    width: 44px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${g.fonts.mono};
    font-size: 12px;
    color: ${t=>t.$destroyed?"#ff8a80":"#ffffff"};
    background-color: ${P.well};
    border: 1px solid ${t=>Pt(t).rail};
    border-radius: 2px;
    outline: none;

    &:focus {
        border-color: ${t=>Pt(t).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,Eg=u.div`
    padding: 6px 10px 5px;
    background: ${P.titleBg};
    border-bottom: 1px solid ${P.line};
    color: ${P.title};
    text-align: left;
    font-family: ${g.fonts.display};
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    user-select: none;
    ${t=>t.$sticky?"position: sticky; top: 0; z-index: 1;":""}
`,Es=t=>`
    color: ${t.title};
    background-color: ${t.wash};
    background-image: linear-gradient(to right, ${t.wash}, rgba(0, 0, 0, 0) 75%);
    border-left: 3px solid ${t.bar};
    border-top: 1px solid ${t.rail};
    border-bottom: 1px solid ${t.rail};
`,Ds=u.div`
    padding: 5px 10px 4px 7px;
    text-align: left;
    font-family: ${g.fonts.display};
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
`,Ps=u.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 3px 0 0 ${t=>Pt(t).bar};
`,Ib=u(Ds)`
    ${Es(W.enh)}
`,Ob=u(Ds)`
    ${Es(W.officer)}
`,Lb=u(Ds)`
    ${Es(W.damage)}
`,zb=u(Ds)`
    ${Es(W.crit)}
`,Dg=u.div`
    height: 2px;
    background-color: ${t=>t.$chrome?P.line:g.colors.enhLine};
    opacity: 0.8;
`,Bb=u.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,Nb=zb,Fb=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${g.colors.warningSoft};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${t=>t.$empty&&`
        color: #6f6257;
    `}
`,_b=u.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,Hb=u.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${P.dim};
`,Wb=u.div`
    flex: 0 0 auto;
    color: ${P.dim};
`,Ub=u.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,Gb=u.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${g.fonts.mono};
    font-size: 11px;
    color: ${t=>t.$empty?"#6f6257":"#ffffff"};
`,Vb=u.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,Yb=u.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,Kb=u.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px 6px 10px;
`,Qb=u.select`
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
    color: ${P.text};
    /*the lobby's own input fill (.lb-input), not the number well*/
    background-color: ${P.btnBg};
    /*Takes the section's ink like the tickers above it - it is the widest control in the
      section, so leaving it on the chassis border was the one thing that still read as
      unpainted once the tickers went rust.*/
    border: 1px solid ${W.crit.rail};
    border-radius: 2px;
    outline: none;
    cursor: pointer;

    &:focus { border-color: ${W.crit.btnText}; }
    &:disabled { cursor: default; }
`;u.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${P.dim};
    cursor: pointer;
    user-select: none;
`;const Pg=(t,e,n,r)=>{const i=[];for(const o in t||{}){if(!t.hasOwnProperty(o))continue;const s=battleDamage.PARAM_CRITICALS[o],l=parseInt((r||{})[o],10)||0;i.push({type:o,isParam:!!s,paramLabel:s?s.label:null,label:battleDamage.critLabel(o,e,l),count:parseInt(t[o],10)||0,param:l,transient:!!(n&&n[o])})}return i};class Ag extends I.Component{constructor(e){super(e),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(e){e.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Ii(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}critMap(){const e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.c?Object.assign({},e.c):{}}paramMap(){const e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.p?Object.assign({},e.p):{}}valueOf(e){return e.isParam?e.param:e.count}maxValueOf(e){if(!e.isParam)return battleDamage.critLimit(e.type);const n=battleDamage.PARAM_CRITICALS[e.type];return Math.min(battleDamage.MAX_CRIT_PARAM,n&&n.max||battleDamage.MAX_CRIT_PARAM)}setValue(e,n){const{ship:r,kind:i,reference:o,onChange:s}=this.props,l=this.critMap(),c=this.paramMap();n>0?e.isParam?(l[e.type]=1,c[e.type]=Math.min(n,this.maxValueOf(e))):l[e.type]=Math.min(n,battleDamage.critLimit(e.type)):(delete l[e.type],delete c[e.type]),battleDamage.setCriticals(r,i,o,l,c),s&&s()}step(e,n){const r=this.rowForType(e);this.setValue(r,this.valueOf(r)+n)}rowForType(e){const{ship:n}=this.props,r=battleDamage.PARAM_CRITICALS[e],i=parseInt(this.paramMap()[e],10)||0;return{type:e,isParam:!!r,paramLabel:r?r.label:null,label:battleDamage.critLabel(e,n.preBattleCritDesc,i),count:parseInt(this.critMap()[e],10)||0,param:i,transient:!!(n.preBattleCritTransient&&n.preBattleCritTransient[e])}}displayRows(e){const{ship:n,kind:r,reference:i}=this.props;return battleDamage.rememberCriticals(n,r,i,(e||[]).map(s=>s.type)).map(s=>this.rowForType(s))}addableTypes(e){const{ship:n,kind:r,reference:i}=this.props,o=battleDamage.offerableCriticals(n,r,i,this.state.showAll),s={};return e.forEach(l=>{s[l]=!0}),o.filter(l=>!s[l]).map(l=>({type:l,label:this.pickerLabel(l)})).sort((l,c)=>l.label.localeCompare(c.label))}pickerLabel(e){const n=battleDamage.PARAM_CRITICALS[e];return n?n.label:battleDamage.critLabel(e,this.props.ship.preBattleCritDesc,0)}onAdd(e){e&&this.setValue(this.rowForType(e),1)}render(){const{ship:e,rows:n,editable:r}=this.props,i=r?this.displayRows(n):n||[],o=!!(r&&battleDamage.catalogueFor(e)),s=o?this.addableTypes(i.map(l=>l.type)):[];return!i.length&&!o?null:a.jsxs(Bb,{children:[a.jsx(Dg,{$chrome:!0}),a.jsx(Nb,{children:"Critical Effects"}),a.jsxs(Ps,{$ink:W.crit,children:[i.map(l=>{const c=this.valueOf(l),d=this.maxValueOf(l);return a.jsxs(Fb,{$empty:r&&c<=0,children:[a.jsxs(_b,{title:l.type,children:[r&&l.isParam?l.paramLabel:l.label,l.transient&&a.jsx(Hb,{children:"(turn 1 only)"})]}),r?a.jsxs(Ub,{children:[a.jsx(Mr,{$ink:W.crit,title:l.isParam?"Reduce":"One fewer",disabled:c<=0,onClick:()=>this.step(l.type,-1),children:"−"}),a.jsx(Gb,{$empty:c<=0,ref:this.wheelRef(l.type),children:c}),a.jsx(Mr,{$ink:W.crit,title:l.isParam?"Increase":c>=d&&d===1?"This effect only applies once":"One more",disabled:c>=d,onClick:()=>this.step(l.type,1),children:"+"})]}):l.count>1&&a.jsxs(Wb,{children:["(x",l.count,")"]})]},l.type)}),o&&a.jsx(Kb,{children:a.jsxs(Qb,{value:"",disabled:s.length===0,title:"Add a critical effect to this unit before the battle",onChange:l=>this.onAdd(l.target.value),children:[a.jsx("option",{value:"",children:s.length?"+ Add effect…":"Nothing to add"}),s.map(l=>a.jsx("option",{value:l.type,children:l.label},l.type))]})})]})]})}}const zc=u.div`
    display: flex;
    justify-content: flex-end;
    padding: 1px 8px 5px 10px;
    font-family: ${g.fonts.mono};
    font-size: 10px;
    color: ${t=>t.$color||g.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,Rg=W.officer.btnText,Xb=u(zc)`
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding-top: 2px;
    opacity: 1;
`,Jb=u.span`
    margin-left: auto;
    opacity: 0.85;
`,Ig=u.div`
    flex: 0 0 auto;
    height: 16px;
    padding: 0 8px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    background: ${W.officer.btnBg};
    border: 1px solid ${W.officer.rail};
    border-radius: 2px;
    color: ${W.officer.btnText};
    font-family: ${g.fonts.mono};
    font-size: 10px;
    cursor: pointer;
    user-select: none;

    &:hover {
        background: ${W.officer.rail};
        color: #ffffff;
    }

    ${t=>t.$disabled&&`
        opacity: 0.35;
        cursor: not-allowed;
        &:hover {
            background: ${W.officer.btnBg};
            color: ${W.officer.btnText};
        }
    `}
`,qb=u.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${g.fonts.mono};
    font-size: 10px;
    color: ${t=>t.$color||g.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${t=>t.$spent?.6:1};
`;class Og extends I.Component{constructor(e){super(e),this.wheelRef=Ii(n=>this.step(n.deltaY<0?1:-1))}step(e){const{row:n,onChange:r}=this.props;if(n.fixed)return;const i=Math.max(0,Math.min(n.max,n.count+e));i!==n.count&&r(n.enhID,i)}onInput(e){const{row:n,onChange:r}=this.props,i=String(e.target.value).replace(/[^0-9]/g,""),o=i===""?0:parseInt(i,10);r(n.enhID,Math.max(0,Math.min(n.max,o)))}render(){const{row:e}=this.props,n=e.count>=e.max,r=(e.max>1?`${e.label} - ${e.count}/${e.max} levels`+(e.count>0?`, ${e.price} pts spent`:"")+(n?"":`; next level ${e.nextPrice} pts`):`${e.label} - ${e.price||e.nextPrice} pts`)+(e.note?`
${e.note}`:"")+(e.blocked?`
${e.blocked}`:""),i=!!e.blocked&&e.count<1,o=e.officer?W.officer:W.enh,s=!!e.fixed,l=`${e.label} - bought with the ship, in its Officers section (Edit the ship to remove him). Move posts him elsewhere.`;return a.jsxs(Pb,{$gold:!e.officer,title:s?l:r,style:i?{opacity:.55}:void 0,children:[a.jsx(Ab,{children:a.jsx(Rb,{children:e.label})}),a.jsx(Mr,{$ink:o,title:s?l:"Remove a level",disabled:s||e.count<=0,onClick:()=>{s||this.step(-1)},children:"−"}),a.jsx(jg,{ref:this.wheelRef,$ink:o,type:"text",value:e.count,readOnly:s,onChange:c=>{s||this.onInput(c)}}),a.jsx(Mr,{$ink:o,title:s?l:i?e.blocked:e.count>=e.max?"Already at the maximum":`Add a level (${e.nextPrice} pts)`,disabled:s||i||e.count>=e.max,onClick:()=>{s||this.step(1)},children:"+"}),a.jsx(qb,{$spent:n,$color:e.officer?o.title:void 0,title:n?`Fully bought - ${e.price} pts`:"Cost of the next level",children:n?`${e.price}p`:`${e.nextPrice}p`})]})}}const Zb=u(Ig)`
    ${t=>t.$on&&`
        background: ${W.officer.bar};
        color: #0b1520;
        cursor: default;
        &:hover {
            background: ${W.officer.bar};
            color: #0b1520;
        }
    `}
`,eS=u.div`
    flex: 0 0 auto;
    display: flex;
    gap: 2px;
`;class tS extends I.Component{renderSides(e){const{onSide:n}=this.props;return typeof n!="function"?null:a.jsx(eS,{children:e.sides.map(r=>a.jsx(Zb,{$on:r.current,title:r.current?`The ${e.label} covers the ${r.name} weapons`:`Cover the ${r.name} weapons instead (free in Fleet Selection)`,onClick:i=>{i.stopPropagation(),r.current||n(e.enhID)},children:r.name},r.id))})}renderMove(e){const{onMove:n}=this.props;if(e&&Array.isArray(e.sides))return this.renderSides(e);if(!e||typeof n!="function")return null;const r=e.moveTo.length>0,i=r?`Move the ${e.label} to another post: click Move, then a highlighted system in the ship window (free in Fleet Selection)`:`There is no other post the ${e.label} can take on this ship right now`;return a.jsx(Ig,{$disabled:!r,title:i,onClick:o=>{o.stopPropagation(),r&&n(e.enhID)},children:"Move"})}render(){const{rows:e,onChange:n}=this.props;if(!e||e.length===0)return null;const r=e.filter(d=>!d.officer),i=e.filter(d=>d.officer),o=d=>d.reduce((h,p)=>h+(p.count>0?p.price:0),0),s=o(r),l=o(i),c=i.find(d=>d.count>0&&(Array.isArray(d.moveTo)||Array.isArray(d.sides)));return a.jsxs(ie.Fragment,{children:[r.length>0&&a.jsx(Ib,{children:"✦ ENHANCEMENTS"}),r.length>0&&a.jsxs(Ps,{$ink:W.enh,children:[r.map(d=>a.jsx(Og,{row:d,onChange:n},d.enhID)),s>0&&a.jsxs(zc,{children:["Refits: ",s," pts"]})]}),i.length>0&&a.jsx(Ob,{children:"Officers"}),i.length>0&&a.jsxs(Ps,{$ink:W.officer,children:[i.map(d=>a.jsx(Og,{row:d,onChange:n},d.enhID)),c?a.jsxs(Xb,{$color:Rg,children:[this.renderMove(c),l>0&&a.jsxs(Jb,{children:["Officers: ",l," pts"]})]}):l>0&&a.jsxs(zc,{$color:Rg,children:["Officers: ",l," pts"]})]})]})}}const nS=u.div`
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
    background: ${P.bg};
    border: 1px solid ${P.line};
    border-radius: ${P.radius};
    box-shadow: ${P.shadow};
    overflow: hidden;
`,rS=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px 5px 10px;
    font-size: 11px;
    color: ${P.text};
`,iS=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,oS=u.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${t=>t.$disabled?"not-allowed":"pointer"};
    user-select: none;
    opacity: ${t=>t.$disabled?.4:1};
    color: ${t=>t.$on?"#ff8a80":P.dim};
`,sS=u.span`
    flex: 0 0 auto;
    color: ${P.dim};
    font-size: 10px;
`,aS=u.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;class lS extends I.Component{constructor(e){super(e),this.wheelRef=Ii(n=>this.step(n.deltaY<0?1:-1))}entry(){const{ship:e,system:n}=this.props;return battleDamage.getEntry(e,battleDamage.KIND_SYSTEM,n.id)||{}}remaining(){const{system:e}=this.props,n=this.entry();return n.k?0:Math.max(0,e.maxhealth-(parseInt(n.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return this.isIndestructible()?1:0}setRemaining(e){const{ship:n,system:r}=this.props,i=r.maxhealth,o=Math.min(this.floor(),i);let s=parseInt(e,10);isNaN(s)&&(s=i),s=Math.max(o,Math.min(i,s));const l=i-s,c=s===0&&i>0;c&&this.rememberHealth(),battleDamage.setSystem(n,r.id,{d:l,k:c?1:0}),this.refresh()}rememberHealth(){const{ship:e,system:n}=this.props;this.isDestroyed()||battleDamage.rememberHealth(e,battleDamage.KIND_SYSTEM,n.id,this.remaining())}setDestroyed(e){const{ship:n,system:r}=this.props;if(e&&this.isIndestructible())return;if(e){this.rememberHealth(),battleDamage.setSystem(n,r.id,{d:r.maxhealth,k:1}),this.refresh();return}const i=battleDamage.healthMemory(n,battleDamage.KIND_SYSTEM,r.id),o=i>0?Math.min(r.maxhealth,i):r.maxhealth;battleDamage.setSystem(n,r.id,{d:r.maxhealth-o,k:0}),this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e);let n=[];if(window.systemEnhancements){const r=parseFloat(e.pointCostSysEnh)||0;n=systemEnhancements.dropDestroyed(e),n.length&&this.settleRefitCost(e,r)}if(window.officers&&typeof officers.dropDestroyedPosts=="function"){const r=officers.dropDestroyedPosts(e);r.length&&(n=n.concat(r),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet())}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate(),n.length&&window.confirm&&typeof confirm.warning=="function"&&confirm.warning(systemEnhancements.describeRemoved(n))}settleRefitCost(e,n){const r=parseFloat(e.pointCostSysEnh)||0;e.pointCost=(parseFloat(e.pointCost)||0)-(n-r),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet()}setEnhancement(e,n){const{ship:r,system:i}=this.props;if(!window.systemEnhancements)return;const o=parseFloat(r.pointCostSysEnh)||0,s=systemEnhancements.taken(r,i.id,e);if(n===s)return;if(systemEnhancements.set(r,i.id,e,n),this.settleRefitCost(r,o),!(!window.gamedata||typeof gamedata.canAffordRefit!="function"||gamedata.canAffordRefit(r))){const c=parseFloat(r.pointCostSysEnh)||0;systemEnhancements.set(r,i.id,e,s),this.settleRefitCost(r,c),systemEnhancements.apply(r),this.forceUpdate(),window.confirm&&typeof confirm.error=="function"&&confirm.error("You cannot afford that enhancement!",function(){});return}systemEnhancements.apply(r),this.refresh()}startMove(e){const{ship:n,system:r}=this.props;if(!window.officers)return;const i=officers.lobbyEntryAt(n,r.id,e);!i||officers.lobbyDestinations(n,i).length===0||(officers.startPick(n,i),window.uiEvents&&window.uiEvents.relay("CloseSystemInfo"))}switchSide(e){const{ship:n,system:r}=this.props;if(!window.officers)return;const i=officers.lobbyEntryAt(n,r.id,e);i&&officers.lobbySwitchSide(n,i)&&this.forceUpdate()}step(e){this.setRemaining(this.remaining()+e)}onInput(e){const n=String(e.target.value).replace(/[^0-9]/g,"");this.setRemaining(n===""?0:parseInt(n,10))}render(){const{ship:e,system:n}=this.props;if(!n||!(n.maxhealth>0))return null;const r=this.remaining(),i=this.isDestroyed(),o=this.isIndestructible(),s=this.entry(),l=Pg(s.c,e.preBattleCritDesc,e.preBattleCritTransient,s.p),c=window.systemEnhancements&&!i?systemEnhancements.menuRowsFor(e,n):[];return a.jsxs(nS,{onClick:d=>d.stopPropagation(),children:[a.jsx(tS,{rows:c,onChange:(d,h)=>this.setEnhancement(d,h),onMove:d=>this.startMove(d),onSide:d=>this.switchSide(d)}),c.length>0&&a.jsx(Dg,{}),a.jsx(Lb,{children:"Damage"}),a.jsx(Ps,{$ink:W.damage,children:a.jsxs(rS,{children:[a.jsxs(iS,{title:`${n.displayName||n.name} (system id ${n.id})`,children:[a.jsx(aS,{children:n.displayName||n.name}),a.jsxs(sS,{children:["#",n.id]})]}),a.jsx(Mr,{$ink:W.damage,title:o&&r<=1?"A reactor cannot be destroyed before the battle":"More damage",disabled:i||r<=this.floor(),onClick:()=>this.step(-1),children:"−"}),a.jsx(jg,{ref:this.wheelRef,$ink:W.damage,type:"text",$destroyed:i,disabled:i,value:i?0:r,onChange:d=>this.onInput(d)}),a.jsx(Mr,{$ink:W.damage,title:"Repair",disabled:i||r>=n.maxhealth,onClick:()=>this.step(1),children:"+"}),a.jsxs(oS,{$on:i,$disabled:o,title:o?"A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship":"Mark this system destroyed before the battle starts",children:[a.jsx(Vb,{type:"checkbox",checked:i,disabled:o,onChange:d=>this.setDestroyed(d.target.checked)}),a.jsx(Yb,{children:"Destroy"})]})]})}),a.jsx(Ag,{ship:e,kind:battleDamage.KIND_SYSTEM,reference:n.id,rows:l,editable:!0,onChange:()=>this.refresh()})]})}}const ct=W.officer,cS=u.div`
    display: flex;
    flex-direction: column;
    width: 100%;
    min-width: 190px;
    contain: inline-size;
    background-color: rgba(16, 26, 38, 0.92);
    border: 1px solid ${ct.rail};
`,dS=u.div`
    padding: 3px;
    background: ${ct.wash};
    border-left: 3px solid ${ct.bar};
    border-bottom: 1px solid ${ct.rail};
    color: ${ct.title};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
`,Lg=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px;
    font-size: 11px;
    color: #deebff;
`,zg=u.div`
    padding: 0 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: #8ca5c0;
    border-bottom: 1px solid ${ct.rail};

    &:last-child {
        border-bottom: none;
    }
`,Bc=u.div`
    padding: 0 6px;
    height: 18px;
    box-sizing: border-box;
    background: ${ct.btnBg};
    border: 1px solid ${ct.rail};
    color: ${ct.btnText};
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 10px;
    white-space: nowrap;
    user-select: none;

    &:hover {
        background: ${ct.bar};
        color: #0b1520;
    }
`,uS=u.div`
    display: flex;
    gap: 2px;
`,hS=u(Bc)`
    ${t=>t.$on&&`
        background: ${ct.bar};
        color: #0b1520;
        cursor: default;
    `}
`;class pS extends I.Component{move(e,n){e.stopPropagation(),e.preventDefault(),officers.startPick(this.props.ship,n),webglScene.customEvent("CloseSystemInfo")}cancel(e,n){e.stopPropagation(),e.preventDefault(),officers.cancelTransfer(this.props.ship,n)}side(e,n,r,i){e.stopPropagation(),e.preventDefault(),r.current?i&&officers.cancelTransfer(this.props.ship,n):i||officers.setTransfer(this.props.ship,n,n.post)}renderSides(e,n,r,i,o){const s=n.find(l=>!l.current);return a.jsxs(ie.Fragment,{children:[a.jsxs(Lg,{children:[a.jsx("span",{children:officers.label(e.id)}),a.jsx(uS,{children:n.map(l=>{const c=r?!l.current:l.current;return a.jsx(hS,{$on:c,onClick:d=>this.side(d,e,l,r),title:c?`He covers the ${l.name} weapons`:`Cover the ${l.name} weapons instead`,children:l.name},l.id)})})]}),a.jsx(zg,{children:r?`Switching to ${s.name}: he covers nothing for the rest of this turn, and the ${s.name} weapons from turn ${o}.`:"Switching sides takes the rest of the turn: he covers nothing until the next."})]},`${e.id}-${i}`)}render(){const{ship:e,system:n}=this.props,r=officers.currentTurn()+1;return a.jsxs(cS,{children:[a.jsx(dS,{children:"Officers"}),officers.movableOn(e,n.id).map((i,o)=>{const s=officers.transferOf(e,i),l=officers.sideChoices(e,i);return l?this.renderSides(i,l,s,o,r):a.jsxs(ie.Fragment,{children:[a.jsxs(Lg,{children:[a.jsx("span",{children:officers.label(i.id)}),s?a.jsx(Bc,{title:"Keep him at this post",onClick:c=>this.cancel(c,i),children:"Cancel Move"}):a.jsx(Bc,{title:"Then click the post to move him to",onClick:c=>this.move(c,i),children:"Move Officer"})]}),a.jsx(zg,{children:s?`To ${officers.systemName(e,s.to)}: he serves nowhere for the rest of this turn, and there from turn ${r}.`:"An officer who moves serves nowhere for the rest of the turn."})]},`${i.id}-${o}`)})]})}}const Bg=u.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,Ng=u.div`
    display: flex;
    flex-wrap: wrap;
`,wt=u.div`
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
`,Fg=u.span`
    color: #ffd27a;
    font-size: 16px;
    line-height: 1;
    text-shadow: black 0 0 3px, black 0 0 3px;
    pointer-events: none;
`;class fS extends I.Component{constructor(e){super(e)}online(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onOnlineClicked(n,r),webglScene.customEvent("CloseSystemInfo")}offline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Is(n,r)&&(shipManager.power.onOfflineClicked(n,r),webglScene.customEvent("CloseSystemInfo"))}allOnline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onlineAll(n,r),webglScene.customEvent("CloseSystemInfo")}allOffline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Is(n,r)&&(shipManager.power.offlineAll(n,r),webglScene.customEvent("CloseSystemInfo"))}overload(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onOverloadClicked(n,r),webglScene.customEvent("CloseSystemInfo")}stopOverload(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onStopOverloadClicked(n,r),webglScene.customEvent("CloseSystemInfo")}boost(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.clickPlus(n,r)}deboost(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.clickMinus(n,r)}addShots(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Zc(n,r)&&weaponManager.changeShots(n,r,1)}reduceShots(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;ed(n,r)&&weaponManager.changeShots(n,r,-1)}removeFireOrderMulti(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Os(n,r)&&weaponManager.removeFiringOrderMulti(n,r)}removeFireOrder(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Li(n,r)&&(weaponManager.removeFiringOrder(n,r),webglScene.customEvent("CloseSystemInfo"))}removeFireOrderAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Li(n,r)&&(weaponManager.removeFiringOrderAll(n,r),webglScene.customEvent("CloseSystemInfo"))}allChangeFiringMode(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(jr(n,r)){weaponManager.onModeClicked(n,r);var i=r.firingMode,o=[];n.flight?o=n.systems.map(f=>f.systems).reduce((f,v)=>f.concat(v),[]).filter(f=>f.weapon):o=n.systems.filter(f=>f.weapon);for(var s=weaponManager.stripPairingSuffix(r.displayName),l=new Array,c=0;c<o.length;c++)s===weaponManager.stripPairingSuffix(o[c].displayName)&&r.weapon&&l.push(o[c]);for(var c=0;c<l.length;c++){var d=l[c];if(d.firingMode!=i&&jr(n,d)){for(var h=d.firingMode,p=0;d.firingMode!=i&&p<2;)weaponManager.onModeClicked(n,d),d.firingMode==1&&p++;if(d.firingMode!=i)for(;d.firingMode!=h;)weaponManager.onModeClicked(n,d)}}}}changeFiringMode(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;jr(n,r)&&weaponManager.onModeClicked(n,r)}selectAllWeapons(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.selectAllWeapons(n,r,"forceSelect"),webglScene.customEvent("CloseSystemInfo")}deselectAllWeapons(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.selectAllWeapons(n,r,"forceDeselect"),webglScene.customEvent("CloseSystemInfo")}declareSelfIntercept(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(Ls(n,r)){if(weaponManager.onDeclareSelfInterceptSingle(n,r),r.canSplitShots)var i=r.checkFinished();i&&webglScene.customEvent("CloseSystemInfo")}}declareSelfInterceptAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(n,r),r.canSplitShots)var i=r.checkFinished();i&&webglScene.customEvent("CloseSystemInfo")}remSelfIntercept(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;zs(n,r)&&weaponManager.removeSelfInterceptSingle(n,r)}declareMeteorDefence(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Bs(n,r)&&(weaponManager.onDeclareMeteorDefence(n,r),webglScene.customEvent("CloseSystemInfo"))}declareMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.onDeclareMeteorDefenceAll(n,r),webglScene.customEvent("CloseSystemInfo")}remMeteorDefence(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ns(n,r)&&(weaponManager.removeMeteorDefence(n,r),webglScene.customEvent("CloseSystemInfo"))}remMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.removeMeteorDefenceAll(n,r),webglScene.customEvent("CloseSystemInfo")}nextCurrClass(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;r.nextCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}prevCurrClass(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;r.prevCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,selectedShip:n,system:r}=this.props;return Rs(e,r)?gamedata.gamephase===-2?a.jsx(Bg,{children:a.jsx(lS,{ship:e,system:r})}):a.jsxs(Bg,{children:[Jg(e,r)&&a.jsx(eb,{ship:e,system:r}),a.jsxs(Ng,{children:[Zc(e,r)&&a.jsx(wt,{title:"More shots",onClick:this.addShots.bind(this),img:"./img/plussquare.png"}),ed(e,r)&&a.jsx(wt,{title:"Less shots",onClick:this.reduceShots.bind(this),img:"./img/minussquare.png"})]}),Qg(e,r)&&a.jsxs(Ov,{ship:e,system:r,showModes:!!jr(e,r),children:[Ls(e,r)&&a.jsx(wt,{title:"Allow interception (RMB = All systems selected)",onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:"./img/addSelfIntercept.png"}),zs(e,r)&&a.jsx(wt,{title:"Remove an intercept order",onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:"./img/remSelfIntercept.png"}),Bs(e,r)&&a.jsx(wt,{title:"Meteor Defence: commit this weapon to defend against meteors this turn - it cannot fire or intercept (RMB = all similar weapons)",onClick:this.declareMeteorDefence.bind(this),onContextMenu:this.declareMeteorDefenceAll.bind(this),img:"./img/selfIntercept.png",children:a.jsx(Fg,{children:"☄"})}),Ns(e,r)&&a.jsx(wt,{title:"Remove Meteor Defence (RMB = all similar weapons)",onClick:this.remMeteorDefence.bind(this),onContextMenu:this.remMeteorDefenceAll.bind(this),img:"./img/remSelfIntercept.png",children:a.jsx(Fg,{children:"☄"})}),Os(e,r)&&a.jsx(wt,{title:"Remove last fire order",onClick:this.removeFireOrderMulti.bind(this),img:"./img/unfiringSmall.png"}),Li(e,r)&&a.jsx(wt,{title:"Remove all fire orders (RMB = All weapons selected)",onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:"./img/firing.png"})]}),a.jsxs(Ng,{children:[Nc(e,r)&&a.jsx(wt,{title:"Select all weapons of this type",onClick:this.selectAllWeapons.bind(this),img:"./img/selectAllWeapons.png",$blend:"screen"}),Nc(e,r)&&a.jsx(wt,{title:"Deselect all weapons of this type",onClick:this.deselectAllWeapons.bind(this),img:"./img/deselectAllWeapons.png",$blend:"screen"})]}),rd(e,r)&&a.jsx(X1,{ship:e,system:r}),Fc(e,r)&&a.jsx(g1,{ship:e,system:r}),Gc(e,r)&&a.jsx($1,{system:r,ship:e}),Vc(e,r)&&a.jsx(A1,{system:r,ship:e}),Wc(e,r)&&a.jsx(db,{system:r,ship:e}),Uc(e,r)&&a.jsx(yb,{system:r,ship:e}),_c(e,r)&&a.jsx(bb,{system:r,ship:e}),Hc(e,r)&&a.jsx(Eb,{system:r,ship:e}),(Yc(e,r)||Kc(e,r))&&a.jsx(pg,{system:r,ship:e}),(Qc(e,r)||Xc(e,r))&&a.jsx(pg,{system:r,ship:e}),As(e,r)&&a.jsx(Kv,{ship:e,system:r,readOnly:!gS(e,r)}),Jc(e,r)&&a.jsx(s1,{ship:e,system:r,readOnly:!mS(e,r)}),"   ",Fs(e,r)&&a.jsx(_1,{ship:e,system:r}),Xg(e,r)&&a.jsx(G1,{ship:e,system:r}),nd(e,r)&&a.jsx(pS,{ship:e,system:r})]}):null}}const Nc=(t,e)=>!(!window.matchMedia("(pointer: coarse)").matches||!e.weapon||Oi(t,e)||gamedata.gamephase!=3&&!e.ballistic&&!e.preFires||gamedata.gamephase!=1&&e.ballistic||gamedata.gamephase!=5&&e.preFires),Fc=(t,e)=>gamedata.gamephase===1&&e.name=="adaptiveArmorController",_c=(t,e)=>e.name==="GraviticAugmenter"&&gamedata.isMyShip(t)&&!e.stowed&&!shipManager.power.isOffline(t,e)&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(t,e)||gamedata.gamephase===5),Hc=(t,e)=>e.name==="MinorThoughtPulsar"&&gamedata.gamephase===3&&gamedata.isMyShip(t)&&!e.stowed&&!shipManager.systems.isDestroyed(t,e)&&!shipManager.power.isOffline(t,e),Wc=(t,e)=>gamedata.gamephase===-1&&t.mine&&(t.spawned==-1&&gamedata.turn==1||t.spawned==gamedata.turn-1)&&(e.name=="CaptorMine"||e.name=="MineControllerDEW"),Uc=(t,e)=>gamedata.gamephase===-1&&t.mine&&(t.spawned==-1&&gamedata.turn==1||t.spawned==gamedata.turn-1)&&e.name=="ProximityMine",Gc=(t,e)=>e.name=="hyachComputer"&&(gamedata.gamephase===1||gamedata.gamephase===3&&typeof e.firingMode=="function"&&e.firingMode()!==null),Vc=(t,e)=>e.name==="hyachSpecialists",Yc=(t,e)=>gamedata.gamephase===1&&e.name==="ThirdspaceShield",Kc=(t,e)=>gamedata.gamephase===1&&e.name==="ThirdspaceShieldGenerator",Qc=(t,e)=>gamedata.gamephase===1&&e.name==="ThoughtShield",Xc=(t,e)=>gamedata.gamephase===1&&e.name==="ThoughtShieldGenerator",As=(t,e)=>gamedata.isMyShip(t)&&e.name=="SelfRepair",gS=(t,e)=>As(t,e)&&gamedata.gamephase===1,Jc=(t,e)=>gamedata.isMyShip(t)&&(e.name=="StructureSelfRepair"||e.name=="CoopStructureSelfRepair"),mS=(t,e)=>Jc(t,e)&&gamedata.gamephase===1,_g=()=>typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted(),cn=(t,e)=>gamedata.gamephase===-2&&!_g()&&t&&t.userid!=0&&!t.flight&&!t.mine&&!xS(e),Hg=(t,e)=>cn(t,e)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(t,e)&&systemEnhancements.offersFor(t,e).length>0,qc=t=>gamedata.gamephase===-2&&!_g()&&t&&t.userid!=0&&!!t.mine&&battleDamage.mineMaxHealth(t)>1,xS=t=>!t||!(t.maxhealth>0)||t.isTargetable===!1&&!t.orbitalMounted||!!t.hideInShipWindow||Array.isArray(t.systems),Rs=(t,e)=>gamedata.gamephase===-2?cn(t,e)||Hg(t,e):Is(t,e)||Wg(t,e)||Ug(t,e)||Gg(t,e)||Vg(t,e)||Yg(t,e)||Zc(t,e)||ed(t,e)||Os(t,e)||Li(t,e)||jr(t,e)||Ls(t,e)||zs(t,e)||Bs(t,e)||Ns(t,e)||Fc(t,e)||Gc(t,e)||Vc(t,e)||Yc(t,e)||Qc(t,e)||Kc(t,e)||Xc(t,e)||As(t,e)||yS(t,e)||wS(t,e)||Fs(t,e)||Xg(t,e)||rd(t,e)||Nc(t,e)||Wc(t,e)||Uc(t,e)||_c(t,e)||Hc(t,e)||nd(t,e),Is=(t,e)=>gamedata.gamephase===1&&(e.canOffLine||e.powerReq>0)&&!e.powerLocked&&!shipManager.power.isOffline(t,e)&&!shipManager.power.getBoost(e)&&!weaponManager.hasFiringOrder(t,e),Wg=(t,e)=>gamedata.gamephase===1&&shipManager.power.isOffline(t,e)&&!shipManager.power.isForcedOffline(t,e)&&!shipManager.power.isVortexLockedOffline(t,e),Ug=(t,e)=>gamedata.gamephase===1&&!shipManager.power.isOffline(t,e)&&e.weapon&&e.overloadable&&!shipManager.power.isOverloading(t,e),Gg=(t,e)=>gamedata.gamephase===1&&e.weapon&&e.overloadable&&shipManager.power.isOverloading(t,e)&&(e.overloadshots>=e.extraoverloadshots||e.overloadshots==0),Vg=(t,e)=>e.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(t,e)&&(!e.isScanner()||e.id==shipManager.power.getHighestSensorsId(t))&&e.name!=="ThirdspaceShieldGenerator"&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor",Yg=(t,e)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(e)&&e.name!=="ThirdspaceShieldGenerator"&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor",Kg=(t,e)=>{const n=weaponManager.getFiringOrder(t,e);return n&&n.type!=="intercept"&&n.type!=="selfIntercept"?n:null},Zc=(t,e)=>{if(Oi(t,e)||!e.weapon||!e.canChangeShots||!weaponManager.hasFiringOrder(t,e))return!1;const n=Kg(t,e);return!!n&&n.shots<e.maxVariableShots},ed=(t,e)=>{if(Oi(t,e)||!e.weapon||!e.canChangeShots||!weaponManager.hasFiringOrder(t,e))return!1;const n=Kg(t,e);return!!n&&n.shots>1},Oi=(t,e)=>e.name==="jumpEngine"&&typeof e.getHeldVortex=="function"&&!!e.getHeldVortex(),Os=(t,e)=>e.weapon&&weaponManager.hasOrderForMode(e)&&e.canSplitShots&&!Oi(t,e),Li=(t,e)=>e.weapon&&weaponManager.hasFiringOrder(t,e)&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked())&&!Oi(t,e),td=t=>!!(window.shipManager&&shipManager.isDockingRider(t)),jr=(t,e)=>e.weapon&&!t.mine&&!e.stowed&&!e.hideFiringModeSelector&&!td(t)&&e.name!=="GraviticAugmenter"&&e.name!=="MinorThoughtPulsar"&&(gamedata.gamephase===1&&e.ballistic||gamedata.gamephase===5&&e.preFires||gamedata.gamephase===3&&!e.ballistic&&!e.preFires)&&(!weaponManager.hasFiringOrder(t,e)||e.multiModeSplit)&&!weaponManager.hasMeteorDefence(t,e)&&Object.keys(e.firingModes).length>1,Ls=(t,e)=>e.weapon&&!td(t)&&weaponManager.canSelfInterceptSingle(t,e),zs=(t,e)=>e.weapon&&e.canSplitShots&&!td(t)&&weaponManager.canRemInterceptSingle(t,e),Qg=(t,e)=>jr(t,e)||Ls(t,e)||zs(t,e)||Bs(t,e)||Ns(t,e)||Os(t,e)||Li(t,e),Bs=(t,e)=>e.weapon&&weaponManager.canDeclareMeteorDefence(t,e),Ns=(t,e)=>e.weapon&&weaponManager.canRemoveMeteorDefence(t,e),yS=(t,e)=>e.canActivate&&typeof e.canActivate=="function"&&e.canActivate()&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor"&&e.name!=="GraviticAugmenter"&&e.name!=="jumpEngine",wS=(t,e)=>e.canDeactivate&&typeof e.canDeactivate=="function"&&e.canDeactivate()&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor"&&e.name!=="GraviticAugmenter"&&e.name!=="jumpEngine",Xg=(t,e)=>e.name==="jumpEngine"&&typeof e.canMaintainVortex=="function"&&(e.canMaintainVortex()||e.canDeactivate()||typeof e.getAbductionOrder=="function"&&!!e.getAbductionOrder()),nd=(t,e)=>!!window.officers&&officers.canManage(t,e),Fs=(t,e)=>e.name==="powerCapacitor"||e.name==="PowerCapacitor",Jg=(t,e)=>Is(t,e)||Wg(t,e)||Ug(t,e)||Gg(t,e)||e.boostable&&(Vg(t,e)||Yg(t,e)),rd=(t,e)=>Fs(t,e)||e.name==="GraviticAugmenter"||e.name==="jumpEngine"?!1:!!(e.canActivate&&typeof e.canActivate=="function"&&e.canActivate()||e.canDeactivate&&typeof e.canDeactivate=="function"&&e.canDeactivate()),vS=(t,e)=>gamedata.gamephase===-2?cn(t,e)||Hg(t,e):Fc(t,e)||Gc(t,e)||Vc(t,e)||Wc(t,e)||Uc(t,e)||_c(t,e)||Hc(t,e)||Yc(t,e)||Kc(t,e)||Qc(t,e)||Xc(t,e)||As(t,e)||Jc(t,e)||Fs(t,e)||Jg(t,e)||rd(t,e)||Qg(t,e)||nd(t,e);let dn=null;function qg(t,e){const n=t&&e?{ship:t,systemId:e.id}:null;n===dn||n&&dn&&n.ship===dn.ship&&n.systemId===dn.systemId||(dn=n,window.shipWindowManagerReact&&window.shipWindowManagerReact.update())}function Zg(t,e){return dn!==null&&!!t&&!!e&&dn.ship===t&&dn.systemId===e.id}const em=u.div`
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
`,tm=u.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,nm=u.div`
    /*11px on a 32px icon: the 7px it launched at was legible only if you already knew to
      look for it (user report 2026-08-15). Still small enough to clear the icon art and the
      [n/n] load counter, which sits along the BOTTOM edge.*/
    font-size: 11px;
    line-height: 11px;
    color: ${g.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,bS=u.div`
    position: absolute;
    top: 0px;
    left: 1px;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    gap: 1px;
    pointer-events: none;
`,SS=u.svg`
    display: block;
    width: 11px;
    height: 11px;
    overflow: visible;
    filter: drop-shadow(0 0 1px black) drop-shadow(0 0 1px black);
`,id=W.officer.bar,kS={serving:g.colors.enhTitle,disabled:"#c4c4c4",killed:"#c4c4c4",moving:id},od=({state:t,title:e})=>a.jsxs(SS,{viewBox:"0 0 12 12",role:"img","aria-label":e,children:[a.jsx("title",{children:e}),a.jsx("path",{d:"M1.5 5.5 L6 2 L10.5 5.5 M1.5 10 L6 6.5 L10.5 10",fill:"none",stroke:kS[t],strokeWidth:"2",strokeLinecap:"square"}),t==="killed"&&a.jsx("path",{d:"M1 11.5 L11 0.5",stroke:"#ff4d4f",strokeWidth:"1.8",strokeLinecap:"round"})]}),$S=u.div`
    position: absolute;
    top: 0px;
    right: 1px;
    z-index: 1;
    pointer-events: none;
    font-size: 11px;
    line-height: 11px;
    color: #ffd27a;
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,rm=u.div`
    position: relative;
    box-sizing: border-box;
    width: 32px;
    height: 32px;
    margin: ${t=>t.$scs?"3px 0":"2px"};
   border: ${t=>t.$firing&&t.$calledShot?"2px solid #ff3366":t.$meteorDefence?"1px solid #d9a441":t.$firing&&t.$intercepting?"1px solid #52b352":t.$firing?"1px solid #eb5c15":t.$orderPending?"2px solid #00e5ff":t.$highlight==="Yellow"?"1px solid #e1b000":t.$highlight==="Orange"?"2px solid #ff6d3c":t.$highlight==="Red"?"2px solid #ff0000":"1px solid #496791"};
     background-color:  ${t=>t.$selected?"#4e6c91":t.$meteorDefence?"#5c4318":t.$firing&&t.$intercepting?"#2f7a3a":t.$firing?"#e06f01":t.$off?"#852d2d":t.$boosted?"#cca300":t.$loading&&t.$loadedAlternate?"#CD9E9E":"rgba(0, 0, 0, 0.7)"};
    box-shadow: ${t=>t.$selected?"0px 0px 15px #0099ff":t.$firing&&t.$calledShot?"0px 0px 12px #ff3366":t.$meteorDefence?"0px 0px 12px #d9a441":t.$firing&&t.$intercepting?"0px 0px 15px #52b352":t.$firing?"box-shadow: 0px 0px 15px #eb5c15":t.$orderPending?"0px 0px 12px #00e5ff":"none"};
    /*The lobby's system menu is open on this system (helpers/menuTarget, user request 2026-10-16): an outline
      drawn just inside the edge, so no border state moves and nothing reflows, in the menu's own focus blue.*/
    ${t=>t.$menuTarget?`
        outline: 2px solid ${P.focus};
        outline-offset: -2px;
        box-shadow: 0 0 8px ${P.focus};
    `:""}
    /*A post the officer being moved may go to (OFFICERS_PLAN.md Stage 10, officers.isPickTarget): the same inset
      outline, in the Officers blue, while "Move Officer" waits for the player's click.*/
    ${t=>t.$officerPick?`
        outline: 2px solid ${id};
        outline-offset: -2px;
        box-shadow: 0 0 8px ${id};
    `:""}
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
    
    ${tm} {
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
`;class sd extends I.Component{constructor(e){super(e),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(e){if(e.stopPropagation(),e.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n);const i=cn(r,n);if((gamedata.waiting||gamedata.replay)&&!i||window.officers&&officers.picking&&officers.pickTarget(r,n))return;const o=!!r.removed&&!shipManager.isDestroyedByDamage(r);if(!(!i&&!o&&(shipManager.isDestroyed(r)||shipManager.isDestroyed(r,n)&&!n.clickableWhenDestroyed))){if(o&&!i){gamedata.isMyShip(r)&&window.uiEvents.relay("SystemClicked",{ship:r,system:n,element:e.currentTarget,showMenu:!0});return}if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(r)){var s=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(s&&s.ship.id!=r.id&&!weaponManager.isSelectedWeapon(n)){window.uiEvents.relay("SystemTargeted",{ship:r,system:n});return}}var l=n.weapon&&typeof n.isSpentLocked=="function"&&n.isSpentLocked(),c=typeof n.canSelectForAbduction=="function"&&n.canSelectForAbduction(r);if(!l&&(n.weapon&&gamedata.gamephase===3&&!n.ballistic&&!n.preFires||gamedata.gamephase===1&&n.ballistic||gamedata.gamephase===5&&n.preFires||weaponManager.canManuallyInterceptWith(r,n)||c)&&!shipManager.isAdrift(r)&&gamedata.isMyShip(r)){if(n.hasSpecialTargeting&&typeof n.reopenSpecialTargeting=="function"&&weaponManager.hasFiringOrder(r,n)&&n.reopenSpecialTargeting(r))return;var d=weaponManager.hasFiringOrder(r,n),h=d&&d!=="self"&&!n.canSplitShots&&!n.hasSpecialTargeting;weaponManager.isSelectedWeapon(n)?weaponManager.unSelectWeapon(r,n):h||weaponManager.selectWeapon(r,n)}if(gamedata.isMyShip(r)&&(n.name==="hangar"||n.name==="catapult"||n.name==="fighterRail")){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog=="function"&&window.DeploymentDock.shipHasOpenableDockDialog(r)&&window.confirm&&typeof window.confirm.hangarDeployDock=="function"){window.confirm.hangarDeployDock(r);return}if(gamedata.gamephase===3&&!n.isShadowHangar&&!shipManager.movement.isRolling(r)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(r)!=="no")&&window.confirm&&typeof window.confirm.hangarLaunch=="function"){window.confirm.hangarLaunch(r);return}}if(gamedata.isMyShip(r)&&(n.name==="dockingCollar"||n.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(r)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(r)!=="no")&&typeof window.lcvRailLaunchable=="function"&&window.lcvRailLaunchable(r,n)&&window.confirm&&typeof window.confirm.lcvLaunch=="function"){window.confirm.lcvLaunch(r);return}gamedata.isMyShip(r)?window.uiEvents.relay("SystemClicked",{ship:r,system:n,element:e.currentTarget,showMenu:!0}):window.uiEvents.relay("SystemTargeted",{ship:r,system:n})}}onSystemMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;e.stopPropagation(),e.preventDefault();let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n),window.uiEvents.relay("SystemMouseOver",{ship:r,system:n,element:e.currentTarget,showInfo:!0})}onSystemMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay("SystemMouseOut"))}onTouchStart(e){e.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:i,ship:o}=this.props;i=shipManager.systems.initializeSystem(i),window.uiEvents.relay("SystemMouseOver",{ship:o,system:i,element:n,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(e){e.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(e){if(e.stopPropagation(),e.preventDefault(),window.matchMedia("(pointer: coarse)").matches)return;let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n),n.weapon&&weaponManager.selectAllWeapons(r,n)}render(){let{system:e,ship:n,scs:r,fighter:i,destroyed:o,mirror:s}=this.props;return e=shipManager.systems.initializeSystem(e),e=shipManager.systems.initializeSystem(e),(ad(n,e)||o)&&!e.clickableWhenDestroyed&&!cn(n,e)?a.jsxs(rm,{$background:am(e),$destroyed:!0,$mirror:s,children:[im(n,e),a.jsx(em,{$health:"0"})]}):a.jsxs(rm,{$scs:r,$highlight:BS(n,e),$destroyed:ad(n,e)||o,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:am(e),$mirror:s,$offline:PS(n,e),$loading:ES(e),$loadedAlternate:DS(e),$selected:NS(e),$firing:CS(n,e),$intercepting:MS(n,e),$meteorDefence:om(n,e),$calledShot:jS(n,e),$boosted:RS(n,e),$off:AS(e),$docked:sm(e),$orderPending:OS(e),$menuTarget:Zg(n,e),$officerPick:!!window.officers&&officers.isPickTarget(n,e),children:[im(n,e),om(n,e)&&a.jsx($S,{title:"Committed to meteor defence this turn",children:"☄"}),a.jsx(tm,{children:FS(n,e)}),(!i||lm(e))&&a.jsx(em,{$scs:r,$health:ad(n,e)||o?0:LS(n,e),$criticals:lm(e),$criticalsBenign:zS(e),$docked:IS(e)})]})}}const im=(t,e)=>{if(!t||!e)return null;const n=!!window.systemEnhancements&&systemEnhancements.hasAny(t,e.id),r=window.officers?officers.postState(t,e.id):null;return!n&&!r?null:a.jsxs(bS,{children:[n&&a.jsx(nm,{title:"Carries a system enhancement",children:"✦"}),r&&a.jsx(od,{state:r,title:officers.describePost(t,e.id)})]})},CS=(t,e)=>(weaponManager.hasFiringOrder(t,e)||TS(e))&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked()),TS=t=>gamedata.gamephase===1&&typeof t.getAbductionOrder=="function"&&!!t.getAbductionOrder(),MS=(t,e)=>weaponManager.isInterceptOnly(t,e),om=(t,e)=>!!e.weapon&&weaponManager.hasMeteorDefence(t,e),jS=(t,e)=>!e.weapon||!weaponManager.hasFiringOrder(t,e)?!1:weaponManager.getCalledShotInfo(t,e)!==null,ES=t=>t.weapon&&(!weaponManager.isLoaded(t)||typeof t.isSpentLocked=="function"&&t.isSpentLocked()),DS=t=>t.weapon&&weaponManager.isLoadedAlternate(t),PS=(t,e)=>shipManager.power.isOffline(t,e),AS=t=>t.activeMeansOff&&t.active,RS=(t,e)=>shipManager.power.isBoosted(t,e)||e.active&&!e.activeMeansOff&&!e.suppressActiveBoost,sm=t=>!!(t.showDockedVisual&&t.activeEffective||t.stowed&&t.stowedArcStart==null||t.dockedWithOrbital),IS=t=>sm(t)||!!t.stowed,OS=t=>!!(t.showDockedVisual&&typeof t.hasPendingDockingOrder=="function"&&t.hasPendingDockingOrder()),LS=(t,e)=>(e.name==="ThirdspaceShield"||e.name==="ThoughtShield")&&e.baseRating?Math.min(100,e.currentHealth/e.baseRating*100):(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,ad=(t,e)=>shipManager.systems.isDestroyed(t,e),am=t=>t.name=="thruster"&&!t.iconPath?window.AssetManager.getSmartImagePath("./img/systemicons/thruster"+t.direction+".png"):t.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${t.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${t.name}.png`),lm=t=>shipManager.criticals.hasCriticalsIcon(t),zS=t=>shipManager.criticals.hasOnlyCritical(t,"HangarOperations",!0)||shipManager.criticals.hasOnlyCritical(t,"LCVLaunchedThisTurn",!0),BS=(t,e)=>shipManager.systems.hasBorderHighlight(t,e),NS=t=>weaponManager.isSelectedWeapon(t),FS=(t,e)=>{if(e.outputDisplay!==void 0&&e.outputDisplay!==null&&e.outputDisplay!="")return e.outputDisplay;if(e.weapon){if(e.stowed&&e.stowedArcStart==null)return"-";if(typeof e.isSpentLocked=="function"&&e.isSpentLocked())return"✓";if(typeof e.getVortexIconLoad=="function"){const i=e.getVortexIconLoad();if(i!=null)return i}const r=weaponManager.hasFiringOrder(t,e);if(r&&e.canChangeShots)return weaponManager.getFiringOrder(t,e).shots+"/"+e.shots;if(r){var n=weaponManager.getCalledShotInfo(t,e);if(n)return"⊕"}else if(!r){let i=weaponManager.getWeaponCurrentLoading(e),o=e.loadingtime;e.normalload>0&&(o=e.normalload),i>o&&(i=o);let s="";return e.overloadturns>0&&shipManager.power.isOverloading(t,e)&&(s="("+e.overloadturns+")"),e.overloadshots>0?"S"+e.overloadshots:i+s+"/"+o}}else{if(e.outputType==="thrust")return shipManager.movement.getRemainingEngineThrust(t);if(e.outputType==="power"){let r=shipManager.power.getReactorPower(t,e);return gamedata.gamephase>1&&r<0?0:r}else return shipManager.systems.getOutput(t,e)}},_S=u.div`
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
`;class HS extends I.Component{constructor(e){super(e)}getWeapons(e,n){return e.flight?e.systems.map(r=>r.systems).reduce((r,i)=>r.concat(i),[]).filter(r=>r.weapon):e.systems.filter(r=>r.weapon||r.outputType==="thrust"||r.outputType==="EW"||r.outputType==="power"||r.outputType==="settings")}render(){const{ship:e,gamePhase:n}=this.props;if(!e)return null;const r=this.getWeapons(e,n);return a.jsx(_S,{children:r.map((i,o)=>a.jsx(sd,{fighter:e.flight,system:i,ship:e},`system-${o}`))})}}const cm=t=>{if(!t.hitChart)return[];const e=["Primary","Front","Aft","Port","Starboard"];let n=5;t.base&&!t.smallBase?(e[1]="Sections",n=2):t.SixSidedShip&&(e[31]="Port Front",e[32]="Port Aft",e[41]="Starboard Front",e[42]="Starboard Aft",n=43);const r=[];for(let i=0;i<n;i++){if(t.hitChart[i]===void 0)continue;const o=[];let s=0;for(const l in t.hitChart[i]){const c=Math.floor((l-s)/20*100);s=l;let d=t.hitChart[i][l];const h=d.indexOf(":");h>0&&(d=d.substring(h+1)),o.push({name:d,chance:c})}r.push({location:i,name:e[i],entries:o})}return r},WS=u.div`
    ${t=>t.$tightBottom?"& > *:last-child { display: none; }":""}
    ${t=>t.$compactText?`
    ${N} {
        font-size: 10px;
        line-height: 1.4;
        color: ${g.colors.textAccent};
    }
    ${ne} {
        font-size: 10px;
        font-style: normal;
        color: ${g.colors.text};
    }`:""}
`;class dm extends I.Component{render(){const{ship:e,hideHitChart:n,tightBottom:r,compactText:i,craft:o}=this.props,s=!!e.mine||window.gamedata&&typeof gamedata.isTerrain=="function"&&gamedata.isTerrain(e.shipSizeClass,e.userid),l=!!e.flight||s;var c=new Array,d=new Array,h=new Array;e.notes&&(c=e.notes.split("<br>")),e.hitChart&&!n&&cm(e).forEach(function(m){d[m.name]=m.entries.map(function(y){return y.name+" "+y.chance+"%"}).join(", ")}),e.enhancementTooltip!=""&&(h=e.enhancementTooltip.split("<br>"));let p={};if(!e.flight&&e.hasAttached&&Object.keys(e.hasAttached).length>0){const m={1:"Forward",2:"Aft",3:"Port",31:"Port-Forward",32:"Port-Aft",4:"Starboard",41:"Starboard-Forward",42:"Starboard-Aft"};for(let y in e.hasAttached){let S=e.hasAttached[y],T=m[S]||"Unknown";p[T]||(p[T]=0),p[T]++}}let f=window.officers?officers.displayStat(e,"offensivebonus"):e.offensivebonus;e.flight&&gamedata.areMinesPresent&&(e.minesweeper?f-=window.ew.getDetectMEW(e):f-=window.ew.getDetectMEW(e)*2);const v=window.officers?officers.currentTurn():0,x=[];if(e.flight&&window.officers){const m={};officers.listFor(e).forEach(y=>{const S=officers.get(y.id);if(o&&S&&S.multi&&String(y.post)!==String(o.id))return;const T=officers.stateText(y),C={name:officers.label(y.id),text:(S?S.summary:"")+(T?" "+T:""),out:!officers.serves(y,v),count:1},k=C.name+"|"+C.text+"|"+C.out;if(m[k]){m[k].count++;return}m[k]=C,x.push(C)}),x.forEach(y=>{y.count>1&&(y.name+=" ("+y.count+")")})}var b=0,M=!0;if(e.mine){var w=shipManager.systems.getSystemByName(e,"mineStealth");w&&!w.isMineRevealed(e)&&(M=!1,d=new Array,c=["No details known, scan with OEW to identify."],h=new Array)}return a.jsxs(WS,{$tightBottom:r,$compactText:i,children:[e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Offensive bonus: "}),f*5]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Armor (F/S/A): "}),shipManager.systems.getFlightArmour(e)]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Profile - Front/Side: "}),e.forwardDefense*5,"/",e.sideDefense*5]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Initiative: "}),window.officers?officers.displayIniativeBonus(e):e.iniativebonus]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Thrust: "}),window.officers?officers.displayStat(e,"freethrust"):e.freethrust]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Turn Cost: "}),e.turncost]},b++),e.flight&&M&&e.turndelay&&a.jsxs(N,{children:[a.jsx(ne,{children:"Turn Delay: "}),e.turndelaycost]},b++),e.flight&&M&&a.jsx(N,{children:" "},b++),Object.keys(c).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"NOTES:"})," "]},b++),Object.keys(c).length>0&&Object.keys(c).map(m=>a.jsx(N,{children:c[m]},b++)),Object.keys(c).length>0&&a.jsx(N,{children:" "},b++),Object.keys(d).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"HIT CHART:"})," "]},b++),Object.keys(d).length>0&&Object.keys(d).map(m=>a.jsxs(N,{children:[a.jsxs(ne,{children:[m,": "]}),d[m]]},b++)),Object.keys(d).length>0&&a.jsx(N,{children:" "},b++),Object.keys(p).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"UNITS ATTACHED:"})," "]},b++),Object.keys(p).length>0&&Object.keys(p).map(m=>a.jsxs(N,{children:[a.jsxs(ne,{children:[m,": "]}),p[m]]},b++)),Object.keys(p).length>0&&a.jsx(N,{children:" "},b++),l&&e.enhancementTooltip!=""&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"ENHANCEMENTS:"})," "]},b++),l&&e.enhancementTooltip!=""&&M&&Object.keys(h).map(m=>a.jsx(N,{children:h[m]},b++)),l&&e.enhancementTooltip!=""&&M&&a.jsx(N,{children:" "},b++),e.flight&&x.length>0&&a.jsxs(N,{children:[a.jsx(YS,{children:"OFFICERS:"})," "]},b++),e.flight&&x.map(m=>a.jsxs(dd,{$out:m.out,children:[a.jsxs(ud,{children:[m.name,": "]}),m.text]},b++)),e.flight&&x.length>0&&a.jsx(N,{children:" "},b++)]})}}const un=u(Uf)`
    /*font-size: 12px;*/
	font-size: 13px;
`,ld=u(Wf)`
    position: absolute;
    z-index: 20000;
    ${t=>Object.keys(t.position).reduce((e,n)=>e+`
`+n+":"+t.position[n]+"px;","")}
    width: ${t=>t.ship?"320px":"220px"};
    text-align: left;
    opacity:0.8;
`,cd=u.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,US={MissileLost:"A missile was lost to damage"},um={DamageReductionReduced:t=>`Damage reduction reduced by ${t}`},N=u(Gf)`
    text-align: left;
    /*color: #5e85bc;*/
	color: #BDEAFA; /*replace dark blue above with bluish white, more eyes friendly*/
    font-family: arial;
    /*font-size: 11px;*/
	font-size: 12px;
`,ne=u.span`
    color: white;
	font-size: 11px;
    font-weight: bold;
`,GS=u.span`
    color: #C6E2FF;
`,VS=u(un)`
    color: ${g.colors.enhTitle};
`,dd=u(N)`
    &&& { color: ${t=>t.$out?g.colors.textDim:g.colors.enhText}; }
`,ud=u.span`
    &&& { color: ${g.colors.enhTitle}; }
`,YS=u(ne)`
    &&& { color: ${g.colors.enhTitle}; }
`;class KS extends I.Component{render(){const{ship:e,selectedShip:n,system:r,boundingBox:i,officersOnly:o,craft:s}=this.props;if(o)return a.jsxs(ld,{position:hd(i),children:[a.jsx(un,{children:window.officers?officers.postName(r):r.displayName}),pm(e,r,!0)]});if(r instanceof Ship||r===e){var l=e.shipClass,c=e.name;if(r.flight&&(l=r.systems[1].displayName),e.mine){var d=shipManager.systems.getSystemByName(e,"mineStealth");d&&!d.isMineRevealed(e)&&(l="Mine",c="Mine")}return a.jsxs(ld,{ship:!0,position:hd(i),children:[a.jsxs(un,{children:[a.jsx(GS,{children:c})," - ",l]}),a.jsx(dm,{ship:e,craft:s})]})}var h="Special",p=new Array;r.data.Special&&r.data.Special!=""&&(p=String(fm(e,r,h)).split("<br>"));var f=0;let v=window.officers?officers.displayStat(e,"offensivebonus"):e.offensivebonus;e.flight&&gamedata.areMinesPresent&&(e.minesweeper?v-=window.ew.getDetectMEW(e):v-=window.ew.getDetectMEW(e)*2);var x=r.displayName,b=r.firingModes?r.firingModes[r.firingMode]:null,M=null;r.name==="ShadowFighterBomb"&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool=="function"&&(M=weaponManager.shadowFighterBombPool(e,r,!0));var w=null;if(r.outputType==="power"&&window.shipManager&&shipManager.power&&typeof shipManager.power.getDockedPowerSummary=="function"){var m=shipManager.power.getDockedPowerSummary(e);m.donors>0&&(w=m)}var y=null;if(typeof r.getAbductionOrder=="function"){var S=r.getAbductionOrder();if(S){var T=gamedata.getShip(S.targetid);y=T?T.name:"Unit "+S.targetid}}var C=window.officers?officers.laserBoostText(e,r):null,k=window.officers?officers.ionBoostText(e,r):null;let $=!1;if(e.mine){var d=shipManager.systems.getSystemByName(e,"mineStealth");d&&!d.isMineRevealed(e)&&($=!0,x="Mine",p=["No details known, scan with OEW to identify."])}return a.jsxs(ld,{position:hd(i),children:[a.jsx(un,{children:x}),!e.flight&&!$&&dt("Structure",r.maxhealth-damageManager.getDamage(e,r)+"/"+r.maxhealth),!e.flight&&!$&&dt("Armor",shipManager.systems.getArmour(e,r)),e.flight&&!$&&dt("Offensive bonus",JS(r,v*5)),r.firingModes&&!$&&dt("Firing mode",b),r.missileArray&&Object.keys(r.missileArray).length>0&&!$&&dt("Ammo Amount",r.missileArray[r.firingMode].amount),!$&&Object.keys(r.data).map((D,E)=>D!=h&&!(D==="Ammunition"&&(r.name==="GrapplingClaw"||r.name==="Marines"))&&dt(D,fm(e,r,D),"data"+E)),M!==null&&dt("Fighters available",M),w&&dt("Shared by docked ships","+"+w.shared+" of "+w.surplus+" pooled from "+w.donors+(w.donors===1?" ship":" ships")),y!==null&&dt("Abduction target",y),C!==null&&!$&&dt("Laser boost",C),k!==null&&!$&&dt("Thrust boost",k),Object.keys(p).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"Special: "})," "]},`special-${f++}`),Object.keys(p).length>0&&Object.keys(p).map(D=>a.jsx(N,{children:p[D]},`special-${f++}`)),!$&&pm(e,r),(Object.keys(r.critData).length>0||r.criticals&&r.criticals.length>0)&&!$&&XS(r),!gamedata.isMyShip(e)&&!$&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&n&&hm(e,n,r),gamedata.isMyShip(e)&&!$&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&n&&hm(e,n,r),gamedata.isMyShip(e)&&!$&&r.weapon&&weaponManager.hasFiringOrder(e,r)&&QS(e,r)]})}}const hm=(t,e,n)=>weaponManager.canCalledshot(t,n,e)?[a.jsx(un,{children:"Called shot"},"calledHeader")].concat(gamedata.selectedSystems.map((r,i)=>{if(weaponManager.isOnWeaponArc(e,t,r))if(weaponManager.checkIsInRange(e,t,r)){var o=r.firingMode;return o=r.firingModes[o],n.id!=null&&!weaponManager.canWeaponCall(r)?a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName}),": Cannot Called Shot"]},`called-${i}`):a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName})," - Approx:  ",weaponManager.calculateHitChange(e,t,r,n.id).hitChance,"%"]},`called-${i}`)}else return a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName}),": Not in Range"]},`called-${i}`);else return a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName}),": Not in Arc"]},`called-${i}`)})):[a.jsx(un,{children:"Called shot"},"calledHeader")].concat(a.jsx(N,{children:"Cannot Target"},"cannotTarget")),QS=(t,e)=>{var n=weaponManager.getCalledShotInfo(t,e);return n?[a.jsx(cd,{},"calledShotDivider"),a.jsx(un,{children:"Called Shot"},"calledShotHeader"),a.jsxs(N,{children:[a.jsx(ne,{children:"Target: "}),n.targetSystem.displayName," (Id: ",n.targetSystem.id,") on ",n.targetShip.name]},"calledShotTarget")]:null},pm=(t,e,n=!1)=>{if(!window.officers||!t||!e)return null;const r=officers.onPost(t,e.id),i=officers.arrivingAt(t,e.id);if(r.length===0&&i.length===0)return null;const o=officers.currentTurn();return(n?[]:[a.jsx(cd,{},"officerDivider")]).concat([a.jsx(VS,{children:"Officers"},"officerHeader")]).concat(r.map((s,l)=>{const c=officers.moveText(t,s)||officers.stateText(s);return a.jsxs(dd,{$out:!officers.serves(s,o)||officers.movingTo(t,s)!==null,children:[a.jsxs(ud,{children:[officers.label(s.id),": "]}),officers.summaryOf(t,s),c?" "+c:""]},`officer-${s.id}-${l}`)}),i.map((s,l)=>a.jsxs(dd,{$out:!0,children:[a.jsxs(ud,{children:[officers.label(s.entry.id)," "]}),officers.arrivalText(t,s.entry)]},`arriving-${s.entry.id}-${l}`)))},XS=t=>{const e=Object.keys(t.critData).length>0?Object.keys(t.critData):[...new Set((t.criticals||[]).map(n=>n.phpclass))];return e.length===0?null:[a.jsx(cd,{},"critDivider"),a.jsx(un,{children:"Criticals"},"criticalHeader")].concat(e.map(n=>{let r=0,i=0;var o=0,s=0,l=!1,c="";o=0,s=0,l=!1,c="";for(const h in t.criticals)t.criticals[h].phpclass==n&&t.criticals[h].turn<=gamedata.turn&&(t.criticals[h].turnend==0||t.criticals[h].turnend>=gamedata.turn)&&(r++,i+=parseInt(t.criticals[h].param,10)||0,r==1&&(o=t.criticals[h].turnend,s=t.criticals[h].turnend,l=t.criticals[h].turnend==0),t.criticals[h].turnend>0?(t.criticals[h].turnend>s&&(s=t.criticals[h].turnend),(t.criticals[h].turnend<o||o==0)&&(o=t.criticals[h].turnend)):l=!0);if(o>0&&(c=" (until end of turn "+o,l?c=c+"+":s>o&&(c=c+"-"+s),c=c+")"),r>=1&&um[n]){const h=um[n](i);return a.jsxs(N,{children:[h," ",c]},`critical-${n}`)}const d=t.critData[n]||US[n]||n;return r>1?a.jsxs(N,{children:["(",r," x) ",d," ",c]},`critical-${n}`):r==1?a.jsxs(N,{children:[d," ",c]},`critical-${n}`):null}))},JS=(t,e)=>typeof t.adjustOffensiveBonusDisplay=="function"?t.adjustOffensiveBonusDisplay(e):e,fm=(t,e,n)=>{const r=typeof e.adjustDataValueDisplay=="function"?e.adjustDataValueDisplay(n,e.data[n]):e.data[n];return window.officers?officers.adjustDataDisplay(t,e,n,r):r},dt=(t,e,n)=>{if(typeof e=="string"&&e.indexOf("<br>")!==-1){const r=e.split("<br>");return a.jsxs(N,{children:[a.jsxs(ne,{children:[t,": "]}),r.map((i,o)=>a.jsxs(I.Fragment,{children:[o>0&&a.jsx("br",{}),i]},o))]},n)}return a.jsxs(N,{children:[a.jsxs(ne,{children:[t,": "]}),e]},n)},hd=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left+t.width,e},qS=300,_s=8;function ZS(t,e,n,r){if(!t||!e)return null;const i=n-e.right-_s,o=e.left-_s;if(Math.max(i,o)<qS)return null;const s={};return i>=o?s.left=e.right:s.right=n-e.left,t.top>r/2?s.bottom=Math.max(_s,r-t.bottom):s.top=Math.max(_s,t.top),s}u(Uf)`
    font-size: 12px;
`;const ek=u(Wf)`
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
`;u(Gf)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,u.span`
    color: white;
`;class tk extends I.Component{componentDidMount(){this.markTarget()}componentDidUpdate(){this.markTarget()}componentWillUnmount(){qg(null,null)}markTarget(){const{ship:e,system:n,avoidBox:r}=this.props,i=!!r&&Rs(e,n);qg(i?e:null,i?n:null)}render(){const{ship:e,system:n,boundingBox:r,avoidBox:i}=this.props;return Rs(e,n)?a.jsx(ek,{position:ZS(r,i,nk(),rk())||ik(r),opacity:vS(e,n)?.95:.8,$bare:gamedata.gamephase===-2,children:a.jsx(fS,{...this.props})}):null}}const nk=()=>document.documentElement.clientWidth||window.innerWidth,rk=()=>document.documentElement.clientHeight||window.innerHeight,ik=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e},ok={0:"Primary",1:"Forward",2:"Aft",3:"Port",4:"Starboard",5:"",31:"Port Fwd",32:"Port Aft",41:"Stbd Fwd",42:"Stbd Aft"},sk=u.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${g.colors.panelBgGlass};
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

    border: ${t=>{switch(t.$location){case 0:return`1px solid ${g.colors.line}`;default:return`1px dotted ${g.colors.line}`}}};
`,ak=u.div`
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
    border-bottom: 1px solid ${g.colors.healthOk};
    overflow: hidden;
    /*lobby pre-battle damage: the bar is the only way to reach a section's Structure,
      which has no icon of its own in the grid - and (OFFICERS_PLAN.md Stage 19) an Expert Targeter's menu or pick*/
    ${t=>t.$damageable?"cursor: pointer;":""}
    /*A block the officer being moved may go to (officers.isPickTarget, Stage 19): SystemIcon's pick outline, in the Officers
      blue, drawn on the bar's own edge (a 15px bar has no room for it inside)*/
    ${t=>t.$officerPick?`
        cursor: pointer;
        outline: 2px solid ${W.officer.bar};
        outline-offset: 0;
        box-shadow: 0 0 8px ${W.officer.bar};
    `:""}
    /*The lobby's system menu is open on this block (helpers/menuTarget - SystemIcon's outline, user request 2026-10-20), in
      the menu's focus blue, on the bar's edge as the pick outline is*/
    ${t=>t.$menuTarget?`
        outline: 2px solid ${P.focus};
        outline-offset: 0;
        box-shadow: 0 0 8px ${P.focus};
    `:""}

    /*structure health fill - the header line doubles as the section's health bar*/
    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: ${t=>t.$health}%;
        background-color: ${t=>t.$criticals?g.colors.healthCrit:g.colors.healthOk};
    }
`,lk=u.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${g.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
    /*A bar with badges after its name (Stage 19: the ✦, an officer's insignia) can run out of room - a Starboard block of 100+
      boxes in a 128px side column. The NAME gives way, with an ellipsis, so the badges and the readout stay whole. The padding
      and the negative margin cancel out: they only give the text shadow room inside the clipped box.*/
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 3px 2px;
    margin: -3px -2px;
`,ck=u.span`
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 3px;
    min-width: 0;
`,dk=u.span`
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: 1px;
`,uk=u.span`
    position: relative;
    z-index: 1;
    flex-shrink: 0; /*the readout never gives way - the name does (SectionName)*/
    font-family: ${g.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${t=>t.$destroyed?"transparent":g.colors.text};
    filter: ${t=>t.$destroyed?"blur(1px)":"none"};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,hk=u.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;class gm extends I.Component{constructor(e){super(e),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(e){const{ship:n,systems:r}=this.props,i=pd(r);if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(i&&window.officers&&officers.picking&&officers.pickTarget(n,i)){e.stopPropagation(),this.hideStructureArc();return}if(i&&window.officers&&!cn(n,i)&&officers.canManage(n,i)){e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:n,system:i,element:e.currentTarget,showMenu:!0});return}if(qc(n)){e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("MineDamageClicked",{ship:n,element:e.currentTarget});return}!i||!cn(n,i)||(e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:n,system:i,element:e.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.hideStructureArc()}showStructureArc(){const{ship:e,systems:n}=this.props;this.arcShown=!0,window.uiEvents.relay("StructureMouseOver",{ship:e,structure:pd(n),element:this.headerElement})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay("StructureMouseOut"))}onStructureMouseOver(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.showStructureArc())}onStructureMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(e){e.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.touches[0];this.touchStartX=n.clientX,this.touchStartY=n.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;const n=e.touches[0];(Math.abs(n.clientX-this.touchStartX)>10||Math.abs(n.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(e){e.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,this.hideStructureArc()}render(){const{ship:e,systems:n,location:r,displayLocation:i,area:o,valign:s,justify:l,wide:c,isTerrain:d,minHeight:h,nameOverride:p,hidden:f}=this.props,v=pd(n),x=qc(e),b=x?battleDamage.mineHealth(e,1):0,M=x?battleDamage.mineMaxHealth(e):0,w=x?b/M*100:v?pk(e,v):0,m=i!==void 0?i:r,y=i!==void 0&&i!==r,S=v&&window.officers?officers.postState(e,v.id):null,T=!!(v&&window.officers)&&officers.isPickTarget(e,v),C=!!(v&&window.officers)&&officers.canManage(e,v),k=!!(v&&window.systemEnhancements)&&systemEnhancements.hasAny(e,v.id),$=p||ok[r]||"",D=(k||S)&&$==="Starboard"?"Stbd":$;return a.jsxs(sk,{$location:r,$area:o,$valign:s,$justify:l,$wide:c,$isTerrain:d,$minHeight:h,$hidden:f,children:[v&&a.jsxs(ak,{ref:E=>{this.headerElement=E},$health:w,$criticals:fk(v),$damageable:cn(e,v)||qc(e)||C,$officerPick:T,$menuTarget:Zg(e,v),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[a.jsxs(ck,{children:[a.jsx(lk,{children:D}),(k||S)&&a.jsxs(dk,{children:[k&&a.jsx(nm,{title:"Carries a system enhancement",children:"✦"}),S&&a.jsx(od,{state:S,title:officers.describePost(e,v.id)})]})]}),a.jsxs(uk,{$destroyed:w===0,children:[x?b:v.maxhealth-damageManager.getDamage(e,v),"/",x?M:v.maxhealth," A",shipManager.systems.getArmour(e,v)]})]}),a.jsx(hk,{children:gk(n,m,c).map(E=>a.jsx(sd,{scs:!0,mirror:y,system:E,ship:e},`system-scs-${r}-${e.id}-${E.id}`))})]})}}const pk=(t,e)=>(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,fk=t=>shipManager.criticals.hasCriticals(t),mm=t=>t.name==="structure",pd=t=>t.find(mm),Hs=t=>t.filter(e=>!mm(e)),gk=(t,e,n)=>(t=Hs(t),n?fd(t):[4,41,42].includes(e)?gd(t):[3,31,32].includes(e)?mk(gd(t)):[1,2,0].includes(e)?fd(t):xk(t)),mk=t=>{let e=[];return t.forEach((n,r)=>{const i=r%3;i===0?e[r+2]=n:i===1?e[r]=n:e[r-2]=n}),e},xk=t=>(t=Hs(t),t.length===3?gd(t):t.length===4?fd(t):t),fd=t=>{t=Hs(t);let e=[];for(;;){const{picked:n,remaining:r}=xd(t,4);if(n.length===0)break;t=r,e=e.concat(n)}for(;;){const{picked:n,remaining:r}=xd(t,2);if(n.length===0)break;t=r;const i=xd(t,2);i.picked.length>0?(t=i.remaining,e=e.concat([n[0],i.picked[0],i.picked[1],n[1]])):(e=e.concat([n[0],t.shift(),t.shift(),n[1]]),e=e.filter(o=>o))}return e=e.concat(t),e},gd=t=>{t=Hs(t);let e=[];for(;;){const{picked:n,remaining:r}=md(t,3);if(n.length===0)break;t=r,e=e.concat(n)}for(;;){const{picked:n,remaining:r}=md(t,2);if(n.length===0)break;const{three:i,remainingSystems:o}=yk(n,r);t=o,e=e.concat(i)}return e=e.concat(t),e},yk=(t,e)=>{const n=md(e,1);return n.picked.length===1?{three:[n.picked[0],t[0],t[1]],remainingSystems:n.remaining}:e.length>0?{three:[e.shift(),t[0],t[1]],remainingSystems:e}:{three:[t[0],t[1]],remainingSystems:e}},md=(t,e=3)=>{const n=t.find(o=>{const s=t.reduce((l,c)=>c.name===o.name?l+1:l,0);return e===1?s===e:s>=e});if(!n)return{picked:[],remaining:t};let r=[];const i=t.filter(o=>o.name===n.name&&e>0?(e--,r.push(o),!1):!0);return{picked:r,remaining:i}},xd=(t,e=3)=>{const n=t.find(d=>{const h=t.reduce((p,f)=>f.name===d.name?p+1:p,0);return e===1?h===e:h>=e});if(!n)return{picked:[],remaining:t};let r=[],i=[];const o=t.filter(d=>d.name===n.name?(i.push(d),!1):!0);for(var s=Math.ceil(e/2),l=Math.floor(e/2),c=0;c<i.length;c++)c<s||c>=i.length-l?r.push(i[c]):o.unshift(i[c]);return{picked:r,remaining:o}},xm={DEW:"#aecdea",CCEW:g.colors.text,SDEW:"#9ac1e5",OEW:"#acd7a8",BDEW:"#8ac785","Detect Mines":"#bfa3db","Detect Stealth":"#ccb6e2",DIST:"#e6b98f",SOEW:g.colors.text,OEW_HOSTILE:"#e49b9b","Saved EW":"#e0d39a",ELINT:"#9ad4cf"},At=(t,e)=>t==="OEW"&&e&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(e)?xm.OEW_HOSTILE:xm[t]||g.colors.textAccent,ym=u.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${t=>t.$flight?"":$r`
        grid-area: ew;
        justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    `}
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${g.colors.panelBgGlass};
    border: 1px solid ${g.colors.line};
    padding: 1px 4px 1px;
`,wm=u.div`
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
    color: ${g.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${g.colors.line};
`,Rt=u.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${g.colors.text};
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
    ${t=>t.$target&&$r`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${t=>t.$hoverable&&$r`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,wk=u.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,It=u.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${t=>t.$color||g.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,Ot=u.span`
    font-family: ${g.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,vk=u.span`
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
    color: ${g.colors.textAccent};
    ${t=>t.$interactive&&$r`
        cursor: pointer;
        &:hover {
            color: ${g.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;class vm extends I.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(e,n){window.webglScene&&(window.uiEvents.relay("EwRangeHover",{shipId:this.props.ship.id,type:e,active:n}),this.activeRangeOverlay=n?e:null)}onTargetClick(e,n){n.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(e)||window.uiEvents.relay("ScrollToShip",{shipId:e.id}))}setTargetHighlight(e,n,r){window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:e.id,type:n,active:r}),this.activeHighlight=r?{targetId:e.id,type:n}:null)}render(){const{ship:e,flight:n}=this.props;return n?a.jsxs(ym,{$flight:!0,children:[a.jsx(wm,{children:"Electronic Warfare"}),bk(e),Sm(e,this)]}):a.jsxs(ym,{children:[a.jsx(wm,{children:"Electronic Warfare"}),Sk(e,this),Sm(e,this)]})}}const bk=t=>{const e=[a.jsxs(Rt,{children:[a.jsx(It,{$color:At("DEW"),children:"DEW"}),a.jsx(Ot,{children:ue(ew.getFlightDEW(t))})]},`dew-scs-${t.id}`)],n=bm(t);return n&&e.push(n),e},bm=t=>{if(!gamedata.isPlayerInGame()||!gamedata.isMyorMyTeamShip(t))return null;const e=ew.getSavedEwAllowance(t);if(e<=0)return null;const r=ew.isLateEwWindowOpen(t)||ew.isLateEwPhase()&&gamedata.isMyShip(t)?`${ue(ew.getLateEwRemaining(t))} / ${ue(e)}`:ue(e);return a.jsxs(Rt,{children:[a.jsx(It,{$color:At("Saved EW"),children:"Saved EW"}),a.jsx(Ot,{children:r})]},`savedew-scs-${t.id}`)},Sk=(t,e)=>{let n=[];const r=!!window.webglScene,i=h=>r?{$hoverable:!0,onMouseEnter:()=>e.setRangeOverlay(h,!0),onMouseLeave:()=>e.setRangeOverlay(h,!1)}:{};if(n.push(a.jsxs(Rt,{children:[a.jsx(It,{$color:At("DEW"),children:"DEW"}),a.jsx(Ot,{children:ue(ew.getDefensiveEW(t))})]},`dew-scs-${t.id}`)),r&&ew.hasElintModules(t)&&gamedata.isMyorMyTeamShip(t)){const h=ew.getElintModuleSplit(t);let p=0;ew.getElintModules(t).forEach(v=>{p+=v.elintLost||0});const f=gamedata.gamephase==1?`${ue(h.freeM)} / ${ue(h.M)}`:p>0?`${ue(h.M)} (lost ${p})`:ue(h.M);n.push(a.jsxs(Rt,{children:[a.jsx(It,{$color:At("ELINT"),children:"ELINT Modules"}),a.jsx(Ot,{children:f})]},`elint-scs-${t.id}`))}var o=Math.max(0,ew.getCCEW(t)-ew.getDistruptionEW(t));o>0&&n.push(a.jsxs(Rt,{children:[a.jsx(It,{$color:At("CCEW"),children:"CCEW"}),a.jsx(Ot,{children:ue(o)})]},`ccew-scs-${t.id}`));let s=ew.getBDEW(t)*.25,l=ew.getDetectSEW(t),c=ew.getDetectMEW(t);shipManager.hasSpecialAbility(t,"ConstrainedEW")&&(s=ew.getBDEW(t)*.2),s&&n.push(a.jsxs(Rt,{...i("BDEW"),children:[a.jsx(It,{$color:At("BDEW"),children:"BDEW"}),a.jsx(Ot,{children:ue(s)})]},`bdew-scs-${t.id}`)),c&&n.push(a.jsxs(Rt,{...i("MDEW"),children:[a.jsx(It,{$color:At("Detect Mines"),children:"Detect Mines"}),a.jsx(Ot,{children:ue(c)})]},`DetectMEW-scs-${t.id}`)),l&&n.push(a.jsxs(Rt,{children:[a.jsx(It,{$color:At("Detect Stealth"),children:"Detect Stealth"}),a.jsx(Ot,{children:ue(l)})]},`DetectSEW-scs-${t.id}`));const d=bm(t);return d&&n.push(d),n},Sm=(t,e)=>{const n=!!window.webglScene;return t.EW.filter(r=>r.turn===gamedata.turn).filter(r=>r.type==="OEW"||r.type==="DIST"||r.type==="SOEW"||r.type==="SDEW").map(r=>{const i=gamedata.getShip(r.targetid);return a.jsxs(Rt,{$target:!0,children:[a.jsxs(wk,{children:[a.jsx(It,{$color:At(r.type,t),children:r.type}),a.jsx(vk,{$interactive:n,title:void 0,onClick:n?e.onTargetClick.bind(e,i):void 0,onMouseEnter:n?()=>e.setTargetHighlight(i,r.type,!0):void 0,onMouseLeave:n?()=>e.setTargetHighlight(i,r.type,!1):void 0,children:i.name})]}),a.jsx(Ot,{children:kk(r,t)})]},`${r.type}-scs-${t.id}-${r.targetid}`)})},kk=(t,e)=>{switch(t.type){case"SDEW":if(shipManager.hasSpecialAbility(e,"ConstrainedEW")){let n=t.amount*.333;return n=Math.round(n*3)/3,ue(n)}else return ue(t.amount*.5);case"DIST":return shipManager.hasSpecialAbility(e,"ConstrainedEW")?ue(t.amount/4):ue(t.amount/3);case"OEW":return ue(Math.max(0,t.amount-ew.getDistruptionEW(e)));default:return ue(t.amount)}},ue=t=>Math.round(t*100)/100,Ws=()=>!!window.gamedata&&window.gamedata.gamephase===-2,$k=t=>{let e=!1,n=0;for(const r in t.damage){const i=t.damage[r];if(i.damageclass==="HyperspaceJump"){e=!0;continue}n+=Math.max(0,i.damage-i.armour)}return e&&n<t.maxhealth},Ck=u.div`
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
`,Tk=u.div`
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
`,Er=u.div`
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
`,Mk=u.div`
    position: absolute;
    top: -5px;
    left: -5px;
    z-index: 3;
    pointer-events: none;
`,km=u.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,jk=u(km)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,Ek=u.div`
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
`,Dk=u.div`
    z-index: 1;
`;class Pk extends I.Component{onSystemMouseOver(e){if(Ws()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||e.nativeEvent&&e.nativeEvent.sourceCapabilities&&e.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:n,fighter:r}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:n,system:n,craft:r,element:e.target})}onSystemMouseOut(){Ws()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onFighterTouchStart(e){if(Ws())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:i,fighter:o}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:i,system:i,craft:o,element:n,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(e){if(!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(e){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onFighterTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){const{ship:e}=this.props,n=window.gamedata&&typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted();return Ws()&&!n&&!!e&&e.userid!=0&&!!e.flight}onHealthBarClick(e){this.canApplyPreBattleDamage()&&(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay("FighterDamageClicked",{ship:this.props.ship,fighter:this.props.fighter,element:e.currentTarget}))}render(){const{ship:e,fighter:n}=this.props,r=shipManager.systems.isDestroyed(e,n),i=shipManager.criticals.isDockedFighter(n),o=!i&&shipManager.criticals.isSplitLaunchedFighter(n),s=!i&&!o&&shipManager.criticals.isDisengagedFighter(n),l=shipManager.criticals.isCutOffFighter(n);let c=null;r?i?c=a.jsx(Er,{$color:"#00b8e6",children:"DOCKED"}):o?c=a.jsx(Er,{$color:"#00b8e6",children:"SPLIT"}):s?c=a.jsx(Er,{$color:"#ff8c00",children:"DROPOUT"}):$k(n)?c=a.jsx(Er,{$color:"#cc8500",children:"JUMPED"}):c=a.jsx(Er,{$color:"#ff5252",children:"DESTROYED"}):l&&(c=a.jsx(Er,{$color:"#ff5252",children:"CUT OFF"}));const d=this.canApplyPreBattleDamage(),h=d?battleDamage.fighterHealth(e,1)/n.maxhealth*100:Ak(e,n),p=d?`${battleDamage.fighterHealth(e,1)} / ${n.maxhealth}`:`${n.maxhealth-damageManager.getDamage(e,n)} / ${n.maxhealth}`,f=window.officers?officers.postState(e,n.id):null;return a.jsxs(Ck,{$docked:i,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[a.jsxs(Tk,{$destroyed:r,$img:window.AssetManager.getSmartImagePath(n.iconPath),children:[a.jsx(km,{children:$m(e,n,Ok(n),r)}),a.jsx(jk,{children:$m(e,n,Lk(n),r)}),a.jsx(Ek,{$health:h,$criticals:Rk(n),$criticalsBenign:Ik(n),$docked:i,$clickable:d,title:d?"Apply pre-battle damage to this flight":void 0,onClick:this.onHealthBarClick.bind(this),children:a.jsx(Dk,{children:p})})]}),c,f&&a.jsx(Mk,{children:a.jsx(od,{state:f,title:officers.describePost(e,n.id)})})]})}}const Ak=(t,e)=>(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,Rk=t=>shipManager.criticals.hasCriticals(t),Ik=t=>shipManager.criticals.hasOnlyCritical(t,"LaunchedThisTurn",!1),Ok=t=>t.systems.filter(e=>e.location==1),Lk=t=>t.systems.filter(e=>e.location!=1),$m=(t,e,n,r)=>n.map((i,o)=>a.jsx(sd,{$destroyed:r,fighter:!0,scs:!0,system:i,ship:t},`system-scs-fighter${e.id}-${t.id}-${i.id}-${o}`)),zk=u.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;class yd extends I.Component{render(){const{ship:e}=this.props;return a.jsx(zk,{children:Bk(e)})}}const Bk=t=>t.systems.map((e,n)=>a.jsx(Pk,{fighter:e,ship:t},`flight-${t.id}-${n}`)),Nk=[3,31,32],Fk=[1,0,2],_k=[4,41,42],Hk=u.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,wd=u.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${t=>t.$side?"center":"flex-start"};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,Wk=u.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${g.colors.line};
    padding: 3px 5px;
`,Uk=u.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${g.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${g.colors.line};
`,Gk=u.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${g.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`,Vk=u.span`
    font-family: ${g.fonts.mono};
    color: ${g.colors.text};
    flex-shrink: 0;
`,Yk=(t,e)=>a.jsxs(Wk,{children:[a.jsx(Uk,{children:e.name}),[...e.entries].sort((n,r)=>r.chance-n.chance).map((n,r)=>a.jsxs(Gk,{children:[a.jsx("span",{children:n.name}),a.jsxs(Vk,{children:[n.chance,"%"]})]},`hitchart-${t.id}-${e.location}-${r}`))]},`hitchart-${t.id}-${e.location}`);class Kk extends I.Component{render(){const{ship:e}=this.props,n=cm(e);if(n.length===0)return null;const r={};n.forEach(c=>{r[c.location]=c});const i=c=>c.filter(d=>r[d]).map(d=>Yk(e,r[d])),o=i(Nk),s=i(Fk),l=i(_k);return a.jsxs(Hk,{children:[o.length>0&&a.jsx(wd,{$side:!0,children:o}),s.length>0&&a.jsx(wd,{children:s}),l.length>0&&a.jsx(wd,{$side:!0,children:l})]})}}const Qk=t=>t.split(" ").map(e=>e.charAt(0).toUpperCase()+e.slice(1)).join(" "),Xk=t=>{const e=[];if(!t||t.flight)return e;const n=t.fighters||{};if(Object.keys(n).length>0){const i={};if(shipManager.systems.shipHasRestrictedHangar(t)){const o=shipManager.systems.getReservedFighterComposition(t);for(let s=0;s<o.length;s++){const l=o[s].category;i[l]||(i[l]=[]);const c=i[l];let d=!1;for(let h=0;h<c.length;h++)if(c[h].phpclass===o[s].phpclass){c[h].count+=o[s].count,d=!0;break}d||c.push({category:o[s].category,phpclass:o[s].phpclass,displayName:o[s].displayName,count:o[s].count,isGroup:o[s].isGroup})}}for(const o in n){const s=n[o],l=Qk(o),c=i[o];if(c&&(o==="heavy"||o==="medium"||o==="light")){let d=s;for(let h=0;h<c.length;h++){const p=Math.min(c[h].count,d);p<=0||(c[h].isGroup?e.push(p+" "+c[h].displayName+"s"):e.push(p+" "+c[h].displayName+" "+l+" Fighters"),d-=p)}d>0&&e.push(d+" "+l+" Fighters");continue}if(o==="normal")e.push(s+" Fighters");else if(o==="superheavy"||o==="heavy"||o==="medium"||o==="light"||o==="ultralight")e.push(s+" "+l+" Fighters");else{if(o==="shuttles"||o==="minesweeping shuttles"||o==="cargo shuttles"||o==="lifeboats"||o==="medical shuttles"||o==="presidential shuttle"||o==="yacht")continue;e.push(s+" "+l)}}}const r=shipManager.systems.getDefaultShuttleComposition(t);for(let i=0;i<r.length;i++)e.push(r[i].count+" "+r[i].type);return e},Jk=u.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${g.colors.textAccent};
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
`,zi=u.div`
    background-color: ${g.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${t=>t.$gold?g.colors.enhLine:g.colors.line};
    padding: 0 8px 3px;
    /*$gold: the enhancement lines in the gold body text on every window (user request 2026-10-11).
      The grid's EnhancementsPanel got it from its EnhArea wrapper, but a flight's block sits in
      the blue Rail and read blue; set here, both read the same.*/
    ${t=>t.$gold?`color: ${g.colors.enhText};`:""}
`,vd=u.div`
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
    color: ${t=>t.$gold?g.colors.enhTitle:g.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${t=>t.$gold?g.colors.enhBg:"rgba(73, 103, 145, 0.25)"};
    border-bottom: 1px solid ${t=>t.$gold?g.colors.enhLine:g.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,In=u.div`
    padding: 1px 0;
`,qk=u.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${g.colors.custom};
`,Re=u.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,Ie=u.span`
    font-size: 10px;
    color: ${g.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,Oe=u.span`
    font-family: ${g.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${t=>t.$changed?g.colors.custom:g.colors.text};
    margin-right: 5px;
`,Zk=u.div`
    width: 150px;
    box-sizing: border-box;
    ${t=>t.$bare?`
    padding: 0;`:`
    background-color: ${g.colors.panelBgGlass};
    border: 1px dotted ${g.colors.line};
    padding: 2px 4px 3px;`}
`,e$=u.div`
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
    color: ${g.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${g.colors.line};
`,Cm=u.span`
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
        background-color: ${g.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;u.div`
    text-align: center;
    font-size: 10px;
    color: ${g.colors.warning};
    padding-top: 2px;
`;const Tm=u.div`
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
    color: ${g.colors.enhTitle};
    background-color: ${g.colors.enhBg};
    border-bottom: 1px solid ${g.colors.enhLine};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,t$=u.div`
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
    color: ${g.colors.enhText};
`,bd=t=>typeof t=="number"?t.toFixed(2):t,On=(t,e)=>window.officers?officers.displayStat(t,e):t[e],Sd=(t,e)=>t*5+"/"+e*5,Us=(t,e)=>t+" ("+bd(e)+")",n$=t=>{const e=window.shipManager;if(!e)return null;const n={},r=e.systems?e.systems.getSystemByName(t,"CnC"):null,i=r&&e.criticals?e.criticals.hasCritical(r,"ProfileIncreased"):0;n.profile=Sd(t.forwardDefense+i,t.sideDefense+i),n.profileChanged=i!==0;const o=t.iniativeadded||0;n.initiative=(t.iniativebonus||0)+o,n.initiativeChanged=o!==0;const s=e.movement;if(s&&typeof s.getTurnCost=="function"&&t.movement&&t.movement.length>0){const l=s.getSpeed(t),c=s.getDockedLcvTurnSurcharge(t),d=s.getTurnDelayCost(t);let h=s.getTurnCost(t);t.submarine&&s.isGoingBackwards(t)&&(h=h*1.33);const p=Math.max(1,Math.ceil(l*h))+c,f=Math.ceil(l*d),v=s.applyCrewTurnDelay(t,typeof s.getQuartermasterTurnDelay=="function"?s.getQuartermasterTurnDelay(t,f):f)+c;n.turnCost=Us(p,h),n.turnDelay=Us(v,d),n.turnCostChanged=n.turnCost!==Us(Math.max(1,Math.ceil(l*t.turncost)),t.turncost),n.turnDelayChanged=n.turnDelay!==Us(Math.ceil(l*t.turndelaycost),t.turndelaycost)}return n},Mm=({ship:t,live:e,bare:n})=>{const r=!t.base,i=e?n$(t):null;return a.jsxs(Zk,{$bare:n,children:[!n&&a.jsxs(e$,{children:[a.jsxs(Cm,{children:[a.jsx("i",{}),a.jsx("i",{}),a.jsx("i",{})]}),"Ship Stats"]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn cost"}),a.jsx(Oe,{$changed:!!(i&&i.turnCostChanged),children:i&&i.turnCost?i.turnCost:bd(On(t,"turncost"))})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn delay"}),a.jsx(Oe,{$changed:!!(i&&i.turnDelayChanged),children:i&&i.turnDelay?i.turnDelay:bd(t.turndelaycost)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Accel/decel"}),a.jsx(Oe,{children:On(t,"accelcost")})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Pivot"}),a.jsx(Oe,{children:On(t,"pivotcost")})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Roll"}),a.jsx(Oe,{children:On(t,"rollcost")})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Profile - Front / Side"}),a.jsx(Oe,{$changed:!!(i&&i.profileChanged),children:i?i.profile:window.officers?Sd(officers.displayStat(t,"forwardDefense"),officers.displayStat(t,"sideDefense")):Sd(t.forwardDefense,t.sideDefense)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Initiative"}),a.jsx(Oe,{$changed:!!(i&&i.initiativeChanged),children:i?i.initiative:window.officers?officers.displayIniativeBonus(t):t.iniativebonus})]})]})},r$=({ship:t})=>{const e=$d(t.enhancementTooltip),n=kd(t);return e.length===0&&!n?null:a.jsx(t$,{children:a.jsxs(zi,{$gold:!0,children:[a.jsx(Tm,{children:"Enhancements"}),e.map((r,i)=>a.jsx(In,{children:r},`enh-${i}`)),n&&a.jsx(In,{children:n},"officers")]})})},kd=t=>window.officers&&t?officers.summaryLine(t):null,$d=t=>(t||"").split(/<br\s*\/?>/i).map(e=>e.replace(/<[^>]*>/g,"").replace(/&nbsp;/g," ").trim()).filter(Boolean);class Cd extends I.Component{render(){const{ship:e,full:n,grid:r,hideEnhancements:i}=this.props,o=Xk(e),s=$d(e.notes),l=i?[]:$d(e.enhancementTooltip),c=i?null:kd(e),d=[];if(e.limited&&e.limited!=0&&d.push("Limited: "+e.limited+"%"),e.variantOf){const f=e.occurence?e.occurence.charAt(0).toUpperCase()+e.occurence.slice(1)+" ":"";d.push(f+"variant of "+e.variantOf)}e.isd&&d.push("In-Service (ISD): "+e.isd);let h=null;e.unofficial==="S"?h="Semi-Custom":e.unofficial&&(h="Custom");const p=s.length>0||d.length>0||h;return a.jsxs(Jk,{$full:n,$grid:r,children:[e.flight&&a.jsxs(zi,{children:[a.jsx(vd,{children:"Flight Stats"}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Armor F/S/A"}),a.jsx(Oe,{children:shipManager.systems.getFlightArmour(e)})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Offensive bonus"}),a.jsx(Oe,{children:On(e,"offensivebonus")*5})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Profile - Front / Side"}),a.jsxs(Oe,{children:[e.forwardDefense*5,"/",e.sideDefense*5]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Thrust"}),a.jsx(Oe,{children:On(e,"freethrust")})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn Cost / Delay"}),a.jsxs(Oe,{children:[e.turncost," / ",e.turndelaycost==0?0:e.turndelaycost]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Accel. / Pivot / Roll"}),a.jsxs(Oe,{children:[e.accelcost," / ",e.pivotcost," / ",e.rollcost]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Initiative"}),a.jsx(Oe,{children:On(e,"iniativebonus")})]})]}),o.length>0&&a.jsxs(zi,{children:[a.jsx(vd,{children:"Hangar Capacity"}),o.map((f,v)=>a.jsx(In,{children:f},`comp-${v}`))]}),p&&a.jsxs(zi,{children:[a.jsx(vd,{children:"Notes"}),s.map((f,v)=>a.jsx(In,{children:f},`note-${v}`)),d.map((f,v)=>a.jsx(In,{children:f},`meta-${v}`)),h&&a.jsx(qk,{children:h})]}),(l.length>0||c)&&a.jsxs(zi,{$gold:!0,children:[a.jsx(Tm,{children:"Enhancements"}),l.map((f,v)=>a.jsx(In,{children:f},`enh-${v}`)),c&&a.jsx(In,{children:c},"officers")]})]})}}const i$=400,o$=u.div`
    display: flex;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 4px 0 0;
`,s$=u.div`
    flex: 0 1 auto;
    min-width: 0;
    width: max-content;
    max-width: ${i$}px;
`,Bi=u.div.attrs({"data-ship-window":""})`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${t=>t.$isMyTeam?`left: 50px; 
 top: 50px;`:`right: 50px; 
 top: 50px;`}
    width: ${t=>t.$variant==="terrain"?"250px":t.$variant==="flight"||t.$variant==="flightEw"?"auto":"fit-content"};
    max-width: ${t=>t.$variant==="flight"?"400px":t.$variant==="flightEw"?"574px":t.$variant==="flightLobby"?"620px":"unset"};
    height: auto;
    border: 1px solid ${g.colors.line};
    background-color: ${g.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${g.colors.text};
    font-family: ${g.fonts.body};

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
`,a$=u.div`
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
    background-color: ${g.colors.panelBg};
    border-bottom: 1px solid ${g.colors.line};
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
`,l$=u.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${t=>t.$tint||g.colors.text};
`,c$=u.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${g.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,d$=u.div`
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
    color: ${g.colors.line};
    ${We}
`,u$=u.div`
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
        background: repeating-linear-gradient(${t=>t.$mirror?"45deg":"315deg"}, ${g.colors.line} 0 1.5px, transparent 1.5px 4px);
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
`,h$=u.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${t=>t.$compact?"width: 100%; align-items: center; margin-bottom: 5px;":"align-items: stretch;"}
    gap: 4px;
`,Gs=u.div`
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
    border: 1px solid ${g.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${t=>t.$active?"rgba(198, 226, 255, 0.12)":"rgba(73, 103, 145, 0.25)"};
    color: ${g.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${We}
`,jm=u.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,p$=u.span`
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
`,Td=u.div`
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
    background-color: ${g.colors.panelBg};
    border: 1px solid ${g.colors.line};
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
`,f$=u.div`
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    grid-template-areas: ${t=>t.$areas};
    justify-content: center;
    gap: 8px;
    /*staggered big base: the two rows meet exactly on Primary's centre line (StaggerCell)*/
    ${t=>t.$staggered?"row-gap: 0;":""}
    padding: 5px 5px 5px 5px;
    box-sizing: border-box;
    width: 100%;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`,g$=u.div`
    grid-area: ${t=>t.$area};
    display: flex;
    flex-direction: column;
    gap: 8px; /*SectionGrid's own gap, so a cell keeps the grid's spacing*/
    align-items: ${t=>t.$side==="left"?"flex-end":t.$side==="right"?"flex-start":"center"};
    justify-content: ${t=>t.$upper?"flex-end":"flex-start"};
    ${t=>t.$side?`padding-${t.$upper?"bottom":"top"}: 4px;`:t.$upper?"":"padding-top: calc(var(--fv-prim-half, 0px) + 8px);"}
`,m$=u.div`
    margin-bottom: calc(-1 * var(--fv-prim-half, 0px));
`,Em=u.div`
    display: flex;
    justify-content: center;
    align-self: stretch;
    ${t=>t.$top?"margin-bottom: auto;":""}
`,x$=u.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,y$=u.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,Md=u.div`
    position: absolute;
    top: 50%;
    left: 50%;
    height: 80%;
    width: auto;
    aspect-ratio: 1 / 1;
    max-width: 80%;
    max-height: 380px;
    /*$offsetVar (staggered big base): a measured nudge instead of a fixed one - the art centres
      on Primary, which sits off the grid's midline when the two halves differ in height*/
    transform: translate(-50%, calc(-50% + ${t=>t.$offsetVar?`var(${t.$offsetVar}, 0px)`:`${t.$offsetY||0}px`})) rotate(-90deg);
    background-image: ${t=>`url(${t.$img})`};
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    filter: ${t=>t.$art?"none":"grayscale(1) brightness(2.2)"};
    opacity: ${t=>t.$art?1:.75};
    pointer-events: none;
    z-index: 0;
`,jd=u.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,w$=u.div`
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
    color: ${t=>t.$color||g.colors.warning};
    background-color: ${t=>t.$bg||"rgba(225, 176, 0, 0.10)"};
    border-top: 1px solid ${g.colors.line};
    flex-shrink: 0;
`,Dm=u.div`
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
`,v$=u.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${g.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${g.fonts.body};
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
`,b$={1:"fwd",2:"aft",0:"prim",3:"left",4:"right",31:"lfwd",41:"rfwd",32:"laft",42:"raft"},Pm={3:4,4:3,31:41,41:31,32:42,42:32},S$={fwd:"end",aft:"center",prim:"center",left:"center",right:"center",lfwd:"start",rfwd:"start",laft:"end",raft:"end"},k$={left:"end",lfwd:"end",laft:"end",right:"start",rfwd:"start",raft:"start"},$$=[1,3,31,32,0,4,41,42,2],C$=[1,3,31,0,4,41,32,2,42],Am=[31,32,41,42],T$='"lup  cup  rup" "llo  clo  rlo"',M$=[{area:"lup",side:"left",upper:!0,stack:["lfwd","left"]},{area:"cup",upper:!0,stack:["fwd","prim"]},{area:"rup",side:"right",upper:!0,stack:["rfwd","right"]},{area:"llo",side:"left",stack:["laft"]},{area:"clo",stack:["aft"]},{area:"rlo",side:"right",stack:["raft"]}],j$=[3,4,31,41,32,42];class E$ extends I.Component{constructor(e){super(e),this.elementRef=I.createRef(),this.controlsRef=I.createRef(),this.popupRef=I.createRef(),this.hitChartBtnRef=I.createRef(),this.sectionGridRef=I.createRef(),this.staggerPrimaryRef=I.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return Ks(this.props.ship)?"left":"right"}isMirroredGrip(){return this.side()==="right"}applyScreenFit(){const e=this.elementRef.current;if(!e)return;const n=Im(),r=Ys(this.side());if(!n&&r===1){(this.screenFit!==1||this.screenFitHeight)&&(e.style.transform="",e.style.transformOrigin="",e.style.maxHeight="",this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}const i=this.measureNatural();if(!i)return;const o=n?L$():null,s=window.innerHeight||document.documentElement.clientHeight;let l=1;if(n){const h=(document.documentElement.clientWidth||window.innerWidth)*o.fillW,p=s*o.fillH;l=Math.min(h/i.width,p/i.height),l=Math.min(o.max,Math.max(o.min,l))}this.autoFit=l;let c=Pd(l*r);c=Math.round(c*100)/100;let d=null;if(n){const h=Math.min(Ed,o.fillH*Math.max(1,r));d=Math.round(s*h/c)}c===this.screenFit&&d===this.screenFitHeight||(this.screenFit=c,this.screenFitHeight=d,e.style.transformOrigin=this.transformOrigin(),e.style.transform=c===1?"":"scale("+c+")",e.style.maxHeight=d==null?"":d+"px",this.resizeStart||this.keepGripOnScreen())}measureNatural(){const e=this.elementRef.current;if(!e)return null;const n=e.style.maxHeight;e.style.maxHeight="none";const r=e.offsetWidth,i=e.offsetHeight;return e.style.maxHeight=n,r&&i?{width:r,height:i}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:Ks(this.props.ship)?"top left":"top right"}onScreenResize(){this.applyScreenFit()}isDragHandle(e){return!e||!e.closest||e.closest(".shipwindow-nodrag")?!1:!!e.closest(".shipwindow-drag-handle")}isResizeHandle(e){return!!(e&&e.closest&&e.closest(".shipwindow-resize-grip"))}isDragSlop(e,n){if(!e||!e.closest||!e.closest(".shipwindow-grab-slop"))return!1;const r=this.elementRef.current,i=r&&r.querySelector(".shipwindow-drag-handle");if(!i)return!1;const o=i.getBoundingClientRect();return n>=o.top&&n<=o.bottom+F$}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(e,n,r){const i=Date.now(),o=!!this.lastPress&&this.lastPress.kind===e&&i-this.lastPress.time<_$;this.lastPress={kind:e,time:i},this.pressPoint={x:n,y:r},this.pendingReset=o}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(e,n){const r=this.elementRef.current;if(!r)return!1;const i=window.getComputedStyle(r);let o=parseFloat(i.left),s=parseFloat(i.top);return isFinite(o)||(o=r.offsetLeft),isFinite(s)||(s=r.offsetTop),this.dragStart={x:e,y:n,left:o,top:s},this.positioned=!0,r.style.left=o+"px",r.style.top=s+"px",r.style.right="auto",!0}moveDrag(e,n){const r=this.elementRef.current;!this.dragStart||!r||(r.style.left=this.dragStart.left+(e-this.dragStart.x)+"px",r.style.top=this.dragStart.top+(n-this.dragStart.y)+"px",this.clampIntoView())}beginResize(e,n){const r=this.elementRef.current;if(!r)return!1;const i=this.measureNatural();if(!i)return!1;const o=window.getComputedStyle(r);let s=parseFloat(o.left),l=parseFloat(o.top);isFinite(s)||(s=r.offsetLeft),isFinite(l)||(l=r.offsetTop);const c=this.screenFit||1,d=this.isMirroredGrip(),h=d?"top right":"top left";!d&&this.transformOrigin()==="top right"&&(s+=i.width*(1-c)),r.style.left=s+"px",r.style.top=l+"px",r.style.right="auto",r.style.transformOrigin=h,this.resizeOrigin=h,this.positioned=!0;const p=r.getBoundingClientRect(),f=d?p.right:p.left,v=d?-1:1;return this.resizeStart={originX:f,originY:p.top,flipX:v,width:i.width,height:i.height,scale:c,maxScale:d?f/i.width:1/0,base:Lm(i.width,i.height,v*(e-f),n-p.top)},!0}moveResize(e,n){const r=this.resizeStart;if(!r)return;const i=Lm(r.width,r.height,r.flipX*(e-r.originX),n-r.originY),o=Pd(Math.min(r.maxScale,r.scale+(i-r.base))),s=this.side(),l=Math.round(o/(this.autoFit||1)*100)/100;l!==Ys(s)&&(zm(s,l),this.applyScreenFit())}moveGesture(e,n){this.pressPoint&&(Math.abs(e-this.pressPoint.x)>Om||Math.abs(n-this.pressPoint.y)>Om)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(e,n):this.moveDrag(e,n)}finishGesture(){const e=this.elementRef.current,n=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),n){const r=this.side();Bm(r,Ys(r)),this.keepGripOnScreen(),this.clampIntoView()}e&&(Rm[this.side()]={top:parseFloat(e.style.top)||0,left:parseFloat(e.style.left)||0})}clampIntoView(e){const n=this.elementRef.current;if(!n||!this.positioned)return;const r=n.getBoundingClientRect(),i=document.documentElement.clientWidth||window.innerWidth,o=window.innerHeight||document.documentElement.clientHeight;let s=0,l=0;r.top<0?l=-r.top:r.top>o-Dr&&(l=o-Dr-r.top),e&&r.width<=i&&r.left<0?s=-r.left:r.right<Dr?s=Dr-r.right:r.left>i-Dr&&(s=i-Dr-r.left),!(!s&&!l)&&(n.style.left=(parseFloat(n.style.left)||0)+s+"px",n.style.top=(parseFloat(n.style.top)||0)+l+"px")}keepGripOnScreen(){const e=this.elementRef.current;if(!e)return;const n=e.getBoundingClientRect(),r=document.documentElement.clientWidth||window.innerWidth;let i;if(this.isMirroredGrip()){if(n.left>=0||(i=Math.min(-n.left,r-n.right),i<=0))return}else if(i=r-n.right,i>=0)return;const o=window.getComputedStyle(e);let s=parseFloat(o.left);isFinite(s)||(s=e.offsetLeft),e.style.left=s+i+"px",e.style.right="auto",this.positioned=!0}resetUserScale(){const e=this.side();Ys(e)!==1&&(zm(e,1),Bm(e,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(e){e.stopPropagation(),this.resetUserScale()}onDragStart(e){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] pointerdown",e.pointerType,"handle:",this.isDragHandle(e.target),"grip:",this.isResizeHandle(e.target)),e.pointerType==="touch"||e.button!=null&&e.button>0)return;const n=this.isResizeHandle(e.target);if(!n&&!this.isDragHandle(e.target))return;if(this.notePress(n?"grip":"header",e.clientX,e.clientY),!(n?this.beginResize(e.clientX,e.clientY):this.beginDrag(e.clientX,e.clientY))){this.cancelDoublePress();return}const r=this.elementRef.current;let i=!1;try{r.setPointerCapture(e.pointerId),i=!0}catch{}this.dragTarget=i?r:document,this.dragTarget.addEventListener("pointermove",this.onDragMove),this.dragTarget.addEventListener("pointerup",this.onDragEnd),this.dragTarget.addEventListener("pointercancel",this.onDragEnd),this.dragPointerId=e.pointerId,e.preventDefault()}onDragMove(e){!this.gestureActive()||e.pointerId!==this.dragPointerId||this.moveGesture(e.clientX,e.clientY)}onDragEnd(e){!this.gestureActive()||e&&e.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(e){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] touchstart",e.touches&&e.touches.length,"handle:",this.isDragHandle(e.target),"grip:",this.isResizeHandle(e.target)),this.gestureActive()||!e.touches||e.touches.length!==1)return;const n=e.touches[0],r=this.isResizeHandle(e.target);if(!(!r&&!this.isDragHandle(e.target)&&!this.isDragSlop(e.target,n.clientY))){if(this.notePress(r?"grip":"header",n.clientX,n.clientY),!(r?this.beginResize(n.clientX,n.clientY):this.beginDrag(n.clientX,n.clientY))){this.cancelDoublePress();return}this.touchDragId=n.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.addEventListener("touchend",this.onTouchDragEnd),document.addEventListener("touchcancel",this.onTouchDragEnd),e.cancelable&&e.preventDefault()}}onTouchDragMove(e){const n=Nm(e.touches,this.touchDragId);if(!this.gestureActive()||!n)return;this.moveGesture(n.clientX,n.clientY);const r=this.dragScroll;r&&((window.scrollX||window.pageXOffset||0)!==r.x||(window.scrollY||window.pageYOffset||0)!==r.y)&&window.scrollTo(r.x,r.y),e.cancelable&&e.preventDefault()}onTouchDragEnd(e){e&&e.touches&&Nm(e.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.removeEventListener("touchend",this.onTouchDragEnd),document.removeEventListener("touchcancel",this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){const e=this.dragTarget;e&&(e.removeEventListener("pointermove",this.onDragMove),e.removeEventListener("pointerup",this.onDragEnd),e.removeEventListener("pointercancel",this.onDragEnd),this.dragPointerId!=null&&e.hasPointerCapture&&e.hasPointerCapture(this.dragPointerId)&&e.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(e){e.stopPropagation();let{ship:n}=this.props;if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Ln()){window.uiEvents.relay("CloseSystemInfo");return}window.uiEvents.relay("SystemClicked",{ship:n,system:n,element:e.target})}onShipTouchMove(e){if(!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(e){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onShipTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:n}=this.props,r=shipManager.systems.getSystemByName(n,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:n,system:r||n.systems[0],element:e.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onUnknownTouchStart(e){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:i}=this.props,o=shipManager.systems.getSystemByName(i,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:i,system:o||i.systems[0],element:n,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){const e=this.elementRef.current,n=Ks(this.props.ship)?"left":"right";e.addEventListener("pointerdown",this.onDragStart),e.addEventListener("touchstart",this.onTouchDragStart,{passive:!1});const r=Rm[n];if(r&&!Im()){const i=Math.max(0,Math.min(r.top,window.innerHeight-60)),o=Math.max(60-e.offsetWidth,Math.min(r.left,window.innerWidth-60));e.style.top=i+"px",e.style.left=o+"px",e.style.right="auto",this.positioned=!0}document.addEventListener("pointerdown",this.onDocumentPointerDown),this.alignStaggeredPrimary(),this.applyScreenFit(),window.addEventListener("resize",this.onScreenResize),window.addEventListener("orientationchange",this.onScreenResize)}componentDidUpdate(){this.alignStaggeredPrimary(),this.applyScreenFit()}alignStaggeredPrimary(){const e=this.sectionGridRef.current,n=this.staggerPrimaryRef.current;if(!e||!n)return;const r=n.offsetHeight/2;e.style.setProperty("--fv-prim-half",r+"px"),e.style.setProperty("--fv-art-dy",n.offsetTop+r-e.clientHeight/2+"px")}componentWillUnmount(){document.removeEventListener("pointerdown",this.onDocumentPointerDown),window.removeEventListener("resize",this.onScreenResize),window.removeEventListener("orientationchange",this.onScreenResize);const e=this.elementRef.current;e&&(e.removeEventListener("pointerdown",this.onDragStart),e.removeEventListener("touchstart",this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(e){this.panelHoverTimer&&(clearTimeout(this.panelHoverTimer),this.panelHoverTimer=null),this.state.hoverPanel!==e&&this.setState({hoverPanel:e})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(e){if(!this.state.openPanel)return;const n=this.controlsRef.current,r=this.popupRef.current;n&&n.contains(e.target)||r&&r.contains(e.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay("CloseShipWindow",{ship:this.props.ship})}togglePanel(e,n){n.stopPropagation(),this.setState(r=>({openPanel:r.openPanel===e?null:e}))}toggleArt(e){e&&e.stopPropagation(),this.setState(n=>({showArt:!n.showArt,openPanel:null}))}renderResizeGrip(e){return a.jsx(u$,{className:"shipwindow-resize-grip shipwindow-nodrag",$pad:N$,$mirror:this.isMirroredGrip(),$overlap:!!e,onDoubleClick:this.onGripDoubleClick,title:"Drag to resize this window — double-click to reset its size"})}renderStatusStrip(e,n){const r=n?[{key:"rolled",text:"⟲ Rolled — port / starboard reversed"}].concat(Wm(e)):Wm(e);return a.jsxs(I.Fragment,{children:[r.map((i,o)=>a.jsx(w$,{$color:i.color,$bg:i.bg,$grip:o===r.length-1,children:i.text},i.key)),this.renderResizeGrip(r.length>0)]})}renderHeader(e,n,r){return a.jsxs(a$,{className:"shipwindow-drag-handle",title:"Drag to move — double-click to reset window size",children:[a.jsx(l$,{$tint:r,title:e,children:e}),a.jsx(c$,{title:n,children:n}),a.jsx(d$,{className:"shipwindow-nodrag",onClick:this.close.bind(this),children:"✕"})]})}renderHitChartButton(e){const{openPanel:n}=this.state;return a.jsxs(Gs,{ref:this.hitChartBtnRef,$wide:e,$active:n==="hitchart",onClick:this.togglePanel.bind(this,"hitchart"),children:[a.jsx(jm,{children:"⊕"}),"Hit Chart"]})}renderStatsButton(e){const{openPanel:n}=this.state;return a.jsxs(Gs,{$wide:e,$active:n==="shipstats",onClick:this.togglePanel.bind(this,"shipstats"),onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[a.jsxs(Cm,{children:[a.jsx("i",{}),a.jsx("i",{}),a.jsx("i",{})]}),"Ship Stats"]})}renderArtButton(e){return a.jsxs(Gs,{$wide:e,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[a.jsx(p$,{}),"Ship Art"]})}renderNotesButton(e){const{openPanel:n}=this.state;return a.jsxs(Gs,{$wide:e,$active:n==="notes",onClick:this.togglePanel.bind(this,"notes"),onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[a.jsx(jm,{children:"✎"}),"Notes"]})}renderControls(e,n,r,i){const o=Ln(),s=_m(this.props.ship),l=Hm(this.props.ship);if(!e&&!n&&!i&&!s&&!l)return null;const c=o;return a.jsxs(h$,{ref:this.controlsRef,$compact:r,children:[e&&this.renderHitChartButton(c),s&&this.renderArtButton(c),l&&this.renderStatsButton(c),n&&this.renderNotesButton(c),i&&a.jsx(Mm,{ship:this.props.ship})]})}getButtonLeft(){const e=this.controlsRef.current,n=this.elementRef.current;return!e||!n?null:Math.round((e.getBoundingClientRect().left-n.getBoundingClientRect().left)/(this.screenFit||1)-n.clientLeft)}getAnchorBelow(e,n){const r=e&&e.current,i=this.elementRef.current;if(!r||!i)return{top:n,left:this.getButtonLeft()};const o=r.getBoundingClientRect(),s=i.getBoundingClientRect(),l=this.screenFit||1;return{left:Math.round((o.left-s.left)/l-i.clientLeft),top:Math.round((o.bottom-s.top)/l-i.clientTop)+4}}renderPopup(e,n,r){const{ship:i}=this.props,{openPanel:o,hoverPanel:s}=this.state,l=o||s;if(!l)return null;const c=l==="hitchart"&&Ln()?this.hitChartBtnRef:this.controlsRef,{top:d,left:h}=this.getAnchorBelow(c,r);return l==="hitchart"&&e?a.jsx(Td,{ref:this.popupRef,$top:d,$left:h,$fit:!0,children:a.jsx(Kk,{ship:i})}):l==="shipstats"&&Hm(i)?a.jsx(Td,{ref:this.popupRef,$top:d,$left:h,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:a.jsx(Mm,{ship:i,live:!0,bare:!0})}):l==="notes"&&n?a.jsx(Td,{ref:this.popupRef,$top:d,$left:h,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:a.jsx(dm,{ship:i,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){const{ship:e}=this.props,n=Ln(),r=Ks(e);var i=e.shipClass,o=e.name;let s=!1;if(e.mine){var l=shipManager.systems.getSystemByName(e,"mineStealth");l&&!l.isMineRevealed(e)&&(i="Mine",o="Mine",s=!0)}o||(o=i,i="");const c=!s&&G$(e),d=!n&&!s&&V$(e);if(e.flight){if(n)return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:D=>{D.preventDefault(),D.stopPropagation()},$isMyTeam:r,$variant:"flightLobby",children:[this.renderHeader(o,i,Ad()),a.jsxs(x$,{children:[a.jsx(y$,{children:a.jsx(yd,{ship:e})}),a.jsx(Cd,{ship:e})]}),this.renderResizeGrip()]});const $=!!(window.ew&&ew.isFlightEwPool(e));return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:D=>{D.preventDefault(),D.stopPropagation()},$isMyTeam:r,$variant:$?"flightEw":"flight",children:[this.renderHeader(o,i,Ad()),$?a.jsxs(o$,{children:[a.jsx(s$,{children:a.jsx(yd,{ship:e})}),a.jsx(vm,{ship:e,flight:!0})]}):a.jsx(yd,{ship:e}),this.renderStatusStrip(e)]})}if(s)return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:$=>{$.preventDefault(),$.stopPropagation()},$isMyTeam:r,$variant:"terrain",children:[this.renderHeader(o,i,null),a.jsxs(Dm,{children:[a.jsx(jd,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(Md,{$img:window.AssetManager.getSmartImagePath(e.imagePath)}),a.jsx(v$,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:"?"})]}),this.renderResizeGrip()]});const h=J$(e),p=X$(h);if((window.gamedata.isTerrain(e.shipSizeClass,e.userid)||e.mine)&&!Fm(h)){const $=c||d||_m(e);return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:D=>{D.preventDefault(),D.stopPropagation()},$isMyTeam:r,$variant:"terrain",children:[this.renderHeader(o,i,null),a.jsxs(Dm,{children:[a.jsx(jd,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(Md,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:e.mine&&$?U$:0}),this.renderControls(c,d,!0),C$.map(D=>h[D].length>0&&a.jsx(gm,{location:D,nameOverride:p[D],ship:e,systems:h[D],isTerrain:!0,hidden:this.state.showArt},`section-${e.id}-${D}`))]}),n&&a.jsx(Cd,{ship:e,full:!0}),this.renderStatusStrip(e),this.renderPopup(c,d,72)]})}const v=shipManager.movement.isRolled(e),x=!!e.enhancementTooltip||kd(e)!==null,b=e.base&&!e.smallBase,M=tC(e,h),w=M?T$:q$(h,x),m=$$.map($=>{if(h[$].length===0)return null;const D=v&&Pm[$]!==void 0?Pm[$]:$,E=b$[D],H=$===0||$===1||$===2||b&&Am.includes($);return{area:E,element:a.jsx(gm,{location:$,displayLocation:D,area:E,valign:M?void 0:S$[E],justify:M?void 0:k$[E],wide:H,minHeight:void 0,nameOverride:p[$],hidden:this.state.showArt,ship:e,systems:h[$]},`section-${e.id}-${$}`)}}).filter(Boolean),y=this.renderControls(c,d,!1,n&&!e.mine),S=n?a.jsx(Cd,{ship:e,grid:!0,hideEnhancements:x}):a.jsx(vm,{ship:e}),T=x&&a.jsx(r$,{ship:e}),C=$=>{const D=$.stack.map(K=>m.find(he=>he.area===K)).filter(Boolean).map(K=>K.area==="prim"?a.jsx(m$,{ref:this.staggerPrimaryRef,children:K.element},"prim"):K.element),E=$.area==="lup"?y:$.area==="rup"?S:null,H=$.area==="rlo"?T:null;return a.jsxs(g$,{$area:$.area,$side:$.side,$upper:$.upper,children:[E&&a.jsx(Em,{$top:!0,children:E}),D,H&&a.jsx(Em,{children:H})]},$.area)},k=a.jsxs(f$,{ref:this.sectionGridRef,$areas:w,$staggered:M,children:[a.jsx(jd,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(Md,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:n&&Fm(h)?W$:0,$offsetVar:M?"--fv-art-dy":void 0}),M?M$.map(C):a.jsxs(a.Fragment,{children:[y,S,T,m.map($=>$.element)]})]});return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:$=>{$.preventDefault(),$.stopPropagation()},$isMyTeam:r,$variant:"ship",children:[this.renderHeader(o,i,Ad()),k,this.renderStatusStrip(e,v),this.renderPopup(c,d)]})}}const Ni=()=>window.uiEvents.relay("CloseSystemInfo"),Rm={left:null,right:null},D$="(max-width: 1024px)",P$="(orientation: portrait)",A$={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},R$={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},Ed=.98,Im=()=>!!window.matchMedia&&window.matchMedia(D$).matches,I$=()=>window.matchMedia?window.matchMedia(P$).matches:window.innerHeight>=window.innerWidth,O$=(t,e)=>e===1?t:{fillW:Math.min(Ed,t.fillW*e),fillH:Math.min(Ed,t.fillH*e),min:Math.min(t.max,t.min*e),max:t.max},L$=()=>{const t=Ln()?R$:A$;return I$()?O$(t,t.portrait):t},z$=.35,B$=3,N$=6,F$=8,_$=400,Om=6,Dr=40,Dd="fv.shipwindow.userScale",Pd=t=>Math.min(B$,Math.max(z$,t)),Lm=(t,e,n,r)=>(n*t+r*e)/(t*t+e*e),Vs={left:null,right:null},Ys=t=>(Vs[t]==null&&(Vs[t]=H$(t)),Vs[t]),zm=(t,e)=>{Vs[t]=e},H$=t=>{try{let e=parseFloat(window.localStorage.getItem(Dd+"."+t));if(isFinite(e)||(e=parseFloat(window.localStorage.getItem(Dd))),isFinite(e)&&e>0)return Pd(e)}catch{}return 1},Bm=(t,e)=>{try{window.localStorage.setItem(Dd+"."+t,String(e))}catch{}},Nm=(t,e)=>{if(!t||e==null)return null;for(let n=0;n<t.length;n++)if(t[n].identifier===e)return t[n];return null},Ln=()=>!!window.gamedata&&window.gamedata.gamephase===-2,W$=35,Fm=t=>j$.some(e=>t[e].length>0),U$=20,Ks=t=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide=="function"?window.ShipWindowManager.isLeftSide(t):t.team===window.gamedata.getPlayerTeam(),G$=t=>!!t.hitChart&&Object.keys(t.hitChart).length>0,_m=t=>!!(t&&t.imagePath)&&!t.flight,Hm=t=>!!t&&!Ln()&&!t.flight&&!t.mine&&!window.gamedata.isTerrain(t.shipSizeClass,t.userid),V$=t=>!!t.notes||!!t.enhancementTooltip||!!(t.hasAttached&&Object.keys(t.hasAttached).length>0),Ad=t=>null,Wm=t=>{if(Ln())return[];const e=[],n=shipManager.getTurnDeployed(t);n>window.gamedata.turn&&n<999&&e.push({key:"deploying",color:g.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Deploying on Turn "+n});const r=shipManager.getArrivalIniPenalty(t);r!==0&&e.push({key:"arrivalScatter",color:g.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Arrival Scatter "+r+" Ini"});const i=t.trueStealth?shipManager.getStealthToggleForecast(t):null;if(t.trueStealth)if(Q$(t,i))e.push({key:"undetected",color:g.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Undetected"});else if(t.mine){const d=shipManager.systems.getSystemByName(t,"mineStealth");!d||d.isMineRevealedToOpponent(t)?e.push({key:"detected",color:g.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected - Revealed"}):e.push({key:"detected",color:g.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Detected - Not Revealed"})}else i===!0?e.push({key:"detected",color:g.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Would be Detected"}):e.push({key:"detected",color:g.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected"});shipManager.isJumpingToHyperspace(t)&&e.push({key:"jumping",color:g.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:"Jumping to Hyperspace"});const o=t.flight&&window.fighterMissions?window.fighterMissions.bannerText(t):null;o&&e.push(o.tone==="warning"?{key:"fighterMission",color:g.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:o.text}:{key:"fighterMission",color:g.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:o.text});const s=t.flight?null:shipManager.movement.getExtendedTurnStatus(t);s&&e.push(s.cancelled?{key:"extendedTurn",color:g.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:s.text}:{key:"extendedTurn",color:g.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:s.text});const l=window.JumpEngine&&typeof window.JumpEngine.getAbductionChain=="function"?window.JumpEngine.getAbductionChain(t.id):null;l&&e.push({key:"abducted",color:"#b36bff",bg:"rgba(127, 0, 255, 0.10)",text:"Being abducted: "+window.JumpEngine.formatAbductionHalves(l.total)+"/"+l.cost+" power-turns"});const c=shipManager.getHangarManoeuvre(t);if(c&&e.push({key:"hangarManoeuvre",color:g.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:c.text}),t.attached&&Object.keys(t.attached).length>0&&!t.detached&&!(c&&c.riding)){const d=window.gamedata.getShip(Object.keys(t.attached)[0]);if(d){const h=Object.values(t.attached)[0];let p="";h==1?p="Front":h==2?p="Aft":h==3||h==31||h==32?p="Port":(h==4||h==41||h==42)&&(p="Starboard"),e.push({key:"attached",color:g.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Attached to "+d.name+(p?" ["+p+"]":"")})}}return t.hasAttached&&Object.keys(t.hasAttached).length>0&&Object.keys(t.hasAttached).filter(h=>{const p=window.gamedata.getShip(h);return!(p&&shipManager.isDockingRider(p))}).length>0&&e.push({key:"boarded",color:g.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Ship is being boarded!"}),t.flight&&K$(t)&&e.push({key:"edfGrounded",color:Y$,bg:"rgba(210, 80, 255, 0.12)",text:"Energy Drained - cannot fire"}),e},Y$="#d250ff",K$=t=>{const e=shipManager.systems.getSystem(t,1);return!e||!e.criticals?!1:e.criticals.some(n=>n.phpclass==="EdfFighterGrounded"&&n.turn+1===window.gamedata.turn)},Q$=(t,e)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(t)==gamedata.turn||shipManager.getTurnDeployed(t)==gamedata.turn))return!0;if(e!=null)return!e;let n=shipManager.isDetected(t);if(!n&&t.team==gamedata.getPlayerTeam()){let r=null;t.mine?r=shipManager.systems.getSystemByName(t,"mineStealth"):t.faction=="Torvalus Speculators"?r=shipManager.systems.getSystemByName(t,"ShadingField"):shipManager.getSpecialAbilityStealth(t,"Cloaking")?r=shipManager.systems.getSystemByName(t,"CloakingDevice"):shipManager.getSpecialAbilityStealth(t,"Stealth")&&(r=shipManager.systems.getSystemByName(t,"stealth")),r&&(Array.isArray(r.detected)&&r.detected.length>0||r.detected===!0||Array.isArray(r.detectedNew)&&r.detectedNew.length>0||r.detectedNew===!0)&&(n=!0)}return!n},X$=t=>{const e={};return[{locations:[3,31,32],name:"Port"},{locations:[4,41,42],name:"Starboard"}].forEach(n=>{const r=n.locations.filter(i=>t[i].some(o=>o.name==="structure"));r.length===1&&r[0]!==n.locations[0]&&(e[r[0]]=n.name)}),e},J$=t=>{const e={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return t.systems.forEach(n=>{n.hideInShipWindow||e[n.location].push(n)}),e},q$=(t,e)=>{const n=[["ctrl","fwd","ew"]];let r=0;if((t[31].length||t[41].length)&&(n.push(["lfwd","prim","rfwd"]),r++),(t[3].length||t[4].length)&&(n.push(["left","prim","right"]),r++),(t[32].length||t[42].length)&&(n.push(["laft","prim","raft"]),r++),r===0&&t[0].length&&n.push([null,"prim",null]),t[2].length&&n.push([null,"aft",null]),e){const i=n[n.length-1];n.length>1&&i[2]===null?i[2]="enh":n.push([null,null,"enh"])}for(let i=1;i<n.length&&n[i][0]===null;i++)n[i][0]="ctrl";for(let i=1;i<n.length&&n[i][2]===null;i++)n[i][2]="ew";return n.map(i=>`"${i[0]||"."}  ${i[1]||"."}  ${i[2]||"."}"`).join(" ")},Z$=4,eC=t=>Math.ceil(t.filter(e=>e.name!=="structure").length/4),tC=(t,e)=>!!(t.base&&!t.smallBase)&&Am.some(n=>eC(e[n])>Z$),nC=u.div`

`,rC=u.div`
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
    border: 1px solid ${g.colors.warning};
    background-color: ${g.colors.windowBg};
    color: ${g.colors.text};
    font-family: ${g.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`,iC=u.span`
    cursor: pointer;
    color: ${g.colors.line};
    font-size: 16px;

    &:hover {
        color: ${g.colors.text};
    }
`;class oC extends I.Component{constructor(e){super(e),this.state={error:null}}static getDerivedStateFromError(e){return{error:e}}componentDidCatch(e,n){const r=this.props.ship;console.error("Ship window render failed for",r&&(r.name||r.shipClass),e,n)}render(){if(this.state.error){const e=this.props.ship;return a.jsxs(rC,{children:[a.jsxs("span",{children:[e&&(e.name||e.shipClass)||"Ship"," — window failed to render (see console)"]}),a.jsx(iC,{onClick:()=>window.uiEvents.relay("CloseShipWindow",{ship:e}),children:"✕"})]})}return this.props.children}}class sC extends I.Component{render(){const{ships:e}=this.props;return a.jsx(nC,{children:e.map(n=>a.jsx(oC,{ship:n,children:a.jsx(E$,{ship:n})},`shipwindow-${n.userid}-${n.id}`))})}}const aC=u.div`
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
    background: ${P.bg};
    border: 1px solid ${P.line};
    border-radius: ${P.radius};
    box-shadow: ${P.shadow};
`;u.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${P.dim};
    user-select: none;
`;const lC=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${P.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,cC=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,dC=u.span`
    flex: 0 0 auto;
    color: ${P.dim};
    font-size: 10px;
    user-select: none;
`,Um=u.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${P.btnBg};
    border: 1px solid ${P.line};
    border-radius: 2px;
    color: ${P.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${P.line};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${P.btnBg}; color: ${P.btnText}; }
    `}
`,uC=u.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${g.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${P.well};
    border: 1px solid ${P.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${P.focus}; }
`,hC=u.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${P.btnBg};
    border: 1px solid ${P.line};
    border-radius: 2px;
    color: ${P.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${g.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${P.line}; color: #ffffff; }
`,pC=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e};class fC extends I.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Ii(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(e,n){const{ship:r}=this.props,i=this.maxHealth();let o=parseInt(n,10);isNaN(o)&&(o=i),o=Math.max(1,Math.min(i,o)),battleDamage.setFighter(r,e,{d:i-o}),this.refresh()}step(e,n){this.setRemaining(e,battleDamage.fighterHealth(this.props.ship,e)+n)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,"");this.setRemaining(e,r===""?0:parseInt(r,10))}propagate(){const{ship:e}=this.props,n=parseInt(e.flightSize,10)||0,r=battleDamage.fighterMaxHealth(e)-battleDamage.fighterHealth(e,1);for(let i=2;i<=n;i++)battleDamage.setFighter(e,i,{d:r});this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){const{ship:e,boundingBox:n}=this.props,r=parseInt(e&&e.flightSize,10)||0,i=this.maxHealth();if(!r||!i)return null;battleDamage.flightSummary(e);const o=[];for(let c=1;c<=r;c++){const d=battleDamage.fighterHealth(e,c);o.push(a.jsxs(lC,{children:[a.jsxs(cC,{children:["Fighter ",c]}),a.jsx(Um,{title:"More damage",disabled:d<=1,onClick:()=>this.step(c,-1),children:"−"}),a.jsx(uC,{ref:this.wheelRef(c),type:"text",value:d,onChange:h=>this.onInput(c,h)}),a.jsx(Um,{title:"Repair",disabled:d>=i,onClick:()=>this.step(c,1),children:"+"}),a.jsxs(dC,{children:["/ ",i]})]},`ftr-${c}`))}const s=battleDamage.flightCritEntry(e)||{},l=Pg(s.c,e.preBattleCritDesc,e.preBattleCritTransient,s.p);return a.jsxs(aC,{$position:pC(n),onClick:c=>c.stopPropagation(),children:[a.jsx(Eg,{$sticky:!0,children:"Fighter Damage"}),o,r>1&&a.jsx(hC,{title:"Copy Fighter 1's damage to every fighter in this flight",onClick:()=>this.propagate(),children:"Apply Fighter 1's damage to all"}),a.jsx(Ag,{ship:e,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:l,editable:!0,onChange:()=>this.refresh()})]})}}const gC=u.div`
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
    background: ${P.bg};
    border: 1px solid ${P.line};
    border-radius: ${P.radius};
    box-shadow: ${P.shadow};
`,mC=u.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${P.dim};
    user-select: none;
`,xC=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${P.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,yC=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,wC=u.span`
    flex: 0 0 auto;
    color: ${P.dim};
    font-size: 10px;
    user-select: none;
`,Gm=u.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${P.btnBg};
    border: 1px solid ${P.line};
    border-radius: 2px;
    color: ${P.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${P.line};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${P.btnBg}; color: ${P.btnText}; }
    `}
`,vC=u.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${g.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${P.well};
    border: 1px solid ${P.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${P.focus}; }
`,bC=u.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${P.btnBg};
    border: 1px solid ${P.line};
    border-radius: 2px;
    color: ${P.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${g.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${P.line}; color: #ffffff; }
`,SC=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e};class kC extends I.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Ii(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(e,n){const{ship:r}=this.props,i=this.maxHealth();let o=parseInt(n,10);isNaN(o)&&(o=i),o=Math.max(1,Math.min(i,o)),battleDamage.setMine(r,e,{d:i-o}),this.refresh()}step(e,n){this.setRemaining(e,battleDamage.mineHealth(this.props.ship,e)+n)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,"");this.setRemaining(e,r===""?0:parseInt(r,10))}propagate(){const{ship:e}=this.props,n=battleDamage.mineCount(e),r=battleDamage.getEntry(e,battleDamage.KIND_MINE,1);for(let i=2;i<=n;i++)battleDamage.setWholeEntry(e,battleDamage.KIND_MINE,i,r);this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){const{ship:e,boundingBox:n}=this.props,r=battleDamage.mineCount(e),i=this.maxHealth();if(!r||i<2)return null;const o=battleDamage.mineSummary(e),s=[];for(let l=1;l<=r;l++){const c=battleDamage.mineHealth(e,l);s.push(a.jsxs(xC,{children:[a.jsxs(yC,{children:["Mine ",l]}),a.jsx(Gm,{title:"More damage",disabled:c<=1,onClick:()=>this.step(l,-1),children:"−"}),a.jsx(vC,{ref:this.wheelRef(l),type:"text",value:c,onChange:d=>this.onInput(l,d)}),a.jsx(Gm,{title:"Repair",disabled:c>=i,onClick:()=>this.step(l,1),children:"+"}),a.jsxs(wC,{children:["/ ",i]})]},`mne-${l}`))}return a.jsxs(gC,{$position:SC(n),onClick:l=>l.stopPropagation(),children:[a.jsx(Eg,{$sticky:!0,children:"Mine Damage"}),a.jsxs(mC,{children:[o.remaining," / ",o.total," structure"]}),s,r>1&&a.jsx(bC,{title:"Copy Mine 1's damage to every mine in this purchase",onClick:()=>this.propagate(),children:"Apply Mine 1 to all"})]})}}class $C{constructor(e){this.parentElement=e,this.roots=new Map}getRoot(e){const n=jQuery(e,this.parentElement)[0];return n?(this.roots.has(n)||this.roots.set(n,tf(n)),this.roots.get(n)):null}unmountRoot(e){const n=jQuery(e,this.parentElement)[0];n&&this.roots.has(n)&&(this.roots.get(n).unmount(),this.roots.delete(n))}EwButtons(e){const n=this.getRoot("#showEwButtons");n&&n.render(a.jsx(bv,{...e}))}FullScreen(e){const n=this.getRoot("#fullScreen");n&&n.render(a.jsx(pv,{...e}))}Surrender(e){const n=this.getRoot("#surrender");n&&n.render(a.jsx(gv,{...e}))}BackToLobby(e){const n=this.getRoot("#backToLobby");n&&n.render(a.jsx(wv,{...e}))}PlayerSettings(e){const n=this.getRoot("#playerSettings");n&&n.render(a.jsx(Bw,{...e}))}showShipThrustUI(e){const n=this.getRoot("#shipThrust");n&&n.render(a.jsx(cv,{...e}))}hideShipThrustUI(){this.unmountRoot("#shipThrust")}showWeaponList(e){const n=this.getRoot("#weaponList");n&&n.render(a.jsx(HS,{...e}))}hideWeaponList(){this.unmountRoot("#weaponList")}showSystemInfo(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(KS,{...e}))}hideSystemInfo(){this.unmountRoot("#systemInfoReact")}showSystemInfoMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(tk,{...e}))}hideSystemInfoMenu(){this.unmountRoot("#systemInfoReact")}canShowSystemInfoMenu(e,n){return Rs(e,n)}showFighterDamageMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(fC,{...e}))}showMineDamageMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(kC,{...e}))}renderShipWindows(e){const n=this.getRoot("#shipWindowsReact");n&&n.render(a.jsx(sC,{...e}))}}window.UIManager=$C});
