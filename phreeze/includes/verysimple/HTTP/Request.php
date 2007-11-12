<?php
/** @package    verysimple::HTTP */

/** import supporting libraries */
require_once("FileUpload.php");

/**
 * Static utility class for processing form post/request data
 *
 * Contains various methods for retrieving user input from forms
 *
 * @package    verysimple::HTTP 
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc. http://www.verysimple.com
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class Request
{

	/** In the case of URL re-writing, sometimes querystrings appended to a URL can get
	 * lost.  This function examines the original request URI and updates $_REQUEST
	 * superglobal to ensure that it contains all of values in the qeurtystring
	 *
	 */
	public static function NormalizeUrlRewrite()
	{
		$uri = array();
		if (isset($_SERVER["REQUEST_URI"]))
		{
			$uri = parse_url($_SERVER["REQUEST_URI"]);
		}
		elseif (isset($_SERVER["QUERY_STRING"]))
		{
			$uri['query'] = $_SERVER["QUERY_STRING"];
		}
		
		if (isset($uri['query']))
		{
			$parts = explode("&",$uri['query']);
			foreach ($parts as $part)
			{
				$keyval = explode("=",$part,2);
				$_REQUEST[$keyval[0]] = isset($keyval[1]) ? urldecode($keyval[1]) : "";
			}
		}
	}
	
	/** Returns the full URL of the PHP page that is currently executing
	 *
	 * @param bool $include_querystring (optional) Specify true/false to include querystring. Default is true.
	 * @return string URL
	 */
	public static function GetCurrentURL($include_querystring = true)
	{
		$protocol = substr($_SERVER["SERVER_PROTOCOL"], 0, strpos($_SERVER["SERVER_PROTOCOL"], "/")) 
			. ($_SERVER["HTTPS"] == "on" ? "S" : "");
		$domain = $_SERVER['HTTP_HOST'];
		$port = ($_SERVER["SERVER_PORT"] == "80" || $_SERVER["SERVER_PORT"] == "443") ? "" : (":" . $_SERVER["SERVER_PORT"]); 
		
		if (isset($_SERVER['REQUEST_URI']))
		{
			// REQUEST_URI is more accurate but isn't always defined on windows
			// in particular for the format http://www.domain.com/?var=val
			$pq = explode("?",$_SERVER['REQUEST_URI']);
			$path = $pq[0];
			$qs = isset($pq[1]) ? "?" . $pq[1] : "";
		}
		else
		{
			// otherwise use SCRIPT_NAME & QUERY_STRING
			$path = $_SERVER['SCRIPT_NAME'];
			$qs = isset($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "";
		}
		
		return strtolower($protocol) . "://" . $domain . $path . $port . ($include_querystring ? $qs : "");
	}
	
	
	
	/**
	* Returns a form upload as a FileUpload object.  This function throws an exeption on fail
	* with details, so it is recommended to use try/catch when calling this function
	*
	* @param    string $fieldname name of the html form field
	* @param    bool $b64encode true to base64encode file data (default false)
	* @param    bool $ignore_empty true to not throw exception if form fields doesn't contain a file (default false)
	* @param    int $max_kb maximum size allowed for upload (default unlimited)
	* @param    array $ok_types if array is provided, only files with those Extensions will be allowed (default all)
	* @return   FileUpload object
	*/
	public static function GetFileUpload($fieldname, $ignore_empty = false, $max_kb = 0, $ok_types = null)
	{
		// make sure there is actually a file upload
		if (!isset($_FILES[$fieldname]))
		{
			// this means the form field wasn't present which is generally an error
			// however if ignore is specified, then return empty string
			if ($ignore_empty)
			{
				return "";
			}
			throw new Exception("\$_FILES['".$fieldname."'] is empty.  Did you forget to add enctype='multipart/form-data' to your form code?");
		}
		
		// make sure a file was actually uploaded, otherwise return null
		if($_FILES[$fieldname]['error'] == 4)
		{
			return;
		}
		
		// get the upload ref	
		$upload = $_FILES[$fieldname];
		
		// make sure there were no errors during upload, but ignore case where
		if ($upload['error'])
		{
			$error_codes[0] = "The file uploaded with success."; 
			$error_codes[1] = "The uploaded file exceeds the upload_max_filesize directive in php.ini."; 
			$error_codes[2] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form."; 
			$error_codes[3] = "The uploaded file was only partially uploaded."; 
			$error_codes[4] = "No file was uploaded."; 
			throw new Exception("Error uploading file: " . $error_codes[$upload['error']]);
		}
		
		// make sure this is a legit file request
		if (!is_uploaded_file($upload['tmp_name']))
		{
			throw new Exception("Unable to access this upload: " . $fieldname);
		}
		
		
		// get the filename and Extension
		$tmp_path = $upload['tmp_name'];
		$info = pathinfo($upload['name']);
		
		$fupload = new FileUpload();
		$fupload->Name = $info['basename'];
		$fupload->Size = $upload['size'];
		$fupload->Type = $upload['type'];
		$fupload->Extension = strtolower($info['extension']);
		
		
		if ($ok_types && !in_array($fupload->Extension, $ok_types) )
		{
			throw new Exception("The file '".htmlentities($fupload->Name)."' is not a type that is allowed.  Allowed file types are: " . (implode(", ",$ok_types)) . ".");
		}
		
		if ($max_kb && ($fupload->Size/1024) > $max_kb)
		{
			throw new Exception("The file '".htmlentities($fupload->Name)."' is to large.  Maximum allowed size is " . number_format($max_kb/1024,2) . "Mb");
		}
		
		// open the file and read the entire contents
		$fh = fopen($tmp_path,"r");
		$fupload->Data = fread($fh, filesize($tmp_path));
		fclose($fh);
		
		return $fupload;
	}
	
	/**
	 * Returns a form upload as an xml document with the file data base64 encoded.
	 * suitable for storing in a clob or blob
	 *
	* @param    string $fieldname name of the html form field
	* @param    bool $b64encode true to base64encode file data (default true)
	* @param    bool $ignore_empty true to not throw exception if form fields doesn't contain a file (default false)
	* @param    int $max_kb maximum size allowed for upload (default unlimited)
	* @param    array $ok_types if array is provided, only files with those Extensions will be allowed (default all)
	* @return   string or null
	 */
	public static function GetFile($fieldname, $b64encode = true, $ignore_empty = false, $max_kb = 0, $ok_types = null)
	{
		$fupload = Request::GetFileUpload($fieldname, $ignore_empty, $max_kb, $ok_types);
		return ($fupload) ? $fupload->ToXML($b64encode) : null;
	}
	
	
	/**
	* Returns a form parameter as a string, handles null values
	*
	* @param    string $fieldname
	* @param    string $default value returned if $_REQUEST[$fieldname] is blank or null (default = empty string)
	* @param    bool $escape if true htmlspecialchars($val) is returned (default = false)
	* @return   string
	*/
	public static function Get($fieldname,$default = "",$escape = false)
	{
		$val = (isset($_REQUEST[$fieldname]) && $_REQUEST[$fieldname] != "") ? $_REQUEST[$fieldname] : $default;
		return $escape ? htmlspecialchars($val) : $val;
	}
	
	/**
	* Returns a form parameter and persists it in the session.  If the form parameter was not passed
	* again, then it returns the session value.  if the session value doesn't exist, then it returns
	* the default setting
	*
	* @param    string $fieldname
	* @param    string $default
	* @return   string
	*/
	public static function GetPersisted($fieldname, $default = "",$escape = false)
	{
		if ( isset($_REQUEST[$fieldname]) )
		{
			$_SESSION["_PERSISTED_".$fieldname] = ($_REQUEST[$fieldname] != "") ? $_REQUEST[$fieldname] : $default;
		}
		
		if ( isset($_SESSION["_PERSISTED_".$fieldname]) )
		{
			return $escape ? htmlspecialchars($_SESSION["_PERSISTED_".$fieldname]) : $_SESSION["_PERSISTED_".$fieldname];
		}
		
		return $default;
	}
	
	/**
	* Returns a form parameter as a date formatted for mysql YYYY-MM-DD, 
	* expects some type of date format.  if default value is not provided,
	* will return today.  if default value is empty string "" will return
	* empty string.
	*
	* @param    string $fieldname
	* @param    string $default default value = today
	* @param    bool $includetime whether to include the time in addition to date
	* @return   string
	*/
	public static function GetAsDate($fieldname, $default = "date('Y-m-d')", $includetime = false)
	{
		$returnVal = Request::Get($fieldname,$default);
		
		if ($returnVal == "date('Y-m-d')")
		{
			return date('Y-m-d');
		}
		elseif ($returnVal == "date('Y-m-d H:i:s')")
		{
			return date('Y-m-d H:i:s');
		}
		elseif ($returnVal == "")
		{
			return "";
		}
		else
		{
			if ($includetime)
			{
				if (Request::Get($fieldname."Hour"))
				{
					$hour = Request::Get($fieldname."Hour",date("H"));
					$minute = Request::Get($fieldname."Minute",date("i"));
					$ampm = Request::Get($fieldname."AMPM","AM");
					
					if ($ampm == "PM")
					{
						$hour = ($hour*1)+12;
					}
					$returnVal .= " " . $hour . ":" . $minute . ":" . "00";
				}

				return date("Y-m-d H:i:s",strtotime($returnVal));
			}
			else
			{
				return date("Y-m-d",strtotime($returnVal));
			}
		}
	}
	
	/**
	* Returns a form parameter as a date formatted for mysql YYYY-MM-DD HH:MM:SS, 
	* expects some type of date format.  if default value is not provided,
	* will return now.  if default value is empty string "" will return
	* empty string.
	*
	* @param    string $fieldname
	* @param    string $default default value = today
	* @return   string
	*/
	public static function GetAsDateTime($fieldname, $default = "date('Y-m-d H:i:s')")
	{
		return Request::GetAsDate($fieldname,$default,true);
	}
	
	/**
	 * Returns a form parameter minus currency symbols
	 *
	 * @param	string	$fieldname
	 * @return	string
	 */
	public static function GetCurrency($fieldname)
	{
		return str_replace(array(',','$'),'',Request::Get($fieldname));	
	}
	
	
}

?>