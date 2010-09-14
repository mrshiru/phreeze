<?php
// set the error reporting to the level desired
// error_reporting(E_ERROR); // E_ALL | E_ERROR | E_WARNING | E_NOTICE

// system paths
define("APP_ROOT", realpath("./"));
define("TEMPLATE_PATH", APP_ROOT . "/templates/");
define("COMPILE_PATH", APP_ROOT . "/templates_c/");
define("TEMP_PATH", APP_ROOT . "/temp/");

// make sure template folder is writable
if (!@fopen(COMPILE_PATH . "permission.test","w"))
{ldelim}
	die("<span style='color: red;'>ERROR: " . COMPILE_PATH . " must be writable</span>");
{rdelim}

// add local libs to include path (optionally use .htaccess or php.ini)
set_include_path(APP_ROOT . "/libs/" . PATH_SEPARATOR . get_include_path());

// require framework libs
require_once("verysimple/HTTP/UrlWriter.php");
require_once("verysimple/HTTP/Request.php");
require_once("verysimple/Phreeze/Phreezer.php");
require_once("verysimple/Phreeze/Dispatcher.php");
require_once("Smarty.class.php");

// it may be necessary to require your user account model files prior to session start
// require_once("Model/Account.php");
session_start();

///####################################################################################################
//# CONFIGURATION SETTINGS

// server timezone - see http://us2.php.net/manual/en/timezones.php
date_default_timezone_set("America/Chicago");

// database connection settings
$csetting = new ConnectionSetting();
$csetting->ConnectionString = "{$connection->Host}:{$connection->Port}";
$csetting->DBName = "{$connection->DBName}";
$csetting->Username = "{$connection->Username}";
$csetting->Password = "{$connection->Password}";

// default action
$default_action = "Default.ListAll";

// specify a custom context object if desired
$G_CONTEXT = null;

// specify a url writer object.  must have method: Get($controller,$method,$params)
$G_URLWRITER = new UrlWriter("index.php?action=%s.%s&%s");

// debugging info
$debug_mode = false;

// convert fatal PHP runtime errors to catchable exceptions
set_error_handler(array("Dispatcher", "HandleException"),error_reporting());

//#
///####################################################################################################

// instantiate the smarty template engine
$G_SMARTY = new Smarty();
$G_SMARTY->template_dir = TEMPLATE_PATH;
$G_SMARTY->compile_dir = COMPILE_PATH;
$G_SMARTY->config_dir = COMPILE_PATH;
$G_SMARTY->cache_dir = COMPILE_PATH;

// debugging configuration
$observer = null;
if ($debug_mode)
{ldelim}
	require_once("verysimple/Phreeze/ObserveToSmarty.php"); 
	$observer = new ObserveToSmarty($G_SMARTY);
{rdelim}

// instantiate the phreezer persistance api
$G_PHREEZER = null;

// instantiate the phreezer persistance api
try
{ldelim}
	$G_PHREEZER = new Phreezer($csetting, $observer);
{rdelim}
catch (Exception $ex)
{ldelim}
	// we have a database connectivity error
	$G_SMARTY->assign('message',$ex->getMessage());
	$G_SMARTY->display('_error.tpl');
	die();
{rdelim}

/* Fetching Strategy Configuration
 * You may uncomment any of the lines below to specify always eager fetching.
 * Alternatively, you can copy/paste to a specific page for one-time eager fetching
 * If you paste into a controller method, replace $G_PHREEZER with $this->Phreezer
 */
{foreach from=$tables item=tbl}
{foreach from=$tbl->Constraints item=constraint}// $G_PHREEZER->SetLoadType("{$tbl->Name|studlycaps}","{$constraint->Name}",KM_LOAD_EAGER);
{/foreach}
{/foreach}


/**
 * Formats the debug_backtrace array into a printable string
 *
 * @access     public
 * @param array  debug_backtrace array
 * @param string $join the string used to join the array
 * @return string
 */
function FormatTrace($tb, $join = " :: ", $show_lines = false)
{ldelim}
	$msg = "";
	$delim = "";

	$calling_function = "";
	$calling_line = "[?]";
	for ($x = count($tb); $x > 0; $x--)
	{ldelim}
		$stack = $tb[$x-1];
		$s_file = isset($stack['file']) ? basename($stack['file']) : "[?]";
		$s_line = isset($stack['line']) ? $stack['line'] : "[?]";
		$s_function = isset($stack['function']) ? $stack['function'] : "";
		$s_class = isset($stack['class']) ? $stack['class'] : "";
		$s_type = isset($stack['type']) ? $stack['type'] : "";

		$msg .= $delim . "$calling_function" . ($show_lines ? " ($s_file Line $s_line)" : "");
		$calling_function = $s_class . $s_type . $s_function;
		
		$delim = $join;
	{rdelim}
	
	return $msg;
{rdelim}

?>