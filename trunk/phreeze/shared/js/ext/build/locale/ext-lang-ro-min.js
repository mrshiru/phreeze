/*
 * Ext JS Library 1.1 RC 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.UpdateManager.defaults.indicatorText="<div class=\"loading-indicator\">\xc3\u017dnc\xc4\u0192rcare...</div>";if(Ext.View){Ext.View.prototype.emptyText="";}if(Ext.grid.Grid){Ext.grid.Grid.prototype.ddText="{0} r\xc3\xa2nd(uri) selectate";}if(Ext.TabPanelItem){Ext.TabPanelItem.prototype.closeText="\xc3\u017dnchide acest tab";}if(Ext.form.Field){Ext.form.Field.prototype.invalidText="Valoarea acestui c\xc3\xa2mp este invalid\xc4\u0192";}if(Ext.LoadMask){Ext.LoadMask.prototype.msg="\xc3\u017dnc\xc4\u0192rcare...";}Date.monthNames=["Ianuarie","Februarie","Martie","Aprilie","Mai","Iunie","Iulie","August","Septembrie","Octombrie","Noiembrie","Decembrie"];Date.dayNames=["Duminic\xc4\u0192","Luni","Mar\xc5\xa3i","Miercuri","Joi","Vineri","S\xc3\xa2mb\xc4\u0192t\xc4\u0192"];if(Ext.MessageBox){Ext.MessageBox.buttonText={ok:"OK",cancel:"Renun\xc5\xa3\xc4\u0192",yes:"Da",no:"Nu"};}if(Ext.util.Format){Ext.util.Format.date=function(v,_2){if(!v){return"";}if(!(v instanceof Date)){v=new Date(Date.parse(v));}return v.dateFormat(_2||"d-m-Y");};}if(Ext.DatePicker){Ext.apply(Ext.DatePicker.prototype,{todayText:"Ast\xc4\u0192zi",minText:"Aceast\xc4\u0192 zi este \xc3\xaenaintea datei de \xc3\xaenceput",maxText:"Aceast\xc4\u0192 zi este dup\xc4\u0192 ultimul termen",disabledDaysText:"",disabledDatesText:"",monthNames:Date.monthNames,dayNames:Date.dayNames,nextText:"Urm\xc4\u0192toarea lun\xc4\u0192 (Control+Right)",prevText:"Luna anterioar\xc4\u0192 (Control+Left)",monthYearText:"Alege o lun\xc4\u0192 (Control+Up/Down pentru a parcurge anii)",todayTip:"{0} (Spacebar)",format:"d-m-y"});}if(Ext.PagingToolbar){Ext.apply(Ext.PagingToolbar.prototype,{beforePageText:"Pagina",afterPageText:"din {0}",firstText:"Prima pagin\xc4\u0192",prevText:"Pagina precedent\xc4\u0192",nextText:"Urm\xc4\u0192toarea pagin\xc4\u0192",lastText:"Ultima pagin\xc4\u0192",refreshText:"Re\xc3\xaemprosp\xc4\u0192tare",displayMsg:"Afi\xc5\u0178eaz\xc4\u0192 {0} - {1} din {2}",emptyMsg:"Nu sunt date de afi\xc5\u0178at"});}if(Ext.form.TextField){Ext.apply(Ext.form.TextField.prototype,{minLengthText:"Lungimea minim\xc4\u0192 pentru acest c\xc3\xa2mp este de {0}",maxLengthText:"Lungimea maxim\xc4\u0192 pentru acest c\xc3\xa2mp este {0}",blankText:"Acest c\xc3\xa2mp este obligatoriu",regexText:"",emptyText:null});}if(Ext.form.NumberField){Ext.apply(Ext.form.NumberField.prototype,{minText:"Valoarea minim\xc4\u0192 permis\xc4\u0192 a acestui c\xc3\xa2mp este {0}",maxText:"Valaorea maxim\xc4\u0192 permis\xc4\u0192 a acestui c\xc3\xa2mp este {0}",nanText:"{0} nu este un num\xc4\u0192r valid"});}if(Ext.form.DateField){Ext.apply(Ext.form.DateField.prototype,{disabledDaysText:"Inactiv",disabledDatesText:"Inactiv",minText:"Data acestui c\xc3\xa2mp trebuie s\xc4\u0192 fie dup\xc4\u0192 {0}",maxText:"Data acestui c\xc3\xa2mp trebuie sa fie \xc3\xaenainte de {0}",invalidText:"{0} nu este o dat\xc4\u0192 valid\xc4\u0192 - trebuie s\xc4\u0192 fie \xc3\xaen formatul {1}",format:"d-m-y"});}if(Ext.form.ComboBox){Ext.apply(Ext.form.ComboBox.prototype,{loadingText:"\xc3\u017dnc\xc4\u0192rcare...",valueNotFoundText:undefined});}if(Ext.form.VTypes){Ext.apply(Ext.form.VTypes,{emailText:"Acest c\xc3\xa2mp trebuie s\xc4\u0192 con\xc5\xa3in\xc4\u0192 o adres\xc4\u0192 de e-mail \xc3\xaen formatul \"user@domain.com\"",urlText:"Acest c\xc3\xa2mp trebuie s\xc4\u0192 con\xc5\xa3in\xc4\u0192 o adres\xc4\u0192 URL \xc3\xaen formatul \"http:/"+"/www.domain.com\"",alphaText:"Acest c\xc3\xa2mp trebuie s\xc4\u0192 con\xc5\xa3in\xc4\u0192 doar litere \xc5\u0178i _",alphanumText:"Acest c\xc3\xa2mp trebuie s\xc4\u0192 con\xc5\xa3in\xc4\u0192 doar litere, cifre \xc5\u0178i _"});}if(Ext.grid.GridView){Ext.apply(Ext.grid.GridView.prototype,{sortAscText:"Sortare ascendent\xc4\u0192",sortDescText:"Sortare descendent\xc4\u0192",lockText:"Blocheaz\xc4\u0192 coloana",unlockText:"Deblocheaz\xc4\u0192 coloana",columnsText:"Coloane"});}if(Ext.grid.PropertyColumnModel){Ext.apply(Ext.grid.PropertyColumnModel.prototype,{nameText:"Nume",valueText:"Valoare",dateFormat:"m/j/Y"});}if(Ext.SplitLayoutRegion){Ext.apply(Ext.SplitLayoutRegion.prototype,{splitTip:"Trage pentru redimensionare.",collapsibleSplitTip:"Trage pentru redimensionare. Dublu-click pentru ascundere."});}