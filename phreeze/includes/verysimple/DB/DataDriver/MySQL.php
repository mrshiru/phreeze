<?php 
/** @package verysimple::DB::DataDriver */

require_once("IDataDriver.php");

/**
 * An implementation of IDataDriver that communicates with
 * a MySQL server.  This is one of the native drivers
 * supported by Phreeze
 *
 * @package    verysimple::DB::DataDriver
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2010 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class DataDriverMySQL implements IDataDriver
{	
	/**
	 * @inheritdocs
	 */
	function GetServerType()
	{
		return "MySQL";
	}
	
	function Ping($connection)
	{
		 return mysql_ping($connection);
	}
	
	/**
	 * @inheritdocs
	 */
	function Open($connectionstring,$database,$username,$password) 
	{
		if ( !$connection = @mysql_connect($connectionstring, $username, $password) )
		{
			throw new Exception("Error connecting to database: " . mysql_error());
		}

		if (!@mysql_select_db($database, $connection))
		{
			throw new Exception("Unable to select database " . $database);
		}
		
		return $connection;
	}
	
	/**
	 * @inheritdocs
	 */
	function Close($connection) 
	{
		@mysql_close($connection); // ignore warnings
	}
	
	/**
	 * @inheritdocs
	 */
	function Query($connection,$sql) 
	{
		if ( !$rs = @mysql_query($sql, $connection) )
		{
			throw new Exception(mysql_error());
		}
		
		return $rs;
	}

	/**
	 * @inheritdocs
	 */
	function Execute($connection,$sql) 
	{
		if ( !$result = @mysql_query($sql, $connection) )
		{
			throw new Exception(mysql_error());
		}
		
		return mysql_affected_rows($connection);
	}
	
	/**
	 * @inheritdocs
	 */
	function Fetch($connection,$rs) 
	{
		return mysql_fetch_assoc($rs);
	}

	/**
	 * @inheritdocs
	 */
	function GetLastInsertId($connection) 
	{
		return (mysql_insert_id($connection));
	}

	/**
	 * @inheritdocs
	 */
	function GetLastError($connection)
	{
		return mysql_error($connection);
	}
	
	/**
	 * @inheritdocs
	 */
	function Release($connection,$rs) 
	{
		mysql_free_result($rs);	
	}
	
	/**
	 * @inheritdocs
	 */
	function Escape($val) 
	{
		return mysql_real_escape_string($val);
 	}
	
	/**
	 * @inheritdocs
	 */
 	function GetTableNames($connection, $dbname, $ommitEmptyTables = false) 
	{
		$sql = "SHOW TABLE STATUS FROM `" . $this->Escape($dbname) . "`";
		$rs = $this->Query($connection,$sql);
		
		$tables = array();
		
		while ( $row = $this->Fetch($connection,$rs) )
		{
			if ( $ommitEmptyTables == false || $rs['Data_free'] > 0 )
			{
				$tables[] = $row['Name'];
			}
		}
		
		return $tables;
 	}
	
	/**
	 * @inheritdocs
	 */
 	function Optimize($connection,$table) 
	{
		$result = "";
		$rs = $this->Query($connection,"optimize table `". $this->Escape($table)."`");

		while ( $row = $this->Fetch($connection,$rs) )
		{
			$tbl = $row['Table'];
			if (!isset($results[$tbl])) $results[$tbl] = "";
			$result .= trim($results[$tbl] . " " . $row['Msg_type'] . "=\"" . $row['Msg_text'] . "\"");	
		}
		
		return $result;
	}
	
}

?>