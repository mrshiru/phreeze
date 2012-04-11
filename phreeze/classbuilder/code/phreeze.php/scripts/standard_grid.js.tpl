{literal}
/**
 * StandardGrid is a utility class for creating an Ext datagrid that is bound
 * to a Phreeze MVC app.  It includes features for making the grid clickable,
 * making the grid resize to fit it's container, and various other things.
 * It is recommended not to change this file, rather to write custom code
 * if a particular grid need to be specialized.
 */

/** @var array storing a reference to all grids created within this page scope */
var standardGridReferences = [];

/**
 * StandardGrid factory class.  Use StandardGrid.init to create grid instances
 */
var StandardGrid = {
		
	version: 3.0,
		
	/**
	 * Initialize an Ext grid
	 * @param stdClass config object
	 * @return Ext.GridPanel
	 */
	init : function(config){
	
		var objectName = config['objectName'];
		var pkName = config['pkName'];
		var pageSize = config['pageSize'] ? config['pageSize'] : 10;
		var gridDiv = config['gridDiv'];
		var panelDiv = config['panelDiv'];
		var readerDef = config['readerDef'];
		var columnDef = config['columnDef'];
		var autoExpandColumn = config['autoExpandColumn'];
		var urlFormat = config['urlFormat'] ? config['urlFormat'] : ('index.php?action='+config['objectName']+'.%s');
		var urlCallback = config['urlCallback'];
		var header = config['header'] ? config['header'] : true;
		
		/** @var Store object which will call a Phreeze controller for it's data */
		var ds = new Ext.data.Store({
			
			proxy: new Ext.data.HttpProxy({
				url: urlFormat.replace(/\%s/, "ListPage")
			}),
			
			reader: new Ext.data.XmlReader({
				record: objectName,
				totalProperty: 'TotalRecords',
				id: pkName 
				}, 
				readerDef
			),
			
			remoteSort: true
		});
		
		// when data doesn't load correctly, we don't get an error but it does raise this event
		ds.on('loadexception',onLoadException);
		
		// fetch the initial data from the controller
		ds.load({params:{start:0,limit:pageSize}});

		/**
		 * fires when the reader has an exception loading data
		 */
		function onLoadException(proxy,request,response,args)
		{
	   		var errtxt = parseServerWarning(response, args.message);
			Ext.Msg.alert("Oh Snap!",errtxt + '<br/><br/>If your session has expired, you may need to log in again.');
		}

		/**
		 * if the server returns an error instead of json, use this function to
		 * try to figure out what the error message is
		 */
		function parseServerWarning(response, defaultMessage)
		{
			var errtxt = defaultMessage;
			
	   	  	if (response && response.responseText)
			{
				var arr = response.responseText.split('class="warning">');
				if (arr.length > 1)
				{
					var arr2 = arr[1].split('</');
					errtxt = 'Server exception: ' + arr2[0];
				}
				else
				{
					errtxt = 'Error parsing server response: Unknown Error';
				}
			}
	   	  	
	   	  	return errtxt;
		}
		
		/**
		 * Returns the URL that should be used for displaying a single record
		 * @param variant primary key of the record
		 * @return string URL to the view/edit page for this record
		 */
		function getUrl(pk)
		{
			url = "";
			if (urlCallback != null)
			{
				url = urlCallback(pk);
			}
			else
			{
				url = urlFormat.replace(/\%s/, "Edit") + '&'+pkName+'='+pk
			}
			
			return url;
		}

		/**
		 * Handles when a row is double-clicked
		 */
		function rowDblClick(grid,rowIndex,e)
		{
			var pk = grid.getStore().getAt(rowIndex).id;
			self.location=getUrl(pk);
		}

		/**
		 * Handles when the new record button is clicked
		 */
		function newRecordClick (button,e)
		{
			self.location=getUrl("");
		}

		/** @var ColumnModel */
		var colModel = new Ext.grid.ColumnModel(columnDef);
		
		var pageBarItems = [];

		if (!config['hideNew']) 
		{
			pageBarItems.push(
				new Ext.Spacer({width: 8})
			);
			pageBarItems.push(
				new Ext.Button({
					text:'Add New '+ objectName.replace(/([A-Z])/g, ' $1') + '',
					handler: newRecordClick,
					cls:'x-btn-text-icon toolbarbutton',
					icon: 'images/add.png',
					toolTip: 'Click to add a new record...'
				})
			);
		}

		/** @var PagingToolbar the footer toolbar which handles pagination */
		var pageBar = new Ext.PagingToolbar({
			store: ds,
			pageSize: pageSize,
			displayInfo: true,
			displayMsg: 'Displaying records {0} - {1} of {2}',
			emptyMsg: "No records to display",
			items: pageBarItems,
			prependButtons: false
		});
		
		// ua = navigator.userAgent.toLowerCase();
		// var isFireFox = ua.indexOf("firefox") > -1;
		// var isFireFox3 = ua.indexOf("firefox/3") > -1;

		// get the container which will be used for sizing the grid
		var containerId = objectName+'GridPanel';
		var container = Ext.get(containerId);
		var containerW = container.getWidth();
		var containerH = container.getHeight();
		
		/** @var GridPanel the container for the Ext Grid */
		var grid = new Ext.grid.GridPanel({
			store: ds,
			cm: colModel,
			bbar: pageBar,
			frame: true,
			height: containerH,
			layout: 'fit', // this should take the place of width: containerW
			title: (objectName + 's').replace(/([A-Z])/g, ' $1'),
			header: header
		});

		// listener must be added before rendering
		// grid.addListener('rowdblclick',rowDblClick);
		grid.addListener('rowclick',rowDblClick);

		// render the grid into the panel div
		grid.applyToMarkup(containerId);

		// grid.getSelectionModel().selectFirstRow();
		
		// grab a reference to this so we can resize the grid when the window size changes
		standardGridReferences.push( {grid: grid, container: container} );

		// return the grid so the caller can get a reference if they want
		return grid;
	}
};

/** @var int a page-scope variable used to buffer re-drawing when the browser window is re-sized */
var standardGridResizeTimeoutId;

/**
 * This will re-size any standard grids that have been generated within this page scope.
 * This is called by standardGridOnWindowResize, which buffers so that there isn't
 * too much re-drawing while the drag is occuring
 * @return
 */
function resizeStandardGrids()
{
	for (var i in standardGridReferences)
	{
		g = standardGridReferences[i];
		
		if (g.grid)
		{
			if (typeof(console) != 'undefined') console.log('resizing standardGrid to width of' + g.container.getWidth() );

			// @TODO if the grid is a variable height, uncomment this line
			// g.grid.setSize( container.getWidth(), container.getHeight());
			
			// @HACK for some reason even thought the grid layout is set to "fit" it doesn't refresh
			// the inner contents of the grid, including the columns and scroll bar.  This forces
			// it to redraw
			g.grid.setWidth('100%');
			g.grid.syncSize();
		}
	}
}

/**
 * This handles the window resize so that adjustments can be made to the grid
 * it is buffered so that rewrites don't happen too often during the drag
 */
function standardGridOnWindowResize() 
{
	window.clearTimeout(standardGridResizeTimeoutId);
	standardGridResizeTimeoutId = window.setTimeout('resizeStandardGrids();', 1);
}

Ext.EventManager.onWindowResize(standardGridOnWindowResize)	
{/literal}