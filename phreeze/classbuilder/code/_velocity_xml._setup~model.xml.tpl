<?xml version="1.0" encoding="UTF-8"?>
<Content>

{foreach from=$tableInfos item=tableInfo}
<!--  TABLENAME = {$tableInfo.table->Name} -->

{if $tableInfo.table->Name != 'system' &&  $tableInfo.table->Name != 'search_index'}	

	{if $tableInfo.table->Comment|strstr:"assignment" }
		{assign var='is_assignment' value='true'}
	{else}
		{assign var='is_assignment' value='false'}
	{/if}
	
    <VO name="{$tableInfo.singular}" table="{$tableInfo.table->Name}" comments="{$tableInfo.table->Comment}" prefix="{$tableInfo.table->ColumnPrefix}" assignment="{$is_assignment}">
	
	{assign var=columns value=$tableInfo.table->Columns}
	{foreach from=$columns item=column}
		{if $column->NameWithoutPrefix == "assign_label" || $column->NameWithoutPrefix == "assign_priority" || $column->NameWithoutPrefix == "id" || $column->NameWithoutPrefix == "created_date" || $column->NameWithoutPrefix == "created_by" || $column->NameWithoutPrefix == "modified_date" || $column->NameWithoutPrefix == "modified_by" || $column->NameWithoutPrefix == "sync_id" || $column->NameWithoutPrefix == "sync_date" || $column->NameWithoutPrefix == "sync_checksum" || $column->NameWithoutPrefix == "is_deleted"}
		{else}
			<Data name="{$column->NameWithoutPrefix|camelCase}" type="{$column->GetFlexType()}" required="{if $column->Null == 'NO'}true{else}false{/if}" 
			{if $column->Default != ''}defaultValue="{if $column->GetFlexType() == 'Boolean'}{if $column->Default}true{else}false{/if}{else}{if $column->GetFlexType() == 'String'}'{$column->Default|escape|replace:"\"":"\\\""|replace:"'":"\\\\'"}'{else}{$column->Default|escape|replace:"\"":"\\\""|replace:"'":"\\\\'"}{/if}{/if}"{/if}
			maxSize="{if $column->Size}{$column->Size}{else}0{/if}" column="{$column->NameWithoutPrefix}"/>
		{/if}
	{/foreach}
	
	{foreach from=$tableInfo.table->Sets item=set}
        <Relation name="{$set->GetterName|camelCase}" object="{$set->SetTableName|studlyCaps}" type="many" fetching="lazy" column="{$set->KeyColumnNoPrefix}" foreignTable="{$set->SetTableName}" foreignColumn="{$set->SetKeyColumn}" {if $set->SetKeyComment|strpos:'cascadeDelete' > -1}cascadeDelete="true"{/if}/>
	{/foreach}
	
	{foreach from=$tableInfo.table->Constraints item=constraint}
        <Relation name="{$constraint->GetterName|camelCase}" object="{$constraint->ReferenceTableName|studlyCaps}" type="one" fetching="lazy" column="{$constraint->KeyColumnNoPrefix}" foreignTable="{$constraint->ReferenceTableName}" foreignColum="{$constraint->ReferenceKeyColumn}"/>
	{/foreach}

    </VO>
{/if}
{/foreach}

</Content>