
/** 
 * Sets the value of all checkboxes in a checkbox group
 *
 * @param Element frm reference to form Element
 * @param string cbGroupName name of the checkbox group
 * @param bool newValue true to check all, or false to uncheck
 * @return Element
 */
function checkAll(frm, cbGroupName, newValue)
{
    for (i = 0; i < frm.elements.length; i++)
    {
        if (frm.elements[i].type == "checkbox" && frm.elements[i].name == cbGroupName)
        {
            frm.elements[i].checked = newValue;
        }
    }
}

/** 
 * Returns the dom object given the id string
 *
 * @param string tagId the id of the dom item
 * @return Element
 */
function getTag(tagId)
{
    return ( document.getElementById ? document.getElementById(tagId) : document.all(tagId) );
}

/** 
 * Checks specified fields on the given form.  if any of the specified
 * fields are blank, then it opens an alert box and returns false.
 * otherwise it returns true.
 * Drop-downs that are validated assume first option is not valid (ie "select one")
 * Checkbox groups & multi-selects are not validated
 *
 * Example: onsubmit="return requireFields(this,['field1','field2'],['msg1','msg2'])"
 *
 * @param  Form  reference to form object to be validated
 * @param  Array array of fieldname to be tested
 * @param  Array array of error messages for each field
 * @return bool
 */
function requireFields(objForm, arrFieldNames, arrErrorMessage) 
{
	if (arrFieldNames.length != arrErrorMessage.length) 
	{
		alert('requireFields error: arrays do not match');
		return false;
	}

	var nElements = objForm.elements.length;
	var objElement, strErrorMessage;

	// loop through all the form elements
	for (var nNum = 0; nNum < nElements; nNum++) 
	{
		objElement = objForm.elements[nNum];

		// loop through the required fields array
		for (var nInner = 0; nInner < arrFieldNames.length; nInner++) 
		{

			// if this element is in the required fields array, then check to see
			// if it is empty.  if so, alert and return false
			if (arrFieldNames[nInner] == objElement.name) 
			{
				if (arrErrorMessage[nInner] != '') 
				{
					strErrorMessage = arrErrorMessage[nInner];
				} 
				else 
				{
					strErrorMessage = objElement.name + ' is required.';
				}

				if ((objElement.type == "text" || objElement.type == "password") && (objElement.value == "" || objElement.value == null)) 
				{
					alert(strErrorMessage);
					objElement.focus();
					return false
				} 
				else if ((objElement.type == "textarea") && (objElement.value == "" || objElement.value == null)) 
				{
					alert(strErrorMessage);
					return false
				} 
				else if (objElement.type == "select-one" && objElement.selectedIndex == 0)
				{
					alert(strErrorMessage);
					objElement.focus();
					return false
				} 
				else if (objElement.type == "radio" && radioButtonFieldIsComplete(objElement) == false)
				{
					alert(strErrorMessage);
					return false
				}
			}

		}

	}
	
	// if we made it all the way to the end, then there were no problems
	return true;
}


/**
 * Checks that the specified radio button group has an option selected
 *
 * @param  RadioButtonGroup  reference to radio button object to be validated
 * @return bool
 */
function radioButtonFieldIsComplete(objRadioButtonGroup) 
{

	// Loop from zero to the one minus the number of radio button selections
	for (var counter = 0; counter < objRadioButtonGroup.length; counter++) 
	{
		// If a radio button has been selected it will return true
		if (objRadioButtonGroup[counter].checked) 
		{
			return true;
		}
	}

	// if we made it this far, then none were checked
	return false;
}


/**
 * Returns an array containing all selected/checked values for a form element.
 *
 * @param  Element objElement
 * @return array
 */
function getValues(objElement) 
{
	var i;
	var objGroup;
	var vals = new Array();
	var eType = objElement.type;
	if ((objElement.type == "checkbox") || (objElement.type == "radio")) 
	{
		objGroup = getFormElements(objElement.form, objElement.name);
		
		for (i = 0, j = 0; i < objGroup.length; i++) 
		{
			if (eType == objGroup[i].type) 
			{
				if (objGroup[i].checked) 
				{
					vals[j++] = objGroup[i].value;
				}
			}
		}
	} 
	else if (objElement.type == "select-multiple") 
	{
		objGroup = objElement.options;
		for (i = 0, j = 0; i < objGroup.length; i++) 
		{
			if (objGroup[i].selected) 
			{
				vals[j++] = objGroup[i].value;
			}
		}
	} 
	else 
	{
		vals[0] = objElement.value;
	}
	
	return vals;
}