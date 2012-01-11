<?php
/** @package    verysimple::HTTP */

require_once("verysimple/HTTP/RequestUtil.php");
require_once("verysimple/Util/UrlWriterMode.php");

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
	private $_mode;
	
	/** 
	 * Constructor allows a rewriting pattern to be specified
	 *
	 * @param string $format sprintf compatible format
	 * @param string UrlWriterMode string
	 */
	public function __construct($format = "%s.%s.page?%s", $mode = UrlWriterMode::WEB )
	{
		$this->_mode = $mode;
		$this->_format = $format;
	}
	
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
	
	/**
	 * Returns true or false based on the $value passed in as to whether or not the
	 * URL Writer is currently in that mode.
	 * 
	 * @param $value	String mode to check against the current mode
	 * @return	boolean TRUE if arg passed in is the current mode
	 */
	public function ModeIs( $value )
	{
		if( strcmp($this->_mode,$value) == 0 )
			return true;
		else
			return false;
	}
	
	/**
	 * Returns how the Dispatcher plucks it's controller and method from the URL.
	 * 
	 * @param $default_action	The Default action in case the argument hasn't been supplied
	 */
	public function GetAction( $url_param = "action", $default_action = "Account.DefaultAction" )
	{
		switch( $this->_mode )
		{
			// TODO: Determine mobile/joomla URL action (if different from default)
			/*
			 *	case UrlWriterMode::JOOMLA:
			 *		break;
			 *	case UrlWriterMode::MOBILE:
			 *		break;
			 */
			default:
				// default is to return the standard browser-based action=%s.%s&%s:
				return RequestUtil::Get($url_param, $default_action);
				break;
		}
	}
}

?>