<?php 
    ob_start("ob_gzhandler"); 
    include_once 'global.php';
    
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
	
	$serverdata = Manager::getTacGamedata($gameid, $thisplayer, null, 0, -1);
    $serverdataJSON = '{}';
    $error = 'null';

    if ($serverdata instanceof TacGameData) {
        $serverdataJSON = json_encode($serverdata->stripForJson(), JSON_NUMERIC_CHECK);
    } else {
        $error = json_encode($serverdata, JSON_NUMERIC_CHECK);
    }
?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Fiery Void</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, minimal-ui' />
    <link href="styles/tactical.css" rel="stylesheet" type="text/css">
    <link href="styles/shipwindow.css" rel="stylesheet" type="text/css">
	<link href="styles/confirm.css" rel="stylesheet" type="text/css">
    <link href="styles/replay.css" rel="stylesheet" type="text/css">
    <link href="styles/shipTooltip.css" rel="stylesheet" type="text/css">
<!--	<link href="styles/helper.css" rel="stylesheet" type="text/css">-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
            integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
            crossorigin="anonymous"></script>
    <script src="client/lib/three.min.js"></script>
    <script src="client/lib/stats.min.js"></script>
    <script src="client/lib/threeLoaderSupport.js"></script>
    <script src="client/lib/threeObjectLoader.js"></script>
    <script src="client/lib/threeObjectLoader2.js"></script>
    <script src="client/lib/threeGltfLoader.js"></script>
    <script src="client/lib/threeColladaLoader.js"></script>
    <script src="client/UI/reactJs/UI.bundle.js"></script>
    <script src="static/ships.js"></script>
    <script>
        window.Config = {
            HEX_SIZE: 25
        };
    </script>

    <?php include_once 'shaders.php'; ?>
    <script>
        $(window).load(function(){
            
            if (<?php print($error); ?>) {
                alert(<?php print($error); ?>);
            }
            
            gamedata.parseServerData(<?php print($serverdataJSON); ?>);
            //shipWindowManager.createShipWindow(gamedata.getActiveShip());
            
			if (gamedata.thisplayer == -1){
				$(".notlogged").show();
				$(".waiting").hide();
				gamedata.waiting = false;
			}

            webglScene.init(
                '#webgl',
                jQuery('#pagecontainer'),
                new window.webglHexGridRenderer(graphics),
                new window.phaseDirector(graphics),
                gamedata,
                window.coordinateConverter
            );

			window.UIManagerInstance = new window.UIManager($("body")[0]);
            window.UIManagerInstance.PlayerSettings(window.Settings);
            window.UIManagerInstance.FullScreen();
            window.UIManagerInstance.EwButtons();
            $("#pagecontainer").show();
        });
        
            
    </script>
