<?php
	// EXAMPLE CONFIF FILE
	// Database credentials
	define('SSL_CERT', 'ssl/BaltimoreCyberTrustRoot.crt.pem');
	define('DB_SERVER', 'SERVERNAME.mysql.database.azure.com');
	define('DB_USERNAME', 'MYNAME@cSERVERNAME');
	define('DB_PASSWORD', 'SUPPERSECUREPASSWORD');
	define('DB_NAME', 'DBNAME');

	// Attempt to connect to MySQL database
	$conn = mysqli_init();
	mysqli_ssl_set($conn, NULL, NULL, SSL_CERT, NULL, NULL);
	mysqli_real_connect($conn, DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, 3306, MYSQLI_CLIENT_SSL);

	// Check the conection
	if (mysqli_connect_errno($conn))
	{
		die('Failed to connect to MySQL: ' . mysqli_connect_error());
	}
?>