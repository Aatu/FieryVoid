<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
session_start();

$taskforces = json_encode(Manager::getTaskforces(1, 1));


?>


<!DOCTYPE HTML>
<html>
<head>
	<title>B5CGM main</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="B5CGM.css" rel="stylesheet" type="text/css">
	<script>
		var gamedata = {};
		var selectedid = -1;
		gamedata.game = {};
		gamedata.game.turn = 0;
		gamedata.game.hyperspacespeed = 1;
		gamedata.taskforces = <?php print($taskforces); ?>;
	</script>
	
</head>


<body>



</body>

</html>