<!--	<script src="client/helper.js"></script>-->
    <script src="client/lib/graphics.js"></script>
    <script src="client/lib/HexagonMath.js"></script>
    <script src="client/lib/AbstractCanvas.js"></script>
    <script src="client/Settings.js"></script>
    <script src="client/renderer/webglHexGridRenderer.js"></script>
    <script src="client/renderer/canvasHexGridRenderer.js"></script>
    <script src="client/renderer/Loader.js"></script>
    <script src="client/renderer/webglScene.js"></script>
    <script src="client/renderer/webglScrolling.js"></script>
    <script src="client/renderer/webglZooming.js"></script>
    <script src="client/renderer/PhaseDirector.js"></script>
    <script src="client/renderer/Animation.js"></script>

    <script src="client/renderer/shipWindowManager.js"></script>

    <script src="client/renderer/sprite/webglSprite.js"></script>
    <script src="client/renderer/sprite/HexagonSprite.js"></script>
    <script src="client/renderer/sprite/ShipEWSprite.js"></script>
    <script src="client/renderer/sprite/ShipSelectedSprite.js"></script>
    <script src="client/renderer/sprite/ShipFacingSprite.js"></script>
    <script src="client/renderer/sprite/BoxSprite.js"></script>
    <script src="client/renderer/sprite/PlainSprite.js"></script>
    <script src="client/renderer/sprite/LineSprite.js"></script>
    <script src="client/renderer/sprite/BallisticSprite.js"></script>
    <script src="client/renderer/sprite/TextSprite.js"></script>
    
    <script src="client/renderer/object/objects.bundle.js"></script>
    <script src="client/renderer/icon/ShipIcon.js"></script>
    <script src="client/renderer/icon/FlightIcon.js"></script>
    <script src="client/renderer/icon/DeploymentIcon.js"></script>

    <script src="client/lib/coordinateConverter.js"></script>
    <script src="client/renderer/icon/ShipIconContainer.js"></script>
    <script src="client/renderer/icon/EWIconContainer.js"></script>
    <script src="client/renderer/icon/BallisticIconContainer.js"></script>

    <script src="client/renderer/animationStrategy/AnimationStrategy.js"></script>
    <script src="client/renderer/animationStrategy/IdleAnimationStrategy.js"></script>
    <script src="client/renderer/animationStrategy/ReplayAnimationStrategy.js"></script>
    <script src="client/renderer/animationStrategy/animation/Animation.js"></script>
    <script src="client/renderer/animationStrategy/animation/ShipMovementAnimation.js"></script>
    <script src="client/renderer/animationStrategy/animation/LogAnimation.js"></script>
    <script src="client/renderer/animationStrategy/animation/CameraPositionAnimation.js"></script>
    <script src="client/renderer/animationStrategy/animation/ShipDestroyedAnimation.js"></script>

    <script src="client/renderer/animationStrategy/animation/FireAnimationHelper.js"></script>
    <script src="client/renderer/animationStrategy/animation/AllWeaponFireAgainstShipAnimation.js"></script>
    <script src="client/renderer/animationStrategy/animation/HexTargetedWeaponFireAnimation.js"></script>
    <script src="client/renderer/animationStrategy/animation/ParticleEmitterContainer.js"></script>
    <script src="client/renderer/animationStrategy/animation/BaseParticle.js"></script>
    <script src="client/renderer/animationStrategy/animation/StarParticle.js"></script>
    <script src="client/renderer/animationStrategy/animation/ParticleEmitter.js"></script>
    <script src="client/renderer/animationStrategy/animation/StarParticleEmitter.js"></script>
    <script src="client/renderer/animationStrategy/animation/ParticleAnimation.js"></script>
    <script src="client/renderer/animationStrategy/animation/ParticleEmitter.js"></script>
    <script src="client/renderer/animationStrategy/animation/effects/Explosion.js"></script>
    <script src="client/renderer/animationStrategy/animation/effects/LaserEffect.js"></script>
    <script src="client/renderer/animationStrategy/animation/effects/BoltEffect.js"></script>
    <script src="client/renderer/animationStrategy/animation/effects/TorpedoEffect.js"></script>
    <script src="client/renderer/animationStrategy/animation/effects/ShipExplosion.js"></script>
    <script src="client/renderer/animationStrategy/animation/effects/SystemDestroyedEffect.js"></script>

    <script src="client/renderer/phaseStrategy/PhaseStrategy.js"></script>
    <script src="client/renderer/phaseStrategy/DeploymentPhaseStrategy.js"></script>
    <script src="client/renderer/phaseStrategy/WaitingPhaseStrategy.js"></script>
    <script src="client/renderer/phaseStrategy/InitialPhaseStrategy.js"></script>
    <script src="client/renderer/phaseStrategy/MovementPhaseStrategy.js"></script>
    <script src="client/renderer/phaseStrategy/FirePhaseStrategy.js"></script>
    <script src="client/renderer/phaseStrategy/ReplayPhaseStrategy.js"></script>
    <script src="client/renderer/terrain/StarField.js"></script>


    <script src="client/renderer/texture/HexagonTexture.js"></script>


    <script src="client/UI/ShipTooltip.js"></script>
    <script src="client/UI/SelectFromShips.js"></script>
    <script src="client/UI/shipTooltipMenu.js"></script>
    <script src="client/UI/shipTooltipInitialOrdersMenu.js"></script>
    <script src="client/UI/shipTooltipFireMenu.js"></script>
    <script src="client/UI/ShipTooltipBallisticsMenu.js"></script>

    <script src="client/model/hexagon/Cube.js"></script>
    <script src="client/model/hexagon/Offset.js"></script>
    <script src="client/gameRules/SimultaneousMovementRule.js"></script>

    <script src="client/UI/botPanel.js"></script>
    <script src="client/UI/replayUI.js"></script>
    <script src="client/gamedata.js"></script>
    <script src="client/windowevents.js"></script>
    <script src="client/mathlib.js"></script>
    <script src="client/lib/seedRandom.js"></script>
    <script src="client/ajaxInterface.js"></script>
    <script src="client/ew.js"></script>
    <script src="client/weaponManager.js"></script>
    <script src="client/damage.js"></script>
	<script src="client/combatLog.js"></script>
	<script src="client/player.js"></script>
    <script src="client/ships.js"></script>
    <script src="client/movement.js"></script>
    <script src="client/criticals.js"></script>
    <script src="client/systems.js"></script>
	<script src="client/power.js"></script>
    <script src="client/UI/infowindow.js"></script>
	<script src="client/UI/systemInfo.js"></script>
    <script src="client/UI/shipwindow.js"></script>
    <script src="client/UI/fleetList.js"></script>
	<script src="client/UI/gameInfo.js"></script>
    <script src="client/UI/flightwindow.js"></script>
	<script src="client/UI/confirm.js"></script>
	<script src="client/model/ship.js"></script>
    <script src="client/model/shipSystem.js"></script>
    <script src="client/model/systemFactory.js"></script>
    <script src="client/model/system/baseSystems.js"></script>
    <script src="client/model/system/defensive.js"></script>
    <script src="client/model/weapon/ammo.js"></script>
    <script src="client/model/weapon/laser.js"></script>
    <script src="client/model/weapon/particle.js"></script>
    <script src="client/model/weapon/matter.js"></script>
    <script src="client/model/weapon/plasma.js"></script>
    <script src="client/model/weapon/special.js"></script>
    <script src="client/model/weapon/torpedo.js"></script>
    <script src="client/model/weapon/pulse.js"></script>
    <script src="client/model/weapon/electromagnetic.js"></script>
    <script src="client/model/weapon/aoe.js"></script>
    <script src="client/model/weapon/molecular.js"></script>
    <script src="client/model/weapon/antimatter.js"></script>
    <script src="client/model/weapon/dualWeapon.js"></script>
    <script src="client/model/weapon/duoWeapon.js"></script>
    <script src="client/model/weapon/gravitic.js"></script>
    <script src="client/model/weapon/missile.js"></script>
    <script src="client/model/weapon/ion.js"></script>
    <script src="client/model/weapon/customs.js"></script>
    <script src="client/model/weapon/customSW.js"></script>
