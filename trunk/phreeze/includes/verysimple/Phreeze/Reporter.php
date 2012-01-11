<?php
/** @package    verysimple::Phreeze */

/**
 * Reporter allows creating dynamic objects that do not necessarily reflect
 * the structure of the datastore table.  This is often useful for reporting
 * or returning aggregate data
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2005 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
abstract class Reporter
{
    protected $_phreezer;
    
    public $CacheLevel = 0;
    
    /**
    * constructor
    *
    * @access     public
    * @param      Phreezer $phreezer
    * @param      Array $row
    */
    final function __construct(&$phreezer, $row = null)
    {
		$this->_phreezer = $phreezer;
		
        if ($row)
        {
            $this->Load($row);
        }
    }
    
    /**
	 * When serializing, make sure that we ommit certain properties that
	 * should never be cached or serialized.
	 */
	function serialize()
	{
		$no_cache_props = array("_cache","_phreezer","_val_errors","_base_validation_complete","CacheLevel","IsLoaded","NoCache");
		$propvals = array();
		$ro = new ReflectionObject($this);
		
		foreach ($ro->getProperties() as $rp )
		{
			$propname = $rp->getName();
			
			if (!in_array($propname,$no_cache_props))
			{
				if (method_exists($rp,"setAccessible")) 
				{
					$rp->setAccessible(true);
					$propvals[$propname] = $rp->getValue($this);
				}
				elseif (!$rp->isPrivate())
				{
					// if < php 5.3 we can't serialize private vars
					$propvals[$propname] = $rp->getValue($this);
				}
				
			}
		}

		return serialize($propvals);
	}
	
	/**
	 * Reload the object when it awakes from serialization
	 * @param $data
	 */
	function unserialize($data)
	{
		$propvals = unserialize($data);
		
		$ro = new ReflectionObject($this);
		
		foreach ($ro->getProperties() as $rp )
		{
			$propname = $rp->name;
			if ( array_key_exists($propname,$propvals) )
			{
				if (method_exists($rp,"setAccessible")) 
				{
					$rp->setAccessible(true);
					$rp->setValue($this,$propvals[$propname]);
				}
				elseif (!$rp->isPrivate())
				{
					// if < php 5.3 we can't serialize private vars
					$rp->setValue($this,$propvals[$propname]);
				}
				
			}
		}
	}
	
	/**
	 * Restores the object's connection to the datastore, for example after serialization
	 * @param $phreezer
	 * @param $row
	 */
	function Refresh(Phreezer $phreezer, $row = null)
	{
		$this->_phreezer = $phreezer;

		if ($row)
        {
            $this->Load($row);
        }
        
         $this->OnRefresh();
	}

	/**
    * Called after object is refreshed, may be overridden
    *
    * @access     public
    */
    public function OnRefresh(){}

    /**
    * This static function can be overridden to populate this object with
    * results of a custom query
    *
    * @access     public
    * @param      Criteria $criteria
    * @return     string
    */
    static function GetCustomQuery($criteria)
    {
    	return "";
    }
    
    /**
    * This static function can be overridden to count the number of results
    * in the query
    *
    * @access     public
    * @param      Criteria $criteria
    * @return     string
    */
    static function GetCustomCountQuery($criteria)
    {
    	return "";
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
    * Loads the object with data given in the row array.
    *
    * @access     public
    * @param      Array $row
    */
    function Load(&$row)
    {
		$this->_phreezer->Observe("Loading " . get_class($this),OBSERVE_DEBUG);

		foreach (array_keys($row) as $prop)
		{
			$this->$prop = $row[$prop];
		}

        $this->OnLoad();
    }
    
    /**
    * Called after object is loaded, may be overridden
    *
    * @access     protected
    */
    protected function OnLoad(){}
    
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

}

?>