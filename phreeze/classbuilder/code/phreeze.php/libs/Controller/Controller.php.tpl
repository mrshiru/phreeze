<?php
/** @package    {$connection->DBName|studlycaps}::Controller */

/** import supporting libraries */
require_once("{$appname}BaseController.php");
require_once("Model/{$singular}.php");

/**
 * {$singular}Controller is the controller class for the {$singular} object.  The
 * controller is responsible for processing input from the user, reading/updating
 * the model as necessary and displaying the appropriate view.
 *
 * @package {$connection->DBName|studlycaps}::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class {$singular}Controller extends {$appname}BaseController
{ldelim}

	/**
	 * Override here for any controller-specific functionality
	 *
	 * @inheritdocs
	 */
	protected function Init()
	{ldelim}
		parent::Init();

		$this->ModelName = "{$singular}";

		// TODO: add controller-wide bootstrap code
	{rdelim}

	/**
	 * List by default displays View{$singular}ListAll.
	 * TODO: it is suggested to override this function
	 *
	 * @inheritdocs
	 */
	function ListAll()
	{ldelim}
		parent::ListAll();
	{rdelim}

	/**
	 * ListPage renders a DataPage as XML.  This is used as a data service
	 * for the AJAX grid used by ListAll
	 */
	public function ListPage()
	{ldelim}
		// these parameters are supplied by extjs grid pagingtoolbar
		$s = RequestUtil::Get('start',0);  // start (zero based)
		$ps = RequestUtil::Get('limit',20); // page size
		$sc = RequestUtil::Get('sort'); // name of column for sorting
		$sd = RequestUtil::Get('dir','ASC'); // sort direction
		$cp = $s/$ps + 1; // current page

		require_once("Model/{$singular}Criteria.php");
		$criteria = new {$singular}Criteria();

		// if a sort was specified, add it to the criteria
		if ($sc)
		{ldelim}
			$criteria->SetOrder($sc, ($sd == "DESC") );
		{rdelim}

		$datapage = $this->Phreezer->Query("{$singular}",$criteria)->GetDataPage($cp,$ps);

		$this->RenderXML($datapage);
	{rdelim}

	/**
	 * Render View{$singular}Display to display a read-only view of the specified record.
	 * This method expects form input to contain a value for {$table->GetPrimaryKeyName()|studlycaps}
	 */
	public function Display()
	{ldelim}
		$this->_AssignModel(RequestUtil::Get("{$table->GetPrimaryKeyName()|studlycaps}"));
		$this->Render("{$singular}Display");
	{rdelim}

	/**
	 * Render View{$singular}Edit to display an editable form for the specified record.
	 * A query value is expected for {$table->GetPrimaryKeyName()|studlycaps}.
	 */
	function Edit()
	{ldelim}
		$this->_AssignModel(RequestUtil::Get("{$table->GetPrimaryKeyName()|studlycaps}"));
		$this->Render("{$singular}Edit");
	{rdelim}

	/**
	 * Render View{$singular}Edit to display an editable form for a new record.
	 */
	function Create()
	{ldelim}
		$this->_AssignModel();
		$this->Render("{$singular}Edit");
	{rdelim}

	/**
	 * Save the record that was submitted by the user via an Edit or Create form.
	 * If a value is provided for {$table->GetPrimaryKeyName()|studlycaps} then it
	 * expected that this is an edit, otherwise it is expect that this is an insert.
	 */
	function Save()
	{ldelim}
		$pk = RequestUtil::Get("{$table->GetPrimaryKeyName()|studlycaps}");

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
				$force_insert = RequestUtil::Get("force_insert");
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

	/**
	 * Delete the {$singular} object from the datastore.  This method
	 * expects a form parameter called {$table->GetPrimaryKeyName()|studlycaps}
	 */
	function Delete()
	{ldelim}
		$pk = RequestUtil::Get("{$table->GetPrimaryKeyName()|studlycaps}");
		${$singular|lower} = $this->Phreezer->Get("{$singular}",$pk);
		${$singular|lower}->Delete();
		$this->Redirect("{$singular}.ListAll","{$singular} was deleted");
	{rdelim}

	/**
	 * Retrieves a {$singular} object from the datastore and assigns it to the view.
	 * Optionally, all child objects are assigned to the view as well
	 *
	 * @param mixed primary key of the object
	 * @param bool set to true and _AssignChildren will be called
	 */
	private function _AssignModel($pk = null, $assign_children = true)
	{ldelim}
		${$singular|lower} = $pk ? $this->Phreezer->Get("{$singular}",$pk) : New {$singular}($this->Phreezer);
		$this->Assign("{$singular|lower}", ${$singular|lower});

		if ($assign_children)
		{ldelim}
			$this->_AssignChildren(${$singular|lower});
		{rdelim}
	{rdelim}


	/**
	 * LoadFromForm creates a new instance of a {$singular} object and populates it with
	 * form data submitted by the user.  The base controller will utilize this function
	 * when validating input via AJAX.
	 *
	 * @inheritdocs
	 * @return {$singular}
	 */
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
		// ${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = RequestUtil::Get("{$column->NameWithoutPrefix|studlycaps}"); // this is an auto-increment
{else}
{if $column->Type == "date" or $column->Type == "datetime"}
{if $column->NameWithoutPrefix|studlycaps == "Created" or $column->NameWithoutPrefix|studlycaps == "CreatedDate" or $column->NameWithoutPrefix|studlycaps == "CreateDate"}
		${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = (${$singular|lower}->{$column->NameWithoutPrefix|studlycaps}) ? ${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} : RequestUtil::GetAsDateTime("");
{else}
		${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = RequestUtil::GetAsDateTime("{$column->NameWithoutPrefix|studlycaps}");
{/if}
{else}
		${$singular|lower}->{$column->NameWithoutPrefix|studlycaps} = RequestUtil::Get("{$column->NameWithoutPrefix|studlycaps}");
{/if}
{/if}
{/foreach}

		return ${$singular|lower};
	{rdelim}

	/**
	 * _AssignChildren assigns all necessary dependency records to the view in order to correctly display
	 * an edit form.  For example, any drop-down lists that need to be populated as well
	 * as child objects are retrieved from the database and assigned to the view.
	 *
	 * TODO: remove any uneeded assignments here
	 * TODO: Change the label arrays "label" property to something friendly for displaying to the user
	 * TODO: a sanity limit of 25 is used on child records
	 *
	 */
	private function _AssignChildren(${$singular|lower})
	{ldelim}
		// label arrays are used for drop-downs:

{foreach from=$table->Constraints item=constraint}
		${$constraint->KeyColumnNoPrefix|lower}s = $this->Phreezer->Query("{$constraint->ReferenceTableName|studlycaps}")->GetLabelArray('{$constraint->ReferenceTable->GetPrimaryKeyName()|studlycaps}','{$constraint->ReferenceTable->GetDescriptorName()|studlycaps}');
		$this->Assign("{$constraint->KeyColumnNoPrefix|studlycaps}Pairs",${$constraint->KeyColumnNoPrefix|lower}s);

{/foreach}
		// get child records and assign as DataPage for grid display
		// if any of these throw an error, check that your foreign key name is not the same as the table name

{foreach from=$table->Sets item=set}
		${$set->GetterName|studlycaps} = ${$singular|lower}->Get{$set->GetterName|studlycaps}()->GetDataPage(1,25);
		$this->Assign("{$set->GetterName|studlycaps}DataPage",${$set->GetterName|studlycaps});

{/foreach}
	{rdelim}
{rdelim}

?>