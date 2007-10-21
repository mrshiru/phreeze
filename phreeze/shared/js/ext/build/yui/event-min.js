/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


if(!YAHOO.util.Event){YAHOO.util.Event=function(){var _1=false;var _2=[];var _3=[];var _4=[];var _5=[];var _6=0;var _7=[];var _8=[];var _9=0;var _a=null;return{POLL_RETRYS:200,POLL_INTERVAL:20,EL:0,TYPE:1,FN:2,WFN:3,OBJ:3,ADJ_SCOPE:4,isSafari:(/KHTML/gi).test(navigator.userAgent),webkit:function(){var v=navigator.userAgent.match(/AppleWebKit\/([^ ]*)/);if(v&&v[1]){return v[1];}return null;}(),isIE:(!this.webkit&&!navigator.userAgent.match(/opera/gi)&&navigator.userAgent.match(/msie/gi)),_interval:null,startInterval:function(){if(!this._interval){var _c=this;var _d=function(){_c._tryPreloadAttach();};this._interval=setInterval(_d,this.POLL_INTERVAL);}},onAvailable:function(_e,_f,_10,_11){_7.push({id:_e,fn:_f,obj:_10,override:_11,checkReady:false});_6=this.POLL_RETRYS;this.startInterval();},onContentReady:function(_12,_13,_14,_15){_7.push({id:_12,fn:_13,obj:_14,override:_15,checkReady:true});_6=this.POLL_RETRYS;this.startInterval();},addListener:function(el,_17,fn,obj,_1a){if(!fn||!fn.call){return false;}if(this._isValidCollection(el)){var ok=true;for(var i=0,len=el.length;i<len;++i){ok=this.on(el[i],_17,fn,obj,_1a)&&ok;}return ok;}else{if(typeof el=="string"){var oEl=this.getEl(el);if(oEl){el=oEl;}else{this.onAvailable(el,function(){YAHOO.util.Event.on(el,_17,fn,obj,_1a);});return true;}}}if(!el){return false;}if("unload"==_17&&obj!==this){_3[_3.length]=[el,_17,fn,obj,_1a];return true;}var _1f=el;if(_1a){if(_1a===true){_1f=obj;}else{_1f=_1a;}}var _20=function(e){return fn.call(_1f,YAHOO.util.Event.getEvent(e),obj);};var li=[el,_17,fn,_20,_1f];var _23=_2.length;_2[_23]=li;if(this.useLegacyEvent(el,_17)){var _24=this.getLegacyIndex(el,_17);if(_24==-1||el!=_4[_24][0]){_24=_4.length;_8[el.id+_17]=_24;_4[_24]=[el,_17,el["on"+_17]];_5[_24]=[];el["on"+_17]=function(e){YAHOO.util.Event.fireLegacyEvent(YAHOO.util.Event.getEvent(e),_24);};}_5[_24].push(li);}else{try{this._simpleAdd(el,_17,_20,false);}catch(ex){this.lastError=ex;this.removeListener(el,_17,fn);return false;}}return true;},fireLegacyEvent:function(e,_27){var ok=true,le,lh,li,_2c,ret;lh=_5[_27];for(var i=0,len=lh.length;i<len;++i){li=lh[i];if(li&&li[this.WFN]){_2c=li[this.ADJ_SCOPE];ret=li[this.WFN].call(_2c,e);ok=(ok&&ret);}}le=_4[_27];if(le&&le[2]){le[2](e);}return ok;},getLegacyIndex:function(el,_31){var key=this.generateId(el)+_31;if(typeof _8[key]=="undefined"){return-1;}else{return _8[key];}},useLegacyEvent:function(el,_34){if(this.webkit&&("click"==_34||"dblclick"==_34)){var v=parseInt(this.webkit,10);if(!isNaN(v)&&v<418){return true;}}return false;},removeListener:function(el,_37,fn){var i,len;if(typeof el=="string"){el=this.getEl(el);}else{if(this._isValidCollection(el)){var ok=true;for(i=0,len=el.length;i<len;++i){ok=(this.removeListener(el[i],_37,fn)&&ok);}return ok;}}if(!fn||!fn.call){return this.purgeElement(el,false,_37);}if("unload"==_37){for(i=0,len=_3.length;i<len;i++){var li=_3[i];if(li&&li[0]==el&&li[1]==_37&&li[2]==fn){_3.splice(i,1);return true;}}return false;}var _3d=null;var _3e=arguments[3];if("undefined"==typeof _3e){_3e=this._getCacheIndex(el,_37,fn);}if(_3e>=0){_3d=_2[_3e];}if(!el||!_3d){return false;}if(this.useLegacyEvent(el,_37)){var _3f=this.getLegacyIndex(el,_37);var _40=_5[_3f];if(_40){for(i=0,len=_40.length;i<len;++i){li=_40[i];if(li&&li[this.EL]==el&&li[this.TYPE]==_37&&li[this.FN]==fn){_40.splice(i,1);break;}}}}else{try{this._simpleRemove(el,_37,_3d[this.WFN],false);}catch(ex){this.lastError=ex;return false;}}delete _2[_3e][this.WFN];delete _2[_3e][this.FN];_2.splice(_3e,1);return true;},getTarget:function(ev,_42){var t=ev.target||ev.srcElement;return this.resolveTextNode(t);},resolveTextNode:function(_44){if(_44&&3==_44.nodeType){return _44.parentNode;}else{return _44;}},getPageX:function(ev){var x=ev.pageX;if(!x&&0!==x){x=ev.clientX||0;if(this.isIE){x+=this._getScrollLeft();}}return x;},getPageY:function(ev){var y=ev.pageY;if(!y&&0!==y){y=ev.clientY||0;if(this.isIE){y+=this._getScrollTop();}}return y;},getXY:function(ev){return[this.getPageX(ev),this.getPageY(ev)];},getRelatedTarget:function(ev){var t=ev.relatedTarget;if(!t){if(ev.type=="mouseout"){t=ev.toElement;}else{if(ev.type=="mouseover"){t=ev.fromElement;}}}return this.resolveTextNode(t);},getTime:function(ev){if(!ev.time){var t=new Date().getTime();try{ev.time=t;}catch(ex){this.lastError=ex;return t;}}return ev.time;},stopEvent:function(ev){this.stopPropagation(ev);this.preventDefault(ev);},stopPropagation:function(ev){if(ev.stopPropagation){ev.stopPropagation();}else{ev.cancelBubble=true;}},preventDefault:function(ev){if(ev.preventDefault){ev.preventDefault();}else{ev.returnValue=false;}},getEvent:function(e){var ev=e||window.event;if(!ev){var c=this.getEvent.caller;while(c){ev=c.arguments[0];if(ev&&Event==ev.constructor){break;}c=c.caller;}}return ev;},getCharCode:function(ev){return ev.charCode||ev.keyCode||0;},_getCacheIndex:function(el,_56,fn){for(var i=0,len=_2.length;i<len;++i){var li=_2[i];if(li&&li[this.FN]==fn&&li[this.EL]==el&&li[this.TYPE]==_56){return i;}}return-1;},generateId:function(el){var id=el.id;if(!id){id="yuievtautoid-"+_9;++_9;el.id=id;}return id;},_isValidCollection:function(o){return(o&&o.length&&typeof o!="string"&&!o.tagName&&!o.alert&&typeof o[0]!="undefined");},elCache:{},getEl:function(id){return document.getElementById(id);},clearCache:function(){},_load:function(e){_1=true;var EU=YAHOO.util.Event;if(this.isIE){EU._simpleRemove(window,"load",EU._load);}},_tryPreloadAttach:function(){if(this.locked){return false;}this.locked=true;var _61=!_1;if(!_61){_61=(_6>0);}var _62=[];for(var i=0,len=_7.length;i<len;++i){var _65=_7[i];if(_65){var el=this.getEl(_65.id);if(el){if(!_65.checkReady||_1||el.nextSibling||(document&&document.body)){var _67=el;if(_65.override){if(_65.override===true){_67=_65.obj;}else{_67=_65.override;}}_65.fn.call(_67,_65.obj);_7[i]=null;}}else{_62.push(_65);}}}_6=(_62.length===0)?0:_6-1;if(_61){this.startInterval();}else{clearInterval(this._interval);this._interval=null;}this.locked=false;return true;},purgeElement:function(el,_69,_6a){var _6b=this.getListeners(el,_6a);if(_6b){for(var i=0,len=_6b.length;i<len;++i){var l=_6b[i];this.removeListener(el,l.type,l.fn);}}if(_69&&el&&el.childNodes){for(i=0,len=el.childNodes.length;i<len;++i){this.purgeElement(el.childNodes[i],_69,_6a);}}},getListeners:function(el,_70){var _71=[],_72;if(!_70){_72=[_2,_3];}else{if(_70=="unload"){_72=[_3];}else{_72=[_2];}}for(var j=0;j<_72.length;++j){var _74=_72[j];if(_74&&_74.length>0){for(var i=0,len=_74.length;i<len;++i){var l=_74[i];if(l&&l[this.EL]===el&&(!_70||_70===l[this.TYPE])){_71.push({type:l[this.TYPE],fn:l[this.FN],obj:l[this.OBJ],adjust:l[this.ADJ_SCOPE],index:i});}}}}return(_71.length)?_71:null;},_unload:function(e){var EU=YAHOO.util.Event,i,j,l,len,_7e;for(i=0,len=_3.length;i<len;++i){l=_3[i];if(l){var _7f=window;if(l[EU.ADJ_SCOPE]){if(l[EU.ADJ_SCOPE]===true){_7f=l[EU.OBJ];}else{_7f=l[EU.ADJ_SCOPE];}}l[EU.FN].call(_7f,EU.getEvent(e),l[EU.OBJ]);_3[i]=null;l=null;_7f=null;}}_3=null;if(_2&&_2.length>0){j=_2.length;while(j){_7e=j-1;l=_2[_7e];if(l){EU.removeListener(l[EU.EL],l[EU.TYPE],l[EU.FN],_7e);}j=j-1;}l=null;EU.clearCache();}for(i=0,len=_4.length;i<len;++i){_4[i][0]=null;_4[i]=null;}_4=null;EU._simpleRemove(window,"unload",EU._unload);},_getScrollLeft:function(){return this._getScroll()[1];},_getScrollTop:function(){return this._getScroll()[0];},_getScroll:function(){var dd=document.documentElement,db=document.body;if(dd&&(dd.scrollTop||dd.scrollLeft)){return[dd.scrollTop,dd.scrollLeft];}else{if(db){return[db.scrollTop,db.scrollLeft];}else{return[0,0];}}},regCE:function(){},_simpleAdd:function(){if(window.addEventListener){return function(el,_83,fn,_85){el.addEventListener(_83,fn,(_85));};}else{if(window.attachEvent){return function(el,_87,fn,_89){el.attachEvent("on"+_87,fn);};}else{return function(){};}}}(),_simpleRemove:function(){if(window.removeEventListener){return function(el,_8b,fn,_8d){el.removeEventListener(_8b,fn,(_8d));};}else{if(window.detachEvent){return function(el,_8f,fn){el.detachEvent("on"+_8f,fn);};}else{return function(){};}}}()};}();(function(){var EU=YAHOO.util.Event;EU.on=EU.addListener;if(document&&document.body){EU._load();}else{EU._simpleAdd(window,"load",EU._load);}EU._simpleAdd(window,"unload",EU._unload);EU._tryPreloadAttach();})();}YAHOO.util.CustomEvent=function(_92,_93,_94,_95){this.type=_92;this.scope=_93||window;this.silent=_94;this.signature=_95||YAHOO.util.CustomEvent.LIST;this.subscribers=[];if(!this.silent){}var _96="_YUICEOnSubscribe";if(_92!==_96){this.subscribeEvent=new YAHOO.util.CustomEvent(_96,this,true);}};YAHOO.util.CustomEvent.LIST=0;YAHOO.util.CustomEvent.FLAT=1;YAHOO.util.CustomEvent.prototype={subscribe:function(fn,obj,_99){if(this.subscribeEvent){this.subscribeEvent.fire(fn,obj,_99);}this.subscribers.push(new YAHOO.util.Subscriber(fn,obj,_99));},unsubscribe:function(fn,obj){if(!fn){return this.unsubscribeAll();}var _9c=false;for(var i=0,len=this.subscribers.length;i<len;++i){var s=this.subscribers[i];if(s&&s.contains(fn,obj)){this._delete(i);_9c=true;}}return _9c;},fire:function(){var len=this.subscribers.length;if(!len&&this.silent){return true;}var _a1=[],ret=true,i;for(i=0;i<arguments.length;++i){_a1.push(arguments[i]);}var _a4=_a1.length;if(!this.silent){}for(i=0;i<len;++i){var s=this.subscribers[i];if(s){if(!this.silent){}var _a6=s.getScope(this.scope);if(this.signature==YAHOO.util.CustomEvent.FLAT){var _a7=null;if(_a1.length>0){_a7=_a1[0];}ret=s.fn.call(_a6,_a7,s.obj);}else{ret=s.fn.call(_a6,this.type,_a1,s.obj);}if(false===ret){if(!this.silent){}return false;}}}return true;},unsubscribeAll:function(){for(var i=0,len=this.subscribers.length;i<len;++i){this._delete(len-1-i);}return i;},_delete:function(_aa){var s=this.subscribers[_aa];if(s){delete s.fn;delete s.obj;}this.subscribers.splice(_aa,1);},toString:function(){return"CustomEvent: "+"'"+this.type+"', "+"scope: "+this.scope;}};YAHOO.util.Subscriber=function(fn,obj,_ae){this.fn=fn;this.obj=obj||null;this.override=_ae;};YAHOO.util.Subscriber.prototype.getScope=function(_af){if(this.override){if(this.override===true){return this.obj;}else{return this.override;}}return _af;};YAHOO.util.Subscriber.prototype.contains=function(fn,obj){if(obj){return(this.fn==fn&&this.obj==obj);}else{return(this.fn==fn);}};YAHOO.util.Subscriber.prototype.toString=function(){return"Subscriber { obj: "+(this.obj||"")+", override: "+(this.override||"no")+" }";};YAHOO.util.EventProvider=function(){};YAHOO.util.EventProvider.prototype={__yui_events:null,__yui_subscribers:null,subscribe:function(_b2,_b3,_b4,_b5){this.__yui_events=this.__yui_events||{};var ce=this.__yui_events[_b2];if(ce){ce.subscribe(_b3,_b4,_b5);}else{this.__yui_subscribers=this.__yui_subscribers||{};var _b7=this.__yui_subscribers;if(!_b7[_b2]){_b7[_b2]=[];}_b7[_b2].push({fn:_b3,obj:_b4,override:_b5});}},unsubscribe:function(_b8,_b9,_ba){this.__yui_events=this.__yui_events||{};var ce=this.__yui_events[_b8];if(ce){return ce.unsubscribe(_b9,_ba);}else{return false;}},unsubscribeAll:function(_bc){return this.unsubscribe(_bc);},createEvent:function(_bd,_be){this.__yui_events=this.__yui_events||{};var _bf=_be||{};var _c0=this.__yui_events;if(_c0[_bd]){}else{var _c1=_bf.scope||this;var _c2=_bf.silent||null;var ce=new YAHOO.util.CustomEvent(_bd,_c1,_c2,YAHOO.util.CustomEvent.FLAT);_c0[_bd]=ce;if(_bf.onSubscribeCallback){ce.subscribeEvent.subscribe(_bf.onSubscribeCallback);}this.__yui_subscribers=this.__yui_subscribers||{};var qs=this.__yui_subscribers[_bd];if(qs){for(var i=0;i<qs.length;++i){ce.subscribe(qs[i].fn,qs[i].obj,qs[i].override);}}}return _c0[_bd];},fireEvent:function(_c6,_c7,_c8,etc){this.__yui_events=this.__yui_events||{};var ce=this.__yui_events[_c6];if(ce){var _cb=[];for(var i=1;i<arguments.length;++i){_cb.push(arguments[i]);}return ce.fire.apply(ce,_cb);}else{return null;}},hasEvent:function(_cd){if(this.__yui_events){if(this.__yui_events[_cd]){return true;}}return false;}};YAHOO.util.KeyListener=function(_ce,_cf,_d0,_d1){if(!_ce){}else{if(!_cf){}else{if(!_d0){}}}if(!_d1){_d1=YAHOO.util.KeyListener.KEYDOWN;}var _d2=new YAHOO.util.CustomEvent("keyPressed");this.enabledEvent=new YAHOO.util.CustomEvent("enabled");this.disabledEvent=new YAHOO.util.CustomEvent("disabled");if(typeof _ce=="string"){_ce=document.getElementById(_ce);}if(typeof _d0=="function"){_d2.subscribe(_d0);}else{_d2.subscribe(_d0.fn,_d0.scope,_d0.correctScope);}function handleKeyPress(e,obj){if(!_cf.shift){_cf.shift=false;}if(!_cf.alt){_cf.alt=false;}if(!_cf.ctrl){_cf.ctrl=false;}if(e.shiftKey==_cf.shift&&e.altKey==_cf.alt&&e.ctrlKey==_cf.ctrl){var _d5;var _d6;if(_cf.keys instanceof Array){for(var i=0;i<_cf.keys.length;i++){_d5=_cf.keys[i];if(_d5==e.charCode){_d2.fire(e.charCode,e);break;}else{if(_d5==e.keyCode){_d2.fire(e.keyCode,e);break;}}}}else{_d5=_cf.keys;if(_d5==e.charCode){_d2.fire(e.charCode,e);}else{if(_d5==e.keyCode){_d2.fire(e.keyCode,e);}}}}}this.enable=function(){if(!this.enabled){YAHOO.util.Event.addListener(_ce,_d1,handleKeyPress);this.enabledEvent.fire(_cf);}this.enabled=true;};this.disable=function(){if(this.enabled){YAHOO.util.Event.removeListener(_ce,_d1,handleKeyPress);this.disabledEvent.fire(_cf);}this.enabled=false;};this.toString=function(){return"KeyListener ["+_cf.keys+"] "+_ce.tagName+(_ce.id?"["+_ce.id+"]":"");};};YAHOO.util.KeyListener.KEYDOWN="keydown";YAHOO.util.KeyListener.KEYUP="keyup";YAHOO.register("event",YAHOO.util.Event,{version:"2.2.0",build:"127"});