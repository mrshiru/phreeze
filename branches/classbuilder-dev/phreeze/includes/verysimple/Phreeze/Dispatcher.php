<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("verysimple/HTTP/Request.php");

/**
 * Dispatcher direct a web request to the correct controller & method
 *
 * @package    verysimple::Phreeze 
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.2
 */
class Dispatcher
{
	
	/**
	 * Processes user input and executes the specified controller method, ensuring
	 * that the controller dependencies are all injected properly
	 * 
	 * @param Phreezer $phreezer Object persistance engine
	 * @param Smarty $smarty rendering engine
	 * @param string $action the user requested action
	 * @param Context (optional) a context object for persisting the state of the current page
	 * @param UrlWriter (optional) a custom writer for URL formatting
	 */
	static function Dispatch($phreezer,$smarty,$action,$context=null,$urlwriter=null)
	{
		// get the action requested
		$params = explode(".", str_replace("/",".", $action) );
		$controller = isset($params[0]) && $params[0] ? $params[0] : "";
		$method = isset($params[1]) && $params[1] ? $params[1] : "";
		
		$cfile = "Controller/".$controller."Controller.php";
		
		if ( !(file_exists($cfile) || file_exists("libs/".$cfile)) )
		{
			throw new Exception("Controller/".$controller."Controller is not defined");
		}
		
		require_once($cfile);
		eval("\$controller = new ".$controller."Controller(\$phreezer,\$smarty,\$context,\$urlwriter);");
		
		$controller->$method();
		
		return true;
	}
}

?>