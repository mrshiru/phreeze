<?php
require_once("verysimple/DB/Reflection/DBConnectionString.php");

session_start();

$connstr = new DBConnectionString();

$connstr->Host = $_REQUEST["host"];
$connstr->Port = $_REQUEST["port"];
$connstr->Username = $_REQUEST["username"];
$connstr->Password = $_REQUEST["password"];
$connstr->DBName = $_REQUEST["dbname"];

// persist to the session
$_SESSION["connstr"] = $connstr;

$NO_SESSION_START = 1;
require_once("_global.php");

$G_SMARTY->assign("redirect","index.php");
$G_SMARTY->display("redirect.tpl")

?>