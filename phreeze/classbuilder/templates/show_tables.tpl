{include file="_header.tpl" header_title="Generate Templates"}

<h1><a href="index.php">Home</a>  <span class="iconlink">Generate Templates</span></h1>

<form action="generate_class.php" method="post">

<p>Select Templates to Generate:
(<input type="checkbox" name="table_toggle" value="0" onclick="checkAll(this.form, 'template_name[]',this.checked)" checked="checked"/> All)
</p>
<div style="height: 100px; width: 500px; overflow: auto; border: solid 1px #666666; background-color: #dddddd;"> 

	{foreach from=$files item=file}
		<div><input type="checkbox" name="template_name[]" value="{$file->Name}" checked="checked"/>{$file->Prefix}</div>
	{/foreach}
</div>

<p>Select Tables:</p> 

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
		<td><input type="checkbox" name="table_name[]" value="{$table->Name}" checked="checked" /></td> 
		<td>{$table->Name}</td> 
		<td><input type="text" name="{$table->Name}_singular" value="{$table->Name|studlycaps}" /></td> 
		<td><input type="text" name="{$table->Name}_plural" value="{$table->Name|studlycaps}s" /></td> 
		<td><input type="text" name="{$table->Name}_prefix" value="{$table->ColumnPrefix}" size="15" /></td> 
	</tr>
{/foreach}

</table>

<p><input type="checkbox" name="debug" value="1" /> Debug Mode</p>

<p><input type="submit" value="Export Templates" /></p>

</form>

{include file="_footer.tpl"}