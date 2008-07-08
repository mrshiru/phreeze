<?php
/** @package    verysimple::IO */

/** import supporting libraries */
require_once("IncludeException.php");

/**
 * Provides helper functions for including classes and files dynamically
 * so that Exceptions are thrown instead of PHP errors and warnings
 *
 * @package    verysimple::IO
 * @author Jason Hinkle
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class Includer
{

	/**
	 * Includes a file with the given path.  If PHP is unable to include the file,
	 * an IncludeException is thrown instead of a PHP warning
	 * @param string path to file passed to the include_once statement
	 */
	public static function IncludeFile($path)
	{
		// re-route error handling temporarily so we can catch errors
		set_error_handler(array("Includer", "IncludeException"),E_ALL);
		
		// use include instead of require so we can catch runtime exceptions
		include_once($path);
		
		// reset error handling back to whatever it was
		restore_error_handler();
	}

	/**
	 * Ensures that a class is defined.  If not, attempts to include the file
	 * using the provided path. If unable to locate the class, an IncludeException
	 * will be thrown.  The path that will be used for include_once is
	 * $classpath . "/" . $classname . ".php"
	 * 
	 * @param string name of class (ex Phreeze)
	 * @param string or array [optional] the relative path(s) where the file would be found
	 */
	public static function RequireClass($classname, $classpath = "")
	{
		if (class_exists($classname)) return true;
				
		if (is_array($classpath))
		{
			$classpaths = $classpath;
		}
		else
		{
			$classpaths = array($classpath);
		}
		
		foreach ($classpaths as $path)
		{
			if (class_exists($classname)) break;

			try
			{
				// append a directory separater if necessary
				if ($path && substr($path,-1) != "/") $path .= "/";
				Includer::IncludeFile($path . $classname . ".php");
			}
			catch (IncludeException $ex) {}

		}
		
		if (!class_exists($classname))
		{
			// the class still isn't defined so there was a problem including the model
			throw new IncludeException("Unable to locate class '$classname'");
		}
	}	
	
	/**
	* Handler for catching file-not-found errors and throwing an IncludeException
	*/
	public static function IncludeException($code, $string, $file, $line, $context)
	{
		$tmp1 = explode(")",$string);
		$tmp2 = explode("(",$tmp1[0]);
		$mfile = $tmp2[1];
		
		$msg = $string ? "Unable to include file: '" . $mfile . "'" : $string;
		
		throw new IncludeException($msg,$code);
	}
	
}

?>