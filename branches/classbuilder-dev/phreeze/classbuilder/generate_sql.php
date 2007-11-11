<?php
/**
 * generate_sql reverse engineers a phreeze application and generates a sql
 * script to create the database based on the field and key map information
 *
 * To use simply place this file in the root of your application directory 
 * and run it from a web browser
 *
 * @author     Matt Dennewitz, Jason Hinkle
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    1.0
 */

header('Content-type: text/plain');

require_once("_config.php");

$field_types = array("FM_TYPE_UNKNOWN",
		"FM_TYPE_DECIMAL",
		"FM_TYPE_INT",
		"FM_TYPE_SMALLINT",
		"FM_TYPE_TINYINT",
		"FM_TYPE_VARCHAR",
		"FM_TYPE_BLOB",
		"FM_TYPE_DATE",
		"FM_TYPE_DATETIME",
		"FM_TYPE_TEXT",
		"FM_TYPE_SMALLTEXT",
		"FM_TYPE_MEDIUMTEXT",
		"FM_TYPE_CHAR",
		"FM_TYPE_LONGBLOB",
		"FM_TYPE_LONGTEXT",
		"FM_TYPE_MEDIUMINT"
		);

function isMap($filename)
{
	return (substr($filename,-7,3) == "Map");
}

function translateFromStudlyCaps($value)
{
	$field = preg_replace("/([A-Z])/","_$1",$value);
	return strtolower( substr($field,1,strlen($field)-1) );
}

$tables = array();

$files = array();
if ($handle = opendir('./libs/Model/DAO/'))
{
	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != ".." && $file != ".svn")
		{
			$files[] = $file;
			include_once('./libs/Model/DAO/' . $file);
		}
	}
}
closedir($handle);

$map_files = array_filter($files,"isMap");

// first pass just gets the maps
$classes = array();
foreach($map_files as $map_file)
{
	$class_name = str_replace(".php","",$map_file);
	$class = new $class_name;
	$maps = $class->GetFieldMaps();
	$keys = $class->GetKeyMaps();

	foreach($maps as $map)
	{
		$classes[$class_name]['maps'] = $maps;
		$classes[$class_name]['keys'] = $keys;
	}
}

// print_r($classes); die();

// second pass creates the sql
foreach($map_files as $map_file)
{
	$class_name = str_replace(".php","",$map_file);
	$class = new $class_name;
	$maps = $class->GetFieldMaps();
	$keys = $class->GetKeyMaps();
	
	
	foreach($maps as $map)
	{
		//print_r($map);

		$sql_column_type = strtolower(str_replace("FM_TYPE_","",$field_types[$map->FieldType]));
		$sql_column_size = ($map->FieldSize) ? "(" . $map->FieldSize . ")":"";
		$sql_default = ($map->DefaultValue) ? " default '" . $map->DefaultValue . "'":"";
		$sql_auto_increment = ($map->IsAutoInsert) ? " not null auto_increment":"";
		
		if($sql_column_type == "decimal")
		{
			$sql_column_size = str_replace(".",",",$sql_column_size);
		}
		
		$sql = $map->ColumnName . " " . $sql_column_type . $sql_column_size . $sql_default . $sql_auto_increment;
		$tables[$map->TableName]['sql'][] = $sql;
		
		if($map->IsPrimaryKey)
		{
			$tables[$map->TableName]['primary_keys'][] = $map->ColumnName;
		}
		
	}
	
	foreach($keys as $key)
	{

		//print_r($key);

		if($key->KeyType == 1)
		{
			continue;
		}
		
		$constraint = "alter table " . $map->TableName . " add constraint " . $key->KeyName . " ";
		$constraint .= "foreign key (" .
			$maps[$key->KeyProperty]->ColumnName .
			") references " .
			$classes[$key->ForeignObject."Map"]['maps'][$key->ForeignKeyProperty]->TableName .
			"(" .
			$classes[$key->ForeignObject."Map"]['maps'][$key->ForeignKeyProperty]->ColumnName .
			");";
		$tables[$map->TableName]['foreign_keys'][] = $constraint;
	}
	
}

$sql = "";

foreach($tables as $table => $data)
{
	$sql .= "drop table if exists `" . $table . "`;" . "\r\n";
	$sql .= "create table `" . $table . "` " . "\r\n";
	$sql .= "(";
	$sql .= implode("," . "\r\n",$data['sql']);
	$sql .= " ";
	
	if(!empty($data['primary_keys']))
	{
		$sql .= ", primary key (" . implode(",",$data['primary_keys']) . ")";
	}
	
	$sql .= ");" . "\r\n\r\n";
}

foreach($tables as $table => $data)
{
	if(!empty($data['foreign_keys']))
	{
		foreach($data['foreign_keys'] as $fk)
		{
			$sql .= $fk . "\r\n";
		}
	}
}

print $sql;

?>