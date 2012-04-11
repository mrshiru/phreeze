<?php
/** @package    {$connection->DBName|studlycaps}::Controller */
 
/** import supporting libraries */
require_once("verysimple/Phreeze/Controller.php");

/**
 * {$appname}BaseController is a base class Controller class from which
 * the front controllers inherit.  it is not necessary to use this 
 * class or any code, however you may use if for application-wide
 * functions such as authentication
 *
 * @package {$connection->DBName|studlycaps}::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class {$appname}BaseController extends Controller
{ldelim}

	/**
	 * Init is called by the base controller before the action method
	 * is called.  This provided an oportunity to hook into the system
	 * for all application actions.  This is a good place for authentication
	 * code.
	 */
	protected function Init()
	{ldelim}
		// TODO: add app-wide bootsrap code
	{rdelim}

{rdelim}
?>