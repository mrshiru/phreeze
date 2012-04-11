<?php

require_once("_global.php");
require_once("verysimple/IO/FileHelper.php");
include_once("zip.lib.php");
require_once("libs/AppConfig.php");

// check for all required fields
if ( empty($_REQUEST["table_name"]) || empty($_REQUEST["package_name"]) )
{
	$G_SMARTY->assign("error","Please select at least one table and one template to generate");
	$G_SMARTY->display("error.tpl");
	exit();
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

$debug = isset($_REQUEST["debug"]) && $_REQUEST["debug"] == "1";
$parameters = explode("\n", trim(str_replace("\r","", $_REQUEST["parameters"])));
$tableNames = $_REQUEST["table_name"];
$packageNames = $_REQUEST["package_name"];
$debug_output = "";

$zipFile = new zipfile();

// need to change the smarty template directory
$G_SMARTY->template_dir = CODE_PATH;

$appname = ucwords(preg_replace("/(\_(.))/e","strtoupper('\\2')",strtolower($G_CONNECTION->DBName)));

foreach ($packageNames as $packageName)
{
	$config = new AppConfig(CODE_PATH . $packageName);

	foreach ($config->GetTemplateFiles() as $templateFile)
	{

		if ($templateFile->generate_mode == 2)
		{
			// single template where one is generated for the entire project instead of one for each selected table
			$templateFilename = str_replace(
					array('{$appname}','{$appname|lower}','{$appname|upper}'),
					array($appname,strtolower($appname),strtoupper($appname)),
					$templateFile->destination
			);

			$contents = file_get_contents(CODE_PATH . $templateFile->source);

			// this is a direct copy
			if ($debug)
			{
				$debug_output .= "\r\n###############################################################\r\n"
				. "# $templateFilename\r\n###############################################################\r\n"
				. "(contents of " . CODE_PATH . $templateFile->source . ")\r\n";
			}
			else
			{
				$zipFile->addFile( $contents , $templateFilename);
			}
		}
		elseif ($templateFile->generate_mode == 1)
		{
			// single template where one is generated for the entire project instead of one for each selected table
			$templateFilename = str_replace(
					array('{$appname}','{$appname|lower}','{$appname|upper}'),
					array($appname,strtolower($appname),strtoupper($appname)),
					$templateFile->destination
			);

			$G_SMARTY->clearAllAssign();

			foreach ($parameters as $param)
			{
				list($key,$val) = explode("=",$param,2);
				$G_SMARTY->assign($key,$val);
			}

			$G_SMARTY->assign("tableNames",$tableNames);
			$G_SMARTY->assign("templateFilename",$templateFilename);
			$G_SMARTY->assign("schema",$schema);
			$G_SMARTY->assign("tables",$schema->Tables);
			$G_SMARTY->assign("connection",$G_CONNECTION);
			$G_SMARTY->assign("appname",$appname);

			$tableInfos = Array();

			// enumerate all selected tables and merge them with the selected template
			// append each to the zip file for output
			foreach ($tableNames as $tableName)
			{
				$tableInfos[$tableName] = Array();
				$tableInfos[$tableName]['table'] = $schema->Tables[$tableName];
				$tableInfos[$tableName]['singular'] = $_REQUEST[$tableName."_singular"];
				$tableInfos[$tableName]['plural'] = $_REQUEST[$tableName."_plural"];
				$tableInfos[$tableName]['prefix'] = $_REQUEST[$tableName."_prefix"];
				$tableInfos[$tableName]['templateFilename'] = $templateFilename;
			}

			$G_SMARTY->assign("tableInfos",$tableInfos);

			if ($debug)
			{
				$debug_output .= "\r\n###############################################################\r\n"
				. "# $templateFilename\r\n###############################################################\r\n"
				. $G_SMARTY->fetch($templateFile->source) . "\r\n";
			}
			else
			{
				// we don't like bare linefeed characters
				$content = $body = preg_replace("/^(?=\n)|[^\r](?=\n)/", "\\0\r", $G_SMARTY->fetch($templateFile->source));

				$zipFile->addFile( $content , $templateFilename);
			}
		}
		else
		{
			// enumerate all selected tables and merge them with the selected template
			// append each to the zip file for output
			foreach ($tableNames as $tableName)
			{
				$singular = $_REQUEST[$tableName."_singular"];
				$plural = $_REQUEST[$tableName."_plural"];
				$prefix = $_REQUEST[$tableName."_prefix"];

				$templateFilename = str_replace(
					array('{$singular}','{$plural}','{$table}','{$appname}','{$singular|lower}','{$plural|lower}','{$table|lower}','{$appname|lower}','{$singular|upper}','{$plural|upper}','{$table|upper}','{$appname|upper}'),
					array($singular,$plural,$tableName,$appname,strtolower($singular),strtolower($plural),strtolower($tableName),strtolower($appname),strtoupper($singular),strtoupper($plural),strtoupper($tableName),strtoupper($appname)),
					$templateFile->destination);

				$G_SMARTY->clearAllAssign();
				$G_SMARTY->assign("appname",$appname);
				$G_SMARTY->assign("singular",$singular);
				$G_SMARTY->assign("plural",$plural);
				$G_SMARTY->assign("prefix",$prefix);
				$G_SMARTY->assign("templateFilename",$templateFilename);
				$G_SMARTY->assign("table",$schema->Tables[$tableName]);
				$G_SMARTY->assign("connection",$G_CONNECTION);

				foreach ($parameters as $param)
				{
					list($key,$val) = explode("=",$param,2);
					$G_SMARTY->assign($key,$val);
				}

				//print "<pre>"; print_r($schema->Tables[$tableName]->PrimaryKeyIsAutoIncrement()); die();
				if ($debug)
				{
					$debug_output .= "\r\n###############################################################\r\n"
					. "# $templateFilename\r\n###############################################################\r\n"
					. $G_SMARTY->fetch($templateFile->source) . "\r\n";
				}
				else
				{
					$zipFile->addFile( $G_SMARTY->fetch($templateFile->source) , $templateFilename);
				}

			}
		}

	}
}

if ($debug)
{
	header("Content-type: text/plain");
	print $debug_output;
}
else
{
	// now output the zip as binary data to the browser
	header("Content-type: application/force-download");

   // laplix 2007-11-02.
   // Use the application name provided by the user in show_tables.
	//header("Content-disposition: attachment; filename=".str_replace(" ","_",$G_CONNSTR->DBName).".zip");
	header("Content-disposition: attachment; filename=".str_replace(" ","_",$G_SMARTY->getTemplateVars('AppName')).".zip");

	header("Content-Transfer-Encoding: Binary");
	header('Content-Type: application/zip');
	print $zipFile->file();
}



?>
