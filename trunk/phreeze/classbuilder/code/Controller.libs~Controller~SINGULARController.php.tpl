<?php
/** @package    {$connection->DBName|studlycaps}::Controller */
 
/** import supporting libraries */
require_once("verysimple/Phreeze/Controller.php");
require_once("Model/{$singular}.php");

/**
 * {$singular}Controller is the controller class for the {$singular} object
 *
 * @package {$connection->DBName|studlycaps}::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class {$singular}Controller extends Controller
{ldelim}

	protected function Init()
	{ldelim}
		$this->ModelName = "{$singular}";
	{rdelim}
	
	// base functions suggested to override
	// function ListAll() {ldelim}{rdelim}

	function ListPage()
	{ldelim}
		// these parameters are supplied by extjs grid pagingtoolbar
		$s = Request::Get('start',0);  // start (zero based)
		$ps = Request::Get('limit',20); // page size
		$sc = Request::Get('sort'); // name of column for sorting
		$sd = Request::Get('dir','ASC'); // sort direction
		$cp = $s/$ps + 1; // current page
		
		require_once("Model/DAO/{$singular}Criteria.php");
		$criteria = new {$singular}Criteria();
		
		// if a sort was specified, add it to the criteria
		if ($sc)
		{ldelim}
			$criteria->SetOrder($sc, ($sd == "DESC") );
		{rdelim}
		
		$datapage = $this->Phreezer->Query("{$singular}",$criteria)->GetDataPage($cp,$ps);
		
		$this->RenderXML($datapage);
	{rdelim}
	
	function Display()
	{ldelim}
		$this->_AssignModel(Request::Get("{$table->GetPrimaryKeyName()|studlycaps}"));
		$this->Render("{$singular}Display");
	{rdelim}

	function Edit()
	{ldelim}
		$this->_AssignModel(Request::Get("{$table->GetPrimaryKeyName()|studlycaps}"));
		$this->Render("{$singular}Edit");
	{rdelim}
	
	function Create()
	{ldelim}
		$this->_AssignModel();
		$this->Render("{$singular}Edit");
	{rdelim}
	
	function Save()
	{ldelim}
		$pk = Request::Get("{$table->GetPrimaryKeyName()|studlycaps}");
		
		${$singular|lower} = $this->LoadFromForm($pk);
		
		if (!${$singular|lower}->Validate())
		{ldelim}
			$this->Assign("{$singular|lower}", ${$singular|lower});
			$this->_AssignChildren(${$singular|lower});
			$this->Assign("warning",implode("<br />",${$singular|lower}->GetValidationErrors()));
			$this->Render("{$singular}Edit");
		{rdelim}
		else
		{ldelim}
			try
			{ldelim}
{if !$table->PrimaryKeyIsAutoIncrement()}
				// this table does not have an auto-insert, so we have to specify an insert
				$force_insert = Request::Get("force_insert");
				${$singular|lower}->Save($force_insert);
{else}
				${$singular|lower}->Save();
{/if}
				$this->Redirect("{$singular}.ListAll","{$singular} was saved");
			{rdelim}
			catch(Exception $ex)
			{ldelim}
				$this->Assign("{$singular|lower}", ${$singular|lower});
				$this->_AssignChildren(${$singular|lower});
				$this->Assign("warning",$ex->getMessage());
				$this->Render("{$singular}Edit");
			{rdelim}

		{rdelim}
	{rdelim}

	function Delete()
	{ldelim}
		$pk = Request::Get("{$table->GetPrimaryKeyName()|studlycaps}");
		${$singular|lower} = $this->Phreezer->Get("{$singular}",$pk);
		${$singular|lower}->Delete();
		$this->Redirect("{$singular}.ListAll","{$singular} was deleted");
	{rdelim}

	private function _AssignModel($pk = null, $assign_children = true)
	{ldelim}
		${$singular|lower} = $pk ? $this->Phreezer->Get("{$singular}",$pk) : New {$singular}($this->Phreezer);
		$this->Assign("{$singular|lower}", ${$singular|lower});

		if ($assign_children)
		{ldelim}
			$this->_AssignChildren(${$singular|lower});
		{rdelim}
	{rdelim}

	
	protected function LoadFromForm($pk = null)
	{ldelim}
{if $table->PrimaryKeyIsAutoIncrement()}
		${$singular|lower} = $pk ? $this->Phreezer->Get("{$singular}",$pk) : New {$singular}($this->Phreezer);
{else}
		// the primary key of this table is not an auto-insert
		${$singular|lower} = New {$singular}($this->Phreezer);
{/if}
{foreach from=$table->Columns item=column}
{if $column->Extra == 'auto_increment'}{*if $column->Key == "PRI"*}
		// ${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = Request::Get("{$column->NameWithoutPrefix|studlycaps}"); // this is an auto-increment
{else}
{if $column->Type == "date" or $column->Type == "datetime"}
{if $column->NameWithoutPrefix|studlycaps == "Created" or $column->NameWithoutPrefix|studlycaps == "CreatedDate" or $column->NameWithoutPrefix|studlycaps == "CreateDate"}
		${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = (${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}) ? ${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} : Request::GetAsDateTime("");
{else}
		${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = Request::GetAsDateTime("{$column->NameWithoutPrefix|studlycaps}");
{/if}
{else}
		${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = Request::Get("{$column->NameWithoutPrefix|studlycaps}");
{/if}
{/if}
{/foreach}
		
		return ${$singular|lower};
	{rdelim}
	
	private function _AssignChildren(${$singular|lower})
	{ldelim}
{foreach from=$table->Constraints item=constraint}
		// get possible values for {$constraint->KeyColumnNoPrefix|studlycaps} and assign as a value pair for html_options
		${$constraint->KeyColumnNoPrefix|lower}s = array();
		$collection = $this->Phreezer->Query("{$constraint->ReferenceTableName|studlycaps}");
		while (${$constraint->KeyColumnNoPrefix|lower} = $collection->Next())
		{ldelim}
			${$constraint->KeyColumnNoPrefix|lower}s[${$constraint->KeyColumnNoPrefix|lower}->{$constraint->ReferenceTable->GetPrimaryKeyName()|studlycaps}] = ${$constraint->KeyColumnNoPrefix|lower}->{$constraint->ReferenceTable->GetDescriptorName()|studlycaps}; // TODO: verify this is the right field
		{rdelim}
		$this->Assign("{$constraint->KeyColumnNoPrefix|studlycaps}Pairs",${$constraint->KeyColumnNoPrefix|lower}s);

{/foreach}
{foreach from=$table->Sets item=set}
		// get {$set->GetterName|studlycaps} child records and assign as DataPage for grid display
		// if this code throws an error, check that your foreign key name is not the same as the table name
		${$set->GetterName|studlycaps} = ${$singular|lower}->Get{$set->GetterName|studlycaps}()->GetDataPage(1,9999); // TODO: update if pagination is necessary
		$this->Assign("{$set->GetterName|studlycaps}DataPage",${$set->GetterName|studlycaps});

{/foreach}
	{rdelim}
{rdelim}

?>