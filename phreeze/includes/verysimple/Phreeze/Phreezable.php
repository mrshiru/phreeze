<?php
/** @package    verysimple::Phreeze */

/**
 * Phreezable Class
 *
 * Abstract base class for object that are persistable by Phreeze
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2005 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.3
 */
abstract class Phreezable
{
    private $_cache;
    protected $_phreezer;
	protected $_val_errors = Array();
	protected $_base_validation_complete = false;
	
    public $IsLoaded;
	public $IsPartiallyLoaded;
	public $NoCache = false;
	
	/** prevent serialization of _phreezer & private properties */
	function __sleep()
	{
		$props = array();
		$ro = new ReflectionObject($this);
		
		foreach ($ro->getProperties() as $rp)
		{
			if ($rp->name != "_phreezer") $props[] = $rp->name;
		}
		return $props;
	}
	
	/** put object back into stable state after deserialization */
	function __wakeup()
	{
		if (!$this->_cache) $this->_cache = array();
	}
	
	
    /**
    * constructor
    *
    * @access     public
    * @param      Phreezer $phreezer
    * @param      Array $row
    */
    final function Phreezable(&$phreezer, $row = null)
    {
		$this->_phreezer = $phreezer;
		$this->_cache = Array();
		
        if ($row)
        {
			$this->Init();
            $this->Load($row);
        }
		else
		{
			$this->LoadDefaults();
			$this->Init();
		}
    }
    
    /**
    * Init is called after contruction.  When loading, Init is called prior to Load().
	* When creating a blank object, Init is called immediately after LoadDefaults()
    *
    * @access     public
    */
    public function Init()
    {
	}
	
    /**
    * LoadDefaults is called during construction if this object is not instantiated with
	* a DB row.  The default values as specified in the fieldmap are loaded
    *
    * @access     public
    */
	public function LoadDefaults()
	{
		$fms = $this->_phreezer->GetFieldMaps(get_class($this));
		
		foreach ($fms as $fm)
		{
			$prop = $fm->PropertyName;
			$this->$prop = $fm->DefaultValue;
		}
	}

	/**
	* LoadFromObject allows this class to be populated from another class, so long as
	* the properties are compatible.  This is useful when using reporters so that you
	* can easily convert them to phreezable objects.  Be sure to check that IsLoaded
	* is true before attempting to save this object.
	*
	* @access     public
	* @param $src the object to populate from, which must contain compatible properties
	*/
	public function LoadFromObject($src)
	{
		$this->IsLoaded = true;
		$src_cls = get_class($src);

		foreach (get_object_vars($this) as $key => $val)
		{
			if ($key != "IsLoaded" && $key != "IsPartiallyLoaded" && substr($key,0,1) != "_")
			{
				if (property_exists($src_cls ,$key))
				{
					$this->$key = $src->$key;
					$this->IsPartiallyLoaded = true;
				}
				else
				{
					$this->IsLoaded = false;
				}
			}
		}
		
	}
	
    /**
    * Validate returns true if the properties all contain valid values.  If not,
	* use GetValidationErrors to see which fields have invalid values.
    *
    * @access     public
    */
	public function Validate()
	{
		// force re-validation
		$this->_val_errors = Array();
		$this->_base_validation_complete = false;
		
		return !$this->HasValidationErrors();
	}

	/**
	 * Add a validation error to the error array
	 * @param string property name
	 * @param string error message
	 */
	protected function AddValidationError($prop,$msg)
	{
		$this->_val_errors[$prop] = $msg;
	}
	
	/**
	 * Returns true if this object has validation errors
	 * @return bool
	 */
	protected function HasValidationErrors()
	{
		$this->_DoBaseValidation();
		return count($this->_val_errors) > 0;
	}
	
	/**
	* Returns the error array - containing an array of fields with invalid values.
	*
	* @access     public
	* @return     array
	*/
	public function GetValidationErrors()
	{
		$this->_DoBaseValidation();
		return $this->_val_errors;
	}
	
