
var StandardGrid = {ldelim}
    init : function(config){ldelim}
    
		var objectName = config['objectName'];
		var pkName = config['pkName'];
		var pageSize = config['pageSize'] ? config['pageSize'] : 10;
		var gridDiv = config['gridDiv'];
		var panelDiv = config['panelDiv'];
		var readerDef = config['readerDef'];
		var columnDef = config['columnDef'];
		var autoExpandColumn = config['autoExpandColumn'];
		var urlFormat = config['urlFormat'] ? config['urlFormat'] : ('index.php?action='+config['objectName']+'.%s');
    
		var ds = new Ext.data.Store({ldelim}
			
			proxy: new Ext.data.HttpProxy({ldelim}
				url: urlFormat.replace(/\%s/, "ListPage")
			{rdelim}),
			
			reader: new Ext.data.XmlReader({ldelim}
				record: objectName,
				totalRecords: 'TotalRecords',
				id: pkName 
				{rdelim}, 
				readerDef
			),
			
			remoteSort: true
		{rdelim});
		
        ds.load();

		// event handlers
		function rowDblClick(grid,rowIndex,e)
		{ldelim}
			var pk = grid.getStore().getAt(rowIndex).id;
			self.location=urlFormat.replace(/\%s/, "Edit") + '&'+pkName+'='+pk;
		{rdelim}

		function newRecordClick (button,e)
		{ldelim}
			self.location=urlFormat.replace(/\%s/, "Edit");
		{rdelim}

		// convert the config array into a columnmodel
        var colModel = new Ext.grid.ColumnModel(columnDef);

		var pageBar = new Ext.PagingToolbar({ldelim}
			store: ds,
			pageSize: pageSize,
			displayInfo: true,
			displayMsg: 'Displaying records {ldelim}0{rdelim} - {ldelim}1{rdelim} of {ldelim}2{rdelim}',
			emptyMsg: "No records to display"
		{rdelim});
		
		// create the Grid
        var grid = new Ext.grid.GridPanel({ldelim}
            store: ds,
            cm: colModel,
            bbar: pageBar,
            frame: true
            {rdelim});

		// listener must be added before rendering
		grid.addListener('rowdblclick',rowDblClick);

		// render the grid into the panel div
        grid.applyToMarkup(objectName+'GridPanel');

		// buttons must be added after rendering
		pageBar.addButton(new Ext.Toolbar.Button({ldelim}
		    text:'New Record...',
		    handler: newRecordClick
		{rdelim}));

		
        // grid.getSelectionModel().selectFirstRow();
    {rdelim}
{rdelim};