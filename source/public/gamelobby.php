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
		
		<!-- replaced by php include below
        <script src="static/ships.js"></script>
		-->
<?php		
	include 'static/ships.php';
?>
		
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
    	<script src="client/model/weapon/ammoWeapons.js"></script>         
        <script src="client/model/weapon/laser.js"></script>
        <script src="client/model/weapon/particle.js"></script>
        <script src="client/model/weapon/matter.js"></script>
        <script src="client/model/weapon/plasma.js"></script>
        <script src="client/model/weapon/special.js"></script>
        <script src="client/model/weapon/supportWeapons.js"></script>        
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
        <script src="client/model/weapon/customNexus.js"></script>
        <script src="client/model/weapon/customDevelopment.js"></script>
        <script src="client/model/weapon/customEscalation.js"></script>		
        <script src="client/model/weapon/customBSG.js"></script>		
        <script src="client/model/weapon/customTrek.js"></script>		
        <script src="client/model/weapon/customCW.js"></script>		
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

                    //Added button to view actual details of purchased ships, so need ot make sure right ship is called for correct tooltip info - DK 30.3.25 
                    if(gamedata.fleetWindowOpen){
                        var ship = gamedata.getFleetShipById(id);
                    }else{
                        var ship = gamedata.getShip(id);
                    }

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

        /* //Old jQuery section, in case my new filtering logic messes anything up - DK   
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
		*/
        
        jQuery(function($){            
            gamedata.parseServerData(<?php print($gamelobbydataJSON); ?>);
            gamedata.parseFactions(<?php print($factions); ?>);

            $('.readybutton').on("click", gamedata.onReadyClicked);		
            $('.checkbutton').on("click", gamedata.checkChoices); //fleet correctness check
            $('.leave').on("click", gamedata.onLeaveClicked);
            $('.leaveslot').on("click", gamedata.onLeaveSlotClicked);
            $('.selectslot').on("click", gamedata.onSelectSlotClicked);
            $('.takeslot').on("click", gamedata.clickTakeslot);

            // Start polling for updates
            ajaxInterface.startPollingGamedata();

            // ✅ Unified filter logic for factions based on Tier and Custom
            function updateTierFilter() {
                const selectedTiers = $('.tier-filter:checked').map(function () {
                    return $(this).data('tier');
                }).get();

                const showCustomFactions = $('#toggleCustomFactions').is(':checked');

                $('.faction').each(function () {
                    const tier = $(this).data('tier');
                    const isCustom = $(this).data('custom') === true || $(this).data('custom') === "true";

                    const isVisible = selectedTiers.includes(tier) && (showCustomFactions || !isCustom);
                    $(this).toggle(isVisible);
                });
            }

            // ✅ Listen to Tier and Custom Faction checkboxes
            $('.tier-filter').on('change', updateTierFilter);
            $('#toggleCustomFactions').on('change', updateTierFilter);

            // ✅ Initial call
            updateTierFilter();

            // ✅ Ship filtering that respects faction open state
            $("#toggleCustomShips").on("change", function () {
                gamedata.applyCustomShipFilter();
            });

            // ✅ Select All / None Tier checkboxes + toggle customs
            $('.tier-select-all').on('click', function () {
                $('.tier-filter').prop('checked', true);
                $('#toggleCustomFactions').prop('checked', true).trigger('change');
                $('#toggleCustomShips').prop('checked', true).trigger('change');
                $('#isdFilter').val(''); // ✅ Reset ISD filter
                gamedata.applyCustomShipFilter(); // ✅ Reapply the filter logic
                updateTierFilter();
            });

            $('.tier-select-none').on('click', function () {
                $('.tier-filter').prop('checked', false);
                $('#toggleCustomFactions').prop('checked', false).trigger('change');
                $('#toggleCustomShips').prop('checked', false).trigger('change');
                $('#isdFilter').val(''); // ✅ Reset ISD filter
                gamedata.applyCustomShipFilter(); // ✅ Reapply the filter logic
                updateTierFilter();
            });

            // Sanitize input on each keystroke, but don't apply filter yet
            $("#isdFilter").on("input", function () {
                let val = $(this).val().replace(/\D/g, ''); // remove non-digits
                if (val.length > 4) val = val.slice(0, 4); // limit to 4 digits
                $(this).val(val);
            });

            // Apply filter only when Enter key is pressed
            $("#isdFilter").on("keypress", function (e) {
                if (e.which === 13) {
                    gamedata.applyCustomShipFilter();
                }
            });

            // Reset ISD filter when clicking "Reset ISD"
            $(".resetISDFilter").on("click", function () {
                $("#isdFilter").val('');
                gamedata.applyCustomShipFilter();
            });

            // Optional: initialize custom ship visibility
            $("#toggleCustomShips").trigger("change");
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
			<div><span> <?php print($gamelobbydata->description); ?> </span></div>
<?php
//define options list
$optionsUsed = '';

if ($gamelobbydata->gamespace == '-1x-1'){ //open map
	$optionsUsed .= 'Open Map';
}else{ //fixed map
	$optionsUsed .= 'Map ' . $gamelobbydata->gamespace;
}

$simMv = false;
$desperate = false;
$asteroids = false;
$moons = false;
$initiativeCategories = null;
$desperateTeams = null;
$asteroidsNo = 0;
$moonsNo = 0;


if ($gamelobbydata->rules) {
    if ($gamelobbydata->rules->hasRuleName('initiativeCategories')) {
        $simMv = true;
        $initiativeRule = $gamelobbydata->rules->getRuleByName('initiativeCategories');
        if ($initiativeRule && method_exists($initiativeRule, 'jsonSerialize')) {
            $initiativeCategories = $initiativeRule->jsonSerialize();
        }
    }

    if ($gamelobbydata->rules->hasRuleName('desperate')) {
        $desperate = true;
        $desperateRule = $gamelobbydata->rules->getRuleByName('desperate');
        if ($desperateRule && method_exists($desperateRule, 'jsonSerialize')) {
            $desperateTeams = $desperateRule->jsonSerialize();
        }        
    }

    if ($gamelobbydata->rules->hasRuleName('asteroids')) {
        $asteroids = true;
        $asteroidsRule = $gamelobbydata->rules->getRuleByName('asteroids');
        if ($asteroidsRule && method_exists($asteroidsRule, 'jsonSerialize')) {
            $asteroidsNo = $asteroidsRule->jsonSerialize();
        }        
    }  

    if ($gamelobbydata->rules->hasRuleName('moons')) {
        $moons = true;
        $moonsRule = $gamelobbydata->rules->getRuleByName('moons');
        if ($moonsRule && method_exists($moonsRule, 'jsonSerialize')) {
            $moonsNo = $moonsRule->jsonSerialize();
        }        
    }       
}

if ($simMv == true) { // simultaneous movement
    $optionsUsed .= ', Simultaneous Movement';
    if ($initiativeCategories !== null) {
        $optionsUsed .= ' (Initiative Categories: ' . $initiativeCategories . ')';
    }
} else { // standard movement
    $optionsUsed .= ', Standard Movement';
}

if ($desperate == true) { // Desperate rules in play
    $teamDisplay = null;
   
    if($desperateTeams == 1) {
            $teamDisplay = "Team 1";
    }else if($desperateTeams == 2){    
        $teamDisplay = "Team 2";
    }else{    
        $teamDisplay = "Both Teams";
    }
    $optionsUsed .= ', Desperate Rules ('. $teamDisplay . ')';
} else { // standard movement
    $optionsUsed .= ', Normal Rules';
}

if ($asteroids == true) { // Asteroid terrain rules in play
    $optionsUsed .= ', Asteroids ('. $asteroidsNo . ')';
}
if ($moons == true) { // Moon terrain rules in play
    $optionsUsed .= ', Moons ('. $moonsNo . ')';
}

if ($asteroids == false && $moons == false) { 
    $optionsUsed .= ', No terrain';
}

?>
<div><span><b>Options:</b> <?php print($optionsUsed); ?> </span></div>

<br>
<a href="files/FV_factions.txt" target="_blank" style="text-decoration: underline;">Fiery Void Factions & Tier List</a> 
- Overview of rules and systems of the fleets available in Fiery Void
<br>
<a href="files/enhancements_list.txt" target="_blank" style="text-decoration: underline;">Common Systems & Enhancement List</a> 
- Details of enhancements and other common systems e.g. Boarding / Missiles
<br>
<span style="color: #f8f8f8;">Random Fleet Selection</span> 
<span style="margin-right: 3px;">-</span> 
<a href="https://old.wheelofnames.com/fx3-uje" target="_blank" style="color: #ff9500; text-decoration: underline;"><strong>Tier 1</strong></a> 
<strong style="margin: 0 2.5px;">|</strong> 
<a href="https://old.wheelofnames.com/rmq-7ds" target="_blank" style="color: #ff9500; text-decoration: underline;"><strong>Tier 2</strong></a>
<strong style="margin: 0 2.5px;">|</strong> 
<a href="https://old.wheelofnames.com/sgd-5zq" target="_blank" style="color: #ff9500; text-decoration: underline;"><strong>Tier 3</strong></a>
<br><br>


	    
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
		<div>
        <!-- 🟡 Fleet points summary & Tier checkboxes in one row -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <div>
            <span class="panelheader" style="padding-right: 15px;">PURCHASE YOUR FLEET</span>
            <span class="panelsubheader current">0</span>
            <span class="panelsubheader">/</span>
            <span class="panelsubheader max">0</span><span class="panelsubheader">pts</span>
            <span class="panelsmall" style="margin-left: 5px;">(</span>
            <span class="panelsmall remaining">0</span><span class="panelsmall">pts left</span>
            <span class="panelsmall">)</span>
        </div>

        <!--
        <div>
                <span class="panelheader" style="padding-right: 15px;">PURCHASE YOUR FLEET</span>
                <span class="panelsubheader current">0</span>
                <span class="panelsubheader">/</span>
                <span class="panelsubheader max">0</span>
                <span class="panelsubheader">pts</span>
                <span class="panelsmall" style="margin-left: 5px;">(</span>
                <span class="panelsmall remaining">0</span>
                <span class="panelsmall">pts left</span>
                <span class="panelsmall">)</span>
            </div>
        -->
            <div style="text-align: right; font-size: 11px;">
                <span class="clickable tier-select-all" style="margin-right: 5px;  text-decoration: underline;">All Filters</span>
                <span style="margin-right: 5px;">|</span>          
                <span class="clickable tier-select-none" style="text-decoration: underline; margin-right: 5px;">No Filters</span>
                <span>|</span>  

                <label style="margin-left: 5px; font-size: 11px;">
                    <span style="margin-right: 2px; font-size: 12px;">Filter by ISD:</span>
                    <input type="text" id="isdFilter" value="" style="width: 35px; height: 12px; text-align: right;">
                    <span class="clickable resetISDFilter" style="text-decoration: underline; margin-left: 3px;  font-size: 10px;">Reset</span>
                </label>

            </div>
        </div>

        <div style="text-align: right; margin-top: 3px;">
            <label style="margin-left: 5px;">Tier 1 <input type="checkbox" class="tier-filter" data-tier="Tier 1" checked></label>
            <label style="margin-left: 5px;">Tier 2 <input type="checkbox" class="tier-filter" data-tier="Tier 2" checked></label>
            <label style="margin-left: 5px;">Tier 3 <input type="checkbox" class="tier-filter" data-tier="Tier 3" checked></label>
            <label style="margin-left: 5px;">Ancients <input type="checkbox" class="tier-filter" data-tier="Tier Ancients" checked></label>
            <label style="margin-left: 5px;">Custom Factions <input type="checkbox" id="toggleCustomFactions" checked></label>
            <label style="margin-left: 5px;">Custom Ships <input type="checkbox" id="toggleCustomShips" checked></label>
        </div>

    <!-- Fleet selection area -->
    <table class="store" style="width:100%; margin-top: 5px;">
        <tr>
            <td style="width:40%; vertical-align: top;">
                <div id="fleet" class="subpanel"></div>
            </td>
            <td style="width:60%;">
                <div id="store" class="subpanel"></div>
            </td>
        </tr>
    </table>
</div>
			
        <div style="margin-top: 8px;">
        <span class="clickable readybutton">READY</span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="clickable checkbutton">CHECK</span>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="files/FV_FleetChecker.txt" title="details of fleet composition rules" target="_blank">Fleet Checker rules</a>
        </div>
		
			
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
                <span class="takeslot clickable">TAKE SLOT</span>
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
                <span class="valueheader shipclass">Class:</span><span class="value shipclass">ship type class here</span>
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
			<div class="notes"></div> <!-- Marcin Sawicki - here more readable -->
                        <div class="EW" style="display:none;"><!-- Marcin Sawicki - at fleet selection EW window is useless! -->
                            <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                            <div class="EWcontainer">
                                <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                                <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                                <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
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
