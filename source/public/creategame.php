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
*** BELOW ARE COMMON PICKUP BATTLE OPTIONS - PICK YOUR OWN, OR FILL IN SOMETHING ELSE! ***
----------------------------
REQUIREMENTS: Pass the fleet checker / something else
CUSTOM FACTIONS: Allowed / Not allowed
CUSTOM UNITS IN OFFICIAL FACTIONS: Allowed / Not allowed
ENHANCEMENTS: Allowed / Allowed up to 100 points / Not allowed
EXPECTED POWER LEVEL: Tier 1 / Tier 2 / something else
FORBIDDEN FACTIONS: None 
MAP BORDERS: Unit leaving map is destroyed / Unit ending movement out of map is destroyed
CALLED SHOTS: Allowed / Not allowed
VICTORY CONDITIONS: Last unit on map / Last ship on map / More forces remaining after turn X
------------------------
				</textarea>
				
				
                <div style="margin-top:20px;"><h3>GAME SPACE</h3></div>
                <div id="gamespace" class="subpanel gamespacecontainer">
                    <div class="slot" >
                        <div>
                            <input id="gamespacecheck" type="checkbox" name="fixedgamespace" checked>USE LIMITED GAME SPACE
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
				    &nbsp;&nbsp;
				    <span class="clickable setsizeknifefight">Resize: Knife Fight (Small Map)</span> <!-- button switching map dimensions -->
				    &nbsp;&nbsp;				    
                    <span class="clickable setsizestandard">Resize: Standard (Normal Map)</span> <!-- button switching map dimensions -->
                    &nbsp;&nbsp;
				    <span class="clickable setswitchsizebaseassault">Resize: Base Assault (Large Map)</span> <!-- button switching map dimensions -->
                            </span>
                        </div>
                      <!---      <input id="flightSizeCheck" style="margin-top:20px;margin-bottom:10px;" type="checkbox" name="variableFlights">increased Flight size (up to 12 units per flight)
                  -->
                    </div>
                </div>



<!---
                <div style="margin-top:20px;"><h3>SIMULTANEOUS MOVEMENT</h3></div>
                <div id="simultaenousMovement" class="subpanel movementspacecontainer">
                    <div class="slot" >
                        <div>
                            <input id="movementcheck" type="checkbox" name="movementcheck">USE SIMULTANEOUS MOVEMENT
                        </div>
                    </div>
                </div>
-->
<div style="margin-top:20px;">
    <h3>GAME OPTIONS</h3>
    </div>

<div id="simultaenousMovement" class="subpanel movementspacecontainer">
    <div class="slot">
        <div>
            <input id="movementcheck" type="checkbox" name="movementcheck">USE SIMULTANEOUS MOVEMENT
        </div>
    </div>
    <div class="slot" id="movementDropdown" style="display:none;">
        <label for="initiativeSelect">NUMBER OF INITIATIVE GROUPS:</label>
        <select id="initiativeSelect" name="initiativeCategories">
            <!-- Dropdown options from 1 to 12 -->
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6" selected>6</option> <!-- Default selection -->
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>
    </div>
</div>

<div id="terrain" class="subpanel movementspacecontainer">
    <div class="slot">
        <div>
            <input id="terraincheck" type="checkbox" name="terraincheck">ADD TERRAIN
        </div>
    </div>

    <div class="slot" id="asteroidsDropdown" style="display:none;">
        <label for="asteroidsSelect">SELECT NUMBER OF ASTEROIDS:</label>
        <select id="asteroidsSelect" name="asteroidsCategories">
            <option value="0">None</option>              
            <option value="3">Few (3)</option>
            <option value="6">Several (6)</option>
            <option value="12">Pack (12)</option>
            <option value="18">Lots (18)</option>
            <option value="24">Horde (24)</option>
            <option value="36">Swarm (36)</option>
            <option value="48">Zounds (48)</option>
        </select>
    </div>

    <div class="slot" id="moonsDropdown" style="display:none;">
        <label for="moonsSelect">SELECT NUMBER OF MOONS:</label>
        <select id="moonsSelect" name="moonsCategories">
            <option value="0">None</option>           
            <option value="1">One Small</option>
            <option value="2">Two Small</option> 
            <option value="3">Three Small</option>
            <option value="4">Four Small</option>                                    
            <option value="5">One Large</option>
            <option value="6">Two Large</option>
            <option value="7">Three Large</option>                         
            <option value="8">One Large / One Small</option>
            <option value="9">One Large / Two Small</option>
            <option value="10">One Large / Three Small</option>                                
        </select>
    </div>
</div>

<div id="desperate" class="subpanel movementspacecontainer">
    <div class="slot">
        <div>
            <input id="desperatecheck" type="checkbox" name="desperatecheck">USE 'DESPERATE' SCENARIO RULES (e.g. Ramming / Deactivating Jump Drive are allowed)
        </div>
     </div>
    <div class="slot" id="desperateDropdown" style="display:none;">
        <label for="desperateSelect">DESPERATE RULES APPLY TO:</label>
        <select id="desperateSelect" name="desperateCategories">
            <option value="-1">Both</option>
            <option value="1">Team 1</option>
            <option value="2">Team 2</option>
        </select>
    </div>
</div>
                <div style="margin-top:20px;"><h3>TEAM 1</h3></div>
                <div id="team1" class="subpanel slotcontainer">
                    
                </div>
                <div><span class="clickable addslotbutton team1" style="margin-left: 5px;">ADD SLOT</span></div>
                
                <div><h3>TEAM 2</h3></div>
                <div id="team2" class="subpanel slotcontainer">
                    
                </div>
                <div><span class="clickable addslotbutton team2" style="margin-left: 5px;">ADD SLOT</span></div>
                
				
				<input type="hidden" name="docreate" value="true">

                <div style="margin-top:5px; text-align: center; text-decoration: underline;"><span>DEPLOYMENT ZONE PREVIEW</span></div>
                <!--<div id="mapPreviewContainer" style="margin-top: 0px;  text-align: center;"> -->                        
                <div id="mapPreviewContainer" style="margin-top: 0px;  margin-bottom: 20px; text-align: center;">
                    <canvas id="mapPreview" width="420" height="300"></canvas>
                </div>


                <input id="createGameData" type="hidden" name="data" value="">
				<input type="submit" style="margin-top: 0px; margin-bottom: 0px; position:absolute; left:8px; bottom:8px;" value="Create Game">                      
				
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
                    <input class ="depy tinySize" type="text" name="depy" value="1">
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
                	<input class="depheight tinySize" type="text" name="depheight" value="0">
                    <span>Turn Available:</span>
                    <input class ="depavailable tinySize" type="text" name="depavailable" value="0">                    
            		<!-- Add a Flexbox container here to align REMOVE SLOT to the right-->
            		<div class="flex-container">
                	<span class="clickable close">REMOVE SLOT</span>            
                    
                    

                </div>
            </div>
        </div>
	</body>
</html>
