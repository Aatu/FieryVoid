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
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
        <script src="client/mathlib.js"></script>
        <script src="client/UI/confirm.js"></script>
        <script src="client/UI/createGame.js"></script>
        <script src="client/ajaxInterface.js"></script>        
	</head>
	<body class="creategame">
  <header class="pageheader">
    <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
    <div class="top-right-row">
      <a href="games.php">Back to Game Lobby</a>        
      <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
  </header>

  <main class="container">
    <section class="panel large create">
      <div class="panelheader"><span>CREATE YOUR GAME</span></div>
			<form id="createGameForm" method="post">
			
        <div class="createheader" style = "margin-top: 10px">GAME NAME:</div>

				<input id="gamename" style= "margin-left: 0px" type="text" name="gamename" value="<?php print($defaultGameName); ?>">
						
        <div class="createheader">CHOOSE A BACKGROUND:</div>
				<select id="mapselect" name="background" style= "margin-left: 0px">
					<!--<option id="default_option" value="default">select ...</option>-->
					<?php
						natsort($maps); // Natural sort: sorts "1", "2", ..., "10", "11"
						foreach ($maps as $name){							
							print("<option value=\"".$name."\">".$name."</option>");
						}
					
					?>
				</select>
        

        <div class="createheader">GAME OPTIONS:</div>

        <div id="simultaenousMovement" class="settings-group movementspacecontainer">
            <div>
                <input id="movementcheck" type="checkbox" name="movementcheck"> <label for="movementcheck" class="clickable">USE SIMULTANEOUS MOVEMENT</label>
            </div>
            <div id="movementDropdown" style="display:none; margin-top:5px;">
                <label for="initiativeSelect">NUMBER OF INITIATIVE GROUPS:</label>
                <select id="initiativeSelect" name="initiativeCategories">
                    <!-- Dropdown options from 1 to 12 -->
        <?php 
        for($i=1;$i<=12;$i++){ 
            $selected = '';
            
            if($i==SimultaneousMovementRule::$defaultNoOfCategories){ //is this value set as default?
                $selected = 'selected';
            }
            
        print("<option value=\"".$i."\" ".$selected." >".$i."</option>");
        }          
              
        ?>
                </select>
            </div>
        </div>

        <div id="terrain" class="settings-group movementspacecontainer">
            <div>
                <input id="terraincheck" type="checkbox" name="terraincheck"> <label for="terraincheck" class="clickable">ADD TERRAIN</label>
            </div>

            <div id="asteroidsDropdown" style="display:none; margin-top:5px; margin-bottom:5px;">
                <div style="display:flex; align-items:center;">
                    <label for="asteroidsSelect" style="display:inline-block; width: 210px; margin: 0;">NUMBER OF ASTEROIDS:</label>
                    <select id="asteroidsSelect" name="asteroidsCategories" style="width: 150px;">
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
            </div>
            <div id="moonsDropdown" style="display:none; margin-top:10px;">
                <div style="margin-bottom:5px; display:flex; align-items:center;">
                    <label for="moonsSmallSelect" style="width: 210px; margin-bottom: 10px; display:inline-block;">SMALL MOONS:</label>
                    <select id="moonsSmallSelect" name="moonsSmall" style="width: 150px;">
                        <option value="0">None</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>        
                    </select>
                </div>
                <div style="margin-bottom:5px; display:flex; align-items:center;">
                    <label for="moonsMediumSelect" style="width: 210px; margin-bottom: 10px; display:inline-block;">MEDIUM MOONS:</label>
                    <select id="moonsMediumSelect" name="moonsMedium" style="width: 150px;">
                        <option value="0">None</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>        
                    </select>
                </div>
                <div style="margin-bottom:5px; display:flex; align-items:center;">
                    <label for="moonsLargeSelect" style="width: 210px; margin-bottom: 10px; display:inline-block;">LARGE MOONS:</label>
                    <select id="moonsLargeSelect" name="moonsLarge" style="width: 150px;">
                        <option value="0">None</option>
                        <option value="1">1</option>
                        <option value="2">2</option>        
                    </select>
                </div>
            </div>
        </div>    


        <div id="desperate" class="settings-group movementspacecontainer">
            <div>
                <input id="desperatecheck" type="checkbox" name="desperatecheck"> <label for="desperatecheck" class="clickable">USE 'DESPERATE' SCENARIO RULES</label>
            </div>    
            
            <div id="desperateDropdown" style="display:none; margin-top:5px;">
                <label for="desperateSelect">DESPERATE RULES APPLY TO:</label>
                <select id="desperateSelect" name="desperateCategories">
                    <option value="-1">Both teams</option>
                    <option value="1">Team 1</option>
                    <option value="2">Team 2</option>
                </select>
            </div>
        </div>

        
        
        <!-- Scenario Description (Full Width) -->
        <div class="scenario-form" style="margin-top: 20px;">
            <div class="split-header">SCENARIO DESCRIPTION:</div>
        
            <div class="scenario-row">
                <label for="req">FLEET REQUIREMENTS:</label>
                <input type="text" id="req" value="Pass the fleet checker / Other">
            </div>
            <div class="scenario-row">
                <label for="tier">EXPECTED POWER LEVEL:</label>
                <input type="text" id="tier" value="Tier 1 / Tier 2 / Tier 3 / Ancient / Other">
            </div>
            <div class="scenario-row">
                <label for="forbidden">FORBIDDEN FACTIONS:</label>
                <input type="text" id="forbidden" value="None">
            </div>                
            <div class="scenario-row">
                <label for="customfactions">CUSTOM FACTIONS / UNITS:</label>
                <input type="text" id="customfactions" value="Allowed / Not allowed">
            </div>
            <div class="scenario-row">
                <label for="enhancements">ENHANCEMENTS:</label>
                <input type="text" id="enhancements" value="Allowed / Up to 100 points / Not allowed">
            </div>
    
            <div class="scenario-row">
                <label for="borders">MAP BORDERS:</label>
                <input type="text" id="borders" value="Unit leaving map is destroyed / Unit ending movement out of map is destroyed">
            </div>
            <div class="scenario-row">
                <label for="called">CALLED SHOTS:</label>
                <select id="called">
                    <option value="Allowed" selected>Allowed</option>
                    <option value="Not allowed">Not allowed</option>
                </select>
            </div>
            <div class="scenario-row">
                <label for="victory">VICTORY CONDITIONS:</label>
                <input type="text" id="victory" value="Last unit on map / Last ship on map / More forces remaining after Turn 12">
            </div>
            <div class="scenario-row">
                <label for="other">ADDITIONAL INFO:</label>
                <textarea id="other" rows="3" style="width: 100%; resize: vertical;"></textarea>
            </div>     
        </div>

        <!-- Moved Game Space Here (Below Scenario Description) -->
        <div class="split-header" style="margin-top: 20px;">GAME SPACE:</div>
        <div id="gamespace" class="settings-group gamespacecontainer">
            <div>
                <input id="gamespacecheck" type="checkbox" name="fixedgamespace" checked> <label for="gamespacecheck" class="clickable">SET MAP BOUNDARIES</label>
            </div>
            <div class="gamespacedefinition">
                <span class="smallSize headerSpan">MAP DIMENSIONS:</span>
                <span class="unlimitedspace">
                    <span>NO BOUNDARIES</span>
                </span>
                <span class="limitedspace invisible">
                    <span>WIDTH:</span>
                    <input class ="spacex tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="number" name="spacex" value="0">
                    <span>HEIGHT:</span>
                    <input class ="spacey tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="number" name="spacey" value="0">   
                    &nbsp;&nbsp;
                    <span style="margin-left: 20px;">PRESETS:</span>
                    <span class="clickable setsizeknifefight" style="text-decoration: underline; color: #8bcaf2; cursor:pointer;" title="30x24">KNIFE FIGHT</span>
                    &nbsp;/&nbsp;				    
                    <span class="clickable setsizestandard" style="text-decoration: underline; color: #8bcaf2; cursor:pointer;" title="42x30">STANDARD</span>
                    &nbsp;/&nbsp;
                    <span class="clickable setswitchsizebaseassault" style="text-decoration: underline; color: #8bcaf2; cursor:pointer;" title="60x40">BASE ASSAULT</span>
                </span>
            </div>
        </div>

        <!-- Split Layout for Teams and Map -->
        <div class="slots-map-wrapper">
            <!-- Left Column: Teams -->
            <div class="slots-column">
                <div class="createsubheader" style="margin-top:0;">TEAM 1:</div>
                <div id="team1" class="subpanel slotcontainer" style="border:none; background:transparent;"></div>
                <div><span class="clickable addslotbutton team1" style="margin-left: 5px; margin-top:5px; color: #8bcaf2">ADD SLOT</span></div>
                
                <div class="createsubheader">TEAM 2:</div>
                <div id="team2" class="subpanel slotcontainer" style="border:none; background:transparent;"></div>
                <div><span class="clickable addslotbutton team2" style="margin-left: 5px; margin-top:5px; color: #8bcaf2">ADD SLOT</span></div>
            </div>

            <!-- Right Column: Map Preview -->
            <div class="map-preview-column">
                <div class="split-header"><span>DEPLOYMENT ZONE PREVIEW:</span></div>
                <div id="mapPreviewContainer" style="margin-top: 10px;  margin-bottom: 20px; text-align: center;">
                    <canvas id="mapPreview" width="420" height="300" style="border: 1px solid #215a7a; border-radius: 4px;"></canvas>
                </div>
            </div>
        </div>
                
				
				<input type="hidden" name="docreate" value="true">

                <input id="createGameData" type="hidden" name="data" value="">

                <div style="text-align: right; margin-top: 10px; padding-bottom: 1px;">
                    <button type="submit" class="btn btn-success-create create-game-btn" style="position: static; margin-top: 0; float: none;">
                        Create Game
                    </button>
                </div>                  
				
			</form>
			
		</div>
        

        <!-- Template for Slots (Hidden) -->
        <div id="slottemplatecontainer" style="display:none;">
            <div class="slot-card slot">
                <div class="header-row">
                    <span class="header-title">SLOT</span>
                    <span class="clickable close remove-btn">REMOVE SLOT</span>
                </div>
                <div class="create-row">
                    <label>NAME:</label>
                    <input class="name mediumSize" type="text" name="name" value="BLUE">
                    
                    <label>POINTS:</label>
                    <input class="points smallSize" type="number" name="points" value="0">
                    
                    <label>DEPLOYS ON TURN:</label>
                    <input class="depavailable tinySize" type="number" name="depavailable" value="1" min="1">
                </div>
                
                <div class="create-row" style="margin-top: 5px; border-top: 1px dashed #215a7a; padding-top: 5px;">
                    <label class="smallSize">DEPLOYMENT:</label>
                    <label>X:</label>
                    <input class="depx tinySize" data-validation="^-{0,1}[0-9]+$" data-default="0" type="number" name="depx" value="0">
                    
                    <label>Y:</label>
                    <input class="depy tinySize" type="number" name="depy" value="1">
                    
                    <label class="depwidthheader">WIDTH:</label>
                    <input class="depwidth tinySize" type="number" name="depwidth" value="0">
                    
                    <label class="depheightheader">HEIGHT:</label>
                    <input class="depheight tinySize" type="number" name="depheight" value="0">
                </div>
            </div>
        </div>
        </section>

        <div id="globalchat" class="panel large create" style="height:200px;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>

  </main>

<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

</body>
</html>
