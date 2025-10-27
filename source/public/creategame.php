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
	</head>
	<body class="creategame">
  <header class="pageheader">
    <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
    <div class="top-right-row">
      <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
  </header>
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
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


        <div class="createheader" style = "margin-bottom: 15px">SCENARIO DESCRIPTION:</div>

        <div class="scenario-form">
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
        <!--<div class="scenario-row">
            <label for="customunits">CUSTOM UNITS IN OFFICIAL FACTIONS:</label>
            <input type="text" id="customunits" value="Allowed / Not allowed">
        </div>-->
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
				
				
                <div class="createsubheader">GAME SPACE:</div>
                <div id="gamespace" class="subpanel gamespacecontainer">
                    <div class="slot" >
                        <div>
                            <input id="gamespacecheck" type="checkbox" name="fixedgamespace" checked>SET MAP BOUNDARIES
                        </div>
                        <div class="gamespacedefinition">
                            <span class="smallSize headerSpan" style="margin-top: 5px; margin-left: 5px">MAP DIMENSIONS:</span>
                            <span class="unlimitedspace">
                                <span>NO BOUNDARIES</span>
                            </span>
                            <span class="limitedspace invisible">
                                <span>WIDTH:</span>
                                <input class ="spacex tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="spacex" value="0">
                                <span>HEIGHT:</span>
                                <input class ="spacey tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="spacey" value="0">   
				    &nbsp;&nbsp;
				    <span style = "margin-left: 40px;">RESIZE MAP:</span><span class="clickable setsizeknifefight" style = "text-decoration: underline; color: #8bcaf2">KNIFE FIGHT (SMALL MAP)</span> <!-- button switching map dimensions -->
				    &nbsp;&nbsp;				    
                    <span class="clickable setsizestandard" style = "text-decoration: underline; color: #8bcaf2">STANDARD (NORMAL MAP)</span> <!-- button switching map dimensions -->
                    &nbsp;&nbsp;
                    <span class="clickable setswitchsizebaseassault" style = "text-decoration: underline; color: #8bcaf2">BASE ASSAULT (LARGE MAP)</span> <!-- button switching map dimensions -->
                            </span>
                        </div>
                      <!---      <input id="flightSizeCheck" style="margin-top:20px;margin-bottom:10px;" type="checkbox" name="variableFlights">increased Flight size (up to 12 units per flight)
                  -->
                    </div>
                </div>

<div class="createsubheader">GAME OPTIONS:</div>

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

<div id="terrain" class="subpanel movementspacecontainer">
    <div class="slot">
        <div>
            <input id="terraincheck" type="checkbox" name="terraincheck">ADD TERRAIN
        </div>
    </div>

    <div class="slot" id="asteroidsDropdown" style="display:none;">
        <label for="asteroidsSelect">NUMBER OF ASTEROIDS:</label>
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
<!--
    <div class="slot" id="moonsDropdown" style="display:none;">
        <label for="moonsSelect">SELECT NUMBER OF MOONS:</label>
        <select id="moonsSelect" name="moonsCategories">
            <option value="0">None</option>           
            <option value="1">One Small</option>
            <option value="2">Two Small</option> 
            <option value="3">Three Small</option>
            <option value="4">Four Small</option>                                    
            <option value="5">One Medium</option>
            <option value="6">Two Medium</option>
            <option value="7">Three Medium</option>                         
            <option value="8">One Medium / One Small</option>
            <option value="9">One Medium / Two Small</option>
            <option value="10">One Medium / Three Small</option>
            <option value="11">One Large / One Small</option>
            <option value="12">One Large / One Medium</option>
            <option value="13">One Large / One Medium / One Small</option>                                               
        </select>
    </div>
</div>
-->

