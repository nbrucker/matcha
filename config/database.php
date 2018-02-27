<?php
if(strpos($_SERVER['REQUEST_URI'], 'database') != false)
{
	header("Location: /");
	exit;
}
$DB_DSN = "mysql:host=127.0.0.1";
$DB_USER = "root";
$DB_PASSWORD = "root";
?>
