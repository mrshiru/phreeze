<?php

require_once("BaseVO.php");

class {$singular}VO extends BaseVO
{ldelim}

{foreach from=$table->Columns item=column}{if $column->NameWithoutPrefix == "id" || $column->NameWithoutPrefix == "created_date" || $column->NameWithoutPrefix == "created_by" || $column->NameWithoutPrefix == "modified_date" || $column->NameWithoutPrefix == "modified_by" || $column->NameWithoutPrefix == "sync_id" || $column->NameWithoutPrefix == "sync_date" || $column->NameWithoutPrefix == "sync_checksum" || $column->NameWithoutPrefix == "is_deleted"}
{else}	public ${$column->NameWithoutPrefix|camelCase};
{/if}
{/foreach}
	
	protected function GetPhreezableObject($phreezer)
	{ldelim}
		require_once("Model/{$singular}.php");
		return new {$singular}($phreezer);
	{rdelim}
	
	function GetPhreezableClassName()
	{ldelim}
		return "{$singular}";
	{rdelim}

	function GetPhreezableCriteria()
	{ldelim}
		require_once("Model/DAO/{$singular}Criteria.php");
		return new {$singular}Criteria();
	{rdelim}
{rdelim}

?>