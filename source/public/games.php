<?php
    include_once 'global.php';

	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
//      return;
	}
	
	$games = Manager::getTacGames($_SESSION["user"]);
	
	$games = json_encode($games, JSON_NUMERIC_CHECK);
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Games</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<link href="styles/lobby.css" rel="stylesheet" type="text/css">
		<link href="styles/games.css" rel="stylesheet" type="text/css">
        <link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script src="client/helper.js"></script>
        <script src="client/games.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/player.js"></script>
        <script src="client/mathlib.js"></script>
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
	
        <img src="img/logo.png">
        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>
		<div class="panel large">
			<div class="logout"><a href="logout.php">LOGOUT</a></div>
			
            <table class="gametable">
                <tr><td>ACTIVE GAMES</td><td>STARTING GAMES</td></tr>
                <tr>
                    <td><div class="gamecontainer active subpanel">
                        <div class="notfound">No active games</div>
                        </div>
                    </td>
                    <td>
                        <div class="gamecontainer lobby subpanel">
                        <div class="notfound">No starting games</div>
                        </div>
                    </td>
                    <td>
                        <a class="link" href="creategame.php">TUTORIAL</a>
                        <a class="link" href="creategame.php">AUTOMATCH</a>
                        <a class="link" href="creategame.php">CREATE GAME</a>
                    </td>
                </tr>
            </table>
			
			
		</div>
        <div id="globalchat" class="panel large" style="height:150px;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>
        
        <div id="globalhelp" class="helppanel">
        <?php
        	$messagelocation='games.php';
        	$ingame=false;
        	include("helper.php")
        ?>
        </div>

	</body>
</html>