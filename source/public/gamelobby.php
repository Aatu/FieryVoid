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

    if (!is_object($gamelobbydata) || $gamelobbydata->status != "LOBBY") {
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
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">          
        <link href="styles/shipwindow.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="client/lib/jquery-ui-1.8.15.custom.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">        
		
		<!-- replaced by php include below
        <script src="static/ships.js"></script>
		-->
<?php		
	//include 'static/ships.php'; //Changed how staticships are loaded to help with HTTP Protocol errors - DK Dec 2025

?>
		
<!--		<script src="client/helper.js"></script>-->
        <script src="client/gamelobby.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/lobbyEnhancements.js"></script>        
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
        <!--<script src="client/model/weapon/dualWeapon.js"></script>-->
        <!--<script src="client/model/weapon/duoWeapon.js"></script>-->
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

        
        jQuery(function($){            
            gamedata.parseServerData(<?php print($gamelobbydataJSON); ?>);
            gamedata.parseFactions(<?php print($factions); ?>);

            $('.readybutton').on("click", gamedata.onReadyClicked);
            $('.savebutton').on("click", gamedata.onSaveClicked)            		
            $('.checkbutton').on("click", gamedata.checkChoices); //fleet correctness check
            $('.leave').on("click", gamedata.onLeaveClicked);
            $('.leaveslot').on("click", gamedata.onLeaveSlotClicked);
            $('.selectslot').on("click", gamedata.onSelectSlotClicked);
            $('.takeslot').on("click", gamedata.clickTakeslot);

            // Start polling for updates
            ajaxInterface.startPollingGamedata();

            // ✅ Unified filter logic for factions based on Tier and Custom
            window.updateTierFilter = function() {   // ✅ Now global
                const selectedTiers = $('.tier-filter:checked').map(function () {
                    return $(this).data('tier');
                }).get();

                const showCustom = $('#toggleCustom').is(':checked');
                const customMode = $('#customSelect').val();

                $('.faction').each(function () {
                    const tier = $(this).data('tier');
                    const isCustom = $(this).data('custom') === true || $(this).data('custom') === "true";

                    let isVisible = false;

                    if (selectedTiers.includes(tier)) {
                        if (showCustom) {
                            if (customMode === 'showOnlyCustom') {
                                isVisible = isCustom;
                            } else {
                                isVisible = true; // show both custom and non-custom
                            }
                        } else {
                            isVisible = !isCustom; // hide custom if toggle unchecked
                        }
                    }

                    $(this).toggle(isVisible);
                });

                // Group headers visibility stays unchanged
                $('.factiongroup-header').each(function () {
                    let header = $(this);
                    let hasVisibleFaction = false;
                    let next = header.next();
                    while (next.length && !next.hasClass('factiongroup-header')) {
                        if (next.hasClass('faction') && next.is(':visible')) {
                            hasVisibleFaction = true;
                            break;
                        }
                        next = next.next();
                    }
                    header.toggle(hasVisibleFaction);
                });
            }

            // ✅ Listen to Tier and Custom Faction checkboxes
            $('.tier-filter').on('change', updateTierFilter);

            /*
            $('#toggleCustom').on('change', function () {
                updateTierFilter();
                gamedata.applyCustomShipFilter();
            });
            */
            $('#toggleCustom').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#customDropdown').show();
                } else {
                    $('#customDropdown').hide();
                }
                updateTierFilter();
                gamedata.applyCustomShipFilter();
            });

            $('#customSelect').on('change', function () {
                updateTierFilter();
                gamedata.applyCustomShipFilter();
            });


            // ✅ Initial call
            updateTierFilter();


            // ✅ Select All / None Tier checkboxes + toggle customs
            $('.tier-select-all').on('click', function () {
                $('.tier-filter').prop('checked', true);
                $('#toggleCustom').prop('checked', true).trigger('change');
                $('#customSelect').val('showCustom'); // ✅ reset custom dropdown to Show Customs                
                $('#isdFilter').val('');
                gamedata.applyCustomShipFilter();
                updateTierFilter();
            });

            $('.tier-select-none').on('click', function () {
                $('.tier-filter').prop('checked', false);
                $('#toggleCustom').prop('checked', false).trigger('change');
                $('#isdFilter').val('');
                gamedata.applyCustomShipFilter();
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
            $("#toggleCustom").trigger("change");
        });


		</script>
	</head>
	<body style="background-image:url(img/maps/<?php print($gamelobbydata->background); ?>)">

  <header class="pageheader">
    <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
    <div class="top-right-row">
      <a href="games.php">Back to Lobby</a>        
      <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
  </header>
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
<main class="container"></main>        
		<div class="panel large lobby">
            <div class="">
                <!--<span class="panelheader">GAME NAME: </span>-->
                <span class="panelsubheader" style="font-size: 24px; color: #e0e7ef;"> <?php print($gamelobbydata->name); ?></span>
            </div>


    <div class="lobbyheader">SCENARIO DESCRIPTION</div>

    <div class="scenario-description">
    <?php
    $desc = $gamelobbydata->description;

    // Replace <br> tags with newlines to normalize input
    $desc = str_replace(['<br>', '<br/>', '<br />'], "\n", $desc);

    // Remove the header line if it exists
    $desc = preg_replace('/^\*{3}.*\*{3}\s*/m', '', $desc);

    // Split into lines
    $lines = preg_split("/\r\n|\n|\r/", trim($desc));

    foreach ($lines as $line) {
        // Trim whitespace for safety
        $line = trim($line);
        if ($line === '') continue; // skip empty lines

        // Try to split on the first colon
        $pos = strpos($line, ':');
        if ($pos !== false) {
            $label = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            // Bold the label regardless of case (you can add uppercase check if you want)
        echo '<span class="scenariolabel">' . htmlspecialchars($label) . ':</span>&nbsp; ' .
            '<span class="scenariovalue">' . htmlspecialchars($value) . '</span><br>';
        } else {
            // Just print line if no colon found
            echo htmlspecialchars($line) . '<br>';
        }
    }
    ?>
    </div>

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
$moonData = [];


