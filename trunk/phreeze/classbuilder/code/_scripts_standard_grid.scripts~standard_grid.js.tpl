
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
		{rdelim}); // datastore
        ds.load();

		// event handlers
		function rowDblClick(grid,rowIndex,e)
		{ldelim}
			var pk = grid.getDataSource().getAt(rowIndex).id;
			self.location=urlFormat.replace(/\%s/, "Edit") + '&'+pkName+'='+pk;
		{rdelim}

		function newRecordClick (button,e)
		{ldelim}
			self.location=urlFormat.replace(/\%s/, "Edit");
		{rdelim}

		// the DefaultColumnModel expects this blob to define columns. It can be extended to provide
        // custom or reusable ColumnModels
        var colModel = new Ext.grid.ColumnModel(columnDef);

        // create the Grid
        var grid = new Ext.grid.Grid(''+objectName+'Grid', {ldelim}
            ds: ds,
            cm: colModel
        {rdelim});

        var layout = Ext.BorderLayout.create({ldelim}
            center: {ldelim}
                margins:{ldelim}left:3,top:3,right:3,bottom:3{rdelim},
                panels: [new Ext.GridPanel(grid)]
            {rdelim}
        {rdelim}, ''+objectName+'GridPanel');

		grid.addListener('rowdblclick',rowDblClick);

        grid.render();

		var gridFoot = grid.getView().getFooterPanel(true);

		var pageBar = new Ext.PagingToolbar(gridFoot, ds, {ldelim}
			pageSize: pageSize,
			displayInfo: true,
			displayMsg: 'Displaying records {ldelim}0{rdelim} - {ldelim}1{rdelim} of {ldelim}2{rdelim}',
			emptyMsg: "No records to display"
		{rdelim});
		
		pageBar.addButton(new Ext.Toolbar.Button({ldelim}
		    text:'New Record...',
		    handler: newRecordClick
		{rdelim}));
		
        // grid.getSelectionModel().selectFirstRow();
    {rdelim}
{rdelim};