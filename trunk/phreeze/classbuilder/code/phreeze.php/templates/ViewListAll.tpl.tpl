{ldelim}include file="_header.tpl" include_ext="true" title="Manage {$plural}"{rdelim}

<h1>Manage {$plural}</h1>

<p>Below are all {$singular} records in the system.  
Double click on a row to edit an existing {$singular}.  
Use the "New Record" button to add a new {$singular}.</p>

<!-- placeholder for the grid -->
<div id="{$singular}GridPanel" style="width:100%; height:300px;">
	<div id="{$singular}Grid" class="ygrid-mso"></div>
</div>

{ldelim}literal{rdelim}
<script type="text/javascript" src="scripts/standard_grid.js"></script>
<script type="text/javascript">

{assign var=comma value=" "}
var readerDef = [
{foreach from=$table->Columns item=column}
{assign var=colname value=$column->NameWithoutPrefix|studlycaps}
	{$comma}{ldelim}name: '{$colname}', mapping: '{$colname}'{rdelim}
{assign var=comma value=","}
{/foreach}
];

{assign var=comma value=" "}
var columnDef = [
{foreach from=$table->Columns item=column}
{assign var=colname value=$column->NameWithoutPrefix|studlycaps}
	 {$comma}{ldelim}header: '{$colname}', dataIndex: '{$colname}', width: 150, sortable: true{rdelim}
{assign var=comma value=","}
{/foreach}
];

var config = {ldelim}
	objectName: '{$singular}',
	pkName: '{$table->GetPrimaryKeyName()|studlycaps}',
	columnDef: columnDef,
	readerDef: readerDef,
	gridDiv: '{$singular}Grid',
	panelDiv: '{$singular}GridPanel',
	autoExpandColumn : 0,
	urlFormat: '{ldelim}/literal{rdelim}{ldelim}$URL->Get('{$singular}','%s'){rdelim}{ldelim}literal{rdelim}'
{rdelim};



Ext.onReady(
	function(){ldelim}StandardGrid.init(config);{rdelim}, 
	StandardGrid
);

</script>
{ldelim}/literal{rdelim}
																																																																																																																																																											
{ldelim}include file="_footer.tpl"{rdelim}