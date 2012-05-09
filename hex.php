<?php 
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	array_walk(glob('./engine/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/ships/*/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/weapons/*.php'), create_function('$v,$i', 'return require_once($v);'));
	array_walk(glob('./engine/tactical/*.php'), create_function('$v,$i', 'return require_once($v);'));
	session_start();


	$gameid = 1;
	$thisplayer = -1;

	if (isset($_GET["gameid"])){
		$gameid = $_GET["gameid"];
	}else{
		//header('Location: games.php');
	}
	
	if (isset($_SESSION["user"])){
		$thisplayer = $_SESSION["user"];
	}
	
	$serverdata = Manager::getTacGamedata($gameid, $thisplayer, -1, 2, -1);
	
	$serverdataJSON = json_encode($serverdata, JSON_NUMERIC_CHECK);
	
	
	//print($serverdataJSON );

	//phpinfo();


?>


<!DOCTYPE HTML>
<html>
<head>
    <title>B5CGM</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="tactical.css" rel="stylesheet" type="text/css">
    <link href="shipwindow.css" rel="stylesheet" type="text/css">
	<link href="./engine/tactical/UI/confirm.css" rel="stylesheet" type="text/css">
    <script src="./engine/jquery-1.5.2.min.js"></script>
    <script src="./engine/jquery-ui-1.8.15.custom.min.js"></script>
    <script src="./engine/elementlocation.js"></script>
    <script>
					
        jQuery(function($){
            
            gamedata.parseServerData(<?php print($serverdataJSON); ?>);
            getWindowDimensions();
            resizeGame();
            //shipWindowManager.createShipWindow(gamedata.getActiveShip());
            
			if (gamedata.thisplayer == -1){
				$(".notlogged").show();
				$(".waiting").hide();
				gamedata.waiting = false;
			}
            $("#pagecontainer").show();
        });
        
            
    </script>
	<script src="./engine/tactical/UI/botPanel.js"></script>
    <script src="./engine/tactical/hexgrid.js"></script>
    <script src="./engine/tactical/gamedata.js"></script>
    <script src="./engine/tactical/windowevents.js"></script>
    <script src="./engine/tactical/mousewheel.js"></script>
    <script src="./engine/tactical/mathlib.js"></script>
    <script src="./engine/tactical/debug.js"></script>
    <script src="./engine/tactical/ajaxInterface.js"></script>
    <script src="./engine/tactical/ew.js"></script>
    <script src="./engine/tactical/EWindicators.js"></script>
    <script src="./engine/tactical/weaponManager.js"></script>
    <script src="./engine/tactical/damage.js"></script>
	<script src="./engine/tactical/Effects.js"></script>
	<script src="./engine/tactical/combatLog.js"></script>
	<script src="./engine/tactical/player.js"></script>
	<script src="./engine/graphics.js"></script>
    <script src="./engine/tactical/ships.js"></script>
    <script src="./engine/tactical/movement.js"></script>
    <script src="./engine/tactical/criticals.js"></script>
    <script src="./engine/tactical/systems.js"></script>
	<script src="./engine/tactical/power.js"></script>
    <script src="./engine/tactical/UI/shipMovement.js"></script>
    <script src="./engine/tactical/UI/infowindow.js"></script>
	<script src="./engine/tactical/UI/systemInfo.js"></script>
    <script src="./engine/tactical/animation.js"></script>
    <script src="./engine/tactical/UI/shipwindow.js"></script>
    <script src="./engine/tactical/UI/flightwindow.js"></script>
    <script src="./engine/tactical/UI/shipclickable.js"></script>
	<script src="./engine/tactical/UI/confirm.js"></script>
	<script src="./engine/tactical/UI/shipSelect.js"></script>
	<script src="./engine/tactical/ballistics.js"></script>
	<script src="./engine/tactical/damageDrawer.js"></script>
	
</head>


<body style="background-image:url(./maps/<?php print($serverdata->background); ?>)">


<div id="phaseheader" class="roundedright" style="">
    <span class="turn value"></span><span class="phase value"></span><span class="activeship value"></span><span class="waiting value"></span><span class="finished value">GAME OVER</span><span class="notlogged value">NOT LOGGED IN</span>
    <table class="uitable">
        <tr>
        <td class="committurn"><div class="ok" ></div></td>
        <td class="cancelturn"><div class="cancel" ></div></td>
        </tr>
    </table>
</div>

<div id="infowindow">
<h2></h2>
<div class="container"></div>
</div>

