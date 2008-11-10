/**
 * generic form validation functions 
 * 
 * @author: Jason M Hinkle http://www.verysimple.com/
 * @version 1.0
 */
 
 
/**
 * Returns the value of a field, regardless of the type.  In the case of multi-select
 * controls, a comma-separated list of values is returned
 * 
 * @param Element fld form element
 * @return string
 */
function getFieldValue(fld) 
{

	if ((fld.type == "checkbox") || (fld.type == "radio")) 
	{
		return getCheckRadioValue(fld);
	} 
	
	if (fld.type == "select-multiple") 
	{
		return getMultiSelectValue(fld);
	} 
	
	if (fld.type == "select") 
	{
		return fld.options[fld.selectedIndex].value;
	}
	
	return fld.value;

}

/**
 * Returns the select value of a radio or checkbox field
 * 
 * @param Element fld multi-select form element
 * @return string
 */
function getCheckRadioValue(fld)
{
	var elems = fld.form.elements;
	var vals = "";
	var use_delim = ",";
	var delim = "";

	for (var i = 0, j = 0; i < elems.length; i++) 
	{
		if (fld.type == elems[i].type && fld.name == elems[i].name) 
		{
			if (elems[i].checked) 
			{
				vals += delim + elems[i].value;
				delim = use_delim;
			}
		}
	}
	return vals;
}

/**
 * Returns the selected value(s) of a multi-select field as a comma-separated list
 * 
 * @param Element fld multi-select form element
 * @return string
 */
function getMultiSelectValue(fld)
{
	var vals = "";
	var use_delim = ",";
	var delim = "";

	var elems = fld.options;
	for (var i = 0, j = 0; i < elems.length; i++) 
	{
		if (elems[i].selected) 
		{
			vals += delim + elems[i].value;
			delim = use_delim;
		}
	}
	
	return vals;
}
