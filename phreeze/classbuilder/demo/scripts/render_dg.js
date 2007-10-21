/*
this script expects the following to be defined at page level:
    phreeze_div
    string phreeze_obj
    string phreeze_pk
    array phreeze_headers
    array phreeze_fields
*/

var dg = function() {

	var onNewRecord = function()
	{
		self.location = 'index.php?action=' + phreeze_obj + '/Edit';
	};

	var onSelect = function(model, row, selected)
	{

	};

	var cellDblClick = function(grid, row, column, e)
	{
		self.location = 'index.php?action=' + phreeze_obj + '/Edit&Id=' + grid.getSelectedRowId();
	};

	return {
		init : function(){
			// limit selection in the grid to one row
			sm = new YAHOO.ext.grid.SingleSelectionModel();

			sm.addListener('rowselect', onSelect);

			yg = YAHOO.ext.grid;

			// create our column model
			cm = new YAHOO.ext.grid.DefaultColumnModel(phreeze_headers);
			cm.defaultSortable = true;

			// create the data model
			dm = new YAHOO.ext.grid.XMLDataModel({
				tagName: phreeze_obj,
				totalTag: 'TotalRecords',
				id: phreeze_pk,
				fields: phreeze_fields
			});

			dm.baseParams['action'] = phreeze_obj + '/ListPage';
			dm.initPaging('index.php', 10);

			// create the grid object
			grid = new YAHOO.ext.grid.Grid(phreeze_div, dm, cm, sm);
			grid.autoWidth = true;
			//grid.autoSizeColumns = true;
			//grid.maxRowsToMeasure = 5;
			grid.addListener('celldblclick', cellDblClick);

			grid.render();

			// see below about the paging toolbar
			var toolbar = grid.getView().getPageToolbar();
			toolbar.addSeparator();

			toolbar.addButton({
				className: 'new-button',
				text: "New Record",
				click: onNewRecord
			});

			// the grid is ready, load page 1 of topics
			dm.loadPage(1);
		}
	};

}();

YAHOO.ext.EventManager.onDocumentReady(dg.init, dg, true);
