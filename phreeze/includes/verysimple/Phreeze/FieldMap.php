<?php
/** @package    verysimple::Phreeze */

/** import supporting libraries */
define("FM_TYPE_UNKNOWN",0);
define("FM_TYPE_DECIMAL",1);
define("FM_TYPE_INT",2);
define("FM_TYPE_SMALLINT",3);
define("FM_TYPE_TINYINT",4);
define("FM_TYPE_MEDIUMINT",15);
define("FM_TYPE_BIGINT",16);
define("FM_TYPE_VARCHAR",5);
define("FM_TYPE_BLOB",6);
define("FM_TYPE_DATE",7);
define("FM_TYPE_DATETIME",8);
define("FM_TYPE_TEXT",9);
define("FM_TYPE_SMALLTEXT",10);
define("FM_TYPE_MEDIUMTEXT",11);
define("FM_TYPE_CHAR",12);
define("FM_TYPE_LONGBLOB",13);
define("FM_TYPE_LONGTEXT",14);

define("FM_TYPE_TIMESTAMP",17);
define("FM_TYPE_ENUM",18);
define("FM_TYPE_TINYTEXT",19);

define("FM_CALCULATION",99); // not to be used during save

/**
 * FieldMap is a base object for mapping a Phreezable object to a database table
 *
 * @package    verysimple::Phreeze
 * @author     VerySimple Inc.
 * @copyright  1997-2007 VerySimple, Inc.
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    2.0
 */
class FieldMap
{

	public $PropertyName;
	public $TableName;
	public $ColumnName;
	public $FieldType;
	public $FieldSize;
	public $IsPrimaryKey;
	public $DefaultValue;
	public $IsAutoInsert;
	
	/**
	* Initializes the FieldMap
	*
	* @param string $pn Model property name
	* @param string $tn DB table name
	* @param string $cb DB column name
	* @param bool $pk True if column is a primary key  (optional default = false)
	* @param int $ft Field type FM_TYPE_VARCHAR | FM_TYPE_INT | etc...  (optional default = FM_TYPE_UNKNOWN)
	* @param int $fs Field size, 0 for unlimited  (optional default = 0)
	* @param variant $dv Default value  (optional default = null)
	* @param bool $iai True if column is auto insert column  (optional default = null)
	*/
	public function FieldMap($pn, $tn, $cn, $pk = false, $ft = FM_TYPE_UNKNOWN, $fs = 0, $dv = null, $iai = null)
	{
		$this->PropertyName = $pn;
		$this->TableName = $tn;
		$this->ColumnName = $cn;
		$this->IsPrimaryKey = $pk;
		$this->FieldType = $ft;
		$this->FieldSize = $fs;
		$this->DefaultValue = $dv;
		$this->IsAutoInsert = $iai;
	}
}

?>