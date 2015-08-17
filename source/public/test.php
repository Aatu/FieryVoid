<?php ob_start("ob_gzhandler"); 
    include_once 'global.php';


	$get = $_GET["string"];

	debug::log($get);
	
?>