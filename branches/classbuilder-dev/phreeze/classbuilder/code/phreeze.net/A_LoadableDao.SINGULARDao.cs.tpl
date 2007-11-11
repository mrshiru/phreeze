using System;
using MySql.Data.MySqlClient;
using System.Text;
using System.Collections;
using Com.VerySimple.Phreeze;

namespace Affinity
{ldelim}
	/// <summary>
	/// This code is automatically generated from the {$singular} schema and
	/// should not be edited if at all possible.  All customizations and 
	/// business logic should be added to the partial class {$singular}.cs
	/// </summary>
	public partial class {$singular} : Loadable
	{ldelim}


		public {$singular}(Phreezer phreezer) : base(phreezer) {ldelim} {rdelim}
		public {$singular}(Phreezer phreezer, MySqlDataReader reader) : base(phreezer,reader) {ldelim} {rdelim}

		/* ~~~ PUBLIC PROPERTIES ~~~ */

{foreach from=$table->Columns item=column}
{if $column->Type == "int"}
{assign var="ctype" value="int"}
{assign var="cval" value="0"}
{elseif $column->Type == "tinyint"}
{assign var="ctype" value="bool"}
{assign var="cval" value="false"}
{elseif $column->Type == "datetime"}
{assign var="ctype" value="DateTime"}
{assign var="cval" value="DateTime.Now"}
{else}
{assign var="ctype" value="string"}
{assign var="cval" value='""'}
{/if}

		private {$ctype} _{$column->NameWithoutPrefix|camelcase} = {$cval};
		public {$ctype} {$column->NameWithoutPrefix|studlycaps}
		{ldelim}
			get {ldelim} return this._{$column->NameWithoutPrefix|camelcase}; {rdelim}
			set {ldelim} this._{$column->NameWithoutPrefix|camelcase} = value; {rdelim}
		{rdelim}
{/foreach}

		/* ~~~ CONSTRAINTS ~~~ */

{foreach from=$table->Constraints item=constraint}
		private {$constraint->ReferenceTableName|studlycaps} _{$constraint->Name|camelcase};
		public {$constraint->ReferenceTableName|studlycaps} {$constraint->ReferenceTableName|studlycaps}
		{ldelim}
			get
			{ldelim}
				if (this._{$constraint->Name|camelcase} == null)
				{ldelim}
					this._{$constraint->Name|camelcase} = new {$constraint->ReferenceTableName|studlycaps}(this.phreezer);
					this._{$constraint->Name|camelcase}.Load(this.{$constraint->KeyColumnNoPrefix|studlycaps});
				{rdelim}
				return this._{$constraint->Name|camelcase};
			{rdelim}
			set {ldelim} this._{$constraint->Name|camelcase} = value; {rdelim}
		{rdelim}

{/foreach}

		/* ~~~ SETS ~~~ */

{foreach from=$table->Sets item=set}
		/// <summary>
		/// Returns a collection of {$set->SetTableName|studlycaps} objects
		/// </summary>
		/// <param name="criteria"></param>
		/// <returns>{$set->SetTableName|studlycaps}s</returns>
		public {$set->SetTableName|studlycaps}s Get{$set->Name|studlycaps}s({$set->SetTableName|studlycaps}Criteria criteria)
		{ldelim}
			criteria.{$set->SetKeyColumnNoPrefix|studlycaps} = this.{$set->KeyColumnNoPrefix|studlycaps};
			{$set->SetTableName|studlycaps}s {$set->Name|camelcase}s = new {$set->SetTableName|studlycaps}s(this.phreezer);
			{$set->Name|camelcase}s.Query(criteria);
			return {$set->Name|camelcase}s;
		{rdelim}

