<?php
    include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false || $_SESSION["user"] == null){
		header('Location: index.php');
        return;
	}
	
	$games = Manager::getTacGames($_SESSION["user"]);
	
	$games = json_encode($games, JSON_NUMERIC_CHECK);
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>B5CGM</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<link href="styles/lobby.css" rel="stylesheet" type="text/css">
		<link href="styles/games.css" rel="stylesheet" type="text/css">
        <link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="client/games.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/player.js"></script>
        <script src="client/UI/confirm.js"></script>
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
        <div id="globalchat" class="panel large" style="height:150px;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>

	</body>
</html>