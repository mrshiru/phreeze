/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.tree.TreeLoader=function(_1){this.baseParams={};this.requestMethod="POST";Ext.apply(this,_1);this.addEvents({"beforeload":true,"load":true,"loadexception":true});Ext.tree.TreeLoader.superclass.constructor.call(this);};Ext.extend(Ext.tree.TreeLoader,Ext.util.Observable,{uiProviders:{},clearOnLoad:true,load:function(_2,_3){if(this.clearOnLoad){while(_2.firstChild){_2.removeChild(_2.firstChild);}}if(_2.attributes.children){var cs=_2.attributes.children;for(var i=0,_6=cs.length;i<_6;i++){_2.appendChild(this.createNode(cs[i]));}if(typeof _3=="function"){_3();}}else{if(this.dataUrl){this.requestData(_2,_3);}}},getParams:function(_7){var _8=[],bp=this.baseParams;for(var _a in bp){if(typeof bp[_a]!="function"){_8.push(encodeURIComponent(_a),"=",encodeURIComponent(bp[_a]),"&");}}_8.push("node=",encodeURIComponent(_7.id));return _8.join("");},requestData:function(_b,_c){if(this.fireEvent("beforeload",this,_b,_c)!==false){this.transId=Ext.Ajax.request({method:this.requestMethod,url:this.dataUrl||this.url,success:this.handleResponse,failure:this.handleFailure,scope:this,argument:{callback:_c,node:_b},params:this.getParams(_b)});}else{if(typeof _c=="function"){_c();}}},isLoading:function(){return this.transId?true:false;},abort:function(){if(this.isLoading()){Ext.Ajax.abort(this.transId);}},createNode:function(_d){if(this.baseAttrs){Ext.applyIf(_d,this.baseAttrs);}if(this.applyLoader!==false){_d.loader=this;}if(typeof _d.uiProvider=="string"){_d.uiProvider=this.uiProviders[_d.uiProvider]||eval(_d.uiProvider);}return(_d.leaf?new Ext.tree.TreeNode(_d):new Ext.tree.AsyncTreeNode(_d));},processResponse:function(_e,_f,_10){var _11=_e.responseText;try{var o=eval("("+_11+")");for(var i=0,len=o.length;i<len;i++){var n=this.createNode(o[i]);if(n){_f.appendChild(n);}}if(typeof _10=="function"){_10(this,_f);}}catch(e){this.handleFailure(_e);}},handleResponse:function(_16){this.transId=false;var a=_16.argument;this.processResponse(_16,a.node,a.callback);this.fireEvent("load",this,a.node,_16);},handleFailure:function(_18){this.transId=false;var a=_18.argument;this.fireEvent("loadexception",this,a.node,_18);if(typeof a.callback=="function"){a.callback(this,a.node);}}});