<?php
/** @package    {$connection->DBName|studlycaps}::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Reporter.php");
require_once("verysimple/Phreeze/DataAdapter.php");

/**
 * This is an example Reporter based on the {$singular} object.  The reporter object
 * allows you to run arbitrary queries that return data which may or may not fith within
 * the data access API.  This can include aggregate data or subsets of data.
 *
 * @package {$connection->DBName|studlycaps}::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class {$singular}Reporter extends Reporter
{ldelim}
	
	public $RecordCount;
	
	/*
	* GetCustomQuery returns a fully formed SQL statement for which the result fields
	* will match with the properties of this reporter object.
	*
	* @param Criteria $criteria
	* @return string SQL statement
	*/
	static function GetCustomQuery($criteria)
	{ldelim}
		$sql = "select sum(1) as RecordCount from `{$table->Name}`";
		return $sql;
	{rdelim}
{rdelim}

?>