<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("Observable.php");
require_once("Criteria.php");
require_once("KeyMap.php");
require_once("FieldMap.php");
require_once("DataAdapter.php");
require_once("NotFoundException.php");
require_once("CacheRam.php");
require_once("CacheNoCache.php");

/**
 * The Phreezer class is a factory for obtaining and working with Phreezable (persistable)
 * objects.  The Phreezer is generally the starting point for the application where you 
 * will obtain one or more objects.
 *
 * @package    verysimple::Phreeze 
 * @author     VerySimple Inc.
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    3.0
 */
class Phreezer extends Observable
{
	public $DataAdapter;
	
	/**
	 * Render engine can hold any arbitrary object used to render views
	 * @var Smarty
	 */
	public $RenderEngine;
	
	public $Version = 3.0;
	public $ValueCacheTimeout = 15;
	
	/**
	 * @var array
	 */
	private $_mapCache;

	/**
	 * @var ICache
	 */
	private $_level1Cache;

	/**
	 * @var ICache
	 */
	private $_level2Cache;
	
    /**
    * Contructor initializes the object.  The database connection is opened upon instantiation
    * and an exception will be thrown if db connectivity fails, so it is advisable to 
    * surround the instantiation with a try/catch
    *
    * @access public
    * @param ConnectionSetting $csetting
    * @param Observable $observer
    */
    public function Phreezer($csetting, $observer = null)
	{
		$this->_mapCache = new CacheRam();
		$this->_level1Cache = new CacheRam();
		$this->_level2Cache = new CacheNoCache();
		
		parent::AttachObserver($observer);
		$this->Observe("Phreeze Instantiated", OBSERVE_DEBUG);
		
		$this->DataAdapter = new DataAdapter($csetting, $observer);
		$this->DataAdapter->Open();
	}
	
	/**
	* Sets a cache provider for the level 1 cache
	* @param ICache $cache
	*/
	private function SetLevel1CacheProvider(ICache $cache)
	{
		$this->_level1Cache = $cache;
	}

	/**
	* Sets a cache provider for the level 1 cache
	* @param ICache $cache
	*/
	public function SetLevel2CacheProvider(ICache $cache)
	{
		$this->_level2Cache = $cache;
	}
	
	/**
	* ValueCache is a utility method allowing any object or value to 
	* be stored in the 2nd Level cache.  The timout is specified by 
	* ValueCacheTimeout.  This
	* 
	* @param string $sql
	* @param variant $val
	*/
	public function SetValueCache($key, $val)
	{
		$this->_level2Cache->Set(md5($key),$val,0,$this->ValueCacheTimeout);
	}
	
	/**
	* Retreives an object or value that was persisted using SetValueCache
	* 
	* @param string $key
	* @return variant
	*/
	public function GetValueCache($key)
	{
		return $this->_level2Cache->Get(md5($key));
	}

	/**
	* Deletes a value from the cache
	* @param string $objectclass
	* @param string $id
	*/
	public function DeleteCache($objectclass,$id)
	{
		$this->_level1Cache->Delete($objectclass . "_" . $id);
		$this->_level2Cache->Delete($objectclass . "_" . $id);
	}
	
	/**
	* Sets value in the cache
	* @param string $objectclass
	* @param string $id
	* @param Phreezable $val
	*/
	public function SetCache($objectclass,$id, Phreezable $val)
	{
		if ($val->NoCache) return false;
		$this->_level1Cache->Set($objectclass . "_" . $id,$val);
		$this->_level2Cache->Set($objectclass . "_" . $id,$val);
	}
	
	/**
	* Retrieves a value from the cache
	* @param string $objectclass
	* @param string $id
	* @return Phreezable
	*/
	public function GetCache($objectclass,$id)
	{
		// include the model so any serialized classes will not throw an exception
		$this->IncludeModel($objectclass);
		
		// see if this object was cached in the level 1 cache
		$obj = $this->_level1Cache->Get($objectclass . "_" . $id);
		
		if ($obj)
		{ 
			$this->Observe("Retrieved TYPE='$objectclass' ID='$id' from 1st Level Cache",OBSERVE_DEBUG);
			// $obj->Refresh($this); level 1 doesn't need to be refreshed
			return $obj;
		}
		
		// try the level 2 cahce
		$obj = $this->_level2Cache->Get($objectclass . "_" . $id);
		
		if ($obj)
		{ 
			$this->Observe("Retrieved TYPE='$objectclass' ID='$id' from 2nd Level Cache",OBSERVE_DEBUG);
			$obj->Refresh($this);
			return $obj;
		}
		
		$this->Observe("No L1/L2 Cache for TYPE='$objectclass' ID='$id'",OBSERVE_DEBUG);
		// $this->Observe("KEYS =" . serialize($this->_level1Cache->GetKeys()) ,OBSERVE_DEBUG);
		return null;
	}
	