</head>


<body>


<div id="phaseheader" class="roundedright" style="">
    <span class="turn value"></span><span class="phase value"></span><span class="activeship value"></span><span class="waiting value"></span><span class="finished value">GAME OVER</span><span class="notlogged value">NOT LOGGED IN</span>
    <table class="uitable">
        <tr>
        <td class="committurn" style="display:none"><div class="ok" ></div></td>
        <td class="surrender" style="display:none"></td>
        <td class="cancelturn" style="display:none"><div class="cancel" ></div></td>
        </tr>
    </table>
</div>

<div id="backDiv" style="">
</div>

<div id="iniGui" style="">
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
                <div class="healthbar bar" style="width:100%;"></div>
                <div class="valuecontainer"><span class="healthvalue value"></span></div>
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
        <div class="systemcontainer">
            <div class="icon">
                <div class="efficiency value"></div>
                    <div class="iconmask"></div>
                <div class="UI">
                    <div class="button stopoverload"></div>
                    <div class="button overload"></div>
                    <div class="button plus"></div>
                    <div class="button minus"></div>
                    <div class="button off"></div>
                    <div class="button on"></div>
                    <div class="button holdfire"></div>
                    <div class="button mode"></div>
                </div>
            </div>
            <div class="health systembarcontainer">
                <div class="healthbar bar" style="width:100%;"></div>
            </div>
            <div class="critical systembarcontainer">
                <div class="valuecontainer"><span class="criticalvalue value">CRITICAL<span></div>
            </div>
            
        </div>
    </div>
    
    
    <div class="iconduo">
        <span class="efficiency value"></span>
            <div class="duoIconmask"></div>
