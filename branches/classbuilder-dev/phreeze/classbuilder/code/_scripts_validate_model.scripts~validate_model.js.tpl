/**
 * ajax validation for a form that uses the phreeze framework
 *
 * @version 1.0
 * @requires YAHOO.util for AJAX connection
 * @requires Ext.util.JSON, Ext.Element and Ext.Fx 
 * @requires shared/js/verysimple/validate.js
 */


var frm_ref;
var validate_in_progress = false;


/**
 *
 */
function validateModel(frm, model)
{ldelim}
    
    if (validate_in_progress)
    {ldelim}
        alert('Form validation is in progress.  Please wait one moment...');
        return false;
    {rdelim}
    
	// TODO: replace this with Ext DomQuery.
	//var validators = getElementsByClass('validator');
   var validators = Ext.query('.validator');
	for (i=0;i<validators.length;i++)
	{ldelim}
		validators[i].style.display='none';
	{rdelim}
   
	frm_ref = frm;
	
	var url = 'index.php?action='+model+'.ValidateInput';
	var pars = "";
	var delim = "";
	
    for (var i = 0; i < frm.elements.length; i++)
    {ldelim}
		if (frm.elements[i].name != '' && frm.elements[i].name != 'action')
		{ldelim}
			var elem = frm.elements[i];

			if (elem.type == "hidden" || elem.type == "text" || elem.type == "password" || elem.type == "textarea")
			{ldelim}
				pars += delim  + elem.name + '=' + escape(getFieldValue(elem));
				delim = "&";
			{rdelim} 
			else if (elem.type == "select-one" || elem.type == "radio")
			{ldelim}
				pars += delim  + elem.name + '=' + escape(getFieldValue(elem));
				delim = "&";
			{rdelim}
        {rdelim}
    {rdelim}

{if $ExtAdapter == 'yui'}
    var validate_callback = 
    {ldelim} 
	    success: processServerResponse, 
	    failure: function(o) {ldelim}alert('Unable to validate via AJAX'){rdelim}, 
	    argument: [] 
    {rdelim} 

    YAHOO.util.Connect.asyncRequest('POST', url, validate_callback, pars);
{elseif $ExtAdapter == 'ext'}
    Ext.Ajax.request({ldelim}method:'POST', url:url, params:pars, callback:processServerResponse{rdelim});
{/if}

	return false;
{rdelim}

/**
 * Processes the validation response from the server, which should be JSON code
 */
{if $ExtAdapter == 'yui'}
function processServerResponse(response)
{elseif $ExtAdapter == 'ext'}
function processServerResponse(options, success, response)
{/if}
{ldelim}

	var result = Ext.util.JSON.decode(response.responseText);
	
	if (result.Success)
	{ldelim}
	    // submit the form
	    frm_ref.submit();
	{rdelim}
	else
	{ldelim}
	    for (var fldname in result.Errors)
	    {ldelim}
	        if (fldname != 'toJSONString')
	        {ldelim}

			    var divref = Ext.get(fldname + '_Error');

			    if (divref)
			    {ldelim}
			        // divref.style.display = 'inline';
			        divref.update("" + result.Errors[fldname] + "");
			        divref.setStyle('display','inline');
			        divref.highlight();
			    {rdelim}
			    else
			    {ldelim}
			        alert(fldname + ": " + result.Errors[fldname]);
			    {rdelim}
	        {rdelim}
	    {rdelim}
	    
		    var divref = Ext.get('Validator_Error');
		    if (divref)
		    {ldelim}
		        divref.update("Errors were found on this form.  Please correct them and submit again.");
			    divref.setStyle('display','block');
		        divref.slideIn();
		    {rdelim}
		    else
		    {ldelim}
		        alert("Errors were found on this form.  Please correct them and submit again.");
		    {rdelim}
	    
	    validate_in_progress = false;
	{rdelim}
{rdelim}

/**
 * getElementByClass function from jquery.
 * TODO: replace this with functionality already present in Ext
 */
function getElementsByClass(searchClass,node,tag) {ldelim}
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {ldelim}
		if ( pattern.test(els[i].className) ) {ldelim}
			classElements[j] = els[i];
			j++;
		{rdelim}
	{rdelim}
	return classElements;
{rdelim}
