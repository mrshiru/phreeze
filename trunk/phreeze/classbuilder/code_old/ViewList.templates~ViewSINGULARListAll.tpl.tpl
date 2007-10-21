{ldelim}include file="_header.tpl" title="{$table->Name|studlycaps}"{rdelim}
{* build the list of columns for our datagrid*} 
{assign var=columns value=""}
{assign var=delim value=""}
{foreach from=$table->Columns item=column}
	{assign var=colname value=$column->NameWithoutPrefix|studlycaps}
	{if $column->Key == "MUL"}
	{foreach from=$table->Constraints item=constraint}
			{if $constraint->KeyColumn==$column->Name}
				{assign var=constname value=$constraint->Name|studlycaps|replace:$plural:''}
				{assign var=descriptor value=$constraint->ReferenceTable->GetDescriptorName()|studlycaps}
				{assign var=colname value=Get$constname()->$descriptor|$constname}
			{/if}
		{/foreach}
	{elseif $column->Type == "datetime" || $column->Type == "date"}
		{assign var=colname value=$colname|cat:"|"|cat:$colname|cat:"|date(m/d/Y)"}
	{else}
		{assign var=colname value=$colname|cat:"|"|cat:$colname}
	{/if}
	{assign var=columns value=$columns|cat:$delim|cat:$colname}
	{assign var=delim value=","}
{/foreach}

<form action="index.php" method="get">

	<fieldset>
	<legend>{$plural|studlycaps}</legend>

	{ldelim}datagrid 
		page=${$singular}DataPage
		edit_url="index.php?action={$singular}/Edit" 
		delete_url="index.php?action={$singular}/Delete"
		primary_key="{$table->GetPrimaryKeyName()|studlycaps}"
		columns="{$columns}"
	{rdelim}

	</fieldset>
	
	<p><input type="button" value="New {$singular}" onclick="self.location='index.php?action={$singular}/Create';" /></p>

</form>

{ldelim}include file="_footer.tpl"{rdelim}
