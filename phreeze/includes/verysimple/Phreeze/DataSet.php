<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("DataPage.php");

/**
 * DataSet stores zero or more Loadable objects
 * The DataSet is the object that is returned by every Phreezer Query operation.
 * The DataSet contains various methods to enumerate through , or retrieve all
 * results all at once.
 *
 * The DataSet executes queries lazily, only when the first result is retrieved.
 * Using GetDataPage will allow retreival of sub-sets of large amounts of data without
 * querying the entire database
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc. <noreply@verysimple.com>
 * @copyright  1997-2007 VerySimple Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.1
 */
class DataSet implements Iterator
{
    protected $_phreezer;
    protected $_rs;
    protected $_objectclass;
    protected $_counter;

    private $_sql;
    private $_current; // the current object in the set
	private $_last; // the previous object in the set
	private $_totalcount;
	private $_no_exception;  // used during iteration to suppress exception on the final Next call
	private $_no_cache;  // if specified then no cached values will be used
	
	public $UnableToCache = true;
	
    /**
    * Contructor initializes the object
    *
    * @access     public
    * @param Phreezer
    * @param string class of object this DataSet contains
    * @param string $sql code
    * @param bool $nocache set to true and any existing cache values will be ignored
    */
    function DataSet(&$preezer, $objectclass, $sql, $nocache = false)
    {
        $this->_counter = -1;
		$this->_totalcount = -1;
		$this->_eof = false;
        $this->_objectclass = $objectclass;
        $this->_phreezer =& $preezer;
        $this->_rs = null;
        $this->_sql = $sql;
        $this->_no_cache = $nocache;
    }

    /**
    * _getObject must be overridden and returns the type of object that
    * this collection will contain.
    *
    * @access     private
    * @param      array $row array to use for populating a single object
    * @return     Preezable
    */
    private function _getObject(&$row)
    {
        $obj = new $this->_objectclass($this->_phreezer, $row);
        return $obj;
    }
    
    /**
    * Next returns the next object in the collection.
    *
    * @access     public
    * @return     Preezable
    */
    function Next()
    {
    	if ($this->UnableToCache) 
    	{
    		require_once("verysimple/Util/ExceptionFormatter.php");
    		$info = ExceptionFormatter::FormatTrace(debug_backtrace());
    		$this->_phreezer->Observe("(DataSet.Next: unable to cache query with cursor) " . $info . "  " . $this->_sql,OBSERVE_QUERY);
    		
    		// use this line to discover where an uncachable query is coming from
    		// throw new Exception("WTF");

    		// stop this warning from repeating on every next call for this dataset
    	    $this->UnableToCache = false; 
    	}
    	
        $this->_verifyRs();
        
		$this->_current = null;
		$this->_counter++;
		
        if ($this->_eof)
        {
			if (!$this->_no_exception)
				throw new Exception("EOF: This is a forward-only dataset.");
        }
        
        if ($row = $this->_phreezer->DataAdapter->Fetch($this->_rs))
        {
            $this->_current = $this->_getObject($row);
			$this->_last = $this->_current;
		}
        else
        {
            $this->_eof = true;
        }

		return $this->_current;
    }
    
 
    /**
    * Executes the sql statement and fills the resultset if necessary
    */
    private function _verifyRs()
    {
        if ($this->_rs == null)
        {        	
			$this->_phreezer->IncludeModel($this->_objectclass);
			$this->_rs = $this->_phreezer->DataAdapter->Select($this->_sql);
        }
    }
    
	public function rewind() {
		$this->_rs = null;
		$this->_counter = 0;
		$this->_no_exception = true;
		$this->_total = $this->Count();
		$this->_verifyRs();
		$this->Next(); // we have to get the party started for php iteration
	}
	
	public function current() {
		// php iteration calls next then gets the current record.  The DataSet
		// Next return the current object.  so, we have to fudge a little on the
		// laster iteration to make it work properly
		return ($this->key() == $this->Count()) ? $this->_last : $this->_current;
	}
	
	public function key() {
		return $this->_counter;
	}
	
	public function valid() {
		return $this->key() <= $this->Count();
	}
    
