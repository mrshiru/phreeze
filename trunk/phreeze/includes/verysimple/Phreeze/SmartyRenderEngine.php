<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
require_once("Smarty.class.php");
require_once("verysimple/Phreeze/IRenderEngine.php");

/**
 * SmartyRenderEngine is an implementation of IRenderEngine that uses
 * the Smarty template engine to render views
 *
 * @package    verysimple::Phreeze 
 * @author     VerySimple Inc.
 * @copyright  1997-2011 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class SmartyRenderEngine extends Smarty implements IRenderEngine
{
	public function SmartyRenderEngine()
	{
		parent::Smarty();
	}
}

?>