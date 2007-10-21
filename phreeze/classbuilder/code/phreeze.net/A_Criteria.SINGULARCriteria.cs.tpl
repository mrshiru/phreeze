using System;
using System.Text;
using System.Collections;
using Com.VerySimple.Phreeze;

namespace Affinity
{ldelim}
	/// <summary>
	/// Summary description for {$singular}Criteria
	/// </summary>
	public class {$singular}Criteria : Criteria
	{ldelim}
{foreach from=$table->Columns item=column}
{if $column->Type == "int"}
{assign var="ctype" value="int"}
{assign var="cval" value=" = -1"}
{elseif $column->Type == "tinyint"}
{assign var="ctype" value="int"}
{assign var="cval" value=" = -1"}
{elseif $column->Type == "datetime"}
{assign var="ctype" value="DateTime"}
{assign var="cval" value=""}
{else}
{assign var="ctype" value="string"}
{assign var="cval" value=""}
{/if}
		public {$ctype} {$column->NameWithoutPrefix|studlycaps}{$cval};
{/foreach}

		protected override void Init()
		{ldelim}
			this.fields = new Hashtable();
{foreach from=$table->Columns item=column}
			this.fields.Add("{$column->NameWithoutPrefix|studlycaps}", "{$column->Name}");
{/foreach}
		{rdelim}

		protected override string GetSelectSql()
		{ldelim}
			return "select * from `{$table->Name}` {$table->ColumnPrefix|replace:"_":""} ";
		{rdelim}

		protected override string GetWhereSql()
		{ldelim}
			StringBuilder sb = new StringBuilder();
			string delim = " where ";

{foreach from=$table->Columns item=column}
{if $column->Type == "int"}
			if (-1 != {$column->NameWithoutPrefix|studlycaps})
{elseif $column->Type == "tinyint"}
			if (-1 != {$column->NameWithoutPrefix|studlycaps})
{elseif $column->Type == "datetime"}
			if ("1-1-1 0:0:0" != Preparer.Escape({$column->NameWithoutPrefix|studlycaps}))
{else}
			if (null != {$column->NameWithoutPrefix|studlycaps})
{/if}
			{ldelim}
				sb.Append(delim + "{$table->ColumnPrefix|replace:"_":""}.{$column->Name} = '" + Preparer.Escape({$column->NameWithoutPrefix|studlycaps}) + "'");
				delim = " and ";
			{rdelim}

{/foreach}
			return sb.ToString();
		{rdelim}
	{rdelim}
{rdelim}