    /**
    * Count returns the number of objects in the collection.  If the 
    * count is not available, a count statement will be executed to determine the total
    * number of rows available
    * 
    * Note: if you get an "Unknown Column" error during a query, it may be due to tables being
    * joined in the wrong order.  To fix this, simply include references in your FieldMap to
    * the foreign tables in the same order that you wish them to be included in the query
    *
    * @access     public
    * @return     int
    */
    function Count()
    {
		if ($this->_totalcount == -1)
		{
			// check the cache
			$cachekey = $this->_sql . " COUNT";
			$this->_totalcount = $this->_no_cache ? null : $this->_phreezer->GetValueCache($cachekey);
			
			// if no cache, go to the db
			if ($this->_totalcount != null)
			{
				$this->_phreezer->Observe("(DataSet.Count: skipping query because cache exists) " . $this->_sql,OBSERVE_QUERY);
			}
			else
			{
				$this->_phreezer->Observe("(DataSet.Count: query does not exist in cache) " . $this->_sql,OBSERVE_QUERY);
				
				$sql = "select count(1) as counter from (" . $this->_sql . ") tmptable";
				
				$rs = $this->_phreezer->DataAdapter->Select($sql);
				$row = $this->_phreezer->DataAdapter->Fetch($rs);
				$this->_phreezer->DataAdapter->Release($rs);
				$this->_totalcount = $row["counter"];

				$this->_phreezer->SetValueCache($cachekey,$this->_totalcount);
			}
		}

		return $this->_totalcount;
    }
    
    /**
    * Returns the entire collection as an array of objects
    *
    * @access     public
    * @return     array
    */
    function ToObjectArray()
    {
		$cachekey = $this->_sql . " OBJECTARRAY";

		$arr = $this->GetDelayedCache($cachekey);
    	
		if ($arr != null)
		{
			// we have a cache value, so we will repopulate from that
			$this->_phreezer->Observe("(DataSet.ToObjectArray: skipping query because cache exists) " . $this->_sql,OBSERVE_QUERY);
			foreach ($arr as $obj)
			{
				$obj->Refresh($this->_phreezer);
			}
		}
		else
		{
			// there is nothing in the cache so we have to reload it
			
			$this->LockCache($cachekey);
			
			$this->UnableToCache = false;
			$arr = Array();
			
			while ($object =& $this->Next())
			{
				$arr[] = $object;
			}
			
			$this->_phreezer->SetValueCache($cachekey,$arr);
			
			$this->UnlockCache($cachekey);
		}
			
		return $arr;
    }
    

    /**
    * @deprecated Use GetLabelArray instead
    */
    function ToLabelArray($val_prop, $label_prop)
    {
        return $this->GetLabelArray($val_prop, $label_prop);
    }
	
	/**
    * Returns the entire collection as an associative array that can be easily used
    * for Smarty dropdowns
    *
    * @access     public
    * @param      string $val_prop the object property to be used for the dropdown value
    * @param      string $label_prop the object property to be used for the dropdown label
    * @return     array
    */
    function GetLabelArray($val_prop, $label_prop)
    {
		// check the cache
		// $cachekey = md5($this->_sql . " VAL=".$val_prop." LABEL=" . $label_prop);
		$cachekey = $this->_sql . " VAL=".$val_prop." LABEL=" . $label_prop;
		
		$arr = $this->GetDelayedCache($cachekey);
		
		// if no cache, go to the db
		if ($arr != null)
		{
			$this->_phreezer->Observe("(DataSet.GetLabelArray: skipping query because cache exists) " . $this->_sql,OBSERVE_QUERY);
		}
		else
		{
			$this->LockCache($cachekey);
			
			$arr = Array();
			$this->UnableToCache = false;
			
			while ($object =& $this->Next())
			{
				$arr[$object->$val_prop] =& $object->$label_prop;
			}

			$this->_phreezer->SetValueCache($cachekey,$arr);
			
			$this->UnlockCache($cachekey);
		}
		
        return $arr;
    }

	/**
	* Release the resources held by this DataSet
	*
	* @access     public
	*/
	function Clear()
    {
         $this->_phreezer->DataAdapter->Release($this->_rs);
    }
    
