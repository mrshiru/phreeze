<?php
/** @package    verysimple::Phreeze */

require_once("IRenderEngine.php");

/**
 * PHPRenderEngine is an implementation of IRenderEngine
 * that uses PHP as the template language
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc.
 * @copyright  1997-2010 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */
class PHPRenderEngine implements IRenderEngine
{
	/** the file path to the template director */
	public $tempatePath;

	/** if this is true, all views will have .php appended the last 4 chars are not .php */
	public $verifyExtension = true;

	/** stores the assigned vars */
	public $model = Array();

	/**
	 * Constructor
	 * @param string $templatePath full path to template directory
	 */
	function __construct($path)
	{
		$this->templatePath = $path;

		if (substr($path,-1) != '/' && substr($path,-1) != '\\') $this->templatePath .= "/";
	}

	/**
	 * @inheritdoc
	 */
	public function assign($key,$value)
	{
		$this->model[$key] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function display($template)
	{
		// these two are special templates used by the Phreeze controller and dispatcher
		if ($template == "_redirect.tpl")
		{
			header("Location: " . $this->model['url']);
			die();
		}
		elseif ($template == "_error.tpl")
		{
			die("<h4>" . $this->model['message'] . "</h4>" . $this->model['stacktrace']);
		}
		else
		{
			if ($this->verifyExtension && substr($template,-4) != '.php') $template .= ".php";

			$path = $this->templatePath . $template;

			if (!is_readable($path))
			{
				throw new Exception("The template file '" . htmlspecialchars($path) . "' was not found.");
			}

			// make this available at the scope of the included file
			$engine = $this;
			$model = $this->model;
			include_once($path);
		}
	}

	/**
	 * Returns the specified model value
	 */
	public function get($key)
	{
		return $this->model[$key];
	}

	/**
	 * @inheritdoc
	 */
	public function fetch($template)
	{
		ob_start();

		$this->display($template);
		$buffer = ob_get_contents();

		ob_end_clean();

		return $buffer;
	}

}

?>