<div class="slot" id="moonsDropdown" style="display:none;">
    <label for="moonsSmallSelect">SMALL MOONS:</label>
    <select id="moonsSmallSelect" name="moonsSmall">
        <option value="0">None</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>        
    </select>
    <br>
    <label for="moonsMediumSelect">MEDIUM MOONS:</label>
    <select id="moonsMediumSelect" name="moonsMedium">
        <option value="0">None</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>        
    </select>
    <br>
    <label for="moonsLargeSelect">LARGE MOONS:</label>
    <select id="moonsLargeSelect" name="moonsLarge">
        <option value="0">None</option>
        <option value="1">1</option>
        <option value="2">2</option>        
    </select>
</div>


<div id="desperate" class="subpanel movementspacecontainer">
    <div class="slot">
        <div>
            <input id="desperatecheck" type="checkbox" name="desperatecheck">USE 'DESPERATE' SCENARIO RULES
        </div>
     </div>
    <div class="slot" id="desperateDropdown" style="display:none;">
        <label for="desperateSelect">DESPERATE RULES APPLY TO:</label>
        <select id="desperateSelect" name="desperateCategories">
            <option value="-1">Both teams</option>
            <option value="1">Team 1</option>
            <option value="2">Team 2</option>
        </select>
    </div>
</div>
                <div class="createsubheader">TEAM 1:</div>
                <div id="team1" class="subpanel slotcontainer">
                    
                </div>
                <div><span class="clickable addslotbutton team1" style="margin-left: 5px; margin-top:5px; color: #8bcaf2">ADD SLOT</span></div>
                
                <div class="createsubheader">TEAM 2:</div>
                <div id="team2" class="subpanel slotcontainer">
                    
                </div>
                <div><span class="clickable addslotbutton team2" style="margin-left: 5px; margin-top:5px; color: #8bcaf2">ADD SLOT</span></div>
                
				
				<input type="hidden" name="docreate" value="true">

                <div class="createsubheader" style="margin-top:5px; text-align: center;"><span>DEPLOYMENT ZONE PREVIEW:</span></div>
                <!--<div id="mapPreviewContainer" style="margin-top: 0px;  text-align: center;"> -->                        
                <div id="mapPreviewContainer" style="margin-top: 0px;  margin-bottom: 20px; text-align: center;">
                    <canvas id="mapPreview" width="420" height="300"></canvas>
                </div>


                <input id="createGameData" type="hidden" name="data" value="">

                <button type="submit" class="btn btn-success-create create-game-btn">
                    Create Game
                </button>                  
				
			</form>
			
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
                <div style="margin-top:5px; margin-bottom:5px;">
                    <span class="smallSize headerSpan">NAME:</span>
                    <input class ="name mediumSize" type="text" name="name" value="BLUE">
                    <span class="smallSize headerSpan">POINTS:</span>
                    <input class ="points smallSize" type="text" name="points" value="0">
                </div>
                <div style="margin-top: 5px">
                    <span class="smallSize headerSpan">DEPLOYMENT:</span>
                    <span>X:</span>
                    <input class ="depx tinySize" data-validation="^-{0,1}[0-9]+$" data-default ="0" type="text" name="depx" value="0">
                    <span>Y:</span>
                    <input class ="depy tinySize" type="text" name="depy" value="1">
                <!--<span>Type</span> options other than 'box' do not work correctly, I'm disabling them
				    <select class="deptype" name="deptype" style="margin-right: 5px">
                        <option value="box">box</option>
                        <option value="circle">circle</option>
                        <option value="distance">distance</option>
						
                    </select>-->
                    <span class="depwidthheader">WIDTH:</span>
                    <input class ="depwidth tinySize" type="text" name="depwidth" value="0">
                	<span class="depheightheader">HEIGHT:</span>
                	<input class="depheight tinySize" type="text" name="depheight" value="0">
                    <span>DEPLOYS ON TURN:</span>
                    <input class="depavailable tinySize" type="number" name="depavailable" value="1" min="1">                   
            		<!-- Add a Flexbox container here to align REMOVE SLOT to the right-->
            		<div class="flex-container">
                	<span class="clickable close" style="color: #8bcaf2">REMOVE SLOT</span>            
                    
                    

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



</body>
</html>
