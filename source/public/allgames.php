<?php

// Load global config and classes
require_once 'global.php';

// Start session if not active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (empty($_SESSION["user"])) {
    header('Location: index.php');
    exit;
}

// Fetch games for logged-in user
$userid = (int)$_SESSION["user"];
$ret = json_encode(Manager::getTacGames($userid), JSON_NUMERIC_CHECK);

// Output JSON response
header('Content-Type: application/json; charset=utf-8');
echo $ret;

exit;


/* OLD VERSION
    include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}

	$ret = json_encode(Manager::getTacGames($_SESSION["user"]), JSON_NUMERIC_CHECK);

	print($ret);

?>*/