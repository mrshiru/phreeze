<?php
/**
 * Define the connection settings to connect to the database.
 * This file should generaly not be part of version control so that
 * it can be customized for each server installation.
 */

	/** database connection settings */
	$csetting->ConnectionString = "{$connection->Host}:{$connection->Port}";
	$csetting->DBName = "{$connection->DBName}";
	$csetting->Username = "{$connection->Username}";
	$csetting->Password = "{$connection->Password}";
	
	/** timezone */
	// date_default_timezone_set("UTC");

	/** additional application settings */
	
?>