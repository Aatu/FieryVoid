<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/tactical/*.php'), create_function('$v,$i', 'return require_once($v);'));
	session_start();
	
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false || $_SESSION["user"] == null){
		header('Location: index.php');
        return;
	}
	
	$gameid = Manager::shouldBeInGame($_SESSION["user"]);
	if ($gameid){
		header('Location: gamelobby.php');
	}
	
	$games = Manager::getTacGames($_SESSION["user"]);
	
	
	
	$games = json_encode($games, JSON_NUMERIC_CHECK);
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>B5CGM</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="base.css" rel="stylesheet" type="text/css">
		<link href="lobby.css" rel="stylesheet" type="text/css">
		<link href="games.css" rel="stylesheet" type="text/css">
		<script src="./engine/jquery-1.5.2.min.js"></script>
		<script src="./engine/setup/games.js"></script>
		<script src="./engine/tactical/ajaxInterface.js"></script>
		<script src="./engine/tactical/player.js"></script>
		<script>
			jQuery(function($){
            
				gamedata.parseServerData(<?php print($games); ?>);
				ajaxInterface.startPollingGames();
				gamedata.thisplayer = <?php print($_SESSION["user"]); ?>;
			});
		</script>
	</head>
	<body>
	
		<div class="panel large">
			<div class="logout"><a href="logout.php">LOGOUT</a></div>
			<div class="panelheader">	<span>GAMES</span>	</div>
			
			<div><span>ACTIVE GAMES</span></div>
			<div class="gamecontainer active subpanel">
				<div class="notfound">No active games</div>
			</div>
			
			<div><span>STARTING GAMES</span></div>
			<div class="gamecontainer lobby subpanel">
				<div class="notfound">No starting games</div>
			</div>
			
			<a href="creategame.php">CREATE GAME</a>
		</div>

	</body>
</html>