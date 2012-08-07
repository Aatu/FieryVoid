<?php
    include_once 'global.php';
    
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
	}
	if (!Manager::canCreateGame($_SESSION["user"])){
		header('Location: games.php');
	}
	
	$maps = Manager::getMapBackgrounds();
	
	if (isset($_POST["docreate"]) && isset($_POST["name"])){
		
		$points = $_POST["points"];
		if (!is_numeric($points))
			$points = 1000;
			
		if ($points >20000)
			$points = 20000;
	
		$id = Manager::createGame($_POST["name"], $_POST["background"], 2, $points, $_SESSION["user"]);
		if ($id){
			header('Location: gamelobby.php');
		}
		
	}
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>FieryVoid</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
        <link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <link href="styles/lobby.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="client/UI/confirm.js"></script>
        <script src="client/UI/createGame.js"></script>
	</head>
	<body>
	
		<div class="panel large">
			<div class="panelheader">	<span>CREATE GAME</span>	</div>
			<form method="post">
			
				<div><span>Name:</span></div>
				<input type="text" name="name" value="">
						
				<div><span>Background:</span></div>
				<select id="mapselect" name="background">
					<!--<option id="default_option" value="default">select ...</option>-->
					<?php
						
						foreach ($maps as $name){
							
							print("<option value=\"".$name."\">".$name."</option>");
						}
					
					?>
				</select>
				<!--
				<div><span>Max players per side:</span></div>
				<select name="maxplayers">
					<option id="1" value="default">1</option>
					<option id="2" value="default">2</option>
					<option id="3" value="default">3</option>
					<option id="4" value="default">4</option>
					
				</select>
				-->
				
				<div><span>Points</span></div>
				<input type="text" name="points" value="1000">
                
                <div style="margin-top:20px;"><span>TEAM 1</span></div>
                <div id="team1" class="subpanel slotcontainer">
                    <div class="slot" data-slotid="1" >
                        <div>
                            <span class="smallSize headerSpan">SLOT:</span>
                            <input class ="slotname name mediumSize" type="text" name="slotname" value="BLUE">
                        </div>
                        <div>
                            <span class="smallSize headerSpan">DEPLOYMENT:</span>
                            <span>X:</span>
                            <input class ="depx tinySize" type="text" name="depx" value="0">
                            <span>Y:</span>
                            <input class ="depy tinySize" type="text" name="depy" value="0">
                            <span>Type</span>
                            <select name="maxplayers">
                                <option value="box">box</option>
                                <option value="circle">circle</option>
                                <option value="distance">distance</option>
                            </select>
                            <span>Width:</span>
                            <input class ="depwidth tinySize" type="text" name="depx" value="0">
                            <span>Height:</span>
                            <input class ="depheight tinySize" type="text" name="depy" value="0">
                        </div>
                    </div>
                </div>

                <div><span>TEAM 2</span></div>
                <div id="team1" class="subpanel slotcontainer">
                    <div class="slot" data-slotid="2" >
                        <span class="mediumSize headerSpan">SLOT:</span>
                        <input class ="slotname name mediumSize" type="text" name="slotname" value="RED">
                    </div>
                </div>
                
                
				
				<input type="hidden" name="docreate" value="true">
				<input type="submit" value="Create">
				
				
			</form>
			
		</div>

	</body>
</html>