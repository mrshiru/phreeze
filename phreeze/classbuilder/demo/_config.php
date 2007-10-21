<?php
// set the error reporting to the level desired
// error_reporting(E_ERROR); // E_ALL | E_ERROR | E_WARNING | E_NOTICE

// uncomment if your libs folder is not configured in php.ini or through .htaccess
ini_set("include_path", "./libs/" . (strstr( PHP_OS , "WIN") ? ";" : ":") . ini_get('include_path') );

// require framework libs
require_once("verysimple/Phreeze/Phreezer.php");
require_once("verysimple/Phreeze/Dispatcher.php");
require_once("Smarty.class.php");

// it may be necessary to require your user account model files prior to session start
// require_once("Model/Account.php");
session_start();

///####################################################################################################
//# CONFIGURATION SETTINGS

// database connection settings
$csetting = new ConnectionSetting();
$csetting->ConnectionString = "localhost:3306";
$csetting->DBName = "condo";
$csetting->Username = "root";
$csetting->Password = "s0urc3";

// default action:
$default_action = "Default/ListAll";

// debugging configuration
$observer = null;
// uncomment below to show Phreeze debug output to browser
// require_once("verysimple/Phreeze/ObserveToBrowser.php"); $observer = new ObserveToBrowser()

//#
///####################################################################################################
?>