	/**
    * Override of the base AttachObserver so that when an observer is attached, it
	* will also be attached to all child objects.  Note that some initialization 
	* messages won't be observed unless you provide it in the Phreezer constructor
    */
	public function AttachObserver($observer)
	{
		parent::AttachObserver($observer);
		$this->DataAdapter->AttachObserver($observer);
	}

	/**
	* Phreezer::Compare is used internally by Phreezer::Sort
	* @param object
	* @param object
	* @return bool
	*/
	static function Compare($a, $b)
	{
		return strcmp($a->ToString(), $b->ToString());
	}
	
	
	/**
	* Sort an array of Phreezable objects.  ToString() is used as the sort 
	* key.  You must implmement ToString on your sortable objects in order
	* for Phreezer::Sort to be effective
	*
	* @param array $objects array of objects
	*/
	static function Sort(&$objects)
	{
		usort($objects,array("Phreezer","Compare"));
	}
	
	/**
	* Get one instance of an object based on criteria.  If multiple records
	* are found, only the first is returned.  If no matches are found,
	* an exception is thrown
	*
	* @access public
	* @param string $objectclass the type of object that will be queried
    * @param Criteria $criteria a Criteria object to limit results
	* @param bool $crash_if_multiple_found default value = true
	* @return Phreezable
	*/
	public function GetByCriteria($objectclass, $criteria, $crash_if_multiple_found = true)
	{
		if (strlen($objectclass) < 1)
		{
			throw new Exception("\$objectclass argument is required");
		}
		
		$obj = null;
		$ds = $this->Query($objectclass, $criteria);
		
		if (!$obj = $ds->Next())
		{
			throw new NotFoundException("$objectclass with specified criteria not found");
		}
		
		if ($crash_if_multiple_found && $ds->Next())
		{
			throw new Exception("More than one $objectclass with specified criteria was found");
		}
		
		return $obj;
	}

    /**
    * Query for a specific type of object
    *
    * @access public
    * @param string $objectclass the type of object that your DataSet will contain
    * @param Criteria $criteria a Criteria object to limit results
    * @return DataSet
    */
 	public function Query($objectclass, $criteria = null)
	{
		if (strlen($objectclass) < 1)
		{
			throw new Exception("\$objectclass argument is required");
		}
		
		// if criteria is null, then create a generic one
		if ($criteria == null)
		{
			$criteria = new Criteria();
		}
		

		// see if this object has a custom query designated
		$custom = $this->GetCustomQuery($objectclass, $criteria);
		
		$sql = "";
		
		if ($custom)
		{
			$this->Observe("Using Custom Query",OBSERVE_DEBUG);
			$sql = $custom;
		}
		else
		{
			// the first-level fieldmaps should be from the primary table
			$fms = $this->GetFieldMaps($objectclass);

			// the query builder will handle creating the SQL for us
			$builder = new QueryBuilder($this);
			$builder->RecurseFieldMaps($objectclass, $fms);

			$sql = $builder->GetSQL($criteria);
		}
		
		$ds = new DataSet($this, $objectclass, $sql);
		
		return $ds;

	}
	
    /**
    * Get one instance of an object based on it's primary key value
    *
    * @access public
    * @param string $objectclass the type of object that your DataSet will contain
    * @param variant $id the value of the primary key
    * @return Phreezable
    */
	public function Get($objectclass, $id)
	{
		if (strlen($objectclass) < 1)
		{
			throw new Exception("\$objectclass argument is required");
		}
		if (strlen($id) < 1)
		{
			throw new Exception("\$id argument is required for $objectclass");
		}
		
		// see if this object was cached & if so return it
		$obj = $this->GetCache($objectclass,$id);
		if ($obj) return $obj;

		$pkm = $this->GetPrimaryKeyMap($objectclass);

		$criteria = new Criteria();
		$criteria->PrimaryKeyField = "`" . $pkm->TableName . "`.`" . $pkm->ColumnName . "`";
		$criteria->PrimaryKeyValue = $id;
		
		$ds = $this->Query($objectclass, $criteria);
		
		if (!$obj = $ds->Next())
		{
			throw new NotFoundException("$objectclass with primary key of $id not found");
		}
		
		// cache the object for future use
		$this->SetCache($objectclass,$id,$obj);
		
		return $obj;
	}
	
