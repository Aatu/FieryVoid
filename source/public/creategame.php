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
		$defaultGameName = ucfirst($playerName) . "'s Game";

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
        <link href="styles/ladder.css" rel="stylesheet" type="text/css">
        <link href="styles/lobby.css" rel="stylesheet" type="text/css">
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">        
        <link href="styles/createGame.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<!--		<script src="client/helper.js"></script>-->
        <script src="client/mathlib.js"></script>
        <script src="client/UI/confirm.js"></script>
        <script src="client/UI/createGame.js"></script>
        <script src="client/ajaxInterface.js"></script>
        <script src="client/ladder.js"></script>        
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
      <form id="createGameForm" method="post">
      <section class="panel large create">
      <div class="panelheader"><span>CREATE YOUR GAME</span></div>
			
        <div class="game-options-wrapper">
            <div class="options-column">
                <div class="split-header" style = "margin-top: 10px">GAME OPTIONS:</div>
                <div class="gameNameSelectContainer">
                    <label for="gamename" class="gameNameSelect">GAME NAME:</label>
                    <input id="gamename" class="gamename" type="text" name="gamename" value="<?php print($defaultGameName); ?>">
                </div>    
            
                <!--<div class="split-header">BACKGROUND & GAME OPTIONS:</div>	-->					
                <!--<div class="createheader">CHOOSE A BACKGROUND:</div>-->
                <div class="backgroundSelectContainer">
                    <label for="backgroundSelect" class="background">BACKGROUND:</label>        
                    <select id="backgroundSelect" class="backgroundSelect" name="background">
                        <!--<option id="default_option" value="default">select ...</option>-->
                        <?php
                            natsort($maps); // Natural sort: sorts "1", "2", ..., "10", "11"
                            foreach ($maps as $name){
                                $displayName = $name;
                                $displayName = preg_replace('/^\d+\./', '', $displayName);
                                $displayName = preg_replace('/\.[^.]+$/', '', $displayName);
                                print("<option value=\"".$name."\">".$displayName."</option>");
                            }
                        
                        ?>
                    </select>
                </div>    



                <div class="settings-group movementspacecontainer">
                     <input id="laddercheck" type="checkbox" name="laddercheck"> <label for="laddercheck" class="clickable">Ladder Game</label>
                     <span class="btn-ladder btn-ladder-inline btn-create-game-ladder">View Ladder</span>
                </div>
            
                <div id="simultaenousMovement" class="settings-group movementspacecontainer">
                    <div>
                        <input id="movementcheck" type="checkbox" name="movementcheck"> <label for="movementcheck" class="clickable">Use Simultaneous Movement</label>
                    </div>
                    <div id="movementDropdown" class="movementDropdown">
                        <label for="initiativeSelect">Number of Brackets:</label>
                        <select id="initiativeSelect" name="initiativeCategories" class="initiativeCategories">
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
                        <input id="terraincheck" type="checkbox" name="terraincheck"> <label for="terraincheck" class="clickable">Add Terrain</label>
                    </div>

                    <div id="asteroidsDropdown" class="terrainDropdowns">
                        <div class="asteroidsDropdown">
                            <label for="asteroidsSelect" class="asteroidsSelect">Number of Asteroids:</label>
                            <select id="asteroidsSelect" name="asteroidsCategories" class="asteroidsCategories">
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
                    <div id="moonsDropdown" class="terrainDropdowns">
                        <div class="moonsDropdown">
                            <label for="moonsSmallSelect" class="moonsSelect">Small Moons:</label>
                            <select id="moonsSmallSelect" name="moonsSmall" class="moonsSmallSelect">
                                <option value="0">None</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>        
                            </select>
                        </div>
                        <div class="moonsDropdown">
                            <label for="moonsMediumSelect" class="moonsSelect">Medium Moons:</label>
                            <select id="moonsMediumSelect" name="moonsMedium" class="moonsMediumSelect">
                                <option value="0">None</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>        
                            </select>
                        </div>
                        <div class="moonsDropdown">
                            <label for="moonsLargeSelect" class="moonsSelect">Large Moons:</label>
                            <select id="moonsLargeSelect" name="moonsLarge" class="moonsLargeSelect">
                                <option value="0">None</option>
                                <option value="1">1</option>
                                <option value="2">2</option>        
                            </select>
                        </div>
                    </div>
                </div>    

                <div class="settings-group movementspacecontainer">
                     <input id="friendlyFireCheck" type="checkbox" name="friendlyFireCheck"> <label for="friendlyFireCheck" class="clickable">Friendly Fire</label>
                </div>

                <div id="desperate" class="settings-group movementspacecontainer">
                    <div>
                        <input id="desperatecheck" type="checkbox" name="desperatecheck"> <label for="desperatecheck" class="clickable">Desperate Scenario</label>
                    </div>    
                    
                    <div id="desperateDropdown" class="desperateDropdown">
                        <label for="desperateSelect">Apply Desparate rules to:</label>
                        <select id="desperateSelect" name="desperateCategories"  class="desparateSelect">
                            <option value="-1">Both teams</option>
                            <option value="1">Team 1</option>
                            <option value="2">Team 2</option>
                        </select>
                    </div>
                </div>

                <div class="settings-group movementspacecontainer">
                     <input id="unlimitedPointsCheck" type="checkbox" name="unlimitedPointsCheck"> <label for="unlimitedPointsCheck" class="clickable">Unlimited Points</label>
                </div>



            </div>
            
            <!-- Scenario Description (Right Column) -->
            <div class="scenario-form">
                <div class="split-header">SCENARIO DESCRIPTION:</div>

                <div class="scenario-row">
                    <label for="tier">EXPECTED POWER LEVEL:</label>
                    <select id="tier">
                        <option value="Any">Any</option>                        
                        <option value="Tier 1" selected>Tier 1</option>
                        <option value="Tier 2">Tier 2</option>
                        <option value="Tier 3">Tier 3</option>
                        <option value="Ancient">Ancient</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" id="tier_custom" class="scenario-custom-input" style="display:none;" placeholder="Enter power level...">
                </div>      

                <div class="scenario-row">
                    <label for="req">FLEET REQUIREMENTS:</label>
                    <select id="req">
                        <option value="Pass the fleet checker">Pass the fleet checker</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" id="req_custom" class="scenario-custom-input" style="display:none;" placeholder="Enter requirements...">
                </div>

                <div class="scenario-row">
                    <label for="customfactions">CUSTOM FACTIONS / UNITS:</label>
                    <select id="customfactions">
                        <option value="Allowed">Allowed</option>
                        <option value="Custom factions only">Custom Factions only</option>                        
                        <option value="Custom ships in official factions only">Custom Ships in official factions only</option>                         
                        <option value="Not allowed" selected>Not allowed</option>
                    </select>
                </div>

                <div class="scenario-row">
                    <label for="forbidden">FORBIDDEN FACTIONS:</label>
                    <input type="text" id="forbidden" value="None" class="forbiddenText">
                </div> 

                <div class="scenario-row">
                    <label for="enhancements">ENHANCEMENTS:</label>
                    <select id="enhancements">
                        <option value="Allowed">Allowed</option>
                        <option value="Up to X points">Up to X points</option>
                        <option value="Not allowed">Not allowed</option>
                    </select>
                    <input type="number" min="0" step="1" data-validation="^[0-9]+$" id="enhancements_custom" class="scenario-custom-input" style="display:none;" placeholder="Enter points...">
                </div>
        
                <div class="scenario-row">
                    <label for="borders">MAP BORDERS:</label>
                    <select id="borders">
                        <option value="Unit ending movement out of map is destroyed">Unit ending movement out of map is destroyed</option>
                        <option value="Unit leaving map is destroyed">Unit leaving map is destroyed</option>
                    </select>
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
                    <select id="victory">
                        <option value="Last unit on map">Last unit on map</option>
                        <option value="Last ship on map">Last ship on map</option>
                        <option value="More forces remaining after Turn 12">More forces remaining after Turn 12</option>
                        <option value="Other">Other</option>                    
                    </select>
                    <input type="text" id="victory_custom" class="scenario-custom-input" style="display:none;" placeholder="Enter victory conditions...">
                </div>
                <div class="scenario-row additional">
                    <label for="other">ADDITIONAL INFO:</label>
                    <textarea id="other" rows="3"></textarea>
                </div>     
            </div>
        </div>
    </section>

    <section class="panel large create" style="margin-top: 15px;">
        <!-- Moved Game Space Here (Below Scenario Description) -->
        <div class="split-header">MAP LAYOUT AND TEAMS:</div>
        <div id="gamespace" class="settings-group gamespacecontainer">
            <div class="mapSelectContainer">
                <label for="mapDimensionsSelect" class="mapDimensionsSelect">MAP TEMPLATES:</label>
                <select id="mapDimensionsSelect" name="mapdimensions" class="mapSelect">
                    <option value="custom">Custom</option>
                    <option value="small">Small (30x24)</option>
                    <option value="standard" selected>Standard (42x30)</option>
                    <option value="large">Large (60x40)</option>
                    <option value="2v2">2v2 (42x40)</option>
                    <option value="ambush">Ambush (42x40)</option>
                    <option value="baseAssault">Base Assault (60x40)</option>                    
                    <option value="convoyRaid">Convoy Raid (42x30)</option>
                    <option value="northvsouth">North Vs South (60x40)</option>                                                           
                    <option value="unlimited">No Boundaries</option>                    
                </select>
            </div>
            <div class="gamespacedefinition">
                <span class="mapDimensions">MAP SIZE:</span>
                <span class="unlimitedspace">
                    <span>No Boundaries</span>
                </span>
                <span class="limitedspace invisible">
                    <span>Width:</span>
                    <input class ="spacex tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="number" name="spacex" value="0">
                    <span>Height:</span>
                    <input class ="spacey tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="number" name="spacey" value="0">   

                </span>
            </div>
        </div>

        <!-- Split Layout for Teams and Map -->
        <div class="slots-map-wrapper">
            <!-- Left Column: Teams -->
            <div class="slots-column">
                <div class="createsubheader" style="margin-top:0;">TEAM 1:</div>
                <div id="team1" class="subpanel slotcontainer" style="border:none; background:transparent;"></div>
                <div><span class="clickable addslotbutton team1" class="add-slot">ADD SLOT</span></div>
                
                <div class="createsubheader">TEAM 2:</div>
                <div id="team2" class="subpanel slotcontainer" style="border:none; background:transparent;"></div>
                <div><span class="clickable addslotbutton team2" class="add-slot">ADD SLOT</span></div>
            </div>

            <!-- Right Column: Map Preview -->
            <div class="map-preview-column">
                <!--<div class="split-header"><span>DEPLOYMENT ZONE PREVIEW:</span></div>-->
                <div id="mapPreviewContainer" class="mapPreviewContainer">
                    <canvas id="mapPreview" width="545" height="390" class="mapPreviewContainerBox"></canvas>
                </div>
            </div>
        </div>
                
				
				<input type="hidden" name="docreate" value="true">

                <input id="createGameData" type="hidden" name="data" value="">

                <div style="text-align: right; margin-top: 10px; padding-bottom: 1px;">
                    <span class="btn btn-fleet-test" onclick="createGame.submitFleetTest()" style="margin-right: 15px;">Fleet Test</span>
                    <button type="submit" class="btn btn-create-submit create-game-btn" style="position: static; margin-top: 0; float: none;">
                        Create Game
                    </button>
                </div>                  
				

        

        <!-- Template for Slots (Hidden) -->
        <div id="slottemplatecontainer" style="display:none;">
            <div class="slot-card slot">
                <div class="header-row">
                    <div style="display:flex; align-items:center;">
                        <label class="slotName">SLOT NAME:</label>
                        <input class="name mediumSize" type="text" name="name" value="BLUE" style="min-width: 150px;">
                    </div>
                    <span class="clickable close remove-btn">Remove Slot</span>
                </div>
                <div class="create-row">
                    
                    <label>Points:</label>
                    <input class="points smallSize" type="text" data-validation="^[0-9]+$" name="points" value="0">
                    <span class="unlimited-label" style="display:none; font-weight:bold; color:#DEEBFF; margin-left:5px; padding: 5px;">Unlimited</span>
                    
                    <label>Deploys on Turn:</label>
                    <input class="depavailable tinySize" type="number" name="depavailable" value="1" min="1">
                </div>
                
                <div class="create-row2">
                    <label class="smallSize">Deployment:</label>
                    <label>x:</label>
                    <input class="depx tinySize" data-validation="^-{0,1}[0-9]+$" data-default="0" type="number" name="depx" value="0">
                    
                    <label>y:</label>
                    <input class="depy tinySize" type="number" name="depy" value="1">
                    
                    <label class="depwidthheader">Width:</label>
                    <input class="depwidth tinySize" type="number" name="depwidth" value="0">
                    
                    <label class="depheightheader">Height:</label>
                    <input class="depheight tinySize" type="number" name="depheight" value="0">
                </div>
            </div>
        </div>
        </section>
      </form>

        <div id="globalchat" class="panel large create" style="height:200px; margin-top: 15px;">
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

<?php include("ladder.php"); ?>
</body>
</html>
