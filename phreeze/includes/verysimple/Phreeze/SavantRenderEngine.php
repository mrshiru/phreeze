<?php
/** @package    verysimple::Phreeze */

require_once("IRenderEngine.php");
require_once('savant/Savant3.php');

/**
 * Implementation of IRenderEngine that uses Savant as the template language
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc.
 * @copyright  1997-2010 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class SavantRenderEngine implements IRenderEngine
{

	static $TEMPLATE_EXTENSION = ".tpl.php";
	private $savant;

	/**
	 * Constructor
	 * @param string $path full path to template directory
	 */
	function __construct($path)
	{
		$this->savant = new Savant3();

		// normalize the path
		if (substr($path,-1) != '/' && substr($path,-1) != '\\') $path .= "/";

		if ($path) $this->savant->setPath('template',$path);
	}

	/**
	 * @inheritdoc
	 */
	public function assign($key,$value)
	{
		$this->savant->$key = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function display($template)
	{
		// these two are special templates used by the Phreeze controller and dispatcher
		if ($template == "_redirect.tpl")
		{
			header("Location: " . $this->savant->url);
			die();
		}
		elseif ($template == "_error.tpl")
		{
			$this->savant->display('_error' . $TEMPLATE_EXTENSION);
			// die("<h4>" . $this->savant->message . "</h4>" . $this->savant->stacktrace);
		}
		else
		{
			$this->savant->display($template . self::$TEMPLATE_EXTENSION);
		}
	}

	/**
	 * Returns the specified model value
	 */
	public function get($key)
	{
		return $this->savant->$key;
	}

	/**
	 * @inheritdoc
	 */
	public function fetch($template)
	{
		return $this->savant->fetch($template . self::$TEMPLATE_EXTENSION);
	}

}

?>