    /**
    * populates the _val_errors array w/ phreezer
    *
    * @access     private
    */
	private function _DoBaseValidation()
	{
		if (!$this->_base_validation_complete)
		{
			$fms = $this->_phreezer->GetFieldMaps(get_class($this));
			
			foreach ($fms as $fm)
			{
				$prop = $fm->PropertyName;
				
				if ($fm->FieldSize && (strlen($this->$prop) > $fm->FieldSize))
				{
					$this->AddValidationError($prop,"$prop exceeds the maximum length of " . $fm->FieldSize . "");
				}
				
				if ($this->$prop == "" && ($fm->DefaultValue || $fm->IsAutoInsert) )
				{
					// these fields are auto-populated so we don't need to validate them unless
					// a specific value was provided
				}
				else
				{
					switch ($fm->FieldType)
					{
						case FM_TYPE_INT:
						case FM_TYPE_SMALLINT:
						case FM_TYPE_TINYINT:
						case FM_TYPE_MEDIUMINT:
						case FM_TYPE_BIGINT:
						case FM_TYPE_DECIMAL:
							if (!is_numeric($this->$prop))
							{
								$this->AddValidationError($prop,"$prop is not a valid number");
							}
							break;
						case FM_TYPE_DATE:
						case FM_TYPE_DATETIME:
							if (!strtotime($this->$prop))
							{
								$this->AddValidationError($prop,"$prop is not a valid date/time value");
							}
							break;
						default:
							break;
					}
				}
			}
		}
		
		// print_r($this->_val_errors);
		
		$this->_base_validation_complete = true;
	}
    
    /**
    * This static function can be overridden to populate this object with
    * results of a custom query
    *
    * @access     public
    * @param      Criteria $criteria
    * @return     string or null
    */
    public static function GetCustomQuery($criteria)
    {
		return null;
	}
    
    /**
    * Refresh the object in the event that it has been saved to the session or serialized
    *
    * @access     public
    * @param      Phreezer $phreezer
    * @param      Array $row
    */
    final function Refresh(&$phreezer, $row = null)
    {
		$this->_phreezer = $phreezer;
		
		// also refresh any children in the cache in case they are accessed
		foreach ($this->_cache as $child)
		{
			if ( in_array("Phreezable", class_parents($child)) )
			{
				$child->Refresh($phreezer, $row);
			}
		}
		
        if ($row)
        {
            $this->Load($row);
        }
	}
    
    /**
     * Serialized string representation of this object.  For sorting
     * purposes it is recommended to override this method
     */
    function ToString()
    {
		return serialize($this);
	}
    
    /**
    * Returns the name of the primary key property.  
    * TODO: does not support multiple primary keys.
    *
    * @access     public
    * @return     string
    */
    function GetPrimaryKeyName()
    {
        $fms = $this->_phreezer->GetFieldMaps(get_class($this));
        foreach ($fms as $fm)
        {
            if ($fm->IsPrimaryKey)
            {
				return $fm->PropertyName;
			}
        }
		
		/*
		print "<pre>";
		$this->Data = "";
		$this->_phreezer = null;
		$this->_cache = null;
		print_r($this);
		
		print_r($fms);
		die();
		*/
		
		throw new Exception("No Primary Key found for " . get_class($this));
    }

    /**
    * Returns the value of the primary key property.  
    * TODO: does not support multiple primary keys.
    *
    * @access     public
    * @return     string
    */
    function GetPrimaryKeyValue()
    {
        $prop = $this->GetPrimaryKeyName();
		return $this->$prop;
    }
    
    /**
    * Returns this object as an associative array with properties as keys and
    * values as values
    *
    * @access     public
    * @return     array
    */
    function GetArray()
    {
		$fms = $this->_phreezer->GetFieldMaps(get_class($this));
		$cols = Array();
		
        foreach ($fms as $fm)
        {
			$prop = $fm->PropertyName;
			$cols[$fm->ColumnName] = $this->$prop;
        }
        
        return $cols;
	}
	
	/**
	 * Persist this object to the data store
	 *
	 * @access public
	 * @param bool $force_insert (default = false)
	 * @return int auto_increment or number of records affected
	 */
	function Save($force_insert = false)
	{
		return $this->_phreezer->Save($this,$force_insert);
	}
	
	/**
	 * Delete this object from the data store
	 *
	 * @access public
	 * @return int number of records affected
	 */
	function Delete()
	{
		return $this->_phreezer->Delete($this);
	}
    
