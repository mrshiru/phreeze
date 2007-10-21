/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.form.Action=function(_1,_2){this.form=_1;this.options=_2||{};};Ext.form.Action.CLIENT_INVALID="client";Ext.form.Action.SERVER_INVALID="server";Ext.form.Action.CONNECT_FAILURE="connect";Ext.form.Action.LOAD_FAILURE="load";Ext.form.Action.prototype={type:"default",failureType:undefined,response:undefined,result:undefined,run:function(_3){},success:function(_4){},handleResponse:function(_5){},failure:function(_6){this.response=_6;this.failureType=Ext.form.Action.CONNECT_FAILURE;this.form.afterAction(this,false);},processResponse:function(_7){this.response=_7;if(!_7.responseText){return true;}this.result=this.handleResponse(_7);return this.result;},getUrl:function(_8){var _9=this.options.url||this.form.url||this.form.el.dom.action;if(_8){var p=this.getParams();if(p){_9+=(_9.indexOf("?")!=-1?"&":"?")+p;}}return _9;},getMethod:function(){return(this.options.method||this.form.method||this.form.el.dom.method||"POST").toUpperCase();},getParams:function(){var bp=this.form.baseParams;var p=this.options.params;if(p){if(typeof p=="object"){p=Ext.urlEncode(Ext.applyIf(p,bp));}else{if(typeof p=="string"&&bp){p+="&"+Ext.urlEncode(bp);}}}else{if(bp){p=Ext.urlEncode(bp);}}return p;},createCallback:function(){return{success:this.success,failure:this.failure,scope:this,timeout:(this.form.timeout*1000),upload:this.form.fileUpload?this.success:undefined};}};Ext.form.Action.Submit=function(_d,_e){Ext.form.Action.Submit.superclass.constructor.call(this,_d,_e);};Ext.extend(Ext.form.Action.Submit,Ext.form.Action,{type:"submit",run:function(){var o=this.options;var _10=this.getMethod()=="POST";if(o.clientValidation===false||this.form.isValid()){Ext.Ajax.request(Ext.apply(this.createCallback(),{form:this.form.el.dom,url:this.getUrl(!_10),params:_10?this.getParams():null,isUpload:this.form.fileUpload}));}else{if(o.clientValidation!==false){this.failureType=Ext.form.Action.CLIENT_INVALID;this.form.afterAction(this,false);}}},success:function(_11){var _12=this.processResponse(_11);if(_12===true||_12.success){this.form.afterAction(this,true);return;}if(_12.errors){this.form.markInvalid(_12.errors);this.failureType=Ext.form.Action.SERVER_INVALID;}this.form.afterAction(this,false);},handleResponse:function(_13){if(this.form.errorReader){var rs=this.form.errorReader.read(_13);var _15=[];if(rs.records){for(var i=0,len=rs.records.length;i<len;i++){var r=rs.records[i];_15[i]=r.data;}}if(_15.length<1){_15=null;}return{success:rs.success,errors:_15};}return Ext.decode(_13.responseText);}});Ext.form.Action.Load=function(_19,_1a){Ext.form.Action.Load.superclass.constructor.call(this,_19,_1a);this.reader=this.form.reader;};Ext.extend(Ext.form.Action.Load,Ext.form.Action,{type:"load",run:function(){Ext.Ajax.request(Ext.apply(this.createCallback(),{method:this.getMethod(),url:this.getUrl(false),params:this.getParams()}));},success:function(_1b){var _1c=this.processResponse(_1b);if(_1c===true||!_1c.success||!_1c.data){this.failureType=Ext.form.Action.LOAD_FAILURE;this.form.afterAction(this,false);return;}this.form.clearInvalid();this.form.setValues(_1c.data);this.form.afterAction(this,true);},handleResponse:function(_1d){if(this.form.reader){var rs=this.form.reader.read(_1d);var _1f=rs.records&&rs.records[0]?rs.records[0].data:null;return{success:rs.success,data:_1f};}return Ext.decode(_1d.responseText);}});Ext.form.Action.ACTION_TYPES={"load":Ext.form.Action.Load,"submit":Ext.form.Action.Submit};