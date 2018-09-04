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
  
	$gamelobbydataJSON = json_encode($gamelobbydata, JSON_NUMERIC_CHECK);
	
	// Getting all ships in one go causes memory overload on the server.
	// Get the factions first. When a faction is opened to buy ships,
	// go bother the server for the ships of that faction only.
	
	$factions = json_encode(Manager::getAllFactions(), JSON_NUMERIC_CHECK);
	
	$ships = [];
	
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Gamelobby</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<link href="styles/lobby.css" rel="stylesheet" type="text/css">
		<link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <link href="styles/shipwindow.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="client/lib/jquery-ui-1.8.15.custom.min.js"></script>
        <script src="static/ships.js"></script>
<!--		<script src="client/helper.js"></script>-->
        <script src="client/gamelobby.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/player.js"></script>
        <script src="client/ships.js"></script>
        <script src="client/criticals.js"></script>
        <script src="client/damage.js"></script>
        <script src="client/systems.js"></script>
        <script src="client/power.js"></script>
        <script src="client/movement.js"></script>
        <script src="client/mathlib.js"></script>
		<script src="client/UI/confirm.js"></script>
        <script src="client/UI/shipwindow.js"></script>
        <script src="client/UI/fleetList.js"></script>
        <script src="client/UI/gameInfo.js"></script>
        <script src="client/UI/flightwindow.js"></script>
        <script src="client/UI/systemInfo.js"></script>
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
		<script>
			
            window.weaponManager = 
            {
                
                mouseoverTimer: null,
                mouseOutTimer: null,
                mouseoverSystem: null,

                onWeaponMouseover: function(e){

                    if (weaponManager.mouseOutTimer != null){
                        clearTimeout(weaponManager.mouseOutTimer); 
                        weaponManager.mouseOutTimer = null;
                    }

                    if (weaponManager.mouseoverTimer != null)
                        return;

                    weaponManager.mouseoverSystem = $(this);
                    weaponManager.mouseoverTimer = setTimeout(weaponManager.doWeaponMouseOver, 150);
                },

                onWeaponMouseOut: function(e){
                    if (weaponManager.mouseoverTimer != null){
                        clearTimeout(weaponManager.mouseoverTimer); 
                        weaponManager.mouseoverTimer = null;
                    }

                    weaponManager.mouseOutTimer = setTimeout(weaponManager.doWeaponMouseout, 50);
                },

                doWeaponMouseOver: function(e){
                    if (weaponManager.mouseoverTimer != null){
                        clearTimeout(weaponManager.mouseoverTimer); 
                        weaponManager.mouseoverTimer = null;
                    }

                    systemInfo.hideSystemInfo();

                    var t = weaponManager.mouseoverSystem;

                    var id = t.data("shipid");

                    var ship = gamedata.getShip(id);
                    var system = null;

                    if (t.hasClass("fightersystem")){
                        system = shipManager.systems.getFighterSystem(ship, t.data("fighterid"), t.data("id"));
                    }else{
                        system = shipManager.systems.getSystem(ship, t.data("id"));
                    }

                    systemInfo.showSystemInfo(t, system, ship);
                },

                doWeaponMouseout: function(){
                    if (weaponManager.mouseOutTimer != null){
                        clearTimeout(weaponManager.mouseOutTimer); 
                        weaponManager.mouseOutTimer = null;
                    }

                    systemInfo.hideSystemInfo();

                    weaponManager.mouseoverSystem = null;
                },
                hasFiringOrder: function(){return false},
                isLoaded: function(){return true},
                isSelectedWeapon: function(){return false},
                getWeaponCurrentLoading: function(weapon)
                {
                    return weapon.normalload;
                },
            }
            
            window.shipManager.movement.isRolled = function(ship)
            {
                return false;
            }
            
            
            window.shipWindowManager.addEW = function(){};
            
            
			jQuery(function($){
            
				gamedata.parseServerData(<?php print($gamelobbydataJSON); ?>);
				gamedata.parseFactions(<?php print($factions); ?>);
				$('.readybutton').on("click", gamedata.onReadyClicked);		
				$('.checkbutton').on("click", gamedata.checkChoices); //fleet correctness check
                $('.leave').on("click", gamedata.onLeaveClicked);
                $('.leaveslot').on("click", gamedata.onLeaveSlotClicked);
                $('.selectslot').on("click", gamedata.onSelectSlotClicked);
                $('.takeslot').on("click", gamedata.clickTakeslot);
				ajaxInterface.startPollingGamedata();
			});
		
		</script>
	</head>
	<body style="background-image:url(img/maps/<?php print($gamelobbydata->background); ?>)">
	
        <img src="img/logo.png">
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
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
			
			<span class="clickable leave">LEAVE GAME</span>
			
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
			
			<div><span class="clickable readybutton">READY</span>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="clickable checkbutton">CHECK</span> <!--fleet correctness check -->
			</div>
			<div id="fleetcheck" class="panel large" style="display:none;"><p id="fleetchecktxt" style="display:block;"><span></div>
		
			
		</div>
                    
        <div id="globalchat" class="panel large" style="height:150px;">
        <?php 
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='gamelobby.php';
//        	$ingame=false;
//        	include("helper.php")
        ?>
        </div>-->
                    
    <div id="systemInfo">
		<div class="name"><span class="name header">test</span></div>
		<div class="datacontainer"></div>
	</div>
                    
    <div id="slottemplatecontainer" style="display:none;">
        <div class="slot" >
            <div class="leaveslot"></div>
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
                    
                    
    <div id="systemtemplatecontainer" style="display:none;">

        <div class="structure system">
            <div class="name"><span class="namevalue">STRUCTURE</span></div>
            <div class="systemcontainer">

                <div class="health systembarcontainer">
                    <div class="healthbar bar" style="width:40px;"></div>
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
                    <div class="healthbar bar" style="width:40px;"></div>
                </div>
                <div class="critical systembarcontainer">
                    <div class="valuecontainer"><span class="criticalvalue value">CRITICAL<span></div>
                </div>

            </div>
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
                        <div class="icon"><img src="" alt="" border="" width="120" height="120"></div>
                        <!--<div class="notes"></div> -->
                    </td>                
                    <td class="col2">
                        <div id="shipSection_1" class="shipSection" style="margin-top: 15px">
                            <table></table>
                        </div>
                        <div id="shipSection_0" class="shipSection primary" style="margin-top: 25px">
                            <table></table>
                        </div>
                        <div id="shipSection_2" class="shipSection" style="margin-bottom: 20px">
                            <table></table>
                        </div>
                    </td>
                    <td class="col3">
			<div class="notes"></div> <!-- Marcin Sawicki - here more readable -->
                        <div class="EW" style="display:none;"><!-- Marcin Sawicki - at fleet selection EW window is useless! -->
                            <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                            <div class="EWcontainer">
                                <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                                <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                                <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                            </div>
                        </div>
                        <div id="shipSection_4" class="shipSection">
                            <table></table>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="hitChartDiv"></div>     
        </div>


        <div class="shipwindow base" style="width: 450px;">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">base:</span><span class="value shipclass">ship type class here</span>
                <div class="close"></div>
            </div>

            <div id="shipSection_3" class="shipSection" style="left: 0px; top: 180px">
                <table id="31" style="border-bottom: 8px solid #7e9dc7"></table>
                <table id="32"></table>
            </div>

            <table class="divider">
                <tr>
                    <td class="col1" style="width: 33%">
                        <div class="icon" style="margin:auto;"><img src="" alt="" border="" width="120" height="120"></div>
                    </td>                
                    <td class="col2" style="width: 33%; border-left: 3px dotted #7e9dc7; border-right: 3px dotted #7e9dc7;">
                        <div id="shipSection_1" class="shipSection" style="margin-top: 20px;">
                            <table></table>
                        </div>
                        <div id="shipSection_0" class="shipSection primary" style="border-top: 3px dotted #7e9dc7; border-bottom: 3px dotted #7e9dc7; margin-top: 35px;">
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
                        <div id="shipSection_4" class="shipSection" style="right: 75px; top: 180px">
                <table id="41" style="border-bottom: 8px solid #7e9dc7"></table>
                <table id="42"></table>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="hitChartDiv"></div>     
        </div>

        <!--
        <div class="shipwindow base">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">base:</span><span class="value shipclass">ship type class here</span>
                <div class="close"></div>
            </div>

            <div id="shipSection_3" class="shipSection">
                <table id="31"></table>
                <table id="32"></table>
            </div>

            <table class="divider">
                <tr>
                    <td class="col1">
                        <div class="icon"><img src="" alt="" border="" width="120" height="120"></div>
                        <div class="notes"></div>
                    </td>                
                    <td class="col2">
                        <div id="shipSection_1" style="margin-top: 10px;" class="shipSection">
                            <table></table>
                        </div>
                        <div id="shipSection_0" class="shipSection primary" style="margin-top: 10px;">
                            <table></table>
                        </div>
                        <div id="shipSection_2" style="margin-top: 10px;" class="shipSection">
                            <table></table>
                        </div>
                    </td>
                    <td class="col3">
                        <div class="EW">
                            <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                            <div class="EWcontainer">
                                <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                                <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                                <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                            </div>
                        </div>
                        <div id="shipSection_4" class="shipSection">
                            <table id="41"></table>
                            <table id="42" style="margin-top: 10px"></table>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="hitChartDiv"></div>     
        </div>-->




        <div class="shipwindow flight">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">Class:</span><span class="value shipclass"></span>
                <div class="close"></div>
            </div>

            <table class="divider">
                <tr>
                    <td class="fightercontainer 0"></td>
                   <!---             <td class="fightercontainer 1"></td>
                    <td class="fightercontainer 2"></td>
                </tr>
                <tr>
                    <td class="fightercontainer 3"></td>
                    <td class="fightercontainer 4"></td>
                    <td class="fightercontainer 5"></td>        -->
			<td>
				<div class="notes"></div>
			</td>
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
                                    
    <div class="missileSelectItem" style="display:none">
        <span>
            <span class="selectText"></span>
            <span class="selectAmount"></span>
            <span class="selectButtons">
                <table>
                    <tr>
                        <td><span class="plusButton"></span></td>
                    </tr>
                    <tr>
                        <td><span class="minusButton"></span></td>
                    </tr>
                </table>
            </span>
        </span>
    </div>
        
    <div class="totalUnitCost" style="display: none">
        <span>
            <span class="totalUnitCostText"></span>
            <span class="totalUnitCostAmount"></span>
        </span>
    </div>                                    
	</body>



</html>
