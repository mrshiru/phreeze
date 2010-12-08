{literal}/**
 * ajax validation for a form that uses the phreeze framework
 *
 * @version 2.2
 * @requires Ext for AJAX connection
 * @requires Ext.util.JSON, Ext.Element and Ext.Fx 
 * @requires scripts/verysimple/validate.js
 */

var frm_ref;
var validate_in_progress = false;


/**
 * server-side validation of the form using the controller for the given model
 * and, on success, submits the form.  on fail, displays the errors received
 * from the server.
 * 
 * @param Form reference to form containing user input used to update model
 * @param string name of model to validate
 * @param saveInline bool true to immediately save the model after validating
 * @param Function reference to function to call on server response.  if provided, the form will not be automatically submitted
 * @param Function reference to function to call if validation fails
 */
function validateModel(frm, model, saveInline, callback, failureCallback)
{
    
    if (validate_in_progress)
    {
        alert('Form validation is in progress.  Please wait one moment...');
        return false;
    }
    
	// TODO: replace this with Ext DomQuery.
	var validators = getElementsByClass('validator');
	for (i=0;i<validators.length;i++)
	{
		validators[i].style.display='none';
	}
   
	frm_ref = frm;
	
	var url = 'index.php?action='+model+'.ValidateInput';
	var pars = Array();
	
    for (var i = 0; i < frm.elements.length; i++)
    {
		if (frm.elements[i].name != '' && frm.elements[i].name != 'action')
		{
			var elem = frm.elements[i];

			if (elem.type == "hidden"  || elem.type == "text" || elem.type == "password" || elem.type == "textarea")
			{
				pars[ escape(elem.name.replace(/ /g,'_') ) ] = getFieldValue(elem);
			} 
			else if (elem.type == "checkbox" && elem.checked == true)
			{
				pars[ escape(elem.name.replace(/ /g,'_') ) ] = elem.value;				
			}
			else if (elem.type == "select-one" || elem.type == "radio")
			{
				pars[ escape(elem.name.replace(/ /g,'_') ) ] = getFieldValue(elem);				
			}
        }
    }
    
    if (saveInline)
    {
		pars['SaveInline'] =true;
    }

	Ext.Ajax.request({
		url: url,
		success: processServerResponse,
		failure: failureCallback ? failureCallback : function(o) {alert('Unable to connect AJAX service: ' + o.statusText)},
		params: pars,
		argument: {inline: saveInline, callbackFunction: callback, failCallbackFunction: failureCallback}
	});
	
	return false;
}

/**
 * Attempt to parse the error message when the server returns HTML instead of JSON
 * @param string html
 * @return string
 */
function getErrorMessage(html)
{
	var ertxt = '';
	var arr = html.split('class="warning">');
	if (arr.length > 1)
	{
		var arr2 = arr[1].split('</');
		errtxt = 'The server returned an error: ' + arr2[0];
	}
	else
	{
		errtxt = 'Oh Snap! The server sent back an unknown error.';
	}
	
	return errtxt;
}

/**
 * Processes the validation response from the server, which should be JSON code
 */
function processServerResponse(response)
{
	var result;
	var inline = response.argument.inline;
	var callbackFunction = response.argument.callbackFunction;
	var failCallbackFunction = response.argument.failCallbackFunction;
	
	try
	{
		result = Ext.util.JSON.decode(response.responseText);
	}
	catch(err)
	{
		validate_in_progress = false;
		
		err_message = getErrorMessage(response.responseText);

	    if (failCallbackFunction)
	    {
			failCallbackFunction(result, frm_ref);
	    }
	    else
	    {
			if ( confirm(err_message + '  Unable to validate.  Do you want to try to submit the form anyway?') ) frm_ref.submit();
	    }

		return false;
	}
	
	
	if (result.Success)
	{
	    if (callbackFunction)
	    {
			callbackFunction(result, frm_ref);
	    }
	    else
	    {
			// no callback - submit the form.  (because this is asycronous returning true doens't work.)
			frm_ref.submit();
	    }
	}
	else
	{
	    for (var fldname in result.Errors)
	    {
	        if (fldname != 'toJSONString')
	        {

			    var divref = Ext.get(fldname + '_Error');

			    if (divref)
			    {
			        // divref.style.display = 'inline';
			        divref.update("" + result.Errors[fldname] + "");
			        divref.setStyle('display','inline');
			        divref.highlight();
			    }
			    else
			    {
			        alert(fldname + ": " + result.Errors[fldname]);
			    }
	        }
	    }
	    
	    var divref = Ext.get('Validator_Error');
	    if (divref)
	    {
	        divref.update("Errors were found on this form.  Please correct them and submit again.");
		    divref.setStyle('display','block');
	        divref.slideIn();
	    }
	    else
	    {
	        alert("Errors were found on this form.  Please correct them and submit again.");
	    }
	    
	    validate_in_progress = false;
	    
	    // if a hander was specified, make sure it gets called
	    if (callbackFunction)
	    {
			callbackFunction(result, frm_ref);
	    }
	}
}

/**
 * getElementByClass function from jquery.
 * TODO: replace this with functionality already present in Ext
 */
function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}{/literal}