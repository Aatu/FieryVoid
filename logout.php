<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	session_start();


	session_destroy();
	session_unset();
	
	
	header('Location: index.php');
	

?>