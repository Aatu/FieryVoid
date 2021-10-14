<?php
    include_once 'global.php';
    
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}
	if (!Manager::canCreateGame($_SESSION["user"])){
		header('Location: games.php');
	}
	
	$maps = Manager::getMapBackgrounds();
	$defaultGameName = 'GAME NAME' . $_SESSION["user"];	
	$playerName = Manager::getPlayerName($_SESSION["user"]);
	//if ($playerName != '')	
		$defaultGameName = 'Game of ' . $playerName;

	if (isset($_POST["docreate"]) && isset($_POST["data"])){
		$id = Manager::createGame($_SESSION["user"], $_POST["data"]);
		if ($id){
			header("Location: gamelobby.php?gameid=$id");
		}
		
	}
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Create game</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
        <link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <link href="styles/lobby.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
        <script src="client/mathlib.js"></script>
        <script src="client/UI/confirm.js"></script>
        <script src="client/UI/createGame.js"></script>
	</head>
	<body class="creategame">
	
        <img src="img/logo.png">
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
		<div class="panel large">
			<div class="panelheader">	<span>CREATE GAME</span>	</div>
			<form id="createGameForm" method="post">
			
				<div><span>Name:</span></div>
				<input id="gamename" type="text" name="gamename" value="<?php print($defaultGameName); ?>">
						
				<div><span>Background:</span></div>
				<select id="mapselect" name="background">
					<!--<option id="default_option" value="default">select ...</option>-->
					<?php
						
						foreach ($maps as $name){							
							print("<option value=\"".$name."\">".$name."</option>");
						}
					
					?>
				</select>
				<div><span>Scenario description:</span></div>
				<textarea id="description" name="description" rows="10" cols="100">
DEFAULT SCENARIO DESCRIPTION
----------------------------
REQUIREMENTS: please pass the fleet checker :)
CUSTOM FACTIONS: not allowed
CUSTOM UNITS IN OFFICIAL FACTIONS: not allowed
ENHANCEMENTS: allowed but don't overdo it (up to 100 points)
EXPECTED POWER LEVEL: similar to Big 4
FORBIDDEN FACTIONS: none 
------------------------
				</textarea>
				
				
                <div style="margin-top:20px;"><h3>GAME SPACE</h3></div>
                <div id="gamespace" class="subpanel gamespacecontainer">
                    <div class="slot" >
                        <div>
                            <input id="gamespacecheck" type="checkbox" name="fixedgamespace">USE LIMITED GAME SPACE
                        </div>
                        <div class="gamespacedefinition" style="height:24px;vertical-align:middle;position:relative">
                            <span class="smallSize headerSpan">GAME SPACE SIZE:</span>
                            <span class="unlimitedspace">
                                <span>Unlimited</span>
                            </span>
                            <span class="limitedspace invisible">
                                <span>Width:</span>
                                <input class ="spacex tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="spacex" value="0">
                                <span>Height:</span>
                                <input class ="spacey tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="spacey" value="0">                
                            </span>
                        </div>
                      <!---      <input id="flightSizeCheck" style="margin-top:20px;margin-bottom:10px;" type="checkbox" name="variableFlights">increased Flight size (up to 12 units per flight)
                  -->
                    </div>
                </div>

                <div style="margin-top:20px;"><h3>SIMULTANEOUS MOVEMENT</h3></div>
                <div id="simultaenousMovement" class="subpanel movementspacecontainer">
                    <div class="slot" >
                        <div>
                            <input id="movementcheck" type="checkbox" name="movementcheck">USE SIMULTAENOUS MOVEMENT
                        </div>
                    </div>
                </div>
                
                <div style="margin-top:20px;"><h3>TEAM 1</h3></div>
                <div id="team1" class="subpanel slotcontainer">
                    
                </div>
                <div><span class="clickable addslotbutton team1">ADD SLOT</span></div>
                
                <div><h3>TEAM 2</h3></div>
                <div id="team2" class="subpanel slotcontainer">
                    
                </div>
                <div><span class="clickable addslotbutton team2">ADD SLOT</span></div>
                
				
				<input type="hidden" name="docreate" value="true">
                <input id="createGameData" type="hidden" name="data" value="">
				<input type="submit" value="Create">
				
				
			</form>
			
		</div>
        
        <div id="globalchat" class="panel large" style="height:150px;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>



<!--        <div id="gamespacetemplatecontainer">
            <div class="slot" >
                <div>
                    <input id="gamespacecheck" type="checkbox" name="fixedgamespace">USE LIMITED GAME SPACE
                </div>
                <div class="gamespacedefinition" style="height:24px;vertical-align:middle;position:relative">
                    <span class="smallSize headerSpan">GAME SPACE SIZE:</span>
                    <span class="unlimitedspace">
                        <span>Unlimited</span>
                    </span>
                    <span class="limitedspace invisible">
                        <span>Width:</span>
                        <input class ="spacex tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="spacex" value="0">
                        <span>Height:</span>
                        <input class ="spacey tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="spacey" value="0">                
                    </span>
                </div>
            </div>
        </div> -->
        
        <div id="slottemplatecontainer" style="display:none;">
            <div class="slot" >
                <div class="close"></div>
                <div>
                    <span class="smallSize headerSpan">NAME:</span>
                    <input class ="name mediumSize" type="text" name="name" value="BLUE">
                    <span class="smallSize headerSpan">POINTS:</span>
                    <input class ="points smallSize" type="text" name="points" value="0">
                </div>
                <div>
                    <span class="smallSize headerSpan">DEPLOYMENT:</span>
                    <span>X:</span>
                    <input class ="depx tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="depx" value="0">
                    <span>Y:</span>
                    <input class ="depy tinySize" type="text" name="depy" value="0">
                    <span>Type</span>
                    <select class="deptype" name="deptype">
                        <option value="box">box</option>
						<!-- options other than 'box' do not work correctly, I'm disabling them
                        <option value="circle">circle</option>
                        <option value="distance">distance</option>
						-->
                    </select>
                    <span class="depwidthheader">Width:</span>
                    <input class ="depwidth tinySize" type="text" name="depwidth" value="0">
                    <span class="depheightheader">Height:</span>
                    <input class ="depheight tinySize" type="text" name="depheight" value="0">
                    <span>Turn available:</span>
                    <input class ="depavailable tinySize" type="text" name="depavailable" value="0">
                </div>
            </div>
        </div>
	</body>
</html>
