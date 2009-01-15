<?xml version="1.0" encoding="UTF-8"?>
<Content>

{foreach from=$tableInfos item=tableInfo}
	<!--  TABLENAME = {$tableInfo.table->Name} -->
    <VO name="{$tableInfo.singular}" table="{$tableInfo.table->Name}" prefix="{$tableInfo.table->ColumnPrefix}" assignment="true">
	
	{assign var=columns value=$tableInfo.table->Columns}
	{foreach from=$columns item=column}
		{if $column->NameWithoutPrefix == "id" || $column->NameWithoutPrefix == "created_date" || $column->NameWithoutPrefix == "created_by" || $column->NameWithoutPrefix == "modified_date" || $column->NameWithoutPrefix == "modified_by" || $column->NameWithoutPrefix == "sync_id" || $column->NameWithoutPrefix == "sync_date" || $column->NameWithoutPrefix == "sync_checksum" || $column->NameWithoutPrefix == "is_deleted"}
		{else}
			<Data name="{$column->NameWithoutPrefix|camelCase}" type="{$column->GetFlexType()}" required="true" column="{$column->NameWithoutPrefix}"/>
		{/if}
	{/foreach}
	
	{foreach from=$tableInfo.table->Sets item=set}
        <Relation name="{$set->GetterName|camelCase}" object="{$set->SetTableName|studlyCaps}" type="many" fetching="lazy" column="{$set->KeyColumnNoPrefix}" foreignTable="{$set->SetTableName}" foreignColum="{$set->SetKeyColumn}"/>
	{/foreach}
	
	{foreach from=$tableInfo.table->Constraints item=constraint}
        <Relation name="{$constraint->GetterName|camelCase}" object="{$constraint->ReferenceTableName|studlyCaps}" type="one" fetching="lazy" column="{$constraint->KeyColumnNoPrefix}" foreignTable="{$constraint->ReferenceTableName}" foreignColum="{$constraint->ReferenceKeyColumn}"/>
	{/foreach}

    </VO>
{/foreach}

</Content>