<div id="systemtemplatecontainer" style="display:none;">
    
	<div class="structure system">
        <div class="name"><span class="namevalue">STRUCTURE</span></div>
        <div class="systemcontainer">

            <div class="health systembarcontainer">
                <div class="healthbar bar" style="width:40px;"></div>
                <div class="valuecontainer"><span class="healthvalue value"></span></div>
            </div>
                       
            <div class="UI">
                <div class="button1"></div>
                <div class="button2"></div>
            </div>
        </div>
    </div>
	
	<div class="fightersystem">
		<div class="icon">
			<span class="efficiency value"></span>
			<div class="iconmask"></div>
        </div>
	</div>

    <div class="system regular">
        <!--<div class="name"><span class="namevalue">Heavy Laser</span></div>-->
        <div class="systemcontainer">
            <div class="icon">
                <span class="efficiency value"></span>
				<div class="iconmask"></div>
            </div>
			<div class="UI">
				<div class="button stopoverload"></div>
				<div class="button overload"></div>
				<div class="button plus"></div>
				<div class="button minus"></div>
				<div class="button off"></div>
				<div class="button on"></div>
				<div class="button holdfire"></div>
			</div>
            <div class="health systembarcontainer">
                <div class="healthbar bar" style="width:40px;"></div>
                <div class="valuecontainer"><span class="healthvalue value"></span></div>
            </div>
            <!--
            <div class="systembarcontainer">
                <div class="efficiencybar bar" style="width:30px;"></div>
                <div class="valuecontainer"><span class="efficiencyvalue value">5/3<span></div>
            </div>
            -->
            <div class="critical systembarcontainer">
                <div class="valuecontainer"><span class="criticalvalue value">CRITICAL<span></div>
            </div>
            
        </div>
    </div>
    
    <div class="fighter">
		<div class="destroyedtext"><span>DESTROYED</span></div>
        <!--<div class="name"><span class="namevalue">Heavy Laser</span></div>-->
        <div class="systemcontainer">
            <div class="icon">
				<table class="fightersystemcontainer 1"><tr></tr></table>
				<div style="height:90px;"></div>
				<table class="fightersystemcontainer 2"><tr></tr></table>
            </div>
			
            <div class="health systembarcontainer">
                <div class="healthbar bar" style="width:90px;"></div>
                <div class="valuecontainer"><span class="healthvalue value"></span></div>
            </div>
            <!--
            <div class="systembarcontainer">
                <div class="efficiencybar bar" style="width:30px;"></div>
                <div class="valuecontainer"><span class="efficiencyvalue value">5/3<span></div>
            </div>
            -->
            
            
        </div>
    </div>

</div>

<div id="shipwindowtemplatecontainer" style="display:none;">
    
    <div class="shipwindow ship">
        <div class="topbar">
			<span class="valueheader name">Name:</span><span class="value name">name here</span>
            <span class="valueheader shipclass">Class:</span><span class="value shipclass">ship type class here</span>
            <div class="close"></div>
        </div>
      
               
            
                        
            
		<table class="divider">
			<tr><td class="col1">
				<div class="icon"><img src="" alt="" border="" width="120" height="120"></div>
				<div class="notes"></div>
				<div id="shipSection_3" class="shipSection">
					<table></table>
				</div>
			</td><td class="col2">
				<div id="shipSection_1" class="shipSection">
					<table></table>
				</div>
				<div id="shipSection_0" class="shipSection primary">
					<table></table>
				</div>
				<div id="shipSection_2" class="shipSection">
					<table></table>
				</div>
			</td><td class="col3">
				<div class="EW">
					<div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
					<div class="EWcontainer">
						<div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
						<div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
					</div>
				</div>
				<div id="shipSection_4" class="shipSection">
					<table></table>
				</div>
				
			</td></tr>
		</table>
            
            
        
       
        
        
    </div>
    
    <div class="shipwindow flight">
        <div class="topbar">
			<span class="valueheader name">Name:</span><span class="value name">name here</span>
            <span class="valueheader shipclass">Class:</span><span class="value shipclass"></span>
            <div class="close"></div>
        </div>
      
               
            
                        
            
		<table class="divider">
			<tr>
				<td class="fightercontainer 0"></td>
				<td class="fightercontainer 1"></td>
				<td class="fightercontainer 2"></td>
			</tr>
			<tr>
				<td class="fightercontainer 3"></td>
				<td class="fightercontainer 4"></td>
				<td class="fightercontainer 5"></td>
			</tr>
		</table>
            
            
        
       
        
        
    </div>

</div>

