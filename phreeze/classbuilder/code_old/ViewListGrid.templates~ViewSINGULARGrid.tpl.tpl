{include file="_header.tpl" title="Account}
 
<script type="text/javascript" src="/js/yui/utilities/utilities.js"></script>
<script type="text/javascript" src="/js/yui-ext/yui-ext.js"></script>

<link rel="stylesheet" type="text/css" href="/js/yui-ext/resources/css/grid.css" />
<link rel="stylesheet" type="text/css" href="/js/yui-ext/resources/css/toolbar.css" />

<!-- a place holder for the grid. requires the unique id to be passed in the javascript function, and width and height ! -->
<div id="account-grid" class="ygrid-mso" style="border: 1px solid #c3daf9; overflow: hidden; height: 300px; width: 700px;"></div>

{literal}

<style>
.ytoolbar .new-button{
	padding:1px;
	width:auto;
	display:block;
}
</style>

<script type="text/javascript">

function onNewRecord()
{
	alert('onNewRecord');
}

function onSelect(model, row, selected)
{
	//alert('onSelect');
}

function cellDblClick(grid, row, column, e)
{
	alert('cellDblClick');
}

// limit selection in the grid to one row
sm = new YAHOO.ext.grid.SingleSelectionModel();

sm.addListener('rowselect', onSelect);

yg = YAHOO.ext.grid;

// create our column model
cm = new YAHOO.ext.grid.DefaultColumnModel([
	{header: "Id", width: 50}, 
	{header: "Role"}, 
	{header: "First Name", editor: new yg.TextEditor()}, 
	{header: "Last Name"}, 
	{header: "Username"}, 
	{header: "Password"},
	{header: "Created"}
]);
cm.defaultSortable = true;

// create the data model
dm = new YAHOO.ext.grid.XMLDataModel({
	tagName: 'Account',
	totalTag: 'TotalRecords',
	id: 'Id',
	fields: ['Id','RoleId','FirstName','LastName','Username','Password','Created']
});

dm.initPaging('index.php?action=Account/ListPage', 4);

// create the grid object
grid = new YAHOO.ext.grid.Grid('account-grid', dm, cm, sm);
//grid.autoWidth = true;
grid.autoSizeColumns = true;
grid.maxRowsToMeasure = 5;
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

</script>

{/literal}

																																																																																																																																																												
{include file="_footer.tpl"}
