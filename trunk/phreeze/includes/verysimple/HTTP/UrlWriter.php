<?php
/** @package    verysimple::HTTP */

/**
 * class for dealing with URLs
 *
 * @package    verysimple::HTTP 
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc. http://www.verysimple.com
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class UrlWriter
{
	private $_format;
	/** 
	 * Constructor allows a rewriting pattern to be specified
	 *
	 * @param string $format sprintf compatible format
	 */
	public function UrlWriter($format = "%s.%s.page?%s")
	{
		$this->_format = $format;
	}
	
	/** Returns a url for the given controller, method and parameters
	 *
	 * @param string $controller
	 * @param string $method
	 * @param string $params in the format param1=val1&param2=val2
	 * @return string URL
	 */
	public function Get($controller,$method,$params = "")
	{
		$qs = "";
		if (is_array($params))
		{
			foreach ($params as $key => $val)
			{
				$qs .= "&" . $key . "=" . urlencode($val);
			}
		}
		else
		{
			$qs = $params;
		}
		
		$url = sprintf($this->_format,$controller,$method,$qs);
		return (substr($url,-1,1) == "&" || substr($url,-1,1) == "?") ? substr($url,0,strlen($url)-1) : $url;
	}
	
}

?>