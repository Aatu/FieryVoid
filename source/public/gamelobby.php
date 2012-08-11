<?php
    include_once 'global.php';
	
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}
    
    if (isset($_GET["leave"]) && isset($_GET["gameid"])){
		Manager::leaveLobbySlot($_SESSION["user"], $_GET["gameid"]);
		header('Location: games.php');
	}
	
	
	$gameid = null;
	
	if (isset($_GET["gameid"])){
		$gameid = $_GET["gameid"];
	}
	
	$gamelobbydata = Manager::getGameLobbyData( $_SESSION["user"], $gameid);
	if (!$gamelobbydata || $gamelobbydata->status != "LOBBY"){
		header('Location: games.php');
	}
	//var_dump($gamelobbydata);
	$gamelobbydataJSON = json_encode($gamelobbydata, JSON_NUMERIC_CHECK);
	
	$ships = json_encode(Manager::getAllShips(), JSON_NUMERIC_CHECK);
	
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>B5CGM</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<link href="styles/lobby.css" rel="stylesheet" type="text/css">
		<link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="client/gamelobby.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/player.js"></script>
		<script src="client/UI/confirm.js"></script>
		<script>
			
			jQuery(function($){
            
				gamedata.parseServerData(<?php print($gamelobbydataJSON); ?>);
				gamedata.parseShips(<?php print($ships); ?>);
				$('.readybutton').on("click", gamedata.onReadyClicked);
                $('.leave').on("click", gamedata.onLeaveClicked);
                $('.close').on("click", gamedata.onLeaveSlotClicked);
                $('.selectslot').on("click", gamedata.onSelectSlotClicked);
                $('.takeslot').on("click", gamedata.clickTakeslot);
				ajaxInterface.startPollingGamedata();
			});
		
		</script>
	</head>
	<body style="background-image:url(img/maps/<?php print($gamelobbydata->background); ?>)">
	
		<div class="panel large">
			<div class="logout"><a href="logout.php">LOGOUT</a></div>
			<div class="">	<span class="panelheader">GAME:</span><span class="panelsubheader"><?php print($gamelobbydata->name); ?></span>	</div>

			<div><span>TEAM 1</span></div>
			<div id="team1" class="subpanel slotcontainer">
			</div>
			
			<div><span>TEAM 2</span></div>
			<div id="team2" class="subpanel slotcontainer">
            </div>
            
            <!--<div class="slot" data-slotid="2" data-playerid=""><span>SLOT 2:</span></div>
			-->
			
			<span class="clickable leave">LEAVE GAME</a>
			
		</div>
		<div class="panel large buy" style="display:none;">
			<div><span class="panelheader" style="padding-right:20px;">PURCHASE YOUR FLEET</span>
				<span class="panelsubheader current">0</span>
				<span class="panelsubheader">/</span>
				<span class="panelsubheader max">0</span>
				<span class="panelsubheader">points</span>
				</div>
			<table class="store" style="width:100%;">
				<tr><td style="width:50%;vertical-align:top;">
					<div id="fleet" class="subpanel">
				</td><td style="width:50%;">
					<div id="store" class="subpanel">
				</td></tr>
			</table>
			
			<div><span class="clickable readybutton">READY</span></div>
			
		</div>
                    
        <div id="globalchat" class="panel large" style="height:150px;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>
                    
    <div id="slottemplatecontainer" style="display:none;">
        <div class="slot" >
            <div class="close"></div>
            <div>
                <span class="smallSize headerSpan">NAME:</span>
                <span class ="value name"></span>
                <span class="smallSize headerSpan">POINTS:</span>
                <span class ="value points"></span>
                <span class="smallSize headerSpan">PLAYER:</span>
                <span class="playername"></span><span class="status">READY</span>
                <span class="takeslot clickable">Take slot</span>
                <span class="selectslot clickable">SELECT</span>
            </div>
            <div>
                <span class="smallSize headerSpan">DEPLOYMENT:</span>
                <span>X:</span>
                <span class ="value depx"></span>
                <span>Y:</span>
                <span class ="value depy"></span>
                <span>Type:</span>
                <span class ="value deptype"></span>
                <span>Width:</span>
                <span class ="value depwidth"></span>
                <span>Height:</span>
                <span class ="value depheight"></span>
                <span>Turn available:</span>
                <span class ="value depavailable"></span>
            </div>
        </div>
    </div>

	</body>
</html>