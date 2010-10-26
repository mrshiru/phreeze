<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("ICache.php");

/**
 * CacheRam is an implementation of a Cache that persists to ram for the current page load only
 *
 * @package    verysimple::Phreeze 
 * @author     VerySimple Inc.
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class CacheMemCache implements ICache
{
	private $_memcache = null;
	private $_prefix = "";
	
	/**
	 * Constructor requires a reference to a MemCache object
	 * @param Memcache memcache object
	 * @param string a unique prefix to use so this app doesn't conflict with any others that may use the same memcache pool
	 */
	public function CacheMemCache($memcache,$uniquePrefix = "CACHE-")
	{
		$this->_memcache = $memcache;
		$this->_prefix = $uniquePrefix ? $uniquePrefix . "-" : "";
	}
	
	/**
	 * @inheritdocs
	 */
	public function Get($key,$flags=null)
	{
		return $this->_memcache->get($this->_prefix . $key);
	}
	
	/**
	 * @inheritdocs
	 */
	public function Set($key,$val,$flags=null,$timeout=null)
	{
		return $this->_memcache->set($this->_prefix . $key,$val,$flags,$timeout);
	}

	/**
	 * @inheritdocs
	 */
	public function Delete($key)
	{
		return $this->_memcache->delete($this->_prefix . $key);
	}
	
}

?>