{/foreach}

		/* ~~~ CRUD OPERATIONS ~~~ */

		/// <summary>
		/// Assigns a value to the primary key
		/// </summary>
		/// <param name="key"></param>
		protected override void SetPrimaryKey(object key)
		{ldelim}
{foreach from=$table->Columns item=column}
{if $column->Key == "PRI"}
{if $column->Type == "int"}
{assign var="ctype" value="int"}
{elseif $column->Type == "tinyint"}
{assign var="ctype" value="bool"}
{elseif $column->Type == "datetime"}
{assign var="ctype" value="DateTime"}
{else}
{assign var="ctype" value="string"}
{/if}
		   this.{$column->NameWithoutPrefix|studlycaps} = ({$ctype})key;
{/if}
{/foreach}
		{rdelim}

		/// <summary>
		/// Returns a SQL statement to select this object from the DB
		/// </summary>
		/// <param name="primaryKey"></param>
		/// <returns></returns>
		protected override string GetSelectSql(object pk)
		{ldelim}
			return "select * from `{$table->Name}` {$table->ColumnPrefix|replace:"_":""} where {$table->ColumnPrefix|replace:"_":""}.{$table->GetPrimaryKeyName(false)} = '" + pk.ToString() + "'";
		{rdelim}

		/// <summary>
		/// Returns an SQL statement to update this object in the DB
		/// </summary>
		/// <returns></returns>
		protected override string GetUpdateSql()
		{ldelim}
			StringBuilder sb = new StringBuilder();
			sb.Append("update `{$table->Name}` set");
{assign var="delim" value=" "}
{foreach from=$table->Columns item=column}
{if $column->Key != "PRI"}
			sb.Append(" {$delim}{$column->Name} = '" + Preparer.Escape(this.{$column->NameWithoutPrefix|studlycaps}) + "'");
{assign var="delim" value=","}
{/if}
{/foreach}
			sb.Append(" where {$table->GetPrimaryKeyName(false)} = '" + Preparer.Escape(this.{$table->GetPrimaryKeyName()|studlycaps}) + "'");
			return sb.ToString();
		{rdelim}

		/// <summary>
		/// Returns an SQL statement to insert this object into the DB
		/// </summary>
		/// <returns></returns>
		protected override string GetInsertSql()
		{ldelim}
			StringBuilder sb = new StringBuilder();
			sb.Append("insert into `{$table->Name}` (");
{assign var="delim" value=" "}
{foreach from=$table->Columns item=column}
{if $column->Extra != "auto_increment"}
			sb.Append(" {$delim}{$column->Name}");
{assign var="delim" value=","}
{/if}
{/foreach}
			sb.Append(" ) values (");
{assign var="delim" value=" "}
{foreach from=$table->Columns item=column}
{if $column->Extra != "auto_increment"}
			sb.Append(" {$delim}'" + Preparer.Escape(this.{$column->NameWithoutPrefix|studlycaps}) + "'");
{assign var="delim" value=","}
{/if}
{/foreach}
			sb.Append(" )");

			return sb.ToString();
		{rdelim}

		/// <summary>
		/// Returns an SQL statement to delete this object from the DB
		/// </summary>
		/// <returns></returns>
		protected override string GetDeleteSql()
		{ldelim}
			return "delete from `{$table->Name}` where {$table->GetPrimaryKeyName(false)} = '" + {$table->GetPrimaryKeyName()|studlycaps}.ToString() + "'";
		{rdelim}

		/// <summary>
		/// reads the column values from a datareader and populates the object properties
		/// </summary>
		/// <param name="reader"></param>
		public override void Load(MySqlDataReader reader)
		{ldelim}
{foreach from=$table->Columns item=column}
{if $column->Type == "int"}
{assign var="ctype" value="Int"}
{elseif $column->Type == "tinyint"}
{assign var="ctype" value="Bool"}
{elseif $column->Type == "datetime"}
{assign var="ctype" value="DateTime"}
{else}
{assign var="ctype" value="String"}
{/if}
		   this.{$column->NameWithoutPrefix|studlycaps} = Preparer.Safe{$ctype}(reader["{$column->Name}"]);
{/foreach}

			this.OnLoad(reader);
		{rdelim}

	{rdelim}
{rdelim}