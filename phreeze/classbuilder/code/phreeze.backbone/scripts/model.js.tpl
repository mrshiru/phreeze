/**
 * backbone model definitions for {$appname}
 */

// Uncomment the following if the server won't support PUT/DELETE or application/json requests
// Backbone.emulateHTTP = true;
// Backbone.emulateJSON = true

// override backbone isNew to allow forcing an insert on tables without auto-insert id
Backbone.Model.prototype.isNew = function () {
	if (this.overrideIsNew == true) return true;
    return this.id == null;
};

var model = {};

{foreach from=$tables item=table}
{assign var=singular value=$tableInfos[$table->Name]['singular']}
{assign var=plural value=$tableInfos[$table->Name]['plural']}
model.{$singular}Model = Backbone.Model.extend({
	urlRoot: 'api/{$singular|lower}',
	idAttribute: '{$table->GetPrimaryKeyName()|studlycaps|lcfirst}',
{foreach from=$table->Columns item=column name=columnsForEach}
	{$column->NameWithoutPrefix|studlycaps|lcfirst}: '',
{/foreach}
	defaults: {
{foreach from=$table->Columns item=column name=columnsForEach}
		'{$column->NameWithoutPrefix|studlycaps|lcfirst}': {if $column->NameWithoutPrefix == $table->GetPrimaryKeyName()}null{elseif $column->Type == "date" or $column->Type == "datetime"}new Date(){else}''{/if}{if !$smarty.foreach.columnsForEach.last},{/if}

{/foreach}
	}
});

model.{$singular}Collection = Backbone.Collection.extend({
	url: 'api/{$singular|lower}',
	model: model.{$singular}Model,

	totalResults: 0,
	totalPages: 0,
	currentPage: 0,
	pageSize: 0,

	// override parse to handle pagination
	parse: function(response) {
		this.totalResults = response.totalResults;
		this.totalPages = response.totalPages;
		this.currentPage = response.currentPage;
		this.pageSize = response.pageSize;

		return response.rows;
	}
});

{/foreach}