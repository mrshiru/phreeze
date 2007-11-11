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
	/** Make an HTTP post using CURL
	*/
	function CurlPost ($url, $data, $verify_cert = false) 
	{
		// convert the data array into a url querystring
		$post_data = "";
		$delim = "";
		foreach (array_keys($data) as $key)
		{
			$post_data .= $delim . $key ."=" . $data[$key];
			$delim = "&";
		}

		$agent = "curl_post.1";
		// $header[] = "Accept: text/vnd.wap.wml,*.*";    
		$ch = curl_init($url);
		
		curl_setopt($ch,		CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,		CURLOPT_VERBOSE, 0); ########### debug
		curl_setopt($ch,	    CURLOPT_USERAGENT, $agent);
		//curl_setopt($ch,	    CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch,		CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch,		CURLOPT_COOKIEJAR, "cook");
		curl_setopt($ch,		CURLOPT_COOKIEFILE, "cook");
		curl_setopt($ch,		CURLOPT_POST, 1);
		curl_setopt($ch,		CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch,		CURLOPT_SSL_VERIFYPEER, $verify_cert);
		curl_setopt($ch,		CURLOPT_NOPROGRESS, 1);
		
		$tmp = curl_exec ($ch);
		$error = curl_error($ch);
		
		if ($error != "") {$tmp .= $error;}
		curl_close ($ch);
		
		return $tmp;
	}
}
?>