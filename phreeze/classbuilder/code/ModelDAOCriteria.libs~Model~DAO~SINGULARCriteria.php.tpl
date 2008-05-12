<?php
/** @package    {$connection->DBName|studlycaps}::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Criteria.php");
	
/**
 * {$singular}Criteria allows custom querying for the {$singular} object.  Here you may
 * add properties that will translate into SQL.  Included are the basic fields that can 
 * be used as a typical "query by example" however you may add properties that do not
 * directly relate to fields or use other types of logic.
 *
 * This file is automatically generated by ClassBuilder.
 *
 * @package {$connection->DBName|studlycaps}::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class {$singular}Criteria extends Criteria
{ldelim}
	/**
	 * Phreeze parses all of the properties in the Criteria class and knows how to process
	 * any that end in the following _Equals, _IsLike, _BeginsWith, _EndsWith, _GreaterThan, LessThan.
	 * If you wish to add any criteria parameters beyond these, you must override OnPrepare
	 */
{foreach from=$table->Columns item=column}	public ${$column->NameWithoutPrefix|studlycaps}_Equals;
	public ${$column->NameWithoutPrefix|studlycaps}_NotEquals;
	public ${$column->NameWithoutPrefix|studlycaps}_IsLike;
	public ${$column->NameWithoutPrefix|studlycaps}_BeginsWith;
	public ${$column->NameWithoutPrefix|studlycaps}_EndWith;
	public ${$column->NameWithoutPrefix|studlycaps}_GreaterThan;
	public ${$column->NameWithoutPrefix|studlycaps}_LessThan;
{/foreach}

	/**
	 * For custom query logic, you may override OnProcess and set the $this->_where to whatever
	 * sql code is necessary.  If you choose to manually set _where then Phreeze will not touch
	 * your where clause at all and so any of the standard property names will be ignored
	 */
	/*
	function OnPrepare()
	{ldelim}
		if ($this->MyCustomField == "special value")
		{ldelim}
			// _where must begin with "where"
			$this->_where = "where db_field ....";
		{rdelim}
	{rdelim}
	*/
{rdelim}

?>