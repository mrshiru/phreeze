<?php

require_once("_global.php");
require_once("verysimple/IO/FolderHelper.php");
require_once('CBProperties.php');
require_once('CBParameter.php');

$folder = new FolderHelper(CODE_PATH);
$files = $folder->ls('*.tpl');

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
	
// try to read the properties file. if it doesn't exists,
// provide defaults
$cbp = new CBProperties;
$verysimple = $cbp->getSection('VerySimple');
if (empty($verysimple))
{
   $verysimple = array(
      'PathToVerySimpleScripts' => '/scripts/verysimple/',
      'PathToExtScripts' => '/scripts/ext/',
      'ExtAdapter' => 'yui',
      'ExtMajorVersion' => 1,
      'AppName' => $schema->Name
   );
   $cbp->putSection('VerySimple', $verysimple);
   $cbp->iniWrite();
}

// load the parameters
$params = array();
$section = $cbp->getSection('VerySimple');
foreach($section as $key => $value)
{
   $params[] = new CBParameter($key, $value);
}

$G_SMARTY->assign("schema",$schema);
$G_SMARTY->assign("files",$files);

// laplix 2007-11-02
$G_SMARTY->assign("params", $params);

$G_SMARTY->display("show_tables.tpl");

?>