if ($gamelobbydata->rules) {
//var_dump($gamelobbydata->rules);    
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
            $moonData = $moonsRule->jsonSerialize();
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
} else { // standard rules
    $optionsUsed .= '';
}

if ($asteroids == true) { // Asteroid terrain rules in play
    $optionsUsed .= ', Asteroids ('. $asteroidsNo . ')';
}
if ($moons == true) { // Moon terrain rules in play

    $small  = $moonData['small']  ?? 0;
    $medium = $moonData['medium'] ?? 0;
    $large  = $moonData['large']  ?? 0;

        function formatMoonCount($count, $type) {
            if ($count <= 0) return null;
            return $count . ' ' . $type;
        }

        // Build each part with pluralization
    $moonParts = array_filter([
        formatMoonCount($small,  'Small'),
        formatMoonCount($medium, 'Medium'),
        formatMoonCount($large,  'Large'),
    ]);

    $optionsUsed .= empty($moonParts)
        ? ', Moons (None)'
        : ', Moons (' . implode(', ', $moonParts) . ')';
}

if ($asteroids == false && $moons == false) { 
    $optionsUsed .= ', No terrain';
}

?>
<div><span class="scenariolabel">OPTIONS SELECTED: </span> <span><?php print($optionsUsed); ?> </span></div>

<div class="lobbyheader" style="margin-bottom: 10px; margin-top: 15px">RULES & INFO</div>

<a href="./factions-tiers.php" target="_blank" style="text-decoration: underline; font-size: 14px; color: #8bcaf2;">Fiery Void: Factions & Tiers</a> 
<span style="font-size: 14px;"> - Overview of Fiery Void factions and their approximate strengths.</span>
<br>
<a href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer" style="text-decoration: underline; font-size: 14px; color: #8bcaf2;">Ammo, Options & Enhancements</a> 
<span style="font-size: 14px;"> - Details of all the extras available to Fiery Void units e.g. Missiles.</span>
<!--<a href="files/enhancements_list.txt" target="_blank" style="text-decoration: underline; font-size: 14px; color: #8bcaf2;">Systems & Enhancements</a> 
<span style="font-size: 14px;"> - Details of common systems and unit enhancements e.g. Boarding Actions / Missiles.</span> -->
<br>