    /**
    * Loads the object with data given in the row array.
    *
    * @access     public
    * @param      Array $row
    */
    function Load(&$row)
    {
        
        $fms = $this->_phreezer->GetFieldMaps(get_class($this));
		$this->_phreezer->Observe("Loading " . get_class($this),OBSERVE_DEBUG);

        $this->IsLoaded = true; // assume true until fail occurs
		$this->IsPartiallyLoaded = false; // at least we tried
		
		// in order to prevent collisions on fields, QueryBuilder appends __tablename__rand to the
		// sql statement.  We need to strip that out so we can match it up to the property names 
		$rowlocal = array();
		foreach ($row as $key => $val)
		{
			$info = explode("___",$key);
			
			// we prefer to use tablename.colname if we have it, but if not
			// just use the colname
			$newkey = isset($info[1]) ? ($info[1] . "." . $info[0]) : $info[0];
			if (isset($rowlocal[$newkey]))
			{
				throw new Exception("The column `$newkey` was selected twice in the same query, causing a data collision");
			}
			$rowlocal[$newkey] = $val;

		}

        foreach ($fms as $fm)
        {
            if ( array_key_exists($fm->TableName . "." . $fm->ColumnName, $rowlocal) )
            {
				// first try to locate the field by tablename.colname
				$prop = $fm->PropertyName;
				$this->$prop = $rowlocal[$fm->TableName . "." . $fm->ColumnName];
            }
			elseif ( array_key_exists($fm->ColumnName, $rowlocal) )
			{
				// if we can't locate the field by tablename.colname, then just look for colname
				$prop = $fm->PropertyName;
				$this->$prop = $rowlocal[$fm->ColumnName];
			}
			else
            {
                // there is a required column missing from this $row array - mark as partially loaded
                $this->_phreezer->Observe("Missing column '".$fm->ColumnName."' while loading " . get_class($this), OBSERVE_WARN);
                $this->IsLoaded = false;
				$this->IsPartiallyLoaded = true;
            }
        }
        
		// now look for any eagerly loaded children - their fields should be available in this query
		$kms = $this->_phreezer->GetKeyMaps(get_class($this));
		
		foreach ($kms as $km)
		{
			if ($km->LoadType == KM_LOAD_EAGER)
			{
				// load the child object that was obtained eagerly and cache so we 
				// won't ever grab the same object twice in one page load
				$this->_phreezer->IncludeModel($km->ForeignObject);
				$foclass = $km->ForeignObject;
				$fo = new $foclass($this->_phreezer,$row);
				$this->_phreezer->SetCache($foclass, $fo->GetPrimaryKeyValue(), $fo);
			}
		}
		$this->_phreezer->Observe("Firing " . get_class($this) . "->OnLoad()",OBSERVE_DEBUG);
		$this->OnLoad();
    }
    
	/**
	* Returns a value from the local cache
	*
	* @access     public
	* @deprecated this is handled internally by Phreezer now
	* @param      string $key
	* @return     object
	*/
	public function GetCache($key)
    {
		return (array_key_exists($key, $this->_cache) ? $this->_cache[$key] : null);
	}
    
	/**
	* Sets a value from in local cache
	*
	* @access     public
	* @deprecated this is handled internally by Phreezer now
	* @param      string $key
	* @param      object $obj
	*/
	public function SetCache($key, $obj)
    {
		$this->_cache[$key] = $obj;
	}
    
	/**
	* Clears all values in the local cache
	*
	* @access     public
	* @deprecated this is handled internally by Phreezer now
	*/
	public function ClearCache()
    {
		$this->_cache = Array();
	}
    
    /**
    * Called after object is loaded, may be overridden
    *
    * @access     protected
    */
    protected function OnLoad(){}
    
	/**
	* Called by Phreezer prior to saving the object, may be overridden.
	* If this function returns any non-true value, then the save operation
	* will be cancelled.  This allows you to perform custom insert/update queries 
	* if necessary
	*
	* @access     protected
	* @param      boolean $is_insert true if Phreezer considers this a new record
	* @return     boolean
	*/
	public function OnSave($is_insert) {return true;}
	
	/**
    * Called by Phreezer after object is updated, may be overridden
    *
    * @access     public
    */
    public function OnUpdate(){}
    
    /**
    * Called by Phreezer after object is inserted, may be overridden
    *
    * @access     public
    */
    public function OnInsert(){}
    
    /**
    * Called by Phreezer after object is deleted, may be overridden
    *
    * @access     public
    */
    public function OnDelete(){}

	/**
	* Called by Phreezer before object is deleted, may be overridden.
	* if a true value is not returned, the delete operation will be aborted
	*
	* @access     public
	* @return	  bool
	*/
	public function OnBeforeDelete(){return true;}
	
    /**
    * Returns true if the current object has been loaded
    *
    * @access     public
    * @return     bool
    */
    public function IsLoaded()
    {
        return $this->IsLoaded;
    }
    
   
    /**
    * Throw an exception if an undeclared property is accessed
    *
    * @access     public
    * @param      string $key
    * @throws     Exception
    */
    public function __get($key)
    {
        throw new Exception("Unknown property: $key");
    }
    
    /**
    * Throw an exception if an undeclared property is accessed
    *
    * @access     public
    * @param      string $key
    * @param      string $val
    * @throws     Exception
    */
    public function __set($key,$val)
    {
        throw new Exception("Unknown property: $key");
    }

}

?>