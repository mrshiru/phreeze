
function standardForm(formId)
{ldelim}
	var frm = new Ext.form.BasicForm(formId);
	//frm.render();
	
	var fields = frm.getValues()
	
	for (var key in fields)
	{ldelim}
		var elem = Ext.get(key);
		if (elem == null)
		{ldelim}
			// this is probably a checkbox
		{rdelim}
		else if (elem.hasClass('combo-box'))
		{ldelim}
			var cb = new Ext.form.ComboBox({ldelim}
				transform:elem.dom.name,
				typeAhead: true,
				triggerAction: 'all',
				forceSelection:true
			{rdelim});
		{rdelim}
		else if (elem.hasClass('date-picker'))
		{ldelim}
			var df = new Ext.form.DateField({ldelim}format:'m/d/Y'{rdelim});
			df.applyToMarkup(elem.dom.name);
		{rdelim}
	{rdelim}
{rdelim}