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
    
	/** prevent serialization of _phreezer */
	function __sleep()
	{
		$props = array();
		$ro = new ReflectionObject($this);
		
		foreach ($ro->getProperties() as $rp)
		{
			if ($rp->name != "_phreezer" && $rp->isPrivate() == false)
				$props[] = $rp->name;
		}
		return $props;
	}
	
	/** put object back into stable state after deserialization */
	function __wakeup()
	{

	}
	
	function Refresh(Phreezer $phreezer, $row = null)
	{
		$this->_phreezer = $phreezer;
	}
	
    /**
    * constructor
    *
    * @access     public
    * @param      Phreezer $phreezer
    * @param      Array $row
    */
    final function Reporter(&$phreezer, $row = null)
    {
		$this->_phreezer = $phreezer;
		
        if ($row)
        {
            $this->Load($row);
        }
    }

    /**
    * This static function can be overridden to populate this object with
    * results of a custom query
    *
    * @access     public
    * @param      Criteria $criteria
    * @return     string
    */
    abstract static function GetCustomQuery($criteria);
	
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