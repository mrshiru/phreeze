{include file="_header.tpl" header_title="Home"}

<h1>Home</h1>

<h2>You are currently connected with the following settings:</h2>

<table class="basic">
	<tr>
		<td>Host</td>
		<td>{$G_CONNSTR->Host}</td>
	</tr>
	<tr>
		<td>Port</td>
		<td>{$G_CONNSTR->Port}</td>
	</tr>
	<tr>
		<td>DB Name</td>
		<td>{$G_CONNSTR->DBName}</td>
	</tr>
	<tr>
		<td>DB Username</td>
		<td>{$G_CONNSTR->Username}</td>
	</tr>
	<tr>
		<td>DB Password</td>
		<td>*****</td>
	</tr>
</table>

<p><a href="show_tables.php" class="iconlink">Generate Templates...</a></p>

<p><a href="new_connection.php" class="iconlink">Change My Connection Settings...</a></p>

{include file="_footer.tpl"}