    /**
     * Returns a DataPage object suitable for binding to the smarty PageView plugin
     *
     * @access     public
     * @param int $pagenum which page of the results to view
     * @param int $pagesize the size of the page (or zero to disable paging).
     * @return DataPage
     */
    function GetDataPage($pagenum, $pagesize)
    {
		// check the cache
		// $cachekey = md5($this->_sql . " PAGE=".$pagenum." SIZE=" . $pagesize);
		$cachekey = $this->_sql . " PAGE=".$pagenum." SIZE=" . $pagesize;
		
		$page = $this->GetDelayedCache($cachekey);
		
		// if no cache, go to the db
		if ($page != null)
		{
			$this->_phreezer->Observe("(DataSet.GetDataPage: skipping query because cache exists) " . $this->_sql,OBSERVE_QUERY);
			
			foreach ($page->Rows as $obj)
			{
				$obj->Refresh($this->_phreezer);
			}
		}
		else
		{
			$this->LockCache($cachekey);
			
			$this->UnableToCache = false;
			
			$page = new DataPage();
			$page->ObjectName = $this->_objectclass;
			$page->ObjectInstance = new $this->_objectclass($this->_phreezer);
			$page->PageSize = $pagesize;
			$page->CurrentPage = $pagenum;
			$page->TotalResults = $this->Count();
	        
	       
			// first check if we have less than or exactly the same number of
			// results as the pagesize.  if so, don't bother doing the math.
			// we know we just have one page
			if ($page->TotalPages > 0 && $page->TotalPages <= $page->PageSize)
			{
				$page->TotalPages = 1;
			}
			else if ($pagesize == 0)
			{
				// we don't want paging to occur in this case
				$page->TotalPages = 1;
			}
			else
			{
				// we have more than one page.  we always need to round up
				// here because 5.1 pages means we are spilling out into 
				// a 6th page.  (this will also handle zero results properly)
				$page->TotalPages = ceil( $page->TotalResults / $pagesize );
			}
	        
			// now enumerate through the rows in the page that we want.
			// decrement the requested pagenum here so that we will be 
			// using a zero-based array - which saves us from having to 
			// decrement on every iteration
			$pagenum--;
			
			$start = $pagesize * $pagenum;
			
			// ~~~ more efficient method where we limit the data queried ~~~  
			// since we are doing paging, we want to get only the records that we
			// want from the database, so we wrap the original query with a 
			// limit query.
			// $sql = "select * from (" . $this->_sql . ") page limit $start,$pagesize";
			$sql = $this->_sql . ($pagesize == 0 ? "" : " limit $start,$pagesize");
			$this->_rs = $this->_phreezer->DataAdapter->Select($sql);
	        
	        
			// transfer all of the results into the page object
			while ( $obj = $this->Next() )
			{
				$page->Rows[] = $obj;
			}

			$this->_phreezer->SetValueCache($cachekey,$page);

			$this->Clear();
			
			$this->UnlockCache($cachekey);
			
		}
		
		return $page;
	}


    /**
     * 
     * @param string $cachekey
     */
    private function GetDelayedCache($cachekey)
    {
    	// if no cache then don't return anything
    	if ($this->_no_cache) return null;
    	
        $obj = $this->_phreezer->GetValueCache($cachekey);

        $lockfile = $this->_phreezer->LockFilePath 
			? $this->_phreezer->LockFilePath . md5($cachekey) . ".lock" 
			: "";
			
    	// no cache, so try three times with a delay to prevent a cache stampede
    	$counter = 1;
		while ( $counter < 4 && $obj == null && $lockfile && file_exists($lockfile) )
		{
			$this->_phreezer->Observe("(DataSet.GetDelayedCache: flood prevention. delayed attempt ".$counter." of 3...) " . $cachekey,OBSERVE_DEBUG);
			usleep(50000); // 5/100th of a second
			$obj = $this->_phreezer->GetValueCache($cachekey);
			$counter++;
		}
		
		return $obj;
    }
    
    /**
     * 
     * @param $cachekey
     */
    private function IsLocked($cachekey)
    {
		return $this->_phreezer->LockFilePath && file_exists($this->_phreezer->LockFilePath . md5($cachekey) . ".lock" );
    }
    
    /**
     * 
     * @param $cachekey
     */
    private function LockCache($cachekey)
    {
		if ($this->_phreezer->LockFilePath) 
		{
			touch($this->_phreezer->LockFilePath . md5($cachekey) . ".lock");
		}
    	
    }
    
    /**
     * 
     * @param $cachekey
     */
    private function UnlockCache($cachekey)
    {
		if ($this->_phreezer->LockFilePath) 
		{
			$lockfile = $this->_phreezer->LockFilePath . md5($cachekey) . ".lock";
			if (file_exists($lockfile)) @unlink($lockfile);
		}
	}

}

?>