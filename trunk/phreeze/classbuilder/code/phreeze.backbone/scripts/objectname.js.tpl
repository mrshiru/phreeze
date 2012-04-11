/**
 * View logic for {$plural}
 */

$(document).ready(function() {
	page.init();
});

/**
 * application logic specific to this page
 */
var page = {

	{$plural|lcfirst}: new model.{$singular}Collection(),
	collectionView: null,
	{$singular|lcfirst}: null,
	modelView: null,

	/**
	 *
	 */
	init: function()
	{
		// make the new button clickable
		$("#new{$singular}Button").click(function(e) {
			e.preventDefault();
			page.showDetailDialog();
		});

		// when the model dialog is closed, reset the model view
		$('#{$singular|lcfirst}DetailDialog').on('hidden',function(){
			$('#modelAlert').html('');
		});

		// save the model when the save button is clicked
		$("#save{$singular}Button").click(function(e) {
			e.preventDefault();
			page.updateModel();
		});

		// initialize the collection view
		this.collectionView = new view.CollectionView({
			el: $("#{$singular|lcfirst}CollectionContainer"),
			collection: page.{$plural|lcfirst}
		});

		// tell the collection view where it's template is located
		this.collectionView.templateEl = $("#{$singular|lcfirst}CollectionTemplate");

		// make the rows clickable ('rendered' is a custom event, not a standard backbone event)
		this.collectionView.on('rendered',function(){

			// attach click handler to the table rows for editing
			$('table.collection tbody tr').click(function(e) {
				e.preventDefault();
				var m = page.{$plural|lcfirst}.get(this.id);
				page.showDetailDialog(m);
			});

			// attach click handlers to the pagination controls
			$('.pageButton').click(function(e) {
				e.preventDefault();
				var p = this.id.substr(5);
				page.fetch{$plural}({ page: p });
			});
		});

		// backbone docs recommend bootstrapping data on initial page load, but we live by our own rules!
		this.fetch{$plural}({ page: 1 });

		// initialize the model view
		this.modelView = new view.ModelView({
			el: $("#{$singular|lcfirst}ModelContainer")
		});

		// tell the model view where it's template is located
		this.modelView.templateEl = $("#{$singular|lcfirst}ModelTemplate");
	},

	/**
	 * Fetch the collection data from the server
	 */
	fetch{$plural}: function(params)
	{
		app.showProgress('loader');;
		$('#newButtonContainer').hide();

		page.{$plural|lcfirst}.fetch({

			data: params,

			success: function() {
				// data returned from the server.  render the collection view
				page.collectionView.render();

				app.hideProgress('loader');
				$('#newButtonContainer').show();
			},

			error: function(m, r) {
				app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'collectionAlert');
				app.hideProgress('loader');
			}

		});
	},

	/**
	 * show the dialog for editing a model
	 * @param model
	 */
	showDetailDialog: function(m) {

		// show the modal dialog
		$('#{$singular|lcfirst}DetailDialog').modal({ show: true });

		// either use the selected model or create a new one
		if (m)
		{
			page.{$singular|lcfirst} = m;
		}
		else
		{
			page.{$singular|lcfirst} = new model.{$singular}Model();
{if !$table->PrimaryKeyIsAutoIncrement()}
			// HACK: force backbone to insert because this table's primary key is not auto-increment
			page.{$singular|lcfirst}.overrideIsNew = true;
{/if}
		}
		page.modelView.model = page.{$singular|lcfirst};

		if (page.{$singular|lcfirst}.id == null || page.{$singular|lcfirst}.id == '')
		{
			// this is a new record, there is no need to contact the server
			page.renderModelView(false);
		}
		else
		{
			app.showProgress('modelLoader');

			// fetch the model from the server so we are not updating stale data
			page.{$singular|lcfirst}.fetch({

				success: function() {
					// data returned from the server.  render the model view
					page.renderModelView(true);
				},

				error: function(m, r) {
					app.appendAlert(app.getErrorMessage(r), 'alert-error',0,'modelAlert');
					app.hideProgress('modelLoader');
				}

			});
		}

	},

	/**
	 * Render the model template in the popup
	 * @param bool show the delete button
	 */
	renderModelView: function(showDeleteButton)
	{
		page.modelView.render();

		app.hideProgress('modelLoader');

		// initialize any special controls
		try {
			$('.date-picker').datepicker({ format: 'yyyy-mm-dd' });
		} catch (error) {
			// this happens if the datepicker input.value isn't a valid date
			if (console) console.log('datepicker error: '+error.message);
		}

{foreach from=$table->Columns item=column name=columnsForEach}
{if $column->Key == "MUL" && $column->Constraints}
{assign var=constraint value=$table->Constraints[$column->Constraints[0]]}
		// populate the dropdown options for {$column->NameWithoutPrefix|studlycaps|lcfirst}
		// TODO: load only the selected value, then fetch all options when the drop-down is clicked
		var {$column->NameWithoutPrefix|studlycaps|lcfirst|escape}Values = new model.{$constraint->ReferenceTableName|studlycaps}Collection();
		{$column->NameWithoutPrefix|studlycaps|lcfirst|escape}Values.fetch({
			success: function(c){
				var dd = $('#{$column->NameWithoutPrefix|studlycaps|lcfirst|escape}');
				dd.append('<option value=""></option>');
				c.forEach(function(item,index)
				{
					dd.append(app.getOptionHtml(
						item.get('{$constraint->ReferenceKeyColumnNoPrefix|studlycaps|lcfirst}'),
						item.get('{$constraint->ReferenceTable->GetDescriptorName()|studlycaps|lcfirst}'),
						page.{$singular|lcfirst}.get('{$column->NameWithoutPrefix|studlycaps|lcfirst|escape}') == item.get('{$constraint->ReferenceKeyColumnNoPrefix|studlycaps|lcfirst}')
					));
				});

			},
			error: function(collection,response,scope){
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
			}
		});

{/if}
{/foreach}

		if (showDeleteButton)
		{
			// attach click handlers to the delete buttons

			$('#delete{$singular}Button').click(function(e) {
				e.preventDefault();
				$('#confirmDelete{$singular}Container').show('fast');
			});

			$('#cancelDelete{$singular}Button').click(function(e) {
				e.preventDefault();
				$('#confirmDelete{$singular}Container').hide('fast');
			});

			$('#confirmDelete{$singular}Button').click(function(e) {
				e.preventDefault();
				page.deleteModel();
			});

		}
		else
		{
			// no point in initializing the click handlers if we don't show the button
			$('#delete{$singular}ButtonContainer').hide();
		}
	},

	/**
	 * update the model that is currently displayed in the dialog
	 */
	updateModel: function()
	{
		// reset any previous errors
		$('#modelAlert').html('');
		$('.control-group').removeClass('error');
		$('.help-inline').html('');

		// if this is new then on success we need to add it to the collection
		var isNew = page.{$singular|lcfirst}.isNew();

		app.showProgress('modelLoader');

		page.{$singular|lcfirst}.save({
{foreach from=$table->Columns item=column name=columnsForEach}
{if $column->Extra != 'auto_increment'}
			'{$column->NameWithoutPrefix|studlycaps|lcfirst}': {if $column->Type == "datetime"}$('input#{$column->NameWithoutPrefix|studlycaps|lcfirst}').val()+' '+$('input#{$column->NameWithoutPrefix|studlycaps|lcfirst}-time').val(){else}$('{if $column->Key == "MUL" && $column->Constraints}select{else}input{/if}#{$column->NameWithoutPrefix|studlycaps|lcfirst}').val(){/if}{if !$smarty.foreach.columnsForEach.last},{/if}
{/if}

{/foreach}
		}, {
			wait: true,
			success: function(){
				$('#{$singular|lcfirst}DetailDialog').modal('hide');
				setTimeout("app.appendAlert('{$singular} was sucessfully " + (isNew ? "inserted" : "updated") + "','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				// if the collection was initally new then we need to add it to the collection now
				if (isNew) { page.{$plural|lcfirst}.add(page.{$singular|lcfirst}) }

				// if desired re-sync the collection with the server
				// page.fetch{$plural}();

{if !$table->PrimaryKeyIsAutoIncrement()}
				// HACK: undo the forced insert
				page.{$singular|lcfirst}.overrideIsNew = false;
{/if}
		},
			error: function(model,response,scope){

				app.hideProgress('modelLoader');

				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');

				try {
					var json = $.parseJSON(response.responseText);

					if (json.errors)
					{
						$.each(json.errors, function(key, value) {
							$('#'+key+'InputContainer').addClass('error');
							$('#'+key+'InputContainer span.help-inline').html(value);
						});
					}
				} catch (e2) {
					if (console) console.log('error parsing server response: '+e2.message);
				}
			}
		});
	},

	/**
	 * delete the model that is currently displayed in the dialog
	 */
	deleteModel: function()
	{
		// reset any previous errors
		$('#modelAlert').html('');

		app.showProgress('modelLoader');

		page.{$singular|lcfirst}.destroy({
			wait: true,
			success: function(){
				$('#{$singular|lcfirst}DetailDialog').modal('hide');
				setTimeout("app.appendAlert('The {$singular} record was deleted','alert-success',3000,'collectionAlert')",500);
				app.hideProgress('modelLoader');

				// if desired re-sync the collection with the server
				// page.fetch{$plural}();
			},
			error: function(model,response,scope){
				app.appendAlert(app.getErrorMessage(response), 'alert-error',0,'modelAlert');
				app.hideProgress('modelLoader');
			}
		});
	}
};

