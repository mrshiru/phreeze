/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.PagingToolbar=function(el,ds,_3){Ext.PagingToolbar.superclass.constructor.call(this,el,null,_3);this.ds=ds;this.cursor=0;this.renderButtons(this.el);this.bind(ds);};Ext.extend(Ext.PagingToolbar,Ext.Toolbar,{pageSize:20,displayMsg:"Displaying {0} - {1} of {2}",emptyMsg:"No data to display",beforePageText:"Page",afterPageText:"of {0}",firstText:"First Page",prevText:"Previous Page",nextText:"Next Page",lastText:"Last Page",refreshText:"Refresh",renderButtons:function(el){Ext.PagingToolbar.superclass.render.call(this,el);this.first=this.addButton({tooltip:this.firstText,cls:"x-btn-icon x-grid-page-first",disabled:true,handler:this.onClick.createDelegate(this,["first"])});this.prev=this.addButton({tooltip:this.prevText,cls:"x-btn-icon x-grid-page-prev",disabled:true,handler:this.onClick.createDelegate(this,["prev"])});this.addSeparator();this.add(this.beforePageText);this.field=Ext.get(this.addDom({tag:"input",type:"text",size:"3",value:"1",cls:"x-grid-page-number"}).el);this.field.on("keydown",this.onPagingKeydown,this);this.field.on("focus",function(){this.dom.select();});this.afterTextEl=this.addText(String.format(this.afterPageText,1));this.field.setHeight(18);this.addSeparator();this.next=this.addButton({tooltip:this.nextText,cls:"x-btn-icon x-grid-page-next",disabled:true,handler:this.onClick.createDelegate(this,["next"])});this.last=this.addButton({tooltip:this.lastText,cls:"x-btn-icon x-grid-page-last",disabled:true,handler:this.onClick.createDelegate(this,["last"])});this.addSeparator();this.loading=this.addButton({tooltip:this.refreshText,cls:"x-btn-icon x-grid-loading",disabled:true,handler:this.onClick.createDelegate(this,["refresh"])});if(this.displayInfo){this.displayEl=Ext.fly(this.el.dom.firstChild).createChild({cls:"x-paging-info"});}},updateInfo:function(){if(this.displayEl){var _5=this.ds.getCount();var _6=_5==0?this.emptyMsg:String.format(this.displayMsg,this.cursor+1,this.cursor+_5,this.ds.getTotalCount());this.displayEl.update(_6);}},onLoad:function(ds,r,o){this.cursor=o.params?o.params.start:0;var d=this.getPageData(),ap=d.activePage,ps=d.pages;this.afterTextEl.el.innerHTML=String.format(this.afterPageText,d.pages);this.field.dom.value=ap;this.first.setDisabled(ap==1);this.prev.setDisabled(ap==1);this.next.setDisabled(ap==ps);this.last.setDisabled(ap==ps);this.loading.enable();this.updateInfo();},getPageData:function(){var _d=this.ds.getTotalCount();return{total:_d,activePage:Math.ceil((this.cursor+this.pageSize)/this.pageSize),pages:_d<this.pageSize?1:Math.ceil(_d/this.pageSize)};},onLoadError:function(){this.loading.enable();},onPagingKeydown:function(e){var k=e.getKey();var d=this.getPageData();if(k==e.RETURN){var v=this.field.dom.value,_12;if(!v||isNaN(_12=parseInt(v,10))){this.field.dom.value=d.activePage;return;}_12=Math.min(Math.max(1,_12),d.pages)-1;this.ds.load({params:{start:_12*this.pageSize,limit:this.pageSize}});e.stopEvent();}else{if(k==e.HOME||(k==e.UP&&e.ctrlKey)||(k==e.PAGEUP&&e.ctrlKey)||(k==e.RIGHT&&e.ctrlKey)||k==e.END||(k==e.DOWN&&e.ctrlKey)||(k==e.LEFT&&e.ctrlKey)||(k==e.PAGEDOWN&&e.ctrlKey)){var _12=(k==e.HOME||(k==e.DOWN&&e.ctrlKey)||(k==e.LEFT&&e.ctrlKey)||(k==e.PAGEDOWN&&e.ctrlKey))?1:d.pages;this.field.dom.value=_12;this.ds.load({params:{start:(_12-1)*this.pageSize,limit:this.pageSize}});e.stopEvent();}else{if(k==e.UP||k==e.RIGHT||k==e.PAGEUP||k==e.DOWN||k==e.LEFT||k==e.PAGEDOWN){var v=this.field.dom.value,_12;var _13=(e.shiftKey)?10:1;if(k==e.DOWN||k==e.LEFT||k==e.PAGEDOWN){_13*=-1;}if(!v||isNaN(_12=parseInt(v,10))){this.field.dom.value=d.activePage;return;}else{if(parseInt(v,10)+_13>=1&parseInt(v,10)+_13<=d.pages){this.field.dom.value=parseInt(v,10)+_13;_12=Math.min(Math.max(1,_12+_13),d.pages)-1;this.ds.load({params:{start:_12*this.pageSize,limit:this.pageSize}});}}e.stopEvent();}}}},beforeLoad:function(){if(this.loading){this.loading.disable();}},onClick:function(_14){var ds=this.ds;switch(_14){case"first":ds.load({params:{start:0,limit:this.pageSize}});break;case"prev":ds.load({params:{start:Math.max(0,this.cursor-this.pageSize),limit:this.pageSize}});break;case"next":ds.load({params:{start:this.cursor+this.pageSize,limit:this.pageSize}});break;case"last":var _16=ds.getTotalCount();var _17=_16%this.pageSize;var _18=_17?(_16-_17):_16-this.pageSize;ds.load({params:{start:_18,limit:this.pageSize}});break;case"refresh":ds.load({params:{start:this.cursor,limit:this.pageSize}});break;}},unbind:function(ds){ds.un("beforeload",this.beforeLoad,this);ds.un("load",this.onLoad,this);ds.un("loadexception",this.onLoadError,this);this.ds=undefined;},bind:function(ds){ds.on("beforeload",this.beforeLoad,this);ds.on("load",this.onLoad,this);ds.on("loadexception",this.onLoadError,this);this.ds=ds;}});