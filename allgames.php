<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*/*.php'), create_function('$v,$i', 'return require_once($v);'));
	session_start();

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}
	
	$ret = json_encode(Manager::getTacGames($_SESSION["user"]), JSON_NUMERIC_CHECK);
		
	print($ret);
	
?>