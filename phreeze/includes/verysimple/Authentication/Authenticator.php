<?php
/** @package    verysimple::Authentication */

/** import supporting libraries */
require_once("IAuthenticatable.php");
require_once("AuthenticationException.php");

/**
 * Authenticator is a collection of static methods for storing a current user
 * in the session and determining if the user has necessary permissions to 
 * perform an action
 * @package    verysimple::Authentication
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class Authenticator
{
	static $user = null;
	
	/**
	 * Returns the currently authenticated user or null
	 *
	 * @access public
	 * @return IAuthenticatable || null
	 */
	public static function GetCurrentUser($guid = "CURRENT_USER")
	{
		if (Authenticator::$user == null)
		{
			if (isset($_SESSION[$guid]))
			{
				Authenticator::$user = unserialize($_SESSION[$guid]);
			}		
		}
		return Authenticator::$user;
	}

	
	/**
	 * Set the given IAuthenticable object as the currently authenticated user.
	 * UnsetAllSessionVars will be called before setting the current user
	 *
	 * @param IAuthenticatable $user
	 * @param mixed $guid a unique id for this session
	 *
	 */
	public static function SetCurrentUser(IAuthenticatable $user, $guid = "CURRENT_USER")
	{
		Authenticator::UnsetAllSessionVars();
		Authenticator::$user = $user;
		$_SESSION[$guid] = serialize($user);
	}

	
	/**
	 * Unsets all session variables without destroying the session
	 *
	 */
	public static function UnsetAllSessionVars()
	{
		foreach (array_keys($_SESSION) as $key)
		{
			unset($_SESSION[$key]);
		}
	}
	
	/**
	 * Forcibly clear all _SESSION variables and destroys the session
	 *
	 * @param string $guid The GUID of this user
	 */
	public static function ClearAuthentication($guid = "CURRENT_USER")
	{
		Authenticator::$user = null;
		unset($_SESSION[$guid]);
		
		Authenticator::UnsetAllSessionVars();
		
		@session_destroy();
	}

}

?>