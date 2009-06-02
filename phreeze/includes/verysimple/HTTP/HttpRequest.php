<?php
/** @package    verysimple::HTTP */

/**
 * HttpRequest is a utility method for makine HTTP requests
 *
 * @package    verysimple::HTTP
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class HttpRequest
{
	
	/** 
	* Make an HTTP POST request using the best method available on the server
	* 
	* @param string $url
	* @param array $data (array of field/value pairs)
	* @param bool true to require verification of SSL cert
	* @return string
	*/
	function Post($url, $data, $verify_cert = false)
	{
		if (function_exists("curl_init"))
		{
			return HttpRequest::CurlPost($url, $data, $verify_cert);
		}
		else
		{
			return HttpRequest::FilePost($url, $data, $verify_cert);
		}
	}
	
	/** 
	* Make an HTTP GET reequest using the best method available on the server
	* 
	* @param string $url
	* @param array $data (array of field/value pairs)
	* @param bool true to require verification of SSL cert
	* @return string
	*/
	function Get($url, $data = "", $verify_cert = false)
	{
		if (function_exists("curl_init"))
		{
			return HttpRequest::CurlGet($url, $data, $verify_cert);
		}
		else
		{
			return HttpRequest::FileGet($url, $data, $verify_cert);
		}
	}
	
	/** 
	 * Make an HTTP GET request using file_get_contents
	 * 
	 * @param string $url
	 * @param array $data (array of field/value pairs)
	 * @param bool true to require verification of SSL cert
	 * @return string
	 */
	function FileGet($url, $data = "", $verify_cert = false)
	{
		$qs = HttpRequest::ArrayToQueryString($data);
		$full_url = $url . ($qs ? "?" . $qs : "");
		return file_get_contents( $full_url );
	}
	
	/** 
	 * Make an HTTP POST request using file_get_contents
	 * 
	 * @param string $url
	 * @param array $data (array of field/value pairs)
	 * @param bool true to require verification of SSL cert
	 * @return string
	 */
	function FilePost($url, $data = "", $verify_cert = false) 
	{
		$qs = HttpRequest::ArrayToQueryString($data);
		$url = $url . ($qs ? "?" . $qs : "");
		
		$show_headers = false;
		$url = parse_url($url);
		
		if (!isset($url['port'])) {
			if ($url['scheme'] == 'http') { $url['port']=80; }
			elseif ($url['scheme'] == 'https') { $url['port']=443; }
		}
		$url['query']=isset($url['query'])?$url['query']:'';
		
		$url['protocol']=$url['scheme'].'://';
		$eol="\r\n";
		
		$headers =  "POST ".$url['protocol'].$url['host'].$url['path']." HTTP/1.0".$eol.
			"Host: ".$url['host'].$eol.
			"Referer: ".$url['protocol'].$url['host'].$url['path'].$eol.
			"Content-Type: application/x-www-form-urlencoded".$eol.
			"Content-Length: ".strlen($url['query']).$eol.
			$eol.$url['query'];
		$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
		if($fp) 
		{
			fputs($fp, $headers);
			$result = '';
			while(!feof($fp)) { $result .= fgets($fp, 128); }
			fclose($fp);
			if (!$show_headers) 
			{
				//removes headers
				$match = preg_split("/\r\n\r\n/s",$result,2);
				$result = $match[1];
			}

			return $result;
		}
	}
	
	/** 
	 * Make an HTTP GET request using CURL
	 * 
	 * @param string $url
	 * @param variant $data querystring or array of field/value pairs
	 * @param bool true to require verification of SSL cert
	 * @return string
	 */
	function CurlGet($url, $data = "", $verify_cert = false) 
	{
		return HttpRequest::CurlRequest("GET",$url, $data, $verify_cert);
	}
	
	/** 
	 * Make an HTTP POST request using CURL
	 * 
	 * @param string $url
	 * @param variant $data querystring or array of field/value pairs
	 * @param bool true to require verification of SSL cert
	 * @return string
	 */
	function CurlPost($url, $data, $verify_cert = false) 
	{
		return HttpRequest::CurlRequest("POST",$url, $data, $verify_cert);
	}
	
	/** 
	 * Make an HTTP request using CURL
	 * 
	 * @param string "POST" or "GET"
	 * @param string $url
	 * @param variant $data querystring or array of field/value pairs
	 * @param bool true to require verification of SSL cert
	 * @return string
	 */
	function CurlRequest($method, $url, $data, $verify_cert = false) 
	{
		$qs = HttpRequest::ArrayToQueryString($data);

		$agent = "verysimple::HttpRequest";
		
		// $header[] = "Accept: text/vnd.wap.wml,*.*";    

		if ($method == "POST")
		{
			$ch = curl_init($url);
			curl_setopt($ch,		CURLOPT_POST, 1);
			curl_setopt($ch,		CURLOPT_POSTFIELDS, $qs);
		}
		else
		{
			$full_url = $url . ($qs ? "?" . $qs : "");
			$ch = curl_init($full_url);
		}
		
		curl_setopt($ch,		CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,		CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,		CURLOPT_VERBOSE, 0); ########### debug
		curl_setopt($ch,	    CURLOPT_USERAGENT, $agent);
		curl_setopt($ch,		CURLOPT_SSL_VERIFYPEER, $verify_cert);
		curl_setopt($ch,		CURLOPT_NOPROGRESS, 1);
		// curl_setopt($ch,	    CURLOPT_HTTPHEADER, $header);
		// curl_setopt($ch,		CURLOPT_COOKIEJAR, "curl_cookie");
		// curl_setopt($ch,		CURLOPT_COOKIEFILE, "curl_cookie");
		
		$tmp = curl_exec ($ch);
		$error = curl_error($ch);
		
		if ($error != "") {$tmp .= $error;}
		curl_close ($ch);
		
		return $tmp;
	}
	
	/**
	 * Converts an array into a URL querystring
	 * @param array key/value pairs
	 * @return string
	 */
	function ArrayToQueryString($arr)
	{
		$qs = $arr;
		
		if (is_array($arr))
		{
			// convert the data array into a url querystring
			$qs = "";
			$delim = "";
			foreach (array_keys($arr) as $key)
			{
				$qs .= $delim . $key ."=" . $arr[$key];
				$delim = "&";
			}
		}

		return $qs;
	}
}
?>