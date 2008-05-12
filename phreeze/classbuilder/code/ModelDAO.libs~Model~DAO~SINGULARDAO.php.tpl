<?php
/** @package {$connection->DBName|studlycaps}::Model::DAO */

/** import supporting libraries */
require_once("verysimple/Phreeze/Phreezable.php");
require_once("{$singular}Map.php");

/**
 * {$singular}DAO provides object-oriented access to the {$column->Name} table.  This
 * class is automatically generated by ClassBuilder.
 *
 * This file should generally not be edited by hand except in special circumstances.
 * Add any custom business logic to the Model class which is extended from this DAO class.
 * Leaving this file alone will allow easy re-generation of all DAOs in the event of schema changes
 *
 * @package {$connection->DBName|studlycaps}::Model::DAO
 * @author ClassBuilder
 * @version 1.0
 */
class {$singular}DAO extends Phreezable
{ldelim}
{foreach from=$table->Columns item=column}	public ${$column->NameWithoutPrefix|studlycaps};
{/foreach}

{foreach from=$table->Sets item=set}	public function Get{$set->GetterName|studlycaps}($criteria = null)
	{ldelim}
		return $this->_phreezer->GetOneToMany($this, "{$set->Name}", $criteria);
	{rdelim}

{/foreach}
{foreach from=$table->Constraints item=constraint}	public function Get{$constraint->GetterName|studlycaps}()
	{ldelim}
		return $this->_phreezer->GetManyToOne($this, "{$constraint->Name}");
	{rdelim}

{/foreach}

{rdelim}
?>