<div id="pagecontainer" oncontextmenu="return false;">
<canvas width="0" height="0" id="hexgrid" class="tacticalcanvas"></canvas>
<canvas width="0" height="0" id="EWindicators" class="tacticalcanvas"></canvas>
<canvas width="0" height="0" id="effects" class="tacticalcanvas"></canvas>

	<div id="systemInfo">
		<div class="name"><span class="name header">test</span></div>
		<div class="datacontainer"></div>
	</div>
	
    <div id="shipNameContainer">
        <div class="namecontainer" style="border-bottom:1px solid white;margin-bottom:3px;"></div>
		<div><span class="iniative value">Speed: 11</span></div>
		<div><span class="speed value">Speed: 11</span></div>
		<div><span class="turndelay value">Turn delay: 5</span></div>
		<div><span class="rolling value">Turn delay: 5</span></div>
		<div><span class="rolled value">Turn delay: 5</span></div>
		<div><span class="pivoting value">Turn delay: 5</span></div>
		<div class="fire" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>TARGETING</span></div>
		<div class="fire targeting"></div>
    </div>
    
	
    <div id="weaponTargetingContainer">
        <div class="infolist">
        
        </div>
    </div>
    
    
    
    
    <div id="shipMovementUI">
        <div id="move">
            <div class="centercontainer">
                <span class="speedvalue value centercontent">00</span>
            </div>
            <canvas id="movecanvas" width="50" height="50"></canvas>
        </div>
        
        <div id="turnright">
            <canvas id="turnrightcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="turnleft">
            <canvas id="turnleftcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="slipright">
            <canvas id="sliprightcanvas" width="30" height="30"></canvas>
        </div>
        
        <div id="slipleft">
            <canvas id="slipleftcanvas" width="30" height="30"></canvas>
        </div>
        
        <div id="pivotright">
            <canvas id="pivotrightcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="pivotleft">
            <canvas id="pivotleftcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="accelerate">
            <canvas id="acceleratecanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="deaccelerate">
            <canvas id="deacceleratecanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="morejink">
            <canvas id="morejinkcanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="lessjink">
            <canvas id="lessjinkcanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="roll">
            <canvas id="rollcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="jink">
			<div class="centercontainer">
                <span class="jinkvalue value centercontent">0</span>
            </div>
            <canvas id="jinkcanvas" width="30" height="30"></canvas>
        </div>
        
        
        <div id="cancel">
            <canvas id="cancelcanvas" width="30" height="30"></canvas>
        </div>
        
    </div>
</div>
<!--
<div class="scrollelement left" style="left:0px;top:0px;"></div>
<div class="scrollelement side" style="right:0px;top:0px;"></div>
<div class="scrollelement side" style="left:0px;top:0px;"></div>
<div class="scrollelement side" style="left:0px;bottom:0px;"></div>
-->

<div id="templatecontainer">

    <div class="hexship">
        
        <canvas id="shipcanvas" width="0" height="0" class="hexshipcanvas"></canvas>
      
    
    </div>
    
    <div class="ewentry deletable"><span class="valueheader"></span><span class="value"></span><div class="button1"></div><div class="button2" ></div></div>

</div>

<div id="botPanel">
	<div class="BPcontainer EW">
		<div class="header"><span class="header">ELECTRONIC WARFARE</span></div>
		<div class="EWcontainer">
			<div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
			<div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
		</div>
	</div>
	
	<div class="BPcontainer movement">
		<div class="header"><span class="header">MOVEMENT INFO</span></div>
		<div class="entry"><span class="valueheader">Current turn delay:</span><span class="value currentturndelay"></span></div>
		<div class="entry"><span class="valueheader">Turn delay:</span><span class="value turndelay"></span></div>
		<div class="entry"><span class="valueheader">Turn cost:</span><span class="value turncost"></span></div>
		<div class="entry"><span class="valueheader">Accel/Deccel cost:</span><span class="value accelcost"></span></div>
		<div class="entry"><span class="valueheader">Pivot cost:</span><span class="value pivotcost"></span></div>
		<div class="entry"><span class="valueheader">Roll cost:</span><span class="value rollcost"></span></div>
		<div class="entry evasion"><span class="valueheader">Evasion:</span><span class="value evasion"></span></div>
		<div class="entry"><span class="valueheader">Unused thrust:</span><span class="value unusedthrust"></span></div>
	</div>
	
	<div class="BPcontainer thrusters">
		<div class="header"><span class="header">ASSIGN THRUST</span></div>
		<table class="frontcontainer">
			<tr>
			<td><div class="system slot_3">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			<td><div class="system slot_1">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			<td><div class="system slot_2">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			<td><div class="system slot_4">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			</tr>
		</table>
		
		<table class="portcontainer">
			<tr>
			<td><div class="system slot_3">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			</tr><tr>
			<td><div class="system slot_1">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			</tr><tr>
			<td><div class="system slot_2">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			</tr><tr>
			<td><div class="system slot_4">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			</tr>
		</table>
		
		<table class="starboardcontainer">
			<tr>
			<td><div class="system slot_3">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			</tr><tr>
			<td><div class="system slot_1">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			</tr><tr>
			<td><div class="system slot_2">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			</tr><tr>
			<td><div class="system slot_4">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			</tr>
		</table>
		
		<table class="aftcontainer">
			<tr>
			<td><div class="system slot_3">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			<td><div class="system slot_1">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			<td><div class="system slot_2">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			<td><div class="system slot_4">
				
				<div class="icon">
					<span class="efficiency value"></span>
					<div class="iconmask" oncontextmenu="botPanel.onThrusterContextMenu(this);return false;"></div>
				</div>

					<div class="button1"></div>
					<div class="button2"></div>

			</div></td>
			
			</tr>
		</table>
		
		<div class="middlecontainer">
			<div class="engine">
				<span class="efficiency value"></span>
			</div>
		</div>
	</div>
</div>
<div id="logcontainer">
	<div class="visiblecontainer assignthrustcontainer red">
		<div class="centeredvaluecontainer">
			<span><h2 class="">ASSIGN THRUST</h2></span>
			<p class="thrustobjective "></p>

			
		</div>
		<div class="ok"></div>
		<div class="cancel"></div>
	</div>
	<div id="log">
	
	</div>
</div>

</body>

</html>
