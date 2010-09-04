<?php 
/** @package verysimple::DB::DataDriver */

require_once("IDataDriver.php");

/**
 * An implementation of IDataDriver that communicates with
 * a SQLite database file.  This is one of the native drivers
 * supported by Phreeze
 *
 * @package    verysimple::DB::DataDriver
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2010 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class DataDriverSQLite implements IDataDriver
{	
	/**
	 * @inheritdocs
	 */
	function GetServerType()
	{
		return "SQLite";
	}
	
	function Ping($connection)
	{
		 throw new Exception("Not Implemented");
	}
	
	/**
	 * @inheritdocs
	 */
	function Open($connectionstring,$database,$username,$password) 
	{
		throw new Exception("Not Implemented");
	}
	
	/**
	 * @inheritdocs
	 */
	function Close($connection) 
	{
		throw new Exception("Not Implemented");
	}
	
	/**
	 * @inheritdocs
	 */
	function Query($connection,$sql) 
	{
		throw new Exception("Not Implemented");
	}

	/**
	 * @inheritdocs
	 */
	function Execute($connection,$sql) 
	{
		throw new Exception("Not Implemented");
	}
	
	/**
	 * @inheritdocs
	 */
	function Fetch($connection,$rs) 
	{
		throw new Exception("Not Implemented");
	}

	/**
	 * @inheritdocs
	 */
	function GetLastInsertId($connection) 
	{
		throw new Exception("Not Implemented");
	}

	/**
	 * @inheritdocs
	 */
	function GetLastError($connection)
	{
		throw new Exception("Not Implemented");
	}
	
	/**
	 * @inheritdocs
	 */
	function Release($connection,$rs) 
	{
		throw new Exception("Not Implemented");
	}
	
	/**
	 * @inheritdocs
	 */
	function Escape($val) 
	{
		throw new Exception("Not Implemented");
 	}
	
	/**
	 * @inheritdocs
	 */
 	function GetTableNames($connection, $dbname, $ommitEmptyTables = false) 
	{
		throw new Exception("Not Implemented");
 	}
	
	/**
	 * @inheritdocs
	 */
 	function Optimize($connection,$table) 
	{
		throw new Exception("Not Implemented");
	}
	
}

?>