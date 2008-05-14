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
	
	/**
	 * Constructor requires a reference to a MemCache object
	 * @param $memcache
	 */
	public function CacheMemCache($memcache)
	{
		$this->_memcache = $memcache;
	}
	
	public function Get($key,$flags=null)
	{
		return $this->_memcache->get($key);
	}
	
	public function Set($key,$val,$flags=null,$timeout=null)
	{
		return $this->_memcache->set($key,$val,$flags,$timeout);
	}

	public function Delete($key)
	{
		return $this->_memcache->delete($key);
	}
	
}

?>