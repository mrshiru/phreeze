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
	 * @param string $templatePath
	 * @param string $compilePath (not used for this render engine)
	 */
	function __construct($templatePath = '',$compilePath = '')
	{
		$this->savant = new Savant3(array('exceptions'=>true));

		// normalize the path
		if (substr($templatePath,-1) != '/' && substr($templatePath,-1) != '\\') $templatePath .= "/";

		if ($templatePath) $this->savant->setPath('template',$templatePath);
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