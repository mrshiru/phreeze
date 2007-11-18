<?php

// laplix 2007-11-02
// Phreeze SVN now has these 3 directories: classbuilder, includes and scripts.
// For dev purposes, it's easier to keep te includes dir under WEB_ROOT/phreeze
// with classbuilder and scripts. So we include the path here.
// FOR PRODUCTION, YOU SHOULD PUT THE includes DIRECTORY **OUTSIDE** YOU WEB ROOT
// AND SETUP THIS LINE TO POINT TO YOUR PRODUCTION includes DIR 
// You can also use your php.ini or .htaccess file to do that
set_include_path(dirname(__FILE__) . '/../includes' . PATH_SEPARATOR . get_include_path());

require_once("verysimple/DB/Reflection/DBConnectionString.php");
require_once('CBProperties.php');

session_start();

$connstr = new DBConnectionString();

$connstr->Host = $_REQUEST["host"];
$connstr->Port = $_REQUEST["port"];
$connstr->Username = $_REQUEST["username"];
$connstr->Password = $_REQUEST["password"];
$connstr->DBName = $_REQUEST["dbname"];

// persist to the session
$_SESSION["connstr"] = $connstr;

// save $connstr for later sessions
$cbp = new CBProperties;
$cbp->putSection('Connection', get_object_vars($connstr));
$cbp->iniWrite();

$NO_SESSION_START = 1;
require_once("_global.php");

$G_SMARTY->assign("redirect","show_tables.php");
$G_SMARTY->display("redirect.tpl")

?>