<!--        <div class="iconUI">
            <div class="button holdfire"></div>
        </div>-->
    </div>
    
    <div class="fighter">
		<div class="destroyedtext"><span>DESTROYED</span></div>
		<div class="disengagedtext"><span>DISENGAGED</span></div>
        <div class="systemcontainer">
            <div class="icon">
				<table class="fightersystemcontainer 1"><tr></tr></table>
				<div style="height:60px;"></div>
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

        <div class="heavyfighter">
            <div class="systemcontainer">
                <div class="icon">
                    <table class="fightersystemcontainer 1"><tr></tr></table>
                    <div style="height:60px;"></div>
                    <table class="fightersystemcontainer 2"><tr></tr></table>
                </div>

                <div class="health systembarcontainer">
                    <div class="healthbar bar" style="width:90px;"></div>
                    <div class="valuecontainer"><span class="healthvalue value"></span></div>
                </div>
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
      
               
        <div id="shipSection_3" class="shipSection">
            <table></table>
        </div>    
                        
            
		<table class="divider">
			<tr>
                <td class="col1">
    				<div class="icon rotate90"><img src="" alt="" border="" width="120" height="120"></div>
    				<div class="notes"></div>
				</td>
                <td class="col2">
                    <div id="shipSection_1" class="shipSection" style="margin-top: 15px">
                        <table></table>
                    </div>
                    <div id="shipSection_0" class="shipSection primary" style="margin-top: 25px; margin-bottom: 25px">
                        <table></table>
                    </div>
                    <div id="shipSection_2" class="shipSection" style="margin-bottom: 25px">
                        <table></table>
                    </div>
    			</td>
                <td class="col3">
                    <div class="EW">
                        <div class="ewheader">
                            <span class="header">ELECTRONIC WARFARE</span>
                        </div>
                        <div class="EWcontainer">
                            <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                            <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                            <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                        </div>
                        <div class="AA" style="display: none">
                            <table class="AAtable" style="width: 100%; border: 1px solid #496791">
                            </table>
                        </div>
                    </div>
                    <div class ="buttons">
                        <div class="HitChart" style="display: none">
                        </div>
                    </div>
    				<div id="shipSection_4" class="shipSection">
    					<table></table>
    				</div>
    			</td>
            </tr>
		</table>   
        <div class="hitChartDiv">
        </div>     
    </div>


    <div class="shipwindow base" style="width: 450px;">
        <div class="topbar">
            <span class="valueheader name">Name:</span><span class="value name">name here</span>
            <span class="valueheader shipclass">base:</span><span class="value shipclass">ship type class here</span>
            <div class="close"></div>
        </div>

        <div id="shipSection_3" class="shipSection" style="left: 0px; top: 180px">
            <table id="31" style="border-bottom: 6px solid #7e9dc7"></table>
            <table id="32"></table>
        </div>

        <table class="divider">
            <tr>
                <td class="col1" style="width: 33%">
                    <div class="icon" style="margin:auto;"><img src="" alt="" border="" width="120" height="120"></div>
                </td>                
                <td class="col2" style="width: 33%; border-left: 6px solid #7e9dc7; border-right: 6px solid #7e9dc7;">
                    <div id="shipSection_1" class="shipSection" style="margin-top: 20px;">
                        <table></table>
                    </div>
                    <div id="shipSection_0" class="shipSection primary" style="border-top: 4px solid #7e9dc7; border-bottom: 4px solid #7e9dc7; margin-top: 35px;">
                        <table></table>
                    </div>
                    <div id="shipSection_2" class="shipSection" style="margin-top: 35px;">
                        <table></table>
                    </div>
                </td>
                <td class="col3" style="width: 33%">
                    <div class="EW" style="margin: auto;">
                        <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                        <div class="EWcontainer">
                            <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                            <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                            <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                            <div class="notes"></div>
                        </div>
                    </div>
                    <div class ="buttons">
                    </div>
                    <div id="shipSection_4" class="shipSection" style="right: 75px; top: 180px">
                <table id="41" style="border-bottom: 6px solid #7e9dc7"></table>
            <table id="42"></table>
                    </div>
                </td>
            </tr>
        </table>
        <div class="hitChartDiv"></div>     
    </div>

    <table id="hitChartTable">
    </table>
    
    <div class="shipwindow flight">
        <div class="topbar">
            <span class="valueheader name">Name:</span><span class="value name">name here</span>
        </div>
        <div>
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
            <tr>
                <td class="fightercontainer 6"></td>
                <td class="fightercontainer 7"></td>
                <td class="fightercontainer 8"></td>
            </tr>
            <tr>
                <td class="fightercontainer 9"></td>
                <td class="fightercontainer 10"></td>
                <td class="fightercontainer 11"></td>
            </tr>
		</table>
    </div>

    <div class="shipwindow heavyfighter">
        <div class="topbar">
            <span class="valueheader name">Name:</span><span class="value name">name here</span>
            <span class="valueheader shipclass">Class:</span><span class="value shipclass"></span>
            <div class="close"></div>
        </div>
                <table class="divider">
                        <tr>
                                <td class="fightercontainer"></td>
                        </tr>
                </table>
    </div>
    
