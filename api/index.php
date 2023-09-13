<?php
	
	$config = parse_ini_file('C:/xampp/htdocs/api/auth.ini');
	$conn = mysqli_connect($config['dbhost'], $config['username']);
	mysqli_select_db($conn, $config['db']);

	echo("Success");

