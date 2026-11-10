"use strict";(function(e){typeof define==`function`&&define.amd?define([],e):e()})(function(){var e=Object.create,t=Object.defineProperty,n=Object.getOwnPropertyDescriptor,r=Object.getOwnPropertyNames,i=Object.getPrototypeOf,a=Object.prototype.hasOwnProperty,o=(e,t)=>()=>(t||(e((t={exports:{}}).exports,t,t.exports),e=null),t.exports),s=(e,i,o,s)=>{if(i&&typeof i==`object`||typeof i==`function`)for(var c=r(i),l=0,u=c.length,d;l<u;l++)d=c[l],!a.call(e,d)&&d!==o&&t(e,d,{get:(e=>i[e]).bind(null,d),enumerable:!(s=n(i,d))||s.enumerable});return e},c=(n,r,o)=>(o=n==null?{}:e(i(n)),s(r||!n||!n.__esModule||!a.call(n,`default`)?t(o,`default`,{value:n,enumerable:!0}):o,n)),l=o((e=>{var t=Symbol.for(`react.transitional.element`),n=Symbol.for(`react.portal`),r=Symbol.for(`react.fragment`),i=Symbol.for(`react.strict_mode`),a=Symbol.for(`react.profiler`),o=Symbol.for(`react.consumer`),s=Symbol.for(`react.context`),c=Symbol.for(`react.forward_ref`),l=Symbol.for(`react.suspense`),u=Symbol.for(`react.memo`),d=Symbol.for(`react.lazy`),f=Symbol.for(`react.activity`),p=Symbol.for(`react.view_transition`),m=Symbol.iterator;function h(e){return typeof e!=`object`||!e?null:(e=m&&e[m]||e[`@@iterator`],typeof e==`function`?e:null)}var g={isMounted:function(){return!1},enqueueForceUpdate:function(){},enqueueReplaceState:function(){},enqueueSetState:function(){}},_=Object.assign,v={};function y(e,t,n){this.props=e,this.context=t,this.refs=v,this.updater=n||g}y.prototype.isReactComponent={},y.prototype.setState=function(e,t){if(typeof e!=`object`&&typeof e!=`function`&&e!=null)throw Error(`takes an object of state variables to update or a function which returns an object of state variables.`);this.updater.enqueueSetState(this,e,t,`setState`)},y.prototype.forceUpdate=function(e){this.updater.enqueueForceUpdate(this,e,`forceUpdate`)};function b(){}b.prototype=y.prototype;function x(e,t,n){this.props=e,this.context=t,this.refs=v,this.updater=n||g}var S=x.prototype=new b;S.constructor=x,_(S,y.prototype),S.isPureReactComponent=!0;var C=Array.isArray;function ee(){}var w={H:null,A:null,T:null,S:null},te=Object.prototype.hasOwnProperty;function T(e,n,r){var i=r.ref;return{$$typeof:t,type:e,key:n,ref:i===void 0?null:i,props:r}}function ne(e,t){return T(e.type,t,e.props)}function re(e){return typeof e==`object`&&!!e&&e.$$typeof===t}function ie(e){var t={"=":`=0`,":":`=2`};return`$`+e.replace(/[=:]/g,function(e){return t[e]})}var ae=/\/+/g;function oe(e,t){return typeof e==`object`&&e&&e.key!=null?ie(``+e.key):t.toString(36)}function se(e){switch(e.status){case`fulfilled`:return e.value;case`rejected`:throw e.reason;default:switch(typeof e.status==`string`?e.then(ee,ee):(e.status=`pending`,e.then(function(t){e.status===`pending`&&(e.status=`fulfilled`,e.value=t)},function(t){e.status===`pending`&&(e.status=`rejected`,e.reason=t)})),e.status){case`fulfilled`:return e.value;case`rejected`:throw e.reason}}throw e}function ce(e,r,i,a,o){var s=typeof e;(s===`undefined`||s===`boolean`)&&(e=null);var c=!1;if(e===null)c=!0;else switch(s){case`bigint`:case`string`:case`number`:c=!0;break;case`object`:switch(e.$$typeof){case t:case n:c=!0;break;case d:return c=e._init,ce(c(e._payload),r,i,a,o)}}if(c)return o=o(e),c=a===``?`.`+oe(e,0):a,C(o)?(i=``,c!=null&&(i=c.replace(ae,`$&/`)+`/`),ce(o,r,i,``,function(e){return e})):o!=null&&(re(o)&&(o=ne(o,i+(o.key==null||e&&e.key===o.key?``:(``+o.key).replace(ae,`$&/`)+`/`)+c)),r.push(o)),1;c=0;var l=a===``?`.`:a+`:`;if(C(e))for(var u=0;u<e.length;u++)a=e[u],s=l+oe(a,u),c+=ce(a,r,i,s,o);else if(u=h(e),typeof u==`function`)for(e=u.call(e),u=0;!(a=e.next()).done;)a=a.value,s=l+oe(a,u++),c+=ce(a,r,i,s,o);else if(s===`object`){if(typeof e.then==`function`)return ce(se(e),r,i,a,o);throw r=String(e),Error(`Objects are not valid as a React child (found: `+(r===`[object Object]`?`object with keys {`+Object.keys(e).join(`, `)+`}`:r)+`). If you meant to render a collection of children, use an array instead.`)}return c}function le(e,t,n){if(e==null)return e;var r=[],i=0;return ce(e,r,``,``,function(e){return t.call(n,e,i++)}),r}function ue(e){if(e._status===-1){var t=e._result,n=t();n.then(function(t){(e._status===0||e._status===-1)&&(e._status=1,e._result=t,n.status===void 0&&(n.status=`fulfilled`,n.value=t))},function(t){(e._status===0||e._status===-1)&&(e._status=2,e._result=t,n.status===void 0&&(n.status=`rejected`,n.reason=t))}),e._status===-1&&(e._status=0,e._result=n)}if(e._status===1)return e._result.default;throw e._result}var E=typeof reportError==`function`?reportError:function(e){if(typeof window==`object`&&typeof window.ErrorEvent==`function`){var t=new window.ErrorEvent(`error`,{bubbles:!0,cancelable:!0,message:typeof e==`object`&&e&&typeof e.message==`string`?String(e.message):String(e),error:e});if(!window.dispatchEvent(t))return}else if(typeof process==`object`&&typeof process.emit==`function`){process.emit(`uncaughtException`,e);return}console.error(e)};function de(e){var t=w.T,n={};n.types=t===null?null:t.types,w.T=n;try{var r=e(),i=w.S;i!==null&&i(n,r),typeof r==`object`&&r&&typeof r.then==`function`&&r.then(ee,E)}catch(e){E(e)}finally{t!==null&&n.types!==null&&(t.types=n.types),w.T=t}}function fe(e){var t=w.T;if(t!==null){var n=t.types;n===null?t.types=[e]:n.indexOf(e)===-1&&n.push(e)}else de(fe.bind(null,e))}var pe={map:le,forEach:function(e,t,n){le(e,function(){t.apply(this,arguments)},n)},count:function(e){var t=0;return le(e,function(){t++}),t},toArray:function(e){return le(e,function(e){return e})||[]},only:function(e){if(!re(e))throw Error(`React.Children.only expected to receive a single React element child.`);return e}};e.Activity=f,e.Children=pe,e.Component=y,e.Fragment=r,e.Profiler=a,e.PureComponent=x,e.StrictMode=i,e.Suspense=l,e.ViewTransition=p,e.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE=w,e.__COMPILER_RUNTIME={__proto__:null,c:function(e){return w.H.useMemoCache(e)}},e.addTransitionType=fe,e.cache=function(e){return function(){return e.apply(null,arguments)}},e.cacheSignal=function(){return null},e.cloneElement=function(e,t,n){if(e==null)throw Error(`The argument must be a React element, but you passed `+e+`.`);var r=_({},e.props),i=e.key;if(t!=null)for(a in t.key!==void 0&&(i=``+t.key),t)!te.call(t,a)||a===`key`||a===`__self`||a===`__source`||a===`ref`&&t.ref===void 0||(r[a]=t[a]);var a=arguments.length-2;if(a===1)r.children=n;else if(1<a){for(var o=Array(a),s=0;s<a;s++)o[s]=arguments[s+2];r.children=o}return T(e.type,i,r)},e.createContext=function(e){return e={$$typeof:s,_currentValue:e,_currentValue2:e,_threadCount:0,Provider:null,Consumer:null},e.Provider=e,e.Consumer={$$typeof:o,_context:e},e},e.createElement=function(e,t,n){var r,i={},a=null;if(t!=null)for(r in t.key!==void 0&&(a=``+t.key),t)te.call(t,r)&&r!==`key`&&r!==`__self`&&r!==`__source`&&(i[r]=t[r]);var o=arguments.length-2;if(o===1)i.children=n;else if(1<o){for(var s=Array(o),c=0;c<o;c++)s[c]=arguments[c+2];i.children=s}if(e&&e.defaultProps)for(r in o=e.defaultProps,o)i[r]===void 0&&(i[r]=o[r]);return T(e,a,i)},e.createRef=function(){return{current:null}},e.forwardRef=function(e){return{$$typeof:c,render:e}},e.isValidElement=re,e.lazy=function(e){return{$$typeof:d,_payload:{_status:-1,_result:e},_init:ue}},e.memo=function(e,t){return{$$typeof:u,type:e,compare:t===void 0?null:t}},e.startTransition=de,e.unstable_useCacheRefresh=function(){return w.H.useCacheRefresh()},e.use=function(e){return w.H.use(e)},e.useActionState=function(e,t,n){return w.H.useActionState(e,t,n)},e.useCallback=function(e,t){return w.H.useCallback(e,t)},e.useContext=function(e){return w.H.useContext(e)},e.useDebugValue=function(){},e.useDeferredValue=function(e,t){return w.H.useDeferredValue(e,t)},e.useEffect=function(e,t){return w.H.useEffect(e,t)},e.useEffectEvent=function(e){return w.H.useEffectEvent(e)},e.useId=function(){return w.H.useId()},e.useImperativeHandle=function(e,t,n){return w.H.useImperativeHandle(e,t,n)},e.useInsertionEffect=function(e,t){return w.H.useInsertionEffect(e,t)},e.useLayoutEffect=function(e,t){return w.H.useLayoutEffect(e,t)},e.useMemo=function(e,t){return w.H.useMemo(e,t)},e.useOptimistic=function(e,t){return w.H.useOptimistic(e,t)},e.useReducer=function(e,t,n){return w.H.useReducer(e,t,n)},e.useRef=function(e){return w.H.useRef(e)},e.useState=function(e){return w.H.useState(e)},e.useSyncExternalStore=function(e,t,n){return w.H.useSyncExternalStore(e,t,n)},e.useTransition=function(){return w.H.useTransition()},e.version=`19.3.0`})),u=o(((e,t)=>{t.exports=l()})),d=o((e=>{function t(e,t){var n=e.length;e.push(t);a:for(;0<n;){var r=n-1>>>1,a=e[r];if(0<i(a,t))e[r]=t,e[n]=a,n=r;else break a}}function n(e){return e.length===0?null:e[0]}function r(e){if(e.length===0)return null;var t=e[0],n=e.pop();if(n!==t){e[0]=n;a:for(var r=0,a=e.length,o=a>>>1;r<o;){var s=2*(r+1)-1,c=e[s],l=s+1,u=e[l];if(0>i(c,n))l<a&&0>i(u,c)?(e[r]=u,e[l]=n,r=l):(e[r]=c,e[s]=n,r=s);else if(l<a&&0>i(u,n))e[r]=u,e[l]=n,r=l;else break a}}return t}function i(e,t){var n=e.sortIndex-t.sortIndex;return n===0?e.id-t.id:n}if(e.unstable_now=void 0,typeof performance==`object`&&typeof performance.now==`function`){var a=performance;e.unstable_now=function(){return a.now()}}else{var o=Date,s=o.now();e.unstable_now=function(){return o.now()-s}}var c=[],l=[],u=1,d=null,f=3,p=!1,m=!1,h=!1,g=!1,_=typeof setTimeout==`function`?setTimeout:null,v=typeof clearTimeout==`function`?clearTimeout:null,y=typeof setImmediate<`u`?setImmediate:null;function b(e){for(var i=n(l);i!==null;){if(i.callback===null)r(l);else if(i.startTime<=e)r(l),i.sortIndex=i.expirationTime,t(c,i);else break;i=n(l)}}function x(e){if(h=!1,b(e),!m){if(n(c)!==null)m=!0,S||(S=!0,ne());else{var t=n(l);t!==null&&ae(x,t.startTime-e)}}}var S=!1,C=-1,ee=5,w=-1;function te(){return g?!0:!(e.unstable_now()-w<ee)}function T(){if(g=!1,S){var t=e.unstable_now();w=t;var i=!0;try{a:{m=!1,h&&(h=!1,v(C),C=-1),p=!0;var a=f;try{b:{for(b(t),d=n(c);d!==null&&!(d.expirationTime>t&&te());){var o=d.callback;if(typeof o==`function`){d.callback=null,f=d.priorityLevel;var s=o(d.expirationTime<=t);if(t=e.unstable_now(),typeof s==`function`){d.callback=s,b(t),i=!0;break b}d===n(c)&&r(c),b(t)}else r(c);d=n(c)}if(d!==null)i=!0;else{var u=n(l);u!==null&&ae(x,u.startTime-t),i=!1}}break a}finally{d=null,f=a,p=!1}i=void 0}}finally{i?ne():S=!1}}}var ne;if(typeof y==`function`)ne=function(){y(T)};else if(typeof MessageChannel<`u`){var re=new MessageChannel,ie=re.port2;re.port1.onmessage=T,ne=function(){ie.postMessage(null)}}else ne=function(){_(T,0)};function ae(t,n){C=_(function(){t(e.unstable_now())},n)}e.unstable_IdlePriority=5,e.unstable_ImmediatePriority=1,e.unstable_LowPriority=4,e.unstable_NormalPriority=3,e.unstable_Profiling=null,e.unstable_UserBlockingPriority=2,e.unstable_cancelCallback=function(e){e.callback=null},e.unstable_forceFrameRate=function(e){0>e||125<e?console.error(`forceFrameRate takes a positive int between 0 and 125, forcing frame rates higher than 125 fps is not supported`):ee=0<e?Math.floor(1e3/e):5},e.unstable_getCurrentPriorityLevel=function(){return f},e.unstable_next=function(e){switch(f){case 1:case 2:case 3:var t=3;break;default:t=f}var n=f;f=t;try{return e()}finally{f=n}},e.unstable_requestPaint=function(){g=!0},e.unstable_runWithPriority=function(e,t){switch(e){case 1:case 2:case 3:case 4:case 5:break;default:e=3}var n=f;f=e;try{return t()}finally{f=n}},e.unstable_scheduleCallback=function(r,i,a){var o=e.unstable_now();switch(typeof a==`object`&&a?(a=a.delay,a=typeof a==`number`&&0<a?o+a:o):a=o,r){case 1:var s=-1;break;case 2:s=250;break;case 5:s=1073741823;break;case 4:s=1e4;break;default:s=5e3}return s=a+s,r={id:u++,callback:i,priorityLevel:r,startTime:a,expirationTime:s,sortIndex:-1},a>o?(r.sortIndex=a,t(l,r),n(c)===null&&r===n(l)&&(h?(v(C),C=-1):h=!0,ae(x,a-o))):(r.sortIndex=s,t(c,r),m||p||(m=!0,S||(S=!0,ne()))),r},e.unstable_shouldYield=te,e.unstable_wrapCallback=function(e){var t=f;return function(){var n=f;f=t;try{return e.apply(this,arguments)}finally{f=n}}}})),f=o(((e,t)=>{t.exports=d()})),p=o((e=>{var t=u();function n(e){var t=`https://react.dev/errors/`+e;if(1<arguments.length){t+=`?args[]=`+encodeURIComponent(arguments[1]);for(var n=2;n<arguments.length;n++)t+=`&args[]=`+encodeURIComponent(arguments[n])}return`Minified React error #`+e+`; visit `+t+` for the full message or use the non-minified dev environment for full errors and additional helpful warnings.`}function r(){}var i={d:{f:r,r:function(){throw Error(n(522))},D:r,C:r,L:r,m:r,X:r,S:r,M:r},p:0,findDOMNode:null},a=Symbol.for(`react.portal`),o=Symbol.for(`react.recoverable`),s=Symbol.for(`react.optimistic_key`);function c(e,t,n){var r=3<arguments.length&&arguments[3]!==void 0?arguments[3]:null;return{$$typeof:a,key:r==null?null:r===s?s:``+r,children:e,containerInfo:t,implementation:n}}var l=t.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE;function d(e,t){if(e===`font`)return``;if(typeof t==`string`)return t===`use-credentials`?t:``}e.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE=i,e.browser=function(e){return{$$typeof:o,_reason:e}},e.createPortal=function(e,t){var r=2<arguments.length&&arguments[2]!==void 0?arguments[2]:null;if(!t||t.nodeType!==1&&t.nodeType!==9&&t.nodeType!==11)throw Error(n(299));return c(e,t,null,r)},e.flushSync=function(e){var t=l.T,n=i.p;try{if(l.T=null,i.p=2,e)return e()}finally{l.T=t,i.p=n,i.d.f()}},e.preconnect=function(e,t){typeof e==`string`&&(t?(t=t.crossOrigin,t=typeof t==`string`?t===`use-credentials`?t:``:void 0):t=null,i.d.C(e,t))},e.prefetchDNS=function(e){typeof e==`string`&&i.d.D(e)},e.preinit=function(e,t){if(typeof e==`string`&&t&&typeof t.as==`string`){var n=t.as,r=d(n,t.crossOrigin),a=typeof t.integrity==`string`?t.integrity:void 0,o=typeof t.fetchPriority==`string`?t.fetchPriority:void 0;n===`style`?i.d.S(e,typeof t.precedence==`string`?t.precedence:void 0,{crossOrigin:r,integrity:a,fetchPriority:o}):n===`script`&&i.d.X(e,{crossOrigin:r,integrity:a,fetchPriority:o,nonce:typeof t.nonce==`string`?t.nonce:void 0})}},e.preinitModule=function(e,t){if(typeof e==`string`){if(typeof t==`object`&&t){if(t.as==null||t.as===`script`){var n=d(t.as,t.crossOrigin);i.d.M(e,{crossOrigin:n,integrity:typeof t.integrity==`string`?t.integrity:void 0,nonce:typeof t.nonce==`string`?t.nonce:void 0,fetchPriority:typeof t.fetchPriority==`string`?t.fetchPriority:void 0})}}else t??i.d.M(e)}},e.preload=function(e,t){if(typeof e==`string`&&typeof t==`object`&&t&&typeof t.as==`string`){var n=t.as,r=d(n,t.crossOrigin);i.d.L(e,n,{crossOrigin:r,integrity:typeof t.integrity==`string`?t.integrity:void 0,nonce:typeof t.nonce==`string`?t.nonce:void 0,type:typeof t.type==`string`?t.type:void 0,fetchPriority:typeof t.fetchPriority==`string`?t.fetchPriority:void 0,referrerPolicy:typeof t.referrerPolicy==`string`?t.referrerPolicy:void 0,imageSrcSet:typeof t.imageSrcSet==`string`?t.imageSrcSet:void 0,imageSizes:typeof t.imageSizes==`string`?t.imageSizes:void 0,media:typeof t.media==`string`?t.media:void 0})}},e.preloadModule=function(e,t){if(typeof e==`string`){if(t){var n=d(t.as,t.crossOrigin);i.d.m(e,{as:typeof t.as==`string`&&t.as!==`script`?t.as:void 0,crossOrigin:n,integrity:typeof t.integrity==`string`?t.integrity:void 0,nonce:typeof t.nonce==`string`?t.nonce:void 0,fetchPriority:typeof t.fetchPriority==`string`?t.fetchPriority:void 0})}else i.d.m(e)}},e.requestFormReset=function(e){i.d.r(e)},e.unstable_batchedUpdates=function(e,t){return e(t)},e.useFormState=function(e,t,n){return l.H.useFormState(e,t,n)},e.useFormStatus=function(){return l.H.useHostTransitionStatus()},e.version=`19.3.0`})),m=o(((e,t)=>{function n(){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<`u`&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE==`function`)try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(n)}catch(e){console.error(e)}}n(),t.exports=p()})),h=o((e=>{var t=f(),n=u(),r=m();function i(e){var t=`https://react.dev/errors/`+e;if(1<arguments.length){t+=`?args[]=`+encodeURIComponent(arguments[1]);for(var n=2;n<arguments.length;n++)t+=`&args[]=`+encodeURIComponent(arguments[n])}return`Minified React error #`+e+`; visit `+t+` for the full message or use the non-minified dev environment for full errors and additional helpful warnings.`}function a(e){return!(!e||e.nodeType!==1&&e.nodeType!==9&&e.nodeType!==11)}function o(e){for(var t=e,n=t;n&&!n.alternate;)t=n,t.flags&4098&&(e=t.return),n=t.return;for(;t.return;)t=t.return;return t.tag===3?e:null}function s(e){if(e.tag===13){var t=e.memoizedState;if(t===null&&(e=e.alternate,e!==null&&(t=e.memoizedState)),t!==null)return t.dehydrated}return null}function c(e){if(e.tag===31){var t=e.memoizedState;if(t===null&&(e=e.alternate,e!==null&&(t=e.memoizedState)),t!==null)return t.dehydrated}return null}function l(e){if(o(e)!==e)throw Error(i(188))}function d(e){var t=e.alternate;if(!t){if(t=o(e),t===null)throw Error(i(188));return t===e?e:null}for(var n=e,r=t;;){var a=n.return;if(a===null)break;var s=a.alternate;if(s===null){if(r=a.return,r!==null){n=r;continue}break}if(a.child===s.child){for(s=a.child;s;){if(s===n)return l(a),e;if(s===r)return l(a),t;s=s.sibling}throw Error(i(188))}if(n.return!==r.return)n=a,r=s;else{for(var c=!1,u=a.child;u;){if(u===n){c=!0,n=a,r=s;break}if(u===r){c=!0,r=a,n=s;break}u=u.sibling}if(!c){for(u=s.child;u;){if(u===n){c=!0,n=s,r=a;break}if(u===r){c=!0,r=s,n=a;break}u=u.sibling}if(!c)throw Error(i(189))}}if(n.alternate!==r)throw Error(i(190))}if(n.tag!==3)throw Error(i(188));return n.stateNode.current===n?e:t}function p(e){var t=e.tag;if(t===5||t===26||t===27||t===6)return e;for(e=e.child;e!==null;){if(t=p(e),t!==null)return t;e=e.sibling}return null}function h(e,t,n,r,i,a){for(;e!==null;){if((e.tag===5||e.tag===27||e.tag===6)&&n(e,r,i,a)||(e.tag!==22||e.memoizedState===null)&&(t||e.tag!==5&&e.tag!==27)&&h(e.child,t,n,r,i,a))return!0;e=e.sibling}return!1}function g(e){for(e=e.return;e!==null;){if(e.tag===3||e.tag===5||e.tag===27)return e;e=e.return}return null}function _(e){var t=!1;for(e=e.return;e!==null&&(e.tag===4&&(t=!0),e.tag!==3&&e.tag!==5&&e.tag!==27);)e=e.return;return t}function v(e){var t=[null,null],n=g(e);return n===null||y(t,e,n.child,{foundSelf:!1}),t}function y(e,t,n,r){for(;n!==null;){if(n===t)r.foundSelf=!0;else if(n.tag===5||n.tag===27||n.tag===6){if(r.foundSelf)return e[1]=n,!0;e[0]=n}else if((n.tag!==22||n.memoizedState===null)&&y(e,t,n.child,r))return!0;n=n.sibling}return!1}function b(e){switch(e.tag){case 5:case 27:case 6:return e.stateNode;case 3:return e.stateNode.containerInfo;default:throw Error(i(559))}}var x=null,S=null;function C(e,t,n){return e===n||e===t&&(x=e,!0)}function ee(e,t,n){return e===n?(S=e,!1):e===t&&(S!==null&&(x=e),!0)}function w(e){if(e===null)return null;do e=e===null?null:e.return;while(e&&e.tag!==5&&e.tag!==27&&e.tag!==3);return e||null}function te(e,t,n){for(var r=0,i=e;i;i=n(i))r++;i=0;for(var a=t;a;a=n(a))i++;for(;0<r-i;)e=n(e),r--;for(;0<i-r;)t=n(t),i--;for(;r--;){if(e===t||t!==null&&e===t.alternate)return e;e=n(e),t=n(t)}return null}var T=Object.assign,ne=Symbol.for(`react.element`),re=Symbol.for(`react.transitional.element`),ie=Symbol.for(`react.portal`),ae=Symbol.for(`react.fragment`),oe=Symbol.for(`react.strict_mode`),se=Symbol.for(`react.profiler`),ce=Symbol.for(`react.consumer`),le=Symbol.for(`react.context`),ue=Symbol.for(`react.forward_ref`),E=Symbol.for(`react.suspense`),de=Symbol.for(`react.suspense_list`),fe=Symbol.for(`react.memo`),pe=Symbol.for(`react.lazy`),me=Symbol.for(`react.activity`),he=Symbol.for(`react.legacy_hidden`),ge=Symbol.for(`react.memo_cache_sentinel`),_e=Symbol.for(`react.view_transition`),ve=Symbol.for(`react.recoverable`),ye=Symbol.iterator;function be(e){return typeof e!=`object`||!e?null:(e=ye&&e[ye]||e[`@@iterator`],typeof e==`function`?e:null)}var xe=Symbol.for(`react.client.reference`);function Se(e){if(e==null)return null;if(typeof e==`function`)return e.$$typeof===xe?null:e.displayName||e.name||null;if(typeof e==`string`)return e;switch(e){case ae:return`Fragment`;case se:return`Profiler`;case oe:return`StrictMode`;case E:return`Suspense`;case de:return`SuspenseList`;case me:return`Activity`;case _e:return`ViewTransition`}if(typeof e==`object`)switch(e.$$typeof){case ie:return`Portal`;case le:return e.displayName||`Context`;case ce:return(e._context.displayName||`Context`)+`.Consumer`;case ue:var t=e.render;return e=e.displayName,e||=(e=t.displayName||t.name||``,e===``?`ForwardRef`:`ForwardRef(`+e+`)`),e;case fe:return t=e.displayName||null,t===null?Se(e.type)||`Memo`:t;case pe:t=e._payload,e=e._init;try{return Se(e(t))}catch{}}return null}var Ce=Array.isArray,D=n.__CLIENT_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,O=r.__DOM_INTERNALS_DO_NOT_USE_OR_WARN_USERS_THEY_CANNOT_UPGRADE,we={pending:!1,data:null,method:null,action:null},Te=[],Ee=-1;function De(e){return{current:e}}function k(e){0>Ee||(e.current=Te[Ee],Te[Ee]=null,Ee--)}function A(e,t){Ee++,Te[Ee]=e.current,e.current=t}var Oe=De(null),ke=De(null),Ae=De(null),je=De(null);function Me(e,t){switch(A(Ae,t),A(ke,e),A(Oe,null),t.nodeType){case 9:case 11:e=(e=t.documentElement)&&(e=e.namespaceURI)?up(e):0;break;default:if(e=t.tagName,t=t.namespaceURI)t=up(t),e=dp(t,e);else switch(e){case`svg`:e=1;break;case`math`:e=2;break;default:e=0}}k(Oe),A(Oe,e)}function Ne(){k(Oe),k(ke),k(Ae)}function Pe(e){var t=e.memoizedState;t!==null&&(sh._currentValue=t.memoizedState,A(je,e)),t=Oe.current;var n=dp(t,e.type);t!==n&&(A(ke,e),A(Oe,n))}function Fe(e){ke.current===e&&(k(Oe),k(ke)),je.current===e&&(k(je),sh._currentValue=we)}var Ie,Le;function Re(e){if(Ie===void 0)try{throw Error()}catch(e){var t=e.stack.trim().match(/\n( *(at )?)/);Ie=t&&t[1]||``,Le=-1<e.stack.indexOf(`
    at`)?` (<anonymous>)`:-1<e.stack.indexOf(`@`)?`@unknown:0:0`:``}return`
`+Ie+e+Le}var ze=!1;function Be(e,t){if(!e||ze)return``;ze=!0;var n=Error.prepareStackTrace;Error.prepareStackTrace=void 0;try{var r={DetermineComponentFrameRoot:function(){try{if(t){var n=function(){throw Error()};if(Object.defineProperty(n.prototype,"props",{set:function(){throw Error()}}),typeof Reflect==`object`&&Reflect.construct){try{Reflect.construct(n,[])}catch(e){var r=e}Reflect.construct(e,[],n)}else{try{n.call()}catch(e){r=e}n=!1;try{var i=Object.getOwnPropertyDescriptor(e.prototype,`props`);Object.defineProperty(e.prototype,"props",{configurable:!0,set:function(){throw Error()}}),n=!0,new e}finally{n&&(i===void 0?delete e.prototype.props:Object.defineProperty(e.prototype,"props",i))}}}else{try{throw Error()}catch(e){r=e}(n=e())&&typeof n.catch==`function`&&n.catch(function(){})}}catch(e){if(e&&r&&typeof e.stack==`string`)return[e.stack,r.stack]}return[null,null]}};r.DetermineComponentFrameRoot.displayName=`DetermineComponentFrameRoot`;var i=Object.getOwnPropertyDescriptor(r.DetermineComponentFrameRoot,`name`);i&&i.configurable&&Object.defineProperty(r.DetermineComponentFrameRoot,"name",{value:`DetermineComponentFrameRoot`});var a=r.DetermineComponentFrameRoot(),o=a[0],s=a[1];if(o&&s){var c=o.split(`
`),l=s.split(`
`);for(i=r=0;r<c.length&&!c[r].includes(`DetermineComponentFrameRoot`);)r++;for(;i<l.length&&!l[i].includes(`DetermineComponentFrameRoot`);)i++;if(r===c.length||i===l.length)for(r=c.length-1,i=l.length-1;1<=r&&0<=i&&c[r]!==l[i];)i--;for(;1<=r&&0<=i;r--,i--)if(c[r]!==l[i]){if(r!==1||i!==1)do if(r--,i--,0>i||c[r]!==l[i]){var u=`
`+c[r].replace(` at new `,` at `);return e.displayName&&u.includes(`<anonymous>`)&&(u=u.replace(`<anonymous>`,e.displayName)),u}while(1<=r&&0<=i);break}}}finally{ze=!1,Error.prepareStackTrace=n}return(n=e?e.displayName||e.name:``)?Re(n):``}function Ve(e,t){switch(e.tag){case 26:case 27:case 5:return Re(e.type);case 16:return Re(`Lazy`);case 13:return e.child!==t&&t!==null?Re(`Suspense Fallback`):Re(`Suspense`);case 19:return Re(`SuspenseList`);case 0:case 15:return Be(e.type,!1);case 11:return Be(e.type.render,!1);case 1:return Be(e.type,!0);case 31:return Re(`Activity`);case 30:return Re(`ViewTransition`);default:return``}}function He(e){try{var t=``,n=null;do t+=Ve(e,n),n=e,e=e.return;while(e);return t}catch(e){return`
Error generating stack: `+e.message+`
`+e.stack}}var Ue=Object.prototype.hasOwnProperty,We=t.unstable_scheduleCallback,Ge=t.unstable_cancelCallback,Ke=t.unstable_shouldYield,qe=t.unstable_requestPaint,Je=t.unstable_now,Ye=t.unstable_getCurrentPriorityLevel,Xe=t.unstable_ImmediatePriority,Ze=t.unstable_UserBlockingPriority,Qe=t.unstable_NormalPriority,$e=t.unstable_LowPriority,et=t.unstable_IdlePriority,tt=t.log,nt=t.unstable_setDisableYieldValue,rt=null,it=null;function at(e){if(typeof tt==`function`&&nt(e),it&&typeof it.setStrictMode==`function`)try{it.setStrictMode(rt,e)}catch{}}var ot=Math.clz32?Math.clz32:lt,st=Math.log,ct=Math.LN2;function lt(e){return e>>>=0,e===0?32:31-(st(e)/ct|0)|0}var ut=256,dt=262144,ft=4194304;function pt(e){var t=e&42;if(t!==0)return t;switch(e&-e){case 1:return 1;case 2:return 2;case 4:return 4;case 8:return 8;case 16:return 16;case 32:return 32;case 64:return 64;case 128:return 128;case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:return e&-e;case 262144:case 524288:case 1048576:case 2097152:return e&3932160;case 4194304:case 8388608:case 16777216:case 33554432:return e&62914560;case 67108864:return 67108864;case 134217728:return 134217728;case 268435456:return 268435456;case 536870912:return 536870912;case 1073741824:return 0;default:return e}}function mt(e,t,n){var r=e.pendingLanes;if(r===0)return 0;var i=0,a=e.suspendedLanes,o=e.pingedLanes;e=e.warmLanes;var s=r&134217727;return s===0?(s=r&~a,s===0?o===0?n||(n=r&~e,n!==0&&(i=pt(n))):i=pt(o):i=pt(s)):(r=s&~a,r===0?(o&=s,o===0?n||(n=s&~e,n!==0&&(i=pt(n))):i=pt(o)):i=pt(r)),i===0?0:t!==0&&t!==i&&(t&a)===0&&(a=i&-i,n=t&-t,a>=n||a===32&&n&4194048)?t:i}function ht(e,t){return(e.pendingLanes&~(e.suspendedLanes&~e.pingedLanes)&t)===0}function gt(e,t){t&8&&(t|=t&32);var n=e.entangledLanes;if(n!==0)for(e=e.entanglements,n&=t;0<n;){var r=31-ot(n),i=1<<r;t|=e[r],n&=~i}return t}function _t(e,t){switch(e){case 1:case 2:case 4:case 8:case 64:return t+250;case 16:case 32:case 128:case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:return t+5e3;case 4194304:case 8388608:case 16777216:case 33554432:return-1;case 67108864:case 134217728:case 268435456:case 536870912:case 1073741824:return-1;default:return-1}}function vt(){var e=ft;return ft<<=1,!(ft&62914560)&&(ft=4194304),e}function yt(e){for(var t=[],n=0;31>n;n++)t.push(e);return t}function bt(e,t){e.pendingLanes|=t,t!==268435456&&(e.suspendedLanes=0,e.pingedLanes=0,e.warmLanes=0)}function xt(e,t,n,r,i,a){var o=e.pendingLanes;e.pendingLanes=n,e.suspendedLanes=0,e.pingedLanes=0,e.warmLanes=0,e.expiredLanes&=n,e.entangledLanes&=n,e.errorRecoveryDisabledLanes&=n,e.shellSuspendCounter=0;var s=e.entanglements,c=e.expirationTimes,l=e.hiddenUpdates;for(n=o&~n;0<n;){var u=31-ot(n),d=1<<u;s[u]=0,c[u]=-1;var f=l[u];if(f!==null)for(l[u]=null,u=0;u<f.length;u++){var p=f[u];p!==null&&(p.lane&=-536870913)}n&=~d}r!==0&&St(e,r,0),a!==0&&i===0&&e.tag!==0&&(e.suspendedLanes|=a&~(o&~t))}function St(e,t,n){e.pendingLanes|=t,e.suspendedLanes&=~t;var r=31-ot(t);e.entangledLanes|=t,e.entanglements[r]=e.entanglements[r]|1073741824|n&261930}function Ct(e,t){var n=e.entangledLanes|=t;for(e=e.entanglements;n;){var r=31-ot(n),i=1<<r;i&t|e[r]&t&&(e[r]|=t),n&=~i}}function wt(e,t){var n=t&-t;return n=n&42?1:Tt(n),(n&(e.suspendedLanes|t))===0?n:0}function Tt(e){switch(e){case 2:e=1;break;case 8:e=4;break;case 32:e=16;break;case 256:case 512:case 1024:case 2048:case 4096:case 8192:case 16384:case 32768:case 65536:case 131072:case 262144:case 524288:case 1048576:case 2097152:case 4194304:case 8388608:case 16777216:case 33554432:e=128;break;case 268435456:e=134217728;break;default:e=0}return e}function Et(e){return e&=-e,2<e?8<e?e&134217727?32:268435456:8:2}function Dt(){var e=O.p;return e===0?(e=window.event,e===void 0?32:Ch(e.type)):e}function Ot(e,t){var n=O.p;try{return O.p=e,t()}finally{O.p=n}}var kt=Math.random().toString(36).slice(2),At=`__reactFiber$`+kt,jt=`__reactProps$`+kt,Mt=`__reactContainer$`+kt,Nt=`__reactEvents$`+kt,Pt=`__reactListeners$`+kt,Ft=`__reactHandles$`+kt,It=`__reactResources$`+kt,Lt=`__reactMarker$`+kt,Rt=`__reactLoad$`+kt;function zt(e){delete e[At],delete e[jt],delete e[Pt],delete e[Ft]}function Bt(e){var t;if(t=e[At])return t;for(var n=e.parentNode;n;){if(t=n[Mt]||n[At]){if(n=t.alternate,t.child!==null||n!==null&&n.child!==null)for(e=fm(e);e!==null;){if(n=e[At])return n;e=fm(e)}return t}e=n,n=e.parentNode}return null}function Vt(e){if(e=e[At]||e[Mt]){var t=e.tag;if(t===5||t===6||t===13||t===31||t===26||t===27||t===3)return e}return null}function Ht(e){var t=e.tag;if(t===5||t===26||t===27||t===6)return e.stateNode;throw Error(i(33))}function Ut(e){var t=e[It];return t||=e[It]={hoistableStyles:new Map,hoistableScripts:new Map},t}function Wt(e){e[Lt]=!0}function Gt(e){e[Rt]=void 0}var Kt=new Set,qt={};function Jt(e,t){Yt(e,t),Yt(e+`Capture`,t)}function Yt(e,t){for(qt[e]=t,e=0;e<t.length;e++)Kt.add(t[e])}var Xt=RegExp(`^[:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD][:A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD\\-.0-9\\u00B7\\u0300-\\u036F\\u203F-\\u2040]*$`),Zt={},Qt={};function $t(e){return Ue.call(Qt,e)?!0:Ue.call(Zt,e)?!1:Xt.test(e)?Qt[e]=!0:(Zt[e]=!0,!1)}var j=!1;function en(){var e=j;return j=!1,e}function tn(e,t,n){if($t(t)){if(n===null)e.removeAttribute(t);else{switch(typeof n){case`undefined`:case`function`:case`symbol`:e.removeAttribute(t);return;case`boolean`:var r=t.toLowerCase().slice(0,5);if(r!==`data-`&&r!==`aria-`){e.removeAttribute(t);return}}e.setAttribute(t,n)}}}function nn(e,t,n){if(n===null)e.removeAttribute(t);else{switch(typeof n){case`undefined`:case`function`:case`symbol`:case`boolean`:e.removeAttribute(t);return}e.setAttribute(t,n)}}function rn(e,t,n,r){if(r===null)e.removeAttribute(n);else{switch(typeof r){case`undefined`:case`function`:case`symbol`:case`boolean`:e.removeAttribute(n);return}e.setAttributeNS(t,n,r)}}function an(e){switch(typeof e){case`bigint`:case`boolean`:case`number`:case`string`:case`undefined`:return e;case`object`:return e;default:return``}}function on(e){var t=e.type;return(e=e.nodeName)&&e.toLowerCase()===`input`&&(t===`checkbox`||t===`radio`)}function sn(e,t,n){var r=Object.getOwnPropertyDescriptor(e.constructor.prototype,t);if(!e.hasOwnProperty(t)&&r!==void 0&&typeof r.get==`function`&&typeof r.set==`function`){var i=r.get,a=r.set;return Object.defineProperty(e,t,{configurable:!0,get:function(){return i.call(this)},set:function(e){n=``+e,a.call(this,e)}}),Object.defineProperty(e,t,{enumerable:r.enumerable}),{getValue:function(){return n},setValue:function(e){n=``+e},stopTracking:function(){e._valueTracker=null,delete e[t]}}}}function cn(e){if(!e._valueTracker){var t=on(e)?`checked`:`value`;e._valueTracker=sn(e,t,``+e[t])}}function ln(e){if(!e)return!1;var t=e._valueTracker;if(!t)return!0;var n=t.getValue(),r=``;return e&&(r=on(e)?e.checked?`true`:`false`:e.value),e=r,e!==n&&(t.setValue(e),!0)}var un=/[\n"\\]/g;function dn(e){return e.replace(un,function(e){return`\\`+e.charCodeAt(0).toString(16)+` `})}function fn(e,t,n,r,i,a,o,s){e.name=``,o!=null&&typeof o!=`function`&&typeof o!=`symbol`&&typeof o!=`boolean`?e.type=o:e.removeAttribute(`type`),t==null?o!==`submit`&&o!==`reset`||e.removeAttribute(`value`):o===`number`?(t===0&&e.value===``||e.value!=t)&&(e.value=``+an(t)):e.value!==``+an(t)&&(e.value=``+an(t)),t==null?n==null?r!=null&&e.removeAttribute(`value`):mn(e,an(n)):o===`number`&&e.value==t?mn(e,an(e.value)):mn(e,an(t)),i==null&&a!=null&&(e.defaultChecked=!!a),i!=null&&(e.checked=i&&typeof i!=`function`&&typeof i!=`symbol`),s!=null&&typeof s!=`function`&&typeof s!=`symbol`&&typeof s!=`boolean`?e.name=``+an(s):e.removeAttribute(`name`)}function pn(e,t,n,r,i,a,o,s){if(a!=null&&typeof a!=`function`&&typeof a!=`symbol`&&typeof a!=`boolean`&&(e.type=a),t!=null||n!=null){if(!(a!==`submit`&&a!==`reset`||t!=null)){cn(e);return}n=n==null?``:``+an(n),t=t==null?n:``+an(t),s||t===e.value||(e.value=t),e.defaultValue=t}r??=i,r=typeof r!=`function`&&typeof r!=`symbol`&&!!r,e.checked=s?e.checked:!!r,e.defaultChecked=!!r,o!=null&&typeof o!=`function`&&typeof o!=`symbol`&&typeof o!=`boolean`&&(e.name=o),cn(e)}function mn(e,t){e.defaultValue!==``+t&&(e.defaultValue=``+t)}function hn(e,t,n,r){if(e=e.options,t){t={};for(var i=0;i<n.length;i++)t[`$`+n[i]]=!0;for(n=0;n<e.length;n++)i=t.hasOwnProperty(`$`+e[n].value),e[n].selected!==i&&(e[n].selected=i),i&&r&&(e[n].defaultSelected=!0)}else{for(n=``+an(n),t=null,i=0;i<e.length;i++){if(e[i].value===n){e[i].selected=!0,r&&(e[i].defaultSelected=!0);return}t!==null||e[i].disabled||(t=e[i])}t!==null&&(t.selected=!0)}}function gn(e,t,n){t!=null&&(t=``+an(t),t!==e.value&&(e.value=t),n==null)?e.defaultValue!==t&&(e.defaultValue=t):e.defaultValue=n==null?``:``+an(n)}function _n(e,t,n,r){if(t==null){if(r!=null){if(n!=null)throw Error(i(92));if(Ce(r)){if(1<r.length)throw Error(i(93));r=r[0]}n=r}n??=``,t=n}n=an(t),e.defaultValue=n,r=e.textContent,r===n&&r!==``&&r!==null&&(e.value=r),cn(e)}function vn(e,t){if(t){var n=e.firstChild;if(n&&n===e.lastChild&&n.nodeType===3){n.nodeValue=t;return}}e.textContent=t}var yn=new Set(`animationIterationCount aspectRatio borderImageOutset borderImageSlice borderImageWidth boxFlex boxFlexGroup boxOrdinalGroup columnCount columns flex flexGrow flexPositive flexShrink flexNegative flexOrder gridArea gridRow gridRowEnd gridRowSpan gridRowStart gridColumn gridColumnEnd gridColumnSpan gridColumnStart fontWeight lineClamp lineHeight opacity order orphans scale tabSize widows zIndex zoom fillOpacity floodOpacity stopOpacity strokeDasharray strokeDashoffset strokeMiterlimit strokeOpacity strokeWidth MozAnimationIterationCount MozBoxFlex MozBoxFlexGroup MozLineClamp msAnimationIterationCount msFlex msZoom msFlexGrow msFlexNegative msFlexOrder msFlexPositive msFlexShrink msGridColumn msGridColumnSpan msGridRow msGridRowSpan WebkitAnimationIterationCount WebkitBoxFlex WebKitBoxFlexGroup WebkitBoxOrdinalGroup WebkitColumnCount WebkitColumns WebkitFlex WebkitFlexGrow WebkitFlexPositive WebkitFlexShrink WebkitLineClamp`.split(` `));function bn(e,t,n){var r=t.indexOf(`--`)===0;n==null||typeof n==`boolean`||n===``?r?e.setProperty(t,``):t===`float`?e.cssFloat=``:e[t]=``:r?e.setProperty(t,n):typeof n!=`number`||n===0||yn.has(t)?t===`float`?e.cssFloat=n:e[t]=(``+n).trim():e[t]=n+`px`}function xn(e,t,n){if(t!=null&&typeof t!=`object`)throw Error(i(62));if(e=e.style,n!=null){for(var r in n)!n.hasOwnProperty(r)||t!=null&&t.hasOwnProperty(r)||(r.indexOf(`--`)===0?e.setProperty(r,``):r===`float`?e.cssFloat=``:e[r]=``,j=!0);for(var a in t)r=t[a],t.hasOwnProperty(a)&&n[a]!==r&&(bn(e,a,r),j=!0)}else for(var o in t)t.hasOwnProperty(o)&&bn(e,o,t[o])}function Sn(e){if(e.indexOf(`-`)===-1)return!1;switch(e){case`annotation-xml`:case`color-profile`:case`font-face`:case`font-face-src`:case`font-face-uri`:case`font-face-format`:case`font-face-name`:case`missing-glyph`:return!1;default:return!0}}var Cn=new Map([[`acceptCharset`,`accept-charset`],[`htmlFor`,`for`],[`httpEquiv`,`http-equiv`],[`crossOrigin`,`crossorigin`],[`accentHeight`,`accent-height`],[`alignmentBaseline`,`alignment-baseline`],[`arabicForm`,`arabic-form`],[`baselineShift`,`baseline-shift`],[`capHeight`,`cap-height`],[`clipPath`,`clip-path`],[`clipRule`,`clip-rule`],[`colorInterpolation`,`color-interpolation`],[`colorInterpolationFilters`,`color-interpolation-filters`],[`colorProfile`,`color-profile`],[`colorRendering`,`color-rendering`],[`dominantBaseline`,`dominant-baseline`],[`enableBackground`,`enable-background`],[`fillOpacity`,`fill-opacity`],[`fillRule`,`fill-rule`],[`floodColor`,`flood-color`],[`floodOpacity`,`flood-opacity`],[`fontFamily`,`font-family`],[`fontSize`,`font-size`],[`fontSizeAdjust`,`font-size-adjust`],[`fontStretch`,`font-stretch`],[`fontStyle`,`font-style`],[`fontVariant`,`font-variant`],[`fontWeight`,`font-weight`],[`glyphName`,`glyph-name`],[`glyphOrientationHorizontal`,`glyph-orientation-horizontal`],[`glyphOrientationVertical`,`glyph-orientation-vertical`],[`horizAdvX`,`horiz-adv-x`],[`horizOriginX`,`horiz-origin-x`],[`imageRendering`,`image-rendering`],[`letterSpacing`,`letter-spacing`],[`lightingColor`,`lighting-color`],[`markerEnd`,`marker-end`],[`markerMid`,`marker-mid`],[`markerStart`,`marker-start`],[`maskType`,`mask-type`],[`overlinePosition`,`overline-position`],[`overlineThickness`,`overline-thickness`],[`paintOrder`,`paint-order`],[`panose-1`,`panose-1`],[`pointerEvents`,`pointer-events`],[`renderingIntent`,`rendering-intent`],[`shapeRendering`,`shape-rendering`],[`stopColor`,`stop-color`],[`stopOpacity`,`stop-opacity`],[`strikethroughPosition`,`strikethrough-position`],[`strikethroughThickness`,`strikethrough-thickness`],[`strokeDasharray`,`stroke-dasharray`],[`strokeDashoffset`,`stroke-dashoffset`],[`strokeLinecap`,`stroke-linecap`],[`strokeLinejoin`,`stroke-linejoin`],[`strokeMiterlimit`,`stroke-miterlimit`],[`strokeOpacity`,`stroke-opacity`],[`strokeWidth`,`stroke-width`],[`textAnchor`,`text-anchor`],[`textDecoration`,`text-decoration`],[`textRendering`,`text-rendering`],[`transformOrigin`,`transform-origin`],[`underlinePosition`,`underline-position`],[`underlineThickness`,`underline-thickness`],[`unicodeBidi`,`unicode-bidi`],[`unicodeRange`,`unicode-range`],[`unitsPerEm`,`units-per-em`],[`vAlphabetic`,`v-alphabetic`],[`vHanging`,`v-hanging`],[`vIdeographic`,`v-ideographic`],[`vMathematical`,`v-mathematical`],[`vectorEffect`,`vector-effect`],[`vertAdvY`,`vert-adv-y`],[`vertOriginX`,`vert-origin-x`],[`vertOriginY`,`vert-origin-y`],[`wordSpacing`,`word-spacing`],[`writingMode`,`writing-mode`],[`xmlnsXlink`,`xmlns:xlink`],[`xHeight`,`x-height`]]),wn=/^[\u0000-\u001F ]*j[\r\n\t]*a[\r\n\t]*v[\r\n\t]*a[\r\n\t]*s[\r\n\t]*c[\r\n\t]*r[\r\n\t]*i[\r\n\t]*p[\r\n\t]*t[\r\n\t]*:/i;function Tn(e){return wn.test(``+e)?`javascript:throw new Error('React has blocked a javascript: URL as a security precaution.')`:e}function En(){}var Dn=null;function On(e){return e=e.target||e.srcElement||window,e.correspondingUseElement&&(e=e.correspondingUseElement),e.nodeType===3?e.parentNode:e}var kn=null,An=null;function jn(e){var t=Vt(e);if(t&&(e=t.stateNode)){var n=e[jt]||null;a:switch(e=t.stateNode,t.type){case`input`:if(fn(e,n.value,n.defaultValue,n.defaultValue,n.checked,n.defaultChecked,n.type,n.name),t=n.name,n.type===`radio`&&t!=null){for(n=e;n.parentNode;)n=n.parentNode;for(n=n.querySelectorAll(`input[name="`+dn(``+t)+`"][type="radio"]`),t=0;t<n.length;t++){var r=n[t];if(r!==e&&r.form===e.form){var a=r[jt]||null;if(!a)throw Error(i(90));fn(r,a.value,a.defaultValue,a.defaultValue,a.checked,a.defaultChecked,a.type,a.name)}}for(t=0;t<n.length;t++)r=n[t],r.form===e.form&&ln(r)}break a;case`textarea`:gn(e,n.value,n.defaultValue);break a;case`select`:t=n.value,t!=null&&hn(e,!!n.multiple,t,!1)}}}var Mn=!1;function Nn(e,t,n){if(Mn)return e(t,n);Mn=!0;try{return e(t)}finally{if(Mn=!1,(kn!==null||An!==null)&&(zd(),kn&&(t=kn,e=An,An=kn=null,jn(t),e)))for(t=0;t<e.length;t++)jn(e[t])}}function Pn(e,t){var n=e.stateNode;if(n===null)return null;var r=n[jt]||null;if(r===null)return null;n=r[t];a:switch(t){case`onClick`:case`onClickCapture`:case`onDoubleClick`:case`onDoubleClickCapture`:case`onMouseDown`:case`onMouseDownCapture`:case`onMouseMove`:case`onMouseMoveCapture`:case`onMouseUp`:case`onMouseUpCapture`:case`onMouseEnter`:(r=!r.disabled)||(e=e.type,r=e!==`button`&&e!==`input`&&e!==`select`&&e!==`textarea`),e=!r;break a;default:e=!1}if(e)return null;if(n&&typeof n!=`function`)throw Error(i(231,t,typeof n));return n}var Fn=typeof window<`u`&&window.document!==void 0&&window.document.createElement!==void 0,In=!1;if(Fn)try{var Ln={};Object.defineProperty(Ln,"passive",{get:function(){In=!0}}),window.addEventListener(`test`,Ln,Ln),window.removeEventListener(`test`,Ln,Ln)}catch{In=!1}var Rn=null,M=null,N=null;function zn(){if(N)return N;var e,t=M,n=t.length,r,i=`value`in Rn?Rn.value:Rn.textContent,a=i.length;for(e=0;e<n&&t[e]===i[e];e++);var o=n-e;for(r=1;r<=o&&t[n-r]===i[a-r];r++);return N=i.slice(e,1<r?1-r:void 0)}function Bn(e){var t=e.keyCode;return`charCode`in e?(e=e.charCode,e===0&&t===13&&(e=13)):e=t,e===10&&(e=13),32<=e||e===13?e:0}function Vn(){return!0}function Hn(){return!1}function Un(e){function t(t,n,r,i,a){for(var o in this._reactName=t,this._targetInst=r,this.type=n,this.nativeEvent=i,this.target=a,this.currentTarget=null,e)e.hasOwnProperty(o)&&(t=e[o],this[o]=t?t(i):i[o]);return this.isDefaultPrevented=(i.defaultPrevented==null?!1===i.returnValue:i.defaultPrevented)?Vn:Hn,this.isPropagationStopped=Hn,this}return T(t.prototype,{preventDefault:function(){this.defaultPrevented=!0;var e=this.nativeEvent;e&&(e.preventDefault?e.preventDefault():typeof e.returnValue!=`unknown`&&(e.returnValue=!1),this.isDefaultPrevented=Vn)},stopPropagation:function(){var e=this.nativeEvent;e&&(e.stopPropagation?e.stopPropagation():typeof e.cancelBubble!=`unknown`&&(e.cancelBubble=!0),this.isPropagationStopped=Vn)},persist:function(){},isPersistent:Vn}),t}var P={eventPhase:0,bubbles:0,cancelable:0,timeStamp:function(e){return e.timeStamp||Date.now()},defaultPrevented:0,isTrusted:0},Wn=Un(P),Gn=T({},P,{view:0,detail:0}),Kn=Un(Gn),qn,Jn,Yn,Xn=T({},Gn,{screenX:0,screenY:0,clientX:0,clientY:0,pageX:0,pageY:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,getModifierState:sr,button:0,buttons:0,relatedTarget:function(e){return e.relatedTarget===void 0?e.fromElement===e.srcElement?e.toElement:e.fromElement:e.relatedTarget},movementX:function(e){return`movementX`in e?e.movementX:(e!==Yn&&(Yn&&e.type===`mousemove`?(qn=e.screenX-Yn.screenX,Jn=e.screenY-Yn.screenY):Jn=qn=0,Yn=e),qn)},movementY:function(e){return`movementY`in e?e.movementY:Jn}}),Zn=Un(Xn),Qn=Un(T({},Xn,{dataTransfer:0})),$n=Un(T({},Gn,{relatedTarget:0})),er=Un(T({},P,{animationName:0,elapsedTime:0,pseudoElement:0})),tr=Un(T({},P,{clipboardData:function(e){return`clipboardData`in e?e.clipboardData:window.clipboardData}})),nr=Un(T({},P,{data:0})),rr={Esc:`Escape`,Spacebar:` `,Left:`ArrowLeft`,Up:`ArrowUp`,Right:`ArrowRight`,Down:`ArrowDown`,Del:`Delete`,Win:`OS`,Menu:`ContextMenu`,Apps:`ContextMenu`,Scroll:`ScrollLock`,MozPrintableKey:`Unidentified`},ir={8:`Backspace`,9:`Tab`,12:`Clear`,13:`Enter`,16:`Shift`,17:`Control`,18:`Alt`,19:`Pause`,20:`CapsLock`,27:`Escape`,32:` `,33:`PageUp`,34:`PageDown`,35:`End`,36:`Home`,37:`ArrowLeft`,38:`ArrowUp`,39:`ArrowRight`,40:`ArrowDown`,45:`Insert`,46:`Delete`,112:`F1`,113:`F2`,114:`F3`,115:`F4`,116:`F5`,117:`F6`,118:`F7`,119:`F8`,120:`F9`,121:`F10`,122:`F11`,123:`F12`,144:`NumLock`,145:`ScrollLock`,224:`Meta`},ar={Alt:`altKey`,Control:`ctrlKey`,Meta:`metaKey`,Shift:`shiftKey`};function or(e){var t=this.nativeEvent;return t.getModifierState?t.getModifierState(e):(e=ar[e])?!!t[e]:!1}function sr(){return or}var cr=Un(T({},Gn,{key:function(e){if(e.key){var t=rr[e.key]||e.key;if(t!==`Unidentified`)return t}return e.type===`keypress`?(e=Bn(e),e===13?`Enter`:String.fromCharCode(e)):e.type===`keydown`||e.type===`keyup`?ir[e.keyCode]||`Unidentified`:``},code:0,location:0,ctrlKey:0,shiftKey:0,altKey:0,metaKey:0,repeat:0,locale:0,getModifierState:sr,charCode:function(e){return e.type===`keypress`?Bn(e):0},keyCode:function(e){return e.type===`keydown`||e.type===`keyup`?e.keyCode:0},which:function(e){return e.type===`keypress`?Bn(e):e.type===`keydown`||e.type===`keyup`?e.keyCode:0}})),lr=Un(T({},Xn,{pointerId:0,width:0,height:0,pressure:0,tangentialPressure:0,tiltX:0,tiltY:0,twist:0,pointerType:0,isPrimary:0})),ur=Un(T({},P,{submitter:0})),dr=Un(T({},Gn,{touches:0,targetTouches:0,changedTouches:0,altKey:0,metaKey:0,ctrlKey:0,shiftKey:0,getModifierState:sr})),fr=Un(T({},P,{propertyName:0,elapsedTime:0,pseudoElement:0})),pr=Un(T({},Xn,{deltaX:function(e){return`deltaX`in e?e.deltaX:`wheelDeltaX`in e?-e.wheelDeltaX:0},deltaY:function(e){return`deltaY`in e?e.deltaY:`wheelDeltaY`in e?-e.wheelDeltaY:`wheelDelta`in e?-e.wheelDelta:0},deltaZ:0,deltaMode:0})),mr=Un(T({},P,{newState:0,oldState:0,source:0})),hr=[9,13,27,32],gr=Fn&&`CompositionEvent`in window,_r=null;Fn&&`documentMode`in document&&(_r=document.documentMode);var vr=Fn&&`TextEvent`in window&&!_r,yr=Fn&&(!gr||_r&&8<_r&&11>=_r),br=` `,xr=!1;function Sr(e,t){switch(e){case`keyup`:return hr.indexOf(t.keyCode)!==-1;case`keydown`:return t.keyCode!==229;case`keypress`:case`mousedown`:case`focusout`:return!0;default:return!1}}function Cr(e){return e=e.detail,typeof e==`object`&&`data`in e?e.data:null}var wr=!1;function Tr(e,t){switch(e){case`compositionend`:return Cr(t);case`keypress`:return t.which===32?(xr=!0,br):null;case`textInput`:return e=t.data,e===br&&xr?null:e;default:return null}}function Er(e,t){if(wr)return e===`compositionend`||!gr&&Sr(e,t)?(e=zn(),N=M=Rn=null,wr=!1,e):null;switch(e){case`paste`:return null;case`keypress`:if(!(t.ctrlKey||t.altKey||t.metaKey)||t.ctrlKey&&t.altKey){if(t.char&&1<t.char.length)return t.char;if(t.which)return String.fromCharCode(t.which)}return null;case`compositionend`:return yr&&t.locale!==`ko`?null:t.data;default:return null}}var Dr={color:!0,date:!0,datetime:!0,"datetime-local":!0,email:!0,month:!0,number:!0,password:!0,range:!0,search:!0,tel:!0,text:!0,time:!0,url:!0,week:!0};function Or(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t===`input`?!!Dr[e.type]:t===`textarea`}function kr(e,t,n,r){kn?An?An.push(r):An=[r]:kn=r,t=Jf(t,`onChange`),0<t.length&&(n=new Wn(`onChange`,`change`,null,n,r),e.push({event:n,listeners:t}))}var Ar=null,jr=null;function Mr(e){Vf(e,0)}function Nr(e){if(ln(Ht(e)))return e}function Pr(e,t){if(e===`change`)return t}var Fr=!1;if(Fn){var Ir;if(Fn){var Lr=`oninput`in document;if(!Lr){var Rr=document.createElement(`div`);Rr.setAttribute(`oninput`,`return;`),Lr=typeof Rr.oninput==`function`}Ir=Lr}else Ir=!1;Fr=Ir&&(!document.documentMode||9<document.documentMode)}function zr(){Ar&&(Ar.detachEvent(`onpropertychange`,Br),jr=Ar=null)}function Br(e){if(e.propertyName===`value`&&Nr(jr)){var t=[];kr(t,jr,e,On(e)),Nn(Mr,t)}}function Vr(e,t,n){e===`focusin`?(zr(),Ar=t,jr=n,Ar.attachEvent(`onpropertychange`,Br)):e===`focusout`&&zr()}function Hr(e){if(e===`selectionchange`||e===`keyup`||e===`keydown`)return Nr(jr)}function Ur(e,t){if(e===`click`)return Nr(t)}function Wr(e,t){if(e===`input`||e===`change`)return Nr(t)}function Gr(e,t){return e===t&&(e!==0||1/e==1/t)||e!==e&&t!==t}var Kr=typeof Object.is==`function`?Object.is:Gr;function qr(e,t){if(Kr(e,t))return!0;if(typeof e!=`object`||!e||typeof t!=`object`||!t)return!1;var n=Object.keys(e),r=Object.keys(t);if(n.length!==r.length)return!1;for(r=0;r<n.length;r++){var i=n[r];if(!Ue.call(t,i)||!Kr(e[i],t[i]))return!1}return!0}function Jr(e){if(e||=typeof document<`u`?document:void 0,e===void 0)return null;try{return e.activeElement||e.body}catch{return e.body}}function Yr(e){for(;e&&e.firstChild;)e=e.firstChild;return e}function Xr(e,t){var n=Yr(e);e=0;for(var r;n;){if(n.nodeType===3){if(r=e+n.textContent.length,e<=t&&r>=t)return{node:n,offset:t-e};e=r}a:{for(;n;){if(n.nextSibling){n=n.nextSibling;break a}n=n.parentNode}n=void 0}n=Yr(n)}}function Zr(e,t){return e&&t?e===t?!0:e&&e.nodeType===3?!1:t&&t.nodeType===3?Zr(e,t.parentNode):`contains`in e?e.contains(t):e.compareDocumentPosition?!!(e.compareDocumentPosition(t)&16):!1:!1}function Qr(e){e=e!=null&&e.ownerDocument!=null&&e.ownerDocument.defaultView!=null?e.ownerDocument.defaultView:window;for(var t=Jr(e.document);t instanceof e.HTMLIFrameElement;){try{var n=typeof t.contentWindow.location.href==`string`}catch{n=!1}if(n)e=t.contentWindow;else break;t=Jr(e.document)}return t}function $r(e){var t=e&&e.nodeName&&e.nodeName.toLowerCase();return t&&(t===`input`&&(e.type===`text`||e.type===`search`||e.type===`tel`||e.type===`url`||e.type===`password`)||t===`textarea`||e.contentEditable===`true`)}var ei=Fn&&`documentMode`in document&&11>=document.documentMode,ti=null,ni=null,ri=null,ii=!1;function ai(e,t,n){var r=n.window===n?n.document:n.nodeType===9?n:n.ownerDocument;ii||ti==null||ti!==Jr(r)||(r=ti,`selectionStart`in r&&$r(r)?r={start:r.selectionStart,end:r.selectionEnd}:(r=(r.ownerDocument&&r.ownerDocument.defaultView||window).getSelection(),r={anchorNode:r.anchorNode,anchorOffset:r.anchorOffset,focusNode:r.focusNode,focusOffset:r.focusOffset}),ri&&qr(ri,r)||(ri=r,r=Jf(ni,`onSelect`),0<r.length&&(t=new Wn(`onSelect`,`select`,null,t,n),e.push({event:t,listeners:r}),t.target=ti)))}function oi(e,t){var n={};return n[e.toLowerCase()]=t.toLowerCase(),n[`Webkit`+e]=`webkit`+t,n[`Moz`+e]=`moz`+t,n}var si={animationend:oi(`Animation`,`AnimationEnd`),animationiteration:oi(`Animation`,`AnimationIteration`),animationstart:oi(`Animation`,`AnimationStart`),transitionrun:oi(`Transition`,`TransitionRun`),transitionstart:oi(`Transition`,`TransitionStart`),transitioncancel:oi(`Transition`,`TransitionCancel`),transitionend:oi(`Transition`,`TransitionEnd`)},ci={},li={};Fn&&(li=document.createElement(`div`).style,`AnimationEvent`in window||(delete si.animationend.animation,delete si.animationiteration.animation,delete si.animationstart.animation),`TransitionEvent`in window||delete si.transitionend.transition);function ui(e){if(ci[e])return ci[e];if(!si[e])return e;var t=si[e],n;for(n in t)if(t.hasOwnProperty(n)&&n in li)return ci[e]=t[n];return e}var di=ui(`animationend`),fi=ui(`animationiteration`),pi=ui(`animationstart`),mi=ui(`transitionrun`),hi=ui(`transitionstart`),gi=ui(`transitioncancel`),_i=ui(`transitionend`),vi=new Map,yi=`abort auxClick beforeToggle cancel canPlay canPlayThrough click close contextMenu copy cut drag dragEnd dragEnter dragExit dragLeave dragOver dragStart drop durationChange emptied encrypted ended error fullscreenChange fullscreenError gotPointerCapture input invalid keyDown keyPress keyUp load loadedData loadedMetadata loadStart lostPointerCapture mouseDown mouseMove mouseOut mouseOver mouseUp paste pause play playing pointerCancel pointerDown pointerMove pointerOut pointerOver pointerUp progress rateChange reset resize seeked seeking stalled submit suspend timeUpdate touchCancel touchEnd touchStart volumeChange scroll toggle touchMove waiting wheel`.split(` `);yi.push(`scrollEnd`);function bi(e,t){vi.set(e,t),Jt(t,[e])}var xi=0;function Si(e,t){if(e.name!=null&&e.name!==`auto`)return e.name;if(t.autoName!==null)return t.autoName;e=bd.identifierPrefix;var n=xi++;return e=`_`+e+`t_`+n.toString(32)+`_`,t.autoName=e}function Ci(e){if(e==null||typeof e==`string`)return e;var t=null,n=Od;if(n!==null)for(var r=0;r<n.length;r++){var i=e[n[r]];if(i!=null){if(i===`none`)return`none`;t=t==null?i:t+(` `+i)}}return t??e.default}function wi(e,t){return e=Ci(e),t=Ci(t),t==null?e===`auto`?null:e:t===`auto`?null:t}var Ti=typeof reportError==`function`?reportError:function(e){if(typeof window==`object`&&typeof window.ErrorEvent==`function`){var t=new window.ErrorEvent(`error`,{bubbles:!0,cancelable:!0,message:typeof e==`object`&&e&&typeof e.message==`string`?String(e.message):String(e),error:e});if(!window.dispatchEvent(t))return}else if(typeof process==`object`&&typeof process.emit==`function`){process.emit(`uncaughtException`,e);return}console.error(e)},Ei=[],Di=0,Oi=0;function ki(){for(var e=Di,t=Oi=Di=0;t<e;){var n=Ei[t];Ei[t++]=null;var r=Ei[t];Ei[t++]=null;var i=Ei[t];Ei[t++]=null;var a=Ei[t];if(Ei[t++]=null,r!==null&&i!==null){var o=r.pending;o===null?i.next=i:(i.next=o.next,o.next=i),r.pending=i}a!==0&&Ni(n,i,a)}}function Ai(e,t,n,r){Ei[Di++]=e,Ei[Di++]=t,Ei[Di++]=n,Ei[Di++]=r,Oi|=r,e.lanes|=r,e=e.alternate,e!==null&&(e.lanes|=r)}function ji(e,t,n,r){return Ai(e,t,n,r),Pi(e)}function Mi(e,t){return Ai(e,null,null,t),Pi(e)}function Ni(e,t,n){e.lanes|=n;var r=e.alternate;r!==null&&(r.lanes|=n);for(var i=!1,a=e.return;a!==null;)a.childLanes|=n,r=a.alternate,r!==null&&(r.childLanes|=n),a.tag===22&&(e=a.stateNode,e===null||e._visibility&1||(i=!0)),e=a,a=a.return;return e.tag===3?(a=e.stateNode,i&&t!==null&&(i=31-ot(n),e=a.hiddenUpdates,r=e[i],r===null?e[i]=[t]:r.push(t),t.lane=n|536870912),a):null}function Pi(e){if(50<kd)throw kd=0,Ad=null,Error(i(185));for(var t=e.return;t!==null;)e=t,t=e.return;return e.tag===3?e.stateNode:null}var Fi={};function Ii(e,t,n,r){this.tag=e,this.key=n,this.sibling=this.child=this.return=this.stateNode=this.type=this.elementType=null,this.index=0,this.refCleanup=this.ref=null,this.pendingProps=t,this.dependencies=this.memoizedState=this.updateQueue=this.memoizedProps=null,this.mode=r,this.subtreeFlags=this.flags=0,this.deletions=null,this.childLanes=this.lanes=0,this.alternate=null}function Li(e,t,n,r){return new Ii(e,t,n,r)}function Ri(e){return e=e.prototype,!(!e||!e.isReactComponent)}function zi(e,t){var n=e.alternate;return n===null?(n=Li(e.tag,t,e.key,e.mode),n.elementType=e.elementType,n.type=e.type,n.stateNode=e.stateNode,n.alternate=e,e.alternate=n):(n.pendingProps=t,n.type=e.type,n.flags=0,n.subtreeFlags=0,n.deletions=null),n.flags=e.flags&1206910976,n.childLanes=e.childLanes,n.lanes=e.lanes,n.child=e.child,n.memoizedProps=e.memoizedProps,n.memoizedState=e.memoizedState,n.updateQueue=e.updateQueue,t=e.dependencies,n.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext},n.sibling=e.sibling,n.index=e.index,n.ref=e.ref,n.refCleanup=e.refCleanup,n}function Bi(e,t){e.flags&=1206910978;var n=e.alternate;return n===null?(e.childLanes=0,e.lanes=t,e.child=null,e.subtreeFlags=0,e.memoizedProps=null,e.memoizedState=null,e.updateQueue=null,e.dependencies=null,e.stateNode=null):(e.childLanes=n.childLanes,e.lanes=n.lanes,e.child=n.child,e.subtreeFlags=0,e.deletions=null,e.memoizedProps=n.memoizedProps,e.memoizedState=n.memoizedState,e.updateQueue=n.updateQueue,e.type=n.type,t=n.dependencies,e.dependencies=t===null?null:{lanes:t.lanes,firstContext:t.firstContext}),e}function Vi(e,t,n,r,a,o){var s=0;if(r=e,typeof r==`function`)Ri(r)&&(s=1);else if(typeof r==`string`)s=qm(e,n,Oe.current)?26:e===`html`||e===`head`||e===`body`?27:5;else a:switch(r){case me:return e=Li(31,n,t,a),e.elementType=me,e.lanes=o,e;case ae:return Hi(n.children,a,o,t);case oe:s=8,a|=24;break;case se:return e=Li(12,n,t,a|2),e.elementType=se,e.lanes=o,e;case E:return e=Li(13,n,t,a),e.elementType=E,e.lanes=o,e;case de:return e=Li(19,n,t,a),e.elementType=de,e.lanes=o,e;case he:case _e:return e=a|32,e=Li(30,n,t,e),e.elementType=_e,e.lanes=o,e.stateNode={autoName:null,paired:null,clones:null,ref:null},e;default:if(typeof r==`object`&&r)switch(r.$$typeof){case le:s=10;break a;case ce:s=9;break a;case ue:s=11;break a;case fe:s=14;break a;case pe:s=16,r=null;break a}s=29,n=Error(i(130,e===null?`null`:typeof e,``)),r=null}return t=Li(s,n,t,a),t.elementType=e,t.type=r,t.lanes=o,t}function Hi(e,t,n,r){return e=Li(7,e,r,t),e.lanes=n,e}function Ui(e,t,n){return e=Li(6,e,null,t),e.lanes=n,e}function Wi(e){var t=Li(18,null,null,0);return t.stateNode=e,t}function Gi(e,t,n){return t=Li(4,e.children===null?[]:e.children,e.key,t),t.lanes=n,t.stateNode={containerInfo:e.containerInfo,pendingChildren:null,implementation:e.implementation},t}var Ki=new WeakMap;function qi(e,t){if(typeof e==`object`&&e){var n=Ki.get(e);return n===void 0?(t={value:e,source:t,stack:He(t)},Ki.set(e,t),t):n}return{value:e,source:t,stack:He(t)}}var Ji=[],Yi=0,Xi=null,Zi=0,Qi=[],$i=0,ea=null,ta=1,na=``;function ra(e,t){Ji[Yi++]=Zi,Ji[Yi++]=Xi,Xi=e,Zi=t}function ia(e,t,n){Qi[$i++]=ta,Qi[$i++]=na,Qi[$i++]=ea,ea=e;var r=ta;e=na;var i=32-ot(r)-1;r&=~(1<<i),n+=1;var a=32-ot(t)+i;if(30<a){var o=i-i%5;a=(r&(1<<o)-1).toString(32),r>>=o,i-=o,ta=1<<32-ot(t)+i|n<<i|r,na=a+e}else ta=1<<a|n<<i|r,na=e}function aa(e){e.return!==null&&(ra(e,1),ia(e,1,0))}function oa(e){for(;e===Xi;)Xi=Ji[--Yi],Ji[Yi]=null,Zi=Ji[--Yi],Ji[Yi]=null;for(;e===ea;)ea=Qi[--$i],Qi[$i]=null,na=Qi[--$i],Qi[$i]=null,ta=Qi[--$i],Qi[$i]=null}function sa(e,t){Qi[$i++]=ta,Qi[$i++]=na,Qi[$i++]=ea,ta=t.id,na=t.overflow,ea=e}var ca=null,F=null,I=!1,la=null,ua=!1,da=Error(i(519));function fa(e){throw va(qi(Error(i(418,1<arguments.length&&arguments[1]!==void 0&&arguments[1]?`text`:`HTML`,``)),e)),da}function pa(e){var t=e.stateNode,n=e.type,r=e.memoizedProps;switch(t[At]=e,t[jt]=r,n){case`dialog`:Q(`cancel`,t),Q(`close`,t);break;case`iframe`:case`object`:case`embed`:Q(`load`,t);break;case`video`:case`audio`:for(n=0;n<zf.length;n++)Q(zf[n],t);break;case`source`:Q(`error`,t);break;case`img`:case`image`:case`link`:Q(`error`,t),Q(`load`,t);break;case`details`:Q(`toggle`,t);break;case`input`:Q(`invalid`,t),pn(t,r.value,r.defaultValue,r.checked,r.defaultChecked,r.type,r.name,!0);break;case`select`:Q(`invalid`,t);break;case`textarea`:Q(`invalid`,t),_n(t,r.value,r.defaultValue,r.children)}n=r.children,typeof n!=`string`&&typeof n!=`number`&&typeof n!=`bigint`||t.textContent===``+n||!0===r.suppressHydrationWarning||ep(t.textContent,n)?(r.popover!=null&&(Q(`beforetoggle`,t),Q(`toggle`,t)),r.onScroll!=null&&Q(`scroll`,t),r.onScrollEnd!=null&&Q(`scrollend`,t),r.onClick!=null&&(t.onclick=En),t=!0):t=!1,t||fa(e,!0)}function ma(e){for(ca=e.return;ca;)switch(ca.tag){case 5:case 31:case 13:ua=!1;return;case 27:case 3:ua=!0;return;default:ca=ca.return}}function ha(e){if(e!==ca)return!1;if(!I)return ma(e),I=!0,!1;var t=e.tag,n;if((n=t!==3&&t!==27)&&((n=t===5)&&(n=e.type,n=n===`form`||n===`button`||pp(e.type,e.memoizedProps)),n=!n),n&&F&&fa(e),ma(e),t===13){if(e=e.memoizedState,e=e===null?null:e.dehydrated,!e)throw Error(i(317));F=dm(e)}else if(t===31){if(e=e.memoizedState,e=e===null?null:e.dehydrated,!e)throw Error(i(317));F=dm(e)}else t===27?(t=F,Sp(e.type)?(e=um,um=null,F=e):F=t):F=ca?lm(e.stateNode.nextSibling):null;return!0}function ga(){F=ca=null,I=!1}function _a(){var e=la;return e!==null&&(pd===null?pd=e:pd.push.apply(pd,e),la=null),e}function va(e){la===null?la=[e]:la.push(e)}var ya=De(null),ba=null,xa=null;function Sa(e,t,n){A(ya,t._currentValue),t._currentValue=n}function Ca(e){e._currentValue=ya.current,k(ya)}function wa(e,t,n){for(;e!==null;){var r=e.alternate;if((e.childLanes&t)===t?r!==null&&(r.childLanes&t)!==t&&(r.childLanes|=t):(e.childLanes|=t,r!==null&&(r.childLanes|=t)),e===n)break;e=e.return}}function Ta(e,t,n,r){var a=e.child;for(a!==null&&(a.return=e);a!==null;){var o=a.dependencies;if(o!==null){var s=a.child;o=o.firstContext;a:for(;o!==null;){var c=o;o=a;for(var l=0;l<t.length;l++)if(c.context===t[l]){o.lanes|=n,c=o.alternate,c!==null&&(c.lanes|=n),wa(o.return,n,e),r||(s=null);break a}o=c.next}}else if(a.tag===18){if(s=a.return,s===null)throw Error(i(341));s.lanes|=n,o=s.alternate,o!==null&&(o.lanes|=n),wa(s,n,e),s=null}else a.tag===13&&a.memoizedState!==null&&a.memoizedState.dehydrated===null?(a.lanes|=n,s=a.alternate,s!==null&&(s.lanes|=n),wa(a.return,n,e),s=a.child,s=s===null?null:s.sibling):s=a.child;if(s!==null)s.return=a;else for(s=a;s!==null;){if(s===e){s=null;break}if(a=s.sibling,a!==null){a.return=s.return,s=a;break}s=s.return}a=s}}function Ea(e,t,n,r){e=null;for(var a=t,o=!1;a!==null;){if(!o){if(a.flags&524288)o=!0;else if(a.flags&262144)break}if(a.tag===10){var s=a.alternate;if(s===null)throw Error(i(387));if(s=s.memoizedProps,s!==null){var c=a.type;Kr(a.pendingProps.value,s.value)||(e===null?e=[c]:e.push(c))}}else if(a===je.current){if(s=a.alternate,s===null)throw Error(i(387));s.memoizedState.memoizedState!==a.memoizedState.memoizedState&&(e===null?e=[sh]:e.push(sh))}a=a.return}return e!==null&&Ta(t,e,n,r),t.flags|=262144,e!==null}function Da(e){for(e=e.firstContext;e!==null;){if(!Kr(e.context._currentValue,e.memoizedValue))return!0;e=e.next}return!1}function Oa(e){ba=e,xa=null,e=e.dependencies,e!==null&&(e.firstContext=null)}function ka(e){return ja(ba,e)}function Aa(e,t){return ba===null&&Oa(e),ja(e,t)}function ja(e,t){var n=t._currentValue;if(t={context:t,memoizedValue:n,next:null},xa===null){if(e===null)throw Error(i(308));xa=t,e.dependencies={lanes:0,firstContext:t},e.flags|=524288}else xa=xa.next=t;return n}var Ma=typeof AbortController<`u`?AbortController:function(){var e=[],t=this.signal={aborted:!1,addEventListener:function(t,n){e.push(n)}};this.abort=function(){t.aborted=!0,e.forEach(function(e){return e()})}},Na=t.unstable_scheduleCallback,Pa=t.unstable_NormalPriority,Fa={$$typeof:le,Consumer:null,Provider:null,_currentValue:null,_currentValue2:null,_threadCount:0};function Ia(){return{controller:new Ma,data:new Map,refCount:0}}function La(e){e.refCount--,e.refCount===0&&Na(Pa,function(){e.controller.abort()})}function Ra(e,t){if(e.pendingLanes&4194048){var n=e.transitionTypes;for(n===null&&(n=e.transitionTypes=[]),e=0;e<t.length;e++){var r=t[e];n.indexOf(r)===-1&&n.push(r)}}}var za=null;function Ba(e){var t=e.transitionTypes;return e.transitionTypes=null,t}var Va=null,Ha=0,Ua=0,Wa=null;function Ga(e,t){if(Va===null){var n=Va=[];Ha=0,Ua=Pf(),Wa={status:`pending`,value:void 0,then:function(e){n.push(e)}}}return Ha++,t.then(Ka,Ka),t}function Ka(){if(--Ha===0&&(za=null,Va!==null)){Wa!==null&&(Wa.status=`fulfilled`);var e=Va;Va=null,Ua=0,Wa=null;for(var t=0;t<e.length;t++)(0,e[t])()}}function qa(e,t){var n=[],r={status:`pending`,value:null,reason:null,then:function(e){n.push(e)}};return e.then(function(){r.status=`fulfilled`,r.value=t;for(var e=0;e<n.length;e++)(0,n[e])(t)},function(e){for(r.status=`rejected`,r.reason=e,e=0;e<n.length;e++)(0,n[e])(void 0)}),r}var Ja=D.S;D.S=function(e,t){if(gd=Je(),typeof t==`object`&&t&&typeof t.then==`function`&&Ga(e,t),za!==null)for(var n=bf;n!==null;)Ra(n,za),n=n.next;if(n=e.types,n!==null){for(var r=bf;r!==null;)Ra(r,n),r=r.next;if(Ua!==0){r=za,r===null&&(r=za=[]);for(var i=0;i<n.length;i++){var a=n[i];r.indexOf(a)===-1&&r.push(a)}}}Ja!==null&&Ja(e,t)};var Ya=De(null);function Xa(){var e=Ya.current;return e===null?G.pooledCache:e}function Za(e,t){t===null?A(Ya,Ya.current):A(Ya,t.pool)}function Qa(){var e=Xa();return e===null?null:{parent:Fa._currentValue,pool:e}}var $a=Error(i(460)),eo=Error(i(474)),to=Error(i(542)),no={then:function(){}};function ro(e){return e=e.status,e===`fulfilled`||e===`rejected`}function io(e,t,n){switch(n=e[n],n===void 0?e.push(t):n!==t&&(t.then(En,En),t=n),t.status){case`fulfilled`:return t.value;case`rejected`:throw e=t.reason,co(e),e===void 0&&!(`reason`in t)?Error(i(600)):e;default:if(typeof t.status==`string`)t.then(En,En);else{if(e=G,e!==null&&100<e.shellSuspendCounter)throw Error(i(482));e=t,e.status=`pending`,e.then(function(e){if(t.status===`pending`){var n=t;n.status=`fulfilled`,n.value=e}},function(e){if(t.status===`pending`){var n=t;n.status=`rejected`,n.reason=e}})}switch(t.status){case`fulfilled`:return t.value;case`rejected`:throw e=t.reason,co(e),e}throw oo=t,$a}}function ao(e){try{var t=e._init;return t(e._payload)}catch(e){throw typeof e==`object`&&e&&typeof e.then==`function`?(oo=e,$a):e}}var oo=null;function so(){if(oo===null)throw Error(i(459));var e=oo;return oo=null,e}function co(e){if(e===$a||e===to)throw Error(i(483))}var lo=null,uo=0;function fo(e){var t=uo;return uo+=1,lo===null&&(lo=[]),io(lo,e,t)}function po(e,t){t=t.props.ref,e.ref=t===void 0?null:t}function mo(e,t){throw t.$$typeof===ne?Error(i(525)):(e=Object.prototype.toString.call(t),Error(i(31,e===`[object Object]`?`object with keys {`+Object.keys(t).join(`, `)+`}`:e)))}function ho(e){function t(t,n){if(e){var r=t.deletions;r===null?(t.deletions=[n],t.flags|=16):r.push(n)}}function n(n,r){if(!e)return null;for(;r!==null;)t(n,r),r=r.sibling;return null}function r(e){for(var t=new Map;e!==null;)e.key===null?t.set(e.index,e):t.set(e.key,e),e=e.sibling;return t}function a(e,t){return e=zi(e,t),e.index=0,e.sibling=null,e}function o(t,n,r){return t.index=r,e?(r=t.alternate,r===null?(t.flags|=134217730,n):(r=r.index,r<n?(t.flags|=2,n):r)):(t.flags|=1048576,n)}function s(t){return e&&t.alternate===null&&(t.flags|=134217730),t}function c(e,t,n,r){return t===null||t.tag!==6?(t=Ui(n,e.mode,r),t.return=e,t):(t=a(t,n),t.return=e,t)}function l(e,t,n,r){var i=n.type;return i===ae?(e=d(e,t,n.props.children,r,n.key),po(e,n),e):t!==null&&(t.elementType===i||typeof i==`object`&&i&&i.$$typeof===pe&&ao(i)===t.type)?(t=a(t,n.props),po(t,n),t.return=e,t):(t=Vi(n.type,n.key,n.props,null,e.mode,r),po(t,n),t.return=e,t)}function u(e,t,n,r){return t===null||t.tag!==4||t.stateNode.containerInfo!==n.containerInfo||t.stateNode.implementation!==n.implementation?(t=Gi(n,e.mode,r),t.return=e,t):(t=a(t,n.children||[]),t.return=e,t)}function d(e,t,n,r,i){return t===null||t.tag!==7?(t=Hi(n,e.mode,r,i),t.return=e,t):(t=a(t,n),t.return=e,t)}function f(e,t,n){if(typeof t==`string`&&t!==``||typeof t==`number`||typeof t==`bigint`)return t=Ui(``+t,e.mode,n),t.return=e,t;if(typeof t==`object`&&t){switch(t.$$typeof){case re:return n=Vi(t.type,t.key,t.props,null,e.mode,n),po(n,t),n.return=e,n;case ie:return t=Gi(t,e.mode,n),t.return=e,t;case pe:return t=ao(t),f(e,t,n)}if(Ce(t)||be(t))return t=Hi(t,e.mode,n,null),t.return=e,t;if(typeof t.then==`function`)return f(e,fo(t),n);if(t.$$typeof===le)return f(e,Aa(e,t),n);mo(e,t)}return null}function p(e,t,n,r){var i=t===null?null:t.key;if(typeof n==`string`&&n!==``||typeof n==`number`||typeof n==`bigint`)return i===null?c(e,t,``+n,r):null;if(typeof n==`object`&&n){switch(n.$$typeof){case re:return n.key===i?l(e,t,n,r):null;case ie:return n.key===i?u(e,t,n,r):null;case pe:return n=ao(n),p(e,t,n,r)}if(Ce(n)||be(n))return i===null?d(e,t,n,r,null):null;if(typeof n.then==`function`)return p(e,t,fo(n),r);if(n.$$typeof===le)return p(e,t,Aa(e,n),r);mo(e,n)}return null}function m(e,t,n,r,i){if(typeof r==`string`&&r!==``||typeof r==`number`||typeof r==`bigint`)return e=e.get(n)||null,c(t,e,``+r,i);if(typeof r==`object`&&r){switch(r.$$typeof){case re:return e=e.get(r.key===null?n:r.key)||null,l(t,e,r,i);case ie:return e=e.get(r.key===null?n:r.key)||null,u(t,e,r,i);case pe:return r=ao(r),m(e,t,n,r,i)}if(Ce(r)||be(r))return e=e.get(n)||null,d(t,e,r,i,null);if(typeof r.then==`function`)return m(e,t,n,fo(r),i);if(r.$$typeof===le)return m(e,t,n,Aa(t,r),i);mo(t,r)}return null}function h(i,a,s,c){for(var l=null,u=null,d=a,h=a=0,g=null;d!==null&&h<s.length;h++){d.index>h?(g=d,d=null):g=d.sibling;var _=p(i,d,s[h],c);if(_===null){d===null&&(d=g);break}e&&d&&_.alternate===null&&t(i,d),a=o(_,a,h),u===null?l=_:u.sibling=_,u=_,d=g}if(h===s.length)return n(i,d),I&&ra(i,h),l;if(d===null){for(;h<s.length;h++)d=f(i,s[h],c),d!==null&&(a=o(d,a,h),u===null?l=d:u.sibling=d,u=d);return I&&ra(i,h),l}for(d=r(d);h<s.length;h++)g=m(d,i,h,s[h],c),g!==null&&(e&&(_=g.alternate,_!==null&&d.delete(_.key===null?h:_.key)),a=o(g,a,h),u===null?l=g:u.sibling=g,u=g);return e&&d.forEach(function(e){return t(i,e)}),I&&ra(i,h),l}function g(a,s,c,l){if(c==null)throw Error(i(151));for(var u=null,d=null,h=s,g=s=0,_=null,v=c.next();h!==null&&!v.done;g++,v=c.next()){h.index>g?(_=h,h=null):_=h.sibling;var y=p(a,h,v.value,l);if(y===null){h===null&&(h=_);break}e&&h&&y.alternate===null&&t(a,h),s=o(y,s,g),d===null?u=y:d.sibling=y,d=y,h=_}if(v.done)return n(a,h),I&&ra(a,g),u;if(h===null){for(;!v.done;g++,v=c.next())v=f(a,v.value,l),v!==null&&(s=o(v,s,g),d===null?u=v:d.sibling=v,d=v);return I&&ra(a,g),u}for(h=r(h);!v.done;g++,v=c.next())v=m(h,a,g,v.value,l),v!==null&&(e&&(_=v.alternate,_!==null&&h.delete(_.key===null?g:_.key)),s=o(v,s,g),d===null?u=v:d.sibling=v,d=v);return e&&h.forEach(function(e){return t(a,e)}),I&&ra(a,g),u}function _(e,r,o,c){if(typeof o==`object`&&o&&o.type===ae&&o.key===null&&o.props.ref===void 0&&(o=o.props.children),typeof o==`object`&&o){switch(o.$$typeof){case re:a:{for(var l=o.key;r!==null;){if(r.key===l){if(l=o.type,l===ae){if(r.tag===7){n(e,r.sibling),c=a(r,o.props.children),po(c,o),c.return=e,e=c;break a}}else if(r.elementType===l||typeof l==`object`&&l&&l.$$typeof===pe&&ao(l)===r.type){n(e,r.sibling),c=a(r,o.props),po(c,o),c.return=e,e=c;break a}n(e,r);break}t(e,r),r=r.sibling}o.type===ae?(c=Hi(o.props.children,e.mode,c,o.key),po(c,o),c.return=e,e=c):(c=Vi(o.type,o.key,o.props,null,e.mode,c),po(c,o),c.return=e,e=c)}return s(e);case ie:a:{for(l=o.key;r!==null;){if(r.key===l){if(r.tag===4&&r.stateNode.containerInfo===o.containerInfo&&r.stateNode.implementation===o.implementation){n(e,r.sibling),c=a(r,o.children||[]),c.return=e,e=c;break a}n(e,r);break}t(e,r),r=r.sibling}c=Gi(o,e.mode,c),c.return=e,e=c}return s(e);case pe:return o=ao(o),_(e,r,o,c)}if(Ce(o))return h(e,r,o,c);if(be(o)){if(l=be(o),typeof l!=`function`)throw Error(i(150));return o=l.call(o),g(e,r,o,c)}if(typeof o.then==`function`)return _(e,r,fo(o),c);if(o.$$typeof===le)return _(e,r,Aa(e,o),c);mo(e,o)}return typeof o==`string`&&o!==``||typeof o==`number`||typeof o==`bigint`?(o=``+o,r!==null&&r.tag===6?(n(e,r.sibling),c=a(r,o),c.return=e,e=c):(n(e,r),c=Ui(o,e.mode,c),c.return=e,e=c),s(e)):n(e,r)}return function(e,t,n,r){try{uo=0;var i=_(e,t,n,r);return lo=null,i}catch(t){if(t===$a||t===to)throw t;var a=Li(29,t,null,e.mode);return a.lanes=r,a.return=e,a}}}var go=ho(!0),_o=ho(!1),vo=!1;function yo(e){e.updateQueue={baseState:e.memoizedState,firstBaseUpdate:null,lastBaseUpdate:null,shared:{pending:null,lanes:0,hiddenCallbacks:null},callbacks:null}}function bo(e,t){e=e.updateQueue,t.updateQueue===e&&(t.updateQueue={baseState:e.baseState,firstBaseUpdate:e.firstBaseUpdate,lastBaseUpdate:e.lastBaseUpdate,shared:e.shared,callbacks:null})}function xo(e){return{lane:e,tag:0,payload:null,callback:null,next:null}}function So(e,t,n){var r=e.updateQueue;if(r===null)return null;if(r=r.shared,W&2){var i=r.pending;return i===null?t.next=t:(t.next=i.next,i.next=t),r.pending=t,t=Pi(e),Ni(e,null,n),t}return Ai(e,r,t,n),Pi(e)}function Co(e,t,n){if(t=t.updateQueue,t!==null&&(t=t.shared,n&4194048)){var r=t.lanes;r&=e.pendingLanes,n|=r,t.lanes=n,Ct(e,n)}}function wo(e,t){var n=e.updateQueue,r=e.alternate;if(r!==null&&(r=r.updateQueue,n===r)){var i=null,a=null;if(n=n.firstBaseUpdate,n!==null){do{var o={lane:n.lane,tag:n.tag,payload:n.payload,callback:null,next:null};a===null?i=a=o:a=a.next=o,n=n.next}while(n!==null);a===null?i=a=t:a=a.next=t}else i=a=t;n={baseState:r.baseState,firstBaseUpdate:i,lastBaseUpdate:a,shared:r.shared,callbacks:r.callbacks},e.updateQueue=n}else e=n.lastBaseUpdate,e===null?n.firstBaseUpdate=t:e.next=t,n.lastBaseUpdate=t}var To=!1;function Eo(){if(To){var e=Wa;if(e!==null)throw e}}function Do(e,t,n,r){To=!1;var i=e.updateQueue;vo=!1;var a=i.firstBaseUpdate,o=i.lastBaseUpdate,s=i.shared.pending;if(s!==null){i.shared.pending=null;var c=s,l=c.next;c.next=null,o===null?a=l:o.next=l,o=c;var u=e.alternate;u!==null&&(u=u.updateQueue,s=u.lastBaseUpdate,s!==o&&(s===null?u.firstBaseUpdate=l:s.next=l,u.lastBaseUpdate=c))}if(a!==null){var d=i.baseState;o=0,u=l=c=null,s=a;do{var f=s.lane&-536870913,p=f!==s.lane;if(p?(q&f)===f:(r&f)===f){f!==0&&f===Ua&&(To=!0),u!==null&&(u=u.next={lane:0,tag:s.tag,payload:s.payload,callback:null,next:null});a:{var m=e,h=s;f=t;var g=n;switch(h.tag){case 1:if(m=h.payload,typeof m==`function`){d=m.call(g,d,f);break a}d=m;break a;case 3:m.flags=m.flags&-65537|128;case 0:if(m=h.payload,f=typeof m==`function`?m.call(g,d,f):m,f==null)break a;d=T({},d,f);break a;case 2:vo=!0}}f=s.callback,f!==null&&(e.flags|=64,p&&(e.flags|=8192),p=i.callbacks,p===null?i.callbacks=[f]:p.push(f))}else p={lane:f,tag:s.tag,payload:s.payload,callback:s.callback,next:null},u===null?(l=u=p,c=d):u=u.next=p,o|=f;if(s=s.next,s===null){if(s=i.shared.pending,s===null)break;p=s,s=p.next,p.next=null,i.lastBaseUpdate=p,i.shared.pending=null}}while(1);u===null&&(c=d),i.baseState=c,i.firstBaseUpdate=l,i.lastBaseUpdate=u,a===null&&(i.shared.lanes=0),sd|=o,e.lanes=o,e.memoizedState=d}}function Oo(e,t){if(typeof e!=`function`)throw Error(i(191,e));e.call(t)}function ko(e,t){var n=e.callbacks;if(n!==null)for(e.callbacks=null,e=0;e<n.length;e++)Oo(n[e],t)}var Ao=De(null),jo=De(0);function Mo(e,t){e=od,A(jo,e),A(Ao,t),od=e|t.baseLanes}function No(){A(jo,od),A(Ao,Ao.current)}function Po(){od=jo.current,k(Ao),k(jo)}var Fo=De(null),Io=null;function Lo(e){var t=e.alternate;A(Ho,Ho.current&1),A(Fo,e),Io===null&&(t===null||Ao.current!==null||t.memoizedState!==null)&&(Io=e)}function Ro(e){A(Ho,Ho.current),A(Fo,e),Io===null&&(Io=e)}function zo(e){e.tag===22?(A(Ho,Ho.current),A(Fo,e),Io===null&&(Io=e)):Bo()}function Bo(){A(Ho,Ho.current),A(Fo,Fo.current)}function Vo(e){k(Fo),Io===e&&(Io=null),k(Ho)}var Ho=De(0);function Uo(e,t){A(Fo,Fo.current),A(Ho,t)}function Wo(e){k(Ho),k(Fo),Io===e&&(Io=null)}function Go(e){for(var t=e;t!==null;){if(t.tag===13){var n=t.memoizedState;if(n!==null&&(n=n.dehydrated,n===null||om(n)||sm(n)))return t}else if(t.tag===19&&t.memoizedProps.revealOrder!==`independent`){if(t.flags&128)return t}else if(t.child!==null){t.child.return=t,t=t.child;continue}if(t===e)break;for(;t.sibling===null;){if(t.return===null||t.return===e)return null;t=t.return}t.sibling.return=t.return,t=t.sibling}return null}var Ko=0,L=null,R=null,qo=null,Jo=!1,Yo=!1,Xo=!1,Zo=0,Qo=0,$o=null,es=0;function ts(){throw Error(i(321))}function ns(e,t){if(t===null)return!1;for(var n=0;n<t.length&&n<e.length;n++)if(!Kr(e[n],t[n]))return!1;return!0}function rs(e,t,n,r,i,a){return Ko=a,L=t,t.memoizedState=null,t.updateQueue=null,t.lanes=0,D.H=e===null||e.memoizedState===null?vc:yc,Xo=!1,a=n(r,i),Xo=!1,Yo&&(a=as(t,n,r,i)),is(e),a}function is(e){D.H=_c;var t=R!==null&&R.next!==null;if(Ko=0,qo=R=L=null,Jo=!1,Qo=0,$o=null,t)throw Error(i(300));e===null||Ic||(e=e.dependencies,e!==null&&Da(e)&&(Ic=!0))}function as(e,t,n,r){L=e;var a=0;do{if(Yo&&($o=null),Qo=0,Yo=!1,25<=a)throw Error(i(301));if(a+=1,qo=R=null,e.updateQueue!=null){var o=e.updateQueue;o.lastEffect=null,o.events=null,o.stores=null,o.memoCache!=null&&(o.memoCache.index=0)}D.H=bc,o=t(n,r)}while(Yo);return o}function os(){var e=D.H,t=e.useState()[0];return t=typeof t.then==`function`?ps(t):t,e=e.useState()[0],(R===null?null:R.memoizedState)!==e&&(L.flags|=1024),t}function ss(){var e=Zo!==0;return Zo=0,e}function cs(e,t,n){t.updateQueue=e.updateQueue,t.flags&=-2053,e.lanes&=~n}function ls(e){if(Jo){for(e=e.memoizedState;e!==null;){var t=e.queue;t!==null&&(t.pending=null),e=e.next}Jo=!1}Ko=0,qo=R=L=null,Yo=!1,Qo=Zo=0,$o=null}function us(){var e={memoizedState:null,baseState:null,baseQueue:null,queue:null,next:null};return qo===null?L.memoizedState=qo=e:qo=qo.next=e,qo}function ds(){if(R===null){var e=L.alternate;e=e===null?null:e.memoizedState}else e=R.next;var t=qo===null?L.memoizedState:qo.next;if(t!==null)qo=t,R=e;else{if(e===null)throw L.alternate===null?Error(i(467)):Error(i(310));R=e,e={memoizedState:R.memoizedState,baseState:R.baseState,baseQueue:R.baseQueue,queue:R.queue,next:null},qo===null?L.memoizedState=qo=e:qo=qo.next=e}return qo}function fs(){return{lastEffect:null,events:null,stores:null,memoCache:null}}function ps(e){var t=Qo;return Qo+=1,$o===null&&($o=[]),e=io($o,e,t),t=L,(qo===null?t.memoizedState:qo.next)===null&&(t=t.alternate,D.H=t===null||t.memoizedState===null?vc:yc),e}function ms(e){if(typeof e==`object`&&e){if(typeof e.then==`function`)return ps(e);if(e.$$typeof===ve)return;if(e.$$typeof===le)return ka(e)}throw Error(i(438,String(e)))}function hs(e){var t=null,n=L.updateQueue;if(n!==null&&(t=n.memoCache),t==null){var r=L.alternate;r!==null&&(r=r.updateQueue,r!==null&&(r=r.memoCache,r!=null&&(t={data:r.data.map(function(e){return e.slice()}),index:0})))}if(t??={data:[],index:0},n===null&&(n=fs(),L.updateQueue=n),n.memoCache=t,n=t.data[t.index],n===void 0)for(n=t.data[t.index]=Array(e),r=0;r<e;r++)n[r]=ge;return t.index++,n}function gs(e,t){return typeof t==`function`?t(e):t}function _s(e){return vs(ds(),R,e)}function vs(e,t,n){var r=e.queue;if(r===null)throw Error(i(311));r.lastRenderedReducer=n;var a=e.baseQueue,o=r.pending;if(o!==null){if(a!==null){var s=a.next;a.next=o.next,o.next=s}t.baseQueue=a=o,r.pending=null}if(o=e.baseState,a===null)e.memoizedState=o;else{t=a.next;var c=s=null,l=null,u=t,d=!1;do{var f=u.lane&-536870913;if(f===u.lane?(Ko&f)===f:(q&f)===f){var p=u.revertLane;if(p===0)l!==null&&(l=l.next={lane:0,revertLane:0,gesture:null,action:u.action,hasEagerState:u.hasEagerState,eagerState:u.eagerState,next:null}),f===Ua&&(d=!0);else if((Ko&p)===p){u=u.next,p===Ua&&(d=!0);continue}else f={lane:0,revertLane:u.revertLane,gesture:null,action:u.action,hasEagerState:u.hasEagerState,eagerState:u.eagerState,next:null},l===null?(c=l=f,s=o):l=l.next=f,L.lanes|=p,sd|=p;f=u.action,Xo&&n(o,f),o=u.hasEagerState?u.eagerState:n(o,f)}else p={lane:f,revertLane:u.revertLane,gesture:u.gesture,action:u.action,hasEagerState:u.hasEagerState,eagerState:u.eagerState,next:null},l===null?(c=l=p,s=o):l=l.next=p,L.lanes|=f,sd|=f;u=u.next}while(u!==null&&u!==t);if(l===null?s=o:l.next=c,!Kr(o,e.memoizedState)&&(Ic=!0,d&&(n=Wa,n!==null)))throw n;e.memoizedState=o,e.baseState=s,e.baseQueue=l,r.lastRenderedState=o}return a===null&&(r.lanes=0),[e.memoizedState,r.dispatch]}function ys(e){var t=ds(),n=t.queue;if(n===null)throw Error(i(311));n.lastRenderedReducer=e;var r=n.dispatch,a=n.pending,o=t.memoizedState;if(a!==null){n.pending=null;var s=a=a.next;do o=e(o,s.action),s=s.next;while(s!==a);Kr(o,t.memoizedState)||(Ic=!0),t.memoizedState=o,t.baseQueue===null&&(t.baseState=o),n.lastRenderedState=o}return[o,r]}function z(e,t,n){var r=L,a=ds(),o=I;if(o){if(n===void 0)throw Error(i(407));n=n()}else n=t();var s=!Kr((R||a).memoizedState,n);if(s&&(a.memoizedState=n,Ic=!0),a=a.queue,Us(xs.bind(null,r,a,e),[e]),e=a.getSnapshot!==t||s||qo!==null&&!!(qo.memoizedState.tag&1),Rs(e?9:8,{destroy:void 0},bs.bind(null,r,a,n,t),null),e){if(r.flags|=2048,G===null)throw Error(i(349));o||Ko&127||B(r,t,n)}return n}function B(e,t,n){e.flags|=16384,e={getSnapshot:t,value:n},t=L.updateQueue,t===null?(t=fs(),L.updateQueue=t,t.stores=[e]):(n=t.stores,n===null?t.stores=[e]:n.push(e))}function bs(e,t,n,r){t.value=n,t.getSnapshot=r,Ss(t)&&Cs(e)}function xs(e,t,n){return n(function(){Ss(t)&&Cs(e)})}function Ss(e){var t=e.getSnapshot;e=e.value;try{var n=t();return!Kr(e,n)}catch{return!0}}function Cs(e){var t=Mi(e,2);t!==null&&Pd(t,e,2)}function ws(e){var t=us();if(typeof e==`function`){var n=e;if(e=n(),Xo){at(!0);try{n()}finally{at(!1)}}}return t.memoizedState=t.baseState=e,t.queue={pending:null,lanes:0,dispatch:null,lastRenderedReducer:gs,lastRenderedState:e},t}function Ts(e,t,n,r){return e.baseState=n,vs(e,R,typeof r==`function`?r:gs)}function Es(e,t,n,r,a){if(mc(e))throw Error(i(485));if(e=t.action,e!==null){var o={payload:a,action:e,next:null,isTransition:!0,status:`pending`,value:null,reason:null,listeners:[],then:function(e){o.listeners.push(e)}};D.T===null?o.isTransition=!1:n(!0),r(o),n=t.pending,n===null?(o.next=t.pending=o,Ds(t,o)):(o.next=n.next,t.pending=n.next=o)}}function Ds(e,t){var n=t.action,r=t.payload,i=e.state;if(t.isTransition){var a=D.T,o={};o.types=a===null?null:a.types,D.T=o;try{var s=n(i,r),c=D.S;c!==null&&c(o,s),Os(e,t,s)}catch(n){As(e,t,n)}finally{a!==null&&o.types!==null&&(a.types=o.types),D.T=a}}else try{a=n(i,r),Os(e,t,a)}catch(n){As(e,t,n)}}function Os(e,t,n){typeof n==`object`&&n&&typeof n.then==`function`?n.then(function(n){ks(e,t,n)},function(n){return As(e,t,n)}):ks(e,t,n)}function ks(e,t,n){t.status=`fulfilled`,t.value=n,js(t),e.state=n,t=e.pending,t!==null&&(n=t.next,n===t?e.pending=null:(n=n.next,t.next=n,Ds(e,n)))}function As(e,t,n){var r=e.pending;if(e.pending=null,r!==null){r=r.next;do t.status=`rejected`,t.reason=n,js(t),t=t.next;while(t!==r)}e.action=null}function js(e){e=e.listeners;for(var t=0;t<e.length;t++)(0,e[t])()}function Ms(e,t){return t}function Ns(e,t){if(I){var n=G.formState;if(n!==null){a:{var r=L;if(I){if(F){b:{for(var i=F,a=ua;i.nodeType!==8;)if(!a||(i=lm(i.nextSibling),i===null)){i=null;break b}a=i.data,i=a===`F!`||a===`F`?i:null}if(i){F=lm(i.nextSibling),r=i.data===`F!`;break a}}fa(r)}r=!1}r&&(t=n[0])}}return n=us(),n.memoizedState=n.baseState=t,r={pending:null,lanes:0,dispatch:null,lastRenderedReducer:Ms,lastRenderedState:t},n.queue=r,n=dc.bind(null,L,r),r.dispatch=n,r=ws(!1),a=pc.bind(null,L,!1,r.queue),r=us(),i={state:t,dispatch:null,action:e,pending:null},r.queue=i,n=Es.bind(null,L,i,a,n),i.dispatch=n,r.memoizedState=e,[t,n,!1]}function Ps(e){return Fs(ds(),R,e)}function Fs(e,t,n){if(t=vs(e,t,Ms)[0],e=_s(gs)[0],typeof t==`object`&&t&&typeof t.then==`function`)try{var r=ps(t)}catch(e){throw e===$a?to:e}else r=t;t=ds();var i=t.queue,a=i.dispatch;return n!==t.memoizedState&&(L.flags|=2048,Rs(9,{destroy:void 0},Is.bind(null,i,n),null)),[r,a,e]}function Is(e,t){e.action=t}function Ls(e){var t=ds(),n=R;if(n!==null)return Fs(t,n,e);ds(),t=t.memoizedState,n=ds();var r=n.queue.dispatch;return n.memoizedState=e,[t,r,!1]}function Rs(e,t,n,r){return e={tag:e,create:n,deps:r,inst:t,next:null},t=L.updateQueue,t===null&&(t=fs(),L.updateQueue=t),n=t.lastEffect,n===null?t.lastEffect=e.next=e:(r=n.next,n.next=e,e.next=r,t.lastEffect=e),e}function zs(){return ds().memoizedState}function Bs(e,t,n,r){var i=us();L.flags|=e,i.memoizedState=Rs(1|t,{destroy:void 0},n,r===void 0?null:r)}function Vs(e,t,n,r){var i=ds();r=r===void 0?null:r;var a=i.memoizedState.inst;R!==null&&r!==null&&ns(r,R.memoizedState.deps)?i.memoizedState=Rs(t,a,n,r):(L.flags|=e,i.memoizedState=Rs(1|t,a,n,r))}function Hs(e,t){Bs(8390656,8,e,t)}function Us(e,t){Vs(2048,8,e,t)}function Ws(e){L.flags|=4;var t=L.updateQueue;if(t===null)t=fs(),L.updateQueue=t,t.events=[e];else{var n=t.events;n===null?t.events=[e]:n.push(e)}}function Gs(e){var t=ds().memoizedState;return Ws({ref:t,nextImpl:e}),function(){if(W&2)throw Error(i(440));return t.impl.apply(void 0,arguments)}}function Ks(e,t){return Vs(4,2,e,t)}function qs(e,t){return Vs(4,4,e,t)}function Js(e,t){if(typeof t==`function`){e=e();var n=t(e);return function(){typeof n==`function`?n():t(null)}}if(t!=null)return e=e(),t.current=e,function(){t.current=null}}function Ys(e,t,n){n=n==null?null:n.concat([e]),Vs(4,4,Js.bind(null,t,e),n)}function Xs(){}function Zs(e,t){var n=ds();t=t===void 0?null:t;var r=n.memoizedState;return t!==null&&ns(t,r[1])?r[0]:(n.memoizedState=[e,t],e)}function Qs(e,t){var n=ds();t=t===void 0?null:t;var r=n.memoizedState;if(t!==null&&ns(t,r[1]))return r[0];if(r=e(),Xo){at(!0);try{e()}finally{at(!1)}}return n.memoizedState=[r,t],r}function $s(e,t,n){return n===void 0||Ko&1073741824&&!(q&261930)?e.memoizedState=t:(e.memoizedState=n,e=Md(),L.lanes|=e,sd|=e,n)}function ec(e,t,n,r){return Kr(n,t)?n:Ao.current===null?!(Ko&106)||Ko&1073741824&&!(q&261930)?(Ic=!0,e.memoizedState=n):(e=Md(),L.lanes|=e,sd|=e,t):(e=$s(e,n,r),Kr(e,t)||(Ic=!0),e)}function tc(e,t,n,r,i){var a=O.p;O.p=a!==0&&8>a?a:8;var o=D.T,s={};s.types=o===null?null:o.types,D.T=s,pc(e,!1,t,n);try{var c=i(),l=D.S;l!==null&&l(s,c),typeof c==`object`&&c&&typeof c.then==`function`?fc(e,t,qa(c,r),jd(e)):fc(e,t,r,jd(e))}catch(n){fc(e,t,{then:function(){},status:`rejected`,reason:n},jd())}finally{O.p=a,o!==null&&s.types!==null&&(o.types=s.types),D.T=o}}function nc(){}function rc(e,t,n,r){if(e.tag!==5)throw Error(i(476));var a=ic(e).queue;tc(e,a,t,we,n===null?nc:function(){return ac(e),n(r)})}function ic(e){var t=e.memoizedState;if(t!==null)return t;t={memoizedState:we,baseState:we,baseQueue:null,queue:{pending:null,lanes:0,dispatch:null,lastRenderedReducer:gs,lastRenderedState:we},next:null};var n={};return t.next={memoizedState:n,baseState:n,baseQueue:null,queue:{pending:null,lanes:0,dispatch:null,lastRenderedReducer:gs,lastRenderedState:n},next:null},e.memoizedState=t,e=e.alternate,e!==null&&(e.memoizedState=t),t}function ac(e){var t=ic(e);t.next===null&&(t=e.alternate.memoizedState),fc(e,t.next.queue,{},jd())}function oc(){return ka(sh)}function sc(){return ds().memoizedState}function cc(){return ds().memoizedState}function lc(e){for(var t=e.return;t!==null;){switch(t.tag){case 24:case 3:var n=jd();e=xo(n);var r=So(t,e,n);r!==null&&(Pd(r,t,n),Co(r,t,n)),t={cache:Ia()},e.payload=t;return}t=t.return}}function uc(e,t,n){var r=jd();n={lane:r,revertLane:0,gesture:null,action:n,hasEagerState:!1,eagerState:null,next:null},mc(e)?hc(t,n):(n=ji(e,t,n,r),n!==null&&(Pd(n,e,r),gc(n,t,r)))}function dc(e,t,n){fc(e,t,n,jd())}function fc(e,t,n,r){var i={lane:r,revertLane:0,gesture:null,action:n,hasEagerState:!1,eagerState:null,next:null};if(mc(e))hc(t,i);else{var a=e.alternate;if(e.lanes===0&&(a===null||a.lanes===0)&&(a=t.lastRenderedReducer,a!==null))try{var o=t.lastRenderedState,s=a(o,n);if(i.hasEagerState=!0,i.eagerState=s,Kr(s,o))return Ai(e,t,i,0),G===null&&ki(),!1}catch{}if(n=ji(e,t,i,r),n!==null)return Pd(n,e,r),gc(n,t,r),!0}return!1}function pc(e,t,n,r){if(r={lane:2,revertLane:Pf(),gesture:null,action:r,hasEagerState:!1,eagerState:null,next:null},mc(e)){if(t)throw Error(i(479))}else t=ji(e,n,r,2),t!==null&&Pd(t,e,2)}function mc(e){var t=e.alternate;return e===L||t!==null&&t===L}function hc(e,t){Yo=Jo=!0;var n=e.pending;n===null?t.next=t:(t.next=n.next,n.next=t),e.pending=t}function gc(e,t,n){if(n&4194048){var r=t.lanes;r&=e.pendingLanes,n|=r,t.lanes=n,Ct(e,n)}}var _c={readContext:ka,use:ms,useCallback:ts,useContext:ts,useEffect:ts,useImperativeHandle:ts,useLayoutEffect:ts,useInsertionEffect:ts,useMemo:ts,useReducer:ts,useRef:ts,useState:ts,useDebugValue:ts,useDeferredValue:ts,useTransition:ts,useSyncExternalStore:ts,useId:ts,useHostTransitionStatus:ts,useFormState:ts,useActionState:ts,useOptimistic:ts,useMemoCache:ts,useCacheRefresh:ts,useEffectEvent:ts},vc={readContext:ka,use:ms,useCallback:function(e,t){return us().memoizedState=[e,t===void 0?null:t],e},useContext:ka,useEffect:Hs,useImperativeHandle:function(e,t,n){n=n==null?null:n.concat([e]),Bs(4194308,4,Js.bind(null,t,e),n)},useLayoutEffect:function(e,t){return Bs(4194308,4,e,t)},useInsertionEffect:function(e,t){Bs(4,2,e,t)},useMemo:function(e,t){var n=us();t=t===void 0?null:t;var r=e();if(Xo){at(!0);try{e()}finally{at(!1)}}return n.memoizedState=[r,t],r},useReducer:function(e,t,n){var r=us();if(n!==void 0){var i=n(t);if(Xo){at(!0);try{n(t)}finally{at(!1)}}}else i=t;return r.memoizedState=r.baseState=i,e={pending:null,lanes:0,dispatch:null,lastRenderedReducer:e,lastRenderedState:i},r.queue=e,e=e.dispatch=uc.bind(null,L,e),[r.memoizedState,e]},useRef:function(e){var t=us();return e={current:e},t.memoizedState=e},useState:function(e){e=ws(e);var t=e.queue,n=dc.bind(null,L,t);return t.dispatch=n,[e.memoizedState,n]},useDebugValue:Xs,useDeferredValue:function(e,t){return $s(us(),e,t)},useTransition:function(){var e=ws(!1);return e=tc.bind(null,L,e.queue,!0,!1),us().memoizedState=e,[!1,e]},useSyncExternalStore:function(e,t,n){var r=L,a=us();if(I){if(n===void 0)throw Error(i(407));n=n()}else{if(n=t(),G===null)throw Error(i(349));q&127||B(r,t,n)}a.memoizedState=n;var o={value:n,getSnapshot:t};return a.queue=o,Hs(xs.bind(null,r,o,e),[e]),r.flags|=2048,Rs(9,{destroy:void 0},bs.bind(null,r,o,n,t),null),n},useId:function(){var e=us(),t=G.identifierPrefix;if(I){var n=na,r=ta;n=(r&~(1<<32-ot(r)-1)).toString(32)+n,t=`_`+t+`R_`+n,n=Zo++,0<n&&(t+=`H`+n.toString(32)),t+=`_`}else n=es++,t=`_`+t+`r_`+n.toString(32)+`_`;return e.memoizedState=t},useHostTransitionStatus:oc,useFormState:Ns,useActionState:Ns,useOptimistic:function(e){var t=us();t.memoizedState=t.baseState=e;var n={pending:null,lanes:0,dispatch:null,lastRenderedReducer:null,lastRenderedState:null};return t.queue=n,t=pc.bind(null,L,!0,n),n.dispatch=t,[e,t]},useMemoCache:hs,useCacheRefresh:function(){return us().memoizedState=lc.bind(null,L)},useEffectEvent:function(e){var t=us(),n={impl:e};return t.memoizedState=n,function(){if(W&2)throw Error(i(440));return n.impl.apply(void 0,arguments)}}},yc={readContext:ka,use:ms,useCallback:Zs,useContext:ka,useEffect:Us,useImperativeHandle:Ys,useInsertionEffect:Ks,useLayoutEffect:qs,useMemo:Qs,useReducer:_s,useRef:zs,useState:function(){return _s(gs)},useDebugValue:Xs,useDeferredValue:function(e,t){return ec(ds(),R.memoizedState,e,t)},useTransition:function(){var e=_s(gs)[0],t=ds().memoizedState;return[typeof e==`boolean`?e:ps(e),t]},useSyncExternalStore:z,useId:sc,useHostTransitionStatus:oc,useFormState:Ps,useActionState:Ps,useOptimistic:function(e,t){return Ts(ds(),R,e,t)},useMemoCache:hs,useCacheRefresh:cc,useEffectEvent:Gs},bc={readContext:ka,use:ms,useCallback:Zs,useContext:ka,useEffect:Us,useImperativeHandle:Ys,useInsertionEffect:Ks,useLayoutEffect:qs,useMemo:Qs,useReducer:ys,useRef:zs,useState:function(){return ys(gs)},useDebugValue:Xs,useDeferredValue:function(e,t){var n=ds();return R===null?$s(n,e,t):ec(n,R.memoizedState,e,t)},useTransition:function(){var e=ys(gs)[0],t=ds().memoizedState;return[typeof e==`boolean`?e:ps(e),t]},useSyncExternalStore:z,useId:sc,useHostTransitionStatus:oc,useFormState:Ls,useActionState:Ls,useOptimistic:function(e,t){var n=ds();return R===null?(n.baseState=e,[e,n.queue.dispatch]):Ts(n,R,e,t)},useMemoCache:hs,useCacheRefresh:cc,useEffectEvent:Gs};function xc(e,t,n,r){t=e.memoizedState,n=n(r,t),n=n==null?t:T({},t,n),e.memoizedState=n,e.lanes===0&&(e.updateQueue.baseState=n)}var Sc={enqueueSetState:function(e,t,n){e=e._reactInternals;var r=jd(),i=xo(r);i.payload=t,n!=null&&(i.callback=n),t=So(e,i,r),t!==null&&(Pd(t,e,r),Co(t,e,r))},enqueueReplaceState:function(e,t,n){e=e._reactInternals;var r=jd(),i=xo(r);i.tag=1,i.payload=t,n!=null&&(i.callback=n),t=So(e,i,r),t!==null&&(Pd(t,e,r),Co(t,e,r))},enqueueForceUpdate:function(e,t){e=e._reactInternals;var n=jd(),r=xo(n);r.tag=2,t!=null&&(r.callback=t),t=So(e,r,n),t!==null&&(Pd(t,e,n),Co(t,e,n))}};function Cc(e,t,n,r,i,a,o){return e=e.stateNode,typeof e.shouldComponentUpdate==`function`?e.shouldComponentUpdate(r,a,o):t.prototype&&t.prototype.isPureReactComponent?!qr(n,r)||!qr(i,a):!0}function wc(e,t,n,r){e=t.state,typeof t.componentWillReceiveProps==`function`&&t.componentWillReceiveProps(n,r),typeof t.UNSAFE_componentWillReceiveProps==`function`&&t.UNSAFE_componentWillReceiveProps(n,r),t.state!==e&&Sc.enqueueReplaceState(t,t.state,null)}function Tc(e,t){var n=t;if(`ref`in t)for(var r in n={},t)r!==`ref`&&(n[r]=t[r]);if(e=e.defaultProps)for(var i in n===t&&(n=T({},n)),e)n[i]===void 0&&(n[i]=e[i]);return n}function Ec(e){Ti(e)}function Dc(e){console.error(e)}function Oc(e){Ti(e)}function kc(e,t){try{var n=e.onUncaughtError;n(t.value,{componentStack:t.stack})}catch(e){setTimeout(function(){throw e})}}function Ac(e,t,n){try{var r=e.onCaughtError;r(n.value,{componentStack:n.stack,errorBoundary:t.tag===1?t.stateNode:null})}catch(e){setTimeout(function(){throw e})}}function jc(e,t,n){return n=xo(n),n.tag=3,n.payload={element:null},n.callback=function(){kc(e,t)},n}function Mc(e){return e=xo(e),e.tag=3,e}function Nc(e,t,n,r){var i=n.type.getDerivedStateFromError;if(typeof i==`function`){var a=r.value;e.payload=function(){return i(a)},e.callback=function(){Ac(t,n,r)}}var o=n.stateNode;o!==null&&typeof o.componentDidCatch==`function`&&(e.callback=function(){Ac(t,n,r),typeof i!=`function`&&(yd===null?yd=new Set([this]):yd.add(this));var e=r.stack;this.componentDidCatch(r.value,{componentStack:e===null?``:e})})}function Pc(e,t,n,r,a){if(n.flags|=32768,typeof r==`object`&&r&&typeof r.then==`function`){if(t=n.alternate,t!==null&&Ea(t,n,a,!0),n=Fo.current,n!==null){switch(n.tag){case 31:case 13:case 19:return Io===null?Kd():n.alternate===null&&Y===0&&(Y=3),n.flags&=-257,n.flags|=65536,n.lanes=a,r===no?n.flags|=16384:(t=n.updateQueue,t===null?n.updateQueue=new Set([r]):t.add(r),mf(e,r,a)),!1;case 22:return n.flags|=65536,r===no?n.flags|=16384:(t=n.updateQueue,t===null?(t={transitions:null,markerInstances:null,retryQueue:new Set([r])},n.updateQueue=t):(n=t.retryQueue,n===null?t.retryQueue=new Set([r]):n.add(r)),mf(e,r,a)),!1}throw Error(i(435,n.tag))}return mf(e,r,a),Kd(),!1}if(I)return t=Fo.current,t===null?(r!==da&&(t=Error(i(423),{cause:r}),va(qi(t,n))),e=e.current.alternate,e.flags|=65536,a&=-a,e.lanes|=a,r=qi(r,n),a=jc(e.stateNode,r,a),wo(e,a),Y!==4&&(Y=2)):(!(t.flags&65536)&&(t.flags|=256),t.flags|=65536,t.lanes=a,r!==da&&(e=Error(i(422),{cause:r}),va(qi(e,n)))),!1;var o=Error(i(520),{cause:r});if(o=qi(o,n),fd===null?fd=[o]:fd.push(o),Y!==4&&(Y=2),t===null)return!0;r=qi(r,n),n=t;do{switch(n.tag){case 3:return n.flags|=65536,e=a&-a,n.lanes|=e,e=jc(n.stateNode,r,e),wo(n,e),!1;case 1:if(t=n.type,o=n.stateNode,!(n.flags&128)&&(typeof t.getDerivedStateFromError==`function`||o!==null&&typeof o.componentDidCatch==`function`&&(yd===null||!yd.has(o))))return n.flags|=65536,a&=-a,n.lanes|=a,a=Mc(a),Nc(a,e,n,r),wo(n,a),!1;break;case 22:if(n.memoizedState!==null)return n.flags|=65536,!1}n=n.return}while(n!==null);return!1}var Fc=Error(i(461)),Ic=!1;function Lc(e,t,n,r){t.child=e===null?_o(t,null,n,r):go(t,e.child,n,r)}function Rc(e,t,n,r,i){n=n.render;var a=t.ref;if(`ref`in r){var o={};for(var s in r)s!==`ref`&&(o[s]=r[s])}else o=r;return Oa(t),r=rs(e,t,n,o,a,i),s=ss(),e!==null&&!Ic?(cs(e,t,i),fl(e,t,i)):(I&&s&&aa(t),t.flags|=1,Lc(e,t,r,i),t.child)}function zc(e,t,n,r,i){if(e===null){var a=n.type;return typeof a==`function`&&!Ri(a)&&a.defaultProps===void 0&&n.compare===null?(t.tag=15,t.type=a,Bc(e,t,a,r,i)):(e=Vi(n.type,null,r,t,t.mode,i),e.ref=t.ref,e.return=t,t.child=e)}if(a=e.child,!pl(e,i)){var o=a.memoizedProps;if(n=n.compare,n=n===null?qr:n,n(o,r)&&e.ref===t.ref)return fl(e,t,i)}return t.flags|=1,e=zi(a,r),e.ref=t.ref,e.return=t,t.child=e}function Bc(e,t,n,r,i){if(e!==null){var a=e.memoizedProps;if(qr(a,r)&&e.ref===t.ref){if(Ic=!1,t.pendingProps=r=a,pl(e,i))e.flags&131072&&(Ic=!0);else return t.lanes=e.lanes,fl(e,t,i)}}return Jc(e,t,n,r,i)}function Vc(e,t,n,r){var i=r.children,a=e===null?null:e.memoizedState;if(e===null&&t.stateNode===null&&(t.stateNode={_visibility:1,_pendingMarkers:null,_retryCache:null,_transitions:null}),r.mode===`hidden`){if(t.flags&128){if(a=a===null?n:a.baseLanes|n,e!==null){for(r=t.child=e.child,i=0;r!==null;)i=i|r.lanes|r.childLanes,r=r.sibling;r=i&~a}else r=0,t.child=null;return Uc(e,t,a,n,r)}if(n&536870912)t.memoizedState={baseLanes:0,cachePool:null},e!==null&&Za(t,a===null?null:a.cachePool),a===null?No():Mo(t,a),zo(t);else return r=t.lanes=536870912,Uc(e,t,a===null?n:a.baseLanes|n,n,r)}else a===null?(e!==null&&Za(t,null),No(),Bo()):(Za(t,a.cachePool),Mo(t,a),Bo(),t.memoizedState=null);return Lc(e,t,i,n),t.child}function Hc(e,t){return e!==null&&e.tag===22||t.stateNode!==null||(t.stateNode={_visibility:1,_pendingMarkers:null,_retryCache:null,_transitions:null}),t.sibling}function Uc(e,t,n,r,i){var a=Xa();return a=a===null?null:{parent:Fa._currentValue,pool:a},t.memoizedState={baseLanes:n,cachePool:a},e!==null&&Za(t,null),No(),zo(t),e!==null&&Ea(e,t,r,!0),t.childLanes=i,null}function Wc(e,t){return t=rl({mode:t.mode,children:t.children},e.mode),t.ref=e.ref,e.child=t,t.return=e,t}function Gc(e,t,n){return go(t,e.child,null,n),e=Wc(t,t.pendingProps),e.flags|=2,Vo(t),t.memoizedState=null,e}function Kc(e,t,n){var r=t.pendingProps,a=!!(t.flags&128);if(t.flags&=-129,e===null){if(I){if(r.mode===`hidden`)return e=Wc(t,r),t.lanes=536870912,e.memoizedState={baseLanes:0,cachePool:null},Hc(null,e);if(Ro(t),(e=F)?(e=am(e,ua),e=e!==null&&e.data===`&`?e:null,e!==null&&(t.memoizedState={dehydrated:e,treeContext:ea===null?null:{id:ta,overflow:na},retryLane:536870912,hydrationErrors:null},n=Wi(e),n.return=t,t.child=n,ca=t,F=null)):e=null,e===null)throw fa(t);return t.lanes=536870912,null}return Wc(t,r)}var o=e.memoizedState;if(o!==null){var s=o.dehydrated;if(Ro(t),a){if(t.flags&256)t.flags&=-257,t=Gc(e,t,n);else if(t.memoizedState!==null)t.child=e.child,t.flags|=128,t=null;else throw Error(i(558))}else if(Ic||Ea(e,t,n,!1),a=(n&e.childLanes)!==0,Ic||a){if(Ao.current===null){if(r=G,r!==null&&(s=wt(r,n),s!==0&&s!==o.retryLane))throw o.retryLane=s,Mi(e,s),Pd(r,e,s),Fc;Kd()}t=Gc(e,t,n)}else e=o.treeContext,F=lm(s.nextSibling),ca=t,I=!0,la=null,ua=!1,e!==null&&sa(t,e),t=Wc(t,r),t.flags|=134221824;return t}return e=zi(e.child,{mode:r.mode,children:r.children}),e.ref=t.ref,t.child=e,e.return=t,e}function qc(e,t){var n=t.ref;if(n===null)e!==null&&e.ref!==null&&(t.flags|=4194816);else{if(typeof n!=`function`&&typeof n!=`object`)throw Error(i(284));(e===null||e.ref!==n)&&(t.flags|=4194816)}}function Jc(e,t,n,r,i){return Oa(t),n=rs(e,t,n,r,void 0,i),r=ss(),e!==null&&!Ic?(cs(e,t,i),fl(e,t,i)):(I&&r&&aa(t),t.flags|=1,Lc(e,t,n,i),t.child)}function Yc(e,t,n,r,i,a){return Oa(t),t.updateQueue=null,n=as(t,r,n,i),is(e),r=ss(),e!==null&&!Ic?(cs(e,t,a),fl(e,t,a)):(I&&r&&aa(t),t.flags|=1,Lc(e,t,n,a),t.child)}function Xc(e,t,n,r,i){if(Oa(t),t.stateNode===null){var a=Fi,o=n.contextType;typeof o==`object`&&o&&(a=ka(o)),a=new n(r,a),t.memoizedState=a.state!==null&&a.state!==void 0?a.state:null,a.updater=Sc,t.stateNode=a,a._reactInternals=t,a=t.stateNode,a.props=r,a.state=t.memoizedState,a.refs={},yo(t),o=n.contextType,a.context=typeof o==`object`&&o?ka(o):Fi,a.state=t.memoizedState,o=n.getDerivedStateFromProps,typeof o==`function`&&(xc(t,n,o,r),a.state=t.memoizedState),typeof n.getDerivedStateFromProps==`function`||typeof a.getSnapshotBeforeUpdate==`function`||typeof a.UNSAFE_componentWillMount!=`function`&&typeof a.componentWillMount!=`function`||(o=a.state,typeof a.componentWillMount==`function`&&a.componentWillMount(),typeof a.UNSAFE_componentWillMount==`function`&&a.UNSAFE_componentWillMount(),o!==a.state&&Sc.enqueueReplaceState(a,a.state,null),Do(t,r,a,i),Eo(),a.state=t.memoizedState),typeof a.componentDidMount==`function`&&(t.flags|=4194308),r=!0}else if(e===null){a=t.stateNode;var s=t.memoizedProps,c=Tc(n,s);a.props=c;var l=a.context,u=n.contextType;o=Fi,typeof u==`object`&&u&&(o=ka(u));var d=n.getDerivedStateFromProps;u=typeof d==`function`||typeof a.getSnapshotBeforeUpdate==`function`,s=t.pendingProps!==s,u||typeof a.UNSAFE_componentWillReceiveProps!=`function`&&typeof a.componentWillReceiveProps!=`function`||(s||l!==o)&&wc(t,a,r,o),vo=!1;var f=t.memoizedState;a.state=f,Do(t,r,a,i),Eo(),l=t.memoizedState,s||f!==l||vo?(typeof d==`function`&&(xc(t,n,d,r),l=t.memoizedState),(c=vo||Cc(t,n,c,r,f,l,o))?(u||typeof a.UNSAFE_componentWillMount!=`function`&&typeof a.componentWillMount!=`function`||(typeof a.componentWillMount==`function`&&a.componentWillMount(),typeof a.UNSAFE_componentWillMount==`function`&&a.UNSAFE_componentWillMount()),typeof a.componentDidMount==`function`&&(t.flags|=4194308)):(typeof a.componentDidMount==`function`&&(t.flags|=4194308),t.memoizedProps=r,t.memoizedState=l),a.props=r,a.state=l,a.context=o,r=c):(typeof a.componentDidMount==`function`&&(t.flags|=4194308),r=!1)}else{a=t.stateNode,bo(e,t),o=t.memoizedProps,u=Tc(n,o),a.props=u,d=t.pendingProps,f=a.context,l=n.contextType,c=Fi,typeof l==`object`&&l&&(c=ka(l)),s=n.getDerivedStateFromProps,(l=typeof s==`function`||typeof a.getSnapshotBeforeUpdate==`function`)||typeof a.UNSAFE_componentWillReceiveProps!=`function`&&typeof a.componentWillReceiveProps!=`function`||(o!==d||f!==c)&&wc(t,a,r,c),vo=!1,f=t.memoizedState,a.state=f,Do(t,r,a,i),Eo();var p=t.memoizedState;o!==d||f!==p||vo||e!==null&&e.dependencies!==null&&Da(e.dependencies)?(typeof s==`function`&&(xc(t,n,s,r),p=t.memoizedState),(u=vo||Cc(t,n,u,r,f,p,c)||e!==null&&e.dependencies!==null&&Da(e.dependencies))?(l||typeof a.UNSAFE_componentWillUpdate!=`function`&&typeof a.componentWillUpdate!=`function`||(typeof a.componentWillUpdate==`function`&&a.componentWillUpdate(r,p,c),typeof a.UNSAFE_componentWillUpdate==`function`&&a.UNSAFE_componentWillUpdate(r,p,c)),typeof a.componentDidUpdate==`function`&&(t.flags|=4),typeof a.getSnapshotBeforeUpdate==`function`&&(t.flags|=1024)):(typeof a.componentDidUpdate!=`function`||o===e.memoizedProps&&f===e.memoizedState||(t.flags|=4),typeof a.getSnapshotBeforeUpdate!=`function`||o===e.memoizedProps&&f===e.memoizedState||(t.flags|=1024),t.memoizedProps=r,t.memoizedState=p),a.props=r,a.state=p,a.context=c,r=u):(typeof a.componentDidUpdate!=`function`||o===e.memoizedProps&&f===e.memoizedState||(t.flags|=4),typeof a.getSnapshotBeforeUpdate!=`function`||o===e.memoizedProps&&f===e.memoizedState||(t.flags|=1024),r=!1)}return a=r,qc(e,t),r=!!(t.flags&128),a||r?(a=t.stateNode,n=r&&typeof n.getDerivedStateFromError!=`function`?null:a.render(),t.flags|=1,e!==null&&r?(t.child=go(t,e.child,null,i),t.child=go(t,null,n,i)):Lc(e,t,n,i),t.memoizedState=a.state,e=t.child):e=fl(e,t,i),e}function Zc(e,t,n,r){return ga(),t.flags|=256,Lc(e,t,n,r),t.child}var Qc={dehydrated:null,treeContext:null,retryLane:0,hydrationErrors:null};function $c(e){return{baseLanes:e,cachePool:Qa()}}function el(e,t,n){return e=e===null?0:e.childLanes&~n,t&&(e|=ud),e}function tl(e,t,n){var r=t.pendingProps,i=!1,a=!!(t.flags&128),o;if((o=a)||(o=e!==null&&e.memoizedState===null?!1:!!(Ho.current&2)),o&&(i=!0,t.flags&=-129),o=!!(t.flags&32),t.flags&=-33,e===null){if(I){if(i?Lo(t):Bo(),(e=F)?(e=am(e,ua),e=e!==null&&e.data!==`&`?e:null,e!==null&&(t.memoizedState={dehydrated:e,treeContext:ea===null?null:{id:ta,overflow:na},retryLane:536870912,hydrationErrors:null},n=Wi(e),n.return=t,t.child=n,ca=t,F=null)):e=null,e===null)throw fa(t);return t.lanes=sm(e)?32:536870912,null}return a=r.children,r=r.fallback,i?(Bo(),i=t.mode,a=rl({mode:`hidden`,children:a},i),r=Hi(r,i,n,null),a.return=t,r.return=t,a.sibling=r,t.child=a,r=t.child,r.memoizedState=$c(n),r.childLanes=el(e,o,n),t.memoizedState=Qc,Hc(null,r)):(Lo(t),nl(t,a))}var s=e.memoizedState;if(s!==null){var c=s.dehydrated;if(c!==null)return al(e,t,a,o,r,c,s,n)}return i?(Bo(),i=r.fallback,a=t.mode,s=e.child,c=s.sibling,r=zi(s,{mode:`hidden`,children:r.children}),r.subtreeFlags=s.subtreeFlags&1206910976,c===null?(i=Hi(i,a,n,null),i.flags|=2):i=zi(c,i),i.return=t,r.return=t,r.sibling=i,t.child=r,Hc(null,r),r=t.child,i=e.child.memoizedState,i===null?i=$c(n):(a=i.cachePool,a===null?a=Qa():(s=Fa._currentValue,a=a.parent===s?a:{parent:s,pool:s}),i={baseLanes:i.baseLanes|n,cachePool:a}),r.memoizedState=i,r.childLanes=el(e,o,n),t.memoizedState=Qc,Hc(e.child,r)):(Lo(t),n=e.child,e=n.sibling,n=zi(n,{mode:`visible`,children:r.children}),n.return=t,n.sibling=null,e!==null&&(o=t.deletions,o===null?(t.deletions=[e],t.flags|=16):o.push(e)),t.child=n,t.memoizedState=null,n)}function nl(e,t){return t=rl({mode:`visible`,children:t},e.mode),t.return=e,e.child=t}function rl(e,t){return e=Li(22,e,null,t),e.lanes=0,e}function il(e,t,n){return go(t,e.child,null,n),e=nl(t,t.pendingProps.children),e.flags|=2,t.memoizedState=null,e}function al(e,t,n,r,a,o,s,c){if(n)return t.flags&256?(Lo(t),t.flags&=-257,il(e,t,c)):t.memoizedState===null?(Bo(),o=a.fallback,s=t.mode,a=rl({mode:`visible`,children:a.children},s),o=Hi(o,s,c,null),o.flags|=2,a.return=t,o.return=t,a.sibling=o,t.child=a,go(t,e.child,null,c),a=t.child,a.memoizedState=$c(c),a.childLanes=el(e,r,c),t.memoizedState=Qc,Hc(null,a)):(Bo(),t.child=e.child,t.flags|=128,null);if(Lo(t),sm(o)){if(r=o.nextSibling&&o.nextSibling.dataset,r)var l=r.dgst;return r=l,r!==``&&(a=Error(i(419)),a.stack=``,a.digest=r,va({value:a,source:null,stack:null})),il(e,t,c)}if(Ic||Ea(e,t,c,!1),r=(c&e.childLanes)!==0,Ic||r){if(Ao.current!==null)return il(e,t,c);if(r=G,r!==null&&(a=wt(r,c),a!==0&&a!==s.retryLane))throw s.retryLane=a,Mi(e,a),Pd(r,e,a),Fc;return om(o)||Kd(),il(e,t,c)}return om(o)?(t.flags|=192,t.child=e.child,null):(e=s.treeContext,F=lm(o.nextSibling),ca=t,I=!0,la=null,ua=!1,e!==null&&sa(t,e),t=nl(t,a.children),t.flags|=134221824,t)}function ol(e,t,n){e.lanes|=t;var r=e.alternate;r!==null&&(r.lanes|=t),wa(e.return,t,n)}function sl(e){for(var t=null;e!==null;){var n=e.alternate;n!==null&&Go(n)===null&&(t=e),e=e.sibling}return t}function cl(e,t,n,r,i,a){var o=e.memoizedState;o===null?e.memoizedState={isBackwards:t,rendering:null,renderingStartTime:0,last:r,tail:n,tailMode:i,treeForkCount:a}:(o.isBackwards=t,o.rendering=null,o.renderingStartTime=0,o.last=r,o.tail=n,o.tailMode=i,o.treeForkCount=a)}function ll(e){var t=e.child;for(e.child=null;t!==null;){var n=t.sibling;t.sibling=e.child,e.child=t,t=n}}function ul(e,t,n){var r=t.pendingProps,i=r.revealOrder,a=r.tail;r=r.children;var o=Ho.current;if(t.flags&128)return Uo(t,o),null;var s=!!(o&2);if(s?(o=o&1|2,t.flags|=128):o&=1,Uo(t,o),i===`backwards`&&e!==null?(ll(e),Lc(e,t,r,n),ll(e)):Lc(e,t,r,n),r=I?Zi:0,!s&&e!==null&&e.flags&128)a:for(e=t.child;e!==null;){if(e.tag===13)e.memoizedState!==null&&ol(e,n,t);else if(e.tag===19)ol(e,n,t);else if(e.child!==null){e.child.return=e,e=e.child;continue}if(e===t)break a;for(;e.sibling===null;){if(e.return===null||e.return===t)break a;e=e.return}e.sibling.return=e.return,e=e.sibling}switch(i){case`backwards`:n=sl(t.child),n===null?(i=t.child,t.child=null):(i=n.sibling,n.sibling=null,ll(t)),cl(t,!0,i,null,a,r);break;case`unstable_legacy-backwards`:for(n=null,i=t.child,t.child=null;i!==null;){if(e=i.alternate,e!==null&&Go(e)===null){t.child=i;break}e=i.sibling,i.sibling=n,n=i,i=e}cl(t,!0,n,null,a,r);break;case`together`:cl(t,!1,null,null,void 0,r);break;case`independent`:t.memoizedState=null;break;default:n=sl(t.child),n===null?(i=t.child,t.child=null):(i=n.sibling,n.sibling=null),cl(t,!1,i,n,a,r)}return t.child}function dl(e,t,n){var r=t.pendingProps;return Sa(t,t.type,r.value),Lc(e,t,r.children,n),t.child}function fl(e,t,n){if(e!==null&&(t.dependencies=e.dependencies),sd|=t.lanes,(n&t.childLanes)===0){if(e!==null){if(Ea(e,t,n,!1),(n&t.childLanes)===0)return null}else return null}if(e!==null&&t.child!==e.child)throw Error(i(153));if(t.child!==null){for(e=t.child,n=zi(e,e.pendingProps),t.child=n,n.return=t;e.sibling!==null;)e=e.sibling,n=n.sibling=zi(e,e.pendingProps),n.return=t;n.sibling=null}return t.child}function pl(e,t){return(e.lanes&t)!==0||(e=e.dependencies,!!(e!==null&&Da(e)))}function ml(e,t,n){switch(t.tag){case 3:Me(t,t.stateNode.containerInfo),Sa(t,Fa,e.memoizedState.cache),ga();break;case 27:case 5:Pe(t);break;case 4:Me(t,t.stateNode.containerInfo);break;case 10:Sa(t,t.type,t.memoizedProps.value);break;case 31:if(t.memoizedState!==null)return t.flags|=128,Ro(t),null;break;case 13:var r=t.memoizedState;if(r!==null){if(r.dehydrated!==null)return Lo(t),t.flags|=128,null;r=Ea(e,t,n,!1);var i=t.child.childLanes;return r||(n&i)!==0?tl(e,t,n):(Lo(t),e=fl(e,t,n),e===null?null:e.sibling)}Lo(t);break;case 19:if(t.flags&128)return ul(e,t,n);if(i=!!(e.flags&128),r=(n&t.childLanes)!==0,r||=(Ea(e,t,n,!1),(n&t.childLanes)!==0),i){if(r)return ul(e,t,n);t.flags|=128}if(i=t.memoizedState,i!==null&&(i.rendering=null,i.tail=null,i.lastEffect=null),Uo(t,Ho.current),r)break;return null;case 22:return t.lanes=0,Vc(e,t,n,t.pendingProps);case 24:Sa(t,Fa,e.memoizedState.cache)}return fl(e,t,n)}function hl(e,t,n){if(e!==null){if(e.memoizedProps!==t.pendingProps)Ic=!0;else{if(!pl(e,n)&&!(t.flags&128))return Ic=!1,ml(e,t,n);Ic=!!(e.flags&131072)}}else Ic=!1,I&&t.flags&1048576&&ia(t,Zi,t.index);switch(t.lanes=0,t.tag){case 16:a:{var r=t.pendingProps;if(e=ao(t.elementType),t.type=e,typeof e==`function`)Ri(e)?(r=Tc(e,r),t.tag=1,t=Xc(null,t,e,r,n)):(t.tag=0,t=Jc(null,t,e,r,n));else{if(e!=null){var a=e.$$typeof;if(a===ue){t.tag=11,t=Rc(null,t,e,r,n);break a}if(a===fe){t.tag=14,t=zc(null,t,e,r,n);break a}if(a===le){t.tag=10,t.type=e,t=dl(null,t,n);break a}}throw t=Se(e)||e,Error(i(306,t,``))}}return t;case 0:return Jc(e,t,t.type,t.pendingProps,n);case 1:return r=t.type,a=Tc(r,t.pendingProps),Xc(e,t,r,a,n);case 3:a:{if(Me(t,t.stateNode.containerInfo),e===null)throw Error(i(387));r=t.pendingProps;var o=t.memoizedState;a=o.element,bo(e,t),Do(t,r,null,n);var s=t.memoizedState;if(r=s.cache,Sa(t,Fa,r),r!==o.cache&&Ta(t,[Fa],n,!0),Eo(),r=s.element,o.isDehydrated){if(o={element:r,isDehydrated:!1,cache:s.cache},t.updateQueue.baseState=o,t.memoizedState=o,t.flags&256){t=Zc(e,t,r,n);break a}if(r!==a){a=qi(Error(i(424)),t),va(a),t=Zc(e,t,r,n);break a}switch(e=t.stateNode.containerInfo,e.nodeType){case 9:e=e.body;break;default:e=e.nodeName===`HTML`?e.ownerDocument.body:e}for(F=lm(e.firstChild),ca=t,I=!0,la=null,ua=!0,n=_o(t,null,r,n),t.child=n;n;)n.flags=n.flags&-3|134221824,n=n.sibling}else{if(ga(),r===a){t=fl(e,t,n);break a}Lc(e,t,r,n)}t=t.child}return t;case 26:return qc(e,t),e===null?(n=Nm(t.type,null,t.pendingProps,null))?t.memoizedState=n:I||(t.stateNode=fp(t.type,t.pendingProps,Ae.current,t)):t.memoizedState=Nm(t.type,e.memoizedProps,t.pendingProps,e.memoizedState),null;case 27:return Pe(t),e===null&&I&&(r=t.stateNode=hm(t.type,t.pendingProps,Ae.current),ca=t,ua=!0,a=F,Sp(t.type)?(um=a,F=lm(r.firstChild)):F=a),Lc(e,t,t.pendingProps.children,n),qc(e,t),e===null&&(t.flags|=4194304),t.child;case 5:return e===null&&I&&((a=r=F)&&(r=rm(r,t.type,t.pendingProps,ua),r===null?a=!1:(t.stateNode=r,ca=t,F=lm(r.firstChild),ua=!1,a=!0)),a||fa(t)),Pe(t),a=t.type,o=t.pendingProps,s=e===null?null:e.memoizedProps,r=o.children,pp(a,o)?r=null:s!==null&&pp(a,s)&&(t.flags|=32),t.memoizedState!==null&&(a=rs(e,t,os,null,null,n),sh._currentValue=a),qc(e,t),Lc(e,t,r,n),t.child;case 6:return e===null&&I&&((e=n=F)&&(n=im(n,t.pendingProps,ua),n===null?e=!1:(t.stateNode=n,ca=t,F=null,e=!0)),e||fa(t)),null;case 13:return tl(e,t,n);case 4:return Me(t,t.stateNode.containerInfo),r=t.pendingProps,e===null?t.child=go(t,null,r,n):Lc(e,t,r,n),t.child;case 11:return Rc(e,t,t.type,t.pendingProps,n);case 7:return r=t.pendingProps,qc(e,t),Lc(e,t,r,n),t.child;case 8:return Lc(e,t,t.pendingProps.children,n),t.child;case 12:return Lc(e,t,t.pendingProps.children,n),t.child;case 10:return dl(e,t,n);case 9:return a=t.type._context,r=t.pendingProps.children,Oa(t),a=ka(a),r=r(a),t.flags|=1,Lc(e,t,r,n),t.child;case 14:return zc(e,t,t.type,t.pendingProps,n);case 15:return Bc(e,t,t.type,t.pendingProps,n);case 19:return ul(e,t,n);case 31:return Kc(e,t,n);case 22:return Vc(e,t,n,t.pendingProps);case 24:return Oa(t),r=ka(Fa),e===null?(a=Xa(),a===null&&(a=G,o=Ia(),a.pooledCache=o,o.refCount++,o!==null&&(a.pooledCacheLanes|=n),a=o),t.memoizedState={parent:r,cache:a},yo(t),Sa(t,Fa,a)):((e.lanes&n)!==0&&(bo(e,t),Do(t,null,null,n),Eo()),a=e.memoizedState,o=t.memoizedState,a.parent===r?(r=o.cache,Sa(t,Fa,r),r!==a.cache&&Ta(t,[Fa],n,!0)):(a={parent:r,cache:r},t.memoizedState=a,t.lanes===0&&(t.memoizedState=t.updateQueue.baseState=a),Sa(t,Fa,r))),Lc(e,t,t.pendingProps.children,n),t.child;case 30:return t.stateNode===null&&(t.stateNode={autoName:null,paired:null,clones:null,ref:null}),r=t.pendingProps,r.name!=null&&r.name!==`auto`?t.flags|=e===null?18882560:18874368:I&&aa(t),e!==null&&e.memoizedProps.name!==r.name?t.flags|=4194816:qc(e,t),Lc(e,t,r.children,n),t.child;case 29:throw t.pendingProps}throw Error(i(156,t.tag))}function gl(e){e.flags|=4}function _l(e,t,n,r,i){var a;if((a=!!(e.mode&32))&&(a=n===null?Jm(t,r):Jm(t,r)&&(r.src!==n.src||r.srcSet!==n.srcSet)),a){if(e.flags|=16777216,(i&335544128)===i){if(e.stateNode.complete)e.flags|=8192;else if(Ud())e.flags|=8192;else throw oo=no,eo}}else e.flags&=-16777217}function vl(e,t){if(t.type!==`stylesheet`||t.state.loading&4)e.flags&=-16777217;else if(e.flags|=16777216,!Ym(t)){if(Ud())e.flags|=8192;else throw oo=no,eo}}function yl(e,t){t!==null&&(e.flags|=4),e.flags&16384&&(t=e.tag===22?536870912:vt(),e.lanes|=t,dd|=t)}function bl(e,t){if(!I)switch(e.tailMode){case`visible`:break;case`collapsed`:for(var n=e.tail,r=null;n!==null;)n.alternate!==null&&(r=n),n=n.sibling;r===null?t||e.tail===null?e.tail=null:e.tail.sibling=null:r.sibling=null;break;default:for(t=e.tail,n=null;t!==null;)t.alternate!==null&&(n=t),t=t.sibling;n===null?e.tail=null:n.sibling=null}}function V(e){var t=e.alternate!==null&&e.alternate.child===e.child,n=0,r=0;if(t)for(var i=e.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags&1206910976,r|=i.flags&1206910976,i.return=e,i=i.sibling;else for(i=e.child;i!==null;)n|=i.lanes|i.childLanes,r|=i.subtreeFlags,r|=i.flags,i.return=e,i=i.sibling;return e.subtreeFlags|=r,e.childLanes=n,t}function xl(e,t,n){var r=t.pendingProps;switch(oa(t),t.tag){case 16:case 15:case 0:case 11:case 7:case 8:case 12:case 9:case 14:return V(t),null;case 1:return V(t),null;case 3:return n=t.stateNode,r=null,e!==null&&(r=e.memoizedState.cache),t.memoizedState.cache!==r&&(t.flags|=2048),Ca(Fa),Ne(),n.pendingContext&&(n.context=n.pendingContext,n.pendingContext=null),(e===null||e.child===null)&&(ha(t)?gl(t):e===null||e.memoizedState.isDehydrated&&!(t.flags&256)||(t.flags|=1024,_a())),V(t),null;case 26:var a=t.type,o=t.memoizedState;return e===null?(gl(t),o===null?(V(t),_l(t,a,null,r,n)):(V(t),vl(t,o))):o?o===e.memoizedState?(V(t),t.flags&=-16777217):(gl(t),V(t),vl(t,o)):(e=e.memoizedProps,e!==r&&gl(t),V(t),_l(t,a,e,r,n)),null;case 27:if(Fe(t),n=Ae.current,a=t.type,e!==null&&t.stateNode!=null)e.memoizedProps!==r&&gl(t);else{if(!r){if(t.stateNode===null)throw Error(i(166));return V(t),t.subtreeFlags&=-33554433,null}e=Oe.current,ha(t)?pa(t,e):(e=hm(a,r,n),t.stateNode=e,gl(t))}return V(t),t.subtreeFlags&=-33554433,null;case 5:if(Fe(t),a=t.type,e!==null&&t.stateNode!=null)e.memoizedProps!==r&&gl(t);else{if(!r){if(t.stateNode===null)throw Error(i(166));return V(t),t.subtreeFlags&=-33554433,null}if(o=Oe.current,ha(t))pa(t,o);else{var s=lp(Ae.current);switch(o){case 1:o=s.createElementNS(`http://www.w3.org/2000/svg`,a);break;case 2:o=s.createElementNS(`http://www.w3.org/1998/Math/MathML`,a);break;default:switch(a){case`svg`:o=s.createElementNS(`http://www.w3.org/2000/svg`,a);break;case`math`:o=s.createElementNS(`http://www.w3.org/1998/Math/MathML`,a);break;case`script`:o=s.createElement(`div`),o.innerHTML=`<script><\/script>`,o=o.removeChild(o.firstChild);break;case`select`:o=typeof r.is==`string`?s.createElement(`select`,{is:r.is}):s.createElement(`select`),r.multiple?o.multiple=!0:r.size&&(o.size=r.size);break;default:o=typeof r.is==`string`?s.createElement(a,{is:r.is}):s.createElement(a)}}o[At]=t,o[jt]=r;a:for(s=t.child;s!==null;){if(s.tag===5||s.tag===6)o.appendChild(s.stateNode);else if(s.tag!==4&&s.tag!==27&&s.child!==null){s.child.return=s,s=s.child;continue}if(s===t)break a;for(;s.sibling===null;){if(s.return===null||s.return===t)break a;s=s.return}s.sibling.return=s.return,s=s.sibling}t.stateNode=o;a:switch(np(o,a,r),a){case`button`:case`input`:case`select`:case`textarea`:r=!!r.autoFocus;break a;case`img`:r=!0;break a;default:r=!1}r&&gl(t)}}return V(t),t.subtreeFlags&=-33554433,_l(t,t.type,e===null?null:e.memoizedProps,t.pendingProps,n),null;case 6:if(e&&t.stateNode!=null)e.memoizedProps!==r&&gl(t);else{if(typeof r!=`string`&&t.stateNode===null)throw Error(i(166));if(e=Ae.current,ha(t)){if(e=t.stateNode,n=t.memoizedProps,r=null,a=ca,a!==null)switch(a.tag){case 27:case 5:r=a.memoizedProps}e[At]=t,e=!!(e.nodeValue===n||r!==null&&!0===r.suppressHydrationWarning||ep(e.nodeValue,n)),e||fa(t,!0)}else e=lp(e).createTextNode(r),e[At]=t,t.stateNode=e}return V(t),null;case 31:if(n=t.memoizedState,e===null||e.memoizedState!==null){if(r=ha(t),n!==null){if(e===null){if(!r)throw Error(i(318));if(e=t.memoizedState,e=e===null?null:e.dehydrated,!e)throw Error(i(557));e[At]=t}else ga(),!(t.flags&128)&&(t.memoizedState=null),t.flags|=4;V(t),e=!1}else n=_a(),e!==null&&e.memoizedState!==null&&(e.memoizedState.hydrationErrors=n),e=!0;if(!e)return t.flags&256?(Vo(t),t):(Vo(t),null);if(t.flags&128)throw Error(i(558))}return V(t),null;case 13:if(r=t.memoizedState,e===null||e.memoizedState!==null&&e.memoizedState.dehydrated!==null){if(a=ha(t),r!==null&&r.dehydrated!==null){if(e===null){if(!a)throw Error(i(318));if(a=t.memoizedState,a=a===null?null:a.dehydrated,!a)throw Error(i(317));a[At]=t}else ga(),!(t.flags&128)&&(t.memoizedState=null),t.flags|=4;V(t),a=!1}else a=_a(),e!==null&&e.memoizedState!==null&&(e.memoizedState.hydrationErrors=a),a=!0;if(!a)return t.flags&256?(Vo(t),t):(Vo(t),null)}return Vo(t),t.flags&128?(t.lanes=n,t):(n=r!==null,e=e!==null&&e.memoizedState!==null,n&&(r=t.child,a=null,r.alternate!==null&&r.alternate.memoizedState!==null&&r.alternate.memoizedState.cachePool!==null&&(a=r.alternate.memoizedState.cachePool.pool),o=null,r.memoizedState!==null&&r.memoizedState.cachePool!==null&&(o=r.memoizedState.cachePool.pool),o!==a&&(r.flags|=2048)),n!==e&&n&&(t.child.flags|=8192),yl(t,t.updateQueue),V(t),null);case 4:return Ne(),e===null&&Wf(t.stateNode.containerInfo),t.flags|=67108864,V(t),null;case 10:return Ca(t.type),V(t),null;case 19:if(Wo(t),r=t.memoizedState,r===null)return V(t),null;if(a=!!(t.flags&128),o=r.rendering,o===null){if(a)bl(r,!1);else{if(Y!==0||e!==null&&e.flags&128)for(e=t.child;e!==null;){if(o=Go(e),o!==null){for(t.flags|=128,bl(r,!1),e=o.updateQueue,t.updateQueue=e,yl(t,e),t.subtreeFlags=0,e=n,n=t.child;n!==null;)Bi(n,e),n=n.sibling;return Uo(t,Ho.current&1|2),I&&ra(t,r.treeForkCount),t.child}e=e.sibling}r.tail!==null&&Je()>_d&&(t.flags|=128,a=!0,bl(r,!1),t.lanes=4194304)}}else{if(!a){if(e=Go(o),e!==null){if(t.flags|=128,a=!0,e=e.updateQueue,t.updateQueue=e,yl(t,e),bl(r,!0),r.tail===null&&r.tailMode!==`collapsed`&&r.tailMode!==`visible`&&!o.alternate&&!I)return V(t),null}else 2*Je()-r.renderingStartTime>_d&&n!==536870912&&(t.flags|=128,a=!0,bl(r,!1),t.lanes=4194304)}r.isBackwards?(o.sibling=t.child,t.child=o):(e=r.last,e===null?t.child=o:e.sibling=o,r.last=o)}if(r.tail!==null){e=r.tail;a:{for(n=e;n!==null;){if(n.alternate!==null){n=!1;break a}n=n.sibling}n=!0}return r.rendering=e,r.tail=e.sibling,r.renderingStartTime=Je(),e.sibling=null,o=Ho.current,o=a?o&1|2:o&1,r.tailMode===`visible`||r.tailMode===`collapsed`||!n||I?Uo(t,o):(n=o,A(Fo,t),A(Ho,n),Io===null&&(Io=t)),I&&ra(t,r.treeForkCount),e}return V(t),null;case 22:case 23:return Vo(t),Po(),r=t.memoizedState!==null,e===null?r&&(t.flags|=8192):e.memoizedState!==null!==r&&(t.flags|=8192),r?n&536870912&&!(t.flags&128)&&(V(t),t.subtreeFlags&6&&(t.flags|=8192)):V(t),n=t.updateQueue,n!==null&&yl(t,n.retryQueue),n=null,e!==null&&e.memoizedState!==null&&e.memoizedState.cachePool!==null&&(n=e.memoizedState.cachePool.pool),r=null,t.memoizedState!==null&&t.memoizedState.cachePool!==null&&(r=t.memoizedState.cachePool.pool),r!==n&&(t.flags|=2048),e!==null&&k(Ya),null;case 24:return n=null,e!==null&&(n=e.memoizedState.cache),t.memoizedState.cache!==n&&(t.flags|=2048),Ca(Fa),V(t),null;case 25:return null;case 30:return t.flags|=33554432,V(t),null}throw Error(i(156,t.tag))}function Sl(e,t){switch(oa(t),t.tag){case 1:return e=t.flags,e&65536?(t.flags=e&-65537|128,t):null;case 3:return Ca(Fa),Ne(),e=t.flags,e&65536&&!(e&128)?(t.flags=e&-65537|128,t):null;case 26:case 27:case 5:return Fe(t),null;case 31:if(t.memoizedState!==null){if(Vo(t),t.alternate===null)throw Error(i(340));ga()}return e=t.flags,e&65536?(t.flags=e&-65537|128,t):null;case 13:if(Vo(t),e=t.memoizedState,e!==null&&e.dehydrated!==null){if(t.alternate===null)throw Error(i(340));ga()}return e=t.flags,e&65536?(t.flags=e&-65537|128,t):null;case 19:return Wo(t),e=t.flags,e&65536?(t.flags=e&-65537|128,e=t.memoizedState,e!==null&&(e.rendering=null,e.tail=null),t.flags|=4,t):null;case 4:return Ne(),null;case 10:return Ca(t.type),null;case 22:case 23:return Vo(t),Po(),e!==null&&k(Ya),e=t.flags,e&65536?(t.flags=e&-65537|128,t):null;case 24:return Ca(Fa),null;case 25:return null;default:return null}}function Cl(e,t){switch(oa(t),t.tag){case 3:Ca(Fa),Ne();break;case 26:case 27:case 5:Fe(t);break;case 4:Ne();break;case 31:t.memoizedState!==null&&Vo(t);break;case 13:Vo(t);break;case 19:Wo(t);break;case 10:Ca(t.type);break;case 22:case 23:Vo(t),Po(),e!==null&&k(Ya);break;case 24:Ca(Fa)}}function wl(e,t){try{var n=t.updateQueue,r=n===null?null:n.lastEffect;if(r!==null){var i=r.next;n=i;do{if((n.tag&e)===e){r=void 0;var a=n.create,o=n.inst;r=a(),o.destroy=r}n=n.next}while(n!==i)}}catch(e){Z(t,t.return,e)}}function Tl(e,t,n){try{var r=t.updateQueue,i=r===null?null:r.lastEffect;if(i!==null){var a=i.next;r=a;do{if((r.tag&e)===e){var o=r.inst,s=o.destroy;if(s!==void 0){o.destroy=void 0,i=t;var c=n,l=s;try{l()}catch(e){Z(i,c,e)}}}r=r.next}while(r!==a)}}catch(e){Z(t,t.return,e)}}function El(e){var t=e.updateQueue;if(t!==null){var n=e.stateNode;try{ko(t,n)}catch(t){Z(e,e.return,t)}}}function Dl(e,t,n){n.props=Tc(e.type,e.memoizedProps),n.state=e.memoizedState;try{n.componentWillUnmount()}catch(n){Z(e,t,n)}}function Ol(e,t){try{var n=e.ref;if(n!==null){switch(e.tag){case 26:case 27:case 5:var r=e.stateNode;break;case 30:var i=e.stateNode,a=Si(e.memoizedProps,i);(i.ref===null||i.ref.name!==a)&&(i.ref=Pp(a)),r=i.ref;break;case 7:if(e.stateNode===null){var o=new Fp(e);h(e.child,!1,Qp,o,void 0,void 0),e.stateNode=o}r=e.stateNode;break;default:r=e.stateNode}typeof n==`function`?e.refCleanup=n(r):n.current=r}}catch(n){Z(e,t,n)}}function kl(e,t){var n=e.ref,r=e.refCleanup;if(n!==null){if(typeof r==`function`)try{r()}catch(n){Z(e,t,n)}finally{e.refCleanup=null,e=e.alternate,e!=null&&(e.refCleanup=null)}else if(typeof n==`function`)try{n(null)}catch(n){Z(e,t,n)}else n.current=null}}function Al(e,t){if((e.tag===5||e.tag===27||e.tag===6)&&e.alternate===null&&t!==null)for(var n=0;n<t.length;n++)em(e.stateNode,t[n])}function jl(e){for(var t=e.return;t!==null&&(Pl(t)&&em(e.stateNode,t.stateNode),!Nl(t));)t=t.return}function Ml(e){for(var t=e.return;t!==null&&(Pl(t)&&tm(e.stateNode,t.stateNode),!Nl(t));)t=t.return}function Nl(e){return e.tag===5||e.tag===3||e.tag===27}function Pl(e){return e&&e.tag===7&&e.stateNode!==null}function Fl(e){var t=e.type,n=e.memoizedProps,r=e.stateNode;try{a:switch(t){case`button`:case`input`:case`select`:case`textarea`:n.autoFocus&&r.focus();break a;case`img`:n.src?r.src=n.src:n.srcSet&&(r.srcset=n.srcSet)}}catch(t){Z(e,e.return,t)}}function Il(e,t,n){try{var r=e.stateNode;ip(r,e.type,n,t),r[jt]=t}catch(t){Z(e,e.return,t)}}function Ll(e){return e.tag===5||e.tag===3||e.tag===26||e.tag===27&&Sp(e.type)||e.tag===4}function Rl(e){a:for(;;){for(;e.sibling===null;){if(e.return===null||Ll(e.return))return null;e=e.return}for(e.sibling.return=e.return,e=e.sibling;e.tag!==5&&e.tag!==6&&e.tag!==18;){if(e.tag===27&&Sp(e.type)||e.flags&2||e.child===null||e.tag===4)continue a;e.child.return=e,e=e.child}if(!(e.flags&2))return e.stateNode}}function zl(e,t,n,r){var i=e.tag;if(i===5||i===6)i=e.stateNode,t?(n.nodeType===9?n.body:n.nodeName===`HTML`?n.ownerDocument.body:n).insertBefore(i,t):(t=n.nodeType===9?n.body:n.nodeName===`HTML`?n.ownerDocument.body:n,t.appendChild(i),n=n._reactRootContainer,n!=null||t.onclick!==null||(t.onclick=En)),Al(e,r),j=!0;else if(i!==4&&(i===27&&(Al(e,r),r=null,Sp(e.type)&&(n=e.stateNode,t=null)),e=e.child,e!==null))for(zl(e,t,n,r),e=e.sibling;e!==null;)zl(e,t,n,r),e=e.sibling}function Bl(e,t,n,r){var i=e.tag;if(i===5||i===6)i=e.stateNode,t?n.insertBefore(i,t):n.appendChild(i),Al(e,r),j=!0;else if(i!==4&&(i===27&&(Al(e,r),r=null,Sp(e.type)&&(n=e.stateNode)),e=e.child,e!==null))for(Bl(e,t,n,r),e=e.sibling;e!==null;)Bl(e,t,n,r),e=e.sibling}function Vl(e){var t=e.stateNode,n=e.memoizedProps;try{for(var r=e.type,i=t.attributes;i.length;)t.removeAttributeNode(i[0]);np(t,r,n),t[At]=e,t[jt]=n}catch(t){Z(e,e.return,t)}}var Hl=!1,Ul=null;function Wl(e){(e.tag===30||e.subtreeFlags&33554432)&&(Hl=!0)}var Gl=null;function Kl(){var e=Gl;return Gl=null,e}var ql=0;function Jl(e,t,n,r,i){return ql=0,Yl(e.child,t,n,r,i)}function Yl(e,t,n,r,i){for(var a=!1;e!==null;){if(e.tag===5){var o=e.stateNode;if(r!==null){var s=Op(o);r.push(s),s.view&&(a=!0)}else a||Op(o).view&&(a=!0);Hl=!0,Tp(o,ql===0?t:t+`_`+ql,n),ql++}else(e.tag!==22||e.memoizedState===null)&&(e.tag===30&&i||Yl(e.child,t,n,r,i)&&(a=!0));e=e.sibling}return a}function Xl(e,t){for(;e!==null;)e.tag===5?Ep(e.stateNode,e.memoizedProps):(e.tag!==22||e.memoizedState===null)&&(e.tag===30&&t||Xl(e.child,t)),e=e.sibling}function Zl(e){if(e.subtreeFlags&18874368)for(e=e.child;e!==null;){if((e.tag!==22||e.memoizedState===null)&&(Zl(e),e.tag===30&&e.flags&18874368&&e.stateNode.paired)){var t=e.memoizedProps;if(t.name==null||t.name===`auto`)throw Error(i(544));var n=t.name;t=wi(t.default,t.share),t!==`none`&&(Jl(e,n,t,null,!1)||Xl(e.child,!1))}e=e.sibling}}function Ql(e,t){if(e.tag===30){var n=e.stateNode,r=e.memoizedProps,i=Si(r,n),a=wi(r.default,n.paired?r.share:r.enter);a===`none`?Zl(e):Jl(e,i,a,null,!1)?(Zl(e),n.paired||t||Nd(e,r.onEnter)):Xl(e.child,!1)}else if(e.subtreeFlags&33554432)for(e=e.child;e!==null;)Ql(e,t),e=e.sibling;else Zl(e)}function $l(e){if(Ul!==null&&Ul.size!==0){var t=Ul;if(e.subtreeFlags&18874368)for(e=e.child;e!==null;){if(e.tag!==22||e.memoizedState===null){if(e.tag===30&&e.flags&18874368){var n=e.memoizedProps,r=n.name;if(r!=null&&r!==`auto`){var i=t.get(r);if(i!==void 0){var a=wi(n.default,n.share);if(a!==`none`&&(Jl(e,r,a,null,!1)?(a=e.stateNode,i.paired=a,a.paired=i,Nd(e,n.onShare)):Xl(e.child,!1)),t.delete(r),t.size===0)break}}}$l(e)}e=e.sibling}}}function eu(e){if(e.tag===30){var t=e.memoizedProps,n=Si(t,e.stateNode),r=Ul===null?void 0:Ul.get(n),i=wi(t.default,r===void 0?t.exit:t.share);i!==`none`&&(Jl(e,n,i,null,!1)?r===void 0?Nd(e,t.onExit):(i=e.stateNode,r.paired=i,i.paired=r,Ul.delete(n),Nd(e,t.onShare)):Xl(e.child,!1)),Ul!==null&&$l(e)}else if(e.subtreeFlags&33554432)for(e=e.child;e!==null;)eu(e),e=e.sibling;else Ul!==null&&$l(e)}function tu(e){for(e=e.child;e!==null;){if(e.tag===30){var t=e.memoizedProps,n=Si(t,e.stateNode);t=wi(t.default,t.update),e.flags&=-5,t!==`none`&&Jl(e,n,t,e.memoizedState=[],!1)}else e.subtreeFlags&33554432&&tu(e);e=e.sibling}}function nu(e){if(e.subtreeFlags&18874368)for(e=e.child;e!==null;){if(e.tag!==22||e.memoizedState===null){if(e.tag===30&&e.flags&18874368){var t=e.stateNode;t.paired!==null&&(t.paired=null,Xl(e.child,!1))}nu(e)}e=e.sibling}}function ru(e){if(e.tag===30)e.stateNode.paired=null,Xl(e.child,!1),nu(e);else if(e.subtreeFlags&33554432)for(e=e.child;e!==null;)ru(e),e=e.sibling;else nu(e)}function iu(e){for(e=e.child;e!==null;)e.tag===30?Xl(e.child,!1):e.subtreeFlags&33554432&&iu(e),e=e.sibling}function au(e,t,n,r,i,a,o){for(var s=!1;t!==null;){if(t.tag===5){var c=t.stateNode;if(a!==null&&ql<a.length){var l=a[ql],u=Op(c);(l.view||u.view)&&(s=!0);var d;if(d=!(e.flags&4)){if(u.clip)d=!0;else{d=l.rect;var f=u.rect;d=d.y!==f.y||d.x!==f.x||d.height!==f.height||d.width!==f.width}}d&&(e.flags|=4),u.abs?u=!l.abs:(l=l.rect,u=u.rect,u=l.height!==u.height||l.width!==u.width),u&&(e.flags|=32)}else e.flags|=32;e.flags&4&&Tp(c,ql===0?n:n+`_`+ql,i),s&&e.flags&4||(Gl===null&&(Gl=[]),Gl.push(c,ql===0?r:r+`_`+ql,t.memoizedProps)),ql++}else(t.tag!==22||t.memoizedState===null)&&(t.tag===30&&o?e.flags|=t.flags&32:au(e,t.child,n,r,i,a,o)&&(s=!0));t=t.sibling}return s}function ou(e,t){for(e=e.child;e!==null;){if(e.tag===30){var n=e.memoizedProps,r=e.stateNode,i=Si(n,r),a=wi(n.default,n.update);if(t){r=r.clones;var o=r===null?null:r.map(kp)}else o=e.memoizedState,e.memoizedState=null;r=e;var s=e.child;ql=0,i=au(r,s,i,i,a,o,!1),e.flags&4&&i&&(t||Nd(e,n.onUpdate))}else e.subtreeFlags&33554432&&ou(e,t);e=e.sibling}}var su=!1,H=!1,cu=!1,lu=!1,uu=typeof WeakSet==`function`?WeakSet:Set,du=null,fu=!1,pu=!1,mu=!1,hu=!1;function gu(e,t,n){if(e=e.containerInfo,sp=gh,e=Qr(e),$r(e)){if(`selectionStart`in e)var r={start:e.selectionStart,end:e.selectionEnd};else a:{r=(r=e.ownerDocument)&&r.defaultView||window;var i=r.getSelection&&r.getSelection();if(i&&i.rangeCount!==0){r=i.anchorNode;var a=i.anchorOffset,o=i.focusNode;i=i.focusOffset;try{r.nodeType,o.nodeType}catch{r=null;break a}var s=0,c=-1,l=-1,u=0,d=0,f=e,p=null;b:for(;;){for(var m;f!==r||a!==0&&f.nodeType!==3||(c=s+a),f!==o||i!==0&&f.nodeType!==3||(l=s+i),f.nodeType===3&&(s+=f.nodeValue.length),(m=f.firstChild)!==null;)p=f,f=m;for(;;){if(f===e)break b;if(p===r&&++u===a&&(c=s),p===o&&++d===i&&(l=s),(m=f.nextSibling)!==null)break;f=p,p=f.parentNode}f=m}r=c===-1||l===-1?null:{start:c,end:l}}else r=null}r||={start:0,end:0}}else r=null;for(cp={focusedElem:e,selectionRange:r},gh=!1,n=(n&335544064)===n,du=t,t=n?9270:1024;du!==null;){if(e=du,n&&(r=e.deletions,r!==null))for(a=0;a<r.length;a++)n&&eu(r[a]);if(e.alternate===null&&e.flags&2)n&&Wl(e),U(n);else{if(e.tag===22){if(r=e.alternate,e.memoizedState!==null){r!==null&&r.memoizedState===null&&n&&eu(r),U(n);continue}if(r!==null&&r.memoizedState!==null){n&&Wl(e),U(n);continue}}r=e.child,(e.subtreeFlags&t)!==0&&r!==null?(r.return=e,du=r):(n&&tu(e),U(n))}}Ul=null}function U(e){for(;du!==null;){var t=du,n=e,r=t.alternate,a=t.flags;switch(t.tag){case 0:case 11:case 15:break;case 1:if(a&1024&&r!==null){n=void 0,a=r.memoizedProps,r=r.memoizedState;var o=t.stateNode;try{var s=Tc(t.type,a);n=o.getSnapshotBeforeUpdate(s,r),o.__reactInternalSnapshotBeforeUpdate=n}catch(e){Z(t,t.return,e)}}break;case 3:if(a&1024){if(r=t.stateNode.containerInfo,n=r.nodeType,n===9)nm(r);else if(n===1)switch(r.nodeName){case`HEAD`:case`HTML`:case`BODY`:nm(r);break;default:r.textContent=``}}break;case 5:case 26:case 27:case 6:case 4:case 17:break;case 30:n&&r!==null&&(n=Si(r.memoizedProps,r.stateNode),a=t.memoizedProps,a=wi(a.default,a.update),a!==`none`&&Jl(r,n,a,r.memoizedState=[],!0));break;default:if(a&1024)throw Error(i(163))}if(r=t.sibling,r!==null){r.return=t.return,du=r;break}du=t.return}}function _u(e,t,n){var r=n.flags;switch(n.tag){case 0:case 11:case 15:Lu(e,n),r&4&&wl(5,n);break;case 1:if(Lu(e,n),r&4){if(e=n.stateNode,t===null)try{e.componentDidMount()}catch(e){Z(n,n.return,e)}else{var i=Tc(n.type,t.memoizedProps);t=t.memoizedState;try{e.componentDidUpdate(i,t,e.__reactInternalSnapshotBeforeUpdate)}catch(e){Z(n,n.return,e)}}}r&64&&El(n),r&512&&Ol(n,n.return);break;case 3:if(Lu(e,n),r&64&&(e=n.updateQueue,e!==null)){if(t=null,n.child!==null)switch(n.child.tag){case 27:case 5:t=n.child.stateNode;break;case 1:t=n.child.stateNode}try{ko(e,t)}catch(e){Z(n,n.return,e)}}break;case 27:t===null&&r&4&&Vl(n);case 26:case 5:Lu(e,n),t===null&&r&4&&Fl(n),r&512&&Ol(n,n.return);break;case 12:Lu(e,n);break;case 31:Lu(e,n),r&4&&Eu(e,n);break;case 13:Lu(e,n),r&4&&Du(e,n),r&64&&(e=n.memoizedState,e!==null&&(e=e.dehydrated,e!==null&&(n=_f.bind(null,n),cm(e,n))));break;case 22:if(r=n.memoizedState!==null||su,!r){var a=t!==null&&t.memoizedState!==null||H;t=su,i=H,su=r,(H=a)&&!i?(r=2,n.subtreeFlags&8772&&(r|=1),zu(e,n,r)):Lu(e,n),su=t,H=i}break;case 30:Lu(e,n),r&512&&Ol(n,n.return);break;case 7:r&512&&Ol(n,n.return);default:Lu(e,n)}}function vu(e,t){for(e=e.child;e!==null;)yu(e,t),e=e.sibling}function yu(e,t){switch(e.tag){case 5:case 26:try{var n=e.stateNode;if(t){var r=n.style;typeof r.setProperty==`function`?r.setProperty(`display`,`none`,`important`):r.display=`none`}else{var i=e.stateNode,a=e.memoizedProps.style,o=a!=null&&a.hasOwnProperty(`display`)?a.display:null;i.style.display=o==null||typeof o==`boolean`?``:(``+o).trim()}}catch(t){Z(e,e.return,t)}bu(e,t);break;case 6:try{e.stateNode.nodeValue=t?``:e.memoizedProps,j=!0}catch(t){Z(e,e.return,t)}break;case 18:try{var s=e.stateNode;t?wp(s,!0):wp(e.stateNode,!1)}catch(t){Z(e,e.return,t)}break;case 22:case 23:e.memoizedState===null&&vu(e,t);break;default:vu(e,t)}}function bu(e,t){if(e.subtreeFlags&67108864)for(e=e.child;e!==null;){a:{var n=e,r=t;switch(n.tag){case 4:yu(n,r);break a;case 22:n.memoizedState===null&&bu(n,r);break a;default:bu(n,r)}}e=e.sibling}}function xu(e){var t=e.alternate;t!==null&&(e.alternate=null,xu(t)),e.child=null,e.deletions=null,e.sibling=null,e.tag===5&&(t=e.stateNode,t!==null&&zt(t)),e.stateNode=null,e.return=null,e.dependencies=null,e.memoizedProps=null,e.memoizedState=null,e.pendingProps=null,e.stateNode=null,e.updateQueue=null}var Su=null,Cu=!1;function wu(e,t,n){for(n=n.child;n!==null;)Tu(e,t,n),n=n.sibling}function Tu(e,t,n){if(it&&typeof it.onCommitFiberUnmount==`function`)try{it.onCommitFiberUnmount(rt,n)}catch{}switch(n.tag){case 26:H||kl(n,t),wu(e,t,n),n.memoizedState?n.memoizedState.count--:n.stateNode&&!H&&(n=n.stateNode,n.parentNode.removeChild(n));break;case 27:H||kl(n,t),Ml(n);var r=Su,i=Cu;Sp(n.type)&&(Su=n.stateNode,Cu=!1),wu(e,t,n),gm(n.stateNode,n.type,n.memoizedProps),Su=r,Cu=i;break;case 5:H||kl(n,t),Ml(n);case 6:if(n.tag===6&&Ml(n),r=Su,i=Cu,Su=null,wu(e,t,n),Su=r,Cu=i,Su!==null){if(Cu)try{(Su.nodeType===9?Su.body:Su.nodeName===`HTML`?Su.ownerDocument.body:Su).removeChild(n.stateNode),j=!0}catch(e){Z(n,t,e)}else try{Su.removeChild(n.stateNode),j=!0}catch(e){Z(n,t,e)}}break;case 18:Su!==null&&(Cu?(e=Su,Cp(e.nodeType===9?e.body:e.nodeName===`HTML`?e.ownerDocument.body:e,n.stateNode),Hh(e)):Cp(Su,n.stateNode));break;case 4:r=Su,i=Cu,Su=n.stateNode.containerInfo,Cu=!0,wu(e,t,n),Su=r,Cu=i;break;case 0:case 11:case 14:case 15:Tl(2,n,t),H||Tl(4,n,t),wu(e,t,n);break;case 1:H||(kl(n,t),r=n.stateNode,typeof r.componentWillUnmount==`function`&&Dl(n,t,r)),wu(e,t,n);break;case 21:wu(e,t,n);break;case 22:H=(r=H)||n.memoizedState!==null,wu(e,t,n),H=r;break;case 30:kl(n,t),wu(e,t,n);break;case 7:H||kl(n,t),wu(e,t,n);break;default:wu(e,t,n)}}function Eu(e,t){if(t.memoizedState===null&&(e=t.alternate,e!==null&&(e=e.memoizedState,e!==null))){e=e.dehydrated;try{Hh(e)}catch(e){Z(t,t.return,e)}}}function Du(e,t){if(t.memoizedState===null&&(e=t.alternate,e!==null&&(e=e.memoizedState,e!==null&&(e=e.dehydrated,e!==null))))try{Hh(e)}catch(e){Z(t,t.return,e)}}function Ou(e){switch(e.tag){case 31:case 13:case 19:var t=e.stateNode;return t===null&&(t=e.stateNode=new uu),t;case 22:return e=e.stateNode,t=e._retryCache,t===null&&(t=e._retryCache=new uu),t;default:throw Error(i(435,e.tag))}}function ku(e,t){var n=Ou(e);t.forEach(function(t){if(!n.has(t)){n.add(t);var r=vf.bind(null,e,t);t.then(r,r)}})}function Au(e,t,n){var r=t.deletions;if(r!==null)for(var a=0;a<r.length;a++){var o=r[a],s=e,c=t,l=c;a:for(;l!==null;){switch(l.tag){case 27:if(Sp(l.type)){Su=l.stateNode,Cu=!1;break a}break;case 5:Su=l.stateNode,Cu=!1;break a;case 3:case 4:Su=l.stateNode.containerInfo,Cu=!0;break a}l=l.return}if(Su===null)throw Error(i(160));Tu(s,c,o),Su=null,Cu=!1,s=o.alternate,s!==null&&(s.return=null),o.return=null}if(t.subtreeFlags&13886)for(t=t.child;t!==null;)Mu(t,e,n),t=t.sibling}var ju=null;function Mu(e,t,n){var r=e.alternate,a=e.flags;switch(e.tag){case 0:case 11:case 14:case 15:if(a&4&&(r=e.updateQueue,r=r===null?null:r.events,r!==null))for(var o=0;o<r.length;o++){var s=r[o];s.ref.impl=s.nextImpl}Au(t,e,n),Nu(e),a&4&&(Tl(3,e,e.return),wl(3,e),Tl(5,e,e.return));break;case 1:Au(t,e,n),Nu(e),a&512&&(H||r===null||kl(r,r.return)),a&64&&su&&(e=e.updateQueue,e!==null&&(t=e.callbacks,t!==null&&(n=e.shared.hiddenCallbacks,e.shared.hiddenCallbacks=n===null?t:n.concat(t))));break;case 26:if(o=ju,Au(t,e,n),Nu(e),a&512&&(H||r===null||kl(r,r.return)),a&4){if(a=r===null?null:r.memoizedState,n=e.memoizedState,r===null){if(n===null){if(e.stateNode===null){if(su)e.stateNode=fp(e.type,e.memoizedProps,t.containerInfo,e);else{a:{t=e.type,n=e.memoizedProps,a=o.ownerDocument||o;b:switch(t){case`title`:r=a.getElementsByTagName(`title`)[0],(!r||r[Lt]||r[At]||r.namespaceURI===`http://www.w3.org/2000/svg`||r.hasAttribute(`itemprop`))&&(r=a.createElement(t),a.head.insertBefore(r,a.querySelector(`head > title`))),np(r,t,n),r[At]=e,Wt(r),t=r;break a;case`link`:if(o=Gm(`link`,`href`,a).get(t+(n.href||``))){for(s=0;s<o.length;s++)if(r=o[s],r.getAttribute(`href`)===(n.href==null||n.href===``?null:n.href)&&r.getAttribute(`rel`)===(n.rel==null?null:n.rel)&&r.getAttribute(`title`)===(n.title==null?null:n.title)&&r.getAttribute(`crossorigin`)===(n.crossOrigin==null?null:n.crossOrigin)){o.splice(s,1);break b}}r=a.createElement(t),np(r,t,n),a.head.appendChild(r);break;case`meta`:if(o=Gm(`meta`,`content`,a).get(t+(n.content||``))){for(s=0;s<o.length;s++)if(r=o[s],r.getAttribute(`content`)===(n.content==null?null:``+n.content)&&r.getAttribute(`name`)===(n.name==null?null:n.name)&&r.getAttribute(`property`)===(n.property==null?null:n.property)&&r.getAttribute(`http-equiv`)===(n.httpEquiv==null?null:n.httpEquiv)&&r.getAttribute(`charset`)===(n.charSet==null?null:n.charSet)){o.splice(s,1);break b}}r=a.createElement(t),np(r,t,n),a.head.appendChild(r);break;default:throw Error(i(468,t))}r[At]=e,Wt(r),t=r}e.stateNode=t}}else su||Km(o,e.type,e.stateNode)}else e.stateNode=Bm(o,n,e.memoizedProps)}else a===n?n===null&&e.stateNode!==null&&Il(e,e.memoizedProps,r.memoizedProps):(a===null?(t=r.stateNode,t===null||H||t.parentNode.removeChild(t)):a.count--,n===null?su||Km(o,e.type,e.stateNode):Bm(o,n,e.memoizedProps))}break;case 27:Au(t,e,n),Nu(e),a&512&&(H||r===null||kl(r,r.return)),r!==null&&a&4&&Il(e,e.memoizedProps,r.memoizedProps);break;case 5:if(o=cu,cu=!1,Au(t,e,n),cu=o,Nu(e),a&512&&(H||r===null||kl(r,r.return)),e.flags&32){t=e.stateNode;try{vn(t,``),j=!0}catch(t){Z(e,e.return,t)}}a&4&&e.stateNode!=null&&(t=e.memoizedProps,Il(e,t,r===null?t:r.memoizedProps)),a&1024&&(lu=!0);break;case 6:if(Au(t,e,n),Nu(e),a&4){if(e.stateNode===null)throw Error(i(162));t=e.memoizedProps,n=e.stateNode;try{n.nodeValue=t,j=!0}catch(t){Z(e,e.return,t)}}break;case 3:if(j=!1,Wm=null,o=ju,ju=bm(t.containerInfo),Au(t,e,n),ju=o,Nu(e),a&4&&r!==null&&r.memoizedState.isDehydrated)try{Hh(t.containerInfo)}catch(t){Z(e,e.return,t)}lu&&(lu=!1,Pu(e)),j=!1;break;case 4:a=cu,cu=su,r=en(),o=ju,ju=bm(e.stateNode.containerInfo),Au(t,e,n),Nu(e),ju=o,j&&pu&&(mu=!0),j=r,cu=a;break;case 12:Au(t,e,n),Nu(e);break;case 31:Au(t,e,n),Nu(e),a&4&&(t=e.updateQueue,t!==null&&(e.updateQueue=null,ku(e,t)));break;case 13:Au(t,e,n),Nu(e),e.child.flags&8192&&e.memoizedState!==null!=(r!==null&&r.memoizedState!==null)&&(hd=Je()),a&4&&(t=e.updateQueue,t!==null&&(e.updateQueue=null,ku(e,t)));break;case 22:o=e.memoizedState!==null,s=r!==null&&r.memoizedState!==null;var c=su,l=H,u=cu;su=c||o,cu=u||o,H=l||s,Au(t,e,n),H=l,cu=u,su=c,Nu(e),a&8192&&(t=e.stateNode,t._visibility=o?t._visibility&-2:t._visibility|1,!o||r===null||s||su||H||(t=s||H,n=su,r=H,su=o||su,H=t,Ru(e,2),su=n,H=r),!o&&cu||vu(e,o)),a&4&&(t=e.updateQueue,t!==null&&(n=t.retryQueue,n!==null&&(t.retryQueue=null,ku(e,n))));break;case 19:Au(t,e,n),Nu(e),a&4&&(t=e.updateQueue,t!==null&&(e.updateQueue=null,ku(e,t)));break;case 30:a&512&&(H||r===null||kl(r,r.return)),a=en(),o=pu,s=(n&335544064)===n,c=e.memoizedProps,pu=s&&wi(c.default,c.update)!==`none`,Au(t,e,n),Nu(e),s&&r!==null&&j&&(e.flags|=4),pu=o,j=a;break;case 21:break;case 7:a&512&&(H||r===null||kl(r,r.return)),r&&r.stateNode!==null&&(r.stateNode._fragmentFiber=e);default:Au(t,e,n),Nu(e)}}function Nu(e){var t=e.flags;if(t&2){try{for(var n,r=e.return;r!==null;){if(Ll(r)){n=r;break}r=r.return}r=null;for(var a=e.return;a!==null;){if(Pl(a)){var o=a.stateNode;r===null?r=[o]:r.push(o)}if(Nl(a))break;a=a.return}var s=r;if(n==null)throw Error(i(160));switch(n.tag){case 27:var c=n.stateNode;Bl(e,Rl(e),c,s);break;case 5:var l=n.stateNode;n.flags&32&&(vn(l,``),n.flags&=-33),Bl(e,Rl(e),l,s);break;case 3:case 4:var u=n.stateNode.containerInfo;zl(e,Rl(e),u,s);break;default:throw Error(i(161))}}catch(t){Z(e,e.return,t)}e.flags&=-3}t&4096&&(e.flags&=-4097)}function Pu(e){if(e.subtreeFlags&1024)for(e=e.child;e!==null;){var t=e;Pu(t),t.tag===5&&t.flags&1024&&(t=t.stateNode,gh=!0,t.reset(),gh=!1),e=e.sibling}}function Fu(e,t){if(t.subtreeFlags&9270)for(t=t.child;t!==null;)Iu(t,e),t=t.sibling;else ou(t,!1)}function Iu(e,t){var n=e.alternate;if(n===null)Ql(e,!1);else switch(e.tag){case 3:if(hu=fu=!1,Kl(),Fu(t,e),!fu&&!mu){if(e=Gl,e!==null)for(var r=0;r<e.length;r+=3){n=e[r];var i=e[r+1];Ep(n,e[r+2]),n=n.ownerDocument.documentElement,n!==null&&n.animate({opacity:[0,0],pointerEvents:[`none`,`none`]},{duration:0,fill:`forwards`,pseudoElement:`::view-transition-group(`+i+`)`})}e=t.containerInfo,e=e.nodeType===9?e.documentElement:e.ownerDocument.documentElement,e!==null&&e.style.viewTransitionName===``&&(e.style.viewTransitionName=`none`,e.animate({opacity:[0,0],pointerEvents:[`none`,`none`]},{duration:0,fill:`forwards`,pseudoElement:`::view-transition-group(root)`}),e.animate({width:[0,0],height:[0,0]},{duration:0,fill:`forwards`,pseudoElement:`::view-transition`})),hu=!0}Gl=null;break;case 5:Fu(t,e);break;case 4:r=fu,fu=!1,Fu(t,e),fu&&(mu=!0),fu=r;break;case 22:e.memoizedState===null&&(n.memoizedState===null?Fu(t,e):Ql(e,!1));break;case 30:r=fu,i=Kl(),fu=!1,Fu(t,e),fu&&(e.flags|=4);var a=e.memoizedProps,o=e.stateNode;t=Si(a,o),o=Si(n.memoizedProps,o);var s=wi(a.default,a.update);s===`none`?t=!1:(a=n.memoizedState,n.memoizedState=null,n=e.child,ql=0,t=au(e,n,t,o,s,a,!0),ql!==(a===null?0:a.length)&&(e.flags|=32)),e.flags&4&&t?(Nd(e,e.memoizedProps.onUpdate),Gl=i):i!==null&&(i.push.apply(i,Gl),Gl=i),fu=e.flags&32?!0:r;break;default:Fu(t,e)}}function Lu(e,t){if(t.subtreeFlags&8772)for(t=t.child;t!==null;)_u(e,t.alternate,t),t=t.sibling}function Ru(e,t){for(e=e.child;e!==null;){var n=e,r=t;switch(n.tag){case 0:case 11:case 14:case 15:Tl(4,n,n.return),Ru(n,r);break;case 1:kl(n,n.return);var i=n.stateNode;typeof i.componentWillUnmount==`function`&&Dl(n,n.return,i),Ru(n,r);break;case 27:r&2&&gm(n.stateNode,n.type,n.memoizedProps);case 5:kl(n,n.return),n.tag!==5&&n.tag!==27||Ml(n),Ru(n,r);break;case 6:Ml(n);break;case 26:kl(n,n.return),i=n.stateNode,n.memoizedState!==null||i===null||H||i.parentNode.removeChild(i),Ru(n,r);break;case 22:n.memoizedState===null&&Ru(n,r);break;case 30:kl(n,n.return),Ru(n,r);break;case 7:kl(n,n.return);default:Ru(n,r)}e=e.sibling}}function zu(e,t,n){for(n=t.subtreeFlags&8772?n:n&-2,t=t.child;t!==null;){var r=t.alternate,i=e,a=t,o=a.flags,s=!!(n&1);switch(a.tag){case 0:case 11:case 15:zu(i,a,n),wl(4,a);break;case 1:if(zu(i,a,n),r=a,i=r.stateNode,typeof i.componentDidMount==`function`)try{i.componentDidMount()}catch(e){Z(r,r.return,e)}if(r=a,i=r.updateQueue,i!==null){var c=r.stateNode;try{var l=i.shared.hiddenCallbacks;if(l!==null)for(i.shared.hiddenCallbacks=null,i=0;i<l.length;i++)Oo(l[i],c)}catch(e){Z(r,r.return,e)}}s&&o&64&&El(a),Ol(a,a.return);break;case 27:n&2&&Vl(a);case 5:a.tag!==5&&a.tag!==27||jl(a),zu(i,a,n),s&&r===null&&o&4&&Fl(a),Ol(a,a.return);break;case 6:jl(a);break;case 26:c=a.stateNode,a.memoizedState!==null||c===null||su||Km(bm(c.ownerDocument),a.type,c),zu(i,a,n),s&&r===null&&o&4&&Fl(a),Ol(a,a.return);break;case 12:zu(i,a,n);break;case 31:zu(i,a,n),s&&o&4&&Eu(i,a);break;case 13:zu(i,a,n),s&&o&4&&Du(i,a);break;case 22:a.memoizedState===null&&zu(i,a,n),Ol(a,a.return);break;case 30:zu(i,a,n),Ol(a,a.return);break;case 7:Ol(a,a.return);default:zu(i,a,n)}t=t.sibling}}function Bu(e,t){var n=null;e!==null&&e.memoizedState!==null&&e.memoizedState.cachePool!==null&&(n=e.memoizedState.cachePool.pool),e=null,t.memoizedState!==null&&t.memoizedState.cachePool!==null&&(e=t.memoizedState.cachePool.pool),e!==n&&(e!=null&&e.refCount++,n!=null&&La(n))}function Vu(e,t){e=null,t.alternate!==null&&(e=t.alternate.memoizedState.cache),t=t.memoizedState.cache,t!==e&&(t.refCount++,e!=null&&La(e))}function Hu(e,t,n,r){var i=(n&335544064)===n;if(t.subtreeFlags&(i?10262:10256))for(t=t.child;t!==null;)Uu(e,t,n,r),t=t.sibling;else i&&iu(t)}function Uu(e,t,n,r){var i=(n&335544064)===n;i&&t.alternate===null&&t.return!==null&&t.return.alternate!==null&&ru(t);var a=t.flags;switch(t.tag){case 0:case 11:case 15:Hu(e,t,n,r),a&2048&&wl(9,t);break;case 1:Hu(e,t,n,r);break;case 3:Hu(e,t,n,r),i&&hu&&(e=e.containerInfo,e=e.nodeType===9?e.body:e.nodeName===`HTML`?e.ownerDocument.body:e,e.style.viewTransitionName===`root`&&(e.style.viewTransitionName=``),e=e.ownerDocument.documentElement,e!==null&&e.style.viewTransitionName===`none`&&(e.style.viewTransitionName=``)),a&2048&&(a=null,t.alternate!==null&&(a=t.alternate.memoizedState.cache),t=t.memoizedState.cache,t!==a&&(t.refCount++,a!=null&&La(a)));break;case 12:if(a&2048){Hu(e,t,n,r),a=t.stateNode;try{var o=t.memoizedProps,s=o.id,c=o.onPostCommit;typeof c==`function`&&c(s,t.alternate===null?`mount`:`update`,a.passiveEffectDuration,-0)}catch(e){Z(t,t.return,e)}}else Hu(e,t,n,r);break;case 31:Hu(e,t,n,r);break;case 13:Hu(e,t,n,r);break;case 23:break;case 22:o=t.stateNode,s=t.alternate,t.memoizedState===null?(i&&s!==null&&s.memoizedState!==null&&ru(t),o._visibility&2?Hu(e,t,n,r):(o._visibility|=2,Wu(e,t,n,r,!!(t.subtreeFlags&10256)||!1))):(i&&s!==null&&s.memoizedState===null&&ru(s),o._visibility&2?Hu(e,t,n,r):Gu(e,t)),a&2048&&Bu(s,t);break;case 24:Hu(e,t,n,r),a&2048&&Vu(t.alternate,t);break;case 30:i&&(a=t.alternate,a!==null&&(Xl(a.child,!0),Xl(t.child,!0))),Hu(e,t,n,r);break;default:Hu(e,t,n,r)}}function Wu(e,t,n,r,i){for(i&&=!!(t.subtreeFlags&10256)||!1,t=t.child;t!==null;){var a=e,o=t,s=n,c=r,l=o.flags;switch(o.tag){case 0:case 11:case 15:Wu(a,o,s,c,i),wl(8,o);break;case 23:break;case 22:var u=o.stateNode;o.memoizedState===null?(u._visibility|=2,Wu(a,o,s,c,i)):u._visibility&2?Wu(a,o,s,c,i):Gu(a,o),i&&l&2048&&Bu(o.alternate,o);break;case 24:Wu(a,o,s,c,i),i&&l&2048&&Vu(o.alternate,o);break;default:Wu(a,o,s,c,i)}t=t.sibling}}function Gu(e,t){if(t.subtreeFlags&10256)for(t=t.child;t!==null;){var n=e,r=t,i=r.flags;switch(r.tag){case 22:Gu(n,r),i&2048&&Bu(r.alternate,r);break;case 24:Gu(n,r),i&2048&&Vu(r.alternate,r);break;default:Gu(n,r)}t=t.sibling}}var Ku=8192;function qu(e,t,n){if(e.subtreeFlags&Ku)for(e=e.child;e!==null;)Ju(e,t,n),e=e.sibling}function Ju(e,t,n){switch(e.tag){case 26:qu(e,t,n),e.flags&Ku&&(e.memoizedState===null?(e=e.stateNode,(t&335544128)===t&&Zm(n,e)):Qm(n,ju,e.memoizedState,e.memoizedProps));break;case 5:qu(e,t,n),e.flags&Ku&&(e=e.stateNode,(t&335544128)===t&&Zm(n,e));break;case 3:case 4:var r=ju;ju=bm(e.stateNode.containerInfo),qu(e,t,n),ju=r;break;case 22:e.memoizedState===null&&(r=e.alternate,r!==null&&r.memoizedState!==null?(r=Ku,Ku=16777216,qu(e,t,n),Ku=r):qu(e,t,n));break;case 30:if((e.flags&Ku)!==0&&(r=e.memoizedProps.name,r!=null&&r!==`auto`)){var i=e.stateNode;i.paired=null,Ul===null&&(Ul=new Map),Ul.set(r,i)}qu(e,t,n);break;default:qu(e,t,n)}}function Yu(e){var t=e.alternate;if(t!==null&&(e=t.child,e!==null)){t.child=null;do t=e.sibling,e.sibling=null,e=t;while(e!==null)}}function Xu(e){var t=e.deletions;if(e.flags&16){if(t!==null)for(var n=0;n<t.length;n++){var r=t[n];du=r,$u(r,e)}Yu(e)}if(e.subtreeFlags&10256)for(e=e.child;e!==null;)Zu(e),e=e.sibling}function Zu(e){switch(e.tag){case 0:case 11:case 15:Xu(e),e.flags&2048&&Tl(9,e,e.return);break;case 3:Xu(e);break;case 12:Xu(e);break;case 22:var t=e.stateNode;e.memoizedState!==null&&t._visibility&2&&(e.return===null||e.return.tag!==13)?(t._visibility&=-3,Qu(e)):Xu(e);break;default:Xu(e)}}function Qu(e){var t=e.deletions;if(e.flags&16){if(t!==null)for(var n=0;n<t.length;n++){var r=t[n];du=r,$u(r,e)}Yu(e)}for(e=e.child;e!==null;){switch(t=e,t.tag){case 0:case 11:case 15:Tl(8,t,t.return),Qu(t);break;case 22:n=t.stateNode,n._visibility&2&&(n._visibility&=-3,Qu(t));break;default:Qu(t)}e=e.sibling}}function $u(e,t){for(;du!==null;){var n=du;switch(n.tag){case 0:case 11:case 15:Tl(8,n,t);break;case 23:case 22:if(n.memoizedState!==null&&n.memoizedState.cachePool!==null){var r=n.memoizedState.cachePool.pool;r!=null&&r.refCount++}break;case 24:La(n.memoizedState.cache)}if(r=n.child,r!==null)r.return=n,du=r;else a:for(n=e;du!==null;){r=du;var i=r.sibling,a=r.return;if(xu(r),r===n){du=null;break a}if(i!==null){i.return=a,du=i;break a}du=a}}}var ed={getCacheForType:function(e){var t=ka(Fa),n=t.data.get(e);return n===void 0&&(n=e(),t.data.set(e,n)),n},cacheSignal:function(){return ka(Fa).controller.signal}},td=typeof WeakMap==`function`?WeakMap:Map,W=0,G=null,K=null,q=0,J=0,nd=null,rd=!1,id=!1,ad=!1,od=0,Y=0,sd=0,cd=0,ld=0,ud=0,dd=0,fd=null,pd=null,md=!1,hd=0,gd=0,_d=1/0,vd=null,yd=null,X=0,bd=null,xd=null,Sd=0,Cd=0,wd=null,Td=null,Ed=null,Dd=null,Od=null,kd=0,Ad=null;function jd(){return W&2&&q!==0?q&-q:D.T===null?Dt():Pf()}function Md(){if(ud===0){if(!(q&536870912)||I){var e=dt;dt<<=1,!(dt&3932160)&&(dt=262144),ud=e}else ud=536870912}return e=Fo.current,e!==null&&(e.flags|=32),ud}function Nd(e,t){if(t!=null){var n=e.stateNode,r=n.ref;r===null&&(r=n.ref=Pp(Si(e.memoizedProps,n))),Dd===null&&(Dd=[]),Dd.push(t.bind(null,r))}}function Pd(e,t,n){(e===G&&(J===2||J===9)||e.cancelPendingCommit!==null)&&(Vd(e,0),Rd(e,q,ud,!1)),bt(e,n),(!(W&2)||e!==G)&&(e===G&&(!(W&2)&&(cd|=n),Y===4&&Rd(e,q,ud,!1)),Ef(e))}function Fd(e,t,n){if(W&6)throw Error(i(327));var r=!n&&!(t&127)&&(t&e.expiredLanes)===0||ht(e,t),a=r?Yd(e,t):qd(e,t,!0),o=r;do{if(a===0){id&&!r&&Rd(e,t,0,!1);break}if(n=e.current.alternate,o&&!Ld(n))a=qd(e,t,!1),o=!1;else{if(a===2){if(o=t,e.errorRecoveryDisabledLanes&o)var s=0;else s=e.pendingLanes&-536870913,s=s===0?s&536870912?536870912:0:s;if(s!==0){t=s;a:{var c=e;a=fd;var l=c.current.memoizedState.isDehydrated;if(l&&(Vd(c,s).flags|=256),s=qd(c,s,!1),s!==2&&s!==6){if(ad&&!l){c.errorRecoveryDisabledLanes|=o,cd|=o,a=4;break a}o=pd,pd=a,o!==null&&(pd===null?pd=o:pd.push.apply(pd,o))}a=s}if(o=!1,a!==2)continue}}if(a===1){Vd(e,0),Rd(e,t,0,!0);break}a:{switch(r=e,o=a,o){case 0:case 1:throw Error(i(345));case 4:if((t&4194048)!==t&&(t&62914560)!==t)break;case 6:Rd(r,t,ud,!rd);break a;case 2:pd=null;break;case 3:case 5:break;default:throw Error(i(329))}if((t&62914560)===t&&(a=hd+300-Je(),10<a)){if(Rd(r,t,ud,!rd),mt(r,0,!0)!==0)break a;Sd=t,r.timeoutHandle=gp(Id.bind(null,r,n,pd,vd,md,t,ud,cd,dd,rd,o,`Throttled`,-0,0),a);break a}Id(r,n,pd,vd,md,t,ud,cd,dd,rd,o,null,-0,0)}break}}while(1);Ef(e)}function Id(e,t,n,r,i,a,o,s,c,l,u,d,f,p){e.timeoutHandle=-1;var m=t.subtreeFlags,h=(a&335544064)===a;d=null,(h||m&8192||(m&16785408)==16785408)&&(d={stylesheets:null,count:0,imgCount:0,imgBytes:0,suspenseyImages:[],waitingForImages:!0,waitingForViewTransition:!1,unsuspend:En},Ul=null,Ju(t,a,d),h&&(m=d,h=e.containerInfo,h=(h.nodeType===9?h:h.ownerDocument).__reactViewTransition,h!=null&&(m.count++,m.waitingForViewTransition=!0,m=nh.bind(m),h.finished.then(m,m))),m=(a&62914560)===a?hd-Je():(a&4194048)===a?gd-Je():0,m=eh(d,m),m!==null)?(Sd=a,e.cancelPendingCommit=m(nf.bind(null,e,t,a,n,r,i,o,s,c,l,u,d,null,f,p)),Rd(e,a,o,!l)):nf(e,t,a,n,r,i,o,s,c,l,u,d)}function Ld(e){for(var t=e;;){var n=t.tag;if((n===0||n===11||n===15)&&t.flags&16384&&(n=t.updateQueue,n!==null&&(n=n.stores,n!==null)))for(var r=0;r<n.length;r++){var i=n[r],a=i.getSnapshot;i=i.value;try{if(!Kr(a(),i))return!1}catch{return!1}}if(n=t.child,t.subtreeFlags&16384&&n!==null)n.return=t,t=n;else{if(t===e)break;for(;t.sibling===null;){if(t.return===null||t.return===e)return!0;t=t.return}t.sibling.return=t.return,t=t.sibling}}return!0}function Rd(e,t,n,r){t=gt(e,t),t&=~ld,t&=~cd,e.suspendedLanes|=t,e.pingedLanes&=~t,r&&(e.warmLanes|=t),r=e.expirationTimes;for(var i=t;0<i;){var a=31-ot(i),o=1<<a;r[a]=-1,i&=~o}n!==0&&St(e,n,t)}function zd(){return W&6?!0:(Df(0,!1),!1)}function Bd(){if(K!==null){if(J===0)var e=K.return;else e=K,xa=ba=null,ls(e),lo=null,uo=0,e=K;for(;e!==null;)Cl(e.alternate,e),e=e.return;K=null}}function Vd(e,t){var n=e.timeoutHandle;return n!==-1&&(e.timeoutHandle=-1,_p(n)),n=e.cancelPendingCommit,n!==null&&(e.cancelPendingCommit=null,n()),Sd=0,Bd(),G=e,K=n=zi(e.current,null),q=t,J=0,nd=null,rd=!1,id=ht(e,t),ad=!1,dd=ud=ld=cd=sd=Y=0,pd=fd=null,md=!1,od=gt(e,t),ki(),n}function Hd(e,t){L=null,D.H=_c,t===$a||t===to?(t=so(),J=3):t===eo?(t=so(),J=4):J=t===Fc?8:typeof t==`object`&&t&&typeof t.then==`function`?6:1,nd=t,K===null&&(Y=1,kc(e,qi(t,e.current)))}function Ud(){var e=Fo.current;return e===null?!0:(q&4194048)===q?Io===null:(q&62914560)===q||q&536870912?e===Io:!1}function Wd(){var e=D.H;return D.H=_c,e===null?_c:e}function Gd(){var e=D.A;return D.A=ed,e}function Kd(){Y=4,rd||(q&4194048)!==q&&Fo.current!==null||(id=!0),!(sd&134217727)&&!(cd&134217727)||G===null||Rd(G,q,ud,!1)}function qd(e,t,n){var r=W;W|=2;var i=Wd(),a=Gd();(G!==e||q!==t)&&(vd=null,Vd(e,t)),t=!1;var o=Y;a:do try{if(J!==0&&K!==null){var s=K,c=nd;switch(J){case 8:Bd(),o=6;break a;case 3:case 2:case 9:case 6:Fo.current===null&&(t=!0);var l=J;if(J=0,nd=null,$d(e,s,c,l),n&&id){o=0;break a}break;default:l=J,J=0,nd=null,$d(e,s,c,l)}}Jd(),o=Y;break}catch(t){Hd(e,t)}while(1);return t&&e.shellSuspendCounter++,xa=ba=null,W=r,D.H=i,D.A=a,K===null&&(G=null,q=0,ki()),o}function Jd(){for(;K!==null;)Zd(K)}function Yd(e,t){var n=W;W|=2;var r=Wd(),a=Gd();G!==e||q!==t?(vd=null,_d=Je()+500,Vd(e,t)):id=ht(e,t);a:do try{if(J!==0&&K!==null){t=K;var o=nd;b:switch(J){case 1:J=0,nd=null,$d(e,t,o,1);break;case 2:case 9:if(ro(o)){J=0,nd=null,Qd(t);break}t=function(){J!==2&&J!==9||G!==e||(J=7),Ef(e)},o.then(t,t);break a;case 3:J=7;break a;case 4:J=5;break a;case 7:ro(o)?(J=0,nd=null,Qd(t)):(J=0,nd=null,$d(e,t,o,7));break;case 5:var s=null;switch(K.tag){case 26:s=K.memoizedState;case 5:case 27:var c=K;if(s?Ym(s):c.stateNode.complete){J=0,nd=null;var l=c.sibling;if(l!==null)K=l;else{var u=c.return;u===null?K=null:(K=u,ef(u))}break b}}J=0,nd=null,$d(e,t,o,5);break;case 6:J=0,nd=null,$d(e,t,o,6);break;case 8:Bd(),Y=6;break a;default:throw Error(i(462))}}Xd();break}catch(t){Hd(e,t)}while(1);return xa=ba=null,D.H=r,D.A=a,W=n,K===null?(G=null,q=0,ki(),Y):0}function Xd(){for(;K!==null&&!Ke();)Zd(K)}function Zd(e){var t=hl(e.alternate,e,od);e.memoizedProps=e.pendingProps,t===null?ef(e):K=t}function Qd(e){var t=e,n=t.alternate;switch(t.tag){case 15:case 0:t=Yc(n,t,t.pendingProps,t.type,void 0,q);break;case 11:t=Yc(n,t,t.pendingProps,t.type.render,t.ref,q);break;case 5:ls(t);var r=t;r===ca&&(I?(ma(r),r.tag===5&&r.stateNode!=null&&(F=r.stateNode)):(ma(r),I=!0));default:Cl(n,t),t=K=Bi(t,od),t=hl(n,t,od)}e.memoizedProps=e.pendingProps,t===null?ef(e):K=t}function $d(e,t,n,r){xa=ba=null,ls(t),lo=null,uo=0;var i=t.return;try{if(Pc(e,i,t,n,q)){Y=1,kc(e,qi(n,e.current)),K=null;return}}catch(t){if(i!==null)throw K=i,t;Y=1,kc(e,qi(n,e.current)),K=null;return}t.flags&32768?(I||r===1?e=!0:id||q&536870912?e=!1:(rd=e=!0,(r===2||r===9||r===3||r===6)&&(r=Fo.current,r!==null&&r.tag===13&&(r.flags|=16384))),tf(t,e)):ef(t)}function ef(e){var t=e;do{if(t.flags&32768){tf(t,rd);return}e=t.return;var n=xl(t.alternate,t,od);if(n!==null){K=n;return}if(t=t.sibling,t!==null){K=t;return}K=t=e}while(t!==null);Y===0&&(Y=5)}function tf(e,t){do{var n=Sl(e.alternate,e);if(n!==null){n.flags&=32767,K=n;return}if(n=e.return,n!==null&&(n.flags|=32768,n.subtreeFlags=0,n.deletions=null),!t&&(e=e.sibling,e!==null)){K=e;return}K=e=n}while(e!==null);Y=6,K=null}function nf(e,t,n,r,a,o,s,c,l,u,d,f){e.cancelPendingCommit=null;do df();while(X!==0);if(W&6)throw Error(i(327));if(t!==null){if(t===e.current)throw Error(i(177));e===G&&(K=G=null,q=0),xd=t,bd=e,Sd=n,wd=a,Td=r,rf(e,t,n,s,c,l,f)}}function rf(e,t,n,r,i,a,o){var s=t.lanes|t.childLanes;if(Cd=s,s|=Oi,xt(e,n,s,r,i,a),Dd=null,(n&335544064)===n?(Od=Ba(e),r=10262):(Od=null,r=10256),(t.subtreeFlags&r)!==0||(t.flags&r)!==0?(e.callbackNode=null,e.callbackPriority=0,yf(Qe,function(){return ff(),null})):(e.callbackNode=null,e.callbackPriority=0),Hl=!1,r=!!(t.flags&13878),t.subtreeFlags&13878||r){r=D.T,D.T=null,i=O.p,O.p=2,a=W,W|=4;try{gu(e,t,n)}finally{W=a,O.p=i,D.T=r}}X=1,Hl?Ed=Mp(o,e.containerInfo,Od,sf,cf,of,lf,ff,af,null,null):(sf(),cf(),lf())}function af(e){if(X!==0){var t=bd.onRecoverableError;t(e,{componentStack:null})}}function of(){X===3&&(X=0,Iu(xd,bd),X=4)}function sf(){if(X===1){X=0;var e=bd,t=xd,n=Sd,r=!!(t.flags&13878);if(t.subtreeFlags&13878||r){r=D.T,D.T=null;var i=O.p;O.p=2;var a=W;W|=4;try{pu=mu=!1,Mu(t,e,n),n=cp;var o=Qr(e.containerInfo),s=n.focusedElem,c=n.selectionRange;if(o!==s&&s&&s.ownerDocument&&Zr(s.ownerDocument.documentElement,s)){if(c!==null&&$r(s)){var l=c.start,u=c.end;if(u===void 0&&(u=l),`selectionStart`in s)s.selectionStart=l,s.selectionEnd=Math.min(u,s.value.length);else{var d=s.ownerDocument||document,f=d&&d.defaultView||window;if(f.getSelection){var p=f.getSelection(),m=s.textContent.length,h=Math.min(c.start,m),g=c.end===void 0?h:Math.min(c.end,m);!p.extend&&h>g&&(o=g,g=h,h=o);var _=Xr(s,h),v=Xr(s,g);if(_&&v&&(p.rangeCount!==1||p.anchorNode!==_.node||p.anchorOffset!==_.offset||p.focusNode!==v.node||p.focusOffset!==v.offset)){var y=d.createRange();y.setStart(_.node,_.offset),p.removeAllRanges(),h>g?(p.addRange(y),p.extend(v.node,v.offset)):(y.setEnd(v.node,v.offset),p.addRange(y))}}}}for(d=[],p=s;p=p.parentNode;)p.nodeType===1&&d.push({element:p,left:p.scrollLeft,top:p.scrollTop});for(typeof s.focus==`function`&&s.focus(),s=0;s<d.length;s++){var b=d[s];b.element.scrollLeft=b.left,b.element.scrollTop=b.top}}gh=!!sp,cp=sp=null}finally{W=a,O.p=i,D.T=r}}e.current=t,X=2}}function cf(){if(X===2){X=0;var e=bd,t=xd,n=!!(t.flags&8772);if(t.subtreeFlags&8772||n){n=D.T,D.T=null;var r=O.p;O.p=2;var i=W;W|=4;try{_u(e,t.alternate,t)}finally{W=i,O.p=r,D.T=n}}X=3}}function lf(){if(X===4||X===3){X=0;var e=Ed;Ed=null,qe();var t=bd,n=xd,r=Sd,i=Td,a=(r&335544064)===r?10262:10256;if((n.subtreeFlags&a)!==0||(n.flags&a)!==0?X=5:(X=0,xd=bd=null,uf(t,t.pendingLanes)),a=t.pendingLanes,a===0&&(yd=null),Et(r),n=n.stateNode,it&&typeof it.onCommitFiberRoot==`function`)try{it.onCommitFiberRoot(rt,n,void 0,(n.current.flags&128)==128)}catch{}if(i!==null){n=D.T,a=O.p,O.p=2,D.T=null;try{for(var o=t.onRecoverableError,s=0;s<i.length;s++){var c=i[s];o(c.value,{componentStack:c.stack})}}finally{D.T=n,O.p=a}}if(i=Dd,o=Od,Od=null,i!==null&&(Dd=null,o===null&&(o=[]),e!==null))for(c=0;c<i.length;c++)n=(0,i[c])(o),n!==void 0&&e.finished.finally(n);Sd&3&&df(),Ef(t),a=t.pendingLanes,r&261930&&a&42?t===Ad?kd++:(kd=0,Ad=t):(kd=0,Ad=null),Df(0,!1)}}function uf(e,t){(e.pooledCacheLanes&=t)===0&&(t=e.pooledCache,t!=null&&(e.pooledCache=null,La(t)))}function df(){return Ed!==null&&(Ed.skipTransition(),Ed=null),sf(),cf(),lf(),ff()}function ff(){if(X!==5)return!1;var e=bd,t=Cd;Cd=0;var n=Et(Sd),r=D.T,a=O.p;try{O.p=32>n?32:n,D.T=null,n=wd,wd=null;var o=bd,s=Sd;if(X=0,xd=bd=null,Sd=0,W&6)throw Error(i(331));var c=W;if(W|=4,Zu(o.current),Uu(o,o.current,s,n),W=c,Df(0,!1),it&&typeof it.onPostCommitFiberRoot==`function`)try{it.onPostCommitFiberRoot(rt,o)}catch{}return!0}finally{O.p=a,D.T=r,uf(e,t)}}function pf(e,t,n){t=qi(n,t),t=jc(e.stateNode,t,2),e=So(e,t,2),e!==null&&(bt(e,2),Ef(e))}function Z(e,t,n){if(e.tag===3)pf(e,e,n);else for(;t!==null;){if(t.tag===3){pf(t,e,n);break}if(t.tag===1){var r=t.stateNode;if(typeof t.type.getDerivedStateFromError==`function`||typeof r.componentDidCatch==`function`&&(yd===null||!yd.has(r))){e=qi(n,e),n=Mc(2),r=So(t,n,2),r!==null&&(Nc(n,r,t,e),bt(r,2),Ef(r));break}}t=t.return}}function mf(e,t,n){var r=e.pingCache;if(r===null){r=e.pingCache=new td;var i=new Set;r.set(t,i)}else i=r.get(t),i===void 0&&(i=new Set,r.set(t,i));i.has(n)||(ad=!0,i.add(n),e=hf.bind(null,e,t,n),t.then(e,e))}function hf(e,t,n){var r=e.pingCache;r!==null&&r.delete(t),e.pingedLanes|=e.suspendedLanes&n,e.warmLanes&=~n,G===e&&(q&n)===n&&(Y===4||Y===3&&(q&62914560)===q&&300>Je()-hd?W&2?ld|=n:Vd(e,0):ld|=n,dd===q&&(dd=0)),Ef(e)}function gf(e,t){t===0&&(t=vt()),e=Mi(e,t),e!==null&&(bt(e,t),Ef(e))}function _f(e){var t=e.memoizedState,n=0;t!==null&&(n=t.retryLane),gf(e,n)}function vf(e,t){var n=0;switch(e.tag){case 31:case 13:var r=e.stateNode,a=e.memoizedState;a!==null&&(n=a.retryLane);break;case 19:r=e.stateNode;break;case 22:r=e.stateNode._retryCache;break;default:throw Error(i(314))}r!==null&&r.delete(t),gf(e,n)}function yf(e,t){return We(e,t)}var bf=null,xf=null,Sf=!1,Cf=!1,wf=!1,Tf=0;function Ef(e){e!==xf&&e.next===null&&(xf===null?bf=xf=e:xf=xf.next=e),Cf=!0,Sf||(Sf=!0,Nf())}function Df(e,t){if(!wf&&Cf){wf=!0;do for(var n=!1,r=bf;r!==null;){if(!t){if(e!==0){var i=r.pendingLanes;if(i===0)var a=0;else{var o=r.suspendedLanes,s=r.pingedLanes;a=(1<<31-ot(42|e)+1)-1,a&=i&~(o&~s),a=a&201326741?a&201326741|1:a?a|2:0}a!==0&&(n=!0,Mf(r,a))}else a=q,a=mt(r,r===G?a:0,r.cancelPendingCommit!==null||r.timeoutHandle!==-1),!(a&3)||ht(r,a)||(n=!0,Mf(r,a))}r=r.next}while(n);wf=!1}}function Of(){kf()}function kf(){Cf=Sf=!1;var e=0;Tf!==0&&hp()&&(e=Tf);for(var t=Je(),n=null,r=bf;r!==null;){var i=r.next,a=Af(r,t);a===0?(r.next=null,n===null?bf=i:n.next=i,i===null&&(xf=n)):(n=r,(e!==0||a&3)&&(Cf=!0)),r=i}X!==0&&X!==5||Df(e,!1),Tf!==0&&(Tf=0)}function Af(e,t){for(var n=e.suspendedLanes,r=e.pingedLanes,i=e.expirationTimes,a=e.pendingLanes&-62914561;0<a;){var o=31-ot(a),s=1<<o,c=i[o];c===-1?((s&n)===0||(s&r)!==0)&&(i[o]=_t(s,t)):c<=t&&(e.expiredLanes|=s),a&=~s}if(t=G,n=q,n=mt(e,e===t?n:0,e.cancelPendingCommit!==null||e.timeoutHandle!==-1),r=e.callbackNode,n===0||e===t&&(J===2||J===9)||e.cancelPendingCommit!==null)return r!==null&&r!==null&&Ge(r),e.callbackNode=null,e.callbackPriority=0;if(!(n&3)||ht(e,n)){if(t=n&-n,t===e.callbackPriority)return t;switch(r!==null&&Ge(r),Et(n)){case 2:case 8:n=Ze;break;case 32:n=Qe;break;case 268435456:n=et;break;default:n=Qe}return r=jf.bind(null,e),n=We(n,r),e.callbackPriority=t,e.callbackNode=n,t}return r!==null&&r!==null&&Ge(r),e.callbackPriority=2,e.callbackNode=null,2}function jf(e,t){if(X!==0&&X!==5)return e.callbackNode=null,e.callbackPriority=0,null;var n=e.callbackNode;if(df()&&e.callbackNode!==n)return null;var r=q;return r=mt(e,e===G?r:0,e.cancelPendingCommit!==null||e.timeoutHandle!==-1),r===0?null:(Fd(e,r,t),Af(e,Je()),e.callbackNode!=null&&e.callbackNode===n?jf.bind(null,e):null)}function Mf(e,t){if(df())return null;Fd(e,t,!0)}function Nf(){bp(function(){W&6?We(Xe,Of):kf()})}function Pf(){if(Tf===0){var e=Ua;e===0&&(e=ut,ut<<=1,!(ut&261888)&&(ut=256)),Tf=e}return Tf}function Ff(e){return e==null||typeof e==`symbol`||typeof e==`boolean`?null:typeof e==`function`?e:Tn(e)}function If(e,t,n,r,i){if(t===`submit`&&n&&n.stateNode===i){var a=Ff((i[jt]||null).action),o=r.submitter;o&&(t=(t=o[jt]||null)?Ff(t.formAction):o.getAttribute(`formAction`),t!==null&&(a=t,o=null));var s=new Wn(`action`,`action`,null,r,i);e.push({event:s,listeners:[{instance:null,listener:function(){if(r.defaultPrevented){if(Tf!==0){var e=new FormData(i,o);rc(n,{pending:!0,data:e,method:i.method,action:a},null,e)}}else typeof a==`function`&&(s.preventDefault(),e=new FormData(i,o),rc(n,{pending:!0,data:e,method:i.method,action:a},a,e))},currentTarget:i}]})}}for(var Lf=0;Lf<yi.length;Lf++){var Rf=yi[Lf];bi(Rf.toLowerCase(),`on`+(Rf[0].toUpperCase()+Rf.slice(1)))}bi(di,`onAnimationEnd`),bi(fi,`onAnimationIteration`),bi(pi,`onAnimationStart`),bi(`dblclick`,`onDoubleClick`),bi(`focusin`,`onFocus`),bi(`focusout`,`onBlur`),bi(mi,`onTransitionRun`),bi(hi,`onTransitionStart`),bi(gi,`onTransitionCancel`),bi(_i,`onTransitionEnd`),Yt(`onMouseEnter`,[`mouseout`,`mouseover`]),Yt(`onMouseLeave`,[`mouseout`,`mouseover`]),Yt(`onPointerEnter`,[`pointerout`,`pointerover`]),Yt(`onPointerLeave`,[`pointerout`,`pointerover`]),Jt(`onChange`,`change click focusin focusout input keydown keyup selectionchange`.split(` `)),Jt(`onSelect`,`focusout contextmenu dragend focusin keydown keyup mousedown mouseup selectionchange`.split(` `)),Jt(`onBeforeInput`,[`compositionend`,`keypress`,`textInput`,`paste`]),Jt(`onCompositionEnd`,`compositionend focusout keydown keypress keyup mousedown`.split(` `)),Jt(`onCompositionStart`,`compositionstart focusout keydown keypress keyup mousedown`.split(` `)),Jt(`onCompositionUpdate`,`compositionupdate focusout keydown keypress keyup mousedown`.split(` `));var zf=`abort canplay canplaythrough durationchange emptied encrypted ended error loadeddata loadedmetadata loadstart pause play playing progress ratechange resize seeked seeking stalled suspend timeupdate volumechange waiting`.split(` `),Bf=new Set(`beforetoggle cancel close invalid load scroll scrollend toggle`.split(` `).concat(zf));function Vf(e,t){t=!!(t&4);for(var n=0;n<e.length;n++){var r=e[n],i=r.event;r=r.listeners;a:{var a=void 0;if(t)for(var o=r.length-1;0<=o;o--){var s=r[o],c=s.instance,l=s.currentTarget;if(s=s.listener,c!==a&&i.isPropagationStopped())break a;a=s,i.currentTarget=l;try{a(i)}catch(e){Ti(e)}i.currentTarget=null,a=c}else for(o=0;o<r.length;o++){if(s=r[o],c=s.instance,l=s.currentTarget,s=s.listener,c!==a&&i.isPropagationStopped())break a;a=s,i.currentTarget=l;try{a(i)}catch(e){Ti(e)}i.currentTarget=null,a=c}}}}function Q(e,t){var n=t[Nt];n===void 0&&(n=t[Nt]=new Set);var r=e+`__bubble`;n.has(r)||(Gf(t,e,2,!1),n.add(r))}function Hf(e,t,n){var r=0;t&&(r|=4),Gf(n,e,r,t)}var Uf=`_reactListening`+Math.random().toString(36).slice(2);function Wf(e){if(!e[Uf]){e[Uf]=!0,Kt.forEach(function(t){t!==`selectionchange`&&(Bf.has(t)||Hf(t,!1,e),Hf(t,!0,e))});var t=e.nodeType===9?e:e.ownerDocument;t===null||t[Uf]||(t[Uf]=!0,Hf(`selectionchange`,!1,t))}}function Gf(e,t,n,r){switch(Ch(t)){case 2:var i=_h;break;case 8:i=vh;break;default:i=yh}n=i.bind(null,t,n,e),i=void 0,!In||t!==`touchstart`&&t!==`touchmove`&&t!==`wheel`||(i=!0),r?i===void 0?e.addEventListener(t,n,!0):e.addEventListener(t,n,{capture:!0,passive:i}):i===void 0?e.addEventListener(t,n,!1):e.addEventListener(t,n,{passive:i})}function Kf(e,t,n,r,i){var a=r;if(!(t&1)&&!(t&2)&&r!==null)a:for(;;){if(r===null)return;var s=r.tag;if(s===3||s===4){var c=r.stateNode.containerInfo;if(c===i)break;if(s===4)for(s=r.return;s!==null;){var l=s.tag;if((l===3||l===4)&&s.stateNode.containerInfo===i)return;s=s.return}for(;c!==null;){if(s=Bt(c),s===null)return;if(l=s.tag,l===5||l===6||l===26||l===27){r=a=s;continue a}c=c.parentNode}}r=r.return}Nn(function(){var r=a,i=On(n),s=[];a:{var c=vi.get(e);if(c!==void 0){var l=Wn,u=e;switch(e){case`keypress`:if(Bn(n)===0)break a;case`keydown`:case`keyup`:l=cr;break;case`focusin`:u=`focus`,l=$n;break;case`focusout`:u=`blur`,l=$n;break;case`beforeblur`:case`afterblur`:l=$n;break;case`click`:if(n.button===2)break a;case`auxclick`:case`dblclick`:case`mousedown`:case`mousemove`:case`mouseup`:case`mouseout`:case`mouseover`:case`contextmenu`:l=Zn;break;case`drag`:case`dragend`:case`dragenter`:case`dragexit`:case`dragleave`:case`dragover`:case`dragstart`:case`drop`:l=Qn;break;case`touchcancel`:case`touchend`:case`touchmove`:case`touchstart`:l=dr;break;case di:case fi:case pi:l=er;break;case _i:l=fr;break;case`scroll`:case`scrollend`:l=Kn;break;case`wheel`:l=pr;break;case`copy`:case`cut`:case`paste`:l=tr;break;case`gotpointercapture`:case`lostpointercapture`:case`pointercancel`:case`pointerdown`:case`pointermove`:case`pointerout`:case`pointerover`:case`pointerup`:l=lr;break;case`submit`:l=ur;break;case`toggle`:case`beforetoggle`:l=mr}var d=!!(t&4),f=!d&&(e===`scroll`||e===`scrollend`),p=d?c===null?null:c+`Capture`:c;d=[];for(var m=r,h;m!==null;){var g=m;if(h=g.stateNode,g=g.tag,g!==5&&g!==26&&g!==27||h===null||p===null||(g=Pn(m,p),g!=null&&d.push(qf(m,g,h))),f)break;m=m.return}0<d.length&&(c=new l(c,u,null,n,i),s.push({event:c,listeners:d}))}}if(!(t&7)){a:{if(l=e===`mouseover`||e===`pointerover`,c=e===`mouseout`||e===`pointerout`,l&&n!==Dn&&(u=n.relatedTarget||n.fromElement)&&(Bt(u)||u[Mt]))break a;(c||l)&&(u=i.window===i?i:(l=i.ownerDocument)?l.defaultView||l.parentWindow:window,c?(l=n.relatedTarget||n.toElement,c=r,l=l?Bt(l):null,l!==null&&(f=o(l),d=l.tag,l!==f||d!==5&&d!==27&&d!==6)&&(l=null)):(c=null,l=r),c!==l&&(d=Zn,g=`onMouseLeave`,p=`onMouseEnter`,m=`mouse`,(e===`pointerout`||e===`pointerover`)&&(d=lr,g=`onPointerLeave`,p=`onPointerEnter`,m=`pointer`),f=c==null?u:Ht(c),h=l==null?u:Ht(l),u=new d(g,m+`leave`,c,n,i),u.target=f,u.relatedTarget=h,g=null,Bt(i)===r&&(d=new d(p,m+`enter`,l,n,i),d.target=h,d.relatedTarget=f,g=d),f=g,d=c&&l?te(c,l,Yf):null,c!==null&&Xf(s,u,c,d,!1),l!==null&&f!==null&&Xf(s,f,l,d,!0)))}a:{if(c=r?Ht(r):window,l=c.nodeName&&c.nodeName.toLowerCase(),l===`select`||l===`input`&&c.type===`file`)var _=Pr;else if(Or(c)){if(Fr)_=Wr;else{_=Hr;var v=Vr}}else l=c.nodeName,!l||l.toLowerCase()!==`input`||c.type!==`checkbox`&&c.type!==`radio`?r&&Sn(r.elementType)&&(_=Pr):_=Ur;if(_&&=_(e,r)){kr(s,_,n,i);break a}v&&v(e,c,r)}switch(v=r?Ht(r):window,e){case`focusin`:(Or(v)||v.contentEditable===`true`)&&(ti=v,ni=r,ri=null);break;case`focusout`:ri=ni=ti=null;break;case`mousedown`:ii=!0;break;case`contextmenu`:case`mouseup`:case`dragend`:ii=!1,ai(s,n,i);break;case`selectionchange`:if(ei)break;case`keydown`:case`keyup`:ai(s,n,i)}var y;if(gr)b:{switch(e){case`compositionstart`:var b=`onCompositionStart`;break b;case`compositionend`:b=`onCompositionEnd`;break b;case`compositionupdate`:b=`onCompositionUpdate`;break b}b=void 0}else wr?Sr(e,n)&&(b=`onCompositionEnd`):e===`keydown`&&n.keyCode===229&&(b=`onCompositionStart`);b&&(yr&&n.locale!==`ko`&&(wr||b!==`onCompositionStart`?b===`onCompositionEnd`&&wr&&(y=zn()):(Rn=i,M=`value`in Rn?Rn.value:Rn.textContent,wr=!0)),v=Jf(r,b),0<v.length&&(b=new nr(b,e,null,n,i),s.push({event:b,listeners:v}),y?b.data=y:(y=Cr(n),y!==null&&(b.data=y)))),(y=vr?Tr(e,n):Er(e,n))&&(b=Jf(r,`onBeforeInput`),0<b.length&&(v=new nr(`onBeforeInput`,`beforeinput`,null,n,i),s.push({event:v,listeners:b}),v.data=y)),If(s,e,r,n,i)}Vf(s,t)})}function qf(e,t,n){return{instance:e,listener:t,currentTarget:n}}function Jf(e,t){for(var n=t+`Capture`,r=[];e!==null;){var i=e,a=i.stateNode;if(i=i.tag,i!==5&&i!==26&&i!==27||a===null||(i=Pn(e,n),i!=null&&r.unshift(qf(e,i,a)),i=Pn(e,t),i!=null&&r.push(qf(e,i,a))),e.tag===3)return r;e=e.return}return[]}function Yf(e){if(e===null)return null;do e=e.return;while(e&&e.tag!==5&&e.tag!==27);return e||null}function Xf(e,t,n,r,i){for(var a=t._reactName,o=[];n!==null&&n!==r;){var s=n,c=s.alternate,l=s.stateNode;if(s=s.tag,c!==null&&c===r)break;s!==5&&s!==26&&s!==27||l===null||(c=l,i?(l=Pn(n,a),l!=null&&o.unshift(qf(n,l,c))):i||(l=Pn(n,a),l!=null&&o.push(qf(n,l,c)))),n=n.return}o.length!==0&&e.push({event:t,listeners:o})}var Zf=/\r\n?/g,Qf=/\u0000|\uFFFD/g;function $f(e){return(typeof e==`string`?e:``+e).replace(Zf,`
`).replace(Qf,``)}function ep(e,t){return t=$f(t),$f(e)===t}function $(e,t,n,r,a,o){switch(n){case`children`:if(typeof r==`string`)t===`body`||t===`textarea`&&r===``||vn(e,r);else if(typeof r==`number`||typeof r==`bigint`)t!==`body`&&vn(e,``+r);else return;break;case`className`:nn(e,`class`,r);break;case`tabIndex`:nn(e,`tabindex`,r);break;case`dir`:case`role`:case`viewBox`:case`width`:case`height`:nn(e,n,r);break;case`style`:xn(e,r,o);return;case`data`:if(t!==`object`){nn(e,`data`,r);break}case`src`:case`href`:if(r===``&&(t!==`a`||n!==`href`)||r==null||typeof r==`function`||typeof r==`symbol`||typeof r==`boolean`){e.removeAttribute(n);break}r=Tn(r),e.setAttribute(n,r);break;case`action`:case`formAction`:if(typeof r==`function`){e.setAttribute(n,`javascript:throw new Error('A React form was unexpectedly submitted. If you called form.submit() manually, consider using form.requestSubmit() instead. If you\\'re trying to use event.stopPropagation() in a submit event handler, consider also calling event.preventDefault().')`);break}if(typeof o==`function`&&(n===`formAction`?(t!==`input`&&$(e,t,`name`,a.name,a,null),$(e,t,`formEncType`,a.formEncType,a,null),$(e,t,`formMethod`,a.formMethod,a,null),$(e,t,`formTarget`,a.formTarget,a,null)):($(e,t,`encType`,a.encType,a,null),$(e,t,`method`,a.method,a,null),$(e,t,`target`,a.target,a,null))),r==null||typeof r==`symbol`||typeof r==`boolean`){e.removeAttribute(n);break}r=Tn(r),e.setAttribute(n,r);break;case`onClick`:r!=null&&(e.onclick=En);return;case`onScroll`:r!=null&&Q(`scroll`,e);return;case`onScrollEnd`:r!=null&&Q(`scrollend`,e);return;case`dangerouslySetInnerHTML`:if(r!=null){if(typeof r!=`object`||!(`__html`in r))throw Error(i(61));if(n=r.__html,n!=null){if(a.children!=null)throw Error(i(60));o?.__html!==n&&(e.innerHTML=n)}}break;case`multiple`:e.multiple=r&&typeof r!=`function`&&typeof r!=`symbol`;break;case`muted`:e.muted=r&&typeof r!=`function`&&typeof r!=`symbol`;break;case`suppressContentEditableWarning`:case`suppressHydrationWarning`:case`defaultValue`:case`defaultChecked`:case`innerHTML`:case`ref`:break;case`autoFocus`:break;case`xlinkHref`:if(r==null||typeof r==`function`||typeof r==`boolean`||typeof r==`symbol`){e.removeAttribute(`xlink:href`);break}n=Tn(r),e.setAttributeNS(`http://www.w3.org/1999/xlink`,`xlink:href`,n);break;case`contentEditable`:case`spellCheck`:case`draggable`:case`value`:case`autoReverse`:case`externalResourcesRequired`:case`focusable`:case`preserveAlpha`:r!=null&&typeof r!=`function`&&typeof r!=`symbol`?e.setAttribute(n,r):e.removeAttribute(n);break;case`inert`:case`allowFullScreen`:case`async`:case`autoPlay`:case`controls`:case`credentialless`:case`default`:case`defer`:case`disabled`:case`disablePictureInPicture`:case`disableRemotePlayback`:case`formNoValidate`:case`hidden`:case`loop`:case`noModule`:case`noValidate`:case`open`:case`playsInline`:case`readOnly`:case`required`:case`reversed`:case`scoped`:case`seamless`:case`itemScope`:r&&typeof r!=`function`&&typeof r!=`symbol`?e.setAttribute(n,``):e.removeAttribute(n);break;case`capture`:case`download`:!0===r?e.setAttribute(n,``):!1!==r&&r!=null&&typeof r!=`function`&&typeof r!=`symbol`?e.setAttribute(n,r):e.removeAttribute(n);break;case`cols`:case`rows`:case`size`:case`span`:r!=null&&typeof r!=`function`&&typeof r!=`symbol`&&!isNaN(r)&&1<=r?e.setAttribute(n,r):e.removeAttribute(n);break;case`rowSpan`:case`start`:r==null||typeof r==`function`||typeof r==`symbol`||isNaN(r)?e.removeAttribute(n):e.setAttribute(n,r);break;case`popover`:Q(`beforetoggle`,e),Q(`toggle`,e),tn(e,`popover`,r);break;case`xlinkActuate`:rn(e,`http://www.w3.org/1999/xlink`,`xlink:actuate`,r);break;case`xlinkArcrole`:rn(e,`http://www.w3.org/1999/xlink`,`xlink:arcrole`,r);break;case`xlinkRole`:rn(e,`http://www.w3.org/1999/xlink`,`xlink:role`,r);break;case`xlinkShow`:rn(e,`http://www.w3.org/1999/xlink`,`xlink:show`,r);break;case`xlinkTitle`:rn(e,`http://www.w3.org/1999/xlink`,`xlink:title`,r);break;case`xlinkType`:rn(e,`http://www.w3.org/1999/xlink`,`xlink:type`,r);break;case`xmlBase`:rn(e,`http://www.w3.org/XML/1998/namespace`,`xml:base`,r);break;case`xmlLang`:rn(e,`http://www.w3.org/XML/1998/namespace`,`xml:lang`,r);break;case`xmlSpace`:rn(e,`http://www.w3.org/XML/1998/namespace`,`xml:space`,r);break;case`is`:tn(e,`is`,r);break;case`innerText`:case`textContent`:return;default:if(!(2<n.length)||n[0]!==`o`&&n[0]!==`O`||n[1]!==`n`&&n[1]!==`N`)n=Cn.get(n)||n,tn(e,n,r);else return}j=!0}function tp(e,t,n,r,a,o){switch(n){case`style`:xn(e,r,o);return;case`dangerouslySetInnerHTML`:if(r!=null){if(typeof r!=`object`||!(`__html`in r))throw Error(i(61));if(n=r.__html,n!=null){if(a.children!=null)throw Error(i(60));o?.__html!==n&&(e.innerHTML=n)}}break;case`children`:if(typeof r==`string`)vn(e,r);else if(typeof r==`number`||typeof r==`bigint`)vn(e,``+r);else return;break;case`onScroll`:r!=null&&Q(`scroll`,e);return;case`onScrollEnd`:r!=null&&Q(`scrollend`,e);return;case`onClick`:r!=null&&(e.onclick=En);return;case`suppressContentEditableWarning`:case`suppressHydrationWarning`:case`innerHTML`:case`ref`:return;case`innerText`:case`textContent`:return;default:if(!qt.hasOwnProperty(n))a:{if(n[0]===`o`&&n[1]===`n`&&(a=n.endsWith(`Capture`),o=n.slice(2,a?n.length-7:void 0),t=e[jt]||null,t=t==null?null:t[n],typeof t==`function`&&e.removeEventListener(o,t,a),typeof r==`function`)){typeof t!=`function`&&t!==null&&(n in e?e[n]=null:e.hasAttribute(n)&&e.removeAttribute(n)),e.addEventListener(o,r,a);break a}j=!0,n in e?e[n]=r:!0===r?e.setAttribute(n,``):tn(e,n,r)}return}j=!0}function np(e,t,n){switch(t){case`div`:case`span`:case`svg`:case`path`:case`a`:case`g`:case`p`:case`li`:break;case`img`:Q(`error`,e),Q(`load`,e);var r=!1,a=!1,o;for(o in n)if(n.hasOwnProperty(o)){var s=n[o];if(s!=null)switch(o){case`src`:r=!0;break;case`srcSet`:a=!0;break;case`children`:case`dangerouslySetInnerHTML`:throw Error(i(137,t));default:$(e,t,o,s,n,null)}}a&&$(e,t,`srcSet`,n.srcSet,n,null),r&&$(e,t,`src`,n.src,n,null);return;case`input`:Q(`invalid`,e);var c=o=s=a=null,l=null,u=null;for(r in n)if(n.hasOwnProperty(r)){var d=n[r];if(d!=null)switch(r){case`name`:a=d;break;case`type`:s=d;break;case`checked`:l=d;break;case`defaultChecked`:u=d;break;case`value`:o=d;break;case`defaultValue`:c=d;break;case`children`:case`dangerouslySetInnerHTML`:if(d!=null)throw Error(i(137,t));break;default:$(e,t,r,d,n,null)}}pn(e,o,c,l,u,s,a,!1);return;case`select`:for(a in Q(`invalid`,e),r=s=o=null,n)if(n.hasOwnProperty(a)&&(c=n[a],c!=null))switch(a){case`value`:o=c;break;case`defaultValue`:s=c;break;case`multiple`:r=c;default:$(e,t,a,c,n,null)}t=o,n=s,e.multiple=!!r,t==null?n!=null&&hn(e,!!r,n,!0):hn(e,!!r,t,!1);return;case`textarea`:for(s in Q(`invalid`,e),o=a=r=null,n)if(n.hasOwnProperty(s)&&(c=n[s],c!=null))switch(s){case`value`:r=c;break;case`defaultValue`:a=c;break;case`children`:o=c;break;case`dangerouslySetInnerHTML`:if(c!=null)throw Error(i(91));break;default:$(e,t,s,c,n,null)}_n(e,r,a,o);return;case`option`:for(l in n)if(n.hasOwnProperty(l)&&(r=n[l],r!=null))switch(l){case`selected`:e.selected=r&&typeof r!=`function`&&typeof r!=`symbol`;break;default:$(e,t,l,r,n,null)}return;case`dialog`:Q(`beforetoggle`,e),Q(`toggle`,e),Q(`cancel`,e),Q(`close`,e);break;case`iframe`:case`object`:Q(`load`,e);break;case`video`:case`audio`:for(r=0;r<zf.length;r++)Q(zf[r],e);break;case`image`:Q(`error`,e),Q(`load`,e);break;case`details`:Q(`toggle`,e);break;case`embed`:case`source`:case`link`:Q(`error`,e),Q(`load`,e);case`area`:case`base`:case`br`:case`col`:case`hr`:case`keygen`:case`meta`:case`param`:case`track`:case`wbr`:case`menuitem`:for(u in n)if(n.hasOwnProperty(u)&&(r=n[u],r!=null))switch(u){case`children`:case`dangerouslySetInnerHTML`:throw Error(i(137,t));default:$(e,t,u,r,n,null)}return;default:if(Sn(t)){for(d in n)n.hasOwnProperty(d)&&(r=n[d],r!==void 0&&tp(e,t,d,r,n,void 0));return}}for(c in n)n.hasOwnProperty(c)&&(r=n[c],r!=null&&$(e,t,c,r,n,null))}var rp={};function ip(e,t,n,r){switch(t){case`div`:case`span`:case`svg`:case`path`:case`a`:case`g`:case`p`:case`li`:break;case`input`:var a=null,o=null,s=null,c=null,l=null,u=null,d=null;for(m in n){var f=n[m];if(n.hasOwnProperty(m)&&f!=null)switch(m){case`checked`:break;case`value`:break;case`defaultValue`:l=f;default:r.hasOwnProperty(m)||$(e,t,m,null,r,f)}}for(var p in r){var m=r[p];if(f=n[p],r.hasOwnProperty(p)&&(m!=null||f!=null))switch(p){case`type`:m!==f&&(j=!0),o=m;break;case`name`:m!==f&&(j=!0),a=m;break;case`checked`:m!==f&&(j=!0),u=m;break;case`defaultChecked`:m!==f&&(j=!0),d=m;break;case`value`:m!==f&&(j=!0),s=m;break;case`defaultValue`:m!==f&&(j=!0),c=m;break;case`children`:case`dangerouslySetInnerHTML`:if(m!=null)throw Error(i(137,t));break;default:m!==f&&$(e,t,p,m,r,f)}}fn(e,s,c,l,u,d,o,a);return;case`select`:for(o in m=s=c=p=null,n)if(l=n[o],n.hasOwnProperty(o)&&l!=null)switch(o){case`value`:break;case`multiple`:m=l;default:r.hasOwnProperty(o)||$(e,t,o,null,r,l)}for(a in r)if(o=r[a],l=n[a],r.hasOwnProperty(a)&&(o!=null||l!=null))switch(a){case`value`:o!==l&&(j=!0),p=o;break;case`defaultValue`:o!==l&&(j=!0),c=o;break;case`multiple`:o!==l&&(j=!0),s=o;default:o!==l&&$(e,t,a,o,r,l)}t=c,n=s,r=m,p==null?!!r!=!!n&&(t==null?hn(e,!!n,n?[]:``,!1):hn(e,!!n,t,!0)):hn(e,!!n,p,!1);return;case`textarea`:for(c in m=p=null,n)if(a=n[c],n.hasOwnProperty(c)&&a!=null&&!r.hasOwnProperty(c))switch(c){case`value`:break;case`children`:break;default:$(e,t,c,null,r,a)}for(s in r)if(a=r[s],o=n[s],r.hasOwnProperty(s)&&(a!=null||o!=null))switch(s){case`value`:a!==o&&(j=!0),p=a;break;case`defaultValue`:a!==o&&(j=!0),m=a;break;case`children`:break;case`dangerouslySetInnerHTML`:if(a!=null)throw Error(i(91));break;default:a!==o&&$(e,t,s,a,r,o)}gn(e,p,m);return;case`option`:for(var h in n)if(p=n[h],n.hasOwnProperty(h)&&p!=null&&!r.hasOwnProperty(h))switch(h){case`selected`:e.selected=!1;break;default:$(e,t,h,null,r,p)}for(l in r)if(p=r[l],m=n[l],r.hasOwnProperty(l)&&p!==m&&(p!=null||m!=null))switch(l){case`selected`:p!==m&&(j=!0),e.selected=p&&typeof p!=`function`&&typeof p!=`symbol`;break;default:$(e,t,l,p,r,m)}return;case`img`:case`link`:case`area`:case`base`:case`br`:case`col`:case`embed`:case`hr`:case`keygen`:case`meta`:case`param`:case`source`:case`track`:case`wbr`:case`menuitem`:for(var g in n)p=n[g],n.hasOwnProperty(g)&&p!=null&&!r.hasOwnProperty(g)&&$(e,t,g,null,r,p);for(u in r)if(p=r[u],m=n[u],r.hasOwnProperty(u)&&p!==m&&(p!=null||m!=null))switch(u){case`children`:case`dangerouslySetInnerHTML`:if(p!=null)throw Error(i(137,t));break;default:$(e,t,u,p,r,m)}return;default:if(Sn(t)){for(var _ in n)p=n[_],n.hasOwnProperty(_)&&p!==void 0&&!r.hasOwnProperty(_)&&tp(e,t,_,void 0,r,p);for(d in r)p=r[d],m=n[d],!r.hasOwnProperty(d)||p===m||p===void 0&&m===void 0||tp(e,t,d,p,r,m);return}}for(var v in n)p=n[v],n.hasOwnProperty(v)&&p!=null&&!r.hasOwnProperty(v)&&$(e,t,v,null,r,p);for(f in r)p=r[f],m=n[f],!r.hasOwnProperty(f)||p===m||p==null&&m==null||$(e,t,f,p,r,m)}function ap(e){switch(e){case`css`:case`script`:case`font`:case`img`:case`image`:case`input`:case`link`:return!0;default:return!1}}function op(){if(typeof performance.getEntriesByType==`function`){for(var e=0,t=0,n=performance.getEntriesByType(`resource`),r=0;r<n.length;r++){var i=n[r],a=i.transferSize,o=i.initiatorType,s=i.duration;if(a&&s&&ap(o)){for(o=0,s=i.responseEnd,r+=1;r<n.length;r++){var c=n[r],l=c.startTime;if(l>s)break;var u=c.transferSize,d=c.initiatorType;u&&ap(d)&&(c=c.responseEnd,o+=u*(c<s?1:(s-l)/(c-l)))}if(--r,t+=8*(a+o)/(i.duration/1e3),e++,10<e)break}}if(0<e)return t/e/1e6}return navigator.connection&&(e=navigator.connection.downlink,typeof e==`number`)?e:5}var sp=null,cp=null;function lp(e){return e.nodeType===9?e:e.ownerDocument}function up(e){switch(e){case`http://www.w3.org/2000/svg`:return 1;case`http://www.w3.org/1998/Math/MathML`:return 2;default:return 0}}function dp(e,t){if(e===0)switch(t){case`svg`:return 1;case`math`:return 2;default:return 0}return e===1&&t===`foreignObject`?0:e}function fp(e,t,n,r){return n=lp(n).createElement(e),n[At]=r,n[jt]=t,np(n,e,t),Wt(n),n}function pp(e,t){return e===`textarea`||e===`noscript`||typeof t.children==`string`||typeof t.children==`number`||typeof t.children==`bigint`||typeof t.dangerouslySetInnerHTML==`object`&&t.dangerouslySetInnerHTML!==null&&t.dangerouslySetInnerHTML.__html!=null}var mp=null;function hp(){var e=window.event;return e&&e.type===`popstate`?e!==mp&&(mp=e,!0):(mp=null,!1)}var gp=typeof setTimeout==`function`?setTimeout:void 0,_p=typeof clearTimeout==`function`?clearTimeout:void 0,vp=typeof Promise==`function`?Promise:void 0,yp=typeof requestAnimationFrame==`function`?requestAnimationFrame:gp,bp=typeof queueMicrotask==`function`?queueMicrotask:vp===void 0?gp:function(e){return vp.resolve(null).then(e).catch(xp)};function xp(e){setTimeout(function(){throw e})}function Sp(e){return e===`head`}function Cp(e,t){var n=t,r=0;do{var i=n.nextSibling;if(e.removeChild(n),i&&i.nodeType===8){if(n=i.data,n===`/$`||n===`/&`){if(r===0){e.removeChild(i),Hh(t);return}r--}else if(n===`$`||n===`$?`||n===`$~`||n===`$!`||n===`&`)r++;else if(n===`html`)_m(e.ownerDocument.documentElement);else if(n===`head`){n=e.ownerDocument.head,_m(n);for(var a=n.firstChild;a;){var o=a.nextSibling,s=a.nodeName;a[Lt]||s===`SCRIPT`||s===`STYLE`||s===`LINK`&&a.rel.toLowerCase()===`stylesheet`||n.removeChild(a),a=o}}else n===`body`&&_m(e.ownerDocument.body)}n=i}while(n);Hh(t)}function wp(e,t){var n=e;e=0;do{var r=n.nextSibling;if(n.nodeType===1?t?(n._stashedDisplay=n.style.display,n.style.display=`none`):(n.style.display=n._stashedDisplay||``,n.getAttribute(`style`)===``&&n.removeAttribute(`style`)):n.nodeType===3&&(t?(n._stashedText=n.nodeValue,n.nodeValue=``):n.nodeValue=n._stashedText||``),r&&r.nodeType===8){if(n=r.data,n===`/$`){if(e===0)break;e--}else n!==`$`&&n!==`$?`&&n!==`$~`&&n!==`$!`||e++}n=r}while(n)}function Tp(e,t,n){if(t=CSS.escape(t)===t?t:`r-`+btoa(t).replace(/=/g,``),e.style.viewTransitionName=t,n!=null&&(e.style.viewTransitionClass=n),n=getComputedStyle(e),n.display===`inline`){if(t=e.getClientRects(),t.length===1)var r=1;else for(var i=r=0;i<t.length;i++){var a=t[i];0<a.width&&0<a.height&&r++}r===1&&(e=e.style,e.display=t.length===1?`inline-block`:`block`,e.marginTop=`-`+n.paddingTop,e.marginBottom=`-`+n.paddingBottom)}}function Ep(e,t){e=e.style,t=t.style;var n=t==null?null:t.hasOwnProperty(`viewTransitionName`)?t.viewTransitionName:t.hasOwnProperty(`view-transition-name`)?t[`view-transition-name`]:null;e.viewTransitionName=n==null||typeof n==`boolean`?``:(``+n).trim(),n=t==null?null:t.hasOwnProperty(`viewTransitionClass`)?t.viewTransitionClass:t.hasOwnProperty(`view-transition-class`)?t[`view-transition-class`]:null,e.viewTransitionClass=n==null||typeof n==`boolean`?``:(``+n).trim(),e.display===`inline-block`&&(t==null?e.display=e.margin=``:(n=t.display,e.display=n==null||typeof n==`boolean`?``:n,n=t.margin,n==null?(n=t.hasOwnProperty(`marginTop`)?t.marginTop:t[`margin-top`],e.marginTop=n==null||typeof n==`boolean`?``:n,t=t.hasOwnProperty(`marginBottom`)?t.marginBottom:t[`margin-bottom`],e.marginBottom=t==null||typeof t==`boolean`?``:t):e.margin=n))}function Dp(e,t,n){return n=n.ownerDocument.defaultView,{rect:e,abs:t.position===`absolute`||t.position===`fixed`,clip:t.clipPath!==`none`||t.overflow!==`visible`||t.filter!==`none`||t.mask!==`none`||t.mask!==`none`||t.borderRadius!==`0px`,view:0<=e.bottom&&0<=e.right&&e.top<=n.innerHeight&&e.left<=n.innerWidth}}function Op(e){return Dp(e.getBoundingClientRect(),getComputedStyle(e),e)}function kp(e){var t=e.getBoundingClientRect();t=new DOMRect(t.x+2e4,t.y+2e4,t.width,t.height);var n=getComputedStyle(e);return Dp(t,n,e)}function Ap(e){return e.documentElement.clientHeight}function jp(e){this.addEventListener(`load`,e),this.addEventListener(`error`,e)}function Mp(e,t,n,r,i,a,o,s,c){var l=t.nodeType===9?t:t.ownerDocument;try{var u=l.startViewTransition({update:function(){var t=l.defaultView,n=t.navigation&&t.navigation.transition,o=l.fonts.status;r();var s=[];if(o===`loaded`&&(Ap(l),l.fonts.status===`loading`&&s.push(l.fonts.ready)),o=s.length,e!==null)for(var c=e.suspenseyImages,u=0,d=0;d<c.length;d++){var f=c[d];if(!f.complete){var p=f.getBoundingClientRect();if(0<p.bottom&&0<p.right&&p.top<t.innerHeight&&p.left<t.innerWidth){if(u+=Xm(f),u>$m){s.length=o;break}f=new Promise(jp.bind(f)),s.push(f)}}}if(0<s.length)return t=Promise.race([Promise.all(s),new Promise(function(e){return setTimeout(e,500)})]).then(i,i),(n?Promise.allSettled([n.finished,t]):t).then(a,a);if(i(),n)return n.finished.then(a,a);a()},types:n});l.__reactViewTransition=u;var d=[];return u.ready.then(function(){for(var e=l.documentElement.getAnimations({subtree:!0}),t=0;t<e.length;t++){var n=e[t],r=n.effect,i=r.pseudoElement;if(i!=null&&i.startsWith(`::view-transition`)){d.push(n),n=r.getKeyframes();for(var a=i=void 0,s=!0,c=0;c<n.length;c++){var u=n[c],f=u.width;if(i===void 0)i=f;else if(i!==f){s=!1;break}if(f=u.height,a===void 0)a=f;else if(a!==f){s=!1;break}delete u.width,delete u.height,u.transform===`none`&&delete u.transform}s&&i!==void 0&&a!==void 0&&(r.setKeyframes(n),s=getComputedStyle(r.target,r.pseudoElement),s.width!==i||s.height!==a)&&(s=n[0],s.width=i,s.height=a,s=n[n.length-1],s.width=i,s.height=a,r.setKeyframes(n))}}o()},function(e){l.__reactViewTransition===u&&(l.__reactViewTransition=null);try{if(typeof e==`object`&&e)switch(e.name){case`InvalidStateError`:(e.message===`View transition was skipped because document visibility state is hidden.`||e.message===`Skipping view transition because document visibility state has become hidden.`||e.message===`Skipping view transition because viewport size changed.`||e.message===`Transition was aborted because of invalid state`)&&(e=null)}e!==null&&c(e)}finally{r(),i(),o()}}),u.finished.finally(function(){for(var e=0;e<d.length;e++)d[e].cancel();l.__reactViewTransition===u&&(l.__reactViewTransition=null),s()}),u}catch{return r(),i(),o(),null}}function Np(e,t){this._scope=document.documentElement,this._selector=`::view-transition-`+e+`(`+t+`)`}Np.prototype.animate=function(e,t){return t=typeof t==`number`?{duration:t}:T({},t),t.pseudoElement=this._selector,this._scope.animate(e,t)},Np.prototype.getAnimations=function(){for(var e=this._scope,t=this._selector,n=e.getAnimations({subtree:!0}),r=[],i=0;i<n.length;i++){var a=n[i].effect;a!==null&&a.target===e&&a.pseudoElement===t&&r.push(n[i])}return r},Np.prototype.getComputedStyle=function(){return getComputedStyle(this._scope,this._selector)};function Pp(e){return{name:e,group:new Np(`group`,e),imagePair:new Np(`image-pair`,e),old:new Np(`old`,e),new:new Np(`new`,e)}}function Fp(e){this._fragmentFiber=e,this._observers=this._eventListeners=null}Fp.prototype.addEventListener=function(e,t,n){var r=null,i=null;if(!(n!=null&&typeof n!=`boolean`&&(r=n.signal||null,r!==null&&r.aborted))){this._eventListeners===null&&(this._eventListeners=[]);var a=this._eventListeners;if(Bp(a,e,t,n)===-1){var o=this,s=t;n!=null&&typeof n!=`boolean`&&!0===n.once&&(s=function(r){o.removeEventListener(e,t,n),typeof t==`function`?t.call(this,r):t.handleEvent(r)}),r!==null&&(i=o.removeEventListener.bind(o,e,t,n),r.addEventListener(`abort`,i,{once:!0}),i=r.removeEventListener.bind(r,`abort`,i)),r=Rp(n),a.push({type:e,listener:t,optionsOrUseCapture:n,attachedListener:s,cleanup:i}),h(this._fragmentFiber.child,!1,Ip,e,s,r)}this._eventListeners=a}};function Ip(e,t,n,r){return b(e).addEventListener(t,n,r),!1}Fp.prototype.removeEventListener=function(e,t,n){var r=this._eventListeners;if(r!==null&&(t=Bp(r,e,t,n),t!==-1)){var i=r[t];n=i.attachedListener;var a=i.cleanup;i=Rp(i.optionsOrUseCapture),h(this._fragmentFiber.child,!1,Lp,e,n,i),r.splice(t,1),a!==null&&a()}};function Lp(e,t,n,r){return b(e).removeEventListener(t,n,r),!1}function Rp(e){return e!=null&&typeof e!=`boolean`&&(!0===e.once||e.signal instanceof AbortSignal)?{capture:e.capture,passive:e.passive}:e}function zp(e){return e==null?`c=0`:typeof e==`boolean`?`c=`+(e?`1`:`0`):`c=`+(e.capture?`1`:`0`)}function Bp(e,t,n,r){if(e.length===0)return-1;r=zp(r);for(var i=0;i<e.length;i++){var a=e[i];if(a.type===t&&a.listener===n&&zp(a.optionsOrUseCapture)===r)return i}return-1}Fp.prototype.dispatchEvent=function(e){var t=g(this._fragmentFiber);if(t===null)return!0;t=b(t);var n=this._eventListeners;if(n!==null&&0<n.length||!e.bubbles){var r=t.nodeType===9?t.createComment(``):document.createTextNode(``);if(n)for(var i=0;i<n.length;i++){var a=n[i];r.addEventListener(a.type,a.attachedListener,Rp(a.optionsOrUseCapture))}if(t.appendChild(r),e=r.dispatchEvent(e),n)for(i=0;i<n.length;i++)a=n[i],r.removeEventListener(a.type,a.attachedListener,Rp(a.optionsOrUseCapture));return t.removeChild(r),e}return t.dispatchEvent(e)},Fp.prototype.focus=function(e){h(this._fragmentFiber.child,!0,Vp,e,void 0,void 0)};function Vp(e,t){return e.tag!==6&&(e=b(e),pm(e,t))}Fp.prototype.focusLast=function(e){var t=[];h(this._fragmentFiber.child,!0,Hp,t,void 0,void 0);for(var n=t.length-1;0<=n&&!Vp(t[n],e);n--);};function Hp(e,t){return t.push(e),!1}Fp.prototype.blur=function(){var e=g(this._fragmentFiber);e!==null&&(e=b(e),e=lp(e).activeElement,e!==null&&h(this._fragmentFiber.child,!1,Up,e,void 0,void 0))};function Up(e,t){return e.tag!==6&&(e=b(e),e===t||e.contains(t)?(t.blur(),!0):!1)}Fp.prototype.observeUsing=function(e){this._observers===null&&(this._observers=new Set),this._observers.add(e),h(this._fragmentFiber.child,!1,Wp,e,void 0,void 0)};function Wp(e,t){return e.tag===6||(e=b(e),t.observe(e)),!1}Fp.prototype.unobserveUsing=function(e){var t=this._observers;if(t!==null&&t.has(e)){t.delete(e),h(this._fragmentFiber.child,!1,Gp,e,void 0,void 0);for(var n=t=0;n<Kp.length;n++){var r=Kp[n];r.fragmentInstance===this&&r.observer===e?e.unobserve(r.instance):Kp[t++]=r}Kp.length=t}};function Gp(e,t){return e.tag===6||(e=b(e),t.unobserve(e)),!1}var Kp=[],qp=!1;function Jp(e,t,n){Kp.push({fragmentInstance:e,observer:t,instance:n}),qp||(qp=!0,mm(function(){qp=!1;var e=Kp;Kp=[];for(var t=0;t<e.length;t++){var n=e[t];n.observer.unobserve(n.instance)}}))}Fp.prototype.getClientRects=function(){var e=[];return h(this._fragmentFiber.child,!1,Yp,e,void 0,void 0),e};function Yp(e,t){if(e.tag===6){e=e.stateNode;var n=e.ownerDocument.createRange();n.selectNodeContents(e),t.push.apply(t,n.getClientRects())}else e=b(e),t.push.apply(t,e.getClientRects());return!1}Fp.prototype.getRootNode=function(e){var t=g(this._fragmentFiber);return t===null?this:b(t).getRootNode(e)},Fp.prototype.compareDocumentPosition=function(e){var t=g(this._fragmentFiber);if(t===null)return Node.DOCUMENT_POSITION_DISCONNECTED;var n=[];h(this._fragmentFiber.child,!1,Hp,n,void 0,void 0);var r=b(t);if(n.length===0){if(n=r,_(this._fragmentFiber)){a:{for(t=this._fragmentFiber.return;t!==null;){if(t.tag===4){t=t.stateNode.containerInfo;break a}if(t.tag===3||t.tag===5||t.tag===27)break;t=t.return}t=null}t!=null&&(n=t)}t=this._fragmentFiber;var i=r=n.compareDocumentPosition(e);return n===e?i=Node.DOCUMENT_POSITION_CONTAINS:r&Node.DOCUMENT_POSITION_CONTAINED_BY&&(n=v(t)[1],n===null?i=Node.DOCUMENT_POSITION_PRECEDING:(e=b(n).compareDocumentPosition(e),i=e===0||e&Node.DOCUMENT_POSITION_FOLLOWING?Node.DOCUMENT_POSITION_FOLLOWING:Node.DOCUMENT_POSITION_PRECEDING)),i|=Node.DOCUMENT_POSITION_IMPLEMENTATION_SPECIFIC}t=b(n[0]),i=b(n[n.length-1]);var a=_(this._fragmentFiber)?t.parentElement:r;if(a==null)return Node.DOCUMENT_POSITION_DISCONNECTED;r=a.compareDocumentPosition(t)&Node.DOCUMENT_POSITION_CONTAINED_BY,a=a.compareDocumentPosition(i)&Node.DOCUMENT_POSITION_CONTAINED_BY;var o=t.compareDocumentPosition(e),s=i.compareDocumentPosition(e),c=o&Node.DOCUMENT_POSITION_CONTAINED_BY||s&Node.DOCUMENT_POSITION_CONTAINED_BY;return s=r&&a&&o&Node.DOCUMENT_POSITION_FOLLOWING&&s&Node.DOCUMENT_POSITION_PRECEDING,t=r&&t===e||a&&i===e||c||s?Node.DOCUMENT_POSITION_CONTAINED_BY:!r&&t===e||!a&&i===e?Node.DOCUMENT_POSITION_IMPLEMENTATION_SPECIFIC:o,t&Node.DOCUMENT_POSITION_DISCONNECTED||t&Node.DOCUMENT_POSITION_IMPLEMENTATION_SPECIFIC||Xp(t,this._fragmentFiber,n[0],n[n.length-1],e)?t:Node.DOCUMENT_POSITION_IMPLEMENTATION_SPECIFIC};function Xp(e,t,n,r,i){var a=Bt(i);if(e&Node.DOCUMENT_POSITION_CONTAINED_BY){if(n=!!a)a:{for(;a!==null;){if(a.tag===7&&(a===t||a.alternate===t)){n=!0;break a}a=a.return}n=!1}return n}if(e&Node.DOCUMENT_POSITION_CONTAINS){if(a===null)return a=i.ownerDocument,i===a||i===a.documentElement||i===a.body;a:{for(a=t,t=g(t);a!==null;){if(!(a.tag!==5&&a.tag!==3&&a.tag!==27||a!==t&&a.alternate!==t)){a=!0;break a}a=a.return}a=!1}return a}return e&Node.DOCUMENT_POSITION_PRECEDING?((t=!!a)&&!(t=a===n)&&(t=te(n,a,w),t===null?t=!1:(h(t,!0,C,a,n),a=x,x=null,t=a!==null)),t):e&Node.DOCUMENT_POSITION_FOLLOWING?((t=!!a)&&!(t=a===r)&&(t=te(r,a,w),t===null?t=!1:(h(t,!0,ee,a,r),a=x,S=x=null,t=a!==null)),t):!1}function Zp(e,t){var n=e.ownerDocument.createRange();n.selectNodeContents(e),e=n.getBoundingClientRect(),window.scrollTo(window.scrollX+e.left,t?window.scrollY+e.top:window.scrollY+e.bottom-window.innerHeight)}Fp.prototype.scrollIntoView=function(e){if(typeof e==`object`)throw Error(i(566));var t=[];h(this._fragmentFiber.child,!1,Hp,t,void 0,void 0);var n=!1!==e;if(t.length===0){var r=v(this._fragmentFiber);if(r=n?r[1]||r[0]||g(this._fragmentFiber):r[0]||r[1],r===null)return;if(r.tag===6){e=b(r),Zp(e,n);return}if(r=b(r),r.nodeType!==9){if(r.nodeType===11){n=`host`in r?r.host:null,n!==null&&n.scrollIntoView(e);return}r.scrollIntoView(e)}}for(r=n?t.length-1:0;r!==(n?-1:t.length);){var a=t[r];a.tag===6?(a=b(a),Zp(a,n)):b(a).scrollIntoView(e),r+=n?-1:1}};function Qp(e,t){return e=b(e),$p(e,t),!1}function $p(e,t){e.reactFragments??=new Set,e.reactFragments.add(t)}function em(e,t){var n=t._eventListeners;if(n!==null)for(var r=0;r<n.length;r++){var i=n[r];e.addEventListener(i.type,i.attachedListener,Rp(i.optionsOrUseCapture))}e.nodeType!==3&&(n=t._observers,n!==null&&n.forEach(function(n){for(var r=0,i=0;i<Kp.length;i++){var a=Kp[i];(a.fragmentInstance!==t||a.observer!==n||a.instance!==e)&&(Kp[r++]=a)}Kp.length=r,n.observe(e)}),$p(e,t))}function tm(e,t){var n=t._eventListeners;if(n!==null)for(var r=0;r<n.length;r++){var i=n[r];e.removeEventListener(i.type,i.attachedListener,Rp(i.optionsOrUseCapture))}e.nodeType!==3&&(n=t._observers,n!==null&&n.forEach(function(n){typeof n.rootMargin==`string`?Jp(t,n,e):n.unobserve(e)}),e.reactFragments!=null&&e.reactFragments.delete(t))}function nm(e){var t=e.firstChild;for(t&&t.nodeType===10&&(t=t.nextSibling);t;){var n=t;switch(t=t.nextSibling,n.nodeName){case`HTML`:case`HEAD`:case`BODY`:nm(n),zt(n);continue;case`SCRIPT`:case`STYLE`:continue;case`LINK`:if(n.rel.toLowerCase()===`stylesheet`)continue}e.removeChild(n)}}function rm(e,t,n,r){for(;e.nodeType===1;){var i=n;if(e.nodeName.toLowerCase()!==t.toLowerCase()){if(!r&&(e.nodeName!==`INPUT`||e.type!==`hidden`))break}else if(!r){if(t===`input`&&e.type===`hidden`){var a=i.name==null?null:``+i.name;if(i.type===`hidden`&&e.getAttribute(`name`)===a)return e}else return e}else if(!e[Lt])switch(t){case`meta`:if(!e.hasAttribute(`itemprop`))break;return e;case`link`:if(a=e.getAttribute(`rel`),a===`stylesheet`&&e.hasAttribute(`data-precedence`)||a!==i.rel||e.getAttribute(`href`)!==(i.href==null||i.href===``?null:i.href)||e.getAttribute(`crossorigin`)!==(i.crossOrigin==null?null:i.crossOrigin)||e.getAttribute(`title`)!==(i.title==null?null:i.title))break;return e;case`style`:if(e.hasAttribute(`data-precedence`))break;return e;case`script`:if(a=e.getAttribute(`src`),(a!==(i.src==null?null:i.src)||e.getAttribute(`type`)!==(i.type==null?null:i.type)||e.getAttribute(`crossorigin`)!==(i.crossOrigin==null?null:i.crossOrigin))&&a&&e.hasAttribute(`async`)&&!e.hasAttribute(`itemprop`))break;return e;default:return e}if(e=lm(e.nextSibling),e===null)break}return null}function im(e,t,n){if(t===``)return null;for(;e.nodeType!==3;)if((e.nodeType!==1||e.nodeName!==`INPUT`||e.type!==`hidden`)&&!n||(e=lm(e.nextSibling),e===null))return null;return e}function am(e,t){for(;e.nodeType!==8;)if((e.nodeType!==1||e.nodeName!==`INPUT`||e.type!==`hidden`)&&!t||(e=lm(e.nextSibling),e===null))return null;return e}function om(e){return e.data===`$?`||e.data===`$~`}function sm(e){return e.data===`$!`||e.data===`$?`&&e.ownerDocument.readyState!==`loading`}function cm(e,t){var n=e.ownerDocument;if(e.data===`$~`)e._reactRetry=t;else if(e.data!==`$?`||n.readyState!==`loading`)t();else{var r=function(){t(),n.removeEventListener(`DOMContentLoaded`,r)};n.addEventListener(`DOMContentLoaded`,r),e._reactRetry=r}}function lm(e){for(;e!=null;e=e.nextSibling){var t=e.nodeType;if(t===1||t===3)break;if(t===8){if(t=e.data,t===`$`||t===`$!`||t===`$?`||t===`$~`||t===`&`||t===`F!`||t===`F`)break;if(t===`/$`||t===`/&`)return null}}return e}var um=null;function dm(e){e=e.nextSibling;for(var t=0;e;){if(e.nodeType===8){var n=e.data;if(n===`/$`||n===`/&`){if(t===0)return lm(e.nextSibling);t--}else n!==`$`&&n!==`$!`&&n!==`$?`&&n!==`$~`&&n!==`&`||t++}e=e.nextSibling}return null}function fm(e){e=e.previousSibling;for(var t=0;e;){if(e.nodeType===8){var n=e.data;if(n===`$`||n===`$!`||n===`$?`||n===`$~`||n===`&`){if(t===0)return e;t--}else n!==`/$`&&n!==`/&`||t++}e=e.previousSibling}return null}function pm(e,t){function n(){r=!0}if(e.ownerDocument.activeElement===e)return!0;var r=!1;try{e.ownerDocument.addEventListener(`focus`,n,!0),(e.focus||HTMLElement.prototype.focus).call(e,t)}finally{e.ownerDocument.removeEventListener(`focus`,n,!0)}return r}function mm(e){yp(function(){yp(function(t){return e(t)})})}function hm(e,t,n){switch(t=lp(n),e){case`html`:if(e=t.documentElement,!e)throw Error(i(452));return e;case`head`:if(e=t.head,!e)throw Error(i(453));return e;case`body`:if(e=t.body,!e)throw Error(i(454));return e;default:throw Error(i(451))}}function gm(e,t,n){for(var r in n){var i=n[r];n.hasOwnProperty(r)&&i!=null&&$(e,t,r,null,rp,i)}n.dangerouslySetInnerHTML!=null&&(e.textContent=``),e.onclick===En&&(e.onclick=null),zt(e)}function _m(e){for(var t=e.attributes;t.length;)e.removeAttributeNode(t[0]);zt(e)}var vm=new Map,ym=new Set;function bm(e){if(typeof e.getRootNode==`function`){var t=e.getRootNode();if(t.nodeType===9||t.nodeType===11)return t}return e.nodeType===9?e:e.ownerDocument}var xm=O.d;O.d={f:Sm,r:Cm,D:Em,C:Dm,L:Om,m:km,X:jm,S:Am,M:Mm};function Sm(){var e=xm.f(),t=zd();return e||t}function Cm(e){var t=Vt(e);t!==null&&t.tag===5&&t.type===`form`?ac(t):xm.r(e)}var wm=typeof document>`u`?null:document;function Tm(e,t,n){var r=wm;if(r&&typeof t==`string`&&t){var i=dn(t);i=`link[rel="`+e+`"][href="`+i+`"]`,typeof n==`string`&&(i+=`[crossorigin="`+n+`"]`),ym.has(i)||(ym.add(i),e={rel:e,crossOrigin:n,href:t},r.querySelector(i)===null&&(t=r.createElement(`link`),np(t,`link`,e),Wt(t),r.head.appendChild(t)))}}function Em(e){xm.D(e),Tm(`dns-prefetch`,e,null)}function Dm(e,t){xm.C(e,t),Tm(`preconnect`,e,t)}function Om(e,t,n){xm.L(e,t,n);var r=wm;if(r&&e&&t){var i=`link[rel="preload"][as="`+dn(t)+`"]`;t===`image`&&n&&n.imageSrcSet?(i+=`[imagesrcset="`+dn(n.imageSrcSet)+`"]`,typeof n.imageSizes==`string`&&(i+=`[imagesizes="`+dn(n.imageSizes)+`"]`)):i+=`[href="`+dn(e)+`"]`;var a=i;switch(t){case`style`:a=Pm(e);break;case`script`:a=Rm(e)}if(!(vm.has(a)||(e=T({rel:`preload`,href:t===`image`&&n&&n.imageSrcSet?void 0:e,as:t},n),vm.set(a,e),r.querySelector(i)!==null||t===`style`&&r.querySelector(Fm(a))||t===`script`&&r.querySelector(zm(a))))){var o=r.createElement(`link`);np(o,`link`,e),t===`style`&&(o[Rt]=!0,o.onload=o.onerror=function(){Gt(o)}),Wt(o),r.head.appendChild(o)}}}function km(e,t){xm.m(e,t);var n=wm;if(n&&e){var r=t&&typeof t.as==`string`?t.as:`script`,i=`link[rel="modulepreload"][as="`+dn(r)+`"][href="`+dn(e)+`"]`,a=i;switch(r){case`audioworklet`:case`paintworklet`:case`serviceworker`:case`sharedworker`:case`worker`:case`script`:a=Rm(e)}if(!vm.has(a)&&(e=T({rel:`modulepreload`,href:e},t),vm.set(a,e),n.querySelector(i)===null)){switch(r){case`audioworklet`:case`paintworklet`:case`serviceworker`:case`sharedworker`:case`worker`:case`script`:if(n.querySelector(zm(a)))return}r=n.createElement(`link`),np(r,`link`,e),Wt(r),n.head.appendChild(r)}}}function Am(e,t,n){xm.S(e,t,n);var r=wm;if(r&&e){var i=Ut(r).hoistableStyles,a=Pm(e);t||=`default`;var o=i.get(a);if(!o){var s={loading:0,preload:null};if(o=r.querySelector(Fm(a)))s.loading=5;else{e=T({rel:`stylesheet`,href:e,"data-precedence":t},n),(n=vm.get(a))&&Hm(e,n);var c=o=r.createElement(`link`);Wt(c),np(c,`link`,e),c._p=new Promise(function(e,t){c.onload=e,c.onerror=t}),c.addEventListener(`load`,function(){s.loading|=1}),c.addEventListener(`error`,function(){s.loading|=2}),s.loading|=4,Vm(o,t,r)}o={type:`stylesheet`,instance:o,count:1,state:s},i.set(a,o)}}}function jm(e,t){xm.X(e,t);var n=wm;if(n&&e){var r=Ut(n).hoistableScripts,i=Rm(e),a=r.get(i);a||(a=n.querySelector(zm(i)),a||(e=T({src:e,async:!0},t),(t=vm.get(i))&&Um(e,t),a=n.createElement(`script`),Wt(a),np(a,`link`,e),n.head.appendChild(a)),a={type:`script`,instance:a,count:1,state:null},r.set(i,a))}}function Mm(e,t){xm.M(e,t);var n=wm;if(n&&e){var r=Ut(n).hoistableScripts,i=Rm(e),a=r.get(i);a||(a=n.querySelector(zm(i)),a||(e=T({src:e,async:!0,type:`module`},t),(t=vm.get(i))&&Um(e,t),a=n.createElement(`script`),Wt(a),np(a,`link`,e),n.head.appendChild(a)),a={type:`script`,instance:a,count:1,state:null},r.set(i,a))}}function Nm(e,t,n,r){var a=(a=Ae.current)?bm(a):null;if(!a)throw Error(i(446));switch(e){case`meta`:case`title`:return null;case`style`:return typeof n.precedence==`string`&&typeof n.href==`string`?(n=Pm(n.href),t=Ut(a).hoistableStyles,r=t.get(n),r||(r={type:`style`,instance:null,count:0,state:null},t.set(n,r)),r):{type:`void`,instance:null,count:0,state:null};case`link`:if(n.rel===`stylesheet`&&typeof n.href==`string`&&typeof n.precedence==`string`){e=Pm(n.href);var o=Ut(a).hoistableStyles,s=o.get(e);if(s||(a=a.ownerDocument||a,s={type:`stylesheet`,instance:null,count:0,state:{loading:0,preload:null}},o.set(e,s),(o=a.querySelector(Fm(e)))?o._p||(s.instance=o,s.state.loading=5):(o=vm.get(e),o||(o={rel:`preload`,as:`style`,href:n.href,crossOrigin:n.crossOrigin,integrity:n.integrity,media:n.media,hrefLang:n.hrefLang,referrerPolicy:n.referrerPolicy},vm.set(e,o)),Lm(a,e,o,s.state))),t&&r===null)throw Error(i(528,``));return s}if(t&&r!==null)throw Error(i(529,``));return null;case`script`:return t=n.async,n=n.src,typeof n==`string`&&t&&typeof t!=`function`&&typeof t!=`symbol`?(n=Rm(n),t=Ut(a).hoistableScripts,r=t.get(n),r||(r={type:`script`,instance:null,count:0,state:null},t.set(n,r)),r):{type:`void`,instance:null,count:0,state:null};default:throw Error(i(444,e))}}function Pm(e){return`href="`+dn(e)+`"`}function Fm(e){return`link[rel="stylesheet"][`+e+`]`}function Im(e){return T({},e,{"data-precedence":e.precedence,precedence:null})}function Lm(e,t,n,r){if(t=e.querySelector(`link[rel="preload"][as="style"][`+t+`]`)){if(!0!==t[Rt]){r.loading=1;return}}else t=e.createElement(`link`),t[Rt]=!0,t.onload=t.onerror=Gt.bind(null,t),np(t,`link`,n),Wt(t),e.head.appendChild(t);r.preload=t,t.addEventListener(`load`,function(){return r.loading|=1}),t.addEventListener(`error`,function(){return r.loading|=2})}function Rm(e){return`[src="`+dn(e)+`"]`}function zm(e){return`script[async]`+e}function Bm(e,t,n){if(t.count++,t.instance===null)switch(t.type){case`style`:var r=e.querySelector(`style[data-href~="`+dn(n.href)+`"]`);if(r)return t.instance=r,Wt(r),r;var a=T({},n,{"data-href":n.href,"data-precedence":n.precedence,href:null,precedence:null});return r=(e.ownerDocument||e).createElement(`style`),Wt(r),np(r,`style`,a),Vm(r,n.precedence,e),t.instance=r;case`stylesheet`:a=Pm(n.href);var o=e.querySelector(Fm(a));if(o)return t.state.loading|=4,t.instance=o,Wt(o),o;r=Im(n),(a=vm.get(a))&&Hm(r,a),o=(e.ownerDocument||e).createElement(`link`),Wt(o);var s=o;return s._p=new Promise(function(e,t){s.onload=e,s.onerror=t}),np(o,`link`,r),t.state.loading|=4,Vm(o,n.precedence,e),t.instance=o;case`script`:return o=Rm(n.src),(a=e.querySelector(zm(o)))?(t.instance=a,Wt(a),a):(r=n,(a=vm.get(o))&&(r=T({},n),Um(r,a)),e=e.ownerDocument||e,a=e.createElement(`script`),Wt(a),np(a,`link`,r),e.head.appendChild(a),t.instance=a);case`void`:return null;default:throw Error(i(443,t.type))}else t.type===`stylesheet`&&!(t.state.loading&4)&&(r=t.instance,t.state.loading|=4,Vm(r,n.precedence,e));return t.instance}function Vm(e,t,n){for(var r=n.querySelectorAll(`link[rel="stylesheet"][data-precedence],style[data-precedence]`),i=r.length?r[r.length-1]:null,a=i,o=0;o<r.length;o++){var s=r[o];if(s.dataset.precedence===t)a=s;else if(a!==i)break}a?a.parentNode.insertBefore(e,a.nextSibling):(t=n.nodeType===9?n.head:n,t.insertBefore(e,t.firstChild))}function Hm(e,t){e.crossOrigin??=t.crossOrigin,e.referrerPolicy??=t.referrerPolicy,e.title??=t.title}function Um(e,t){e.crossOrigin??=t.crossOrigin,e.referrerPolicy??=t.referrerPolicy,e.integrity??=t.integrity}var Wm=null;function Gm(e,t,n){if(Wm===null){var r=new Map,i=Wm=new Map;i.set(n,r)}else i=Wm,r=i.get(n),r||(r=new Map,i.set(n,r));if(r.has(e))return r;for(r.set(e,null),n=n.getElementsByTagName(e),i=0;i<n.length;i++){var a=n[i];if(!(a[Lt]||a[At]||e===`link`&&a.getAttribute(`rel`)===`stylesheet`)&&a.namespaceURI!==`http://www.w3.org/2000/svg`){var o=a.getAttribute(t)||``;o=e+o;var s=r.get(o);s?s.push(a):r.set(o,[a])}}return r}function Km(e,t,n){e=e.ownerDocument||e,e.head.insertBefore(n,t===`title`?e.querySelector(`head > title`):null)}function qm(e,t,n){if(n===1||t.itemProp!=null)return!1;switch(e){case`meta`:case`title`:return!0;case`style`:if(typeof t.precedence!=`string`||typeof t.href!=`string`||t.href===``)break;return!0;case`link`:if(typeof t.rel!=`string`||typeof t.href!=`string`||t.href===``||t.onLoad||t.onError)break;switch(t.rel){case`stylesheet`:return e=t.disabled,typeof t.precedence==`string`&&e==null;default:return!0}case`script`:if(t.async&&typeof t.async!=`function`&&typeof t.async!=`symbol`&&!t.onLoad&&!t.onError&&t.src&&typeof t.src==`string`)return!0}return!1}function Jm(e,t){return e===`img`&&t.src!=null&&t.src!==``&&t.onLoad==null&&t.loading!==`lazy`}function Ym(e){return!(e.type===`stylesheet`&&!(e.state.loading&3))}function Xm(e){return(e.width||100)*(e.height||100)*(typeof devicePixelRatio==`number`?devicePixelRatio:1)*.25}function Zm(e,t){typeof t.decode==`function`&&(e.imgCount++,t.complete||(e.imgBytes+=Xm(t),e.suspenseyImages.push(t)),e=rh.bind(e),t.decode().then(e,e))}function Qm(e,t,n,r){if(n.type===`stylesheet`&&(typeof r.media!=`string`||!1!==matchMedia(r.media).matches)&&!(n.state.loading&4)){if(n.instance===null){var i=Pm(r.href),a=t.querySelector(Fm(i));if(a){t=a._p,typeof t==`object`&&t&&typeof t.then==`function`&&(e.count++,e=nh.bind(e),t.then(e,e)),n.state.loading|=4,n.instance=a,Wt(a);return}a=t.ownerDocument||t,r=Im(r),(i=vm.get(i))&&Hm(r,i),a=a.createElement(`link`),Wt(a);var o=a;o._p=new Promise(function(e,t){o.onload=e,o.onerror=t}),np(a,`link`,r),n.instance=a}e.stylesheets===null&&(e.stylesheets=new Map),e.stylesheets.set(n,t),(t=n.state.preload)&&!(n.state.loading&3)&&(e.count++,n=nh.bind(e),t.addEventListener(`load`,n),t.addEventListener(`error`,n))}}var $m=0;function eh(e,t){return e.stylesheets&&e.count===0&&ah(e,e.stylesheets),0<e.count||0<e.imgCount?function(n){var r=setTimeout(function(){if(e.stylesheets&&ah(e,e.stylesheets),e.unsuspend){var t=e.unsuspend;e.unsuspend=null,t()}},6e4+t);0<e.imgBytes&&$m===0&&($m=62500*op());var i=setTimeout(function(){if(e.waitingForImages=!1,e.count===0&&(e.stylesheets&&ah(e,e.stylesheets),e.unsuspend)){var t=e.unsuspend;e.unsuspend=null,t()}},(e.imgBytes>$m?50:800)+t);return e.unsuspend=n,function(){e.unsuspend=null,clearTimeout(r),clearTimeout(i)}}:null}function th(e){if(e.count===0&&(e.imgCount===0||!e.waitingForImages)){if(e.stylesheets)ah(e,e.stylesheets);else if(e.unsuspend){var t=e.unsuspend;e.unsuspend=null,t()}}}function nh(){this.count--,th(this)}function rh(){this.imgCount--,th(this)}var ih=null;function ah(e,t){e.stylesheets=null,e.unsuspend!==null&&(e.count++,ih=new Map,t.forEach(oh,e),ih=null,nh.call(e))}function oh(e,t){if(!(t.state.loading&4)){var n=ih.get(e);if(n)var r=n.get(null);else{n=new Map,ih.set(e,n);for(var i=e.querySelectorAll(`link[data-precedence],style[data-precedence]`),a=0;a<i.length;a++){var o=i[a];(o.nodeName===`LINK`||o.getAttribute(`media`)!==`not all`)&&(n.set(o.dataset.precedence,o),r=o)}r&&n.set(null,r)}i=t.instance,o=i.getAttribute(`data-precedence`),a=n.get(o)||r,a===r&&n.set(null,i),n.set(o,i),this.count++,r=nh.bind(this),i.addEventListener(`load`,r),i.addEventListener(`error`,r),a?a.parentNode.insertBefore(i,a.nextSibling):(e=e.nodeType===9?e.head:e,e.insertBefore(i,e.firstChild)),t.state.loading|=4}}var sh={$$typeof:le,Provider:null,Consumer:null,_currentValue:we,_currentValue2:we,_threadCount:0};function ch(e,t,n,r,i,a,o,s,c){this.tag=1,this.containerInfo=e,this.pingCache=this.current=this.pendingChildren=null,this.timeoutHandle=-1,this.callbackNode=this.next=this.pendingContext=this.context=this.cancelPendingCommit=null,this.callbackPriority=0,this.expirationTimes=yt(-1),this.entangledLanes=this.shellSuspendCounter=this.errorRecoveryDisabledLanes=this.expiredLanes=this.warmLanes=this.pingedLanes=this.suspendedLanes=this.pendingLanes=0,this.entanglements=yt(0),this.hiddenUpdates=yt(null),this.identifierPrefix=r,this.onUncaughtError=i,this.onCaughtError=a,this.onRecoverableError=o,this.pooledCache=null,this.pooledCacheLanes=0,this.formState=c,this.transitionTypes=null,this.incompleteTransitions=new Map}function lh(e,t,n,r,i,a,o,s,c,l,u,d){return e=new ch(e,t,n,o,c,l,u,d,s),t=1,!0===a&&(t|=24),a=Li(3,null,null,t),e.current=a,a.stateNode=e,t=Ia(),t.refCount++,e.pooledCache=t,t.refCount++,a.memoizedState={element:r,isDehydrated:n,cache:t},yo(a),e}function uh(e){return e?(e=Fi,e):Fi}function dh(e,t,n,r,i,a){i=uh(i),r.context===null?r.context=i:r.pendingContext=i,r=xo(t),r.payload={element:n},a=a===void 0?null:a,a!==null&&(r.callback=a),n=So(e,r,t),n!==null&&(Pd(n,e,t),Co(n,e,t))}function fh(e,t){if(e=e.memoizedState,e!==null&&e.dehydrated!==null){var n=e.retryLane;e.retryLane=n!==0&&n<t?n:t}}function ph(e,t){fh(e,t),(e=e.alternate)&&fh(e,t)}function mh(e){if(e.tag===13||e.tag===31){var t=Mi(e,67108864);t!==null&&Pd(t,e,67108864),ph(e,67108864)}}function hh(e){if(e.tag===13||e.tag===31){var t=jd();t=Tt(t);var n=Mi(e,t);n!==null&&Pd(n,e,t),ph(e,t)}}var gh=!0;function _h(e,t,n,r){var i=D.T;D.T=null;var a=O.p;try{O.p=2,yh(e,t,n,r)}finally{O.p=a,D.T=i}}function vh(e,t,n,r){var i=D.T;D.T=null;var a=O.p;try{O.p=8,yh(e,t,n,r)}finally{O.p=a,D.T=i}}function yh(e,t,n,r){if(gh){var i=bh(r);if(i===null)Kf(e,t,r,xh,n),Mh(e,r);else if(Ph(i,e,t,n,r))r.stopPropagation();else if(Mh(e,r),t&4&&-1<jh.indexOf(e)){for(;i!==null;){var a=Vt(i);if(a!==null)switch(a.tag){case 3:if(a=a.stateNode,a.current.memoizedState.isDehydrated){var o=pt(a.pendingLanes);if(o!==0){var s=a;for(s.pendingLanes|=2,s.entangledLanes|=2;o;){var c=1<<31-ot(o);s.entanglements[1]|=c,o&=~c}Ef(a),!(W&6)&&(_d=Je()+500,Df(0,!1))}}break;case 31:case 13:s=Mi(a,2),s!==null&&Pd(s,a,2),zd(),ph(a,2)}if(a=bh(r),a===null&&Kf(e,t,r,xh,n),a===i)break;i=a}i!==null&&r.stopPropagation()}else Kf(e,t,r,null,n)}}function bh(e){return e=On(e),Sh(e)}var xh=null;function Sh(e){if(xh=null,e=Bt(e),e!==null){var t=o(e);if(t===null)e=null;else{var n=t.tag;if(n===13){if(e=s(t),e!==null)return e;e=null}else if(n===31){if(e=c(t),e!==null)return e;e=null}else if(n===3){if(t.stateNode.current.memoizedState.isDehydrated)return t.tag===3?t.stateNode.containerInfo:null;e=null}else t!==e&&(e=null)}}return xh=e,null}function Ch(e){switch(e){case`beforetoggle`:case`cancel`:case`click`:case`close`:case`contextmenu`:case`copy`:case`cut`:case`auxclick`:case`dblclick`:case`dragend`:case`dragstart`:case`drop`:case`focusin`:case`focusout`:case`input`:case`invalid`:case`keydown`:case`keypress`:case`keyup`:case`mousedown`:case`mouseup`:case`paste`:case`pause`:case`play`:case`pointercancel`:case`pointerdown`:case`pointerup`:case`ratechange`:case`reset`:case`seeked`:case`submit`:case`toggle`:case`touchcancel`:case`touchend`:case`touchstart`:case`volumechange`:case`change`:case`selectionchange`:case`textInput`:case`compositionstart`:case`compositionend`:case`compositionupdate`:case`beforeblur`:case`afterblur`:case`beforeinput`:case`blur`:case`fullscreenchange`:case`fullscreenerror`:case`focus`:case`hashchange`:case`popstate`:case`select`:case`selectstart`:return 2;case`drag`:case`dragenter`:case`dragexit`:case`dragleave`:case`dragover`:case`mousemove`:case`mouseout`:case`mouseover`:case`pointermove`:case`pointerout`:case`pointerover`:case`resize`:case`scroll`:case`touchmove`:case`wheel`:case`mouseenter`:case`mouseleave`:case`pointerenter`:case`pointerleave`:return 8;case`message`:switch(Ye()){case Xe:return 2;case Ze:return 8;case Qe:case $e:return 32;case et:return 268435456;default:return 32}default:return 32}}var wh=!1,Th=null,Eh=null,Dh=null,Oh=new Map,kh=new Map,Ah=[],jh=`mousedown mouseup touchcancel touchend touchstart auxclick dblclick pointercancel pointerdown pointerup dragend dragstart drop compositionend compositionstart keydown keypress keyup input textInput copy cut paste click change contextmenu reset`.split(` `);function Mh(e,t){switch(e){case`focusin`:case`focusout`:Th=null;break;case`dragenter`:case`dragleave`:Eh=null;break;case`mouseover`:case`mouseout`:Dh=null;break;case`pointerover`:case`pointerout`:Oh.delete(t.pointerId);break;case`gotpointercapture`:case`lostpointercapture`:kh.delete(t.pointerId)}}function Nh(e,t,n,r,i,a){return e===null||e.nativeEvent!==a?(e={blockedOn:t,domEventName:n,eventSystemFlags:r,nativeEvent:a,targetContainers:[i]},t!==null&&(t=Vt(t),t!==null&&mh(t)),e):(e.eventSystemFlags|=r,t=e.targetContainers,i!==null&&t.indexOf(i)===-1&&t.push(i),e)}function Ph(e,t,n,r,i){switch(t){case`focusin`:return Th=Nh(Th,e,t,n,r,i),!0;case`dragenter`:return Eh=Nh(Eh,e,t,n,r,i),!0;case`mouseover`:return Dh=Nh(Dh,e,t,n,r,i),!0;case`pointerover`:var a=i.pointerId;return Oh.set(a,Nh(Oh.get(a)||null,e,t,n,r,i)),!0;case`gotpointercapture`:return a=i.pointerId,kh.set(a,Nh(kh.get(a)||null,e,t,n,r,i)),!0}return!1}function Fh(e){var t=Bt(e.target);if(t!==null){var n=o(t);if(n!==null){if(t=n.tag,t===13){if(t=s(n),t!==null){e.blockedOn=t,Ot(e.priority,function(){hh(n)});return}}else if(t===31){if(t=c(n),t!==null){e.blockedOn=t,Ot(e.priority,function(){hh(n)});return}}else if(t===3&&n.stateNode.current.memoizedState.isDehydrated){e.blockedOn=n.tag===3?n.stateNode.containerInfo:null;return}}}e.blockedOn=null}function Ih(e){if(e.blockedOn!==null)return!1;for(var t=e.targetContainers;0<t.length;){var n=bh(e.nativeEvent);if(n===null){n=e.nativeEvent;var r=new n.constructor(n.type,n);Dn=r,n.target.dispatchEvent(r),Dn=null}else return t=Vt(n),t!==null&&mh(t),e.blockedOn=n,!1;t.shift()}return!0}function Lh(e,t,n){Ih(e)&&n.delete(t)}function Rh(){wh=!1,Th!==null&&Ih(Th)&&(Th=null),Eh!==null&&Ih(Eh)&&(Eh=null),Dh!==null&&Ih(Dh)&&(Dh=null),Oh.forEach(Lh),kh.forEach(Lh)}function zh(e,n){e.blockedOn===n&&(e.blockedOn=null,wh||(wh=!0,t.unstable_scheduleCallback(t.unstable_NormalPriority,Rh)))}var Bh=null;function Vh(e){Bh!==e&&(Bh=e,t.unstable_scheduleCallback(t.unstable_NormalPriority,function(){Bh===e&&(Bh=null);for(var t=0;t<e.length;t+=3){var n=e[t],r=e[t+1],i=e[t+2];if(typeof r!=`function`){if(Sh(r||n)===null)continue;break}var a=Vt(n);a!==null&&(e.splice(t,3),t-=3,rc(a,{pending:!0,data:i,method:n.method,action:r},r,i))}}))}function Hh(e){function t(t){return zh(t,e)}Th!==null&&zh(Th,e),Eh!==null&&zh(Eh,e),Dh!==null&&zh(Dh,e),Oh.forEach(t),kh.forEach(t);for(var n=0;n<Ah.length;n++){var r=Ah[n];r.blockedOn===e&&(r.blockedOn=null)}for(;0<Ah.length&&(n=Ah[0],n.blockedOn===null);)Fh(n),n.blockedOn===null&&Ah.shift();if(n=(e.ownerDocument||e).$$reactFormReplay,n!=null)for(r=0;r<n.length;r+=3){var i=n[r],a=n[r+1],o=i[jt]||null;if(typeof a==`function`)o||Vh(n);else if(o){var s=null;if(a&&a.hasAttribute(`formAction`)){if(i=a,o=a[jt]||null)s=o.formAction;else if(Sh(i)!==null)continue}else s=o.action;typeof s==`function`?n[r+1]=s:(n.splice(r,3),r-=3),Vh(n)}}}function Uh(){function e(e){e.canIntercept&&e.info===`react-transition`&&e.intercept({handler:function(){return new Promise(function(e){return i=e})},focusReset:`manual`,scroll:`manual`})}function t(){i!==null&&(i(),i=null),r||setTimeout(n,20)}function n(){if(!r&&!navigation.transition){var e=navigation.currentEntry;e&&e.url!=null&&navigation.navigate(e.url,{state:e.getState(),info:`react-transition`,history:`replace`})}}if(typeof navigation==`object`){var r=!1,i=null;return navigation.addEventListener(`navigate`,e),navigation.addEventListener(`navigatesuccess`,t),navigation.addEventListener(`navigateerror`,t),setTimeout(n,100),function(){r=!0,navigation.removeEventListener(`navigate`,e),navigation.removeEventListener(`navigatesuccess`,t),navigation.removeEventListener(`navigateerror`,t),i!==null&&(i(),i=null)}}}function Wh(e){this._internalRoot=e}Gh.prototype.render=Wh.prototype.render=function(e){var t=this._internalRoot;if(t===null)throw Error(i(409));var n=t.current;dh(n,jd(),e,t,null,null)},Gh.prototype.unmount=Wh.prototype.unmount=function(){var e=this._internalRoot;if(e!==null){this._internalRoot=null;var t=e.containerInfo;dh(e.current,2,null,e,null,null),zd(),t[Mt]=null}};function Gh(e){this._internalRoot=e}Gh.prototype.unstable_scheduleHydration=function(e){if(e){var t=Dt();e={blockedOn:null,target:e,priority:t};for(var n=0;n<Ah.length&&t!==0&&t<Ah[n].priority;n++);Ah.splice(n,0,e),n===0&&Fh(e)}};var Kh=n.version;if(Kh!==`19.3.0`)throw Error(i(527,Kh,`19.3.0`));O.findDOMNode=function(e){var t=e._reactInternals;if(t===void 0)throw typeof e.render==`function`?Error(i(188)):(e=Object.keys(e).join(`,`),Error(i(268,e)));return e=d(t),e=e===null?null:p(e),e=e===null?null:e.stateNode,e};var qh={bundleType:0,version:`19.3.0`,rendererPackageName:`react-dom`,currentDispatcherRef:D,reconcilerVersion:`19.3.0`};if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<`u`){var Jh=__REACT_DEVTOOLS_GLOBAL_HOOK__;if(!Jh.isDisabled&&Jh.supportsFiber)try{rt=Jh.inject(qh),it=Jh}catch{}}e.createRoot=function(e,t){if(!a(e))throw Error(i(299));var n=!1,r=``,o=Ec,s=Dc,c=Oc;return t!=null&&(!0===t.unstable_strictMode&&(n=!0),t.identifierPrefix!==void 0&&(r=t.identifierPrefix),t.onUncaughtError!==void 0&&(o=t.onUncaughtError),t.onCaughtError!==void 0&&(s=t.onCaughtError),t.onRecoverableError!==void 0&&(c=t.onRecoverableError)),t=lh(e,1,!1,null,null,n,r,null,o,s,c,Uh),e[Mt]=t.current,Wf(e),new Wh(t)}})),g=o(((e,t)=>{function n(){if(typeof __REACT_DEVTOOLS_GLOBAL_HOOK__<`u`&&typeof __REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE==`function`)try{__REACT_DEVTOOLS_GLOBAL_HOOK__.checkDCE(n)}catch(e){console.error(e)}}n(),t.exports=h()})),_=c(u()),v=g(),y=function(){return y=Object.assign||function(e){for(var t,n=1,r=arguments.length;n<r;n++)for(var i in t=arguments[n],t)Object.prototype.hasOwnProperty.call(t,i)&&(e[i]=t[i]);return e},y.apply(this,arguments)};function b(e,t,n){if(n||arguments.length===2)for(var r=0,i=t.length,a;r<i;r++)(a||!(r in t))&&(a||=Array.prototype.slice.call(t,0,r),a[r]=t[r]);return e.concat(a||Array.prototype.slice.call(t))}var x=`-ms-`,S=`-moz-`,C=`-webkit-`,ee=`comm`,w=`rule`,te=`decl`,T=`@import`,ne=`@namespace`,re=`@keyframes`,ie=`@layer`,ae=Math.abs,oe=String.fromCharCode,se=Object.assign;function ce(e,t){return fe(e,0)^45?(((t<<2^fe(e,0))<<2^fe(e,1))<<2^fe(e,2))<<2^fe(e,3):0}function le(e){return e.trim()}function ue(e,t){return(e=t.exec(e))?e[0]:e}function E(e,t,n){return e.replace(t,n)}function de(e,t,n){return e.indexOf(t,n)}function fe(e,t){return e.charCodeAt(t)|0}function pe(e,t,n){return e.slice(t,n)}function me(e){return e.length}function he(e){return e.length}function ge(e,t){return t.push(e),e}function _e(e,t){return e.map(t).join(``)}function ve(e,t){return e.filter(function(e){return!ue(e,t)})}var ye=1,be=1,xe=0,Se=0,Ce=0,D=``;function O(e,t,n,r,i,a,o,s){return{value:e,root:t,parent:n,type:r,props:i,children:a,line:ye,column:be,length:o,return:``,siblings:s}}function we(e,t){return se(O(``,null,null,``,null,null,0,e.siblings),e,{length:-e.length},t)}function Te(e){for(;e.root;)e=we(e.root,{children:[e]});ge(e,e.siblings)}function Ee(){return Ce}function De(){return Ce=Se>0?fe(D,--Se):0,be--,Ce===10&&(be=1,ye--),Ce}function k(){return Ce=Se<xe?fe(D,Se++):0,be++,Ce===10&&(be=1,ye++),Ce}function A(){return fe(D,Se)}function Oe(){return Se}function ke(e,t){return pe(D,e,t)}function Ae(e){switch(e){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function je(e){return ye=be=1,xe=me(D=e),Se=0,[]}function Me(e){return D=``,e}function Ne(e){return le(ke(Se-1,Ie(e===91?e+2:e===40?e+1:e)))}function Pe(e){for(;(Ce=A())&&Ce<33;)k();return Ae(e)>2||Ae(Ce)>3?``:` `}function Fe(e,t){for(;--t&&k()&&!(Ce<48||Ce>102||Ce>57&&Ce<65||Ce>70&&Ce<97););return ke(e,Oe()+(t<6&&A()==32&&k()==32))}function Ie(e){for(;k();)switch(Ce){case e:return Se;case 34:case 39:e!==34&&e!==39&&Ie(Ce);break;case 40:e===41&&Ie(e);break;case 92:k()}return Se}function Le(e,t){for(;k()&&e+Ce!==57&&(e+Ce!==84||A()!==47););return`/*`+ke(t,Se-1)+`*`+oe(e===47?e:k())}function Re(e){for(;!Ae(A());)k();return ke(e,Se)}function ze(e){return Me(Be(``,null,null,null,[``],e=je(e),0,[0],e))}function Be(e,t,n,r,i,a,o,s,c){for(var l=0,u=0,d=o,f=0,p=0,m=0,h=1,g=1,_=1,v=0,y=``,b=i,x=a,S=r,C=y;g;)switch(m=v,v=k()){case 40:if(m!=108&&fe(C,d-1)==58){de(C+=E(Ne(v),`&`,`&\f`),`&\f`,ae(l?s[l-1]:0))!=-1&&(_=-1);break}case 34:case 39:case 91:C+=Ne(v);break;case 9:case 10:case 13:case 32:C+=Pe(m);break;case 92:C+=Fe(Oe()-1,7);continue;case 47:switch(A()){case 42:case 47:ge(He(Le(k(),Oe()),t,n,c),c),(Ae(m||1)==5||Ae(A()||1)==5)&&me(C)&&pe(C,-1,void 0)!==` `&&(C+=` `);break;default:C+=`/`}break;case 123*h:s[l++]=me(C)*_;case 125*h:case 59:case 0:switch(v){case 0:case 125:g=0;case 59+u:_==-1&&(C=E(C,/\f/g,``)),p>0&&(me(C)-d||h===0&&m===47)&&ge(p>32?Ue(C+`;`,r,n,d-1,c):Ue(E(C,` `,``)+`;`,r,n,d-2,c),c);break;case 59:C+=`;`;default:if(ge(S=Ve(C,t,n,l,u,i,s,y,b=[],x=[],d,a),a),v===123){if(u===0)Be(C,t,S,S,b,a,d,s,x);else{switch(f){case 99:if(fe(C,3)===110)break;case 108:if(fe(C,2)===97)break;default:u=0;case 100:case 109:case 115:}u?Be(e,S,S,r&&ge(Ve(e,S,S,0,0,i,s,y,i,b=[],d,x),x),i,x,d,s,r?b:x):Be(C,S,S,S,[``],x,0,s,x)}}}l=u=p=0,h=_=1,y=C=``,d=o;break;case 58:d=1+me(C),p=m;default:if(h<1){if(v==123)--h;else if(v==125&&h++==0&&De()==125)continue}switch(C+=oe(v),v*h){case 38:_=u>0?1:(C+=`\f`,-1);break;case 44:s[l++]=(me(C)-1)*_,_=1;break;case 64:A()===45&&(C+=Ne(k())),f=A(),u=d=me(y=C+=Re(Oe())),v++;break;case 45:m===45&&me(C)==2&&(h=0)}}return a}function Ve(e,t,n,r,i,a,o,s,c,l,u,d){for(var f=i-1,p=i===0?a:[``],m=he(p),h=0,g=0,_=0;h<r;++h)for(var v=0,y=pe(e,f+1,f=ae(g=o[h])),b=e;v<m;++v)(b=le(g>0?p[v]+` `+y:E(y,/&\f/g,p[v])))&&(c[_++]=b);return O(e,t,n,i===0?w:s,c,l,u,d)}function He(e,t,n,r){return O(e,t,n,ee,oe(Ee()),pe(e,2,-2),0,r)}function Ue(e,t,n,r,i){return O(e,t,n,te,pe(e,0,r),pe(e,r+1,-1),r,i)}function We(e,t,n){switch(ce(e,t)){case 5103:return C+`print-`+e+e;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:case 6391:case 5879:case 5623:case 6135:case 4599:return C+e+e;case 4855:return C+e.replace(`add`,`source-over`).replace(`substract`,`source-out`).replace(`intersect`,`source-in`).replace(`exclude`,`xor`)+e;case 4789:return S+e+e;case 5349:case 4246:case 4810:case 6968:case 2756:return C+e+S+e+x+e+e;case 5936:switch(fe(e,t+11)){case 114:return C+e+x+E(e,/[svh]\w+-[tblr]{2}/,`tb`)+e;case 108:return C+e+x+E(e,/[svh]\w+-[tblr]{2}/,`tb-rl`)+e;case 45:return C+e+x+E(e,/[svh]\w+-[tblr]{2}/,`lr`)+e}case 6828:case 4268:case 2903:return C+e+x+e+e;case 6165:return C+e+x+`flex-`+e+e;case 5187:return C+e+E(e,/(\w+).+(:[^]+)/,C+`box-$1$2`+x+`flex-$1$2`)+e;case 5443:return C+e+x+`flex-item-`+E(e,/flex-|-self/g,``)+(ue(e,/flex-|baseline/)?``:x+`grid-row-`+E(e,/flex-|-self/g,``))+e;case 4675:return C+e+x+`flex-line-pack`+E(e,/align-content|flex-|-self/g,``)+e;case 5548:return C+e+x+E(e,`shrink`,`negative`)+e;case 5292:return C+e+x+E(e,`basis`,`preferred-size`)+e;case 6060:return C+`box-`+E(e,`-grow`,``)+C+e+x+E(e,`grow`,`positive`)+e;case 4554:return C+E(e,/([^-])(transform)/g,`$1`+C+`$2`)+e;case 6187:return E(E(E(e,/(zoom-|grab)/,C+`$1`),/(image-set)/,C+`$1`),e,``)+e;case 5495:case 3959:return E(e,/(image-set\([^]*)/,C+"$1$`$1");case 4968:return E(E(e,/(.+:)(flex-)?(.*)/,C+`box-pack:$3`+x+`flex-pack:$3`),/space-between/,`justify`)+C+e+e;case 4200:if(!ue(e,/flex-|baseline/))return x+`grid-column-align`+pe(e,t)+e;break;case 2592:case 3360:return x+E(e,`template-`,``)+e;case 4384:case 3616:return n&&n.some(function(e,n){return t=n,ue(e.props,/grid-\w+-end/)})?~de(e+(n=n[t].value),`span`,0)?e:x+E(e,`-start`,``)+e+x+`grid-row-span:`+(~de(n,`span`,0)?ue(n,/\d+/):+ue(n,/\d+/)-ue(e,/\d+/))+`;`:x+E(e,`-start`,``)+e;case 4896:case 4128:return n&&n.some(function(e){return ue(e.props,/grid-\w+-start/)})?e:x+E(E(e,`-end`,`-span`),`span `,``)+e;case 4095:case 3583:case 4068:case 2532:return E(e,/(.+)-inline(.+)/,C+`$1$2`)+e;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(me(e)-1-t>6)switch(fe(e,t+1)){case 109:if(fe(e,t+4)!==45)break;case 102:return E(e,/(.+:)(.+)-([^]+)/,`$1`+C+`$2-$3$1`+S+(fe(e,t+3)==108?`$3`:`$2-$3`))+e;case 115:return~de(e,`stretch`,0)?We(E(e,`stretch`,`fill-available`),t,n)+e:e}break;case 5152:case 5920:return E(e,/(.+?):(\d+)(\s*\/\s*(span)?\s*(\d+))?(.*)/,function(t,n,r,i,a,o,s){return x+n+`:`+r+s+(i?x+n+`-span:`+(a?o:+o-r)+s:``)+e});case 4949:if(fe(e,t+6)===121)return E(e,`:`,`:`+C)+e;break;case 6444:switch(fe(e,fe(e,14)===45?18:11)){case 120:return E(e,/(.+:)([^;\s!]+)(;|(\s+)?!.+)?/,`$1`+C+(fe(e,14)===45?`inline-`:``)+`box$3$1`+C+`$2$3$1`+x+`$2box$3`)+e;case 100:return E(e,`:`,`:`+x)+e}break;case 5719:case 2647:case 2135:case 3927:case 2391:return E(e,`scroll-`,`scroll-snap-`)+e}return e}function Ge(e,t){for(var n=``,r=0;r<e.length;r++)n+=t(e[r],r,e,t)||``;return n}function Ke(e,t,n,r){switch(e.type){case ie:if(e.children.length)break;case T:case ne:case te:return e.return=e.return||e.value;case ee:return``;case re:return e.return=e.value+`{`+Ge(e.children,r)+`}`;case w:if(!me(e.value=e.props.join(`,`)))return``}return me(n=Ge(e.children,r))?e.return=e.value+`{`+n+`}`:``}function qe(e){var t=he(e);return function(n,r,i,a){for(var o=``,s=0;s<t;s++)o+=e[s](n,r,i,a)||``;return o}}function Je(e){return function(t){t.root||(t=t.return)&&e(t)}}function Ye(e,t,n,r){if(e.length>-1&&!e.return)switch(e.type){case te:e.return=We(e.value,e.length,n);return;case re:return Ge([we(e,{value:E(e.value,`@`,`@`+C)})],r);case w:if(e.length)return _e(n=e.props,function(t){switch(ue(t,r=/(::plac\w+|:read-\w+)/)){case`:read-only`:case`:read-write`:Te(we(e,{props:[E(t,/:(read-\w+)/,`:`+S+`$1`)]})),Te(we(e,{props:[t]})),se(e,{props:ve(n,r)});break;case`::placeholder`:Te(we(e,{props:[E(t,/:(plac\w+)/,`:`+C+`input-$1`)]})),Te(we(e,{props:[E(t,/:(plac\w+)/,`:`+S+`$1`)]})),Te(we(e,{props:[E(t,/:(plac\w+)/,x+`input-$1`)]})),Te(we(e,{props:[t]})),se(e,{props:ve(n,r)})}return``})}}var Xe={animationIterationCount:1,aspectRatio:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,scale:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},Ze=typeof process<`u`&&({}.REACT_APP_SC_ATTR||{}.SC_ATTR)||`data-styled`,Qe=`active`,$e=`data-styled-version`,et=`6.3.8`,tt=`/*!sc*/
`,nt=typeof window<`u`&&typeof document<`u`,rt=_.createContext===void 0,it=!!(typeof SC_DISABLE_SPEEDY==`boolean`?SC_DISABLE_SPEEDY:typeof process<`u`&&{}.REACT_APP_SC_DISABLE_SPEEDY!==void 0&&{}.REACT_APP_SC_DISABLE_SPEEDY!==``?{}.REACT_APP_SC_DISABLE_SPEEDY!==`false`&&{}.REACT_APP_SC_DISABLE_SPEEDY:typeof process<`u`&&{}.SC_DISABLE_SPEEDY!==void 0&&{}.SC_DISABLE_SPEEDY!==``&&{}.SC_DISABLE_SPEEDY!==`false`&&{}.SC_DISABLE_SPEEDY),at=Object.freeze([]),ot=Object.freeze({});function st(e,t,n){return n===void 0&&(n=ot),e.theme!==n.theme&&e.theme||t||n.theme}var ct=new Set(`a.abbr.address.area.article.aside.audio.b.bdi.bdo.blockquote.body.button.br.canvas.caption.cite.code.col.colgroup.data.datalist.dd.del.details.dfn.dialog.div.dl.dt.em.embed.fieldset.figcaption.figure.footer.form.h1.h2.h3.h4.h5.h6.header.hgroup.hr.html.i.iframe.img.input.ins.kbd.label.legend.li.main.map.mark.menu.meter.nav.object.ol.optgroup.option.output.p.picture.pre.progress.q.rp.rt.ruby.s.samp.search.section.select.slot.small.span.strong.sub.summary.sup.table.tbody.td.template.textarea.tfoot.th.thead.time.tr.u.ul.var.video.wbr.circle.clipPath.defs.ellipse.feBlend.feColorMatrix.feComponentTransfer.feComposite.feConvolveMatrix.feDiffuseLighting.feDisplacementMap.feDistantLight.feDropShadow.feFlood.feFuncA.feFuncB.feFuncG.feFuncR.feGaussianBlur.feImage.feMerge.feMergeNode.feMorphology.feOffset.fePointLight.feSpecularLighting.feSpotLight.feTile.feTurbulence.filter.foreignObject.g.image.line.linearGradient.marker.mask.path.pattern.polygon.polyline.radialGradient.rect.stop.svg.switch.symbol.text.textPath.tspan.use`.split(`.`)),lt=/[!"#$%&'()*+,./:;<=>?@[\\\]^`{|}~-]+/g,ut=/(^-|-$)/g;function dt(e){return e.replace(lt,`-`).replace(ut,``)}var ft=/(a)(d)/gi,pt=function(e){return String.fromCharCode(e+(e>25?39:97))};function mt(e){var t,n=``;for(t=Math.abs(e);t>52;t=t/52|0)n=pt(t%52)+n;return(pt(t%52)+n).replace(ft,`$1-$2`)}var ht,gt=function(e,t){for(var n=t.length;n;)e=33*e^t.charCodeAt(--n);return e},_t=function(e){return gt(5381,e)};function vt(e){return mt(_t(e)>>>0)}function yt(e){return e.displayName||e.name||`Component`}function bt(e){return typeof e==`string`&&!0}var xt=typeof Symbol==`function`&&Symbol.for,St=xt?Symbol.for(`react.memo`):60115,Ct=xt?Symbol.for(`react.forward_ref`):60112,wt={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},Tt={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},Et={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},Dt=((ht={})[Ct]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},ht[St]=Et,ht);function Ot(e){return(`type`in(t=e)&&t.type.$$typeof)===St?Et:`$$typeof`in e?Dt[e.$$typeof]:wt;var t}var kt=Object.defineProperty,At=Object.getOwnPropertyNames,jt=Object.getOwnPropertySymbols,Mt=Object.getOwnPropertyDescriptor,Nt=Object.getPrototypeOf,Pt=Object.prototype;function Ft(e,t,n){if(typeof t!=`string`){if(Pt){var r=Nt(t);r&&r!==Pt&&Ft(e,r,n)}var i=At(t);jt&&(i=i.concat(jt(t)));for(var a=Ot(e),o=Ot(t),s=0;s<i.length;++s){var c=i[s];if(!(c in Tt||n&&n[c]||o&&c in o||a&&c in a)){var l=Mt(t,c);try{kt(e,c,l)}catch{}}}}return e}function It(e){return typeof e==`function`}function Lt(e){return typeof e==`object`&&`styledComponentId`in e}function Rt(e,t){return e&&t?`${e} ${t}`:e||t||``}function zt(e,t){if(e.length===0)return``;for(var n=e[0],r=1;r<e.length;r++)n+=t?t+e[r]:e[r];return n}function Bt(e){return typeof e==`object`&&!!e&&e.constructor.name===Object.name&&!(`props`in e&&e.$$typeof)}function Vt(e,t,n){if(n===void 0&&(n=!1),!n&&!Bt(e)&&!Array.isArray(e))return t;if(Array.isArray(t))for(var r=0;r<t.length;r++)e[r]=Vt(e[r],t[r]);else if(Bt(t))for(var r in t)e[r]=Vt(e[r],t[r]);return e}function Ht(e,t){Object.defineProperty(e,"toString",{value:t})}function Ut(e){var t=[...arguments].slice(1);return Error(`An error occurred. See https://github.com/styled-components/styled-components/blob/main/packages/styled-components/src/utils/errors.md#${e} for more information.${t.length>0?` Args: ${t.join(`, `)}`:``}`)}var Wt=function(){function e(e){this.groupSizes=new Uint32Array(512),this.length=512,this.tag=e}return e.prototype.indexOfGroup=function(e){for(var t=0,n=0;n<e;n++)t+=this.groupSizes[n];return t},e.prototype.insertRules=function(e,t){if(e>=this.groupSizes.length){for(var n=this.groupSizes,r=n.length,i=r;e>=i;)if((i<<=1)<0)throw Ut(16,`${e}`);this.groupSizes=new Uint32Array(i),this.groupSizes.set(n),this.length=i;for(var a=r;a<i;a++)this.groupSizes[a]=0}for(var o=this.indexOfGroup(e+1),s=(a=0,t.length);a<s;a++)this.tag.insertRule(o,t[a])&&(this.groupSizes[e]++,o++)},e.prototype.clearGroup=function(e){if(e<this.length){var t=this.groupSizes[e],n=this.indexOfGroup(e),r=n+t;this.groupSizes[e]=0;for(var i=n;i<r;i++)this.tag.deleteRule(n)}},e.prototype.getGroup=function(e){var t=``;if(e>=this.length||this.groupSizes[e]===0)return t;for(var n=this.groupSizes[e],r=this.indexOfGroup(e),i=r+n,a=r;a<i;a++)t+=`${this.tag.getRule(a)}${tt}`;return t},e}(),Gt=new Map,Kt=new Map,qt=1,Jt=function(e){if(Gt.has(e))return Gt.get(e);for(;Kt.has(qt);)qt++;var t=qt++;return Gt.set(e,t),Kt.set(t,e),t},Yt=function(e,t){qt=t+1,Gt.set(e,t),Kt.set(t,e)},Xt=`style[${Ze}][${$e}="${et}"]`,Zt=RegExp(`^${Ze}\\.g(\\d+)\\[id="([\\w\\d-]+)"\\].*?"([^"]*)`),Qt=function(e,t,n){for(var r,i=n.split(`,`),a=0,o=i.length;a<o;a++)(r=i[a])&&e.registerName(t,r)},$t=function(e,t){for(var n=(t.textContent??``).split(tt),r=[],i=0,a=n.length;i<a;i++){var o=n[i].trim();if(o){var s=o.match(Zt);if(s){var c=0|parseInt(s[1],10),l=s[2];c!==0&&(Yt(l,c),Qt(e,l,s[3]),e.getTag().insertRules(c,r)),r.length=0}else r.push(o)}}},j=function(e){for(var t=document.querySelectorAll(Xt),n=0,r=t.length;n<r;n++){var i=t[n];i&&i.getAttribute(Ze)!==Qe&&($t(e,i),i.parentNode&&i.parentNode.removeChild(i))}};function en(){return typeof __webpack_nonce__<`u`?__webpack_nonce__:null}var tn=function(e){var t=document.head,n=e||t,r=document.createElement(`style`),i=function(e){var t=Array.from(e.querySelectorAll(`style[${Ze}]`));return t[t.length-1]}(n),a=i===void 0?null:i.nextSibling;r.setAttribute(Ze,Qe),r.setAttribute($e,et);var o=en();return o&&r.setAttribute(`nonce`,o),n.insertBefore(r,a),r},nn=function(){function e(e){this.element=tn(e),this.element.appendChild(document.createTextNode(``)),this.sheet=function(e){if(e.sheet)return e.sheet;for(var t=document.styleSheets,n=0,r=t.length;n<r;n++){var i=t[n];if(i.ownerNode===e)return i}throw Ut(17)}(this.element),this.length=0}return e.prototype.insertRule=function(e,t){try{return this.sheet.insertRule(t,e),this.length++,!0}catch{return!1}},e.prototype.deleteRule=function(e){this.sheet.deleteRule(e),this.length--},e.prototype.getRule=function(e){var t=this.sheet.cssRules[e];return t&&t.cssText?t.cssText:``},e}(),rn=function(){function e(e){this.element=tn(e),this.nodes=this.element.childNodes,this.length=0}return e.prototype.insertRule=function(e,t){if(e<=this.length&&e>=0){var n=document.createTextNode(t);return this.element.insertBefore(n,this.nodes[e]||null),this.length++,!0}return!1},e.prototype.deleteRule=function(e){this.element.removeChild(this.nodes[e]),this.length--},e.prototype.getRule=function(e){return e<this.length?this.nodes[e].textContent:``},e}(),an=function(){function e(e){this.rules=[],this.length=0}return e.prototype.insertRule=function(e,t){return e<=this.length&&(this.rules.splice(e,0,t),this.length++,!0)},e.prototype.deleteRule=function(e){this.rules.splice(e,1),this.length--},e.prototype.getRule=function(e){return e<this.length?this.rules[e]:``},e}(),on=nt,sn={isServer:!nt,useCSSOMInjection:!it},cn=function(){function e(e,t,n){e===void 0&&(e=ot),t===void 0&&(t={});var r=this;this.options=y(y({},sn),e),this.gs=t,this.names=new Map(n),this.server=!!e.isServer,!this.server&&nt&&on&&(on=!1,j(this)),Ht(this,function(){return function(e){for(var t=e.getTag(),n=t.length,r=``,i=function(n){var i=function(e){return Kt.get(e)}(n);if(i===void 0)return`continue`;var a=e.names.get(i),o=t.getGroup(n);if(a===void 0||!a.size||o.length===0)return`continue`;var s=`${Ze}.g${n}[id="${i}"]`,c=``;a!==void 0&&a.forEach(function(e){e.length>0&&(c+=`${e},`)}),r+=`${o}${s}{content:"${c}"}${tt}`},a=0;a<n;a++)i(a);return r}(r)})}return e.registerId=function(e){return Jt(e)},e.prototype.rehydrate=function(){!this.server&&nt&&j(this)},e.prototype.reconstructWithOptions=function(t,n){return n===void 0&&(n=!0),new e(y(y({},this.options),t),this.gs,n&&this.names||void 0)},e.prototype.allocateGSInstance=function(e){return this.gs[e]=(this.gs[e]||0)+1},e.prototype.getTag=function(){return this.tag||=(e=function(e){var t=e.useCSSOMInjection,n=e.target;return e.isServer?new an(n):t?new nn(n):new rn(n)}(this.options),new Wt(e));var e},e.prototype.hasNameForId=function(e,t){return this.names.has(e)&&this.names.get(e).has(t)},e.prototype.registerName=function(e,t){if(Jt(e),this.names.has(e))this.names.get(e).add(t);else{var n=new Set;n.add(t),this.names.set(e,n)}},e.prototype.insertRules=function(e,t,n){this.registerName(e,t),this.getTag().insertRules(Jt(e),n)},e.prototype.clearNames=function(e){this.names.has(e)&&this.names.get(e).clear()},e.prototype.clearRules=function(e){this.getTag().clearGroup(Jt(e)),this.clearNames(e)},e.prototype.clearTag=function(){this.tag=void 0},e}(),ln=/&/g,un=47;function dn(e){if(e.indexOf(`}`)===-1)return!1;for(var t=e.length,n=0,r=0,i=!1,a=0;a<t;a++){var o=e.charCodeAt(a);if(r!==0||i||o!==un||e.charCodeAt(a+1)!==42){if(i)o===42&&e.charCodeAt(a+1)===un&&(i=!1,a++);else if(o!==34&&o!==39||a!==0&&e.charCodeAt(a-1)===92){if(r===0){if(o===123)n++;else if(o===125&&--n<0)return!0}}else r===0?r=o:r===o&&(r=0)}else i=!0,a++}return n!==0||r!==0}function fn(e,t){return e.map(function(e){return e.type===`rule`&&(e.value=`${t} ${e.value}`,e.value=e.value.replaceAll(`,`,`,${t} `),e.props=e.props.map(function(e){return`${t} ${e}`})),Array.isArray(e.children)&&e.type!==`@keyframes`&&(e.children=fn(e.children,t)),e})}function pn(e){var t,n,r,i=e===void 0?ot:e,a=i.options,o=a===void 0?ot:a,s=i.plugins,c=s===void 0?at:s,l=function(e,r,i){return i.startsWith(n)&&i.endsWith(n)&&i.replaceAll(n,``).length>0?`.${t}`:e},u=c.slice();u.push(function(e){e.type===`rule`&&e.value.includes(`&`)&&(e.props[0]=e.props[0].replace(ln,n).replace(r,l))}),o.prefix&&u.push(Ye),u.push(Ke);var d=function(e,i,a,s){i===void 0&&(i=``),a===void 0&&(a=``),s===void 0&&(s=`&`),t=s,n=i,r=RegExp(`\\${n}\\b`,`g`);var c=function(e){if(!dn(e))return e;for(var t=e.length,n=``,r=0,i=0,a=0,o=!1,s=0;s<t;s++){var c=e.charCodeAt(s);if(a!==0||o||c!==un||e.charCodeAt(s+1)!==42){if(o)c===42&&e.charCodeAt(s+1)===un&&(o=!1,s++);else if(c!==34&&c!==39||s!==0&&e.charCodeAt(s-1)===92){if(a===0){if(c===123)i++;else if(c===125){if(--i<0){for(var l=s+1;l<t;){var u=e.charCodeAt(l);if(u===59||u===10)break;l++}l<t&&e.charCodeAt(l)===59&&l++,i=0,s=l-1,r=l;continue}i===0&&(n+=e.substring(r,s+1),r=s+1)}else c===59&&i===0&&(n+=e.substring(r,s+1),r=s+1)}}else a===0?a=c:a===c&&(a=0)}else o=!0,s++}if(r<t){var d=e.substring(r);dn(d)||(n+=d)}return n}(function(e){if(e.indexOf(`//`)===-1)return e;for(var t=e.length,n=[],r=0,i=0,a=0,o=0;i<t;){var s=e.charCodeAt(i);if(s!==34&&s!==39||i!==0&&e.charCodeAt(i-1)===92){if(a===0){if(s===40&&i>=3&&(32|e.charCodeAt(i-1))==108&&(32|e.charCodeAt(i-2))==114&&(32|e.charCodeAt(i-3))==117)o=1,i++;else if(o>0)s===41?o--:s===40&&o++,i++;else if(s===un&&i+1<t&&e.charCodeAt(i+1)===un){for(i>r&&n.push(e.substring(r,i));i<t&&e.charCodeAt(i)!==10;)i++;r=i}else i++}else i++}else a===0?a=s:a===s&&(a=0),i++}return r===0?e:(r<t&&n.push(e.substring(r)),n.join(``))}(e)),l=ze(a||i?`${a} ${i} { ${c} }`:c);o.namespace&&(l=fn(l,o.namespace));var d=[];return Ge(l,qe(u.concat(Je(function(e){return d.push(e)})))),d};return d.hash=c.length?c.reduce(function(e,t){return t.name||Ut(15),gt(e,t.name)},5381).toString():``,d}var mn=new cn,hn=pn(),gn={shouldForwardProp:void 0,styleSheet:mn,stylis:hn},_n=rt?{Provider:function(e){return e.children},Consumer:function(e){return(0,e.children)(gn)}}:_.createContext(gn);_n.Consumer;var vn=rt?{Provider:function(e){return e.children},Consumer:function(e){return(0,e.children)(void 0)}}:_.createContext(void 0);function yn(){return rt?gn:_.useContext(_n)}function bn(e){if(rt||!_.useMemo)return e.children;var t=yn().styleSheet,n=_.useMemo(function(){var n=t;return e.sheet?n=e.sheet:e.target&&(n=n.reconstructWithOptions({target:e.target},!1)),e.disableCSSOMInjection&&(n=n.reconstructWithOptions({useCSSOMInjection:!1})),n},[e.disableCSSOMInjection,e.sheet,e.target,t]),r=_.useMemo(function(){return pn({options:{namespace:e.namespace,prefix:e.enableVendorPrefixes},plugins:e.stylisPlugins})},[e.enableVendorPrefixes,e.namespace,e.stylisPlugins]),i=_.useMemo(function(){return{shouldForwardProp:e.shouldForwardProp,styleSheet:n,stylis:r}},[e.shouldForwardProp,n,r]);return _.createElement(_n.Provider,{value:i},_.createElement(vn.Provider,{value:r},e.children))}var xn=function(){function e(e,t){var n=this;this.inject=function(e,t){t===void 0&&(t=hn);var r=n.name+t.hash;e.hasNameForId(n.id,r)||e.insertRules(n.id,r,t(n.rules,r,`@keyframes`))},this.name=e,this.id=`sc-keyframes-${e}`,this.rules=t,Ht(this,function(){throw Ut(12,String(n.name))})}return e.prototype.getName=function(e){return e===void 0&&(e=hn),this.name+e.hash},e}();function Sn(e,t){return t==null||typeof t==`boolean`||t===``?``:typeof t!=`number`||t===0||e in Xe||e.startsWith(`--`)?String(t).trim():`${t}px`}var Cn=function(e){return e>=`A`&&e<=`Z`};function wn(e){for(var t=``,n=0;n<e.length;n++){var r=e[n];if(n===1&&r===`-`&&e[0]===`-`)return e;Cn(r)?t+=`-`+r.toLowerCase():t+=r}return t.startsWith(`ms-`)?`-`+t:t}var Tn=function(e){return e==null||!1===e||e===``},En=function(e){var t=[];for(var n in e){var r=e[n];e.hasOwnProperty(n)&&!Tn(r)&&(Array.isArray(r)&&r.isCss||It(r)?t.push(`${wn(n)}:`,r,`;`):Bt(r)?t.push.apply(t,b(b([`${n} {`],En(r),!1),[`}`],!1)):t.push(`${wn(n)}: ${Sn(n,r)};`))}return t};function Dn(e,t,n,r){if(Tn(e))return[];if(Lt(e))return[`.${e.styledComponentId}`];if(It(e))return!It(i=e)||i.prototype&&i.prototype.isReactComponent||!t?[e]:Dn(e(t),t,n,r);var i;return e instanceof xn?n?(e.inject(n,r),[e.getName(r)]):[e]:Bt(e)?En(e):Array.isArray(e)?Array.prototype.concat.apply(at,e.map(function(e){return Dn(e,t,n,r)})):[e.toString()]}function On(e){for(var t=0;t<e.length;t+=1){var n=e[t];if(It(n)&&!Lt(n))return!1}return!0}var kn=_t(et),An=function(){function e(e,t,n){this.rules=e,this.staticRulesId=``,this.isStatic=(n===void 0||n.isStatic)&&On(e),this.componentId=t,this.baseHash=gt(kn,t),this.baseStyle=n,cn.registerId(t)}return e.prototype.generateAndInjectStyles=function(e,t,n){var r=this.baseStyle?this.baseStyle.generateAndInjectStyles(e,t,n).className:``;if(this.isStatic&&!n.hash){if(this.staticRulesId&&t.hasNameForId(this.componentId,this.staticRulesId))r=Rt(r,this.staticRulesId);else{var i=zt(Dn(this.rules,e,t,n)),a=mt(gt(this.baseHash,i)>>>0);if(!t.hasNameForId(this.componentId,a)){var o=n(i,`.${a}`,void 0,this.componentId);t.insertRules(this.componentId,a,o)}r=Rt(r,a),this.staticRulesId=a}}else{for(var s=gt(this.baseHash,n.hash),c=``,l=0;l<this.rules.length;l++){var u=this.rules[l];if(typeof u==`string`)c+=u;else if(u){var d=zt(Dn(u,e,t,n));s=gt(s,d+l),c+=d}}if(c){var f=mt(s>>>0);if(!t.hasNameForId(this.componentId,f)){var p=n(c,`.${f}`,void 0,this.componentId);t.insertRules(this.componentId,f,p)}r=Rt(r,f)}}return{className:r,css:typeof window>`u`?t.getTag().getGroup(Jt(this.componentId)):``}},e}(),jn=rt?{Provider:function(e){return e.children},Consumer:function(e){return(0,e.children)(void 0)}}:_.createContext(void 0);jn.Consumer;var Mn={};function Nn(e,t,n){var r=Lt(e),i=e,a=!bt(e),o=t.attrs,s=o===void 0?at:o,c=t.componentId,l=c===void 0?function(e,t){var n=typeof e==`string`?dt(e):`sc`;Mn[n]=(Mn[n]||0)+1;var r=`${n}-${vt(et+n+Mn[n])}`;return t?`${t}-${r}`:r}(t.displayName,t.parentComponentId):c,u=t.displayName,d=u===void 0?function(e){return bt(e)?`styled.${e}`:`Styled(${yt(e)})`}(e):u,f=t.displayName&&t.componentId?`${dt(t.displayName)}-${t.componentId}`:t.componentId||l,p=r&&i.attrs?i.attrs.concat(s).filter(Boolean):s,m=t.shouldForwardProp;if(r&&i.shouldForwardProp){var h=i.shouldForwardProp;if(t.shouldForwardProp){var g=t.shouldForwardProp;m=function(e,t){return h(e,t)&&g(e,t)}}else m=h}var v=new An(n,f,r?i.componentStyle:void 0);function b(e,t){return function(e,t,n){var r=e.attrs,i=e.componentStyle,a=e.defaultProps,o=e.foldedComponentIds,s=e.styledComponentId,c=e.target,l=rt?void 0:_.useContext(jn),u=yn(),d=e.shouldForwardProp||u.shouldForwardProp,f=st(t,l,a)||ot,p=function(e,t,n){for(var r,i=y(y({},t),{className:void 0,theme:n}),a=0;a<e.length;a+=1){var o=It(r=e[a])?r(i):r;for(var s in o)s===`className`?i.className=Rt(i.className,o[s]):s===`style`?i.style=y(y({},i.style),o[s]):i[s]=o[s]}return`className`in t&&typeof t.className==`string`&&(i.className=Rt(i.className,t.className)),i}(r,t,f),m=p.as||c,h={};for(var g in p)p[g]===void 0||g[0]===`$`||g===`as`||g===`theme`&&p.theme===f||(g===`forwardedAs`?h.as=p.forwardedAs:d&&!d(g,m)||(h[g]=p[g]));var v=function(e,t){var n=yn();return e.generateAndInjectStyles(t,n.styleSheet,n.stylis)}(i,p),b=v.className,x=v.css,S=Rt(o,s);b&&(S+=` `+b),p.className&&(S+=` `+p.className),h[bt(m)&&!ct.has(m)?`class`:`className`]=S,n&&(h.ref=n);var C=(0,_.createElement)(m,h);return rt&&x?_.createElement(_.Fragment,null,_.createElement(`style`,{precedence:`styled-components`,href:`sc-${s}-${b}`,children:x}),C):C}(x,e,t)}b.displayName=d;var x=_.forwardRef(b);return x.attrs=p,x.componentStyle=v,x.displayName=d,x.shouldForwardProp=m,x.foldedComponentIds=r?Rt(i.foldedComponentIds,i.styledComponentId):``,x.styledComponentId=f,x.target=r?i.target:e,Object.defineProperty(x,"defaultProps",{get:function(){return this._foldedDefaultProps},set:function(e){this._foldedDefaultProps=r?function(e){for(var t=[...arguments].slice(1),n=0,r=t;n<r.length;n++)Vt(e,r[n],!0);return e}({},i.defaultProps,e):e}}),Ht(x,function(){return`.${x.styledComponentId}`}),a&&Ft(x,e,{attrs:!0,componentStyle:!0,displayName:!0,foldedComponentIds:!0,shouldForwardProp:!0,styledComponentId:!0,target:!0}),x}function Pn(e,t){for(var n=[e[0]],r=0,i=t.length;r<i;r+=1)n.push(t[r],e[r+1]);return n}var Fn=function(e){return Object.assign(e,{isCss:!0})};function In(e){var t=[...arguments].slice(1);if(It(e)||Bt(e))return Fn(Dn(Pn(at,b([e],t,!0))));var n=e;return t.length===0&&n.length===1&&typeof n[0]==`string`?Dn(n):Fn(Dn(Pn(n,t)))}function Ln(e,t,n){if(n===void 0&&(n=ot),!t)throw Ut(1,t);var r=function(r){var i=[...arguments].slice(1);return e(t,n,In.apply(void 0,b([r],i,!1)))};return r.attrs=function(r){return Ln(e,t,y(y({},n),{attrs:Array.prototype.concat(n.attrs,r).filter(Boolean)}))},r.withConfig=function(r){return Ln(e,t,y(y({},n),r))},r}var Rn=function(e){return Ln(Nn,e)},M=Rn;ct.forEach(function(e){M[e]=Rn(e)}),function(){function e(e,t){this.rules=e,this.componentId=t,this.isStatic=On(e),cn.registerId(this.componentId+1)}return e.prototype.createStyles=function(e,t,n,r){var i=r(zt(Dn(this.rules,t,n,r)),``),a=this.componentId+e;n.insertRules(a,a,i)},e.prototype.removeStyles=function(e,t){t.clearRules(this.componentId+e)},e.prototype.renderStyles=function(e,t,n,r){e>2&&cn.registerId(this.componentId+e);var i=this.componentId+e;this.isStatic?n.hasNameForId(i,i)||this.createStyles(e,t,n,r):(this.removeStyles(e,n),this.createStyles(e,t,n,r))},e}(),function(){function e(){var e=this;this._emitSheetCSS=function(){var t=e.instance.toString();if(!t)return``;var n=en();return`<style ${zt([n&&`nonce="${n}"`,`${Ze}="true"`,`${$e}="${et}"`].filter(Boolean),` `)}>${t}</style>`},this.getStyleTags=function(){if(e.sealed)throw Ut(2);return e._emitSheetCSS()},this.getStyleElement=function(){var t;if(e.sealed)throw Ut(2);var n=e.instance.toString();if(!n)return[];var r=((t={})[Ze]=``,t[$e]=et,t.dangerouslySetInnerHTML={__html:n},t),i=en();return i&&(r.nonce=i),[_.createElement(`style`,y({},r,{key:`sc-0-0`}))]},this.seal=function(){e.sealed=!0},this.instance=new cn({isServer:!0}),this.sealed=!1}return e.prototype.collectStyles=function(e){if(this.sealed)throw Ut(2);return _.createElement(bn,{sheet:this.instance},e)},e.prototype.interleaveWithNodeStream=function(e){throw Ut(3)},e}(),`${Ze}`;var N={colors:{windowBg:`#152029de`,panelBg:`#04161c`,panelBgGlass:`rgba(4, 22, 28, 0.22)`,line:`#496791`,linePrimary:`#6089c1`,text:`#ffffff`,textAccent:`#C6E2FF`,textDim:`#7f9bb8`,healthOk:`#427231`,healthCrit:`#ed6738`,warning:`#e1b000`,warningSoft:`#e6b400`,statusOk:`limegreen`,statusAlert:`#e1b000`,statusBad:`red`,statusPending:`#00b8e6`,enhText:`#d8be86`,enhTitle:`#e8cf93`,enhBg:`rgba(169, 128, 56, 0.30)`,enhLine:`#8a6d3b`,greenBg:`rgba(8, 28, 12, 0.92)`,greenLine:`#3f8a3f`,greenTitleBg:`#16401b`,greenText:`#e6ffe6`,greenLabel:`#bdf0bd`,greenBtnBg:`#1b5e20`,greenBtnLine:`#2e7d32`,greenBtnLineHover:`#66bb6a`,greenGlow:`#4caf50`,custom:`#cccc00`,chromeBg:`#0a3340`,chromeLine:`#587e8d`,chromeText:`#deebff`,overlayBg:`black`,overlayBgSoft:`rgba(0, 0, 0, 0.65)`},fonts:{body:`arial`,mono:`Consolas, "Lucida Console", monospace`,display:`"Orbitron", sans-serif`},radii:{modal:`0px`,tooltip:`7px`},hud:{btn:`36px`,btnSmall:`25px`,gap:`10px`,gapSmall:`5px`,icon:`26px`,iconSmall:`17px`,glyph:`30px`,glyphSmall:`20px`}},zn=M.div`
    border: 1px solid ${N.colors.line};
    color: ${N.colors.chromeText};
    background-color: ${N.colors.windowBg};
    font-family:${N.fonts.body};
`,Bn=M.div`
    width: 100%;
    height: 100%;
    position: absolute;
    right: 0;
    top: 0;
    z-index: 99999;
    background-color: rgba(0,0,0,0.5);
`,Vn=M(zn)`
    box-shadow: 5px 5px 10px ${N.colors.overlayBg};
`;M(M.span`
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
`)`
    font-weight: normal;
`;var Hn=In`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`,Un=o((e=>{var t=Symbol.for(`react.transitional.element`),n=Symbol.for(`react.fragment`);function r(e,n,r){var i=null;if(r!==void 0&&(i=``+r),n.key!==void 0&&(i=``+n.key),`key`in n)for(var a in r={},n)a!==`key`&&(r[a]=n[a]);else r=n;return n=r.ref,{$$typeof:t,type:e,key:i,ref:n===void 0?null:n,props:r}}e.Fragment=n,e.jsx=r,e.jsxs=r})),P=o(((e,t)=>{t.exports=Un()}))(),Wn=class extends _.Component{render(){return(0,P.jsxs)(Gn,{children:[(0,P.jsx)(Kn,{children:this.props.label}),(0,P.jsx)(qn,{type:this.props.type||`text`,value:this.props.value,placeholder:this.props.placeholder,onKeyDown:this.props.onKeydown,onChange:this.props.onChange,tabIndex:`0`})]})}},Gn=M.div`
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
`,Kn=M.span`
    flex: 1;
    min-width: 0; /* allow the label to shrink/wrap instead of forcing the row
                     wider than the panel */
    font-size: 13px;
    line-height: 1.3;
    color: #b8cfe6;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
    }
`,qn=M.input`
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
`,Jn=M(class extends _.Component{render(){let{children:e,className:t}=this.props;return(0,P.jsx)(`div`,{className:t,children:e})}})`
    z-index:7001;
    position:absolute;
    text-align:center;
    font-family:${N.fonts.body};
    font-size:12px;
    color:${N.colors.text};
    background-color:${N.colors.overlayBgSoft};
    border-radius: ${N.radii.tooltip};
    -moz-border-radius: ${N.radii.tooltip};
    -webkit-border-radius: ${N.radii.tooltip};
    padding:3px 3px 3px 3px;
    padding-bottom: 8px;
`,Yn=M.div`
    text-transform: uppercase;
    font-size: 16px;
    border-bottom: 1px solid white;
    width: 100%;
    margin: 5px 0;
    font-weight: bold;
`,Xn=M.div`
    color: ${e=>e.$type==`good`?`#6fc126;`:e.$type==`bad`?`#ff7b3f;`:`white;`}
    font-weight: ${e=>e.$important?`bold`:`inherit`};
    font-size: ${e=>e.$important?`14px`:`12px`};
    margin-top: ${e=>e.$space?`14px`:`0`};
`,Zn=class extends _.Component{getOnChange(e){return t=>{this.props.set(e,t.target.value),this.props.save(),this.forceUpdate()}}getOnKeyDown(e){return t=>{if(console.log(`keydown`),t.preventDefault(),t.stopPropagation(),!cr[t.keyCode])return;let n={keyCode:t.keyCode,shiftKey:t.shiftKey,altKey:t.altKey,ctrlKey:t.ctrlKey,metaKey:t.metaKey};this.props.set(e,n),this.props.save(),this.forceUpdate()}}getKey(e){return console.log(this.props.settings),Qn(this.props.settings[e])}get(e){return this.props.settings[e]}render(){return(0,P.jsx)($n,{onClick:this.props.close,children:(0,P.jsxs)(er,{onClick:e=>e.stopPropagation(),children:[(0,P.jsxs)(tr,{children:[(0,P.jsx)(nr,{children:`Player Settings`}),(0,P.jsx)(rr,{onClick:this.props.close,title:`Close`,children:`✕`})]}),(0,P.jsxs)(ir,{children:[(0,P.jsx)(ar,{children:`Settings apply to this browser and device only. Reload the page for changes to take effect.`}),(0,P.jsx)(or,{children:`Keys`}),(0,P.jsx)(Wn,{label:`Display ALL Electronic Warfare (EW)`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ShowAllEW`),value:this.getKey.call(this,`ShowAllEW`)}),(0,P.jsx)(Wn,{label:`Display FRIENDLY Electronic Warfare (EW)`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ShowFriendlyEW`),value:this.getKey.call(this,`ShowFriendlyEW`)}),(0,P.jsx)(Wn,{label:`Display ENEMY Electronic Warfare (EW)`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ShowEnemyEW`),value:this.getKey.call(this,`ShowEnemyEW`)}),(0,P.jsx)(Wn,{label:`Display ALL Ballistics`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ShowAllBallistics`),value:this.getKey.call(this,`ShowAllBallistics`)}),(0,P.jsx)(Wn,{label:`Display FRIENDLY Ballistics`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ShowFriendlyBallistics`),value:this.getKey.call(this,`ShowFriendlyBallistics`)}),(0,P.jsx)(Wn,{label:`Display ENEMY Ballistics`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ShowEnemyBallistics`),value:this.getKey.call(this,`ShowEnemyBallistics`)}),(0,P.jsx)(Wn,{label:`Toggle RULER tool`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ToggleLoS`),value:this.getKey.call(this,`ToggleLoS`)}),(0,P.jsx)(Wn,{label:`Toggle HEX numbers`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ToggleHexNumbers`),value:this.getKey.call(this,`ToggleHexNumbers`)}),(0,P.jsx)(Wn,{label:`Toggle MAP background`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ToggleBackground`),value:this.getKey.call(this,`ToggleBackground`)}),(0,P.jsx)(Wn,{label:`Close all SHIP WINDOWS and tooltips`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`CloseAllWindows`),value:this.getKey.call(this,`CloseAllWindows`)}),(0,P.jsx)(or,{children:`Replay`}),(0,P.jsx)(Wn,{label:`Play / pause Replay`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`TogglePlayPause`),value:this.getKey.call(this,`TogglePlayPause`)}),(0,P.jsx)(or,{children:`Sound`}),(0,P.jsx)(Wn,{label:`Toggle sound in Replay`,onChange:()=>{},onKeydown:this.getOnKeyDown.call(this,`ToggleSound`),value:this.getKey.call(this,`ToggleSound`)}),(0,P.jsx)(or,{children:`Visual`}),(0,P.jsx)(Wn,{placeholder:`0`,type:`number`,label:`Zoom level to switch to strategic view`,onChange:this.getOnChange.call(this,`ZoomLevelToStrategic`),value:this.get.call(this,`ZoomLevelToStrategic`)}),(0,P.jsx)(sr,{children:`Fiery Void is an unofficial fan-made game inspired by Babylon 5 Wars. It is not endorsed by or affiliated with any official rights holders. All trademarks remain the property of their respective owners.`})]})]})})}},Qn=e=>{let t=cr[e.keyCode];return t=t.toUpperCase(),e.shiftKey&&(t+=` + shift`),e.altKey&&(t+=` + alt`),e.ctrlKey&&(t+=` + ctrl`),e.metaKey&&(t+=` + cmd`),t},$n=M(Bn)`
    /* Pin to the viewport (not the #playerSettings mount box) so the centred
       Panel is always screen-centred regardless of where the root sits. */
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
`,er=M.div`
    display: flex;
    flex-direction: column;
    width: 520px;
    max-width: calc(100% - 24px);
    max-height: 88vh;
    background-color: ${N.colors.panelBg};
    border: 1px solid ${N.colors.line};
    border-radius: ${N.radii.modal};
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.65);
    color: ${N.colors.chromeText};
    font-family: ${N.fonts.body};
    overflow: hidden;

    /* Portrait phones OR short landscape phones (wider than 765px). */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        max-height: 94vh;
        max-width: calc(100% - 12px);
    }
`,tr=M.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    padding: 10px 8px 10px 16px;
    background-color: ${N.colors.windowBg};
    border-bottom: 1px solid ${N.colors.line};
`,nr=M.span`
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #deebff;
    text-shadow: black 0 0 10px, black 0 0 3px;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 13px;
    }
`,rr=M.div`
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
    ${Hn}

    &:hover {
        color: #d6f7fd;
        border-color: #49c4d4;
        background-color: rgba(73, 196, 212, 0.12);
    }
`,ir=M.div`
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
`,ar=M.p`
    margin: 10px 14px 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #6689ba;

    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        font-size: 11px;
        margin: 8px 10px 2px;
    }
`,or=M.div`
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
`,sr=M.p`
    margin: 18px 14px 4px;
    padding-top: 12px;
    border-top: 1px solid rgba(88, 126, 141, 0.2);
    font-size: 10px;
    line-height: 1.4;
    text-align: center;
    color: #567;
    opacity: 0.85;
`,cr={27:`esc`,32:`space`,48:`0`,49:`1`,50:`2`,51:`3`,52:`4`,53:`5`,54:`6`,55:`7`,56:`8`,57:`9`,58:`:`,65:`a`,66:`b`,67:`c`,68:`d`,69:`e`,70:`f`,71:`g`,72:`h`,73:`i`,74:`j`,75:`k`,76:`l`,77:`m`,78:`n`,79:`o`,80:`p`,81:`q`,82:`r`,83:`s`,84:`t`,85:`u`,86:`v`,87:`w`,88:`x`,89:`y`,90:`z`},lr=class extends _.Component{constructor(e){super(e),this.state={open:!1}}open(){this.setState({open:!0})}close(){this.setState({open:!1})}render(){return this.state.open?(0,P.jsx)(Zn,{close:this.close.bind(this),...this.props}):(0,P.jsx)(ur,{onClick:this.open.bind(this),children:`⚙`})}},ur=M(Vn)`
    width: ${N.hud.btn};
    height: ${N.hud.btn};
    position: fixed;
    right: 0;
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: ${N.hud.glyph};
    border-right: none;
    border-top: none;
    ${Hn}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${N.hud.btnSmall};
        height: ${N.hud.btnSmall};
        font-size: ${N.hud.glyphSmall};

    }
`,dr=`fv-thrust-relayout`,fr=8,pr=6,mr=40,hr=60,gr=80,_r=4,vr=(e,t,n)=>{let r=window.coordinateConverter&&window.coordinateConverter.zoom||1,i=Math.min((e.canvasSize||200)*.75,250)/2/r+_r,a=Math.min(i,120),o=e=>n.type===`roll`||!Array.isArray(t)||t[e]!==null,s=t=>o(t)?shipManager.systems.getThrusters(e,t).length:0,c=Math.max(s(1),s(2))*mr/2;return{x:Math.min(i,hr),y:Math.max(Math.min(i,gr),c),clear:a}},yr=M.span`
    font-family: ${N.fonts.mono};
    font-size: 11px;
    line-height: 1;
    padding: 1px 2px;
    color: ${e=>e.$over?N.colors.healthCrit:N.colors.text};
    background-color: rgba(0, 0, 0, 0.55);
    pointer-events: none;
`,br=M.div`
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    position: relative; // Needed for absolute positioning of ::before
    box-sizing: border-box;
    ${e=>e.$destroyed?`box-shadow: inset 0 0 0 1px ${N.colors.statusBad};`:e.$box===`need`?`box-shadow: inset 0 0 0 1px ${N.colors.warning};`:e.$box===`extra`?`box-shadow: inset 0 0 0 1px ${N.colors.greenBtnLineHover};`:``}

    &::before {
        content: "";
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: -1;
        /* thrusterGreen*.png: the ship window's blue thruster1*.png with only the blue body repainted
           in the panel's green (theme.colors.greenLine, shading kept) - the red critical bands are
           untouched, which a CSS hue-rotate could not do (it turned them purple). */
        background-image: ${e=>{switch(e.$crits){case 11:return`url(img/systemicons/thrusterGreen-critical12.png);`;case 10:return`url(img/systemicons/thrusterGreen-critical1.png);`;case 1:return`url(img/systemicons/thrusterGreen-critical2.png);`;default:return`url(img/systemicons/thrusterGreen.png);`}}}
        background-size: cover;
        transform: ${e=>{switch(e.$direction){case 4:return`rotate(180deg)`;case 1:return`rotate(90deg)`;case 2:return`rotate(270deg)`;default:return`none`}}};
        ${e=>e.$destroyed?`opacity: 0.3;`:``}
    }

    ${e=>e.$destroyed?`cursor: default;`:Hn}
`,xr=M.div`
    display: flex;
    position: absolute;
`,Sr=M(xr)`
    flex-direction: row;
    left: var(--fv-ring-x, ${hr}px);
    transform: translate(0, -50%);
    flex-wrap: wrap;
    max-width: ${mr}px;
`,Cr=M(Sr)`
    left: calc(-1 * var(--fv-ring-x, ${hr}px) - ${mr}px);
`,wr=M(xr)`
    flex-direction: row;
    top: calc(-1 * var(--fv-ring-y, ${gr}px) - ${mr}px);
    transform: translate(-50%, 0);
`,Tr=M(wr)`
    top: var(--fv-ring-y, ${gr}px);
`,Er=M.div`
    position: relative;
    transform: rotate(${e=>e.$rotation}deg);

    & ${yr} {
        transform: rotate(${e=>-e.$rotation}deg);
    }
`,Dr=M.div`
    position: absolute;
    transform: translate(-50%, -50%);
    z-index: 7002;
    display: flex;
    align-items: center;
    justify-content: center;
`,Or=M.div`
    position: absolute;
    left: 0;
    top: 0;
    z-index: 1;
    width: 180px;
    box-sizing: border-box;
    background-color: ${N.colors.greenBg};
    border: 1px solid ${N.colors.greenLine};
    box-shadow: 5px 5px 10px ${N.colors.overlayBg};
    font-family: ${N.fonts.body};
    color: ${N.colors.greenText};
    text-align: left;
    user-select: none;
`,kr=M.div`
    box-sizing: border-box;
    padding: 3px 6px;
    line-height: 1.2;
    font-size: 11px;
    font-weight: bold;
    text-align: center;
    white-space: pre;
    overflow: hidden;
    text-overflow: ellipsis;
    color: ${N.colors.greenText};
    background-color: ${N.colors.greenTitleBg};
    border-bottom: 1px solid ${N.colors.greenLine};
`,Ar=M.div`
    padding: 3px 6px 4px;
`,jr=M.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 6px;
    padding: 1px 0;
`,Mr=M.span`
    font-size: 10px;
    color: ${N.colors.greenLabel};
    white-space: nowrap;
`,Nr=()=>window.UI&&window.UI.shipMovement&&window.UI.shipMovement.extendedTurnColour||`#ff8c00`,Pr=M.span`
    font-family: ${N.fonts.mono};
    font-size: 11px;
    white-space: nowrap;
    color: ${e=>e.$state===`ok`?N.colors.statusOk:e.$state===`open`?N.colors.warning:e.$state===`owed`?Nr():N.colors.greenText};
`,Fr=M.div`
    padding: 2px 0 1px;
    font-size: 10px;
    line-height: 1.25;
    color: ${N.colors.greenLabel};
`,Ir=M.div`
    position: relative;
    height: 4px;
    margin: 2px 0 3px;
    background-color: rgba(0, 0, 0, 0.45);
    border: 1px solid ${N.colors.greenLine};
`,Lr=M.div`
    position: absolute;
    top: 0;
    bottom: 0;
    background-color: ${e=>e.$owed?Nr():N.colors.statusOk};
`,Rr=M.div`
    position: absolute;
    top: -3px;
    bottom: -3px;
    width: 1px;
    background-color: ${N.colors.greenText};
`,zr=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 3px;
    min-height: 16px;
    font-size: 10px;
    color: ${N.colors.greenText};
    cursor: ${e=>e.$locked?`not-allowed`:`pointer`};

    @media (pointer: coarse) {
        min-height: 32px;
    }
`,Br=M.span`
    position: relative;
    flex: 0 0 auto;
    width: 10px;
    height: 10px;
    box-sizing: border-box;
    border: 1px solid ${e=>e.$locked?N.colors.greenLine:N.colors.greenBtnLineHover};
    background-color: ${e=>e.$checked?N.colors.greenBtnLine:`rgba(0, 0, 0, 0.45)`};

    ${e=>e.$checked&&`
    &::after {
        content: "";
        position: absolute;
        left: 2.5px;
        top: 0;
        width: 3px;
        height: 6px;
        border-right: 1.5px solid ${N.colors.greenText};
        border-bottom: 1.5px solid ${N.colors.greenText};
        transform: rotate(45deg);
    }`}
`,Vr=M.div`
    height: 1px;
    margin: 3px 0;
    background-color: ${N.colors.greenLine};
`,Hr=M.div`
    display: flex;
    gap: 4px;
    padding: 0 6px 6px;
`,Ur=M.div`
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
    background: ${N.colors.greenBtnBg};
    border: 1px solid ${N.colors.greenBtnLine};
    color: ${N.colors.greenText};

    &:hover {
        background: ${N.colors.greenBtnLine};
        border: 1px solid ${N.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${e=>e.$primary&&!e.disabled&&`
        background: ${N.colors.greenBtnLine};
        border: 1px solid ${N.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${N.colors.greenGlow};
        color: #ffffff;
    `}

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${N.colors.greenBtnBg}; border: 1px solid ${N.colors.greenBtnLine}; color: ${N.colors.greenText}; }
    `}

    @media (pointer: coarse) {
        height: 32px;
    }
`,Wr=[`Either`,`Front`,`Aft`,`Port`,`Stbd`],Gr=[1,2,3,4,0],Kr=e=>e?`Starboard`:`Port`,qr=e=>shipManager.movement.isExtendedTurnStart(e)?`begin`:shipManager.movement.isExtendedTurnCompletion(e)?`complete`:`normal`,Jr=(e,t)=>{switch(t.type){case`extendTurnLeft`:case`extendTurnRight`:return`Begin Extended Turn
`+Kr(t.type===`extendTurnRight`);case`turnleft`:case`turnright`:return t.value===`turnIntoPivot`?`Turn into Pivot · `+Kr(t.type===`turnright`):t.value===`extendedTurn`?`Complete Extended Turn
`+Kr(t.type===`turnright`):`Turn `+Kr(t.type===`turnright`);case`pivotleft`:case`pivotright`:return`Pivot `+Kr(t.type===`pivotright`);case`slipleft`:case`slipright`:return`Slip `+Kr(t.type===`slipright`);case`roll`:return t.value===`emergencyRoll`?`Emergency Roll`:`Roll`;case`speedchange`:{let n=e.movement.lastIndexOf(t),r=n>0?e.movement[n-1]:null;return r&&(t.speed<r.speed||t.heading!=r.heading)?`Decelerate`:`Accelerate`}case`jink`:return`Jink`;case`halfPhase`:return`Half-Phase`;case`contract`:return`Contraction`;default:return`Assign Thrust`}},Yr=(e,t)=>{if(!Array.isArray(e)||!Array.isArray(t))return{rows:[],extra:0};let n=[1,2,3,4].every(t=>!(e[t]>0)),r=[];return Gr.forEach(i=>{let a=e[i];if(!(a>0)||i>0&&t[i]===null)return;let o=Math.max(0,t[i]||0);r.push({slot:i,label:i===0?n?`Any`:`Either`:Wr[i],paid:a-o,required:a,open:o})}),{rows:r,extra:t[0]<0?-t[0]:0}},Xr=e=>Array.isArray(e)&&!e.some(e=>e>0),Zr=(e,t,n)=>{let r={left:t.left-n,top:t.top-n,right:t.left+n,bottom:t.top+n};return Array.from(e.children).forEach(e=>{let t=e.getBoundingClientRect();t.width!==0&&t.height!==0&&(r.left=Math.min(r.left,t.left),r.top=Math.min(r.top,t.top),r.right=Math.max(r.right,t.right),r.bottom=Math.max(r.bottom,t.bottom))}),r},Qr=class extends _.Component{constructor(e){super(e),this.containerRef=_.createRef(),this.ringRef=_.createRef(),this.panelRef=_.createRef(),this.layout=this.layout.bind(this)}componentDidMount(){this.layout(),window.addEventListener(`resize`,this.layout),this.container=this.containerRef.current,this.container&&this.container.addEventListener(dr,this.layout)}componentDidUpdate(){this.layout()}componentWillUnmount(){window.removeEventListener(`resize`,this.layout),this.container&&this.container.removeEventListener(dr,this.layout)}layout(){let e=this.containerRef.current,t=this.ringRef.current,n=this.panelRef.current;if(!e||!t||!n)return;let{ship:r,totalRequired:i,movement:a}=this.props,o=vr(r,i,a);t.style.setProperty(`--fv-ring-x`,o.x+`px`),t.style.setProperty(`--fv-ring-y`,o.y+`px`),this.placePanel(e,t,n,o.clear)}placePanel(e,t,n,r){let i=e.getBoundingClientRect(),a=Zr(t,i,r),o=window.innerWidth,s=window.innerHeight,c=n.offsetWidth,l=n.offsetHeight,u=e=>Math.min(Math.max(e,fr),o-c-fr),d=e=>Math.min(Math.max(e,fr),s-l-fr),f={left:u(i.left-c/2),top:a.bottom+pr},p=[f,{left:u(i.left-c/2),top:a.top-pr-l},{left:a.right+pr,top:d(i.top-l/2)},{left:a.left-pr-c,top:d(i.top-l/2)}].find(e=>e.left>=fr&&e.top>=fr&&e.left+c<=o-fr&&e.top+l<=s-fr)||{left:f.left,top:d(f.top)};n.style.left=Math.round(p.left-i.left)+`px`,n.style.top=Math.round(p.top-i.top)+`px`}ready(){window.shipManager.movement.doneAssignThrust(this.props.ship)}cancel(){window.shipManager.movement.cancelAssignThrustEvent(this.props.ship)}toggleExtendedTurn(e){e.enabled&&window.shipManager.movement.setExtendedTurn(this.props.ship,!e.checked)}render(){let{ship:e,position:t,rotation:n,totalRequired:r,remainginRequired:i,movement:a}=this.props,o=Math.round(Math.abs(n)),{rows:s,extra:c}=Yr(r,i),l=qr(a),u=shipManager.movement.getExtendedTurnToggle(e,a),d,f,p=null;l===`begin`?(p=shipManager.movement.getExtendedTurnStartPayment(e,a),d=p.valid,f=p.paid<p.minimum?`Pay at least ${p.minimum} now (${p.paid} so far)`:`Leave at least 1 thrust owed for next turn`):(d=Xr(i),f=`Still needed: `+s.filter(e=>e.open>0).map(e=>`${e.label} ${e.open}`).join(` · `));let m=e=>e.open===0?`ok`:l===`begin`?`owed`:`open`;return(0,P.jsxs)(Dr,{ref:this.containerRef,onMouseOver:e=>e.preventDefault(),onContextMenu:e=>e.preventDefault(),id:`thrustUIContainer`,style:{left:`${t.x}px`,top:`${t.y}px`},children:[(0,P.jsxs)(Er,{ref:this.ringRef,style:{transform:`rotate(${o}deg)`},$rotation:o,children:[(0,P.jsx)(Sr,{children:ti(e,1,r,i,a)}),(0,P.jsx)(wr,{children:ti(e,3,r,i,a)}),(0,P.jsx)(Tr,{children:ti(e,4,r,i,a)}),(0,P.jsx)(Cr,{children:ti(e,2,r,i,a)})]}),(0,P.jsxs)(Or,{ref:this.panelRef,children:[(0,P.jsx)(kr,{children:Jr(e,a)}),(0,P.jsxs)(Ar,{children:[s.map(e=>(0,P.jsxs)(jr,{children:[(0,P.jsx)(Mr,{children:e.label}),(0,P.jsxs)(Pr,{$state:m(e),children:[e.paid,`/`,e.required]})]},`thrust-row-${e.slot}`)),c>0&&(0,P.jsxs)(jr,{children:[(0,P.jsx)(Mr,{children:`Extra thrust`}),(0,P.jsxs)(Pr,{$state:`ok`,children:[`+`,c]})]}),(0,P.jsx)(Vr,{}),p&&ei(p),(0,P.jsxs)(jr,{children:[(0,P.jsx)(Mr,{children:`Engine thrust`}),(0,P.jsx)(Pr,{children:shipManager.movement.getRemainingEngineThrust(e)})]}),$r(e,a),l===`begin`&&(0,P.jsx)(Fr,{children:`Completes on next turn's movement`}),l===`complete`&&(0,P.jsx)(Fr,{children:`Begun last turn`}),u&&(0,P.jsxs)(P.Fragment,{children:[(0,P.jsxs)(zr,{$locked:!u.enabled,title:u.enabled?u.checked?`Make a normal turn instead`:`Pay part of this turn now and the rest next turn`:u.hint,onClick:()=>this.toggleExtendedTurn(u),children:[(0,P.jsx)(Br,{$checked:u.checked,$locked:!u.enabled}),`Make Extended Turn`]}),!u.enabled&&u.hint&&(0,P.jsx)(Fr,{children:u.hint})]})]}),(0,P.jsxs)(Hr,{children:[(0,P.jsx)(Ur,{$primary:!0,disabled:!d,title:d?`Confirm this manoeuvre`:f,onClick:d?this.ready.bind(this):void 0,children:`Confirm`}),(0,P.jsx)(Ur,{onClick:this.cancel.bind(this),children:`Cancel`})]})]})]})}},$r=(e,t)=>{if(!shipManager.movement.isTurn(t))return null;let n=shipManager.movement.calculateTurndelay(e,t,t.speed);return(0,P.jsxs)(jr,{children:[(0,P.jsx)(Mr,{children:`Turn delay`}),(0,P.jsx)(Pr,{children:n})]})},ei=e=>{let t=e.paid>=e.minimum,n=e.owed.any+e.owed.rear+e.owed.side,r=t=>(e.cost>0?Math.min(100,Math.max(0,t/e.cost*100)):0)+`%`;return(0,P.jsxs)(P.Fragment,{children:[(0,P.jsxs)(jr,{children:[(0,P.jsx)(Mr,{children:`Turn cost`}),(0,P.jsx)(Pr,{children:e.cost})]}),(0,P.jsxs)(jr,{children:[(0,P.jsxs)(Mr,{children:[`Paying now (min `,e.minimum,`)`]}),(0,P.jsx)(Pr,{$state:t?`ok`:`open`,children:e.paid})]}),(0,P.jsxs)(Ir,{title:`At least ${e.minimum} now, at most ${e.cost-1}`,children:[(0,P.jsx)(Lr,{style:{left:0,width:r(e.paid)}}),(0,P.jsx)(Lr,{$owed:!0,style:{left:r(e.paid),width:r(e.cost-e.paid)}}),(0,P.jsx)(Rr,{style:{left:r(e.minimum)}})]}),(0,P.jsxs)(jr,{children:[(0,P.jsx)(Mr,{children:`Thrust owed next turn`}),(0,P.jsx)(Pr,{$state:`owed`,children:n})]})]})},ti=(e,t,n,r,i)=>{let a=shipManager.systems.getThrusters(e,t);if(i.type!==`roll`&&n[t]===null)return null;let o=Array.isArray(r)&&(r[t]>0||r[0]>0);return a.map((n,r)=>{let i=shipManager.systems.isDestroyed(e,n),a=i||!shipManager.movement.wouldAcceptThrust(e,n)?void 0:o?`need`:`extra`,s=()=>{shipManager.movement.assignThrust(e,n),shipManager.movement.updateAssignThrust(e)},c=t=>{t.preventDefault(),shipManager.movement.unAssignThrust(e,n),shipManager.movement.updateAssignThrust(e)},l=shipManager.criticals.hasCritical(n,`HalfEfficiency`)?10:0;shipManager.criticals.hasCritical(n,`FirstThrustIgnored`)&&(l+=1);let u=shipManager.movement.getAmountChanneled(e,n),d=shipManager.systems.getOutput(e,n);return(0,P.jsx)(br,{$crits:l,$direction:t,$destroyed:i,$box:a,onClick:i?void 0:s,onContextMenu:i?e=>e.preventDefault():c,children:(0,P.jsxs)(yr,{$over:u>d,children:[u,`/`,d]})},`thruster-${t}-${r}`)})},ni=()=>(0,P.jsxs)(`svg`,{viewBox:`0 0 24 24`,fill:`none`,stroke:`currentColor`,strokeWidth:`2`,strokeLinecap:`round`,strokeLinejoin:`round`,"aria-hidden":`true`,focusable:`false`,children:[(0,P.jsx)(`path`,{d:`M9 3H3v6`}),(0,P.jsx)(`path`,{d:`M15 3h6v6`}),(0,P.jsx)(`path`,{d:`M9 21H3v-6`}),(0,P.jsx)(`path`,{d:`M15 21h6v-6`})]}),ri=class extends _.Component{fullScreen(){var e=window.document,t=e.documentElement,n=t.requestFullscreen||t.mozRequestFullScreen||t.webkitRequestFullScreen||t.msRequestFullscreen,r=e.exitFullscreen||e.mozCancelFullScreen||e.webkitExitFullscreen||e.msExitFullscreen;!e.fullscreenElement&&!e.mozFullScreenElement&&!e.webkitFullscreenElement&&!e.msFullscreenElement?n.call(t):r.call(e)}render(){return(0,P.jsx)(ii,{onClick:this.fullScreen.bind(this),title:`Full screen`,children:(0,P.jsx)(ni,{})})}},ii=M(Vn)`
    width: ${N.hud.btn};
    height: ${N.hud.btn};
    position: fixed;
    right: calc((${N.hud.btn} + ${N.hud.gap}) * 2);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Hn}

    svg {
        width: ${N.hud.icon};
        height: ${N.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${N.hud.btnSmall};
        height: ${N.hud.btnSmall};
        right: calc((${N.hud.btnSmall} + ${N.hud.gapSmall}) * 2);

        svg {
            width: ${N.hud.iconSmall};
            height: ${N.hud.iconSmall};
        }
    }
`,ai=class extends _.Component{constructor(e){super(e),this.state={available:oi()},this.surrender=this.surrender.bind(this)}componentDidMount(){this.availabilityCheck=setInterval(()=>{let e=oi();this.state.available!==e&&this.setState({available:e})},500)}componentWillUnmount(){clearInterval(this.availabilityCheck)}surrender(){window.gamedata.onSurrenderClicked()}render(){return this.state.available?(0,P.jsx)(ui,{onClick:this.surrender,title:`Surrender`,children:(0,P.jsx)(`img`,{src:si(),alt:``})}):null}},oi=()=>{if(window.gamedata===void 0)return!1;let e=window.gamedata;if(e.replay||!e.isPlayerInGame()||e.status===`SURRENDERED`||e.status===`FINISHED`)return!1;for(let t in e.slots){let n=e.slots[t];if(n.playerid==e.thisplayer&&n.surrendered!==null&&n.surrendered!==void 0)return!1}return!0},si=()=>{let e=`./img/surrender_icon1.png`;return window.AssetManager?window.AssetManager.getSmartImagePath(e):e},ci=`34px`,li=`22px`,ui=M(Vn)`
    width: ${N.hud.btn};
    height: ${N.hud.btn};
    position: fixed;
    right: calc(${N.hud.btn} + ${N.hud.gap});
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Hn}

    img {
        width: ${ci};
        height: ${ci};
        display: block;
        transform: translateY(${`-4px`});
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
        width: ${N.hud.btnSmall};
        height: ${N.hud.btnSmall};
        right: calc(${N.hud.btnSmall} + ${N.hud.gapSmall});

        img {
            width: ${li};
            height: ${li};
            transform: translateY(${`-2px`});
        }
    }
`,di=()=>(0,P.jsxs)(`svg`,{viewBox:`0 0 24 24`,fill:`none`,stroke:`currentColor`,strokeWidth:`2`,strokeLinecap:`round`,strokeLinejoin:`round`,"aria-hidden":`true`,focusable:`false`,children:[(0,P.jsx)(`path`,{d:`M3 10.5L12 3l9 7.5`}),(0,P.jsx)(`path`,{d:`M5 9v12h14V9`}),(0,P.jsx)(`path`,{d:`M10 21v-6h4v6`})]}),fi=class extends _.Component{render(){return(0,P.jsx)(pi,{as:`a`,href:`games.php`,title:`Back to Lobby`,children:(0,P.jsx)(di,{})})}},pi=M(Vn)`
    width: ${N.hud.btn};
    height: ${N.hud.btn};
    position: fixed;
    right: calc((${N.hud.btn} + ${N.hud.gap}) * 3);
    top: 0;
    z-index: 4;
    display: flex;
    align-items: center;
    justify-content: center;
    border-top: none;
    ${Hn}

    svg {
        width: ${N.hud.icon};
        height: ${N.hud.icon};
        display: block;
    }

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${N.hud.btnSmall};
        height: ${N.hud.btnSmall};
        right: calc((${N.hud.btnSmall} + ${N.hud.gapSmall}) * 3);

        svg {
            width: ${N.hud.iconSmall};
            height: ${N.hud.iconSmall};
        }
    }
`,mi=class extends _.Component{constructor(e){super(e),this.state={losToggled:!1,hexToggled:!1,soundToggled:gamedata.playAudio!==!1,bgToggled:!1,ebToggled:!1,fbToggled:!1,originalBgImage:null,replayMode:this.getReplayMode()},this.showFriendlyEW=this.showFriendlyEW.bind(this),this.showEnemyEW=this.showEnemyEW.bind(this),this.toggleFriendlyBallisticLines=this.toggleFriendlyBallisticLines.bind(this),this.toggleEnemyBallisticLines=this.toggleEnemyBallisticLines.bind(this),this.toggleLoS=this.toggleLoS.bind(this),this.externalToggleLoS=this.externalToggleLoS.bind(this),this.toggleHexNumbers=this.toggleHexNumbers.bind(this),this.externalToggleHexNumbers=this.externalToggleHexNumbers.bind(this),this.toggleSound=this.toggleSound.bind(this),this.externalToggleSound=this.externalToggleSound.bind(this),this.toggleBackground=this.toggleBackground.bind(this),this.externalToggleBackground=this.externalToggleBackground.bind(this),this.setBackground=this.setBackground.bind(this),this.externalSetBackground=this.externalSetBackground.bind(this)}getReplayMode(){return gamedata.replay||!gamedata.isPlayerInGame()}componentDidMount(){window.addEventListener(`LoSToggled`,this.externalToggleLoS),window.addEventListener(`HexNumbersToggled`,this.externalToggleHexNumbers),window.addEventListener(`BackgroundToggled`,this.externalToggleBackground),window.addEventListener(`BackgroundPreferenceChanged`,this.externalSetBackground),window.addEventListener(`soundToggled`,this.externalToggleSound),window.gameOptions&&gameOptions.get(`hideMapBackground`)&&this.setBackground(!0),this.replayCheck=setInterval(()=>{let e=this.getReplayMode();this.state.replayMode!==e&&this.setState({replayMode:e})},500)}componentWillUnmount(){window.removeEventListener(`LoSToggled`,this.externalToggleLoS),window.removeEventListener(`HexNumbersToggled`,this.externalToggleHexNumbers),window.removeEventListener(`BackgroundToggled`,this.externalToggleBackground),window.removeEventListener(`BackgroundPreferenceChanged`,this.externalSetBackground),window.removeEventListener(`soundToggled`,this.externalToggleSound),clearInterval(this.replayCheck)}externalToggleLoS(){this.setState({losToggled:gamedata.showLoS})}externalToggleHexNumbers(){this.setState(e=>({hexToggled:!e.hexToggled}))}externalToggleSound(){this.setState({soundToggled:gamedata.playAudio})}externalToggleBackground(){this.toggleBackground()}externalSetBackground(e){this.setBackground(!!(e.detail&&e.detail.off))}showFriendlyEW(e){webglScene.customEvent(`ShowFriendlyEW`,{up:e})}showEnemyEW(e){webglScene.customEvent(`ShowEnemyEW`,{up:e})}toggleFriendlyBallisticLines(e){if(e)return;let t=!this.state.fbToggled;this.setState({fbToggled:t}),webglScene.customEvent(`ToggleFriendlyBallisticLines`,{up:e})}toggleEnemyBallisticLines(e){if(e)return;let t=!this.state.ebToggled;this.setState({ebToggled:t}),webglScene.customEvent(`ToggleEnemyBallisticLines`,{up:e})}toggleLoS(e){if(e)return;let t=!this.state.losToggled;this.setState({losToggled:t}),webglScene.customEvent(`ToggleLoS`,{up:e}),window.dispatchEvent(new CustomEvent(`LoSToggled`))}toggleHexNumbers(e){if(e)return;let t=!this.state.hexToggled;this.setState({hexToggled:t}),webglScene.customEvent(`ToggleHexNumbers`,{up:e}),window.dispatchEvent(new CustomEvent(`HexNumbersToggled`))}toggleSound(){let e=!this.state.soundToggled;this.setState({soundToggled:e}),webglScene.customEvent(`ToggleSound`,{enabled:e})}toggleBackground(){this.setBackground(!this.state.bgToggled)}setBackground(e){let t=document.getElementById(`background`);if(!t||e===this.state.bgToggled)return;let n=this.state.originalBgImage;e?(n||=t.style.backgroundImage,t.style.backgroundImage=`none`,t.style.backgroundColor=`black`):(t.style.backgroundImage=n||``,t.style.backgroundColor=``),this.setState({bgToggled:e,originalBgImage:n}),window.webglScene&&typeof webglScene.customEvent==`function`&&webglScene.customEvent(`MapBackgroundSet`,{off:e})}render(){return(0,P.jsxs)(hi,{children:[(0,P.jsx)(vi,{onMouseDown:this.showFriendlyEW.bind(this,!1),onMouseUp:this.showFriendlyEW.bind(this,!0),onTouchStart:this.showFriendlyEW.bind(this,!1),onTouchEnd:this.showFriendlyEW.bind(this,!0)}),(0,P.jsx)(_i,{onMouseDown:this.showEnemyEW.bind(this,!1),onMouseUp:this.showEnemyEW.bind(this,!0),onTouchStart:this.showEnemyEW.bind(this,!1),onTouchEnd:this.showEnemyEW.bind(this,!0)}),(0,P.jsx)(wi,{$showing:this.state.fbToggled,onMouseDown:this.toggleFriendlyBallisticLines.bind(this,!1)}),(0,P.jsx)(Ci,{$showing:this.state.ebToggled,onMouseDown:this.toggleEnemyBallisticLines.bind(this,!1)}),(0,P.jsx)(Ti,{$showing:this.state.losToggled,onMouseDown:this.toggleLoS.bind(this,!1)}),(0,P.jsx)(Ei,{$showing:this.state.hexToggled,onMouseDown:this.toggleHexNumbers.bind(this,!1)}),(0,P.jsx)(Di,{$showing:!this.state.bgToggled,onMouseDown:this.toggleBackground,title:this.state.bgToggled?`Enable Background`:`Disable Background`}),this.state.replayMode&&(0,P.jsx)(Oi,{$toggled:this.state.soundToggled,onMouseDown:this.toggleSound,title:this.state.soundToggled?`Sound On`:`Sound Off`})]})}},hi=M.div`
    position: fixed;
    right: 0;
    top: 55px;
    z-index: 4;

    /* Narrow phones (portrait) OR short landscape phones: nudge up.
       Landscape phones report width > 765px, so key off short height too. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        top: 40px;
    }

`,gi=M(Vn)`
    display: flex;
    width: ${N.hud.btn};
    height: ${N.hud.btn};
    align-items: center;
    justify-content: center;
    border-right: none;
    margin-top: 3px;
    background-repeat: no-repeat;
    background-size: cover;
    ${Hn}

    /* Shrink on narrow phones (portrait) AND short landscape phones — a phone
       held sideways is wider than 765px, so also match on short viewport height. */
    @media (max-width: 765px), (max-height: 500px) and (orientation: landscape) {
        width: ${N.hud.btnSmall};
        height: ${N.hud.btnSmall};
    }
`,_i=M(gi)`
    background-image: url("./img/EEW.png");
`,vi=M(gi)`
    background-image: url("./img/FEW.png");
`,yi=In`
    position: relative;
    border: 1px solid ${e=>e.$showing?N.colors.linePrimary:N.colors.line};
    border-right: none;
    background-image: ${e=>e.$showing?`linear-gradient(90deg, rgba(96, 137, 193, 0.32), rgba(96, 137, 193, 0.08))`:`none`};
    box-shadow: ${e=>e.$showing?`inset 2px 0 0 ${N.colors.greenBtnLineHover}, `:``}5px 5px 10px ${N.colors.overlayBg};
`,bi=In`
    opacity: ${e=>e.$showing?1:.45};
    //Only half grey: fully desaturated, the faded friendly (yellow) and enemy (orange)
    //ballistic icons become indistinguishable.
    filter: ${e=>e.$showing?`none`:`grayscale(0.5)`};
    transition: opacity 0.15s ease, filter 0.15s ease;
`,xi=In`
    opacity: ${e=>e.$showing?1:.7};
`,Si=M(gi)`
    ${yi}

    &::before {
        content: '';
        position: absolute;
        inset: 0;
        background-repeat: no-repeat;
        background-size: cover;
        ${bi}
    }
    &:hover::before {
        ${xi}
    }
`,Ci=M(Si)`
    &::before { background-image: url("./img/ballisticTarget2.png"); }
`,wi=M(Si)`
    &::before { background-image: url("./img/ballisticLaunch2.png"); }
`,Ti=M(Si)`
    &::before { background-image: url("./img/los1.png"); }
`,Ei=M(Si)`
    &::before { background-image: url("./img/hexNumber.png"); }
`,Di=M(gi)`
    ${yi}

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
        ${bi}
    }
    &:hover::after {
        ${xi}
    }
`,Oi=M(gi)`
    background-image: ${e=>e.$toggled?`url("./img/soundOn.png")`:`url("./img/soundOff.png")`};
    border: 1px solid ${N.colors.line};
    border-right: none;
`,ki=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Ai=M.div`
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
`,ji=M.div`
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    padding: 4px;
    max-width: 210px;
`,Mi=M.div`
    display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${e=>e.img});
    background-size: cover;
    align-items: center;
    opacity: 1 !important;    
    justify-content: center;
    ${Hn}
    border: 1px solid ${e=>e.selected?`#ef4444`:`transparent`};
    position: relative;
    box-shadow: ${e=>e.selected?`0 0 5px #b43131`:`none`};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`,Ni=class extends _.Component{selectMode(e,t){e.stopPropagation(),e.preventDefault();let{ship:n,system:r}=this.props;weaponManager.onSetModeClicked(n,r,t)}selectAllMode(e,t){e.stopPropagation(),e.preventDefault();let{ship:n,system:r}=this.props;weaponManager.onSetModeAllClicked(n,r,t)}render(){let{ship:e,system:t}=this.props,n=this.props.showModes!==!1,r=parseInt(t.firingMode),i=``;i=t.iconPath?`./img/systemicons/${t.iconPath}`:`./img/systemicons/${t.name}.png`;let a=[];for(let e in t.firingModes)if(t.firingModes.hasOwnProperty(e)){let n=parseInt(e),o=t.firingModes[e],s=n===r,c=t.modeLetters||1;t.modeLettersArray&&t.modeLettersArray[n]&&(c=t.modeLettersArray[n]),a.push((0,P.jsx)(Mi,{img:i,selected:s,onClick:e=>this.selectMode(e,n),onContextMenu:e=>this.selectAllMode(e,n),title:`Set mode: ${o} ${s?`(Current)`:``} (Right click to set all)`,children:o.substring(0,c)},n))}return(0,P.jsxs)(ki,{children:[n&&(0,P.jsx)(Ai,{children:`Select Firing Mode`}),(0,P.jsxs)(ji,{children:[n&&a,this.props.children]})]})}},Pi=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
    vertical-align: center;

    @media (max-width: 768px) {
        min-width: 250px;       
    }  

`,Fi=M.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,Ii=M.div`
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
    ${e=>e.$lockHeight?`height: ${e.$lockHeight}px; max-height: ${e.$lockHeight}px;`:``}

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

`,Li=M.div`
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
    cursor: ${e=>e.$readOnly?`default`:`grab`};
    touch-action: ${e=>e.$readOnly?`auto`:`none`};
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
    ${e=>e.$dragging&&`
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
    ${e=>e.$lineBefore&&`
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
    ${e=>e.$gapBefore&&`
        margin-top: ${e.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -${e.$gapSize}px;
            height: ${e.$gapSize}px;
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
    ${e=>e.$lineAtEnd&&`
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
`,Ri=M.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;

    @media (max-width: 768px) {
        margin-bottom: 4px;
        text-align: center;          
    }
`,zi=M.span`
    font-weight: bold;
`,Bi=M.span`
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

`,Vi=M(zi)`
    color: #ffb833;
    font-weight: normal;    
`,Hi=M(zi)`
    color: #ff3333;
    font-weight: normal;    
`,Ui=M.div`
    display: flex;
    gap: 2px;

    @media (max-width: 768px) {
        justify-content: center;       
    }
`,Wi=M.div`
    width: 18px;
    height: 18px;
    background-image: url(${e=>e.img});
    background-size: cover;
    cursor: pointer;
    opacity: 0.9;
    margin-left: 3px;
    &:hover {
        opacity: 1;
    }
    
     ${Hn}
`,Gi=M.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,Ki=M.div`
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
`,qi=M.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,Ji=M(Li)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,Yi=M.span`
    color: #00b8e6;
    font-weight: normal;
    margin-right: 4px;
`,Xi=M.input`
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
`,Zi=M.span`
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
`,Qi=class extends _.Component{constructor(e){super(e),this.state={priorityInputs:{},drag:null},this.lastOrder=[],this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener(`pointermove`,this.onDragMove),window.removeEventListener(`pointerup`,this.onDragEnd),window.removeEventListener(`pointercancel`,this.onDragEnd)}getEffectiveCriticalRepairCost(e,t){return t.name===`cnC`?4:e.repairCost}getDockedUnits(){let{ship:e,system:t}=this.props;if(!t.servicesDockedUnits)return[];let n=[],r=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(let e of r)if(e.isDockingBay&&Array.isArray(e.shipsDocked))for(let t of e.shipsDocked){let e=gamedata.getShip(t.shipId);e&&n.indexOf(e)===-1&&n.push(e)}return n}getDockedRepairables(){let{system:e}=this.props,t=[];for(let n of this.getDockedUnits()){let r=Array.isArray(n.systems)?n.systems:Object.values(n.systems),i=`d`+n.id+`:`;for(let a of r){let r=a.name===`structure`||a.name===`cnC`||a.name===`SelfRepair`;if(!shipManager.systems.isDestroyed(n,a)&&a.repairPriority>=1&&a.criticals){let r=Array.isArray(a.criticals)?a.criticals:Object.values(a.criticals);for(let o of r){if(o.repairPriority===0||o.turn>=gamedata.turn||o.oneturn||o.turnend>0)continue;let r=i+a.id+`-`+o.id,s=o.repairPriority||0,c=!1;e.priorityChanges&&r in e.priorityChanges&&e.priorityChanges[r]>=0?(s=e.priorityChanges[r],c=!0):s<10&&(s+=a.repairPriority),!(s<1)&&t.push({type:`critical`,sys:a,crit:o,ownerShip:n,shipId:n.id,docked:!0,priority:s,overridden:c,cost:this.getEffectiveCriticalRepairCost(o,a),id:a.id,subId:o.id,keyId:r})}}if(!r||a.repairPriority===0)continue;let o=shipManager.systems.getTotalDamage(a);if(o<=0)continue;if(a.name===`structure`){if(shipManager.systems.isDestroyed(n,a))continue}else{let e=a.structureHomeLocation!==void 0&&a.structureHomeLocation!==null?a.structureHomeLocation:a.location;if(e!=0){let t=shipManager.systems.getStructureSystem(n,e);if(t&&shipManager.systems.isDestroyed(n,t))continue}}let s=i+a.id,c=a.repairPriority,l=!1;e.priorityChanges&&s in e.priorityChanges&&e.priorityChanges[s]>=0&&(c=e.priorityChanges[s],l=!0),!(c<1)&&(!l&&shipManager.systems.isDestroyed(n,a)&&c<=10&&(c+=10),t.push({type:`system`,sys:a,ownerShip:n,shipId:n.id,docked:!0,priority:c,overridden:l,damage:o,maxHealth:a.maxhealth,id:a.id,subId:0,keyId:s}))}}return t}getRepairableSystems(){let{ship:e,system:t}=this.props,n=[],r=[],i=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(let o of i){if(o.name===`SelfRepair`||o.repairPriority===0||o.privateRepairOnly&&!t.repairRestrictedTo||t.repairRestrictedTo&&!t.repairRestrictedTo.includes(o.id)||o.name==`structure`&&shipManager.systems.isDestroyed(o.ship,o))continue;let i=o.structureHomeLocation!==void 0&&o.structureHomeLocation!==null?o.structureHomeLocation:o.location;if(o.name!=`structure`&&i!=0){var a=shipManager.systems.getStructureSystem(o.ship,i);if(a&&shipManager.systems.isDestroyed(o.ship,a))continue}let s=o.repairPriority,c=!1;if(t.priorityChanges&&o.id in t.priorityChanges&&t.priorityChanges[o.id]>=0&&(s=t.priorityChanges[o.id],c=!0),!c&&shipManager.systems.isDestroyed(e,o)&&s<=10&&(s+=10),!shipManager.systems.isDestroyed(e,o)&&o.criticals){let n=Array.isArray(o.criticals)?o.criticals:Object.values(o.criticals);for(let i of n){if(i.repairPriority===0||i.turn>=gamedata.turn||i.oneturn||i.turnend>0)continue;let n=i.repairPriority||0,a=o.id+`-`+i.id,s=!1;t.priorityChanges&&a in t.priorityChanges&&t.priorityChanges[a]>=0?(n=t.priorityChanges[a],s=!0):n<10&&(n+=o.repairPriority),r.push({type:`critical`,sys:o,crit:i,ownerShip:e,shipId:0,docked:!1,priority:n,overridden:s,cost:this.getEffectiveCriticalRepairCost(i,o),id:o.id,subId:i.id,keyId:a})}}let l=shipManager.systems.getTotalDamage(o);l>0&&n.push({type:`system`,sys:o,ownerShip:e,shipId:0,docked:!1,priority:s,overridden:c,damage:l,maxHealth:o.maxhealth,id:o.id,subId:0,keyId:o.id})}let o=[...r,...n,...this.getDockedRepairables()];return o.sort((e,t)=>{if(e.priority!==t.priority)return t.priority-e.priority;let n=!!e.overridden;if(n!==!!t.overridden)return n?-1:1;if(this.lastOrder&&this.lastOrder.length>0){let n=this.lastOrder.indexOf(e.keyId),r=this.lastOrder.indexOf(t.keyId);if(n!==-1&&r!==-1)return n-r}let r=e.shipId||0,i=t.shipId||0;return r===i?e.id===t.id?e.subId-t.subId:e.id-t.id:r-i}),this.lastOrder=o.map(e=>e.keyId),o}handleInputChange(e,t,n){let r=e.target.value;if(r===``){this.setState(e=>({priorityInputs:{...e.priorityInputs,[t]:``}}));return}let i=parseInt(r,10);isNaN(i)||(this.setState(e=>({priorityInputs:{...e.priorityInputs,[t]:i}})),this.setPriority(t,i))}handleWheel(e,t,n){e.preventDefault();let r=n+(e.deltaY<0?1:-1);r<1||this.setPriority(t,r)}componentDidUpdate(e){if(this.dragRef&&this.dragRef.started&&this.listRef){let e=this.listRef.querySelector(`[data-keyid="`+this.dragRef.keyId+`"]`);e&&this.positionDraggedEl(e,this.dragRef,this.dragRef.lastClientY)}let t=this.getRepairableSystems(),n=this.state.priorityInputs,r={},i=!1;t.forEach(e=>{let t=e.keyId,a=e.priority;n[t]!==void 0&&n[t]!==a&&document.activeElement!==document.getElementById(`prio-input-${t}`)&&(r[t]=a,i=!0)}),i&&this.setState(e=>({priorityInputs:{...e.priorityInputs,...r}}))}handleTop(e,t){e.stopPropagation();let n=this.getRepairableSystems();if(n.length===0)return;let r=n[0].priority,i=n.find(e=>e.keyId===t);if(!i||i.priority===r)return;let a=r+1;this.setPriority(t,a)}handleUp(e,t,n){e.stopPropagation();let r=n+1;r!==n&&this.setPriority(t,r)}handleDown(e,t,n){e.stopPropagation(),!(n<=1)&&this.setPriority(t,n-1)}handleReset(e,t){e.stopPropagation(),this.setPriority(t,-1)}setPriority(e,t){let{ship:n,system:r}=this.props;r.setOverride(e,t),webglScene.customEvent(`SystemDataChanged`,{ship:n,system:r})}onRowPointerDown(e,t,n,r){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest(`input, .sr-action-button`))return;let i=e.currentTarget,a=i?i.offsetHeight:24,o=this.listRef?this.listRef.offsetHeight:0,s=i?i.offsetWidth:0,c=i?e.clientY-i.getBoundingClientRect().top:0;this.dragRef={keyId:t,pointerId:e.pointerId,startY:e.clientY,startIdx:n,order:r,gapSize:a,lockHeight:o,anchorWidth:s,grabOffsetInRow:c,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener(`pointermove`,this.onDragMove),window.addEventListener(`pointerup`,this.onDragEnd),window.addEventListener(`pointercancel`,this.onDragEnd)}onDragMove(e){let t=this.dragRef;if(t&&e.pointerId===t.pointerId){if(!t.started){if(Math.abs(e.clientY-t.startY)<4)return;t.started=!0}e.preventDefault(),t.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){let t=this.dragRef;if(!t||!this.listRef)return;let n=this.listRef.querySelectorAll(`[data-keyid]`),r=e-this.listRef.getBoundingClientRect().top+this.listRef.scrollTop,i=this.state.drag&&this.state.drag.dropIdx===0?t.gapSize:0,a=0,o=null;for(let e=0;e<n.length;e++)if(n[e].getAttribute(`data-keyid`)===String(t.keyId))o=n[e];else{if(r<n[e].offsetTop-i+n[e].offsetHeight/2)break;a++}o&&this.positionDraggedEl(o,t,e);let s=this.state.drag;(!s||s.keyId!==t.keyId||s.dropIdx!==a)&&this.setState({drag:{keyId:t.keyId,startIdx:t.startIdx,dropIdx:a,gapSize:t.gapSize,lockHeight:t.lockHeight}})}positionDraggedEl(e,t,n){let r=n-this.listRef.getBoundingClientRect().top+this.listRef.scrollTop-t.grabOffsetInRow,i=Math.max(0,this.listRef.scrollHeight-t.gapSize);r<0?r=0:r>i&&(r=i),e.style.top=r+`px`,e.style.left=`0px`,e.style.width=t.anchorWidth+`px`,e.style.transform=`none`}updateAutoScroll(e){let t=this.listRef;if(!t){this.stopAutoScroll();return}let n=t.getBoundingClientRect(),r=t.scrollTop>0,i=t.scrollTop<t.scrollHeight-t.clientHeight-1,a=e-n.top,o=n.bottom-e;r&&a<30?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,a)/30),this.ensureAutoScrollRunning()):i&&o<30?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,o)/30),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF??=requestAnimationFrame(this.autoScrollTick)}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;let e=this.listRef,t=this.dragRef;if(!e||!t||this.autoScrollDir===0)return;let n=e.scrollTop;e.scrollTop=n+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===n?this.stopAutoScroll():(this.updateDragForPointer(t.lastClientY),this.updateAutoScroll(t.lastClientY))}onDragEnd(e){let t=this.dragRef;if(!t||e&&e.pointerId!=null&&e.pointerId!==t.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){let e=this.listRef.querySelector(`[data-keyid="`+t.keyId+`"]`);e&&(e.style.transform=``,e.style.top=``,e.style.left=``,e.style.width=``)}this.dragRef=null;let n=this.state.drag;this.setState({drag:null}),t.started&&n&&n.dropIdx!==t.startIdx&&this.applyDropReorder(t.order,t.keyId,n.dropIdx)}applyDropReorder(e,t,n){let{ship:r,system:i}=this.props,a=e.findIndex(e=>e.keyId===t);if(a===-1)return;let o=e.slice(),[s]=o.splice(a,1);o.splice(n,0,s);let c=o[n+1],l=o[n-1],u;u=c?c.priority+1:l?Math.max(1,l.priority-1):1,i.setOverride(t,u);for(let e=n-1;e>=0&&!(o[e].priority>u);e--)u+=1,i.setOverride(o[e].keyId,u);webglScene.customEvent(`SystemDataChanged`,{ship:r,system:i})}handlePropagate(e){e.stopPropagation();let{ship:t,system:n}=this.props;for(let e of t.systems)if(e.name===`SelfRepair`&&e.id!==n.id){if(e.priorityChanges)for(let t in e.priorityChanges)(!n.priorityChanges||!(t in n.priorityChanges))&&e.setOverride(t,-1);if(n.priorityChanges)for(let t in n.priorityChanges){let r=n.priorityChanges[t];r>=0&&e.setOverride(t,r)}}webglScene.customEvent(`SystemDataChanged`,{ship:t,system:n})}render(){let{ship:e,readOnly:t}=this.props,n=this.getRepairableSystems(),r=0,i=Array.isArray(e.systems)?e.systems:Object.values(e.systems);for(let e of i)e.name===`SelfRepair`&&r++;return(0,P.jsxs)(Pi,{children:[(0,P.jsx)(Fi,{children:t?`Repair Queue (view only)`:`Manage Repair Queue`}),(0,P.jsxs)(Ii,{ref:e=>{this.listRef=e},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[n.length===0&&(0,P.jsx)(Ji,{children:`No damaged systems`}),n.map((r,i)=>{let a=this.state.drag,o=a&&a.keyId===r.keyId,s=!1,c=!1,l=!1;if(a&&!o){let e=n.length-1,t=i>a.startIdx?i-1:i;a.dropIdx===0&&t===0?s=!0:a.dropIdx===e&&t===e-1?c=!0:a.dropIdx===t&&(l=!0)}let u=r.ownerShip||e;return(0,P.jsxs)(Li,{"data-keyid":r.keyId,$dragging:o,$gapBefore:s,$lineAtEnd:c,$lineBefore:l,$gapSize:a?a.gapSize:0,$readOnly:t,onPointerDown:e=>this.onRowPointerDown(e,r.keyId,i,n),children:[(0,P.jsx)(Ri,{children:r.type===`critical`?(0,P.jsxs)(P.Fragment,{children:[(0,P.jsxs)(Vi,{children:[r.docked&&(0,P.jsxs)(Yi,{children:[u.name,`:`]}),r.sys.displayName,` (`,r.crit.description||r.crit.phpclass,`)`]}),(0,P.jsxs)(Bi,{children:[`Cost: `,r.cost,` `,(0,P.jsx)(qi,{}),` Id: `,r.sys.id]})]}):(0,P.jsxs)(P.Fragment,{children:[shipManager.systems.isDestroyed(u,r.sys)?(0,P.jsxs)(Hi,{children:[r.docked&&(0,P.jsxs)(Yi,{children:[u.name,`:`]}),r.sys.displayName]}):(0,P.jsxs)(zi,{children:[r.docked&&(0,P.jsxs)(Yi,{children:[u.name,`:`]}),r.sys.displayName]}),(0,P.jsxs)(Bi,{children:[`HP: `,shipManager.systems.getRemainingHealth(r.sys),` / `,r.sys.maxhealth,` `,(0,P.jsx)(qi,{}),` Id: `,r.sys.id]})]})}),(0,P.jsx)(Ui,{children:t?(0,P.jsx)(Zi,{title:`Priority (view only)`,children:r.priority}):(0,P.jsxs)(P.Fragment,{children:[(0,P.jsx)(Wi,{className:`sr-action-button`,title:`Reset Default`,onClick:e=>this.handleReset(e,r.keyId),img:`./img/iconSRCancel.png`}),(0,P.jsx)(Wi,{className:`sr-action-button`,title:`Decrease Priority`,onClick:e=>this.handleDown(e,r.keyId,r.priority),img:`./img/systemicons/AAclasses/iconMinus.png`}),(0,P.jsx)(Xi,{id:`prio-input-${r.keyId}`,type:`number`,value:this.state.priorityInputs[r.keyId]===void 0?r.priority:this.state.priorityInputs[r.keyId],onChange:e=>this.handleInputChange(e,r.keyId,r.priority),onClick:e=>e.stopPropagation(),onWheel:e=>this.handleWheel(e,r.keyId,r.priority)}),(0,P.jsx)(Wi,{className:`sr-action-button`,title:`Increase Priority`,onClick:e=>this.handleUp(e,r.keyId,r.priority),img:`./img/systemicons/AAclasses/iconPlus.png`}),(0,P.jsx)(Wi,{className:`sr-action-button`,title:`Move to Top`,onClick:e=>this.handleTop(e,r.keyId),img:`./img/iconSRHigh.png`})]})})]},r.keyId)})]}),!t&&r>1&&(0,P.jsx)(Gi,{children:(0,P.jsx)(Ki,{onClick:e=>this.handlePropagate(e),children:`Set all Self Repair systems`})})]})}},$i=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 250px;
`,ea=M.div`
    padding: 3px;
    background-color: #2b3e51;
    border: 1px solid #496791;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,ta=M.div`
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    max-height: 200px;
    overflow-y: auto;
    overflow-x: hidden;
    display: block;
    position: relative;
    scrollbar-gutter: stable;
    ${e=>e.$lockHeight?`height: ${e.$lockHeight}px; max-height: ${e.$lockHeight}px;`:``}

    &::-webkit-scrollbar { width: 6px; }
    &::-webkit-scrollbar-track { background: #0d1620; }
    &::-webkit-scrollbar-thumb { background: #2b3e51; }
    &::-webkit-scrollbar-thumb:hover { background: #5a7ea8; }
`,na=M.div`
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 5px;
    margin-right: 3px;
    border-bottom: 1px solid #2b3e51;
    font-size: 12px;
    color: #e6e6e6;
    cursor: ${e=>e.$readOnly?`default`:`grab`};
    touch-action: ${e=>e.$readOnly?`auto`:`none`};
    user-select: none;
    position: relative;

    ${e=>e.$dragging&&`
        position: absolute;
        opacity: 0.95;
        z-index: 3;
        cursor: grabbing;
        background-color: rgba(43, 62, 81, 0.92);
        pointer-events: none;
        transition: none;
    `}

    ${e=>e.$lineBefore&&`
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

    ${e=>e.$gapBefore&&`
        margin-top: ${e.$gapSize}px;
        &::before {
            content: "";
            position: absolute;
            left: 0; right: 0;
            top: -${e.$gapSize}px;
            height: ${e.$gapSize}px;
            box-shadow: inset 0 0 0 2px #ffcc33, 0 0 6px 1px rgba(255, 204, 51, 0.5);
            background-color: rgba(255, 204, 51, 0.12);
            pointer-events: none;
        }
    `}

    ${e=>e.$lineAtEnd&&`
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
`,ra=M(na)`
    justify-content: center;
    font-style: italic;
    opacity: 0.7;
`,ia=M.div`
    flex-grow: 1;
    display: flex;
    flex-direction: column;
`,aa=M.span`
    font-weight: bold;
`,oa=M(aa)`
    color: #ff3333;
    font-weight: normal;
`,sa=M.span`
    font-size: 9px;
    color: #c8d5ea;
    margin-top: 2px;
    margin-left: 1px;
`,ca=M.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 4px;
    vertical-align: middle;
    opacity: 0.7;
`,F=M.div`
    padding: 4px;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #496791;
    border-top: none;
    text-align: center;
`,I=M.div`
    cursor: pointer;
    background-color: #2b3e51;
    border: 1px solid #496791;
    padding: 3px 8px;
    font-size: 12px;
    color: #f2f2f2;
    font-weight: normal;
    display: inline-block;
    &:hover { background-color: #496791; color: #ffffff; }
`,la=M.div`
    font-size: 9px;
    color: #7a99bb;
    text-align: center;
    padding: 2px 0 0 0;
    font-style: italic;
`,ua=class extends _.Component{constructor(e){super(e),this.state={drag:null},this.dragRef=null,this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.autoScrollTick=this.autoScrollTick.bind(this),this.autoScrollRAF=null,this.autoScrollDir=0}componentWillUnmount(){this.removeDragListeners(),this.stopAutoScroll()}removeDragListeners(){window.removeEventListener(`pointermove`,this.onDragMove),window.removeEventListener(`pointerup`,this.onDragEnd),window.removeEventListener(`pointercancel`,this.onDragEnd)}getOrderedBlocks(){let{ship:e,system:t}=this.props,n=(t.structureBlocks||[]).map(t=>{let n=(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).find(e=>e.id===t.id),r=n?shipManager.systems.getRemainingHealth(n):t.maxhealth,i=n?n.maxhealth:t.maxhealth||0,a=n?shipManager.systems.isDestroyed(e,n):!1;return{id:t.id,displayName:t.displayName,hp:r,maxhealth:i,destroyed:a}}),r=t.repairOrder||[];if(r.length===0)return n.slice().sort((e,t)=>{let n=+!!t.destroyed,r=+!!e.destroyed;return n===r?t.maxhealth-t.hp-(e.maxhealth-e.hp):n-r});let i=[],a=new Set;for(let e of r){let t=n.find(t=>t.id===e);t&&(i.push(t),a.add(e))}for(let e of n)a.has(e.id)||i.push(e);return i}onRowPointerDown(e,t,n,r){if(this.props.readOnly||e.button!=null&&e.button!==0||e.target&&e.target.closest&&e.target.closest(`.ssr-action-button`))return;let i=e.currentTarget,a=i?i.offsetHeight:24,o=this.listRef?this.listRef.offsetHeight:0,s=i?i.offsetWidth:0,c=i?e.clientY-i.getBoundingClientRect().top:0;this.dragRef={keyId:t,pointerId:e.pointerId,startY:e.clientY,startIdx:n,order:r,gapSize:a,lockHeight:o,anchorWidth:s,grabOffsetInRow:c,lastClientY:e.clientY,started:!1},e.preventDefault(),window.addEventListener(`pointermove`,this.onDragMove),window.addEventListener(`pointerup`,this.onDragEnd),window.addEventListener(`pointercancel`,this.onDragEnd)}onDragMove(e){let t=this.dragRef;if(t&&e.pointerId===t.pointerId){if(!t.started){if(Math.abs(e.clientY-t.startY)<4)return;t.started=!0}e.preventDefault(),t.lastClientY=e.clientY,this.updateDragForPointer(e.clientY),this.updateAutoScroll(e.clientY)}}updateDragForPointer(e){let t=this.dragRef;if(!t||!this.listRef)return;let n=this.listRef.querySelectorAll(`[data-keyid]`),r=e-this.listRef.getBoundingClientRect().top+this.listRef.scrollTop,i=this.state.drag&&this.state.drag.dropIdx===0?t.gapSize:0,a=0,o=null;for(let e=0;e<n.length;e++)if(n[e].getAttribute(`data-keyid`)===String(t.keyId))o=n[e];else{if(r<n[e].offsetTop-i+n[e].offsetHeight/2)break;a++}o&&this.positionDraggedEl(o,t,e);let s=this.state.drag;(!s||s.keyId!==t.keyId||s.dropIdx!==a)&&this.setState({drag:{keyId:t.keyId,startIdx:t.startIdx,dropIdx:a,gapSize:t.gapSize,lockHeight:t.lockHeight}})}positionDraggedEl(e,t,n){let r=n-this.listRef.getBoundingClientRect().top+this.listRef.scrollTop-t.grabOffsetInRow,i=Math.max(0,this.listRef.scrollHeight-t.gapSize);r<0?r=0:r>i&&(r=i),e.style.top=r+`px`,e.style.left=`0px`,e.style.width=t.anchorWidth+`px`,e.style.transform=`none`}updateAutoScroll(e){let t=this.listRef;if(!t){this.stopAutoScroll();return}let n=t.getBoundingClientRect(),r=t.scrollTop>0,i=t.scrollTop<t.scrollHeight-t.clientHeight-1,a=e-n.top,o=n.bottom-e;r&&a<30?(this.autoScrollDir=-1,this.autoScrollSpeed=2+12*(1-Math.max(0,a)/30),this.ensureAutoScrollRunning()):i&&o<30?(this.autoScrollDir=1,this.autoScrollSpeed=2+12*(1-Math.max(0,o)/30),this.ensureAutoScrollRunning()):this.stopAutoScroll()}ensureAutoScrollRunning(){this.autoScrollRAF??=requestAnimationFrame(this.autoScrollTick)}stopAutoScroll(){this.autoScrollDir=0,this.autoScrollRAF!=null&&(cancelAnimationFrame(this.autoScrollRAF),this.autoScrollRAF=null)}autoScrollTick(){this.autoScrollRAF=null;let e=this.listRef,t=this.dragRef;if(!e||!t||this.autoScrollDir===0)return;let n=e.scrollTop;e.scrollTop=n+this.autoScrollDir*(this.autoScrollSpeed||6),e.scrollTop===n?this.stopAutoScroll():(this.updateDragForPointer(t.lastClientY),this.updateAutoScroll(t.lastClientY))}onDragEnd(e){let t=this.dragRef;if(!t||e&&e.pointerId!=null&&e.pointerId!==t.pointerId)return;if(this.removeDragListeners(),this.stopAutoScroll(),this.listRef){let e=this.listRef.querySelector(`[data-keyid="`+t.keyId+`"]`);e&&(e.style.transform=``,e.style.top=``,e.style.left=``,e.style.width=``)}this.dragRef=null;let n=this.state.drag;this.setState({drag:null}),t.started&&n&&n.dropIdx!==t.startIdx&&this.applyDropReorder(t.order,t.keyId,n.dropIdx)}componentDidUpdate(){if(this.dragRef&&this.dragRef.started&&this.listRef){let e=this.listRef.querySelector(`[data-keyid="`+this.dragRef.keyId+`"]`);e&&this.positionDraggedEl(e,this.dragRef,this.dragRef.lastClientY)}}applyDropReorder(e,t,n){let{ship:r,system:i}=this.props,a=e.slice(),o=a.findIndex(e=>e.id===t);if(o===-1)return;let[s]=a.splice(o,1);a.splice(n,0,s);let c=a.map(e=>e.id);i.setRepairOrder(c),webglScene.customEvent(`SystemDataChanged`,{ship:r,system:i})}handleReset(e){e.stopPropagation();let{ship:t,system:n}=this.props;n.setRepairOrder([]),webglScene.customEvent(`SystemDataChanged`,{ship:t,system:n})}render(){let{ship:e,readOnly:t}=this.props,n=this.getOrderedBlocks(),r=(this.props.system.repairOrder||[]).length>0;return(0,P.jsxs)($i,{children:[(0,P.jsx)(ea,{children:t?`Structure Repair Order (view only)`:`Manage Structure Repair`}),!t&&(0,P.jsx)(la,{children:`Drag rows to set repair priority — top = first repaired`}),(0,P.jsxs)(ta,{ref:e=>{this.listRef=e},$lockHeight:this.state.drag?this.state.drag.lockHeight:0,children:[n.length===0&&(0,P.jsx)(ra,{children:`No structure blocks found`}),n.map((e,r)=>{let i=this.state.drag,a=i&&i.keyId===e.id,o=!1,s=!1,c=!1;if(i&&!a){let e=n.length-1,t=r>i.startIdx?r-1:r;i.dropIdx===0&&t===0?o=!0:i.dropIdx===e&&t===e-1?s=!0:i.dropIdx===t&&(c=!0)}let l=e.maxhealth-e.hp;return(0,P.jsx)(na,{"data-keyid":e.id,$dragging:a,$gapBefore:o,$lineAtEnd:s,$lineBefore:c,$gapSize:i?i.gapSize:0,$readOnly:t,onPointerDown:t=>this.onRowPointerDown(t,e.id,r,n),children:(0,P.jsxs)(ia,{children:[e.destroyed?(0,P.jsx)(oa,{children:e.displayName}):(0,P.jsx)(aa,{children:e.displayName}),(0,P.jsxs)(sa,{children:[`HP: `,e.hp,` / `,e.maxhealth,l>0&&(0,P.jsxs)(P.Fragment,{children:[(0,P.jsx)(ca,{}),`Dmg: `,l,e.destroyed?` — DESTROYED`:``]})]})]})},e.id)})]}),!t&&(0,P.jsx)(F,{children:(0,P.jsx)(I,{className:`ssr-action-button`,onClick:e=>this.handleReset(e),title:`Clear custom order and return to default (destroyed first, then most damaged)`,children:r?`Reset to Default Order`:`Using Default Order`})})]})}},da=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(0, 0, 0, 0.9);
    border: 1px solid #808080;
`,fa=M.div`
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
`,pa=M.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,ma=M.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #808080;
    font-size: 11px;
    color: #e6e6e6;

    &:hover {
        background-color: rgba(43, 62, 81, 0.6);
    }
`,ha=M.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,ga=M.div`
    flex: 1;  
    margin-right: 5px;      
    font-weight: normal; 
`,_a=M.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,va=M.div`
    width: 20px;
    text-align: center;
`,ya=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #2b3e51; color: #f2f2f2; }
    `}
`,ba=M.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,xa=class extends _.Component{constructor(e){super(e),this.listRef=_.createRef()}handleIncrease(e){let{system:t}=this.props;t.setCurrDmgType(e),t.canIncrease()&&(t.doIncrease(),this.forceUpdate())}handleDecrease(e){let{system:t}=this.props;t.setCurrDmgType(e),t.canDecrease()&&(t.doDecrease(),this.forceUpdate())}handlePropagate(e){let{ship:t,system:n}=this.props,r=window.gamedata,i=window.shipManager,a=window.webglScene;console.log(`Propagating AA setting for:`,e),n.setCurrDmgType(e);let o=n.allocatedAA[e];var s=[];for(var c in r.ships){var l=r.ships[c];if(l.userid==t.userid&&!i.isDestroyed(l)){if(l.flight)for(var u=0;u<l.systems.length;u++){var d=l.systems[u];if(d)for(var f=0;f<d.systems.length;f++){var p=d.systems[f];if(p&&p.displayName==`Adaptive Armor Controller`){s.push(p);break}}}else for(var f=0;f<l.systems.length;f++){var p=l.systems[f];if(p.displayName==`Adaptive Armor Controller`){s.push(p);break}}}}console.log(`Found AA controllers:`,s.length);for(var m=0;m<s.length;m++){var p=s[m];p.setCurrDmgType(e);let t=0;for(;p.getCurrAllocated()<o&&p.canIncrease()&&t<100;)p.doIncrease(),t++;t>=100&&console.warn(`AA Propagation safety break for`,p)}a.customEvent(`SystemDataChanged`,{ship:t,system:n})}getRelevantArmorTypes(){let{ship:e,system:t}=this.props;return t.getRelevantArmorTypes(e)}render(){let{ship:e,system:t}=this.props;if(!t||!t.availableAA)return null;let n=this.getRelevantArmorTypes(),r=t.allocatedAA,i=e=>{let n=t.AAtotal_used,i=t.AAtotal,a=r[e]||0,o=t.AApertype,s=t.availableAA[e],c=t.AApreallocated,l=t.AApreallocated_used;return!(n>=i||a>=o||c<=l&&s<=a)},a=e=>t.currchangedAA[e]>0,o=e=>r[e]>0;return(0,P.jsxs)(da,{children:[(0,P.jsx)(fa,{children:`Manage Adaptive Armor`}),(0,P.jsxs)(pa,{ref:this.listRef,children:[n.map(e=>(0,P.jsxs)(ma,{children:[(0,P.jsx)(ha,{src:`./img/systemicons/AAclasses/${e}.png`,alt:e}),(0,P.jsx)(ga,{children:e}),(0,P.jsxs)(_a,{children:[(0,P.jsx)(ya,{onClick:()=>this.handleDecrease(e),disabled:!a(e),children:`-`}),(0,P.jsx)(va,{children:r[e]}),(0,P.jsx)(ya,{onClick:()=>this.handleIncrease(e),disabled:!i(e),children:`+`}),(0,P.jsx)(ya,{title:`Propagate to all units`,onClick:()=>this.handlePropagate(e),disabled:!o(e),style:{marginLeft:`5px`},children:(0,P.jsx)(`img`,{src:`./img/systemicons/AAclasses/iconPropagate.png`,alt:`Propagate`,style:{width:`12px`,height:`12px`}})})]})]},e)),n.length===0&&(0,P.jsx)(ma,{children:`No armor types available`})]}),(0,P.jsxs)(`div`,{style:{padding:`5px`,textAlign:`center`,fontSize:`10px`,color:`#f2f2f2`},children:[`Total: `,t.AAtotal_used,` / `,t.AAtotal,` `,(0,P.jsx)(ba,{}),` Max Per Type: `,t.AApertype]})]})}},Sa=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,Ca=M.div`
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
`,wa=M.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Ta=M.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #5d3564;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(75, 43, 81, 0.6);
    }
`,Ea=M.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,Da=M.div`
    flex: 1;
    font-weight: normal; 
`,Oa=M.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,ka=M.div`
    width: 20px;
    text-align: center;
`,Aa=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`,ja=M.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`,Ma=class extends _.Component{constructor(e){super(e),this.listRef=_.createRef()}handleIncrease(e){let{system:t}=this.props;t.setCurrFCType(e),t.canIncrease()&&(t.doIncrease(),this.forceUpdate())}handleDecrease(e){let{system:t}=this.props;t.setCurrFCType(e),t.canDecrease()&&(t.doDecrease(),this.forceUpdate())}handlePropagate(e){let{ship:t,system:n}=this.props,r=window.gamedata,i=window.shipManager,a=window.webglScene;console.log(`Propagating BFCP setting for:`,e),n.setCurrFCType(e);let o=n.allocatedBFCP[e];var s=[];for(var c in r.ships){var l=r.ships[c];if(l.userid==t.userid&&!i.isDestroyed(l)){if(l.flight)for(var u=0;u<l.systems.length;u++){var d=l.systems[u];if(d)for(var f=0;f<d.systems.length;f++){var p=d.systems[f];if(p&&p.displayName==`Computer`){s.push(p);break}}}else for(var f=0;f<l.systems.length;f++){var p=l.systems[f];if(p.displayName==`Computer`){s.push(p);break}}}}console.log(`Found BFCP controllers:`,s.length);for(var m=0;m<s.length;m++){var p=s[m];p.setCurrFCType(e);let t=0;for(;p.getCurrAllocated()<o&&p.canIncrease()&&t<100;)p.doIncrease(),t++;for(;p.getCurrAllocated()>o&&p.canDecrease()&&t<100;)p.doDecrease(),t++;t>=100&&console.warn(`BFCP Propagation safety break for`,p)}a.customEvent(`SystemDataChanged`,{ship:t,system:n})}render(){let{system:e}=this.props;if(!e||!e.allocatedBFCP)return null;let t=Object.keys(e.allocatedBFCP),n=e.allocatedBFCP,r=(t,n)=>{let r=e.currClass;e.setCurrFCType(t);let i=!!e[n]();return e.currClass=r,i},i=e=>r(e,`canIncrease`),a=e=>r(e,`canDecrease`),o=window.gamedata&&window.gamedata.gamephase===3&&typeof e.firingMode==`function`?e.firingMode():null,s=e=>n[e]>=0;return(0,P.jsxs)(Sa,{children:[(0,P.jsx)(Ca,{children:`Hyach Computer`}),(0,P.jsxs)(wa,{ref:this.listRef,children:[t.map(e=>(0,P.jsxs)(Ta,{children:[(0,P.jsx)(Ea,{src:`./img/systemicons/BFCPclasses/${e}.png`,alt:e}),(0,P.jsx)(Da,{children:e}),(0,P.jsxs)(Oa,{children:[(0,P.jsx)(Aa,{onClick:()=>this.handleDecrease(e),disabled:!a(e),children:`-`}),(0,P.jsx)(ka,{children:n[e]}),(0,P.jsx)(Aa,{onClick:()=>this.handleIncrease(e),disabled:!i(e),children:`+`}),(0,P.jsx)(Aa,{title:`Propagate to all units`,onClick:()=>this.handlePropagate(e),disabled:!s(e),style:{marginLeft:`5px`},children:(0,P.jsx)(`img`,{src:`./img/systemicons/BFCPclasses/iconPropagate.png`,alt:`Propagate`,style:{width:`12px`,height:`12px`}})})]})]},e)),t.length===0&&(0,P.jsx)(Ta,{children:`No FC types available`})]}),(0,P.jsxs)(`div`,{style:{padding:`5px`,textAlign:`center`,fontSize:`10px`,color:`#f2f2f2`},children:[`Total: `,e.BFCPtotal_used,` / `,e.output,` `,(0,P.jsx)(ja,{}),` Max Per Type: `,e.BFCPpertype]}),o&&(0,P.jsx)(`div`,{style:{padding:`0 5px 5px`,textAlign:`center`,fontSize:`10px`,color:`#c9a6cf`},children:o===`reshuffle`?`Computer Specialist: any point may be moved`:`Expert Software Engineer: one held point may be added`})]})}},Na=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #5d3564;
`,Pa=M.div`
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
`,Fa=M.div`
    background-color: rgba(0, 0, 0, 0.8);
    border: 1px solid #4b2b51; 
    max-height: 280px;
    overflow-y: auto;
    display: block;
`,Ia=M.div`
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
`,La=M.img`
    width: 20px;
    height: 20px;
    margin-right: 5px;
`,Ra=M.span`
    flex-grow: 1;
    font-weight: bold;
`,za=M.div`
    display: flex;
    gap: 2px;
    align-items: center;
`,Ba=M.div`
    width: 16px; 
    height: 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: ${e=>e.disabled?`#555`:`#7c4686`};
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: ${e=>e.disabled?`default`:`pointer`};
    border: 1px solid #aaa;
    opacity: ${e=>e.disabled?.5:1};
    
    &:hover {
         background-color: ${e=>e.disabled?`#555`:`#a06daa`};
    }
    ${e=>!e.disabled&&Hn}
`,Va=e=>window.gamedata.turn===window.shipManager.getTurnPlaced(e)&&window.gamedata.gamephase===-1,Ha=class extends _.Component{constructor(e){super(e)}handleSelect(e){let{system:t}=this.props;t.specCurrClass=e,t.canSelect()&&(t.doSelect(),this.forceUpdate())}handleUnselect(e){let{system:t}=this.props;t.specCurrClass=e,t.canUnselect()&&(t.doUnselect(),this.forceUpdate())}handleUse(e){let{system:t}=this.props;t.specCurrClass=e,t.canUse()&&(t.doUse(),this.forceUpdate())}handleCancel(e){let{system:t}=this.props;t.specCurrClass=e,t.canDecrease()&&(t.doDecrease(),this.forceUpdate())}render(){let{ship:e,system:t}=this.props;if(!t)return null;let n=Va(e),r=[],i=!n&&window.officers?window.officers.specialistReuseList(e):[];n?t.allSpec&&(r=Object.keys(t.allSpec)):t.availableSpec&&(r=Object.keys(t.availableSpec).filter(e=>t.availableSpec[e]>0||i.indexOf(e)!==-1)),r.sort();let a=``;return a=n?`Specialists Selected: ${Object.values(t.availableSpec||{}).reduce((e,t)=>e+t,0)} / ${t.specTotal}`:`Specialists Used: ${t.specTotal_used||0} / ${t.specTotal}`,(0,P.jsxs)(Na,{children:[(0,P.jsx)(Pa,{children:`Hyach Specialists`}),(0,P.jsxs)(Fa,{children:[r.map(e=>{let r=n&&(t.specCurrClass=e,t.canSelect()),a=n&&(t.specCurrClass=e,t.canUnselect()),o=!n&&(t.specCurrClass=e,t.canUse()),s=!n&&(t.specCurrClass=e,t.canDecrease());t.availableSpec&&t.availableSpec[e],t.currAllocatedSpec&&t.currAllocatedSpec[e];let c=`./img/systemicons/Specialistclasses/${e}.png?v=2`;return(0,P.jsxs)(Ia,{children:[(0,P.jsx)(La,{src:c,alt:e}),(0,P.jsxs)(Ra,{children:[e,i.indexOf(e)!==-1&&(0,P.jsx)(`span`,{style:{fontWeight:`normal`},children:` (repeat)`})]}),(0,P.jsx)(za,{children:n?(0,P.jsxs)(P.Fragment,{children:[(0,P.jsx)(Ba,{onClick:()=>this.handleUnselect(e),disabled:!a,children:(0,P.jsx)(`img`,{src:`./img/systemicons/Specialistclasses/iconMinus.png`,style:{width:`12px`,height:`12px`},alt:`Cancel`})}),(0,P.jsx)(Ba,{onClick:()=>this.handleSelect(e),disabled:!r,children:(0,P.jsx)(`img`,{src:`./img/systemicons/Specialistclasses/iconPlus.png`,style:{width:`12px`,height:`12px`},alt:`Use`})})]}):(0,P.jsxs)(P.Fragment,{children:[(0,P.jsx)(Ba,{onClick:()=>this.handleUse(e),disabled:!o,children:(0,P.jsx)(`img`,{src:`./img/systemicons/Specialistclasses/iconPlus.png`,style:{width:`12px`,height:`12px`},alt:`Use`})}),(0,P.jsx)(Ba,{onClick:()=>this.handleCancel(e),disabled:!s,children:(0,P.jsx)(`img`,{src:`./img/systemicons/Specialistclasses/iconMinus.png`,style:{width:`12px`,height:`12px`},alt:`Cancel`})})]})})]},e)}),r.length===0&&(0,P.jsx)(Ia,{style:{justifyContent:`center`,fontStyle:`italic`},children:`No Specialists Available`})]}),(0,P.jsx)(`div`,{style:{padding:`5px`,textAlign:`center`,fontSize:`10px`,color:`#f2f2f2`},children:a})]})}},Ua=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 250px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${N.colors.line};
`,Wa=M.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${N.colors.line};
    border-bottom: 1px solid ${N.colors.line};
    color: ${N.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;     
    font-weight: bold;
`,Ga=M.div`
    max-height: 250px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Ka=M.div`
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
`,qa=M.div`
    flex: 1;
    min-width: 80px;
    font-weight: normal; 
`,Ja=M.div`
    display: flex;
    align-items: center;     
    gap: 2px;
    margin-left: 10px;
`,Ya=M.input`
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
`,Xa=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    &.small {
        width: 18px;
    }
`;M.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #496791;
    margin: 0 6px;
    vertical-align: middle;
    opacity: 0.7;
`;var Za=M.div`
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
`,Qa=class extends _.Component{constructor(e){super(e),this.listRef=_.createRef(),this.state={shieldInputs:{}}}getShieldLabel(e){let t=e.startArc,n=e.endArc;if(t===void 0||n===void 0)return e.displayName;let r=(t+n)/2;t>n&&(r=(t+n+360)/2),r%=360;let i=``;return r>=337.5||r<22.5?i=`Front`:r>=22.5&&r<67.5?i=`Front Starboard`:r>=67.5&&r<112.5?i=`Starboard`:r>=112.5&&r<157.5?i=`Aft Starboard`:r>=157.5&&r<202.5?i=`Aft`:r>=202.5&&r<247.5?i=`Aft Port`:r>=247.5&&r<292.5?i=`Port`:r>=292.5&&r<337.5&&(i=`Front Port`),i?`${i} - ${e.displayName}`:e.displayName}getShieldSortPriority(e){return{Front:1,Port:2,"Front Port":3,Starboard:4,"Front Starboard":5,"Aft Port":6,"Aft Starboard":7,Aft:8}[this.getShieldLabel(e).split(` - `)[0]]||99}getGeneratorAndShields(){let{ship:e,system:t}=this.props,n=null,r=[],i=``,a=``;return t.name===`ThirdspaceShield`||t.name===`ThirdspaceShieldGenerator`?(i=`ThirdspaceShield`,a=`ThirdspaceShieldGenerator`):(t.name===`ThoughtShield`||t.name===`ThoughtShieldGenerator`)&&(i=`ThoughtShield`,a=`ThoughtShieldGenerator`),i?(e.systems&&(Array.isArray(e.systems)?e.systems:Object.values(e.systems)).forEach(e=>{e.name===a&&(n=e),e.name===i&&r.push(e)}),r.sort((e,t)=>{let n=this.getShieldSortPriority(e),r=this.getShieldSortPriority(t);return n===r?e.id-t.id:n-r}),{generator:n,shields:r,systemName:i}):{generator:null,shields:[]}}handleIncrease(e,t){e.canIncrease()&&(e.doIncrease(t),this.afterShieldChange(e))}handleDecrease(e,t){e.canDecrease()&&(e.doDecrease(t),this.afterShieldChange(e))}handleMin(e){e.canDecrease()&&(e.doMin(),this.afterShieldChange(e))}handleMax(e){e.canIncrease()&&(e.doMax(),this.afterShieldChange(e))}afterShieldChange(e){this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:e}),this.updateInputState(e)}handleInputChange(e,t){if(t===``){this.setState(t=>({shieldInputs:{...t.shieldInputs,[e.id]:``}}));return}let n=parseInt(t,10);if(isNaN(n))return;let r=n-e.currentHealth;r>0?this.handleIncrease(e,r):r<0&&this.handleDecrease(e,Math.abs(r))}updateInputState(e){this.setState(t=>({shieldInputs:{...t.shieldInputs,[e.id]:e.currentHealth}}))}handleWheel(e,t){e.preventDefault(),(e.deltaY<0?1:-1)>0?this.handleIncrease(t,1):this.handleDecrease(t,1)}handleMouseEnter(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){let t=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);t&&(t.showWeaponArc(this.props.ship,e),window.webglScene.requestRender())}}handleMouseLeave(e){if(window.webglScene&&window.webglScene.phaseDirector&&window.webglScene.phaseDirector.shipIconContainer){let e=window.webglScene.phaseDirector.shipIconContainer.getByShip(this.props.ship);e&&(e.hideWeaponArcs(),window.webglScene.requestRender())}}handleBoost(e){e&&(shipManager.power.clickPlus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:e}))}handleDeBoost(e){e&&(shipManager.power.clickMinus(this.props.ship,e),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:e}))}handleEqualise(e){e&&(e.doEqualise(),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:e}))}componentDidMount(){let{generator:e,shields:t}=this.getGeneratorAndShields(),n={};t.forEach(e=>n[e.id]=e.currentHealth),this.setState({shieldInputs:n}),window.webglScene}componentDidUpdate(e){let{generator:t,shields:n}=this.getGeneratorAndShields(),r=this.state.shieldInputs,i={},a=!1;n.forEach(e=>{r[e.id]!==e.currentHealth&&document.activeElement!==document.getElementById(`shield-input-${e.id}`)&&(i[e.id]=e.currentHealth,a=!0)}),a&&this.setState(e=>({shieldInputs:{...e.shieldInputs,...i}}))}render(){let{generator:e,shields:t,systemName:n}=this.getGeneratorAndShields();return e?(0,P.jsxs)(Ua,{children:[(0,P.jsx)(Wa,{children:n===`ThirdspaceShield`?`Thirdspace Shields`:`Thought Shields`}),(0,P.jsxs)(`div`,{style:{padding:`5px`,textAlign:`center`,fontSize:`11px`,color:`#deebff`,borderBottom:`1px solid #496791`},children:[`Unallocated Shield Energy: `,e.storedCapacity]}),n===`ThirdspaceShield`&&(0,P.jsxs)(`div`,{style:{padding:`5px`,textAlign:`center`,fontSize:`11px`,color:`#deebff`,borderBottom:`1px solid #496791`,display:`flex`,justifyContent:`center`,alignItems:`center`,gap:`5px`},children:[`Regeneration Rate: `,shipManager.systems.getOutputNoBoost(this.props.ship,e)+shipManager.power.getBoost(e)*t.length,(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleDeBoost(e),title:`Reduce Boost`,children:`-`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleBoost(e),title:`Boost Generator`,children:`+`})]}),(0,P.jsxs)(Ga,{ref:this.listRef,children:[t.map(e=>(0,P.jsxs)(Ka,{onMouseEnter:()=>this.handleMouseEnter(e),onMouseLeave:()=>this.handleMouseLeave(e),children:[(0,P.jsx)(qa,{children:this.getShieldLabel(e)}),(0,P.jsxs)(Ja,{children:[(0,P.jsx)(Xa,{onClick:()=>this.handleMin(e),disabled:!e.canDecrease(),title:`Drop shield to 0`,children:`Min`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleDecrease(e,25),disabled:!e.canDecrease(),children:`-25`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleDecrease(e,10),disabled:!e.canDecrease(),children:`-10`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleDecrease(e,5),disabled:!e.canDecrease(),children:`-5`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleDecrease(e,1),disabled:!e.canDecrease(),children:`-1`}),(0,P.jsx)(Ya,{id:`shield-input-${e.id}`,type:`number`,value:this.state.shieldInputs[e.id]===void 0?e.currentHealth:this.state.shieldInputs[e.id],onChange:t=>this.handleInputChange(e,t.target.value),onWheel:t=>this.handleWheel(t,e)}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleIncrease(e,1),disabled:!e.canIncrease(),children:`+1`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleIncrease(e,5),disabled:!e.canIncrease(),children:`+5`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleIncrease(e,10),disabled:!e.canIncrease(),children:`+10`}),(0,P.jsx)(Xa,{className:`small`,onClick:()=>this.handleIncrease(e,25),disabled:!e.canIncrease(),children:`+25`}),(0,P.jsx)(Xa,{onClick:()=>this.handleMax(e),disabled:!e.canIncrease(),title:`Raise shield to maximum`,children:`Max`})]})]},e.id)),t.length===0&&(0,P.jsx)(Ka,{children:`No Shields Found`})]}),e&&(0,P.jsx)(Za,{onClick:()=>this.handleEqualise(e),children:`Equalise Shields`})]}):(0,P.jsx)(`div`,{children:`No Generator Found`})}},$a=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95;
    background-color: rgba(16, 26, 38, 0.9);
    border: 1px solid ${N.colors.line};
`,eo=M.div`
    padding: 3px;
    background-color: #215a7a;
    border: 1px solid ${N.colors.line};
    border-bottom: 1px solid ${N.colors.line};
    color: ${N.colors.chromeText};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,to=M.div`
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
`,no=M.div`
    flex: 1;
`,ro=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
`,io=M.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${N.colors.textDim};
    border-bottom: 1px solid #496791;

    &:last-child {
        border-bottom: none;
    }

    b {
        color: #deebff;
        font-weight: normal;
    }
`,ao=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `}

    ${e=>e.$active&&e.$variant!==`activate`&&`
        background: #806c00;
        color: white;
        border: 1px solid #e6c300;
        opacity: 1;
    `}

    ${e=>e.$active&&e.$variant===`activate`&&`
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
`,oo=class extends _.Component{handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system}))}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system}))}handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system}))}canBoost(){let{ship:e,system:t}=this.props;return t.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,t)}canDeBoost(){let{ship:e,system:t}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(t)}canActivate(){return this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate()}render(){let{ship:e,system:t}=this.props,n=shipManager.power.getBoost(t),r=t.active;return(0,P.jsxs)($a,{children:[(0,P.jsx)(eo,{children:`Power Capacitor`}),t.boostable&&(0,P.jsxs)(to,{children:[(0,P.jsx)(no,{children:`Open Petals`}),(0,P.jsxs)(ro,{children:[(0,P.jsx)(ao,{onClick:()=>this.handleDeBoost(),disabled:!this.canDeBoost(),$active:n===0,children:`OFF`}),(0,P.jsx)(ao,{onClick:()=>this.handleBoost(),disabled:!this.canBoost(),$active:n>0,$variant:`activate`,children:`ON`})]})]}),(0,P.jsxs)(to,{children:[(0,P.jsx)(no,{children:`Double Recharge`}),(0,P.jsxs)(ro,{children:[(0,P.jsx)(ao,{onClick:()=>this.handleDeactivate(),disabled:!this.canDeactivate(),$active:!r,children:`OFF`}),(0,P.jsx)(ao,{onClick:()=>this.handleActivate(),disabled:!this.canActivate(),$active:r,$variant:`activate`,children:`ON`})]})]})]})}},so=M($a)`
    width: 100%;
    min-width: 190px;
    contain: inline-size;
`,co={surface:`rgba(32, 0, 32, 0.9)`,line:`#5d3564`,accent:`#7c4686`,hover:`rgba(75, 43, 81, 0.6)`,text:`#f2f2f2`,textDim:`#d8b9e6`},lo=M(so)`
    background-color: ${co.surface};
    border: 1px solid ${co.line};
`,uo=M.div`
    padding: 3px;
    background-color: ${co.line};
    border: 1px solid ${co.accent};
    color: ${co.text};
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    font-weight: bold;
`,fo=M.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px 8px;
    border-bottom: 1px solid ${co.line};
    font-size: 11px;
    color: ${co.text};

    &:hover {
        background-color: ${co.hover};
    }
`,po=M.div`
    padding: 2px 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: ${co.textDim};
`,mo=M.div`
    min-width: 24px;
    width: ${e=>e.$wide?`auto`:`24px`};
    padding: ${e=>e.$wide?`0 6px`:`0`};
    height: 18px;
    box-sizing: border-box;
    background: ${co.line};
    border: 1px solid ${co.accent};
    color: ${co.text};
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

    ${e=>e.$active&&`
        background: ${co.accent};
        border: 1px solid #a06daa;
        color: #ffffff;
        opacity: 1;
        cursor: default;
        &:hover { background: ${co.accent}; border: 1px solid #a06daa; }
    `}

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: ${co.textDim}; border: 1px solid ${co.accent}; }
    `}
`,ho=class extends _.Component{handleActivate(){this.props.system.canActivate()&&(this.props.system.doActivate(),this.forceUpdate())}handleDeactivate(){this.props.system.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate())}handlePower(e){let{ship:t,system:n}=this.props;if(!go(t,n))return;let r=n.getAbductionOrder();n.setAbductionPowerLevel(n.getAbductionPowerLevel(r)+e)&&(webglScene.customEvent(`SystemDataChanged`,{ship:n.getOwningUnit()||t,system:n}),this.forceUpdate())}handleCancel(){let{ship:e,system:t}=this.props;gamedata.gamephase===1&&gamedata.isMyShip(t.getOwningUnit()||e)&&(weaponManager.removeFiringOrder(t.getOwningUnit()||e,t),this.forceUpdate())}renderMaintain(){let{system:e}=this.props,t=e.isMaintainingVortex(),n=e.chargesVortexUpkeep()?e.getVortexUpkeepCost():0;return(0,P.jsxs)(_.Fragment,{children:[(0,P.jsx)(eo,{children:`Jump Point`}),(0,P.jsxs)(to,{children:[(0,P.jsx)(no,{children:`Maintain Vortex`}),(0,P.jsxs)(ro,{children:[(0,P.jsx)(ao,{onClick:()=>this.handleDeactivate(),disabled:!e.canDeactivate(),$active:!t,children:`OFF`}),(0,P.jsx)(ao,{onClick:()=>this.handleActivate(),disabled:!e.canActivate(),$active:t,$variant:`activate`,children:`ON`})]})]}),(0,P.jsx)(io,{children:n>0?t?`Held open. `+n+` power is drawn from the Power Capacitor at end of turn. No turn limit while it is paid.`:`Closes at end of turn unless maintained. Costs `+n+` power from the Power Capacitor on each turn it is used - no systems are shut down, and nothing is drawn on a turn it is idle.`:t?`Held open. All powered systems except the Scanner are shut down this turn.`:`Closes at end of turn unless maintained. Shuts down all powered systems except the Scanner.`})]})}renderAbduction(){let{ship:e,system:t}=this.props,n=t.getAbductionOrder(),r=gamedata.getShip(n.targetid),i=t.getAbductionPowerLevel(n),a=go(e,t),o=t.isExtraDimensional(),s=t.abductionMaxPower||1,c=window.JumpEngine.formatAbductionHalves(t.getAbductionHalves(n)),l=window.JumpEngine.getAbductionChain(n.targetid),u=window.JumpEngine.getAbductionCostPreview(n.targetid),d=t.isAbductionPowered(n),f;if(d)f=c+` power-turn`+(c===`1`?``:`s`)+` for `+t.getAbductionPowerDraw()+` power. So far `+window.JumpEngine.formatAbductionHalves(l.total)+` of `+l.cost+` power-turns.`;else{let e=!!r&&gamedata.isTerrain(r.shipSizeClass,r.userid),t=!!r&&((r.hexOffsets||[]).length>0||r.Huge>0);f=(e?`Targeting: takes hold if `+(t?`every hex it occupies is`:`it is`)+` inside your connected field and your OEW beats its DEW.`:`Targeting: takes hold if the target ends its move in your connected field and your OEW beats its DEW.`)+` Power can be applied from next turn`+(u===null?`.`:`, and `+u+` power-turns will abduct it.`)}return(0,P.jsxs)(lo,{children:[(0,P.jsxs)(uo,{children:[`Abduction`,r?`: `+r.name:``]}),d&&(0,P.jsxs)(fo,{children:[(0,P.jsx)(no,{children:o?`Power`:`Double power`}),o?(0,P.jsxs)(ro,{children:[(0,P.jsx)(mo,{onClick:()=>this.handlePower(-1),disabled:!a||i<=1,children:`-`}),(0,P.jsx)(mo,{$active:!0,children:`x`+i}),(0,P.jsx)(mo,{onClick:()=>this.handlePower(1),disabled:!a||i>=s,children:`+`})]}):null]}),gamedata.gamephase===1&&gamedata.isMyShip(t.getOwningUnit()||e)&&(0,P.jsxs)(fo,{children:[(0,P.jsx)(no,{children:`Declaration`}),(0,P.jsx)(ro,{children:(0,P.jsx)(mo,{$wide:!0,onClick:()=>this.handleCancel(),children:`CANCEL`})})]}),(0,P.jsx)(po,{children:f})]})}render(){let{system:e}=this.props,t=e.canMaintainVortex()||e.canDeactivate(),n=typeof e.getAbductionOrder==`function`&&!!e.getAbductionOrder();return(0,P.jsxs)(_.Fragment,{children:[t&&(0,P.jsx)(so,{children:this.renderMaintain()}),n&&this.renderAbduction()]})}},go=(e,t)=>gamedata.gamephase===1&&gamedata.isMyShip(t.getOwningUnit()||e)&&t.isExtraDimensional()&&t.isAbductionPowered(),_o={surface:`rgba(32, 0, 32, 0.9)`,line:`#5d3564`,accent:`#7c4686`,text:`#f2f2f2`},vo=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: ${e=>e.$isWeapon||e.$isPurple?_o.surface:`rgba(16, 26, 38, 0.9)`};
    border: 1px solid ${e=>e.$isWeapon?`#b43131`:e.$isPurple?_o.line:N.colors.line};
`,yo=M.div`
    padding: 3px;
    background-color: ${e=>e.$isWeapon?`#571616`:e.$isPurple?_o.line:`#215a7a`};
    border: 1px solid ${e=>e.$isWeapon?`#b43131`:e.$isPurple?_o.line:N.colors.line};
    border-bottom: 1px solid ${e=>e.$isWeapon?`#b43131`:e.$isPurple?_o.line:N.colors.line};
    color: ${e=>e.$isWeapon||e.$isPurple?_o.text:N.colors.chromeText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,bo=M.div`
    display: flex;
    align-items: center;
    padding: 1px 1px;
    border-bottom: 1px solid ${e=>e.$isPurple?_o.line:`#496791`};
    font-size: 12px;
    color: ${e=>e.$isPurple?_o.text:`#deebff`};
    justify-content: center;

    &:last-child {
        border-bottom: none;
    }

`;M.div`
    flex: 1;
    margin-right: 10px;    
`;var xo=M.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    width: 100%;
    padding: 2px;
`,So=M.div`
    flex: 1;
    height: 18px;
    background: ${e=>e.$isPurple?_o.line:`#203348`};
    border: 1px solid ${e=>e.$isPurple?_o.accent:`#496791`};
    color: ${e=>e.$isPurple?_o.text:`#deebff`};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${e=>e.$isPurple?`#5e3666`:`#496791`};
        border: 1px solid ${e=>e.$isPurple?`#9a5aa6`:`#5d82b6ff`};
        color: #ffffff;
        opacity: 1;
    }

    ${e=>e.disabled&&(e.$isPurple?`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; border: 1px solid ${_o.accent}; }
    `:`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #203348; color: #deebff; }
    `)}

    ${e=>(e.$active||e.$variant===`activate`&&e.$isWeapon)&&e.$variant===`activate`&&!e.$isWeapon&&`
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
    ${e=>e.$variant===`activate`&&e.$isWeapon&&!(e.$isToggle&&!e.$active)&&`
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

        ${e.$active?`
            background: #b35900;
            border: 1px solid #ffb74d;
            box-shadow: 0 0 5px #ff9800;
        `:``}
    `}

    ${e=>e.$active&&e.$variant===`deactivate`&&`
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
`,Co=class extends _.Component{handleActivate(){this.canActivate()&&(this.props.system.doActivate(),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system}))}handleActivateAll(e){e.preventDefault();let{ship:t,system:n}=this.props,r=[];r=t.flight?t.systems.map(e=>e.systems).reduce((e,t)=>e.concat(t),[]):t.systems;let i=!1;r.forEach(e=>{e.name&&e.name===n.name&&e.canActivate&&typeof e.canActivate==`function`&&e.canActivate()&&(e.doActivate(),i=!0)}),i&&(this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:t,system:n}))}handleDeactivate(){this.canDeactivate()&&(this.props.system.doDeactivate(),this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system}))}handleDeactivateAll(e){e.preventDefault();let{ship:t,system:n}=this.props,r=[];r=t.flight?t.systems.map(e=>e.systems).reduce((e,t)=>e.concat(t),[]):t.systems;let i=!1;r.forEach(e=>{e.name&&e.name===n.name&&e.canDeactivate&&typeof e.canDeactivate==`function`&&e.canDeactivate()&&(e.doDeactivate(),i=!0)}),i&&(this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:t,system:n}))}canActivate(){return this.props.system.canActivate&&typeof this.props.system.canActivate==`function`&&this.props.system.canActivate()}canDeactivate(){return this.props.system.canDeactivate&&typeof this.props.system.canDeactivate==`function`&&this.props.system.canDeactivate()}render(){let{ship:e,system:t}=this.props,n=t.active||t.weapon&&!t.activationIsToggle&&weaponManager.hasFiringOrder(e,t),r=typeof t.getActivateLabel==`function`?t.getActivateLabel():null,i=typeof t.getDeactivateLabel==`function`?t.getDeactivateLabel():null,a=r||(t.weapon?`Fire`:`Activate`),o=i||(t.weapon?`Don't Fire`:`Deactivate`),s=!!t.singleActivationButton,c=!s||this.canActivate(),l=(!t.weapon||t.activationIsToggle)&&(!s||this.canDeactivate()),u=!!t.activationMenuPurple&&!t.weapon;return(0,P.jsxs)(vo,{$isWeapon:t.weapon,$isPurple:u,children:[(0,P.jsx)(yo,{$isWeapon:t.weapon,$isPurple:u,children:t.displayName}),(0,P.jsx)(bo,{$isPurple:u,children:(0,P.jsxs)(xo,{children:[c&&(0,P.jsx)(So,{onClick:()=>this.handleActivate(),onContextMenu:e=>this.handleActivateAll(e),disabled:!this.canActivate(),$active:n,$variant:`activate`,$isWeapon:t.weapon,$isToggle:!!t.activationIsToggle,$isPurple:u,children:a}),l&&(0,P.jsx)(So,{onClick:()=>this.handleDeactivate(),onContextMenu:e=>this.handleDeactivateAll(e),disabled:!this.canDeactivate(),$active:!n,$variant:`deactivate`,$isPurple:u,children:o})]})})]})}},wo=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 160px;
    opacity: 0.95 !important;
    background-color: rgba(24, 20, 6, 0.9);
    border: 1px solid #8d7e40;
`,To=M.div`
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
`,Eo=M.div`
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
`,Do=M.div`
    flex: 1;
    padding-left: 8px;
    padding-right: 8px;
`,Oo=M.div`
    display: flex;
    align-items: center;     
    gap: 5px;
    padding: 2px;
`,ko=M.div`
    min-width: 20px;
    text-align: center;
    font-size: 11px;
    font-weight: bold;
`,Ao=M.div`
    width: ${e=>e.$narrow?`18px`:`30px`};
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #3d2e10; color: #fff8d6; }
    `}

    ${e=>e.$active&&e.$variant===`activate`&&`
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

    ${e=>e.$active&&e.$variant===`deactivate`&&`
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

    ${e=>e.$active&&e.$variant===`warning`&&`
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

    ${e=>e.$active&&e.$variant===`risk`&&`
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

    ${e=>e.$active&&e.$variant===`info`&&`
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
`,jo=class extends _.Component{handleOnline(){this.canOnline()&&(shipManager.power.onOnlineClicked(this.props.ship,this.props.system),this.handleUpdate())}handleOnlineAll(e){e.preventDefault(),this.canOnline()&&(shipManager.power.onlineAll(this.props.ship,this.props.system),this.handleUpdate())}handleOffline(){if(this.canOffline()){let{ship:e,system:t}=this.props;for(;shipManager.power.getBoost(t)>0;)shipManager.power.clickMinus(e,t);shipManager.power.onOfflineClicked(e,t),this.handleUpdate()}}handleOfflineAll(e){if(e.preventDefault(),this.canOffline()){let{ship:e,system:t}=this.props;for(;shipManager.power.getBoost(t)>0;)shipManager.power.clickMinus(e,t);shipManager.power.offlineAll(e,t),this.handleUpdate()}}handleBoost(){this.canBoost()&&(shipManager.power.clickPlus(this.props.ship,this.props.system),this.handleUpdate())}handleDeBoost(){this.canDeBoost()&&(shipManager.power.clickMinus(this.props.ship,this.props.system),this.handleUpdate())}handleOverload(){this.canOverload()&&(shipManager.power.onOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleStopOverload(){this.canStopOverload()&&(shipManager.power.onStopOverloadClicked(this.props.ship,this.props.system),this.handleUpdate())}handleUpdate(){this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:this.props.ship,system:this.props.system})}canOffline(){let{ship:e,system:t}=this.props;return gamedata.gamephase===1&&(t.canOffLine||t.powerReq>0||window.officers&&officers.mayShunt(e,t))&&!t.powerLocked&&!shipManager.power.isOffline(e,t)&&!weaponManager.hasFiringOrder(e,t)}canOnline(){let{ship:e,system:t}=this.props;return gamedata.gamephase===1&&shipManager.power.isOffline(e,t)&&!shipManager.power.isForcedOffline(e,t)&&!shipManager.power.isVortexLockedOffline(e,t)}canBoost(){let{ship:e,system:t}=this.props;return t.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,t)&&(!t.isScanner()||t.id==shipManager.power.getHighestSensorsId(e))&&t.name!==`ThirdspaceShieldGenerator`&&t.name!==`powerCapacitor`&&t.name!==`PowerCapacitor`}canDeBoost(){let{ship:e,system:t}=this.props;return gamedata.gamephase===1&&!!shipManager.power.getBoost(t)&&t.name!==`ThirdspaceShieldGenerator`&&t.name!==`powerCapacitor`&&t.name!==`PowerCapacitor`}canOverload(){let{ship:e,system:t}=this.props;return gamedata.gamephase===1&&!shipManager.power.isOffline(e,t)&&t.weapon&&t.overloadable&&!shipManager.power.isOverloading(e,t)}canStopOverload(){let{ship:e,system:t}=this.props;return gamedata.gamephase===1&&t.weapon&&t.overloadable&&shipManager.power.isOverloading(e,t)&&(t.overloadshots>=t.extraoverloadshots||t.overloadshots==0)}render(){let{ship:e,system:t}=this.props,n=this.canOffline()||this.canOnline(),r=t.boostable&&(this.canBoost()||this.canDeBoost()),i=t.overloadable&&(this.canOverload()||this.canStopOverload());if(!n&&!r&&!i)return null;let a=shipManager.power.isOffline(e,t),o=shipManager.power.getBoost(t),s=shipManager.power.isOverloading(e,t),c=t.name===`reactor`,l=t.name===`jumpEngine`,u=l&&typeof t.getChargeBoostMax==`function`&&t.getChargeBoostMax()>0,d=`Boost Level`;return c&&(d=`Self-Destruct`),l&&(d=u?`Extra Charging`:`Jump to Hyperspace`),(0,P.jsxs)(wo,{children:[(0,P.jsx)(To,{children:`Power Settings`}),n&&(0,P.jsxs)(Eo,{children:[(0,P.jsx)(Do,{children:`Power`}),(0,P.jsxs)(Oo,{children:[(0,P.jsx)(Ao,{onClick:()=>this.handleOnline(),onContextMenu:e=>this.handleOnlineAll(e),disabled:!this.canOnline(),$active:!a,$variant:`activate`,children:`On`}),(0,P.jsx)(Ao,{onClick:()=>this.handleOffline(),onContextMenu:e=>this.handleOfflineAll(e),disabled:!this.canOffline(),$active:a,$variant:`deactivate`,children:`Off`})]})]}),r&&(0,P.jsxs)(Eo,{children:[(0,P.jsx)(Do,{children:d}),c||l&&!u?(0,P.jsxs)(Oo,{children:[(0,P.jsx)(Ao,{onClick:()=>this.handleBoost(),disabled:o>0||!this.canBoost(),$active:o>0,$variant:c?`deactivate`:`risk`,children:`Yes`}),(0,P.jsx)(Ao,{onClick:()=>this.handleDeBoost(),disabled:o===0||!this.canDeBoost(),$active:o===0,$variant:`activate`,children:`No`})]}):(0,P.jsxs)(Oo,{children:[(0,P.jsx)(Ao,{onClick:()=>this.handleDeBoost(),$narrow:!0,children:`-`}),(0,P.jsx)(ko,{children:o}),(0,P.jsx)(Ao,{onClick:()=>this.handleBoost(),$narrow:!0,children:`+`})]})]}),i&&(0,P.jsxs)(Eo,{children:[(0,P.jsx)(Do,{children:`Overcharge`}),(0,P.jsxs)(Oo,{children:[(0,P.jsx)(Ao,{onClick:()=>this.handleOverload(),disabled:!this.canOverload(),$active:s,$variant:`warning`,children:`Yes`}),(0,P.jsx)(Ao,{onClick:()=>this.handleStopOverload(),disabled:!this.canStopOverload(),$active:!s,$variant:`deactivate`,children:`No`})]})]})]})}},Mo=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 0px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,No=M.div`
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
`,Po=M.div`
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
`,Fo=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; }
    `}
`,Io=M.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`,Lo=M.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,Ro=M.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,zo=M.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,Bo=M.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,Vo=M.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,Ho=M.div`
    width: 20px;
    text-align: center;
`,Uo=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`;M.span`
    display: inline-block;
    width: 1px;
    height: 10px;
    background-color: #f2f2f2;
    margin: 0 4px;
    font-weight: bold;     
    vertical-align: middle;
    opacity: 0.7;
`;var Wo=class extends _.Component{constructor(e){super(e),this.listRef=_.createRef()}handleIncrease(e){let{system:t}=this.props;t.setCurrShipType(e),t.canIncrease()&&(t.doIncrease(),this.forceUpdate())}handleDecrease(e){let{system:t}=this.props;t.setCurrShipType(e),t.canDecrease()&&(t.doDecrease(),this.forceUpdate())}handlePropagate(e){let{ship:t,system:n}=this.props,r=window.gamedata,i=window.shipManager,a=window.webglScene,o=!!(n.hasMultiTarget&&n.hasMultiTarget()),s=o?n.getCurrWeaponId():null,c=o?n.getMineWeapons():[],l=o?c.find(e=>String(e.id)===String(s)):null;if(o&&!l)return;n.setCurrShipType(e);let u=n.rangeSetting||n.range,d=o?n.allocatedRanges[s]:n.allocatedRanges;if(!d)return;let f=d[e]===null||d[e]===void 0?u:d[e];var p=[];for(var m in r.ships){var h=r.ships[m];if(h.userid==t.userid&&!i.isDestroyed(h)){if(h.phpclass&&t.phpclass){if(h.phpclass!=t.phpclass)continue}else if(h.shipClass!=t.shipClass)continue;for(var g in h.systems){var _=h.systems[g];_&&_.name===n.name&&!!(_.hasMultiTarget&&_.hasMultiTarget())===o&&p.push({unit:h,ctrl:_})}}}for(var v=0;v<p.length;v++){var _=p[v].ctrl;if(o){_.ensureMultiAllocatedShape&&_.ensureMultiAllocatedShape();var y=_.getMineWeapons().find(e=>e.displayName===l.displayName&&e.indexInGroup===l.indexInGroup);if(!y)continue;_.setCurrWeaponId(y.id)}_.setCurrShipType(e);let t=0,n=()=>o?_.allocatedRanges[_.getCurrWeaponId()]:_.allocatedRanges,r=()=>_.range||_.rangeSetting,i=()=>{let t=n();return t?t[e]??r():r()};for(;i()<f&&_.canIncrease()&&t<100;)_.doIncrease(),t++;for(;i()>f&&_.canDecrease()&&t<100;)_.doDecrease(),t++;t>=100&&console.warn(`Mine Settings Propagation safety break for`,_)}a.customEvent(`SystemDataChanged`,{ship:t,system:n})}cycleWeapon(e){let{system:t}=this.props;if(!t.hasMultiTarget||!t.hasMultiTarget())return;let n=t.getMineWeapons();if(n.length===0)return;let r=t.getCurrWeaponId(),i=n.findIndex(e=>String(e.id)===String(r));i<0&&(i=0);let a=(i+e+n.length)%n.length;t.setCurrWeaponId(n[a].id),this.forceUpdate()}render(){let{system:e}=this.props;if(!e||(e.range=e.range||e.rangeSetting,!e.range))return null;let t=!!(e.hasMultiTarget&&e.hasMultiTarget()),n=[],r=null,i=null,a=e.allocatedRanges||{};t?(e.ensureMultiAllocatedShape&&e.ensureMultiAllocatedShape(),n=e.getMineWeapons(),r=e.getCurrWeaponId(),i=n.find(e=>String(e.id)===String(r))||n[0]||null,a=r!=null&&e.allocatedRanges[r]?e.allocatedRanges[r]:{}):(e.ensureFlatAllocatedShape&&e.ensureFlatAllocatedShape(),a=e.allocatedRanges||{});let o=Object.keys(a),s=e.validTargets||o,c=t=>s.includes(t)?a[t]??e.range:`N/A`,l=t=>s.includes(t)?c(t)<e.range:!1,u=e=>s.includes(e)?c(e)>0:!1,d=e=>!!s.includes(e),f=t&&n.length>0?(0,P.jsxs)(Po,{children:[(0,P.jsx)(Fo,{onClick:()=>this.cycleWeapon(-1),disabled:n.length<2,title:`Previous weapon`,children:`<`}),(0,P.jsx)(Io,{children:i?i.label:``}),(0,P.jsx)(Fo,{onClick:()=>this.cycleWeapon(1),disabled:n.length<2,title:`Next weapon`,children:`>`})]}):(0,P.jsx)(No,{children:`Set Mine Range`});return(0,P.jsxs)(Mo,{children:[f,(0,P.jsxs)(Lo,{ref:this.listRef,children:[o.map(e=>(0,P.jsxs)(Ro,{children:[(0,P.jsx)(zo,{src:`./img/systemicons/BFCPclasses/${e}.png`,alt:e}),(0,P.jsx)(Bo,{children:e}),(0,P.jsxs)(Vo,{onWheel:t=>{t.deltaY<0&&l(e)?this.handleIncrease(e):t.deltaY>0&&u(e)&&this.handleDecrease(e)},children:[(0,P.jsx)(Uo,{onClick:()=>this.handleDecrease(e),disabled:!u(e),children:`-`}),(0,P.jsx)(Ho,{children:c(e)}),(0,P.jsx)(Uo,{onClick:()=>this.handleIncrease(e),disabled:!l(e),children:`+`}),(0,P.jsx)(Uo,{title:t?`Propagate this weapon's settings to same-class mines with Multiple Targets`:`Propagate to all mines of same type`,onClick:()=>this.handlePropagate(e),disabled:!d(e),style:{marginLeft:`5px`},children:(0,P.jsx)(`img`,{src:`./img/systemicons/BFCPclasses/minePropagate.png`,alt:`Propagate`,style:{width:`12px`,height:`12px`}})})]})]},e)),o.length===0&&(0,P.jsx)(Ro,{children:`No ship types available`})]}),(0,P.jsxs)(`div`,{style:{padding:`5px`,textAlign:`center`,fontSize:`10px`,color:`#f2f2f2`},children:[`Max Range: `,e.range]})]})}},Go=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,Ko=M.div`
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
`,L=M.div`
    max-height: 200px;
    overflow-y: auto;
    display: block;
    padding: 0;
`,R=M.div`
    display: flex;
    align-items: center;
    padding: 3px 5px;
    border-bottom: 1px solid #b43131;
    font-size: 11px;
    color: #f2f2f2;

    &:hover {
        background-color: rgba(32, 0, 32, 0.6);
    }
`,qo=M.img`
    width: 20px;
    height: 20px;
    margin-right: 8px;
`,Jo=M.div`
    flex: 1;
    font-weight: normal;
    margin-right: 25px;     
`,Yo=M.div`
    display: flex;
    align-items: center;
    gap: 2px;
`,Xo=M.div`
    width: 30px;
    text-align: center;
    font-weight: bold;
    color: ${e=>e.$active?`#4CAF50`:`#F44336`};
`,Zo=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #4b2b51; color: #d8b9e6; }
    `}
`,Qo=class extends _.Component{constructor(e){super(e),this.listRef=_.createRef()}handleToggle(e){let{system:t}=this.props;t.setCurrShipType(e),t.canSet()?(t.doSet(),this.forceUpdate()):t.canUnset()&&(t.doUnset(),this.forceUpdate())}handlePropagate(e){let{ship:t,system:n}=this.props,r=window.gamedata,i=window.shipManager,a=window.webglScene;console.log(`Propagating Mine settings for:`,e),n.setCurrShipType(e);let o=n.allocatedShipTypes[e];var s=[];for(var c in r.ships){var l=r.ships[c];if(l.userid==t.userid&&!i.isDestroyed(l))for(var u=0;u<l.systems.length;u++){var d=l.systems[u];if(l.shipClass==t.shipClass&&d.name===n.name){s.push(d);break}}}console.log(`Found Mine Weapons of same type:`,s.length);for(var f=0;f<s.length;f++){var d=s[f];d.setCurrShipType(e);let t=0;for(;d.allocatedShipTypes[e]!==o&&(o?d.canSet():d.canUnset())&&t<10;)o?d.doSet():d.doUnset(),t++;t>=10&&console.warn(`Mine Settings Propagation safety break for`,d)}a.customEvent(`SystemDataChanged`,{ship:t,system:n})}render(){let{system:e}=this.props;if(!e)return null;let t=e.allocatedShipTypes||{},n=Object.keys(t),r=e=>t[e]?`YES`:`NO`,i=e=>{let t=Number(this.props.ship.spawned),n=t===-1?1:t+1;return window.gamedata.turn===n},a=e=>!0;return(0,P.jsxs)(Go,{children:[(0,P.jsx)(Ko,{children:`Set Target Types`}),(0,P.jsxs)(L,{ref:this.listRef,children:[n.map(e=>(0,P.jsxs)(R,{children:[(0,P.jsx)(qo,{src:`./img/systemicons/BFCPclasses/${e}.png`,alt:e}),(0,P.jsx)(Jo,{children:e}),(0,P.jsxs)(Yo,{children:[(0,P.jsx)(Xo,{$active:t[e],children:r(e)}),(0,P.jsx)(Zo,{onClick:()=>this.handleToggle(e),disabled:!i(e),style:{marginLeft:`5px`},children:`Toggle`}),(0,P.jsx)(Zo,{title:`Propagate to all mines of same type`,onClick:()=>this.handlePropagate(e),disabled:!a(e),style:{marginLeft:`5px`,width:`16px`,padding:`0`},children:(0,P.jsx)(`img`,{src:`./img/systemicons/BFCPclasses/minePropagate.png`,alt:`Propagate`,style:{width:`12px`,height:`12px`}})})]})]},e)),n.length===0&&(0,P.jsx)(R,{children:`No ship types available`})]})]})}},$o=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 1px;
    width: 100%;
    min-width: 180px;
    opacity: 0.95 !important;
    background-color: ${N.colors.greenBg};
    border: 1px solid ${N.colors.greenLine};
`,es=M.div`
    padding: 3px;
    background-color: ${N.colors.greenTitleBg};
    border: 1px solid ${N.colors.greenLine};
    color: ${N.colors.greenText};
    text-align: center;
    font-size: 11px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,ts=M.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 3px;
    background-color: ${N.colors.greenTitleBg};
    border: 1px solid ${N.colors.greenLine};
    color: ${N.colors.greenText};
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 2px;
    min-width: 220px;
`,ns=M.div`
    width: 20px;
    height: 18px;
    background: ${N.colors.greenBtnBg};
    border: 1px solid ${N.colors.greenBtnLine};
    color: ${N.colors.greenText};
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;

    &:hover {
        background: ${N.colors.greenBtnLine};
        border: 1px solid ${N.colors.greenBtnLineHover};
        color: #ffffff;
    }

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${N.colors.greenBtnBg}; border: 1px solid ${N.colors.greenBtnLine}; color: ${N.colors.greenText}; }
    `}
`,rs=M.div`
    flex: 1;
    text-align: center;
    padding: 0 6px;
`;M.div`
    text-align: center;
    color: ${N.colors.greenLabel};
    font-size: 10px;
    padding: 2px 4px 0 4px;
`;var is=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    padding: 3px;
`,as=M.div`
    width: 70px;
    font-size: 10px;
    color: ${N.colors.greenLabel};
    user-select: none;
`,os=M.div`
    flex: 1;
    height: 20px;
    background: ${N.colors.greenBtnBg};
    border: 1px solid ${N.colors.greenBtnLine};
    color: ${N.colors.greenText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 11px;
    padding: 0 4px;
    opacity: 0.95;
    user-select: none;

    &:hover {
        background: ${N.colors.greenBtnLine};
        border: 1px solid ${N.colors.greenBtnLineHover};
        color: #ffffff;
        opacity: 1;
    }

    ${e=>e.$active&&`
        background: ${N.colors.greenBtnLine};
        border: 1px solid ${N.colors.greenBtnLineHover};
        box-shadow: 0 0 5px ${N.colors.greenGlow};
        color: #ffffff;
        opacity: 1;
    `}

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${N.colors.greenBtnBg}; border: 1px solid ${N.colors.greenBtnLine}; color: ${N.colors.greenText}; }
    `}
`,ss=class extends _.Component{refresh(){let{ship:e,system:t}=this.props;this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:e,system:t})}cycleMode(e){let{system:t}=this.props;if(!gamedata.isMyShip(this.props.ship))return;let n=t.firingMode==1?2:1;t.setFiringMode(n),typeof t.initializationUpdate==`function`&&t.initializationUpdate(),this.refresh()}activateMode1(){let{ship:e,system:t}=this.props;t.canActivate()&&(t.doActivate(),webglScene.customEvent(`SystemDataChanged`,{ship:e,system:t}),webglScene.customEvent(`CloseSystemInfo`))}targetWarrior(){let{ship:e,system:t}=this.props;weaponManager.isSelectedWeapon(t)||weaponManager.selectWeapon(e,t),webglScene.customEvent(`SystemDataChanged`,{ship:e,system:t}),webglScene.customEvent(`CloseSystemInfo`)}setRotation(e,t){let{system:n}=this.props;n.rotationDirection=e,n.rotationAmount=t,typeof n.updateRotationNotes==`function`&&n.updateRotationNotes(),this.refresh()}renderInitialOrders(){let{system:e}=this.props,t=parseInt(e.firingMode,10),n=e.firingModes[t]||``;return(0,P.jsxs)($o,{children:[(0,P.jsxs)(ts,{children:[(0,P.jsx)(ns,{onClick:()=>this.cycleMode(-1),title:`Previous mode`,children:`<`}),(0,P.jsx)(rs,{children:n}),(0,P.jsx)(ns,{onClick:()=>this.cycleMode(1),title:`Next mode`,children:`>`})]}),t==1&&(0,P.jsx)(is,{children:(0,P.jsx)(os,{onClick:()=>this.activateMode1(),children:`Activate`})}),t==2&&(0,P.jsx)(is,{children:(0,P.jsx)(os,{onClick:()=>this.targetWarrior(),$active:weaponManager.isSelectedWeapon(e),children:`Target friendly Warrior`})})]})}engageMode3(){let{ship:e,system:t}=this.props;gamedata.isMyShip(e)&&(weaponManager.hasFiringOrder(e,t)||(t.setFiringMode(3),typeof t.initializationUpdate==`function`&&t.initializationUpdate(),this.refresh()))}renderPreFiring(){let{system:e}=this.props;if(e.firingMode!=3)return(0,P.jsxs)($o,{children:[(0,P.jsx)(es,{children:`Gravitic Augmenter`}),(0,P.jsx)(is,{children:(0,P.jsx)(os,{onClick:()=>this.engageMode3(),children:`Engage Gravity Shifting`})})]});let t=e.rotationDirection||1,n=e.rotationAmount||1,r=(e,r)=>t==e&&n==r;return(0,P.jsxs)($o,{children:[(0,P.jsx)(es,{children:`Gravity Shift Settings`}),(0,P.jsxs)(is,{children:[(0,P.jsx)(as,{children:`Clockwise`}),(0,P.jsx)(os,{onClick:()=>this.setRotation(1,1),$active:r(1,1),children:`60°`}),(0,P.jsx)(os,{onClick:()=>this.setRotation(1,2),$active:r(1,2),children:`120°`})]}),(0,P.jsxs)(is,{children:[(0,P.jsx)(as,{children:`Anti-Clockwise`}),(0,P.jsx)(os,{onClick:()=>this.setRotation(2,1),$active:r(2,1),children:`60°`}),(0,P.jsx)(os,{onClick:()=>this.setRotation(2,2),$active:r(2,2),children:`120°`})]})]})}render(){let{system:e}=this.props;return e?gamedata.gamephase==1?(e.firingMode==3&&(e.setFiringMode(1),typeof e.initializationUpdate==`function`&&e.initializationUpdate()),this.renderInitialOrders()):gamedata.gamephase==5?this.renderPreFiring():null:null}},cs=3,ls=M.div`
    display: flex;
    flex-direction: column;
    margin-top: 5px;
    width: 100%;
    min-width: 200px;
    box-sizing: border-box;
    opacity: 0.95;
    background-color: rgba(32, 0, 32, 0.9);
    border: 1px solid #b43131;
`,us=M.div`
    padding: 3px;
    background-color: #180606;
    border: 1px solid #b43131;
    color: #f2f2f2;
    text-align: center;
    font-size: 12px;
    margin-bottom: 2px;
    opacity: 1 !important;
    font-weight: bold;
`,ds=M.div`
    text-align: center;
    color: ${e=>e.$empty?`#f0a0a0`:`#f2f2f2`};
    font-size: 10px;
    padding: 2px 4px 3px 4px;
    user-select: none;
`,fs=M.div`
    display: flex;
    align-items: center;
    gap: 4px;
    width: 100%;
    box-sizing: border-box;
    padding: 3px 5px;
`,ps=M.div`
    flex: 1;
    min-width: 0;
    font-size: 11px;
    color: #f2f2f2;
    user-select: none;
`,ms=M.div`
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

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: #683333; border: 1px solid #641b1b; color: #f2f2f2; }
    `}
`,hs=M.input`
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
`;M.div`
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
`;var gs=[{key:`hitBoost5`,label:`Hit Chance`,increment:5,prefix:`+`,suffix:`%`,display:e=>e*2,parse:e=>e/2},{key:`shotBoost`,label:`Shots`,increment:1,prefix:`+`,suffix:``,display:e=>e,parse:e=>e},{key:`dmgBoost5`,label:`Damage`,increment:5,prefix:`+`,suffix:``,display:e=>e,parse:e=>e}],_s=(e,t)=>e.prefix+e.display(t|0)+e.suffix,vs=class extends _.Component{fieldSteps(e,t){return(t|0)/e.increment}allocatedSteps(e){return gs.reduce((t,n)=>t+this.fieldSteps(n,e[n.key]),0)}maxSteps(){let{ship:e,system:t}=this.props;return typeof t.getMaxSteps==`function`?t.getMaxSteps(e):Math.floor(shipManager.movement.getRemainingEngineThrust(e)/cs)}totalShots(e){return(e.guns|0)+(e.shotBoost|0)}maxDamageSteps(e){return this.totalShots(e)}clampDamageToShots(e){let t=gs.find(e=>e.key===`dmgBoost5`),n=this.maxDamageSteps(e)*t.increment;(e.dmgBoost5|0)>n&&(e.dmgBoost5=n)}refresh(){let{ship:e,system:t}=this.props;this.forceUpdate(),webglScene.customEvent(`SystemDataChanged`,{ship:e,system:t})}setValue(e,t){let{ship:n,system:r}=this.props;if(!gamedata.isMyShip(n))return;let i=Math.max(0,Math.round((t|0)/e.increment)*e.increment),a=this.allocatedSteps(r)-this.fieldSteps(e,r[e.key]),o=Math.max(0,this.maxSteps()-a);if(i/e.increment>o&&(i=o*e.increment),e.key===`dmgBoost5`){let t=this.maxDamageSteps(r)*e.increment;i>t&&(i=t)}r[e.key]=i,e.key===`shotBoost`&&this.clampDamageToShots(r),typeof r.updateBoostNotes==`function`&&r.updateBoostNotes(),this.syncFlight(r),this.refresh()}syncFlight(e){let t=this.getFlightPulsars();for(let n=0;n<t.length;n++){let r=t[n];r!==e&&(gs.forEach(t=>{r[t.key]=e[t.key]|0}),typeof r.updateBoostNotes==`function`&&r.updateBoostNotes())}}step(e,t){let{system:n}=this.props;this.setValue(e,(n[e.key]|0)+t*e.increment)}onWheel(e,t){t.preventDefault(),this.step(e,t.deltaY<0?1:-1)}onInput(e,t){let n=String(t.target.value).replace(/[^0-9]/g,``),r=n===``?0:parseInt(n,10);this.setValue(e,e.parse(r))}propagate(){let{ship:e,system:t}=this.props;if(!gamedata.isMyShip(e))return;let n=this.getFlightPulsars();for(let e=0;e<n.length;e++){let r=n[e];r!==t&&(gs.forEach(e=>{r[e.key]=t[e.key]|0}),this.clampWeaponToBudget(r),typeof r.updateBoostNotes==`function`&&r.updateBoostNotes())}this.refresh()}getFlightPulsars(){let{ship:e}=this.props,t=[],n=e&&e.systems?e.systems:[];for(let e=0;e<n.length;e++){let r=n[e]&&n[e].systems?n[e].systems:[];for(let e=0;e<r.length;e++)r[e]&&r[e].name===`MinorThoughtPulsar`&&t.push(r[e])}return t}clampWeaponToBudget(e){let t=[`dmgBoost5`,`shotBoost`,`hitBoost5`],n=e=>gs.find(t=>t.key===e),r=0;for(;r++<200&&!(gs.reduce((t,n)=>t+(e[n.key]|0)/n.increment,0)<=this.maxSteps());)for(let r=0;r<t.length;r++){let i=t[r];if((e[i]|0)>0){e[i]=(e[i]|0)-n(i).increment;break}}}render(){let{ship:e,system:t}=this.props;if(!t)return null;let n=typeof t.getSpareThrust==`function`?t.getSpareThrust(e):shipManager.movement.getRemainingEngineThrust(e),r=this.allocatedSteps(t)*cs,i=Math.max(0,n-r),a=i>=cs;return(0,P.jsxs)(ls,{children:[(0,P.jsx)(us,{children:`Minor Thought Pulsar`}),(0,P.jsxs)(ds,{$empty:i<cs,children:[`Available thrust: `,i]}),gs.map(e=>{let n=t[e.key]|0,r=e.key===`dmgBoost5`&&n/e.increment>=this.maxDamageSteps(t);return(0,P.jsxs)(fs,{children:[(0,P.jsx)(ps,{children:e.label}),(0,P.jsx)(ms,{title:`Less`,disabled:n<=0,onClick:()=>this.step(e,-1),children:`−`}),(0,P.jsx)(hs,{type:`text`,value:_s(e,n),onChange:t=>this.onInput(e,t),onWheel:t=>this.onWheel(e,t)}),(0,P.jsx)(ms,{title:r?`One +5 per shot (add shots for more)`:`More`,disabled:!a||r,onClick:()=>this.step(e,1),children:`+`})]},e.key)})]})}},ys=e=>{let t=null,n=t=>{t.preventDefault(),e(t)};return e=>{t!==e&&(t&&t.removeEventListener(`wheel`,n,{passive:!1}),t=e,t&&t.addEventListener(`wheel`,n,{passive:!1}))}},z={bg:`linear-gradient(180deg, rgb(27, 45, 62), rgb(18, 32, 45))`,line:`#2a6b8f`,radius:`6px`,shadow:`0 8px 28px rgba(0, 0, 0, 0.6)`,titleBg:`linear-gradient(180deg, rgb(54, 79, 110), rgb(36, 57, 80))`,title:`#c6e2ff`,text:`#deebff`,dim:`#8ca5c0`,btnBg:`#081420`,btnText:`#deebff`,well:`#000000`,focus:`#8bcaf2`},B={enh:{rail:N.colors.enhLine,bar:`#c39a52`,wash:N.colors.enhBg,title:N.colors.enhTitle,btnBg:`#292114`,btnText:N.colors.enhTitle},damage:{rail:`#2f7f92`,bar:`#4cb8d0`,wash:`rgba(58, 159, 181, 0.26)`,title:`#e0f5fa`,btnBg:`#0f262d`,btnText:`#b6e3ee`},crit:{rail:`#a85c33`,bar:`#dd7643`,wash:`rgba(168, 92, 51, 0.34)`,title:`#ffe8dc`,btnBg:`#291914`,btnText:`#eab99e`},officer:{rail:`#5d79a8`,bar:`#90b1ee`,wash:`rgba(144, 177, 238, 0.24)`,title:`#ffffff`,btnBg:`#141b29`,btnText:`#b8cdf2`}},bs={rail:z.line,bar:z.line,btnBg:z.btnBg,btnText:z.btnText},xs=e=>e.$ink||bs,Ss=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px 4px 10px;
    font-size: 11px;
    color: ${e=>e.$gold?N.colors.enhText:z.text};
`,Cs=M.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,ws=M.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`;M.span`
    flex: 0 0 auto;
    color: ${e=>e.$gold?N.colors.enhText:z.dim};
    font-size: 10px;
    opacity: ${e=>e.$gold?.75:1};
`;var Ts=M.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${e=>xs(e).btnBg};
    border: 1px solid ${e=>xs(e).rail};
    border-radius: 2px;
    color: ${e=>xs(e).btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${e=>xs(e).rail};
        color: #ffffff;
        opacity: 1;
    }

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover {
            background: ${xs(e).btnBg};
            color: ${xs(e).btnText};
        }
    `}
`,Es=M.input`
    flex: 0 0 44px;
    width: 44px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${N.fonts.mono};
    font-size: 12px;
    color: ${e=>e.$destroyed?`#ff8a80`:`#ffffff`};
    background-color: ${z.well};
    border: 1px solid ${e=>xs(e).rail};
    border-radius: 2px;
    outline: none;

    &:focus {
        border-color: ${e=>xs(e).btnText};
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`,Ds=M.div`
    padding: 6px 10px 5px;
    background: ${z.titleBg};
    border-bottom: 1px solid ${z.line};
    color: ${z.title};
    text-align: left;
    font-family: ${N.fonts.display};
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    user-select: none;
    ${e=>e.$sticky?`position: sticky; top: 0; z-index: 1;`:``}
`,Os=e=>`
    color: ${e.title};
    background-color: ${e.wash};
    background-image: linear-gradient(to right, ${e.wash}, rgba(0, 0, 0, 0) 75%);
    border-left: 3px solid ${e.bar};
    border-top: 1px solid ${e.rail};
    border-bottom: 1px solid ${e.rail};
`,ks=M.div`
    padding: 5px 10px 4px 7px;
    text-align: left;
    font-family: ${N.fonts.display};
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
`,As=M.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here may
      ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
    box-shadow: inset 3px 0 0 ${e=>xs(e).bar};
`,js=M(ks)`
    ${Os(B.enh)}
`,Ms=M(ks)`
    ${Os(B.officer)}
`,Ns=M(ks)`
    ${Os(B.damage)}
`,Ps=M(ks)`
    ${Os(B.crit)}
`,Fs=M.div`
    height: 2px;
    background-color: ${e=>e.$chrome?z.line:N.colors.enhLine};
    opacity: 0.8;
`,Is=M.div`
    display: flex;
    flex-direction: column;
    /*The menus above are shrink-to-fit tooltips capped with a max-width; nothing in here
      may ask to be wider than the menu it sits in.*/
    min-width: 0;
    max-width: 100%;
    box-sizing: border-box;
`,Ls=Ps,Rs=M.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${N.colors.warningSoft};
    user-select: none;

    /* An effect dialled down to nothing is not carried any more, but its row stays so it
       can be put back — dimmed so it never reads as an active critical. */
    ${e=>e.$empty&&`
        color: #6f6257;
    `}
`,zs=M.div`
    flex: 1;
    min-width: 0;
    /*"Damage reduction reduced by" and friends wrap inside the menu rather than widening
      it - the menus are shrink-to-fit and capped.*/
    overflow-wrap: anywhere;
`,Bs=M.span`
    margin-left: 4px;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${z.dim};
`,Vs=M.div`
    flex: 0 0 auto;
    color: ${z.dim};
`,Hs=M.div`
    display: flex;
    align-items: center;
    gap: 4px;
    flex: 0 0 auto;
`,Us=M.div`
    flex: 0 0 20px;
    text-align: center;
    font-family: ${N.fonts.mono};
    font-size: 11px;
    color: ${e=>e.$empty?`#6f6257`:`#ffffff`};
`,Ws=M.input`
    margin: 0;
    width: 12px;
    height: 12px;
    flex: 0 0 12px;
    cursor: pointer;

    &[type='checkbox'] {
        position: relative;
        top: 0;
    }
`,Gs=M.span`
    flex: 0 0 auto;
    line-height: 1;
    margin-top: 2px;
`,Ks=M.div`
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px 6px 10px;
`,qs=M.select`
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
    color: ${z.text};
    /*the lobby's own input fill (.lb-input), not the number well*/
    background-color: ${z.btnBg};
    /*Takes the section's ink like the tickers above it - it is the widest control in the
      section, so leaving it on the chassis border was the one thing that still read as
      unpainted once the tickers went rust.*/
    border: 1px solid ${B.crit.rail};
    border-radius: 2px;
    outline: none;
    cursor: pointer;

    &:focus { border-color: ${B.crit.btnText}; }
    &:disabled { cursor: default; }
`;M.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    font-size: 9px;
    letter-spacing: 0.3px;
    color: ${z.dim};
    cursor: pointer;
    user-select: none;
`;var Js=(e,t,n,r)=>{let i=[];for(let a in e||{}){if(!e.hasOwnProperty(a))continue;let o=battleDamage.PARAM_CRITICALS[a],s=parseInt((r||{})[a],10)||0;i.push({type:a,isParam:!!o,paramLabel:o?o.label:null,label:battleDamage.critLabel(a,t,s),count:parseInt(e[a],10)||0,param:s,transient:!!(n&&n[a])})}return i},Ys=class extends _.Component{constructor(e){super(e),this.wheelRefs={},this.state={showAll:!1}}componentDidMount(){this.fetchCatalogue()}componentDidUpdate(e){e.ship!==this.props.ship&&this.fetchCatalogue()}fetchCatalogue(){this.props.editable&&battleDamage.loadCatalogue(this.props.ship,()=>this.forceUpdate())}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=ys(t=>this.step(e,t.deltaY<0?1:-1))),this.wheelRefs[e]}critMap(){let e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.c?Object.assign({},e.c):{}}paramMap(){let e=battleDamage.getEntry(this.props.ship,this.props.kind,this.props.reference);return e&&e.p?Object.assign({},e.p):{}}valueOf(e){return e.isParam?e.param:e.count}maxValueOf(e){if(!e.isParam)return battleDamage.critLimit(e.type);let t=battleDamage.PARAM_CRITICALS[e.type];return Math.min(battleDamage.MAX_CRIT_PARAM,t&&t.max||battleDamage.MAX_CRIT_PARAM)}setValue(e,t){let{ship:n,kind:r,reference:i,onChange:a}=this.props,o=this.critMap(),s=this.paramMap();t>0?e.isParam?(o[e.type]=1,s[e.type]=Math.min(t,this.maxValueOf(e))):o[e.type]=Math.min(t,battleDamage.critLimit(e.type)):(delete o[e.type],delete s[e.type]),battleDamage.setCriticals(n,r,i,o,s),a&&a()}step(e,t){let n=this.rowForType(e);this.setValue(n,this.valueOf(n)+t)}rowForType(e){let{ship:t}=this.props,n=battleDamage.PARAM_CRITICALS[e],r=parseInt(this.paramMap()[e],10)||0;return{type:e,isParam:!!n,paramLabel:n?n.label:null,label:battleDamage.critLabel(e,t.preBattleCritDesc,r),count:parseInt(this.critMap()[e],10)||0,param:r,transient:!!(t.preBattleCritTransient&&t.preBattleCritTransient[e])}}displayRows(e){let{ship:t,kind:n,reference:r}=this.props;return battleDamage.rememberCriticals(t,n,r,(e||[]).map(e=>e.type)).map(e=>this.rowForType(e))}addableTypes(e){let{ship:t,kind:n,reference:r}=this.props,i=battleDamage.offerableCriticals(t,n,r,this.state.showAll),a={};return e.forEach(e=>{a[e]=!0}),i.filter(e=>!a[e]).map(e=>({type:e,label:this.pickerLabel(e)})).sort((e,t)=>e.label.localeCompare(t.label))}pickerLabel(e){let t=battleDamage.PARAM_CRITICALS[e];return t?t.label:battleDamage.critLabel(e,this.props.ship.preBattleCritDesc,0)}onAdd(e){e&&this.setValue(this.rowForType(e),1)}render(){let{ship:e,rows:t,editable:n}=this.props,r=n?this.displayRows(t):t||[],i=!!(n&&battleDamage.catalogueFor(e)),a=i?this.addableTypes(r.map(e=>e.type)):[];return!r.length&&!i?null:(0,P.jsxs)(Is,{children:[(0,P.jsx)(Fs,{$chrome:!0}),(0,P.jsx)(Ls,{children:`Critical Effects`}),(0,P.jsxs)(As,{$ink:B.crit,children:[r.map(e=>{let t=this.valueOf(e),r=this.maxValueOf(e);return(0,P.jsxs)(Rs,{$empty:n&&t<=0,children:[(0,P.jsxs)(zs,{title:e.type,children:[n&&e.isParam?e.paramLabel:e.label,e.transient&&(0,P.jsx)(Bs,{children:`(turn 1 only)`})]}),n?(0,P.jsxs)(Hs,{children:[(0,P.jsx)(Ts,{$ink:B.crit,title:e.isParam?`Reduce`:`One fewer`,disabled:t<=0,onClick:()=>this.step(e.type,-1),children:`−`}),(0,P.jsx)(Us,{$empty:t<=0,ref:this.wheelRef(e.type),children:t}),(0,P.jsx)(Ts,{$ink:B.crit,title:e.isParam?`Increase`:t>=r&&r===1?`This effect only applies once`:`One more`,disabled:t>=r,onClick:()=>this.step(e.type,1),children:`+`})]}):e.count>1&&(0,P.jsxs)(Vs,{children:[`(x`,e.count,`)`]})]},e.type)}),i&&(0,P.jsx)(Ks,{children:(0,P.jsxs)(qs,{value:``,disabled:a.length===0,title:`Add a critical effect to this unit before the battle`,onChange:e=>this.onAdd(e.target.value),children:[(0,P.jsx)(`option`,{value:``,children:a.length?`+ Add effect…`:`Nothing to add`}),a.map(e=>(0,P.jsx)(`option`,{value:e.type,children:e.label},e.type))]})})]})]})}},Xs=M.div`
    display: flex;
    justify-content: flex-end;
    padding: 1px 8px 5px 10px;
    font-family: ${N.fonts.mono};
    font-size: 10px;
    color: ${e=>e.$color||N.colors.enhText};
    opacity: 0.85;
    user-select: none;
`,Zs=B.officer.btnText,Qs=M(Xs)`
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding-top: 2px;
    opacity: 1;
`,$s=M.span`
    margin-left: auto;
    opacity: 0.85;
`,ec=M.div`
    flex: 0 0 auto;
    height: 16px;
    padding: 0 8px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    background: ${B.officer.btnBg};
    border: 1px solid ${B.officer.rail};
    border-radius: 2px;
    color: ${B.officer.btnText};
    font-family: ${N.fonts.mono};
    font-size: 10px;
    cursor: pointer;
    user-select: none;

    &:hover {
        background: ${B.officer.rail};
        color: #ffffff;
    }

    ${e=>e.$disabled&&`
        opacity: 0.35;
        cursor: not-allowed;
        &:hover {
            background: ${B.officer.btnBg};
            color: ${B.officer.btnText};
        }
    `}
`,tc=M.span`
    flex: 0 0 auto;
    min-width: 34px;
    text-align: right;
    font-family: ${N.fonts.mono};
    font-size: 10px;
    color: ${e=>e.$color||N.colors.enhTitle};
    /*Nothing left to buy: the column has stopped quoting a price and is reporting a spend,
      so it stops looking like a price.*/
    opacity: ${e=>e.$spent?.6:1};
`,nc=class extends _.Component{constructor(e){super(e),this.wheelRef=ys(e=>this.step(e.deltaY<0?1:-1))}step(e){let{row:t,onChange:n}=this.props;if(t.fixed)return;let r=Math.max(0,Math.min(t.max,t.count+e));r!==t.count&&n(t.enhID,r)}onInput(e){let{row:t,onChange:n}=this.props,r=String(e.target.value).replace(/[^0-9]/g,``),i=r===``?0:parseInt(r,10);n(t.enhID,Math.max(0,Math.min(t.max,i)))}render(){let{row:e}=this.props,t=e.count>=e.max,n=(e.max>1?`${e.label} - ${e.count}/${e.max} levels`+(e.count>0?`, ${e.price} pts spent`:``)+(t?``:`; next level ${e.nextPrice} pts`):`${e.label} - ${e.price||e.nextPrice} pts`)+(e.note?`\n${e.note}`:``)+(e.blocked?`\n${e.blocked}`:``),r=!!e.blocked&&e.count<1,i=e.officer?B.officer:B.enh,a=!!e.fixed,o=`${e.label} - bought with the ship, in its Officers section (Edit the ship to remove him). Move posts him elsewhere.`;return(0,P.jsxs)(Ss,{$gold:!e.officer,title:a?o:n,style:r?{opacity:.55}:void 0,children:[(0,P.jsx)(Cs,{children:(0,P.jsx)(ws,{children:e.label})}),(0,P.jsx)(Ts,{$ink:i,title:a?o:`Remove a level`,disabled:a||e.count<=0,onClick:()=>{a||this.step(-1)},children:`−`}),(0,P.jsx)(Es,{ref:this.wheelRef,$ink:i,type:`text`,value:e.count,readOnly:a,onChange:e=>{a||this.onInput(e)}}),(0,P.jsx)(Ts,{$ink:i,title:a?o:r?e.blocked:e.count>=e.max?`Already at the maximum`:`Add a level (${e.nextPrice} pts)`,disabled:a||r||e.count>=e.max,onClick:()=>{a||this.step(1)},children:`+`}),(0,P.jsx)(tc,{$spent:t,$color:e.officer?i.title:void 0,title:t?`Fully bought - ${e.price} pts`:`Cost of the next level`,children:t?`${e.price}p`:`${e.nextPrice}p`})]})}},rc=M(ec)`
    ${e=>e.$on&&`
        background: ${B.officer.bar};
        color: #0b1520;
        cursor: default;
        &:hover {
            background: ${B.officer.bar};
            color: #0b1520;
        }
    `}
`,ic=M.div`
    flex: 0 0 auto;
    display: flex;
    gap: 2px;
`,ac=class extends _.Component{renderSides(e){let{onSide:t}=this.props;return typeof t==`function`?(0,P.jsx)(ic,{children:e.sides.map(n=>(0,P.jsx)(rc,{$on:n.current,title:n.current?`The ${e.label} covers the ${n.name} weapons`:`Cover the ${n.name} weapons instead (free in Fleet Selection)`,onClick:r=>{r.stopPropagation(),n.current||t(e.enhID)},children:n.name},n.id))}):null}renderMove(e){let{onMove:t}=this.props;if(e&&Array.isArray(e.sides))return this.renderSides(e);if(!e||typeof t!=`function`)return null;let n=e.moveTo.length>0,r=n?`Move the ${e.label} to another post: click Move, then a highlighted system in the ship window (free in Fleet Selection)`:`There is no other post the ${e.label} can take on this ship right now`;return(0,P.jsx)(ec,{$disabled:!n,title:r,onClick:r=>{r.stopPropagation(),n&&t(e.enhID)},children:`Move`})}render(){let{rows:e,onChange:t}=this.props;if(!e||e.length===0)return null;let n=e.filter(e=>!e.officer),r=e.filter(e=>e.officer),i=e=>e.reduce((e,t)=>e+(t.count>0?t.price:0),0),a=i(n),o=i(r),s=r.find(e=>e.count>0&&(Array.isArray(e.moveTo)||Array.isArray(e.sides)));return(0,P.jsxs)(_.Fragment,{children:[n.length>0&&(0,P.jsx)(js,{children:`✦ ENHANCEMENTS`}),n.length>0&&(0,P.jsxs)(As,{$ink:B.enh,children:[n.map(e=>(0,P.jsx)(nc,{row:e,onChange:t},e.enhID)),a>0&&(0,P.jsxs)(Xs,{children:[`Refits: `,a,` pts`]})]}),r.length>0&&(0,P.jsx)(Ms,{children:`Officers`}),r.length>0&&(0,P.jsxs)(As,{$ink:B.officer,children:[r.map(e=>(0,P.jsx)(nc,{row:e,onChange:t},e.enhID)),s?(0,P.jsxs)(Qs,{$color:Zs,children:[this.renderMove(s),o>0&&(0,P.jsxs)($s,{children:[`Officers: `,o,` pts`]})]}):o>0&&(0,P.jsxs)(Xs,{$color:Zs,children:[`Officers: `,o,` pts`]})]})]})}},oc=M.div`
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
    background: ${z.bg};
    border: 1px solid ${z.line};
    border-radius: ${z.radius};
    box-shadow: ${z.shadow};
    overflow: hidden;
`,sc=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 8px 5px 10px;
    font-size: 11px;
    color: ${z.text};
`,cc=M.div`
    flex: 1;
    min-width: 0;
    user-select: none;
    display: flex;
    align-items: baseline;
    gap: 4px;
`,lc=M.label`
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 0 0 auto;
    cursor: ${e=>e.$disabled?`not-allowed`:`pointer`};
    user-select: none;
    opacity: ${e=>e.$disabled?.4:1};
    color: ${e=>e.$on?`#ff8a80`:z.dim};
`,uc=M.span`
    flex: 0 0 auto;
    color: ${z.dim};
    font-size: 10px;
`,dc=M.span`
    flex: 1 1 auto;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
`,fc=class extends _.Component{constructor(e){super(e),this.wheelRef=ys(e=>this.step(e.deltaY<0?1:-1))}entry(){let{ship:e,system:t}=this.props;return battleDamage.getEntry(e,battleDamage.KIND_SYSTEM,t.id)||{}}remaining(){let{system:e}=this.props,t=this.entry();return t.k?0:Math.max(0,e.maxhealth-(parseInt(t.d,10)||0))}isDestroyed(){return!!this.entry().k}isIndestructible(){return battleDamage.isIndestructible(this.props.system)}floor(){return+!!this.isIndestructible()}setRemaining(e){let{ship:t,system:n}=this.props,r=n.maxhealth,i=Math.min(this.floor(),r),a=parseInt(e,10);isNaN(a)&&(a=r),a=Math.max(i,Math.min(r,a));let o=r-a,s=a===0&&r>0;s&&this.rememberHealth(),battleDamage.setSystem(t,n.id,{d:o,k:+!!s}),this.refresh()}rememberHealth(){let{ship:e,system:t}=this.props;this.isDestroyed()||battleDamage.rememberHealth(e,battleDamage.KIND_SYSTEM,t.id,this.remaining())}setDestroyed(e){let{ship:t,system:n}=this.props;if(e&&this.isIndestructible())return;if(e){this.rememberHealth(),battleDamage.setSystem(t,n.id,{d:n.maxhealth,k:1}),this.refresh();return}let r=battleDamage.healthMemory(t,battleDamage.KIND_SYSTEM,n.id),i=r>0?Math.min(n.maxhealth,r):n.maxhealth;battleDamage.setSystem(t,n.id,{d:n.maxhealth-i,k:0}),this.refresh()}refresh(){let{ship:e}=this.props;battleDamage.applyToShip(e);let t=[];if(window.systemEnhancements){let n=parseFloat(e.pointCostSysEnh)||0;t=systemEnhancements.dropDestroyed(e),t.length&&this.settleRefitCost(e,n)}if(window.officers&&typeof officers.dropDestroyedPosts==`function`){let n=officers.dropDestroyedPosts(e);n.length&&(t=t.concat(n),window.gamedata&&typeof gamedata.calculateFleet==`function`&&gamedata.calculateFleet())}window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow==`function`&&gamedata.refreshFleetRow(e),this.forceUpdate(),t.length&&window.confirm&&typeof confirm.warning==`function`&&confirm.warning(systemEnhancements.describeRemoved(t))}settleRefitCost(e,t){let n=parseFloat(e.pointCostSysEnh)||0;e.pointCost=(parseFloat(e.pointCost)||0)-(t-n),window.gamedata&&typeof gamedata.calculateFleet==`function`&&gamedata.calculateFleet()}setEnhancement(e,t){let{ship:n,system:r}=this.props;if(!window.systemEnhancements)return;let i=parseFloat(n.pointCostSysEnh)||0,a=systemEnhancements.taken(n,r.id,e);if(t!==a){if(systemEnhancements.set(n,r.id,e,t),this.settleRefitCost(n,i),window.gamedata&&typeof gamedata.canAffordRefit==`function`&&!gamedata.canAffordRefit(n)){let t=parseFloat(n.pointCostSysEnh)||0;systemEnhancements.set(n,r.id,e,a),this.settleRefitCost(n,t),systemEnhancements.apply(n),this.forceUpdate(),window.confirm&&typeof confirm.error==`function`&&confirm.error(`You cannot afford that enhancement!`,function(){})}else systemEnhancements.apply(n),this.refresh()}}startMove(e){let{ship:t,system:n}=this.props;if(!window.officers)return;let r=officers.lobbyEntryAt(t,n.id,e);r&&officers.lobbyDestinations(t,r).length!==0&&(officers.startPick(t,r),window.uiEvents&&window.uiEvents.relay(`CloseSystemInfo`))}switchSide(e){let{ship:t,system:n}=this.props;if(!window.officers)return;let r=officers.lobbyEntryAt(t,n.id,e);r&&officers.lobbySwitchSide(t,r)&&this.forceUpdate()}step(e){this.setRemaining(this.remaining()+e)}onInput(e){let t=String(e.target.value).replace(/[^0-9]/g,``);this.setRemaining(t===``?0:parseInt(t,10))}render(){let{ship:e,system:t}=this.props;if(!t||!(t.maxhealth>0))return null;let n=this.remaining(),r=this.isDestroyed(),i=this.isIndestructible(),a=this.entry(),o=Js(a.c,e.preBattleCritDesc,e.preBattleCritTransient,a.p),s=window.systemEnhancements&&!r?systemEnhancements.menuRowsFor(e,t):[];return(0,P.jsxs)(oc,{onClick:e=>e.stopPropagation(),children:[(0,P.jsx)(ac,{rows:s,onChange:(e,t)=>this.setEnhancement(e,t),onMove:e=>this.startMove(e),onSide:e=>this.switchSide(e)}),s.length>0&&(0,P.jsx)(Fs,{}),(0,P.jsx)(Ns,{children:`Damage`}),(0,P.jsx)(As,{$ink:B.damage,children:(0,P.jsxs)(sc,{children:[(0,P.jsxs)(cc,{title:`${t.displayName||t.name} (system id ${t.id})`,children:[(0,P.jsx)(dc,{children:t.displayName||t.name}),(0,P.jsxs)(uc,{children:[`#`,t.id]})]}),(0,P.jsx)(Ts,{$ink:B.damage,title:i&&n<=1?`A reactor cannot be destroyed before the battle`:`More damage`,disabled:r||n<=this.floor(),onClick:()=>this.step(-1),children:`−`}),(0,P.jsx)(Es,{ref:this.wheelRef,$ink:B.damage,type:`text`,$destroyed:r,disabled:r,value:r?0:n,onChange:e=>this.onInput(e)}),(0,P.jsx)(Ts,{$ink:B.damage,title:`Repair`,disabled:r||n>=t.maxhealth,onClick:()=>this.step(1),children:`+`}),(0,P.jsxs)(lc,{$on:r,$disabled:i,title:i?`A reactor cannot be destroyed before the battle: losing it destroys the primary structure, which destroys the ship`:`Mark this system destroyed before the battle starts`,children:[(0,P.jsx)(Ws,{type:`checkbox`,checked:r,disabled:i,onChange:e=>this.setDestroyed(e.target.checked)}),(0,P.jsx)(Gs,{children:`Destroy`})]})]})}),(0,P.jsx)(Ys,{ship:e,kind:battleDamage.KIND_SYSTEM,reference:t.id,rows:o,editable:!0,onChange:()=>this.refresh()})]})}},pc=B.officer,mc=M.div`
    display: flex;
    flex-direction: column;
    width: 100%;
    min-width: 190px;
    contain: inline-size;
    background-color: rgba(16, 26, 38, 0.92);
    border: 1px solid ${pc.rail};
`,hc=M.div`
    padding: 3px;
    background: ${pc.wash};
    border-left: 3px solid ${pc.bar};
    border-bottom: 1px solid ${pc.rail};
    color: ${pc.title};
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 2px;
`,gc=M.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    padding: 3px 8px;
    font-size: 11px;
    color: #deebff;
`,_c=M.div`
    padding: 0 8px 5px 8px;
    font-size: 10px;
    line-height: 1.35;
    color: #8ca5c0;
    border-bottom: 1px solid ${pc.rail};

    &:last-child {
        border-bottom: none;
    }
`,vc=M.div`
    padding: 0 6px;
    height: 18px;
    box-sizing: border-box;
    background: ${pc.btnBg};
    border: 1px solid ${pc.rail};
    color: ${pc.btnText};
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 10px;
    white-space: nowrap;
    user-select: none;

    &:hover {
        background: ${pc.bar};
        color: #0b1520;
    }
`,yc=M.div`
    display: flex;
    gap: 2px;
`,bc=M(vc)`
    ${e=>e.$on&&`
        background: ${pc.bar};
        color: #0b1520;
        cursor: default;
    `}
`,xc=class extends _.Component{move(e,t){e.stopPropagation(),e.preventDefault(),officers.startPick(this.props.ship,t),webglScene.customEvent(`CloseSystemInfo`)}cancel(e,t){e.stopPropagation(),e.preventDefault(),officers.cancelTransfer(this.props.ship,t)}side(e,t,n,r){e.stopPropagation(),e.preventDefault(),n.current?r&&officers.cancelTransfer(this.props.ship,t):r||officers.setTransfer(this.props.ship,t,t.post)}renderSides(e,t,n,r,i){let a=t.find(e=>!e.current);return(0,P.jsxs)(_.Fragment,{children:[(0,P.jsxs)(gc,{children:[(0,P.jsx)(`span`,{children:officers.label(e.id,this.props.ship)}),(0,P.jsx)(yc,{children:t.map(t=>{let r=n?!t.current:t.current;return(0,P.jsx)(bc,{$on:r,onClick:r=>this.side(r,e,t,n),title:r?`He covers the ${t.name} weapons`:`Cover the ${t.name} weapons instead`,children:t.name},t.id)})})]}),(0,P.jsx)(_c,{children:n?`Switching to ${a.name}: he covers nothing for the rest of this turn, and the ${a.name} weapons from turn ${i}.`:`Switching sides takes the rest of the turn: he covers nothing until the next.`})]},`${e.id}-${r}`)}render(){let{ship:e,system:t}=this.props,n=officers.currentTurn()+1;return(0,P.jsxs)(mc,{children:[(0,P.jsx)(hc,{children:`Officers`}),officers.movableOn(e,t.id).map((t,r)=>{let i=officers.transferOf(e,t),a=officers.sideChoices(e,t);return a?this.renderSides(t,a,i,r,n):(0,P.jsxs)(_.Fragment,{children:[(0,P.jsxs)(gc,{children:[(0,P.jsx)(`span`,{children:officers.label(t.id,e)}),i?(0,P.jsx)(vc,{title:`Keep him at this post`,onClick:e=>this.cancel(e,t),children:`Cancel Move`}):(0,P.jsx)(vc,{title:`Then click the post to move him to`,onClick:e=>this.move(e,t),children:`Move Officer`})]}),(0,P.jsx)(_c,{children:i?`To ${officers.systemName(e,i.to)}: he serves nowhere for the rest of this turn, and there from turn ${n}.`:`An officer who moves serves nowhere for the rest of the turn.`})]},`${t.id}-${r}`)})]})}},Sc=M.div`
    display: flex;
    flex-direction: column;
    width: fit-content;
`,Cc=M.div`
    display: flex;
    flex-wrap: wrap;
`,wc=M.div`
	display: flex;
    width: 30px;
    height: 30px;
    background-image: url(${e=>e.img});
	background-size: cover;
	align-items: center;
    justify-content: center;
    mix-blend-mode: ${e=>e.$blend||`normal`};
    ${Hn}

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
`,Tc=M.span`
    color: #ffd27a;
    font-size: 16px;
    line-height: 1;
    text-shadow: black 0 0 3px, black 0 0 3px;
    pointer-events: none;
`,Ec=M(Tc)`
    color: #9fe3ff;
`,Dc=class extends _.Component{constructor(e){super(e)}online(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;shipManager.power.onOnlineClicked(t,n),webglScene.customEvent(`CloseSystemInfo`)}offline(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;Xc(t,n)&&(shipManager.power.onOfflineClicked(t,n),webglScene.customEvent(`CloseSystemInfo`))}allOnline(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;shipManager.power.onlineAll(t,n),webglScene.customEvent(`CloseSystemInfo`)}allOffline(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;Xc(t,n)&&(shipManager.power.offlineAll(t,n),webglScene.customEvent(`CloseSystemInfo`))}overload(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;shipManager.power.onOverloadClicked(t,n),webglScene.customEvent(`CloseSystemInfo`)}stopOverload(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;shipManager.power.onStopOverloadClicked(t,n),webglScene.customEvent(`CloseSystemInfo`)}boost(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;shipManager.power.clickPlus(t,n)}deboost(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;shipManager.power.clickMinus(t,n)}addShots(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;rl(t,n)&&weaponManager.changeShots(t,n,1)}reduceShots(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;il(t,n)&&weaponManager.changeShots(t,n,-1)}removeFireOrderMulti(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;ol(t,n)&&weaponManager.removeFiringOrderMulti(t,n)}removeFireOrder(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;sl(t,n)&&(weaponManager.removeFiringOrder(t,n),webglScene.customEvent(`CloseSystemInfo`))}removeFireOrderAll(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;sl(t,n)&&(weaponManager.removeFiringOrderAll(t,n),webglScene.customEvent(`CloseSystemInfo`))}allChangeFiringMode(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;if(ll(t,n)){weaponManager.onModeClicked(t,n);var r=n.firingMode,i=[];i=t.flight?t.systems.map(e=>e.systems).reduce((e,t)=>e.concat(t),[]).filter(e=>e.weapon):t.systems.filter(e=>e.weapon);for(var a=weaponManager.stripPairingSuffix(n.displayName),o=[],s=0;s<i.length;s++)a===weaponManager.stripPairingSuffix(i[s].displayName)&&n.weapon&&o.push(i[s]);for(var s=0;s<o.length;s++){var c=o[s];if(c.firingMode!=r&&ll(t,c)){for(var l=c.firingMode,u=0;c.firingMode!=r&&u<2;)weaponManager.onModeClicked(t,c),c.firingMode==1&&u++;if(c.firingMode!=r)for(;c.firingMode!=l;)weaponManager.onModeClicked(t,c)}}}}changeFiringMode(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;ll(t,n)&&weaponManager.onModeClicked(t,n)}selectAllWeapons(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;weaponManager.selectAllWeapons(t,n,`forceSelect`),webglScene.customEvent(`CloseSystemInfo`)}deselectAllWeapons(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;weaponManager.selectAllWeapons(t,n,`forceDeselect`),webglScene.customEvent(`CloseSystemInfo`)}declareSelfIntercept(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;if(ul(t,n)){if(weaponManager.onDeclareSelfInterceptSingle(t,n),n.canSplitShots)var r=n.checkFinished();r&&webglScene.customEvent(`CloseSystemInfo`)}}declareSelfInterceptAll(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;if(weaponManager.onDeclareSelfInterceptSingleAll(t,n),n.canSplitShots)var r=n.checkFinished();r&&webglScene.customEvent(`CloseSystemInfo`)}remSelfIntercept(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;dl(t,n)&&weaponManager.removeSelfInterceptSingle(t,n)}declareMeteorDefence(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;pl(t,n)&&(weaponManager.onDeclareMeteorDefence(t,n),webglScene.customEvent(`CloseSystemInfo`))}declareMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;weaponManager.onDeclareMeteorDefenceAll(t,n),webglScene.customEvent(`CloseSystemInfo`)}remMeteorDefence(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;ml(t,n)&&(weaponManager.removeMeteorDefence(t,n),webglScene.customEvent(`CloseSystemInfo`))}remMeteorDefenceAll(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;weaponManager.removeMeteorDefenceAll(t,n),webglScene.customEvent(`CloseSystemInfo`)}declareStormDefence(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;hl(t,n)&&(emStorm.declare(t,n),webglScene.customEvent(`CloseSystemInfo`))}declareStormDefenceAll(e){e.stopPropagation(),e.preventDefault();let{ship:t}=this.props;emStorm.declareAll(t),webglScene.customEvent(`CloseSystemInfo`)}remStormDefence(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;gl(t,n)&&(emStorm.withdraw(t,n),webglScene.customEvent(`CloseSystemInfo`))}remStormDefenceAll(e){e.stopPropagation(),e.preventDefault();let{ship:t}=this.props;emStorm.withdrawAll(t),webglScene.customEvent(`CloseSystemInfo`)}nextCurrClass(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;n.nextCurrClass(),webglScene.customEvent(`SystemDataChanged`,{ship:t,system:n})}prevCurrClass(e){e.stopPropagation(),e.preventDefault();let{ship:t,system:n}=this.props;n.prevCurrClass(),webglScene.customEvent(`SystemDataChanged`,{ship:t,system:n})}render(){let{ship:e,selectedShip:t,system:n}=this.props;return Yc(e,n)?gamedata.gamephase===-2?(0,P.jsx)(Sc,{children:(0,P.jsx)(fc,{ship:e,system:n})}):(0,P.jsxs)(Sc,{children:[xl(e,n)&&(0,P.jsx)(jo,{ship:e,system:n}),(0,P.jsxs)(Cc,{children:[rl(e,n)&&(0,P.jsx)(wc,{title:`More shots`,onClick:this.addShots.bind(this),img:`./img/plussquare.png`}),il(e,n)&&(0,P.jsx)(wc,{title:`Less shots`,onClick:this.reduceShots.bind(this),img:`./img/minussquare.png`})]}),fl(e,n)&&(0,P.jsxs)(Ni,{ship:e,system:n,showModes:!!ll(e,n),children:[ul(e,n)&&(0,P.jsx)(wc,{title:`Allow interception (RMB = All systems selected)`,onClick:this.declareSelfIntercept.bind(this),onContextMenu:this.declareSelfInterceptAll.bind(this),img:`./img/addSelfIntercept.png`}),dl(e,n)&&(0,P.jsx)(wc,{title:`Remove an intercept order`,onClick:this.remSelfIntercept.bind(this),onContextMenu:this.remSelfIntercept.bind(this),img:`./img/remSelfIntercept.png`}),pl(e,n)&&(0,P.jsx)(wc,{title:`Meteor Defence: commit this weapon to defend against meteors this turn - it cannot fire or intercept (RMB = all similar weapons)`,onClick:this.declareMeteorDefence.bind(this),onContextMenu:this.declareMeteorDefenceAll.bind(this),img:`./img/selfIntercept.png`,children:(0,P.jsx)(Tc,{children:`☄`})}),ml(e,n)&&(0,P.jsx)(wc,{title:`Remove Meteor Defence (RMB = all similar weapons)`,onClick:this.remMeteorDefence.bind(this),onContextMenu:this.remMeteorDefenceAll.bind(this),img:`./img/remSelfIntercept.png`,children:(0,P.jsx)(Tc,{children:`☄`})}),ol(e,n)&&(0,P.jsx)(wc,{title:`Remove last fire order`,onClick:this.removeFireOrderMulti.bind(this),img:`./img/unfiringSmall.png`}),sl(e,n)&&(0,P.jsx)(wc,{title:`Remove all fire orders (RMB = All weapons selected)`,onClick:this.removeFireOrder.bind(this),onContextMenu:this.removeFireOrderAll.bind(this),img:`./img/firing.png`})]}),(0,P.jsxs)(Cc,{children:[hl(e,n)&&(0,P.jsx)(wc,{title:`Storm Defence: until withdrawn, this EM shield takes an EM storm strike on a side it faces (one per storm), going offline for that turn instead of a random system (RMB = all EM shields)`,onClick:this.declareStormDefence.bind(this),onContextMenu:this.declareStormDefenceAll.bind(this),img:`./img/selfIntercept.png`,children:(0,P.jsx)(Ec,{children:`⚡`})}),gl(e,n)&&(0,P.jsx)(wc,{title:`Withdraw Storm Defence (RMB = all EM shields)`,onClick:this.remStormDefence.bind(this),onContextMenu:this.remStormDefenceAll.bind(this),img:`./img/remSelfIntercept.png`,children:(0,P.jsx)(Ec,{children:`⚡`})})]}),(0,P.jsxs)(Cc,{children:[Oc(e,n)&&(0,P.jsx)(wc,{title:`Select all weapons of this type`,onClick:this.selectAllWeapons.bind(this),img:`./img/selectAllWeapons.png`,$blend:`screen`}),Oc(e,n)&&(0,P.jsx)(wc,{title:`Deselect all weapons of this type`,onClick:this.deselectAllWeapons.bind(this),img:`./img/deselectAllWeapons.png`,$blend:`screen`})]}),Sl(e,n)&&(0,P.jsx)(Co,{ship:e,system:n}),kc(e,n)&&(0,P.jsx)(xa,{ship:e,system:n}),Pc(e,n)&&(0,P.jsx)(Ma,{system:n,ship:e}),Fc(e,n)&&(0,P.jsx)(Ha,{system:n,ship:e}),Mc(e,n)&&(0,P.jsx)(Wo,{system:n,ship:e}),Nc(e,n)&&(0,P.jsx)(Qo,{system:n,ship:e}),Ac(e,n)&&(0,P.jsx)(ss,{system:n,ship:e}),jc(e,n)&&(0,P.jsx)(vs,{system:n,ship:e}),(Ic(e,n)||Lc(e,n))&&(0,P.jsx)(Qa,{system:n,ship:e}),(Rc(e,n)||zc(e,n))&&(0,P.jsx)(Qa,{system:n,ship:e}),Bc(e,n)&&(0,P.jsx)(Qi,{ship:e,system:n,readOnly:!Vc(e,n)}),Hc(e,n)&&(0,P.jsx)(ua,{ship:e,system:n,readOnly:!Uc(e,n)}),`   `,V(e,n)&&(0,P.jsx)(oo,{ship:e,system:n}),yl(e,n)&&(0,P.jsx)(ho,{ship:e,system:n}),bl(e,n)&&(0,P.jsx)(xc,{ship:e,system:n})]}):null}},Oc=(e,t)=>!(!window.matchMedia(`(pointer: coarse)`).matches||!t.weapon||al(e,t)||gamedata.gamephase!=3&&!t.ballistic&&!t.preFires||gamedata.gamephase!=1&&t.ballistic||gamedata.gamephase!=5&&t.preFires),kc=(e,t)=>gamedata.gamephase===1&&t.name==`adaptiveArmorController`,Ac=(e,t)=>t.name===`GraviticAugmenter`&&gamedata.isMyShip(e)&&!t.stowed&&!shipManager.power.isOffline(e,t)&&!(typeof t.isSpentLocked==`function`&&t.isSpentLocked())&&(gamedata.gamephase===1&&!weaponManager.hasFiringOrder(e,t)||gamedata.gamephase===5),jc=(e,t)=>t.name===`MinorThoughtPulsar`&&gamedata.gamephase===3&&gamedata.isMyShip(e)&&!t.stowed&&!shipManager.systems.isDestroyed(e,t)&&!shipManager.power.isOffline(e,t),Mc=(e,t)=>gamedata.gamephase===-1&&e.mine&&(e.spawned==-1&&gamedata.turn==1||e.spawned==gamedata.turn-1)&&(t.name==`CaptorMine`||t.name==`MineControllerDEW`),Nc=(e,t)=>gamedata.gamephase===-1&&e.mine&&(e.spawned==-1&&gamedata.turn==1||e.spawned==gamedata.turn-1)&&t.name==`ProximityMine`,Pc=(e,t)=>t.name==`hyachComputer`&&(gamedata.gamephase===1||gamedata.gamephase===3&&typeof t.firingMode==`function`&&t.firingMode()!==null),Fc=(e,t)=>t.name===`hyachSpecialists`,Ic=(e,t)=>gamedata.gamephase===1&&t.name===`ThirdspaceShield`,Lc=(e,t)=>gamedata.gamephase===1&&t.name===`ThirdspaceShieldGenerator`,Rc=(e,t)=>gamedata.gamephase===1&&t.name===`ThoughtShield`,zc=(e,t)=>gamedata.gamephase===1&&t.name===`ThoughtShieldGenerator`,Bc=(e,t)=>gamedata.isMyShip(e)&&t.name==`SelfRepair`,Vc=(e,t)=>Bc(e,t)&&gamedata.gamephase===1,Hc=(e,t)=>gamedata.isMyShip(e)&&(t.name==`StructureSelfRepair`||t.name==`CoopStructureSelfRepair`),Uc=(e,t)=>Hc(e,t)&&gamedata.gamephase===1,Wc=()=>typeof gamedata.fleetIsCommitted==`function`&&gamedata.fleetIsCommitted(),Gc=(e,t)=>gamedata.gamephase===-2&&!Wc()&&e&&e.userid!=0&&!e.flight&&!e.mine&&!Jc(t),Kc=(e,t)=>Gc(e,t)&&!!window.systemEnhancements&&!shipManager.systems.isDestroyed(e,t)&&systemEnhancements.offersFor(e,t).length>0,qc=e=>gamedata.gamephase===-2&&!Wc()&&e&&e.userid!=0&&!!e.mine&&battleDamage.mineMaxHealth(e)>1,Jc=e=>!e||!(e.maxhealth>0)||e.isTargetable===!1&&!e.orbitalMounted||!!e.hideInShipWindow||Array.isArray(e.systems),Yc=(e,t)=>gamedata.gamephase===-2?Gc(e,t)||Kc(e,t):Xc(e,t)||Zc(e,t)||Qc(e,t)||$c(e,t)||el(e,t)||tl(e,t)||rl(e,t)||il(e,t)||ol(e,t)||sl(e,t)||ll(e,t)||ul(e,t)||dl(e,t)||pl(e,t)||ml(e,t)||hl(e,t)||gl(e,t)||kc(e,t)||Pc(e,t)||Fc(e,t)||Ic(e,t)||Rc(e,t)||Lc(e,t)||zc(e,t)||Bc(e,t)||_l(e,t)||vl(e,t)||V(e,t)||yl(e,t)||Sl(e,t)||Oc(e,t)||Mc(e,t)||Nc(e,t)||Ac(e,t)||jc(e,t)||bl(e,t),Xc=(e,t)=>gamedata.gamephase===1&&(t.canOffLine||t.powerReq>0||window.officers&&officers.mayShunt(e,t))&&!t.powerLocked&&!shipManager.power.isOffline(e,t)&&!shipManager.power.getBoost(t)&&!weaponManager.hasFiringOrder(e,t),Zc=(e,t)=>gamedata.gamephase===1&&shipManager.power.isOffline(e,t)&&!shipManager.power.isForcedOffline(e,t)&&!shipManager.power.isVortexLockedOffline(e,t),Qc=(e,t)=>gamedata.gamephase===1&&!shipManager.power.isOffline(e,t)&&t.weapon&&t.overloadable&&!shipManager.power.isOverloading(e,t),$c=(e,t)=>gamedata.gamephase===1&&t.weapon&&t.overloadable&&shipManager.power.isOverloading(e,t)&&(t.overloadshots>=t.extraoverloadshots||t.overloadshots==0),el=(e,t)=>t.boostable&&gamedata.gamephase===1&&shipManager.power.canBoost(e,t)&&(!t.isScanner()||t.id==shipManager.power.getHighestSensorsId(e))&&t.name!==`ThirdspaceShieldGenerator`&&t.name!==`powerCapacitor`&&t.name!==`PowerCapacitor`,tl=(e,t)=>gamedata.gamephase===1&&!!shipManager.power.getBoost(t)&&t.name!==`ThirdspaceShieldGenerator`&&t.name!==`powerCapacitor`&&t.name!==`PowerCapacitor`,nl=(e,t)=>{let n=weaponManager.getFiringOrder(e,t);return n&&n.type!==`intercept`&&n.type!==`selfIntercept`?n:null},rl=(e,t)=>{if(al(e,t)||!t.weapon||!t.canChangeShots||!weaponManager.hasFiringOrder(e,t))return!1;let n=nl(e,t);return!!n&&n.shots<t.maxVariableShots},il=(e,t)=>{if(al(e,t)||!t.weapon||!t.canChangeShots||!weaponManager.hasFiringOrder(e,t))return!1;let n=nl(e,t);return!!n&&n.shots>1},al=(e,t)=>t.name===`jumpEngine`&&typeof t.getHeldVortex==`function`&&!!t.getHeldVortex(),ol=(e,t)=>t.weapon&&weaponManager.hasOrderForMode(t)&&t.canSplitShots&&!al(e,t),sl=(e,t)=>t.weapon&&weaponManager.hasFiringOrder(e,t)&&!(typeof t.isSpentLocked==`function`&&t.isSpentLocked())&&!al(e,t),cl=e=>!!(window.shipManager&&shipManager.isDockingRider(e)),ll=(e,t)=>t.weapon&&!e.mine&&!t.stowed&&!t.hideFiringModeSelector&&!cl(e)&&t.name!==`GraviticAugmenter`&&t.name!==`MinorThoughtPulsar`&&(gamedata.gamephase===1&&t.ballistic||gamedata.gamephase===5&&t.preFires||gamedata.gamephase===3&&!t.ballistic&&!t.preFires)&&(!weaponManager.hasFiringOrder(e,t)||t.multiModeSplit)&&!weaponManager.hasMeteorDefence(e,t)&&Object.keys(t.firingModes).length>1,ul=(e,t)=>t.weapon&&!cl(e)&&weaponManager.canSelfInterceptSingle(e,t),dl=(e,t)=>t.weapon&&t.canSplitShots&&!cl(e)&&weaponManager.canRemInterceptSingle(e,t),fl=(e,t)=>ll(e,t)||ul(e,t)||dl(e,t)||pl(e,t)||ml(e,t)||ol(e,t)||sl(e,t),pl=(e,t)=>t.weapon&&weaponManager.canDeclareMeteorDefence(e,t),ml=(e,t)=>t.weapon&&weaponManager.canRemoveMeteorDefence(e,t),hl=(e,t)=>!!(window.emStorm&&emStorm.canDeclare(e,t)),gl=(e,t)=>!!(window.emStorm&&emStorm.canWithdraw(e,t)),_l=(e,t)=>t.canActivate&&typeof t.canActivate==`function`&&t.canActivate()&&t.name!==`powerCapacitor`&&t.name!==`PowerCapacitor`&&t.name!==`GraviticAugmenter`&&t.name!==`jumpEngine`,vl=(e,t)=>t.canDeactivate&&typeof t.canDeactivate==`function`&&t.canDeactivate()&&t.name!==`powerCapacitor`&&t.name!==`PowerCapacitor`&&t.name!==`GraviticAugmenter`&&t.name!==`jumpEngine`,yl=(e,t)=>t.name===`jumpEngine`&&typeof t.canMaintainVortex==`function`&&(t.canMaintainVortex()||t.canDeactivate()||typeof t.getAbductionOrder==`function`&&!!t.getAbductionOrder()),bl=(e,t)=>!!window.officers&&officers.canManage(e,t),V=(e,t)=>t.name===`powerCapacitor`||t.name===`PowerCapacitor`,xl=(e,t)=>Xc(e,t)||Zc(e,t)||Qc(e,t)||$c(e,t)||t.boostable&&(el(e,t)||tl(e,t)),Sl=(e,t)=>V(e,t)||t.name===`GraviticAugmenter`||t.name===`jumpEngine`?!1:!!(t.canActivate&&typeof t.canActivate==`function`&&t.canActivate()||t.canDeactivate&&typeof t.canDeactivate==`function`&&t.canDeactivate()),Cl=(e,t)=>gamedata.gamephase===-2?Gc(e,t)||Kc(e,t):kc(e,t)||Pc(e,t)||Fc(e,t)||Mc(e,t)||Nc(e,t)||Ac(e,t)||jc(e,t)||Ic(e,t)||Lc(e,t)||Rc(e,t)||zc(e,t)||Bc(e,t)||Hc(e,t)||V(e,t)||xl(e,t)||Sl(e,t)||fl(e,t)||bl(e,t),wl=null;function Tl(e,t){let n=e&&t?{ship:e,systemId:t.id}:null;n===wl||n&&wl&&n.ship===wl.ship&&n.systemId===wl.systemId||(wl=n,window.shipWindowManagerReact&&window.shipWindowManagerReact.update())}function El(e,t){return wl!==null&&!!e&&!!t&&wl.ship===e&&wl.systemId===t.id}var Dl=M.div`
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
        width:  ${e=>e.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        background-color: ${e=>e.$docked?`#00ffff`:e.$criticals?e.$criticalsBenign?`#00ffff`:`#ed6738`:`#427231`};
    }
`,Ol=M.div`
    width:100%;
    height: calc(100% - 5px);
    color: white;
    font-family: arial;
    font-size: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    text-shadow: black 0 0 6px, black 0 0 6px;
`,kl=M.div`
    /*11px on a 32px icon: the 7px it launched at was legible only if you already knew to
      look for it (user report 2026-08-15). Still small enough to clear the icon art and the
      [n/n] load counter, which sits along the BOTTOM edge.*/
    font-size: 11px;
    line-height: 11px;
    color: ${N.colors.enhTitle};
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,Al=M.div`
    position: absolute;
    top: 0px;
    left: 1px;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    gap: 1px;
    pointer-events: none;
`,jl=M.svg`
    display: block;
    width: 11px;
    height: 11px;
    overflow: visible;
    filter: drop-shadow(0 0 1px black) drop-shadow(0 0 1px black);
`,Ml=B.officer.bar,Nl={serving:N.colors.enhTitle,disabled:`#c4c4c4`,killed:`#c4c4c4`,moving:Ml},Pl=({state:e,title:t})=>(0,P.jsxs)(jl,{viewBox:`0 0 12 12`,role:`img`,"aria-label":t,children:[(0,P.jsx)(`title`,{children:t}),(0,P.jsx)(`path`,{d:`M1.5 5.5 L6 2 L10.5 5.5 M1.5 10 L6 6.5 L10.5 10`,fill:`none`,stroke:Nl[e],strokeWidth:`2`,strokeLinecap:`square`}),e===`killed`&&(0,P.jsx)(`path`,{d:`M1 11.5 L11 0.5`,stroke:`#ff4d4f`,strokeWidth:`1.8`,strokeLinecap:`round`})]}),Fl=M.div`
    position: absolute;
    top: 0px;
    right: 1px;
    z-index: 1;
    pointer-events: none;
    font-size: 11px;
    line-height: 11px;
    color: #ffd27a;
    text-shadow: black 0 0 3px, black 0 0 3px, black 0 0 3px;
`,Il=M(Fl)`
    color: #9fe3ff;
`,Ll=M.div`
    position: relative;
    box-sizing: border-box;
    width: 32px;
    height: 32px;
    margin: ${e=>e.$scs?`3px 0`:`2px`};
   border: ${e=>e.$firing&&e.$calledShot?`2px solid #ff3366`:e.$meteorDefence?`1px solid #d9a441`:e.$firing&&e.$intercepting?`1px solid #52b352`:e.$firing?`1px solid #eb5c15`:e.$orderPending?`2px solid #00e5ff`:e.$highlight===`Yellow`?`1px solid #e1b000`:e.$highlight===`Orange`?`2px solid #ff6d3c`:e.$highlight===`Red`?`2px solid #ff0000`:`1px solid #496791`};
     background-color:  ${e=>e.$selected?`#4e6c91`:e.$meteorDefence?`#5c4318`:e.$firing&&e.$intercepting?`#2f7a3a`:e.$firing?`#e06f01`:e.$off?`#852d2d`:e.$boosted?`#cca300`:e.$loading&&e.$loadedAlternate?`#CD9E9E`:`rgba(0, 0, 0, 0.7)`};
    box-shadow: ${e=>e.$selected?`0px 0px 15px #0099ff`:e.$firing&&e.$calledShot?`0px 0px 12px #ff3366`:e.$meteorDefence?`0px 0px 12px #d9a441`:e.$firing&&e.$intercepting?`0px 0px 15px #52b352`:e.$firing?`box-shadow: 0px 0px 15px #eb5c15`:e.$orderPending?`0px 0px 12px #00e5ff`:`none`};
    /*The lobby's system menu is open on this system (helpers/menuTarget, user request 2026-10-16): an outline
      drawn just inside the edge, so no border state moves and nothing reflows, in the menu's own focus blue.*/
    ${e=>e.$menuTarget?`
        outline: 2px solid ${z.focus};
        outline-offset: -2px;
        box-shadow: 0 0 8px ${z.focus};
    `:``}
    /*A post the officer being moved may go to (OFFICERS_PLAN.md Stage 10, officers.isPickTarget): the same inset
      outline, in the Officers blue, while "Move Officer" waits for the player's click.*/
    ${e=>e.$officerPick?`
        outline: 2px solid ${Ml};
        outline-offset: -2px;
        box-shadow: 0 0 8px ${Ml};
    `:``}
    /*$mirror (rolled ship, port/starboard drawn swapped): the icon ART is flipped
      horizontally on an ::after layer so its facing matches the drawn side, while
      text, health bar and state overlays stay unflipped and readable*/
    background-image: ${e=>e.$mirror?`none`:`url(${e.$background})`};
    background-size: cover;
    ${e=>e.$mirror?`z-index: 0;`:``} /*own stacking context keeps the z:-1 art layer inside this icon*/
    ${e=>e.$mirror?`
    &::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url(${e.$background});
        background-size: cover;
        transform: scaleX(-1);
        z-index: -1;
    }
    `:``}
    filter: ${e=>e.$destroyed?`blur(1px)`:`none`};
    cursor: pointer;
    -webkit-user-select: none;
    -webkit-touch-callout: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    
    ${Ol} {
        display: ${e=>e.$offline?`none`:`flex`};
        /*docked Kirishiac Orbital: keep the icon text ([n/5] regeneration counter) fully
        readable above the blue fade - the ::before overlay is positioned, so the static
        text would otherwise paint underneath it*/
        ${e=>e.$docked?`position: relative; z-index: 1;`:``}
    }


    &::before {
        content: "";
        position:absolute;
        width: 100%;
        height: 100%;
        opacity: ${e=>e.$destroyed||e.$offline||e.$loading||e.$docked?`0.5`:`0`};

        background-color: ${e=>e.$destroyed||e.$offline?`black`:e.$loading?`orange`:e.$docked?`#1a4a6e`:`transparent`};

        background-image: ${e=>e.$offline?`url(./img/offline.png)`:`none`};
    }
`,Rl=class extends _.Component{constructor(e){super(e),this.longPressTimer=null,this.ignoreNextClick=!1,this.touchActive=!1}clickSystem(e){if(e.stopPropagation(),e.preventDefault(),this.ignoreNextClick){this.ignoreNextClick=!1;return}let{system:t,ship:n}=this.props;t=shipManager.systems.initializeSystem(t);let r=Gc(n,t);if((gamedata.waiting||gamedata.replay)&&!r||window.officers&&officers.picking&&officers.pickTarget(n,t))return;let i=!!n.removed&&!shipManager.isDestroyedByDamage(n);if(r||i||!shipManager.isDestroyed(n)&&(!shipManager.isDestroyed(n,t)||t.clickableWhenDestroyed)){if(i&&!r)gamedata.isMyShip(n)&&window.uiEvents.relay(`SystemClicked`,{ship:n,system:t,element:e.currentTarget,showMenu:!0});else{if(gamedata.rules&&gamedata.rules.friendlyFire===1&&gamedata.isMyShip(n)){var a=gamedata.selectedSystems.length>0?gamedata.selectedSystems[0]:null;if(a&&a.ship.id!=n.id&&!weaponManager.isSelectedWeapon(t)){window.uiEvents.relay(`SystemTargeted`,{ship:n,system:t});return}}var o=t.weapon&&typeof t.isSpentLocked==`function`&&t.isSpentLocked(),s=typeof t.canSelectForAbduction==`function`&&t.canSelectForAbduction(n);if(!o&&(t.weapon&&gamedata.gamephase===3&&!t.ballistic&&!t.preFires||gamedata.gamephase===1&&t.ballistic||gamedata.gamephase===5&&t.preFires||weaponManager.canManuallyInterceptWith(n,t)||s)&&!shipManager.isAdrift(n)&&gamedata.isMyShip(n)){if(t.hasSpecialTargeting&&typeof t.reopenSpecialTargeting==`function`&&weaponManager.hasFiringOrder(n,t)&&t.reopenSpecialTargeting(n))return;var c=weaponManager.hasFiringOrder(n,t),l=c&&c!==`self`&&!t.canSplitShots&&!t.hasSpecialTargeting;weaponManager.isSelectedWeapon(t)?weaponManager.unSelectWeapon(n,t):l||weaponManager.selectWeapon(n,t)}if(gamedata.isMyShip(n)&&(t.name===`hangar`||t.name===`catapult`||t.name===`fighterRail`)){if(gamedata.gamephase===-1&&window.DeploymentDock&&typeof window.DeploymentDock.shipHasOpenableDockDialog==`function`&&window.DeploymentDock.shipHasOpenableDockDialog(n)&&window.confirm&&typeof window.confirm.hangarDeployDock==`function`){window.confirm.hangarDeployDock(n);return}if(gamedata.gamephase===3&&!t.isShadowHangar&&!shipManager.movement.isRolling(n)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(n)!==`no`)&&window.confirm&&typeof window.confirm.hangarLaunch==`function`){window.confirm.hangarLaunch(n);return}}gamedata.isMyShip(n)&&(t.name===`dockingCollar`||t.isLCVRail)&&gamedata.gamephase===3&&!shipManager.movement.isRolling(n)&&!(shipManager.movement.isPivoting&&shipManager.movement.isPivoting(n)!==`no`)&&typeof window.lcvRailLaunchable==`function`&&window.lcvRailLaunchable(n,t)&&window.confirm&&typeof window.confirm.lcvLaunch==`function`?window.confirm.lcvLaunch(n):gamedata.isMyShip(n)?window.uiEvents.relay(`SystemClicked`,{ship:n,system:t,element:e.currentTarget,showMenu:!0}):window.uiEvents.relay(`SystemTargeted`,{ship:n,system:t})}}}onSystemMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;e.stopPropagation(),e.preventDefault();let{system:t,ship:n}=this.props;t=shipManager.systems.initializeSystem(t),window.uiEvents.relay(`SystemMouseOver`,{ship:n,system:t,element:e.currentTarget,showInfo:!0})}onSystemMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay(`SystemMouseOut`))}onTouchStart(e){e.stopPropagation(),this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);let t=e.currentTarget,n=e.touches[0];this.touchStartX=n.clientX,this.touchStartY=n.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{system:e,ship:n}=this.props;e=shipManager.systems.initializeSystem(e),window.uiEvents.relay(`SystemMouseOver`,{ship:n,system:e,element:t,showInfo:!0}),this.longPressTimer=null},400)}onTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;let t=e.touches[0],n=t.clientX-this.touchStartX,r=t.clientY-this.touchStartY;(Math.abs(n)>10||Math.abs(r)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onTouchCancel(e){e.stopPropagation(),this.longPressTimer&&=(clearTimeout(this.longPressTimer),null),this.touchActive=!1,window.uiEvents.relay(`SystemMouseOut`)}onTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay(`SystemMouseOut`),setTimeout(()=>{this.touchActive=!1},300)}onContextMenu(e){if(e.stopPropagation(),e.preventDefault(),window.matchMedia(`(pointer: coarse)`).matches)return;let{system:t,ship:n}=this.props;t=shipManager.systems.initializeSystem(t),t.weapon&&weaponManager.selectAllWeapons(n,t)}render(){let{system:e,ship:t,scs:n,fighter:r,destroyed:i,mirror:a}=this.props;return e=shipManager.systems.initializeSystem(e),e=shipManager.systems.initializeSystem(e),(tu(t,e)||i)&&!e.clickableWhenDestroyed&&!Gc(t,e)?(0,P.jsxs)(Ll,{$background:nu(e),$destroyed:!0,$mirror:a,children:[zl(t,e),(0,P.jsx)(Dl,{$health:`0`})]}):(0,P.jsxs)(Ll,{$scs:n,$highlight:au(t,e),$destroyed:tu(t,e)||i,onClick:this.clickSystem.bind(this),onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onTouchStart.bind(this),onTouchMove:this.onTouchMove.bind(this),onTouchEnd:this.onTouchEnd.bind(this),onTouchCancel:this.onTouchCancel.bind(this),onContextMenu:this.onContextMenu.bind(this),$background:nu(e),$mirror:a,$offline:Jl(t,e),$loading:Kl(e),$loadedAlternate:ql(e),$selected:ou(e),$firing:Bl(t,e),$intercepting:Hl(t,e),$meteorDefence:Ul(t,e),$calledShot:Gl(t,e),$boosted:Xl(t,e),$off:Yl(e),$docked:Zl(e),$orderPending:$l(e),$menuTarget:El(t,e),$officerPick:!!window.officers&&officers.isPickTarget(t,e),children:[zl(t,e),Ul(t,e)&&(0,P.jsx)(Fl,{title:`Committed to meteor defence this turn`,children:`☄`}),Wl(e)&&(0,P.jsx)(Il,{title:`Storm Defence (until withdrawn): takes an EM storm strike on a side it faces`,children:`⚡`}),(0,P.jsx)(Ol,{children:su(t,e)}),(!r||ru(e))&&(0,P.jsx)(Dl,{$scs:n,$health:tu(t,e)||i?0:eu(t,e),$criticals:ru(e),$criticalsBenign:iu(e),$docked:Ql(e)})]})}},zl=(e,t)=>{if(!e||!t)return null;let n=!!window.systemEnhancements&&systemEnhancements.hasAny(e,t.id),r=window.officers?officers.postState(e,t.id):null;return!n&&!r?null:(0,P.jsxs)(Al,{children:[n&&(0,P.jsx)(kl,{title:`Carries a system enhancement`,children:`✦`}),r&&(0,P.jsx)(Pl,{state:r,title:officers.describePost(e,t.id)})]})},Bl=(e,t)=>(weaponManager.hasFiringOrder(e,t)||Vl(t))&&!(typeof t.isSpentLocked==`function`&&t.isSpentLocked()),Vl=e=>gamedata.gamephase===1&&typeof e.getAbductionOrder==`function`&&!!e.getAbductionOrder(),Hl=(e,t)=>weaponManager.isInterceptOnly(e,t),Ul=(e,t)=>!!t.weapon&&weaponManager.hasMeteorDefence(e,t),Wl=e=>!!window.emStorm&&emStorm.isActive()&&emStorm.isEMShield(e)&&emStorm.isDeclared(e),Gl=(e,t)=>!t.weapon||!weaponManager.hasFiringOrder(e,t)?!1:weaponManager.getCalledShotInfo(e,t)!==null,Kl=e=>e.weapon&&(!weaponManager.isLoaded(e)||typeof e.isSpentLocked==`function`&&e.isSpentLocked()),ql=e=>e.weapon&&weaponManager.isLoadedAlternate(e),Jl=(e,t)=>shipManager.power.isOffline(e,t),Yl=e=>e.activeMeansOff&&e.active,Xl=(e,t)=>shipManager.power.isBoosted(e,t)||t.active&&!t.activeMeansOff&&!t.suppressActiveBoost,Zl=e=>!!(e.showDockedVisual&&e.activeEffective||e.stowed&&e.stowedArcStart==null||e.dockedWithOrbital),Ql=e=>Zl(e)||!!e.stowed,$l=e=>!!(e.showDockedVisual&&typeof e.hasPendingDockingOrder==`function`&&e.hasPendingDockingOrder()),eu=(e,t)=>(t.name===`ThirdspaceShield`||t.name===`ThoughtShield`)&&t.baseRating?Math.min(100,t.currentHealth/t.baseRating*100):(t.maxhealth-damageManager.getDamage(e,t))/t.maxhealth*100,tu=(e,t)=>shipManager.systems.isDestroyed(e,t),nu=e=>e.name==`thruster`&&!e.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/thruster`+e.direction+`.png`):e.iconPath?window.AssetManager.getSmartImagePath(`./img/systemicons/${e.iconPath}`):window.AssetManager.getSmartImagePath(`./img/systemicons/${e.name}.png`),ru=e=>shipManager.criticals.hasCriticalsIcon(e),iu=e=>shipManager.criticals.hasOnlyCritical(e,`HangarOperations`,!0)||shipManager.criticals.hasOnlyCritical(e,`LCVLaunchedThisTurn`,!0),au=(e,t)=>shipManager.systems.hasBorderHighlight(e,t),ou=e=>weaponManager.isSelectedWeapon(e),su=(e,t)=>{if(t.outputDisplay!==void 0&&t.outputDisplay!==null&&t.outputDisplay!=``)return t.outputDisplay;if(t.weapon){if(t.stowed&&t.stowedArcStart==null)return`-`;if(typeof t.isSpentLocked==`function`&&t.isSpentLocked())return`✓`;if(typeof t.getVortexIconLoad==`function`){let e=t.getVortexIconLoad();if(e!=null)return e}let n=weaponManager.hasFiringOrder(e,t);if(n&&t.canChangeShots)return weaponManager.getFiringOrder(e,t).shots+`/`+t.shots;if(n){if(weaponManager.getCalledShotInfo(e,t))return`⊕`}else if(!n){let n=weaponManager.getWeaponCurrentLoading(t),r=t.loadingtime;t.normalload>0&&(r=t.normalload),n>r&&(n=r);let i=``;return t.overloadturns>0&&shipManager.power.isOverloading(e,t)&&(i=`(`+t.overloadturns+`)`),t.overloadshots>0?`S`+t.overloadshots:n+i+`/`+r}}else if(t.outputType===`thrust`)return shipManager.movement.getRemainingEngineThrust(e);else if(t.outputType===`power`){let n=shipManager.power.getReactorPower(e,t);return gamedata.gamephase>1&&n<0?0:n}else return shipManager.systems.getOutput(e,t)},H=M.div`
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
`,cu=class extends _.Component{constructor(e){super(e)}getWeapons(e,t){return e.flight?e.systems.map(e=>e.systems).reduce((e,t)=>e.concat(t),[]).filter(e=>e.weapon):e.systems.filter(e=>e.weapon||e.outputType===`thrust`||e.outputType===`EW`||e.outputType===`power`||e.outputType===`settings`)}render(){let{ship:e,gamePhase:t}=this.props;if(!e)return null;let n=this.getWeapons(e,t);return(0,P.jsx)(H,{children:n.map((t,n)=>(0,P.jsx)(Rl,{fighter:e.flight,system:t,ship:e},`system-${n}`))})}},lu=e=>{if(!e.hitChart)return[];let t=[`Primary`,`Front`,`Aft`,`Port`,`Starboard`],n=5;e.base&&!e.smallBase?(t[1]=`Sections`,n=2):e.SixSidedShip&&(t[31]=`Port Front`,t[32]=`Port Aft`,t[41]=`Starboard Front`,t[42]=`Starboard Aft`,n=43);let r=[];for(let i=0;i<n;i++){if(e.hitChart[i]===void 0)continue;let n=[],a=0;for(let t in e.hitChart[i]){let r=Math.floor((t-a)/20*100);a=t;let o=e.hitChart[i][t],s=o.indexOf(`:`);s>0&&(o=o.substring(s+1)),n.push({name:o,chance:r})}r.push({location:i,name:t[i],entries:n})}return r},uu=M.div`
    ${e=>e.$tightBottom?`& > *:last-child { display: none; }`:``}
    ${e=>e.$compactText?`
    ${U} {
        font-size: 10px;
        line-height: 1.4;
        color: ${N.colors.textAccent};
    }
    ${_u} {
        font-size: 10px;
        font-style: normal;
        color: ${N.colors.text};
    }`:``}
`,du=class extends _.Component{render(){let{ship:e,hideHitChart:t,tightBottom:n,compactText:r,craft:i}=this.props,a=!!e.mine||window.gamedata&&typeof gamedata.isTerrain==`function`&&gamedata.isTerrain(e.shipSizeClass,e.userid),o=!!e.flight||a;var s=[],c=[],l=[];e.notes&&(s=e.notes.split(`<br>`)),e.hitChart&&!t&&lu(e).forEach(function(e){c[e.name]=e.entries.map(function(e){return e.name+` `+e.chance+`%`}).join(`, `)}),e.enhancementTooltip!=``&&(l=e.enhancementTooltip.split(`<br>`));let u={};if(!e.flight&&e.hasAttached&&Object.keys(e.hasAttached).length>0){let t={1:`Forward`,2:`Aft`,3:`Port`,31:`Port-Forward`,32:`Port-Aft`,4:`Starboard`,41:`Starboard-Forward`,42:`Starboard-Aft`};for(let n in e.hasAttached){let r=t[e.hasAttached[n]]||`Unknown`;u[r]||(u[r]=0),u[r]++}}let d=window.officers?officers.displayStat(e,`offensivebonus`):e.offensivebonus;e.flight&&window.nebula&&nebula.isActive()&&(d=nebula.showOffensiveBonus(d)),e.flight&&window.emStorm&&emStorm.isActive()&&(d=Math.max(0,d-emStorm.offensivePenalty(e))),e.flight&&gamedata.areMinesPresent&&(e.minesweeper?d-=window.ew.getDetectMEW(e):d-=window.ew.getDetectMEW(e)*2);let f=e.flight&&window.officers?officers.shuntOb(e):0,p=e.flight&&window.officers?officers.shuntThrust(e):0,m=window.officers?officers.currentTurn():0,h=[];if(e.flight&&window.officers){let t={};officers.listFor(e).forEach(n=>{let r=officers.get(n.id);if(i&&r&&r.multi&&String(n.post)!==String(i.id))return;let a=officers.stateText(n),o={name:officers.label(n.id,e),text:(r?r.summary:``)+(a?` `+a:``),out:!officers.serves(n,m),count:1},s=o.name+`|`+o.text+`|`+o.out;t[s]?t[s].count++:(t[s]=o,h.push(o))}),h.forEach(e=>{e.count>1&&(e.name+=` (`+e.count+`)`)})}var g=0,_=!0;if(e.mine){var v=shipManager.systems.getSystemByName(e,`mineStealth`);v&&!v.isMineRevealed(e)&&(_=!1,c=[],s=[`No details known, scan with OEW to identify.`],l=[])}return(0,P.jsxs)(uu,{$tightBottom:n,$compactText:r,children:[e.flight&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Offensive bonus: `}),(d+f)*5,f>0?` (+`+f*5+` shunted, direct fire only)`:``]},g++),e.flight&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Armor (F/S/A): `}),shipManager.systems.getFlightArmour(e)]},g++),e.flight&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Profile - Front/Side: `}),e.forwardDefense*5,`/`,e.sideDefense*5]},g++),e.flight&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Initiative: `}),window.officers?officers.displayIniativeBonus(e):e.iniativebonus]},g++),e.flight&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Thrust: `}),(window.officers?officers.displayStat(e,`freethrust`):e.freethrust)+p,p>0?` (+`+p+` shunted)`:``]},g++),e.flight&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Turn Cost: `}),e.turncost]},g++),e.flight&&_&&e.turndelay&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Turn Delay: `}),e.turndelaycost]},g++),e.flight&&_&&(0,P.jsx)(U,{children:`\xA0`},g++),Object.keys(s).length>0&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`NOTES:`}),`\xA0`]},g++),Object.keys(s).length>0&&Object.keys(s).map(e=>(0,P.jsx)(U,{children:s[e]},g++)),Object.keys(s).length>0&&(0,P.jsx)(U,{children:`\xA0`},g++),Object.keys(c).length>0&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`HIT CHART:`}),`\xA0`]},g++),Object.keys(c).length>0&&Object.keys(c).map(e=>(0,P.jsxs)(U,{children:[(0,P.jsxs)(_u,{children:[e,`: `]}),c[e]]},g++)),Object.keys(c).length>0&&(0,P.jsx)(U,{children:`\xA0`},g++),Object.keys(u).length>0&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`UNITS ATTACHED:`}),`\xA0`]},g++),Object.keys(u).length>0&&Object.keys(u).map(e=>(0,P.jsxs)(U,{children:[(0,P.jsxs)(_u,{children:[e,`: `]}),u[e]]},g++)),Object.keys(u).length>0&&(0,P.jsx)(U,{children:`\xA0`},g++),o&&e.enhancementTooltip!=``&&_&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`ENHANCEMENTS:`}),`\xA0`]},g++),o&&e.enhancementTooltip!=``&&_&&Object.keys(l).map(e=>(0,P.jsx)(U,{children:l[e]},g++)),o&&e.enhancementTooltip!=``&&_&&(0,P.jsx)(U,{children:`\xA0`},g++),e.flight&&h.length>0&&(0,P.jsxs)(U,{children:[(0,P.jsx)(Su,{children:`OFFICERS:`}),`\xA0`]},g++),e.flight&&h.map(e=>(0,P.jsxs)(bu,{$out:e.out,children:[(0,P.jsxs)(xu,{children:[e.name,`: `]}),e.text]},g++)),e.flight&&h.length>0&&(0,P.jsx)(U,{children:`\xA0`},g++)]})}},fu=M(Yn)`
    /*font-size: 12px;*/
	font-size: 13px;
`,pu=M(Jn)`
    position: absolute;
    z-index: 20000;
    ${e=>Object.keys(e.position).reduce((t,n)=>t+`
`+n+`:`+e.position[n]+`px;`,``)}
    width: ${e=>e.ship?`320px`:`220px`};
    text-align: left;
    opacity:0.8;
`,mu=M.div`
    height: 1px;
    background: rgba(189, 234, 250, 0.3);
    margin: 5px 0;
`,hu={MissileLost:`A missile was lost to damage`},gu={DamageReductionReduced:e=>`Damage reduction reduced by ${e}`,EMStormSensors:e=>`EM storm: sensors -${e}`,EMStormPower:e=>`EM storm: power -${e}`,EMStormInitiative:e=>`EM storm: initiative -${e*5}`,EMStormOffensive:e=>`EM storm: offensive bonus -${e*5}`},U=M(Xn)`
    text-align: left;
    /*color: #5e85bc;*/
	color: #BDEAFA; /*replace dark blue above with bluish white, more eyes friendly*/
    font-family: arial;
    /*font-size: 11px;*/
	font-size: 12px;
`,_u=M.span`
    color: white;
	font-size: 11px;
    font-weight: bold;
`,vu=M.span`
    color: #C6E2FF;
`,yu=M(fu)`
    color: ${N.colors.enhTitle};
`,bu=M(U)`
    &&& { color: ${e=>e.$out?N.colors.textDim:N.colors.enhText}; }
`,xu=M.span`
    &&& { color: ${N.colors.enhTitle}; }
`,Su=M(_u)`
    &&& { color: ${N.colors.enhTitle}; }
`,Cu=class extends _.Component{render(){let{ship:e,selectedShip:t,system:n,boundingBox:r,officersOnly:i,craft:a}=this.props;if(i)return(0,P.jsxs)(pu,{position:ju(r),children:[(0,P.jsx)(fu,{children:window.officers?officers.postName(n):n.displayName}),Eu(e,n,!0)]});if(n instanceof Ship||n===e){var o=e.shipClass,s=e.name;if(n.flight&&(o=n.systems[1].displayName),e.mine){var c=shipManager.systems.getSystemByName(e,`mineStealth`);c&&!c.isMineRevealed(e)&&(o=`Mine`,s=`Mine`)}return(0,P.jsxs)(pu,{ship:!0,position:ju(r),children:[(0,P.jsxs)(fu,{children:[(0,P.jsx)(vu,{children:s}),` - `,o]}),(0,P.jsx)(du,{ship:e,craft:a})]})}var l=`Special`,u=[];n.data.Special&&n.data.Special!=``&&(u=String(ku(e,n,l)).split(`<br>`));var d=0;let f=window.officers?officers.displayStat(e,`offensivebonus`):e.offensivebonus;e.flight&&window.nebula&&nebula.isActive()&&(f=nebula.showOffensiveBonus(f)),e.flight&&window.emStorm&&emStorm.isActive()&&(f=Math.max(0,f-emStorm.offensivePenalty(e))),e.flight&&gamedata.areMinesPresent&&(e.minesweeper?f-=window.ew.getDetectMEW(e):f-=window.ew.getDetectMEW(e)*2),e.flight&&window.officers&&!n.ballistic&&(f+=officers.shuntOb(e));var p=n.displayName,m=n.firingModes?n.firingModes[n.firingMode]:null,h=null;n.name===`ShadowFighterBomb`&&window.weaponManager&&typeof weaponManager.shadowFighterBombPool==`function`&&(h=weaponManager.shadowFighterBombPool(e,n,!0));var g=null;if(n.outputType===`power`&&window.shipManager&&shipManager.power&&typeof shipManager.power.getDockedPowerSummary==`function`){var _=shipManager.power.getDockedPowerSummary(e);_.donors>0&&(g=_)}var v=null;if(typeof n.getAbductionOrder==`function`){var y=n.getAbductionOrder();if(y){var b=gamedata.getShip(y.targetid);v=b?b.name:`Unit `+y.targetid}}var x=window.officers?officers.laserBoostText(e,n):null,S=window.officers?officers.ionBoostText(e,n):null;let C=!1;if(e.mine){var c=shipManager.systems.getSystemByName(e,`mineStealth`);c&&!c.isMineRevealed(e)&&(C=!0,p=`Mine`,u=[`No details known, scan with OEW to identify.`])}return(0,P.jsxs)(pu,{position:ju(r),children:[(0,P.jsx)(fu,{children:p}),!e.flight&&!C&&Au(`Structure`,n.maxhealth-damageManager.getDamage(e,n)+`/`+n.maxhealth),!e.flight&&!C&&Au(`Armor`,shipManager.systems.getArmour(e,n)),e.flight&&!C&&Au(`Offensive bonus`,Ou(n,f*5)),n.firingModes&&!C&&Au(`Firing mode`,m),n.missileArray&&Object.keys(n.missileArray).length>0&&!C&&Au(`Ammo Amount`,n.missileArray[n.firingMode].amount),!C&&Object.keys(n.data).map((t,r)=>t!=l&&(t!==`Ammunition`||n.name!==`GrapplingClaw`&&n.name!==`Marines`)&&Au(t,ku(e,n,t),`data`+r)),h!==null&&Au(`Fighters available`,h),g&&Au(`Shared by docked ships`,`+`+g.shared+` of `+g.surplus+` pooled from `+g.donors+(g.donors===1?` ship`:` ships`)),v!==null&&Au(`Abduction target`,v),x!==null&&!C&&Au(`Laser boost`,x),S!==null&&!C&&Au(`Thrust boost`,S),Object.keys(u).length>0&&(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Special: `}),`\xA0`]},`special-${d++}`),Object.keys(u).length>0&&Object.keys(u).map(e=>(0,P.jsx)(U,{children:u[e]},`special-${d++}`)),!C&&Eu(e,n),(Object.keys(n.critData).length>0||n.criticals&&n.criticals.length>0)&&!C&&Du(n),!gamedata.isMyShip(e)&&!C&&(gamedata.gamephase==3||gamedata.gamephase==1)&&gamedata.waiting==0&&gamedata.selectedSystems.length>0&&t&&wu(e,t,n),gamedata.isMyShip(e)&&!C&&gamedata.rules&&gamedata.rules.friendlyFire===1&&(gamedata.gamephase==3||gamedata.gamephase==5||gamedata.gamephase==1)&&gamedata.waiting==0&&gamedata.selectedSystems.length>0&&t&&wu(e,t,n),gamedata.isMyShip(e)&&!C&&n.weapon&&weaponManager.hasFiringOrder(e,n)&&Tu(e,n)]})}},wu=(e,t,n)=>weaponManager.canCalledshot(e,n,t)?[(0,P.jsx)(fu,{children:`Called shot`},`calledHeader`)].concat(gamedata.selectedSystems.map((r,i)=>{if(weaponManager.isOnWeaponArc(t,e,r)){if(weaponManager.checkIsInRange(t,e,r)){var a=r.firingMode;return a=r.firingModes[a],n.id!=null&&!weaponManager.canWeaponCall(r)?(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:r.displayName}),`: Cannot Called Shot`]},`called-${i}`):(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:r.displayName}),` - Approx:  `,weaponManager.calculateHitChange(t,e,r,n.id).hitChance,`%`]},`called-${i}`)}return(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:r.displayName}),`: Not in Range`]},`called-${i}`)}return(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:r.displayName}),`: Not in Arc`]},`called-${i}`)})):[(0,P.jsx)(fu,{children:`Called shot`},`calledHeader`)].concat((0,P.jsx)(U,{children:`Cannot Target`},`cannotTarget`)),Tu=(e,t)=>{var n=weaponManager.getCalledShotInfo(e,t);return n?[(0,P.jsx)(mu,{},`calledShotDivider`),(0,P.jsx)(fu,{children:`Called Shot`},`calledShotHeader`),(0,P.jsxs)(U,{children:[(0,P.jsx)(_u,{children:`Target: `}),n.targetSystem.displayName,` (Id: `,n.targetSystem.id,`) on `,n.targetShip.name]},`calledShotTarget`)]:null},Eu=(e,t,n=!1)=>{if(!window.officers||!e||!t)return null;let r=officers.onPost(e,t.id),i=officers.arrivingAt(e,t.id);if(r.length===0&&i.length===0)return null;let a=officers.currentTurn();return(n?[]:[(0,P.jsx)(mu,{},`officerDivider`)]).concat([(0,P.jsx)(yu,{children:`Officers`},`officerHeader`)]).concat(r.map((t,n)=>{let r=officers.moveText(e,t)||officers.stateText(t);return(0,P.jsxs)(bu,{$out:!officers.serves(t,a)||officers.movingTo(e,t)!==null,children:[(0,P.jsxs)(xu,{children:[officers.label(t.id,e),`: `]}),officers.summaryOf(e,t),r?` `+r:``]},`officer-${t.id}-${n}`)}),i.map((t,n)=>(0,P.jsxs)(bu,{$out:!0,children:[(0,P.jsxs)(xu,{children:[officers.label(t.entry.id,e),` `]}),officers.arrivalText(e,t.entry)]},`arriving-${t.entry.id}-${n}`)))},Du=e=>{let t=Object.keys(e.critData).length>0?Object.keys(e.critData):[...new Set((e.criticals||[]).map(e=>e.phpclass))];return t.length===0?null:[(0,P.jsx)(mu,{},`critDivider`),(0,P.jsx)(fu,{children:`Criticals`},`criticalHeader`)].concat(t.map(t=>{let n=0,r=0;var i=0,a=0,o=!1,s=``;i=0,a=0,o=!1,s=``;for(let s in e.criticals)e.criticals[s].phpclass==t&&e.criticals[s].turn<=gamedata.turn&&(e.criticals[s].turnend==0||e.criticals[s].turnend>=gamedata.turn)&&(n++,r+=parseInt(e.criticals[s].param,10)||0,n==1&&(i=e.criticals[s].turnend,a=e.criticals[s].turnend,o=e.criticals[s].turnend==0),e.criticals[s].turnend>0?(e.criticals[s].turnend>a&&(a=e.criticals[s].turnend),(e.criticals[s].turnend<i||i==0)&&(i=e.criticals[s].turnend)):o=!0);if(i>0&&(s=` (until end of turn `+i,o?s+=`+`:a>i&&(s=s+`-`+a),s+=`)`),n>=1&&gu[t]){let e=gu[t](r);return(0,P.jsxs)(U,{children:[e,` `,s]},`critical-${t}`)}let c=e.critData[t]||hu[t]||t;return n>1?(0,P.jsxs)(U,{children:[`(`,n,` x) `,c,` `,s]},`critical-${t}`):n==1?(0,P.jsxs)(U,{children:[c,` `,s]},`critical-${t}`):null}))},Ou=(e,t)=>typeof e.adjustOffensiveBonusDisplay==`function`?e.adjustOffensiveBonusDisplay(t):t,ku=(e,t,n)=>{let r=typeof t.adjustDataValueDisplay==`function`?t.adjustDataValueDisplay(n,t.data[n]):t.data[n];return window.officers?officers.adjustDataDisplay(e,t,n,r):r},Au=(e,t,n)=>{if(typeof t==`string`&&t.indexOf(`<br>`)!==-1){let r=t.split(`<br>`);return(0,P.jsxs)(U,{children:[(0,P.jsxs)(_u,{children:[e,`: `]}),r.map((e,t)=>(0,P.jsxs)(_.Fragment,{children:[t>0&&(0,P.jsx)(`br`,{}),e]},t))]},n)}return(0,P.jsxs)(U,{children:[(0,P.jsxs)(_u,{children:[e,`: `]}),t]},n)},ju=e=>{let t={};return e.top>window.innerHeight/2?t.bottom=window.innerHeight-e.top:t.top=e.top+e.height,e.left>window.innerWidth/2?t.right=window.innerWidth-e.right:t.left=e.left+e.width,t};function Mu(e,t,n,r){if(!e||!t)return null;let i=n-t.right-8,a=t.left-8;if(Math.max(i,a)<300)return null;let o={};return i>=a?o.left=t.right:o.right=n-t.left,e.top>r/2?o.bottom=Math.max(8,r-e.bottom):o.top=Math.max(8,e.top),o}M(Yn)`
    font-size: 12px;
`;var Nu=M(Jn)`
    position: absolute;
    z-index: 20000;
    ${e=>Object.keys(e.position).reduce((t,n)=>t+`
`+n+`:`+e.position[n]+`px;`,``)}
    max-width: 500px;
    text-align: left;
    opacity: ${e=>e.opacity||.8};
    border: 1px solid #496791;
    padding-bottom: 3px;
    /*the lobby mounts #systemInfoReact inside a pointer-events: none fixed overlay
      (same as #shipWindowsReact) - this menu is interactive, so it must opt back in.
      No-op in game.php, where the mount point has no pointer-events override.*/
    pointer-events: auto;
    /*Lobby: the menu inside (ApplyDamageMenu) is a framed panel of its own, so this tooltip
      draws nothing round it - no second border, padding or fill, and no element opacity.*/
    ${e=>e.$bare&&`
        padding: 0;
        border: 0;
        border-radius: 0;
        background: none;
        opacity: 1;
    `}
`;M(Xn)`
    text-align: left;
    color: #5e85bc;
    font-family: arial;
    font-size: 11px;
`,M.span`
    color: white;
`;var Pu=class extends _.Component{componentDidMount(){this.markTarget()}componentDidUpdate(){this.markTarget()}componentWillUnmount(){Tl(null,null)}markTarget(){let{ship:e,system:t,avoidBox:n}=this.props,r=!!n&&Yc(e,t);Tl(r?e:null,r?t:null)}render(){let{ship:e,system:t,boundingBox:n,avoidBox:r}=this.props;return Yc(e,t)?(0,P.jsx)(Nu,{position:Mu(n,r,Fu(),Iu())||Lu(n),opacity:Cl(e,t)?.95:.8,$bare:gamedata.gamephase===-2,children:(0,P.jsx)(Dc,{...this.props})}):null}},Fu=()=>document.documentElement.clientWidth||window.innerWidth,Iu=()=>document.documentElement.clientHeight||window.innerHeight,Lu=e=>{let t={};return e.top>window.innerHeight/2?t.bottom=window.innerHeight-e.top:t.top=e.top+e.height,e.left>window.innerWidth/2?t.right=window.innerWidth-e.right:t.left=e.left,t},Ru={0:`Primary`,1:`Forward`,2:`Aft`,3:`Port`,4:`Starboard`,5:``,31:`Port Fwd`,32:`Port Aft`,41:`Stbd Fwd`,42:`Stbd Aft`},zu=M.div`
    position: relative;
    z-index: 1; /*above the watermark + ship-hover underlay*/
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    background-color: ${N.colors.panelBgGlass};
    ${e=>e.$area?`grid-area: ${e.$area};`:``}
    ${e=>e.$valign?`align-self: ${e.$valign};`:``}
    ${e=>e.$justify?`justify-self: ${e.$justify};`:``}
    width: ${e=>e.$isTerrain?`125px`:e.$wide?`156px`:`128px`};
    ${e=>e.$minHeight?`min-height: ${e.$minHeight}px;`:``}
    /*Ship Art toggle (item 3, 2026-07-22): hide the whole section (header + icons) but
      keep its grid footprint so the window/watermark never resize while the art shows*/
    ${e=>e.$hidden?`visibility: hidden;`:``}
    margin: ${e=>e.$area?`0`:`2px`};

    -webkit-user-select: none;
    -webkit-touch-callout: none;
    user-select: none;

    border: ${e=>{switch(e.$location){case 0:return`1px solid ${N.colors.line}`;default:return`1px dotted ${N.colors.line}`}}};
`,Bu=M.div`
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
    border-bottom: 1px solid ${N.colors.healthOk};
    overflow: hidden;
    /*lobby pre-battle damage: the bar is the only way to reach a section's Structure,
      which has no icon of its own in the grid - and (OFFICERS_PLAN.md Stage 19) an Expert Targeter's menu or pick*/
    ${e=>e.$damageable?`cursor: pointer;`:``}
    /*A block the officer being moved may go to (officers.isPickTarget, Stage 19): SystemIcon's pick outline, in the Officers
      blue, drawn on the bar's own edge (a 15px bar has no room for it inside)*/
    ${e=>e.$officerPick?`
        cursor: pointer;
        outline: 2px solid ${B.officer.bar};
        outline-offset: 0;
        box-shadow: 0 0 8px ${B.officer.bar};
    `:``}
    /*The lobby's system menu is open on this block (helpers/menuTarget - SystemIcon's outline, user request 2026-10-20), in
      the menu's focus blue, on the bar's edge as the pick outline is*/
    ${e=>e.$menuTarget?`
        outline: 2px solid ${z.focus};
        outline-offset: 0;
        box-shadow: 0 0 8px ${z.focus};
    `:``}

    /*structure health fill - the header line doubles as the section's health bar*/
    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: ${e=>e.$health}%;
        background-color: ${e=>e.$criticals?N.colors.healthCrit:N.colors.healthOk};
    }
`,Vu=M.span`
    position: relative;
    top: 1px; /*nudge the name down to line up with the mono readout (2026-07-22)*/
    z-index: 1;
    font-size: 8px;
    line-height: 1;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    color: ${N.colors.text};
    text-shadow: black 0 0 4px, black 0 0 4px;
    /*A bar with badges after its name (Stage 19: the ✦, an officer's insignia) can run out of room - a Starboard block of 100+
      boxes in a 128px side column. The NAME gives way, with an ellipsis, so the badges and the readout stay whole. The padding
      and the negative margin cancel out: they only give the text shadow room inside the clipped box.*/
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 3px 2px;
    margin: -3px -2px;
`,Hu=M.span`
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 3px;
    min-width: 0;
`,Uu=M.span`
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: 1px;
`,Wu=M.span`
    position: relative;
    z-index: 1;
    flex-shrink: 0; /*the readout never gives way - the name does (SectionName)*/
    font-family: ${N.fonts.mono};
    font-size: 10px;
    line-height: 1;
    white-space: nowrap;
    color: ${e=>e.$destroyed?`transparent`:N.colors.text};
    filter: ${e=>e.$destroyed?`blur(1px)`:`none`};
    text-shadow: black 0 0 6px, black 0 0 6px;
`,Gu=M.div`
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
    flex-grow: 1;
    padding: 1px 0 2px;
`,Ku=class extends _.Component{constructor(e){super(e),this.longPressTimer=null,this.touchActive=!1,this.ignoreNextClick=!1,this.arcShown=!1,this.onStructureMouseOver=this.onStructureMouseOver.bind(this),this.onStructureMouseOut=this.onStructureMouseOut.bind(this),this.onStructureTouchStart=this.onStructureTouchStart.bind(this),this.onStructureTouchMove=this.onStructureTouchMove.bind(this),this.onStructureTouchEnd=this.onStructureTouchEnd.bind(this),this.onStructureTouchCancel=this.onStructureTouchCancel.bind(this),this.onStructureClick=this.onStructureClick.bind(this)}onStructureClick(e){let{ship:t,systems:n}=this.props,r=Xu(n);this.ignoreNextClick?this.ignoreNextClick=!1:r&&window.officers&&officers.picking&&officers.pickTarget(t,r)?(e.stopPropagation(),this.hideStructureArc()):r&&window.officers&&!Gc(t,r)&&officers.canManage(t,r)?(e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay(`SystemClicked`,{ship:t,system:r,element:e.currentTarget,showMenu:!0})):qc(t)?(e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay(`MineDamageClicked`,{ship:t,element:e.currentTarget})):r&&Gc(t,r)&&(e.stopPropagation(),this.hideStructureArc(),window.uiEvents.relay(`SystemClicked`,{ship:t,system:r,element:e.currentTarget,showMenu:!0}))}componentWillUnmount(){this.longPressTimer&&=(clearTimeout(this.longPressTimer),null),this.hideStructureArc()}showStructureArc(){let{ship:e,systems:t}=this.props;this.arcShown=!0,window.uiEvents.relay(`StructureMouseOver`,{ship:e,structure:Xu(t),element:this.headerElement})}hideStructureArc(){this.arcShown&&(this.arcShown=!1,window.uiEvents.relay(`StructureMouseOut`))}onStructureMouseOver(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.showStructureArc())}onStructureMouseOut(e){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||(e.stopPropagation(),this.hideStructureArc())}onStructureTouchStart(e){e.stopPropagation(),this.touchActive=!0,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);let t=e.touches[0];this.touchStartX=t.clientX,this.touchStartY=t.clientY,this.longPressTimer=setTimeout(()=>{this.showStructureArc(),this.longPressTimer=null},400)}onStructureTouchMove(e){if(e.stopPropagation(),!this.longPressTimer)return;let t=e.touches[0];(Math.abs(t.clientX-this.touchStartX)>10||Math.abs(t.clientY-this.touchStartY)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onStructureTouchEnd(e){e.stopPropagation(),this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):(this.ignoreNextClick=!0,this.hideStructureArc()),setTimeout(()=>{this.touchActive=!1},300)}onStructureTouchCancel(e){e.stopPropagation(),this.longPressTimer&&=(clearTimeout(this.longPressTimer),null),this.touchActive=!1,this.hideStructureArc()}render(){let{ship:e,systems:t,location:n,displayLocation:r,area:i,valign:a,justify:o,wide:s,isTerrain:c,minHeight:l,nameOverride:u,hidden:d}=this.props,f=Xu(t),p=qc(e),m=p?battleDamage.mineHealth(e,1):0,h=p?battleDamage.mineMaxHealth(e):0,g=p?m/h*100:f?qu(e,f):0,_=r===void 0?n:r,v=r!==void 0&&r!==n,y=f&&window.officers?officers.postState(e,f.id):null,b=!!(f&&window.officers)&&officers.isPickTarget(e,f),x=!!(f&&window.officers)&&officers.canManage(e,f),S=!!(f&&window.systemEnhancements)&&systemEnhancements.hasAny(e,f.id),C=u||Ru[n]||``,ee=(S||y)&&C===`Starboard`?`Stbd`:C;return(0,P.jsxs)(zu,{$location:n,$area:i,$valign:a,$justify:o,$wide:s,$isTerrain:c,$minHeight:l,$hidden:d,children:[f&&(0,P.jsxs)(Bu,{ref:e=>{this.headerElement=e},$health:g,$criticals:Ju(f),$damageable:Gc(e,f)||qc(e)||x,$officerPick:b,$menuTarget:El(e,f),onClick:this.onStructureClick,onMouseOver:this.onStructureMouseOver,onMouseOut:this.onStructureMouseOut,onTouchStart:this.onStructureTouchStart,onTouchMove:this.onStructureTouchMove,onTouchEnd:this.onStructureTouchEnd,onTouchCancel:this.onStructureTouchCancel,children:[(0,P.jsxs)(Hu,{children:[(0,P.jsx)(Vu,{children:ee}),(S||y)&&(0,P.jsxs)(Uu,{children:[S&&(0,P.jsx)(kl,{title:`Carries a system enhancement`,children:`✦`}),y&&(0,P.jsx)(Pl,{state:y,title:officers.describePost(e,f.id)})]})]}),(0,P.jsxs)(Wu,{$destroyed:g===0,children:[p?m:f.maxhealth-damageManager.getDamage(e,f),`/`,p?h:f.maxhealth,` A`,shipManager.systems.getArmour(e,f)]})]}),(0,P.jsx)(Gu,{children:Qu(t,_,s).map(t=>(0,P.jsx)(Rl,{scs:!0,mirror:v,system:t,ship:e},`system-scs-${n}-${e.id}-${t.id}`))})]})}},qu=(e,t)=>(t.maxhealth-damageManager.getDamage(e,t))/t.maxhealth*100,Ju=e=>shipManager.criticals.hasCriticals(e),Yu=e=>e.name===`structure`,Xu=e=>e.find(Yu),Zu=e=>e.filter(e=>!Yu(e)),Qu=(e,t,n)=>(e=Zu(e),n?td(e):[4,41,42].includes(t)?W(e):[3,31,32].includes(t)?$u(W(e)):[1,2,0].includes(t)?td(e):ed(e)),$u=e=>{let t=[];return e.forEach((e,n)=>{let r=n%3;r===0?t[n+2]=e:r===1?t[n]=e:t[n-2]=e}),t},ed=e=>(e=Zu(e),e.length===3?W(e):e.length===4?td(e):e),td=e=>{e=Zu(e);let t=[];for(;;){let{picked:n,remaining:r}=q(e,4);if(n.length===0)break;e=r,t=t.concat(n)}for(;;){let{picked:n,remaining:r}=q(e,2);if(n.length===0)break;e=r;let i=q(e,2);i.picked.length>0?(e=i.remaining,t=t.concat([n[0],i.picked[0],i.picked[1],n[1]])):(t=t.concat([n[0],e.shift(),e.shift(),n[1]]),t=t.filter(e=>e))}return t=t.concat(e),t},W=e=>{e=Zu(e);let t=[];for(;;){let{picked:n,remaining:r}=K(e,3);if(n.length===0)break;e=r,t=t.concat(n)}for(;;){let{picked:n,remaining:r}=K(e,2);if(n.length===0)break;let{three:i,remainingSystems:a}=G(n,r);e=a,t=t.concat(i)}return t=t.concat(e),t},G=(e,t)=>{let n=K(t,1);return n.picked.length===1?{three:[n.picked[0],e[0],e[1]],remainingSystems:n.remaining}:t.length>0?{three:[t.shift(),e[0],e[1]],remainingSystems:t}:{three:[e[0],e[1]],remainingSystems:t}},K=(e,t=3)=>{let n=e.find(n=>{let r=e.reduce((e,t)=>t.name===n.name?e+1:e,0);return t===1?r===t:r>=t});if(!n)return{picked:[],remaining:e};let r=[];return{picked:r,remaining:e.filter(e=>e.name===n.name&&t>0?(t--,r.push(e),!1):!0)}},q=(e,t=3)=>{let n=e.find(n=>{let r=e.reduce((e,t)=>t.name===n.name?e+1:e,0);return t===1?r===t:r>=t});if(!n)return{picked:[],remaining:e};let r=[],i=[],a=e.filter(e=>e.name!==n.name||(i.push(e),!1));for(var o=Math.ceil(t/2),s=Math.floor(t/2),c=0;c<i.length;c++)c<o||c>=i.length-s?r.push(i[c]):a.unshift(i[c]);return{picked:r,remaining:a}},J={DEW:`#aecdea`,CCEW:N.colors.text,SDEW:`#9ac1e5`,OEW:`#acd7a8`,BDEW:`#8ac785`,"Detect Mines":`#bfa3db`,"Detect Stealth":`#ccb6e2`,DIST:`#e6b98f`,SOEW:N.colors.text,OEW_HOSTILE:`#e49b9b`,"Saved EW":`#e0d39a`,ELINT:`#9ad4cf`},nd=(e,t)=>e===`OEW`&&t&&gamedata.isPlayerInGame()&&!gamedata.isMyorMyTeamShip(t)?J.OEW_HOSTILE:J[e]||N.colors.textAccent,rd=M.div`
    /*WALKERS OF SIGMA-957 (WALKERS_OF_SIGMA_PLAN.md 3.11, Stage 12): $flight is the Mapmaker's
      copy of this panel in the FLIGHT window, which has no SCS grid to sit in - it is a flex
      row beside the FighterList (see ShipWindow's FlightEwBody). grid-area/justify-self are
      inert in a flex parent, but naming them only for the grid keeps the two placements from
      being confused later.*/
    ${e=>e.$flight?``:In`
        grid-area: ew;
        justify-self: center; /*centred in its column, matching the Hit Chart / Notes stack*/
    `}
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px; /*matches the Hit Chart / Notes / Enhancements chrome in game (user 2026-07-19)*/
    box-sizing: border-box;
    background-color: ${N.colors.panelBgGlass};
    border: 1px solid ${N.colors.line};
    padding: 1px 4px 1px;
`,id=M.div`
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
    color: ${N.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -1px -2px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${N.colors.line};
`,ad=M.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    font-size: 9px;
    color: ${N.colors.text};
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
    ${e=>e.$target&&In`
        align-items: center;
        padding-top: 2px;        
    `}

    /*BDEW / Detect Mines rows raise the matching map overlay while hovered (see getShipRows), so
      they carry the same faint affordance as an interactive target name - pointer cursor plus a
      glow. Applied to the whole row because the whole row is the hover target, not just its label.*/
    ${e=>e.$hoverable&&In`
        cursor: pointer;
        &:hover {
            text-shadow: white 0 0 6px;
        }
    `}
`,od=M.div`
    display: flex;
    align-items: baseline;
    gap: 4px;
    flex: 1 1 auto;
    min-width: 0; /*lets RowTarget shrink below its max-content width so the name can wrap*/
`,Y=M.span`
    font-size: 8px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${e=>e.$color||N.colors.textAccent};
    white-space: nowrap;
    margin-left: 0px;
`,sd=M.span`
    font-family: ${N.fonts.mono};
    font-size: 10px;
    margin-right: 2px;
    margin-left: 3px;          
`,cd=M.span`
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
    color: ${N.colors.textAccent};
    ${e=>e.$interactive&&In`
        cursor: pointer;
        &:hover {
            color: ${N.colors.text};
            text-shadow: white 0 0 6px;
        }
    `}
`,ld=class extends _.Component{componentWillUnmount(){this.activeHighlight&&window.webglScene&&(window.uiEvents.relay(`EwTargetHighlight`,{shipId:this.props.ship.id,targetId:this.activeHighlight.targetId,type:this.activeHighlight.type,active:!1}),this.activeHighlight=null),this.activeRangeOverlay&&this.setRangeOverlay(this.activeRangeOverlay,!1)}setRangeOverlay(e,t){window.webglScene&&(window.uiEvents.relay(`EwRangeHover`,{shipId:this.props.ship.id,type:e,active:t}),this.activeRangeOverlay=t?e:null)}onTargetClick(e,t){t.stopPropagation(),window.webglScene&&(shipManager.shouldBeHidden(e)||window.uiEvents.relay(`ScrollToShip`,{shipId:e.id}))}setTargetHighlight(e,t,n){window.webglScene&&(window.uiEvents.relay(`EwTargetHighlight`,{shipId:this.props.ship.id,targetId:e.id,type:t,active:n}),this.activeHighlight=n?{targetId:e.id,type:t}:null)}render(){let{ship:e,flight:t}=this.props;return t?(0,P.jsxs)(rd,{$flight:!0,children:[(0,P.jsx)(id,{children:`Electronic Warfare`}),ud(e),pd(e,this)]}):(0,P.jsxs)(rd,{children:[(0,P.jsx)(id,{children:`Electronic Warfare`}),fd(e,this),pd(e,this)]})}},ud=e=>{let t=[(0,P.jsxs)(ad,{children:[(0,P.jsx)(Y,{$color:nd(`DEW`),children:`DEW`}),(0,P.jsx)(sd,{children:hd(ew.getFlightDEW(e))})]},`dew-scs-${e.id}`)],n=dd(e);return n&&t.push(n),t},dd=e=>{if(!gamedata.isPlayerInGame()||!gamedata.isMyorMyTeamShip(e))return null;let t=ew.getSavedEwAllowance(e);if(t<=0)return null;let n=ew.isLateEwWindowOpen(e)||ew.isLateEwPhase()&&gamedata.isMyShip(e)?`${hd(ew.getLateEwRemaining(e))} / ${hd(t)}`:hd(t);return(0,P.jsxs)(ad,{children:[(0,P.jsx)(Y,{$color:nd(`Saved EW`),children:`Saved EW`}),(0,P.jsx)(sd,{children:n})]},`savedew-scs-${e.id}`)},fd=(e,t)=>{let n=[],r=!!window.webglScene,i=e=>r?{$hoverable:!0,onMouseEnter:()=>t.setRangeOverlay(e,!0),onMouseLeave:()=>t.setRangeOverlay(e,!1)}:{};if(n.push((0,P.jsxs)(ad,{children:[(0,P.jsx)(Y,{$color:nd(`DEW`),children:`DEW`}),(0,P.jsx)(sd,{children:hd(ew.getDefensiveEW(e))})]},`dew-scs-${e.id}`)),r&&ew.hasElintModules(e)&&gamedata.isMyorMyTeamShip(e)){let t=ew.getElintModuleSplit(e),r=0;ew.getElintModules(e).forEach(e=>{r+=e.elintLost||0});let i=gamedata.gamephase==1?`${hd(t.freeM)} / ${hd(t.M)}`:r>0?`${hd(t.M)} (lost ${r})`:hd(t.M);n.push((0,P.jsxs)(ad,{children:[(0,P.jsx)(Y,{$color:nd(`ELINT`),children:`ELINT Modules`}),(0,P.jsx)(sd,{children:i})]},`elint-scs-${e.id}`))}var a=Math.max(0,ew.getCCEW(e)-ew.getDistruptionEW(e));a>0&&n.push((0,P.jsxs)(ad,{children:[(0,P.jsx)(Y,{$color:nd(`CCEW`),children:`CCEW`}),(0,P.jsx)(sd,{children:hd(a)})]},`ccew-scs-${e.id}`));let o=ew.getBDEW(e)*.25,s=ew.getDetectSEW(e),c=ew.getDetectMEW(e);shipManager.hasSpecialAbility(e,`ConstrainedEW`)&&(o=ew.getBDEW(e)*.2),o&&n.push((0,P.jsxs)(ad,{...i(`BDEW`),children:[(0,P.jsx)(Y,{$color:nd(`BDEW`),children:`BDEW`}),(0,P.jsx)(sd,{children:hd(o)})]},`bdew-scs-${e.id}`)),c&&n.push((0,P.jsxs)(ad,{...i(`MDEW`),children:[(0,P.jsx)(Y,{$color:nd(`Detect Mines`),children:`Detect Mines`}),(0,P.jsx)(sd,{children:hd(c)})]},`DetectMEW-scs-${e.id}`)),s&&n.push((0,P.jsxs)(ad,{children:[(0,P.jsx)(Y,{$color:nd(`Detect Stealth`),children:`Detect Stealth`}),(0,P.jsx)(sd,{children:hd(s)})]},`DetectSEW-scs-${e.id}`));let l=dd(e);return l&&n.push(l),n},pd=(e,t)=>{let n=!!window.webglScene;return e.EW.filter(e=>e.turn===gamedata.turn).filter(e=>e.type===`OEW`||e.type===`DIST`||e.type===`SOEW`||e.type===`SDEW`).map(r=>{let i=gamedata.getShip(r.targetid);return(0,P.jsxs)(ad,{$target:!0,children:[(0,P.jsxs)(od,{children:[(0,P.jsx)(Y,{$color:nd(r.type,e),children:r.type}),(0,P.jsx)(cd,{$interactive:n,title:void 0,onClick:n?t.onTargetClick.bind(t,i):void 0,onMouseEnter:n?()=>t.setTargetHighlight(i,r.type,!0):void 0,onMouseLeave:n?()=>t.setTargetHighlight(i,r.type,!1):void 0,children:i.name})]}),(0,P.jsx)(sd,{children:md(r,e)})]},`${r.type}-scs-${e.id}-${r.targetid}`)})},md=(e,t)=>{switch(e.type){case`SDEW`:if(shipManager.hasSpecialAbility(t,`ConstrainedEW`)){let t=e.amount*.333;return t=Math.round(t*3)/3,hd(t)}return hd(e.amount*.5);case`DIST`:return shipManager.hasSpecialAbility(t,`ConstrainedEW`)?hd(e.amount/4):hd(e.amount/3);case`OEW`:return hd(Math.max(0,e.amount-ew.getDistruptionEW(t)));default:return hd(e.amount)}},hd=e=>Math.round(e*100)/100,gd=()=>!!window.gamedata&&window.gamedata.gamephase===-2,_d=e=>{let t=!1,n=0;for(let r in e.damage){let i=e.damage[r];i.damageclass===`HyperspaceJump`?t=!0:n+=Math.max(0,i.damage-i.armour)}return t&&n<e.maxhealth},vd=M.div`
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
`,yd=M.div`
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;
    height: 100%;
    background-image: url(${e=>e.$img});
    background-size: 80%;
    background-position: center;
    background-repeat: no-repeat;
    filter: ${e=>e.$destroyed?`blur(1px)`:`none`};
    opacity: ${e=>e.$destroyed?`0.5`:`1`};
`,X=M.div`
    position: absolute;
    top: 55px;
    left: 9px;
    width: 96px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    padding: 5px 0;
    background-color: black;
    color: ${e=>e.$color};
    border: 1px solid ${e=>e.$color};
    z-index: 2;
    pointer-events: none;
    opacity: 0.7;
`,bd=M.div`
    position: absolute;
    top: -5px;
    left: -5px;
    z-index: 3;
    pointer-events: none;
`,xd=M.div`
    display: flex;
    width: 100%;
    flex-wrap: wrap;
    height: 50%;
    justify-content: space-evenly;
    align-items: flex-start;
`,Sd=M(xd)`
    height: calc(50% - 16px);
    align-items: flex-end;
`,Cd=M.div`
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    width: calc(100% - 4px);
    height: 16px;
    box-sizing: border-box;
    background-color: black;
    cursor: ${e=>e.$clickable?`pointer`:`default`};
    color: ${e=>e.$health===0?`transparent`:`white`};
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
        width:  ${e=>e.$health}%;
        height: 100%;
        left: 0;
        bottom: 0;
        z-index: 0;
        background-color: ${e=>e.$docked?`#00b8e6`:e.$criticals?e.$criticalsBenign?`#00ccff`:`#ed6738`:`#427231`};
        border: 1px solid black;
    }
`,wd=M.div`
    z-index: 1;
`,Td=class extends _.Component{onSystemMouseOver(e){if(gd()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||e.nativeEvent&&e.nativeEvent.sourceCapabilities&&e.nativeEvent.sourceCapabilities.firesTouchEvents)return;let{ship:t,fighter:n}=this.props;window.uiEvents.relay(`SystemMouseOver`,{ship:t,system:t,craft:n,element:e.target})}onSystemMouseOut(){gd()||this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay(`SystemMouseOut`)}onFighterTouchStart(e){if(gd())return;this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);let t=e.currentTarget,n=e.touches[0];this.touchStartX=n.clientX,this.touchStartY=n.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:e,fighter:n}=this.props;window.uiEvents.relay(`SystemMouseOver`,{ship:e,system:e,craft:n,element:t,showInfo:!0}),this.longPressTimer=null},400)}onFighterTouchMove(e){if(!this.longPressTimer)return;let t=e.touches[0],n=t.clientX-this.touchStartX,r=t.clientY-this.touchStartY;(Math.abs(n)>10||Math.abs(r)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onFighterTouchCancel(e){this.longPressTimer&&=(clearTimeout(this.longPressTimer),null),this.touchActive=!1,window.uiEvents.relay(`SystemMouseOut`)}onFighterTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay(`SystemMouseOut`),setTimeout(()=>{this.touchActive=!1},300)}canApplyPreBattleDamage(){let{ship:e}=this.props,t=window.gamedata&&typeof gamedata.fleetIsCommitted==`function`&&gamedata.fleetIsCommitted();return gd()&&!t&&!!e&&e.userid!=0&&!!e.flight}onHealthBarClick(e){this.canApplyPreBattleDamage()&&(e.stopPropagation(),e.preventDefault(),window.uiEvents.relay(`FighterDamageClicked`,{ship:this.props.ship,fighter:this.props.fighter,element:e.currentTarget}))}render(){let{ship:e,fighter:t}=this.props,n=shipManager.systems.isDestroyed(e,t),r=shipManager.criticals.isDockedFighter(t),i=!r&&shipManager.criticals.isSplitLaunchedFighter(t),a=!r&&!i&&shipManager.criticals.isDisengagedFighter(t),o=shipManager.criticals.isCutOffFighter(t),s=null;n?s=r?(0,P.jsx)(X,{$color:`#00b8e6`,children:`DOCKED`}):i?(0,P.jsx)(X,{$color:`#00b8e6`,children:`SPLIT`}):a?(0,P.jsx)(X,{$color:`#ff8c00`,children:`DROPOUT`}):_d(t)?(0,P.jsx)(X,{$color:`#cc8500`,children:`JUMPED`}):(0,P.jsx)(X,{$color:`#ff5252`,children:`DESTROYED`}):o&&(s=(0,P.jsx)(X,{$color:`#ff5252`,children:`CUT OFF`}));let c=this.canApplyPreBattleDamage(),l=c?battleDamage.fighterHealth(e,1)/t.maxhealth*100:Ed(e,t),u=c?`${battleDamage.fighterHealth(e,1)} / ${t.maxhealth}`:`${t.maxhealth-damageManager.getDamage(e,t)} / ${t.maxhealth}`,d=window.officers?officers.postState(e,t.id):null;return(0,P.jsxs)(vd,{$docked:r,onMouseOver:this.onSystemMouseOver.bind(this),onMouseOut:this.onSystemMouseOut.bind(this),onTouchStart:this.onFighterTouchStart.bind(this),onTouchMove:this.onFighterTouchMove.bind(this),onTouchEnd:this.onFighterTouchEnd.bind(this),onTouchCancel:this.onFighterTouchCancel.bind(this),children:[(0,P.jsxs)(yd,{$destroyed:n,$img:window.AssetManager.getSmartImagePath(t.iconPath),children:[(0,P.jsx)(xd,{children:jd(e,t,kd(t),n)}),(0,P.jsx)(Sd,{children:jd(e,t,Ad(t),n)}),(0,P.jsx)(Cd,{$health:l,$criticals:Dd(t),$criticalsBenign:Od(t),$docked:r,$clickable:c,title:c?`Apply pre-battle damage to this flight`:void 0,onClick:this.onHealthBarClick.bind(this),children:(0,P.jsx)(wd,{children:u})})]}),s,d&&(0,P.jsx)(bd,{children:(0,P.jsx)(Pl,{state:d,title:officers.describePost(e,t.id)})})]})}},Ed=(e,t)=>(t.maxhealth-damageManager.getDamage(e,t))/t.maxhealth*100,Dd=e=>shipManager.criticals.hasCriticals(e),Od=e=>shipManager.criticals.hasOnlyCritical(e,`LaunchedThisTurn`,!1),kd=e=>e.systems.filter(e=>e.location==1),Ad=e=>e.systems.filter(e=>e.location!=1),jd=(e,t,n,r)=>n.map((n,i)=>(0,P.jsx)(Rl,{$destroyed:r,fighter:!0,scs:!0,system:n,ship:e},`system-scs-fighter${t.id}-${e.id}-${n.id}-${i}`)),Md=M.div`
    display: flex;
    flex-wrap: wrap;
    width: 100%;
    justify-content: space-around;
`,Nd=class extends _.Component{render(){let{ship:e}=this.props;return(0,P.jsx)(Md,{children:Pd(e)})}},Pd=e=>e.systems.map((t,n)=>(0,P.jsx)(Td,{fighter:t,ship:e},`flight-${e.id}-${n}`)),Fd=[3,31,32],Id=[1,0,2],Ld=[4,41,42],Rd=M.div`
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: stretch;
    gap: 5px;
`,zd=M.div`
    display: flex;
    flex-direction: column;
    /*side columns centre against the Front/Primary/Aft stack, mimicking the ship*/
    justify-content: ${e=>e.$side?`center`:`flex-start`};
    gap: 5px;
    flex: 0 1 auto;
    min-width: 0;
`,Bd=M.div`
    min-width: 110px;
    max-width: 100%;
    box-sizing: border-box;
    border: 1px dotted ${N.colors.line};
    padding: 3px 5px;
`,Vd=M.div`
    font-size: 9px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: ${N.colors.text};
    background-color: rgba(73, 103, 145, 0.25);
    margin: -3px -5px 2px;
    padding: 3px 5px 2px;
    border-bottom: 1px solid ${N.colors.line};
`,Hd=M.div`
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 10px;
    color: ${N.colors.textAccent};
    padding: 1px 0;
    border-bottom: 1px solid rgba(73, 103, 145, 0.35);

    &:last-child {
        border-bottom: none;
    }
`,Ud=M.span`
    font-family: ${N.fonts.mono};
    color: ${N.colors.text};
    flex-shrink: 0;
`,Wd=(e,t)=>(0,P.jsxs)(Bd,{children:[(0,P.jsx)(Vd,{children:t.name}),[...t.entries].sort((e,t)=>t.chance-e.chance).map((n,r)=>(0,P.jsxs)(Hd,{children:[(0,P.jsx)(`span`,{children:n.name}),(0,P.jsxs)(Ud,{children:[n.chance,`%`]})]},`hitchart-${e.id}-${t.location}-${r}`))]},`hitchart-${e.id}-${t.location}`),Gd=class extends _.Component{render(){let{ship:e}=this.props,t=lu(e);if(t.length===0)return null;let n={};t.forEach(e=>{n[e.location]=e});let r=t=>t.filter(e=>n[e]).map(t=>Wd(e,n[t])),i=r(Fd),a=r(Id),o=r(Ld);return(0,P.jsxs)(Rd,{children:[i.length>0&&(0,P.jsx)(zd,{$side:!0,children:i}),a.length>0&&(0,P.jsx)(zd,{children:a}),o.length>0&&(0,P.jsx)(zd,{$side:!0,children:o})]})}},Kd=e=>e.split(` `).map(e=>e.charAt(0).toUpperCase()+e.slice(1)).join(` `),qd=e=>{let t=[];if(!e||e.flight)return t;let n=e.fighters||{};if(Object.keys(n).length>0){let r={};if(shipManager.systems.shipHasRestrictedHangar(e)){let t=shipManager.systems.getReservedFighterComposition(e);for(let e=0;e<t.length;e++){let n=t[e].category;r[n]||(r[n]=[]);let i=r[n],a=!1;for(let n=0;n<i.length;n++)if(i[n].phpclass===t[e].phpclass){i[n].count+=t[e].count,a=!0;break}a||i.push({category:t[e].category,phpclass:t[e].phpclass,displayName:t[e].displayName,count:t[e].count,isGroup:t[e].isGroup})}}for(let e in n){let i=n[e],a=Kd(e),o=r[e];if(o&&(e===`heavy`||e===`medium`||e===`light`)){let e=i;for(let n=0;n<o.length;n++){let r=Math.min(o[n].count,e);r<=0||(o[n].isGroup?t.push(r+` `+o[n].displayName+`s`):t.push(r+` `+o[n].displayName+` `+a+` Fighters`),e-=r)}e>0&&t.push(e+` `+a+` Fighters`)}else if(e===`normal`)t.push(i+` Fighters`);else if(e===`superheavy`||e===`heavy`||e===`medium`||e===`light`||e===`ultralight`)t.push(i+` `+a+` Fighters`);else if(e===`shuttles`||e===`minesweeping shuttles`||e===`cargo shuttles`||e===`lifeboats`||e===`medical shuttles`||e===`presidential shuttle`||e===`yacht`)continue;else t.push(i+` `+a)}}let r=shipManager.systems.getDefaultShuttleComposition(e);for(let e=0;e<r.length;e++)t.push(r[e].count+` `+r[e].type);return t},Jd=M.div`
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 10px;
    line-height: 1.4;
    color: ${N.colors.textAccent};
    ${e=>e.$grid?`
    grid-area: ew;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 1;
    width: 150px;`:e.$full?`
    width: 100%;
    padding: 4px;`:`
    flex: 0 0 auto;
    width: 200px;
    padding: 4px;`}
`,Yd=M.div`
    background-color: ${N.colors.panelBgGlass};
    /*$gold: the Enhancements block matches its bronze header border (user request
      2026-07-18) so the whole panel reads as the gold-accented one*/
    border: 1px dotted ${e=>e.$gold?N.colors.enhLine:N.colors.line};
    padding: 0 8px 3px;
    /*$gold: the enhancement lines in the gold body text on every window (user request 2026-10-11).
      The grid's EnhancementsPanel got it from its EnhArea wrapper, but a flight's block sits in
      the blue Rail and read blue; set here, both read the same.*/
    ${e=>e.$gold?`color: ${N.colors.enhText};`:``}
`,Xd=M.div`
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
    color: ${e=>e.$gold?N.colors.enhTitle:N.colors.text};
    /*shaded header-bar blue (same as the hit chart section names) so the block
      headers stand out against the glass panels (feedback 2026-07-17).
      $gold: muted bronze variant for the Enhancements blocks (user request
      2026-07-18) - stands out from the blue chrome without going garish.*/
    background-color: ${e=>e.$gold?N.colors.enhBg:`rgba(73, 103, 145, 0.25)`};
    border-bottom: 1px solid ${e=>e.$gold?N.colors.enhLine:N.colors.line};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,Zd=M.div`
    padding: 1px 0;
`,Qd=M.div`
    padding: 1px 0;
    font-weight: bold;
    /*font-style: italic;*/
    color: ${N.colors.custom};
`,$d=M.div`
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 4px;
    padding-top: 1px;
`,ef=M.span`
    font-size: 10px;
    color: ${N.colors.textAccent};
    white-space: nowrap;
    margin-left: 5px;    
`,tf=M.span`
    font-family: ${N.fonts.mono};
    font-size: 10px;
    /*$changed: this turn's live cost differs from the ship's own blueprint figure -
      attached ships, docked LCVs, a reversing submarine (user request 2026-07-26).
      Flagged in the custom-content yellow so a modified cost is never misread as the
      hull's own stat.*/
    color: ${e=>e.$changed?N.colors.custom:N.colors.text};
    margin-right: 5px;
`,nf=M.div`
    width: 150px;
    box-sizing: border-box;
    ${e=>e.$bare?`
    padding: 0;`:`
    background-color: ${N.colors.panelBgGlass};
    border: 1px dotted ${N.colors.line};
    padding: 2px 4px 3px;`}
`,rf=M.div`
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
    color: ${N.colors.text};
    /*shaded header-bar blue, matching BlockTitle / the ctrl buttons*/
    background-color: rgba(73, 103, 145, 0.25);
    margin: -2px -4px 2px;
    padding: 0 4px;
    border-bottom: 1px solid ${N.colors.line};
`,af=M.span`
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
        background-color: ${N.colors.text};
    }
    i:nth-child(1) { height: 45%; }
    i:nth-child(2) { height: 70%; }
    i:nth-child(3) { height: 100%; }
`;M.div`
    text-align: center;
    font-size: 10px;
    color: ${N.colors.warning};
    padding-top: 2px;
`;var of=M.div`
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
    color: ${N.colors.enhTitle};
    background-color: ${N.colors.enhBg};
    border-bottom: 1px solid ${N.colors.enhLine};
    margin: 0 -8px 3px;
    padding: 0 6px 0 4px;
`,sf=M.div`
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
    color: ${N.colors.enhText};
`,cf=e=>typeof e==`number`?e.toFixed(2):e,lf=(e,t)=>window.officers?officers.displayStat(e,t):e[t],uf=(e,t)=>window.officers&&e.flight?t===`ob`?officers.shuntOb(e):officers.shuntThrust(e):0,df=(e,t)=>e*5+`/`+t*5,ff=(e,t)=>e+` (`+cf(t)+`)`,pf=e=>{let t=window.shipManager;if(!t)return null;let n={},r=t.systems?t.systems.getSystemByName(e,`CnC`):null,i=r&&t.criticals?t.criticals.hasCritical(r,`ProfileIncreased`):0;n.profile=df(e.forwardDefense+i,e.sideDefense+i),n.profileChanged=i!==0;let a=e.iniativeadded||0;n.initiative=(e.iniativebonus||0)+a,n.initiativeChanged=a!==0;let o=t.movement;if(o&&typeof o.getTurnCost==`function`&&e.movement&&e.movement.length>0){let t=o.getSpeed(e),r=o.getDockedLcvTurnSurcharge(e),i=o.getTurnDelayCost(e),a=o.getTurnCost(e);e.submarine&&o.isGoingBackwards(e)&&(a*=1.33);let s=Math.max(1,Math.ceil(t*a))+r,c=Math.ceil(t*i),l=o.applyCrewTurnDelay(e,typeof o.getQuartermasterTurnDelay==`function`?o.getQuartermasterTurnDelay(e,c):c)+r;n.turnCost=ff(s,a),n.turnDelay=ff(l,i),n.turnCostChanged=n.turnCost!==ff(Math.max(1,Math.ceil(t*e.turncost)),e.turncost),n.turnDelayChanged=n.turnDelay!==ff(Math.ceil(t*e.turndelaycost),e.turndelaycost)}return n},Z=({ship:e,live:t,bare:n})=>{let r=!e.base,i=t?pf(e):null;return(0,P.jsxs)(nf,{$bare:n,children:[!n&&(0,P.jsxs)(rf,{children:[(0,P.jsxs)(af,{children:[(0,P.jsx)(`i`,{}),(0,P.jsx)(`i`,{}),(0,P.jsx)(`i`,{})]}),`Ship Stats`]}),r&&(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Turn cost`}),(0,P.jsx)(tf,{$changed:!!(i&&i.turnCostChanged),children:i&&i.turnCost?i.turnCost:cf(lf(e,`turncost`))})]}),r&&(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Turn delay`}),(0,P.jsx)(tf,{$changed:!!(i&&i.turnDelayChanged),children:i&&i.turnDelay?i.turnDelay:cf(e.turndelaycost)})]}),r&&(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Accel/decel`}),(0,P.jsx)(tf,{children:lf(e,`accelcost`)})]}),r&&(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Pivot`}),(0,P.jsx)(tf,{children:lf(e,`pivotcost`)})]}),r&&(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Roll`}),(0,P.jsx)(tf,{children:lf(e,`rollcost`)})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Profile - Front / Side`}),(0,P.jsx)(tf,{$changed:!!(i&&i.profileChanged),children:i?i.profile:window.officers?df(officers.displayStat(e,`forwardDefense`),officers.displayStat(e,`sideDefense`)):df(e.forwardDefense,e.sideDefense)})]}),r&&(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Initiative`}),(0,P.jsx)(tf,{$changed:!!(i&&i.initiativeChanged),children:i?i.initiative:window.officers?officers.displayIniativeBonus(e):e.iniativebonus})]})]})},mf=({ship:e})=>{let t=gf(e.enhancementTooltip),n=hf(e);return t.length===0&&!n?null:(0,P.jsx)(sf,{children:(0,P.jsxs)(Yd,{$gold:!0,children:[(0,P.jsx)(of,{children:`Enhancements`}),t.map((e,t)=>(0,P.jsx)(Zd,{children:e},`enh-${t}`)),n&&(0,P.jsx)(Zd,{children:n},`officers`)]})})},hf=e=>window.officers&&e?officers.summaryLine(e):null,gf=e=>(e||``).split(/<br\s*\/?>/i).map(e=>e.replace(/<[^>]*>/g,``).replace(/&nbsp;/g,` `).trim()).filter(Boolean),_f=class extends _.Component{render(){let{ship:e,full:t,grid:n,hideEnhancements:r}=this.props,i=qd(e),a=gf(e.notes),o=r?[]:gf(e.enhancementTooltip),s=r?null:hf(e),c=[];if(e.limited&&e.limited!=0&&c.push(`Limited: `+e.limited+`%`),e.variantOf){let t=e.occurence?e.occurence.charAt(0).toUpperCase()+e.occurence.slice(1)+` `:``;c.push(t+`variant of `+e.variantOf)}e.isd&&c.push(`In-Service (ISD): `+e.isd);let l=null;e.unofficial===`S`?l=`Semi-Custom`:e.unofficial&&(l=`Custom`);let u=a.length>0||c.length>0||l;return(0,P.jsxs)(Jd,{$full:t,$grid:n,children:[e.flight&&(0,P.jsxs)(Yd,{children:[(0,P.jsx)(Xd,{children:`Flight Stats`}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Armor F/S/A`}),(0,P.jsx)(tf,{children:shipManager.systems.getFlightArmour(e)})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Offensive bonus`}),(0,P.jsx)(tf,{children:(lf(e,`offensivebonus`)+uf(e,`ob`))*5})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Profile - Front / Side`}),(0,P.jsxs)(tf,{children:[e.forwardDefense*5,`/`,e.sideDefense*5]})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Thrust`}),(0,P.jsx)(tf,{children:lf(e,`freethrust`)+uf(e,`thrust`)})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Turn Cost / Delay`}),(0,P.jsxs)(tf,{children:[e.turncost,` / `,e.turndelaycost==0?0:e.turndelaycost]})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Accel. / Pivot / Roll`}),(0,P.jsxs)(tf,{children:[e.accelcost,` / `,e.pivotcost,` / `,e.rollcost]})]}),(0,P.jsxs)($d,{children:[(0,P.jsx)(ef,{children:`Initiative`}),(0,P.jsx)(tf,{children:lf(e,`iniativebonus`)})]})]}),i.length>0&&(0,P.jsxs)(Yd,{children:[(0,P.jsx)(Xd,{children:`Hangar Capacity`}),i.map((e,t)=>(0,P.jsx)(Zd,{children:e},`comp-${t}`))]}),u&&(0,P.jsxs)(Yd,{children:[(0,P.jsx)(Xd,{children:`Notes`}),a.map((e,t)=>(0,P.jsx)(Zd,{children:e},`note-${t}`)),c.map((e,t)=>(0,P.jsx)(Zd,{children:e},`meta-${t}`)),l&&(0,P.jsx)(Qd,{children:l})]}),(o.length>0||s)&&(0,P.jsxs)(Yd,{$gold:!0,children:[(0,P.jsx)(of,{children:`Enhancements`}),o.map((e,t)=>(0,P.jsx)(Zd,{children:e},`enh-${t}`)),s&&(0,P.jsx)(Zd,{children:s},`officers`)]})]})}},vf=400,yf=M.div`
    display: flex;
    align-items: flex-start;
    gap: 2px;
    padding: 4px 4px 0 0;
`,bf=M.div`
    flex: 0 1 auto;
    min-width: 0;
    width: max-content;
    max-width: ${vf}px;
`,xf=M.div.attrs({"data-ship-window":``})`
    display: flex;
    flex-direction: column;
    position: absolute;
    ${e=>e.$isMyTeam?`left: 50px; 
 top: 50px;`:`right: 50px; 
 top: 50px;`}
    width: ${e=>e.$variant===`terrain`?`250px`:e.$variant===`flight`||e.$variant===`flightEw`?`auto`:`fit-content`};
    max-width: ${e=>e.$variant===`flight`?`400px`:e.$variant===`flightEw`?`574px`:e.$variant===`flightLobby`?`620px`:`unset`};
    height: auto;
    border: 1px solid ${N.colors.line};
    background-color: ${N.colors.windowBg};
    opacity: 0.95;
    z-index: 10001;
    pointer-events: auto; /*the lobby mounts windows inside a pointer-events: none fixed overlay*/
    overflow: visible; /*lets the Hit Chart / Notes popup extend past the window; the watermark is clipped by the body instead*/
    box-shadow: 5px 5px 10px black;
    font-size: 10px;
    color: ${N.colors.text};
    font-family: ${N.fonts.body};

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
        ${e=>e.$isMyTeam?`left: 4px; 
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
        width: ${e=>e.$variant===`terrain`?`250px`:`max-content`};
        max-width: ${e=>e.$variant===`flight`?`400px`:e.$variant===`flightEw`?`574px`:e.$variant===`flightLobby`?`620px`:`none`};
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
`,Sf=M.div`
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
    background-color: ${N.colors.panelBg};
    border-bottom: 1px solid ${N.colors.line};
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
`,Cf=M.span`
    font-size: 11px;
    line-height: 26px; /*centres the shared baseline within the 26px header bar*/
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0;
    flex-shrink: 1; /*long flight names ellipsise instead of pushing past the ✕*/
    color: ${e=>e.$tint||N.colors.text};
`,wf=M.span`
    font-size: 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    color: ${N.colors.textAccent};
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 0; /*allow flex shrink so the ellipsis can engage*/
    flex-shrink: 3; /*the class gives way before the ship name does*/
`,Tf=M.div`
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
    color: ${N.colors.line};
    ${Hn}
`,Ef=M.div`
    position: sticky;
    bottom: 0;
    align-self: ${e=>e.$mirror?`flex-start`:`flex-end`};
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    /*$overlap: draw the mark ON TOP of the row above (a status banner) instead of taking a
      row of its own - the negative margin is exactly the grip's own height, so the row above
      keeps the window's bottom edge. See renderStatusStrip.*/
    ${e=>e.$overlap?`margin-top: -16px;`:``}
    box-sizing: border-box;
    z-index: 5;
    cursor: ${e=>e.$mirror?`nesw-resize`:`nwse-resize`};
    /*the grip owns the gesture: without this a finger drag scrolls the window/page instead*/
    touch-action: none;

    &::after { /*three diagonal rules clipped to the corner triangle - the standard grip mark*/
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(${e=>e.$mirror?`45deg`:`315deg`}, ${N.colors.line} 0 1.5px, transparent 1.5px 4px);
        clip-path: ${e=>e.$mirror?`polygon(0 0, 0 100%, 100% 100%)`:`polygon(100% 0, 100% 100%, 0 100%)`};
        opacity: 0.85;
    }

    &::before { /*finger pad - see the note above*/
        content: "";
        position: absolute;
        top: -${e=>e.$pad}px;
        bottom: 0;
        left: ${e=>e.$mirror?`0`:`-`+e.$pad+`px`};
        right: ${e=>e.$mirror?`-`+e.$pad+`px`:`0`};
    }

    &:hover::after {
        opacity: 1;
    }
`,Df=M.div`
    grid-area: ctrl;
    justify-self: center;
    align-self: start;
    position: relative; /*above the watermark + ship-click underlay*/
    z-index: 2;
    display: flex;
    flex-direction: column;
    ${e=>e.$compact?`width: 100%; align-items: center; margin-bottom: 5px;`:`align-items: stretch;`}
    gap: 4px;
`,Of=M.div`
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
    min-width: ${e=>e.$wide?`150px`:`130px`};
    border: 1px solid ${N.colors.line};
    /*idle fill = the shaded header-bar blue (same as the hit chart section names)
      so the chrome buttons read as section headers (feedback 2026-07-17)*/
    background-color: ${e=>e.$active?`rgba(198, 226, 255, 0.12)`:`rgba(73, 103, 145, 0.25)`};
    color: ${N.colors.text}; /*white like the Ship Stats title (feedback round 3)*/
    font-size: 8px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    white-space: nowrap;
    ${Hn}
`,kf=M.span`
    font-size: 12px;
    line-height: 1;
    color: inherit;
`,Af=M.span`
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
`,jf=M.div`
    position: absolute;
    top: ${e=>e.$top||78}px;
    /*left edge aligns with the control buttons (measured - they sit centred in the
      grid's ctrl column, not at the window's 6px margin); feedback 2026-07-19*/
    left: ${e=>e.$left==null?6:e.$left}px;
    /*$fit (Notes): size to content instead of spanning the window*/
    ${e=>e.$fit?`right: auto; width: fit-content; max-width: calc(100% - 12px);`:`right: 6px;`}
    /*Notes popup never narrower than the 130px Notes button it drops from (both
      border-box), so short notes still read as one block under the button*/
    ${e=>e.$notes?`min-width: 130px;`:``}
    z-index: 20;
    max-height: 70vh;
    overflow-y: auto;
    box-sizing: border-box;
    background-color: ${N.colors.panelBg};
    border: 1px solid ${N.colors.line};
    box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.7);
    /*Notes ($notes) trims the bottom gap to ~5px (feedback 2026-07-19; paired with
      ShipInfo dropping its trailing blank line); Hit Chart keeps the extra bottom
      padding so the last chart rows never look clipped*/
    padding: 5px 5px ${e=>e.$notes?`5px`:`10px`};
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
`,Mf=M.div`
    position: relative;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    grid-template-areas: ${e=>e.$areas};
    justify-content: center;
    gap: 8px;
    /*staggered big base: the two rows meet exactly on Primary's centre line (StaggerCell)*/
    ${e=>e.$staggered?`row-gap: 0;`:``}
    padding: 5px 5px 5px 5px;
    box-sizing: border-box;
    width: 100%;
    overflow: hidden; /*clips the watermark now that the window itself is overflow: visible*/
`,Nf=M.div`
    grid-area: ${e=>e.$area};
    display: flex;
    flex-direction: column;
    gap: 8px; /*SectionGrid's own gap, so a cell keeps the grid's spacing*/
    align-items: ${e=>e.$side===`left`?`flex-end`:e.$side===`right`?`flex-start`:`center`};
    justify-content: ${e=>e.$upper?`flex-end`:`flex-start`};
    ${e=>e.$side?`padding-${e.$upper?`bottom`:`top`}: 4px;`:e.$upper?``:`padding-top: calc(var(--fv-prim-half, 0px) + 8px);`}
`,Pf=M.div`
    margin-bottom: calc(-1 * var(--fv-prim-half, 0px));
`,Ff=M.div`
    display: flex;
    justify-content: center;
    align-self: stretch;
    ${e=>e.$top?`margin-bottom: auto;`:``}
`,If=M.div`
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    width: 100%;
`,Lf=M.div`
    flex: 1 1 auto;
    min-width: 120px; /*at least one fighter icon column*/
    max-width: 400px;
`,Rf=M.div`
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
    transform: translate(-50%, calc(-50% + ${e=>e.$offsetVar?`var(${e.$offsetVar}, 0px)`:`${e.$offsetY||0}px`})) rotate(-90deg);
    background-image: ${e=>`url(${e.$img})`};
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    filter: ${e=>e.$art?`none`:`grayscale(1) brightness(2.2)`};
    opacity: ${e=>e.$art?1:.75};
    pointer-events: none;
    z-index: 0;
`,zf=M.div`
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
`,Bf=M.div`
    width: 100%;
    box-sizing: border-box;
    /*equal top/bottom: the old 2px-top/3px-bottom made the text sit visibly high in
      the tinted strip (the border-top reads as a separator line, not banner fill,
      so it doesn't compensate). At 9px uppercase every half-pixel shows.

      $grip: the resize grip is drawn in this banner's corner (renderStatusStrip), so the
      16px mark plus the usual 6px gutter is reserved - on BOTH sides, because a one-sided
      reserve would push the centred text off the window's midline, and which corner the
      grip sits on follows the dock.*/
    padding: 3px ${e=>e.$grip?`22px`:`6px`};
    text-align: center;
    font-size: 9px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: ${e=>e.$color||N.colors.warning};
    background-color: ${e=>e.$bg||`rgba(225, 176, 0, 0.10)`};
    border-top: 1px solid ${N.colors.line};
    flex-shrink: 0;
`,Vf=M.div`
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
`,Q=M.div`
    position: relative;
    box-sizing: border-box;
    width: 50px;
    height: 50px;
    margin: auto;
    border: 1px solid ${N.colors.line};
    background-color: black;
    color: #e3c182;
    font-family: ${N.fonts.body};
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
`,Hf={1:`fwd`,2:`aft`,0:`prim`,3:`left`,4:`right`,31:`lfwd`,41:`rfwd`,32:`laft`,42:`raft`},Uf={3:4,4:3,31:41,41:31,32:42,42:32},Wf={fwd:`end`,aft:`center`,prim:`center`,left:`center`,right:`center`,lfwd:`start`,rfwd:`start`,laft:`end`,raft:`end`},Gf={left:`end`,lfwd:`end`,laft:`end`,right:`start`,rfwd:`start`,raft:`start`},Kf=[1,3,31,32,0,4,41,42,2],qf=[1,3,31,0,4,41,32,2,42],Jf=[31,32,41,42],Yf=`"lup  cup  rup" "llo  clo  rlo"`,Xf=[{area:`lup`,side:`left`,upper:!0,stack:[`lfwd`,`left`]},{area:`cup`,upper:!0,stack:[`fwd`,`prim`]},{area:`rup`,side:`right`,upper:!0,stack:[`rfwd`,`right`]},{area:`llo`,side:`left`,stack:[`laft`]},{area:`clo`,stack:[`aft`]},{area:`rlo`,side:`right`,stack:[`raft`]}],Zf=[3,4,31,41,32,42],Qf=class extends _.Component{constructor(e){super(e),this.elementRef=_.createRef(),this.controlsRef=_.createRef(),this.popupRef=_.createRef(),this.hitChartBtnRef=_.createRef(),this.sectionGridRef=_.createRef(),this.staggerPrimaryRef=_.createRef(),this.state={openPanel:null,hoverPanel:null,showArt:!1},this.panelHoverTimer=null,this.onDocumentPointerDown=this.onDocumentPointerDown.bind(this),this.onDragStart=this.onDragStart.bind(this),this.onDragMove=this.onDragMove.bind(this),this.onDragEnd=this.onDragEnd.bind(this),this.onTouchDragStart=this.onTouchDragStart.bind(this),this.onTouchDragMove=this.onTouchDragMove.bind(this),this.onTouchDragEnd=this.onTouchDragEnd.bind(this),this.onScreenResize=this.onScreenResize.bind(this),this.onGripDoubleClick=this.onGripDoubleClick.bind(this),this.screenFit=1,this.screenFitHeight=null,this.autoFit=1}side(){return kp(this.props.ship)?`left`:`right`}isMirroredGrip(){return this.side()===`right`}applyScreenFit(){let e=this.elementRef.current;if(!e)return;let t=ap(),n=bp(this.side());if(!t&&n===1){(this.screenFit!==1||this.screenFitHeight)&&(e.style.transform=``,e.style.transformOrigin=``,e.style.maxHeight=``,this.screenFit=1,this.screenFitHeight=null),this.autoFit=1;return}let r=this.measureNatural();if(!r)return;let i=t?cp():null,a=window.innerHeight||document.documentElement.clientHeight,o=1;if(t){let e=(document.documentElement.clientWidth||window.innerWidth)*i.fillW,t=a*i.fillH;o=Math.min(e/r.width,t/r.height),o=Math.min(i.max,Math.max(i.min,o))}this.autoFit=o;let s=_p(o*n);s=Math.round(s*100)/100;let c=null;if(t){let e=Math.min(ip,i.fillH*Math.max(1,n));c=Math.round(a*e/s)}(s!==this.screenFit||c!==this.screenFitHeight)&&(this.screenFit=s,this.screenFitHeight=c,e.style.transformOrigin=this.transformOrigin(),e.style.transform=s===1?``:`scale(`+s+`)`,e.style.maxHeight=c==null?``:c+`px`,this.resizeStart||this.keepGripOnScreen())}measureNatural(){let e=this.elementRef.current;if(!e)return null;let t=e.style.maxHeight;e.style.maxHeight=`none`;let n=e.offsetWidth,r=e.offsetHeight;return e.style.maxHeight=t,n&&r?{width:n,height:r}:null}transformOrigin(){return this.resizeOrigin?this.resizeOrigin:kp(this.props.ship)?`top left`:`top right`}onScreenResize(){this.applyScreenFit()}isDragHandle(e){return!e||!e.closest||e.closest(`.shipwindow-nodrag`)?!1:!!e.closest(`.shipwindow-drag-handle`)}isResizeHandle(e){return!!(e&&e.closest&&e.closest(`.shipwindow-resize-grip`))}isDragSlop(e,t){if(!fp||!e||!e.closest||!e.closest(`.shipwindow-grab-slop`))return!1;let n=this.elementRef.current,r=n&&n.querySelector(`.shipwindow-drag-handle`);if(!r)return!1;let i=r.getBoundingClientRect();return t>=i.top&&t<=i.bottom+fp}gestureActive(){return!!(this.dragStart||this.resizeStart)}notePress(e,t,n){let r=Date.now(),i=!!this.lastPress&&this.lastPress.kind===e&&r-this.lastPress.time<pp;this.lastPress={kind:e,time:r},this.pressPoint={x:t,y:n},this.pendingReset=i}cancelDoublePress(){this.lastPress=null,this.pendingReset=!1}beginDrag(e,t){let n=this.elementRef.current;if(!n)return!1;let r=window.getComputedStyle(n),i=parseFloat(r.left),a=parseFloat(r.top);return isFinite(i)||(i=n.offsetLeft),isFinite(a)||(a=n.offsetTop),this.dragStart={x:e,y:t,left:i,top:a},this.positioned=!0,n.style.left=i+`px`,n.style.top=a+`px`,n.style.right=`auto`,!0}moveDrag(e,t){let n=this.elementRef.current;this.dragStart&&n&&(n.style.left=this.dragStart.left+(e-this.dragStart.x)+`px`,n.style.top=this.dragStart.top+(t-this.dragStart.y)+`px`,this.clampIntoView())}beginResize(e,t){let n=this.elementRef.current;if(!n)return!1;let r=this.measureNatural();if(!r)return!1;let i=window.getComputedStyle(n),a=parseFloat(i.left),o=parseFloat(i.top);isFinite(a)||(a=n.offsetLeft),isFinite(o)||(o=n.offsetTop);let s=this.screenFit||1,c=this.isMirroredGrip(),l=c?`top right`:`top left`;!c&&this.transformOrigin()===`top right`&&(a+=r.width*(1-s)),n.style.left=a+`px`,n.style.top=o+`px`,n.style.right=`auto`,n.style.transformOrigin=l,this.resizeOrigin=l,this.positioned=!0;let u=n.getBoundingClientRect(),d=c?u.right:u.left,f=c?-1:1;return this.resizeStart={originX:d,originY:u.top,flipX:f,width:r.width,height:r.height,scale:s,maxScale:c?d/r.width:1/0,base:vp(r.width,r.height,f*(e-d),t-u.top)},!0}moveResize(e,t){let n=this.resizeStart;if(!n)return;let r=vp(n.width,n.height,n.flipX*(e-n.originX),t-n.originY),i=_p(Math.min(n.maxScale,n.scale+(r-n.base))),a=this.side(),o=Math.round(i/(this.autoFit||1)*100)/100;o!==bp(a)&&(xp(a,o),this.applyScreenFit())}moveGesture(e,t){this.pressPoint&&(Math.abs(e-this.pressPoint.x)>mp||Math.abs(t-this.pressPoint.y)>mp)&&this.cancelDoublePress(),this.resizeStart?this.moveResize(e,t):this.moveDrag(e,t)}finishGesture(){let e=this.elementRef.current,t=!!this.resizeStart;if(this.dragStart=null,this.resizeStart=null,this.pendingReset&&(this.cancelDoublePress(),this.resetUserScale()),t){let e=this.side();Cp(e,bp(e)),this.keepGripOnScreen(),this.clampIntoView()}e&&(ep[this.side()]={top:parseFloat(e.style.top)||0,left:parseFloat(e.style.left)||0})}clampIntoView(e){let t=this.elementRef.current;if(!t||!this.positioned)return;let n=t.getBoundingClientRect(),r=document.documentElement.clientWidth||window.innerWidth,i=window.innerHeight||document.documentElement.clientHeight,a=0,o=0;n.top<0?o=-n.top:n.top>i-hp&&(o=i-hp-n.top),e&&n.width<=r&&n.left<0?a=-n.left:n.right<hp?a=hp-n.right:n.left>r-hp&&(a=r-hp-n.left),(a||o)&&(t.style.left=(parseFloat(t.style.left)||0)+a+`px`,t.style.top=(parseFloat(t.style.top)||0)+o+`px`)}keepGripOnScreen(){let e=this.elementRef.current;if(!e)return;let t=e.getBoundingClientRect(),n=document.documentElement.clientWidth||window.innerWidth,r;if(this.isMirroredGrip()){if(t.left>=0||(r=Math.min(-t.left,n-t.right),r<=0))return}else if(r=n-t.right,r>=0)return;let i=window.getComputedStyle(e),a=parseFloat(i.left);isFinite(a)||(a=e.offsetLeft),e.style.left=a+r+`px`,e.style.right=`auto`,this.positioned=!0}resetUserScale(){let e=this.side();bp(e)!==1&&(xp(e,1),Cp(e,1),this.applyScreenFit(),this.clampIntoView(!0))}onGripDoubleClick(e){e.stopPropagation(),this.resetUserScale()}onDragStart(e){if(window.FV_DRAG_DEBUG&&console.log(`[shipwindow] pointerdown`,e.pointerType,`handle:`,this.isDragHandle(e.target),`grip:`,this.isResizeHandle(e.target)),e.pointerType===`touch`||e.button!=null&&e.button>0)return;let t=this.isResizeHandle(e.target);if(!t&&!this.isDragHandle(e.target))return;if(this.notePress(t?`grip`:`header`,e.clientX,e.clientY),!(t?this.beginResize(e.clientX,e.clientY):this.beginDrag(e.clientX,e.clientY))){this.cancelDoublePress();return}let n=this.elementRef.current,r=!1;try{n.setPointerCapture(e.pointerId),r=!0}catch{}this.dragTarget=r?n:document,this.dragTarget.addEventListener(`pointermove`,this.onDragMove),this.dragTarget.addEventListener(`pointerup`,this.onDragEnd),this.dragTarget.addEventListener(`pointercancel`,this.onDragEnd),this.dragPointerId=e.pointerId,e.preventDefault()}onDragMove(e){this.gestureActive()&&e.pointerId===this.dragPointerId&&this.moveGesture(e.clientX,e.clientY)}onDragEnd(e){!this.gestureActive()||e&&e.pointerId!==this.dragPointerId||(this.stopDragListening(),this.finishGesture())}onTouchDragStart(e){if(window.FV_DRAG_DEBUG&&console.log(`[shipwindow] touchstart`,e.touches&&e.touches.length,`handle:`,this.isDragHandle(e.target),`grip:`,this.isResizeHandle(e.target)),this.gestureActive()||!e.touches||e.touches.length!==1)return;let t=e.touches[0],n=this.isResizeHandle(e.target);(n||this.isDragHandle(e.target)||this.isDragSlop(e.target,t.clientY))&&(this.notePress(n?`grip`:`header`,t.clientX,t.clientY),(n?this.beginResize(t.clientX,t.clientY):this.beginDrag(t.clientX,t.clientY))?(this.touchDragId=t.identifier,this.dragScroll={x:window.scrollX||window.pageXOffset||0,y:window.scrollY||window.pageYOffset||0},document.addEventListener(`touchmove`,this.onTouchDragMove,{passive:!1}),document.addEventListener(`touchend`,this.onTouchDragEnd),document.addEventListener(`touchcancel`,this.onTouchDragEnd),e.cancelable&&e.preventDefault()):this.cancelDoublePress())}onTouchDragMove(e){let t=wp(e.touches,this.touchDragId);if(!this.gestureActive()||!t)return;this.moveGesture(t.clientX,t.clientY);let n=this.dragScroll;n&&((window.scrollX||window.pageXOffset||0)!==n.x||(window.scrollY||window.pageYOffset||0)!==n.y)&&window.scrollTo(n.x,n.y),e.cancelable&&e.preventDefault()}onTouchDragEnd(e){e&&e.touches&&wp(e.touches,this.touchDragId)||(this.stopTouchDragListening(),this.gestureActive()&&this.finishGesture())}stopTouchDragListening(){document.removeEventListener(`touchmove`,this.onTouchDragMove,{passive:!1}),document.removeEventListener(`touchend`,this.onTouchDragEnd),document.removeEventListener(`touchcancel`,this.onTouchDragEnd),this.touchDragId=null,this.dragScroll=null}stopDragListening(){let e=this.dragTarget;e&&(e.removeEventListener(`pointermove`,this.onDragMove),e.removeEventListener(`pointerup`,this.onDragEnd),e.removeEventListener(`pointercancel`,this.onDragEnd),this.dragPointerId!=null&&e.hasPointerCapture&&e.hasPointerCapture(this.dragPointerId)&&e.releasePointerCapture(this.dragPointerId)),this.dragTarget=null,this.dragPointerId=null}onShipClick(e){e.stopPropagation();let{ship:t}=this.props;this.ignoreNextClick?this.ignoreNextClick=!1:Tp()?window.uiEvents.relay(`CloseSystemInfo`):window.uiEvents.relay(`SystemClicked`,{ship:t,system:t,element:e.target})}onShipTouchMove(e){if(!this.longPressTimer)return;let t=e.touches[0],n=t.clientX-this.touchStartX,r=t.clientY-this.touchStartY;(Math.abs(n)>10||Math.abs(r)>10)&&(clearTimeout(this.longPressTimer),this.longPressTimer=null)}onShipTouchCancel(e){this.longPressTimer&&=(clearTimeout(this.longPressTimer),null),this.touchActive=!1,window.uiEvents.relay(`SystemMouseOut`)}onShipTouchEnd(e){this.longPressTimer?(clearTimeout(this.longPressTimer),this.longPressTimer=null):window.uiEvents.relay(`SystemMouseOut`),setTimeout(()=>{this.touchActive=!1},300)}onUnknownMouseOver(e){if(this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3)return;let{ship:t}=this.props,n=shipManager.systems.getSystemByName(t,`mineStealth`);window.uiEvents.relay(`SystemMouseOver`,{ship:t,system:n||t.systems[0],element:e.currentTarget,showInfo:!0})}onUnknownMouseOut(){this.touchActive||window.lastTouchActiveTime&&Date.now()-window.lastTouchActiveTime<1e3||window.uiEvents.relay(`SystemMouseOut`)}onUnknownTouchStart(e){this.touchActive=!0,this.ignoreNextClick=!1,window.lastTouchActiveTime=Date.now(),this.longPressTimer&&clearTimeout(this.longPressTimer);let t=e.currentTarget,n=e.touches[0];this.touchStartX=n.clientX,this.touchStartY=n.clientY,this.longPressTimer=setTimeout(()=>{this.ignoreNextClick=!0;let{ship:e}=this.props,n=shipManager.systems.getSystemByName(e,`mineStealth`);window.uiEvents.relay(`SystemMouseOver`,{ship:e,system:n||e.systems[0],element:t,showInfo:!0}),this.longPressTimer=null},400)}componentDidMount(){let e=this.elementRef.current,t=kp(this.props.ship)?`left`:`right`;e.addEventListener(`pointerdown`,this.onDragStart),e.addEventListener(`touchstart`,this.onTouchDragStart,{passive:!1});let n=ep[t];if(n&&!ap()){let t=Math.max(0,Math.min(n.top,window.innerHeight-60)),r=Math.max(60-e.offsetWidth,Math.min(n.left,window.innerWidth-60));e.style.top=t+`px`,e.style.left=r+`px`,e.style.right=`auto`,this.positioned=!0}document.addEventListener(`pointerdown`,this.onDocumentPointerDown),this.alignStaggeredPrimary(),this.applyScreenFit(),window.addEventListener(`resize`,this.onScreenResize),window.addEventListener(`orientationchange`,this.onScreenResize)}componentDidUpdate(){this.alignStaggeredPrimary(),this.applyScreenFit()}alignStaggeredPrimary(){let e=this.sectionGridRef.current,t=this.staggerPrimaryRef.current;if(!e||!t)return;let n=t.offsetHeight/2;e.style.setProperty(`--fv-prim-half`,n+`px`),e.style.setProperty(`--fv-art-dy`,t.offsetTop+n-e.clientHeight/2+`px`)}componentWillUnmount(){document.removeEventListener(`pointerdown`,this.onDocumentPointerDown),window.removeEventListener(`resize`,this.onScreenResize),window.removeEventListener(`orientationchange`,this.onScreenResize);let e=this.elementRef.current;e&&(e.removeEventListener(`pointerdown`,this.onDragStart),e.removeEventListener(`touchstart`,this.onTouchDragStart,{passive:!1})),this.stopDragListening(),this.stopTouchDragListening(),this.dragStart=null,this.resizeStart=null,this.panelHoverTimer&&clearTimeout(this.panelHoverTimer)}onPanelHoverStart(e){this.panelHoverTimer&&=(clearTimeout(this.panelHoverTimer),null),this.state.hoverPanel!==e&&this.setState({hoverPanel:e})}onPanelHoverEnd(){this.panelHoverTimer&&clearTimeout(this.panelHoverTimer),this.panelHoverTimer=setTimeout(()=>{this.panelHoverTimer=null,this.setState({hoverPanel:null})},150)}onDocumentPointerDown(e){if(!this.state.openPanel)return;let t=this.controlsRef.current,n=this.popupRef.current;t&&t.contains(e.target)||n&&n.contains(e.target)||this.setState({openPanel:null})}close(){window.uiEvents.relay(`CloseShipWindow`,{ship:this.props.ship})}togglePanel(e,t){t.stopPropagation(),this.setState(t=>({openPanel:t.openPanel===e?null:e}))}toggleArt(e){e&&e.stopPropagation(),this.setState(e=>({showArt:!e.showArt,openPanel:null}))}renderResizeGrip(e){return(0,P.jsx)(Ef,{className:`shipwindow-resize-grip shipwindow-nodrag`,$pad:dp,$mirror:this.isMirroredGrip(),$overlap:!!e,onDoubleClick:this.onGripDoubleClick,title:`Drag to resize this window — double-click to reset its size`})}renderStatusStrip(e,t){let n=t?[{key:`rolled`,text:`⟲ Rolled — port / starboard reversed`}].concat(Fp(e)):Fp(e);return(0,P.jsxs)(_.Fragment,{children:[n.map((e,t)=>(0,P.jsx)(Bf,{$color:e.color,$bg:e.bg,$grip:t===n.length-1,children:e.text},e.key)),this.renderResizeGrip(n.length>0)]})}renderHeader(e,t,n){return(0,P.jsxs)(Sf,{className:`shipwindow-drag-handle`,title:`Drag to move — double-click to reset window size`,children:[(0,P.jsx)(Cf,{$tint:n,title:e,children:e}),(0,P.jsx)(wf,{title:t,children:t}),(0,P.jsx)(Tf,{className:`shipwindow-nodrag`,onClick:this.close.bind(this),children:`✕`})]})}renderHitChartButton(e){let{openPanel:t}=this.state;return(0,P.jsxs)(Of,{ref:this.hitChartBtnRef,$wide:e,$active:t===`hitchart`,onClick:this.togglePanel.bind(this,`hitchart`),children:[(0,P.jsx)(kf,{children:`⊕`}),`Hit Chart`]})}renderStatsButton(e){let{openPanel:t}=this.state;return(0,P.jsxs)(Of,{$wide:e,$active:t===`shipstats`,onClick:this.togglePanel.bind(this,`shipstats`),onMouseEnter:this.onPanelHoverStart.bind(this,`shipstats`),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[(0,P.jsxs)(af,{children:[(0,P.jsx)(`i`,{}),(0,P.jsx)(`i`,{}),(0,P.jsx)(`i`,{})]}),`Ship Stats`]})}renderArtButton(e){return(0,P.jsxs)(Of,{$wide:e,$active:this.state.showArt,onClick:this.toggleArt.bind(this),children:[(0,P.jsx)(Af,{}),`Ship Art`]})}renderNotesButton(e){let{openPanel:t}=this.state;return(0,P.jsxs)(Of,{$wide:e,$active:t===`notes`,onClick:this.togglePanel.bind(this,`notes`),onMouseEnter:this.onPanelHoverStart.bind(this,`notes`),onMouseLeave:this.onPanelHoverEnd.bind(this),children:[(0,P.jsx)(kf,{children:`✎`}),`Notes`]})}renderControls(e,t,n,r){let i=Tp(),a=jp(this.props.ship),o=Mp(this.props.ship);if(!e&&!t&&!r&&!a&&!o)return null;let s=i;return(0,P.jsxs)(Df,{ref:this.controlsRef,$compact:n,children:[e&&this.renderHitChartButton(s),a&&this.renderArtButton(s),o&&this.renderStatsButton(s),t&&this.renderNotesButton(s),r&&(0,P.jsx)(Z,{ship:this.props.ship})]})}getButtonLeft(){let e=this.controlsRef.current,t=this.elementRef.current;return!e||!t?null:Math.round((e.getBoundingClientRect().left-t.getBoundingClientRect().left)/(this.screenFit||1)-t.clientLeft)}getAnchorBelow(e,t){let n=e&&e.current,r=this.elementRef.current;if(!n||!r)return{top:t,left:this.getButtonLeft()};let i=n.getBoundingClientRect(),a=r.getBoundingClientRect(),o=this.screenFit||1;return{left:Math.round((i.left-a.left)/o-r.clientLeft),top:Math.round((i.bottom-a.top)/o-r.clientTop)+4}}renderPopup(e,t,n){let{ship:r}=this.props,{openPanel:i,hoverPanel:a}=this.state,o=i||a;if(!o)return null;let s=o===`hitchart`&&Tp()?this.hitChartBtnRef:this.controlsRef,{top:c,left:l}=this.getAnchorBelow(s,n);return o===`hitchart`&&e?(0,P.jsx)(jf,{ref:this.popupRef,$top:c,$left:l,$fit:!0,children:(0,P.jsx)(Gd,{ship:r})}):o===`shipstats`&&Mp(r)?(0,P.jsx)(jf,{ref:this.popupRef,$top:c,$left:l,$fit:!0,onMouseEnter:this.onPanelHoverStart.bind(this,`shipstats`),onMouseLeave:this.onPanelHoverEnd.bind(this),children:(0,P.jsx)(Z,{ship:r,live:!0,bare:!0})}):o===`notes`&&t?(0,P.jsx)(jf,{ref:this.popupRef,$top:c,$left:l,$fit:!0,$notes:!0,onMouseEnter:this.onPanelHoverStart.bind(this,`notes`),onMouseLeave:this.onPanelHoverEnd.bind(this),children:(0,P.jsx)(du,{ship:r,hideHitChart:!0,tightBottom:!0,compactText:!0})}):null}render(){let{ship:e}=this.props,t=Tp(),n=kp(e);var r=e.shipClass,i=e.name;let a=!1;if(e.mine){var o=shipManager.systems.getSystemByName(e,`mineStealth`);o&&!o.isMineRevealed(e)&&(r=`Mine`,i=`Mine`,a=!0)}i||(i=r,r=``);let s=!a&&Ap(e),c=!t&&!a&&Np(e);if(e.flight){if(t)return(0,P.jsxs)(xf,{ref:this.elementRef,onClick:$f,onContextMenu:e=>{e.preventDefault(),e.stopPropagation()},$isMyTeam:n,$variant:`flightLobby`,children:[this.renderHeader(i,r,Pp(e)),(0,P.jsxs)(If,{children:[(0,P.jsx)(Lf,{children:(0,P.jsx)(Nd,{ship:e})}),(0,P.jsx)(_f,{ship:e})]}),this.renderResizeGrip()]});let a=!!(window.ew&&ew.isFlightEwPool(e));return(0,P.jsxs)(xf,{ref:this.elementRef,onClick:$f,onContextMenu:e=>{e.preventDefault(),e.stopPropagation()},$isMyTeam:n,$variant:a?`flightEw`:`flight`,children:[this.renderHeader(i,r,Pp(e)),a?(0,P.jsxs)(yf,{children:[(0,P.jsx)(bf,{children:(0,P.jsx)(Nd,{ship:e})}),(0,P.jsx)(ld,{ship:e,flight:!0})]}):(0,P.jsx)(Nd,{ship:e}),this.renderStatusStrip(e)]})}if(a)return(0,P.jsxs)(xf,{ref:this.elementRef,onClick:$f,onContextMenu:e=>{e.preventDefault(),e.stopPropagation()},$isMyTeam:n,$variant:`terrain`,children:[this.renderHeader(i,r,null),(0,P.jsxs)(Vf,{children:[(0,P.jsx)(zf,{className:`shipwindow-grab-slop`,onClick:this.onShipClick.bind(this)}),(0,P.jsx)(Rf,{$img:window.AssetManager.getSmartImagePath(e.imagePath)}),(0,P.jsx)(Q,{onMouseOver:this.onUnknownMouseOver.bind(this),onMouseOut:this.onUnknownMouseOut.bind(this),onTouchStart:this.onUnknownTouchStart.bind(this),onTouchMove:this.onShipTouchMove.bind(this),onTouchEnd:this.onShipTouchEnd.bind(this),onTouchCancel:this.onShipTouchCancel.bind(this),children:`?`})]}),this.renderResizeGrip()]});let l=Bp(e),u=zp(l);if((window.gamedata.isTerrain(e.shipSizeClass,e.userid)||e.mine)&&!Dp(l)){let a=s||c||jp(e);return(0,P.jsxs)(xf,{ref:this.elementRef,onClick:$f,onContextMenu:e=>{e.preventDefault(),e.stopPropagation()},$isMyTeam:n,$variant:`terrain`,children:[this.renderHeader(i,r,null),(0,P.jsxs)(Vf,{children:[(0,P.jsx)(zf,{className:`shipwindow-grab-slop`,onClick:this.onShipClick.bind(this)}),(0,P.jsx)(Rf,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:e.mine&&a?Op:0}),this.renderControls(s,c,!0),qf.map(t=>l[t].length>0&&(0,P.jsx)(Ku,{location:t,nameOverride:u[t],ship:e,systems:l[t],isTerrain:!0,hidden:this.state.showArt},`section-${e.id}-${t}`))]}),t&&(0,P.jsx)(_f,{ship:e,full:!0}),this.renderStatusStrip(e),this.renderPopup(s,c,72)]})}let d=shipManager.movement.isRolled(e),f=!!e.enhancementTooltip||hf(e)!==null,p=e.base&&!e.smallBase,m=Wp(e,l),h=m?Yf:Vp(l,f),g=Kf.map(t=>{if(l[t].length===0)return null;let n=d&&Uf[t]!==void 0?Uf[t]:t,r=Hf[n],i=t===0||t===1||t===2||p&&Jf.includes(t);return{area:r,element:(0,P.jsx)(Ku,{location:t,displayLocation:n,area:r,valign:m?void 0:Wf[r],justify:m?void 0:Gf[r],wide:i,minHeight:void 0,nameOverride:u[t],hidden:this.state.showArt,ship:e,systems:l[t]},`section-${e.id}-${t}`)}}).filter(Boolean),_=this.renderControls(s,c,!1,t&&!e.mine),v=t?(0,P.jsx)(_f,{ship:e,grid:!0,hideEnhancements:f}):(0,P.jsx)(ld,{ship:e}),y=f&&(0,P.jsx)(mf,{ship:e}),b=(0,P.jsxs)(Mf,{ref:this.sectionGridRef,$areas:h,$staggered:m,children:[(0,P.jsx)(zf,{className:`shipwindow-grab-slop`,onClick:this.onShipClick.bind(this)}),(0,P.jsx)(Rf,{$img:window.AssetManager.getSmartImagePath(e.imagePath),$art:this.state.showArt,$offsetY:t&&Dp(l)?Ep:0,$offsetVar:m?`--fv-art-dy`:void 0}),m?Xf.map(e=>{let t=e.stack.map(e=>g.find(t=>t.area===e)).filter(Boolean).map(e=>e.area===`prim`?(0,P.jsx)(Pf,{ref:this.staggerPrimaryRef,children:e.element},`prim`):e.element),n=e.area===`lup`?_:e.area===`rup`?v:null,r=e.area===`rlo`?y:null;return(0,P.jsxs)(Nf,{$area:e.area,$side:e.side,$upper:e.upper,children:[n&&(0,P.jsx)(Ff,{$top:!0,children:n}),t,r&&(0,P.jsx)(Ff,{children:r})]},e.area)}):(0,P.jsxs)(P.Fragment,{children:[_,v,y,g.map(e=>e.element)]})]});return(0,P.jsxs)(xf,{ref:this.elementRef,onClick:$f,onContextMenu:e=>{e.preventDefault(),e.stopPropagation()},$isMyTeam:n,$variant:`ship`,children:[this.renderHeader(i,r,Pp(e)),b,this.renderStatusStrip(e,d),this.renderPopup(s,c)]})}},$f=()=>window.uiEvents.relay(`CloseSystemInfo`),ep={left:null,right:null},$=`(max-width: 1024px)`,tp=`(orientation: portrait)`,np={fillW:.6,fillH:.85,min:.4,max:1,portrait:1.4},rp={fillW:.96,fillH:.96,min:.5,max:1.75,portrait:1.2},ip=.98,ap=()=>!!window.matchMedia&&window.matchMedia($).matches,op=()=>window.matchMedia?window.matchMedia(tp).matches:window.innerHeight>=window.innerWidth,sp=(e,t)=>t===1?e:{fillW:Math.min(ip,e.fillW*t),fillH:Math.min(ip,e.fillH*t),min:Math.min(e.max,e.min*t),max:e.max},cp=()=>{let e=Tp()?rp:np;return op()?sp(e,e.portrait):e},lp=.35,up=3,dp=6,fp=8,pp=400,mp=6,hp=40,gp=`fv.shipwindow.userScale`,_p=e=>Math.min(up,Math.max(lp,e)),vp=(e,t,n,r)=>(n*e+r*t)/(e*e+t*t),yp={left:null,right:null},bp=e=>(yp[e]??(yp[e]=Sp(e)),yp[e]),xp=(e,t)=>{yp[e]=t},Sp=e=>{try{let t=parseFloat(window.localStorage.getItem(gp+`.`+e));if(isFinite(t)||(t=parseFloat(window.localStorage.getItem(gp))),isFinite(t)&&t>0)return _p(t)}catch{}return 1},Cp=(e,t)=>{try{window.localStorage.setItem(gp+`.`+e,String(t))}catch{}},wp=(e,t)=>{if(!e||t==null)return null;for(let n=0;n<e.length;n++)if(e[n].identifier===t)return e[n];return null},Tp=()=>!!window.gamedata&&window.gamedata.gamephase===-2,Ep=35,Dp=e=>Zf.some(t=>e[t].length>0),Op=20,kp=e=>window.ShipWindowManager&&typeof window.ShipWindowManager.isLeftSide==`function`?window.ShipWindowManager.isLeftSide(e):e.team===window.gamedata.getPlayerTeam(),Ap=e=>!!e.hitChart&&Object.keys(e.hitChart).length>0,jp=e=>!!(e&&e.imagePath)&&!e.flight,Mp=e=>!!e&&!Tp()&&!e.flight&&!e.mine&&!window.gamedata.isTerrain(e.shipSizeClass,e.userid),Np=e=>!!e.notes||!!e.enhancementTooltip||!!(e.hasAttached&&Object.keys(e.hasAttached).length>0),Pp=e=>null,Fp=e=>{if(Tp())return[];let t=[],n=shipManager.getTurnDeployed(e);n>window.gamedata.turn&&n<999&&t.push({key:`deploying`,color:N.colors.statusPending,bg:`rgba(0, 184, 230, 0.10)`,text:`Deploying on Turn `+n});let r=shipManager.getArrivalIniPenalty(e);r!==0&&t.push({key:`arrivalScatter`,color:N.colors.statusPending,bg:`rgba(0, 184, 230, 0.10)`,text:`Arrival Scatter `+r+` Ini`});let i=e.trueStealth?shipManager.getStealthToggleForecast(e):null;if(e.trueStealth){if(Rp(e,i))t.push({key:`undetected`,color:N.colors.statusOk,bg:`rgba(50, 205, 50, 0.08)`,text:`Undetected`});else if(e.mine){let n=shipManager.systems.getSystemByName(e,`mineStealth`);!n||n.isMineRevealedToOpponent(e)?t.push({key:`detected`,color:N.colors.statusBad,bg:`rgba(255, 80, 80, 0.10)`,text:`Detected - Revealed`}):t.push({key:`detected`,color:N.colors.statusAlert,bg:`rgba(255, 165, 0, 0.10)`,text:`Detected - Not Revealed`})}else i===!0?t.push({key:`detected`,color:N.colors.statusBad,bg:`rgba(255, 80, 80, 0.10)`,text:`Would be Detected`}):t.push({key:`detected`,color:N.colors.statusBad,bg:`rgba(255, 80, 80, 0.10)`,text:`Detected`})}shipManager.isJumpingToHyperspace(e)&&t.push({key:`jumping`,color:N.colors.warning,bg:`rgba(225, 176, 0, 0.10)`,text:`Jumping to Hyperspace`});let a=e.flight&&window.fighterMissions?window.fighterMissions.bannerText(e):null;a&&t.push(a.tone===`warning`?{key:`fighterMission`,color:N.colors.warning,bg:`rgba(225, 176, 0, 0.10)`,text:a.text}:{key:`fighterMission`,color:N.colors.statusPending,bg:`rgba(0, 184, 230, 0.10)`,text:a.text});let o=e.flight?null:shipManager.movement.getExtendedTurnStatus(e);o&&t.push(o.cancelled?{key:`extendedTurn`,color:N.colors.statusAlert,bg:`rgba(255, 165, 0, 0.10)`,text:o.text}:{key:`extendedTurn`,color:N.colors.statusOk,bg:`rgba(50, 205, 50, 0.08)`,text:o.text});let s=window.JumpEngine&&typeof window.JumpEngine.getAbductionChain==`function`?window.JumpEngine.getAbductionChain(e.id):null;s&&t.push({key:`abducted`,color:`#b36bff`,bg:`rgba(127, 0, 255, 0.10)`,text:`Being abducted: `+window.JumpEngine.formatAbductionHalves(s.total)+`/`+s.cost+` power-turns`});let c=shipManager.getHangarManoeuvre(e);if(c&&t.push({key:`hangarManoeuvre`,color:N.colors.statusPending,bg:`rgba(0, 184, 230, 0.10)`,text:c.text}),e.attached&&Object.keys(e.attached).length>0&&!e.detached&&!(c&&c.riding)){let n=window.gamedata.getShip(Object.keys(e.attached)[0]);if(n){let r=Object.values(e.attached)[0],i=``;r==1?i=`Front`:r==2?i=`Aft`:r==3||r==31||r==32?i=`Port`:(r==4||r==41||r==42)&&(i=`Starboard`),t.push({key:`attached`,color:N.colors.statusOk,bg:`rgba(50, 205, 50, 0.08)`,text:`Attached to `+n.name+(i?` [`+i+`]`:``)})}}return e.hasAttached&&Object.keys(e.hasAttached).length>0&&Object.keys(e.hasAttached).filter(e=>{let t=window.gamedata.getShip(e);return!(t&&shipManager.isDockingRider(t))}).length>0&&t.push({key:`boarded`,color:N.colors.statusAlert,bg:`rgba(255, 165, 0, 0.10)`,text:`Ship is being boarded!`}),e.flight&&Lp(e)&&t.push({key:`edfGrounded`,color:Ip,bg:`rgba(210, 80, 255, 0.12)`,text:`Energy Drained - cannot fire`}),t},Ip=`#d250ff`,Lp=e=>{let t=shipManager.systems.getSystem(e,1);return!t||!t.criticals?!1:t.criticals.some(e=>e.phpclass===`EdfFighterGrounded`&&e.turn+1===window.gamedata.turn)},Rp=(e,t)=>{if(gamedata.gamephase==-1&&(shipManager.getTurnPlaced(e)==gamedata.turn||shipManager.getTurnDeployed(e)==gamedata.turn))return!0;if(t!=null)return!t;let n=shipManager.isDetected(e);if(!n&&e.team==gamedata.getPlayerTeam()){let t=null;e.mine?t=shipManager.systems.getSystemByName(e,`mineStealth`):e.faction==`Torvalus Speculators`?t=shipManager.systems.getSystemByName(e,`ShadingField`):shipManager.getSpecialAbilityStealth(e,`Cloaking`)?t=shipManager.systems.getSystemByName(e,`CloakingDevice`):shipManager.getSpecialAbilityStealth(e,`Stealth`)&&(t=shipManager.systems.getSystemByName(e,`stealth`)),t&&(Array.isArray(t.detected)&&t.detected.length>0||t.detected===!0||Array.isArray(t.detectedNew)&&t.detectedNew.length>0||t.detectedNew===!0)&&(n=!0)}return!n},zp=e=>{let t={};return[{locations:[3,31,32],name:`Port`},{locations:[4,41,42],name:`Starboard`}].forEach(n=>{let r=n.locations.filter(t=>e[t].some(e=>e.name===`structure`));r.length===1&&r[0]!==n.locations[0]&&(t[r[0]]=n.name)}),t},Bp=e=>{let t={0:[],1:[],2:[],3:[],4:[],5:[],41:[],42:[],31:[],32:[]};return e.systems.forEach(e=>{e.hideInShipWindow||t[e.location].push(e)}),t},Vp=(e,t)=>{let n=[[`ctrl`,`fwd`,`ew`]],r=0;if((e[31].length||e[41].length)&&(n.push([`lfwd`,`prim`,`rfwd`]),r++),(e[3].length||e[4].length)&&(n.push([`left`,`prim`,`right`]),r++),(e[32].length||e[42].length)&&(n.push([`laft`,`prim`,`raft`]),r++),r===0&&e[0].length&&n.push([null,`prim`,null]),e[2].length&&n.push([null,`aft`,null]),t){let e=n[n.length-1];n.length>1&&e[2]===null?e[2]=`enh`:n.push([null,null,`enh`])}for(let e=1;e<n.length&&n[e][0]===null;e++)n[e][0]=`ctrl`;for(let e=1;e<n.length&&n[e][2]===null;e++)n[e][2]=`ew`;return n.map(e=>`"${e[0]||`.`}  ${e[1]||`.`}  ${e[2]||`.`}"`).join(` `)},Hp=4,Up=e=>Math.ceil(e.filter(e=>e.name!==`structure`).length/4),Wp=(e,t)=>!(!e.base||e.smallBase)&&Jf.some(e=>Up(t[e])>Hp),Gp=M.div`

`,Kp=M.div`
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
    border: 1px solid ${N.colors.warning};
    background-color: ${N.colors.windowBg};
    color: ${N.colors.text};
    font-family: ${N.fonts.body};
    font-size: 11px;
    box-shadow: 5px 5px 10px black;
`,qp=M.span`
    cursor: pointer;
    color: ${N.colors.line};
    font-size: 16px;

    &:hover {
        color: ${N.colors.text};
    }
`,Jp=class extends _.Component{constructor(e){super(e),this.state={error:null}}static getDerivedStateFromError(e){return{error:e}}componentDidCatch(e,t){let n=this.props.ship;console.error(`Ship window render failed for`,n&&(n.name||n.shipClass),e,t)}render(){if(this.state.error){let e=this.props.ship;return(0,P.jsxs)(Kp,{children:[(0,P.jsxs)(`span`,{children:[e&&(e.name||e.shipClass)||`Ship`,` — window failed to render (see console)`]}),(0,P.jsx)(qp,{onClick:()=>window.uiEvents.relay(`CloseShipWindow`,{ship:e}),children:`✕`})]})}return this.props.children}},Yp=class extends _.Component{render(){let{ships:e}=this.props;return(0,P.jsx)(Gp,{children:e.map(e=>(0,P.jsx)(Jp,{ship:e,children:(0,P.jsx)(Qf,{ship:e})},`shipwindow-${e.userid}-${e.id}`))})}},Xp=M.div`
    position: absolute;
    z-index: 20000;
    ${e=>Object.keys(e.$position).reduce((t,n)=>t+`
`+n+`:`+e.$position[n]+`px;`,``)}
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
    background: ${z.bg};
    border: 1px solid ${z.line};
    border-radius: ${z.radius};
    box-shadow: ${z.shadow};
`;M.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${z.dim};
    user-select: none;
`;var Zp=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${z.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,Qp=M.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,$p=M.span`
    flex: 0 0 auto;
    color: ${z.dim};
    font-size: 10px;
    user-select: none;
`,em=M.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${z.btnBg};
    border: 1px solid ${z.line};
    border-radius: 2px;
    color: ${z.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${z.line};
        color: #ffffff;
        opacity: 1;
    }

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${z.btnBg}; color: ${z.btnText}; }
    `}
`,tm=M.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${N.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${z.well};
    border: 1px solid ${z.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${z.focus}; }
`,nm=M.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${z.btnBg};
    border: 1px solid ${z.line};
    border-radius: 2px;
    color: ${z.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${N.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${z.line}; color: #ffffff; }
`,rm=e=>{let t={};return e.top>window.innerHeight/2?t.bottom=window.innerHeight-e.top:t.top=e.top+e.height,e.left>window.innerWidth/2?t.right=window.innerWidth-e.right:t.left=e.left,t},im=class extends _.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=ys(t=>this.step(e,t.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.fighterMaxHealth(this.props.ship)}setRemaining(e,t){let{ship:n}=this.props,r=this.maxHealth(),i=parseInt(t,10);isNaN(i)&&(i=r),i=Math.max(1,Math.min(r,i)),battleDamage.setFighter(n,e,{d:r-i}),this.refresh()}step(e,t){this.setRemaining(e,battleDamage.fighterHealth(this.props.ship,e)+t)}onInput(e,t){let n=String(t.target.value).replace(/[^0-9]/g,``);this.setRemaining(e,n===``?0:parseInt(n,10))}propagate(){let{ship:e}=this.props,t=parseInt(e.flightSize,10)||0,n=battleDamage.fighterMaxHealth(e)-battleDamage.fighterHealth(e,1);for(let r=2;r<=t;r++)battleDamage.setFighter(e,r,{d:n});this.refresh()}refresh(){let{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow==`function`&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){let{ship:e,boundingBox:t}=this.props,n=parseInt(e&&e.flightSize,10)||0,r=this.maxHealth();if(!n||!r)return null;battleDamage.flightSummary(e);let i=[];for(let t=1;t<=n;t++){let n=battleDamage.fighterHealth(e,t);i.push((0,P.jsxs)(Zp,{children:[(0,P.jsxs)(Qp,{children:[`Fighter `,t]}),(0,P.jsx)(em,{title:`More damage`,disabled:n<=1,onClick:()=>this.step(t,-1),children:`−`}),(0,P.jsx)(tm,{ref:this.wheelRef(t),type:`text`,value:n,onChange:e=>this.onInput(t,e)}),(0,P.jsx)(em,{title:`Repair`,disabled:n>=r,onClick:()=>this.step(t,1),children:`+`}),(0,P.jsxs)($p,{children:[`/ `,r]})]},`ftr-${t}`))}let a=battleDamage.flightCritEntry(e)||{},o=Js(a.c,e.preBattleCritDesc,e.preBattleCritTransient,a.p);return(0,P.jsxs)(Xp,{$position:rm(t),onClick:e=>e.stopPropagation(),children:[(0,P.jsx)(Ds,{$sticky:!0,children:`Fighter Damage`}),i,n>1&&(0,P.jsx)(nm,{title:`Copy Fighter 1's damage to every fighter in this flight`,onClick:()=>this.propagate(),children:`Apply Fighter 1's damage to all`}),(0,P.jsx)(Ys,{ship:e,kind:battleDamage.KIND_FIGHTER,reference:battleDamage.REF_FLIGHT,rows:o,editable:!0,onChange:()=>this.refresh()})]})}},am=M.div`
    position: absolute;
    z-index: 20000;
    ${e=>Object.keys(e.$position).reduce((t,n)=>t+`
`+n+`:`+e.$position[n]+`px;`,``)}
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
    background: ${z.bg};
    border: 1px solid ${z.line};
    border-radius: ${z.radius};
    box-shadow: ${z.shadow};
`,om=M.div`
    text-align: center;
    font-size: 10px;
    padding: 2px 4px;
    color: ${z.dim};
    user-select: none;
`,sm=M.div`
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px 3px 10px;
    font-size: 11px;
    color: ${z.text};

    /*the lobby panel-head blue, faint*/
    &:hover {
        background-color: rgba(73, 103, 145, 0.22);
    }
`,cm=M.div`
    flex: 1;
    min-width: 0;
    user-select: none;
`,lm=M.span`
    flex: 0 0 auto;
    color: ${z.dim};
    font-size: 10px;
    user-select: none;
`,um=M.div`
    width: 24px;
    height: 20px;
    flex: 0 0 24px;
    box-sizing: border-box;
    background: ${z.btnBg};
    border: 1px solid ${z.line};
    border-radius: 2px;
    color: ${z.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    line-height: 1;
    opacity: 0.9;
    user-select: none;

    &:hover {
        background: ${z.line};
        color: #ffffff;
        opacity: 1;
    }

    ${e=>e.disabled&&`
        opacity: 0.3;
        cursor: not-allowed;
        &:hover { background: ${z.btnBg}; color: ${z.btnText}; }
    `}
`,dm=M.input`
    flex: 0 0 40px;
    width: 40px;
    height: 20px;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    text-align: center;
    font-family: ${N.fonts.mono};
    font-size: 12px;
    color: #ffffff;
    background-color: ${z.well};
    border: 1px solid ${z.line};
    border-radius: 2px;
    outline: none;

    &:focus { border-color: ${z.focus}; }
`,fm=M.div`
    margin: 5px 8px 8px 10px;
    min-height: 24px;
    padding: 3px 8px;
    box-sizing: border-box;
    background: ${z.btnBg};
    border: 1px solid ${z.line};
    border-radius: 2px;
    color: ${z.btnText};
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-family: ${N.fonts.display};
    font-size: 8.5px;
    font-weight: 700;
    letter-spacing: 0.7px;
    text-transform: uppercase;
    user-select: none;

    &:hover { background: ${z.line}; color: #ffffff; }
`,pm=e=>{let t={};return e.top>window.innerHeight/2?t.bottom=window.innerHeight-e.top:t.top=e.top+e.height,e.left>window.innerWidth/2?t.right=window.innerWidth-e.right:t.left=e.left,t},mm=class extends _.Component{constructor(e){super(e),this.wheelRefs={}}wheelRef(e){return this.wheelRefs[e]||(this.wheelRefs[e]=ys(t=>this.step(e,t.deltaY<0?1:-1))),this.wheelRefs[e]}maxHealth(){return battleDamage.mineMaxHealth(this.props.ship)}setRemaining(e,t){let{ship:n}=this.props,r=this.maxHealth(),i=parseInt(t,10);isNaN(i)&&(i=r),i=Math.max(1,Math.min(r,i)),battleDamage.setMine(n,e,{d:r-i}),this.refresh()}step(e,t){this.setRemaining(e,battleDamage.mineHealth(this.props.ship,e)+t)}onInput(e,t){let n=String(t.target.value).replace(/[^0-9]/g,``);this.setRemaining(e,n===``?0:parseInt(n,10))}propagate(){let{ship:e}=this.props,t=battleDamage.mineCount(e),n=battleDamage.getEntry(e,battleDamage.KIND_MINE,1);for(let r=2;r<=t;r++)battleDamage.setWholeEntry(e,battleDamage.KIND_MINE,r,n);this.refresh()}refresh(){let{ship:e}=this.props;battleDamage.applyToShip(e),window.shipWindowManagerReact&&window.shipWindowManagerReact.update(),window.gamedata&&typeof gamedata.refreshFleetRow==`function`&&gamedata.refreshFleetRow(e),this.forceUpdate()}render(){let{ship:e,boundingBox:t}=this.props,n=battleDamage.mineCount(e),r=this.maxHealth();if(!n||r<2)return null;let i=battleDamage.mineSummary(e),a=[];for(let t=1;t<=n;t++){let n=battleDamage.mineHealth(e,t);a.push((0,P.jsxs)(sm,{children:[(0,P.jsxs)(cm,{children:[`Mine `,t]}),(0,P.jsx)(um,{title:`More damage`,disabled:n<=1,onClick:()=>this.step(t,-1),children:`−`}),(0,P.jsx)(dm,{ref:this.wheelRef(t),type:`text`,value:n,onChange:e=>this.onInput(t,e)}),(0,P.jsx)(um,{title:`Repair`,disabled:n>=r,onClick:()=>this.step(t,1),children:`+`}),(0,P.jsxs)(lm,{children:[`/ `,r]})]},`mne-${t}`))}return(0,P.jsxs)(am,{$position:pm(t),onClick:e=>e.stopPropagation(),children:[(0,P.jsx)(Ds,{$sticky:!0,children:`Mine Damage`}),(0,P.jsxs)(om,{children:[i.remaining,` / `,i.total,` structure`]}),a,n>1&&(0,P.jsx)(fm,{title:`Copy Mine 1's damage to every mine in this purchase`,onClick:()=>this.propagate(),children:`Apply Mine 1 to all`})]})}};window.UIManager=class{constructor(e){this.parentElement=e,this.roots=new Map}getRoot(e){let t=jQuery(e,this.parentElement)[0];return t?(this.roots.has(t)||this.roots.set(t,(0,v.createRoot)(t)),this.roots.get(t)):null}unmountRoot(e){let t=jQuery(e,this.parentElement)[0];t&&this.roots.has(t)&&(this.roots.get(t).unmount(),this.roots.delete(t))}EwButtons(e){let t=this.getRoot(`#showEwButtons`);t&&t.render((0,P.jsx)(mi,{...e}))}FullScreen(e){let t=this.getRoot(`#fullScreen`);t&&t.render((0,P.jsx)(ri,{...e}))}Surrender(e){let t=this.getRoot(`#surrender`);t&&t.render((0,P.jsx)(ai,{...e}))}BackToLobby(e){let t=this.getRoot(`#backToLobby`);t&&t.render((0,P.jsx)(fi,{...e}))}PlayerSettings(e){let t=this.getRoot(`#playerSettings`);t&&t.render((0,P.jsx)(lr,{...e}))}showShipThrustUI(e){let t=this.getRoot(`#shipThrust`);t&&t.render((0,P.jsx)(Qr,{...e}))}hideShipThrustUI(){this.unmountRoot(`#shipThrust`)}showWeaponList(e){let t=this.getRoot(`#weaponList`);t&&t.render((0,P.jsx)(cu,{...e}))}hideWeaponList(){this.unmountRoot(`#weaponList`)}showSystemInfo(e){let t=this.getRoot(`#systemInfoReact`);t&&t.render((0,P.jsx)(Cu,{...e}))}hideSystemInfo(){this.unmountRoot(`#systemInfoReact`)}showSystemInfoMenu(e){let t=this.getRoot(`#systemInfoReact`);t&&t.render((0,P.jsx)(Pu,{...e}))}hideSystemInfoMenu(){this.unmountRoot(`#systemInfoReact`)}canShowSystemInfoMenu(e,t){return Yc(e,t)}showFighterDamageMenu(e){let t=this.getRoot(`#systemInfoReact`);t&&t.render((0,P.jsx)(im,{...e}))}showMineDamageMenu(e){let t=this.getRoot(`#systemInfoReact`);t&&t.render((0,P.jsx)(mm,{...e}))}renderShipWindows(e){let t=this.getRoot(`#shipWindowsReact`);t&&t.render((0,P.jsx)(Yp,{...e}))}}});