{include file="_header.tpl" header_title="Generate Application"}

<h1><span class="iconlink">Generate Application</span></h1>

<form action="generate_class.php" method="post">

<h2>1. Select Tables:</h2> 

<table class="basic"> 
<tr> 
    <th><input type="checkbox" name="table_toggle" value="0" onclick="checkAll(this.form, 'table_name[]',this.checked)" checked="checked" /></th> 
    <th>Table</th> 
    <th>Object (Singular)</th> 
    <th>Object (Plural)</th> 
    <th>Column Prefix</th> 
</tr> 

{foreach from=$schema->Tables item=table}
	<tr>
		<td><input type="checkbox" name="table_name[]" value="{$table->Name}" {if !$table->IsView}checked="checked"{/if} /></td> 
		<td>{if $table->IsView}VIEW: {/if}{$table->Name}</td> 
		<td><input type="text" name="{$table->Name}_singular" value="{$table->Name|studlycaps}" /></td> 
		<td><input type="text" name="{$table->Name}_plural" value="{$table->Name|studlycaps}s" /></td> 
		<td><input type="text" name="{$table->Name}_prefix" value="{$table->ColumnPrefix}" size="15" /></td> 
	</tr>
{/foreach}

</table>

<h2>2. Select Templates to Generate:</h2>

<div style="height: 150px; overflow: auto; border: solid 1px #666666; background-color: #E1E1E1;"> 

	<div><input type="checkbox" name="table_toggle" value="0" onclick="checkAll(this.form, 'template_name[]',this.checked)" checked="checked"/> <em>SELECT ALL</em></div>
	{foreach from=$files item=file}
		<div><input type="checkbox" name="template_name[]" value="{$file->Name}" checked="checked"/>{$file->Prefix}</div>
	{/foreach}
</div>

<h2>3. (Optional) Additional Parameters (one per line):</h2> 

<p>
<!-- laplix 2007-11-02. using the $param var instead of harcoding the parameters
<textarea name="parameters" style="width: 400px; height: 75px;">PathToVerySimpleScripts=/scripts/verysimple/
PathToExtScripts=/scripts/ext/
</textarea>
-->
<textarea name="parameters" style="width: 400px; height: 75px;">{foreach from=$params item=param}
{$param->name}={$param->value}
{/foreach}
</textarea>
</p>

<h2>4. Send Output To:</h2>

<p>
<input type="radio" name="debug" value="" checked="checked" /> Zip Archive 
<input type="radio" name="debug" value="1" /> Browser
</p>

<p><input type="submit" value="Generate Application" /></p>

</form>

{include file="_footer.tpl"}