	/**
	 * Persist an object to the data store.  An insert or update will be executed based
	 * on whether the primary key has a value.  use $form_insert to override this 
	 * in the case of a primary key that is not an auto_increment
	 *
	 * @access public
	 * @param Object $obj the object to persist
	 * @param bool $force_insert (default = false)
	 * @return int the auto_increment id (insert) or the number of records updated (update)
	 */
	public function Save($obj, $force_insert = false)
	{
		$objectclass = get_class($obj);
		$fms = $this->GetFieldMaps($objectclass);

		$pk = $obj->GetPrimaryKeyName();
		$id = $obj->$pk;
		$table = $fms[$pk]->TableName;
		$pkcol = $fms[$pk]->ColumnName;
		$returnval = "";

		// if there is no value for the primary key, this is an insert
		$is_insert = $force_insert || strlen($id) == 0;

		// fire the OnSave event in case the object needs to prepare itself
		// if OnSave returns false, then don't proceed with the save
		$this->Observe("Firing ".get_class($obj)."->OnSave($is_insert)",OBSERVE_DEBUG);
		if (!$obj->OnSave($is_insert))
		{
			$this->Observe("".get_class($obj)."->OnSave($is_insert) returned FALSE.  Exiting without saving",OBSERVE_WARN);
			return false;
		}

		$sql = "";
		
		if (!$is_insert)
		{
			// this is an update

			// remove this class from the cache before saving
			$this->DeleteCache($objectclass,$id);

			$sql = "update `$table` set ";
			$delim = "";
			foreach ($fms as $fm)
			{
				if ((!$fm->IsPrimaryKey) && $fm->FieldType != FM_CALCULATION)
				{
					$prop = $fm->PropertyName;
					$val = $obj->$prop;
					$sql .= $delim . "`" . $fm->ColumnName . "` = '" . DataAdapter::Escape($val) . "'";
					$delim = ", ";
				}
			}
			$sql .= " where $pkcol = '" . DataAdapter::Escape($id) . "'";

			$returnval = $this->DataAdapter->Execute($sql);
			$obj->OnUpdate(); // fire OnUpdate event
		}
		else
		{
			// this is an insert
			$sql = "insert into `$table` (";
			$delim = "";
			foreach ($fms as $fm)
			{
				if ($force_insert || ((!$fm->IsPrimaryKey) && $fm->FieldType != FM_CALCULATION))
				{
					$prop = $fm->PropertyName;
					$val = $obj->$prop;
					$sql .= $delim . "`" . $fm->ColumnName . "`";
					$delim = ", ";
				}
			}

			$sql .= ") values (";

			$delim = "";
			foreach ($fms as $fm)
			{
				if ($force_insert || ((!$fm->IsPrimaryKey) && $fm->FieldType != FM_CALCULATION))
				{
					$prop = $fm->PropertyName;
					$val = $obj->$prop;
					$sql .= $delim . "'" . DataAdapter::Escape($val) . "'";
					$delim = ", ";
				}
			}
			$sql .= ")";
			
			// for the insert we also need to get the insert id of the primary key
			$returnval = $this->DataAdapter->Execute($sql);
			if (!$force_insert)
			{
				$obj->$pk = $this->DataAdapter->GetLastInsertId();
			}
			$obj->OnInsert(); // fire OnInsert event
		}
		
		return $returnval;
	}
	
    /**
    * Delete the given object from the data store
    *
    * @access public
    * @param Object $obj the object to delete
    */
	public function Delete($obj)
	{
		$objectclass = get_class($obj);
		
		// remove from cache
		$this->DeleteCache($objectclass,$id);

		if (!$obj->OnBeforeDelete())
		{
			$this->Observe("Delete was cancelled because OnBeforeDelete did not return true");
			return 0;
		}
		
		$fms = $this->GetFieldMaps($objectclass);

		$pk = $obj->GetPrimaryKeyName();
		$id = $obj->$pk;
		$table = $fms[$pk]->TableName;
		$pkcol = $fms[$pk]->ColumnName;
		
		$sql = "delete from `$table` where `$pkcol` = '" . DataAdapter::Escape($id) . "'";
		$returnval = $this->DataAdapter->Execute($sql);
		$obj->OnDelete(); // fire OnDelete event

		return $returnval;

	}

