<?php
/** @package    verysimple::Util */

/**
 * Utility for catching PHP errors and converting them to an exception
 * that can be caught at runtime
 * 
 * @example <pre>
 * try
 * {
 *   ExceptionThrower::Start();
 *   // @TODO PHP command here
 *   ExceptionThrower::Stop();
 * }
 * catch (Exception $ex)
 * {
 *   ExceptionThrower::Stop();
 *   // handle or re-throw exception
 * }</pre>
 * @package    verysimple::Util
 * @author Jason Hinkle
 * @copyright  1997-2008 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class ExceptionThrower
{
	/**
	 * Start redirecting PHP errors
	 * @param int $level the level(s) of PHP error to catch (ex: E_WARNING | E_NOTICE)
	 */
	static function Start($level = E_ALL)
	{
		set_error_handler(array("ExceptionThrower", "HandleError"), $level);
	}
	
	/**
	 * Stop redirecting PHP errors
	 */
	static function Stop()
	{
		restore_error_handler();
	}
	
	/**
	 * Fired by the PHP error handler function.  Calling this function will
	 * always throw an exception unless error_reporting == 0.  If the
	 * PHP command is called with @ preceeding it, then it will be ignored
	 * here as well.
	 * 
	 * @param string $code
	 * @param string $string
	 * @param string $file
	 * @param string $line
	 * @param string $context
	 */
	static function HandleError($code, $string, $file, $line, $context)
	{
		if (error_reporting() == 0) return;
		throw new Exception($string,$code);
	}
}