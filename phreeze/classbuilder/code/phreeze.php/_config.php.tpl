{literal}<?php
/** @package    {/literal}{$connection->DBName|studlycaps}{literal} */

/** configure the root, system and include paths */
if (!defined("APP_ROOT")) define("APP_ROOT", realpath("./"));
define("TEMPLATE_PATH", APP_ROOT . "/templates/");
define("COMPILE_PATH", APP_ROOT . "/templates_c/");
define("TEMP_PATH", APP_ROOT . "/temp/");
define("UPLOAD_PATH", APP_ROOT . "/uploads/");

/* define web paths **/
define("EXT_SCRIPT_PATH", "/scripts/ext-3.3.1/");

/* ensure the framework libraries can be located */
set_include_path(
	APP_ROOT . "/libs/" . PATH_SEPARATOR 
	. APP_ROOT . "/../includes/" . PATH_SEPARATOR
	. get_include_path()
);

/** require framework libs */
require_once("verysimple/HTTP/UrlWriter.php");
require_once("verysimple/HTTP/Request.php");
require_once("verysimple/Phreeze/Phreezer.php");
require_once("verysimple/Phreeze/Dispatcher.php");
require_once("verysimple/Util/ExceptionFormatter.php");
require_once("verysimple/Phreeze/SmartyRenderEngine.php");


// load user's database settings
$csetting = new ConnectionSetting();
include_once("_connection.php");

/**
 * GlobalConfig contains the global variable.  This is implented 
 * as a singleton for easier unit testing.
 * 
 * @package {/literal}{$connection->DBName|studlycaps}{literal}
 * @author ClassBuilder
 */
class GlobalConfig
{

	public static $default_action = "Default.DefaultAction";
	public static $url_format = "index.php?action=%s.%s{delim}%s";
	public static $debug_mode = false;
	
	private static $instance;
	private static $csetting;
	private static $level_2_cache;
	private static $is_initialized = false;
	
	private $context;
	private $urlwriter;
	private $phreezer;
	private $render_engine;
	private $created;

	/** prevents external construction */
	private function __construct(){}
	
	/** prevents external cloning */
	private function __clone() {}
	
	/**
	 * Initialize the GlobalConfig option, injecting any settings that
	 * are required
	 * @param ConnectionSetting $csetting
	 * @param unknown_type $level_2_cache
	 */
	static function Init(ConnectionSetting $csetting, $level_2_cache = null)
	{
		if (!self::$is_initialized)
		{
			self::$csetting = $csetting;
			self::$level_2_cache = $level_2_cache;
			self::$is_initialized = true;
		}
	}
	
	/**
	 * Returns an instance of the GlobalConfig singleton
	 * @return GlobalConfig
	 */
	static function GetInstance()
	{
		if (!self::$instance instanceof self) { 
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * @return Context
	 */
	function GetContext()
	{	
		if ($this->context == null)
		{
		}
		return $this->context;

	}
	
	/**
	 * @return UrlWriter
	 */
	function GetUrlWriter()
	{
		if ($this->urlwriter == null)
		{
			$this->urlwriter = new UrlWriter(self::$url_format);
		}
		return $this->urlwriter;
	}
	
	/**
	 * @return string
	 */
	function GetDefaultAction()
	{
		return self::$default_action;
	}
	
	/**
	 * @return Phreezer
	 */
	function GetPhreezer()
	{
		if (!self::$is_initialized) throw new Exception("GlobalConfig::Init() must be called before GetPhreezer()");
		
		$cacheTimeout = 15;
		
		if ($this->phreezer == null)
		{
			$observer = null;
			
			if (self::$debug_mode)
			{
				require_once("verysimple/Phreeze/ObserveToSmarty.php"); 
				$observer = new ObserveToSmarty($this->GetRenderEngine());
				//require_once("verysimple/Phreeze/ObserveToBrowser.php"); 
				//$observer = new ObserveToBrowser();
			}
			
			// instantiate the phreezer persistance api (observer is defined in _config.connection.php)
			$this->phreezer = new Phreezer(self::$csetting, $observer);

			if (self::$level_2_cache)
			{
				$this->phreezer->SetLevel2CacheProvider( self::$level_2_cache, TEMP_PATH );
			}
			
			// ($cacheTimeout may be overridden in _config.connection.php)
			$this->phreezer->ValueCacheTimeout = $cacheTimeout;
		}
		
		
		return $this->phreezer;
	}

	/**
	 * @return IRenderEngine
	 */
	function GetRenderEngine()
	{

		if ($this->render_engine == null)
		{
			$this->render_engine = new SmartyRenderEngine();
			$this->render_engine->template_dir = TEMPLATE_PATH;
			$this->render_engine->compile_dir = COMPILE_PATH;
			$this->render_engine->config_dir = COMPILE_PATH;
			$this->render_engine->cache_dir = COMPILE_PATH;

			$this->render_engine->assign("UPLOAD_PATH",UPLOAD_PATH);	
			$this->render_engine->assign("TEMP_PATH",TEMP_PATH);
			$this->render_engine->assign("EXT_SCRIPT_PATH",EXT_SCRIPT_PATH);	

			if (defined('ERROR_REPORTING_BASE_URL'))
			{
				$this->render_engine->assign("ERROR_REPORTING_BASE_URL",ERROR_REPORTING_BASE_URL);
			}
		}
		
		return $this->render_engine;

	}

	/**
	 * Formats the debug_backtrace array into a printable string
	 *
	 * @access     public
	 * @param array  debug_backtrace array
	 * @param string $join the string used to join the array
	 * @return string
	 */
	static function FormatTrace($tb, $join = " :: ", $show_lines = false)
	{
		return ExceptionFormatter::FormatTrace($tb,0,$join,$show_lines);
	}
}

GlobalConfig::Init($csetting);

if (file_exists("_config_overrides.php")) include_once("_config_overrides.php");

?>{/literal}