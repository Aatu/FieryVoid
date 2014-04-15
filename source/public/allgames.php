<?php
    include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false || $_SESSION["user"] == null){
		header('Location: index.php');
	}

	$ret = json_encode(Manager::getTacGames($_SESSION["user"]), JSON_NUMERIC_CHECK);

	print($ret);

?>