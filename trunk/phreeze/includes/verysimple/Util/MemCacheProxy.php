<?php
/** @package    verysimple::Util */

/**
* MemCacheProxy provides simple access to memcache pool but ignores
* if the server is down instead of throwing an error.  if the server
* could not be contacted, ServerOffline will be set to true.
* @package    verysimple::Authentication
* @author     VerySimple Inc.
* @copyright  1997-2007 VerySimple, Inc.
* @license    http://www.gnu.org/licenses/lgpl.html  LGPL
* @version    1.0
*/
class MemCacheProxy
{
	private $_memcache;
	public $ServerOffline = false;
	
	/**
	 * Acts as a proxy for a MemCache server and fails gracefull if the pool
	 * cannot be contacted
	 * @param array in host/port format: array('host1'=>'11211','host2'=>'11211')
	 */
	public function MemCacheProxy($server_array = array('localhost'=>'11211'))
	{
		if (class_exists('Memcache'))
		{
			$this->_memcache = new Memcache();
			foreach (array_keys($server_array) as $host)
			{
				// print "adding server $host " . $server_array[$host];
				$this->_memcache->addServer($host, $server_array[$host]);
			}
		}
		else
		{
			$this->ServerOffline = true;
		}
	}
	
	
	/**
	 * This is method get
	 * @param string $key This is a description
	 * @return mixed cache value or null
	 */
	public function get($key)
	{
		// prevent hammering the server if it is down
		if ($this->ServerOffline) return null;
		
		$val = null;
		try
		{
			$val = $this->_memcache->get($key);
		}
		catch (Exception $ex)
		{
			// memcache is not working
			$this->ServerOffline = true;
		}
		return $val;
	}
	
	
	/**
	 * This is method set
	 *
	 * @param mixed $key This is a description
	 * @param mixed $var This is a description
	 * @param mixed $flags This is a description
	 * @param mixed $expire This is a description
	 * @return mixed This is the return value description
	 *
	 */
	public function set($key, $var, $flags = false, $expire = 0)
	{
		// prevent hammering the server if it is down
		if ($this->ServerOffline) return null;
		
		try
		{
			$this->_memcache->set($key, $var, $flags, $expire);
		}
		catch (Exception $ex)
		{
			// memcache is not working
			$this->ServerOffline = true;
		}
	}
	
	
}

?>