<a href="https://old.wheelofnames.com/fx3-uje" target="_blank" style="color: #8bcaf2; text-decoration: underline; font-size: 14px;">Tier 1</a> 
<strong style="margin: 0 3px; font-size: 16px;">|</strong> 
<a href="https://old.wheelofnames.com/rmq-7ds" target="_blank" style="color: #8bcaf2; text-decoration: underline; font-size: 14px;">Tier 2</a>
<strong style="margin: 0 3px; font-size: 16px;">|</strong> 
<a href="https://old.wheelofnames.com/sgd-5zq" target="_blank" style="color: #8bcaf2;  text-decoration: underline; font-size: 14px;">Tier 3</a>
<span style="margin-left: 3px; margin-right: 3px;">-</span>
<span style="font-size: 14px;">Random Faction Wheels</span> 
<br><br>


	    
            <div class="createsubheader" style = "margin-top: 0px; margin-bottom: 5px; margin-left: 1px;">TEAM 1:</div>
			<div id="team1" class="subpanel slotcontainer">
			</div>
			
            <div class="createsubheader" style = "margin-top: 5px; margin-bottom: 5px; margin-left: 1px;">TEAM 2:</div>
			<div id="team2" class="subpanel slotcontainer">
            </div>
            
            <!--<div class="slot" data-slotid="2" data-playerid=""><span>SLOT 2:</span></div>
			-->
			
            <div class="button-container">
                <span class="btn btn-secondary-lobby leave">Leave Game</span>
            </div>
			
		</div>
