{include file="_header.tpl" header_title="New Connection"}

<h1><a href="index.php">Home</a> <span class="iconlink">New Connection</span></h1>

<form action="set_connection.php" method="post">

<table class="basic">
	<tr>
		<td>Host</td>
		<td><input type="text" name="host" value="localhost" /></td>
	</tr>
	<tr>
		<td>Port</td>
		<td><input type="text" name="port" value="3306" /></td>
	</tr>
	<tr>
		<td>DB Name</td>
		<td><input type="text" name="dbname" value="" /></td>
	</tr>
	<tr>
		<td>DB Username</td>
		<td><input type="text" name="username" value="root" /></td>
	</tr>
	<tr>
		<td>DB Password</td>
		<td><input type="password" name="password" value="" /></td>
	</tr>
</table>

<p><input type="submit" value="Set Connection" /> <input type="reset" value="Cancel" onclick="self.location='index.php';" /></p>

</form>

{include file="_footer.tpl"}