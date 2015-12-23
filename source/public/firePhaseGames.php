<?php
    include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}

	$ret = json_encode(Manager::getFirePhaseGames($_SESSION["user"]), JSON_NUMERIC_CHECK);

	print($ret);

?>