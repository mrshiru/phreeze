<?php

///####################################################################################################
//# CONFIGURATION SETTINGS

// system paths
define("APP_ROOT", realpath("./"));
define("TEMPLATE_PATH", APP_ROOT . "/templates/");
define("TEMP_PATH", APP_ROOT . "/templates_c/");
define("CODE_PATH", APP_ROOT . "/code/");

// add local libs to include path (optionally use .htaccess or php.ini)
set_include_path(APP_ROOT . "/libs/" . PATH_SEPARATOR . get_include_path());

// laplix 2007-11-02s
// Phreeze SVN now has these 3 directories: classbuilder, includes and scripts.
// For dev purposes, it's easier to keep te includes dir under WEB_ROOT/phreeze
// with classbuilder and scripts. So we include the path here.
// FOR PRODUCTION, YOU SHOULD PUT THE includes DIRECTORY **OUTSIDE** YOU WEB ROOT
// AND SETUP THIS LINE TO POINT TO YOUR PRODUCTION includes DIR 
// You can also use your php.ini or .htaccess file to do that
set_include_path(APP_ROOT . "../includes/" . PATH_SEPARATOR . get_include_path());

error_reporting(E_ALL); // E_ALL | E_ERROR | E_WARNING | E_NOTICE

//#
///####################################################################################################

require_once("Smarty.class.php");
require_once("verysimple/DB/Reflection/DBServer.php");

if (empty($NO_SESSION_START))
{
	session_start();
}

$G_SMARTY = new Smarty();

$G_SMARTY->template_dir = TEMPLATE_PATH;
$G_SMARTY->compile_dir = TEMP_PATH;
$G_SMARTY->config_dir = TEMP_PATH;
$G_SMARTY->cache_dir = TEMP_PATH;
	
// see if the connection information has been specifed, otherwise we can't continue
// we need to redirect the user to the set_connection page
if ( !array_key_exists("connstr",$_SESSION) )
{
	// force the user to specify a connection string
	$G_SMARTY->display("new_connection.tpl");
	exit();
}
else
{
	// create the database access objects
	$G_CONNSTR = $_SESSION["connstr"];
	$G_HANDLER = new DBEventHandler(); // set the LogLevel at constructor if desired 
	$G_CONNECTION = new DBConnection($G_CONNSTR, $G_HANDLER);
	$G_SERVER = new DBServer($G_CONNECTION);
}


?>
