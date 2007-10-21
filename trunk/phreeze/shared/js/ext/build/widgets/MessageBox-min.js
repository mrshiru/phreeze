/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.MessageBox=function(){var _1,_2,_3,_4;var _5,_6,_7,_8,_9,pp;var _b,_c,_d;var _e=function(_f){_1.hide();Ext.callback(_2.fn,_2.scope||window,[_f,_c.dom.value],1);};var _10=function(){if(_2&&_2.cls){_1.el.removeClass(_2.cls);}if(_4){Ext.TaskMgr.stop(_4);_4=null;}};var _11=function(b){var _13=0;if(!b){_b["ok"].hide();_b["cancel"].hide();_b["yes"].hide();_b["no"].hide();_1.footer.dom.style.display="none";return _13;}_1.footer.dom.style.display="";for(var k in _b){if(typeof _b[k]!="function"){if(b[k]){_b[k].show();_b[k].setText(typeof b[k]=="string"?b[k]:Ext.MessageBox.buttonText[k]);_13+=_b[k].el.getWidth()+15;}else{_b[k].hide();}}}return _13;};var _15=function(d,k,e){if(_2&&_2.closable!==false){_1.hide();}if(e){e.stopEvent();}};return{getDialog:function(){if(!_1){_1=new Ext.BasicDialog("x-msg-box",{autoCreate:true,shadow:true,draggable:true,resizable:false,constraintoviewport:false,fixedcenter:true,collapsible:false,shim:true,modal:true,width:400,height:100,buttonAlign:"center",closeClick:function(){if(_2&&_2.buttons&&_2.buttons.no&&!_2.buttons.cancel){_e("no");}else{_e("cancel");}}});_1.on("hide",_10);_3=_1.mask;_1.addKeyListener(27,_15);_b={};var bt=this.buttonText;_b["ok"]=_1.addButton(bt["ok"],_e.createCallback("ok"));_b["yes"]=_1.addButton(bt["yes"],_e.createCallback("yes"));_b["no"]=_1.addButton(bt["no"],_e.createCallback("no"));_b["cancel"]=_1.addButton(bt["cancel"],_e.createCallback("cancel"));_5=_1.body.createChild({html:"<span class=\"ext-mb-text\"></span><br /><input type=\"text\" class=\"ext-mb-input\" /><textarea class=\"ext-mb-textarea\"></textarea><div class=\"ext-mb-progress-wrap\"><div class=\"ext-mb-progress\"><div class=\"ext-mb-progress-bar\">&#160;</div></div></div>"});_6=_5.dom.firstChild;_7=Ext.get(_5.dom.childNodes[2]);_7.enableDisplayMode();_7.addKeyListener([10,13],function(){if(_1.isVisible()&&_2&&_2.buttons){if(_2.buttons.ok){_e("ok");}else{if(_2.buttons.yes){_e("yes");}}}});_8=Ext.get(_5.dom.childNodes[3]);_8.enableDisplayMode();_9=Ext.get(_5.dom.childNodes[4]);_9.enableDisplayMode();var pf=_9.dom.firstChild;pp=Ext.get(pf.firstChild);pp.setHeight(pf.offsetHeight);}return _1;},updateText:function(_1b){if(!_1.isVisible()&&!_2.width){_1.resizeTo(this.maxWidth,100);}_6.innerHTML=_1b||"&#160;";var w=Math.max(Math.min(_2.width||_6.offsetWidth,this.maxWidth),Math.max(_2.minWidth||this.minWidth,_d));if(_2.prompt){_c.setWidth(w);}if(_1.isVisible()){_1.fixedcenter=false;}_1.setContentSize(w,_5.getHeight());if(_1.isVisible()){_1.fixedcenter=true;}return this;},updateProgress:function(_1d,_1e){if(_1e){this.updateText(_1e);}pp.setWidth(Math.floor(_1d*_9.dom.firstChild.offsetWidth));return this;},isVisible:function(){return _1&&_1.isVisible();},hide:function(){if(this.isVisible()){_1.hide();}},show:function(_1f){if(this.isVisible()){this.hide();}var d=this.getDialog();_2=_1f;d.setTitle(_2.title||"&#160;");d.close.setDisplayed(_2.closable!==false);_c=_7;_2.prompt=_2.prompt||(_2.multiline?true:false);if(_2.prompt){if(_2.multiline){_7.hide();_8.show();_8.setHeight(typeof _2.multiline=="number"?_2.multiline:this.defaultTextHeight);_c=_8;}else{_7.show();_8.hide();}}else{_7.hide();_8.hide();}_9.setDisplayed(_2.progress===true);this.updateProgress(0);_c.dom.value=_2.value||"";if(_2.prompt){_1.setDefaultButton(_c);}else{var bs=_2.buttons;var db=null;if(bs&&bs.ok){db=_b["ok"];}else{if(bs&&bs.yes){db=_b["yes"];}}_1.setDefaultButton(db);}_d=_11(_2.buttons);this.updateText(_2.msg);if(_2.cls){d.el.addClass(_2.cls);}d.proxyDrag=_2.proxyDrag===true;d.modal=_2.modal!==false;d.mask=_2.modal!==false?_3:false;if(!d.isVisible()){document.body.appendChild(_1.el.dom);d.animateTarget=null;d.show(_1f.animEl);}return this;},progress:function(_23,msg){this.show({title:_23,msg:msg,buttons:false,progress:true,closable:false,minWidth:this.minProgressWidth});return this;},alert:function(_25,msg,fn,_28){this.show({title:_25,msg:msg,buttons:this.OK,fn:fn,scope:_28});return this;},wait:function(msg,_2a){this.show({title:_2a,msg:msg,buttons:false,closable:false,progress:true,modal:true,width:300,wait:true});_4=Ext.TaskMgr.start({run:function(i){Ext.MessageBox.updateProgress(((((i+20)%20)+1)*5)*0.01);},interval:1000});return this;},confirm:function(_2c,msg,fn,_2f){this.show({title:_2c,msg:msg,buttons:this.YESNO,fn:fn,scope:_2f});return this;},prompt:function(_30,msg,fn,_33,_34){this.show({title:_30,msg:msg,buttons:this.OKCANCEL,fn:fn,minWidth:250,scope:_33,prompt:true,multiline:_34});return this;},OK:{ok:true},YESNO:{yes:true,no:true},OKCANCEL:{ok:true,cancel:true},YESNOCANCEL:{yes:true,no:true,cancel:true},defaultTextHeight:75,maxWidth:600,minWidth:100,minProgressWidth:250,buttonText:{ok:"OK",cancel:"Cancel",yes:"Yes",no:"No"}};}();Ext.Msg=Ext.MessageBox;