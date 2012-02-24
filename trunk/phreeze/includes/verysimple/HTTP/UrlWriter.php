<?php
/** @package    verysimple::HTTP */

require_once("verysimple/Phreeze/ActionRouter.php");
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
class UrlWriter extends ActionRouter
{	
	/** Returns a url for the given controller, method and parameters
	 *
	 * @param string $controller
	 * @param string $method
	 * @param string $params in the format param1=val1&param2=val2
	 * @param bool $strip_api set to true to strip virtual part of the url in a rest call
	 * @param string $delim the querystring variable delimiter (& or &amp; for generating valid html)
	 * @return string URL
	 */
	public function Get($controller, $method, $params = "", $strip_api = true, $delim="&")
	{
		$format = str_replace("{delim}",$delim,$this->_format);
		
		$qs = "";
		$d = "";
		if (is_array($params))
		{
			foreach ($params as $key => $val)
			{
				// if no val, the omit the equal sign (this might be used in rest-type requests)
				$qs .= $d . $key . (strlen($val) ? ("=" . urlencode($val)) : "");
				$d = $delim;
			}
		}
		else
		{
			$qs = $params;
		}

		$url = sprintf($format,$controller,$method,$qs);

		// strip off trailing delimiters from the url
		$url = (substr($url,-5) == "&amp;") ? substr($url,0,strlen($url)-5) : $url;
		$url = (substr($url,-1) == "&" || substr($url,-1) == "?") ? substr($url,0,strlen($url)-1) : $url;

		$api_check = explode("/api/",RequestUtil::GetCurrentUrl());
		if ($strip_api && count($api_check) > 1)
		{
			$url = $api_check[0] . "/" . $url;
		}
		
		return $url;
	}
}

?>