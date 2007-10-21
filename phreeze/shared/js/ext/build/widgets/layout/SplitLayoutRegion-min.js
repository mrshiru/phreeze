/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.SplitLayoutRegion=function(_1,_2,_3,_4){this.cursor=_4;Ext.SplitLayoutRegion.superclass.constructor.call(this,_1,_2,_3);};Ext.extend(Ext.SplitLayoutRegion,Ext.LayoutRegion,{splitTip:"Drag to resize.",collapsibleSplitTip:"Drag to resize. Double click to hide.",useSplitTips:false,applyConfig:function(_5){Ext.SplitLayoutRegion.superclass.applyConfig.call(this,_5);if(_5.split){if(!this.split){var _6=Ext.DomHelper.append(this.mgr.el.dom,{tag:"div",id:this.el.id+"-split",cls:"x-layout-split x-layout-split-"+this.position,html:"&#160;"});this.split=new Ext.SplitBar(_6,this.el,this.orientation);this.split.on("moved",this.onSplitMove,this);this.split.useShim=_5.useShim===true;this.split.getMaximumSize=this[this.position=="north"||this.position=="south"?"getVMaxSize":"getHMaxSize"].createDelegate(this);if(this.useSplitTips){this.split.el.dom.title=_5.collapsible?this.collapsibleSplitTip:this.splitTip;}if(_5.collapsible){this.split.el.on("dblclick",this.collapse,this);}}if(typeof _5.minSize!="undefined"){this.split.minSize=_5.minSize;}if(typeof _5.maxSize!="undefined"){this.split.maxSize=_5.maxSize;}if(_5.hideWhenEmpty||_5.hidden){this.hideSplitter();}}},getHMaxSize:function(){var _7=this.config.maxSize||10000;var _8=this.mgr.getRegion("center");return Math.min(_7,(this.el.getWidth()+_8.getEl().getWidth())-_8.getMinWidth());},getVMaxSize:function(){var _9=this.config.maxSize||10000;var _a=this.mgr.getRegion("center");return Math.min(_9,(this.el.getHeight()+_a.getEl().getHeight())-_a.getMinHeight());},onSplitMove:function(_b,_c){this.fireEvent("resized",this,_c);},getSplitBar:function(){return this.split;},hide:function(){this.hideSplitter();Ext.SplitLayoutRegion.superclass.hide.call(this);},hideSplitter:function(){if(this.split){this.split.el.setLocation(-2000,-2000);this.split.el.hide();}},show:function(){if(this.split){this.split.el.show();}Ext.SplitLayoutRegion.superclass.show.call(this);},beforeSlide:function(){if(Ext.isGecko){this.bodyEl.clip();if(this.tabs){this.tabs.bodyEl.clip();}if(this.activePanel){this.activePanel.getEl().clip();if(this.activePanel.beforeSlide){this.activePanel.beforeSlide();}}}},afterSlide:function(){if(Ext.isGecko){this.bodyEl.unclip();if(this.tabs){this.tabs.bodyEl.unclip();}if(this.activePanel){this.activePanel.getEl().unclip();if(this.activePanel.afterSlide){this.activePanel.afterSlide();}}}},initAutoHide:function(){if(this.autoHide!==false){if(!this.autoHideHd){var st=new Ext.util.DelayedTask(this.slideIn,this);this.autoHideHd={"mouseout":function(e){if(!e.within(this.el,true)){st.delay(500);}},"mouseover":function(e){st.cancel();},scope:this};}this.el.on(this.autoHideHd);}},clearAutoHide:function(){if(this.autoHide!==false){this.el.un("mouseout",this.autoHideHd.mouseout);this.el.un("mouseover",this.autoHideHd.mouseover);}},clearMonitor:function(){Ext.get(document).un("click",this.slideInIf,this);},slideOut:function(){if(this.isSlid||this.el.hasActiveFx()){return;}this.isSlid=true;if(this.collapseBtn){this.collapseBtn.hide();}this.closeBtnState=this.closeBtn.getStyle("display");this.closeBtn.hide();if(this.stickBtn){this.stickBtn.show();}this.el.show();this.el.alignTo(this.collapsedEl,this.getCollapseAnchor());this.beforeSlide();this.el.setStyle("z-index",10001);this.el.slideIn(this.getSlideAnchor(),{callback:function(){this.afterSlide();this.initAutoHide();Ext.get(document).on("click",this.slideInIf,this);this.fireEvent("slideshow",this);},scope:this,block:true});},afterSlideIn:function(){this.clearAutoHide();this.isSlid=false;this.clearMonitor();this.el.setStyle("z-index","");if(this.collapseBtn){this.collapseBtn.show();}this.closeBtn.setStyle("display",this.closeBtnState);if(this.stickBtn){this.stickBtn.hide();}this.fireEvent("slidehide",this);},slideIn:function(cb){if(!this.isSlid||this.el.hasActiveFx()){Ext.callback(cb);return;}this.isSlid=false;this.beforeSlide();this.el.slideOut(this.getSlideAnchor(),{callback:function(){this.el.setLeftTop(-10000,-10000);this.afterSlide();this.afterSlideIn();Ext.callback(cb);},scope:this,block:true});},slideInIf:function(e){if(!e.within(this.el)){this.slideIn();}},animateCollapse:function(){this.beforeSlide();this.el.setStyle("z-index",20000);var _12=this.getSlideAnchor();this.el.slideOut(_12,{callback:function(){this.el.setStyle("z-index","");this.collapsedEl.slideIn(_12,{duration:0.3});this.afterSlide();this.el.setLocation(-10000,-10000);this.el.hide();this.fireEvent("collapsed",this);},scope:this,block:true});},animateExpand:function(){this.beforeSlide();this.el.alignTo(this.collapsedEl,this.getCollapseAnchor(),this.getExpandAdj());this.el.setStyle("z-index",20000);this.collapsedEl.hide({duration:0.1});this.el.slideIn(this.getSlideAnchor(),{callback:function(){this.el.setStyle("z-index","");this.afterSlide();if(this.split){this.split.el.show();}this.fireEvent("invalidated",this);this.fireEvent("expanded",this);},scope:this,block:true});},anchors:{"west":"left","east":"right","north":"top","south":"bottom"},sanchors:{"west":"l","east":"r","north":"t","south":"b"},canchors:{"west":"tl-tr","east":"tr-tl","north":"tl-bl","south":"bl-tl"},getAnchor:function(){return this.anchors[this.position];},getCollapseAnchor:function(){return this.canchors[this.position];},getSlideAnchor:function(){return this.sanchors[this.position];},getAlignAdj:function(){var cm=this.cmargins;switch(this.position){case"west":return[0,0];break;case"east":return[0,0];break;case"north":return[0,0];break;case"south":return[0,0];break;}},getExpandAdj:function(){var c=this.collapsedEl,cm=this.cmargins;switch(this.position){case"west":return[-(cm.right+c.getWidth()+cm.left),0];break;case"east":return[cm.right+c.getWidth()+cm.left,0];break;case"north":return[0,-(cm.top+cm.bottom+c.getHeight())];break;case"south":return[0,cm.top+cm.bottom+c.getHeight()];break;}}});