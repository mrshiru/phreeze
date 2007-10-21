<?php

require_once("_global.php");
require_once("verysimple/IO/FolderHelper.php");

$folder = new FolderHelper(CODE_PATH);
$files = $folder->GetFiles();

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
	
$G_SMARTY->assign("schema",$schema);
$G_SMARTY->assign("files",$files);
$G_SMARTY->display("show_tables.tpl");

?>