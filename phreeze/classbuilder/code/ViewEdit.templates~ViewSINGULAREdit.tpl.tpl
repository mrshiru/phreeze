{ldelim}include file="_header.tpl" title="Edit {$singular|studlycaps}"{rdelim}

<!-- verysimple form libs -->
<script type="text/javascript" src="{$PathToVerySimpleScripts}validate.js"></script>
<link rel="stylesheet" rev="stylesheet" href="{$PathToVerySimpleScripts}resources/css/tables.css" />
<link rel="stylesheet" rev="stylesheet" href="{$PathToVerySimpleScripts}resources/css/forms.css" />

<!-- local form libs -->
<script type="text/javascript" src="scripts/standard_form.js"></script>
<script type="text/javascript" src="scripts/validate_model.js"></script>


<form id="{$singular|studlycaps}Form" action="{ldelim}$URL->Get('{$singular}','Save'){rdelim}" onsubmit="return validateModel(this,'{$singular}');" method="post">

<fieldset id="{$singular|studlycaps}FieldSet">
	<legend>Edit {$table->Name|studlycaps}</legend>
{foreach from=$table->Columns item=column}
{if !$table->PrimaryKeyIsAutoIncrement()}
	{if $column->Key == "PRI"}{ldelim}if ${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} == ''}{ldelim}assign var=force_insert value=1{rdelim}{ldelim}else{rdelim}{ldelim}assign var=force_insert value=0{rdelim}{ldelim}/if{rdelim}{/if}
{/if}
	<div class="x-form-item">
		<label style="width: 120px;" for="{$column->NameWithoutPrefix|studlycaps}">{$column->NameWithoutPrefix|studlycaps}:</label>
		<div class="x-form-element">
{if $column->Extra == 'auto_increment'} {* this is an auto-increment field *}
		<input type="text" readonly="readonly" class="x-form-text x-form-field read-only{if $column->Size > 100} long{elseif $column->Size > 50} medium{elseif $column->Size > 10} small{elseif $column->Size > 0} tiny{else} medium{/if}" {if $column->Size}maxlength="{$column->Size}"{/if} id="{$column->NameWithoutPrefix|studlycaps}" name="{$column->NameWithoutPrefix|studlycaps}" value="{ldelim}${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}|escape{rdelim}"  />
{elseif $column->Key == "MUL" && $column->Constraints} {* this is is a key so we need a dop-down *}
		{ldelim}html_options id="{$column->NameWithoutPrefix|studlycaps}" name="{$column->NameWithoutPrefix|studlycaps}" class="x-form-field combo-box" options=${$column->NameWithoutPrefix|studlycaps}Pairs selected=${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}{rdelim}
{elseif $column->Type == "datetime" || $column->Type == "date"}
		{ldelim}include file="_datepicker.tpl" fieldname="{$column->NameWithoutPrefix|studlycaps}" fieldvalue=${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}{rdelim}
{elseif $column->Type == "text" || $column->Type == "mediumtext" || $column->Type == "longtext" || $column->Type == "tinytext"}
		<textarea id="{$column->NameWithoutPrefix|studlycaps}" name="{$column->NameWithoutPrefix|studlycaps}" class="x-form-textarea x-form-field resizable">{ldelim}${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}|escape{rdelim}</textarea>
{else}
		<input type="text" id="{$column->NameWithoutPrefix|studlycaps}" name="{$column->NameWithoutPrefix|studlycaps}" value="{ldelim}${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}|escape{rdelim}" class="x-form-text x-form-field{if $column->Size > 100} long{elseif $column->Size > 50} medium{elseif $column->Size > 10} small{elseif $column->Size > 0} tiny{else} medium{/if}" {if $column->Size}maxlength="{$column->Size}"{/if} />
{/if}
		</div>
		<div id="{$column->NameWithoutPrefix|studlycaps}_Error" class="validator field_validator"></div>
	</div>

{/foreach}
	<div id="Validator_Error" class="validator form_validator"></div>

	<p>
{if !$table->PrimaryKeyIsAutoIncrement()}
		<input type="hidden" id="force_insert" name="force_insert" value="{ldelim}$force_insert{rdelim}" /> 
{/if}
		<input type="submit" class="button" value=" OK " /> 
		<input type="button" class="button" value="Cancel" onclick="self.location='{ldelim}$URL->Get('{$singular}','ListAll'){rdelim}';" />
	</p>

</fieldset>

{ldelim}* hide the child objects for new records *{rdelim}
{ldelim}if (${$singular|lower}->{$table->GetPrimaryKeyName()|studlycaps}){rdelim}

{ldelim}assign var=params value='{$table->GetPrimaryKeyName()|studlycaps}='|cat:${$singular|lower}->{$table->GetPrimaryKeyName()|studlycaps}{rdelim}
	<p><input type="button" class="button" value="Delete" onclick="if (confirm('Delete this record?')) {ldelim}ldelim{rdelim}self.location='{ldelim}$URL->Get('{$singular}','Delete',$params){rdelim}';{ldelim}rdelim{rdelim} else {ldelim}ldelim{rdelim}return false;{ldelim}rdelim{rdelim}" /></p>

{foreach from=$table->Sets item=set}
	<fieldset>
		<legend>{$set->GetterName|studlycaps}</legend>
		
		{ldelim}datagrid 
			page=${$set->GetterName|studlycaps}DataPage
			edit_url="index.php?action={$set->SetTableName|studlycaps}.Edit" 
			delete_url="index.php?action={$set->SetTableName|studlycaps}.Delete"
			primary_key="{$set->SetPrimaryKeyNoPrefix|studlycaps}"
		{rdelim}

		<p><input type="button" value="New {$set->SetTableName|studlycaps}" onclick="self.location='index.php?action={$set->SetTableName|studlycaps}.Create&amp;{$set->SetKeyColumnNoPrefix|studlycaps}={ldelim}${$singular|lower}->{$table->GetPrimaryKeyName()|studlycaps}{rdelim}';" /></p>

	</fieldset>

{/foreach}

{ldelim}/if{rdelim}

</form>

<script type="text/javascript">
	Ext.onReady(function (){ldelim}ldelim{rdelim}standardForm('{$singular|studlycaps}Form');{ldelim}rdelim{rdelim});
</script>

{ldelim}include file="_footer.tpl"{rdelim}