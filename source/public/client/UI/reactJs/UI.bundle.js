(function(Hi){typeof define=="function"&&define.amd?define(Hi):Hi()})(function(){"use strict";function Hi(t){return t&&t.__esModule&&Object.prototype.hasOwnProperty.call(t,"default")?t.default:t}var Ld={exports:{}},Wi={},zd={exports:{}},B={};/**
 * @license React
 * react.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var Rr=Symbol.for("react.element"),Xm=Symbol.for("react.portal"),Jm=Symbol.for("react.fragment"),qm=Symbol.for("react.strict_mode"),Zm=Symbol.for("react.profiler"),ex=Symbol.for("react.provider"),tx=Symbol.for("react.context"),nx=Symbol.for("react.forward_ref"),rx=Symbol.for("react.suspense"),ix=Symbol.for("react.memo"),ox=Symbol.for("react.lazy"),Bd=Symbol.iterator;function sx(t){return t===null||typeof t!="object"?null:(t=Bd&&t[Bd]||t["@@iterator"],typeof t=="function"?t:null)}var Nd={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},Fd=Object.assign,_d={};function Fn(t,e,n){this.props=t,this.context=e,this.refs=_d,this.updater=n||Nd}Fn.prototype.isReactComponent={},Fn.prototype.setState=function(t,e){if(typeof t!="object"&&typeof t!="function"&&t!=null)throw Error("setState(...): takes an object of state variables to update or a function which returns an object of state variables.");this.updater.enqueueSetState(this,t,e,"setState")},Fn.prototype.forceUpdate=function(t){this.updater.enqueueForceUpdate(this,t,"forceUpdate")};function Hd(){}Hd.prototype=Fn.prototype;function Zs(t,e,n){this.props=t,this.context=e,this.refs=_d,this.updater=n||Nd}var ea=Zs.prototype=new Hd;ea.constructor=Zs,Fd(ea,Fn.prototype),ea.isPureReactComponent=!0;var Wd=Array.isArray,Ud=Object.prototype.hasOwnProperty,ta={current:null},Gd={key:!0,ref:!0,__self:!0,__source:!0};function Vd(t,e,n){var r,i={},o=null,s=null;if(e!=null)for(r in e.ref!==void 0&&(s=e.ref),e.key!==void 0&&(o=""+e.key),e)Ud.call(e,r)&&!Gd.hasOwnProperty(r)&&(i[r]=e[r]);var l=arguments.length-2;if(l===1)i.children=n;else if(1<l){for(var c=Array(l),d=0;d<l;d++)c[d]=arguments[d+2];i.children=c}if(t&&t.defaultProps)for(r in l=t.defaultProps,l)i[r]===void 0&&(i[r]=l[r]);return{$$typeof:Rr,type:t,key:o,ref:s,props:i,_owner:ta.current}}function ax(t,e){return{$$typeof:Rr,type:t.type,key:e,ref:t.ref,props:t.props,_owner:t._owner}}function na(t){return typeof t=="object"&&t!==null&&t.$$typeof===Rr}function lx(t){var e={"=":"=0",":":"=2"};return"$"+t.replace(/[=:]/g,function(n){return e[n]})}var Yd=/\/+/g;function ra(t,e){return typeof t=="object"&&t!==null&&t.key!=null?lx(""+t.key):e.toString(36)}function Ui(t,e,n,r,i){var o=typeof t;(o==="undefined"||o==="boolean")&&(t=null);var s=!1;if(t===null)s=!0;else switch(o){case"string":case"number":s=!0;break;case"object":switch(t.$$typeof){case Rr:case Xm:s=!0}}if(s)return s=t,i=i(s),t=r===""?"."+ra(s,0):r,Wd(i)?(n="",t!=null&&(n=t.replace(Yd,"$&/")+"/"),Ui(i,e,n,"",function(d){return d})):i!=null&&(na(i)&&(i=ax(i,n+(!i.key||s&&s.key===i.key?"":(""+i.key).replace(Yd,"$&/")+"/")+t)),e.push(i)),1;if(s=0,r=r===""?".":r+":",Wd(t))for(var l=0;l<t.length;l++){o=t[l];var c=r+ra(o,l);s+=Ui(o,e,n,c,i)}else if(c=sx(t),typeof c=="function")for(t=c.call(t),l=0;!(o=t.next()).done;)o=o.value,c=r+ra(o,l++),s+=Ui(o,e,n,c,i);else if(o==="object")throw e=String(t),Error("Objects are not valid as a React child (found: "+(e==="[object Object]"?"object with keys {"+Object.keys(t).join(", ")+"}":e)+"). If you meant to render a collection of children, use an array instead.");return s}function Gi(t,e,n){if(t==null)return t;var r=[],i=0;return Ui(t,r,"","",function(o){return e.call(n,o,i++)}),r}function cx(t){if(t._status===-1){var e=t._result;e=e(),e.then(function(n){(t._status===0||t._status===-1)&&(t._status=1,t._result=n)},function(n){(t._status===0||t._status===-1)&&(t._status=2,t._result=n)}),t._status===-1&&(t._status=0,t._result=e)}if(t._status===1)return t._result.default;throw t._result}var ke={current:null},Vi={transition:null},dx={ReactCurrentDispatcher:ke,ReactCurrentBatchConfig:Vi,ReactCurrentOwner:ta};function Kd(){throw Error("act(...) is not supported in production builds of React.")}B.Children={map:Gi,forEach:function(t,e,n){Gi(t,function(){e.apply(this,arguments)},n)},count:function(t){var e=0;return Gi(t,function(){e++}),e},toArray:function(t){return Gi(t,function(e){return e})||[]},only:function(t){if(!na(t))throw Error("React.Children.only expected to receive a single React element child.");return t}},B.Component=Fn,B.Fragment=Jm,B.Profiler=Zm,B.PureComponent=Zs,B.StrictMode=qm,B.Suspense=rx,B.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=dx,B.act=Kd,B.cloneElement=function(t,e,n){if(t==null)throw Error("React.cloneElement(...): The argument must be a React element, but you passed "+t+".");var r=Fd({},t.props),i=t.key,o=t.ref,s=t._owner;if(e!=null){if(e.ref!==void 0&&(o=e.ref,s=ta.current),e.key!==void 0&&(i=""+e.key),t.type&&t.type.defaultProps)var l=t.type.defaultProps;for(c in e)Ud.call(e,c)&&!Gd.hasOwnProperty(c)&&(r[c]=e[c]===void 0&&l!==void 0?l[c]:e[c])}var c=arguments.length-2;if(c===1)r.children=n;else if(1<c){l=Array(c);for(var d=0;d<c;d++)l[d]=arguments[d+2];r.children=l}return{$$typeof:Rr,type:t.type,key:i,ref:o,props:r,_owner:s}},B.createContext=function(t){return t={$$typeof:tx,_currentValue:t,_currentValue2:t,_threadCount:0,Provider:null,Consumer:null,_defaultValue:null,_globalName:null},t.Provider={$$typeof:ex,_context:t},t.Consumer=t},B.createElement=Vd,B.createFactory=function(t){var e=Vd.bind(null,t);return e.type=t,e},B.createRef=function(){return{current:null}},B.forwardRef=function(t){return{$$typeof:nx,render:t}},B.isValidElement=na,B.lazy=function(t){return{$$typeof:ox,_payload:{_status:-1,_result:t},_init:cx}},B.memo=function(t,e){return{$$typeof:ix,type:t,compare:e===void 0?null:e}},B.startTransition=function(t){var e=Vi.transition;Vi.transition={};try{t()}finally{Vi.transition=e}},B.unstable_act=Kd,B.useCallback=function(t,e){return ke.current.useCallback(t,e)},B.useContext=function(t){return ke.current.useContext(t)},B.useDebugValue=function(){},B.useDeferredValue=function(t){return ke.current.useDeferredValue(t)},B.useEffect=function(t,e){return ke.current.useEffect(t,e)},B.useId=function(){return ke.current.useId()},B.useImperativeHandle=function(t,e,n){return ke.current.useImperativeHandle(t,e,n)},B.useInsertionEffect=function(t,e){return ke.current.useInsertionEffect(t,e)},B.useLayoutEffect=function(t,e){return ke.current.useLayoutEffect(t,e)},B.useMemo=function(t,e){return ke.current.useMemo(t,e)},B.useReducer=function(t,e,n){return ke.current.useReducer(t,e,n)},B.useRef=function(t){return ke.current.useRef(t)},B.useState=function(t){return ke.current.useState(t)},B.useSyncExternalStore=function(t,e,n){return ke.current.useSyncExternalStore(t,e,n)},B.useTransition=function(){return ke.current.useTransition()},B.version="18.3.1",zd.exports=B;var I=zd.exports;const ie=Hi(I);/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var ux=I,hx=Symbol.for("react.element"),px=Symbol.for("react.fragment"),fx=Object.prototype.hasOwnProperty,gx=ux.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,mx={key:!0,ref:!0,__self:!0,__source:!0};function Qd(t,e,n){var r,i={},o=null,s=null;n!==void 0&&(o=""+n),e.key!==void 0&&(o=""+e.key),e.ref!==void 0&&(s=e.ref);for(r in e)fx.call(e,r)&&!mx.hasOwnProperty(r)&&(i[r]=e[r]);if(t&&t.defaultProps)for(r in e=t.defaultProps,e)i[r]===void 0&&(i[r]=e[r]);return{$$typeof:hx,type:t,key:o,ref:s,props:i,_owner:gx.current}}Wi.Fragment=px,Wi.jsx=Qd,Wi.jsxs=Qd,Ld.exports=Wi;var a=Ld.exports,Xd={exports:{}},Le={},Jd={exports:{}},qd={};/**
 * @license React
 * scheduler.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */(function(t){function e(A,O){var L=A.length;A.push(O);e:for(;0<L;){var G=L-1>>>1,V=A[G];if(0<i(V,O))A[G]=O,A[L]=V,L=G;else break e}}function n(A){return A.length===0?null:A[0]}function r(A){if(A.length===0)return null;var O=A[0],L=A.pop();if(L!==O){A[0]=L;e:for(var G=0,V=A.length,Bn=V>>>1;G<Bn;){var ht=2*(G+1)-1,Dr=A[ht],pt=ht+1,Nn=A[pt];if(0>i(Dr,L))pt<V&&0>i(Nn,Dr)?(A[G]=Nn,A[pt]=L,G=pt):(A[G]=Dr,A[ht]=L,G=ht);else if(pt<V&&0>i(Nn,L))A[G]=Nn,A[pt]=L,G=pt;else break e}}return O}function i(A,O){var L=A.sortIndex-O.sortIndex;return L!==0?L:A.id-O.id}if(typeof performance=="object"&&typeof performance.now=="function"){var o=performance;t.unstable_now=function(){return o.now()}}else{var s=Date,l=s.now();t.unstable_now=function(){return s.now()-l}}var c=[],d=[],h=1,p=null,f=3,v=!1,x=!1,b=!1,M=typeof setTimeout=="function"?setTimeout:null,w=typeof clearTimeout=="function"?clearTimeout:null,g=typeof setImmediate<"u"?setImmediate:null;typeof navigator<"u"&&navigator.scheduling!==void 0&&navigator.scheduling.isInputPending!==void 0&&navigator.scheduling.isInputPending.bind(navigator.scheduling);function y(A){for(var O=n(d);O!==null;){if(O.callback===null)r(d);else if(O.startTime<=A)r(d),O.sortIndex=O.expirationTime,e(c,O);else break;O=n(d)}}function S(A){if(b=!1,y(A),!x)if(n(c)!==null)x=!0,Fi(T);else{var O=n(d);O!==null&&zn(S,O.startTime-A)}}function T(A,O){x=!1,b&&(b=!1,w($),$=-1),v=!0;var L=f;try{for(y(O),p=n(c);p!==null&&(!(p.expirationTime>O)||A&&!H());){var G=p.callback;if(typeof G=="function"){p.callback=null,f=p.priorityLevel;var V=G(p.expirationTime<=O);O=t.unstable_now(),typeof V=="function"?p.callback=V:p===n(c)&&r(c),y(O)}else r(c);p=n(c)}if(p!==null)var Bn=!0;else{var ht=n(d);ht!==null&&zn(S,ht.startTime-O),Bn=!1}return Bn}finally{p=null,f=L,v=!1}}var C=!1,k=null,$=-1,D=5,E=-1;function H(){return!(t.unstable_now()-E<D)}function K(){if(k!==null){var A=t.unstable_now();E=A;var O=!0;try{O=k(!0,A)}finally{O?he():(C=!1,k=null)}}else C=!1}var he;if(typeof g=="function")he=function(){g(K)};else if(typeof MessageChannel<"u"){var ut=new MessageChannel,Id=ut.port2;ut.port1.onmessage=K,he=function(){Id.postMessage(null)}}else he=function(){M(K,0)};function Fi(A){k=A,C||(C=!0,he())}function zn(A,O){$=M(function(){A(t.unstable_now())},O)}t.unstable_IdlePriority=5,t.unstable_ImmediatePriority=1,t.unstable_LowPriority=4,t.unstable_NormalPriority=3,t.unstable_Profiling=null,t.unstable_UserBlockingPriority=2,t.unstable_cancelCallback=function(A){A.callback=null},t.unstable_continueExecution=function(){x||v||(x=!0,Fi(T))},t.unstable_forceFrameRate=function(A){0>A||125<A?console.error("forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported"):D=0<A?Math.floor(1e3/A):5},t.unstable_getCurrentPriorityLevel=function(){return f},t.unstable_getFirstCallbackNode=function(){return n(c)},t.unstable_next=function(A){switch(f){case 1:case 2:case 3:var O=3;break;default:O=f}var L=f;f=O;try{return A()}finally{f=L}},t.unstable_pauseExecution=function(){},t.unstable_requestPaint=function(){},t.unstable_runWithPriority=function(A,O){switch(A){case 1:case 2:case 3:case 4:case 5:break;default:A=3}var L=f;f=A;try{return O()}finally{f=L}},t.unstable_scheduleCallback=function(A,O,L){var G=t.unstable_now();switch(typeof L=="object"&&L!==null?(L=L.delay,L=typeof L=="number"&&0<L?G+L:G):L=G,A){case 1:var V=-1;break;case 2:V=250;break;case 5:V=1073741823;break;case 4:V=1e4;break;default:V=5e3}return V=L+V,A={id:h++,callback:O,priorityLevel:A,startTime:L,expirationTime:V,sortIndex:-1},L>G?(A.sortIndex=L,e(d,A),n(c)===null&&A===n(d)&&(b?(w($),$=-1):b=!0,zn(S,L-G))):(A.sortIndex=V,e(c,A),x||v||(x=!0,Fi(T))),A},t.unstable_shouldYield=H,t.unstable_wrapCallback=function(A){var O=f;return function(){var L=f;f=O;try{return A.apply(this,arguments)}finally{f=L}}}})(qd),Jd.exports=qd;var xx=Jd.exports;/**
 * @license React
 * react-dom.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */var yx=I,ze=xx;function j(t){for(var e="https://reactjs.org/docs/error-decoder.html?invariant="+t,n=1;n<arguments.length;n++)e+="&args[]="+encodeURIComponent(arguments[n]);return"Minified React error #"+t+"; visit "+e+" for the full message or use the non-minified dev environment for full errors and additional helpful warnings."}var Zd=new Set,Ir={};function hn(t,e){_n(t,e),_n(t+"Capture",e)}function _n(t,e){for(Ir[t]=e,t=0;t<e.length;t++)Zd.add(e[t])}var vt=!(typeof window>"u"||typeof window.document>"u"||typeof window.document.createElement>"u"),ia=Object.prototype.hasOwnProperty,wx=/^[:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD][:A-Z_a-z\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02FF\u0370-\u037D\u037F-\u1FFF\u200C-\u200D\u2070-\u218F\u2C00-\u2FEF\u3001-\uD7FF\uF900-\uFDCF\uFDF0-\uFFFD\-.0-9\u00B7\u0300-\u036F\u203F-\u2040]*$/,eu={},tu={};function vx(t){return ia.call(tu,t)?!0:ia.call(eu,t)?!1:wx.test(t)?tu[t]=!0:(eu[t]=!0,!1)}function bx(t,e,n,r){if(n!==null&&n.type===0)return!1;switch(typeof e){case"function":case"symbol":return!0;case"boolean":return r?!1:n!==null?!n.acceptsBooleans:(t=t.toLowerCase().slice(0,5),t!=="data-"&&t!=="aria-");default:return!1}}function Sx(t,e,n,r){if(e===null||typeof e>"u"||bx(t,e,n,r))return!0;if(r)return!1;if(n!==null)switch(n.type){case 3:return!e;case 4:return e===!1;case 5:return isNaN(e);case 6:return isNaN(e)||1>e}return!1}function $e(t,e,n,r,i,o,s){this.acceptsBooleans=e===2||e===3||e===4,this.attributeName=r,this.attributeNamespace=i,this.mustUseProperty=n,this.propertyName=t,this.type=e,this.sanitizeURL=o,this.removeEmptyString=s}var ge={};"children dangerouslySetInnerHTML defaultValue defaultChecked innerHTML suppressContentEditableWarning suppressHydrationWarning style".split(" ").forEach(function(t){ge[t]=new $e(t,0,!1,t,null,!1,!1)}),[["acceptCharset","accept-charset"],["className","class"],["htmlFor","for"],["httpEquiv","http-equiv"]].forEach(function(t){var e=t[0];ge[e]=new $e(e,1,!1,t[1],null,!1,!1)}),["contentEditable","draggable","spellCheck","value"].forEach(function(t){ge[t]=new $e(t,2,!1,t.toLowerCase(),null,!1,!1)}),["autoReverse","externalResourcesRequired","focusable","preserveAlpha"].forEach(function(t){ge[t]=new $e(t,2,!1,t,null,!1,!1)}),"allowFullScreen async autoFocus autoPlay controls default defer disabled disablePictureInPicture disableRemotePlayback formNoValidate hidden loop noModule noValidate open playsInline readOnly required reversed scoped seamless itemScope".split(" ").forEach(function(t){ge[t]=new $e(t,3,!1,t.toLowerCase(),null,!1,!1)}),["checked","multiple","muted","selected"].forEach(function(t){ge[t]=new $e(t,3,!0,t,null,!1,!1)}),["capture","download"].forEach(function(t){ge[t]=new $e(t,4,!1,t,null,!1,!1)}),["cols","rows","size","span"].forEach(function(t){ge[t]=new $e(t,6,!1,t,null,!1,!1)}),["rowSpan","start"].forEach(function(t){ge[t]=new $e(t,5,!1,t.toLowerCase(),null,!1,!1)});var oa=/[\-:]([a-z])/g;function sa(t){return t[1].toUpperCase()}"accent-height alignment-baseline arabic-form baseline-shift cap-height clip-path clip-rule color-interpolation color-interpolation-filters color-profile color-rendering dominant-baseline enable-background fill-opacity fill-rule flood-color flood-opacity font-family font-size font-size-adjust font-stretch font-style font-variant font-weight glyph-name glyph-orientation-horizontal glyph-orientation-vertical horiz-adv-x horiz-origin-x image-rendering letter-spacing lighting-color marker-end marker-mid marker-start overline-position overline-thickness paint-order panose-1 pointer-events rendering-intent shape-rendering stop-color stop-opacity strikethrough-position strikethrough-thickness stroke-dasharray stroke-dashoffset stroke-linecap stroke-linejoin stroke-miterlimit stroke-opacity stroke-width text-anchor text-decoration text-rendering underline-position underline-thickness unicode-bidi unicode-range units-per-em v-alphabetic v-hanging v-ideographic v-mathematical vector-effect vert-adv-y vert-origin-x vert-origin-y word-spacing writing-mode xmlns:xlink x-height".split(" ").forEach(function(t){var e=t.replace(oa,sa);ge[e]=new $e(e,1,!1,t,null,!1,!1)}),"xlink:actuate xlink:arcrole xlink:role xlink:show xlink:title xlink:type".split(" ").forEach(function(t){var e=t.replace(oa,sa);ge[e]=new $e(e,1,!1,t,"http://www.w3.org/1999/xlink",!1,!1)}),["xml:base","xml:lang","xml:space"].forEach(function(t){var e=t.replace(oa,sa);ge[e]=new $e(e,1,!1,t,"http://www.w3.org/XML/1998/namespace",!1,!1)}),["tabIndex","crossOrigin"].forEach(function(t){ge[t]=new $e(t,1,!1,t.toLowerCase(),null,!1,!1)}),ge.xlinkHref=new $e("xlinkHref",1,!1,"xlink:href","http://www.w3.org/1999/xlink",!0,!1),["src","href","action","formAction"].forEach(function(t){ge[t]=new $e(t,1,!1,t.toLowerCase(),null,!0,!0)});function aa(t,e,n,r){var i=ge.hasOwnProperty(e)?ge[e]:null;(i!==null?i.type!==0:r||!(2<e.length)||e[0]!=="o"&&e[0]!=="O"||e[1]!=="n"&&e[1]!=="N")&&(Sx(e,n,i,r)&&(n=null),r||i===null?vx(e)&&(n===null?t.removeAttribute(e):t.setAttribute(e,""+n)):i.mustUseProperty?t[i.propertyName]=n===null?i.type===3?!1:"":n:(e=i.attributeName,r=i.attributeNamespace,n===null?t.removeAttribute(e):(i=i.type,n=i===3||i===4&&n===!0?"":""+n,r?t.setAttributeNS(r,e,n):t.setAttribute(e,n))))}var bt=yx.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED,Yi=Symbol.for("react.element"),Hn=Symbol.for("react.portal"),Wn=Symbol.for("react.fragment"),la=Symbol.for("react.strict_mode"),ca=Symbol.for("react.profiler"),nu=Symbol.for("react.provider"),ru=Symbol.for("react.context"),da=Symbol.for("react.forward_ref"),ua=Symbol.for("react.suspense"),ha=Symbol.for("react.suspense_list"),pa=Symbol.for("react.memo"),Bt=Symbol.for("react.lazy"),iu=Symbol.for("react.offscreen"),ou=Symbol.iterator;function Or(t){return t===null||typeof t!="object"?null:(t=ou&&t[ou]||t["@@iterator"],typeof t=="function"?t:null)}var Z=Object.assign,fa;function Lr(t){if(fa===void 0)try{throw Error()}catch(n){var e=n.stack.trim().match(/\n( *(at )?)/);fa=e&&e[1]||""}return`
`+fa+t}var ga=!1;function ma(t,e){if(!t||ga)return"";ga=!0;var n=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{if(e)if(e=function(){throw Error()},Object.defineProperty(e.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct(e,[])}catch(d){var r=d}Reflect.construct(t,[],e)}else{try{e.call()}catch(d){r=d}t.call(e.prototype)}else{try{throw Error()}catch(d){r=d}t()}}catch(d){if(d&&r&&typeof d.stack=="string"){for(var i=d.stack.split(`
`),o=r.stack.split(`
`),s=i.length-1,l=o.length-1;1<=s&&0<=l&&i[s]!==o[l];)l--;for(;1<=s&&0<=l;s--,l--)if(i[s]!==o[l]){if(s!==1||l!==1)do if(s--,l--,0>l||i[s]!==o[l]){var c=`
`+i[s].replace(" at new "," at ");return t.displayName&&c.includes("<anonymous>")&&(c=c.replace("<anonymous>",t.displayName)),c}while(1<=s&&0<=l);break}}}finally{ga=!1,Error.prepareStackTrace=n}return(t=t?t.displayName||t.name:"")?Lr(t):""}function kx(t){switch(t.tag){case 5:return Lr(t.type);case 16:return Lr("Lazy");case 13:return Lr("Suspense");case 19:return Lr("SuspenseList");case 0:case 2:case 15:return t=ma(t.type,!1),t;case 11:return t=ma(t.type.render,!1),t;case 1:return t=ma(t.type,!0),t;default:return""}}function xa(t){if(t==null)return null;if(typeof t=="function")return t.displayName||t.name||null;if(typeof t=="string")return t;switch(t){case Wn:return"Fragment";case Hn:return"Portal";case ca:return"Profiler";case la:return"StrictMode";case ua:return"Suspense";case ha:return"SuspenseList"}if(typeof t=="object")switch(t.$$typeof){case ru:return(t.displayName||"Context")+".Consumer";case nu:return(t._context.displayName||"Context")+".Provider";case da:var e=t.render;return t=t.displayName,t||(t=e.displayName||e.name||"",t=t!==""?"ForwardRef("+t+")":"ForwardRef"),t;case pa:return e=t.displayName||null,e!==null?e:xa(t.type)||"Memo";case Bt:e=t._payload,t=t._init;try{return xa(t(e))}catch{}}return null}function $x(t){var e=t.type;switch(t.tag){case 24:return"Cache";case 9:return(e.displayName||"Context")+".Consumer";case 10:return(e._context.displayName||"Context")+".Provider";case 18:return"DehydratedFragment";case 11:return t=e.render,t=t.displayName||t.name||"",e.displayName||(t!==""?"ForwardRef("+t+")":"ForwardRef");case 7:return"Fragment";case 5:return e;case 4:return"Portal";case 3:return"Root";case 6:return"Text";case 16:return xa(e);case 8:return e===la?"StrictMode":"Mode";case 22:return"Offscreen";case 12:return"Profiler";case 21:return"Scope";case 13:return"Suspense";case 19:return"SuspenseList";case 25:return"TracingMarker";case 1:case 0:case 17:case 2:case 14:case 15:if(typeof e=="function")return e.displayName||e.name||null;if(typeof e=="string")return e}return null}function Nt(t){switch(typeof t){case"boolean":case"number":case"string":case"undefined":return t;case"object":return t;default:return""}}function su(t){var e=t.type;return(t=t.nodeName)&&t.toLowerCase()==="input"&&(e==="checkbox"||e==="radio")}function Cx(t){var e=su(t)?"checked":"value",n=Object.getOwnPropertyDescriptor(t.constructor.prototype,e),r=""+t[e];if(!t.hasOwnProperty(e)&&typeof n<"u"&&typeof n.get=="function"&&typeof n.set=="function"){var i=n.get,o=n.set;return Object.defineProperty(t,e,{configurable:!0,get:function(){return i.call(this)},set:function(s){r=""+s,o.call(this,s)}}),Object.defineProperty(t,e,{enumerable:n.enumerable}),{getValue:function(){return r},setValue:function(s){r=""+s},stopTracking:function(){t._valueTracker=null,delete t[e]}}}}function Ki(t){t._valueTracker||(t._valueTracker=Cx(t))}function au(t){if(!t)return!1;var e=t._valueTracker;if(!e)return!0;var n=e.getValue(),r="";return t&&(r=su(t)?t.checked?"true":"false":t.value),t=r,t!==n?(e.setValue(t),!0):!1}function Qi(t){if(t=t||(typeof document<"u"?document:void 0),typeof t>"u")return null;try{return t.activeElement||t.body}catch{return t.body}}function ya(t,e){var n=e.checked;return Z({},e,{defaultChecked:void 0,defaultValue:void 0,value:void 0,checked:n??t._wrapperState.initialChecked})}function lu(t,e){var n=e.defaultValue==null?"":e.defaultValue,r=e.checked!=null?e.checked:e.defaultChecked;n=Nt(e.value!=null?e.value:n),t._wrapperState={initialChecked:r,initialValue:n,controlled:e.type==="checkbox"||e.type==="radio"?e.checked!=null:e.value!=null}}function cu(t,e){e=e.checked,e!=null&&aa(t,"checked",e,!1)}function wa(t,e){cu(t,e);var n=Nt(e.value),r=e.type;if(n!=null)r==="number"?(n===0&&t.value===""||t.value!=n)&&(t.value=""+n):t.value!==""+n&&(t.value=""+n);else if(r==="submit"||r==="reset"){t.removeAttribute("value");return}e.hasOwnProperty("value")?va(t,e.type,n):e.hasOwnProperty("defaultValue")&&va(t,e.type,Nt(e.defaultValue)),e.checked==null&&e.defaultChecked!=null&&(t.defaultChecked=!!e.defaultChecked)}function du(t,e,n){if(e.hasOwnProperty("value")||e.hasOwnProperty("defaultValue")){var r=e.type;if(!(r!=="submit"&&r!=="reset"||e.value!==void 0&&e.value!==null))return;e=""+t._wrapperState.initialValue,n||e===t.value||(t.value=e),t.defaultValue=e}n=t.name,n!==""&&(t.name=""),t.defaultChecked=!!t._wrapperState.initialChecked,n!==""&&(t.name=n)}function va(t,e,n){(e!=="number"||Qi(t.ownerDocument)!==t)&&(n==null?t.defaultValue=""+t._wrapperState.initialValue:t.defaultValue!==""+n&&(t.defaultValue=""+n))}var zr=Array.isArray;function Un(t,e,n,r){if(t=t.options,e){e={};for(var i=0;i<n.length;i++)e["$"+n[i]]=!0;for(n=0;n<t.length;n++)i=e.hasOwnProperty("$"+t[n].value),t[n].selected!==i&&(t[n].selected=i),i&&r&&(t[n].defaultSelected=!0)}else{for(n=""+Nt(n),e=null,i=0;i<t.length;i++){if(t[i].value===n){t[i].selected=!0,r&&(t[i].defaultSelected=!0);return}e!==null||t[i].disabled||(e=t[i])}e!==null&&(e.selected=!0)}}function ba(t,e){if(e.dangerouslySetInnerHTML!=null)throw Error(j(91));return Z({},e,{value:void 0,defaultValue:void 0,children:""+t._wrapperState.initialValue})}function uu(t,e){var n=e.value;if(n==null){if(n=e.children,e=e.defaultValue,n!=null){if(e!=null)throw Error(j(92));if(zr(n)){if(1<n.length)throw Error(j(93));n=n[0]}e=n}e==null&&(e=""),n=e}t._wrapperState={initialValue:Nt(n)}}function hu(t,e){var n=Nt(e.value),r=Nt(e.defaultValue);n!=null&&(n=""+n,n!==t.value&&(t.value=n),e.defaultValue==null&&t.defaultValue!==n&&(t.defaultValue=n)),r!=null&&(t.defaultValue=""+r)}function pu(t){var e=t.textContent;e===t._wrapperState.initialValue&&e!==""&&e!==null&&(t.value=e)}function fu(t){switch(t){case"svg":return"http://www.w3.org/2000/svg";case"math":return"http://www.w3.org/1998/Math/MathML";default:return"http://www.w3.org/1999/xhtml"}}function Sa(t,e){return t==null||t==="http://www.w3.org/1999/xhtml"?fu(e):t==="http://www.w3.org/2000/svg"&&e==="foreignObject"?"http://www.w3.org/1999/xhtml":t}var Xi,gu=function(t){return typeof MSApp<"u"&&MSApp.execUnsafeLocalFunction?function(e,n,r,i){MSApp.execUnsafeLocalFunction(function(){return t(e,n,r,i)})}:t}(function(t,e){if(t.namespaceURI!=="http://www.w3.org/2000/svg"||"innerHTML"in t)t.innerHTML=e;else{for(Xi=Xi||document.createElement("div"),Xi.innerHTML="<svg>"+e.valueOf().toString()+"</svg>",e=Xi.firstChild;t.firstChild;)t.removeChild(t.firstChild);for(;e.firstChild;)t.appendChild(e.firstChild)}});function Br(t,e){if(e){var n=t.firstChild;if(n&&n===t.lastChild&&n.nodeType===3){n.nodeValue=e;return}}t.textContent=e}var Nr={animationIterationCount:!0,aspectRatio:!0,borderImageOutset:!0,borderImageSlice:!0,borderImageWidth:!0,boxFlex:!0,boxFlexGroup:!0,boxOrdinalGroup:!0,columnCount:!0,columns:!0,flex:!0,flexGrow:!0,flexPositive:!0,flexShrink:!0,flexNegative:!0,flexOrder:!0,gridArea:!0,gridRow:!0,gridRowEnd:!0,gridRowSpan:!0,gridRowStart:!0,gridColumn:!0,gridColumnEnd:!0,gridColumnSpan:!0,gridColumnStart:!0,fontWeight:!0,lineClamp:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,tabSize:!0,widows:!0,zIndex:!0,zoom:!0,fillOpacity:!0,floodOpacity:!0,stopOpacity:!0,strokeDasharray:!0,strokeDashoffset:!0,strokeMiterlimit:!0,strokeOpacity:!0,strokeWidth:!0},Tx=["Webkit","ms","Moz","O"];Object.keys(Nr).forEach(function(t){Tx.forEach(function(e){e=e+t.charAt(0).toUpperCase()+t.substring(1),Nr[e]=Nr[t]})});function mu(t,e,n){return e==null||typeof e=="boolean"||e===""?"":n||typeof e!="number"||e===0||Nr.hasOwnProperty(t)&&Nr[t]?(""+e).trim():e+"px"}function xu(t,e){t=t.style;for(var n in e)if(e.hasOwnProperty(n)){var r=n.indexOf("--")===0,i=mu(n,e[n],r);n==="float"&&(n="cssFloat"),r?t.setProperty(n,i):t[n]=i}}var Mx=Z({menuitem:!0},{area:!0,base:!0,br:!0,col:!0,embed:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0});function ka(t,e){if(e){if(Mx[t]&&(e.children!=null||e.dangerouslySetInnerHTML!=null))throw Error(j(137,t));if(e.dangerouslySetInnerHTML!=null){if(e.children!=null)throw Error(j(60));if(typeof e.dangerouslySetInnerHTML!="object"||!("__html"in e.dangerouslySetInnerHTML))throw Error(j(61))}if(e.style!=null&&typeof e.style!="object")throw Error(j(62))}}function $a(t,e){if(t.indexOf("-")===-1)return typeof e.is=="string";switch(t){case"annotation-xml":case"color-profile":case"font-face":case"font-face-src":case"font-face-uri":case"font-face-format":case"font-face-name":case"missing-glyph":return!1;default:return!0}}var Ca=null;function Ta(t){return t=t.target||t.srcElement||window,t.correspondingUseElement&&(t=t.correspondingUseElement),t.nodeType===3?t.parentNode:t}var Ma=null,Gn=null,Vn=null;function yu(t){if(t=ai(t)){if(typeof Ma!="function")throw Error(j(280));var e=t.stateNode;e&&(e=vo(e),Ma(t.stateNode,t.type,e))}}function wu(t){Gn?Vn?Vn.push(t):Vn=[t]:Gn=t}function vu(){if(Gn){var t=Gn,e=Vn;if(Vn=Gn=null,yu(t),e)for(t=0;t<e.length;t++)yu(e[t])}}function bu(t,e){return t(e)}function Su(){}var ja=!1;function ku(t,e,n){if(ja)return t(e,n);ja=!0;try{return bu(t,e,n)}finally{ja=!1,(Gn!==null||Vn!==null)&&(Su(),vu())}}function Fr(t,e){var n=t.stateNode;if(n===null)return null;var r=vo(n);if(r===null)return null;n=r[e];e:switch(e){case"onClick":case"onClickCapture":case"onDoubleClick":case"onDoubleClickCapture":case"onMouseDown":case"onMouseDownCapture":case"onMouseMove":case"onMouseMoveCapture":case"onMouseUp":case"onMouseUpCapture":case"onMouseEnter":(r=!r.disabled)||(t=t.type,r=!(t==="button"||t==="input"||t==="select"||t==="textarea")),t=!r;break e;default:t=!1}if(t)return null;if(n&&typeof n!="function")throw Error(j(231,e,typeof n));return n}var Ea=!1;if(vt)try{var _r={};Object.defineProperty(_r,"passive",{get:function(){Ea=!0}}),window.addEventListener("test",_r,_r),window.removeEventListener("test",_r,_r)}catch{Ea=!1}function jx(t,e,n,r,i,o,s,l,c){var d=Array.prototype.slice.call(arguments,3);try{e.apply(n,d)}catch(h){this.onError(h)}}var Hr=!1,Ji=null,qi=!1,Da=null,Ex={onError:function(t){Hr=!0,Ji=t}};function Dx(t,e,n,r,i,o,s,l,c){Hr=!1,Ji=null,jx.apply(Ex,arguments)}function Px(t,e,n,r,i,o,s,l,c){if(Dx.apply(this,arguments),Hr){if(Hr){var d=Ji;Hr=!1,Ji=null}else throw Error(j(198));qi||(qi=!0,Da=d)}}function pn(t){var e=t,n=t;if(t.alternate)for(;e.return;)e=e.return;else{t=e;do e=t,e.flags&4098&&(n=e.return),t=e.return;while(t)}return e.tag===3?n:null}function $u(t){if(t.tag===13){var e=t.memoizedState;if(e===null&&(t=t.alternate,t!==null&&(e=t.memoizedState)),e!==null)return e.dehydrated}return null}function Cu(t){if(pn(t)!==t)throw Error(j(188))}function Ax(t){var e=t.alternate;if(!e){if(e=pn(t),e===null)throw Error(j(188));return e!==t?null:t}for(var n=t,r=e;;){var i=n.return;if(i===null)break;var o=i.alternate;if(o===null){if(r=i.return,r!==null){n=r;continue}break}if(i.child===o.child){for(o=i.child;o;){if(o===n)return Cu(i),t;if(o===r)return Cu(i),e;o=o.sibling}throw Error(j(188))}if(n.return!==r.return)n=i,r=o;else{for(var s=!1,l=i.child;l;){if(l===n){s=!0,n=i,r=o;break}if(l===r){s=!0,r=i,n=o;break}l=l.sibling}if(!s){for(l=o.child;l;){if(l===n){s=!0,n=o,r=i;break}if(l===r){s=!0,r=o,n=i;break}l=l.sibling}if(!s)throw Error(j(189))}}if(n.alternate!==r)throw Error(j(190))}if(n.tag!==3)throw Error(j(188));return n.stateNode.current===n?t:e}function Tu(t){return t=Ax(t),t!==null?Mu(t):null}function Mu(t){if(t.tag===5||t.tag===6)return t;for(t=t.child;t!==null;){var e=Mu(t);if(e!==null)return e;t=t.sibling}return null}var ju=ze.unstable_scheduleCallback,Eu=ze.unstable_cancelCallback,Rx=ze.unstable_shouldYield,Ix=ze.unstable_requestPaint,oe=ze.unstable_now,Ox=ze.unstable_getCurrentPriorityLevel,Pa=ze.unstable_ImmediatePriority,Du=ze.unstable_UserBlockingPriority,Zi=ze.unstable_NormalPriority,Lx=ze.unstable_LowPriority,Pu=ze.unstable_IdlePriority,eo=null,ft=null;function zx(t){if(ft&&typeof ft.onCommitFiberRoot=="function")try{ft.onCommitFiberRoot(eo,t,void 0,(t.current.flags&128)===128)}catch{}}var et=Math.clz32?Math.clz32:Fx,Bx=Math.log,Nx=Math.LN2;function Fx(t){return t>>>=0,t===0?32:31-(Bx(t)/Nx|0)|0}var to=64,no=4194304;function Wr(t){switch(t&-t){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return t&4194240;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return t&130023424;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 1073741824;default:return t}}function ro(t,e){var n=t.pendingLanes;if(n===0)return 0;var r=0,i=t.suspendedLanes,o=t.pingedLanes,s=n&268435455;if(s!==0){var l=s&~i;l!==0?r=Wr(l):(o&=s,o!==0&&(r=Wr(o)))}else s=n&~i,s!==0?r=Wr(s):o!==0&&(r=Wr(o));if(r===0)return 0;if(e!==0&&e!==r&&!(e&i)&&(i=r&-r,o=e&-e,i>=o||i===16&&(o&4194240)!==0))return e;if(r&4&&(r|=n&16),e=t.entangledLanes,e!==0)for(t=t.entanglements,e&=r;0<e;)n=31-et(e),i=1<<n,r|=t[n],e&=~i;return r}function _x(t,e){switch(t){case 1:case 2:case 4:return e+250;case 8:case 16:case 32:case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return e+5e3;case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:return-1;case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function Hx(t,e){for(var n=t.suspendedLanes,r=t.pingedLanes,i=t.expirationTimes,o=t.pendingLanes;0<o;){var s=31-et(o),l=1<<s,c=i[s];c===-1?(!(l&n)||l&r)&&(i[s]=_x(l,e)):c<=e&&(t.expiredLanes|=l),o&=~l}}function Aa(t){return t=t.pendingLanes&-1073741825,t!==0?t:t&1073741824?1073741824:0}function Au(){var t=to;return to<<=1,!(to&4194240)&&(to=64),t}function Ra(t){for(var e=[],n=0;31>n;n++)e.push(t);return e}function Ur(t,e,n){t.pendingLanes|=e,e!==536870912&&(t.suspendedLanes=0,t.pingedLanes=0),t=t.eventTimes,e=31-et(e),t[e]=n}function Wx(t,e){var n=t.pendingLanes&~e;t.pendingLanes=e,t.suspendedLanes=0,t.pingedLanes=0,t.expiredLanes&=e,t.mutableReadLanes&=e,t.entangledLanes&=e,e=t.entanglements;var r=t.eventTimes;for(t=t.expirationTimes;0<n;){var i=31-et(n),o=1<<i;e[i]=0,r[i]=-1,t[i]=-1,n&=~o}}function Ia(t,e){var n=t.entangledLanes|=e;for(t=t.entanglements;n;){var r=31-et(n),i=1<<r;i&e|t[r]&e&&(t[r]|=e),n&=~i}}var U=0;function Ru(t){return t&=-t,1<t?4<t?t&268435455?16:536870912:4:1}var Iu,Oa,Ou,Lu,zu,La=!1,io=[],Ft=null,_t=null,Ht=null,Gr=new Map,Vr=new Map,Wt=[],Ux="mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset submit".split(" ");function Bu(t,e){switch(t){case"focusin":case"focusout":Ft=null;break;case"dragenter":case"dragleave":_t=null;break;case"mouseover":case"mouseout":Ht=null;break;case"pointerover":case"pointerout":Gr.delete(e.pointerId);break;case"gotpointercapture":case"lostpointercapture":Vr.delete(e.pointerId)}}function Yr(t,e,n,r,i,o){return t===null||t.nativeEvent!==o?(t={blockedOn:e,domEventName:n,eventSystemFlags:r,nativeEvent:o,targetContainers:[i]},e!==null&&(e=ai(e),e!==null&&Oa(e)),t):(t.eventSystemFlags|=r,e=t.targetContainers,i!==null&&e.indexOf(i)===-1&&e.push(i),t)}function Gx(t,e,n,r,i){switch(e){case"focusin":return Ft=Yr(Ft,t,e,n,r,i),!0;case"dragenter":return _t=Yr(_t,t,e,n,r,i),!0;case"mouseover":return Ht=Yr(Ht,t,e,n,r,i),!0;case"pointerover":var o=i.pointerId;return Gr.set(o,Yr(Gr.get(o)||null,t,e,n,r,i)),!0;case"gotpointercapture":return o=i.pointerId,Vr.set(o,Yr(Vr.get(o)||null,t,e,n,r,i)),!0}return!1}function Nu(t){var e=fn(t.target);if(e!==null){var n=pn(e);if(n!==null){if(e=n.tag,e===13){if(e=$u(n),e!==null){t.blockedOn=e,zu(t.priority,function(){Ou(n)});return}}else if(e===3&&n.stateNode.current.memoizedState.isDehydrated){t.blockedOn=n.tag===3?n.stateNode.containerInfo:null;return}}}t.blockedOn=null}function oo(t){if(t.blockedOn!==null)return!1;for(var e=t.targetContainers;0<e.length;){var n=Ba(t.domEventName,t.eventSystemFlags,e[0],t.nativeEvent);if(n===null){n=t.nativeEvent;var r=new n.constructor(n.type,n);Ca=r,n.target.dispatchEvent(r),Ca=null}else return e=ai(n),e!==null&&Oa(e),t.blockedOn=n,!1;e.shift()}return!0}function Fu(t,e,n){oo(t)&&n.delete(e)}function Vx(){La=!1,Ft!==null&&oo(Ft)&&(Ft=null),_t!==null&&oo(_t)&&(_t=null),Ht!==null&&oo(Ht)&&(Ht=null),Gr.forEach(Fu),Vr.forEach(Fu)}function Kr(t,e){t.blockedOn===e&&(t.blockedOn=null,La||(La=!0,ze.unstable_scheduleCallback(ze.unstable_NormalPriority,Vx)))}function Qr(t){function e(i){return Kr(i,t)}if(0<io.length){Kr(io[0],t);for(var n=1;n<io.length;n++){var r=io[n];r.blockedOn===t&&(r.blockedOn=null)}}for(Ft!==null&&Kr(Ft,t),_t!==null&&Kr(_t,t),Ht!==null&&Kr(Ht,t),Gr.forEach(e),Vr.forEach(e),n=0;n<Wt.length;n++)r=Wt[n],r.blockedOn===t&&(r.blockedOn=null);for(;0<Wt.length&&(n=Wt[0],n.blockedOn===null);)Nu(n),n.blockedOn===null&&Wt.shift()}var Yn=bt.ReactCurrentBatchConfig,so=!0;function Yx(t,e,n,r){var i=U,o=Yn.transition;Yn.transition=null;try{U=1,za(t,e,n,r)}finally{U=i,Yn.transition=o}}function Kx(t,e,n,r){var i=U,o=Yn.transition;Yn.transition=null;try{U=4,za(t,e,n,r)}finally{U=i,Yn.transition=o}}function za(t,e,n,r){if(so){var i=Ba(t,e,n,r);if(i===null)tl(t,e,r,ao,n),Bu(t,r);else if(Gx(i,t,e,n,r))r.stopPropagation();else if(Bu(t,r),e&4&&-1<Ux.indexOf(t)){for(;i!==null;){var o=ai(i);if(o!==null&&Iu(o),o=Ba(t,e,n,r),o===null&&tl(t,e,r,ao,n),o===i)break;i=o}i!==null&&r.stopPropagation()}else tl(t,e,r,null,n)}}var ao=null;function Ba(t,e,n,r){if(ao=null,t=Ta(r),t=fn(t),t!==null)if(e=pn(t),e===null)t=null;else if(n=e.tag,n===13){if(t=$u(e),t!==null)return t;t=null}else if(n===3){if(e.stateNode.current.memoizedState.isDehydrated)return e.tag===3?e.stateNode.containerInfo:null;t=null}else e!==t&&(t=null);return ao=t,null}function _u(t){switch(t){case"cancel":case"click":case"close":case"contextmenu":case"copy":case"cut":case"auxclick":case"dblclick":case"dragend":case"dragstart":case"drop":case"focusin":case"focusout":case"input":case"invalid":case"keydown":case"keypress":case"keyup":case"mousedown":case"mouseup":case"paste":case"pause":case"play":case"pointercancel":case"pointerdown":case"pointerup":case"ratechange":case"reset":case"resize":case"seeked":case"submit":case"touchcancel":case"touchend":case"touchstart":case"volumechange":case"change":case"selectionchange":case"textInput":case"compositionstart":case"compositionend":case"compositionupdate":case"beforeblur":case"afterblur":case"beforeinput":case"blur":case"fullscreenchange":case"focus":case"hashchange":case"popstate":case"select":case"selectstart":return 1;case"drag":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"mousemove":case"mouseout":case"mouseover":case"pointermove":case"pointerout":case"pointerover":case"scroll":case"toggle":case"touchmove":case"wheel":case"mouseenter":case"mouseleave":case"pointerenter":case"pointerleave":return 4;case"message":switch(Ox()){case Pa:return 1;case Du:return 4;case Zi:case Lx:return 16;case Pu:return 536870912;default:return 16}default:return 16}}var Ut=null,Na=null,lo=null;function Hu(){if(lo)return lo;var t,e=Na,n=e.length,r,i="value"in Ut?Ut.value:Ut.textContent,o=i.length;for(t=0;t<n&&e[t]===i[t];t++);var s=n-t;for(r=1;r<=s&&e[n-r]===i[o-r];r++);return lo=i.slice(t,1<r?1-r:void 0)}function co(t){var e=t.keyCode;return"charCode"in t?(t=t.charCode,t===0&&e===13&&(t=13)):t=e,t===10&&(t=13),32<=t||t===13?t:0}function uo(){return!0}function Wu(){return!1}function Be(t){function e(n,r,i,o,s){this._reactName=n,this._targetInst=i,this.type=r,this.nativeEvent=o,this.target=s,this.currentTarget=null;for(var l in t)t.hasOwnProperty(l)&&(n=t[l],this[l]=n?n(o):o[l]);return this.isDefaultPrevented=(o.defaultPrevented!=null?o.defaultPrevented:o.returnValue===!1)?uo:Wu,this.isPropagationStopped=Wu,this}return Z(e.prototype,{preventDefault:function(){this.defaultPrevented=!0;var n=this.nativeEvent;n&&(n.preventDefault?n.preventDefault():typeof n.returnValue!="unknown"&&(n.returnValue=!1),this.isDefaultPrevented=uo)},stopPropagation:function(){var n=this.nativeEvent;n&&(n.stopPropagation?n.stopPropagation():typeof n.cancelBubble!="unknown"&&(n.cancelBubble=!0),this.isPropagationStopped=uo)},persist:function(){},isPersistent:uo}),e}var Kn={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(t){return t.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Fa=Be(Kn),Xr=Z({},Kn,{view:0,detail:0}),Qx=Be(Xr),_a,Ha,Jr,ho=Z({},Xr,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:Ua,button:0,buttons:0,relatedTarget:function(t){return t.relatedTarget===void 0?t.fromElement===t.srcElement?t.toElement:t.fromElement:t.relatedTarget},movementX:function(t){return"movementX"in t?t.movementX:(t!==Jr&&(Jr&&t.type==="mousemove"?(_a=t.screenX-Jr.screenX,Ha=t.screenY-Jr.screenY):Ha=_a=0,Jr=t),_a)},movementY:function(t){return"movementY"in t?t.movementY:Ha}}),Uu=Be(ho),Xx=Z({},ho,{dataTransfer:0}),Jx=Be(Xx),qx=Z({},Xr,{relatedTarget:0}),Wa=Be(qx),Zx=Z({},Kn,{animationName:0,elapsedTime:0,pseudoElement:0}),e0=Be(Zx),t0=Z({},Kn,{clipboardData:function(t){return"clipboardData"in t?t.clipboardData:window.clipboardData}}),n0=Be(t0),r0=Z({},Kn,{data:0}),Gu=Be(r0),i0={Esc:"Escape",Spacebar:" ",Left:"ArrowLeft",Up:"ArrowUp",Right:"ArrowRight",Down:"ArrowDown",Del:"Delete",Win:"OS",Menu:"ContextMenu",Apps:"ContextMenu",Scroll:"ScrollLock",MozPrintableKey:"Unidentified"},o0={8:"Backspace",9:"Tab",12:"Clear",13:"Enter",16:"Shift",17:"Control",18:"Alt",19:"Pause",20:"CapsLock",27:"Escape",32:" ",33:"PageUp",34:"PageDown",35:"End",36:"Home",37:"ArrowLeft",38:"ArrowUp",39:"ArrowRight",40:"ArrowDown",45:"Insert",46:"Delete",112:"F1",113:"F2",114:"F3",115:"F4",116:"F5",117:"F6",118:"F7",119:"F8",120:"F9",121:"F10",122:"F11",123:"F12",144:"NumLock",145:"ScrollLock",224:"Meta"},s0={Alt:"altKey",Control:"ctrlKey",Meta:"metaKey",Shift:"shiftKey"};function a0(t){var e=this.nativeEvent;return e.getModifierState?e.getModifierState(t):(t=s0[t])?!!e[t]:!1}function Ua(){return a0}var l0=Z({},Xr,{key:function(t){if(t.key){var e=i0[t.key]||t.key;if(e!=="Unidentified")return e}return t.type==="keypress"?(t=co(t),t===13?"Enter":String.fromCharCode(t)):t.type==="keydown"||t.type==="keyup"?o0[t.keyCode]||"Unidentified":""},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:Ua,charCode:function(t){return t.type==="keypress"?co(t):0},keyCode:function(t){return t.type==="keydown"||t.type==="keyup"?t.keyCode:0},which:function(t){return t.type==="keypress"?co(t):t.type==="keydown"||t.type==="keyup"?t.keyCode:0}}),c0=Be(l0),d0=Z({},ho,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0}),Vu=Be(d0),u0=Z({},Xr,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:Ua}),h0=Be(u0),p0=Z({},Kn,{propertyName:0,elapsedTime:0,pseudoElement:0}),f0=Be(p0),g0=Z({},ho,{deltaX:function(t){return"deltaX"in t?t.deltaX:"wheelDeltaX"in t?-t.wheelDeltaX:0},deltaY:function(t){return"deltaY"in t?t.deltaY:"wheelDeltaY"in t?-t.wheelDeltaY:"wheelDelta"in t?-t.wheelDelta:0},deltaZ:0,deltaMode:0}),m0=Be(g0),x0=[9,13,27,32],Ga=vt&&"CompositionEvent"in window,qr=null;vt&&"documentMode"in document&&(qr=document.documentMode);var y0=vt&&"TextEvent"in window&&!qr,Yu=vt&&(!Ga||qr&&8<qr&&11>=qr),Ku=" ",Qu=!1;function Xu(t,e){switch(t){case"keyup":return x0.indexOf(e.keyCode)!==-1;case"keydown":return e.keyCode!==229;case"keypress":case"mousedown":case"focusout":return!0;default:return!1}}function Ju(t){return t=t.detail,typeof t=="object"&&"data"in t?t.data:null}var Qn=!1;function w0(t,e){switch(t){case"compositionend":return Ju(e);case"keypress":return e.which!==32?null:(Qu=!0,Ku);case"textInput":return t=e.data,t===Ku&&Qu?null:t;default:return null}}function v0(t,e){if(Qn)return t==="compositionend"||!Ga&&Xu(t,e)?(t=Hu(),lo=Na=Ut=null,Qn=!1,t):null;switch(t){case"paste":return null;case"keypress":if(!(e.ctrlKey||e.altKey||e.metaKey)||e.ctrlKey&&e.altKey){if(e.char&&1<e.char.length)return e.char;if(e.which)return String.fromCharCode(e.which)}return null;case"compositionend":return Yu&&e.locale!=="ko"?null:e.data;default:return null}}var b0={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function qu(t){var e=t&&t.nodeName&&t.nodeName.toLowerCase();return e==="input"?!!b0[t.type]:e==="textarea"}function Zu(t,e,n,r){wu(r),e=xo(e,"onChange"),0<e.length&&(n=new Fa("onChange","change",null,n,r),t.push({event:n,listeners:e}))}var Zr=null,ei=null;function S0(t){xh(t,0)}function po(t){var e=er(t);if(au(e))return t}function k0(t,e){if(t==="change")return e}var eh=!1;if(vt){var Va;if(vt){var Ya="oninput"in document;if(!Ya){var th=document.createElement("div");th.setAttribute("oninput","return;"),Ya=typeof th.oninput=="function"}Va=Ya}else Va=!1;eh=Va&&(!document.documentMode||9<document.documentMode)}function nh(){Zr&&(Zr.detachEvent("onpropertychange",rh),ei=Zr=null)}function rh(t){if(t.propertyName==="value"&&po(ei)){var e=[];Zu(e,ei,t,Ta(t)),ku(S0,e)}}function $0(t,e,n){t==="focusin"?(nh(),Zr=e,ei=n,Zr.attachEvent("onpropertychange",rh)):t==="focusout"&&nh()}function C0(t){if(t==="selectionchange"||t==="keyup"||t==="keydown")return po(ei)}function T0(t,e){if(t==="click")return po(e)}function M0(t,e){if(t==="input"||t==="change")return po(e)}function j0(t,e){return t===e&&(t!==0||1/t===1/e)||t!==t&&e!==e}var tt=typeof Object.is=="function"?Object.is:j0;function ti(t,e){if(tt(t,e))return!0;if(typeof t!="object"||t===null||typeof e!="object"||e===null)return!1;var n=Object.keys(t),r=Object.keys(e);if(n.length!==r.length)return!1;for(r=0;r<n.length;r++){var i=n[r];if(!ia.call(e,i)||!tt(t[i],e[i]))return!1}return!0}function ih(t){for(;t&&t.firstChild;)t=t.firstChild;return t}function oh(t,e){var n=ih(t);t=0;for(var r;n;){if(n.nodeType===3){if(r=t+n.textContent.length,t<=e&&r>=e)return{node:n,offset:e-t};t=r}e:{for(;n;){if(n.nextSibling){n=n.nextSibling;break e}n=n.parentNode}n=void 0}n=ih(n)}}function sh(t,e){return t&&e?t===e?!0:t&&t.nodeType===3?!1:e&&e.nodeType===3?sh(t,e.parentNode):"contains"in t?t.contains(e):t.compareDocumentPosition?!!(t.compareDocumentPosition(e)&16):!1:!1}function ah(){for(var t=window,e=Qi();e instanceof t.HTMLIFrameElement;){try{var n=typeof e.contentWindow.location.href=="string"}catch{n=!1}if(n)t=e.contentWindow;else break;e=Qi(t.document)}return e}function Ka(t){var e=t&&t.nodeName&&t.nodeName.toLowerCase();return e&&(e==="input"&&(t.type==="text"||t.type==="search"||t.type==="tel"||t.type==="url"||t.type==="password")||e==="textarea"||t.contentEditable==="true")}function E0(t){var e=ah(),n=t.focusedElem,r=t.selectionRange;if(e!==n&&n&&n.ownerDocument&&sh(n.ownerDocument.documentElement,n)){if(r!==null&&Ka(n)){if(e=r.start,t=r.end,t===void 0&&(t=e),"selectionStart"in n)n.selectionStart=e,n.selectionEnd=Math.min(t,n.value.length);else if(t=(e=n.ownerDocument||document)&&e.defaultView||window,t.getSelection){t=t.getSelection();var i=n.textContent.length,o=Math.min(r.start,i);r=r.end===void 0?o:Math.min(r.end,i),!t.extend&&o>r&&(i=r,r=o,o=i),i=oh(n,o);var s=oh(n,r);i&&s&&(t.rangeCount!==1||t.anchorNode!==i.node||t.anchorOffset!==i.offset||t.focusNode!==s.node||t.focusOffset!==s.offset)&&(e=e.createRange(),e.setStart(i.node,i.offset),t.removeAllRanges(),o>r?(t.addRange(e),t.extend(s.node,s.offset)):(e.setEnd(s.node,s.offset),t.addRange(e)))}}for(e=[],t=n;t=t.parentNode;)t.nodeType===1&&e.push({element:t,left:t.scrollLeft,top:t.scrollTop});for(typeof n.focus=="function"&&n.focus(),n=0;n<e.length;n++)t=e[n],t.element.scrollLeft=t.left,t.element.scrollTop=t.top}}var D0=vt&&"documentMode"in document&&11>=document.documentMode,Xn=null,Qa=null,ni=null,Xa=!1;function lh(t,e,n){var r=n.window===n?n.document:n.nodeType===9?n:n.ownerDocument;Xa||Xn==null||Xn!==Qi(r)||(r=Xn,"selectionStart"in r&&Ka(r)?r={start:r.selectionStart,end:r.selectionEnd}:(r=(r.ownerDocument&&r.ownerDocument.defaultView||window).getSelection(),r={anchorNode:r.anchorNode,anchorOffset:r.anchorOffset,focusNode:r.focusNode,focusOffset:r.focusOffset}),ni&&ti(ni,r)||(ni=r,r=xo(Qa,"onSelect"),0<r.length&&(e=new Fa("onSelect","select",null,e,n),t.push({event:e,listeners:r}),e.target=Xn)))}function fo(t,e){var n={};return n[t.toLowerCase()]=e.toLowerCase(),n["Webkit"+t]="webkit"+e,n["Moz"+t]="moz"+e,n}var Jn={animationend:fo("Animation","AnimationEnd"),animationiteration:fo("Animation","AnimationIteration"),animationstart:fo("Animation","AnimationStart"),transitionend:fo("Transition","TransitionEnd")},Ja={},ch={};vt&&(ch=document.createElement("div").style,"AnimationEvent"in window||(delete Jn.animationend.animation,delete Jn.animationiteration.animation,delete Jn.animationstart.animation),"TransitionEvent"in window||delete Jn.transitionend.transition);function go(t){if(Ja[t])return Ja[t];if(!Jn[t])return t;var e=Jn[t],n;for(n in e)if(e.hasOwnProperty(n)&&n in ch)return Ja[t]=e[n];return t}var dh=go("animationend"),uh=go("animationiteration"),hh=go("animationstart"),ph=go("transitionend"),fh=new Map,gh="abort auxClick cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel".split(" ");function Gt(t,e){fh.set(t,e),hn(e,[t])}for(var qa=0;qa<gh.length;qa++){var Za=gh[qa],P0=Za.toLowerCase(),A0=Za[0].toUpperCase()+Za.slice(1);Gt(P0,"on"+A0)}Gt(dh,"onAnimationEnd"),Gt(uh,"onAnimationIteration"),Gt(hh,"onAnimationStart"),Gt("dblclick","onDoubleClick"),Gt("focusin","onFocus"),Gt("focusout","onBlur"),Gt(ph,"onTransitionEnd"),_n("onMouseEnter",["mouseout","mouseover"]),_n("onMouseLeave",["mouseout","mouseover"]),_n("onPointerEnter",["pointerout","pointerover"]),_n("onPointerLeave",["pointerout","pointerover"]),hn("onChange","change click focusin focusout input keydown keyup selectionchange".split(" ")),hn("onSelect","focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange".split(" ")),hn("onBeforeInput",["compositionend","keypress","textInput","paste"]),hn("onCompositionEnd","compositionend focusout keydown keypress keyup mousedown".split(" ")),hn("onCompositionStart","compositionstart focusout keydown keypress keyup mousedown".split(" ")),hn("onCompositionUpdate","compositionupdate focusout keydown keypress keyup mousedown".split(" "));var ri="abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting".split(" "),R0=new Set("cancel close invalid load scroll toggle".split(" ").concat(ri));function mh(t,e,n){var r=t.type||"unknown-event";t.currentTarget=n,Px(r,e,void 0,t),t.currentTarget=null}function xh(t,e){e=(e&4)!==0;for(var n=0;n<t.length;n++){var r=t[n],i=r.event;r=r.listeners;e:{var o=void 0;if(e)for(var s=r.length-1;0<=s;s--){var l=r[s],c=l.instance,d=l.currentTarget;if(l=l.listener,c!==o&&i.isPropagationStopped())break e;mh(i,l,d),o=c}else for(s=0;s<r.length;s++){if(l=r[s],c=l.instance,d=l.currentTarget,l=l.listener,c!==o&&i.isPropagationStopped())break e;mh(i,l,d),o=c}}}if(qi)throw t=Da,qi=!1,Da=null,t}function Q(t,e){var n=e[al];n===void 0&&(n=e[al]=new Set);var r=t+"__bubble";n.has(r)||(yh(e,t,2,!1),n.add(r))}function el(t,e,n){var r=0;e&&(r|=4),yh(n,t,r,e)}var mo="_reactListening"+Math.random().toString(36).slice(2);function ii(t){if(!t[mo]){t[mo]=!0,Zd.forEach(function(n){n!=="selectionchange"&&(R0.has(n)||el(n,!1,t),el(n,!0,t))});var e=t.nodeType===9?t:t.ownerDocument;e===null||e[mo]||(e[mo]=!0,el("selectionchange",!1,e))}}function yh(t,e,n,r){switch(_u(e)){case 1:var i=Yx;break;case 4:i=Kx;break;default:i=za}n=i.bind(null,e,n,t),i=void 0,!Ea||e!=="touchstart"&&e!=="touchmove"&&e!=="wheel"||(i=!0),r?i!==void 0?t.addEventListener(e,n,{capture:!0,passive:i}):t.addEventListener(e,n,!0):i!==void 0?t.addEventListener(e,n,{passive:i}):t.addEventListener(e,n,!1)}function tl(t,e,n,r,i){var o=r;if(!(e&1)&&!(e&2)&&r!==null)e:for(;;){if(r===null)return;var s=r.tag;if(s===3||s===4){var l=r.stateNode.containerInfo;if(l===i||l.nodeType===8&&l.parentNode===i)break;if(s===4)for(s=r.return;s!==null;){var c=s.tag;if((c===3||c===4)&&(c=s.stateNode.containerInfo,c===i||c.nodeType===8&&c.parentNode===i))return;s=s.return}for(;l!==null;){if(s=fn(l),s===null)return;if(c=s.tag,c===5||c===6){r=o=s;continue e}l=l.parentNode}}r=r.return}ku(function(){var d=o,h=Ta(n),p=[];e:{var f=fh.get(t);if(f!==void 0){var v=Fa,x=t;switch(t){case"keypress":if(co(n)===0)break e;case"keydown":case"keyup":v=c0;break;case"focusin":x="focus",v=Wa;break;case"focusout":x="blur",v=Wa;break;case"beforeblur":case"afterblur":v=Wa;break;case"click":if(n.button===2)break e;case"auxclick":case"dblclick":case"mousedown":case"mousemove":case"mouseup":case"mouseout":case"mouseover":case"contextmenu":v=Uu;break;case"drag":case"dragend":case"dragenter":case"dragexit":case"dragleave":case"dragover":case"dragstart":case"drop":v=Jx;break;case"touchcancel":case"touchend":case"touchmove":case"touchstart":v=h0;break;case dh:case uh:case hh:v=e0;break;case ph:v=f0;break;case"scroll":v=Qx;break;case"wheel":v=m0;break;case"copy":case"cut":case"paste":v=n0;break;case"gotpointercapture":case"lostpointercapture":case"pointercancel":case"pointerdown":case"pointermove":case"pointerout":case"pointerover":case"pointerup":v=Vu}var b=(e&4)!==0,M=!b&&t==="scroll",w=b?f!==null?f+"Capture":null:f;b=[];for(var g=d,y;g!==null;){y=g;var S=y.stateNode;if(y.tag===5&&S!==null&&(y=S,w!==null&&(S=Fr(g,w),S!=null&&b.push(oi(g,S,y)))),M)break;g=g.return}0<b.length&&(f=new v(f,x,null,n,h),p.push({event:f,listeners:b}))}}if(!(e&7)){e:{if(f=t==="mouseover"||t==="pointerover",v=t==="mouseout"||t==="pointerout",f&&n!==Ca&&(x=n.relatedTarget||n.fromElement)&&(fn(x)||x[St]))break e;if((v||f)&&(f=h.window===h?h:(f=h.ownerDocument)?f.defaultView||f.parentWindow:window,v?(x=n.relatedTarget||n.toElement,v=d,x=x?fn(x):null,x!==null&&(M=pn(x),x!==M||x.tag!==5&&x.tag!==6)&&(x=null)):(v=null,x=d),v!==x)){if(b=Uu,S="onMouseLeave",w="onMouseEnter",g="mouse",(t==="pointerout"||t==="pointerover")&&(b=Vu,S="onPointerLeave",w="onPointerEnter",g="pointer"),M=v==null?f:er(v),y=x==null?f:er(x),f=new b(S,g+"leave",v,n,h),f.target=M,f.relatedTarget=y,S=null,fn(h)===d&&(b=new b(w,g+"enter",x,n,h),b.target=y,b.relatedTarget=M,S=b),M=S,v&&x)t:{for(b=v,w=x,g=0,y=b;y;y=qn(y))g++;for(y=0,S=w;S;S=qn(S))y++;for(;0<g-y;)b=qn(b),g--;for(;0<y-g;)w=qn(w),y--;for(;g--;){if(b===w||w!==null&&b===w.alternate)break t;b=qn(b),w=qn(w)}b=null}else b=null;v!==null&&wh(p,f,v,b,!1),x!==null&&M!==null&&wh(p,M,x,b,!0)}}e:{if(f=d?er(d):window,v=f.nodeName&&f.nodeName.toLowerCase(),v==="select"||v==="input"&&f.type==="file")var T=k0;else if(qu(f))if(eh)T=M0;else{T=C0;var C=$0}else(v=f.nodeName)&&v.toLowerCase()==="input"&&(f.type==="checkbox"||f.type==="radio")&&(T=T0);if(T&&(T=T(t,d))){Zu(p,T,n,h);break e}C&&C(t,f,d),t==="focusout"&&(C=f._wrapperState)&&C.controlled&&f.type==="number"&&va(f,"number",f.value)}switch(C=d?er(d):window,t){case"focusin":(qu(C)||C.contentEditable==="true")&&(Xn=C,Qa=d,ni=null);break;case"focusout":ni=Qa=Xn=null;break;case"mousedown":Xa=!0;break;case"contextmenu":case"mouseup":case"dragend":Xa=!1,lh(p,n,h);break;case"selectionchange":if(D0)break;case"keydown":case"keyup":lh(p,n,h)}var k;if(Ga)e:{switch(t){case"compositionstart":var $="onCompositionStart";break e;case"compositionend":$="onCompositionEnd";break e;case"compositionupdate":$="onCompositionUpdate";break e}$=void 0}else Qn?Xu(t,n)&&($="onCompositionEnd"):t==="keydown"&&n.keyCode===229&&($="onCompositionStart");$&&(Yu&&n.locale!=="ko"&&(Qn||$!=="onCompositionStart"?$==="onCompositionEnd"&&Qn&&(k=Hu()):(Ut=h,Na="value"in Ut?Ut.value:Ut.textContent,Qn=!0)),C=xo(d,$),0<C.length&&($=new Gu($,t,null,n,h),p.push({event:$,listeners:C}),k?$.data=k:(k=Ju(n),k!==null&&($.data=k)))),(k=y0?w0(t,n):v0(t,n))&&(d=xo(d,"onBeforeInput"),0<d.length&&(h=new Gu("onBeforeInput","beforeinput",null,n,h),p.push({event:h,listeners:d}),h.data=k))}xh(p,e)})}function oi(t,e,n){return{instance:t,listener:e,currentTarget:n}}function xo(t,e){for(var n=e+"Capture",r=[];t!==null;){var i=t,o=i.stateNode;i.tag===5&&o!==null&&(i=o,o=Fr(t,n),o!=null&&r.unshift(oi(t,o,i)),o=Fr(t,e),o!=null&&r.push(oi(t,o,i))),t=t.return}return r}function qn(t){if(t===null)return null;do t=t.return;while(t&&t.tag!==5);return t||null}function wh(t,e,n,r,i){for(var o=e._reactName,s=[];n!==null&&n!==r;){var l=n,c=l.alternate,d=l.stateNode;if(c!==null&&c===r)break;l.tag===5&&d!==null&&(l=d,i?(c=Fr(n,o),c!=null&&s.unshift(oi(n,c,l))):i||(c=Fr(n,o),c!=null&&s.push(oi(n,c,l)))),n=n.return}s.length!==0&&t.push({event:e,listeners:s})}var I0=/\r\n?/g,O0=/\u0000|\uFFFD/g;function vh(t){return(typeof t=="string"?t:""+t).replace(I0,`
`).replace(O0,"")}function yo(t,e,n){if(e=vh(e),vh(t)!==e&&n)throw Error(j(425))}function wo(){}var nl=null,rl=null;function il(t,e){return t==="textarea"||t==="noscript"||typeof e.children=="string"||typeof e.children=="number"||typeof e.dangerouslySetInnerHTML=="object"&&e.dangerouslySetInnerHTML!==null&&e.dangerouslySetInnerHTML.__html!=null}var ol=typeof setTimeout=="function"?setTimeout:void 0,L0=typeof clearTimeout=="function"?clearTimeout:void 0,bh=typeof Promise=="function"?Promise:void 0,z0=typeof queueMicrotask=="function"?queueMicrotask:typeof bh<"u"?function(t){return bh.resolve(null).then(t).catch(B0)}:ol;function B0(t){setTimeout(function(){throw t})}function sl(t,e){var n=e,r=0;do{var i=n.nextSibling;if(t.removeChild(n),i&&i.nodeType===8)if(n=i.data,n==="/$"){if(r===0){t.removeChild(i),Qr(e);return}r--}else n!=="$"&&n!=="$?"&&n!=="$!"||r++;n=i}while(n);Qr(e)}function Vt(t){for(;t!=null;t=t.nextSibling){var e=t.nodeType;if(e===1||e===3)break;if(e===8){if(e=t.data,e==="$"||e==="$!"||e==="$?")break;if(e==="/$")return null}}return t}function Sh(t){t=t.previousSibling;for(var e=0;t;){if(t.nodeType===8){var n=t.data;if(n==="$"||n==="$!"||n==="$?"){if(e===0)return t;e--}else n==="/$"&&e++}t=t.previousSibling}return null}var Zn=Math.random().toString(36).slice(2),gt="__reactFiber$"+Zn,si="__reactProps$"+Zn,St="__reactContainer$"+Zn,al="__reactEvents$"+Zn,N0="__reactListeners$"+Zn,F0="__reactHandles$"+Zn;function fn(t){var e=t[gt];if(e)return e;for(var n=t.parentNode;n;){if(e=n[St]||n[gt]){if(n=e.alternate,e.child!==null||n!==null&&n.child!==null)for(t=Sh(t);t!==null;){if(n=t[gt])return n;t=Sh(t)}return e}t=n,n=t.parentNode}return null}function ai(t){return t=t[gt]||t[St],!t||t.tag!==5&&t.tag!==6&&t.tag!==13&&t.tag!==3?null:t}function er(t){if(t.tag===5||t.tag===6)return t.stateNode;throw Error(j(33))}function vo(t){return t[si]||null}var ll=[],tr=-1;function Yt(t){return{current:t}}function X(t){0>tr||(t.current=ll[tr],ll[tr]=null,tr--)}function Y(t,e){tr++,ll[tr]=t.current,t.current=e}var Kt={},ye=Yt(Kt),Me=Yt(!1),gn=Kt;function nr(t,e){var n=t.type.contextTypes;if(!n)return Kt;var r=t.stateNode;if(r&&r.__reactInternalMemoizedUnmaskedChildContext===e)return r.__reactInternalMemoizedMaskedChildContext;var i={},o;for(o in n)i[o]=e[o];return r&&(t=t.stateNode,t.__reactInternalMemoizedUnmaskedChildContext=e,t.__reactInternalMemoizedMaskedChildContext=i),i}function je(t){return t=t.childContextTypes,t!=null}function bo(){X(Me),X(ye)}function kh(t,e,n){if(ye.current!==Kt)throw Error(j(168));Y(ye,e),Y(Me,n)}function $h(t,e,n){var r=t.stateNode;if(e=e.childContextTypes,typeof r.getChildContext!="function")return n;r=r.getChildContext();for(var i in r)if(!(i in e))throw Error(j(108,$x(t)||"Unknown",i));return Z({},n,r)}function So(t){return t=(t=t.stateNode)&&t.__reactInternalMemoizedMergedChildContext||Kt,gn=ye.current,Y(ye,t),Y(Me,Me.current),!0}function Ch(t,e,n){var r=t.stateNode;if(!r)throw Error(j(169));n?(t=$h(t,e,gn),r.__reactInternalMemoizedMergedChildContext=t,X(Me),X(ye),Y(ye,t)):X(Me),Y(Me,n)}var kt=null,ko=!1,cl=!1;function Th(t){kt===null?kt=[t]:kt.push(t)}function _0(t){ko=!0,Th(t)}function Qt(){if(!cl&&kt!==null){cl=!0;var t=0,e=U;try{var n=kt;for(U=1;t<n.length;t++){var r=n[t];do r=r(!0);while(r!==null)}kt=null,ko=!1}catch(i){throw kt!==null&&(kt=kt.slice(t+1)),ju(Pa,Qt),i}finally{U=e,cl=!1}}return null}var rr=[],ir=0,$o=null,Co=0,Ge=[],Ve=0,mn=null,$t=1,Ct="";function xn(t,e){rr[ir++]=Co,rr[ir++]=$o,$o=t,Co=e}function Mh(t,e,n){Ge[Ve++]=$t,Ge[Ve++]=Ct,Ge[Ve++]=mn,mn=t;var r=$t;t=Ct;var i=32-et(r)-1;r&=~(1<<i),n+=1;var o=32-et(e)+i;if(30<o){var s=i-i%5;o=(r&(1<<s)-1).toString(32),r>>=s,i-=s,$t=1<<32-et(e)+i|n<<i|r,Ct=o+t}else $t=1<<o|n<<i|r,Ct=t}function dl(t){t.return!==null&&(xn(t,1),Mh(t,1,0))}function ul(t){for(;t===$o;)$o=rr[--ir],rr[ir]=null,Co=rr[--ir],rr[ir]=null;for(;t===mn;)mn=Ge[--Ve],Ge[Ve]=null,Ct=Ge[--Ve],Ge[Ve]=null,$t=Ge[--Ve],Ge[Ve]=null}var Ne=null,Fe=null,q=!1,nt=null;function jh(t,e){var n=Xe(5,null,null,0);n.elementType="DELETED",n.stateNode=e,n.return=t,e=t.deletions,e===null?(t.deletions=[n],t.flags|=16):e.push(n)}function Eh(t,e){switch(t.tag){case 5:var n=t.type;return e=e.nodeType!==1||n.toLowerCase()!==e.nodeName.toLowerCase()?null:e,e!==null?(t.stateNode=e,Ne=t,Fe=Vt(e.firstChild),!0):!1;case 6:return e=t.pendingProps===""||e.nodeType!==3?null:e,e!==null?(t.stateNode=e,Ne=t,Fe=null,!0):!1;case 13:return e=e.nodeType!==8?null:e,e!==null?(n=mn!==null?{id:$t,overflow:Ct}:null,t.memoizedState={dehydrated:e,treeContext:n,retryLane:1073741824},n=Xe(18,null,null,0),n.stateNode=e,n.return=t,t.child=n,Ne=t,Fe=null,!0):!1;default:return!1}}function hl(t){return(t.mode&1)!==0&&(t.flags&128)===0}function pl(t){if(q){var e=Fe;if(e){var n=e;if(!Eh(t,e)){if(hl(t))throw Error(j(418));e=Vt(n.nextSibling);var r=Ne;e&&Eh(t,e)?jh(r,n):(t.flags=t.flags&-4097|2,q=!1,Ne=t)}}else{if(hl(t))throw Error(j(418));t.flags=t.flags&-4097|2,q=!1,Ne=t}}}function Dh(t){for(t=t.return;t!==null&&t.tag!==5&&t.tag!==3&&t.tag!==13;)t=t.return;Ne=t}function To(t){if(t!==Ne)return!1;if(!q)return Dh(t),q=!0,!1;var e;if((e=t.tag!==3)&&!(e=t.tag!==5)&&(e=t.type,e=e!=="head"&&e!=="body"&&!il(t.type,t.memoizedProps)),e&&(e=Fe)){if(hl(t))throw Ph(),Error(j(418));for(;e;)jh(t,e),e=Vt(e.nextSibling)}if(Dh(t),t.tag===13){if(t=t.memoizedState,t=t!==null?t.dehydrated:null,!t)throw Error(j(317));e:{for(t=t.nextSibling,e=0;t;){if(t.nodeType===8){var n=t.data;if(n==="/$"){if(e===0){Fe=Vt(t.nextSibling);break e}e--}else n!=="$"&&n!=="$!"&&n!=="$?"||e++}t=t.nextSibling}Fe=null}}else Fe=Ne?Vt(t.stateNode.nextSibling):null;return!0}function Ph(){for(var t=Fe;t;)t=Vt(t.nextSibling)}function or(){Fe=Ne=null,q=!1}function fl(t){nt===null?nt=[t]:nt.push(t)}var H0=bt.ReactCurrentBatchConfig;function li(t,e,n){if(t=n.ref,t!==null&&typeof t!="function"&&typeof t!="object"){if(n._owner){if(n=n._owner,n){if(n.tag!==1)throw Error(j(309));var r=n.stateNode}if(!r)throw Error(j(147,t));var i=r,o=""+t;return e!==null&&e.ref!==null&&typeof e.ref=="function"&&e.ref._stringRef===o?e.ref:(e=function(s){var l=i.refs;s===null?delete l[o]:l[o]=s},e._stringRef=o,e)}if(typeof t!="string")throw Error(j(284));if(!n._owner)throw Error(j(290,t))}return t}function Mo(t,e){throw t=Object.prototype.toString.call(e),Error(j(31,t==="[object Object]"?"object with keys {"+Object.keys(e).join(", ")+"}":t))}function Ah(t){var e=t._init;return e(t._payload)}function Rh(t){function e(w,g){if(t){var y=w.deletions;y===null?(w.deletions=[g],w.flags|=16):y.push(g)}}function n(w,g){if(!t)return null;for(;g!==null;)e(w,g),g=g.sibling;return null}function r(w,g){for(w=new Map;g!==null;)g.key!==null?w.set(g.key,g):w.set(g.index,g),g=g.sibling;return w}function i(w,g){return w=rn(w,g),w.index=0,w.sibling=null,w}function o(w,g,y){return w.index=y,t?(y=w.alternate,y!==null?(y=y.index,y<g?(w.flags|=2,g):y):(w.flags|=2,g)):(w.flags|=1048576,g)}function s(w){return t&&w.alternate===null&&(w.flags|=2),w}function l(w,g,y,S){return g===null||g.tag!==6?(g=oc(y,w.mode,S),g.return=w,g):(g=i(g,y),g.return=w,g)}function c(w,g,y,S){var T=y.type;return T===Wn?h(w,g,y.props.children,S,y.key):g!==null&&(g.elementType===T||typeof T=="object"&&T!==null&&T.$$typeof===Bt&&Ah(T)===g.type)?(S=i(g,y.props),S.ref=li(w,g,y),S.return=w,S):(S=Jo(y.type,y.key,y.props,null,w.mode,S),S.ref=li(w,g,y),S.return=w,S)}function d(w,g,y,S){return g===null||g.tag!==4||g.stateNode.containerInfo!==y.containerInfo||g.stateNode.implementation!==y.implementation?(g=sc(y,w.mode,S),g.return=w,g):(g=i(g,y.children||[]),g.return=w,g)}function h(w,g,y,S,T){return g===null||g.tag!==7?(g=Cn(y,w.mode,S,T),g.return=w,g):(g=i(g,y),g.return=w,g)}function p(w,g,y){if(typeof g=="string"&&g!==""||typeof g=="number")return g=oc(""+g,w.mode,y),g.return=w,g;if(typeof g=="object"&&g!==null){switch(g.$$typeof){case Yi:return y=Jo(g.type,g.key,g.props,null,w.mode,y),y.ref=li(w,null,g),y.return=w,y;case Hn:return g=sc(g,w.mode,y),g.return=w,g;case Bt:var S=g._init;return p(w,S(g._payload),y)}if(zr(g)||Or(g))return g=Cn(g,w.mode,y,null),g.return=w,g;Mo(w,g)}return null}function f(w,g,y,S){var T=g!==null?g.key:null;if(typeof y=="string"&&y!==""||typeof y=="number")return T!==null?null:l(w,g,""+y,S);if(typeof y=="object"&&y!==null){switch(y.$$typeof){case Yi:return y.key===T?c(w,g,y,S):null;case Hn:return y.key===T?d(w,g,y,S):null;case Bt:return T=y._init,f(w,g,T(y._payload),S)}if(zr(y)||Or(y))return T!==null?null:h(w,g,y,S,null);Mo(w,y)}return null}function v(w,g,y,S,T){if(typeof S=="string"&&S!==""||typeof S=="number")return w=w.get(y)||null,l(g,w,""+S,T);if(typeof S=="object"&&S!==null){switch(S.$$typeof){case Yi:return w=w.get(S.key===null?y:S.key)||null,c(g,w,S,T);case Hn:return w=w.get(S.key===null?y:S.key)||null,d(g,w,S,T);case Bt:var C=S._init;return v(w,g,y,C(S._payload),T)}if(zr(S)||Or(S))return w=w.get(y)||null,h(g,w,S,T,null);Mo(g,S)}return null}function x(w,g,y,S){for(var T=null,C=null,k=g,$=g=0,D=null;k!==null&&$<y.length;$++){k.index>$?(D=k,k=null):D=k.sibling;var E=f(w,k,y[$],S);if(E===null){k===null&&(k=D);break}t&&k&&E.alternate===null&&e(w,k),g=o(E,g,$),C===null?T=E:C.sibling=E,C=E,k=D}if($===y.length)return n(w,k),q&&xn(w,$),T;if(k===null){for(;$<y.length;$++)k=p(w,y[$],S),k!==null&&(g=o(k,g,$),C===null?T=k:C.sibling=k,C=k);return q&&xn(w,$),T}for(k=r(w,k);$<y.length;$++)D=v(k,w,$,y[$],S),D!==null&&(t&&D.alternate!==null&&k.delete(D.key===null?$:D.key),g=o(D,g,$),C===null?T=D:C.sibling=D,C=D);return t&&k.forEach(function(H){return e(w,H)}),q&&xn(w,$),T}function b(w,g,y,S){var T=Or(y);if(typeof T!="function")throw Error(j(150));if(y=T.call(y),y==null)throw Error(j(151));for(var C=T=null,k=g,$=g=0,D=null,E=y.next();k!==null&&!E.done;$++,E=y.next()){k.index>$?(D=k,k=null):D=k.sibling;var H=f(w,k,E.value,S);if(H===null){k===null&&(k=D);break}t&&k&&H.alternate===null&&e(w,k),g=o(H,g,$),C===null?T=H:C.sibling=H,C=H,k=D}if(E.done)return n(w,k),q&&xn(w,$),T;if(k===null){for(;!E.done;$++,E=y.next())E=p(w,E.value,S),E!==null&&(g=o(E,g,$),C===null?T=E:C.sibling=E,C=E);return q&&xn(w,$),T}for(k=r(w,k);!E.done;$++,E=y.next())E=v(k,w,$,E.value,S),E!==null&&(t&&E.alternate!==null&&k.delete(E.key===null?$:E.key),g=o(E,g,$),C===null?T=E:C.sibling=E,C=E);return t&&k.forEach(function(K){return e(w,K)}),q&&xn(w,$),T}function M(w,g,y,S){if(typeof y=="object"&&y!==null&&y.type===Wn&&y.key===null&&(y=y.props.children),typeof y=="object"&&y!==null){switch(y.$$typeof){case Yi:e:{for(var T=y.key,C=g;C!==null;){if(C.key===T){if(T=y.type,T===Wn){if(C.tag===7){n(w,C.sibling),g=i(C,y.props.children),g.return=w,w=g;break e}}else if(C.elementType===T||typeof T=="object"&&T!==null&&T.$$typeof===Bt&&Ah(T)===C.type){n(w,C.sibling),g=i(C,y.props),g.ref=li(w,C,y),g.return=w,w=g;break e}n(w,C);break}else e(w,C);C=C.sibling}y.type===Wn?(g=Cn(y.props.children,w.mode,S,y.key),g.return=w,w=g):(S=Jo(y.type,y.key,y.props,null,w.mode,S),S.ref=li(w,g,y),S.return=w,w=S)}return s(w);case Hn:e:{for(C=y.key;g!==null;){if(g.key===C)if(g.tag===4&&g.stateNode.containerInfo===y.containerInfo&&g.stateNode.implementation===y.implementation){n(w,g.sibling),g=i(g,y.children||[]),g.return=w,w=g;break e}else{n(w,g);break}else e(w,g);g=g.sibling}g=sc(y,w.mode,S),g.return=w,w=g}return s(w);case Bt:return C=y._init,M(w,g,C(y._payload),S)}if(zr(y))return x(w,g,y,S);if(Or(y))return b(w,g,y,S);Mo(w,y)}return typeof y=="string"&&y!==""||typeof y=="number"?(y=""+y,g!==null&&g.tag===6?(n(w,g.sibling),g=i(g,y),g.return=w,w=g):(n(w,g),g=oc(y,w.mode,S),g.return=w,w=g),s(w)):n(w,g)}return M}var sr=Rh(!0),Ih=Rh(!1),jo=Yt(null),Eo=null,ar=null,gl=null;function ml(){gl=ar=Eo=null}function xl(t){var e=jo.current;X(jo),t._currentValue=e}function yl(t,e,n){for(;t!==null;){var r=t.alternate;if((t.childLanes&e)!==e?(t.childLanes|=e,r!==null&&(r.childLanes|=e)):r!==null&&(r.childLanes&e)!==e&&(r.childLanes|=e),t===n)break;t=t.return}}function lr(t,e){Eo=t,gl=ar=null,t=t.dependencies,t!==null&&t.firstContext!==null&&(t.lanes&e&&(Ee=!0),t.firstContext=null)}function Ye(t){var e=t._currentValue;if(gl!==t)if(t={context:t,memoizedValue:e,next:null},ar===null){if(Eo===null)throw Error(j(308));ar=t,Eo.dependencies={lanes:0,firstContext:t}}else ar=ar.next=t;return e}var yn=null;function wl(t){yn===null?yn=[t]:yn.push(t)}function Oh(t,e,n,r){var i=e.interleaved;return i===null?(n.next=n,wl(e)):(n.next=i.next,i.next=n),e.interleaved=n,Tt(t,r)}function Tt(t,e){t.lanes|=e;var n=t.alternate;for(n!==null&&(n.lanes|=e),n=t,t=t.return;t!==null;)t.childLanes|=e,n=t.alternate,n!==null&&(n.childLanes|=e),n=t,t=t.return;return n.tag===3?n.stateNode:null}var Xt=!1;function vl(t){t.updateQueue={baseState:t.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,interleaved:null,lanes:0},effects:null}}function Lh(t,e){t=t.updateQueue,e.updateQueue===t&&(e.updateQueue={baseState:t.baseState,firstBaseUpdate:t.firstBaseUpdate,lastBaseUpdate:t.lastBaseUpdate,shared:t.shared,effects:t.effects})}function Mt(t,e){return{eventTime:t,lane:e,tag:0,payload:null,callback:null,next:null}}function Jt(t,e,n){var r=t.updateQueue;if(r===null)return null;if(r=r.shared,F&2){var i=r.pending;return i===null?e.next=e:(e.next=i.next,i.next=e),r.pending=e,Tt(t,n)}return i=r.interleaved,i===null?(e.next=e,wl(r)):(e.next=i.next,i.next=e),r.interleaved=e,Tt(t,n)}function Do(t,e,n){if(e=e.updateQueue,e!==null&&(e=e.shared,(n&4194240)!==0)){var r=e.lanes;r&=t.pendingLanes,n|=r,e.lanes=n,Ia(t,n)}}function zh(t,e){var n=t.updateQueue,r=t.alternate;if(r!==null&&(r=r.updateQueue,n===r)){var i=null,o=null;if(n=n.firstBaseUpdate,n!==null){do{var s={eventTime:n.eventTime,lane:n.lane,tag:n.tag,payload:n.payload,callback:n.callback,next:null};o===null?i=o=s:o=o.next=s,n=n.next}while(n!==null);o===null?i=o=e:o=o.next=e}else i=o=e;n={baseState:r.baseState,firstBaseUpdate:i,lastBaseUpdate:o,shared:r.shared,effects:r.effects},t.updateQueue=n;return}t=n.lastBaseUpdate,t===null?n.firstBaseUpdate=e:t.next=e,n.lastBaseUpdate=e}function Po(t,e,n,r){var i=t.updateQueue;Xt=!1;var o=i.firstBaseUpdate,s=i.lastBaseUpdate,l=i.shared.pending;if(l!==null){i.shared.pending=null;var c=l,d=c.next;c.next=null,s===null?o=d:s.next=d,s=c;var h=t.alternate;h!==null&&(h=h.updateQueue,l=h.lastBaseUpdate,l!==s&&(l===null?h.firstBaseUpdate=d:l.next=d,h.lastBaseUpdate=c))}if(o!==null){var p=i.baseState;s=0,h=d=c=null,l=o;do{var f=l.lane,v=l.eventTime;if((r&f)===f){h!==null&&(h=h.next={eventTime:v,lane:0,tag:l.tag,payload:l.payload,callback:l.callback,next:null});e:{var x=t,b=l;switch(f=e,v=n,b.tag){case 1:if(x=b.payload,typeof x=="function"){p=x.call(v,p,f);break e}p=x;break e;case 3:x.flags=x.flags&-65537|128;case 0:if(x=b.payload,f=typeof x=="function"?x.call(v,p,f):x,f==null)break e;p=Z({},p,f);break e;case 2:Xt=!0}}l.callback!==null&&l.lane!==0&&(t.flags|=64,f=i.effects,f===null?i.effects=[l]:f.push(l))}else v={eventTime:v,lane:f,tag:l.tag,payload:l.payload,callback:l.callback,next:null},h===null?(d=h=v,c=p):h=h.next=v,s|=f;if(l=l.next,l===null){if(l=i.shared.pending,l===null)break;f=l,l=f.next,f.next=null,i.lastBaseUpdate=f,i.shared.pending=null}}while(!0);if(h===null&&(c=p),i.baseState=c,i.firstBaseUpdate=d,i.lastBaseUpdate=h,e=i.shared.interleaved,e!==null){i=e;do s|=i.lane,i=i.next;while(i!==e)}else o===null&&(i.shared.lanes=0);bn|=s,t.lanes=s,t.memoizedState=p}}function Bh(t,e,n){if(t=e.effects,e.effects=null,t!==null)for(e=0;e<t.length;e++){var r=t[e],i=r.callback;if(i!==null){if(r.callback=null,r=n,typeof i!="function")throw Error(j(191,i));i.call(r)}}}var ci={},mt=Yt(ci),di=Yt(ci),ui=Yt(ci);function wn(t){if(t===ci)throw Error(j(174));return t}function bl(t,e){switch(Y(ui,e),Y(di,t),Y(mt,ci),t=e.nodeType,t){case 9:case 11:e=(e=e.documentElement)?e.namespaceURI:Sa(null,"");break;default:t=t===8?e.parentNode:e,e=t.namespaceURI||null,t=t.tagName,e=Sa(e,t)}X(mt),Y(mt,e)}function cr(){X(mt),X(di),X(ui)}function Nh(t){wn(ui.current);var e=wn(mt.current),n=Sa(e,t.type);e!==n&&(Y(di,t),Y(mt,n))}function Sl(t){di.current===t&&(X(mt),X(di))}var ee=Yt(0);function Ao(t){for(var e=t;e!==null;){if(e.tag===13){var n=e.memoizedState;if(n!==null&&(n=n.dehydrated,n===null||n.data==="$?"||n.data==="$!"))return e}else if(e.tag===19&&e.memoizedProps.revealOrder!==void 0){if(e.flags&128)return e}else if(e.child!==null){e.child.return=e,e=e.child;continue}if(e===t)break;for(;e.sibling===null;){if(e.return===null||e.return===t)return null;e=e.return}e.sibling.return=e.return,e=e.sibling}return null}var kl=[];function $l(){for(var t=0;t<kl.length;t++)kl[t]._workInProgressVersionPrimary=null;kl.length=0}var Ro=bt.ReactCurrentDispatcher,Cl=bt.ReactCurrentBatchConfig,vn=0,te=null,le=null,pe=null,Io=!1,hi=!1,pi=0,W0=0;function we(){throw Error(j(321))}function Tl(t,e){if(e===null)return!1;for(var n=0;n<e.length&&n<t.length;n++)if(!tt(t[n],e[n]))return!1;return!0}function Ml(t,e,n,r,i,o){if(vn=o,te=e,e.memoizedState=null,e.updateQueue=null,e.lanes=0,Ro.current=t===null||t.memoizedState===null?Y0:K0,t=n(r,i),hi){o=0;do{if(hi=!1,pi=0,25<=o)throw Error(j(301));o+=1,pe=le=null,e.updateQueue=null,Ro.current=Q0,t=n(r,i)}while(hi)}if(Ro.current=zo,e=le!==null&&le.next!==null,vn=0,pe=le=te=null,Io=!1,e)throw Error(j(300));return t}function jl(){var t=pi!==0;return pi=0,t}function xt(){var t={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return pe===null?te.memoizedState=pe=t:pe=pe.next=t,pe}function Ke(){if(le===null){var t=te.alternate;t=t!==null?t.memoizedState:null}else t=le.next;var e=pe===null?te.memoizedState:pe.next;if(e!==null)pe=e,le=t;else{if(t===null)throw Error(j(310));le=t,t={memoizedState:le.memoizedState,baseState:le.baseState,baseQueue:le.baseQueue,queue:le.queue,next:null},pe===null?te.memoizedState=pe=t:pe=pe.next=t}return pe}function fi(t,e){return typeof e=="function"?e(t):e}function El(t){var e=Ke(),n=e.queue;if(n===null)throw Error(j(311));n.lastRenderedReducer=t;var r=le,i=r.baseQueue,o=n.pending;if(o!==null){if(i!==null){var s=i.next;i.next=o.next,o.next=s}r.baseQueue=i=o,n.pending=null}if(i!==null){o=i.next,r=r.baseState;var l=s=null,c=null,d=o;do{var h=d.lane;if((vn&h)===h)c!==null&&(c=c.next={lane:0,action:d.action,hasEagerState:d.hasEagerState,eagerState:d.eagerState,next:null}),r=d.hasEagerState?d.eagerState:t(r,d.action);else{var p={lane:h,action:d.action,hasEagerState:d.hasEagerState,eagerState:d.eagerState,next:null};c===null?(l=c=p,s=r):c=c.next=p,te.lanes|=h,bn|=h}d=d.next}while(d!==null&&d!==o);c===null?s=r:c.next=l,tt(r,e.memoizedState)||(Ee=!0),e.memoizedState=r,e.baseState=s,e.baseQueue=c,n.lastRenderedState=r}if(t=n.interleaved,t!==null){i=t;do o=i.lane,te.lanes|=o,bn|=o,i=i.next;while(i!==t)}else i===null&&(n.lanes=0);return[e.memoizedState,n.dispatch]}function Dl(t){var e=Ke(),n=e.queue;if(n===null)throw Error(j(311));n.lastRenderedReducer=t;var r=n.dispatch,i=n.pending,o=e.memoizedState;if(i!==null){n.pending=null;var s=i=i.next;do o=t(o,s.action),s=s.next;while(s!==i);tt(o,e.memoizedState)||(Ee=!0),e.memoizedState=o,e.baseQueue===null&&(e.baseState=o),n.lastRenderedState=o}return[o,r]}function Fh(){}function _h(t,e){var n=te,r=Ke(),i=e(),o=!tt(r.memoizedState,i);if(o&&(r.memoizedState=i,Ee=!0),r=r.queue,Pl(Uh.bind(null,n,r,t),[t]),r.getSnapshot!==e||o||pe!==null&&pe.memoizedState.tag&1){if(n.flags|=2048,gi(9,Wh.bind(null,n,r,i,e),void 0,null),fe===null)throw Error(j(349));vn&30||Hh(n,e,i)}return i}function Hh(t,e,n){t.flags|=16384,t={getSnapshot:e,value:n},e=te.updateQueue,e===null?(e={lastEffect:null,stores:null},te.updateQueue=e,e.stores=[t]):(n=e.stores,n===null?e.stores=[t]:n.push(t))}function Wh(t,e,n,r){e.value=n,e.getSnapshot=r,Gh(e)&&Vh(t)}function Uh(t,e,n){return n(function(){Gh(e)&&Vh(t)})}function Gh(t){var e=t.getSnapshot;t=t.value;try{var n=e();return!tt(t,n)}catch{return!0}}function Vh(t){var e=Tt(t,1);e!==null&&st(e,t,1,-1)}function Yh(t){var e=xt();return typeof t=="function"&&(t=t()),e.memoizedState=e.baseState=t,t={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:fi,lastRenderedState:t},e.queue=t,t=t.dispatch=V0.bind(null,te,t),[e.memoizedState,t]}function gi(t,e,n,r){return t={tag:t,create:e,destroy:n,deps:r,next:null},e=te.updateQueue,e===null?(e={lastEffect:null,stores:null},te.updateQueue=e,e.lastEffect=t.next=t):(n=e.lastEffect,n===null?e.lastEffect=t.next=t:(r=n.next,n.next=t,t.next=r,e.lastEffect=t)),t}function Kh(){return Ke().memoizedState}function Oo(t,e,n,r){var i=xt();te.flags|=t,i.memoizedState=gi(1|e,n,void 0,r===void 0?null:r)}function Lo(t,e,n,r){var i=Ke();r=r===void 0?null:r;var o=void 0;if(le!==null){var s=le.memoizedState;if(o=s.destroy,r!==null&&Tl(r,s.deps)){i.memoizedState=gi(e,n,o,r);return}}te.flags|=t,i.memoizedState=gi(1|e,n,o,r)}function Qh(t,e){return Oo(8390656,8,t,e)}function Pl(t,e){return Lo(2048,8,t,e)}function Xh(t,e){return Lo(4,2,t,e)}function Jh(t,e){return Lo(4,4,t,e)}function qh(t,e){if(typeof e=="function")return t=t(),e(t),function(){e(null)};if(e!=null)return t=t(),e.current=t,function(){e.current=null}}function Zh(t,e,n){return n=n!=null?n.concat([t]):null,Lo(4,4,qh.bind(null,e,t),n)}function Al(){}function ep(t,e){var n=Ke();e=e===void 0?null:e;var r=n.memoizedState;return r!==null&&e!==null&&Tl(e,r[1])?r[0]:(n.memoizedState=[t,e],t)}function tp(t,e){var n=Ke();e=e===void 0?null:e;var r=n.memoizedState;return r!==null&&e!==null&&Tl(e,r[1])?r[0]:(t=t(),n.memoizedState=[t,e],t)}function np(t,e,n){return vn&21?(tt(n,e)||(n=Au(),te.lanes|=n,bn|=n,t.baseState=!0),e):(t.baseState&&(t.baseState=!1,Ee=!0),t.memoizedState=n)}function U0(t,e){var n=U;U=n!==0&&4>n?n:4,t(!0);var r=Cl.transition;Cl.transition={};try{t(!1),e()}finally{U=n,Cl.transition=r}}function rp(){return Ke().memoizedState}function G0(t,e,n){var r=tn(t);if(n={lane:r,action:n,hasEagerState:!1,eagerState:null,next:null},ip(t))op(e,n);else if(n=Oh(t,e,n,r),n!==null){var i=Te();st(n,t,r,i),sp(n,e,r)}}function V0(t,e,n){var r=tn(t),i={lane:r,action:n,hasEagerState:!1,eagerState:null,next:null};if(ip(t))op(e,i);else{var o=t.alternate;if(t.lanes===0&&(o===null||o.lanes===0)&&(o=e.lastRenderedReducer,o!==null))try{var s=e.lastRenderedState,l=o(s,n);if(i.hasEagerState=!0,i.eagerState=l,tt(l,s)){var c=e.interleaved;c===null?(i.next=i,wl(e)):(i.next=c.next,c.next=i),e.interleaved=i;return}}catch{}finally{}n=Oh(t,e,i,r),n!==null&&(i=Te(),st(n,t,r,i),sp(n,e,r))}}function ip(t){var e=t.alternate;return t===te||e!==null&&e===te}function op(t,e){hi=Io=!0;var n=t.pending;n===null?e.next=e:(e.next=n.next,n.next=e),t.pending=e}function sp(t,e,n){if(n&4194240){var r=e.lanes;r&=t.pendingLanes,n|=r,e.lanes=n,Ia(t,n)}}var zo={readContext:Ye,useCallback:we,useContext:we,useEffect:we,useImperativeHandle:we,useInsertionEffect:we,useLayoutEffect:we,useMemo:we,useReducer:we,useRef:we,useState:we,useDebugValue:we,useDeferredValue:we,useTransition:we,useMutableSource:we,useSyncExternalStore:we,useId:we,unstable_isNewReconciler:!1},Y0={readContext:Ye,useCallback:function(t,e){return xt().memoizedState=[t,e===void 0?null:e],t},useContext:Ye,useEffect:Qh,useImperativeHandle:function(t,e,n){return n=n!=null?n.concat([t]):null,Oo(4194308,4,qh.bind(null,e,t),n)},useLayoutEffect:function(t,e){return Oo(4194308,4,t,e)},useInsertionEffect:function(t,e){return Oo(4,2,t,e)},useMemo:function(t,e){var n=xt();return e=e===void 0?null:e,t=t(),n.memoizedState=[t,e],t},useReducer:function(t,e,n){var r=xt();return e=n!==void 0?n(e):e,r.memoizedState=r.baseState=e,t={pending:null,interleaved:null,lanes:0,dispatch:null,lastRenderedReducer:t,lastRenderedState:e},r.queue=t,t=t.dispatch=G0.bind(null,te,t),[r.memoizedState,t]},useRef:function(t){var e=xt();return t={current:t},e.memoizedState=t},useState:Yh,useDebugValue:Al,useDeferredValue:function(t){return xt().memoizedState=t},useTransition:function(){var t=Yh(!1),e=t[0];return t=U0.bind(null,t[1]),xt().memoizedState=t,[e,t]},useMutableSource:function(){},useSyncExternalStore:function(t,e,n){var r=te,i=xt();if(q){if(n===void 0)throw Error(j(407));n=n()}else{if(n=e(),fe===null)throw Error(j(349));vn&30||Hh(r,e,n)}i.memoizedState=n;var o={value:n,getSnapshot:e};return i.queue=o,Qh(Uh.bind(null,r,o,t),[t]),r.flags|=2048,gi(9,Wh.bind(null,r,o,n,e),void 0,null),n},useId:function(){var t=xt(),e=fe.identifierPrefix;if(q){var n=Ct,r=$t;n=(r&~(1<<32-et(r)-1)).toString(32)+n,e=":"+e+"R"+n,n=pi++,0<n&&(e+="H"+n.toString(32)),e+=":"}else n=W0++,e=":"+e+"r"+n.toString(32)+":";return t.memoizedState=e},unstable_isNewReconciler:!1},K0={readContext:Ye,useCallback:ep,useContext:Ye,useEffect:Pl,useImperativeHandle:Zh,useInsertionEffect:Xh,useLayoutEffect:Jh,useMemo:tp,useReducer:El,useRef:Kh,useState:function(){return El(fi)},useDebugValue:Al,useDeferredValue:function(t){var e=Ke();return np(e,le.memoizedState,t)},useTransition:function(){var t=El(fi)[0],e=Ke().memoizedState;return[t,e]},useMutableSource:Fh,useSyncExternalStore:_h,useId:rp,unstable_isNewReconciler:!1},Q0={readContext:Ye,useCallback:ep,useContext:Ye,useEffect:Pl,useImperativeHandle:Zh,useInsertionEffect:Xh,useLayoutEffect:Jh,useMemo:tp,useReducer:Dl,useRef:Kh,useState:function(){return Dl(fi)},useDebugValue:Al,useDeferredValue:function(t){var e=Ke();return le===null?e.memoizedState=t:np(e,le.memoizedState,t)},useTransition:function(){var t=Dl(fi)[0],e=Ke().memoizedState;return[t,e]},useMutableSource:Fh,useSyncExternalStore:_h,useId:rp,unstable_isNewReconciler:!1};function rt(t,e){if(t&&t.defaultProps){e=Z({},e),t=t.defaultProps;for(var n in t)e[n]===void 0&&(e[n]=t[n]);return e}return e}function Rl(t,e,n,r){e=t.memoizedState,n=n(r,e),n=n==null?e:Z({},e,n),t.memoizedState=n,t.lanes===0&&(t.updateQueue.baseState=n)}var Bo={isMounted:function(t){return(t=t._reactInternals)?pn(t)===t:!1},enqueueSetState:function(t,e,n){t=t._reactInternals;var r=Te(),i=tn(t),o=Mt(r,i);o.payload=e,n!=null&&(o.callback=n),e=Jt(t,o,i),e!==null&&(st(e,t,i,r),Do(e,t,i))},enqueueReplaceState:function(t,e,n){t=t._reactInternals;var r=Te(),i=tn(t),o=Mt(r,i);o.tag=1,o.payload=e,n!=null&&(o.callback=n),e=Jt(t,o,i),e!==null&&(st(e,t,i,r),Do(e,t,i))},enqueueForceUpdate:function(t,e){t=t._reactInternals;var n=Te(),r=tn(t),i=Mt(n,r);i.tag=2,e!=null&&(i.callback=e),e=Jt(t,i,r),e!==null&&(st(e,t,r,n),Do(e,t,r))}};function ap(t,e,n,r,i,o,s){return t=t.stateNode,typeof t.shouldComponentUpdate=="function"?t.shouldComponentUpdate(r,o,s):e.prototype&&e.prototype.isPureReactComponent?!ti(n,r)||!ti(i,o):!0}function lp(t,e,n){var r=!1,i=Kt,o=e.contextType;return typeof o=="object"&&o!==null?o=Ye(o):(i=je(e)?gn:ye.current,r=e.contextTypes,o=(r=r!=null)?nr(t,i):Kt),e=new e(n,o),t.memoizedState=e.state!==null&&e.state!==void 0?e.state:null,e.updater=Bo,t.stateNode=e,e._reactInternals=t,r&&(t=t.stateNode,t.__reactInternalMemoizedUnmaskedChildContext=i,t.__reactInternalMemoizedMaskedChildContext=o),e}function cp(t,e,n,r){t=e.state,typeof e.componentWillReceiveProps=="function"&&e.componentWillReceiveProps(n,r),typeof e.UNSAFE_componentWillReceiveProps=="function"&&e.UNSAFE_componentWillReceiveProps(n,r),e.state!==t&&Bo.enqueueReplaceState(e,e.state,null)}function Il(t,e,n,r){var i=t.stateNode;i.props=n,i.state=t.memoizedState,i.refs={},vl(t);var o=e.contextType;typeof o=="object"&&o!==null?i.context=Ye(o):(o=je(e)?gn:ye.current,i.context=nr(t,o)),i.state=t.memoizedState,o=e.getDerivedStateFromProps,typeof o=="function"&&(Rl(t,e,o,n),i.state=t.memoizedState),typeof e.getDerivedStateFromProps=="function"||typeof i.getSnapshotBeforeUpdate=="function"||typeof i.UNSAFE_componentWillMount!="function"&&typeof i.componentWillMount!="function"||(e=i.state,typeof i.componentWillMount=="function"&&i.componentWillMount(),typeof i.UNSAFE_componentWillMount=="function"&&i.UNSAFE_componentWillMount(),e!==i.state&&Bo.enqueueReplaceState(i,i.state,null),Po(t,n,i,r),i.state=t.memoizedState),typeof i.componentDidMount=="function"&&(t.flags|=4194308)}function dr(t,e){try{var n="",r=e;do n+=kx(r),r=r.return;while(r);var i=n}catch(o){i=`
Error generating stack: `+o.message+`
`+o.stack}return{value:t,source:e,stack:i,digest:null}}function Ol(t,e,n){return{value:t,source:null,stack:n??null,digest:e??null}}function Ll(t,e){try{console.error(e.value)}catch(n){setTimeout(function(){throw n})}}var X0=typeof WeakMap=="function"?WeakMap:Map;function dp(t,e,n){n=Mt(-1,n),n.tag=3,n.payload={element:null};var r=e.value;return n.callback=function(){Go||(Go=!0,Jl=r),Ll(t,e)},n}function up(t,e,n){n=Mt(-1,n),n.tag=3;var r=t.type.getDerivedStateFromError;if(typeof r=="function"){var i=e.value;n.payload=function(){return r(i)},n.callback=function(){Ll(t,e)}}var o=t.stateNode;return o!==null&&typeof o.componentDidCatch=="function"&&(n.callback=function(){Ll(t,e),typeof r!="function"&&(Zt===null?Zt=new Set([this]):Zt.add(this));var s=e.stack;this.componentDidCatch(e.value,{componentStack:s!==null?s:""})}),n}function hp(t,e,n){var r=t.pingCache;if(r===null){r=t.pingCache=new X0;var i=new Set;r.set(e,i)}else i=r.get(e),i===void 0&&(i=new Set,r.set(e,i));i.has(n)||(i.add(n),t=dy.bind(null,t,e,n),e.then(t,t))}function pp(t){do{var e;if((e=t.tag===13)&&(e=t.memoizedState,e=e!==null?e.dehydrated!==null:!0),e)return t;t=t.return}while(t!==null);return null}function fp(t,e,n,r,i){return t.mode&1?(t.flags|=65536,t.lanes=i,t):(t===e?t.flags|=65536:(t.flags|=128,n.flags|=131072,n.flags&=-52805,n.tag===1&&(n.alternate===null?n.tag=17:(e=Mt(-1,1),e.tag=2,Jt(n,e,1))),n.lanes|=1),t)}var J0=bt.ReactCurrentOwner,Ee=!1;function Ce(t,e,n,r){e.child=t===null?Ih(e,null,n,r):sr(e,t.child,n,r)}function gp(t,e,n,r,i){n=n.render;var o=e.ref;return lr(e,i),r=Ml(t,e,n,r,o,i),n=jl(),t!==null&&!Ee?(e.updateQueue=t.updateQueue,e.flags&=-2053,t.lanes&=~i,jt(t,e,i)):(q&&n&&dl(e),e.flags|=1,Ce(t,e,r,i),e.child)}function mp(t,e,n,r,i){if(t===null){var o=n.type;return typeof o=="function"&&!ic(o)&&o.defaultProps===void 0&&n.compare===null&&n.defaultProps===void 0?(e.tag=15,e.type=o,xp(t,e,o,r,i)):(t=Jo(n.type,null,r,e,e.mode,i),t.ref=e.ref,t.return=e,e.child=t)}if(o=t.child,!(t.lanes&i)){var s=o.memoizedProps;if(n=n.compare,n=n!==null?n:ti,n(s,r)&&t.ref===e.ref)return jt(t,e,i)}return e.flags|=1,t=rn(o,r),t.ref=e.ref,t.return=e,e.child=t}function xp(t,e,n,r,i){if(t!==null){var o=t.memoizedProps;if(ti(o,r)&&t.ref===e.ref)if(Ee=!1,e.pendingProps=r=o,(t.lanes&i)!==0)t.flags&131072&&(Ee=!0);else return e.lanes=t.lanes,jt(t,e,i)}return zl(t,e,n,r,i)}function yp(t,e,n){var r=e.pendingProps,i=r.children,o=t!==null?t.memoizedState:null;if(r.mode==="hidden")if(!(e.mode&1))e.memoizedState={baseLanes:0,cachePool:null,transitions:null},Y(hr,_e),_e|=n;else{if(!(n&1073741824))return t=o!==null?o.baseLanes|n:n,e.lanes=e.childLanes=1073741824,e.memoizedState={baseLanes:t,cachePool:null,transitions:null},e.updateQueue=null,Y(hr,_e),_e|=t,null;e.memoizedState={baseLanes:0,cachePool:null,transitions:null},r=o!==null?o.baseLanes:n,Y(hr,_e),_e|=r}else o!==null?(r=o.baseLanes|n,e.memoizedState=null):r=n,Y(hr,_e),_e|=r;return Ce(t,e,i,n),e.child}function wp(t,e){var n=e.ref;(t===null&&n!==null||t!==null&&t.ref!==n)&&(e.flags|=512,e.flags|=2097152)}function zl(t,e,n,r,i){var o=je(n)?gn:ye.current;return o=nr(e,o),lr(e,i),n=Ml(t,e,n,r,o,i),r=jl(),t!==null&&!Ee?(e.updateQueue=t.updateQueue,e.flags&=-2053,t.lanes&=~i,jt(t,e,i)):(q&&r&&dl(e),e.flags|=1,Ce(t,e,n,i),e.child)}function vp(t,e,n,r,i){if(je(n)){var o=!0;So(e)}else o=!1;if(lr(e,i),e.stateNode===null)Fo(t,e),lp(e,n,r),Il(e,n,r,i),r=!0;else if(t===null){var s=e.stateNode,l=e.memoizedProps;s.props=l;var c=s.context,d=n.contextType;typeof d=="object"&&d!==null?d=Ye(d):(d=je(n)?gn:ye.current,d=nr(e,d));var h=n.getDerivedStateFromProps,p=typeof h=="function"||typeof s.getSnapshotBeforeUpdate=="function";p||typeof s.UNSAFE_componentWillReceiveProps!="function"&&typeof s.componentWillReceiveProps!="function"||(l!==r||c!==d)&&cp(e,s,r,d),Xt=!1;var f=e.memoizedState;s.state=f,Po(e,r,s,i),c=e.memoizedState,l!==r||f!==c||Me.current||Xt?(typeof h=="function"&&(Rl(e,n,h,r),c=e.memoizedState),(l=Xt||ap(e,n,l,r,f,c,d))?(p||typeof s.UNSAFE_componentWillMount!="function"&&typeof s.componentWillMount!="function"||(typeof s.componentWillMount=="function"&&s.componentWillMount(),typeof s.UNSAFE_componentWillMount=="function"&&s.UNSAFE_componentWillMount()),typeof s.componentDidMount=="function"&&(e.flags|=4194308)):(typeof s.componentDidMount=="function"&&(e.flags|=4194308),e.memoizedProps=r,e.memoizedState=c),s.props=r,s.state=c,s.context=d,r=l):(typeof s.componentDidMount=="function"&&(e.flags|=4194308),r=!1)}else{s=e.stateNode,Lh(t,e),l=e.memoizedProps,d=e.type===e.elementType?l:rt(e.type,l),s.props=d,p=e.pendingProps,f=s.context,c=n.contextType,typeof c=="object"&&c!==null?c=Ye(c):(c=je(n)?gn:ye.current,c=nr(e,c));var v=n.getDerivedStateFromProps;(h=typeof v=="function"||typeof s.getSnapshotBeforeUpdate=="function")||typeof s.UNSAFE_componentWillReceiveProps!="function"&&typeof s.componentWillReceiveProps!="function"||(l!==p||f!==c)&&cp(e,s,r,c),Xt=!1,f=e.memoizedState,s.state=f,Po(e,r,s,i);var x=e.memoizedState;l!==p||f!==x||Me.current||Xt?(typeof v=="function"&&(Rl(e,n,v,r),x=e.memoizedState),(d=Xt||ap(e,n,d,r,f,x,c)||!1)?(h||typeof s.UNSAFE_componentWillUpdate!="function"&&typeof s.componentWillUpdate!="function"||(typeof s.componentWillUpdate=="function"&&s.componentWillUpdate(r,x,c),typeof s.UNSAFE_componentWillUpdate=="function"&&s.UNSAFE_componentWillUpdate(r,x,c)),typeof s.componentDidUpdate=="function"&&(e.flags|=4),typeof s.getSnapshotBeforeUpdate=="function"&&(e.flags|=1024)):(typeof s.componentDidUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=4),typeof s.getSnapshotBeforeUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=1024),e.memoizedProps=r,e.memoizedState=x),s.props=r,s.state=x,s.context=c,r=d):(typeof s.componentDidUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=4),typeof s.getSnapshotBeforeUpdate!="function"||l===t.memoizedProps&&f===t.memoizedState||(e.flags|=1024),r=!1)}return Bl(t,e,n,r,o,i)}function Bl(t,e,n,r,i,o){wp(t,e);var s=(e.flags&128)!==0;if(!r&&!s)return i&&Ch(e,n,!1),jt(t,e,o);r=e.stateNode,J0.current=e;var l=s&&typeof n.getDerivedStateFromError!="function"?null:r.render();return e.flags|=1,t!==null&&s?(e.child=sr(e,t.child,null,o),e.child=sr(e,null,l,o)):Ce(t,e,l,o),e.memoizedState=r.state,i&&Ch(e,n,!0),e.child}function bp(t){var e=t.stateNode;e.pendingContext?kh(t,e.pendingContext,e.pendingContext!==e.context):e.context&&kh(t,e.context,!1),bl(t,e.containerInfo)}function Sp(t,e,n,r,i){return or(),fl(i),e.flags|=256,Ce(t,e,n,r),e.child}var Nl={dehydrated:null,treeContext:null,retryLane:0};function Fl(t){return{baseLanes:t,cachePool:null,transitions:null}}function kp(t,e,n){var r=e.pendingProps,i=ee.current,o=!1,s=(e.flags&128)!==0,l;if((l=s)||(l=t!==null&&t.memoizedState===null?!1:(i&2)!==0),l?(o=!0,e.flags&=-129):(t===null||t.memoizedState!==null)&&(i|=1),Y(ee,i&1),t===null)return pl(e),t=e.memoizedState,t!==null&&(t=t.dehydrated,t!==null)?(e.mode&1?t.data==="$!"?e.lanes=8:e.lanes=1073741824:e.lanes=1,null):(s=r.children,t=r.fallback,o?(r=e.mode,o=e.child,s={mode:"hidden",children:s},!(r&1)&&o!==null?(o.childLanes=0,o.pendingProps=s):o=qo(s,r,0,null),t=Cn(t,r,n,null),o.return=e,t.return=e,o.sibling=t,e.child=o,e.child.memoizedState=Fl(n),e.memoizedState=Nl,t):_l(e,s));if(i=t.memoizedState,i!==null&&(l=i.dehydrated,l!==null))return q0(t,e,s,r,l,i,n);if(o){o=r.fallback,s=e.mode,i=t.child,l=i.sibling;var c={mode:"hidden",children:r.children};return!(s&1)&&e.child!==i?(r=e.child,r.childLanes=0,r.pendingProps=c,e.deletions=null):(r=rn(i,c),r.subtreeFlags=i.subtreeFlags&14680064),l!==null?o=rn(l,o):(o=Cn(o,s,n,null),o.flags|=2),o.return=e,r.return=e,r.sibling=o,e.child=r,r=o,o=e.child,s=t.child.memoizedState,s=s===null?Fl(n):{baseLanes:s.baseLanes|n,cachePool:null,transitions:s.transitions},o.memoizedState=s,o.childLanes=t.childLanes&~n,e.memoizedState=Nl,r}return o=t.child,t=o.sibling,r=rn(o,{mode:"visible",children:r.children}),!(e.mode&1)&&(r.lanes=n),r.return=e,r.sibling=null,t!==null&&(n=e.deletions,n===null?(e.deletions=[t],e.flags|=16):n.push(t)),e.child=r,e.memoizedState=null,r}function _l(t,e){return e=qo({mode:"visible",children:e},t.mode,0,null),e.return=t,t.child=e}function No(t,e,n,r){return r!==null&&fl(r),sr(e,t.child,null,n),t=_l(e,e.pendingProps.children),t.flags|=2,e.memoizedState=null,t}function q0(t,e,n,r,i,o,s){if(n)return e.flags&256?(e.flags&=-257,r=Ol(Error(j(422))),No(t,e,s,r)):e.memoizedState!==null?(e.child=t.child,e.flags|=128,null):(o=r.fallback,i=e.mode,r=qo({mode:"visible",children:r.children},i,0,null),o=Cn(o,i,s,null),o.flags|=2,r.return=e,o.return=e,r.sibling=o,e.child=r,e.mode&1&&sr(e,t.child,null,s),e.child.memoizedState=Fl(s),e.memoizedState=Nl,o);if(!(e.mode&1))return No(t,e,s,null);if(i.data==="$!"){if(r=i.nextSibling&&i.nextSibling.dataset,r)var l=r.dgst;return r=l,o=Error(j(419)),r=Ol(o,r,void 0),No(t,e,s,r)}if(l=(s&t.childLanes)!==0,Ee||l){if(r=fe,r!==null){switch(s&-s){case 4:i=2;break;case 16:i=8;break;case 64:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:case 67108864:i=32;break;case 536870912:i=268435456;break;default:i=0}i=i&(r.suspendedLanes|s)?0:i,i!==0&&i!==o.retryLane&&(o.retryLane=i,Tt(t,i),st(r,t,i,-1))}return rc(),r=Ol(Error(j(421))),No(t,e,s,r)}return i.data==="$?"?(e.flags|=128,e.child=t.child,e=uy.bind(null,t),i._reactRetry=e,null):(t=o.treeContext,Fe=Vt(i.nextSibling),Ne=e,q=!0,nt=null,t!==null&&(Ge[Ve++]=$t,Ge[Ve++]=Ct,Ge[Ve++]=mn,$t=t.id,Ct=t.overflow,mn=e),e=_l(e,r.children),e.flags|=4096,e)}function $p(t,e,n){t.lanes|=e;var r=t.alternate;r!==null&&(r.lanes|=e),yl(t.return,e,n)}function Hl(t,e,n,r,i){var o=t.memoizedState;o===null?t.memoizedState={isBackwards:e,rendering:null,renderingStartTime:0,last:r,tail:n,tailMode:i}:(o.isBackwards=e,o.rendering=null,o.renderingStartTime=0,o.last=r,o.tail=n,o.tailMode=i)}function Cp(t,e,n){var r=e.pendingProps,i=r.revealOrder,o=r.tail;if(Ce(t,e,r.children,n),r=ee.current,r&2)r=r&1|2,e.flags|=128;else{if(t!==null&&t.flags&128)e:for(t=e.child;t!==null;){if(t.tag===13)t.memoizedState!==null&&$p(t,n,e);else if(t.tag===19)$p(t,n,e);else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break e;for(;t.sibling===null;){if(t.return===null||t.return===e)break e;t=t.return}t.sibling.return=t.return,t=t.sibling}r&=1}if(Y(ee,r),!(e.mode&1))e.memoizedState=null;else switch(i){case"forwards":for(n=e.child,i=null;n!==null;)t=n.alternate,t!==null&&Ao(t)===null&&(i=n),n=n.sibling;n=i,n===null?(i=e.child,e.child=null):(i=n.sibling,n.sibling=null),Hl(e,!1,i,n,o);break;case"backwards":for(n=null,i=e.child,e.child=null;i!==null;){if(t=i.alternate,t!==null&&Ao(t)===null){e.child=i;break}t=i.sibling,i.sibling=n,n=i,i=t}Hl(e,!0,n,null,o);break;case"together":Hl(e,!1,null,null,void 0);break;default:e.memoizedState=null}return e.child}function Fo(t,e){!(e.mode&1)&&t!==null&&(t.alternate=null,e.alternate=null,e.flags|=2)}function jt(t,e,n){if(t!==null&&(e.dependencies=t.dependencies),bn|=e.lanes,!(n&e.childLanes))return null;if(t!==null&&e.child!==t.child)throw Error(j(153));if(e.child!==null){for(t=e.child,n=rn(t,t.pendingProps),e.child=n,n.return=e;t.sibling!==null;)t=t.sibling,n=n.sibling=rn(t,t.pendingProps),n.return=e;n.sibling=null}return e.child}function Z0(t,e,n){switch(e.tag){case 3:bp(e),or();break;case 5:Nh(e);break;case 1:je(e.type)&&So(e);break;case 4:bl(e,e.stateNode.containerInfo);break;case 10:var r=e.type._context,i=e.memoizedProps.value;Y(jo,r._currentValue),r._currentValue=i;break;case 13:if(r=e.memoizedState,r!==null)return r.dehydrated!==null?(Y(ee,ee.current&1),e.flags|=128,null):n&e.child.childLanes?kp(t,e,n):(Y(ee,ee.current&1),t=jt(t,e,n),t!==null?t.sibling:null);Y(ee,ee.current&1);break;case 19:if(r=(n&e.childLanes)!==0,t.flags&128){if(r)return Cp(t,e,n);e.flags|=128}if(i=e.memoizedState,i!==null&&(i.rendering=null,i.tail=null,i.lastEffect=null),Y(ee,ee.current),r)break;return null;case 22:case 23:return e.lanes=0,yp(t,e,n)}return jt(t,e,n)}var Tp,Wl,Mp,jp;Tp=function(t,e){for(var n=e.child;n!==null;){if(n.tag===5||n.tag===6)t.appendChild(n.stateNode);else if(n.tag!==4&&n.child!==null){n.child.return=n,n=n.child;continue}if(n===e)break;for(;n.sibling===null;){if(n.return===null||n.return===e)return;n=n.return}n.sibling.return=n.return,n=n.sibling}},Wl=function(){},Mp=function(t,e,n,r){var i=t.memoizedProps;if(i!==r){t=e.stateNode,wn(mt.current);var o=null;switch(n){case"input":i=ya(t,i),r=ya(t,r),o=[];break;case"select":i=Z({},i,{value:void 0}),r=Z({},r,{value:void 0}),o=[];break;case"textarea":i=ba(t,i),r=ba(t,r),o=[];break;default:typeof i.onClick!="function"&&typeof r.onClick=="function"&&(t.onclick=wo)}ka(n,r);var s;n=null;for(d in i)if(!r.hasOwnProperty(d)&&i.hasOwnProperty(d)&&i[d]!=null)if(d==="style"){var l=i[d];for(s in l)l.hasOwnProperty(s)&&(n||(n={}),n[s]="")}else d!=="dangerouslySetInnerHTML"&&d!=="children"&&d!=="suppressContentEditableWarning"&&d!=="suppressHydrationWarning"&&d!=="autoFocus"&&(Ir.hasOwnProperty(d)?o||(o=[]):(o=o||[]).push(d,null));for(d in r){var c=r[d];if(l=i!=null?i[d]:void 0,r.hasOwnProperty(d)&&c!==l&&(c!=null||l!=null))if(d==="style")if(l){for(s in l)!l.hasOwnProperty(s)||c&&c.hasOwnProperty(s)||(n||(n={}),n[s]="");for(s in c)c.hasOwnProperty(s)&&l[s]!==c[s]&&(n||(n={}),n[s]=c[s])}else n||(o||(o=[]),o.push(d,n)),n=c;else d==="dangerouslySetInnerHTML"?(c=c?c.__html:void 0,l=l?l.__html:void 0,c!=null&&l!==c&&(o=o||[]).push(d,c)):d==="children"?typeof c!="string"&&typeof c!="number"||(o=o||[]).push(d,""+c):d!=="suppressContentEditableWarning"&&d!=="suppressHydrationWarning"&&(Ir.hasOwnProperty(d)?(c!=null&&d==="onScroll"&&Q("scroll",t),o||l===c||(o=[])):(o=o||[]).push(d,c))}n&&(o=o||[]).push("style",n);var d=o;(e.updateQueue=d)&&(e.flags|=4)}},jp=function(t,e,n,r){n!==r&&(e.flags|=4)};function mi(t,e){if(!q)switch(t.tailMode){case"hidden":e=t.tail;for(var n=null;e!==null;)e.alternate!==null&&(n=e),e=e.sibling;n===null?t.tail=null:n.sibling=null;break;case"collapsed":n=t.tail;for(var r=null;n!==null;)n.alternate!==null&&(r=n),n=n.sibling;r===null?e||t.tail===null?t.tail=null:t.tail.sibling=null:r.sibling=null}}function ve(t){var e=t.alternate!==null&&t.alternate.child===t.child,n=0,r=0;if(e)for(var i=t.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags&14680064,r|=i.flags&14680064,i.return=t,i=i.sibling;else for(i=t.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags,r|=i.flags,i.return=t,i=i.sibling;return t.subtreeFlags|=r,t.childLanes=n,e}function ey(t,e,n){var r=e.pendingProps;switch(ul(e),e.tag){case 2:case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return ve(e),null;case 1:return je(e.type)&&bo(),ve(e),null;case 3:return r=e.stateNode,cr(),X(Me),X(ye),$l(),r.pendingContext&&(r.context=r.pendingContext,r.pendingContext=null),(t===null||t.child===null)&&(To(e)?e.flags|=4:t===null||t.memoizedState.isDehydrated&&!(e.flags&256)||(e.flags|=1024,nt!==null&&(ec(nt),nt=null))),Wl(t,e),ve(e),null;case 5:Sl(e);var i=wn(ui.current);if(n=e.type,t!==null&&e.stateNode!=null)Mp(t,e,n,r,i),t.ref!==e.ref&&(e.flags|=512,e.flags|=2097152);else{if(!r){if(e.stateNode===null)throw Error(j(166));return ve(e),null}if(t=wn(mt.current),To(e)){r=e.stateNode,n=e.type;var o=e.memoizedProps;switch(r[gt]=e,r[si]=o,t=(e.mode&1)!==0,n){case"dialog":Q("cancel",r),Q("close",r);break;case"iframe":case"object":case"embed":Q("load",r);break;case"video":case"audio":for(i=0;i<ri.length;i++)Q(ri[i],r);break;case"source":Q("error",r);break;case"img":case"image":case"link":Q("error",r),Q("load",r);break;case"details":Q("toggle",r);break;case"input":lu(r,o),Q("invalid",r);break;case"select":r._wrapperState={wasMultiple:!!o.multiple},Q("invalid",r);break;case"textarea":uu(r,o),Q("invalid",r)}ka(n,o),i=null;for(var s in o)if(o.hasOwnProperty(s)){var l=o[s];s==="children"?typeof l=="string"?r.textContent!==l&&(o.suppressHydrationWarning!==!0&&yo(r.textContent,l,t),i=["children",l]):typeof l=="number"&&r.textContent!==""+l&&(o.suppressHydrationWarning!==!0&&yo(r.textContent,l,t),i=["children",""+l]):Ir.hasOwnProperty(s)&&l!=null&&s==="onScroll"&&Q("scroll",r)}switch(n){case"input":Ki(r),du(r,o,!0);break;case"textarea":Ki(r),pu(r);break;case"select":case"option":break;default:typeof o.onClick=="function"&&(r.onclick=wo)}r=i,e.updateQueue=r,r!==null&&(e.flags|=4)}else{s=i.nodeType===9?i:i.ownerDocument,t==="http://www.w3.org/1999/xhtml"&&(t=fu(n)),t==="http://www.w3.org/1999/xhtml"?n==="script"?(t=s.createElement("div"),t.innerHTML="<script><\/script>",t=t.removeChild(t.firstChild)):typeof r.is=="string"?t=s.createElement(n,{is:r.is}):(t=s.createElement(n),n==="select"&&(s=t,r.multiple?s.multiple=!0:r.size&&(s.size=r.size))):t=s.createElementNS(t,n),t[gt]=e,t[si]=r,Tp(t,e,!1,!1),e.stateNode=t;e:{switch(s=$a(n,r),n){case"dialog":Q("cancel",t),Q("close",t),i=r;break;case"iframe":case"object":case"embed":Q("load",t),i=r;break;case"video":case"audio":for(i=0;i<ri.length;i++)Q(ri[i],t);i=r;break;case"source":Q("error",t),i=r;break;case"img":case"image":case"link":Q("error",t),Q("load",t),i=r;break;case"details":Q("toggle",t),i=r;break;case"input":lu(t,r),i=ya(t,r),Q("invalid",t);break;case"option":i=r;break;case"select":t._wrapperState={wasMultiple:!!r.multiple},i=Z({},r,{value:void 0}),Q("invalid",t);break;case"textarea":uu(t,r),i=ba(t,r),Q("invalid",t);break;default:i=r}ka(n,i),l=i;for(o in l)if(l.hasOwnProperty(o)){var c=l[o];o==="style"?xu(t,c):o==="dangerouslySetInnerHTML"?(c=c?c.__html:void 0,c!=null&&gu(t,c)):o==="children"?typeof c=="string"?(n!=="textarea"||c!=="")&&Br(t,c):typeof c=="number"&&Br(t,""+c):o!=="suppressContentEditableWarning"&&o!=="suppressHydrationWarning"&&o!=="autoFocus"&&(Ir.hasOwnProperty(o)?c!=null&&o==="onScroll"&&Q("scroll",t):c!=null&&aa(t,o,c,s))}switch(n){case"input":Ki(t),du(t,r,!1);break;case"textarea":Ki(t),pu(t);break;case"option":r.value!=null&&t.setAttribute("value",""+Nt(r.value));break;case"select":t.multiple=!!r.multiple,o=r.value,o!=null?Un(t,!!r.multiple,o,!1):r.defaultValue!=null&&Un(t,!!r.multiple,r.defaultValue,!0);break;default:typeof i.onClick=="function"&&(t.onclick=wo)}switch(n){case"button":case"input":case"select":case"textarea":r=!!r.autoFocus;break e;case"img":r=!0;break e;default:r=!1}}r&&(e.flags|=4)}e.ref!==null&&(e.flags|=512,e.flags|=2097152)}return ve(e),null;case 6:if(t&&e.stateNode!=null)jp(t,e,t.memoizedProps,r);else{if(typeof r!="string"&&e.stateNode===null)throw Error(j(166));if(n=wn(ui.current),wn(mt.current),To(e)){if(r=e.stateNode,n=e.memoizedProps,r[gt]=e,(o=r.nodeValue!==n)&&(t=Ne,t!==null))switch(t.tag){case 3:yo(r.nodeValue,n,(t.mode&1)!==0);break;case 5:t.memoizedProps.suppressHydrationWarning!==!0&&yo(r.nodeValue,n,(t.mode&1)!==0)}o&&(e.flags|=4)}else r=(n.nodeType===9?n:n.ownerDocument).createTextNode(r),r[gt]=e,e.stateNode=r}return ve(e),null;case 13:if(X(ee),r=e.memoizedState,t===null||t.memoizedState!==null&&t.memoizedState.dehydrated!==null){if(q&&Fe!==null&&e.mode&1&&!(e.flags&128))Ph(),or(),e.flags|=98560,o=!1;else if(o=To(e),r!==null&&r.dehydrated!==null){if(t===null){if(!o)throw Error(j(318));if(o=e.memoizedState,o=o!==null?o.dehydrated:null,!o)throw Error(j(317));o[gt]=e}else or(),!(e.flags&128)&&(e.memoizedState=null),e.flags|=4;ve(e),o=!1}else nt!==null&&(ec(nt),nt=null),o=!0;if(!o)return e.flags&65536?e:null}return e.flags&128?(e.lanes=n,e):(r=r!==null,r!==(t!==null&&t.memoizedState!==null)&&r&&(e.child.flags|=8192,e.mode&1&&(t===null||ee.current&1?ce===0&&(ce=3):rc())),e.updateQueue!==null&&(e.flags|=4),ve(e),null);case 4:return cr(),Wl(t,e),t===null&&ii(e.stateNode.containerInfo),ve(e),null;case 10:return xl(e.type._context),ve(e),null;case 17:return je(e.type)&&bo(),ve(e),null;case 19:if(X(ee),o=e.memoizedState,o===null)return ve(e),null;if(r=(e.flags&128)!==0,s=o.rendering,s===null)if(r)mi(o,!1);else{if(ce!==0||t!==null&&t.flags&128)for(t=e.child;t!==null;){if(s=Ao(t),s!==null){for(e.flags|=128,mi(o,!1),r=s.updateQueue,r!==null&&(e.updateQueue=r,e.flags|=4),e.subtreeFlags=0,r=n,n=e.child;n!==null;)o=n,t=r,o.flags&=14680066,s=o.alternate,s===null?(o.childLanes=0,o.lanes=t,o.child=null,o.subtreeFlags=0,o.memoizedProps=null,o.memoizedState=null,o.updateQueue=null,o.dependencies=null,o.stateNode=null):(o.childLanes=s.childLanes,o.lanes=s.lanes,o.child=s.child,o.subtreeFlags=0,o.deletions=null,o.memoizedProps=s.memoizedProps,o.memoizedState=s.memoizedState,o.updateQueue=s.updateQueue,o.type=s.type,t=s.dependencies,o.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext}),n=n.sibling;return Y(ee,ee.current&1|2),e.child}t=t.sibling}o.tail!==null&&oe()>pr&&(e.flags|=128,r=!0,mi(o,!1),e.lanes=4194304)}else{if(!r)if(t=Ao(s),t!==null){if(e.flags|=128,r=!0,n=t.updateQueue,n!==null&&(e.updateQueue=n,e.flags|=4),mi(o,!0),o.tail===null&&o.tailMode==="hidden"&&!s.alternate&&!q)return ve(e),null}else 2*oe()-o.renderingStartTime>pr&&n!==1073741824&&(e.flags|=128,r=!0,mi(o,!1),e.lanes=4194304);o.isBackwards?(s.sibling=e.child,e.child=s):(n=o.last,n!==null?n.sibling=s:e.child=s,o.last=s)}return o.tail!==null?(e=o.tail,o.rendering=e,o.tail=e.sibling,o.renderingStartTime=oe(),e.sibling=null,n=ee.current,Y(ee,r?n&1|2:n&1),e):(ve(e),null);case 22:case 23:return nc(),r=e.memoizedState!==null,t!==null&&t.memoizedState!==null!==r&&(e.flags|=8192),r&&e.mode&1?_e&1073741824&&(ve(e),e.subtreeFlags&6&&(e.flags|=8192)):ve(e),null;case 24:return null;case 25:return null}throw Error(j(156,e.tag))}function ty(t,e){switch(ul(e),e.tag){case 1:return je(e.type)&&bo(),t=e.flags,t&65536?(e.flags=t&-65537|128,e):null;case 3:return cr(),X(Me),X(ye),$l(),t=e.flags,t&65536&&!(t&128)?(e.flags=t&-65537|128,e):null;case 5:return Sl(e),null;case 13:if(X(ee),t=e.memoizedState,t!==null&&t.dehydrated!==null){if(e.alternate===null)throw Error(j(340));or()}return t=e.flags,t&65536?(e.flags=t&-65537|128,e):null;case 19:return X(ee),null;case 4:return cr(),null;case 10:return xl(e.type._context),null;case 22:case 23:return nc(),null;case 24:return null;default:return null}}var _o=!1,be=!1,ny=typeof WeakSet=="function"?WeakSet:Set,R=null;function ur(t,e){var n=t.ref;if(n!==null)if(typeof n=="function")try{n(null)}catch(r){re(t,e,r)}else n.current=null}function Ul(t,e,n){try{n()}catch(r){re(t,e,r)}}var Ep=!1;function ry(t,e){if(nl=so,t=ah(),Ka(t)){if("selectionStart"in t)var n={start:t.selectionStart,end:t.selectionEnd};else e:{n=(n=t.ownerDocument)&&n.defaultView||window;var r=n.getSelection&&n.getSelection();if(r&&r.rangeCount!==0){n=r.anchorNode;var i=r.anchorOffset,o=r.focusNode;r=r.focusOffset;try{n.nodeType,o.nodeType}catch{n=null;break e}var s=0,l=-1,c=-1,d=0,h=0,p=t,f=null;t:for(;;){for(var v;p!==n||i!==0&&p.nodeType!==3||(l=s+i),p!==o||r!==0&&p.nodeType!==3||(c=s+r),p.nodeType===3&&(s+=p.nodeValue.length),(v=p.firstChild)!==null;)f=p,p=v;for(;;){if(p===t)break t;if(f===n&&++d===i&&(l=s),f===o&&++h===r&&(c=s),(v=p.nextSibling)!==null)break;p=f,f=p.parentNode}p=v}n=l===-1||c===-1?null:{start:l,end:c}}else n=null}n=n||{start:0,end:0}}else n=null;for(rl={focusedElem:t,selectionRange:n},so=!1,R=e;R!==null;)if(e=R,t=e.child,(e.subtreeFlags&1028)!==0&&t!==null)t.return=e,R=t;else for(;R!==null;){e=R;try{var x=e.alternate;if(e.flags&1024)switch(e.tag){case 0:case 11:case 15:break;case 1:if(x!==null){var b=x.memoizedProps,M=x.memoizedState,w=e.stateNode,g=w.getSnapshotBeforeUpdate(e.elementType===e.type?b:rt(e.type,b),M);w.__reactInternalSnapshotBeforeUpdate=g}break;case 3:var y=e.stateNode.containerInfo;y.nodeType===1?y.textContent="":y.nodeType===9&&y.documentElement&&y.removeChild(y.documentElement);break;case 5:case 6:case 4:case 17:break;default:throw Error(j(163))}}catch(S){re(e,e.return,S)}if(t=e.sibling,t!==null){t.return=e.return,R=t;break}R=e.return}return x=Ep,Ep=!1,x}function xi(t,e,n){var r=e.updateQueue;if(r=r!==null?r.lastEffect:null,r!==null){var i=r=r.next;do{if((i.tag&t)===t){var o=i.destroy;i.destroy=void 0,o!==void 0&&Ul(e,n,o)}i=i.next}while(i!==r)}}function Ho(t,e){if(e=e.updateQueue,e=e!==null?e.lastEffect:null,e!==null){var n=e=e.next;do{if((n.tag&t)===t){var r=n.create;n.destroy=r()}n=n.next}while(n!==e)}}function Gl(t){var e=t.ref;if(e!==null){var n=t.stateNode;switch(t.tag){case 5:t=n;break;default:t=n}typeof e=="function"?e(t):e.current=t}}function Dp(t){var e=t.alternate;e!==null&&(t.alternate=null,Dp(e)),t.child=null,t.deletions=null,t.sibling=null,t.tag===5&&(e=t.stateNode,e!==null&&(delete e[gt],delete e[si],delete e[al],delete e[N0],delete e[F0])),t.stateNode=null,t.return=null,t.dependencies=null,t.memoizedProps=null,t.memoizedState=null,t.pendingProps=null,t.stateNode=null,t.updateQueue=null}function Pp(t){return t.tag===5||t.tag===3||t.tag===4}function Ap(t){e:for(;;){for(;t.sibling===null;){if(t.return===null||Pp(t.return))return null;t=t.return}for(t.sibling.return=t.return,t=t.sibling;t.tag!==5&&t.tag!==6&&t.tag!==18;){if(t.flags&2||t.child===null||t.tag===4)continue e;t.child.return=t,t=t.child}if(!(t.flags&2))return t.stateNode}}function Vl(t,e,n){var r=t.tag;if(r===5||r===6)t=t.stateNode,e?n.nodeType===8?n.parentNode.insertBefore(t,e):n.insertBefore(t,e):(n.nodeType===8?(e=n.parentNode,e.insertBefore(t,n)):(e=n,e.appendChild(t)),n=n._reactRootContainer,n!=null||e.onclick!==null||(e.onclick=wo));else if(r!==4&&(t=t.child,t!==null))for(Vl(t,e,n),t=t.sibling;t!==null;)Vl(t,e,n),t=t.sibling}function Yl(t,e,n){var r=t.tag;if(r===5||r===6)t=t.stateNode,e?n.insertBefore(t,e):n.appendChild(t);else if(r!==4&&(t=t.child,t!==null))for(Yl(t,e,n),t=t.sibling;t!==null;)Yl(t,e,n),t=t.sibling}var me=null,it=!1;function qt(t,e,n){for(n=n.child;n!==null;)Rp(t,e,n),n=n.sibling}function Rp(t,e,n){if(ft&&typeof ft.onCommitFiberUnmount=="function")try{ft.onCommitFiberUnmount(eo,n)}catch{}switch(n.tag){case 5:be||ur(n,e);case 6:var r=me,i=it;me=null,qt(t,e,n),me=r,it=i,me!==null&&(it?(t=me,n=n.stateNode,t.nodeType===8?t.parentNode.removeChild(n):t.removeChild(n)):me.removeChild(n.stateNode));break;case 18:me!==null&&(it?(t=me,n=n.stateNode,t.nodeType===8?sl(t.parentNode,n):t.nodeType===1&&sl(t,n),Qr(t)):sl(me,n.stateNode));break;case 4:r=me,i=it,me=n.stateNode.containerInfo,it=!0,qt(t,e,n),me=r,it=i;break;case 0:case 11:case 14:case 15:if(!be&&(r=n.updateQueue,r!==null&&(r=r.lastEffect,r!==null))){i=r=r.next;do{var o=i,s=o.destroy;o=o.tag,s!==void 0&&(o&2||o&4)&&Ul(n,e,s),i=i.next}while(i!==r)}qt(t,e,n);break;case 1:if(!be&&(ur(n,e),r=n.stateNode,typeof r.componentWillUnmount=="function"))try{r.props=n.memoizedProps,r.state=n.memoizedState,r.componentWillUnmount()}catch(l){re(n,e,l)}qt(t,e,n);break;case 21:qt(t,e,n);break;case 22:n.mode&1?(be=(r=be)||n.memoizedState!==null,qt(t,e,n),be=r):qt(t,e,n);break;default:qt(t,e,n)}}function Ip(t){var e=t.updateQueue;if(e!==null){t.updateQueue=null;var n=t.stateNode;n===null&&(n=t.stateNode=new ny),e.forEach(function(r){var i=hy.bind(null,t,r);n.has(r)||(n.add(r),r.then(i,i))})}}function ot(t,e){var n=e.deletions;if(n!==null)for(var r=0;r<n.length;r++){var i=n[r];try{var o=t,s=e,l=s;e:for(;l!==null;){switch(l.tag){case 5:me=l.stateNode,it=!1;break e;case 3:me=l.stateNode.containerInfo,it=!0;break e;case 4:me=l.stateNode.containerInfo,it=!0;break e}l=l.return}if(me===null)throw Error(j(160));Rp(o,s,i),me=null,it=!1;var c=i.alternate;c!==null&&(c.return=null),i.return=null}catch(d){re(i,e,d)}}if(e.subtreeFlags&12854)for(e=e.child;e!==null;)Op(e,t),e=e.sibling}function Op(t,e){var n=t.alternate,r=t.flags;switch(t.tag){case 0:case 11:case 14:case 15:if(ot(e,t),yt(t),r&4){try{xi(3,t,t.return),Ho(3,t)}catch(b){re(t,t.return,b)}try{xi(5,t,t.return)}catch(b){re(t,t.return,b)}}break;case 1:ot(e,t),yt(t),r&512&&n!==null&&ur(n,n.return);break;case 5:if(ot(e,t),yt(t),r&512&&n!==null&&ur(n,n.return),t.flags&32){var i=t.stateNode;try{Br(i,"")}catch(b){re(t,t.return,b)}}if(r&4&&(i=t.stateNode,i!=null)){var o=t.memoizedProps,s=n!==null?n.memoizedProps:o,l=t.type,c=t.updateQueue;if(t.updateQueue=null,c!==null)try{l==="input"&&o.type==="radio"&&o.name!=null&&cu(i,o),$a(l,s);var d=$a(l,o);for(s=0;s<c.length;s+=2){var h=c[s],p=c[s+1];h==="style"?xu(i,p):h==="dangerouslySetInnerHTML"?gu(i,p):h==="children"?Br(i,p):aa(i,h,p,d)}switch(l){case"input":wa(i,o);break;case"textarea":hu(i,o);break;case"select":var f=i._wrapperState.wasMultiple;i._wrapperState.wasMultiple=!!o.multiple;var v=o.value;v!=null?Un(i,!!o.multiple,v,!1):f!==!!o.multiple&&(o.defaultValue!=null?Un(i,!!o.multiple,o.defaultValue,!0):Un(i,!!o.multiple,o.multiple?[]:"",!1))}i[si]=o}catch(b){re(t,t.return,b)}}break;case 6:if(ot(e,t),yt(t),r&4){if(t.stateNode===null)throw Error(j(162));i=t.stateNode,o=t.memoizedProps;try{i.nodeValue=o}catch(b){re(t,t.return,b)}}break;case 3:if(ot(e,t),yt(t),r&4&&n!==null&&n.memoizedState.isDehydrated)try{Qr(e.containerInfo)}catch(b){re(t,t.return,b)}break;case 4:ot(e,t),yt(t);break;case 13:ot(e,t),yt(t),i=t.child,i.flags&8192&&(o=i.memoizedState!==null,i.stateNode.isHidden=o,!o||i.alternate!==null&&i.alternate.memoizedState!==null||(Xl=oe())),r&4&&Ip(t);break;case 22:if(h=n!==null&&n.memoizedState!==null,t.mode&1?(be=(d=be)||h,ot(e,t),be=d):ot(e,t),yt(t),r&8192){if(d=t.memoizedState!==null,(t.stateNode.isHidden=d)&&!h&&t.mode&1)for(R=t,h=t.child;h!==null;){for(p=R=h;R!==null;){switch(f=R,v=f.child,f.tag){case 0:case 11:case 14:case 15:xi(4,f,f.return);break;case 1:ur(f,f.return);var x=f.stateNode;if(typeof x.componentWillUnmount=="function"){r=f,n=f.return;try{e=r,x.props=e.memoizedProps,x.state=e.memoizedState,x.componentWillUnmount()}catch(b){re(r,n,b)}}break;case 5:ur(f,f.return);break;case 22:if(f.memoizedState!==null){Bp(p);continue}}v!==null?(v.return=f,R=v):Bp(p)}h=h.sibling}e:for(h=null,p=t;;){if(p.tag===5){if(h===null){h=p;try{i=p.stateNode,d?(o=i.style,typeof o.setProperty=="function"?o.setProperty("display","none","important"):o.display="none"):(l=p.stateNode,c=p.memoizedProps.style,s=c!=null&&c.hasOwnProperty("display")?c.display:null,l.style.display=mu("display",s))}catch(b){re(t,t.return,b)}}}else if(p.tag===6){if(h===null)try{p.stateNode.nodeValue=d?"":p.memoizedProps}catch(b){re(t,t.return,b)}}else if((p.tag!==22&&p.tag!==23||p.memoizedState===null||p===t)&&p.child!==null){p.child.return=p,p=p.child;continue}if(p===t)break e;for(;p.sibling===null;){if(p.return===null||p.return===t)break e;h===p&&(h=null),p=p.return}h===p&&(h=null),p.sibling.return=p.return,p=p.sibling}}break;case 19:ot(e,t),yt(t),r&4&&Ip(t);break;case 21:break;default:ot(e,t),yt(t)}}function yt(t){var e=t.flags;if(e&2){try{e:{for(var n=t.return;n!==null;){if(Pp(n)){var r=n;break e}n=n.return}throw Error(j(160))}switch(r.tag){case 5:var i=r.stateNode;r.flags&32&&(Br(i,""),r.flags&=-33);var o=Ap(t);Yl(t,o,i);break;case 3:case 4:var s=r.stateNode.containerInfo,l=Ap(t);Vl(t,l,s);break;default:throw Error(j(161))}}catch(c){re(t,t.return,c)}t.flags&=-3}e&4096&&(t.flags&=-4097)}function iy(t,e,n){R=t,Lp(t)}function Lp(t,e,n){for(var r=(t.mode&1)!==0;R!==null;){var i=R,o=i.child;if(i.tag===22&&r){var s=i.memoizedState!==null||_o;if(!s){var l=i.alternate,c=l!==null&&l.memoizedState!==null||be;l=_o;var d=be;if(_o=s,(be=c)&&!d)for(R=i;R!==null;)s=R,c=s.child,s.tag===22&&s.memoizedState!==null?Np(i):c!==null?(c.return=s,R=c):Np(i);for(;o!==null;)R=o,Lp(o),o=o.sibling;R=i,_o=l,be=d}zp(t)}else i.subtreeFlags&8772&&o!==null?(o.return=i,R=o):zp(t)}}function zp(t){for(;R!==null;){var e=R;if(e.flags&8772){var n=e.alternate;try{if(e.flags&8772)switch(e.tag){case 0:case 11:case 15:be||Ho(5,e);break;case 1:var r=e.stateNode;if(e.flags&4&&!be)if(n===null)r.componentDidMount();else{var i=e.elementType===e.type?n.memoizedProps:rt(e.type,n.memoizedProps);r.componentDidUpdate(i,n.memoizedState,r.__reactInternalSnapshotBeforeUpdate)}var o=e.updateQueue;o!==null&&Bh(e,o,r);break;case 3:var s=e.updateQueue;if(s!==null){if(n=null,e.child!==null)switch(e.child.tag){case 5:n=e.child.stateNode;break;case 1:n=e.child.stateNode}Bh(e,s,n)}break;case 5:var l=e.stateNode;if(n===null&&e.flags&4){n=l;var c=e.memoizedProps;switch(e.type){case"button":case"input":case"select":case"textarea":c.autoFocus&&n.focus();break;case"img":c.src&&(n.src=c.src)}}break;case 6:break;case 4:break;case 12:break;case 13:if(e.memoizedState===null){var d=e.alternate;if(d!==null){var h=d.memoizedState;if(h!==null){var p=h.dehydrated;p!==null&&Qr(p)}}}break;case 19:case 17:case 21:case 22:case 23:case 25:break;default:throw Error(j(163))}be||e.flags&512&&Gl(e)}catch(f){re(e,e.return,f)}}if(e===t){R=null;break}if(n=e.sibling,n!==null){n.return=e.return,R=n;break}R=e.return}}function Bp(t){for(;R!==null;){var e=R;if(e===t){R=null;break}var n=e.sibling;if(n!==null){n.return=e.return,R=n;break}R=e.return}}function Np(t){for(;R!==null;){var e=R;try{switch(e.tag){case 0:case 11:case 15:var n=e.return;try{Ho(4,e)}catch(c){re(e,n,c)}break;case 1:var r=e.stateNode;if(typeof r.componentDidMount=="function"){var i=e.return;try{r.componentDidMount()}catch(c){re(e,i,c)}}var o=e.return;try{Gl(e)}catch(c){re(e,o,c)}break;case 5:var s=e.return;try{Gl(e)}catch(c){re(e,s,c)}}}catch(c){re(e,e.return,c)}if(e===t){R=null;break}var l=e.sibling;if(l!==null){l.return=e.return,R=l;break}R=e.return}}var oy=Math.ceil,Wo=bt.ReactCurrentDispatcher,Kl=bt.ReactCurrentOwner,Qe=bt.ReactCurrentBatchConfig,F=0,fe=null,se=null,xe=0,_e=0,hr=Yt(0),ce=0,yi=null,bn=0,Uo=0,Ql=0,wi=null,De=null,Xl=0,pr=1/0,Et=null,Go=!1,Jl=null,Zt=null,Vo=!1,en=null,Yo=0,vi=0,ql=null,Ko=-1,Qo=0;function Te(){return F&6?oe():Ko!==-1?Ko:Ko=oe()}function tn(t){return t.mode&1?F&2&&xe!==0?xe&-xe:H0.transition!==null?(Qo===0&&(Qo=Au()),Qo):(t=U,t!==0||(t=window.event,t=t===void 0?16:_u(t.type)),t):1}function st(t,e,n,r){if(50<vi)throw vi=0,ql=null,Error(j(185));Ur(t,n,r),(!(F&2)||t!==fe)&&(t===fe&&(!(F&2)&&(Uo|=n),ce===4&&nn(t,xe)),Pe(t,r),n===1&&F===0&&!(e.mode&1)&&(pr=oe()+500,ko&&Qt()))}function Pe(t,e){var n=t.callbackNode;Hx(t,e);var r=ro(t,t===fe?xe:0);if(r===0)n!==null&&Eu(n),t.callbackNode=null,t.callbackPriority=0;else if(e=r&-r,t.callbackPriority!==e){if(n!=null&&Eu(n),e===1)t.tag===0?_0(_p.bind(null,t)):Th(_p.bind(null,t)),z0(function(){!(F&6)&&Qt()}),n=null;else{switch(Ru(r)){case 1:n=Pa;break;case 4:n=Du;break;case 16:n=Zi;break;case 536870912:n=Pu;break;default:n=Zi}n=Qp(n,Fp.bind(null,t))}t.callbackPriority=e,t.callbackNode=n}}function Fp(t,e){if(Ko=-1,Qo=0,F&6)throw Error(j(327));var n=t.callbackNode;if(fr()&&t.callbackNode!==n)return null;var r=ro(t,t===fe?xe:0);if(r===0)return null;if(r&30||r&t.expiredLanes||e)e=Xo(t,r);else{e=r;var i=F;F|=2;var o=Wp();(fe!==t||xe!==e)&&(Et=null,pr=oe()+500,kn(t,e));do try{ly();break}catch(l){Hp(t,l)}while(!0);ml(),Wo.current=o,F=i,se!==null?e=0:(fe=null,xe=0,e=ce)}if(e!==0){if(e===2&&(i=Aa(t),i!==0&&(r=i,e=Zl(t,i))),e===1)throw n=yi,kn(t,0),nn(t,r),Pe(t,oe()),n;if(e===6)nn(t,r);else{if(i=t.current.alternate,!(r&30)&&!sy(i)&&(e=Xo(t,r),e===2&&(o=Aa(t),o!==0&&(r=o,e=Zl(t,o))),e===1))throw n=yi,kn(t,0),nn(t,r),Pe(t,oe()),n;switch(t.finishedWork=i,t.finishedLanes=r,e){case 0:case 1:throw Error(j(345));case 2:$n(t,De,Et);break;case 3:if(nn(t,r),(r&130023424)===r&&(e=Xl+500-oe(),10<e)){if(ro(t,0)!==0)break;if(i=t.suspendedLanes,(i&r)!==r){Te(),t.pingedLanes|=t.suspendedLanes&i;break}t.timeoutHandle=ol($n.bind(null,t,De,Et),e);break}$n(t,De,Et);break;case 4:if(nn(t,r),(r&4194240)===r)break;for(e=t.eventTimes,i=-1;0<r;){var s=31-et(r);o=1<<s,s=e[s],s>i&&(i=s),r&=~o}if(r=i,r=oe()-r,r=(120>r?120:480>r?480:1080>r?1080:1920>r?1920:3e3>r?3e3:4320>r?4320:1960*oy(r/1960))-r,10<r){t.timeoutHandle=ol($n.bind(null,t,De,Et),r);break}$n(t,De,Et);break;case 5:$n(t,De,Et);break;default:throw Error(j(329))}}}return Pe(t,oe()),t.callbackNode===n?Fp.bind(null,t):null}function Zl(t,e){var n=wi;return t.current.memoizedState.isDehydrated&&(kn(t,e).flags|=256),t=Xo(t,e),t!==2&&(e=De,De=n,e!==null&&ec(e)),t}function ec(t){De===null?De=t:De.push.apply(De,t)}function sy(t){for(var e=t;;){if(e.flags&16384){var n=e.updateQueue;if(n!==null&&(n=n.stores,n!==null))for(var r=0;r<n.length;r++){var i=n[r],o=i.getSnapshot;i=i.value;try{if(!tt(o(),i))return!1}catch{return!1}}}if(n=e.child,e.subtreeFlags&16384&&n!==null)n.return=e,e=n;else{if(e===t)break;for(;e.sibling===null;){if(e.return===null||e.return===t)return!0;e=e.return}e.sibling.return=e.return,e=e.sibling}}return!0}function nn(t,e){for(e&=~Ql,e&=~Uo,t.suspendedLanes|=e,t.pingedLanes&=~e,t=t.expirationTimes;0<e;){var n=31-et(e),r=1<<n;t[n]=-1,e&=~r}}function _p(t){if(F&6)throw Error(j(327));fr();var e=ro(t,0);if(!(e&1))return Pe(t,oe()),null;var n=Xo(t,e);if(t.tag!==0&&n===2){var r=Aa(t);r!==0&&(e=r,n=Zl(t,r))}if(n===1)throw n=yi,kn(t,0),nn(t,e),Pe(t,oe()),n;if(n===6)throw Error(j(345));return t.finishedWork=t.current.alternate,t.finishedLanes=e,$n(t,De,Et),Pe(t,oe()),null}function tc(t,e){var n=F;F|=1;try{return t(e)}finally{F=n,F===0&&(pr=oe()+500,ko&&Qt())}}function Sn(t){en!==null&&en.tag===0&&!(F&6)&&fr();var e=F;F|=1;var n=Qe.transition,r=U;try{if(Qe.transition=null,U=1,t)return t()}finally{U=r,Qe.transition=n,F=e,!(F&6)&&Qt()}}function nc(){_e=hr.current,X(hr)}function kn(t,e){t.finishedWork=null,t.finishedLanes=0;var n=t.timeoutHandle;if(n!==-1&&(t.timeoutHandle=-1,L0(n)),se!==null)for(n=se.return;n!==null;){var r=n;switch(ul(r),r.tag){case 1:r=r.type.childContextTypes,r!=null&&bo();break;case 3:cr(),X(Me),X(ye),$l();break;case 5:Sl(r);break;case 4:cr();break;case 13:X(ee);break;case 19:X(ee);break;case 10:xl(r.type._context);break;case 22:case 23:nc()}n=n.return}if(fe=t,se=t=rn(t.current,null),xe=_e=e,ce=0,yi=null,Ql=Uo=bn=0,De=wi=null,yn!==null){for(e=0;e<yn.length;e++)if(n=yn[e],r=n.interleaved,r!==null){n.interleaved=null;var i=r.next,o=n.pending;if(o!==null){var s=o.next;o.next=i,r.next=s}n.pending=r}yn=null}return t}function Hp(t,e){do{var n=se;try{if(ml(),Ro.current=zo,Io){for(var r=te.memoizedState;r!==null;){var i=r.queue;i!==null&&(i.pending=null),r=r.next}Io=!1}if(vn=0,pe=le=te=null,hi=!1,pi=0,Kl.current=null,n===null||n.return===null){ce=1,yi=e,se=null;break}e:{var o=t,s=n.return,l=n,c=e;if(e=xe,l.flags|=32768,c!==null&&typeof c=="object"&&typeof c.then=="function"){var d=c,h=l,p=h.tag;if(!(h.mode&1)&&(p===0||p===11||p===15)){var f=h.alternate;f?(h.updateQueue=f.updateQueue,h.memoizedState=f.memoizedState,h.lanes=f.lanes):(h.updateQueue=null,h.memoizedState=null)}var v=pp(s);if(v!==null){v.flags&=-257,fp(v,s,l,o,e),v.mode&1&&hp(o,d,e),e=v,c=d;var x=e.updateQueue;if(x===null){var b=new Set;b.add(c),e.updateQueue=b}else x.add(c);break e}else{if(!(e&1)){hp(o,d,e),rc();break e}c=Error(j(426))}}else if(q&&l.mode&1){var M=pp(s);if(M!==null){!(M.flags&65536)&&(M.flags|=256),fp(M,s,l,o,e),fl(dr(c,l));break e}}o=c=dr(c,l),ce!==4&&(ce=2),wi===null?wi=[o]:wi.push(o),o=s;do{switch(o.tag){case 3:o.flags|=65536,e&=-e,o.lanes|=e;var w=dp(o,c,e);zh(o,w);break e;case 1:l=c;var g=o.type,y=o.stateNode;if(!(o.flags&128)&&(typeof g.getDerivedStateFromError=="function"||y!==null&&typeof y.componentDidCatch=="function"&&(Zt===null||!Zt.has(y)))){o.flags|=65536,e&=-e,o.lanes|=e;var S=up(o,l,e);zh(o,S);break e}}o=o.return}while(o!==null)}Gp(n)}catch(T){e=T,se===n&&n!==null&&(se=n=n.return);continue}break}while(!0)}function Wp(){var t=Wo.current;return Wo.current=zo,t===null?zo:t}function rc(){(ce===0||ce===3||ce===2)&&(ce=4),fe===null||!(bn&268435455)&&!(Uo&268435455)||nn(fe,xe)}function Xo(t,e){var n=F;F|=2;var r=Wp();(fe!==t||xe!==e)&&(Et=null,kn(t,e));do try{ay();break}catch(i){Hp(t,i)}while(!0);if(ml(),F=n,Wo.current=r,se!==null)throw Error(j(261));return fe=null,xe=0,ce}function ay(){for(;se!==null;)Up(se)}function ly(){for(;se!==null&&!Rx();)Up(se)}function Up(t){var e=Kp(t.alternate,t,_e);t.memoizedProps=t.pendingProps,e===null?Gp(t):se=e,Kl.current=null}function Gp(t){var e=t;do{var n=e.alternate;if(t=e.return,e.flags&32768){if(n=ty(n,e),n!==null){n.flags&=32767,se=n;return}if(t!==null)t.flags|=32768,t.subtreeFlags=0,t.deletions=null;else{ce=6,se=null;return}}else if(n=ey(n,e,_e),n!==null){se=n;return}if(e=e.sibling,e!==null){se=e;return}se=e=t}while(e!==null);ce===0&&(ce=5)}function $n(t,e,n){var r=U,i=Qe.transition;try{Qe.transition=null,U=1,cy(t,e,n,r)}finally{Qe.transition=i,U=r}return null}function cy(t,e,n,r){do fr();while(en!==null);if(F&6)throw Error(j(327));n=t.finishedWork;var i=t.finishedLanes;if(n===null)return null;if(t.finishedWork=null,t.finishedLanes=0,n===t.current)throw Error(j(177));t.callbackNode=null,t.callbackPriority=0;var o=n.lanes|n.childLanes;if(Wx(t,o),t===fe&&(se=fe=null,xe=0),!(n.subtreeFlags&2064)&&!(n.flags&2064)||Vo||(Vo=!0,Qp(Zi,function(){return fr(),null})),o=(n.flags&15990)!==0,n.subtreeFlags&15990||o){o=Qe.transition,Qe.transition=null;var s=U;U=1;var l=F;F|=4,Kl.current=null,ry(t,n),Op(n,t),E0(rl),so=!!nl,rl=nl=null,t.current=n,iy(n),Ix(),F=l,U=s,Qe.transition=o}else t.current=n;if(Vo&&(Vo=!1,en=t,Yo=i),o=t.pendingLanes,o===0&&(Zt=null),zx(n.stateNode),Pe(t,oe()),e!==null)for(r=t.onRecoverableError,n=0;n<e.length;n++)i=e[n],r(i.value,{componentStack:i.stack,digest:i.digest});if(Go)throw Go=!1,t=Jl,Jl=null,t;return Yo&1&&t.tag!==0&&fr(),o=t.pendingLanes,o&1?t===ql?vi++:(vi=0,ql=t):vi=0,Qt(),null}function fr(){if(en!==null){var t=Ru(Yo),e=Qe.transition,n=U;try{if(Qe.transition=null,U=16>t?16:t,en===null)var r=!1;else{if(t=en,en=null,Yo=0,F&6)throw Error(j(331));var i=F;for(F|=4,R=t.current;R!==null;){var o=R,s=o.child;if(R.flags&16){var l=o.deletions;if(l!==null){for(var c=0;c<l.length;c++){var d=l[c];for(R=d;R!==null;){var h=R;switch(h.tag){case 0:case 11:case 15:xi(8,h,o)}var p=h.child;if(p!==null)p.return=h,R=p;else for(;R!==null;){h=R;var f=h.sibling,v=h.return;if(Dp(h),h===d){R=null;break}if(f!==null){f.return=v,R=f;break}R=v}}}var x=o.alternate;if(x!==null){var b=x.child;if(b!==null){x.child=null;do{var M=b.sibling;b.sibling=null,b=M}while(b!==null)}}R=o}}if(o.subtreeFlags&2064&&s!==null)s.return=o,R=s;else e:for(;R!==null;){if(o=R,o.flags&2048)switch(o.tag){case 0:case 11:case 15:xi(9,o,o.return)}var w=o.sibling;if(w!==null){w.return=o.return,R=w;break e}R=o.return}}var g=t.current;for(R=g;R!==null;){s=R;var y=s.child;if(s.subtreeFlags&2064&&y!==null)y.return=s,R=y;else e:for(s=g;R!==null;){if(l=R,l.flags&2048)try{switch(l.tag){case 0:case 11:case 15:Ho(9,l)}}catch(T){re(l,l.return,T)}if(l===s){R=null;break e}var S=l.sibling;if(S!==null){S.return=l.return,R=S;break e}R=l.return}}if(F=i,Qt(),ft&&typeof ft.onPostCommitFiberRoot=="function")try{ft.onPostCommitFiberRoot(eo,t)}catch{}r=!0}return r}finally{U=n,Qe.transition=e}}return!1}function Vp(t,e,n){e=dr(n,e),e=dp(t,e,1),t=Jt(t,e,1),e=Te(),t!==null&&(Ur(t,1,e),Pe(t,e))}function re(t,e,n){if(t.tag===3)Vp(t,t,n);else for(;e!==null;){if(e.tag===3){Vp(e,t,n);break}else if(e.tag===1){var r=e.stateNode;if(typeof e.type.getDerivedStateFromError=="function"||typeof r.componentDidCatch=="function"&&(Zt===null||!Zt.has(r))){t=dr(n,t),t=up(e,t,1),e=Jt(e,t,1),t=Te(),e!==null&&(Ur(e,1,t),Pe(e,t));break}}e=e.return}}function dy(t,e,n){var r=t.pingCache;r!==null&&r.delete(e),e=Te(),t.pingedLanes|=t.suspendedLanes&n,fe===t&&(xe&n)===n&&(ce===4||ce===3&&(xe&130023424)===xe&&500>oe()-Xl?kn(t,0):Ql|=n),Pe(t,e)}function Yp(t,e){e===0&&(t.mode&1?(e=no,no<<=1,!(no&130023424)&&(no=4194304)):e=1);var n=Te();t=Tt(t,e),t!==null&&(Ur(t,e,n),Pe(t,n))}function uy(t){var e=t.memoizedState,n=0;e!==null&&(n=e.retryLane),Yp(t,n)}function hy(t,e){var n=0;switch(t.tag){case 13:var r=t.stateNode,i=t.memoizedState;i!==null&&(n=i.retryLane);break;case 19:r=t.stateNode;break;default:throw Error(j(314))}r!==null&&r.delete(e),Yp(t,n)}var Kp;Kp=function(t,e,n){if(t!==null)if(t.memoizedProps!==e.pendingProps||Me.current)Ee=!0;else{if(!(t.lanes&n)&&!(e.flags&128))return Ee=!1,Z0(t,e,n);Ee=!!(t.flags&131072)}else Ee=!1,q&&e.flags&1048576&&Mh(e,Co,e.index);switch(e.lanes=0,e.tag){case 2:var r=e.type;Fo(t,e),t=e.pendingProps;var i=nr(e,ye.current);lr(e,n),i=Ml(null,e,r,t,i,n);var o=jl();return e.flags|=1,typeof i=="object"&&i!==null&&typeof i.render=="function"&&i.$$typeof===void 0?(e.tag=1,e.memoizedState=null,e.updateQueue=null,je(r)?(o=!0,So(e)):o=!1,e.memoizedState=i.state!==null&&i.state!==void 0?i.state:null,vl(e),i.updater=Bo,e.stateNode=i,i._reactInternals=e,Il(e,r,t,n),e=Bl(null,e,r,!0,o,n)):(e.tag=0,q&&o&&dl(e),Ce(null,e,i,n),e=e.child),e;case 16:r=e.elementType;e:{switch(Fo(t,e),t=e.pendingProps,i=r._init,r=i(r._payload),e.type=r,i=e.tag=fy(r),t=rt(r,t),i){case 0:e=zl(null,e,r,t,n);break e;case 1:e=vp(null,e,r,t,n);break e;case 11:e=gp(null,e,r,t,n);break e;case 14:e=mp(null,e,r,rt(r.type,t),n);break e}throw Error(j(306,r,""))}return e;case 0:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),zl(t,e,r,i,n);case 1:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),vp(t,e,r,i,n);case 3:e:{if(bp(e),t===null)throw Error(j(387));r=e.pendingProps,o=e.memoizedState,i=o.element,Lh(t,e),Po(e,r,null,n);var s=e.memoizedState;if(r=s.element,o.isDehydrated)if(o={element:r,isDehydrated:!1,cache:s.cache,pendingSuspenseBoundaries:s.pendingSuspenseBoundaries,transitions:s.transitions},e.updateQueue.baseState=o,e.memoizedState=o,e.flags&256){i=dr(Error(j(423)),e),e=Sp(t,e,r,n,i);break e}else if(r!==i){i=dr(Error(j(424)),e),e=Sp(t,e,r,n,i);break e}else for(Fe=Vt(e.stateNode.containerInfo.firstChild),Ne=e,q=!0,nt=null,n=Ih(e,null,r,n),e.child=n;n;)n.flags=n.flags&-3|4096,n=n.sibling;else{if(or(),r===i){e=jt(t,e,n);break e}Ce(t,e,r,n)}e=e.child}return e;case 5:return Nh(e),t===null&&pl(e),r=e.type,i=e.pendingProps,o=t!==null?t.memoizedProps:null,s=i.children,il(r,i)?s=null:o!==null&&il(r,o)&&(e.flags|=32),wp(t,e),Ce(t,e,s,n),e.child;case 6:return t===null&&pl(e),null;case 13:return kp(t,e,n);case 4:return bl(e,e.stateNode.containerInfo),r=e.pendingProps,t===null?e.child=sr(e,null,r,n):Ce(t,e,r,n),e.child;case 11:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),gp(t,e,r,i,n);case 7:return Ce(t,e,e.pendingProps,n),e.child;case 8:return Ce(t,e,e.pendingProps.children,n),e.child;case 12:return Ce(t,e,e.pendingProps.children,n),e.child;case 10:e:{if(r=e.type._context,i=e.pendingProps,o=e.memoizedProps,s=i.value,Y(jo,r._currentValue),r._currentValue=s,o!==null)if(tt(o.value,s)){if(o.children===i.children&&!Me.current){e=jt(t,e,n);break e}}else for(o=e.child,o!==null&&(o.return=e);o!==null;){var l=o.dependencies;if(l!==null){s=o.child;for(var c=l.firstContext;c!==null;){if(c.context===r){if(o.tag===1){c=Mt(-1,n&-n),c.tag=2;var d=o.updateQueue;if(d!==null){d=d.shared;var h=d.pending;h===null?c.next=c:(c.next=h.next,h.next=c),d.pending=c}}o.lanes|=n,c=o.alternate,c!==null&&(c.lanes|=n),yl(o.return,n,e),l.lanes|=n;break}c=c.next}}else if(o.tag===10)s=o.type===e.type?null:o.child;else if(o.tag===18){if(s=o.return,s===null)throw Error(j(341));s.lanes|=n,l=s.alternate,l!==null&&(l.lanes|=n),yl(s,n,e),s=o.sibling}else s=o.child;if(s!==null)s.return=o;else for(s=o;s!==null;){if(s===e){s=null;break}if(o=s.sibling,o!==null){o.return=s.return,s=o;break}s=s.return}o=s}Ce(t,e,i.children,n),e=e.child}return e;case 9:return i=e.type,r=e.pendingProps.children,lr(e,n),i=Ye(i),r=r(i),e.flags|=1,Ce(t,e,r,n),e.child;case 14:return r=e.type,i=rt(r,e.pendingProps),i=rt(r.type,i),mp(t,e,r,i,n);case 15:return xp(t,e,e.type,e.pendingProps,n);case 17:return r=e.type,i=e.pendingProps,i=e.elementType===r?i:rt(r,i),Fo(t,e),e.tag=1,je(r)?(t=!0,So(e)):t=!1,lr(e,n),lp(e,r,i),Il(e,r,i,n),Bl(null,e,r,!0,t,n);case 19:return Cp(t,e,n);case 22:return yp(t,e,n)}throw Error(j(156,e.tag))};function Qp(t,e){return ju(t,e)}function py(t,e,n,r){this.tag=t,this.key=n,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.ref=null,this.pendingProps=e,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=r,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function Xe(t,e,n,r){return new py(t,e,n,r)}function ic(t){return t=t.prototype,!(!t||!t.isReactComponent)}function fy(t){if(typeof t=="function")return ic(t)?1:0;if(t!=null){if(t=t.$$typeof,t===da)return 11;if(t===pa)return 14}return 2}function rn(t,e){var n=t.alternate;return n===null?(n=Xe(t.tag,e,t.key,t.mode),n.elementType=t.elementType,n.type=t.type,n.stateNode=t.stateNode,n.alternate=t,t.alternate=n):(n.pendingProps=e,n.type=t.type,n.flags=0,n.subtreeFlags=0,n.deletions=null),n.flags=t.flags&14680064,n.childLanes=t.childLanes,n.lanes=t.lanes,n.child=t.child,n.memoizedProps=t.memoizedProps,n.memoizedState=t.memoizedState,n.updateQueue=t.updateQueue,e=t.dependencies,n.dependencies=e===null?null:{lanes:e.lanes,firstContext:e.firstContext},n.sibling=t.sibling,n.index=t.index,n.ref=t.ref,n}function Jo(t,e,n,r,i,o){var s=2;if(r=t,typeof t=="function")ic(t)&&(s=1);else if(typeof t=="string")s=5;else e:switch(t){case Wn:return Cn(n.children,i,o,e);case la:s=8,i|=8;break;case ca:return t=Xe(12,n,e,i|2),t.elementType=ca,t.lanes=o,t;case ua:return t=Xe(13,n,e,i),t.elementType=ua,t.lanes=o,t;case ha:return t=Xe(19,n,e,i),t.elementType=ha,t.lanes=o,t;case iu:return qo(n,i,o,e);default:if(typeof t=="object"&&t!==null)switch(t.$$typeof){case nu:s=10;break e;case ru:s=9;break e;case da:s=11;break e;case pa:s=14;break e;case Bt:s=16,r=null;break e}throw Error(j(130,t==null?t:typeof t,""))}return e=Xe(s,n,e,i),e.elementType=t,e.type=r,e.lanes=o,e}function Cn(t,e,n,r){return t=Xe(7,t,r,e),t.lanes=n,t}function qo(t,e,n,r){return t=Xe(22,t,r,e),t.elementType=iu,t.lanes=n,t.stateNode={isHidden:!1},t}function oc(t,e,n){return t=Xe(6,t,null,e),t.lanes=n,t}function sc(t,e,n){return e=Xe(4,t.children!==null?t.children:[],t.key,e),e.lanes=n,e.stateNode={containerInfo:t.containerInfo,pendingChildren:null,implementation:t.implementation},e}function gy(t,e,n,r,i){this.tag=e,this.containerInfo=t,this.finishedWork=this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.pendingContext=this.context=null,this.callbackPriority=0,this.eventTimes=Ra(0),this.expirationTimes=Ra(-1),this.entangledLanes=this.finishedLanes=this.mutableReadLanes=this.expiredLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=Ra(0),this.identifierPrefix=r,this.onRecoverableError=i,this.mutableSourceEagerHydrationData=null}function ac(t,e,n,r,i,o,s,l,c){return t=new gy(t,e,n,l,c),e===1?(e=1,o===!0&&(e|=8)):e=0,o=Xe(3,null,null,e),t.current=o,o.stateNode=t,o.memoizedState={element:r,isDehydrated:n,cache:null,transitions:null,pendingSuspenseBoundaries:null},vl(o),t}function my(t,e,n){var r=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:Hn,key:r==null?null:""+r,children:t,containerInfo:e,implementation:n}}function Xp(t){if(!t)return Kt;t=t._reactInternals;e:{if(pn(t)!==t||t.tag!==1)throw Error(j(170));var e=t;do{switch(e.tag){case 3:e=e.stateNode.context;break e;case 1:if(je(e.type)){e=e.stateNode.__reactInternalMemoizedMergedChildContext;break e}}e=e.return}while(e!==null);throw Error(j(171))}if(t.tag===1){var n=t.type;if(je(n))return $h(t,n,e)}return e}function Jp(t,e,n,r,i,o,s,l,c){return t=ac(n,r,!0,t,i,o,s,l,c),t.context=Xp(null),n=t.current,r=Te(),i=tn(n),o=Mt(r,i),o.callback=e??null,Jt(n,o,i),t.current.lanes=i,Ur(t,i,r),Pe(t,r),t}function Zo(t,e,n,r){var i=e.current,o=Te(),s=tn(i);return n=Xp(n),e.context===null?e.context=n:e.pendingContext=n,e=Mt(o,s),e.payload={element:t},r=r===void 0?null:r,r!==null&&(e.callback=r),t=Jt(i,e,s),t!==null&&(st(t,i,s,o),Do(t,i,s)),s}function es(t){if(t=t.current,!t.child)return null;switch(t.child.tag){case 5:return t.child.stateNode;default:return t.child.stateNode}}function qp(t,e){if(t=t.memoizedState,t!==null&&t.dehydrated!==null){var n=t.retryLane;t.retryLane=n!==0&&n<e?n:e}}function lc(t,e){qp(t,e),(t=t.alternate)&&qp(t,e)}function xy(){return null}var Zp=typeof reportError=="function"?reportError:function(t){console.error(t)};function cc(t){this._internalRoot=t}ts.prototype.render=cc.prototype.render=function(t){var e=this._internalRoot;if(e===null)throw Error(j(409));Zo(t,e,null,null)},ts.prototype.unmount=cc.prototype.unmount=function(){var t=this._internalRoot;if(t!==null){this._internalRoot=null;var e=t.containerInfo;Sn(function(){Zo(null,t,null,null)}),e[St]=null}};function ts(t){this._internalRoot=t}ts.prototype.unstable_scheduleHydration=function(t){if(t){var e=Lu();t={blockedOn:null,target:t,priority:e};for(var n=0;n<Wt.length&&e!==0&&e<Wt[n].priority;n++);Wt.splice(n,0,t),n===0&&Nu(t)}};function dc(t){return!(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11)}function ns(t){return!(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11&&(t.nodeType!==8||t.nodeValue!==" react-mount-point-unstable "))}function ef(){}function yy(t,e,n,r,i){if(i){if(typeof r=="function"){var o=r;r=function(){var d=es(s);o.call(d)}}var s=Jp(e,r,t,0,null,!1,!1,"",ef);return t._reactRootContainer=s,t[St]=s.current,ii(t.nodeType===8?t.parentNode:t),Sn(),s}for(;i=t.lastChild;)t.removeChild(i);if(typeof r=="function"){var l=r;r=function(){var d=es(c);l.call(d)}}var c=ac(t,0,!1,null,null,!1,!1,"",ef);return t._reactRootContainer=c,t[St]=c.current,ii(t.nodeType===8?t.parentNode:t),Sn(function(){Zo(e,c,n,r)}),c}function rs(t,e,n,r,i){var o=n._reactRootContainer;if(o){var s=o;if(typeof i=="function"){var l=i;i=function(){var c=es(s);l.call(c)}}Zo(e,s,t,i)}else s=yy(n,e,t,i,r);return es(s)}Iu=function(t){switch(t.tag){case 3:var e=t.stateNode;if(e.current.memoizedState.isDehydrated){var n=Wr(e.pendingLanes);n!==0&&(Ia(e,n|1),Pe(e,oe()),!(F&6)&&(pr=oe()+500,Qt()))}break;case 13:Sn(function(){var r=Tt(t,1);if(r!==null){var i=Te();st(r,t,1,i)}}),lc(t,1)}},Oa=function(t){if(t.tag===13){var e=Tt(t,134217728);if(e!==null){var n=Te();st(e,t,134217728,n)}lc(t,134217728)}},Ou=function(t){if(t.tag===13){var e=tn(t),n=Tt(t,e);if(n!==null){var r=Te();st(n,t,e,r)}lc(t,e)}},Lu=function(){return U},zu=function(t,e){var n=U;try{return U=t,e()}finally{U=n}},Ma=function(t,e,n){switch(e){case"input":if(wa(t,n),e=n.name,n.type==="radio"&&e!=null){for(n=t;n.parentNode;)n=n.parentNode;for(n=n.querySelectorAll("input[name="+JSON.stringify(""+e)+'][type="radio"]'),e=0;e<n.length;e++){var r=n[e];if(r!==t&&r.form===t.form){var i=vo(r);if(!i)throw Error(j(90));au(r),wa(r,i)}}}break;case"textarea":hu(t,n);break;case"select":e=n.value,e!=null&&Un(t,!!n.multiple,e,!1)}},bu=tc,Su=Sn;var wy={usingClientEntryPoint:!1,Events:[ai,er,vo,wu,vu,tc]},bi={findFiberByHostInstance:fn,bundleType:0,version:"18.3.1",rendererPackageName:"react-dom"},vy={bundleType:bi.bundleType,version:bi.version,rendererPackageName:bi.rendererPackageName,rendererConfig:bi.rendererConfig,overrideHookState:null,overrideHookStateDeletePath:null,overrideHookStateRenamePath:null,overrideProps:null,overridePropsDeletePath:null,overridePropsRenamePath:null,setErrorHandler:null,setSuspenseHandler:null,scheduleUpdate:null,currentDispatcherRef:bt.ReactCurrentDispatcher,findHostInstanceByFiber:function(t){return t=Tu(t),t===null?null:t.stateNode},findFiberByHostInstance:bi.findFiberByHostInstance||xy,findHostInstancesForRefresh:null,scheduleRefresh:null,scheduleRoot:null,setRefreshHandler:null,getCurrentFiber:null,reconcilerVersion:"18.3.1-next-f1338f8080-20240426"};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<"u"){var is=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!is.isDisabled&&is.supportsFiber)try{eo=is.inject(vy),ft=is}catch{}}Le.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED=wy,Le.createPortal=function(t,e){var n=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!dc(e))throw Error(j(200));return my(t,e,null,n)},Le.createRoot=function(t,e){if(!dc(t))throw Error(j(299));var n=!1,r="",i=Zp;return e!=null&&(e.unstable_strictMode===!0&&(n=!0),e.identifierPrefix!==void 0&&(r=e.identifierPrefix),e.onRecoverableError!==void 0&&(i=e.onRecoverableError)),e=ac(t,1,!1,null,null,n,!1,r,i),t[St]=e.current,ii(t.nodeType===8?t.parentNode:t),new cc(e)},Le.findDOMNode=function(t){if(t==null)return null;if(t.nodeType===1)return t;var e=t._reactInternals;if(e===void 0)throw typeof t.render=="function"?Error(j(188)):(t=Object.keys(t).join(","),Error(j(268,t)));return t=Tu(e),t=t===null?null:t.stateNode,t},Le.flushSync=function(t){return Sn(t)},Le.hydrate=function(t,e,n){if(!ns(e))throw Error(j(200));return rs(null,t,e,!0,n)},Le.hydrateRoot=function(t,e,n){if(!dc(t))throw Error(j(405));var r=n!=null&&n.hydratedSources||null,i=!1,o="",s=Zp;if(n!=null&&(n.unstable_strictMode===!0&&(i=!0),n.identifierPrefix!==void 0&&(o=n.identifierPrefix),n.onRecoverableError!==void 0&&(s=n.onRecoverableError)),e=Jp(e,null,t,1,n??null,i,!1,o,s),t[St]=e.current,ii(t),r)for(t=0;t<r.length;t++)n=r[t],i=n._getVersion,i=i(n._source),e.mutableSourceEagerHydrationData==null?e.mutableSourceEagerHydrationData=[n,i]:e.mutableSourceEagerHydrationData.push(n,i);return new ts(e)},Le.render=function(t,e,n){if(!ns(e))throw Error(j(200));return rs(null,t,e,!1,n)},Le.unmountComponentAtNode=function(t){if(!ns(t))throw Error(j(40));return t._reactRootContainer?(Sn(function(){rs(null,null,t,!1,function(){t._reactRootContainer=null,t[St]=null})}),!0):!1},Le.unstable_batchedUpdates=tc,Le.unstable_renderSubtreeIntoContainer=function(t,e,n,r){if(!ns(n))throw Error(j(200));if(t==null||t._reactInternals===void 0)throw Error(j(38));return rs(t,e,n,!1,r)},Le.version="18.3.1-next-f1338f8080-20240426";function tf(){if(!(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__>"u"||typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE!="function"))try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(tf)}catch(t){console.error(t)}}tf(),Xd.exports=Le;var by=Xd.exports,nf,rf=by;nf=rf.createRoot,rf.hydrateRoot;var Ae=function(){return Ae=Object.assign||function(e){for(var n,r=1,i=arguments.length;r<i;r++){n=arguments[r];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(e[o]=n[o])}return e},Ae.apply(this,arguments)};function os(t,e,n){if(n||arguments.length===2)for(var r=0,i=e.length,o;r<i;r++)(o||!(r in e))&&(o||(o=Array.prototype.slice.call(e,0,r)),o[r]=e[r]);return t.concat(o||Array.prototype.slice.call(e))}typeof SuppressedError=="function"&&SuppressedError;var J="-ms-",Si="-moz-",_="-webkit-",of="comm",ss="rule",uc="decl",Sy="@import",ky="@namespace",sf="@keyframes",$y="@layer",af=Math.abs,hc=String.fromCharCode,pc=Object.assign;function Cy(t,e){return de(t,0)^45?(((e<<2^de(t,0))<<2^de(t,1))<<2^de(t,2))<<2^de(t,3):0}function lf(t){return t.trim()}function Dt(t,e){return(t=e.exec(t))?t[0]:t}function z(t,e,n){return t.replace(e,n)}function as(t,e,n){return t.indexOf(e,n)}function de(t,e){return t.charCodeAt(e)|0}function Tn(t,e,n){return t.slice(e,n)}function at(t){return t.length}function cf(t){return t.length}function ki(t,e){return e.push(t),t}function Ty(t,e){return t.map(e).join("")}function df(t,e){return t.filter(function(n){return!Dt(n,e)})}var ls=1,gr=1,uf=0,Je=0,ae=0,mr="";function cs(t,e,n,r,i,o,s,l){return{value:t,root:e,parent:n,type:r,props:i,children:o,line:ls,column:gr,length:s,return:"",siblings:l}}function on(t,e){return pc(cs("",null,null,"",null,null,0,t.siblings),t,{length:-t.length},e)}function xr(t){for(;t.root;)t=on(t.root,{children:[t]});ki(t,t.siblings)}function My(){return ae}function jy(){return ae=Je>0?de(mr,--Je):0,gr--,ae===10&&(gr=1,ls--),ae}function lt(){return ae=Je<uf?de(mr,Je++):0,gr++,ae===10&&(gr=1,ls++),ae}function sn(){return de(mr,Je)}function ds(){return Je}function us(t,e){return Tn(mr,t,e)}function $i(t){switch(t){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function Ey(t){return ls=gr=1,uf=at(mr=t),Je=0,[]}function Dy(t){return mr="",t}function fc(t){return lf(us(Je-1,gc(t===91?t+2:t===40?t+1:t)))}function Py(t){for(;(ae=sn())&&ae<33;)lt();return $i(t)>2||$i(ae)>3?"":" "}function Ay(t,e){for(;--e&&lt()&&!(ae<48||ae>102||ae>57&&ae<65||ae>70&&ae<97););return us(t,ds()+(e<6&&sn()==32&&lt()==32))}function gc(t){for(;lt();)switch(ae){case t:return Je;case 34:case 39:t!==34&&t!==39&&gc(ae);break;case 40:t===41&&gc(t);break;case 92:lt();break}return Je}function Ry(t,e){for(;lt()&&t+ae!==57;)if(t+ae===84&&sn()===47)break;return"/*"+us(e,Je-1)+"*"+hc(t===47?t:lt())}function Iy(t){for(;!$i(sn());)lt();return us(t,Je)}function Oy(t){return Dy(hs("",null,null,null,[""],t=Ey(t),0,[0],t))}function hs(t,e,n,r,i,o,s,l,c){for(var d=0,h=0,p=s,f=0,v=0,x=0,b=1,M=1,w=1,g=0,y="",S=i,T=o,C=r,k=y;M;)switch(x=g,g=lt()){case 40:if(x!=108&&de(k,p-1)==58){as(k+=z(fc(g),"&","&\f"),"&\f",af(d?l[d-1]:0))!=-1&&(w=-1);break}case 34:case 39:case 91:k+=fc(g);break;case 9:case 10:case 13:case 32:k+=Py(x);break;case 92:k+=Ay(ds()-1,7);continue;case 47:switch(sn()){case 42:case 47:ki(Ly(Ry(lt(),ds()),e,n,c),c),($i(x||1)==5||$i(sn()||1)==5)&&at(k)&&Tn(k,-1,void 0)!==" "&&(k+=" ");break;default:k+="/"}break;case 123*b:l[d++]=at(k)*w;case 125*b:case 59:case 0:switch(g){case 0:case 125:M=0;case 59+h:w==-1&&(k=z(k,/\f/g,"")),v>0&&(at(k)-p||b===0&&x===47)&&ki(v>32?pf(k+";",r,n,p-1,c):pf(z(k," ","")+";",r,n,p-2,c),c);break;case 59:k+=";";default:if(ki(C=hf(k,e,n,d,h,i,l,y,S=[],T=[],p,o),o),g===123)if(h===0)hs(k,e,C,C,S,o,p,l,T);else{switch(f){case 99:if(de(k,3)===110)break;case 108:if(de(k,2)===97)break;default:h=0;case 100:case 109:case 115:}h?hs(t,C,C,r&&ki(hf(t,C,C,0,0,i,l,y,i,S=[],p,T),T),i,T,p,l,r?S:T):hs(k,C,C,C,[""],T,0,l,T)}}d=h=v=0,b=w=1,y=k="",p=s;break;case 58:p=1+at(k),v=x;default:if(b<1){if(g==123)--b;else if(g==125&&b++==0&&jy()==125)continue}switch(k+=hc(g),g*b){case 38:w=h>0?1:(k+="\f",-1);break;case 44:l[d++]=(at(k)-1)*w,w=1;break;case 64:sn()===45&&(k+=fc(lt())),f=sn(),h=p=at(y=k+=Iy(ds())),g++;break;case 45:x===45&&at(k)==2&&(b=0)}}return o}function hf(t,e,n,r,i,o,s,l,c,d,h,p){for(var f=i-1,v=i===0?o:[""],x=cf(v),b=0,M=0,w=0;b<r;++b)for(var g=0,y=Tn(t,f+1,f=af(M=s[b])),S=t;g<x;++g)(S=lf(M>0?v[g]+" "+y:z(y,/&\f/g,v[g])))&&(c[w++]=S);return cs(t,e,n,i===0?ss:l,c,d,h,p)}function Ly(t,e,n,r){return cs(t,e,n,of,hc(My()),Tn(t,2,-2),0,r)}function pf(t,e,n,r,i){return cs(t,e,n,uc,Tn(t,0,r),Tn(t,r+1,-1),r,i)}function ff(t,e,n){switch(Cy(t,e)){case 5103:return _+"print-"+t+t;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return _+t+t;case 4855:return _+t.replace("add","source-over").replace("substract","source-out").replace("intersect","source-in").replace("exclude","xor")+t;case 4789:return Si+t+t;case 5349:case 4246:case 4810:case 6968:case 2756:return _+t+Si+t+J+t+t;case 5936:switch(de(t,e+11)){case 114:return _+t+J+z(t,/[svh]\w+-[tblr]{2}/,"tb")+t;case 108:return _+t+J+z(t,/[svh]\w+-[tblr]{2}/,"tb-rl")+t;case 45:return _+t+J+z(t,/[svh]\w+-[tblr]{2}/,"lr")+t}case 6828:case 4268:case 2903:return _+t+J+t+t;case 6165:return _+t+J+"flex-"+t+t;case 5187:return _+t+z(t,/(\w+).+(:[^]+)/,_+"box-$1$2"+J+"flex-$1$2")+t;case 5443:return _+t+J+"flex-item-"+z(t,/flex-|-self/g,"")+(Dt(t,/flex-|baseline/)?"":J+"grid-row-"+z(t,/flex-|-self/g,""))+t;case 4675:return _+t+J+"flex-line-pack"+z(t,/align-content|flex-|-self/g,"")+t;case 5548:return _+t+J+z(t,"shrink","negative")+t;case 5292:return _+t+J+z(t,"basis","preferred-size")+t;case 6060:return _+"box-"+z(t,"-grow","")+_+t+J+z(t,"grow","positive")+t;case 4554:return _+z(t,/([^-])(transform)/g,"$1"+_+"$2")+t;case 6187:return z(z(z(t,/(zoom-|grab)/,_+"$1"),/(image-set)/,_+"$1"),t,"")+t;case 5495:case 3959:return z(t,/(image-set\([^]*)/,_+"$1$`$1");case 4968:return z(z(t,/(.+:)(flex-)?(.*)/,_+"box-pack:$3"+J+"flex-pack:$3"),/space-between/,"justify")+_+t+t;case 4200:if(!Dt(t,/flex-|baseline/))return J+"grid-column-align"+Tn(t,e)+t;break;case 2592:case 3360:return J+z(t,"template-","")+t;case 4384:case 3616:return n&&n.some(function(r,i){return e=i,Dt(r.props,/grid-\w+-end/)})?~as(t+(n=n[e].value),"span",0)?t:J+z(t,"-start","")+t+J+"grid-row-span:"+(~as(n,"span",0)?Dt(n,/\d+/):+Dt(n,/\d+/)-+Dt(t,/\d+/))+";":J+z(t,"-start","")+t;case 4896:case 4128:return n&&n.some(function(r){return Dt(r.props,/grid-\w+-start/)})?t:J+z(z(t,"-end","-span"),"span ","")+t;case 4095:case 3583:case 4068:case 2532:return z(t,/(.+)-inline(.+)/,_+"$1$2")+t;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(at(t)-1-e>6)switch(de(t,e+1)){case 109:if(de(t,e+4)!==45)break;case 102:return z(t,/(.+:)(.+)-([^]+)/,"$1"+_+"$2-$3$1"+Si+(de(t,e+3)==108?"$3":"$2-$3"))+t;case 115:return~as(t,"stretch",0)?ff(z(t,"stretch","fill-available"),e,n)+t:t}break;case 5152:case 5920:return z(t,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(r,i,o,s,l,c,d){return J+i+":"+o+d+(s?J+i+"-span:"+(l?c:+c-+o)+d:"")+t});case 4949:if(de(t,e+6)===121)return z(t,":",":"+_)+t;break;case 6444:switch(de(t,de(t,14)===45?18:11)){case 120:return z(t,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,"$1"+_+(de(t,14)===45?"inline-":"")+"box$3$1"+_+"$2$3$1"+J+"$2box$3")+t;case 100:return z(t,":",":"+J)+t}break;case 5719:case 2647:case 2135:case 3927:case 2391:return z(t,"scroll-","scroll-snap-")+t}return t}function ps(t,e){for(var n="",r=0;r<t.length;r++)n+=e(t[r],r,t,e)||"";return n}function zy(t,e,n,r){switch(t.type){case $y:if(t.children.length)break;case Sy:case ky:case uc:return t.return=t.return||t.value;case of:return"";case sf:return t.return=t.value+"{"+ps(t.children,r)+"}";case ss:if(!at(t.value=t.props.join(",")))return""}return at(n=ps(t.children,r))?t.return=t.value+"{"+n+"}":""}function By(t){var e=cf(t);return function(n,r,i,o){for(var s="",l=0;l<e;l++)s+=t[l](n,r,i,o)||"";return s}}function Ny(t){return function(e){e.root||(e=e.return)&&t(e)}}function Fy(t,e,n,r){if(t.length>-1&&!t.return)switch(t.type){case uc:t.return=ff(t.value,t.length,n);return;case sf:return ps([on(t,{value:z(t.value,"@","@"+_)})],r);case ss:if(t.length)return Ty(n=t.props,function(i){switch(Dt(i,r=/(::plac\w+|:read-\w+)/)){case":read-only":case":read-write":xr(on(t,{props:[z(i,/:(read-\w+)/,":"+Si+"$1")]})),xr(on(t,{props:[i]})),pc(t,{props:df(n,r)});break;case"::placeholder":xr(on(t,{props:[z(i,/:(plac\w+)/,":"+_+"input-$1")]})),xr(on(t,{props:[z(i,/:(plac\w+)/,":"+Si+"$1")]})),xr(on(t,{props:[z(i,/:(plac\w+)/,J+"input-$1")]})),xr(on(t,{props:[i]})),pc(t,{props:df(n,r)});break}return""})}}var _y={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},He={},yr=typeof process<"u"&&He!==void 0&&(He.REACT_APP_SC_ATTR||He.SC_ATTR)||"data-styled",gf="active",mf="data-styled-version",fs="6.3.8",mc=`/*!sc*/
`,gs=typeof window<"u"&&typeof document<"u",wr=ie.createContext===void 0,Hy=!!(typeof SC_DISABLE_SPEEDY=="boolean"?SC_DISABLE_SPEEDY:typeof process<"u"&&He!==void 0&&He.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&He.REACT_APP_SC_DISABLE_SPEEDY!==""?He.REACT_APP_SC_DISABLE_SPEEDY!=="false"&&He.REACT_APP_SC_DISABLE_SPEEDY:typeof process<"u"&&He!==void 0&&He.SC_DISABLE_SPEEDY!==void 0&&He.SC_DISABLE_SPEEDY!==""&&He.SC_DISABLE_SPEEDY!=="false"&&He.SC_DISABLE_SPEEDY),ms=Object.freeze([]),vr=Object.freeze({});function Wy(t,e,n){return n===void 0&&(n=vr),t.theme!==n.theme&&t.theme||e||n.theme}var xf=new Set(["a","abbr","address","area","article","aside","audio","b","bdi","bdo","blockquote","body","button","br","canvas","caption","cite","code","col","colgroup","data","datalist","dd","del","details","dfn","dialog","div","dl","dt","em","embed","fieldset","figcaption","figure","footer","form","h1","h2","h3","h4","h5","h6","header","hgroup","hr","html","i","iframe","img","input","ins","kbd","label","legend","li","main","map","mark","menu","meter","nav","object","ol","optgroup","option","output","p","picture","pre","progress","q","rp","rt","ruby","s","samp","search","section","select","slot","small","span","strong","sub","summary","sup","table","tbody","td","template","textarea","tfoot","th","thead","time","tr","u","ul","var","video","wbr","circle","clipPath","defs","ellipse","feBlend","feColorMatrix","feComponentTransfer","feComposite","feConvolveMatrix","feDiffuseLighting","feDisplacementMap","feDistantLight","feDropShadow","feFlood","feFuncA","feFuncB","feFuncG","feFuncR","feGaussianBlur","feImage","feMerge","feMergeNode","feMorphology","feOffset","fePointLight","feSpecularLighting","feSpotLight","feTile","feTurbulence","filter","foreignObject","g","image","line","linearGradient","marker","mask","path","pattern","polygon","polyline","radialGradient","rect","stop","svg","switch","symbol","text","textPath","tspan","use"]),Uy=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,Gy=/(^-|-$)/g;function yf(t){return t.replace(Uy,"-").replace(Gy,"")}var Vy=/(a)(d)/gi,wf=function(t){return String.fromCharCode(t+(t>25?39:97))};function xc(t){var e,n="";for(e=Math.abs(t);e>52;e=e/52|0)n=wf(e%52)+n;return(wf(e%52)+n).replace(Vy,"$1-$2")}var yc,br=function(t,e){for(var n=e.length;n;)t=33*t^e.charCodeAt(--n);return t},vf=function(t){return br(5381,t)};function Yy(t){return xc(vf(t)>>>0)}function Ky(t){return t.displayName||t.name||"Component"}function wc(t){return typeof t=="string"&&!0}var bf=typeof Symbol=="function"&&Symbol.for,Sf=bf?Symbol.for("react.memo"):60115,Qy=bf?Symbol.for("react.forward_ref"):60112,Xy={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},Jy={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},kf={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},qy=((yc={})[Qy]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},yc[Sf]=kf,yc);function $f(t){return("type"in(e=t)&&e.type.$$typeof)===Sf?kf:"$$typeof"in t?qy[t.$$typeof]:Xy;var e}var Zy=Object.defineProperty,tw=Object.getOwnPropertyNames,Cf=Object.getOwnPropertySymbols,nw=Object.getOwnPropertyDescriptor,rw=Object.getPrototypeOf,Tf=Object.prototype;function Mf(t,e,n){if(typeof e!="string"){if(Tf){var r=rw(e);r&&r!==Tf&&Mf(t,r,n)}var i=tw(e);Cf&&(i=i.concat(Cf(e)));for(var o=$f(t),s=$f(e),l=0;l<i.length;++l){var c=i[l];if(!(c in Jy||n&&n[c]||s&&c in s||o&&c in o)){var d=nw(e,c);try{Zy(t,c,d)}catch{}}}}return t}function Sr(t){return typeof t=="function"}function vc(t){return typeof t=="object"&&"styledComponentId"in t}function Mn(t,e){return t&&e?"".concat(t," ").concat(e):t||e||""}function jf(t,e){if(t.length===0)return"";for(var n=t[0],r=1;r<t.length;r++)n+=t[r];return n}function Ci(t){return t!==null&&typeof t=="object"&&t.constructor.name===Object.name&&!("props"in t&&t.$$typeof)}function bc(t,e,n){if(n===void 0&&(n=!1),!n&&!Ci(t)&&!Array.isArray(t))return e;if(Array.isArray(e))for(var r=0;r<e.length;r++)t[r]=bc(t[r],e[r]);else if(Ci(e))for(var r in e)t[r]=bc(t[r],e[r]);return t}function Sc(t,e){Object.defineProperty(t,"toString",{value:e})}function Ti(t){for(var e=[],n=1;n<arguments.length;n++)e[n-1]=arguments[n];return new Error("An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#".concat(t," for more information.").concat(e.length>0?" Args: ".concat(e.join(", ")):""))}var iw=function(){function t(e){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=e}return t.prototype.indexOfGroup=function(e){for(var n=0,r=0;r<e;r++)n+=this.groupSizes[r];return n},t.prototype.insertRules=function(e,n){if(e>=this.groupSizes.length){for(var r=this.groupSizes,i=r.length,o=i;e>=o;)if((o<<=1)<0)throw Ti(16,"".concat(e));this.groupSizes=new Uint32Array(o),this.groupSizes.set(r),this.length=o;for(var s=i;s<o;s++)this.groupSizes[s]=0}for(var l=this.indexOfGroup(e+1),c=(s=0,n.length);s<c;s++)this.tag.insertRule(l,n[s])&&(this.groupSizes[e]++,l++)},t.prototype.clearGroup=function(e){if(e<this.length){var n=this.groupSizes[e],r=this.indexOfGroup(e),i=r+n;this.groupSizes[e]=0;for(var o=r;o<i;o++)this.tag.deleteRule(r)}},t.prototype.getGroup=function(e){var n="";if(e>=this.length||this.groupSizes[e]===0)return n;for(var r=this.groupSizes[e],i=this.indexOfGroup(e),o=i+r,s=i;s<o;s++)n+="".concat(this.tag.getRule(s)).concat(mc);return n},t}(),xs=new Map,ys=new Map,ws=1,Mi=function(t){if(xs.has(t))return xs.get(t);for(;ys.has(ws);)ws++;var e=ws++;return xs.set(t,e),ys.set(e,t),e},ow=function(t,e){ws=e+1,xs.set(t,e),ys.set(e,t)},sw="style[".concat(yr,"][").concat(mf,'="').concat(fs,'"]'),aw=new RegExp("^".concat(yr,'\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)')),lw=function(t,e,n){for(var r,i=n.split(","),o=0,s=i.length;o<s;o++)(r=i[o])&&t.registerName(e,r)},cw=function(t,e){for(var n,r=((n=e.textContent)!==null&&n!==void 0?n:"").split(mc),i=[],o=0,s=r.length;o<s;o++){var l=r[o].trim();if(l){var c=l.match(aw);if(c){var d=0|parseInt(c[1],10),h=c[2];d!==0&&(ow(h,d),lw(t,h,c[3]),t.getTag().insertRules(d,i)),i.length=0}else i.push(l)}}},Ef=function(t){for(var e=document.querySelectorAll(sw),n=0,r=e.length;n<r;n++){var i=e[n];i&&i.getAttribute(yr)!==gf&&(cw(t,i),i.parentNode&&i.parentNode.removeChild(i))}};function dw(){return typeof __webpack_nonce__<"u"?__webpack_nonce__:null}var Df=function(t){var e=document.head,n=t||e,r=document.createElement("style"),i=function(l){var c=Array.from(l.querySelectorAll("style[".concat(yr,"]")));return c[c.length-1]}(n),o=i!==void 0?i.nextSibling:null;r.setAttribute(yr,gf),r.setAttribute(mf,fs);var s=dw();return s&&r.setAttribute("nonce",s),n.insertBefore(r,o),r},uw=function(){function t(e){this.element=Df(e),this.element.appendChild(document.createTextNode("")),this.sheet=function(n){if(n.sheet)return n.sheet;for(var r=document.styleSheets,i=0,o=r.length;i<o;i++){var s=r[i];if(s.ownerNode===n)return s}throw Ti(17)}(this.element),this.length=0}return t.prototype.insertRule=function(e,n){try{return this.sheet.insertRule(n,e),this.length++,!0}catch{return!1}},t.prototype.deleteRule=function(e){this.sheet.deleteRule(e),this.length--},t.prototype.getRule=function(e){var n=this.sheet.cssRules[e];return n&&n.cssText?n.cssText:""},t}(),hw=function(){function t(e){this.element=Df(e),this.nodes=this.element.childNodes,this.length=0}return t.prototype.insertRule=function(e,n){if(e<=this.length&&e>=0){var r=document.createTextNode(n);return this.element.insertBefore(r,this.nodes[e]||null),this.length++,!0}return!1},t.prototype.deleteRule=function(e){this.element.removeChild(this.nodes[e]),this.length--},t.prototype.getRule=function(e){return e<this.length?this.nodes[e].textContent:""},t}(),pw=function(){function t(e){this.rules=[],this.length=0}return t.prototype.insertRule=function(e,n){return e<=this.length&&(this.rules.splice(e,0,n),this.length++,!0)},t.prototype.deleteRule=function(e){this.rules.splice(e,1),this.length--},t.prototype.getRule=function(e){return e<this.length?this.rules[e]:""},t}(),Pf=gs,fw={isServer:!gs,useCSSOMInjection:!Hy},Af=function(){function t(e,n,r){e===void 0&&(e=vr),n===void 0&&(n={});var i=this;this.options=Ae(Ae({},fw),e),this.gs=n,this.names=new Map(r),this.server=!!e.isServer,!this.server&&gs&&Pf&&(Pf=!1,Ef(this)),Sc(this,function(){return function(o){for(var s=o.getTag(),l=s.length,c="",d=function(p){var f=function(w){return ys.get(w)}(p);if(f===void 0)return"continue";var v=o.names.get(f),x=s.getGroup(p);if(v===void 0||!v.size||x.length===0)return"continue";var b="".concat(yr,".g").concat(p,'[id="').concat(f,'"]'),M="";v!==void 0&&v.forEach(function(w){w.length>0&&(M+="".concat(w,","))}),c+="".concat(x).concat(b,'{content:"').concat(M,'"}').concat(mc)},h=0;h<l;h++)d(h);return c}(i)})}return t.registerId=function(e){return Mi(e)},t.prototype.rehydrate=function(){!this.server&&gs&&Ef(this)},t.prototype.reconstructWithOptions=function(e,n){return n===void 0&&(n=!0),new t(Ae(Ae({},this.options),e),this.gs,n&&this.names||void 0)},t.prototype.allocateGSInstance=function(e){return this.gs[e]=(this.gs[e]||0)+1},t.prototype.getTag=function(){return this.tag||(this.tag=(e=function(n){var r=n.useCSSOMInjection,i=n.target;return n.isServer?new pw(i):r?new uw(i):new hw(i)}(this.options),new iw(e)));var e},t.prototype.hasNameForId=function(e,n){return this.names.has(e)&&this.names.get(e).has(n)},t.prototype.registerName=function(e,n){if(Mi(e),this.names.has(e))this.names.get(e).add(n);else{var r=new Set;r.add(n),this.names.set(e,r)}},t.prototype.insertRules=function(e,n,r){this.registerName(e,n),this.getTag().insertRules(Mi(e),r)},t.prototype.clearNames=function(e){this.names.has(e)&&this.names.get(e).clear()},t.prototype.clearRules=function(e){this.getTag().clearGroup(Mi(e)),this.clearNames(e)},t.prototype.clearTag=function(){this.tag=void 0},t}(),gw=/&/g,kr=47;function Rf(t){if(t.indexOf("}")===-1)return!1;for(var e=t.length,n=0,r=0,i=!1,o=0;o<e;o++){var s=t.charCodeAt(o);if(r!==0||i||s!==kr||t.charCodeAt(o+1)!==42)if(i)s===42&&t.charCodeAt(o+1)===kr&&(i=!1,o++);else if(s!==34&&s!==39||o!==0&&t.charCodeAt(o-1)===92){if(r===0){if(s===123)n++;else if(s===125&&--n<0)return!0}}else r===0?r=s:r===s&&(r=0);else i=!0,o++}return n!==0||r!==0}function If(t,e){return t.map(function(n){return n.type==="rule"&&(n.value="".concat(e," ").concat(n.value),n.value=n.value.replaceAll(",",",".concat(e," ")),n.props=n.props.map(function(r){return"".concat(e," ").concat(r)})),Array.isArray(n.children)&&n.type!=="@keyframes"&&(n.children=If(n.children,e)),n})}function mw(t){var e,n,r,i=vr,o=i.options,s=o===void 0?vr:o,l=i.plugins,c=l===void 0?ms:l,d=function(f,v,x){return x.startsWith(n)&&x.endsWith(n)&&x.replaceAll(n,"").length>0?".".concat(e):f},h=c.slice();h.push(function(f){f.type===ss&&f.value.includes("&")&&(f.props[0]=f.props[0].replace(gw,n).replace(r,d))}),s.prefix&&h.push(Fy),h.push(zy);var p=function(f,v,x,b){v===void 0&&(v=""),x===void 0&&(x=""),b===void 0&&(b="&"),e=b,n=v,r=new RegExp("\\".concat(n,"\\b"),"g");var M=function(y){if(!Rf(y))return y;for(var S=y.length,T="",C=0,k=0,$=0,D=!1,E=0;E<S;E++){var H=y.charCodeAt(E);if($!==0||D||H!==kr||y.charCodeAt(E+1)!==42)if(D)H===42&&y.charCodeAt(E+1)===kr&&(D=!1,E++);else if(H!==34&&H!==39||E!==0&&y.charCodeAt(E-1)===92){if($===0)if(H===123)k++;else if(H===125){if(--k<0){for(var K=E+1;K<S;){var he=y.charCodeAt(K);if(he===59||he===10)break;K++}K<S&&y.charCodeAt(K)===59&&K++,k=0,E=K-1,C=K;continue}k===0&&(T+=y.substring(C,E+1),C=E+1)}else H===59&&k===0&&(T+=y.substring(C,E+1),C=E+1)}else $===0?$=H:$===H&&($=0);else D=!0,E++}if(C<S){var ut=y.substring(C);Rf(ut)||(T+=ut)}return T}(function(y){if(y.indexOf("//")===-1)return y;for(var S=y.length,T=[],C=0,k=0,$=0,D=0;k<S;){var E=y.charCodeAt(k);if(E!==34&&E!==39||k!==0&&y.charCodeAt(k-1)===92)if($===0)if(E===40&&k>=3&&(32|y.charCodeAt(k-1))==108&&(32|y.charCodeAt(k-2))==114&&(32|y.charCodeAt(k-3))==117)D=1,k++;else if(D>0)E===41?D--:E===40&&D++,k++;else if(E===kr&&k+1<S&&y.charCodeAt(k+1)===kr){for(k>C&&T.push(y.substring(C,k));k<S&&y.charCodeAt(k)!==10;)k++;C=k}else k++;else k++;else $===0?$=E:$===E&&($=0),k++}return C===0?y:(C<S&&T.push(y.substring(C)),T.join(""))}(f)),w=Oy(x||v?"".concat(x," ").concat(v," { ").concat(M," }"):M);s.namespace&&(w=If(w,s.namespace));var g=[];return ps(w,By(h.concat(Ny(function(y){return g.push(y)})))),g};return p.hash=c.length?c.reduce(function(f,v){return v.name||Ti(15),br(f,v.name)},5381).toString():"",p}var xw=new Af,kc=mw(),$c={shouldForwardProp:void 0,styleSheet:xw,stylis:kc},Of=wr?{Provider:function(t){return t.children},Consumer:function(t){return(0,t.children)($c)}}:ie.createContext($c);Of.Consumer,wr||ie.createContext(void 0);function Lf(){return wr?$c:ie.useContext(Of)}var yw=function(){function t(e,n){var r=this;this.inject=function(i,o){o===void 0&&(o=kc);var s=r.name+o.hash;i.hasNameForId(r.id,s)||i.insertRules(r.id,s,o(r.rules,s,"@keyframes"))},this.name=e,this.id="sc-keyframes-".concat(e),this.rules=n,Sc(this,function(){throw Ti(12,String(r.name))})}return t.prototype.getName=function(e){return e===void 0&&(e=kc),this.name+e.hash},t}();function ww(t,e){return e==null||typeof e=="boolean"||e===""?"":typeof e!="number"||e===0||t in _y||t.startsWith("--")?String(e).trim():"".concat(e,"px")}var vw=function(t){return t>="A"&&t<="Z"};function zf(t){for(var e="",n=0;n<t.length;n++){var r=t[n];if(n===1&&r==="-"&&t[0]==="-")return t;vw(r)?e+="-"+r.toLowerCase():e+=r}return e.startsWith("ms-")?"-"+e:e}var Bf=function(t){return t==null||t===!1||t===""},Nf=function(t){var e=[];for(var n in t){var r=t[n];t.hasOwnProperty(n)&&!Bf(r)&&(Array.isArray(r)&&r.isCss||Sr(r)?e.push("".concat(zf(n),":"),r,";"):Ci(r)?e.push.apply(e,os(os(["".concat(n," {")],Nf(r),!1),["}"],!1)):e.push("".concat(zf(n),": ").concat(ww(n,r),";")))}return e};function jn(t,e,n,r){if(Bf(t))return[];if(vc(t))return[".".concat(t.styledComponentId)];if(Sr(t)){if(!Sr(o=t)||o.prototype&&o.prototype.isReactComponent||!e)return[t];var i=t(e);return jn(i,e,n,r)}var o;return t instanceof yw?n?(t.inject(n,r),[t.getName(r)]):[t]:Ci(t)?Nf(t):Array.isArray(t)?Array.prototype.concat.apply(ms,t.map(function(s){return jn(s,e,n,r)})):[t.toString()]}function bw(t){for(var e=0;e<t.length;e+=1){var n=t[e];if(Sr(n)&&!vc(n))return!1}return!0}var Sw=vf(fs),kw=function(){function t(e,n,r){this.rules=e,this.staticRulesId="",this.isStatic=(r===void 0||r.isStatic)&&bw(e),this.componentId=n,this.baseHash=br(Sw,n),this.baseStyle=r,Af.registerId(n)}return t.prototype.generateAndInjectStyles=function(e,n,r){var i=this.baseStyle?this.baseStyle.generateAndInjectStyles(e,n,r).className:"";if(this.isStatic&&!r.hash)if(this.staticRulesId&&n.hasNameForId(this.componentId,this.staticRulesId))i=Mn(i,this.staticRulesId);else{var o=jf(jn(this.rules,e,n,r)),s=xc(br(this.baseHash,o)>>>0);if(!n.hasNameForId(this.componentId,s)){var l=r(o,".".concat(s),void 0,this.componentId);n.insertRules(this.componentId,s,l)}i=Mn(i,s),this.staticRulesId=s}else{for(var c=br(this.baseHash,r.hash),d="",h=0;h<this.rules.length;h++){var p=this.rules[h];if(typeof p=="string")d+=p;else if(p){var f=jf(jn(p,e,n,r));c=br(c,f+h),d+=f}}if(d){var v=xc(c>>>0);if(!n.hasNameForId(this.componentId,v)){var x=r(d,".".concat(v),void 0,this.componentId);n.insertRules(this.componentId,v,x)}i=Mn(i,v)}}return{className:i,css:typeof window>"u"?n.getTag().getGroup(Mi(this.componentId)):""}},t}(),Ff=wr?{Provider:function(t){return t.children},Consumer:function(t){return(0,t.children)(void 0)}}:ie.createContext(void 0);Ff.Consumer;var Cc={};function $w(t,e,n){var r=vc(t),i=t,o=!wc(t),s=e.attrs,l=s===void 0?ms:s,c=e.componentId,d=c===void 0?function(S,T){var C=typeof S!="string"?"sc":yf(S);Cc[C]=(Cc[C]||0)+1;var k="".concat(C,"-").concat(Yy(fs+C+Cc[C]));return T?"".concat(T,"-").concat(k):k}(e.displayName,e.parentComponentId):c,h=e.displayName,p=h===void 0?function(S){return wc(S)?"styled.".concat(S):"Styled(".concat(Ky(S),")")}(t):h,f=e.displayName&&e.componentId?"".concat(yf(e.displayName),"-").concat(e.componentId):e.componentId||d,v=r&&i.attrs?i.attrs.concat(l).filter(Boolean):l,x=e.shouldForwardProp;if(r&&i.shouldForwardProp){var b=i.shouldForwardProp;if(e.shouldForwardProp){var M=e.shouldForwardProp;x=function(S,T){return b(S,T)&&M(S,T)}}else x=b}var w=new kw(n,f,r?i.componentStyle:void 0);function g(S,T){return function(C,k,$){var D=C.attrs,E=C.componentStyle,H=C.defaultProps,K=C.foldedComponentIds,he=C.styledComponentId,ut=C.target,Id=wr?void 0:ie.useContext(Ff),Fi=Lf(),zn=C.shouldForwardProp||Fi.shouldForwardProp,A=Wy(k,Id,H)||vr,O=function(Xs,Pr,Js){for(var _i,zt=Ae(Ae({},Pr),{className:void 0,theme:Js}),Od=0;Od<Xs.length;Od+=1){var qs=Sr(_i=Xs[Od])?_i(zt):_i;for(var Ar in qs)Ar==="className"?zt.className=Mn(zt.className,qs[Ar]):Ar==="style"?zt.style=Ae(Ae({},zt.style),qs[Ar]):zt[Ar]=qs[Ar]}return"className"in Pr&&typeof Pr.className=="string"&&(zt.className=Mn(zt.className,Pr.className)),zt}(D,k,A),L=O.as||ut,G={};for(var V in O)O[V]===void 0||V[0]==="$"||V==="as"||V==="theme"&&O.theme===A||(V==="forwardedAs"?G.as=O.forwardedAs:zn&&!zn(V,L)||(G[V]=O[V]));var Bn=function(Xs,Pr){var Js=Lf(),_i=Xs.generateAndInjectStyles(Pr,Js.styleSheet,Js.stylis);return _i}(E,O),ht=Bn.className,Dr=Bn.css,pt=Mn(K,he);ht&&(pt+=" "+ht),O.className&&(pt+=" "+O.className),G[wc(L)&&!xf.has(L)?"class":"className"]=pt,$&&(G.ref=$);var Nn=I.createElement(L,G);return wr&&Dr?ie.createElement(ie.Fragment,null,ie.createElement("style",{precedence:"styled-components",href:"sc-".concat(he,"-").concat(ht),children:Dr}),Nn):Nn}(y,S,T)}g.displayName=p;var y=ie.forwardRef(g);return y.attrs=v,y.componentStyle=w,y.displayName=p,y.shouldForwardProp=x,y.foldedComponentIds=r?Mn(i.foldedComponentIds,i.styledComponentId):"",y.styledComponentId=f,y.target=r?i.target:t,Object.defineProperty(y,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(S){this._foldedDefaultProps=r?function(T){for(var C=[],k=1;k<arguments.length;k++)C[k-1]=arguments[k];for(var $=0,D=C;$<D.length;$++)bc(T,D[$],!0);return T}({},i.defaultProps,S):S}}),Sc(y,function(){return".".concat(y.styledComponentId)}),o&&Mf(y,t,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),y}function _f(t,e){for(var n=[t[0]],r=0,i=e.length;r<i;r+=1)n.push(e[r],t[r+1]);return n}var Hf=function(t){return Object.assign(t,{isCss:!0})};function Pt(t){for(var e=[],n=1;n<arguments.length;n++)e[n-1]=arguments[n];if(Sr(t)||Ci(t))return Hf(jn(_f(ms,os([t],e,!0))));var r=t;return e.length===0&&r.length===1&&typeof r[0]=="string"?jn(r):Hf(jn(_f(r,e)))}function Tc(t,e,n){if(n===void 0&&(n=vr),!e)throw Ti(1,e);var r=function(i){for(var o=[],s=1;s<arguments.length;s++)o[s-1]=arguments[s];return t(e,n,Pt.apply(void 0,os([i],o,!1)))};return r.attrs=function(i){return Tc(t,e,Ae(Ae({},n),{attrs:Array.prototype.concat(n.attrs,i).filter(Boolean)}))},r.withConfig=function(i){return Tc(t,e,Ae(Ae({},n),i))},r}var Wf=function(t){return Tc($w,t)},u=Wf;xf.forEach(function(t){u[t]=Wf(t)});const m={colors:{windowBg:"#152029de",panelBg:"#04161c",panelBgGlass:"rgba(4, 22, 28, 0.22)",line:"#496791",linePrimary:"#6089c1",text:"#ffffff",textAccent:"#C6E2FF",textDim:"#7f9bb8",healthOk:"#427231",healthCrit:"#ed6738",warning:"#e1b000",warningSoft:"#e6b400",statusOk:"limegreen",statusAlert:"#e1b000",statusBad:"red",statusPending:"#00b8e6",enhText:"#d8be86",enhTitle:"#e8cf93",enhBg:"rgba(169, 128, 56, 0.30)",enhLine:"#8a6d3b",greenBg:"rgba(8, 28, 12, 0.92)",greenLine:"#3f8a3f",greenTitleBg:"#16401b",greenText:"#e6ffe6",greenLabel:"#bdf0bd",greenBtnBg:"#1b5e20",greenBtnLine:"#2e7d32",greenBtnLineHover:"#66bb6a",greenGlow:"#4caf50",custom:"#cccc00",chromeText:"#deebff",overlayBg:"black",overlayBgSoft:"rgba(0, 0, 0, 0.65)"},fonts:{body:"arial",mono:'Consolas, "Lucida Console", monospace',display:'"Orbitron", sans-serif'},radii:{modal:"0px",tooltip:"7px"},hud:{btn:"36px",btnSmall:"25px",gap:"10px",gapSmall:"5px",icon:"26px",iconSmall:"17px",glyph:"30px",glyphSmall:"20px"}},Cw=u.div`
    border: 1px solid ${m.colors.line};
    color: ${m.colors.chromeText};
    background-color: ${m.colors.windowBg};
    font-family:${m.fonts.body};
`,Tw=u.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,ji=u(Cw)`
    box-shadow: 5px 5px 10px ${m.colors.overlayBg};
`,Mw=u.span`
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
`;u(Mw)`
    font-weight: normal;
`;const We=Pt`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;class Ue extends I.Component{render(){return a.jsxs(jw,{children:[a.jsx(Ew,{children:this.props.label}),a.jsx(Dw,{type:this.props.type||"text",value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:"0"})]})}}const jw=u.div`
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
`,Ew=u.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,Dw=u.input`
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
`;class Pw extends I.Component{render(){const{children:e,className:n}=this.props;return a.jsx("div",{className:n,children:e})}}const Uf=u(Pw)`
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
`,Gf=u.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,Vf=u.div`
    color: ${t=>t.$type=="good"?"#6fc126;":t.$type=="bad"?"#ff7b3f;":"white;"}
    font-weight: ${t=>t.$important?"bold":"inherit"};
    font-size: ${t=>t.$important?"14px":"12px"};
    margin-top: ${t=>t.$space?"14px":"0"};
`;class Aw extends I.Component{getOnChange(e){return n=>{this.props.set(e,n.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(e){return n=>{if(console.log("keydown"),n.preventDefault(),n.stopPropagation(),!Yf[n.keyCode])return;const r={keyCode:n.keyCode,shiftKey:n.shiftKey,altKey:n.altKey,ctrlKey:n.ctrlKey,metaKey:n.metaKey};this.props.set(e,r),this.props.save(),this.forceUpdate()}}getKey(e){return console.log(this.props.settings),Rw(this.props.settings[e])}get(e){return this.props.settings[e]}render(){return a.jsx(Iw,{onClick:this.props.close,children:a.jsxs(Ow,{onClick:e=>e.stopPropagation(),children:[a.jsxs(Lw,{children:[a.jsx(zw,{children:"Player Settings"}),a.jsx(Bw,{onClick:this.props.close,title:"Close",children:"✕"})]}),a.jsxs(Nw,{children:[a.jsx(Fw,{children:"Settings apply to this browser and device only. Reload the page for changes to take effect."}),a.jsx(vs,{children:"Keys"}),a.jsx(Ue,{label:"Display ALL Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllEW"),value:this.getKey.call(this,"ShowAllEW")}),a.jsx(Ue,{label:"Display FRIENDLY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyEW"),value:this.getKey.call(this,"ShowFriendlyEW")}),a.jsx(Ue,{label:"Display ENEMY Electronic Warfare (EW)",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyEW"),value:this.getKey.call(this,"ShowEnemyEW")}),a.jsx(Ue,{label:"Display ALL Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowAllBallistics"),value:this.getKey.call(this,"ShowAllBallistics")}),a.jsx(Ue,{label:"Display FRIENDLY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowFriendlyBallistics"),value:this.getKey.call(this,"ShowFriendlyBallistics")}),a.jsx(Ue,{label:"Display ENEMY Ballistics",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ShowEnemyBallistics"),value:this.getKey.call(this,"ShowEnemyBallistics")}),a.jsx(Ue,{label:"Toggle RULER tool",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleLoS"),value:this.getKey.call(this,"ToggleLoS")}),a.jsx(Ue,{label:"Toggle HEX numbers",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleHexNumbers"),value:this.getKey.call(this,"ToggleHexNumbers")}),a.jsx(Ue,{label:"Toggle MAP background",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleBackground"),value:this.getKey.call(this,"ToggleBackground")}),a.jsx(Ue,{label:"Close all SHIP WINDOWS and tooltips",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"CloseAllWindows"),value:this.getKey.call(this,"CloseAllWindows")}),a.jsx(vs,{children:"Replay"}),a.jsx(Ue,{label:"Play / pause Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"TogglePlayPause"),value:this.getKey.call(this,"TogglePlayPause")}),a.jsx(vs,{children:"Sound"}),a.jsx(Ue,{label:"Toggle sound in Replay",onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,"ToggleSound"),value:this.getKey.call(this,"ToggleSound")}),a.jsx(vs,{children:"Visual"}),a.jsx(Ue,{placeholder:"0",type:"number",label:"Zoom level to switch to strategic view",onChange:this.getOnChange.call(this,"ZoomLevelToStrategic"),value:this.get.call(this,"ZoomLevelToStrategic")}),a.jsx(_w,{children:"Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners."})]})]})})}}const Rw=t=>{let e=Yf[t.keyCode];return e=e.toUpperCase(),t.shiftKey&&(e+=" + shift"),t.altKey&&(e+=" + alt"),t.ctrlKey&&(e+=" + ctrl"),t.metaKey&&(e+=" + cmd"),e},Iw=u(Tw)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,Ow=u.div`
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
`,Lw=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${m.colors.windowBg};
    border-bottom: 1px solid ${m.colors.line};
`,zw=u.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,Bw=u.div`
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
`,Nw=u.div`
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
`,Fw=u.p`
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
`,_w=u.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,Yf={27:"esc",32:"space",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",58:":",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z"};class Hw extends I.Component{constructor(e){super(e),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?a.jsx(Aw,{close:this.close.bind(this),...this.props}):a.jsx(Ww,{onClick:this.open.bind(this),children:"⚙"})}}const Ww=u(ji)`
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
`,Kf="fv-thrust-relayout",an=8,bs=6,Ei=40,Mc=60,Ss=80,Uw=4,Gw=(t,e,n)=>{const r=window.coordinateConverter&&window.coordinateConverter.zoom||1,i=Math.min((t.canvasSize||200)*.75,250)/2/r+Uw,o=Math.min(i,Ss+Ei),s=d=>n.type==="roll"||!Array.isArray(e)||e[d]!==null,l=d=>s(d)?shipManager.systems.getThrusters(t,d).length:0,c=Math.max(l(1),l(2))*Ei/2;return{x:Math.min(i,Mc),y:Math.max(Math.min(i,Ss),c),clear:o}},Qf=u.span`
    font-family: ${m.fonts.mono};
    font-size: 11px;
    line-height: 1;
    padding: 1px 2px;
    color: ${t=>t.$over?m.colors.healthCrit:m.colors.text};
    background-color: rgba(0, 0, 0, 0.55);
    pointer-events: none;
`,Vw=u.div`
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
`,Xf=u.div`
    display: flex;
    position: absolute;
`,Jf=u(Xf)`
    flex-direction: row;
    left: var(--fv-ring-x, ${Mc}px);
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: ${Ei}px;
`,Yw=u(Jf)`
    left: calc(-1 * var(--fv-ring-x, ${Mc}px) - ${Ei}px);
`,qf=u(Xf)`
    flex-direction: row;
    top: calc(-1 * var(--fv-ring-y, ${Ss}px) - ${Ei}px);
    transform: translate(-50%, 0);
`,Kw=u(qf)`
    top: var(--fv-ring-y, ${Ss}px);
`,Qw=u.div`
    position: relative;
    transform: rotate(${t=>t.$rotation}deg);

    & ${Qf} {
        transform: rotate(${t=>-t.$rotation}deg);
    }
`,Xw=u.div`
    position: absolute;
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
`,Jw=u.div`
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
`,qw=u.div`
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
`,Zw=u.div`
    padding: 3px 6px 4px;
`,En=u.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px;
    padding: 1px 0;
`,Dn=u.span`
    font-size: 10px;
    color: ${m.colors.greenLabel};
    white-space: nowrap;
`,Zf=()=>window.UI&&window.UI.shipMovement&&window.UI.shipMovement.extendedTurnColour||"#ff8c00",Pn=u.span`
    font-family: ${m.fonts.mono};
    font-size: 11px;
    white-space: nowrap;
    color: ${t=>t.$state==="ok"?m.colors.statusOk:t.$state==="open"?m.colors.warning:t.$state==="owed"?Zf():m.colors.greenText};
`,jc=u.div`
    padding: 2px 0 1px;
    font-size: 10px;
    line-height: 1.25;
    color: ${m.colors.greenLabel};
`,ev=u.div`
    position: relative;
    height: 4px;
    margin: 2px 0 3px;
    background-color: rgba(0, 0, 0, 0.45);
    border: 1px solid ${m.colors.greenLine};
`,eg=u.div`
    position: absolute;
    top: 0;
    bottom: 0;
    background-color: ${t=>t.$owed?Zf():m.colors.statusOk};
`,tv=u.div`
    position: absolute;
    top: -3px;
    bottom: -3px;
    width: 1px;
    background-color: ${m.colors.greenText};
`,nv=u.div`
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
`,rv=u.span`
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
`,iv=u.div`
    height: 1px;
    margin: 3px 0;
    background-color: ${m.colors.greenLine};
`,ov=u.div`
    display: flex;
    gap: 4px;
    padding: 0 6px 6px;
`,tg=u.div`
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
`,sv=["Either","Front","Aft","Port","Stbd"],av=[1,2,3,4,0],$r=t=>t?"Starboard":"Port",lv=t=>shipManager.movement.isExtendedTurnStart(t)?"begin":shipManager.movement.isExtendedTurnCompletion(t)?"complete":"normal",cv=(t,e)=>{switch(e.type){case"extendTurnLeft":case"extendTurnRight":return`Begin Extended Turn
`+$r(e.type==="extendTurnRight");case"turnleft":case"turnright":return e.value==="turnIntoPivot"?"Turn into Pivot · "+$r(e.type==="turnright"):e.value==="extendedTurn"?`Complete Extended Turn
`+$r(e.type==="turnright"):"Turn "+$r(e.type==="turnright");case"pivotleft":case"pivotright":return"Pivot "+$r(e.type==="pivotright");case"slipleft":case"slipright":return"Slip "+$r(e.type==="slipright");case"roll":return e.value==="emergencyRoll"?"Emergency Roll":"Roll";case"speedchange":{const n=t.movement.lastIndexOf(e),r=n>0?t.movement[n-1]:null;return r&&(e.speed<r.speed||e.heading!=r.heading)?"Decelerate":"Accelerate"}case"jink":return"Jink";case"halfPhase":return"Half-Phase";case"contract":return"Contraction";default:return"Assign Thrust"}},dv=(t,e)=>{if(!Array.isArray(t)||!Array.isArray(e))return{rows:[],extra:0};const n=[1,2,3,4].every(o=>!(t[o]>0)),r=[];av.forEach(o=>{const s=t[o];if(!(s>0)||o>0&&e[o]===null)return;const l=Math.max(0,e[o]||0);r.push({slot:o,label:o===0?n?"Any":"Either":sv[o],paid:s-l,required:s,open:l})});const i=e[0]<0?-e[0]:0;return{rows:r,extra:i}},uv=t=>Array.isArray(t)&&!t.some(e=>e>0),hv=(t,e,n)=>{const r={left:e.left-n,top:e.top-n,right:e.left+n,bottom:e.top+n};return Array.from(t.children).forEach(i=>{const o=i.getBoundingClientRect();o.width===0||o.height===0||(r.left=Math.min(r.left,o.left),r.top=Math.min(r.top,o.top),r.right=Math.max(r.right,o.right),r.bottom=Math.max(r.bottom,o.bottom))}),r};class pv extends I.Component{constructor(e){super(e),this.containerRef=I.createRef(),this.ringRef=I.createRef(),this.panelRef=I.createRef(),this.layout=this.layout.bind(this)}componentDidMount(){this.layout(),window.addEventListener("resize",this.layout),this.container=this.containerRef.current,this.container&&this.container.addEventListener(Kf,this.layout)}componentDidUpdate(){this.layout()}componentWillUnmount(){window.removeEventListener("resize",this.layout),this.container&&this.container.removeEventListener(Kf,this.layout)}layout(){const e=this.containerRef.current,n=this.ringRef.current,r=this.panelRef.current;if(!e||!n||!r)return;const{ship:i,totalRequired:o,movement:s}=this.props,l=Gw(i,o,s);n.style.setProperty("--fv-ring-x",l.x+"px"),n.style.setProperty("--fv-ring-y",l.y+"px"),this.placePanel(e,n,r,l.clear)}placePanel(e,n,r,i){const o=e.getBoundingClientRect(),s=hv(n,o,i),l=window.innerWidth,c=window.innerHeight,d=r.offsetWidth,h=r.offsetHeight,p=w=>Math.min(Math.max(w,an),l-d-an),f=w=>Math.min(Math.max(w,an),c-h-an),v={left:p(o.left-d/2),top:s.bottom+bs},x=[v,{left:p(o.left-d/2),top:s.top-bs-h},{left:s.right+bs,top:f(o.top-h/2)},{left:s.left-bs-d,top:f(o.top-h/2)}],b=w=>w.left>=an&&w.top>=an&&w.left+d<=l-an&&w.top+h<=c-an,M=x.find(b)||{left:v.left,top:f(v.top)};r.style.left=Math.round(M.left-o.left)+"px",r.style.top=Math.round(M.top-o.top)+"px"}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}toggleExtendedTurn(e){e.enabled&&window.shipManager.movement.setExtendedTurn(this.props.ship,!e.checked)}render(){const{ship:e,position:n,rotation:r,totalRequired:i,remainginRequired:o,movement:s}=this.props,l=Math.round(Math.abs(r)),{rows:c,extra:d}=dv(i,o),h=lv(s),p=shipManager.movement.getExtendedTurnToggle(e,s);let f,v,x=null;h==="begin"?(x=shipManager.movement.getExtendedTurnStartPayment(e,s),f=x.valid,v=x.paid<x.minimum?`Pay at least ${x.minimum} now (${x.paid} so far)`:"Leave at least 1 thrust owed for next turn"):(f=uv(o),v="Still needed: "+c.filter(M=>M.open>0).map(M=>`${M.label} ${M.open}`).join(" · "));const b=M=>M.open===0?"ok":h==="begin"?"owed":"open";return a.jsxs(Xw,{ref:this.containerRef,onMouseOver:M=>M.preventDefault(),onContextMenu:M=>M.preventDefault(),id:"thrustUIContainer",style:{left:`${n.x}px`,top:`${n.y}px`},children:[a.jsxs(Qw,{ref:this.ringRef,style:{transform:`rotate(${l}deg)`},$rotation:l,children:[a.jsx(Jf,{children:ks(e,1,i,o,s)}),a.jsx(qf,{children:ks(e,3,i,o,s)}),a.jsx(Kw,{children:ks(e,4,i,o,s)}),a.jsx(Yw,{children:ks(e,2,i,o,s)})]}),a.jsxs(Jw,{ref:this.panelRef,children:[a.jsx(qw,{children:cv(e,s)}),a.jsxs(Zw,{children:[c.map(M=>a.jsxs(En,{children:[a.jsx(Dn,{children:M.label}),a.jsxs(Pn,{$state:b(M),children:[M.paid,"/",M.required]})]},`thrust-row-${M.slot}`)),d>0&&a.jsxs(En,{children:[a.jsx(Dn,{children:"Extra thrust"}),a.jsxs(Pn,{$state:"ok",children:["+",d]})]}),a.jsx(iv,{}),x&&gv(x),a.jsxs(En,{children:[a.jsx(Dn,{children:"Engine thrust"}),a.jsx(Pn,{children:shipManager.movement.getRemainingEngineThrust(e)})]}),fv(e,s),h==="begin"&&a.jsx(jc,{children:"Completes on next turn's movement"}),h==="complete"&&a.jsx(jc,{children:"Begun last turn"}),p&&a.jsxs(a.Fragment,{children:[a.jsxs(nv,{$locked:!p.enabled,title:p.enabled?p.checked?"Make a normal turn instead":"Pay part of this turn now and the rest next turn":p.hint,onClick:()=>this.toggleExtendedTurn(p),children:[a.jsx(rv,{$checked:p.checked,$locked:!p.enabled}),"Make Extended Turn"]}),!p.enabled&&p.hint&&a.jsx(jc,{children:p.hint})]})]}),a.jsxs(ov,{children:[a.jsx(tg,{$primary:!0,disabled:!f,title:f?"Confirm this manoeuvre":v,onClick:f?this.ready.bind(this):void 0,children:"Confirm"}),a.jsx(tg,{onClick:this.cancel.bind(this),children:"Cancel"})]})]})]})}}const fv=(t,e)=>{if(!shipManager.movement.isTurn(e))return null;const n=shipManager.movement.calculateTurndelay(t,e,e.speed);return a.jsxs(En,{children:[a.jsx(Dn,{children:"Turn delay"}),a.jsx(Pn,{children:n})]})},gv=t=>{const e=t.paid>=t.minimum,n=t.owed.any+t.owed.rear+t.owed.side,r=i=>(t.cost>0?Math.min(100,Math.max(0,i/t.cost*100)):0)+"%";return a.jsxs(a.Fragment,{children:[a.jsxs(En,{children:[a.jsx(Dn,{children:"Turn cost"}),a.jsx(Pn,{children:t.cost})]}),a.jsxs(En,{children:[a.jsxs(Dn,{children:["Paying now (min ",t.minimum,")"]}),a.jsx(Pn,{$state:e?"ok":"open",children:t.paid})]}),a.jsxs(ev,{title:`At least ${t.minimum} now, at most ${t.cost-1}`,children:[a.jsx(eg,{style:{left:0,width:r(t.paid)}}),a.jsx(eg,{$owed:!0,style:{left:r(t.paid),width:r(t.cost-t.paid)}}),a.jsx(tv,{style:{left:r(t.minimum)}})]}),a.jsxs(En,{children:[a.jsx(Dn,{children:"Thrust owed next turn"}),a.jsx(Pn,{$state:"owed",children:n})]})]})},ks=(t,e,n,r,i)=>{const o=shipManager.systems.getThrusters(t,e);if(i.type!=="roll"&&n[e]===null)return null;const s=Array.isArray(r)&&(r[e]>0||r[0]>0);return o.map((l,c)=>{const d=shipManager.systems.isDestroyed(t,l),h=d||!shipManager.movement.wouldAcceptThrust(t,l)?void 0:s?"need":"extra",p=()=>{shipManager.movement.assignThrust(t,l),shipManager.movement.updateAssignThrust(t)},f=M=>{M.preventDefault(),shipManager.movement.unAssignThrust(t,l),shipManager.movement.updateAssignThrust(t)};let v=shipManager.criticals.hasCritical(l,"HalfEfficiency")?10:0;shipManager.criticals.hasCritical(l,"FirstThrustIgnored")&&(v+=1);const x=shipManager.movement.getAmountChanneled(t,l),b=shipManager.systems.getOutput(t,l);return a.jsx(Vw,{$crits:v,$direction:e,$destroyed:d,$box:h,onClick:d?void 0:p,onContextMenu:d?M=>M.preventDefault():f,children:a.jsxs(Qf,{$over:x>b,children:[x,"/",b]})},`thruster-${e}-${c}`)})},mv=()=>a.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[a.jsx("path",{d:"M9 3H3v6"}),a.jsx("path",{d:"M15 3h6v6"}),a.jsx("path",{d:"M9 21H3v-6"}),a.jsx("path",{d:"M15 21h6v-6"})]});class xv extends I.Component{fullScreen(){var e=window.document,n=e.documentElement,r=n.requestFullscreen||n.mozRequestFullScreen||n.webkitRequestFullScreen||n.msRequestFullscreen,i=e.exitFullscreen||e.mozCancelFullScreen||e.webkitExitFullscreen||e.msExitFullscreen;!e.fullscreenElement&&!e.mozFullScreenElement&&!e.webkitFullscreenElement&&!e.msFullscreenElement?r.call(n):i.call(e)}render(){return a.jsx(yv,{onClick:this.fullScreen.bind(this),title:"Full screen",children:a.jsx(mv,{})})}}const yv=u(ji)`
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
`;class wv extends I.Component{constructor(e){super(e),this.state={available:ng()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{const e=ng();this.state.available!==e&&this.setState({available:e})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?a.jsx(bv,{onClick:this.surrender,title:"Surrender",children:a.jsx("img",{src:vv(),alt:""})}):null}}const ng=()=>{if(typeof window.gamedata>"u")return!1;const t=window.gamedata;if(t.replay||!t.isPlayerInGame()||t.status==="SURRENDERED"||t.status==="FINISHED")return!1;for(const e in t.slots){const n=t.slots[e];if(n.playerid==t.thisplayer&&n.surrendered!==null&&n.surrendered!==void 0)return!1}return!0},vv=()=>{const t="./img/surrender_icon1.png";return window.AssetManager?window.AssetManager.getSmartImagePath(t):t},rg="34px",ig="22px",bv=u(ji)`
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
        width: ${rg};
        height: ${rg};
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
            width: ${ig};
            height: ${ig};
            transform: translateY(${"-2px"});
        }
    }
`,Sv=()=>a.jsxs("svg",{viewBox:"0 0 24 24",fill:"none",stroke:"currentColor",strokeWidth:"2",strokeLinecap:"round",strokeLinejoin:"round","aria-hidden":"true",focusable:"false",children:[a.jsx("path",{d:"M3 10.5L12 3l9 7.5"}),a.jsx("path",{d:"M5 9v12h14V9"}),a.jsx("path",{d:"M10 21v-6h4v6"})]});class kv extends I.Component{render(){return a.jsx($v,{as:"a",href:"games.php",title:"Back to Lobby",children:a.jsx(Sv,{})})}}const $v=u(ji)`
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
`;class Cv extends ie.Component{constructor(e){super(e),this.state={losToggled:!1,hexToggled:!1,soundToggled:gamedata.playAudio!==!1,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this),this.setBackground=this.setBackground.bind(this),this.externalSetBackground=this.externalSetBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener("LoSToggled",this.externalToggleLoS),window.addEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.addEventListener("BackgroundToggled",this.externalToggleBackground),window.addEventListener("BackgroundPreferenceChanged",this.externalSetBackground),window.addEventListener("soundToggled",this.externalToggleSound),window.gameOptions&&gameOptions.get("hideMapBackground")&&this.setBackground(!0),this.replayCheck=setInterval(()=>{const e=this.getReplayMode();this.state.replayMode!==e&&this.setState({replayMode:e})},500)}componentWillUnmount(){window.removeEventListener("LoSToggled",this.externalToggleLoS),window.removeEventListener("HexNumbersToggled",this.externalToggleHexNumbers),window.removeEventListener("BackgroundToggled",this.externalToggleBackground),window.removeEventListener("BackgroundPreferenceChanged",this.externalSetBackground),window.removeEventListener("soundToggled",this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(e=>({hexToggled:!e.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}externalSetBackground(e){this.setBackground(!!(e.detail&&e.detail.off))}showFriendlyEW(e){webglScene.customEvent("ShowFriendlyEW",{up:e})}showEnemyEW(e){webglScene.customEvent("ShowEnemyEW",{up:e})}toggleFriendlyBallisticLines(e){if(e)return;const n=!this.state.fbToggled;this.setState({fbToggled:n}),webglScene.customEvent("ToggleFriendlyBallisticLines",{up:e})}toggleEnemyBallisticLines(e){if(e)return;const n=!this.state.ebToggled;this.setState({ebToggled:n}),webglScene.customEvent("ToggleEnemyBallisticLines",{up:e})}toggleLoS(e){if(e)return;const n=!this.state.losToggled;this.setState({losToggled:n}),webglScene.customEvent("ToggleLoS",{up:e}),window.dispatchEvent(new CustomEvent("LoSToggled"))}toggleHexNumbers(e){if(e)return;const n=!this.state.hexToggled;this.setState({hexToggled:n}),webglScene.customEvent("ToggleHexNumbers",{up:e}),window.dispatchEvent(new CustomEvent("HexNumbersToggled"))}toggleSound(){const e=!this.state.soundToggled;this.setState({soundToggled:e}),webglScene.customEvent("ToggleSound",{enabled:e})}toggleBackground(){this.setBackground(!this.state.bgToggled)}setBackground(e){const n=document.getElementById("background");if(!n||e===this.state.bgToggled)return;let r=this.state.originalBgImage;e?(r||(r=n.style.backgroundImage),n.style.backgroundImage="none",n.style.backgroundColor="black"):(n.style.backgroundImage=r||"",n.style.backgroundColor=""),this.setState({bgToggled:e,originalBgImage:r})}render(){return a.jsxs(Tv,{children:[a.jsx(jv,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),a.jsx(Mv,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),a.jsx(Dv,{$showing:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),a.jsx(Ev,{$showing:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),a.jsx(Pv,{$showing:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),a.jsx(Av,{$showing:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),a.jsx(Rv,{$showing:!this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?"Enable Background":"Disable Background"}),this.state.replayMode&&a.jsx(Iv,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?"Sound On":"Sound Off"})]})}}const Tv=u.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,Di=u(ji)`
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
`,Mv=u(Di)`
    background-image: url("./img/EEW.png");
`,jv=u(Di)`
    background-image: url("./img/FEW.png");
`,og=Pt`
    position: relative;
    border: 1px solid ${t=>t.$showing?m.colors.linePrimary:m.colors.line};
    border-right: none;
    background-image: ${t=>t.$showing?"linear-gradient(90deg, rgba(96, 137, 193, 0.32), rgba(96, 137, 193, 0.08))":"none"};
    box-shadow: ${t=>t.$showing?`inset 2px 0 0 ${m.colors.textAccent}, `:""}5px 5px 10px ${m.colors.overlayBg};
`,sg=Pt`
    opacity: ${t=>t.$showing?1:.45};
    //Only half grey: fully desaturated, the faded friendly (yellow) and enemy (orange)
    //ballistic icons become indistinguishable.
    filter: ${t=>t.$showing?"none":"grayscale(0.5)"};
    transition: opacity 0.15s ease, filter 0.15s ease;
`,ag=Pt`
    opacity: ${t=>t.$showing?1:.7};
`,$s=u(Di)`
    ${og}

    &::before {
        content: '';
        position: absolute;
        inset: 0;
        background-repeat: no-repeat;
        background-size: cover;
        ${sg}
    }
    &:hover::before {
        ${ag}
    }
`,Ev=u($s)`
    &::before { background-image: url("./img/ballisticTarget2.png"); }
`,Dv=u($s)`
    &::before { background-image: url("./img/ballisticLaunch2.png"); }
`,Pv=u($s)`
    &::before { background-image: url("./img/los1.png"); }
`,Av=u($s)`
    &::before { background-image: url("./img/hexNumber.png"); }
`,Rv=u(Di)`
    ${og}

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
        ${sg}
    }
    &:hover::after {
        ${ag}
    }
`,Iv=u(Di)`
    background-image: ${t=>t.$toggled?'url("./img/soundOn.png")':'url("./img/soundOff.png")'};
    border: 1px solid ${m.colors.line};
    border-right: none;
`,Ov=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Lv=u.div`
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
`,zv=u.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,Bv=u.div`
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
`;class Nv extends I.Component{selectMode(e,n){e.stopPropagation(),e.preventDefault();const{ship:r,system:i}=this.props;weaponManager.onSetModeClicked(r,i,n)}selectAllMode(e,n){e.stopPropagation(),e.preventDefault();const{ship:r,system:i}=this.props;weaponManager.onSetModeAllClicked(r,i,n)}render(){const{ship:e,system:n}=this.props,r=this.props.showModes!==!1,i=parseInt(n.firingMode);let o="";n.iconPath?o=`./img/systemicons/${n.iconPath}`:o=`./img/systemicons/${n.name}.png`;const s=[];for(const l in n.firingModes)if(n.firingModes.hasOwnProperty(l)){const c=parseInt(l),d=n.firingModes[l],h=c===i;let p=n.modeLetters||1;n.modeLettersArray&&n.modeLettersArray[c]&&(p=n.modeLettersArray[c]),s.push(a.jsx(Bv,{img:o,selected:h,onClick:f=>this.selectMode(f,c),onContextMenu:f=>this.selectAllMode(f,c),title:`Set mode: ${d} ${h?"(Current)":""} (Right click to set all)`,children:d.substring(0,p)},c))}return a.jsxs(Ov,{children:[r&&a.jsx(Lv,{children:"Select Firing Mode"}),a.jsxs(zv,{children:[r&&s,this.props.children]})]})}}const Fv=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,_v=u.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,Hv=u.div`
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

`,lg=u.div`
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
`,Wv=u.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,Ec=u.span`
    font-weight: bold;
`,cg=u.span`
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

`,Uv=u(Ec)`
    color: #ffb833;
    font-weight: normal;    
`,Gv=u(Ec)`
    color: #ff3333;
    font-weight: normal;    
`,Vv=u.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,Cs=u.div`
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
`,Yv=u.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,Kv=u.div`
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
`,dg=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,Qv=u(lg)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,Dc=u.span`
    color: #00b8e6;
    font-weight: normal;
    margin-right: 4px;
`,Xv=u.input`
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
`,Jv=u.span`
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
`;class qv extends I.Component{constructor(e){super(e),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getEffectiveCriticalRepairCost(e,n){return n.name==="cnC"?4:e.repairCost}getDockedUnits(){const{ship:e,system:n}=this.props;if(!n.servicesDockedUnits)return[];const r=[],i=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const o of i)if(!(!o.isDockingBay||!Array.isArray(o.shipsDocked)))for(const s of o.shipsDocked){const l=gamedata.getShip(s.shipId);l&&r.indexOf(l)===-1&&r.push(l)}return r}getDockedRepairables(){const{system:e}=this.props,n=[];for(const r of this.getDockedUnits()){const i=Array.isArray(r.systems)?r.systems:Object.values(r.systems),o="d"+r.id+":";for(const s of i){const l=s.name==="structure"||s.name==="cnC"||s.name==="SelfRepair";if(!shipManager.systems.isDestroyed(r,s)&&s.repairPriority>=1&&s.criticals){const f=Array.isArray(s.criticals)?s.criticals:Object.values(s.criticals);for(const v of f){if(v.repairPriority===0||v.turn>=gamedata.turn||v.oneturn||v.turnend>0)continue;const x=o+s.id+"-"+v.id;let b=v.repairPriority||0,M=!1;e.priorityChanges&&x in e.priorityChanges&&e.priorityChanges[x]>=0?(b=e.priorityChanges[x],M=!0):b<10&&(b+=s.repairPriority),!(b<1)&&n.push({type:"critical",sys:s,crit:v,ownerShip:r,shipId:r.id,docked:!0,priority:b,overridden:M,cost:this.getEffectiveCriticalRepairCost(v,s),id:s.id,subId:v.id,keyId:x})}}if(!l||s.repairPriority===0)continue;const c=shipManager.systems.getTotalDamage(s);if(c<=0)continue;if(s.name==="structure"){if(shipManager.systems.isDestroyed(r,s))continue}else{const f=s.structureHomeLocation!==void 0&&s.structureHomeLocation!==null?s.structureHomeLocation:s.location;if(f!=0){const v=shipManager.systems.getStructureSystem(r,f);if(v&&shipManager.systems.isDestroyed(r,v))continue}}const d=o+s.id;let h=s.repairPriority,p=!1;e.priorityChanges&&d in e.priorityChanges&&e.priorityChanges[d]>=0&&(h=e.priorityChanges[d],p=!0),!(h<1)&&(!p&&shipManager.systems.isDestroyed(r,s)&&h<=10&&(h+=10),n.push({type:"system",sys:s,ownerShip:r,shipId:r.id,docked:!0,priority:h,overridden:p,damage:c,maxHealth:s.maxhealth,id:s.id,subId:0,keyId:d}))}}return n}getRepairableSystems(){const{ship:e,system:n}=this.props,r=[],i=[],o=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const c of o){if(c.name==="SelfRepair"||c.repairPriority===0||c.privateRepairOnly&&!n.repairRestrictedTo||n.repairRestrictedTo&&!n.repairRestrictedTo.includes(c.id)||c.name=="structure"&&shipManager.systems.isDestroyed(c.ship,c))continue;const d=c.structureHomeLocation!==void 0&&c.structureHomeLocation!==null?c.structureHomeLocation:c.location;if(c.name!="structure"&&d!=0){var s=shipManager.systems.getStructureSystem(c.ship,d);if(s&&shipManager.systems.isDestroyed(c.ship,s))continue}let h=c.repairPriority,p=!1;if(n.priorityChanges&&c.id in n.priorityChanges&&n.priorityChanges[c.id]>=0&&(h=n.priorityChanges[c.id],p=!0),!p&&shipManager.systems.isDestroyed(e,c)&&h<=10&&(h+=10),!shipManager.systems.isDestroyed(e,c)&&c.criticals){const v=Array.isArray(c.criticals)?c.criticals:Object.values(c.criticals);for(const x of v){if(x.repairPriority===0||x.turn>=gamedata.turn||x.oneturn||x.turnend>0)continue;let b=x.repairPriority||0;const M=c.id+"-"+x.id;let w=!1;n.priorityChanges&&M in n.priorityChanges&&n.priorityChanges[M]>=0?(b=n.priorityChanges[M],w=!0):b<10&&(b+=c.repairPriority),i.push({type:"critical",sys:c,crit:x,ownerShip:e,shipId:0,docked:!1,priority:b,overridden:w,cost:this.getEffectiveCriticalRepairCost(x,c),id:c.id,subId:x.id,keyId:M})}}const f=shipManager.systems.getTotalDamage(c);f>0&&r.push({type:"system",sys:c,ownerShip:e,shipId:0,docked:!1,priority:h,overridden:p,damage:f,maxHealth:c.maxhealth,id:c.id,subId:0,keyId:c.id})}const l=[...i,...r,...this.getDockedRepairables()];return l.sort((c,d)=>{if(c.priority!==d.priority)return d.priority-c.priority;const h=!!c.overridden,p=!!d.overridden;if(h!==p)return h?-1:1;if(this.lastOrder&&this.lastOrder.length>0){const x=this.lastOrder.indexOf(c.keyId),b=this.lastOrder.indexOf(d.keyId);if(x!==-1&&b!==-1)return x-b}const f=c.shipId||0,v=d.shipId||0;return f!==v?f-v:c.id!==d.id?c.id-d.id:c.subId-d.subId}),this.lastOrder=l.map(c=>c.keyId),l}handleInputChange(e,n,r){const i=e.target.value;if(i===""){this.setState(s=>({priorityInputs:{...s.priorityInputs,[n]:""}}));return}const o=parseInt(i,10);isNaN(o)||(this.setState(s=>({priorityInputs:{...s.priorityInputs,[n]:o}})),this.setPriority(n,o))}handleWheel(e,n,r){e.preventDefault();const i=e.deltaY<0?1:-1,o=r+i;o<1||this.setPriority(n,o)}componentDidUpdate(e){if(this.dragRef&&this.dragRef.started&&this.listRef){const s=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');s&&this.positionDraggedEl(s,this.dragRef,this.dragRef.lastClientY)}const n=this.getRepairableSystems(),r=this.state.priorityInputs,i={};let o=!1;n.forEach(s=>{const l=s.keyId,c=s.priority;r[l]!==void 0&&r[l]!==c&&document.activeElement!==document.getElementById(`prio-input-${l}`)&&(i[l]=c,o=!0)}),o&&this.setState(s=>({priorityInputs:{...s.priorityInputs,...i}}))}handleTop(e,n){e.stopPropagation();const r=this.getRepairableSystems();if(r.length===0)return;const i=r[0].priority,o=r.find(l=>l.keyId===n);if(!o||o.priority===i)return;let s=i+1;this.setPriority(n,s)}handleUp(e,n,r){e.stopPropagation();let i=r+1;i!==r&&this.setPriority(n,i)}handleDown(e,n,r){e.stopPropagation(),!(r<=1)&&this.setPriority(n,r-1)}handleReset(e,n){e.stopPropagation(),this.setPriority(n,-1)}setPriority(e,n){const{ship:r,system:i}=this.props;i.setOverride(e,n),webglScene.customEvent("SystemDataChanged",{ship:r,system:i})}onRowPointerDown(e,n,r,i){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest("input, .sr-action-button"))return;const o=e.currentTarget,s=o?o.offsetHeight:24,l=this.listRef?this.listRef.offsetHeight:0,c=o?o.offsetWidth:0,d=o?e.clientY-o.getBoundingClientRect().top:0;this.dragRef={keyId:n,pointerId:e.pointerId,startY:e.clientY,startIdx:r,order:i,gapSize:s,lockHeight:l,anchorWidth:c,grabOffsetInRow:d,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(e){const n=this.dragRef;if(!(!n||e.pointerId!==n.pointerId)){if(!n.started){if(Math.abs(e.clientY-n.startY)<4)return;n.started=!0}e.preventDefault(),n.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){const n=this.dragRef;if(!n||!this.listRef)return;const r=this.listRef.querySelectorAll("[data-keyid]"),i=this.listRef.getBoundingClientRect(),o=e-i.top+this.listRef.scrollTop,l=this.state.drag&&this.state.drag.dropIdx===0?n.gapSize:0;let c=0,d=null;for(let p=0;p<r.length;p++){if(r[p].getAttribute("data-keyid")===String(n.keyId)){d=r[p];continue}const f=r[p].offsetTop-l+r[p].offsetHeight/2;if(o<f)break;c++}d&&this.positionDraggedEl(d,n,e);const h=this.state.drag;(!h||h.keyId!==n.keyId||h.dropIdx!==c)&&this.setState({drag:{keyId:n.keyId,startIdx:n.startIdx,dropIdx:c,gapSize:n.gapSize,lockHeight:n.lockHeight}})}positionDraggedEl(e,n,r){const i=this.listRef.getBoundingClientRect();let o=r-i.top+this.listRef.scrollTop-n.grabOffsetInRow;const s=Math.max(0,this.listRef.scrollHeight-n.gapSize);o<0?o=0:o>s&&(o=s),e.style.top=o+"px",e.style.left="0px",e.style.width=n.anchorWidth+"px",e.style.transform="none"}updateAutoScroll(e){const n=this.listRef;if(!n){this.stopAutoScroll();return}const r=30,i=n.getBoundingClientRect(),o=n.scrollTop>0,s=n.scrollTop<n.scrollHeight-n.clientHeight-1,l=e-i.top,c=i.bottom-e;o&&l<r?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,l)/r),this.ensureAutoScrollRunning()):s&&c<r?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,c)/r),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const e=this.listRef,n=this.dragRef;if(!e||!n||this.autoScrollDir===0)return;const r=e.scrollTop;if(e.scrollTop=r+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===r){this.stopAutoScroll();return}this.updateDragForPointer(n.lastClientY),this.updateAutoScroll(n.lastClientY)}onDragEnd(e){const n=this.dragRef;if(!n||e&&e.pointerId!=null&&e.pointerId!==n.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const i=this.listRef.querySelector('[data-keyid="'+n.keyId+'"]');i&&(i.style.transform="",i.style.top="",i.style.left="",i.style.width="")}this.dragRef=null;const r=this.state.drag;this.setState({drag:null}),!(!n.started||!r)&&r.dropIdx!==n.startIdx&&this.applyDropReorder(n.order,n.keyId,r.dropIdx)}applyDropReorder(e,n,r){const{ship:i,system:o}=this.props,s=e.findIndex(f=>f.keyId===n);if(s===-1)return;const l=e.slice(),[c]=l.splice(s,1);l.splice(r,0,c);const d=l[r+1],h=l[r-1];let p;d?p=d.priority+1:h?p=Math.max(1,h.priority-1):p=1,o.setOverride(n,p);for(let f=r-1;f>=0&&!(l[f].priority>p);f--)p+=1,o.setOverride(l[f].keyId,p);webglScene.customEvent("SystemDataChanged",{ship:i,system:o})}handlePropagate(e){e.stopPropagation();const{ship:n,system:r}=this.props;for(const i of n.systems)if(i.name==="SelfRepair"&&i.id!==r.id){if(i.priorityChanges)for(const o in i.priorityChanges)(!r.priorityChanges||!(o in r.priorityChanges))&&i.setOverride(o,-1);if(r.priorityChanges)for(const o in r.priorityChanges){const s=r.priorityChanges[o];s>=0&&i.setOverride(o,s)}}webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,readOnly:n}=this.props,r=this.getRepairableSystems();let i=0;const o=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(const s of o)s.name==="SelfRepair"&&i++;return a.jsxs(Fv,{children:[a.jsx(_v,{children:n?"Repair Queue (view only)":"Manage Repair Queue"}),a.jsxs(Hv,{ref:s=>{this.listRef=s},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[r.length===0&&a.jsx(Qv,{children:"No damaged systems"}),r.map((s,l)=>{const c=this.state.drag,d=c&&c.keyId===s.keyId;let h=!1,p=!1,f=!1;if(c&&!d){const x=r.length-1,b=l>c.startIdx?l-1:l;c.dropIdx===0&&b===0?h=!0:c.dropIdx===x&&b===x-1?p=!0:c.dropIdx===b&&(f=!0)}const v=s.ownerShip||e;return a.jsxs(lg,{"data-keyid":s.keyId,$dragging:d,$gapBefore:h,$lineAtEnd:p,$lineBefore:f,$gapSize:c?c.gapSize:0,$readOnly:n,onPointerDown:x=>this.onRowPointerDown(x,s.keyId,l,r),children:[a.jsx(Wv,{children:s.type==="critical"?a.jsxs(a.Fragment,{children:[a.jsxs(Uv,{children:[s.docked&&a.jsxs(Dc,{children:[v.name,":"]}),s.sys.displayName," (",s.crit.description||s.crit.phpclass,")"]}),a.jsxs(cg,{children:["Cost: ",s.cost," ",a.jsx(dg,{})," Id: ",s.sys.id]})]}):a.jsxs(a.Fragment,{children:[shipManager.systems.isDestroyed(v,s.sys)?a.jsxs(Gv,{children:[s.docked&&a.jsxs(Dc,{children:[v.name,":"]}),s.sys.displayName]}):a.jsxs(Ec,{children:[s.docked&&a.jsxs(Dc,{children:[v.name,":"]}),s.sys.displayName]}),a.jsxs(cg,{children:["HP: ",shipManager.systems.getRemainingHealth(s.sys)," / ",s.sys.maxhealth," ",a.jsx(dg,{})," Id: ",s.sys.id]})]})}),a.jsx(Vv,{children:n?a.jsx(Jv,{title:"Priority (view only)",children:s.priority}):a.jsxs(a.Fragment,{children:[a.jsx(Cs,{className:"sr-action-button",title:"Reset Default",onClick:x=>this.handleReset(x,s.keyId),img:"./img/iconSRCancel.png"}),a.jsx(Cs,{className:"sr-action-button",title:"Decrease Priority",onClick:x=>this.handleDown(x,s.keyId,s.priority),img:"./img/systemicons/AAclasses/iconMinus.png"}),a.jsx(Xv,{id:`prio-input-${s.keyId}`,type:"number",value:this.state.priorityInputs[s.keyId]!==void 0?this.state.priorityInputs[s.keyId]:s.priority,onChange:x=>this.handleInputChange(x,s.keyId,s.priority),onClick:x=>x.stopPropagation(),onWheel:x=>this.handleWheel(x,s.keyId,s.priority)}),a.jsx(Cs,{className:"sr-action-button",title:"Increase Priority",onClick:x=>this.handleUp(x,s.keyId,s.priority),img:"./img/systemicons/AAclasses/iconPlus.png"}),a.jsx(Cs,{className:"sr-action-button",title:"Move to Top",onClick:x=>this.handleTop(x,s.keyId),img:"./img/iconSRHigh.png"})]})})]},s.keyId)})]}),!n&&i>1&&a.jsx(Yv,{children:a.jsx(Kv,{onClick:s=>this.handlePropagate(s),children:"Set all Self Repair systems"})})]})}}const Zv=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
`,e1=u.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,t1=u.div`
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
`,ug=u.div`
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
`,n1=u(ug)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,r1=u.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,hg=u.span`
    font-weight: bold;
`,i1=u(hg)`
    color: #ff3333;
    font-weight: normal;
`,o1=u.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,s1=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,a1=u.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,l1=u.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,c1=u.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`;class d1 extends I.Component{constructor(e){super(e),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener("pointermove",this.onDragMove),window.removeEventListener("pointerup",this.onDragEnd),window.removeEventListener("pointercancel",this.onDragEnd)}getOrderedBlocks(){const{ship:e,system:n}=this.props,r=(n.structureBlocks||[]).map(l=>{const c=(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).find(f=>f.id===l.id),d=c?shipManager.systems.getRemainingHealth(c):l.maxhealth,h=c?c.maxhealth:l.maxhealth||0,p=c?shipManager.systems.isDestroyed(e,c):!1;return{id:l.id,displayName:l.displayName,hp:d,maxhealth:h,destroyed:p}}),i=n.repairOrder||[];if(i.length===0)return r.slice().sort((l,c)=>{const d=c.destroyed?1:0,h=l.destroyed?1:0;return d!==h?d-h:c.maxhealth-c.hp-(l.maxhealth-l.hp)});const o=[],s=new Set;for(const l of i){const c=r.find(d=>d.id===l);c&&(o.push(c),s.add(l))}for(const l of r)s.has(l.id)||o.push(l);return o}onRowPointerDown(e,n,r,i){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest(".ssr-action-button"))return;const o=e.currentTarget,s=o?o.offsetHeight:24,l=this.listRef?this.listRef.offsetHeight:0,c=o?o.offsetWidth:0,d=o?e.clientY-o.getBoundingClientRect().top:0;this.dragRef={keyId:n,pointerId:e.pointerId,startY:e.clientY,startIdx:r,order:i,gapSize:s,lockHeight:l,anchorWidth:c,grabOffsetInRow:d,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener("pointermove",this.onDragMove),window.addEventListener("pointerup",this.onDragEnd),window.addEventListener("pointercancel",this.onDragEnd)}onDragMove(e){const n=this.dragRef;if(!(!n||e.pointerId!==n.pointerId)){if(!n.started){if(Math.abs(e.clientY-n.startY)<4)return;n.started=!0}e.preventDefault(),n.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){const n=this.dragRef;if(!n||!this.listRef)return;const r=this.listRef.querySelectorAll("[data-keyid]"),i=this.listRef.getBoundingClientRect(),o=e-i.top+this.listRef.scrollTop,l=this.state.drag&&this.state.drag.dropIdx===0?n.gapSize:0;let c=0,d=null;for(let p=0;p<r.length;p++){if(r[p].getAttribute("data-keyid")===String(n.keyId)){d=r[p];continue}const f=r[p].offsetTop-l+r[p].offsetHeight/2;if(o<f)break;c++}d&&this.positionDraggedEl(d,n,e);const h=this.state.drag;(!h||h.keyId!==n.keyId||h.dropIdx!==c)&&this.setState({drag:{keyId:n.keyId,startIdx:n.startIdx,dropIdx:c,gapSize:n.gapSize,lockHeight:n.lockHeight}})}positionDraggedEl(e,n,r){const i=this.listRef.getBoundingClientRect();let o=r-i.top+this.listRef.scrollTop-n.grabOffsetInRow;const s=Math.max(0,this.listRef.scrollHeight-n.gapSize);o<0?o=0:o>s&&(o=s),e.style.top=o+"px",e.style.left="0px",e.style.width=n.anchorWidth+"px",e.style.transform="none"}updateAutoScroll(e){const n=this.listRef;if(!n){this.stopAutoScroll();return}const r=30,i=n.getBoundingClientRect(),o=n.scrollTop>0,s=n.scrollTop<n.scrollHeight-n.clientHeight-1,l=e-i.top,c=i.bottom-e;o&&l<r?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,l)/r),this.ensureAutoScrollRunning()):s&&c<r?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,c)/r),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF==null&&(this.autoScrollRAF=requestAnimationFrame(this.autoScrollTick))}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;const e=this.listRef,n=this.dragRef;if(!e||!n||this.autoScrollDir===0)return;const r=e.scrollTop;if(e.scrollTop=r+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===r){this.stopAutoScroll();return}this.updateDragForPointer(n.lastClientY),this.updateAutoScroll(n.lastClientY)}onDragEnd(e){const n=this.dragRef;if(!n||e&&e.pointerId!=null&&e.pointerId!==n.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){const i=this.listRef.querySelector('[data-keyid="'+n.keyId+'"]');i&&(i.style.transform="",i.style.top="",i.style.left="",i.style.width="")}this.dragRef=null;const r=this.state.drag;this.setState({drag:null}),!(!n.started||!r)&&r.dropIdx!==n.startIdx&&this.applyDropReorder(n.order,n.keyId,r.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){const e=this.listRef.querySelector('[data-keyid="'+this.dragRef.keyId+'"]');e&&this.positionDraggedEl(e,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(e,n,r){const{ship:i,system:o}=this.props,s=e.slice(),l=s.findIndex(h=>h.id===n);if(l===-1)return;const[c]=s.splice(l,1);s.splice(r,0,c);const d=s.map(h=>h.id);o.setRepairOrder(d),webglScene.customEvent("SystemDataChanged",{ship:i,system:o})}handleReset(e){e.stopPropagation();const{ship:n,system:r}=this.props;r.setRepairOrder([]),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,readOnly:n}=this.props,r=this.getOrderedBlocks(),i=(this.props.system.repairOrder||[]).length>0;return a.jsxs(Zv,{children:[a.jsx(e1,{children:n?"Structure Repair Order (view only)":"Manage Structure Repair"}),!n&&a.jsx(c1,{children:"Drag rows to set repair priority — top = first repaired"}),a.jsxs(t1,{ref:o=>{this.listRef=o},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[r.length===0&&a.jsx(n1,{children:"No structure blocks found"}),r.map((o,s)=>{const l=this.state.drag,c=l&&l.keyId===o.id;let d=!1,h=!1,p=!1;if(l&&!c){const v=r.length-1,x=s>l.startIdx?s-1:s;l.dropIdx===0&&x===0?d=!0:l.dropIdx===v&&x===v-1?h=!0:l.dropIdx===x&&(p=!0)}const f=o.maxhealth-o.hp;return a.jsx(ug,{"data-keyid":o.id,$dragging:c,$gapBefore:d,$lineAtEnd:h,$lineBefore:p,$gapSize:l?l.gapSize:0,$readOnly:n,onPointerDown:v=>this.onRowPointerDown(v,o.id,s,r),children:a.jsxs(r1,{children:[o.destroyed?a.jsx(i1,{children:o.displayName}):a.jsx(hg,{children:o.displayName}),a.jsxs(o1,{children:["HP: ",o.hp," / ",o.maxhealth,f>0&&a.jsxs(a.Fragment,{children:[a.jsx(s1,{}),"Dmg: ",f,o.destroyed?" — DESTROYED":""]})]})]})},o.id)})]}),!n&&a.jsx(a1,{children:a.jsx(l1,{className:"ssr-action-button",onClick:o=>this.handleReset(o),title:"Clear custom order and return to default (destroyed first, then most damaged)",children:i?"Reset to Default Order":"Using Default Order"})})]})}}const u1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,h1=u.div`
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
`,p1=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,pg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,f1=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,g1=u.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,m1=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,x1=u.div`
    width: 20px;
    text-align: center;
`,Pc=u.div`
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
`,y1=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class w1 extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrDmgType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrDmgType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating AA setting for:",e),r.setCurrDmgType(e);const l=r.allocatedAA[e];var c=[];for(var d in i.ships){var h=i.ships[d];if(h.userid==n.userid&&!o.isDestroyed(h))if(h.flight)for(var p=0;p<h.systems.length;p++){var f=h.systems[p];if(f)for(var v=0;v<f.systems.length;v++){var x=f.systems[v];if(x&&x.displayName=="Adaptive Armor Controller"){c.push(x);break}}}else for(var v=0;v<h.systems.length;v++){var x=h.systems[v];if(x.displayName=="Adaptive Armor Controller"){c.push(x);break}}}console.log("Found AA controllers:",c.length);for(var b=0;b<c.length;b++){var x=c[b];x.setCurrDmgType(e);let w=0;for(;x.getCurrAllocated()<l&&x.canIncrease()&&w<100;)x.doIncrease(),w++;w>=100&&console.warn("AA Propagation safety break for",x)}s.customEvent("SystemDataChanged",{ship:n,system:r})}getRelevantArmorTypes(){const{ship:e,system:n}=this.props;return n.getRelevantArmorTypes(e)}render(){const{ship:e,system:n}=this.props;if(!n||!n.availableAA)return null;const r=this.getRelevantArmorTypes(),i=n.allocatedAA,o=c=>{const d=n.AAtotal_used,h=n.AAtotal,p=i[c]||0,f=n.AApertype,v=n.availableAA[c],x=n.AApreallocated,b=n.AApreallocated_used;return!(d>=h||p>=f||x<=b&&v<=p)},s=c=>n.currchangedAA[c]>0,l=c=>i[c]>0;return a.jsxs(u1,{children:[a.jsx(h1,{children:"Manage Adaptive Armor"}),a.jsxs(p1,{ref:this.listRef,children:[r.map(c=>a.jsxs(pg,{children:[a.jsx(f1,{src:`./img/systemicons/AAclasses/${c}.png`,alt:c}),a.jsx(g1,{children:c}),a.jsxs(m1,{children:[a.jsx(Pc,{onClick:()=>this.handleDecrease(c),disabled:!s(c),children:"-"}),a.jsx(x1,{children:i[c]}),a.jsx(Pc,{onClick:()=>this.handleIncrease(c),disabled:!o(c),children:"+"}),a.jsx(Pc,{title:"Propagate to all units",onClick:()=>this.handlePropagate(c),disabled:!l(c),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/AAclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},c)),r.length===0&&a.jsx(pg,{children:"No armor types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",n.AAtotal_used," / ",n.AAtotal," ",a.jsx(y1,{})," Max Per Type: ",n.AApertype]})]})}}const v1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,b1=u.div`
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
`,S1=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,fg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,k1=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,$1=u.div`
    flex: 1;
    font-weight: normal; 
`,C1=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,T1=u.div`
    width: 20px;
    text-align: center;
`,Ac=u.div`
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
`,M1=u.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;class j1 extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrFCType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrFCType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating BFCP setting for:",e),r.setCurrFCType(e);const l=r.allocatedBFCP[e];var c=[];for(var d in i.ships){var h=i.ships[d];if(h.userid==n.userid&&!o.isDestroyed(h))if(h.flight)for(var p=0;p<h.systems.length;p++){var f=h.systems[p];if(f)for(var v=0;v<f.systems.length;v++){var x=f.systems[v];if(x&&x.displayName=="Computer"){c.push(x);break}}}else for(var v=0;v<h.systems.length;v++){var x=h.systems[v];if(x.displayName=="Computer"){c.push(x);break}}}console.log("Found BFCP controllers:",c.length);for(var b=0;b<c.length;b++){var x=c[b];x.setCurrFCType(e);let w=0;for(;x.getCurrAllocated()<l&&x.canIncrease()&&w<100;)x.doIncrease(),w++;for(;x.getCurrAllocated()>l&&x.canDecrease()&&w<100;)x.doDecrease(),w++;w>=100&&console.warn("BFCP Propagation safety break for",x)}s.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{system:e}=this.props;if(!e||!e.allocatedBFCP)return null;const n=Object.keys(e.allocatedBFCP),r=e.allocatedBFCP,i=(d,h)=>{const p=e.currClass;e.setCurrFCType(d);const f=!!e[h]();return e.currClass=p,f},o=d=>i(d,"canIncrease"),s=d=>i(d,"canDecrease"),l=window.gamedata&&window.gamedata.gamephase===3&&typeof e.firingMode=="function"?e.firingMode():null,c=d=>r[d]>=0;return a.jsxs(v1,{children:[a.jsx(b1,{children:"Hyach Computer"}),a.jsxs(S1,{ref:this.listRef,children:[n.map(d=>a.jsxs(fg,{children:[a.jsx(k1,{src:`./img/systemicons/BFCPclasses/${d}.png`,alt:d}),a.jsx($1,{children:d}),a.jsxs(C1,{children:[a.jsx(Ac,{onClick:()=>this.handleDecrease(d),disabled:!s(d),children:"-"}),a.jsx(T1,{children:r[d]}),a.jsx(Ac,{onClick:()=>this.handleIncrease(d),disabled:!o(d),children:"+"}),a.jsx(Ac,{title:"Propagate to all units",onClick:()=>this.handlePropagate(d),disabled:!c(d),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/iconPropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},d)),n.length===0&&a.jsx(fg,{children:"No FC types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Total: ",e.BFCPtotal_used," / ",e.output," ",a.jsx(M1,{})," Max Per Type: ",e.BFCPpertype]}),l&&a.jsx("div",{style:{padding:"0 5px 5px",textAlign:"center",fontSize:"10px",color:"#c9a6cf"},children:l==="reshuffle"?"Computer Specialist: any point may be moved":"Expert Software Engineer: one held point may be added"})]})}}const E1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,D1=u.div`
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
`,P1=u.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,gg=u.div`
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
`,A1=u.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,R1=u.span`
    flex-grow: 1;
    font-weight: bold;
`,I1=u.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,Ts=u.div`
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
`,O1=t=>window.gamedata.turn===window.shipManager.getTurnPlaced(t)&&window.gamedata.gamephase===-1;class L1 extends I.Component{constructor(e){super(e)}handleSelect(e){const{system:n}=this.props;n.specCurrClass=e,n.canSelect()&&(n.doSelect(),this.forceUpdate())}handleUnselect(e){const{system:n}=this.props;n.specCurrClass=e,n.canUnselect()&&(n.doUnselect(),this.forceUpdate())}handleUse(e){const{system:n}=this.props;n.specCurrClass=e,n.canUse()&&(n.doUse(),this.forceUpdate())}handleCancel(e){const{system:n}=this.props;n.specCurrClass=e,n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}render(){const{ship:e,system:n}=this.props;if(!n)return null;const r=O1(e);let i=[];const o=!r&&window.officers?window.officers.specialistReuseList(e):[];r?n.allSpec&&(i=Object.keys(n.allSpec)):n.availableSpec&&(i=Object.keys(n.availableSpec).filter(l=>n.availableSpec[l]>0||o.indexOf(l)!==-1)),i.sort();let s="";return r?s=`Specialists Selected: ${Object.values(n.availableSpec||{}).reduce((c,d)=>c+d,0)} / ${n.specTotal}`:s=`Specialists Used: ${n.specTotal_used||0} / ${n.specTotal}`,a.jsxs(E1,{children:[a.jsx(D1,{children:"Hyach Specialists"}),a.jsxs(P1,{children:[i.map(l=>{const c=r&&(n.specCurrClass=l,n.canSelect()),d=r&&(n.specCurrClass=l,n.canUnselect()),h=!r&&(n.specCurrClass=l,n.canUse()),p=!r&&(n.specCurrClass=l,n.canDecrease());n.availableSpec&&n.availableSpec[l]>0,n.currAllocatedSpec&&n.currAllocatedSpec[l];const f=`./img/systemicons/Specialistclasses/${l}.png?v=2`;return a.jsxs(gg,{children:[a.jsx(A1,{src:f,alt:l}),a.jsxs(R1,{children:[l,o.indexOf(l)!==-1&&a.jsx("span",{style:{fontWeight:"normal"},children:" (repeat)"})]}),a.jsx(I1,{children:r?a.jsxs(a.Fragment,{children:[a.jsx(Ts,{onClick:()=>this.handleUnselect(l),disabled:!d,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})}),a.jsx(Ts,{onClick:()=>this.handleSelect(l),disabled:!c,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})})]}):a.jsxs(a.Fragment,{children:[a.jsx(Ts,{onClick:()=>this.handleUse(l),disabled:!h,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconPlus.png",style:{width:"12px",height:"12px"},alt:"Use"})}),a.jsx(Ts,{onClick:()=>this.handleCancel(l),disabled:!p,children:a.jsx("img",{src:"./img/systemicons/Specialistclasses/iconMinus.png",style:{width:"12px",height:"12px"},alt:"Cancel"})})]})})]},l)}),i.length===0&&a.jsx(gg,{style:{justifyContent:"center",fontStyle:"italic"},children:"No Specialists Available"})]}),a.jsx("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:s})]})}}const z1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${m.colors.line};
`,B1=u.div`
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
`,N1=u.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,mg=u.div`
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
`,F1=u.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,_1=u.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,H1=u.input`
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
`;const W1=u.div`
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
`;class xg extends I.Component{constructor(e){super(e),this.listRef=ie.createRef(),this.state={shieldInputs:{}}}getShieldLabel(e){let n=e.startArc,r=e.endArc;if(n===void 0||r===void 0)return e.displayName;let i=(n+r)/2;n>r&&(i=(n+r+360)/2),i=i%360;let o="";return i>=337.5||i<22.5?o="Front":i>=22.5&&i<67.5?o="Front Starboard":i>=67.5&&i<112.5?o="Starboard":i>=112.5&&i<157.5?o="Aft Starboard":i>=157.5&&i<202.5?o="Aft":i>=202.5&&i<247.5?o="Aft Port":i>=247.5&&i<292.5?o="Port":i>=292.5&&i<337.5&&(o="Front Port"),o?`${o} - ${e.displayName}`:e.displayName}getShieldSortPriority(e){const n=this.getShieldLabel(e).split(" - ")[0];return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[n]||99}getGeneratorAndShields(){const{ship:e,system:n}=this.props;let r=null,i=[],o="",s="";return n.name==="ThirdspaceShield"||n.name==="ThirdspaceShieldGenerator"?(o="ThirdspaceShield",s="ThirdspaceShieldGenerator"):(n.name==="ThoughtShield"||n.name==="ThoughtShieldGenerator")&&(o="ThoughtShield",s="ThoughtShieldGenerator"),o?(e.systems&&(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).forEach(c=>{c.name===s&&(r=c),c.name===o&&i.push(c)}),i.sort((l,c)=>{const d=this.getShieldSortPriority(l),h=this.getShieldSortPriority(c);return d!==h?d-h:l.id-c.id}),{generator:r,shields:i,systemName:o}):{generator:null,shields:[]}}handleIncrease(e,n){e.canIncrease()&&(e.doIncrease(n),this.afterShieldChange(e))}handleDecrease(e,n){e.canDecrease()&&(e.doDecrease(n),this.afterShieldChange(e))}handleMin(e){e.canDecrease()&&(e.doMin(),this.afterShieldChange(e))}handleMax(e){e.canIncrease()&&(e.doMax(),this.afterShieldChange(e))}afterShieldChange(e){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}),this.updateInputState(e)}handleInputChange(e,n){if(n===""){this.setState(s=>({shieldInputs:{...s.shieldInputs,[e.id]:""}}));return}const r=parseInt(n,10);if(isNaN(r))return;const i=e.currentHealth,o=r-i;o>0?this.handleIncrease(e,o):o<0&&this.handleDecrease(e,Math.abs(o))}updateInputState(e){this.setState(n=>({shieldInputs:{...n.shieldInputs,[e.id]:e.currentHealth}}))}handleWheel(e,n){e.preventDefault(),(e.deltaY<0?1:-1)>0?this.handleIncrease(n,1):this.handleDecrease(n,1)}handleMouseEnter(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const n=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);n&&(n.showWeaponArc(this.props.ship,e),window.webglScene.requestRender())}}handleMouseLeave(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){const n=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);n&&(n.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(e){e&&(shipManager.power.clickPlus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}handleDeBoost(e){e&&(shipManager.power.clickMinus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}handleEqualise(e){e&&(e.doEqualise(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:e}))}componentDidMount(){const{generator:e,shields:n}=this.getGeneratorAndShields(),r={};n.forEach(i=>r[i.id]=i.currentHealth),this.setState({shieldInputs:r})}componentDidUpdate(e){const{generator:n,shields:r}=this.getGeneratorAndShields(),i=this.state.shieldInputs,o={};let s=!1;r.forEach(l=>{i[l.id]!==l.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${l.id}`)&&(o[l.id]=l.currentHealth,s=!0)}),s&&this.setState(l=>({shieldInputs:{...l.shieldInputs,...o}}))}render(){const{generator:e,shields:n,systemName:r}=this.getGeneratorAndShields();return e?a.jsxs(z1,{children:[a.jsx(B1,{children:r==="ThirdspaceShield"?"Thirdspace Shields":"Thought Shields"}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791"},children:["Unallocated Shield Energy: ",e.storedCapacity]}),r==="ThirdspaceShield"&&a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"11px",color:"#deebff",borderBottom:"1px solid #496791",display:"flex",justifyContent:"center",alignItems:"center",gap:"5px"},children:["Regeneration Rate: ",shipManager.systems.getOutputNoBoost(this.props.ship,e)+shipManager.power.getBoost(e)*n.length,a.jsx(qe,{className:"small",onClick:()=>this.handleDeBoost(e),title:"Reduce Boost",children:"-"}),a.jsx(qe,{className:"small",onClick:()=>this.handleBoost(e),title:"Boost Generator",children:"+"})]}),a.jsxs(N1,{ref:this.listRef,children:[n.map(i=>a.jsxs(mg,{onMouseEnter:()=>this.handleMouseEnter(i),onMouseLeave:()=>this.handleMouseLeave(i),children:[a.jsx(F1,{children:this.getShieldLabel(i)}),a.jsxs(_1,{children:[a.jsx(qe,{onClick:()=>this.handleMin(i),disabled:!i.canDecrease(),title:"Drop shield to 0",children:"Min"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,25),disabled:!i.canDecrease(),children:"-25"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,10),disabled:!i.canDecrease(),children:"-10"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,5),disabled:!i.canDecrease(),children:"-5"}),a.jsx(qe,{className:"small",onClick:()=>this.handleDecrease(i,1),disabled:!i.canDecrease(),children:"-1"}),a.jsx(H1,{id:`shield-input-${i.id}`,type:"number",value:this.state.shieldInputs[i.id]!==void 0?this.state.shieldInputs[i.id]:i.currentHealth,onChange:o=>this.handleInputChange(i,o.target.value),onWheel:o=>this.handleWheel(o,i)}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,1),disabled:!i.canIncrease(),children:"+1"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,5),disabled:!i.canIncrease(),children:"+5"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,10),disabled:!i.canIncrease(),children:"+10"}),a.jsx(qe,{className:"small",onClick:()=>this.handleIncrease(i,25),disabled:!i.canIncrease(),children:"+25"}),a.jsx(qe,{onClick:()=>this.handleMax(i),disabled:!i.canIncrease(),title:"Raise shield to maximum",children:"Max"})]})]},i.id)),n.length===0&&a.jsx(mg,{children:"No Shields Found"})]}),e&&a.jsx(W1,{onClick:()=>this.handleEqualise(e),children:"Equalise Shields"})]}):a.jsx("div",{children:"No Generator Found"})}}const yg=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${m.colors.line};
`,wg=u.div`
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
`,Rc=u.div`
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
`,U1=u.div`
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
`,Cr=u.div`
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
`;class G1 extends I.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}canBoost(){const{ship:e,system:n}=this.props;return n.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,n)}canDeBoost(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(n)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){const{ship:e,system:n}=this.props,r=shipManager.power.getBoost(n),i=n.active;return a.jsxs(yg,{children:[a.jsx(wg,{children:"Power Capacitor"}),n.boostable&&a.jsxs(Rc,{children:[a.jsx(Pi,{children:"Open Petals"}),a.jsxs(Ai,{children:[a.jsx(Cr,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:r===0,children:"OFF"}),a.jsx(Cr,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:r>0,$variant:"activate",children:"ON"})]})]}),a.jsxs(Rc,{children:[a.jsx(Pi,{children:"Double Recharge"}),a.jsxs(Ai,{children:[a.jsx(Cr,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!i,children:"OFF"}),a.jsx(Cr,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:i,$variant:"activate",children:"ON"})]})]})]})}}const vg=u(yg)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`,Se={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",hover:"rgba(75, 43, 81, 0.6)",text:"#f2f2f2",textDim:"#d8b9e6"},V1=u(vg)`
    background-color: ${Se.surface};
    border: 1px solid ${Se.line};
`,Y1=u.div`
    padding: 3px;
    background-color: ${Se.line};
    border: 1px solid ${Se.accent};
    color: ${Se.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,bg=u.div`
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
`,K1=u.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${Se.textDim};
`,Ms=u.div`
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
`;class Q1 extends I.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}handlePower(e){const{ship:n,system:r}=this.props;if(!Sg(n,r))return;const i=r.getAbductionOrder();r.setAbductionPowerLevel(r.getAbductionPowerLevel(i)+e)&&(webglScene.customEvent("SystemDataChanged",{ship:r.getOwningUnit()||n,system:r}),this.forceUpdate())}handleCancel(){const{ship:e,system:n}=this.props;gamedata.gamephase!==1||!gamedata.isMyShip(n.getOwningUnit()||e)||(weaponManager.removeFiringOrder(n.getOwningUnit()||e,n),this.forceUpdate())}renderMaintain(){const{system:e}=this.props,n=e.isMaintainingVortex(),r=e.chargesVortexUpkeep()?e.getVortexUpkeepCost():0;return a.jsxs(ie.Fragment,{children:[a.jsx(wg,{children:"Jump Point"}),a.jsxs(Rc,{children:[a.jsx(Pi,{children:"Maintain Vortex"}),a.jsxs(Ai,{children:[a.jsx(Cr,{onClick:()=>this.handleDeactivate(),disabled:!e.canDeactivate(),$active:!n,children:"OFF"}),a.jsx(Cr,{onClick:()=>this.handleActivate(),disabled:!e.canActivate(),$active:n,$variant:"activate",children:"ON"})]})]}),a.jsx(U1,{children:r>0?n?"Held open. "+r+" power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.":"Closes at end of turn unless maintained. Costs "+r+" power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.":n?"Held open. All powered systems except the Scanner are shut down this turn.":"Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner."})]})}renderAbduction(){const{ship:e,system:n}=this.props,r=n.getAbductionOrder(),i=gamedata.getShip(r.targetid),o=n.getAbductionPowerLevel(r),s=Sg(e,n),l=n.isExtraDimensional(),c=n.abductionMaxPower||1,d=window.JumpEngine.formatAbductionHalves(n.getAbductionHalves(r)),h=window.JumpEngine.getAbductionChain(r.targetid),p=window.JumpEngine.getAbductionCostPreview(r.targetid),f=n.isAbductionPowered(r);let v;if(f)v=d+" power-turn"+(d==="1"?"":"s")+" for "+n.getAbductionPowerDraw()+" power. So far "+window.JumpEngine.formatAbductionHalves(h.total)+" of "+h.cost+" power-turns.";else{const x=!!i&&gamedata.isTerrain(i.shipSizeClass,i.userid),b=!!i&&((i.hexOffsets||[]).length>0||i.Huge>0);v=(x?"Targeting: takes hold if "+(b?"every hex it occupies is":"it is")+" inside your connected field and your OEW beats its DEW.":"Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.")+" Power can be applied from next turn"+(p!==null?", and "+p+" power-turns will abduct it.":".")}return a.jsxs(V1,{children:[a.jsxs(Y1,{children:["Abduction",i?": "+i.name:""]}),f&&a.jsxs(bg,{children:[a.jsx(Pi,{children:l?"Power":"Double power"}),l?a.jsxs(Ai,{children:[a.jsx(Ms,{onClick:()=>this.handlePower(-1),disabled:!s||o<=1,children:"-"}),a.jsx(Ms,{$active:!0,children:"x"+o}),a.jsx(Ms,{onClick:()=>this.handlePower(1),disabled:!s||o>=c,children:"+"})]}):null]}),gamedata.gamephase===1&&gamedata.isMyShip(n.getOwningUnit()||e)&&a.jsxs(bg,{children:[a.jsx(Pi,{children:"Declaration"}),a.jsx(Ai,{children:a.jsx(Ms,{$wide:!0,onClick:()=>this.handleCancel(),children:"CANCEL"})})]}),a.jsx(K1,{children:v})]})}render(){const{system:e}=this.props,n=e.canMaintainVortex()||e.canDeactivate(),r=typeof e.getAbductionOrder=="function"&&!!e.getAbductionOrder();return a.jsxs(ie.Fragment,{children:[n&&a.jsx(vg,{children:this.renderMaintain()}),r&&this.renderAbduction()]})}}const Sg=(t,e)=>gamedata.gamephase===1&&gamedata.isMyShip(e.getOwningUnit()||t)&&e.isExtraDimensional()&&e.isAbductionPowered(),Ze={surface:"rgba(32, 0, 32, 0.9)",line:"#5d3564",accent:"#7c4686",text:"#f2f2f2"},X1=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${t=>t.$isWeapon||t.$isPurple?Ze.surface:"rgba(16, 26, 38, 0.9)"};
    border: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Ze.line:m.colors.line};
`,J1=u.div`
    padding: 3px;
    background-color: ${t=>t.$isWeapon?"#571616":t.$isPurple?Ze.line:"#215a7a"};
    border: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Ze.line:m.colors.line};
    border-bottom: 1px solid ${t=>t.$isWeapon?"#b43131":t.$isPurple?Ze.line:m.colors.line};
    color: ${t=>t.$isWeapon||t.$isPurple?Ze.text:m.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,q1=u.div`
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
`;const Z1=u.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,kg=u.div`
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
`;class eb extends I.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleActivateAll(e){e.preventDefault();const{ship:n,system:r}=this.props;let i=[];n.flight?i=n.systems.map(s=>s.systems).reduce((s,l)=>s.concat(l),[]):i=n.systems;let o=!1;i.forEach(s=>{!s.name||s.name!==r.name||s.canActivate&&typeof s.canActivate=="function"&&s.canActivate()&&(s.doActivate(),o=!0)}),o&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(e){e.preventDefault();const{ship:n,system:r}=this.props;let i=[];n.flight?i=n.systems.map(s=>s.systems).reduce((s,l)=>s.concat(l),[]):i=n.systems;let o=!1;i.forEach(s=>{!s.name||s.name!==r.name||s.canDeactivate&&typeof s.canDeactivate=="function"&&s.canDeactivate()&&(s.doDeactivate(),o=!0)}),o&&(this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate=="function"&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate=="function"&&this.props.system.canDeactivate()}render(){const{ship:e,system:n}=this.props,r=n.active||n.weapon&&!n.activationIsToggle&&weaponManager.hasFiringOrder(e,n),i=typeof n.getActivateLabel=="function"?n.getActivateLabel():null,o=typeof n.getDeactivateLabel=="function"?n.getDeactivateLabel():null,s=i||(n.weapon?"Fire":"Activate"),l=o||(n.weapon?"Don't Fire":"Deactivate"),c=!!n.singleActivationButton,d=!c||this.canActivate(),h=(!n.weapon||n.activationIsToggle)&&(!c||this.canDeactivate()),p=!!n.activationMenuPurple&&!n.weapon;return a.jsxs(X1,{$isWeapon:n.weapon,$isPurple:p,children:[a.jsx(J1,{$isWeapon:n.weapon,$isPurple:p,children:n.displayName}),a.jsx(q1,{$isPurple:p,children:a.jsxs(Z1,{children:[d&&a.jsx(kg,{onClick:()=>this.handleActivate(),onContextMenu:f=>this.handleActivateAll(f),disabled:!this.canActivate(),$active:r,$variant:"activate",$isWeapon:n.weapon,$isToggle:!!n.activationIsToggle,$isPurple:p,children:s}),h&&a.jsx(kg,{onClick:()=>this.handleDeactivate(),onContextMenu:f=>this.handleDeactivateAll(f),disabled:!this.canDeactivate(),$active:!r,$variant:"deactivate",$isPurple:p,children:l})]})})]})}}const tb=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,nb=u.div`
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
`,Ic=u.div`
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
`,Oc=u.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,js=u.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,rb=u.div`
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
`;class ib extends I.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(e){e.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){const{ship:e,system:n}=this.props;for(;shipManager.power.getBoost(n)>0;)shipManager.power.clickMinus(e,n);shipManager.power.onOfflineClicked(e,n),this.handleUpdate()}}handleOfflineAll(e){if(e.preventDefault(),this.canOffline()){const{ship:n,system:r}=this.props;for(;shipManager.power.getBoost(r)>0;)shipManager.power.clickMinus(n,r);shipManager.power.offlineAll(n,r),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:this.props.ship,system:this.props.system})}canOffline(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&(n.canOffLine||n.powerReq>0)&&!n.powerLocked&&!shipManager.power.isOffline(e,n)&&!weaponManager.hasFiringOrder(e,n)}canOnline(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(e,n)&&!shipManager.power.isForcedOffline(e,n)&&!shipManager.power.isVortexLockedOffline(e,n)}canBoost(){const{ship:e,system:n}=this.props;return n.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,n)&&(!n.isScanner()||n.id==shipManager.power.getHighestSensorsId(e))&&n.name!=="ThirdspaceShieldGenerator"&&n.name!=="powerCapacitor"&&n.name!=="PowerCapacitor"}canDeBoost(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(n)&&n.name!=="ThirdspaceShieldGenerator"&&n.name!=="powerCapacitor"&&n.name!=="PowerCapacitor"}canOverload(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(e,n)&&n.weapon&&n.overloadable&&!shipManager.power.isOverloading(e,n)}canStopOverload(){const{ship:e,system:n}=this.props;return gamedata.gamephase===1&&n.weapon&&n.overloadable&&shipManager.power.isOverloading(e,n)&&(n.overloadshots>=n.extraoverloadshots||n.overloadshots==0)}render(){const{ship:e,system:n}=this.props,r=this.canOffline()||this.canOnline(),i=n.boostable&&(this.canBoost()||this.canDeBoost()),o=n.overloadable&&(this.canOverload()||this.canStopOverload());if(!r&&!i&&!o)return null;const s=shipManager.power.isOffline(e,n),l=shipManager.power.getBoost(n),c=shipManager.power.isOverloading(e,n),d=n.name==="reactor",h=n.name==="jumpEngine",p=h&&typeof n.getChargeBoostMax=="function"&&n.getChargeBoostMax()>0;let f="Boost Level";return d&&(f="Self-Destruct"),h&&(f=p?"Extra Charging":"Jump to Hyperspace"),a.jsxs(tb,{children:[a.jsx(nb,{children:"Power Settings"}),r&&a.jsxs(Ic,{children:[a.jsx(Oc,{children:"Power"}),a.jsxs(js,{children:[a.jsx(ln,{onClick:()=>this.handleOnline(),onContextMenu:v=>this.handleOnlineAll(v),disabled:!this.canOnline(),$active:!s,$variant:"activate",children:"On"}),a.jsx(ln,{onClick:()=>this.handleOffline(),onContextMenu:v=>this.handleOfflineAll(v),disabled:!this.canOffline(),$active:s,$variant:"deactivate",children:"Off"})]})]}),i&&a.jsxs(Ic,{children:[a.jsx(Oc,{children:f}),d||h&&!p?a.jsxs(js,{children:[a.jsx(ln,{onClick:()=>this.handleBoost(),disabled:l>0||!this.canBoost(),$active:l>0,$variant:d?"deactivate":"risk",children:"Yes"}),a.jsx(ln,{onClick:()=>this.handleDeBoost(),disabled:l===0||!this.canDeBoost(),$active:l===0,$variant:"activate",children:"No"})]}):a.jsxs(js,{children:[a.jsx(ln,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:"-"}),a.jsx(rb,{children:l}),a.jsx(ln,{onClick:()=>this.handleBoost(),$narrow:!0,children:"+"})]})]}),o&&a.jsxs(Ic,{children:[a.jsx(Oc,{children:"Overcharge"}),a.jsxs(js,{children:[a.jsx(ln,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:c,$variant:"warning",children:"Yes"}),a.jsx(ln,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!c,$variant:"deactivate",children:"No"})]})]})]})}}const ob=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,sb=u.div`
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
`,ab=u.div`
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
`,$g=u.div`
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
`,lb=u.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,cb=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Cg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,db=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,ub=u.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,hb=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,pb=u.div`
    width: 20px;
    text-align: center;
`,Lc=u.div`
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
`;class fb extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleIncrease(e){const{system:n}=this.props;n.setCurrShipType(e),n.canIncrease()&&(n.doIncrease(),this.forceUpdate())}handleDecrease(e){const{system:n}=this.props;n.setCurrShipType(e),n.canDecrease()&&(n.doDecrease(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene,l=!!(r.hasMultiTarget&&r.hasMultiTarget()),c=l?r.getCurrWeaponId():null,d=l?r.getMineWeapons():[],h=l?d.find($=>String($.id)===String(c)):null;if(l&&!h)return;r.setCurrShipType(e);const p=r.rangeSetting||r.range,f=l?r.allocatedRanges[c]:r.allocatedRanges;if(!f)return;const v=f[e]===null||f[e]===void 0?p:f[e];var x=[];for(var b in i.ships){var M=i.ships[b];if(M.userid==n.userid&&!o.isDestroyed(M)){if(M.phpclass&&n.phpclass){if(M.phpclass!=n.phpclass)continue}else if(M.shipClass!=n.shipClass)continue;for(var w in M.systems){var g=M.systems[w];if(g&&g.name===r.name){var y=!!(g.hasMultiTarget&&g.hasMultiTarget());y===l&&x.push({unit:M,ctrl:g})}}}}for(var S=0;S<x.length;S++){var T=x[S],g=T.ctrl;if(l){g.ensureMultiAllocatedShape&&g.ensureMultiAllocatedShape();var C=g.getMineWeapons(),k=C.find(he=>he.displayName===h.displayName&&he.indexInGroup===h.indexInGroup);if(!k)continue;g.setCurrWeaponId(k.id)}g.setCurrShipType(e);let D=0;const E=()=>l?g.allocatedRanges[g.getCurrWeaponId()]:g.allocatedRanges,H=()=>g.range||g.rangeSetting,K=()=>{const he=E();if(!he)return H();const ut=he[e];return ut??H()};for(;K()<v&&g.canIncrease()&&D<100;)g.doIncrease(),D++;for(;K()>v&&g.canDecrease()&&D<100;)g.doDecrease(),D++;D>=100&&console.warn("Mine Settings Propagation safety break for",g)}s.customEvent("SystemDataChanged",{ship:n,system:r})}cycleWeapon(e){const{system:n}=this.props;if(!n.hasMultiTarget||!n.hasMultiTarget())return;const r=n.getMineWeapons();if(r.length===0)return;const i=n.getCurrWeaponId();let o=r.findIndex(l=>String(l.id)===String(i));o<0&&(o=0);const s=(o+e+r.length)%r.length;n.setCurrWeaponId(r[s].id),this.forceUpdate()}render(){const{system:e}=this.props;if(!e||(e.range=e.range||e.rangeSetting,!e.range))return null;const n=!!(e.hasMultiTarget&&e.hasMultiTarget());let r=[],i=null,o=null,s=e.allocatedRanges||{};n?(e.ensureMultiAllocatedShape&&e.ensureMultiAllocatedShape(),r=e.getMineWeapons(),i=e.getCurrWeaponId(),o=r.find(x=>String(x.id)===String(i))||r[0]||null,s=i!=null&&e.allocatedRanges[i]?e.allocatedRanges[i]:{}):(e.ensureFlatAllocatedShape&&e.ensureFlatAllocatedShape(),s=e.allocatedRanges||{});const l=Object.keys(s),c=e.validTargets||l,d=x=>{if(!c.includes(x))return"N/A";const b=s[x];return b??e.range},h=x=>c.includes(x)?d(x)<e.range:!1,p=x=>c.includes(x)?d(x)>0:!1,f=x=>!!c.includes(x),v=n&&r.length>0?a.jsxs(ab,{children:[a.jsx($g,{onClick:()=>this.cycleWeapon(-1),disabled:r.length<2,title:"Previous weapon",children:"<"}),a.jsx(lb,{children:o?o.label:""}),a.jsx($g,{onClick:()=>this.cycleWeapon(1),disabled:r.length<2,title:"Next weapon",children:">"})]}):a.jsx(sb,{children:"Set Mine Range"});return a.jsxs(ob,{children:[v,a.jsxs(cb,{ref:this.listRef,children:[l.map(x=>a.jsxs(Cg,{children:[a.jsx(db,{src:`./img/systemicons/BFCPclasses/${x}.png`,alt:x}),a.jsx(ub,{children:x}),a.jsxs(hb,{onWheel:b=>{b.deltaY<0&&h(x)?this.handleIncrease(x):b.deltaY>0&&p(x)&&this.handleDecrease(x)},children:[a.jsx(Lc,{onClick:()=>this.handleDecrease(x),disabled:!p(x),children:"-"}),a.jsx(pb,{children:d(x)}),a.jsx(Lc,{onClick:()=>this.handleIncrease(x),disabled:!h(x),children:"+"}),a.jsx(Lc,{title:n?"Propagate this weapon's settings to same-class mines with Multiple Targets":"Propagate to all mines of same type",onClick:()=>this.handlePropagate(x),disabled:!f(x),style:{marginLeft:"5px"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},x)),l.length===0&&a.jsx(Cg,{children:"No ship types available"})]}),a.jsxs("div",{style:{padding:"5px",textAlign:"center",fontSize:"10px",color:"#f2f2f2"},children:["Max Range: ",e.range]})]})}}const gb=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,mb=u.div`
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
`,xb=u.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Tg=u.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,yb=u.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,wb=u.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,vb=u.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,bb=u.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${t=>t.$active?"#4CAF50":"#F44336"};
`,Mg=u.div`
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
`;class Sb extends I.Component{constructor(e){super(e),this.listRef=ie.createRef()}handleToggle(e){const{system:n}=this.props;n.setCurrShipType(e),n.canSet()?(n.doSet(),this.forceUpdate()):n.canUnset()&&(n.doUnset(),this.forceUpdate())}handlePropagate(e){const{ship:n,system:r}=this.props,i=window.gamedata,o=window.shipManager,s=window.webglScene;console.log("Propagating Mine settings for:",e),r.setCurrShipType(e);const l=r.allocatedShipTypes[e];var c=[];for(var d in i.ships){var h=i.ships[d];if(h.userid==n.userid&&!o.isDestroyed(h))for(var p=0;p<h.systems.length;p++){var f=h.systems[p];if(h.shipClass==n.shipClass&&f.name===r.name){c.push(f);break}}}console.log("Found Mine Weapons of same type:",c.length);for(var v=0;v<c.length;v++){var f=c[v];f.setCurrShipType(e);let b=0;for(;f.allocatedShipTypes[e]!==l&&(l?f.canSet():f.canUnset())&&b<10;)l?f.doSet():f.doUnset(),b++;b>=10&&console.warn("Mine Settings Propagation safety break for",f)}s.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{system:e}=this.props;if(!e)return null;const n=e.allocatedShipTypes||{},r=Object.keys(n),i=s=>n[s]?"YES":"NO",o=s=>{const l=Number(this.props.ship.spawned),c=l===-1?1:l+1;return window.gamedata.turn===c};return a.jsxs(gb,{children:[a.jsx(mb,{children:"Set Target Types"}),a.jsxs(xb,{ref:this.listRef,children:[r.map(s=>a.jsxs(Tg,{children:[a.jsx(yb,{src:`./img/systemicons/BFCPclasses/${s}.png`,alt:s}),a.jsx(wb,{children:s}),a.jsxs(vb,{children:[a.jsx(bb,{$active:n[s],children:i(s)}),a.jsx(Mg,{onClick:()=>this.handleToggle(s),disabled:!o(),style:{marginLeft:"5px"},children:"Toggle"}),a.jsx(Mg,{title:"Propagate to all mines of same type",onClick:()=>this.handlePropagate(s),disabled:!1,style:{marginLeft:"5px",width:"16px",padding:"0"},children:a.jsx("img",{src:"./img/systemicons/BFCPclasses/minePropagate.png",alt:"Propagate",style:{width:"12px",height:"12px"}})})]})]},s)),r.length===0&&a.jsx(Tg,{children:"No ship types available"})]})]})}}const zc=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: ${m.colors.greenBg};
    border: 1px solid ${m.colors.greenLine};
`,jg=u.div`
    padding: 3px;
    background-color: ${m.colors.greenTitleBg};
    border: 1px solid ${m.colors.greenLine};
    color: ${m.colors.greenText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,kb=u.div`
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
`,Eg=u.div`
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
`,$b=u.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;u.div`
    text-align: center;
    color: ${m.colors.greenLabel};
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;const Ri=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,Dg=u.div`
    width: 70px;
    font-size: 10px;
    color: ${m.colors.greenLabel};
    user-select: none;
`,An=u.div`
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
`;class Cb extends I.Component{refresh(){const{ship:e,system:n}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n})}cycleMode(e){const{system:n}=this.props;if(!gamedata.isMyShip(this.props.ship))return;const r=n.firingMode==1?2:1;n.setFiringMode(r),typeof n.initializationUpdate=="function"&&n.initializationUpdate(),this.refresh()}activateMode1(){const{ship:e,system:n}=this.props;n.canActivate()&&(n.doActivate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n}),webglScene.customEvent("CloseSystemInfo"))}targetWarrior(){const{ship:e,system:n}=this.props;weaponManager.isSelectedWeapon(n)||weaponManager.selectWeapon(e,n),webglScene.customEvent("SystemDataChanged",{ship:e,system:n}),webglScene.customEvent("CloseSystemInfo")}setRotation(e,n){const{system:r}=this.props;r.rotationDirection=e,r.rotationAmount=n,typeof r.updateRotationNotes=="function"&&r.updateRotationNotes(),this.refresh()}renderInitialOrders(){const{system:e}=this.props,n=parseInt(e.firingMode,10),r=e.firingModes[n]||"";return a.jsxs(zc,{children:[a.jsxs(kb,{children:[a.jsx(Eg,{onClick:()=>this.cycleMode(-1),title:"Previous mode",children:"<"}),a.jsx($b,{children:r}),a.jsx(Eg,{onClick:()=>this.cycleMode(1),title:"Next mode",children:">"})]}),n==1&&a.jsx(Ri,{children:a.jsx(An,{onClick:()=>this.activateMode1(),children:"Activate"})}),n==2&&a.jsx(Ri,{children:a.jsx(An,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(e),children:"Target friendly Warrior"})})]})}engageMode3(){const{ship:e,system:n}=this.props;gamedata.isMyShip(e)&&(weaponManager.hasFiringOrder(e,n)||(n.setFiringMode(3),typeof n.initializationUpdate=="function"&&n.initializationUpdate(),this.refresh()))}renderPreFiring(){const{system:e}=this.props;if(e.firingMode!=3)return a.jsxs(zc,{children:[a.jsx(jg,{children:"Gravitic Augmenter"}),a.jsx(Ri,{children:a.jsx(An,{onClick:()=>this.engageMode3(),children:"Engage Gravity Shifting"})})]});const n=e.rotationDirection||1,r=e.rotationAmount||1,i=(o,s)=>n==o&&r==s;return a.jsxs(zc,{children:[a.jsx(jg,{children:"Gravity Shift Settings"}),a.jsxs(Ri,{children:[a.jsx(Dg,{children:"Clockwise"}),a.jsx(An,{onClick:()=>this.setRotation(1,1),$active:i(1,1),children:"60°"}),a.jsx(An,{onClick:()=>this.setRotation(1,2),$active:i(1,2),children:"120°"})]}),a.jsxs(Ri,{children:[a.jsx(Dg,{children:"Anti-Clockwise"}),a.jsx(An,{onClick:()=>this.setRotation(2,1),$active:i(2,1),children:"60°"}),a.jsx(An,{onClick:()=>this.setRotation(2,2),$active:i(2,2),children:"120°"})]})]})}render(){const{system:e}=this.props;return e?gamedata.gamephase==1?(e.firingMode==3&&(e.setFiringMode(1),typeof e.initializationUpdate=="function"&&e.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}}const Es=3,Tb=u.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Mb=u.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,jb=u.div`
    text-align: center;
    color: ${t=>t.$empty?"#f0a0a0":"#f2f2f2"};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,Eb=u.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,Db=u.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,Pg=u.div`
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
`,Pb=u.input`
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
`;const Rn=[{key:"hitBoost5",label:"Hit Chance",increment:5,prefix:"+",suffix:"%",display:t=>t*2,parse:t=>t/2},{key:"shotBoost",label:"Shots",increment:1,prefix:"+",suffix:"",display:t=>t,parse:t=>t},{key:"dmgBoost5",label:"Damage",increment:5,prefix:"+",suffix:"",display:t=>t,parse:t=>t}],Ab=(t,e)=>t.prefix+t.display(e|0)+t.suffix;class Rb extends I.Component{fieldSteps(e,n){return(n|0)/e.increment}allocatedSteps(e){return Rn.reduce((n,r)=>n+this.fieldSteps(r,e[r.key]),0)}maxSteps(){const{ship:e,system:n}=this.props;return typeof n.getMaxSteps=="function"?n.getMaxSteps(e):Math.floor(shipManager.movement.getRemainingEngineThrust(e)/Es)}totalShots(e){return(e.guns|0)+(e.shotBoost|0)}maxDamageSteps(e){return this.totalShots(e)}clampDamageToShots(e){const n=Rn.find(i=>i.key==="dmgBoost5"),r=this.maxDamageSteps(e)*n.increment;(e.dmgBoost5|0)>r&&(e.dmgBoost5=r)}refresh(){const{ship:e,system:n}=this.props;this.forceUpdate(),webglScene.customEvent("SystemDataChanged",{ship:e,system:n})}setValue(e,n){const{ship:r,system:i}=this.props;if(!gamedata.isMyShip(r))return;let o=Math.max(0,Math.round((n|0)/e.increment)*e.increment);const s=this.allocatedSteps(i)-this.fieldSteps(e,i[e.key]),l=Math.max(0,this.maxSteps()-s);if(o/e.increment>l&&(o=l*e.increment),e.key==="dmgBoost5"){const d=this.maxDamageSteps(i)*e.increment;o>d&&(o=d)}i[e.key]=o,e.key==="shotBoost"&&this.clampDamageToShots(i),typeof i.updateBoostNotes=="function"&&i.updateBoostNotes(),this.syncFlight(i),this.refresh()}syncFlight(e){const n=this.getFlightPulsars();for(let r=0;r<n.length;r++){const i=n[r];i!==e&&(Rn.forEach(o=>{i[o.key]=e[o.key]|0}),typeof i.updateBoostNotes=="function"&&i.updateBoostNotes())}}step(e,n){const{system:r}=this.props;this.setValue(e,(r[e.key]|0)+n*e.increment)}onWheel(e,n){n.preventDefault(),this.step(e,n.deltaY<0?1:-1)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,""),i=r===""?0:parseInt(r,10);this.setValue(e,e.parse(i))}propagate(){const{ship:e,system:n}=this.props;if(!gamedata.isMyShip(e))return;const r=this.getFlightPulsars();for(let i=0;i<r.length;i++){const o=r[i];o!==n&&(Rn.forEach(s=>{o[s.key]=n[s.key]|0}),this.clampWeaponToBudget(o),typeof o.updateBoostNotes=="function"&&o.updateBoostNotes())}this.refresh()}getFlightPulsars(){const{ship:e}=this.props,n=[],r=e&&e.systems?e.systems:[];for(let i=0;i<r.length;i++){const o=r[i]&&r[i].systems?r[i].systems:[];for(let s=0;s<o.length;s++)o[s]&&o[s].name==="MinorThoughtPulsar"&&n.push(o[s])}return n}clampWeaponToBudget(e){const n=["dmgBoost5","shotBoost","hitBoost5"],r=o=>Rn.find(s=>s.key===o);let i=0;for(;i++<200&&!(Rn.reduce((s,l)=>s+(e[l.key]|0)/l.increment,0)<=this.maxSteps());)for(let s=0;s<n.length;s++){const l=n[s];if((e[l]|0)>0){e[l]=(e[l]|0)-r(l).increment;break}}}render(){const{ship:e,system:n}=this.props;if(!n)return null;const r=typeof n.getSpareThrust=="function"?n.getSpareThrust(e):shipManager.movement.getRemainingEngineThrust(e),i=this.allocatedSteps(n)*Es,o=Math.max(0,r-i),s=o>=Es;return a.jsxs(Tb,{children:[a.jsx(Mb,{children:"Minor Thought Pulsar"}),a.jsxs(jb,{$empty:o<Es,children:["Available thrust: ",o]}),Rn.map(l=>{const c=n[l.key]|0,d=l.key==="dmgBoost5"&&c/l.increment>=this.maxDamageSteps(n);return a.jsxs(Eb,{children:[a.jsx(Db,{children:l.label}),a.jsx(Pg,{title:"Less",disabled:c<=0,onClick:()=>this.step(l,-1),children:"−"}),a.jsx(Pb,{type:"text",value:Ab(l,c),onChange:h=>this.onInput(l,h),onWheel:h=>this.onWheel(l,h)}),a.jsx(Pg,{title:d?"One +5 per shot (add shots for more)":"More",disabled:!s||d,onClick:()=>this.step(l,1),children:"+"})]},l.key)})]})}}const Ii=t=>{let e=null;const n=r=>{r.preventDefault(),t(r)};return r=>{e!==r&&(e&&e.removeEventListener("wheel",n,{passive:!1}),e=r,e&&e.addEventListener("wheel",n,{passive:!1}))}},P={bg:"linear-gradient(180deg, rgb(27, 45, 62), rgb(18, 32, 45))",line:"#2a6b8f",radius:"6px",shadow:"0 8px 28px rgba(0, 0, 0, 0.6)",titleBg:"linear-gradient(180deg, rgb(54, 79, 110), rgb(36, 57, 80))",title:"#c6e2ff",text:"#deebff",dim:"#8ca5c0",btnBg:"#081420",btnText:"#deebff",well:"#000000",focus:"#8bcaf2"},W={enh:{rail:m.colors.enhLine,bar:"#c39a52",wash:m.colors.enhBg,title:m.colors.enhTitle,btnBg:"#292114",btnText:m.colors.enhTitle},damage:{rail:"#2f7f92",bar:"#4cb8d0",wash:"rgba(58, 159, 181, 0.26)",title:"#e0f5fa",btnBg:"#0f262d",btnText:"#b6e3ee"},crit:{rail:"#a85c33",bar:"#dd7643",wash:"rgba(168, 92, 51, 0.34)",title:"#ffe8dc",btnBg:"#291914",btnText:"#eab99e"},officer:{rail:"#5d79a8",bar:"#90b1ee",wash:"rgba(144, 177, 238, 0.24)",title:"#ffffff",btnBg:"#141b29",btnText:"#b8cdf2"}},Ib={rail:P.line,bar:P.line,btnBg:P.btnBg,btnText:P.btnText},At=t=>t.$ink||Ib,Ob=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px 4px 10px;
    font-size: 11px;
    color: ${t=>t.$gold?m.colors.enhText:P.text};
`,Lb=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,zb=u.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;u.span`
    flex: 0 0 auto;
    color: ${t=>t.$gold?m.colors.enhText:P.dim};
    font-size: 10px;
    opacity: ${t=>t.$gold?.75:1};
`;const Tr=u.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${t=>At(t).btnBg};
    border: 1px solid ${t=>At(t).rail};
    border-radius: 2px;
    color: ${t=>At(t).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${t=>At(t).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${t=>t.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${At(t).btnBg};
            color: ${At(t).btnText};
        }
    `}
`,Ag=u.input`
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
    background-color: ${P.well};
    border: 1px solid ${t=>At(t).rail};
    border-radius: 2px;
    outline: none;

    &:focus {
        border-color: ${t=>At(t).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,Rg=u.div`
    padding: 6px 10px 5px;
    background: ${P.titleBg};
    border-bottom: 1px solid ${P.line};
    color: ${P.title};
    text-align: left;
    font-family: ${m.fonts.display};
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    user-select: none;
    ${t=>t.$sticky?"position: sticky; top: 0; z-index: 1;":""}
`,Ds=t=>`
    color: ${t.title};
    background-color: ${t.wash};
    background-image: linear-gradient(to right, ${t.wash}, rgba(0, 0, 0, 0) 75%);
    border-left: 3px solid ${t.bar};
    border-top: 1px solid ${t.rail};
    border-bottom: 1px solid ${t.rail};
`,Ps=u.div`
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
`,As=u.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 3px 0 0 ${t=>At(t).bar};
`,Bb=u(Ps)`
    ${Ds(W.enh)}
`,Nb=u(Ps)`
    ${Ds(W.officer)}
`,Fb=u(Ps)`
    ${Ds(W.damage)}
`,_b=u(Ps)`
    ${Ds(W.crit)}
`,Ig=u.div`
    height: 2px;
    background-color: ${t=>t.$chrome?P.line:m.colors.enhLine};
    opacity: 0.8;
`,Hb=u.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,Wb=_b,Ub=u.div`
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
`,Gb=u.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,Vb=u.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${P.dim};
`,Yb=u.div`
    flex: 0 0 auto;
    color: ${P.dim};
`,Kb=u.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,Qb=u.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${m.fonts.mono};
    font-size: 11px;
    color: ${t=>t.$empty?"#6f6257":"#ffffff"};
`,Xb=u.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,Jb=u.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,qb=u.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px 6px 10px;
`,Zb=u.select`
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
`;const Og=(t,e,n,r)=>{const i=[];for(const o in t||{}){if(!t.hasOwnProperty(o))continue;const s=battleDamage.PARAM_CRITICALS[o],l=parseInt((r||{})[o],10)||0;i.push({type:o,isParam:!!s,paramLabel:s?s.label:null,label:battleDamage.critLabel(o,e,l),count:parseInt(t[o],10)||0,param:l,transient:!!(n&&n[o])})}return i};class Lg extends I.Component{constructor(e){super(e),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(e){e.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Ii(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}critMap(){const e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.c?Object.assign({},e.c):{}}paramMap(){const e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.p?Object.assign({},e.p):{}}valueOf(e){return e.isParam?e.param:e.count}maxValueOf(e){if(!e.isParam)return battleDamage.critLimit(e.type);const n=battleDamage.PARAM_CRITICALS[e.type];return Math.min(battleDamage.MAX_CRIT_PARAM,n&&n.max||battleDamage.MAX_CRIT_PARAM)}setValue(e,n){const{ship:r,kind:i,reference:o,onChange:s}=this.props,l=this.critMap(),c=this.paramMap();n>0?e.isParam?(l[e.type]=1,c[e.type]=Math.min(n,this.maxValueOf(e))):l[e.type]=Math.min(n,battleDamage.critLimit(e.type)):(delete l[e.type],delete c[e.type]),battleDamage.setCriticals(r,i,o,l,c),s&&s()}step(e,n){const r=this.rowForType(e);this.setValue(r,this.valueOf(r)+n)}rowForType(e){const{ship:n}=this.props,r=battleDamage.PARAM_CRITICALS[e],i=parseInt(this.paramMap()[e],10)||0;return{type:e,isParam:!!r,paramLabel:r?r.label:null,label:battleDamage.critLabel(e,n.preBattleCritDesc,i),count:parseInt(this.critMap()[e],10)||0,param:i,transient:!!(n.preBattleCritTransient&&n.preBattleCritTransient[e])}}displayRows(e){const{ship:n,kind:r,reference:i}=this.props;return battleDamage.rememberCriticals(n,r,i,(e||[]).map(s=>s.type)).map(s=>this.rowForType(s))}addableTypes(e){const{ship:n,kind:r,reference:i}=this.props,o=battleDamage.offerableCriticals(n,r,i,this.state.showAll),s={};return e.forEach(l=>{s[l]=!0}),o.filter(l=>!s[l]).map(l=>({type:l,label:this.pickerLabel(l)})).sort((l,c)=>l.label.localeCompare(c.label))}pickerLabel(e){const n=battleDamage.PARAM_CRITICALS[e];return n?n.label:battleDamage.critLabel(e,this.props.ship.preBattleCritDesc,0)}onAdd(e){e&&this.setValue(this.rowForType(e),1)}render(){const{ship:e,rows:n,editable:r}=this.props,i=r?this.displayRows(n):n||[],o=!!(r&&battleDamage.catalogueFor(e)),s=o?this.addableTypes(i.map(l=>l.type)):[];return!i.length&&!o?null:a.jsxs(Hb,{children:[a.jsx(Ig,{$chrome:!0}),a.jsx(Wb,{children:"Critical Effects"}),a.jsxs(As,{$ink:W.crit,children:[i.map(l=>{const c=this.valueOf(l),d=this.maxValueOf(l);return a.jsxs(Ub,{$empty:r&&c<=0,children:[a.jsxs(Gb,{title:l.type,children:[r&&l.isParam?l.paramLabel:l.label,l.transient&&a.jsx(Vb,{children:"(turn 1 only)"})]}),r?a.jsxs(Kb,{children:[a.jsx(Tr,{$ink:W.crit,title:l.isParam?"Reduce":"One fewer",disabled:c<=0,onClick:()=>this.step(l.type,-1),children:"−"}),a.jsx(Qb,{$empty:c<=0,ref:this.wheelRef(l.type),children:c}),a.jsx(Tr,{$ink:W.crit,title:l.isParam?"Increase":c>=d&&d===1?"This effect only applies once":"One more",disabled:c>=d,onClick:()=>this.step(l.type,1),children:"+"})]}):l.count>1&&a.jsxs(Yb,{children:["(x",l.count,")"]})]},l.type)}),o&&a.jsx(qb,{children:a.jsxs(Zb,{value:"",disabled:s.length===0,title:"Add a critical effect to this unit before the battle",onChange:l=>this.onAdd(l.target.value),children:[a.jsx("option",{value:"",children:s.length?"+ Add effect…":"Nothing to add"}),s.map(l=>a.jsx("option",{value:l.type,children:l.label},l.type))]})})]})]})}}const Bc=u.div`
    display: flex;
    justify-content: flex-end;
    padding: 1px 8px 5px 10px;
    font-family: ${m.fonts.mono};
    font-size: 10px;
    color: ${t=>t.$color||m.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,zg=W.officer.btnText,eS=u(Bc)`
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding-top: 2px;
    opacity: 1;
`,tS=u.span`
    margin-left: auto;
    opacity: 0.85;
`,Bg=u.div`
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
    font-family: ${m.fonts.mono};
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
`,nS=u.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${m.fonts.mono};
    font-size: 10px;
    color: ${t=>t.$color||m.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${t=>t.$spent?.6:1};
`;class Ng extends I.Component{constructor(e){super(e),this.wheelRef=Ii(n=>this.step(n.deltaY<0?1:-1))}step(e){const{row:n,onChange:r}=this.props;if(n.fixed)return;const i=Math.max(0,Math.min(n.max,n.count+e));i!==n.count&&r(n.enhID,i)}onInput(e){const{row:n,onChange:r}=this.props,i=String(e.target.value).replace(/[^0-9]/g,""),o=i===""?0:parseInt(i,10);r(n.enhID,Math.max(0,Math.min(n.max,o)))}render(){const{row:e}=this.props,n=e.count>=e.max,r=(e.max>1?`${e.label} - ${e.count}/${e.max} levels`+(e.count>0?`, ${e.price} pts spent`:"")+(n?"":`; next level ${e.nextPrice} pts`):`${e.label} - ${e.price||e.nextPrice} pts`)+(e.note?`
${e.note}`:"")+(e.blocked?`
${e.blocked}`:""),i=!!e.blocked&&e.count<1,o=e.officer?W.officer:W.enh,s=!!e.fixed,l=`${e.label} - bought with the ship, in its Officers section (Edit the ship to remove him). Move posts him elsewhere.`;return a.jsxs(Ob,{$gold:!e.officer,title:s?l:r,style:i?{opacity:.55}:void 0,children:[a.jsx(Lb,{children:a.jsx(zb,{children:e.label})}),a.jsx(Tr,{$ink:o,title:s?l:"Remove a level",disabled:s||e.count<=0,onClick:()=>{s||this.step(-1)},children:"−"}),a.jsx(Ag,{ref:this.wheelRef,$ink:o,type:"text",value:e.count,readOnly:s,onChange:c=>{s||this.onInput(c)}}),a.jsx(Tr,{$ink:o,title:s?l:i?e.blocked:e.count>=e.max?"Already at the maximum":`Add a level (${e.nextPrice} pts)`,disabled:s||i||e.count>=e.max,onClick:()=>{s||this.step(1)},children:"+"}),a.jsx(nS,{$spent:n,$color:e.officer?o.title:void 0,title:n?`Fully bought - ${e.price} pts`:"Cost of the next level",children:n?`${e.price}p`:`${e.nextPrice}p`})]})}}const rS=u(Bg)`
    ${t=>t.$on&&`
        background: ${W.officer.bar};
        color: #0b1520;
        cursor: default;
        &:hover {
            background: ${W.officer.bar};
            color: #0b1520;
        }
    `}
`,iS=u.div`
    flex: 0 0 auto;
    display: flex;
    gap: 2px;
`;class oS extends I.Component{renderSides(e){const{onSide:n}=this.props;return typeof n!="function"?null:a.jsx(iS,{children:e.sides.map(r=>a.jsx(rS,{$on:r.current,title:r.current?`The ${e.label} covers the ${r.name} weapons`:`Cover the ${r.name} weapons instead (free in Fleet Selection)`,onClick:i=>{i.stopPropagation(),r.current||n(e.enhID)},children:r.name},r.id))})}renderMove(e){const{onMove:n}=this.props;if(e&&Array.isArray(e.sides))return this.renderSides(e);if(!e||typeof n!="function")return null;const r=e.moveTo.length>0,i=r?`Move the ${e.label} to another post: click Move, then a highlighted system in the ship window (free in Fleet Selection)`:`There is no other post the ${e.label} can take on this ship right now`;return a.jsx(Bg,{$disabled:!r,title:i,onClick:o=>{o.stopPropagation(),r&&n(e.enhID)},children:"Move"})}render(){const{rows:e,onChange:n}=this.props;if(!e||e.length===0)return null;const r=e.filter(d=>!d.officer),i=e.filter(d=>d.officer),o=d=>d.reduce((h,p)=>h+(p.count>0?p.price:0),0),s=o(r),l=o(i),c=i.find(d=>d.count>0&&(Array.isArray(d.moveTo)||Array.isArray(d.sides)));return a.jsxs(ie.Fragment,{children:[r.length>0&&a.jsx(Bb,{children:"✦ ENHANCEMENTS"}),r.length>0&&a.jsxs(As,{$ink:W.enh,children:[r.map(d=>a.jsx(Ng,{row:d,onChange:n},d.enhID)),s>0&&a.jsxs(Bc,{children:["Refits: ",s," pts"]})]}),i.length>0&&a.jsx(Nb,{children:"Officers"}),i.length>0&&a.jsxs(As,{$ink:W.officer,children:[i.map(d=>a.jsx(Ng,{row:d,onChange:n},d.enhID)),c?a.jsxs(eS,{$color:zg,children:[this.renderMove(c),l>0&&a.jsxs(tS,{children:["Officers: ",l," pts"]})]}):l>0&&a.jsxs(Bc,{$color:zg,children:["Officers: ",l," pts"]})]})]})}}const sS=u.div`
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
`,aS=u.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px 5px 10px;
    font-size: 11px;
    color: ${P.text};
`,lS=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,cS=u.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${t=>t.$disabled?"not-allowed":"pointer"};
    user-select: none;
    opacity: ${t=>t.$disabled?.4:1};
    color: ${t=>t.$on?"#ff8a80":P.dim};
`,dS=u.span`
    flex: 0 0 auto;
    color: ${P.dim};
    font-size: 10px;
`,uS=u.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;class hS extends I.Component{constructor(e){super(e),this.wheelRef=Ii(n=>this.step(n.deltaY<0?1:-1))}entry(){const{ship:e,system:n}=this.props;return battleDamage.getEntry(e,battleDamage.KIND_SYSTEM,n.id)||{}}remaining(){const{system:e}=this.props,n=this.entry();return n.k?0:Math.max(0,e.maxhealth-(parseInt(n.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return this.isIndestructible()?1:0}setRemaining(e){const{ship:n,system:r}=this.props,i=r.maxhealth,o=Math.min(this.floor(),i);let s=parseInt(e,10);isNaN(s)&&(s=i),s=Math.max(o,Math.min(i,s));const l=i-s,c=s===0&&i>0;c&&this.rememberHealth(),battleDamage.setSystem(n,r.id,{d:l,k:c?1:0}),this.refresh()}rememberHealth(){const{ship:e,system:n}=this.props;this.isDestroyed()||battleDamage.rememberHealth(e,battleDamage.KIND_SYSTEM,n.id,this.remaining())}setDestroyed(e){const{ship:n,system:r}=this.props;if(e&&this.isIndestructible())return;if(e){this.rememberHealth(),battleDamage.setSystem(n,r.id,{d:r.maxhealth,k:1}),this.refresh();return}const i=battleDamage.healthMemory(n,battleDamage.KIND_SYSTEM,r.id),o=i>0?Math.min(r.maxhealth,i):r.maxhealth;battleDamage.setSystem(n,r.id,{d:r.maxhealth-o,k:0}),this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e);let n=[];if(window.systemEnhancements){const r=parseFloat(e.pointCostSysEnh)||0;n=systemEnhancements.dropDestroyed(e),n.length&&this.settleRefitCost(e,r)}if(window.officers&&typeof officers.dropDestroyedPosts=="function"){const r=officers.dropDestroyedPosts(e);r.length&&(n=n.concat(r),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet())}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate(),n.length&&window.confirm&&typeof confirm.warning=="function"&&confirm.warning(systemEnhancements.describeRemoved(n))}settleRefitCost(e,n){const r=parseFloat(e.pointCostSysEnh)||0;e.pointCost=(parseFloat(e.pointCost)||0)-(n-r),window.gamedata&&typeof gamedata.calculateFleet=="function"&&gamedata.calculateFleet()}setEnhancement(e,n){const{ship:r,system:i}=this.props;if(!window.systemEnhancements)return;const o=parseFloat(r.pointCostSysEnh)||0,s=systemEnhancements.taken(r,i.id,e);if(n===s)return;if(systemEnhancements.set(r,i.id,e,n),this.settleRefitCost(r,o),!(!window.gamedata||typeof gamedata.canAffordRefit!="function"||gamedata.canAffordRefit(r))){const c=parseFloat(r.pointCostSysEnh)||0;systemEnhancements.set(r,i.id,e,s),this.settleRefitCost(r,c),systemEnhancements.apply(r),this.forceUpdate(),window.confirm&&typeof confirm.error=="function"&&confirm.error("You cannot afford that enhancement!",function(){});return}systemEnhancements.apply(r),this.refresh()}startMove(e){const{ship:n,system:r}=this.props;if(!window.officers)return;const i=officers.lobbyEntryAt(n,r.id,e);!i||officers.lobbyDestinations(n,i).length===0||(officers.startPick(n,i),window.uiEvents&&window.uiEvents.relay("CloseSystemInfo"))}switchSide(e){const{ship:n,system:r}=this.props;if(!window.officers)return;const i=officers.lobbyEntryAt(n,r.id,e);i&&officers.lobbySwitchSide(n,i)&&this.forceUpdate()}step(e){this.setRemaining(this.remaining()+e)}onInput(e){const n=String(e.target.value).replace(/[^0-9]/g,"");this.setRemaining(n===""?0:parseInt(n,10))}render(){const{ship:e,system:n}=this.props;if(!n||!(n.maxhealth>0))return null;const r=this.remaining(),i=this.isDestroyed(),o=this.isIndestructible(),s=this.entry(),l=Og(s.c,e.preBattleCritDesc,e.preBattleCritTransient,s.p),c=window.systemEnhancements&&!i?systemEnhancements.menuRowsFor(e,n):[];return a.jsxs(sS,{onClick:d=>d.stopPropagation(),children:[a.jsx(oS,{rows:c,onChange:(d,h)=>this.setEnhancement(d,h),onMove:d=>this.startMove(d),onSide:d=>this.switchSide(d)}),c.length>0&&a.jsx(Ig,{}),a.jsx(Fb,{children:"Damage"}),a.jsx(As,{$ink:W.damage,children:a.jsxs(aS,{children:[a.jsxs(lS,{title:`${n.displayName||n.name} (system id ${n.id})`,children:[a.jsx(uS,{children:n.displayName||n.name}),a.jsxs(dS,{children:["#",n.id]})]}),a.jsx(Tr,{$ink:W.damage,title:o&&r<=1?"A reactor cannot be destroyed before the battle":"More damage",disabled:i||r<=this.floor(),onClick:()=>this.step(-1),children:"−"}),a.jsx(Ag,{ref:this.wheelRef,$ink:W.damage,type:"text",$destroyed:i,disabled:i,value:i?0:r,onChange:d=>this.onInput(d)}),a.jsx(Tr,{$ink:W.damage,title:"Repair",disabled:i||r>=n.maxhealth,onClick:()=>this.step(1),children:"+"}),a.jsxs(cS,{$on:i,$disabled:o,title:o?"A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship":"Mark this system destroyed before the battle starts",children:[a.jsx(Xb,{type:"checkbox",checked:i,disabled:o,onChange:d=>this.setDestroyed(d.target.checked)}),a.jsx(Jb,{children:"Destroy"})]})]})}),a.jsx(Lg,{ship:e,kind:battleDamage.KIND_SYSTEM,reference:n.id,rows:l,editable:!0,onChange:()=>this.refresh()})]})}}const ct=W.officer,pS=u.div`
    display: flex;
    flex-direction: column;
    width: 100%;
    min-width: 190px;
    contain: inline-size;
    background-color: rgba(16, 26, 38, 0.92);
    border: 1px solid ${ct.rail};
`,fS=u.div`
    padding: 3px;
    background: ${ct.wash};
    border-left: 3px solid ${ct.bar};
    border-bottom: 1px solid ${ct.rail};
    color: ${ct.title};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
`,Fg=u.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px;
    font-size: 11px;
    color: #deebff;
`,_g=u.div`
    padding: 0 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: #8ca5c0;
    border-bottom: 1px solid ${ct.rail};

    &:last-child {
        border-bottom: none;
    }
`,Nc=u.div`
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
`,gS=u.div`
    display: flex;
    gap: 2px;
`,mS=u(Nc)`
    ${t=>t.$on&&`
        background: ${ct.bar};
        color: #0b1520;
        cursor: default;
    `}
`;class xS extends I.Component{move(e,n){e.stopPropagation(),e.preventDefault(),officers.startPick(this.props.ship,n),webglScene.customEvent("CloseSystemInfo")}cancel(e,n){e.stopPropagation(),e.preventDefault(),officers.cancelTransfer(this.props.ship,n)}side(e,n,r,i){e.stopPropagation(),e.preventDefault(),r.current?i&&officers.cancelTransfer(this.props.ship,n):i||officers.setTransfer(this.props.ship,n,n.post)}renderSides(e,n,r,i,o){const s=n.find(l=>!l.current);return a.jsxs(ie.Fragment,{children:[a.jsxs(Fg,{children:[a.jsx("span",{children:officers.label(e.id)}),a.jsx(gS,{children:n.map(l=>{const c=r?!l.current:l.current;return a.jsx(mS,{$on:c,onClick:d=>this.side(d,e,l,r),title:c?`He covers the ${l.name} weapons`:`Cover the ${l.name} weapons instead`,children:l.name},l.id)})})]}),a.jsx(_g,{children:r?`Switching to ${s.name}: he covers nothing for the rest of this turn, and the ${s.name} weapons from turn ${o}.`:"Switching sides takes the rest of the turn: he covers nothing until the next."})]},`${e.id}-${i}`)}render(){const{ship:e,system:n}=this.props,r=officers.currentTurn()+1;return a.jsxs(pS,{children:[a.jsx(fS,{children:"Officers"}),officers.movableOn(e,n.id).map((i,o)=>{const s=officers.transferOf(e,i),l=officers.sideChoices(e,i);return l?this.renderSides(i,l,s,o,r):a.jsxs(ie.Fragment,{children:[a.jsxs(Fg,{children:[a.jsx("span",{children:officers.label(i.id)}),s?a.jsx(Nc,{title:"Keep him at this post",onClick:c=>this.cancel(c,i),children:"Cancel Move"}):a.jsx(Nc,{title:"Then click the post to move him to",onClick:c=>this.move(c,i),children:"Move Officer"})]}),a.jsx(_g,{children:s?`To ${officers.systemName(e,s.to)}: he serves nowhere for the rest of this turn, and there from turn ${r}.`:"An officer who moves serves nowhere for the rest of the turn."})]},`${i.id}-${o}`)})]})}}const Hg=u.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,Wg=u.div`
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
`,Ug=u.span`
    color: #ffd27a;
    font-size: 16px;
    line-height: 1;
    text-shadow: black 0 0 3px, black 0 0 3px;
    pointer-events: none;
`;class yS extends I.Component{constructor(e){super(e)}online(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onOnlineClicked(n,r),webglScene.customEvent("CloseSystemInfo")}offline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Os(n,r)&&(shipManager.power.onOfflineClicked(n,r),webglScene.customEvent("CloseSystemInfo"))}allOnline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onlineAll(n,r),webglScene.customEvent("CloseSystemInfo")}allOffline(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Os(n,r)&&(shipManager.power.offlineAll(n,r),webglScene.customEvent("CloseSystemInfo"))}overload(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onOverloadClicked(n,r),webglScene.customEvent("CloseSystemInfo")}stopOverload(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.onStopOverloadClicked(n,r),webglScene.customEvent("CloseSystemInfo")}boost(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.clickPlus(n,r)}deboost(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;shipManager.power.clickMinus(n,r)}addShots(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;ed(n,r)&&weaponManager.changeShots(n,r,1)}reduceShots(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;td(n,r)&&weaponManager.changeShots(n,r,-1)}removeFireOrderMulti(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ls(n,r)&&weaponManager.removeFiringOrderMulti(n,r)}removeFireOrder(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Li(n,r)&&(weaponManager.removeFiringOrder(n,r),webglScene.customEvent("CloseSystemInfo"))}removeFireOrderAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Li(n,r)&&(weaponManager.removeFiringOrderAll(n,r),webglScene.customEvent("CloseSystemInfo"))}allChangeFiringMode(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(Mr(n,r)){weaponManager.onModeClicked(n,r);var i=r.firingMode,o=[];n.flight?o=n.systems.map(f=>f.systems).reduce((f,v)=>f.concat(v),[]).filter(f=>f.weapon):o=n.systems.filter(f=>f.weapon);for(var s=weaponManager.stripPairingSuffix(r.displayName),l=new Array,c=0;c<o.length;c++)s===weaponManager.stripPairingSuffix(o[c].displayName)&&r.weapon&&l.push(o[c]);for(var c=0;c<l.length;c++){var d=l[c];if(d.firingMode!=i&&Mr(n,d)){for(var h=d.firingMode,p=0;d.firingMode!=i&&p<2;)weaponManager.onModeClicked(n,d),d.firingMode==1&&p++;if(d.firingMode!=i)for(;d.firingMode!=h;)weaponManager.onModeClicked(n,d)}}}}changeFiringMode(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Mr(n,r)&&weaponManager.onModeClicked(n,r)}selectAllWeapons(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.selectAllWeapons(n,r,"forceSelect"),webglScene.customEvent("CloseSystemInfo")}deselectAllWeapons(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.selectAllWeapons(n,r,"forceDeselect"),webglScene.customEvent("CloseSystemInfo")}declareSelfIntercept(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(zs(n,r)){if(weaponManager.onDeclareSelfInterceptSingle(n,r),r.canSplitShots)var i=r.checkFinished();i&&webglScene.customEvent("CloseSystemInfo")}}declareSelfInterceptAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(n,r),r.canSplitShots)var i=r.checkFinished();i&&webglScene.customEvent("CloseSystemInfo")}remSelfIntercept(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Bs(n,r)&&weaponManager.removeSelfInterceptSingle(n,r)}declareMeteorDefence(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Ns(n,r)&&(weaponManager.onDeclareMeteorDefence(n,r),webglScene.customEvent("CloseSystemInfo"))}declareMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.onDeclareMeteorDefenceAll(n,r),webglScene.customEvent("CloseSystemInfo")}remMeteorDefence(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;Fs(n,r)&&(weaponManager.removeMeteorDefence(n,r),webglScene.customEvent("CloseSystemInfo"))}remMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;weaponManager.removeMeteorDefenceAll(n,r),webglScene.customEvent("CloseSystemInfo")}nextCurrClass(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;r.nextCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}prevCurrClass(e){e.stopPropagation(),e.preventDefault();const{ship:n,system:r}=this.props;r.prevCurrClass(),webglScene.customEvent("SystemDataChanged",{ship:n,system:r})}render(){const{ship:e,selectedShip:n,system:r}=this.props;return Is(e,r)?gamedata.gamephase===-2?a.jsx(Hg,{children:a.jsx(hS,{ship:e,system:r})}):a.jsxs(Hg,{children:[tm(e,r)&&a.jsx(ib,{ship:e,system:r}),a.jsxs(Wg,{children:[ed(e,r)&&a.jsx(wt,{title:"More shots",onClick:this.addShots.bind(this),img:"./img/plussquare.png"}),td(e,r)&&a.jsx(wt,{title:"Less shots",onClick:this.reduceShots.bind(this),img:"./img/minussquare.png"})]}),Zg(e,r)&&a.jsxs(Nv,{ship:e,system:r,showModes:!!Mr(e,r),children:[zs(e,r)&&a.jsx(wt,{title:"Allow interception (RMB = All systems selected)",onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:"./img/addSelfIntercept.png"}),Bs(e,r)&&a.jsx(wt,{title:"Remove an intercept order",onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:"./img/remSelfIntercept.png"}),Ns(e,r)&&a.jsx(wt,{title:"Meteor Defence: commit this weapon to defend against meteors this turn - it cannot fire or intercept (RMB = all similar weapons)",onClick:this.declareMeteorDefence.bind(this),onContextMenu:this.declareMeteorDefenceAll.bind(this),img:"./img/selfIntercept.png",children:a.jsx(Ug,{children:"☄"})}),Fs(e,r)&&a.jsx(wt,{title:"Remove Meteor Defence (RMB = all similar weapons)",onClick:this.remMeteorDefence.bind(this),onContextMenu:this.remMeteorDefenceAll.bind(this),img:"./img/remSelfIntercept.png",children:a.jsx(Ug,{children:"☄"})}),Ls(e,r)&&a.jsx(wt,{title:"Remove last fire order",onClick:this.removeFireOrderMulti.bind(this),img:"./img/unfiringSmall.png"}),Li(e,r)&&a.jsx(wt,{title:"Remove all fire orders (RMB = All weapons selected)",onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:"./img/firing.png"})]}),a.jsxs(Wg,{children:[Fc(e,r)&&a.jsx(wt,{title:"Select all weapons of this type",onClick:this.selectAllWeapons.bind(this),img:"./img/selectAllWeapons.png",$blend:"screen"}),Fc(e,r)&&a.jsx(wt,{title:"Deselect all weapons of this type",onClick:this.deselectAllWeapons.bind(this),img:"./img/deselectAllWeapons.png",$blend:"screen"})]}),id(e,r)&&a.jsx(eb,{ship:e,system:r}),_c(e,r)&&a.jsx(w1,{ship:e,system:r}),Vc(e,r)&&a.jsx(j1,{system:r,ship:e}),Yc(e,r)&&a.jsx(L1,{system:r,ship:e}),Uc(e,r)&&a.jsx(fb,{system:r,ship:e}),Gc(e,r)&&a.jsx(Sb,{system:r,ship:e}),Hc(e,r)&&a.jsx(Cb,{system:r,ship:e}),Wc(e,r)&&a.jsx(Rb,{system:r,ship:e}),(Kc(e,r)||Qc(e,r))&&a.jsx(xg,{system:r,ship:e}),(Xc(e,r)||Jc(e,r))&&a.jsx(xg,{system:r,ship:e}),Rs(e,r)&&a.jsx(qv,{ship:e,system:r,readOnly:!wS(e,r)}),qc(e,r)&&a.jsx(d1,{ship:e,system:r,readOnly:!vS(e,r)}),"   ",_s(e,r)&&a.jsx(G1,{ship:e,system:r}),em(e,r)&&a.jsx(Q1,{ship:e,system:r}),rd(e,r)&&a.jsx(xS,{ship:e,system:r})]}):null}}const Fc=(t,e)=>!(!window.matchMedia("(pointer: coarse)").matches||!e.weapon||Oi(t,e)||gamedata.gamephase!=3&&!e.ballistic&&!e.preFires||gamedata.gamephase!=1&&e.ballistic||gamedata.gamephase!=5&&e.preFires),_c=(t,e)=>gamedata.gamephase===1&&e.name=="adaptiveArmorController",Hc=(t,e)=>e.name==="GraviticAugmenter"&&gamedata.isMyShip(t)&&!e.stowed&&!shipManager.power.isOffline(t,e)&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(t,e)||gamedata.gamephase===5),Wc=(t,e)=>e.name==="MinorThoughtPulsar"&&gamedata.gamephase===3&&gamedata.isMyShip(t)&&!e.stowed&&!shipManager.systems.isDestroyed(t,e)&&!shipManager.power.isOffline(t,e),Uc=(t,e)=>gamedata.gamephase===-1&&t.mine&&(t.spawned==-1&&gamedata.turn==1||t.spawned==gamedata.turn-1)&&(e.name=="CaptorMine"||e.name=="MineControllerDEW"),Gc=(t,e)=>gamedata.gamephase===-1&&t.mine&&(t.spawned==-1&&gamedata.turn==1||t.spawned==gamedata.turn-1)&&e.name=="ProximityMine",Vc=(t,e)=>e.name=="hyachComputer"&&(gamedata.gamephase===1||gamedata.gamephase===3&&typeof e.firingMode=="function"&&e.firingMode()!==null),Yc=(t,e)=>e.name==="hyachSpecialists",Kc=(t,e)=>gamedata.gamephase===1&&e.name==="ThirdspaceShield",Qc=(t,e)=>gamedata.gamephase===1&&e.name==="ThirdspaceShieldGenerator",Xc=(t,e)=>gamedata.gamephase===1&&e.name==="ThoughtShield",Jc=(t,e)=>gamedata.gamephase===1&&e.name==="ThoughtShieldGenerator",Rs=(t,e)=>gamedata.isMyShip(t)&&e.name=="SelfRepair",wS=(t,e)=>Rs(t,e)&&gamedata.gamephase===1,qc=(t,e)=>gamedata.isMyShip(t)&&(e.name=="StructureSelfRepair"||e.name=="CoopStructureSelfRepair"),vS=(t,e)=>qc(t,e)&&gamedata.gamephase===1,Gg=()=>typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted(),cn=(t,e)=>gamedata.gamephase===-2&&!Gg()&&t&&t.userid!=0&&!t.flight&&!t.mine&&!bS(e),Vg=(t,e)=>cn(t,e)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(t,e)&&systemEnhancements.offersFor(t,e).length>0,Zc=t=>gamedata.gamephase===-2&&!Gg()&&t&&t.userid!=0&&!!t.mine&&battleDamage.mineMaxHealth(t)>1,bS=t=>!t||!(t.maxhealth>0)||t.isTargetable===!1&&!t.orbitalMounted||!!t.hideInShipWindow||Array.isArray(t.systems),Is=(t,e)=>gamedata.gamephase===-2?cn(t,e)||Vg(t,e):Os(t,e)||Yg(t,e)||Kg(t,e)||Qg(t,e)||Xg(t,e)||Jg(t,e)||ed(t,e)||td(t,e)||Ls(t,e)||Li(t,e)||Mr(t,e)||zs(t,e)||Bs(t,e)||Ns(t,e)||Fs(t,e)||_c(t,e)||Vc(t,e)||Yc(t,e)||Kc(t,e)||Xc(t,e)||Qc(t,e)||Jc(t,e)||Rs(t,e)||SS(t,e)||kS(t,e)||_s(t,e)||em(t,e)||id(t,e)||Fc(t,e)||Uc(t,e)||Gc(t,e)||Hc(t,e)||Wc(t,e)||rd(t,e),Os=(t,e)=>gamedata.gamephase===1&&(e.canOffLine||e.powerReq>0)&&!e.powerLocked&&!shipManager.power.isOffline(t,e)&&!shipManager.power.getBoost(e)&&!weaponManager.hasFiringOrder(t,e),Yg=(t,e)=>gamedata.gamephase===1&&shipManager.power.isOffline(t,e)&&!shipManager.power.isForcedOffline(t,e)&&!shipManager.power.isVortexLockedOffline(t,e),Kg=(t,e)=>gamedata.gamephase===1&&!shipManager.power.isOffline(t,e)&&e.weapon&&e.overloadable&&!shipManager.power.isOverloading(t,e),Qg=(t,e)=>gamedata.gamephase===1&&e.weapon&&e.overloadable&&shipManager.power.isOverloading(t,e)&&(e.overloadshots>=e.extraoverloadshots||e.overloadshots==0),Xg=(t,e)=>e.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(t,e)&&(!e.isScanner()||e.id==shipManager.power.getHighestSensorsId(t))&&e.name!=="ThirdspaceShieldGenerator"&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor",Jg=(t,e)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(e)&&e.name!=="ThirdspaceShieldGenerator"&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor",qg=(t,e)=>{const n=weaponManager.getFiringOrder(t,e);return n&&n.type!=="intercept"&&n.type!=="selfIntercept"?n:null},ed=(t,e)=>{if(Oi(t,e)||!e.weapon||!e.canChangeShots||!weaponManager.hasFiringOrder(t,e))return!1;const n=qg(t,e);return!!n&&n.shots<e.maxVariableShots},td=(t,e)=>{if(Oi(t,e)||!e.weapon||!e.canChangeShots||!weaponManager.hasFiringOrder(t,e))return!1;const n=qg(t,e);return!!n&&n.shots>1},Oi=(t,e)=>e.name==="jumpEngine"&&typeof e.getHeldVortex=="function"&&!!e.getHeldVortex(),Ls=(t,e)=>e.weapon&&weaponManager.hasOrderForMode(e)&&e.canSplitShots&&!Oi(t,e),Li=(t,e)=>e.weapon&&weaponManager.hasFiringOrder(t,e)&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked())&&!Oi(t,e),nd=t=>!!(window.shipManager&&shipManager.isDockingRider(t)),Mr=(t,e)=>e.weapon&&!t.mine&&!e.stowed&&!e.hideFiringModeSelector&&!nd(t)&&e.name!=="GraviticAugmenter"&&e.name!=="MinorThoughtPulsar"&&(gamedata.gamephase===1&&e.ballistic||gamedata.gamephase===5&&e.preFires||gamedata.gamephase===3&&!e.ballistic&&!e.preFires)&&(!weaponManager.hasFiringOrder(t,e)||e.multiModeSplit)&&!weaponManager.hasMeteorDefence(t,e)&&Object.keys(e.firingModes).length>1,zs=(t,e)=>e.weapon&&!nd(t)&&weaponManager.canSelfInterceptSingle(t,e),Bs=(t,e)=>e.weapon&&e.canSplitShots&&!nd(t)&&weaponManager.canRemInterceptSingle(t,e),Zg=(t,e)=>Mr(t,e)||zs(t,e)||Bs(t,e)||Ns(t,e)||Fs(t,e)||Ls(t,e)||Li(t,e),Ns=(t,e)=>e.weapon&&weaponManager.canDeclareMeteorDefence(t,e),Fs=(t,e)=>e.weapon&&weaponManager.canRemoveMeteorDefence(t,e),SS=(t,e)=>e.canActivate&&typeof e.canActivate=="function"&&e.canActivate()&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor"&&e.name!=="GraviticAugmenter"&&e.name!=="jumpEngine",kS=(t,e)=>e.canDeactivate&&typeof e.canDeactivate=="function"&&e.canDeactivate()&&e.name!=="powerCapacitor"&&e.name!=="PowerCapacitor"&&e.name!=="GraviticAugmenter"&&e.name!=="jumpEngine",em=(t,e)=>e.name==="jumpEngine"&&typeof e.canMaintainVortex=="function"&&(e.canMaintainVortex()||e.canDeactivate()||typeof e.getAbductionOrder=="function"&&!!e.getAbductionOrder()),rd=(t,e)=>!!window.officers&&officers.canManage(t,e),_s=(t,e)=>e.name==="powerCapacitor"||e.name==="PowerCapacitor",tm=(t,e)=>Os(t,e)||Yg(t,e)||Kg(t,e)||Qg(t,e)||e.boostable&&(Xg(t,e)||Jg(t,e)),id=(t,e)=>_s(t,e)||e.name==="GraviticAugmenter"||e.name==="jumpEngine"?!1:!!(e.canActivate&&typeof e.canActivate=="function"&&e.canActivate()||e.canDeactivate&&typeof e.canDeactivate=="function"&&e.canDeactivate()),$S=(t,e)=>gamedata.gamephase===-2?cn(t,e)||Vg(t,e):_c(t,e)||Vc(t,e)||Yc(t,e)||Uc(t,e)||Gc(t,e)||Hc(t,e)||Wc(t,e)||Kc(t,e)||Qc(t,e)||Xc(t,e)||Jc(t,e)||Rs(t,e)||qc(t,e)||_s(t,e)||tm(t,e)||id(t,e)||Zg(t,e)||rd(t,e);let dn=null;function nm(t,e){const n=t&&e?{ship:t,systemId:e.id}:null;n===dn||n&&dn&&n.ship===dn.ship&&n.systemId===dn.systemId||(dn=n,window.shipWindowManagerReact&&window.shipWindowManagerReact.update())}function rm(t,e){return dn!==null&&!!t&&!!e&&dn.ship===t&&dn.systemId===e.id}const im=u.div`
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
`,om=u.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,sm=u.div`
    /*11px on a 32px icon: the 7px it launched at was legible only if you already knew to
      look for it (user report 2026-08-15). Still small enough to clear the icon art and the
      [n/n] load counter, which sits along the BOTTOM edge.*/
    font-size: 11px;
    line-height: 11px;
    color: ${m.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,CS=u.div`
    position: absolute;
    top: 0px;
    left: 1px;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    gap: 1px;
    pointer-events: none;
`,TS=u.svg`
    display: block;
    width: 11px;
    height: 11px;
    overflow: visible;
    filter: drop-shadow(0 0 1px black) drop-shadow(0 0 1px black);
`,od=W.officer.bar,MS={serving:m.colors.enhTitle,disabled:"#c4c4c4",killed:"#c4c4c4",moving:od},sd=({state:t,title:e})=>a.jsxs(TS,{viewBox:"0 0 12 12",role:"img","aria-label":e,children:[a.jsx("title",{children:e}),a.jsx("path",{d:"M1.5 5.5 L6 2 L10.5 5.5 M1.5 10 L6 6.5 L10.5 10",fill:"none",stroke:MS[t],strokeWidth:"2",strokeLinecap:"square"}),t==="killed"&&a.jsx("path",{d:"M1 11.5 L11 0.5",stroke:"#ff4d4f",strokeWidth:"1.8",strokeLinecap:"round"})]}),jS=u.div`
    position: absolute;
    top: 0px;
    right: 1px;
    z-index: 1;
    pointer-events: none;
    font-size: 11px;
    line-height: 11px;
    color: #ffd27a;
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,am=u.div`
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
        outline: 2px solid ${od};
        outline-offset: -2px;
        box-shadow: 0 0 8px ${od};
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
    
    ${om} {
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
`;class ad extends I.Component{constructor(e){super(e),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(e){if(e.stopPropagation(),e.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n);const i=cn(r,n);if((gamedata.waiting||gamedata.replay)&&!i||window.officers&&officers.picking&&officers.pickTarget(r,n))return;const o=!!r.removed&&!shipManager.isDestroyedByDamage(r);if(!(!i&&!o&&(shipManager.isDestroyed(r)||shipManager.isDestroyed(r,n)&&!n.clickableWhenDestroyed))){if(o&&!i){gamedata.isMyShip(r)&&window.uiEvents.relay("SystemClicked",{ship:r,system:n,element:e.currentTarget,showMenu:!0});return}if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(r)){var s=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(s&&s.ship.id!=r.id&&!weaponManager.isSelectedWeapon(n)){window.uiEvents.relay("SystemTargeted",{ship:r,system:n});return}}var l=n.weapon&&typeof n.isSpentLocked=="function"&&n.isSpentLocked(),c=typeof n.canSelectForAbduction=="function"&&n.canSelectForAbduction(r);if(!l&&(n.weapon&&gamedata.gamephase===3&&!n.ballistic&&!n.preFires||gamedata.gamephase===1&&n.ballistic||gamedata.gamephase===5&&n.preFires||weaponManager.canManuallyInterceptWith(r,n)||c)&&!shipManager.isAdrift(r)&&gamedata.isMyShip(r)){if(n.hasSpecialTargeting&&typeof n.reopenSpecialTargeting=="function"&&weaponManager.hasFiringOrder(r,n)&&n.reopenSpecialTargeting(r))return;var d=weaponManager.hasFiringOrder(r,n),h=d&&d!=="self"&&!n.canSplitShots&&!n.hasSpecialTargeting;weaponManager.isSelectedWeapon(n)?weaponManager.unSelectWeapon(r,n):h||weaponManager.selectWeapon(r,n)}if(gamedata.isMyShip(r)&&(n.name==="hangar"||n.name==="catapult"||n.name==="fighterRail")){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog=="function"&&window.DeploymentDock.shipHasOpenableDockDialog(r)&&window.confirm&&typeof window.confirm.hangarDeployDock=="function"){window.confirm.hangarDeployDock(r);return}if(gamedata.gamephase===3&&!n.isShadowHangar&&!shipManager.movement.isRolling(r)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(r)!=="no")&&window.confirm&&typeof window.confirm.hangarLaunch=="function"){window.confirm.hangarLaunch(r);return}}if(gamedata.isMyShip(r)&&(n.name==="dockingCollar"||n.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(r)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(r)!=="no")&&typeof window.lcvRailLaunchable=="function"&&window.lcvRailLaunchable(r,n)&&window.confirm&&typeof window.confirm.lcvLaunch=="function"){window.confirm.lcvLaunch(r);return}gamedata.isMyShip(r)?window.uiEvents.relay("SystemClicked",{ship:r,system:n,element:e.currentTarget,showMenu:!0}):window.uiEvents.relay("SystemTargeted",{ship:r,system:n})}}onSystemMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;e.stopPropagation(),e.preventDefault();let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n),window.uiEvents.relay("SystemMouseOver",{ship:r,system:n,element:e.currentTarget,showInfo:!0})}onSystemMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay("SystemMouseOut"))}onTouchStart(e){e.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:i,ship:o}=this.props;i=shipManager.systems.initializeSystem(i),window.uiEvents.relay("SystemMouseOver",{ship:o,system:i,element:n,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(e){e.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(e){if(e.stopPropagation(),e.preventDefault(),window.matchMedia("(pointer: coarse)").matches)return;let{system:n,ship:r}=this.props;n=shipManager.systems.initializeSystem(n),n.weapon&&weaponManager.selectAllWeapons(r,n)}render(){let{system:e,ship:n,scs:r,fighter:i,destroyed:o,mirror:s}=this.props;return e=shipManager.systems.initializeSystem(e),e=shipManager.systems.initializeSystem(e),(ld(n,e)||o)&&!e.clickableWhenDestroyed&&!cn(n,e)?a.jsxs(am,{$background:um(e),$destroyed:!0,$mirror:s,children:[lm(n,e),a.jsx(im,{$health:"0"})]}):a.jsxs(am,{$scs:r,$highlight:HS(n,e),$destroyed:ld(n,e)||o,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:um(e),$mirror:s,$offline:OS(n,e),$loading:RS(e),$loadedAlternate:IS(e),$selected:WS(e),$firing:ES(n,e),$intercepting:PS(n,e),$meteorDefence:cm(n,e),$calledShot:AS(n,e),$boosted:zS(n,e),$off:LS(e),$docked:dm(e),$orderPending:NS(e),$menuTarget:rm(n,e),$officerPick:!!window.officers&&officers.isPickTarget(n,e),children:[lm(n,e),cm(n,e)&&a.jsx(jS,{title:"Committed to meteor defence this turn",children:"☄"}),a.jsx(om,{children:US(n,e)}),(!i||hm(e))&&a.jsx(im,{$scs:r,$health:ld(n,e)||o?0:FS(n,e),$criticals:hm(e),$criticalsBenign:_S(e),$docked:BS(e)})]})}}const lm=(t,e)=>{if(!t||!e)return null;const n=!!window.systemEnhancements&&systemEnhancements.hasAny(t,e.id),r=window.officers?officers.postState(t,e.id):null;return!n&&!r?null:a.jsxs(CS,{children:[n&&a.jsx(sm,{title:"Carries a system enhancement",children:"✦"}),r&&a.jsx(sd,{state:r,title:officers.describePost(t,e.id)})]})},ES=(t,e)=>(weaponManager.hasFiringOrder(t,e)||DS(e))&&!(typeof e.isSpentLocked=="function"&&e.isSpentLocked()),DS=t=>gamedata.gamephase===1&&typeof t.getAbductionOrder=="function"&&!!t.getAbductionOrder(),PS=(t,e)=>weaponManager.isInterceptOnly(t,e),cm=(t,e)=>!!e.weapon&&weaponManager.hasMeteorDefence(t,e),AS=(t,e)=>!e.weapon||!weaponManager.hasFiringOrder(t,e)?!1:weaponManager.getCalledShotInfo(t,e)!==null,RS=t=>t.weapon&&(!weaponManager.isLoaded(t)||typeof t.isSpentLocked=="function"&&t.isSpentLocked()),IS=t=>t.weapon&&weaponManager.isLoadedAlternate(t),OS=(t,e)=>shipManager.power.isOffline(t,e),LS=t=>t.activeMeansOff&&t.active,zS=(t,e)=>shipManager.power.isBoosted(t,e)||e.active&&!e.activeMeansOff&&!e.suppressActiveBoost,dm=t=>!!(t.showDockedVisual&&t.activeEffective||t.stowed&&t.stowedArcStart==null||t.dockedWithOrbital),BS=t=>dm(t)||!!t.stowed,NS=t=>!!(t.showDockedVisual&&typeof t.hasPendingDockingOrder=="function"&&t.hasPendingDockingOrder()),FS=(t,e)=>(e.name==="ThirdspaceShield"||e.name==="ThoughtShield")&&e.baseRating?Math.min(100,e.currentHealth/e.baseRating*100):(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,ld=(t,e)=>shipManager.systems.isDestroyed(t,e),um=t=>t.name=="thruster"&&!t.iconPath?window.AssetManager.getSmartImagePath("./img/systemicons/thruster"+t.direction+".png"):t.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${t.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${t.name}.png`),hm=t=>shipManager.criticals.hasCriticalsIcon(t),_S=t=>shipManager.criticals.hasOnlyCritical(t,"HangarOperations",!0)||shipManager.criticals.hasOnlyCritical(t,"LCVLaunchedThisTurn",!0),HS=(t,e)=>shipManager.systems.hasBorderHighlight(t,e),WS=t=>weaponManager.isSelectedWeapon(t),US=(t,e)=>{if(e.outputDisplay!==void 0&&e.outputDisplay!==null&&e.outputDisplay!="")return e.outputDisplay;if(e.weapon){if(e.stowed&&e.stowedArcStart==null)return"-";if(typeof e.isSpentLocked=="function"&&e.isSpentLocked())return"✓";if(typeof e.getVortexIconLoad=="function"){const i=e.getVortexIconLoad();if(i!=null)return i}const r=weaponManager.hasFiringOrder(t,e);if(r&&e.canChangeShots)return weaponManager.getFiringOrder(t,e).shots+"/"+e.shots;if(r){var n=weaponManager.getCalledShotInfo(t,e);if(n)return"⊕"}else if(!r){let i=weaponManager.getWeaponCurrentLoading(e),o=e.loadingtime;e.normalload>0&&(o=e.normalload),i>o&&(i=o);let s="";return e.overloadturns>0&&shipManager.power.isOverloading(t,e)&&(s="("+e.overloadturns+")"),e.overloadshots>0?"S"+e.overloadshots:i+s+"/"+o}}else{if(e.outputType==="thrust")return shipManager.movement.getRemainingEngineThrust(t);if(e.outputType==="power"){let r=shipManager.power.getReactorPower(t,e);return gamedata.gamephase>1&&r<0?0:r}else return shipManager.systems.getOutput(t,e)}},GS=u.div`
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
`;class VS extends I.Component{constructor(e){super(e)}getWeapons(e,n){return e.flight?e.systems.map(r=>r.systems).reduce((r,i)=>r.concat(i),[]).filter(r=>r.weapon):e.systems.filter(r=>r.weapon||r.outputType==="thrust"||r.outputType==="EW"||r.outputType==="power"||r.outputType==="settings")}render(){const{ship:e,gamePhase:n}=this.props;if(!e)return null;const r=this.getWeapons(e,n);return a.jsx(GS,{children:r.map((i,o)=>a.jsx(ad,{fighter:e.flight,system:i,ship:e},`system-${o}`))})}}const pm=t=>{if(!t.hitChart)return[];const e=["Primary","Front","Aft","Port","Starboard"];let n=5;t.base&&!t.smallBase?(e[1]="Sections",n=2):t.SixSidedShip&&(e[31]="Port Front",e[32]="Port Aft",e[41]="Starboard Front",e[42]="Starboard Aft",n=43);const r=[];for(let i=0;i<n;i++){if(t.hitChart[i]===void 0)continue;const o=[];let s=0;for(const l in t.hitChart[i]){const c=Math.floor((l-s)/20*100);s=l;let d=t.hitChart[i][l];const h=d.indexOf(":");h>0&&(d=d.substring(h+1)),o.push({name:d,chance:c})}r.push({location:i,name:e[i],entries:o})}return r},YS=u.div`
    ${t=>t.$tightBottom?"& > *:last-child { display: none; }":""}
    ${t=>t.$compactText?`
    ${N} {
        font-size: 10px;
        line-height: 1.4;
        color: ${m.colors.textAccent};
    }
    ${ne} {
        font-size: 10px;
        font-style: normal;
        color: ${m.colors.text};
    }`:""}
`;class fm extends I.Component{render(){const{ship:e,hideHitChart:n,tightBottom:r,compactText:i,craft:o}=this.props,s=!!e.mine||window.gamedata&&typeof gamedata.isTerrain=="function"&&gamedata.isTerrain(e.shipSizeClass,e.userid),l=!!e.flight||s;var c=new Array,d=new Array,h=new Array;e.notes&&(c=e.notes.split("<br>")),e.hitChart&&!n&&pm(e).forEach(function(g){d[g.name]=g.entries.map(function(y){return y.name+" "+y.chance+"%"}).join(", ")}),e.enhancementTooltip!=""&&(h=e.enhancementTooltip.split("<br>"));let p={};if(!e.flight&&e.hasAttached&&Object.keys(e.hasAttached).length>0){const g={1:"Forward",2:"Aft",3:"Port",31:"Port-Forward",32:"Port-Aft",4:"Starboard",41:"Starboard-Forward",42:"Starboard-Aft"};for(let y in e.hasAttached){let S=e.hasAttached[y],T=g[S]||"Unknown";p[T]||(p[T]=0),p[T]++}}let f=window.officers?officers.displayStat(e,"offensivebonus"):e.offensivebonus;e.flight&&gamedata.areMinesPresent&&(e.minesweeper?f-=window.ew.getDetectMEW(e):f-=window.ew.getDetectMEW(e)*2);const v=window.officers?officers.currentTurn():0,x=[];if(e.flight&&window.officers){const g={};officers.listFor(e).forEach(y=>{const S=officers.get(y.id);if(o&&S&&S.multi&&String(y.post)!==String(o.id))return;const T=officers.stateText(y),C={name:officers.label(y.id),text:(S?S.summary:"")+(T?" "+T:""),out:!officers.serves(y,v),count:1},k=C.name+"|"+C.text+"|"+C.out;if(g[k]){g[k].count++;return}g[k]=C,x.push(C)}),x.forEach(y=>{y.count>1&&(y.name+=" ("+y.count+")")})}var b=0,M=!0;if(e.mine){var w=shipManager.systems.getSystemByName(e,"mineStealth");w&&!w.isMineRevealed(e)&&(M=!1,d=new Array,c=["No details known, scan with OEW to identify."],h=new Array)}return a.jsxs(YS,{$tightBottom:r,$compactText:i,children:[e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Offensive bonus: "}),f*5]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Armor (F/S/A): "}),shipManager.systems.getFlightArmour(e)]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Profile - Front/Side: "}),e.forwardDefense*5,"/",e.sideDefense*5]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Initiative: "}),window.officers?officers.displayIniativeBonus(e):e.iniativebonus]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Thrust: "}),window.officers?officers.displayStat(e,"freethrust"):e.freethrust]},b++),e.flight&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"Turn Cost: "}),e.turncost]},b++),e.flight&&M&&e.turndelay&&a.jsxs(N,{children:[a.jsx(ne,{children:"Turn Delay: "}),e.turndelaycost]},b++),e.flight&&M&&a.jsx(N,{children:" "},b++),Object.keys(c).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"NOTES:"})," "]},b++),Object.keys(c).length>0&&Object.keys(c).map(g=>a.jsx(N,{children:c[g]},b++)),Object.keys(c).length>0&&a.jsx(N,{children:" "},b++),Object.keys(d).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"HIT CHART:"})," "]},b++),Object.keys(d).length>0&&Object.keys(d).map(g=>a.jsxs(N,{children:[a.jsxs(ne,{children:[g,": "]}),d[g]]},b++)),Object.keys(d).length>0&&a.jsx(N,{children:" "},b++),Object.keys(p).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"UNITS ATTACHED:"})," "]},b++),Object.keys(p).length>0&&Object.keys(p).map(g=>a.jsxs(N,{children:[a.jsxs(ne,{children:[g,": "]}),p[g]]},b++)),Object.keys(p).length>0&&a.jsx(N,{children:" "},b++),l&&e.enhancementTooltip!=""&&M&&a.jsxs(N,{children:[a.jsx(ne,{children:"ENHANCEMENTS:"})," "]},b++),l&&e.enhancementTooltip!=""&&M&&Object.keys(h).map(g=>a.jsx(N,{children:h[g]},b++)),l&&e.enhancementTooltip!=""&&M&&a.jsx(N,{children:" "},b++),e.flight&&x.length>0&&a.jsxs(N,{children:[a.jsx(JS,{children:"OFFICERS:"})," "]},b++),e.flight&&x.map(g=>a.jsxs(ud,{$out:g.out,children:[a.jsxs(hd,{children:[g.name,": "]}),g.text]},b++)),e.flight&&x.length>0&&a.jsx(N,{children:" "},b++)]})}}const un=u(Gf)`
    /*font-size: 12px;*/
	font-size: 13px;
`,cd=u(Uf)`
    position: absolute;
    z-index: 20000;
    ${t=>Object.keys(t.position).reduce((e,n)=>e+`
`+n+":"+t.position[n]+"px;","")}
    width: ${t=>t.ship?"320px":"220px"};
    text-align: left;
    opacity:0.8;
`,dd=u.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,KS={MissileLost:"A missile was lost to damage"},gm={DamageReductionReduced:t=>`Damage reduction reduced by ${t}`},N=u(Vf)`
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
`,QS=u.span`
    color: #C6E2FF;
`,XS=u(un)`
    color: ${m.colors.enhTitle};
`,ud=u(N)`
    &&& { color: ${t=>t.$out?m.colors.textDim:m.colors.enhText}; }
`,hd=u.span`
    &&& { color: ${m.colors.enhTitle}; }
`,JS=u(ne)`
    &&& { color: ${m.colors.enhTitle}; }
`;class qS extends I.Component{render(){const{ship:e,selectedShip:n,system:r,boundingBox:i,officersOnly:o,craft:s}=this.props;if(o)return a.jsxs(cd,{position:pd(i),children:[a.jsx(un,{children:window.officers?officers.postName(r):r.displayName}),xm(e,r,!0)]});if(r instanceof Ship||r===e){var l=e.shipClass,c=e.name;if(r.flight&&(l=r.systems[1].displayName),e.mine){var d=shipManager.systems.getSystemByName(e,"mineStealth");d&&!d.isMineRevealed(e)&&(l="Mine",c="Mine")}return a.jsxs(cd,{ship:!0,position:pd(i),children:[a.jsxs(un,{children:[a.jsx(QS,{children:c})," - ",l]}),a.jsx(fm,{ship:e,craft:s})]})}var h="Special",p=new Array;r.data.Special&&r.data.Special!=""&&(p=String(ym(e,r,h)).split("<br>"));var f=0;let v=window.officers?officers.displayStat(e,"offensivebonus"):e.offensivebonus;e.flight&&gamedata.areMinesPresent&&(e.minesweeper?v-=window.ew.getDetectMEW(e):v-=window.ew.getDetectMEW(e)*2);var x=r.displayName,b=r.firingModes?r.firingModes[r.firingMode]:null,M=null;r.name==="ShadowFighterBomb"&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool=="function"&&(M=weaponManager.shadowFighterBombPool(e,r,!0));var w=null;if(r.outputType==="power"&&window.shipManager&&shipManager.power&&typeof shipManager.power.getDockedPowerSummary=="function"){var g=shipManager.power.getDockedPowerSummary(e);g.donors>0&&(w=g)}var y=null;if(typeof r.getAbductionOrder=="function"){var S=r.getAbductionOrder();if(S){var T=gamedata.getShip(S.targetid);y=T?T.name:"Unit "+S.targetid}}var C=window.officers?officers.laserBoostText(e,r):null,k=window.officers?officers.ionBoostText(e,r):null;let $=!1;if(e.mine){var d=shipManager.systems.getSystemByName(e,"mineStealth");d&&!d.isMineRevealed(e)&&($=!0,x="Mine",p=["No details known, scan with OEW to identify."])}return a.jsxs(cd,{position:pd(i),children:[a.jsx(un,{children:x}),!e.flight&&!$&&dt("Structure",r.maxhealth-damageManager.getDamage(e,r)+"/"+r.maxhealth),!e.flight&&!$&&dt("Armor",shipManager.systems.getArmour(e,r)),e.flight&&!$&&dt("Offensive bonus",tk(r,v*5)),r.firingModes&&!$&&dt("Firing mode",b),r.missileArray&&Object.keys(r.missileArray).length>0&&!$&&dt("Ammo Amount",r.missileArray[r.firingMode].amount),!$&&Object.keys(r.data).map((D,E)=>D!=h&&!(D==="Ammunition"&&(r.name==="GrapplingClaw"||r.name==="Marines"))&&dt(D,ym(e,r,D),"data"+E)),M!==null&&dt("Fighters available",M),w&&dt("Shared by docked ships","+"+w.shared+" of "+w.surplus+" pooled from "+w.donors+(w.donors===1?" ship":" ships")),y!==null&&dt("Abduction target",y),C!==null&&!$&&dt("Laser boost",C),k!==null&&!$&&dt("Thrust boost",k),Object.keys(p).length>0&&a.jsxs(N,{children:[a.jsx(ne,{children:"Special: "})," "]},`special-${f++}`),Object.keys(p).length>0&&Object.keys(p).map(D=>a.jsx(N,{children:p[D]},`special-${f++}`)),!$&&xm(e,r),(Object.keys(r.critData).length>0||r.criticals&&r.criticals.length>0)&&!$&&ek(r),!gamedata.isMyShip(e)&&!$&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&n&&mm(e,n,r),gamedata.isMyShip(e)&&!$&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==!1&&gamedata.selectedSystems.length>0&&n&&mm(e,n,r),gamedata.isMyShip(e)&&!$&&r.weapon&&weaponManager.hasFiringOrder(e,r)&&ZS(e,r)]})}}const mm=(t,e,n)=>weaponManager.canCalledshot(t,n,e)?[a.jsx(un,{children:"Called shot"},"calledHeader")].concat(gamedata.selectedSystems.map((r,i)=>{if(weaponManager.isOnWeaponArc(e,t,r))if(weaponManager.checkIsInRange(e,t,r)){var o=r.firingMode;return o=r.firingModes[o],n.id!=null&&!weaponManager.canWeaponCall(r)?a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName}),": Cannot Called Shot"]},`called-${i}`):a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName})," - Approx:  ",weaponManager.calculateHitChange(e,t,r,n.id).hitChance,"%"]},`called-${i}`)}else return a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName}),": Not in Range"]},`called-${i}`);else return a.jsxs(N,{children:[a.jsx(ne,{children:r.displayName}),": Not in Arc"]},`called-${i}`)})):[a.jsx(un,{children:"Called shot"},"calledHeader")].concat(a.jsx(N,{children:"Cannot Target"},"cannotTarget")),ZS=(t,e)=>{var n=weaponManager.getCalledShotInfo(t,e);return n?[a.jsx(dd,{},"calledShotDivider"),a.jsx(un,{children:"Called Shot"},"calledShotHeader"),a.jsxs(N,{children:[a.jsx(ne,{children:"Target: "}),n.targetSystem.displayName," (Id: ",n.targetSystem.id,") on ",n.targetShip.name]},"calledShotTarget")]:null},xm=(t,e,n=!1)=>{if(!window.officers||!t||!e)return null;const r=officers.onPost(t,e.id),i=officers.arrivingAt(t,e.id);if(r.length===0&&i.length===0)return null;const o=officers.currentTurn();return(n?[]:[a.jsx(dd,{},"officerDivider")]).concat([a.jsx(XS,{children:"Officers"},"officerHeader")]).concat(r.map((s,l)=>{const c=officers.moveText(t,s)||officers.stateText(s);return a.jsxs(ud,{$out:!officers.serves(s,o)||officers.movingTo(t,s)!==null,children:[a.jsxs(hd,{children:[officers.label(s.id),": "]}),officers.summaryOf(t,s),c?" "+c:""]},`officer-${s.id}-${l}`)}),i.map((s,l)=>a.jsxs(ud,{$out:!0,children:[a.jsxs(hd,{children:[officers.label(s.entry.id)," "]}),officers.arrivalText(t,s.entry)]},`arriving-${s.entry.id}-${l}`)))},ek=t=>{const e=Object.keys(t.critData).length>0?Object.keys(t.critData):[...new Set((t.criticals||[]).map(n=>n.phpclass))];return e.length===0?null:[a.jsx(dd,{},"critDivider"),a.jsx(un,{children:"Criticals"},"criticalHeader")].concat(e.map(n=>{let r=0,i=0;var o=0,s=0,l=!1,c="";o=0,s=0,l=!1,c="";for(const h in t.criticals)t.criticals[h].phpclass==n&&t.criticals[h].turn<=gamedata.turn&&(t.criticals[h].turnend==0||t.criticals[h].turnend>=gamedata.turn)&&(r++,i+=parseInt(t.criticals[h].param,10)||0,r==1&&(o=t.criticals[h].turnend,s=t.criticals[h].turnend,l=t.criticals[h].turnend==0),t.criticals[h].turnend>0?(t.criticals[h].turnend>s&&(s=t.criticals[h].turnend),(t.criticals[h].turnend<o||o==0)&&(o=t.criticals[h].turnend)):l=!0);if(o>0&&(c=" (until end of turn "+o,l?c=c+"+":s>o&&(c=c+"-"+s),c=c+")"),r>=1&&gm[n]){const h=gm[n](i);return a.jsxs(N,{children:[h," ",c]},`critical-${n}`)}const d=t.critData[n]||KS[n]||n;return r>1?a.jsxs(N,{children:["(",r," x) ",d," ",c]},`critical-${n}`):r==1?a.jsxs(N,{children:[d," ",c]},`critical-${n}`):null}))},tk=(t,e)=>typeof t.adjustOffensiveBonusDisplay=="function"?t.adjustOffensiveBonusDisplay(e):e,ym=(t,e,n)=>{const r=typeof e.adjustDataValueDisplay=="function"?e.adjustDataValueDisplay(n,e.data[n]):e.data[n];return window.officers?officers.adjustDataDisplay(t,e,n,r):r},dt=(t,e,n)=>{if(typeof e=="string"&&e.indexOf("<br>")!==-1){const r=e.split("<br>");return a.jsxs(N,{children:[a.jsxs(ne,{children:[t,": "]}),r.map((i,o)=>a.jsxs(I.Fragment,{children:[o>0&&a.jsx("br",{}),i]},o))]},n)}return a.jsxs(N,{children:[a.jsxs(ne,{children:[t,": "]}),e]},n)},pd=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left+t.width,e},nk=300,Hs=8;function rk(t,e,n,r){if(!t||!e)return null;const i=n-e.right-Hs,o=e.left-Hs;if(Math.max(i,o)<nk)return null;const s={};return i>=o?s.left=e.right:s.right=n-e.left,t.top>r/2?s.bottom=Math.max(Hs,r-t.bottom):s.top=Math.max(Hs,t.top),s}u(Gf)`
    font-size: 12px;
`;const ik=u(Uf)`
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
`;u(Vf)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,u.span`
    color: white;
`;class ok extends I.Component{componentDidMount(){this.markTarget()}componentDidUpdate(){this.markTarget()}componentWillUnmount(){nm(null,null)}markTarget(){const{ship:e,system:n,avoidBox:r}=this.props,i=!!r&&Is(e,n);nm(i?e:null,i?n:null)}render(){const{ship:e,system:n,boundingBox:r,avoidBox:i}=this.props;return Is(e,n)?a.jsx(ik,{position:rk(r,i,sk(),ak())||lk(r),opacity:$S(e,n)?.95:.8,$bare:gamedata.gamephase===-2,children:a.jsx(yS,{...this.props})}):null}}const sk=()=>document.documentElement.clientWidth||window.innerWidth,ak=()=>document.documentElement.clientHeight||window.innerHeight,lk=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e},ck={0:"Primary",1:"Forward",2:"Aft",3:"Port",4:"Starboard",5:"",31:"Port Fwd",32:"Port Aft",41:"Stbd Fwd",42:"Stbd Aft"},dk=u.div`
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
`,uk=u.div`
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
        background-color: ${t=>t.$criticals?m.colors.healthCrit:m.colors.healthOk};
    }
`,hk=u.span`
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
    /*A bar with badges after its name (Stage 19: the ✦, an officer's insignia) can run out of room - a Starboard block of 100+
      boxes in a 128px side column. The NAME gives way, with an ellipsis, so the badges and the readout stay whole. The padding
      and the negative margin cancel out: they only give the text shadow room inside the clipped box.*/
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 3px 2px;
    margin: -3px -2px;
`,pk=u.span`
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 3px;
    min-width: 0;
`,fk=u.span`
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: 1px;
`,gk=u.span`
    position: relative;
    z-index: 1;
    flex-shrink: 0; /*the readout never gives way - the name does (SectionName)*/
    font-family: ${m.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${t=>t.$destroyed?"transparent":m.colors.text};
    filter: ${t=>t.$destroyed?"blur(1px)":"none"};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,mk=u.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`;class wm extends I.Component{constructor(e){super(e),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(e){const{ship:n,systems:r}=this.props,i=fd(r);if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(i&&window.officers&&officers.picking&&officers.pickTarget(n,i)){e.stopPropagation(),this.hideStructureArc();return}if(i&&window.officers&&!cn(n,i)&&officers.canManage(n,i)){e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:n,system:i,element:e.currentTarget,showMenu:!0});return}if(Zc(n)){e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("MineDamageClicked",{ship:n,element:e.currentTarget});return}!i||!cn(n,i)||(e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay("SystemClicked",{ship:n,system:i,element:e.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.hideStructureArc()}showStructureArc(){const{ship:e,systems:n}=this.props;this.arcShown=!0,window.uiEvents.relay("StructureMouseOver",{ship:e,structure:fd(n),element:this.headerElement})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay("StructureMouseOut"))}onStructureMouseOver(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.showStructureArc())}onStructureMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(e){e.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.touches[0];this.touchStartX=n.clientX,this.touchStartY=n.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;const n=e.touches[0];(Math.abs(n.clientX-this.touchStartX)>10||Math.abs(n.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(e){e.stopPropagation(),this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,this.hideStructureArc()}render(){const{ship:e,systems:n,location:r,displayLocation:i,area:o,valign:s,justify:l,wide:c,isTerrain:d,minHeight:h,nameOverride:p,hidden:f}=this.props,v=fd(n),x=Zc(e),b=x?battleDamage.mineHealth(e,1):0,M=x?battleDamage.mineMaxHealth(e):0,w=x?b/M*100:v?xk(e,v):0,g=i!==void 0?i:r,y=i!==void 0&&i!==r,S=v&&window.officers?officers.postState(e,v.id):null,T=!!(v&&window.officers)&&officers.isPickTarget(e,v),C=!!(v&&window.officers)&&officers.canManage(e,v),k=!!(v&&window.systemEnhancements)&&systemEnhancements.hasAny(e,v.id),$=p||ck[r]||"",D=(k||S)&&$==="Starboard"?"Stbd":$;return a.jsxs(dk,{$location:r,$area:o,$valign:s,$justify:l,$wide:c,$isTerrain:d,$minHeight:h,$hidden:f,children:[v&&a.jsxs(uk,{ref:E=>{this.headerElement=E},$health:w,$criticals:yk(v),$damageable:cn(e,v)||Zc(e)||C,$officerPick:T,$menuTarget:rm(e,v),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[a.jsxs(pk,{children:[a.jsx(hk,{children:D}),(k||S)&&a.jsxs(fk,{children:[k&&a.jsx(sm,{title:"Carries a system enhancement",children:"✦"}),S&&a.jsx(sd,{state:S,title:officers.describePost(e,v.id)})]})]}),a.jsxs(gk,{$destroyed:w===0,children:[x?b:v.maxhealth-damageManager.getDamage(e,v),"/",x?M:v.maxhealth," A",shipManager.systems.getArmour(e,v)]})]}),a.jsx(mk,{children:wk(n,g,c).map(E=>a.jsx(ad,{scs:!0,mirror:y,system:E,ship:e},`system-scs-${r}-${e.id}-${E.id}`))})]})}}const xk=(t,e)=>(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,yk=t=>shipManager.criticals.hasCriticals(t),vm=t=>t.name==="structure",fd=t=>t.find(vm),Ws=t=>t.filter(e=>!vm(e)),wk=(t,e,n)=>(t=Ws(t),n?gd(t):[4,41,42].includes(e)?md(t):[3,31,32].includes(e)?vk(md(t)):[1,2,0].includes(e)?gd(t):bk(t)),vk=t=>{let e=[];return t.forEach((n,r)=>{const i=r%3;i===0?e[r+2]=n:i===1?e[r]=n:e[r-2]=n}),e},bk=t=>(t=Ws(t),t.length===3?md(t):t.length===4?gd(t):t),gd=t=>{t=Ws(t);let e=[];for(;;){const{picked:n,remaining:r}=yd(t,4);if(n.length===0)break;t=r,e=e.concat(n)}for(;;){const{picked:n,remaining:r}=yd(t,2);if(n.length===0)break;t=r;const i=yd(t,2);i.picked.length>0?(t=i.remaining,e=e.concat([n[0],i.picked[0],i.picked[1],n[1]])):(e=e.concat([n[0],t.shift(),t.shift(),n[1]]),e=e.filter(o=>o))}return e=e.concat(t),e},md=t=>{t=Ws(t);let e=[];for(;;){const{picked:n,remaining:r}=xd(t,3);if(n.length===0)break;t=r,e=e.concat(n)}for(;;){const{picked:n,remaining:r}=xd(t,2);if(n.length===0)break;const{three:i,remainingSystems:o}=Sk(n,r);t=o,e=e.concat(i)}return e=e.concat(t),e},Sk=(t,e)=>{const n=xd(e,1);return n.picked.length===1?{three:[n.picked[0],t[0],t[1]],remainingSystems:n.remaining}:e.length>0?{three:[e.shift(),t[0],t[1]],remainingSystems:e}:{three:[t[0],t[1]],remainingSystems:e}},xd=(t,e=3)=>{const n=t.find(o=>{const s=t.reduce((l,c)=>c.name===o.name?l+1:l,0);return e===1?s===e:s>=e});if(!n)return{picked:[],remaining:t};let r=[];const i=t.filter(o=>o.name===n.name&&e>0?(e--,r.push(o),!1):!0);return{picked:r,remaining:i}},yd=(t,e=3)=>{const n=t.find(d=>{const h=t.reduce((p,f)=>f.name===d.name?p+1:p,0);return e===1?h===e:h>=e});if(!n)return{picked:[],remaining:t};let r=[],i=[];const o=t.filter(d=>d.name===n.name?(i.push(d),!1):!0);for(var s=Math.ceil(e/2),l=Math.floor(e/2),c=0;c<i.length;c++)c<s||c>=i.length-l?r.push(i[c]):o.unshift(i[c]);return{picked:r,remaining:o}},bm={DEW:"#aecdea",CCEW:m.colors.text,SDEW:"#9ac1e5",OEW:"#acd7a8",BDEW:"#8ac785","Detect Mines":"#bfa3db","Detect Stealth":"#ccb6e2",DIST:"#e6b98f",SOEW:m.colors.text,OEW_HOSTILE:"#e49b9b","Saved EW":"#e0d39a",ELINT:"#9ad4cf"},Rt=(t,e)=>t==="OEW"&&e&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(e)?bm.OEW_HOSTILE:bm[t]||m.colors.textAccent,Sm=u.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${t=>t.$flight?"":Pt`
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
`,km=u.div`
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
`,It=u.div`
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
    ${t=>t.$target&&Pt`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${t=>t.$hoverable&&Pt`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,kk=u.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,Ot=u.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${t=>t.$color||m.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,Lt=u.span`
    font-family: ${m.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,$k=u.span`
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
    ${t=>t.$interactive&&Pt`
        cursor: pointer;
        &:hover {
            color: ${m.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`;class $m extends I.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(e,n){window.webglScene&&(window.uiEvents.relay("EwRangeHover",{shipId:this.props.ship.id,type:e,active:n}),this.activeRangeOverlay=n?e:null)}onTargetClick(e,n){n.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(e)||window.uiEvents.relay("ScrollToShip",{shipId:e.id}))}setTargetHighlight(e,n,r){window.webglScene&&(window.uiEvents.relay("EwTargetHighlight",{shipId:this.props.ship.id,targetId:e.id,type:n,active:r}),this.activeHighlight=r?{targetId:e.id,type:n}:null)}render(){const{ship:e,flight:n}=this.props;return n?a.jsxs(Sm,{$flight:!0,children:[a.jsx(km,{children:"Electronic Warfare"}),Ck(e),Tm(e,this)]}):a.jsxs(Sm,{children:[a.jsx(km,{children:"Electronic Warfare"}),Tk(e,this),Tm(e,this)]})}}const Ck=t=>{const e=[a.jsxs(It,{children:[a.jsx(Ot,{$color:Rt("DEW"),children:"DEW"}),a.jsx(Lt,{children:ue(ew.getFlightDEW(t))})]},`dew-scs-${t.id}`)],n=Cm(t);return n&&e.push(n),e},Cm=t=>{if(!gamedata.isPlayerInGame()||!gamedata.isMyorMyTeamShip(t))return null;const e=ew.getSavedEwAllowance(t);if(e<=0)return null;const r=ew.isLateEwWindowOpen(t)||ew.isLateEwPhase()&&gamedata.isMyShip(t)?`${ue(ew.getLateEwRemaining(t))} / ${ue(e)}`:ue(e);return a.jsxs(It,{children:[a.jsx(Ot,{$color:Rt("Saved EW"),children:"Saved EW"}),a.jsx(Lt,{children:r})]},`savedew-scs-${t.id}`)},Tk=(t,e)=>{let n=[];const r=!!window.webglScene,i=h=>r?{$hoverable:!0,onMouseEnter:()=>e.setRangeOverlay(h,!0),onMouseLeave:()=>e.setRangeOverlay(h,!1)}:{};if(n.push(a.jsxs(It,{children:[a.jsx(Ot,{$color:Rt("DEW"),children:"DEW"}),a.jsx(Lt,{children:ue(ew.getDefensiveEW(t))})]},`dew-scs-${t.id}`)),r&&ew.hasElintModules(t)&&gamedata.isMyorMyTeamShip(t)){const h=ew.getElintModuleSplit(t);let p=0;ew.getElintModules(t).forEach(v=>{p+=v.elintLost||0});const f=gamedata.gamephase==1?`${ue(h.freeM)} / ${ue(h.M)}`:p>0?`${ue(h.M)} (lost ${p})`:ue(h.M);n.push(a.jsxs(It,{children:[a.jsx(Ot,{$color:Rt("ELINT"),children:"ELINT Modules"}),a.jsx(Lt,{children:f})]},`elint-scs-${t.id}`))}var o=Math.max(0,ew.getCCEW(t)-ew.getDistruptionEW(t));o>0&&n.push(a.jsxs(It,{children:[a.jsx(Ot,{$color:Rt("CCEW"),children:"CCEW"}),a.jsx(Lt,{children:ue(o)})]},`ccew-scs-${t.id}`));let s=ew.getBDEW(t)*.25,l=ew.getDetectSEW(t),c=ew.getDetectMEW(t);shipManager.hasSpecialAbility(t,"ConstrainedEW")&&(s=ew.getBDEW(t)*.2),s&&n.push(a.jsxs(It,{...i("BDEW"),children:[a.jsx(Ot,{$color:Rt("BDEW"),children:"BDEW"}),a.jsx(Lt,{children:ue(s)})]},`bdew-scs-${t.id}`)),c&&n.push(a.jsxs(It,{...i("MDEW"),children:[a.jsx(Ot,{$color:Rt("Detect Mines"),children:"Detect Mines"}),a.jsx(Lt,{children:ue(c)})]},`DetectMEW-scs-${t.id}`)),l&&n.push(a.jsxs(It,{children:[a.jsx(Ot,{$color:Rt("Detect Stealth"),children:"Detect Stealth"}),a.jsx(Lt,{children:ue(l)})]},`DetectSEW-scs-${t.id}`));const d=Cm(t);return d&&n.push(d),n},Tm=(t,e)=>{const n=!!window.webglScene;return t.EW.filter(r=>r.turn===gamedata.turn).filter(r=>r.type==="OEW"||r.type==="DIST"||r.type==="SOEW"||r.type==="SDEW").map(r=>{const i=gamedata.getShip(r.targetid);return a.jsxs(It,{$target:!0,children:[a.jsxs(kk,{children:[a.jsx(Ot,{$color:Rt(r.type,t),children:r.type}),a.jsx($k,{$interactive:n,title:void 0,onClick:n?e.onTargetClick.bind(e,i):void 0,onMouseEnter:n?()=>e.setTargetHighlight(i,r.type,!0):void 0,onMouseLeave:n?()=>e.setTargetHighlight(i,r.type,!1):void 0,children:i.name})]}),a.jsx(Lt,{children:Mk(r,t)})]},`${r.type}-scs-${t.id}-${r.targetid}`)})},Mk=(t,e)=>{switch(t.type){case"SDEW":if(shipManager.hasSpecialAbility(e,"ConstrainedEW")){let n=t.amount*.333;return n=Math.round(n*3)/3,ue(n)}else return ue(t.amount*.5);case"DIST":return shipManager.hasSpecialAbility(e,"ConstrainedEW")?ue(t.amount/4):ue(t.amount/3);case"OEW":return ue(Math.max(0,t.amount-ew.getDistruptionEW(e)));default:return ue(t.amount)}},ue=t=>Math.round(t*100)/100,Us=()=>!!window.gamedata&&window.gamedata.gamephase===-2,jk=t=>{let e=!1,n=0;for(const r in t.damage){const i=t.damage[r];if(i.damageclass==="HyperspaceJump"){e=!0;continue}n+=Math.max(0,i.damage-i.armour)}return e&&n<t.maxhealth},Ek=u.div`
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
`,Dk=u.div`
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
`,jr=u.div`
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
`,Pk=u.div`
    position: absolute;
    top: -5px;
    left: -5px;
    z-index: 3;
    pointer-events: none;
`,Mm=u.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,Ak=u(Mm)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,Rk=u.div`
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
`,Ik=u.div`
    z-index: 1;
`;class Ok extends I.Component{onSystemMouseOver(e){if(Us()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||e.nativeEvent&&e.nativeEvent.sourceCapabilities&&e.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:n,fighter:r}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:n,system:n,craft:r,element:e.target})}onSystemMouseOut(){Us()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onFighterTouchStart(e){if(Us())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:i,fighter:o}=this.props;window.uiEvents.relay("SystemMouseOver",{ship:i,system:i,craft:o,element:n,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(e){if(!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(e){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onFighterTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){const{ship:e}=this.props,n=window.gamedata&&typeof gamedata.fleetIsCommitted=="function"&&gamedata.fleetIsCommitted();return Us()&&!n&&!!e&&e.userid!=0&&!!e.flight}onHealthBarClick(e){this.canApplyPreBattleDamage()&&(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay("FighterDamageClicked",{ship:this.props.ship,fighter:this.props.fighter,element:e.currentTarget}))}render(){const{ship:e,fighter:n}=this.props,r=shipManager.systems.isDestroyed(e,n),i=shipManager.criticals.isDockedFighter(n),o=!i&&shipManager.criticals.isSplitLaunchedFighter(n),s=!i&&!o&&shipManager.criticals.isDisengagedFighter(n),l=shipManager.criticals.isCutOffFighter(n);let c=null;r?i?c=a.jsx(jr,{$color:"#00b8e6",children:"DOCKED"}):o?c=a.jsx(jr,{$color:"#00b8e6",children:"SPLIT"}):s?c=a.jsx(jr,{$color:"#ff8c00",children:"DROPOUT"}):jk(n)?c=a.jsx(jr,{$color:"#cc8500",children:"JUMPED"}):c=a.jsx(jr,{$color:"#ff5252",children:"DESTROYED"}):l&&(c=a.jsx(jr,{$color:"#ff5252",children:"CUT OFF"}));const d=this.canApplyPreBattleDamage(),h=d?battleDamage.fighterHealth(e,1)/n.maxhealth*100:Lk(e,n),p=d?`${battleDamage.fighterHealth(e,1)} / ${n.maxhealth}`:`${n.maxhealth-damageManager.getDamage(e,n)} / ${n.maxhealth}`,f=window.officers?officers.postState(e,n.id):null;return a.jsxs(Ek,{$docked:i,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[a.jsxs(Dk,{$destroyed:r,$img:window.AssetManager.getSmartImagePath(n.iconPath),children:[a.jsx(Mm,{children:jm(e,n,Nk(n),r)}),a.jsx(Ak,{children:jm(e,n,Fk(n),r)}),a.jsx(Rk,{$health:h,$criticals:zk(n),$criticalsBenign:Bk(n),$docked:i,$clickable:d,title:d?"Apply pre-battle damage to this flight":void 0,onClick:this.onHealthBarClick.bind(this),children:a.jsx(Ik,{children:p})})]}),c,f&&a.jsx(Pk,{children:a.jsx(sd,{state:f,title:officers.describePost(e,n.id)})})]})}}const Lk=(t,e)=>(e.maxhealth-damageManager.getDamage(t,e))/e.maxhealth*100,zk=t=>shipManager.criticals.hasCriticals(t),Bk=t=>shipManager.criticals.hasOnlyCritical(t,"LaunchedThisTurn",!1),Nk=t=>t.systems.filter(e=>e.location==1),Fk=t=>t.systems.filter(e=>e.location!=1),jm=(t,e,n,r)=>n.map((i,o)=>a.jsx(ad,{$destroyed:r,fighter:!0,scs:!0,system:i,ship:t},`system-scs-fighter${e.id}-${t.id}-${i.id}-${o}`)),_k=u.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`;class wd extends I.Component{render(){const{ship:e}=this.props;return a.jsx(_k,{children:Hk(e)})}}const Hk=t=>t.systems.map((e,n)=>a.jsx(Ok,{fighter:e,ship:t},`flight-${t.id}-${n}`)),Wk=[3,31,32],Uk=[1,0,2],Gk=[4,41,42],Vk=u.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,vd=u.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${t=>t.$side?"center":"flex-start"};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,Yk=u.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${m.colors.line};
    padding: 3px 5px;
`,Kk=u.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${m.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${m.colors.line};
`,Qk=u.div`
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
`,Xk=u.span`
    font-family: ${m.fonts.mono};
    color: ${m.colors.text};
    flex-shrink: 0;
`,Jk=(t,e)=>a.jsxs(Yk,{children:[a.jsx(Kk,{children:e.name}),[...e.entries].sort((n,r)=>r.chance-n.chance).map((n,r)=>a.jsxs(Qk,{children:[a.jsx("span",{children:n.name}),a.jsxs(Xk,{children:[n.chance,"%"]})]},`hitchart-${t.id}-${e.location}-${r}`))]},`hitchart-${t.id}-${e.location}`);class qk extends I.Component{render(){const{ship:e}=this.props,n=pm(e);if(n.length===0)return null;const r={};n.forEach(c=>{r[c.location]=c});const i=c=>c.filter(d=>r[d]).map(d=>Jk(e,r[d])),o=i(Wk),s=i(Uk),l=i(Gk);return a.jsxs(Vk,{children:[o.length>0&&a.jsx(vd,{$side:!0,children:o}),s.length>0&&a.jsx(vd,{children:s}),l.length>0&&a.jsx(vd,{$side:!0,children:l})]})}}const Zk=t=>t.split(" ").map(e=>e.charAt(0).toUpperCase()+e.slice(1)).join(" "),e$=t=>{const e=[];if(!t||t.flight)return e;const n=t.fighters||{};if(Object.keys(n).length>0){const i={};if(shipManager.systems.shipHasRestrictedHangar(t)){const o=shipManager.systems.getReservedFighterComposition(t);for(let s=0;s<o.length;s++){const l=o[s].category;i[l]||(i[l]=[]);const c=i[l];let d=!1;for(let h=0;h<c.length;h++)if(c[h].phpclass===o[s].phpclass){c[h].count+=o[s].count,d=!0;break}d||c.push({category:o[s].category,phpclass:o[s].phpclass,displayName:o[s].displayName,count:o[s].count,isGroup:o[s].isGroup})}}for(const o in n){const s=n[o],l=Zk(o),c=i[o];if(c&&(o==="heavy"||o==="medium"||o==="light")){let d=s;for(let h=0;h<c.length;h++){const p=Math.min(c[h].count,d);p<=0||(c[h].isGroup?e.push(p+" "+c[h].displayName+"s"):e.push(p+" "+c[h].displayName+" "+l+" Fighters"),d-=p)}d>0&&e.push(d+" "+l+" Fighters");continue}if(o==="normal")e.push(s+" Fighters");else if(o==="superheavy"||o==="heavy"||o==="medium"||o==="light"||o==="ultralight")e.push(s+" "+l+" Fighters");else{if(o==="shuttles"||o==="minesweeping shuttles"||o==="cargo shuttles"||o==="lifeboats"||o==="medical shuttles"||o==="presidential shuttle"||o==="yacht")continue;e.push(s+" "+l)}}}const r=shipManager.systems.getDefaultShuttleComposition(t);for(let i=0;i<r.length;i++)e.push(r[i].count+" "+r[i].type);return e},t$=u.div`
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
`,zi=u.div`
    background-color: ${m.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${t=>t.$gold?m.colors.enhLine:m.colors.line};
    padding: 0 8px 3px;
    /*$gold: the enhancement lines in the gold body text on every window (user request 2026-10-11).
      The grid's EnhancementsPanel got it from its EnhArea wrapper, but a flight's block sits in
      the blue Rail and read blue; set here, both read the same.*/
    ${t=>t.$gold?`color: ${m.colors.enhText};`:""}
`,bd=u.div`
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
`,In=u.div`
    padding: 1px 0;
`,n$=u.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${m.colors.custom};
`,Re=u.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,Ie=u.span`
    font-size: 10px;
    color: ${m.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,Oe=u.span`
    font-family: ${m.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${t=>t.$changed?m.colors.custom:m.colors.text};
    margin-right: 5px;
`,r$=u.div`
    width: 150px;
    box-sizing: border-box;
    ${t=>t.$bare?`
    padding: 0;`:`
    background-color: ${m.colors.panelBgGlass};
    border: 1px dotted ${m.colors.line};
    padding: 2px 4px 3px;`}
`,i$=u.div`
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
`,Em=u.span`
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
`;u.div`
    text-align: center;
    font-size: 10px;
    color: ${m.colors.warning};
    padding-top: 2px;
`;const Dm=u.div`
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
`,o$=u.div`
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
`,Sd=t=>typeof t=="number"?t.toFixed(2):t,On=(t,e)=>window.officers?officers.displayStat(t,e):t[e],kd=(t,e)=>t*5+"/"+e*5,Gs=(t,e)=>t+" ("+Sd(e)+")",s$=t=>{const e=window.shipManager;if(!e)return null;const n={},r=e.systems?e.systems.getSystemByName(t,"CnC"):null,i=r&&e.criticals?e.criticals.hasCritical(r,"ProfileIncreased"):0;n.profile=kd(t.forwardDefense+i,t.sideDefense+i),n.profileChanged=i!==0;const o=t.iniativeadded||0;n.initiative=(t.iniativebonus||0)+o,n.initiativeChanged=o!==0;const s=e.movement;if(s&&typeof s.getTurnCost=="function"&&t.movement&&t.movement.length>0){const l=s.getSpeed(t),c=s.getDockedLcvTurnSurcharge(t),d=s.getTurnDelayCost(t);let h=s.getTurnCost(t);t.submarine&&s.isGoingBackwards(t)&&(h=h*1.33);const p=Math.max(1,Math.ceil(l*h))+c,f=Math.ceil(l*d),v=s.applyCrewTurnDelay(t,typeof s.getQuartermasterTurnDelay=="function"?s.getQuartermasterTurnDelay(t,f):f)+c;n.turnCost=Gs(p,h),n.turnDelay=Gs(v,d),n.turnCostChanged=n.turnCost!==Gs(Math.max(1,Math.ceil(l*t.turncost)),t.turncost),n.turnDelayChanged=n.turnDelay!==Gs(Math.ceil(l*t.turndelaycost),t.turndelaycost)}return n},Pm=({ship:t,live:e,bare:n})=>{const r=!t.base,i=e?s$(t):null;return a.jsxs(r$,{$bare:n,children:[!n&&a.jsxs(i$,{children:[a.jsxs(Em,{children:[a.jsx("i",{}),a.jsx("i",{}),a.jsx("i",{})]}),"Ship Stats"]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn cost"}),a.jsx(Oe,{$changed:!!(i&&i.turnCostChanged),children:i&&i.turnCost?i.turnCost:Sd(On(t,"turncost"))})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn delay"}),a.jsx(Oe,{$changed:!!(i&&i.turnDelayChanged),children:i&&i.turnDelay?i.turnDelay:Sd(t.turndelaycost)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Accel/decel"}),a.jsx(Oe,{children:On(t,"accelcost")})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Pivot"}),a.jsx(Oe,{children:On(t,"pivotcost")})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Roll"}),a.jsx(Oe,{children:On(t,"rollcost")})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Profile - Front / Side"}),a.jsx(Oe,{$changed:!!(i&&i.profileChanged),children:i?i.profile:window.officers?kd(officers.displayStat(t,"forwardDefense"),officers.displayStat(t,"sideDefense")):kd(t.forwardDefense,t.sideDefense)})]}),r&&a.jsxs(Re,{children:[a.jsx(Ie,{children:"Initiative"}),a.jsx(Oe,{$changed:!!(i&&i.initiativeChanged),children:i?i.initiative:window.officers?officers.displayIniativeBonus(t):t.iniativebonus})]})]})},a$=({ship:t})=>{const e=Cd(t.enhancementTooltip),n=$d(t);return e.length===0&&!n?null:a.jsx(o$,{children:a.jsxs(zi,{$gold:!0,children:[a.jsx(Dm,{children:"Enhancements"}),e.map((r,i)=>a.jsx(In,{children:r},`enh-${i}`)),n&&a.jsx(In,{children:n},"officers")]})})},$d=t=>window.officers&&t?officers.summaryLine(t):null,Cd=t=>(t||"").split(/<br\s*\/?>/i).map(e=>e.replace(/<[^>]*>/g,"").replace(/&nbsp;/g," ").trim()).filter(Boolean);class Td extends I.Component{render(){const{ship:e,full:n,grid:r,hideEnhancements:i}=this.props,o=e$(e),s=Cd(e.notes),l=i?[]:Cd(e.enhancementTooltip),c=i?null:$d(e),d=[];if(e.limited&&e.limited!=0&&d.push("Limited: "+e.limited+"%"),e.variantOf){const f=e.occurence?e.occurence.charAt(0).toUpperCase()+e.occurence.slice(1)+" ":"";d.push(f+"variant of "+e.variantOf)}e.isd&&d.push("In-Service (ISD): "+e.isd);let h=null;e.unofficial==="S"?h="Semi-Custom":e.unofficial&&(h="Custom");const p=s.length>0||d.length>0||h;return a.jsxs(t$,{$full:n,$grid:r,children:[e.flight&&a.jsxs(zi,{children:[a.jsx(bd,{children:"Flight Stats"}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Armor F/S/A"}),a.jsx(Oe,{children:shipManager.systems.getFlightArmour(e)})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Offensive bonus"}),a.jsx(Oe,{children:On(e,"offensivebonus")*5})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Profile - Front / Side"}),a.jsxs(Oe,{children:[e.forwardDefense*5,"/",e.sideDefense*5]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Thrust"}),a.jsx(Oe,{children:On(e,"freethrust")})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Turn Cost / Delay"}),a.jsxs(Oe,{children:[e.turncost," / ",e.turndelaycost==0?0:e.turndelaycost]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Accel. / Pivot / Roll"}),a.jsxs(Oe,{children:[e.accelcost," / ",e.pivotcost," / ",e.rollcost]})]}),a.jsxs(Re,{children:[a.jsx(Ie,{children:"Initiative"}),a.jsx(Oe,{children:On(e,"iniativebonus")})]})]}),o.length>0&&a.jsxs(zi,{children:[a.jsx(bd,{children:"Hangar Capacity"}),o.map((f,v)=>a.jsx(In,{children:f},`comp-${v}`))]}),p&&a.jsxs(zi,{children:[a.jsx(bd,{children:"Notes"}),s.map((f,v)=>a.jsx(In,{children:f},`note-${v}`)),d.map((f,v)=>a.jsx(In,{children:f},`meta-${v}`)),h&&a.jsx(n$,{children:h})]}),(l.length>0||c)&&a.jsxs(zi,{$gold:!0,children:[a.jsx(Dm,{children:"Enhancements"}),l.map((f,v)=>a.jsx(In,{children:f},`enh-${v}`)),c&&a.jsx(In,{children:c},"officers")]})]})}}const l$=400,c$=u.div`
    display: flex;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 4px 0 0;
`,d$=u.div`
    flex: 0 1 auto;
    min-width: 0;
    width: max-content;
    max-width: ${l$}px;
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
`,u$=u.div`
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
`,h$=u.span`
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
`,p$=u.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${m.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,f$=u.div`
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
`,g$=u.div`
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
`,m$=u.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${t=>t.$compact?"width: 100%; align-items: center; margin-bottom: 5px;":"align-items: stretch;"}
    gap: 4px;
`,Vs=u.div`
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
`,Am=u.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,x$=u.span`
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
`,Md=u.div`
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
`,y$=u.div`
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
`,w$=u.div`
    grid-area: ${t=>t.$area};
    display: flex;
    flex-direction: column;
    gap: 8px; /*SectionGrid's own gap, so a cell keeps the grid's spacing*/
    align-items: ${t=>t.$side==="left"?"flex-end":t.$side==="right"?"flex-start":"center"};
    justify-content: ${t=>t.$upper?"flex-end":"flex-start"};
    ${t=>t.$side?`padding-${t.$upper?"bottom":"top"}: 4px;`:t.$upper?"":"padding-top: calc(var(--fv-prim-half, 0px) + 8px);"}
`,v$=u.div`
    margin-bottom: calc(-1 * var(--fv-prim-half, 0px));
`,Rm=u.div`
    display: flex;
    justify-content: center;
    align-self: stretch;
    ${t=>t.$top?"margin-bottom: auto;":""}
`,b$=u.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,S$=u.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,jd=u.div`
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
`,Ed=u.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,k$=u.div`
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
`,Im=u.div`
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
`,$$=u.div`
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
`,C$={1:"fwd",2:"aft",0:"prim",3:"left",4:"right",31:"lfwd",41:"rfwd",32:"laft",42:"raft"},Om={3:4,4:3,31:41,41:31,32:42,42:32},T$={fwd:"end",aft:"center",prim:"center",left:"center",right:"center",lfwd:"start",rfwd:"start",laft:"end",raft:"end"},M$={left:"end",lfwd:"end",laft:"end",right:"start",rfwd:"start",raft:"start"},j$=[1,3,31,32,0,4,41,42,2],E$=[1,3,31,0,4,41,32,2,42],Lm=[31,32,41,42],D$='"lup  cup  rup" "llo  clo  rlo"',P$=[{area:"lup",side:"left",upper:!0,stack:["lfwd","left"]},{area:"cup",upper:!0,stack:["fwd","prim"]},{area:"rup",side:"right",upper:!0,stack:["rfwd","right"]},{area:"llo",side:"left",stack:["laft"]},{area:"clo",stack:["aft"]},{area:"rlo",side:"right",stack:["raft"]}],A$=[3,4,31,41,32,42];class R$ extends I.Component{constructor(e){super(e),this.elementRef=I.createRef(),this.controlsRef=I.createRef(),this.popupRef=I.createRef(),this.hitChartBtnRef=I.createRef(),this.sectionGridRef=I.createRef(),this.staggerPrimaryRef=I.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return Qs(this.props.ship)?"left":"right"}isMirroredGrip(){return this.side()==="right"}applyScreenFit(){const e=this.elementRef.current;if(!e)return;const n=Bm(),r=Ks(this.side());if(!n&&r===1){(this.screenFit!==1||this.screenFitHeight)&&(e.style.transform="",e.style.transformOrigin="",e.style.maxHeight="",this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}const i=this.measureNatural();if(!i)return;const o=n?F$():null,s=window.innerHeight||document.documentElement.clientHeight;let l=1;if(n){const h=(document.documentElement.clientWidth||window.innerWidth)*o.fillW,p=s*o.fillH;l=Math.min(h/i.width,p/i.height),l=Math.min(o.max,Math.max(o.min,l))}this.autoFit=l;let c=Ad(l*r);c=Math.round(c*100)/100;let d=null;if(n){const h=Math.min(Dd,o.fillH*Math.max(1,r));d=Math.round(s*h/c)}c===this.screenFit&&d===this.screenFitHeight||(this.screenFit=c,this.screenFitHeight=d,e.style.transformOrigin=this.transformOrigin(),e.style.transform=c===1?"":"scale("+c+")",e.style.maxHeight=d==null?"":d+"px",this.resizeStart||this.keepGripOnScreen())}measureNatural(){const e=this.elementRef.current;if(!e)return null;const n=e.style.maxHeight;e.style.maxHeight="none";const r=e.offsetWidth,i=e.offsetHeight;return e.style.maxHeight=n,r&&i?{width:r,height:i}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:Qs(this.props.ship)?"top left":"top right"}onScreenResize(){this.applyScreenFit()}isDragHandle(e){return!e||!e.closest||e.closest(".shipwindow-nodrag")?!1:!!e.closest(".shipwindow-drag-handle")}isResizeHandle(e){return!!(e&&e.closest&&e.closest(".shipwindow-resize-grip"))}isDragSlop(e,n){if(!e||!e.closest||!e.closest(".shipwindow-grab-slop"))return!1;const r=this.elementRef.current,i=r&&r.querySelector(".shipwindow-drag-handle");if(!i)return!1;const o=i.getBoundingClientRect();return n>=o.top&&n<=o.bottom+U$}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(e,n,r){const i=Date.now(),o=!!this.lastPress&&this.lastPress.kind===e&&i-this.lastPress.time<G$;this.lastPress={kind:e,time:i},this.pressPoint={x:n,y:r},this.pendingReset=o}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(e,n){const r=this.elementRef.current;if(!r)return!1;const i=window.getComputedStyle(r);let o=parseFloat(i.left),s=parseFloat(i.top);return isFinite(o)||(o=r.offsetLeft),isFinite(s)||(s=r.offsetTop),this.dragStart={x:e,y:n,left:o,top:s},this.positioned=!0,r.style.left=o+"px",r.style.top=s+"px",r.style.right="auto",!0}moveDrag(e,n){const r=this.elementRef.current;!this.dragStart||!r||(r.style.left=this.dragStart.left+(e-this.dragStart.x)+"px",r.style.top=this.dragStart.top+(n-this.dragStart.y)+"px",this.clampIntoView())}beginResize(e,n){const r=this.elementRef.current;if(!r)return!1;const i=this.measureNatural();if(!i)return!1;const o=window.getComputedStyle(r);let s=parseFloat(o.left),l=parseFloat(o.top);isFinite(s)||(s=r.offsetLeft),isFinite(l)||(l=r.offsetTop);const c=this.screenFit||1,d=this.isMirroredGrip(),h=d?"top right":"top left";!d&&this.transformOrigin()==="top right"&&(s+=i.width*(1-c)),r.style.left=s+"px",r.style.top=l+"px",r.style.right="auto",r.style.transformOrigin=h,this.resizeOrigin=h,this.positioned=!0;const p=r.getBoundingClientRect(),f=d?p.right:p.left,v=d?-1:1;return this.resizeStart={originX:f,originY:p.top,flipX:v,width:i.width,height:i.height,scale:c,maxScale:d?f/i.width:1/0,base:Fm(i.width,i.height,v*(e-f),n-p.top)},!0}moveResize(e,n){const r=this.resizeStart;if(!r)return;const i=Fm(r.width,r.height,r.flipX*(e-r.originX),n-r.originY),o=Ad(Math.min(r.maxScale,r.scale+(i-r.base))),s=this.side(),l=Math.round(o/(this.autoFit||1)*100)/100;l!==Ks(s)&&(_m(s,l),this.applyScreenFit())}moveGesture(e,n){this.pressPoint&&(Math.abs(e-this.pressPoint.x)>Nm||Math.abs(n-this.pressPoint.y)>Nm)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(e,n):this.moveDrag(e,n)}finishGesture(){const e=this.elementRef.current,n=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),n){const r=this.side();Hm(r,Ks(r)),this.keepGripOnScreen(),this.clampIntoView()}e&&(zm[this.side()]={top:parseFloat(e.style.top)||0,left:parseFloat(e.style.left)||0})}clampIntoView(e){const n=this.elementRef.current;if(!n||!this.positioned)return;const r=n.getBoundingClientRect(),i=document.documentElement.clientWidth||window.innerWidth,o=window.innerHeight||document.documentElement.clientHeight;let s=0,l=0;r.top<0?l=-r.top:r.top>o-Er&&(l=o-Er-r.top),e&&r.width<=i&&r.left<0?s=-r.left:r.right<Er?s=Er-r.right:r.left>i-Er&&(s=i-Er-r.left),!(!s&&!l)&&(n.style.left=(parseFloat(n.style.left)||0)+s+"px",n.style.top=(parseFloat(n.style.top)||0)+l+"px")}keepGripOnScreen(){const e=this.elementRef.current;if(!e)return;const n=e.getBoundingClientRect(),r=document.documentElement.clientWidth||window.innerWidth;let i;if(this.isMirroredGrip()){if(n.left>=0||(i=Math.min(-n.left,r-n.right),i<=0))return}else if(i=r-n.right,i>=0)return;const o=window.getComputedStyle(e);let s=parseFloat(o.left);isFinite(s)||(s=e.offsetLeft),e.style.left=s+i+"px",e.style.right="auto",this.positioned=!0}resetUserScale(){const e=this.side();Ks(e)!==1&&(_m(e,1),Hm(e,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(e){e.stopPropagation(),this.resetUserScale()}onDragStart(e){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] pointerdown",e.pointerType,"handle:",this.isDragHandle(e.target),"grip:",this.isResizeHandle(e.target)),e.pointerType==="touch"||e.button!=null&&e.button>0)return;const n=this.isResizeHandle(e.target);if(!n&&!this.isDragHandle(e.target))return;if(this.notePress(n?"grip":"header",e.clientX,e.clientY),!(n?this.beginResize(e.clientX,e.clientY):this.beginDrag(e.clientX,e.clientY))){this.cancelDoublePress();return}const r=this.elementRef.current;let i=!1;try{r.setPointerCapture(e.pointerId),i=!0}catch{}this.dragTarget=i?r:document,this.dragTarget.addEventListener("pointermove",this.onDragMove),this.dragTarget.addEventListener("pointerup",this.onDragEnd),this.dragTarget.addEventListener("pointercancel",this.onDragEnd),this.dragPointerId=e.pointerId,e.preventDefault()}onDragMove(e){!this.gestureActive()||e.pointerId!==this.dragPointerId||this.moveGesture(e.clientX,e.clientY)}onDragEnd(e){!this.gestureActive()||e&&e.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(e){if(window.FV_DRAG_DEBUG&&console.log("[shipwindow] touchstart",e.touches&&e.touches.length,"handle:",this.isDragHandle(e.target),"grip:",this.isResizeHandle(e.target)),this.gestureActive()||!e.touches||e.touches.length!==1)return;const n=e.touches[0],r=this.isResizeHandle(e.target);if(!(!r&&!this.isDragHandle(e.target)&&!this.isDragSlop(e.target,n.clientY))){if(this.notePress(r?"grip":"header",n.clientX,n.clientY),!(r?this.beginResize(n.clientX,n.clientY):this.beginDrag(n.clientX,n.clientY))){this.cancelDoublePress();return}this.touchDragId=n.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.addEventListener("touchend",this.onTouchDragEnd),document.addEventListener("touchcancel",this.onTouchDragEnd),e.cancelable&&e.preventDefault()}}onTouchDragMove(e){const n=Wm(e.touches,this.touchDragId);if(!this.gestureActive()||!n)return;this.moveGesture(n.clientX,n.clientY);const r=this.dragScroll;r&&((window.scrollX||window.pageXOffset||0)!==r.x||(window.scrollY||window.pageYOffset||0)!==r.y)&&window.scrollTo(r.x,r.y),e.cancelable&&e.preventDefault()}onTouchDragEnd(e){e&&e.touches&&Wm(e.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener("touchmove",this.onTouchDragMove,{passive:!1}),document.removeEventListener("touchend",this.onTouchDragEnd),document.removeEventListener("touchcancel",this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){const e=this.dragTarget;e&&(e.removeEventListener("pointermove",this.onDragMove),e.removeEventListener("pointerup",this.onDragEnd),e.removeEventListener("pointercancel",this.onDragEnd),this.dragPointerId!=null&&e.hasPointerCapture&&e.hasPointerCapture(this.dragPointerId)&&e.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(e){e.stopPropagation();let{ship:n}=this.props;if(this.ignoreNextClick){this.ignoreNextClick=!1;return}if(Ln()){window.uiEvents.relay("CloseSystemInfo");return}window.uiEvents.relay("SystemClicked",{ship:n,system:n,element:e.target})}onShipTouchMove(e){if(!this.longPressTimer)return;const n=e.touches[0],r=n.clientX-this.touchStartX,i=n.clientY-this.touchStartY;(Math.abs(r)>10||Math.abs(i)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(e){this.longPressTimer&&(clearTimeout(this.longPressTimer),this.longPressTimer=null),this.touchActive=!1,window.uiEvents.relay("SystemMouseOut")}onShipTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay("SystemMouseOut"),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:n}=this.props,r=shipManager.systems.getSystemByName(n,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:n,system:r||n.systems[0],element:e.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay("SystemMouseOut")}onUnknownTouchStart(e){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);const n=e.currentTarget,r=e.touches[0];this.touchStartX=r.clientX,this.touchStartY=r.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:i}=this.props,o=shipManager.systems.getSystemByName(i,"mineStealth");window.uiEvents.relay("SystemMouseOver",{ship:i,system:o||i.systems[0],element:n,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){const e=this.elementRef.current,n=Qs(this.props.ship)?"left":"right";e.addEventListener("pointerdown",this.onDragStart),e.addEventListener("touchstart",this.onTouchDragStart,{passive:!1});const r=zm[n];if(r&&!Bm()){const i=Math.max(0,Math.min(r.top,window.innerHeight-60)),o=Math.max(60-e.offsetWidth,Math.min(r.left,window.innerWidth-60));e.style.top=i+"px",e.style.left=o+"px",e.style.right="auto",this.positioned=!0}document.addEventListener("pointerdown",this.onDocumentPointerDown),this.alignStaggeredPrimary(),this.applyScreenFit(),window.addEventListener("resize",this.onScreenResize),window.addEventListener("orientationchange",this.onScreenResize)}componentDidUpdate(){this.alignStaggeredPrimary(),this.applyScreenFit()}alignStaggeredPrimary(){const e=this.sectionGridRef.current,n=this.staggerPrimaryRef.current;if(!e||!n)return;const r=n.offsetHeight/2;e.style.setProperty("--fv-prim-half",r+"px"),e.style.setProperty("--fv-art-dy",n.offsetTop+r-e.clientHeight/2+"px")}componentWillUnmount(){document.removeEventListener("pointerdown",this.onDocumentPointerDown),window.removeEventListener("resize",this.onScreenResize),window.removeEventListener("orientationchange",this.onScreenResize);const e=this.elementRef.current;e&&(e.removeEventListener("pointerdown",this.onDragStart),e.removeEventListener("touchstart",this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(e){this.panelHoverTimer&&(clearTimeout(this.panelHoverTimer),this.panelHoverTimer=null),this.state.hoverPanel!==e&&this.setState({hoverPanel:e})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(e){if(!this.state.openPanel)return;const n=this.controlsRef.current,r=this.popupRef.current;n&&n.contains(e.target)||r&&r.contains(e.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay("CloseShipWindow",{ship:this.props.ship})}togglePanel(e,n){n.stopPropagation(),this.setState(r=>({openPanel:r.openPanel===e?null:e}))}toggleArt(e){e&&e.stopPropagation(),this.setState(n=>({showArt:!n.showArt,openPanel:null}))}renderResizeGrip(e){return a.jsx(g$,{className:"shipwindow-resize-grip shipwindow-nodrag",$pad:W$,$mirror:this.isMirroredGrip(),$overlap:!!e,onDoubleClick:this.onGripDoubleClick,title:"Drag to resize this window — double-click to reset its size"})}renderStatusStrip(e,n){const r=n?[{key:"rolled",text:"⟲ Rolled — port / starboard reversed"}].concat(Ym(e)):Ym(e);return a.jsxs(I.Fragment,{children:[r.map((i,o)=>a.jsx(k$,{$color:i.color,$bg:i.bg,$grip:o===r.length-1,children:i.text},i.key)),this.renderResizeGrip(r.length>0)]})}renderHeader(e,n,r){return a.jsxs(u$,{className:"shipwindow-drag-handle",title:"Drag to move — double-click to reset window size",children:[a.jsx(h$,{$tint:r,title:e,children:e}),a.jsx(p$,{title:n,children:n}),a.jsx(f$,{className:"shipwindow-nodrag",onClick:this.close.bind(this),children:"✕"})]})}renderHitChartButton(e){const{openPanel:n}=this.state;return a.jsxs(Vs,{ref:this.hitChartBtnRef,$wide:e,$active:n==="hitchart",onClick:this.togglePanel.bind(this,"hitchart"),children:[a.jsx(Am,{children:"⊕"}),"Hit Chart"]})}renderStatsButton(e){const{openPanel:n}=this.state;return a.jsxs(Vs,{$wide:e,$active:n==="shipstats",onClick:this.togglePanel.bind(this,"shipstats"),onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[a.jsxs(Em,{children:[a.jsx("i",{}),a.jsx("i",{}),a.jsx("i",{})]}),"Ship Stats"]})}renderArtButton(e){return a.jsxs(Vs,{$wide:e,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[a.jsx(x$,{}),"Ship Art"]})}renderNotesButton(e){const{openPanel:n}=this.state;return a.jsxs(Vs,{$wide:e,$active:n==="notes",onClick:this.togglePanel.bind(this,"notes"),onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[a.jsx(Am,{children:"✎"}),"Notes"]})}renderControls(e,n,r,i){const o=Ln(),s=Gm(this.props.ship),l=Vm(this.props.ship);if(!e&&!n&&!i&&!s&&!l)return null;const c=o;return a.jsxs(m$,{ref:this.controlsRef,$compact:r,children:[e&&this.renderHitChartButton(c),s&&this.renderArtButton(c),l&&this.renderStatsButton(c),n&&this.renderNotesButton(c),i&&a.jsx(Pm,{ship:this.props.ship})]})}getButtonLeft(){const e=this.controlsRef.current,n=this.elementRef.current;return!e||!n?null:Math.round((e.getBoundingClientRect().left-n.getBoundingClientRect().left)/(this.screenFit||1)-n.clientLeft)}getAnchorBelow(e,n){const r=e&&e.current,i=this.elementRef.current;if(!r||!i)return{top:n,left:this.getButtonLeft()};const o=r.getBoundingClientRect(),s=i.getBoundingClientRect(),l=this.screenFit||1;return{left:Math.round((o.left-s.left)/l-i.clientLeft),top:Math.round((o.bottom-s.top)/l-i.clientTop)+4}}renderPopup(e,n,r){const{ship:i}=this.props,{openPanel:o,hoverPanel:s}=this.state,l=o||s;if(!l)return null;const c=l==="hitchart"&&Ln()?this.hitChartBtnRef:this.controlsRef,{top:d,left:h}=this.getAnchorBelow(c,r);return l==="hitchart"&&e?a.jsx(Md,{ref:this.popupRef,$top:d,$left:h,$fit:!0,children:a.jsx(qk,{ship:i})}):l==="shipstats"&&Vm(i)?a.jsx(Md,{ref:this.popupRef,$top:d,$left:h,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"shipstats"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:a.jsx(Pm,{ship:i,live:!0,bare:!0})}):l==="notes"&&n?a.jsx(Md,{ref:this.popupRef,$top:d,$left:h,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,"notes"),onMouseLeave:this.onPanelHoverEnd.bind(this),children:a.jsx(fm,{ship:i,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){const{ship:e}=this.props,n=Ln(),r=Qs(e);var i=e.shipClass,o=e.name;let s=!1;if(e.mine){var l=shipManager.systems.getSystemByName(e,"mineStealth");l&&!l.isMineRevealed(e)&&(i="Mine",o="Mine",s=!0)}o||(o=i,i="");const c=!s&&Q$(e),d=!n&&!s&&X$(e);if(e.flight){if(n)return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:D=>{D.preventDefault(),D.stopPropagation()},$isMyTeam:r,$variant:"flightLobby",children:[this.renderHeader(o,i,Rd()),a.jsxs(b$,{children:[a.jsx(S$,{children:a.jsx(wd,{ship:e})}),a.jsx(Td,{ship:e})]}),this.renderResizeGrip()]});const $=!!(window.ew&&ew.isFlightEwPool(e));return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:D=>{D.preventDefault(),D.stopPropagation()},$isMyTeam:r,$variant:$?"flightEw":"flight",children:[this.renderHeader(o,i,Rd()),$?a.jsxs(c$,{children:[a.jsx(d$,{children:a.jsx(wd,{ship:e})}),a.jsx($m,{ship:e,flight:!0})]}):a.jsx(wd,{ship:e}),this.renderStatusStrip(e)]})}if(s)return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:$=>{$.preventDefault(),$.stopPropagation()},$isMyTeam:r,$variant:"terrain",children:[this.renderHeader(o,i,null),a.jsxs(Im,{children:[a.jsx(Ed,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(jd,{$img:window.AssetManager.getSmartImagePath(e.imagePath)}),a.jsx($$,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:"?"})]}),this.renderResizeGrip()]});const h=tC(e),p=eC(h);if((window.gamedata.isTerrain(e.shipSizeClass,e.userid)||e.mine)&&!Um(h)){const $=c||d||Gm(e);return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:D=>{D.preventDefault(),D.stopPropagation()},$isMyTeam:r,$variant:"terrain",children:[this.renderHeader(o,i,null),a.jsxs(Im,{children:[a.jsx(Ed,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(jd,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:e.mine&&$?K$:0}),this.renderControls(c,d,!0),E$.map(D=>h[D].length>0&&a.jsx(wm,{location:D,nameOverride:p[D],ship:e,systems:h[D],isTerrain:!0,hidden:this.state.showArt},`section-${e.id}-${D}`))]}),n&&a.jsx(Td,{ship:e,full:!0}),this.renderStatusStrip(e),this.renderPopup(c,d,72)]})}const v=shipManager.movement.isRolled(e),x=!!e.enhancementTooltip||$d(e)!==null,b=e.base&&!e.smallBase,M=oC(e,h),w=M?D$:nC(h,x),g=j$.map($=>{if(h[$].length===0)return null;const D=v&&Om[$]!==void 0?Om[$]:$,E=C$[D],H=$===0||$===1||$===2||b&&Lm.includes($);return{area:E,element:a.jsx(wm,{location:$,displayLocation:D,area:E,valign:M?void 0:T$[E],justify:M?void 0:M$[E],wide:H,minHeight:void 0,nameOverride:p[$],hidden:this.state.showArt,ship:e,systems:h[$]},`section-${e.id}-${$}`)}}).filter(Boolean),y=this.renderControls(c,d,!1,n&&!e.mine),S=n?a.jsx(Td,{ship:e,grid:!0,hideEnhancements:x}):a.jsx($m,{ship:e}),T=x&&a.jsx(a$,{ship:e}),C=$=>{const D=$.stack.map(K=>g.find(he=>he.area===K)).filter(Boolean).map(K=>K.area==="prim"?a.jsx(v$,{ref:this.staggerPrimaryRef,children:K.element},"prim"):K.element),E=$.area==="lup"?y:$.area==="rup"?S:null,H=$.area==="rlo"?T:null;return a.jsxs(w$,{$area:$.area,$side:$.side,$upper:$.upper,children:[E&&a.jsx(Rm,{$top:!0,children:E}),D,H&&a.jsx(Rm,{children:H})]},$.area)},k=a.jsxs(y$,{ref:this.sectionGridRef,$areas:w,$staggered:M,children:[a.jsx(Ed,{className:"shipwindow-grab-slop",onClick:this.onShipClick.bind(this)}),a.jsx(jd,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:n&&Um(h)?Y$:0,$offsetVar:M?"--fv-art-dy":void 0}),M?P$.map(C):a.jsxs(a.Fragment,{children:[y,S,T,g.map($=>$.element)]})]});return a.jsxs(Bi,{ref:this.elementRef,onClick:Ni,onContextMenu:$=>{$.preventDefault(),$.stopPropagation()},$isMyTeam:r,$variant:"ship",children:[this.renderHeader(o,i,Rd()),k,this.renderStatusStrip(e,v),this.renderPopup(c,d)]})}}const Ni=()=>window.uiEvents.relay("CloseSystemInfo"),zm={left:null,right:null},I$="(max-width: 1024px)",O$="(orientation: portrait)",L$={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},z$={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},Dd=.98,Bm=()=>!!window.matchMedia&&window.matchMedia(I$).matches,B$=()=>window.matchMedia?window.matchMedia(O$).matches:window.innerHeight>=window.innerWidth,N$=(t,e)=>e===1?t:{fillW:Math.min(Dd,t.fillW*e),fillH:Math.min(Dd,t.fillH*e),min:Math.min(t.max,t.min*e),max:t.max},F$=()=>{const t=Ln()?z$:L$;return B$()?N$(t,t.portrait):t},_$=.35,H$=3,W$=6,U$=8,G$=400,Nm=6,Er=40,Pd="fv.shipwindow.userScale",Ad=t=>Math.min(H$,Math.max(_$,t)),Fm=(t,e,n,r)=>(n*t+r*e)/(t*t+e*e),Ys={left:null,right:null},Ks=t=>(Ys[t]==null&&(Ys[t]=V$(t)),Ys[t]),_m=(t,e)=>{Ys[t]=e},V$=t=>{try{let e=parseFloat(window.localStorage.getItem(Pd+"."+t));if(isFinite(e)||(e=parseFloat(window.localStorage.getItem(Pd))),isFinite(e)&&e>0)return Ad(e)}catch{}return 1},Hm=(t,e)=>{try{window.localStorage.setItem(Pd+"."+t,String(e))}catch{}},Wm=(t,e)=>{if(!t||e==null)return null;for(let n=0;n<t.length;n++)if(t[n].identifier===e)return t[n];return null},Ln=()=>!!window.gamedata&&window.gamedata.gamephase===-2,Y$=35,Um=t=>A$.some(e=>t[e].length>0),K$=20,Qs=t=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide=="function"?window.ShipWindowManager.isLeftSide(t):t.team===window.gamedata.getPlayerTeam(),Q$=t=>!!t.hitChart&&Object.keys(t.hitChart).length>0,Gm=t=>!!(t&&t.imagePath)&&!t.flight,Vm=t=>!!t&&!Ln()&&!t.flight&&!t.mine&&!window.gamedata.isTerrain(t.shipSizeClass,t.userid),X$=t=>!!t.notes||!!t.enhancementTooltip||!!(t.hasAttached&&Object.keys(t.hasAttached).length>0),Rd=t=>null,Ym=t=>{if(Ln())return[];const e=[],n=shipManager.getTurnDeployed(t);n>window.gamedata.turn&&n<999&&e.push({key:"deploying",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Deploying on Turn "+n});const r=shipManager.getArrivalIniPenalty(t);r!==0&&e.push({key:"arrivalScatter",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:"Arrival Scatter "+r+" Ini"});const i=t.trueStealth?shipManager.getStealthToggleForecast(t):null;if(t.trueStealth)if(Z$(t,i))e.push({key:"undetected",color:m.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Undetected"});else if(t.mine){const d=shipManager.systems.getSystemByName(t,"mineStealth");!d||d.isMineRevealedToOpponent(t)?e.push({key:"detected",color:m.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected - Revealed"}):e.push({key:"detected",color:m.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Detected - Not Revealed"})}else i===!0?e.push({key:"detected",color:m.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Would be Detected"}):e.push({key:"detected",color:m.colors.statusBad,bg:"rgba(255, 80, 80, 0.10)",text:"Detected"});shipManager.isJumpingToHyperspace(t)&&e.push({key:"jumping",color:m.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:"Jumping to Hyperspace"});const o=t.flight&&window.fighterMissions?window.fighterMissions.bannerText(t):null;o&&e.push(o.tone==="warning"?{key:"fighterMission",color:m.colors.warning,bg:"rgba(225, 176, 0, 0.10)",text:o.text}:{key:"fighterMission",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:o.text});const s=t.flight?null:shipManager.movement.getExtendedTurnStatus(t);s&&e.push(s.cancelled?{key:"extendedTurn",color:m.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:s.text}:{key:"extendedTurn",color:m.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:s.text});const l=window.JumpEngine&&typeof window.JumpEngine.getAbductionChain=="function"?window.JumpEngine.getAbductionChain(t.id):null;l&&e.push({key:"abducted",color:"#b36bff",bg:"rgba(127, 0, 255, 0.10)",text:"Being abducted: "+window.JumpEngine.formatAbductionHalves(l.total)+"/"+l.cost+" power-turns"});const c=shipManager.getHangarManoeuvre(t);if(c&&e.push({key:"hangarManoeuvre",color:m.colors.statusPending,bg:"rgba(0, 184, 230, 0.10)",text:c.text}),t.attached&&Object.keys(t.attached).length>0&&!t.detached&&!(c&&c.riding)){const d=window.gamedata.getShip(Object.keys(t.attached)[0]);if(d){const h=Object.values(t.attached)[0];let p="";h==1?p="Front":h==2?p="Aft":h==3||h==31||h==32?p="Port":(h==4||h==41||h==42)&&(p="Starboard"),e.push({key:"attached",color:m.colors.statusOk,bg:"rgba(50, 205, 50, 0.08)",text:"Attached to "+d.name+(p?" ["+p+"]":"")})}}return t.hasAttached&&Object.keys(t.hasAttached).length>0&&Object.keys(t.hasAttached).filter(h=>{const p=window.gamedata.getShip(h);return!(p&&shipManager.isDockingRider(p))}).length>0&&e.push({key:"boarded",color:m.colors.statusAlert,bg:"rgba(255, 165, 0, 0.10)",text:"Ship is being boarded!"}),t.flight&&q$(t)&&e.push({key:"edfGrounded",color:J$,bg:"rgba(210, 80, 255, 0.12)",text:"Energy Drained - cannot fire"}),e},J$="#d250ff",q$=t=>{const e=shipManager.systems.getSystem(t,1);return!e||!e.criticals?!1:e.criticals.some(n=>n.phpclass==="EdfFighterGrounded"&&n.turn+1===window.gamedata.turn)},Z$=(t,e)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(t)==gamedata.turn||shipManager.getTurnDeployed(t)==gamedata.turn))return!0;if(e!=null)return!e;let n=shipManager.isDetected(t);if(!n&&t.team==gamedata.getPlayerTeam()){let r=null;t.mine?r=shipManager.systems.getSystemByName(t,"mineStealth"):t.faction=="Torvalus Speculators"?r=shipManager.systems.getSystemByName(t,"ShadingField"):shipManager.getSpecialAbilityStealth(t,"Cloaking")?r=shipManager.systems.getSystemByName(t,"CloakingDevice"):shipManager.getSpecialAbilityStealth(t,"Stealth")&&(r=shipManager.systems.getSystemByName(t,"stealth")),r&&(Array.isArray(r.detected)&&r.detected.length>0||r.detected===!0||Array.isArray(r.detectedNew)&&r.detectedNew.length>0||r.detectedNew===!0)&&(n=!0)}return!n},eC=t=>{const e={};return[{locations:[3,31,32],name:"Port"},{locations:[4,41,42],name:"Starboard"}].forEach(n=>{const r=n.locations.filter(i=>t[i].some(o=>o.name==="structure"));r.length===1&&r[0]!==n.locations[0]&&(e[r[0]]=n.name)}),e},tC=t=>{const e={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return t.systems.forEach(n=>{n.hideInShipWindow||e[n.location].push(n)}),e},nC=(t,e)=>{const n=[["ctrl","fwd","ew"]];let r=0;if((t[31].length||t[41].length)&&(n.push(["lfwd","prim","rfwd"]),r++),(t[3].length||t[4].length)&&(n.push(["left","prim","right"]),r++),(t[32].length||t[42].length)&&(n.push(["laft","prim","raft"]),r++),r===0&&t[0].length&&n.push([null,"prim",null]),t[2].length&&n.push([null,"aft",null]),e){const i=n[n.length-1];n.length>1&&i[2]===null?i[2]="enh":n.push([null,null,"enh"])}for(let i=1;i<n.length&&n[i][0]===null;i++)n[i][0]="ctrl";for(let i=1;i<n.length&&n[i][2]===null;i++)n[i][2]="ew";return n.map(i=>`"${i[0]||"."}  ${i[1]||"."}  ${i[2]||"."}"`).join(" ")},rC=4,iC=t=>Math.ceil(t.filter(e=>e.name!=="structure").length/4),oC=(t,e)=>!!(t.base&&!t.smallBase)&&Lm.some(n=>iC(e[n])>rC),sC=u.div`

`,aC=u.div`
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
`,lC=u.span`
    cursor: pointer;
    color: ${m.colors.line};
    font-size: 16px;

    &:hover {
        color: ${m.colors.text};
    }
`;class cC extends I.Component{constructor(e){super(e),this.state={error:null}}static getDerivedStateFromError(e){return{error:e}}componentDidCatch(e,n){const r=this.props.ship;console.error("Ship window render failed for",r&&(r.name||r.shipClass),e,n)}render(){if(this.state.error){const e=this.props.ship;return a.jsxs(aC,{children:[a.jsxs("span",{children:[e&&(e.name||e.shipClass)||"Ship"," — window failed to render (see console)"]}),a.jsx(lC,{onClick:()=>window.uiEvents.relay("CloseShipWindow",{ship:e}),children:"✕"})]})}return this.props.children}}class dC extends I.Component{render(){const{ships:e}=this.props;return a.jsx(sC,{children:e.map(n=>a.jsx(cC,{ship:n,children:a.jsx(R$,{ship:n})},`shipwindow-${n.userid}-${n.id}`))})}}const uC=u.div`
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
`;const hC=u.div`
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
`,pC=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,fC=u.span`
    flex: 0 0 auto;
    color: ${P.dim};
    font-size: 10px;
    user-select: none;
`,Km=u.div`
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
`,gC=u.input`
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
    background-color: ${P.well};
    border: 1px solid ${P.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${P.focus}; }
`,mC=u.div`
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
    font-family: ${m.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${P.line}; color: #ffffff; }
`,xC=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e};class yC extends I.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Ii(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(e,n){const{ship:r}=this.props,i=this.maxHealth();let o=parseInt(n,10);isNaN(o)&&(o=i),o=Math.max(1,Math.min(i,o)),battleDamage.setFighter(r,e,{d:i-o}),this.refresh()}step(e,n){this.setRemaining(e,battleDamage.fighterHealth(this.props.ship,e)+n)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,"");this.setRemaining(e,r===""?0:parseInt(r,10))}propagate(){const{ship:e}=this.props,n=parseInt(e.flightSize,10)||0,r=battleDamage.fighterMaxHealth(e)-battleDamage.fighterHealth(e,1);for(let i=2;i<=n;i++)battleDamage.setFighter(e,i,{d:r});this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){const{ship:e,boundingBox:n}=this.props,r=parseInt(e&&e.flightSize,10)||0,i=this.maxHealth();if(!r||!i)return null;battleDamage.flightSummary(e);const o=[];for(let c=1;c<=r;c++){const d=battleDamage.fighterHealth(e,c);o.push(a.jsxs(hC,{children:[a.jsxs(pC,{children:["Fighter ",c]}),a.jsx(Km,{title:"More damage",disabled:d<=1,onClick:()=>this.step(c,-1),children:"−"}),a.jsx(gC,{ref:this.wheelRef(c),type:"text",value:d,onChange:h=>this.onInput(c,h)}),a.jsx(Km,{title:"Repair",disabled:d>=i,onClick:()=>this.step(c,1),children:"+"}),a.jsxs(fC,{children:["/ ",i]})]},`ftr-${c}`))}const s=battleDamage.flightCritEntry(e)||{},l=Og(s.c,e.preBattleCritDesc,e.preBattleCritTransient,s.p);return a.jsxs(uC,{$position:xC(n),onClick:c=>c.stopPropagation(),children:[a.jsx(Rg,{$sticky:!0,children:"Fighter Damage"}),o,r>1&&a.jsx(mC,{title:"Copy Fighter 1's damage to every fighter in this flight",onClick:()=>this.propagate(),children:"Apply Fighter 1's damage to all"}),a.jsx(Lg,{ship:e,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:l,editable:!0,onChange:()=>this.refresh()})]})}}const wC=u.div`
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
`,vC=u.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${P.dim};
    user-select: none;
`,bC=u.div`
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
`,SC=u.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,kC=u.span`
    flex: 0 0 auto;
    color: ${P.dim};
    font-size: 10px;
    user-select: none;
`,Qm=u.div`
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
`,$C=u.input`
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
    background-color: ${P.well};
    border: 1px solid ${P.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${P.focus}; }
`,CC=u.div`
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
    font-family: ${m.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${P.line}; color: #ffffff; }
`,TC=t=>{const e={};return t.top>window.innerHeight/2?e.bottom=window.innerHeight-t.top:e.top=t.top+t.height,t.left>window.innerWidth/2?e.right=window.innerWidth-t.right:e.left=t.left,e};class MC extends I.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=Ii(n=>this.step(e,n.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(e,n){const{ship:r}=this.props,i=this.maxHealth();let o=parseInt(n,10);isNaN(o)&&(o=i),o=Math.max(1,Math.min(i,o)),battleDamage.setMine(r,e,{d:i-o}),this.refresh()}step(e,n){this.setRemaining(e,battleDamage.mineHealth(this.props.ship,e)+n)}onInput(e,n){const r=String(n.target.value).replace(/[^0-9]/g,"");this.setRemaining(e,r===""?0:parseInt(r,10))}propagate(){const{ship:e}=this.props,n=battleDamage.mineCount(e),r=battleDamage.getEntry(e,battleDamage.KIND_MINE,1);for(let i=2;i<=n;i++)battleDamage.setWholeEntry(e,battleDamage.KIND_MINE,i,r);this.refresh()}refresh(){const{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow=="function"&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){const{ship:e,boundingBox:n}=this.props,r=battleDamage.mineCount(e),i=this.maxHealth();if(!r||i<2)return null;const o=battleDamage.mineSummary(e),s=[];for(let l=1;l<=r;l++){const c=battleDamage.mineHealth(e,l);s.push(a.jsxs(bC,{children:[a.jsxs(SC,{children:["Mine ",l]}),a.jsx(Qm,{title:"More damage",disabled:c<=1,onClick:()=>this.step(l,-1),children:"−"}),a.jsx($C,{ref:this.wheelRef(l),type:"text",value:c,onChange:d=>this.onInput(l,d)}),a.jsx(Qm,{title:"Repair",disabled:c>=i,onClick:()=>this.step(l,1),children:"+"}),a.jsxs(kC,{children:["/ ",i]})]},`mne-${l}`))}return a.jsxs(wC,{$position:TC(n),onClick:l=>l.stopPropagation(),children:[a.jsx(Rg,{$sticky:!0,children:"Mine Damage"}),a.jsxs(vC,{children:[o.remaining," / ",o.total," structure"]}),s,r>1&&a.jsx(CC,{title:"Copy Mine 1's damage to every mine in this purchase",onClick:()=>this.propagate(),children:"Apply Mine 1 to all"})]})}}class jC{constructor(e){this.parentElement=e,this.roots=new Map}getRoot(e){const n=jQuery(e,this.parentElement)[0];return n?(this.roots.has(n)||this.roots.set(n,nf(n)),this.roots.get(n)):null}unmountRoot(e){const n=jQuery(e,this.parentElement)[0];n&&this.roots.has(n)&&(this.roots.get(n).unmount(),this.roots.delete(n))}EwButtons(e){const n=this.getRoot("#showEwButtons");n&&n.render(a.jsx(Cv,{...e}))}FullScreen(e){const n=this.getRoot("#fullScreen");n&&n.render(a.jsx(xv,{...e}))}Surrender(e){const n=this.getRoot("#surrender");n&&n.render(a.jsx(wv,{...e}))}BackToLobby(e){const n=this.getRoot("#backToLobby");n&&n.render(a.jsx(kv,{...e}))}PlayerSettings(e){const n=this.getRoot("#playerSettings");n&&n.render(a.jsx(Hw,{...e}))}showShipThrustUI(e){const n=this.getRoot("#shipThrust");n&&n.render(a.jsx(pv,{...e}))}hideShipThrustUI(){this.unmountRoot("#shipThrust")}showWeaponList(e){const n=this.getRoot("#weaponList");n&&n.render(a.jsx(VS,{...e}))}hideWeaponList(){this.unmountRoot("#weaponList")}showSystemInfo(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(qS,{...e}))}hideSystemInfo(){this.unmountRoot("#systemInfoReact")}showSystemInfoMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(ok,{...e}))}hideSystemInfoMenu(){this.unmountRoot("#systemInfoReact")}canShowSystemInfoMenu(e,n){return Is(e,n)}showFighterDamageMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(yC,{...e}))}showMineDamageMenu(e){const n=this.getRoot("#systemInfoReact");n&&n.render(a.jsx(MC,{...e}))}renderShipWindows(e){const n=this.getRoot("#shipWindowsReact");n&&n.render(a.jsx(dC,{...e}))}}window.UIManager=jC});
