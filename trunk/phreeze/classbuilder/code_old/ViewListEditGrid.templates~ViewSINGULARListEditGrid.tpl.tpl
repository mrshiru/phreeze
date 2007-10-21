{ldelim}include file="_header.tpl" title="Account{rdelim}
 
<!-- YAHOO UI Utilities Lib, you will need to replace this with the path to your YUI lib file -->
<script type="text/javascript" src="/js/yui/utilities/utilities.js"></script>

<link rel="stylesheet" type="text/css" href="/js/yui-ext/resources/css/grid.css" />

<script type="text/javascript" src="/js/yui-ext/yui-ext.js"></script>

<!-- you must define the select box here, as the custom editor for the 'Light' column will require it -->
<select name="Role" id="Role" class="ygrid-editor" style="visibility: hidden;">
	<option value="1">Anonymous</option>
	<option value="2">Renter</option>
	<option value="3">Owner</option>
	<option value="4">Employee</option>
	<option value="5">Manager</option>
	<option value="6">Board</option>
	<option value="7">Admin</option>
</select>

<p>Accounts</p>

<p>
<!-- a place holder for the grid. requires the unique id to be passed in the javascript function, and width and height ! -->
<div id="example-grid" class="ygrid-mso" style="border: 1px solid #c3daf9; overflow: hidden;"></div>

</p>

{ldelim}literal{rdelim}
<script type="text/javascript">
/*
 * yui-ext
 * Copyright(c) 2006, Jack Slocum.
 */

var yuiOnEdit = function(){ldelim}

    var startEditing = function(value, row, cell){ldelim}
    {rdelim};
    
    var stopEditing = function(){ldelim}
        alert('stop editing');  
    {rdelim};

{rdelim}();

var yuiGrid = function(){ldelim}
    var dataModel;
    var grid;
    var colModel;
    
    var formatMoney = function(value){ldelim}
        value -= 0;
        value = (Math.round(value*100))/100;
        value = (value == Math.floor(value)) ? value + '.00' : ( (value*10 == Math.floor(value*10)) ? value + '0' : value);
        return "$" + value;  
    {rdelim};
    
    var formatBoolean = function(value){ldelim}
        return value ? 'Yes' : 'No';  
    {rdelim};
    
    var formatDate = function(value){ldelim}
        return value.dateFormat('M d, Y');  
    {rdelim};
    
    var parseDate = function(value){ldelim}
        return new Date(Date.parse(value));  
    {rdelim};
    
    var formatDate = function(value){ldelim}
        val = new Date(Date.parse(value));  
        return val.dateFormat('m/d/Y');  
        // return val.dateFormat('M d, Y');  
    {rdelim};

    var formatRole = function(value) {ldelim}
         var statusselect = document.getElementById('Role').options;
         var myval;
         for ( var i = 0, len = statusselect.length; i < len; i++ )
         {ldelim}
            if (statusselect[i].value == value )
            {ldelim}
               myval = statusselect[i].text;
            {rdelim}
         {rdelim}
         return myval;
     {rdelim}

    
    return {ldelim}
    init : function(){ldelim}
        var schema = {ldelim}
            tagName: 'Account',
            totalTag: 'TotalRecords',
            id: 'Id',
            fields: ['Id','RoleId','FirstName','LastName','Username','Password','Created']
        {rdelim};
        dataModel = new YAHOO.ext.grid.XMLDataModel(schema);
        
        // the DefaultColumnModel expects this blob to define columns. It can be extended to provide 
        // custom or reusable ColumnModels
        
        var yg = YAHOO.ext.grid;
        
        //editor: new yg.SelectEditor('Role')
        
        var colModel = new YAHOO.ext.grid.DefaultColumnModel([
			{ldelim}header: "Id", width: 25, sortable: true{rdelim}, 
			{ldelim}header: "Role", width: 100, sortable: true, renderer:formatRole, editor: new yg.SelectEditor('Role'){rdelim}, 
			{ldelim}header: "First Name", width: 100, sortable: true, editor: new yg.TextEditor({ldelim}allowBlank: true{rdelim}){rdelim}, 
			{ldelim}header: "Last Name", width: 100, sortable: true, editor: new yg.TextEditor({ldelim}allowBlank: true{rdelim}){rdelim}, 
			{ldelim}header: "Username", width: 100, sortable: true, editor: new yg.TextEditor({ldelim}allowBlank: true{rdelim}){rdelim}, 
			{ldelim}header: "Password", width: 100, sortable: true, editor: new yg.TextEditor({ldelim}allowBlank: true{rdelim}){rdelim},
			{ldelim}header: "Created", width: 140, sortable: true, renderer:formatDate, editor: new yg.DateEditor({ldelim}format: 'm/d/Y'{rdelim}){rdelim}
		]);
		
		// create the Grid
        var grid = new YAHOO.ext.grid.EditorGrid('example-grid', dataModel, colModel);
        grid.autoWidth = true;
        grid.autoHeight = true;
    	// grid.getSelectionModel().clicksToActivateCell = 2; // force double-click to edit
        grid.render();

        var oncellupdate = function(model,row,column){ldelim}
			//alert(model.getDocument());
			alert(row+':'+column);
        {rdelim};

        dataModel.addListener('CellUpdated',oncellupdate);

		/*
        var onscroll = function(top,left){ldelim}
			//alert(model.getDocument());
			alert(top+':'+left);
        {rdelim};
        grid.addListener('BodyScroll',onscroll);
        */

        dataModel.initPaging('index.php?action=Account/ListPage',5);
        dataModel.loadPage(1);
        
        //dataModel.load('index.php?action=Account/RenderXML');
        
		var toolbar = grid.getView().getPageToolbar();
		toolbar.addSeparator();
		toolbar.addButton({ldelim}
		className: 'new-topic-button',
		text: "New Topic",
		click: createTopic
		{rdelim});

        
    {rdelim}
    {rdelim}
{rdelim}();

YAHOO.ext.EventManager.onDocumentReady(yuiGrid.init, yuiGrid, true);

</script>

{ldelim}/literal{rdelim}

																																																																																																																																																												
{ldelim}include file="_footer.tpl"{rdelim}