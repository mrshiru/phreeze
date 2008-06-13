<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("IObservable.php");
require_once("ConnectionSetting.php");
require_once("DataPage.php");
require_once("DataSet.php");
require_once("QueryBuilder.php");

/**
 * DataAdapter abstracts and provides access to the data store
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2005 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class DataAdapter implements IObservable
{
    
    private $_observers = Array();
    private $_csetting;
	private $_dbconn;
	private $_dbopen;
    
    
    /**
    * Contructor initializes the object
    *
    * @access     public
    * @param ConnectionSetting $csetting
    * @param Observable $listener
    */
    function DataAdapter($csetting, $listener = null)
    {
		$this->AttachObserver($listener);
		$this->_csetting =& $csetting;
		$this->Observe("DataAdapter Instantiated", OBSERVE_DEBUG);
	}
	
	
	/**
     * Destructor closes the db connection.
     *
     * @access     public
     */    
	function __destruct()
	{
		$this->Observe("DataAdapter Destructor Firing...",OBSERVE_DEBUG);
		$this->Close();
	}
	
    /**
	 * Returns name of the DB currently in use
	 *
	 * @access public
	 * @return string
	 */	
	function GetDBName()
	{
		return $this->_csetting->DBName;
	}
	
    /**
	 * Opens a connection to the MySQL Server and selects the specified database
	 *
	 * @access public
	 */	
	function Open()
	{
		$this->Observe("Opening Connection...",OBSERVE_DEBUG);
		
		if ($this->_dbopen)
		{
			$this->Observe("Connection Already Open" . mysql_error(),OBSERVE_WARN);
		}
		else
		{
			if ( !$this->_dbconn = @mysql_connect($this->_csetting->ConnectionString, $this->_csetting->Username, $this->_csetting->Password) )
			{
				$this->Observe("Error connecting to database: " . mysql_error(),OBSERVE_FATAL);
				throw new Exception("Error connecting to database: " . mysql_error());
			}

			$this->_dbopen = true;
			
			if (!@mysql_select_db($this->_csetting->DBName, $this->_dbconn))
			{
				$this->Observe("Unable to select database " . $this->_csetting->DBName,OBSERVE_FATAL);
				throw new Exception("Unable to select database " . $this->_csetting->DBName);
			}
			
			$this->Observe("Connection Open",OBSERVE_DEBUG);
		}
	}
	
	/**
	 * Closing the connection to the MySQL Server
	 *
	 * @access public
	 */	
	function Close()
	{
		$this->Observe("Closing Connection...",OBSERVE_DEBUG);
		
		if ($this->_dbopen)
		{
			@mysql_close($this->_dbconn); // ignore warnings
			$this->_dbopen = false;
			$this->Observe("Connection Closed",OBSERVE_DEBUG);
		}
		else
		{
			$this->Observe("Connection Not Open",OBSERVE_DEBUG);
		}
	}
    
    /**
	 * Checks that the connection is open and if not, crashes
	 *
	 * @access public
	 * @param bool $auto Automatically try to connect if connection isn't already open
	 */	
	private function RequireConnection($auto = false)
	{
		if ($this->_dbopen)
		{
			// mysql_ping($this->_dbconn);
		}
		else
		{
			if ($auto)
			{
				$this->Open();
			}
			else
			{
				$this->Observe("DB is not connected.  Please call DBConnection->Open() first.",OBSERVE_FATAL);
				throw new Exception("DB is not connected.  Please call DBConnection->Open() first.");
			}
		}
	}
	
	/**
	 * Executes a SQL select statement and returns a MySQL resultset
	 *
	 * @access public
	 * @param string $sql
	 * @return mysql_query
	 */	
	function Select($sql)
	{
		$this->RequireConnection(true);
		$this->Observe($sql, OBSERVE_QUERY);
		
		if ( !$rs = @mysql_query($sql, $this->_dbconn) )
		{
			$this->Observe("Error executing SQL: " . mysql_error(),OBSERVE_FATAL);
			throw new Exception('Error executing SQL: ' . mysql_error());
		}
		
		return $rs;
	}
	
	/**
	 * Executes a SQL query that does not return a resultset
	 *
	 * @access public
	 * @param string $sql
	 * @return int number of records affected
	 */	
	function Execute($sql)
	{
		$this->RequireConnection(true);
		$this->Observe($sql, OBSERVE_QUERY);

		if ( !$result = @mysql_query($sql, $this->_dbconn) )
		{
			$this->Observe("Error executing SQL: " . mysql_error(),OBSERVE_FATAL);
			throw new Exception('Error executing SQL: ' . mysql_error());
		}
		
		return mysql_affected_rows($this->_dbconn);
	}
	
	/**
	 * Returns last auto-inserted Id
	 *
	 * @access public
	 * @return int
	 */	
	function GetLastInsertId()
	{
		$this->RequireConnection();
		$this->Observe("mysql_insert_id", OBSERVE_QUERY);
		return (mysql_insert_id($this->_dbconn));
	}
	
	/**
	 * Moves the database curser forward and returns the current row as an associative array
	 *
	 * @access public
	 * @param mysql_query $rs
	 * @return Array
	 */	
	function Fetch($rs)
	{
		$this->RequireConnection();

		$this->Observe("Fetching next result as array",OBSERVE_DEBUG);
		return mysql_fetch_assoc($rs);
	}
	
	/**
	 * Releases the resources for the given resultset
	 *
	 * @access public
	 * @param mysql_query $rs
	 */	
	function Release($rs)
	{
		$this->RequireConnection();

		$this->Observe("Releasing result resources",OBSERVE_DEBUG);
		mysql_free_result($rs);
	}
	
	/**
	 * Removes any illegal chars from a value to prepare it for use in SQL
	 *
	 * @access public
	 * @param string $val
	 * @return string
	 */	
    public static function Escape($val)
    {
		// if magic quotes are enabled, then we need to stip the slashes that php added
		if (get_magic_quotes_runtime() || get_magic_quotes_gpc()) $val = stripslashes($val);

		return mysql_real_escape_string($val);
	}
	
	
    /**
    * Registers/attaches an IObserver to this object
    *
    * @access public
	* @param IObserver $observer
    */
	public function AttachObserver($listener)
	{
		if ($listener) $this->_observers[] =& $listener;
	}
	
    /**
    * Fires the Observe event on all registered observers
    *
    * @access public
    * @param variant $obj the $obj or message that you want to log/listen to, etc.
    * @param int $ltype the type/level
    */
	public function Observe($obj, $ltype = OBSERVE_INFO)
	{
		foreach ($this->_observers as $observer) @$observer->Observe($obj, $ltype);
	}
}
?>