<?php
/** @package    verysimple::DB::Reflection */

/**
 * DBSet is an object representation of table dependency relationship
 *
 * @package    verysimple::DB::Reflection
 * @author Jason Hinkle
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version 1.0
 */
class DBSet
{
	public $Table;
	public $Name;
	public $KeyColumn;
	public $SetTableName;
	public $SetKeyColumn;
	
	public $KeyColumnNoPrefix;
	public $SetKeyColumnNoPrefix;
	public $SetPrimaryKey;
	public $SetPrimaryKeyNoPrefix;

	/**
	 * Instantiate new DBSet
	 *
	 * @access public
	 * @param DBTable $table that is the dependent/child table
	 * @param Array $row array that is result from parsing show create table	
	 */	
	function DBSet($table, $row)
	{
		$this->Table =& $table->Schema->Tables[$row[2]];
		
		$this->Name = $row[0];
		$this->KeyColumn = $row[3];
		$this->SetTableName = $table->Name;
		$this->SetKeyColumn = $row[1];

		$reftable = $this->Table->Schema->Tables[$this->SetTableName];
		// print "<p><b>" . $this->Table->Name . " set references " . $reftable->Name . "</b></p>";

		$this->SetPrimaryKey = $reftable->GetPrimaryKeyName(false);

		$this->KeyColumnNoPrefix = $this->Table->RemovePrefix($this->KeyColumn);
		$this->SetKeyColumnNoPrefix = $reftable->RemovePrefix($this->SetKeyColumn);
		$this->SetPrimaryKeyNoPrefix = $reftable->RemovePrefix($this->SetPrimaryKey);
	}
}

?>