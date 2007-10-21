    <asp:Panel ID="pnlForm" runat="server">
        <div class="fields">

		    <div class="groupheader">{$singular|studlycaps} Details</div>
		    <fieldset id="{$singular}_fields">
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
		        <div class="line">
			        <div class="field horizontal">
				        <div class="label horizontal width_125">{$column->NameWithoutPrefix|studlycaps}</div>
				        <div class="input horizontal">
                            <asp:TextBox ID="txt{$column->NameWithoutPrefix|studlycaps}" runat="server"></asp:TextBox></div>
			        </div>
		        </div>
{/foreach}
		    </fieldset>
	    </div>
	    
	    <p>
            <asp:Button ID="btnSave" runat="server" Text="Save {$singular|studlycaps}" />
            <asp:Button ID="btnCancel" runat="server" Text="Cancel" /></p>
    </asp:Panel>
    
	private Affinity.{$singular} {$singular|lower};
	private bool isUpdate;

	override protected void PageBase_Init(object sender, System.EventArgs e)
	{ldelim}
		// we have to call the base first so phreezer is instantiated
		base.PageBase_Init(sender, e);

		int id = NoNull.GetInt(Request["id"], 0);
		this.{$singular|lower} = new Affinity.{$singular}(this.phreezer);

		this.isUpdate = (!id.Equals(0));
		
		if (this.isUpdate)
		{ldelim}
			this.{$singular|lower}.Load(id);
		{rdelim}
	{rdelim}
    
    
	/// <summary>
	/// Persist to DB
	/// </summary>
	protected void UpdateAccount()
	{ldelim}
{foreach from=$table->Columns item=column}
{if $column->Type == "int"}
{assign var="ctype" value="int"}
    		this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps} = int.Parse(txt{$column->NameWithoutPrefix|studlycaps}.Text);
{elseif $column->Type == "tinyint"}
    		this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps} = int.Parse(txt{$column->NameWithoutPrefix|studlycaps}.Text);
{elseif $column->Type == "datetime"}
    		this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps} = DateTime.Parse(txt{$column->NameWithoutPrefix|studlycaps}.Text);
{else}
    		this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps} = txt{$column->NameWithoutPrefix|studlycaps}.Text;
{/if}
{/foreach}
	{rdelim}

    // #################################
    
		if (!Page.IsPostBack)
		{ldelim}
			// populate the form
{foreach from=$table->Columns item=column}
{if $column->Type == "int"}
{assign var="ctype" value="int"}
    		txt{$column->NameWithoutPrefix|studlycaps}.Text = this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps}.ToString();
{elseif $column->Type == "tinyint"}
    		txt{$column->NameWithoutPrefix|studlycaps}.Text = this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps}.ToString();
{elseif $column->Type == "datetime"}
    		txt{$column->NameWithoutPrefix|studlycaps}.Text = this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps}.ToShortDateString();
{else}
    		txt{$column->NameWithoutPrefix|studlycaps}.Text = this.{$singular|lower}.{$column->NameWithoutPrefix|studlycaps}.ToString();
{/if}
{/foreach}
		{rdelim}