</div>


<div id="movementUiReact"></div>
<div id="shipWindowsReact"></div>
<div id="systemInfoReact"></div>
<div id="weaponList"></div>
<div id="showEwButtons"></div>
<div id="fullScreen"></div>
<div id="playerSettings"></div>
<div id="shipThrust"></div>
<div id="pagecontainer" oncontextmenu="return false;">
    <div id="background"></div>
    <div id="webgl" class="tacticalcanvas"></div>


    <div id="shipNameContainer">
        <div class="namecontainer" style="border-bottom:1px solid white;margin-bottom:3px;"></div>

		<div class="fire" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>TARGETING</span></div>
		<div class="fire targeting"></div>
    </div>
	
    <div id="weaponTargetingContainer">
        <div class="infolist">
        
        </div>
    </div>
    
<!--        <div id="globalhelp" class="ingamehelppanel">
        <?php
        	$messagelocation='hex.php';
        	$ingame=true;
        	include('helper.php');
        ?>
        </div>-->
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
<div id="logcontainer">    
    <div id="logUI">
        <div id="logTab" data-select="#log" class="logUiEntry selected">
            <span>LOG</span>
        </div>
        <div id="infoTab" data-select="#gameinfo" class="logUiEntry">
            <span>INFO</span>
        </div>
        <div id="declarationsTab" data-select="#declarations" class="logUiEntry" style = "overflow-y: auto;"> <!-- fire and EW declarations review -->
            <span>DECLARATIONS</span>
        </div>	    
        <div id="chatTab" data-select="#chat" class="logUiEntry">
            <span>GAME CHAT</span>
        </div>
        <div id="globalChatTab" data-select="#globalchat" class="logUiEntry">
            <span>CHAT</span>
        </div>
        <div id="expandBotPanel">
            <span>Click!</span>
        </div>
<!--        <div id="settingsTab" data-select="#settings" class="logUiEntry">
            <span>SETTINGS</span>
        </div>-->
    </div>
    <div id="log" class="logPanelEntry">

    </div>
    <div id="gameinfo" class="logPanelEntry" style="display:none;">

    </div>
    <div id="declarations" class="logPanelEntry" style="display:none;"> <!-- fire and EW declarations review -->
	<?php
	    include("declarations.php");
	?>
    </div>
    
    <div id="chat" class="logPanelEntry" style="display:none;">
        <?php 
            $chatgameid = $gameid;
            $chatelement = "#chat";
            include("chat.php")
        ?>
    </div>
    
    <div id="globalchat" class="logPanelEntry" style="display:none;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
    </div>
 
<!--    <div id="settings" class="logPanelEntry" style="display:none;">
        <div id="helphide" class="helphide">
        <span title="Show or Hide Vir to help you">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        HELP
        </span>
        </div>
        <span title="Disable or Enable the extra Commit check to end a turn">
        <div id="autocommit" class="autocommit">
        <img id="autocommitimg" src="img/ok.png" height="30" width="30">	
        <span id="autocommittext" class="autocommittext">COMMIT</span>
        </span>
        </div>
    </div>-->
        
    <div class="fleetlistentry">
        <div class="fleetheader">
        </div>
        <div class="fleetlist">
                <div class="fleetlistline">
                </div>
    </div>
    
</div>

</body>

</html>

<?php 
    ob_end_flush();
?>
