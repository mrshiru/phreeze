<?php
/** @package {$connection->DBName|studlycaps}::Controller */
 
/** import supporting libraries */
require_once("verysimple/Phreeze/Controller.php");

/**
 * DefaultController is the entry point to the application
 *
 * @package {$connection->DBName|studlycaps}::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class DefaultController extends Controller
{ldelim}
	function ListAll()
	{ldelim}
		$this->Smarty->display("ViewListAll.tpl");
	{rdelim}

	protected function Init()
	{ldelim}
		// we have to implement this method even though the model isn't used
		$this->ModelName = "NULL";
	{rdelim}

	protected function LoadFromForm($pk = null)
	{ldelim}
		return null;
	{rdelim}

{rdelim}
?>