<div class="panel large lobby buy" style="display:none;">

    <!-- Header row -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <div>
            <span class="panelheader" style="margin-left: 5px; padding-right: 15px;">PURCHASE YOUR FLEET</span>
        </div>  
        <div>
            <span class="panelsubheader current">0</span>
            <span class="panelsubheader">/</span>
            <span class="panelsubheader max">0</span><span class="panelsubheader">pts</span>
            <span class="panelsmall" style="margin-left: 5px;">(</span>
            <span class="panelsmall remaining">0</span><span class="panelsmall">pts left</span>
            <span class="panelsmall">)</span>
        </div>
    </div>

            <div style="margin-top: 3px; margin-left: 5px; font-size: 12px;">
                <span class="clickable tier-select-all" style="margin-right: 5px; text-decoration: underline; color: #8bcaf2;">All Filters</span>
                <span style="margin-right: 5px; font-size: 16px;  font-weight: bold">|</span>          
                <span class="clickable tier-select-none" style="text-decoration: underline; margin-right: 5px; color: #8bcaf2;">No Filters</span>
                <span style="font-size: 16px;  font-weight: bold">|</span>  

                <label style="margin-left: 5px; margin-top: 3px;">
                    <span style="margin-right: 2px;">Filter by ISD:</span>
                    <input type="text" id="isdFilter" value="" style="width: 36px; height: 14px; text-align: right;">
                    <span class="clickable resetISDFilter" style="text-decoration: underline; margin-left: 3px; font-size: 11px; color: #8bcaf2;">Reset ISD</span>
                </label>
            </div>


    <div style="display:flex; align-items:center; font-size:12px; margin-top:3px; flex-wrap:nowrap;">
        <label style="margin-left:5px;">Tier 1 <input type="checkbox" class="tier-filter" data-tier="Tier 1" checked></label>
        <label style="margin-left:5px;">Tier 2 <input type="checkbox" class="tier-filter" data-tier="Tier 2" checked></label>
        <label style="margin-left:5px;">Tier 3 <input type="checkbox" class="tier-filter" data-tier="Tier 3" checked></label>
        <label style="margin-left:5px;">Ancients <input type="checkbox" class="tier-filter" data-tier="Tier Ancients" checked></label>
        <label style="margin-left:5px;">Other <input type="checkbox" class="tier-filter" data-tier="Tier Other" checked></label>

        <span style="margin-left:6px; margin-right:6px; font-size:16px; font-weight:bold;">|</span>

        <label style="margin-left:5px;">Show Custom<input type="checkbox" id="toggleCustom" class="yellow-tick"></label>
        <span id="customDropdown" style="display:none; margin-left:10px;">
            <select id="customSelect" name="customFilterMode">
                <option value="showCustom">Show Customs</option>
                <option value="showOnlyCustom">Show Only Customs</option>
            </select>
        </span>  


        <div style="display:flex; align-items:center; margin-left:auto; font-size:12px; gap:6px;">
            <label style="margin-left: 5px; margin-top: 0px; display:flex; align-items:center;">
                <span style="margin-right: 2px; font-size: 12px;">Load Fleet by #ID:</span>
                <input type="text" id="fleetIdInput" value="" class="fleetIdInput">
            </label>

            <!-- Custom Saved Fleet Dropdown -->
            <div style="position:relative; margin-left:auto; font-size:12px;">
                <div id="fleetDropdownButton" class="fleet-dropdown-btn">
                    Load a Saved Fleet
                </div>
                <div id="fleetDropdownList" class="fleet-dropdown-list">
                    <!-- populated dynamically -->
                </div>
            </div>
        </div>


    </div>

    <script>
        let cachedFleets = [];
        // References
        const fleetDropdownButton = document.getElementById('fleetDropdownButton');
        const fleetDropdownList = document.getElementById('fleetDropdownList');

        // Initialize cache once on page load
        ajaxInterface.getSavedFleets(function(fleets) {
            cachedFleets = fleets;
            gamedata.populateFleetDropdown();
        });        

        // Toggle dropdown visibility
        fleetDropdownButton.addEventListener('click', () => {
            fleetDropdownList.style.display = fleetDropdownList.style.display === 'block' ? 'none' : 'block';
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', (e) => {
            if (!fleetDropdownButton.contains(e.target) && !fleetDropdownList.contains(e.target)) {
                fleetDropdownList.style.display = 'none';
            }
        });

        const fleetInput = document.getElementById("fleetIdInput");

        // Sanitize input on each keystroke: allow only digits
        fleetInput.addEventListener("input", function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, "");
        });

        // Trigger load on Enter key
        fleetInput.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                const fleetId = this.value.trim();
                if (fleetId !== "" && !isNaN(fleetId)) {
                    gamedata.loadSavedFleetById(parseInt(fleetId, 10));
                } else {
                    console.warn("Please enter a valid numeric Fleet ID.");
                }
            }
        });

    </script>

        <!-- Fleet selection area -->
        <table class="store" style="width:100%; margin-top: 5px;">
            <tr>
                <td style="width:45%;">
                    <div id="store" class="subpanel"></div>
                </td>            
                <td style="width:55%; vertical-align: top;">
                    <div id="fleet" class="subpanel" style="text-align: right;"></div>
                </td>
            </tr>
        </table>

			
        <div style="text-align: right; margin-top: 8px;">
            <a href="files/FV_FleetChecker.txt" title="Details of fleet composition rules" target="_blank">Fleet Checker rules</a>
            &nbsp;            
            <span class="btn btn-primary-lobby checkbutton">CHECK</span>
            &nbsp;&nbsp;
            <span class="btn btn-primary-lobby savebutton">SAVE FLEET</span>
            &nbsp;&nbsp;            
            <span class="btn btn-success-lobby readybutton">READY</span>
        </div>

    </div> <!-- Final closing of the .buy panel -->

        <!-- ✅ Your inserted fleetcheck panel -->
        <div id="fleetcheck" class="panel large lobby" style="display:none;"><p id="fleetchecktxt" style="display:block;"><span></div>

</div>

    <div id="deploymentPreview" class="panel large lobby" style="margin-top:10px;">
        <div class="createsubheader" style="margin-top:5px; text-align: center;"><span>DEPLOYMENT ZONE PREVIEW:</span></div>
        <div id="mapPreviewContainer" style="margin: 0 auto 20px auto; text-align: center;">
            <canvas id="mapPreview" width="420" height="300"></canvas>
        </div>
    </div>

        <div id="globalchat" class="panel large lobby" style="height:200px;">
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
            <div class="leaveslot">Leave Slot</div>
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
                <!---<span>Type:</span>
                <span class ="value deptype"></span> --->
                <span>Width:</span>
                <span class ="value depwidth"></span>
                <span>Height:</span>
                <span class ="value depheight"></span>
                <span>Deploys on Turn:</span>
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
    </main>	
    
<div id="global-blocking-overlay" class="blocking-overlay" style="display:none;">
    <span>
        TRANSMITTING ORDERS...<br>
        <span class="blocking-warning">Do not close window</span>
    </span>

</div>    

<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>


	</body>



</html>
