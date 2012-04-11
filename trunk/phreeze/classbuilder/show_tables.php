<?php

require_once("_global.php");
require_once("verysimple/IO/FolderHelper.php");
require_once("libs/AppConfig.php");

// load up the available packages (based on files: code/*.config)
$folder = new FolderHelper(CODE_PATH);
$files = $folder->GetFiles('/config/');
$packages = Array();

foreach ($files as $fileHelper)
{
	$packages[] = new AppConfig($fileHelper->Path);
}

// read and parse the database structure
try
{
	$schema = new DBSchema($G_SERVER);
}
catch (exception $ex)
{
	$G_SMARTY->assign("error",$ex->getmessage());
	$G_SMARTY->display("new_connection.tpl");
	exit();
}
	
// initialize parameters that will be passed on to the code templates
$params = array();
$params[] = new AppParameter('PathToVerySimpleScripts', '/scripts/verysimple/');
$params[] = new AppParameter('PathToExtScripts', '/scripts/ext-2/');
$params[] = new AppParameter('AppName', $schema->Name);

$G_SMARTY->assign("schema",$schema);
$G_SMARTY->assign("packages",$packages);
$G_SMARTY->assign("params", $params);

$G_SMARTY->display("show_tables.tpl");

?>
