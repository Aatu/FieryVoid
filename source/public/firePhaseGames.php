<?php
    include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}
    
    $userid = $_SESSION["user"];
    session_write_close(); // Release session lock early

	$ret = json_encode(Manager::getFirePhaseGames($userid), JSON_NUMERIC_CHECK);

	print($ret);

?>