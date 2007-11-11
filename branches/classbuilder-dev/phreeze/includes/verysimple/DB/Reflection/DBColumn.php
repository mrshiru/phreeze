<?php
/** @package    verysimple::DB::Reflection */

/**
 * DBcolumn is an object representation of column
 * @package    verysimple::DB::Reflection
 * @author Jason Hinkle
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
 class DBColumn
{
	public $Table;
	public $Name;
	public $Type;
	public $Unsigned;
	public $Size;
	public $Key;
	public $Null;
	public $Default;
	public $Extra;
	public $NameWithoutPrefix; // populated by DBTable if there is a prefix
	public $MaxSize;
	public $Keys = array();
	public $Constraints = array();
	
	/**
	 * Instantiate new DBColumn
	 *
	 * @access public
	 * @param DBTable $table
	 * @param Array $row result from "describe table" statement
	 */	
	function DBColumn($table, $row)
	{
		// typical type is something like varchar(40)
		$typesize = explode("(",$row["Type"]);
		
		$tmp = isset($typesize[1]) ? str_replace(")","", $typesize[1]) : "" ;
		$sizesign = explode(" ", $tmp);
		
		$this->Table =& $table;
		$this->Name = $row["Field"];
		$this->NameWithoutPrefix = $row["Field"];
		$this->Type = $typesize[0];
		$this->Unsigned = isset($sizesign[1]);
		$this->Size = $sizesign[0] ;
		$this->Null = $row["Null"];
		$this->Key = $row["Key"];
		$this->Default = $row["Default"];
		$this->Extra = $row["Extra"];
		
		// if ($this->Key == "MUL") print " ########################## " . print_r($row,1) . " ########################## ";
		
		// size may be saved for decimals as "n,n" so we need to convert that to an int
		$tmp = explode(",",$this->Size);
		$this->MaxSize = count($tmp) > 1 ? ($tmp[0] + $tmp[1]) : $this->Size;
	}
}

?>