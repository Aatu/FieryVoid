<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
session_start();

$gamedata = json_encode(Manager::getGamedata(1, 1));


?>


<!DOCTYPE HTML>
<html>
<head>
	<title>B5CGM main</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="B5CGM.css" rel="stylesheet" type="text/css">
	<script src="./engine/jquery-1.5.2.min.js"></script>
	<script src="./engine/elementlocation.js"></script>
	<script>
		var gamedata = {};
		var selectedid = -1;

		gamedata = <?php print($gamedata); ?>;
		
		
		jQuery(function($){
			populateUI();
		
		
			$(".taskforce.container").bind("click", selectTaskforce);
			$(".mapmarker.symbol").bind("click", selectTaskforce);
			
			$("#mapcontainer").bind("click", mapClick);
			$("#endturn").bind("click", endTurn);
			
			$("#pagecontainer").show();
		});
		
			
	</script>
	<script src="./engine/script.js"></script>
	<script src="./engine/mapchoosebox.js"></script>
	<script src="./engine/maporderhandlers.js"></script>
	<script src="./engine/gamelogic.js"></script>
	<script src="./engine/canvas.js"></script>
</head>


<body>
<?php
if (isset($_POST["action"]) && $_POST["action"] == "endturn"){

	
	$json = $_POST["gamedata"];
	Manager::submitGamedata("endturn", $json, 1,1);




}
?>

<h1>B5 CGM</h1>
<div id="pagecontainer" class="container hidden">
	

	<div class="left container">
		<div id="taskforcecontainer" class="container">
	
		</div>
		<div id="turncommands"><div id="endturn"><span>END TURN</span></div></div> 
		<form id="endturnform" method="post">
			<input type="hidden" name="action" value="endturn"></input>
			<input id="toPHP" type="hidden" name="gamedata" value=""></input>
		</form>
	</div>
	
	<div class="right container">
		<div id="mapcontainer" class="container" style="background-image:url(./maps/kurav.jpg)">
		<div style="position:absolute;top:0px;left:0px;width:800px;height:800px;">
			<canvas width="800" height="800" id="ordercanvas" class="mapordercanvas"></canvas>
		</div>
			<div id="mapchoosecontainer"><div class="dot"></div></div>
		</div>
	</div>
</div>

<div id="templatecontainer">
	
		<div class="taskforce container">
			<div class="symbol container">
				<div class="taskforcesymbol"></div>
			</div>
			<div class="row">
				<span class="dataentry">TASKFORCE</span>
				<span class="dataentry name"></span>
			</div>
			
			<div class="bottomrow">
			<table><tr>
				<td class="location"><span class="dataentry location"></span>
				</td><td><img style="margin-left:5px;" src="./img/scanner.png">
				<span class="dataentry bestscanner"></span>
				</td><td><img style="margin-left:5px;" src="./img/fighter.png">
				<span class="dataentry totalfighters"></span>
				</td><td><img class ="symbol speed" style="margin-left:5px;" src="./img/speed.png">
				<span class="dataentry speed"></span>
				</td><td><img class ="symbol jumpengine" style="margin-left:5px;" src="./img/jumpengine.png">
				<span class="dataentry jumpengine"></span>
			</td></tr></table>
			</div>
			
			<div class="taskforceorders container">
			<table>
				<tr>
				
				<?php 
					
					for ($i = 0; $i <= 10; $i++) {
						$turn = $i*100;
						print('<td class="turnindicator">'.$turn.'</td>');
					}
				
				?>
				</tr>
			</table>
			<table>
				<tr class="ordercontainer">
					<td style="display:none;"></td> <!-- for validity -->
				</tr>
			</table>
				
				
			</div>
			
			<div class="taskforceships container">
			
			</div>
			
			
		</div>
				
		
		
		<div class="ship container">
			<div class="ship symbol"></div>
			<div class="row"><span class="dataentry name"></span></div>
			<div class="row"><span class="dataentry type"></span></div>
			<div class="bottomrow">
				<div class="jumpengine systementry" style="left:2px;width:43px;">
					<div class="systementrypic" style="float:left;background-image:url(./img/jumpengine.png)"></div>
					<span class="dataentry jumpengine" style="float:right"></span>
				</div>
				<div class="systementry" style="left:52px;">
					<div class="systementrypic" style="float:left;background-image:url(./img/scanner.png)"></div>
					<span class="dataentry scanner" style="float:right"></span>
				</div>
				<div class="systementry" style="left:94px;">
					<div class="systementrypic" style="float:left;background-image:url(./img/fighter.png)"></div>
					<span class="dataentry fighters" style="float:right"></span>
				</div>


			</div>
		</div>
		
		<div class="mapmarkertaskforce container">
			<div class="mapmarker symbol"></div>
			<div class="mapmarker text">
				<span class="dataentry name"></span>
			</div>
		</div>
		
		<table><tr>
		<td class="order">
			<span class="dataentry type"></span>
		</td>
		</tr></table>
		
		<div class="orderboxorder move">
			
			<div data-name="alertlevel" class="choice">
				<span class="header">Alert level:</span>
				<span data-value="0" class="entry">Silent</span>
				<span data-value="1" class="entry">Safe</span>
				<span data-value="2" class="entry  selected">Cruise</span>
				<span data-value="3" class="entry">Patrol</span>
				<span data-value="4" class="entry">Combat</span>
			
			</div>
			<div data-name="hyperspace" class="choice jumpcapable">
				<span class="header">Jump to hyperspace:</span>
				<span data-value="1" class="entry yes selected">Yes</span>
				<span data-value="0" class="entry no ">No</span>
			</div>
			<div data-name="time" class="choice hidden">
				<span class="header">Turns:</span>
				<span data-value="0" class="selected">0</span>
			</div>
			
			<div data-name="fightersassigned" class="choice fighters hidden">
				<span class="header">Fighters:</span>
				<span data-value="0" class="0 entry selected">0</span>
				<span data-value="3" class="3 entry hidden">3</span>
				<span data-value="6" class="6 entry hidden">6</span>
				<span data-value="12" class="12 entry hidden">12</span>
			</div>
			
			<div class="confirm container"><span data-ordertype="move" class="ok">OK </span><span class="cancel"> CANCEL</span></div>
		</div>
		
		<div class="orderboxorder patr">
			
			<div data-name="alertlevel" class="choice">
				<span class="header">Alert level:</span>
				<span data-value="2" class="entry">Cruise</span>
				<span data-value="3" class="entry selected">Patrol</span>
				<span data-value="4" class="entry">Combat</span>
			
			</div>
			<div data-name="hyperspace" class="choice jumpcapable">
				<span class="header">Patrol from hyperspace:</span>
				<span data-value="1" class="entry yes">Yes</span>
				<span data-value="0" class="entry no selected">No</span>
			</div>
			<div data-name="time" class="choice">
				<span class="header">Turns:</span><input class="entry" data-value="100" type="text" maxlength="4" size="4" value="100"></input>

			</div>
			
			<div data-name="fightersassigned" class="choice fighters">
				<span class="header">Fighters:</span>
				<span data-value="0" class="0 entry selected">0</span>
				<span data-value="3" class="3 entry hidden">3</span>
				<span data-value="6" class="6 entry hidden">6</span>
				<span data-value="12" class="12 entry hidden">12</span>
			</div>
			
			<div class="confirm container"><span data-ordertype="patr" class="ok">OK </span><span class="cancel"> CANCEL</span></div>
		</div>
		
		
		
		
	
</div>

</body>

</html>