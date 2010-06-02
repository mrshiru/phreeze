<?php
/** @package    verysimple::HTTP */

require_once("verysimple/HTTP/RequestUtil.php");

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
	 * @param bool $strip_api set to true to strip virtual part of the url in a rest call
	 * @return string URL
	 */
	public function Get($controller,$method,$params = "",$strip_api = true)
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
		
		$url = (substr($url,-1,1) == "&" || substr($url,-1,1) == "?") ? substr($url,0,strlen($url)-1) : $url;

		//
		$api_check = explode("/api/",RequestUtil::GetCurrentUrl());
		if ($strip_api && count($api_check) > 1)
		{
			$url = $api_check[0] . "/" . $url;
		}
		
		return $url;
	}
	
}

?>