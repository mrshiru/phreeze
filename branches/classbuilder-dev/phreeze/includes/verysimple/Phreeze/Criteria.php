<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("DataAdapter.php");

/**
 * Criteria is a base object that is passed into Phreeze->Query for retreiving
 * records based on specific criteria
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.1
 */
class Criteria
{
	protected $_join;
	protected $_where;
	protected $_order;
	protected $_is_prepared;
	
	private $_fieldmaps;
	
	public $PrimaryKeyField;
	public $PrimaryKeyValue;
	
	public function Criteria($where = "", $order = "")
	{
		$this->_where = $where;
		$this->_order = $order;
	}
	
	/** Prepare is called just prior to execution and will fire OnPrepare after it completes
	 * If this is a base Criteria class, then we can only do a lookup by PrimaryKeyField or
	 * else raw SQL must be provided during construction.  _Equals, _BeginsWith can only be
	 * used by inherited Criteria classes because we don't know what table this is associated
	 * with, so we can't translate property names to column names.
	 *
	 */
	private final function Prepare()
	{
		if (!$this->_is_prepared)
		{
			
			if (get_class($this) == "Criteria")
			{
				if ($this->PrimaryKeyField)
				{
					// PrimaryKeyField property was specified. this might be coming from $phreezer->Get
					$this->_where = " " . $this->PrimaryKeyField ." = '". DataAdapter::Escape($this->PrimaryKeyValue) . "'";
				}
				// else {raw SQL was likely provided in the constructor. this might be coming from $phreezer->GetOneToMany}
			}
			else
			{
				// loop through all of the properties and attempt to 
				// build a query based on any values that have been set
				$delim = "";
				$this->_where = "";

				$props = get_object_vars($this);
				foreach ($props as $prop => $val)
				{
					// TODO: tighten this up a bit to reduce redundant code
					if (substr($prop,-7) == "_Equals" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_Equals","",$prop));
						$this->_where .= $delim . " " . $dbfield ." = '". DataAdapter::Escape($val) . "'";
						$delim = " and";
					}
					elseif (substr($prop,-10) == "_NotEquals" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_NotEquals","",$prop));
						$this->_where .= $delim . " " . $dbfield ." != '". DataAdapter::Escape($val) . "'";
						$delim = " and";
					}
					elseif (substr($prop,-7) == "_IsLike" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_IsLike","",$prop));
						$this->_where .= $delim . " " . $dbfield ." like '%". DataAdapter::Escape($val) . "%'";
						$delim = " and";
					}
					elseif (substr($prop,-11) == "_BeginsWith" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_BeginsWith","",$prop));
						$this->_where .= $delim . " " . $dbfield ." like '". DataAdapter::Escape($val) . "%'";
						$delim = " and";
					}
					elseif (substr($prop,-9) == "_EndsWith" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_EndsWith","",$prop));
						$this->_where .= $delim . " " . $dbfield ." like '%". DataAdapter::Escape($val) . "'";
						$delim = " and";
					}
					elseif (substr($prop,-12) == "_GreaterThan" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_GreaterThan","",$prop));
						$this->_where .= $delim . " " . $dbfield ." > '". DataAdapter::Escape($val) . "'";
						$delim = " and";
					}
					elseif (substr($prop,-9) == "_LessThan" && strlen($this->$prop))
					{
						$dbfield = $this->GetFieldFromProp(str_replace("_LessThan","",$prop));
						$this->_where .= $delim . " " . $dbfield ." < '". DataAdapter::Escape($val) . "'";
						$delim = " and";
					}
				}
			}

			// prepend the sql so the statement will work correctly
			if ($this->_where)
			{
				$this->_where = " where " . $this->_where;
			}

			if ($this->_order)
			{
				$this->_order = " order by " . $this->_order;
			}

			$this->OnPrepare();
			$this->_is_prepared = true;
		}
	}
	
	public function OnPrepare() {}

	public final function GetWhere()
	{
		$this->Prepare();
		return $this->_where;
	}
	

	public final function GetJoin()
	{
		$this->Prepare();
		return $this->_join;
	}
	
	public final function GetOrder()
	{
		$this->Prepare();
		return $this->_order;
	}

	/**
	 * Adds an object property to the order by clause.  If any sorting needs to be done
	 * on foreign tables, then for the moment, you need to override this method and
	 * handle it manually.  You can call this method repeatedly to add more than
	 * one property for sorting.
	 *
	 * @param string $property the name of the object property
	 * @param bool $desc (optional) set to true to sort in descending order (default false)
	 */
	public function SetOrder($property,$desc = false)
	{
		if (!$property)
		{
			// no property was specified.
			return;
		}
		
		$delim = ($this->_order) ? "," : "";
		
		if($property == '?')
		{
			$this->_order = "RAND()" . $delim . $this->_order;
		}
		else
		{
			$colname = $this->GetFieldFromProp($property);
			$this->_order .= $delim . $colname . ($desc ? " desc" : "");	
		}

	}
	
	protected function GetFieldFromProp($propname)
	{

		if (get_class($this) == "Criteria")
		{
			throw new Exception("Phreeze is unable to determine field mapping.  The base Criteria class should only be used to query by primary key without sorting");
		}

		if (!$this->_fieldmaps)
		{
			// we have to open the file to get the fieldmaps
			$mapname = str_replace("Criteria","Map",get_class($this));
			
			if (!file_exists("Model/DAO/".$mapname.".php") && !file_exists("libs/Model/DAO/".$mapname.".php"))
			{
				throw new Exception("Model/DAO/".$mapname.".php" . " could not be found.  If your model file isn't located here, then you must implement GetFieldFromProp manually");
			}
			
			include_once("Model/DAO/".$mapname.".php");
			eval("\$this->_fieldmaps = $mapname::GetFieldMaps();");
		}
		
		// make sure this property is defined
		if (!isset($this->_fieldmaps[$propname]))
		{
			throw new Exception("Unknown Property '$propname' specified.");
		}
		//print_r($this->_fieldmaps);
		$fm = $this->_fieldmaps[$propname];
		
		return $fm->FieldType == FM_CALCULATION ? "(" . $fm->ColumnName . ")" : "`" . $fm->TableName . "`.`" . $fm->ColumnName . "`";

	}
}

?>