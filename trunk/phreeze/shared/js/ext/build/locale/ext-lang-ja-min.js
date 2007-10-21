/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.UpdateManager.defaults.indicatorText="<div class=\"loading-indicator\">\xe8\xaa\xe3\ufffd\xbf\xe8\xbe\xbc\xe3\ufffd\xbf\xe4\xb8...</div>";if(Ext.View){Ext.View.prototype.emptyText="";}if(Ext.grid.Grid){Ext.grid.Grid.prototype.ddText="{0} \xe8\xa1\u0152\xe9\ufffd\xb8\xe6\u0160\u017e";}if(Ext.TabPanelItem){Ext.TabPanelItem.prototype.closeText="\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u201a\xbf\xe3\u0192\u2013\xe3\u201a\u2019\xe9\u2013\u2030\xe3\ufffd\u02dc\xe3\u201a\u2039";}if(Ext.form.Field){Ext.form.Field.prototype.invalidText="\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe5\u20ac\xa4\xe3\ufffd\u0152\xe4\xb8\ufffd\xe6\xa3\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a";}Date.monthNames=["1\xe6\u0153\u02c6","2\xe6\u0153\u02c6","3\xe6\u0153\u02c6","4\xe6\u0153\u02c6","5\xe6\u0153\u02c6","6\xe6\u0153\u02c6","7\xe6\u0153\u02c6","8\xe6\u0153\u02c6","9\xe6\u0153\u02c6","10\xe6\u0153\u02c6","11\xe6\u0153\u02c6","12\xe6\u0153\u02c6"];Date.dayNames=["\xe6\u2014\xa5","\xe6\u0153\u02c6","\xe7\ufffd\xab","\xe6\xb0\xb4","\xe6\u0153\xa8","\xe9\u2021\u2018","\xe5\u0153\u0178"];if(Ext.MessageBox){Ext.MessageBox.buttonText={ok:"OK",cancel:"\xe3\u201a\xe3\u0192\xa3\xe3\u0192\xb3\xe3\u201a\xbb\xe3\u0192\xab",yes:"\xe3\ufffd\xaf\xe3\ufffd\u201e",no:"\xe3\ufffd\u201e\xe3\ufffd\u201e\xe3\ufffd\u02c6"};}if(Ext.util.Format){Ext.util.Format.date=function(v,_2){if(!v){return"";}if(!(v instanceof Date)){v=new Date(Date.parse(v));}return v.dateFormat(_2||"Y/m/d");};}if(Ext.DatePicker){Ext.apply(Ext.DatePicker.prototype,{todayText:"\xe4\xbb\u0160\xe6\u2014\xa5",minText:"\xe9\ufffd\xb8\xe6\u0160\u017e\xe3\ufffd\u2014\xe3\ufffd\u0178\xe6\u2014\xa5\xe4\xbb\u02dc\xe3\ufffd\xaf\xe6\u0153\u20ac\xe5\xb0\ufffd\xe5\u20ac\xa4\xe4\xbb\xa5\xe4\xb8\u2039\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",maxText:"\xe9\ufffd\xb8\xe6\u0160\u017e\xe3\ufffd\u2014\xe3\ufffd\u0178\xe6\u2014\xa5\xe4\xbb\u02dc\xe3\ufffd\xaf\xe6\u0153\u20ac\xe5\xa4\xa7\xe5\u20ac\xa4\xe4\xbb\xa5\xe4\xb8\u0160\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",disabledDaysText:"",disabledDatesText:"",monthNames:Date.monthNames,dayNames:Date.dayNames,nextText:"\xe6\xac\xa1\xe6\u0153\u02c6\xe3\ufffd\xb8 (\xe3\u201a\xb3\xe3\u0192\xb3\xe3\u0192\u02c6\xe3\u0192\xe3\u0192\xbc\xe3\u0192\xab+\xe5\ufffd\xb3)",prevText:"\xe5\u2030\ufffd\xe6\u0153\u02c6\xe3\ufffd\xb8 (\xe3\u201a\xb3\xe3\u0192\xb3\xe3\u0192\u02c6\xe3\u0192\xe3\u0192\xbc\xe3\u0192\xab+\xe5\xb7\xa6)",monthYearText:"\xe6\u0153\u02c6\xe9\ufffd\xb8\xe6\u0160\u017e (\xe3\u201a\xb3\xe3\u0192\xb3\xe3\u0192\u02c6\xe3\u0192\xe3\u0192\xbc\xe3\u0192\xab+\xe4\xb8\u0160/\xe4\xb8\u2039\xe3\ufffd\xa7\xe5\xb9\xb4\xe7\xa7\xbb\xe5\u2039\u2022)",todayTip:"{0} (\xe3\u201a\xb9\xe3\u0192\u0161\xe3\u0192\xbc\xe3\u201a\xb9\xe3\u201a\xe3\u0192\xbc)",format:"Y/m/d"});}if(Ext.PagingToolbar){Ext.apply(Ext.PagingToolbar.prototype,{beforePageText:"\xe3\u0192\u0161\xe3\u0192\xbc\xe3\u201a\xb8",afterPageText:"/ {0}",firstText:"\xe6\u0153\u20ac\xe5\u02c6\ufffd\xe3\ufffd\xae\xe3\u0192\u0161\xe3\u0192\xbc\xe3\u201a\xb8",prevText:"\xe5\u2030\ufffd\xe3\ufffd\xae\xe3\u0192\u0161\xe3\u0192\xbc\xe3\u201a\xb8",nextText:"\xe6\xac\xa1\xe3\ufffd\xae\xe3\u0192\u0161\xe3\u0192\xbc\xe3\u201a\xb8",lastText:"\xe6\u0153\u20ac\xe5\xbe\u0152\xe3\ufffd\xae\xe3\u0192\u0161\xe3\u0192\xbc\xe3\u201a\xb8",refreshText:"\xe6\u203a\xb4\xe6\u2013\xb0",displayMsg:"{2} \xe4\xbb\xb6\xe4\xb8 {0} - {1} \xe3\u201a\u2019\xe8\xa1\xa8\xe7\xa4\xba",emptyMsg:"\xe8\xa1\xa8\xe7\xa4\xba\xe3\ufffd\u2122\xe3\u201a\u2039\xe3\u0192\u2021\xe3\u0192\xbc\xe3\u201a\xbf\xe3\ufffd\u0152\xe3\ufffd\u201a\xe3\u201a\u0160\xe3\ufffd\xbe\xe3\ufffd\u203a\xe3\u201a\u201c\xe3\u20ac\u201a"});}if(Ext.form.TextField){Ext.apply(Ext.form.TextField.prototype,{minLengthText:"\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe6\u0153\u20ac\xe5\xb0\ufffd\xe5\u20ac\xa4\xe3\ufffd\xaf {0} \xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",maxLengthText:"\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe6\u0153\u20ac\xe5\xa4\xa7\xe5\u20ac\xa4\xe3\ufffd\xaf {0} \xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",blankText:"\xe5\xbf\u2026\xe9\xa0\u02c6\xe9\xa0\u2026\xe7\u203a\xae\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",regexText:"",emptyText:null});}if(Ext.form.NumberField){Ext.apply(Ext.form.NumberField.prototype,{minText:"\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe6\u0153\u20ac\xe5\xb0\ufffd\xe5\u20ac\xa4\xe3\ufffd\xaf {0} \xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",maxText:"\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe6\u0153\u20ac\xe5\xa4\xa7\xe5\u20ac\xa4\xe3\ufffd\xaf {0} \xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",nanText:"{0} \xe3\ufffd\xaf\xe6\u2022\xb0\xe5\u20ac\xa4\xe3\ufffd\xa7\xe3\ufffd\xaf\xe3\ufffd\u201a\xe3\u201a\u0160\xe3\ufffd\xbe\xe3\ufffd\u203a\xe3\u201a\u201c\xe3\u20ac\u201a"});}if(Ext.form.DateField){Ext.apply(Ext.form.DateField.prototype,{disabledDaysText:"\xe7\u201e\xa1\xe5\u0160\xb9",disabledDatesText:"\xe7\u201e\xa1\xe5\u0160\xb9",minText:"\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe6\u2014\xa5\xe4\xbb\u02dc\xe3\ufffd\xaf\xe3\u20ac\ufffd {0} \xe4\xbb\xa5\xe9\u2122\ufffd\xe3\ufffd\xae\xe6\u2014\xa5\xe4\xbb\u02dc\xe3\ufffd\xab\xe8\xa8\xe5\xae\u0161\xe3\ufffd\u2014\xe3\ufffd\xa6\xe3\ufffd\ufffd\xe3\ufffd\xa0\xe3\ufffd\u2022\xe3\ufffd\u201e\xe3\u20ac\u201a",maxText:"\xe3\ufffd\u201c\xe3\ufffd\xae\xe3\u0192\u2022\xe3\u201a\xa3\xe3\u0192\xbc\xe3\u0192\xab\xe3\u0192\u2030\xe3\ufffd\xae\xe6\u2014\xa5\xe4\xbb\u02dc\xe3\ufffd\xaf\xe3\u20ac\ufffd {0} \xe4\xbb\xa5\xe5\u2030\ufffd\xe3\ufffd\xae\xe6\u2014\xa5\xe4\xbb\u02dc\xe3\ufffd\xab\xe8\xa8\xe5\xae\u0161\xe3\ufffd\u2014\xe3\ufffd\xa6\xe3\ufffd\ufffd\xe3\ufffd\xa0\xe3\ufffd\u2022\xe3\ufffd\u201e\xe3\u20ac\u201a",invalidText:"{0} \xe3\ufffd\xaf\xe9\u2013\u201c\xe9\ufffd\u2022\xe3\ufffd\xa3\xe3\ufffd\u0178\xe6\u2014\xa5\xe4\xbb\u02dc\xe5\u2026\xa5\xe5\u0160\u203a\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a - \xe5\u2026\xa5\xe5\u0160\u203a\xe5\xbd\xa2\xe5\xbc\ufffd\xe3\ufffd\xaf\xe3\u20ac\u0152{1}\xe3\u20ac\ufffd\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",format:"Y/m/d"});}if(Ext.form.ComboBox){Ext.apply(Ext.form.ComboBox.prototype,{loadingText:"\xe8\xaa\xe3\ufffd\xbf\xe8\xbe\xbc\xe3\ufffd\xbf\xe4\xb8...",valueNotFoundText:undefined});}if(Ext.form.VTypes){Ext.apply(Ext.form.VTypes,{emailText:"\xe3\u0192\xa1\xe3\u0192\xbc\xe3\u0192\xab\xe3\u201a\xa2\xe3\u0192\u2030\xe3\u0192\xac\xe3\u201a\xb9\xe3\u201a\u2019\"user@domain.com\"\xe3\ufffd\xae\xe5\xbd\xa2\xe5\xbc\ufffd\xe3\ufffd\xa7\xe5\u2026\xa5\xe5\u0160\u203a\xe3\ufffd\u2014\xe3\ufffd\xa6\xe3\ufffd\ufffd\xe3\ufffd\xa0\xe3\ufffd\u2022\xe3\ufffd\u201e\xe3\u20ac\u201a",urlText:"URL\xe3\u201a\u2019\"http:/"+"/www.domain.com\"\xe3\ufffd\xae\xe5\xbd\xa2\xe5\xbc\ufffd\xe3\ufffd\xa7\xe5\u2026\xa5\xe5\u0160\u203a\xe3\ufffd\u2014\xe3\ufffd\xa6\xe3\ufffd\ufffd\xe3\ufffd\xa0\xe3\ufffd\u2022\xe3\ufffd\u201e\xe3\u20ac\u201a",alphaText:"\xe5\ufffd\u0160\xe8\xa7\u2019\xe8\u2039\xb1\xe5\u2014\xe3\ufffd\xa8\"_\"\xe3\ufffd\xae\xe3\ufffd\xbf\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a",alphanumText:"\xe5\ufffd\u0160\xe8\xa7\u2019\xe8\u2039\xb1\xe6\u2022\xb0\xe3\ufffd\xa8\"_\"\xe3\ufffd\xae\xe3\ufffd\xbf\xe3\ufffd\xa7\xe3\ufffd\u2122\xe3\u20ac\u201a"});}if(Ext.grid.GridView){Ext.apply(Ext.grid.GridView.prototype,{sortAscText:"\xe6\u02dc\u2021\xe9\xa0\u2020",sortDescText:"\xe9\u2122\ufffd\xe9\xa0\u2020",lockText:"\xe3\u201a\xab\xe3\u0192\xa9\xe3\u0192\xa0\xe3\u0192\xe3\u0192\u0192\xe3\u201a\xaf",unlockText:"\xe3\u201a\xab\xe3\u0192\xa9\xe3\u0192\xa0\xe3\u0192\xe3\u0192\u0192\xe3\u201a\xaf\xe8\xa7\xa3\xe9\u2122\xa4",columnsText:"Columns"});}if(Ext.grid.PropertyColumnModel){Ext.apply(Ext.grid.PropertyColumnModel.prototype,{nameText:"\xe5\ufffd\ufffd\xe7\xa7\xb0",valueText:"\xe5\u20ac\xa4",dateFormat:"Y/m/d"});}if(Ext.SplitLayoutRegion){Ext.apply(Ext.SplitLayoutRegion.prototype,{splitTip:"\xe3\u0192\u2030\xe3\u0192\xa9\xe3\u0192\u0192\xe3\u201a\xb0\xe3\ufffd\u2122\xe3\u201a\u2039\xe3\ufffd\xa8\xe3\u0192\xaa\xe3\u201a\xb5\xe3\u201a\xa4\xe3\u201a\xba\xe3\ufffd\xa7\xe3\ufffd\ufffd\xe3\ufffd\xbe\xe3\ufffd\u2122\xe3\u20ac\u201a",collapsibleSplitTip:"\xe3\u0192\u2030\xe3\u0192\xa9\xe3\u0192\u0192\xe3\u201a\xb0\xe3\ufffd\xa7\xe3\u0192\xaa\xe3\u201a\xb5\xe3\u201a\xa4\xe3\u201a\xba\xe3\u20ac\u201a \xe3\u0192\u20ac\xe3\u0192\u2013\xe3\u0192\xab\xe3\u201a\xaf\xe3\u0192\xaa\xe3\u0192\u0192\xe3\u201a\xaf\xe3\ufffd\xa7\xe9\u0161\xa0\xe3\ufffd\u2122\xe3\u20ac\u201a"});}