    /**
    * Delete all objects from the datastore used by the given object
    *
    * @access public
    * @param Object $obj the object to delete
    */
	public function DeleteAll($obj)
	{
		$fms = $this->GetFieldMaps(get_class($obj));
		$pk = $obj->GetPrimaryKeyName();
		$table = $fms[$pk]->TableName;
		
		$sql = "delete from `$table`";
		$returnval = $this->DataAdapter->Execute($sql);
		$obj->OnDelete(); // fire OnDelete event
		return $returnval;

	}

    /**
    * Returns all FieldMaps for the given object class
    *
    * @access public
    * @param string $objectclass the type of object that your DataSet will contain
    * @return Array of FieldMap objects
    */
	public function GetFieldMaps($objectclass)
	{
		// this is a temporary ram cache
		$fms = $this->_mapCache->Get($objectclass."FieldMaps");
		if ($fms) return $fms;
		
		$this->IncludeModel($objectclass);
		eval("\$fms = " . $objectclass . "Map::GetFieldMaps();");
		$this->_mapCache->Set($objectclass."FieldMaps",$fms);
		return $fms;
	}

    /**
    * Returns the custom query for the given object class if it is defined
    *
    * @access public
    * @param string $objectclass the type of object that your DataSet will contain
    * @return Array of FieldMap objects
    */
	public function GetCustomQuery($objectclass, $criteria)
	{
		$this->IncludeModel($objectclass);
		eval("\$sql = " . $objectclass . "::GetCustomQuery(\$criteria);");
		return $sql;
	}

	static $cnt = 0; // used for debugging php memory errors due to circular references
	
    /**
    * Returns all KeyMaps for the given object class
    *
    * @access public
    * @param string $objectclass the type of object
    * @return Array of KeyMap objects
    */
	public function GetKeyMaps($objectclass)
	{
		// TODO: if a php memory error occurs within this method, uncomment this block to debug
		/*
		if (Phreezer::$cnt++ > 500)
		{
			throw new Exception("A sanity limit was exceeded when recursing KeyMaps for `$objectclass`.  Please check your Map for circular joins.");
		}
		//*/
		
		// this is a temporary ram cache
		$kms = $this->_mapCache->Get($objectclass."KeyMaps");
		if ($kms) return $kms;
		
		$this->IncludeModel($objectclass);
		eval("\$kms = " . $objectclass . "Map::GetKeyMaps();");
		$this->_mapCache->Set($objectclass."KeyMaps",$kms);
		return $kms;
	}

    /**
    * Return specific FieldMap for the given object class with the given name
    *
    * @access public
    * @param string $objectclass the type of object
    * @param string $propertyname the name of the property
    * @return Array of FieldMap objects
    */
	public function GetFieldMap($objectclass, $propertyname)
	{
		$fms = $this->GetFieldMaps($objectclass);
		return $fms[$propertyname];
	}
	
    /**
    * Return specific KeyMap for the given object class with the given name
    *
    * @access public
    * @param string $objectclass the type of object
    * @param string $keyname the name of the key
    * @return Array of KeyMap objects
    */
	public function GetKeyMap($objectclass, $keyname)
	{
		$kms = $this->GetKeyMaps($objectclass);
		return $kms[$keyname];
	}

    /**
    * Returns the name of the DB column associted with the given property
    *
    * @access public
    * @param string $objectclass the type of object
    * @param string $propertyname the name of the property
    * @return string name of the DB Column
    */
	public function GetColumnName($objectclass, $propertyname)
	{
		$fm = $this->GetFieldMap($objectclass, $propertyname);
		return $fm->ColumnName;
	}

	/**
	* Returns the name of the DB Table associted with the given property
	*
	* @access public
	* @param string $objectclass the type of object
	* @param string $propertyname the name of the property
	* @return string name of the DB Column
	*/
	public function GetTableName($objectclass, $propertyname)
	{
		$fm = $this->GetFieldMap($objectclass, $propertyname);
		return $fm->TableName;
	}
	
	/**
    * Return the KeyMap for the primary key for the given object class
    *
    * @access public
    * @param string $objectclass the type of object
    * @return KeyMap object
    */
	public function GetPrimaryKeyMap($objectclass)
	{
		$fms = $this->GetFieldMaps($objectclass);
		foreach ($fms as $fm)
		{
			if ($fm->IsPrimaryKey)
			{
				return $fm;
			}
		}
	}


