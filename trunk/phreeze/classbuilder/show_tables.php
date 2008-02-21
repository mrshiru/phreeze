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
	
/**
 * Holds all the vars that will be presented in the parameters section.
 * Note: this is quick and dirty. Should probably have its own file 
 * somewhere.
 *
 * @package Phreeze::ClassBuilder
 * @author  laplix
 * @since   2007-11-02
 */
class Parameter
{
   var $name;
   var $value;

   /**
    * Constructor.
    * @param string $name     Parameter name.
    * @param string $value    Parameter value.
    */
   function __construct($name=null, $value=null) {
      $this->name = $name;
      $this->value = $value;
   }

   /**
    * Constructor for php4.
    * @see __construct()
    */
   function Parameter($name=null, $value=null)
   {
      $this->__construct();
   }
}

// laplix 2007-11-02. Setup the parameters.
$params = array();
$params[] = new Parameter('PathToVerySimpleScripts', '/scripts/verysimple/');
$params[] = new Parameter('PathToExtScripts', '/scripts/ext-2/');

// Laplix 2007-11-02.
// AppName will enable the user to provide a name for his application.
// This will be used as the zip file name. Defaults to the database name.
$params[] = new Parameter('AppName', $schema->Name);

/*
echo "<pre>";
print_r($schema);
print_r($files);
print_r($params);
print_r($schema->Name);
exit();
/**/

$G_SMARTY->assign("schema",$schema);
$G_SMARTY->assign("files",$files);

// laplix 2007-11-02
$G_SMARTY->assign("params", $params);

$G_SMARTY->display("show_tables.tpl");

?>
