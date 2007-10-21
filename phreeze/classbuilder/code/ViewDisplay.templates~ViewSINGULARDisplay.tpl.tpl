{ldelim}include file="_header.tpl" title="{$table->Name|studlycaps} Details"{rdelim}


	<fieldset>
	<legend>{$table->Name|studlycaps} Details</legend>

{foreach from=$table->Columns item=column}
	{if $column->NameWithoutPrefix != $table->GetPrimaryKeyName()}
		<div class="field">
		<label for="{$column->NameWithoutPrefix|studlycaps}">{$column->NameWithoutPrefix|studlycaps}:</label>
		{if $column->Key == "MUL"} {* this is is a key so we need a lookup *}
			{ldelim}assign var={$column->NameWithoutPrefix|studlycaps} value=${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}{rdelim}
			{ldelim}${$column->NameWithoutPrefix|studlycaps}Pairs.${$column->NameWithoutPrefix|studlycaps}{rdelim}
		{elseif $column->Type == "datetime" || $column->Type == "date"}
			{ldelim}${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}|escape{rdelim}
		{elseif $column->Type == "text" || $column->Type == "mediumtext" || $column->Type == "longtext" || $column->Type == "tinytext"}
			{ldelim}${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}|escape{rdelim}
		{else}
			{ldelim}${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}|escape{rdelim}
		{/if}
		</div>


	{/if}
{/foreach}

	</fieldset>

{foreach from=$table->Sets item=set}
	<fieldset>
		<legend>{$set->Name|studlycaps|replace:$singular:''}</legend>
		
		{ldelim}datagrid 
			page=${$set->Name|studlycaps|replace:$singular:''}DataPage
		{rdelim}

	</fieldset>

{/foreach}

{ldelim}include file="_footer.tpl"{rdelim}