    /**
    * Query for a child objects in a one-to-many relationship
    *
    * @access public
    * @param Phreezable $parent the parent object
    * @param string $keyname The name of the key representing the relationship
    * @return Criteria $criteria a Criteria object to limit the results
    */
    public function GetOneToMany($parent, $keyname, $criteria)
    {
		
		// get the keymap for this child relationship
		$km = $this->GetKeyMap(get_class($parent), $keyname);

		// we need the value of the foreign key.  (ex. to get all orders for a customer, we need Customer.Id)
		$parent_prop = $km->KeyProperty;
		$key_value = $parent->$parent_prop;

		if (!$criteria)
		{
			// if no criteria was specified, then create a generic one.  we can specify SQL
			// code in the constructor, but we have to translate the properties into column names
			$foreign_table = $this->GetTableName($km->ForeignObject,$km->ForeignKeyProperty);
			$foreign_column = $this->GetColumnName($km->ForeignObject,$km->ForeignKeyProperty);
			$criteria = new Criteria("`" . $foreign_table . "`.`" . $foreign_column . "` = '" . DataAdapter::Escape($key_value) . "'");
		}
		else
		{
			// ensure that the criteria passed in will filter correctly by foreign key
			$foreign_prop = $km->ForeignKeyProperty;
			
			// this is only for backwards compatibility, but it should be ignored by current criteria objects
			$criteria->$foreign_prop = $key_value;
			
			// the current criteria "Equals" format "FieldName_Equals" 
			$foreign_prop .= "_Equals";
			$criteria->$foreign_prop = $key_value;
		}

		return $this->Query($km->ForeignObject,$criteria);
	}
	
    /**
    * Query for a parent object in a many-to-one relationship
    *
    * @access public
    * @param Phreezable $parent the parent object
    * @param string $keyname The name of the key representing the relationship
    * @return Phreezable object an object of the type specified by the KeyMap
    */
	public function GetManyToOne($parent, $keyname)
	{
		// get the keymap for this child relationship
		$km = $this->GetKeyMap(get_class($parent), $keyname);
		
		// we need the value of the foreign key.  (ex. to get all orders for a customer, we need Customer.Id)
		// we also need to know the class of the object we're retrieving because if it's cached, we need to 
		// make sure the model file is loaded
		$objectclass = $km->ForeignObject;
		$parent_prop = $km->KeyProperty;
		$key_value = $parent->$parent_prop;
		
		// get this object  Get uses caching so we don't need to bother
		$obj = $this->Get($km->ForeignObject,$key_value);
		
		return $obj;

	}
	
	/**
	* Dynamically override the LoadType for a KeyMap.  This is useful for 
	* eager fetching for a particular query.  One set, this configuration
	* will be used until the end of the page context, or it is changed.
	*
	* @access public
	* @param string $objectclass The name of the object class
	* @param string $keyname The unique id of the KeyMap in the objects KeyMaps collection
	* @param int $load_type (optional) KM_LOAD_EAGER | KM_LOAD_LAZY  (default is KM_LOAD_EAGER)
	*/
	public function SetLoadType($objectclass, $keyname, $load_type = KM_LOAD_EAGER)
	{
		$this->GetKeyMap($objectclass, $keyname)->LoadType = $load_type;
	}
	
	/**
	* If the type is not already defined, attempts to require_once the definition.
	* If the Model file cannot be located, an exception is thrown
	*
	* @access public
	* @param string $objectclass The name of the object class
	*/
	public function IncludeModel($objectclass)
	{
		if (class_exists($objectclass)) return true;
		
		// re-route error handling temporarily so we can catch errors
		set_error_handler(array("Phreezer", "ModelException"),E_ALL);
		
		// use include instead of require so we can catch runtime exceptions
		include_once("Model/" . $objectclass . ".php");
		
		// reset error handling back to whatever it was
		restore_error_handler();
		
		if (!class_exists($objectclass))
		{
			// the class still isn't defined so there was a problem including the model
			$this->Observe("Unable to locate Model definition for '$objectclass'",OBSERVE_FATAL);
			throw new Exception("Unable to locate Model definition for '$objectclass'");
		}
	}

	/**
	 * Handler for catching IncludeModel file-not-found errors
	 */
	public static function ModelException($code, $string, $file, $line, $context)
	{
		$tmp1 = explode(")",$string);
		$tmp2 = explode("(",$tmp1[0]);
		$mfile = $tmp2[1];
		$msg = "Phreeze was unable to locate the Model file: ~/" . $mfile;
		
		throw new Exception($msg,$code);
	}

}

?>