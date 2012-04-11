<?php
/** @package {$connection->DBName|studlycaps}::Controller */

/** import supporting libraries */
require_once("{$appname}BaseController.php");

/**
 * DefaultController is the entry point to the application
 *
 * @package {$connection->DBName|studlycaps}::Controller
 * @author ClassBuilder
 * @version 1.0
 */
class DefaultController extends {$appname}BaseController
{ldelim}

	/**
	 * Override here for any controller-specific functionality
	 */
	protected function Init()
	{ldelim}
		parent::Init();

		// TODO: add controller-wide bootstrap code
	{rdelim}

	/**
	 * Default Action is the method that is called if the controller
	 * action is specified without a method name.
	 */
	public function DefaultAction()
	{ldelim}
		$this->Render();
	{rdelim}

{rdelim}
?>